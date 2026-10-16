---
title: "Neva Engine — Rust Kod Örnekleri (6 Örnek)"
type: reference
category: nevaengine
version: 1.0.0
status: active
authority: "Master Orchestrator → Embedded Engineer"
updated: 2026-10-15
---

# Neva Engine — Rust Kod Örnekleri

> **Durum:** v1.0.0 · derlenmedi/test edilmedi (araştırma MD) · K1-K6 uyumlu

**Zorunlu Bağlantılar:** [[11-cok-dilli-kod-ornekleri]] (index) · [[10-io-format-32bit-192khz-kurallari]] (K1-K6 SSOT) · [[06-gereksinimler-ve-farkliliklar]] (R16 · R18) · [[07-mimari-diyagram-kontrol-yuzeyleri]] (R10)

## §1 Bağlam

Rust, Neva Engine için **sistem güvenliği alternatifi**dir (index §6 — C++ çekirdeğinin
dili değil, alternatif sahne). Tüm I/O ve DSP yolu **32-bit float** üzerinden akar
(K1-K6 — doc 10 SSOT):

| Kural | İçerik |
|---|---|
| K1 | Input (capture/loopback) **32-bit float** |
| K2 | Internal zincir **32-bit float** (`f32`) |
| K3 | Output **32-bit float** — 24-bit int'e düşüşte **TPDF dither zorunlu** |
| K4 | SR **44.1 / 48 / 88.2 / 96 / 176.4 / 192 kHz** · varsayılan **48 kHz** |
| K5 | Cihaz 192 sunmuyorsa → **cihazın en yüksek yerel SR'si** |
| K6 | Bit-perfect = **SR eşit + float32** |

İlgili gereksinimler: **R16** (EQ çift form) · **R10** (compressor + brickwall limiter) ·
**R18** (cihaz bazlı otomatik varsayılan).

> ⚠️ **Bu dosyadaki kod derlenmedi/test edilmedi; vault referans örneğidir.** Üretim kodu
> doc 01/03 planındaki Faz 0 build+test kapısından geçer. Yalnız Rust **standart
> kütüphanesi** (`std`) kullanılmıştır — `Cargo.toml` bağımlılığı YOK. Zero-hallucination:
> doğrulanamayan satır `⚠️ VERIFICATION REQUIRED` ile işaretlidir.

## §2 Örnek 1 — Biquad EQ (R16 çift form) · (R16 · K2)

RBJ peaking koefisyonları + Direct Form I ve Transposed DF-II. `&mut self` deseni:
durum değişimi münhasır ödünç ile güvence altında (ownership — §8).

