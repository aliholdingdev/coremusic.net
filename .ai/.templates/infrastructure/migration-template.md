---
title: "CoreMusic — Database Migration Template"
type: migration-template
category: infrastructure
date: 2026-09-06
updated: 2026-09-23
version: 2.0.0
status: active
authority: SSOT
---

# CoreMusic — Database Migration Template

**Veritabanı:** MySQL 9 · **BCNF zorunlu (ADR-040)** · **Sorumlu Agent:** Data Engineer

**Zorunlu Bağlantılar:** [[.templates/index]] · [[../CLAUDE.md]] · [[../../AGENTS.md]] · [[../query/Query-Template]] · [[../testing/phpunit-template]]

---

## 1. Amaç

Bu şablon, CoreMusic veritabanı migration ve seed işlerini standartlaştırmaktır: migration dosyası formatı (disk kanıtı: `$queries` dizisi içinde ham SQL), BCNF kontrol listesi, soft delete/timestamp zorunlulukları ve çalıştırma adımlarını tek iskelette sunar. **Guardrail #16:** yeni migration işi bu şablondan üretilmek ZORUNLUDUR.

| Karar | ADR | Şablona gömülü karşılığı |
|-------|-----|--------------------------|
| Forward-only migration (revert yasak) | ADR-014 | §4.1 #1 + §4.3 |
| BCNF zorunlu | ADR-040 | §4.2 kontrol listesi |
| PDO + prepared statement, ORM yasak | ADR-002 | §3.4 çalıştırma notu |
| Social OAuth şeması (örnek migration) | ADR-072 · ADR-088 | §3.1 disk kanıtı |
| Soft delete + timestamp standardı | — | §3.2/§3.3 |

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `shared/database/migrations/*.php` (SQL sorgu dizisi formatı) | Uygulama sorguları → `[[../query/Query-Template]]` |
| `.ai/.sql/mysql/*.sql` — 18 BCNF şema referansı | CI/CD pipeline → `[[../infrastructure/github-actions-template]]` |
| Tablo/alan/index değişikliği, seed verisi | Donanım/firmware dokümanları |
| Veritabanı migration testleri | Uygulama katmanı testleri → `[[../testing/phpunit-template]]` |

- **Kullananlar:** Data Engineer (birincil), Backend Architect (ikincil), QA Engineer (migration testi).
- **Dosya tipi:** PHP migration dosyası (dizide ham SQL) + Markdown şablon dokümanı.
- **Migration aracı durumu:** `.ai/brain.md` bağımlılık listesinde `robmorgan/phinx` geçer; `shared/`, `auth.coremusic.net/`, `home.coremusic.net/` composer.json dosyalarında phinx GÖRÜNMEZ → araç seçimi **⚠️ VERIFICATION REQUIRED**. Diskte fiilen var olan format §3.1'dedir.

---

## 3. Mimari

Şablonun gövdesi: disk kanıtıyla doğrulanmış migration formatı, örnek migration, seed/query blokları ve çalıştırma adımları.

### 3.1 Disk Kanıtı — Migration Envanteri

