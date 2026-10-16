---
title: "Neva Engine — Python Kod Örnekleri (Offline Analiz / Araç Zinciri)"
type: reference
category: nevaengine
version: 1.0.0
status: active
authority: "Master Orchestrator → Embedded Engineer"
updated: 2026-10-15
---

# Neva Engine — Python Kod Örnekleri (Offline Analiz / Araç Zinciri)

> **Durum:** v1.0.0 · derlenmedi/test edilmedi (araştırma MD) · K1-K6 uyumlu

**Zorunlu Bağlantılar:** [[11-cok-dilli-kod-ornekleri]] (index) · [[10-io-format-32bit-192khz-kurallari]] (K1-K6 SSOT) · [[07-mimari-diyagram-kontrol-yuzeyleri]] (R10/R18) · [[06-gereksinimler-ve-farkliliklar]] (R16)

## §1 Bağlam — Neva Engine + Python

Neva Engine'in gerçek zamanlı çekirdeği C++20/JUCE'tadır. **Python'un bağlamı**: offline
analiz ve araç zinciri — WAV dosyası doğrulama, SR/format raporlama, dither/kazanç
mühendisliği, telemetri/ölçüm betikleri. **Python real-time DEĞİLDİR** (GIL — §4 dürüst
not). Bağlayıcı I/O sözleşmesi (doc 10 — SSOT):

| Kural | İçerik |
|---|---|
| K1 | Input **32-bit float** |
| K2 | Internal processing **32-bit float** |
| K3 | Output **32-bit float**; 24-bit int düşüşünde **TPDF dither** |
| K4 | SR **44.1–192 kHz** — varsayılan **48 kHz** |
| K5 | Cihaz 192 sunmuyorsa **en yüksek yerel SR** |
| K6 | Bit-perfect = **SR eşit + float32** |

**Bağımlılık notu (requirements):** tek zorunlu harici paket `numpy>=1.24`
(Python 3.11+ hedefi). Örneklerde `wave` modülü **kullanılmaz** — stdlib `wave` IEEE
float (format 3) **desteklemez**, yalnız PCM int okur/yazar (bilinen stdlib sınırı);
WAV parse `struct` ile manuel yapılır (§5).

**Dil standartları (global AGENTS.md):** strict type hints (`-> None`, `tuple[...]`) ·
f-string · `numpy` float32 dikkati (numpy varsayılanı **float64** → her yerde explicit
`dtype=np.float32` / `np.float32(...)` dönüşümü) · Python 3.11+.

```python
"""Neva Engine offline analiz araçları — araştırma MD, çalıştırılmadı.

Bağımlılık: numpy>=1.24 (Python 3.11+). Real-time değil — analiz/araç zinciri.
"""
from __future__ import annotations

import math
import platform
import struct
import threading

import numpy as np
```

## §2 Örnek 1 — Biquad EQ (R16 çift form · K2)

RBJ peaking EQ katsayıları + Direct Form I; 48 kHz referans (K4). R16: parametrik ve
grafik (31 bant) EQ aynı motordur. Dürüst not: sample-by-sample Python döngüsü
**yavaştır** — offline analiz için yeterli; hız gerekiyorsa C++ çekirdek veya vektörleşmiş
taraf (numpy/scipy `lfilter`, ek bağımlılık bu örnekte yok) kullanılır.

