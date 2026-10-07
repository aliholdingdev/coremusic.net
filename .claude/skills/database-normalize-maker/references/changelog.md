# database-normalize-maker — Sürüm Geçmişi

> Kök `changelog:` alanı v3.0 formatında yasaktır (skill-maker N10) — geçmiş burada tutulur.

| Sürüm | Tarih | Değişiklik |
|-------|-------|-----------|
| 3.0.0 (format) | 2026-10-07 | claude-skill-v3 frontmatter'e yükseltildi (previous-version: 5.1); `references/db-engine-notes.md` (db-engine merge — 18 DB tam envanteri) + `references/changelog.md` eklendi; anti-overthink tek satıra indirildi; CLAUDE.md sidecar'ları kaldırıldı |
| 5.1 | 2026-09-29 | Faz2 içerik kalite denetimi: ölü referans ".ai/ADR/" → ".ai/.decisions/" (2 yer), ADR-021 → ADR-002 (ORM yasak); §2.1 bayat 11-DB envanteri işaretlendi (SSOT = 18 DB, ADR-040) + coremusic_users → coremusic_user düzeltmesi; project_structure disk uyarısı |
| 5.0 | 2026-08-15 | Complete rewrite with web-research best practices; Model → Migrate → Validate workflow; expand-contract zero-downtime migration; anti-pattern catalog; constraints catalog; common schema patterns; index design strategy; 25-item verification checklist; MySQL 9 rules; all SQL command types; error handling reference |

> Not: metadata.version = 3.0.0 format sürümüdür; içerik soyu `previous-version: 5.1`
> metadata alanında korunur.