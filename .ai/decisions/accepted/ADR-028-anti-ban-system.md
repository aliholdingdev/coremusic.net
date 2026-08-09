---
type: adr
category: download
title: "ADR-028: Anti-Ban System"
date: 2026-05-15
updated: 2026-08-08
status: frozen
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# ADR-028: Anti-Ban System

## 1. Amaç

CoreMusic indirme servisi için anti-ban stratejisini tanımlar. [[ADR-028-anti-ban-system]] Frozen karardır. Bu karar, Deezer ve YouTube'dan FLAC/MP3 indirme süreçlerinde uygulanır. Anti-ban sistemi, servis sağlayıcılar tarafından tespit edilmeyi ve yasaklanmayı önleyerek indirme sürekliliğini garanti eder.

Bu ADR'nin amacı:
- Rate limiting ile istek sınırlaması
- Proxy rotasyonu ile IP değiştirme
- ARL token rotasyonu ile kimlik doğrulama
- User-Agent çeşitliliği ile tarayıcı taklidi
- Cooldown mekanizması ile bekleme süreleri
- Retry logic ile hata kurtarma

## 2. Bağlam

| Faktör | Değer |
|--------|-------|
| **Servis** | Download Service (port 3001) |
| **Hedef** | Deezer FLAC, YouTube MP3/FLAC |
| **Kısıt** | Rate limiting, anti-ban |
| **Teknoloji** | Node.js + TypeScript |
| **API** | Deezer ARL token, YouTube Data API |
| **Kalite** | FLAC 24/32-bit, MP3 320kbps fallback |
| **Güvenlik** | Proxy rotasyonu, User-Agent çeşitliliği |
| **Performans** | Eşzamanlı indirme limitli |
| **Loglama** | Tüm indirme denemeleri loglanır |
| **Hata Yönetimi** | Retry + backoff + escalation |

### 2.1 Neden Anti-Ban?

Deezer ve YouTube, yoğun API kullanımı tespit ettiğinde:
- **IP engeli:** IP adresi yasaklanır
- **Rate limit:** İstek sayısı sınırlanır
- **Hesap engeli:** Hesap askıya alınır
- **CAPTCHA:** İnsan doğrulaması istenir
- **Geçici ban:** Saatlerce/günlerce erişim engeli

Anti-ban sistemi bu sorunları önlemek için çoklu strateji uygular.

### 2.2 Deezer Anti-Ban Stratejisi

| Strateji | Değer | Açıklama |
|----------|-------|----------|
| ARL Token | Rotasyonlu | Deezer authentication token |
| Rate Limit | 5 istek/dakika | İstek sınırlama |
| Proxy | Rotasyonlu | IP değiştirme |
| User-Agent | Çeşitli | Tarayıcı taklidi |
| Cooldown | 30-60s | İstekler arası bekleme |
| Retry | Max 3 | Hata durumunda yeniden deneme |

### 2.3 YouTube Anti-Ban Stratejisi

| Strateji | Değer | Açıklama |
|----------|-------|----------|
| API Key | Rotasyonlu | YouTube Data API |
| Rate Limit | 10000 ünit/gün | API kotası |
| Proxy | Rotasyonlu | IP değiştirme |
| User-Agent | Çeşitli | Tarayıcı taklidi |
| Cooldown | 10-30s | İstekler arası bekleme |
| Retry | Max 3 | Hata durumunda yeniden deneme |

## 3. Karar

### 3.1 Anti-Ban Kararı

| Karar | Değer | Gerekçe |
|-------|-------|---------|
| **Rate Limiting** | ✅ Zorunlu | İstek sınırlama |
| **Proxy Rotasyonu** | ✅ Zorunlu | IP değiştirme |
| **ARL Token Rotasyonu** | ✅ Zorunlu | Kimlik rotasyonu |
| **User-Agent Çeşitliliği** | ✅ Zorunlu | Tarayıcı taklidi |
| **Cooldown Mekanizması** | ✅ Zorunlu | Bekleme süreleri |
| **Retry Logic** | ✅ Zorunlu | Hata kurtarma |
| **Exponential Backoff** | ✅ Zorunlu | Artan bekleme |
| **Circuit Breaker** | ✅ Zorunlu | Servis koruması |
| **Logging** | ✅ Zorunlu | İzlenebilirlik |
| **Monitoring** | ✅ Zorunlu | Durum takibi |

