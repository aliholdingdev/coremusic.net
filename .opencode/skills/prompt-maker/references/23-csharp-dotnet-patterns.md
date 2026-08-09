# C# & .NET PATTERNS — TAM REHBER
# Prompt Maker v7.2.0 | 2026-06-11 | Kiro IDE Native
# ASP.NET Core, async/await, DI, middleware, EF Core, Minimal API

---

## ASP.NET CORE MİMARİSİ

### Program.cs — Minimal API (Modern)

```csharp
using Microsoft.AspNetCore.Builder;
using Microsoft.Extensions.DependencyInjection;
using Microsoft.Extensions.Hosting;

var builder = WebApplication.CreateBuilder(args);

// DI Container
builder.Services.AddScoped<ITrackRepository, SqlTrackRepository>();
builder.Services.AddScoped<ITrackService, TrackService>();
builder.Services.AddScoped<IPasswordHasher, Argon2PasswordHasher>();

// Authentication
builder.Services.AddAuthentication(JwtBearerDefaults.AuthenticationScheme)
    .AddJwtBearer(options => {
        options.TokenValidationParameters = new TokenValidationParameters {
            ValidateIssuer           = true,
            ValidateAudience         = true,
            ValidateLifetime         = true,
            ValidateIssuerSigningKey = true,
            ValidIssuer              = builder.Configuration["Jwt:Issuer"],
            ValidAudience            = builder.Configuration["Jwt:Audience"],
            IssuerSigningKey         = new SymmetricSecurityKey(
                Encoding.UTF8.GetBytes(builder.Configuration["Jwt:Key"]!)
            ),
        };
    });

builder.Services.AddAuthorization();

// Rate limiting
builder.Services.AddRateLimiter(options => {
    options.AddFixedWindowLimiter("api", limiterOptions => {
        limiterOptions.PermitLimit       = 60;
        limiterOptions.Window            = TimeSpan.FromMinutes(1);
        limiterOptions.QueueProcessingOrder = QueueProcessingOrder.OldestFirst;
        limiterOptions.QueueLimit        = 0;
    });
});

// CORS
builder.Services.AddCors(options => {
    options.AddPolicy("AllowedOrigins", policy => {
        policy.WithOrigins("https://music.coremusic.net")
              .AllowAnyMethod()
              .AllowAnyHeader();
    });
});

var app = builder.Build();

// Middleware pipeline (sıra önemli!)
if (app.Environment.IsDevelopment()) {
    app.UseDeveloperExceptionPage();
} else {
    app.UseExceptionHandler("/error");
    app.UseHsts();
}

app.UseHttpsRedirection();
app.UseCors("AllowedOrigins");
app.UseRateLimiter();
app.UseAuthentication();
app.UseAuthorization();

// Endpoints
app.MapGroup("/api/v1/tracks")
   .MapTrackEndpoints()
   .RequireRateLimiting("api");

app.Run();
```

### Minimal API Endpoints

```csharp
public static class TrackEndpoints
{
    public static RouteGroupBuilder MapTrackEndpoints(this RouteGroupBuilder group)
    {
        group.MapGet("/", GetTracks);
        group.MapGet("/{id:int}", GetTrack);
        group.MapPost("/", CreateTrack).RequireAuthorization();
        group.MapPatch("/{id:int}", UpdateTrack).RequireAuthorization();
        group.MapDelete("/{id:int}", DeleteTrack).RequireAuthorization();
        return group;
    }

    private static async Task<IResult> GetTracks(
        [FromQuery] int page = 1,
        [FromQuery] int perPage = 20,
        ITrackService trackService = default!,
        CancellationToken ct = default)
    {
        var result = await trackService.ListTracksAsync(page, perPage, ct);
        return Results.Ok(result);
    }

    private static async Task<IResult> GetTrack(
        int id,
        ITrackService trackService,
        CancellationToken ct)
    {
        var track = await trackService.GetTrackAsync(id, ct);
        return track is null
            ? Results.NotFound(new { error = "Track not found" })
            : Results.Ok(track);
    }

    private static async Task<IResult> CreateTrack(
        [FromBody] CreateTrackRequest request,
        ITrackService trackService,
        IValidator<CreateTrackRequest> validator,
        CancellationToken ct)
    {
        var validation = await validator.ValidateAsync(request, ct);
        if (!validation.IsValid) {
            return Results.ValidationProblem(validation.ToDictionary());
        }

        var track = await trackService.CreateTrackAsync(request, ct);
        return Results.Created($"/api/v1/tracks/{track.Id}", track);
    }
}
```

---

## ASYNC/AWAIT BEST PRACTICES

