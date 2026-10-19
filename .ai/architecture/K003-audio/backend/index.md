---
node_id: K003.04
node_type: subdomain
status: draft
parent_id: K003-audio
children:
  - K003.04.01 asio
depends_on:
  - K003.01 engine
  - K003.02 device
  - K003.03 driver
evidence:
  - ".ai/architecture/K003-audio/backend/asio/index.md (katalog, 2026-10-12)"
  - ".ai/architecture/K003-audio/backend/asio/asio-kapsam.md (kapsam sözleşmesi)"
  - ".ai/architecture/K003-audio/audio-master.md §4 A1–A6 · §5 T1–T4 (ASIO/WASAPI)"
  - ".ai/architecture/K003-audio/backend/backend-kapsam.md · backend-ornekler.md · backend-dogrulama.md (2026-10-14 eklendi)"
  - ".ai/projects/NevaEngine/os/01–04.md · 00-sdk-lib-envanter.md · 01-gelistirme-plani.md (2026-10-14 okundu)"
validation:
  - "Çocuk: 1 (asio) — disk sayımı 2026-10-14: backend/asio/ = 19 dosya (index + kapsam + 17 deep-dive); 2026-10-12 sayımı 8 idi, deep-dive seti büyümüştür"
  - "backend/ kök seviyesi 2026-10-14: index.md + kapsam/ornekler/dogrulama üçlüsü tamam (12 çocuk şemasıyla hizalı)"
open_questions:
  - "K003-Q5: engine↔backend sınırı ve pull-model veri sahipliği?"
group: mimari
---

# K003.04 · backend — Backend / Platform API Katmanı

`node_id: K003.04` · `node_type: subdomain` · `parent: K003-audio`

## 1. Bu alan nedir?

**Backend**, ses motoru ile işletim sistemi ses API'si arasındaki **çeviri katmanıdır**: motor "bu bloğu çal" der; backend bunu Core Audio `AudioUnit`'ine, ASIO `bufferSwitch`'ine, WASAPI exclusive isteğine ya da ALSA `snd_pcm_writei`'ye çevirir. Çeviri = format/kanal/çözünürlük uyarlama + latency ayarı + hata kodu tercümesi. K003.03 driver donanım/kernel sınırını, backend ise **OS kullanıcı-zamanı API** sınırını taşır.

## 2. Kapsam

- **İçinde:** platform ses API bağlayıcıları (Core Audio / WASAPI / ASIO / ALSA / PipeWire), format dönüşümü (Float32↔Int16↔Int24), exclusive/shared seçim politikası, latency & buffer-size müzakeresi, backend hata kodlarının uygulama diline tercümesi.
- **Dışında:** DSP algoritması (K004) · çekirdek zamanlama/lock-free (K003.09) · driver/kernel modülü (K003.03) · cihaz listeleme UI'ı (K003.02 device).
- **Komşu:** `K003.04.01 asio` — bu alt-alanın TEK çocuktur.

## 3. Çocuklar (diskten sayım — v2 §2.3 kaydıyla birebir)

| # | Çocuk | ID | Dosya | Yapısı |
|---|---|---|---|---|
| 1 | asio | K003.04.01 | 19 | `index.md` + `asio-kapsam.md` + 17 deep-dive (2026-10-14 glob sayımı; 2026-10-12 kaydı 8 idi — deep-dive seti büyümüştür) |

**Diğer platform bağlayıcı dosyaları (Core Audio / WASAPI / ALSA / PipeWire) için ayrı çocuk KAYITLI DEĞİLDIR** — bu bağlayıcıların omurgası `audio-master.md` §4/§5 seviyesindedir; yeni çocuk ancak v2 §2.3'e eklenerek açılır (KURAL 1).

## 4. asio alt-alanı (katalog — 19 dosya, salt-okunur)

