# skill-maker — Changelog (v3.0 N10: kök `changelog[]` buraya taşındı)

> Sürüm otoritesi: `SKILL.md` → `metadata.version`. Bu dosya yalnız geçmiş kaydıdır.

| Version | Date | Changes |
|---------|------|---------|
| 3.0.0 | 2026-10-07 | **Claude Skill v3.0 formatına yükseltme (format otoritesi).** Frontmatter yalnız `name`/`description`/`license`/`metadata` köküne indirildi; eski kök alanlar (`title`, `type`, `version`, `format`, `updated`, `authority`, `mode[]`, `purpose[]`, `reference{}`, `triggers[]`, `changelog[]`) yasaklandı → description'a / metadata.* / gövdeye / bu dosyaya taşındı. SKILL.md §5 FORMAT SPEC (N1-N10) eklendi; `examples/` zorunlu hale getirildi; CLAUDE.md sidecar'ları silindi; `references/examples.md` içeriği `examples/01-basic-skill.md` + `examples/02-coremusic-php-skill.md`'e kopyalandı — **orijinal dosyalar korundu** (yetki düzeltmesi 2026-10-07: yalnız CLAUDE.md sidecar'ları silinebilir; `references/examples.md` ve `references/Skills-Olusturma-Rehberi (1).md` silinmez/geri alındı). |
| 3.0.0 | 2026-10-07 | Yapı v3.0 (claude-skill-v3.0): tek SKILL.md + references/ + examples/ multi-md iskeleti; içerik çekirdeği korundu (önceki içerik sürümü 3.2) |
| 3.2 | 2026-09-29 | Faz2 içerik kalite denetimi — `.ai/ADR/` → `.ai/.decisions/` |

---

*CoreMusic Skill v3.0 (changelog) — Updated: 2026-10-07*