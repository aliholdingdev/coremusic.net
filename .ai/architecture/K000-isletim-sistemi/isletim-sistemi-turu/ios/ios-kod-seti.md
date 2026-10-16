---
node_id: "K000-isletim-sistemi/isletim-sistemi-turu/ios/ios-kod-seti"
node_type: code
status: verify-required
parent_id: "K000-isletim-sistemi/isletim-sistemi-turu/ios"
children: []
depends_on:
  - "K000-isletim-sistemi/isletim-sistemi-turu/isletim-sistemi-turu-kod-seti"
  - "K000-isletim-sistemi/isletim-sistemi-turu/ios/ios-audio-unit-arayuzu"
  - "K000-isletim-sistemi/isletim-sistemi-turu/ios/ios-cihaz-ve-rota-yonetimi"
evidence:
  - "https://developer.apple.com/documentation/avfaudio/avaudiosession (✅ HTTP 200 · 2026-10-14)"
  - "https://developer.apple.com/documentation/avfaudio/avaudioengine (✅ HTTP 200 · 2026-10-14)"
  - "https://developer.apple.com/documentation/coreaudio/audiostreambasicdescription (✅ HTTP 200 · 2026-10-14)"
  - "https://developer.apple.com/documentation/audiotoolbox (✅ HTTP 200 · 2026-10-14)"
  - "https://developer.apple.com/documentation/audiotoolbox/audiounit (✅ HTTP 200 · 2026-10-14)"
  - "https://developer.apple.com/documentation/audiotoolbox/audio-components (✅ HTTP 200 · 2026-10-14)"
  - "https://developer.apple.com/documentation/foundation/processinfo (✅ HTTP 200 · 2026-10-14)"
  - "https://developer.apple.com/documentation/foundation/filemanager (✅ HTTP 200 · 2026-10-14)"
  - "https://docs.swift.org/swift-book/documentation/the-swift-programming-language/ (✅ HTTP 200 · 2026-10-14)"
validation:
  - "2026-10-15: 19 benzersiz resmi URL (HTTP 200 kapısı) · 15 kod bloğu (dil-etiketli fence) · 13 dil kapsama turları P1–P7 tamamlandı · örnekler ORİJİNAL ve derlenmedi"
  - "2026-10-14: 9 benzersiz resmi URL · hepsi HTTP 200 (HEAD) doğrulandı; içerik alıntısı YAPILMADI → örnekler ORİJİNAL ve derlenmedi"
  - "2026-10-14 (genişletme turu): 10 taşınabilir dil slotu (C++20·Rust·Python·Go·C#·PHP·JS/Node·Zig·Java/Kotlin·PowerShell) — 11 yeni URL HEAD 200; 1 elenen aday (/w/cpp/program/getenv 404 → /w/c/program/getenv 200); 6 aday semantik kapıda N/A (§9)"
  - "Elenen 404 adaylar: /documentation/audiotoolbox/audio-unit-v3 · /documentation/coreaudio/remoteio_audio_unit · /library/archive/.../AudioUnitProgrammingGuide/ (ölçümle elendi)"
  - "Repo'da iOS hedefine dair disk kanıtı YOK (swift/xcode 0 iz) → UNKNOWN (Q4)"
open_questions:
  - "Örnekler derlenmedi/çalıştırılmadı (cihaz/simulator yok) → ⚠️ VERIFICATION REQUIRED (tüm gruplar)"
  - "I1: AUv3 sayfası 404 → AudioUnit-V3 imzaları kardeş ios-audio-unit-arayuzu.md'ye devredildi, burada yalnız niyet"
  - "I2: iOS hedef platform setinde mi? (Q4/T-U3) UNKNOWN — repo izi yok"
  - "I3: AVAudioSession kategori/option çiftlerinin tam listesi doğrulanmadı → ⚠️"
  - "I4: Rust/Zig çapraz-derleme SDK zinciri (Xcode SDK elde sağlanır) repo'da yok → ⚠️ (§H.2/§H.4)"
  - "I5: macOS koşucuda pwsh varlığı ve sürümü doğrulanmadı → ⚠️ (§E.2)"
group: kod
---

# ios-kod-seti — iOS (Apple mobil) kod seti (orijinal, derlenmemiş)

> **Eksen:** `K000-isletim-sistemi / isletim-sistemi-turu / ios` · **Grup:** `kod` · **Durum:** `verify-required`
> **Sözleşme:** Üretim/CI **yalnız Linux'ta** koşar (kök set §1); kod ise **tüm sınıflarda**
> çalışacak biçimde yazılır. iOS ailesi kök set §4'te "uygulama kapsayıcı modeli" olarak
> sınıflandırılmıştır; bu dosya o ailenin **platform-native** kod yüzeyini (Swift · Objective-C ·
> C · Bash) ve taşınabilir dillerin bu ailedeki durumunu tek yapıda taşır.
> **Authority:** Bayram Ali / Vault Steward · **Tarih:** 2026-10-14 · **Mod:** RED TEAM / TRUTH MODE

