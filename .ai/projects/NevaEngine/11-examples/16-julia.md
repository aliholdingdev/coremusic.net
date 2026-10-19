---
title: "Neva Engine — Julia Kod Örnekleri (6 Örnek)"
type: reference
category: nevaengine
version: 1.0.0
status: active
authority: "Master Orchestrator → Embedded Engineer"
updated: 2026-10-19
---

# Neva Engine — Julia Kod Örnekleri

> **Derlenmedi/test edilmedi — üretim kodu doc 01/03 planından geçer.** · v1.0.0 · K1-K6 uyumlu · **Rol:** Julia = **DSP araştırması / offline** — gerçek zamanlı (RT) DEĞİLDİR.
> **Zorunlu Bağlantılar:** [[11-cok-dilli-kod-ornekleri]] (index) · [[10-io-format-32bit-192khz-kurallari]] (K1-K6) · [[06-gereksinimler-ve-farkliliklar]] (R16 · R18) · [[07-mimari-diyagram-kontrol-yuzeyleri]] (R10)

## §1 Bağlam
Julia, Neva Engine'de **offline doğrama** dilidir (katsayı doğrulama · filtre yanıtı · ölçüm analizi). Örnekler yalnız **Julia Base + TOML stdlib**; harici paket adları `⚠️` ile işaretlidir, API'leri garanti edilmez. DSP/I/O yolu K1-K6 (32-bit float · 44.1–192 kHz · varsayılan 48 kHz) üzerinden akar.
## §2 Örnek 1 — Biquad EQ · (R16 · K2)
```julia
# neva_biquad.jl — yalnız Julia Base · Float32 (K2) · RBJ peaking (a0=1 normalize)
struct BiquadCoeffs; b0::Float32; b1::Float32; b2::Float32; a1::Float32; a2::Float32; end
function make_peaking(f0::Float32, q::Float32, gain_db::Float32, fs::Float32)
    q = clamp(q, 0.05f0, 32.0f0); f0 = clamp(f0, 10.0f0, 0.49f0*fs)     # Nyquist
    a = 10.0f0^(gain_db/40.0f0); w0 = 2.0f0*Float32(pi)*(f0/fs)
    α = sin(w0)/(2.0f0*q); a0 = 1.0f0 + α/a
    BiquadCoeffs((1+α*a)/a0, -2*cos(w0)/a0, (1-α*a)/a0, -2*cos(w0)/a0, (1-α/a)/a0)
end
mutable struct BiquadDf1; c::BiquadCoeffs; x1::Float32=0; x2::Float32=0; y1::Float32=0; y2::Float32=0; end
function process!(f::BiquadDf1, x::Float32)   # DF1 4 durum (DF2T = üretim tercihi, R16)
    y = f.c.b0*x + f.c.b1*f.x1 + f.c.b2*f.x2 - f.c.a1*f.y1 - f.c.a2*f.y2
    f.x2=f.x1; f.x1=x; f.y2=f.y1; f.y1=y; y
end
```
**Neva bağlamı:** offline katsayı doğrulama — koefisyonlar C++20 çekirdeğe (doc 02/03) aktarılır; Julia RT katmanı değildir.
## §3 Örnek 2 — 32f buffer döngüsü · (K2 · K4 · K5)
```julia
const DEFAULT_RATE = 48_000; const K4_RATES = (44_100, 48_000, 88_200, 96_000, 176_400, 192_000)
function pick_sample_rate(preferred::Int, device_rates::Vector{Int})
    pref = preferred in K4_RATES ? preferred : DEFAULT_RATE          # K4
    pref in device_rates && return pref                               # K6: SR eşit
    k4 = filter(r -> r in K4_RATES, device_rates)
    isempty(k4) ? DEFAULT_RATE : maximum(k4)                          # K5: en yüksek yerel
end
split_channels(buf) = (buf[1:2:end], buf[2:2:end])   # vektörel alternatif (kanal split)
function process_interleaved!(buf::Vector{Float32}, eq::Vector{BiquadDf1})   # interleaved 32f
    @inbounds for i in 1:2:length(buf)-1; buf[i]=process!(eq[1],buf[i]); buf[i+1]=process!(eq[2],buf[i+1]); end
    buf    # K1→K2: ham 32f, dönüşüm yok — IIR durumu sıralı → döngü zorunlu (sapma)
end
```
**Sapma (madde-başına):** vektörel alternatif `split_channels` (kanal split); IIR durum-geçişi sıralı → EQ vektörleştiremez. **Neva bağlamı:** offline blok işleme (test WAV'ları) — canlı callback değil.
## §4 Örnek 3 — SPSC ring buffer · (R10)
```julia
# SAPMA: Julia Base'de gerçek lock-free SPSC YOK — tek-thread offline halka alternatifi
mutable struct SimpleRing; buf::Vector{Float32}; mask::Int; head::Int; tail::Int; end   # cap = 2^n
SimpleRing(cap::Int) = SimpleRing(zeros(Float32, cap), cap - 1, 0, 0)
function push!(r::SimpleRing, v::Float32)
    next = r.head + 1
    next - r.tail > r.mask && return false                    # dolu
    r.buf[(r.head & r.mask) + 1] = v; r.head = next; true     # 1-bazlı indeks (+1)
end
function pop!(r::SimpleRing)
    r.tail == r.head && return nothing                        # boş
    v = r.buf[(r.tail & r.mask) + 1]; r.tail += 1; v
end
```
**Dürüst sapma:** lock-free/atomik SPSC Base'de YOK. **Neva bağlamı:** çok-thread gerekiyorsa `Base.Threads.Channel` (kilitli, RT değil) veya harici paket (`⚠️ VERIFICATION REQUIRED`); gerçek SPSC C++/Rust çekirdektedir (R10 · doc 03).
## §5 Örnek 4 — WAV 32f I/O · (K3 · K1)
```julia
le_u16(b, i) = UInt16(b[i]) | UInt16(b[i+1]) << 8      # LE bayttan elle kurulum (host-bağımsız)
le_u32(b, i) = UInt32(b[i]) | UInt32(b[i+1]) << 8 | UInt32(b[i+2]) << 16 | UInt32(b[i+3]) << 24
function read_wav_f32(path)        # Base: WAV okuyucu YOK → elle parse (44-bayt header)
    raw = read(path)
    (String(raw[1:4]) == "RIFF" && String(raw[9:12]) == "WAVE") || error("RIFF/WAVE")
    fmt = le_u16(raw, 21); ch = le_u16(raw, 23); fs = le_u32(raw, 25)
    (fmt == 0x0003 && fs in K4_RATES) || error("format 3 / K4 SR (K1·K4)")
    nd = Int(le_u32(raw, 41))                        # 'data' chunk boyutu
    collect(reinterpret(Float32, raw[45:44+nd])), Int(fs), Int(ch)
end
```
**Dürüst sapma:** elle sabit-ofset parse (genişletilmiş chunk'lı WAV'larda chunk-walk gerekir). **Harici paket örneği: `WAV.jl`** (`⚠️ VERIFICATION REQUIRED`). **Neva bağlamı:** offline test I/O — doc 05 PCM doğrulaması (K1 girdi · K3 çıktı).
## §6 Örnek 5 — Compressor+limiter · (R10 · K3)
```julia
# envelope (atk 5 ms / rel 50 ms) → −12 dB → 4:1 → makeup → 0 dBFS clamp (K3)
mutable struct Compressor; env::Float32; atk::Float32; rel::Float32; thr::Float32; ratio::Float32; makeup::Float32; end
function Compressor(fs::Float32)             # tek kutuplu IIR katsayıları
    Compressor(0f0, exp(-1f0/(0.005f0*fs)), exp(-1f0/(0.050f0*fs)),
               10.0f0^(-12f0/20f0), 4f0, 10.0f0^((1f0-1f0/4f0)*12f0*0.5f0/20f0))
end
function process!(c::Compressor, x::Float32)
    a = abs(x) > c.env ? c.atk : c.rel                       # attack/release geçişi
    c.env = a*c.env + (1f0 - a)*abs(x); g = c.makeup
    c.env > c.thr && (g *= 10.0f0^(-(20f0*log10(c.env+1f-12)+12f0)*(1f0-1f0/c.ratio)/20f0))
    clamp(x*g, -1f0, 1f0)                                    # brickwall 0 dBFS
end
```
**TPDF dither notu (K3):** 24-bit int düşüşünde `d = (rand(Float32)-rand(Float32))/2f0^23` (1 LSB) + round + clamp; float32 çıktıda (K6) dither yok. **Neva bağlamı:** offline dinamik doğrulama; üretim compressor C++ çekirdekte (R10).
## §7 Örnek 6 — OS/cihaz enum · (R18)
```julia
# SAPMA: Julia Base'de ses cihazı enum/erişimi YOK — yalnız dosyadan okuma
using TOML                                            # Julia stdlib
function load_saved_profile(path::String)             # R18: önceki seçim kaydı
    isfile(path) || return nothing
    d = TOML.parsefile(path)
    haskey(d, "backend") ? (backend = String(d["backend"]),
                            rate = Int(get(d, "rate", 48_000))) : nothing    # K4 default
end
detect_os() = String(Sys.KERNEL)                       # OS ipucu — cihaz erişimi DEĞİL
```
**Dürüst sapma:** cihaz listesi/enum bu dilde YOK (gerçek cihaz: harici paket örn. `PortAudio.jl`, `⚠️ VERIFICATION REQUIRED`). **Neva bağlamı:** offline profil/ayar katmanı (R5); cihaz enum yalnız C++/Rust çekirdekte (R18 · doc 03).
## §8 Dile özel notlar (Julia)
| Başlık | Not |
|---|---|
| **Tip-stabilite** | Alan/argüman tipleri sabit (`Float32`) — her çağrı aynı tipte döner; tiplenmemiş kod dinamikleşir (K2) |
| **Float32 kaçakları** | Literaller `Float64` → her yerde `10.0f0^…`/`Float32(pi)`; `10.0^(…)` sessizce K2 zincirini double'a kaçırır |
| **Bağımlılık** | Yalnız Base + TOML; `WAV.jl`/`PortAudio.jl` örnektir, API'leri garanti edilmez (`⚠️ VERIFICATION REQUIRED`) |
| **RT yok · 1-bazlı** | GC + JIT ısınması RT taahhüt etmez, lock-free SPSC yok (§4); ring indeksinde `(head & mask)+1` zorunlu |
| **Sıcak yol yasakları** | `push!`/`process!` içinde yeni `Vector` tahsisi, `println` (yalnız debug) |

**Kural: clean-room (R1) — standart desenler, tescilli kod kopyalanmaz.** · **Authority:** Master Orchestrator → Embedded Engineer · v1.0.0 (2026-10-19) · derlenmedi/test edilmedi · commit'siz (orkestratöre ait)
