---
title: "Neva Engine — TypeScript Kod Örnekleri (Web Mixer · Tipli Katman)"
type: reference
category: nevaengine
version: 1.0.0
status: active
authority: "Master Orchestrator → Embedded Engineer"
updated: 2026-10-15
---

# Neva Engine — TypeScript Kod Örnekleri (Web Mixer · Tipli Katman)

> **Durum:** v1.0.0 · derlenmedi/test edilmedi (araştırma MD) · K1-K6 uyumlu

**İlgili:** [[11-cok-dilli-kod-ornekleri]] (index) · [[10-io-format-32bit-192khz-kurallari]] (K1-K6 SSOT) · [[05-neva-mixer-web-pcm-plani]] (Layer 3) · [[05-javascript]] (05-javascript.md — aynı desen, JS formu)

## §1 Bağlam — TS'in Neva'daki rolü

TypeScript, Neva web mixer'in **tipli katmanıdır**: 05 §2 Layer 3 (vanilla JS proje
standardı olsa da) mantıksal sözleşmeleri (WS JSON op'ları, cihaz yeteneği, DSP param
arayüzleri) `interface`/`type` ile sabitler. JS ile aynı runtime: **AudioWorklet
128-frame bloklar**, `Float32Array` kanalları.

**TS + AudioWorklet farkı (önemli):** `lib.dom.d.ts` içinde `AudioWorkletProcessor` ve
`registerProcessor` **TANIMLI DEĞİLDİR** (⚠️ gerçek durum: Web Audio işleticisi tip
paketine dahil değil) → §2'de `declare` ile sözleşme yazılır (veya `@types/audioworklet`
paketi — bağımlılık eklemek bu dosyada YOK, zero-dependency tercih). `process(inputs,
outputs, parameters)` çerçevesi (frame = 128 örnek) tiplenir; `sampleRate` worklet
global'ıdır ve yine `declare` gerektirir.

**K1-K6 özeti (doc 10 — bağlayıcı):**

| Kural | İçerik |
|---|---|
| K1 | Input **32-bit float** (`Float32Array`) |
| K2 | Internal processing **32-bit float** |
| K3 | Output **32-bit float** (24/16-bit int düşüşte **TPDF dither**) |
| K4 | SR **44.1–192 kHz** (varsayılan **48 kHz**) |
| K5 | Cihaz 192 sunmuyorsa → **en yüksek yerel SR** |
| K6 | Bit-perfect = **SR eşit + float32** |

**Dil kuralları (global AGENTS.md):** `strict: true` (derleme ayarı) · `interface`/`type` ·
**generic'i gereksiz kullanma** (yalnız gerçekten çoklu tip gerektiren yerde) ·
`Float32Array` her zaman tipli (`Float32Array`, `number` DEĞİL kanal parametresinde).

---

## §2 Örnek 1 — Biquad EQ (R16 çift form): RBJ peaking + Direct Form I · 48 kHz · K2

> **R16:** tek motor + 2 arayüz formu (parametrik/grafik) — tipler sözleşmeyi sabitler.
> **K2:** çalışma `Float32Array` (float32 kanal). AudioWorklet farkı: `process` çerçevesi
> `AudioWorkletProcessor` sınıfı ile `declare` edilir; `registerProcessor` tiplenir.

