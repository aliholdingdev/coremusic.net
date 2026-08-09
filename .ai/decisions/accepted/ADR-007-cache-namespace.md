---
type: adr
category: infrastructure
title: "ADR-007: Cache Namespace + Zero Code Before Plan"
date: 2026-05-15
updated: 2026-08-08
status: frozen
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# ADR-007: Cache Namespace + Zero Code Before Plan

**Status:** Frozen (değiştirilemez)
**Kategorisi:** Infrastructure
**İlgili Agent:** Backend, Data, Security
**Frozen:** 2026-05-15 — ADR 001-037 arasında değiştirilemez

---

## 1. Amaç

Bu ADR, CoreMusic platformunda APCu tabanlı cache namespace standardını ve Zero Code Before Plan prensibini tanımlar. Cache'leme stratejisi, tüm backend servislerinde tutarlılık sağlamak için namespace tabanlıdır. Zero Code Before Plan, kod yazmadan önce tam planlama zorunluluğunu getirir.

---

## 2. Bağlam

### 2.1 İş Problemi

Backend servisleri yüksek yük altında performans sorunları yaşayabilir:

| Sorun | Sonuç | Çözüm |
|-------|-------|-------|
| Tekrarlanan DB sorguları | DB yükü artar | Cache |
| Namespace çakışması | Veri kaybı | Namespace standardı |
| Plansız kodlama | Mimari bozulma | Zero Code Before Plan |
| Cache bozulması | Tutarsız veri | Cache invalidation |
| Yüksek maliyet | Kaynak israfı | Önbellekleme |

### 2.2 İlişkili Kararlar

| ADR | İlişki |
|-----|--------|
| ADR-002 | PDO mandatory |
| ADR-004 | Multi-domain SPA |
| ADR-013 | Rate limiting APCu |
| ADR-022 | DB hardened security |
| ADR-042 | MSA limit |

---

## 3. Karar

CoreMusic'te APCu tabanlı cache namespace standardı uygulanacak. Zero Code Before Plan prensibi tüm geliştirme süreçlerinde zorunlu olacak.

### 3.1 Karar Özeti

| Parametre | Değer |
|-----------|-------|
| Cache driver | APCu |
| Namespace format | `{domain}:{subdomain}:{entity}:{id}` |
| TTL varsayılan | 3600s |
| Zero Code Before Plan | Zorunlu |
| Onay süreci | Plan → Onay → Kod |

---

## 4. Teknik Detaylar

### 4.1 Cache Namespace Formatı

```php
// Namespace formatı
'{domain}:{subdomain}:{entity}:{id}'

// Örnekler
'music:music.coremusic.net:user:12345'
'music:music.coremusic.net:song:67890'
'music:music.coremusic.net:album:11111'
'admin:admin.coremusic.net:config:app'
'download:download.coremusic.net:queue:22222'
```

### 4.2 Cache Implementasyonu

```php
class CacheManager {
    private const PREFIX = 'cmusic:';
    private const DEFAULT_TTL = 3600;

    public function get(string $namespace, string $key): mixed {
        $fullKey = self::PREFIX . $namespace . ':' . $key;
        return apcu_fetch($fullKey);
    }

    public function set(string $namespace, string $key, mixed $value, int $ttl = self::DEFAULT_TTL): bool {
        $fullKey = self::PREFIX . $namespace . ':' . $key;
        return apcu_store($fullKey, $value, $ttl);
    }

    public function delete(string $namespace, string $key): bool {
        $fullKey = self::PREFIX . $namespace . ':' . $key;
        return apcu_delete($fullKey);
    }

    public function clear(string $namespace): bool {
        $info = apcu_cache_info();
        $prefix = self::PREFIX . $namespace . ':';
        foreach ($info['cache_list'] as $entry) {
            if (str_starts_with($entry['info'], $prefix)) {
                apcu_delete($entry['info']);
            }
        }
        return true;
    }
}
```

### 4.3 Cache Stratejisi

| Strateji | Kullanım | Örnek |
|----------|----------|-------|
| Read-Through | DB okuma | User profile |
| Write-Through | DB yazma | Config update |
| Write-Behind | Async yazma | Audit log |
| Cache-Aside | Manuel | Search results |

### 4.4 Cache TTL Değerleri

| Veri Türü | TTL | Güncelleme |
|-----------|-----|-----------|
| User session | 3600s | Otomatik |
| User profile | 1800s | Manuel |
| Config | 7200s | Manuel |
| Search results | 300s | Otomatik |
| API response | 60s | Otomatik |
| Static content | 86400s | Manuel |

### 4.5 Zero Code Before Plan Protokolü

```
[1. Gereksinim Analizi] → [2. Planlama] → [3. Onay] → [4. Kodlama] → [5. Test]
```

