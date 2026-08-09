# SÖZLÜK & REFERANSLAR — TAM LİSTE
# Prompt Maker v7.2.0 | 2026-06-11 | Kiro IDE Native
# Teknik terimler sözlüğü, kısaltmalar, tüm referans kaynakları

---

## SÖZLÜK — TEKNİK TERİMLER

### A

**AbortController**
JavaScript'te fetch isteklerini iptal etmek için kullanılan API.
SPA router'larda navigation değişince önceki isteği iptal etmek için zorunlu.
Kaynak: developer.mozilla.org/en-US/docs/Web/API/AbortController

**ADR (Architecture Decision Record)**
Mimari kararları belgeleyen doküman.
Format: Context → Problem → Decision → Tradeoffs → Impact
CoreMusic'te: .ai/decisions/ klasöründe saklanır.

**APCu (Alternative PHP Cache — User)**
PHP'de in-process key-value cache.
Hız: ~0.1ms. Kapsam: Tek PHP-FPM worker.
CoreMusic'te L1 cache olarak kullanılır.

**ARIA (Accessible Rich Internet Applications)**
Web içeriğini ekran okuyucular için erişilebilir kılan HTML attribute'ları.
Örnek: aria-label, aria-describedby, aria-live, aria-hidden
Kaynak: w3.org/TR/wai-aria/

**Argon2id**
Şifre hash algoritması. PHP'de PASSWORD_ARGON2ID sabiti ile kullanılır.
2015 Password Hashing Competition kazananı.
CoreMusic'te zorunlu (MD5/SHA-1 yasak).
Kaynak: php.net/manual/en/function.password-hash.php

**AST (Abstract Syntax Tree)**
Kaynak kodun ağaç yapısında temsili.
PHPStan, ESLint gibi araçlar AST üzerinde çalışır.

### B

**BEM (Block Element Modifier)**
CSS naming convention.
Format: .block__element--modifier
Örnek: .cm-track-card__title--active
CoreMusic'te ITCSS ile birlikte kullanılır.

**Biquad Filter**
İki kutuplu IIR dijital filtre.
DSP'de EQ, low-pass, high-pass için kullanılır.
Direct Form II ile implement edilir.

**Brute Force Attack**
Şifre tahmin saldırısı. Rate limiting ile önlenir.
CoreMusic'te: 5 deneme/15 dakika limiti.

### C

**Cache-Aside Pattern**
Cache stratejisi: Önce cache'e bak, yoksa DB'den oku, cache'e yaz.
CoreMusic'te APCu + Redis ile uygulanır.

**Canonical Form**
Prompt Maker'ın kullanıcı cevaplarını dönüştürdüğü standart JSON format.
Intent analysis çıktısı.

**Circuit Breaker**
Hata toleransı pattern'i. Servis başarısız olunca devre açılır.
Retry → Half-Open → Closed döngüsü.

**Clean Architecture**
Robert C. Martin'in mimari prensibi.
Katmanlar: Presentation → Application → Domain → Infrastructure
Bağımlılık yönü: Dıştan içe.

**CQRS (Command Query Responsibility Segregation)**
Okuma (Query) ve yazma (Command) işlemlerini ayıran pattern.
Büyük sistemlerde ölçeklenebilirlik için kullanılır.

**CSP (Content Security Policy)**
XSS saldırılarını önleyen HTTP header.
Hangi kaynakların yüklenebileceğini belirler.
CoreMusic'te nonce-based CSP kullanılır.

**CSRF (Cross-Site Request Forgery)**
Kullanıcı adına sahte istek gönderme saldırısı.
Önleme: CSRF token (bin2hex(random_bytes(32))).

### D

**DDD (Domain-Driven Design)**
İş mantığını merkeze alan yazılım tasarım yaklaşımı.
Kavramlar: Entity, Value Object, Aggregate, Repository, Domain Service.