```typescript
'use strict';

// AudioWorklet tip sözleşmesi — lib.dom.d.ts'de YOK (§1 notu; paket eklenmedi)
declare const sampleRate: number; // worklet global — AudioContext SR (K4/K5)
declare function registerProcessor(name: string, ctor: new (options?: AudioWorkletNodeOptions) => AudioWorkletProcessor): void;
declare abstract class AudioWorkletProcessor {
  readonly port: MessagePort;
  constructor(options?: AudioWorkletNodeOptions);
  abstract process(inputs: Float32Array[][], outputs: Float32Array[][], parameters: Record<string, Float32Array>): boolean;
}
interface AudioWorkletNodeOptions { numberOfInputs?: number; numberOfOutputs?: number; outputChannelCount?: number[]; }

// R16 — parametrik bant sözleşmesi (arayüz formu: grafik 31 band de aynı type'ı kullanır)
interface PeakingBand {
  freq: number;   // Hz
  q: number;      // Q
  gainDb: number; // dB
}

class BiquadPeaking {
  #b0 = 1; #b1 = 0; #b2 = 0; #a1 = 0; #a2 = 0;
  #x1 = 0; #x2 = 0; #y1 = 0; #y2 = 0;

  setPeaking(band: PeakingBand, sr: number = 48000): void { // K4: SR parametrik
    const a = 10 ** (band.gainDb / 40);
    const w0 = (2 * Math.PI * band.freq) / sr;
    const alpha = Math.sin(w0) / (2 * band.q);
    const cosW0 = Math.cos(w0);
    const b0 = 1 + alpha * a, b1 = -2 * cosW0, b2 = 1 - alpha * a;
    const a0 = 1 + alpha / a, a1 = -2 * cosW0, a2 = 1 - alpha / a;
    this.#b0 = b0 / a0; this.#b1 = b1 / a0; this.#b2 = b2 / a0;
    this.#a1 = a1 / a0; this.#a2 = a2 / a0;
  }

  processSample(x: number): number { // hesap float64; kanal float32'ye yazarken yuvarlanır (§8 not 1)
    const y = this.#b0 * x + this.#b1 * this.#x1 + this.#b2 * this.#x2
            - this.#a1 * this.#y1 - this.#a2 * this.#y2;
    this.#x2 = this.#x1; this.#x1 = x;
    this.#y2 = this.#y1; this.#y1 = y;
    return y;
  }
  reset(): void { this.#x1 = this.#x2 = this.#y1 = this.#y2 = 0; }
}

// AudioWorklet: process çerçevesi (frame = 128 örnek) — tipli
class NevaEqProcessor extends AudioWorkletProcessor {
  #eq = new BiquadPeaking();
  #band: PeakingBand = { freq: 1000, q: 1.0, gainDb: 0 };

  process(inputs: Float32Array[][], outputs: Float32Array[][]): boolean {
    const input = inputs[0]; const output = outputs[0];
    if (!input || input.length === 0 || !output || output.length === 0) return true;

    this.#eq.setPeaking(this.#band, sampleRate); // K4: worklet global SR
    for (let ch = 0; ch < output.length; ch++) {
      const src = input[Math.min(ch, input.length - 1)];
      const dst = output[ch];
      for (let i = 0; i < dst.length; i++) {     // 128 frame
        dst[i] = this.#eq.processSample(src[i] ?? 0); // K2: Float32Array (tip güvenli)
      }
    }
    return true;
  }
}
registerProcessor('neva-eq', NevaEqProcessor);
```

---

## §3 Örnek 2 — 32f buffer döngüsü (K2/K4/K5): Float32Array + SR fallback

> **K2/K4/K5.** `sr` hesaplaması JS'teki ile aynı (paylaşılan mantık — 05 §3 ile birebir);
> fark: tüm imzalar `number` ile tiplenir, kanal parametreleri `Float32Array`.
> **AudioWorklet farkı (process-frame):** `process()` her çağrıda **128 frame** işler
> (sabit — spec); SR `sampleRate` global'inden gelir (K5 otomatik, §7).