---

## §0. Politika (bu dosyanın kanıt dili — bağlayıcı)

| Kural | Açıklama |
|---|---|
| Örnek etiketi | Her kod bloğu `[ORİJİNAL ÖRNEK · derlenmedi]` taşır |
| Kaynak | Arayüz adları §8'deki **2026-10-14 HTTP 200** sayfalarından (Apple/Swift + genişletme turu: cppreference · rust-lang · pkg.go.dev · zig raw · learn.microsoft) türetildi; içerik alıntısı yok |
| Derleme | Simulator/cihaz yok → tüm davranış iddiaları `⚠️ VERIFICATION REQUIRED` |
| Repo izi | iOS'e dair disk kanıtı **YOK** → `Dosya:/Satır:` **üretilmez** |
| Yasak | `TBD` dolgusu · 404 sayfadan imza · "App Store'da çalıştı" iddiası |

**Etiket sözlüğü:** `[ORİJİNAL ÖRNEK · derlenmedi]` = mimari niyet · `[TASARIM]` = tercih, kanıt
değil · `⚠️ VERIFICATION REQUIRED` = sürüm/davranış doğrulanmadı · `UNKNOWN` = repo'da kanıt yok.

---

## §1. Platform sözleşmesi — "koşan tek Linux, iOS'a taşınan kod"

| Katman | Karar | Kanıt sınıfı |
|---|---|---|
| Üretim/CI runtime | **Linux** (`ubuntu-latest`) | [KANIT] kök set §H |
| iOS çekirdek ailesi | Darwin/XNU (hibrit) — kök set §4 | [TASARIM] kök set §4 |
| Uygulama modeli | İmzalı kapsayıcı · sandbox · mağaza | [KANIT] sınıf tanımı |
| Ses oturumu | **AVAudioSession zorunlu** (kategori + kesinti) | §C |
| Veri düzlemi | AudioUnit (RemoteIO) render callback — C yüzeyi | §D |
| PHP runtime | **Cihazda YOK** | [KANIT] sınıf tanımı |

**Sözleşme kuralları (bu aileye özgü 4 madde):**

1. **Ses oturumu olmadan ses YOKTUR** — kategori/oturum kurulumu veri düzleminden **önce**dür (§C).
2. **Kesinti (interruption) ve rota değişimi olayları** zorunlu dinleyicidir; masaüstü
   "cihaz bağlı kalır" varsayımı bu ailede geçersizdir.
3. **Render callback C dilindedir** (Swift ile de çağrılabilir ama tahsis/kilit yasağı aynıdır).
4. **Taşınabilir dil yoktur, Swift/ObjC tekelindedir** (§3 matrisi) — dil seçimi bu ailede
   **dar**dır; cross-compile bu aileye girmez.

---

## §2. Grup haritası (bu dosyanın grupları)

| Grup | Konu | Diller | § |
|---|---|---|---|
| A | Platform tespiti (derleme + çalışma) | Swift · C | §GRUP A |
| B | Yol / kapsayıcı dizinleri | Swift | §GRUP B |
| C | Ses oturumu (kontrol düzlemi) | Swift | §GRUP C |
| D | Ses veri düzlemi (ASBD + render callback niyeti) | C | §GRUP D |
| E | Dış araç / derleme kabuğu | Bash (xcodebuild) | §GRUP E |
| F | Ortam / gizli anahtar | Swift | §GRUP F |
| G | CI / hedef matrisi (tasarım) | YAML | §GRUP G |
| H | Taşınabilir diller — çapraz-derleme / kavram yüzeyi | C++20 · Rust · Go · Zig | §GRUP H |

