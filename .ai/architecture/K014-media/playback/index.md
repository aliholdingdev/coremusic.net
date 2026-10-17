---
node_id: K014-media/playback
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
  - "Sahiplik: K015 mi K012 mi → ⚠️ VERIFICATION REQUIRED (§7/1)"
group: mimari
---


# playback — K014-media (kademe-2 · subdomain)

**ELI15:** Dosyanın/sinyalin çalınması: okuma, konum, oynatma durumu.

**Sınır (ne DEĞİL):** Ses işleme K003-audio · durum orkestrasyonu K012/playback-service ile çakışma riski.

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
| 2 | Sahiplik: K015 mi K012 mi → ⚠️ VERIFICATION REQUIRED (§7/1) | FM open_questions |
| 3 | Kademe-3/4 genisletmesi | ADR-097 K4 faz (C) bekliyor |

> Bu tablo FM `open_questions` alaniyi index`e gorunur kilir; yeni iddia eklemez.

---

## §ROL (ELI15)

Playback, bir parçanın **dosyanın/sinyalin çalınması** işidir: okuma, konum ve oynatma durumu. Kullanıcı "çal" dediğinde parçanın yerini bulur, durumunu (oynat/duraklat/ileri/geri) yönetir ve saniye cinsinden konumu takip eder (master §9.5 — v2 §2.15 S2 + TASARIM İDDİASI). Sesin **işlenmesi** bu çocuğun işi DEĞİLDİR: decode/çalma motoru K003-audio'dan alınır (master §2.2 · §14.1). Sahiplik sınırı belirsizdir: durum orkestrasyonu K012/playback-service ile çakışma riski taşır (master §7/1 → ⚠️ VERIFICATION REQUIRED). Aşağıdaki tüm satırlar TASARIM İDDİASI'dır; codec/seek temeli master §22'de doğrulanmıştır.

## §KAPSAM

### Kapsam içinde (bu çocuğun malı)

| # | Konu | Kaynak |
|---|---|---|
| 1 | Çalma durumu: play / pause / ileri / geri | master §9.5 (TASARIM İDDİASI) |
| 2 | Pozisyon takibi (saniye) | master §9.5 (TASARIM İDDİASI) |
| 3 | Dosyanın/sinyalin çalınması (okuma) | master §9.5 · v2 §2.15 (S2) |
| 4 | Gapless/seek: Xing/LAME tag (MP3) + FLAC `SEEKTABLE` | master §22.2 (doğrulanmış) |
| 5 | Codec/format kapısı: MP3 + FLAC + WAV (temel set) | master §22.1 (TASARIM ÖNERİSİ) |
| 6 | Durum kalıcılığı (pozisyon kaydı) — şema K007'de | master §9.5 · §2.2 |

### Kapsam dışında (kimin?)

| # | Konu | Ait olduğu düğüm | Kaynak |
|---|---|---|---|
| 1 | Ses sinyali işleme, DSP, mixer | K003-audio / K004-dsp | master §2.2 |
| 2 | Çalma durumu/orkestrasyonu (servis) | K012-services (playback-service) | master §7/1 → ⚠️ §A/2 |
| 3 | Kalıcı veri şeması/ORM | K007-data | master §2.2 |
| 4 | Ağ taşıması ayrıntı (akışta seek) | K010-network | master §2.2 · §9.6 |
| 5 | Uygulama akışı / arayüz | K011-application | master §2.2 · §14.1 |
| 6 | Dış katalog/akış kaynağı | K013-integraion | master §2.2 |

## §BİLEŞENLER

| # | Bileşen | Sorumluluk | Etiket |
|---|---|---|---|
| 1 | Player state machine | IDLE → PLAYING → PAUSED → SEEKING → ENDED geçişleri | TASARIM ÖNERİSİ (bkz. `ornekler.md`) |
| 2 | Position tracker | saniye cinsinden konum takibi + K007'ye kalıcı kayıt | TASARIM İDDİASI (master §9.5) |
| 3 | Seek çözümleyici | MP3'te Xing/LAME tag, FLAC'ta `SEEKTABLE` ile byte offset | doğrulanmış (master §22.2) |
| 4 | Codec/format kapısı | MP3 · FLAC · WAV okuma izni | TASARIM ÖNERİSİ (master §22.1) |
| 5 | Playlist ilerletici | parça bitince sıradaki parçaya geçiş | TASARIM ÖNERİSİ |
| 6 | Ses motoru kapısı | K003-audio decode/çalma çağrısı | TASARIM İDDİASI (master §2.2 · §14.1) |

## §STANDARTLAR

| # | Standart/fark | Ne | Kaynak | Etiket |
|---|---|---|---|---|
| 1 | MP3 | Fraunhofer patentleri 2017'de doldu — royalty-free | master §22.1 | ✅ doğrulanmış |
| 2 | FLAC | RFC 9639 · royalty-free · Xiph.org BSD | master §22.1 | ✅ doğrulanmış |
| 3 | WAV/PCM | Ham PCM, tescilsiz | master §22.1 | ✅ doğrulanmış |
| 4 | Gapless/seek | Xing/LAME tag (MP3) + FLAC `SEEKTABLE` + HTTP `Range` (RFC 9110 §14) | master §22.2 | ✅ doğrulanmış |
| 5 | ID3v2.3 vs v2.4 | tarih alanı TYER→TDRC kırması — süre/etiket okuma uyumu | master §22.3 | ✅ doğrulanmış |
| 6 | Vorbis Comment | FLAC/Ogg etiket şeması (süre bilgisi kaynağı) | master §22.3 | ✅ doğrulanmış |
| 7 | LIST-INFO | WAV etiket şeması | master §22.3 | ✅ doğrulanmış |
| 8 | EBU R128 (−23 LUFS) / ReplayGain 2.0 (−18 LUFS) | seviye normalizasyonu — UYGULAMA K003/K004'te | master §22.3 | ✅ doğrulanmış (sınır: iş K003/K004) |

### §STANDARTLAR — TASARIM ÖNERİLERİ (Vault Steward onayı ister)

| # | Öneri | Gerekçe | Kaynak |
|---|---|---|---|
| 1 | Temel codec set = **MP3 + FLAC + WAV** | üçü de tescil riski taşımaz | master §22.1 TASARIM ÖNERİSİ |
| 2 | Player state machine = IDLE · PLAYING · PAUSED · SEEKING · ENDED | master §9.5 durum/pozisyon iddialarının somut hali | `ornekler.md` §2-§16 (tasarım) |
| 3 | AAC / WMA kullanılmaz | AAC Via LA patentli (encoder/decoder satışına uygulanır), WMA proprietary | master §22.1 |
| 4 | Opus (RFC 6716) ileride eklenir | ağ/düşük gecikme senaryosu | master §22.1 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
