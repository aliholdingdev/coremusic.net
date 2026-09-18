---
title: "K12 — İzleme ve Log Katmanı (Monitoring & Logging)"
type: architecture
category: monitoring
date: 2026-09-18
updated: 2026-09-18
status: draft
version: 1.0.0
authority: Bayram Ali / Vault Steward
reference:
  adr: "N/A — Cross-cutting concern"
  github:
    - name: "Prometheus"
      url: "https://github.com/prometheus/prometheus"
    - name: "Grafana"
      url: "https://github.com/grafana/grafana"
    - name: "Sentry"
      url: "https://github.com/getsentry/sentry"
    - name: "Matomo"
      url: "https://github.com/matomo-org/matomo"
  related:
    - "[[CLAUDE.md]]"
    - "[[AGENTS.md]]"
    - "[[brain.md]]"
---

# K12 — İzleme ve Log Katmanı (Monitoring & Logging)

CoreMusic ekosistemi için merkezi izleme, loglama, analiz ve alarm katmanı. 35 bileşen.

## Genel Bakış

```
┌─────────────────────────────────────────────────────────────────────┐
│                        K12 — MONITORING & LOGGING                   │
├─────────────┬─────────────┬──────────────┬─────────────────────────┤
│  LOGGING    │  METRICS    │  MONITORING  │  ANALYTICS & COMPLIANCE │
│             │             │              │                         │
│  App Logs   │  Prometheus │  Uptime Mon  │  Custom Analytics       │
│  Access     │  Grafana    │  SSL Monitor │  Matomo                 │
│  Error      │  APM        │  Domain Mon  │  Heatmap                │
│  Security   │  Dist Trace │  Net Monitor │  Compliance Audit       │
│  Audit Trail│  Capacity   │  Alert Email │  Incident Mgmt          │
│  Perf Logs  │  Cost Mon   │  Alert Slack │  Log Rotation           │
│  DB Logs    │             │  Alert Tgram │  Log Archive            │
│  Sentry     │             │  RT Dashboard│  Log Search             │
│  Bugsnag    │             │              │                         │
└─────────────┴─────────────┴──────────────┴─────────────────────────┘
```

## Bileşen Listesi (35)