**Deadlock**
İki veya daha fazla işlemin birbirini beklemesi.
Önleme: Her zaman aynı sırada lock al.

**DIP (Dependency Inversion Principle)**
SOLID'in D'si. Yüksek seviyeli modüller soyutlamalara bağımlı olmalı.
Uygulama: Interface + Constructor injection.

**DOM (Document Object Model)**
HTML dokümanının programatik temsili.
DOM-safe rendering: textContent kullan, innerHTML = userInput yasak.

**DSP (Digital Signal Processing)**
Dijital sinyalleri işleme bilimi.
Audio DSP: EQ, compressor, reverb, filter.

### E

**ESD (Electrostatic Discharge)**
Statik elektrik deşarjı. Elektronik devrelere zarar verir.
Önleme: TVS diode, seri resistor, ground plane.

**EXPLAIN**
MySQL'de sorgu planını gösteren komut.
type: ALL = full scan (kötü), ref/range = index kullanıyor (iyi).

### F

**Fail-Safe**
Hata durumunda güvenli duruma geçme prensibi.
Fail-open'ın tersi: Hata = erişim engelle (güvenli).

**Feature Flag**
Kod değişikliği olmadan özellik açma/kapama mekanizması.
Canary deployment, A/B testing için kullanılır.

**FPM (FastCGI Process Manager)**
PHP'nin web sunucusu ile iletişim kurma yöntemi.
Her istek izole worker'da çalışır (shared nothing).

### G

**GCM (Galois/Counter Mode)**
AES şifreleme modu. Authenticated encryption sağlar.
AES-256-GCM: CoreMusic'te zorunlu şifreleme algoritması.

**God Class**
Her şeyi yapan tek sınıf. Anti-pattern.
Çözüm: SRP uygula, sorumlulukları ayır.

**Guard Ring**
PCB'de yüksek empedans node'ları çevreleyen koruyucu iz.
Leakage current'ı önler.

### H

**HAL (Hardware Abstraction Layer)**
Donanım bağımsız yazılım katmanı.
STM32 HAL: ST'nin resmi sürücü kütüphanesi.

**HSTS (HTTP Strict Transport Security)**
Tarayıcıyı HTTPS kullanmaya zorlayan header.
max-age=31536000; includeSubDomains; preload

**Hexagonal Architecture**
Ports & Adapters mimarisi. Domain merkezi, adaptörler çevresinde.
Uygulama: Interface (port) + Implementation (adapter).

### I

**IDOR (Insecure Direct Object Reference)**
Kullanıcının başkasının kaynağına erişmesi.
Önleme: Resource ownership kontrolü.

**IIFE (Immediately Invoked Function Expression)**
Hemen çalışan JavaScript fonksiyonu.
CoreMusic'te zorunlu pattern: (function(CoreMusic) { ... })(...);

**ISP (Interface Segregation Principle)**
SOLID'in I'sı. Büyük interface'leri küçük, spesifik interface'lere böl.

**ITCSS (Inverted Triangle CSS)**
CSS mimarisi. Katmanlar: Settings → Tools → Generic → Elements → Objects → Components → Utilities.
CoreMusic'te zorunlu (ADR-002).

### J

**JWT (JSON Web Token)**
Stateless authentication token.
Format: header.payload.signature (Base64URL encoded).
CoreMusic'te: access token (15dk) + refresh token (7gün).

### K

**Keyset Pagination**
Cursor-based pagination. OFFSET yerine son kaydın değerini kullan.
Büyük veri setlerinde OFFSET'ten çok daha hızlı.

### L

**LCP (Largest Contentful Paint)**
Core Web Vitals metriği. En büyük içerik elementinin yüklenme süresi.
Hedef: <2.5s (iyi), <4s (kabul edilebilir).

**LSP (Liskov Substitution Principle)**
SOLID'in L'si. Alt sınıflar üst sınıfların yerine geçebilmeli.

### M

