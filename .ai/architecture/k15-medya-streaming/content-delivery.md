---
title: "Content Delivery Network"
layer: K15
category: "Medya & Streaming"
date: 2026-09-20
---

# Content Delivery Network (CDN)

## Genel Bakış

Content Delivery Network entegrasyonu, medya içeriklerini coğrafi olarak dağıtılmış edge sunucuları üzerinden sunar. Origin shield, token authentication ve edge caching stratejileri ile düşük gecikme yüksek bant genişliği sağlar. COREMUSIC, çoklu CDN stratejisi ile failover ve load balancing desteği sunar.

## CDN Mimarisi

```
┌─────────────────────────────────────────────────────────┐
│                 CDN Architecture                        │
├─────────────────────────────────────────────────────────┤
│                                                         │
│  ┌──────────┐    ┌──────────┐    ┌──────────┐         │
│  │ Origin   │───▶│ Origin   │───▶│ Edge     │──▶ User  │
│  │ Server   │    │ Shield   │    │ PoP      │         │
│  │          │    │          │    │          │         │
│  └──────────┘    └──────────┘    └──────────┘         │
│       │               │               │                 │
│       ▼               ▼               ▼                 │
│  ┌──────────┐    ┌──────────┐    ┌──────────┐         │
│  │ Primary  │    │ Secondary│    │ Cache    │         │
│  │ Storage  │    │ Storage  │    │ Layer    │         │
│  └──────────┘    └──────────┘    └──────────┘         │
│                                                         │
│  CDN Providers:                                        │
│  ├── CloudFront (AWS)                                 │
│  ├── Cloudflare                                      │
│  ├── Fastly                                          │
│  ├── Akamai                                          │
│  └── Self-hosted (Nginx)                              │
└─────────────────────────────────────────────────────────┘
```

## Teknik Detaylar

### Origin Shield

```
Origin Shield Pipeline:
┌──────────┐    ┌──────────┐    ┌──────────┐
│ Edge PoP │───▶│ Shield   │───▶│ Origin   │
│          │    │ (Central)│    │ Server   │
└──────────┘    └──────────┘    └──────────┘
     │               │               │
     ▼               ▼               ▼
  Cache Miss      Cache Miss     Source Storage
  → Shield        → Origin       → Origin Pull

Shield Benefits:
- Origin load reduction: %70-90
- Origin bandwidth reduction: %80-95
- Improved cache hit ratio
- Reduced origin latency
```

### Token Authentication

```
Token Authentication Pipeline:
┌──────────┐    ┌──────────┐    ┌──────────┐
│ Client   │───▶│ CDN Edge │───▶│ Origin   │
│ Request  │    │ Verify   │    │ Validate │
└──────────┘    └──────────┘    └──────────┘
     │               │               │
     ▼               ▼               ▼
  Token Param   HMAC Verify    Session Check
  URL Signing   Timestamp      Rate Limit
  IP Binding    IP Check       Geo Block

Token URL Format:
https://cdn.coremusic.io/stream/audio.m3u8
  ?token=abc123def456
  &expires=1726848000
  &ip=203.0.113.42
  &path=/stream/audio.m3u8
  &signature=hmac-sha256
```

### Edge Caching Stratejisi

```
Cache Policy Matrix:
┌──────────────────┬──────────────┬──────────────┬───────────┐
│ İçerik Tipi      │ TTL          │ Stale       │ Purge     │
├──────────────────┼──────────────┼──────────────┼───────────┤
│ HLS Segments     │ 7 gün        │ revalidate  │ Manuel    │
│ DASH Segments    │ 7 gün        │ revalidate  │ Manuel    │
│ Podcast Files    │ 30 gün       │ stale-if-   │ Manuel    │
│                  │              │ error       │           │
│ Album Art        │ 30 gün       │ stale-      │ Manuel    │
│                  │              │ while-      │           │
│                  │              │ revalidate  │           │
│ Metadata         │ 1 saat       │ stale-      │ Auto      │
│                  │              │ while-      │           │
│                  │              │ revalidate  │           │
│ Static Assets    │ 1 yıl        │ immutable   │ Versioned │
│ (JS/CSS/Fonts)   │              │             │           │
└──────────────────┴──────────────┴──────────────┴───────────┘
```

### Cache Headers

```
Cache-Control Directives:
┌──────────────────┬────────────────────────────────────┐
│ Directive        │ Kullanım                           │
├──────────────────┼────────────────────────────────────┤
│ public           │ CDN'de cache'lenebilir             │
│ private          │ Sadece browser cache               │
│ no-cache         │ Revalidation gerekli               │
│ no-store         │ Hiç cache'leme                     │
│ max-age          │ Cache süresi (saniye)              │
│ s-maxage         │ CDN cache süresi                   │
│ must-revalidate  │ Süre dolduğunda revalidate         │
│ stale-while-     │ Revalidation arası eski cache kullan│
│ revalidate       │                                    │
│ stale-if-error   │ Hata durumunda eski cache kullan   │
│ immutable        │ Asla değişmez (versiyonlu dosya)   │
└──────────────────┴────────────────────────────────────┘
```

### CDN Configuration Example

