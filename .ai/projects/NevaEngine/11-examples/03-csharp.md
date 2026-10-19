---
title: "Neva Engine — C# Kod Örnekleri (R12 Masaüstü Kontrol)"
type: reference
category: nevaengine
version: 1.0.0
status: active
authority: "Master Orchestrator → Embedded Engineer"
updated: 2026-10-15
---

# Neva Engine — C# Kod Örnekleri (R12 Masaüstü Kontrol)

> **Durum:** v1.0.0 · derlenmedi/test edilmedi (araştırma MD) · K1-K6 uyumlu

**Zorunlu Bağlantılar:** [[11-cok-dilli-kod-ornekleri]] (index) · [[10-io-format-32bit-192khz-kurallari]] (K1-K6 SSOT) · [[07-mimari-diyagram-kontrol-yuzeyleri]] (R10/R12/R18) · [[06-gereksinimler-ve-farkliliklar]] (R16)

## §1 Bağlam — Neva Engine + C#

Neva Engine, 32-bit float zincir üzerine kurulu bir Windows ses motorudur (DSP çekirdeği C++20/JUCE —
doc 00/03). Bağlayıcı I/O sözleşmesi (doc 10 — SSOT):

| Kural | İçerik |
|---|---|
| K1 | Input **32-bit float** (kayıpsız upcast) |
| K2 | Internal processing **32-bit float** |
| K3 | Output **32-bit float**; 24-bit int'e düşüşte **TPDF dither zorunlu** |
| K4 | SR **44.1 / 48 / 88.2 / 96 / 176.4 / 192 kHz** — varsayılan **48 kHz**, üst sınır 192 |
| K5 | Cihaz 192 sunmuyorsa zincir **cihazın en yüksek yerel SR'sinde** açılır |
| K6 | Bit-perfect = **SR eşit + float32** (aksi halde tek noktada SRC/dither) |

**C#'ın bağlamı (R12):** Windows native masaüstü istemci (WinForms/WPF) → aynı WS/REST JSON
control plane. C# DSP motorunu **yönetir** (komut/profil/ölçüm), gerçek zamanlı örnekleme
C++ çekirdeğindedir — buradaki biquad/compressor örnekleri kontrol-doğrulama ve offline WAV
araçları içindir (aynı algoritma, farklı öncelik).

