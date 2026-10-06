---
title: "ASIO Çekirdek Entegrasyonu — Callback Yaşam Döngüsü, Hard-RT Kısıtları ve Fallback"
type: architecture
category: d01-os-donanim-surucu
version: 4.0.0
status: active
authority: "K000 Windows Core — SSOT: .ai/architecture/k000-windows-core/"
updated: 2026-10-06
---

# ASIO Çekirdek Entegrasyonu — Callback Yaşam Döngüsü, Hard-RT Kısıtları ve Fallback

> **K numarası:** K000 — Windows Core. Bu belge ASIO'nun CoreMusic **K0 (platform) +
> K2 (sürücü)** sınırındaki entegrasyonunu anlatır. Depoda `*.cpp` / `*.h` sayısı
> **0**'dır (ADR-017 §1.1-B kanıtı) → tüm kod blokları **kavramsal iskelettir**.
> Ölçülmüş round-trip/latency rakamı bu depoda **üretilmez**; sayısal satırlar
> kaynak belgeden **olduğu gibi** alınır ve etiketlenir.

---

## §1 Kapsam ve Bağlam

### §1.1 Dosya İlişkileri

| Dosya | İlişki |
|---|---|
| [[index]] | K000 klasör indeksi — bu dosyanın üst dizini |
| [[windows-api-yuzeyi]] | Win32/COM yüzeyi — `CreateThread`, `SetThreadPriority`, `CoInitializeEx` |
| [[win32-olay-dongusu-ve-mesaj-kuyrugu]] | Olay döngüsü — ASIO callback'i mesaj kuyruğundan **bağımsızdır** (§9.4) |
| [[wasapi-ses-yolu-cekirdek]] | Fallback zincirinin 2. ve 3. halkası |
| [[windows-guvenlik-ve-olcullu-kisitlar]] | RT öncelik/affinity izinleri, token kısıtları |
| [[windows-performans-ve-gozlemlenebilirlik]] | xrun sayacı, ölçüm kapısı |

### §1.2 Kapsam Sınırı

| Kapsar | Kapsamaz |
|---|---|
| ASIO callback sözleşmesi (`bufferSwitch`) | **K1 Donanım** — DAC/ADC, kart devresi → `[KAPSAM DIŞI]` |
| ASIO SDK ağacı + lisans gate (K0) | **K3** DSP algoritmaları → `[KAPSAM DIŞI]` |
| Hard-RT kısıtları (tahsisat/kilit/I/O yasağı) | JUCE/plugin ADR kararı → `[[ADR-017-dsp-hardware-mode]]` |
| Buffer boyutu + latency **bütçesi** (hedef) | Gerçek latency **ölçümü** → `[NOT PROVIDED]` |
| ASIO → WASAPI → Null fallback zinciri | macOS CoreAudio / Linux ALSA → `[KAPSAM DIŞI]` (ADR-019) |

---

## §2 Gömülü Kaynaklar

| # | Dosya | Bu belgede kullanımı |
|---|---|---|
| 1 | `_backup\arch-2026-10-06_1057\architecture\k0-isletim-sistemi\windows-api.md` | §1 ASIO SDK Entegrasyonu (L25–L84) · §3 Threading (L155–L185) · §5 COM (L240–L249) |
| 2 | `_backup\arch-2026-10-06_1057\architecture\k0-isletim-sistemi\README.md` | `initializeASIO`/`shutdownASIO` (L90–L92) · öncelik tablosu (L242) · ASIO Zamanlama (L359–L363) |
| 3 | `_backup\arch-2026-10-06_1057\architecture\k2-surucu\asio-drivers.md` | ASIO mimarisi, exclusive mod, buffer yönetimi, callback zinciri, hata yönetimi |
| 4 | `_backup\arch-2026-10-06_1057\architecture\katman-baglilik-matrisi.md` | K0 kök · K2 → K1 · K3 → K2 · yasaklar |
| 5 | `.ai\.decisions\accepted\ADR-017-dsp-hardware-mode.md` | Hard-RT kısıtları, buffer bütçesi, xrun politikası, sahiplik |
| 6 | `.ai\.decisions\accepted\ADR-019-per-os-neva-player.md` | `IAudioBackend` adapter sözleşmesi, backend seçim sırası |
| 7 | `.ai\ecosystem\asio-wasapi-rehber.md` | ASIO SDK açık kaynak lisansı (§3.1, §5.2), callback dersi, 64-bit kısıtı (§3.3) |
| 8 | `.ai\AGENTS.md` | §17 madde 6 · §10.1 eskalasyon · §5 domain boundary (`*.cpp → Embedded`) |
| 9 | `.ai\brain.md` | K2 tanımı (L229) · fallback zinciri (L862/L873) · RT yasakları (§7.1) |
| 10 | `.ai\CLAUDE.md` | Latency bütçesi (L429) · Tier 1 platform (L347) |