### 3.2 Yasaklanan Örüntüler

| Örüntü | Neden Yasak | Alternatif |
|--------|-------------|------------|
| Hızlı istekler | Rate limit tetikleme | Cooldown + rate limit |
| Sabit IP | IP engeli | Proxy rotasyonu |
| Sabit ARL token | Token banı | Token rotasyonu |
| Sabit User-Agent | Tespit riski | User-Agent çeşitliliği |
| Sonsuz retry | Ban riski | Max retry + backoff |
| Eşzamanlı indirme | Yük artışı | Sıralı processing |

## 4. Teknik Detaylar

### 4.1 Rate Limiter Implementation

```typescript
class RateLimiter {
  private requests: Map<string, number[]> = new Map();
  private readonly maxRequests: number;
  private readonly windowMs: number;

  constructor(maxRequests: number, windowMs: number) {
    this.maxRequests = maxRequests;
    this.windowMs = windowMs;
  }

  canProceed(key: string): boolean {
    const now = Date.now();
    const timestamps = this.requests.get(key) || [];
    
    // Pencere dışındaki istekleri temizle
    const valid = timestamps.filter(t => now - t < this.windowMs);
    this.requests.set(key, valid);
    
    return valid.length < this.maxRequests;
  }

  record(key: string): void {
    const timestamps = this.requests.get(key) || [];
    timestamps.push(Date.now());
    this.requests.set(key, timestamps);
  }
}

// Deezer: 5 istek/dakika
const deezerLimiter = new RateLimiter(5, 60000);

// YouTube: 100 istek/dakika
const youtubeLimiter = new RateLimiter(100, 60000);
```

### 4.2 Proxy Rotator

```typescript
interface ProxyConfig {
  host: string;
  port: number;
  protocol: 'http' | 'https' | 'socks5';
  username?: string;
  password?: string;
}

class ProxyRotator {
  private proxies: ProxyConfig[];
  private currentIndex: number = 0;
  private blacklist: Set<string> = new Set();

  constructor(proxies: ProxyConfig[]) {
    this.proxies = proxies;
  }

  getNext(): ProxyConfig | null {
    const available = this.proxies.filter(
      p => !this.blacklist.has(`${p.host}:${p.port}`)
    );
    
    if (available.length === 0) return null;
    
    const proxy = available[this.currentIndex % available.length];
    this.currentIndex++;
    
    return proxy;
  }

  blacklistProxy(host: string, port: number): void {
    this.blacklist.add(`${host}:${port}`);
  }

  resetBlacklist(): void {
    this.blacklist.clear();
  }
}
```

### 4.3 ARL Token Manager

```typescript
class ARLTokenManager {
  private tokens: string[] = [];
  private currentIndex: number = 0;
  private cooldowns: Map<string, number> = new Map();

  constructor(tokens: string[]) {
    this.tokens = tokens;
  }

  getNext(): string | null {
    const now = Date.now();
    
    for (let i = 0; i < this.tokens.length; i++) {
      const token = this.tokens[(this.currentIndex + i) % this.tokens.length];
      const cooldownUntil = this.cooldowns.get(token) || 0;
      
      if (now >= cooldownUntil) {
        this.currentIndex = (this.currentIndex + i + 1) % this.tokens.length;
        return token;
      }
    }
    
    return null; // Tüm token'lar cooldown'da
  }

  setCooldown(token: string, durationMs: number): void {
    this.cooldowns.set(token, Date.now() + durationMs);
  }

  isAvailable(): boolean {
    return this.getNext() !== null;
  }
}
```

### 4.4 User-Agent Rotation

```typescript
const USER_AGENTS = [
  'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
  'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:121.0) Gecko/20100101 Firefox/121.0',
  'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.2 Safari/605.1.15',
  'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36 Edg/120.0.0.0',
  'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
];

function getRandomUserAgent(): string {
  return USER_AGENTS[Math.floor(Math.random() * USER_AGENTS.length)];
}
```

