---
title: "CSRF Koruması"
layer: K6
category: "Güvenlik"
date: 2026-09-20
---

# CSRF Koruması

## Genel Bakış

Cross-Site Request Forgery (CSRF) koruması, kullanıcıların oturum açıkken kötü niyetli web siteleri tarafından istek göndermesini önler. Double Submit Cookie ve Synchronizer Token pattern'lerini birlikte kullanarak derinlemesine koruma sağlar. SameSite cookie attributes ve origin validation ile çok katmanlı koruma uygulanır.

## Teknik Detaylar

### CSRF Saldırı Senaryosu

```
1. Kullanıcı coremusic.com'da oturum açar
2. Kullanıcı malicious-site.com'u ziyaret eder
3. Malicious site, coremusic.com'a gizli form gönderir
4. Tarayıcı, coremusic.com cookie'lerini otomatik ekler
5. coremusic.com isteği kullanıcı oturumuyla işler
```

### Koruma Stratejisi: Double Submit Cookie

```
İstemci tarafında:
1. CSRF token'ı cookie'ye yazılır
2. Form gönderiminde token hem cookie'den hem formdan okunur
3. Sunucu ikisini karşılaştırır
4. Eşleşmezse istek reddedilir
```

### Koruma Stratejisi: Synchronizer Token

```
1. Kullanıcı oturum açtığında benzersiz token üretilir
2. Token sunucuda oturumla birlikte saklanır
3. Her formda token gizli alan olarak yer alır
4. Form gönderiminde token doğrulanır
5. Token her kullanımda yenilenir
```

### SameSite Cookie Politikası

- **Strict**: CSRF koruması en güçlü seviye
- **Lax**: Navigation isteklerinde cookie gönderilir
- **None**: Cross-site isteklerde cookie gönderilir (HTTPS zorunlu)

COREMUSIC'te `Lax` varsayılan, hassas işlemler için `Strict` kullanılır.

## Konfigürasyon / Kod

```typescript
import crypto from 'crypto';

// CSRF Token Üretimi
function generateCsrfToken(): string {
  return crypto.randomBytes(32).toString('hex');
}

// CSRF Token Hash (Double Submit için)
function hashCsrfToken(token: string): string {
  return crypto
    .createHmac('sha256', process.env.CSRF_SECRET)
    .update(token)
    .digest('hex');
}

// CSRF Middleware
function csrfProtection(options: {
  cookieName?: string;
  headerName?: string;
  saltLength?: number;
} = {}) {
  const {
    cookieName = '_csrf',
    headerName = 'x-csrf-token',
    saltLength = 32,
  } = options;

  return async (req: Request, res: Response, next: NextFunction) => {
    // GET istekleri için token oluştur
    if (req.method === 'GET') {
      const token = generateCsrfToken();
      const hashedToken = hashCsrfToken(token);

      // Cookie'ye yaz
      res.cookie(cookieName, hashedToken, {
        httpOnly: true,
        secure: process.env.NODE_ENV === 'production',
        sameSite: 'lax',
        path: '/',
        maxAge: 3600000, // 1 saat
      });

      // Response header'a ekle (API clients için)
      res.setHeader('X-CSRF-Token', token);

      return next();
    }

    // Mutasyon isteklerini doğrula
    if (['POST', 'PUT', 'PATCH', 'DELETE'].includes(req.method)) {
      const cookieToken = req.cookies[cookieName];
      const headerToken = req.headers[headerName] as string;

      if (!cookieToken || !headerToken) {
        return res.status(403).json({
          error: 'CSRF token missing',
        });
      }

      // Token'ları karşılaştır
      if (cookieToken !== hashCsrfToken(headerToken)) {
        await logCsrfFailure({
          ip: req.ip,
          userAgent: req.headers['user-agent'],
          path: req.path,
          method: req.method,
        });

        return res.status(403).json({
          error: 'CSRF token mismatch',
        });
      }

      // Token'ı yenile (replay attack önleme)
      const newToken = generateCsrfToken();
      res.cookie(cookieName, hashCsrfToken(newToken), {
        httpOnly: true,
        secure: process.env.NODE_ENV === 'production',
        sameSite: 'lax',
        path: '/',
        maxAge: 3600000,
      });
      res.setHeader('X-CSRF-Token', newToken);
    }

    next();
  };
}

// Origin Validation Middleware
function originValidation(allowedOrigins: string[]) {
  return (req: Request, res: Response, next: NextFunction) => {
    const origin = req.headers.origin || req.headers.referer;

    if (!origin) {
      return res.status(403).json({
        error: 'Origin header required',
      });
    }

    const isAllowed = allowedOrigins.some(allowed =>
      origin.startsWith(allowed)
    );

    if (!isAllowed) {
      return res.status(403).json({
        error: 'Origin not allowed',
      });
    }

    next();
  };
}

// SameSite Cookie Ayarları
function secureCookieMiddleware(
  req: Request,
  res: Response,
  next: NextFunction
) {
  // Tüm cookie'lere güvenli ayarları uygula
  const originalCookie = res.cookie.bind(res);

  res.cookie = function(name: string, value: string, options: any = {}) {
    return originalCookie(name, value, {
      ...options,
      httpOnly: options.httpOnly !== false,
      secure: process.env.NODE_ENV === 'production',
      sameSite: options.sameSite || 'lax',
      path: options.path || '/',
    });
  };

  next();
}

// CSRF Token Cookie'sini Temizle
function clearCsrfCookie(res: Response): void {
  res.clearCookie('_csrf', {
    path: '/',
    httpOnly: true,
    secure: process.env.NODE_ENV === 'production',
    sameSite: 'lax',
  });
}

// API Client'lar için CSRF Token Endpoint
app.get('/api/csrf-token', (req, res) => {
  const token = generateCsrfToken();
  const hashedToken = hashCsrfToken(token);

  res.cookie('_csrf', hashedToken, {
    httpOnly: true,
    secure: true,
    sameSite: 'strict',
    path: '/',
    maxAge: 3600000,
  });

  res.json({ csrfToken: token });
});
```

## Güvenlik Kontrolleri

- [ ] Double Submit Cookie pattern aktif olmalı
- [ ] Synchronizer Token her form için benzersiz olmalı
- [ ] SameSite cookie attribute zorunlu olmalı
- [ ] Origin/Referer header doğrulaması yapılmalı
- [ ] Token her kullanımda yenilenmeli (replay prevention)
- [ ] CSRF token 1 saatten uzun yaşamamalı
- [ ] GET isteklerinde state-changing işlem yapılmamalı
- [ ] Cross-origin formlarda Additional Header gerekmeli
- [ ] Token brute-force koruması olmalı
- [ ] Same-origin policy browser tarafında da desteklenmeli

## Bağımlılıklar

- **session-management.md**: Oturum bazlı token üretimi
- **security-headers.md**: SameSite cookie ayarları
- **audit-logging.md**: CSRF başarısızlıkları loglanır

## Durum: Implementasyon

- [x] Koruma stratejisi tasarlandı
- [x] Double Submit Cookie pattern seçildi
- [ ] CSRF middleware implemente edilecek
- [ ] Origin validation kurulacak
- [ ] SameSite cookie ayarları yapılacak
- [ ] Token rotation mekanizması kurulacak
- [ ] Test senaryoları yazılacak
