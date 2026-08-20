---
type: architecture
category: security-encryption
title: "CoreMusic — Encryption Layers"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Encryption Layers

**See also:** [[architecture/07-security/index]] · [[ADR-022-database-hardened-security]] · [[ADR-034-credential-vault-normalization]]

---

## 1. Amaç

Encryption Layers, CoreMusic platformunun tüm katmanlardaki şifreleme mekanizmalarını tanımlar. Veri depolama, iletişim ve kimlik doğrulama için çok katmanlı şifreleme uygulanır.

---

## 2. Şifreleme Katmanları

```
┌─────────────────────────────────┐
│    Application Layer            │
│    (AES-256-GCM veri şifre)    │
├─────────────────────────────────┤
│    Transport Layer              │
│    (TLS 1.3 iletişim)          │
├─────────────────────────────────┤
│    Storage Layer                │
│    (AES-256-GCM disk şifre)    │
├─────────────────────────────────┤
│    Hardware Layer               │
│    (TPM / HSM anahtar)         │
└─────────────────────────────────┘
```

---

## 3. AES-256-GCM Parametreleri

| Parametre | Değer | Kaynak |
|-----------|-------|--------|
| Algorithm | AES-256-GCM | [[ADR-022]] |
| Key Size | 256-bit (32 byte) | [[ADR-022]] |
| IV Size | 96-bit (12 byte) | [[ADR-022]] |
| Tag Size | 16 byte | [[ADR-022]] |
| Standard | NIST SP 800-38D | — |

---

## 4. Kullanım Alanları

### Veri Depolama (At Rest)

| Veri | Şifreleme | Konum |
|------|-----------|-------|
| Password Hash | Argon2id | coremusic_auth |
| API Keys | AES-256-GCM | Credential Vault |
| JWT Secrets | AES-256-GCM | .env |
| User Data | AES-256-GCM | coremusic_user |
| Media Metadata | Şifrelenmemiş | coremusic_musics |
| Firmware | AES-256-GCM | Flash |

### İletişim (In Transit)

| Bağlantı | Protokol | ADR |
|----------|----------|-----|
| Web API | TLS 1.3 | [[ADR-022]] |
| Servisler arası | mTLS | [[ADR-022]] |
| Device ↔ Cloud | TLS 1.3 | [[ADR-022]] |
| WebSocket | WSS (TLS) | [[ADR-012]] |
| MQTT | MQTTS (TLS) | — |

---

## 5. Password Hashing (Argon2id)

| Parametre | Değer | ADR |
|-----------|-------|-----|
| Algorithm | Argon2id | [[ADR-022]] |
| Memory | 64MB | [[ADR-022]] |
| Iterations | 4 | [[ADR-022]] |
| Parallelism | 2 | [[ADR-022]] |
| Salt | Random 16 byte | — |

```php
// Doğru
$hash = password_hash($password, PASSWORD_ARGON2ID, [
    'memory_cost' => 65536,  // 64MB
    'time_cost' => 4,
    'threads' => 2,
]);

// Doğrulama
if (password_verify($input, $storedHash)) {
    // Giriş başarılı
}
```

---

## 6. CSRF Token

| Parametre | Değer | ADR |
|-----------|-------|-----|
| Token Key | `csrf_token` | [[ADR-010]] |
| Yasak Key | `_csrf_token` | [[ADR-010]] |
| Doğrulama | `hash_equals()` (timing-safe) | [[ADR-010]] |
| Üretim | `random_bytes(32)` | — |

---

## 7. Credential Vault

| Parametre | Değer | ADR |
|-----------|-------|-----|
| Şifreleme | AES-256-GCM | [[ADR-034]] |
| Anahtar | Ana şifre (user-provided) | [[ADR-034]] |
| Saklama | Encrypted file | [[ADR-034]] |
| Erişim | Vault class only | [[ADR-034]] |

---

## 8. Key Management

| Anahtar | Tür | Ömür | Saklama |
|---------|-----|------|---------|
| AES Key | Symmetric | 1 yıl | HSM / Vault |
| RSA Key | Asymmetric | 2 yıl | HSM |
| JWT Secret | Symmetric | 1 yıl | .env (encrypted) |
| CSRF Token | Per-session | Oturum | Session |
| API Key | Per-service | 1 yıl | Credential Vault |

---

## 9. Yasak Kurallar

| ❌ Yasak | ✅ Doğru | ADR |
|----------|----------|-----|
| Düz metin secret | Encrypted storage | [[ADR-022]] |
| MD5/SHA1 password | Argon2id | [[ADR-022]] |
| ECB mode | GCM mode | [[ADR-022]] |
| Short key (<256-bit) | 256-bit key | [[ADR-022]] |
| Hardcoded secret | .env / Vault | [[ADR-022]] |
| `_csrf_token` | `csrf_token` | [[ADR-010]] |

---

## 10. ADR Referansları

| ADR | Konu |
|-----|------|
| [[ADR-010-csrf-protection-strategy]] | CSRF token |
| [[ADR-011-session-management]] | Session yönetimi |
| [[ADR-022-database-hardened-security]] | Şifreleme standartları |
| [[ADR-034-credential-vault-normalization]] | Credential vault |

---

## 11. Çapraz Referanslar

| Kaynak | Hedef | İlişki |
|--------|-------|--------|
| Encryption | [[architecture/07-security/index]] | Güvenlik katmanı |
| Encryption | [[electronic/firmware/index]] | Firmware şifreleme |
| Encryption | [[architecture/l0-infrastructure]] | Veri depolama |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
