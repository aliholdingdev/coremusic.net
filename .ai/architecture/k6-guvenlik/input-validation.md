---
title: "Girdi Doğrulama"
layer: K6
category: "Güvenlik"
date: 2026-09-20
---

# Girdi Doğrulama ve Temizleme

## Genel Bakış

Girdi doğrulama, tüm kullanıcı girdilerinin işlenmeden önce kontrol edilmesini ve temizlenmesini sağlar. SQL injection, XSS, command injection ve diğer injection saldırılarını önler. Whitelist-based validation, parameterized queries ve output encoding ile çok katmanlı koruma uygulanır.

## Teknik Detaylar

### Saldırı Vektörleri

| Saldırı | Yöntem | Koruma |
|---------|--------|--------|
| SQL Injection | ' OR 1=1 -- | Parameterized queries |
| XSS (Stored) | <script>alert(1)</script> | Output encoding |
| XSS (Reflected) | URL parametresi | Input sanitization |
| Command Injection | ; rm -rf / | Command allowlist |
| Path Traversal | ../../etc/passwd | Path normalization |
| LDAP Injection | *)(uid=*))(|(uid=* | Input escaping |
| NoSQL Injection | {"$gt": ""} | Schema validation |
| SSRF | http://169.254.169.254 | URL allowlist |

### Validation Seviyeleri

```
┌─────────────────────────────────────────────────┐
│              VALIDATION SEVİYELERİ               │
├─────────────────────────────────────────────────┤
│ Level 1: Schema Validation (Zod/Yup)           │
│ Level 2: Business Logic Validation             │
│ Level 3: Sanitization (DOMPurify)              │
│ Level 4: Parameterized Queries                 │
│ Level 5: Output Encoding                       │
└─────────────────────────────────────────────────┘
```

### Whitelist Yaklaşımı

Sadece tanımlanan formatlar kabul edilir:
- E-posta: RFC 5322 formatı
- URL: Sadece http/https protokolü
- Dosya adı: Sadece alfanümerik ve underscore
- SQL: Parametrik query kullanımı zorunlu

## Konfigürasyon / Kod

```typescript
import { z } from 'zod';
import DOMPurify from 'isomorphic-dompurify';

// Zod Schema Tanımlamaları
const schemas = {
  // E-posta validasyonu
  email: z.string()
    .email('Geçersiz e-posta adresi')
    .max(255, 'E-posta çok uzun')
    .toLowerCase()
    .trim(),

  // Şifre validasyonu
  password: z.string()
    .min(12, 'Şifre en az 12 karakter olmalı')
    .max(128, 'Şifre çok uzun')
    .regex(
      /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/,
      'Şifre yeterince güçlü değil'
    ),

  // Kullanıcı adı
  username: z.string()
    .min(3, 'Kullanıcı adı en az 3 karakter')
    .max(30, 'Kullanıcı adı en fazla 30 karakter')
    .regex(/^[a-zA-Z0-9_-]+$/, 'Geçersiz karakter')
    .trim(),

  // Audio dosyası meta verisi
  audioMetadata: z.object({
    title: z.string().min(1).max(200).trim(),
    artist: z.string().min(1).max(200).trim(),
    album: z.string().max(200).trim().optional(),
    genre: z.string().max(50).trim().optional(),
    year: z.number().int().min(1900).max(2100).optional(),
    duration: z.number().positive().optional(),
  }),

  // Playlist
  playlist: z.object({
    name: z.string().min(1).max(100).trim(),
    description: z.string().max(500).trim().optional(),
    isPublic: z.boolean().default(false),
  }),

  // Search query
  searchQuery: z.string()
    .min(1, 'Arama terimi boş olamaz')
    .max(200, 'Arama terimi çok uzun')
    .trim()
    .transform(s => s.replace(/[<>"'%;()]/g, '')),

  // Pagination
  pagination: z.object({
    page: z.coerce.number().int().min(1).default(1),
    limit: z.coerce.number().int().min(1).max(100).default(20),
    sortBy: z.enum(['createdAt', 'updatedAt', 'name', 'title']).default('createdAt'),
    sortOrder: z.enum(['asc', 'desc']).default('desc'),
  }),

  // UUID
  uuid: z.string().uuid('Geçersiz ID formatı'),

  // URL
  url: z.string()
    .url('Geçersiz URL')
    .refine(
      url => ['http:', 'https:'].includes(new URL(url).protocol),
      'Sadece HTTP/HTTPS URLleri kabul edilir'
    ),

  // Dosya yükleme
  fileUpload: z.object({
    filename: z.string()
      .max(255)
      .regex(/^[a-zA-Z0-9._-]+$/, 'Geçersiz dosya adı'),
    mimetype: z.enum([
      'audio/mpeg',
      'audio/flac',
      'audio/wav',
      'audio/ogg',
      'audio/mp4',
      'audio/aac',
    ]),
    size: z.number()
      .max(100 * 1024 * 1024, 'Dosya boyutu 100MB\'ı aşamaz'),
  }),
};

// Sanitization Fonksiyonları
const sanitizer = {
  // HTML temizleme (XSS koruması)
  html(input: string): string {
    return DOMPurify.sanitize(input, {
      ALLOWED_TAGS: [], // Hiçbir HTML tag'ine izin verme
      ALLOWED_ATTR: [],
    });
  },

  // SQL injection koruması
  sql(input: string): string {
    // Parameterized queries kullanılmalı
    // Bu sadece fallback
    return input
      .replace(/'/g, "''")
      .replace(/;/g, '')
      .replace(/--/g, '')
      .replace(/\/\//g, '');
  },

  // Path traversal koruması
  path(input: string): string {
    return input
      .replace(/\.\./g, '')
      .replace(/\//g, '')
      .replace(/\\/g, '')
      .normalize('NFC');
  },

  // Command injection koruması
  command(input: string): string {
    return input
      .replace(/[;&|`$(){}[\]<>]/g, '')
      .trim();
  },

  // LDAP injection koruması
  ldap(input: string): string {
    return input
      .replace(/[*()\\]/g, '')
      .trim();
  },

  // XSS için output encoding
  encodeForHTML(input: string): string {
    return input
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#x27;');
  },

  encodeForAttribute(input: string): string {
    return input
      .replace(/&/g, '&amp;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#x27;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;');
  },

  encodeForJavaScript(input: string): string {
    return input
      .replace(/\\/g, '\\\\')
      .replace(/'/g, "\\'")
      .replace(/"/g, '\\"')
      .replace(/\n/g, '\\n')
      .replace(/\r/g, '\\r');
  },

  encodeForURL(input: string): string {
    return encodeURIComponent(input);
  },
};

// Validation Middleware
function validate(schema: z.ZodSchema) {
  return async (req: Request, res: Response, next: NextFunction) => {
    try {
      const result = await schema.safeParseAsync({
        ...req.body,
        ...req.query,
        ...req.params,
      });

      if (!result.success) {
        const errors = result.error.errors.map(e => ({
          field: e.path.join('.'),
          message: e.message,
        }));

        return res.status(400).json({
          error: 'Validation failed',
          details: errors,
        });
      }

      // Sanitize edilmiş veriyi req'e ekle
      req.validatedData = result.data;
      next();
    } catch (error) {
      next(error);
    }
  };
}

// Request Body Sanitization Middleware
function sanitizeBody(
  req: Request,
  res: Response,
  next: NextFunction
) {
  if (req.body && typeof req.body === 'object') {
    req.body = deepSanitize(req.body);
  }
  next();
}

function deepSanitize(obj: any): any {
  if (typeof obj === 'string') {
    return sanitizer.html(obj);
  }

  if (Array.isArray(obj)) {
    return obj.map(deepSanitize);
  }

  if (typeof obj === 'object' && obj !== null) {
    const sanitized: any = {};
    for (const [key, value] of Object.entries(obj)) {
      sanitized[key] = deepSanitize(value);
    }
    return sanitized;
  }

  return obj;
}

// SQL Injection Koruması (ORM Kullanımı)
// Prisma ile parametrik query kullanımı
async function safeQuery() {
  // DOĞRU - Parameterized query
  const users = await prisma.user.findMany({
    where: {
      email: userInput, // Parametre olarak传递
    },
  });

  // YANLIŞ - Raw query (SQL injection riski)
  // await prisma.$queryRaw`SELECT * FROM users WHERE email = ${userInput}`;

  // Gerekirse raw query verwenden (parameterized)
  const safeRaw = await prisma.$queryRaw`
    SELECT * FROM users
    WHERE email = ${userInput}
    AND status = 'active'
  `;
}

// SSRF Koruması
function validateUrl(url: string): boolean {
  try {
    const parsed = new URL(url);

    // Protokol kontrolü
    if (!['http:', 'https:'].includes(parsed.protocol)) {
      return false;
    }

    // Private IP aralıklarını engelle
    const hostname = parsed.hostname;
    const privateRanges = [
      /^127\./,
      /^10\./,
      /^172\.(1[6-9]|2\d|3[01])\./,
      /^192\.168\./,
      /^169\.254\./,
      /^0\./,
      /^localhost$/i,
      /^::1$/,
      /^fc00:/,
      /^fe80:/,
    ];

    if (privateRanges.some(range => range.test(hostname))) {
      return false;
    }

    // DNS rebinding koruması
    const resolved = require('dns').lookupSync(hostname);
    if (privateRanges.some(range => range.test(resolved))) {
      return false;
    }

    return true;
  } catch {
    return false;
  }
}

// Dosya Yükleme Validasyonu
function validateFileUpload(file: Express.Multer.File): {
  valid: boolean;
  error?: string;
} {
  // Dosya boyutu kontrolü
  if (file.size > 100 * 1024 * 1024) {
    return { valid: false, error: 'Dosya boyutu 100MB\'ı aşamaz' };
  }

  // MIME type kontrolü
  const allowedTypes = [
    'audio/mpeg',
    'audio/flac',
    'audio/wav',
    'audio/ogg',
    'audio/mp4',
    'audio/aac',
  ];

  if (!allowedTypes.includes(file.mimetype)) {
    return { valid: false, error: 'Desteklenmeyen dosya formatı' };
  }

  // Dosya uzantısı kontrolü
  const ext = file.originalname.split('.').pop()?.toLowerCase();
  const allowedExtensions = ['mp3', 'flac', 'wav', 'ogg', 'm4a', 'aac'];

  if (!ext || !allowedExtensions.includes(ext)) {
    return { valid: false, error: 'Geçersiz dosya uzantısı' };
  }

  // Magic number kontrolü (gerçek dosya tipi)
  const magicNumbers = {
    'audio/mpeg': [0xFF, 0xFB],
    'audio/flac': [0x66, 0x4C, 0x61, 0x43],
    'audio/wav': [0x52, 0x49, 0x46, 0x46],
  };

  return { valid: true };
}

// Content-Type Zorunlu Kılma
function requireContentType(
  allowedTypes: string[]
) {
  return (req: Request, res: Response, next: NextFunction) => {
    const contentType = req.headers['content-type'];

    if (!contentType) {
      return res.status(415).json({
        error: 'Content-Type header required',
      });
    }

    const isAllowed = allowedTypes.some(type =>
      contentType.includes(type)
    );

    if (!isAllowed) {
      return res.status(415).json({
        error: 'Unsupported Content-Type',
      });
    }

    next();
  };
}

// Hatalı Input Loglama
async function logInvalidInput(
  req: Request,
  errors: any[]
): Promise<void> {
  await auditLogger.log({
    level: 'WARN',
    category: 'SECURITY_EVENT',
    action: 'INVALID_INPUT',
    actor: {
      userId: (req.user as any)?.sub,
      ip: req.ip,
      userAgent: req.headers['user-agent'] || '',
    },
    resource: { type: 'input', id: req.path },
    outcome: 'FAILURE',
    metadata: {
      errors,
      body: req.body,
      query: req.query,
    },
  });
}
```

## Güvenlik Kontrolleri

- [ ] Tüm girdiler Zod schema ile doğrulanmalı
- [ ] HTML inputları DOMPurify ile temizlenmeli
- [ ] SQL parametrik query kullanımı zorunlu olmalı
- [ ] Path traversal koruması aktif olmalı
- [ ] SSRF koruması URL validasyonu ile sağlanmalı
- [ ] Dosya yükleme MIME type ve magic number kontrolü yapmalı
- [ ] Content-Type header zorunlu olmalı
- [ ] Hatalı girdiler loglanmalı
- [ ] Rate limiting invalid input'lara uygulanmalı
- [ ] Output encoding tüm response'larda uygulanmalı

## Bağımlılıklar

- **zod**: Schema validasyonu
- **isomorphic-dompurify**: HTML sanitization
- **csp-policy.md**: XSS koruması ile birlikte çalışır
- **audit-logging.md**: Invalid input logları
- **rate-limiting.md**: Brute-force koruması

## Durum: Implementasyon

- [x] Saldırı vektörleri analiz edildi
- [x] Validation seviyeleri tanımlandı
- [ ] Zod schema'ları implemente edilecek
- [ ] Sanitization fonksiyonları yazılacak
- [ ] Validation middleware kurulacak
- [ ] File upload validation yapılacak
- [ ] SSRF koruması aktifleştirilecek
- [ ] Integration test yazılacak
