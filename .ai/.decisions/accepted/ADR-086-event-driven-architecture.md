---
title: "CoreMusic — ADR-086: Event Driven Architecture (PSR-14 — altyapı IMPLEMENTED, wiring PLANNED)"
type: "architecture-decision"
category: "architecture"
date: "2026-10-07"
updated: "2026-10-07"
version: "1.0.0"
status: "accepted"
authority: "Servisler arası gevşek bağlanma PSR-14 event'lerle yapılır (EventDispatcher). Altyapı (dispatcher + 12 event) diskte IMPLEMENTED; production dispatch WIRING PLANNED'tir — 2026-10-07 ölçümünde Events/ dışı 0 dispatch çağrısı vardır ve 'var' denmez."
kaynak: "brain.md §13 ADR-086 + §195 Event Driven metni + disk kanıtı (2026-10-07): shared/src/Events/{EventDispatcher,StoppableEventTrait,Domain/×9,Integration/×3}, shared/composer.json psr/event-dispatcher ^1.0, grep dispatch usage = 0 (Events/ dışı) · şablon adr-template.md"
governance: "Red Team · Human Mode · Truth Mode"
---

# CoreMusic — ADR-086: Event Driven Architecture (PSR-14)

> **Durum:** ✅ **ACCEPTED** (karar) · **Uygulama:** ⚠️ **KISMİ — altyapı tam, wiring PLANNED** · **Tarih:** 2026-10-07 (dosya boşluktan dolduruldu) · **Ağırlık:** 1 · **İlgili ADR:** 084, 085
> **Karar serisi:** `.ai/.decisions/accepted/` · **Slug:** `event-driven-architecture` · **Dosya:** `ADR-086-event-driven-architecture.md`
> **Not:** Numara kayıtlıydı, dosyası yoktu ("brain.md only"); mevcut 086 dolduruldu.

---

## §1 Bağlam (Context)

### §1.1 Disk Kanıtları (2026-10-07 ölçümü)

| Yüzey | Kanıt | Durum |
|-------|-------|-------|
| Dispatcher | `shared/src/Events/EventDispatcher.php` → `implements Psr\EventDispatcher\EventDispatcherInterface` | ✅ IMPLEMENTED |
| Bağımlılık | `shared/composer.json:18` → `"psr/event-dispatcher": "^1.0"` | ✅ |
| Stoppable | `Events/StoppableEventTrait.php` | ✅ |
| Domain event ×9 | GenderSet, MediaAccessed, MusicAdded, MusicPlayed, PasswordResetRequested, PlaylistCreated, UserLoggedIn, UserLoggedOut, UserRegistered (`Events/Domain/`) | ✅ |
| Integration event ×3 | AuthValidated, Notification, SessionCreated (`Events/Integration/`) | ✅ |
| Testler | `shared/tests/Events/` | ✅ |
| **Production dispatch** | `grep new …Event(` + `new EventDispatcher(` → **0** (Events/ dışı, vendor hariç) | ❌ **PLANNED (wiring)** |
| Servis doğrudan çağrı | `home include/Auth/HomeAuthBridge` → auth'a **HTTP curl** (validate-key bridge) — olay yayını DEĞİL | mevcut gerçek (bridge, ADR-043/011) |

### §1.2 Kısıtlamalar

- "Servisler birbirini doğrudan çağırmaz, event yayınlar" (anayasa §6A.4) — bugünün kodunda PHP servisleri zaten ayrı host'lar; aralarında doğrudan tek akış **auth_key bridge**'idir (kasıtlı, ADR-043). Event'ler aynı süreç içi (in-process) kullanılır.
- Event sınıfı taşıyıcıdır: iş mantığı event handler'lara taşınır; dispatch asla kritik yolun yerine geçmez (handler hatası isteği düşürmez — Stoppable desen mevcut).

---

## §2 Karar (Decision)

### §2.1 Karar

1. **PSR-14 tek dispatcher:** tüm event'ler `CoreMusic\Events\EventDispatcher` üzerinden; arayüz `Psr\EventDispatcher\EventDispatcherInterface`.
2. **Altyapı KABUL edilmiş ve implement** (dispatcher + 12 event + test).
3. **Wiring (production dispatch) PLANNED:** ilk adaylar UserLoggedIn/UserLoggedOut (audit), MusicPlayed (istatistik), PasswordResetRequested (bildirim) — handler'lar yazıldıktan ve test edildikten sonra servis akışlarına bağlanır.
4. Bu ADR'den sonra "event driven SİSTEM" iddiası yalnız bu dosyadaki durum notuyla birlikte okunur: **altyapı var, uçtan uca akış PLANNED**.

### §2.2 Gerekçe

Zero-hallucination: 0 dispatch gerçeği gizlenmez; altyapı gerçekten mevcut ve testli olduğu için karar "rejected" değil, "kısmi implement" olarak işaretlenir. Wiring ayrı fazda yapılır (kapsam + test disiplini).

---

## §3 Alternatifler (Alternatives)

| # | Alternatif | Neden reddedildi |
|---|-----------|------------------|
| 1 | ADR'yi "rejected" yapmak | Dispatcher + 12 event + test diskte gerçekten var; reddetmek gerçeği yansıtmaz. |
| 2 | Şimdi tam wiring (tüm servislere dispatch) | Kapsam patlaması; handler'lar henüz yok — anlamsız event üretimi (kayıp/gürültü). |
| 3 | PSR-14 yerine özel arayüz | Ekosistem uyumu + `psr/event-dispatcher` zaten bağımlılık; tekerlek icat edilmez. |

---

## §4 Sonuçlar (Consequences)

**Olumlu:** tek dispatcher SSOT'u · testli altyapı hazır · wiring başladığında kod değişikliği küçük (dispatch satırları + handler'lar).

**Olumsuz / Risk:** anayasa "event driven" gibi mutlak konuşur → bu ADR'nin kısmi-notu P9 vault senkronunda §6A.4'e bağlanmalı · event'ler şu an ölü ağırlık (bkz. P6 dead-code taraması — silinmez, wiring PLANNED).

**Risk:** Düşük (doküman + kısıtlı).

---

## §5 Uygulama (Implementation)

### §5.1 Adımlar (2026-10-07)

Dosya oluşturuldu (brain metni + grep kanıtı); `index.md` satırı gerçek link'e çevrildi; log.md kaydı. **Kod değişikliği yok.**

### §5.2 Wiring Planı (sonraki faz — bu ADR kapsam dışı)

1. Audit handler (`UserLoggedIn/Out → coremusic_logs`) 2. `MusicPlayed → istatistik` 3. `PasswordResetRequested → mail (B-F-08 ile birlikte)` — her biri ayrı PR + test.

### §5.3 Rollback

Dosya + index satırı geri alınır; brain.md metni durur.

---

## §6 İlgili Dokümanlar

`shared/src/Events/**` · `shared/tests/Events/` · `shared/composer.json` · [[ADR-084-api-gateway-architecture]] · [[ADR-085-modular-composer-packages]] · [[../brain.md]] §195/§13 · anayasa §6A.4

---

## §7 Onay

| Tarih | Karar Veren | Durum | Not |
|-------|-------------|-------|-----|
| 2026-10-07 | Bayram Ali (Vault Steward) · Claude Code | ACCEPTED (kısmi implement) | Refactor P2-11 — kullanıcı kararı: "ADR'ları yaz + PLANNED dürüstçe işaretle" |
