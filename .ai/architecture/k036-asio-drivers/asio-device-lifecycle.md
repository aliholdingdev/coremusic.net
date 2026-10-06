---
title: "ASIO Cihaz Yaşam Döngüsü"
type: architecture
category: architecture
version: 1.0.0
status: draft
authority: "SSOT — .ai/architecture"
updated: 2026-10-06
---

# K036.4 — ASIO Cihaz Yaşam Döngüsü (Device Lifecycle)

> **Hub:** [[index]] · **Önceki:** [[asio-thread-model]] (K036.5) · **İlgili:** [[asio-exclusive-mode]] (K036.1) · [[asio-hata-yonetimi]] (K036.7)
>
> **Kapsam:** Cihaz açma/kapama/sıfırlama, örnekleme hızı & kanal değişimi, cihaz kaybı durumunda
> WASAPI fallback zinciri, durum makinesi. Kod implementasyonu repo'da YOK (bkz. [[index]] §1.2) —
> bu dosya backup mimari taslağını derinleştirir.

---

## §1 Backup Kanıt Kaynakları (birincil içerik kaynağı)

| # | Backup dosyası | Kanıt satırları | Kullanılan içerik |
|---|---|---|---|
| 1 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/driver-stack-mimari.md` | L148–L210 | `DriverLifecycle` durum makinesi (CREATED→DESTROYED, geçiş tablosu) |
| 2 | `_backup/.../k2-surucu/driver-stack-mimari.md` | L212–L260 | `ErrorChain` — katmanlı hata bildirimi + retry (`ERR_*` kodları 1001–1006) |
| 3 | `_backup/.../k2-surucu/driver-stack-mimari.md` | L116–L133 | `AudioDriverFactory::createAutoDetect` — Windows: ASIO tercihli, WASAPI fallback |
| 4 | `_backup/.../k2-surucu/asio-drivers.md` | L105–L113 | `ASIOError_*` hata durumları ve tepki stratejileri |
| 5 | `_backup/.../k2-surucu/asio-drivers.md` | L139–L158 | `ASIOConfig` kurulum akışı (exclusiveMode = true) |
| 6 | `_backup/.../k2-surucu/index.md` | L52–L53 | Hata toleransı: donanım kopse K3 çökmez, graceful degradation |
| 7 | `_backup/.../k2-surucu/index.md` | L63–L71 | Performans metrikleri (round-trip < 0.5ms ASIO Exclusive hedefi) |
| 8 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` | L79–L87 | Buffer/SR varsayılanları (512/48000/32-bit float/8.1) |
| 9 | `.ai/AGENTS.md` | §17 Edge Case #6 | Cihaz/hat kopması → WASAPI fallback zorunluluğu |
| 10 | `.ai/ecosystem/asio-wasapi-rehber.md` | §5.1 | ASIO yoksa/koptuysa fallback zinciri |

⚠️ VERIFICATION REQUIRED: §3.4'teki `LOST`/`RECOVERING` ara durumları backup durum makinesinde
yoktur; yalnız `ERROR` durumu vardır. `LOST`/`RECOVERING`, `.ai/AGENTS.md` §17 Edge Case #6
gerekliliğinden türetilmiş **planlama katmanıdır** — implementasyon repo'da yoktur.

---

## §2 Durum Makinesi (backup tabanlı)

### §2.1 Backup'taki kanonik geçiş tablosu

Kaynak: `driver-stack-mimari.md` L193–L208.

```
State geçiş izni (backup L195-L202):

  CREATED     ──→ INITIALIZED
  INITIALIZED ──→ CONFIGURED | ERROR
  CONFIGURED  ──→ RUNNING    | ERROR
  RUNNING     ──→ PAUSED | ERROR | DESTROYED
  PAUSED      ──→ RUNNING | DESTROYED
  ERROR       ──→ INITIALIZED | DESTROYED

Geçersiz geçiş → "Invalid state transition" hatası, dönüş false (L167-L170).
```

### §2.2 ASIO'ya uyarlanmış durum diyagramı

```
                    ┌──────────────────────────────────────────────┐
                    │                                              │
                    ▼                                              │
   ┌─────────┐  init()   ┌──────────────┐  configure()  ┌──────────────┐
   │ CREATED │──────────→│ INITIALIZED  │──────────────→│ CONFIGURED   │
   └─────────┘           │ (SDK load)   │               │ (SR/canal/   │
        ▲                └──────┬───────┘               │  buffer set) │
        │                       │ fail                  └──────┬───────┘
        │                       ▼                              │ start()
        │                  ┌────────┐                          ▼
        │                  │ ERROR  │◄──── ASIOError_* ──┌──────────────┐
        │                  └───┬────┘      (L105-L113)   │   RUNNING    │
        │         recover/     │                         │ (callback    │
        │         re-init      │                         │  aktif)      │
        │                      ▼                         └──────┬───────┘
        │              ┌───────────────┐         pause()        │
        └──────────────│  DESTROYED    │◄───────────────────┐   │ stop()
                       │ (lock bırak)  │                    ▼   │
                       └───────────────┘              ┌──────────────┐
                                                      │   PAUSED     │
                                                      └──────────────┘
```