---

## §3 K0 ↔ K2 ↔ K3 Konumu (Matris Kanıtlı)

```text
K1 Donanım ←── K2 Sürücü (ASIO callback bu katmanda) ←── K3 Ses Motoru
                   ↑
              K0 platform kökü (SDK bağımlılığı, thread/COM/bellek API'leri)
```

| İddia | Etiket |
|---|---|
| ASIO bir **sürücü protokolüdür**; K2 kapsamındadır | **MEVCUT PROJE GERÇEĞİ** (`asio-drivers.md` L12) |
| K2 → K1 bağımlılığı vardır | **MEVCUT PROJE GERÇEĞİ** (matris) |
| K3 → K2 bağımlıdır; callback K3'e teslim eder | **MEVCUT PROJE GERÇEĞİ** (`asio-drivers.md` L68–L72) |
| ASIO SDK, K0 **derleme bağımlılığıdır** | **MEVCUT PROJE GERÇEĞİ** (`asio-wasapi-rehber.md` §3.7.1) |
| K6 → K0 ve K12 → K0 bağımlılıkları **yasaktır** | **MEVCUT PROJE GERÇEĞİ** (matris) |

---

## §4 ASIO Yolu — Neden Windows Ses Altyapısını Bypass Eder

`asio-drivers.md` L18–L22 (MEVCUT PROJE GERÇEĞİ):

| İddia | Kaynak satır |
|---|---|
| ASIO, WDM/MME/DirectSound'u **tamamen atlar**, doğrudan veri yolu kurar | L18 |
| Kernel geçişleri minimize edilir; kopyalama **yalnız bir kez** | L20 |
| Buffer boyutu uygulama tarafından kontrol edilir — **32 sample'a kadar** | L21 |
| Interrupt-driven processing — donanım kesmesi ile tetiklenir | L22 |

**Karşı taraf (OS yüzeyi):** `.ai/CLAUDE.md` L347 Tier 1 = `Windows (XP-11, Server
2012 R2+)` · `README.md` L45 aynı satır → ASIO hedefi bu tier'da taşınır.
*(Sürüm aralığı çelişkisi → `## ÇELİŞKİ / DOĞRULAMA` maddesi, bu belgenin 2 numaralı
kaydı değil; sürüm çelişkisi [[wasapi-ses-yolu-cekirdek]] dosyasındadır.)*

---

## §5 ASIO SDK Yapısı (K0 Bağımlılığı)

`windows-api.md` §1.1 (MEVCUT PROJE GERÇEĞİ — ağaç birebir):

```text
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

### §5.1 Bağımlılık Kaydı

```text
bağımlılık: ASIO SDK
  kaynak: resmi ASIO SDK (açık kaynak lisans — exa 2026-09-24)
  sürüm: [UNKNOWN]  ← belirsizlik: §ÇELİŞKİ maddesi 1
  katman: K0 (derleme) → K2 (sözleşme) kullanır
  çıkış: LICENSE metni pakete kopyalanır (asio-wasapi-rehber §5.2 madde 3)
  yasak: SDK içine AGPL/PCM5122 izi karıştırma
```

### §5.2 Sürüm Belirsizliği Özeti

| Kaynak | İddia |
|---|---|
| `_backup\...\k0-isletim-sistemi\windows-api.md` L27 | `### 1.1 ASIO 2.3 SDK Yapısı` |
| `_backup\...\k0-isletim-sistemi\README.md` L630 | `K0.8.1.7 | ASIO 2.3 SDK Yapısı | windows-api.md L27` |
| `.ai\AGENTS.md` §4 ve §15 (Embedded Engineer satırı) | `ASIO SDK 2.3.4` |
| `.ai\.decisions\accepted\ADR-017-...md` L128 | `ASIO SDK 2.3.4 (bufferSwitch, asioDriver)` |

