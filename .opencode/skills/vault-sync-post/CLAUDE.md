---
title: "CoreMusic - vault-sync-post Baglam"
type: context
folder: "C:\www\coremusic.net\.opencode\skills\vault-sync-post"
category: vault-management
date: 2026-09-20
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
---

# vault-sync-post - CLAUDE.md

**Zorunlu Baglantilar:** · [[../../.ai/CLAUDE.md]]

## 1. Baglam
Islem sonrasi otomatik vault guncelleme skill'i. Session kaydi, root .md dosyalari, .claude/.opencode senkronizasyonu.

## 2. Mevcut Durum
| Durum | Deger |
|-------|-------|
| Dosya | 1 (SKILL.md) |
| Konum | .opencode/skills/vault-sync-post |

## 3. Komsu Iliskiler
| Yon | Hedef | Iliski |
|-----|-------|--------|
| Parent | [[../../.ai/CLAUDE.md]] | Ust baglam (AI Anayasa) |
| Talimatlar | | Agent kurallari |
| Skill | [[./SKILL.md]] | Uygulama talimati |

## 4. Ilgili Scriptler
| Script | Amaç |
|--------|------|
| `.ai/scripts/session-save.mjs` | Session kaydetme — ⚠️ VERIFICATION REQUIRED — araç yok, senkronizasyon manuel |
| `.ai/scripts/vault-post-update.mjs` | Vault guncelleme — ⚠️ VERIFICATION REQUIRED — araç yok, senkronizasyon manuel |
| `.ai/scripts/vault-utf8-writer.mjs` | UTF-8 guvenli yazma |
| `.ai/scripts/vault-cmd.mjs` | Turkce komut arayuzu — ⚠️ VERIFICATION REQUIRED — araç yok, senkronizasyon manuel |

## 5. Kullanim Alanlari
- Gorev tamamlandiktan sonra
- Kod degisikligi yapildiktan sonra
- Vault dosyasi guncellendikten sonra
- Session sonlandirilirken
- /vault-post-update komutu ile

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-20
**Mode:** Red Team · Human Mode · Truth Mode
