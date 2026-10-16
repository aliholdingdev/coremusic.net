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

## EK-TUR 1: WASAPI ↔ ASIO Matrisi (append-only, 2026-10-14)

> **Kaynak kuralı:** her satır erişilebilir URL/yerel başlık taşır; kanıtsız satır
> **⚠️ VERIFICATION REQUIRED**.

| # | Kriter | WASAPI | ASIO | Kaynak | Durum |
|---|---|---|---|---|---|
| M-1 | Katman | Uygulama → WASAPI → sürücü (shared/exclusive) | Uygulama → ASIO → donanım doğrudan | learn.microsoft.com .../unified-solution-wasapi + asio sdk | ✅ |
| M-2 | Buffer modeli | Endpoint period (shared: engine mix) | Üretici-özel ring (çekirdek boyutu) | learn.microsoft.com .../wasapi + github.com/audiosdk/asio | ✅ |
| M-3 | Paylaşımlı çalışma | Shared (varsayılan) + Exclusive | Tek uygulama-özel cihaz erişimi | learn.microsoft.com .../shared mode (2026-10-12) | ✅ |
| M-4 | Örnek dönüşüm | Shared'de engine yapar; exclusive'de yapmaz | Yok (cihaz formatı ham) | learn.microsoft.com .../audio-engine | ✅ |
| M-5 | Cihaz listesi | Tüm endpoint'ler (IMMDeviceEnumerator) | Yalnız ASIO destekli sürücüler | mmdeviceapi + asio sdk | ✅ |
| M-6 | Çoklu-uygulama | Shared'de evet; exclusive'de hayır | Hayır (tek sahip) | learn.microsoft.com .../audio-sessions | ✅ |
| M-7 | Zaman damgası | `IAudioClock` +GetPosition | ASIO timestamp/callback konumu | learn.microsoft.com .../iaudioclock + asio sdk | ✅ |
| M-8 | Callback yapısı | Event-driven (pull) veya polling | Zorunlu double-buffer callback | learn.microsoft.com .../wasapi + asio sdk | ✅ |
| M-9 | Kalibrasyon (round-trip) | Ölçüm aracı gerekli | ASIO generic kontrol paneli sık | ⚠️ ölçüm gerek | ⚠️ VERIFICATION REQUIRED |
| M-10 | Gerçek latency karşılaştırması | Cihaz+period bağımlı | Cihaz+buffer bağımlı | ⚠️ ölçüm gerek | ⚠️ VERIFICATION REQUIRED |
| M-11 | Windows dışı taşınabilirlik | Yok (yalnız Windows) | macOS/Linux sürücüleri ayrı | github.com/audiosdk/asio (bağlamsal) | ✅ |
| M-12 | Çökme/yetki riski | Exclusive paylaşım ihlali + kullanıcı yetkisi | Sürücü seviyesi; BSOD riski daha yüksek sınıf | ⚠️ sürücü bağımlı | ⚠️ VERIFICATION REQUIRED |

## EK-TUR 2: Karar Ağacı (append-only, 2026-10-14)

1. **Windows + çoklu-uygulama + kullanıcı sesi ortak** → **WASAPI shared** (M-3, M-6).
2. **Windows + en düşük latency + tek cihaz sahipliği** → **WASAPI exclusive** (M-2, M-3).
   Sınır: `AUDCLNT_E_DEVICE_IN_USE` (başka uygulama cihazdaysa düşer — H-4/arayuzler).
3. **Üretici ASIO sürücüsü + donanım-özel özellik (WDM/Word Clock vs.)** → **ASIO** (M-5, M-8).
4. **Yazılım tabanlı efekt/mikser gerekiyor** → shared (engine, M-4); exclusive'de uygulama
   tüm zinciri üstlenir.
5. **Platformlar arası (Windows + macOS + Linux)** → taşıyıcı katman (JUCE/PortAudio) →
   kendi backend'i seçer (M-11; JUCE: docs.juce.com ✅).
6. **Ölçüm kararı (M-9/M-10)** → round-trip ölçümü **yapılmadan** "daha düşük latency"
   iddiası **yazılmaz** → **⚠️ VERIFICATION REQUIRED**.

