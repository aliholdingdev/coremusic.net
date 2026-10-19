---
title: "Neva Engine — Java Kod Örnekleri (6 Örnek)"
type: reference
category: nevaengine
version: 1.0.0
status: active
authority: "Master Orchestrator → Embedded Engineer"
updated: 2026-10-19
---

# Neva Engine — Java Kod Örnekleri

> **Durum:** v1.0.0 · derlenmedi/test edilmedi (araştırma MD) · K1-K6 uyumlu

**Zorunlu Bağlantılar:** [[11-cok-dilli-kod-ornekleri]] (index) · [[10-io-format-32bit-192khz-kurallari]] (K1-K6 SSOT) · [[06-gereksinimler-ve-farkliliklar]] (R16 · R18) · [[07-mimari-diyagram-kontrol-yuzeyleri]] (R10)

## §1 Bağlam

Java, Neva masaüstü **kontrol katmanı**dır (javax.sound üzerinden cihaz keşfi + offline
WAV I/O). Bu katman **real-time DSP callback'i DEĞİLDİR** — JVM GC duraklaması nedeniyle
sıcak yol (RT render) Java'da çalışmaz; DSP C++ çekirdekte döner. Tüm örnekler yine
**32-bit float** (K1-K6) mantığını uygular.

| Kural | İçerik |
|---|---|
| K1 | Input (capture/loopback) **32-bit float** |
| K2 | Internal zincir **32-bit float** (`float` — primitive) |
| K3 | Output **32-bit float** — 24-bit int düşüşünde **TPDF dither zorunlu** |
| K4 | SR **44.1 / 48 / 88.2 / 96 / 176.4 / 192 kHz** · varsayılan **48 kHz** |
| K5 | Cihaz 192 sunmuyorsa → **cihazın en yüksek yerel SR'si** |
| K6 | Bit-perfect = **SR eşit + float32** |

> ⚠️ **Bu dosyadaki kod derlenmedi/test edilmedi; vault referans örneğidir.** Üretim kodu
> doc 01/03 planındaki Faz 0 build+test kapısından geçer. Yalnız JDK std API
> (`javax.sound.sampled`, `java.util.concurrent`) kullanılmıştır. Zero-hallucination:
> doğrulanamayan satır `⚠️ VERIFICATION REQUIRED`.

## §2 Örnek 1 — Biquad EQ (RBJ peaking + Direct Form I) · (R16 · K2)

**Dürüst sapma (R16 çift form):** §4 şeması RBJ peaking + Direct Form I ister; Java
örneğinde tek form (DF1) verilir — Transposed DF-II C++/Rust dosyalarındaki ayrı sınıf
ile örneklendi. `Math` (sin/cos/pow) kullanılır; `float` overload'ları ile K2 korunur.

```java
// NevaBiquad.java — java.lang.Math, float (K2)
public final class NevaBiquad {
    private NevaBiquad() {}

    public record BiquadCoeffs(float b0, float b1, float b2, float a1, float a2) {
        static BiquadCoeffs identity() { return new BiquadCoeffs(1f, 0f, 0f, 0f, 0f); }
    }

    /** RBJ peaking (Audio EQ Cookbook) — a0 = 1 normalize */
    public static BiquadCoeffs makePeaking(float f0, float q, float gainDb, float fs) {
        float qc = Math.min(Math.max(q, 0.05f), 32.0f);
        float f0c = Math.min(Math.max(f0, 10.0f), 0.49f * fs);   // Nyquist'e çarpmama
        float gdc = Math.min(Math.max(gainDb, -24.0f), 24.0f);

        float A = (float) Math.pow(10.0, gdc / 40.0f);
        float w0 = 2.0f * (float) Math.PI * (f0c / fs);
        float cosw0 = (float) Math.cos(w0);
        float alpha = (float) Math.sin(w0) / (2.0f * qc);
        float a0 = 1.0f + alpha / A;

        return new BiquadCoeffs(
            (1.0f + alpha * A) / a0,
            (-2.0f * cosw0) / a0,
            (1.0f - alpha * A) / a0,
            (-2.0f * cosw0) / a0,
            (1.0f - alpha / A) / a0
        );
    }

    /** Direct Form I — 4 durum (R16 referans form) */
    public static final class BiquadDf1 {
        private BiquadCoeffs c;
        private float x1 = 0f, x2 = 0f, y1 = 0f, y2 = 0f;

        public BiquadDf1(BiquadCoeffs c) { this.c = c; }

        public void setCoeffs(BiquadCoeffs neu) { this.c = neu; reset(); }  // pop azaltma
        public void reset() { x1 = 0f; x2 = 0f; y1 = 0f; y2 = 0f; }

        public float process(float x) {
            float y = c.b0() * x + c.b1() * x1 + c.b2() * x2 - c.a1() * y1 - c.a2() * y2;
            x2 = x1; x1 = x;
            y2 = y1; y1 = y;
            return y;
        }
    }
}
```