```csharp
#nullable enable

// ✅ DOĞRU: ConfigureAwait(false) — deadlock önleme
public async Task<Track?> GetTrackAsync(int id, CancellationToken ct = default)
{
    return await _repository.FindByIdAsync(id, ct).ConfigureAwait(false);
}

// ✅ DOĞRU: CancellationToken her async metodda
public async Task<IReadOnlyList<Track>> ListTracksAsync(
    int page,
    int perPage,
    CancellationToken ct = default)
{
    return await _repository.ListAsync(page, perPage, ct).ConfigureAwait(false);
}

// ✅ DOĞRU: ValueTask — sık çağrılan, genellikle sync tamamlanan metodlar
public ValueTask<Track?> GetFromCacheAsync(int id)
{
    if (_cache.TryGetValue(id, out var track)) {
        return ValueTask.FromResult<Track?>(track);
    }
    return new ValueTask<Track?>(FetchFromDbAsync(id));
}

// ❌ YANLIŞ: async void (exception yakalanmaz)
public async void LoadData() { /* ... */ } // YANLIŞ

// ✅ DOĞRU: async Task
public async Task LoadDataAsync() { /* ... */ }

// ❌ YANLIŞ: .Result veya .Wait() (deadlock riski)
var track = GetTrackAsync(1).Result; // YANLIŞ

// ✅ DOĞRU: await
var track = await GetTrackAsync(1);

// ✅ DOĞRU: Parallel async işlemler
var (user, tracks) = await (
    GetUserAsync(userId, ct),
    GetTracksAsync(userId, ct)
).WhenAll();

// Extension method
public static async Task<(T1, T2)> WhenAll<T1, T2>(
    this (Task<T1>, Task<T2>) tasks)
{
    await Task.WhenAll(tasks.Item1, tasks.Item2).ConfigureAwait(false);
    return (tasks.Item1.Result, tasks.Item2.Result);
}
```

---

## RECORD DTO PATTERN

```csharp
#nullable enable

// ✅ DOĞRU: Immutable Record DTO
public record CreateTrackRequest(
    string Title,
    int    ArtistId,
    int    Duration,
    string FileUrl
);

public record TrackResponse(
    int      Id,
    string   Title,
    string   ArtistName,
    int      Duration,
    string   Status,
    DateTime CreatedAt
);

public record PaginatedResponse<T>(
    IReadOnlyList<T> Data,
    int              CurrentPage,
    int              PerPage,
    int              Total,
    int              TotalPages,
    bool             HasNext,
    bool             HasPrev
);

// ✅ DOĞRU: FluentValidation
public class CreateTrackRequestValidator : AbstractValidator<CreateTrackRequest>
{
    public CreateTrackRequestValidator()
    {
        RuleFor(x => x.Title)
            .NotEmpty()
            .MaximumLength(255);

        RuleFor(x => x.ArtistId)
            .GreaterThan(0);

        RuleFor(x => x.Duration)
            .InclusiveBetween(1, 3600);

        RuleFor(x => x.FileUrl)
            .NotEmpty()
            .Must(url => Uri.TryCreate(url, UriKind.Absolute, out var uri)
                         && uri.Scheme == "https")
            .WithMessage("FileUrl must be a valid HTTPS URL");
    }
}
```

---

## DEPENDENCY INJECTION

```csharp
// ✅ DOĞRU: Interface + Implementation
public interface ITrackRepository
{
    Task<Track?> FindByIdAsync(int id, CancellationToken ct = default);
    Task<IReadOnlyList<Track>> ListAsync(int page, int perPage, CancellationToken ct = default);
    Task<Track> CreateAsync(Track track, CancellationToken ct = default);
    Task UpdateAsync(Track track, CancellationToken ct = default);
    Task DeleteAsync(int id, CancellationToken ct = default);
}

// ✅ DOĞRU: Constructor injection (field injection yasak)
public sealed class TrackService : ITrackService
{
    private readonly ITrackRepository _repository;
    private readonly IMemoryCache     _cache;
    private readonly ILogger<TrackService> _logger;

    public TrackService(
        ITrackRepository repository,
        IMemoryCache     cache,
        ILogger<TrackService> logger)
    {
        _repository = repository ?? throw new ArgumentNullException(nameof(repository));
        _cache      = cache      ?? throw new ArgumentNullException(nameof(cache));
        _logger     = logger     ?? throw new ArgumentNullException(nameof(logger));
    }

    public async Task<Track?> GetTrackAsync(int id, CancellationToken ct = default)
    {
        var cacheKey = $"track:{id}";

        if (_cache.TryGetValue(cacheKey, out Track? cached)) {
            return cached;
        }

        var track = await _repository.FindByIdAsync(id, ct).ConfigureAwait(false);

        if (track is not null) {
            _cache.Set(cacheKey, track, TimeSpan.FromMinutes(5));
        }

        return track;
    }
}

// DI Lifetime seçimi:
// Transient  → Her inject'te yeni instance (stateless, lightweight)
// Scoped     → Request başına bir instance (DbContext, Repository)
// Singleton  → Uygulama boyunca tek instance (Cache, Config)

builder.Services.AddTransient<IEmailService, SmtpEmailService>();
builder.Services.AddScoped<ITrackRepository, EfTrackRepository>();
builder.Services.AddSingleton<IMemoryCache, MemoryCache>();
```

---

## MIDDLEWARE PIPELINE

```csharp
// ✅ DOĞRU: Custom middleware
public class RequestLoggingMiddleware
{
    private readonly RequestDelegate _next;
    private readonly ILogger<RequestLoggingMiddleware> _logger;

    public RequestLoggingMiddleware(
        RequestDelegate next,
        ILogger<RequestLoggingMiddleware> logger)
    {
        _next   = next;
        _logger = logger;
    }

    public async Task InvokeAsync(HttpContext context)
    {
        var requestId = Guid.NewGuid().ToString("N")[..8];
        var stopwatch = Stopwatch.StartNew();

        context.Response.Headers["X-Request-Id"] = requestId;

        _logger.LogInformation(
            "[{RequestId}] {Method} {Path} started",
            requestId, context.Request.Method, context.Request.Path);

        try {
            await _next(context).ConfigureAwait(false);
        } finally {
            stopwatch.Stop();
            _logger.LogInformation(
                "[{RequestId}] {Method} {Path} → {StatusCode} ({ElapsedMs}ms)",
                requestId,
                context.Request.Method,
                context.Request.Path,
                context.Response.StatusCode,
                stopwatch.ElapsedMilliseconds);
        }
    }
}

// Kayıt
app.UseMiddleware<RequestLoggingMiddleware>();
```

---

## C# GÜVENLİK

```csharp
#nullable enable

// ✅ DOĞRU: Parameterized query (EF Core)
public async Task<Track?> FindByTitleAsync(string title, CancellationToken ct)
{
    return await _context.Tracks
        .Where(t => t.Title == title && t.Status == TrackStatus.Active)
        .FirstOrDefaultAsync(ct)
        .ConfigureAwait(false);
}

// ✅ DOĞRU: Raw SQL — parameterized
public async Task<IList<Track>> SearchAsync(string query, CancellationToken ct)
{
    return await _context.Tracks
        .FromSqlRaw(
            "SELECT * FROM tracks WHERE MATCH(title) AGAINST ({0} IN BOOLEAN MODE)",
            query  // EF Core otomatik parameterize eder
        )
        .ToListAsync(ct)
        .ConfigureAwait(false);
}

// ✅ DOĞRU: Şifre hash
public string HashPassword(string plainPassword)
{
    return BCrypt.Net.BCrypt.HashPassword(plainPassword, workFactor: 12);
}

public bool VerifyPassword(string plainPassword, string hash)
{
    return BCrypt.Net.BCrypt.Verify(plainPassword, hash);
}

// ✅ DOĞRU: Sensitive data masking (logging)
public class SensitiveDataDestructuringPolicy : IDestructuringPolicy
{
    private static readonly HashSet<string> SensitiveFields = new(StringComparer.OrdinalIgnoreCase) {
        "password", "token", "secret", "key", "creditCard", "ssn"
    };

    public bool TryDestructure(object value, ILogEventPropertyValueFactory factory, out LogEventPropertyValue result)
    {
        // Sensitive field'ları maskele
        result = null!;
        return false;
    }
}
```

---

## C# CHECKLIST

```
Async:
  [ ] async void yok mu? (async Task kullan)
  [ ] .Result / .Wait() yok mu?
  [ ] ConfigureAwait(false) var mı?
  [ ] CancellationToken her async metodda mı?

Nullable:
  [ ] #nullable enable aktif mi?
  [ ] Null check'ler var mı?
  [ ] ArgumentNullException.ThrowIfNull() kullanıldı mı?

DI:
  [ ] Constructor injection kullanıldı mı?
  [ ] Lifetime doğru seçildi mi?
  [ ] Interface'ler kullanıldı mı?

Security:
  [ ] Parameterized query kullanıldı mı?
  [ ] Input validation var mı? (FluentValidation)
  [ ] Şifre hash'leme doğru mu? (BCrypt/Argon2)
  [ ] Sensitive data loglanmıyor mu?

Performance:
  [ ] ValueTask kullanıldı mı? (sık çağrılan metodlar)
  [ ] IMemoryCache kullanıldı mı?
  [ ] Async stream kullanıldı mı? (büyük veri)
  [ ] EF Core N+1 önlendi mi? (Include/ThenInclude)
```

---

*C# & .NET Patterns v7.0.0 — 2026-05-29 | Kiro IDE Native*
*ASP.NET Core, Minimal API, async/await, DI, middleware, EF Core*
*Kaynak: learn.microsoft.com/dotnet, learn.microsoft.com/aspnet*