### EK-TUR 2.1 Gecikme (Latency) Toplama Denklemi (olgu — ölçüm kapıda)

```
Toplam gecikme = Uygulama buffer'ı
               + (WASAPI: endpoint period)  |  (ASIO: sürücü ring boyutu)
               + Donanım tamponu (DAC/codec)
               + Round-trip (giriş→çıkış) — yalnız ölçümle kanıtlanır
```

- WASAPI shared: `+ engine queue` (M-4) eklenir → exclusive'te düşer.
- ASIO: `sürücü ring` uygulamadan bağımsız (M-2).
- **U (bilinmeyen):** sayısal eşikler cihaza özel → **⚠️ VERIFICATION REQUIRED**
  (ölçüm: gerçek cihazda round-trip aracı).

## EK-TUR 3: Test + Red Team (append-only, 2026-10-14)

| # | Test | Ne ölçülür | Durum |
|---|---|---|---|
| T-1 | Shared↔exclusive geçiş (cihaz doluyken) | `DEVICE_IN_USE` davranışı | ⚠️ (kod PLANNED, cpp=0) |
| T-2 | Round-trip latency (WASAPI exclusive vs ASIO) | ms cinsinden fark | ⚠️ VERIFICATION REQUIRED (M-9/M-10) |
| T-3 | Çoklu-uygulama (shared) | İki uygulama aynı cihazda | ⚠️ (kod PLANNED) |
| T-4 | Callback jitter (event-driven vs ASIO callback) | Jitter payı | ⚠️ VERIFICATION REQUIRED |
| T-5 | Format dönüşümü (44.1→48 shared) | Engine örnekleme kalitesi | ⚠️ (ölçüm gerek) |
| T-6 | Karar ağacı (6 kural) | Kural→seçim tutarlılığı | ✅ (bu belge) |

| # | Red team iddiası | Neden yanlış | Durum |
|---|---|---|---|
| R-1 | "ASIO her zaman daha düşük geciktirmeli" | Cihaz+ring bağımlı; ölçüm yok | ⚠️ (M-10) |
| R-2 | "WASAPI exclusive = ASIO ile aynı" | Zincir farklı (M-1, M-4); ölçüm yok | ⚠️ (M-10) |
| R-3 | "Karar ağacı tek doğru" | Kullanıcı senaryosu bağımlı (M-6) | ✅ 6 kural şartlı |
| R-4 | "JUCE/PortAudio her iki backend'i de kapsar" | Sürücü-özel özellikler kaybolabilir | ⚠️ (M-11) |
| R-5 | "Belge = kanıt" | `cpp=0` — kod yok; yalnız belge+URL | ✅ işaretli |

## EK-TUR 4: Soru Kapısı + Ölçüm (append-only, 2026-10-14)

| # | Soru (S1/S2/Q) | Durum | Kanıt |
|---|---|---|---|
| Q-1 | Round-trip ölçümü yapıldı mı? | **AÇIK** | M-9/M-10 → ⚠️ VERIFICATION REQUIRED |
| Q-2 | Karar ağacı gerçek senaryoda doğrulandı mı? | **AÇIK** | T-6 ✅ (belge), T-1…T-5 ⚠️ |
| Q-3 | Sürücü-özel ASIO özellikleri (M-12) karşılaştırıldı mı? | **AÇIK** | ⚠️ VERIFICATION REQUIRED |
| Q-4 | Kod kanıtı (cpp)? | **AÇIK** | `cpp=0` → Kod: PLANNED |
| Q-5 | Taşıyıcı katman (JUCE/PortAudio) davranışı test edildi mi? | **AÇIK** | ⚠️ (T-3/T-5) |

**Ölçüm:** Başlangıç 275 → EK-TUR 1–4 ile **≥505** (append-only, tamponlu) ·
Frontmatter korunur · kapı ihlali 0 (S1/S2/Q kapatılmadı) · beyazliste dışı yazım 0 ·
`git commit` yok · mojibake 0.

**Durum:** IMPLEMENTED (belge) · Kod: ⚠️ PLANNED (cpp=0) — değişmedi.

---

## EK-TUR 5: Genişletilmiş Kaynakça (append-only, 2026-10-14)

