---
title: ".opencode — Product Requirements Document"
type: prd
category: surface
version: 1.0.0
status: active
authority: "SSOT: .ai/PRD.md — Yüzey envanteri @.opencode/CONTEXT.md (ölçüm 2026-10-12)"
updated: 2026-10-23
---
# .opencode — PRD

> **Yüzey:** OpenCode V2 çalışma ortamı yüzeyi (anayasa kopyası, rol/routing,
> yapılandırma, subagent/komut tanımları). **Şablon:** 15 başlık. Kanıt yoksa
> `⚠️ VERIFICATION REQUIRED`.

## 1. Amaç

OpenCode'un bu projedeki oturumlarını tekrarlanabilir kılmak: boot dosyaları,
rol/routing, yapılandırma yedekleri ve agent/komut envanteri tek klasörde,
ölçümle ve otorite zinciriyle (SSOT `.ai/`) durur.

## 2. Kapsam

- ✅ `CLAUDE.md` (anayasa kopyası) · `AGENTS.md` (rol/routing, 313 satır) ·
  `WORKFLOW.md` (adım→kapı, 307 satır) · `CONTEXT.md` (envanter)
- ✅ `opencode.json` + 6 yedek · `package.json` / `package-lock.json`
- ✅ `agents/` (17 subagent) · `command/` (10 komut) · `.workflows/` (3 akış) ·
  `rules/` (boş) · `node_modules/` (3.667 dosya / 270 dizin)
- ❌ `.ai/` vault içeriği (SSOT — yalnız ihtiyaç anında okunur) · ❌ `git commit` ·
  ❌ `.md` içine secret

## 3. Kullanıcı Hikâyeleri

- **US-1** — *OpenCode:* "Her oturum aynı boot sırasıyla başlasın" → CLAUDE.md/AGENTS.md/WORKFLOW.md.
- **US-2** — *tooling:* "Yeni subagent/komut ekleyeyim, envanter şaşmasın" → `agents/`, `command/` + CONTEXT revizyonu.
- **US-3** — *Security:* "`opencode.json` sızıntısız olsun" → sır denetimi; `.bak` dosyaları değiştirilmez.

## 4. Gereksinimler

| # | Gereksinim | Öncelik |
|---|---|---|
| G-1 | Boot üçlüsü (CLAUDE/AGENTS/WORKFLOW) geçerli ve authority bağlanır | P0 |
| G-2 | `opencode.json` yapılandırması + 6 yedek salt-okunur | P0 |
| G-3 | `agents/` 17 + `command/` 10 envanteri ölçümle taze | P1 |
| G-4 | `skills/` bu klasörde YOK (tek ev `.claude/skills` — 2026-10-07 kapanış) | P0 |
| G-5 | `node_modules` karantinası (3. paket kodu okunmaz/üretilmez) | P1 |

## 5. Kabuk Şema (envanter — ölçüm 2026-10-12)

```text
.opencode/  14 kök dosya, 5 alt dizin
  CLAUDE.md · AGENTS.md (313 satır) · WORKFLOW.md (307) · CONTEXT.md
  opencode.json + .bak ×6 (2026-08-15 → 2026-10-01)
  agents/ 17 · command/ 10 · .workflows/ 3 · rules/ (boş) · node_modules/ 3667 dosya
  skills/ → DISKTE YOK (tek ev: .claude/skills)
```

## 6. Öncelik Sırası (P0 → P3)

`P0` boot + yapılandırma · `P1` envanter tazeliği · `P2` komut/agent genişlemesi ·
`P3` yedek arşivleme (silme YOK).

## 7. Kabul Kriterleri

1. Kök envanter = disk (14 dosya / 5 alt dizin; değiştiyse CONTEXT revize + tarih).
2. `skills/` iddiası dosyada **0** (kapanış 2026-10-07 korunur).
3. `.bak` dosyaları 6/6 yerinde, değişmedi.
4. `opencode.json` içeriğinde görünür secret **0**.

## 8. Dış Bağımlılıklar

OpenCode V2 runtime · npm (package-lock) · `.ai/` vault (SSOT) · kök `AGENTS.md`.

## 9. Riskler

- 🟡 `rules/` boş → kural dosyası eksik beklentisi olabilir (beklenen dur: kural
  kök/vault'ta, `⚠️ VERIFICATION REQUIRED` yalnız yeni eklemede).
- 🟡 6 yedek zamanla karışırsa yanlış `.bak` geri yüklenir → değiştirilemez etiketi korunur.
- 🟡 `.workflows/` (3) ile kök `.workflows/` (11 dosya) kafa karışıklığı → CONTEXT ayrımı korur.

## 10. Başarı Ölçütleri

boot 3/3 geçerli · envanter ölçüm tarihli · yedek 6/6 dokunulmaz · secret 0.

## 11. Atılacak Adımlar (24–48h)

1. Envanteri yeniden ölç (14 kök dosya / 5 dizin) — fark varsa CONTEXT revize.
2. `opencode.json` sır denetimi (Security).
3. Bu PRD'yi kök `PRD.md` §5 tablosuna satır olarak ekle (orkestratör akışı).

## 12. Referanslar

`[[CONTEXT]]` · `[[AGENTS.md]]` · `[[WORKFLOW.md]]` · `opencode.json` ·
`@.ai/AGENTS.md` · `@PRD.md` (aile indeksi) · kök `CLAUDE.md` §Skill Registry.

## 13. Sürüm Geçmişi

| Sürüm | Tarih | Değişiklik |
|---|---|---|
| 1.0.0 | 2026-10-23 | İlk sürüm (CONTEXT envanteri 2026-10-12 kanıtı) |

## 14. Ekipler / Sahiplik

MO (boot + envanter) · tooling-engineer (command/agents üretimi) ·
Security (`opencode.json` sır denetimi) · DevOps (paket/lock).

## 15. Onay

**Onaylamayan sorumlu:** MO · **Onay tarihi:** 2026-10-23 ·
**Karar mercii:** Kullanıcı (yapılandırma/onay değişiklikleri).