**MISRA C:2025**
Gömülü sistemler için C kodlama standardı.
Motor Industry Software Reliability Association tarafından yayınlanır.

**MVC (Model-View-Controller)**
Yazılım mimarisi pattern'i.
Model: Veri, View: Sunum, Controller: İş mantığı koordinasyonu.

### N

**N+1 Query**
Loop içinde database sorgusu. Performance anti-pattern.
Önleme: JOIN veya eager loading.

**Nonce**
"Number used once". CSP'de inline script'lere izin vermek için.
Her request'te yeni nonce üretilir: base64_encode(random_bytes(16)).

**Normalization**
Veritabanı tasarım prensibi. Veri tekrarını azaltır.
1NF → 2NF → 3NF → BCNF.

### O

**OCP (Open/Closed Principle)**
SOLID'in O'su. Genişlemeye açık, değişikliğe kapalı.

**OWASP (Open Web Application Security Project)**
Web güvenliği standartları organizasyonu.
OWASP Top 10:2025: En yaygın 10 web güvenlik açığı.

### P

**PCI DSS (Payment Card Industry Data Security Standard)**
Ödeme kartı güvenlik standardı.
v4.0 (2024): Güncel versiyon. Ödeme işleyen sistemler için zorunlu.

**PDO (PHP Data Objects)**
PHP'de veritabanı erişim katmanı.
Parameterized query ile SQL injection önler.
CoreMusic'te zorunlu (ADR-003).

**PSRR (Power Supply Rejection Ratio)**
Güç kaynağı gürültüsünün çıkışa yansıma oranı.
Hedef: >60dB@100Hz (pro audio).

### R

**RAII (Resource Acquisition Is Initialization)**
C++'da kaynak yönetimi prensibi.
Constructor'da al, destructor'da bırak.
Smart pointer'lar RAII uygular.

**Rate Limiting**
Belirli sürede maksimum istek sayısı sınırı.
CoreMusic'te: APCu ile sliding window.

**RBAC (Role-Based Access Control)**
Rol tabanlı erişim kontrolü.
Kullanıcı → Rol → İzin hiyerarşisi.

**Redis**
In-memory key-value store.
CoreMusic'te L2 cache ve session için kullanılır.

**Repository Pattern**
Veri erişim katmanını soyutlayan pattern.
Interface (domain) + Implementation (infrastructure).

**RTOS (Real-Time Operating System)**
Gerçek zamanlı işletim sistemi.
FreeRTOS, Zephyr, VxWorks.

### S

**SameSite Cookie**
CSRF saldırılarını önleyen cookie attribute.
Strict: Sadece aynı site'den gelen isteklerde gönderilir.

**Semantic HTML**
Anlam taşıyan HTML elementleri.
header, nav, main, footer, article, section, aside.

**Session Fixation**
Saldırganın session ID'yi önceden belirlemesi.
Önleme: Login sonrası session_regenerate_id(true).

**Sliding Window**
Rate limiting algoritması. Son N saniyedeki istekleri say.
OFFSET'ten daha adil dağılım sağlar.

**SNR (Signal-to-Noise Ratio)**
Sinyal gücünün gürültü gücüne oranı. dB cinsinden.
Pro audio hedefi: >100dB.

**SOLID**
5 nesne yönelimli tasarım prensibi:
S: Single Responsibility
O: Open/Closed
L: Liskov Substitution
I: Interface Segregation
D: Dependency Inversion

**SPA (Single Page Application)**
Tek sayfa uygulaması. Sayfa yenilenmeden navigation.
CoreMusic'te: PHP backend render + JS navigation.

**SRI (Subresource Integrity)**
CDN'den yüklenen kaynakların bütünlüğünü doğrulayan HTML attribute.
integrity="sha384-[hash]"

**SSRF (Server-Side Request Forgery)**
Sunucunun saldırganın belirlediği URL'ye istek göndermesi.
Önleme: URL whitelist validation, private IP engelleme.

