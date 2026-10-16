---
node_id: donanım-api-kod-seti
node_type: code
status: verify-required
parent_id: donanım-api
children: []
depends_on:
  - donanım-api-kapsam
  - donanım-api-dogrulama
  - K001-donanım-platformlararasi-kod-ornekleri
evidence:
  - "2026-10-14: 31 resmi URL HEAD/GET HTTP 200 — tam liste §URL ENVANTERİ (kesişim 0 / 2.730)"
  - "2026-10-14: Tizen `sound_manager_*` fonksiyon adları disk kanıtlı (temp doxygen: group__CAPI__MEDIA__SOUND__MANAGER*.txt)"
  - "donanım-api-kapsam.md §25-§26 (bu dosyanın özet matris karşılıkları)"
validation: "2026-10-14: yazıldı — 11 OS + 11 dil matrisi · 28 kod bloğu (§4: 16 · §6: 12) · URL 31/31 HTTP 200 · kod blokları derlenmedi/çalıştırılmadı"
open_questions:
  - "K1: Tüm kod blokları tasarımdır — derlenmedi, çalıştırılmadı, test edilmedi → ⚠️ VERIFICATION REQUIRED"
  - "K2: FreeBSD `SNDCTL_DSP_SETFMT` / `AFMT_S16_LE` sabit değerleri resmi sayfadan okunmadı → ⚠️ VERIFICATION REQUIRED"
  - "K3: Zephyr satırı [TASARIM] — sürücü modeli API'si (device_get_binding vs DEVICE_DT_GET) doğrulanmadı → ⚠️ VERIFICATION REQUIRED"
  - "K4: webOS Luna `setMuted` parametre şeması topluluk örneğinden — resmi referansta teyit edilmedi → ⚠️ VERIFICATION REQUIRED"
  - "K5: CoreMusic'in hangi OS'yi hedeflediği (Windows/macOS/Linux öncelik sırası) bilinmiyor → Q-A1 bağları — UNKNOWN"
group: kod
---

# donanım-api — Kod Seti (Her OS · Her Dil · Öneri + Veri)

> **Kapsam:** Bu dosya, `donanım-api` düğümü için **her işletim sisteminde donanıma erişim
> arayüzünü** ve **her programlama dilinde donanıma erişim desenini** (öneri + resmi veri)
> tarif eder. Üç şey taşır: (1) OS bazlı erişim arayüzü matrisi, (2) dil bazlı FFI/erişim
> matrisi, (3) her ikisi için doğrulanmış URL'li kod blokları.
>
> **Sahiplik sınırı (K001 root dosyalarıyla ayrım):**
>
> | Dosya | Ne taşır | Ayrım |
> |---|---|---|
> | `donanım-platformlararasi-kod-ornekleri.md` (K001 kökü) | Dil → **OS algılama makrosu** (`__linux__` · `process.platform` · `GOOS`) ve genel sistem yolları (disk/mount) | Algılama — **donanım erişimi DEĞİL** |
> | `donanım-api-kod-seti.md` (bu dosya) | OS → **donanım-erişim arayüzü** (ioctl · sysfs · WASAPI · CoreAudio · AAudio · Luna · WebHID) + dil → **donanım-erişim FFI deseni** | Erişim yüzeyi |
> | `donanım-kod-ornekleri.md` (K001 kökü) | Repo'da **var olan gerçek PHP/JS kodu** | Repo gerçeği — bu dosya tasarımı taşır |
>
> **Dürüstlük notu:** Bu dosyadaki **hiçbir kod bu vault'ta derlenmedi veya çalıştırılmadı**
> (`[ORİJİNAL ÖRNEK — derlenmedi]`). API adları/imzaları §URL ENVANTERİ'ndeki resmi
> sayfalardan (docs.kernel.org · learn.microsoft.com · developer.apple.com ·
> developer.android.com · cppreference · php.net · nodejs.org · pkg.go.dev · keil.com ·
> docs.tizen.org · webostv.developer.lge.com) doğrulanmıştır; doğrulanamayan her satır
> `⚠️ VERIFICATION REQUIRED` işaretini taşır.

---

## §0 Politika

- Bu dosyadaki kod "çalışan" değil "doğrulanmış resmi API'lerle çizilmiş" koddur: `verify-required`.
- Her kod bloğu: dil yorum satırı + resmi URL (ilk satır) + `[ORİJİNAL ÖRNEK — derlenmedi]`.
- Sessiz fallback yok: doğrulanamayan hücre `[TASARIM]` veya `UNKNOWN` + `⚠️ VERIFICATION REQUIRED`.
- **Uydurma sabit yasağı:** komut numarası / register offset'i / buffer sayısı gibi
  donanıma özgü sayılar bu dosyaya **yazılmaz** — hepsi Q-A1/Q-A3 (kapsam §6) bekler.
- Yazım dili Türkçe; resmi API adları ve kod İngilizce.
- Bu dosya Q-A1 (protokol) kararını **seçmez**; dalları ve kanıtları karar verene açar.

