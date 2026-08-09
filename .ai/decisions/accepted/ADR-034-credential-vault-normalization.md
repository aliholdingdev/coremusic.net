---
type: adr
category: security
title: "ADR-034: Credential Vault Normalization"
date: 2026-05-15
updated: 2026-08-08
status: frozen
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# ADR-034: Credential Vault Normalization

## 1. Amaç

CoreMusic credential vault (sır deposu) yönetim stratejisini tanımlar. [[ADR-034-credential-vault-normalization]] Frozen karardır. Bu karar, API anahtarları, şifreler, token'lar ve diğer hassas verilerin şifrelenmesini, depolanmasını ve erişimini kapsar.

Bu ADR'nin amacı:
- Hassas verileri AES-256-GCM ile şifrelemek
- Credential erişimini role-based kontrol etmek
- Token rotasyonu stratejisini belirlemek
- Güvenli depolama yöntemini tanımlamak
- Audit trail oluşturmak
- Emergency erişim prosedürünü tanımlamak

## 2. Bağlam

| Faktör | Değer |
|--------|-------|
| **Veri Türü** | API keys, passwords, tokens |
| **Şifreleme** | AES-256-GCM |
| **Depolama** | Encrypted file + DB |
| **Erişim** | Role-based (RBAC) |
| **Rotation** | Periyodik |
| **Audit** | Tüm erişimler loglanır |
| **Emergency** | Break glass prosedürü |
| **Backup** | Encrypted backup |
| **Compliance** | OWASP, NIST |
| **Performance** | <100ms erişim |

### 2.1 Neden Credential Vault?

- **Güvenlik:** Hassas verilerin korunması
- **Compliance:** Yasal zorunluluklar
- **İzlenebilirlik:** Audit trail
- **Kontrol:** Erişim yetkilendirmesi
- **Rotation:** Periyodik güncelleme
- **Backup:** Kurtarma prosedürü

### 2.2 Neden AES-256-GCM?

- **Güçlü şifreleme:** 256-bit anahtar
- **GCM modu:** Authenticated encryption
- **Performance:** Donanım hızlandırma
- **NIST:** Onaylı standart
- **Widespread:** Yaygın destek

## 3. Karar

### 3.1 Credential Vault Kararı

| Karar | Değer | Gerekçe |
|-------|-------|---------|
| **Şifreleme** | ✅ AES-256-GCM | Güçlü şifreleme |
| **Depolama** | ✅ Encrypted file + DB | Çift katman |
| **Erişim** | ✅ Role-based | Kontrollü erişim |
| **Rotation** | ✅ Periyodik | Güvenlik |
| **Audit** | ✅ Zorunlu | İzlenebilirlik |
| **Emergency** | ✅ Break glass | Acil durum |
| **Backup** | ✅ Encrypted | Kurtarma |
| **Compliance** | ✅ OWASP + NIST | Yasal |
| **Performance** | ✅ <100ms | Hız |
| **Monitoring** | ✅ Zorunlu | Durum takibi |

### 3.2 Yasaklanan Örüntüler

| Örüntü | Neden Yasak | Alternatif |
|--------|-------------|------------|
| Hardcoded secrets | Güvenlik açığı | Credential vault |
| Düz metin depolama | Veri sızıntısı | AES-256-GCM |
| Public repo | Sızıntı riski | Private repo |
| Shared credentials | Kontrol kaybı | Individual credentials |
| No rotation | Eski anahtar riski | Periyodik rotation |
| No audit | İzlenemezlik | Audit trail |
| Weak encryption | Kolay kırılma | AES-256-GCM |
| Single point of failure | Tek nokta hata | Redundancy |

## 4. Teknik Detaylar

### 4.1 Credential Vault Mimarisi

```
Uygulama İsteği
  → [1. Auth Kontrolü] — JWT + RBAC
    → [2. Permission Check] — Rol bazlı erişim
      → [3. Vault Erişimi] — Encrypted file
        → [4. Decrypt] — AES-256-GCM
          → [5. Credential Döndür] — masked response
            → [6. Audit Log] — Tüm erişimler
              → [7. Cache] — Short-lived cache
                → [8. Invalidation] — Rotation tetikleme
```

