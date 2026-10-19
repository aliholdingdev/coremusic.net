---
node_id: K014-media/metadata
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
  - "Bilginin kaynağı (dış servis mi yerel mi) → ⚠️ VERIFICATION REQUIRED (§7/8)"
group: mimari
---


# metadata — K014-media (kademe-2 · subdomain)

**ELI15:** Şarkı bilgileri: sanatçı, albüm, süre, tür vb. açıklama verisi.

**Sınır (ne DEĞİL):** Dosya/akış K015/streaming · dış kaynak K013-integraion · şema K007-data.

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
| 2 | Bilginin kaynağı (dış servis mi yerel mi) → ⚠️ VERIFICATION REQUIRED (§7/8) | FM open_questions |
| 3 | Kademe-3/4 genisletmesi | ADR-097 K4 faz (C) bekliyor |

> Bu tablo FM `open_questions` alaniyi index`e gorunur kilir; yeni iddia eklemez.

## §ROL (ELI15)

metadata, K014-media'nın "şarkı bilgileri" çocuğudur: sanatçı, albüm, süre ve tür gibi açıklama verisini tutar. Etiketler dosyanın kendisinden okunur/yazılır: MP3'te ID3v2.3/ID3v2.4, FLAC/Ogg'ta Vorbis Comment, MP4/M4A'ta `ilst` kutuları (`©nam` · `©ART` · `covr`), WAV'ta LIST-INFO. Etiket yoksa dosya adıyla devam eden bir fallback TASARIM İDDİASI'dır. Ses seviyesi normalizasyonu alanları (EBU R128 · ReplayGain 2.0) bu çocuğun verisidir; bu kazancın sinyale uygulanması K003-audio/K004-dsp'in işidir. Dış metadata kaynakları (MusicBrainz · Cover Art Archive · AcoustID) K013-integraion üzerinden konuşulur; kalıcılık K007-data'dadır.

## §KAPSAM

### Kapsam içinde (bu çocuğun malı)

| # | Konu | Kaynak |
|---|---|---|
| 1 | Sanatçı · albüm · süre · tür | master §3/2 · §9.2 |
| 2 | Dosya etiketi okuma/yazma (ID3v2.3/2.4 · Vorbis Comment · ilst · LIST-INFO) | master §22.3 |
| 3 | Etiket normalizasyonu (ID3v2.3 TYER → ID3v2.4 TDRC) | master §22.3 |
| 4 | Loudness alanları: EBU R128 (−23 LUFS) · ReplayGain 2.0 (−18 LUFS) | master §22.3 |
| 5 | Dış metadata kaynağı sorgusu (K013 üzerinden) | master §9.2 · §22.3 |
| 6 | Etiket eksikse fallback (dosya adı) | master §9.2 |
| 7 | Kalıcılık → K007-data | master §9.2 · §2.2 |

### Kapsam dışında (kimin?)

| # | Konu | Ait olduğu düğüm | Kaynak |
|---|---|---|---|
| 1 | Kapak resmi indirme/önbellek | K014-media/artwork | master §3/3 · §9.3 |
| 2 | Dosya/akış taşıması | K014-media/streaming + K010-network | master §2.2 · §9.6 |
| 3 | Dış servisle HTTP konuşması (rate limit taşıma) | K013-integraion | master §2.2 |
| 4 | Kalıcı veri şeması/ORM | K007-data | master §2.2 |
| 5 | Loudness kazancının sinyale uygulanması (DSP) | K003-audio / K004-dsp | master §1 (DSP yapmaz) |
| 6 | Arama indeksi (etiket araması) | K014-media/catalog | master §9.4 |
| 7 | YZ önerileri / kişiselleştirme | K006-yapay-zeka / K021-recomenataion | master §2.2 |
| 8 | Uygulama akışı / arayüz | K011-application | master §2.2 |

## §BİLEŞENLER

| # | Bileşen | Sorumluluk | Etiket |
|---|---|---|---|
| 1 | Etiket okuyucu | ID3v2 · Vorbis Comment · ilst · LIST-INFO çerçeve okuma | TASARIM İDDİASI (master §22.3) |
| 2 | Etiket yazıcı | aynı şemalara yazma | TASARIM İDDİASI (master §22.3) |
| 3 | Normalizatör | TYER→TDRC dahil alan eşlemesi | TASARIM ÖNERİSİ (master §22.3 uyumluluk kırılması) |
| 4 | Loudness alanı | R128 −23 LUFS · ReplayGain −18 LUFS saklama | TASARIM İDDİASI (master §22.3) |
| 5 | Fallback | etiket yoksa dosya adı | TASARIM İDDİASI (master §9.2) |
| 6 | Dış kaynak istemcisi | MusicBrainz · CAA · AcoustID — User-Agent + rate-limit uyumlu | TASARIM ÖNERİSİ (master §22.3) |
| 7 | Kalıcılık arayüzü | metadata'ı K007-data'ya okumak/yazmak | TASARIM İDDİASI (master §9.2) |

## §STANDARTLAR

| # | Standart | Ne işe yarar | Kaynak |
|---|---|---|---|
| 1 | ID3v2.3 vs ID3v2.4 — tarih alanı TYER→TDRC kırması (uyumluluk dikkat) | MP3 etiketi | master §22.3 · §22.7 (ISO/IEC 14496-3 — ID3v2.4/MP4 ilst) |
| 2 | Vorbis Comment | FLAC/Ogg etiketi | master §22.3 |
| 3 | MP4/M4A `ilst` kutuları: `©nam` · `©ART` · `covr` | MP4 etiketi | master §22.3 |
| 4 | LIST-INFO | WAV etiketi | master §22.3 |
| 5 | EBU R128 — −23 LUFS (yayın) | ses seviyesi normalizasyonu | master §22.3 |
| 6 | ReplayGain 2.0 — −18 LUFS | ses seviyesi normalizasyonu | master §22.3 |
| 7 | MusicBrainz API politikası — 1 istek/sn/IP · CC0 çekirdek · User-Agent zorunlu | birincil kimlik | master §22.3 — **TASARIM ÖNERİSİ** |
| 8 | Cover Art Archive — resmi kademeler 250/500/1200 px | kapak kaynağı (mal artwork'ün — §9.3) | master §22.3 · §22.4 |
| 9 | AcoustID — ≤3 istek/sn | parmak izi (fuzzy eşleşme) | master §22.3 — **TASARIM ÖNERİSİ** |
| 10 | Last.fm — sayısal limit | ⚠️ UNKNOWN — VERIFICATION REQUIRED | master §22.3 |
| 11 | Etiket okunacak format seti: MP3 · FLAC · WAV (Opus ileride) | dosya kapsamı | master §22.1 — **TASARIM ÖNERİSİ** |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