```python
def peaking_coefficients(
    sample_rate: float, freq: float, q: float, gain_db: float
) -> tuple[np.ndarray, np.ndarray]:
    """RBJ peaking EQ katsayıları (b, a) — a0'a bölünmüş, float32 (K2)."""
    a = np.float32(10.0 ** (gain_db / 40.0))          # A = 10^(dB/40)
    w0 = np.float32(2.0 * np.pi * freq / sample_rate)  # radyan/ornek
    cos_w0 = np.float32(np.cos(w0))
    alpha = np.float32(np.sin(w0) / (2.0 * q))         # bandwidth term

    b0 = np.float32(1.0 + alpha * a)
    b1 = np.float32(-2.0 * cos_w0)
    b2 = np.float32(1.0 - alpha * a)
    a0 = np.float32(1.0 + alpha / a)
    a1 = np.float32(-2.0 * cos_w0)
    a2 = np.float32(1.0 - alpha / a)

    b = np.array([b0 / a0, b1 / a0, b2 / a0], dtype=np.float32)
    a = np.array([np.float32(1.0), a1 / a0, a2 / a0], dtype=np.float32)
    return b, a


class BiquadPeaking:
    """Direct Form I (K2: internal 32f) — offline kullanım içindir.

    y[n] = b0·x[n] + b1·x[n−1] + b2·x[n−2] − a1·y[n−1] − a2·y[n−2]
    """

    def __init__(self, sample_rate: float, freq: float, q: float, gain_db: float) -> None:
        self._b, self._a = peaking_coefficients(sample_rate, freq, q, gain_db)
        self._x1 = self._x2 = self._y1 = self._y2 = np.float32(0.0)

    def process_sample(self, x: np.float32) -> np.float32:
        y = (
            self._b[0] * x + self._b[1] * self._x1 + self._b[2] * self._x2
            - self._a[1] * self._y1 - self._a[2] * self._y2
        )
        y32 = np.float32(y)  # numpy karışık tip üretimini float32'e sabitle
        self._x2, self._x1 = self._x1, x
        self._y2, self._y1 = self._y1, y32
        return y32

    def process(self, buf: np.ndarray) -> np.ndarray:
        out = np.empty(buf.shape, dtype=np.float32)
        flat_in, flat_out = buf.ravel(), out.ravel()
        for i in range(flat_in.size):
            flat_out[i] = self.process_sample(np.float32(flat_in[i]))
        return out
```

## §3 Örnek 2 — 32f Buffer Döngüsü (K2 · K4 · K5)

48 kHz varsayılan (K4), cihaz 192 sunmuyorsa en yüksek yerel SR (K5), float32 buffer
üzerinde allocation kontrollü döngü (K2). **Uyarı:** numpy varsayılanı `float64`'tir —
her yerde `dtype=np.float32` açıkça yazılır.

```python
DEFAULT_SAMPLE_RATE: int = 48_000  # K4 varsayılanı
SUPPORTED_RATES: tuple[int, ...] = (44_100, 48_000, 88_200, 96_000, 176_400, 192_000)


def resolve_chain_rate(device_max_rate: int) -> int:
    """K5: cihaz 192 sunmuyorsa en yüksek yerel SR; 44.1 altı cihaz → 44.1 kHz."""
    best = 0
    for r in SUPPORTED_RATES:
        if r <= device_max_rate and r > best:
            best = r
    return best if best > 0 else SUPPORTED_RATES[0]


def is_bit_perfect(source_rate: int, device_rate: int) -> bool:
    """K6: bit-perfect kapısı — SR eşit + float32 (format varsayımı K1-K3)."""
    return source_rate == device_rate


def make_buffer(frames: int, channels: int = 2) -> np.ndarray:
    """K2: interleaved float32 buffer — dtype baştan sabit (float64 sürprizi yok)."""
    return np.zeros(frames * channels, dtype=np.float32)


def apply_gain(buf: np.ndarray, gain: float) -> np.ndarray:
    """K2: float32 kazanç döngüsü — explicit dtype, vektörleşmiş (float32 korunur)."""
    return (buf * np.float32(gain)).astype(np.float32, copy=False)
```

## §4 Örnek 3 — SPSC Ring Buffer (R10 · K2)

