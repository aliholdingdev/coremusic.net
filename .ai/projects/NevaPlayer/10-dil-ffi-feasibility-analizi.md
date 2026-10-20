---
title: "Neva Player — 25 Dil FFI Uyumluluk & Feasibility Analizi (statik + runtime + kod örneği)"
type: doc
category: audio-engine
version: 1.0.0
status: active
authority: "SSOT: .ai/research/neva-player-dil-uyumluluk-matrisi.md (25 dil, 2026-10-19 doğrulanmış veri) · ADR-0105 v1.4.0 · architecture/neva-player/index.md v1.1.7 · zero-hallucination"
updated: 2026-10-20
---

# Neva Player — 25 Dil FFI Uyumluluk & Feasibility Analizi

**Zorunlu Bağlantılar:** [[neva-player-dil-uyumluluk-matrisi]] (kaynak matris) · [[neva-player-media-stack]] ·
[[media-stack-web-verification]] (112 kaynak) · [[01-kapsam-ve-kabul-kriterleri]] (AK-7 FFI smoke) ·
[[..../../.decisions/accepted/ADR-0105-neva-player-media-stack]] · [[..../../../architecture/neva-player/index]]

---

## §0 Kapsam, Kanıt ve Sorumluluk Sınırı

| Madde | Değer |
|---|---|
| **Soru** | Neva Engine (C++20, 32b/192kHz hi-res + 4K/HDR video) çekirdeğe **25 dil** nasıl bağlanır; hangi dilde **ne yapılabilir** (feasibility); statik ve runtime tarafı nedir? |
| **Kapsam** | 25 dil · FFI mekanizması · binding olgunluğu · **statik analiz** (§5) · **runtime analiz** (§6) · **kod örnekleri** (§4) · faz planı (§8) |
| **Kapsam dışı** | UI çatılarının (Qt/WPF/Flutter/egui...) sürüm/yıldız araştırması (UNKNOWN — matris U8) · medya kütüphane seçimi (ADR-0105) · sürücü katmanı |
| **Veri kaynağı** | Sürüm/yıldız/lisans verileri **2026-10-19** GitHub REST API · PyPI JSON · crates.io API ile doğrulanmıştır (matris §0.3). Bu doküman yeni sürüm **iddia etmez**; sayıları matristen alır. |
| **Örnek kod statüsü** | ⚠️ **TASLAK/ÖRNEK** — Neva Player diskte `0 .cpp` (ADR-0105 §1). §4'teki tüm kod blokları **önerilen şablondur**, mevcut koddur. `VERIFICATION REQUIRED` = derlenip test edilmedi. |
| **Derleme/hesap** | §7'deki tama/efor sayıları **DERIVED (tahmin, ölçüm değildir)**; §9'daki UNKNOWN'lar ancak §7.3 ölçüm kapısıyla kapanır. |

---

## §1 Karar Özeti (tek sayfa)

1. **Tek kamuya açık sözleşme = C ABI.** 25 dilin **tamamı** C ABI'ye erişir; C++ başlığını doğrudan dışa açmak yalnız C++ / Objective-C++ / Swift / D için anlamlıdır (matris B2). → `neva_c.h` tek başlık, PIMPL ile C++ gizlenir.
2. **★★★ = 11 dil** (çekirdek erişimi olgun + UI pratik) · **★★ = 8** · **★ = 6** (yalnız script/araç). Sayılar matris §3'ten birebir.
3. **Feasibility çekirdeği 3 dil:** `C++20` (çekirdek + masaüstü UI) · `C` (ABI) · `Python` (binding/betik). **Windows UI = C#**, ikinci platform hedefe göre Kotlin/TS/Dart/Swift (matris §8).
4. **RT kuralı (bağlayıcı):** gerçek zamanlı ses/yol **her zaman C++ içindedir**; diller yalnız **komut/durum/olay** taşır. **Yönlü geri çağırma (C→managed) ses thread'inde YASAKTIR** (GC/jit sıçraması — §6.3).
5. **Overhead kapanışının tek yolu blok-bazlı API'dir:** 128–4096 kare tek çağrıda taşınırsa FFI maliyeti binlerce örneklem arasına dağılır; örneklem başına çağrıda yalnız cgo'nun **~40 ns**'i bile **~%0,77** yutar (matris B5/§4.1 — *mühendislik hesabı, ölçüm değil*).
6. **WASM = Aşama 2 ve "hi-res exclusive" iddiası YOKTUR** (matris §6.3): `setSinkId` yalnız cihaz seçimi sunar; exclusive/192kHz zorlama API'si W3C'de tanımlı değildir.
7. **Bakım riski taşıyan binding'ler:** `autocxx` **ARCHIVED** · `Cxx.jl` **ARCHIVED** · `sol2` düşük aktivite · `SWIG` **GPL-3.0** · `Rcpp` **GPL-2.0** (lisans — §9 U-lar).

---

## §2 FFI Yüzeyi: `neva_c.h` Sözleşme Tasarımı (statik, birincil çıktı)

### §2.1 Kurallar (matris §7.2 ile birebir + mühendislik detayı)

