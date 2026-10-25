---
title: "app-nodejs-projects — TODO Listesi"
type: todo
category: project-tracking
version: 1.2.0
status: active
authority: SSOT
updated: 2026-10-25
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

## TAM Madde Listesi — Küçükten Büyüğe (Geçmiş · Açık · Gelecek) — 2026-10-25

> Tek doğruluk listesi; `## Geçmiş & Mikro Ölçek` kanıt detayıdır. Sıra: MICRO → ORTA → EPIC.

### A. GEÇMİŞ — TAMAMLANAN
- [x] MICRO: kök lock tek kaynak + `apps/import-map-importer` ayrı workspace kuralı yazıldı (AGENTS §3)
- [x] MICRO: `.ai/log.md:241` "novasearch AYRI PROJE" kaydı
- [x] ORTA: sqlite test suite geçti (kayıt: `nova-search-engine/docs/PRD.md`)
- [x] ORTA: turbo monorepo + pnpm lock yapısı + strict TS kuralları (AGENTS §1-§3)
- [x] EPIC: nova-search-engine coverage ölçümü **%60.30** (kayıt: nova PRD — hedef %75)

### B. AÇIK — EKSİK / YARIM (küçükten büyüğe)
- [ ] MICRO: P-1a — `turbo typecheck` → 0 hata
- [ ] MICRO: P-1b — `turbo run lint` → 0
- [ ] MICRO: P-4a — `pnpm test --coverage` → %60.30 doğrulaması
- [ ] MICRO: P-2a — `.ai/agent/` tekillik dosya listesini çıkar → MO raporu (vault sahası)
- [ ] MICRO: gRPC `gateway-proto` safeguard (log notu) — `generateText`'e indirgeme kuralı korunuyor mu denetimi
- [ ] ORTA: P-1 **Build/test kanıtı** → PRD §11.1 (`turbo typecheck && turbo run lint && pnpm test`)
- [ ] ORTA: P-3 **import-map-importer lock ayrımı** → `docs/CONTRIBUTING`'e işle
- [ ] ORTA: P-4 **Coverage kapısı** %60.30 vs hedef %75 → ölçüm/kapı kararı
- [ ] ORTA: P-5 **MySQL canlı test** (sqlite geçti; MySQL havuz `⚠️ VERIFICATION REQUIRED`) — server PC
- [ ] EPIC: coverage %75 + Playwright E2E yeşilliği (PROJECTS §11 test stratejisi)

### C. GELECEK — SIRADAKİ BÜYÜK İŞLER (öncelik sırası)
| Sıra | EPIC iş | İlk MICRO adım |
|---|---|---|
| 1 | P-1 turbo gate yeşilliği | 3 komut (typecheck/lint/test) |
| 2 | P-4 coverage kararı | `pnpm test --coverage` |
| 3 | P-3 lock notu | CONTRIBUTING editi |
| 4 | P-5 MySQL canlı (server PC) + %75 kapısı | coverage raporu |## Adım Adım Yürütme (2026-10-25 güçlendirme)

**Kural:** Her adım tek oturum · `[ ]` → çalış · `[x]` + kanıt → kapanır · **YAPMA** bağlayıcı · komutu gönder → agent yapar.

| # | Adım | Ne yapılacak (kaynak) | YAPMA | Hazır komut |
|---|---|---|---|---|
| P-1 | [ ] | **Build gate** — `turbo typecheck && turbo run lint && pnpm test` çalıştır, çıktıyı PRD §11.1'e yaz | kapı geçmeden "bitti" | `P-1: turbo typecheck + lint + pnpm test çalıştır, 0 hata kanıtını app-nodejs/PRD §11.1'e yaz` |
| P-2 | [ ] | `.ai/agent/` tekillik ihlali — doğrula/düzelt (vault sahası, MO ile) | vault dosyasını tek başına silme | `P-2: .ai/agent/ tekillik ihlalini doğrula — .ai/log.md:241 "novasearch AYRI PROJE" kuralına göre raporla` |
| P-3 | [ ] | `import-map-importer` lock ayrımı → `docs/CONTRIBUTING`'e işle | kök lock'u birleştirme | `P-3: import-map-importer ayrı workspace lock kuralını CONTRIBUTING'e işle` |
| P-4 | [ ] | Coverage kapısı — %60.30 vs hedef %75 → ölçüm/kapı kararı | hedefi kendine göre düşürme | `P-4: nova-search-engine coverage ölç, %60.30 doğrulaması + kapı kararını PRD'ye yaz` |
| P-5 | [ ] | MySQL canlı test (sqlite geçti; MySQL havuz `⚠️ VR`) — server PC'de | dev makinede "geçti" iddiası | `P-5: MySQL canlı havuz testini server PC'de çalıştır, kanıtı PRD'ye yaz` |

**Yüzey yasakları:** `any`/`require` · `shared/types` dışında kopya tip · ters bağımlılık (apps→shared→packages) · `php-backend-standards` (geçersiz) · commit.
## Geçmiş & Mikro Ölçek — Tam Liste (2026-10-25)

**Geçmiş — tamamlanan (kanıtlı):**
- [x] `[ORTA]` sqlite test suite geçti (kayıt: `nova-search-engine/docs/PRD.md`)
- [x] `[ORTA]` `.ai/agent/` "novasearch AYRI PROJE" kuralı kayıtlı — `.ai/log.md:241`
- [x] `[MİKRO]` kök lock tek kaynak + `apps/import-map-importer` ayrı workspace kuralı yazıldı (AGENTS §3)

**Mikro işler (en küçük → büyük):**
- [ ] `[MİKRO]` P-1 altı: `turbo typecheck` tek komut → 0 hata çıktısı
- [ ] `[MİKRO]` P-1 altı: `turbo run lint` → 0
- [ ] `[MİKRO]` P-4 altı: `pnpm test -- --coverage` → %60.30 doğrulaması
- [ ] `[ORTA]` P-3 lock notu CONTRIBUTING'e → `[EPİK]` P-1+P-4 kapıları yeşil → MySQL canlı (server PC)## Referanslar

- `[[PRD.md]]` (§11 Atılacak Adımlar) · `[[CONTEXT.md]]` · `[[AGENTS.md]]` · `[[CLAUDE.md]]`
- `[[nova-search-engine/docs/PRD]]` · `nova-search-engine/README.md` · `.ai/log` (novasearch ayrı proje)
- Aile indeksi: `[[../TODO]]` · `[[../PRD.md]]` · Vault TODO: `[[../.ai/TODO.md]]`

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Last Updated:** 2026-10-23