→ Bu belgede sürüm **yazılmaz**; `## ÇELİŞKİ / DOĞRULAMA` maddesi 1'e taşınır.

### §5.3 Lisans Gate (K0 — Release)

`asio-wasapi-rehber.md` §5.2 kontrol listesi (MEVCUT PROJE GERÇEĞİ):

```text
[ ] LICENSE metni repoda saklandı mı (kaynak + atıf)?
[ ] ASIO SDK dosyaları modifiye edildiyse lisans metni korundu mu?
[ ] Yürütülebilir dağıtıma SDK atıf/zorunlu metni eklendi mi?
[ ] Değiştirilen SDK kaynakları lisans uyumlu şekilde yayınlandı mı?
[ ] 3. taraf ASIO sürücü kodu SDK'ye karıştırılmadı mı?
[ ] AGPL kaynak izi yok — sweep yapıldı mı?
[ ] PCM5122 izi yok?
Sonuç: hepsi [x] → release geçer; [ ] var → release BLOKE
```

---

## §6 Callback Protokolü

### §6.1 `ASIOCallbacks` (Kaynak: `windows-api.md` §1.2 — birebir)

```cpp
// KAVRAMSAL İSKELET — kaynak: windows-api.md §1.2
// Depoda derlenebilir ASIO kodu YOKTUR.
class ASIOCallbacks {
public:
    // Buffer switch — ana audio callback
    virtual void bufferSwitch(
        long doubleBufferIndex,
        ASIOBool directProcess
    ) = 0;

    // Sample rate değişikliği
    virtual void sampleRateDidChange(
        ASIOSampleRate sRate
    ) = 0;

    // Buffer swap talebi
    virtual void bufferRequest(
        ASIOBufferInfo* info,
        int numChannels,
        long bufferSize
    ) = 0;

    // Mesaj iletimi
    virtual ASIOBool ioSamplesNeeded() = 0;
};
```

### §6.2 Üyeler Tablosu

| Üye | Rol | Etiket |
|---|---|---|
| `bufferSwitch(long, ASIOBool)` | Ana RT callback — buffer değişimi | **MEVCUT PROJE GERÇEĞİ** |
| `sampleRateDidChange(ASIOSampleRate)` | Örnekleme hızı değişimi | **MEVCUT PROJE GERÇEĞİ** |
| `bufferRequest(ASIOBufferInfo*, int, long)` | Buffer swap talebi | **MEVCUT PROJE GERÇEĞİ** |
| `ioSamplesNeeded()` | Mesaj iletimi | **MEVCUT PROJE GERÇEĞİ** |
| `wintimer.cpp` → "Buffer switch timer" | Zamanlayıcı kaynağı | **MEVCUT PROJE GERÇEĞİ** (§5.1 ağaç) |

---

## §7 bufferSwitch Yaşam Döngüsü (K2 → K3 → K2)

### §7.1 Dört Adım (Kaynak: `asio-drivers.md` §Callback Zinciri, L63–L77)

```cpp
// KAVRAMSAL — kaynak: k2-surucu/asio-drivers.md L63-77
void ASIOCallback(long index, long process) {
    // 1. Input buffer'ı oku
    readInputBuffer(index, inputBuffers);

    // 2. K3 Ses Motoru'na ilet
    feedToEngine(inputBuffers, sampleCount);

    // 3. K3'ten output buffer'ı al
    readFromEngine(outputBuffers, sampleCount);

    // 4. Output buffer'ı donanıma yaz
    writeOutputBuffer(index, outputBuffers);
}
```

### §7.2 Sıra Tablosu

| # | Adım | Katman | Kapsam |
|---|---|---|---|
| 1 | Input buffer okuma | K2 | bu belge |
| 2 | `feedToEngine` | K2 → K3 geçişi | sınır |
| 3 | `readFromEngine` | K3 → K2 dönüşü | `[KAPSAM DIŞI]` (işin kendisi) |
| 4 | Output buffer yazma | K2 | bu belge |

### §7.3 Ortak Arayüz

`asio-wasapi-rehber.md` §5.4 (MEVCUT PROJE GERÇEĞİ):

