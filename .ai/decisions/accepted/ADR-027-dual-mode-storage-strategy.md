---
type: adr
category: infrastructure
title: "ADR-027: Dual-Mode Storage Strategy"
date: 2026-05-15
updated: 2026-08-08
status: frozen
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# ADR-027: Dual-Mode Storage Strategy

**Status:** Frozen (değiştirilemez)
**Kategorisi:** Infrastructure
**İlgili Agent:** [[.agents/data-engineer]]

---

## 1. Amaç

Bu ADR, CoreMusic platformunun hibrit (dual-mode) depolama stratejisini tanımlar. Dosya sistemi, veritabanı ve cache katmanlarının rolleri, veri akışı, senkronizasyon ve performans optimizasyonlarını kapsar. Tüm veri saklama ve erişim işlemleri bu stratejiye göre yönetilir.

---

## 2. Bağlam

CoreMusic farklı türde verileri yönetir:
- Medya dosyaları (FLAC, MP3, kapak görselleri)
- Metadata (şarkı bilgileri, albüm detayları)
- Kullanıcı verileri (tercihler, geçmiş)
- Sistem konfigürasyonu
- Session verileri
- Log kayıtları
- Cache verileri

Her veri türü için en uygun depolama yöntemi farklıdır. Tek bir depolama yöntemi tüm gereksinimleri karşılayamaz.

---

## 3. Karar

CoreMusic'te **hibrit depolama** (dosya sistemi + veritabanı + cache) kullanılacak. Her veri türü için en uygun depolama yöntemi seçilecek ve katmanlar arası veri akışı tanımlanacaktır.

### 3.1 Temel Prensipler

| Prensip | Açıklama | ADR |
|---------|----------|-----|
| Dual-mode | Dosya + DB + Cache | Bu ADR |
| Doğru araç | Her veri türü için uygun method | Bu ADR |
| Senkronizasyon | Katmanlar arası tutarlılık | Bu ADR |
| Performans | Cache ile hızlı erişim | Bu ADR |
| Güvenlik | Hassas veri koruması | [[ADR-022-database-hardened-security]] |

---

## 4. Teknik Detaylar

### 4.1 Depolama Katmanları

```
┌─────────────────────────────────────────┐
│ Layer 3: Application Cache (APCu/Redis)  │
│ Sıcak veri, session, frequently used     │
├─────────────────────────────────────────┤
│ Layer 2: Database (MySQL 9 BCNF)         │
│ Metadata, kullanıcı verisi, config       │
├─────────────────────────────────────────┤
│ Layer 1: File System                     │
│ Medya dosyaları, kapak görselleri        │
├─────────────────────────────────────────┤
│ Layer 0: Cold Storage (NAS/Archive)      │
│ Eski dosyalar, backup                    │
└─────────────────────────────────────────┘
```

### 4.2 Veri Türü → Depolama Eşlemesi

| Veri Türü | Birincil | İkincil | Cache | ADR |
|-----------|---------|---------|-------|-----|
| Medya dosyası (FLAC/MP3) | Dosya sistemi | — | — | Bu ADR |
| Kapak görselleri | Dosya sistemi | CDN | APCu | Bu ADR |
| Şarkı metadata | MySQL | — | APCu | [[ADR-040-database-authority]] |
| Albüm bilgisi | MySQL | — | APCu | [[ADR-040-database-authority]] |
| Kullanıcı profili | MySQL | — | APCu | [[ADR-040-database-authority]] |
| Kullanıcı tercihleri | MySQL | — | APCu | [[ADR-040-database-authority]] |
| Çalma listesi | MySQL | — | APCu | [[ADR-040-database-authority]] |
| Session verisi | MySQL | — | APCu | [[ADR-011-session-management]] |
| Auth token | Credential vault | MySQL | — | [[ADR-034-credential-vault-normalization]] |
| Log kayıtları | MySQL | Dosya | — | [[ADR-004-multi-domain-spa]] |
| Sistem config | MySQL | — | APCu | [[ADR-040-database-authority]] |
| Cache verisi | APCu/Redis | — | — | Bu ADR |
| Download queue | MySQL | — | — | [[ADR-026-download-service-architecture]] |
| EQ preset | Dosya (JSON) | MySQL | APCu | [[ADR-025-professional-eq-system]] |
| Plugin verisi | Dosya sistemi | — | — | Bu ADR |

### 4.3 Dosya Sistemi Yapısı

#### 4.3.1 Ana Dizin Yapısı

