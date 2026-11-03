---
title: "shared — TODO Listesi"
type: todo
category: project-tracking
version: 1.3.0
status: active
authority: SSOT
updated: 2026-10-30
---

# shared (Ortak Çekirdek) — TODO Listesi

**Zorunlu Bağlantılar:** [[PRD.md]] · [[CONTEXT.md]] · [[../TODO]] · [[../PRD.md]] · [[../.ai/TODO.md]]

**Kural (Zero-Hallucination):** Her madde PRD §7 / disk kanıtına dayanır; kanıtlanamayan madde yazılmaz. Durum: `[ ]` açık · `[x]` kapalı.

**eli10:** Ortak çekirdeğin kalan işleri: alt PRD bağlantıları, migrations/config sözleşmesi, DECOMMISSIONED envanter notu.
**eli15:** #9–#17 alt PRD'lerinin **9/9'u diskte mevcut** (2026-10-24 disk doğrulaması: `src/{PageRouter,Middleware,Api,Security,Database,OAuth,Events,Cache,Session}/PRD.md`); #23, #24 diskte YOK — bu iki bağlantı maddesi o dosyalar üretilince kapanır (eksik-PRD listesi: `[[../TODO]]` §2).

## Açık Maddeler

- [x] **Alt PRD bağlantıları** — #9–#17 (PageRouter · Middleware · Api · Security · Database · OAuth · Events · Cache · Session) · 2026-10-24 disk doğrulaması: **9/9 mevcut** (`src/{PageRouter,Middleware,Api,Security,Database,OAuth,Events,Cache,Session}/PRD.md`) · Kaynak: `[[PRD.md]]` §7.1
- [ ] **#23 (`shared/database/migrations`) + #24 (`shared/config`) alt PRD'leri** — şema/konfigürasyon sözleşmesini derinleştirir (diskte YOK) · Kaynak: `[[PRD.md]]` §7.2
- [ ] **DECOMMISSIONED envanter notu** — AI · Bff modülleri için not `[[CONTEXT.md]]` §3.5'e taşınır; geri ekleme yalnız ADR ile · Kaynak: `[[PRD.md]]` §7.3
- [ ] **EPIC: Duplikasyon konsolidasyon backlog'u** — kod-tekilleşme (repository tek kanonik tablodan okur) + tablo birleştirme expand-and-contract planı: `api_keys` (auth↔api) · `eq_presets` (system↔neva↔studio) · `genres` (musics↔catalog) · settings ×3 (system) · wifi/BT (system↔wireless) · download history ×2 · audio features ×2 · medya dosya tabloları ×4 (`music_files`, `media_metadata`, `download_cache`, `asset`) → ADR-112 eki, onay sonrası sıraya alınır · Kaynak: şema envanteri 2026-10-30 (`.ai/.sql/mysql/` 21 dump)
- [ ] **ORTA: Coverage sürücüsü (pcov/xdebug) kurulumu** — `php -m`'de sürücü yok → ≥%80 PHPUnit kapısı bu oturumda koşulamadı; sürücü + coverage raporu bağlanır · Kaynak: `[[PRD.md]]` PRD-SHR-006 Coverage Doğrulama Notu (2026-10-24)
- [ ] **MICRO: ViewModeManager birim testi** — `shared/CLAUDE.md` §2 bilinen riski: `ThemeManagerModeTest` var, `ViewModeManager` testi yok → test eklenir · Kaynak: `[[CLAUDE.md]]` §2 (shared)

## TAM Madde Listesi — Küçükten Büyüğe (Geçmiş · Açık · Gelecek) — 2026-10-25

> Tek doğruluk listesi; `## Geçmiş & Mikro Ölçek` kanıt detayıdır. Sıra: MICRO → ORTA → EPIC.

### A. GEÇMİŞ — TAMAMLANAN
- [x] MICRO: `.gitleaks.toml` tracked — `43e6763` · phpstan 0 + `--/` 343 dosya temizliği — `3e3ed57`
- [x] MICRO: `RateLimiterUnavailableException` + yeni test dosyaları (fail-closed) **yazıldı** (commit yok)
- [x] ORTA: #9-#17 alt PRD **9/9 diskte** doğrulandı — 2026-10-24
- [x] ORTA: Interface→Contract (6 dizin, 20 tracked) — 2026-10-23
- [x] ORTA: fail-closed test paketi: `CacheRateLimiterFailClosed` · `PageRouterChainOrder` · `ServiceRegistryHealth` · `UuidV7Test`
- [x] EPIC: SEC-01..05 + RBAC claims + session save-path paritesi — `d3d5c4e`
- [x] EPIC: DB erişimi sertleştirme + db-access-guard CI gate — `2513de8`
- [x] EPIC: ADR-085 tek `shared/` + PSR-4 · ADR-086 PSR-14 events (9 domain + 3 integration) · Contracts tek kok

### B. AÇIK — EKSİK / YARIM (küçükten büyüğe)
- [ ] MICRO: S-5a — shared `composer test` çalıştır → yeşil kanıtını sakla
- [ ] MICRO: S-3a — migration dosya adı iskeleti (`20261025_*.sql` + yorumlu DOWN)
- [ ] MICRO: S-2a — `CONTEXT.md` §3.5 başlığını oku → ekleme noktasını bul
- [ ] MICRO: M diff inceleme: `RateLimitMiddleware` · `ApcuAdapter` · `ConnectionFactory` · `DatabaseManager` · `SessionInitializer` · `UuidV7` (14 M + 6 yeni)
- [ ] MICRO: 14 case-variant duplicate başlık + ffprobe süre NULL (`.ai/TODO` §4 — MVP'de kabul, import pass bekliyor)
- [ ] ORTA: S-1 **#23 `migrations` + #24 `config` alt PRD'leri ÜRET** (diskte YOK — 7 eksikten 2'si)
- [ ] ORTA: S-2 DECOMMISSIONED envanter notu (AI · Bff) → `CONTEXT.md` §3.5
- [ ] ORTA: S-3 **Power Governor migration** (speaker_profiles + channel_power_caps + power_governor_audit — BCNF taslağı hazır) · **çalıştırma yok**
- [ ] ORTA: S-4 **Power Governor API** (T-04: 7 uç + audit olayları + RouteConfigTest)
- [ ] ORTA: S-5 28 M + 6 yeni dosyanın test yeşilliği + commit pakesi (onay → MO)
- [ ] EPIC: Power Governor ürünleşmesi (şema + API + panel + 429/audit kapıları)
- [ ] EPIC: Backend 09-11 canlı curl + KAPI-E (server PC) · Coverage ≥%80 (PLT-005/011)

### C. GELECEK — SIRADAKİ BÜYÜK İŞLER (öncelik sırası)
| Sıra | EPIC iş | İlk MICRO adım |
|---|---|---|
| 1 | S-3 migration yaz (çalıştırma yok) | dosya adı iskeleti |
| 2 | S-4 API 7 uç + audit | T-04 sözleşmesi okuması |
| 3 | S-5 commit paketi | `composer test` yeşil |
| 4 | S-1 PRD ×2 (7 eksikten) + coverage ≥%80 | `composer test --coverage` |
| 5 | Power Governor canlı + PLT-005 doğrulama | curl ×3 (server PC) |## Adım Adım Yürütme (2026-10-25 güçlendirme)

**Kural:** Her adım tek oturum · `[ ]` → çalış · `[x]` + kanıt → kapanır · **YAPMA** bağlayıcı · komutu gönder → agent yapar.

| # | Adım | Ne yapılacak (kaynak) | YAPMA | Hazır komut |
|---|---|---|---|---|
| S-1 | [ ] | #23 `shared/database/migrations` + #24 `shared/config` alt PRD'leri (**diskte YOK**) | şablonsuz PRD | `S-1: migrations + config için iki alt PRD üret (7 alanlı FM, §6 kapıları)` |
| S-2 | [ ] | DECOMMISSIONED envanter notu (AI · Bff) → `CONTEXT.md` §3.5; geri ekleme yalnız ADR | decommissioned modülü geri ekleme (ADR-030/084) | `S-2: AI+Bff decommissioned notunu CONTEXT §3.5'e taşı, envanter tablosunu güncelle` |
| S-3 | [ ] | **Power Governor migration** — `speaker_profiles + channel_power_caps + power_governor_audit` (BCNF taslağı mevcut: data-engineer çıktısı 2026-10-25) → `20261025_create_power_governor_tables.sql` | **migration ÇALISTIRMA** (production onay ister) · `SELECT *` · ORM | `S-3: Power Governor migration'ını yaz — BCNF taslağını kullan, ADR-014 forward-only, DOWN yorumlu; taslağı .ai'ye kaydet, migration ÇALISTIRMA` |
| S-4 | [ ] | **Power Governor API** — T-04 sözleşmesi (7 uç, audit olayları) → controller + `RouteConfigTest` | middleware sırası · public uç · sahte endpoint | `S-4: T-04 API sözleşmesini uygula — 7 route + DTO + RouteConfigTest; audit log'u PDO prepared ile yaz` |
| S-5 | [ ] | Bekleyen 14 M + 6 yeni dosya (rate-limit fail-closed · UuidV7 · PageRouterChainOrder · ServiceRegistryHealth · CacheRateLimiterFailClosed) → test yeşil → commit pakesi | fail-closed mantığını "basitlik" uğruna açma (fail-open yasak) | `S-5: shared'taki bekleyen değişiklikleri gözden geçir, tüm suite'i çalıştır, commit mesajını hazırla (commit MO'da)` |

**Yüzey yasakları:** ORM · `SELECT *` · middleware sırası · BypassAuth kapsam genişletme (ADR-008) · `DatabaseManager` dışında bağlantı · subdomain-özel iş kuralı gömme · commit.
## Geçmiş & Mikro Ölçek — Tam Liste (2026-10-25)

**Geçmiş — tamamlanan (kanıtlı):**
- [x] `[EPİK]` Interface→Contract taşıması (6 dizin, 20 tracked) — 2026-10-23
- [x] `[EPİK]` SEC-01..05 + RBAC claims + session save-path paritesi — `d3d5c4e`
- [x] `[ORTA]` #9-#17 alt PRD 9/9 diskte mevcut doğrulandı — 2026-10-24 (bu dosya §Açık)
- [x] `[ORTA]` Rate-limit fail-closed + UuidV7 + PageRouterChainOrder + ServiceRegistryHealth testleri **yazıldı** (dosyalar var, commit yok) — `git status` bugün
- [x] `[MİKRO]` phpstan level 5 → 0 · `--/` stray 343 dosya temizliği — `3e3ed57`
- [x] `[MİKRO]` `.gitleaks.toml` tracked — `43e6763` · `RateLimiterUnavailableException` yeni sınıf

**Mikro işler (en küçük → büyük):**
- [ ] `[MİKRO]` S-5 altı: `composer test` (shared) çalıştır → yeşil kanıtı sakla
- [ ] `[MİKRO]` S-3 altı: migration dosya adını oluştur (`20261025_*.sql`) — boş iskelet + DOWN yorumu
- [ ] `[MİKRO]` S-2 altı: `CONTEXT.md` §3.5'in başlığını oku → ekleme noktası
- [ ] `[ORTA]` S-3 şema + S-4 API → `[EPİK]` Power Governor canlı (panel + 429/audit kapılarıyla)## Referanslar

- `[[PRD.md]]` (§7 Sonraki Adım) · `[[CONTEXT.md]]` · `[[WORKFLOW.md]]` · `[[AGENTS.md]]` · `[[CLAUDE.md]]` · `config/CLAUDE.md`
- Aile indeksi: `[[../TODO]]` · `[[../PRD.md]]` · Vault TODO: `[[../.ai/TODO.md]]`

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Last Updated:** 2026-10-23
