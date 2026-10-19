---
title: "Neva Engine — Swift Kod Örnekleri (6 Örnek)"
type: reference
category: nevaengine
version: 1.0.0
status: active
authority: "Master Orchestrator → Embedded Engineer"
updated: 2026-10-19
---

# Neva Engine — Swift Kod Örnekleri

> **Durum:** v1.0.0 · derlenmedi/test edilmedi (araştırma MD) · K1-K6 uyumlu

**Zorunlu Bağlantılar:** [[11-cok-dilli-kod-ornekleri]] (index) · [[10-io-format-32bit-192khz-kurallari]] (K1-K6 SSOT) · [[06-gereksinimler-ve-farkliliklar]] (R16 · R18) · [[07-mimari-diyagram-kontrol-yuzeyleri]] (R10)

## §1 Bağlam

Swift, Neva iOS/macOS (R17) katmanının dilidir — CoreAudio/AVAudioEngine bağlamı. Tüm DSP
yolu **32-bit float** (K1-K6 — doc 10 SSOT) üzerinden akar; bu dosyadaki örnekler yalnız
**Foundation** (sin/cos/pow + Data/FileManager) ve dil standartları kullanır — AVAudioEngine
bu örneklere dahil DEĞİLDİR (R17 entegrasyonu ayrı katmandır).

| Kural | İçerik |
|---|---|
| K1 | Input (capture/loopback) **32-bit float** |
| K2 | Internal zincir **32-bit float** (`Float` = IEEE-754 tek) |
| K3 | Output **32-bit float** — 24-bit int'e düşüşte **TPDF dither zorunlu** |
| K4 | SR **44.1 / 48 / 88.2 / 96 / 176.4 / 192 kHz** · varsayılan **48 kHz** |
| K5 | Cihaz 192 sunmuyorsa → **cihazın en yüksek yerel SR'si** |
| K6 | Bit-perfect = **SR eşit + float32** |

> ⚠️ **Bu dosyadaki kod derlenmedi/test edilmedi; vault referans örneğidir.** Üretim kodu
> doc 01/03 planındaki Faz 0 build+test kapısından geçer. Yalnız Foundation + dil std
> API kullanılmıştır. Zero-hallucination: doğrulanamayan satır `⚠️ VERIFICATION REQUIRED`.

## §2 Örnek 1 — Biquad EQ (RBJ peaking + Direct Form I) · (R16 · K2)

**Dürüst sapma (R16 çift form):** §4 şeması RBJ peaking + Direct Form I ister; Swift
örneğinde tek form (DF1) verilir — Transposed DF-II C++/Rust dosyalarındaki ayrı sınıf
ile örneklendi. Burada bilerek yalnız DF1 tutuldu (dil idiomu: `mutating` metot + `struct`).

```swift
// NevaBiquad.swift — Foundation (sin/cos/pow), Float32 (K2)
import Foundation

struct BiquadCoeffs {
    var b0: Float = 1, b1: Float = 0, b2: Float = 0
    var a1: Float = 0, a2: Float = 0
}

/// RBJ peaking (Audio EQ Cookbook) — a0 = 1 normalize
func makePeaking(f0: Float, q: Float, gainDb: Float, fs: Float) -> BiquadCoeffs {
    let qc = min(max(q, 0.05), 32.0)
    let f0c = min(max(f0, 10.0), 0.49 * fs)         // Nyquist'e çarpmama
    let gdc = min(max(gainDb, -24.0), 24.0)

    let A = pow(10.0, gdc / 40.0)
    let w0 = 2.0 * Float.pi * (f0c / fs)
    let cosw0 = cos(w0)
    let alpha = sin(w0) / (2.0 * qc)
    let a0 = 1.0 + alpha / A

    return BiquadCoeffs(
        b0: (1.0 + alpha * A) / a0,
        b1: (-2.0 * cosw0) / a0,
        b2: (1.0 - alpha * A) / a0,
        a1: (-2.0 * cosw0) / a0,
        a2: (1.0 - alpha / A) / a0
    )
}

/// Direct Form I — 4 durum (R16 referans form; yüksek-Q'da DF2T tercih edilir)
struct BiquadDf1 {
    var c = BiquadCoeffs()
    private var x1: Float = 0, x2: Float = 0
    private var y1: Float = 0, y2: Float = 0

    mutating func setCoeffs(_ new: BiquadCoeffs) { c = new; reset() }  // pop azaltma
    mutating func reset() { x1 = 0; x2 = 0; y1 = 0; y2 = 0 }

    mutating func process(_ x: Float) -> Float {
        let y = c.b0 * x + c.b1 * x1 + c.b2 * x2 - c.a1 * y1 - c.a2 * y2
        x2 = x1; x1 = x
        y2 = y1; y1 = y
        return y
    }
}
```

