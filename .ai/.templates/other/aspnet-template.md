---
type: template
category: backend
title: "ASP.NET Template"
date: 2026-08-06
updated: 2026-08-06
status: active
version: 3.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team • Human Mode • Truth Mode
tech: ASP.NET 9, C# 13, Entity Framework, Blazor
---

# ASP.NET Template

**See also:** [[index]] · [[CLAUDE.md]]

---

## 1. Amaç (Purpose)

Bu dosya, CoreMusic ekosisteminde ASP.NET 9 / C# 13 ile yazılabilecek **yardımcı (auxiliary) araçlar ve servisler** için şablon ve standartları tanımlar.

> **⚠️ ÖNEMLİ NOT:** CoreMusic'in birincil yığını (primary stack) **PHP 8.4**'tür. ASP.NET, CoreMusic ekosisteminde sadece以下 durumlar için düşünülebilir:
> - Monitoring dashboard (admin araçları)
> - Internal microservice (Yapay zeka pipeline yardımcı servisleri)
> - Windows-specific tools (WASAPI/ASIO ile etkileşim gerektiren araçlar)
> - Standalone CLI utilities (FFmpeg wrapper, audio analysis tools)

### 1.1 Kapsam (In-Scope)

| Alan | Açıklama |
|------|----------|
| Internal admin tools | Monitoring, log analysis, reporting |
| Windows desktop utilities | Audio device enumeration, driver management |
| Standalone microservices | AI inference, audio processing helpers |
| Blazor Server dashboards | Real-time monitoring panels |
| CLI tools | FFmpeg wrappers, audio file analysis |

### 1.2 Kapsam Dışı (Out-of-Scope)

| Alan | Neden |
|------|-------|
| Ana backend API | CoreMusic PHP 8.4 (ADR-042) |
| Frontend SPA | Vanilla JS (ADR-001) |
| Veritabanı şeması | Data Engineer sorumluluğunda |
| Authentication core | Auth.coremusic.net PHP 8.4 |

---

## 2. Tech Stack

| Bileşen | Versiyon | Kaynak |
|---------|----------|--------|
| ASP.NET Core | 9.0 | [dotnet.microsoft.com](https://dotnet.microsoft.com) |
| C# | 13.0 | [docs.microsoft.com/csharp](https://docs.microsoft.com/dotnet/csharp) |
| Entity Framework Core | 9.0 | [docs.microsoft.com/ef](https://docs.microsoft.com/ef/core) |
| Blazor | .NET 9 | [docs.microsoft.com/blazor](https://docs.microsoft.com/aspnet/core/blazor) |
| xUnit | 6.x | [xunit.net](https://xunit.net) |
| Serilog | 4.x | [serilog.net](https://serilog.net) |
| FluentValidation | 11.x | [fluentvalidation.net](https://fluentvalidation.net) |

> **⚠️ N/A — CoreMusic uses PHP 8.4** for primary backend services (ADR-042).

---

## 3. Code Standards

### 3.1 Project Structure

```
src/
├── MyApp.Api/                    # Web API host
│   ├── Controllers/
│   │   ├── MusicController.cs
│   │   └── HealthController.cs
│   ├── Middleware/
│   │   ├── CorrelationIdMiddleware.cs
│   │   └── RequestLoggingMiddleware.cs
│   ├── Filters/
│   │   └── ValidationFilter.cs
│   ├── Program.cs
│   └── appsettings.json
├── MyApp.Application/            # Business logic (Use Cases)
│   ├── Interfaces/
│   │   ├── IMusicService.cs
│   │   └── IAudioAnalyzer.cs
│   ├── Services/
│   │   ├── MusicService.cs
│   │   └── AudioAnalyzer.cs
│   ├── DTOs/
│   │   ├── MusicTrackDto.cs
│   │   └── AlbumDto.cs
│   └── Validators/
│       ├── MusicTrackValidator.cs
│       └── AlbumValidator.cs
├── MyApp.Domain/                 # Entities, Value Objects, Enums
│   ├── Entities/
│   │   ├── MusicTrack.cs
│   │   ├── Artist.cs
│   │   └── Album.cs
│   ├── ValueObjects/
│   │   ├── AudioFormat.cs
│   │   └── Duration.cs
│   ├── Enums/
│   │   ├── Genre.cs
│   │   └── AudioQuality.cs
│   └── Interfaces/
│       ├── IRepository.cs
│       └── IUnitOfWork.cs
├── MyApp.Infrastructure/         # EF Core, external services
│   ├── Data/
│   │   ├── AppDbContext.cs
│   │   ├── Configurations/
│   │   │   ├── MusicTrackConfiguration.cs
│   │   │   └── ArtistConfiguration.cs
│   │   └── Migrations/
│   ├── Services/
│   │   ├── FileStorageService.cs
│   │   └── AudioMetadataService.cs
│   └── DependencyInjection.cs
└── MyApp.Tests/                  # Test projects
    ├── Unit/
    │   ├── MusicServiceTests.cs
    │   └── ValidatorTests.cs
    ├── Integration/
    │   └── MusicApiTests.cs
    └── Fixtures/
        └── TestDatabaseFixture.cs
```

### 3.2 Service Registration (DI Container)

```csharp
// Program.cs — Service Registration
var builder = WebApplication.CreateBuilder(args);

// Scoped: One instance per HTTP request
builder.Services.AddScoped<IMusicRepository, MusicRepository>();
builder.Services.AddScoped<IMusicService, MusicService>();
builder.Services.AddScoped<IUnitOfWork, UnitOfWork>();

// Transient: New instance every time it's requested
builder.Services.AddTransient<IAudioAnalyzer, AudioAnalyzer>();
builder.Services.AddTransient<ICsvExporter, CsvExporter>();

// Singleton: One instance for the entire application lifetime
builder.Services.AddSingleton<ICacheService, MemoryCacheService>();
builder.Services.AddSingleton<IAudioDeviceEnumerator, AudioDeviceEnumerator>();

// Hosted Services
builder.Services.AddHostedService<DatabaseMigrationService>();
builder.Services.AddHostedService<AudioIndexingService>();
```

> **⚠️ N/A — CoreMusic uses PHP 8.4** for primary backend services (ADR-002).

### 3.3 Controller Patterns

```csharp
[ApiController]
[Route("api/v{version:apiVersion}/[controller]")]
[ApiVersion("1.0")]
public class MusicController : ControllerBase
{
    private readonly IMusicService _musicService;
    private readonly ILogger<MusicController> _logger;

    public MusicController(IMusicService musicService, ILogger<MusicController> logger)
    {
        _musicService = musicService ?? throw new ArgumentNullException(nameof(musicService));
        _logger = logger ?? throw new ArgumentNullException(nameof(logger));
    }

    /// <summary>
    /// Gets a music track by ID.
    /// </summary>
    [HttpGet("{id:guid}")]
    [ProducesResponseType(typeof(MusicTrackDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    [ResponseCache(Duration = 60)]
    public async Task<IActionResult> GetById(Guid id, CancellationToken cancellationToken)
    {
        _logger.LogInformation("Fetching music track {TrackId}", id);

        var track = await _musicService.GetByIdAsync(id, cancellationToken);

        if (track is null)
        {
            _logger.LogWarning("Music track {TrackId} not found", id);
            return NotFound(new { Error = "Track not found", TrackId = id });
        }

        return Ok(track);
    }

    /// <summary>
    /// Creates a new music track.
    /// </summary>
    [HttpPost]
    [Authorize(Policy = "AdminOnly")]
    [ProducesResponseType(typeof(MusicTrackDto), StatusCodes.Status201Created)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<IActionResult> Create(
        [FromBody] CreateMusicTrackRequest request,
        CancellationToken cancellationToken)
    {
        var track = await _musicService.CreateAsync(request, cancellationToken);
        return CreatedAtAction(nameof(GetById), new { id = track.Id }, track);
    }

    /// <summary>
    /// Updates an existing music track.
    /// </summary>
    [HttpPut("{id:guid}")]
    [Authorize(Policy = "AdminOnly")]
    [ProducesResponseType(typeof(MusicTrackDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    public async Task<IActionResult> Update(
        Guid id,
        [FromBody] UpdateMusicTrackRequest request,
        CancellationToken cancellationToken)
    {
        var track = await _musicService.UpdateAsync(id, request, cancellationToken);
        return Ok(track);
    }

    /// <summary>
    /// Deletes a music track.
    /// </summary>
    [HttpDelete("{id:guid}")]
    [Authorize(Policy = "SuperAdmin")]
    [ProducesResponseType(StatusCodes.Status204NoContent)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    public async Task<IActionResult> Delete(Guid id, CancellationToken cancellationToken)
    {
        await _musicService.DeleteAsync(id, cancellationToken);
        return NoContent();
    }
}
```

### 3.4 Entity Framework

```csharp
// DbContext
public class AppDbContext : DbContext
{
    public DbSet<MusicTrack> MusicTracks => Set<MusicTrack>();
    public DbSet<Artist> Artists => Set<Artist>();
    public DbSet<Album> Albums => Set<Album>();

    public AppDbContext(DbContextOptions<AppDbContext> options) : base(options) { }

    protected override void OnModelCreating(ModelBuilder modelBuilder)
    {
        modelBuilder.ApplyConfigurationsFromAssembly(typeof(AppDbContext).Assembly);
        base.OnModelCreating(modelBuilder);
    }

    public override async Task<int> SaveChangesAsync(CancellationToken cancellationToken = default)
    {
        foreach (var entry in ChangeTracker.Entries<AuditableEntity>())
        {
            switch (entry.State)
            {
                case EntityState.Added:
                    entry.Entity.CreatedAt = DateTime.UtcNow;
                    break;
                case EntityState.Modified:
                    entry.Entity.UpdatedAt = DateTime.UtcNow;
                    break;
            }
        }

        return await base.SaveChangesAsync(cancellationToken);
    }
}

// Entity Configuration
public class MusicTrackConfiguration : IEntityTypeConfiguration<MusicTrack>
{
    public void Configure(EntityTypeBuilder<MusicTrack> builder)
    {
        builder.ToTable("music_tracks");

        builder.HasKey(e => e.Id);
        builder.Property(e => e.Id)
            .HasColumnName("id")
            .ValueGeneratedNever();

        builder.Property(e => e.Title)
            .HasColumnName("title")
            .HasMaxLength(256)
            .IsRequired();

        builder.Property(e => e.Duration)
            .HasColumnName("duration")
            .HasPrecision(10, 2);

        builder.Property(e => e.FilePath)
            .HasColumnName("file_path")
            .HasMaxLength(1024)
            .IsRequired();

        builder.HasIndex(e => e.Title);
        builder.HasIndex(e => e.ArtistId);
    }
}

// Repository Pattern
public class MusicRepository : IMusicRepository
{
    private readonly AppDbContext _context;

    public MusicRepository(AppDbContext context)
    {
        _context = context ?? throw new ArgumentNullException(nameof(context));
    }

    public async Task<MusicTrack?> GetByIdAsync(Guid id, CancellationToken ct = default)
    {
        return await _context.MusicTracks
            .AsNoTracking()
            .Include(t => t.Artist)
            .Include(t => t.Album)
            .FirstOrDefaultAsync(t => t.Id == id, ct);
    }

    public async Task<List<MusicTrack>> GetAllAsync(int page, int pageSize, CancellationToken ct = default)
    {
        return await _context.MusicTracks
            .AsNoTracking()
            .Include(t => t.Artist)
            .OrderBy(t => t.Title)
            .Skip((page - 1) * pageSize)
            .Take(pageSize)
            .ToListAsync(ct);
    }

    public async Task AddAsync(MusicTrack entity, CancellationToken ct = default)
    {
        await _context.MusicTracks.AddAsync(entity, ct);
    }

    public void Update(MusicTrack entity)
    {
        _context.MusicTracks.Update(entity);
    }

    public void Remove(MusicTrack entity)
    {
        _context.MusicTracks.Remove(entity);
    }
}
```

### 3.5 Authentication

> **⚠️ N/A — CoreMusic uses PHP 8.4 session-based auth** (ADR-011, ADR-043).

```csharp
// JWT Authentication
builder.Services.AddAuthentication(options =>
{
    options.DefaultAuthenticateScheme = JwtBearerDefaults.AuthenticationScheme;
    options.DefaultChallengeScheme = JwtBearerDefaults.AuthenticationScheme;
})
.AddJwtBearer(options =>
{
    options.TokenValidationParameters = new TokenValidationParameters
    {
        ValidateIssuer = true,
        ValidateAudience = true,
        ValidateLifetime = true,
        ValidateIssuerSigningKey = true,
        ValidIssuer = builder.Configuration["Jwt:Issuer"],
        ValidAudience = builder.Configuration["Jwt:Audience"],
        IssuerSigningKey = new SymmetricSecurityKey(
            Encoding.UTF8.GetBytes(builder.Configuration["Jwt:Key"]!)),
        ClockSkew = TimeSpan.FromMinutes(1)
    };

    options.Events = new JwtBearerEvents
    {
        OnAuthenticationFailed = context =>
        {
            var logger = context.HttpContext.RequestServices
                .GetRequiredService<ILogger<Program>>();
            logger.LogWarning("Authentication failed: {Error}", context.Exception.Message);
            return Task.CompletedTask;
        }
    };
});
```

### 3.6 Authorization

```csharp
builder.Services.AddAuthorization(options =>
{
    options.AddPolicy("AdminOnly", policy =>
        policy.RequireRole("Admin"));

    options.AddPolicy("SuperAdmin", policy =>
        policy.RequireRole("Admin", "SuperAdmin"));

    options.AddPolicy("MusicRead", policy =>
        policy.RequireClaim("permission", "music.read"));

    options.AddPolicy("MusicWrite", policy =>
        policy.RequireClaim("permission", "music.write"));
});
```

### 3.7 Middleware Pipeline

```csharp
var app = builder.Build();

// Ordering matters — pipeline order is critical
app.UseMiddleware<CorrelationIdMiddleware>();      // 1. Correlation ID
app.UseMiddleware<RequestLoggingMiddleware>();     // 2. Request logging
app.UseExceptionHandler("/error");                // 3. Exception handling
app.UseHsts();                                    // 4. HSTS
app.UseHttpsRedirection();                        // 5. HTTPS redirect
app.UseStaticFiles();                             // 6. Static files
app.UseRouting();                                 // 7. Routing
app.UseCors("AllowSpecificOrigins");              // 8. CORS
app.UseAuthentication();                          // 9. Authentication
app.UseAuthorization();                           // 10. Authorization
app.UseResponseCaching();                         // 11. Response caching
app.UseOutputCache();                             // 12. Output caching
app.MapControllers();                             // 13. Controllers
app.MapHealthChecks("/health");                   // 14. Health checks
```

> **⚠️ N/A — CoreMusic uses PHP 8.4 middleware pipeline** (ADR-010/011/012/013/022).

### 3.8 API Versioning

```csharp
builder.Services.AddApiVersioning(options =>
{
    options.DefaultApiVersion = new ApiVersion(1, 0);
    options.AssumeDefaultVersionWhenUnspecified = true;
    options.ReportApiVersions = true;
    options.ApiVersionReader = ApiVersionReader.Combine(
        new UrlSegmentApiVersionReader(),
        new HeaderApiVersionReader("X-Api-Version"),
        new MediaTypeApiVersionReader("v")
    );
}).AddApiExplorer(options =>
{
    options.GroupNameFormat = "'v'VVV";
    options.SubstituteApiVersionInUrl = true;
});
```

### 3.9 Response Caching

```csharp
builder.Services.AddOutputCache(options =>
{
    options.AddBasePolicy(builder => builder.Expire(TimeSpan.FromSeconds(30)));
    options.AddPolicy("MusicList", builder =>
        builder.Expire(TimeSpan.FromMinutes(5))
               .Tag("music"));
    options.AddPolicy("MusicDetail", builder =>
        builder.Expire(TimeSpan.FromMinutes(10))
               .Tag("music"));
});
```

### 3.10 Logging (Serilog)

```csharp
builder.Host.UseSerilog((context, configuration) =>
{
    configuration
        .ReadFrom.Configuration(context.Configuration)
        .Enrich.FromLogContext()
        .Enrich.WithMachineName()
        .Enrich.WithEnvironmentName()
        .WriteTo.Console(outputTemplate:
            "[{Timestamp:HH:mm:ss} {Level:u3}] {Message:lj} " +
            "{Properties:j}{NewLine}{Exception}")
        .WriteTo.File("logs/myapp-.log",
            rollingInterval: RollingInterval.Day,
            retainedFileCountLimit: 30);
});
```

### 3.11 Health Checks

```csharp
builder.Services.AddHealthChecks()
    .AddDbContextCheck<AppDbContext>(name: "database")
    .AddCheck<FileStorageHealthCheck>("file_storage")
    .AddCheck<AudioServiceHealthCheck>("audio_service");

// Custom Health Check
public class AudioServiceHealthCheck : IHealthCheck
{
    private readonly IAudioDeviceEnumerator _enumerator;

    public AudioServiceHealthCheck(IAudioDeviceEnumerator enumerator)
    {
        _enumerator = enumerator;
    }

    public Task<HealthCheckResult> CheckHealthAsync(
        HealthCheckContext context,
        CancellationToken ct = default)
    {
        try
        {
            var devices = _enumerator.GetAvailableDevices();
            return Task.FromResult(
                HealthCheckResult.Healthy(
                    $"Found {devices.Count} audio devices",
                    new Dictionary<string, object>
                    {
                        ["device_count"] = devices.Count
                    }));
        }
        catch (Exception ex)
        {
            return Task.FromResult(
                HealthCheckResult.Unhealthy(
                    "Audio device enumeration failed",
                    ex));
        }
    }
}
```

### 3.12 Background Services

```csharp
public class AudioIndexingService : BackgroundService
{
    private readonly IServiceProvider _services;
    private readonly ILogger<AudioIndexingService> _logger;
    private readonly TimeSpan _interval = TimeSpan.FromMinutes(15);

    public AudioIndexingService(
        IServiceProvider services,
        ILogger<AudioIndexingService> logger)
    {
        _services = services;
        _logger = logger;
    }

    protected override async Task ExecuteAsync(CancellationToken stoppingToken)
    {
        _logger.LogInformation("Audio indexing service starting");

        while (!stoppingToken.IsCancellationRequested)
        {
            try
            {
                using var scope = _services.CreateScope();
                var analyzer = scope.ServiceProvider
                    .GetRequiredService<IAudioAnalyzer>();

                await analyzer.ReindexLibraryAsync(stoppingToken);
                _logger.LogInformation("Audio indexing completed");
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Audio indexing failed");
            }

            await Task.Delay(_interval, stoppingToken);
        }
    }
}
```

### 3.13 Blazor Components

> **⚠️ N/A — CoreMusic uses Vanilla JS** (ADR-001). Blazor is only for internal admin dashboards.

```razor
@* MusicTrackList.razor *@
@page "/admin/tracks"
@attribute [Authorize(Policy = "AdminOnly")]

<h3>Music Tracks</h3>

@if (isLoading)
{
    <div class="spinner">Loading...</div>
}
else
{
    <table class="table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Artist</th>
                <th>Duration</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach (var track in tracks)
            {
                <tr>
                    <td>@track.Title</td>
                    <td>@track.ArtistName</td>
                    <td>@track.Duration.ToString("mm\\:ss")</td>
                    <td>
                        <button class="btn btn-sm"
                                @onclick="() => EditTrack(track.Id)">
                            Edit
                        </button>
                    </td>
                </tr>
            }
        </tbody>
    </table>
}

@code {
    private List<MusicTrackDto> tracks = new();
    private bool isLoading = true;

    [Inject] private IMusicService MusicService { get; set; } = default!;
    [Inject] private NavigationManager Navigation { get; set; } = default!;

    protected override async Task OnInitializedAsync()
    {
        tracks = await MusicService.GetAllAsync(1, 50);
        isLoading = false;
    }

    private void EditTrack(Guid id)
    {
        Navigation.NavigateTo($"/admin/tracks/{id}/edit");
    }
}
```

### 3.14 Testing

```csharp
// Unit Test
public class MusicServiceTests
{
    private readonly Mock<IMusicRepository> _mockRepo;
    private readonly MusicService _sut;

    public MusicServiceTests()
    {
        _mockRepo = new Mock<IMusicRepository>();
        _sut = new MusicService(_mockRepo.Object);
    }

    [Fact]
    public async Task GetByIdAsync_ExistingTrack_ReturnsTrack()
    {
        // Arrange
        var trackId = Guid.NewGuid();
        var expected = new MusicTrack { Id = trackId, Title = "Test Song" };
        _mockRepo.Setup(r => r.GetByIdAsync(trackId, It.IsAny<CancellationToken>()))
            .ReturnsAsync(expected);

        // Act
        var result = await _sut.GetByIdAsync(trackId);

        // Assert
        Assert.NotNull(result);
        Assert.Equal(trackId, result!.Id);
        Assert.Equal("Test Song", result.Title);
    }

    [Fact]
    public async Task GetByIdAsync_NonExistingTrack_ReturnsNull()
    {
        // Arrange
        var trackId = Guid.NewGuid();
        _mockRepo.Setup(r => r.GetByIdAsync(trackId, It.IsAny<CancellationToken>()))
            .ReturnsAsync((MusicTrack?)null);

        // Act
        var result = await _sut.GetByIdAsync(trackId);

        // Assert
        Assert.Null(result);
    }

    [Theory]
    [InlineData("")]
    [InlineData(null)]
    public async Task CreateAsync_InvalidTitle_ThrowsValidationException(string? title)
    {
        // Arrange
        var request = new CreateMusicTrackRequest { Title = title! };

        // Act & Assert
        await Assert.ThrowsAsync<ValidationException>(
            () => _sut.CreateAsync(request));
    }
}

// Integration Test
public class MusicApiTests : IClassFixture<WebApplicationFactory<Program>>
{
    private readonly WebApplicationFactory<Program> _factory;
    private readonly HttpClient _client;

    public MusicApiTests(WebApplicationFactory<Program> factory)
    {
        _factory = factory.WithWebHostBuilder(builder =>
        {
            builder.ConfigureServices(services =>
            {
                // Replace with in-memory database
                services.AddDbContext<AppDbContext>(options =>
                    options.UseInMemoryDatabase("TestDb"));
            });
        });
        _client = _factory.CreateClient();
    }

    [Fact]
    public async Task GetTracks_ReturnsSuccess()
    {
        // Act
        var response = await _client.GetAsync("/api/v1/music");

        // Assert
        response.EnsureSuccessStatusCode();
        var content = await response.Content.ReadAsStringAsync();
        Assert.Contains("[", content);
    }

    [Fact]
    public async Task GetTrack_NonExisting_ReturnsNotFound()
    {
        // Arrange
        var id = Guid.NewGuid();

        // Act
        var response = await _client.GetAsync($"/api/v1/music/{id}");

        // Assert
        Assert.Equal(HttpStatusCode.NotFound, response.StatusCode);
    }
}
```

---

## 4. Hard Guardrails

| # | Kural | Açıklama | İhlal Sonucu |
|---|-------|----------|--------------|
| 1 | **async/await mandatory** | All I/O operations MUST be async | Deadlock risk, revert |
| 2 | **DI required** | No `new` in controllers/services for dependencies | Code rejected |
| 3 | **No service locator** | Never use `GetService<T>()` outside composition root | Code rejected |
| 4 | **CancellationToken propagation** | All async methods must accept `CancellationToken` | Code rejected |
| 5 | **No null reference** | Use nullable reference types, null checks | Code rejected |
| 6 | **No static mutable state** | Thread safety violations | Code rejected |
| 7 | **EF Core async** | Use `ToListAsync`, `FirstOrDefaultAsync`, etc. | Code rejected |
| 8 | **No SQL injection** | Use parameterized queries, EF Core LINQ | Security incident |
| 9 | **No hardcoded secrets** | Use `IConfiguration` or User Secrets | Security incident |
| 10 | **Structured logging** | Use `$"{Variable}"` not string concatenation | Code rejected |

> **⚠️ N/A — CoreMusic primary guardrails are PHP-specific** (ADR-002, ADR-010, ADR-042).

---

## 5. Naming Conventions

| Tip | Format | Örnek |
|-----|--------|-------|
| Class | PascalCase | `MusicService`, `AudioAnalyzer` |
| Interface | `I` prefix + PascalCase | `IMusicService`, `IAudioAnalyzer` |
| Method | PascalCase | `GetByIdAsync`, `CreateTrack` |
| Property | PascalCase | `Title`, `CreatedAt`, `IsDeleted` |
| Private field | `_camelCase` | `_context`, `_logger` |
| Parameter | camelCase | `cancellationToken`, `trackId` |
| Local variable | camelCase | `trackList`, `totalCount` |
| Constant | PascalCase | `MaxRetryCount`, `DefaultPageSize` |
| Enum | PascalCase | `Genre.Rock`, `AudioQuality.Flac` |
| File name | Match class name | `MusicService.cs`, `MusicTrack.cs` |
| Test class | `{Class}Tests` | `MusicServiceTests` |
| Test method | `Test_{Method}_{Scenario}_{Expected}` | `GetById_ExistingTrack_ReturnsTrack` |

---

## 6. Security Considerations

| Alan | Uygulama | Detay |
|------|----------|-------|
| CSRF | Anti-forgery tokens | Form ve API'de token doğrulama |
| XSS | Razor HTML encoding | `<text>` ve `@(` ile otomatik encode |
| SQL Injection | Parameterized queries | EF Core LINQ, raw SQL'de `FromSqlInterpolated` |
| Authentication | JWT / Cookie | Token rotation, expiration |
| Authorization | Policy-based | `[Authorize(Policy = "X")]` |
| Secrets | User Secrets / Vault | `dotnet user-secrets` veya Azure Key Vault |
| HTTPS | HSTS | `UseHsts()` + `UseHttpsRedirection()` |
| CORS | Restricted origins | Sadece bilinen origin'lere izin |
| Rate Limiting | Built-in middleware | `app.UseRateLimiter()` |
| Input Validation | FluentValidation | Request DTO'ları doğrula |

> **⚠️ N/A — CoreMusic uses PHP 8.4** for primary security (ADR-010, ADR-011, ADR-012, ADR-022).

---

## 7. Performance Notes

| Alan | İyileştirme | Hedef |
|------|-------------|-------|
| Async I/O | All database/file/network calls async | Deadlock prevention |
| Response Caching | Output cache for frequent queries | < 50ms TTFB |
| Connection Pooling | EF Core DbContext pooling | Minimize connection time |
| Memory | Object pooling for large objects | < 100MB working set |
| Pagination | Skip/Take with indexed queries | < 200ms response |
| Compiled Queries | `EF.CompileQuery` for hot paths | 2-5x faster |
| Batch Operations | `ExecuteUpdateAsync`, `ExecuteDeleteAsync` | Bulk operations |
| Serialization | System.Text.Json (not Newtonsoft) | Faster serialization |

---

## 8. Edge Cases

| Senaryo | Belirti | Çözüm |
|---------|---------|-------|
| Null Reference | `NullReferenceException` | Nullable reference types, null checks |
| Async Deadlock | Thread pool exhaustion | Always use `await`, never `.Result` or `.Wait()` |
| DbContext Lifetime | Stale data, tracking conflicts | Use `AsNoTracking()` for reads |
| Cancellation | Operation continues after client disconnect | Propagate `CancellationToken` everywhere |
| Concurrency | `DbUpdateConcurrencyException` | Optimistic concurrency with row version |
| Memory Leak | Growing memory over time | Dispose pattern, `IAsyncDisposable` |
| Timeout | `TaskCanceledException` | Set explicit command timeouts |
| Serialization | Circular reference in JSON | `[JsonIgnore]` or `ReferenceHandler.IgnoreCycles` |

---

## 9. Troubleshooting

| Hata | Olası Neden | Çözüm |
|------|-------------|-------|
| 500 Internal Server Error | Unhandled exception | Check logs, add exception handler |
| DI ResolutionException | Service not registered | Check `Program.cs` registration |
| EF Migration Failed | Pending model changes | `dotnet ef migrations add` then `update` |
| 401 Unauthorized | Invalid/expired token | Check JWT configuration |
| 403 Forbidden | Missing role/claim | Verify authorization policy |
| TaskCanceledException | Timeout | Increase `CommandTimeout` or optimize query |
| SqlException: Timeout | Slow query | Add index, optimize LINQ |
| ObjectDisposedException | DbContext after dispose | Check scoped lifetime |
| JsonException: Circular ref | Entity has cycles | Use `[JsonIgnore]` or DTOs |

---

## 10. Common Anti-Patterns

### 10.1 async void

❌ **WRONG:**
```csharp
public async void ProcessTrack(Guid id)  // Fire-and-forget, exceptions lost
{
    await _service.ProcessAsync(id);
}
```

✅ **CORRECT:**
```csharp
public async Task ProcessTrackAsync(Guid id, CancellationToken ct)
{
    await _service.ProcessAsync(id, ct);
}
```

### 10.2 Service Locator

❌ **WRONG:**
```csharp
public class MusicController : ControllerBase
{
    public IActionResult Get()
    {
        var service = HttpContext.RequestServices.GetRequiredService<IMusicService>();
        return Ok(service.GetAll());
    }
}
```

✅ **CORRECT:**
```csharp
public class MusicController : ControllerBase
{
    private readonly IMusicService _service;
    public MusicController(IMusicService service) => _service = service;
}
```

### 10.3 N+1 Query

❌ **WRONG:**
```csharp
var tracks = await _context.MusicTracks.ToListAsync();
foreach (var track in tracks)
{
    track.Artist = await _context.Artists.FindAsync(track.ArtistId); // N+1!
}
```

✅ **CORRECT:**
```csharp
var tracks = await _context.MusicTracks
    .Include(t => t.Artist)
    .ToListAsync();
```

### 10.4 Blocking Async

❌ **WRONG:**
```csharp
var result = _service.GetByIdAsync(id).Result;  // Deadlock risk
```

✅ **CORRECT:**
```csharp
var result = await _service.GetByIdAsync(id);
```

### 10.5 Swallowing Exceptions

❌ **WRONG:**
```csharp
try { await _service.ProcessAsync(id); }
catch { }  // Silent failure
```

✅ **CORRECT:**
```csharp
try { await _service.ProcessAsync(id); }
catch (Exception ex)
{
    _logger.LogError(ex, "Processing failed for {Id}", id);
    throw;
}
```

### 10.6 Not Using CancellationToken

❌ **WRONG:**
```csharp
public async Task<List<Track>> GetAll()
{
    return await _context.MusicTracks.ToListAsync();
}
```

✅ **CORRECT:**
```csharp
public async Task<List<Track>> GetAllAsync(CancellationToken ct = default)
{
    return await _context.MusicTracks.ToListAsync(ct);
}
```

---

## 11. CoreMusic Integration

ASP.NET, CoreMusic ekosisteminde şu alanlarda düşünülebilir:

| Alan | Kullanım | Öncelik |
|------|----------|---------|
| Admin Dashboard | Monitoring, log analysis, user management | Orta |
| Audio Device Tool | WASAPI/ASIO device enumeration (Windows) | Düşük |
| AI Inference Service | ML.NET tabanlı müzik önerisi | Düşük |
| CLI Utilities | FFmpeg wrapper, audio analysis | Düşük |
| Health Monitor | Servis durumu dashboard'ı | Orta |

> **⚠️ N/A — CoreMusic uses PHP 8.4** for all primary backend services (ADR-039, ADR-042).

---

## 12. Project Template

### 12.1 .csproj File

```xml
<Project Sdk="Microsoft.NET.Sdk.Web">

  <PropertyGroup>
    <TargetFramework>net9.0</TargetFramework>
    <Nullable>enable</Nullable>
    <ImplicitUsings>enable</ImplicitUsings>
    <RootNamespace>CoreMusic.Tools</RootNamespace>
    <AssemblyName>CoreMusic.Tools</AssemblyName>
  </PropertyGroup>

  <ItemGroup>
    <PackageReference Include="Microsoft.AspNetCore.OpenApi" Version="9.0.0" />
    <PackageReference Include="Microsoft.EntityFrameworkCore" Version="9.0.0" />
    <PackageReference Include="Microsoft.EntityFrameworkCore.SqlServer" Version="9.0.0" />
    <PackageReference Include="Microsoft.EntityFrameworkCore.InMemory" Version="9.0.0" />
    <PackageReference Include="Serilog.AspNetCore" Version="8.0.0" />
    <PackageReference Include="FluentValidation.AspNetCore" Version="11.3.0" />
    <PackageReference Include="Microsoft.AspNetCore.Authentication.JwtBearer" Version="9.0.0" />
    <PackageReference Include="Microsoft.AspNetCore.Mvc.Versioning" Version="5.1.0" />
    <PackageReference Include="AspNetCore.HealthChecks.SqlServer" Version="8.0.0" />
  </ItemGroup>

  <ItemGroup>
    <ProjectReference Include="..\CoreMusic.Tools.Domain\CoreMusic.Tools.Domain.csproj" />
    <ProjectReference Include="..\CoreMusic.Tools.Application\CoreMusic.Tools.Application.csproj" />
    <ProjectReference Include="..\CoreMusic.Tools.Infrastructure\CoreMusic.Tools.Infrastructure.csproj" />
  </ItemGroup>

</Project>
```

### 12.2 Program.cs

```csharp
using Serilog;
using FluentValidation;
using FluentValidation.AspNetCore;

var builder = WebApplication.CreateBuilder(args);

// Serilog
builder.Host.UseSerilog((context, configuration) =>
    configuration.ReadFrom.Configuration(context.Configuration));

// Controllers + FluentValidation
builder.Services.AddControllers()
    .AddFluentValidation(fv =>
        fv.RegisterValidatorsFromAssemblyContaining<Program>());

// EF Core
builder.Services.AddDbContext<AppDbContext>(options =>
    options.UseSqlServer(
        builder.Configuration.GetConnectionString("DefaultConnection")));

// Authentication
builder.Services.AddAuthentication(JwtBearerDefaults.AuthenticationScheme)
    .AddJwtBearer(options =>
    {
        options.TokenValidationParameters = new TokenValidationParameters
        {
            ValidateIssuer = true,
            ValidateAudience = true,
            ValidateLifetime = true,
            ValidateIssuerSigningKey = true,
            ValidIssuer = builder.Configuration["Jwt:Issuer"],
            ValidAudience = builder.Configuration["Jwt:Audience"],
            IssuerSigningKey = new SymmetricSecurityKey(
                Encoding.UTF8.GetBytes(builder.Configuration["Jwt:Key"]!))
        };
    });

// Authorization
builder.Services.AddAuthorization(options =>
{
    options.AddPolicy("AdminOnly", p => p.RequireRole("Admin"));
});

// CORS
builder.Services.AddCors(options =>
{
    options.AddPolicy("AllowSpecificOrigins", policy =>
    {
        policy.WithOrigins("https://admin.coremusic.net")
              .AllowAnyHeader()
              .AllowAnyMethod();
    });
});

// Rate Limiting
builder.Services.AddRateLimiter(options =>
{
    options.AddFixedWindowLimiter("fixed", opts =>
    {
        opts.PermitLimit = 100;
        opts.Window = TimeSpan.FromMinutes(1);
    });
});

// Health Checks
builder.Services.AddHealthChecks()
    .AddDbContextCheck<AppDbContext>();

// API Versioning
builder.Services.AddApiVersioning()
    .AddApiExplorer();

// Output Cache
builder.Services.AddOutputCache();

// Services
builder.Services.AddScoped<IMusicRepository, MusicRepository>();
builder.Services.AddScoped<IMusicService, MusicService>();
builder.Services.AddScoped<IUnitOfWork, UnitOfWork>();

var app = builder.Build();

// Middleware Pipeline
app.UseMiddleware<CorrelationIdMiddleware>();
app.UseSerilogRequestLogging();
app.UseExceptionHandler("/error");
app.UseHsts();
app.UseHttpsRedirection();
app.UseStaticFiles();
app.UseRouting();
app.UseCors("AllowSpecificOrigins");
app.UseRateLimiter();
app.UseAuthentication();
app.UseAuthorization();
app.UseOutputCache();
app.MapControllers();
app.MapHealthChecks("/health");

app.Run();
```

---

## 13. Related Documents

| Dosya | İlişki |
|-------|--------|
| [[index]] | Master katalog |
| [[CLAUDE.md]] | Ana sözleşme |
| [[AGENTS.md]] | Ajan yetkileri |
| [[WORKFLOW.md]] | İş akışları |
| [[brain.md]] | Mimari kararlar |
| [[keys.md]] | Navigasyon haritası |
| [[decisions/accepted/ADR-001-vanilla-js-itcss]] | Frontend framework yasağı |
| [[decisions/accepted/ADR-002-pdo-mandatory-no-orm]] | ORM yasağı (PHP için) |

---

## 14. Cross-References

| ADR | Konu | ASP.NET İlgisi |
|-----|------|----------------|
| ADR-001 | Vanilla JS + ITCSS | Frontend kararları, Blazor N/A |
| ADR-002 | PDO mandatory, ORM yasak | EF Core ORMPHP için yasak, ASP.NET için serbest |
| ADR-003 | 18 BCNF veritabanı | EF Core mapping ile uyumlu |
| ADR-004 | Multi-domain SPA | Routing kararları |
| ADR-007 | Zero Code Before Plan | Planlama zorunlu |
| ADR-010 | CSRF koruma | ASP.NET anti-forgery token |
| ADR-011 | Session yönetimi | ASP.NET session/cookie auth |
| ADR-012 | CSP nonce | Razor'da nonce yönetimi |
| ADR-013 | Rate limiting | ASP.NET rate limiter middleware |
| ADR-022 | DB hardened security | EF Core parameterized queries |
| ADR-042 | Vault restructuring | PHP 8.4 primary (ASP.NET auxiliary) |

---

## 15. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 3.0.0 |
| **Toplam Satır** | 560+ |
| **Bölüm Sayısı** | 18 |
| **Kod Örnekleri** | 15+ |
| **Anti-Pattern** | 6 |
| **Guardrails** | 10 |
| **Edge Cases** | 8 |
| **Troubleshooting** | 9 |
| **N/A Marker** | 7 (PHP-primary sections) |
| **Uyumluluk** | Red Team • Human Mode • Truth Mode |

---

## 16. Examples

### 16.1 Complete Controller

```csharp
[ApiController]
[Route("api/v{version:apiVersion}/[controller]")]
[ApiVersion("1.0")]
[Produces("application/json")]
public class ArtistController : ControllerBase
{
    private readonly IArtistService _artistService;
    private readonly ILogger<ArtistController> _logger;

    public ArtistController(
        IArtistService artistService,
        ILogger<ArtistController> logger)
    {
        _artistService = artistService;
        _logger = logger;
    }

    [HttpGet]
    [ProducesResponseType(typeof(List<ArtistDto>), 200)]
    public async Task<IActionResult> GetAll(
        [FromQuery] int page = 1,
        [FromQuery] int pageSize = 20,
        CancellationToken ct = default)
    {
        var artists = await _artistService.GetAllAsync(page, pageSize, ct);
        return Ok(artists);
    }

    [HttpGet("{id:guid}")]
    [ProducesResponseType(typeof(ArtistDto), 200)]
    [ProducesResponseType(404)]
    public async Task<IActionResult> GetById(Guid id, CancellationToken ct)
    {
        var artist = await _artistService.GetByIdAsync(id, ct);
        return artist is null ? NotFound() : Ok(artist);
    }

    [HttpPost]
    [Authorize(Policy = "AdminOnly")]
    [ProducesResponseType(typeof(ArtistDto), 201)]
    [ProducesResponseType(400)]
    public async Task<IActionResult> Create(
        [FromBody] CreateArtistRequest request,
        CancellationToken ct)
    {
        var artist = await _artistService.CreateAsync(request, ct);
        return CreatedAtAction(nameof(GetById), new { id = artist.Id }, artist);
    }

    [HttpPut("{id:guid}")]
    [Authorize(Policy = "AdminOnly")]
    [ProducesResponseType(typeof(ArtistDto), 200)]
    [ProducesResponseType(404)]
    public async Task<IActionResult> Update(
        Guid id,
        [FromBody] UpdateArtistRequest request,
        CancellationToken ct)
    {
        var artist = await _artistService.UpdateAsync(id, request, ct);
        return Ok(artist);
    }

    [HttpDelete("{id:guid}")]
    [Authorize(Policy = "SuperAdmin")]
    [ProducesResponseType(204)]
    [ProducesResponseType(404)]
    public async Task<IActionResult> Delete(Guid id, CancellationToken ct)
    {
        await _artistService.DeleteAsync(id, ct);
        return NoContent();
    }
}
```

### 16.2 Complete Service

```csharp
public class ArtistService : IArtistService
{
    private readonly IArtistRepository _repository;
    private readonly IUnitOfWork _unitOfWork;
    private readonly ILogger<ArtistService> _logger;

    public ArtistService(
        IArtistRepository repository,
        IUnitOfWork unitOfWork,
        ILogger<ArtistService> logger)
    {
        _repository = repository;
        _unitOfWork = unitOfWork;
        _logger = logger;
    }

    public async Task<ArtistDto?> GetByIdAsync(Guid id, CancellationToken ct)
    {
        var artist = await _repository.GetByIdAsync(id, ct);
        return artist is null ? null : MapToDto(artist);
    }

    public async Task<List<ArtistDto>> GetAllAsync(
        int page, int pageSize, CancellationToken ct)
    {
        var artists = await _repository.GetAllAsync(page, pageSize, ct);
        return artists.Select(MapToDto).ToList();
    }

    public async Task<ArtistDto> CreateAsync(
        CreateArtistRequest request, CancellationToken ct)
    {
        var validator = new CreateArtistRequestValidator();
        var validationResult = await validator.ValidateAsync(request, ct);

        if (!validationResult.IsValid)
        {
            throw new ValidationException(validationResult.Errors);
        }

        var artist = new Artist
        {
            Id = Guid.NewGuid(),
            Name = request.Name,
            Bio = request.Bio,
            CreatedAt = DateTime.UtcNow
        };

        await _repository.AddAsync(artist, ct);
        await _unitOfWork.SaveChangesAsync(ct);

        _logger.LogInformation("Created artist {ArtistId}: {Name}", artist.Id, artist.Name);

        return MapToDto(artist);
    }

    public async Task<ArtistDto> UpdateAsync(
        Guid id, UpdateArtistRequest request, CancellationToken ct)
    {
        var artist = await _repository.GetByIdAsync(id, ct)
            ?? throw new NotFoundException($"Artist {id} not found");

        artist.Name = request.Name;
        artist.Bio = request.Bio;
        artist.UpdatedAt = DateTime.UtcNow;

        _repository.Update(artist);
        await _unitOfWork.SaveChangesAsync(ct);

        return MapToDto(artist);
    }

    public async Task DeleteAsync(Guid id, CancellationToken ct)
    {
        var artist = await _repository.GetByIdAsync(id, ct)
            ?? throw new NotFoundException($"Artist {id} not found");

        _repository.Remove(artist);
        await _unitOfWork.SaveChangesAsync(ct);

        _logger.LogInformation("Deleted artist {ArtistId}", id);
    }

    private static ArtistDto MapToDto(Artist artist) => new()
    {
        Id = artist.Id,
        Name = artist.Name,
        Bio = artist.Bio,
        TrackCount = artist.Tracks?.Count ?? 0,
        CreatedAt = artist.CreatedAt
    };
}
```

### 16.3 Complete DbContext

```csharp
public class AppDbContext : DbContext
{
    public DbSet<MusicTrack> MusicTracks => Set<MusicTrack>();
    public DbSet<Artist> Artists => Set<Artist>();
    public DbSet<Album> Albums => Set<Album>();
    public DbSet<Playlist> Playlists => Set<Playlist>();

    public AppDbContext(DbContextOptions<AppDbContext> options) : base(options) { }

    protected override void OnModelCreating(ModelBuilder modelBuilder)
    {
        modelBuilder.ApplyConfigurationsFromAssembly(typeof(AppDbContext).Assembly);

        // Global query filter for soft delete
        foreach (var entityType in modelBuilder.Model.GetEntityTypes())
        {
            if (typeof(ISoftDeletable).IsAssignableFrom(entityType.ClrType))
            {
                modelBuilder.Entity(entityType.ClrType)
                    .HasQueryFilter(SoftDeleteFilter.GetFilter(entityType.ClrType));
            }
        }

        base.OnModelCreating(modelBuilder);
    }

    public override async Task<int> SaveChangesAsync(CancellationToken cancellationToken = default)
    {
        var entries = ChangeTracker.Entries<AuditableEntity>();

        foreach (var entry in entries)
        {
            switch (entry.State)
            {
                case EntityState.Added:
                    entry.Entity.CreatedAt = DateTime.UtcNow;
                    break;
                case EntityState.Modified:
                    entry.Entity.UpdatedAt = DateTime.UtcNow;
                    break;
            }
        }

        // Soft delete
        foreach (var entry in ChangeTracker.Entries<ISoftDeletable>())
        {
            if (entry.State == EntityState.Deleted)
            {
                entry.State = EntityState.Modified;
                entry.Entity.IsDeleted = true;
                entry.Entity.DeletedAt = DateTime.UtcNow;
            }
        }

        return await base.SaveChangesAsync(cancellationToken);
    }
}

// Configuration
public class MusicTrackConfiguration : IEntityTypeConfiguration<MusicTrack>
{
    public void Configure(EntityTypeBuilder<MusicTrack> builder)
    {
        builder.ToTable("music_tracks");

        builder.HasKey(e => e.Id);
        builder.Property(e => e.Id).HasColumnName("id");
        builder.Property(e => e.Title).HasColumnName("title").HasMaxLength(256).IsRequired();
        builder.Property(e => e.FilePath).HasColumnName("file_path").HasMaxLength(1024).IsRequired();
        builder.Property(e => e.Duration).HasColumnName("duration").HasPrecision(10, 2);
        builder.Property(e => e.Bitrate).HasColumnName("bitrate");
        builder.Property(e => e.SampleRate).HasColumnName("sample_rate");
        builder.Property(e => e.Format).HasColumnName("format").HasMaxLength(32);

        builder.HasIndex(e => e.Title);
        builder.HasIndex(e => e.ArtistId);
        builder.HasIndex(e => e.AlbumId);

        builder.HasOne(e => e.Artist)
            .WithMany(a => a.Tracks)
            .HasForeignKey(e => e.ArtistId)
            .OnDelete(DeleteBehavior.Restrict);

        builder.HasOne(e => e.Album)
            .WithMany(a => a.Tracks)
            .HasForeignKey(e => e.AlbumId)
            .OnDelete(DeleteBehavior.SetNull);
    }
}
```

---

## 17. Checklist

Pre-commit ASP.NET quality checklist:

- [ ] `dotnet build` — no warnings or errors
- [ ] `dotnet test` — all tests pass
- [ ] `dotnet format` — code formatting applied
- [ ] No `async void` methods
- [ ] All `async` methods accept `CancellationToken`
- [ ] No `new` for dependencies in controllers/services
- [ ] No hardcoded secrets (check `appsettings.json`)
- [ ] No `SELECT *` — explicit column selection
- [ ] No synchronous I/O (`ToString()`, `Result`, `.Wait()`)
- [ ] No `Task.Run` in web request pipeline
- [ ] All controllers have `[ApiController]` attribute
- [ ] All actions have `[ProducesResponseType]` attributes
- [ ] No `DbContext` injected as singleton
- [ ] No circular references in JSON serialization
- [ ] Structured logging (no string concatenation)
- [ ] Nullable reference types enabled
- [ ] XML documentation for public APIs
- [ ] Health check endpoint functional
- [ ] Rate limiting configured
- [ ] CORS policy restricts origins

---

## 18. Migration Guide

### 18.1 Creating a Migration

```bash
# Create new migration
dotnet ef migrations add AddMusicTracks \
    --project src/CoreMusic.Tools.Infrastructure \
    --startup-project src/CoreMusic.Tools.Api

# Apply migration
dotnet ef database update \
    --project src/CoreMusic.Tools.Infrastructure \
    --startup-project src/CoreMusic.Tools.Api

# Generate SQL script
dotnet ef migrations script \
    --project src/CoreMusic.Tools.Infrastructure \
    --startup-project src/CoreMusic.Tools.Api \
    --output migrations.sql

# Remove last migration (development only)
dotnet ef migrations remove \
    --project src/CoreMusic.Tools.Infrastructure \
    --startup-project src/CoreMusic.Tools.Api
```

### 18.2 Migration Patterns

```csharp
// Reversible migration
public partial class AddMusicTracks : Migration
{
    protected override void Up(MigrationBuilder migrationBuilder)
    {
        migrationBuilder.CreateTable(
            name: "music_tracks",
            columns: table => new
            {
                id = table.Column<Guid>(type: "uniqueidentifier", nullable: false),
                title = table.Column<string>(type: "nvarchar(256)", maxLength: 256, nullable: false),
                file_path = table.Column<string>(type: "nvarchar(1024)", maxLength: 1024, nullable: false),
                duration = table.Column<decimal>(type: "decimal(10,2)", precision: 10, scale: 2, nullable: false),
                artist_id = table.Column<Guid>(type: "uniqueidentifier", nullable: false),
                created_at = table.Column<DateTime>(type: "datetime2", nullable: false),
                updated_at = table.Column<DateTime>(type: "datetime2", nullable: true),
                is_deleted = table.Column<bool>(type: "bit", nullable: false, defaultValue: false)
            },
            constraints: table =>
            {
                table.PrimaryKey("PK_music_tracks", x => x.id);
                table.ForeignKey(
                    name: "FK_music_tracks_artists_artist_id",
                    column: x => x.artist_id,
                    principalTable: "artists",
                    principalColumn: "id",
                    onDelete: ReferentialAction.Restrict);
            });

        migrationBuilder.CreateIndex(name: "IX_music_tracks_title", table: "music_tracks", column: "title");
        migrationBuilder.CreateIndex(name: "IX_music_tracks_artist_id", table: "music_tracks", column: "artist_id");
    }

    protected override void Down(MigrationBuilder migrationBuilder)
    {
        migrationBuilder.DropTable(name: "music_tracks");
    }
}

// Data migration
public partial class SeedDefaultGenres : Migration
{
    protected override void Up(MigrationBuilder migrationBuilder)
    {
        migrationBuilder.Sql(@"
            INSERT INTO genres (id, name, created_at)
            VALUES
                ('00000000-0000-0000-0000-000000000001', 'Rock', GETUTCDATE()),
                ('00000000-0000-0000-0000-000000000002', 'Pop', GETUTCDATE()),
                ('00000000-0000-0000-0000-000000000003', 'Jazz', GETUTCDATE()),
                ('00000000-0000-0000-0000-000000000004', 'Classical', GETUTCDATE()),
                ('00000000-0000-0000-0000-000000000005', 'Electronic', GETUTCDATE());
        ");
    }

    protected override void Down(MigrationBuilder migrationBuilder)
    {
        migrationBuilder.Sql("DELETE FROM genres WHERE id IN ('00000000-0000-0000-0000-000000000001', '00000000-0000-0000-0000-000000000002', '00000000-0000-0000-0000-000000000003', '00000000-0000-0000-0000-000000000004', '00000000-0000-0000-0000-000000000005')");
    }
}
```

### 18.3 Best Practices

| Kural | Açıklama |
|-------|----------|
| Always test migrations | Run `Up()` then `Down()` in dev |
| Seed data in separate migration | Don't mix schema and data changes |
| Use `migrationBuilder.Sql` for complex ops | Raw SQL for advanced scenarios |
| Never delete production data | Use soft delete pattern |
| Review generated SQL | Check index creation, FK constraints |
| Version control migrations | Commit migration files to git |
| Use idempotent scripts | Script should be safe to run multiple times |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-06
**Mode:** Red Team • Human Mode • Truth Mode