### §2.3 ASIO Exclusive lock ile durum eşlemesi

| Durum | ASIO Exclusive Lock | Kanıt |
|---|---|---|
| CREATED → CONFIGURED | Henüz alınmadı | — |
| CONFIGURED → RUNNING geçişi sırasında | `ASIOExclusiveMode`'da driver başlatılırken lock devreye girer | [[asio-exclusive-mode]] §4 (bkz. o dosya) |
| RUNNING | **Lock SAHİBİ** — tek uygulama | index.md §2 (Sözleşme #2) |
| PAUSED | Lock korunur (akış durur, cihaz hâlâ açık) | ⚠️ VERIFICATION REQUIRED — backup'ta pause↔lock ilişkisi yok |
| ERROR / DESTROYED | Lock bırakılır | [[asio-exclusive-mode]] §5 |

### §2.4 Durum geçiş pseudokodu (backup iskeleti)

Backup `driver-stack-mimari.md` L150–L209'daki `DriverLifecycle::transitionTo` iskeletini
ASYO'ya uyarlayan taslak (repo'da implementasyon YOK):

```
transitionTo(newState):
  1. isValidTransition(current, newState)  — backup L193 tablosu
  2. geçerliyse:
       INITIALIZED → loadDriver()          // SDK + COM init
       CONFIGURED  → applyConfig()         // 512/48000/32f (windows-api.md L81-L87)
       RUNNING     → startStream()         // callback başlar, lock sahiplenilir
       PAUSED      → stopCallbacks()       // lock korunur (⚠️ VERIFICATION REQUIRED)
       ERROR       → handleError()         // asio-drivers.md L105-L113 stratejisi
       DESTROYED   → unloadDriver()        // lock bırakılır
  3. geçersizse → logError("Invalid state transition"), false döndür
```

---

## §3 Cihaz Açma / Kapama / Sıfırlama

### §3.1 Açma (open) akışı

Backup `asio-drivers.md` L139–L158'deki kurulum sırası:

```
1. ASIOConfig oluştur
     config.deviceId   = getDefaultASIODevice()
     config.bufferSize = 512
     config.sampleRate = 48000
     config.exclusiveMode = true
2. driver.init(config)            → INITIALIZED
3. SR/kanal doğrula (canSampleRate) → CONFIGURED
4. driver.start()                 → RUNNING (callback zinciri aktif)
```

Her adımda başarısızlık → `ERROR` durumuna geçiş (§2.1 izni mevcut).

### §3.2 Kapama (close) akışı

```
1. driver.stop()                  → PAUSED   (callback durur)
2. buffer/COM kaynaklarını bırak  → DESTROYED (lock serbest)
```

Kritik sıra: **önce akış durur, sonra lock bırakılır** — aksi halde lock sahibi olmadan
cihaz serbest kalır ve ikinci uygulama sessizce Exclusive'e girer (Sözleşme #2 ihlali).

### §3.3 Sıfırlama (reset)

| Tetik | Backup kanıtı | Tepki |
|---|---|---|
| `ASIOError_HardwareFailure` | asio-drivers.md L111 | K3'ü durdur (doğrudan RUNNING→ERROR) |
| `ASIOError_UnableToStart` | asio-drivers.md L112 | 3 yeniden deneme |
| Buffer hatası | asio-drivers.md L110 | Buffer boyutunu donanım desteklediği sınıra çek |

`ERR_TIMEOUT` / `ERR_BUFFER_OVERFLOW` → retry (driver-stack-mimari.md L255–L258);
diğerleri retry'siz üst katmana bildirilir.

### §3.4 Cihaz kaybı → WASAPI fallback (Edge Case #6)

```
Cihaz koptu / lock beklenmedik kaybedildi
        │
        ▼
ERROR durumu  ── retry başarısız (asio-drivers.md L112: 3 deneme)
        │
        ▼
Graceful degradation (k2 index.md L52-L53: K3 çökmez)
        │
        ▼
WASAPI'ye fallback  ── .ai/AGENTS.md §17 Edge Case #6 (zorunlu)
        │                 kaynak: AudioDriverFactory::createAutoDetect
        │                 "ASIO tercihli, WASAPI fallback"
        │                 (driver-stack-mimari.md L120-L123)
        ▼
K3 sessizce çalışmaya devam eder; kullanıcıya durum bildirilir
```

Fallback zinciri ayrıntısı: `.ai/ecosystem/asio-wasapi-rehber.md` §5.1.
WASAPI tarafı: `[[../k037-wasapi-exclusive/index]]`.

---

## §4 Örnekleme Hızı (SR) Değişimi