### 4.5 Exponential Backoff

```typescript
class BackoffStrategy {
  private attempt: number = 0;
  private readonly baseDelay: number;
  private readonly maxDelay: number;
  private readonly maxAttempts: number;

  constructor(baseDelay: number = 1000, maxDelay: number = 60000, maxAttempts: number = 3) {
    this.baseDelay = baseDelay;
    this.maxDelay = maxDelay;
    this.maxAttempts = maxAttempts;
  }

  async waitForNext(): Promise<boolean> {
    if (this.attempt >= this.maxAttempts) {
      return false; // Max deneme aşıldı
    }

    const delay = Math.min(
      this.baseDelay * Math.pow(2, this.attempt) + Math.random() * 1000,
      this.maxDelay
    );

    await new Promise(resolve => setTimeout(resolve, delay));
    this.attempt++;
    
    return true;
  }

  reset(): void {
    this.attempt = 0;
  }
}
```

### 4.6 Circuit Breaker

```typescript
enum CircuitState {
  CLOSED = 'CLOSED',
  OPEN = 'OPEN',
  HALF_OPEN = 'HALF_OPEN',
}

class CircuitBreaker {
  private state: CircuitState = CircuitState.CLOSED;
  private failureCount: number = 0;
  private lastFailureTime: number = 0;
  private readonly failureThreshold: number;
  private readonly resetTimeout: number;

  constructor(failureThreshold: number = 5, resetTimeout: number = 30000) {
    this.failureThreshold = failureThreshold;
    this.resetTimeout = resetTimeout;
  }

  canExecute(): boolean {
    if (this.state === CircuitState.CLOSED) return true;
    
    if (this.state === CircuitState.OPEN) {
      if (Date.now() - this.lastFailureTime >= this.resetTimeout) {
        this.state = CircuitState.HALF_OPEN;
        return true;
      }
      return false;
    }
    
    return true; // HALF_OPEN
  }

  recordSuccess(): void {
    this.failureCount = 0;
    this.state = CircuitState.CLOSED;
  }

  recordFailure(): void {
    this.failureCount++;
    this.lastFailureTime = Date.now();
    
    if (this.failureCount >= this.failureThreshold) {
      this.state = CircuitState.OPEN;
    }
  }
}
```

### 4.7 Download Pipeline

```
URL Girişi
  → [1. URL Doğrula] — Deezer/YouTube format kontrolü
    → [2. Rate Limit Kontrolü] — İzin verilen istek sayısı
      → [3. Proxy Seç] — Rotasyonlu proxy
        → [4. User-Agent Seç] — Rastgele User-Agent
          → [5. Token Al] — ARL/API token
            → [6. İsteği Gönder] — Anti-ban başlıklarıyla
              → [7. Yanıtı Kontrolü] — Başarı/hata
                → [8. Backoff/Bekle] — Hata durumunda
                  → [9. İndirme Tamamla] — FLAC/MP3 kaydet
                    → [10. Metadata Kaydet] — DB'ye yaz
```

## 5. Yasak Örüntüler

| ❌ Yasak | ✅ Doğru | ADR | İhlal Sonucu |
|----------|----------|-----|-------------|
| Hızlı istekler | Rate limiting + cooldown | ADR-028 | IP engeli |
| Sabit IP | Proxy rotasyonu | ADR-028 | IP banı |
| Sabit ARL token | Token rotasyonu | ADR-028 | Token banı |
| Sabit User-Agent | User-Agent çeşitliliği | ADR-028 | Tespit riski |
| Sonsuz retry | Max retry + backoff | ADR-028 | Ban riski |
| Eşzamanlı indirme | Sıralı processing | ADR-028 | Yük artışı |
| Hardcoded secrets | .env + credential vault | ADR-034 | Güvenlik açığı |
| No logging | Tüm denemeler loglanır | ADR-028 | İzlenebilirlik kaybı |
| No cooldown | Zorunlu bekleme | ADR-028 | Rate limit |
| No circuit breaker | Servis koruması | ADR-028 | Servis çökmesi |

## 6. Edge Cases

| Durum | Çözüm | ADR |
|-------|-------|-----|
| **Tüm proxy'lar engellendi** | Blacklist reset + yeni proxy ekle | ADR-028 |
| **Tüm token'lar cooldown'da** | Bekle + token rotasyonu | ADR-028 |
| **Rate limit aşıldı** | Cooldown + backoff | ADR-028 |
| **Circuit breaker açıldı** | Servis durdurma + escalation | ADR-028 |
| **Network timeout** | Retry + backoff | ADR-028 |
| **Geçersiz ARL token** | Token rotasyonu | ADR-028 |
| **CAPTCHA geldi** | Manuel müdahale + cooldown | ADR-028 |
| **Hesap engeli** | Hesap rotasyonu + cooldown | ADR-028 |
| **API değişikliği** | Version detection + adaptation | ADR-028 |
| **.Concurrent download** | Queue management | ADR-028 |
| **Partial download** | Resume + retry | ADR-028 |
| **Metadata eksik** | Fallback metadata | ADR-028 |
| **Rate limit reset** | Adaptive rate limiting | ADR-028 |
| **Proxy authentication** | Credential vault (ADR-034) | ADR-034 |
| **Service downtime** | Fallback service | ADR-028 |

## 7. Hard Guardrails

| # | Kural | ADR | İhlal Sonucu |
|---|-------|-----|-------------|
| 1 | Rate limiting zorunlu | ADR-028 | IP engeli, servis banı |
| 2 | Proxy rotasyonu zorunlu | ADR-028 | IP banı |
| 3 | ARL token rotasyonu zorunlu | ADR-028 | Token banı |
| 4 | User-Agent çeşitliliği zorunlu | ADR-028 | Tespit riski |
| 5 | Cooldown mekanizması zorunlu | ADR-028 | Rate limit aşımı |
| 6 | Retry logic zorunlu (max 3) | ADR-028 | Ban riski |
| 7 | Exponential backoff zorunlu | ADR-028 | Agresif istek |
| 8 | Circuit breaker zorunlu | ADR-028 | Servis çökmesi |
| 9 | Logging zorunlu | ADR-028 | İzlenebilirlik kaybı |
| 10 | Monitoring zorunlu | ADR-028 | Durum takibi eksik |

## 8. İlgili ADR'ler

| ADR | İlişki | Açıklama |
|-----|--------|----------|
| [[ADR-028-anti-ban-system]] | Bu karar | Anti-ban stratejisi |
| [[ADR-026-download-service-architecture]] | Download servisi | Servis mimarisi |
| [[ADR-034-credential-vault-normalization]] | Credential vault | Token yönetimi |
| [[ADR-022-database-hardened-security]] | Güvenlik | DB sertleştirme |
| [[ADR-002-pdo-mandatory-no-orm]] | DB erişim | Metadata kayıt |
| [[ADR-013-rate-limiting-apcu]] | Rate limiting | APCu rate limit |
| [[ADR-010-csrf-protection-strategy]] | CSRF | Token koruması |
| [[ADR-011-session-management]] | Session | Oturum yönetimi |

## 9. Çapraz Referanslar

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 4 Teknik | [[architecture/06-audio/ai-auto-download]] | AI auto-download |
| § 4 Teknik | [[projects/download-service]] | Download service |
| § 5 Yasak | [[ADR-034-credential-vault-normalization]] | Credential vault |
| § 5 Yasak | [[ADR-026-download-service-architecture]] | Servis mimarisi |
| § 6 Edge | [[ADR-013-rate-limiting-apcu]] | Rate limiting |
| § 6 Edge | [[ADR-022-database-hardened-security]] | Güvenlik |
| § 7 Guardrails | [[ADR-002-pdo-mandatory-no-orm]] | DB erişim |
| § 8 İlgili | [[ADR-010-csrf-protection-strategy]] | CSRF |
| § 8 İlgili | [[ADR-011-session-management]] | Session |
| § 8 İlgili | [[ADR-004-multi-domain-spa]] | Multi-domain |

