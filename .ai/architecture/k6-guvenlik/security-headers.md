---
title: "HTTP Güvenlik Başlıkları"
layer: K6
category: "Güvenlik"
date: 2026-09-20
---

# HTTP Güvenlik Başlıkları

## Genel Bakış

HTTP güvenlik başlıkları, tarayıcı davranışını kontrol ederek saldırı vektörlerini daraltır. HSTS, X-Frame-Options, X-Content-Type-Options ve diğer başlıklar ile clickjacking, MIME sniffing, man-in-the-middle ve diğer web tabanlı saldırılara karşı savunma sağlar.

## Teknik Detaylar

### Güvenlik Başlıkları Matrisi

| Başlık | Değer | Amaç |
|--------|-------|------|
| Strict-Transport-Security | max-age=63072000; includeSubDomains; preload | Zorunlu HTTPS |
| X-Frame-Options | DENY | Clickjacking koruması |
| X-Content-Type-Options | nosniff | MIME sniffing koruması |
| X-XSS-Protection | 0 | Eski XSS filtresi (devre dışı) |
| Referrer-Policy | strict-origin-when-cross-origin | Referrer bilgi sızıntısı |
| Permissions-Policy | camera=(), microphone=(), geolocation=() | Özellik kısıtlamaları |
| Cross-Origin-Opener-Policy | same-origin | Cross-origin iletişim |
| Cross-Origin-Embedder-Policy | require-corp | Cross-origin kaynak yükleme |
| Cross-Origin-Resource-Policy | same-origin | Cross-origin kaynak erişimi |

### HSTS Preload

COREMUSIC HSTS preload listesine kayıtlıdır:
- `max-age=63072000` (2 yıl)
- `includeSubDomains` alt domainler dahil
- `preload` tarayıcı preload listesine ekleme

### X-Frame-Options Stratejisi

- **DENY**: Tüm frame embed'leri engelle
- **SAMEORIGIN**: Sadece aynı origin'den frame'e izin ver
- CSP `frame-ancestors` ile birlikte kullanılır

### Permissions-Policy

Tarayıcı özelliklerini devre dışı bırakır:
- Kamera: Engellenmiş
- Mikrofon: Engellenmiş
- Coğrafi konum: Engellenmiş
- Ödeme API: Engellenmiş (gerekirse açılır)
- Kanvas: Engellenmiş

## Konfigürasyon / Kod

```typescript
// Tüm Güvenlik Başlıklarını Ayarlama
function securityHeaders(
  req: Request,
  res: Response,
  next: NextFunction
) {
  // Strict Transport Security (HSTS)
  res.setHeader('Strict-Transport-Security',
    'max-age=63072000; includeSubDomains; preload'
  );

  // Clickjacking Koruması
  res.setHeader('X-Frame-Options', 'DENY');

  // MIME Sniffing Koruması
  res.setHeader('X-Content-Type-Options', 'nosniff');

  // XSS Filtresi (Devre dışı - artık etkisiz)
  res.setHeader('X-XSS-Protection', '0');

  // Referrer Politikası
  res.setHeader('Referrer-Policy', 'strict-origin-when-cross-origin');

  // Özellik Kısıtlamaları
  res.setHeader('Permissions-Policy',
    'camera=(), ' +
    'microphone=(), ' +
    'geolocation=(), ' +
    'payment=(), ' +
    'usb=(), ' +
    'magnetometer=(), ' +
    'gyroscope=(), ' +
    'accelerometer=(), ' +
    'ambient-light-sensor=(), ' +
    'autoplay=(), ' +
    'encrypted-media=(), ' +
    'fullscreen=(self), ' +
    'picture-in-picture=(self)'
  );

  // Cross-Origin Korumaları
  res.setHeader('Cross-Origin-Opener-Policy', 'same-origin');
  res.setHeader('Cross-Origin-Embedder-Policy', 'require-corp');
  res.setHeader('Cross-Origin-Resource-Policy', 'same-origin');

  // Server Bilgisi Gizleme
  res.removeHeader('X-Powered-By');
  res.setHeader('Server', 'COREMUSIC');

  next();
}

// API Endpoint'leri İçin Özel Başlıklar
function apiSecurityHeaders(
  req: Request,
  res: Response,
  next: NextFunction
) {
  // API'ler için cache kontrolü
  res.setHeader('Cache-Control',
    'no-store, no-cache, must-revalidate, proxy-revalidate'
  );
  res.setHeader('Pragma', 'no-cache');
  res.setHeader('Expires', '0');

  // CORS başlıkları
  res.setHeader('Access-Control-Allow-Origin',
    process.env.ALLOWED_ORIGIN || 'https://coremusic.com'
  );
  res.setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH');
  res.setHeader('Access-Control-Allow-Headers',
    'Content-Type, Authorization, X-Requested-With, X-CSRF-Token'
  );
  res.setHeader('Access-Control-Allow-Credentials', 'true');
  res.setHeader('Access-Control-Max-Age', '86400');

  next();
}

// Development Ortamı İçin Daha Esnek Başlıklar
function devSecurityHeaders(
  req: Request,
  res: Response,
  next: NextFunction
) {
  // HSTS devre dışı
  // Frame engeli kaldırıldı (debugging için)

  res.setHeader('X-Content-Type-Options', 'nosniff');
  res.setHeader('Referrer-Policy', 'no-referrer-when-downgrade');

  // CORS dev ortamında geniş
  res.setHeader('Access-Control-Allow-Origin', '*');

  next();
}

// Content-Security-Policy Report-Only
function cspReportOnly(
  req: Request,
  res: Response,
  next: NextFunction
) {
  res.setHeader('Content-Security-Policy-Report-Only',
    "default-src 'self'; " +
    "script-src 'self'; " +
    "style-src 'self' 'unsafe-inline'; " +
    "img-src 'self' data: https:; " +
    "report-uri /api/csp-report"
  );

  next();
}

// İçe Aktarma Bloklama (Import Mapping)
function blockEmbedding(
  req: Request,
  res: Response,
  next: NextFunction
) {
  // Sitenin iframe'e yerleştirilmesini engelle
  res.setHeader('X-Frame-Options', 'DENY');

  // Cross-origin kaynak yüklemesini engelle
  res.setHeader('Cross-Origin-Resource-Policy', 'cross-origin');

  next();
}

// Helmet.js Benzeri Kapsamlı Konfigürasyon
const helmetConfig = {
  contentSecurityPolicy: {
    directives: {
      defaultSrc: ["'self'"],
      scriptSrc: ["'self'"],
      styleSrc: ["'self'", "'unsafe-inline'"],
      imgSrc: ["'self'", 'data:', 'https:'],
      fontSrc: ["'self'", 'https://fonts.gstatic.com'],
      connectSrc: ["'self'", 'https://api.coremusic.com'],
      mediaSrc: ["'self'", 'blob:'],
      objectSrc: ["'none'"],
      frameSrc: ["'none'"],
      frameAncestors: ["'none'"],
      formAction: ["'self'"],
      baseUri: ["'self'"],
      manifestSrc: ["'self'"],
      upgradeInsecureRequests: [],
    },
  },
  crossOriginEmbedderPolicy: true,
  crossOriginOpenerPolicy: { policy: 'same-origin' },
  crossOriginResourcePolicy: { policy: 'same-origin' },
  dnsPrefetchControl: { allow: false },
  frameguard: { action: 'deny' },
  hidePoweredBy: true,
  hsts: {
    maxAge: 63072000,
    includeSubDomains: true,
    preload: true,
  },
  ieNoOpen: true,
  noSniff: true,
  referrerPolicy: { policy: 'strict-origin-when-cross-origin' },
  xssFilter: false, // Devre dışı - etkisiz
};

// Güvenlik Başlık Test Fonksiyonu
async function testSecurityHeaders(
  url: string
): Promise<Record<string, { present: boolean; value: string }>> {
  const response = await fetch(url);
  const headers = response.headers;

  const requiredHeaders = [
    'strict-transport-security',
    'x-frame-options',
    'x-content-type-options',
    'referrer-policy',
    'permissions-policy',
    'cross-origin-opener-policy',
    'cross-origin-embedder-policy',
    'cross-origin-resource-policy',
  ];

  const results: Record<string, { present: boolean; value: string }> = {};

  for (const header of requiredHeaders) {
    const value = headers.get(header);
    results[header] = {
      present: !!value,
      value: value || '',
    };
  }

  return results;
}
```

## Güvenlik Kontrolleri

- [ ] HSTS max-age 1 yıldan uzun olmalı (2 yıl önerilir)
- [ ] includeSubDomains aktif olmalı
- [ ] Preload kaydı tamamlanmalı
- [ ] X-Frame-Options DENY olmalı
- [ ] X-Content-Type-Options nosniff olmalı
- [ ] X-XSS-Protection devre dışı olmalı (0)
- [ ] Referrer-Policy strict-origin-when-cross-origin olmalı
- [ ] Permissions-Policy gereksiz özellikleri engellemeli
- [ ] Cross-Origin başlıkları aktif olmalı
- [ ] Server version bilgisi gizli olmalı

## Bağımlılıklar

- **csp-policy.md**: CSP başlığı ile birlikte çalışır
- **csrf-protection.md**: SameSite cookie ayarları
- **session-management.md**: Secure cookie ayarları

## Durum: Implementasyon

- [x] Tüm güvenlik başlıkları tanımlandı
- [x] Helmet.js konfigürasyonu hazırlandı
- [ ] Security headers middleware implemente edilecek
- [ ] HSTS preload başvurusu yapılacak
- [ ] Development/production ayrımı kurulacak
- [ ] Security header testleri yazılacak
- [ ] Monitoring alertleri oluşturulacak
