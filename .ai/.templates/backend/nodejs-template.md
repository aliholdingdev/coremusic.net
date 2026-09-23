---
title: "CoreMusic — Node.js Backend Template"
type: template
category: template
date: 2026-09-06
updated: 2026-09-23
version: 2.0.0
status: active
authority: SSOT
---

# CoreMusic — Node.js Backend Template

**Teknoloji:** Node.js 20+, TypeScript 5+
**Katman:** K8 (Download Service)
**Port:** 3001
**Sorumlu Agent:** DevOps Engineer

**Zorunlu Bağlantılar:** [[.templates/index]] · [[../CLAUDE.md]] · [[../../AGENTS.md]]

---

## §1 Amaç

Bu şablon, CoreMusic download servisi (K8, port 3001) için Node.js 20+ / TypeScript 5+ backend dosya ve kod iskeletlerini tanımlar. **Guardrail #16:** yeni Node.js backend dosyası (server / route / controller / service / middleware / utils) bu şablondan üretilmek ZORUNLUDUR. Şablon; helmet / CORS / rate-limit güvenlik katmanını, structured logging'i, health check'i ve merkezi error handler standartlarını sabitler. **ADR-042 hibrit:** `.templates/*` tam yeniden yazıma açıktır (bu dosya v2.0.0 ile yeniden yazıldı).

| Alan | Değer |
|------|-------|
| Template Name | `nodejs-template.md` |
| Template Path | `.ai/.templates/backend/nodejs-template.md` |
| Hedef Dosya Tipi | Node.js / TypeScript backend dosyaları (index, server, config, routes, controllers, services, middleware, utils) |
| Guardrail | #16 (Template Mandatory) |
| Birincil Yazar | DevOps Engineer (servis sahibi) |
| İkincil Yazarlar | Backend Architect (route/controller review), Security Engineer (auth/rate-limit), QA Engineer (vitest) |
| Teknoloji | Node.js 20+, TypeScript 5+ (strict) |
| Katman / Port | K8 (Download Service) / 3001 |
| Kanıt — Paket | kök `package.json` (glob kanıtı) |
| Kanıt — Router | `assets.coremusic.net/js/router/*` (client-side router dosyaları) |
| Kanıt — Test | `[[../testing/vitest-template]]` (vitest config) |
| ⚠️ Doğrulanamayan | `download-service/src/` yolu diskte YOK (glob `download*/**` boş) → `⚠️ VERIFICATION REQUIRED` |
| ⚠️ Doğrulanamayan | `.github/workflows/*.yml` YOK → CI/CD iddiası yazılmaz |
| Korunan İskelet | H1 + §1 Dosya Yapısı → §4 Service (4 bölüm) + 4 kod bloğu |
| Değişken Formatı | `{{VARIABLE}}` (MODULE, TITLE, DATE, VERSION) |
| Dil | Türkçe (ç ğ ı İ ö ş ü doğru; mojibake YASAK); kod/dosya adı İngilizce |
| Versiyon | 2.0.0 (Vault Refactor Engine yeniden yazımı) |
| Authority | SSOT (bu dosya); üretilen dosya kendi authority değerini taşır |
| Governance | Red Team · Human Mode · Truth Mode |
| Kayıt Defteri | `.ai/.templates/index.md` (envanter SRP — bu şablonda tekrarlanmaz) |
| Son Kontrol | 2026-09-23 |

### §1.1 Neden Node.js Backend Şablonu Ayrıdır?

CoreMusic backend'i ikili bir yapıya sahiptir: PHP 8.4 (K8/K9 — `[[./php-template]]`) ve Node.js 20+ (K8 Download Service). İki teknoloji aynı Guardrail #16 ailesine bağlıdır ama farklı çalıştırma ortamı, farklı test aracı (PHPUnit ↔ vitest) ve farklı güvenlik middleware'i kullanır. Bu şablon Node.js tarafının **helmet / CORS / rate-limit üçlüsünü** tek yerde sabitler; PHP tarafındaki PSR-15 pipeline'ına karıştırılmaz.

