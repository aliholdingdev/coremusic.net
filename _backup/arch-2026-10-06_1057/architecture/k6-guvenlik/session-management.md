---
title: "Oturum Yönetimi"
layer: K6
category: "Güvenlik"
date: 2026-09-20
---

# Oturum Yönetimi

## Genel Bakış

Oturum yönetimi, kullanıcıların kimlik doğrulamasından sonra güvenli oturumlar oluşturmasını ve sürdürmesini sağlar. Server-side sessions, secure cookie ayarları ve session fixation koruması ile çok katmanlı oturum güvenliği uygulanır.

## Teknik Detaylar

### Oturum Yaşam Döngüsü

```
1. Kullanıcı giriş yapar → Session ID üretilir
2. Session Redis'te saklanır (TTL: 24 saat)
3. Session ID HttpOnly cookie'ye yazılır
4. Her istekte session Redis'ten okunur
5. Inactivity timeout kontrolü (30 dk)
6. Absolute timeout kontrolü (24 saat)
7. Kullanıcı çıkış yapar → Session silinir
```

### Cookie Ayarları

| Ayar | Değer | Açıklama |
|------|-------|----------|
| httpOnly | true | JavaScript erişimi engellenir |
| secure | true | Sadece HTTPS ile gönderilir |
| sameSite | lax | Cross-site request koruması |
| path | / | Tüm site için geçerli |
| maxAge | 86400000 | 24 saat |
| domain | .coremusic.com | Alt domainler dahil |

### Session Fixing Koruması

1. Her girişde yeni session ID üretilir
2. Eski session ID geçersiz kılınır
3. Session ID 32 byte rastgele değer
4. Session ID'de waktu bilgisi olmaz

### Concurrent Session Control

- Kullanıcı başına maksimum 5 aktif oturum
- Yeni oturum açıldığında en eski oturum kapatılır
- Admin tüm oturumları sonlandırabilir

## Konfigürasyon / Kod