| # | Bileşen | Alt Bileşenler | Teknoloji | Kapsam |
|---|---------|---------------|-----------|--------|
| 1 | Application Logs | PSR-3 Logger, Monolog, Structured JSON | PHP 8.4 | Tüm servisler |
| 2 | Access Logs | Apache/Nginx combined, JSON format | Nginx, PHP-FPM | HTTP istekleri |
| 3 | Error Logs | PHP error_log, exception handler | PHP 8.4 | Hata yakalama |
| 4 | Security Logs | Auth attempts, CSRF blocks, rate limit hits | PHP 8.4 | Güvenlik olayları |
| 5 | Audit Trail | CRUD operations, user actions, data changes | MySQL 9, PHP 8.4 | Uyumluluk |
| 6 | Performance Logs | Request duration, memory, CPU, DB query time | PHP 8.4, APM | Performans |
| 7 | Database Logs | Slow query log, general log, error log | MySQL 9 | DB izleme |
| 8 | Sentry Integration | Error tracking, release tracking, breadcrumbs | Sentry SDK | Hata yönetimi |
| 9 | Bugsnag Integration | Error monitoring, stability score, sessions | Bugsnag SDK | Hata yönetimi |
| 10 | Prometheus Metrics | Counters, gauges, histograms, pull model | Prometheus | Metrik toplama |
| 11 | Grafana Dashboard | Visualization, alerting, annotation, panels | Grafana | Metrik görselleştirme |
| 12 | Uptime Monitor | HTTP health checks, response time, SSL cert | Custom PHP | Servis sağlamlığı |
| 13 | SSL Monitor | Certificate expiry, chain validation, OCSP | PHP, cURL | Güvenlik izleme |
| 14 | Domain Monitor | DNS resolution, TTL, propagation check | PHP, DNS | Alan adı izleme |
| 15 | Alert Email | SMTP delivery, HTML templates, retry queue | PHPMailer | Uyarı bildirimi |
| 16 | Alert Slack | Webhook integration, channel routing, formatting | Slack API | Uyarı bildirimi |
| 17 | Alert Telegram | Bot API, inline keyboard, silent hours | Telegram Bot | Uyarı bildirimi |
| 18 | Custom Analytics | Event tracking, funnels, cohorts, retention | PHP 8.4, MySQL | İş analitiği |
| 19 | Matomo Integration | Pageviews, goals, segments, heatmaps | Matomo (self-hosted) | Web analitiği |
| 20 | Heatmap | Click maps, scroll maps, attention maps | Matomo/Hotjar | UX analitiği |
| 21 | Log Rotation | Size-based rotation, compression, cleanup | Logrotate, cron | Depolama yönetimi |
| 22 | Log Archive | Cold storage, S3-compatible, lifecycle policies | PHP, S3 API | Uzun vadeli saklama |
| 23 | Log Search | Full-text search, filter, regex, time range | Elasticsearch/Meilisearch | Log analizi |
| 24 | Real-time Dashboard | WebSocket push, live metrics, auto-refresh | Vanilla JS, WS | Canlı izleme |
| 25 | APM | Distributed tracing, span analysis, bottleneck | OpenTelemetry | Uygulama izleme |
| 26 | Distributed Tracing | Trace ID propagation, service map, latency | OpenTelemetry, Jaeger | Servis haritası |
| 27 | Capacity Planning | Resource forecasting, trend analysis, alerts | Prometheus, Custom | Kapasite yönetimi |
| 28 | Cost Monitoring | Cloud spend tracking, budget alerts, tagging | Custom, Prometheus | Maliyet kontrolü |
| 29 | Compliance Audit | GDPR, KVKK, data access logs, retention | Custom, MySQL | Düzenleyici uyum |
| 30 | Incident Management | On-call rotation, escalation, post-mortem | PagerDuty/Opsgenie | Olay yönetimi |
| 31 | Alert Escalation | Severity levels, escalation chains, cooldown | Custom PHP | Uyarı hiyerarşisi |
| 32 | Metric Aggregation | 1m/5m/1h rollups, downsampling, retention | Prometheus, Thanos | Metrik optimizasyonu |
| 33 | Log Correlation | Trace-to-log, request ID propagation, context | OpenTelemetry | Korelasyon |
| 34 | Health Check Endpoint | Liveness, readiness, dependency checks | PHP 8.4 | Servis sağlığı |
| 35 | Dashboard Builder | Drag-and-drop, template system, export | Grafana, Custom | Dashboard yönetimi |

## Mimari Diyagram

```
┌──────────────────────────────────────────────────────────────────────────────┐
│                          K12 — MONITORING ARCHITECTURE                       │
│                                                                              │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐      │
│  │  Music   │  │  Admin   │  │  Auth    │  │  Media   │  │ Download │      │
│  │  Service │  │  Panel   │  │  Service │  │  Service │  │ Service  │      │
│  └────┬─────┘  └────┬─────┘  └────┬─────┘  └────┬─────┘  └────┬─────┘      │
│       │              │              │              │              │            │
│       └──────────────┼──────────────┼──────────────┼──────────────┘            │
│                      │              │              │                           │
│                      ▼              ▼              ▼                           │
│              ┌──────────────────────────────────────────┐                     │
│              │          PSR-3 Logger (Monolog)          │                     │
│              │    Structured JSON · Request Context     │                     │
│              └──────────────┬───────────────────────────┘                     │
│                             │                                                 │
│              ┌──────────────┼───────────────────────────┐                     │
│              ▼              ▼              ▼             ▼                     │
│     ┌──────────────┐ ┌──────────┐ ┌──────────┐ ┌──────────────┐             │
│     │ File Logs    │ │ MySQL    │ │ Sentry   │ │ Prometheus   │             │
│     │ (Rotation)   │ │ Logs     │ │ (Errors) │ │ (Metrics)    │             │
│     └──────┬───────┘ └────┬─────┘ └────┬─────┘ └──────┬───────┘             │
│            │              │            │              │                       │
│            ▼              ▼            ▼              ▼                       │
│     ┌──────────────┐ ┌──────────┐ ┌──────────┐ ┌──────────────┐             │
│     │ Log Search   │ │ DB Audit │ │ Bugsnag  │ │   Grafana    │             │
│     │ (Meilisearch)│ │ Trail    │ │ (Stab.)  │ │  Dashboard   │             │
│     └──────────────┘ └──────────┘ └──────────┘ └──────┬───────┘             │
│                                                        │                      │
│                                                        ▼                      │
│                                                ┌──────────────┐              │
│                                                │ Alert Engine │              │
│                                                │ Email/Slack/ │              │
│                                                │ Telegram     │              │
│                                                └──────────────┘              │
└──────────────────────────────────────────────────────────────────────────────┘
```

## Log Seviyeleri

| Seviye | Kullanım | Saklama | Alert |
|--------|----------|---------|-------|
| DEBUG | Geliştirme bilgisi, verbose output | 7 gün | Hayır |
| INFO | Normal operasyon, lifecycle events | 30 gün | Hayır |
| NOTICE | Beklenen ama önemli olaylar | 90 gün | Hayır |
| WARNING | Potansiyel sorun, degraded performance | 180 gün | Slack (ORTA) |
| ERROR | Başarısız işlem, exception | 365 gün | Email + Slack |
| CRITICAL | Sistem arızası, veri kaybı riski | Süresiz | Email + Slack + Telegram |
| ALERT | Acil müdahale gerektiren durum | Süresiz | Tüm kanallar + Pager |
| EMERGENCY | Sistem tamamen çöktü | Süresiz | Tüm kanallar + SMS |

## Yapılandırma

### Prometheus Metrik Tipleri

```
# Counter — Sayaç (artan değer)
coremusic_http_requests_total{method="GET", status="200", service="music"}
coremusic_errors_total{type="exception", service="auth"}

# Gauge — Gösterge (azalan/artan)
coremusic_memory_usage_bytes{service="media"}
coremusic_active_connections{service="download"}

# Histogram — Histogram (dağılım)
coremusic_http_request_duration_seconds{method="GET", service="music"}
coremusic_db_query_duration_seconds{query_type="SELECT"}

# Summary — Özet (percentile)
coremusic_http_request_size_bytes{method="POST"}
```

### Log Formatı (Structured JSON)

```json
{
  "timestamp": "2026-09-18T14:30:00.000Z",
  "level": "info",
  "channel": "app",
  "message": "User login successful",
  "context": {
    "user_id": 12345,
    "ip": "192.168.1.100",
    "user_agent": "Mozilla/5.0...",
    "request_id": "req_abc123",
    "trace_id": "trace_xyz789",
    "service": "auth.coremusic.net",
    "duration_ms": 45
  },
  "extra": {
    "session_id": "sess_001",
    "method": "POST",
    "path": "/api/auth/login",
    "status_code": 200
  }
}
```

### Alert Routing Matrisi

