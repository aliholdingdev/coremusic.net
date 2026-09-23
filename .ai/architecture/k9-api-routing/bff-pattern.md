---
title: "Backend for Frontend (BFF) Pattern"
layer: K9
category: "API & Routing"
date: 2026-09-20
status: "tamamlandı"
---

# Backend for Frontend (BFF) Pattern

## Genel Bakış

BFF (Backend for Frontend) pattern, her istemci türü için özel bir API katmanı oluşturarak backend servislerinin karmaşıklığını soyutlar. Web, mobil ve masaüstü uygulamaları farklı veri formatları, bandwidth gereksinimleri ve UX ihtiyaçlarına sahiptir; BFF bunu tek bir unified API ile karşılar.

COREMUSIC'te üç ayrı BFF tanımlanmıştır: Web BFF (full-featured), Mobile BFF (bandwidth-optimized) ve Desktop BFF (high-fidelity). Her BFF kendi downstream servislerini orchestrated eder ve istemciye optimize edilmiş response döndürür.

## API Tanımı

### Web BFF Endpoints

| Endpoint | Method | Açıklama | Response Boyutu |
|----------|--------|----------|-----------------|
| `/bff/web/dashboard` | GET | Dashboard verisi (birleştirilmiş) | ~50KB |
| `/bff/web/player` | GET | Player state + track metadata | ~10KB |
| `/bff/web/library` | GET | Kütüphane (paginated) | ~30KB |
| `/bff/web/search/{query}` | GET | Arama sonuçları | ~20KB |
| `/bff/web/playlist/{id}` | GET | Playlist detay + track list | ~40KB |
| `/bff/web/ai/insights` | GET | AI önerileri + istatistikler | ~15KB |

### Mobile BFF Endpoints

| Endpoint | Method | Açıklama | Response Boyutu |
|----------|--------|----------|-----------------|
| `/bff/mobile/feed` | GET | Ana feed (compact) | ~8KB |
| `/bff/mobile/player` | GET | Player state (minimal) | ~3KB |
| `/bff/mobile/library` | GET | Kütüphane (thumbnail-based) | ~5KB |
| `/bff/mobile/search/{query}` | GET | Arama sonuçları (compact) | ~4KB |
| `/bff/mobile/offline/{playlistId}` | GET | Offline-ready playlist | ~12KB |
| `/bff/mobile/sync` | POST | Offline sync data | varies |

### Desktop BFF Endpoints

| Endpoint | Method | Açıklama | Response Boyutu |
|----------|--------|----------|-----------------|
| `/bff/desktop/full-track/{id}` | GET | Yüksek kalite track metadata | ~25KB |
| `/bff/desktop/equalizer/{trackId}` | GET | EQ preset + analiz | ~15KB |
| `/bff/desktop/hires-stream/{trackId}` | GET | Hi-Res stream URL | ~2KB |

## Teknik Detaylar

### BFF Mimarisi

```
┌─────────────────────────────────────────────────────────────┐
│                     BFF LAYER                               │
│                                                             │
│  ┌──────────────────┐  ┌──────────────────┐               │
│  │    Web BFF       │  │   Mobile BFF     │               │
│  │  (Node.js/Rust)  │  │  (Go/Rust)       │               │
│  │                  │  │                  │               │
│  │  - Full metadata │  │  - Compact data  │               │
│  │  - High quality  │  │  - Thumbnails    │               │
│  │  - Real-time WS  │  │  - Offline-ready │               │
│  └────────┬─────────┘  └────────┬─────────┘               │
│           │                     │                          │
│  ┌────────┴─────────────────────┴─────────┐               │
│  │           GraphQL Aggregator            │               │
│  │    (Service composition & batching)     │               │
│  └────────┬─────────────────────┬──────────┘               │
│           │                     │                          │
└───────────┼─────────────────────┼──────────────────────────┘
            │                     │
┌───────────▼─────────────────────▼──────────────────────────┐
│                    K10 SERVICE LAYER                        │
│  ┌─────────┐  ┌─────────┐  ┌─────────┐  ┌─────────┐     │
│  │ User    │  │ Track   │  │ Playlist│  │ AI      │     │
│  │ Service │  │ Service │  │ Service │  │ Service │     │
│  └─────────┘  └─────────┘  └─────────┘  └─────────┘     │
└─────────────────────────────────────────────────────────────┘
```

### BFF Aggregation Pattern

```typescript
// Web BFF - Dashboard Aggregation
interface DashboardResponse {
  user: UserProfile;
  recentlyPlayed: TrackSummary[];
  recommendations: AIRecommendation[];
  playlists: PlaylistSummary[];
  stats: ListeningStats;
}

async function getDashboard(userId: string): Promise<DashboardResponse> {
  // Parallel fetching - tüm servisler aynı anda çağrılır
  const [user, recentlyPlayed, recommendations, playlists, stats] =
    await Promise.all([
      userService.getProfile(userId),
      trackService.getRecentlyPlayed(userId, { limit: 10 }),
      aiService.getRecommendations(userId, { limit: 5 }),
      playlistService.getUserPlaylists(userId, { limit: 5 }),
      trackService.getListeningStats(userId),
    ]);

  // Response transformation - compact data
  return {
    user: {
      id: user.id,
      displayName: user.displayName,
      avatarUrl: user.avatarUrl,
      subscription: user.subscription.tier,
    },
    recentlyPlayed: recentlyPlayed.map(track => ({
      id: track.id,
      title: track.title,
      artist: track.artist.name,
      duration: track.duration,
      artworkUrl: track.artwork?.thumbnailUrl,
    })),
    recommendations: recommendations.map(rec => ({
      trackId: rec.trackId,
      reason: rec.reason,
      confidence: rec.confidence,
    })),
    playlists: playlists.map(pl => ({
      id: pl.id,
      name: pl.name,
      trackCount: pl.trackCount,
      artworkUrl: pl.artwork?.thumbnailUrl,
    })),
    stats: {
      totalListeningHours: stats.totalHours,
      topGenres: stats.genres.slice(0, 5),
      weeklyTrend: stats.weeklyMinutes,
    },
  };
}
```