| Adım | Aksiyon | Çıktı | Zorunlu |
|------|---------|-------|---------|
| 1 | Gereksinimleri tanımla | Gereksinim dokümanı | ✅ |
| 2 | Teknik planı hazırla | Plan dokümanı | ✅ |
| 3 | Kullanıcı onayını al | Onay kaydı | ✅ |
| 4 | Kodlamaya başla | Kod | ✅ |
| 5 | Testleri çalıştır | Test sonuçları | ✅ |

### 4.6 Plan Dokümanı Formatı

```markdown
# Görev Planı

## Gereksinimler
- [ ] Gereksinim 1
- [ ] Gereksinim 2

## Teknik Çözüm
- Mimari tasarım
- API tasarımı
- DB tasarımı

## Etkilenen Dosyalar
- dosya1.php
- dosya2.js

## Test Senaryoları
- [ ] Test 1
- [ ] Test 2

## Riskler
- Risk 1: Çözüm
- Risk 2: Çözüm

## Zaman Tahmini
- X saat/gün
```

### 4.7 Cache Namespace Kuralları

| Kural | Açıklama | Örnek |
|-------|----------|-------|
| Domain zorunlu | Her namespace domain içermeli | `music:...` |
| Subdomain zorunlu | Her namespace subdomain içermeli | `music:music.coremusic.net:...` |
| Entity zorunlu | Her namespace entity içermeli | `music:music.coremusic.net:user:...` |
| ID opsiyonel | Tüm entity'lerin ID'si olmayabilir | `music:music.coremusic.net:config:app` |
| snake_case | Namespace'ler snake_case olmalı | `core_music` ❌ → `coremusic` ✅ |
| Max uzunluk | Namespace max 255 karakter | — |

### 4.8 Cache Invalidation Stratejisi

| Tetikleyici | Aksiyon | Yöntem |
|-------------|---------|--------|
| DB update | İlgili cache'i temizle | Manuel invalidation |
| Config change | Config cache'ini temizle | Manuel invalidation |
| User logout | Session cache'ini temizle | Otomatik |
| TTL expiration | Cache otomatik süresi doldu | Otomatik |
| Manual flush | Tüm cache'i temizle | Manuel |

### 4.9 Cache Monitoring

| Metrik | Hedef | Ölçüm |
|--------|-------|-------|
| Hit rate | >90% | `apcu_cache_info()` |
| Miss rate | <10% | `apcu_cache_info()` |
| Size | <64MB | `apcu_cache_info()` |
| Entries | <10000 | `apcu_cache_info()` |
| Evictions | <1% | `apcu_cache_info()` |

### 4.10 Zero Code Before Plan Aksiyon Matrisi

| Durum | Aksiyon | Sonuç |
|-------|---------|-------|
| Plan onaylandı | Kodlamaya başla | Devam |
| Plan onaylanmadı | Planı güncelle | Tekrar onay |
| Plan eksik | Eksikleri tamamla | Güncelle |
| Yeni gereksinim | Planı güncelle | Tekrar onay |
| Acil durum | Hızlı plan + onay | Devam |
| Reddedildi | Kapat | Durdur |

### 4.11 Cache Hata Yönetimi

| Hata | Sonuç | Çözüm |
|------|-------|-------|
| APCu unavailable | DB'ye fall back | Retry |
| Cache corruption | Cache'i temizle | Yeniden oluştur |
| Namespace conflict | Hata logla | Namespace'i düzelt |
| TTL expiration | Yeniden yükle | Otomatik |
| Memory full | Eviction | TTL azalt |

### 4.12 Plan Onay Workflow'u

```
Plan hazırlandı
  → [1. Self-Review] — Eksik var mı?
    → [2. Peer Review] — Başka bir agent incelesin
      → [3. User Approval] — Kullanıcı onayı
        → [4. Documentation] — Onayı kaydet
          → [5. Execution] — Kodlamaya başla
```

### 4.13 Cache Implementasyonu Detayı

```php
class CacheManager {
    private const PREFIX = 'cmusic:';
    private const DEFAULT_TTL = 3600;
    private const MAX_KEY_LENGTH = 255;

    public function get(string $namespace, string $key): mixed {
        $fullKey = self::buildKey($namespace, $key);
        $value = apcu_fetch($fullKey, $success);
        
        if (!$success) {
            return null;
        }
        
        return $value;
    }

    public function set(string $namespace, string $key, mixed $value, int $ttl = self::DEFAULT_TTL): bool {
        $fullKey = self::buildKey($namespace, $key);
        
        if (strlen($fullKey) > self::MAX_KEY_LENGTH) {
            throw new \InvalidArgumentException("Cache key too long: {$fullKey}");
        }
        
        return apcu_store($fullKey, $value, $ttl);
    }

    public function delete(string $namespace, string $key): bool {
        $fullKey = self::buildKey($namespace, $key);
        return apcu_delete($fullKey);
    }

    public function clear(string $namespace): bool {
        $info = apcu_cache_info();
        $prefix = self::PREFIX . $namespace . ':';
        
        foreach ($info['cache_list'] as $entry) {
            if (str_starts_with($entry['info'], $prefix)) {
                apcu_delete($entry['info']);
            }
        }
        
        return true;
    }

    public function exists(string $namespace, string $key): bool {
        $fullKey = self::buildKey($namespace, $key);
        return apcu_exists($fullKey);
    }

    public function increment(string $namespace, string $key, int $step = 1): int|false {
        $fullKey = self::buildKey($namespace, $key);
        return apcu_inc($fullKey, $step);
    }

    public function decrement(string $namespace, string $key, int $step = 1): int|false {
        $fullKey = self::buildKey($namespace, $key);
        return apcu_dec($fullKey, $step);
    }

    private function buildKey(string $namespace, string $key): string {
        return self::PREFIX . $namespace . ':' . $key;
    }

    public function getStats(): array {
        return apcu_cache_info();
    }

    public function getHitRate(): float {
        $info = apcu_cache_info();
        $hits = $info['num_hits'] ?? 0;
        $misses = $info['num_misses'] ?? 0;
        $total = $hits + $misses;
        
        return $total > 0 ? ($hits / $total) * 100 : 0;
    }
}
```

### 4.14 Cache Provider Arayüzü

```php
interface CacheProviderInterface {
    public function get(string $namespace, string $key): mixed;
    public function set(string $namespace, string $key, mixed $value, int $ttl = 3600): bool;
    public function delete(string $namespace, string $key): bool;
    public function clear(string $namespace): bool;
    public function exists(string $namespace, string $key): bool;
    public function increment(string $namespace, string $key, int $step = 1): int|false;
    public function decrement(string $namespace, string $key, int $step = 1): int|false;
}

class ApcuCacheProvider implements CacheProviderInterface {
    // APCu implementasyonu
}

class RedisCacheProvider implements CacheProviderInterface {
    // Redis implementasyonu (gelecek için)
}
```

### 4.15 Zero Code Before Plan Detayı

```
[1. Gereksinim Analizi]
  → Kullanıcı gereksinimleri tanımlandı
    → Teknik gereksinimler yazıldı
      → Sınır koşulları belirlendi

[2. Planlama]
  → Mimari tasarım hazırlandı
    → API tasarımı yazıldı
      → DB tasarımı yapıldı
        → Test planı oluşturuldu

[3. Onay]
  → Plan dokümanı sunuldu
    → Kullanıcı inceledi
      → Onay verildi (veya reddedildi)

[4. Kodlama]
  → Plan dahilinde kodlama başladı
    → Adım adım uygulama
      → Her adım test edildi

[5. Test]
  → Unit test yazıldı
    → Integration test yazıldı
      → E2E test yazıldı
        → Tüm testler geçti

[6. Review]
  → Code review yapıldı
    → Güvenlik kontrolleri yapıldı
      → Performans kontrolleri yapıldı

[7. Deploy]
  → Staging'de test edildi
    → Production'a deploy edildi
      → Monitor edildi
```

### 4.16 Cache Monitoring Dashboard

| Panel | Metrik | Hedef | Alarm |
|-------|--------|-------|-------|
| Hit Rate | % | >90% | <80% → WARN |
| Miss Rate | % | <10% | >20% → WARN |
| Memory | MB | <64MB | >50MB → WARN |
| Entries | adet | <10000 | >8000 → WARN |
| Evictions | adet | <100 | >50 → WARN |
| Operations |/s | >1000 | <500 → WARN |

### 4.17 Cache Warmer Stratejisi

```php
class CacheWarmer {
    public function warmAll(): void {
        $this->warmUserSessions();
        $this->warmConfig();
        $this->warmStaticContent();
    }

    private function warmUserSessions(): void {
        // Aktif kullanıcı oturumlarını cache'le
    }

    private function warmConfig(): void {
        // Config dosyalarını cache'le
    }

    private function warmStaticContent(): void {
        // Statik içerikleri cache'le
    }
}
```

### 4.18 Cache Invalidation Detayı

| Tetikleyici | Yöntem | Kapsam | Öncelik |
|-------------|--------|--------|---------|
| DB update | Manuel invalidation | İlgili entity | CRITICAL |
| Config change | Manuel invalidation | Config namespace | HIGH |
| User logout | Otomatik invalidation | User session | HIGH |
| TTL expiration | Otomatik | Tek entry | ORTA |
| Manual flush | Manuel | Tüm namespace | DÜŞÜK |
| Memory pressure | Otomatik eviction | Eski entry'ler | ORTA |

### 4.19 Cache Hata Yönetimi Detayı

