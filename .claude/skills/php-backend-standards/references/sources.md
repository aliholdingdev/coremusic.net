---
title: "php-backend-standards — Verified Sources"
type: reference
category: skills
version: 4.0.0
status: active
date: 2026-10-21
updated: 2026-10-21
---

# Kaynak Defteri — php-backend-standards

> **Amaç:** Bu skill içindeki her teknoloji iddiasının arkasındaki resmi kaynağı
> taşır. Erişim tarihi **2026-10-21**. Bir iddia bu listede yoksa ve disk kanıtı
> da yoksa → `⚠️ VERIFICATION REQUIRED` etiketi zorunludur (Zero-Hallucination).

## §1 Web Kaynakları (erişim: 2026-10-21)

| # | Kaynak | URL | Doğrulanan |
|---|--------|-----|------------|
| 1 | PSR-15: HTTP Server Request Handlers (PHP-FIG) | https://www.php-fig.org/psr/psr-15/ | `MiddlewareInterface::process(ServerRequestInterface, RequestHandlerInterface)` imzası; `RequestHandlerInterface::handle()`; exception middleware'lerin **ilk/en dışta** olması önerilir (SHOULD) |
| 2 | PSR-14: Event Dispatching (PHP-FIG) | https://www.php-fig.org/psr/psr-14/ | Event/Listener/Emitter/Dispatcher/ListenerProvider tanımları; `EventDispatcherInterface::dispatch(object)`; `ListenerProviderInterface::getListenersForEvent(): iterable`; `StoppableEventInterface::isPropagationStopped(): bool`; listener hatası diğer listener'ları bloklar ve yayılır; dispatcher listener'ları **senkron** ve provider sırasıyla çağırır |
| 3 | PSR-12: Extended Coding Style (PHP-FIG) | https://www.php-fig.org/psr/psr-12/ | PSR-2'yi genişletir/yerine geçer, PSR-1'i şart koşar; `declare(strict_types=1)` yazımı **boşluksuz** (`declare(strict_types=1);`); 4 boşluk girinti; 120 karakter yumuşak limit; küçük harf anahtar kelimeler; her property/method'ta görünürlük belirteci; kapanış `?>` yazılmaz |
| 4 | PSR-7: HTTP Message Interfaces (PHP-FIG) | https://www.php-fig.org/psr/psr-7/ | `ServerRequestInterface` superglobal (`$_SERVER/$_GET/$_POST/$_COOKIE/$_FILES`) soyutlaması; mesaj **değiştirilemez** (immutability — `with*` yeni örnek döner); `getParsedBody(): null\|array\|object`; `getAttribute($name, $default = null)`; `withAttribute()` |
| 5 | OWASP Top 10:2025 — A05:2025 Injection | https://owasp.org/Top10/2025/A05_2025-Injection/ | Injection #5'e düştü; **%100** uygulama test edildi; **37 CWE**; XSS (CWE-79) **30k+ CVE**, SQLi (CWE-89) **14k+ CVE**; CWE listesi (20, 74, 77, 78, 79, 89, 90, 94, 95, 98, 113, 470, 643, 917 …) |
| 6 | PHP — Supported Versions | https://www.php.net/supported-versions.php | 8.2 security yalnız → 31 Dec 2026 · 8.3 → 31 Dec 2027 · **8.4** active 31 Dec 2026 / security 31 Dec 2028 · 8.5 initial 20 Nov 2025 · model: 2 yıl active + 2 yıl security |
| 7 | PHP 8.4 — Release Announcement | https://www.php.net/releases/8.4/en.php | Property hooks · asymmetric visibility (`public private(set)`) · `#[\Deprecated]` · yeni DOM API · `array_find/array_any/array_all` · **PDO driver alt sınıfları** (`Pdo\Mysql`, `Pdo\Pgsql`, `Pdo\Sqlite` …) + `PDO::connect()` · `new MyClass()->method()` parantezsiz · IMAP/OCI8/PDO_OCI/pspell paketlerinden çıkarıldı · örtük nullable parametreler **deprecated** |
| 8 | PHP Manual — `PDO::prepare()` | https://www.php.net/manual/en/pdo.prepare.php | `prepare(string $query, array $options = []): PDOStatement\|false`; `:name` ve `?` işaretcileri **aynı şablonda karıştırılamaz**; işaretciler yalnız **tam veri literali** temsil eder (tablo/sütun/anahtar kelime bağlanamaz); `IN()` içindeki çoklu değer tek parametreyle bağlanamaz; kullanıcı girdisi sorguya doğrudan yazılmaz |
| 9 | PHP Manual — PDO MySQL Driver | https://www.php.net/manual/en/ref.pdo-mysql.php | **PDO_MYSQL emulated prepares kullanır (varsayılan TRUE)**; `PDO::MYSQL_ATTR_LOCAL_INFILE`, `PDO::MYSQL_ATTR_INIT_COMMAND`, `PDO::MYSQL_ATTR_FOUND_ROWS` vb. sürücüye özgü sabitler |
| 10 | MySQL — 2.7 Client Programming Security | https://dev.mysql.com/doc/mysql-security-excerpt/8.0/en/secure-client-programming.html | Yalnız string değil **sayısal** girdiler de doğrulanmalı (`234 OR 1=1`); MySQL sunucu hatasının istemciye ham gösterilmesi **CWE-209** (log'a yaz, istemciye "Internal Error" dön); PHP için `mysqli`/`pdo_mysql` + placeholder kullanımı önerilir |
| 11 | FastRoute (nikic) — README | https://github.com/nikic/FastRoute/blob/master/README.md | `simpleDispatcher()` / `addRoute($method, $pattern, $handler)`; `{id:\d+}` placeholder sözdizimi; `Dispatcher::NOT_FOUND=0 · FOUND=1 · METHOD_NOT_ALLOWED=2`; **405 yanıtında `Allow:` başlığı zorunlu (HTTP spec)**; grup rotaları `addGroup()`; `cachedDispatcher()` ile önbellek; PHP >= 8.1 |