**Dürüst uyarı:** Python gerçek zamanlı **değildir** — GIL nedeniyle kilitli kuyruk
yeterlidir; lock-free SPSC'nin anlamı yoktur (GIL zaten tek interpreter kilidi).
`threading.Lock` + `numpy.float32` buffer ile **basit kilitli** versiyon aşağıda:
araç zinciri/analiz (ör. producer betik → consumer rapor) için uygundur. Gerçek
real-time yol C++ çekirdektir (doc 02); Python yalnız test/analiz.

```python
class SpscRing:
    """Basit kilitli ring buffer — ANALİZ/ARAÇ içindir, real-time DEĞİL (GIL)."""

    def __init__(self, capacity: int) -> None:
        if capacity < 2:
            raise ValueError("capacity >= 2 olmalı")
        self._buf = np.zeros(capacity, dtype=np.float32)  # K2: float32
        self._capacity = capacity
        self._head = 0
        self._tail = 0
        self._lock = threading.Lock()  # GIL gerçek zamanlı garanti etmez — kilit şart

    def try_write(self, value: float) -> bool:
        """Doluysa False döner."""
        with self._lock:
            nxt = (self._head + 1) % self._capacity
            if nxt == self._tail:
                return False  # dolu
            self._buf[self._head] = np.float32(value)
            self._head = nxt
            return True

    def try_read(self) -> tuple[np.float32, bool]:
        """Boşsa (0.0, False) döner."""
        with self._lock:
            if self._tail == self._head:
                return np.float32(0.0), False  # boş
            value = np.float32(self._buf[self._tail])
            self._tail = (self._tail + 1) % self._capacity
            return value, True

    @property
    def available(self) -> int:
        with self._lock:
            return (self._head - self._tail) % self._capacity
```

## §5 Örnek 4 — WAV 32f Okuma/Yazma (K3 · K1)

RIFF header + `fmt` chunk `audio_format = 3` (WAVE_FORMAT_IEEE_FLOAT) + 32 bit; SR K4
listesiyle doğrulanır. **Dürüst not:** stdlib `wave` modülü IEEE float (format 3)
**desteklemez** (yalnız PCM int) → parse `struct` ile manuel yapılır; `np.frombuffer`
ile doğrudan `float32` (little-endian `<f4`) dizisine çevrilir.

```python
_ALLOWED_RATES: tuple[int, ...] = (44_100, 48_000, 88_200, 96_000, 176_400, 192_000)  # K4


def read_wav32(path: str) -> tuple[np.ndarray, int, int]:
    """RIFF + format 3 (IEEE_FLOAT) 32f okuma (K3/K1) — struct ile manuel parse.

    Dönüş: (interleaved float32 samples, sample_rate, channels).
    """
    with open(path, "rb") as f:
        raw = f.read()

    if len(raw) < 44 or raw[0:4] != b"RIFF" or raw[8:12] != b"WAVE":
        raise ValueError("RIFF/WAVE değil")

    pos = 12
    channels = 0
    sample_rate = 0
    samples: np.ndarray | None = None

    while pos + 8 <= len(raw):
        chunk_id = raw[pos : pos + 4]
        (chunk_size,) = struct.unpack_from("<I", raw, pos + 4)
        body = pos + 8

        if chunk_id == b"fmt ":
            audio_format, channels, sample_rate, _byte_rate, _block_align, bits = (
                struct.unpack_from("<HHIIHH", raw, body)
            )
            if audio_format != 3 or bits != 32:
                raise ValueError(
                    f"beklenen format 3/32bit, gelen {audio_format}/{bits}"
                )
        elif chunk_id == b"data":
            count = chunk_size // 4  # 4 bayt/örnek (32f)
            samples = np.frombuffer(raw, dtype="<f4", count=count, offset=body).copy()
            # .copy() zorunlu: frombuffer çıktısı read-only'dir (numpy2.x dtype/deprecation yok)

        # tüm chunk'lar word-aligned: tek baytlık pad atla (RIFF kuralı)
        pos = body + chunk_size + (chunk_size % 2)

    if samples is None:
        raise ValueError("data chunk yok")
    if sample_rate not in _ALLOWED_RATES:
        raise ValueError(f"SR {sample_rate} K4 listesinde yok")
    return samples, sample_rate, channels


def write_wav32(path: str, samples: np.ndarray, sample_rate: int, channels: int) -> None:
    """32f WAV yazma — format 3 header (K3). np.float32 → little-endian <f4."""
    if samples.dtype != np.float32:
        samples = samples.astype(np.float32)  # K3: 32f sözleşmesi
    data = samples.astype("<f4", copy=False).tobytes()  # little-endian 4B/örnek
    data_size = len(data)

    header = struct.pack(
        "<4sI4s4sIHHIIHH4sI",
        b"RIFF",
        36 + data_size,        # RIFF size = (WAVE + fmt + data başlıkları) + data
        b"WAVE",
        b"fmt ",
        16,                    # fmt chunk boyutu (PCM/float için 16)
        3,                     # WAVE_FORMAT_IEEE_FLOAT (K3)
        channels,
        sample_rate,
        sample_rate * channels * 4,  # byte rate
        channels * 4,                # block align
        32,                          # bit depth
        b"data",
        data_size,
    )

    with open(path, "wb") as f:
        f.write(header)
        f.write(data)
```

