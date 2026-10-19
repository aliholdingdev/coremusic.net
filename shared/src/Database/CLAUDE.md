---
title: "CoreMusic — shared/src/Database Bağlam"
type: context
folder: "shared/src/Database"
category: layer0-infrastructure
date: 2026-09-21
updated: 2026-09-21
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
---

# Database — CLAUDE.md (Detaylı)

**Zorunlu Bağlantılar:** · [[../CLAUDE.md]] · [[../../.ai/architecture/k5-veri-yonetimi]]

---

## 1. Bağlam

18 BCNF veritabanı (156 tablo) yönetimi. **ORM kesinlikle yasaktır** (ADR-002). Tüm DB erişimi PDO prepared statement ile yapılır. DatabaseManager tek bağlantı noktasıdır.

---

## 2. Mevcut Durum

| Durum | Değer |
|-------|-------|
| Toplam dosya | 2 PHP dosyası |
| ADR | ADR-002 (PDO mandatory), ADR-003 (9→18 DB), ADR-022 (hardened security) |
| DB motoru | MySQL 9 |
| Toplam DB | 18 BCNF |
| Toplam tablo | 156 |

### 2.1 Dosya Envanteri

| Dosya | Amaç |
|-------|------|
| `DatabaseManager.php` | Tek DB bağlantı noktası, connection factory |
| `DatabaseRegistry.php` | 18 DB kaydı, connection mapping |

---

## 3. 18 BCNF Veritabanı

| # | Veritabanı | Amaç | Tablo |
|---|------------|------|-------|
| 1 | `coremusic_auth` | Users, roles, sessions, tokens, credential vault | 13 |
| 2 | `coremusic_user` | Profiles, preferences, history, favorites | 7 |
| 3 | `coremusic_musics` | Songs, artists, genres, lyrics, files | 22 |
| 4 | `coremusic_albums` | Album collections, discs, stats | 5 |
| 5 | `coremusic_playlist` | User and AI playlists | 5 |
| 6 | `coremusic_catalog` | Reference data | 8 |
| 7 | `coremusic_logs` | Audit trail, analytics | 22 |
| 8 | `coremusic_media` | Device sync, metadata | 8 |
| 9 | `coremusic_system` | Settings, config, cache | 17 |
| 10 | `coremusic_social` | Comments, shares, rooms | 9 |
| 11 | `coremusic_wireless` | WiFi + Bluetooth | 5 |
| 12 | `coremusic_ai` | Preferences, recommendations | 6 |
| 13 | `coremusic_api` | API keys, rate limits | 4 |
| 14 | `coremusic_cms` | Pages, blog, FAQs | 8 |
| 15 | `coremusic_download` | Download queue, cache | 4 |
| 16 | `coremusic_neva` | EQ presets, DSP settings | 4 |
| 17 | `coremusic_studio` | Studio sessions, tracks | 6 |
| 18 | `coremusic_patch` | Schema versions, patches | 3 |
| | **TOPLAM** | | **156** |

---

## 4. Kod Standartları

| Kural | Detay |
|-------|-------|
| ORM yasak | Sadece PDO prepared statement (ADR-002) |
| SELECT * yasak | Açık sütun listesi zorunlu |
| BCNF zorunlu | 18 veritabanında normalizasyon |
| Soft delete | `is_deleted = 0` (hard delete yasak) |
| Snake case | Tablo ve sütun adlandırma |
| Prepared statement | Tüm SQL sorgularında zorunlu |
| Transaction | ACID uyumlu işlemler |
| Connection | DatabaseManager dışında DB bağlantısı açılmaz |

---

## 5. DatabaseManager Kullanımı

```php
// Doğru kullanım
$db = DatabaseManager::getInstance();
$conn = $db->getConnection('coremusic_auth');
$stmt = $conn->prepare("SELECT id, email FROM users WHERE id = :id");
$stmt->execute([':id' => $userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Yanlış kullanım (yasak)
$result = $pdo->query("SELECT * FROM users"); // SELECT * yasak
$result = $pdo->query("SELECT * FROM users WHERE id = $id"); // Prepared statement yok
```

---

## 6. Komşu İlişkileri

| Yön | Hedef | İlişki |
|-----|-------|--------|
| Parent | [[../CLAUDE.md]] | Shared library üst bağlam |
| Tüketen | [[../../auth.coremusic.net/CLAUDE.md]] | Auth DB erişimi |
| Tüketen | [[../../home.coremusic.net/CLAUDE.md]] | Music DB erişimi |
| Referans | [[../../.ai/architecture/k5-veri-yonetimi]] | Veri mimarisi |
| ADR | [[../../.ai/decisions/accepted/ADR-002-pdo-mandatory-no-orm]] | ORM yasağı |
| ADR | [[../../.ai/decisions/accepted/ADR-003-multi-db-9-databases]] | 18 DB |
| ADR | [[../../.ai/decisions/accepted/ADR-022-database-hardened-security]] | DB güvenlik |

---

## 7. Yasaklar

| # | Yasak | Neden |
|---|-------|-------|
| 1 | ORM (Eloquent, Doctrine) | ADR-002 yasağı |
| 2 | `SELECT *` | SQL injection riski |
| 3 | Hard delete | Soft delete zorunlu |
| 4 | DatabaseManager dışında DB bağlantısı | Connection pool bozulması |
| 5 | Prepared statement olmadan sorgu | SQL injection |
| 6 | Global DB constant | Config-based connection |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-21
**Mode:** Red Team · Human Mode · Truth Mode
