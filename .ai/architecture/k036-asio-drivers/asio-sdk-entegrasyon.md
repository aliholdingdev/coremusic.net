---
title: "ASIO SDK Entegrasyonu"
type: architecture
category: architecture
version: 1.0.0
status: draft
authority: "SSOT — .ai/architecture"
updated: 2026-10-06
---

# K036.6 — ASIO SDK Entegrasyonu (SDK Yapısı · HAL Zinciri · Lisans)

> **Hub:** [[index]] · **İlgili:** [[asio-buffer-callback]] (K036.2) · [[asio-exclusive-mode]] (K036.1) ·
> [[asio-device-lifecycle]] (K036.4)
>
> **Kapsam:** ASIO SDK 2.3.4 dosya yapısı, callback protokolü, HAL→adapter→OS→driver→hardware
> zinciri, `ASIODriverManager` taslağı, lisans ve 64-bit-only kısıtı.
> ⚠️ Repo'da SDK entegrasyon kodu YOK (bkz. [[index]] §1.2) — tüm sınıf/imza adları
> backup taslaklarından gelir, gerçek SDK imzaları değildir.

---

## §1 Backup Kanıt Kaynakları (birincil içerik kaynağı)

| # | Backup dosyası | Kanıt satırları | Kullanılan içerik |
|---|---|---|---|
| 1 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` | L25–L48 | `ASIOSDK2/` ağaç yapısı (common/host/host/pc/driver/host) |
| 2 | `_backup/.../k0-isletim-sistemi/windows-api.md` | L50–L77 | `ASIOCallbacks` protokolü: `bufferSwitch` · `sampleRateDidChange` · `bufferRequest` · `ioSamplesNeeded` |
| 3 | `_backup/.../k0-isletim-sistemi/windows-api.md` | L79–L87 | Buffer konfigürasyon tablosu (512/48000/32f/8.1, min-max) |
| 4 | `_backup/.../k2-surucu/asio-drivers.md` | L79–L89 | Soyutlama katmanları: ASIO SDK → host → driver → OS → hardware |
| 5 | `_backup/.../k2-surucu/asio-drivers.md` | L115–L158 | `ASIODriverManager` sınıf taslağı + kullanım örneği |
| 6 | `_backup/.../k2-surucu/asio-drivers.md` | L165–L176 | Bağımlılıklar: "ASIO SDK · Dış kütüphane · Steinberg ASIO SDK v2.3+" · Faz 1–5 planı |
| 7 | `_backup/.../k2-surucu/driver-stack-mimari.md` | L50–L134 | `IAudioHAL` arayüzü + `AudioDriverFactory` (create / createAutoDetect) |
| 8 | `_backup/.../k2-surucu/driver-stack-mimari.md` | L16–L48 | Katman diyagramı (K3→HAL→Driver Abstraction→Platform→K1→K0) |
| 9 | `_backup/.../00-enterprise-index.md` | L75, L187 | K2 = ASIO SDK (Steinberg, özel lisans) · "C++ ASIO / düşük gecikme" |
| 10 | `_backup/.../github-referanslari.md` | L146, L201, L205 | "ASIO SDK · Steinberg (özel) · K2 birincil — sürücü katmanının resmi SDK'sı" |
| 11 | `_backup/.../k0-isletim-sistemi/README.md` | L90–L92 | `initializeASIO()` / `shutdownASIO()` statik arayüz |
| 12 | `_backup/.../frontend-restructuring-plan.md` | L139 | "L2.1 ASIO Driver (Steinberg SDK 2.3.4)" |

---

## §2 ASIO SDK 2.3.4 Dosya Yapısı

Kaynak: `windows-api.md` L29–L48 (backup ağacı birebir taşınır):

```
ASIOSDK2/
├── common/
│   ├── asio.h                 # ASIO C definition
│   ├── asiodrivers.h          # Driver management
│   ├── asiolist.h             # Driver enumeration
│   ├── ASIOConvertSamples.h   # Sample conversion
│   └── asio.cpp               # Host interface
├── host/
│   ├── asiodrivers.h/cpp      # Driver instantiation
│   └── ASIOConvertSamples.h/cpp
├── host/pc/
│   ├── asiolist.h/cpp         # COM-based driver list
│   └── ginclude.h             # Platform definitions
├── driver/asiosample/
│   ├── asiosmpl.h/cpp         # Sample driver
│   └── wintimer.cpp           # Buffer switch timer
└── host/sample/
    └── hostsample.cpp         # Host application example