## §1 Çalışma sözleşmesi

| # | Kural | Ayrıntı |
|---|---|---|
| 1 | Kod statüsü | Tasarım şablonu: derlenmedi · çalıştırılmadı · test edilmedi |
| 2 | Kanıt | Her OS/dil satırı en az 1 resmi HTTP 200 URL taşır (§URL ENVANTERİ) |
| 3 | Bilinmeyen | CoreMusic'e özgü parametre = `UNKNOWN`; kanıtsız iddia = `⚠️` |
| 4 | Kapsam dışı | Register haritası ham verisi, DMA buffer boyutu, IOCTL komut listesi → kapsam §17-§18 (UNKNOWN) |
| 5 | Bağ | Özet matrisler `donanım-api-kapsam.md` §25-§26'da; kanıt zinciri `donanım-api-dogrulama.md` §25-§26'da |

## §2 GRUP haritası

| GRUP | Konu | Soru | Bölüm |
|---|---|---|---|
| GRUP A | OS bazlı donanım-erişim arayüzleri | Her OS'de donanıma hangi arayüzle erişilir? | §3 matris · §4 kod |
| GRUP B | Dil bazlı donanım-erişim desenleri | Her dilde donanıma nasıl çağrılır? | §5 matris · §6 kod |
| GRUP C | K001 API'ye öneriler (karar girdisi) | Q-A1 protokolü hangi kanıtlara oturur? | §7 |

**Bridge kproları:**

| Konu | Bu dosyada | Kpr |
|---|---|---|
| MMIO/PIO, DMA, register kuraları (genel kavram) | — | `donanım-api-kapsam.md` §7-§16 |
| IOCTL/sysfs karar ağacı | §7 (yalnız kanıt girdisi) | `donanım-api-kapsam.md` §17 |
| OS algılama makroları | — | `donanım-platformlararasi-kod-ornekleri.md` §PLATFORM MATRİSİ |
| Repo gerçek PHP/JS kodu | — | `donanım-kod-ornekleri.md` |

---

## §3 GRUP A — OS Bazlı Donanım-Erişim Arayüzü Matrisi (11 OS)

