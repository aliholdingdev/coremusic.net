# NODE.JS & TYPESCRIPT PATTERNS — TAM REHBER
# Prompt Maker v7.2.0 | 2026-06-11 | Kiro IDE Native
# Event loop, streams, worker threads, TypeScript strict, async patterns

---

## NODE.JS RUNTIME ANLAYIŞI

### Event Loop Aşamaları

```
Event Loop Döngüsü:
  1. timers          → setTimeout, setInterval callback'leri
  2. pending I/O     → Önceki döngüden kalan I/O callback'leri
  3. idle/prepare    → Internal kullanım
  4. poll            → Yeni I/O event'leri bekle, callback çalıştır
  5. check           → setImmediate callback'leri
  6. close callbacks → socket.on('close') gibi

Microtask Queue (her aşama arasında):
  - process.nextTick() → En yüksek öncelik
  - Promise.resolve()  → İkinci öncelik
  - queueMicrotask()   → Üçüncü öncelik

Kural: Microtask'lar bir sonraki event loop aşamasına geçmeden önce tükenir.
```

### Event Loop Starvation Önleme

```javascript
// ❌ YANLIŞ: Event loop'u bloke eden senkron işlem
function processLargeArray(arr) {
    for (let i = 0; i < arr.length; i++) {
        heavyComputation(arr[i]); // 10M iterasyon = event loop bloke!
    }
}

// ✅ DOĞRU: Chunk'lara böl, event loop'a nefes ver
async function processLargeArrayAsync(arr, chunkSize = 1000) {
    for (let i = 0; i < arr.length; i += chunkSize) {
        const chunk = arr.slice(i, i + chunkSize);
        chunk.forEach(item => heavyComputation(item));

        // Event loop'a nefes ver
        await new Promise(resolve => setImmediate(resolve));
    }
}

// ✅ DAHA İYİ: Worker thread kullan (CPU-intensive işler için)
const { Worker, isMainThread, parentPort, workerData } = require('worker_threads');

if (isMainThread) {
    async function processInWorker(data) {
        return new Promise((resolve, reject) => {
            const worker = new Worker(__filename, { workerData: data });
            worker.on('message', resolve);
            worker.on('error', reject);
            worker.on('exit', (code) => {
                if (code !== 0) reject(new Error(`Worker exited with code ${code}`));
            });
        });
    }
} else {
    // Worker thread
    const result = heavyComputation(workerData);
    parentPort.postMessage(result);
}
```

### Stream Backpressure

```javascript
// ✅ DOĞRU: Backpressure yönetimi
const { Readable, Writable, Transform } = require('stream');
const { pipeline } = require('stream/promises');

// Transform stream — backpressure otomatik yönetilir
class JsonTransform extends Transform {
    constructor(options = {}) {
        super({ ...options, objectMode: true });
    }

    _transform(chunk, encoding, callback) {
        try {
            const parsed = JSON.parse(chunk.toString());
            this.push(parsed);
            callback();
        } catch (error) {
            callback(new Error(`JSON parse error: ${error.message}`));
        }
    }
}

// pipeline: Backpressure + error handling otomatik
async function processStream(inputPath, outputPath) {
    const { createReadStream, createWriteStream } = require('fs');

    await pipeline(
        createReadStream(inputPath),
        new JsonTransform(),
        new Writable({
            objectMode: true,
            write(chunk, encoding, callback) {
                processData(chunk);
                callback();
            }
        })
    );
}

// ❌ YANLIŞ: pipe() ile backpressure yönetimi yok
// readStream.pipe(writeStream); // Backpressure yok, memory leak riski
```

---

## TYPESCRIPT STRICT MODE

### tsconfig.json — Production Ayarları

```json
{
  "compilerOptions": {
    "target": "ES2022",
    "module": "NodeNext",
    "moduleResolution": "NodeNext",
    "lib": ["ES2022"],
    "outDir": "./dist",
    "rootDir": "./src",

    "strict": true,
    "noImplicitAny": true,
    "strictNullChecks": true,
    "strictFunctionTypes": true,
    "strictBindCallApply": true,
    "strictPropertyInitialization": true,
    "noImplicitThis": true,
    "useUnknownInCatchVariables": true,

    "noUnusedLocals": true,
    "noUnusedParameters": true,
    "noImplicitReturns": true,
    "noFallthroughCasesInSwitch": true,
    "noUncheckedIndexedAccess": true,
    "exactOptionalPropertyTypes": true,

    "esModuleInterop": true,
    "skipLibCheck": true,
    "forceConsistentCasingInFileNames": true,
    "declaration": true,
    "declarationMap": true,
    "sourceMap": true
  },
  "include": ["src/**/*"],
  "exclude": ["node_modules", "dist", "**/*.test.ts"]
}
```

