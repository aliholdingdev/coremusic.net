---
title: "admin.coremusic.net — TODO Listesi"
type: todo
category: project-tracking
version: 1.2.0
status: active
authority: SSOT
updated: 2026-10-25
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

## TAM Madde Listesi — Küçükten Büyüğe (Geçmiş · Açık · Gelecek) — 2026-10-25

> Tek doğruluk listesi; `## Geçmiş & Mikro Ölçek` kanıt detayıdır. Sıra: MICRO → ORTA → EPIC.

### A. GEÇMİŞ — TAMAMLANAN
- [x] MICRO: db-access-guard CI gate (ADR-002) — `2513de8`
- [x] MICRO: aile indeksi satırı #29 → kök `PRD.md` §5 (v1.2.0) — 2026-10-23
- [x] ORTA: `AdminMarkupContractTest` + ADR-058 markup sözleşmesi koruması (auth formları)
- [x] ORTA: admin PRD (PRD-ADM-001..007 + §11 Risk §6-1) — `a44c567`
- [x] ORTA: `/dev-login` yalnız DEBUG koşulu kodda mevcut (`bootstrap.php:91-105`) — kanıt satırı hazır
- [x] EPIC: Backend Adım 11 dropdown rol-bazlı sunucu filtresi (header) — `f3a6cc9`

### B. AÇIK — EKSİK / YARIM (küçükten büyüğe)
- [ ] MICRO: N-1a — `.env`/config `DEBUG_MODE` satırını oku → değer + dosya:satır notu
- [ ] MICRO: N-3a — `.phpunit.result.cache` tracked mı → `git ls-files` (1 komut)
- [ ] MICRO: N-2a — login rotasına 6 istek at → 429 gelmediyse kanıt log'u
- [ ] MICRO: dashboard tek sorgusu (`SELECT COUNT(*) FROM admin_users`, `admin-dashboard.php:36`) → yeni sorgu eklenirse Data handover hatırlatması
- [ ] ORTA: N-1 **CRITICAL — `/dev-login` production teyidi** (DEBUG_MODE false + 404/403 kanıtı) → PRD §6-1 Risk
- [ ] ORTA: N-2 **Rate-limit canlı kanıtı** (login/alias) → `⚠️ VERIFICATION REQUIRED` kapat
- [ ] ORTA: N-3 `.phpunit.result.cache` gitignore kararı (DevOps)
- [ ] ORTA: N-4 bekleyen diff (dashboard + markup test + `web.config`) → phpunit yeşil → commit taslağı
- [ ] EPIC: Security denetimi kapısı → PRD §6-1 CRITICAL Risk kapanışı

### C. GELECEK — SIRADAKİ BÜYÜK İŞLER (öncelik sırası)
| Sıra | EPIC iş | İlk MICRO adım |
|---|---|---|
| 1 | N-1 CRITICAL backdoor kapısı | `DEBUG_MODE` okuma |
| 2 | N-2 429 kanıtı | 6 istek testi |
| 3 | N-4 commit paketi | phpunit yeşil |
| 4 | Prod güvenlik denetimi + pentest | audit raporu |## Adım Adım Yürütme (2026-10-25 güçlendirme)

**Kural:** Her adım tek oturum · `[ ]` → çalış · `[x]` + kanıt → kapanır · **YAPMA** bağlayıcı · komutu gönder → agent yapar.

| # | Adım | Ne yapılacak (kaynak) | YAPMA | Hazır komut |
|---|---|---|---|---|
| N-1 | [ ] | **CRITICAL** — `/dev-login` production teyidi: `DEBUG_MODE=false` + route erişilemezlik kanıtı (`bootstrap.php:91-105`) | DEBUG'i prod'da açma / backdoor'u silmeden "geçti" deme | `N-1: /dev-login production kanıtını al — DEBUG_MODE false + erişim 404/403 kanıtını PRD §6-1 Risk'e yaz` |
| N-2 | [ ] | Rate-limit canlı kanıtı (login/alias rotaları) → `⚠️ VR` kapat | kanıtsız kapatma | `N-2: login/alias rate-limit canlı testini çalıştır, 429 kanıtını PRD §7.2'ye yaz` |
| N-3 | [ ] | `.phpunit.result.cache` gitignore kararı (DevOps) | kararı skip | `N-3: .phpunit.result.cache için ignore/tracked kararını ver ve .gitignore'a işle` |
| N-4 | [ ] | Bekleyen değişiklik inceleme: `admin-dashboard.php` + `AdminMarkupContractTest` + `web.config` (git M) → test yeşil → commit paketi | markup sözleşmesini testi bozarak geçirmede | `N-4: admin bekleyen değişikliklerini incele, phpunit yeşil kanıtıyla commit mesajı hazırla (commit MO'da)` |

**Yüzey yasakları:** `shared/config/routes.php` değişikliği · auth formlarını buraya taşıma (ADR-043) · `DEBUG_MODE` prod'da açma · secret/.env yazımı · commit.
## Geçmiş & Mikro Ölçek — Tam Liste (2026-10-25)

**Geçmiş — tamamlanan (kanıtlı):**
- [x] `[MİKRO]` Aile indeksi satırı #29 — kök `PRD.md` §5'e eklendi (v1.2.0) — 2026-10-23 (bu dosya §Kapanan)
- [x] `[ORTA]` `AdminMarkupContractTest` + ADR-058 markup sözleşmesi koruma altında (AGENTS §3.1)
- [x] `[MİKRO]` db-access-guard CI gate (ADR-002) — `2513de8`

**Mikro işler (en küçük → büyük):**
- [ ] `[MİKRO]` N-1 altı: `.env`/config'te `DEBUG_MODE` satırını oku → değer + dosya satırı notu
- [ ] `[MİKRO]` N-3 altı: `.phpunit.result.cache` diskte var mı, tracked mı → `git ls-files` (1 komut)
- [ ] `[MİKRO]` N-2 altı: login rotasına 6 istek at → 429 gelmediyse kanıt log'u
- [ ] `[ORTA]` N-4 diff inceleme → `[EPİK]` N-1 CRITICAL kapısı (prod backdoor kapanışı)## Referanslar

- `[[PRD.md]]` (§6 Risk · §7 Sonraki Adım) · `[[CONTEXT.md]]` · `[[WORKFLOW.md]]` · `[[AGENTS.md]]` · `[[CLAUDE.md]]`
- Aile indeksi: `[[../TODO]]` · `[[../PRD.md]]` · Vault TODO: `[[../.ai/TODO.md]]`

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Last Updated:** 2026-10-23
