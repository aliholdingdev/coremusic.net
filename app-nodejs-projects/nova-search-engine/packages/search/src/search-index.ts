// ===========================================
// Search Index — ayrı indeks bileşeni (MSA düzeni)
// Prompt kuralı: "Her mesajda tüm veritabanını tarama.
// Önce indexle. Ardından yalnız alakalı max N kayda bak."
// Çekirdek: terim → video ters indeksi; aday seçimi
// indeksten yapılır, pahalı skorlama yalnız adaylara.
// ===========================================

import type { Database } from '@novasearch/database';
import type { Video } from '@novasearch/common';

/** Terimden üretilen basit token (küçük harf + bölmeli). */
function tokenize(text: string): string[] {
  return text
    .toLowerCase()
    .replace(/[^\p{L}\p{N}\s]/gu, ' ')
    .split(/\s+/)
    .filter((t) => t.length > 2);
}

export interface IndexStats {
  videos: number;
  terms: number;
  loaded: boolean;
}

export class SearchIndex {
  /** terim → video kimlikleri (ters indeks) */
  private postings = new Map<string, Set<string>>();
  /** kimlik → video (aday getirme için) */
  private store = new Map<string, Video>();
  private loaded = false;
  private loadedAt = 0;
  /**
   * Tazelik penceresi: dış süreçlerden (refresh/oto-collect) gelen
   * güncellemeler görünür olsun diye indeks periyodik yeniden kurulur.
   */
  private ttlMs = 60_000;

  constructor(private db: Database) {}

  /** İndeksi DB'den kur (TTL dolunca sessizce yenilenir). */
  async ensureLoaded(maxRows = 20_000): Promise<void> {
    if (this.loaded && Date.now() - this.loadedAt < this.ttlMs) return;
    this.postings.clear();
    this.store.clear();
    const rows = await this.db.videos.topViewed(maxRows);
    for (const v of rows) this.addOne(v);
    this.loaded = true;
    this.loadedAt = Date.now();
  }

  /** Yeni toplanan kayıtları indekse artımlı ekle. */
  add(videos: Video[]): void {
    for (const v of videos) this.addOne(v);
    this.loaded = true;
  }

  private addOne(v: Video): void {
    this.store.set(v.youtubeId, v);
    const terms = new Set([
      ...tokenize(v.title),
      ...tokenize(v.description),
      ...tokenize(v.channelName),
      ...(v.tags ?? []).flatMap((t) => tokenize(t)),
    ]);
    for (const term of terms) {
      let postings = this.postings.get(term);
      if (!postings) {
        postings = new Set();
        this.postings.set(term, postings);
      }
      postings.add(v.youtubeId);
    }
  }

  /**
   * Aday seçimi: önce indeks, sonra yalnız alakalı kayıtlar.
   * Skorlama (pahalı iş) çağıran tarafa yalnız bu adaylar gider.
   * `limit` MSA bütçesidir (varsayılan 20 — prompt kuralı).
   */
  async candidates(terms: string[], limit = 20): Promise<Video[]> {
    await this.ensureLoaded();
    const scores = new Map<string, number>();
    for (const term of terms) {
      const exact = this.postings.get(term);
      const matches = exact ?? this.prefixMatch(term);
      for (const id of matches) {
        scores.set(id, (scores.get(id) ?? 0) + 1);
      }
    }
    const ranked = [...scores.entries()]
      .sort((a, b) => {
        // terim eşitliği önce, sonra popülerlik
        if (b[1] !== a[1]) return b[1] - a[1];
        return (this.store.get(b[0])?.viewCount ?? 0) - (this.store.get(a[0])?.viewCount ?? 0);
      })
      .slice(0, Math.max(1, limit));
    return ranked
      .map(([id]) => this.store.get(id))
      .filter((v): v is Video => v !== undefined);
  }

  /** Önek eşleşmesi (kısa terimler için: "tark" → "tarkan"). */
  private prefixMatch(term: string): Set<string> {
    const out = new Set<string>();
    if (term.length < 3) return out;
    for (const [key, ids] of this.postings) {
      if (key.startsWith(term)) {
        for (const id of ids) out.add(id);
        if (out.size > 500) break; // maliyet tavanı
      }
    }
    return out;
  }

  /** İndeksi tazele (refresh sonrası eski kayıtlar güncellenir). */
  update(videos: Video[]): void {
    for (const v of videos) {
      this.store.set(v.youtubeId, v); // eski terim kartları kalır; güncellenen alanlar taze
    }
  }

  stats(): IndexStats {
    return { videos: this.store.size, terms: this.postings.size, loaded: this.loaded };
  }
}