## §6 Örnek 5 — Compressor + Brickwall Limiter (R10 · K3)

Peak envelope (tek kutuplu, attack/release) → hard-knee 4:1 → −12 dB eşik → 0 dBFS
brickwall clamp. K3: 24-bit int düşüşünde TPDF dither (`tpdf_dither24`) eklenir;
float32 çıkışta dither **yok** (K6 bit-perfect).

```python
class CompressorLimiter:
    """R10: envelope + brickwall limiter (K3) — offline analiz, real-time değil."""

    THRESHOLD_DB: float = -12.0
    RATIO: float = 4.0  # 4:1

    def __init__(self, sample_rate: float, attack_ms: float = 5.0, release_ms: float = 50.0) -> None:
        # katsayı = exp(−1 / (τ·sr)) — tek zaman sabitli tek kutuplu envelope
        self._attack = math.exp(-1.0 / (attack_ms / 1000.0 * sample_rate))
        self._release = math.exp(-1.0 / (release_ms / 1000.0 * sample_rate))
        self._env: float = 0.0

    def process(self, buf: np.ndarray) -> np.ndarray:
        out = np.empty(buf.shape, dtype=np.float32)
        flat_in, flat_out = buf.ravel(), out.ravel()

        for i in range(flat_in.size):
            x = float(flat_in[i])
            abs_x = abs(x)
            if abs_x > self._env:
                self._env = self._attack * self._env + (1.0 - self._attack) * abs_x
            else:
                self._env = self._release * self._env + (1.0 - self._release) * abs_x

            gain = 1.0
            if self._env > 0.0:
                level_db = 20.0 * math.log10(self._env)
                over = level_db - self.THRESHOLD_DB
                if over > 0.0:
                    # hard-knee 4:1: eşiği aşan her dB için 0.25 dB çıkış
                    out_db = self.THRESHOLD_DB + over / self.RATIO
                    gain = 10.0 ** ((out_db - level_db) / 20.0)

            y = x * gain
            flat_out[i] = np.float32(min(1.0, max(-1.0, y)))  # 0 dBFS clamp (K3)

        return out


def tpdf_dither24(buf: np.ndarray, rng: np.random.Generator) -> np.ndarray:
    """K3: 24-bit int düşüşünde TPDF dither — ±1 LSB üçgen gürültü (float32)."""
    lsb = np.float32(1.0 / 8_388_608.0)  # 2^23 — 24-bit LSB
    noise = (
        rng.random(buf.shape, dtype=np.float32) - rng.random(buf.shape, dtype=np.float32)
    ) * lsb  # iki bağımsız uniform → üçgen PDF
    return (buf + noise).astype(np.float32, copy=False)
```