```typescript
'use strict';

// K4/K5 — SR fallback sözleşmesi (05 §3 ile aynı mantık, tipli)
const NEVA_SR_LIST: readonly number[] = [44100, 48000, 88200, 96000, 176400, 192000]; // K4
const NEVA_DEFAULT_SR = 48000;                                                        // K4 varsayılan

function nevaFallbackSampleRate(deviceSr: number | null): number {
  if (deviceSr !== null && NEVA_SR_LIST.includes(deviceSr)) return deviceSr;          // K6: SR eşit
  let best = NEVA_DEFAULT_SR;
  for (const sr of NEVA_SR_LIST) {
    if (deviceSr === null || sr <= deviceSr) best = Math.max(best, sr);
  }
  return best; // K5: cihaz 192 sunmuyorsa en yüksek geçerli
}

// K2/K4/K5 — 32f blok döngüsü (in-place, sıfır allocate)
function nevaProcessBlock32f(block: Float32Array, dcOffset = 0): Float32Array {
  for (let i = 0; i < block.length; i++) {
    let y = (block[i] as number) + dcOffset;
    y = y < -1 ? -1 : y > 1 ? 1 : y;   // K3 clamp 0 dBFS
    block[i] = y;                       // Float32Array → float32 (K1/K2)
  }
  return block;
}

// Worklet: 128-frame blok + a-rate param (Float32Array)
class NevaGainProcessor extends AudioWorkletProcessor {
  #gain = 1.0;

  process(
    inputs: Float32Array[][],
    outputs: Float32Array[][],
    parameters: Record<string, Float32Array>,
  ): boolean {
    const input = inputs[0]; const output = outputs[0];
    if (!input || input.length === 0 || !output || output.length === 0) return true;

    const gainParam: Float32Array | undefined = parameters['gain']; // a-rate: her örnek
    for (let ch = 0; ch < output.length; ch++) {
      const src = input[Math.min(ch, input.length - 1)];
      const dst = output[ch];
      for (let i = 0; i < dst.length; i++) {          // 128 frame (process-frame farkı)
        const g = gainParam ? (gainParam[i] as number) : this.#gain;
        dst[i] = (src[i] as number) * g;
      }
      nevaProcessBlock32f(dst);                        // K3 clamp
    }
    return true;
  }
}
registerProcessor('neva-gain', NevaGainProcessor);
```

---

## §4 Örnek 3 — Buffer kuyruk/producer-consumer (R10): SPSC ring + Atomics

> **R10.** SharedArrayBuffer **varsa** (`crossOriginIsolated` — ⚠️ VERIFICATION REQUIRED,
> 05 §8 not 4 / JS §8 not 4) `Atomics` ile lock-free SPSC; **yoksa** tek-thread mantık.
> **worklet kuralı:** `process()` içinde blocking `Atomics.wait` **YASAK** (audio thread
> underrun) → kuyruk non-blocking: dolu drop, boş sessizlik.

