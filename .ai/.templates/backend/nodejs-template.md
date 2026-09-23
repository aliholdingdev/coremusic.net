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
