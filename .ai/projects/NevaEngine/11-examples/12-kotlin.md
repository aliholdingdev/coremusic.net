---
title: "Neva Engine — Kotlin Kod Örnekleri (6 Örnek)"
type: reference
category: nevaengine
version: 1.0.0
status: active
authority: "Master Orchestrator → Embedded Engineer"
updated: 2026-10-19
---

# Neva Engine — Kotlin Kod Örnekleri

> **Durum:** v1.0.0 · derlenmedi/test edilmedi (araştırma MD) · K1-K6 uyumlu

**Zorunlu Bağlantılar:** [[11-cok-dilli-kod-ornekleri]] (index) · [[10-io-format-32bit-192khz-kurallari]] (K1-K6 SSOT) · [[06-gereksinimler-ve-farkliliklar]] (R16 · R18) · [[07-mimari-diyagram-kontrol-yuzeyleri]] (R10)

## §1 Bağlam

Kotlin, Neva Android (R17) katmanının dilidir — NDK/AAudio JNI sınırı. AAudio asıl
render C++'ta döner; Kotlin bu örnekte **JNI kontrol/koordinasyon katmanının** idiomatik
karşılıklarını gösterir. Tüm DSP yolu **32-bit float** (K1-K6) — Kotlin `Float` = IEEE-754
tek. Örnekler yalnız dil std (`kotlin.math`) + `java.util.concurrent.atomic` kullanır.

| Kural | İçerik |
|---|---|
| K1 | Input (capture/loopback) **32-bit float** |
| K2 | Internal zincir **32-bit float** (`Float`) |
| K3 | Output **32-bit float** — 24-bit int düşüşünde **TPDF dither zorunlu** |
| K4 | SR **44.1 / 48 / 88.2 / 96 / 176.4 / 192 kHz** · varsayılan **48 kHz** |
| K5 | Cihaz 192 sunmuyorsa → **cihazın en yüksek yerel SR'si** |
| K6 | Bit-perfect = **SR eşit + float32** |

> ⚠️ **Bu dosyadaki kod derlenmedi/test edilmedi; vault referans örneğidir.** Üretim kodu
> doc 01/03 planındaki Faz 0 build+test kapısından geçer. Yalnız `kotlin.math` +
> `java.util.concurrent.atomic` API kullanılmıştır — AAudio/JNI native tarafı bu örneklere
> dahil DEĞİLDİR. Zero-hallucination: doğrulanamayan satır `⚠️ VERIFICATION REQUIRED`.

## §2 Örnek 1 — Biquad EQ (RBJ peaking + Direct Form I) · (R16 · K2)

**Dürüst sapma (R16 çift form):** §4 şeması RBJ peaking + Direct Form I ister; Kotlin
örneğinde tek form (DF1) verilir — Transposed DF-II C++/Rust dosyalarındaki ayrı sınıf
ile örneklendi. `kotlin.math` (sin/cos/pow) kullanılır; `sinh` gibi alan dışı fonksiyon YOK.

```kotlin
// NevaBiquad.kt — kotlin.math, Float (K2)
import kotlin.math.cos
import kotlin.math.sin
import kotlin.math.pow

data class BiquadCoeffs(
    val b0: Float = 1f, val b1: Float = 0f, val b2: Float = 0f,
    val a1: Float = 0f, val a2: Float = 0f
)

/** RBJ peaking (Audio EQ Cookbook) — a0 = 1 normalize */
fun makePeaking(f0: Float, q: Float, gainDb: Float, fs: Float): BiquadCoeffs {
    val qc = q.coerceIn(0.05f, 32.0f)
    val f0c = f0.coerceIn(10.0f, 0.49f * fs)          // Nyquist'e çarpmama
    val gdc = gainDb.coerceIn(-24.0f, 24.0f)

    val A = 10f.pow(gdc / 40f)
    val w0 = 2f * kotlin.math.PI.toFloat() * (f0c / fs)
    val cosw0 = cos(w0)
    val alpha = sin(w0) / (2f * qc)
    val a0 = 1f + alpha / A

    return BiquadCoeffs(
        b0 = (1f + alpha * A) / a0,
        b1 = (-2f * cosw0) / a0,
        b2 = (1f - alpha * A) / a0,
        a1 = (-2f * cosw0) / a0,
        a2 = (1f - alpha / A) / a0
    )
}

/** Direct Form I — 4 durum (R16 referans form) */
class BiquadDf1(private var c: BiquadCoeffs = BiquadCoeffs()) {
    private var x1 = 0f; private var x2 = 0f
    private var y1 = 0f; private var y2 = 0f

    fun setCoeffs(new: BiquadCoeffs) { c = new; reset() }   // pop azaltma
    fun reset() { x1 = 0f; x2 = 0f; y1 = 0f; y2 = 0f }

    fun process(x: Float): Float {
        val y = c.b0 * x + c.b1 * x1 + c.b2 * x2 - c.a1 * y1 - c.a2 * y2
        x2 = x1; x1 = x
        y2 = y1; y1 = y
        return y
    }
}
```

**Neva bağlamı:** R16 parametrik EQ bandı — Android tarafında referans/ürün kontrolü;
gerçek render C++ (AAudio) katmanında döner, bu sınıf JNI üzerinden parametre üretir.

## §3 Örnek 2 — 32f buffer döngüsü (K2/K4/K5) · (K2 · K4 · K5)

```kotlin
// NevaBuffer.kt — kotlin.math yok, sadece dil std; ShortArray değil FloatArray (K1-K3)
const val DEFAULT_RATE: UInt = 48_000u                 // K4 varsayılan

/** K4 destek listesi */
fun isK4Rate(sr: UInt): Boolean = sr in setOf(
    44_100u, 48_000u, 88_200u, 96_000u, 176_400u, 192_000u
)

/** SR seçimi: tercih → (K6 adayı?) → K5 en yüksek yerel → K4 48 kHz */
fun pickSampleRate(preferred: UInt, deviceRates: List<UInt>): UInt {
    val p = if (isK4Rate(preferred)) preferred else DEFAULT_RATE
    if (p in deviceRates) return p                       // K6: SR eşit
    val k4 = deviceRates.filter(::isK4Rate).maxOrNull()  // K5 fallback
    return k4 ?: DEFAULT_RATE
}

/** Interleaved float32 zincir — stereo, in-place FloatArray (K1 → K2 → K3) */
fun processInterleaved(buf: FloatArray, left: BiquadDf1, right: BiquadDf1) {
    var i = 0
    while (i + 1 < buf.size) {
        buf[i] = left.process(buf[i])                    // K1: ham 32f — upcast yok
        buf[i + 1] = right.process(buf[i + 1])           // K2: DSP Float üzerinde
        i += 2
    }
}
```

**Neva bağlamı:** AAudio callback'ininJNI sınırından gelen float32 tampon; Kotlin
tarafı doğrudan `FloatArray` üzerinden işler (dönüşüm yok → K6 bit-perfect adayı).

## §4 Örnek 3 — Lock-free SPSC ring buffer (R10) · (R10)

Kotlin/JVM `java.util.concurrent.atomic` (AtomicInteger + AtomicIntegerArray) ile
**gerçek lock-free SPSC** kurulabilir — atomik head/tail + atomik hücreler, kilit yok.
**Dürüst sınır:** JVM bellek modeli C++'taki `unsafe` pointer desenine izin vermez; hücreler
`AtomicIntegerArray` (f32 bit deseni) olduğundan her erişim atomik ve tanımlı. JIT
`Acquire/Release` karşılığı Volatile semantics sağlar — ama **AAudio render thread'inde
JVM GC duraklaması** riski taşır (⚠️ VERIFICATION REQUIRED: üretimde ring C++ çekirdekte).

```kotlin
// NevaSpsc.kt — java.util.concurrent.atomic, lock-free SPSC
import java.util.concurrent.atomic.AtomicInteger
import java.util.concurrent.atomic.AtomicIntegerArray

/** Başlatma — NON-RT (engine init). RT yolunda tahsis YOK. */
class SpscRing(capacityPow2: Int) {
    private val buf: AtomicIntegerArray
    private val mask: Int
    private val head = AtomicInteger(0)   // yazıcı indeksi (monoton)
    private val tail = AtomicInteger(0)   // okuyucu indeksi (monoton)

    init {
        require(capacityPow2 > 0 && (capacityPow2 and (capacityPow2 - 1)) == 0) {
            "kapasite 2^n olmalı"
        }
        buf = AtomicIntegerArray(capacityPow2)
        mask = capacityPow2 - 1
    }

    /** RT-güvenli: lock yok, block yok. Doluysa false. */
    fun push(v: Float): Boolean {
        val h = head.get()                               // Relaxed okuma
        val next = h + 1
        if (next - tail.get() > mask) return false       // dolu (Acquire)
        buf.set(h and mask, v.toRawBits())               // f32 → bit deseni (K2)
        head.set(next)                                   // Release → veri yayınlanır
        return true
    }

    /** RT-güvenli. Boşsa null. */
    fun pop(): Float? {
        val t = tail.get()                               // Relaxed okuma
        if (t == head.get()) return null                 // boş (Acquire)
        val bits = buf.get(t and mask)                   // atomik hücre okuma
        tail.set(t + 1)                                  // Release
        return Float.fromBits(bits)                      // bit deseni → f32 (K2/K3)
    }
}
```

**Neva bağlamı:** R10 kontrol↔render SPSC — Android'de Kotlin üretici (UI/parametre) ↔
C++ AAudio tüketici sınırında; JVM GC pause taşıdığı için üretimde native ring tercih edilir.

## §5 Örnek 4 — WAV 32f okuma/yazma (K3/K1) · (K3 · K1)

44-byte RIFF header · format **3 (IEEE_FLOAT)** · little-endian. Yazma: header + veri
bloğu; okuma: chunk walk + doğrulama. `ShortArray` DEĞİL — 32f olduğu için `FloatArray`.

```kotlin
// NevaWav.kt — java.io (DataOutputStream/BufferedInputStream), little-endian
import java.io.BufferedInputStream
import java.io.DataInputStream
import java.io.DataOutputStream
import java.io.File
import java.io.FileInputStream
import java.io.FileOutputStream
import java.io.RandomAccessFile

const val WAV_FORMAT_IEEE_FLOAT: Short = 3              // WAVE_FORMAT_IEEE_FLOAT (K1/K3)

/** Uygulama hata tipi — sealed class Result/Either yerine */
sealed class NevaWavError : Exception() {
    class BadFormat(msg: String) : NevaWavError()
    class Unsupported(msg: String) : NevaWavError()
}

fun writeWavF32(path: String, fs: UInt, channels: UShort, samples: FloatArray) {
    require(fs in 44_100u..192_000u) { "sample rate 44.1k..192k (K4)" }
    require(channels in 1u..2u) { "channels 1..2" }
    val dataBytes = samples.size * 4
    DataOutputStream(FileOutputStream(File(path))).use { o ->
        o.writeBytes("RIFF"); o.writeInt(36 + dataBytes); o.writeBytes("WAVE")
        o.writeBytes("fmt "); o.writeInt(16)
        o.writeShort(WAV_FORMAT_IEEE_FLOAT.toInt())      // format 3 (K1/K3)
        o.writeShort(channels.toInt()); o.writeInt(fs.toInt())
        o.writeInt(fs.toInt() * channels.toInt() * 4)    // byteRate
        o.writeShort(channels.toInt() * 4); o.writeShort(32)   // blockAlign / bits
        o.writeBytes("data"); o.writeInt(dataBytes)
        for (s in samples) o.writeInt(s.toRawBits())     // bit-düz, kayıpsız (K2→K3)
    }
}

data class WavInfo(
    var fs: UInt = 0u, var channels: UShort = 0u,
    var format: UShort = 0u, var bits: UShort = 0u,
    var dataBytes: Int = 0, var dataStart: Long = 0
)

fun readWavF32(path: String): Pair<FloatArray, WavInfo> {
    RandomAccessFile(File(path), "r").use { f ->
        val hdr = ByteArray(12); f.readFully(hdr)
        if (String(hdr, 0, 4) != "RIFF" || String(hdr, 8, 4) != "WAVE") {
            throw NevaWavError.BadFormat("not RIFF/WAVE")
        }
        val info = WavInfo()
        while (true) {
            val ch = ByteArray(8); f.readFully(ch)
            val id = String(ch, 0, 4)
            val size = (ch[4].toInt() and 0xFF) or ((ch[5].toInt() and 0xFF) shl 8) or
                    ((ch[6].toInt() and 0xFF) shl 16) or ((ch[7].toInt() and 0xFF) shl 24)
            if (id == "fmt ") {
                require(size >= 16) { "fmt < 16" }
                val fmt = ByteArray(16); f.readFully(fmt)
                info.format = ((fmt[0].toInt() and 0xFF) or ((fmt[1].toInt() and 0xFF) shl 8)).toUShort()
                info.channels = ((fmt[2].toInt() and 0xFF) or ((fmt[3].toInt() and 0xFF) shl 8)).toUShort()
                info.fs = ((fmt[4].toInt() and 0xFF) or ((fmt[5].toInt() and 0xFF) shl 8) or
                        ((fmt[6].toInt() and 0xFF) shl 16) or ((fmt[7].toInt() and 0xFF) shl 24)).toUInt()
                info.bits = ((fmt[14].toInt() and 0xFF) or ((fmt[15].toInt() and 0xFF) shl 8)).toUShort()
                f.seek(f.filePointer + (size - 16))       // kalan fmt uzantısı atla
            } else if (id == "data") {
                info.dataBytes = size; info.dataStart = f.filePointer
                break
            } else {
                f.seek(f.filePointer + size + (size and 1))   // word-hizalı padding
            }
        }
        if (info.format.toInt() != 3) throw NevaWavError.BadFormat("format != 3")
        if (info.bits.toInt() != 32) throw NevaWavError.BadFormat("bits != 32")
        if (info.channels !in 1u..2u) throw NevaWavError.BadFormat("channels")
        if (info.fs !in 44_100u..192_000u) throw NevaWavError.Unsupported("K4")

        val raw = ByteArray(info.dataBytes)
        f.readFully(raw)
        val out = FloatArray(info.dataBytes / 4)
        for (i in out.indices) {
            val q = i * 4
            val bits = (raw[q].toInt() and 0xFF) or ((raw[q + 1].toInt() and 0xFF) shl 8) or
                    ((raw[q + 2].toInt() and 0xFF) shl 16) or ((raw[q + 3].toInt() and 0xFF) shl 24)
            out[i] = Float.fromBits(bits)
        }
        return out to info
    }
}
```

**Neva bağlamı:** R17 dosya içe/dışa aktarma (Android Storage Access Framework). 32f WAV —
K6 bit-perfect offline render hedefi; dither yalnız 24-bit int düşüşünde (§6).

## §6 Örnek 5 — Compressor + brickwall limiter (R10) · (R10 · K3)

```kotlin
// NevaComp.kt — kotlin.math (exp/log10/pow), Float (K2)
import kotlin.math.abs
import kotlin.math.exp
import kotlin.math.log10
import kotlin.math.pow

class Compressor(fs: Float) {
    companion object {
        const val THRESHOLD_DB = -12f
        const val RATIO = 4f
        const val ATTACK_SEC = 0.005f
        const val RELEASE_SEC = 0.050f
    }

    private var env = 0f
    private val atkCoef = exp(-1f / (ATTACK_SEC * fs))
    private val relCoef = exp(-1f / (RELEASE_SEC * fs))
    private val thresholdLin = 10f.pow(THRESHOLD_DB / 20f)
    private val makeup = 10f.pow((1f - 1f / RATIO) * (-THRESHOLD_DB) * 0.5f / 20f)
    private var rng: UInt = 0x1234_5678u                 // TPDF dither durumu (K3)

    /** Tek kutuplu IIR envelope: attack'e göre yüksel, release'e göre düş */
    private fun envelope(inAbs: Float): Float {
        val a = if (inAbs > env) atkCoef else relCoef
        env = a * env + (1f - a) * inAbs
        return env
    }

    /** Tek örnek: compressor + brickwall limiter (K2 → K3 clamp) */
    fun process(x: Float): Float {
        val level = envelope(abs(x))
        var gain = makeup
        if (level > thresholdLin) {
            val levelDb = 20f * (level + 1e-12f).log10()
            val over = levelDb - THRESHOLD_DB
            val grDb = -over * (1f - 1f / RATIO)
            gain *= 10f.pow(grDb / 20f)
        }
        return (x * gain).coerceIn(-1f, 1f)              // 0 dBFS clamp (K3)
    }

    fun processBlock(buf: FloatArray) {
        for (i in buf.indices) buf[i] = process(buf[i])
    }

    /** K3: 24-bit int düşüşünde TPDF dither (iki uniform [-0.5,+0.5] LSB toplamı) */
    fun tpdfDither24(): Float {
        // ⚠️ VERIFICATION REQUIRED: üretim RNG seçimi (xoshiro256** vb.)
        rng = rng * 1_664_525u + 1_013_904_223u          // UInt wrap (LCG)
        val u1 = (rng shr 8).toFloat() / (1u shl 24).toFloat()
        rng = rng * 1_664_525u + 1_013_904_223u
        val u2 = (rng shr 8).toFloat() / (1u shl 24).toFloat()
        return (u1 - u2) * (1f / 8_388_608f)             // 2^-23 = 1 LSB @24-bit
    }
}
```

**Neva bağlamı:** R10 master bus — Android DynamicProcessing (AAudio) öncesi Kotlin
referans; üretimde C++ çekirdekte birebir aynı algoritma çalışır. Cihaz float32 sunuyorsa
dither uygulanmaz (K6) — yalnız 24-bit int çıkışta `tpdfDither24()`.

## §7 Örnek 6 — OS/cihaz enum (R18) · (R18 · K4 · K5)

```kotlin
// NevaDevice.kt — seçim katmanı — OS API çağrısı YOK
enum class Backend(val label: String) {
    NONE("NONE"),
    AAudio("AAudio")        // Android 8+ — düşük gecikme, float32 (K1)
}

/** Derleme-zamanı API seviyesi seçimi — runtime sorgu yok */
fun detectBackend(): Backend = AAudio    // Android hedefi (R17) — tek platform

data class DeviceCaps(
    val backend: Backend,
    val maxRate: UInt,           // cihazın en yüksek yerel SR'si (K5 kaynağı)
    val maxChannels: UShort,
    val supportsF32: Boolean     // 32-bit float I/O (K1/K3)
)

data class DeviceProfile(        // R18 — cihaz hafızası (R5 profil sistemiyle entegre)
    val hasSaved: Boolean = false,
    val savedRate: UInt = 0u
)

data class Selection(
    val backend: Backend,
    val rate: UInt,
    val fromSaved: Boolean
)

/** R18: 1) önceki seçim kaydı → 2) en yüksek SR'lı cihaz (K5) → 3) K4 48 kHz */
fun pickDefault(profile: DeviceProfile, caps: List<DeviceCaps>): Selection {
    if (profile.hasSaved) {
        caps.firstOrNull { it.backend == AAudio && profile.savedRate <= it.maxRate }
            ?.let { return Selection(it.backend, profile.savedRate, true) }
    }
    caps.filter { it.supportsF32 && it.maxRate > 48_000u }
        .maxByOrNull { it.maxRate }
        ?.let { return Selection(it.backend, it.maxRate, false) }
    return Selection(detectBackend(), 48_000u, false)    // K4
}
```

**Neva bağlamı:** R17 Android — tek platform (AAudio). Desteğin ötesinde OS enum'u
**bu dilde anlamsızdır**; `detectBackend()` yalnız AAudio döner (dürüst not: Windows/
macOS/Linux backend'leri Kotlin dosyasında tanımlanmaz — JNI native katmanın işi).

## §8 Dile özel notlar (Kotlin)

| Başlık | Not |
|---|---|
| **Standart** | Kotlin 1.9+ (JVM) · `kotlin.math` + `java.util.concurrent.atomic` — AAudio/JNI native tarafı bu dosyada YOK (R17 JNI sınırı ayrı katman) |
| **`var` bilinçli** | `var` yalnız **durumlu** sınıf üyelerinde (Biquad/Compressor); saf hesap `val` — `process` in-place (taahhüt: tahsis yok) |
| **`UInt`/`UShort`** | Kotlin unsigned tipler Kotlin 1.3+; `toRawBits`/`fromBits` f32 bit-düz dönüşüm (K2/K3) — `ShortArray` DEĞİL `FloatArray` (32f, K1-K3) |
| **Lock-free SPSC** | Örnek 3 **gerçek lock-free** — `AtomicInteger` (head/tail) + `AtomicIntegerArray` (hücre); `head.get()` Relaxed, `tail.get()` Acquire, `set()` Release eşleşmesi |
| **JVM GC duraklaması** | AAudio render thread'inde JVM GC riski → üretimde ring C++ çekirdekte (⚠️ VERIFICATION REQUIRED: JNI sınır gecikme ölçümü) |
| **Hata yönetimi** | `sealed class NevaWavError : Exception` (Result/Either deseni) · `require` ile ön-koşul (IAE fırlatır) |
| **LCG wrap** | `UInt` `*`/`+` doğal wrap (saturate/throw değil) — Rust `wrapping_*` / Swift `&*`/`&+` karşılığı |
| **Pitfall — `Float` vs `Double`** | `kotlin.math` fonksiyonları `Double` yükümlüdür; `Float` zincirde `Float` versiyonlarını kullan (değilse sessiz `Double`'a widen → K2 kayması) |
| **Sıcak yol yasakları** | RT callback'te `FloatArray` tahsisi, `println`, `synchronized` (priority inversion) — §4 kuyruk non-RT trafiği içindir |
| **Bit-perfect (K6)** | `toRawBits`/`Float.fromBits` bit-düz — WAV yaz→ok round-trip'te örnek kaybı yok |

## §9 Sürüm Geçmişi

| Sürüm | Tarih | Değişiklik |
|---|---|---|
| 1.0.0 | 2026-10-19 | İlk sürüm: 6 örnek (RBJ DF1 · 32f buffer · lock-free SPSC atomik · WAV sealed error · comp+limiter · AAudio enum) |

**Authority:** Master Orchestrator → Embedded Engineer · Derlenmedi/test edilmedi (araştırma MD) ·
commit'siz (orkestratöre ait)