```text
Ortak K2→K3 arayüzü: process(io, channels, n)
  ASIO yolu:        sürücü ──bufferSwitch(index)──> K2 callback
  WASAPI exclusive: event-driven ──IAudioClient event──> K2 callback
  kural: callback içinde bloklama YOK (lock-free)
```

### §7.4 Zamanlama Mülkiyeti (ADR-017)

ADR-017 §1.1-C (MEVCUT PROJE GERÇEĞİ — `status: accepted`):

> **Katman 3 — ASIO/WASAPI host katmanı (K2):** ASIO Exclusive birincil, WASAPI
> Exclusive/Shared ikincil, buffer/latency yönetimi ve cihaz keşfi; **gerçekleşen
> gecikme bu katmanın sorumluluğudur.**

| Katman | RT garantisi | Etiket |
|---|---|---|
| XMOS/xCORE firmware (K1) | **Garanti sahibi** (donanım) | **MEVCUT PROJE GERÇEĞİ** (ADR-017 §A) |
| JUCE / Neva Engine plugin (K3) | **Garantisi yok** — yalnız hesaplar | **MEVCUT PROJE GERÇEĞİ** (ADR-017 §B) |
| ASIO/WASAPI host (K2) | Gerçekleşen gecikmenin sahibi | **MEVCUT PROJE GERÇEĞİ** (ADR-017 §C) |

---

## §8 Buffer Konfigürasyonu ve Latency Bütçesi

### §8.1 Buffer Tablosu (Kaynak: `windows-api.md` §1.3)

| Parametre | Varsayılan | Min | Max |
|---|---|---|---|
| Buffer Size | 512 samples | 64 | 1024 |

> Bu satır **eski belge iddiasıdır** — `⚠️ VERIFICATION REQUIRED` (gerçek donanımda
> doğrulanmadı). ADR-017 §1.1 aynı değerleri `brain.md:286` üzerinden tekrarlar:
> "ASIO Buffer: 512 sample varsayılan (64-1024), 48kHz, 32-bit float, ~10.67ms".

### §8.2 Buffer → Süre Matematiği (Kavramsal)

`asio-drivers.md` L49–L52 (MEVCUT PROJE GERÇEĞİ — kaynakta verilen örnekler):

| Buffer | Örnekleme | Süre (kaynak iddiası) |
|---|---|---|
| 32 sample | 96 kHz | 0.33 ms (minimum) |
| 64 sample | 96 kHz | 0.67 ms (dengeli) |
| 128 sample | 96 kHz | 1.33 ms (güvenli) |

> **Doğrulama:** `32 / 96000 = 0.333 ms` aritmetikle tutarlıdır — bu, kaynağın
> **kendi iç tutarlılığıdır**, donanım ölçümü değildir.

### §8.3 Latency Hesabı (Kaynak: `asio-drivers.md` L92–L103 — birebir)

```text
Total Latency = Input Buffer + Processing + Output Buffer + Driver Overhead

Örnek (96kHz, 64 sample):
Input:    64/96000 = 0.667ms
Process:  ~0.1ms (K3 DSP)
Output:   64/96000 = 0.667ms
Driver:   ~0.05ms
─────────────────────────────
Total:    ~1.48ms (one-way)
RTT:      ~2.96ms (round-trip)
```

> **⚠️ VERIFICATION REQUIRED:** Bu hesap **kâğıt hesabıdır**, ölçüm değildir.
> `process ~0.1ms` ve `driver ~0.05ms` değerlerinin kaynağı belgede verilmemiştir.

### §8.4 Bütçe Kapısı (Bağlayıcı)

| Kalem | Değer | Kaynak | Etiket |
|---|---|---|---|
| Azami bütçe | **<10 ms ASIO / <20 ms WASAPI** | `.ai/CLAUDE.md` L429 + ADR-006 | **MEVCUT PROJE GERÇEĞİ (hedef)** |
| k2 tasarım hedefi | `<0.5 ms` round-trip, 32 sample'a kadar | `asio-drivers.md` L12, L21, L35 | **MEVCUT PROJE GERÇEĞİ (hedef)** |
| Kapının konma biçimi | **ölçülen round-trip** (talep ≠ gerçek) | ADR-017 §2.2b | **MEVCUT PROJE GERÇEĞİ** |
| `underrun = 0` | `⚠️ VERIFICATION REQUIRED` olarak kalır | ADR-006 / ADR-017 §4 | **AÇIK — tescil edilmedi** |
| Ölçülmüş gerçek değer | — | — | `[NOT PROVIDED]` |

