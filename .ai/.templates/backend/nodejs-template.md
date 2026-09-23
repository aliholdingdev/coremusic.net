---
title: "CoreMusic — Node.js Backend Template"
type: template
category: template
version: 2.0.0
status: active
authority: "Template (Guardrail #16) — Registry: .ai/.templates/index.md"
updated: 2026-09-23
date: 2026-09-23
reference_doc: Freelancer Technical Documentation v1.0
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/brain.md"
---

# CoreMusic — Node.js Backend Template

**Teknoloji:** Node.js 20+, TypeScript 5+
**Katman:** K8 (Download Service)
**Port:** 3001
**Sorumlu Agent:** DevOps Engineer

**Zorunlu Bağlantılar / See also:** [[.templates/index]] · [[../CLAUDE.md]] · [[../../AGENTS.md]]

## 1. Amaç

Bu şablon, CoreMusic download servisi (K8, port 3001) için Node.js 20+ / TypeScript 5+ backend dosya ve kod iskeletlerini tanımlar. **Guardrail #16:** yeni Node.js backend dosyası (server/route/controller/service/middleware) bu şablondan üretilmek ZORUNLUDUR. Şablon; helmet/CORS/rate-limit güvenlik katmanı, structured logging, health check ve merkezi error handler standartlarını sabitler. **ADR-042 hibrit:** `.templates/*` tam yeniden yazıma açıktır (bu dosya v2.0.0 ile yeniden yazıldı).

## 2. Kapsam

- **Geçerli dosya tipleri:** `download-service/src/**` altındaki TypeScript dosyaları (index, server, config, routes, controllers, services, middleware, utils) + `tests/*.test.ts` (K8 Download Service).
- **Kullananlar:** DevOps Engineer (birincil, servis sahibi), Backend Architect (route/controller review), Security Engineer (auth/rate-limit), QA Engineer (vitest).
- **Kapsam dışı:** PHP backend (→ `[[./php-template]]`), frontend JS (→ `[[../frontend/js-template]]`), CI/CD pipeline (→ `[[../infrastructure/github-actions-template]]`).

## 3. Mimari

Şablonun tam iskeleti (placeholder'lı frontmatter + H1 + dosya yapısı + 4 kod şablonu, eksiksiz):

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
}
```

---

*Node.js Backend Template v1.0.0 — CoreMusic Development Standards*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: {{DATE}}*
*Mode: Red Team · Human Mode · Truth Mode*
````

## 4. Kurallar

1. **Guardrail #16:** Yeni Node.js backend dosyası bu şablondan üretilir; dış iskelet silinemez.
2. **Güvenlik zorunlu (iskelet §2 — ihlalde servis yayımlanamaz):** `helmet()` + CORS (`origin: config.ALLOWED_ORIGINS`, `credentials: true`) + rate limit (`windowMs: 60_000`, `max: 60`, `standardHeaders: true`) + body limit `10mb`.
3. **Yapı zorunlu:** TypeScript 5+ strict; her route `routes/`, her endpoint `controller` + `service` katmanına ayrılır; controller hata yakalar ve `next(error)` ile merkezi error handler'a iletir.
4. **Logging + health:** `utils/logger.ts` structured log; `/health` endpoint'i zorunlu; hata log'unda `message` + `stack` taşınır.
5. **Ortam/zarf:** `config.ALLOWED_ORIGINS` / `config.PORT` config üzerinden okunur; hardcoded secret yasak.
6. **Bilinmeyen API:** `⚠️ VERIFICATION REQUIRED` etiketi kullanılır.
7. **Port/servis:** Download Service = port 3001 (K8) — port çakışması → DevOps Engineer'a bildir.

## 5. Workflow

ŞABLONU SEÇ → KOPYALA → `{{PLACEHOLDER}}` DOLDUR → GUARDRAIL #16 DOĞRULA → COMMIT

1. **ŞABLONU SEÇ:** `backend/nodejs-template.md`.
2. **KOPYALA:** `download-service/src/` altındaki ilgili katmana (routes/controllers/services) kopyala.
3. **`{{PLACEHOLDER}}` DOLDUR:** `{{MODULE}}` (routes/controller/service/test dosya adları + class isimleri), `{{TITLE}}`, `{{DATE}}`.
4. **GUARDRAIL #16 DOĞRULA:** §6 kontrol listesi + §4 kuralları (helmet/CORS/rate-limit/health/error handler).
5. **COMMIT:** `vitest` + `tsc --noEmit` ile doğrula; registry + log güncel.

## 6. Doğrulama

- [ ] 7 alanlı frontmatter var (`title`, `type`, `category`, `version`, `status`, `authority`, `updated`)
- [ ] 8 bölüm var (H1 + §1-§7)
- [ ] tüm `{{PLACEHOLDER}}`'lar dolduruldu (`{{MODULE}}`, `{{TITLE}}`, `{{DATE}}`)
- [ ] dosya bu şablona uygun (Node.js/TypeScript backend dosyası)
- [ ] helmet + CORS + rate-limit (60/60s) + health check + structured logger + `next(error)` akışı yerinde

**REFACTOR REPORT:** FILE: nodejs-template.md · PURPOSE: Node.js (K8 download service) backend şablonu (Guardrail #16) · VALIDATION: 7 alan + §1-§7 + bilgi korunumu (15 placeholder, 5 kod bloğu korundu) · RELATED: [[.templates/index]] · [[../CLAUDE.md]]

## 7. Referanslar

- [[.templates/index]] — Template Registry (bu şablonun kaydı)
- [[../CLAUDE.md]] — vault ana sözleşme
- [[../../AGENTS.md]] — agent registry
- [[./php-template]] — eşdeğer backend şablonu (PHP)
- [[../infrastructure/github-actions-template]] — CI/CD pipeline