`backend/asio/` **değiştirilmez** (koruma kararı — 6 dosyalık salt-okunur karar tüm alt-alana şamildir). Aşağıdaki tablo **2026-10-12 katalog kaydıdır** (8 dosya); 2026-10-14 sayımında alt-alanda **19 dosya** vardır — kayıt sonrası eklenen 11 deep-dive (adları disk glob'uyla doğrulandı, içerikleri bu turda okunmadı): `asio-cpp-ornekleri` · `asio-buffer-stratejileri` · `asio-hot-swap-ve-device-loss` · `asio-hata-yonetimi-ve-fallback` · `asio-kanal-yapilandirmasi-ve-converters` · `asio-multichannel-ve-timestamp` · `asio-timestamp-ve-clock-senkronu` · `asio-uygulama-entegrasyon-ornekleri` · `asio-wasapi-karsilastirma` · `asio-cihaz-yonetimi-ve-enumarasyon` · `asio-latency-ve-jitter`:

| Dosya | Ne belgeler | Durum |
|---|---|---|
| `index.md` | Katalog (7 satır: dosya → soru → kanıt yoğunluğu) | korunan katalog |
| `asio-kapsam.md` | Kapsam sözleşmesi (IN/OUT-SCOPE + açık sorular) | korunan |
| `asio-anayasa.md` | ASIO temel doğası ve host sözleşmesi | **salt-okunur** |
| `asio-arayuzler.md` | `IASIO*` arayüz ailesi (host↔driver API) | **salt-okunur** |
| `asio-surucu-modeli.md` | ASIO driver'ının Windows'ta çalışma modeli | **salt-okunur** |
| `asio-platform-uyumlulugu.md` | Windows/mimari kombinasyonları | **salt-okunur** |
| `asio-daw-uyumlulugu.md` | DAW uyumluluğu (multi-client) | **salt-okunur** |
| `asio-lisans-ve-dogrulama.md` | GPLv3 lisans + SDK 2.3.4 doğrulaması | **salt-okunur** |

Teknik omurga (A1–A6 pull/exclusive, T1–T4 ASIO/WASAPI) `audio-master.md` §4–§5'tedir; lisans-riski K003-Q3 (GPLv3) `audio-dogrulama.md`'dedir.

## 5. Gezinti

`index.md` (bu dosya) · **`backend-kapsam.md`** (Kademe-2 kapsam kaydı — §8'de Q5'in tasarım düzeyi çözümü) · **`backend-ornekler.md`** (NevaEngine os/01–04 entegrasyon örnekleri + asio/ atıf kataloğu) · **`backend-dogrulama.md`** (kanıt/doğrulama tablosu) · `asio/index.md` (alt-alan kataloğu) · `asio/` 19 dosya.
(2026-10-14: kök seviye kapsam/örnekler/doğrulama üçlüsü eklendi — 12 çocuk şemasıyla hizalı; `audio-master.md` §10 tablosu ile disk gerçeği artık birebir.)

## 6. Açık sorular

- **K003-Q5:** engine↔backend sınırı: pull-model'de render callback'i kimin sahipliği? → 2026-10-14: **tasarım düzeyi çözüm var** (`backend-kapsam.md` §8 — dört platform örneği ortak deseni: cihaz I/O buffer'ı adaptörde, `prepare()` init anında, engine `process()` in-place); kesin sınır **ADR'si bekliyor** — kapı KAPALI, `[TASARIM]`.
- **K003-Q1 (ASIO lisans):** ✅ **KAPANDI (2026-10-14)** — ASIO SDK **GPLv3 açık varyant** kabul edildi (`.ai/projects/NevaEngine/01-gelistirme-plani.md` §7 karar #1; detay `backend-dogrulama.md` §3).
- ASIO Cihaz Paylaşım Protokolü ayrı node mu? (asio-kapsam §4.1 — korunan dosyada)
- Neva Engine ASIO-Guard uygulayacak mı? (asio-kapsam §4.2 — korunan dosyada)

---

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward

