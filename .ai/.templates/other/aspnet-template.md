---
title: "CoreMusic — ASP.NET 9 / C# 13 Backend Template"
type: template
category: other
date: 2026-09-23
updated: 2026-09-23
version: 1.0.0
status: active
authority: reference
---

# CoreMusic — ASP.NET 9 / C# 13 Backend Template

**Zorunlu Bağlantılar:** [[../../.templates/index.md]] · [[../../CLAUDE.md]] · [[../../brain.md]] · [[../../.templates/other/cpp-template.md]] · [[../../log.md]]

---

## §1. Amaç

Bu şablon, CoreMusic'te **ASP.NET 9 / C# 13** kullanacak servisler için hazır backend iskeleti (minimal API, DI, middleware pipeline, veri erişimi, test) sunar.

> **Truth Mode (§1.1):** **ASP.NET, CoreMusic'te şu an kullanılmıyor — bu şablon hazır bulunduruluyor.** Kanıt: `glob('**/*.cs')` → 0 dosya, `glob('**/*.{csproj,sln}')` → 0 dosya (2026-09-23). Aktif stack PHP 8.4'tür (`AGENTS.md` §25.2: "Backend | PHP 8.4 | IMPLEMENTED"). Kullanıma geçiş ayrı ADR + owner onayı gerektirir.

### §1.1 Truth Mode Durum Tablosu

| İddia | Durum | Kanıt (glob/grep) |
|---|---|---|
| ASP.NET projede yok | ✅ doğrulandı | `**/*.cs` = 0, `**/*.csproj` = 0 |
| Aktif stack PHP 8.4 | ✅ | `AGENTS.md` §25.2, 4 composer.json |
| Şablon "hazır bulunduruluyor" | ✅ | bu dosya, `status: active` |
| `{{TECH_STACK}}` dolduruldu mu? | ⚠️ HAYIR — doldurulacak | §2.2 |

### §1.2 Kapsam Dışı Konular

| Konu | İlgili Şablon | Not |
|---|---|---|
| PHP backend (aktif) | `other/php-template.md` (varsa) / `shared/src/` | gerçek stack |
| C++/C ses motoru | `other/cpp-template.md`, `other/c-template.md` | firmware |
| ADR kaydı | `adr/adr-template.md` | bu şablon ADR değil |
| CI/CD | DevOps pipeline | `.github/workflows/` |
| Frontend | `adr/adr-frontend-template.md` | Vanilla JS/ITCSS |

---

## §2. Kapsam

| Kapsam | Kapsam Dışı |
|---|---|
| `Program.cs`, minimal API endpoint, DI | PHP Controller/Service (aktif stack) |
| Middleware pipeline (PSR-15 muadili) | C++/C firmware kodu |
| Veri erişimi (Dapper/EF karar noktası) | CSS/ITCSS frontend |
| `appsettings.json`, config, health check | Donanım PCB (hardware-template) |
| xUnit/NUnit test iskeleti | Prompt/ADR dokümanları |

- **Dosya tipi:** `*.cs`, `*.csproj` + Markdown şablon dokümanı
- **Teknoloji:** .NET 9 (LTS), C# 13, ASP.NET Core Minimal API
- **Kullanan agent:** Backend Architect (birincil), Security Engineer (ikincil — middleware)
- **Katman:** L2 (backend) · **Guardrail:** #16 (Template Mandatory)
- **Durum:** 🔵 PLANNED / hazır bulunduruluyor (§1.1)

### §2.1 Stack Eşlemesi (PHP → ASP.NET)

| CoreMusic (PHP, aktif) | ASP.NET 9 muadili | Not |
|---|---|---|
| `fast-route` | Minimal API route grupları (`MapGroup`) | §3.1 |
| PSR-15 middleware | `IMiddleware` / `IEndpointFilter` | §3.4 |
| PDO prepared | **Dapper** (hafif) veya EF Core | ⚠️ ADR gerekli |
| APCu cache | `IMemoryCache` / `IDistributedCache` | §3.5 |
| `php-di` | `IServiceCollection` (built-in DI) | §3.3 |
| PHPUnit | xUnit / NUnit | §3.6 |
| PHPStan level max | Roslyn analyzers + nullable ref types | §4.1 |

