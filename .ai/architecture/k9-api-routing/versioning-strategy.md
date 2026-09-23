---
title: "API Versioning Stratejisi"
layer: K9
category: "API & Routing"
date: 2026-09-20
status: "tamamlandı"
---

# API Versioning Stratejisi

## Genel Bakış

API versioning, COREMUSIC API'lerinin zaman içinde değişen gereksinimlere uyum sağlamasını sağlar. URL path, header ve query parametre kombinasyonu ile version belirleme desteklenir. Backward compatibility guarantee, deprecation policy ve migration guides ile sorunsuz geçiş sağlanır.

Her major version için minimum 12 ay destek garantisi verilir. Sunset header ile deprecated versiyonlar için advance notice verilir. Client SDK'ler otomatik version upgrade desteği sunar.

## API Tanımı

### Version Format

```
URL Path:     /api/v1/tracks
Header:       Accept-Version: v1
Query Param:  /api/tracks?version=1
```

### Version Lifecycle

| Faz | Süre | Açıklama |
|-----|------|----------|
| Active | 12 ay | Tam destek, yeni feature'lar |
| Deprecated | 6 ay | Sadece bug fix, deprecation warning |
| Sunset | 3 ay | Rate limit düşürülmesi |
| Retired | - | Request'ler 410 döner |

## Teknik Detaylar

### Version Router Implementation

```typescript
// versioning/VersionRouter.ts
import { Router, Request, Response, NextFunction } from "express";

interface VersionConfig {
  version: string;
  status: "active" | "deprecated" | "sunset" | "retired";
  releaseDate: Date;
  deprecationDate?: Date;
  sunsetDate?: Date;
  minClientVersion?: string;
}

class VersionRouter {
  private versions: Map<string, VersionConfig>;
  private routes: Map<string, Router>;

  constructor() {
    this.versions = new Map();
    this.routes = new Map();
  }

  registerVersion(config: VersionConfig, router: Router): void {
    this.versions.set(config.version, config);
    this.routes.set(config.version, router);
  }

  middleware() {
    return (req: Request, res: Response, next: NextFunction) => {
      const version = this.resolveVersion(req);
      const config = this.versions.get(version);

      if (!config) {
        return res.status(400).json({
          code: "INVALID_VERSION",
          message: `API version '${version}' is not supported`,
          availableVersions: Array.from(this.versions.keys()),
        });
      }

      // Version lifecycle checks
      if (config.status === "retired") {
        return res.status(410).json({
          code: "VERSION_RETIRED",
          message: `API version '${version}' has been retired`,
          sunsetDate: config.sunsetDate,
          migrationGuide: `https://docs.coremusic.com/migrate-to-v${this.getNextVersion(version)}`,
        });
      }

      // Deprecation headers
      if (config.status === "deprecated" || config.status === "sunset") {
        res.setHeader("Deprecation", "true");
        res.setHeader("Sunset", config.sunsetDate?.toUTCString() || "");
        res.setHeader("Link", [
          `<https://docs.coremusic.com/deprecations/${version}>; rel="deprecation",
          `<https://api.coremusic.com/${this.getNextVersion(version)}>; rel="successor-version"`,
        ]);
      }

      // Attach version to request
      req.apiVersion = version;
      req.versionConfig = config;

      // Route to versioned handler
      const router = this.routes.get(version);
      if (router) {
        router(req, res, next);
      } else {
        next();
      }
    };
  }

  private resolveVersion(req: Request): string {
    // 1. URL path'ten: /api/v1/tracks → v1
    const pathMatch = req.path.match(/^\/api\/(v\d+)\//);
    if (pathMatch) return pathMatch[1];

    // 2. Header'dan: Accept-Version: v1
    const headerVersion = req.headers["accept-version"] as string;
    if (headerVersion) return headerVersion;

    // 3. Query param'dan: ?version=1
    const queryVersion = req.query.version as string;
    if (queryVersion) return `v${queryVersion}`;

    // 4. Default version
    return this.getDefaultVersion();
  }

  private getDefaultVersion(): string {
    const activeVersions = Array.from(this.versions.entries())
      .filter(([_, config]) => config.status === "active")
      .sort((a, b) => b[1].releaseDate.getTime() - a[1].releaseDate.getTime());

    return activeVersions[0]?.[0] || "v1";
  }

  private getNextVersion(current: string): string {
    const num = parseInt(current.replace("v", ""));
    return `v${num + 1}`;
  }
}
```

### Version-Specific Routes

```typescript
// routes/v1/tracks.ts
import { Router } from "express";

const router = Router();

// v1: Artist object nested
router.get("/tracks/:id", async (req, res) => {
  const track = await trackService.getTrack(req.params.id);

  // v1 format: artist is object
  res.json({
    id: track.id,
    title: track.title,
    artist: {
      id: track.artistId,
      name: track.artistName,
    },
    album: {
      id: track.albumId,
      title: track.albumTitle,
    },
    duration: track.duration,
  });
});

export default router;