```typescript
'use strict';

// R10 — SPSC ring: header Int32 (head/tail) + veri Float32Array (K2)
interface RingHeader {
  head: number;   // okuyucu konumu (worklet)
  tail: number;   // yazıcı konumu (main)
  capacity: number;
}

class SpscRing {
  static readonly HEADER_INTS = 4; // [0]=head [1]=tail [2]=unused [3]=capacity
  #buf: Float32Array;
  #cap: number;
  #head = 0;
  #tail = 0;
  #header: Int32Array | null = null;
  #shared: boolean;

  constructor(capacity: number, sharedBuffer: SharedArrayBuffer | null = null) {
    this.#cap = capacity;
    this.#shared = sharedBuffer instanceof SharedArrayBuffer;
    if (this.#shared && sharedBuffer) {
      this.#header = new Int32Array(sharedBuffer, 0, SpscRing.HEADER_INTS);
      this.#buf = new Float32Array(sharedBuffer, SpscRing.HEADER_INTS * 4);
      Atomics.store(this.#header, 3, capacity);
      Atomics.store(this.#header, 0, 0);
      Atomics.store(this.#header, 1, 0);
    } else {
      this.#buf = new Float32Array(capacity); // zero-alloc: baştan allocate (K2)
      this.#header = null;
    }
  }

  // Üretici (main thread) — ASLA bloklanmaz; doluysa bırakılan sample sayısı döner
  push(samples: Float32Array): number {
    let written = 0;
    for (let i = 0; i < samples.length; i++) {
      if (this.#header) {
        const h = Atomics.load(this.#header, 0);
        const t = Atomics.load(this.#header, 1);
        if ((t + 1) % this.#cap === h) break;                 // full → backpressure
        this.#buf[t] = samples[i] as number;
        Atomics.store(this.#header, 1, (t + 1) % this.#cap);
        Atomics.notify(this.#header, 1, 1);
      } else {
        if ((this.#tail + 1) % this.#cap === this.#head) break;
        this.#buf[this.#tail] = samples[i] as number;
        this.#tail = (this.#tail + 1) % this.#cap;
      }
      written++;
    }
    return written;
  }

  // Tüketici (AudioWorklet process() İÇİNDE) — non-blocking, lock/wait YOK
  fill(dst: Float32Array): number {
    let read = 0;
    while (read < dst.length) {
      let h: number, t: number;
      if (this.#header) { h = Atomics.load(this.#header, 0); t = Atomics.load(this.#header, 1); }
      else { h = this.#head; t = this.#tail; }
      if (h === t) break;                                     // empty → drop
      dst[read++] = this.#buf[h] as number;
      if (this.#header) Atomics.store(this.#header, 0, (h + 1) % this.#cap);
      else this.#head = (h + 1) % this.#cap;
    }
    for (; read < dst.length; read++) dst[read] = 0;          // eksik: sessizlik (128 frame tam)
    return read;
  }
}

class NevaStreamProcessor extends AudioWorkletProcessor {
  #ring = new SpscRing(16384); // ⚠️ worklet scope'ta SharedArrayBuffer global erişimi
                                // VERIFICATION REQUIRED (05 §8 not 4) — null-fallback dahil
  process(inputs: Float32Array[][], outputs: Float32Array[][]): boolean {
    const output = outputs[0];
    if (!output || output.length === 0) return true;
    const frame = output[0] as Float32Array;
    this.#ring.fill(frame);                        // non-blocking okuma
    for (let ch = 1; ch < output.length; ch++) (output[ch] as Float32Array).set(frame);
    return true;
  }
}
registerProcessor('neva-stream', NevaStreamProcessor);
```

---

## §5 Örnek 4 — WAV 32f okuma/yazma (K3 · K1): DataView + ArrayBuffer

> **K3/K1.** `DataView` LE parametresi `true` (WAV little-endian zorunlu). JS dosyası
> (05 §5) ile aynı algoritma; fark: `Wav32fHeader` interface'i ile başlık sözleşmesinin
> tiplenmesi. `decode` sonucu `Float32Array` (K1 float32 kanal).

