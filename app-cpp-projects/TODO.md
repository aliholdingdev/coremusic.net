---
title: "app-cpp-projects — TODO Listesi"
type: todo
category: project-tracking
version: 1.0.0
status: active
authority: SSOT
updated: 2026-10-23
---

# app-cpp-projects — TODO Listesi

**Zorunlu Bağlantılar:** [[PRD.md]] · [[CONTEXT.md]] · [[../TODO]] · [[../PRD.md]] · [[../.ai/TODO.md]]

**Kural (Zero-Hallucination):** Her madde PRD §11 / disk kanıtına dayanır; kanıtlanamayan madde yazılmaz. Durum: `[ ]` açık · `[x]` kapalı.

**eli10:** C++/Neva projelerinin kalan işleri: taşıma kararı, build kanıtı, kararın PRD'lere işlenmesi.
**eli15:** `app-cpp-projects/` klasörü doküman-only; C++ kodu `--/neva-engine/`'dedir (commit `9fd9991`). `.ai/TODO.md` §2'deki "sınıflandır (163 dosya)" maddesi ile bu dosya eşgüdümlüdür — kod taşındığı için madde yeniden ölçülmeli.

## Açık Maddeler (PRD §11 — 24–48h)

- [ ] **Taşıma TODO'su kapatma kararı** — `.ai/PLAN.md:330` ve mimari §19 "kullanıcı onayı ⏳" → kapat/karar ver (kullanıcı) · Kaynak: `[[PRD.md]]` §11.1
- [ ] **Build/test kanıtı** — `cmake` + `ctest` çalıştırılıp sonuç kaydedilir · Kaynak: `[[PRD.md]]` §11.2
- [ ] **Karar sonucunun işlenmesi** — bu PRD §5 + kök `[[../PRD.md]]`'ye yansıtılır · Kaynak: `[[PRD.md]]` §11.3

## İlişkili Vault Maddesi

- `[[../.ai/TODO.md]]` §2 "`app-cpp-projects/` sınıflandır" — kod `--/neva-engine/`'e taşınmış (`9fd9991`) → madde yeniden ölçülüp kapatılabilir · Kaynak: `git log` + `ls -R` 2026-10-23

## Referanslar

- `[[PRD.md]]` (§11 Atılacak Adımlar) · `[[CONTEXT.md]]` · `[[AGENTS.md]]` · `[[CLAUDE.md]]`
- `.ai/projects/NevaEngine/coremusic-neva-engine-prd.md` (SSOT) · `.ai/PLAN.md:330` · `[[../PRD.md]]` §5.1
- Aile indeksi: `[[../TODO]]` · Vault TODO: `[[../.ai/TODO.md]]`

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Last Updated:** 2026-10-23
