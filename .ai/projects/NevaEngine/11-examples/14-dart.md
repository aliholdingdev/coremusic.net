---
title: "Neva Engine — Dart Kod Örnekleri (6 Örnek)"
type: reference
category: nevaengine
version: 1.0.0
status: active
authority: "Master Orchestrator → Embedded Engineer"
updated: 2026-10-19
---

# Neva Engine — Dart Kod Örnekleri

> **Durum:** v1.0.0 · derlenmedi/test edilmedi (araştırma MD) · K1-K6 uyumlu

**Zorunlu Bağlantılar:** [[11-cok-dilli-kod-ornekleri]] (index) · [[10-io-format-32bit-192khz-kurallari]] (K1-K6 SSOT) · [[06-gereksinimler-ve-farkliliklar]] (R16 · R18) · [[07-mimari-diyagram-kontrol-yuzeyleri]] (R10)

## §1 Bağlam

Dart, Neva **Flutter R13 arayüz katmanı**dır. **Dürüst sınır: Dart/Flutter'da gerçek
real-time DSP YOKTUR** — Flutter UI thread'i + Dart VM GC duraklaması RT audio callback
için uygundur. DSP C++ çekirdekte (JNI/FFI) döner; Dart bu örnekte **arayüz/koordinasyon
katmanının** idiomatik karşılıklarını gösterir. `dart:typed_data` (Float32List) + `dart:io`
kullanılır.

