---
title: "auth.coremusic.net — TODO Listesi"
type: todo
category: project-tracking
version: 1.0.0
status: active
authority: SSOT
updated: 2026-10-23
---

# auth.coremusic.net — TODO Listesi

**Zorunlu Bağlantılar:** [[PRD.md]] · [[CONTEXT.md]] · [[../TODO]] · [[../PRD.md]] · [[../.ai/TODO.md]]

**Kural (Zero-Hallucination):** Her madde PRD §7 / disk kanıtına dayanır; kanıtlanamayan madde yazılmaz. Durum: `[ ]` açık · `[x]` kapalı.

**eli10:** Auth'un kalan işleri: include akışı sözleşmesi, SSO/RBAC çapraz kontrol, Security denetimi.
**eli15:** #22 (`auth/include` alt PRD) **diskte YOK** — bağlama maddesi o dosya üretilince kapanır (bkz. `[[../TODO]]` §2). Security bulgusu varsa giderilmeden kapı kapanmaz.

## Açık Maddeler

- [ ] **#22 (`auth/include` PRD — diskte YOK) ile akış sözleşmesi** — Controller→Handler→Service→Repository derinleştirme · Kaynak: `[[PRD.md]]` §7.1
- [ ] **SSO/RBAC çapraz kontrolü** — #1 (kök) ve #5 (home) PRD'leriyle akış doğrulaması (PRD-PLT-004) · Kaynak: `[[PRD.md]]` §7.2
- [ ] **Security denetimi** — sonucu bu PRD §4 kapısına eklenir; açık bulgu varsa giderilir · Kaynak: `[[PRD.md]]` §7.3

## Referanslar

- `[[PRD.md]]` (§7 Sonraki Adım) · `[[CONTEXT.md]]` · `[[WORKFLOW.md]]` · `[[AGENTS.md]]` · `[[CLAUDE.md]]`
- Aile indeksi: `[[../TODO]]` · `[[../PRD.md]]` · Vault TODO: `[[../.ai/TODO.md]]`

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Last Updated:** 2026-10-23
