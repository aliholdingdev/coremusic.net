# DB Engine Notları — 18 BCNF Envanteri & Hızlı Kontrol

> **Kaynak:** `.opencode/skills/db-engine/SKILL.md` (v1.1, 2026-09-29) benzersiz içeriği
> bu dosyaya birleştirildi (2026-10-07, db-engine merge). Ana skill:
> [SKILL.md](../SKILL.md).

## 1. 18 BCNF Veritabanı (ADR-040 — Güncel Envanter)

> SSOT: `.ai/.sql/mysql/*.sql` = 18 dosya (disk kanıtı) · `.ai/CLAUDE.md` DB tablosu ·
> [[.ai/.decisions/accepted/ADR-040-database-authority]]. SKILL.md §2.1'deki 11 satırlık
> liste bayattır (orada uyarı ile işaretli); **tam envanter aşağıdaki 18 satırdır.**

| # | Veritabanı | Amaç |
|---|-----------|------|
| 1 | `coremusic_auth` | Kimlik doğrulama, session, token |
| 2 | `coremusic_user` | Profiller, tercihler |
| 3 | `coremusic_musics` | Şarkılar, sanatçılar, dosyalar |
| 4 | `coremusic_albums` | Albüm koleksiyonları |
| 5 | `coremusic_playlist` | Çalma listeleri |
| 6 | `coremusic_catalog` | Referans verileri |
| 7 | `coremusic_logs` | Audit trail, analitik |
| 8 | `coremusic_media` | Cihaz senkronizasyonu |
| 9 | `coremusic_system` | Ayarlar, config |
| 10 | `coremusic_social` | Yorumlar, paylaşımlar |
| 11 | `coremusic_wireless` | WiFi + Bluetooth |
| 12 | `coremusic_ai` | Öneri profilleri |
| 13 | `coremusic_api` | API anahtarları |
| 14 | `coremusic_cms` | Sayfalar, blog |
| 15 | `coremusic_download` | İndirme kuyruğu |
| 16 | `coremusic_neva` | EQ, DSP ayarları |
| 17 | `coremusic_studio` | Stüdyo oturumları |
| 18 | `coremusic_patch` | Schema versiyonları |

**Kurallar:** ORM yasak (ADR-002) · SELECT * yasak · prepared statement zorunlu ·
BCNF zorunlu · çapraz-DB foreign key yasak (ADR-040 izolasyonu).

## 2. Normalizasyon Hızlı Kontrol Soruları

| Form | Kural | Kontrol |
|------|-------|---------|
| **1NF** | Atomik değer, liste yok | Her kolonda tek değer var mı? |
| **2NF** | Kısmi bağımlılık yok | Composite PK'da tüm non-key kolonlar tam bağımlı mı? |
| **3NF** | Geçici bağımlılık yok | A→B→C zinciri varsa ayrı tabloya taşı |
| **BCNF** | Her determinant key | Belirleyici candidate key mi? |

## 3. MySQL 9 Zorunlu Kurallar (Özet)

| Kural | Değer |
|-------|-------|
| Motor | `ENGINE=InnoDB` |
| Charset | `utf8mb4_unicode_ci` |
| PK | `id BIGINT UNSIGNED AUTO_INCREMENT` |
| Timestamp | `created_at`, `updated_at`, `deleted_at` (her tabloda) |
| Para | `DECIMAL(10,2)` — FLOAT yasak |
| İsimlendirme | snake_case, çoğul tablolar |

### Zorunlu Kolon Şablonu

```sql
id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
deleted_at TIMESTAMP NULL DEFAULT NULL
```

## 4. Workflow: Model → Design → Validate (Hızlı)

1. **Model:** tablolar, ilişkiler, sensitive alanlar, veri miktarı, sorgu kalıpları topla.
2. **Design:** kolonlar, tipler, PK/FK/Constraint/Index tanımla.
3. **Normalize:** 1NF → 2NF → 3NF → BCNF kontrolü.
4. **Generate:** `schema.sql`, `seed_data.sql`, `er_diagram.md`, `migration/`.
5. **Validate:** aşağıdaki 10 madde.

### 10 Maddelik Hızlı Doğrulama

- [ ] PK var mı? BIGINT UNSIGNED mi?
- [ ] Charset utf8mb4_unicode_ci mi?
- [ ] Engine InnoDB mi?
- [ ] Timestamp kolonları var mı?
- [ ] FK'lar doğru tanımlı mı? ON DELETE stratejisi seçilmiş mi?
- [ ] FK'lara index eklendi mi?
- [ ] BCNF uyumlu mu?
- [ ] SELECT * kullanılmamış mı?
- [ ] Soft delete kullanılıyor mu?
- [ ] Para kolonları DECIMAL mi (FLOAT değil)?

> Tam 25 maddelik checklist: [SKILL.md](../SKILL.md) §11 Adım 6.

## 5. Yasaklar (Özet)

| Yasaklı | Neden |
|---------|-------|
| ORM kullanımı | ADR-002 — PDO prepared only |
| SELECT * | Açık kolon listesi zorunlu |
| FLOAT para birimi | DECIMAL(10,2) |
| Cross-database FK | 18 DB BCNF izolasyonu (ADR-040) |
| Hard delete | Soft delete (deleted_at) |
| Stored procedure (iş mantığı) | PHP'de yapılmalı |
| ENUM sabitleri | Lookup tablosu kullan |

---
*CoreMusic reference v3.0 — database-normalize-maker/references/db-engine-notes.md — Updated: 2026-10-07*