```typescript
'use strict';

// K3/K1 — WAV 32f sözleşmeleri
interface Wav32fHeader {
  sampleRate: number;  // K4/K6
  channels: number;
  bitsPerSample: 32;   // sabit tip: yalnız 32
  audioFormat: 3;      // sabit tip: yalnız 3 (IEEE_FLOAT)
}

interface Wav32fDecodeResult {
  header: Wav32fHeader;
  samples: Float32Array; // K1: float32, interleaved
}

class Wav32f {
  static encode(samples: Float32Array, channels: number, sampleRate: number): ArrayBuffer {
    const dataSize = samples.length * 4;
    const buffer = new ArrayBuffer(44 + dataSize);
    const view = new DataView(buffer);
    const writeStr = (off: number, s: string): void => {
      for (let i = 0; i < s.length; i++) view.setUint8(off + i, s.charCodeAt(i));
    };

    writeStr(0, 'RIFF');
    view.setUint32(4, 36 + dataSize, true);
    writeStr(8, 'WAVE');
    writeStr(12, 'fmt ');
    view.setUint32(16, 16, true);                        // fmt 16 bayt (§8 not 3: 18 bayt varyantı ⚠️)
    view.setUint16(20, 3, true);                         // format 3 (K3)
    view.setUint16(22, channels, true);
    view.setUint32(24, sampleRate, true);                // K4/K6
    view.setUint32(28, sampleRate * channels * 4, true);
    view.setUint16(32, channels * 4, true);
    view.setUint16(34, 32, true);                        // 32 bit
    writeStr(36, 'data');
    view.setUint32(40, dataSize, true);
    for (let i = 0; i < samples.length; i++) {
      view.setFloat32(44 + i * 4, samples[i] as number, true); // f32 LE
    }
    return buffer;
  }

  static decode(arrayBuffer: ArrayBuffer): Wav32fDecodeResult {
    const view = new DataView(arrayBuffer);
    const readStr = (off: number, len: number): string => {
      let s = '';
      for (let i = 0; i < len; i++) s += String.fromCharCode(view.getUint8(off + i));
      return s;
    };
    if (readStr(0, 4) !== 'RIFF' || readStr(8, 4) !== 'WAVE') throw new Error('Geçersiz RIFF/WAVE');

    let offset = 12; let sampleRate = 0; let channels = 0;
    let dataOffset = -1; let dataSize = 0;
    while (offset + 8 <= view.byteLength) {              // chunk zinciri (sihirli ofset yok)
      const id = readStr(offset, 4);
      const size = view.getUint32(offset + 4, true);
      if (id === 'fmt ') {
        const format = view.getUint16(offset + 8, true);
        const bits = view.getUint16(offset + 22, true);
        if (format !== 3 || bits !== 32) throw new Error(`Yalnız format 3 / 32 bit (gelen: ${format}/${bits})`);
        channels = view.getUint16(offset + 10, true);
        sampleRate = view.getUint32(offset + 12, true);   // K4/K6
      } else if (id === 'data') {
        dataOffset = offset + 8; dataSize = size;
        break;
      }
      offset += 8 + size + (size % 2);
    }
    if (dataOffset < 0) throw new Error('data chunk yok');

    const count = Math.floor(dataSize / 4);
    const samples = new Float32Array(count);
    for (let i = 0; i < count; i++) {
      samples[i] = view.getFloat32(dataOffset + i * 4, true); // K1: f32 LE
    }
    return { header: { sampleRate, channels, bitsPerSample: 32, audioFormat: 3 }, samples };
  }
}
```

---

## §6 Örnek 5 — Compressor + brickwall limiter (R10): envelope + -12 dB / 4:1 + clamp + TPDF dither (K3)

> **R10 → K3.** 05 §6 ile aynı mantık; fark: `CompParams` interface (eşik/oran/attack/
> release) ve dither için **tohumlu PRNG** tipi (deterministik replay — §8 not 5).
> `Math.random()` tohumsuzdur; üretimde xorshift/mulberry32 tohumlu PRNG önerilir.

```typescript
'use strict';

// R10 — dinamik parametreler sözleşmesi
interface CompParams {
  thresholdDb: number;  // -12 (R10)
  ratio: number;        // 4:1
  attackMs: number;     // 5
  releaseMs: number;    // 100
}

// K3 — deterministik PRNG sözleşmesi (0 = ...1.0 arası; tohumlu → replay)
type Prng = () => number;

const mulberry32 = (seed: number): Prng => {
  let s = seed >>> 0;
  return () => {
    s = (s + 0x6d2b79f5) | 0;
    let t = Math.imul(s ^ (s >>> 15), 1 | s);
    t = (t + Math.imul(t ^ (t >>> 7), 61 | t)) ^ t;
    return ((t ^ (t >>> 14)) >>> 0) / 4294967296;
  };
};

class NevaCompProcessor extends AudioWorkletProcessor {
  #env = 0;
  #p: CompParams = { thresholdDb: -12, ratio: 4, attackMs: 5, releaseMs: 100 };
  #attackCoef: number;
  #releaseCoef: number;
  #prng: Prng = Math.random(); // ⚠️ tohumsuz — deterministik replay için mulberry32(SEED) (§8 not 5)

  constructor() {
    super();
    this.#attackCoef = Math.exp(-1 / ((this.#p.attackMs / 1000) * sampleRate));  // K4: global SR
    this.#releaseCoef = Math.exp(-1 / ((this.#p.releaseMs / 1000) * sampleRate));
  }

  process(inputs: Float32Array[][], outputs: Float32Array[][]): boolean {
    const input = inputs[0]; const output = outputs[0];
    if (!input || input.length === 0 || !output || output.length === 0) return true;

    const threshold = 10 ** (this.#p.thresholdDb / 20);
    for (let ch = 0; ch < output.length; ch++) {
      const src = input[Math.min(ch, input.length - 1)];
      const dst = output[ch];
      for (let i = 0; i < dst.length; i++) {            // 128 frame
        const x = src[i] as number;
        const ax = Math.abs(x);
        this.#env = ax > this.#env
          ? this.#attackCoef * this.#env + (1 - this.#attackCoef) * ax
          : this.#releaseCoef * this.#env + (1 - this.#releaseCoef) * ax;

        let gr = 1;
        if (this.#env > threshold && this.#env > 0) {
          gr = threshold * (this.#env / threshold) ** (1 / this.#p.ratio) / this.#env;
        }
        let y = x * gr;
        y = y < -1 ? -1 : y > 1 ? 1 : y;                // brickwall clamp (K3)
        dst[i] = y;
      }
    }
    return true;
  }

  /** K3 — TPDF dither: yalnız 24/16-bit int'e düşüşte (float32'de gerekmez). */
  tpdfDither(sample: number, levels = 16777216): number {
    const lsb = 1 / levels;
    const r1 = this.#prng();
    const r2 = this.#prng();
    return sample + (r1 + r2 - 1) * lsb; // üçgen PDF → [-LSB, +LSB]
  }
}
registerProcessor('neva-comp', NevaCompProcessor);
```