---

## §9 Gerçek Zamanlı Thread Yapısı

### §9.1 Oluşturma ve Öncelik (Kaynak: `windows-api.md` §3.1 — birebir)

```cpp
// KAVRAMSAL İSKELET — kaynak: windows-api.md L155-L172
HANDLE hThread = CreateThread(
    NULL,                           // Default security
    0,                              // Default stack size
    AudioThreadProc,                // Thread function
    lpParam,                        // Parameter
    0,                              // Run immediately
    &dwThreadId                     // Thread ID
);

// Set real-time priority
SetThreadPriority(hThread, THREAD_PRIORITY_TIME_CRITICAL);

// Set CPU affinity (dedicated core)
SetThreadAffinityMask(hThread, 1 << audioCoreIndex);

// Set thread name (for debugging)
SetThreadDescription(hThread, L"Audio Processing Thread");
```

### §9.2 Öncelik Sıralaması

| Kullanım | Öncelik | Kaynak | Etiket |
|---|---|---|---|
| Audio Processing | `TIME_CRITICAL (15)` — Dedicated | `README.md` L242 | **MEVCUT PROJE GERÇEĞİ** |
| ASIO callback | RT iş parçacığı en yüksek uygulama önceliğinde | ADR-017 §G | **MEVCUT PROJE GERÇEĞİ** |
| Öncelik verilmezse | "ölçüm geçersiz" | ADR-017 §G | **MEVCUT PROJE GERÇEĞİ** |

> **Kısıt:** `SetThreadPriority`/`SetThreadAffinityMask` çağrılarının **bu makinede
> başarısı** ölçülmüştür → `[NOT PROVIDED]`. İzin/privilege boyutu
> [[windows-guvenlik-ve-olcullu-kisitlar]] kapsamındadır.

### §9.3 Senkronizasyon Primitifleri Yasakları (Kaynak: `windows-api.md` §3.2)

| Primitif | Kullanım | Audio Thread'de |
|---|---|---|
| Critical Section | Mutex | ❌ YASAK |
| SRW Lock | Lightweight mutex | ❌ YASAK |
| Event | Olay beklemesi | ⚠️ Sınırlı |
| Semaphore | Sayaçlı senkronizasyon | ⚠️ Sınırlı |
| `Interlocked*` | Atomik işlemler | ✅ İzinli |
| `atomic<>` | Lock-free | ✅ İzinli |

### §9.4 Mesaj Kuyruğu İlişkisi

ASIO `bufferSwitch` çağrısı **`WM_*` mesaj döngüsüne bağlı değildir**; donanım
zamanlayıcısı (`wintimer.cpp` — "Buffer switch timer") tetikler. Bu, bu belge ile
[[win32-olay-dongusu-ve-mesaj-kuyrugu]] arasındaki sınırı çizer:

| Zamanlayıcı | Kullanım | Bekler mi? |
|---|---|---|
| `WM_*` kuyruğu | UI / Win32 olay döngüsü | evet (`GetMessage`) |
| ASIO timer + callback | RT ses yolu | **hayır** — bloklamaz |

> **`⚠️ VERIFICATION REQUIRED`:** İki mekanizmanın aynı süreçte birlikte kullanımı
> (thread mimarisi) bu depoda yazılmamıştır.

---

## §10 Hard-RT Kısıtları (ADR-017 — Bağlayıcı)

ADR-017 §E (MEVCUT PROJE GERÇEĞİ, `status: accepted`):

| Yasak | Açıklama |
|---|---|
| `new` / `malloc` / `free` / `delete` / `throw` | Callback içinde tahsisat + istisna |
| Mutex / kilit | Bloklama |
| Disk / DB / ağ I/O | Bloklama |
| Loglama | Genellikle tahsisat + I/O |
| `catch` | İstisna yakalama |

**Zorunlu:** paylaşılan durum `std::atomic` / lock-free ring buffer ile taşınır.

### §10.1 Gerekçe (ARAŞTIRMA REFERANSI)