### TypeScript Pattern'ları

```typescript
// ✅ DOĞRU: Discriminated Union (type-safe state machine)
type TrackState =
    | { status: 'idle' }
    | { status: 'loading'; progress: number }
    | { status: 'playing'; trackId: number; position: number }
    | { status: 'error'; message: string };

function renderTrackState(state: TrackState): string {
    switch (state.status) {
        case 'idle':    return 'Hazır';
        case 'loading': return `Yükleniyor: %${state.progress}`;
        case 'playing': return `Çalıyor: ${state.trackId} @ ${state.position}s`;
        case 'error':   return `Hata: ${state.message}`;
        // TypeScript: exhaustive check — tüm case'ler ele alındı
    }
}

// ✅ DOĞRU: Branded Types (type-safe ID'ler)
type UserId  = number & { readonly __brand: 'UserId' };
type TrackId = number & { readonly __brand: 'TrackId' };

function createUserId(id: number): UserId {
    return id as UserId;
}

// Artık UserId ve TrackId karıştırılamaz
function getTrack(trackId: TrackId): Promise<Track> { /* ... */ }
// getTrack(userId); // TypeScript ERROR — doğru!

// ✅ DOĞRU: Result type (exception yerine)
type Result<T, E = Error> =
    | { ok: true;  value: T }
    | { ok: false; error: E };

async function fetchTrack(id: TrackId): Promise<Result<Track>> {
    try {
        const track = await db.tracks.findById(id);
        if (!track) {
            return { ok: false, error: new Error(`Track ${id} not found`) };
        }
        return { ok: true, value: track };
    } catch (error) {
        return { ok: false, error: error instanceof Error ? error : new Error(String(error)) };
    }
}

// Kullanım
const result = await fetchTrack(trackId);
if (result.ok) {
    console.log(result.value.title); // TypeScript: value kesinlikle Track
} else {
    console.error(result.error.message); // TypeScript: error kesinlikle Error
}

// ✅ DOĞRU: Zod ile runtime validation
import { z } from 'zod';

const CreateTrackSchema = z.object({
    title:    z.string().min(1).max(255),
    artistId: z.number().int().positive(),
    duration: z.number().int().min(1).max(3600),
    fileUrl:  z.string().url(),
});

type CreateTrackDTO = z.infer<typeof CreateTrackSchema>;

function validateCreateTrack(input: unknown): CreateTrackDTO {
    return CreateTrackSchema.parse(input); // Hata fırlatır
    // veya: CreateTrackSchema.safeParse(input) → Result<CreateTrackDTO>
}
```

---

## ASYNC PATTERNS

### Promise Composition

```typescript
// ✅ DOĞRU: Promise.all — paralel, hepsi başarılı olmalı
const [user, tracks, playlists] = await Promise.all([
    fetchUser(userId),
    fetchTracks(userId),
    fetchPlaylists(userId),
]);

// ✅ DOĞRU: Promise.allSettled — paralel, bazıları başarısız olabilir
const results = await Promise.allSettled([
    fetchUser(userId),
    fetchTracks(userId),
    fetchPlaylists(userId),
]);

results.forEach((result, index) => {
    if (result.status === 'fulfilled') {
        console.log(`[${index}] Başarılı:`, result.value);
    } else {
        console.error(`[${index}] Başarısız:`, result.reason);
    }
});

// ✅ DOĞRU: Promise.race — ilk tamamlanan
const result = await Promise.race([
    fetchFromPrimary(),
    fetchFromFallback(),
    timeout(5000), // 5 saniye timeout
]);

// ✅ DOĞRU: Timeout wrapper
function withTimeout<T>(promise: Promise<T>, ms: number): Promise<T> {
    const timeoutPromise = new Promise<never>((_, reject) =>
        setTimeout(() => reject(new Error(`Timeout after ${ms}ms`)), ms)
    );
    return Promise.race([promise, timeoutPromise]);
}

// ✅ DOĞRU: Retry with exponential backoff
async function withRetry<T>(
    fn: () => Promise<T>,
    maxRetries = 3,
    baseDelayMs = 1000
): Promise<T> {
    let lastError: Error;

    for (let attempt = 0; attempt <= maxRetries; attempt++) {
        try {
            return await fn();
        } catch (error) {
            lastError = error instanceof Error ? error : new Error(String(error));

            if (attempt === maxRetries) break;

            // Exponential backoff + jitter
            const delay = baseDelayMs * Math.pow(2, attempt)
                        + Math.random() * 500;
            await new Promise(resolve => setTimeout(resolve, delay));
        }
    }

    throw lastError!;
}
```