**Neva bağlamı:** R16 parametrik EQ bant motoru — AVAudioEngine katmanında (R17) ayrı bir
render callback içinde çağrılır; bu dosya yalnız DSP çekirdeğidir.

## §3 Örnek 2 — 32f buffer döngüsü (K2/K4/K5) · (K2 · K4 · K5)

```swift
// NevaBuffer.swift — Foundation yok, sadece dil std
let defaultRate: UInt32 = 48_000                        // K4 varsayılan

/// K4 destek listesi
func isK4Rate(_ sr: UInt32) -> Bool {
    switch sr {
    case 44_100, 48_000, 88_200, 96_000, 176_400, 192_000: return true
    default: return false
    }
}

/// SR seçimi: tercih → (K6 adayı?) → K5 en yüksek yerel → K4 48 kHz
func pickSampleRate(preferred: UInt32, deviceRates: [UInt32]) -> UInt32 {
    let p = isK4Rate(preferred) ? preferred : defaultRate
    if deviceRates.contains(p) { return p }              // K6: SR eşit
    let k4 = deviceRates.filter(isK4Rate).max()          // K5 fallback
    return k4 ?? defaultRate
}

/// Interleaved float32 zincir — stereo, in-out [Float] (K1 → K2 → K3)
func processInterleaved(_ buf: inout [Float], left: inout BiquadDf1, right: inout BiquadDf1) {
    var i = 0
    while i + 1 < buf.count {
        buf[i] = left.process(buf[i])                    // K1: ham 32f — upcast yok
        buf[i + 1] = right.process(buf[i + 1])           // K2: DSP Float üzerinde
        i += 2
    }
}
```

**Neva bağlamı:** R17 iOS/macOS capture/render ring'inden gelen `AudioBufferList`
float32 kanalları burada işlenir (dönüşüm yok → K6 bit-perfect adayı).

## §4 Örnek 3 — Kilit tabanlı SPSC yerine geçici kuyruk (R10) · (R10)

**Dürüst sapma (R10 lock-free SPSC):** Swift `os_unfair_lock`/`NSLock` kilit API'lerine
sahiptir ama **atomik head/tail + lock-free ring** bu örnekte kurulmaz. Bunun yerine
**NSLock korumalı sabit boyutlu Float kuyruk** verilir. **Gerçek lock-free SPSC yoktur** —
R10'un "lock-free" şartı bu dil örneğinde KARŞILANMAZ; production'da lock-free ring ya
C++ çekirdekten (interop) ya da `ManagedAtomic` (swift-atomics — 3. parti, bu örnekte YOK)
ile kurulur.

```swift
// NevaQueue.swift — Foundation (NSLock) — LOCK-BASED, lock-free DEĞİL
import Foundation

/// Tek-yazıcı/tek-okuyucu yerine NEZDİNDE genel kuyruk; NSLock korumalı.
/// RT-güvenli DEĞİL: lock priority inversion riski taşır (R10 ihlali — bilinçli).
final class FloatRingQueue {
    private var buf: [Float]
    private let capacity: Int
    private var head = 0          // yazıcı indeksi
    private var tail = 0          // okuyucu indeksi
    private let lock = NSLock()

    init(capacity: Int) {
        self.capacity = max(1, capacity)
        self.buf = [Float](repeating: 0, count: max(1, capacity))
    }

    /// Doluysa false (çağıran politika belirler)
    func enqueue(_ v: Float) -> Bool {
        lock.lock(); defer { lock.unlock() }
        let next = (head + 1) % capacity
        if next == tail { return false }                 // dolu
        buf[head] = v
        head = next
        return true
    }

    /// Boşsa nil
    func dequeue() -> Float? {
        lock.lock(); defer { lock.unlock() }
        if tail == head { return nil }                   // boş
        let v = buf[tail]
        tail = (tail + 1) % capacity
        return v
    }
}
```

**Neva bağlamı:** R17 platform tarafında UI↔DSP mesaj kanalı (örn. parametre değişimi).
R10 lock-free ring **çekirdek C++ katmanında** kalır; Swift'te bu kuyruk yalnız
non-RT kontrol trafiği içindir.

