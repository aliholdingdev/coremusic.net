---
title: Kırık Wiki-Link Raporu (Önceden Var — Onarım Kaydı)
type: report
status: active
version: 2.0.0
created: 2026-09-24
updated: 2026-09-27
author: coremusic-vault-docs
authority: report
---

# Kırık Wiki-Link Raporu (Önceden Var — Onarım Kaydı)

> **NE ZAMAN OKUNUR:** Vault link hijyeni, ADR link retarget'i veya dead-mark temizliği yapılacak her görevde bu rapor okunur; her yeni tarama sonunda güncellenir. v1 (2026-09-24) salt rapordu — **v2 (2026-09-27) onarım kaydıdır**: 22 retarget `fixed`, 9 eşdeğersiz `unresolved`, 16 satır9/26'da dışsal olarak çözülmüş.

**Özet (2026-09-27):** v1 kaydı 34 satır → bugün 6 dosyada tarama 31 aday (`.opencode/CLAUDE.md` 12 yeni dahil) → onarım: **fixed 22 · unresolved 9 · dışsal çözülen 16** → onarım sonrası taze tarama: **kırık link 0 / 6 dosya**.

**Retarget desenleri:** (1) ADR → gerçek kayıt dosyası `[[.decisions/accepted/<slug>]]` (kısa slug'lar tam yola, `decisions/`→`.decisions/` nokta öneki); (2) kök registry → `[[../.ai/AGENTS.md]]`; (3) taşınan mimari dosya → yeni k-yolu (`k11-ux/itcss-9-layer`); (4) eşdeğer kanıtlanamayan hedef → link kaldırılmadı, **düz metne çevrildi** (`unresolved`).

## A. Onarılan — `status: fixed` (22)

| Dosya | Satır | Eski hedef | Yeni hedef |
|---|---|---|---|
| `.claude/CLAUDE.md` | 659 | `[[ADR-017-dsp-hardware-mode]]` | `[[.decisions/accepted/ADR-017-dsp-hardware-mode]]` |
| `.claude/CLAUDE.md` | 660 | `[[ADR-010-csrf-protection-strategy]]` | `[[.decisions/accepted/ADR-010-csrf-protection-strategy]]` |
| `.claude/CLAUDE.md` | 662 | `[[ADR-011-session-management]]` | `[[.decisions/accepted/ADR-011-session-management]]` |
| `.claude/CLAUDE.md` | 816 | `[[decisions/accepted/ADR-001-vanilla-js-itcss]]` | `[[.decisions/accepted/ADR-001-vanilla-js-itcss]]` |
| `.claude/CLAUDE.md` | 817 | `[[decisions/accepted/ADR-002-pdo-mandatory-no-orm]]` | `[[.decisions/accepted/ADR-002-pdo-mandatory-no-orm]]` |
| `.claude/CLAUDE.md` | 818 | `[[decisions/accepted/ADR-010-csrf-protection-strategy]]` | `[[.decisions/accepted/ADR-010-csrf-protection-strategy]]` |
| `.claude/CLAUDE.md` | 819 | `[[decisions/accepted/ADR-011-session-management]]` | `[[.decisions/accepted/ADR-011-session-management]]` |
| `.claude/CLAUDE.md` | 820 | `[[decisions/accepted/ADR-022-database-hardened-security]]` | `[[.decisions/accepted/ADR-022-database-hardened-security]]` |
| `.claude/CLAUDE.md` | 882 | `[[ADR-010-csrf-protection-strategy]]` | `[[.decisions/accepted/ADR-010-csrf-protection-strategy]]` |
| `.opencode/CLAUDE.md` | 659,660,662,816-820,882 | `.claude` ile hash-esit ayna — aynı 9 retarget | aynı |
| `assets.coremusic.net/AGENTS.md` | 15 | `[[../AGENTS.md]]` | `[[../.ai/AGENTS.md]]` |
| `assets.coremusic.net/AGENTS.md` | 77 | `[[../.ai/architecture/l3-presentation/itcss-architecture.md]]` | `[[../.ai/architecture/k11-ux/itcss-9-layer.md]]` |
| `home.coremusic.net/CLAUDE.md` | 123 | `[[../AGENTS.md]]` | `[[../.ai/AGENTS.md]]` |
| `assets.coremusic.net/CLAUDE.md` | 198 | `[[../AGENTS.md]]` | `[[../.ai/AGENTS.md]]` |

> ADR hedeflerinin tümü diskte gerçek dosyaya bağlandı: `.ai/.decisions/accepted/` altında ADR-001/002/010/011/017/022 dosyaları mevcut (9/26, commit `9695a2e` ile oluştu — frozen ADR metinlerine dokunulmadı, yalnız link yolu).

## B. Çözülmeyen — `status: unresolved` (9)

| Dosya | Satır | Hedef | Neden |
|---|---|---|---|
| `.claude/CLAUDE.md` | 535 | `screens/B-home/dashboard-1920` | B-home dizini yok (`screens/` = shared + T01-T31); eşleştirilecek aynı ekran dosyası kanıtlanamadı — düz metin bırakıldı |
| `.claude/CLAUDE.md` | 886 | `architecture/k0-k5-software/k5-data-layer/database_master` | `k0-k5-software` dizini yok; `k5-veri-yonetimi/mysql-18-database.md` içinde `database_master` geçmiyor — eşdeğer kanıtlanamadı |
| `.claude/CLAUDE.md` | 889 | `architecture/03-contracts/master-implementation-plan` | `03-contracts` dizini yok; ADR-087 dosyası da yok (yalnızca `.decisions/index.md` kaydı var) |
| `.opencode/CLAUDE.md` | 535,886,889 | aynı 3 hedef | hash-esit ayna — aynı nedenler |
| `assets.coremusic.net/AGENTS.md` | 78 | `../.ai/architecture/l3-presentation/js-module-architecture.md` | `l3-presentation` dizini yok; `js-module-architecture` repo genelinde yok |
| `home.coremusic.net/CLAUDE.md` | 127 | `../.ai/.subdomains/home.coremusic.net/index.md` | `.ai/.subdomains/` altında yalnızca `CLAUDE.md` var; `home.coremusic.net/` dizini + `index.md` yok |
| `assets.coremusic.net/Css copy/CLAUDE.md` | 24 | `../../.ai/architecture/l3-presentation/CLAUDE.md` | `l3-presentation` dizini yok; `architecture/` altında `CLAUDE.md` yok |

> Kurallar gereği link **kaldırılmadı** — hedef metni düz metne çevrildi (kural: silme yok, yönlendirme + raporlama).

## C. Dışsal çözülen — 9/26 commit `9695a2e` (16 satır / 14 hedef)

v1'de (9/24) kırıkken9/26'da ADR arşivinin `.ai/.decisions/accepted/` altına oluşması ve dosyaların yeniden düzenlenmesiyle kendiliğinden çözülenler:

`architecture/00-overview/architecture-master` · `decisions/accepted/ADR-038-8.1-...` · `decisions/accepted/ADR-040-...` · `decisions/accepted/ADR-042-...` · `decisions/accepted/ADR-044-...` · `.decisions/draft/ADR-089-classab-24v` · `ADR-040-database-authority` · `ADR-038-8.1-...` · `architecture/l0-infrastructure` · `decisions/accepted/ADR-043-...` · `ADR-044-...` · `architecture/05-data/database_master` · `architecture/06-audio/index` · `decisions/accepted/ADR-087-...`

- Sayım: v1 34 satır = 18 carry-over + 16 dışsal çözülen (hedef bazlı benzersiz: 14).
- Bugün 31 aday = 18 carry-over + `.opencode` 12 (v1 kapsamı dışındaydı, kural3 ile dahil) + 1 yeni hedef (`k0-k5-software/.../database_master` — dosya9/26'da yeni yolla yazılmış).

## D. Doğrulama

- Onarım sonrası taze tarama (6 adaylı çözücü + kod bloğu strip): **6/6 dosya `broken: 0`**.
- Writer verify: **6/6 OK** (BOM=0, mojibake=0, cjk=0, NUL=0); satır sayıları korundu (936/936/85/188/252/33).
- `.claude/CLAUDE.md` ↔ `.opencode/CLAUDE.md` hash eşit (onarım sonrası da).

## Kurallar

- Bu rapor salt-okunurdur; yeni düzeltme için ayrıca onay gerekir.
- Frozen ADR (001-037): yalnız link hedefi değişti, metin 0 edit.
- `unresolved` 9 hedef: eşdeğer dosya üretilince yeniden retarget edilir; düz metinler bilinçli bırakılır.
- Figma token / secret bu rapora YAZILMAZ (REDACTED politikası).