### T

**THD+N (Total Harmonic Distortion + Noise)**
Toplam harmonik bozulma + gürültü. % cinsinden.
Pro audio hedefi: <0.01%@1kHz.

**Trusted Types**
DOM XSS'i önleyen browser API.
innerHTML gibi tehlikeli sink'lere sadece "trusted" değer kabul eder.

**TVS Diode (Transient Voltage Suppressor)**
Geçici voltaj dalgalanmalarını absorbe eden koruyucu eleman.
ESD koruması için kullanılır.

### U

**ULTRATHINK**
CoreMusic projesinde kullanılan derin analiz protokolü.
10 adımlı: Problem → Analiz → Edge case → Implementasyon → Test → Dokümantasyon.

### V

**Value Object**
Domain-Driven Design kavramı. Kimliği olmayan, değeriyle tanımlanan nesne.
Örnek: Email, Money, Address.
Immutable olmalı.

### W

**WCAG (Web Content Accessibility Guidelines)**
Web erişilebilirlik standartları. W3C tarafından yayınlanır.
WCAG 2.2 AA: CoreMusic'te minimum hedef.

**Watchdog Timer**
Gömülü sistemlerde sistem donmasını önleyen donanım zamanlayıcı.
Periyodik olarak reset edilmezse sistemi yeniden başlatır.

### X

**XSS (Cross-Site Scripting)**
Kullanıcı girdisinin HTML/JS olarak yorumlanması.
Önleme: Output encoding (htmlspecialchars), textContent, CSP.

---

## REFERANS KAYNAKLARI — TAM LİSTE

### Resmi Dokümantasyon

```
PHP:
  php.net/manual/                    → PHP resmi docs
  php.net/releases/                  → Versiyon notları
  php-fig.org/psr/                   → PSR standartları
  phpunit.de/documentation/          → PHPUnit docs

JavaScript/Web:
  developer.mozilla.org/             → MDN Web Docs
  tc39.es/proposals/                 → TC39 proposals
  whatwg.org/                        → WHATWG specs
  w3.org/TR/                         → W3C specs
  ecma-international.org/            → ECMA specs

Kiro IDE:
  kiro.dev/docs/                     → Kiro resmi docs
  kiro.dev/docs/skills/              → Skills
  kiro.dev/docs/steering/            → Steering
  kiro.dev/docs/hooks/               → Hooks
  agentskills.io/specification       → SKILL.md spec

Database:
  dev.mysql.com/doc/8.0/             → MySQL 8.0
  redis.io/docs/                     → Redis
  postgresql.org/docs/               → PostgreSQL

C/C++:
  cppreference.com/                  → C++ reference
  isocpp.org/                        → ISO C++
  misra.org.uk/                      → MISRA C:2025

C#/.NET:
  learn.microsoft.com/dotnet/        → .NET docs
  learn.microsoft.com/aspnet/        → ASP.NET Core
```

### Güvenlik Kaynakları

```
OWASP:
  owasp.org/Top10/                   → OWASP Top 10:2025
  cheatsheetseries.owasp.org/        → Cheat Sheet Series
  owasp.org/www-project-wstg/        → Testing Guide
  owasp.org/www-project-asvs/        → ASVS

PCI DSS:
  pcisecuritystandards.org/          → PCI DSS v4.0

CVE/NVD:
  nvd.nist.gov/                      → National Vulnerability Database
  cve.mitre.org/                     → CVE listesi

Browser Security:
  content-security-policy.com/       → CSP rehberi
  web.dev/articles/trusted-types     → Trusted Types
```

### Accessibility Kaynakları

```
  w3.org/WAI/WCAG22/                 → WCAG 2.2
  w3.org/TR/wai-aria/                → ARIA
  webaim.org/                        → WebAIM
  a11yproject.com/                   → A11Y Project
  dequeuniversity.com/               → Deque University
```

### Prompt Engineering Kaynakları

