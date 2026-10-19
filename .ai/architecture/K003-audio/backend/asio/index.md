---
title: "K003-audio/backend/asio — ASIO Düğümü Kataloğu"
node_id: "K003-audio/backend/asio"
node_type: "node-index"
status: "draft"
parent_id: "K003-audio/backend"
children:
  - "K003-audio/backend/asio/asio-kapsam"
  - "K003-audio/backend/asio/asio-anayasa"
  - "K003-audio/backend/asio/asio-arayuzler"
  - "K003-audio/backend/asio/asio-surucu-modeli"
  - "K003-audio/backend/asio/asio-platform-uyumlulugu"
  - "K003-audio/backend/asio/asio-daw-uyumlulugu"
  - "K003-audio/backend/asio/asio-lisans-ve-dogrulama"
  - "K003-audio/backend/asio/asio-buffer-stratejileri"
  - "K003-audio/backend/asio/asio-latency-ve-jitter"
  - "K003-audio/backend/asio/asio-multichannel-ve-timestamp"
  - "K003-audio/backend/asio/asio-hot-swap-ve-device-loss"
  - "K003-audio/backend/asio/asio-wasapi-karsilastirma"
  - "K003-audio/backend/asio/asio-cpp-ornekleri"
  - "K003-audio/backend/asio/asio-cihaz-yonetimi-ve-enumarasyon"
  - "K003-audio/backend/asio/asio-timestamp-ve-clock-senkronu"
  - "K003-audio/backend/asio/asio-kanal-yapilandirmasi-ve-converters"
  - "K003-audio/backend/asio/asio-hata-yonetimi-ve-fallback"
  - "K003-audio/backend/asio/asio-uygulama-entegrasyon-ornekleri"
depends_on:
  - "K003-audio/backend"
evidence:
  - "https://github.com/audiosdk/asio"
  - "https://github.com/audiosdk/asio/blob/main/changes.txt"
  - "https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams"
validation:
  - "Dosya satırı / URL / FM denetimi 2026-10-12 turunda ölçüldü; iddia–kanıt satırları ilgili deep-dive §iddia tablolarındadır"
open_questions:
  - "ASIO Cihaz Paylaşım Protokolü K003 ASIO kapsamına dahil mi (asio-kapsam §4.1)?"
  - "Neva Engine ASIO-Guard uygulayacak mı (asio-kapsam §4.2)?"
date: "2026-10-12"
updated: "2026-10-12"
group: mimari
---

# K003-audio/backend/asio — ASIO Düğümü Kataloğu

Neva Engine'in **ASIO** (Audio Stream Input/Output) host yüzeyi için deep-dive düğümlü kataloğu. **19 dosya**: 1 katalog (bu dosya) + 1 kapsam sözleşmesi + 17 deep-dive;
kanıtlanamayan iddialar `⚠️ VERIFICATION REQUIRED` / `UNKNOWN` ile işaretlenir.

## §0 ELI15

Her dosya tek bir enstrümanın notasıdır: `asio-anayasa` kuralları, `asio-arayuzler` API'yi, `asio-kapsam` kapsam kartını;
yeni beş dosya cihaz kimliği, saat senkronu, örnek tipi dönüşümü, hata/fallback ve entegrasyon akışlarını taşır.

## §1 Okuma Yolu (bağlantı grafiği)

```
asio-kapsam → asio-anayasa → asio-arayuzler · asio-surucu-modeli
                           → asio-platform-uyumlulugu → asio-daw-uyumlulugu
asio-cihaz-yonetimi → asio-hata-yonetimi-ve-fallback → asio-wasapi-karsilastirma
asio-multichannel → asio-timestamp · asio-kanal-yapilandirmasi (kod en son)
```

## §GRUPLAR (ADR-097 §5.5 — iki-ekseni gruplama · dosya → ana soru → kademe)

