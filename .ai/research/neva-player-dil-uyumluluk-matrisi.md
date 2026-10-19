---
title: 'Neva Player — 25 Programlama Dili Uyumluluk Matrisi (C++20 çekirdek + UI fizibilitesi)'
type: research
status: active
date: 2026-10-19
---

# Neva Player — Programlama Dili Uyumluluk Matrisi

> **Kapsam:** Neva Engine (C++20, hi-res ses 32b/192kHz + 4K video) çekirdek kabul edilir; 25 dil için (a) çekirdeğe erişim yolu (FFI/binding), (b) binding olgunluğu, (c) performans overhead, (d) o dilde player arayüzü yazılıp yazılamayacağı, (e) öneri seviyesi incelenir.
> **Kanıt tarihi:** Tüm sürüm/yıldız/lisans verileri **2026-10-19** tarihinde GitHub REST API (v3), PyPI JSON API, crates.io API ve doğrudan HTTP GET/HEAD ile toplanmıştır. Yıldız sayıları o anlık değerdir (zamanla değişir).
> **Zero-hallucination:** Doğrulanamayan her değer **UNKNOWN** veya **bilinmiyor** ile işaretlenmiştir. Uydurma sürüm/yıldız/URL yoktur. Çerçeve adları (Qt, WPF, JavaFX, Flutter, egui, Tauri, Slint, Electron vb.) yalnızca tanım amaçlıdır — bu araştırmada sürüm/yıldızları doğrulanmamıştır (UNKNOWN).

---

## 0. Metodoloji ve Okuma Kılavuzu

### 0.1 Öneri seviyesi tanımı

| Seviye | Anlamı |
|---|---|
| **★★★** | Çekirdek erişimi düşük maliyetli/olgun **ve** o dilde player UI yazımı pratik, üretim kalitesinde yaygın. |
| **★★** | Çekirdek erişimi var (FFI sağlam) **ama** o dilde player UI pratik değil: niş UI kitleri, platform kısıtı ya da dilin UI'da nadiren tercih edilmesi. |
| **★** | Teknik olarak erişim mümkün, ancak önerilen kullanım **yalnız script/araç/eklenti/sunucu**; UI önerilmez. |

### 0.2 Güven (confidence) notasyonu

- **Yüksek:** resmi dokümantasyon, spesifikasyon, paket kayıt defteri (PyPI/crates.io) veya GitHub API doğrulaması.
- **Orta:** ikinci el benchmark/blog, tek kaynaklı ölçüm.
- **Düşük:** topluluk yorumu/tek gönderi.
- **UNKNOWN:** doğrulanamadı → yazılmadı.

### 0.3 Toplanan doğrulanmış özet veri (2026-10-19)