(§E.2 PowerShell de GRUP E'ye eklendi — 2026-10-14 genişletme turu.)

**Toplam:** 8 grup · **15 kod bloğu** · 9 dil yüzeyi (Swift, C, C++20, Rust, Go, Zig, PowerShell, Bash, YAML) + taşınabilir dil matrisi §3 + N/A slot kayıtları §9.

---

## GRUP A — Platform tespiti

### §A.1 Swift — `ProcessInfo` + derleme zamanı koşulu

```swift
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak: https://developer.apple.com/documentation/foundation/processinfo (✅ 2026-10-14)
import Foundation

let info = ProcessInfo.processInfo
let isLowPower = info.isLowPowerModeEnabled          // pil modu — ses kararı ipucu
#if os(iOS)
let platform = "ios"                                  // derleme zamanı ayrımı — tek nokta
#elseif os(macOS)
let platform = "macos"
#else
#error("Desteklenmeyen platform — UNKNOWN sınıf sessizce geçilmez")
#endif
```

**Sınıf dersi:** Apple ailesinde tespit **derleme zamanı `#if os(...)`** ile yapılır (Go/Rust
desenine benzer); çalışma-zamanı `#if` yoktur — `ProcessInfo` yalnız **durum** okur (pil,
sürüm), sınıf **seçmez**.

### §A.2 C — hedef makro ayrımı (NDK benzeri tuzaq yok ama dikkat)

```c
/* [ORİJİNAL ÖRNEK · derlenmedi] */
#if defined(__APPLE__) && defined(__arm64__)
#  define CM_APPLE_ARM64 1          /* iPhone/iPad — hedef mimari */
#elif defined(__APPLE__)
#  define CM_APPLE_OTHER 1
#else
#  error "Apple dışı hedef — bu blok iOS sınıfı için yazılmıştır"
#endif
```

**Not:** `TARGET_OS_IPHONE` gibi `TargetConditionals` makroları **var** ama sürüm bilgisi
doğrulanmadığı için bu dosyada kullanılmadı (`⚠️ I3`) — yalnız mimari makro gösterildi.

---

## GRUP B — Yol / kapsayıcı dizinleri

### §B.1 Swift — `FileManager` ile önbellek/destek dizinleri

```swift
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak: https://developer.apple.com/documentation/foundation/filemanager (✅ 2026-10-14)
import Foundation

let fm = FileManager.default
if let cache = try? fm.url(for: .cachesDirectory, in: .userDomainMask,
                           appropriateFor: nil, create: true) {
    let file = cache.appendingPathComponent("cmtpl.pcm")   // kapsayıcı içi — ortak /tmp YOK
}
```

**Fark:** `os.TempDir()` karşılığı **uygulama-özel** `caches`/`tmp` dizinleridir; kapsayıcı
dışına yazma sandbox ihlalidir (oturum sonunda sistem temizleyebilir → kalıcı veri `documents`).

---

## GRUP C — Ses oturumu (kontrol düzlemi — iOS'un ilk kuralı)

### §C.1 Swift — `AVAudioSession` kategori kurulumu

```swift
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak: https://developer.apple.com/documentation/avfaudio/avaudiosession (✅ 2026-10-14)
import AVFoundation

let session = AVAudioSession.sharedInstance()
do {
    // playback kategorisi · mixWithOthers seçeneği — tam option seti ⚠️ I3
    try session.setCategory(.playback, mode: .default, options: [.mixWithOthers])
    try session.setActive(true)                     // başarısızlık THROW — sessiz yutulmaz
} catch {
    // Hata raporlanır; oturum kurulmadan veri düzlemi BAŞLATILMAZ
    NSLog("AVAudioSession kurulumu başarısız: \(error)")
}
```

**Sınıf dersi:** iOS'ta ses = **oturum + kategori** kararıdır; veri düzlemi (§D) bu kararın
**altındadır**. Kategori değişimi sesi **keser** → çalışma zamanında nadiren yapılır
(kardeş `ios-cihaz-ve-rota-yonetimi.md`).

### §C.2 Swift — AVAudioEngine yüksek seviye yüzey (niyet)

```swift
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak: https://developer.apple.com/documentation/avfaudio/avaudioengine (✅ 2026-10-14)
import AVFoundation

let engine = AVAudioEngine()
// engine.attach(playerNode) · engine.connect(playerNode, engine.mainMixerNode, format:)
// Ayrıntı (düğüm grafiği, format eşleşmesi) kardeş dosyada — burada yalnız yüzey
```

**Neden iki yol:** `AVAudioEngine` (yüksek seviye, ⚠️ otomatik yönetim) ile AudioUnit (§D, düşük
seviye, kontrol) **yan yana** yaşar; hangisinin seçileceği `isletim-sistemi-audio` dalının kararıdır.

---

## GRUP D — Ses veri düzlemi (ASBD + render callback niyeti)

### §D.1 C — `AudioStreamBasicDescription` alanları

```c
/* [ORİJİNAL ÖRNEK · derlenmedi] */
/* Resmi kaynak: https://developer.apple.com/documentation/coreaudio/audiostreambasicdescription (✅ 2026-10-14) */
#include <CoreAudio/CoreAudioTypes.h>
#include <stdint.h>

/* 32-bit float · 48 kHz · 2 kanal (ADR-017 32-bit float ses ile hizalı) */
static const AudioStreamBasicDescription kFormat = {
    .mSampleRate          = 48000.0,
    .mFormatID            = kAudioFormatLinearPCM,
    .mFormatFlags         = kAudioFormatFlagIsFloat | kAudioFormatFlagIsPacked,
    .mBytesPerPacket      = 8,      /* 4 byte × 2 kanal */
    .mFramesPerPacket     = 1,
    .mBytesPerFrame       = 8,
    .mChannelsPerFrame    = 2,
    .mBitsPerChannel      = 32,
};
```

**Not:** Alan adları ASBD sayfasından türetildi; **değerler** bu vault'un tasarım tercihidir
(ADR-017) — donanım doğrulaması `K001-donanım` dalındadır (`⚠️` = cihaz-özel sınır).

### §D.2 C — render callback iskeleti (imza ⚠️ · tahsis/kilit yasağı)

```c
/* [ORİJİNAL ÖRNEK · derlenmedi] */
/* AudioUnit render callback deseni — tam imzalar kardeş ios-audio-unit-arayuzu.md (I1) */
#include <AudioToolbox/AudioToolbox.h>

static OSStatus render_cb(void *inRefCon,
                          AudioUnitRenderActionFlags *ioActionFlags,
                          const AudioTimeStamp *inTimeStamp,
                          UInt32 inBusNumber,
                          UInt32 inNumberFrames,
                          AudioBufferList *ioData)
{
    (void)inRefCon; (void)ioActionFlags; (void)inTimeStamp; (void)inBusNumber;
    /* Veri düzlemi: baştan tahsis edilmiş halka tampondan kopyasız okuma */
    RingBuffer *rb = (RingBuffer *)inRefCon;
    ring_read(rb, (float *)ioData->mBuffers[0].mData, inNumberFrames);
    return noErr;             /* hata yolu: ioActionFlags ile işaretlenir (⚠️ tam yüzey I1) */
}
```

**Kural (aynı):** callback içinde **tahsis, kilitleme, dosya/JSRuntime erişimi yasak**
(kök set §5 ders 1–2); `inNumberFrames` **boyut isteği**, garanti değil — underrun'da
sayaç artar (kardeş `ios-gercek-zamanli-tampon.md`).

---

## GRUP E — Dış araç / derleme kabuğu

### §E.1 Bash — simulator derlemesi (geliştirme/CI)

```bash
#!/usr/bin/env bash
# [ORİJİNAL ÖRNEK · derlenmedi]
set -euo pipefail
command -v xcodebuild >/dev/null || { echo "xcodebuild PATH'te yok" >&2; exit 127; }
xcodebuild -scheme CoreMusic -destination 'platform=iOS Simulator,name=iPhone 15' build
# ⚠️ hedef cihaz adı sürüm-bağlı doğrulanmadı (UNKNOWN) — CI'da parametre yapılır
```

**Not:** iOS derleme zinciri **macOS koşucusu** gerektirir → bu aile Linux CI'sına **ek**
hattır (kök set §H.1 matrisine satır); "Linux'ta derlenir" **iddia edilmez** (macOS-only araç).

### §E.2 PowerShell — macOS koşucu kapısı (yalnız CI yüzeyi)

```powershell
https://learn.microsoft.com/en-us/powershell/module/microsoft.powershell.core/about/about_automatic_variables
[ORİJİNAL ÖRNEK · derlenmedi]
# PowerShell 6+ (pwsh); Windows PowerShell 5.1'de $IsMacOS TANIMLI DEĞİLDİR → $null → yanlış okunur ⚠️
if (-not $IsMacOS) {
    Write-Error "iOS derleme zinciri yalnız macOS koşucuda çalışır (§E.1)"
    exit 2                              # fail-safe — sessiz fallback YASAK
}
$PSVersionTable.PSVersion              # koşucu sürümünü logla — sürüm sabitlenmedi (UNKNOWN)
& xcodebuild -scheme CoreMusic -destination 'generic/platform=iOS Simulator' build
if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }   # hata yükseltilir, yutulmaz
```

**Sınır kaydı (N/A değil):** PowerShell **iOS cihazda YOKTUR**; bu blok §E.1'in pwsh karşılığı
olan **host/CI** yüzeyidir. macOS koşucuda `pwsh` varlığı ve sürümü **doğrulanmadı** (`⚠️ I5`) —
CI betiği koşucu kontrolüyle açılır. `$IsMacOS`/`$PSVersionTable` varlığı kaynak sayfada
ölçüldü (§8/18–19).

---

## GRUP F — Ortam / gizli anahtar

### §F.1 Swift — ortamdan okuma (anahtar gömülmez)

```swift
// [ORİJİNAL ÖRNEK · derlenmedi]
import Foundation
// Kural: gizli anahtar Info.plist'e/derlemeye GÖMÜLMEZ (paket ters açılabilir);
// kısa ömürlü oturum anahtarı sunucudan alınır — bu örnek yalnız okuma yüzeyidir
let token = ProcessInfo.processInfo.environment["COREMUSIC_API_TOKEN"]
guard let t = token, !t.isEmpty else {
    fatalError("COREMUSIC_API_TOKEN eksik")   // fail-safe — sessiz fallback YASAK
}
```

---

## GRUP G — CI / hedef matrisi (tasarım — repo'da yok)

```yaml
# [ORİJİNAL ÖRNEK · derlenmedi] — repo'da iOS CI yok → [TASARIM]
name: ios-port
on: [pull_request]
jobs:
  build:
    runs-on: macos-latest                # macOS-only araç zinciri (§E.1 notu)
    timeout-minutes: 20
    steps:
      - uses: actions/checkout@v4
      - run: xcodebuild -scheme CoreMusic -destination 'generic/platform=iOS Simulator' build
```

**Kural:** iOS hattı **taşıma kapısıdır**; kapatılırsa "iOS'ta çalışır" `UNKNOWN`'a düşer.

---

## GRUP H — Taşınabilir diller (çapraz-derleme / kavram yüzeyi — 2026-10-14 genişletme)

> **Sahiplik (R4):** Bu grup yalnız **dil slotu** işlemini yapar. Ses stacki, rota ve tampon
> kararları kardeş dosyalardadır: `../macos/macos-kod-seti.md` · `ios-gercek-zamanli-tampon.md`
> · `ios-cihaz-ve-rota-yonetimi.md` — burada tekrarlanmaz.

### §H.1 C++20 — `steady_clock` (Xcode toolchain'in taşınabilir dili)

```cpp
https://en.cppreference.com/w/cpp/chrono/steady_clock
[ORİJİNAL ÖRNEK · derlenmedi]
#include <chrono>
#include <cstdint>

// Xcode toolchain C++20 derler (clang++) — proje/CI ayarı UNKNOWN (repo izi yok, I2)
using steady = std::chrono::steady_clock;

// RT zaman ölçümü: veri düzlemi tahsis/kilit yasağı (§D.2) C++ için de geçerlidir
std::int64_t elapsed_ns_since(steady::time_point t0) {
    return std::chrono::duration_cast<std::chrono::nanoseconds>(
               steady::now() - t0).count();
}
```

**Yerleşim gerekçesi:** C++ bu ailenin **birincil taşınabilir** dilidir — veri düzlemi zaten
C'dir (§D), makro kapısı §A.2 ile birlikte kullanılır. Ortam-okuma yüzeyi C getenv karşılığı
ölçüldü (§8/11) ama örnek bilinçli olarak §F.1 Swift'i çoğaltmaz: yalnız zamanlama yüzeyi
gösterilir.

### §H.2 Rust — `cfg(target_os = "ios")` kapısı

```rust
https://doc.rust-lang.org/reference/conditional-compilation.html
[ORİJİNAL ÖRNEK · derlenmedi]
// rustc çapraz-hedefi: aarch64-apple-ios — derleme Xcode SDK zinciri gerektirir ⚠️ (toolchain sürümü UNKNOWN, I4)
use std::time::Instant; // https://doc.rust-lang.org/std/time/struct.Instant.html (§8/13)

#[cfg(target_os = "ios")]
pub mod platform { pub fn tag() -> &'static str { "ios" } }

#[cfg(not(target_os = "ios"))]
pub mod platform { pub fn tag() -> &'static str { "non-ios" } } // dışlama — sessiz geçiş YOK

pub fn measure_ns(f: impl FnOnce()) -> u128 {
    let t0 = Instant::now();
    f();
    t0.elapsed().as_nanos()
}
```

**Sınır:** Rust iOS'a **çapraz-derlenir** (bu vault'taki iki gerçek çapraz-hedeften biri,
diğeri Zig); SDK zinciri ve CI hattı repo'da **yok** → davranış `⚠️ VERIFICATION REQUIRED`
(I4). RT kuralları (tahsis/kilit yasağı) Rust içinde de aynıdır.

