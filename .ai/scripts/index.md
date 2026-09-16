---
title: "CoreMusic — Scripts Index"
type: system
category: automation
date: 2026-09-04
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/scripts/index.md"
  source_of_truth:
    - ".ai/CLAUDE.md"
    - ".ai/AGENTS.md"
---

# CoreMusic — Scripts Index

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[index.md]]

---

## 1. Amaç

CoreMusic ekosistemindeki tüm otomasyon scriptlerinin ve yardımcı araçların indeksidir.

---

## 2. Vault Scriptleri

| Script | Konum | Amaç |
|--------|-------|------|
| vault-utf8-writer.mjs | `.ai/scripts/vault-utf8-writer.mjs` | UTF-8 güvenli vault yazma aracı (append / insert-before-marker / write / copy / verify / repair / scan). Vault Updater zorunlu yazma kanalı — PowerShell dosya YAZMA cmdlet'leri yasak (2026-09-08). repair: CP1254 ham baytları yedek alarak UTF-8'e çevirir |
| vault-cmd.mjs | `.ai/scripts/vault-cmd.mjs` | Türkçe komut arayüzü (yazım-hatasi toleranslı, Levenshtein ≤ 2): ls/dir, type/oku, kg/ara, chk/dogrula salt-okunur; ekle/yaz/onar/tara utf8-writer'a devreder. Çıktılar Türkçe (2026-09-08) |
| session-save.mjs | `.ai/scripts/session-save.mjs` | Session durumu kaydetme aracı — `.ai/sessions/` dizinine session dosyası oluşturur, MEMORY.md §18/§20 günceller, log.md'ye audit trail ekler. Kullanım: `node session-save.mjs --task "..." --status completed|partial|failed --agent <adi>` |
| vault-post-update.mjs | `.ai/scripts/vault-post-update.mjs` | İşlem sonrası vault güncelleme aracı — 12 root .ai dosyasını günceller, .claude/.opencode senkronizasyonunu yapar, wiki-link doğrulaması çalıştırır. Kullanım: `node vault-post-update.mjs [--scope root|full] [--dry-run]` |
| ~~vault-integrity-check.ps1~~ | — | Kayıp; işlevi `vault-utf8-writer.mjs verify` modu + vault-check komutu üzerine alındı |

---

## 3. CI/CD Scriptleri

| Script | Konum | Amaç |
|--------|-------|------|
| — | — | — |

---

## 4. Geliştirme Scriptleri

| Script | Konum | Amaç |
|--------|-------|------|
| — | — | — |

---

## 5. Cross References

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 2 Vault | [[WORKFLOW.md]] §8.7 | Vault sync |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-08
**Mode:** Red Team · Human Mode · Truth Mode
