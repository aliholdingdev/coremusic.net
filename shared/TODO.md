---
title: "shared — TODO Listesi"
type: todo
category: project-tracking
version: 1.0.0
status: active
authority: SSOT
updated: 2026-10-23
---

# shared (Ortak Çekirdek) — TODO Listesi

**Zorunlu Bağlantılar:** [[PRD.md]] · [[CONTEXT.md]] · [[../TODO]] · [[../PRD.md]] · [[../.ai/TODO.md]]

**Kural (Zero-Hallucination):** Her madde PRD §7 / disk kanıtına dayanır; kanıtlanamayan madde yazılmaz. Durum: `[ ]` açık · `[x]` kapalı.

**eli10:** Ortak çekirdeğin kalan işleri: alt PRD bağlantıları, migrations/config sözleşmesi, DECOMMISSIONED envanter notu.
**eli15:** #9–#17, #23, #24 alt PRD'lerinin çoğu **diskte YOK** — bağlantı maddeleri o dosyalar üretilince kapanır (eksik-PRD listesi: `[[../TODO]]` §2).

## Açık Maddeler

- [ ] **Alt PRD bağlantıları** — #9–#17 (PageRouter · Middleware · Api · Security · Database · OAuth · Events · Cache · Session) bu dosyaya bağlanır (üretim durumuna göre) · Kaynak: `[[PRD.md]]` §7.1
- [ ] **#23 (`shared/database/migrations`) + #24 (`shared/config`) alt PRD'leri** — şema/konfigürasyon sözleşmesini derinleştirir (diskte YOK) · Kaynak: `[[PRD.md]]` §7.2
- [ ] **DECOMMISSIONED envanter notu** — AI · Bff modülleri için not `[[CONTEXT.md]]` §3.5'e taşınır; geri ekleme yalnız ADR ile · Kaynak: `[[PRD.md]]` §7.3

## Referanslar

- `[[PRD.md]]` (§7 Sonraki Adım) · `[[CONTEXT.md]]` · `[[WORKFLOW.md]]` · `[[AGENTS.md]]` · `[[CLAUDE.md]]` · `config/CLAUDE.md`
- Aile indeksi: `[[../TODO]]` · `[[../PRD.md]]` · Vault TODO: `[[../.ai/TODO.md]]`

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Last Updated:** 2026-10-23