// routes/v2/tracks.ts
import { Router } from "express";

const router = Router();

// v2: Artist ID only, use includes pattern
router.get("/tracks/:id", async (req, res) => {
  const track = await trackService.getTrack(req.params.id);
  const includes = req.query.include?.split(",") || [];

  const response: any = {
    id: track.id,
    title: track.title,
    artistId: track.artistId,
    albumId: track.albumId,
    duration: track.duration,
  };

  // Conditional includes
  if (includes.includes("artist")) {
    response.artist = await artistService.getArtist(track.artistId);
  }
  if (includes.includes("album")) {
    response.album = await albumService.getAlbum(track.albumId);
  }

  res.json(response);
});

export default router;
```

### Client SDK Version Selection

```typescript
// sdk/version-selection.ts
interface ClientConfig {
  apiVersion: string;
  baseUrl: string;
  autoUpgrade: boolean;
}

class CoreMusicClient {
  private config: ClientConfig;

  constructor(config: Partial<ClientConfig> = {}) {
    this.config = {
      apiVersion: config.apiVersion || "v1",
      baseUrl: config.baseUrl || "https://api.coremusic.com",
      autoUpgrade: config.autoUpgrade ?? true,
    };
  }

  async request<T>(
    method: string,
    path: string,
    options?: RequestOptions
  ): Promise<T> {
    const url = `${this.config.baseUrl}/api/${this.config.apiVersion}${path}`;

    const response = await fetch(url, {
      method,
      headers: {
        "Content-Type": "application/json",
        "Accept-Version": this.config.apiVersion,
        ...options?.headers,
      },
      body: options?.body ? JSON.stringify(options.body) : undefined,
    });

    // Deprecation warning
    if (response.headers.get("Deprecation") === "true") {
      console.warn(
        `⚠️ API ${this.config.apiVersion} is deprecated. ` +
        `Migrate to ${response.headers.get("Link")?.match(/successor-version=(v\d+)/)?.[1] || "latest"}`
      );

      if (this.config.autoUpgrade) {
        await this.upgradeVersion(response);
      }
    }

    return response.json();
  }

  private async upgradeVersion(response: Response): Promise<void> {
    const successorMatch = response.headers.get("Link")
      ?.match(/successor-version=(v\d+)/);

    if (successorMatch) {
      const newVersion = successorMatch[1];
      console.log(`Auto-upgrading to ${newVersion}`);
      this.config.apiVersion = newVersion;
    }
  }
}
```

### Migration Guides

```yaml
# migrations/v1-to-v2.yaml
migration:
  from: v1
  to: v2
  breaking_changes:
    - description: "Artist response format changed"
      endpoint: "GET /tracks/:id"
      before: |
        { "artist": { "id": "...", "name": "..." } }
      after: |
        { "artistId": "..." }
      resolution: "Use ?include=artist for nested artist object"

    - description: "Pagination format changed"
      endpoint: "GET /tracks"
      before: |
        { "items": [...], "total": 100, "page": 1, "perPage": 20 }
      after: |
        { "items": [...], "pagination": { "total": 100, "page": 1, "pageSize": 20, "hasMore": true } }
      resolution: "Update pagination parsing logic"

    - description: "Error format changed"
      endpoint: "All endpoints"
      before: |
        { "error": "message" }
      after: |
        { "code": "ERROR_CODE", "message": "Human readable message", "details": {} }
      resolution: "Update error handling"

  automated_tools:
    - name: "@coremusic/migration-cli"
      command: "npx @coremusic/migration-cli v1-to-v2 --dry-run"
```

## Konfigürasyon

```yaml
# versioning-config.yaml
versioning:
  default_version: "v1"
  supported_versions:
    v1:
      status: deprecated
      release_date: "2025-01-01"
      deprecation_date: "2026-01-01"
      sunset_date: "2026-07-01"
      min_client_version: "1.0.0"

    v2:
      status: active
      release_date: "2026-01-01"
      min_client_version: "2.0.0"

  resolution_order:
    - url_path     # /api/v1/...
    - header       # Accept-Version
    - query        # ?version=1
    - default

  response_headers:
    deprecation: true
    sunset: true
    successor_version: true
    api_version: true  # X-API-Version header

  migration:
    auto_upgrade_sdk: true
    migration_guide_base: "https://docs.coremusic.com/migrations"
```

## Bağımlılıklar

### Bağımlı Olduğu
- **K9 API Gateway**: Version routing
- **K9 OpenAPI**: Version-specific specs

### Bağımlı Olan
- **K11-K20 Clients**: Version-aware API clients
- **CI/CD**: Version compatibility testing

## Durum: Implementasyon

- [x] Version resolution logic
- [x] URL path versioning
- [x] Header versioning
- [x] Deprecation headers
- [x] Sunset headers
- [ ] Auto-upgrade middleware
- [ ] Migration CLI tool
- [ ] Version compatibility testing
- [ ] Client SDK version management
- [ ] Admin dashboard (version status)