## §5 Örnek 4 — WAV 32f okuma/yazma (K3/K1) · (K3 · K1)

44-byte RIFF header · format **3 (IEEE_FLOAT)** · little-endian. Yazma: header + `Data`
append; okuma: chunk walk + doğrulama. Hata yönetimi: `throws` + `enum: Error`.

```swift
// NevaWav.swift — Foundation (Data/FileManager), little-endian host varsayımı
import Foundation

enum NevaWavError: Error {           // Result/Either yerine throws deseni
    case badFormat(String)
    case unsupported(String)
}

func writeWavF32(to path: String, fs: UInt32, channels: UInt16, samples: [Float]) throws {
    guard (44_100...192_000).contains(fs) else {
        throw NevaWavError.unsupported("sample rate 44.1k..192k (K4)")
    }
    guard channels >= 1 && channels <= 2 else {
        throw NevaWavError.unsupported("channels 1..2")
    }
    let dataBytes = UInt32(samples.count * 4)
    var h = Data()
    func u16(_ v: UInt16) { var le = v.littleEndian; withUnsafeBytes(of: &le) { h.append(contentsOf: $0) } }
    func u32(_ v: UInt32) { var le = v.littleEndian; withUnsafeBytes(of: &le) { h.append(contentsOf: $0) } }

    h.append(contentsOf: [0x52,0x49,0x46,0x46])          // "RIFF"
    u32(36 + dataBytes)
    h.append(contentsOf: [0x57,0x41,0x56,0x45])          // "WAVE"
    h.append(contentsOf: [0x66,0x6D,0x74,0x20])          // "fmt "
    u32(16); u16(3)                                      // format 3 IEEE_FLOAT (K1/K3)
    u16(channels); u32(fs)
    u32(fs * UInt32(channels) * 4); u16(channels * 4); u16(32)
    h.append(contentsOf: [0x64,0x61,0x74,0x61])          // "data"
    u32(dataBytes)

    var body = Data(capacity: Int(dataBytes))
    for s in samples {
        var le = s.bitPattern.littleEndian                 // bit-düz, kayıpsız (K2→K3)
        withUnsafeBytes(of: &le) { body.append(contentsOf: $0) }
    }
    try (h + body).write(to: URL(fileURLWithPath: path))
}

struct WavInfo { var fs: UInt32 = 0; var channels: UInt16 = 0
                 var format: UInt16 = 0; var bits: UInt16 = 0; var dataBytes: UInt32 = 0
                 var dataStart: Int = 0 }

func readWavF32(path: String) throws -> (samples: [Float], info: WavInfo) {
    let d = try Data(contentsOf: URL(fileURLWithPath: path))
    guard d.count >= 44,
          [0x52,0x49,0x46,0x46] == [d[0],d[1],d[2],d[3]],
          [0x57,0x41,0x56,0x45] == [d[8],d[9],d[10],d[11]] else {
        throw NevaWavError.badFormat("not RIFF/WAVE")
    }
    var info = WavInfo()
    var p = 12
    while p + 8 <= d.count {
        let id = [d[p],d[p+1],d[p+2],d[p+3]]
        let size = UInt32(d[p+4]) | UInt32(d[p+5]) << 8 | UInt32(d[p+6]) << 16 | UInt32(d[p+7]) << 24
        if id == [0x66,0x6D,0x74,0x20] {                    // "fmt "
            guard size >= 16 else { throw NevaWavError.badFormat("fmt < 16") }
            info.format = UInt16(d[p+8]) | UInt16(d[p+9]) << 8
            info.channels = UInt16(d[p+10]) | UInt16(d[p+11]) << 8
            info.fs = UInt32(d[p+12]) | UInt32(d[p+13]) << 8 | UInt32(d[p+14]) << 16 | UInt32(d[p+15]) << 24
            info.bits = UInt16(d[p+22]) | UInt16(d[p+23]) << 8
            p += 8 + Int(size) + Int(size & 1)              // word-hizalı padding
        } else if id == [0x64,0x61,0x74,0x61] {             // "data"
            info.dataBytes = size; info.dataStart = p + 8
            break
        } else {
            p += 8 + Int(size) + Int(size & 1)
        }
    }
    guard info.format == 3 else { throw NevaWavError.badFormat("format != 3") }
    guard info.bits == 32 else { throw NevaWavError.badFormat("bits != 32") }
    guard info.channels >= 1 && info.channels <= 2 else { throw NevaWavError.badFormat("channels") }
    guard (44_100...192_000).contains(info.fs) else { throw NevaWavError.unsupported("K4") }

    var out = [Float](); out.reserveCapacity(Int(info.dataBytes / 4))
    var q = info.dataStart
    while q + 4 <= info.dataStart + Int(info.dataBytes) && q + 4 <= d.count {
        let b0 = UInt32(d[q]); let b1 = UInt32(d[q+1]); let b2 = UInt32(d[q+2]); let b3 = UInt32(d[q+3])
        out.append(Float(bitPattern: b0 | b1 << 8 | b2 << 16 | b3 << 24))
        q += 4
    }
    return (out, info)
}
```