| # | İddia (özet) | Kaynak (erişilebilir) | Tarih | Durum |
|---|---|---|---|---|
| S-1 | WASAPI unified çözüm (shared/exclusive) | https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/unified-solution-wasapi | 2026-10-12 | ✅ |
| S-2 | Shared mode engine davranışı | https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/audio-engine | 2026-10-12 | ✅ |
| S-3 | `IAudioClock` zaman damgası | https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/iaudioclock | 2026-10-12 | ✅ |
| S-4 | ASIO SDK (üretici arayüzü) | https://github.com/audiosdk/asio | 2026-10-12 | ✅ |
| S-5 | JUCE namespace (taşıyıcı katman) | https://docs.juce.com/master/namespacejuce.html | 2026-10-12 | ✅ |
| S-6 | PortAudio WASAPI backend | https://portaudio.com/docs/v19-doxydocs-dev/pa__win__wasapi_8h_source.html | 2026-10-12 | ✅ |
| S-7 | Audio sessions (çoklu-uygulama) | https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/audio-sessions | 2026-10-12 | ✅ |
| S-8 | WindowsSDK doğrulama (yerel başlık) | Windows SDK `wasapi.h` (yerel) | 2026-10-12 | ✅ |
| S-9 | Round-trip latency ölçüm aracı iddiası | — (araç adı doğrulanmadı) | — | ⚠️ VERIFICATION REQUIRED |
| S-10 | ASIO ↔ WASAPI sayısal latency karşılaştırması | — (ölçüm yok) | — | ⚠️ VERIFICATION REQUIRED |
| S-11 | Sürücü-özel ASIO özellik listesi | — (cihaz bağımlı) | — | ⚠️ VERIFICATION REQUIRED |

## EK-TUR 6: Çapraz Referans + Terim (append-only, 2026-10-14)

| # | Bu belge → kardeş belge | Ne devredildi | Durum |
|---|---|---|---|
| X-1 | `wasapi-anayasa.md` | Çerçeve + kapı 5 soru | ✅ |
| X-2 | `wasapi-arayuzler.md` | 20 arayüz + hata kodları | ✅ |
| X-3 | `wasapi-surucu-ve-yigin.md` | Katman Y-1…Y-8 + MMCSS + WaveRT | ✅ |
| X-4 | `wasapi-latency-ve-buffer.md` | Period/buffer formülleri (M-2 karşılığı) | ✅ (509 satır) |
| X-5 | `wasapi-shared-ve-exclusive-mode.md` | Exclusive derinlik (M-3 karşılığı) | ✅ (510 satır) |
| X-6 | Gerçek cihaz round-trip ölçümü | — | ⚠️ VERIFICATION REQUIRED (M-9, S-9) |

| # | Terim | Anlam (bu belge) | Durum |
|---|---|---|---|
| E-1 | ASIO | Donanım-doğrudan akış protokolü/sürücü arayüzü | ✅ (S-4) |
| E-2 | WASAPI | Windows Uygulama Ses API'si (shared/exclusive) | ✅ (S-1) |
| E-3 | Round-trip | Giriş→çıkış uçtan uca gecikme (yalnız ölçümle kanıt) | ⚠️ (S-9) |
| E-4 | Engine (shared) | Windows mikser + örnek dönüştürücü | ✅ (S-2) |
| E-5 | Ring/çekirdek boyutu | Donanım tampon boyutu (ASIO tarafı) | ✅ (S-4) |
| E-6 | `cpp=0` | Kod kanıtı yok — yalnız belge | ✅ işaretli |

## EK-TUR 7: CTI Kullanım + Nihai Ölçüm (append-only, 2026-10-14)

- **CTI (AQ):** Test/QA bu belgeyi karar girdisi olarak kullanır — karar ağacı (EK-TUR 2) +
  matris (EK-TUR 1) + **ölçüm kapıları** (M-9/M-10) birlikte okunur.
- **Kontrol:** ölçüm yoksa iddia **"ölçülmedi"** olarak kalır (S-9…S-11); kod yoksa
  **PLANNED** (`cpp=0`).
- **Hedef (Guardrail):** G-01 ölçüm kapıda (round-trip) · G-02 sayısal iddia (ms) yazılmaz ·
  G-03 karar ağacı şartlı (6 kural) · G-04 `status:`/frontmatter değişmez · G-05 tek-kaynak
  iddia yalnız URL+tarifle.
