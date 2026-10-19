---
title: "Neva Engine — Lua Kod Örnekleri (6 Örnek)"
type: reference
category: nevaengine
version: 1.0.0
status: active
authority: "Master Orchestrator → Embedded Engineer"
updated: 2026-10-19
---

# Neva Engine — Lua Kod Örnekleri

> **Durum:** v1.0.0 · derlenmedi/test edilmedi (araştırma MD) · K1-K6 uyumlu

**Zorunlu Bağlantılar:** [[11-cok-dilli-kod-ornekleri]] (index) · [[10-io-format-32bit-192khz-kurallari]] (K1-K6 SSOT) · [[06-gereksinimler-ve-farkliliklar]] (R16 · R18) · [[07-mimari-diyagram-kontrol-yuzeyleri]] (R10)

## §1 Bağlam

Lua, Neva **preset/skript motoru eklentisidir**. **Dürüst sınır: Lua'da gerçek zamanlı
ses YOKTUR** — tek-thread VM, GC duraklaması, `number` = IEEE-754 çifttir (float32 yok).
Lua yalnız preset/otomasyon/kontrol mantığı üretir; DSP C++ çekirdekte döner. Örnekler
**Lua 5.3+** (`string.pack`, tam sayı bit operatörleri) üzerine kuruludur.