```rust
// neva_biquad.rs — std only, f32 (K2)
/// RBJ peaking (Audio EQ Cookbook) — normalize edilmiş koefisyonlar (a0 = 1)
#[derive(Clone, Copy, Default)]
pub struct BiquadCoeffs {
    pub b0: f32, pub b1: f32, pub b2: f32,
    pub a1: f32, pub a2: f32,
}

/// f0 (Hz), q, gain_db — fs = örnekleme hızı (K4: 44100..192000)
pub fn make_peaking(f0: f32, q: f32, gain_db: f32, fs: f32) -> BiquadCoeffs {
    // Parametre doğrulama — K4 aralığı içinde
    let q = q.clamp(0.05, 32.0);
    let f0 = f0.clamp(10.0, 0.49 * fs);          // Nyquist'e çarpmama
    let gain_db = gain_db.clamp(-24.0, 24.0);

    let a = 10f32.powf(gain_db / 40.0);          // amplitude
    let w0 = 2.0 * std::f32::consts::PI * (f0 / fs);
    let cos_w0 = w0.cos();
    let alpha = w0.sin() / (2.0 * q);
    let a0 = 1.0 + alpha / a;

    BiquadCoeffs {
        b0: (1.0 + alpha * a) / a0,
        b1: (-2.0 * cos_w0) / a0,
        b2: (1.0 - alpha * a) / a0,
        a1: (-2.0 * cos_w0) / a0,
        a2: (1.0 - alpha / a) / a0,
    }
}

/// Direct Form I — 4 durum değişkeni (referans / yüksek-Q)
#[derive(Default)]
pub struct BiquadDf1 {
    c: BiquadCoeffs,
    x1: f32, x2: f32, y1: f32, y2: f32,
}

impl BiquadDf1 {
    pub fn new(c: BiquadCoeffs) -> Self { Self { c, ..Default::default() } }
    /// Koefisyon güncelle (R16 canlı EQ) — durumu sıfırla, pop sesini azalt
    pub fn set_coeffs(&mut self, c: BiquadCoeffs) {
        self.c = c;
        self.reset();
    }
    pub fn reset(&mut self) {
        self.x1 = 0.0; self.x2 = 0.0; self.y1 = 0.0; self.y2 = 0.0;
    }
    pub fn process(&mut self, x: f32) -> f32 {
        let y = self.c.b0 * x + self.c.b1 * self.x1 + self.c.b2 * self.x2
              - self.c.a1 * self.y1 - self.c.a2 * self.y2;
        self.x2 = self.x1; self.x1 = x;
        self.y2 = self.y1; self.y1 = y;
        y
    }
}

/// Transposed Direct Form II — 2 durum değişkeni (üretim tercihi: 32f'de daha stabil)
#[derive(Default)]
pub struct BiquadDf2t {
    c: BiquadCoeffs,
    z1: f32, z2: f32,
}

impl BiquadDf2t {
    pub fn new(c: BiquadCoeffs) -> Self { Self { c, ..Default::default() } }
    pub fn set_coeffs(&mut self, c: BiquadCoeffs) {
        self.c = c;
        self.z1 = 0.0;
        self.z2 = 0.0;
    }
    pub fn process(&mut self, x: f32) -> f32 {
        let y = self.c.b0 * x + self.z1;
        self.z1 = self.c.b1 * x - self.c.a1 * y + self.z2;
        self.z2 = self.c.b2 * x - self.c.a2 * y;
        y
    }
}
```

**Form karşılaştırması:** DF1 = 4 durum, kütülatif hata yüksek · DF2T = 2 durum, 32f'de
daha stabil (Neva üretim tercihi). İki form aynı `BiquadCoeffs`'i paylaşır — R16 çift formun
(parametrik ↔ grafik) tek motor prensibi.

## §3 Örnek 2 — 32f buffer döngüsü (K2/K4/K5) · (K2 · K4 · K5)

48 kHz varsayılan, interleaved float32 döngüsü, SR fallback enum/`match` ile.

```rust
// neva_buffer.rs — std only
pub const DEFAULT_RATE: u32 = 48_000;   // K4 varsayılan

/// K4 destek listesi — enum + repr(u32): değerler doğrudan Hz
#[derive(Clone, Copy, PartialEq, Eq, Debug)]
#[repr(u32)]
pub enum SampleRate {
    R44100 = 44_100,
    R48000 = 48_000,
    R88200 = 88_200,
    R96000 = 96_000,
    R176400 = 176_400,
    R192000 = 192_000,
}

pub fn is_k4_rate(sr: u32) -> bool {
    match sr {
        44_100 | 48_000 | 88_200 | 96_000 | 176_400 | 192_000 => true,
        _ => false,                                   // K4 dışı — reddet
    }
}

pub fn rate_label(sr: u32) -> &'static str {
    match sr {
        44_100 => "44.1 kHz",
        48_000 => "48 kHz",
        88_200 => "88.2 kHz",
        96_000 => "96 kHz",
        176_400 => "176.4 kHz",
        192_000 => "192 kHz",
        _ => "unsupported",
    }
}

/// SR seçimi: tercih → (K6: SR eşit?) → K5 en yüksek yerel SR → K4 48 kHz
pub fn pick_sample_rate(preferred: u32, device_rates: &[u32]) -> u32 {
    let preferred = if is_k4_rate(preferred) { preferred } else { DEFAULT_RATE };

    if device_rates.contains(&preferred) {
        return preferred;                             // K6 adayı: SR eşit
    }
    // K5 fallback: cihazın en yüksek YEREL SR'si (K4 listesi içinden, zorlama yok)
    device_rates.iter().copied()
        .filter(|&r| is_k4_rate(r))
        .max()
        .unwrap_or(DEFAULT_RATE)
}

/// Interleaved 32f zincir döngüsü — stereo (K1 → K2 → K3, dönüşüm yok)
pub fn process_interleaved(buf: &mut [f32], eq: &mut [BiquadDf2t; 2]) {
    // Destructure: iki münhasır &mut — ödünç (borrow) kuralları ihlal edilmez
    let [left, right] = eq;
    for frame in buf.chunks_mut(2) {
        if frame.len() < 2 { break; }
        frame[0] = left.process(frame[0]);            // K1: ham 32f — upcast yok
        frame[1] = right.process(frame[1]);           // K2: DSP f32 üzerinde
        // K3: cihaz float32 sunarsa yazılan bayt birebir gider (K6 bit-perfect)
    }
}
```