| Yol (disk kanıtı) | Durum |
|-------------------|-------|
| `shared/database/migrations/oauth_connections_migration.php` | Mevcut — `oauth_connections` (coremusic_social DB, ADR-088 + ADR-072) |
| `shared/database/migrations/oauth_states_migration.php` | Mevcut — `oauth_states` |
| `shared/database/migrations/CLAUDE.md` | Klasör bağlam dosyası |
| `.ai/.sql/mysql/*.sql` | 18 şema: `coremusic_{ai,albums,api,auth,catalog,cms,download,logs,media,musics,neva,patch,playlist,social,studio,system,user,wireless}` |
| `database/migrations/` (kök) | Yok — migration kök dizini `shared/` altındadır |
| `vendor/bin/phinx` | Doğrulanmadı (composer.json'da phinx yok) → ⚠️ VERIFICATION REQUIRED |

**Format gerçeği:** mevcut migration dosyaları Phinx sınıfı değil, `$queries = [ "CREATE TABLE ...", ... ]` dizisidir ve dosya başında ADR referansları taşır (`@see [[decisions/accepted/ADR-...]]`). Yeni migration bu formata uyar.

### 3.2 Migration Şablonu (disk formatı)

```php
<?php declare(strict_types=1);

/**
 * CoreMusic — {{TABLE}} Migration ({{DATABASE_NAME}})
 *
 * ADR-040 (BCNF) + ADR-014 (forward-only) compliant.
 * Soft delete + timestamp zorunlu.
 *
 * @see [[decisions/accepted/ADR-040-{{ADR_SLUG}}]]
 */

$queries = [

// ============================================================
// {{TABLE}} — {{TABLE_PURPOSE}}
// ============================================================
"CREATE TABLE IF NOT EXISTS {{TABLE}} (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    {{COLUMN_1}} VARCHAR(255) NOT NULL,
    {{COLUMN_2}} INT UNSIGNED NOT NULL DEFAULT 0,
    {{COLUMN_3}} DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    status ENUM('active', 'passive') NOT NULL DEFAULT 'active',
    is_deleted TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    UNIQUE KEY uk_{{TABLE}}_{{COLUMN_1}} ({{COLUMN_1}}),
    INDEX idx_{{TABLE}}_status (status),
    INDEX idx_{{TABLE}}_deleted (is_deleted),
    INDEX idx_{{TABLE}}_created (created_at),
    INDEX idx_{{TABLE}}_updated (updated_at),
    KEY fk_{{TABLE}}_parent ({{COLUMN_2}})
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

// ============================================================
// İleriye dönük düzeltme (ADR-014: revert YOK, yeni satır eklenir)
// ============================================================
"ALTER TABLE {{TABLE}}
    ADD COLUMN {{COLUMN_4}} VARCHAR(64) NULL AFTER {{COLUMN_3}}",

];

return $queries;
```

### 3.3 Seed / Veri Yükleme Şablonu

```php
<?php declare(strict_types=1);

/**
 * CoreMusic — {{TABLE}} seed verisi (migration ile aynı format).
 * Kurulum verisi INSERT; üretim verisi asla bu dosyada tutulmaz.
 *
 * @see [[../CLAUDE.md]] — Guardrail #16
 */

$queries = [

"INSERT IGNORE INTO {{TABLE}}
    ({{COLUMN_1}}, {{COLUMN_2}}, {{COLUMN_3}}, status, created_at)
VALUES
    ('{{SEED_VALUE_1}}', 100, 99.99, 'active', NOW()),
    ('{{SEED_VALUE_2}}', 200, 149.99, 'active', NOW()),
    ('{{SEED_VALUE_3}}', 300, 199.99, 'passive', NOW())",

];

return $queries;
```

### 3.4 Çalıştırma Adımları

```bash
# 1) Migration dosyasını oluştur (mevcut format — §3.2)
#    hedef: shared/database/migrations/{{TABLE}}_migration.php

# 2) Şemayı referans şemalarla karşılaştır (18 dosya, §3.1)
#    .ai/.sql/mysql/coremusic_{{DB}}.sql

# 3) Test ortamında uygula ve doğrula
#    (araç seçimi ⚠️ VERIFICATION REQUIRED — composer.json'da phinx yok)

# 4) İlgili testleri çalıştır (§4.4)
vendor/bin/phpunit --testsuite Unit

# 5) Sorgu katmanını da güncelle — soft delete + prepared (Query-Template §4.1)
```

### 3.5 BCNF Şema Kalıbı (18 referans şemayla hizalı)

```sql
-- Örnek: normal form kontrolü için asgari kalıp
CREATE TABLE IF NOT EXISTS {{TABLE}} (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,   -- birincil anahtar
    {{COLUMN_1}} VARCHAR(255) NOT NULL,              -- aday anahtar adayı
    {{COLUMN_2}} INT UNSIGNED NOT NULL,              -- bağımlı olmayan alan
    {{COLUMN_3}} DECIMAL(10,2) NOT NULL,
    is_deleted TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    UNIQUE KEY uk_{{TABLE}}_{{COLUMN_1}} ({{COLUMN_1}})
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Kural: her tablo birinci anahtara sahiptir (id),
--       aday anahtar dışındaki alanlar tam fonksiyonel bağımlıdır,
--       yinelenen veri yoktur → BCNF (ADR-040).
```

### 3.6 İleriye Dönük Değişiklik Kalıbı (ADR-014)

| Değişiklik tipi | Doğru yaklaşım | Yasak yaklaşım |
|-----------------|----------------|----------------|
| Alan ekleme | Yeni `ALTER ... ADD COLUMN` satırı (§3.2) | Mevcut satırı düzenlemek |
| Alan kaldırma | Yeni `DROP COLUMN` satırı; önce `is_deleted` ile pasife alma | Migration'ı geri almak |
| Tip değişikliği | Yeni `ALTER ... MODIFY` satırı + veri doğrulama | Doğrudan overwrite |
| Index ekleme | Yeni `ADD INDEX` satırı | Mevcut satırı silip yeniden yazmak |
| Tablo yeniden adlandırma | Yeni tablo + veri kopyası + eskiyi pasife alma | `RENAME` ile sessiz kırılma |

### 3.7 Alan Tipi Rehberi

| Veri tipi | MySQL tipi | Kullanım | Dikkat |
|-----------|------------|----------|--------|
| Kimlik | `BIGINT UNSIGNED AUTO_INCREMENT` | Birincil anahtar | `id` zorunlu (§4.2) |
| Kısa metin | `VARCHAR(255)` | Başlık, ad, slug | `UNIQUE` ile |
| Uzun metin | `TEXT` / `JSON` | İçerik, profil verisi | Index alınmaz (prefix index hariç) |
| Sayı | `INT UNSIGNED` | Sayaç, FK | İşaretli gerekmedikçe UNSIGNED |
| Para | `DECIMAL(10,2)` | Fiyat, bakiye | `FLOAT` YASAK |
| Bayrak | `TINYINT(1)` | `is_deleted`, `is_active` | Varsayılan zorunlu |
| Zaman | `TIMESTAMP` | `created_at`, `updated_at` | `DEFAULT CURRENT_TIMESTAMP` |
| Sıralı enum | `ENUM(...)` | Durum, sağlayıcı | Değer listesi ADR'ye bağlı |
| GUID | `CHAR(36)` / `BINARY(16)` | UuidV7 | İndeks uzunluğuna dikkat |

### 3.8 Index Stratejisi Kalıbı

```sql
-- Sorgu kalıbı → index eşlemesi (Query-Template §3.1 ile hizalı)
-- 1) Birincil anahtar zaten cluster edilir
-- 2) WHERE / ORDER BY / JOIN sütunlarına composite index:

CREATE INDEX idx_{{TABLE}}_{{COLUMN_1}}_status_created
    ON {{TABLE}} ({{COLUMN_1}}, status, created_at DESC);

-- 3) Soft delete filtresi hemen her sorguda var → seçici tabloda ayrıca:
CREATE INDEX idx_{{TABLE}}_deleted_created
    ON {{TABLE}} (is_deleted, created_at DESC);

-- 4) Eşsizlik → iş kuralı koruması:
CREATE UNIQUE KEY uk_{{TABLE}}_{{COLUMN_1}} ({{COLUMN_1}});

-- 5) Uzun metin sütununa tam index YASAK; gerekirse prefix:
--    (sorgu kalıbı yoksa index eklemek yazma maliyeti getirir)
```

### 3.9 İleriye Taşıma (Roll-Forward) Veri Örneği

```php
<?php declare(strict_types=1);

/**
 * Yeni alanın mevcut veriden türetilmesi — forward-only (ADR-014).
 * Eski veri silinmez; türetilen alan yazılır, eski alan pasife alınır.
 */

$queries = [

// 1) Alanı ekle (boş bırak)
"ALTER TABLE {{TABLE}}
    ADD COLUMN {{COLUMN_4}} VARCHAR(64) NULL AFTER {{COLUMN_3}}",

// 2) Mevcut veriden türet (parti partici — büyük tabloda LIMIT'li döngü)
"UPDATE {{TABLE}}
    SET {{COLUMN_4}} = CONCAT('gen-', id)
    WHERE {{COLUMN_4}} IS NULL
      AND is_deleted = 0
    LIMIT 1000",

// 3) Eski alanı pasife al (silme DEĞİL — §3.6)
"ALTER TABLE {{TABLE}}
    MODIFY {{COLUMN_3}} DECIMAL(10,2) NULL",

];

return $queries;
```

### 3.10 Migration Test Kalıbı (`[[../testing/phpunit-template]]` ile)

```php
<?php declare(strict_types=1);

namespace CoreMusic\Tests\Unit\Database;

use PHPUnit\Framework\TestCase;

final class {{TABLE}}MigrationTest extends TestCase
{
    public function testQueriesArrayIsNonEmpty(): void
    {
        // Arrange
        $file = dirname(__DIR__, 3) . '/database/migrations/{{TABLE}}_migration.php';

        // Act
        $queries = require $file;

        // Assert
        $this->assertIsArray($queries);
        $this->assertNotEmpty($queries);
    }

    public function testCreateTableContainsBcnfColumns(): void
    {
        // Arrange
        $file = dirname(__DIR__, 3) . '/database/migrations/{{TABLE}}_migration.php';
        $queries = require $file;
        $ddl = implode("\n", $queries);

        // Act + Assert
        $this->assertStringContainsString('PRIMARY KEY', $ddl);
        $this->assertStringContainsString('is_deleted', $ddl);
        $this->assertStringContainsString('created_at', $ddl);
        $this->assertStringContainsString('updated_at', $ddl);
        $this->assertStringNotContainsString('DELETE FROM', $ddl);
    }

    public function testNamingIsSnakeCase(): void
    {
        // Arrange
        $file = dirname(__DIR__, 3) . '/database/migrations/{{TABLE}}_migration.php';
        $queries = require $file;
        $ddl = implode("\n", $queries);

        // Assert
        $this->assertMatchesRegularExpression('/CREATE TABLE IF NOT EXISTS [a-z0-9_]+/', $ddl);
    }
}
```

### 3.11 18 Referans Şema (`.ai/.sql/mysql/`)

| Dosya | Ana varlık (şema adı) | Kullanım |
|-------|----------------------|----------|
| `coremusic_auth.sql` | Kimlik/oturum | Auth migration hizası |
| `coremusic_user.sql` | Kullanıcı profili | Profil alanları |
| `coremusic_social.sql` | Sosyal/OAuth | `oauth_connections` (ADR-072/088) |
| `coremusic_api.sql` | API anahtarı/limit | Gateway migration |
| `coremusic_playlist.sql` | Çalma listesi | İlişki tabloları |
| `coremusic_albums.sql` | Albüm | Katalog |
| `coremusic_catalog.sql` | Katalog | Meta veri |
| `coremusic_media.sql` | Medya dosyası | Dosya meta |
| `coremusic_musics.sql` | Parça | Ses meta |
| `coremusic_download.sql` | İndirme | Lisans/limit |
| `coremusic_logs.sql` | Kayıt/audit | Audit trail |
| `coremusic_ai.sql` | AI katmanı | ADR-030 |
| `coremusic_neva.sql` | Neva Engine verisi | DSP/servo ⚠️ |
| `coremusic_patch.sql` | Yamalar | Firmware yama |
| `coremusic_studio.sql` | Stüdyo | Proje verisi |
| `coremusic_system.sql` | Sistem ayarı | Config |
| `coremusic_cms.sql` | İçerik | CMS |
| `coremusic_wireless.sql` | Kablosuz | WirelessConnect |

### 3.12 Yıkıcı Değişiklik Öncesi Kontrol

| # | Soru | Evet ise |
|---|------|----------|
| 1 | Sütun silinecek mi? | Önce `is_deleted` ile pasife al (§4.1 #3) |
| 2 | Tip daraltılacak mı? | Yeni alan + veri kopyası (§3.9) |
| 3 | Tablo birleşecek mi? | Yeni tablo + kopya + eskiyi arşivle |
| 4 | Index değişecek mi? | Yeni `ADD INDEX` satırı, sonra eskisini düşür |
| 5 | Veri > 1M satır mı? | Parti parti `UPDATE ... LIMIT` (§3.9) |
| 6 | Diğer 17 şema etkilenir mi? | Drift kontrolü (§6 #7) |

### 3.13 Migration → Sorgu Senkron Tablosu

| Migration değişikliği | Güncellenen sorgu | Şablon |
|-----------------------|-------------------|--------|
| Yeni alan (`{{COLUMN_4}}`) | SELECT açık sütun listesine eklenir | `[[../query/Query-Template]]` §3.1 |
| Yeni enum değeri | Filtre/sayfalama değeri | Query §3.1 |
| Index değişimi | ORDER BY/LIMIT planı | Query §3.1 |
| Soft delete sütunu | `is_deleted = 0` koşulu | Query §4.1 #3 |
| Tablo yeniden adlandırma | Tüm Repository sorguları | Query §5 |

### 3.14 Kilit (LOCK) ve Downtime Rehberi

| İşlem | Kilit davranışı | Beklenen süre ⚠️ | Öneri |
|-------|-----------------|------------------|-------|
| `ADD COLUMN` (nullable) | Metadata lock | kısa | Düşük saatte çalıştır |
| `ADD INDEX` (büyük tablo) | Satır kopyası | orta | Bölerek değil, tek seferde + izleme |
| `MODIFY` tip daraltma | Satır yeniden yazımı | uzun | Yeni alan + kopya (§3.9) |
| `DROP COLUMN` | Metadata lock | kısa | Önce pasife alma (§4.1 #3) |
| Toplu `UPDATE` | Satır kilidi | değişken | `LIMIT`'li parti (§3.9) |
| Tablo yeniden adlandırma | Yüksek | uzun | Yeni tablo + kopya |

> Süreler tablo boyutuna göre değişir; tahminler ölçülmeden yayına alınmaz (⚠️ VERIFICATION REQUIRED).

### 3.15 Ortam Matrisi

| Ortam | Veritabanı | Migration davranışı | Doğrulama |
|-------|------------|---------------------|-----------|
| Yerel geliştirme | `coremusic_{{DB}}_dev` | Bölüm bölüm uygulanır | §3.10 testleri |
| Test (CI) | `coremusic_test` | Temiz kurulum + migration | `vendor/bin/phpunit` |
| Staging | `coremusic_{{DB}}_staging` | Aynısı, veri kopyalı | §6 #7 drift kontrolü |
| Üretim | `coremusic_{{DB}}` | Yalnız onaylı commit | §4.6 eskalasyon hazır |
| Geri dönüş ortamı | — | Yok (ADR-014) | Yeni forward migration |

> Ortam adları örnek kalıptır; gerçek adlar `.env`/vault dışında doğrulanır (REDACTED politikası — §4.2).

---

## 4. Kurallar

### 4.1 Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Forward-only: migration revert yasak (ADR-014) | Geri alınamaz veri karışımı |
| 2 | BCNF zorunlu (ADR-040) — §3.5 kalıbı | Normalizasyon hatası |
| 3 | `is_deleted` soft delete zorunlu (gerçek `DELETE` yasak) | Veri kaybı |
| 4 | `created_at` / `updated_at` zorunlu | Audit trail eksik |
| 5 | snake_case tablo/alan adı zorunlu | Tutarsızlık |
| 6 | Ham SQL migration içinde; ORM yasak (ADR-002) | Bağımlılık + injection riski |
| 7 | `SELECT *` / string birleştirme yasak | SQL injection |
| 8 | Placeholder doldurulmadan commit yasak | Yarım şema |

### 4.2 BCNF Kontrol Listesi

| Kontrol | Kriter | ✔ |
|---------|--------|---|
| Birincil anahtar | `id BIGINT UNSIGNED AUTO_INCREMENT` | ☐ |
| Fonksiyonel bağımlılık | Aday anahtar dışındaki alanlar tam bağımlı | ☐ |
| Yinelenen veri yok | İkincil anahtarlarla tekrar yok | ☐ |
| Soft delete | `is_deleted TINYINT(1) DEFAULT 0` | ☐ |
| Timestamp | `created_at`, `updated_at`, (`deleted_at`) | ☐ |
| Karakter seti | `utf8mb4` + `utf8mb4_unicode_ci` | ☐ |
| Motor | `ENGINE=InnoDB` | ☐ |
| Index | Sorgu kalıplarına göre `INDEX`/`UNIQUE KEY` | ☐ |
| Adlandırma | snake_case (tablo + alan) | ☐ |
| İlişki | FK tanımı var; cascade niyeti açık | ☐ |

### 4.3 Ek Kurallar

- **Zorunlu:** migration dosyası §3.2 formatındadır — `$queries` dizisi + `return $queries;` + dosya başında ADR `@see` referansları.
- **Zorunlu:** değişiklik önce `.ai/.sql/mysql/` içindeki ilgili 18 şemadan biriyle karşılaştırılır (şema drift'i önlenir).
- **Zorunlu:** migration sonrası ilgili Repository sorguları `[[../query/Query-Template]]` ile hizalanır (soft delete + prepared).
- **Yasak:** `{{TABLE}}`, `{{DATABASE_NAME}}`, `{{COLUMN_*}}`, `{{ADR_SLUG}}` placeholder'larının commit öncesi açık kalması.
- **Yasak:** üretim verisinin migration dosyasına gömülmesi (seed yalnızca kurulum verisi — §3.3).
- **Uyarı:** araç seçimi (Phinx mi, `$queries` çalıştırıcı mı) composer.json kanıtıyla doğrulanmadı → ⚠️ VERIFICATION READY değil, **VERIFICATION REQUIRED**.
- **Eskalasyon:** BCNF çelişkisi → L1 (Data) → L2, timeout 30 s (AGENTS.md §10.1).

### 4.4 Yaygın Hatalar

| # | Hata | Sonuç | Doğrusu |
|---|------|-------|---------|
| 1 | Mevcut migration satırını düzenlemek | Uygulanmış ortamlarda drift | Yeni satır ekle (ADR-014) |
| 2 | `FLOAT` ile para | Yuvarlama kaybı | `DECIMAL(10,2)` (§3.7) |
| 3 | `DELETE FROM` ile temizlik | Geri alınamaz kayıp | `is_deleted = 1` (§4.1 #3) |
| 4 | Soft delete'siz okuma sorgusu | Silinmiş veri sızar | Query §3.1 koşulu |
| 5 | Kök `database/migrations/` varsayımı | Dosya kaybolur | `shared/database/migrations/` (§3.1) |
| 6 | phinx'i kurulu varsaymak | Bozuk komut | ⚠️ VERIFICATION REQUIRED (§2) |
| 7 | Tek `UPDATE` ile milyon satır | Timeout | `LIMIT`'li parti (§3.9) |
| 8 | Placeholder'lı commit | Yarım şema | §6 #3 kontrolü |

### 4.5 Test ve Coverage Beklentisi

| Kalem | Beklenti | Kaynak |
|-------|----------|--------|
| Migration dosyası yüklenir | `require` ile `$queries` dizisi döner | §3.10 |
| DDL içeriği | PRIMARY KEY + soft delete + timestamp içerir | §4.2 |
| Adlandırma | snake_case regex ile doğrulanır | §4.1 #5 |
| Suite | `vendor/bin/phpunit` ilgili testsuite yeşil | `[[../testing/phpunit-template]]` |
| Coverage | ≥ %80 (repository + migration katmanı dahil) | AGENTS.md §16 |

### 4.6 Eskalasyon ve Geri Dönüş

| Durum | Başlangıç | Hedef | Timeout | Geri dönüş |
|-------|-----------|-------|---------|------------|
| BCNF çelişkisi | L1 (Data) | L2 (Tech Lead) | 30 s | Yeni forward migration |
| Migration hatası üretimde | L1 (Data) | L2 | 30 s | Yeni düzeltme satırı (ADR-014) |
| Schema drift (18 şemadan biri) | L1 | L2 | 30 s | Şema dosyası + migration birlikte güncellenir |
| Test suite kırmızı | L1 (QA) | L2 | 60 s | Migration geri alınmaz, düzeltilir |
| Mimari çelişki (ADR) | L2 | L3 | 60 s | ADR güncellemesi (frozen değilse) |

**Not:** reversibles bir migration yazılmaz; her düzeltme yeni bir forward satırdır (ADR-014).

---

## 5. Workflow

```
ŞABLONU SEÇ → ŞEMAYI KARŞILAŞTIR → KOPYALA → {{VARIABLE}} DOLDUR → GUARDRAIL #16 DOĞRULA → COMMIT
```

1. **ŞABLONU SEÇ:** `.ai/.templates/infrastructure/migration-template.md` (Guardrail #16).
2. **ŞEMAYI KARŞILAŞTIR:** ilgili `.ai/.sql/mysql/{{DB}}.sql` (18 dosyadan) + `shared/database/migrations/CLAUDE.md`.
3. **KOPYALA:** `shared/database/migrations/{{TABLE}}_migration.php` oluştur (§3.2).
4. **`{{VARIABLE}}` DOLDUR:** `{{TITLE}}`, `{{TABLE}}`, `{{DATABASE_NAME}}`, `{{COLUMN_1..4}}`, `{{TABLE_PURPOSE}}`, `{{ADR_SLUG}}`, `{{SEED_VALUE_*}}`.
5. **GUARDRAIL #16 DOĞRULA:** §6 + §4.1 (8 madde) + §4.2 (10 satır) tam.
6. **COMMIT:** test ortamında uygula + `vendor/bin/phpunit` ilgili suite; `log.md` append'i parent yapar.

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | 7 zorunlu alan (title, type, category, date, updated, version, status, authority) |
| 2 | Bölüm yapısı | §1-§7 numaralı, en fazla 3 başlık seviyesi |
| 3 | Placeholder | `{{VARIABLE}}` kalmadı |
| 4 | Format | `$queries` dizisi + `return $queries;` + `@see` ADR referansı |
| 5 | Guardrails | §4.1 8/8 — revert yok, BCNF, soft delete, snake_case |
| 6 | BCNF | §4.2 onay kutuları 10/10 |
| 7 | Karşılaştırma | İlgili `.ai/.sql/mysql/*.sql` ile drift yok |
| 8 | İleriye dönük | §3.6 tablosuna uygun değişim |
| 9 | Test | İlgili phpunit suite yeşil (`[[../testing/phpunit-template]]`) |
| 10 | Halüsinasyon | Doğrulanmayan araç/yol iddia edilmedi (⚠️ işaretli) |

---

## 7. Referanslar

| Kaynak | Yol / Kimlik | Amaç |
|--------|--------------|------|
| Template registry | [[.templates/index]] | Envanter (DRY) |
| Vault anayasası | [[../CLAUDE.md]] | Hard Guardrails, ADR-042 |
| Agent registry | [[../../AGENTS.md]] | §6 yönlendirme (migration → Data Engineer) |
| Sorgu şablonu | [[../query/Query-Template]] | Migration sonrası sorgu hizası |
| Test şablonu | [[../testing/phpunit-template]] | Migration testleri |
| Disk kanıtı (migration) | `shared/database/migrations/` | Format + 2 gerçek dosya |
| Disk kanıtı (şema) | `.ai/.sql/mysql/` (18 SQL) | BCNF referans şemaları |
| Araç durumu | `*/composer.json` (3 kök) | phinx görünmüyor → ⚠️ VERIFICATION REQUIRED |
| İlgili ADR'ler | ADR-002 · ADR-014 · ADR-040 · ADR-072 · ADR-088 | §1 tablosunda eşleştirilmiştir |

---

**Template Version:** 2.0.0
**Last Updated:** 2026-09-23