| # | Kural | Gerekçe |
|---|---|---|
| R1 | **Tek başlık** `neva_c.h`, tümü `extern "C"`, `neva_*` ön eki | 25 dilin tamamı aynı sembol setini görür |
| R2 | **C++ asla dışa açılmaz** (PIMPL / `hidden` visibility) | Aksi halde her dilde ABI kırılır (vtables, isim kovalama, exception boundary) |
| R3 | **Her public struct'ta `size_t struct_size` ilk alan** | Gelecekte alan eklemede backward/forward compat; dil tarafı eski struct'ı güvenle geçirir |
| R4 | **Opaque handle** (`typedef struct neva_session neva_session_t;`) | Bellek ömrü tek taraflı; dil GC'si çekirdek nesneyi serbest bırakamaz |
| R5 | **Hata = `neva_status_t` enum + thread-local mesaj** | C++ istisnası C'ye sızmaz; JNA/P-Invoke/`cgo` hepsi enum'u temiz okur |
| R6 | **Veri kopyasız:** ham örnekleme asla dilin belleğine kopyalanmaz; **paylaşılan tampon + pointer/uzunluk** geçilir | GC basıncı FFI'dan büyük olur (matris §4.1-2) |
| R7 | **Bloklı çağrı:** `neva_session_read(frames)` — çağrı başına değil, **blok başına** | §7.1 hesabının ön koşulu |
| R8 | **Sembol gizleme:** `-fvisibility=hidden` / `.def` dosyası — yalnız `neva_*` dışarıda | Boyut + yanlış sembol erişimi riskini kapatır |
| R9 | **Sürüm/scheme semantiği:** `neva_session_api_version()` runtime'da okunur, derleme zamanı makro DEĞİL | Dil binding'i sürüm uyuşmazlığını **açılışta** fark eder |

### §2.2 Önerilen başlık iskeleti (TASLAK — `VERIFICATION REQUIRED`)

```c
/* neva_c.h — Neva Player tek kamuya açık C ABI sözleşmesi
 * Statü: TASLAK (Neva Player 0 .cpp). Derlenmedi/test edilmedi. */
#ifndef NEVA_C_H
#define NEVA_C_H

#include <stdint.h>
#include <stddef.h>

#if defined(_WIN32)
#  define NEVA_API __declspec(dllimport)   /* tüketen taraf; üreten taraf .def ile */
#else
#  define NEVA_API __attribute__((visibility("default")))
#endif

#ifdef __cplusplus
extern "C" {
#endif

typedef struct neva_session neva_session_t;            /* R4: opaque */

typedef enum neva_status {                              /* R5 */
    NEVA_OK                     = 0,
    NEVA_ERR_INVALID_ARG        = 1,
    NEVA_ERR_UNSUPPORTED_FORMAT = 2,
    NEVA_ERR_DEVICE             = 3,
    NEVA_ERR_AGAIN              = 4,   /* non-blocking: veri henüz yok */
    NEVA_ERR_VERSION            = 5    /* R9: ABI uyuşmazlığı */
} neva_status_t;

typedef struct neva_stream_params {                     /* R3 */
    size_t   struct_size;      /* = sizeof(neva_stream_params_t) */
    uint32_t sample_rate;      /* 44100 .. 192000 */
    uint16_t channels;         /* 1 .. 8 */
    uint16_t bits_per_sample;  /* 16 / 24 / 32 */
    uint8_t  is_float;         /* 0 = int_PCM, 1 = float32 */
    uint8_t  reserved[7];      /* hizalama + gelecek alan */
} neva_stream_params_t;

NEVA_API uint32_t      neva_session_api_version(void);                       /* R9 */
NEVA_API const char*   neva_last_error(void);                        /* thread-local, R5 */
NEVA_API neva_status_t neva_session_open(const char* uri,
                                         const neva_stream_params_t* p,
                                         neva_session_t** out);      /* R4 */
NEVA_API neva_status_t neva_session_read(neva_session_t* s,
                                         void* planar_or_interleaved,
                                         uint32_t frames,
                                         uint32_t* out_frames);      /* R7 */
NEVA_API neva_status_t neva_session_seek(neva_session_t* s, uint64_t frame);
NEVA_API void          neva_session_close(neva_session_t* s);        /* R4: tek serbest bırakıcı */

#ifdef __cplusplus
} /* extern "C" */
#endif
#endif /* NEVA_C_H */
```

**Neden `planar_or_interleaved` pointer'ı kopyasız:** Python `numpy.ndarray` / C# `Span<float>` /
Java `MemorySegment` / Dart `Pointer<Float>` / JS `Float32Array` hepsi **doğrudan bellek adresi** taşır;
çekirdek aynı tamponu okur (R6).

---

## §3 FFI Uyumluluk & Feasibility Matrisi (25 dil)

> **★ seviyesi** matris §3'ten (★★★=11 · ★★=8 · ★=6). **Efor = t-shirt (S/M/L/XL), DERIVED tahmin** —
> ölçüm değildir; **Faz** §8 planındadır. **Overhead** sütunu yalnızca doğrulanmış değeri gösterir.

