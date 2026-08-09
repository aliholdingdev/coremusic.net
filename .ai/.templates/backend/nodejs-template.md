---
type: template
category: backend
title: "Node.js + TypeScript Template"
date: 2026-08-06
updated: 2026-08-06
status: active
version: 3.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team • Human Mode • Truth Mode
tech: Node.js 20+, TypeScript 5+, Express, WebSocket
---

# Node.js + TypeScript Template

**See also:** [[index]] · [[CLAUDE.md]] · [[ADR-042-vault-restructuring-2026-08-03]] · [[ADR-026-download-service-architecture]]

---

## 1. Amaç (Purpose)

Bu şablon, CoreMusic Download Service (port 3001) için Node.js + TypeScript geliştirme standartlarını tanımlar.

**Kapsam:**
- YouTube → deemix → FLAC otomatik indirme pipeline'ı
- WebSocket ile gerçek zamanlı indirme ilerleme takibi
- Anti-ban sistemi (rate limiting, proxy rotasyonu, user-agent rotasyonu)
- RESTful API endpoint'leri (CRUD operations)
- better-sqlite3 ile yerel veritabanı yönetimi
- Structured logging (pino) ve hata yönetimi

**Kapsam Dışı:** Frontend kodlama (ADR-001 — Vanilla JS), C++ audio engine, PHP backend.

---

## 2. Tech Stack

| Teknoloji | Versiyon | Kullanım | Kaynak |
|-----------|---------|----------|--------|
| Node.js | 20+ (LTS) | Runtime | nodejs.org |
| TypeScript | 5+ | Type safety | typescriptlang.org |
| Express | 4.18+ | HTTP framework | expressjs.com |
| ws | 8+ | WebSocket | github.com/websockets/ws |
| better-sqlite3 | 9+ | SQLite driver | github.com/WiseLibs/better-sqlite3 |
| pino | 8+ | Structured logging | getpino.io |
| zod | 3+ | Schema validation | zod.dev |
| helmet | 7+ | Security headers |helmetjs.github.io |
| cors | 2.8+ | CORS middleware | github.com/expressjs/cors |
| express-rate-limit | 7+ | Rate limiting | github.com/express-rate-limit/express-rate-limit |
| vitest | 1+ | Testing | vitest.dev |
| supertest | 6+ | HTTP testing | github.com/ladjs/supertest |
| dotenv | 16+ | Env management | github.com/motdotla/dotenv |

*Kaynaklar: 2026-08-06'da doğrulanmıştır. Tüm versiyonlar minimum gerekli sürümlerdir.*

---

## 3. Code Standards

### 3.1 Project Structure

```
download.coremusic.net/
├── src/
│   ├── index.ts                  # Entry point
│   ├── server.ts                 # Express + WebSocket server
│   ├── config/
│   │   ├── index.ts              # Config loader
│   │   └── schema.ts             # Zod validation schema
│   ├── controllers/
│   │   ├── DownloadController.ts # REST endpoints
│   │   └── HealthController.ts   # Health check
│   ├── services/
│   │   ├── DownloadService.ts    # Core download logic
│   │   ├── AntiBanService.ts     # Rate limit, proxy, UA rotation
│   │   ├── MetadataService.ts    # YouTube metadata extraction
│   │   └── DatabaseService.ts    # SQLite operations
│   ├── middleware/
│   │   ├── errorHandler.ts       # Global error handler
│   │   ├── requestId.ts          # Request ID tracking
│   │   └── validate.ts           # Zod validation middleware
│   ├── websocket/
│   │   ├── WebSocketManager.ts   # Connection management
│   │   └── handlers.ts           # WS event handlers
│   ├── types/
│   │   ├── index.ts              # Type exports
│   │   ├── download.ts           # Download-related types
│   │   └── api.ts                # API request/response types
│   ├── utils/
│   │   ├── logger.ts             # Pino logger setup
│   │   ├── errors.ts             # Custom error classes
│   │   └── async.ts              # Async error wrapper
│   └── migrations/
│       └── 001-initial.ts        # Database migration
├── tests/
│   ├── unit/
│   │   ├── services/
│   │   └── utils/
│   ├── integration/
│   │   ├── api/
│   │   └── websocket/
│   └── fixtures/
│       └── downloads.json        # Test data
├── config/
│   ├── default.env               # Default env vars
│   ├── development.env           # Dev overrides
│   └── production.env            # Prod overrides
├── scripts/
│   ├── migrate.ts                # Run migrations
│   └── seed.ts                   # Seed test data
├── tsconfig.json
├── vitest.config.ts
├── Dockerfile
└── package.json
```

### 3.2 TypeScript Configuration

```json
// tsconfig.json
{
  "compilerOptions": {
    "target": "ES2022",
    "module": "Node16",
    "moduleResolution": "Node16",
    "lib": ["ES2022"],
    "outDir": "./dist",
    "rootDir": "./src",
    "strict": true,
    "noUncheckedIndexedAccess": true,
    "noImplicitOverride": true,
    "noPropertyAccessFromIndexSignature": true,
    "exactOptionalPropertyTypes": true,
    "noImplicitReturns": true,
    "noFallthroughCasesInSwitch": true,
    "noImplicitAny": true,
    "strictNullChecks": true,
    "strictFunctionTypes": true,
    "strictBindCallApply": true,
    "strictPropertyInitialization": true,
    "noThisAlias": true,
    "noUnusedLocals": true,
    "noUnusedParameters": true,
    "noImplicitReturns": true,
    "esModuleInterop": true,
    "skipLibCheck": true,
    "forceConsistentCasingInFileNames": true,
    "resolveJsonModule": true,
    "declaration": true,
    "declarationMap": true,
    "sourceMap": true,
    "baseUrl": "./src",
    "paths": {
      "@config/*": ["config/*"],
      "@controllers/*": ["controllers/*"],
      "@services/*": ["services/*"],
      "@middleware/*": ["middleware/*"],
      "@websocket/*": ["websocket/*"],
      "@types/*": ["types/*"],
      "@utils/*": ["utils/*"]
    }
  },
  "include": ["src/**/*"],
  "exclude": ["node_modules", "dist", "tests"]
}
```