```typescript
import Redis from 'ioredis';
import { v4 as uuidv4 } from 'uuid';
import crypto from 'crypto';

const redis = new Redis(process.env.REDIS_URL);

// Session Konfigürasyonu
const SESSION_CONFIG = {
  cookieName: 'sid',
  maxAge: 24 * 60 * 60 * 1000, // 24 saat
  inactivityTimeout: 30 * 60 * 1000, // 30 dakika
  maxSessionsPerUser: 5,
  secureCookie: process.env.NODE_ENV === 'production',
};

// Session Veri Yapısı
interface SessionData {
  id: string;
  userId: string;
  roles: string[];
  ip: string;
  userAgent: string;
  createdAt: number;
  lastActivity: number;
  metadata: Record<string, any>;
}

// Session Oluşturma
async function createSession(
  userId: string,
  req: Request
): Promise<string> {
  // Concurrent session kontrolü
  await enforceConcurrentSessionLimit(userId);

  const sessionId = generateSecureSessionId();
  const sessionData: SessionData = {
    id: sessionId,
    userId,
    roles: await getUserRoles(userId),
    ip: req.ip,
    userAgent: req.headers['user-agent'] || '',
    createdAt: Date.now(),
    lastActivity: Date.now(),
    metadata: {},
  };

  // Redis'e kaydet
  await redis.setex(
    `session:${sessionId}`,
    SESSION_CONFIG.maxAge / 1000,
    JSON.stringify(sessionData)
  );

  // User mapping kaydet
  await redis.setex(
    `user_sessions:${userId}:${sessionId}`,
    SESSION_CONFIG.maxAge / 1000,
    sessionId
  );

  return sessionId;
}

// Session Okuma
async function getSession(sessionId: string): Promise<SessionData | null> {
  const data = await redis.get(`session:${sessionId}`);
  if (!data) return null;

  const session: SessionData = JSON.parse(data);

  // Inactivity timeout kontrolü
  const now = Date.now();
  if (now - session.lastActivity > SESSION_CONFIG.inactivityTimeout) {
    await destroySession(sessionId);
    return null;
  }

  // Son aktivite zamanını güncelle
  session.lastActivity = now;
  await redis.setex(
    `session:${sessionId}`,
    SESSION_CONFIG.maxAge / 1000,
    JSON.stringify(session)
  );

  return session;
}

// Session Yenileme (Fixing Koruması)
async function refreshSession(
  oldSessionId: string,
  req: Request
): Promise<string> {
  const oldSession = await getSession(oldSessionId);
  if (!oldSession) {
    throw new Error('Session not found');
  }

  // Eski session'ı sil
  await destroySession(oldSessionId);

  // Yeni session oluştur
  return createSession(oldSession.userId, req);
}

// Session Silme
async function destroySession(sessionId: string): Promise<void> {
  const data = await redis.get(`session:${sessionId}`);
  if (!data) return;

  const session: SessionData = JSON.parse(data);

  // User mapping'i sil
  await redis.del(`user_sessions:${session.userId}:${sessionId}`);

  // Session'ı sil
  await redis.del(`session:${sessionId}`);
}

// Tüm Oturumları Sonlandır
async function destroyAllUserSessions(userId: string): Promise<void> {
  const pattern = `user_sessions:${userId}:*`;
  const keys = await redis.keys(pattern);

  for (const key of keys) {
    const sessionId = await redis.get(key);
    if (sessionId) {
      await redis.del(`session:${sessionId}`);
    }
    await redis.del(key);
  }
}

// Concurrent Session Limit
async function enforceConcurrentSessionLimit(
  userId: string
): Promise<void> {
  const pattern = `user_sessions:${userId}:*`;
  const keys = await redis.keys(pattern);

  if (keys.length >= SESSION_CONFIG.maxSessionsPerUser) {
    // En eski session'ı bul ve sil
    let oldestKey = keys[0];
    let oldestTime = Infinity;

    for (const key of keys) {
      const sessionId = await redis.get(key);
      if (sessionId) {
        const data = await redis.get(`session:${sessionId}`);
        if (data) {
          const session: SessionData = JSON.parse(data);
          if (session.createdAt < oldestTime) {
            oldestTime = session.createdAt;
            oldestKey = key;
          }
        }
      }
    }

    // En eski session'ı sil
    const oldestSessionId = await redis.get(oldestKey);
    if (oldestSessionId) {
      await destroySession(oldestSessionId);
    }
  }
}

// Secure Session ID Üretimi
function generateSecureSessionId(): string {
  return crypto.randomBytes(32).toString('hex');
}

// Session Middleware
function sessionMiddleware(
  req: Request,
  res: Response,
  next: NextFunction
) {
  const sessionId = req.cookies[SESSION_CONFIG.cookieName];

  if (!sessionId) {
    // Yeni session başlat
    req.session = null;
    return next();
  }

  // Session'ı yükle
  getSession(sessionId).then(session => {
    if (session) {
      req.session = session;
      req.sessionId = sessionId;
    } else {
      // Geçersiz session - cookie'yi temizle
      res.clearCookie(SESSION_CONFIG.cookieName);
      req.session = null;
    }
    next();
  });
}

// Session Cookie Ayarları
function setSessionCookie(res: Response, sessionId: string): void {
  res.cookie(SESSION_CONFIG.cookieName, sessionId, {
    httpOnly: true,
    secure: SESSION_CONFIG.secureCookie,
    sameSite: 'lax',
    path: '/',
    maxAge: SESSION_CONFIG.maxAge,
    domain: '.coremusic.com',
  });
}

// Session Refresh Response Header
function refreshSessionCookie(
  res: Response,
  sessionId: string
): void {
  setSessionCookie(res, sessionId);

  // Session fingerprint header
  res.setHeader('X-Session-Refresh', 'true');
}

// Brute-Force Koruması (Session)
async function checkSessionBruteForce(
  ip: string
): Promise<boolean> {
  const attempts = await redis.get(`login_attempts:${ip}`);

  if (attempts && parseInt(attempts) >= 5) {
    return true; // Block
  }

  return false; // Allow
}

async function recordLoginAttempt(
  ip: string,
  success: boolean
): Promise<void> {
  if (success) {
    await redis.del(`login_attempts:${ip}`);
  } else {
    const key = `login_attempts:${ip}`;
    await redis.incr(key);
    await redis.expire(key, 900); // 15 dakika
  }
}

// Session Validation Middleware
function validateSession(req: Request, res: Response, next: NextFunction) {
  if (req.session) {
    // IP değişikliği kontrolü
    if (req.session.ip !== req.ip) {
      // Şüpheli aktivite
      logSuspiciousActivity({
        type: 'IP_CHANGE',
        sessionId: req.sessionId,
        oldIp: req.session.ip,
        newIp: req.ip,
      });

      // Session'ı sonlandır
      destroySession(req.sessionId);
      res.clearCookie(SESSION_CONFIG.cookieName);
      return res.status(401).json({ error: 'Session invalidated' });
    }

    // User-Agent değişikliği kontrolü
    if (req.session.userAgent !== req.headers['user-agent']) {
      logSuspiciousActivity({
        type: 'USER_AGENT_CHANGE',
        sessionId: req.sessionId,
        oldUA: req.session.userAgent,
        newUA: req.headers['user-agent'],
      });
    }
  }

  next();
}

// Aktif Oturumları Listele
async function listActiveSessions(
  userId: string
): Promise<SessionData[]> {
  const pattern = `user_sessions:${userId}:*`;
  const keys = await redis.keys(pattern);
  const sessions: SessionData[] = [];

  for (const key of keys) {
    const sessionId = await redis.get(key);
    if (sessionId) {
      const data = await redis.get(`session:${sessionId}`);
      if (data) {
        sessions.push(JSON.parse(data));
      }
    }
  }

  return sessions;
}
```

## Güvenlik Kontrolleri

- [ ] Session ID 32 byte rastgele değer olmalı
- [ ] HttpOnly ve secure cookie zorunlu olmalı
- [ ] SameSite attribute ayarlanmalı
- [ ] Session fixation koruması aktif olmalı
- [ ] Inactivity timeout 30 dk olmalı
- [ ] Absolute timeout 24 saati aşmamalı
- [ ] Concurrent session limit uygulanmalı
- [ ] IP değişikliğinde session invalidasyonu yapılmalı
- [ ] Brute-force koruması aktif olmalı
- [ ] Session'lar Redis'te encrypted saklanmalı

## Bağımlılıklar

- **redis**: Session storage
- **authentication-jwt.md**: JWT ile birlikte çalışır
- **audit-logging.md**: Session olayları loglanır
- **rate-limiting.md**: Brute-force koruması

## Durum: Implementasyon

- [x] Oturum yapısı tasarlandı
- [x] Cookie ayarları tanımlandı
- [ ] Session middleware implemente edilecek
- [ ] Redis entegrasyonu kurulacak
- [ ] Session fixation koruması yapılacak
- [ ] Concurrent session control aktifleştirilecek
- [ ] Monitoring dashboard oluşturulacak
- [ ] Unit test yazılacak
