---
title: Kırık Wiki-Link Raporu (Önceden Var — Onarım Kaydı)
type: report
status: active
version: 2.1.0
created: 2026-09-24
updated: 2026-09-27
author: coremusic-vault-docs
authority: report
---

# Kırık Wiki-Link Raporu (Önceden Var — Onarım Kaydı)

> **NE ZAMAN OKUNUR:** Vault link hijyeni, ADR link retarget'i veya dead-mark temizliği yapılacak her görevde bu rapor okunur; her yeni tarama sonunda güncellenir. v1 (2026-09-24) salt rapordu; v2 (2026-09-27) onarım kaydı: 22 retarget `fixed`. **v2.1 (2026-09-27): 9 `unresolved` hedeften 8'i `dead-marked (faz6-D)`, 1'i `blocked` (hedef dosya silinmiş).**

**Özet (2026-09-27):** v1 kaydı 34 satır → 6 dosyada 31 aday → onarım: **fixed 22 · dead-marked 8 (faz6-D) · blocked 1 · dışsal çözülen 16** → dead-mark sonrası taze tarama: **kırık link 0 / 6 dosya** (1 dosya git'ten silindi, `file-not-found`).

**Retarget desenleri:** (1) ADR → gerçek kayıt dosyası `[[.decisions/accepted/<slug>]]` (kısa slug'lar tam yola, `decisions/`→`.decisions/`); (2) kök registry → `[[../.ai/AGENTS.md]]`; (3) taşınan mimari → yeni k-yolu (`k11-ux/itcss-9-layer`); (4) eşdeğer kanıtlanmayan hedef → düz metin + **dead-mark etiketi `⚠️ DEAD (faz6-D): <eski hedef> — <neden>`** (hedef uydurulmadı, eşdeğer dosya üretilmedi — kapalı karar).

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

## B. Eşdeğersiz hedefler — `status: dead-marked (faz6-D)` (8) + `blocked` (1)

| Dosya | Satır | Hedef | Neden | Status |
|---|---|---|---|---|
| `.claude/CLAUDE.md` | 535 | `screens/B-home/dashboard-1920` | B-home dizini yok (`screens/` = shared + T01-T31); eşleştirilecek aynı ekran dosyası kanıtlanamadı | `dead-marked (faz6-D)` · 2026-09-27 |
| `.claude/CLAUDE.md` | 886 | `architecture/k0-k5-software/k5-data-layer/database_master` | `k0-k5-software` dizini yok; `mysql-18-database.md` içinde `database_master` geçmiyor — eşdeğer kanıtlanamadı | `dead-marked (faz6-D)` · 2026-09-27 |
| `.claude/CLAUDE.md` | 889 | `architecture/03-contracts/master-implementation-plan` | `03-contracts` dizini yok; ADR-087 dosyası da yok (yalnızca `.decisions/index.md` kaydı var) | `dead-marked (faz6-D)` · 2026-09-27 |
| `.opencode/CLAUDE.md` | 535,886,889 | aynı 3 hedef | hash-esit ayna — aynı nedenler | `dead-marked (faz6-D)` · 2026-09-27 |
| `assets.coremusic.net/AGENTS.md` | 78 | `../.ai/architecture/l3-presentation/js-module-architecture.md` | `l3-presentation` dizini yok; `js-module-architecture` repo genelinde yok | `dead-marked (faz6-D)` · 2026-09-27 |
| `home.coremusic.net/CLAUDE.md` | 127 | `../.ai/.subdomains/home.coremusic.net/index.md` | `.ai/.subdomains/` altında yalnızca `CLAUDE.md` var; `home.coremusic.net/` dizini + `index.md` yok | `dead-marked (faz6-D)` · 2026-09-27 |
| `assets.coremusic.net/Css copy/CLAUDE.md` | 24 | `../../.ai/architecture/l3-presentation/CLAUDE.md` | `l3-presentation` dizini yok; **ayrıca dosyanın kendisi 2026-09-27'de silinmiş** (`Css copy/` git staged-D; `Css/` altına taşınmadı) → etiket yazılamadı | `blocked` · file-removed 2026-09-27 |

> Kurallar gereği link **kaldırılmadı**; düz metin korundu ve yanına faz6-D etiketi eklendi: `⚠️ DEAD (faz6-D): <eski hedef> — <neden>` (8 satır). `blocked` satırda dosya diskte olmadığı için etiket uygulanamadı — hedef uydurulmadı, eşdeğer dosya üretilmedi (kapalı karar).

## C. Dışsal çözülen — 9/26 commit `9695a2e` (16 satır / 14 hedef)

v1'de (9/24) kırıkken 9/26'da ADR arşivinin `.ai/.decisions/accepted/` altına oluşması ve dosyaların yeniden düzenlenmesiyle kendiliğinden çözülenler:

`architecture/00-overview/architecture-master` · `decisions/accepted/ADR-038-8.1-...` · `decisions/accepted/ADR-040-...` · `decisions/accepted/ADR-042-...` · `decisions/accepted/ADR-044-...` · `.decisions/draft/ADR-089-classab-24v` · `ADR-040-database-authority` · `ADR-038-8.1-...` · `architecture/l0-infrastructure` · `decisions/accepted/ADR-043-...` · `ADR-044-...` · `architecture/05-data/database_master` · `architecture/06-audio/index` · `decisions/accepted/ADR-087-...`

- Sayım: v1 34 satır = 18 carry-over + 16 dışsal çözülen (hedef bazlı benzersiz: 14).
- Bugün 31 aday = 18 carry-over + `.opencode` 12 (v1 kapsamı dışındaydı, kural3 ile dahil) + 1 yeni hedef (`k0-k5-software/.../database_master` — dosya 9/26'da yeni yolla yazılmış).

## D. Doğrulama

- Onarım sonrası taze tarama (6 adaylı çözücü + kod bloğu strip): **6/6 dosya `broken: 0`**.
- **Dead-mark (faz6-D): 8/9 `dead-marked`** (2026-09-27): `.claude` 3 · `.opencode` 3 · `assets/AGENTS` 1 · `home/CLAUDE` 1 — **1 `blocked`**: `Css copy/CLAUDE.md` eşzamanlı başka bir işlemce silindi (git staged-D; `Css/` altına taşınmadı) → etiket yazılamadı.
- Dead-mark sonrası taze tarama: **6/6 dosya `broken: 0`** (`Css copy/CLAUDE.md` = `file-not-found`, skip edildi) — dead-mark metni link sayılmadı ✓.
- Writer verify: **4/4 OK** (BOM=0, mojibake=0, cjk=0, NUL=0); satır sayıları korundu (936/936/85/188).
- `.claude/CLAUDE.md` ↔ `.opencode/CLAUDE.md` hash eşit (dead-mark sonrası da).

## Kurallar

- Bu rapor salt-okunurdur; yeni düzeltme için ayrıca onay gerekir.
- Frozen ADR (001-037): yalnız link hedefi değişti, metin 0 edit.
- `blocked` 1 hedef (`l3-presentation/CLAUDE.md`): hedef dosya + sahip dosyası kayıp — sahip kararıyla geri getirilirse dead-mark tamamlanır.
- Dead-mark edilen 8 hedef: eşdeğer dosya üretilirse (onayla) etiket kaldırılıp retarget edilir.
- Figma token / secret bu rapora YAZILMAZ (REDACTED politikası).
