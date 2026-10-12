# composer-sync — Sürüm Geçmişi

> Kök `changelog:` alanı v3.0 formatında yasaktır (skill-maker N10) — geçmiş burada tutulur.
> 3.1 kayıtları `.opencode/skills/composer-sync/SKILL.md`'den (silinmeden önce) birleştirildi (2026-10-07).

| Sürüm | Tarih | Değişiklik |
|-------|-------|-----------|
| 3.0.0 (format) | 2026-10-07 | claude-skill-v3 frontmatter'e yükseltildi (previous-version: 3.1); `.opencode` kopyasının benzersiz içeriği birleştirildi: `name`+`description`, `.ai/ADR/` → `.ai/.decisions/` (2 yer), project_structure disk uyarısı, changelog 3.1 kayıtları; `references/changelog.md` eklendi; anti-overthink tek satıra indirildi; CLAUDE.md sidecar kaldırıldı |
| 3.1 | 2026-09-29 | Faz2 içerik kalite denetimi — ".ai/ADR/ → .ai/.decisions/; ADR-042 yolu tam dosya adıyla düzeltildi"; Dup-ID notu eklendi (kaynak .opencode kopyasıydı — 2026-10-07 birleştirmeyle çözümldü); project_structure: diskte olmayan 8 subdomain klasörü uyarı işareti aldı |
| 3.0 | 2026-08-15 | Complete rewrite — shared folder as source; Composer dependency for shared library kaldırıldı; path-based junction resolution; PowerShell scripts; file count validation (83 PHP); critical file checksums; rollback procedure; multi-subdomain orchestration |

> Not: metadata.version = 3.0.0 format sürümüdür; içerik soyu `previous-version: 3.1`
> metadata alanında korunur. Dup-ID durumu çözüldü: tek kanonik kopya = `.claude/skills/composer-sync/`.