### Mobile BFF - Bandwidth Optimization

```typescript
// Mobile BFF - Compact Feed
interface MobileFeedResponse {
  sections: FeedSection[];
  nextCursor: string;
  prefetch: string[]; // Bir sonraki sayfa URL'leri
}

async function getMobileFeed(
  userId: string,
  cursor?: string
): Promise<MobileFeedResponse> {
  const limit = 20; // Mobile için daha az veri

  const [recentlyPlayed, forYou, newReleases] = await Promise.all([
    trackService.getRecentlyPlayed(userId, { limit: 5, compact: true }),
    aiService.getForYouFeed(userId, { limit: 10, compact: true }),
    trackService.getNewReleases({ limit: 5, compact: true }),
  ]);

  return {
    sections: [
      {
        type: "recent",
        title: "Son Dinlenenler",
        items: recentlyPlayed.map(t => ({
          id: t.id,
          title: t.title,
          artist: t.artistName,
          thumb: t.artwork?.thumbnailUrl, // 64x64 thumbnail
        })),
      },
      {
        type: "recommended",
        title: "Sizin İçin",
        items: forYou.map(t => ({
          id: t.id,
          title: t.title,
          artist: t.artistName,
          thumb: t.artwork?.thumbnailUrl,
        })),
      },
    ],
    nextCursor: generateCursor(limit),
    prefetch: [
      `/bff/mobile/feed?cursor=${generateCursor(limit)}`,
    ],
  };
}
```

### BFF Service Discovery

```rust
pub struct BFFRegistry {
    services: HashMap<ServiceName, ServiceConfig>,
    health_checker: HealthChecker,
}

impl BFFRegistry {
    pub async fn resolve(&self, service: ServiceName) -> Result<ServiceInstance> {
        let config = self.services.get(&service)
            .ok_or_else(|| BFFError::ServiceNotFound(service.clone()))?;

        // Health check ile healthy instance bul
        let instances = self.health_checker
            .get_healthy_instances(&config.name)
            .await?;

        // Load balancing
        let instance = self.select_instance(&instances, &config.strategy)?;

        Ok(instance)
    }

    // Circuit breaker her servis için ayrı
    pub async fn call_service(
        &self,
        service: ServiceName,
        request: Request,
    ) -> Result<Response> {
        let instance = self.resolve(service.clone()).await?;
        let circuit = self.get_circuit_breaker(&service);

        circuit.call(async {
            let client = self.get_client(&instance);
            client.send(request).await
        }).await
    }
}
```

### Response Diferansiyasyon

```
Track Metadata Comparison:

Web BFF Response (~2KB per track):
{
  "id": "trk_123",
  "title": "Bohemian Rhapsody",
  "artist": { "id": "art_456", "name": "Queen", "bio": "..." },
  "album": { "id": "alb_789", "title": "A Night at the Opera", "year": 1975 },
  "duration": 354,
  "genres": ["rock", "classic rock", "progressive rock"],
  "audioQuality": { "format": "FLAC", "bitDepth": 24, "sampleRate": 96000 },
  "artwork": { "full": "https://cdn.../1000x1000.jpg", "thumbnail": "https://cdn.../300x300.jpg" },
  "lyrics": { "available": true, "synced": true },
  "analysis": { "energy": 0.82, "danceability": 0.65, "acousticness": 0.12 }
}

Mobile BFF Response (~300 bytes per track):
{
  "id": "trk_123",
  "title": "Bohemian Rhapsody",
  "artist": "Queen",
  "duration": 354,
  "thumb": "https://cdn.../64x64.jpg"
}
```

## Konfigürasyon

```yaml
# bff-config.yaml
bff:
  web:
    port: 4001
    upstream_timeout: 30s
    aggregation_timeout: 10s
    cache_ttl: 60s
    features:
      - real_time_streaming
      - high_res_audio
      - lyrics_sync
      - ai_insights

  mobile:
    port: 4002
    upstream_timeout: 15s
    aggregation_timeout: 5s
    cache_ttl: 300s
    features:
      - offline_sync
      - compact_responses
      - push_notifications
      - background_download

  desktop:
    port: 4003
    upstream_timeout: 30s
    aggregation_timeout: 10s
    cache_ttl: 120s
    features:
      - hires_audio
      - advanced_eq
      - multi_room
      - local_library
```

## Bağımlılıklar

### Bağımlı Olduğu
- **API Gateway**: Request routing
- **K10 Services**: User, Track, Playlist, AI servisleri
- **Redis**: Response caching

### Bağımlı Olan
- **K11 Web**: Web BFF'i kullanan SPA
- **K12 Mobile**: Mobile BFF'i kullanan uygulama
- **K13 Desktop**: Desktop BFF'i kullanan uygulama

## Durum: Implementasyon

- [x] BFF pattern design
- [ ] Web BFF implementasyonu
- [ ] Mobile BFF implementasyonu
- [ ] Desktop BFF implementasyonu
- [ ] GraphQL aggregator
- [ ] Response caching
- [ ] Circuit breaker per service
- [ ] Offline sync endpoint
- [ ] A/B testing support
