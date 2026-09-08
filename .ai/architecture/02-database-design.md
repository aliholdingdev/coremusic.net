---
title: "Database Design — 11 Mantıksal Veritabanı"
type: architecture
category: data
date: 2026-09-03
updated: 2026-09-03
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
reference:
  authority: ".ai/architecture/02-database-design.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/brain.md · .ai/architecture/05-data/database_master.md"
---

# Database Design — 11 Mantıksal Veritabanı

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[brain.md]] · [[05-data/database_master.md]]

---

## 1. Amaç

CoreMusic platformu için tek bir MySQL 9 sunucusu üzerinde, mikroservis odaklı **11 ayrı mantıksal veritabanı** (logical schema) tasarımı. Her veritabanı bağımsız bir iş alanını temsil eder ve BCNF normalizasyonuna tam uyumludur.

---

## 2. Tasarım İlkeleri

| İlke | Açıklama |
|------|----------|
| **BCNF Zorunlu** | Tüm tablolar Boyce-Codd Normal Form kurallarına tam uyumlu |
| **Snake Case** | İstisnasız tüm tablo, sütun, indeks ve constraint isimleri |
| **Soft Delete** | `is_deleted TINYINT(1) NOT NULL DEFAULT 0` her tabloda zorunlu |
| **UUID Primary Key** | `CHAR(36)` formatında, hem uygulama hem MySQL tarafında üretilebilir |
| **Prepared Statement** | Tüm sorgular prepared statement olmalı |
| **SELECT * Yasak** | Sadece açık sütun listesi |
| **ORM Yasak** | Sadece PDO prepared statement |

---

## 3. Hibrit UUID Stratejisi

### 3.1 UUID Üretim Mekanizması

| Katman | Üretim Yöntemi | Açıklama |
|--------|----------------|----------|
| **Uygulama (PHP)** | `Ramsey\Uuid\Uuid::uuid7()` | Sıralı, time-based, çakışma riski yok |
| **MySQL (DB)** | `UUID()` veya `UUID_TO_BIN(UUID(), 1)` | Fallback olarak kullanılabilir |

### 3.2 UUID Formatı

```sql
-- CHAR(36) formatı (insan-okunabilir)
'550e8400-e29b-41d4-a716-446655440000'

-- Veya BINARY(16) formatı (daha verimli)
-- Uygulama katmanında CHAR(36) → BINARY(16) dönüşümü yapılır
```

### 3.3 Çakışma Önleme

- UUID v7 time-based olduğu için çakışma ihtimali yoktur
- Ek garanti olarak `UNIQUE` constraint eklenir
- Uygulama katmanında ve DB tarafında bağımsız üretim desteklenir

---

## 4. 11 Mantıksal Veritabanı

| # | Veritabanı | Sorumluluk | Tablo Sayısı |
|---|------------|------------|-------------|
| 1 | `coremusic_auth` | Kimlik doğrulama, roller, izinler, session, token | 9 |
| 2 | `coremusic_user` | Kullanıcı profilleri, tercihler | 2 |
| 3 | `coremusic_media` | Medya dosyaları, etiketler | 2 |
| 4 | `coremusic_playlist` | Çalma listeleri, liste öğeleri | 2 |
| 5 | `coremusic_library` | Kullanıcı kütüphaneleri, favoriler | 2 |
| 6 | `coremusic_activity` | Dinleme geçmişi, indirme, olaylar | 5 |
| 7 | `coremusic_notification` | Bildirimler, bildirim ayarları | 2 |
| 8 | `coremusic_device` | Kayıtlı cihazlar, cihaz oturumları | 2 |
| 9 | `coremusic_search` | Arama indeksi, arama önerileri | 2 |
| 10 | `coremusic_config` | Sistem yapılandırması, özellik bayrakları | 2 |
| 11 | `coremusic_analytics` | Analitik veriler, performans metrikleri | 4 |
| | **TOPLAM** | | **34** |

---

## 5. Veritabanı Detayları

### 5.1 coremusic_auth — Kimlik Doğrulama

**Amaç:** Kullanıcı hesapları, roller, izinler, oturumlar, token yönetimi.

| Tablo | Amaç | BCNF Açıklaması |
|-------|------|-----------------|
| `users` | Kullanıcı hesapları | Her kullanıcı benzersiz e-posta ile tanımlanır |
| `roles` | Sistem rolleri | Rol adı benzersiz, bağımsız varlık |
| `user_roles` | Kullanıcı-rol eşleme | Composite PK (user_id, role_id) |
| `permissions` | Sistem izinleri | İzin adı benzersiz, modül+eylem çifti |
| `role_permissions` | Rol-izin eşleme | Composite PK (role_id, permission_id) |
| `refresh_tokens` | JWT yenileme tokenları | Token benzersiz, kullanıcıya bağlı |
| `auth_audit_log` | Kimlik doğrulama logları | Olay bazlı, bağımsız kayıt |
| `user_sessions` | Aktif oturumlar | Session token benzersiz |
| `password_resets` | Şifre sıfırlama talepleri | Token benzersiz, süreli |

