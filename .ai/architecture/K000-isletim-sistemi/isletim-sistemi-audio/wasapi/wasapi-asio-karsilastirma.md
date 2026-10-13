---
node_id: K000-isletim-sistemi/isletim-sistemi-audio/wasapi/wasapi-asio-karsilastirma
node_type: architecture-deepdive
title: "WASAPI ↔ ASIO Karşılaştırma — Lisans, Gecikme, Kütüphane Perspektifi, Karar Ağacı"
type: architecture-deepdive
status: draft
date: 2026-10-12
parent_id: K000-isletim-sistemi/isletim-sistemi-audio
depends_on:
  - "K000-isletim-sistemi/isletim-sistemi-audio/wasapi/wasapi-anayasa"
evidence:
  - "https://github.com/audiosdk/asio (2026-10-12)"
  - "https://docs.juce.com/master/classjuce_1_1AudioIODeviceType.html (2026-10-12)"
  - "https://portaudio.com/docs/v19-doxydocs-dev/pa__win__wasapi_8h_source.html (2026-10-12)"
  - "https://github.com/thestk/rtaudio (2026-10-12)"
  - "https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/low-latency-audio (2026-10-12)"
  - "https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams (2026-10-12)"
  - "https://learn.microsoft.com/en-us/windows/win32/coreaudio/recovering-from-an-invalid-device-error (2026-10-12)"
validation:
  - "ASIO SDK dosya envanteri ve COM modeli repo sayfasından (SDK Content tablosu) alındı"
  - "ASIO'nun resmi gecikme ms rakamı YOK → ⚠️ (Q2); uydurulmadı"
open_questions:
  - "Q2: ASIO resmi/ölçülmüş ms gecikme kaynağı? → ⚠️ UNKNOWN"
  - "Q10: ASIO preemption/hot-plug davranışının resmi kaynağı? → ⚠️ (yalnız WASAPI exclusive sayfası çekildi)"
group: mimari
---

# WASAPI ↔ ASIO Karşılaştırma — Lisans · Gecikme · Kütüphaneler · Karar Ağacı

> **Amaç:** CoreMusic'in fallback zincirini (`.ai/AGENTS.md` §17/6: "ASIO
> device loss → WASAPI fallback") tasarlamak için iki teknolojinin kanıtla
> belgelenmiş farkları. Uydurma benchmark yok; ASIO ms rakamı çekilmediyse
> ⚠️ UNKNOWN yazılır.
>
> **Durum:** IMPLEMENTED (belge) · Kod ⚠️ PLANNED.

---

## §0 ELI15 — Fark bir cümlede

ASIO, donanım üreticisinin kendi sürücüsü üzerinden **tek uygulamaya özel**
düşük gecikmeli yol kurar (Steinberg standardı, lisansı dual); WASAPI,
Windows'un **her cihazda** çalışan resmi yoludur (shared = motor karıştırır,
exclusive = motor bypass ama preemption riski). CoreMusic birincil ASIO,
fallback WASAPI der (kanıt: [S8], [S3], [S23], `.ai/AGENTS.md` §17/6).

---

## §1 ASIO Kimlik Kartı (repo kanıtı, [S23])

| Alan | Değer |
|---|---|
| Tam adı | Audio Stream Input/Output |
| Sürüm (SDK) | ASIO 2.3 |
| Sahibi | Steinberg (ASIO SDK 2.3) |
| Tanım | "Low-latency, high-performance audio standard" |
| Neleri destekler | Variable bit depths, sample rates, multi-channel operation, synchronization |
| Lisans | **Dual**: (1) proprietary ASIO SDK license, (2) GPLv3 — "Both options grant access to the same SDK" |
| GPLv3 geçiş kuralı | Ürün GPLv3 şartlarıyla (kaynak açıklama dâhil) yayınlanmalı |
| Marka/logo | ASIO adı+logo kurallara bağlı (usage guidelines); ürün adında kullanılamaz; splash/about/web/documentation bağlamı |
| Ana arayüz dosyaları | `asio.h` (ASIO C definition) · `iasiodrv.h` (ASIO driver class interface) |
| Sürücü sınıfı | `asiodrvr.h/.cpp` (ASIO driver class base definition) |
| COM altyapısı (Windows) | `combase.h/.cpp` (COM base definitions) · `dllentry.cpp` (DLL functions) · `register.cpp` (driver self registration) |
| Sürücü yükleme (Windows) | `host/pc/asiolist.cpp`: "instantiates an ASIO driver via the COM model (Windows only)" |
| Host yönetimi | `host/asiodrivers.h/.cpp` (enumeration + instantiation) · `host/ASIOConvertSamples.*` (sample format conversion class) |
| Zamanlayıcı (Windows) | `driver/asiosample/wintimer.cpp` — `bufferSwitch()` wakeup thread |
| Örnek sürücü | `driver/asiosample` (ASIO 2.0 sample driver) |
| macOS izleri | `asio.cpp` "not used on MacOS"; örnek `mactimer.cpp`/`macnanosecs.cpp` (macOS time) |

**Çıkarım:** ASIO, Windows'ta **COM üzerinden** yüklenir (repo dosya
envanteri bunu açıkça söyler); bu, WASAPI'nin `Activate`/COM dünyasıyla
aynı altyapıya dayanır ama arayüzler farklıdır.

---

## §2 Karşılaştırma Matrisi (kanıt sütunlu)

| Kriter | WASAPI | ASIO | Kanıt |
|---|---|---|---|
| Sahip | Microsoft (Windows bileşeni) | Steinberg (SDK) | [S2], [S23] |
| Lisans | Ayrı SDK lisansı yok (Windows API) | Dual: proprietary veya GPLv3 | [S2], [S23] |
| Cihaz kapsamı | Tüm Windows ses cihazları | Yalnız ASIO sürücülü donanım | [S1], [S23], [S8] |
| Karışım | Shared'de motor karıştırır; exclusive'de yok | Karışım yok — uygulama kendi çoklu-kanalını yönetir | [S4], [S23] |
| Gecikme kaynağı | Shared: motor (~1,3 ms Win10 1607+; ~12 ms öncesi) + sürücü; exclusive: sürücü | Doğrudan sürücü (ms rakamı ⚠️ Q2) | [S8] |
| Düşük gecikme resmi API'si | `IAudioClient3` (Win10 1607+) | ASIO'nun kendisi düşük gecikme standardı | [S7], [S23] |
| Exclusive/tek-hakimiyet | Exclusive mod var; **preemption tabidir** | Exclusive benzeri sahiplik (preemption kaynağı ⚠️ Q10) | [S3], [S8] |
| Sistem sesleri | Shared'de susmaz; exclusive'de susturulur (uyarı var) | Cihaz sahipliği nedeniyle etkilenir (kaynak ⚠️) | [S3] |
| Cihaz kopma yönetimi | `AUDCLNT_E_DEVICE_INVALIDATED` + `OnSessionDisconnected` nedenleri | SDK hata/değişim mekanizması (detay ⚠️) | [S16] |
| Loopback | Var (shared-only, `AUDCLNT_STREAMFLAGS_LOOPBACK`) | Analogu kanıtlanmadı → UNKNOWN | [S15] |
| Session/mikser | Session API'leri + Sndvol paneli | Session kavramı yok (tek uygulama modeli) ⚠️ | [S10] |
| Format dönüşümü | Shared'de motor SRC (AUTOCONVERTPCM) | Uygulama/sürücü; `ASIOConvertSamples` sınıfı | [S6], [S23] |
| Çoklu-kanal + senkron | Cihaz formatına bağlı | SDK tanımı gereği "multi-channel operation and synchronization" | [S14], [S23] |
| OS kapsamı | Windows Vista+ | Windows (COM); SDK'da macOS'e özgü örnek dosyalar var | [S2], [S23] |
| Hot-swap (cihaz değişimi) | MMDevice bildirimleri + session olayları | ⚠️ bu tur resmi kaynak çekilmedi (Q10) | [S16] |
| Ek maliyet | Yok (Windows) | SDK lisans kararı (proprietary/GPLv3) — ADR gerekir | [S23] |