Not (K5): `pick_sample_rate(192_000, &[44_100, 48_000, 96_000])` → `96_000` — cihaz 192
sunmadığı için en yüksek yerel SR seçilir, zincir orada açılır.

## §4 Örnek 3 — Lock-free SPSC ring buffer (R10) · (R10)

Atomik `head`/`tail` (`AtomicUsize`, `Ordering::{Acquire, Release}`) · power-of-2 kapasite ·
`&mut self` deseni (tek-yazıcı/tek-okuyucu sahiplik kurallarıyla güvence) · **`unsafe`
bloğu YOK** — hücreler `AtomicU32` (f32 bit deseni) olduğu için veri yarışı tanım dışı
değildir; init dışında tahsis yok (zero-allocation, R10).

```rust
// neva_spsc.rs — std only, lock-free SPSC, unsafe YOK
use std::sync::atomic::{AtomicU32, AtomicUsize, Ordering};
use std::sync::Arc;

#[derive(Debug)]
pub enum RingError {
    NotPowerOfTwo,
    ZeroCapacity,
}

struct Inner {
    buf: Box<[AtomicU32]>,   // f32 bit deseni — atomik hücre (unsafe gerekmez)
    mask: usize,             // kapasite - 1 (kapasite mutlaka 2^n)
    head: AtomicUsize,       // yazıcı indeksi (monoton artar, taşar)
    tail: AtomicUsize,       // okuyucu indeksi (monoton artar, taşar)
}

/// Kuyruk tutucusu; `split` ile tek seferlik Producer/Consumer'a ayrılır.
pub struct SpscRing {
    inner: Arc<Inner>,
}

pub struct Producer {
    inner: Arc<Inner>,
}

pub struct Consumer {
    inner: Arc<Inner>,
}

impl SpscRing {
    /// Başlatma — NON-RT context (engine init). RT yolunda tahsis YOK.
    pub fn new(capacity_pow2: usize) -> Result<SpscRing, RingError> {
        if capacity_pow2 == 0 {
            return Err(RingError::ZeroCapacity);
        }
        if capacity_pow2 & (capacity_pow2 - 1) != 0 {
            return Err(RingError::NotPowerOfTwo);      // 2^n değil
        }
        let mut buf = Vec::with_capacity(capacity_pow2);
        for _ in 0..capacity_pow2 {
            buf.push(AtomicU32::new(0));
        }
        Ok(SpscRing {
            inner: Arc::new(Inner {
                buf: buf.into_boxed_slice(),
                mask: capacity_pow2 - 1,
                head: AtomicUsize::new(0),
                tail: AtomicUsize::new(0),
            }),
        })
    }

    /// Sahiplik bölmesi: `SpscRing` tüketilir → GERİ DÖNÜŞÜ OLAMAYAN tek Producer +
    /// tek Consumer. SPSC invariant'ı derleyici tarafından zorlanır (ownership).
    pub fn split(self) -> (Producer, Consumer) {
        (Producer { inner: self.inner.clone() },
         Consumer { inner: self.inner })
    }
}

impl Producer {
    /// RT güvenli: lock yok, block yok, tahsis yok. Doluysa false (çağıran politika).
    /// `&mut self` = tek-yazıcı garantisi (iki yazıcı aynı handle'a &mut alamaz).
    pub fn push(&mut self, v: f32) -> bool {
        let head = self.inner.head.load(Ordering::Relaxed);
        let next = head.wrapping_add(1);
        let tail = self.inner.tail.load(Ordering::Acquire);
        if next.wrapping_sub(tail) > self.inner.mask {
            return false;                              // dolu
        }
        // f32 → bit deseni: to_bits/from_bits bit-düz kaydırma, kayıpsız (K2: 32f)
        self.inner.buf[head & self.inner.mask].store(v.to_bits(), Ordering::Relaxed);
        self.inner.head.store(next, Ordering::Release); // release → veri yayınlanır
        true
    }
}

impl Consumer {
    /// RT güvenli. Boşsa None.
    /// `&mut self` = tek-okuyucu garantisi.
    pub fn pop(&mut self) -> Option<f32> {
        let tail = self.inner.tail.load(Ordering::Relaxed);
        if tail == self.inner.head.load(Ordering::Acquire) {
            return None;                               // boş
        }
        let bits = self.inner.buf[tail & self.inner.mask].load(Ordering::Relaxed);
        // acquire (head) ile senkronize edildi → bayt bayt görünür
        self.inner.tail.store(tail.wrapping_add(1), Ordering::Release);
        Some(f32::from_bits(bits))                     // bit deseni → f32 (K2/K3 korunur)
    }
}
```