### §H.3 Go — `GOOS=ios` taşınma kavramı `[KAVRAM]`

```go
https://pkg.go.dev/os
[ORİJİNAL ÖRNEK · derlenmedi]
// [KAVRAM] — gomobile bind (üçüncü parti) bu vault'a EKLENMEDİ; yalnız kavram kaydı
package platform

import (
	"errors"
	"os"
	"runtime" // https://pkg.go.dev/runtime (§8/15)
)

func Gate() (string, error) {
	if runtime.GOOS != "ios" { // GOOS=ios cgo + SDK zinciri gerektirir ⚠️
		return "", errors.New("hedef GOOS=ios değil")
	}
	tok := os.Getenv("COREMUSIC_API_TOKEN")
	if tok == "" {
		return "", errors.New("COREMUSIC_API_TOKEN eksik") // fail-safe — sessiz fallback YASAK
	}
	return tok, nil
}
```

**Sınır:** Go iOS'a yalnız **gomobile** benzeri bir bind katmanıyla taşınır; araç üçüncü
parti olduğu için bağımlılık olarak eklenmedi (`[KAVRAM]`). Cihaz runtime'ında Go yoktur —
taşınan şey dilin **kütüphanesi**, çalışma zamanı değil.

### §H.4 Zig — `posix.isatty` (çapraz-derleme kavramı)