| Konu | Değer | Kanıt |
|---|---|---|
| Varsayılan SR | 48000 Hz | windows-api.md L84; brain.md L295 |
| Min / Max SR | 44100 / 192000 | windows-api.md L84 |
| SR değişim callback'i | `sampleRateDidChange(sRate)` | windows-api.md L63–L65 |
| SR doğrulama | `canSampleRate()` / `setSampleRate()` | asio-drivers.md L126–L127 |

Akış (planlanan):

```
sampleRateDidChange(sR) gelir
  → callback RT thread'indedir (bufferSwitch gibi)     [UNKNOWN kesin thread]
  → yeni SR'yi doğrula (canSampleRate)
  → desteklenmiyorsa ERROR → kullanıcıya bildir
  → destekleniyorsa latency yeniden hesapla → [[asio-latency-hesap]] §4
```

⚠️ VERIFICATION REQUIRED: `sampleRateDidChange` anında buffer'ın yeniden kurulup
kurulmadığı backup'ta tanımlanmamıştır (yalnız callback imzası vardır: windows-api.md L62–L65).

## §5 Kanal Sayısı Değişimi

| Konu | Değer | Kanıt |
|---|---|---|
| Varsayılan kanal | 8 (8.1) | windows-api.md L86 |
| Min / Max | 1 / 16 | windows-api.md L86 |
| `setChannelCount` | HAL arayüzünde zorunlu metod | driver-stack-mimari.md L76 |
| `bufferRequest` callback | buffer yeniden talebi (kanal değişimi tetikleyebilir) | windows-api.md L68–L72 |

8.1 kanal mimarisi ayrı dosya konusudur — ilgili katman dosyası diskte yok (planlı D01
modülü); bkz. [[index]] §12 (wiki-link verilmez, kırık link = 0 hedefi).

---

## §6 Performans Hedefleri (backup metrikleri)

Kaynak: `driver-stack-mimari.md` L354–L362 + `k2-surucu/index.md` L63–L71.

| Metrik | Hedef | Kaynak |
|---|---|---|
| Hata kurtarma süresi | < 100ms | driver-stack-mimari.md L360 |
| Driver değişim süresi (fallback dahil) | < 10ms | driver-stack-mimari.md L359 |
| Soyutlama overhead | < 0.01ms | driver-stack-mimari.md L358 |
| Round-trip (ASIO Exclusive) | < 0.5ms | k2 index.md L67 |
| Buffer | 32–64 sample @ 96kHz (backup hedefi) — çelişir: brain.md L295 512 varsayılan @48k | ⚠️ VERIFICATION REQUIRED (bkz. [[asio-latency-hesap]] §6) |

---

## §7 Wiki-Bağlantılar

- Hub: [[index]]
- Exclusive lock sahiplenme: [[asio-exclusive-mode]]
- Callback döngüsü (RUNNING durumundaki çalışır hali): [[asio-buffer-callback]]
- SR değişiminde yeniden hesap: [[asio-latency-hesap]]
- RT thread kuralları: [[asio-thread-model]]
- Hata kodu detayı ve eskalasyon: [[asio-hata-yonetimi]]
- Fallback hedefi: `[[../k037-wasapi-exclusive/index]]`
- macOS karşılaştırma: `[[../k038-core-audio-macos/index]]`

---

## §8 Backup Kanıt Özeti (bu dosyaya ait)

```
backup kanıtı: k2-surucu/driver-stack-mimari.md L148-L210  (durum makinesi)
backup kanıtı: k2-surucu/driver-stack-mimari.md L212-L260  (error chain + retry)
backup kanıtı: k2-surucu/driver-stack-mimari.md L116-L133  (ASIO→WASAPI fallback factory)
backup kanıtı: k2-surucu/driver-stack-mimari.md L354-L362  (performans metrikleri)
backup kanıtı: k2-surucu/asio-drivers.md      L105-L113    (ASIOError_* tepkileri)
backup kanıtı: k2-surucu/asio-drivers.md      L139-L158    (açma akışı / ASIOConfig)
backup kanıtı: k2-surucu/index.md             L52-L53, L63-L71 (hata toleransı, metrikler)
backup kanıtı: k0-isletim-sistemi/windows-api.md L79-L87   (SR/canal/buffer varsayılanları)
kanıt: .ai/AGENTS.md §17 Edge Case #6 (→ WASAPI fallback zorunlu)
kanıt: .ai/ecosystem/asio-wasapi-rehber.md §5.1 (fallback zinciri)
```

**Çelişki raporu:** Backup `k2-surucu/index.md` L68 buffer hedefi "32–64 sample @ 96kHz" der;
`.ai/brain.md` L295 varsayılan "512 sample @ 48kHz" der. Bu dosyada ikisi de taşındı,
biri bastırılmadı — nihai mutabakat için ⚠️ VERIFICATION REQUIRED (ayrıntı: [[asio-latency-hesap]] §6).

---

*Backmatter: `version: 1.0.0` · `status: draft` · `updated: 2026-10-06` · `authority: "SSOT — .ai/architecture"`*