Neden `unsafe` yok: klasik SPSC (unsafe sürümünde) veri dizisine `&mut`/`&mut` paylaşıp
tanım-dışı davranış yaratırdı; burada hücreler `AtomicU32` olduğu için her erişim atomik
ve dil tarafından tanımlı. `wrap` için `usize` sarmalama `wrapping_*` ile açıkça ifade
edildi (overflow panik yok). `Arc` sayacı yalnız `split`/init'de bir kez çalışır.

## §5 Örnek 4 — WAV 32f okuma/yazma (K3) · (K3 · K1)

44-byte RIFF header · format **3 (IEEE_FLOAT)** · 44100–192000 · 32-bit · 2 kanal.
Yazma: header + `write_all`; okuma: chunk walk ile minimal parse + doğrulama.
Hata yönetimi: `Result<T, E>`.

```rust
// neva_wav.rs — std only (fs/io), little-endian host varsayımı (x86/ARM64 LE)
use std::fs::File;
use std::io::{self, BufReader, Read, Seek, SeekFrom, Write};
use std::path::Path;

pub const WAV_FORMAT_IEEE_FLOAT: u16 = 3;   // WAVE_FORMAT_IEEE_FLOAT (K1/K3)

/// Uygulama hata tipi — `Result<T, NevaError>` deseni (panic YOK)
#[derive(Debug)]
pub enum NevaError {
    Io(io::Error),
    BadFormat(&'static str),        // header/chunk doğrulama hatası
    Unsupported(&'static str),      // SR/bit/kanal destek dışı (K1/K4)
}

impl From<io::Error> for NevaError {
    fn from(e: io::Error) -> Self { NevaError::Io(e) }
}

impl std::fmt::Display for NevaError {
    fn fmt(&self, f: &mut std::fmt::Formatter<'_>) -> std::fmt::Result {
        match self {
            NevaError::Io(e) => write!(f, "I/O error: {e}"),
            NevaError::BadFormat(m) => write!(f, "bad wav format: {m}"),
            NevaError::Unsupported(m) => write!(f, "unsupported: {m}"),
        }
    }
}

fn put_u16_le(v: u16) -> [u8; 2] { v.to_le_bytes() }
fn put_u32_le(v: u32) -> [u8; 4] { v.to_le_bytes() }

/// Yazma: 44-byte standart header + float32 veri (K3: doğrudan 32f — dither DEĞİL;
/// 24-bit int'e düşüş yalnız cihaz 24-bit isterse §6 dither'ı ile yapılır).
///
/// Sınır notu: std fs ile tek seferlik `Vec<u8>` üretimi 32-bit hedefte
/// `isize::MAX` ≈ **2 GiB** sınırına takılır → örnek parça parça `write_all`
/// kullanır; std fs ile >2 GiB tek-dosya üretim önerilmez (parça/blok yazın).
pub fn write_wav_f32(path: &Path, fs: u32, channels: u16, samples: &[f32]) -> Result<(), NevaError> {
    if !(44_100..=192_000).contains(&fs) {
        return Err(NevaError::Unsupported("sample rate 44.1k..192k (K4)"));
    }
    if channels == 0 || channels > 2 {
        return Err(NevaError::Unsupported("channels 1..=2"));
    }
    let data_bytes = (samples.len() * 4) as u32;
    let block_align = channels * (32 / 8);
    let byte_rate = fs * u32::from(channels) * (32 / 8);

    let mut h: Vec<u8> = Vec::with_capacity(44);
    h.extend_from_slice(b"RIFF");
    h.extend_from_slice(&put_u32_le(36 + data_bytes));
    h.extend_from_slice(b"WAVE");
    h.extend_from_slice(b"fmt ");
    h.extend_from_slice(&put_u32_le(16));                       // fmt chunk size
    h.extend_from_slice(&put_u16_le(WAV_FORMAT_IEEE_FLOAT));    // format 3
    h.extend_from_slice(&put_u16_le(channels));
    h.extend_from_slice(&put_u32_le(fs));
    h.extend_from_slice(&put_u32_le(byte_rate));
    h.extend_from_slice(&put_u16_le(block_align));
    h.extend_from_slice(&put_u16_le(32));                       // bits/sample
    h.extend_from_slice(b"data");
    h.extend_from_slice(&put_u32_le(data_bytes));

    let mut f = File::create(path)?;
    f.write_all(&h)?;
    for s in samples {
        f.write_all(&s.to_le_bytes())?;   // safe + std only (zerocopy/bytemuck YOK)
    }
    Ok(())
}

/// Okuma — chunk walk (RIFF genişletmelerine dayanıklı) + doğrulama
#[derive(Debug, Default, Clone, Copy)]
pub struct WavInfo {
    pub fs: u32,
    pub channels: u16,
    pub format: u16,
    pub bits: u16,
    pub data_bytes: u32,
    pub data_pos: u64,
}

fn get_u16_le(b: &[u8]) -> u16 { u16::from_le_bytes([b[0], b[1]]) }
fn get_u32_le(b: &[u8]) -> u32 { u32::from_le_bytes([b[0], b[1], b[2], b[3]]) }

pub fn read_wav_f32(path: &Path) -> Result<(Vec<f32>, WavInfo), NevaError> {
    let mut f = BufReader::new(File::open(path)?);

    let mut hdr = [0u8; 12];
    f.read_exact(&mut hdr)?;
    if &hdr[0..4] != b"RIFF" || &hdr[8..12] != b"WAVE" {
        return Err(NevaError::BadFormat("not RIFF/WAVE"));
    }

    let mut info = WavInfo::default();
    loop {
        let mut ch = [0u8; 8];
        f.read_exact(&mut ch).map_err(|_| NevaError::BadFormat("data chunk missing"))?;
        let size = get_u32_le(&ch[4..8]);
        if &ch[0..4] == b"fmt " {
            if size < 16 {
                return Err(NevaError::BadFormat("fmt chunk < 16"));
            }
            let mut fmt = [0u8; 16];
            f.read_exact(&mut fmt)?;
            info.format = get_u16_le(&fmt[0..2]);
            info.channels = get_u16_le(&fmt[2..4]);
            info.fs = get_u32_le(&fmt[4..8]);
            info.bits = get_u16_le(&fmt[14..16]);
            f.seek(SeekFrom::Current(i64::from(size) - 16))?;
        } else if &ch[0..4] == b"data" {
            info.data_bytes = size;
            info.data_pos = f.stream_position()?;
            break;
        } else {
            // bilinmeyen chunk atla — WAVE chunk'ları word-hizalı (odd size +1 padding)
            f.seek(SeekFrom::Current(i64::from(size) + i64::from(size & 1)))?;
        }
    }

    // Doğrulama — K1/K4
    if info.format != WAV_FORMAT_IEEE_FLOAT {
        return Err(NevaError::BadFormat("format != 3 (IEEE_FLOAT)"));
    }
    if info.bits != 32 {
        return Err(NevaError::BadFormat("bits != 32"));
    }
    if info.channels == 0 || info.channels > 2 {
        return Err(NevaError::BadFormat("channels 1..=2"));
    }
    if !(44_100..=192_000).contains(&info.fs) {
        return Err(NevaError::Unsupported("sample rate 44.1k..192k (K4)"));
    }

    let mut raw = vec![0u8; info.data_bytes as usize];
    f.read_exact(&mut raw)?;
    let samples: Vec<f32> = raw.chunks_exact(4)
        .map(|b| f32::from_le_bytes([b[0], b[1], b[2], b[3]]))
        .collect();
    Ok((samples, info))
}
```

