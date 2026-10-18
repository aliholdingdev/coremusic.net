---
node_id: K014-media/media-kararlar.md
node_type: subdomain
status: draft
parent_id: K014-media
children: []
depends_on:
  - K007-data
  - K010-network
  - K013-integraion
evidence:
  - ".ai/.decisions/index.md ADR-026 · ADR-027 · ADR-028 · ADR-092 (accepted) · ADR-097 · ADR-103 (draft) — okundu 2026-10-18"
  - ".ai/architecture/K014-media/media-master.md §22.1-§22.5 (TASARIM ÖNERİLERİ), §7 (açık sorular), §16 (bilinmeyen envanteri) — okundu 2026-10-18"
  - ".ai/architecture/K013-integraion/integraion-web-arastirma.md §3.8 (MusicBrainz rate limit — 200 doğrulandı) — okundu 2026-10-18"
validation:
  - "2026-10-18 — 6 gerçek ADR (4 accepted + 2 draft) + 5 TASARIM ÖNERİSİ + 4 açık konu; uydurma ADR numarası YOK; format Durum/Bağlam/Karar/Alternatifler/Sonuçlar"
open_questions:
  - "⚠️ ADR-097 (K-Space V3) DRAFT — onay bekliyor."
  - "⚠️ ADR-103 (medya-sağlayıcı auth gate) DRAFT — K020-discovery tur-2 devri (2026-10-17)."
  - "⚠️ §22.1-§22.5 TASARIM ÖNERİLERİ Vault Steward onayı ister — henüz karar DEĞİL."
  - "⚠️ playback/offline/synchronization/media-testing sahipliği (§7/1-§7/4) — ADR YOK."
group: mimari
---

# K014 MEDIA — Kararlar

> **Kaynaklar**: `.ai/.decisions/index.md` (ADR numaraları — uydurma YOK) · `media-master.md` §22 (TASARIM ÖNERİLERİ) · §7 (açık sorular) · `integraion-web-arastirma.md` §3.8.
> Format (zorunlu): Durum / Bağlam / Karar / Alternatifler / Sonuçlar.

## §1 ADR-092 — Medya Arşivi Dizin Ekseni ve ULID Kimliği (accepted)

- **Durum**: accepted · Infrastructure
- **Bağlam**: Medya arşivi dosya/dizin yapısı ve dosya kimliği belirsiz (koleksiyon büyüdükçe).
- **Karar**: Medya arşivi dizin ekseni + **ULID** kimliği (kronolojik,-sortlanabilir tekil kimlik).
- **Alternatifler**: UUID v4 — sırasız (dizin ekseni kırılır); timestamp adlandırma — tekillik yok.
- **Sonuçlar**: Koleksiyon fiziksel dizinleme ULID ile; library/downloads/offline bileşenleri bu kimliği kullanır.

## §2 ADR-026 — Download Service Architecture (accepted)

- **Durum**: accepted · Architecture
- **Bağlam**: `K014/downloads` (§9.7) — indirme servisi mimarisi.
- **Karar**: Download Service Architecture — indirme servisi ayrı mimari (resume · atomic rename · checksum).
- **Alternatifler**: Uygulama içi basit dosya kopyası — REDDEDİLDİ (resume/checksum yok).
- **Sonuçlar**: `downloads` bileşeni bu mimariye uyar (§9.7: `Range: bytes=N-` resume · `.part` + atomik rename · SHA-256/ETag — ornekler.md).

## §3 ADR-027 — Dual-Mode Storage Strategy (accepted)

- **Durum**: accepted · Infrastructure
- **Bağlam**: `K014/offline` (§9.8) — çevrimdışı depolama stratejisi.
- **Karar**: Dual-Mode Storage — iki mod (çevrimiçi/çevrimdışı) depolama stratejisi.
- **Alternatifler**: Tek mod (her şey yerel) — REDDEDİLDİ (alan); tek mod (her şey bulut) — REDDEDİLDİ (offline yok).
- **Sonuçlar**: offline + downloads + media-cache bu stratejiye uyar.

## §4 ADR-028 — Anti-Ban System (accepted)

- **Durum**: accepted · Download
- **Bağlam**: Dış servislerden indirme yaparken ban riski (rate limit ihlali).
- **Karar**: Anti-Ban System — rate limit uyumu + backoff + User-Agent politikası (MusicBrainz 1 sn/kullanıcı — `integraion-web-arastirma.md` §3.8 doğrulandı).
- **Alternatifler**: Sınırsız istek — REDDEDİLDİ (ban); sabit aralık — REDDEDİLDİ (tespit kolay).
- **Sonuçlar**: downloads/metadata/artwork dış servis istekleri anti-ban ile sarılır.

## §5 ADR-103 — Medya-Sağlayıcı Auth Gate (⚠️ DRAFT)

- **Durum**: **⚠️ draft** (2026-10-17 — K020-discovery tur-2 keşfinden devredildi).
- **Bağlam**: Harici medya-sağlayıcı servisleri (Jellyfin/Plex/Navidrome — K013 §5.1-§5.3) için kimlik şeması kapısı.
- **Karar (taslak)**: Medya-sağlayıcı auth gate — harici servis kimlik şeması kapısı + üretim entegrasyonu ön koşulları.
- **Alternatifler**: Her sağlayıcı kendi auth'u — uyumsuzluk; düz API key — güvenlik riski (K009).
- **Sonuçlar**: **DRAFT — kabul edilene kadar uygulanmaz**. Kanıt: `K020-discovery/discovery-web-arastirma.md` (12 servis · 18 URL · 0 hata).

## §6 ADR-097 — K-Space V3 (⚠️ DRAFT)

- **Durum**: **⚠️ draft** (2026-10-12 — onay bekliyor).
- **Bağlam**: K-Space V3 — sıfırdan kurulum, çoklu-MD şema, isimlendirme, Band-2..7 rezervasyonu (media-master §1: "ADR-097 K2/K3").
- **Karar (taslak)**: Medya mimarisi K-Space V3 şemasına uyar; rol sözlüğü (§1) bu ADR'den.
- **Sonuçlar**: media-master.md §1 bu ADR'ye dayanır. **DRAFT — onay bekliyor.**

## §7 TASARIM ÖNERİLERİ (§22.1-§22.5 — Vault Steward Onayı İster)

> Bunlar **karar değildir** — §22 web araştırmasıyla desteklenmiş TASARIM ÖNERİLERİ; nihai onay Vault Steward'a ait.

| # | Öneri | Kaynak | Durum |
|---|---|---|---|
| 1 | **Codec seti** (§22.1): temel = MP3 + FLAC + WAV (tescil riski taşımaz); Opus ileride; AAC/WMA kaçınılır | RFC 9639 · RFC 6716 · patent durumları (2026-10-17) | ⏳ Vault Steward onayı |
| 2 | **Akış protokolü** (§22.2): yerel dosya → HTTP Range (RFC 9110); adaptif → HLS (RFC 8216) veya DASH (ISO/IEC 23009-1) yalnız canlı/uzun içerikte | RFC 9110/8216 · ISO (2026-10-17) | ⏳ Vault Steward onayı |
| 3 | **Metadata kaynağı** (§22.3): MusicBrainz birincil kimlik (1 sn · User-Agent) + CAA kapak (250/500/1200 px) + AcoustID fuzzy (≤3/sn) | MusicBrainz/CAA/AcoustID (2026-10-17) | ⏳ Vault Steward onayı |
| 4 | **Artwork varyantları** (§22.4): üç kademe (thumbnail ~64-128 · medium ~250-500 · large ~640-1200 px) + `immutable` cache | CAA kademeleri (2026-10-17) | ⏳ Vault Steward onayı |
| 5 | **media-cache stratejisi** (§22.5): katalog/artwork → ARC (scan-resistant) + `immutable`/ETag; parça TTL politikası §16/3 kararı | §22.5 (2026-10-17) | ⏳ Vault Steward eşik onayı |

### §8.1 Karar → Bileşen Etkisi

| Karar | Etkilenen bileşen (media-bilesenler.md) | Etki |
|---|---|---|
| ADR-092 (ULID) | library · downloads · offline | track_ulid = ULID (§9.1/§9.7/§9.8) |
| ADR-026 (Download Service) | downloads | resume · atomik rename · checksum (§9.7) |
| ADR-027 (Dual-Mode Storage) | offline · media-cache | READY/PENDING/MISSING + eviction (§9.8-§9.9) |
| ADR-028 (Anti-Ban) | metadata · artwork · downloads | rate limit + backoff + User-Agent (§9.2-§9.3, §9.7) |
| §22.1 (codec önerisi) | playback · streaming | MP3+FLAC+WAV temel (§9.5-§9.6) |
| §22.2 (Range/HLS önerisi) | playback · streaming | RFC 9110 Range · HLS/DASH (§9.5-§9.6) |
| §22.3 (MusicBrainz+CAA+AcoustID) | metadata · artwork | birincil kimlik + kapak + fuzzy (§9.2-§9.3) |
| §22.5 (ARC önerisi) | media-cache · catalog | scan-resistant + immutable/ETag (§9.4, §9.9) |

## §9 Açık Konular (§7 + §16 — henüz ADR yok)

| # | Konu | Durum | Not |
|---|---|---|---|
| 1 | playback sahibi (K014 vs K012/playback-service) | **KARAR YOK** | §7/1 — Vault Steward kararı |
| 2 | offline sahibi (K014/offline-sync vs K012/offline kuyruğu) | **KARAR YOK** | §7/2 |
| 3 | synchronization adı iki domain'de (K013 + K014) | **ÇELİŞKİ** | §7/3 — ne ayırıyor? |
| 4 | media-testing sahipliği (K014 vs K18-testing — master'da: K019-testing) | **KARAR YOK** | §7/4 |
| 5 | Referans ürünler neyi kanıtlar (Plexamp/Finamp/Navidrome/Jellyfin) | **Onay bekliyor** | §7/5 |
| 6 | Plex/Emby proprietary lisans kararı | **VERIFICATION REQUIRED** | §7/6 |
| 7 | OpenAudio deposu tam adresi | **UNKNOWN** | §7/7 (~2800★) |
| 8 | metadata/artwork kaynağı (dış servis vs yerel) | **VERIFICATION REQUIRED** | §7/8 |
| 9 | synchronization yönü (çift/tek) | **UNKNOWN** | §12/9 — Vault Steward |

## §10 Bu Dosyanın Kullanım Sırası

1. **Önce §1-§8** (kabul edilmiş ADR'ler) okunur — bunlar bağlayıcıdır; içeriklerindeki
   Durum satırı (`Accepted` / `Draft`) diskteki ADR kaydıyla eşleşir.
2. **§8 TASARIM ÖNERİLERİ** yalnız öneridir; Vault Steward onayı olmadan kod-ornekleri'ndeki
   karşılıkları üretime alınmaz (media-kod-ornekleri §13 "TASARIM ÖNERİSİ" etiketi).
3. **§9 açık konular** çözülene kadar media-riskler §5 tablosunda "karar bekliyor" kalır;
   kapanış kanıtı bu dosyaya §9'a tarih + karar satırı olarak eklenir.
4. Çelişki halinde otorite sırası: `.ai/CLAUDE.md` §2.1 → bu dosya → media-master.md §22.