## §2 Disk Kaynakları (repo içi — SSOT, web'in üstünde)

| Dosya | Ne verir |
|-------|----------|
| `.ai/CLAUDE.md` §6 | Frozen middleware pipeline (10 kapı + Controller) |
| `.ai/CLAUDE.md` §6A | API-First: `OpenAPI Spec → DTO → Contract → Validation → Use Case → Kod` · BFF tablosu · CQRS · Event Driven · SPA→ApiClient |
| `.ai/.decisions/accepted/ADR-002-pdo-mandatory-no-orm.md` | PDO zorunlu, ORM yasak |
| `.ai/.decisions/accepted/ADR-010-csrf-protection-strategy.md` | `csrf_token` anahtarı, katmanlar |
| `.ai/.decisions/accepted/ADR-084-api-gateway-architecture.md` | Gateway/BFF, CQRS, OpenAPI durumu |
| `.ai/.decisions/accepted/ADR-086-event-driven-architecture.md` | PSR-14 altyapısı, 12 event |
| `.ai/.templates/backend/php-template.md` | 8 Hard Guardrail + 7 kod bloğu + BCNF 6 kural |
| `.ai/.templates/documentation/api-doc-template.md` | `{status,data,meta}` sarmalayıcı · 7 error code · 9 HTTP kodu |
| `shared/src/Middleware/*.php` | 11 middleware dosyası (disk kanıtı) |
| `shared/src/Api/**` | Gateway, RouteTable, ApiMiddlewarePipeline (disk kanıtı) |

## §3 Doğrulanamayanlar (etiket zorunlu)

| İddia | Durum |
|-------|-------|
| ADR-083 dosyası diskte var | ⚠️ VERIFICATION REQUIRED — `.ai/.decisions/index.md:127` → `<!-- NO FILE on disk 2026-10-06 — brain.md only -->` |
| OpenAPI spec dosyası (`openapi*.{yaml,yml,json}`) diskte var | ❌ 0 glob — iddia edilmez |
| CQRS implemente edildi | ❌ kodda 0 grep isabeti (ADR-084) → PLANNED |
| BFF Admin / Car | PLANNED (§6A.2 tablosu hedef) |
| `shared/src/Events/**` dışından production event dispatch | ❌ 0 → wiring PLANNED (ADR-086) |
| `.ai/architecture/k9-api-routing/middleware-pipeline.md` | ❌ diskte YOK — dead link |
| BCNF self-declaration (18 DB) | denetim PLANNED (ADR-033) |
