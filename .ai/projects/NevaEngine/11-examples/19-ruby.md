---
title: "Neva Engine — Ruby Kod Örnekleri (6 Örnek)"
type: reference
category: nevaengine
version: 1.0.0
status: active
authority: "Master Orchestrator → Embedded Engineer"
updated: 2026-10-19
---

# Neva Engine — Ruby Kod Örnekleri

> **Derlenmedi/test edilmedi — üretim kodu doc 01/03 planından geçer.** · v1.0.0 · K1-K6 uyumlu · **Rol:** Ruby = **araç/script** (batch decode · test · raporlama) — gerçek zamanlı (RT) DEĞİLDİR (GVL).
> **Zorunlu Bağlantılar:** [[11-cok-dilli-kod-ornekleri]] (index) · [[10-io-format-32bit-192khz-kurallari]] (K1-K6) · [[06-gereksinimler-ve-farkliliklar]] (R16 · R18) · [[07-mimari-diyagram-kontrol-yuzeyleri]] (R10)

## §1 Bağlam
Ruby, Neva Engine'de **araç/script** dilidir (batch decode · test · raporlama). Örnekler yalnız Ruby stdlib; harici paket adları `⚠️` ile işaretlidir, API'leri garanti edilmez. GVL nedeniyle RT katman yoktur — DSP/I/O yolu K1-K6 (32-bit float · 44.1–192 kHz · varsayılan 48 kHz) üzerinden akar, canlı taraf C++ çekirdektedir.
## §2 Örnek 1 — Biquad EQ · (R16 · K2)
```ruby
# neva_biquad.rb — Ruby Float = Float64 (K2 sapması: float32 saha double'a genişler)
BiquadCoeffs = Struct.new(:b0, :b1, :b2, :a1, :a2)
def make_peaking(f0:, q:, gain_db:, fs:)
  q = q.clamp(0.05, 32.0); f0 = f0.clamp(10.0, 0.49 * fs)              # Nyquist
  a = 10.0**(gain_db / 40.0); w0 = 2.0 * Math::PI * (f0 / fs); al = Math.sin(w0) / (2.0 * q); a0 = 1.0 + al / a
  BiquadCoeffs.new((1 + al * a) / a0, -2 * Math.cos(w0) / a0, (1 - al * a) / a0, -2 * Math.cos(w0) / a0, (1 - al / a) / a0)
end
class BiquadDf1                                   # DF1 4 durum (DF2T üretim tercihi, R16)
  def initialize(c) = (@c = c; @x1 = @x2 = @y1 = @y2 = 0.0)
  def process(x)
    y = @c.b0*x + @c.b1*@x1 + @c.b2*@x2 - @c.a1*@y1 - @c.a2*@y2; @x2, @x1, @y2, @y1 = @x1, x, @y1, y; y
  end
end
```
**Neva bağlamı:** offline katsayı doğrulama — üretim DF2T C++20 çekirdekte (doc 02/03); Ruby Float64 sapması bilinçli (K2).
## §3 Örnek 2 — 32f buffer döngüsü · (K2 · K4 · K5)
```ruby
K4_RATES = [44_100, 48_000, 88_200, 96_000, 176_400, 192_000].freeze
def pick_sample_rate(preferred, device_rates)                   # K4 → K6 → K5
  pref = K4_RATES.include?(preferred) ? preferred : 48_000               # K4 default
  return pref if device_rates.include?(pref)                             # K6 SR eşit
  k4 = device_rates.select { |r| K4_RATES.include?(r) }; k4.empty? ? 48_000 : k4.max   # K5 en yüksek
end
def pad_and_clip(x, frame, ceiling_db: -0.1)     # vektörel alternatif (K5 dolgu)
  rem = x.length % frame; padded = x + Array.new(rem.zero? ? 0 : frame - rem, 0.0); c = 10.0**(ceiling_db / 20.0)
  padded.map { |s| [[s, c].min, -c].max }                               # clip (K2)
end
```
**Sapma (madde-başına):** vektörel alternatif `map` (clip/pad); IIR durum-geçişi sıralı → EQ vektörleştiremez. **Neva bağlamı:** offline blok işleme — canlı callback değil.
## §4 Örnek 3 — SPSC ring buffer · (R10)
```ruby
# SAPMA: Ruby GVL nedeniyle gerçek lock-free SPSC YOK (R10) — RBEB emülasyonu
class SimpleRing
  def initialize(cap) = (@buf = Array.new(cap); @mask = cap - 1; @head = @tail = 0)   # cap = 2^n
  def push(v)                                                # false = dolu
    nxt = @head + 1; return false if (nxt - @tail) > @mask
    @buf[@head & @mask] = v; @head = nxt; true               # maske indeksleme
  end
  def pop                                                    # nil = boş
    return nil if @tail == @head; v = @buf[@tail & @mask]; @tail += 1; v
  end
end
```
**Dürüst sapma:** gerçek lock-free/atomik SPSC Ruby'de YOK (GVL). **Neva bağlamı:** çok-thread gerekiyorsa `Queue` (stdlib, kilitli, RT değil); gerçek SPSC C++/Rust çekirdektedir (R10 · doc 03).
## §5 Örnek 4 — WAV 32f I/O · (K3 · K1)
```ruby
# Ruby stdlib'de WAV okuyucu YOK → elle 44-bayt sabit-ofset parse (K3 · 32f mono)
WAV_FMT_IEEE_FLOAT = 3; WAV_FMT_EXTENSIBLE = 0xFFFE
def read_wav_f32(path)
  raw = File.binread(path)
  raise "RIFF/WAVE" unless raw[0, 4] == "RIFF" && raw[8, 4] == "WAVE"
  fmt, ch, bits = raw[20, 2].unpack1("v"), raw[22, 2].unpack1("v"), raw[34, 2].unpack1("v")
  raise "format 3 / 32f mono (K1·K3)" unless [WAV_FMT_IEEE_FLOAT, WAV_FMT_EXTENSIBLE].include?(fmt) && ch == 1 && bits == 32
  nd = raw[40, 4].unpack1("V")                                # 'data' chunk boyutu (LE uint32)
  { pcm: raw[44, nd].unpack("e#{nd / 4}"), fs: raw[24, 4].unpack1("V"), ch: ch }  # 'e' = LE f32
end
```
**Dürüst sapma:** elle sabit-ofset parse — genişletilmiş chunk'larda chunk-walk gerekir. **Harici paket örneği: `ruby-audio` / `ruby_sndfile`** (`⚠️ VERIFICATION REQUIRED`). **Neva bağlamı:** offline test I/O — doc 05 PCM doğrulaması.
## §6 Örnek 5 — Compressor+limiter · (R10 · K3)
```ruby
# envelope (atk 5 ms / rel 50 ms) → −12 dB thr → 4:1 → clamp 0 dBFS (K3)
class Compressor
  def initialize(fs)
    @env = 0.0; @ka = Math.exp(-1.0 / (0.005 * fs)); @kr = Math.exp(-1.0 / (0.050 * fs))
    @thr = 10.0**(-12.0 / 20.0); @ratio = 4.0
  end
  def process(x)                                    # tek thread (R10) — canlı chain C++'ta
    a = x.abs > @env ? @ka : @kr; @env = a * @env + (1.0 - a) * x.abs
    g = @env > @thr ? 10.0**(((20.0 * Math.log10(@env + 1e-12) + 12.0) * (1 - 1 / @ratio)) / 20.0) : 1.0
    [[x * g, 1.0].min, -1.0].max                      # brickwall
  end
end
```
**Neva bağlamı:** offline dinamik doğrulama — tahsis serbest (RT değil); üretim compressor C++ çekirdekte (R10).
## §7 Örnek 6 — OS/cihaz enum · (R18)
```ruby
# SAPMA: Ruby'de ses cihazı API'si YOK — os_str stdlib (>= 3.2, ⚠️ VERIFICATION
# REQUIRED) — fallback: RbConfig + dosyadan okunan backend enum (R18)
def os_kind
  case (File.exist?("/tmp/os.txt") ? File.read("/tmp/os.txt") : RbConfig::CONFIG["host_os"]).downcase
  when /win/ then "Windows" when /darwin|mac/ then "macOS" when /linux/ then "Linux" else "Unknown"
  end
end
def backend_from_name(name)                          # kayıt edilmiş seçim (R18)
  n = name.downcase
  return :wasapi if n.include?("wasapi"); return :asio if n.include?("asio"); return :coreaudio if n.include?("coreaudio")
  return :alsa if n.include?("alsa"); return :pulse if n.include?("pulse"); n.include?("null") ? :null_device : :unknown
end
```
**Dürüst sapma:** gerçek cihaz listesi/enum Ruby'de YOK — profil/ayar katmanı; cihaz enum yalnız C++/Rust çekirdekte (R18 · doc 03 §7).
## §8 Dile özel notlar (Ruby)
| Başlık | Not |
|---|---|
| **GVL · RT yok** | Tek gerçek thread — lock-free SPSC yok (§4); canlı chain C++ çekirdekte (R10) |
| **Float64 (K2)** | `Float` = 64 bit — float32 saha double'a genişler; dosyada f32 `'e'` ile korunur |
| **WAV stdlib'de yok** | `File.binread` + `unpack` `'e'`/`'V'`/`'v'` (LE); harici paketler `⚠️` |
| **Araç/script katmanı** | Tahsis/GC serbest — Ruby'ye RT taahhüt edilmez; sıcak yol C++'tadır |
| **Sıcak yol yasakları** | Üretim callback yoluna Ruby koymak yasak; yalnız offline araç/test |

**Kural: clean-room (R1) — standart desenler, tescilli kod kopyalanmaz.** · **Authority:** Master Orchestrator → Embedded Engineer · v1.0.0 (2026-10-19) · derlenmedi/test edilmedi · commit'siz (orkestratöre ait)