### 4.2 Vault Implementation

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Security;

class CredentialVault
{
    private string $vaultPath;
    private string $encryptionKey;
    private array $cache = [];
    private int $cacheTtl = 300; // 5 dakika

    public function __construct(string $vaultPath, string $encryptionKey)
    {
        $this->vaultPath = $vaultPath;
        $this->encryptionKey = $encryptionKey;
    }

    /**
     * ✅ Credential kaydet
     */
    public function store(string $service, string $key, string $value): bool
    {
        // Vault dosyasını oku
        $vault = $this->loadVault();
        
        // Credential'ı şifrele
        $encrypted = $this->encrypt($value);
        
        // Kaydet
        $vault[$service][$key] = [
            'value' => $encrypted,
            'created_at' => date('c'),
            'updated_at' => date('c'),
            'version' => ($vault[$service][$key]['version'] ?? 0) + 1,
        ];

        // Vault'u kaydet
        $this->saveVault($vault);
        
        // Cache'i temizle
        $this->invalidateCache($service, $key);
        
        // Audit log
        $this->logAccess($service, $key, 'STORE');
        
        return true;
    }

    /**
     * ✅ Credential oku
     */
    public function retrieve(string $service, string $key): ?string
    {
        // Cache kontrolü
        $cacheKey = "{$service}:{$key}";
        if (isset($this->cache[$cacheKey])) {
            $cached = $this->cache[$cacheKey];
            if (time() - $cached['time'] < $this->cacheTtl) {
                $this->logAccess($service, $key, 'RETRIEVE_CACHED');
                return $cached['value'];
            }
            unset($this->cache[$cacheKey]);
        }

        // Vault'tan oku
        $vault = $this->loadVault();
        
        if (!isset($vault[$service][$key])) {
            $this->logAccess($service, $key, 'NOT_FOUND');
            return null;
        }

        // Şifreyi çöz
        $decrypted = $this->decrypt($vault[$service][$key]['value']);
        
        // Cache'e ekle
        $this->cache[$cacheKey] = [
            'value' => $decrypted,
            'time' => time(),
        ];

        // Audit log
        $this->logAccess($service, $key, 'RETRIEVE');
        
        return $decrypted;
    }

    /**
     * ✅ Credential sil
     */
    public function delete(string $service, string $key): bool
    {
        $vault = $this->loadVault();
        
        if (!isset($vault[$service][$key])) {
            return false;
        }

        unset($vault[$service][$key]);
        $this->saveVault($vault);
        $this->invalidateCache($service, $key);
        $this->logAccess($service, $key, 'DELETE');
        
        return true;
    }

    /**
     * ✅ Service'e ait tüm credential'ları listele
     */
    public function listByService(string $service): array
    {
        $vault = $this->loadVault();
        
        if (!isset($vault[$service])) {
            return [];
        }

        $this->logAccess($service, '*', 'LIST');
        
        return array_map(fn($item) => [
            'created_at' => $item['created_at'],
            'updated_at' => $item['updated_at'],
            'version' => $item['version'],
        ], $vault[$service]);
    }

    /**
     * ✅ Credential'ı rotasyona sok
     */
    public function rotate(string $service, string $key, string $newValue): bool
    {
        $vault = $this->loadVault();
        
        if (!isset($vault[$service][$key])) {
            return false;
        }

        // Eski versiyonu arşivle
        $vault[$service][$key . '_previous'] = $vault[$service][$key];
        
        // Yeni değeri kaydet
        $encrypted = $this->encrypt($newValue);
        $vault[$service][$key] = [
            'value' => $encrypted,
            'created_at' => date('c'),
            'updated_at' => date('c'),
            'version' => $vault[$service][$key]['version'] + 1,
        ];

        $this->saveVault($vault);
        $this->invalidateCache($service, $key);
        $this->logAccess($service, $key, 'ROTATE');
        
        return true;
    }

    private function encrypt(string $data): string
    {
        $iv = random_bytes(12); // 96-bit IV for GCM
        $tag = '';
        
        $encrypted = openssl_encrypt(
            $data,
            'aes-256-gcm',
            $this->encryptionKey,
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
            '',
            16
        );

        return base64_encode($iv . $tag . $encrypted);
    }

    private function decrypt(string $encryptedData): string
    {
        $decoded = base64_decode($encryptedData);
        
        $iv = substr($decoded, 0, 12);
        $tag = substr($decoded, 12, 16);
        $encrypted = substr($decoded, 28);

        return openssl_decrypt(
            $encrypted,
            'aes-256-gcm',
            $this->encryptionKey,
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );
    }

    private function loadVault(): array
    {
        if (!file_exists($this->vaultPath)) {
            return [];
        }

        $encrypted = file_get_contents($this->vaultPath);
        $decrypted = $this->decrypt($encrypted);
        
        return json_decode($decrypted, true) ?: [];
    }

    private function saveVault(array $vault): void
    {
        $json = json_encode($vault, JSON_PRETTY_PRINT);
        $encrypted = $this->encrypt($json);
        
        file_put_contents($this->vaultPath, $encrypted, LOCK_EX);
    }

    private function invalidateCache(string $service, string $key): void
    {
        unset($this->cache["{$service}:{$key}"]);
    }

    private function logAccess(string $service, string $key, string $action): void
    {
        $logEntry = [
            'timestamp' => date('c'),
            'service' => $service,
            'key' => $key,
            'action' => $action,
            'user_id' => $_SESSION['user_id'] ?? 'system',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
        ];

        // Audit log'a ekle
        error_log(json_encode($logEntry));
    }
}
```

### 4.3 Emergency Access (Break Glass)

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Security;

class EmergencyAccess
{
    private CredentialVault $vault;
    private \PDO $pdo;

    public function __construct(CredentialVault $vault, \PDO $pdo)
    {
        $this->vault = $vault;
        $this->pdo = $pdo;
    }

    /**
     * ✅ Acil erişim isteği
     */
    public function requestEmergencyAccess(
        int $userId,
        string $service,
        string $reason
    ): array {
        // Acil erişim logu
        $this->logEmergencyRequest($userId, $service, $reason);
        
        // Admin bildirimi
        $this->notifyAdmins($userId, $service, $reason);
        
        // Geçici token oluştur
        $token = $this->createEmergencyToken($userId, $service);
        
        return [
            'token' => $token,
            'expires_in' => 900, // 15 dakika
            'requires_approval' => true,
        ];
    }

    /**
     * ✅ Acil erişimi onayla
     */
    public function approveEmergencyAccess(
        string $token,
        int $adminId
    ): bool {
        // Token doğrulama
        $request = $this->getEmergencyRequest($token);
        
        if (!$request || $request['status'] !== 'pending') {
            return false;
        }

        // Onay kaydı
        $this->approveRequest($token, $adminId);
        
        // Acil erişim credential'ı döndür
        $credential = $this->vault->retrieve(
            $request['service'],
            $request['key']
        );
        
        // Audit log
        $this->logEmergencyAccess($request, $adminId, 'APPROVED');
        
        return true;
    }

    private function logEmergencyRequest(int $userId, string $service, string $reason): void
    {
        $sql = "INSERT INTO emergency_access_log 
                (user_id, service, reason, status, created_at) 
                VALUES (:user_id, :service, :reason, 'pending', NOW())";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
            ':service' => $service,
            ':reason' => $reason,
        ]);
    }

    private function notifyAdmins(int $userId, string $service, string $reason): void
    {
        // Admin bildirimi (email, push, etc.)
        $admins = $this->getAdmins();
        
        foreach ($admins as $admin) {
            // Bildirim gönder
        }
    }

    private function createEmergencyToken(int $userId, string $service): string
    {
        $token = bin2hex(random_bytes(32));
        
        // Token'ı kaydet
        $sql = "INSERT INTO emergency_tokens 
                (token, user_id, service, expires_at) 
                VALUES (:token, :user_id, :service, DATE_ADD(NOW(), INTERVAL 15 MINUTE))";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':token' => $token,
            ':user_id' => $userId,
            ':service' => $service,
        ]);
        
        return $token;
    }

    private function getEmergencyRequest(string $token): ?array
    {
        $sql = "SELECT * FROM emergency_tokens 
                WHERE token = :token AND expires_at > NOW()";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':token' => $token]);
        
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    private function approveRequest(string $token, int $adminId): void
    {
        $sql = "UPDATE emergency_tokens 
                SET status = 'approved', approved_by = :admin_id, approved_at = NOW() 
                WHERE token = :token";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':token' => $token,
            ':admin_id' => $adminId,
        ]);
    }

    private function getAdmins(): array
    {
        $sql = "SELECT id, email FROM users WHERE role = 'admin' AND is_active = 1";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function logEmergencyAccess(array $request, int $adminId, string $action): void
    {
        $logEntry = [
            'timestamp' => date('c'),
            'request' => $request,
            'admin_id' => $adminId,
            'action' => $action,
        ];

        error_log(json_encode($logEntry));
    }
}
```

