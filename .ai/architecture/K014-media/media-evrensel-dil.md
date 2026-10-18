---
node_id: K014-media/media-evrensel-dil.md
node_type: subdomain
status: draft
parent_id: K014-media
children: []
depends_on:
  - K003-audio
  - K007-data
  - K013-integraion
evidence:
  - ".ai/architecture/K014-media/media-master.md §3 (11 çocuk), §9.1-§9.11 (bileşen adları), §22 (doğrulanmış standartlar) — okundu 2026-10-18"
  - ".ai/architecture/K014-media/media-kod-ornekleri.md §2-§15 (14 blok — kod adları) — okundu 2026-10-18"
  - ".ai/architecture/K013-integraion/integraion-evrensel-dil.md §2-§3 (K013 sözlüğü — sınır) — okundu 2026-10-18"
validation:
  - "2026-10-18 — 30 terim EN/TR + bağlam; kod örnekleri: media-kod-ornekleri.md §2-§15 · ornekler.md (11 çocuk × 15 dil)"
open_questions:
  - "⚠️ playback/offline/synchronization/media-testing sahipliği (§7/1-§7/4) — terim sahipliği de belirsiz."
  - "⚠️ 'OpenAudio' — tam adres UNKNOWN (§7/7); aday referans, kullanılmaz."
  - "⚠️ 'K015' ibaresi media-master.md'de eski ad olarak duruyor (§1/§9/§18) — silinmedi, bu sözlük güncel ad K014 kullanır."
  - "⚠️ §22.1-§22.5 TASARIM ÖNERİLERİ Vault Steward onayı ister."
group: mimari
---

# K014 MEDIA — Evrensel Dil (Sözlük)

> **Kaynak**: `media-master.md` §3 (11 çocuk) · §9.1-§9.11 · §22 (doğrulanmış standartlar) · `integraion-evrensel-dil.md` (K013 sınırı).
> **Not**: media-master.md §1/§9/§18 kendini "K015" olarak adlandırır (eski ad) — güncel ad **K014-media** (registry §5/15). Bu sözlük güncel adı kullanır.

## §1 Kullanım Kuralları

1. Kodda EN (`Track`, `ContentHash`), arayüzde TR (parça, içerik özeti) kullanılır.
2. Yeni terim: `media-master.md` §3 + bu dosya birlikte güncellenir.
3. Doğrulanmamış iddialar `⚠️` ile işaretli kalır (§4).

## §2 Medya Temel Terimleri (15)

