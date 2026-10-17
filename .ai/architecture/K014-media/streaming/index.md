---
node_id: K014-media/streaming
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
  - "Akış biçimi/protokolü → ⚠️ UNKNOWN (taşıma K014'te)"
group: mimari
---


# streaming — K014-media (kademe-2 · subdomain)

**ELI15:** Akışla dinleme: kaynağından parça parça okuyup çalma.

**Sınır (ne DEĞİL):** Taşıma K010-network · dış kaynak K013-integraion · çalma K015/playback.

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
| 2 | Akış biçimi/protokolü → ⚠️ UNKNOWN (taşıma K014'te) | FM open_questions |
| 3 | Kademe-3/4 genisletmesi | ADR-097 K4 faz (C) bekliyor |

> Bu tablo FM `open_questions` alaniyi index`e gorunur kilir; yeni iddia eklemez.

---

## §ROL (ELI15)

Streaming, parçanın **kaynağından parça parça okunup çalınması** işidir: ağ üzerinden alınan veriyi oynatıcıya aktarır. Bu çocuk "neyin nereden geleceği"ni (dış kaynak) ve "verinin nasıl taşınacağını" (taşıma) kendisi YAPMAZ — dış kaynak K013-integraion, taşıma K010-network'dür (master §9.6 · §2.2). Çocuğun işi akış oturumudur: aralık (Range) istekleri, parçalı içerik yanıtları (206), manifest/segment düzeni ve bağlantı kopması durumunda offline fallback (master §9.6 — TASARIM İDDİASI). Protokol temeli master §22.2'de doğrulanmıştır: HTTP Range (RFC 9110/STD 97) · HLS (RFC 8216) · DASH (ISO/IEC 23009-1:2026).

## §KAPSAM

### Kapsam içinde (bu çocuğun malı)

| # | Konu | Kaynak |
|---|---|---|
| 1 | Akışla dinleme (akış oturumu yaşam döngüsü) | master §9.6 · v2 §2.15 (S2) |
| 2 | HTTP `Range` istekleri ile parçalı okuma (seek/ileri sarma) | master §22.2 (RFC 9110 §14 — doğrulanmış) |
| 3 | Manifest üretimi (M3U8-style playlist) + HLS/DASH segment düzeni | master §22.2 (RFC 8216 · ISO/IEC 23009-1 — doğrulanmış) |
| 4 | HTTP cache başlıkları: `ETag` (içerik hash) + `immutable` | master §22.5 (doğrulanmış) |
| 5 | Bağlantı kopması → offline fallback | master §9.6 (TASARIM İDDİASI) |
| 6 | İçerik biçimi kapısı: MP3/FLAC/WAV temel; Opus öneri | master §22.1 (TASARIM ÖNERİSİ) |

### Kapsam dışında (kimin?)

| # | Konu | Ait olduğu düğüm | Kaynak |
|---|---|---|---|
| 1 | Ağ taşıması ayrıntısı (bağlantı, paket, retry) | K010-network | master §2.2 · §9.6 · §14.2 |
| 2 | Dış kaynak/katalog konuşması | K013-integraion | master §2.2 · §9.6 · §14.2 |
| 3 | Çalma durumu/konumu | K014-media/playback | master §9.6 (Sınır) |
| 4 | İndirme/çevrimdışı saklama | K014-media/downloads · K014-media/offline | master §9.7 · §9.8 |
| 5 | Önbellek boyut/TTL politikası eşikleri | K014-media/media-cache (Vault Steward §16/3) | master §22.5 |
| 6 | Arayüz / uygulama akışı | K011-application | master §2.2 |

## §BİLEŞENLER

| # | Bileşen | Sorumluluk | Etiket |
|---|---|---|---|
| 1 | Range istek oluşturucu | `Range: bytes=a-b` başlığı ile parçalı okuma talebi | doğrulanmış (RFC 9110 · master §22.2) |
| 2 | Partial-content işleyici | 206 Partial Content + `Content-Range` yanıtı | doğrulanmış (RFC 9110 · master §22.2) |
| 3 | Manifest üretici | M3U8-style playlist + HLS segment listesi | doğrulanmış (RFC 8216 · master §22.2) |
| 4 | DASH adaptasyon notu | ISO/IEC 23009-1 byte-range segment düzeni (yalnız canlı/uzun içerik) | TASARIM ÖNERİSİ (master §22.2) |
| 5 | Cache başlığı katmanı | `ETag` (içerik hash) + `Cache-Control: public, max-age=31536000, immutable` | doğrulanmış (master §22.5) |
| 6 | Offline fallback | bağlantı kopunca yerel/offline kaynağa düşme | TASARIM İDDİASI (master §9.6) |

## §STANDARTLAR

| # | Standart/fark | Ne | Kaynak | Etiket |
|---|---|---|---|---|
| 1 | HTTP Progressive / Range | `Range` istekleri — parça/seek için temel | master §22.2 (RFC 9110 / STD 97) | ✅ doğrulanmış |
| 2 | HLS | adaptif akış — Apple/CDN uyumu | master §22.2 (RFC 8216 — Informational) | ✅ doğrulanmış |
| 3 | MPEG-DASH | bağımsız adaptif akış standardı | master §22.2 (ISO/IEC 23009-1:2026) | ✅ doğrulanmış |
| 4 | WebRTC | gerçek zamanlı senaryoya özel | master §22.2 (RFC 8825) | ⚠️ senaryo özel |
| 5 | Cache başlıkları | `Cache-Control: public, max-age=31536000, immutable` + `ETag` (içerik hash) | master §22.5 | ✅ doğrulanmış |
| 6 | FLAC | RFC 9639 — kaynak içerik biçimlerinden biri | master §22.1 | ✅ doğrulanmış |
| 7 | Opus | RFC 6716 — ağ/düşük gecikme için öneri codec | master §22.1 | ✅ doğrulanmış |

### §STANDARTLAR — TASARIM ÖNERİLERİ (Vault Steward onayı ister)

| # | Öneri | Gerekçe | Kaynak |
|---|---|---|---|
| 1 | Yerel dosya → HTTP Range (RFC 9110); adaptif akış → HLS veya DASH (yalnız canlı/uzun içerik) | en basit yol önce | master §22.2 TASARIM ÖNERİSİ |
| 2 | Katalog/artwork önbelleği → ARC (scan-resistant) + `immutable`/ETag | tarama ile kirletilmez | master §22.5 TASARIM ÖNERİSİ |
| 3 | M3U8 manifest formatı | RFC 8216 kanıtlı HLS yapısı | master §22.2 |
| 4 | Parça TTL/boyut eşikleri | Vault Steward §16/3 kararı bekliyor | master §22.5 · §16/3 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
