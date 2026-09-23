---
title: "RBAC Yetkilendirme Sistemi"
layer: K6
category: "Güvenlik"
date: 2026-09-20
---

# RBAC Yetkilendirme Sistemi

## Genel Bakış

Role-Based Access Control (RBAC) sistemi, COREMUSIC platformunda kullanıcıların hangi kaynaklara erişebileceğini tanımlar. Üç temel rol (admin, editor, user) ve granüler izin matrisi ile minimum yetki prensibi uygulanır. Rol hiyerarşisi ve dinamik izin kontrolü ile esnek ama güvenli bir yetkilendirme modeli sağlar.

## Teknik Detaylar

### Rol Hiyerarşisi

```
┌─────────────────────────────────────────┐
│              SUPER ADMIN                │
│         (Tüm izinlere sahip)            │
└─────────────────┬───────────────────────┘
                  │
┌─────────────────▼───────────────────────┐
│               ADMIN                     │
│    (Sistem yönetimi, kullanıcı yönetimi)│
└─────────────────┬───────────────────────┘
                  │
┌─────────────────▼───────────────────────┐
│              EDITOR                     │
│   (İçerik yönetimi, ses dosyaları)      │
└─────────────────┬───────────────────────┘
                  │
┌─────────────────▼───────────────────────┐
│               USER                      │
│    (Temel erişim, kişisel veriler)      │
└─────────────────────────────────────────┘
```

### İzin Matrisi

| Kaynak | Admin | Editor | User | Guest |
|--------|-------|--------|------|-------|
| Kullanıcı Listesi | CRUD | R | R(ben) | - |
| Ses Dosyaları | CRUD | CRUD | R(p) | - |
| Playlist | CRUD | CRUD | CRUD(k) | R |
| AI Model | CRUD | R | - | - |
| Sistem Ayarları | CRUD | R | - | - |
| Audit Log | R | R(kendi) | - | - |
| API Key | CRUD | R | - | - |
| Rol Yönetimi | CRUD | - | - | - |

*R: Read, C: Create, U: Update, D: Delete, k: kendi, ben: sadece kendisi, p: paylaşılan*

### Permission Formatı

```
resource:action:scope
Örnekler:
  audio:read:own          # Kendi ses dosyalarını okuma
  audio:write:all         # Tüm ses dosyalarını yazma
  user:read:team          # Takım üyelerini okuma
  playlist:manage:own     # Kendi playlistlerini yönetme
  system:admin:all        # Sistem yönetimi
```

### Dinamik İzin Kontrolü

RBAC statik rollerin yanı sıra, koşullu izin kontrolü de destekler:
- Zaman tabanlı izinler (sadece iş saatlerinde)
- Bağlam tabanlı izinler (sadece belirli IP aralıklarından)
- Öznitelik tabanlı izinler (belirli dosya türleri için)

## Konfigürasyon / Kod