| # | EN Terim | TR Karşılık | Bağlam |
|---|---|---|---|
| 1 | Library | Kitaplık | §9.1 — koleksiyonun kendisi (şarkı/album kümesi) |
| 2 | Metadata | Şarkı Bilgileri | §9.2 — sanatçı/albüm/süre/tür · kaynak ⚠️ UNKNOWN (§7/8) |
| 3 | Artwork | Kapak Resimleri | §9.3 — thumbnail/medium/large (CAA 250/500/1200 px — §22.4) |
| 4 | Catalog | Dizin/Liste Verisi | §9.4 — arama kaynağı olabilir (K012 sınır §7/1) |
| 5 | Playback | Çalma İşlemi | §9.5 — oynat/duraklat/ileri/geri · sahiplik §7/1 belirsiz |
| 6 | Streaming | Akışla Dinleme | §9.6 — taşıma K010 · dış kaynak K013 |
| 7 | Downloads | İndirme | §9.7 — resume/atomik (ADR-026) |
| 8 | Offline | Çevrimdışı Kullanılabilirlik | §9.8 — READY/PENDING/MISSING · ADR-027 |
| 9 | Media Cache | Medya Önbelleği | §9.9 — ARC (scan-resistant, §22.5) |
| 10 | Synchronization | Medya Verisi Eşitlemesi | §9.10 — K013 ile aynı ad §7/3 · yön ⚠️ UNKNOWN (§12/9) |
| 11 | Media Testing | Medya Testleri | §9.11 — K18-testing ile §7/4 belirsiz (master'da: K019-testing) |
| 12 | Track | Parça | media-kod-ornekleri §2.1 (Track record — ULID + hash) |
| 13 | Content Hash | İçerik Özeti (Hash) | §2.1 — SHA-256 · dedup anahtarı |
| 14 | ULID | ULID Kimliği | ADR-092 — kronolojik sortlanabilir tekil kimlik |
| 15 | Golden Fixture | Altın Test Verisi | §11.1 — PCM SHA-256 byte-birebir referans |

## §3 Standart / Teknik Terimler (15 — §22 doğrulanmış)

| # | EN Terim | TR Karşılık | Bağlam |
|---|---|---|---|
| 16 | FLAC | FLAC (Kayıpsız) | RFC 9639 — royalty-free (§22.1) |
| 17 | Opus | Opus (Ağ Codec'i) | RFC 6716 — royalty-free · öneri (§22.1) |
| 18 | MP3 | MP3 | Fraunhofer patentleri 2017 doldu (§22.1) |
| 19 | WAV/PCM | WAV/PCM (Ham Ses) | tescilsiz · ham/kayıpsız (§22.1) |
| 20 | HTTP Range | HTTP Aralık İsteği | RFC 9110 (STD 97) — 206 + `Content-Range` (§22.2) |
| 21 | HLS | HLS (Uyarlı Akış) | RFC 8216 — M3U8 manifest (§22.2) |
| 22 | MPEG-DASH | MPEG-DASH | ISO/IEC 23009-1 — bağımsız standard (§22.2) |
| 23 | ID3v2 | ID3v2 (MP3 Etiketi) | §22.3 — TYER (v2.3) → TDRC (v2.4) kırılması |
| 24 | Vorbis Comment | Vorbis Yorumu | §22.3 — FLAC/Ogg etiket şeması |
| 25 | `ilst` | `ilst` Kutuları | §22.3 — MP4/M4A: `©nam` · `©ART` · `covr` |
| 26 | LIST-INFO | LIST-INFO | §22.3 — WAV etiket şeması |
| 27 | EBU R128 | EBU R128 (Loudness) | §22.3 — −23 LUFS yayın |
| 28 | ReplayGain 2.0 | ReplayGain 2.0 | §22.3 — −18 LUFS |
| 29 | Xing/LAME | Xing/LAME Etiketi | §22.2 — MP3 gapless/seek |
| 30 | SEEKTABLE | SEEKTABLE (FLAC) | §22.2 — FLAC seek tablosu |

## §4 Dış Servis / Referans Terimleri (⚠ işaretli)

| # | EN Terim | TR Karşılık | Durum |
|---|---|---|---|
| 31 | MusicBrainz | MusicBrainz (Katalog) | 1 sn/kullanıcı · User-Agent zorunlu (§22.3 — 2026-10-18 doğrulandı) |
| 32 | Cover Art Archive (CAA) | Cover Art Archive (Kapak Arşivi) | 250/500/1200 px resmi (§22.3-§22.4) |
| 33 | AcoustID | AcoustID (Parmak İzi) | ≤3 istek/sn (§22.3) |
| 34 | Jellyfin | Jellyfin (Medya Sunucusu) | GPL-2.0 · 55732★ (§4/1 — GitHub doğrulandı 2026-10-12) |
| 35 | Navidrome | Navidrome (Web Müzik Sunucusu) | ⚠️ GPL-3.0 · 24109★ (§11 — URL'siz beyan, kanıt bekliyor) |
| 36 | Plexamp / Finamp / Phonograph_Plus / Picard / Strawberry | (referans ürünler) | ⚠️ §11 — URL'siz beyan (S3); açık kaynak kod/fikir incelemesi |
| 37 | Plex / Emby | Plex / Emby | ⚠️ Proprietary (§11.1) — yalnız fikir, kopya yasak · §7/6 lisans kararı |
| 38 | OpenAudio | OpenAudio | ⚠️ UNKNOWN (§7/7) — tam adres yok; aday referans, kullanılmaz |
| 39 | Last.fm | Last.fm | ⚠️ limit UNKNOWN (§22.3 — VERIFICATION REQUIRED) |
| 40 | ARC | ARC (Tarama-Dayanıklı Önbellek) | §22.5 — T1/T2/B1/B2 · katalog/artwork tercihi |

## §5 Eşanlamlı / Kaçınılacak Terimler

| Yanlış / Eski | Doğru | Not |
|---|---|---|
| K015 (media-master.md §1/§9/§18) | **K014-media** | Eski ad — güncel registry §5/15: K014-media |
| K019-testing / K018-devops / K017-observability | K18-testing / K17-devops / K016-observability | Eski adlar — yeni atıf yasak |
| K0100-identitiy / K020-licence / K020-comilance | K009-security / K019-comilance / K020-discovery | Eski adlar — yeni atıf yasak |
| WMA / AAC (temel sette) | MP3 + FLAC + WAV | §22.1: WMA proprietary ❌ · AAC lisans maliyeti ⚠️ — Vault Steward onayı ister |
| offline-sync (K013'teki synchronization ile karıştırma) | synchronization (K014) vs synchronization (K013) | §7/3: iki domain'de aynı ad — çelişki çözülmedi ⚠️ |
| `media-master.md` "K015 media-service" | K012 playback-service / K014 playback | §7/1: sahiplik belirsiz ⚠️ |

## §6 Bağlantılı Dosyalar

| Dosya | İçerik | Ad (değiştirilemez) |
|---|---|---|
| `.ai/architecture/K014-media/media-master.md` | §3 (11 çocuk) + §22 (standartlar) — SSOT | media-master |
| `.ai/architecture/K014-media/media-kod-ornekleri.md` | Kod söylemleri (14 blok · 13 dil) | media-kod-ornekleri |
| `.ai/architecture/K013-integraion/integraion-evrensel-dil.md` | K013 sözlüğü (sınır) | integraion-evrensel-dil |
| `.ai/architecture/K014-media/media-bilesenler.md` | 11 bileşen (§9.x) — terim bağlamları | media-bilesenler |
| `.ai/architecture/K014-media/media-kararlar.md` | ADR-092/026/027/028 + TASARIM ÖNERİLERİ | media-kararlar |
| `.ai/architecture/K014-media/media-test-stratejisi.md` | 8 test senaryosu (§13) | media-test-stratejisi |
| `.ai/architecture/K014-media/media-web-arastirma.md` | Doğrulanmış RFC/ISO kaynakları | media-web-arastirma |

## §7 Terim Kullanım Kuralları

1. **Yeni kod yorumu/belge** yalnız §2-§3 sütunundaki EN↔TR çiftini kullanır; eşanlamlı
   (§5) kullanıldığında düzeltme açılır.
2. **⚠️ satırları** (§4) doğrulanana kadar yalnızca "aday referans" olarak anılır;
   kod içine sabitlenmez (OpenAudio adresi, Last.fm limiti — §7/7/§22.3).
3. **Eski adlar** (§5) yeni atıf olarak kullanılmaz; bulunursa diff'te reddedilir.
   Sözlük kendisi eski adları yalnızca "yanlış → doğru" eşlemesi olarak barındırır.
4. **Kapsam dışı terimler** (stereo, latency, codec kod adları) bu sözlükte tanımlanmaz —
   ilgili K sözlüğüne (services-evrensel-dil, integraion-evrensel-dil) başvurulur.