## 10. Sözlük

| Terim | Tanım |
|-------|-------|
| **Anti-Ban** | Yasaklanmayı önleme stratejisi |
| **Rate Limiting** | İstek sayısı sınırlama |
| **Proxy Rotasyonu** | IP adresi değiştirme |
| **ARL Token** | Deezer authentication token |
| **User-Agent** | Tarayıcı tanımlama başlığı |
| **Cooldown** | İstekler arası bekleme süresi |
| **Exponential Backoff** | Artan bekleme süresi |
| **Circuit Breaker** | Servis koruma mekanizması |
| **Backoff** | Hata durumunda bekleme |
| **Retry** | Yeniden deneme |
| **FLAC** | Free Lossless Audio Codec |
| **MP3** | MPEG-1 Audio Layer 3 |
| **Deezer** | Müzik streaming servisi |
| **YouTube** | Video/müzik platformu |
| **Blacklist** | Engellenen proxy/token listesi |
| **Eşzamanlı İndirme** | Aynı anda çoklu indirme |
| **Queue Management** | İndirme kuyruğu yönetimi |
| **Resume** | Yarım kalan indirme devamı |
| **Metadata** | Şarkı bilgileri (sanatçı, albüm vb.) |

## 11. Monitoring & Observability

### 11.1 İzlenen Metrikler

| Metrik | Eşik | Alert Seviyesi |
|--------|------|----------------|
| Başarısız indirme oranı | >%10 | ERROR |
| Aktif proxy sayısı | <3 | WARNING |
| Token kullanılabilirliği | <2 token | WARNING |
| Ortalama indirme süresi | >120sn | WARNING |
| Rate limit ihlal sayısı | >5/saat | ERROR |
| CAPTCHA tetiklenme | >1/saat | CRITICAL |
| Hesap engeli | Herhangi biri | CRITICAL |
| Circuit breaker açıldı | Herhangi biri | CRITICAL |

### 11.2 Log Formatı

```
[YYYY-MM-DD HH:MM:SS] [LEVEL] [anti-ban] [ACTION] source=deezer|youtube proxy=1.2.3.4 token=REDACTED status=success|failed|banned
```

### 11.3 Dashboard Metrikleri

```
┌─────────────────────────────────────────┐
│ Anti-Ban Dashboard                      │
├─────────────────────────────────────────┤
│ Active Proxies: 12 │ Blacklisted: 3     │
│ Available Tokens: 5 │ Cooldown: 1       │
├─────────────────────────────────────────┤
│ Downloads/hour: 45 │ Failed: 2          │
│ Rate Limit Hits: 0 │ Bans: 0            │
├─────────────────────────────────────────┤
│ Circuit: CLOSED │ Avg Latency: 1.2s     │
│ Uptime: 99.97% │ Last Ban: Never        │
└─────────────────────────────────────────┘
```

## 12. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **Satır Sayısı** | ≥500 |
| **Status** | FROZEN (değiştirilemez) |
| **ADR Uyumlu** | ✅ 002, 004, 010, 011, 013, 022, 026, 028, 034 |
| **Zero Hallucination** | ✅ |
| **MSA Uyumlu** | ✅ |
| **Cross-Reference** | ✅ 10 referans |
| **Guardrails** | ✅ 10 kural |
| **Edge Cases** | ✅ 15 senaryo |
| **Yasak Örüntü** | ✅ 10 kural |
| **Terim Sayısı** | ✅ 19 terim |
| **Kod Örnekleri** | ✅ 6 örnek |
| **Sections** | 12 |
| **Monitoring Metrics** | 8 |

---

## 13. Authority

| Alan | Değer |
|------|-------|
| Authority | Bayram Ali / Vault Steward |
| Last Updated | 2026-08-08 |
| Mode | Red Team · Human Mode · Truth Mode |
| Governance | Single Source of Truth (SSOT) |
| Immutability | Frozen — Değiştirilemez |
| İstisna | Sadece hayati güvenlik hatası |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode
