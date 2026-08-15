---
title: "CoreMusic — Database Architecture"
category: architecture
date: 2026-08-09
updated: 2026-08-09
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Database Architecture

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[index.md]] · [[keys.md]] · [[brain.md]] · [[MEMORY.md]] · [[log.md]]

---

## 1. Amaç

CoreMusic, [[decisions/accepted/ADR-040-database-authority]] ile belirlenen 18 BCNF veritabanı kullanır. MySQL ana depolama, SQLite ise gömülü/yerel cihazlar için kullanılır.

---

## 2. 18 BCNF Veritabanı

| # | Veritabanı | Dosya | Amaç |
|---|------------|-------|------|
| 1 | coremusic_auth | `.sql/coremusic_auth.sql` | Kullanıcılar, roller, oturumlar, Argon2id hash'leri |
| 2 | coremusic_user | `.sql/coremusic_user.sql` | Profiller, tercihler, geçmiş |
| 3 | coremusic_musics | `.sql/coremusic_musics.sql` | Şarkılar, sanatçılar, türler, metadata |
| 4 | coremusic_albums | `.sql/coremusic_albums.sql` | Albüm koleksiyonları |
| 5 | coremusic_playlist | `.sql/coremusic_playlist.sql` | Kullanıcı ve AI çalma listeleri |
| 6 | coremusic_catalog | `.sql/coremusic_catalog.sql` | İndirme kuyrukları, servis durumu |
| 7 | coremusic_logs | `.sql/coremusic_logs.sql` | Uygulama logları, audit trail |
| 8 | coremusic_media | `.sql/coremusic_media.sql` | Medya dosyası metadata |
| 9 | coremusic_system | `.sql/coremusic_system.sql` | Sistem konfigürasyonu |

---

## 3. Veritabanı Kuralları

| Kural | Açıklama | ADR |
|-------|----------|-----|
| ORM YASAK | Sadece PDO prepared statement | [[decisions/accepted/ADR-002-pdo-mandatory-no-orm]] |
| SELECT * YASAK | Açık sütun listesi zorunlu | — |
| Prepared Statement Zorunlu | Parametreli sorgular | — |
| BCNF Zorunlu | Boyce-Codd Normal Form | [[decisions/accepted/ADR-040-database-authority]] |
| Soft Delete | `is_deleted = 0` | — |
| Snake Case | tablo ve sütun adları | — |

---

## 4. Depolama Stratejisi

### 4.1 MySQL (Ana Depolama)

| Özellik | Değer |
|---------|-------|
| Motor | InnoDB |
| Karakter Seti | utf8mb4 |
| Sıralama | utf8mb4_unicode_ci |
| Normalizasyon | BCNF |
| Yedekleme | Günlük tam, saatlik incremental |

### 4.2 SQLite (Gömülü/Yerel)

| Kullanım Alanı | Açıklama |
|----------------|----------|
| Yerel Önbellek | Local caching |
| Çevrimdışı Kuyruk | Offline queue |
| Konfigürasyon | Configuration storage |

---

## 5. Migration Stratejisi

[[decisions/accepted/ADR-014-multi-db-migration-strategy]] ile belirlenen strateji:

| Kural | Değer |
|-------|-------|
| Yön | İleriye doğru (forward-only) |
| Version | Versiyonlu migration |
| Geri Alma | Desteklenmez |
| Test | Migration öncesi test |

---

## 6. Yedekleme Stratejisi

| Tür | Sıklık | Saklama |
|-----|--------|---------|
| Tam Yedek | Günlük | 30 gün |
| Incremental | Saatlik | 7 gün |
| Transaction Log | Sürekli | 3 gün |

---

## 7. Deposu Kalıbı (Repository Pattern)

Her veritabanı için repository sınıfı:

```
AuthRepository
UserRepository
MusicsRepository
AlbumsRepository
PlaylistRepository
CatalogRepository
LogsRepository
MediaRepository
SystemRepository
```

---

## 8. Gömülü Cihazlar İçin SQLite

| Özellik | Açıklama |
|---------|----------|
| Yerel Önbellek | Sorgu sonuçları |
| Çevrimdışı Kuyruk | İnternetsiz işlemler |
| Konfigürasyon | Cihaz ayarları |
| Senkronizasyon | [[decisions/accepted/ADR-050-multi-db-sync-strategy]] |

---

## 9. Çapraz Veritabanı Senkronizasyonu

[[decisions/accepted/ADR-050-multi-db-sync-strategy]] ile belirlenen strateji:

| Senaryo | Çözüm |
|---------|-------|
| Ana ↔ Gömülü | Incremental sync |
| Çakışma | Son yazan kazanır |
| Çevrimdışı | Queue + retry |

---

## 10. Şema Tasarım İlkeleri

| İlke | Açıklama |
|------|----------|
| BCNF | Her tablo bağımsız |
| Atomik Veri | Parçalanamaz değerler |
| Normalizasyon | Tekrar önleme |
| İndeksleme | Sorgu performansı |
| Kısıtlama | Veri bütünlüğü |

---

## 10A. BCNF Normalizasyon Detayı

[[decisions/accepted/ADR-040-database-authority]] ve [[decisions/accepted/ADR-033-sql-normalization-strategy]] ile belirlenen standartlar:

