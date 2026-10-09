---
type: architecture
category: layer
title: "K005 — Veri Yönetimi"
date: 2026-10-09
status: active
version: 2.0.0
authority: SSOT
---

# K005 — Veri Yönetimi

## §1 Kimlik
- Katman: K005 · Alan: **A0** (K0-K5 aralığı — kapsam etiketi uyuşmazlığı: A0 etiketi "Altyapı/Donanım", K005 veri yönetimidir; bkz. [[katman-baglilik-matrisi]] §1).
- Kapsam: BCNF veri yönetimi — PDO-only, şema sahipliği, migration, outbox.

## §2 Sorumluluk
1. BCNF şemaları; modül→tablo sahipliği: yazma münhasır, okuma paylaşımlı (plan §5.1).
2. PDO zorunlu; ORM ve `SELECT *` yasak; prepared statement (ADR-002/033).
3. Sıralı, geri alınabilir migration stratejisi (ADR-014).
4. Cross-DB yazmada outbox + WAL zorunlu; ikili (dual) yazma yasak (ADR-081, plan §5.3).
5. Okuma/yazma ayrımı yalnızca sorgu düzeyinde; ayrı okuma DB'si/event-store KURULMAZ (plan §5.3).

## §3 Bağlantılar (Dependency Rule)
- **Alt:** K000 (MySQL/PDO zemini).
- **Üst (çağıran):** K006 (session/token tabloları) · K008/K010 repository'leri (port/adaptor).

## §4 ADR Bağlantıları
| ADR | Başlık |
|---|---|
| ADR-002 | PDO Mandatory, ORM Yasak (frozen) |
| ADR-003 | Multi-DB 9 BCNF Veritabanı (frozen) |
| ADR-014 | Multi-DB Migration Strategy (frozen) |
| ADR-033 | SQL Normalization Strategy (frozen) |
| ADR-040 | Database Authority (18 BCNF) |
| ADR-041 / ADR-050 | DB Normalization Supplementary · Multi-DB Sync Strategy |
| ADR-081 | Multi-Provider Data Sync (Outbox+WAL) |
| ADR-072…ADR-079 | Domain şemaları (social/podcast/radio/ai/video/studio/cms/i18n) |

## §5 Durum
**IMPLEMENTED** — kanıt (2026-10-09): `.ai/.sql/mysql/*.sql` = 20 dosya · `shared/src/Database/{DatabaseManager,DatabaseRegistry}.php` · ADR-081/084 dosyaları diskte.

## §6 Risk / Not
- ⚠️ VERIFICATION REQUIRED: "18 BCNF DB" sayı iddiası doğrulanamadı — diskte 20 `.sql` dosyası var (dosya ≠ veritabanı); ayrıca ADR-003 başlığı "**9** BCNF" derken ADR-040 "**18** BCNF" der → sayı **UNKNOWN**, Data Engineer sayımı ister (plan §1/§7).
- Cross-DB outbox denetimi (dual-write taraması) yapılmadı → plan §6 Faz 3.
