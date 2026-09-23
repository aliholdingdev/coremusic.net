---
title: "CQRS Pattern - Command Query Responsibility Segregation"
layer: K9
category: "API & Routing"
date: 2026-09-20
status: "tamamlandı"
---

# CQRS Pattern

## Genel Bakış

CQRS (Command Query Responsibility Segregation), okuma ve yazma işlemlerini ayrı modellerle yöneten mimari bir pattern'dir. COREMUSIC'te bu pattern, yüksek throughput'lu okuma işlemlerini (şarkı listeleme, arama, playlist görüntüleme) yazma işlemlerinden (şarkı ekleme, profil güncelleme, playlist oluşturma) ayırarak optimize eder.

Read model, denormalize edilmiş ve sorgulanmaya optimize edilmiş veri sunarken, write model, domain-driven design prensiplerine uygun aggregate root'lar kullanır. Event sourcing ile tutarlılık sağlanır.

## API Tanımı

### Command Endpoints (Yazma)

| Endpoint | Method | Command | Açıklama |
|----------|--------|---------|----------|
| `/api/v1/commands/track/play` | POST | PlayTrack | Şarkı çalma kaydı |
| `/api/v1/commands/track/like` | POST | LikeTrack | Şarkıyı beğenme |
| `/api/v1/commands/playlist/create` | POST | CreatePlaylist | Playlist oluşturma |
| `/api/v1/commands/playlist/add-track` | POST | AddTrackToPlaylist | Playlist'e şarkı ekleme |
| `/api/v1/commands/user/update-profile` | PUT | UpdateProfile | Profil güncelleme |
| `/api/v1/commands/user/equalizer/save` | POST | SaveEqualizerPreset | EQ preset kaydetme |

### Query Endpoints (Okuma)

| Endpoint | Method | Query | Açıklama |
|----------|--------|-------|----------|
| `/api/v1/queries/tracks/{id}` | GET | GetTrackById | Şarkı detayı |
| `/api/v1/queries/tracks/search` | GET | SearchTracks | Şarkı arama |
| `/api/v1/queries/playlists/{id}` | GET | GetPlaylistById | Playlist detayı |
| `/api/v1/queries/user/library` | GET | GetUserLibrary | Kullanıcı kütüphanesi |
| `/api/v1/queries/user/stats` | GET | GetUserStats | Dinleme istatistikleri |
| `/api/v1/queries/ai/recommendations` | GET | GetRecommendations | AI önerileri |

## Teknik Detaylar

### CQRS Mimarisi

```
┌─────────────────────────────────────────────────────────────┐
│                    COMMAND SIDE (Write)                      │
│                                                             │
│  ┌──────────┐    ┌──────────┐    ┌──────────┐            │
│  │ Command  │ →  │ Command  │ →  │ Aggregate│            │
│  │ Handler  │    │ Validator│    │ Root     │            │
│  └──────────┘    └──────────┘    └────┬─────┘            │
│                                       │                   │
│                               ┌───────▼───────┐          │
│                               │  Event Store  │          │
│                               │  (PostgreSQL) │          │
│                               └───────┬───────┘          │
└───────────────────────────────────────┼───────────────────┘
                                        │
                              ┌─────────▼─────────┐
                              │    Event Bus       │
                              │  (Kafka/RabbitMQ)  │
                              └─────────┬─────────┘
                                        │
┌───────────────────────────────────────┼───────────────────┐
│                    QUERY SIDE (Read)  │                    │
│                               ┌───────▼───────┐          │
│                               │  Projection   │          │
│                               │  Engine       │          │
│                               └───────┬───────┘          │
│                                       │                   │
│  ┌──────────┐    ┌──────────┐    ┌────▼──────┐          │
│  │  Query   │ ←  │  Query   │ ←  │  Read     │          │
│  │  Handler │    │  Cache   │    │  Database │          │
│  └──────────┘    └──────────┘    └───────────┘          │
│  (Elasticsearch / Redis Cache)                           │
└─────────────────────────────────────────────────────────────┘
```

### Command Implementation

```typescript
// Command Definitions
interface Command {
  commandId: string;
  timestamp: Date;
  userId: string;
  correlationId?: string;
}

interface PlayTrackCommand extends Command {
  type: "PLAY_TRACK";
  trackId: string;
  deviceType: "web" | "mobile" | "desktop";
  position: number; // saniye cinsinden
}

interface CreatePlaylistCommand extends Command {
  type: "CREATE_PLAYLIST";
  name: string;
  description?: string;
  isPublic: boolean;
  trackIds?: string[];
}

// Command Handler
class PlayTrackHandler implements CommandHandler<PlayTrackCommand> {
  constructor(
    private eventStore: EventStore,
    private validator: CommandValidator,
  ) {}

  async handle(command: PlayTrackCommand): Promise<void> {
    // 1. Validate
    await this.validator.validate(command);

    // 2. Load Aggregate
    const userHistory = await this.eventStore.load(
      `user_history_${command.userId}`
    );

    // 3. Execute Domain Logic
    const event = userHistory.playTrack(
      command.trackId,
      command.deviceType,
      command.position
    );

    // 4. Persist Event
    await this.eventStore.append(
      `user_history_${command.userId}`,
      [event]
    );

    // 5. Event Bus'a publish et (projections için)
    await this.eventBus.publish(event);
  }
}

// Aggregate Root
class UserHistoryAggregate {
  private events: DomainEvent[] = [];

  playTrack(
    trackId: string,
    deviceType: string,
    position: number
  ): TrackPlayedEvent {
    // Domain rules
    if (this.recentlyPlayed(0).trackId === trackId) {
      throw new DomainError("Track already playing");
    }

    const event: TrackPlayedEvent = {
      type: "TRACK_PLAYED",
      trackId,
      deviceType,
      position,
      timestamp: new Date(),
    };

    this.events.push(event);
    return event;
  }

  // Snapshot ile aggregate reconstruction
  static fromEvents(events: DomainEvent[]): UserHistoryAggregate {
    const aggregate = new UserHistoryAggregate();
    events.forEach(e => aggregate.apply(e));
    return aggregate;
  }
}
```

### Query Implementation

```typescript
// Read Model
interface TrackReadModel {
  id: string;
  title: string;
  artist: string;
  album: string;
  duration: number;
  genres: string[];
  playCount: number;
  lastPlayedAt: Date | null;
  artworkUrl: string;
  audioQuality: string;
}

// Query Handler
class SearchTracksHandler implements QueryHandler<SearchTracksQuery> {
  constructor(
    private searchIndex: ElasticsearchClient,
    private cache: RedisCache,
  ) {}

  async handle(query: SearchTracksQuery): Promise<PaginatedResult<TrackReadModel>> {
    const cacheKey = `search:${query.userId}:${query.term}:${query.page}`;

    // Cache'den kontrol
    const cached = await this.cache.get<PaginatedResult<TrackReadModel>>(cacheKey);
    if (cached) return cached;

    // Elasticsearch'ten ara
    const results = await this.searchIndex.search({
      index: "tracks",
      body: {
        query: {
          bool: {
            must: [
              {
                multi_match: {
                  query: query.term,
                  fields: ["title^3", "artist^2", "album", "genres"],
                  fuzziness: "AUTO",
                },
              },
            ],
            filter: [
              { term: { status: "published" } },
            ],
          },
        },
        highlight: {
          fields: {
            title: {},
            artist: {},
          },
        },
        from: (query.page - 1) * query.pageSize,
        size: query.pageSize,
      },
    });

    const result = {
      items: results.hits.hits.map(hit => ({
        ...hit._source,
        score: hit._score,
      })),
      total: results.hits.total.value,
      page: query.page,
      pageSize: query.pageSize,
    };

    // Cache'e kaydet (5 dakika)
    await this.cache.set(cacheKey, result, { ttl: 300 });

    return result;
  }
}
```

### Projection Engine

```typescript
// Event → Read Model projection
class TrackPlayCountProjection {
  constructor(
    private readDb: PostgresPool,
    private eventBus: EventBus,
  ) {}

  async start() {
    this.eventBus.subscribe("TRACK_PLAYED", async (event) => {
      await this.handleTrackPlayed(event);
    });

    this.eventBus.subscribe("TRACK_LIKED", async (event) => {
      await this.handleTrackLiked(event);
    });
  }

  private async handleTrackPlayed(event: TrackPlayedEvent) {
    const query = `
      INSERT INTO track_play_counts (track_id, play_count, last_played_at, updated_at)
      VALUES ($1, 1, $2, NOW())
      ON CONFLICT (track_id) DO UPDATE SET
        play_count = track_play_counts.play_count + 1,
        last_played_at = EXCLUDED.last_played_at,
        updated_at = NOW()
    `;
    await this.readDb.query(query, [event.trackId, event.timestamp]);
  }
}
```

### Event Store Schema

```sql
-- PostgreSQL Event Store
CREATE TABLE events (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  stream_id VARCHAR(255) NOT NULL,
  event_type VARCHAR(100) NOT NULL,
  payload JSONB NOT NULL,
  metadata JSONB DEFAULT '{}',
  version INTEGER NOT NULL,
  created_at TIMESTAMPTZ DEFAULT NOW(),

  UNIQUE(stream_id, version)
);

CREATE INDEX idx_events_stream ON events(stream_id, version);
CREATE INDEX idx_events_type ON events(event_type, created_at);

-- Snapshot tablosu
CREATE TABLE snapshots (
  stream_id VARCHAR(255) PRIMARY KEY,
  version INTEGER NOT NULL,
  state JSONB NOT NULL,
  created_at TIMESTAMPTZ DEFAULT NOW()
);
```

## Konfigürasyon

```yaml
# cqrs-config.yaml
cqrs:
  command_side:
    event_store:
      type: postgresql
      connection: "postgres://coremusic:secret@localhost:5432/events"
      pool_size: 20
      snapshot_interval: 100  # Her 100 event'te snapshot al

  query_side:
    read_database:
      type: elasticsearch
      hosts: ["http://localhost:9200"]
      index_prefix: "coremusic"

    cache:
      type: redis
      connection: "redis://localhost:6379"
      default_ttl: 300
      max_memory: "1gb"

  projection:
    workers: 4
    batch_size: 100
    retry_count: 3
    dead_letter_queue: true
```

## Bağımlılıklar

### Bağımlı Olduğu
- **K0 OS**: PostgreSQL, Elasticsearch, Redis
- **K9 Event Bus**: Event publishing/subscribing

### Bağımlı Olan
- **K9 API Gateway**: Command/Query routing
- **K10 Services**: Domain logic

## Durum: Implementasyon

- [x] CQRS pattern design
- [x] Command/Query separation
- [ ] Event Store (PostgreSQL)
- [ ] Projection engine
- [ ] Read model (Elasticsearch)
- [ ] Snapshot mechanism
- [ ] Dead letter queue
- [ ] Read model caching (Redis)
- [ ] Event replay capability
- [ ] Monitoring & metrics