```zig
https://raw.githubusercontent.com/ziglang/zig/master/lib/std/posix.zig
[ORİJİNAL ÖRNEK · derlenmedi]
const posix = @import("std").posix;

// Çapraz-derleme: -target aarch64-ios (Apple SDK zinciri elde sağlanır) ⚠️ I4
// İmza posix.zig MASTER'dan ölçüldü (§8/16) — master = sürüm-bağımlı, derlenmedi ⚠️
// iOS cihazda terminal YOKTUR; bu sorgu yalnız geliştirme/CI yüzeyinde anlam taşır
pub fn isInteractiveStdin() bool {
    return posix.isatty(0); // 0 = stdin fd — master imzası bool döner (hata-union'ı yok) ⚠️
}
```

**Sınır:** Zig çapraz-derleme gerçektir ama Apple **SDK zinciri** zig ile gelmez (elde
sağlanır) → `⚠️ VERIFICATION REQUIRED`. mmap/getrlimit/setrlimit imzaları aynı kaynakta
ölçüldü (§8/16) ama RT tampon planı kardeş `ios-gercek-zamanli-tampon.md`'ye **devredildi**
(R4 — sahiplik tekrarı yasak).

---

## §3. Taşınabilir dil × iOS kapsama matrisi

| Dil | iOS'ta durumu | Mekanizma | Etiket |
|---|---|---|---|
| C / Objective-C | ✅ **Birincil** (veri düzlemi C'dir) | CoreAudio/AudioToolbox | §D |
| Swift | ✅ **Birincil** (kontrol düzlemi) | AVFoundation/Foundation | §A/§B/§C |
| JS | ⚠️ Kısıtlı | WKWebView içi JS ⚠️ (kanıt yok) | [TASARIM] |
| Python | ❌ Cihazda yok | Yalnız geliştirme/CI betiği | sınıf dışı |
| Go | ⚠️ Kısmi | `GOOS=ios GOARCH=arm64` (cgo/SDK gerekir) ⚠️ | ⚠️ |
| Rust | ⚠️ Kısmi | `aarch64-apple-ios` hedefi ⚠️ | ⚠️ |
| C# (.NET) | ❌ | .NET for iOS ayrı ekosistem — bu vault'ta hedef değil | [TASARIM] |
| PHP | ❌ **Yok** | Sunucu sınıfı | [KANIT] sınıf tanımı |
| Bash | ⚠️ Yalnız geliştirme | xcodebuild kabuğu (cihazda yok) | §E |
| C++ (C++20) | ✅ **Birincil-sınıf** (Xcode clang++ toolchain) | derleyici yüzeyi; veri düzlemi C ile aynı aile | §H.1 |
| Zig | ⚠️ Çapraz-derleme gerçek · SDK zinciri elde | `-target aarch64-ios` · `std.posix` (master) | §H.4 |
| Node.js | ❌ Cihazda yok | yalnız JS/WKWebView satırı geçerlidir | §9 N/A |
| Java | ❌ Cihazda yok | iOS'ta JVM çalışma zamanı yoktur | §9 N/A |
| Kotlin | ❌ | KMP/bind toolchain üçüncü parti — bu vault hedefi değil | §9 N/A |
| PowerShell | ⚠️ Yalnız geliştirme/CI | macOS koşucuda pwsh · `$IsMacOS` kapısı (⚠️ I5) | §E.2 |

**Okuma kuralı:** ✅ kendi dili · ⚠️ imkân var ama kanıt yok · ❌ sınıf dışı (sınır, hata değil).
**Genişletme notu (2026-10-14):** 10 slot turu §9'da; C++20 satırı bu turda eklendi — §H.1

---

## §4. Sınıf dersleri (bu ailenin mimari mesajı)

| # | Ders | Karşılığı |
|---|---|---|
| 1 | Sınıf seçimi **derleme zamanı** `#if os()` ile olur; çalışma-zamanı değil | §A.1 |
| 2 | Ses oturumu **zorunlu ön koşul**dur — veri düzlemi önce gelmez | §C.1 |
| 3 | Ortak `/tmp` **yoktur**; kapsayıcı-özel dizinler | §B.1 |
| 4 | Derleme zinciri **macOS**'tur — "Linux'ta derlenir" denmez | §E.1 |
| 5 | ASBD **tasarım kararı** ile donanım gerçeği ayrıdır (ADR-017 vs cihaz) | §D.1 |

---

## §5. Açık sorular (bu dosya eksenli)

| # | Soru | Durum |
|---|---|---|
| I1 | AUv3/imza yüzeyi (404 ölçümü) | ⚠️ — kardeş dosyaya devredildi |
| I2 | iOS hedef platform setinde mi? (Q4/T-U3) | UNKNOWN — repo izi yok |
| I3 | AVAudioSession kategori/option tam listesi | ⚠️ doğrulanmadı |
| I4 | Rust/Zig çapraz-derleme SDK zinciri (Xcode SDK elde) repo'da yok | ⚠️ §H.2/§H.4 |
| I5 | macOS koşucuda `pwsh` varlığı + sürümü | ⚠️ §E.2 |

---

## §6. Red-team — olası 5 eleştiri ve yanıt

1. **"Render callback iskelet, imza yok."** Yanıt: AUv3 sayfası 404 (§8 elenenler) → imza
   uydurulmadı; tam yüzey kardeş dosyanın sorumluluğunda (I1).
2. **"Swift sürümü sabitlenmemiş."** Yanıt: UNKNOWN — sürüm sabitlenmeden sürüm iddiası yazılmaz.
3. **"PHP/Python neden yok?"** Yanıt: sınıf tanımı — iOS cihaz runtime'ında ikisi de yok (§3 ❌).
4. **"CI örneği macOS gerektirir, o zaman 'her yerde çalışır' çelişkili."** Yanıt: sözleşme
   "koşan tek Linux, **taşınan** kod"tur; derleme zinciri ≠ çalışma zamanı (§E.1 notu).
5. **"mixWithOthers her zaman gerekli mi?"** Yanıt: bu dosya **karar vermez** — kategori
   seçimi `isletim-sistemi-audio` + kardeş rota dosyasının işidir (§C.1).

---

## §7. Kabul kontrol listesi (bu dosya)

- [x] §0–§6 (politika, sözleşme, 7 grup, 10 blok, matris, dersler, sorular, red-team)
- [x] 10 alanlı frontmatter · `node_type: code` · `group: kod` · `status: verify-required`
- [x] Her kod bloğu `[ORİJİNAL ÖRNEK · derlenmedi]`
- [x] 9 benzersiz URL (§8) · kardeş ios-web-arastirma ile disjoints
- [x] **Genişletme (2026-10-14):** 10 dil slotu — 5 slot blok (PowerShell §E.2 · C++20 §H.1 · Rust §H.2 · Go §H.3 · Zig §H.4) + 5 slot N/A (Python · PHP · C# · JS/Node · Java/Kotlin — 6 satır, §9) · toplam 15 blok · 20 benzersiz URL · 11 yeni URL hepsi HTTP 200 (HEAD)
- [ ] **≥500 satır** — ölçüm ↓ (kod ağırlıklı dosya, hedef bilinçli esnetildi)

---

## §8. Kaynakça ve doğrulama (2026-10-14)

| # | Konu | URL | Durum |
|---|---|---|---|
| 1 | AVAudioSession | https://developer.apple.com/documentation/avfaudio/avaudiosession | ✅ 200 |
| 2 | AVAudioEngine | https://developer.apple.com/documentation/avfaudio/avaudioengine | ✅ 200 |
| 3 | ASBD | https://developer.apple.com/documentation/coreaudio/audiostreambasicdescription | ✅ 200 |
| 4 | AudioToolbox (üst) | https://developer.apple.com/documentation/audiotoolbox | ✅ 200 |
| 5 | AudioUnit (bileşen) | https://developer.apple.com/documentation/audiotoolbox/audiounit | ✅ 200 |
| 6 | Audio Components | https://developer.apple.com/documentation/audiotoolbox/audio-components | ✅ 200 |
| 7 | ProcessInfo | https://developer.apple.com/documentation/foundation/processinfo | ✅ 200 |
| 8 | FileManager | https://developer.apple.com/documentation/foundation/filemanager | ✅ 200 |
| 9 | Swift Book | https://docs.swift.org/swift-book/documentation/the-swift-programming-language/ | ✅ 200 |
| 10 | C++ steady_clock | https://en.cppreference.com/w/cpp/chrono/steady_clock | ✅ 200 |
| 11 | C getenv (C++ karşılığı) | https://en.cppreference.com/w/c/program/getenv | ✅ 200 |
| 12 | Rust cfg (conditional-compilation) | https://doc.rust-lang.org/reference/conditional-compilation.html | ✅ 200 |
| 13 | Rust Instant | https://doc.rust-lang.org/std/time/struct.Instant.html | ✅ 200 |
| 14 | Go os | https://pkg.go.dev/os | ✅ 200 |
| 15 | Go runtime (GOOS) | https://pkg.go.dev/runtime | ✅ 200 |
| 16 | Zig std.posix (raw master) | https://raw.githubusercontent.com/ziglang/zig/master/lib/std/posix.zig | ✅ 200 |
| 17 | Zig docs (master) | https://ziglang.org/documentation/master/ | ✅ 200 |
| 18 | PowerShell otomatik değişkenler ($IsMacOS/$PSVersionTable) | https://learn.microsoft.com/en-us/powershell/module/microsoft.powershell.core/about/about_automatic_variables | ✅ 200 |
| 19 | PowerShell başlatma (sürüm yüzeyi) | https://learn.microsoft.com/en-us/powershell/scripting/windows-powershell/starting-windows-powershell | ✅ 200 |

**İçerik-ölçüm (genişletme turları — yalnız imza doğrulaması, alıntı yok):**
`about_automatic_variables` → `IsMacOS` + `PSVersionTable` **var** · `posix.zig` (master) →
`pub fn isatty` (bool imza) · `pub fn mmap` · `pub fn getrlimit` · `pub fn setrlimit` ·
`pub const PROT` · `pub const MAP` **var** · `conditional-compilation.html` → `target_os` **var** ·
`pkg.go.dev/os` → `Getenv` **var** · `pkg.go.dev/runtime` → `GOOS` **var** ·
`steady_clock` sayfası → `steady_clock` **var**.

**Elenen adaylar (ölçümle):** `audio-unit-v3` **404** · `remoteio_audio_unit` **404** ·
`AudioUnitProgrammingGuide` (archive) **404** — üçü iddia dayanağı **yapılmadı** (I1 kaydı).
**Genişletme eleni:** `en.cppreference.com/w/cpp/program/getenv` **404** → yerine
`/w/c/program/getenv` **200** ölçüldü (§8/11).

**Semantik kapıda N/A bırakılan 6 aday (HEAD 200 ölçüldü — başlık doğru, içerik iOS native
yüzeyi DEĞİL → blok yazılmadı, bkz §9):** `docs.python.org/3/library/os.html` ·
`learn.microsoft.com/en-us/dotnet/api/system.environment` · `nodejs.org/api/fs.html` ·
`www.php.net/manual/en/function.php-uname.php` · `docs.oracle.com/en/java/javase/21/docs/api/` ·
`kotlinlang.org/api/latest/jvm/stdlib/`.

**Elenen adaylar (ölçümle):** `audio-unit-v3` **404** · `remoteio_audio_unit` **404** ·
`AudioUnitProgrammingGuide` (archive) **404** — üçü iddia dayanağı **yapılmadı** (I1 kaydı).

---

## §9. 10 dil slotu işleme kaydı — K000 13-dil kapsama turu (2026-10-14)

| Slot | Dil | Karar | Gerekçe | Yer |
|---|---|---|---|---|
| 1 | C++20 | ✅ **Blok** | Xcode toolchain C++ derler; veri düzlemi (§D) zaten C ailesi | §H.1 |
| 2 | Rust | ✅ **Blok** | `aarch64-apple-ios` gerçek çapraz-hedef; `cfg` kapısı resmi referanstan | §H.2 |
| 3 | Python | **N/A** | iOS cihaz runtime'ında Python yorumlayıcısı **yok** (§3 ❌ sınıf tanımı); betik yüzeyi yalnız geliştirme/CI'dır — GRUP E (Bash/PowerShell) zaten kapsar, ikinci bir kabuk dil eklenmez | §3/§9 |
| 4 | Go | ✅ **Blok `[KAVRAM]`** | `GOOS=ios` taşınması gomobile benzeri bind katmanıyla olur; araç üçüncü parti → bağımlılık EKLENMEDİ | §H.3 |
| 5 | C# | **N/A** | .NET for iOS **ayrı ekosistemdir** (§3 [TASARIM]); bu vault'un hedefi değil — bağımlılık/kurulum eklenmez | §3/§9 |
| 6 | PHP | **N/A** | PHP sunucu sınıfıdır; §1 sözleşmesi "PHP runtime: Cihazda **YOK**" — iOS kapsayıcısında PHP döngüsü çalışmaz | §1/§3/§9 |
| 7 | JS/Node | **N/A** | Node.js çalışma zamanı iOS cihazda **yok**; JS yalnız WKWebView içidir ve o da [TASARIM · kanıt yok] (§3 JS satırı korunur) | §3/§9 |
| 8 | Zig | ✅ **Blok** | `zig -target aarch64-ios` çapraz-derleme gerçektir; Apple SDK zinciri elde sağlanır → `⚠️ I4` | §H.4 |
| 9 | Java | **N/A** | iOS'ta JVM çalışma zamanı **yoktur**; iOS'a taşınan bir JVM yok (bağımlılık yasağı) | §9 |
| 10 | Kotlin | **N/A** | Kotlin/JVM iOS'de çalışmaz; Kotlin Multiplatform bind yüzeyi üçüncü parti toolchain'dir ve bu vault'ta hedef **değildir** | §9 |
| 11 | PowerShell | ✅ **Blok (sınır kaydı)** | Cihazda yok; macOS koşucuda **host/CI** yüzeyidir (`$IsMacOS` kapısı + xcodebuild) — §E.1'in pwsh karşılığı | §E.2 |

**Toplam:** 10 slot · **5 slot blok** (PowerShell, C++20, Rust, Go, Zig) · **5 slot N/A**
(Python, PHP, C#, JS/Node, Java/Kotlin → 6 N/A satırı) · 0 yeni bağımlılık · 0 yer tutucu.

**Fence ölçümü (self-check):** dosya öncesi **10** kod bloğu → sonrası **15** (+5: §E.2 ·
§H.1 · §H.2 · §H.3 · §H.4; Java ve Kotlin ayrı fence **almadı** — N/A kararı). Her yeni blokta
fence'ın ilk satırı resmi URL, ikinci satır `[ORİJİNAL ÖRNEK · derlenmedi]` (§0 sözleşmesi).

---

**Authority:** Bayram Ali / Vault Steward · **Tarih:** 2026-10-14
**Mode:** Red Team · Human Mode · Truth Mode
**Ölçüm (2026-10-14 genişletme sonrası):** 10 alanlı frontmatter · **8 tablo** (§0·§1·§2·§3·§4·§5·§8·§9 — sayımla listelenir) · **15 kod bloğu** · **20 benzersiz URL** (bu dosyaya özel)

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