```typescript
// Rol ve İpin Tanımları
enum Permission {
  // Ses dosyaları
  AUDIO_READ = 'audio:read',
  AUDIO_WRITE = 'audio:write',
  AUDIO_DELETE = 'audio:delete',

  // Playlist
  PLAYLIST_READ = 'playlist:read',
  PLAYLIST_WRITE = 'playlist:write',
  PLAYLIST_MANAGE = 'playlist:manage',

  // Kullanıcı
  USER_READ = 'user:read',
  USER_WRITE = 'user:write',
  USER_DELETE = 'user:delete',

  // Sistem
  SYSTEM_ADMIN = 'system:admin',
  AUDIT_READ = 'audit:read',
  API_KEY_MANAGE = 'api_key:manage',
}

enum Role {
  GUEST = 'guest',
  USER = 'user',
  EDITOR = 'editor',
  ADMIN = 'admin',
  SUPER_ADMIN = 'super_admin',
}

// Rol-İzin Haritası
const ROLE_PERMISSIONS: Record<Role, Permission[]> = {
  [Role.GUEST]: [
    Permission.PLAYLIST_READ,
  ],
  [Role.USER]: [
    Permission.AUDIO_READ,
    Permission.PLAYLIST_READ,
    Permission.PLAYLIST_WRITE,
    Permission.USER_READ,
  ],
  [Role.EDITOR]: [
    Permission.AUDIO_READ,
    Permission.AUDIO_WRITE,
    Permission.AUDIO_DELETE,
    Permission.PLAYLIST_READ,
    Permission.PLAYLIST_WRITE,
    Permission.PLAYLIST_MANAGE,
    Permission.USER_READ,
  ],
  [Role.ADMIN]: [
    Permission.AUDIO_READ,
    Permission.AUDIO_WRITE,
    Permission.AUDIO_DELETE,
    Permission.PLAYLIST_READ,
    Permission.PLAYLIST_WRITE,
    Permission.PLAYLIST_MANAGE,
    Permission.USER_READ,
    Permission.USER_WRITE,
    Permission.USER_DELETE,
    Permission.SYSTEM_ADMIN,
    Permission.AUDIT_READ,
    Permission.API_KEY_MANAGE,
  ],
  [Role.SUPER_ADMIN]: Object.values(Permission),
};

// Yetkilendirme Middleware
function authorize(...requiredPermissions: Permission[]) {
  return async (req: Request, res: Response, next: NextFunction) => {
    const tokenPayload = req.user as TokenPayload;

    if (!tokenPayload) {
      return res.status(401).json({ error: 'Authentication required' });
    }

    const userRoles = tokenPayload.roles as Role[];
    const userPermissions = new Set<Permission>();

    // Tüm rollerin izinlerini topla
    for (const role of userRoles) {
      const rolePerms = ROLE_PERMISSIONS[role] || [];
      rolePerms.forEach(p => userPermissions.add(p));
    }

    // İzin kontrolü
    const hasPermission = requiredPermissions.every(p =>
      userPermissions.has(p)
    );

    if (!hasPermission) {
      await logAuthorizationFailure({
        userId: tokenPayload.sub,
        requiredPermissions,
        userPermissions: Array.from(userPermissions),
        resource: req.path,
        method: req.method,
      });

      return res.status(403).json({
        error: 'Insufficient permissions',
        required: requiredPermissions,
      });
    }

    next();
  };
}

// Sahiplik Kontrolü (Ownership)
function authorizeOwnership(
  resourceLoader: (id: string) => Promise<{ ownerId: string }>
) {
  return async (req: Request, res: Response, next: NextFunction) => {
    const user = req.user as TokenPayload;

    // Admin her şeye erişebilir
    if (user.roles.includes(Role.ADMIN)) {
      return next();
    }

    const resourceId = req.params.id;
    const resource = await resourceLoader(resourceId);

    if (!resource) {
      return res.status(404).json({ error: 'Resource not found' });
    }

    if (resource.ownerId !== user.sub) {
      return res.status(403).json({ error: 'Access denied' });
    }

    next();
  };
}

// Kullanıcı API Key ile Yetkilendirme
async function authorizeApiKey(
  apiKey: string,
  requiredPermissions: Permission[]
): Promise<boolean> {
  const key = await db.apiKeys.findUnique({
    where: { key: apiKey, isActive: true },
    include: { user: true },
  });

  if (!key) return false;

  // Key scope kontrolü
  const keyPermissions = new Set(key.scopedPermissions);

  return requiredPermissions.every(p => keyPermissions.has(p));
}
```

## Güvenlik Kontrolleri

- [ ] Rol değişiklikleri sadece super_admin yapabilmeli
- [ ] Her izin isteği audit log'a kaydedilmeli
- [ ] Admin rolü two-factor authentication zorunlu
- [ ] İzin escalation önlenmeli (kendi rolünden yüksek izin veremez)
- [ ] API key scope'u minimum principle ile tanımlanmalı
- [ ] Rol hiyerarşisinde döngü kontrolü yapılmalı
- [ ] Yetkilendirme cache'i TTL ile sınırlandırılmalı (30 sn)
- [ ] Brute-force koruması aktif olmalı
- [ ] Service-to-service iletişimde mTLS kullanılmalı
- [ ] Misafir kullanıcılar için rate limiting uygulanmalı

## Bağımlılıklar

- **authentication-jwt.md**: JWT payload'ından roller okunur
- **audit-logging.md**: Yetkilendirme başarısızlıkları loglanır
- **session-management.md**: Oturum bazlı yetki durumu
- **rate-limiting.md**: Brute-force koruması

## Durum: Implementasyon

- [x] Rol yapısı tasarlandı
- [x] İzin matrisi tanımlandı
- [ ] RBAC middleware implemente edilecek
- [ ] Permission seed verileri oluşturulacak
- [ ] Admin panel için rol yönetimi UI'ı yapılacak
- [ ] Ownership kontrolü implemente edilecek
- [ ] API key yetkilendirmesi kurulacak
- [ ] Integration test yazılacak
