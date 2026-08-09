---
type: architecture
category: contracts
title: "API Rate Limiting Strategy"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# API Rate Limiting Strategy

**Zorunlu Bağlantılar:** [[api-architecture-master]], [[ADR-013-rate-limiting-apcu]]

---

## 1. Amaç

CoreMusic API'sinin abuse önleme, DDoS koruması ve adil kaynak kullanımı sağlamak için rate limiting stratejisini tanımlar. APCu tabanlı sliding window algoritması kullanılır.

---

## 2. Temel İlkeler

| İlke | Açıklama |
|------|----------|
| Sliding Window | Zamana dayalı kayan pencere |
| Per-Endpoint | Her endpoint için ayrı limit |
| Per-User | Kullanıcı bazlı limit |
| Per-IP | IP adresi bazlı limit |
| Graceful Degradation | Limit aşıldığında nazik yanıt |
| Header Transparency | Client'a durum bilgisi |

---

## 3. Sliding Window vs Fixed Window

| Özellik | Sliding Window | Fixed Window |
|---------|---------------|--------------|
| Pencere | Kayan (sliding) | Sabit (fixed) |
| Burst | Daha az burst | Burst riski |
| Accuracy | Yüksek | Orta |
| Karmaşıklık | Orta | Düşük |
| Kullanım | ✅ CoreMusic | — |

---

## 4. Per-Endpoint Limitleri

| Endpoint | Limit | Pencere | Kategori |
|----------|-------|---------|----------|
| `POST /auth/login` | 5 | 60s | Auth (Sıkı) |
| `POST /auth/register` | 3 | 300s | Auth (Çok Sıkı) |
| `POST /auth/forgot-password` | 3 | 300s | Auth (Çok Sıkı) |
| `POST /auth/reset-password` | 5 | 60s | Auth (Sıkı) |
| `GET /api/*` | 120 | 60s | API (Normal) |
| `POST /api/*` | 60 | 60s | API (Yazma) |
| `PUT /api/*` | 60 | 60s | API (Yazma) |
| `DELETE /api/*` | 30 | 60s | API (Silme) |
| `POST /download/*` | 10 | 60s | Download |
| `GET /media/stream/*` | 200 | 60s | Streaming |
| `POST /search` | 30 | 60s | Search |
| `*` (Default) | 60 | 60s | Default |

---

## 5. Per-User Limitleri

| Kullanıcı Rolü | Limit/60s | Burst |
|----------------|-----------|-------|
| Anonymous | 30 | 5 |
| User | 60 | 10 |
| Premium | 120 | 20 |
| Admin | 300 | 50 |
| Internal Service | 1000 | 100 |

---

## 6. Per-IP Limitleri

| Kategori | Limit/60s | Açıklama |
|----------|-----------|----------|
| Anonymous | 30 | Auth yok |
| Authenticated | 120 | Auth var |
| Internal | ∞ | Internal services |
| Blacklisted | 0 | Block |

---

## 7. Rate Limit Headers

### 7.1 Response Headers

| Header | Açıklama | Örnek |
|--------|----------|-------|
| `X-RateLimit-Limit` | Pencere içindeki maksimum istek | `120` |
| `X-RateLimit-Remaining` | Kalan istek hakkı | `85` |
| `X-RateLimit-Reset` | Pencere sıfırlanma zamanı (epoch) | `1691592060` |
| `Retry-After` | Yeniden deneme süresi (saniye) | `30` |

### 7.2 Örnek Response Header'ları

```
HTTP/1.1 200 OK
X-RateLimit-Limit: 120
X-RateLimit-Remaining: 85
X-RateLimit-Reset: 1691592060
```

```
HTTP/1.1 429 Too Many Requests
X-RateLimit-Limit: 120
X-RateLimit-Remaining: 0
X-RateLimit-Reset: 1691592060
Retry-After: 30
```

---

## 8. Graceful Degradation

### 8.1 429 Response

```json
{
    "error": {
        "code": 429,
        "message": "Rate limit exceeded",
        "retry_after": 30,
        "limit": 120,
        "remaining": 0,
        "reset": 1691592060
    }
}
```

### 8.2 Degradation Seviyeleri

| Seviye | Kalan | Aksiyon |
|--------|-------|---------|
| Normal | >20% | Tam hız |
| Uyarı | 10-20% | Header ile uyarı |
| Kritik | 1-10% | Yavaşlatma |
| Limit | 0 | 429 + Retry-After |

---

## 9. Rate Limit Bypass (Internal Services)

| Servis | Bypass | Yöntem |
|--------|--------|--------|
| Media Service (5000/6000) | ✅ | Internal IP whitelist |
| Audio Service (9741/9742) | ✅ | Internal IP whitelist |
| Download Service (3001) | ✅ | API key header |
| Auth Service | ✅ | Internal network |
| AI Service | ✅ | Internal network |

```php
// Internal service detection
if ($this->isInternalService($request)) {
    return; // Skip rate limiting
}

private function isInternalService(Request $request): bool
{
    $internalIps = ['127.0.0.1', '::1', '10.0.0.0/8', '172.16.0.0/12'];
    $clientIp = $request->getClientIp();

    foreach ($internalIps as $ip) {
        if ($clientIp === $ip || $this->ipInCidr($clientIp, $ip)) {
            return true;
        }
    }

    return false;
}
```

---

## 10. Uygulama (APCu Sliding Window)

```php
final class RateLimiter
{
    public function __construct(
        private \APCu $apcu,
        private int $windowSize = 60
    ) {}

    public function attempt(string $key, int $limit): bool
    {
        $now = time();
        $windowStart = $now - $this->windowSize;
        $windowKey = "rate_limit:{$key}";

        // Sliding window cleanup
        $this->apcu->lock($windowKey);
        $requests = $this->apcu->get($windowKey) ?? [];

        // Remove old requests
        $requests = array_filter($requests, fn($ts) => $ts > $windowStart);

        if (count($requests) >= $limit) {
            $this->apcu->unlock($windowKey);
            return false;
        }

        // Add current request
        $requests[] = $now;
        $this->apcu->set($windowKey, $requests, $this->windowSize);

        $this->apcu->unlock($windowKey);
        return true;
    }

    public function getRemaining(string $key, int $limit): int
    {
        $now = time();
        $windowStart = $now - $this->windowSize;
        $requests = $this->apcu->get("rate_limit:{$key}") ?? [];
        $requests = array_filter($requests, fn($ts) => $ts > $windowStart);

        return max(0, $limit - count($requests));
    }

    public function getResetTime(string $key): int
    {
        $requests = $this->apcu->get("rate_limit:{$key}") ?? [];
        if (empty($requests)) {
            return time() + $this->windowSize;
        }
        return min($requests) + $this->windowSize;
    }
}
```

---

## 11. Key Generation

| Kural | Format | Örnek |
|-------|--------|-------|
| Authenticated | `user:{userId}:{endpoint}` | `user:123:api/tracks` |
| Anonymous | `ip:{clientIp}:{endpoint}` | `ip:192.168.1.1:api/tracks` |
| Global | `global:{endpoint}` | `global:api/tracks` |

---

## 12. Monitoring & Alerting

| Metrik | Eşik | Aksiyon |
|--------|------|---------|
| 429 Oranı | >5% | Warning |
| Burst Oranı | >10 req/s | Investigation |
| Blacklist | Herhangi biri | Auto-block + alert |
| Internal Bypass | Yetkisiz | CRITICAL alert |

---

## 13. Edge Cases

| Durum | Çözüm |
|-------|-------|
| APCu çökmesi | Fallback to allow (fail-open) |
| Clock drift | Server clock sync (NTP) |
| Distributed deploy | Redis distributed lock |
| Token refresh storm | Separate auth rate limit |
| DDoS | Layer 7 firewall + rate limit |

---

## 14. Warnings

| # | Uyarı | Sonuc |
|---|-------|-------|
| 1 | Rate limit olmadan produksiyon | DDoS riski |
| 2 | Sliding window yerine fixed window | Burst riski |
| 3 | Header'expose edilmezse | Client tarafı tahmin |
| 4 | Internal bypass滥用 edilirse | Güvenlik açığı |
| 5 | 429 response detaylı değilse | Kullanıcı deneyimi düşüşü |

---

## 15. Cross References

| Kaynak | Hedef | İlişki |
|--------|-------|--------|
| [[api-architecture-master]] | Ana mimari referans | Master |
| [[ADR-013-rate-limiting-apcu]] | Rate limiting ADR | Frozen ADR |

---

## 16. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 1.0.0 |
| Algorithm | APCu Sliding Window ✅ |
| Endpoint Count | 12 endpoint |
| User Roles | 5 seviye |
| Header Count | 4 header |
| Internal Services | 5 bypass |
| Degradation Levels | 4 seviye |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