### 5.2 coremusic_user — Kullanıcı Profilleri

**Amaç:** Kullanıcı kişisel bilgileri ve tercihleri.

| Tablo | Amaç | BCNF Açıklaması |
|-------|------|-----------------|
| `user_profiles` | Kullanıcı profilleri | Kullanıcı başına tek profil |
| `user_preferences` | Kullanıcı tercihleri | Kullanıcı başına tek tercih seti |

### 5.3 coremusic_media — Medya Yönetimi

**Amaç:** Medya dosyaları ve etiket yönetimi.

| Tablo | Amaç | BCNF Açıklaması |
|-------|------|-----------------|
| `media_files` | Medya dosya kayıtları | Dosya hash benzersiz |
| `media_tags` | Medya etiketleri | Etiket-değer çifti, bağımsız |

### 5.4 coremusic_playlist — Çalma Listeleri

**Amaç:** Çalma listesi yönetimi ve sıra takibi.

| Tablo | Amaç | BCNF Açıklaması |
|-------|------|-----------------|
| `playlists` | Çalma listeleri | Kullanıcıya ait, benzersiz isim |
| `playlist_items` | Liste öğeleri | Composite PK (playlist_id, position) |

### 5.5 coremusic_library — Kütüphane ve Favoriler

**Amaç:** Kullanıcı kütüphaneleri ve favori yönetimi.

| Tablo | Amaç | BCNF Açıklaması |
|-------|------|-----------------|
| `user_libraries` | Kullanıcı kütüphaneleri | Kullanıcı-kütüphane ilişkisi |
| `user_favorites` | Kullanıcı favorileri | Kullanıcı-medya ilişkisi, benzersiz |

### 5.6 coremusic_activity — Aktivite ve Olaylar

**Amaç:** Dinleme geçmişi, indirme yönetimi, olay takibi.

| Tablo | Amaç | BCNF Açıklaması |
|-------|------|-----------------|
| `listening_history` | Dinleme geçmişi | Kullanıcı-medya-zaman üçlüsü |
| `download_queue` | İndirme kuyruğu | Sıralı, durum bazlı |
| `download_history` | İndirme geçmişi | Tamamlanan indirmeler |
| `play_events` | Oynatma olayları | Başlat/durdur/atlama olayları |
| `search_events` | Arama olayları | Arama sorgusu ve sonuçları |

### 5.7 coremusic_notification — Bildirimler

**Amaç:** Bildirim yönetimi ve tercihleri.

| Tablo | Amaç | BCNF Açıklaması |
|-------|------|-----------------|
| `notifications` | Kullanıcı bildirimleri | Okunma durumu takibi |
| `notification_settings` | Bildirim tercihleri | Kullanıcı başına tek ayar seti |

### 5.8 coremusic_device — Cihaz Yönetimi

**Amaç:** Kayıtlı cihazlar ve oturum takibi.

| Tablo | Amaç | BCNF Açıklaması |
|-------|------|-----------------|
| `registered_devices` | Kayıtlı cihazlar | Cihaz parmak izi benzersiz |
| `device_sessions` | Cihaz oturumları | Cihaz-bağlantı ilişkisi |

### 5.9 coremusic_search — Arama Motoru

**Amaç:** Arama indeksi ve önerileri.

| Tablo | Amaç | BCNF Açıklaması |
|-------|------|-----------------|
| `search_index` | Arama indeksi | Full-text arama için optimize |
| `search_suggestions` | Arama önerileri | Popüler arama terimleri |

### 5.10 coremusic_config — Sistem Yapılandırması

**Amaç:** Sistem ayarları ve özellik bayrakları.

| Tablo | Amaç | BCNF Açıklaması |
|-------|------|-----------------|
| `system_config` | Sistem ayarları | Anahtar-değer çifti, bağımsız |
| `feature_flags` | Özellik bayrakları | Özellik bazlı aktif/pasif |

### 5.11 coremusic_analytics — Analitik

**Amaç:** Performans metrikleri ve analitik veriler.

| Tablo | Amaç | BCNF Açıklaması |
|-------|------|-----------------|
| `daily_stats` | Günlük istatistikler | Tarih bazlı, bağımsız |
| `performance_metrics` | Performans metrikleri | Zaman serisi verisi |
| `error_logs` | Hata kayıtları | Hata bazlı, bağımsız |
| `audit_logs` | Denetim kayıtları | Eylem bazlı, bağımsız |

