---
title: "auth.coremusic.net — TODO Listesi"
type: todo
category: project-tracking
version: 1.2.0
status: active
authority: SSOT
updated: 2026-10-25
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

## TAM Madde Listesi — Küçükten Büyüğe (Geçmiş · Açık · Gelecek) — 2026-10-25

> Tek doğruluk listesi; `## Geçmiş & Mikro Ölçek` bu listenin kanıt detayıdır. Sıra: MICRO → ORTA → EPIC.

### A. GEÇMİŞ — TAMAMLANAN
- [x] MICRO: `AutoRedirectHandlerTest` yazıldı (yeni dosya, commit bekliyor) — `git status`
- [x] ORTA: auth testlerinde çift ağaç → tek ağaç (227 tracked `tests/Unit/Domain/**`) — 2026-10-23
- [x] ORTA: Interface→Contract (Controllers/Services/Repositories) — 2026-10-23
- [x] ORTA: rate-limit bypass/pepper paritesi + session save-path paritesi — `d3d5c4e`
- [x] ORTA: 7 sayfa akışı (login/register/forgot/reset/gender/logout) hexagonal katmanlarla — ADR-043
- [x] EPIC: Backend Adım 08-11 auth yüzeyi (CSRF token + dropdown rol) — `1e806d1` `f3a6cc9`

### B. AÇIK — EKSİK / YARIM (küçükten büyüğe)
- [ ] MICRO: A-1a — `auth/include` klasör envanterini listele (PRD girdisi)
- [ ] MICRO: A-3a — `composer test` çalıştır → yeşil/kırmızı çıktıyı sakla
- [ ] MICRO: A-4a — `AutoRedirectHandler` + `SessionManager` diff'ini oku → commit taslağı
- [ ] MICRO: `.phpunit.result.cache` bu klasörde tracked mı → `git ls-files` (1 komut)
- [ ] MICRO: Argon2id dışında hash üretimi 0 kontrolü (`Password` VO dışı)
- [ ] ORTA: A-1 **#22 `auth/include` alt PRD'si ÜRET** (diskte YOK — kök TODO 7 eksikten)
- [ ] ORTA: A-2 SSO/RBAC çapraz kontrol (#1 kök + #5 home · PRD-PLT-004)
- [ ] ORTA: A-3 Security denetimi (ADR-010/011/013) → PASS/FAIL PRD §4 kapısı
- [ ] EPIC: Auth security kapı kapanışı + canlı login/reset/sso akışı (server PC)

### C. GELECEK — SIRADAKİ BÜYÜK İŞLER (öncelik sırası)
| Sıra | EPIC iş | İlk MICRO adım |
|---|---|---|
| 1 | A-1 PRD üret (7 eksikten biri) | envanter listesi |
| 2 | A-2 SSO çapraz doğrulama | 7 sayfa akış okuması |
| 3 | A-3 audit PASS → PRD §4 | `composer test` |
| 4 | Commit paketi + canlı akış (server PC) | diff taslağı |## Adım Adım Yürütme (2026-10-25 güçlendirme)

**Kural:** Her adım tek oturum · `[ ]` → çalış · `[x]` + kanıt → kapanır · **YAPMA** bağlayıcı · komutu gönder → agent yapar.

| # | Adım | Ne yapılacak (kaynak) | YAPMA | Hazır komut |
|---|---|---|---|---|
| A-1 | [ ] | #22 `auth/include` alt PRD'si **üretilir** (7 alanlı FM + §6 kapıları; diskte YOK) | şablonsuz/kapısız PRD | `A-1: auth/include için alt PRD üret — 7 alanlı frontmatter, Amaç/Kapsam/Gereksinim/Kapı/ADR/Risk/SonrakiAdım, şablon: .ai/.templates` |
| A-2 | [ ] | SSO/RBAC çapraz kontrol (#1 kök + #5 home PRD'leri, PRD-PLT-004) | yeni auth akışı/icat | `A-2: SSO/RBAC akışını PRD-PLT-004 ile çapraz doğrula, sonucu auth/PRD §7.2'ye yaz` |
| A-3 | [ ] | Security denetimi (ADR-010/011/013) → sonucu PRD §4 kapısına ekle | bulguyu gizleme/kod içinde düzeltme denemesi | `A-3: auth security-audit çalıştır — CSRF/session/rate-limit, PASS/FAIL kanıtıyla PRD §4'e yaz` |
| A-4 | [ ] | Bekleyen değişiklikler inceleme + commit paketi (`AutoRedirectHandler`+yeni test · `SessionManager` · `index.php`) | **subagent/agent commit atmaz** | `A-4: auth'taki bekleyen değişiklikleri incele ve commit mesajı hazırla — commit MO'da` |

**Yüzey yasakları:** `.env` içerik yazımı · bypass/AutoRedirect kapsam genişletme (ADR-008) · Argon2id dışında hash · domain katmanında superglobal · ORM/`SELECT *` · commit.
## Geçmiş & Mikro Ölçek — Tam Liste (2026-10-25)

**Geçmiş — tamamlanan (kanıtlı):**
- [x] `[ORTA]` auth testlerinde çift ağaç → tek ağaç (`tests/Unit/Domain/**` 227 tracked) — `.ai/TODO` §2, 2026-10-23
- [x] `[ORTA]` rate-limit bypass/pepper paritesi + session save-path paritesi — `d3d5c4e`
- [x] `[MİKRO]` `AutoRedirectHandlerTest` yazıldı (yeni dosya, commit bekliyor) — `git status` bugün
- [x] `[MİKRO]` Interface→Contract taşıması (Controllers/Services/Repositories) — 2026-10-23

**Mikro işler (en küçük → büyük):**
- [ ] `[MİKRO]` A-4 altı: `AutoRedirectHandler` + `SessionManager` diff'ini oku → commit mesajı taslağı
- [ ] `[MİKRO]` A-3 altı: `composer test` çalıştır → yeşil/kırmızı çıktıyı sakla (audit girdisi)
- [ ] `[MİKRO]` A-1 altı: `auth/include` klasör envanterini listele (PRD girdisi)
- [ ] `[ORTA]` A-1 PRD üret → A-2 SSO çapraz kontrol → `[EPİK]` A-3 security kapı kapanışı## Referanslar

- `[[PRD.md]]` (§7 Sonraki Adım) · `[[CONTEXT.md]]` · `[[WORKFLOW.md]]` · `[[AGENTS.md]]` · `[[CLAUDE.md]]`
- Aile indeksi: `[[../TODO]]` · `[[../PRD.md]]` · Vault TODO: `[[../.ai/TODO.md]]`

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Last Updated:** 2026-10-23