| # | Dil | ★ | FFI mekanizması (doğru/güncel) | Binding aracı (2026-10-19) | Overhead | Efor | Faz | Feasibility kararı |
|---|---|---|---|---|---|---|---|---|
| 1 | **C** | ★★★ | Doğrudan statik/dinamik link | — (standart) | ~%0 | **S** | 1 | **Zorunlu** — ABI dili; UI için C++ |
| 2 | **C++** | ★★★ | Aynı süreç, static/DLL + PIMPL | — | ~%0 | **S** | 1 | **Varsayılan** — çekirdek + masaüstü UI |
| 3 | **Rust** | ★★★ | C ABI + `bindgen`; C++ tipleri `cxx` | bindgen **0.73.2** · cxx **1.0.205** · autocxx **ARCHIVED** | UNKNOWN | M | 2 | **Sarmalayıcı/araç** — çekirdeğe yazılmaz, sarar |
| 4 | **Python** | ★★★ | C++ extension (header-only) / ctypes | pybind11 **3.1.0** · nanobind **3.1.0** · Cython **3.3.0** | UNKNOWN | **M** | **1** | **İkinci hedef** — betik/test/prototip (AK-7) |
| 5 | **C#** | ★★★ | P/Invoke (`[DllImport]`) | CppSharp **v1.2** · ClangSharp · CsWinRT · SWIG | UNKNOWN | **M** | **1** | **Windows masaüstü UI** — .NET deneyimiyle uyum |
| 6 | **Java** | ★★★ | JNI · **FFM (JEP 454, JDK 22)** · JNA | JNA **5.19.1** · JavaCPP **1.5.14** (Apache-2/GPL-2+CE) | UNKNOWN | L | 2 | Kurumsal/araç UI; **RT JVM'de değil** |
| 7 | **Kotlin** | ★★★ | JVM: JNI/FFM · Native: `cinterop` · uniffi | cinterop (resmi) · uniffi **0.32.2** | UNKNOWN | L | 2 | **Android** hedefi varsa |
| 8 | **Swift** | ★★★ | **C++ interop (5.9+)** + C ABI | swift **70.514★** | UNKNOWN | L | 3 | **Apple** hedefi; Win/Linux olgunluğu UNKNOWN |
| 9 | **JavaScript** | ★★★ | WASM (Emscripten) / Node **N-API** | Emscripten · node-addon-api **2.411★** · wasm-bindgen **9.193★** | UNKNOWN | M | 2 | **Web/uzak kontrol** (§6.3 sınırı) |
| 10 | **TypeScript** | ★★★ | JS ile aynı + tip katmanı | JS araçları | UNKNOWN | M | 2 | Web'de JS yerine **TS önerilir** |
| 11 | **Dart** | ★★★ | `dart:ffi` + **ffigen** | dart-lang/native (BSD-3) · `jni` | UNKNOWN | L | 3 | **Flutter tek kod tabanı** masaüstü+mobil |
| 12 | **Go** | ★★ | `cgo` (yalnız C → **C ABI sarmalayıcı şart**) | stdlib `cmd/cgo` | **~40 ns/çağrı** ✅ | M | 2 | CLI/sunucu/dönüştürme aracı — **UI değil** |
| 13 | **Objective-C** | ★★ | **Objective-C++** (`.mm`) — ara katman yok | Clang yerleşik | ~%0 | S | 3 | Legacy Apple; yeni UI → Swift |
| 14 | **Zig** | ★★ | `@cImport` — **C++ header DESTEKLENMEZ** | Zig (GitHub aynası, upstream Codeberg) | UNKNOWN | S | 3 | SIMD/DSP yardımcı; **C ABI şart** |
| 15 | **D** | ★★ | `extern(C)` + **`extern(C++)`** | dmd (BSL-1.0) | UNKNOWN | S | 3 | Sistem aracı / legacy C++ entegrasyonu |
| 16 | **Haskell** | ★★ | Yerleşik FFI + `inline-c` (307★, lisans UNKNOWN) | GHC | UNKNOWN | M | 3 | Doğrulama/akademik araç |
| 17 | **OCaml** | ★★ | Yerleşik C FFI + ctypes (MIT, 413★) | ocaml.org manual | UNKNOWN | M | 3 | Araç / dil-tabanlı alt sistem |
| 18 | **Scala** | ★★ | JVM JNI/FFM · **Scala Native** C FFI | scala-native (lisans **UNKNOWN**) | UNKNOWN | M | 3 | JVM build/analiz katmanı |
| 19 | **VB.NET** | ★★ | P/Invoke (`Declare`/`DllImport`) | .NET yerleşik | UNKNOWN | S | 3 | Legacy Windows UI; modern → C# |
| 20 | **PHP** | ★ | **PHP FFI** (7.4+ yerleşik; php.ini ile kapalı olabilir → **UNKNOWN**) | php-src yerleşik | UNKNOWN | M | 2 | **Sunucu/CLI**: transkod, katalog, uzak servis |
| 21 | **Ruby** | ★ | `ffi` gem (BSD-3, 2.128★) / Rice (UNKNOWN lisans) | ffi · Rice | UNKNOWN | S | 3 | Kurulum/dağıtım/CI betiği |
| 22 | **Julia** | ★ | `ccall` + Clang.jl (243★) · **Cxx.jl ARCHIVED** | Clang.jl | UNKNOWN | S | 3 | Ses analizi / DSP araştırması / ölçüm |
| 23 | **R** | ★ | `.Call` + **Rcpp (GPL-2.0 ⚠️)** | Rcpp 797★ | UNKNOWN | S | 3 | İstatistik/ölçüm raporlama |
| 24 | **Lua** | ★ | Lua C API · LuaJIT **FFI** · sol2 (**düşük bakım**) | LuaJIT 5.801★ · sol2 v3.3.0 | UNKNOWN | **S** | **2** | **Eklenti/betik dili** (§7 desen 4) |
| 25 | **PowerShell** | ★ | `Add-Type` + P/Invoke | .NET yerleşik | UNKNOWN | S | 3 | Windows otomasyonu / paketleme |

### §3.1 Feasibility kırılımı (karar odaklı)

| Grup | Diller | Karar |
|---|---|---|
| **Faz 1 — çekirdek + Windows** | C · C++ · C# · Python | ABI + masaüstü UI + binding smoke (AK-7) |
| **Faz 2 — ikinci yüzey** | JS/TS · Go · Java · Kotlin · PHP · Rust · Lua | Web/uzak kontrol · CLI araç · kurumsal · **eklenti SDK** |
| **Faz 3 — platform/niş** | Swift · Dart · Objective-C · Zig · D · Haskell · OCaml · Scala · VB.NET · Ruby · Julia · R · PowerShell | Hedefe göre; **R/Julia/Lua/PowerShell/PHP/Ruby UI dili değildir** (matris §8.3) |
| **Asla UI olarak** | PHP · Ruby · Julia · R · Lua · PowerShell | ★ — rolü script/analiz/eklenti (matris §8.3) |