```

Sürüm kanıtları:

- "Steinberg ASIO SDK v2.3+" — `k2-surucu/asio-drivers.md` L169
- "Steinberg SDK 2.3.4" — `frontend-restructuring-plan.md` L139
- Vault: `.ai/CLAUDE.md` L328/L446/L451 (ASIO SDK 2.3.4) · `.ai/brain.md` L64/L69

`COM-based driver list` (L41) → sürücü listesi COM üzerinden taranır; COM init zorunlu:
`CoInitializeEx(COINIT_MULTITHREADED ...)` — `windows-api.md` L242–L245.

---

## §3 ASIO Callback Protokolü

Kaynak: `windows-api.md` L52–L77 (backup taslağı):

```cpp
class ASIOCallbacks {
public:
    // Buffer switch — ana audio callback
    virtual void bufferSwitch(long doubleBufferIndex, ASIOBool directProcess) = 0;

    // Sample rate değişikliği
    virtual void sampleRateDidChange(ASIOSampleRate sRate) = 0;

    // Buffer swap talebi
    virtual void bufferRequest(ASIOBufferInfo* info, int numChannels, long bufferSize) = 0;

    // Mesaj iletimi
    virtual ASIOBool ioSamplesNeeded() = 0;
};
```

⚠️ VERIFICATION REQUIRED: bu sınıf adı/imzaları backup'taki **host taslağıdır**; gerçek ASIO SDK
2.3.4 header'larındaki callback adları (`bufferSwitchTimeInfo` vb.) backup'ta yer almaz —
repo'da SDK header'ı bulunmadığından doğrulama yapılamadı.

Her callback'in RT tarafı ayrıntısı: [[asio-buffer-callback]] (K036.2).
`sampleRateDidChange` yaşam döngüsü tepkisi: [[asio-device-lifecycle]] §4.

### §3.1 Callback ↔ dosya eşlemesi

| Callback | Backup satırı | İşlev | İşleyen topic dosyası |
|---|---|---|---|
| `bufferSwitch` | L57–L60 | Ana ses akışı | [[asio-buffer-callback]] §2 |
| `sampleRateDidChange` | L63–L65 | SR değişimi | [[asio-device-lifecycle]] §4 |
| `bufferRequest` | L68–L72 | Buffer yeniden kurulumu | [[asio-buffer-callback]] §5 |
| `ioSamplesNeeded` | L75 | Akış talebi bayrağı | [[asio-buffer-callback]] §7 |

---

## §4 ASIO Buffer Konfigürasyonu (SDK tarafı)

Kaynak: `windows-api.md` L81–L87 — backup tablosu birebir:

| Parametre | Varsayılan | Min | Max |
|-----------|-----------|-----|-----|
| Buffer Size | 512 samples | 64 | 1024 |
| Sample Rate | 48000 Hz | 44100 | 192000 |
| Bit Depth | 32-bit float | 16 | 32 |
| Channels | 8 (8.1) | 1 | 16 |
| Latency | ~10.67ms | ~1.33ms | ~21.33ms |

Tutarlılık: `.ai/brain.md` L295 (512 / 48kHz / 32-bit float / ~10.67ms) — **aynı değerler**.
Ayrıca `.ai/AGENTS.md` kuralları: 512 buffer varsayılan, 48kHz, 32-bit float (kural #7/#8).

---

## §5 HAL → Adapter → OS → Driver → Hardware Zinciri

### §5.1 Backup soyutlama katmanları

Kaynak: `asio-drivers.md` L81–L89:

| Katman | Rol |
|---|---|
| ASIO SDK | Callback yönetimi, buffer değişimi |
| Host (bizim kod) | Uygulama <-> SDK köprüsü |
| ASIO driver (3. taraf) | Donanıma özel |
| OS (WDM) | İzin/kesme servisleri |
| Hardware | DAC/ADC |

### §5.2 Genişletilmiş katman diyagramı

Kaynak tabanı: `driver-stack-mimari.md` L18–L48 (K3→HAL→ASIO→Platform→K1 WDM→K0 DAC):

```
┌──────────────────────────────────────────────────────────────────┐
│ K3 Neva Engine (platform bağımsız)                                │
│   (Neva Engine katman dosyası diskte yok — planlı D01 modülü)      │
├──────────────────────────────────────────────────────────────────┤
│ K2 HAL Interface — IAudioHAL (driver-stack-mimari.md L54-L82)     │
│   initialize/shutdown · start/stop · read/write · setSampleRate   │
├──────────────────────────────────────────────────────────────────┤
│ K2 Driver Abstraction                                             │
│   ┌─────────┬──────────┬───────┬────────────┐                     │
│   │  ASIO   │  WASAPI  │ ALSA  │ CoreAudio  │  ← factory seçimi   │
│   └────┬────┴──────────┴───────┴────────────┘                     │
├────────┼──────────────────────────────────────────────────────────┤
│ K2 Platform Specific (Windows: COM init, thread öncelik)          │
├────────┼──────────────────────────────────────────────────────────┤
│ K1 OS/Kernel — WDM  (driver-stack-mimari.md L40)                  │
├────────┼──────────────────────────────────────────────────────────┤
│ K0 Hardware — DAC / ADC / DSP / USB  (L44)                        │
└──────────────────────────────────────────────────────────────────┘
```

### §5.3 Fabrika seçimi (ASIO tercihli → WASAPI fallback)

Kaynak: `driver-stack-mimari.md` L116–L133:

```
createAutoDetect(config):
  #ifdef _WIN32
    if (isASIOAvailable()) → create(DRIVER_TYPE_ASIO, config)
    else                   → create(DRIVER_TYPE_WASAPI, config)