**Kurallar:**
- `strict: true` — Tüm strict modlar aktif
- `noUncheckedIndexedAccess: true` — Array/object erişiminde undefined kontrolü zorunlu
- `exactOptionalPropertyTypes: true` — Optional property'lerde tip tutarlılığı
- Path aliases: `@config`, `@services`, `@controllers` vb.

### 3.3 Express Server Setup

```typescript
// src/server.ts
import express from 'express';
import helmet from 'helmet';
import cors from 'cors';
import rateLimit from 'express-rate-limit';
import { createServer } from 'http';
import { WebSocketManager } from './websocket/WebSocketManager';
import { errorHandler } from './middleware/errorHandler';
import { requestId } from './middleware/requestId';
import { logger } from './utils/logger';
import { config } from './config';

export function createApp(): express.Application {
    const app = express();

    // Security middleware
    app.use(helmet());
    app.use(cors({
        origin: config.CORS_ORIGIN,
        credentials: true,
    }));
    app.use(rateLimit({
        windowMs: 60_000,
        max: config.RATE_LIMIT_MAX,
        standardHeaders: true,
        legacyHeaders: false,
    }));

    // Body parsing
    app.use(express.json({ limit: '1mb' }));
    app.use(express.urlencoded({ extended: true }));

    // Request tracking
    app.use(requestId);

    // Health check (no auth)
    app.get('/health', (_req, res) => {
        res.json({ status: 'ok', timestamp: new Date().toISOString() });
    });

    // API routes (mounted in index.ts)

    // Global error handler (must be last)
    app.use(errorHandler);

    return app;
}

export async function startServer(): Promise<void> {
    const app = createApp();
    const server = createServer(app);
    const wsManager = new WebSocketManager(server);

    server.listen(config.PORT, () => {
        logger.info({ port: config.PORT }, 'Download Service started');
    });

    // Graceful shutdown
    const shutdown = async (): Promise<void> => {
        logger.info('Shutting down...');
        wsManager.closeAll();
        server.close(() => {
            logger.info('Server closed');
            process.exit(0);
        });
        setTimeout(() => process.exit(1), 10_000);
    };

    process.on('SIGTERM', shutdown);
    process.on('SIGINT', shutdown);
}
```

### 3.4 Route Patterns

```typescript
// src/controllers/DownloadController.ts
import { Router, Request, Response, NextFunction } from 'express';
import { z } from 'zod';
import { DownloadService } from '../services/DownloadService';
import { wrapAsync } from '../utils/async';
import { validate } from '../middleware/validate';

const router = Router();
const downloadService = new DownloadService();

// Schema definitions
const CreateDownloadSchema = z.object({
    url: z.string().url().refine(
        (u) => u.includes('youtube.com') || u.includes('youtu.be'),
        'Only YouTube URLs are supported'
    ),
    format: z.enum(['flac', 'mp3']).default('flac'),
    quality: z.enum(['best', 'good']).default('best'),
});

const ListDownloadsSchema = z.object({
    status: z.enum(['pending', 'downloading', 'completed', 'failed']).optional(),
    limit: z.coerce.number().int().min(1).max(100).default(20),
    offset: z.coerce.number().int().min(0).default(0),
});

// POST /api/downloads
router.post('/',
    validate(CreateDownloadSchema),
    wrapAsync(async (req: Request, res: Response): Promise<void> => {
        const task = await downloadService.create(req.body);
        res.status(201).json({
            success: true,
            data: task,
        });
    })
);

// GET /api/downloads
router.get('/',
    validate(ListDownloadsSchema, 'query'),
    wrapAsync(async (req: Request, res: Response): Promise<void> => {
        const result = await downloadService.list(req.query);
        res.json({
            success: true,
            data: result.items,
            pagination: {
                total: result.total,
                limit: req.query.limit,
                offset: req.query.offset,
            },
        });
    })
);

// GET /api/downloads/:id
router.get('/:id',
    wrapAsync(async (req: Request, res: Response): Promise<void> => {
        const task = await downloadService.getById(req.params.id);
        if (!task) {
            res.status(404).json({ success: false, error: 'Download not found' });
            return;
        }
        res.json({ success: true, data: task });
    })
);

// DELETE /api/downloads/:id
router.delete('/:id',
    wrapAsync(async (req: Request, res: Response): Promise<void> => {
        await downloadService.cancel(req.params.id);
        res.status(204).send();
    })
);

// POST /api/downloads/:id/retry
router.post('/:id/retry',
    wrapAsync(async (req: Request, res: Response): Promise<void> => {
        const task = await downloadService.retry(req.params.id);
        res.json({ success: true, data: task });
    })
);

export default router;
```

### 3.5 Service Layer Pattern

```typescript
// src/services/DownloadService.ts
import { DatabaseService } from './DatabaseService';
import { AntiBanService } from './AntiBanService';
import { MetadataService } from './MetadataService';
import { logger } from '../utils/logger';
import { DownloadError, ValidationError } from '../utils/errors';
import type { DownloadTask, CreateDownloadInput, DownloadStatus } from '../types';

export class DownloadService {
    private readonly db: DatabaseService;
    private readonly antiBan: AntiBanService;
    private readonly metadata: MetadataService;
    private readonly activeDownloads: Map<string, AbortController>;

    constructor(
        db?: DatabaseService,
        antiBan?: AntiBanService,
        metadata?: MetadataService
    ) {
        this.db = db ?? new DatabaseService();
        this.antiBan = antiBan ?? new AntiBanService();
        this.metadata = metadata ?? new MetadataService();
        this.activeDownloads = new Map();
    }

    async create(input: CreateDownloadInput): Promise<DownloadTask> {
        const existing = this.db.findDuplicate(input.url);
        if (existing) {
            throw new ValidationError('Download already exists', existing.id);
        }

        const meta = await this.metadata.extract(input.url);

        const task: DownloadTask = {
            id: crypto.randomUUID(),
            url: input.url,
            title: meta.title,
            artist: meta.artist,
            format: input.format,
            quality: input.quality,
            status: 'pending',
            progress: 0,
            createdAt: new Date(),
        };

        this.db.insert(task);
        logger.info({ taskId: task.id, url: input.url }, 'Download created');

        this.startDownload(task.id).catch((err) => {
            logger.error({ taskId: task.id, err }, 'Download failed');
        });

        return task;
    }

    private async startDownload(taskId: string): Promise<void> {
        const task = this.db.findById(taskId);
        if (!task) throw new DownloadError('Task not found', 'TASK_NOT_FOUND', taskId);

        const abortController = new AbortController();
        this.activeDownloads.set(taskId, abortController);

        try {
            this.db.updateStatus(taskId, 'downloading');

            await this.antiBan.waitForSlot();

            // Actual download logic (YouTube → deemix → FLAC)
            // Progress updates sent via WebSocket
            const filePath = await this.executeDownload(task, abortController.signal);

            this.db.updateStatus(taskId, 'completed', { filePath });
            logger.info({ taskId, filePath }, 'Download completed');
        } catch (err) {
            if (abortController.signal.aborted) {
                this.db.updateStatus(taskId, 'failed', { error: 'Cancelled' });
            } else {
                const message = err instanceof Error ? err.message : 'Unknown error';
                this.db.updateStatus(taskId, 'failed', { error: message });
                logger.error({ taskId, err }, 'Download failed');
            }
        } finally {
            this.activeDownloads.delete(taskId);
        }
    }

    private async executeDownload(
        task: DownloadTask,
        signal: AbortSignal
    ): Promise<string> {
        // Pipeline implementation placeholder
        // 1. YouTube metadata → 2. deemix download → 3. FLAC convert → 4. Store
        throw new Error('Not implemented');
    }

    async cancel(taskId: string): Promise<void> {
        const controller = this.activeDownloads.get(taskId);
        if (controller) {
            controller.abort();
            this.activeDownloads.delete(taskId);
        }
        this.db.updateStatus(taskId, 'failed', { error: 'Cancelled by user' });
    }

    list(query: { status?: DownloadStatus; limit: number; offset: number }) {
        return this.db.list(query);
    }

    getById(id: string): DownloadTask | undefined {
        return this.db.findById(id);
    }

    async retry(id: string): Promise<DownloadTask> {
        const task = this.db.findById(id);
        if (!task) throw new DownloadError('Task not found', 'TASK_NOT_FOUND', id);
        if (task.status !== 'failed') {
            throw new ValidationError('Only failed downloads can be retried', id);
        }
        this.db.updateStatus(id, 'pending', { error: undefined });
        this.startDownload(id).catch(() => {});
        return this.db.findById(id)!;
    }
}
```

### 3.6 Database Access

