---
title: "OAuth 2.0 Entegrasyonu"
layer: K6
category: "Güvenlik"
date: 2026-09-20
---

# OAuth 2.0 Entegrasyonu

## Genel Bakış

OAuth 2.0, COREMUSIC'e üçüncü taraf hesaplarla (Google, Apple, GitHub) güvenli giriş imkanı sağlar. Authorization Code Flow with PKCE, token exchange ve refresh token mekanizmaları ile modern standartlara uygun kimlik paylaşımı uygulanır.

## Teknik Detaylar

### OAuth 2.0 Flow (Authorization Code + PKCE)

```
┌──────────┐                              ┌──────────┐
│  Kullanıcı │                              │ COREMUSIC │
│  Tarayıcı  │                              │  Sunucu   │
└──────┬─────┘                              └─────┬─────┘
       │                                          │
1.     │──── /auth/google ───────────────────────▶│
       │     (code_verifier, code_challenge)      │
       │                                          │
2.     │◀─── Redirect to Google ─────────────────│
       │                                          │
3.     │──── User authenticates at Google ──────▶│
       │                                          │
4.     │◀─── Authorization Code ─────────────────│
       │                                          │
5.     │──── Exchange code + code_verifier ─────▶│
       │                                          │
6.     │◀─── Access Token + Refresh Token ───────│
       │                                          │
7.     │◀─── Session created ────────────────────│
```

### PKCE (Proof Key for Code Exchange)

- `code_verifier`: 43-128 karakter rastgele string
- `code_challenge`: BASE64URL(SHA256(code_verifier))
- Authorization request'te code_challenge gönderilir
- Token request'te code_verifier gönderilir

### Provider Konfigürasyonu

| Provider | Client ID | Scope | Endpoint |
|----------|-----------|-------|----------|
| Google | coremusic-google | email profile | accounts.google.com |
| Apple | coremusic-apple | email name | appleid.apple.com |
| GitHub | coremusic-github | user:email | github.com/login/oauth |

### Token Exchange

Third-party access token'lar COREMUSIC token'larına dönüştürülür:
1. Third-party token doğrulanır
2. Kullanıcı bilgileri extract edilir
3. COREMUSIC user record oluşturulur/güncellenir
4. COREMUSIC token pair üretilir

## Konfigürasyon / Kod