```

Aynı zincir `.ai/ecosystem/asio-wasapi-rehber.md` §5.1'de teyitlidir ve `.ai/AGENTS.md`
§17 Edge Case #6 ile zorunludur. Çalışma zamanı geçiş (cihaz kaybı): [[asio-device-lifecycle]] §3.4.

---

## §6 `ASIODriverManager` Taslağı (backup)

Kaynak: `asio-drivers.md` L117–L137 — taslak imzalar (⚠️ gerçek SDK imzaları DEĞİL):

```cpp
class ASIODriverManager {
public:
    bool canSampleRate(ASIOSampleRate rate);
    bool setSampleRate(ASIOSampleRate rate);

    void registerCallback(ASIOCallback* callback);

    // Exclusive mode kontrolü
    bool enableExclusiveMode();
    bool isExclusiveModeActive() const;
    // ... (backup taslağı L117-L137)
};
```

Kullanım örneği (backup L142–L148):

```
ASIODriverManager driver;
config.deviceId      = getDefaultASIODevice();
config.exclusiveMode = true;
```

Yerleşim planı (backup'a göre):

```
ASIODriverManager (taslak)
   │  komutlar: init/start/stop, exclusiveMode
   ▼
ASIO SDK 2.3.4 (common/asio.cpp host interface)   ← dış bağımlılık, lisanslı
   │  COM enumeration (host/pc/asiolist)
   ▼
