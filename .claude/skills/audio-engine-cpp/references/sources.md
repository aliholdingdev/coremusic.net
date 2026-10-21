# Kaynaklar (Truth Mode) — audio-engine-cpp v4.0.0

> Her iddia bu dosyadaki kaynaklara dayanır. Doğrulanamayan iddia → `⚠️ VERIFICATION REQUIRED`.
> Erişim tarihi: **2026-10-21**. Yerel disk kanıtları (vault/disksel dosyalar) §D'de.

## §1 JUCE

### S1 — docs.juce.com: AudioProcessor::processBlock
- **Ad:** AudioProcessor reference — processBlock (Audio Processing)
- **URL:** https://docs.juce.com/
- **Erişim:** 2026-10-21
- **Doğrulanan:** `processBlock`'ün gerçek-zamanlı ses işleme giriş noktası olduğu;
  AudioProcessor'ın DSP bileşenlerinin temel soyutlaması olduğu.

### S2 — juce.com: What's New in JUCE 9
- **Ad:** JUCE 9 — What's New
- **URL:** https://juce.com/whats-new/
- **Erişim:** 2026-10-21
- **Doğrulanan:** JUCE 9 sürümünün varlığı ve duyurusu. `⚠️ VERIFICATION REQUIRED`:
  kesin GA tarihi/sürüm numarası sayfadan doğrulanmadı; vault `00-sdk-lib-envanter.md`
  "JUCE 9.x" der — depoda JUCE **entegre değil** (CMakeLists: "3. parti bağımlılık YOK").

## §2 ASIO SDK

### S3 — steinberg.net: ASIO SDK (open source)
- **Ad:** ASIO SDK — Steinberg (developers/asiosdk-open)
- **URL:** https://www.steinberg.net/developers/asiosdk-open
- **Erişim:** 2026-10-21
- **Doğrulanan:** ASIO SDK'nın Steinberg tarafından açık-kaynak/ücretsiz dağıtıldığı;
  resmi indirme kaynağı.

### S3b — vcpkg: asiosdk portu
- **Ad:** vcpkg port — asiosdk (REF / sürüm kanıtı)
- **URL:** https://github.com/microsoft/vcpkg (ports/asiosdk)
- **Erişim:** 2026-10-21
- **Doğrulanan:** Paket arşivinin `ASIO-SDK_2.3.4_2025-10-15.zip` olduğu —
  **ASIO SDK 2.3.4** sürüm kanıtı (vault §24 ile uyumlu).

### S3c — MSYS2: asio-sdk paketi
- **Ad:** MSYS2 asio-sdk paketi
- **URL:** https://packages.msys2.org/
- **Erişim:** 2026-10-21
- **Doğrulanan:** Aynı 2.3.4 arşivinin MSYS2 deposunda da bulunduğu (çapraz doğrulama).

## §3 WASAPI (Microsoft Learn)

### S4 — Exclusive-mode streams
- **Ad:** Exclusive-mode streams (WASAPI)
- **URL:** https://learn.microsoft.com/en-us/windows-hardware/audio/audclnt-streamflags-xxx
- **Erişim:** 2026-10-21
- **Doğrulanan:** Exclusive-mode akışlarının düşük-gecikme sağladığı; Windows
  mikserinin bypass edildiği; birden fazla uygulamanın exclusive akışı paylaşamayacağı.

### S5 — Low-latency audio / IAudioClient3 / MMCSS
- **Ad:** Get low-latency audio (Windows audio stack)
- **URL:** https://learn.microsoft.com/en-us/windows-hardware/design/component-guidelines/get-low-latency-audio
- **Erişim:** 2026-10-21
- **Doğrulanan:** `IAudioClient3`'ün Windows 10+ düşük-gecikme API'si olduğu;
  MMCSS ("Pro Audio" iş programı) ile callback thread önceliği verildiği;
  buffer süresinin `IAudioClient::Initialize` ile belirlendiği; exclusive-mode
  akışının latency'i minimize ettiği.

### S6 — IAudioClient::Initialize
- **Ad:** IAudioClient::Initialize (API reference)
- **URL:** https://learn.microsoft.com/en-us/windows/win32/api/audioclient/nf-audioclient-iaudioclientinitialize
- **Erişim:** 2026-10-21
- **Doğrulanan:** Buffer süresinin (hnsBufferDuration) Initialize parametresiyle
  belirlendiği; exclusive/shared flag (`AUDCLNT_SHAREMODE_*`) davranışı.

## §D Yerel Disk Kanıtları (vault + depo — bu skill'in veri kaynağı)

| Kanıt | Dosya | Ne doğrulandı |
|---|---|---|
| Test framework | `app-cpp-projects/neva-engine/CMakeLists.txt` + `tests/` (3 dosya) | **ctest + `<cassert>`** — vault'un "Google Test" ifadesiyle ÇELİŞİYOR (SKILL.md §10.1.1) |
| 3. parti bağımlılık yok | `app-cpp-projects/neva-engine/CMakeLists.txt` | JUCE/ASIO depoda entegre değil; yalnız `ole32 avrt uuid` (Windows) |
| MMCSS kanıtı | `src/wasapi_backend.cpp` | `AvSetMmThreadCharacteristicsW(L"Pro Audio")` (S5 ile uyumlu) |
| EqChain 62 biquad | `include/neva/eq_chain.h` | 31 bant × 2 = 62 biquad sözleşmesi (contracts.md §7) |
| Gecikme hedefleri | `.ai/sartname.md` §16.3 | ASIO <10 ms · WASAPI <20 ms (geçici); toplam <20 ms (nihai) |
| WASAPI ölçümleri | `.ai/wasapi_latency.md` §3.3.10/§3.3.14 | Win11 exclusive/shared default 480; 256 bandı <5.4 ms |
| DSP pipeline | `.ai/projects/NevaEngine/02-dsp-zincir-mimari.md` | EQ→Reverb→Crossover→Limiter sırası (dsp-chain-map.md §1) |
| Lisans envanteri | `.ai/projects/NevaEngine/00-sdk-lib-envanter.md` | ASIO SDK/JUCE/Comtus-reverb lisans durumları (lisans metinleri okunmalı) |
| ASIO exclusive kuralı | `AGENTS.md` kural 9 + vault brain #910 | Tek uygulama; çoklu exclusive deneme → sürücü çökme riski |

## §V Bekleyen Doğrulamalar (VERIFICATION REQUIRED)

1. **JUCE 9 kesin sürüm/GA tarihi** — S2'den doğrulanmadı; entegrasyon başlarken
   resmi sürüm sayfasından teyit edilmeli.
2. **ASIO SDK 2.3.4 lisans metni** — S3 sayfası sürüm yazmıyor; sürüm kanıtı
   S3b/S3c paket kayıtlarından. Lisans metni indirilip okunmalı (00-sdk-lib-envanter.md).
3. **Comtus-reverb lisansı** — vault envanterinde referans var; lisans tipi
   doğrulanmadı.
4. **ETW/WPR ölçüm entegrasyonu** — xrun teşhisi için ölçüm aracının depoya
   entegre edilmediği doğrulandı; araç seçimi planlanacak.