```
  arxiv.org/search/?query=prompt+engineering → arXiv papers
  getmaxim.ai/articles/              → Prompt Engineering 2026
  promptlayer.com/blog/              → Production LLM prompts
  learn.microsoft.com/azure/ai-services/openai/concepts/prompt-engineering
  platform.openai.com/docs/guides/prompt-engineering
  docs.anthropic.com/claude/docs/prompt-engineering
```

### Audio/DSP Kaynakları

```
  miniaud.io/docs/                   → miniaudio
  kfrlib.com/                        → KFR DSP
  asio-sdk.steinberg.net/            → ASIO SDK
  docs.microsoft.com/windows/win32/coreaudio/ → WASAPI
  jackaudio.org/                     → JACK Audio
```

### Embedded Kaynakları

```
  st.com/resource/en/reference_manual/ → STM32 reference
  arm.com/developer/                 → ARM architecture
  freertos.org/Documentation/        → FreeRTOS
  zephyrproject.org/                 → Zephyr RTOS
  misra.org.uk/                      → MISRA C:2025
```

### Performance Kaynakları

```
  web.dev/performance/               → Web performance
  developer.chrome.com/docs/devtools/ → Chrome DevTools
  use-the-index-luke.com/            → SQL indexing
  redis.io/docs/management/optimization/ → Redis optimization
```

---

## PROMPT MAKER REFERANS DOSYALARI

```
.kiro/skills/prompt-maker/
  SKILL.md                              ← Ana skill (74K)
  CHANGELOG.md                          ← Versiyon geçmişi
  EXAMPLES.md                           ← 6 örnek senaryo
  prompt örenkler.md                    ← Gerçek prompt örnekleri

  references/
    01-prompt-types-deep.md             ← 20 prompt tipi
    02-question-bank.md                 ← 420+ soru
    03-security-owasp-full.md           ← OWASP Top 10:2025
    04-language-standards-full.md       ← PHP/JS/TS/C++/C#/SQL
    05-output-templates.md              ← Çıktı şablonları
    06-deep-domain-rules.md             ← Hexagonal, SPA, Audio
    07-architecture-patterns.md         ← SOLID, Design Patterns
    08-full-example-sessions.md         ← 4 tam örnek oturum
    09-quality-scoring-rubric.md        ← 800 puanlık rubric
    10-web-research-protocol.md         ← 500+ kaynak protokolü
    11-coremusic-deep-rules.md          ← CoreMusic özel kurallar
    12-performance-testing-devops.md    ← PHPUnit, Playwright, CI/CD
    13-uiux-accessibility.md            ← WCAG 2.2, Component States
    14-embedded-audio-electronics.md    ← C++, STM32, Audio, PCB
    15-api-design-patterns.md           ← REST, OpenAPI, Rate Limiting
    16-database-design-patterns.md      ← MySQL, Indexing, Migration
    17-prompt-engineering-deep.md       ← Teknikler, anti-pattern'lar
    18-security-deep-dive.md            ← JWT, AES, CSP, XSS, SQL
    19-master-prompt-full-example.md    ← CoreMusic MASTER PROMPT
    20-kiro-hooks-steering-deep.md      ← Hook/Steering/Skill rehberi
    21-glossary-and-references.md       ← Bu dosya
    coremusic-prompts.md                ← CoreMusic şablonları
    kiro-ide-docs.md                    ← Kiro IDE referans
    multi-agent-patterns.md             ← Multi-agent patterns
    platform-formats.md                 ← Platform formatları
    prompt-types.md                     ← Prompt tipleri
    security-model.md                   ← Güvenlik modeli
    validation-engine.md                ← Validation engine
```

---

*Glossary & References v7.0.0 — 2026-05-29 | Kiro IDE Native*
*Tüm teknik terimler, kısaltmalar ve referans kaynakları*
*© 2026 CoreMusic — Bayram Ali Engineering*