- pybind11 **3.1.0** (PyPI, BSD-3-Clause, 18.025★) · nanobind **3.1.0** (PyPI, BSD-3-Clause, 3.748★) · Cython **3.3.0** (Apache-2.0, 10.870★)
- cxx **1.0.205** (crates.io, Apache-2.0, 6.844★) · bindgen **0.73.2** (crates.io, BSD-3-Clause, 5.301★) · autocxx 0.30.0 (2.562★) → **archived/arşivlenmiş**
- uniffi **0.32.2** (crates.io, MPL-2.0, 5.038★; resmi hedefler: Kotlin, Swift, Python, Ruby — 3. taraf: C#, Go)
- SWIG son etiket **v4.5.1** (6.330★; PyPI metadata: GPL-3.0-or-later) · CppSharp sürüm **v1.2** (2025-11-19, MIT, 3.403★) · ClangSharp (MIT, 1.255★) · CsWinRT (MIT, 668★)
- JNA **5.19.1** (README; LGPL-2.1+ veya Apache-2.0, 8.926★) · JavaCPP **1.5.14** (2026-08-10; Apache-2.0 **veya** GPL-2+Classpath, 4.705★) · JNR-FFI 1.327★ (lisans UNKNOWN)
- JEP **454** (Foreign Function & Memory API) → **Release: 22**, Status: Closed/Delivered (openjdk.org/jeps/454)
- Emscripten 27.727★ · wasm-bindgen 9.193★ (Apache-2.0) · node-addon-api 2.411★ (MIT) · ffmpeg.wasm 17.890★ (MIT) · GoogleChromeLabs/web-audio-samples 756★ (Apache-2.0)
- Cxx.jl 762★ → **archived** (son push 2024-05) · JuliaInterop/Clang.jl 243★ (MIT, aktif) · Rcpp 797★ (**GPL-2.0**) · yallop/ocaml-ctypes 413★ (MIT, son push 2025-11)
- sol2 **v3.3.0** (release 2022-06-25, MIT, 5.160★; depo son commit 2025-03 → düşük aktivite) · LuaJIT 5.801★ · ffi (Ruby) 2.128★ (BSD-3-Clause) · Rice 415★
- Zig GitHub aynası 43.292★ (MIT) ve depo açıklaması **Moved to Codeberg** (upstream taşınmış) · dmd 3.329★ (BSL-1.0) · ghc 3.275★ · scala-native 4.696★ (lisans API: NOASSERTION → UNKNOWN) · ocaml 6.619★

---

## 1. Özet Bulgular

| # | Bulgu | Güven |
|---|---|---|
| B1 | Neva Engine için **tek kamuya açık sözleşme C ABI** olmalıdır: 25 dilin tamamı C ABI'ye erişir; C++ başlıklarını doğrudan dışa açmak yalnız C++ (ve kısmen Swift/Objective-C++) için anlamlıdır. | Yüksek |
| B2 | **Doğrudan C++ erişimi** yalnızca 4 dilde doğal: C++ (aynı süreç), Objective-C++ (.mm), Swift (C++ interop, 5.9+), D (`extern(C++)`). Zig bile C++ header'ı import **edemez** — C ABI sarmalayıcı gerekir. | Yüksek |
| B3 | Otomatik binding üreticileri olgunlaştı: C++→Python (pybind11/nanobind), C++→C# (CppSharp v1.2), C→Rust (bindgen 0.73.2), C→Java (JNA 5.19.1 / JEP 454 FFM), C→Dart (ffigen). Bakım riski taşıyanlar: **autocxx (archived)**, **Cxx.jl (archived)**, **sol2 (düşük aktivite)**. | Yüksek |
| B4 | **Performans overhead'i için yayımlanmış, Neva benzeri iş yüküne ait ölçüm bulunmamaktadır** — 25 dilin 20'si için değer bilinmiyor. Tek doğrulanmış sayı ailesi cgo'dur (~40 ns/çağrı). | Yüksek (eksikliğin kendisi) |
| B5 | Overhead, çağrı **granülaritesine** bağlıdır: ses iş yükünü blok başına (128 kare ≈ 0,67 ms @192kHz) tek çağrıya indirirseniz FFI maliyeti ihmal edilebilir; **örneklem başına** çağrı yaparsanız (192.000 çağrı/sn) yalnız 40 ns'lik bir maliyet bile ~%0,77 yutar. → Binding API'si **blok/paket bazlı** tasarlanmalı. | Orta (mühendislik hesabı; ölçüm değil) |
| B6 | Tarayıcı/WASM hedefi **işleme için** uygundur (Web Audio 32-bit float + AudioWorklet), **exclusive/hi-res çıktı için değildir**: W3C Web Audio 1.1 + Audio Output Devices API yalnız `setSinkId` (cihaz seçimi) sunar; DAC'ı exclusive modda devralma/örneklem hızını zorlama API'si tanımlı değildir. | Yüksek |
| B7 | UI tarafında öne çıkanlar: **C++ (Qt tarzı masaüstü)**, **C# (Windows masaüstü)**, **Kotlin (Android)**, **TypeScript (web)**, **Dart (Flutter cross-platform)**, **Swift (Apple)**, **Python (hızlı masaüstü/araç UI)**. | Orta |
| B8 | Lisans dikkat noktaları: **Rcpp = GPL-2.0**, **SWIG = GPL-3.0**, **JavaCPP = Apache-2.0 veya GPL-2+CE (dual)**, **JNA = LGPL-2.1+ veya Apache-2.0**, **uniffi = MPL-2.0**, **autocxx = arşivli**. Tescilli Neva çekirdeği ile dağıtımda hukuki değerlendirme gerekir. | Yüksek |

---

## 2. Matris A — Çekirdek Erişim Mekanizması ve Binding Olgunluğu

| Dil | Binding mekanizması (güncel/doğru) | Binding aracı: ad · sürüm · lisans · GitHub ★ (2026-10-19) | Olgunluk |
|---|---|---|---|
| **C** | Doğrudan statik/dinamik link (C ABI) | — (standart; araç gerekmez) | Olgun (standart) |
| **C++** | Doğrudan, aynı süreç (static lib / DLL + PIMPL) | — | Olgun (standart) |
| **Rust** | C ABI + `bindgen` (C header); C++ tip/geçiş için `cxx` | bindgen **0.73.2** · BSD-3 · **5.301★** · cxx **1.0.205** · Apache-2.0 · **6.844★** · autocxx 0.30.0 · Apache-2.0 · 2.562★ → **ARCHIVED** | Olgun (autocxx hariç) |
| **Python** | C++ extension (header-only C++ binding) veya yerleşik ctypes/cffi | pybind11 **3.1.0** · BSD-3 · **18.025★** · nanobind **3.1.0** · BSD-3 · **3.748★** · Cython **3.3.0** · Apache-2.0 · **10.870★** | Olgun (en olgun sınıf) |
| **C#** | P/Invoke (`[DllImport]`, C ABI); header'dan otomatik üretici | CppSharp **v1.2** (2025-11-19) · MIT · **3.403★** (NuGet: CppSharp 1.1.84.17100) · ClangSharp · MIT · **1.255★** · CsWinRT · MIT · **668★** (WinRT) · SWIG C# | Olgun |
| **Java** | JNI (JDK yerleşik) · **FFM API (JEP 454, JDK 22)** · JNA (kodsuz) | JNA **5.19.1** · LGPL-2.1+/Apache-2.0 · **8.926★** · JavaCPP **1.5.14** (2026-08-10) · Apache-2.0 veya GPL-2+CE · **4.705★** · JNR-FFI · **1.327★** (lisans UNKNOWN) · JNAerator **529★** (düşük aktivite) | Olgun (araç dağınıklığı var) |
| **Kotlin** | JVM'de JNI/FFM (Java ile aynı); Kotlin/Native'de **cinterop**; Rust çekirdeği ise **uniffi** | Kotlin/Native `cinterop` (resmi) · uniffi **0.32.2** · MPL-2.0 · **5.038★** (Kotlin destekli) | Olgun |
| **Go** | **cgo** (yalnız C; C++ için C ABI sarmalayıcı zorunlu) | stdlib `cmd/cgo` (resmi) | Olgun (C++ için ek katman şart) |
| **Swift** | **C++ interoperability (Swift 5.9+)**; ayrıca C ABI (Clang importer) | swiftlang/swift · Apache-2.0 · **70.514★** (dil deposu) | Olgun (Apple platformu) |
| **Objective-C** | **Objective-C++** (`.mm`) — C++'ı ara katman olmadan doğrudan çağırır | Clang (yerleşik) | Olgun (legacy tercih) |
| **JavaScript** | WASM (Emscripten) veya Node **N-API** C++ addon | Emscripten · **27.727★** (lisans API: NOASSERTION → UNKNOWN) · node-addon-api · MIT · **2.411★** · wasm-bindgen (Rust) · Apache-2.0 · **9.193★** | Olgun |
| **TypeScript** | JavaScript ile aynı mekanizma + statik tip katmanı | (JS araçları kullanılır) | Olgun |
| **Dart** | `dart:ffi` (yerleşik) + **ffigen** (header→binding üretici); JNI için `jni` paketi | dart-lang/native (ffigen/jni mono-repo) · BSD-3 · **277★** · `api.dart.dev/stable/dart-ffi` | Olgun |
| **PHP** | **PHP FFI** (PHP 7.4+ çekirdeğe yerleşik; C ABI, kod üretimi yok) | php-src yerleşik FFI · **php/php-src 40.446★** | Orta (dağıtıma göre FFI kapalı olabilir → varsayılan UNKNOWN) |
| **Ruby** | **ffi** gem (C ABI) veya **Rice** (C++ extension derleyicisi) | ffi · BSD-3 · **2.128★** · Rice · lisans API: NOASSERTION → UNKNOWN · **415★** | Orta |
| **Zig** | **`@cImport`** ile C header doğrudan; **C++ header DESTEKLENMEZ** → C ABI şart | Zig (GitHub aynası **43.292★**, MIT, depo: *Moved to Codeberg* → upstream Codeberg) | Olgun (hosting taşınmış) |
| **D** | `extern(C)` + **`extern(C++)`** ile C++ sınıf/struct çağrısı (dmd/ldc) | dmd · BSL-1.0 · **3.329★** · spec: Interface to C++ | Orta (C++ kapsamı spec kısıtlarına tabi) |
| **Julia** | Yerleşik **`ccall`** (C ABI) + otomatik binding üretici | Clang.jl · MIT · **243★** (aktif) · Cxx.jl · **762★** → **ARCHIVED** (son push 2024-05) | Orta (C++ doğrudan yol: bakımsız) |
| **R** | `.Call` / C API (R-exts) + **Rcpp** | Rcpp · **GPL-2.0** · **797★** | Olgun (lisans riski: GPL) |
| **Lua** | Yerleşik **Lua C API**; LuaJIT'de **FFI** (C ABI doğrudan); gömme için sol2 | sol2 **v3.3.0** (2022-06-25) · MIT · **5.160★** (düşük aktivite) · LuaJIT **5.801★** · lua/lua **10.384★** | Orta/düşük bakım |
| **Haskell** | Yerleşik FFI (`foreign import`) + `inline-c` | inline-c · **307★** · lisans UNKNOWN · ghc/ghc · **3.275★** | Orta |
| **OCaml** | Yerleşik C FFI (*Interfacing C with OCaml*) + ctypes | ocaml-ctypes · MIT · **413★** (son push 2025-11) · ocaml/ocaml · **6.619★** | Orta |
| **Scala** | JVM: JNI/**FFM**/JNA (Java ile aynı); **Scala Native** ile C FFI | scala-native · **4.696★** · lisans API: NOASSERTION → UNKNOWN | Orta |
| **VB.NET** | P/Invoke (`Declare` / `[DllImport]`) — .NET yerleşik | .NET yerleşik (`learn.microsoft.com/.../native-interop/pinvoke`) | Olgun (modern projede C# tercih) |
| **PowerShell** | `Add-Type` ile C# snippet derleyip P/Invoke | .NET yerleşik (`Add-Type` dokümantasyonu) | Yalnız araç |

---

## 3. Matris B — UI Fizibilitesi ve Öneri Seviyesi

| Dil | (a) Çekirdek erişimi | (b) O dilde player UI pratik mi? | (c) Tipik kullanım senaryosu (Neva bağlamında) | ★ |
|---|---|---|---|---|
| **C** | Evet (doğrudan) | Yazılabilir (GTK/Win32) ama modern widget verimi düşük | Çekirdek katmanı, ABI dili, FFmpeg tarzı alt sistem | ★★★ |
| **C++** | Evet (doğrudan) | **Evet — en pratik** (masaüstü UI çatıları) | Çekirdek + masaüstü player UI (tek dilde uçtan uca) | ★★★ |
| **Rust** | Evet (bindgen/cxx) | Evet (niş ama üretimde yaygınlaşan masaüstü/web-kabuk UI) | Güvenli sarmalayıcı, CLI/araçlar, masaüstü kabuk | ★★★ |
| **Python** | Evet (pybind11/nanobind) | Evet (masaüstü UI çatıları; GIL + dağıtım paketleme dikkat) | Betik, otomasyon, test, DSP prototipi, eklenti | ★★★ |
| **C#** | Evet (P/Invoke + üreticiler) | **Evet — Windows masaüstünde en pratik** | Windows player UI (WPF/WinForms/WinUI/Avalonia) | ★★★ |
| **Java** | Evet (JNI/FFM/JNA) | Evet (masaüstü UI çatıları); modern his orta | Cross-platform masaüstü, kurumsal araç UI | ★★★ |
| **Kotlin** | Evet (cinterop/JVM) | **Evet — Android'de birincil tercih** | Android player UI (+ masaüstünde Kotlin UI çatıları) | ★★★ |
| **Swift** | Evet (C++ interop 5.9+) | Evet (Apple platformda) | iOS/macOS player UI; Windows/Linux'ta olgun değil | ★★★ |
| **JavaScript** | Evet (WASM/N-API) | **Evet — web UI'ın dili** | Web player UI, masaüstü kabuk (webview tabanlı) | ★★★ |
| **TypeScript** | Evet (JS ile aynı) | **Evet — web UI'da tip güvenliği** | Web player UI, uzak kontrol paneli | ★★★ |
| **Dart** | Evet (dart:ffi + ffigen) | **Evet — Flutter ile tek kod tabanı UI** | Cross-platform masaüstü+mobil UI | ★★★ |
| **Go** | Evet (cgo; C++ için C ABI) | **Hayır/pratik değil** (masaüstü UI kitleri niş) | CLI, sunucu, dönüştürme/analiz araçları | ★★ |
| **Objective-C** | Evet (`.mm`, doğrudan) | Yazılabilir (legacy UI); yeni proje için Swift tercih edilir | Legacy Apple UI / mevcut Objective-C kod tabanı | ★★ |
| **Zig** | Kısmen (C header evet, C++ header hayır) | Hayır/pratik değil (UI kitleri niş) | Performans/SIMD/DSP yardımcı araçları, build araçları | ★★ |
| **D** | Evet (`extern(C++)`) | Sınırlı (UI kitleri niş) | Sistem/performans aracı, legacy C++ entegrasyonu | ★★ |
| **Haskell** | Evet (yerleşik FFI) | Sınırlı/niş (GI/GTK binding'leri) | Araç, doğrulama, akademik/araştırma | ★★ |
| **OCaml** | Evet (yerleşik C FFI) | Sınırlı/niş | Araç, dil-tabanlı alt sistem, akademik | ★★ |
| **Scala** | Evet (JVM FFI / Scala Native) | Sınırlı/niş (JVM masaüstü UI nadir tercih) | JVM araç, build/analiz katmanı | ★★ |
| **VB.NET** | Evet (P/Invoke) | Yazılabilir (WinForms) ama modern ekipler C# tercih eder | Legacy Windows UI, hızlı iç araç | ★★ |
| **PHP** | Evet (PHP FFI) | **Hayır** — sunucu/CLI dili; UI tarayıcıda JS | Sunucu tarafı transkod/analiz, katalog servisi | ★ |
| **Ruby** | Evet (ffi/Rice) | Hayır | Betik, kurulum/dağıtım araçları | ★ |
| **Julia** | Evet (ccall; Cxx.jl arşivli) | Hayır (UI niş, çizim odaklı) | Ses analizi, DSP araştırması, ölçüm betiği | ★ |
| **R** | Evet (.Call/Rcpp) | Hayır (Shiny = dashboard, player değil) | İstatistik, ölçüm raporlama, işitsel analiz | ★ |
| **Lua** | Evet (C API / LuaJIT FFI) | Hayır | **Eklenti/betik dili** (player scripting, preset) | ★ |
| **PowerShell** | Evet (Add-Type + P/Invoke) | Hayır | Windows otomasyonu, paketleme/dağıtım betiği | ★ |

**Dağılım:** ★★★ = 11 dil · ★★ = 8 dil · ★ = 6 dil · Toplam = **25 dil**.

---

## 4. Performans Overhead Tablosu (dürüst durum)

| Dil / mekanizma | Tipik overhead | Kaynak | Güven |
|---|---|---|---|
| C / C++ (doğrudan çağrı) | ~%0 (aynı süreç, ara katman yok) | Kavramsal (ölçüm gerekmez) | Yüksek |
| Go **cgo** (Go→C) | **~40 ns/çağrı** (tek çekirdek; Go iç çağrısı ~1–2 ns ile karşılaştırılınca büyük) | aureliar.net *Benchmarking Go FFI* (2023); shane.ai *CGO Performance in Go 1.21* (2023) | Orta |
| Go cgo (C→Go callback) | Eski: ~1–3 µs → Go 1.21 sonrası **~100–200 ns** | Topluluk raporu (reddit/r/golang, 2023) | Düşük |
| Go (sayıya vurulmuş etki) | 192.000 çağrı/sn × 40 ns ≈ **7,68 ms/sn ≈ %0,77** (örneklem başına çağrı varsa) | Mühendislik hesabı (ölçüm değil) | Orta |
| Python (pybind11/nanobind) | **bilinmiyor** (yayımlanmış sayı yok) — asıl maliyet: örneklemin Python'a kopyalanması + GIL | — | UNKNOWN |
| C# P/Invoke | **bilinmiyor** (marshalling kopyası eklenebilir; blittable tiplerle kopyasız olabilir) | — | UNKNOWN |
| Java JNI / FFM (JEP 454) | **bilinmiyor** | JNA README: *doğruluk ve kolaylık performanstan önce gelir* (nitel beyan) | UNKNOWN |
| Java JNA (kodsuz çağrı) | **bilinmiyor** (README sayı vermiyor; libffi tabanlı, JNI'den yavaş olduğu yaygın kabul — ölçüm yok) | — | UNKNOWN |
| Rust (bindgen/cxx, C ABI) | **bilinmiyor** (C ABI çağrısı C düzeyinde varsayılır; yayımlanmış ölçüm yok) | — | UNKNOWN |
| Swift ↔ C++ (interop) | **bilinmiyor** | — | UNKNOWN |
| Dart FFI | **bilinmiyor** | — | UNKNOWN |
| WASM ↔ host geçişi | **bilinmiyor** (N-API/WASM çağrı maliyeti için sayı yok) | — | UNKNOWN |
| PHP FFI / Ruby FFI / Lua C API / Julia ccall / OCaml / Haskell / R / Zig / D / Kotlin / VB.NET / PowerShell | **bilinmiyor** (bu araştırmada doğrulanmış ölçüm bulunamadı) | — | UNKNOWN |

### 4.1 Kritik mühendislik çıkarımı (hesap, ölçüm değil)

1. **Blok bazlı API:** UI ↔ çekirdek arayüzü örneklem başına değil, **blok/paket başına** (örn.128–4096 kare, metadata, komut) çağrılırsa her FFI maliyeti binlerce örneklem arasında dağılır → overhead pratikte sıfırlanır.
2. **Ses verisi kopyalanmamalı:** Binding, ham örneklemleri dilin GC belleğine kopyalamak yerine **paylaşılan tampon/pointer + blok-dışı kuyruk** üzerinden geçirmelidir (Python/C#/Java için aksi halde GC basıncı FFI'dan büyük maliyet yaratır).
3. **Gerçek zamanlı yol ayrı:** çıkışı yapan yol (ASIO/WASAPI/CoreAudio) daima C++ içindedir; dil yalnız **komut/durum/olay** taşır.
4. **Ölçüm kapısı:** Neva için bindgen/pybind11/P-Invoke mikro-benchmark'ı yapılmalı → bu rapordaki UNKNOWN'lar ancak o ölçümle kapanır (bkz. §9).

---

## 5. Dil Bazlı Değerlendirme (25 dil)

### 5.1 ★★★ — Tam destek önerilir (11 dil)

#### 1) C
- **Binding:** Doğrudan statik/dinamik link; araç yok.
- **Olgunluk:** Standart (dil + ABI). **Overhead:** ~%0 (doğrudan).
- **UI:** (a) evet · (b) Yazılabilir (GTK/Win32) ama modern widget üretimi düşük; FFmpeg/VLC sınıfı projelerde çekirdek dili olarak kanıtlanmış · (c) Çekirdek katmanı + ABI dili.
- **Öneri:** ★★★ (Neva'nın **public C ABI** dili olması nedeniyle zorunlu); UI için C++ tercih edilir.

#### 2) C++
- **Binding:** Yok — doğrudan aynı süreçte (static lib / DLL + PIMPL ile gizli sembol).
- **Olgunluk:** Standart. **Overhead:** ~%0.
- **UI:** (a) evet · (b) **Evet — en pratik yol** (masaüstü UI çatıları + video/ses widget'ları) · (c) Uçtan uca masaüstü player (çekirdek + UI tek dil).
- **Öneri:** ★★★ — Neva Player'ın **varsayılan dili**.

#### 3) Rust
- **Binding:** C ABI + `bindgen 0.73.2`; C++ tipleri için `cxx 1.0.205`; `autocxx` **archived** (2026-09) → kullanımdan düşürülmeli. Alternatif üretici: *Diplomat* (uniffi README'de C/C++ odaklı alternatif, 923★ — hâlâ aktif).
- **Olgunluk:** Yüksek (bindgen/cxx güncel, iki aktif depo). **Overhead:** bilinmiyor (UNKNOWN).
- **UI:** (a) evet · (b) Evet — masaüstü kabuk/webview ve niş native UI çerçeveleri mevcut (çerçeve adları doğrulanmadı: UNKNOWN) · (c) CLI/araç, güvenli sarmalayıcı, gelecekte çekirdek-yanı katman.
- **Öneri:** ★★★ (araç/sarmalayıcı katmanı olarak; C++ çekirdeğe yazılmaz, onu sarar).

#### 4) Python
- **Binding:** `pybind11 3.1.0` (18.025★) veya `nanobind 3.1.0` (3.748★, daha küçük/daha hızlı iddia edilir — karşılaştırmalı ölçüm bu raporda YOK: UNKNOWN); gerektiğinde `Cython 3.3.0`.
- **Olgunluk:** En olgun sınıf. **Overhead:** bilinmiyor; asıl risk GIL + veri kopyası.
- **UI:** (a) evet · (b) Evet (masaüstü UI çatıları; dağıtım/paketleme dikkat) · (c) Betik, otomasyon, test, DSP prototipi, kullanıcı eklentileri.
- **Öneri:** ★★★ — **ikinci binding hedefi** (mühendislik üretkenliği).

#### 5) C#
- **Binding:** P/Invoke (`[DllImport]`, C ABI zorunlu); üreticiler: `CppSharp v1.2` (MIT, 3.403★), `ClangSharp` (MIT, 1.255★), `CsWinRT` (MIT, 668★), SWIG C# (SWIG v4.5.1).
- **Olgunluk:** Yüksek (hem elle hem otomatik yol). **Overhead:** bilinmiyor (marshalling kopya riski; blittable ile azaltılır).
- **UI:** (a) evet · (b) **Evet — Windows masaüstünde en pratik** · (c) Windows player UI + CoreMusic tarafındaki .NET deneyimiyle uyum.
- **Öneri:** ★★★ — **masaüstü UI hedefi**.

#### 6) Java
- **Binding:** JNI (JDK yerleşik) · **JEP 454 FFM API (Release 22, java.lang.foreign)** — artık birincil yol · JNA **5.19.1** (kodsuz prototip) · JavaCPP **1.5.14** (Apache-2.0 veya GPL-2+CE → lisans dikkat).
- **Olgunluk:** Yüksek ama araç dağınık (4 farklı yol). **Overhead:** bilinmiyor (UNKNOWN).
- **UI:** (a) evet · (b) Evet (masaüstü UI çatıları; modern his orta) · (c) Cross-platform masaüstü/kurumsal araç.
- **Öneri:** ★★★ — ancak **gerçek zamanlı ses JVM'de değil, C++ çekirdekte** olmalı (GC jitter).

#### 7) Kotlin
- **Binding:** JVM'de JNI/FFM (Java ile aynı); **Kotlin/Native `cinterop`** (C header'dan doğrudan, resmi); Rust çekirdeği varsa **uniffi 0.32.2** (MPL-2.0, 5.038★, Kotlin destekli).
- **Olgunluk:** Yüksek (uniffi'nin Mozilla üretimi, Firefox'ta üretimde — README beyanı). **Overhead:** JVM ile aynı → bilinmiyor.
- **UI:** (a) evet · (b) **Evet — Android'de birincil** · (c) Android player UI.
- **Öneri:** ★★★ — **mobil hedefi varsa**.

#### 8) Swift
- **Binding:** **C++ interoperability (Swift 5.9+)** — resmi (swift.org); ayrıca C ABI.
- **Olgunluk:** Yüksek (dil deposu 70.514★, aktif). **Overhead:** bilinmiyor (aynı süreç, kopyasız taşınabilir).
- **UI:** (a) evet · (b) Evet (Apple platformda) · (c) iOS/macOS player UI.
- **Öneri:** ★★★ **yalnız Apple hedefi için**; Windows/Linux masaüstünde olgunluğu doğrulanmadı (UNKNOWN).

#### 9) JavaScript
- **Binding:** Tarayıcıda **WASM** (Emscripten 27.727★); Node'da **N-API / node-addon-api** (2.411★, MIT); Rust çekirdeği için `wasm-bindgen` (9.193★).
- **Olgunluk:** Yüksek. **Overhead:** bilinmiyor.
- **UI:** (a) evet · (b) **Evet — web UI'ın dili** (masaüstü kabuk ile de mümkün) · (c) Web player UI, uzak kumanda paneli.
- **Öneri:** ★★★ — **web hedefi varsa** (bkz. §6 sınırları).

#### 10) TypeScript
- **Binding:** JS ile birebir aynı (aynı WASM/N-API yoluna tip katmanı).
- **Olgunluk:** JS altyapısına bağımlı → yüksek. **Overhead:** JS ile aynı → bilinmiyor.
- **UI:** (a) evet · (b) **Evet — web UI'da tip güvenliği** · (c) Web player UI, kontrol/monitoring paneli.
- **Öneri:** ★★★ — web hedefinde JS yerine TS önerilir (mühendislik tercihi; ölçüm gerekmez).

#### 11) Dart
- **Binding:** `dart:ffi` (yerleşik) + **ffigen** (header'dan binding üretir, `dart-lang/native` mono-repo, BSD-3, 277★); Android JNI için `jni` paketi (aynı depo).
- **Olgunluk:** Yüksek (resmi Dart paketleri). **Overhead:** bilinmiyor.
- **UI:** (a) evet · (b) **Evet — Flutter ile tek kod tabanı, cross-platform** · (c) Cross-platform masaüstü+mobil UI.
- **Öneri:** ★★★ — **tek UI kodu ile masaüstü+mobil** hedefleniyorsa C#/Kotlin yerine aday.

### 5.2 ★★ — Çekirdek erişimi var, UI pratik değil (8 dil)

#### 12) Go
- **Binding:** `cgo` (yerleşik, `pkg.go.dev/cmd/cgo`) — **yalnız C**; C++ çekirdek için **C ABI sarmalayıcı şart**.
- **Olgunluk:** Yüksek (stdlib). **Overhead:** **~40 ns/çağrı** (aureliar.net 2023; shane.ai 2023) — bu raporda doğrulanmış tek sayısal FFI verisi.
- **UI:** (a) evet (cgo ile) · (b) Hayır/pratik değil — masaüstü UI kitleri niş (adlar doğrulanmadı: UNKNOWN) · (c) CLI, sunucu, dönüştürme/analiz aracı.
- **Öneri:** ★★

#### 13) Objective-C
- **Binding:** **Objective-C++** (`.mm`) — Clang'ın yerleşik dili; C++ doğrudan çağrılır, FFI katmanı yok.
- **Olgunluk:** Yüksek (Clang users manual: Objective-C++ dili). **Overhead:** ~%0.
- **UI:** (a) evet · (b) Yazılabilir (legacy UI framework'leri) ama yeni proje için Swift tercih edilir · (c) Legacy Apple UI.
- **Öneri:** ★★ (yeni arayüzde Swift ★★★ tercih edilir).

#### 14) Zig
- **Binding:** **`@cImport`** — C header'ını doğrudan import eder (Zig dokümanı). **C++ header'ı DESTEKLENMEZ** → Neva için C ABI şart.
- **Olgunluk:** Dil olgun (GitHub aynası 43.292★, MIT) ancak depo *Moved to Codeberg* → GitHub aynası güncelliğini yitirmiş (son push 2025-11).
- **Overhead:** C düzeyi varsayılır ama ölçüm yok → bilinmiyor.
- **UI:** (a) evet (C ABI üzerinden) · (b) Hayır/pratik değil (UI kitleri niş, doğrulanmadı) · (c) Performans/SIMD/DSP yardımcıları.
- **Öneri:** ★★

#### 15) D
- **Binding:** `extern(C)` + **`extern(C++)`** (D spec: *Interface to C++* — `extern(C++)` class/struct ile C++ üye fonksiyonları çağrılabilir).
- **Olgunluk:** dmd · BSL-1.0 · 3.329★; C++ kapsamı spec kısıtlarına tabi (detaylar okunmadı → kapsam UNKNOWN).
- **Overhead:** bilinmiyor. **UI:** (a) evet · (b) Sınırlı/niş · (c) Sistem/performans aracı, legacy C++ entegrasyonu.
- **Öneri:** ★★

#### 16) Haskell
- **Binding:** GHC yerleşik FFI (`foreign import`); otomatik yardımcı `inline-c` (307★, lisans UNKNOWN).
- **Olgunluk:** Orta (inline-c düşük yıldızlı, lisansı doğrulanamadı). **Overhead:** bilinmiyor.
- **UI:** (a) evet · (b) Sınırlı/niş (GI/GTK binding'leri — doğrulanmadı) · (c) Araç, doğrulama, akademik.
- **Öneri:** ★★

#### 17) OCaml
- **Binding:** Yerleşik C FFI (*OCaml Manual — Interfacing C with OCaml*, ocaml.org/manual/5.1/intfc.html) + `ocaml-ctypes` (MIT, 413★, son push 2025-11).
- **Olgunluk:** Orta/iyi (dil 6.619★, aktif). **Overhead:** bilinmiyor.
- **UI:** (a) evet · (b) Sınırlı/niş · (c) Araç, dil-tabanlı alt sistem, akademik.
- **Öneri:** ★★

#### 18) Scala
- **Binding:** JVM üzerinde JNI/**FFM (JEP 454)**/JNA (Java ile aynı); **Scala Native** (4.696★) ile C FFI (lisans GitHub API: NOASSERTION → UNKNOWN).
- **Olgunluk:** Orta. **Overhead:** JVM ile aynı → bilinmiyor.
- **UI:** (a) evet · (b) Sınırlı/niş (JVM masaüstü UI nadir tercih) · (c) JVM araç, build/analiz katmanı.
- **Öneri:** ★★

#### 19) VB.NET
- **Binding:** P/Invoke (`Declare` / `[DllImport]`) — .NET yerleşik; C ABI gerektirir.
- **Olgunluk:** Yüksek (platform özelliği). **Overhead:** C# P/Invoke ile aynı → bilinmiyor.
- **UI:** (a) evet · (b) Yazılabilir (WinForms), modern ekipler C# tercih eder · (c) Legacy Windows UI, hızlı iç araç.
- **Öneri:** ★★

### 5.3 ★ — Yalnız script/araç (6 dil)

#### 20) PHP
- **Binding:** **PHP FFI** (PHP 7.4+ çekirdeğe yerleşik, C ABI; kod üretimi yok). Bazı dağıtımlarda/ellerde FFI kapalı olabilir (php.ini) — varsayılan değeri bu araştırmada doğrulanamadı: UNKNOWN.
- **Olgunluk:** php-src yerleşik (40.446★). **Overhead:** bilinmiyor.
- **UI:** (a) evet (FFI ile çekirdek) · (b) **Hayır** — PHP sunucu/CLI dili; gerçek player UI tarayıcıda JS/TS olur · (c) Sunucu tarafı transkod/katalog/analiz servisi.
- **Öneri:** ★

#### 21) Ruby
- **Binding:** `ffi` gem (BSD-3, 2.128★) ile C ABI; `Rice` (415★) ile C++ extension derleme; stdlib `fiddle` (URL doğrulanmadı: UNKNOWN).
- **Olgunluk:** Orta (ffi aktif; Rice düşük yıldız). **Overhead:** bilinmiyor.
- **UI:** (a) evet · (b) Hayır · (c) Betik, kurulum/dağıtım/CI aracı.
- **Öneri:** ★

#### 22) Julia
- **Binding:** Yerleşik **`ccall`** (C ABI); otomatik: **Clang.jl** (MIT, 243★, aktif); **Cxx.jl → ARCHIVED** (762★, son push 2024-05) → Julia↔C++ doğrudan yol fiilen bakımsız.
- **Olgunluk:** C için iyi, C++ için zayıf. **Overhead:** bilinmiyor (ccall); GC sıçraması gerçek zamanlı ses için risk (ölçüm yok: UNKNOWN).
- **UI:** (a) evet · (b) Hayır (çizim/analiz odaklı) · (c) Ses analizi, DSP araştırması, ölçüm betiği.
- **Öneri:** ★ (araştırma/doku dili olarak değerli ama UI değil).

#### 23) R
- **Binding:** `.Call` / C API (R-exts, CRAN) + **Rcpp** (GPL-2.0, 797★).
- **Olgunluk:** İyi ama **lisans riski**: GPL-2.0 bileşenin tescilli Neva çekirdeğiyle dağıtımı hukuki inceleme ister.
- **Overhead:** bilinmiyor. **UI:** (a) evet · (b) Hayır (Shiny = web dashboard, player UI değil) · (c) İstatistik, ölçüm raporlama.
- **Öneri:** ★

#### 24) Lua
- **Binding:** Yerleşik **Lua C API** (lua.org/manual/5.4); LuaJIT'de **FFI** (luajit.org/ext_ffi.html, LuaJIT 5.801★); C++'tan gömme için **sol2 v3.3.0** (MIT, 5.160★) — release 2022-06-25, depo son commit 2025-03 → **düşük bakım**.
- **Olgunluk:** C API olgun; sol2 yanıt verme hızı düşük. **Overhead:** C API düzeyi → ölçüm yok: bilinmiyor.
- **UI:** (a) evet · (b) Hayır (oyun/niş UI dışında) · (c) **Player eklenti/betik dili** (preset, kısayol, otomasyon).
- **Öneri:** ★ — ama **eklenti dili** olarak Neva mimarisinde ayrı yeri vardır (bkz. §7 desen D).

#### 25) PowerShell
- **Binding:** `Add-Type` ile C# snippet derleyip P/Invoke (yerleşik, Microsoft dokümanı).
- **Olgunluk:** Platform özelliği. **Overhead:** C# ile aynı → bilinmiyor.
- **UI:** (a) evet (tekil çağrılar) · (b) Hayır (WPF'i çağırılabilir ama pratik değil) · (c) Windows otomasyonu, paketleme/dağıtım betiği.
- **Öneri:** ★

---
## 6. WASM / Web: Tarayıcıda Hi-res Ses Mümkün mü? (Dürüst Değerlendirme)

### 6.1 Kanıtlanabilen / mümkün olan

| Konu | Durum | Kanıt |
|---|---|---|
| Ses işleme biçimi | **32-bit float** (W3C: *High dynamic range, using 32-bit floats for internal processing*) | [W3C Web Audio API 1.1](https://www.w3.org/TR/webaudio-1.1/) (22.09.2026) |
| AudioWorklet işleme bloğu | **128 kare, Float32Array** (giriş/çıkış kanal başına) | [MDN — AudioWorkletProcessor.process()](https://developer.mozilla.org/en-US/docs/Web/API/AudioWorkletProcessor/process) |
| Bağlam örnekleme hızı | `AudioContext.sampleRate` → tüm node'lar aynı hızda; gerçek hız **çıktı cihazına bağlı** | [MDN — BaseAudioContext.sampleRate](https://developer.mozilla.org/en-US/docs/Web/API/AudioContext/sampleRate) |
| Çıktı cihazı seçimi | **Var** — `AudioContext.setSinkId()` (Chrome 110+); `AudioSinkInfo` ile durum | [MDN — setSinkId](https://developer.mozilla.org/en-US/docs/Web/API/AudioContext/setSinkId) · [Chrome blog](https://developer.chrome.com/blog/audiocontext-setsinkid) · [MDN — AudioSinkInfo](https://developer.mozilla.org/en-US/docs/Web/API/AudioSinkInfo) |
| C++ çekirdek → tarayıcı | **Mümkün** (Emscripten ile WASM derlemesi; kod çözücü de WASM'de) | [Emscripten (27.727★)](https://github.com/emscripten-core/emscripten) · [ffmpeg.wasm (17.890★, MIT)](https://github.com/ffmpegwasm/ffmpeg.wasm) |
| worklet ↔ ana thread veri | **Mümkün** — SharedArrayBuffer ring buffer (cross-origin isolation şart) | [web-audio-samples (756★, Apache-2.0, aktif)](https://github.com/GoogleChromeLabs/web-audio-samples) · [MDN — crossOriginIsolated](https://developer.mozilla.org/en-US/docs/Web/API/crossOriginIsolated) |

### 6.2 Sınırlar (dürüst kısım)

1. **Exclusive / bit-perfect mod YOK.** W3C Web Audio 1.1 ve [Audio Output Devices API](https://w3c.github.io/mediacapture-output/) yalnız **cihaz seçimi** (`setSinkId`) tanımlar; DAC'ı exclusive modda devralma, OS mixer'ı atlatma veya çıktı örneklem hızını uygulamanın zorlaması gibi bir API **spesifikasyonda tanımlı değildir**. → Tarayıcıda **ASIO/WASAPI-exclusive eşdeğeri yoktur.** (Yüksek güven: spesifikasyon kapsamı.)
2. **192kHz hedefi ölçülemez.** `AudioContext.sampleRate` istenen hızda açılabilir, ancak çıktı cihazı 48kHz'te çalışıyorsa kaybı göze alamayacağımız bir **örnekleme dönüşümü (SRC)** tarayıcı/OS tarafında yine yapılır. Uygulama bu zinciri kontrol edemez.
3. **32-bit float = işlem derinliği, bit-exact zincir değil.** Float32 işleme geniş dinamik aralık sağlar; fakat son aşamada OS mixer → DAC zinciri (volume, efekt, sürücü) uygulamanın dışındadır. *Hi-res çıktı* iddiası yalnız native (exclusive) yolla savunulabilir.
4. **Gecikme garantisi yok.** AudioWorklet düşük gecikmeli çalışır ancak exclusive ASIO/WASAPI benzeri sayısal garanti sunmaz — bu raporda **ölçüm bulunmadı: gecikme değerleri UNKNOWN.**
5. **4K video:** tarayıcıda donanım hızlandırma ile mümkün; WASM içinde yazılım decode CPU'da pahalıdır (ölçüm yok: UNKNOWN). Ses ve video yolları ayrı tasarlanmalı.
6. **Gerçek zamanlı thread yok.** WASM'de uygulamanın kendi gerçek-zamanlı thread'ini yönetmesi sınırlıdır; AudioWorklet kendi thread'inde çalışır → C++ çekirdeğin tamamı worklet'e taşınmak zorunda değil, **ring buffer + SharedArrayBuffer** gerekir (COOP/COEP başlıkları zorunlu, bkz. crossOriginIsolated).

### 6.3 WASM sonucu

> **Tarayıcı, Neva için «kaliteli dinleme + uzak kontrol» hedefidir, «hi-res exclusive çıktı» hedefi değildir.** WebAudio float32 + AudioWorklet sayesinde **işleme** zinciri 32-bit float olabilir; ancak **özel mod/192kHz exclusive çıkış tarayıcıda yapılamaz.** Bu nedenle WASM hedefi **Aşama 2** olarak planlanmalı; hi-res iddiası yalnız native sürümlerde (C++ çekirdek + ASIO/WASAPI/CoreAudio) yapılmalıdır.

---

## 7. «C++ Çekirdek + N Dil Frontend» Mimari Desenleri

| # | Desen | Yapı | Artılar | Eksiler / Riskler | Hangi senaryoda? |
|---|---|---|---|---|---|
| **1** | **Monolithic SDK** | Dile özel hazır paket (wheel / JAR / NuGet / SPM) — her dil için ayrı binding kodu | En iyi tip güvenliği, en iyi performans, IDE tam desteği | Kod ×N; her sürümde N kat bakım; API tutarsızlaşma riski | **1–2 dil**, ürün olarak dışa SDK satıyorsanız |
| **2** | **C ABI wrapper** ★ | Tek **ince C API** (PIMPL ile C++ gizlenir); her dil kendi hazır FFI'sıyla çeker (pybind11/P-Invoke/cgo/dart:ffi/...) | Tek sözleşme, ABI-stable, her dil için araç zaten hazır, eklentiye de açı | C++ tipleri C'ye indirgenir (zarf kodu yazılır); tip güvenliği dile göre değişir | **3+ dil, dahili + 3. taraf kullanım — Neva için VARSAYILAN** |
| **3** | **RPC / IPC** | Çekirdek ayrı süreçte; gRPC / JSON-RPC / named pipe / stdio ile komut-durum | Süreç/çökme izolasyonu, **lisans/dil uyumsuzluğu** çözülür (ör. PHP sunucu ↔ native), uzaktan UI | **Ses verisi taşımak pahalı** (kopya + gecikme) → yalnız komut/durum/olay taşınır | UI ile çekirdek ayrı süreçte olmalıysa; sunucu ile oynatıcı entegrasyonunda |
| **4** | **Plugin mimarisi** | Çekirdek, dil-agnostik **C ABI eklenti SDK** sunar; DSP/UI eklentileri her dille yazılabilir | 3. taraf ekosistemi, sanal eklentiler, sürüm uzantısı | Her dil için eklenti çalışma zamanı gerekir; ABI kararlılığı kritik | Eklenti pazaryeri/DSP zinciri açacaksanız (**2 ile kombine edilir**) |

### 7.1 Karar matrisi (hızlı)

| Soru | Evet ise | Hayır ise |
|---|---|---|
| Yalnız 1–2 dil mi? | Desen 1 (monolithic) | Desen 2 |
| 3+ dil / dahili+3. taraf mı? | **Desen 2 (C ABI)** + opsiyonel Desen 4 | Desen 1 |
| UI ile çekirdek farklı süreçte olmalı mı (çökme/uzak/PHP)? | Desen 3 (RPC/IPC) — ses verisi değil, komut/olay | Desen 2 |
| Tarayıcı hedefi var mı? | Desen 2'nin WASM varyantı + AudioWorklet (§6) | — |
| 3. taraf DSP/UI eklentisi planlanıyor mu? | Desen 4 (eklenti SDK) + Desen 2 | Desen 2 tek başına |

### 7.2 Desen 2 için tasarım kuralları (Neva)

1. **Tek kamuya açık başlık** (örn. `neva_c.h`): `neva_*` ön eki, `extern «C»`, sürüm/scheme semantiği, struct'larda `size` alanı (gelecek ekleme uyumu).
2. **C++ asla dışa açılmaz** (PIMPL/gizli sembol) — aksi halde her dil için ABI kırılması yaşanır.
3. **Bloklı veri modeli:** ses örneklemleri **kopyasız, paylaşılan tampon** üzerinden; dil tarafına yalnız pointer + uzunluk geçer (§4.1).
4. **Hata modeli:** `neva_status_t` + thread-local hata metni (C++ istisnaları C'ye sızmaz).
5. **Ölçüm kapısı:** her binding için mikro-benchmark (çağrı başına ns + kopya baytı) — §9'daki UNKNOWN'lar ancak böyle kapanır.

---

## 8. Neva Player İçin Önerilen Dil Paketi

### 8.1 Çekirdek paketi (3 dil — zorunlu)

| # | Dil | Rol | Gerekçe | Risk / dengeleyici |
|---|---|---|---|---|
| **1** | **C++20** | Neva Engine çekirdek **+** masaüstü player UI | Hi-res DSP, 4K video, donanım API'leri (ASIO/WASAPI/CoreAudio/D3DVA) C++ ekosisteminde; UI da aynı dilde → FFI sıfır | UI kodu çekirdeğe karışmamalı (katman ayrımı) |
| **2** | **C (C ABI)** | Tek kamuya açık sözleşme dili | 25 dilin tamamı C ABI'ye bağlanır; ABI-stable sürümleme, eklenti SDK'sının da temeli | Zarf kodu (marshaller) yazma yükü — bir kez yazılır |
| **3** | **Python** (pybind11/nanobind) | Betik, otomasyon, test, DSP prototipi | En olgun binding (18.025★), mühendislik üretkenliği; R/Julia'nın yaptığı analiz işini de görür | Gerçek zamanlı yol Python'a girmez (yalnız komut/durum) |

### 8.2 Arayüz paketi (2 dil + hedef seçimi)

| # | Dil | Rol | Gerekçe |
|---|---|---|---|
| **4** | **C#** (.NET) | **Windows masaüstü player UI** | P/Invoke + CppSharp v1.2 olgun; Windows'ta en verimli masaüstü UI; CoreMusic'in .NET deneyimiyle uyum | P/Invoke marshalling'i blok-bazlı API ile sınırlandırılmalı |
| **5** | **Hedefe göre 1 tanesi** | İkinci platform arayüzü | ↓ seçim tablosu |

| İkinci platform hedefi | Seçilecek dil | Neden |
|---|---|---|
| **Android** | **Kotlin** (cinterop / uniffi) | Android'de birincil dil; Rust/Core C++ ile binding olgun |
| **Web / uzak kontrol** | **TypeScript** | Web UI'ın dili; WASM + AudioWorklet ile birleşir (§6 sınırları geçerli) |
| **Tek kod tabanı masaüstü+mobil** | **Dart** (Flutter) | dart:ffi + ffigen; tek UI kodu, iki platform |
| **iOS/macOS** | **Swift** (C++ interop 5.9+) | Apple'da doğal yol; Windows/Linux'ta olgun değil |
| **Genel amaçlı araç/sarmalayıcı (Aşama 2)** | **Rust** (bindgen/cxx) | C ABI üzerinde güvenli sarmalayıcı, CLI araçlar, gelecekte ikinci çekirdek katmanı |

### 8.3 Önerilmeyenler (ve neden)

- **R / Julia / Lua / PowerShell / PHP / Ruby:** çekirdeğe erişimleri var (★/★★★ tablosuna bakın) ama **UI dili olarak değiller** — sırasıyla analiz, araştırma, eklenti betiği, otomasyon, sunucu ve betik rollerine sıkıştırılmalı.
- **Go / Scala / Haskell / OCaml / D / Zig / VB.NET:** teknik olarak ★★ ama bu projede **kazançları maliyetini karşılamaz** (UI niş, ekip/UI ekosistemi yok). İstisna: **Zig** — SIMD/alt-optimizasyon denemelerinde araştırılabilir (C ABI ile sorunsuz).
- **C:** UI dili olarak değil, **ABI dili olarak** önerilir (★★★ nedeni burada).

### 8.4 Özet paket (tek satır)

> **C++20 (çekirdek + masaüstü UI) · C (ABI sözleşmesi) · Python (binding/betik) · C# (Windows UI) · + hedefe göre Kotlin (Android) / TypeScript (web) / Dart (Flutter)** — opsiyonel Aşama 2: **Rust** (araç/sarmalayıcı) ve **WASM** (web dinleme, hi-res iddiasız).

---

## 9. Bilinmeyenler (UNKNOWN) ve Boşluklar

| # | Bilinmeyen | Neden | Kapanma yolu |
|---|---|---|---|
| U1 | 25 dilin **20'si için FFI overhead yüzdesi/ns değeri** | Yayım bulunmadı | Neva C API'si üzerinde mikro-benchmark (çağrı başına ns, kopya baytı) |
| U2 | pybind11 vs nanobind **hız farkı** | Bu raporda karşılaştırmalı ölçüm doğrulanmadı | Kendi binding'imizde iki aday için ölçüm |
| U3 | JNA / JNI / FFM / P-Invoke **sayısal overhead** | Kaynaklarda sayı yok (JNA README yalnız nitel beyan) | JMH / BenchmarkDotNet ölçümü |
| U4 | WASM ↔ AudioWorklet **gecikme sayıları** | Ölçüm yok | Tarayıcı içi ölçüm (AudioContext.baseLatency/outputLatency + worklet) |
| U5 | PHP FFI **varsayılan etkinlik değeri** (php.ini) | php.net sayfasında doğrulanamadı | Hedef PHP sürümünün `php -i` çıktısı |
| U6 | **Rice (Ruby) / JNR-FFI / scala-native / Emscripten / inline-c / JNAerator lisans SPDX kodu** | GitHub API: NOASSERTION veya null | Depo LICENSE dosyasının okunması |
| U7 | **Sürüm/yıldızların zamanla değişmesi** | Yıldızlar 2026-10-19 anlı değerdir | İleride yeniden çekim |
| U8 | **UI çatılarının (Qt/WPF/JavaFX/Flutter/egui/Tauri/Slint) sürümleri** | Bu araştırmada doğrulanmadı | Ayrı UI-teknoloji araştırması |
| U9 | Zig upstream sürümü (GitHub aynası *Moved to Codeberg*) | Codeberg'de doğrulanmadı | Codeberg repo kontrolü |
| U10 | **autocxx/Cxx.jl arşivlenmesinin** yerine geçecek güncel C++↔dil yolları (Diplomat vb.) | Yalnız uniffi README'sinde listelendi (923★) | Diplomat olgunluk incelemesi |

---

## 10. Kaynaklar (erişim tarihi: 2026-10-19)

### 10.1 Paket kayıt defterleri / API (birincil kanıt)

- PyPI JSON API — [pybind11 3.1.0 (BSD-3-Clause)](https://pypi.org/project/pybind11/) · [nanobind 3.1.0](https://pypi.org/project/nanobind/) · [Cython 3.3.0 (Apache-2.0)](https://pypi.org/project/Cython/) · [swig (PyPI metadata: GPL-3.0-or-later)](https://pypi.org/project/swig/)
- crates.io — [cxx 1.0.205](https://crates.io/crates/cxx) · [bindgen 0.73.2](https://crates.io/crates/bindgen) · [uniffi 0.32.2](https://crates.io/crates/uniffi) · [autocxx 0.30.0](https://crates.io/crates/autocxx)
- GitHub REST API (repo metadata: stars/license/archived/pushed_at; releases/tags) — tüm ★ değerleri bu uçtan çekilmiştir:
  - [pybind/pybind11](https://github.com/pybind/pybind11) · [wjakob/nanobind](https://github.com/wjakob/nanobind) · [cython/cython](https://github.com/cython/cython)
  - [rust-lang/rust-bindgen](https://github.com/rust-lang/rust-bindgen) · [dtolnay/cxx](https://github.com/dtolnay/cxx) · [google/autocxx (archived)](https://github.com/google/autocxx) · [rust-diplomat/diplomat](https://github.com/rust-diplomat/diplomat)
  - [swig/swig (tag v4.5.1)](https://github.com/swig/swig) · [mozilla/uniffi-rs](https://github.com/mozilla/uniffi-rs)
  - [mono/CppSharp (release v1.2, 2025-11-19)](https://github.com/mono/CppSharp) · [NuGet: CppSharp](https://www.nuget.org/packages/CppSharp/) · [dotnet/ClangSharp](https://github.com/dotnet/ClangSharp) · [microsoft/CsWinRT](https://github.com/microsoft/CsWinRT)
  - [java-native-access/jna](https://github.com/java-native-access/jna) (README: sürüm **5.19.1**, LGPL-2.1+/Apache-2.0) · [bytedeco/javacpp](https://github.com/bytedeco/javacpp) (release **1.5.14**, LICENSE: Apache-2.0 **veya** GPL-2+Classpath) · [jnr/jnr-ffi](https://github.com/jnr/jnr-ffi) · [nativelibs4java/JNAerator](https://github.com/nativelibs4java/JNAerator)
  - [dart-lang/native](https://github.com/dart-lang/native) · [ffi/ffi (Ruby)](https://github.com/ffi/ffi) · [jasonroelofs/rice](https://github.com/jasonroelofs/rice)
  - [LuaJIT/LuaJIT](https://github.com/LuaJIT/LuaJIT) · [lua/lua](https://github.com/lua/lua) · [ThePhD/sol2](https://github.com/ThePhD/sol2) · [RcppCore/Rcpp](https://github.com/RcppCore/Rcpp)
  - [JuliaInterop/Cxx.jl (archived)](https://github.com/JuliaInterop/Cxx.jl) · [JuliaInterop/Clang.jl](https://github.com/JuliaInterop/Clang.jl) · [yallop/ocaml-ctypes](https://github.com/yallop/ocaml-ctypes) · [fpco/inline-c](https://github.com/fpco/inline-c)
  - [scala-native/scala-native](https://github.com/scala-native/scala-native) · [ghc/ghc](https://github.com/ghc/ghc) · [dlang/dmd](https://github.com/dlang/dmd) · [ziglang/zig (*Moved to Codeberg*)](https://github.com/ziglang/zig) · [JetBrains/kotlin](https://github.com/JetBrains/kotlin) · [ocaml/ocaml](https://github.com/ocaml/ocaml)
  - [emscripten-core/emscripten](https://github.com/emscripten-core/emscripten) · [rustwasm/wasm-bindgen](https://github.com/rustwasm/wasm-bindgen) · [nodejs/node-addon-api](https://github.com/nodejs/node-addon-api) · [ffmpegwasm/ffmpeg.wasm](https://github.com/ffmpegwasm/ffmpeg.wasm) · [GoogleChromeLabs/web-audio-samples](https://github.com/GoogleChromeLabs/web-audio-samples)
  - [php/php-src](https://github.com/php/php-src) · [swiftlang/swift](https://github.com/swiftlang/swift)

### 10.2 Resmi dokümantasyon / spesifikasyon

- [OpenJDK JEP 454 — Foreign Function & Memory API (Release 22)](https://openjdk.org/jeps/454) · [java.lang.foreign API (JDK 22)](https://docs.oracle.com/en/java/javase/22/docs/api/java.base/java/lang/foreign/package-summary.html)
- [Swift — Mixing Swift and C++ (C++ interop, Swift 5.9+)](https://swift.org/documentation/cxx-interop/) · [Desteklenen özellikler](https://swift.org/documentation/cxx-interop/status/)
- [Clang Users Manual (Objective-C++ dili)](https://clang.llvm.org/docs/UsersManual.html)
- [Kotlin/Native — C interop (cinterop)](https://kotlinlang.org/docs/native-c-interop.html)
- [Go — cmd/cgo](https://pkg.go.dev/cmd/cgo)
- [Dart FFI (api.dart.dev)](https://api.dart.dev/stable/dart-ffi)
- [PHP FFI sınıfı](https://www.php.net/manual/en/class.ffi.php)
- [Zig — C kütüphaneleriyle entegrasyon (@cImport)](https://ziglang.org/learn/overview/#importing-c-code)
- [D — Interface to C++ (extern(C++))](https://dlang.org/spec/cpp_interface.html)
- [Julia — Calling C and Fortran Code (ccall)](https://docs.julialang.org/en/v1/manual/calling-c-and-fortran-code/)
- [R — Writing R Extensions (R-exts / .Call)](https://cran.r-project.org/doc/manuals/r-release/R-exts.html)
- [LuaJIT — FFI](https://luajit.org/ext_ffi.html) · [Lua 5.4 Reference Manual](https://www.lua.org/manual/5.4/manual.html)
- [GHC Users Guide — Foreign Function Interface](https://ghc.gitlab.haskell.org/ghc/doc/users_guide/exts/ffi.html)
- [OCaml Manual — Interfacing C with OCaml](https://ocaml.org/manual/5.1/intfc.html) · [OCaml Manual kökü](https://ocaml.org/manual/)
- [.NET — P/Invoke (DllImport)](https://learn.microsoft.com/en-us/dotnet/standard/native-interop/pinvoke)
- [PowerShell — Add-Type](https://learn.microsoft.com/en-us/powershell/module/microsoft.powershell.utility/add-type)
- [uniffi kullanıcı kılavuzu](https://mozilla.github.io/uniffi-rs/) · [cxx.rs](https://cxx.rs/)

### 10.3 Web ses spesifikasyonları

- [W3C Web Audio API 1.1 (32-bit float iç işleme)](https://www.w3.org/TR/webaudio-1.1/)
- [W3C Audio Output Devices API (setSinkId)](https://w3c.github.io/mediacapture-output/)
- [MDN — AudioWorkletProcessor.process() (128 kare / Float32Array)](https://developer.mozilla.org/en-US/docs/Web/API/AudioWorkletProcessor/process) · [MDN — AudioWorklet](https://developer.mozilla.org/en-US/docs/Web/API/AudioWorklet)
- [MDN — BaseAudioContext.sampleRate](https://developer.mozilla.org/en-US/docs/Web/API/AudioContext/sampleRate) · [MDN — AudioContext.setSinkId()](https://developer.mozilla.org/en-US/docs/Web/API/AudioContext/setSinkId) · [MDN — AudioSinkInfo](https://developer.mozilla.org/en-US/docs/Web/API/AudioSinkInfo)
- [MDN — crossOriginIsolated (SharedArrayBuffer gereksinimi)](https://developer.mozilla.org/en-US/docs/Web/API/crossOriginIsolated)
- [Chrome Blog — AudioContext.setSinkId (Chrome 110+)](https://developer.chrome.com/blog/audiocontext-setsinkid)

### 10.4 Performans (ikinci el — orta/düşük güven)

- [Benchmarking Go FFI (~40 ns/cgo çağrısı)](https://aureliar.net/posts/benchmarking-go-ffi/) (2023)
- [CGO Performance in Go 1.21 (~40 ns)](https://shane.ai/posts/cgo-performance-in-go1.21/) (2023)
- [changkun/cgo-benchmarks (Cgo vs Go vs C)](https://github.com/changkun/cgo-benchmarks) (2019)
- Go 1.21 callback iyileştirmesi (1–3 µs → 100–200 ns) raporu: [reddit/r/golang](https://www.reddit.com/r/golang/comments/168c4ly/cgo_performance_in_go_121/) — **düşük güven**

---

*Bu rapor 2026-10-19 tarihinde araştırma subagent'i tarafından üretilmiştir. Kod içermez, analiz + kaynak içerir. Doğrulanamayan hiçbir sürüm/yıldız/URL uydurulmamış, UNKNOWN olarak işaretlenmiştir (§9).*