**Neva bağlamı:** R16 referans doğrulama / offline preset hesabı — gerçek render C++
çekirdekte döner; Java bandı yalnız kontrol/ test katmanında çalışır (RT değil).

## §3 Örnek 2 — 32f buffer döngüsü (K2/K4/K5) · (K2 · K4 · K5)

```java
// NevaBuffer.java — sadece JDK std; float[] (K1-K3 — byte[] değil)
public final class NevaBuffer {
    private NevaBuffer() {}

    public static final int DEFAULT_RATE = 48_000;               // K4 varsayılan
    private static final int[] K4_RATES =
        { 44_100, 48_000, 88_200, 96_000, 176_400, 192_000 };

    /** K4 destek listesi */
    public static boolean isK4Rate(int sr) {
        for (int r : K4_RATES) if (r == sr) return true;
        return false;
    }

    /** SR seçimi: tercih → (K6 adayı?) → K5 en yüksek yerel → K4 48 kHz */
    public static int pickSampleRate(int preferred, int[] deviceRates) {
        int p = isK4Rate(preferred) ? preferred : DEFAULT_RATE;
        for (int r : deviceRates) if (r == p) return p;          // K6: SR eşit
        int best = -1;                                           // K5 fallback
        for (int r : deviceRates) if (isK4Rate(r) && r > best) best = r;
        return best > 0 ? best : DEFAULT_RATE;
    }

    /** Interleaved float32 zincir — stereo, in-place float[] (K1 → K2 → K3) */
    public static void processInterleaved(
            float[] buf, NevaBiquad.BiquadDf1 left, NevaBiquad.BiquadDf1 right) {
        for (int i = 0; i + 1 < buf.length; i += 2) {
            buf[i] = left.process(buf[i]);                        // K1: ham 32f
            buf[i + 1] = right.process(buf[i + 1]);               // K2: DSP float
        }
    }
}
```

**Neva bağlamı:** javax.sound `SourceDataLine` float32 tamponu (K3 bit-perfect adayı) —
masaüstü kontrol katmanında offline render/monitoring; RT callback Java'da DEĞİL.

## §4 Örnek 3 — Kilit tabanlı SPSC yerine geçici kuyruk (R10) · (R10)

**Dürüst sapma (R10 lock-free SPSC):** Java `java.util.concurrent.atomic`
(`AtomicIntegerArray` + `VarHandle`) ile lock-free SPSC **teorik olarak mümkündür** —
aynı JVM atomikleri 12-kotlin.md §4'te birebir gösterildi. Ancak bu dosya masaüstü
**kontrol katmanı**dır (RT şartı yok); bu nedenle **`ArrayBlockingQueue` tabanlı basit
kuyruk** verilir. **Gerçek lock-free SPSC bu örnekte YOKTUR** — üretimde lock-free
gerekirse Kotlin §4 deseni (aynı JVM atomikleri) taşınır; JVM GC duraklaması RT render
için yine de engeldir (§8).

```java
// NevaQueue.java — java.util.concurrent (ArrayBlockingQueue) — LOCK-BASED, lock-free DEĞİL
import java.util.concurrent.ArrayBlockingQueue;
import java.util.concurrent.BlockingQueue;

public final class NevaQueue {
    private NevaQueue() {}

    /**
     * Sabit boyutlu Float kuyruk — ArrayBlockingQueue (ReentrantLock tabanlı).
     * RT-güvenli DEĞİL: kilit + priority inversion riski (R10 ihlali — bilinçli).
     * Kullanım: non-RT kontrol trafiği (parametre/log) — DSP çekirdeğe dokunmaz.
     */
    public static final class FloatRingQueue {
        private final BlockingQueue<Float> q;

        public FloatRingQueue(int capacity) {
            this.q = new ArrayBlockingQueue<>(Math.max(1, capacity));
        }

        /** Doluysa false (çağıran politika belirler) */
        public boolean enqueue(float v) { return q.offer(v); }

        /** Boşsa null (Float sarmalama — primitive getter yok) */
        public Float dequeue() { return q.poll(); }
    }
}
```

**Neva bağlamı:** R12 masaüstü kontrol ↔ C++ çekirdek JNI/pipe trafiği (parametre, durum,
log). R10 lock-free ring **çekirdek C++ katmanında** kalır; Java kuyruk yalnız non-RT
kontrol mesajları içindir.

## §5 Örnek 4 — WAV 32f okuma/yazma (K3/K1) · (K3 · K1) — javax.sound

javax.sound `AudioSystem` üzerinden format 3 (IEEE_FLOAT) okuma/yazma. **Dürüst sınır:**
javax.sound bazı platformlarda WAV/float32 yazımı için uygun writer sunmayabilir — bu
durumda elle RIFF parse (09-rust.md §5'teki şema) fallback olur.

```java
// NevaWav.java — javax.sound.sampled (AudioSystem / AudioInputStream)
import javax.sound.sampled.AudioFileFormat;
import javax.sound.sampled.AudioFormat;
import javax.sound.sampled.AudioInputStream;
import javax.sound.sampled.AudioSystem;
import java.io.File;

public final class NevaWav {
    private NevaWav() {}

    /** Yazma — 32f WAV (K3); encoding FLOAT = format 3 IEEE_FLOAT karşılığı */
    public static void writeWavF32(File file, float sampleRate, int channels, float[] samples)
            throws Exception {
        int rate = (int) sampleRate;
        if (rate < 44_100 || rate > 192_000) {
            throw new IllegalArgumentException("sample rate 44.1k..192k (K4)");
        }
        if (channels < 1 || channels > 2) {
            throw new IllegalArgumentException("channels 1..2");
        }
        AudioFormat fmt = new AudioFormat(
            AudioFormat.Encoding.FLOAT, (float) rate, 32, channels,
            channels * 4, (float) rate, false /* little-endian */);
        byte[] pcm = new byte[samples.length * 4];
        for (int i = 0; i < samples.length; i++) {
            int bits = Float.floatToRawIntBits(samples[i]);       // bit-düz (K2→K3)
            pcm[i * 4] = (byte) (bits);
            pcm[i * 4 + 1] = (byte) (bits >> 8);
            pcm[i * 4 + 2] = (byte) (bits >> 16);
            pcm[i * 4 + 3] = (byte) (bits >> 24);
        }
        try (AudioInputStream ais = new AudioInputStream(
                new java.io.ByteArrayInputStream(pcm), fmt, samples.length / channels)) {
            AudioSystem.write(ais, AudioFileFormat.Type.WAVE, file);
        }
    }

    /** Okuma — chunk/format doğrulamayı AudioSystem üstlenir; dönüşüm float[] */
    public static float[] readWavF32(File file) throws Exception {
        AudioInputStream ais = AudioSystem.getAudioInputStream(file);
        AudioFormat fmt = ais.getFormat();
        if (fmt.getEncoding() != AudioFormat.Encoding.FLOAT || fmt.getSampleSizeInBits() != 32) {
            throw new IllegalStateException("format != 32-bit IEEE_FLOAT (K1/K3)");
        }
        int rate = (int) fmt.getSampleRate();
        if (rate < 44_100 || rate > 192_000) {
            throw new IllegalStateException("sample rate 44.1k..192k (K4)");
        }
        byte[] pcm = ais.readAllBytes();
        float[] out = new float[pcm.length / 4];
        for (int i = 0; i < out.length; i++) {
            int b0 = pcm[i * 4] & 0xFF, b1 = pcm[i * 4 + 1] & 0xFF;
            int b2 = pcm[i * 4 + 2] & 0xFF, b3 = pcm[i * 4 + 3] & 0xFF;
            out[i] = Float.intBitsToFloat(b0 | b1 << 8 | b2 << 16 | b3 << 24);
        }
        ais.close();
        return out;
    }
}
```

**Neva bağlamı:** R12 masaüstü dosya içe/dışa aktarma (denetim/arka plan render). 32f WAV
— K6 bit-perfect offline hedefi; dither yalnız 24-bit int düşüşünde (§6).

## §6 Örnek 5 — Compressor + brickwall limiter (R10) · (R10 · K3)

```java
// NevaComp.java — java.lang.Math, float (K2) — offline doğrulama sürümü (RT değil)
public final class NevaComp {
    public static final float THRESHOLD_DB = -12.0f;
    public static final float RATIO = 4.0f;
    public static final float ATTACK_SEC = 0.005f;
    public static final float RELEASE_SEC = 0.050f;

    private float env = 0f;
    private final float atkCoef, relCoef, thresholdLin, makeup;
    private int rng = 0x1234_5678;                          // TPDF dither durumu (K3)

    public NevaComp(float fs) {
        atkCoef = (float) Math.exp(-1.0 / (ATTACK_SEC * fs));
        relCoef = (float) Math.exp(-1.0 / (RELEASE_SEC * fs));
        thresholdLin = (float) Math.pow(10.0, THRESHOLD_DB / 20.0);
        makeup = (float) Math.pow(10.0, (1.0 - 1.0 / RATIO) * (-THRESHOLD_DB) * 0.5 / 20.0);
    }

    /** Tek kutuplu IIR envelope: attack'e göre yüksel, release'e göre düş */
    private float envelope(float inAbs) {
        float a = inAbs > env ? atkCoef : relCoef;
        env = a * env + (1.0f - a) * inAbs;
        return env;
    }

    /** Tek örnek: compressor + brickwall limiter (K2 → K3 clamp) */
    public float process(float x) {
        float level = envelope(Math.abs(x));
        float gain = makeup;
        if (level > thresholdLin) {
            float levelDb = 20.0f * (float) Math.log10(level + 1.0e-12f);
            float over = levelDb - THRESHOLD_DB;
            float grDb = -over * (1.0f - 1.0f / RATIO);
            gain *= (float) Math.pow(10.0, grDb / 20.0);
        }
        return Math.min(Math.max(x * gain, -1.0f), 1.0f);   // 0 dBFS clamp (K3)
    }

    /** Blok işleme — düz kanal (çağıran ayırır) */
    public void processBlock(float[] buf) {
        for (int i = 0; i < buf.length; i++) buf[i] = process(buf[i]);
    }

    /** K3: 24-bit int düşüşünde TPDF dither (iki uniform [-0.5,+0.5] LSB toplamı) */
    public float tpdfDither24() {
        // ⚠️ VERIFICATION REQUIRED: üretim RNG seçimi (xoshiro256** vb.)
        rng = rng * 1_664_525 + 1_013_904_223;              // int wrap (LCG)
        float u1 = (float) ((rng >>> 8) & 0xFFFFFF) / (float) (1 << 24);
        rng = rng * 1_664_525 + 1_013_904_223;
        float u2 = (float) ((rng >>> 8) & 0xFFFFFF) / (float) (1 << 24);
        return (u1 - u2) * (1.0f / 8_388_608.0f);           // 2^-23 = 1 LSB @24-bit
    }
}
```

**Neva bağlamı:** R10 doğrulama/orchestration — Java master bus değerini offline hesaplar,
C++ çekirdeğe aynı parametreleri gönderir. Cihaz float32 sunuyorsa dither uygulanmaz (K6).

## §7 Örnek 6 — OS/cihaz enum (R18) · (R18 · K4 · K5) — javax.sound cihaz keşfi

javax.sound `AudioSystem.getMixerInfo()` ile kayıt/bağlama enumerate — dilin platform
API'si ile. **Dürüst not:** ASIO/WASAPI/ALSA/CoreAudio birincil backend'leri javax.sound
katmanında GÖRÜNMEZ (yalnız JVM mixer'ları); birincil backend seçimi C++ çekirdektir.

```java
// NevaDevice.java — javax.sound.sampled (AudioSystem.getMixerInfo) — seçim katmanı
import javax.sound.sampled.AudioFileFormat;
import javax.sound.sampled.AudioFormat;
import javax.sound.sampled.AudioSystem;
import javax.sound.sampled.Mixer;

public final class NevaDevice {
    private NevaDevice() {}

    /** Backend — javax.sound katmanı OS-specific backend GÖRÜTMEZ (dürüst sınır) */
    public enum Backend {
        NONE("NONE"), JVM_MIXER("JVM Mixer");       // ASIO/WASAPI/ALSA/CoreAudio: C++ katmanı
        private final String label;
        Backend(String label) { this.label = label; }
        public String label() { return label; }
    }

    /** JVM mixer keşfi — javax.sound ile cihaz listesi */
    public static int countMixers() {
        return AudioSystem.getMixerInfo().length;
    }

    public record DeviceCaps(
        Backend backend, int maxRate, int maxChannels, boolean supportsF32) {}

    /** R18 — cihaz hafızası (R5 profil sistemiyle entegre) */
    public record DeviceProfile(boolean hasSaved, int savedRate) {}

    public record Selection(Backend backend, int rate, boolean fromSaved) {}

    /** R18: 1) önceki seçim kaydı → 2) en yüksek SR'lı cihaz (K5) → 3) K4 48 kHz */
    public static Selection pickDefault(DeviceProfile profile, DeviceCaps[] caps) {
        if (profile.hasSaved()) {
            for (DeviceCaps d : caps) {
                if (d.backend() == Backend.JVM_MIXER && profile.savedRate() <= d.maxRate()) {
                    return new Selection(d.backend(), profile.savedRate(), true);
                }
            }
        }
        DeviceCaps best = null;                          // K5 fallback
        for (DeviceCaps d : caps) {
            if (d.supportsF32() && d.maxRate() > 48_000 && (best == null || d.maxRate() > best.maxRate())) {
                best = d;
            }
        }
        if (best != null) return new Selection(best.backend(), best.maxRate(), false);
        return new Selection(Backend.JVM_MIXER, 48_000, false);   // K4
    }

    /** Cihazın desteklediği en yüksek float32 SR — Mixer Api'den (dürüst sınır: basit tarama) */
    public static int maxFloatRate(javax.sound.sampled.Mixer mixer) {
        int max = 0;
        for (AudioFileFormat.Type t : AudioSystem.getWriterFileTypes()) { /* noop — keşif */ }
        // ⚠️ VERIFICATION REQUIRED: Mixer.Info'dan gerçek SR listesi platforma bağlıdır;
        // üretimde cihazın native API'si (WASAPI/CoreAudio) C++ çekirdekten sorgulanır.
        return max;
    }
}
```

**Neva bağlamı:** R12 masaüstü kontrol — javax.sound mixer listesi kullanıcıya gösterilir;
**asıl birincil backend (ASIO/WASAPI/ALSA/CoreAudio) + K5 en-yüksek-SR seçimi C++ çekirdekte**.
Java katmanı yalnız JVM mixer envanterini sunar (dürüst not: OS enum'unun tamamı burada
anlamsızdır).

## §8 Dile özel notlar (Java)

| Başlık | Not |
|---|---|
| **Standart** | Java 17+ (record, `var` yalnız yerel) · `javax.sound.sampled` + `java.util.concurrent` — JNI/ native tarafı bu dosyada YOK (R12 kontrol katmanı) |
| **`var` yalnız yerel** | Java 10+ `var` **yerel değişkenlerle sınırlıdır** — alan/parametre/return tipi DEĞİL (saf `float[]`/`record` kullanıldı); sınıf alanı olarak `var` YAZILAMAZ |
| **`float` = Float32** | Java `float` IEEE-754 tek (32-bit) — K2/K3 doğrudan; `Math` fonksiyonları `double` döner → `Math.sin(float)` yerine `(float) Math.sin(...)` K2 korur |
| **Lock-free SPSC YOK** | Örnek 3 **`ArrayBlockingQueue` (ReentrantLock)** — R10 lock-free SPSC **karşılanmaz**; lock-free gerekirse Kotlin §4 deseni (aynı JVM atomikleri) taşınır |
| **GC duraklaması** | JVM GC RT render için engeldir → Java **yalnız non-RT kontrol katmanı**; DSP C++ çekirdekte (R12) |
| **Hata yönetimi** | `throws Exception` + `IllegalStateException`/`IllegalArgumentException` — Result/Either yerine Java idiomu (checked/unchecked karışımı bilinçli) |
| **LCG wrap** | `int` `*`/`+` doğal wrap (saturate/throw değil) — Rust `wrapping_*` / Kotlin `UInt` / Swift `&*`/`&+` karşılığı |
| **`floatToRawIntBits`** | `Float.floatToRawIntBits`/`intBitsToFloat` bit-düz (NaN koruyucu) — K6 bit-perfect round-trip |
| **`readAllBytes` sınırsız** | `AudioInputStream.readAllBytes()` büyük dosyada bellek taşır → streaming (⚠️ VERIFICATION REQUIRED: üretim yolu) |
| **Sıcak yol yasakları** | RT callback'te Java GC/`synchronized` (priority inversion) — §4 kuyruk non-RT kontrol trafiği içindir |

## §9 Sürüm Geçmişi

| Sürüm | Tarih | Değişiklik |
|---|---|---|
| 1.0.0 | 2026-10-19 | İlk sürüm: 6 örnek (RBJ DF1 · 32f buffer · ArrayBlockingQueue dürüst sapma · WAV javax.sound · comp+limiter · JVM mixer enum) |

**Authority:** Master Orchestrator → Embedded Engineer · Derlenmedi/test edilmedi (araştırma MD) ·
commit'siz (orkestratöre ait)