**Dil standartları (global AGENTS.md):** `#nullable enable` · record DTO · `Span<float>` /
`Memory<float>` (allocation yok) · `async/await` + `ConfigureAwait(false)` (kütüphane/servis
kodunda UI thread'e dönüş gereksiz) · top-level statements + class biçimi karışık.

Top-level statements ile kullanım (Program.cs):

```csharp
// Program.cs — top-level statements (R12 istemci giriş noktası)
using Neva.Examples;

var biquad = new BiquadPeaking();
biquad.SetPeaking(sampleRate: 48_000f, freq: 1000f, q: 0.707f, gainDb: 3f);
Console.WriteLine($"Zincir SR: {NevaFormat.ResolveChainRate(deviceMaxRate: 96_000)} Hz"); // → 96000 (K5)
```

## §2 Örnek 1 — Biquad EQ (R16 çift form · K2)

RBJ (Robert Bristow-Johnson) peaking EQ katsayıları + Direct Form I işleme; 48 kHz referans.
R16: parametrik (frekans/Q/gain) ve grafik (31 bant) EQ **aynı** `BiquadPeaking` motorunu
çağırır — grafik form yalnız bant merkez frekanslarını sabit tutar.

```csharp
using System;

namespace Neva.Examples;

/// <summary>RBJ "peaking EQ" biquad — Direct Form I (K2: internal 32f).</summary>
public sealed class BiquadPeaking
{
    // Direct Form I durum değişkenleri (32-bit float — K2)
    private float _x1, _x2, _y1, _y2;

    // Normalize edilmiş katsayılar (a0'a bölünmüş)
    private float _b0, _b1, _b2, _a1, _a2;

    /// <summary>RBJ Audio EQ Cookbook peaking EQ katsayıları (48k dahil her K4 SR).</summary>
    public void SetPeaking(float sampleRate, float freq, float q, float gainDb)
    {
        float a = MathF.Pow(10f, gainDb / 40f);            // A = 10^(dB/40)
        float w0 = 2f * MathF.PI * freq / sampleRate;      // radyan/ornek
        float cosW0 = MathF.Cos(w0);
        float alpha = MathF.Sin(w0) / (2f * q);            // bandwidth term

        float b0 = 1f + alpha * a;
        float b1 = -2f * cosW0;
        float b2 = 1f - alpha * a;
        float a0 = 1f + alpha / a;
        float a1 = -2f * cosW0;
        float a2 = 1f - alpha / a;

        // a0'a bölme (normalizasyon)
        _b0 = b0 / a0; _b1 = b1 / a0; _b2 = b2 / a0;
        _a1 = a1 / a0; _a2 = a2 / a0;

        // Katsayı değişikliğinde durum sıfırlama — pop önleme (tasarım tercihi)
        _x1 = _x2 = _y1 = _y2 = 0f;
    }

    /// <summary>Direct Form I — sample-by-sample:
    /// y[n] = b0·x[n] + b1·x[n−1] + b2·x[n−2] − a1·y[n−1] − a2·y[n−2]</summary>
    public float ProcessSample(float x)
    {
        float y = _b0 * x + _b1 * _x1 + _b2 * _x2 - _a1 * _y1 - _a2 * _y2;
        _x2 = _x1; _x1 = x;
        _y2 = _y1; _y1 = y;
        return y;
    }

    /// <summary>Buffer işleme — zero-allocation (Span tercihi).</summary>
    public void Process(ReadOnlySpan<float> input, Span<float> output)
    {
        for (int i = 0; i < input.Length; i++)
            output[i] = ProcessSample(input[i]);
    }
}
```

## §3 Örnek 2 — 32f Buffer Döngüsü (K2 · K4 · K5)

48 kHz varsayılan (K4), cihaz 192 sunmuyorsa en yüksek yerel SR (K5), interleaved float32
buffer üzerinde allocation-sız kazanç döngüsü (K2).

```csharp
using System;

namespace Neva.Examples;

public static class NevaFormat
{
    // K4: varsayılan 48 kHz — global sabit tek başına R18'siz kullanılmaz (bkz §7)
    public const int DefaultSampleRate = 48_000;

    // K4: desteklenen SR listesi — üst sınır 192 kHz
    public static readonly int[] SupportedRates =
        { 44_100, 48_000, 88_200, 96_000, 176_400, 192_000 };

    /// <summary>K5: cihaz 192 sunmuyorsa en yüksek yerel SR; 44.1 altı cihaz → 44.1 kHz.</summary>
    public static int ResolveChainRate(int deviceMaxRate)
    {
        int best = 0;
        foreach (int r in SupportedRates)
            if (r <= deviceMaxRate && r > best) best = r;
        return best > 0 ? best : SupportedRates[0]; // 44.1 kHz — en düşük destek
    }

    /// <summary>K6: bit-perfect kapısı — SR eşit + float32.</summary>
    public static bool IsBitPerfect(int sourceRate, int deviceRate) =>
        sourceRate == deviceRate; // formatın float32 olduğu varsayılır (K1-K3)
}

public static class BufferOps
{
    /// <summary>K2: interleaved float32 buffer'a sabit kazanç — allocation yok.</summary>
    public static void ApplyGain(Span<float> buffer, float gain)
    {
        for (int i = 0; i < buffer.Length; i++)
            buffer[i] *= gain;
    }
}
```

## §4 Örnek 3 — SPSC Ring Buffer (R10 · K2)

Tek yazıcı / tek okuyucu lock-free kuyruk: `head` yalnız üretici (audio callback), `tail`
yalnız tüketici (render/servis) yazar. Buffer baştan allocate edilir (RT kuralı).
Alternatif: `Interlocked.Increment` tabanlı indeks de kullanılabilir; burada
`Volatile.Read/Write` tercih edilmiştir.

```csharp
using System;
using System.Threading;

namespace Neva.Examples;

/// <summary>SPSC lock-free ring buffer (R10) — tek yazıcı, tek okuyucu.</summary>
public sealed class SpscRingBuffer
{
    private readonly float[] _buffer; // güç-2 boyut (mask hesabı için)
    private readonly int _mask;
    private int _head; // yalnız yazıcı yazar
    private int _tail; // yalnız okuyucu yazar

    public SpscRingBuffer(int powerOfTwoCapacity)
    {
        if (powerOfTwoCapacity < 2 || (powerOfTwoCapacity & (powerOfTwoCapacity - 1)) != 0)
            throw new ArgumentException("Boyut 2'nin kuvveti olmalı.", nameof(powerOfTwoCapacity));
        _buffer = new float[powerOfTwoCapacity]; // baştan allocate — RT kuralı
        _mask = powerOfTwoCapacity - 1;
    }

    /// <summary>Doluysa false döner (yazıcı çağırır).</summary>
    public bool TryWrite(float value)
    {
        int head = Volatile.Read(ref _head);
        int next = (head + 1) & _mask;
        if (next == Volatile.Read(ref _tail)) return false; // dolu
        _buffer[head] = value;                              // önce veri
        Volatile.Write(ref _head, next);                    // sonra index yayını (happens-after)
        return true;
    }

    /// <summary>Boşsa false döner (okuyucu çağırır).</summary>
    public bool TryRead(out float value)
    {
        int tail = Volatile.Read(ref _tail);
        if (tail == Volatile.Read(ref _head)) { value = 0f; return false; } // boş
        value = _buffer[tail];
        Volatile.Write(ref _tail, (tail + 1) & _mask);
        return true;
    }
}
```

## §5 Örnek 4 — WAV 32f Okuma/Yazma (K3 · K1)

RIFF header + `fmt` chunk `audioFormat = 3` (WAVE_FORMAT_IEEE_FLOAT) + 32 bit; SR K4
listesiyle doğrulanır. `BinaryReader`/`BinaryWriter` little-endian okur/yazar (.NET
`BinaryReader` zaten little-endian'dir).

```csharp
using System;
using System.IO;
using System.Text;

namespace Neva.Examples;

/// <summary>WAV meta bilgisi — record DTO.</summary>
public sealed record WavInfo(int SampleRate, int Channels, long Frames);

public static class Wav32
{
    // K4: izin verilen SR'ler — 192 kHz'e kadar
    private static readonly int[] AllowedRates =
        { 44_100, 48_000, 88_200, 96_000, 176_400, 192_000 };

    /// <summary>RIFF + format 3 (IEEE_FLOAT) 32f okuma (K3/K1).</summary>
    public static (WavInfo Info, float[] Interleaved) Read(string path)
    {
        using var fs = File.OpenRead(path);
        using var br = new BinaryReader(fs);

        if (Encoding.ASCII.GetString(br.ReadBytes(4)) != "RIFF")
            throw new InvalidDataException("RIFF değil");
        br.ReadUInt32(); // RIFF size — güvenilmez yazıcılar için atlanır
        if (Encoding.ASCII.GetString(br.ReadBytes(4)) != "WAVE")
            throw new InvalidDataException("WAVE değil");

        ushort audioFormat = 0, channels = 0, bits = 0;
        uint sampleRate = 0;
        float[] samples = Array.Empty<float>();
        bool fmtSeen = false, dataSeen = false;

        while (fs.Position + 8 <= fs.Length)
        {
            string chunkId = Encoding.ASCII.GetString(br.ReadBytes(4));
            uint chunkSize = br.ReadUInt32();

            if (chunkId == "fmt ")
            {
                audioFormat = br.ReadUInt16(); // 3 = IEEE_FLOAT (K3)
                channels    = br.ReadUInt16();
                sampleRate  = br.ReadUInt32();
                br.ReadUInt32();               // byte rate
                br.ReadUInt16();               // block align
                bits        = br.ReadUInt16(); // 32 beklenir
                if (chunkSize > 16) br.ReadBytes((int)(chunkSize - 16)); // cbSize vb. — atla
                fmtSeen = true;
            }
            else if (chunkId == "data")
            {
                int count = (int)(chunkSize / 4); // 4 bayt/örnek (32f)
                samples = new float[count];
                for (int i = 0; i < count; i++) samples[i] = br.ReadSingle();
                dataSeen = true;
            }
            else
            {
                br.ReadBytes((int)chunkSize); // bilinmeyen chunk — atla
            }
            if ((chunkSize & 1) == 1 && fs.Position < fs.Length) br.ReadByte(); // word-pad
        }

        if (!fmtSeen || !dataSeen) throw new InvalidDataException("fmt/data chunk yok");
        if (audioFormat != 3 || bits != 32)
            throw new InvalidDataException($"Beklenen format 3/32bit, gelen {audioFormat}/{bits}");
        if (Array.IndexOf(AllowedRates, (int)sampleRate) < 0)
            throw new InvalidDataException($"SR {sampleRate} K4 listesinde yok");

        return (new WavInfo((int)sampleRate, channels, samples.Length / channels), samples);
    }

    /// <summary>32f WAV yazma — format 3 header (K3).</summary>
    public static void Write(string path, ReadOnlySpan<float> interleaved, int sampleRate, int channels)
    {
        uint dataSize = (uint)(interleaved.Length * 4);
        using var fs = File.Create(path);
        using var bw = new BinaryWriter(fs);

        bw.Write(Encoding.ASCII.GetBytes("RIFF"));
        bw.Write(36u + dataSize);
        bw.Write(Encoding.ASCII.GetBytes("WAVE"));
        bw.Write(Encoding.ASCII.GetBytes("fmt "));
        bw.Write(16u);                              // fmt chunk boyutu
        bw.Write((ushort)3);                        // WAVE_FORMAT_IEEE_FLOAT (K3)
        bw.Write((ushort)channels);
        bw.Write((uint)sampleRate);
        bw.Write((uint)(sampleRate * channels * 4)); // byte rate
        bw.Write((ushort)(channels * 4));            // block align
        bw.Write((ushort)32);                        // bit depth
        bw.Write(Encoding.ASCII.GetBytes("data"));
        bw.Write(dataSize);
        foreach (float s in interleaved) bw.Write(s);
    }
}
```

## §6 Örnek 5 — Compressor + Brickwall Limiter (R10 · K3)

Peak envelope (tek kutuplu, attack/release) → hard-knee 4:1 ratio → −12 dB eşik →
0 dBFS brickwall clamp. K3: 24-bit int çıkışa düşüşte TPDF dither eklenir (aşağıda
`TpdfDither`); float32 çıkışta dither **yok** (K6 bit-perfect).

```csharp
using System;

namespace Neva.Examples;

/// <summary>Compressor + brickwall limiter (R10 · K3).</summary>
public sealed class CompressorLimiter
{
    private const float ThresholdDb = -12f; // istenen eşik
    private const float Ratio = 4f;         // 4:1

    private float _attackCoef;
    private float _releaseCoef;
    private float _env; // peak envelope (linear)

    public CompressorLimiter(float sampleRate, float attackMs = 5f, float releaseMs = 50f)
    {
        // katsayı = exp(−1 / (τ·sr)) — tek zaman sabitli tek kutuplu envelope
        _attackCoef  = MathF.Exp(-1f / (attackMs  / 1000f * sampleRate));
        _releaseCoef = MathF.Exp(-1f / (releaseMs / 1000f * sampleRate));
    }

    public void Process(Span<float> buffer)
    {
        for (int i = 0; i < buffer.Length; i++)
        {
            float x = buffer[i];
            float abs = MathF.Abs(x);
            _env = abs > _env
                ? _attackCoef  * _env + (1f - _attackCoef)  * abs
                : _releaseCoef * _env + (1f - _releaseCoef) * abs;

            float gain = 1f;
            if (_env > 0f)
            {
                float levelDb = 20f * MathF.Log10(_env);
                float over = levelDb - ThresholdDb;
                if (over > 0f)
                {
                    // hard-knee 4:1: eşiği aşan her dB için 0.25 dB çıkış
                    float outDb = ThresholdDb + over / Ratio;
                    gain = MathF.Pow(10f, (outDb - levelDb) / 20f);
                }
            }

            float y = x * gain;
            // brickwall limiter: 0 dBFS clamp (K3)
            buffer[i] = y > 1f ? 1f : y < -1f ? -1f : y;
        }
    }
}

/// <summary>K3: 24-bit int'e düşüşte TPDF dither (±1 LSB üçgen gürültü).</summary>
public static class TpdfDither
{
    public static void Apply(Span<float> buffer, Random rng)
    {
        const float lsb = 1f / 8_388_608f; // 2^23 — 24-bit LSB
        for (int i = 0; i < buffer.Length; i++)
            buffer[i] += (rng.NextSingle() - rng.NextSingle()) * lsb; // üçgen PDF
    }
}
```

## §7 Örnek 6 — OS/Cihaz Enum Otomatik Varsayılan (R18)

R18: global sabit varsayılan **YOK** — varsayılanlar cihaz yeteneğinden otomatik tespit
edilir. Bu örnek **yalın** ve **Windows-only**'dir (R12 istemci): `Environment.OSVersion` +
enum; gerçek cihaz listesi WASAPI/ASIO API'sinden gelir (OS device-change callback →
capability sorgu → per-device profil).

```csharp
using System;

namespace Neva.Examples;

/// <summary>R18: backend enum — OS/cihaz bazlı otomatik varsayılan.</summary>
public enum NevaBackend
{
    Unknown = 0,
    WasapiShared,
    WasapiExclusive,
    Asio,
}

/// <summary>Cihaz yetenekleri — OS device-change callback'inden gelir (R18).</summary>
public sealed record DeviceCaps(string DeviceId, int MaxSampleRate, int MaxChannels);

/// <summary>Per-device profil DTO (R5) — control plane JSON karşılığı (R12).</summary>
public sealed record DeviceProfileDto(string DeviceId, int SampleRate, int BufferFrames, NevaBackend Backend);

public static class DeviceDefaults
{
    /// <summary>OS varsayılan backend — bu örnekte yalın: yalnız Windows (R12).</summary>
    public static NevaBackend DefaultBackend()
    {
        // Environment.OSVersion — .NET 5+ Windows'ta gerçek OS sürümünü döner.
        // WASAPI Vista+ (major ≥ 6); XP yalnız ASIO (doc 10 §2 — XP modülü ayrı şartname).
        var os = Environment.OSVersion;
        if (os.Platform != PlatformID.Win32NT)
            return NevaBackend.Unknown;
        return os.Version.Major >= 6 ? NevaBackend.WasapiShared : NevaBackend.Asio;
    }

    /// <summary>K4+K5+K6+R18: cihaz yeteneğine göre otomatik varsayılan profil.</summary>
    public static DeviceProfileDto ResolveProfile(DeviceCaps caps)
    {
        int rate = NevaFormat.ResolveChainRate(caps.MaxSampleRate); // K5 fallback
        // NOT: 512 smp yalnız örnektir — gerçek R18'de buffer da cihaz gecikme
        // raporundan seçilir; "global sabit varsayılan" R18'de yasaktır.
        const int bufferFrames = 512;
        return new DeviceProfileDto(caps.DeviceId, rate, bufferFrames, DefaultBackend());
    }
}
```

Yalın kayıt örneği (Windows-only — `Microsoft.Win32.Registry`; .NET Windows hedefinde
yerleşik, [SupportedOSPlatform("windows")] ile işaretlenir; non-Windows hedefte derleme
uyarısı verir):

```csharp
// using Microsoft.Win32;
// var key = Registry.CurrentUser.CreateSubKey(@"Software\NevaEngine\Devices");
// key.SetValue(caps.DeviceId, $"{profile.SampleRate},{profile.BufferFrames},{profile.Backend}");
```

R12 control-plane async notu:

```csharp
// Kütüphane/servis kodunda UI thread'e dönüş gereksiz → ConfigureAwait(false).
// public static async Task<DeviceProfileDto> FetchProfileAsync(
//     HttpClient http, string deviceId, CancellationToken ct) =>
//     await http.GetFromJsonAsync<DeviceProfileDto>(
//         $"/api/devices/{deviceId}", ct).ConfigureAwait(false);
```

## §8 Dile Özel Notlar (C#)

1. `#nullable enable` proje genelinde zorunlu; tüm örnekler nullable-uyumlu (`Array.Empty`
   fallback, `out` parametreleri).
2. DTO'lar `record` (immutable, value equality) — control plane JSON eşlemesi için uygun.
3. DSP buffer'ları `Span<float>`/`ReadOnlySpan<float>` ile taşınır — allocation yok
   (Embedded kalitesi: zero-allocation, doc §16).
4. `async/await`: yalnız I/O (dosya/control plane) tarafında; audio callback **sync**'tir.
   Servis/kütüphane kodu `ConfigureAwait(false)` kullanır.
5. Top-level statements (Program.cs) + class/record ayrımı: giriş ince, mantık tip içinde.
6. `unsafe`/pointer yok — SPSC `Volatile` ile çözülür; pointer yolu yalnız C++ çekirdekte.
7. `MathF` (float-precision math) — double yerine float zincirle tutarlılığı (K2).

## §9 Sürüm Geçmişi

| Sürüm | Tarih | Değişiklik |
|---|---|---|
| 1.0.0 | 2026-10-15 | İlk sürüm: 6 örnek (biquad · 32f buffer · SPSC · WAV 32f · compressor+limiter · OS/cihaz enum) |

**Authority:** Master Orchestrator → Embedded Engineer · Derlenmedi/test edilmedi (araştırma MD) ·
[[11-cok-dilli-kod-ornekleri]] index'ine bağlı · commit'siz (orkestratöre ait)