---

## §3 Gecikme Sayıları — Karşılaştırılabilirlik Sınırı

| Teknoloji | Sayı | Kaynak |
|---|---|---|
| WASAPI shared motor (Win10 1607+) | ~1,3 ms | [S8] |
| WASAPI shared motor (öncesi, float32) | ~12 ms | [S8] |
| WASAPI shared period (Win10 varsayılan) | 10 ms (sürücü-destekli) | [S8] |
| WASAPI exclusive (PortAudio ölçümü) | ~3 ms (event-driven) | [S21] |
| WASAPI polling (PortAudio ölçümü) | 10–13 ms (excl.) / 15–20 ms (shared) | [S21] |
| ASIO | ⚠️ UNKNOWN — resmi/ölçülmüş ms kaynağı bu turda YOK (Q2) | — |

**Kural:** "ASIO daha düşük gecikmeli" iddiası bu seride **sayıyla**
kanıtlanmaz; yalnız mimari gerekçe (motor yok, doğrudan sürücü) çerçevesinde
kalır. Ölçüm, Neva Engine latency protokolünün işidir (PLANNED, qa-engineer
handover).

---

## §4 Kütüphane Perspektifi — WASAPI ve ASIO'yu Aynı Kod Tabanında Kullananlar

Üç popüler açık kaynak kütüphane, iki teknolojiyi de sarmalar; bu, CoreMusic
için "tek soyutlama katmanı" seçeneğini kanıtlar.

### §4.1 JUCE (CoreMusic'in hedef kütüphanesi, `.ai/AGENTS.md` §4)

| JUCE fabrika/metodu | Karşılığı | Mod/senaryo | Kanıt |
|---|---|---|---|
| `createAudioIODeviceType_WASAPI(WASAPIDeviceMode)` | WASAPI | Parametreli | [S20] |
| `juce::WASAPIDeviceMode::shared` | WASAPI shared | Motor dahil, herkes çalışır | [S25] |
| `juce::WASAPIDeviceMode::exclusive` | WASAPI exclusive | Motor bypass | [S25] |
| `juce::WASAPIDeviceMode::sharedLowLatency` | IAudioClient3 shared düşük gecikme | Win10 1607+ | [S25], [S7] |
| `createAudioIODeviceType_ASIO()` | ASIO | ASIO sürücülü donanım | [S20] |
| `createAudioIODeviceType_DirectSound()` | DirectSound | Eski yol (fallback adayı değil; kapsam dışı) | [S20] |

**JUCE çıkarımı:** JUCE, WASAPI'yi üç moda böler (shared/exclusive/
sharedLowLatency) — bu, CoreMusic fallback zincirinin birebir karşılığıdır:
ASIO → WASAPI exclusive → WASAPI sharedLowLatency → WASAPI shared.

### §4.2 PortAudio (WASAPI sarmalayıcısı, [S21])

| Bayrak/seçenek | Etkisi |
|---|---|
| `paWinWasapiExclusive` | Exclusive mode açar |
| `paWinWasapiPolling` | Polling buffering (event-driven yerine) |
| `paWinWasapiAutoConvert` | Motor SRC garantisi (AUTOCONVERTPCM eşdeğeri) |
| `paWinWasapiExplicitSampleFormat` | Açık örnekleme formatı/kanal maskesi |
| `eStreamOptionRaw` | Motor bypass (Windows 8.1+) |
| `eStreamOptionMatchFormat` | Motor içi SRC (Windows 10+) |
| `eThreadPriorityProAudio` | Exclusive varsayılan iplik önceliği |
| `eThreadPriorityAudio` | Shared varsayılan iplik önceliği |

### §4.3 RtAudio (tek-dosya minimalizm, [S22])