| Grup | Dosya | Ana soru | Kademe |
|---|---|---|---|
| mimari | [`index.md`](index.md) | Düğüm kataloğu, okuma yolu, grup haritası | Kademe-3 |
| mimari | [`asio-kapsam.md`](asio-kapsam.md) | Ne kapsar, ne kapsamaz — bağımlılık + kabul kriteri | Kademe-3 |
| mimari | [`asio-anayasa.md`](asio-anayasa.md) | ASIO'nun temel doğası ve host sözleşmesi? | Kademe-3 |
| mimari | [`asio-arayuzler.md`](asio-arayuzler.md) | Host↔driver API yüzeyi hangi arayüzler? | Kademe-3 |
| mimari | [`asio-surucu-modeli.md`](asio-surucu-modeli.md) | ASIO driver'ı Windows'ta nasıl çalışır? | Kademe-3 |
| mimari | [`asio-platform-uyumlulugu.md`](asio-platform-uyumlulugu.md) | Hangi Windows/mimari kombinasyonları geçerli? | Kademe-3 |
| mimari | [`asio-buffer-stratejileri.md`](asio-buffer-stratejileri.md) | Tampon boyutu, çift tamponlama, priming stratejileri | Kademe-3 |
| mimari | [`asio-latency-ve-jitter.md`](asio-latency-ve-jitter.md) | Gecikme bileşenleri, jitter kaynakları, ölçüm yöntemi | Kademe-3 |
| mimari | [`asio-multichannel-ve-timestamp.md`](asio-multichannel-ve-timestamp.md) | Çok kanallı eşleme, zaman damgası ve saat modeli | Kademe-3 |
| mimari | [`asio-hot-swap-ve-device-loss.md`](asio-hot-swap-ve-device-loss.md) | Cihaz ekleme/çıkarma, device-loss kurtarma yolu | Kademe-3 |
| mimari | [`asio-wasapi-karsilastirma.md`](asio-wasapi-karsilastirma.md) | ASIO ↔ WASAPI yetenek/gecikme/kısıt karşılaştırması | Kademe-3 |
| mimari | [`asio-cihaz-yonetimi-ve-enumarasyon.md`](asio-cihaz-yonetimi-ve-enumarasyon.md) | Cihaz bilgisi, indeks/ kimlik, `ASIOOutputReady`, enumerasyon yenileme | Kademe-3 |
| mimari | [`asio-timestamp-ve-clock-senkronu.md`](asio-timestamp-ve-clock-senkronu.md) | Örnek konumu, systemTime, word clock alanı, drift ölçümü | Kademe-3 |
| mimari | [`asio-kanal-yapilandirmasi-ve-converters.md`](asio-kanal-yapilandirmasi-ve-converters.md) | `ASIOChannelInfo` tip/grup, 16/24/32/f32 dönüştürücü, endianness | Kademe-3 |
| mimari | [`asio-hata-yonetimi-ve-fallback.md`](asio-hata-yonetimi-ve-fallback.md) | Oran müzakeresi başarısızlığı, `ASE_*`, WASAPI fallback, resync | Kademe-3 |
| kod | [`asio-daw-uyumlulugu.md`](asio-daw-uyumlulugu.md) | ASIO DAW'larla nasıl geçinir (multi-client, ASIO-Guard)? | Kademe-3 |
| kod | [`asio-cpp-ornekleri.md`](asio-cpp-ornekleri.md) | Kaynaklı C++ örnekleri (ASIO SDK hostsample + JUCE) | Kademe-3 |
| kod | [`asio-uygulama-entegrasyon-ornekleri.md`](asio-uygulama-entegrasyon-ornekleri.md) | Host callback zinciri, `asioMessage` pompa, DAW topolojisi | Kademe-3 |
| kanit | [`asio-lisans-ve-dogrulama.md`](asio-lisans-ve-dogrulama.md) | Lisans nedir, sürüm nasıl doğrulanır? | Kademe-3 |

**Dağılım:** `mimari` 15 · `kod` 3 · `kanit` 1 · toplam 19 dosya (index dahil).

## §2 Bağlayıcı Repo Kuralları ve Sürüm Kanıtı

- **`*.cpp`/`*.h` yasak** — bu düğümde yalnız şartname + tasarım; `NevaEngine/` C++ kodu **PLANNED**.
- **ASIO Exclusive Lock:** yalnız BİR uygulama — device-loss → **WASAPI shared**
  (IAudioClient3 low-period) fallback **bağlayıcı** (`.ai/AGENTS.md` §17/6).
- **PCM5122 yasak, yalnız PCM3168A** (ADR-038 H001) — donanım komşusu.
- **Subagent commit atmaz** — tüm değişiklikler draft; commit orkestratöre aittir.
- **Sürüm kanıtı:** ASIO SDK **2.3.4** = `changes.txt` + `audiosdk/asio` deposu ✅
  (2026-10-12); vcpkg sürümü/SHA512 **UNKNOWN** (⚠️ VERIFICATION REQUIRED).

**Kaynak:** audiosdk/asio (README+changes.txt) · learn.microsoft coreaudio · juce-framework/JUCE · 2026-10-12
**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