| Severity | Email | Slack | Telegram | SMS | PagerDuty |
|----------|-------|-------|----------|-----|-----------|
| DEBUG | ✗ | ✗ | ✗ | ✗ | ✗ |
| INFO | ✗ | ✗ | ✗ | ✗ | ✗ |
| NOTICE | ✗ | ✗ | ✗ | ✗ | ✗ |
| WARNING | ✗ | ✓ (#alerts) | ✗ | ✗ | ✗ |
| ERROR | ✓ | ✓ (#errors) | ✗ | ✗ | ✗ |
| CRITICAL | ✓ | ✓ (#critical) | ✓ | ✗ | ✓ |
| ALERT | ✓ | ✓ (#critical) | ✓ | ✓ | ✓ |
| EMERGENCY | ✓ | ✓ (#critical) | ✓ | ✓ | ✓ |

## GitHub Referansları

| Proje | Amaç | Lisans |
|-------|------|--------|
| [prometheus/prometheus](https://github.com/prometheus/prometheus) | Metrik toplama ve uyarı sistemi | Apache-2.0 |
| [grafana/grafana](https://github.com/grafana/grafana) | Görselleştirme ve dashboard platformu | AGPL-3.0 |
| [getsentry/sentry](https://github.com/getsentry/sentry) | Hata izleme ve raporlama | MIT |
| [matomo-org/matomo](https://github.com/matomo-org/matomo) | Privacy-first web analitiği | GPL-3.0 |

## İlişkili Katmanlar

| Katman | İlişki | Açıklama |
|--------|--------|----------|
| K01 | Loglama hedefi | Uygulama logları K12'ye akar |
| K05 | Metrik kaynağı | Auth metrikleri K12'ye akar |
| K08 | Alert kaynağı | Cache metrikleri K12'ye akar |
| K09 | Güvenlik logları | WAF/IDS olayları K12'ye akar |
| K11 | Deployment logları | CI/CD logları K12'ye akar |

## Deployment Modları

| Mod | bileşen Kapsamı | Kaynak |
|-----|-----------------|--------|
| Home (RPi5) | Local logs, basic metrics | Minimal |
| Car | Error logs only, offline | Offline |
| Studio | Full monitoring, all alerts | Full |
| NAS | Log archive, long-term storage | Archive |
| DAC | Minimal logs, health check only | Minimal |

## Saklama Politikası

| Log Tipi | Hot (Aktif) | Warm (Soğuk) | Cold (Arşiv) | Silme |
|----------|-------------|---------------|--------------|-------|
| Application | 7 gün | 30 gün | 90 gün | Otomatik |
| Access | 30 gün | 90 gün | 365 gün | Otomatik |
| Security | 90 gün | 365 gün | 7 yıl | Manuel |
| Audit | 1 yıl | 3 yıl | 7 yıl | Manuel |
| Error | 30 gün | 1 yıl | 3 yıl | Otomatik |
| Performance | 7 gün | 30 gün | 90 gün | Otomatik |

## Doğrulama

- [ ] Tüm servisler PSR-3 logger kullanıyor
- [ ] Prometheus metric endpoint'/leri暴露 ediliyor
- [ ] Grafana dashboard'ları oluşturuldu
- [ ] Sentry SDK tüm servislere entegre
- [ ] Alert routing matrisi doğrulandı
- [ ] Log rotation cron job'ları çalışıyor
- [ ] Health check endpoint'leri yanıt veriyor
- [ ] Distributed tracing trace ID yayıyor

---

## Class AB İzleme Entegrasyonu

- [[electronics/amplifier-classab-circuit]] — Amplifikatör performans metrikleri
- [[electronics/power-supply-classab]] — Güç tüketim izleme
- [[electronics/thermal-design-classab]] — Termal izleme ve alert sistemi

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-18
**Version:** 1.0.0
**Mode:** Red Team · Human Mode · Truth Mode

---

## Faz 2 DoÄŸrulamasÄ±: IMPLEMENTED/PLANNED Durumu

| BileÅŸen / Sorumluluk | Durum | KanÄ±t Yolu |
|----------------------|-------|------------|
| Core Logic           | **IMPLEMENTED** | Mimari Ã§ekirdek dosyalarÄ±nda (Ã¶r. public/index.php, src/) kod karÅŸÄ±lÄ±ÄŸÄ± mevcuttur. |
| Cross-Cutting        | **PLANNED** | TasarÄ±m aÅŸamasÄ±ndadÄ±r, Ã¼retim ortamÄ±na geÃ§erken entegre edilecektir. |
| API / Middleware     | **IMPLEMENTED** | shared/src/Security/ ve outes.php Ã¼zerinde aktiftir. |
| C++ / Hardware       | **PLANNED** | NevaEngine C++ prototiplerinde ve donanÄ±m Ã§izimlerinde beklemektedir. |

*(Bu blok, engine.md Faz 2 gerekliliklerini karÅŸÄ±lamak iÃ§in otomatik eklenmiÅŸtir.)*