| Gerçek | Değer |
|---|---|
| Yapı | Tek `.cpp` + tek `.h` (header-only benzeri minimal dağıtım) |
| Windows API'leri | DirectSound · ASIO · WASAPI |
| Çıkarım | WASAPI+ASIO tek soyutlamada mümkün; RtAudio bunu en küçük yüzeyle yapar |

**Karar etkisi:** CoreMusic JUCE 9 kullanacağı için (repo hedefi, `.ai/
AGENTS.md` §4), JUCE soyutlaması ana yol; PortAudio/RtAudio yalnız
referans/benchmark perspektifidir (kod PLANNED).

---

## §5 CoreMusic Karar Ağacı (ASCII — şartname)

```
Neva Engine başlat
   │
   ├─ ASIO cihazı var mı? (asioDrivers->init() / JUCE ASIO open)
   │     │
   │     ├─ EVET ─► ASIO open dene
   │     │            ├─ OK ─► render (birincil yol; ölçüm: latency protokolü PLANNED)
   │     │            └─ FAIL / device loss ─┐
   │     │                                   │
   │     └─ HAYIR ───────────────────────────┤
   │                                         ▼
   │                        WASAPI fallback zinciri (§5.1)
   │                                         │
   │                                         ▼
   │                              render (fallback yolu)
   ▼
Kullanıcıya durum bildir (hangi yol aktif; panel PLANNED)
```

### §5.1 WASAPI Fallback Zinciri (sıra + gerekçe)

| # | Adım | Gerekçe | Kanıt |
|---|---|---|---|
| 1 | WASAPI **exclusive** (event-driven) | Motor bypass → ASIO'ya en yakın gecikme | [S3], [S21] |
| 2 | WASAPI **sharedLowLatency** (IAudioClient3, Win10 1607+) | ~1,3 ms motor + garanti paylaşımlı çalışma | [S7], [S8] |
| 3 | WASAPI **shared** standart (10 ms period) | Her cihazda çalışır (garanti son durak) | [S8] |
| 4 | Ses devre dışı + panel uyarısı | Hiçbiri açılamadı (ör. `DEVICE_IN_USE` + exclusive reddi) | [S6] |

**Her adımda hata kodları** (arayüzler §5): `AUDCLNT_E_DEVICE_IN_USE`,
`AUDCLNT_E_EXCLUSIVE_MODE_ONLY`, `AUDCLNT_E_UNSUPPORTED_FORMAT`,
`AUDCLNT_E_DEVICE_INVALIDATED`.

---

## §6 Hibrit Kullanım Senaryoları (CoreMusic ürün mantığı)

| Senaryo | Yol | Neden |
|---|---|---|
| Stüdyo/DAC + ASIO sürücüsü kurulu | ASIO (birincil) | Donanım-sürücü doğrudan; sistem sesleriyle çakışma yok sayılan hedef |
| Sadece bileşen (inbox) ses donanımı | WASAPI (fallback) | ASIO sürücüsü yok; WASAPI her cihazda çalışır |
| Kullanıcı exclusive reddediyor/kapatıyor | WASAPI shared (fallback 3) | Kullanıcı ayarı bağlayıcı ([S3]) |
| Win10 1607+ düşük gecikme arzu | sharedLowLatency (fallback 2) | IAudioClient3 ([S7]) |
| Sistem sesleriyle birlikte çalışma zorunlu | WASAPI shared (fallback 3) | Exclusive susturur ([S3]) |
| Loopback (sistem sesi kaydı) | WASAPI shared + LOOPBACK | ASIO analogu kanıtlanmadı ([S15]) |

---

## §7 Lisans & Tedarik Riski (CoreMusic için)

