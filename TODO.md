---
title: "CoreMusic — Kök TODO Aile İndeksi"
type: todo
category: project-tracking
version: 1.0.0
status: active
authority: SSOT
updated: 2026-10-23
---

# CoreMusic — Kök TODO Aile İndeksi

**Zorunlu Bağlantılar:** [[PRD.md]] · [[CLAUDE.md]] · [[AGENTS.md]] · [[.ai/TODO.md]] · [[.ai/PRD.md]]

**Kural (Zero-Hallucination):** Her madde disk kanıtına dayanır; kanıtlanamayan madde yazılmaz. Durum: `[ ]` açık · `[x]` kapalı.

**eli10:** Bu dosya, her yüzeyin TODO.md'sine giden merkezi dizindir — PRD aile indeksi (`PRD.md` §5) ile aynı mantık.
**eli15:** Yüzey seviyesi maddeler kendi yüzey TODO'sunda; vault/depо geneli maddeler [[.ai/TODO.md]] §2–§4'te. Bir madde iki yerdeyse kapanışta ikisi de işaretlenir. Yüzey TODO'su maddeyi kendi PRD §7/§11'inden türetir — burada madde tekrarı yapılmaz.

---

## §1 Yüzey TODO Aile İndeksi

| # | Yüzey | TODO | PRD | Açık madde |
|---|---|---|---|---|
| — | **kök (bu dosya)** | `TODO.md` | [[PRD.md]] | 3 |
| 1 | admin.coremusic.net | [[admin.coremusic.net/TODO]] | [[admin.coremusic.net/PRD]] | 3 |
| 2 | api.coremusic.net | [[api.coremusic.net/TODO]] | [[api.coremusic.net/PRD]] | 3 |
| 3 | assets.coremusic.net | [[assets.coremusic.net/TODO]] | [[assets.coremusic.net/PRD]] | 3 |
| 4 | auth.coremusic.net | [[auth.coremusic.net/TODO]] | [[auth.coremusic.net/PRD]] | 3 |
| 5 | home.coremusic.net | [[home.coremusic.net/TODO]] | [[home.coremusic.net/PRD]] | 3 |
| 6 | media.coremusic.net | [[media.coremusic.net/TODO]] | [[media.coremusic.net/PRD]] | 3 |
| 7 | shared | [[shared/TODO]] | [[shared/PRD]] | 3 |
| 8 | app-nodejs-projects | [[app-nodejs-projects/TODO]] | [[app-nodejs-projects/PRD]] | 5 |
| 9 | app-cpp-projects | [[app-cpp-projects/TODO]] | [[app-cpp-projects/PRD]] | 3 |

> Modül seviyesi alt PRD'ler (`assets/js/*`, `shared/config`, `nova-search-engine/docs` …) kendi yüzey TODO'su üzerinden izlenir; ayrı TODO.md'si yoktur.

---

## §2 Kök Seviye Açık Maddeler

- [ ] **6 eksik PRD (#22–#27):** kök `[[PRD.md]]` §5 tablosunda satır var ama dosya diskte **YOK** — `auth/include` · `shared/database/migrations` · `shared/config` · `.ai/.sql` · `.ai/ui-design` · `.ai/.decisions` → üretilmeli (üretim sonunda ilgili yüzey TODO'suna bağlanır) · Kaynak: glob 2026-10-23
- [ ] **Yüzey doküman turu + TODO ailesi commit bekliyor:** 16 PRD + revizyonlar (kök `PRD.md` v1.2.0 · `.ai/PRD.md` v1.0.1 · `admin/PRD.md` §11 · `.ai/index.md`) + bu TODO ailesi (bu dosya + 9 yüzey TODO + `.ai/TODO.md` v1.4.0) — commit orkestratörde · Kaynak: `git status` 2026-10-23
- [ ] **Neva PRD commit bekliyor** — vault kopyaları + kök `PRD.md` §5.1 + `AGENTS.md` §9 … (detay: `[[.ai/TODO.md]]` §3) · Kaynak: `git status` 2026-10-23

---

## §3 Çalışma Kuralı

1. Yeni iş doğar → önce ilgili **yüzey TODO'suna** yazılır (kanıt satırıyla); kök/seviye atlama yalnız depo geneli işler içindir.
2. Madde kapanır → `[x]` + kanıt (commit/dosya/ölçüm) + tarih; PRD §4 kapı maddesi de güncellenirse PRD sürümü bump edilir.
3. Vault/depо geneli madde → `[[.ai/TODO.md]]` §2–§4 (SSOT); bu dosya yalnız indeks + kök maddeler.

---

## Referanslar

- PRD aile indeksi: `[[PRD.md]]` (§5 · §5.1 Neva) · Vault TODO SSOT: `[[.ai/TODO.md]]` · Vault PRD: `[[.ai/PRD.md]]`
- Boot: `[[CLAUDE.md]]` · `[[AGENTS.md]]` · Süreç: `[[.ai/WORKFLOW.md]]`

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Last Updated:** 2026-10-23