**Neva bağlamı:** R17 dosya içe/dışa aktarma (share sheet / Files app). 32f WAV — K6
bit-perfect offline render hedefi; dither yalnız 24-bit int düşüşünde (§6).

## §6 Örnek 5 — Compressor + brickwall limiter (R10) · (R10 · K3)

```swift
// NevaComp.swift — Foundation (exp/log10/pow), Float (K2)
import Foundation

struct Compressor {
    static let thresholdDb: Float = -12.0
    static let ratio: Float = 4.0
    static let attackSec: Float = 0.005
    static let releaseSec: Float = 0.050

    private var env: Float = 0
    private let atkCoef: Float
    private let relCoef: Float
    private let thresholdLin: Float
    private let makeup: Float
    private var rng: UInt32 = 0x1234_5678                 // TPDF dither durumu (K3)

    init(fs: Float) {
        atkCoef = exp(-1.0 / (Compressor.attackSec * fs))
        relCoef = exp(-1.0 / (Compressor.releaseSec * fs))
        thresholdLin = pow(10.0, Compressor.thresholdDb / 20.0)
        makeup = pow(10.0, (1.0 - 1.0 / Compressor.ratio) * (-Compressor.thresholdDb) * 0.5 / 20.0)
    }

    private mutating func envelope(_ inAbs: Float) -> Float {
        let a = inAbs > env ? atkCoef : relCoef
        env = a * env + (1.0 - a) * inAbs
        return env
    }

    /// Tek örnek: compressor + brickwall limiter (K2 → K3 clamp)
    mutating func process(_ x: Float) -> Float {
        let level = envelope(abs(x))
        var gain = makeup
        if level > thresholdLin {
            let levelDb = 20.0 * log10(level + 1.0e-12)
            let over = levelDb - Compressor.thresholdDb
            let grDb = -over * (1.0 - 1.0 / Compressor.ratio)
            gain *= pow(10.0, grDb / 20.0)
        }
        return min(max(x * gain, -1.0), 1.0)               // 0 dBFS clamp (K3)
    }

    mutating func processBlock(_ buf: inout [Float]) {
        for i in buf.indices { buf[i] = process(buf[i]) }
    }

    /// K3: 24-bit int düşüşünde TPDF dither (iki uniform [-0.5,+0.5] LSB toplamı)
    mutating func tpdfDither24() -> Float {
        // ⚠️ VERIFICATION REQUIRED: üretim RNG seçimi (xoshiro256** vb.)
        rng = rng &* 1_664_525 &+ 1_013_904_223
        let u1 = Float(rng >> 8) / Float(1 << 24)
        rng = rng &* 1_664_525 &+ 1_013_904_223
        let u2 = Float(rng >> 8) / Float(1 << 24)
        return (u1 - u2) * (1.0 / 8_388_608.0)             // 2^-23 = 1 LSB @24-bit
    }
}
```

**Neva bağlamı:** R17 master bus DynamicsProcessor öncesi/sonrası; cihaz float32
sunuyorsa dither uygulanmaz (K6) — yalnız 24-bit int çıkışta `tpdfDither24()`.

## §7 Örnek 6 — OS/cihaz enum (R18) · (R18 · K4 · K5)