> ⚠️ **ADR-002 (PDO mandatory, ORM yasak) PHP katmanına aittir (frozen).** ASP.NET tarafında ORM muadili için **yeni ADR** gerekir; bu şablon varsayılan olarak micro-ORM **Dapper** örnekler, EF Core'u §3.3'te seçenek olarak bırakır. Kullanım öncesi karar zorunlu.

### §2.2 `{{TECH_STACK}}` Placeholder Kaydı

| Placeholder | Doldurma Kuralı | Şu Anki Değer |
|---|---|---|
| `{{TECH_STACK}}` | gerçek stack ilan edildiğinde doldur | **`PHP 8.4 (aktif) → ASP.NET 9 (PLANNED)`** |
| `{{DATE}}` | şablon kopyalandığında | kopya anındaki tarih |
| `{{SERVICE_NAME}}` | servis adı (ör. `CoreMusic.Api`) | doldurulacak |

---

## §3. Mimari

### §3.1 Program.cs — Minimal API + DI

```csharp
// Program.cs — CoreMusic ASP.NET 9 iskeleti (hazır, kullanılmadı)
using CoreMusic.Api.Middleware;
using CoreMusic.Api.Services;
using Microsoft.AspNetCore.Diagnostics.HealthChecks;

var builder = WebApplication.CreateBuilder(args);

// --- {{TECH_STACK}}: .NET 9 / C# 13 ---
builder.Services.AddProblemDetails();
builder.Services.AddHealthChecks()
    .AddMySql(builder.Configuration.GetConnectionString("CoreMusic")
        ?? throw new InvalidOperationException("Connection string yok"));

// DI kayıtları (§3.3)
builder.Services.AddSingleton<IClock, SystemClock>();
builder.Services.AddScoped<IOrderService, OrderService>();
builder.Services.AddScoped<IDbConnectionFactory, MySqlConnectionFactory>();

// JSON: camelCase + enum string (PHP API ile aynı sözleşmeli kalır)
builder.Services.ConfigureHttpJsonOptions(o =>
{
    o.SerializerOptions.PropertyNamingPolicy = System.Text.Json.JsonNamingPolicy.CamelCase;
});

var app = builder.Build();

// --- Middleware pipeline sırası (§3.4; PHP pipeline ile hizalı) ---
app.UseMiddleware<CorrelationIdMiddleware>();   // 1. trace id
app.UseMiddleware<SecurityHeadersMiddleware>(); // 2. CSP/HSTS (ADR-012 muadili)
app.UseMiddleware<RateLimitMiddleware>();       // 3. APCu 60 req/60s muadili (ADR-013)
app.UseMiddleware<AuthMiddleware>();            // 4. JWT

app.MapHealthChecks("/health");
app.MapGroup("/api/v1")
   .WithTags("v1")
   .MapOrdersEndpoints();                       // §3.2

app.Run();

namespace CoreMusic.Api
{
    // Top-level statement yanında explicit Program sınıfı (test edilebilirlik)
    public partial class Program { }
}
```

### §3.2 Endpoint Grubu — Route Tipleri

```csharp
// Endpoints/OrderEndpoints.cs
using CoreMusic.Api.Services;

namespace CoreMusic.Api.Endpoints;

public static class OrderEndpoints
{
    public static RouteGroupBuilder MapOrdersEndpoints(this RouteGroupBuilder group)
    {
        // GET /api/v1/orders?page=1 — clean URL, query binding
        group.MapGet("/", async (int page, IOrderService svc, CancellationToken ct) =>
        {
            var result = await svc.ListAsync(page, ct);
            return Results.Ok(result);           // 200 + camelCase JSON
        })
        .RequireAuthorization()
        .WithSummary("Sipariş listesi");

        // GET /api/v1/orders/{id:int} — tip güvenli route param
        group.MapGet("/{id:long}", async (long id, IOrderService svc, CancellationToken ct) =>
        {
            var order = await svc.FindAsync(id, ct);
            return order is null
                ? Results.NotFound()                                   // 404
                : Results.Ok(order);
        })
        .RequireAuthorization();

        // POST /api/v1/orders — model validation (FluentValidation muadili: DataAnnotations)
        group.MapPost("/", async (CreateOrderDto dto, IOrderService svc, CancellationToken ct) =>
        {
            if (!Validate(dto)) return Results.UnprocessableEntity(dto); // 422
            var created = await svc.CreateAsync(dto, ct);
            return Results.Created($"/api/v1/orders/{created.Id}", created);
        })
        .RequireAuthorization();

        return group;
    }

    private static bool Validate(CreateOrderDto dto) =>
        dto.Items.Count > 0 && dto.Total > 0;
}

public sealed record CreateOrderDto(IReadOnlyList<string> Items, decimal Total);
```

### §3.3 Servis + DI Yaşam Döngüsü

```csharp
// Services/OrderService.cs
using System.Data.Common;

namespace CoreMusic.Api.Services;

// Yaşam döngüsü eşlemesi: PHP request-scope → Scoped DI
public interface IOrderService
{
    Task<OrderDto?> FindAsync(long id, CancellationToken ct);
    Task<IReadOnlyList<OrderDto>> ListAsync(int page, CancellationToken ct);
    Task<OrderDto> CreateAsync(CreateOrderDto dto, CancellationToken ct);
}

public sealed class OrderService : IOrderService
{
    private readonly IDbConnectionFactory _db;
    private readonly IClock _clock;
    private readonly ILogger<OrderService> _log;

    // Constructor injection — test edilebilirlik için (IClock fake ile değiştirilir)
    public OrderService(IDbConnectionFactory db, IClock clock, ILogger<OrderService> log)
    {
        _db = db; _clock = clock; _log = log;
    }

    public async Task<OrderDto?> FindAsync(long id, CancellationToken ct)
    {
        // Dapper (micro-ORM) — parametreli sorgu, SQLi yok (ADR-002 ruhu)
        const string sql = "SELECT id, total, created_at FROM orders WHERE id = @Id";
        await using var conn = await _db.OpenAsync(ct);
        var row = await conn.QuerySingleOrDefaultAsync<OrderDto>(
            sql, new { Id = id });
        return row;
    }

    public Task<IReadOnlyList<OrderDto>> ListAsync(int page, CancellationToken ct) =>
        throw new NotImplementedException("§2.1 ⚠️ ORM/veri kararı ADR bekliyor");

    public Task<OrderDto> CreateAsync(CreateOrderDto dto, CancellationToken ct) =>
        throw new NotImplementedException("§2.1 ⚠️ ORM/veri kararı ADR bekliyor");
}

public sealed record OrderDto(long Id, decimal Total, DateTimeOffset CreatedAt);

// DI lifetime tablosu §3.3 altındaki tabloya bakınız.
```

**DI Lifetime Eşlemesi:**

| Servis | Lifetime | PHP Muadili | Not |
|---|---|---|---|
| `IOrderService` | Scoped | request scope | her istekte 1 instance |
| `IDbConnectionFactory` | Singleton | singleton pool | connection pool |
| `IClock` | Singleton | — | test fake'lenebilir |
| `IMemoryCache` | Singleton | APCu | §3.5 |
| Middleware | Singleton (state'siz) | PSR-15 | §3.4 |

### §3.4 Middleware Pipeline (IMiddleware)

```csharp
// Middleware/RateLimitMiddleware.cs — ADR-013 (APCu 60 req/60s) muadili
using System.Threading.RateLimiting;
using Microsoft.AspNetCore.RateLimiting;

namespace CoreMusic.Api.Middleware;

public sealed class RateLimitMiddleware
{
    private readonly RequestDelegate _next;
    private readonly ILogger<RateLimitMiddleware> _log;

    public RateLimitMiddleware(RequestDelegate next, ILogger<RateLimitMiddleware> log)
    {
        _next = next; _log = log;
    }

    public async Task InvokeAsync(HttpContext ctx)
    {
        // Fixed window: 60 istek / 60 saniye (PHP APCu limitiyle eşdeğer)
        var window = ctx.Request.Headers["X-Forwarded-For"].FirstOrDefault() ?? "local";
        if (!RateGate.TryAcquire(window, out var retryAfter))
        {
            ctx.Response.StatusCode = StatusCodes.Status429TooManyRequests;
            ctx.Response.Headers.RetryAfter = retryAfter.TotalSeconds.ToString("0");
            _log.LogWarning("Rate limit aşıldı: {Key}", window);
            return;                                // 429 — pipeline durur
        }

        await _next(ctx);                          // sonraki middleware
    }
}

internal static class RateGate
{
    private static readonly System.Collections.Concurrent.ConcurrentDictionary<string, Window> Gates = new();
    public static bool TryAcquire(string key, out TimeSpan retryAfter) { /* ... */ retryAfter = TimeSpan.Zero; return true; }
    private sealed class Window { }
}
```

**Pipeline Sıra Tablosu (PHP ile hizalı):**

| # | Middleware | Görev | PHP muadili | ADR |
|---|---|---|---|---|
| 1 | CorrelationId | trace id | — | — |
| 2 | SecurityHeaders | CSP/HSTS | header class | ADR-012 |
| 3 | RateLimit | 60/60s | APCu sayaç | ADR-013 |
| 4 | Auth | JWT doğrulama | session/JWT | ADR-011 |
| 5 | ExceptionHandler | 500 → ProblemDetails | hata sınıfı | — |

> **Sıra değişmez** (PHP pipeline kuralının muadili); değiştirme = ADR + Security onayı.

### §3.5 Yapılandırma + Cache

```json
// appsettings.json — .env muadili (REDACTED: secret Vault'ta)
{
  "ConnectionStrings": {
    "CoreMusic": ""
  },
  "Jwt": {
    "Issuer": "coremusic.net",
    "Audience": "api",
    "ClockSkewSeconds": 60
  },
  "RateLimit": { "PermitLimit": 60, "WindowSeconds": 60 },
  "Cache": { "AbsoluteTtlSeconds": 300 },
  "Logging": { "LogLevel": { "Default": "Information" } }
}
```

| Ayar | Kaynak | REDACTED |
|---|---|---|
| `ConnectionStrings:CoreMusic` | user-secrets / env | ✅ secret |
| `Jwt:Key` | env `JWT__Key` | ✅ secret |
| `RateLimit:*` | appsettings | ❌ |
| Feature toggle | appsettings.{Env}.json | ❌ |

### §3.6 Test İskeleti (xUnit)

```csharp
// tests/CoreMusic.Api.Tests/OrderServiceTests.cs
namespace CoreMusic.Api.Tests;

public class OrderServiceTests
{
    [Fact]
    public async Task FindAsync_BulunamayanId_NullDonmeli()
    {
        // Arrange
        var svc = new OrderService(new FakeDb(), new FixedClock(), NullLogger<OrderService>.Instance);

        // Act
        var result = await svc.FindAsync(404, CancellationToken.None);

        // Assert
        Assert.Null(result);
    }

    [Theory]
    [InlineData(0, 0, false)]   // boş sipariş
    [InlineData(1, 10, true)]   // geçerli
    public void Validate_Kurallari_Dogrular(int n, decimal total, bool expected)
    {
        var dto = new CreateOrderDto(new List<string>(new string[n]), total);
        // §3.2 Validate örtülü; eşleştirici test buraya
    }
}
```

| Test Katmanı | Araç | Hedef |
|---|---|---|
| Unit | xUnit | servis mantığı |
| Integration | WebApplicationFactory | pipeline §3.4 |
| E2E | Playwright (var) | UI↔API |

---

## §4. Kurallar

### §4.1 Hard Guardrails (C# / .NET)

| # | Kural | İhlal Sonucu |
|---|---|---|
| 1 | `Nullable enable` + analyzer'lar (`TreatWarningsAsErrors`) | null NRE |
| 2 | Async API'de `CancellationToken` zorunlu | asılı istek |
| 3 | SQL **sadece parametreli** (Dapper `@Param`) | SQLi (ADR-002 ruhu) |
| 4 | Middleware sırası §3.4'te değişmez | CSP/RateLimit bozulması |
| 5 | Secret appsettings'e yazılmaz (REDACTED/Vault) | sızıntı |
| 6 | `async void` yasak | unobserved exception |
| 7 | Endpoint'lerde `async/await` zinciri (sync-over-async yasak) | thread havuzu blokajı |

### §4.2 Yasaklı Örüntüler

```csharp
// ❌ YASAK — secret appsettings'te
"ApiKey": "cm_live_1234567890"          // ❌ REDACTED ihlali

// ✅ DOĞRU — env / Vault
builder.Configuration["ApiKey"]          // env'den oku, log'lama

// ❌ YASAK — sync-over-async
public string Get() => _svc.GetAsync().Result;   // ❌ deadlock riski

// ✅ DOĞRU — await + CancellationToken
public async Task<string> GetAsync(CancellationToken ct) =>
    await _svc.GetAsync(ct);

// ❌ YASAK — parametresiz SQL (dinamik string)
$"... WHERE id = {id}"                  // ❌ SQLi

// ✅ DOĞRU — parametreli
await conn.QueryAsync(sql, new { Id = id });
```

Ek kurallar:

- **Zorunlu:** her endpoint'in `Produces<T>`/özgün status kodu belgelenir (404/422/429 §3.2).
- **Zorunlu:** `{{TECH_STACK}}`, `{{SERVICE_NAME}}`, `{{DATE}}` doldurulmadan commit yasak (Guardrail #16).
- **Yasak:** bu şablon aktif ilan edilmeden PHP kod yerine EF/ASP.NET konması (§1 Truth Mode → ADR gerekli).
- **Standart:** .NET 9 SDK, C# 13, `dotnet format` + analyzer temizliği.
- **Uyarı:** doğrulanamayan API `⚠️ VERIFICATION REQUIRED` (ADR-005 ruhu).

---

## §5. Workflow

```text
ŞABLONU SEÇ → KOPYALA → {{PLACEHOLDER}} DOLDUR → TRUTH MODE (§1) → GUARDRAIL #16 → COMMIT
```

1. **ŞABLONU SEÇ:** `.ai/.templates/other/aspnet-template.md` (Guardrail #16).
2. **KOPYALA:** §3.1→`Program.cs`, §3.2→`Endpoints/`, §3.3→`Services/`, §3.4→`Middleware/`, §3.5→`appsettings.json`, §3.6→`tests/`.
3. **{{PLACEHOLDER}} DOLDUR:** `{{TECH_STACK}}` (§2.2), `{{SERVICE_NAME}}`, `{{DATE}}`.
4. **TRUTH MODE DOĞRULA:** "kullanılmıyor → hazır" ifadesi silinmedi (§1.1); ORM kararı için ADR açıldı mı (§2.1)?
5. **GUARDRAIL #16 DOĞRULA:** 7 alanlı frontmatter + §1-§7 + placeholder yok + §4.1/§4.2 ihlali yok + `dotnet build -warnaserror` temiz.
6. **COMMIT:** PHP aktif stack'e çelişki yoksa onayla, `log.md`'ye giriş ekle.

---

## §6. Doğrulama

- [ ] 7 alanlı frontmatter var (title, type, category, date, updated, version, status, authority)
- [ ] §1-§7 var; §1'de "şu an kullanılmıyor, hazır bulunduruluyor" ifadesi mevcut (Truth Mode)
- [ ] tüm `{{PLACEHOLDER}}`'lar dolduruldu VEYA §2.2 kayıtlı
- [ ] dosya bu şablona uygun (ASP.NET backend)
- [ ] §4.1 guardrails + §4.2 yasaklı örüntüler geçti (secret/SQLi/async yok)
- [ ] `glob('**/*.cs')` sonucu §1.1 iddiasıyla tutarlı (şu an: 0)

### §6.1 Endpoint Hata Kodu Sözleşmesi (PHP API ile hizalı)

| # | Durum | Koşul | Gövde (ProblemDetails) | Client davranışı |
|---|---|---|---|---|
| 1 | 200 | GET başarılı | kaynak JSON | render |
| 2 | 201 | POST oluşturuldu | `Location` başlığı + kaynak | navigasyon |
| 3 | 204 | DELETE başarılı | boş | listeyi yenile |
| 4 | 400 | şema ihlali | `title`, `status`, `errors[]` | form göster |
| 5 | 401 | token yok/geçersiz | `title=Unauthorized` | yeniden auth (§3.4) |
| 6 | 403 | rol yetmiyor | `title=Forbidden` | engelli ekranı |
| 7 | 404 | kaynak yok | `title=NotFound` | boş durum |
| 8 | 409 | çakışma (UNIQUE) | `detail=konu` | yeniden dene |
| 9 | 422 | iş kuralı (Validate) | `errors[]` alan bazlı | hatayı alana bas |
| 10 | 429 | rate limit (§3.4) | `Retry-After` başlığı | backoff |
| 11 | 500 | beklenmeyen | `traceId` (CorrelationId) | genel hata + traceId kopyala |
| 12 | 503 | bağımlı servis kapalı | `Retry-After` | tekrar dene |

```csharp
// Hata gövdesi yardımcı kodu (§3.4 ExceptionHandler ile kullanılır)
static IResult ApiError(HttpContext ctx, int status, string title, params (string,string)[] errors) =>
    Results.Problem(
        statusCode: status,
        title: title,
        detail: ctx.TraceIdentifier,           // traceId (500'de müşteriye verilir)
        extensions: new Dictionary<string, object?>
        {
            ["errors"] = errors.ToDictionary(e => e.Item1, e => e.Item2)
        });
```

**REFACTOR REPORT:** FILE: aspnet-template.md · PURPOSE: ASP.NET 9 / C# 13 Backend Template (hazır bulunduruluyor) · VALIDATION: 7 alan + §1-§7 + Truth Mode §1.1 + bilgi korunumu · RELATED: [[../../.templates/index.md]] · [[../../CLAUDE.md]]

---

## §7. Referanslar

- [[../../.templates/index.md]] — şablon registry
- [[../../CLAUDE.md]] — AI anayasası, Hard Guardrails
- [[../../AGENTS.md]] — routing (§6: backend/API → Backend Architect; §25.2 stack kanıtı = PHP 8.4 IMPLEMENTED)
- [[../../.templates/other/cpp-template.md]] — kalıp kaynak (§1-§7 iskeleti)
- `.ai/brain.md` — §13 ADR listesi

İlgili ADR'ler (§2.1 karar kapıları):

| ADR | Konu | Bu Şablondaki Etki |
|---|---|---|
| ADR-002 | PDO mandatory, ORM yasak | ⚠️ ORM muadili için YENİ ADR gerekli |
| ADR-011 | COREMUSIC_SESS, 3600s idle timeout | §3.4 Auth muadili |
| ADR-012 | strict-dynamic, nonce-based CSP | §3.4 SecurityHeaders |
| ADR-013 | APCu, 60 req/60s | §3.4 RateLimit muadili |
| ADR-006 | <200ms TTFB, <100ms API | §3.6 performans testi hedefi |

> ⚠️ ADR dosyaları diskte ayrı `.md` olarak bulunmamaktadır (bkz. [[../../.templates/adr/adr-index.md]] §2.0); numaralar `brain.md` §13.1 kaynaklıdır.

---

### §7.1 Paket / Sürüm Kaydı (kullanıma geçişte doldur)

| Paket | Amaç | Sürüm (kilitli) | Not |
|---|---|---|---|
| `Microsoft.AspNetCore.App` | framework | 9.0.x | runtime LTS |
| `Dapper` | micro-ORM (§2.1 karar) | 2.1.x | ADR bekliyor |
| `MySqlConnector` | MySQL sürücüsü | 2.3.x | PDO muadili |
| `Microsoft.Extensions.Caching.Memory` | IMemoryCache (§3.5) | 9.0.x | APCu muadili |
| `Microsoft.AspNetCore.RateLimiting` | §3.4 | 9.0.x | ADR-013 muadili |
| `xunit` + `xunit.runner.visualstudio` | test (§3.6) | 2.9.x | PHPUnit muadili |
| `Microsoft.NET.Test.Sdk` | test altyapısı | 17.x | — |

| Sürümleme Kuralı | Değer |
|---|---|
| NuGet.lock | commit zorunlu (supply-chain) |
| `dotnet list package --vulnerable` | CI'da 0 high |
| Yükseltme | minor otomatik / major → inceleme |
| `{{TECH_STACK}}` | §2.2'deki değere hizalı kalmalı |

---

**Template Version:** 1.0.0
**Last Updated:** 2026-09-23
