---
title: "Neva Engine — Zig Kod Örnekleri (6 Örnek)"
type: reference
category: nevaengine
version: 1.0.0
status: active
authority: "Master Orchestrator → Embedded Engineer"
updated: 2026-10-19
---

# Neva Engine — Zig Kod Örnekleri

> **Derlenmedi/test edilmedi — üretim kodu doc 01/03 planından geçer.** · v1.0.0 · K1-K6 uyumlu · **Rol:** Zig = **motor adayı** (doc 01 §5) — nohiddenalloc · error union · std API sürüm duyarlı.
> **Zorunlu Bağlantılar:** [[11-cok-dilli-kod-ornekleri]] (index) · [[10-io-format-32bit-192khz-kurallari]] (K1-K6) · [[06-gereksinimler-ve-farkliliklar]] (R16 · R18) · [[07-mimari-diyagram-kontrol-yuzeyleri]] (R10)

## §1 Bağlam
Zig, Neva Engine motorunun aday dillerindendir (doc 01 §5): çalışma zamanı yok, tahsis açık (`alloc`), `std.atomic` ile lock-free SPSC mümkündür. Örnekler yalnız `std` — std API'leri sürüm duyarlıdır, `⚠️` ile işaretlidir. DSP/I/O yolu K1-K6 (32-bit float · 44.1–192 kHz · varsayılan 48 kHz) üzerinden akar.
## §2 Örnek 1 — Biquad EQ · (R16 · K2)
```zig
// neva_biquad.zig — Float32 (K2) · RBJ peaking (a0 normalize) · durum struct'ta (nohiddenalloc)
const Biquad = struct {
    b0: f32, b1: f32, b2: f32, a1: f32, a2: f32, z1: f32 = 0, z2: f32 = 0,
    pub fn peaking(fs: f32, f0i: f32, qi: f32, g_db: f32) Biquad {
        const q = std.math.clamp(qi, 0.05, 32.0); const f0 = std.math.clamp(f0i, 10.0, 0.49 * fs);   // Nyquist
        const a = std.math.pow(f32, 10.0, g_db / 40.0); const w0 = 2.0 * std.math.pi * (f0 / fs);
        const al = @sin(w0) / (2.0 * q); const a0 = 1.0 + al / a; const cw = @cos(w0);
        return .{ .b0 = (1.0 + al * a) / a0, .b1 = -2.0 * cw / a0, .b2 = (1.0 - al * a) / a0,
                  .a1 = -2.0 * cw / a0, .a2 = (1.0 - al / a) / a0 };
    }
    pub fn process(self: *Biquad, x: f32) f32 {   // DF2T — üretim tercihi (R16)
        const y = self.b0 * x + self.z1; self.z1 = self.b1 * x - self.a1 * y + self.z2; self.z2 = self.b2 * x - self.a2 * y; y }
};
```
**Neva bağlamı:** katsayı doğrulama/üretim — doğrudan C++20 çekirdek kanalı (doc 02/03); DF2T tek pointer, gizli tahsis yok.
## §3 Örnek 2 — 32f buffer döngüsü · (K2 · K4 · K5)
```zig
const k4 = [_]u32{ 44_100, 48_000, 88_200, 96_000, 176_400, 192_000 };
fn has(list: []const u32, v: u32) bool { for (list) |r| if (r == v) return true; return false; }
pub fn pickSampleRate(pref: u32, dev: []const u32) u32 {           // K4 → K6 → K5
    if (!has(&k4, pref)) return 48_000; if (has(dev, pref)) return pref;      // K4 default · K6 eşit
    var best: u32 = 48_000; for (dev) |r| if (has(&k4, r) and r > best) best = r; return best; }   // K5 en yüksek
pub fn padAndClip(gpa: std.mem.Allocator, x: []const f32, frame: usize, ceil_db: f32) ![]f32 {
    const rem = x.len % frame; const buf = try gpa.alloc(f32, x.len + if (rem == 0) 0 else frame - rem);  // K5 pad · tek alloc
    @memcpy(buf[0..x.len], x); @memset(buf[x.len..], 0.0);
    const c = std.math.pow(f32, 10.0, ceil_db / 20.0);
    for (buf) |*s| s.* = std.math.clamp(s.*, -c, c); return buf; }   // clip (K2)
```
**Neva bağlamı:** pad tek `gpa.alloc` (nohiddenalloc — çağıran `buf`yu `gpa.free` ile serbest bırakır); clip ceiling 32f sahada.
## §4 Örnek 3 — SPSC ring buffer · (R10)
```zig
// std.atomic.Value ile lock-free SPSC (R10 uygun) — veri kendisi atomik değil
pub fn SPSC(comptime T: type, comptime cap: usize) type { return struct {
    buf: [cap]T = undefined, head: std.atomic.Value(usize) = .init(0), tail: std.atomic.Value(usize) = .init(0),
    pub fn push(self: *@This(), v: T) bool {
        const t = self.tail.load(.monotonic); const n = (t + 1) % cap;
        if (n == self.head.load(.acquire)) return false;            // dolu
        self.buf[t] = v; self.tail.store(n, .release); true }
    pub fn pop(self: *@This(), out: *T) bool {
        const h = self.head.load(.monotonic); if (h == self.tail.load(.acquire)) return false;  // boş
        out.* = self.buf[h]; self.head.store((h + 1) % cap, .release); true }
}; }
```
**Dürüst sapma:** `std.atomic.Value` Zig 0.11+ kabulü — hedef sürüm doc 01 §5.2 (`⚠️ VERIFICATION REQUIRED`). **Neva bağlamı:** gerçek lock-free SPSC (R10) — üretim callback kanalı.
## §5 Örnek 4 — WAV 32f I/O · (K3 · K1)
```zig
// std.io API'si sürüm arasında değişir (0.13/0.14 ayrımı ⚠️ VERIFICATION REQUIRED)
// → ham bayt parse: sabit 44-bayt ofset (K3) · 32f mono · LE (std.mem.readInt)
pub const WavInfo = struct { rate: u32, frames: usize };
pub fn parseF32Mono(b: []const u8) !WavInfo {
    if (b.len < 44 or !std.mem.eql(u8, b[0..4], "RIFF") or !std.mem.eql(u8, b[8..12], "WAVE")) return error.BadWave;
    if (std.mem.readInt(u16, b[20..22], .little) != 3 or std.mem.readInt(u16, b[22..24], .little) != 1 or std.mem.readInt(u16, b[34..36], .little) != 32) return error.NotMono32f;   // fmt 3 · mono · 32 bit
    return .{ .rate = std.mem.readInt(u32, b[24..28], .little),
              .frames = std.mem.readInt(u32, b[40..44], .little) / 4 };            // 'data' bayt / 4
}
```
**Dürüst sapma:** genişletilmiş chunk'lı WAV'larda chunk-walk gerekir; std.io Reader hedef sürüme göre güncellenir. **Neva bağlamı:** doc 05 PCM doğrulama (K1 girdi · K3 çıktı).
## §6 Örnek 5 — Compressor+limiter · (R10 · K3)
```zig
// envelope (atk 5 ms / rel 50 ms) → −12 dB → 4:1 → clamp 0 dBFS (K3) · durum kalıcı (K7)
pub const Comp = struct {
    env: f32 = 0, ka: f32, kr: f32, thr: f32, ratio: f32 = 4.0,
    pub fn init(fs: f32) Comp { return .{ .ka = @exp(-1.0 / (0.005 * fs)), .kr = @exp(-1.0 / (0.050 * fs)), .thr = std.math.pow(f32, 10.0, -12.0 / 20.0) }; }
    pub fn process(self: *Comp, x: f32) f32 {
        const a = if (@abs(x) > self.env) self.ka else self.kr; self.env = a * self.env + (1.0 - a) * @abs(x);
        const g = if (self.env > self.thr) std.math.pow(f32, 10.0, (20.0 * @log10(self.env + 1e-12) + 12.0) * (1.0 - 1.0 / self.ratio) / 20.0) else 1.0;
        return std.math.clamp(x * g, -1.0, 1.0); }                 // brickwall
};
```
**Neva bağlamı:** tek geçiş — tahsis yok (nohiddenalloc); üretim compressor zinciri doc 03 §7.
## §7 Örnek 6 — OS/cihaz enum · (R18)
```zig
// SAPMA: ses cihazı listeleme/enum std'de YOK — OS derleme zamanında, backend dosyadan (R18)
const builtin = @import("builtin");
pub const Backend = enum { wasapi, asio, coreaudio, alsa, pulse, null_device, unknown };
pub fn osKind() []const u8 {                      // doc 04 §4 — derleme zamanı OS
    return switch (builtin.os.tag) { .windows => "Windows", .macos => "macOS", .linux => "Linux", else => "Unknown" }; }
pub fn backendFromName(name: []const u8) Backend {   // kayıt edilmiş seçim (dosyadan, R18)
    if (std.mem.indexOf(u8, name, "WASAPI") != null) return .wasapi; if (std.mem.indexOf(u8, name, "ASIO") != null) return .asio;
    if (std.mem.indexOf(u8, name, "ALSA") != null) return .alsa; if (std.mem.indexOf(u8, name, "Pulse") != null) return .pulse;
    if (std.mem.indexOf(u8, name, "Null") != null) return .null_device; .unknown }
```
**Dürüst sapma:** gerçek cihaz keşfi (ASIO/WASAPI enumeration) dilde değil, SDK/çekirdektir (doc 04 §4.6 fallback). **Neva bağlamı:** profil/ayar katmanı — cihaz enum yalnız C++/Rust çekirdekte (R18).
## §8 Dile özel notlar (Zig)
| Başlık | Not |
|---|---|
| **nohiddenalloc** | Tüm tahsis açık (`gpa`) — callback/UI yasak; `undefined` buffer + `@memset` deseni |
| **Error union** | `!T` dönüşlü her I/O/alloc yolu — çağıran `try` ile zorunlu, exception yok |
| **Sürüm duyarlı std** | `std.atomic.Value` (0.11+) ve `std.io.Reader` (0.13/0.14) `⚠️ VERIFICATION REQUIRED` |
| **Sahiplik yok** | Rust `split_at` borrow modeli Zig'de yok — `[]T` referans döner, dilim kopyalamaz |
| **Sıcak yol yasakları** | Callback içinde `gpa` tahsisi, `std.debug.print` (yalnız debug) |

**Kural: clean-room (R1) — standart desenler, tescilli kod kopyalanmaz.** · **Authority:** Master Orchestrator → Embedded Engineer · v1.0.0 (2026-10-19) · derlenmedi/test edilmedi · commit'siz (orkestratöre ait)
