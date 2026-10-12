---
name: skill-maker-example-01-basic-skill
description: "Example (not a skill) — skill-maker basic worked example: sql-optimizer ham istekten v3.0 SKILL.md çıktısına tam döngü (references/examples.md içeriğinden kopyalandı; orijinal korundu)."
license: MIT
metadata:
  version: 1.0.0
  format: claude-skill-v3
  category: example
  updated: 2026-10-07
  source: references/examples.md (içerik buraya kopyalandı; orijinal dosya korundu)
---

# Örnek 01 — Temel Skill Üretim Senaryosu (girdi → çıktı)

> Bu dosya skill-maker'ın **temel** çalışmış örneğidir (N5: girdi → çıktı tam döngü).
> Uzun tam döküm için `examples/new-skill-example.md`, uçtan uca üretim kaydı için
> `examples/full-skill-walkthrough.md`.

## Girdi (kullanıcı isteği — ham)

```text
"Yeni bir veritabanı analiz skill'i oluştur, adı sql-optimizer olsun.
Sorguları analiz etsin, EXPLAIN ile darboğazları bulsun."
```

## Agentic İş Akışı (skill-maker §4 pipeline)

1. **ADIM 1 Gereksinim:** ad = `sql-optimizer` (lowercase-hyphen ✓) · domain = DB performans
   (MySQL 9) · araçlar = `EXPLAIN`, `.ai/.sql/` şemaları · çıktı = Markdown rapor.
2. **ADIM 2 Web Research:** MySQL 8/9 refman + blog teyidi (min 2-3 kaynak) — EXPLAIN
   FORMAT=JSON, EXPLAIN ANALYZE doğrulandı; tek kaynaklı iddia `⚠️ VERIFICATION REQUIRED`.
3. **ADIM 3 Yapı:** v3.0 iskelet — `SKILL.md` + `references/{overview,rules}.md` +
   `examples/`; frontmatter yalnız `name`/`description`/`license`/`metadata` (N1).
4. **ADIM 4 Enjeksiyon:** CoreMusic kuralları — ADR-002 raw PDO + prepared; `SELECT *`
   yasak (§21); BCNF (ADR-040); test-gate ("EXPLAIN çıktısı olmadan performans iddiası yok").
5. **ADIM 5 Kalite:** `references/checklist.md` + örneklerle hizala + kullanıcıya rapor.

## Çıktı (üretilen SKILL.md — kısaltılmış)

```markdown
---
name: sql-optimizer
description: "Use when analyzing slow MySQL queries or schema performance — EXPLAIN based, BCNF aware. Tetikleyici: 'sql analiz', 'optimize et'."
license: MIT
metadata:
  version: 1.0.0
  format: claude-skill-v3
  author: Bayram Ali (ULTRATHINK Engineering)
  category: database-orchestration
  tags: [mysql, explain, truth-mode]
  updated: 2026-10-07
---

# sql-optimizer — Otonom Analiz

§1 Genel Bakış · §2 Zorunlu Okumalar (references/rules.md, references/overview.md …)
§3 Örnekler (examples/n-plus-one-analysis.md …) · §4 Akış (slow query → EXPLAIN → analiz)
§5 Kurallar: EXPLAIN zorunlu · SELECT * yasak · uydurma index tahmini YASAK
(// ⚠️ VERIFICATION REQUIRED)

Anti-overthink: kök AGENTS.md §5 (MAX THINKING 7 madde) geçerlidir.

*CoreMusic Skill v3.0 — metadata.version: 1.0.0 — Updated: 2026-10-07*
```

## Doğrulama çıktısı (ADIM 5)

```text
[x] Web'den min 2-3 kaynak doğrulama (EXPLAIN ANALYZE) ✓
[x] Halüsinasyon yok — tek kaynaklı iddia etiketli
[x] v3.0 iskelet tam (SKILL.md + references/ + examples/)
[x] Frontmatter kökü SADECE name/description/license/metadata (N1) ✓
[x] SKILL.md ≤250 hedef / 2000 hard cap ✓
[x] Credential hardcoded yok
```

**Kullanıcıya rapor:** "Skill `sql-optimizer` v3.0 hazır — 1 kaynak `⚠️ VERIFICATION
REQUIRED` işaretli; onay: envantere eklensin mi?"

---

*CoreMusic Skill v3.0 (örnek 01) — Updated: 2026-10-07*