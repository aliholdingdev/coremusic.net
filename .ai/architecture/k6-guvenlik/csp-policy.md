---
title: "Content Security Policy"
layer: K6
category: "Güvenlik"
date: 2026-09-20
---

# Content Security Policy (CSP)

## Genel Bakış

Content Security Policy (CSP), CROSS-Site Scripting (XSS) ve veri enjeksiyonu saldırılarını önlemek için tarayıcıya hangi kaynakların yüklenebileceğini tanımlar. Nonce tabanlı politika ile dinamik script yüklemeleri güvenli hale getirilir. Report-Only modu ile politika test edildikten sonra enforcement moduna geçilir.

## Teknik Detaylar

### CSP Direktifleri

```
Content-Security-Policy:
  default-src 'self';
  script-src 'self' 'nonce-{random}' 'strict-dynamic';
  style-src 'self' 'unsafe-inline';
  img-src 'self' data: https:;
  font-src 'self' https://fonts.gstatic.com;
  connect-src 'self' https://api.coremusic.com;
  media-src 'self' blob:;
  object-src 'none';
  frame-src 'none';
  frame-ancestors 'none';
  form-action 'self';
  base-uri 'self';
  manifest-src 'self';
  worker-src 'self' blob:;
  prefetch-src 'self';
  upgrade-insecure-requests;
  block-all-mixed-content;
```

### Nonce Üretimi

Her istek için benzersiz rastgele değer üretilir ve sadece o istek için geçerlidir. Nonce, script ve style tag'lerine eklenerek güvenli kaynak load edilmesi sağlanır.

### CSP Raporlama

Politika ihlalleri raporlama endpoint'ine gönderilir. Bu veriler güvenlik analizi ve politika optimizasyonu için kullanılır.

### Strict-Dynamic

`strict-dynamic` directive'i, meşru script'lerin yüklediği tüm script'leri otomatik olarak信任 eder. Bu sayede third-party script bağımlılığı azalır.

## Konfigürasyon / Kod

```typescript
import crypto from 'crypto';
import { v4 as uuidv4 } from 'uuid';

// Nonce Üretimi
function generateNonce(): string {
  return crypto.randomBytes(16).toString('base64');
}

// CSP Header Oluşturma
function buildCspHeader(nonce: string, reportUri?: string): string {
  const directives = [
    "default-src 'self'",
    `script-src 'self' 'nonce-${nonce}' 'strict-dynamic'`,
    "style-src 'self' 'unsafe-inline'",
    "img-src 'self' data: https:",
    "font-src 'self' https://fonts.gstatic.com",
    "connect-src 'self' https://api.coremusic.com wss://ws.coremusic.com",
    "media-src 'self' blob:",
    "object-src 'none'",
    "frame-src 'none'",
    "frame-ancestors 'none'",
    "form-action 'self'",
    "base-uri 'self'",
    "manifest-src 'self'",
    "worker-src 'self' blob:",
    "upgrade-insecure-requests",
    "block-all-mixed-content",
  ];

  if (reportUri) {
    directives.push(`report-uri ${reportUri}`);
    directives.push('report-to csp-endpoint');
  }

  return directives.join('; ');
}

// CSP Middleware
function cspMiddleware(options: {
  reportOnly?: boolean;
  reportUri?: string;
} = {}) {
  return (req: Request, res: Response, next: NextFunction) => {
    const nonce = generateNonce();
    const cspHeader = buildCspHeader(nonce, options.reportUri);

    // Nonce'u request'e ekle (template rendering için)
    (req as any).cspNonce = nonce;

    // CSP header'ı ayarla
    const headerName = options.reportOnly
      ? 'Content-Security-Policy-Report-Only'
      : 'Content-Security-Policy';

    res.setHeader(headerName, cspHeader);

    // Reporting API v1 header (gelecek için hazır)
    if (options.reportUri) {
      res.setHeader('Reporting-Endpoints', 'csp-endpoint="/api/csp-report"');
    }

    next();
  };
}

// CSP Rapor Endpoint'i
app.post('/api/csp-report', express.json({ type: 'application/csp-report' }),
  async (req: Request, res: Response) => {
    const report = req.body;

    // Raporu logla
    await logCspViolation({
      documentUri: report['document-uri'],
      violatedDirective: report['violated-directive'],
      effectiveDirective: report['effective-directive'],
      blockedUri: report['blocked-uri'],
      sourceFile: report['source-file'],
      lineNumber: report['line-number'],
      statusCode: report['status-code'],
      timestamp: new Date(),
    });

    res.status(204).end();
  }
);

// Template Helper - Nonce Ekleme
function cspScriptTag(nonce: string, src: string): string {
  return `<script nonce="${nonce}" src="${src}"></script>`;
}

function cspStyleTag(nonce: string, href: string): string {
  return `<link rel="stylesheet" nonce="${nonce}" href="${href}">`;
}

// Trusted Types Policy (XSS koruması için)
function createTrustedTypesPolicy(): void {
  if (typeof window !== 'undefined' && window.trustedTypes) {
    window.trustedTypes.createPolicy('coremusic', {
      createHTML: (input: string) => input,
      createScriptURL: (input: string) => {
        const url = new URL(input, window.location.origin);
        if (url.origin !== window.location.origin) {
          throw new TypeError('URL not from same origin');
        }
        return url.toString();
      },
      createScript: (input: string) => input,
    });
  }
}

// Dynamic Script Yükleme
async function loadScriptWithNonce(
  url: string,
  nonce: string
): Promise<void> {
  return new Promise((resolve, reject) => {
    const script = document.createElement('script');
    script.nonce = nonce;
    script.src = url;
    script.onload = () => resolve();
    script.onerror = () => reject(new Error(`Failed to load: ${url}`));
    document.head.appendChild(script);
  });
}

// CSP Development Mode (daha esnek politika)
function buildDevCspHeader(nonce: string): string {
  return [
    "default-src 'self' 'unsafe-inline' 'unsafe-eval'",
    `script-src 'self' 'unsafe-inline' 'unsafe-eval' 'nonce-${nonce}'`,
    "style-src 'self' 'unsafe-inline'",
    "img-src 'self' data: https: http:",
    "connect-src 'self' http://localhost:* ws://localhost:*",
    "font-src 'self' data:",
    "media-src 'self' blob: http://localhost:*",
    "object-src 'none'",
    "frame-src 'none'",
  ].join('; ');
}
```

## Güvenlik Kontrolleri

- [ ] Production'da CSP enforcement modu aktif olmalı
- [ ] Development'ta report-only modu kullanılmalı
- [ ] Nonce her istek için benzersiz üretilmeli
- [ ] `unsafe-inline` ve `unsafe-eval` mümkünse kaldırılmalı
- [ ] `object-src 'none'` zorunlu olmalı
- [ ] `frame-ancestors 'none'` iframe embed'i engellemeli
- [ ] CSP ihlalleri raporlanmalı ve analiz edilmeli
- [ ] Report-Only modu en az 2 hafta çalışmalı
- [ ] Third-party script'ler nonce ile yüklenmeli
- [ ] Mixed content blocking aktif olmalı

## Bağımlılıklar

- **security-headers.md**: Diğer HTTP güvenlik başlıkları
- **csrf-protection.md**: CSRF token ile birlikte çalışır
- **audit-logging.md**: CSP ihlalleri loglanır

## Durum: Implementasyon

- [x] CSP politikası tasarlandı
- [x] Nonce üretme mekanizması planlandı
- [ ] CSP middleware implemente edilecek
- [ ] Report endpoint kurulacak
- [ ] Trusted Types policy oluşturulacak
- [ ] Development ve production politikaları ayrılabilecek
- [ ] CSP testleri yazılacak
- [ ] Reporting API entegrasyonu yapılacak