| # | İşletim sistemi | Donanım-erişim arayüzü ailesi | Ses/cihaz yolu (veri) | K001 API etkisi | Kanıt | Durum |
|---|---|---|---|---|---|---|
| OS1 | **Linux** | `ioctl(2)` · sysfs (`/sys`) · karakter cihaz (`/dev`) | ALSA: `/dev/snd/*` · alsa-lib | Q-A1 kararının birincil adayı (kapsam §17 Q1-Q6) | #L1-#L3 | ✅ URL 200 · genel kavram |
| OS2 | **Windows** | `DeviceIoControl` (IOCTL) · SetupAPI | WASAPI: `IAudioClient` · `IAudioRenderClient` | `win-sw` agent yüzeyi; ASIO→WASAPI fallback (AGENTS §17/6) | #W1-#W3 | ✅ URL 200 |
| OS3 | **macOS** | CoreAudio HAL: `AudioObjectGetPropertyData/SetPropertyData` | `kAudioHardwarePropertyDefaultOutputDevice` | Donanım→ses yolu IOKit üzerinden HAL | #M1-#M2 | ✅ URL 200 (arşiv + güncel) |
| OS4 | **iOS** | AVAudioSession (kategori · route · aktivasyon) | `setCategory(_:options:)` | Mobil kapsam K001'de **UNKNOWN** (dar kapsamlı) | #I1-#I2 | ✅ URL 200 |
| OS5 | **Android** | NDK AAudio (Oboe sarmalayıcı) | `AAudioStream*` builder/okuma-yazma | Düşük gecikme referansı (exclusive/shared mod) | #A1 | ✅ URL 200 |
| OS6 | **FreeBSD** | OSS uyumlu `/dev/dsp` · `SNDCTL_*` ioctl | `AFMT_*` format seçimi | Taşınabilirlik karşılaştırma girdisi | #F1 | ⚠️ URL 200 · sabitler teyitsiz (K2) |
| OS7 | **RTOS / Zephyr** | Cihaz sürücü modeli (device tree kökenli) | I2S/SPI üzerinden codec | Gömülü referans — K001'de K005 sınırı | #R1 | ⚠️ [TASARIM] (K3) |
| OS8 | **Gömülü DSP (CMSIS-DSP)** | `arm_math` kütüphanesi (sabit) | FIR/EQ zinciri | K004-DSP referansı (anayasa §2 sınırı) | #R2 | ✅ URL 200 |
| OS9 | **Web / ChromeOS** | WebUSB · WebHID (tarayıcı donanım API'si) | — (ses WebAudio kapsam dışı bırakıldı) | Tarayıcı → cihaz erişimi güvenlik sınırı (OWASP) | #B1-#B3 | ✅ URL 200 |
| OS10 | **Tizen** | CAPI `sound_manager_*` (donanım ses yönetimi) | `sound_manager_get/set_volume` | TV/mobil platform karşılığı | #T1 | ✅ URL 200 · fonksiyonlar disk kanıtlı |
| OS11 | **webOS (LG)** | Luna servis otobüsü: `luna://com.webos.audio` | `volumeUp` · `setMuted` (ACG: `audio.operation`) | IoT/TV erişim modeli — iş katmanı üzerinden | #O1 | ✅ URL 200 · parametre şeması ⚠️ (K4) |

> **Not (OS9):** ChromeOS, Linux uygulama kabından (Crostini) donanıma dolaylı erişir;
> ayrık donanım API'si sunmaz → satır #C1.

### §3.1 OS matrisi — Q-A1'e katkı sütunu

| # | Ölçüt | Linux | Windows | macOS | Katkı |
|---|---|---|---|---|---|
| A1 | Düşük gecikme komut yolu | `ioctl` ✓ | `DeviceIoControl` ✓ | HAL property ✓ | hepsi genel kavram |
| A2 | Dosya/attribute tabanlı okuma | sysfs ✓ | — | — | Linux'a özgü |
| A3 | Ses akış API'si | ALSA (snd_pcm) | WASAPI | CoreAudio | üçü de ses akış ≠ kontrol yolu (kapsam §4/C4) |
| A4 | CoreMusic seçimi | — | — | — | **UNKNOWN** (Q-A1) |

---

## §4 GRUP A — OS Bazlı Kod Örnekleri

### §4.1 Linux — karakter cihaz + ioctl (kontrol yolu)

```c
/* Kanıt: #L1 (§8 — HTTP 200 · 2026-10-14) */
/* [ORİJİNAL ÖRNEK — derlenmedi] */
#include <fcntl.h>
#include <sys/ioctl.h>

int fd = open("/dev/snd/controlC0", O_RDWR);   /* ALSA kontrol cihazı — yolu genel kavram */
if (fd < 0) { /* errno oku — hata sınıfı kapsam §19/E2 */ }

/* req: komut numarası — donanım datasheet'inden gelir.
   CoreMusic komut seti YOK → UNKNOWN (Q-A1/Q-A3, kapsam §17.3). */
int rc = ioctl(fd, req, &arg);
close(fd);
```

### §4.2 Linux — ALSA sürücü API'si (yalnız referans — K000 sınırı)

```c
/* Kanıt: #L2 (§8 — HTTP 200 · 2026-10-14) */
/* [ORİJİNAL ÖRNEK — derlenmedi] — çekirdek sürücü kodu = K000 kapsamı (anayasa §10/2) */
static struct snd_pcm_ops playback_ops = {
    .open = chip_pcm_open, .hw_params = chip_pcm_hw_params,
    .prepare = chip_pcm_prepare, .trigger = chip_pcm_trigger,
};
/* İmza resmi sayfadan: int snd_pcm_new(struct snd_card *, const char *, int,
   int playback_count, int capture_count, struct snd_pcm **) */
snd_pcm_new(card, "coremusic", device, 1, 0, &pcm);
```

### §4.3 Linux — sysfs (durum okuma yolu)

```c
/* Kanıt: #L3 (§8 — HTTP 200 · 2026-10-14) */
/* [ORİJİNAL ÖRNEK — derlenmedi] */
FILE *f = fopen("/sys/class/sound/hwC0D0/uevent", "r");  /* örnek attribute — gerçek yol K001'de UNKNOWN */
if (f) { /* fgets ile oku — salt okuma, yazma yasak (kapsam §7.2/R1) */ fclose(f); }
```

### §4.4 Windows — DeviceIoControl (IOCTL kontrol yolu)

```cpp
/* Kanıt: #W1 (§8 — HTTP 200 · 2026-10-14) */
/* [ORİJİNAL ÖRNEK — derlenmedi] */
HANDLE h = CreateFileW(L"\\\\.\\ExampleDev", GENERIC_READ | GENERIC_WRITE,
                       0, nullptr, OPEN_EXISTING, 0, nullptr);
if (h == INVALID_HANDLE_VALUE) { /* GetLastError — kapsam §19/E2 */ }
DWORD br = 0;
/* dwCode: IOCTL kodu — CoreMusic komut seti UNKNOWN (Q-A1) */
DeviceIoControl(h, dwCode, inBuf, inSize, outBuf, outSize, &br, nullptr);
CloseHandle(h);
```

### §4.5 Windows — WASAPI (ses akış yolu)

```cpp
/* Kanıt: #W2 (§8 — HTTP 200 · 2026-10-14) */
/* [ORİJİNAL ÖRNEK — derlenmedi] — arayüz adları resmi sayfadan */
/* IMMDevice → Activate(IID_IAudioClient) → IAudioClient::Initialize(...)
   → GetService(IID_IAudioRenderClient, ...) → GetBuffer / ReleaseBuffer */
IAudioClient* client = nullptr;      /* akışı başlatır/durdurur (kapsam §17.3 O4/O5) */
IAudioRenderClient* render = nullptr; /* tampon doldurma — K003 görevi */
client->GetService(__uuidof(IAudioRenderClient), (void**)&render);
```

### §4.6 macOS — CoreAudio HAL (varsayılan çıkış cihazı)

```c
/* Kanıt: #M1 (§8 — HTTP 200 · 2026-10-14) */
/* [ORİJİNAL ÖRNEK — derlenmedi] — imza arşiv örneğinden doğrulandı */
AudioObjectPropertyAddress addr = {
    kAudioHardwarePropertyDefaultOutputDevice,
    kAudioObjectPropertyScopeGlobal,
    kAudioObjectPropertyElementMaster
};
AudioDeviceID dev = kAudioObjectUnknown;
UInt32 size = sizeof(dev);
AudioObjectGetPropertyData(kAudioObjectSystemObject, &addr, 0, NULL, &size, &dev);
```

### §4.7 macOS — CoreAudio güncel property anahtarı

```c
/* Kanıt: #M2 (§8 — HTTP 200 · 2026-10-14) */
/* [ORİJİNAL ÖRNEK — derlenmedi] */
/* kAudioHardwarePropertyDefaultOutputDevice — sistem varsayılan çıkışını okur.
   Mülkiyet/scope yapısı §4.6 ile aynıdır; örnek blok tekrar edilmez. */
```

### §4.8 iOS — AVAudioSession kategori + aktivasyon

```swift
/* Kanıt: #I1 (§8 — HTTP 200 · 2026-10-14) */
/* [ORİJİNAL ÖRNEK — derlenmedi] */
let session = AVAudioSession.sharedInstance()
try session.setCategory(.playAndRecord, options: [])
try session.setActive(true)   /* başarısızlık → kapsam §19/E3 durum hatası */
```

### §4.9 Android — AAudio düşük gecikme akışı

```c
/* Kanıt: #A1 (§8 — HTTP 200 · 2026-10-14) */
/* [ORİJİNAL ÖRNEK — derlenmedi] — enum/fonksiyon adları resmi referanstan */
AAudioStreamBuilder* builder = NULL;
AAudio_createStreamBuilder(&builder);
AAudioStreamBuilder_setPerformanceMode(builder, AAUDIO_PERFORMANCE_MODE_LOW_LATENCY);
AAudioStreamBuilder_setSharingMode(builder, AAUDIO_SHARING_MODE_EXCLUSIVE);
AAudioStream* stream = NULL;
AAudioStreamBuilder_openStream(builder, &stream);   /* hatada stream NULL — hata sınıfı E1/E2 */
AAudioStreamBuilder_delete(builder);
```

### §4.10 FreeBSD — OSS `/dev/dsp` (taşınabilirlik karşılaştırma girdisi)

```c
/* Kanıt: #F1 (§8 — HTTP 200 · 2026-10-14) */
/* [ORİJİNAL ÖRNEK — derlenmedi] ⚠️ SNDCTL_DSP_SETFMT / AFMT_S16_LE sabitleri
   resmi sayfadan okunmadı — VERIFICATION REQUIRED (K2) */
int fd = open("/dev/dsp", O_WRONLY);
int fmt = AFMT_S16_LE;
ioctl(fd, SNDCTL_DSP_SETFMT, &fmt);
close(fd);
```

### §4.11 RTOS / Zephyr — cihaz sürücü modeli

```c
/* Kanıt: #R1 (§8 — HTTP 200 · 2026-10-14) */
/* [TASARIM] ⚠️ — sürücü-modeli API'si doğrulanmadı (K3):
   device_get_binding(...) eski, DEVICE_DT_GET(...) güncel desendir. */
const struct device* dev = /* DT alias'tan al */;
/* dev ile register/I2S erişimi → donanım-kernel sınırı (kapsam §3/B1) */
```

### §4.12 Gömülü DSP — CMSIS-DSP filtre zinciri

```c
/* Kanıt: #R2 (§8 — HTTP 200 · 2026-10-14) */
/* [ORİJİNAL ÖRNEK — derlenmedi] */
#include "arm_math.h"
arm_fir_instance_f32 fir;
arm_fir_init_f32(&fir, numTaps, coeffs, state, blockSize);
/* K004-DSP zinciri parçası — K001 API'si yalnız ölçüm/tetik yüzeyi (§23) */
```

### §4.13 Web — WebHID ile donanım keşfi

```js
/* Kanıt: #B1 (§8 — HTTP 200 · 2026-10-14) */
/* [ORİJİNAL ÖRNEK — derlenmedi] */
const devices = await navigator.hid.requestDevice({ filters: [] });
for (const d of devices) { console.log(d.productName); }
```

### §4.14 Web — WebUSB ile donanım keşfi

```js
/* Kanıt: #B2 (§8 — HTTP 200 · 2026-10-14) */
/* [ORİJİNAL ÖRNEK — derlenmedi] */
const dev = await navigator.usb.requestDevice({ filters: [] });
await dev.open();
```

### §4.15 Tizen — CAPI sound_manager (fonksiyon adları disk kanıtlı)

```c
/* Kanıt: #T1 (§8 — HTTP 200 · 2026-10-14) */
/* [ORİJİNAL ÖRNEK — derlenmedi] — sound_manager_get_volume / sound_manager_set_volume
   fonksiyon adları temp doxygen çıktısından (group__CAPI__MEDIA__SOUND__MANAGER*) doğrulandı */
int volume = 0;
sound_manager_get_volume(sound_type, &volume);
sound_manager_set_volume(sound_type, volume);
```

### §4.16 webOS — Luna servis otobüsü ses metodu

```js
/* Kanıt: #O1 (§8 — HTTP 200 · 2026-10-14) */
/* [ORİJİNAL ÖRNEK — derlenmedi] — ACG tablosu: com.webos.audio → setMuted|volumeUp|volumeDown
   (yetki grubu audio.operation). Parametre şeması ⚠️ teyitsiz (K4). */
webOS.service.request("luna://com.webos.audio", {
  method: "setMuted",
  parameters: { /* ⚠️ şema doğrulanmadı */ },
  onFailure: (err) => { /* hata kodu kapsam §19 */ },
});
```

---

## §5 GRUP B — Dil Bazlı Donanım-Erişim Matrisi (11 Dil)

| # | Dil | Donanım erişim yolu | Desen / FFI | K001 API uyumu | Kanıt | Durum |
|---|---|---|---|---|---|---|
| D1 | **C** | doğrudan sistem çağrısı (`open` · `ioctl`) | yok (native) | çekirdek referans — K001 C++20 hedefiyle uyumlu | #G1 | ✅ URL 200 |
| D2 | **C++** | RAII sarmalayıcı + `noexcept` | native + `dlopen` | **K001 birincil adayı** (kural 1-2: C++20/JUCE) | #G2 | ✅ URL 200 |
| D3 | **Rust** | `extern "C"` + `unsafe` | güvenli sarmalayıcı katmanı | alternatif — güvenli FFI, öğrenme eğrisi | #G3 | ✅ URL 200 |
| D4 | **Zig** | `@cImport` + C ABI | doğrudan C çağrısı | niş alternatif — K001'de kanıt yok | #G4 | ✅ URL 200 |
| D5 | **Python** | `ctypes` / C sarmalayıcı | kütüphane yükleyerek | test/araç katmanı — gerçek-zamanlı akışa uygun değil | #G5 | ✅ URL 200 |
| D6 | **Go** | `syscall` · `cgo` | C köprüsü | servis/araç katmanı; GC gecikme riski (gerçek-zamanlı) | #G6 | ✅ URL 200 |
| D7 | **C#** | P/Invoke (`DllImport`) | marshalling | Windows yüzeyi (win-sw) için aday | #G7 | ✅ URL 200 |
| D8 | **PHP** | FFI (`FFI::cdef`) | preload FFI | web katmanı — donanıma **dolaylı** erişim; repo dili ≠ donanım dili | #G8 | ✅ URL 200 |
| D9 | **JavaScript/Node** | N-API eklentisi (native addon) | C++ eklenti | araç/prototip — K000 O2: üçüncü parti paket eklenmez | #G9 | ✅ URL 200 |
| D10 | **Swift** | `Darwin`/`Glibc` modülü + CoreAudio | doğrudan | Apple platformu adayı (macOS/iOS) | #G10 | ✅ URL 200 |
| D11 | **Java / Kotlin** | JNI (`native` / `external fun`) | Java VM üzerinden | Android yüzeyi adayı | #G11-#G12 | ✅ URL 200 |

### §5.1 Dil matrisi — erişim derinliği karşılaştırması

| # | Derinlik | Diller | Yorum (genel kavram) |
|---|---|---|---|
| E1 | Seviye-0 (ham register/IOCTL) | C · C++ · Rust · Zig | gerçek-zamanlı ses akışına uygun (kapsam §16) |
| E2 | Seviye-1 (FFI köprüsü) | Swift · C# · Go · Java/Kotlin · PHP(FFI) | ek kopyalama/marshalling maliyeti |
| E3 | Seviye-2 (VM/sarmalayıcı) | Python · JavaScript/Node | test/prototip — üretim akış yolu DEĞİL |
| E4 | CoreMusic seçimi | — | **UNKNOWN** — §5.4 + doğrulama §5.4 (API dili sorusu Z4) |

---

## §6 GRUP B — Dil Bazlı Kod Örnekleri

### §6.1 C — ham ioctl

```c
/* Kanıt: #G1 (§8 — HTTP 200 · 2026-10-14) */
/* [ORİJİNAL ÖRNEK — derlenmedi] */
#include <fcntl.h>
#include <sys/ioctl.h>
int fd = open("/dev/snd/controlC0", O_RDWR);
int rc = ioctl(fd, req, &arg);   /* req CoreMusic'te UNKNOWN (Q-A1) */
if (rc < 0) { /* errno → hata sınıfı kapsam §19 */ }
```

### §6.2 C++ — RAII sarmalayıcı (K001 stil örneği)

```cpp
/* Kanıt: #G2 (§8 — HTTP 200 · 2026-10-14) */
/* [ORİJİNAL ÖRNEK — derlenmedi] */
class DeviceHandle {
    int fd_ = -1;
public:
    explicit DeviceHandle(const char* p) : fd_(::open(p, O_RDWR)) {}
    ~DeviceHandle() { if (fd_ >= 0) ::close(fd_); }
    DeviceHandle(const DeviceHandle&) = delete;
    int ioctlCmd(unsigned long req, void* arg) noexcept {
        return fd_ < 0 ? -1 : ::ioctl(fd_, req, arg);
    }
};
```

### §6.3 Rust — extern "C" + unsafe FFI

```rust
/* Kanıt: #G3 (§8 — HTTP 200 · 2026-10-14) */
/* [ORİJİNAL ÖRNEK — derlenmedi] */
extern "C" {
    fn ioctl(fd: i32, req: u64, arg: *mut core::ffi::c_void) -> i32;
}
pub unsafe fn cmd(fd: i32, req: u64, arg: *mut u8) -> i32 {
    ioctl(fd, req, arg as *mut core::ffi::c_void)
}
/* unsafe sınırı yalnız bu fonksiyonda — üst katman güvenli sarmalayıcı ister */
```

### §6.4 Zig — @cImport C ABI

```zig
/* Kanıt: #G4 (§8 — HTTP 200 · 2026-10-14) */
/* [ORİJİNAL ÖRNEK — derlenmedi] */
const c = @cImport({
    @cInclude("fcntl.h");
    @cInclude("sys/ioctl.h");
});
pub fn cmd(fd: c_int, req: c_ulong, arg: *anyopaque) c_int {
    return c.ioctl(fd, req, arg);
}
```

### §6.5 Python — ctypes üzerinden erişim (test/araç katmanı)

```python
# Kanıt: #G5 (§8 — HTTP 200 · 2026-10-14)
# [ORİJİNAL ÖRNEK — derlenmedi]
import ctypes, os
libc = ctypes.CDLL("libc.so.6", use_errno=True)   # Windows'ta farklı yükleme — genel kavram
fd = os.open("/dev/snd/controlC0", os.O_RDWR)
rc = libc.ioctl(fd, req, arg)                      # req tipi/genişlik kapsam §7.2/R5
os.close(fd)
```

### §6.6 Go — syscall ile IOCTL (tek-OS çekirdeği)

```go
// Kanıt: #G6 (§8 — HTTP 200 · 2026-10-14)
// [ORİJİNAL ÖRNEK — derlenmedi]
package main

import "syscall"

func cmd(fd int, req uintptr, arg unsafe.Pointer) error {
	_, _, e := syscall.Syscall(syscall.SYS_IOCTL, uintptr(fd), req, uintptr(arg))
	if e != 0 { return e }
	return nil
}
// Çapraz-OS için cgo gerekir (URL: cgo) — gerçek-zamanlı akışta GC dikkati (genel kavram)
```

### §6.7 C# — P/Invoke marshalling

```csharp
/* Kanıt: #G7 (§8 — HTTP 200 · 2026-10-14) */
/* [ORİJİNAL ÖRNEK — derlenmedi] */
using System.Runtime.InteropServices;

static class Native {
    [DllImport("libc", SetLastError = true)]
    internal static extern int ioctl(int fd, UIntPtr req, byte[] arg);
}
/* Windows'ta karşılığı DeviceIoControl P/Invoke'tur (#4.4) — hatada
   Marshal.GetLastWin32Error() anında okunmalı (K000 kod-seti O4 benzeri disiplin) */
```

### §6.8 PHP — FFI ile sınır katmanı (repo dili)

```php
<?php
declare(strict_types=1);
/* Kanıt: #G8 (§8 — HTTP 200 · 2026-10-14) */
/* [ORİJİNAL ÖRNEK — derlenmedi] — production'da FFI.enable/preload koşulu
   K000 kod-seti O1 ile aynı açık sorudur */
$ffi = FFI::cdef("int ioctl(int fd, unsigned long request, void *arg);", "libc.so.6");
/* PHP CoreMusic'in web katmanıdır; donanıma erişim burada DEĞİL, API üzerinden (§7) */
```

### §6.9 JavaScript/Node — N-API eklenti konsepti

```js
/* Kanıt: #G9 (§8 — HTTP 200 · 2026-10-14) */
/* [ORİJİNAL ÖRNEK — derlenmedi] — [TASARIM]: gerçek erişim native C++ addon içindedir;
   üçüncü parti paket (koffi vb.) eklenmez (K000 kod-seti O2 kuralı). */
const { createRequire } = require("module");
// const addon = require("./build/Release/hwnode.node");  // addon YOK — PLANNED
```

### §6.10 Swift — Darwin modülü üzerinden

```swift
/* Kanıt: #G10 (§8 — HTTP 200 · 2026-10-14) */
/* [ORİJİNAL ÖRNEK — derlenmedi] */
#if canImport(Darwin)
import Darwin
#else
import Glibc
#endif
/* Darwin'de/CoreAudio erişimi §4.6-§4.8 bloklarıyla olur;
   ioctl yalnız Linux/BSD dalında anlamlıdır */
```

### §6.11 Java — JNI native yöntem bildirimi

```java
/* Kanıt: #G11 (§8 — HTTP 200 · 2026-10-14) */
/* [ORİJİNAL ÖRNEK — derlenmedi] */
public final class Hw {
    static { System.loadLibrary("hw"); }
    private static native int ioctl(int fd, long req, byte[] arg);
}
/* JNI köprüsü C tarafında implemente edilir — K001 sınırında değil, uygulama katmanında */
```

### §6.12 Kotlin — external fun (JNI ile aynı köprü)

```kotlin
/* Kanıt: #G12 (§8 — HTTP 200 · 2026-10-14) */
/* [ORİJİNAL ÖRNEK — derlenmedi] */
object Hw {
    init { System.loadLibrary("hw") }
    external fun ioctl(fd: Int, req: Long, arg: ByteArray): Int
}
/* Kotlin/Native farklı dünyadır (platform.posix) — bu dosyada kapsam dışı */
```

---

## §7 GRUP C — K001 API'ye Öneriler (Karar Girdisi — BAĞLAYICI DEĞİL)

> Bu bölüm **öneri** taşır; nihai kararlar Vault Steward'dır (Q-A1 · Q-A2 · Q-A3 · Z4).

| # | Öneri | Gerekçe | Dayanak | Karar durumu |
|---|---|---|---|---|
| N1 | Q-A1 için **çift kanal** mimarisi değerlendir: komut/durum = `ioctl` benzeri dar yüzey; envanter/ölçü = dosya/attribute benzeri geniş yüzey | Linux (`ioctl` + sysfs) ve Windows (`DeviceIoControl` + kayıt) ikisi de fiilen bu ayrımı kullanır — genel kavram | §3 OS1-OS2 · §4.1-§4.5 | **öneri** — Q-A1 açık |
| N2 | API'nin ses **akış** yolunu (WASAPI/ALSA/CoreAudio stream) K003'e, **kontrol** yolunu K001'e bırak | Akış ≠ kontrol ayrımı kapsam §4/C4 (PCM3168A iki ayrı arayüz) | kapsam §4/C4 · §4.5 | **öneri** |
| N3 | Dil kararı: K001 çekirdeği **C++20** (RAII · `noexcept` · zero-allocation kuralı); araç/test katmanı Python; web köprüsü PHP FFI **istemiyor** (repo dili ≠ donanım dili) | §5.1 derinlik tablosu; repo PHP ≥8.4 kanıtı (doğrulama §5.3) | §5/D2 · §6.2 · §6.8 | **öneri** — Z4 (API dili) açık |
| N4 | Çapraz-platform hedef için Windows (win-sw) + macOS (CoreAudio) + Linux (ALSA) üçlüsü referans alınır; iOS/Android/Tizen/webOS **kapsam dışı bırakılır** (dar kapsamlı satırlar) | K001'in masaüstü ses odağı (anayasa) | §3 OS4-OS11 | **öneri** — kapsam §2'de değil, genişletme kapısı |
| N5 | IOCTL komut seti (§17.3 O1-O6) yazılırken her komut için **salt-okunur/yazma** niteliği register metadata tablosundan gelmeli | kapsam §7.2 R1-R5 register kuraları | kapsam §7.2 | **öneri** — register haritası dosyası yok (kapsam §10.2) |

### §7.1 Açık soru bağlantısı (değişmez)

| Soru | Bu dosyanın konumu |
|---|---|
| Q-A1 (protokol) | Seçim YOK — §3/§4 tüm dalları açık taşınır |
| Q-A3 (çağrı listesi) | §4 bloklarında `req`/`dwCode` yer tutucuları **UNKNOWN** olarak bırakıldı |
| Z4 (API dili) | §7/N3 yalnız **öneri** der; doğrulama §5.4 UNKNOWN kalır |
| K1-K5 (bu dosya açık soruları) | frontmatter `open_questions` |

---

## §8 URL ENVANTERİ (2026-10-14 · HEAD/GET · hepsi HTTP 200 · envanter kesişimi 0 / 2.730)

### §8.1 OS kanıtları (19 URL)

| # | URL | Kullanım | 2026-10-14 |
|---|---|---|---|
| #L1 | https://pubs.opengroup.org/onlinepubs/9699919799/functions/ioctl.html | §4.1 · POSIX ioctl | ✅ 200 |
| #L2 | https://docs.kernel.org/sound/kernel-api/alsa-driver-api.html | §4.2 · ALSA sürücü API | ✅ 200 |
| #L3 | https://docs.kernel.org/filesystems/sysfs.html | §4.3 · sysfs | ✅ 200 |
| #W1 | https://learn.microsoft.com/en-us/windows/win32/api/ioapiset/nf-ioapiset-deviceiocontrol | §4.4 · IOCTL | ✅ 200 |
| #W2 | https://learn.microsoft.com/en-us/windows/win32/api/audioclient/nn-audioclient-iaudiorenderclient | §4.5 · WASAPI render | ✅ 200 |
| #W3 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/stream-routing | OS2 akış yönlendirme | ✅ 200 |
| #M1 | https://developer.apple.com/library/archive/samplecode/HALExamples/Listings/ConfigDefaultOutput_c.html | §4.6 · CoreAudio HAL | ✅ 200 |
| #M2 | https://developer.apple.com/documentation/coreaudio/kaudiohardwarepropertydefaultoutputdevice | §4.7 · güncel property | ✅ 200 |
| #I1 | https://developer.apple.com/documentation/avfaudio/avaudiosession/setcategory(_:options:) | §4.8 · iOS session | ✅ 200 |
| #I2 | https://developer.apple.com/library/archive/documentation/Audio/Conceptual/AudioSessionProgrammingGuide/AudioSessionBasics/AudioSessionBasics.html | OS4 kategori/mode verisi | ✅ 200 |
| #A1 | https://developer.android.com/ndk/reference/group/audio | §4.9 · AAudio | ✅ 200 |
| #F1 | https://docs.freebsd.org/en/books/handbook/multimedia/ | §4.10 · FreeBSD OSS | ✅ 200 |
| #R1 | https://docs.zephyrproject.org/latest/services/index.html | §4.11 · Zephyr kök | ✅ 200 |
| #R2 | https://www.keil.com/pack/doc/CMSIS/DSP/html/index.html | §4.12 · CMSIS-DSP | ✅ 200 |
| #B1 | https://developer.mozilla.org/en-US/docs/Web/API/WebHID_API | §4.13 · WebHID | ✅ 200 |
| #B2 | https://developer.mozilla.org/en-US/docs/Web/API/WebUSB_API | §4.14 · WebUSB | ✅ 200 |
| #B3 | https://chromeos.dev/en/linux | OS9 · Crostini/Linux kabı | ✅ 200 |
| #T1 | https://docs.tizen.org/application/native/guides/multimedia/audio-management/ | §4.15 · Tizen CAPI | ✅ 200 |
| #O1 | https://webostv.developer.lge.com/develop/guides/acg-guide | §4.16 · webOS Luna ACG | ✅ 200 |

### §8.2 Dil kanıtları (12 URL)

| # | URL | Kullanım | 2026-10-14 |
|---|---|---|---|
| #G1 | https://en.cppreference.com/w/c | §6.1 · C | ✅ 200 |
| #G2 | https://en.cppreference.com/w/cpp | §6.2 · C++ | ✅ 200 |
| #G3 | https://doc.rust-lang.org/nomicon/ | §6.3 · Rust unsafe/FFI | ✅ 200 |
| #G4 | https://ziglang.org/learn/overview/ | §6.4 · Zig | ✅ 200 |
| #G5 | https://docs.python.org/3/library/ctypes.html | §6.5 · Python | ✅ 200 |
| #G6 | https://pkg.go.dev/cmd/cgo | §6.6 · Go cgo | ✅ 200 |
| #G7 | https://learn.microsoft.com/en-us/dotnet/standard/native-interop/ | §6.7 · C# P/Invoke | ✅ 200 |
| #G8 | https://www.php.net/manual/en/book.ffi.php | §6.8 · PHP FFI | ✅ 200 |
| #G9 | https://nodejs.org/api/n-api.html | §6.9 · Node N-API | ✅ 200 |
| #G10 | https://developer.apple.com/documentation/swift | §6.10 · Swift | ✅ 200 |
| #G11 | https://docs.oracle.com/en/java/javase/21/docs/specs/jni/intro.html | §6.11 · JNI | ✅ 200 |
| #G12 | https://kotlinlang.org/docs/java-interop.html | §6.12 · Kotlin | ✅ 200 |

**Yöntem:** `Invoke-WebRequest -Method Head -TimeoutSec 15 -UseBasicParsing -UserAgent 'Mozilla/5.0'`
(gerekirse GET yedeği). Kesişim kontrolü: `url-inv-all.txt` (2.730 URL) — **8 aday çakıştı ve
değiştirildi**; tablodaki 31 URL'nin tamamı envanter dışı (0 kesişim). Doğrulama zinciri:
`donanım-api-dogrulama.md` §25.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