```swift
// NevaDevice.swift — Foundation yok (seçim katmanı — OS API çağrısı YOK)
enum Backend: Equatable {
    case none
    case coreAudio      // iOS/macOS — AUHAL/AVAudioEngine float32 (K1)
}

extension Backend {
    var name: String {
        switch self {
        case .coreAudio: return "CoreAudio"
        case .none: return "NONE"
        }
    }
}

/// Derleme-zamanı OS seçimi — runtime sorgu yok
func detectBackend() -> Backend {
    #if os(iOS) || os(macOS) || os(tvOS) || os(watchOS)
    return .coreAudio
    #else
    return .none
    #endif
}

struct DeviceCaps {
    var backend: Backend
    var maxRate: UInt32        // cihazın en yüksek yerel SR'si (K5 kaynağı)
    var maxChannels: UInt16
    var supportsF32: Bool      // 32-bit float I/O (K1/K3)
}

struct DeviceProfile {         // R18 — cihaz hafızası (R5 profil sistemiyle entegre)
    var hasSaved = false
    var savedRate: UInt32 = 0
}

struct Selection {
    var backend: Backend
    var rate: UInt32
    var fromSaved: Bool
}

/// R18: 1) önceki seçim kaydı → 2) en yüksek SR'lı cihaz (K5) → 3) K4 48 kHz
func pickDefault(profile: DeviceProfile, caps: [DeviceCaps]) -> Selection {
    if profile.hasSaved, let d = caps.first(where: { $0.backend == .coreAudio && profile.savedRate <= $0.maxRate }) {
        return Selection(backend: d.backend, rate: profile.savedRate, fromSaved: true)
    }
    if let best = caps.filter({ $0.supportsF32 && $0.maxRate > 48_000 }).max(by: { $0.maxRate < $1.maxRate }) {
        return Selection(backend: best.backend, rate: best.maxRate, fromSaved: false)
    }
    return Selection(backend: detectBackend(), rate: 48_000, fromSaved: false)   // K4
}
```

**Neva bağlamı:** R17 iOS/macOS — tek platform (CoreAudio). Desteğin ötesinde bir OS
enum'u **bu dilde anlamsızdır**; `detectBackend()` yalnızca derleme koşulu ile .coreAudio
döner (dürüst not: Windows/Linux backend'leri Swift dosyasında tanımlanmaz).

## §8 Dile özel notlar (Swift)

| Başlık | Not |
|---|---|
| **Standart** | Swift 5.9+ · **Foundation** yalnız sin/cos/pow/exp/log10 + Data/FileManager için; AVAudioEngine **bu dosyada YOK** (R17 entegrasyonu ayrı katman) |
| **`var` bilinçli** | `var` yalnız **durumlu** struct'larda (Biquad/Compressor/Queue); saf hesap `let` — `inout` ile tek-buffer işleme |
| **`mutating` deseni** | DSP state `struct` + `mutating func process` — referans tip (class) değil; value-semantics RT callback'te kopya maliyeti taşır (⚠️ production: `UnsafeMutableBufferPointer` tercih edilir) |
| **`&*` / `&+` overflow** | LCG dither `&*`/`&+` (saturate/throw değil, wrap) — Rust `wrapping_*` karşılığı |
| **`Float` = Float32** | Swift `Float` IEEE-754 tek (32-bit) — K2/K3 doğrudan; `Double` kullanılırsa K2 çift hassasiyete kayar (pitfall) |
| **Hata yönetimi** | `throws` + `enum: Error` (Result/Either yerine) — WAV I/O'da `try/catch` |
| **Lock-free YOK** | Örnek 3 **NSLock tabanlı** — R10 lock-free SPSC **karşılanmaz**; `ManagedAtomic` 3. parti (bu örnekte YOK) |
| **Sıcak yol yasakları** | RT callback'te `Data.append` (taahhüt: yalnız init), `print`, kilit (priority inversion) — §4 kuyruk non-RT kontrol trafiği içindir |
| **Pitfall — Data kopyası** | `Data` value-type, `+`/append kopya üretir; büyük WAV'ta streaming `OutputStream` (⚠️ VERIFICATION REQUIRED: üretim yolu) |
| **Bit-perfect (K6)** | `Float(bitPattern:)` / `.bitPattern` bit-düz dönüşüm — WAV yaz→ok round-trip'te örnek kaybı yok |

## §9 Sürüm Geçmişi

| Sürüm | Tarih | Değişiklik |
|---|---|---|
| 1.0.0 | 2026-10-19 | İlk sürüm: 6 örnek (RBJ DF1 · 32f buffer · NSLock kuyruk dürüst sapma · WAV throws · comp+limiter · CoreAudio enum) |

**Authority:** Master Orchestrator → Embedded Engineer · Derlenmedi/test edilmedi (araştırma MD) ·
commit'siz (orkestratöre ait)