```nginx
# Nginx CDN Origin Configuration
location ~* \.(m3u8|m4s|ts|mp4)$ {
    add_header Cache-Control "public, max-age=604800, stale-while-revalidate=86400";
    add_header X-Content-Type-Options "nosniff";
    add_header Access-Control-Allow-Origin "*";
    
    # HLS specific
    if ($request_uri ~* \.m3u8$) {
        add_header Cache-Control "public, max-age=3, stale-while-revalidate=10";
        add_header Content-Type "application/vnd.apple.mpegurl";
    }
    
    # DASH specific
    if ($request_uri ~* \.mpd$) {
        add_header Cache-Control "public, max-age=3, stale-while-revalidate=10";
        add_header Content-Type "application/dash+xml";
    }
    
    # Token verification
    auth_request /verify-token;
    auth_request_set $token_user $upstream_http_x_user_id;
    
    # Range request support
    slice $slice_range;
    proxy_cache_key $scheme$proxy_host$uri$slice_range;
}
```

### Multi-CDN Stratejisi

```
Multi-CDN Load Balancing:
┌─────────────────────────────────────────────────────┐
│                                                     │
│  ┌──────────┐    ┌──────────┐    ┌──────────┐     │
│  │ DNS      │───▶│ GeoDNS   │───▶│ CDN      │     │
│  │ Resolver │    │ Router   │    │ Selector │     │
│  └──────────┘    └──────────┘    └────┬─────┘     │
│                                       │           │
│  CDN Selection:                      │           │
│  ├── Performance-based: latency     ▼           │
│  ├── Cost-based: price per GB                   │
│  ├── Availability: health checks                │
│  └── Failover: automatic switch                 │
│                                                     │
│  Providers:                                        │
│  ├── Primary: CloudFront (global)                │
│  ├── Secondary: Cloudflare (DDoS protection)     │
│  ├── Tertiary: Fastly (real-time purge)          │
│  └── Self-hosted: Nginx (internal)               │
└─────────────────────────────────────────────────────┘
```

### Rate Limiting

```
Rate Limiting Strategy:
┌──────────────────┬────────────────────────────────────┐
│ API Endpoint     │ Limit                              │
├──────────────────┼────────────────────────────────────┤
│ /stream/*        │ 100 req/min per IP                │
│ /podcast/*       │ 50 req/min per IP                 │
│ /metadata/*      │ 200 req/min per IP                │
│ /media/upload    │ 10 req/min per user               │
│ /search/*        │ 30 req/min per IP                 │
└──────────────────┴────────────────────────────────────┘

Rate Limit Headers:
X-RateLimit-Limit: 100
X-RateLimit-Remaining: 87
X-RateLimit-Reset: 1726848060
Retry-After: 30
```

## Kod / Konfigürasyon

### CDN Manager Konfigürasyonu

```yaml
cdn_manager:
  primary:
    provider: "cloudfront"
    distribution_id: "E1234567890ABC"
    origin_domain: "origin.coremusic.io"
    cache_behavior:
      default_ttl: 86400
      max_ttl: 604800
      min_ttl: 0
      
  secondary:
    provider: "cloudflare"
    zone_id: "abc123def456"
    cache_level: "aggressive"
    
  self_hosted:
    provider: "nginx"
    origin_url: "https://origin.coremusic.io"
    
  token_auth:
    enabled: true
    secret: "${CDN_TOKEN_SECRET}"
    algorithm: "hmac-sha256"
    expiry_seconds: 3600
    
  failover:
    enabled: true
    health_check_interval: 30
    failure_threshold: 3
    
  monitoring:
    metrics: true
    alert_on_error: true
    log_level: "info"
```

### Nginx Edge Cache Konfigürasyonu

```yaml
nginx_edge:
  cache:
    path: "/var/cache/nginx/media"
    levels: "1:2"
    keys_zone: "media_cache:100m"
    max_size: "10g"
    inactive: "30d"
    use_temp_path: "off"
    
  proxy_cache:
    valid: "200 7d"
    valid_not_found: "1m"
    stale: "error timeout updating http_500 http_502 http_503 http_504"
    lock: "on"
    lock_timeout: "5s"
    
  upstream:
    servers:
      - "origin1.coremusic.io:443"
      - "origin2.coremusic.io:443"
    keepalive: 32
    health_check:
      interval: 10
      fails: 3
      passes: 2
```

## Bağımlılıklar

| Bağımlılık | Versiyon | Kullanım |
|------------|----------|----------|
| Nginx | 1.25.x | CDN edge server |
| CloudFront | 2.x | AWS CDN |
| Cloudflare | API v4 | CDN & DDoS protection |
| Fastly | API v1 | Real-time CDN |
| Redis | 7.x | Token cache, rate limiting |
| MySQL | 8.0.x | Origin metadata |

## Durum: Uygulama

CDN entegrasyonu aktif geliştirme aşamasındadır. Temel edge caching ve token authentication tamamlanmıştır.

| Özellik | Durum |
|---------|-------|
| Nginx Edge Cache | Tamamlandı |
| Token Authentication | Tamamlandı |
| CloudFront Integration | Tamamlandı |
| Multi-CDN Failover | Tamamlandı |
| Cache Invalidation | Tamamlandı |
| Rate Limiting | Tamamlandı |
| Origin Shield | Tamamlandı |
| Real-time Purge | Geliştirme |
| CDN Analytics | Planlama |
