---
title: "Response Caching & Cache Invalidation"
layer: K9
category: "API & Routing"
date: 2026-09-20
status: "tamamlandı"
---

# Response Caching

## Genel Bakış

Response Caching, COREMUSIC API'lerinin response sürelerini ve server load'unu optimize eden caching stratejisidir. Multi-tier caching (CDN Edge → Application → Database) ile performans maximize edilir. ETags ve conditional requests ile bandwidth kullanımı optimize edilir.

Cache invalidation stratejileri ile stale data önlenir. Write-through, write-behind ve cache-aside patterns desteklenir. Redis Cluster ile distributed caching, CDN ile edge caching sağlanır.

## API Tanımı

### Cache Headers

| Header | Açıklama |
|--------|----------|
| `Cache-Control` | Cache policy (max-age, no-cache, private, public) |
| `ETag` | Response fingerprint (conditional request) |
| `Last-Modified` | Son değiştirme zamanı |
| `If-None-Match` | ETag comparison (304 dönerse cache hit) |
| `If-Modified-Since` | Time-based conditional request |
| `Vary` | Cache key parametreleri |

### Cache Tiers

| Tier | Konum | TTL | Kullanım |
|------|-------|-----|----------|
| L1 | Application Memory (in-process) | 30s | Hot data |
| L2 | Redis Cluster | 5min-24h | Shared cache |
| L3 | CDN Edge | 1h-7d | Static content |
| L4 | Browser Cache | varies | Client-side |

## Teknik Detaylar

### Caching Mimarisi

```
┌─────────────────────────────────────────────────────────────────┐
│                    RESPONSE CACHING                              │
│                                                                 │
│  Client Request                                                 │
│       │                                                         │
│       ▼                                                         │
│  ┌────────────────┐                                             │
│  │  CDN Edge      │ ← L3: Static assets, public API            │
│  │  (CloudFlare)  │   TTL: 1-7 days                             │
│  └───────┬────────┘                                             │
│          │ MISS                                                 │
│          ▼                                                      │
│  ┌────────────────┐                                             │
│  │  API Gateway   │ ← Rate limit, auth check                    │
│  │  (K9)          │                                             │
│  └───────┬────────┘                                             │
│          │                                                      │
│          ▼                                                      │
│  ┌────────────────┐                                             │
│  │  L1: App       │ ← In-process LRU cache                     │
│  │  Memory Cache  │   TTL: 30s, Max: 1000 entries               │
│  └───────┬────────┘                                             │
│          │ MISS                                                 │
│          ▼                                                      │
│  ┌────────────────┐                                             │
│  │  L2: Redis     │ ← Distributed cache (Redis Cluster)        │
│  │  Cluster       │   TTL: 5min-24h, Max: 10GB                 │
│  └───────┬────────┘                                             │
│          │ MISS                                                 │
│          ▼                                                      │
│  ┌────────────────┐                                             │
│  │  BFF Layer     │ ← Business logic, aggregation               │
│  └───────┬────────┘                                             │
│          │                                                      │
│          ▼                                                      │
│  ┌────────────────┐                                             │
│  │  Service Layer │ ← Microservices                             │
│  └───────┬────────┘                                             │
│          │                                                      │
│          ▼                                                      │
│  ┌────────────────┐                                             │
│  │  Database      │ ← PostgreSQL, Elasticsearch                 │
│  │  (Source of    │                                             │
│  │   Truth)       │                                             │
│  └────────────────┘                                             │
│                                                                 │
│  Cache Write-Through:                                          │
│  ┌──────┐  ┌──────┐  ┌──────┐                                 │
│  │Write │→ │Cache │→ │DB    │                                 │
│  │      │  │Update│  │Update│                                 │
│  └──────┘  └──────┘  └──────┘                                 │
│                                                                 │
│  Cache Invalidation Bus:                                       │
│  ┌──────┐  ┌──────┐  ┌──────┐                                 │
│  │Event │→ │Invalid│→ │Clear │                                 │
│  │Pub   │  │ator   │  │Cache │                                 │
│  └──────┘  └──────┘  └──────┘                                 │
└─────────────────────────────────────────────────────────────────┘
```

### Multi-Tier Cache Implementation

```typescript
// cache/MultiTierCache.ts
interface CacheConfig {
  l1: { maxEntries: number; ttlMs: number };
  l2: { redisUrl: string; prefix: string };
  l3: { cdnEnabled: boolean; cdnUrl: string };
}

class MultiTierCache {
  private l1: LRUCache<string, any>;
  private l2: Redis;
  private config: CacheConfig;

  constructor(config: CacheConfig) {
    this.config = config;
    this.l1 = new LRUCache({
      max: config.l1.maxEntries,
      ttl: config.l1.ttlMs,
    });
    this.l2 = new Redis(config.l2.redisUrl);
  }

  async get<T>(key: string): Promise<{ data: T; tier: string } | null> {
    // L1 check
    const l1Result = this.l1.get(key);
    if (l1Result) {
      return { data: l1Result as T, tier: "l1" };
    }

    // L2 check (Redis)
    const l2Result = await this.l2.get(`${this.config.l2.prefix}:${key}`);
    if (l2Result) {
      const parsed = JSON.parse(l2Result) as T;
      // L1'e promoted et
      this.l1.set(key, parsed);
      return { data: parsed, tier: "l2" };
    }

    return null;
  }

  async set(
    key: string,
    value: any,
    options: {
      ttlMs?: number;
      tags?: string[];
      cacheControl?: string;
    } = {}
  ): Promise<void> {
    const serialized = JSON.stringify(value);

    // L1 store
    this.l1.set(key, value);

    // L2 store (Redis)
    const ttlSeconds = Math.ceil((options.ttlMs || 300000) / 1000);
    await this.l2.setex(
      `${this.config.l2.prefix}:${key}`,
      ttlSeconds,
      serialized
    );

    // Tags store (invalidation için)
    if (options.tags) {
      for (const tag of options.tags) {
        await this.l2.sadd(`${this.config.l2.prefix}:tag:${tag}`, key);
        await this.l2.expire(
          `${this.config.l2.prefix}:tag:${tag}`,
          ttlSeconds
        );
      }
    }
  }

  async invalidateByTag(tag: string): Promise<void> {
    const keys = await this.l2.smembers(
      `${this.config.l2.prefix}:tag:${tag}`
    );

    if (keys.length === 0) return;

    // L1'den temizle
    for (const key of keys) {
      this.l1.delete(key);
    }

    // L2'den temizle
    const pipeline = this.l2.pipeline();
    for (const key of keys) {
      pipeline.del(`${this.config.l2.prefix}:${key}`);
    }
    pipeline.del(`${this.config.l2.prefix}:tag:${tag}`);
    await pipeline.exec();
  }

  async invalidateByPattern(pattern: string): Promise<void> {
    const keys = await this.l2.keys(`${this.config.l2.prefix}:${pattern}`);

    for (const key of keys) {
      const cacheKey = key.replace(`${this.config.l2.prefix}:`, "");
      this.l1.delete(cacheKey);
    }

    if (keys.length > 0) {
      await this.l2.del(...keys);
    }
  }
}
```

### ETag Generation

```typescript
// cache/ETagGenerator.ts
import { createHash } from "crypto";

class ETagGenerator {
  // Strong ETag (tam eşleşme)
  static strong(body: any): string {
    const hash = createHash("sha256")
      .update(JSON.stringify(body))
      .digest("hex")
      .slice(0, 16);
    return `"${hash}"`;
  }

  // Weak ETag (semantik eşleşme)
  static weak(body: any): string {
    const hash = createHash("sha1")
      .update(JSON.stringify(body))
      .digest("hex")
      .slice(0, 16);
    return `W/"${hash}"`;
  }

  // Resource-based ETag (version + lastModified)
  static fromResource(resource: {
    id: string;
    version: number;
    updatedAt: Date;
  }): string {
    const data = `${resource.id}:${resource.version}:${resource.updatedAt.getTime()}`;
    const hash = createHash("md5").update(data).digest("hex");
    return `"${hash}"`;
  }
}
```

### Conditional Request Handler

```typescript
// middleware/conditionalRequest.ts
export function conditionalRequestMiddleware(
  cache: MultiTierCache
) {
  return async (req: Request, res: Response, next: NextFunction) => {
    const cacheKey = generateCacheKey(req);

    // Cached response'u kontrol et
    const cached = await cache.get(cacheKey);
    if (!cached) {
      return next();
    }

    const cachedETag = ETagGenerator.strong(cached.data);
    const ifNoneMatch = req.headers["if-none-match"];
    const ifModifiedSince = req.headers["if-modified-since"];

    // ETag comparison
    if (ifNoneMatch === cachedETag) {
      return res.status(304).end();
    }

    // Last-Modified comparison
    if (ifModifiedSince) {
      const modifiedDate = new Date(cached.data.updatedAt);
      if (modifiedDate <= new Date(ifModifiedSince)) {
        return res.status(304).end();
      }
    }

    // Cache hit - headers ekle
    res.setHeader("ETag", cachedETag);
    res.setHeader("Cache-Control", cached.cacheControl || "private, max-age=300");
    res.setHeader("X-Cache", "HIT");
    res.setHeader("X-Cache-Tier", cached.tier);

    return res.json(cached.data);
  };
}

// Response interceptor - cache store
export function cacheStoreMiddleware(cache: MultiTierCache) {
  return async (req: Request, res: Response, next: NextFunction) => {
    const originalJson = res.json.bind(res);

    res.json = function (body: any) {
      // Cacheable response'ları kaydet
      if (isCacheable(req, res)) {
        const cacheKey = generateCacheKey(req);
        const cacheControl = res.getHeader("Cache-Control") as string;

        cache.set(cacheKey, body, {
          ttlMs: parseCacheControl(cacheControl),
          tags: getCacheTags(req),
          cacheControl,
        });

        // ETag header ekle
        const etag = ETagGenerator.strong(body);
        res.setHeader("ETag", etag);
        res.setHeader("X-Cache", "MISS");
      }

      return originalJson(body);
    };

    next();
  };
}
```

### Cache Invalidation Bus

```typescript
// cache/InvalidationBus.ts
class CacheInvalidationBus {
  private cache: MultiTierCache;
  private eventBus: EventBus;

  constructor(cache: MultiTierCache, eventBus: EventBus) {
    this.cache = cache;
    this.eventBus = eventBus;
    this.setupSubscriptions();
  }

  private setupSubscriptions(): void {
    // Track changes
    this.eventBus.subscribe("TRACK_UPDATED", async (event) => {
      await this.cache.invalidateByTag(`track:${event.trackId}`);
      await this.cache.invalidateByPattern(`tracks:*`);
    });

    this.eventBus.subscribe("TRACK_DELETED", async (event) => {
      await this.cache.invalidateByTag(`track:${event.trackId}`);
      await this.cache.invalidateByPattern(`tracks:*`);
      await this.cache.invalidateByPattern(`search:*`);
    });

    // Playlist changes
    this.eventBus.subscribe("PLAYLIST_UPDATED", async (event) => {
      await this.cache.invalidateByTag(`playlist:${event.playlistId}`);
      await this.cache.invalidateByTag(`user:${event.userId}:playlists`);
    });

    // User changes
    this.eventBus.subscribe("USER_UPDATED", async (event) => {
      await this.cache.invalidateByTag(`user:${event.userId}`);
    });

    // Artist/Album changes
    this.eventBus.subscribe("ARTIST_UPDATED", async (event) => {
      await this.cache.invalidateByTag(`artist:${event.artistId}`);
      await this.cache.invalidateByPattern(`artist:${event.artistId}:*`);
    });

    this.eventBus.subscribe("ALBUM_UPDATED", async (event) => {
      await this.cache.invalidateByTag(`album:${event.albumId}`);
      await this.cache.invalidateByPattern(`albums:*`);
    });
  }
}
```

### Cache Middleware

```typescript
// middleware/cache.ts
export function cacheMiddleware(
  cache: MultiTierCache,
  options: {
    ttl?: number;
    tags?: string[];
    keyGenerator?: (req: Request) => string;
    condition?: (req: Request) => boolean;
  } = {}
) {
  const ttl = options.ttl || 300; // 5 dakika default

  return async (req: Request, res: Response, next: NextFunction) => {
    // Condition check
    if (options.condition && !options.condition(req)) {
      return next();
    }

    // Cache key generation
    const key = options.keyGenerator
      ? options.keyGenerator(req)
      : generateCacheKey(req);

    // Cache lookup
    const cached = await cache.get(key);
    if (cached) {
      res.setHeader("X-Cache", "HIT");
      res.setHeader("X-Cache-Tier", cached.tier);
      return res.json(cached.data);
    }

    // Cache miss - continue to handler
    res.setHeader("X-Cache", "MISS");
    next();
  };
}

// Specific cache configurations
export const cacheConfigs = {
  // Kısa TTL - frequently changing data
  realtime: {
    ttl: 30, // 30 saniye
    tags: [],
    condition: (req: Request) => req.method === "GET",
  },

  // Orta TTL - normal API responses
  normal: {
    ttl: 300, // 5 dakika
    tags: [],
    condition: (req: Request) => req.method === "GET",
  },

  // Uzun TTL - rarely changing data
  static: {
    ttl: 3600, // 1 saat
    tags: ["static"],
    condition: (req: Request) => req.method === "GET",
  },

  // User-specific cache
  userSpecific: {
    ttl: 120, // 2 dakika
    keyGenerator: (req: Request) =>
      `user:${req.user?.id}:${req.originalUrl}`,
    condition: (req: Request) =>
      req.method === "GET" && !!req.user,
  },
};
```

## Konfigürasyon

```yaml
# response-caching.yaml
caching:
  l1:
    enabled: true
    max_entries: 1000
    ttl_ms: 30000  # 30 saniye
    eviction_policy: "lru"

  l2:
    enabled: true
    redis_cluster:
      nodes:
        - "redis-1:6379"
        - "redis-2:6379"
        - "redis-3:6379"
    prefix: "coremusic:cache"
    max_memory: "10gb"
    eviction_policy: "allkeys-lru"

  l3:
    cdn_enabled: true
    cdn_provider: "cloudflare"
    cdn_zone_id: "${CF_ZONE_ID}"
    purge_api_key: "${CF_API_KEY}"

    cache_rules:
      - pattern: "/api/v1/tracks/*/artwork*"
        ttl: 86400  # 1 gün
        immutable: true
      - pattern: "/api/v1/static/*"
        ttl: 604800  # 7 gün
        immutable: true
      - pattern: "/api/v1/*"
        ttl: 300  # 5 dakika
        bypass_on_cookie: "nocache"

  etag:
    enabled: true
    algorithm: "sha256"
    strong_etag: true

  invalidation:
    enabled: true
    strategy: "event-driven"  # event-driven | ttl-only | hybrid
    event_bus: "kafka"
    batch_size: 100
    debounce_ms: 100

  default_headers:
    cache_control:
      public_api: "public, max-age=300, stale-while-revalidate=60"
      private_api: "private, max-age=60, must-revalidate"
      authenticated: "private, no-cache, no-store"
      static_asset: "public, max-age=604800, immutable"
```

## Bağımlılıklar

### Bağımlı Olduğu
- **Redis Cluster**: Distributed cache
- **K9 Event Bus**: Invalidation events
- **CDN**: Edge caching

### Bağımlı Olan
- **K9 API Gateway**: Cache middleware
- **K9 BFF**: BFF-level caching
- **K10 Services**: Cache tags, invalidation events

## Durum: Implementasyon

- [x] L1 in-memory cache (LRU)
- [x] L2 Redis cache
- [x] ETag generation
- [x] Conditional requests (304)
- [x] Cache-Control headers
- [ ] L3 CDN integration
- [ ] Cache invalidation bus
- [ ] Tag-based invalidation
- [ ] Pattern-based invalidation
- [ ] Cache warming
- [ ] Cache monitoring & metrics
- [ ] Admin dashboard (cache stats)