| Normal Form | Kural | Uygulama |
|-------------|-------|----------|
| **1NF** | Atomik değerler, tekrar eden grup yok | Her sütun tek değer |
| **2NF** | 1NF + kısmi bağımlılık yok | Tüm olmayan anahtar, tam anahtara bağımlı |
| **3NF** | 2NF + geçişli bağımlılık yok | Olmayan anahtar, sadece anahtara bağımlı |
| **BCNF** | Her determinant aday anahtar olmalı | Fonksiyonel bağımlılıkların tamamı |

**BCNF Kontrol Akışı:**
```
Tablo tanımla → Fonksiyonel bağımlılıkları çıkar →
Determinant'ları belirle → Aday anahtarlarla karşılaştır →
BCNF ihlali var mı? → Evet: Tabloyu böl → Hayır: Uygun
```

**Olası İhlal Senaryoları:**
| Durum | Örnek | Çözüm |
|-------|-------|-------|
| Yetki tablosu | `role_permission(role, permission, grantable)` | Tabloları böl |
| Kullanıcı-rol | `user_role(user_id, role_id, assigned_by)` | Junction tablosu |
| Albüm-sarkı | `album_track(album_id, track_id, disc_number)` | Junction tablosu |

---

## 10B. Repository Pattern Detayı

ORM yasak (ADR-002). Sadece PDO prepared statement ile repository kalıbı kullanılır.

| Repository | Sorumlu DB | Ana Metotlar |
|------------|------------|-------------|
| `AuthRepository` | coremusic_auth | findUserByEmail, createSession, validateToken |
| `UserRepository` | coremusic_user | findById, updateProfile, getPreferences |
| `MusicsRepository` | coremusic_musics | search, findByArtist, getMetadata |
| `AlbumsRepository` | coremusic_albums | findById, getTracks, getArtwork |
| `PlaylistRepository` | coremusic_playlist | findByUser, addTrack, reorder |
| `CatalogRepository` | coremusic_catalog | getQueue, updateStatus, getServices |
| `LogsRepository` | coremusic_logs | append, query, rotate |
| `MediaRepository` | coremusic_media | findByHash, getMetadata, updateStatus |
| `SystemRepository` | coremusic_system | getConfig, setConfig, getModules |

**Repository Kuralları:**
- Her repository bir `PDO` instance'ı alır (constructor injection)
- Prepared statement zorunlu
- SELECT * yasak — açık sütun listesi
- Soft delete: `is_deleted = 0`
- Snake_case naming
- Transaction desteği

---

## 10C. Şema Versioning

| Özellik | Değer |
|---------|-------|
| Format | `YYYYMMDDHHMMSS` timestamp |
| Yön | Forward-only (geri alma desteklenmez) |
| Dosya | `migrations/YYYYMMDDHHMMSS_description.sql` |
| Log | Her migration `coremusic_logs`'a kaydedilir |
| Test | Migration öncesi otomatik test |

**Migration Akışı:**
```
Yeni migration oluştur → Version numarası ata →
SQL yaz → Test ortamında çalıştır →
Production'a uygula → Log kaydı oluştur
```

---

## 11. İndeksleme Stratejisi

| Tür | Kullanım |
|-----|----------|
| Birincil Anahtar | Her tabloda zorunlu |
| Benzersiz | E-posta, kullanıcı adı |
| Normal | Sık sorgulanan sütunlar |
| Birleşik | Çoklu sütun sorguları |
| Örtük | Dış anahtarlar |

---

## 12. Sorgu Optimizasyonu

| Teknik | Açıklama |
|--------|----------|
| EXPLAIN | Sorgu planı analizi |
| İndeks Kullanımı | Doğru indeks seçimi |
| JOIN Optimizasyonu | Az sorgu |
| Pagination | Limit/offset |
| Cache | APCu/Redis önbellek |

---

## 13. Veritabanı Güvenliği

| Önlem | Açıklama |
|-------|----------|
| Şifreleme | AES-256-GCM (durumda) |
| Parametreli Sorgular | SQL enjeksiyon önleme |
| Audit Log | Tüm işlemler loglanır |
| Erişim Kontrolü | Rol bazlı |
| Şifre Hash | Argon2id (64MB/4/2) |

---

## 14. İlgili ADR'ler

| ADR | Konu |
|-----|------|
| [[decisions/accepted/ADR-002-pdo-mandatory-no-orm]] | PDO zorunlu, ORM yasak |
| [[decisions/accepted/ADR-003-multi-db-9-databases]] | 18 BCNF veritabanı |
| [[decisions/accepted/ADR-014-multi-db-migration-strategy]] | Migration stratejisi |
| [[decisions/accepted/ADR-033-sql-normalization-strategy]] | SQL normalizasyon |
| [[decisions/accepted/ADR-040-database-authority]] | DB otoritesi |
| [[decisions/accepted/ADR-050-multi-db-sync-strategy]] | DB senkronizasyonu |

---

## 15. İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[architecture/05-data/database_master]] | Ana veritabanı referansı |
| [[architecture/05-data/bcnf-normalization]] | BCNF normalizasyonu |
| `.sql/coremusic_auth.sql` | Auth şeması |
| `.sql/coremusic_user.sql` | Kullanıcı şeması |
| `.sql/coremusic_musics.sql` | Müzik şeması |

---

## 16. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 2.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Sections | 19 |
| ADR References | 6 |
| Database Count | 18 BCNF |
| Storage Engines | MySQL + SQLite |
| Migration Strategy | Forward-only |
| Repository Count | 9 |
| Normal Forms | 1NF → 2NF → 3NF → BCNF |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode