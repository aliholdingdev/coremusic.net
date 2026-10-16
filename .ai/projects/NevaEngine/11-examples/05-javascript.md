---
title: "Neva Engine — JavaScript Kod Örnekleri (Web Mixer · AudioWorklet)"
type: reference
category: nevaengine
version: 1.0.0
status: active
authority: "Master Orchestrator → Embedded Engineer"
updated: 2026-10-15
---

# Neva Engine — JavaScript Kod Örnekleri (Web Mixer · AudioWorklet)

> **Durum:** v1.0.0 · derlenmedi/test edilmedi (araştırma MD) · K1-K6 uyumlu

**İlgili:** [[11-cok-dilli-kod-ornekleri]] (index) · [[10-io-format-32bit-192khz-kurallari]] (K1-K6 SSOT) · [[05-neva-mixer-web-pcm-plani]] (Layer 3 web UI — bu dosyanın bağlamı)

## §1 Bağlam — JS'in Neva'daki rolü

JavaScript, Neva'da **web mixer'in** dilidir (05 §2 Layer 3 — vanilla JS, framework yok;
CoreMusic SPA standardı ile tutarlı). İki yüzey:

- **AudioWorklet (canlı DSP):** `AudioWorkletProcessor.process()` — 128-frame bloklarda
  float32 işleme (EQ/comp/limiter). 05 §5 Yol A: `decodeAudioData` → `OfflineAudioContext`
  → biquad chain → WAV encode (dosya editörü).
- **Kontrol/görselleştirme:** WebSocket JSON (05 §4) + canvas WebGL RTA — ses işi
  çekirdek/C++'tadır; işletim yeri `AudioWorklet` ise tarayıcıdadır (dosya editörü Yol A).

**Gerçek zamanlılık dürüstlüğü:** `AudioWorklet` main-thread **DEĞİLDİR** (ayrı audio
thread'inde, gerçek zamanlı sınıfı) — ama tarayıcı işleticisi GC/underrun riski taşır;
düşük gecikme garantisi C++ çekirdek (ASIO <10 ms, R3) kadar sağlam değildir.

**K1-K6 özeti (doc 10 — bağlayıcı):**

| Kural | İçerik |
|---|---|
| K1 | Input **32-bit float** (`Float32Array` — Web Audio'nun yerel formu) |
| K2 | Internal processing **32-bit float** |
| K3 | Output **32-bit float** (24-bit int düşüşte **TPDF dither**) |
| K4 | SR **44.1–192 kHz** (varsayılan **48 kHz**) |
| K5 | Cihaz 192 sunmuyorsa → **en yüksek yerel SR** (§7'de otomatik) |
| K6 | Bit-perfect = **SR eşit + float32** |

**Dil kuralları (global AGENTS.md):** `var` YOK · `any` YOK (JS zaten dinamik — tip
güvenliği TS dosyasında, 06) · `'use strict'` · modern ES2024 (class fields, private
`#alan`) · AudioWorklet kayıt deseni (`registerProcessor`).

---

## §2 Örnek 1 — Biquad EQ (R16 çift form): RBJ peaking + Direct Form I · 48 kHz · K2

> **R16:** tek biquad motoru — parametrik (f/Q/dB) ve grafik (31 band) arayüz aynı
> `processSample`'ı çağırır. **K2:** `Float32Array` üzerinde float32 (JS number = float64
> hesap, Web Audio depolama float32 — JS'te "değer" float64, "kanal" float32; Örnek 2 notu).
> Bu blok `AudioWorkletProcessor.process()` İÇİNDE çalışır (aşağıdaki tam desen).

```javascript
'use strict';

// R16 · K2 — RBJ peaking koef. + Direct Form I (class fields, ES2024)
class BiquadPeaking {
  #b0 = 1; #b1 = 0; #b2 = 0; #a1 = 0; #a2 = 0;
  #x1 = 0; #x2 = 0; #y1 = 0; #y2 = 0;   // Direct Form I durumu (kanal başına!)

  // RBJ peaking — sampleRate: K4 (worklet'te global sampleRate, §3)
  setPeaking(freq, q, gainDb, sampleRate = 48000) {
    const a = 10 ** (gainDb / 40);
    const w0 = (2 * Math.PI * freq) / sampleRate;
    const alpha = Math.sin(w0) / (2 * q);
    const cosW0 = Math.cos(w0);
    const b0 = 1 + alpha * a;
    const b1 = -2 * cosW0;
    const b2 = 1 - alpha * a;
    const a0 = 1 + alpha / a;
    const a1 = -2 * cosW0;
    const a2 = 1 - alpha / a;
    this.#b0 = b0 / a0; this.#b1 = b1 / a0; this.#b2 = b2 / a0;
    this.#a1 = a1 / a0; this.#a2 = a2 / a0;
  }

  processSample(x) {
    const y = this.#b0 * x + this.#b1 * this.#x1 + this.#b2 * this.#x2
            - this.#a1 * this.#y1 - this.#a2 * this.#y2;
    this.#x2 = this.#x1; this.#x1 = x;
    this.#y2 = this.#y1; this.#y1 = y;
    return y;
  }

  reset() { this.#x1 = this.#x2 = this.#y1 = this.#y2 = 0; }
}

// AudioWorklet deseni: process() bloğu içinde kullanılır (128-frame)
class NevaEqProcessor extends AudioWorkletProcessor {
  #eq = new BiquadPeaking();          // stereo: ayrı state için dizi de olur (not §8)
  #freq = 1000; #q = 1.0; #gainDb = 0;

  process(inputs, outputs /*, parameters */) {
    const input = inputs[0];
    const output = outputs[0];
    if (!input || input.length === 0) return true;   // kaynak yok → açık kal (worklet sözleşmesi)

    this.#eq.setPeaking(this.#freq, this.#q, this.#gainDb, sampleRate); // K4: global SR
    for (let ch = 0; ch < output.length; ch++) {
      const src = input[Math.min(ch, input.length - 1)];
      const dst = output[ch];
      for (let i = 0; i < dst.length; i++) {        // 128 frame (blok)
        dst[i] = this.#eq.processSample(src[i]);    // K2: float32 kanal
      }
    }
    return true;
  }
}
registerProcessor('neva-eq', NevaEqProcessor);       // AudioWorklet kayıt deseni
```

---

## §3 Örnek 2 — 32f buffer döngüsü (K2/K4/K5): Float32Array + 128-frame + SR fallback

> **K2/K4:** Web Audio kanalları `Float32Array`'dir (float32 yerel) — JS `number` float64
> hesaplar, kanala yazarken float32'e yuvarlanır (aynı "değer dönüşümü" PHP'deki gibi,
> ama burada **döngü zaten kanal üzerinde** çalışır). **K5:** worklet scope'unda global
> `sampleRate` = **AudioContext'in açıldığı SR** = tarayıcının/CIHAZIN desteklediği SR
> (§7 — K5 otomatik). **128-frame blok:** `process()` her çağrıda 128 örnek (spec: sabit).

```javascript
'use strict';

// K4/K5 — SR fallback: K4 listesi içinden cihaz SR'sine en yakın alt sınır, yoksa 48k
const NEVA_SR_LIST = [44100, 48000, 88200, 96000, 176400, 192000]; // K4
const NEVA_DEFAULT_SR = 48000;                                      // K4 varsayılan

function nevaFallbackSampleRate(deviceSr) {
  if (Number.isFinite(deviceSr) && NEVA_SR_LIST.includes(deviceSr)) return deviceSr; // K6: eşit → dönüşüm yok
  let best = NEVA_DEFAULT_SR;
  for (const sr of NEVA_SR_LIST) {
    if (!Number.isFinite(deviceSr) || sr <= deviceSr) best = Math.max(best, sr);
  }
  return best; // K5: cihaz 192 sunmuyorsa en yüksek geçerli SR
}

// K2/K4/K5 — 32f blok döngüsü: DC offset + clamp (K3 0 dBFS brickwall)
function nevaProcessBlock32f(block /* Float32Array */, dcOffset = 0) {
  for (let i = 0; i < block.length; i++) {
    let y = block[i] + dcOffset;
    y = y < -1 ? -1 : y > 1 ? 1 : y;   // K3 clamp (limiter Örnek 5)
    block[i] = y;                       // Float32Array yazımı → float32 (K1/K2)
  }
  return block;
}

// AudioWorklet içinde kullanım — 128-frame bloklar, sıfır allocate (zero-allocation)
class NevaGainProcessor extends AudioWorkletProcessor {
  #gain = 1.0;
  process(inputs, outputs, parameters) {
    const input = inputs[0];
    const output = outputs[0];
    if (!input || input.length === 0) return true;

    // a-rate param: parameters.get('gain') her ÖRNEK için değer döndürür (Float32Array)
    const gainParam = parameters.get('gain');
    for (let ch = 0; ch < output.length; ch++) {
      const src = input[Math.min(ch, input.length - 1)];
      const dst = output[ch];
      for (let i = 0; i < dst.length; i++) {        // 128 frame
        const g = gainParam ? gainParam[i] : this.#gain;
        dst[i] = src[i] * g;                         // K2: float32 zincir
      }
      nevaProcessBlock32f(dst);                      // K3 clamp
    }
    return true;
  }
}
registerProcessor('neva-gain', NevaGainProcessor);
```

---

## §4 Örnek 3 — Buffer kuyruk/producer-consumer (R10): SPSC ring + Atomics

> **R10.** **SharedArrayBuffer varsa:** `Atomics` ile lock-free SPSC (head/tail ayrı
> cache-line'a — üretimde `Atomics.store` relaxed + tek yazıcı/tek okuyucu). **Yoksa**
> (COOP/COEP başlıkları yoksa — ⚠️ VERIFICATION REQUIRED: sunucu başlıkları Faz 0'da
> doğrulanacak): tek thread içinde basit ring. **Worklet kuralı:** `process()` içinde
> `wait`/`lock YASAK` (audio thread blocking = underrun) → kuyruk **non-blocking**;
> doluysa drop/backpressure, boşsa pas geç.

```javascript
'use strict';

// R10 — SPSC ring buffer (tek writer: main thread, tek reader: AudioWorklet)
// SharedArrayBuffer yoksa: sabit Float32Array + kilit yok (tek thread mantığı).
class SpscRing {
  #buf; #cap; #head = 0; #tail = 0; #shared; #header = null;
  static HEADER = 4; // [0]=head [1]=tail [2]=count [3]=capacity (Int32Array)

  constructor(capacity, sharedBuffer = null) {
    this.#cap = capacity;
    this.#shared = sharedBuffer instanceof SharedArrayBuffer;
    if (this.#shared) {
      const header = new Int32Array(sharedBuffer, 0, SpscRing.HEADER);
      const data = new Float32Array(sharedBuffer, SpscRing.HEADER * 4);
      this.#buf = data;
      Atomics.store(header, 3, capacity, 0);
      Atomics.store(header, 0, 0, 0);
      Atomics.store(header, 1, 0, 0);
      this.#header = header;
    } else {
      this.#buf = new Float32Array(capacity); // K2: float32 depolama (zero-alloc: baştan)
      this.#header = null;
    }
  }

  // Üretici (main thread) — doluysa drop (backpressure), ASLA bloklanmaz
  push(samples) {
    let written = 0;
    for (const s of samples) {
      if (this.#shared) {
        const h = Atomics.load(this.#header, 0);
        const t = Atomics.load(this.#header, 1);
        if ((t + 1) % this.#cap === h) break;                    // full → bırak
        this.#buf[t] = s;                                        // Float32Array → float32 (K2)
        Atomics.store(this.#header, 1, (t + 1) % this.#cap, 0);
        Atomics.notify(this.#header, 1, 1);                      // opsiyonel wakeup
      } else {
        if ((this.#tail + 1) % this.#cap === this.#head) break;
        this.#buf[this.#tail] = s;
        this.#tail = (this.#tail + 1) % this.#cap;
      }
      written++;
    }
    return written;
  }

  // Tüketici (AudioWorklet process() İÇİNDE — non-blocking,lock/wait YOK)
  fill(dst /* Float32Array */) {
    let read = 0;
    while (read < dst.length) {
      let h, t;
      if (this.#shared) {
        h = Atomics.load(this.#header, 0);
        t = Atomics.load(this.#header, 1);
      } else {
        h = this.#head; t = this.#tail;
      }
      if (h === t) break;                                        // empty → ses yok, drop
      dst[read++] = this.#buf[h];                                // okuma → K2 float32
      if (this.#shared) {
        Atomics.store(this.#header, 0, (h + 1) % this.#cap, 0);
      } else {
        this.#head = (h + 1) % this.#cap;
      }
    }
    for (; read < dst.length; read++) dst[read] = 0;             // eksik kalan: sessizlik
    return read;
  }
}

// Worklet: kuyruktan oku → işle → output (kilit yok, bloklama yok)
class NevaStreamProcessor extends AudioWorkletProcessor {
  #ring = new SpscRing(16384); // ⚠️ SABİT: worklet scope'ta SAB nedir? (SharedArrayBuffer
                                // global worklet'te var mı → VERIFICATION REQUIRED, §8 not 4)
  process(inputs, outputs) {
    const output = outputs[0];
    if (!output || output.length === 0) return true;
    const frame = output[0];                    // 128 frame
    this.#ring.fill(frame);                     // non-blocking okuma
    for (let ch = 1; ch < output.length; ch++) output[ch].set(frame); // mono→çift kopya
    return true;
  }
}
registerProcessor('neva-stream', NevaStreamProcessor);
```

---

## §5 Örnek 4 — WAV 32f okuma/yazma (K3 · K1): DataView + ArrayBuffer

> **K3/K1:** WAV `audioFormat = 3` (IEEE_FLOAT), little-endian → `DataView.setFloat32(off,
> v, true)` / `getFloat32(off, true)` (3. argüman `true` = LE). Chunk zinciri parse edilir
> (sihirli 44 bayt YOK — §8 not 3). 05 §5 Yol A'nın encode adımı ile aynı desen.

```javascript
'use strict';

// K3/K1 — WAV 32f (format 3) encode/decode (DataView + ArrayBuffer, LE)
class Wav32f {
  /** @param {Float32Array} samples interleaved @param {number} channels @param {number} sampleRate (K4) */
  static encode(samples, channels, sampleRate) {
    const dataSize = samples.length * 4;                  // 4 bayt = float32
    const buffer = new ArrayBuffer(44 + dataSize);        // 16 bayt fmt (standart 44 bayt)
    const view = new DataView(buffer);
    const writeStr = (off, s) => { for (let i = 0; i < s.length; i++) view.setUint8(off + i, s.charCodeAt(i)); };

    writeStr(0, 'RIFF');
    view.setUint32(4, 36 + dataSize, true);               // LE
    writeStr(8, 'WAVE');
    writeStr(12, 'fmt ');
    view.setUint32(16, 16, true);                         // fmt chunk 16 bayt
    view.setUint16(20, 3, true);                          // format 3 = IEEE_FLOAT (K3)
    view.setUint16(22, channels, true);
    view.setUint32(24, sampleRate, true);                 // K4/K6: SR başlıkta
    view.setUint32(28, sampleRate * channels * 4, true);  // byteRate
    view.setUint16(32, channels * 4, true);               // blockAlign
    view.setUint16(34, 32, true);                         // bitsPerSample
    writeStr(36, 'data');
    view.setUint32(40, dataSize, true);
    for (let i = 0; i < samples.length; i++) {
      view.setFloat32(44 + i * 4, samples[i], true);      // f32 LE (K1/K3)
    }
    return buffer;
  }

  /** @returns {{sampleRate:number, channels:number, samples:Float32Array}} */
  static decode(arrayBuffer) {
    const view = new DataView(arrayBuffer);
    const readStr = (off, len) => {
      let s = '';
      for (let i = 0; i < len; i++) s += String.fromCharCode(view.getUint8(off + i));
      return s;
    };
    if (readStr(0, 4) !== 'RIFF' || readStr(8, 4) !== 'WAVE') throw new Error('Geçersiz RIFF/WAVE');

    let offset = 12; let sampleRate = 0; let channels = 0; let format = 0; let bits = 0;
    let dataOffset = -1; let dataSize = 0;
    while (offset + 8 <= view.byteLength) {               // chunk zinciri (sihirli ofset yok)
      const id = readStr(offset, 4);
      const size = view.getUint32(offset + 4, true);      // LE
      if (id === 'fmt ') {
        format = view.getUint16(offset + 8, true);
        channels = view.getUint16(offset + 10, true);
        sampleRate = view.getUint32(offset + 12, true);    // K4/K6
        bits = view.getUint16(offset + 22, true);
      } else if (id === 'data') {
        dataOffset = offset + 8; dataSize = size;
        break;
      }
      offset += 8 + size + (size % 2);                    // pad
    }
    if (format !== 3 || bits !== 32 || dataOffset < 0) {
      throw new Error(`Yalnız format 3 / 32 bit (gelen: ${format}/${bits})`);
    }
    const count = Math.floor(dataSize / 4);
    const samples = new Float32Array(count);              // K1: float32 kanal
    for (let i = 0; i < count; i++) {
      samples[i] = view.getFloat32(dataOffset + i * 4, true); // f32 LE
    }
    return { sampleRate, channels, samples };
  }
}
```

---

## §6 Örnek 5 — Compressor + brickwall limiter (R10): envelope + -12 dB / 4:1 + clamp + TPDF dither (K3)

> **R10 → K3.** Tarayıcıda `DynamicsCompressorNode` hazır (05 §5 Yol A) — bu örnek
> **elle yazılmış** Worklet versiyonudur (daha az node, kontrol tam). **K3 dither:** Web
> Audio zaten float32 → dither'a gerek yok; dither yalnız **dosyadan 24/16-bit int'e
> indirme** anında (encode öncesi) uygulanır. **⚠️ Deterministik replay:** `Math.random()`
> tohumsuz → aynı WAV iki kez aynı byte olmaz; deterministik dither için tohumlu PRNG
> (mulberry32 — §8 not 5) önerilir.

```javascript
'use strict';

// R10 — peak compressor + brickwall limiter (AudioWorklet, K2 float32)
class NevaCompProcessor extends AudioWorkletProcessor {
  #env = 0;
  #thresholdDb = -12;   // R10 eşiği
  #ratio = 4;           // 4:1
  #attackCoef; #releaseCoef;

  constructor() {
    super();
    // K4: worklet global sampleRate (AudioContext SR = cihaz SR — §7, K5)
    const attack = Math.exp(-1 / (0.005 * sampleRate));   // 5 ms
    const release = Math.exp(-1 / (0.100 * sampleRate));  // 100 ms
    this.#attackCoef = attack;
    this.#releaseCoef = release;
  }

  process(inputs, outputs) {
    const input = inputs[0]; const output = outputs[0];
    if (!input || input.length === 0) return true;

    const threshold = 10 ** (this.#thresholdDb / 20);
    for (let ch = 0; ch < output.length; ch++) {
      const src = input[Math.min(ch, input.length - 1)];
      const dst = output[ch];
      for (let i = 0; i < dst.length; i++) {              // 128 frame
        const x = src[i];
        const ax = Math.abs(x);
        // peak envelope (attack/release) — tek zarf (stereo için L/R max alınır: not §8)
        this.#env = ax > this.#env
          ? this.#attackCoef * this.#env + (1 - this.#attackCoef) * ax
          : this.#releaseCoef * this.#env + (1 - this.#releaseCoef) * ax;

        let gr = 1;
        if (this.#env > threshold && this.#env > 0) {
          gr = threshold * (this.#env / threshold) ** (1 / this.#ratio) / this.#env;
        }
        let y = x * gr;
        y = y < -1 ? -1 : y > 1 ? 1 : y;                  // brickwall clamp 0 dBFS (K3)
        dst[i] = y;
      }
    }
    return true;
  }
}
registerProcessor('neva-comp', NevaCompProcessor);

// K3 — TPDF dither (yalnız 24/16-bit int'e düşüşte; float32'de GEREKMEZ)
// r1,r2 düzgün rastgele → toplam üçgen PDF → [-1 LSB, +1 LSB]
function tpdfDither(sample, levels = 16777216) {          // 24-bit: 2^24
  const lsb = 1 / levels;
  const r1 = Math.random();                               // ⚠️ tohumsuz → §8 not 5
  const r2 = Math.random();
  return sample + (r1 + r2 - 1) * lsb;
}
```

---

## §7 Örnek 6 — OS/cihaz enum otomatik varsayılan (R18): navigator + Web Audio SR

> **R18 — web tarafının en güzel uyumu:** `new AudioContext()` **zaten cihazın
> desteklediği SR'de** açılır → `context.sampleRate` = cihaz SR'si → **K5 otomatik
> uygulanır** (192 desteklemeyen cihazda 96/48 döner, hiç kod yazmadan). K4 listesine
> uymuyorsa §3 `nevaFallbackSampleRate()` ile oturt. OS enum: `navigator.userAgentData`
> (Client Hints — Chromium) varsa onu, yoksa `navigator.platform`/`userAgent` (deprecated
> — yalnız teşhis, ≠ cihaz yeteneği). `userAgentData` **standard değildir** (Chromium'a
> özgü; TS'te union type karşılığı — 06 §7, tiplendirme notu §8).

```javascript
'use strict';

// R18 — OS enum (JS'de enum yok → frozen object + frozen değerler; TS'te union, §7/06)
const NEVA_OS = Object.freeze({
  WINDOWS: 'windows', MACOS: 'macos', LINUX: 'linux', OTHER: 'other',
});

function detectOsFamily() {
  const uaData = navigator.userAgentData;                 // Chromium Client Hints
  if (uaData && Array.isArray(uaData.platform)) {         // ⚠️ platform alanı enum değil
    const p = (uaData.platform[0] || '').toLowerCase();   // ör. "Windows"/"macOS"/"Linux"
    if (p.includes('win')) return NEVA_OS.WINDOWS;
    if (p.includes('mac')) return NEVA_OS.MACOS;
    if (p.includes('linux') || p.includes('android')) return NEVA_OS.LINUX;
  }
  const p = (navigator.platform || navigator.userAgent || '').toLowerCase(); // deprecated — teşhis
  if (p.includes('win')) return NEVA_OS.WINDOWS;
  if (p.includes('mac')) return NEVA_OS.MACOS;
  if (p.includes('linux')) return NEVA_OS.LINUX;
  return NEVA_OS.OTHER;
}

// R18 + K5 + K6 — cihaz SR'sini keşfet (AudioContext = cihazın desteklediği SR)
async function nevaDetectDevice() {
  const supported = globalThis.AudioContext || globalThis.webkitAudioContext;
  if (!supported) {
    return { os: detectOsFamily(), supported: false, sampleRate: null }; // Web Audio yok
  }
  const ctx = new supported();                            // boş context — SR zaten cihaz SR
  const deviceSr = ctx.sampleRate;                        // K5: cihazın en yüksek yerel SR'si
  await ctx.close();

  const effectiveSr = nevaFallbackSampleRate(deviceSr);   // §3 — K4 listesi (44.1…192k)
  return {
    os: detectOsFamily(),
    supported: true,
    sampleRate: deviceSr,          // gerçek cihaz SR
    effectiveSampleRate: effectiveSr, // K4'e oturtulmuş (fallback)
    bitPerfect: deviceSr === effectiveSr, // K6: SR eşit + float32 → dönüşüm yok
    audioWorklet: typeof AudioWorkletProcessor !== 'undefined', // canlı DSP yüzeyi
  };
}
// NOT: takeUserGesture() sonrasında ctx.resume() gerekir (otoplay policy) — §8 not 6
```

---

## §8 Dile özel notlar

1. **`var` yok · `any` yok · `'use strict'`:** modern ES2024 (class fields, private
   `#alan`). JS dinamiktir — tip güvenliği için **06-typescript.md** (strict:true) kullan.
2. **float gerçekliği:** JS `number` = float64; Web Audio kanalları `Float32Array` =
   float32. Hesap float64, **depolama/kanal float32** → her `dst[i] = ...` yazımı float32
   yuvarlamasıdır (K1/K2'nin web karşılığı — PHP'deki `pack('g')` ile aynı fenomen).
3. **WAV fmt 16 vs 18 bayt:** encode 16 bayt fmt (44 bayt toplam, yaygın) yazdı;
   non-PCM'te `cbSize` (18 bayt) bekleyen okuyucular olabilir → ⚠️ VERIFICATION REQUIRED
   (hedef tarayıcı/`decodeAudioData` ile test — Faz 0).
4. **SharedArrayBuffer + worklet:** `SharedArrayBuffer` **main thread'de** COOP/COEP
   başlıkları olmadan yok; **worklet scope'unda varlığı ve `Atomics` erişimi
   ⚠️ VERIFICATION REQUIRED** (Faz 0 spike: `crossOriginIsolated` + worklet global test).
   SAB yoksa §4'teki non-shared dal (tek thread mantığı) kullanılır — worklet tek başına
   okuyucu olduğu için SAB'siz halka worklet içinden **yazılamaz** (yalnız main→worklet
   kopya deseni gerekir).
5. **Deterministik dither:** `Math.random()` tohumsuz. Deterministik gerekiyorsa tohumlu
   PRNG öner — mulberry32: `s = (s + 0x6D2B79F5)|0; t = Math.imul(s ^ (s >>> 15), 1 | s);
   t = (t + Math.imul(t ^ (t >>> 7), 61 | t)) ^ t; return ((t ^ (t >>> 14)) >>> 0) / 4294967296;`
   (standart bilinen LCG-sınıfı desen; ⚠️ üretimde sabit tohum + test).
6. **Autoplay/otoplay policy:** `AudioContext` kullanıcı etkileşimi olmadan `suspended`;
   `resume()` zorunlu. Worklet `registerProcessor` dosyası `audioWorklet.addModule()` ile
   yüklenir — sessizlikte `process()` `true` döner (worklet yaşam döngüsü).
7. **`sampleRate` global (worklet scope):** `AudioContext` SR'si = cihaz SR → K4/K5/K6
   tek değişkende toplanır (§7 vurgusu — en güzel K5 otomasyonu).

## §9 Sürüm Geçmişi

| Sürüm | Tarih | Değişiklik |
|---|---|---|
| 1.0.0 | 2026-10-15 | İlk sürüm: 6 örnek (RBJ EQ worklet · 32f 128-frame blok · SPSC ring · WAV DataView · comp+limiter · R18 OS/cihaz enum) |
