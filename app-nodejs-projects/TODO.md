---
title: "app-nodejs-projects — TODO Listesi"
type: todo
category: project-tracking
version: 1.0.0
status: active
authority: SSOT
updated: 2026-10-23
---

# app-nodejs-projects — TODO Listesi

**Zorunlu Bağlantılar:** [[PRD.md]] · [[CONTEXT.md]] · [[../TODO]] · [[../PRD.md]] · [[../.ai/TODO.md]]

**Kural (Zero-Hallucination):** Her madde PRD §11 / disk kanıtına dayanır; kanıtlanamayan madde yazılmaz. Durum: `[ ]` açık · `[x]` kapalı.

**eli10:** Node.js projelerinin (turbo monorepo) kalan işleri: build kanıtı, vault ihlali, lock ayrımı, nova-search-engine coverage kapısı.
**eli15:** Maddeler `PRD.md §11`'den türetildi. `.ai/agent/` tekillik ihlali vault sahasıdır — doğrulama/kapatma vault sahibine aittir.

## Açık Maddeler (PRD §11 — 24–48h)

- [ ] **Build/test kanıtı** — `turbo typecheck && turbo run lint && pnpm test` çalıştırılıp sonuç kaydedilir · Kaynak: `[[PRD.md]]` §11.1
- [ ] **`.ai/agent/` tekillik ihlali** — doğrula/düzelt (vault sahası; `.ai/log.md:241` "novasearch AYRI PROJE" kuralı) · Kaynak: `[[PRD.md]]` §11.2
- [ ] **import-map-importer lock ayrımı** — `docs/CONTRIBUTING`'e işle · Kaynak: `[[PRD.md]]` §11.3

## Açık Maddeler (nova-search-engine)

- [ ] **Coverage kapısı** — son ölçüm %60.30 (kayıt: `nova-search-engine/docs/PRD.md`) vs hedef %75 → ölçüm/kapı kararı · Kaynak: `nova-search-engine/docs/PRD.md`
- [ ] **MySQL canlı test** — sqlite suite geçti; MySQL havuz canlı doğrulaması `⚠️ VERIFICATION REQUIRED` · Kaynak: `nova-search-engine/docs/PRD.md`

## Referanslar

- `[[PRD.md]]` (§11 Atılacak Adımlar) · `[[CONTEXT.md]]` · `[[AGENTS.md]]` · `[[CLAUDE.md]]`
- `[[nova-search-engine/docs/PRD]]` · `nova-search-engine/README.md` · `.ai/log` (novasearch ayrı proje)
- Aile indeksi: `[[../TODO]]` · `[[../PRD.md]]` · Vault TODO: `[[../.ai/TODO.md]]`

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Last Updated:** 2026-10-23