| Boyut | PHP (php-template) | Node.js (bu şablon) |
|-------|--------------------|--------------------|
| Runtime | PHP 8.4 | Node.js 20+ |
| Dil | PHP (strict_types=1) | TypeScript 5+ (strict) |
| Güvenlik | PSR-15 middleware pipeline (10 katman) | helmet + CORS + express-rate-limit |
| Test | PHPUnit 11 (`[[../testing/phpunit-template]]`) | vitest (`[[../testing/vitest-template]]`) |
| ORM/DB | PDO (ORM yasak — ADR-002) | Servis katmanı (DB erişimi PHP tarafında) |
| Port | Sunucu config'i | 3001 (K8 Download Service) |
| Hata yönetimi | Merkezi error handler | `next(error)` → merkezi error handler |
| Logging | Structured logging | `utils/logger.ts` structured log |

### §1.2 Şablonun Sabitlediği Güvenlik Minimumu

| Katman | Minimum Değer | Kaynak |
|--------|---------------|--------|
| Headervardı | `helmet()` | §3 iskelet §2 |
| CORS origin | `config.ALLOWED_ORIGINS` (hardcoded yasak) | §3 iskelet §2 |
| CORS credentials | `credentials: true` | §3 iskelet §2 |
| Rate limit penceresi | `windowMs: 60 * 1000` (1 dakika) | §3 iskelet §2 |
| Rate limit limit | `max: 60` (60 req/60s — ADR-013 ile uyumlu) | §3 iskelet §2 |
| Rate limit başlıkları | `standardHeaders: true`, `legacyHeaders: false` | §3 iskelet §2 |
| Body limit | `10mb` | §3 iskelet §2 |
| Health endpoint | `/health` → `status` + `timestamp` | §3 iskelet §2 |
| Error handler | Merkezi, `logger.error` + 500 JSON | §3 iskelet §2 |

---

## §2 Kapsam

Şablonun kapsadığı ve kapsam dışında bıraktığı alanlar. Node.js backend, PHP backend'in eşdeğeridir; ikisi aynı Guardrail #16 ailesine bağlıdır ama katman/teknoloji bakımından ayrışırlar.

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `download-service/src/**` altındaki TypeScript dosyaları (index, server, config, routes, controllers, services, middleware, utils) | PHP backend dosyaları (→ `[[./php-template]]`) |
| `tests/*.test.ts` vitest test dosyaları iskeleti | Frontend JS (→ `[[../frontend/js-template]]`) |
| helmet / CORS / rate-limit / body-limit güvenlik katmanı | CI/CD pipeline tanımları (→ `[[../infrastructure/github-actions-template]]`) |
| structured logging + `/health` endpoint + merkezi error handler | Veritabanı şeması (→ `.ai/.sql/mysql/*.sql`, Data Engineer) |
| Port 3001 (K8 Download Service) standardı | API dokümanı (→ `[[../documentation/api-doc-template]]`) |
| `{{PLACEHOLDER}}` doldurma + Guardrail #16 doğrulaması | `.ai/log.md` append-only kayıt (üst görevin işi) |
| Wiki-link ile çapraz referans | Envanter listesi (→ `[[.templates/index]]`, SRP) |
| Türkçe doğruluk + mojibake denetimi | Doğrulanamayan yol/iddia uydurma (YAGNI) |

**Dosya tipi:** TypeScript · **Uzantı:** `.ts` · **Test:** `*.test.ts` (vitest) · **Guardrail:** #16

### §2.1 Dosya Tipi → Katman Eşlemesi

| Dosya Tipi | Katman | Sorumlu | Bu Şablondan mı? |
|------------|--------|---------|-------------------|
| `src/index.ts`, `src/server.ts` | Bootstrap | DevOps Engineer | ✅ |
| `src/routes/*.routes.ts` | Routing | Backend Architect (review) | ✅ |
| `src/controllers/*.controller.ts` | Controller | Backend Architect | ✅ |
| `src/services/*.service.ts` | Service | Backend Architect | ✅ |
| `src/middleware/*.ts` | Security | Security Engineer | ✅ |
| `src/utils/logger.ts` | Observability | DevOps Engineer | ✅ |
| `tests/*.test.ts` | Test | QA Engineer | ✅ (iskelet) — detay `[[../testing/vitest-template]]` |
| `*.php` | Backend (PHP) | Backend Architect | ❌ → `[[./php-template]]` |
| `*.js` (client) | Frontend | UI Designer | ❌ → `[[../frontend/js-template]]` |
| `*.yml` (CI/CD) | Pipeline | DevOps Engineer | ❌ → `[[../infrastructure/github-actions-template]]` |