---

## §7 Örnek 6 — OS/cihaz enum otomatik varsayılan (R18): union type enum + Web Audio SR

> **R18 + K5 otomasyonu:** `AudioContext.sampleRate` = **cihazın desteklediği SR** →
> K5 kod yazmadan uygulanır (JS §7 ile aynı). Fark: JS'te frozen object olan enum,
> TS'te **union type** (`'windows' | 'macos' | ...`) — `const` assertion ile literal
> daraltma. `navigator.userAgentData` (Client Hints) **lib.dom.d.ts'de yok** → `declare`
> ile eklenir (paket yok, zero-dependency).

```typescript
'use strict';

// R18 — OS enum: TS union type (JS frozen object'un tip güvenli karşılığı)
type OsFamily = 'windows' | 'macos' | 'linux' | 'other';

// Client Hints — standart değil (Chromium): lib.dom.d.ts'de tanımsız → declare
interface NavigatorUaData {
  readonly platform?: string;
}
interface NavigatorWithUAData extends Navigator {
  readonly userAgentData?: NavigatorUaData;
}
declare const navigator: NavigatorWithUAData; // mevcut navigator'a genişletilmiş görünüm

function detectOsFamily(): OsFamily {
  const uaData = navigator.userAgentData;
  if (uaData?.platform) {                     // Chromium — telemetry değil, teşhis
    const p = uaData.platform.toLowerCase();
    if (p.includes('win')) return 'windows';
    if (p.includes('mac')) return 'macos';
    if (p.includes('linux') || p.includes('android')) return 'linux';
  }
  const p = (navigator.platform ?? navigator.userAgent ?? '').toLowerCase(); // deprecated
  if (p.includes('win')) return 'windows';
  if (p.includes('mac')) return 'macos';
  if (p.includes('linux')) return 'linux';
  return 'other';
}

// R18 + K5 + K6 — cihaz yeteneği sözleşmesi
interface DeviceCapability {
  os: OsFamily;
  supported: boolean;          // Web Audio var mı
  sampleRate: number | null;   // gerçek cihaz SR (AudioContext)
  effectiveSampleRate: number | null; // K4 listesine oturtulmuş (K5 fallback)
  bitPerfect: boolean;         // K6: SR eşit + float32 → dönüşüm yok
  audioWorklet: boolean;       // canlı DSP yüzeyi (process-frame)
}

async function nevaDetectDevice(): Promise<DeviceCapability> {
  const Ctor: typeof AudioContext | undefined =
    globalThis.AudioContext ?? (globalThis as { webkitAudioContext?: typeof AudioContext }).webkitAudioContext;
  if (!Ctor) {
    return { os: detectOsFamily(), supported: false, sampleRate: null, effectiveSampleRate: null, bitPerfect: false, audioWorklet: false };
  }
  const ctx = new Ctor();                         // boş context
  const deviceSr = ctx.sampleRate;                // K5: cihazın SR'si — otomatik
  await ctx.close();

  const effectiveSr = nevaFallbackSampleRate(deviceSr); // §3 (K4 listesi)
  return {
    os: detectOsFamily(),
    supported: true,
    sampleRate: deviceSr,
    effectiveSampleRate: effectiveSr,
    bitPerfect: deviceSr === effectiveSr,          // K6
    audioWorklet: typeof AudioWorkletProcessor !== 'undefined',
  };
}
// NOT: kullanıcı etkileşimi sonrası ctx.resume() (otoplay policy) — §8 not 6
```