---

## §4 Kod Örnekleri (hepsi — TASLAK, derlenmedi)

> ⚠️ **Tüm bloklar önerilen şablondur.** Neva Player diskte `0 .cpp` (ADR-0105 §1). Ammaç: her FFI yolunun
> **gerçek sözdizimini** göstermek — "nasıl görüneceği" sorusunu kapatmak. Doğrulama §7.3 ölçüm kapısındadır.

### §4.1 C++ çekirdek tarafı (PIMPL — C++ dışarı sızmaz)

```cpp
// neva_session.cpp — çekirdek; public C yüzeyi neva_c.h
#include "neva_c.h"
#include <memory>
#include <string>

struct neva_session {                       // opaque (R4)
    struct Impl;
    std::unique_ptr<Impl> p;                // R2: tek gerçek nesne
};

extern "C" uint32_t neva_session_api_version(void) { return 0x00010000u; }

extern "C" neva_status_t neva_session_open(const char* uri,
                                           const neva_stream_params_t* in,
                                           neva_session_t** out)
{
    if (!uri || !out || !in)                 return NEVA_ERR_INVALID_ARG;
    if (in->struct_size < sizeof(*in))       return NEVA_ERR_INVALID_ARG;  // R3
    try {
        *out = new neva_session{ /* Impl::open(uri, *in) */ };
    } catch (...) {
        return NEVA_ERR_UNSUPPORTED_FORMAT;  // R5: istisna C'ye sızmaz
    }
    return NEVA_OK;
}
```

### §4.2 C# — P/Invoke (Windows UI hedefi)

```csharp
using System;
using System.Runtime.InteropServices;

internal static class Neva
{
    private const string Lib = "neva";

    // ⚠️ C'deki `size_t` C# `uint` DEĞİL — x64/ARM64'te 8 bayt.
    //    `nuint` mimari-genişliktedir; `uint` kullanılırsa struct kayar ve ABI BOZULUR.
    [StructLayout(LayoutKind.Sequential)]
    internal struct StreamParams
    {
        public nuint  StructSize;      // R3 — C: size_t (8 bayt x64)
        public uint   SampleRate;
        public ushort Channels;
        public ushort BitsPerSample;
        public byte   IsFloat;
        public unsafe fixed byte Reserved[7];
    }
    // static_assert karşılığı (§5.1): yalnız x64'te geçerli
    // System.Runtime.InteropServices.Marshal.SizeOf<StreamParams>() == 24 olmalı

    [DllImport(Lib, CallingConvention = CallingConvention.Cdecl)]
    internal static extern uint neva_session_api_version();

    [DllImport(Lib, CallingConvention = CallingConvention.Cdecl)]
    internal static extern int neva_session_open(
        [MarshalAs(UnmanagedType.LPUTF8Str)] string uri,
        in StreamParams p, out IntPtr session);

    [DllImport(Lib, CallingConvention = CallingConvention.Cdecl)]
    internal static extern int neva_session_read(
        IntPtr session, float[] buf, uint frames, out uint outFrames); // R6/R7: blittable, kopyasız
}
```

### §4.3 Python — ctypes (C ABI smoke, ADR §5.5) + pybind11 (üretim binding)