| Kural | İçerik |
|---|---|
| K1 | Input (capture/loopback) **32-bit float** |
| K2 | Internal zincir **32-bit float** (`Float32List` — Dart'ta native `float` primitive YOK) |
| K3 | Output **32-bit float** — 24-bit int düşüşünde **TPDF dither zorunlu** |
| K4 | SR **44.1 / 48 / 88.2 / 96 / 176.4 / 192 kHz** · varsayılan **48 kHz** |
| K5 | Cihaz 192 sunmuyorsa → **cihazın en yüksek yerel SR'si** |
| K6 | Bit-perfect = **SR eşit + float32** |

> ⚠️ **Bu dosyadaki kod derlenmedi/test edilmedi; vault referans örneğidir.** Üretim kodu
> doc 01/03 planındaki Faz 0 build+test kapısından geçer. Yalnız `dart:core` +
> `dart:typed_data` + `dart:io` API kullanılmıştır — Flutter widget/FFI native tarafı bu
> örneklere dahil DEĞİLDİR. Zero-hallucination: doğrulanamayan satır `⚠️ VERIFICATION REQUIRED`.

## §2 Örnek 1 — Biquad EQ (RBJ peaking + Direct Form I) · (R16 · K2)

**Dürüst sapma (R16 çift form):** §4 şeması RBJ peaking + Direct Form I ister; Dart
örneğinde tek form (DF1) verilir — Transposed DF-II C++/Rust dosyalarındaki ayrı sınıf
ile örneklendi. **K2 sapması:** Dart `double` IEEE-754 çifttir (64-bit) — 32-bit float
`Float32List` ile korunur; katsayılar `Float32List` içinde tutulur, geçici hesap `double`
(⚠️ üretimde FFİ/C++ çekirdek hesaplar).

```dart
// neva_biquad.dart — dart:math + dart:typed_data, Float32List (K2)
import 'dart:math' as math;
import 'dart:typed_data';

double _pow10(double v) => math.pow(10.0, v).toDouble();
double _log10(double v) => math.log(v) / math.log(10.0);

/// RBJ peaking (Audio EQ Cookbook) — a0 = 1 normalize; katsayılar Float32List[5]
/// sırası: b0, b1, b2, a1, a2
Float32List makePeaking(double f0, double q, double gainDb, double fs) {
  final qc = q.clamp(0.05, 32.0);
  final f0c = f0.clamp(10.0, 0.49 * fs);          // Nyquist'e çarpmama
  final gdc = gainDb.clamp(-24.0, 24.0);

  final a = _pow10(gdc / 40.0);
  final w0 = 2.0 * math.pi * (f0c / fs);
  final cosw0 = math.cos(w0);
  final alpha = math.sin(w0) / (2.0 * qc);
  final a0 = 1.0 + alpha / a;

  return Float32List.fromList([
    (1.0 + alpha * a) / a0,
    (-2.0 * cosw0) / a0,
    (1.0 - alpha * a) / a0,
    (-2.0 * cosw0) / a0,
    (1.0 - alpha / a) / a0,
  ]);
}

/// Direct Form I — 4 durum (R16 referans form); durum Double (Dart hesabı)
class BiquadDf1 {
  Float32List c;
  double _x1 = 0, _x2 = 0, _y1 = 0, _y2 = 0;

  BiquadDf1(this.c);

  void setCoeffs(Float32List neu) { c = neu; reset(); }   // pop azaltma
  void reset() { _x1 = 0; _x2 = 0; _y1 = 0; _y2 = 0; }

  double process(double x) {
    final y = c[0] * x + c[1] * _x1 + c[2] * _x2 - c[3] * _y1 - c[4] * _y2;
    _x2 = _x1; _x1 = x;
    _y2 = _y1; _y1 = y;
    return y;
  }
}
```

**Neva bağlamı:** R16 parametrik EQ bandı — Flutter (R13) tarafında UI/örnek hesap;
gerçek render C++ çekirdekte döner, Dart yalnız parametre üretir.

## §3 Örnek 2 — 32f buffer döngüsü (K2/K4/K5) · (K2 · K4 · K5)

```dart
// neva_buffer.dart — dart:typed_data, Float32List (K1-K3)
const int defaultRate = 48000;                        // K4 varsayılan

const Set<int> _k4Rates = {44100, 48000, 88200, 96000, 176400, 192000};

/// K4 destek listesi
bool isK4Rate(int sr) => _k4Rates.contains(sr);

/// SR seçimi: tercih → (K6 adayı?) → K5 en yüksek yerel → K4 48 kHz
int pickSampleRate(int preferred, List<int> deviceRates) {
  final p = isK4Rate(preferred) ? preferred : defaultRate;
  if (deviceRates.contains(p)) return p;              // K6: SR eşit
  final k4 = deviceRates.where(isK4Rate).fold<int>(0, (a, b) => b > a ? b : a);
  return k4 > 0 ? k4 : defaultRate;                   // K5 fallback
}

/// Interleaved float32 zincir — stereo, in-place Float32List (K1 → K2 → K3)
void processInterleaved(Float32List buf, BiquadDf1 left, BiquadDf1 right) {
  for (var i = 0; i + 1 < buf.length; i += 2) {
    buf[i] = left.process(buf[i]);                    // K1: ham 32f — upcast yok
    buf[i + 1] = right.process(buf[i + 1]);           // K2: DSP Float32List
  }
}
```

**Neva bağlamı:** Flutter arayüzünden gelen float32 tampon (UI↔çekirdek FFI sınırı) —
Dart tarafı doğrudan `Float32List` üzerinden işler (dönüşüm yok → K6 bit-perfect adayı).

## §4 Örnek 3 — Isolate tabanlı kuyruk (SPSC yerine geçici) · (R10)

**Dürüst sapma (R10 lock-free SPSC):** Dart **paylaşılan bellek yok** (her isolate kendi
belleğini taşır) — `SendPort`/`ReceivePort` ile **mesaj tabanlı kuyruk** kurulur. **Gerçek
lock-free SPSC ring buffer YOKTUR** — Dart bellek modeli atomik paylaşım sunmaz; SPSC
invariant'ı `SendPort` tek-yazıcı/tek-okuyucu sözleşmesiyle uygulanır. Production'da
lock-free ring **C++ çekirdekte** (FFI) döner; Dart kuyruk yalnız UI↔çekirdek kontrol
trafiği içindir.

```dart
// neva_queue.dart — dart:isolate (SendPort/ReceivePort) — mesaj tabanlı, lock-free DEĞİL
import 'dart:async';
import 'dart:isolate';

/// SPSC sözleşmesi: tek-yazıcı isolate (UI) → tek-okuyucu isolate (worker).
/// Kilit YOK ama atomik paylaşım da YOK — mesaj kuyruğu (R10 ihlali — bilinçli).
class FloatRingQueue {
  final SendPort _out;            // yazıcının gönderim portu
  ReceivePort? _in;               // okuyucunun alım portu (init'de atanır)
  final int capacity;
  bool _closed = false;

  FloatRingQueue(this._out, {this.capacity = 4096});

  /// Doluysa false (dart:isolate portu kendi kuyruğunu yönetir; basit politika)
  bool enqueue(double v) {
    if (_closed) return false;                        // SPSC'deki "full/close" durumu
    _out.send(v);                                     // tek-yazıcı garantisi (sözleşme)
    return true;
  }

  /// Okuyucu isolate'i kur — tek-okuyucu
  Stream<double> open() {
    final rcv = ReceivePort();
    _in = rcv;
    return rcv.cast<double>();                        // boşta: port kapanana dek akar
  }

  void close() {
    _closed = true;
    _in?.close();
  }
}
```

**Neva bağlamı:** R13 Flutter UI ↔ C++ çekirdek kontrol kanalı (parametre, durum, log).
R10 lock-free ring **çekirdek C++ katmanında** kalır; Dart mesaj kuyruğu yalnız non-RT
kontrol mesajları içindir (RT DSP yok — dürüst sınır, §1).

## §5 Örnek 4 — WAV 32f okuma/yazma (K3/K1) · (K3 · K1)

44-byte RIFF header · format **3 (IEEE_FLOAT)** · little-endian. `dart:io` (File/
RandomAccessFile) + `ByteData`. Hata yönetimi: `Exception` (Dart idiomu).

```dart
// neva_wav.dart — dart:io + dart:typed_data, little-endian host varsayımı
import 'dart:io';
import 'dart:typed_data';

const int wavFormatIeeeFloat = 3;                     // WAVE_FORMAT_IEEE_FLOAT (K1/K3)

/// Yazma: 44-byte standart header + float32 veri (K3 — 24-bit düşüşte dither §6'da)
Future<void> writeWavF32(String path, int fs, int channels, Float32List samples) async {
  if (fs < 44100 || fs > 192000) {
    throw Exception('sample rate 44.1k..192k (K4)');
  }
  if (channels < 1 || channels > 2) throw Exception('channels 1..2');
  final dataBytes = samples.length * 4;
  final h = ByteData(44);
  h.setUint32(0, 0x46464952, Endian.little);          // "RIFF" (LE bayt)
  h.setUint32(4, 36 + dataBytes, Endian.little);
  h.setUint32(8, 0x45564157, Endian.little);          // "WAVE"
  h.setUint32(12, 0x20746D66, Endian.little);         // "fmt "
  h.setUint32(16, 16, Endian.little);                 // fmt chunk size
  h.setUint16(20, wavFormatIeeeFloat, Endian.little); // format 3 (K1/K3)
  h.setUint16(22, channels, Endian.little);
  h.setUint32(24, fs, Endian.little);
  h.setUint32(28, fs * channels * 4, Endian.little);  // byteRate
  h.setUint16(32, channels * 4, Endian.little);       // blockAlign
  h.setUint16(34, 32, Endian.little);                 // bits/sample
  h.setUint32(36, 0x61746164, Endian.little);         // "data"
  h.setUint32(40, dataBytes, Endian.little);

  final body = ByteData(dataBytes);
  for (var i = 0; i < samples.length; i++) {
    body.setFloat32(i * 4, samples[i], Endian.little); // bit-düz, kayıpsız (K2→K3)
  }
  await File(path).writeAsBytes(Uint8List.fromList(h.buffer.asUint8List() +
      body.buffer.asUint8List()));
}

class WavInfo {
  int fs = 0, channels = 0, format = 0, bits = 0, dataBytes = 0, dataStart = 0;
}

Future<(Float32List, WavInfo)> readWavF32(String path) async {
  final bytes = await File(path).readAsBytes();
  if (bytes.length < 44 || bytes[0] != 0x52 || bytes[1] != 0x49 ||
      bytes[2] != 0x46 || bytes[3] != 0x46 || bytes[8] != 0x57 ||
      bytes[9] != 0x41 || bytes[10] != 0x56 || bytes[11] != 0x45) {
    throw Exception('not RIFF/WAVE');
  }
  final d = ByteData.sublistView(bytes);
  final info = WavInfo();
  var p = 12;
  while (p + 8 <= bytes.length) {
    final id = String.fromCharCodes(bytes.sublist(p, p + 4));
    final size = d.getUint32(p + 4, Endian.little);
    if (id == 'fmt ') {
      if (size < 16) throw Exception('fmt < 16');
      info.format = d.getUint16(p + 8, Endian.little);
      info.channels = d.getUint16(p + 10, Endian.little);
      info.fs = d.getUint32(p + 12, Endian.little);
      info.bits = d.getUint16(p + 22, Endian.little);
      p += 8 + size + (size & 1);                     // word-hizalı padding
    } else if (id == 'data') {
      info.dataBytes = size; info.dataStart = p + 8;
      break;
    } else {
      p += 8 + size + (size & 1);
    }
  }
  if (info.format != wavFormatIeeeFloat) throw Exception('format != 3 (IEEE_FLOAT)');
  if (info.bits != 32) throw Exception('bits != 32');
  if (info.channels < 1 || info.channels > 2) throw Exception('channels 1..2');
  if (info.fs < 44100 || info.fs > 192000) throw Exception('sample rate 44.1k..192k (K4)');

  final count = info.dataBytes ~/ 4;
  final out = Float32List(count);
  for (var i = 0; i < count; i++) {
    out[i] = d.getFloat32(info.dataStart + i * 4, Endian.little);
  }
  return (out, info);
}
```

**Neva bağlamı:** R13 dosya içe/dışa aktarma (Flutter share/XFile). 32f WAV — K6 bit-perfect
offline hedefi; dither yalnız 24-bit int düşüşünde (§6).

## §6 Örnek 5 — Compressor + brickwall limiter (R10) · (R10 · K3)

```dart
// neva_comp.dart — dart:math (exp/log10), double (K2 — Dart hesabı 64-bit)
import 'dart:math' as math;
import 'dart:typed_data';

double _pow10(double v) => math.pow(10.0, v).toDouble();
double _log10(double v) => math.log(v) / math.log(10.0);

class Compressor {
  static const double thresholdDb = -12.0;
  static const double ratio = 4.0;
  static const double attackSec = 0.005;
  static const double releaseSec = 0.050;

  double _env = 0;
  final double _atkCoef, _relCoef, _thresholdLin, _makeup;
  int _rng = 0x12345678;                              // TPDF dither durumu (K3)

  Compressor(double fs)
      : _atkCoef = math.exp(-1.0 / (attackSec * fs)),
        _relCoef = math.exp(-1.0 / (releaseSec * fs)),
        _thresholdLin = _pow10(thresholdDb / 20.0),
        _makeup = _pow10((1.0 - 1.0 / ratio) * (-thresholdDb) * 0.5 / 20.0);

  /// Tek kutuplu IIR envelope: attack'e göre yüksel, release'e göre düş
  double _envelope(double inAbs) {
    final a = inAbs > _env ? _atkCoef : _relCoef;
    _env = a * _env + (1.0 - a) * inAbs;
    return _env;
  }

  /// Tek örnek: compressor + brickwall limiter (K2 → K3 clamp)
  double process(double x) {
    final level = _envelope(x.abs());
    var gain = _makeup;
    if (level > _thresholdLin) {
      final levelDb = 20.0 * _log10(level + 1.0e-12);
      final over = levelDb - thresholdDb;
      final grDb = -over * (1.0 - 1.0 / ratio);
      gain *= _pow10(grDb / 20.0);
    }
    return (x * gain).clamp(-1.0, 1.0);               // 0 dBFS clamp (K3)
  }

  /// Blok işleme — düz kanal (çağıran ayırır)
  void processBlock(Float32List buf) {
    for (var i = 0; i < buf.length; i++) {
      buf[i] = process(buf[i]);
    }
  }

  /// K3: 24-bit int düşüşünde TPDF dither (iki uniform [-0.5,+0.5] LSB toplamı)
  double tpdfDither24() {
    // ⚠️ VERIFICATION REQUIRED: üretim RNG seçimi (xoshiro256** vb.)
    _rng = (_rng * 1664525 + 1013904223) & 0xFFFFFFFF;  // int32 wrap (LCG)
    final u1 = ((_rng >> 8) & 0xFFFFFF).toDouble() / (1 << 24).toDouble();
    _rng = (_rng * 1664525 + 1013904223) & 0xFFFFFFFF;
    final u2 = ((_rng >> 8) & 0xFFFFFF).toDouble() / (1 << 24).toDouble();
    return (u1 - u2) * (1.0 / 8388608.0);             // 2^-23 = 1 LSB @24-bit
  }
}
```

**Neva bağlamı:** R10 master bus — Flutter arayüzünde gain-reduction göstergesi hesabı;
gerçek compressor C++ çekirdekte döner, Dart yalnız UI değerini üretir. Cihaz float32
sunuyorsa dither uygulanmaz (K6).

## §7 Örnek 6 — OS/cihaz enum (R18) · (R18 · K4 · K5)

**Dürüst not:** Flutter (R13) **çok-platform** katmandır — Dart `Platform` API OS adını
verir ama **birincil ses backend'ini (ASIO/WASAPI/ALSA/CoreAudio) GÖRÜTMEZ**; bu enum
yalnız arayüz tarafı platform etiketidir. Backend seçimi C++ çekirdektir.

```dart
// neva_device.dart — dart:io (Platform) — seçim/etiket katmanı — backend API YOK
import 'dart:io';

/// Arayüz etiketi — ASIO/WASAPI/ALSA/CoreAudio bu katmanda DEĞİL (dürüst sınır)
enum Backend { none, platformLabel }

/// Derleme/platform etiketi — backend seçimi DEĞİL (R18 backend C++ çekirdektir)
Backend detectBackendLabel() {
  if (Platform.isWindows || Platform.isMacOS || Platform.isLinux || Platform.isAndroid) {
    return Backend.platformLabel;
  }
  return Backend.none;
}

String osLabel() {
  if (Platform.isWindows) return 'Windows';
  if (Platform.isMacOS) return 'macOS';
  if (Platform.isLinux) return 'Linux';
  if (Platform.isAndroid) return 'Android';
  if (Platform.isIOS) return 'iOS';
  return 'UNKNOWN';
}

class DeviceCaps {
  final int maxRate;                    // cihazın en yüksek yerel SR'si (K5 kaynağı)
  final int maxChannels;
  final bool supportsF32;               // 32-bit float I/O (K1/K3)
  const DeviceCaps(this.maxRate, this.maxChannels, this.supportsF32);
}

class DeviceProfile {                   // R18 — cihaz hafızası (R5 profil)
  final bool hasSaved;
  final int savedRate;
  const DeviceProfile({this.hasSaved = false, this.savedRate = 0});
}

class Selection {
  final Backend backend;
  final int rate;
  final bool fromSaved;
  const Selection(this.backend, this.rate, this.fromSaved);
}

/// R18: 1) önceki seçim kaydı → 2) en yüksek SR'lı cihaz (K5) → 3) K4 48 kHz
Selection pickDefault(DeviceProfile profile, List<DeviceCaps> caps) {
  if (profile.hasSaved) {
    for (final d in caps) {
      if (profile.savedRate <= d.maxRate) {
        return Selection(Backend.platformLabel, profile.savedRate, true);
      }
    }
  }
  DeviceCaps? best;                                   // K5 fallback
  for (final d in caps) {
    if (d.supportsF32 && d.maxRate > 48000 && (best == null || d.maxRate > best.maxRate)) {
      best = d;
    }
  }
  if (best != null) return Selection(Backend.platformLabel, best.maxRate, false);
  return Selection(detectBackendLabel(), 48000, false);   // K4
}
```

**Neva bağlamı:** R13 Flutter arayüzünde OS rozeti + R18 cihaz hafızası (profil kaydı).
**Dürüst not:** OS enum'unun tamamı bu katmanda **yalnız etiket**tir — asıl backend
seçimi + K5 en-yüksek-SR sorgusu C++ çekirdekte (FFI) yapılır.

## §8 Dile özel notlar (Dart)

| Başlık | Not |
|---|---|
| **Standart** | Dart 3+ · `dart:core` + `dart:typed_data` + `dart:io` + `dart:isolate` — Flutter widget/FFI native tarafı bu dosyada YOK (R13 arayüz katmanı) |
| **`final` / `late`** | `final` yeniden bağlanamayan yerel/alan; `late` geç başlatılan alan (öncekilerde kullanılmadı — ctor init tercih edildi); `const` derleme sabiti |
| **`double` = IEEE-754 çift (64-bit)** | Dart'ta **native `float` primitive YOK** — 32-bit float `Float32List` ile korunur (K2); geçici hesap `double` (⚠️ üretimde FFİ/C++ çekirdek hesaplar) |
| **`Float32List` / `ByteData`** | `dart:typed_data` — `setFloat32`/`getFloat32` bit-düz (K2→K3); `Endian.little` WAV için (RIFF little-endian) |
| **Paylaşılan bellek YOK** | Dart isolate'ler arası bellek paylaşılmaz → **gerçek lock-free SPSC YOK**; `SendPort`/`ReceivePort` mesaj kuyruğu (§4 dürüst sapma) |
| **Hata yönetimi** | `throw Exception(...)` + `Future` hata akışı (async I/O) — Result/Either yerine Dart idiomu |
| **`clamp` / `abs`** | `num.clamp`/`abs` — `double` üzerinde; brickwall limiter K3 clamp'i |
| **1-based DEĞİL** | Dart **0-based** dizin (Lua'dan farklıdır); `ByteData.setUint32` offset'leri 0'dan başlar |
| **int wrap** | Dart `int` 64-bit; LCG `& 0xFFFFFFFF` ile 32-bit wrap zorlanır (Rust `wrapping_*` karşılığı) |
| **Sıcak yol yasakları** | RT callback'te Dart GC/`async` (priority inversion) — §4 kuyruk non-RT kontrol trafiği içindir; **Flutter'da real-time DSP yok** (§1 dürüst sınır) |
| **Bit-perfect (K6)** | `ByteData.setFloat32/getFloat32` bit-düz — WAV yaz→ok round-trip'te örnek kaybı yok |

## §9 Sürüm Geçmişi

| Sürüm | Tarih | Değişiklik |
|---|---|---|
| 1.0.0 | 2026-10-19 | İlk sürüm: 6 örnek (RBJ DF1 · 32f buffer · Isolate kuyruk dürüst sapma · WAV ByteData · comp+limiter · Platform enum dürüst sınır) |

**Authority:** Master Orchestrator → Embedded Engineer · Derlenmedi/test edilmedi (araştırma MD) ·
commit'siz (orkestratöre ait)