## §7 Örnek 6 — OS/Cihaz Enum Otomatik Varsayılan (R18)

R18: global sabit varsayılan **YOK** — varsayılanlar cihaz yeteneğinden otomatik
tespit edilir. `platform.system()` switch OS varsayılan backend'ini verir; cihaz bazlı
profil OS API'sinden gelen `DeviceCaps` ile çözülür (Python tarafı raporlama/araçtır —
gerçek cihaz sorgusu C++ çekirdektedir).

```python
from dataclasses import dataclass


@dataclass(frozen=True, slots=True)
class DeviceCaps:
    """Cihaz yetenekleri — OS device-change sorgusundan gelir (R18)."""

    device_id: str
    max_sample_rate: int
    max_channels: int


@dataclass(frozen=True, slots=True)
class DeviceProfile:
    """Per-device profil DTO (R5) — rapor/analiz karşılığı."""

    device_id: str
    sample_rate: int
    buffer_frames: int
    backend: str


# OS varsayılan backend adları (dizin — enum yerine, Python idiomu)
BACKEND_BY_OS: dict[str, str] = {
    "Windows": "wasapi",
    "Darwin": "coreaudio",
    "Linux": "alsa",  # PipeWire ALSA API üzerinden
}


def default_backend(system: str | None = None) -> str:
    """R18 — OS varsayılan backend (platform.system())."""
    return BACKEND_BY_OS.get(system if system is not None else platform.system(), "unknown")


def resolve_profile(caps: DeviceCaps) -> DeviceProfile:
    """K4+K5+R18: cihaz yeteneğine göre otomatik varsayılan profil.

    NOT: buffer_frames gerçek R18'de cihaz gecikme raporundan seçilir —
    512 yalnız örnektir; "global sabit varsayılan" R18'de yasaktır.
    """
    return DeviceProfile(
        device_id=caps.device_id,
        sample_rate=resolve_chain_rate(caps.max_sample_rate),  # K5 fallback
        buffer_frames=512,
        backend=default_backend(),
    )
```

## §8 Dile Özel Notlar (Python)

1. **Bağımlılık:** `numpy>=1.24` (tek harici paket); Python **3.11+** (hedef). stdlib
   `wave` **bilinçli kullanılmadı** — IEEE float (format 3) desteklemiyor; `struct`
   manuel parse doğru yol.
2. **float32 disiplini:** numpy varsayılanı `float64` — her buffer/katsayı/gürültü
   üretiminde explicit `dtype=np.float32` / `np.float32(...)`; `np.float32(0.0)` değil
   örnek float32 kalır (K1-K3).
3. **Real-time yok:** GIL nedeniyle lock-free iddiası yapılmaz (§4 dürüst not); Python
   yalnız offline analiz/araç — realtime yol C++20 çekirdek (doc 02).
4. **Type hints:** tüm fonksiyonlarda `-> ` dönüş + parametre hint'i; `from __future__
   import annotations` ile 3.11+ sözdizimi (`tuple[int, ...]`, `X | None`) uyumu.
5. **f-string** hata mesajlarında (`f"SR {sample_rate}..."`) — format() yok.
6. **`slots=True` dataclass:** DTO'lar immutable + hafif (frozen — profil değiştirilemez).
7. **RNG:** `np.random.default_rng()` — `np.random` global state yerine kontrol edilebilir
   tohum (dither tekrarlanabilirliği için).

## §9 Sürüm Geçmişi

| Sürüm | Tarih | Değişiklik |
|---|---|---|
| 1.0.0 | 2026-10-15 | İlk sürüm: 6 örnek (biquad · 32f buffer · kilitli ring · WAV 32f struct · compressor+limiter · platform enum) |

**Authority:** Master Orchestrator → Embedded Engineer · Derlenmedi/test edilmedi (araştırma MD) ·
[[11-cok-dilli-kod-ornekleri]] index'ine bağlı · commit'siz (orkestratöre ait)