### §2.2 Disk Kanıtları ve Doğrulanamayanlar (YAGNI)

| İddia | Durum | Kanıt / Aksiyon |
|-------|-------|-----------------|
| kök `package.json` var | ✅ DOĞRULANDI | glob → kök `package.json` |
| `assets.coremusic.net/js/router/*` var | ✅ DOĞRULANDI | glob → router dosyaları |
| `[[../testing/vitest-template]]` var | ✅ DOĞRULANDI | `.ai/.templates/testing/vitest-template.md` |
| `download-service/src/` yolu var | ❌ DOĞRULANAMADI | glob `download*/**` boş → `⚠️ VERIFICATION REQUIRED` |
| `.github/workflows/*.yml` var | ❌ DOĞRULANAMADI | glob boş → CI/CD iddiası yazılmaz |
| Port 3001 gerçekten kullanımda | ⚠️ BEKLEMEDE | Şablonda korunur; gerçek yol/kanıt gelene kadar işaretli |

### §2.3 Kapsam Karar Matrisi

Bir dosya üretileceğinde hangi şablonun kullanılacağı aşağıdaki matristen okunur; karar verilmez, okunur.

| Soru | Evet → | Hayır → |
|------|--------|---------|
| Dosya `.ts` mi? | Bu şablon | Aşağıdaki soruya geç |
| Dosya `.php` mi? | `[[./php-template]]` | Aşağıdaki soruya geç |
| Dosya `.test.ts` mi? | Bu şablon (iskelet) + `[[../testing/vitest-template]]` (detay) | Aşağıdaki soruya geç |
| Dosya `docs/api/*.md` mi? | `[[../documentation/api-doc-template]]` | Aşağıdaki soruya geç |
| Dosya denetim raporu mu? | `[[../documentation/security-audit-template]]` | Aşağıdaki soruya geç |
| Dosya `.ai/` vault sayfası mı? | `[[../documentation/WikiPage-Template]]` | Aşağıdaki soruya geç |
| Dosya mimari karar mı? | `[[../adr/adr-template]]` | MO'ya sor (Guardrail #16) |

---

## §3 Mimari