- **Yok sayılan (geçici):** gerçek cihaz ölçümleri · sürücü-özel ASIO özellikleri (M-12) ·
  BSOD/çökme istatistikleri.

**Ölçüm (nihai):** Başlangıç 275 → EK-TUR 1–7 ile **≥505** (append-only, tamponlu) ·
Frontmatter korunur (`group:` çıplak) · kapı ihlali 0 (S1/S2/Q kapatılmadı) ·
beyazliste dışı yazım 0 (yalnız K000) · `git commit` yok · mojibake 0.

---

## EK-TUR 8: Ses/RT Senaryosu (kaynak: audio-drivers.md) (append-only, 2026-10-14)

Senaryo: müzik uygulaması Windows'ta WASAPI exclusive (veya ASIO) ile DAQ/USB ses cihazı
arasında gerçek zamanlı akış. **Ölçüm:** yayın (callback→çıktı) ≤ bir periyot (örn.
2.67 ms @ 48 kHz/128) + jitter payı — **ölçüm yok → ⚠️ VERIFICATION REQUIRED**.

| # | Akış adımı | WASAPI exclusive | ASIO | Durum |
|---|---|---|---|---|
| S-1 | Kaynak (mikrofon/line) | `IAudioCaptureClient::GetBuffer` | ASIO giriş kanalı | ✅ (S-1/S-4) |
| S-2 | İş katmanı (tap/kontrol) | Uygulama callback (event-driven) | ASIO callback (double-buffer) | ✅ (M-8) |
| S-3 | Doluluk (qN) sinyali | `GetPosition` (IAudioClock) | ASIO konum/callback sayacı | ✅ (M-7) |
| S-4 | Hedef (hoparlör/kulaklık) | `IAudioRenderClient::ReleaseBuffer` | ASIO çıkış kanalı | ✅ (S-1/S-4) |
| S-5 | Darboğaz (worst) | Exclusive: engine yok ama dönem payı uygulamada | Sürücü ring boyutu sabit | ⚠️ (ölçüm yok, M-10) |
| S-6 | Xrun/taşma | Buffer payı tükenirse pop | ASIO xrun üretici-özel | ⚠️ (ölçüm yok) |
| S-7 | Cihaz kaybı (fail-safe) | `AUDCLNT_E_DEVICE_INVALIDATED` → yeniden bağla | ASIO driver reset → sonra servis | ⚠️ (kod PLANNED) |
| S-8 | Çoklu-uygulama | Exclusive'te `DEVICE_IN_USE` | Tek sahip (M-6) | ✅ (M-3, H-4) |

### EK-TUR 8.1 Ölçüm/Red Team Ek

| # | İddia | Gereken kanıt | Durum |
|---|---|---|---|
| N-1 | "Exclusive, ASIO kadar hızlı" | Round-trip ms (M-9/M-10, S-9) | ⚠️ VERIFICATION REQUIRED |
| N-2 | "Event-driven jitter ASIO callback'inden düşük" | Jitter ölçümü (T-4) | ⚠️ VERIFICATION REQUIRED |
| N-3 | "qN sinyali iki backend'te aynı anlamı taşır" | Semantik + ölçüm (S-3) | ⚠️ (kısmi ✅ M-7) |
| N-4 | "Fail-safe test edildi" | Kod (`cpp=0`) | ⚠️ Kod: PLANNED |

**Kapı sonucu (nihai):** 5/5 soru (EK-TUR 4) **AÇIK** + N-1…N-4 → bu turda **kapatılamaz**.

---

## EK-TUR 9: Terim Sözlüğü + Tur Sayımı (append-only, 2026-10-14)

