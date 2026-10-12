---
title: "skill-maker Example — Ham İstekten v3.0 Skill Üretimi"
type: example
category: skills
version: 1.0.0
updated: 2026-10-07
---

# Örnek: Yeni Skill Üretimi (ADIM 1 → ADIM 5)

Bu örnek, skill-maker §2 akışının **gerçek bir istek üzerinde** nasıl yürüdüğünü gösterir.

---

## Girdi (kullanıcı isteği — ham)

```text
"kullanıcı: sql-optimizer adında yeni bir skill oluştur — sorguları analiz etsin,
EXPLAIN ile darboğazları bulsun"
```

---

## ADIM 1 — Gereksinim Analizi (Soru-Cevap)

| Soru | Cevap | Not |
|------|-------|-----|
| Skill adı | `sql-optimizer` | lowercase-hyphen ✓ |
| Ana görev/domain | DB performans analizi (MySQL 9) | domain: data-engineer |
| Araçlar/API'ler | MySQL `EXPLAIN`, `.ai/.sql/` şemaları | doğrulanacak (ADIM 2) |
| Çıktı | Rapor + düzeltme önerisi (şema/sorgu) | format: Markdown tablo |

Onay: kullanıcı 3 cevabı da verdi → ADIM 2'ye geç.

---

## ADIM 2 — Web Research & Truth Mode

| İddia | Kaynak (min 2-3) | Sonuç |
|-------|------------------|-------|
| `EXPLAIN FORMAT=JSON` MySQL 9'da var | MySQL 8.0/9.0 refman (dev.mysql.com) + işaretlenen sürüm notu | ✅ Verified |
| `EXPLAIN ANALYZE` gerçek çalıştırma yapar | MySQL refman + blog teyidi | ✅ Verified |
| "EXPLAIN output kolonları" spesifik listesi | Tek kaynak | ⚠️ `VERIFICATION REQUIRED` — skill'e "sürüm notuna bak" notuyla yazıldı |

Kural: tek kaynaklı iddia skill içine **kesin** olarak alınmadı → etiketlendi (§1.2).

---

## ADIM 3 — Yapı Üretimi (v3.0 iskelet)

```text
.claude/skills/sql-optimizer/
├── SKILL.md               ← v3.0 iskelet (aşağıda iskelet)
├── references/
│   ├── overview.md        ← MySQL EXPLAIN derinlemesine (doğuğrulanmış kısım)
│   └── rules.md           ← CoreMusic DB kısıtları (ADR-002: prepared, SELECT * yasak)
└── examples/
    └── n-plus-one-analysis.md
```

`SKILL.md` iskeleti (üretilen — özet):

```markdown
---
name: sql-optimizer
description: "Use when analyzing slow MySQL queries or schema performance — EXPLAIN based, BCNF aware."
license: MIT
metadata:
  version: 1.0.0
  format: claude-skill-v3
  author: Bayram Ali (ULTRATHINK Engineering)
  category: database-orchestration
  tags: [mysql, truth-mode]
  updated: 2026-10-07
---

# sql-optimizer v3.0
§0 Ne zaman kullanılır (trigger tablosu)
§1 Çekirdek kurallar (EXPLAIN zorunlu, SELECT * yasak → references/rules.md)
§2 Akış (slow query → EXPLAIN → analiz → öneri → doğrulama)
§3 references/ indeksi
§4 examples/ indeksi
§5 Doğrulama checklist
§6 İlişkili kurallar (ADR-002, ADR-040, .ai/CLAUDE.md §18)
```

---

## ADIM 4 — CoreMusic Kurallarının Enjeksiyonu

SKILL.md'ye gömülü kurallar:

- **ADR-002:** Raw PDO + prepared statement; ORM yok → optimizer YALNIZ sorgu/şema önerir,
  ORM eklemez.
- **`SELECT *` yasak** (§21) → önerilen sorgularda explicit kolon listesi.
- **BCNF** (ADR-040, `.ai/CLAUDE.md` §18) → normalizasyon ihlali bulursa düzeltme ister.
- **OWASP:** optimizer raporunda veri sızdıran sorgu logları `[REDACTED]`.
- **Test gate:** "EXPLAIN çıktısı olmadan performans iddiası yazılmaz" (test edilmemiş = yok).

---

## ADIM 5 — Kalite Geçişi (checklist çıktısı)

```text
[x] Web'den min 2-3 kaynak doğrulama — EXPLAIN ANALYZE (2 kaynak) ✓; tek kaynaklı
    kolon listesi ⚠️ etiketlendi
[x] Halüsinasyon yok (etiketli madde kesin yazılmadı)
[x] v3.0 iskelet tam (SKILL.md + references/ + examples/)
[x] Frontmatter kökü SADECE name/description/license/metadata (N1) ✓
[x] 2000 satır kısıtı (SKILL.md ~120 satır) ✓
[x] CoreMusic kuralları enjekte (ADR-002, SELECT *, BCNF, test-gate)
[x] Credential hardcoded yok
[x] Registry notu: .claude/CONTEXT.md envantere eklenecek
```

**Kullanıcıya rapor:** "Skill `sql-optimizer` v3.0 hazır — 1 kaynak `⚠️ VERIFICATION REQUIRED`
işaretli; onay: envantere eklensin mi?"

---

## Öğrenilecek dersler (bu örneğin özeti)

1. **Soru-Cevap atlanmaz** — 3 cevap yoksa ADIM 2'ye geçilmez.
2. **Tek kaynak = etiket** — doğrulanamayan satır kesin yazılmaz.
3. **Yapı önce, içerik sonra** — iskelet onaylanınca derinlik yazılır.
4. **CoreMusic kuralı enjeksiyon** — skill, vault'la çelişemez (DUR + sor).
