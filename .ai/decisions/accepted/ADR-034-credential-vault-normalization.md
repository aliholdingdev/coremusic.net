---
type: decision
id: "034"
title: "ADR-034: Credential Vault Normalization"
category: "security"
status: "frozen"
date: "2026-06-20"
updated: "2026-08-15"
authority: "Security Engineer"
governance: "Red Team · Human Mode · Truth Mode"
supersedes: null
version: 2.0.0
tags: [security, credential, vault, encryption, aes-256-gcm, frozen]
risk-level: "critical"
owasp-top10: ["A02:2021", "A04:2021"]
references:
  - "[[brain.md]]"
  - "[[CLAUDE.md]]"
  - "[[AGENTS.md]]"
  - "[[decisions/accepted/ADR-022-database-hardened-security]]"
  - "[[architecture/l0-infrastructure]]"
---

# ADR-034: Credential Vault Normalization

---

## 1. Executive Summary

### 1.1 Kararın Özeti

CoreMusic credential yönetimi, **merkezi credential vault** ile AES-256-GCM şifreleme kullanılarak uygulanır. Tüm hassas bilgiler (API key, DB password, JWT secret, ARL token) credential vault'ta AES-256-GCM ile şifrelenir. Vault master key'i sadece environment variable'dan yüklenir.

### 1.2 Temel Gerekçe

Credential'ların kodda veya log'da düz metin olarak bulunması, en ciddi güvenlik açıklarından biridir. Merkezi credential vault, tüm hassas bilgileri tek bir güvenli noktada yönetir.

### 1.3 Beklenen Sonuçlar

- Tüm credential'lar AES-256-GCM ile şifrelenir
- Vault master key'i environment variable'dan yüklenir
- Credential'lar ASLA kodda veya log'da görünmez
- Log'larda `[REDACTED]` ile maskelenir

---

## 2. Status

| Alan | Değer |
|------|-------|
| **Durum** | frozen |
| **Versiyon** | 2.0.0 |
| **Oluşturma Tarihi** | 2026-06-20 |
| **Son Güncelleme** | 2026-08-15 |
| **Otorite** | Security Engineer |
| **Risk Seviyesi** | critical |

---

## 3. Context

### 3.1 Problem Tanımı

Credential sızıntıları:
- Kod içinde hardcode edilmiş API key'ler
- Log dosyalarında görünür şifreler
- .env dosyalarının sızması
- Git history'sinde kalan credential'lar

### 3.2 Credential Vault Mimarisi

```
┌─────────────────────────────────────────────────┐
│              Credential Vault                     │
│                                                  │
│  ┌────────────────────────────────────────────┐  │
│  │  Master Key (Environment Variable)        │  │
│  │  • CREDENTIAL_VAULT_KEY env var           │  │
│  │  • 256-bit AES key                         │  │
│  │  • ASLA kodda saklanmaz                    │  │
│  └────────────────────────────────────────────┘  │
│                                                  │
│  ┌────────────────────────────────────────────┐  │
│  │  Encrypted Credentials:                    │  │
│  │                                            │  │
│  │  DB_PASSWORD    → AES-256-GCM encrypted   │  │
│  │  API_KEY        → AES-256-GCM encrypted   │  │
│  │  JWT_SECRET     → AES-256-GCM encrypted   │  │
│  │  DEEZER_ARL     → AES-256-GCM encrypted   │  │
│  │  SMTP_PASSWORD  → AES-256-GCM encrypted   │  │
│  │  REDIS_PASSWORD → AES-256-GCM encrypted   │  │
│  └────────────────────────────────────────────┘  │
│                                                  │
│  ┌────────────────────────────────────────────┐  │
│  │  Encryption Format:                        │  │
│  │  base64(iv) . "." . base64(ciphertext)     │  │
│  │    . "." . base64(tag)                      │  │
│  │                                            │  │
│  │  IV: 96-bit (12 byte) random              │  │
│  │  Tag: 16-byte authentication              │  │
│  └────────────────────────────────────────────┘  │
│                                                  │
└─────────────────────────────────────────────────┘
```

---

## 4. Decision

### 4.1 Karar Bildirimi

**CoreMusic, AES-256-GCM şifreli merkezi credential vault kullanır. Tüm hassas bilgiler vault'ta saklanır.**

### 4.2 Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | AES-256-GCM şifreleme | ✅ Zorunlu |
| 2 | Master key env variable | ✅ Zorunlu |
| 3 | Credential kodda yasak | ❌ Yasak |
| 4 | Credential log'da yasak | ❌ Yasak |
| 5 | `[REDACTED]` maskeleme | ✅ Zorunlu |
| 6 | .gitignore'da .env | ✅ Zorunlu |

### 4.3 Kod Örnekleri

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Security\Service;

/**
 * Credential Vault Service
 *
 * ADR-034 uyumlu credential yönetimi.
 * AES-256-GCM şifreleme.
 */
final class CredentialVaultService
{
    private const CIPHER = 'aes-256-gcm';
    private string $masterKey;

    public function __construct()
    {
        $this->masterKey = getenv('CREDENTIAL_VAULT_KEY');
        if ($this->masterKey === false || $this->masterKey === '') {
            throw new \RuntimeException('CREDENTIAL_VAULT_KEY not set');
        }
    }

    /**
     * Credential'ı şifreler ve vault'a kaydeder.
     */
    public function store(string $key, string $value): void
    {
        $iv = random_bytes(12);
        $tag = '';
        $ciphertext = openssl_encrypt(
            $value,
            self::CIPHER,
            $this->masterKey,
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
            '',
            16
        );

        $encrypted = base64_encode($iv) . '.' .
                     base64_encode($ciphertext) . '.' .
                     base64_encode($tag);

        file_put_contents(
            $this->getVaultPath($key),
            $encrypted
        );
    }

    /**
     * Credential'ı çözer.
     */
    public function retrieve(string $key): string
    {
        $encrypted = file_get_contents($this->getVaultPath($key));
        if ($encrypted === false) {
            throw new \RuntimeException("Credential not found: {$key}");
        }

        [$iv, $ciphertext, $tag] = explode('.', $encrypted);

        return openssl_decrypt(
            base64_decode($ciphertext),
            self::CIPHER,
            $this->masterKey,
            OPENSSL_RAW_DATA,
            base64_decode($iv),
            base64_decode($tag)
        );
    }

    private function getVaultPath(string $key): string
    {
        return __DIR__ . '/../../.vault/' . $key . '.enc';
    }
}
```

### 4.4 Redaction Kuralları

| Veri Türü | Log Formatı |
|-----------|-------------|
| API Key | `API Key: [REDACTED] (service: deezer)` |
| DB Password | `DB Password: [REDACTED]` |
| JWT Secret | `JWT Secret: [REDACTED]` |
| Session Token | `Session: [REDACTED]` |
| ARL Token | `ARL: [REDACTED]` |

---

## 5. Alternatives Considered

| Alternatif | Neden Reddedildi |
|------------|------------------|
| Hardcoded credentials | Güvensiz, OWASP ihlali |
| Plain .env | Encryption yok |
| HashiCorp Vault | Harici bağımlılık |

---

## 6. Consequences

### Olumlu
- Credential güvenliği sağlanır
- OWASP A02 uyumluluğu
- Merkezi yönetim

### Olumsuz
- Master key yönetimi karmaşık
- Vault erişim overhead

---

## 7. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **Satır** | ~500+ |
| **Status** | Frozen |

---

*ADR-034: Credential Vault Normalization v2.0.0 — CoreMusic Security*
*Authority: Security Engineer · Last Updated: 2026-08-15*
*Status: Frozen · Governance: Red Team · Human Mode · Truth Mode*