**(a) C ABI smoke — `ctypes` (ADR-0105 §5.5: \"İlk iki binding doğrulayıcısı: Python (ctypes, birincil) + C# (P/Invoke, ikincil)\")**

Sıfır bağımlılık, derleme yok → **`neva_c.h` sözleşmesini doğrulayan ilk kapı budur.** AK-7 bu.

```python
# smoke_abi.py — ADR §5.5 C ABI smoke (derlenmedi, taslak)
import ctypes as C

lib = C.WinDLL("neva")                     # .so/.dylib için C.CDLL
lib.neva_session_api_version.restype = C.c_uint32
lib.neva_session_api_version.argtypes = []

ver = lib.neva_session_api_version()       # R9: handshake
major = (ver >> 16) & 0xFFFF
if major != 1:
    raise RuntimeError(f"ABI uyuşmazlığı: beklenen major=1, gelen={major}")

class StreamParams(C.Structure):           # R3: struct_size ilk alan
    _fields_ = [("struct_size", C.c_size_t),
                ("sample_rate", C.c_uint32),
                ("channels", C.c_uint16),
                ("bits_per_sample", C.c_uint16),
                ("is_float", C.c_uint8),
                ("reserved", C.c_uint8 * 7)]

assert C.sizeof(StreamParams) == 24        # §5.1 hizalama denetimi (x64)

lib.neva_session_open.restype = C.c_int
lib.neva_session_open.argtypes = [C.c_char_p,
                                  C.POINTER(StreamParams),
                                  C.POINTER(C.c_void_p)]     # R4: opaque handle

p = StreamParams(); p.struct_size = C.sizeof(StreamParams)
p.sample_rate, p.channels, p.bits_per_sample, p.is_float = 192000, 2, 32, 1

h = C.c_void_p()
st = lib.neva_session_open(b"test.flac", C.byref(p), C.byref(h))
if st != 0:
    raise RuntimeError(f"open başarısız: {st}")             # R5: enum
# ... read/close ...
lib.neva_session_close(h)                                 # R4: tek serbest bırakıcı
```

**(b) Üretim binding — pybind11 (numpy doğrudan bellek adresi, kopyasız)**

```cpp
// neva_py.cpp — derleme: pip wheel
#include <pybind11/pybind11.h>
#include <pybind11/numpy.h>
#include "neva_c.h"
namespace py = pybind11;

static neva_session_t* g_handle = nullptr;   // örn.: gerçek sürümde nesne tutulur

PYBIND11_MODULE(_neva, m) {
    m.doc() = "Neva Player Python binding";
    m.def("abi_version", &neva_session_api_version);
    m.def("read", [](py::array_t<float, py::array::c_style | py::array::forcecast> a,
                     uint32_t frames) {
        auto buf = a.request();                       // R6: doğrudan bellek adresi
        uint32_t got = 0;
        auto st = neva_session_read(g_handle, buf.ptr, frames, &got);
        if (st != NEVA_OK) throw std::runtime_error(neva_last_error());  // R5
        return got;                                   // kopya yok
    }, py::arg("array"), py::arg("frames"));
}
```

### §4.4 Rust — bindgen + güvenli sarmalayıcı

```rust
// build.rs — C header'dan binding üretir (bindgen 0.73.2)
fn main() {
    println!("cargo:rustc-link-search=native=../build");
    println!("cargo:rustc-link-lib=dylib=neva");
    let b = bindgen::Builder::default().header("../include/neva_c.h");
    b.generate().expect("bindgen").write_to_file(out).unwrap();
}
```
```rust
// lib.rs — safe wrapper (R2: C++ görünmez)
include!(concat!(env!("OUT_DIR"), "/bindings.rs"));

pub struct Session { raw: *mut neva_session_t }        // R4: opaque
impl Session {
    pub fn open(uri: &str) -> Result<Self, neva_status_t> {
        let c = std::ffi::CString::new(uri).unwrap();
        let mut p = neva_stream_params_t { struct_size: std::mem::size_of::<_>() as _, ..Default::default() };
        let mut h = std::ptr::null_mut();
        let st = unsafe { neva_session_open(c.as_ptr(), &p, &mut h) };
        if st == neva_status_t::NEVA_OK { Ok(Self { raw: h }) } else { Err(st) }
    }
}
impl Drop for Session {
    fn drop(&mut self) { unsafe { neva_session_close(self.raw) } }   // tek serbest bırakıcı
}
```

### §4.5 Go — cgo (yalnız C!)

```go
package neva

/*
#cgo LDFLAGS: -L${SRCDIR}/../build -lneva
#include "neva_c.h"        // C++ header DEĞİL — cgo C destekler (matris §5.2)
*/
import "C"

func AbiVersion() uint32 { return uint32(C.neva_session_api_version()) }

func Open(uri string) (C.neva_session_t, C.neva_status_t) {
    c := C.CString(uri); defer C.free(unsafe.Pointer(c))
    var p C.neva_stream_params_t
    p.struct_size = C.size_t(unsafe.Sizeof(p))
    var h *C.neva_session_t
    st := C.neva_session_open(c, &p, &h)
    return *h, st      // ⚠️ TASLAK: gerçek kod hata/özellik sarmalayıcısı ekler
}
```

### §4.6 Java — JNA (kodsuz) / JEP 454 FFM

```java
// JNA (5.19.1) — kodsuz; prototip/araç için
import com.sun.jna.*;

public interface Neva extends Library {
    Neva INSTANCE = Native.load("neva", Neva.class);
    int    neva_session_api_version();
    int    neva_session_open(String uri, StreamParams p, PointerByReference out);
}
```
```java
// Üretim adayı: JEP 454 FFM (JDK 22) — daha hızlı, güvenli bellek modeli
import java.lang.foreign.*;
import static java.lang.foreign.ValueLayout.*;
try (Arena a = Arena.ofConfined()) {
    Linker linker = Linker.nativeLinker();
    MethodHandle abi = linker.downcallHandle(
        linker.defaultLookup().find("neva_session_api_version").orElseThrow(),
        FunctionDescriptor.of(JAVA_INT));
    int v = (int) abi.invokeExact();
}
```

### §4.7 Dart — `dart:ffi` + ffigen (Flutter hedefi)

```dart
import 'dart:ffi';

final dylib = DynamicLibrary.open('neva.dll');   // .so / .dylib platforma göre
typedef _AbiC = Uint32 Function();
typedef _AbiD = int Function();
final abi = dylib.lookupFunction<_AbiC, _AbiD>('neva_session_api_version');

typedef _OpenC = Int32 Function(Pointer<Utf8> uri, Pointer<StreamParams> p,
                                Pointer<Pointer<NevaSession>> out);
// ffigen: header'dan otomatik üretir (dart-lang/native, BSD-3)
```

### §4.8 JavaScript / TypeScript — Emscripten WASM + N-API

```c
/* neva_web.c — Emscripten dışa aktarımı (Aşama 2) */
#include "neva_c.h"
#include <emscripten/emscripten.h>

EMSCRIPTEN_KEEPALIVE
uint32_t neva_session_api_version_js(void) { return neva_session_api_version(); }
```
```ts
// TS — hi-res exclusive iddiası YOK (matris §6.3); yalnız işleme/dinleme
const Module = await createNevaModule();
const abi: number = Module._neva_session_api_version_js();
// Ses: AudioWorklet (128 kare, Float32Array) + SharedArrayBuffer ring (COOP/COEP şart)
```

### §4.9 Kotlin/Native — cinterop (Android hedefi)

```
// neva.def — cinterop tanımı (resmi yol)
headers = neva_c.h
headerFilter = neva_c.h
package = com.neva.core
compilerOpts = -I../include
linkerOpts = -lneva
```
```kotlin
import com.neva.core.neva_session_api_version
import com.neva.core.neva_session_open
val v: UInt = neva_session_api_version()
```

### §4.10 Swift — C++ interoperability (5.9+)

```swift
// C ABI header modül haritası ile import edilir (Clang importer)
import neva_c
let v = neva_session_api_version()                 // doğrudan, ara katman yok
// C++ tipleri için Swift 5.9+ interop — yalnız C++ sınıf/struct (matris B2)
```

### §4.11 Küçük diller (tek satır kanıt — hepsi C ABI çeker)

```php
// PHP FFI (php.ini'de etkin olabilir → varsayılan UNKNOWN)
$ffi = FFI::cdef("uint32_t neva_session_api_version(void);", "neva.dll");
echo $ffi->neva_session_api_version();
```
```lua
-- LuaJIT FFI (eklenti/betik dili — §7 desen 4)
local ffi = require("ffi")
ffi.cdef[[ uint32_t neva_session_api_version(void); ]]
local neva = ffi.load("neva")
```
```powershell
# PowerShell — yalnız otomasyon
Add-Type -TypeDefinition 'using System.Runtime.InteropServices; public class N {
  [DllImport("neva")] public static extern uint neva_session_api_version(); }'
[N]::neva_session_api_version()
```
```ruby
# Ruby ffi gem (BSD-3)
require 'ffi'
module Neva; extend FFI::Library; ffi_lib 'neva'
  attach_function :neva_session_api_version, [], :uint32; end
```
```julia
# Julia ccall — Cxx.jl ARCHIVED (C++ doğrudan yol bakımsız)
ccall((:neva_session_api_version, "neva"), UInt32, ())
```
```r
# R .Call + Rcpp (GPL-2.0 ⚠️ lisans dikkat)
.Call("neva_session_api_version", PACKAGE = "neva")
```
```d
// D — extern(C++) ile doğrudan C++ (matris B2)
extern(C) uint neva_session_api_version();
```

---

## §5 Statik Analiz (derleme · ABI · paketleme)

### §5.1 Sembol ve ABI yüzeyi

| Kontrol | Yöntem | Neden |
|---|---|---|
| **Görünürlük** | `-fvisibility=hidden` + `NEVA_API` yalnız `neva_*` için `default` | Yanlış sembol sızıntısını engeller; paket boyutunu düşürür |
| **Windows export** | `.def` dosyası (yalnız `neva_*`) veya `__declspec(dllexport)` üretici tarafı | C++ mangling'inin dışarı sızmaması (R2) |
| **C++ dışarı sızıntısı yok** | `dumpbin /exports` veya `nm -D --defined-only` → `_ZN*`/`?` kalıbı **0 olmalı** | Aksi halde her dilde ABI kırılır |
| **İstisna sızıntısı yok** | `-fno-exceptions` seçeneği değerlendirilir; her public sınırda `catch(...)` | C tarafında `terminate` riski (§4.1) |
| **RTTI/RT sembolleri** | PIMPL → public başlıkta `typeid`/`dynamic_cast` yok | ABI kararlılığı |
| **struct hizalama** | `static_assert(sizeof(neva_stream_params_t) == 24)` **x64/ARM64'te** (size_t 8 + u32 + u16 + u16 + u8 + 7×u8) | Farklı dil/mimari (x64/ARM64) arasında byte düzeni — C# `nuint`, Rust `usize`, Go `C.size_t` hepsi mimari-genişliktir |
| **`struct_size` denetimi** | Her open'da `in->struct_size >= sizeof(...)` | R3 — eski struct'lı tüketici |

### §5.2 Paketleme matrisi (dil başına dağıtım birimi)

| Dil | Dağıtım birimi | Ekleme gereken | Statik risk |
|---|---|---|---|
| C/C++ | `neva.dll` / `.so` / `.dylib` + başlık | — | **Birincil üretilen** |
| C# | NuGet (`neva.runtime.win-x64`) | runtimes/{rid}/native/ | RID/натive eşleşmesi |
| Python | wheel (`pybind11`) — `manylinux`/`win_amd64` | `.so/.pyd` + `_neva` | wheel başına CPython ABI (cp3x) |
| Rust | `crates.io` crate (bindgen üretimini `build.rs` çalıştırır) | link search + lib | bindgen sürümü ↔ header uyumu |
| Java | JAR + `jna`/FFM | `.dll/.so` yolu | Mimari (x64/ARM64) + `Native.load` yolu |
| Kotlin/Native | `.klib` + cinterop | `.def` | Native hedefe özel derleme |
| Go | cgo (derleme anında) | C header + lib | `CGO_ENABLED` (kapalıysa **imkânsız**) |
| Dart/Flutter | `dart:ffi` + ffigen | paket içinde native lib | Platform klasör yapısı |
| JS/TS | Emscripten `.wasm` + glue | COOP/COEP başlıkları | SharedArrayBuffer izolasyonu |
| PHP | sunucu-üstünde `.dll` (FFI) | `ffi.enable=true` | **Dağıtıma göre kapalı olabilir (UNKNOWN)** |
| Ruby / Lua / Julia / R | gem / rockok / paket / CRAN | lib yolu | Yalnız araç sınıfı |

### §5.3 ABI sürümleme (R9)

```text
neva_session_api_version() -> 0x00MMmmpp  (major.minor.patch)
major değişimi  → tüketici AÇILIŞTA NEVA_ERR_VERSION alır (asla sessiz çökme)
minor ekleme    → struct_size ile geriye uyumlu (R3)
patch           → davranış düzeltmesi, sembol yok
```

---

## §6 Runtime Analiz (çalışma zamanı davranışı)

### §6.1 Veri ve ömür modeli (bağlayıcı)

| Konu | Kural | Kırılırsa |
|---|---|---|
| **Veri yolu** | Ham örnekleme **yalnız C++ tamponunda**; dile pointer+uzunluk (R6/R7) | GC basıncı + kopya → underrun |
| **Ömür** | `neva_session_close` **tek** serbest bırakıcı (R4); dil GC'si asla çekirdek nesneyi free etmez | Çift free / use-after-free |
| **Dil GC nesnesi** | C++ yalnız **dilin tuttuğu handle'ı** saklar (ref-count veya `shared_ptr` değil — handle) | GC ayıklarsa UAF |
| **Kuyruk** | Blok-dışı SPSC/MPSC kuyruk; dil yalnız **komut/durum/olay** yollar | RT thread'inde dil çalışır → jitter |

### §6.2 Çalışma zamanı risk tablosu (dil → risk → kural)

| Dil | Runtime riski | Kural |
|---|---|---|
| **C/C++** | Yok (aynı süreç) | RT yolun kendisi |
| **Python** | **GIL** + GC → blok-bazlı, GIL tutulmadan bekleme | `read` GIL'siz kuyruktan okur |
| **C#** | GC (Gen2 sıçraması) | `Span<T>`/`NativeMemory`; **RT callback yok**; `GC.Collect` tetiklenmez |
| **Java/Kotlin (JVM)** | GC + JIT ısınma sıçraması | **RT yol JVM'de asla**; sadece komut/olay |
| **Go** | **cgo ~40 ns/çağrı** (doğrulanmış tek sayı) | Blok-bazlı → ihmal edilebilir; `runtime.LockOSThread` gerekebilir |
| **JS/TS** | Event-loop + GC | AudioWorklet ayrı thread; **RT callback ana thread'e değil** |
| **Dart** | GC + isolate geçişi | `Isolate` sınırında kopya → blok-bazlı |
| **Rust** | Yok (drop deterministik) | `unsafe` sınırı tek yerde |
| **PHP/Ruby/Lua/Julia/R** | GC / sıçrama | **Yalnız betik/araç** — RT yok |
| **WASM** | Gerçek-zamanlı thread sınırlı | Ring + SharedArrayBuffer (COOP/COEP) |

### §6.3 Bağlayıcı RT kuralı (❌ yasaklar)

1. **Ses thread'inden dil runtime'ına geri çağırma YASAK** (C→Python/JVM/.NET/JS/GC).
   → Çözüm: dil yalnız **komut yazar**, çekirdek **poll** eder (lock-free kuyruk).
2. **Dil GC'sini RT thread'inde tetiklemek YASAK.**
3. **Örnekleme kopyasını dil belleğine almak YASAK** (R6) — istisna: yalnız kayıt/dışa aktarma yolu (non-RT).
4. **FFI çağrısını örneklem başına yapmak YASAK** (R7) — §7.1 hesabı.

---

## §7 Performans Bütçesi ve Ölçüm Protokolü

### §7.1 Blok granülaritesi hesabı (DERIVED — ölçüm değil, matris B5/§4.1)

| Senaryo | Çağrı/sn @192 kHz | Maliyet (cgo 40 ns) | Yutma |
|---|---|---|---|
| **Örneklem başına** (1 kare) | 192.000 | 7,68 ms/sn | **~%0,77** |
| **128 kare blok** (≈0,67 ms) | 1.500 | 60 µs/sn | ~%0,006 |
| **1024 kare blok** | 187,5 | 7,5 µs/sn | ~%0,00075 |
| C/C++ doğrudan | — | ~%0 | 0 |

> **Çıkarım:** FFI maliyeti, çağrı **sayısı** ile değil, **granülarite** ile ölçeklenir.
> Dolayısıyla binding API'si **zorunlu olarak blok-bazlı** tasarlanmalıdır (R7) — bu bir tercih değil, bütçe kuralıdır.

### §7.2 Bütçe (TASLAK — ADR-0105 §2.10 gecikme bütçesine bağlı)

| Kalem | Hedef | Not |
|---|---|---|
| FFI geçişi (blok-bazlı) | ≤ %0,01 blok süresi | §7.1 uyarınca |
| Kopya baytı | **0** (paylaşılan tampon) | R6 |
| Dil tarafı GC etkisi | RT yolu **0** (dil RT'de değil) | §6.3 |

### §7.3 Ölçüm kapısı (UNKNOWN'ları kapatan tek yol)

| # | Ne ölçülür | Araç | Kapattığı UNKNOWN |
|---|---|---|---|
| M1 | C ABI çağrı süresi (çağrı başına ns) + blok başına | **Google Benchmark** / BenchmarkDotNet | U1, U3 |
| M2 | pybind11 **vs** nanobind kopya/gecikme | Python `timeit` + çekirdek sayaç | U2 |
| M3 | P/Invoke blittable vs marshalling | BenchmarkDotNet | U3 |
| M4 | cgo ~40 ns teyidi (Neva ABI'sı üzerinde) | Go `testing.B` | matris §4 teyidi |
| M5 | WASM ↔ AudioWorklet gecikme (`baseLatency`/`outputLatency`) | Tarayıcı içi ölçüm | U4 |
| M6 | Null-test (AK-1) + THD+N (AK-2) — **binding üzerinden** | Bit-perfect null-test | AK-7 FFI smoke |

**Kapı kuralı:** M1–M6 tamamlanmadan **"FFI overhead kabul edilebilir" ifadesi yazılamaz**; yazılan her oran `DERIVED` olarak etiketlenir.

---

## §8 Faz Planı

| Faz | Kapsam | Çıktı | Kapı |
|---|---|---|---|
| **Faz 1** | `neva_c.h` (R1–R9) + C++ çekirdek sınırı + **C# UI** + **Python binding** | ABI başlığı + `neva.dll` + wheel + NuGet taslağı | **AK-7 / ADR §5.5 C ABI smoke** (Python **ctypes** + C# P/Invoke) geçmeli |
| **Faz 2** | **JS/TS** (WASM, Aşama 2) · **Go** CLI araç · **Java** (JEP 454) · **Kotlin/Android** · **PHP** (sunucu) · **Rust** sarmalayıcı · **Lua** eklenti SDK | 2. yüzey + eklenti SDK | §7.3 M1–M4 ölçümü |
| **Faz 3** | Swift · Dart/Flutter · Objective-C · Zig · D · Haskell · OCaml · Scala · VB.NET · Ruby · Julia · R · PowerShell | Platform/niş talep geldikçe | Talep-bağlı |
| **Her faz** | §5.3 ABI sürümleme + §9 UNKNOWN takibi | — | **UNKNOWN sayısı azalmadan faz kapanamaz** |

---

## §9 Riskler ve UNKNOWN'lar

| # | Konu | Durum | Kapanma yolu |
|---|---|---|---|
| U1 | **25 dilin 20'si için FFI overhead değeri** | UNKNOWN (yayım yok) | §7.3 M1/M4 |
| U2 | pybind11 vs nanobind hız farkı | UNKNOWN (karşılaştırmalı ölçüm doğrulanmadı) | M2 |
| U3 | JNA/JNI/FFM/P-Invoke sayısal overhead | UNKNOWN | M3 |
| U4 | WASM ↔ AudioWorklet gecikme | UNKNOWN | M5 |
| U5 | PHP FFI `php.ini` varsayılan etkinliği | UNKNOWN | hedef sürüm `php -i` |
| U6 | Rice / JNR-FFI / scala-native / Emscripten / inline-c lisans SPDX | **NOASSERTION → UNKNOWN** | depo LICENSE okuma |
| U7 | UI çatılarının (Qt/WPF/Flutter/egui/Tauri/Slint) sürüm/yıldızları | UNKNOWN | ayrı UI araştırması |
| U8 | Zig upstream (GitHub *Moved to Codeberg*) | UNKNOWN | Codeberg kontrolü |
| U9 | `autocxx`/`Cxx.jl` arşivinin yerine geçecek yol (Diplomat 923★) | ORTA güven | Diplomat olgunluk incelemesi |
| **R-1** | **Lisans:** `SWIG` **GPL-3.0** · `Rcpp` **GPL-2.0** · `JavaCPP` Apache/GPL dual · `JNA` LGPL-2.1+ **veya** Apache-2.0 · `uniffi` MPL-2.0 | **Yüksek güven (matris B8)** | Tescilli/özel bileşenle dağıtımda **hukuki inceleme** (ADR-0105 §5.9 ruhu) |
| **R-2** | **Bakım:** `autocxx` ARCHIVED · `Cxx.jl` ARCHIVED · `sol2` düşük aktivite | Yüksek güven | Kullanım listesinden düşür (autocxx) / alternatif (Diplomat) |
| **R-3** | **cgo `CGO_ENABLED=0`** → Go hedefi **imkânsız** | Yüksek | Dağıtımda cgo zorunlu olduğunu belgele |
| **R-4** | **WASM "hi-res" yanılgısı** | Yüksek (matris §6.3) | İddia yalnız native sürümlerde (ADR-0105 bit-perfect) |

---

## §10 Kaynaklar

**Birincil (bu analizin dayanağı):**
- [[neva-player-dil-uyumluluk-matrisi]] — 25 dil · Matris A/B · §4 overhead · §5 dil değerlendirmesi · §6 WASM · §7 desenler · §9 UNKNOWN · §10 kaynak listesi (2026-10-19 GitHub REST / PyPI / crates.io doğrulamalı)
- [[media-stack-web-verification]] — 112 kaynak medya-yığını doğrulaması
- [[neva-player-media-stack]] — 69 kaynak
- ADR-0105 (`v1.4.0`) — C ABI FFI kararı (§50 matris satırı: *"tek C ABI yüzeyi (`neva_session_*`) - sürüm sözleşmesi 2.6"*; §5.5 **C ABI smoke = Python ctypes + C# P/Invoke**; §5.9 lisans denetimi `--enable-gpl` + `--enable-version3`)
- `architecture/neva-player/index.md` (`v1.1.7`) §4 **"Dil/FFI Stratejisi (matris özeti: 25 dil, ★★★=11)"**

**Seçilmiş birincil dokümanlar (matris §10.2–§10.3'ten):** OpenJDK JEP 454 (Release 22) ·
Swift C++ interop (5.9+) · Kotlin/Native `cinterop` · Go `cmd/cgo` · Dart FFI · PHP FFI sınıfı ·
Zig `@cImport` · D `extern(C++)` · Julia `ccall` · R-exts · LuaJIT FFI · GHC FFI · OCaml C interop ·
.NET P/Invoke · W3C Web Audio 1.1 · W3C Audio Output Devices API · MDN AudioWorklet.

---

**Authority:** Embedded Engineer (L1) · **Statü:** ANALİZ (TASLAK kod içerir)
**Sürüm geçmişi:** 1.0.0 (2026-10-20) — ilk yazım: 25 dil FFI + feasibility + statik/runtime/kod-örnek "hepsi".