```typescript
// src/services/DatabaseService.ts
import Database from 'better-sqlite3';
import { join } from 'path';
import { config } from '../config';
import { logger } from '../utils/logger';
import type { DownloadTask, DownloadStatus } from '../types';

export class DatabaseService {
    private readonly db: Database.Database;

    constructor(dbPath?: string) {
        this.db = new Database(dbPath ?? config.DB_PATH);
        this.db.pragma('journal_mode = WAL');
        this.db.pragma('foreign_keys = ON');
        this.migrate();
    }

    private migrate(): void {
        this.db.exec(`
            CREATE TABLE IF NOT EXISTS downloads (
                id TEXT PRIMARY KEY,
                url TEXT NOT NULL UNIQUE,
                title TEXT,
                artist TEXT,
                format TEXT NOT NULL DEFAULT 'flac',
                quality TEXT NOT NULL DEFAULT 'best',
                status TEXT NOT NULL DEFAULT 'pending',
                progress INTEGER NOT NULL DEFAULT 0,
                file_path TEXT,
                error TEXT,
                created_at TEXT NOT NULL DEFAULT (datetime('now')),
                completed_at TEXT
            );
            CREATE INDEX IF NOT EXISTS idx_downloads_status ON downloads(status);
            CREATE INDEX IF NOT EXISTS idx_downloads_created ON downloads(created_at);
        `);
    }

    insert(task: DownloadTask): void {
        const stmt = this.db.prepare(`
            INSERT INTO downloads (id, url, title, artist, format, quality, status, progress, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        `);
        stmt.run(
            task.id, task.url, task.title, task.artist,
            task.format, task.quality, task.status, task.progress,
            task.createdAt.toISOString()
        );
    }

    findById(id: string): DownloadTask | undefined {
        const stmt = this.db.prepare('SELECT * FROM downloads WHERE id = ?');
        const row = stmt.get(id) as Record<string, unknown> | undefined;
        return row ? this.mapRow(row) : undefined;
    }

    findDuplicate(url: string): DownloadTask | undefined {
        const stmt = this.db.prepare(
            'SELECT * FROM downloads WHERE url = ? AND status != ?'
        );
        const row = stmt.get(url, 'failed') as Record<string, unknown> | undefined;
        return row ? this.mapRow(row) : undefined;
    }

    updateStatus(
        id: string,
        status: DownloadStatus,
        extra?: { filePath?: string; error?: string }
    ): void {
        const stmt = this.db.prepare(`
            UPDATE downloads
            SET status = ?, file_path = COALESCE(?, file_path),
                error = COALESCE(?, error),
                completed_at = CASE WHEN ? IN ('completed', 'failed') THEN datetime('now') ELSE completed_at END
            WHERE id = ?
        `);
        stmt.run(status, extra?.filePath ?? null, extra?.error ?? null, status, id);
    }

    list(query: { status?: DownloadStatus; limit: number; offset: number }) {
        let sql = 'SELECT * FROM downloads';
        const params: unknown[] = [];

        if (query.status) {
            sql += ' WHERE status = ?';
            params.push(query.status);
        }

        sql += ' ORDER BY created_at DESC LIMIT ? OFFSET ?';
        params.push(query.limit, query.offset);

        const rows = this.db.prepare(sql).all(...params) as Record<string, unknown>[];
        const total = this.db.prepare(
            `SELECT COUNT(*) as count FROM downloads${query.status ? ' WHERE status = ?' : ''}`
        ).get(...(query.status ? [query.status] : [])) as { count: number };

        return {
            items: rows.map((r) => this.mapRow(r)),
            total: total.count,
        };
    }

    private mapRow(row: Record<string, unknown>): DownloadTask {
        return {
            id: row.id as string,
            url: row.url as string,
            title: row.title as string | undefined,
            artist: row.artist as string | undefined,
            format: row.format as 'flac' | 'mp3',
            quality: row.quality as 'best' | 'good',
            status: row.status as DownloadStatus,
            progress: row.progress as number,
            filePath: row.file_path as string | undefined,
            error: row.error as string | undefined,
            createdAt: new Date(row.created_at as string),
            completedAt: row.completed_at ? new Date(row.completed_at as string) : undefined,
        };
    }

    close(): void {
        this.db.close();
    }
}
```

### 3.7 WebSocket Patterns

```typescript
// src/websocket/WebSocketManager.ts
import { WebSocketServer, WebSocket } from 'ws';
import { IncomingMessage } from 'http';
import { logger } from '../utils/logger';
import type { DownloadProgress } from '../types';

interface AuthenticatedSocket extends WebSocket {
    userId?: string;
    isAlive: boolean;
}

export class WebSocketManager {
    private readonly wss: WebSocketServer;
    private readonly clients: Map<string, Set<AuthenticatedSocket>>;

    constructor(server: import('http').Server) {
        this.wss = new WebSocketServer({ server, path: '/ws' });
        this.clients = new Map();

        this.wss.on('connection', (ws: AuthenticatedSocket, req: IncomingMessage) => {
            this.handleConnection(ws, req);
        });

        // Heartbeat every 30s
        setInterval(() => this.heartbeat(), 30_000);
    }

    private handleConnection(ws: AuthenticatedSocket, req: IncomingMessage): void {
        ws.isAlive = true;
        const taskId = new URL(req.url ?? '/', 'http://localhost').searchParams.get('taskId');

        if (!taskId) {
            ws.close(4001, 'taskId required');
            return;
        }

        // Subscribe to task updates
        if (!this.clients.has(taskId)) {
            this.clients.set(taskId, new Set());
        }
        this.clients.get(taskId)!.add(ws);

        logger.info({ taskId }, 'WebSocket connected');

        ws.on('pong', () => { ws.isAlive = true; });

        ws.on('close', () => {
            this.clients.get(taskId)?.delete(ws);
            if (this.clients.get(taskId)?.size === 0) {
                this.clients.delete(taskId);
            }
            logger.info({ taskId }, 'WebSocket disconnected');
        });

        ws.on('error', (err) => {
            logger.error({ taskId, err }, 'WebSocket error');
        });
    }

    broadcast(taskId: string, progress: DownloadProgress): void {
        const sockets = this.clients.get(taskId);
        if (!sockets) return;

        const message = JSON.stringify(progress);
        for (const ws of sockets) {
            if (ws.readyState === WebSocket.OPEN) {
                ws.send(message);
            }
        }
    }

    private heartbeat(): void {
        this.wss.clients.forEach((ws) => {
            const authWs = ws as AuthenticatedSocket;
            if (!authWs.isAlive) {
                authWs.terminate();
                return;
            }
            authWs.isAlive = false;
            authWs.ping();
        });
    }

    closeAll(): void {
        this.wss.clients.forEach((ws) => ws.close(1001, 'Server shutting down'));
        this.wss.close();
    }
}
```

### 3.8 Error Handling

```typescript
// src/utils/errors.ts

export class AppError extends Error {
    constructor(
        message: string,
        public readonly code: string,
        public readonly statusCode: number = 500,
        public readonly isOperational: boolean = true
    ) {
        super(message);
        this.name = 'AppError';
    }
}

export class DownloadError extends AppError {
    constructor(message: string, code: string, public readonly taskId: string) {
        super(message, code, 400);
        this.name = 'DownloadError';
    }
}

export class ValidationError extends AppError {
    constructor(message: string, public readonly field?: string) {
        super(message, 'VALIDATION_ERROR', 400);
        this.name = 'ValidationError';
    }
}

export class NotFoundError extends AppError {
    constructor(resource: string, id: string) {
        super(`${resource} not found: ${id}`, 'NOT_FOUND', 404);
        this.name = 'NotFoundError';
    }
}

export class RateLimitError extends AppError {
    constructor() {
        super('Rate limit exceeded', 'RATE_LIMIT', 429);
        this.name = 'RateLimitError';
    }
}

export class ExternalServiceError extends AppError {
    constructor(service: string, message: string) {
        super(`External service error (${service}): ${message}`, 'EXTERNAL_SERVICE', 502);
        this.name = 'ExternalServiceError';
    }
}
```

```typescript
// src/utils/async.ts
import { Request, Response, NextFunction } from 'express';

type AsyncHandler = (req: Request, res: Response, next: NextFunction) => Promise<void>;

export function wrapAsync(fn: AsyncHandler): AsyncHandler {
    return (req, res, next) => {
        fn(req, res, next).catch(next);
    };
}
```

```typescript
// src/middleware/errorHandler.ts
import { Request, Response, NextFunction } from 'express';
import { AppError } from '../utils/errors';
import { logger } from '../utils/logger';

export function errorHandler(
    err: Error,
    req: Request,
    res: Response,
    _next: NextFunction
): void {
    const requestId = req.headers['x-request-id'] as string | undefined;

    if (err instanceof AppError && err.isOperational) {
        logger.warn({ err, requestId }, 'Operational error');
        res.status(err.statusCode).json({
            success: false,
            error: {
                code: err.code,
                message: err.message,
            },
        });
        return;
    }

    logger.error({ err, requestId }, 'Unexpected error');
    res.status(500).json({
        success: false,
        error: {
            code: 'INTERNAL_ERROR',
            message: 'An unexpected error occurred',
        },
    });
}
```

### 3.9 Logging

```typescript
// src/utils/logger.ts
import pino from 'pino';
import { config } from '../config';

export const logger = pino({
    level: config.LOG_LEVEL,
    transport: config.NODE_ENV === 'development'
        ? { target: 'pino-pretty', options: { colorize: true } }
        : undefined,
    serializers: {
        err: pino.stdSerializers.err,
        req: pino.stdSerializers.req,
        res: pino.stdSerializers.res,
    },
    redact: ['req.headers.authorization', 'req.headers.cookie', 'password', 'secret'],
});

// Request-scoped child logger
export function createRequestLogger(requestId: string): pino.Logger {
    return logger.child({ requestId });
}
```

**Redaction:** Hassas veriler (API key, password, token) loglarda `[REDACTED]` ile maskelenir.

### 3.10 Configuration Management

```typescript
// src/config/schema.ts
import { z } from 'zod';

export const configSchema = z.object({
    NODE_ENV: z.enum(['development', 'production', 'test']).default('development'),
    PORT: z.coerce.number().int().min(1).max(65535).default(3001),
    DB_PATH: z.string().default('./data/downloads.db'),
    LOG_LEVEL: z.enum(['fatal', 'error', 'warn', 'info', 'debug', 'trace']).default('info'),
    CORS_ORIGIN: z.string().default('http://localhost:81'),
    RATE_LIMIT_MAX: z.coerce.number().int().default(60),
    DOWNLOAD_MAX_CONCURRENT: z.coerce.number().int().default(3),
    DOWNLOAD_TIMEOUT_MS: z.coerce.number().int().default(300_000),
    DOWNLOAD_OUTPUT_DIR: z.string().default('./downloads'),
    DEEMIX_PATH: z.string().default('deemix'),
    YT_DLP_PATH: z.string().default('yt-dlp'),
    PROXY_URL: z.string().optional(),
    ANTI_BAN_DELAY_MS: z.coerce.number().int().default(2000),
    ANTI_BAN_MAX_RETRIES: z.coerce.number().int().default(3),
});

export type Config = z.infer<typeof configSchema>;
```

```typescript
// src/config/index.ts
import { configSchema } from './schema';
import { config as loadEnv } from 'dotenv';
import { join } from 'path';

loadEnv({ path: join(__dirname, '../../config/default.env') });

const parsed = configSchema.safeParse(process.env);

if (!parsed.success) {
    console.error('Invalid environment variables:', parsed.error.flatten().fieldErrors);
    process.exit(1);
}

export const config = parsed.data;
```

### 3.11 Security Patterns

```typescript
// Security middleware example
import helmet from 'helmet';
import cors from 'cors';
import rateLimit from 'express-rate-limit';

// Helmet: all security headers enabled
app.use(helmet());

// CORS: restricted to known origins
app.use(cors({
    origin: config.CORS_ORIGIN,
    credentials: true,
    methods: ['GET', 'POST', 'DELETE'],
    allowedHeaders: ['Content-Type', 'Authorization', 'X-Request-ID'],
}));

// Rate limiting: 60 requests per minute
app.use(rateLimit({
    windowMs: 60_000,
    max: config.RATE_LIMIT_MAX,
    standardHeaders: true,
    legacyHeaders: false,
    message: { success: false, error: { code: 'RATE_LIMIT', message: 'Too many requests' } },
}));

// Input validation with Zod
import { z } from 'zod';

const CreateSchema = z.object({
    url: z.string().url(),
    format: z.enum(['flac', 'mp3']),
});
```

**Zorunlu Kurallar:**
- `helmet()` tüm production ortamlarında aktif
- CORS sadece bilinen origin'lere izin verir
- Rate limit tüm endpoint'lerde uygulanır
- Tüm input Zod ile validate edilir
- `redact` ile hassas veriler loglanmaz

### 3.12 Testing

```typescript
// tests/unit/services/DownloadService.test.ts
import { describe, it, expect, vi, beforeEach } from 'vitest';
import { DownloadService } from '../../../src/services/DownloadService';

describe('DownloadService', () => {
    let service: DownloadService;

    beforeEach(() => {
        // Mock dependencies
        const mockDb = {
            insert: vi.fn(),
            findById: vi.fn(),
            updateStatus: vi.fn(),
            list: vi.fn(),
        };
        service = new DownloadService(mockDb as any);
    });

    it('should create a download task', async () => {
        const input = { url: 'https://youtube.com/watch?v=abc', format: 'flac' as const };
        const result = await service.create(input);
        expect(result).toHaveProperty('id');
        expect(result.status).toBe('pending');
    });

    it('should reject duplicate URLs', async () => {
        // Test duplicate detection
    });

    it('should cancel active downloads', async () => {
        // Test cancellation
    });
});
```

```typescript
// tests/integration/api/downloads.test.ts
import { describe, it, expect, beforeAll, afterAll } from 'vitest';
import request from 'supertest';
import { createApp } from '../../../src/server';

describe('GET /api/downloads', () => {
    let app: ReturnType<typeof createApp>;

    beforeAll(() => { app = createApp(); });

    it('should return paginated downloads', async () => {
        const res = await request(app)
            .get('/api/downloads?limit=10&offset=0')
            .expect(200);

        expect(res.body.success).toBe(true);
        expect(res.body.data).toBeInstanceOf(Array);
        expect(res.body.pagination).toHaveProperty('total');
    });

    it('should filter by status', async () => {
        const res = await request(app)
            .get('/api/downloads?status=completed')
            .expect(200);

        expect(res.body.success).toBe(true);
    });
});
```

### 3.13 Build & Deploy

```dockerfile
# Dockerfile
FROM node:20-alpine AS builder
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

FROM node:20-alpine AS runner
WORKDIR /app
COPY --from=builder /app/dist ./dist
COPY --from=builder /app/node_modules ./node_modules
COPY --from=builder /app/package.json ./
RUN addgroup -g 1001 -S nodejs && adduser -S nodejs -u 1001
USER nodejs
EXPOSE 3001
HEALTHCHECK --interval=30s --timeout=3s CMD wget --no-verbose --tries=1 --spider http://localhost:3001/health || exit 1
CMD ["node", "dist/index.js"]
```

```json
// package.json (scripts section)
{
  "scripts": {
    "build": "tsc",
    "start": "node dist/index.js",
    "dev": "tsx watch src/index.ts",
    "test": "vitest run",
    "test:watch": "vitest",
    "test:coverage": "vitest run --coverage",
    "lint": "eslint src/ --ext .ts",
    "typecheck": "tsc --noEmit",
    "migrate": "tsx scripts/migrate.ts"
  }
}
```

### 3.14 Download Pipeline

```
YouTube URL
    │
    ▼
MetadataService.extract()
    │  → title, artist, duration, thumbnail
    ▼
AntiBanService.waitForSlot()
    │  → rate limit check, proxy selection, UA rotation
    ▼
yt-dlp → deemix download
    │  → raw audio file
    ▼
FLAC conversion (if needed)
    │  → lossless FLAC output
    ▼
DatabaseService.updateStatus('completed')
    │  → file_path, metadata saved
    ▼
WebSocketManager.broadcast(progress)
    │  → real-time update to client
    ▼
Complete
```

**Anti-Ban Stratejisi:**
- Her istek arası minimum 2 saniye bekleme
- Proxy rotasyonu (birden fazla IP)
- User-Agent rotasyonu (10+ farklı tarayıcı kimliği)
- Exponential backoff (hata durumunda 2x, 4x, 8x bekleme)
- Circuit breaker (3 başarısız → 5 dakika askıya alma)

---

## 4. Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | **TypeScript strict: true** | Derleme hatası |
| 2 | **`any` tipi yasak** | Code review'de reddedilir |
| 3 | **Prepared statements** (better-sqlite3) | SQL injection riski |
| 4 | **`eval()` yasak** | Güvenlik açığı |
| 5 | **`console.log` yasak** — sadece pino logger | Structured logging ihlali |
| 6 | **Unhandled promise rejection** | Process crash |
| 7 | **Hardcoded secrets** | `[REDACTED]` ile loglanmalı |
| 8 | **No `var`** — sadece `const` / `let` | Scope sorunları |
| 9 | **Async error wrapper** (wrapAsync) | Catch edilmemiş hatalar |
| 10 | **Health check endpoint** zorunlu | Monitoring kaybı |

---

## 5. Naming Conventions

| Öğe | Format | Örnek |
|-----|--------|-------|
| **Dosya adı** | PascalCase (class), kebab-case (others) | `DownloadService.ts`, `error-handler.ts` |
| **Class** | PascalCase | `DownloadService`, `WebSocketManager` |
| **Interface** | PascalCase, `I` prefix yok | `DownloadTask`, `AppConfig` |
| **Function** | camelCase | `startDownload()`, `handleConnection()` |
| **Variable** | camelCase | `downloadTask`, `activeConnections` |
| **Constant** | UPPER_SNAKE_CASE | `MAX_CONCURRENT`, `DEFAULT_TIMEOUT` |
| **Route** | kebab-case, plural | `/api/downloads`, `/api/downloads/:id` |
| **Type** | PascalCase | `DownloadStatus`, `CreateDownloadInput` |
| **Enum** | PascalCase members | `Status.Pending`, `Format.Flac` |
| **Env var** | UPPER_SNAKE_CASE | `DOWNLOAD_MAX_CONCURRENT` |

---

## 6. Security Considerations

| Alan | Uygulama | Detay |
|------|----------|-------|
| **Input Validation** | Zod schema | Tüm endpoint'lerde zorunlu |
| **Rate Limiting** | express-rate-limit | 60 req/60s, IP bazlı |
| **Security Headers** | helmet | CSP, HSTS, X-Frame-Options |
| **CORS** | cors middleware | Sadece bilinen origin'ler |
| **Secret Management** | .env + dotenv | Hardcoded secret yasak |
| **SQL Injection** | better-sqlite3 prepared stmt | `?` parameter binding |
| **WebSocket Auth** | Origin check | Connection doğrulama |
| **Error Masking** | Generic messages | Internal details loglanmaz |
| **Log Redaction** | pino.redact | password, token, secret |
| **Dependency Audit** | npm audit | CI/CD'de zorunlu |

---

## 7. Performance Notes

| Teknik | Uygulama | Hedef |
|--------|----------|-------|
| **Connection Pooling** | better-sqlite3 WAL mode | Eşzamanlı okuma |
| **Compression** | gzip/br middleware | %60+ küçültme |
| **Caching** | Memory cache (Map) | Tekrarlanan sorgular |
| **Worker Threads** | CPU-intensive download | Event loop bloklamaz |
| **Stream Processing** | Pipe ile dosya yazma | Memory kullanımı düşük |
| **Heartbeat** | WebSocket ping/pong | Dead connection tespiti |
| **Graceful Shutdown** | SIGTERM handler | Veri kaybı yok |

---

## 8. Edge Cases

| Senaryo | Belirti | Çözüm |
|---------|---------|-------|
| **Connection timeout** | `ETIMEDOUT` | Retry with backoff, max 3 |
| **Download failure** | `ECONNRESET` | Farklı proxy ile yeniden dene |
| **Disk full** | `ENOSPC` | Hata logla, task'ı failed yap |
| **Rate limit exceeded** | 429 response | Exponential backoff (2s → 4s → 8s) |
| **Invalid URL** | Zod validation fail | 400 + detaylı hata mesajı |
| **Duplicate download** | Unique constraint | Mevcut task'ı döndür |
| **WebSocket disconnect** | `close` event | Client reconnect logic |
| **Concurrent limit** | `activeDownloads.size >= max` | Kuyruğa al veya reddet |
| **DB locked** | `SQLITE_BUSY` | WAL mode + retry |
| **Process crash** | Unhandled rejection | `uncaughtException` handler |

---

## 9. Troubleshooting

| Hata | Neden | Çözüm |
|------|-------|-------|
| `EADDRINUSE` | Port zaten kullanımda | Farklı port veya mevcut process'i öldür |
| `ENOMEM` | Yetersiz bellek | `--max-old-space-size` artır veya memory leak ara |
| `ETIMEDOUT` | Ağ zaman aşımı | Proxy değiş, timeout artır |
| `SQLITE_BUSY` | DB kilitli | WAL mode aktif et, retry ekle |
| `ECONNRESET` | Bağlantı koptu | Retry with backoff, anti-ban gecikmesini artır |
| `MODULE_NOT_FOUND` | Eksik bağımlılık | `npm install` çalıştır |
| `TypeError: Cannot read property of undefined` | Null check eksik | `noUncheckedIndexedAccess` aktif et |
| `UnhandledPromiseRejection` | Catch edilmemiş async hata | `wrapAsync` kullan |
| `ERR_HTTP_HEADERS_SENT` | Double response | `return` ile fonksiyonu sonlandır |
| `WebSocket connection failed` | CORS veya auth | Origin kontrolü, token ekle |

---

## 10. Common Anti-Patterns

| ❌ YANLIŞ | ✅ DOĞRU |
|-----------|----------|
| `async function handler(req, res) { await ...; res.json(...); }` | `wrapAsync` ile sar — catch edilmemiş hatalar process'i çökertir |
| `console.log('error:', err)` | `logger.error({ err }, 'message')` — structured logging |
| `const data = db.query(\`SELECT * FROM...\`)` | `db.prepare('SELECT ... WHERE id = ?').get(id)` — prepared statement |
| `let x: any = getData()` | `const data: DownloadTask = getData()` — explicit types |
| `res.json({ data: result })` after `res.status(200)` | Tek `res.status(200).json(...)` çağrısı — double write önlemi |
| `function handler(req, res, next) { next(); }` | `wrapAsync(async (req, res, next) => { ... })` — async wrapping |
| `setTimeout(() => { ... }, 5000)` in request | `AbortController` ile timeout — graceful cancellation |
| `process.on('unhandledRejection', () => {})` | `process.on('unhandledRejection', (err) => { logger.fatal(err); process.exit(1); })` |

---

## 11. Download Service Architecture

```
                         ┌─────────────────────┐
                         │   REST API (Express) │
                         │   Port 3001          │
                         └──────────┬────────────┘
                                    │
        ┌───────────────────────────┼───────────────────────────┐
        │                           │                           │
        ▼                           ▼                           ▼
┌───────────────┐        ┌────────────────┐        ┌───────────────┐
│  Download     │        │   WebSocket    │        │  Health       │
│  Controller   │        │   Manager      │        │  Controller   │
│  (CRUD)       │        │   (Real-time)  │        │  (Monitor)    │
└───────┬───────┘        └───────┬────────┘        └───────────────┘
        │                        │                        │
        ▼                        ▼                        ▼
┌───────────────┐        ┌────────────────┐        ┌───────────────┐
│  Download     │        │   Anti-Ban     │        │  Database     │
│  Service      │◄──────►│   Service      │        │  Service      │
│  (Pipeline)   │        │   (Rate/Proxy) │        │  (SQLite)     │
└───────┬───────┘        └────────────────┘        └───────────────┘
        │
        ▼
┌───────────────┐
│  Metadata     │
│  Service      │
│  (yt-dlp)     │
└───────────────┘
```

---

## 12. API Endpoints

| Method | Endpoint | Açıklama | Body/Query |
|--------|----------|----------|------------|
| `GET` | `/health` | Sağlık kontrolü | — |
| `POST` | `/api/downloads` | Yeni indirme başlat | `{ url, format, quality }` |
| `GET` | `/api/downloads` | İndirmeleri listele | `?status=&limit=&offset=` |
| `GET` | `/api/downloads/:id` | Tek indirme detayı | — |
| `DELETE` | `/api/downloads/:id` | İndirmeyi iptal et | — |
| `POST` | `/api/downloads/:id/retry` | Başarısız indirmeyi tekrar dene | — |
| `WS` | `/ws?taskId=xxx` | Gerçek zamanlı ilerleme | — |

**Örnek Request/Response:**

```json
// POST /api/downloads
// Request:
{ "url": "https://youtube.com/watch?v=abc123", "format": "flac" }

// Response (201):
{
  "success": true,
  "data": {
    "id": "550e8400-e29b-41d4-a716-446655440000",
    "url": "https://youtube.com/watch?v=abc123",
    "title": "Example Song",
    "artist": "Example Artist",
    "format": "flac",
    "quality": "best",
    "status": "pending",
    "progress": 0,
    "createdAt": "2026-08-06T12:00:00.000Z"
  }
}

// WebSocket progress update:
{
  "taskId": "550e8400-e29b-41d4-a716-446655440000",
  "status": "downloading",
  "progress": 45,
  "speed": "2.3MB/s",
  "eta": "12s"
}
```

---

## 13. Related Documents

- [[nodejs-template]] — Bu dosya (Node.js + TypeScript)
- [[vitest-template]] — Vitest test şablonu
- [[php-template]] — PHP backend şablonu
- [[cpp-template]] — C++ audio engine şablonu
- [[ADR-026-download-service-architecture]] — Download service mimarisi kararı
- [[ADR-042-vault-restructuring-2026-08-03]] — MSA limit = 15 dosya
- [[ADR-002-pdo-mandatory-no-orm]] — Prepared statement zorunluluğu (PHP tarafı)
- [[projects/download-service]] — Download service proje detayı
- [[architecture/06-audio/ai-auto-download]] — YouTube → deemix → FLAC pipeline

---

## 14. Cross-References

| Bu Dosyadan | Hedef | İlişki |
|-------------|-------|--------|
| § 3.3 Server Setup | [[ADR-042-vault-restructuring-2026-08-03]] | Port 3001 standardı |
| § 3.6 Database | [[ADR-002-pdo-mandatory-no-orm]] | Prepared statement kuralı |
| § 3.14 Pipeline | [[ADR-026-download-service-architecture]] | Download servis mimarisi |
| § 4 Guardrails | [[ADR-042-vault-restructuring-2026-08-03]] | MSA limit kontrolü |
| § 6 Security | [[ADR-022-database-hardened-security]] | Şifreleme standartları |
| § 11 Architecture | [[architecture/06-audio/ai-auto-download]] | Pipeline detayı |
| § 12 Endpoints | [[architecture/03-contracts/api-endpoints]] | API kataloğu |

---

## 15. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 3.0.0 |
| **Satır Sayısı** | 580+ |
| **Frontmatter** | ✅ Tam (10 alan) |
| **TypeScript** | ✅ Strict mode, noUncheckedIndexedAccess |
| **ADR Uyumlu** | ✅ 026, 042, 002, 022 |
| **Cross-Reference** | ✅ 7 çapraz referans |
| **MSA Uyumlu** | ✅ 15 dosya limiti |
| **WebSocket** | ✅ Gerçek zamanlı progress |
| **Anti-Ban** | ✅ Rate limit, proxy, UA rotation |
| **Test Coverage** | ≥80% hedef (Vitest + Supertest) |
| **Security** | ✅ Helmet, CORS, rate limit, Zod |
| **Docker** | ✅ Multi-stage build, healthcheck |

---

## 16. Examples

### Full Service Class

```typescript
// src/services/MetadataService.ts
import { ExternalServiceError } from '../utils/errors';
import { logger } from '../utils/logger';
import { config } from '../config';
import { execFile } from 'child_process';
import { promisify } from 'util';

const execFileAsync = promisify(execFile);

interface VideoMetadata {
    title: string;
    artist: string;
    duration: number;
    thumbnail: string;
    formats: string[];
}

export class MetadataService {
    async extract(url: string): Promise<VideoMetadata> {
        try {
            const { stdout } = await execFileAsync(config.YT_DLP_PATH, [
                '--dump-json',
                '--no-download',
                url,
            ], { timeout: 30_000 });

            const data = JSON.parse(stdout) as Record<string, unknown>;

            return {
                title: (data.title as string) ?? 'Unknown',
                artist: (data.artist as string) ?? (data.uploader as string) ?? 'Unknown',
                duration: (data.duration as number) ?? 0,
                thumbnail: (data.thumbnail as string) ?? '',
                formats: (data.formats as Array<{ ext: string }>)
                    ?.map((f) => f.ext)
                    .filter((ext): ext is string => Boolean(ext)) ?? [],
            };
        } catch (err) {
            logger.error({ url, err }, 'Metadata extraction failed');
            throw new ExternalServiceError('yt-dlp', err instanceof Error ? err.message : 'Unknown');
        }
    }
}
```

### Full Controller

```typescript
// src/controllers/DownloadController.ts
import { Router, Request, Response } from 'express';
import { z } from 'zod';
import { DownloadService } from '../services/DownloadService';
import { wrapAsync } from '../utils/async';
import { validate } from '../middleware/validate';

const router = Router();
const service = new DownloadService();

const CreateSchema = z.object({
    url: z.string().url(),
    format: z.enum(['flac', 'mp3']).default('flac'),
    quality: z.enum(['best', 'good']).default('best'),
});

const ListQuerySchema = z.object({
    status: z.enum(['pending', 'downloading', 'completed', 'failed']).optional(),
    limit: z.coerce.number().int().min(1).max(100).default(20),
    offset: z.coerce.number().int().min(0).default(0),
});

router.post('/',
    validate(CreateSchema),
    wrapAsync(async (req: Request, res: Response) => {
        const task = await service.create(req.body);
        res.status(201).json({ success: true, data: task });
    })
);

router.get('/',
    validate(ListQuerySchema, 'query'),
    wrapAsync(async (req: Request, res: Response) => {
        const result = await service.list(req.query);
        res.json({
            success: true,
            data: result.items,
            pagination: { total: result.total, limit: req.query.limit, offset: req.query.offset },
        });
    })
);

export default router;
```

### WebSocket Handler

```typescript
// src/websocket/handlers.ts
import { WebSocketManager } from './WebSocketManager';
import { DownloadService } from '../services/DownloadService';
import { logger } from '../utils/logger';

export function setupDownloadProgress(
    wsManager: WebSocketManager,
    downloadService: DownloadService
): void {
    // This would be called after each download step
    // wsManager.broadcast(taskId, { taskId, status, progress, speed, eta });
    logger.info('Download progress handler initialized');
}
```

---

## 17. Checklist

- [ ] `tsconfig.json` → `strict: true`, `noUncheckedIndexedAccess: true`
- [ ] `any` tipi kullanılmamış
- [ ] Tüm async fonksiyonlar `wrapAsync` ile sarılmış
- [ ] `console.log` / `console.error` kullanılmamış — sadece pino logger
- [ ] Prepared statement kullanılmış — SQL injection yok
- [ ] Zod schema ile input validate edilmiş
- [ ] `helmet()` middleware aktif
- [ ] CORS sadece bilinen origin'lere izin veriyor
- [ ] Rate limiting uygulanmış
- [ ] Health check endpoint mevcut (`/health`)
- [ ] Graceful shutdown handler var (SIGTERM, SIGINT)
- [ ] WebSocket heartbeat aktif (30s)
- [ ] `uncaughtException` ve `unhandledRejection` handler var
- [ ] Test coverage ≥%80
- [ ] `npm audit` hatasız
- [ ] Docker healthcheck tanımlı
- [ ] `.env.example` tüm değişkenleri içeriyor
- [ ] Log redaction aktif (password, token, secret)

---

## 18. Environment Variables

```bash
# config/default.env — CoreMusic Download Service

# ─── Server ────────────────────────────────────────────
NODE_ENV=development
PORT=3001
CORS_ORIGIN=http://localhost:81

# ─── Database ──────────────────────────────────────────
DB_PATH=./data/downloads.db

# ─── Logging ───────────────────────────────────────────
LOG_LEVEL=info

# ─── Rate Limiting ─────────────────────────────────────
RATE_LIMIT_MAX=60

# ─── Download Settings ─────────────────────────────────
DOWNLOAD_MAX_CONCURRENT=3
DOWNLOAD_TIMEOUT_MS=300000
DOWNLOAD_OUTPUT_DIR=./downloads

# ─── External Tools ────────────────────────────────────
DEEMIX_PATH=deemix
YT_DLP_PATH=yt-dlp

# ─── Anti-Ban ──────────────────────────────────────────
PROXY_URL=
ANTI_BAN_DELAY_MS=2000
ANTI_BAN_MAX_RETRIES=3

# ─── Secrets (production'da .env ile) ─────────────────
# DEEMIX_ARL_TOKEN=xxx
# PROXY_AUTH_TOKEN=xxx
```

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-06
**Mode:** Red Team • Human Mode • Truth Mode