| Hata | Sonuç | Çözüm | Öncelik |
|------|-------|-------|---------|
| APCu unavailable | DB'ye fall back | Retry 3x | CRITICAL |
| Cache corruption | Tutarsız veri | Cache'i temizle | HIGH |
| Namespace conflict | Veri kaybı | Namespace'i düzelt | HIGH |
| TTL expiration | Veri yok | Yeniden yükle | ORTA |
| Memory full | Eviction | TTL azalt | ORTA |
| Key too long | Hata | Key'i kısalt | DÜŞÜK |

---

## 5. Yasak Örüntüleri

| ❌ Yasak | ✅ Doğru | İhlal Sonucu |
|----------|----------|-------------|
| Plansız kodlama | Plan + onay ile kodlama | Mimari bozulma |
| Namespace çakışması | Benzersiz namespace | Veri kaybı |
| Cache key guesswork | Structured keys | Hata riski |
| Hardcoded TTL | Config'den oku | Bakım zorluğu |
| Cache without invalidation | Invalidation stratejisi | Tutarsızlık |
| No monitoring | Cache monitoring | Görünmeyen sorunlar |

---

## 6. Edge Cases

| Senaryo | Tetikleyici | Çözüm |
|---------|-------------|-------|
| Cache stampede | Yüksek load | Mutex ile single load |
| Namespace pollution | Çok fazla entry | TTL azaltma |
| Memory pressure | APCu memory dolu | Eviction + monitoring |
| Concurrent writes | Eşzamanlı yazma | Lock mechanism |
| Plan rejection | Kullanıcı reddetti | Planı güncelle |
| Acil durum | Kritik hata | Hızlı plan + onay |

---

## 7. Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Zero Code Before Plan zorunlu | Mimari bozulma |
| 2 | Cache namespace standardı zorunlu | Namespace çakışması |
| 3 | Plan onayı zorunlu | Yetkisiz kodlama |
| 4 | Cache invalidation zorunlu | Tutarsız veri |
| 5 | Cache monitoring zorunlu | Görünmeyen sorunlar |

---

## 8. İlgili ADR'ler

| ADR | Konu | İlişki |
|-----|------|--------|
| [[ADR-002-pdo-mandatory-no-orm]] | PDO mandatory | DB cache |
| [[ADR-004-multi-domain-spa]] | Multi-domain SPA | Namespace yapısı |
| [[ADR-013-rate-limiting-apcu]] | Rate limiting APCu | APCu kullanımı |
| [[ADR-022-database-hardened-security]] | DB security | Güvenlik cache |
| [[ADR-042-vault-restructuring-2026-08-03]] | Vault standardı | Bilgi kaynağı |
| [[ADR-005-ultrathink-protocol]] | Zero Hallucination | Planlama |

---

## 9. Çapraz Referanslar

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 3 Karar | [[CLAUDE.md]] §7 | Hard guardrails |
| § 4.1 Namespace | [[ADR-004-multi-domain-spa]] | Domain yapısı |
| § 4.2 Implement | [[ADR-013-rate-limiting-apcu]] | APCu |
| § 4.5 Zero Code | [[ADR-005-ultrathink-protocol]] | Zero Hallucination |
| § 5 Yasaklar | [[CLAUDE.md]] §21 | Yasak örüntüleri |
| § 6 Edge | [[ADR-042-vault-restructuring-2026-08-03]] | Edge cases |
| § 7 Guardrails | [[brain.md]] §17 | Hard guardrails |
| § 8 ADR | [[ADR-022-database-hardened-security]] | Security |

---

## 10. Sözlük

| Terim | Tanım |
|-------|-------|
| **APCu** | APC User Cache — PHP önbellek sistemi |
| **Namespace** | Cache anahtar alan adı |
| **TTL** | Time To Live — Ömür süresi |
| **Zero Code Before Plan** | Plan olmadan kod yazma yasağı |
| **Read-Through** | Otomatik okuma stratejisi |
| **Write-Through** | Senkron yazma stratejisi |
| **Write-Behind** | Async yazma stratejisi |
| **Cache-Aside** | Manuel cache stratejisi |
| **Invalidation** | Cache geçersizleştirme |
| **Stampede** | Yüksek eşzamanlı erişim |
| **Eviction** | Bellek baskısında silme |

---

## 11. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| Version | 2.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Sections | 11 |
| Frozen | 2026-05-15 |
| Cache Strategies | 4 |
| TTL Values | 6 |
| Zero Code Steps | 5 |
| Plan Template Fields | 6 |
| Cache Rules | 6 |
| Invalidation Triggers | 5 |
| Monitoring Metrics | 5 |
| Yasak Örüntüleri | 6 |
| Edge Cases | 6 |
| Hard Guardrails | 5 |
| ADR References | 6 |
| Cross References | 8 |
| Glossary Terms | 11 |
| Authority | SSOT |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode
