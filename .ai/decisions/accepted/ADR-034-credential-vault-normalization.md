---
title: "ADR-034: Credential Vault Normalization"
status: frozen
date: 2026-06-20
tags: [security, credential, vault, encryption, aes-256-gcm, frozen]
---

# ADR-034: Credential Vault Normalization

---

## 1. Executive Summary

### 1.1 KararÄ±n Ã–zeti

CoreMusic credential yÃ¶netimi, **merkezi credential vault** ile AES-256-GCM ÅŸifreleme kullanÄ±larak uygulanÄ±r. TÃ¼m hassas bilgiler (API key, DB password, JWT secret, ARL token) credential vault'ta AES-256-GCM ile ÅŸifrelenir. Vault master key'i sadece environment variable'dan yÃ¼klenir.

### 1.2 Temel GerekÃ§e

Credential'larÄ±n kodda veya log'da dÃ¼z metin olarak bulunmasÄ±, en ciddi gÃ¼venlik aÃ§Ä±klarÄ±ndan biridir. Merkezi credential vault, tÃ¼m hassas bilgileri tek bir gÃ¼venli noktada yÃ¶netir.

### 1.3 Beklenen SonuÃ§lar

- TÃ¼m credential'lar AES-256-GCM ile ÅŸifrelenir
- Vault master key'i environment variable'dan yÃ¼klenir
- Credential'lar ASLA kodda veya log'da gÃ¶rÃ¼nmez
- Log'larda `[REDACTED]` ile maskelenir

---

## 2. Status

| Alan | DeÄŸer |
|------|-------|
| **Durum** | frozen |
| **Versiyon** | 2.0.0 |
| **OluÅŸturma Tarihi** | 2026-06-20 |
| **Son GÃ¼ncelleme** | 2026-08-15 |
| **Otorite** | Security Engineer |
| **Risk Seviyesi** | critical |

---

## 3. Context

### 3.1 Problem TanÄ±mÄ±

Credential sÄ±zÄ±ntÄ±larÄ±:
- Kod iÃ§inde hardcode edilmiÅŸ API key'ler
- Log dosyalarÄ±nda gÃ¶rÃ¼nÃ¼r ÅŸifreler
- .env dosyalarÄ±nÄ±n sÄ±zmasÄ±
- Git history'sinde kalan credential'lar

### 3.2 Credential Vault Mimarisi

```
â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”
â”‚              Credential Vault                     â”‚
â”‚                                                  â”‚
â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”  â”‚
â”‚  â”‚  Master Key (Environment Variable)        â”‚  â”‚
â”‚  â”‚  â€¢ CREDENTIAL_VAULT_KEY env var           â”‚  â”‚
â”‚  â”‚  â€¢ 256-bit AES key                         â”‚  â”‚
â”‚  â”‚  â€¢ ASLA kodda saklanmaz                    â”‚  â”‚
â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜  â”‚
â”‚                                                  â”‚
â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”  â”‚
â”‚  â”‚  Encrypted Credentials:                    â”‚  â”‚
â”‚  â”‚                                            â”‚  â”‚
â”‚  â”‚  DB_PASSWORD    â†’ AES-256-GCM encrypted   â”‚  â”‚
â”‚  â”‚  API_KEY        â†’ AES-256-GCM encrypted   â”‚  â”‚
â”‚  â”‚  JWT_SECRET     â†’ AES-256-GCM encrypted   â”‚  â”‚
â”‚  â”‚  DEEZER_ARL     â†’ AES-256-GCM encrypted   â”‚  â”‚
â”‚  â”‚  SMTP_PASSWORD  â†’ AES-256-GCM encrypted   â”‚  â”‚
â”‚  â”‚  REDIS_PASSWORD â†’ AES-256-GCM encrypted   â”‚  â”‚
â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜  â”‚
â”‚                                                  â”‚
â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”  â”‚
â”‚  â”‚  Encryption Format:                        â”‚  â”‚
â”‚  â”‚  base64(iv) . "." . base64(ciphertext)     â”‚  â”‚
â”‚  â”‚    . "." . base64(tag)                      â”‚  â”‚
â”‚  â”‚                                            â”‚  â”‚
â”‚  â”‚  IV: 96-bit (12 byte) random              â”‚  â”‚
â”‚  â”‚  Tag: 16-byte authentication              â”‚  â”‚
â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜  â”‚
â”‚                                                  â”‚
â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜
```

---

## 4. Decision

### 4.1 Karar Bildirimi

**CoreMusic, AES-256-GCM ÅŸifreli merkezi credential vault kullanÄ±r. TÃ¼m hassas bilgiler vault'ta saklanÄ±r.**

### 4.2 Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | AES-256-GCM ÅŸifreleme | âœ… Zorunlu |
| 2 | Master key env variable | âœ… Zorunlu |
| 3 | Credential kodda yasak | âŒ Yasak |
| 4 | Credential log'da yasak | âŒ Yasak |
| 5 | `[REDACTED]` maskeleme | âœ… Zorunlu |
| 6 | .gitignore'da .env | âœ… Zorunlu |

### 4.3 Kod Ã–rnekleri

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Security\Service;

/**
 * Credential Vault Service
 *
 * ADR-034 uyumlu credential yÃ¶netimi.
 * AES-256-GCM ÅŸifreleme.
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
     * Credential'Ä± ÅŸifreler ve vault'a kaydeder.
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
     * Credential'Ä± Ã§Ã¶zer.
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

### 4.4 Redaction KurallarÄ±

| Veri TÃ¼rÃ¼ | Log FormatÄ± |
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
| Hardcoded credentials | GÃ¼vensiz, OWASP ihlali |
| Plain .env | Encryption yok |
| HashiCorp Vault | Harici baÄŸÄ±mlÄ±lÄ±k |

---

## 6. Consequences

### Olumlu
- Credential gÃ¼venliÄŸi saÄŸlanÄ±r
- OWASP A02 uyumluluÄŸu
- Merkezi yÃ¶netim

### Olumsuz
- Master key yÃ¶netimi karmaÅŸÄ±k
- Vault eriÅŸim overhead

---

## 7. Quality Report

| Metrik | DeÄŸer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **SatÄ±r** | ~500+ |
| **Status** | Frozen |

---

*ADR-034: Credential Vault Normalization v2.0.0 â€” CoreMusic Security*
*Authority: Security Engineer Â· Last Updated: 2026-08-15*
*Status: Frozen Â· Governance: Red Team Â· Human Mode Â· Truth Mode*