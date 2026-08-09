---
title: "CoreMusic — OAuth Authorization"
type: architecture
category: security
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — OAuth Authorization

**Zorunlu Bağlantılar:** [[index]] · [[ADR-020-api-public-security]] · [[ADR-043-auth-subdomain-consolidation]]

---

## 1. Amaç

OAuth 2.0 tabanlı yetkilendirme sistemini tanımlar. RBAC, permission management ve API security.

---

## 2. OAuth Roles

| Role | Yetki | Kullanım |
|------|-------|----------|
| admin | Tam yetki | Yönetim paneli |
| editor | İçerik düzenleme | Müzik yönetimi |
| viewer | Sadece okuma | Dinleme |
| api | API erişimi | Dış servisler |
| guest | Sınırlı erişim | Misafir |

---

## 3. Authorization Flow

```
Client → Auth Server → Resource Server
  ↓          ↓              ↓
Request    Validate      Grant Access
```

---

## 4. RBAC Matrix

| Resource | admin | editor | viewer | api | guest |
|----------|-------|--------|--------|-----|-------|
| /admin/* | ✅ | ❌ | ❌ | ❌ | ❌ |
| /music/* | ✅ | ✅ | ✅ | ✅ | ✅ |
| /api/* | ✅ | ✅ | ❌ | ✅ | ❌ |
| /user/* | ✅ | ✅ | ✅ | ❌ | ❌ |
| /media/* | ✅ | ✅ | ✅ | ✅ | ❌ |

---

## 5. Permission Model

| Permission | Description |
|------------|-------------|
| music:read | Müzik okuma |
| music:write | Müzik yazma |
| music:delete | Müzik silme |
| playlist:read | Çalma listesi okuma |
| playlist:write | Çalma listesi yazma |
| user:read | Kullanıcı okuma |
| user:write | Kullanıcı yazma |
| admin:full | Tam yönetici yetkisi |

---

## 6. API Key Management

| Özellik | Değer |
|---------|-------|
| Format | UUID v4 |
| Storage | Hashed in DB |
| Rotation | 90 gün |
| Rate Limit | Per-key |

---

## 7. CORS Configuration

| Setting | Değer |
|---------|-------|
| Allowed Origins | Whitelist |
| Methods | GET, POST, PUT, DELETE |
| Headers | Authorization, Content-Type |
| Credentials | true |
| Max Age | 3600 |

---

## 8. Security Rules

| Kural | Açıklama |
|-------|----------|
| Least privilege | Minimum yetki prensibi |
| No hardcoded secrets | .env vault |
| Rate limiting | API abuse önleme |
| Audit trail | Tüm yetkilendirme logları |
| Token rotation | Periyodik token yenileme |

---

## 9. Cross References

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 2 Roles | [[ADR-020-api-public-security]] | API security |
| § 3 Flow | [[ADR-043-auth-subdomain-consolidation]] | Auth consolidation |
| § 6 API Key | [[ADR-034-credential-vault-normalization]] | Credential vault |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