## §6 Örnek 5 — Compressor + brickwall limiter (R10) · (R10 · K3)

Envelope follower (attack 5 ms / release 50 ms, tek kutuplu IIR) → −12 dB · ratio 4:1 →
makeup → 0 dBFS clamp. K3: 24-bit int düşüşünde **TPDF dither**.

```rust
// neva_comp.rs — std only, f32 (K2)
pub struct Compressor {
    env: f32,
    atk: f32,                 // exp(-1 / (attack_s  * fs))
    rel: f32,                 // exp(-1 / (release_s * fs))
    threshold_lin: f32,       // -12 dBFS
    ratio: f32,               // 4:1
    makeup: f32,
    rng: u32,                 // TPDF dither durumu (K3)
}

impl Compressor {
    pub const THRESHOLD_DB: f32 = -12.0;
    pub const RATIO: f32 = 4.0;
    pub const ATTACK_SEC: f32 = 0.005;    // 5 ms
    pub const RELEASE_SEC: f32 = 0.050;   // 50 ms

    pub fn new(fs: u32) -> Self {
        let fs = fs as f32;
        Self {
            env: 0.0,
            atk: (-1.0 / (Self::ATTACK_SEC * fs)).exp(),
            rel: (-1.0 / (Self::RELEASE_SEC * fs)).exp(),
            threshold_lin: 10f32.powf(Self::THRESHOLD_DB / 20.0),
            ratio: Self::RATIO,
            makeup: 10f32.powf((1.0 - 1.0 / Self::RATIO) * (-Self::THRESHOLD_DB) * 0.5 / 20.0),
            rng: 0x1234_5678,
        }
    }

    /// Tek kutuplu IIR envelope: attack'e göre yüksel, release'e göre düş
    fn envelope(&mut self, in_abs: f32) -> f32 {
        let a = if in_abs > self.env { self.atk } else { self.rel };
        self.env = a * self.env + (1.0 - a) * in_abs;
        self.env
    }

    /// Tek örnek: compressor + brickwall limiter (K2 → K3 clamp)
    pub fn process(&mut self, x: f32) -> f32 {
        let level = self.envelope(x.abs());
        let mut gain = self.makeup;
        if level > self.threshold_lin {
            let level_db = 20.0 * (level + 1.0e-12).log10();
            let over = level_db - Self::THRESHOLD_DB;
            let gr_db = -over * (1.0 - 1.0 / self.ratio);
            gain *= 10f32.powf(gr_db / 20.0);
        }
        (x * gain).clamp(-1.0, 1.0)      // brickwall: 0 dBFS clamp (K3)
    }

    /// Blok işleme — interleaved değil, düz kanal (çağıran ayırır)
    pub fn process_block(&mut self, buf: &mut [f32]) {
        for s in buf.iter_mut() {
            *s = self.process(*s);
        }
    }

    /// K3: 24-bit int'e düşüşte TPDF dither — iki uniform [-0.5,+0.5] LSB toplamı
    pub fn tpdf_dither24(&mut self) -> f32 {
        // LCG — yalnız dither jitter'i (kripto DEĞİL)
        // ⚠️ VERIFICATION REQUIRED: üretim RNG seçimi (xoshiro256** vb.)
        self.rng = self.rng.wrapping_mul(1_664_525).wrapping_add(1_013_904_223);
        let u1 = (self.rng >> 8) as f32 / (1u32 << 24) as f32;
        self.rng = self.rng.wrapping_mul(1_664_525).wrapping_add(1_013_904_223);
        let u2 = (self.rng >> 8) as f32 / (1u32 << 24) as f32;
        (u1 - u2) * (1.0 / 8_388_608.0)   // 2^-23 = 1 LSB @24-bit
    }
}
```

