---
node_id: K014-media/artwork
node_type: subdomain
status: draft
parent_id: K014-media
children: []
depends_on: []
evidence:
  - "v2 §2.15 — çocuk adı kaynağı (birebir)"
validation:
  - "İsim v2 §2.15 ile birebir karşılaştırıldı — 2026-10-12"
open_questions:
  - "Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C)"
  - "Boyut/format politikası → ⚠️ UNKNOWN"
group: mimari
---


# artwork — K014-media (kademe-2 · subdomain)

**ELI15:** Kapak resimleri: getirme, boyutlandırma ve önbelleğe alma (iddia).

**Sınır (ne DEĞİL):** Önbellek politikası media-cache · dış kaynak K013 · gösterim K011.

**Bağımlılık (özet):** Düğümün tamamı üst kademeye bağlıdır; ayrıntı ve etiketler media-bagimliliklar.md §1'dedir. Bu dosyadaki satırlar TASARIM İDDİASI'dır.

**Durum:** draft · kademe-3 (bounded-context) ve kademe-4 genişletmesi ADR-097 K4 fazı (C) bekliyor · module'e inilmez.


## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |

## §A Kapilar (bu index)

| # | Kapilar | Durum |
|---|---|---|
| 1 | Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C) | FM open_questions |
| 2 | Boyut/format politikası → ⚠️ UNKNOWN | FM open_questions |
| 3 | Kademe-3/4 genisletmesi | ADR-097 K4 faz (C) bekliyor |

> Bu tablo FM `open_questions` alaniyi index`e gorunur kilir; yeni iddia eklemez.

---

## §ROL (ELI15)

Kapak resimleri (artwork) çocuğu, şarkı/album kapaklarını **getirir, boyutlandırır ve önbelleğe alır** (v2 §2.15 · master §9.3). Birincil dış kaynak **Cover Art Archive (CAA)**'dır; resmi kademe boyutları **250 / 500 / 1200 px**'dir (master §22.3 · §22.4). Çocuk üç kademe varyant üretir: thumbnail (~64–128 px) · medium (~250–500 px) · large (~640–1200 px) (master §22.4 — TASARIM ÖNERİSİ). Sunum önbelleği `Cache-Control: public, max-age=31536000, immutable` + **ETag** (içerik hash) ile yapılır (master §22.5). Kapak yoksa/ayrılamazsa **placeholder** gösterilir (master §9.3 — TASARIM İDDİASI).

## §KAPSAM

### Kapsam içinde (bu çocuğun malı)

| # | Konu | Dayanak |
|---|---|---|
| 1 | Kapak resmi getirme — CAA resmi kademeleri 250 / 500 / 1200 px | master §22.3 · §22.4 |
| 2 | Boyut varyantı üretimi — thumbnail / medium / large | master §22.4 (TASARIM ÖNERİSİ) |
| 3 | Gömülü (embedded) kapak çıkarma — MP3 ID3v2 · FLAC/Ogg Vorbis Comment · MP4/M4A `covr` · WAV LIST-INFO | master §22.3 |
| 4 | Sunum önbelleği — `immutable` + ETag (içerik hash) | master §22.5 |
| 5 | Kapak yoksa placeholder (TASARIM İDDİASI) | master §9.3 |
| 6 | Dış kaynağa uyum — MusicBrainz/CAA rate-limit + zorunlu User-Agent | master §22.3 |

### Kapsam dışında (kimin?)

| # | Konu | Ait olduğu düğüm | Dayanak |
|---|---|---|---|
| 1 | Önbellek boyut/TTL politikası ve eşikleri | K014-media/media-cache (eşik: Vault Steward §16/3) | master §9.9 · §16/3 |
| 2 | Dış servis erişimi (MusicBrainz/CAA taşıması) | K013-integraion | master §2.2 |
| 3 | Ağ taşımasının ayrıntısı | K010-network | master §2.2 |
| 4 | Arayüzde kapak gösterimi | K011-application | master §9.3 · index §A sınır notu |
| 5 | Kalıcı depolama şeması | K007-data | master §2.2 |
| 6 | Kapak hangi albumün? (album/parça kimliği) | K014-media/metadata | master §9.2 |
| 7 | Ses seviyesi normalizasyonu (EBU R128 · ReplayGain 2.0) | K014-media/metadata (etiket alanı) | master §22.3 |

## §BİLEŞENLER

> Bu tablodaki bileşen adları **TASARIM İDDİASI**'dır (kademe-2 design node — repo'da henüz kodu YOK); sorumluluk satırları master §9.3 + §22'e dayanır.

| # | Bileşen | Sorumluluk | Dayanak |
|---|---|---|---|
| 1 | Cover Fetcher | CAA'dan resmi kademe (250/500/1200 px) getirir; MusicBrainz politikası (1 istek/sn/IP + zorunlu User-Agent) uyumu | master §22.3 · §22.4 |
| 2 | Variant Generator | thumbnail (~128) · medium (250/500) · large (1200) üçlü varyantı üretir | master §22.4 (TASARIM ÖNERİSİ) |
| 3 | Embedded Art Extractor | dosya içi gömülü kapağı etiket şemasına göre çıkarır (ID3v2 · Vorbis Comment · `covr`) | master §22.3 |
| 4 | Artwork Cache (HTTP) | `Cache-Control: public, max-age=31536000, immutable` + ETag (içerik hash) ile 304 yeniden doğrulama | master §22.5 |
| 5 | Placeholder Fallback | kapak yoksa/ayrılamazsa placeholder döner | master §9.3 (TASARIM İDDİASI) |

## §STANDARTLAR

> Bu bölüm yalnız `media-master.md` §22'de **doğrulanmış** standartları taşır (zero-hallucination); öneri satırları **TASARIM ÖNERİSİ** etiketlidir ve Vault Steward onayı ister.

| # | Standart / kural | Ne söyler | Dayanak |
|---|---|---|---|
| 1 | Cover Art Archive kademeleri | resmi boyutlar **250 / 500 / 1200 px**; görsel lisansı ayrıdır | master §22.3 · §22.4 |
| 2 | ID3v2.3 vs ID3v2.4 (MP3) | tarih alanı **TYER→TDRC kırılması** — uyumluluk dikkati; gömülü kapak bu şemadadır | master §22.3 (şema: ISO/IEC 14496-3 — §22.7) |
| 3 | Vorbis Comment (FLAC/Ogg) | FLAC/Ogg etiket şeması (FLAC standardı RFC 9639) | master §22.3 · §22.1 · §22.7 |
| 4 | MP4/M4A `ilst` kutuları | `©nam` · `©ART` · **`covr`** (kapak kutusu) | master §22.3 |
| 5 | LIST-INFO (WAV) | WAV etiket şeması | master §22.3 |
| 6 | HTTP cache başlıkları | `Cache-Control: public, max-age=31536000, immutable` + **ETag** (içerik hash) | master §22.5 |
| 7 | MusicBrainz API politikası | **1 istek/sn/IP** · **User-Agent zorunlu** · CC0 çekirdek — CAA'nın kimlik kaynağı | master §22.3 |

**TASARIM ÖNERİSİ (Vault Steward onayı ister):** üç kademe varyant üret + `immutable`/ETag cache ile sun (master §22.4 · §22.5); eşikler §16/3 kararıdır.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
