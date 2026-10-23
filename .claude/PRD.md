---
title: ".claude — Product Requirements Document"
type: prd
category: surface
version: 1.0.0
status: active
authority: "SSOT: .ai/PRD.md — Yüzey envanteri @.claude/CONTEXT.md (ölçüm 2026-10-21)"
updated: 2026-10-23
---
# .claude — PRD

> **Yüzey:** Claude Code araç-kurulum yüzeyi (boot/izin/MCP ayarları, kural ve
> yetenek envanteri). **Şablon:** 15 başlık. Kanıt yoksa `⚠️ VERIFICATION REQUIRED`.

## 1. Amaç

Claude Code'ın bu projedeki davranışını tek klasörde, ölçümlü envanterle
sürdürmek: boot sırası, izin sınırı, hook kapıları, skill/subagent/command
envanteri ve vault↔disk çelişki kaydı.

## 2. Kapsam

- ✅ `settings.json` / `settings.local.json` / `mcp.json` (anahtarlar REDACTED)
- ✅ `agent/` (118) · `command/` (24) · `skills/` (13 dizin / 134 dosya, 13
  `SKILL.md`) · `hooks/` (3 betik) · `rules/` (1) · `plans/`
- ✅ CLAUDE.md / AGENTS.md / WORKFLOW.md / CONTEXT.md (kural-rol-süreç-envanter)
- ❌ `.ai/` vault içeriği (SSOT — burada üretilmez) · ❌ `C:\.claude\skills`
  (erişim REDDEDİLDİ → UNKNOWN) · ❌ `git commit`

## 3. Kullanıcı Hikâyeleri

- **US-1** — *Claude Code:* "Aynı okuma sırasıyla boot olayım" → boot zinciri
  CONTEXT.md §boot.
- **US-2** — *MO:* "Kaç skill/subagent var, vault ile disk tutuşuyor mu?"
  → §envanter + §çelişki kaydı.
- **US-3** — *Security:* "Sırlar `.md`'de mi?" → settings içeriği REDACTED.

## 4. Gereksinimler

| # | Gereksinim | Öncelik |
|---|---|---|
| G-1 | Boot/izin/MCP ayarlarının tek yerde ve geçerli olması | P0 |
| G-2 | Envanter sayılarının ölçümle (tarihli) durması | P0 |
| G-3 | 3 hook kapısı: `skill-mandate` · `ai-risk-gate` · `ai-validate-post` | P0 |
| G-4 | Vault↔disk çelişkilerinin işaretlenmesi (düzeltme yalnız onayla) | P1 |
| G-5 | Yeni skill'in `.ai/.templates/` şablonundan üretilmesi (Guardrail #16) | P1 |

## 5. Kabuk Şema (envanter — ölçüm 2026-10-21)

```text
.claude/  12 kök girdi = 6 dosya + 6 dizin · recursive 287 dosya / 50 dizin
  settings.json → hooks: UserPromptSubmit · PreToolUse(Edit|Write) · PostToolUse(Edit|Write)
  agent/ 118 · command/ 24 · skills/ 13 dizin/134 dosya (13 SKILL.md, v3.0)
  hooks/ 3 (.cjs) · rules/ 1 (skill-usage.md) · plans/ (geçici)
```

## 6. Öncelik Sırası (P0 → P3)

`P0` boot + hook kapıları · `P1` envanter/çelişki tazeliği · `P2` skill
yenilemeleri · `P3` plans/ temizliği.

## 7. Kabul Kriterleri

1. Envanter sayıları son ölçüm tarihiyle diskte eşit (2026-10-21: 13/134 vb.).
2. 3 hook olayı `settings.json`'da bağlı ve betikler `.cjs` mevcut.
3. Sekiz eski sayı (ör. "12 dizin/153 dosya") çelişki tablosunda işaretli.
4. Secret içeren `.md` yazımı **0** (anahtarlar REDACTED).

## 8. Dış Bağımlılıklar

Claude Code runtime · `.ai/` vault (SSOT boot listesi) · kök `CLAUDE.md`
§Skill Registry · `.ai/.templates/` (skill şablonu).

## 9. Riskler

- 🟡 Envanter eskiyince vault↔disk farkı gizlenir (2026-10-21 düzeltmesi: "12/153" → "13/134").
- 🟡 `C:\.claude\skills` erişimi REDDEDİLDİ → global kütüphane UNKNOWN.
- 🟡 `.claude/AGENTS.md` gibi dosyalarla kök `.claude/AGENTS.md` (Proje) karmaşası → yetki belirsizliği.

## 10. Başarı Ölçütleri

envanter taze (ölçüm tarihli) · hook 3/3 bağlı · çelişki kaydı açık · secret 0.

## 11. Atılacak Adımlar (24–48h)

1. Envanteri yeniden ölç (12 kök girdi / recursive sayım) — değiştiyse CONTEXT revize.
2. `settings.json` hook bağlantılarını doğrula.
3. Bu PRD'yi kök `PRD.md` §5 tablosuna satır olarak ekle (orkestratör akışı).

## 12. Referanslar

`[[CONTEXT]]` · `[[AGENTS.md]]` · `[[WORKFLOW.md]]` · `rules/skill-usage.md` ·
`@.ai/AGENTS.md` · `@PRD.md` (aile indeksi) · `@CLAUDE.md` §Skill Registry.

## 13. Sürüm Geçmişi

| Sürüm | Tarih | Değişiklik |
|---|---|---|
| 1.0.0 | 2026-10-23 | İlk sürüm (CONTEXT envanteri 2026-10-21 kanıtı) |

## 14. Ekipler / Sahiplik

MO (envanter + vault-sync) · kullanıcı (ayar onayı) · Security (secret denetimi) ·
domain agent (skill içeriği).

## 15. Onay

**Onaylamayan sorumlu:** MO · **Onay tarihi:** 2026-10-23 ·
**Karar mercii:** Kullanıcı (ayar/izin değişiklikleri).