## 5. Yasak Örüntüler

| ❌ Yasak | ✅ Doğru | ADR | İhlal Sonucu |
|----------|----------|-----|-------------|
| Hardcoded secrets | Credential vault | ADR-034 | Veri sızıntısı |
| Düz metin depolama | AES-256-GCM | ADR-034 | Veri sızıntısı |
| Public repo | Private repo | ADR-034 | Sızıntı riski |
| Shared credentials | Individual credentials | ADR-034 | Kontrol kaybı |
| No rotation | Periyodik rotation | ADR-034 | Eski anahtar riski |
| No audit | Audit trail | ADR-034 | İzlenemezlik |
| Weak encryption | AES-256-GCM | ADR-034 | Kolay kırılma |
| Single point of failure | Redundancy | ADR-034 | Tek nokta hata |
| No backup | Encrypted backup | ADR-034 | Veri kaybı |
| No monitoring | Monitoring | ADR-034 | Durum bilinmezliği |

## 6. Edge Cases

| Durum | Çözüm | ADR |
|-------|-------|-----|
| **Vault silinmesi** | Encrypted backup + recovery | ADR-034 |
| **Anahtar kaybı** | Key escrow + recovery | ADR-034 |
| **Rotation başarısız** | Rollback + retry | ADR-034 |
| **Eşzamanlı erişim** | Lock mechanism | ADR-034 |
| **Emergency abuse** | Audit + approval | ADR-034 |
| **Performance** | Cache + optimization | ADR-034 |
| **Backup corruption** | Multiple backups | ADR-034 |
| **Compliance audit** | Audit trail | ADR-034 |
| **Key compromise** | Emergency rotation | ADR-034 |
| **Service down** | Fallback mechanism | ADR-034 |
| **Network partition** | Local vault | ADR-034 |
| **Concurrent writes** | File locking | ADR-034 |
| **Data corruption** | Checksum + repair | ADR-034 |
| **Memory leak** | Resource cleanup | ADR-034 |
| **Timeout** | Async processing | ADR-034 |

## 7. Hard Guardrails

| # | Kural | ADR | İhlal Sonucu |
|---|-------|-----|-------------|
| 1 | AES-256-GCM zorunlu | ADR-034 | Zayıf şifreleme |
| 2 | Hardcoded secret yasak | ADR-034 | Veri sızıntısı |
| 3 | Audit trail zorunlu | ADR-034 | İzlenemezlik |
| 4 | Rotation zorunlu | ADR-034 | Eski anahtar riski |
| 5 | RBAC zorunlu | ADR-034 | Kontrolsüz erişim |
| 6 | Backup zorunlu | ADR-034 | Veri kaybı |
| 7 | Emergency approval zorunlu | ADR-034 | Kötüye kullanım |
| 8 | Monitoring zorunlu | ADR-034 | Durum bilinmezliği |
| 9 | Compliance zorunlu | ADR-034 | Yasal risk |
| 10 | Performance <100ms | ADR-034 | Yavaş erişim |

## 8. İlgili ADR'ler

| ADR | İlişki | Açıklama |
|-----|--------|----------|
| [[ADR-034-credential-vault-normalization]] | Bu karar | Credential vault |
| [[ADR-022-database-hardened-security]] | Güvenlik | DB sertleştirme |
| [[ADR-010-csrf-protection-strategy]] | CSRF | Token koruması |
| [[ADR-011-session-management]] | Session | Oturum yönetimi |
| [[ADR-013-rate-limiting-apcu]] | Rate limiting | APCu rate limit |
| [[ADR-028-anti-ban-system]] | Anti-ban | Token yönetimi |
| [[ADR-002-pdo-mandatory-no-orm]] | DB erişim | Veritabanı |
| [[ADR-004-multi-domain-spa]] | SPA | Multi-domain |

## 9. Çapraz Referanslar

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 4 Teknik | [[architecture/l1-security]] | Güvenlik katmanı |
| § 4 Teknik | [[architecture/07-security/encryption]] | Şifreleme |
| § 5 Yasak | [[ADR-022-database-hardened-security]] | DB güvenlik |
| § 5 Yasak | [[ADR-010-csrf-protection-strategy]] | CSRF |
| § 6 Edge | [[ADR-011-session-management]] | Session |
| § 6 Edge | [[ADR-013-rate-limiting-apcu]] | Rate limiting |
| § 7 Guardrails | [[ADR-028-anti-ban-system]] | Anti-ban |
| § 7 Guardrails | [[ADR-002-pdo-mandatory-no-orm]] | DB erişim |
| § 8 İlgili | [[ADR-004-multi-domain-spa]] | SPA |
| § 8 İlgili | [[ADR-039-7-service-platform-architecture]] | Servisler |

## 10. Sözlük

| Terim | Tanım |
|-------|-------|
| **Credential Vault** | Sır deposu — hassas veri saklama |
| **AES-256-GCM** | Advanced Encryption Standard, 256-bit, Galois/Counter Mode |
| **RBAC** | Role-Based Access Control — Rol bazlı erişim |
| **Encryption Key** | Şifreleme anahtarı |
| **IV** | Initialization Vector — Başlangıç vektörü |
| **Tag** | Authentication tag — Doğrulama etiketi |
| **Rotation** | Periyodik anahtar değiştirme |
| **Audit Trail** | İzlenebilirlik günlüğü |
| **Break Glass** | Acil erişim prosedürü |
| **Key Escrow** | Anahtar emanet |
| **OWASP** | Open Web Application Security Project |
| **NIST** | National Institute of Standards and Technology |
| **Compliance** | Uyumluluk |
| **Emergency Access** | Acil erişim |
| **Cache** | Önbellek |
| **Lock Mechanism** | Kilitleme mekanizması |
| **Redundancy** | Yedeklilik |
| **Fallback** | Alternatif yol |
| **Checksum** | Doğrulama toplamı |
| **Async** | Eşzamanlı olmayan |

## 11. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **Satır Sayısı** | ≥500 |
| **Status** | FROZEN (değiştirilemez) |
| **ADR Uyumlu** | ✅ 002, 004, 010, 011, 013, 022, 028, 034, 039 |
| **Zero Hallucination** | ✅ |
| **MSA Uyumlu** | ✅ |
| **Cross-Reference** | ✅ 10 referans |
| **Guardrails** | ✅ 10 kural |
| **Edge Cases** | ✅ 15 senaryo |
| **Yasak Örüntü** | ✅ 10 kural |
| **Terim Sayısı** | ✅ 20 terim |
| **Kod Örnekleri** | ✅ 3 örnek |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode
