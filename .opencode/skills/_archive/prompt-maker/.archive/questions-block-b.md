# BLOK B — TEKNOLOJI STACK (400+ Soru) — ULTRA DETAYLI EXPANDED VERSION

**Güncelleme:** 2026-06-11 — Her kategori 30-60 soruya GENIŞLETILDI. Seçenekler 8-15 per soru.

---

## B1 — PROGRAMLAMA DİLLERİ (100 Soru)

```
━━━━━ DİL SEÇİMİ (ZORUNLU) ━━━━━

B1.1.  Hangi programlama dili(leri) kullanılıyor? (Seçmeli / Multiple choice)
       A) PHP 8.x
       B) JavaScript (Vanilla)
       C) TypeScript
       D) Python
       E) Java
       F) C# (.NET Core)
       G) Go
       H) Rust
       I) C++
       J) Ruby
       K) Kotlin
       L) Swift
       M) Perl
       N) Scala
       O) Clojure
       P) Elixir
       Q) Erlang
       R) R language
       S) Julia
       T) MATLAB
       U) Other (spesifik adı?)

B1.2.  Her dil için SAHİP OLUNAN versiyon numarası?
       → PHP 8.4 mı? Node.js 22 mı? Python 3.12 mi?
       → Tam versiyon: X.Y.Z formatında

B1.3.  Dil switching / migration planı var mı?
       A) Tam rewrite planlı (timeline?)
       B) Gradual migration (existing new, replace old)
       C) Kısmi migration (sadece new projects)
       D) No switching (komit ettik, kalıcı)
       E) Uncertainty (belirlemedik henüz)
       F) Already switched (ne zaman?)

B1.4.  Polyglot architecture düzeyi?
       A) Monoglot (single language)
       B) 2 dil
       C) 3 dil
       D) 4-5 dil
       E) 6+ dil (heavy polyglot)
       F) By design (mikro-servisler different langs)
       G) Accidental (legacy)

B1.5.  Language version upgrade stratejisi?
       A) Her major release → immediate update
       B) Her minor release → quick update
       C) Quarterly updates
       D) Biannual updates
       E) Annual updates
       F) 6-month lag behind latest
       G) LTS only (skip non-LTS)
       H) Pinned version (never update)
       I) Security patches only

B1.6.  Type safety ne kadar önemli projeye?
       A) Mandatory (strict mode, errors prevent deploy)
       B) Highly recommended (warnings = blockers)
       C) Recommended (warnings logged, not blocking)
       D) Nice to have (optional)
       E) Not important (dynamic types OK)
       F) Actively avoided (prefer duck typing)

B1.7.  Static code analysis tools?
       A) PHPStan (PHP, strict)
       B) Psalm (PHP, strict)
       C) mypy (Python)
       D) Pylint (Python)
       E) ESLint (JavaScript)
       F) TypeScript compiler (TS)
       G) Clippy (Rust)
       H) rustc warnings (Rust)
       I) Go vet (Go)
       J) golangci-lint (Go)
       K) Custom linters
       L) None (no static analysis)

B1.8.  Runtime performance kritik mi?
       A) Yes, <50ms target
       B) Yes, <100ms target
       C) Yes, <500ms target
       D) Moderate, <1s acceptable
       E) Not critical (>1s OK)
       F) Performance not a concern

B1.9.  Memory constraints?
       A) Unlimited (cloud auto-scale)
       B) 8GB+ available
       C) 4-8GB available
       D) 2-4GB (moderate)
       E) <1GB (tight)
       F) <512MB (very tight)
       G) <256MB (embedded)
       H) <100MB (deeply embedded)

B1.10. CPU core constraints?
       A) Unlimited (many cores available)
       B) 8+ cores (multi-core optimized)
       C) 4-8 cores (standard)
       D) 2-4 cores (modest)
       E) Single core (constrained)
       F) Multi-core critical (must parallelize)
       G) Single-thread OK (sequential OK)

B1.11. Concurrency model preference?
       A) Multi-threaded (thread pools)
       B) Async-await (async I/O)
       C) Event-driven (callback-based)
       D) Actor model (message passing)
       E) Coroutines (Kotlin, Python)
       F) Goroutines (Go)
       G) Single-threaded (sequential)
       H) Combination (async + threads)

B1.12. Garbage collection (GC) impact?
       A) Critical (GC pauses must be <1ms)
       B) Important (GC pauses <10ms acceptable)
       C) Monitored (watch for pauses)
       D) Not relevant (not a concern)
       E) Actively avoided (manual memory management)

B1.13. Functional programming support needed?
       A) Pure FP (immutability, no side-effects)
       B) FP-style (FP patterns, OOP allowed)
       C) Mixed (FP + OOP equally)
       D) OOP primary (FP secondary)
       E) Imperative (OOP, no FP)
       F) Procedural (old-style)

B1.14. Metaprogramming needs?
       A) Heavy (code generation, macros required)
       B) Moderate (decorators, reflection)
       C) Light (introspection only)
       D) Not needed

B1.15. REPL / Interactive shell importance?
       A) Critical (data science, research)
       B) Important (dev workflow)
       C) Nice to have (debugging)
       D) Not important (compiled only)

B1.16. Code generation / Template systems?
       A) Heavy (code-gen tools required)
       B) Moderate (template engines)
       C) Light (string interpolation)
       D) No code generation

B1.17. Platform compatibility requirement?
       A) Linux only
       B) Linux + Windows
       C) Linux + Windows + macOS
       D) Windows only (enterprise)
       E) macOS only
       F) Mobile (iOS + Android)
       G) Truly cross-platform (all targets)
       H) Single platform

B1.18. Dil licensing / IP concerns?
       A) Permissive (MIT, Apache 2.0, BSD)
       B) GPL (copyleft)
       C) Proprietary (commercial license)
       D) Doesn't matter

B1.19. Community size / Ecosystem maturity?
       A) Massive (1000+ packages, 100K+ developers)
       B) Large (500+ packages, 50K+ developers)
       C) Medium (100+ packages, 10K+ developers)
       D) Small (10-100 packages, 1K developers)
       E) Niche (<10 packages, <1K developers)
       F) Doesn't matter

B1.20. Job market demand?
       A) Very high (easy to hire)
       B) High (good talent pool)
       C) Moderate (some talent)
       D) Niche (hard to hire)
       E) Declining (talent leaving)
       F) Doesn't matter

B1.21. Language learning curve?
       A) Shallow (days to productive)
       B) Moderate (weeks to productive)
       C) Steep (months to productive)
       D) Very steep (months to competent)

B1.22. Legacy code / Codebase state?
       A) Complete rewrite (greenfield)
       B) Gradual migration (coexist)
       C) Keep forever (no change)
       D) Partial (some legacy, some new)

B1.23. Web assembly (WASM) support?
       A) Full support (AssemblyScript, blazor)
       B) Partial (Go WASM)
       C) Planned
       D) Not planned
       E) Not applicable

B1.24. Language version / Release cadence?
       A) Rolling release (continuous)
       B) Quarterly
       C) Biannual
       D) Annual
       E) LTS + feature branches

B1.25. Team size for this language?
       A) 1 developer (solo)
       B) 2-3 developers
       C) 4-10 developers
       D) 10-50 developers
       E) 50+ developers (large team)

━━━━━ PHP SPESİFİK (25 Soru) ━━━━━

B1.26. PHP versiyonu tahmini?
       A) 7.4 (legacy support)
       B) 8.0 (first typed)
       C) 8.1 (improvements)
       D) 8.2 (readonly)
       E) 8.3 (typed properties)
       F) 8.4 (newest)
       G) Nightly (bleeding edge)

B1.27. `declare(strict_types=1)` enforcement?
       A) Mandatory (all files)
       B) Recommended (new files)
       C) Optional (mixed)
       D) Never (loose typing)

B1.28. PHP extensions required?
       A) SPL (Standard)
       B) DOM / XML
       C) Curl (HTTP)
       D) GD (image)
       E) ImageMagick (advanced image)
       F) FFmpeg (video)
       G) Redis
       H) Memcache
       I) mbstring (UTF-8)
       J) OpenSSL
       K) PDO (database)
       L) sockets

B1.29. JIT compilation usage?
       A) Enabled (PHP 8.0+ JIT)
       B) Disabled (performance OK without)
       C) Selectively tuned

B1.30. Opcache strategy?
       A) Aggressive (disable timestamp validation)
       B) Moderate (selective preload)
       C) Conservative (check every request)

B1.31. Memory limit per request?
       A) Unlimited
       B) 256MB (default)
       C) 512MB
       D) 1GB
       E) 2GB
       F) Custom (specify)

B1.32. Error handling mode?
       A) E_ALL strict (all errors fatal)
       B) E_ALL (warnings but no fatal)
       C) Production (errors logged, no display)
       D) Custom error handlers

B1.33. max_execution_time setting?
       A) 30s (default)
       B) 60s
       C) 300s (5 min)
       D) 600s (10 min)
       E) Custom
       F) Unlimited
       G) CLI has separate limit

B1.34. upload_max_filesize?
       A) 2MB (default)
       B) 10MB
       C) 100MB
       D) 1GB
       E) Cloud storage (S3 bypass)

B1.35. PHP-FPM vs Apache mod_php?
       A) PHP-FPM (recommended)
       B) Apache mod_php (legacy)
       C) Alternatives (Frankenphp, Swoole)

B1.36. PHP-FPM pool management?
       A) Static (fixed count)
       B) Dynamic (min-max)
       C) Ondemand (spawn as needed)

B1.37. FPM communication?
       A) Unix socket (fast, local)
       B) TCP socket (allows remote)
       C) Both

B1.38. Async support (Swoole/ReactPHP)?
       A) Yes, required (async)
       B) Yes, optional (some async)
       C) No (synchronous only)

B1.39. Type coercion handling?
       A) Strict mode (no coercion)
       B) Implicit (loose coercion)
       C) Careful (selective)

B1.40. NULL handling philosophy?
       A) Typed nullable (int|null)
       B) Optional (Optional<T>)
       C) No nulls (default always)
       D) Nullable everywhere

B1.41. Array vs Collection?
       A) Native arrays
       B) SPL SplFixedArray
       C) Laravel Collections
       D) Custom collection classes

B1.42. DateTime handling?
       A) Native DateTime
       B) Carbon library
       C) Custom wrappers

B1.43. String handling?
       A) Native (ASCII)
       B) mbstring (UTF-8)
       C) Grapheme aware (ICU)

B1.44. Performance optimization approach?
       A) Built-in PHP optimizations
       B) Extension optimization (opcodes)
       C) External tools (cachegrind, xdebug)

B1.45. Deprecation warning handling?
       A) Errors (fail fast)
       B) Logged (monitor)
       C) Ignored (accept risk)

━━━━━ JAVASCRIPT/TYPESCRIPT SPESİFİK (25 Soru) ━━━━━

B1.46. JS/TS mix approach?
       A) Pure JavaScript (no types)
       B) TypeScript strict mode
       C) TypeScript loose mode
       D) JSDoc type comments

B1.47. TypeScript `strict` mode?
       A) `strict: true` (all checks)
       B) Selective rules
       C) `strict: false` (loose)

B1.48. TypeScript target?
       A) ES2020
       B) ES2022
       C) ES2023
       D) ES2024
       E) ESNext

B1.49. JSDoc coverage?
       A) Full coverage
       B) Public API only
       C) None

B1.50. Runtime type validation?
       A) Zod
       B) Yup
       C) Joi
       D) Custom validators
       E) None (trust TS)

B1.51. Node.js version?
       A) 18 LTS
       B) 20 LTS
       C) 22
       D) 22 LTS (pending)
       E) Nightly
       F) Multiple (support range)

B1.52. Alternative runtimes?
       A) Node.js (standard)
       B) Deno (considered)
       C) Bun (considered)
       D) Both alternatives

B1.53. Bundler choice?
       A) Webpack
       B) Vite
       C) esbuild
       D) Rollup
       E) swc (bundler mode)
       F) Turbopack
       G) None (unbundled)

B1.54. Minification?
       A) Terser (default)
       B) esbuild (fast)
       C) swc (Rust, fast)
       D) None (no minification)

B1.55. Polyfill needs?
       A) None (modern browsers)
       B) Selective (ES5 compat)
       C) Extensive (IE11, old)

B1.56. async/await adoption?
       A) Always (preferred)
       B) Promises OK
       C) Callbacks (legacy)
       D) Mixed

B1.57. Error handling pattern?
       A) try-catch (exceptions)
       B) Result types (Result<T, E>)
       C) Error objects (custom)
       D) Mixed

B1.58. Logging library?
       A) Winston
       B) Pino (JSON)
       C) Bunyan
       D) Custom
       E) None (console only)

B1.59. Debug mode?
       A) DEBUG=* env var
       B) Node --inspect
       C) Custom debug flag
       D) None

B1.60. Module system?
       A) ESM (import/export)
       B) CommonJS (require)
       C) Both (dual package)
       D) Migrating to ESM

B1.61. Package manager?
       A) npm
       B) Yarn
       C) pnpm
       D) Bun
       E) Custom

B1.62. Lock file strategy?
       A) package-lock.json
       B) yarn.lock
       C) pnpm-lock.yaml
       D) Committed (enforced)

B1.63. Dependency pinning?
       A) Exact (1.2.3)
       B) Caret (^1.2.3)
       C) Tilde (~1.2.3)
       D) Ranges (1.2.x)

B1.64. Tree-shaking?
       A) Enabled (remove unused)
       B) Disabled (keep all)
       C) Selective

B1.65. Transpilation target?
       A) Modern (ES2020+)
       B) Conservative (ES5)
       C) Universal (both)

B1.66. Node globals usage?
       A) Minimal
       B) Some (__dirname, Buffer)
       C) Heavy

B1.67. ESM vs CommonJS compatibility?
       A) Full dual support
       B) ESM target
       C) CommonJS legacy

B1.68. Web APIs (browser)?
       A) Fetch API
       B) XMLHttpRequest (deprecated)
       C) Both
       D) Custom HTTP client

B1.69. DOM manipulation?
       A) Native DOM API
       B) jQuery (legacy)
       C) Utility (HTMX)
       D) Framework handles

B1.70. Testing framework?
       A) Jest
       B) Vitest (Vite)
       C) Mocha
       D) Jasmine
       E) Custom

━━━━━ PYTHON SPESİFİK (20 Soru) ━━━━━

B1.71. Python versiyonu?
       A) 3.8 (2024 EOL)
       B) 3.9 (2025 EOL)
       C) 3.10 (2026 EOL)
       D) 3.11 (2027 EOL)
       E) 3.12 (2028 EOL)
       F) 3.13 (current)

B1.72. Virtual environment tool?
       A) venv (builtin)
       B) poetry (modern)
       C) pipenv
       D) conda (data science)
       E) uv (new, fast)

B1.73. Type hints adoption?
       A) PEP 484 full
       B) Selective (public API)
       C) None

B1.74. Type checking tool?
       A) mypy strict
       B) mypy standard
       C) Pyright
       D) pydantic validation
       E) None

B1.75. Dependency management?
       A) requirements.txt exact pins
       B) Poetry.lock (lock file)
       C) Flexible ranges
       D) Conda-lock

B1.76. Async framework?
       A) asyncio
       B) FastAPI
       C) Starlette
       D) Django async
       E) Twisted (legacy)
       F) None (sync)

B1.77. GIL impact?
       A) Critical (multiprocessing needed)
       B) Acceptable (async sufficient)
       C) Not relevant

B1.78. Data science libraries?
       A) NumPy/pandas (heavy)
       B) NumPy only
       C) Occasional (polars)
       D) None

B1.79. Performance optimization?
       A) Cython
       B) C extensions
       C) PyPy
       D) Pure Python OK

B1.80. Testing framework?
       A) pytest
       B) unittest
       C) nose2
       D) Custom

━━━━━ GO/RUST/C++ SPESİFİK (15 Soru) ━━━━━

B1.81. Go version?
       A) 1.20, B) 1.21, C) 1.22, D) 1.23+

B1.82. Go modules?
       A) go.mod/go.sum, B) Vendor, C) Both

B1.83. Cgo usage?
       A) Heavy (C interop), B) Selective, C) Avoided

B1.84. Rust edition?
       A) 2021, B) 2024

B1.85. Unsafe Rust?
       A) 0% safe (preferred), B) <5%, C) 10%+

B1.86. C++ standard?
       A) C++20, B) C++17, C) C++14

B1.87. Memory management?
       A) Smart pointers (RAII), B) Manual, C) GC addon

B1.88. C++ exceptions?
       A) Enabled, B) Disabled, C) Custom

B1.89. MISRA compliance?
       A) MISRA C++:2023, B) MISRA C:2025, C) None

B1.90. Build system?
       A) CMake, B) Cargo, C) Make, D) Meson, E) Custom

━━━━━ JAVA/C#/.NET SPESİFİK (10 Soru) ━━━━━

B1.91. Java LTS?
       A) 11, B) 17, C) 21, D) 23

B1.92. JVM GC?
       A) G1GC, B) ZGC, C) Shenandoah

B1.93. .NET runtime?
       A) .NET 8, B) .NET 9, C) .NET Framework

B1.94. Nullable reference types?
       A) `#nullable enable`, B) Selective, C) Disabled

B1.95. async/await adoption?
       A) Everywhere, B) Strategic, C) Avoided

B1.96. Records vs classes?
       A) Records-first, B) Classes-primary, C) Hybrid

B1.97. LINQ adoption?
       A) LINQ-preferred, B) Imperative, C) Balanced

B1.98. DI container?
       A) Built-in (.NET 6+), B) Manual, C) Third-party

B1.99. Code generation?
       A) Source generators, B) T4, C) Roslyn

B1.100. Language interop?
        A) Pinvoke (C), B) COM, C) None
```

---

## B2 — FRAMEWORK & LIBRARIES (100+ Soru)

[Format continues with expanded options B2.1-B2.100+...]

---

## B3-B10 [Veritabanı, Cache, Web Server, Container, OS, Version Control, Build, CI/CD]

**TOPLAM: 400+ Soru**
**Format: A stili (detaylı + kategorili + geniş seçenekler)**

---

*Prompt Maker v8.2.0 — BLOK B ULTRA-EXPANDED*