K3 zincir notu: cihaz **float32** sunuyorsa (K6 koşulu) dither **uygulanmaz**; yalnız
24-bit int çıkışa dönüşüm noktasında `tpdf_dither24()` çağrılır + round + clamp
(doc 10 §1 K3 — düşüş dönüşümünde dither zorunlu).

## §7 Örnek 6 — OS/cihaz enum otomatik varsayılan (R18) · (R18 · K4 · K5)

Backend enum (ASIO/WASAPI/ALSA/CoreAudio/NONE) + cihaz-bazlı otomatik varsayılan seçim:
önceki seçim kaydı → yoksa en yüksek SR'lı cihaz → varsayılan 48 kHz.

```rust
// neva_device.rs — std only (seçim katmanı — OS API çağrısı YOK)
#[derive(Clone, Copy, PartialEq, Eq, Debug)]
pub enum Backend {
    None,
    Asio,        // Windows — düşük gecikme (R3 <10 ms hedefi)
    Wasapi,      // Windows — exclusive/shared + loopback (K1)
    Alsa,        // Linux   — FLOAT_LE (K1)
    CoreAudio,   // macOS   — AUHAL float32 (K1)
}

impl Backend {
    pub fn name(self) -> &'static str {
        match self {
            Backend::Asio => "ASIO",
            Backend::Wasapi => "WASAPI",
            Backend::Alsa => "ALSA",
            Backend::CoreAudio => "CoreAudio",
            Backend::None => "NONE",
        }
    }
}

/// Compile-time OS seçimi (std macro — runtime sorgu yok)
pub fn detect_backend() -> Backend {
    if cfg!(target_os = "windows") {
        Backend::Wasapi            // ASIO varsa init sırasında tercih edilir (os/01)
    } else if cfg!(target_os = "macos") {
        Backend::CoreAudio
    } else if cfg!(target_os = "linux") {
        Backend::Alsa
    } else {
        Backend::None
    }
}

#[derive(Clone, Copy, Debug)]
pub struct DeviceCaps {
    pub backend: Backend,
    pub max_rate: u32,        // cihazın en yüksek yerel SR'si (K5 kaynağı)
    pub max_channels: u16,
    pub supports_f32: bool,   // 32-bit float I/O (K1/K3)
}

#[derive(Clone, Copy, Debug, Default)]
pub struct DeviceProfile {    // R18 — cihaz hafızası (R5 profil sistemiyle entegre)
    pub has_saved: bool,
    pub saved_backend: Backend,
    pub saved_rate: u32,
}

#[derive(Clone, Copy, Debug)]
pub struct Selection {
    pub backend: Backend,
    pub rate: u32,
    pub from_saved: bool,
}

/// R18: 1) önceki seçim kaydı → 2) en yüksek SR'lı cihaz (K5) → 3) K4 48 kHz
pub fn pick_default(profile: &DeviceProfile, caps: &[DeviceCaps]) -> Selection {
    // 1) önceki seçim — cihaz hâlâ destekliyorsa
    if profile.has_saved {
        if let Some(d) = caps.iter().find(|d| {
            d.backend == profile.saved_backend && profile.saved_rate <= d.max_rate
        }) {
            return Selection { backend: d.backend, rate: profile.saved_rate, from_saved: true };
        }
    }

    // 2) en yüksek SR'lı f32 destekli cihaz (K5 — zorlama yok, yerel en yüksek)
    let best = caps.iter()
        .filter(|d| d.supports_f32 && d.max_rate > 48_000)
        .max_by_key(|d| d.max_rate);
    if let Some(d) = best {
        return Selection { backend: d.backend, rate: d.max_rate, from_saved: false };
    }

    // 3) varsayılan (K4: 48 kHz) — backend yine tespit edilmiş kalır
    Selection { backend: detect_backend(), rate: 48_000, from_saved: false }
}
```

## §8 Dile özel notlar (Rust)

| Başlık | Not |
|---|---|
| **Standart** | Rust **2021 edition** · dil + `std` — `core`/`alloc` ayrımı bu örneklerde yok |
| **Bağımlılık** | **`Cargo.toml` bağımlılığı YOK — yalnız `std`.** `use std::sync::atomic::*`, `std::sync::Arc`, `std::fs`, `std::io` |
| **Derleme (tek dosya)** | `rustc --edition 2021 -O neva_examples.rs -o neva_examples` — Cargo gerekmez |
| **Ownership / `&mut self`** | Biquad/Compressor `process(&mut self)` → durum münhasır; SPSC `split(self)` tek Producer + tek Consumer doğurur (derleyici zorlar); `pop/push(&mut self)` tekillik garantisi |
| **Atomikler** | `AtomicUsize` (head/tail) + `AtomicU32` (hücre) · `Ordering::{Relaxed, Acquire, Release}` — `Acquire` (oku) / `Release` (yaz) eşleşmesi SPSC'de yeterli; `SeqCst` gereksiz yavaş olurdu |
| **`unsafe` YOK** | Hücre atomik olduğu için dil tanımlı davranış korunur; klasik `unsafe` SPSC deseni (iç `&mut` paylaşımı) **kullanılmadı** |
| **Hata yönetimi** | `Result<T, NevaError>` + `?` operator · `enum` ile tip güvenli hata · panic yalnız programlama hatasında (`debug_assert` vb.) |
| **Pitfall — `f32` ↔ `f64`** | `f32::exp/log10/powf` metotları ile kal (`10f32.powf`); `10.0f64` literali sessizce `f64` hesaba düşürür ve K2 zincirini çift hassasiyete kaydırır |
| **Pitfall — denormal** | Rust std'de denormal flush **yok** — envelope `0'a` yaklaşırken denormal üretir (CPU yavaşlar): üretimde `if env < 1e-9 { env = 0.0 }` flush'i eklenir (⚠️ denorm-flag ISA'ya göre doğrulanır) |
| **Pitfall — WAV yazım hızı** | `to_le_bytes` döngüsü güvenli ama byte-by-byte; büyük dosyalar için blok tampon + `write_all` gerekir (zerocopy isteği **`unsafe`/harici crate** demektir → bu örnekte bilinçli yok) |
| **WAV sınırı** | std fs ile tek seferlik `Vec<u8>` üretimi **~2 GiB** (32-bit `isize::MAX`) sınırı → örnek parça parça yazar; `data` chunk `u32` = <4 GiB mutlak tavan |
| **Bit-perfect (K6)** | `f32::to_bits`/`from_bits` bit-düz dönüşüm — `push → pop` arasında örnek kaybı/yuvarlama yok |
| **Sıcak yol yasakları** | RT callback'te `Arc` klonlama/yeni `Vec` (taahhüt: yalnız init), `println!`, `Mutex`/`RwLock` (priority inversion riski — R10 lock-free tercih) |

## §9 Sürüm Geçmişi

| Sürüm | Tarih | Değişiklik |
|---|---|---|
| 1.0.0 | 2026-10-15 | İlk sürüm: 6 örnek (biquad · 32f buffer · SPSC unsafe'siz · WAV Result · comp+limiter · backend enum) |

**Authority:** Master Orchestrator → Embedded Engineer · Derlenmedi/test edilmedi (araştırma MD) ·
commit'siz (orkestratöre ait)