ADR-017 §1.3 kaynağı: Ross Bencina "callback'te tahsisat yapma — **allocator kilidi
taşıyabilir**" der (rossbencina.com — Real-time audio programming 101); Timur.audio
mutex'in gerçek-zamanlı iş parçacığında uygun olmadığını yazar. Etiket: **ARAŞTIRMA
REFERANSI** (ADR-017 web turu, 6 sorgu / ~22 kaynak).

### §10.2 Bellek API'leri (K0)

`windows-api.md` §4 (MEVCUT PROJE GERÇEĞİ):

| API | Amaç | Not |
|---|---|---|
| `GetLargePageMinimum()` | 2MB large page | `MEM_LARGE_PAGES` ile `VirtualAlloc` |
| `VirtualAlloc` / `VirtualFree` | Büyük sayfa tahsisi | Önceden ayrılmalı |
| `CreateFileMapping` + `MapViewOfFile` | Büyük ses dosyaları | `PAGE_READONLY` / `FILE_MAP_READ` |

> **Kural:** Bu çağrılar callback **içinde değil**, kurulum aşamasında yapılır
> (ADR-017 §E yasağı ile uyumlu). Bu ayrım belgede açıkça yazılmamıştır →
> `⚠️ VERIFICATION REQUIRED` (çıkarım, kaynaklı emir değil).

---

## §11 Xrun Politikası (Tespit → Bildirim → Kurtarma)

ADR-017 §F (MEVCUT PROJE GERÇEĞİ):

```text
1) TESPIT     — atomik sayaç (underflow/overrun)           [K2]
2) BILDIRIM   — non-RT iş parçacığında sıklık-limitli log   [ADR-013 ruhu]
3) KURTARMA   — buffer artır → CPU yükü azalt → fade-out/50ms → restart
4) CIHAZ      — ASIO device loss → WASAPI → Null Output
```

| Adım | Sahip | Etiket |
|---|---|---|
| Sayaç (xrun_count) | K2 | **MEVCUT PROJE GERÇEĞİ** (`asio-wasapi-rehber.md` §5.1) |
| Log limiti | K14/ADR-013 | **MEVCUT PROJE GERÇEĞİ** |
| Kurtarma sırasında ses kesilmez, **fade-out** uygulanır | `brain.md:869` | **MEVCUT PROJE GERÇEĞİ** |
| CI kapısı (`xrun = 0`) | `.github/workflows/` → **PLANNED** | ADR-017 §5.1 adım 8 |

---

## §12 Hata Durumları ve Fallback Zinciri

### §12.1 ASIO Hata Tablosu (Kaynak: `asio-drivers.md` L107–L112)

| Hata | Eylem |
|---|---|
| `ASIOError_InvalidMode` | Exclusive kullanılamıyorsa **Shared mode'a geç** |
| `ASIOError_BufferSize` | Buffer boyutu donanım tarafından desteklenmiyorsa |
| `ASIOError_HardwareFailure` | Donanım hatası → **K3'ü durdur** |
| `ASIOError_UnableToStart` | Başlatma hatası → **3 yeniden deneme** |

### §12.2 Zincir

```text
Windows (K0):
  ASIO ──ok──> ASIO Exclusive
    │ cihaz kaybı (USB kopması) / 32-bit sürücü / InvalidMode
  WASAPI Exclusive
    │ paylaşımlı kullanım / DEVICE_IN_USE
  WASAPI Shared
    │
  Null Output   (sessiz, durum=ERROR — UI'da bildirim)
```

| Halka | Kaynak | Etiket |
|---|---|---|
| ASIO → WASAPI → Null | `.ai/brain.md` L862/L873 | **MEVCUT PROJE GERÇEĞİ** |
| `ASIO device loss → WASAPI fallback` | `.ai/AGENTS.md` §17 madde 6 | **MEVCUT PROJE GERÇEĞİ** |
| `ASIOError_InvalidMode → Shared` | `asio-drivers.md` L109 | **MEVCUT PROJE GERÇEĞİ** |
| `AUDCLNT_E_DEVICE_IN_USE → Shared` | ADR-019 §2.2c | **MEVCUT PROJE GERÇEĞİ** |
| Eskalasyon: ASIO cihaz kaybı L1(Embedded) → L2, 30s | `.ai/AGENTS.md` §10.1 | **MEVCUT PROJE GERÇEĞİ** |