| Kural | İçerik |
|---|---|
| K1 | Input (capture/loopback) **32-bit float** (çekirdekten Lua'ya kopya) |
| K2 | Internal zincir **32-bit float** (Lua'da `number` = çift — dürüst sapma) |
| K3 | Output **32-bit float** — 24-bit int düşüşünde **TPDF dither zorunlu** |
| K4 | SR **44.1 / 48 / 88.2 / 96 / 176.4 / 192 kHz** · varsayılan **48 kHz** |
| K5 | Cihaz 192 sunmuyorsa → **cihazın en yüksek yerel SR'si** |
| K6 | Bit-perfect = **SR eşit + float32** (Lua kopya üzerinden — bit-düz değil) |

> ⚠️ **Bu dosyadaki kod derlenmedi/test edilmedi; vault referans örneğidir.** Üretim kodu
> doc 01/03 planındaki Faz 0 build+test kapısından geçer. Yalnız Lua 5.3+ std
> (`math`, `string.pack/unpack`, `io`, `os`) kullanılmıştır. Zero-hallucination:
> doğrulanamayan satır `⚠️ VERIFICATION REQUIRED`.

## §2 Örnek 1 — Biquad EQ (RBJ peaking + Direct Form I) · (R16 · K2)

**Dürüst sapma (R16 çift form):** §4 şeması RBJ peaking + Direct Form I ister; Lua
örneğinde tek form (DF1) verilir — Transposed DF-II C++/Rust dosyalarındaki ayrı tablo
ile örneklendi. **K2 sapması:** Lua `number` IEEE-754 **çifttir** — 32-bit float
`string.pack("f")` ile yazılır; hesap çiftte çalışır (⚠️ üretimde band hesabı C++ çekirdekte).

```lua
-- neva_biquad.lua — Lua 5.3+ (math lib), number = çift (K2 dürüst sapma)
local M = {}

-- RBJ peaking (Audio EQ Cookbook) — a0 = 1 normalize; koefisyon tablosu
function M.make_peaking(f0, q, gain_db, fs)
  q       = math.min(math.max(q, 0.05), 32.0)
  f0      = math.min(math.max(f0, 10.0), 0.49 * fs)   -- Nyquist'e çarpmama
  gain_db = math.min(math.max(gain_db, -24.0), 24.0)

  local a      = 10 ^ (gain_db / 40.0)
  local w0     = 2.0 * math.pi * (f0 / fs)
  local cos_w0 = math.cos(w0)
  local alpha  = math.sin(w0) / (2.0 * q)
  local a0     = 1.0 + alpha / a

  return {
    b0 = (1.0 + alpha * a) / a0,
    b1 = (-2.0 * cos_w0) / a0,
    b2 = (1.0 - alpha * a) / a0,
    a1 = (-2.0 * cos_w0) / a0,
    a2 = (1.0 - alpha / a)  / a0,
  }
end

-- Direct Form I — 4 durum (R16 referans form); tablo = durum
function M.new_df1(c)
  local s = { c = c, x1 = 0, x2 = 0, y1 = 0, y2 = 0 }
  function s:set_coeffs(neu) self.c = neu; self:reset() end   -- pop azaltma
  function s:reset() self.x1, self.x2, self.y1, self.y2 = 0, 0, 0, 0 end
  function s:process(x)
    local c = self.c
    local y = c.b0 * x + c.b1 * self.x1 + c.b2 * self.x2 - c.a1 * self.y1 - c.a2 * self.y2
    self.x2, self.x1 = self.x1, x
    self.y2, self.y1 = self.y1, y
    return y
  end
  return s
end

return M
```

**Neva bağlamı:** R16 preset hesabı — Lua skript motoru band parametrelerini hesaplar,
C++ çekirdeğe koefisyon tablosunu gönderir (gerçek DSP Lua'da DEĞİL — §1 dürüst sınır).

## §3 Örnek 2 — 32f buffer döngüsü (K2/K4/K5) · (K2 · K4 · K5)

```lua
-- neva_buffer.lua — Lua 5.3+ std, tablo (array) — gerçek 32f buffer DEĞİL (dürüst sapma)
local M = {}

M.DEFAULT_RATE = 48000                                -- K4 varsayılan
local K4_RATES = { 44100, 48000, 88200, 96000, 176400, 192000 }

-- K4 destek listesi (Lua 1-based table)
function M.is_k4_rate(sr)
  for _, r in ipairs(K4_RATES) do
    if r == sr then return true end
  end
  return false
end

-- SR seçimi: tercih → (K6 adayı?) → K5 en yüksek yerel → K4 48 kHz
function M.pick_sample_rate(preferred, device_rates)
  local p = M.is_k4_rate(preferred) and preferred or M.DEFAULT_RATE
  for _, r in ipairs(device_rates) do
    if r == p then return p end                        -- K6: SR eşit
  end
  local best = 0                                      -- K5 fallback
  for _, r in ipairs(device_rates) do
    if M.is_k4_rate(r) and r > best then best = r end
  end
  if best > 0 then return best end
  return M.DEFAULT_RATE
end

-- Interleaved float32 zincir — stereo, tablo üzerinde (K1 → K2 → K3)
-- ⚠️ dürüst sapma: Lua tablosu number[] = çift; "float32" burada mantıksal kopyadır
function M.process_interleaved(buf, left, right)
  local i = 1                                         -- Lua 1-based!
  while i + 1 <= #buf do
    buf[i]     = left:process(buf[i])                 -- K1: mantıksal 32f kopya
    buf[i + 1] = right:process(buf[i + 1])            -- K2: DSP (çift)
    i = i + 2
  end
end
```

**Neva bağlamı:** C++ çekirdekten Lua'ya kopya tampon (preset/otomasyon hesabı) — Lua
tarafında gerçek 32f yok; bit-perfect (K6) C++ çekirdekte korunur.

## §4 Örnek 3 — Kilit tabanlı kuyruk (SPSC yerine geçici) · (R10)

**Dürüst sapma (R10 lock-free SPSC):** Lua **tek-thread VM'dir** — atomik, lock-free,
thread primitive **YOKTUR**. `coroutine` tabanlı producer/consumer kuyruğu verilir;
**gerçek lock-free SPSC ring buffer YOKTUR**. `coroutine.yield/resume` tek-yazıcı/tek-
okuyucu sözleşmesini uygular; production'da lock-free ring **C++ çekirdekte** döner.

```lua
-- neva_queue.lua — coroutine tabanlı kuyruk — lock-free DEĞİL (tek-thread VM)
local M = {}

-- Sabit boyutlu Float kuyruk — coroutine (producer/consumer)
-- RT-güvenli DEĞİL: Lua GC + coroutine devri (R10 ihlali — bilinçli)
function M.new_ring(capacity)
  capacity = math.max(1, capacity)
  local buf, head, tail = {}, 1, 1                     -- Lua 1-based
  local ring = {}

  -- Yazıcı: doluysa yield (SPSC'deki "full" durumunun coroutine karşılığı)
  function ring:push(v)
    local next_head = head % capacity + 1
    if next_head == tail then coroutine.yield("full") end
    buf[head] = v
    head = next_head
  end

  -- Okuyucu: boşsa yield; değer coroutine.resume argümanı ile döner
  function ring:pop()
    if tail == head then coroutine.yield("empty") end
    local v = buf[tail]
    buf[tail] = nil                                   -- GC'ye bırak
    tail = tail % capacity + 1
    coroutine.yield("value", v)                       -- değeri çağıran'a gönder
  end

  return ring
end

-- Örnek kullanım: coroutine köprüsü (tek-thread sözleşmesi)
function M.bridge(ring, on_value)
  local co = coroutine.create(function()
    while true do ring:pop() end
  end)
  while true do
    local ok, state, v = coroutine.resume(co)
    if ok and state == "value" then on_value(v)
    elseif not ok then error(state) end               -- hata yukarı taşınır
  end
end
```

**Neva bağlamı:** Lua skript motoru ↔ C++ çekirdek kontrol trafiği (preset change, log).
R10 lock-free ring **çekirdek C++ katmanında** kalır; Lua coroutine kuyruğu yalnız non-RT
kontrol mesajları içindir (RT ses Lua'da yok — dürüst sınır, §1).

## §5 Örnek 4 — WAV 32f okuma/yazma (K3/K1) · (K3 · K1)

44-byte RIFF header · format **3 (IEEE_FLOAT)** · little-endian. `string.pack`/
`string.unpack` (Lua 5.3+) + `io.open`. **Dürüst sınır:** `string.pack("f")` **tek
hassasiyete** (`float`) yazar; Lua `number` çifttir — round-trip kaybı taşır.

```lua
-- neva_wav.lua — Lua 5.3+ (string.pack/unpack, io), little-endian
local M = {}

local WAV_FORMAT_IEEE_FLOAT = 3                        -- WAVE_FORMAT_IEEE_FLOAT (K1/K3)

-- Yazma: 44-byte standart header + float32 veri (K3 — 24-bit düşüşte dither §6)
function M.write_wav_f32(path, fs, channels, samples)
  if fs < 44100 or fs > 192000 then
    error("sample rate 44.1k..192k (K4)")
  end
  if channels < 1 or channels > 2 then error("channels 1..2") end
  local n = #samples
  local data_bytes = n * 4
  local f = assert(io.open(path, "wb"))

  -- header (little-endian "<")
  f:write("RIFF")
  f:write(string.pack("<I4", 36 + data_bytes))
  f:write("WAVE")
  f:write("fmt ")
  f:write(string.pack("<I4", 16))
  f:write(string.pack("<I2", WAV_FORMAT_IEEE_FLOAT))   -- format 3 (K1/K3)
  f:write(string.pack("<I2", channels))
  f:write(string.pack("<I4", fs))
  f:write(string.pack("<I4", fs * channels * 4))       -- byteRate
  f:write(string.pack("<I2", channels * 4))            -- blockAlign
  f:write(string.pack("<I2", 32))                      -- bits/sample
  f:write("data")
  f:write(string.pack("<I4", data_bytes))

  for i = 1, n do
    f:write(string.pack("<f", samples[i]))             -- number → float32 (K2→K3)
  end
  f:close()
end

function M.read_wav_f32(path)
  local f = assert(io.open(path, "rb"))
  local hdr = f:read(12)
  if hdr:sub(1, 4) ~= "RIFF" or hdr:sub(9, 12) ~= "WAVE" then
    f:close(); error("not RIFF/WAVE")
  end

  local info = { fs = 0, channels = 0, format = 0, bits = 0, data_bytes = 0, data_start = 0 }
  while true do
    local ch = f:read(8)
    if not ch then f:close(); error("data chunk missing") end
    local id = ch:sub(1, 4)
    local size = string.unpack("<I4", ch, 5)
    if id == "fmt " then
      if size < 16 then f:close(); error("fmt < 16") end
      local fmt = f:read(16)
      info.format   = string.unpack("<I2", fmt, 1)
      info.channels = string.unpack("<I2", fmt, 3)
      info.fs       = string.unpack("<I4", fmt, 5)
      info.bits     = string.unpack("<I2", fmt, 15)
      f:seek("cur", size - 16)                         -- fmt uzantısı atla
    elseif id == "data" then
      info.data_bytes = size
      info.data_start = f:seek("cur")
      break
    else
      f:seek("cur", size + (size % 2))                 -- word-hizalı padding
    end
  end

  if info.format ~= WAV_FORMAT_IEEE_FLOAT then f:close(); error("format != 3 (IEEE_FLOAT)") end
  if info.bits ~= 32 then f:close(); error("bits != 32") end
  if info.channels < 1 or info.channels > 2 then f:close(); error("channels 1..2") end
  if info.fs < 44100 or info.fs > 192000 then f:close(); error("sample rate 44.1k..192k (K4)") end

  local raw = f:read(info.data_bytes)
  f:close()
  local samples = {}
  for i = 1, info.data_bytes // 4 do                   -- Lua 5.3+ // tamsayı bölme
    samples[i] = string.unpack("<f", raw, 1 + (i - 1) * 4)  -- float32 → number (çift)
  end
  return samples, info
end
```

**Neva bağlamı:** Lua preset dosyası içe/dışa aktarma (offline render doğrulama). 32f WAV
— K6 bit-perfect **yalnız C++ çekirdekte**; Lua round-trip çift↔tek dönüşümü taşır
(dürüst not).

## §6 Örnek 5 — Compressor + brickwall limiter (R10) · (R10 · K3)

```lua
-- neva_comp.lua — Lua 5.3+ (math lib), number = çift (K2 dürüst sapma)
local M = {}

M.THRESHOLD_DB = -12.0
M.RATIO        = 4.0
M.ATTACK_SEC   = 0.005
M.RELEASE_SEC  = 0.050

function M.new(fs)
  local c = {
    env           = 0,
    atk_coef      = math.exp(-1.0 / (M.ATTACK_SEC * fs)),
    rel_coef      = math.exp(-1.0 / (M.RELEASE_SEC * fs)),
    threshold_lin = 10 ^ (M.THRESHOLD_DB / 20.0),
    makeup        = 10 ^ ((1.0 - 1.0 / M.RATIO) * (-M.THRESHOLD_DB) * 0.5 / 20.0),
    rng           = 0x12345678,                        -- TPDF dither durumu (K3)
  }

  -- Tek kutuplu IIR envelope: attack'e göre yüksel, release'e göre düş
  local function envelope(in_abs)
    local a = in_abs > c.env and c.atk_coef or c.rel_coef
    c.env = a * c.env + (1.0 - a) * in_abs
    return c.env
  end

  -- Tek örnek: compressor + brickwall limiter (K2 → K3 clamp)
  function c:process(x)
    local level = envelope(math.abs(x))
    local gain = self.makeup
    if level > self.threshold_lin then
      local level_db = 20.0 * math.log(level + 1.0e-12, 10)   -- Lua math.log(x, base)
      local over = level_db - M.THRESHOLD_DB
      local gr_db = -over * (1.0 - 1.0 / self.ratio)
      gain = gain * 10 ^ (gr_db / 20.0)
    end
    return math.min(math.max(x * gain, -1.0), 1.0)     -- 0 dBFS clamp (K3)
  end

  function c:process_block(buf)
    for i = 1, #buf do buf[i] = self:process(buf[i]) end
  end

  -- K3: 24-bit int düşüşünde TPDF dither (iki uniform [-0.5,+0.5] LSB toplamı)
  function c:tpdf_dither24()
    -- ⚠️ VERIFICATION REQUIRED: üretim RNG seçimi (xoshiro256** vb.)
    self.rng = (self.rng * 1664525 + 1013904223) % 0x100000000   -- uint32 wrap (LCG)
    local u1 = (self.rng >> 8) / 2 ^ 24
    self.rng = (self.rng * 1664525 + 1013904223) % 0x100000000
    local u2 = (self.rng >> 8) / 2 ^ 24
    return (u1 - u2) * (1.0 / 2 ^ 23)                 -- 2^-23 = 1 LSB @24-bit
  end

  -- alanları closure'dan döndür (self yerine tabloya yaz)
  c.ratio          = M.RATIO
  return c
end

return M
```

**Neva bağlamı:** R10 master bus preset hesabı — Lua gain-reduction değerini hesaplar,
C++ çekirdeğe parametre gönderir. Cihaz float32 sunuyorsa dither uygulanmaz (K6).

## §7 Örnek 6 — OS/cihaz enum (R18) · (R18 · K4 · K5)

**Dürüst not:** Lua `os` kütüphanesi OS adını verir ama **birincil ses backend'ini
(ASIO/WASAPI/ALSA/CoreAudio) GÖRÜTMEZ**; bu enum yalnız skript etiketidir. Backend
seçimi C++ çekirdektir (R18 backend + K5 sorgusu orada).

```lua
-- neva_device.lua — os lib (getenv) — skript/etiket katmanı — backend API YOK
local M = {}

-- Arayüz etiketi — ASIO/WASAPI/ALSA/CoreAudio bu katmanda DEĞİL (dürüst sınır)
M.Backend = { NONE = "NONE", PLATFORM_LABEL = "PLATFORM_LABEL" }

-- OS etiketi — backend seçimi DEĞİL (R18 backend C++ çekirdektir)
function M.os_label()
  local raw = os.getenv("OS")                          -- Windows
  if raw then return "Windows" end
  raw = os.getenv("HOME")
  if raw then
    -- ⚠️ VERIFICATION REQUIRED: Linux/macOS ayrımı Lua std'de sağlam tespit edilmez;
    -- üretimde C++ çekirdek Platform API'sinden etiket gelir.
    return "POSIX"
  end
  return "UNKNOWN"
end

function M.detect_backend_label()
  return M.Backend.PLATFORM_LABEL
end

function M.new_caps(max_rate, max_channels, supports_f32)
  return { max_rate = max_rate, max_channels = max_channels, supports_f32 = supports_f32 }
end

function M.new_profile(has_saved, saved_rate)
  return { has_saved = has_saved or false, saved_rate = saved_rate or 0 }
end

-- R18: 1) önceki seçim kaydı → 2) en yüksek SR'lı cihaz (K5) → 3) K4 48 kHz
function M.pick_default(profile, caps_list)
  if profile.has_saved then
    for _, d in ipairs(caps_list) do
      if profile.saved_rate <= d.max_rate then
        return { backend = M.Backend.PLATFORM_LABEL, rate = profile.saved_rate, from_saved = true }
      end
    end
  end
  local best = nil                                     -- K5 fallback
  for _, d in ipairs(caps_list) do
    if d.supports_f32 and d.max_rate > 48000 and (best == nil or d.max_rate > best.max_rate) then
      best = d
    end
  end
  if best then
    return { backend = M.Backend.PLATFORM_LABEL, rate = best.max_rate, from_saved = false }
  end
  return { backend = M.detect_backend_label(), rate = 48000, from_saved = false }   -- K4
end

return M
```

**Neva bağlamı:** Lua skript motoru — OS rozeti + R18 cihaz hafızası (preset kaydı).
**Dürüst not:** OS enum'unun tamamı bu katmanda **yalnız etiket**tir — asıl backend
seçimi + K5 en-yüksek-SR sorgusu C++ çekirdekte yapılır.

## §8 Dile özel notlar (Lua)

| Başlık | Not |
|---|---|
| **Standart** | **Lua 5.3+** (`string.pack/unpack`, `<<`/`>>`/`&` bit operatörleri, `//` tamsayı bölme) — LuaJIT/5.1 uyumu YOK (`string.pack` yok) |
| **1-based index** | Lua **1-based** dizin — `buf[1]` ilk örnek; ring `head/tail` 1'den başlar; `ByteData`/`setUint32` offset'lerinden FARKLIDIR (Dart/Java 0-based) |
| **`local` her yerde** | Global kaçışı önle; `local M = {}` modül deseni (return M) |
| **number = IEEE-754 çift** | Lua `number` **64-bit çifttir** — 32-bit float `string.pack("f")` ile yazılır; **gerçek float32 zincir Lua'da YOK** (K2 dürüst sapma); bit-perfect (K6) C++ çekirdekte |
| **Atomik/lock/thread YOK** | Tek-thread VM — **gerçek lock-free SPSC YOK**; `coroutine` kuyruğu (§4 dürüst sapma); production ring C++ çekirdekte |
| **Hata yönetimi** | `error()` + `pcall`/`assert` — Result/Either yerine Lua idiomu; chunk walk'da `f:close(); error(...)` kaynak sızıntısını önler |
| **`string.pack`/`unpack`** | `<` little-endian; `"f"` tek hassasiyet (`float`); `"I2"/"I4"` tamsayı — WAV RIFF header bayt düzeni |
| **uint32 wrap** | Lua 5.3 `& 0xFFFFFFFF` ile 32-bit wrap zorlanır (Rust `wrapping_*` karşılığı); `>>` bit kaydırma (5.3+ bit op) |
| **`math.log(x, base)`** | Lua `math.log` **iki argümanlı**dır (x, base) — tek argümanlı log10 DEĞİL (`math.log(level, 10)` yazılır) |
| **`//` tamsayı bölme** | Lua 5.3+ `//` tamsayı bölme (floor) — WAV bayt sayımı için |
| **Sıcak yol yasakları** | Lua GC + coroutine devri RT için engeldir → **Lua'da gerçek zamanlı ses YOK** (§1 dürüst sınır); kuyruk non-RT kontrol trafiği içindir |
| **Bit-perfect (K6)** | `string.pack("f")` bit-düz **DEĞİL** — çift↔tek dönüşüm kaybı taşır; round-trip doğrulama C++ çekirdekte |

## §9 Sürüm Geçmişi

| Sürüm | Tarih | Değişiklik |
|---|---|---|
| 1.0.0 | 2026-10-19 | İlk sürüm: 6 örnek (RBJ DF1 · 32f buffer · coroutine kuyruk dürüst sapma · WAV string.pack · comp+limiter · os.getenv enum dürüst sınır) |

**Authority:** Master Orchestrator → Embedded Engineer · Derlenmedi/test edilmedi (araştırma MD) ·
commit'siz (orkestratöre ait)