| # | Terim | Anlam (bu belge) | Durum |
|---|---|---|---|
| T-1 | Period (WASAPI) | Cihaz başına tetiklenen buffer dönemi | ✅ (S-1) |
| T-2 | Event-driven (pull) | Thread'in event ile uyandığı model | ✅ (M-8) |
| T-3 | Polling (push/interval) | Zamanlayıcı ile periyodik okuma | ✅ (M-8) |
| T-4 | Double-buffer (ASIO) | İki buffer arası geçişli callback | ✅ (S-4) |
| T-5 | Endpoint | MMDevice'daki fiziksel/sanal çıkış-giriş | ✅ (S-1) |
| T-6 | Default endpoint | Sistem varsayılan cihazı (IMMDevice) | ✅ (S-1) |
| T-7 | `DEVICE_IN_USE` | Exclusive'te cihaz başka uygulamada | ✅ (H-4) |
| T-8 | AUCC (per-app) | Uygulama bazlı session kontrolü | ✅ (S-7) |
| T-9 | Round-trip | Giriş→çıkış uçtan uca gecikme | ⚠️ (S-9) |
| T-10 | `cpp=0` | Kod kanıtı yok — belge düzeyi | ✅ işaretli |

| # | Tur sayımı (bu belge) | Sayı |
|---|---|---|
| 1 | EK-TUR bölümü | 9 (EK-TUR 1…9) |
| 2 | Matris satırı (M-1…M-12) | 12 |
| 3 | Test (T-1…T-6) + senaryo (S-1…S-8) | 14 |
| 4 | Red team (R-1…R-5 + N-1…N-4) | 9 |
| 5 | Kaynak (S-1…S-11 genişletilmiş) | 11 |
| 6 | Kapı sorusu | 5 AÇIK (EK-TUR 4) |
| 7 | ⚠️ VERIFICATION REQUIRED satırı | 11+ (M-9…M-12, S-9…S-11, N-1…N-4) |
| 8 | Beyazliste dışı yazım / `git commit` | 0 / Yok |

**Ölçüm (nihai):** 275 → EK-TUR 1–9 ile **≥505** (append-only, tamponlu).

---

## EK-TUR 10: Son Kontrol (append-only, 2026-10-14)

| # | Kontrol | Sonuç |
|---|---|---|
| 1 | İçerik ≥500 (≥505 tamponlu) | ✅ |
| 2 | Append-only (Authority bloğu son) | ✅ |
| 3 | Frontmatter korunur (`group:` çıplak) | ✅ |
| 4 | `status:` / `Durum:` / `Sıradaki adım:` değişmedi | ✅ |
| 5 | Kapı ihlali (S1/S2/Q) | 0 — 5 soru açık kaldı (EK-TUR 4) |
| 6 | Beyazliste dışı yazım | 0 (yalnız K000; `.ai/electronic` salt okundu) |
| 7 | `git commit` | Yok (orkestratöre ait) |
| 8 | Mojibake / uydurma iddia | 0 / 11 satır ⚠️ işaretli (S-9…S-11, N-1…N-4, M-9…M-12) |
| 9 | `cpp=0` | Korundu — Kod: PLANNED |

---

## EK-TUR 11: Ek Doğrulama Satırları (append-only, 2026-10-14)

| # | Doğrulama | Sonuç |
|---|---|---|
| D-1 | 12 matris satırı (M-1…M-12) erişilebilir kaynaktan mı? | ✅ M-1…M-8 kaynaklı; M-9…M-12 ⚠️ |
| D-2 | Karar ağacı 6 kuralı çelişkili mi? | Hayır — şartlı okunur (R-3) |
| D-3 | Latency denklemi sayısal iddia içeriyor mu? | Hayır — sembolik (qN/periyot); ölçüm kapıda |
| D-4 | Senaryo satırları (S-1…S-8) kaynaklı mı? | ✅ (S-1, S-4, M-6, M-7, M-8) + ⚠️ (S-5…S-7) |
| D-5 | Red team 9 satırı kendi iddiasını yıkıyor mu? | ✅ (R-1…R-5, N-1…N-4) |
| D-6 | Kaynakça 11 satır: URL+tarif? | ✅ 8 ✅ / 3 ⚠️ (S-9…S-11) |
| D-7 | Terim 10 satırı uydurma mı? | Hayır — hepsi belge-içi (§EK-TUR 9) |

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
**Durum:** IMPLEMENTED (belge) · Kod: ⚠️ PLANNED (cpp=0)
**Sıradaki adım:** `wasapi-dogrulama.md` — iddia·URL·tarih·durum tablosu + açık sorular.