---

## §13 Sahiplik ve Adapter Sınırı (ADR-019)

| Öğe | Sorumlu | Kaynak |
|---|---|---|
| `*.cpp` / `*.h` (çekirdek + `IAudioBackend` + adapter) | **Embedded Engineer** | `.ai/AGENTS.md` §5 |
| WASAPI/COM detayı danışmanlığı | **Windows Software Engineer** (`win-sw`) | `.ai/AGENTS.md` §15 |
| Çekirdek asla OS API'sini doğrudan çağırmaz | yalnız `IAudioBackend` üzerinden | ADR-019 §B |
| Backend sırası (Windows) | ASIO → WASAPI Exclusive → Shared → Null | ADR-019 §C |
| Kod durumu | **PLANNED** (0 `*.cpp/*.h`, `IAudioBackend` = 0 eşleşme) | ADR-019 §1.3-B |

**Domain boundary ihlali** → derhal revert + `log.md` ERROR (`.ai/AGENTS.md` §18).

---

## §14 Gömülü Doğrulanabilir İddialar Tablosu

| # | İddia | Kanıt | Etiket |
|---|---|---|---|
| 1 | `initializeASIO()` / `shutdownASIO()` statik imzaları | `README.md` L90–L92 | **MEVCUT PROJE GERÇEĞİ** |
| 2 | `ASIOCallbacks` 4 üyeli protokol | `windows-api.md` L50–L77 | **MEVCUT PROJE GERÇEĞİ** |
| 3 | Buffer varsayılan 512 / min 64 / max 1024 | `windows-api.md` L81–L83 | **ESKİ BELGE İDDİASI** |
| 4 | ASIO Exclusive `< 0.5ms` hedefi | `asio-drivers.md` L12, L35 | **MEVCUT PROJE GERÇEĞİ (hedef)** |
| 5 | Callback içinde bloklama YOK | `asio-wasapi-rehber.md` §5.4 | **MEVCUT PROJE GERÇEĞİ** |
| 6 | Hard-RT yasakları (tahsisat/kilit/I/O/log) | ADR-017 §E | **MEVCUT PROJE GERÇEĞİ** |
| 7 | Latency bütçesi `<10ms ASIO / <20ms WASAPI` | `.ai/CLAUDE.md` L429 | **MEVCUT PROJE GERÇEĞİ (hedef)** |
| 8 | Repo'da ASIO kodu var mı? → `*.cpp/*.h = 0` | ADR-017 §1.1-B | **OLUMSUZ — kanıt yok** |
| 9 | `.ai/log.md` "12 header, ~79KB" iddiası | `.ai/log.md` L98 (ADR-017 aktarımı) | `⚠️ VERIFICATION REQUIRED` |
| 10 | ASIO SDK sürümü | — | `⚠️ VERIFICATION REQUIRED` (§ÇELİŞKİ 1) |

---

## §15 Bağlantı Haritası

| Konu | Giden dosya | Kapsam |
|---|---|---|
| WASAPI fallback halkaları | [[wasapi-ses-yolu-cekirdek]] | K000 |
| `WM_*` olay döngüsü | [[win32-olay-dongusu-ve-mesaj-kuyrugu]] | K000 |
| RT öncelik izinleri / token | [[windows-guvenlik-ve-olcullu-kisitlar]] | K000 |
| QPC/ETW + xrun ölçüm yolları | [[windows-performans-ve-gozlemlenebilirlik]] | K000 |
| XMOS firmware zamanlaması | `[KAPSAM DIŞI]` → K1 | K1 |
| DSP zinciri (EQ→comp→limiter) | `[KAPSAM DIŞI]` → K3 | K3 |
| macOS/Linux adapter | `[KAPSAM DIŞI]` → ADR-019 | — |
| JUCE plugin RT kuralları | `[KAPSAM DIŞI]` → ADR-017 §B | K3 |

---

## ÇELİŞKİ / DOĞRULAMA

> Bu belgede en fazla **5** madde; bu dosya **2** madde taşır (toplam 10 sınırı).
> Düzeltme yapılmaz — yalnız raporlanır.