---

## 6. İlişki Haritası

```
coremusic_auth.users
  ├──► coremusic_user.user_profiles.user_id
  ├──► coremusic_user.user_preferences.user_id
  ├──► coremusic_auth.user_roles.user_id
  ├──► coremusic_auth.user_sessions.user_id
  ├──► coremusic_auth.refresh_tokens.user_id
  ├──► coremusic_library.user_libraries.user_id
  ├──► coremusic_library.user_favorites.user_id
  ├──► coremusic_activity.listening_history.user_id
  ├──► coremusic_activity.download_queue.user_id
  ├──► coremusic_activity.download_history.user_id
  ├──► coremusic_activity.play_events.user_id
  ├──► coremusic_activity.search_events.user_id
  ├──► coremusic_notification.notifications.user_id
  ├──► coremusic_notification.notification_settings.user_id
  ├──► coremusic_device.registered_devices.user_id
  ├──► coremusic_device.device_sessions.user_id
  └──► coremusic_analytics.audit_logs.user_id

coremusic_media.media_files
  ├──► coremusic_media.media_tags.media_id
  ├──► coremusic_library.user_favorites.media_id
  ├──► coremusic_playlist.playlist_items.media_id
  └──► coremusic_activity.listening_history.media_id

coremusic_playlist.playlists
  └──► coremusic_playlist.playlist_items.playlist_id
```

---

## 7. İsimlendirme Standartları

| Öğe | Format | Örnek |
|-----|--------|-------|
| Veritabanı adı | `coremusic_[alan]` | `coremusic_auth` |
| Tablo adı | snake_case, çoğul | `user_roles` |
| Sütun adı | snake_case | `created_at` |
| PK sütunu | `id CHAR(36)` | `id CHAR(36) NOT NULL` |
| FK sütunu | `[tablo]_id` | `user_id`, `role_id` |
| Unique constraint | `uk_[tablo]_[sütun]` | `uk_users_email` |
| Index | `idx_[tablo]_[sütun]` | `idx_users_email` |
| Composite index | `idx_[tablo]_[sütun1]_[sütun2]` | `idx_user_roles_user_role` |
| Soft delete | `is_deleted` | `is_deleted TINYINT(1) DEFAULT 0` |
| Timestamp | `created_at`, `updated_at` | `TIMESTAMP DEFAULT CURRENT_TIMESTAMP` |

---

## 8. Cross-DB Join Stratejisi

MySQL'de farklı veritabanları arasında Foreign Key tanımlanamaz. Bu nedenle:

| Stratejim | Açıklama |
|-----------|----------|
| **Application-Level Join** | PHP/PDO katmanında JOIN yapılır |
| **Service-Level Join** | Servis katmanında veri birleştirilir |
| **Cache-Based Join** | Sık kullanılan ilişkiler cache'lenir |
| **Eventual Consistency** | Bazı ilişkiler async senkronize edilir |

---

## 9. Performans Optimizasyonu

| Teknik | Açıklama |
|--------|----------|
| **İndeksleme** | Tüm FK alanları + arama alanları indeksli |
| **Composite Index** | Sık sorgulanan bileşik alanlar |
| **Soft Delete Index** | `is_deleted` içeren bileşik indeksler |
| **Covering Index** | Sık sorgulanan SELECT için covering index |
| **Partitioning** | Büyük tablolar için tarih bazlı partitioning |

---

## 10. Güvenlik Standartları

| Kural | Uygulama |
|-------|----------|
| **Prepared Statement** | Tüm SQL sorguları prepared statement |
| **SELECT * Yasak** | Sadece açık sütun listesi |
| **ORM Yasak** | Sadece PDO prepared statement |
| **Secret Management** | Hassas veriler `.env` veya credential vault'ta |
| **Audit Trail** | Tüm kritik işlemler `audit_logs`'a kaydedilir |
| **Soft Delete** | Hard delete yasak, `is_deleted` ile silme |

---

## 11. Cross References

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 2 Tasarım İlkeleri | [[CLAUDE.md]] §7 | Hard Guardrails |
| § 3 UUID Stratejisi | [[brain.md]] §10 | PHP Security |
| § 4 Veritabanları | [[05-data/database_master.md]] | 18 BCNF tanımı |
| § 7 İsimlendirme | [[brain.md]] §18 | Coding Standards |
| § 10 Güvenlik | [[ADR-022-database-hardened-security]] | DB güvenlik |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-03
**Mode:** Red Team · Human Mode · Truth Mode