```
/var/www/coremusic/
├── media/                          ← Medya dosyaları
│   ├── songs/                      ← FLAC/MP3 dosyaları
│   │   ├── {artist}/{album}/       ← Albüm bazlı dizinleme
│   │   │   ├── 01-song.flac
│   │   │   ├── 02-song.flac
│   │   │   └── cover.jpg
│   │   └── ...
│   ├── covers/                     ← Kapak görselleri
│   │   ├── songs/{song_id}.jpg
│   │   ├── albums/{album_id}.jpg
│   │   └── artists/{artist_id}.jpg
│   └── temp/                       ← Geçici dosyalar
├── cache/                          ← Dosya tabanlı cache
│   ├── thumbnails/                 ← Küçük resimler
│   └── metadata/                   ← Metadata cache
├── logs/                           ← Log dosyaları
│   ├── app/
│   ├── access/
│   └── error/
├── backup/                         ← Yedekleme
│   ├── daily/
│   ├── weekly/
│   └── monthly/
└── uploads/                        ← Kullanıcı yüklemeleri
```

#### 4.3.2 Dosya İsimlendirme

| Tür | Format | Örnek |
|-----|--------|-------|
| Şarkı | `{sanatçı}-{albüm}-{sira}-{başlık}.flac` | `taylor-1989-01-shake.flac` |
| Kapak | `{tip}_{id}.{ext}` | `song_12345.jpg` |
| Metadata | `{type}_{id}.json` | `album_67890.json` |
| Log | `{type}_{YYYY-MM-DD}.log` | `app_2026-08-08.log` |

### 4.4 Veritabanı Stratejisi

#### 4.4.1 9 BCNF Veritabanı

| # | Veritabanı | Amaç | ADR |
|---|------------|------|-----|
| 1 | coremusic_auth | Users, roles, sessions | [[ADR-040-database-authority]] |
| 2 | coremusic_user | Profiles, preferences | [[ADR-040-database-authority]] |
| 3 | coremusic_musics | Songs, artists, genres | [[ADR-040-database-authority]] |
| 4 | coremusic_albums | Album collections | [[ADR-040-database-authority]] |
| 5 | coremusic_playlist | Playlists | [[ADR-040-database-authority]] |
| 6 | coremusic_catalog | Download queues | [[ADR-040-database-authority]] |
| 7 | coremusic_logs | Audit trail | [[ADR-040-database-authority]] |
| 8 | coremusic_media | Media metadata | [[ADR-040-database-authority]] |
| 9 | coremusic_system | System config | [[ADR-040-database-authority]] |

#### 4.4.2 DB Erişim Kuralları

| Kural | Değer | ADR |
|-------|-------|-----|
| ORM yasak | Sadece PDO prepared | [[ADR-002-pdo-mandatory-no-orm]] |
| SELECT * yasak | Explicit column list | [[ADR-002-pdo-mandatory-no-orm]] |
| BCNF zorunlu | 9 veritabanı normalizasyonu | [[ADR-040-database-authority]] |
| Soft delete | `is_deleted = 0` | [[ADR-040-database-authority]] |
| Snake_case | Tablo ve sütun adları | [[ADR-040-database-authority]] |

### 4.5 Cache Stratejisi

#### 4.5.1 Cache Katmanları

| Katman | Teknoloji | Omur | Kullanım |
|--------|-----------|------|----------|
| L1 (Hot) | APCu | Oturum sonu | Session, frequently used |
| L2 (Warm) | APCu | 1 saat | Metadata, config |
| L3 (Cool) | Dosya | 24 saat | Thumbnail, static |
| L4 (Cold) | Redis | 7 gün | Cross-server sync |

#### 4.5.2 Cache Politikası

```
Read-Through: Cache miss'de DB'den oku → Cache'e yaz
Write-Through: DB + Cache'e aynı anda yaz
Write-Behind: Cache'e yaz, sonra DB'ye async yaz
Eviction: LRU (Least Recently Used)
```

#### 4.5.3 Cache Namespace

| Namespace | İçerik | TTL | ADR |
|-----------|--------|-----|-----|
| `auth:` | Session token | 3600s | [[ADR-011-session-management]] |
| `user:` | Kullanıcı tercihleri | 1800s | [[ADR-040-database-authority]] |
| `music:` | Şarkı metadata | 3600s | [[ADR-040-database-authority]] |
| `album:` | Albüm bilgisi | 3600s | [[ADR-040-database-authority]] |
| `playlist:` | Çalma listesi | 1800s | [[ADR-040-database-authority]] |
| `config:` | Sistem config | 7200s | [[ADR-040-database-authority]] |
| `rate:` | Rate limit sayaçları | 60s | [[ADR-013-rate-limiting-apcu]] |
| `search:` | Arama sonuçları | 300s | — |

### 4.6 Veri Akışı

#### 4.6.1 Write Akışı

```
Uygulama yazma isteği
  → DB'ye yaz (transaction başlat)
    → Başarılı mı?
      → Evet → Cache'i invalidate et → Log yaz → Başarılı
      → Hayır → Rollback → Hata logla → Hata dön
```

#### 4.6.2 Read Akışı

```
Uygulama okuma isteği
  → Cache kontrolü (L1/L2)
    → Cache hit → Veriyi dön
    → Cache miss → DB'den oku
      → Cache'e yaz (TTL ile)
        → Veriyi dön
```

#### 4.6.3 Sync Akışı (Cross-Server)

```
Sunucu A'da değişiklik
  → DB'ye yaz
    → Redis'e publish (kanal: `db:{table}:{id}`)
      → Diğer sunucular subscribe olur
        → Cache'lerini invalidate eder
```

### 4.7 Senkronizasyon Mekanizması

#### 4.7.1 DB ↔ Cache Senkronizasyonu

| Durum | Mekanizma | Süre |
|-------|-----------|------|
| DB güncellemesi | Cache invalidation | Anlık |
| Cache süresi doldu | TTL-based expiry | Otomatik |
| Manuel flush | Admin komutu | Manuel |
| Servis restart | Cache rebuild | Restart sırasında |

#### 4.7.2 Dosya ↔ DB Senkronizasyonu

| Durum | Mekanizma | Süre |
|-------|-----------|------|
| Dosya yükleme | DB metadata yazma | Yükleme sırasında |
| Dosya silme | DB soft delete | Silme sırasında |
| Metadata değişikliği | DB güncelleme + dosya adı | Güncelleme sırasında |
| Dosya bozulması | DB flag + rebuild | Tespit anında |

### 4.8 Performans Optimizasyonları

#### 4.8.1 Dosya Sistemi Optimizasyonları

| Optimizasyon | Açıklama |
|-------------|----------|
| Dizin hiyerarşisi | `{sanatçı}/{albüm}/` ile dosya sayısı azaltma |
| Thumbnail | Küçük resimler ile orijinale erişimi azaltma |
| Lazy load | Dosya adedini azaltma |
| Compression | Metadata JSON sıkıştırma |

#### 4.8.2 DB Optimizasyonları

| Optimizasyon | Açıklama |
|-------------|----------|
| Index | Sorgu hızlandırma |
| Prepared statement | SQL compilation cache |
| Connection pool | Bağlantı yeniden kullanma |
| Read replica | Okuma yükü dağıtma |

#### 4.8.3 Cache Optimizasyonları

| Optimizasyon | Açıklama |
|-------------|----------|
| Warm-up | Servis başlangıcında cache doldurma |
| Pre-fetch | Beklenen verileri önceden çekme |
| Batch write | Çoklu yazma işleme |
| Compression | Büyük verileri sıkıştırma |

### 4.9 Backup Stratejisi

#### 4.9.1 Backup Türleri

| Tür | Sıklık | Saklama | Kapsam |
|-----|--------|---------|--------|
| Full backup | Haftalık | 1 ay | Tüm DB |
| Incremental | Günlük | 14 gün | Değişiklikler |
| Differential | 6 saatte bir | 3 gün | Son full'den beri |
| File backup | Günlük | 1 ay | Medya dosyaları |
| Log backup | Her saat | 7 gün | Transaction logları |

#### 4.9.2 Kurtarma Prosedürü

| Durum | Kurtarma | Hedef Süre |
|-------|----------|------------|
| DB silinmesi | Full restore | <30 dk |
| Dosya kaybı | File restore | <1 sa |
| Cache bozulması | Flush + rebuild | <5 dk |
| Servis çökmesi | PM2 restart | <1 dk |
| Tam kurtarma | Full backup restore | <2 sa |

### 4.10 Monitoring

#### 4.10.1 İzlenen Metrikler

| Metrik | Eşik | Aksiyon |
|--------|------|---------|
| DB boyutu | >10GB | Uyarı |
| Dosya sistemi kullanımı | >%80 | Uyarı |
| Cache hit ratio | <%80 | Optimizasyon |
| Sorgu süresi | >100ms | İyileştirme |
| Bağlantı sayısı | >100 | Pool artırma |
| Disk I/O | >80% | Optimizasyon |

#### 4.10.2 Monitoring Dashboard

```
┌─────────────────────────────────────────┐
│ Storage Monitoring Dashboard            │
├─────────────────────────────────────────┤
│ DB: 4.2GB/10GB │ Files: 120GB/500GB    │
├─────────────────────────────────────────┤
│ Cache Hit: 92% │ DB Queries: 45/s      │
├─────────────────────────────────────────┤
│ Backup: OK │ Last: 2026-08-08 02:00    │
├─────────────────────────────────────────┤
│ Alerts: 0 │ Warnings: 1               │
└─────────────────────────────────────────┘
```

#### 4.10.3 Alert Kanalları

| Kanal | Kullanım | Süre |
|-------|----------|------|
| Email | Günlük rapor | 24 saat |
| Slack | Gerçek zamanlı | Anlık |
| PagerDuty | CRITICAL | Anlık |
| Dashboard | Anlık durum | Sürekli |

### 4.11 Disaster Recovery

#### 4.11.1 Kurtarma Senaryoları

| Senaryo | Öncelik | Kurtarma | Hedef Süre |
|---------|---------|----------|------------|
| DB silinmesi | CRITICAL | Full restore | <30 dk |
| Dosya kaybı | HIGH | File restore | <1 sa |
| Cache bozulması | MEDIUM | Flush + rebuild | <5 dk |
| Servis çökmesi | HIGH | PM2 restart | <1 dk |
| Tam kurtarma | CRITICAL | Full backup | <2 sa |
| Disk hatası | HIGH | Swap disk | <15 dk |
| Network partition | MEDIUM | Failover | <5 dk |
| Data corruption | HIGH | Checksum verify | <1 sa |

#### 4.11.2 Recovery Time Objectives

| Metrik | Hedef |
|--------|-------|
| RTO (Recovery Time Objective) | <2 saat |
| RPO (Recovery Point Objective) | <1 saat |
| MTTR (Mean Time To Repair) | <30 dk |
| MTBF (Mean Time Between Failures) | >720 saat |

### 4.12 Capacity Planning

#### 4.12.1 Büyüme Tahminleri

| Yıl | DB Boyutu | Dosya Boyutu | Cache |
|-----|-----------|-------------|-------|
| 1 | 5GB | 200GB | 1GB |
| 2 | 10GB | 500GB | 2GB |
| 3 | 20GB | 1TB | 4GB |
| 5 | 50GB | 5TB | 8GB |

#### 4.12.2 Scaling Stratejisi

| Katman | Strateji | Tetikleyici |
|--------|----------|-------------|
| DB | Read replica | >100 query/s |
| Cache | Cluster | >2GB memory |
| File | NAS migration | >500GB |
| Backup | Remote storage | >100GB backup |

---

## 5. Yasak Örüntüleri

| ❌ Yasak | ✅ Doğru | Sonuc |
|----------|----------|-------|
| Tüm verileri dosyaya | Doğru araç seçimi | Performans düşüşü |
| Tüm verileri DB'ye | Hibrit yaklaşım | Dosya avantajı kaybı |
| Cache invalidation eksik | Write-through + invalidation | Tutarlısızlık |
| Hardcoded path | Konfigüre edilebilir | Taşınabilirlik |
| Sync file+DB ohne transaction | Transaction ile atomiklik | Veri kaybı |
| Backup eksik | Düzenli backup | Veri kaybı |
| Monitoring eksik | Aktif monitoring | Sorun tespit edilemez |
| Log'da hassas veri | `[REDACTED]` | Güvenlik açığı |
| ORM kullanımı | Raw PDO | SQL injection |
| SELECT * | Explicit columns | Veri sızıntısı |

---

## 6. Edge Cases

| Edge Case | Tetikleyici | Çözüm |
|-----------|-------------|-------|
| Cache-DB tutarsızlığı | Network partition | Reconciliation job |
| Dosya-DB tutarsızlığı | Yarım yükleme | Transaction + rollback |
| Cache stampede | Yüksek load | Mutex ile single load |
| Disk dolması | %100 kullanım | Eski dosya temizleme |
| DB connection pool exhausted | Çoklu istek | Pool artırma + queue |
| Backup başarısız | Disk hatası | Alternatif backup yeri |
| Cache eviction率 çok yüksek | TTL çok kısa | TTL ayarlama |
| Cross-server sync lag | Network gecikmesi | Async replication |
| Dosya bozulması | Disk hatası | Checksum + restore |
| Metadata conflict | Eşzamanlı güncelleme | Optimistic locking |

---

## 7. Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Transaction zorunlu — DB yazma işlemlerinde atomiklik | Veri tutarsızlığı |
| 2 | Cache invalidation zorunlu — DB değişikliğinde cache temizleme | Yanlış veri |
| 3 | Backup zorunlu — Günlük backup olmadan production yasak | Veri kaybı |
| 4 | Monitoring zorunlu — Disk, DB, cache izlenmeli | Sorun tespit edilemez |
| 5 | Log redaction — Hassas veri log'da `[REDACTED]` | Güvenlik açığı |
| 6 | ORM yasak — Sadece PDO prepared | SQL injection |
| 7 | BCNF zorunlu — 9 DB normalizasyon | Veri tekrarı |
| 8 | Soft delete — Fiziksel silme yasak | Geri alma imkanı |
| 9 | Namespace standardı — Cache namespace formatı | Cache collision |
| 10 | Recovery time — Kurtarma süresi <2 saat | Uzun kesinti |

---

## 8. İlgili ADR'ler

| ADR | Konu | İlişki |
|-----|------|--------|
| [[ADR-002-pdo-mandatory-no-orm]] | PDO mandatory | DB erişimi |
| [[ADR-003-multi-db-9-databases]] | 9 BCNF DB | Veritabanı yapısı |
| [[ADR-004-multi-domain-spa]] | Vault versiyonlama | Log yönetimi |
| [[ADR-011-session-management]] | Session yönetimi | Session storage |
| [[ADR-013-rate-limiting-apcu]] | Rate limiting | Cache kullanımı |
| [[ADR-022-database-hardened-security]] | DB güvenlik | Şifreleme |
| [[ADR-025-professional-eq-system]] | EQ sistemi | Preset depolama |
| [[ADR-026-download-service-architecture]] | Download servisi | Dosya yönetimi |
| [[ADR-034-credential-vault-normalization]] | Credential vault | Secret storage |
| [[ADR-040-database-authority]] | DB authority | 9 BCNF |

---

## 9. Çapraz Referanslar

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 3 Karar | [[ADR-040-database-authority]] | DB yapısı |
| § 4.2 Eşleme | [[ADR-003-multi-db-9-databases]] | Multi-DB |
| § 4.3 Dosya | [[architecture/l0-infrastructure]] | Altyapı |
| § 4.5 Cache | [[ADR-013-rate-limiting-apcu]] | APCu kullanımı |
| § 4.8 Optimizasyon | [[ADR-006-performance-targets]] | Performans |
| § 4.9 Backup | [[ADR-022-database-hardened-security]] | Güvenlik |
| § 5 Yasak | [[ADR-002-pdo-mandatory-no-orm]] | ORM yasağı |
| § 6 Edge | [[ADR-007-cache-namespace]] | Cache stratejisi |
| § 7 Guardrails | [[CLAUDE.md]] §7 | Hard guardrails |
| § 8 ADR | [[ADR-026-download-service-architecture]] | Dosya yönetimi |

---

## 10. Sözlük

| Terim | Tanım |
|-------|-------|
| **Dual-Mode** | İki farklı depolama yöntemi kullanımı |
| **Hibrit** | Farklı depolama katmanlarının birleşimi |
| **APCu** | APC User Cache — PHP önbellek sistemi |
| **Redis** | Anahtar-değer veritabanı (cache) |
| **BCNF** | Boyce-Codd Normal Form |
| **PDO** | PHP Data Objects — DB erişim katmanı |
| **Transaction** | Atomik veritabanı işlemi |
| **Cache invalidation** | Cache verisinin geçersiz kılınması |
| **Cache stampede** | Çoklu istek aynı anda cache miss |
| **LRU** | Least Recently Used — Cache eviction |
| **Read-Through** | Cache miss'de DB'den okuma |
| **Write-Through** | DB + Cache'e eşzamanlı yazma |
| **Soft delete** | Fiziksel silme yerine flag ile silme |
| **Reconciliation** | Tutarlılık kontrol mekanizması |
| **Backup** | Veri yedekleme |
| **Recovery** | Veri kurtarma |
| **Monitoring** | Sistem izleme |

---

## 11. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| Version | 1.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Sections | 11 |
| SSOT Authority | ADR-027 Dual-Mode Storage Strategy |
| Last Updated | 2026-08-08 |
| ADR References | 10 |
| Cross References | 10 |
| Edge Cases | 10 |
| Hard Guardrails | 10 |
| Forbidden Patterns | 10 |
| Glossary Terms | 17 |
| Storage Layers | 4 (Cache, DB, File, Cold) |
| Data Type Mappings | 15 |
| Cache Namespaces | 8 |
| Backup Types | 5 |
| Monitoring Metrics | 6 |
| Recovery Scenarios | 5 |

---

## 12. Authority

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