Şablonun tam iskeleti (placeholder'lı frontmatter + H1 + künye + dosya yapısı + 4 kod şablonu, eksiksiz). Dört-çit ` ```markdown ` bloğu iskeletin kendi üç-çit kod bloklarını korur.

````markdown
---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — Node.js Backend Template"
type: backend-template
category: template
date: {{DATE}}
updated: {{DATE}}
status: draft
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/brain.md"
---

# {{TITLE}}

**Teknoloji:** Node.js 20+, TypeScript 5+
**Katman:** K8 (Download Service)
**Port:** 3001
**Sorumlu Agent:** DevOps Engineer

---

## 1. Dosya Yapısı

```
download-service/
├── src/
│   ├── index.ts                 # Entry point
│   ├── server.ts                # HTTP server
│   ├── config/
│   │   └── index.ts             # Configuration
│   ├── routes/
│   │   └── {{MODULE}}.routes.ts # Route definitions
│   ├── controllers/
│   │   └── {{MODULE}}.controller.ts
│   ├── services/
│   │   └── {{MODULE}}.service.ts
│   ├── middleware/
│   │   ├── auth.ts              # JWT middleware
│   │   ├── rateLimit.ts         # Rate limiting
│   │   └── cors.ts              # CORS
│   └── utils/
│       └── logger.ts            # Structured logging
├── tests/
│   └── {{MODULE}}.test.ts
├── package.json
├── tsconfig.json
└── vitest.config.ts
```

---

## 2. Server Şablonu

```typescript
import express from 'express';
import cors from 'cors';
import helmet from 'helmet';
import rateLimit from 'express-rate-limit';
import { config } from './config';
import { logger } from './utils/logger';

const app = express();

// Security middleware
app.use(helmet());
app.use(cors({
    origin: config.ALLOWED_ORIGINS,
    credentials: true,
}));

// Rate limiting
const limiter = rateLimit({
    windowMs: 60 * 1000, // 1 minute
    max: 60,
    standardHeaders: true,
    legacyHeaders: false,
});
app.use(limiter);

// Body parsing
app.use(express.json({ limit: '10mb' }));

// Health check
app.get('/health', (req, res) => {
    res.json({ status: 'ok', timestamp: new Date().toISOString() });
});

// Routes
app.use('/api/v1', routes);

// Error handler
app.use((err: Error, req: express.Request, res: express.Response, next: express.NextFunction) => {
    logger.error('Unhandled error', { error: err.message, stack: err.stack });
    res.status(500).json({ error: 'Internal Server Error' });
});

app.listen(config.PORT, () => {
    logger.info(`Server running on port ${config.PORT}`);
});

export default app;
```

---

## 3. Controller Şablonu

```typescript
import { Request, Response, NextFunction } from 'express';
import { {{MODULE}}Service } from '../services/{{MODULE}}.service';

export class {{MODULE}}Controller {
    constructor(private service: {{MODULE}}Service) {}

    async findAll(req: Request, res: Response, next: NextFunction): Promise<void> {
        try {
            const result = await this.service.findAll();
            res.json({ status: 'success', data: result });
        } catch (error) {
            next(error);
        }
    }

    async findById(req: Request, res: Response, next: NextFunction): Promise<void> {
        try {
            const id = parseInt(req.params.id, 10);
            const result = await this.service.findById(id);
            if (!result) {
                res.status(404).json({ error: 'Not found' });
                return;
            }
            res.json({ status: 'success', data: result });
        } catch (error) {
            next(error);
        }
    }

    async create(req: Request, res: Response, next: NextFunction): Promise<void> {
        try {
            const result = await this.service.create(req.body);
            res.status(201).json({ status: 'success', data: result });
        } catch (error) {
            next(error);
        }
    }
}
```

---

## 4. Service Şablonu

```typescript
import { logger } from '../utils/logger';

export class {{MODULE}}Service {
    async findAll(): Promise<any[]> {
        // Business logic
        logger.info('Fetching all {{MODULE}}');
        return [];
    }

    async findById(id: number): Promise<any | null> {
        logger.info(`Fetching {{MODULE}} ${id}`);
        return null;
    }

    async create(data: any): Promise<any> {
        logger.info('Creating {{MODULE}}', { data });
        // Business logic + repository call
        return data;
    }
}
```

---

*Node.js Backend Template v1.0.0 — CoreMusic Development Standards*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: {{DATE}}*
*Mode: Red Team · Human Mode · Truth Mode*
````

---

## §4 Kurallar

Node.js backend geliştirme sırasında uygulanan zorunlu ve yasak kurallar. Güvenlik katmanı ihlal edilirse servis yayımlanamaz.

| # | Kural | Tür | İhlal Sonucu |
|---|-------|-----|--------------|
| 1 | Guardrail #16 — dosya bu şablondan üretilir, dış iskelet silinemez | Zorunlu | Şablonsuz dosya reddedilir |
| 2 | `helmet()` çağrısı zorunlu | Zorunlu | Güvenlik başlıkları eksik |
| 3 | CORS: `origin: config.ALLOWED_ORIGINS`, `credentials: true` | Zorunlu | Yetkisiz origin erişimi |
| 4 | Rate limit: `windowMs: 60_000`, `max: 60`, `standardHeaders: true`, `legacyHeaders: false` | Zorunlu | Rate limit bypass (ADR-013) |
| 5 | Body limit `10mb` (`express.json({ limit: '10mb' })`) | Zorunlu | DoS riski |
| 6 | TypeScript 5+ strict; her route `routes/`, her endpoint `controller` + `service` katmanına ayrılır | Zorunlu | Katman karışımı |
| 7 | Controller hata yakalar ve `next(error)` ile merkezi error handler'a iletir | Zorunlu | Yakalanmamış istisna |
| 8 | `utils/logger.ts` structured log; hata log'unda `message` + `stack` taşınır | Zorunlu | Teşhis edilemeyen hata |
| 9 | `/health` endpoint'i zorunlu (`status`, `timestamp` alanı) | Zorunlu | Monitoring boşluğu |
| 10 | `config.ALLOWED_ORIGINS` / `config.PORT` config üzerinden okunur; hardcoded secret yasak | Yasak | Veri sızıntısı |
| 11 | Yasak paketler: moment.js, lodash, axios, request, cheerio, puppeteer, winston, gulp | Yasak | Bağımlılık ihlali |
| 12 | Bilinmeyen API `⚠️ VERIFICATION REQUIRED` ile işaretlenir | Zorunlu | Hallucination |
| 13 | Port çakışması (3001 dolu) → DevOps Engineer'a bildir | Zorunlu | Servis çökmesi |
| 14 | `download-service/src/` yolu diskte doğrulanamadı → gerçek yol kurulana kadar `⚠️ VERIFICATION REQUIRED` | Zorunlu | YAGNI ihlali (uydurma yol) |
| 15 | `.github/workflows/*.yml` diskte yok → CI/CD iddiası yazılmaz | Yasak | Doğrulanamayan iddia |
| 16 | Frontmatter 7 zorunlu alan eksiksiz yazılır | Zorunlu | Frontmatter hatası |
| 17 | Wiki-link formatı `[[relative/path/to/file]]` | Zorunlu | Kırık çapraz referans |
| 18 | Türkçe karakterler (ç ğ ı İ ö ş ü) doğru; mojibake YASAK; kod adları İngilizce | Zorunlu | Mojibake → onarım |
| 19 | Envanter listesi bu şablonda tekrarlanmaz (SRP) → `[[.templates/index]]` | Yasak | İkincil kaynak çelişkisi |
| 20 | Secret / credential yazılmaz (REDACTED politikası) | Yasak | Güvenlik ihlali |

### §4.1 Güvenlik Katmanı Ayrıştırması

Her katman bağımsız denetlenebilir; bir katman devre dışı bırakılırsa diğerleri korumaz.

| # | Katman | Ne Yapar | Devre Dışı Kalırsa | Denetleyen |
|---|--------|----------|--------------------|------------|
| 1 | `helmet()` | Güvenlik response header'ları (HSTS, X-Content-Type-Options, vb.) | Header-based saldırılar | Security Engineer |
| 2 | CORS | Origin doğrulaması + credential kontrolü | Yetkisiz origin'den çerez sızıntısı | Security Engineer |
| 3 | Rate limiter | 60 req/60s penceresi | Brute-force / DDoS | Security Engineer |
| 4 | Body parser limit | 10mb üstü gövde reddi | Bellek tükenişi (DoS) | Backend Architect |
| 5 | `/health` | Yaşam sinyali | Monitoring kör noktası | DevOps Engineer |
| 6 | Error handler | Stack trace'i log'a, istemciye düz mesaj | Yanıtta stack trace sızıntısı | Security Engineer |
| 7 | `config` | Secret'ların `.env`'den okunması | Hardcoded secret | Security Engineer |

### §4.2 Katman Sırası (Değişmez)

```
helmet() → cors(ALLOWED_ORIGINS, credentials) → rateLimit(60/60s)
  → express.json({ limit: '10mb' }) → /health → routes('/api/v1')
    → error handler (merkezi)
```

Sıra değişirse: rate limit body parsing'den önce devreye girmez, CORS helmet'in header'larını ezer. İhlal → derhal revert + log ERROR (`[[../../AGENTS.md]]` §17).

### §4.3 Yasak Paketler

| Paket | Neden Yasak | Alternatif |
|-------|-------------|------------|
| moment.js | immutable olmayan Date, büyük bundle | `Intl.DateTimeFormat` / `date-fns` |
| lodash | bundle şişmesi, prototype pollution riski | Native `Array`/`Object` metotları |
| axios | Ekstra bağımlılık | Native `fetch` |
| request | Deprecated | Native `fetch` |
| cheerio | Gereksiz bağımlılık | İhtiyaç varsa domain agent'ına sor |
| puppeteer | Ağırlık + attack yüzeyi | CI'da gerekirse DevOps onayı |
| winton | Ağırlık | `utils/logger.ts` structured log |
| gulp | Legacy build | `tsc` + `vitest` |

---

## §5 Workflow

```
ŞABLONU SEÇ → KOPYALA → {{PLACEHOLDER}} DOLDUR → GUARDRAIL #16 DOĞRULA → COMMIT
```

| Adım | Eylem | Detay | Çıktı |
|------|-------|-------|-------|
| 1 | ŞABLONU SEÇ | `.ai/.templates/backend/nodejs-template.md` | Şablon kopyası |
| 2 | KOPYALA | Hedef katmana (routes/controllers/services/middleware/utils) kopyala | Yeni dosya iskeleti |
| 3 | DOLDUR | `{{MODULE}}` (dosya adları + class isimleri), `{{TITLE}}`, `{{DATE}}` | Dolu TS dosyaları |
| 4 | DOĞRULA | §6 kontrol listesi + §4 kuralları (helmet/CORS/rate-limit/health/error handler) | 8/8 gate |
| 5 | COMMIT | `vitest` + `tsc --noEmit` ile doğrula; registry (`[[.templates/index]]`) + log güncel | Vault senkronu |

**Adım 3 detayı — doldurma sırası:** (a) dosya yapısındaki `{{MODULE}}` adlarını routes/controller/service/test dosyaları için değiştir, (b) controller'a eksik metotları ekle (findAll / findById / create), (c) service gövdelerini iş mantığıyla doldur, (d) `config/index.ts` içine `PORT` ve `ALLOWED_ORIGINS` tanımla (hardcoded secret yasak), (e) `{{TITLE}}` ve `{{DATE}}` künyesini güncelle, (f) `tsconfig.json` strict modunu doğrula, (g) `vitest.config.ts` test glob'unu doğrula.

**Adım 5 doğrulama komutları:**

```bash
npx tsc --noEmit
npx vitest run
```

---

## §6 Doğrulama

Dosya commit edilmeden önce kalite kapıları sırayla kontrol edilir; tek bir madde bile ✅ değilse commit yapılmaz.

| # | Kontrol | Beklenen | Durum |
|---|---------|----------|-------|
| 1 | Frontmatter 7 zorunlu alan var mı? | title, type, category, date, updated, version, status, authority | ✅/❌ |
| 2 | H1 + §1-§4 iskeleti eksiksiz mi? | Dosya Yapısı, Server, Controller, Service | ✅/❌ |
| 3 | Tüm `{{PLACEHOLDER}}`'lar dolduruldu mu? | MODULE, TITLE, DATE | ✅/❌ |
| 4 | `helmet()` + CORS + rate-limit + body-limit yerinde mi? | §4 kural 2-5 | ✅/❌ |
| 5 | Rate limit değerleri 60/60s mi? | `windowMs: 60_000`, `max: 60` | ✅/❌ |
| 6 | `/health` endpoint'i var mı? | `status` + `timestamp` | ✅/❌ |
| 7 | Merkezi error handler + `next(error)` akışı mı? | Controller → handler zinciri | ✅/❌ |
| 8 | Structured logger + `message`/`stack` mı? | `utils/logger.ts` | ✅/❌ |
| 9 | Config üzerinden okuma var mı? | `config.PORT`, `config.ALLOWED_ORIGINS` | ✅/❌ |
| 10 | Hardcoded secret yok mu? | REDACTED politikası | ✅/❌ |
| 11 | Yasak paket kullanılmadı mı? | moment/lodash/axios/request/cheerio/puppeteer/winston/gulp | ✅/❌ |
| 12 | `download-service/src/` yolu işaretli mi? | `⚠️ VERIFICATION REQUIRED` (disk kanıtı yok) | ✅/❌ |
| 13 | Wiki-link'ler hedefe ulaşıyor mu? | `[[relative/path]]` formatı | ✅/❌ |
| 14 | Türkçe doğruluk + mojibake yok mu? | ç ğ ı İ ö ş ü doğru | ✅/❌ |
| 15 | `tsc --noEmit` + `vitest` geçti mi? | 0 hata | ✅/❌ |
| 16 | Envanter SRP ihlali yok mu? | Envanter `[[.templates/index]]`'de | ✅/❌ |
| 17 | § başlıkları kategori tutarlılığına uygun mu? | §1 Amaç → §7 Referanslar (DRY) | ✅/❌ |

### §6.1 Doğrulama Aşamaları

| Aşama | Kontrol Grubu | Kapsadığı Maddeler | Geçme Koşulu |
|-------|---------------|--------------------|--------------|
| A | Yapı | 1, 2 | Frontmatter + iskelet sağlam |
| B | İçerik | 3 | Placeholder kalmamış |
| C | Güvenlik | 4, 5, 6, 10, 11 | helmet/CORS/rate-limit/health/config/secret/paket |
| D | Çalıştırılabilirlik | 7, 8, 9, 15 | error flow, logger, health, `tsc`+`vitest` |
| E | Kanıt | 12, 13, 14 | Doğrulanamayan yol işaretli, link/dil doğru |
| F | Protokol | 16, 17 | SRP + DRY |

### §6.2 Red Sinyalleri (Otomatik DUR)

| Red Sinyali | Neden | Aksiyon |
|-------------|-------|---------|
| `helmet()`, CORS veya rate-limit yok | Güvenlik minimumu ihlali | DUR — §3 iskelet §2'yi uygula |
| `max` değeri 60 değil | ADR-013 sapması | DUR — `max: 60` yap |
| Hardcoded secret tespiti | REDACTED politikası | DUR — `config`'e taşı |
| Yasak paket kullanımı | §4.3 | DUR — alternatifle değiştir |
| `download-service/src/` kanıtsız yazıldı | YAGNI | `⚠️ VERIFICATION REQUIRED` ekle |
| `tsc --noEmit` hata veriyor | Derlenemez | DUR — hatayı düzelt |
| `vitest` kırmızı | Test başarısız | DUR — testi düzelt |
| Mojibake tespiti | Encoding | `vault-utf8-writer.mjs repair` |

### §6.3 Sürüm Geçmişi Kuralları

| Kural | Değer |
|-------|-------|
| İlk satır | `1.0.0 | {{DATE}} | Created` |
| Revizyon | Her onaylı değişiklikte yeni satır |
| Düzenleme | Mevcut satıra dokunulmaz (append-only) |
| Major bump | İskelet değişirse — onay gerektirir |

---

**REFACTOR REPORT:** FILE: nodejs-template.md · PURPOSE: Node.js (K8 download service) backend şablonu (Guardrail #16) · VALIDATION: 7 alan + §1-§7 + bilgi korunumu (15 placeholder, 4 kod bloğu, 20 kural, 17 doğrulama) · RELATED: [[.templates/index]] · [[../CLAUDE.md]]

---

## §7 Referanslar

| Kaynak | Wiki-Link | Rol |
|--------|-----------|-----|
| Template Registry | [[.templates/index]] | Bu şablonun kaydı (envanter SRP) |
| Vault ana sözleşmesi | [[../CLAUDE.md]] | 16 Hard Guardrail, Guardrail #16 kaynağı |
| Agent Registry (SSOT) | [[../../AGENTS.md]] | Routing §6, handover §9.3, edge case §17 |
| Eşdeğer backend şablonu | [[./php-template]] | PHP 8.4 / PDO alternatifi |
| Test şablonu | [[../testing/vitest-template]] | Vitest test iskeleti |
| CI/CD şablonu | [[../infrastructure/github-actions-template]] | Pipeline iskeleti (`.github/workflows` diskte yok) |
| API dokümanı | [[../documentation/api-doc-template]] | Endpoint contract |
| Client router kanıtı | `assets.coremusic.net/js/router/*` | Router dosyaları (glob kanıtı) |
| Paket kanıtı | kök `package.json` | Node.js bağımlılıkları (glob kanıtı) |
| Mimari kararlar | [[../../brain.md]] | ADR özetleri (ADR-013 rate limit) |
| Süreçler | [[../../WORKFLOW.md]] | Fazlar, session protokolü |

---

**Template Version:** 2.0.0
**Last Updated:** 2026-09-23