Windows sürücüsü (K1 WDM)  →  Donanım (K0)
```

Üst katman erişimi: `initializeASIO()` / `shutdownASIO()` — `k0-isletim-sistemi/README.md` L90–L92.

---

## §7 HAL Arayüzü (IAudioHAL) — ASIO adapter'ının uymak zorunda olduğu sözleşme

Kaynak: `driver-stack-mimari.md` L54–L82 (metod listesi özet):

| Grup | Metodlar |
|---|---|
| Yaşam döngüsü | `initialize(config)` · `shutdown()` |
| Playback | `startPlayback()` · `stopPlayback()` · `write()` |
| Capture | `startCapture()` · `stopCapture()` · `read()` |
| Control | `setSampleRate()` · `setBufferSize()` · `setChannelCount()` |
| Durum | `getLatency()` · `isRunning()` · `getDeviceInfo()` |

Adapter eşlemesi: `class ASIODriver : public IAudioHAL` — `driver-stack-mimari.md` L85.

---

## §8 Lisans ve 64-bit-only Kısıtı

| Konu | Kural | Kaynak |
|---|---|---|
| Lisans | Steinberg ASIO SDK **özel lisans** — K2 birincil dış bağımlılık | `00-enterprise-index.md` L187 · `github-referanslari.md` L205 |
| Lisans metni | `.ai/ecosystem/asio-wasapi-rehber.md` §3.1 | vault |
| Mimarî kısıt | **64-bit-only** derleme | `.ai/ecosystem/asio-wasapi-rehber.md` §3.3 |
| Fallback | SDK yoksa / 32-bit hedefte WASAPI | rehber §5.1 · `.ai/AGENTS.md` §17 |

⚠️ VERIFICATION REQUIRED: SDK lisans metninin tam ifadesi ve 32-bit yasağının birebir wording'i
yalnızca `.ai/ecosystem/asio-wasapi-rehber.md` §3.1/§3.3 içeriğindedir; bu dosyada özetlendi —
ikincil alıntı yapılmadı.

---

## §9 Entegrasyon Fazları (backup planı)

Kaynak: `asio-drivers.md` L173–L179:

| Faz | İçerik |
|---|---|
| Faz 1 | ASIO SDK entegrasyonu ve temel callback yapısı |
| Faz 2 | Exclusive mode implementasyonu |
| Faz 3 | Buffer yönetimi (bkz. [[asio-buffer-callback]]) |
| Faz 4 | Latency optimizasyonu (bkz. [[asio-latency-hesap]]) |
| Faz 5 | Hata yönetimi + WASAPI fallback (bkz. [[asio-hata-yonetimi]]) |

Sürücü öncelik sırası (backup `k2-surucu/index.md` L91): "Önce ALSA ve PipeWire, ardından
WASAPI ve ASIO, en sonda CoreAudio".

---

## §10 Wiki-Bağlantılar

- Hub: [[index]]
- Callback detayı: [[asio-buffer-callback]]
- Exclusive mode: [[asio-exclusive-mode]]
- Latency: [[asio-latency-hesap]]
- Yaşam döngüsü: [[asio-device-lifecycle]]
- Thread: [[asio-thread-model]]
- Hata: [[asio-hata-yonetimi]]
- WASAPI fallback hedefi: `[[../k037-wasapi-exclusive/index]]`
- macOS CoreAudio: `[[../k038-core-audio-macos/index]]`

---

## §11 Backup Kanıt Özeti (bu dosyaya ait)

```
backup kanıtı: k0-isletim-sistemi/windows-api.md L25-L48   (ASIOSDK2/ ağacı)
backup kanıtı: k0-isletim-sistemi/windows-api.md L50-L77   (ASIOCallbacks protokolü)
backup kanıtı: k0-isletim-sistemi/windows-api.md L79-L87   (buffer konfigürasyon tablosu)
backup kanıtı: k0-isletim-sistemi/windows-api.md L238-L249 (COM init)
backup kanıtı: k0-isletim-sistemi/README.md     L90-L92    (initializeASIO/shutdownASIO)
backup kanıtı: k2-surucu/asio-drivers.md        L79-L89    (soyutlama katmanları)
backup kanıtı: k2-surucu/asio-drivers.md        L115-L158  (ASIODriverManager taslağı + kullanım)
backup kanıtı: k2-surucu/asio-drivers.md        L165-L176  (bağımlılıklar + faz planı)
backup kanıtı: k2-surucu/driver-stack-mimari.md L16-L48    (katman diyagramı)
backup kanıtı: k2-surucu/driver-stack-mimari.md L50-L134   (IAudioHAL + factory + fallback)
backup kanıtı: 00-enterprise-index.md           L75, L187  (K2 = ASIO SDK özel lisans)
backup kanıtı: github-referanslari.md           L146, L201, L205 (SDK lisans tablosu)
backup kanıtı: frontend-restructuring-plan.md   L139       (Steinberg SDK 2.3.4)
kanıt: .ai/CLAUDE.md L328/L446/L451 · .ai/brain.md L64/L69 (SDK 2.3.4)
kanıt: .ai/ecosystem/asio-wasapi-rehber.md §3.1 (lisans) · §3.3 (64-bit-only) · §5.1 (fallback)
```

**Çelişki raporu:** backup içi çelişki YOK — `asio-drivers.md` L169 "v2.3+" ile
`frontend-restructuring-plan.md` L139 "2.3.4" uyumlu (2.3.4 ≥ 2.3+). Vault (`.ai/CLAUDE.md`)
2.3.4'e sabitler.

---

*Backmatter: `version: 1.0.0` · `status: draft` · `updated: 2026-10-06` · `authority: "SSOT — .ai/architecture"`*
