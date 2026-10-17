---
node_id: K014-media/media-testing
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
  - "media-testing ↔ K019-testing sahipliği → ⚠️ VERIFICATION REQUIRED (§7/4)"
group: mimari
---


# media-testing — K014-media (kademe-2 · subdomain)

**ELI15:** Medya tarafı testleri: dosya, akış, çevrimdışı senaryoları.

**Sınır (ne DEĞİL):** Ortak test altyapısı K019-testing · ses kalitesi testi K003/K019 sınırında.

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
| 2 | media-testing ↔ K019-testing sahipliği → ⚠️ VERIFICATION REQUIRED (§7/4) | FM open_questions |
| 3 | Kademe-3/4 genisletmesi | ADR-097 K4 faz (C) bekliyor |

> Bu tablo FM `open_questions` alaniyi index`e gorunur kilir; yeni iddia eklemez.

---

## §ROL (ELI15)

Medya testleri, medya tarafının doğru çalıştığını kanıtlayan testlerin evidir: dosyanın doğru çözüldüğü (decode doğruluğu), ses seviyesinin yayın hedefine oturduğu (loudness) ve HTTP Range isteklerinin doğru sözleşmeyi verdiği (parça/seek) burada sınanır (master §9.11 — v2 §2.15 S2; kapsam decode/çalma/akış kalitesi TASARIM İDDİASI). Sahiplik K019-testing ile belirsizdir — ortak test altyapısı (runner/CI/coverage gate) K019'dadır; bu çocuk yalnız medya'ya özgü senaryoların İÇERİĞİNİ tanımlar (master §7/4 — ⚠️ VERIFICATION REQUIRED). Test aracı seti hâlâ ⚠️ UNKNOWN'dur (master §12/10) ve master §22.6'da "⏳ AÇIK"tır — Vault Steward + K019 incelemesi beklenir; bu dosya araç SEÇMEZ. Ölçüm eşikleri (decode süresi, akış gecikmesi dahil) de ⚠️ UNKNOWN'dur (master §15); bu çocuk yalnız ölçüm VERİSİ üretir, ölçüm altyapısı K017-observability'dedir.

## §KAPSAM

### Kapsam içinde (bu çocuğun malı)

| # | Konu | Kaynak |
|---|---|---|
| 1 | Medya testleri rolü: decode/çalma/akış kalitesi kapsamı | master §9.11 · v2 §2.15 (S2) |
| 2 | Decode doğruluk testi: çözülmüş PCM çıktısının hash'i → golden fixture ile birebir karşılaştırma (senaryo #1 — sahiplik ⚠️ §7/4) | master §13/1 · TASARIM ÖNERİSİ (`ornekler.md` §2-§16) |
| 3 | Loudness doğrulama testi: EBU R128 hedef −23 LUFS | master §22.3 · TASARIM ÖNERİSİ |
| 4 | HTTP Range sözleşmesi testi: 206 + `Content-Range` + `Accept-Ranges` (parça/seek) | master §22.2 (RFC 9110) · TASARIM ÖNERİSİ |
| 5 | Etiket okuma/yazma round-trip testi: yaz → oku → eşitlik (ID3v2 · Vorbis Comment · `ilst` · LIST-INFO) | master §22.3 · TASARIM ÖNERİSİ |
| 6 | Akış kalitesi test senaryosu: bitrate/kesinti (sahiplik ⚠️ belirsiz) | master §13/2 |
| 7 | Golden fixture deposu: referans dosya + beklenen hash/LUFS/etiket çifti (salt-okunur) | TASARIM ÖNERİSİ (`ornekler.md`) |
| 8 | Regresyon eşiği kapısı — bu dosya eşik KOYMaz, yalnız okur | ⚠️ UNKNOWN → Vault Steward (master §15) |

### Kapsam dışında (kimin?)

| # | Konu | Ait olduğu düğüm | Kaynak |
|---|---|---|---|
| 1 | Ortak test altyapısı (runner/CI/coverage gate) | K019-testing | master §7/4 (⚠️ sahiplik) · stub sınır |
| 2 | Ses kalite/sinyal testi (DSP çıktı ölçümü) | K003-audio / K019 sınırı | stub sınır |
| 3 | Çevrimdışı erişim testi (ne hazır) | K014-media/offline | master §13/3 |
| 4 | İndirme resume testi (yarıda kesilme) | K014-media/downloads | master §13/4 |
| 5 | Önbellek TTL testi (süre politikası) | K014-media/media-cache | master §13/5 |
| 6 | Metadata doğruluk testi (etiket kaynağı) | K014-media/metadata | master §13/6 |
| 7 | Artwork yükleme testi (kapak resmi) | K014-media/artwork | master §13/7 |
| 8 | Eşitleme tutarlılık testi (cihazlar arası eşitlik) | K014-media/synchronization | master §13/8 |
| 9 | Ölçüm altyapısı (istek/panel/alarm) | K017-observability | master §15 notu |
| 10 | Test aracı seti seçimi (ne ile test edileceği?) | Vault Steward + K019 incelemesi | master §12/10 · §22.6 (⏳ AÇIK) |
| 11 | DSP/sinyal işleme doğrulaması | K003-audio / K004-dsp | master §2.2 |

## §BİLEŞENLER

| # | Bileşen | Sorumluluk | Etiket |
|---|---|---|---|
| 1 | Decode doğruluk testi | çözülmüş PCM'in hash'ini alır → golden fixture hash'i ile birebir karşılaştırır; fark = FAIL (senaryo §13/1) | TASARIM ÖNERİSİ (`ornekler.md` §2-§16) |
| 2 | Loudness ölçüm/doğrulama | ölçülen LUFS → EBU R128 hedef −23 LUFS (master §22.3); tolerans eşiği AÇIKTIR | TASARIM ÖNERİSİ · eşik ⚠️ UNKNOWN (master §15) |
| 3 | Range sözleşmesi testi | `Range: bytes=a-b` isteği → 206 + `Content-Range` + `Accept-Ranges` doğrular (RFC 9110 · master §22.2) | TASARIM ÖNERİSİ (`ornekler.md`) |
| 4 | Etiket round-trip testi | etiket yaz → oku → alan eşitliği (ID3v2 · Vorbis Comment · `ilst` · LIST-INFO · master §22.3) | TASARIM ÖNERİSİ (`ornekler.md`) |
| 5 | Golden fixture deposu | referans dosya + beklenen hash/LUFS/etiket çifti; fixture'lar salt-okunur, test verisi üretiminde kullanılır | TASARIM ÖNERİSİ (`ornekler.md`) |
| 6 | Regresyon eşiği kapısı | tolerans/sınır DEĞERİ okur — değer KOYMAZ | ⚠️ UNKNOWN → Vault Steward (master §15) |
| 7 | Araç kapısı | test aracı setini çağrır — araç SEÇİMİ YAPMAZ | ⚠️ UNKNOWN (master §12/10 · §22.6 ⏳ AÇIK) |
| 8 | Ölçüm verisi akışı | test metrikleri → K017-observability (K015 yalnız veri üretir) | TASARIM İDDİASI (master §15 notu) |
| 9 | Senaryo kayıt defteri | master §13/#1–#8 senaryolarının medya'ya özgü içerik tanımı (sahiplik satır bazında) | master §13 |

## §STANDARTLAR

| # | Standart/fark | Ne | Kaynak | Etiket |
|---|---|---|---|---|
| 1 | HTTP Range / parça-sekme sözleşmesi | 206 Partial Content + `Content-Range` + `Accept-Ranges` — RFC 9110 (STD 97) | master §22.2 | ✅ doğrulanmış standart |
| 2 | HLS adaptif akış | RFC 8216 (Informational) — yalnız canlı/uzun içerikte | master §22.2 | ✅ doğrulanmış standart |
| 3 | MPEG-DASH | ISO/IEC 23009-1:2026 | master §22.2 | ✅ doğrulanmış standart |
| 4 | EBU R128 yayın hedefi | −23 LUFS | master §22.3 | ✅ doğrulanmış referans |
| 5 | ReplayGain 2.0 | −18 LUFS (kullanıcı dosyası normalizasyonu) | master §22.3 | ✅ doğrulanmış referans |
| 6 | Codec/format seti (fixture kapsamı) | MP3 + FLAC + WAV temel seti | master §22.1 (set seçimi TASARIM ÖNERİSİ — Steward onayı ister) | ✅ doğrulanmış · seçim TASARIM ÖNERİSİ |
| 7 | Etiket şemaları (round-trip testi) | MP3 ID3v2.3↔v2.4 (TYER→TDRC kırılması!) · FLAC Vorbis Comment · MP4 `ilst` · WAV LIST-INFO | master §22.3 | ✅ doğrulanmış |
| 8 | Cache başlıkları (indirme/önbellek testi) | `ETag` (içerik hash) + `Cache-Control: public, max-age=31536000, immutable` | master §22.5 | ✅ doğrulanmış |
| 9 | Test senaryoları (#1–#8) | decode doğruluğu · akış kalitesi · çevrimdışı · resume · TTL · metadata · artwork · eşitleme | master §13 | ✅ doğrulanmış senaryo listesi |
| 10 | Ölçüm eşikleri | decode süresi/akış gecikmesi/indirme hızı/isabet oranı/eşitleme gecikmesi — hepsi ⚠️ UNKNOWN | master §15 | ⚠️ UNKNOWN → Vault Steward |
| 11 | Test araç seti | ne ile test edileceği — KAPANMADI | master §12/10 · §22.6 (⏳ AÇIK) | ⚠️ UNKNOWN |

> **Not:** master §22 web araştırması test ARAÇ SETİNİ ve ölçüm EŞİKLERİNİ kapatmamıştır (§22.6 "⏳ AÇIK" · §15 eşik ⚠️ UNKNOWN) — bu çocuk için doğrulanmış **araç adı/package/sürüm YAZILMAZ**; `ornekler.md`'deki örnekler dil-dahili std yapılar veya işaretli pseudocode seam'lerdir.

### §STANDARTLAR — TASARIM ÖNERİLERİ (Vault Steward onayı ister)

| # | Öneri | Gerekçe | Kaynak |
|---|---|---|---|
| 1 | Her decode testi PCM'in SHA-256 hash'ini golden fixture hash'i ile birebir karşılaştırır | deterministik, araçtan bağımsız doğruluk kanıtı | TASARIM ÖNERİSİ (`ornekler.md` §2-§16) |
| 2 | Loudness doğrulaması EBU R128 −23 LUFS hedefine göre yapılır; tolerans DEĞERİ konmaz | hedef doğrulanmış (§22.3), eşik AÇIK (§15) | master §22.3 · §15 → Vault Steward |
| 3 | Her medya sunucu testi Range sözleşmesini (206 + `Content-Range` + `Accept-Ranges`) doğrular | parça/seek temeli RFC 9110 (§22.2) | master §22.2 |
| 4 | Etiket round-trip'i format başına yazılır; ID3v2.3↔v2.4 TYER→TDRC kırılması ayrıca sınanır | bilinen uyumluluk kırmızı çizgisi | master §22.3 |
| 5 | Regresyon eşiği (tolerans) Vault Steward kararı olmadan CI'a bağlanmaz | eşik AÇIK — uydurma sayı yazılmaz | master §15 (eşik ⚠️ UNKNOWN) |
| 6 | Test aracı seti seçimi K019-testing incelemesi + Vault Steward kararına bağlıdır | araç AÇIK (§12/10) — bu dosya araç SEÇMEZ | master §12/10 · §22.6 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