### Async Iterator Pattern

```typescript
// ✅ DOĞRU: Async generator — büyük veri setleri için
async function* fetchAllTracks(pageSize = 100): AsyncGenerator<Track[]> {
    let page = 1;
    let hasMore = true;

    while (hasMore) {
        const { data, pagination } = await api.getTracks({ page, pageSize });
        yield data;

        hasMore = pagination.hasNext;
        page++;
    }
}

// Kullanım
for await (const tracks of fetchAllTracks()) {
    await processBatch(tracks);
    // Memory: Sadece bir sayfa bellekte
}
```

---

## NODE.JS GÜVENLİK

```typescript
// ✅ DOĞRU: Helmet.js ile HTTP güvenlik header'ları
import helmet from 'helmet';
import express from 'express';

const app = express();

app.use(helmet({
    contentSecurityPolicy: {
        directives: {
            defaultSrc:  ["'self'"],
            scriptSrc:   ["'self'"],
            styleSrc:    ["'self'"],
            imgSrc:      ["'self'", 'data:'],
            connectSrc:  ["'self'"],
            fontSrc:     ["'self'"],
            objectSrc:   ["'none'"],
            frameSrc:    ["'none'"],
            upgradeInsecureRequests: [],
        },
    },
    hsts: {
        maxAge: 31536000,
        includeSubDomains: true,
        preload: true,
    },
}));

// ✅ DOĞRU: Rate limiting
import rateLimit from 'express-rate-limit';

const apiLimiter = rateLimit({
    windowMs: 60 * 1000,  // 1 dakika
    max: 60,              // 60 istek
    standardHeaders: true,
    legacyHeaders: false,
    message: { error: 'Too many requests' },
});

const loginLimiter = rateLimit({
    windowMs: 15 * 60 * 1000, // 15 dakika
    max: 5,
    skipSuccessfulRequests: true,
});

app.use('/api/', apiLimiter);
app.use('/auth/login', loginLimiter);

// ✅ DOĞRU: Input validation (express-validator)
import { body, validationResult } from 'express-validator';

const createTrackValidation = [
    body('title').trim().isLength({ min: 1, max: 255 }).escape(),
    body('artistId').isInt({ min: 1 }),
    body('duration').isInt({ min: 1, max: 3600 }),
    body('fileUrl').isURL({ protocols: ['https'] }),
];

app.post('/api/tracks', createTrackValidation, (req, res) => {
    const errors = validationResult(req);
    if (!errors.isEmpty()) {
        return res.status(422).json({ errors: errors.array() });
    }
    // ...
});
```

---

## NODE.JS CHECKLIST

```
Event Loop:
  [ ] CPU-intensive işler worker thread'de mi?
  [ ] Senkron I/O yok mu? (fs.readFileSync production'da yasak)
  [ ] Event loop starvation riski var mı?
  [ ] Backpressure yönetimi var mı?

Memory:
  [ ] Memory leak riski var mı?
  [ ] Event listener cleanup var mı?
  [ ] Stream'ler düzgün kapatılıyor mu?
  [ ] Circular reference var mı?

Security:
  [ ] Helmet.js aktif mi?
  [ ] Rate limiting var mı?
  [ ] Input validation var mı?
  [ ] SQL/NoSQL injection koruması var mı?
  [ ] Path traversal koruması var mı?

TypeScript:
  [ ] strict: true aktif mi?
  [ ] any kullanımı yok mu?
  [ ] Tüm async fonksiyonlar await'li mi?
  [ ] Error handling tam mı?
```

---

*Node.js & TypeScript Patterns v7.0.0 — 2026-05-29 | Kiro IDE Native*
*Event loop, streams, worker threads, TypeScript strict, async patterns*
*Kaynak: nodejs.org/docs, typescriptlang.org/docs, expressjs.com*