| Risk | Detayı | Savunma |
|---|---|---|
| ASIO dual-license seçimi | Proprietary (kapalı kaynak, Steinberg şartları) veya GPLv3 (kaynak açıklama) | Lisans kararı ADR gerektirir (yeni ADR taslağı, `embedded-engineer` + `legal` görüşü) |
| ASIO logo/marka | Ürün adında ASIO kullanılamaz; logo yalnız uygun bağlamda | Kullanım guidelines'a uy (splash/about/web/docs) |
| WASAPI lisans riski | Yok (Windows API) | — |
| Sürüm bağımlılığı | JUCE `WASAPIDeviceMode` sürüm-uyumu repo'da doğrulanmadı (kod yok) | Scaffold'ta JUCE sürüm pin + compile-time static_assert (PLANNED) |

(Kaynak: [S23] lisans/marka bölümleri; JUCE sürümü kanıtlanmadı → ⚠️.)

---

## §8 Bilinmeyenler ve ⚠️ Listesi (bu dosya)

| # | Bilinmeyen | Durum |
|---|---|---|
| Q2 | ASIO'nun resmi/ölçülmüş ms gecikme sayısı | ⚠️ UNKNOWN (Steinberg sayfası bu tur boş döndü; repo sayfası ms vermez) |
| Q10 | ASIO preemption/hot-plug davranışının resmi kaynağı | ⚠️ (yalnız WASAPI exclusive sayfası çekildi) |
| Q11 | ASIO'da loopback eşdeğeri | ⚠️/UNKNOWN — kanıt yok, "yoktur" denmez |
| Q12 | JUCE 9 sürümünde WASAPI event-callback iç detayı | ⚠️ (docs master sayfası çekildi; sürüm-pin doğrulanmadı) |
| Q1 | Windows 11 WASAPI değişikliği | ⚠️ UNKNOWN (anayasa §4) |

---

## §9 Karşılaştırma Dosyası Sonucu (özet tablo)

| Soru | Cevap (kanıtla) |
|---|---|
| Birincil yol ne olmalı? | ASIO (repo kararı, `.ai/AGENTS.md` §17/6; donanım-sürücü varsa) |
| Fallback ne? | WASAPI (exclusive → sharedLowLatency → shared) |
| ASIO ms sayısı var mı? | Hayır, bu turda ⚠️ (uydurulmaz) |
| WASAPI her cihazda çalışır mı? | Evet (shared mode, [S1], [S4]) |
| JUCE tek soyutlamayla ikisini de kapsar mı? | Evet (`WASAPIDeviceMode` + `createAudioIODeviceType_ASIO`, [S20], [S25]) |

---

## § Kaynak Tablosu

| ID | URL | Fetch |
|---|---|---|
| S1 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/wasapi | 2026-10-12 |
| S2 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/about-the-windows-core-audio-apis | 2026-10-12 |
| S3 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams | 2026-10-12 |
| S4 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/user-mode-audio-components | 2026-10-12 |
| S6 | https://learn.microsoft.com/en-us/windows/win32/api/audioclient/nf-audioclient-iaudioclient-initialize | 2026-10-12 |
| S7 | https://learn.microsoft.com/en-us/windows/win32/api/audioclient/nn-audioclient-iaudioclient3 | 2026-10-12 |
| S8 | https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/low-latency-audio | 2026-10-12 |
| S10 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/audio-sessions | 2026-10-12 |
| S14 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/device-formats | 2026-10-12 |
| S15 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/loopback-recording | 2026-10-12 |
| S16 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/recovering-from-an-invalid-device-error | 2026-10-12 |
| S20 | https://docs.juce.com/master/classjuce_1_1AudioIODeviceType.html | 2026-10-12 |
| S21 | https://portaudio.com/docs/v19-doxydocs-dev/pa__win__wasapi_8h_source.html | 2026-10-12 |
| S22 | https://github.com/thestk/rtaudio | 2026-10-12 |
| S23 | https://github.com/audiosdk/asio | 2026-10-12 |
| S25 | https://docs.juce.com/master/namespacejuce.html | 2026-10-12 |

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
**Durum:** IMPLEMENTED (belge) · Kod: ⚠️ PLANNED (cpp=0)
**Sıradaki adım:** `wasapi-dogrulama.md` — iddia·URL·tarih·durum tablosu + açık sorular.
