---
title: "admin.coremusic.net — TODO Listesi"
type: todo
category: project-tracking
version: 1.0.0
status: active
authority: SSOT
updated: 2026-10-23
---

# admin.coremusic.net — TODO Listesi

**Zorunlu Bağlantılar:** [[PRD.md]] · [[CONTEXT.md]] · [[../TODO]] · [[../PRD.md]] · [[../.ai/TODO.md]]

**Kural (Zero-Hallucination):** Her madde PRD §7 / disk kanıtına dayanır; kanıtlanamayan madde yazılmaz. Durum: `[ ]` açık · `[x]` kapalı.

**eli10:** Admin panelinin kalan işleri: debug kapısı production kontrolü, rate-limit kanıtı, cache dosyası kararı.
**eli15:** Maddeler `PRD.md §7.2`'den türetildi; aile indeksi satırı (#29) bu turda kapandı. Kapanışta her maddeye kaynak (commit/dosya/ölçüm) eklenir.

## Açık Maddeler

- [ ] **`/dev-login` production teyidi** — `DEBUG_MODE` production'da `false` teyidi + route erişilemezlik kanıtı → Security denetimi · Kaynak: `[[PRD.md]]` §7.2 · Risk §6-1 (CRITICAL)
- [ ] **Rate-limit uygulama kanıtı** — login/alias rotalarında rate-limit canlı kanıtı yok → `⚠️ VERIFICATION REQUIRED` · Kaynak: `[[PRD.md]]` §7.2
- [ ] **`.phpunit.result.cache` gitignore kararı** — ignore mu tracked mı (DevOps kararı) · Kaynak: `[[PRD.md]]` §7.2

## Kapanan Maddeler

- [x] **Aile indeksi satırı #29** — kök `[[../PRD.md]]` §5 tablosuna eklendi; indeks sürümü 1.2.0 · KAPANDI 2026-10-23

## Referanslar

- `[[PRD.md]]` (§6 Risk · §7 Sonraki Adım) · `[[CONTEXT.md]]` · `[[WORKFLOW.md]]` · `[[AGENTS.md]]` · `[[CLAUDE.md]]`
- Aile indeksi: `[[../TODO]]` · `[[../PRD.md]]` · Vault TODO: `[[../.ai/TODO.md]]`

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Last Updated:** 2026-10-23