---

## §8 Dile özel notlar

1. **`strict: true` zorunlu** (tsconfig): `noImplicitAny`, `strictNullChecks` — bu
   dosyadaki `as number`/`?? 0` kullanımları `Float32Array` index'inin `number |
   undefined` dönebilmesindendir (noUncheckedIndexedAccess açık ise; ⚠️ proje tsconfig'ü
   Faz 0'da doğrulanacak). Kanal parametreleri asla `number[]` DEĞİL `Float32Array`.
2. **float gerçekliği (JS ile aynı):** TS `number` = float64; Web Audio kanalı
   `Float32Array` = float32. Her `dst[i] = ...` float32 yuvarlamasıdır (K1/K2 — PHP
   `pack('g')` ile aynı fenomen, §1/JS §8 not 2).
3. **AudioWorklet tipleri yok:** `AudioWorkletProcessor`/`registerProcessor`/worklet
   `sampleRate` `lib.dom.d.ts`'de tanımsız → bu dosyada `declare` (zero-dependency).
   Paket yolu: `@types/audioworklet` (⚠️ paket adı/sürümü bağımlılık kontrolünden
   geçmeli — bu MD'de eklenmedi). `userAgentData` da standart dışı (Chromium) → `declare`.
4. **Generic'i gereksiz kullanma:** `Record<string, Float32Array>`, union type ve
   interface yeterli; `SpscRing<T>` gibi gereksiz generik **yok** (yalnız gerçek
   çok-tipli gereksinimde — bu dosyada yok).
5. **Deterministik dither:** `Prng` type + `mulberry32(seed)` (§6) — `Math.random()`
   tohumsuz olduğundan aynı WAV'ı iki kez aynı byte üretmez; deterministik gerekiyorsa
   tohum sabitlenmeli (test edilebilirlik).
6. **Autoplay/otoplay + worklet yaşam döngüsü:** `AudioContext` `suspended` başlar →
   kullanıcı etkileşiminde `resume()`; `audioWorklet.addModule()` ile processor
   yüklenir; sessizlikte `process()` `true` döner. `SharedArrayBuffer` için
   `crossOriginIsolated` (COOP/COEP) başlıkları şart — ⚠️ VERIFICATION REQUIRED (Faz 0,
   05 §8 not 4). SAB yoksa §4 null-fallback (tek-thread mantık) kullanılır.
7. **AudioWorklet process-frame farkı:** `process(inputs, outputs, parameters)` her
   çağrıda **128 frame** (sabit) işler — `OfflineAudioContext.renderQuantum` ile aynı;
   `ScriptProcessorNode` (128–4096, main thread) yerine worklet kullanılır (gerçek
   zamanlı sınıfı — bkz JS §1 dürüstlük notu).

## §9 Sürüm Geçmişi

| Sürüm | Tarih | Değişiklik |
|---|---|---|
| 1.0.0 | 2026-10-15 | İlk sürüm: 6 örnek (tipli RBJ EQ worklet · Float32Array blok + SR fallback · SPSC ring · DataView WAV · comp+limiter + Prng · R18 union enum + cihaz SR) |
