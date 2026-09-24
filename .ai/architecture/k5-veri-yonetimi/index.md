---
title: "K5 Veri Yönetimi Katmanı - Genel Bakış"
layer: K5
category: "Veri Yönetimi"
date: 2026-09-20
version: 1.0.0
status: stable
components: 78
dependencies: [K0, K1, K2]
---

# K5 Veri Yönetimi Katmanı

## Genel Bakış

K5 Veri Yönetimi katmanı, COREMUSIC Hi-Fi audio sisteminin tüm veri depolama, önbellek, yedekleme ve veri güvenliği süreçlerini yöneten merkezi veri altyapısıdır. MySQL 8.0 tabanlı 18 BCNF veritabanı, Redis 7 önbellek, APCu bellek içi depolama ve dosya sistemi organizasyonunu kapsar.

## Mimari Konum

K5 katmanı, K0-K4 katmanlarının veri ihtiyaçlarını karşılarkan, K6 (Servis) katmanına veri erişim API'ları sağlar.

```
K5 Veri Yönetimi
├── mysql-18-database/        # 18 BCNF, 156 tablo
├── redis-cache/              # Redis 7, session/query cache
├── apcu-memory/              # APCu in-memory cache
├── file-system-storage/      # Medya kütüphane yapısı
├── sqlite-local/             # Yerel/offline depolama
├── backup-strategy/          # Otomatik yedekleme
├── data-security/            # Şifreleme ve GDPR
├── migration-strategy/       # Schema versiyonlama
├── database-registry/        # Veritabanı kaydı
├── cache-strategy/           # Çoklu seviye cache
├── connection-pooling/       # Bağlantı havuzu
└── data-lifecycle/           # Veri yaşam döngüsü
```

## Veri Depolama Tipleri

| Depolama Tipi | Teknoloji | Kullanım | Boyut |
|---------------|-----------|----------|-------|
| Relational DB | MySQL 8.0 | Ana veri depolama | 500GB |
| Cache | Redis 7 | Session, query cache | 16GB |
| Memory Cache | APCu | PHP opcache | 2GB |
| File System | ext4/XFS | Medya dosyaları | 10TB |
| Local DB | SQLite | Offline mod | 10GB |
| Object Storage | MinIO | Backup | 20TB |

## Veri Akışı

```
┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│  K4 AI      │────▶│  K5 Veri    │────▶│  K6 Servis  │
│  Services   │     │  Yönetimi   │     │  Layer      │
└─────────────┘     └─────────────┘     └─────────────┘
                           │
                           ▼
                    ┌─────────────┐
                    │  K7-K20     │
                    │  Upper      │
                    │  Layers     │
                    └─────────────┘
```

## BCNF Veritabanı Yapısı

### 18 Veritabanı

| # | Veritabanı | Amaç | Tablo Sayısı |
|---|-----------|------|--------------|
| 1 | coremusic_users | Kullanıcı verileri | 12 |
| 2 | coremusic_music | Müzik metadata | 18 |
| 3 | coremusic_audio | Ses dosyaları | 8 |
| 4 | coremusic_playlists | Playlist yönetimi | 10 |
| 5 | coremusic_analytics | Kullanım analitikleri | 15 |
| 6 | coremusic_ai | AI model verileri | 12 |
| 7 | coremusic_social | Sosyal özellikler | 14 |
| 8 | coremusic_store | Mağaza verileri | 16 |
| 9 | coremusic_payments | Ödeme işlemleri | 11 |
| 10 | coremusic_content | İçerik yönetimi | 9 |
| 11 | coremusic_search | Arama indeksleri | 8 |
| 12 | coremusic_notifications | Bildirimler | 7 |
| 13 | coremusic_sessions | Oturum yönetimi | 6 |
| 14 | coremusic_logs | Sistem logları | 10 |
| 15 | coremusic_config | Konfigürasyon | 5 |
| 16 | coremusic_integrations | Entegrasyonlar | 8 |
| 17 | coremusic_local | Yerel veriler | 6 |
| 18 | coremusic_cache | Cache metadata | 4 |
| | **Toplam** | | **156** |

## Cache Stratejisi

### Çoklu Seviye Cache

```
Level 1: APCu (PHP Memory) - TTL: 60s
    ↓ Miss
Level 2: Redis (Distributed) - TTL: 1800s
    ↓ Miss
Level 3: MySQL (Persistent) - No TTL
    ↓ Miss
Level 4: File System (Media) - No TTL
```

### Cache Invalidation

```yaml
strategies:
  write_through: true
  write_back: false
  cache_aside: true
  
invalidation:
  event_based: true
  time_based: true
  manual: true
  
ttl_strategy:
  session: 1800
  query: 300
  static: 86400
  media: 604800
```

## Performans Metrikleri

| Metrik | Hedef | Mevcut |
|--------|-------|--------|
| DB Query Latency | < 10ms | 7ms |
| Cache Hit Ratio | > 95% | 97.2% |
| Connection Pool Usage | < 80% | 65% |
| Backup RPO | < 1 saat | 15 dakika |
| Migration Downtime | 0 saniye | 0 saniye |
| Data Retention | 7 yıl | 7 yıl |

## Güvenlik

- AES-256 encryption at rest
- TLS 1.3 encryption in transit
- GDPR compliance (right to erasure)
- Data classification (public/internal/confidential/secret)
- Audit logging for all data access
- Role-based access control (RBAC)

## Bağımlılıklar

| Bağımlılık | Version | Amaç |
|------------|---------|------|
| MySQL | 8.0 | Relational database |
| Redis | 7.x | Distributed cache |
| PHP APCu | 8.3+ | In-memory cache |
| MinIO | Latest | Object storage |
| SQLite | 3.44+ | Local storage |

## Durum: Implementasyon

K5 Veri Yönetimi katmanı **stable** durumdadır. Tüm 18 veritabanı production-ready. Cache strategy ve connection pooling aktif. Migration strategy zero-downtime ile uygulanmaktadır.
