---
title: "HashiCorp Vault Entegrasyonu"
layer: K6
category: "Güvenlik"
date: 2026-09-20
---

# HashiCorp Vault Entegrasyonu

## Genel Bakış

HashiCorp Vault, COREMUSIC'teki tüm gizli verilerin (API anahtarları, veritabanı şifreleri, sertifikalar) merkezi ve güvenli şekilde yönetilmesini sağlar. Dynamic secrets, secret rotation ve audit logging ile zero-trust secret yönetimi uygulanır.

## Teknik Detaylar

### Vault Yapısı

```
┌─────────────────────────────────────────────────────┐
│                HASHICORP VAULT                       │
├─────────────────────────────────────────────────────┤
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐ │
│  │ KV Engine   │  │ Database    │  │ Transit     │ │
│  │ (Static)    │  │ (Dynamic)   │  │ (Encrypt)   │ │
│  └─────────────┘  └─────────────┘  └─────────────┘ │
│                                                     │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐ │
│  │ PKI Engine  │  │ AppRole     │  │ Audit Log   │ │
│  │ (TLS Certs) │  │ (Auth)      │  │ (Log)       │ │
│  └─────────────┘  └─────────────┘  └─────────────┘ │
└─────────────────────────────────────────────────────┘
```

### Secret Tipleri

| Tip | Motor | Ömür | Kullanım |
|-----|-------|------|----------|
| Static Secrets | KV v2 | Kalıcı | API anahtarları |
| Dynamic DB | Database | 1 saat | Veritabanı şifreleri |
| Dynamic TLS | PKI | 24 saat | mTLS sertifikaları |
| Encryption | Transit | Kalıcı | Veri şifreleme |

### AppRole Authentication

COREMUSIC Vault ile iletişim kurmak için AppRole kullanır:
1. Role ID (sabit, environment variable'dan)
2. Secret ID (dinamik, short-lived)
3. Token TTL: 1 saat
4. Renewable: Auto-renew enabled

### Secret Rotation

- Static secrets: 30 günde bir
- Dynamic secrets: Her istekte yeni
- Database credentials: 24 saat
- API keys: 90 günde bir

## Konfigürasyon / Kod

```typescript
import vault from 'node-vault';

// Vault Client Konfigürasyonu
const vaultClient = vault({
  apiVersion: 'v1',
  endpoint: process.env.VAULT_ADDR || 'https://vault.coremusic.com:8200',
  token: process.env.VAULT_TOKEN,
});

// AppRole Authentication
async function authenticateWithAppRole(): Promise<string> {
  const roleId = process.env.VAULT_ROLE_ID;
  const secretId = await getSecretId();

  const result = await vaultClient.approleLogin({
    role_id: roleId,
    secret_id: secretId,
  });

  vaultClient.token = result.auth.client_token;

  // Token'ı yenile
  await scheduleTokenRenewal(result.auth.client_token, result.auth.lease_duration);

  return result.auth.client_token;
}

// Secret Okuma
async function getSecret(path: string): Promise<Record<string, any>> {
  try {
    const result = await vaultClient.read(`secret/data/${path}`);
    return result.data.data;
  } catch (error) {
    if (error.response?.statusCode === 404) {
      throw new Error(`Secret not found: ${path}`);
    }
    throw error;
  }
}

// Secret Yazma
async function setSecret(
  path: string,
  data: Record<string, any>
): Promise<void> {
  await vaultClient.write(`secret/data/${path}`, {
    data,
  });
}

// Dynamic Database Credential
async function getDynamicDatabaseCredential(
  role: string
): Promise<{
  username: string;
  password: string;
  leaseId: string;
  ttl: number;
}> {
  const result = await vaultClient.database.generateCredentials({
    name: role,
  });

  return {
    username: result.data.username,
    password: result.data.password,
    leaseId: result.lease_id,
    ttl: result.lease_duration,
  };
}

// Transit Encryption (Vault ile Şifreleme)
async function encryptWithTransit(
  keyName: string,
  plaintext: string
): Promise<string> {
  const result = await vaultClient.transit.encrypt({
    name: keyName,
    plaintext: Buffer.from(plaintext).toString('base64'),
  });

  return result.data.ciphertext;
}

async function decryptWithTransit(
  keyName: string,
  ciphertext: string
): Promise<string> {
  const result = await vaultClient.transit.decrypt({
    name: keyName,
    ciphertext,
  });

  return Buffer.from(result.data.plaintext, 'base64').toString();
}

// PKI Certificate Oluşturma
async function generateCertificate(
  role: string,
  commonName: string,
  ttl: string = '24h'
): Promise<{
  certificate: string;
  privateKey: string;
  caChain: string[];
}> {
  const result = await vaultClient.pki.generateCertificate({
    name: role,
    commonName,
    ttl,
  });

  return {
    certificate: result.data.certificate,
    privateKey: result.data.private_key,
    caChain: result.data.ca_chain,
  };
}

// Secret Rotation Hook
async function rotateSecret(path: string): Promise<void> {
  const currentSecret = await getSecret(path);

  // Yeni secret üret
  const newSecret = await generateNewSecret(path);

  // Yeni secret'ı yaz
  await setSecret(path, newSecret);

  // Eski secret'ı deprecated olarak işaretle
  await setSecret(`${path}_deprecated`, {
    ...currentSecret,
    deprecatedAt: new Date().toISOString(),
  });

  // Servisleri bilgilendir
  await notifyServicesRotation(path);

  // Audit log
  await logSecretRotation(path, 'success');
}

// Database Secret Rotation
async function rotateDatabaseSecrets(): Promise<void> {
  const roles = ['app-read', 'app-write', 'app-admin'];

  for (const role of roles) {
    await rotateSecret(`database/creds/${role}`);
  }
}

// Secret Access Policy
const SECRET_POLICIES = {
  'coremusic-app': `
    path "secret/data/coremusic/*" {
      capabilities = ["read"]
    }
    path "secret/data/coremusic/config/*" {
      capabilities = ["read", "list"]
    }
    path "database/creds/app-*" {
      capabilities = ["read"]
    }
    path "transit/encrypt/coremusic-key" {
      capabilities = ["update"]
    }
    path "transit/decrypt/coremusic-key" {
      capabilities = ["update"]
    }
  `,
  'coremusic-admin': `
    path "secret/*" {
      capabilities = ["create", "read", "update", "delete", "list"]
    }
    path "database/*" {
      capabilities = ["create", "read", "update", "delete", "list"]
    }
    path "pki/*" {
      capabilities = ["create", "read", "update", "delete", "list"]
    }
  `,
};

// Token Renewal
async function scheduleTokenRenewal(
  token: string,
  ttl: number
): Promise<void> {
  // TTL'in yarısında yenile
  const renewAt = (ttl / 2) * 1000;

  setInterval(async () => {
    try {
      await vaultClient.tokenRenewSelf();
      console.log('Vault token renewed successfully');
    } catch (error) {
      console.error('Vault token renewal failed:', error);
      // Yeniden authenticate ol
      await authenticateWithAppRole();
    }
  }, renewAt);
}

// Health Check
async function checkVaultHealth(): Promise<{
  sealed: boolean;
  standby: boolean;
  version: string;
}> {
  const health = await vaultClient.health();
  return {
    sealed: health.sealed,
    standby: health.standby,
    version: health.version,
  };
}

// Emergency Secret Access (Break Glass)
async function emergencySecretAccess(
  secretPath: string,
  reason: string,
  approver: string
): Promise<Record<string, any>> {
  // Emergency access policy ile oku
  const secret = await getSecret(secretPath);

  // Emergency audit trail
  await logEmergencyAccess({
    secretPath,
    reason,
    approver,
    timestamp: new Date(),
    accessedBy: process.env.VAULT_ROLE_ID,
  });

  // Security team'i bilgilendir
  await notifySecurityTeam({
    type: 'EMERGENCY_ACCESS',
    secretPath,
    reason,
    approver,
  });

  return secret;
}
```

## Güvenlik Kontrolleri

- [ ] AppRole authentication zorunlu olmalı
- [ ] Secret ID short-lived (5 dakika) olmalı
- [ ] Token TTL 1 saati aşmamalı
- [ ] Dynamic secrets her istekte yenilenmeli
- [ ] Secret rotation aktif olmalı
- [ ] Audit logging tüm erişimleri kaydetmeli
- [ ] Emergency access onay mekanizması olmalı
- [ ] Vault seal durumu izlenmeli
- [ ] Unseal key'leri HSM'de saklanmalı
- [ ] Policy principle of least privilege uygulanmalı

## Bağımlılıklar

- **encryption-aes256.md**: Transit encryption için
- **password-hashing.md**: Database credential yönetimi
- **audit-logging.md**: Secret access logları
- **K0 OS Katmanı**: Vault servis yönetimi

## Durum: Implementasyon

- [x] Vault yapısı tasarlandı
- [x] Policy'ler tanımlandı
- [ ] Vault cluster kurulacak
- [ ] AppRole auth entegrasyonu yapılacak
- [ ] Dynamic secrets aktifleştirilecek
- [ ] Secret rotation otomasyonu kurulacak
- [ ] Emergency access flow'u oluşturulacak
- [ ] Monitoring ve alerting ayarlanacak