```typescript
import crypto from 'crypto';

// PKCE Yardımcı Fonksiyonları
function generateCodeVerifier(): string {
  return crypto.randomBytes(32).toString('base64url');
}

function generateCodeChallenge(verifier: string): string {
  return crypto.createHash('sha256')
    .update(verifier)
    .digest('base64url');
}

// OAuth Provider Konfigürasyonları
interface OAuthProvider {
  name: string;
  clientId: string;
  clientSecret: string;
  authorizationUrl: string;
  tokenUrl: string;
  userInfoUrl: string;
  scopes: string[];
}

const OAUTH_PROVIDERS: Record<string, OAuthProvider> = {
  google: {
    name: 'Google',
    clientId: process.env.GOOGLE_CLIENT_ID,
    clientSecret: process.env.GOOGLE_CLIENT_SECRET,
    authorizationUrl: 'https://accounts.google.com/o/oauth2/v2/auth',
    tokenUrl: 'https://oauth2.googleapis.com/token',
    userInfoUrl: 'https://www.googleapis.com/oauth2/v2/userinfo',
    scopes: ['openid', 'email', 'profile'],
  },
  apple: {
    name: 'Apple',
    clientId: process.env.APPLE_CLIENT_ID,
    clientSecret: process.env.APPLE_CLIENT_SECRET,
    authorizationUrl: 'https://appleid.apple.com/auth/authorize',
    tokenUrl: 'https://appleid.apple.com/auth/token',
    userInfoUrl: '', // Apple userInfo token ile alınır
    scopes: ['name', 'email'],
  },
  github: {
    name: 'GitHub',
    clientId: process.env.GITHUB_CLIENT_ID,
    clientSecret: process.env.GITHUB_CLIENT_SECRET,
    authorizationUrl: 'https://github.com/login/oauth/authorize',
    tokenUrl: 'https://github.com/login/oauth/access_token',
    userInfoUrl: 'https://api.github.com/user',
    scopes: ['user:email'],
  },
};

// Authorization URL Oluşturma
function getAuthorizationUrl(
  provider: string,
  redirectUri: string
): { url: string; state: string; codeVerifier: string } {
  const config = OAUTH_PROVIDERS[provider];
  if (!config) throw new Error(`Unknown provider: ${provider}`);

  const state = crypto.randomBytes(16).toString('hex');
  const codeVerifier = generateCodeVerifier();
  const codeChallenge = generateCodeChallenge(codeVerifier);

  const params = new URLSearchParams({
    client_id: config.clientId,
    redirect_uri: redirectUri,
    response_type: 'code',
    scope: config.scopes.join(' '),
    state,
    code_challenge: codeChallenge,
    code_challenge_method: 'S256',
  });

  return {
    url: `${config.authorizationUrl}?${params.toString()}`,
    state,
    codeVerifier,
  };
}

// Authorization Code Exchange
async function exchangeCode(
  provider: string,
  code: string,
  codeVerifier: string,
  redirectUri: string
): Promise<OAuthTokenResponse> {
  const config = OAUTH_PROVIDERS[provider];

  const response = await fetch(config.tokenUrl, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
      'Accept': 'application/json',
    },
    body: new URLSearchParams({
      client_id: config.clientId,
      client_secret: config.clientSecret,
      code,
      grant_type: 'authorization_code',
      redirect_uri: redirectUri,
      code_verifier: codeVerifier,
    }),
  });

  if (!response.ok) {
    throw new Error('Token exchange failed');
  }

  return response.json();
}

// Kullanıcı Bilgilerini Alma
async function getUserInfo(
  provider: string,
  accessToken: string
): Promise<OAuthUserInfo> {
  const config = OAUTH_PROVIDERS[provider];

  const response = await fetch(config.userInfoUrl, {
    headers: {
      Authorization: `Bearer ${accessToken}`,
      Accept: 'application/json',
    },
  });

  if (!response.ok) {
    throw new Error('Failed to fetch user info');
  }

  const data = await response.json();

  // Provider'a göre normalize et
  switch (provider) {
    case 'google':
      return {
        id: data.id,
        email: data.email,
        name: data.name,
        avatar: data.picture,
        provider: 'google',
      };
    case 'apple':
      return {
        id: data.sub,
        email: data.email,
        name: data.name,
        avatar: '',
        provider: 'apple',
      };
    case 'github':
      return {
        id: data.id.toString(),
        email: data.email,
        name: data.name || data.login,
        avatar: data.avatar_url,
        provider: 'github',
      };
    default:
      throw new Error(`Unknown provider: ${provider}`);
  }
}

// OAuth Kullanıcı Oluşturma/Güncelleme
async function findOrCreateOAuthUser(
  userInfo: OAuthUserInfo
): Promise<{ user: User; isNew: boolean }> {
  // Mevcut OAuth bağlantısını kontrol et
  const existingLink = await db.oAuthLinks.findFirst({
    where: {
      provider: userInfo.provider,
      providerUserId: userInfo.id,
    },
    include: { user: true },
  });

  if (existingLink) {
    // Mevcut kullanıcıyı güncelle
    await db.oAuthLinks.update({
      where: { id: existingLink.id },
      data: {
        lastLoginAt: new Date(),
        accessToken: undefined, // Saklamıyoruz
      },
    });

    return { user: existingLink.user, isNew: false };
  }

  // E-posta ile mevcut kullanıcı var mı?
  const existingUser = await db.users.findFirst({
    where: { email: userInfo.email },
  });

  if (existingUser) {
    // Mevcut kullanıcıya OAuth bağlantısı ekle
    await db.oAuthLinks.create({
      data: {
        userId: existingUser.id,
        provider: userInfo.provider,
        providerUserId: userInfo.id,
        email: userInfo.email,
        lastLoginAt: new Date(),
      },
    });

    return { user: existingUser, isNew: false };
  }

  // Yeni kullanıcı oluştur
  const newUser = await db.users.create({
    data: {
      email: userInfo.email,
      name: userInfo.name,
      avatar: userInfo.avatar,
      emailVerified: true, // OAuth ile doğrulandı
      oAuthLinks: {
        create: {
          provider: userInfo.provider,
          providerUserId: userInfo.id,
          email: userInfo.email,
          lastLoginAt: new Date(),
        },
      },
    },
    include: { oAuthLinks: true },
  });

  return { user: newUser, isNew: true };
}

// Refresh Token ile Yenileme
async function refreshOAuthToken(
  provider: string,
  refreshToken: string
): Promise<OAuthTokenResponse> {
  const config = OAUTH_PROVIDERS[provider];

  const response = await fetch(config.tokenUrl, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: new URLSearchParams({
      client_id: config.clientId,
      client_secret: config.clientSecret,
      refresh_token: refreshToken,
      grant_type: 'refresh_token',
    }),
  });

  return response.json();
}

// Token Revocation
async function revokeOAuthToken(
  provider: string,
  token: string
): Promise<void> {
  const config = OAUTH_PROVIDERS[provider];

  // Google ve GitHub token revocation destekler
  if (provider === 'google') {
    await fetch(
      `https://oauth2.googleapis.com/revoke?token=${token}`,
      { method: 'POST' }
    );
  }

  // Apple token revocation desteklemez, sadece local silme
}

// OAuth Bağlantısını Kaldırma
async function unlinkOAuthProvider(
  userId: string,
  provider: string
): Promise<void> {
  // Kullanıcının en az bir auth yöntemi olmalı
  const user = await db.users.findUnique({
    where: { id: userId },
    include: { oAuthLinks: true },
  });

  if (!user) throw new Error('User not found');

  const hasPassword = !!user.passwordHash;
  const otherOAuth = user.oAuthLinks.filter(l => l.provider !== provider);

  if (!hasPassword && otherOAuth.length === 0) {
    throw new Error('Cannot unlink last authentication method');
  }

  await db.oAuthLinks.deleteMany({
    where: {
      userId,
      provider,
    },
  });
}

// State Token Yönetimi
class StateManager {
  private store: Map<string, { state: string; expiresAt: number }>;

  constructor() {
    this.store = new Map();
  }

  generate(): string {
    const state = crypto.randomBytes(16).toString('hex');
    this.store.set(state, {
      state,
      expiresAt: Date.now() + 600000, // 10 dakika
    });
    return state;
  }

  verify(state: string): boolean {
    const stored = this.store.get(state);
    if (!stored) return false;

    if (Date.now() > stored.expiresAt) {
      this.store.delete(state);
      return false;
    }

    this.store.delete(state); // Tek kullanımlık
    return true;
  }
}

// OAuth Middleware
function oauthMiddleware(
  provider: string,
  redirectUri: string
) {
  const stateManager = new StateManager();

  return async (req: Request, res: Response) => {
    const { url, state, codeVerifier } = getAuthorizationUrl(
      provider,
      redirectUri
    );

    // State ve codeVerifier'ı session'da sakla
    req.session = {
      ...req.session,
      oauthState: state,
      oauthCodeVerifier: codeVerifier,
      oauthProvider: provider,
    };

    res.redirect(url);
  };
}

// OAuth Callback Handler
async function handleOAuthCallback(
  req: Request,
  res: Response
): Promise<void> {
  const { code, state } = req.query as { code: string; state: string };

  // State doğrulama
  if (!stateManager.verify(state)) {
    return res.status(403).json({ error: 'Invalid state' });
  }

  const provider = req.session?.oauthProvider;
  const codeVerifier = req.session?.oauthCodeVerifier;

  if (!provider || !codeVerifier) {
    return res.status(400).json({ error: 'Missing OAuth session data' });
  }

  // Code exchange
  const tokenResponse = await exchangeCode(
    provider,
    code,
    codeVerifier,
    `${process.env.BASE_URL}/auth/callback/${provider}`
  );

  // Kullanıcı bilgilerini al
  const userInfo = await getUserInfo(provider, tokenResponse.access_token);

  // Kullanıcı oluştur/güncelle
  const { user, isNew } = await findOrCreateOAuthUser(userInfo);

  // COREMUSIC session oluştur
  const sessionId = await createSession(user.id, req);
  setSessionCookie(res, sessionId);

  // Audit log
  await auditLogger.log({
    level: 'INFO',
    category: 'AUTHENTICATION',
    action: isNew ? 'OAUTH_REGISTER' : 'OAUTH_LOGIN',
    actor: { userId: user.id, ip: req.ip, userAgent: req.headers['user-agent'] || '' },
    resource: { type: 'auth', id: provider },
    outcome: 'SUCCESS',
    metadata: { provider, isNew },
  });

  // Ana sayfaya yönlendir
  res.redirect('/');
}
```

## Güvenlik Kontrolleri

- [ ] PKCE zorunlu olmalı (state parametresi yeterli değil)
- [ ] State token tek kullanımlık ve short-lived olmalı
- [ ] Redirect URI tam olarak eşleşmeli
- [ ] Third-party token'lar saklanmamalı
- [ ] Email doğrulaması OAuth ile otomatik yapılmalı
- [ ] Son auth yöntemi kaldırılamamalı
- [ ] Token revocation desteklenmeli
- [ ] OAuth error'ları loglanmalı
- [ ] Rate limiting OAuth endpoint'lerine uygulanmalı
- [ ] HTTPS zorunlu olmalı

## Bağımlılıklar

- **session-management.md**: OAuth session yönetimi
- **authentication-jwt.md**: COREMUSIC token üretimi
- **audit-logging.md**: OAuth olayları loglanır
- **input-validation.md**: OAuth parametre doğrulama

## Durum: Implementasyon

- [x] OAuth flow tasarlandı
- [x] Provider konfigürasyonları tanımlandı
- [ ] OAuth middleware implemente edilecek
- [ ] Google entegrasyonu yapılacak
- [ ] Apple entegrasyonu yapılacak
- [ ] GitHub entegrasyonu yapılacak
- [ ] State manager kurulacak
- [ ] Callback handler yazılacak