**1 — ASIO SDK sürümü: `2.3` mi, `2.3.4` mü?**

| Kaynak | Satır | İddia |
|---|---|---|
| `_backup\...\k0-isletim-sistemi\windows-api.md` | L27 | `### 1.1 ASIO 2.3 SDK Yapısı` |
| `_backup\...\k0-isletim-sistemi\README.md` | L630 | `K0.8.1.7 | ASIO 2.3 SDK Yapısı | windows-api.md L27` |
| `.ai\AGENTS.md` | §4 / §15 (Embedded Engineer) | `ASIO SDK 2.3.4` |
| `.ai\.decisions\accepted\ADR-017-dsp-hardware-mode.md` | L128 | `ASIO SDK 2.3.4 (bufferSwitch, asioDriver)` |

> **Etki:** K0 derleme bağımlılığının sabitleneceği sürüm belirsiz (reproducible
> build riski — `asio-wasapi-rehber.md` §3.7.1 "sürüm: sabit (tag/hash)").
> **Durum:** `⚠️ VERIFICATION REQUIRED` — resmi SDK sürümü depoda tescilli değil;
> bu belge sürüm **yazmaz**, `bağımlılık kaydı`nda `[UNKNOWN]` taşır.

**2 — ADR-017'nin atıf yaptığı spec yolu canlı vault'ta yok**

| Kaynak | Satır | İddia |
|---|---|---|
| `.ai\.decisions\accepted\ADR-017-dsp-hardware-mode.md` | L39–L40 | `ASIO/WASAPI host spesifikasyonu — .ai/architecture/k2-surucu/ (IMPLEMENTED doküman)` + `asio-drivers.md:12` |
| Canlı disk (bu görevde ölçülen) | `.ai\architecture\k2-surucu\` | **DİZİN YOK** — glob `**/k2-surucu/*.md` yalnız `_backup\arch-2026-10-06_1057\architecture\k2-surucu\` altını buldu |
| Aynı durum ADR-019 için | L34, L236 | `.ai/architecture/k2-surucu/asio-drivers.md` atfı — aynı eksik yol |

> **Etki:** "IMPLEMENTED" etiketli spec'in canlı yolu kırık; kanıt yalnız backup'ta
> duruyor. ADR'ler frozen'dır (dokunulmaz) → **düzeltme bu belgeye ait değildir.**
> **Durum:** `⚠️ VERIFICATION REQUIRED` — klasör ya taşındı ya da ADR yolu
> güncellenmedi; hangisi olduğu depoda çözülemedi.

---

## Kaynaklar

Okunan gerçek dosya yolları (bu belgeye gömülü kanıt):

1. `C:\www\coremusic.net\_backup\arch-2026-10-06_1057\architecture\k0-isletim-sistemi\windows-api.md`
2. `C:\www\coremusic.net\_backup\arch-2026-10-06_1057\architecture\k0-isletim-sistemi\README.md`
3. `C:\www\coremusic.net\_backup\arch-2026-10-06_1057\architecture\k2-surucu\asio-drivers.md`
4. `C:\www\coremusic.net\_backup\arch-2026-10-06_1057\architecture\katman-baglilik-matrisi.md`
5. `C:\www\coremusic.net\.ai\.decisions\accepted\ADR-017-dsp-hardware-mode.md`
6. `C:\www\coremusic.net\.ai\.decisions\accepted\ADR-019-per-os-neva-player.md`
7. `C:\www\coremusic.net\.ai\ecosystem\asio-wasapi-rehber.md`
8. `C:\www\coremusic.net\.ai\AGENTS.md`
9. `C:\www\coremusic.net\.ai\brain.md`
10. `C:\www\coremusic.net\.ai\CLAUDE.md`
11. `C:\www\coremusic.net\.github\workflows\ci.yml` · `secret-scan.yml` (varlık ölçümü)
12. `C:\www\coremusic.net\.ai\architecture\k000-windows-core\index.md` (salt-okunur stil referansı)

**Doğrulama durumu:** Bu belgede **yeni sürüm/ölçüm/latency rakamı üretilmemiştir**.
`*.cpp`/`*.h` = 0 olduğundan tüm kod blokları **kavramsal iskelettir**; `⚠️`
işaretli satırlar gerçekleştirme öncesi doğrulanmalıdır.
