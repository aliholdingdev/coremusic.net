---
title: "CoreMusic — Veritabanı Kuralları (MySQL 9 · 18 BCNF)"
type: guide
category: governance
date: 2026-10-22
updated: 2026-10-22
version: 1.0.0
status: active
authority: "Derived — SSOT: .ai/CLAUDE.md §18 (ADR-040) · .ai/AGENTS.md §16 (Data standardı)"
docType: claude
---

# CoreMusic — Veritabanı Kuralları (Database Rules)

**docType:** claude · **Klasör:** `.agents/rules/` · **Sorumlu:** data-engineer

**Zorunlu Bağlantılar:** [[RULES]] · [[backend-rules]] · [[security-rules]] · [[../../.ai/CLAUDE]]

> **eli10:** Şema işlerinde BCNF kanıtlı; ORM yok, `SELECT *` yok, prepared statement var; migration `.sql` dosyadan yürür.

---

## 1. Amaç

Veritabanı kurallarını (18 BCNF envanteri, normalization, migration, sorgu standartları, ek sistemler) vault SSOT'undan (`@.ai/CLAUDE.md` §18, ADR-040) toplar. Data kalite hedefi **%100** (BCNF, no ORM, no SELECT *, prepared) — `.ai/AGENTS.md` §16.

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| MySQL 9 şema/migration/sorgu (18 BCNF + ek 2 DB) | Backend katmanı → [[backend-rules]] |
| `.ai/.sql/mysql/*.sql` · `shared/database/migrations/` | Secret/API key yönetimi → [[security-rules]] |
| BCNF audit / 3NF→BCNF düzeltme | Cache (APCu) → [[architecture-rules]] §3.7 |

---

## 3. Mimari

### 3.1 18 BCNF Veritabanı — 156 Tablo (`@.ai/CLAUDE.md` §18, ADR-040)

| # | Veritabanı | Amaç |
|---|------------|------|
| 1 | `coremusic_auth` | Users, roles, sessions, tokens, credential vault, API keys |
| 2 | `coremusic_user` | Profiles, preferences, history, favorites |
| 3 | `coremusic_musics` | Songs, artists, genres, lyrics, files, podcasts, videos, radio |
| 4 | `coremusic_albums` | Album collections, discs, stats |
| 5 | `coremusic_playlist` | User/AI playlists, collaborators, followers |
| 6 | `coremusic_catalog` | Reference data (genres, roles, instruments, moods) |
| 7 | `coremusic_logs` | App logs, audit trail, analytics, performance metrics |
| 8 | `coremusic_media` | Device sync, media metadata, access control |
| 9 | `coremusic_system` | Settings, config, cache, EQ, notifications, i18n |
| 10 | `coremusic_social` | Comments, shares, activity, listening rooms |
| 11 | `coremusic_wireless` | WiFi + Bluetooth networks |
| 12 | `coremusic_ai` | Preference profiles, listening features, recommendations |
| 13 | `coremusic_api` | API keys, rate limits, call logs, webhooks |
| 14 | `coremusic_cms` | Pages, blog, tags, media assets, FAQs, banners |
| 15 | `coremusic_download` | Download queue, history, cache, source APIs |
| 16 | `coremusic_neva` | EQ presets, DSP settings, routing matrix, spectrum |
| 17 | `coremusic_studio` | Studio sessions, tracks, presets, equipment |
| 18 | `coremusic_patch` | Schema versions, migration logs, patches |

### 3.2 Ek Sistemler — 18'in DIŞINDA (vault §18 envanter düzeltmesi 2026-10-07, canlı DB doğrulaması)

| DB | Tablo | Durum |
|----|:-----:|-------|
| `media_catalog` | 9 | `.sql` dosyası VAR; canlıya **deploy edilmedi** → PLANNED |
| `novasearch` | 7 | Canlıda VAR; **knex** ile yönetiliyor (ADR-014 runner dışı); şema SSOT'u `.ai/.sql/mysql/novasearch.sql` (salt-okunur dump) |

`.sql/mysql/` dosya sayısı = **20** (18 + media_catalog + novasearch). "18'e dahil" diye sayılmazlar.

### 3.3 Zorunlu Kurallar (`@.ai/CLAUDE.md` §18 + §21)

| Kural | Değer |
|-------|-------|
| **BCNF** | 18 DB kesinlikle BCNF; violation → 3NF→BCNF audit (Edge Case) |
| **ORM** | **YASAK** (ADR-002) — raw PDO |
| **`SELECT *`** | **YASAK** — explicit columns |
| **Prepared statement** | **Zorunlu** (parametreli sorgu) |
| Migration | `.sql` dosyası + migration log (`coremusic_patch`); knex yalnız novasearch (documented exception) |
| İsimlendirme | `coremusic_` prefix (18'lik seri); ek sistemler prefix'siz (documented) |
| Port | 3306 (Port Register §11) |

### 3.4 Yazılı Şema SSOT'ları

- Şema dosyaları: `.ai/.sql/mysql/*.sql` (salt-okunur dump'lar dahil).
- Migration kodu: `shared/database/migrations/` (CLAUDE.md pointer'ı mevcut).
- Skill: `database-normalize-maker` (BCNF/migration üretim kapısı).

---

## 4. Kurallar

| # | Kural | İhlal |
|---|-------|-------|
| 1 | BCNF ihlali tespit → audit + düzeltme planı | Escalation L1(Data)→L2, 30s (`.ai/AGENTS.md` §10.1) |
| 2 | ORM / `SELECT *` / prepared'sız sorgu | Revert |
| 3 | Şema değişikliği elde SQL ile production'a uygulanmaz | Migration dosyası + onay |
| 4 | Ek 2 DB "18'e dahil" diye sayılmaz | Zero-Hallucination |
| 5 | DB "var" iddiası disk/canlı kanıtlı | `VERIFICATION REQUIRED` |
| 6 | Domain boundary: `*.sql` = data agent | Layer violation → revert |

---

## 5. Workflow

```text
Şema/migration/sorgu işi
  → Vault: @.ai/CLAUDE.md §18 (ADR-040) + .ai/.sql/mysql/*.sql
  → Skill: database-normalize-maker (BCNF kanıtı)
  → Migration dosyası üret (shared/database/migrations/)
  → Test ortamında uygula + rollback dene
  → QA (veri bütünlüğü) → MO: log.md
```

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter 7 alan + docType | Mevcut |
| 2 | 18 DB adı vault §18 ile birebir | §3.1 |
| 3 | Ek 2 DB durumu | §3.2 (deploy edilmedi / knex) |
| 4 | Zorunlu kurallar 4'lü | BCNF · no ORM · no SELECT * · prepared |
| 5 | 156 tablo + 20 dosya sayıları vault ile eşit | §3.1-§3.2 |

---

## 7. Referanslar

| Kaynak | Yol | Amaç |
|--------|-----|------|
| Anayasa §18 | [[../../.ai/CLAUDE]] | 18 DB + ADR-040 |
| Yasaklar §21 | [[../../.ai/CLAUDE]] | ORM/SELECT * |
| Şema dump'ları | `.ai/.sql/mysql/` | Salt-okunur SSOT |
| Migration | `shared/database/migrations/` | Kod tarafı |
| Skill | `.claude/skills/database-normalize-maker/SKILL.md` | BCNF üretim kapısı |
| Master | [[RULES]] §4.2 | Özet |

**Last Updated:** 2026-10-22 · **Şablon:** context-template (Guardrail #16)
