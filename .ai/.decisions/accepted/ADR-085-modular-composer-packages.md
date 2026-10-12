---
title: "CoreMusic — ADR-085: Shared Library Hybrid (tek shared/ + PSR-4 namespace modularitesi)"
type: "architecture-decision"
category: "architecture"
date: "2026-10-07"
updated: "2026-10-07"
version: "1.0.0"
status: "accepted"
authority: "Tek shared/ dizini + tek composer paketi (coremusic/shared-infrastructure) + PSR-4 namespace ile modüler ayrım; subdomain'ler path repository ile bağlıdır. Karar DISKTE IMPLEMENTED'TIR."
kaynak: "brain.md §13 ADR-085 metni + disk kanıtı (2026-10-07): shared/composer.json (v2.0.0, PSR-4 CoreMusic\\→src), consumer composer.json path repo ../shared (auth/home/api) · şablon adr-template.md"
governance: "Red Team · Human Mode · Truth Mode"
---

# CoreMusic — ADR-085: Shared Library Hybrid

> **Durum:** ✅ **ACCEPTED** (uygulanmış) · **Tarih:** 2026-10-07 (dosya boşluktan dolduruldu) · **Ağırlık:** 1 · **İlgili ADR:** 084, 002
> **Karar serisi:** `.ai/.decisions/accepted/` · **Slug:** `modular-composer-packages` · **Dosya:** `ADR-085-modular-composer-packages.md`
> **Not:** Numara kayıtlıydı, dosyası yoktu ("brain.md only"); mevcut 085 dolduruldu.

---

## §1 Bağlam (Context)

### §1.1 Disk Kanıtları (2026-10-07)

| Yüzey | Kanıt | Durum |
|-------|-------|-------|
| Tek paket | `shared/composer.json` → `coremusic/shared-infrastructure` v2.0.0 (`composer update` çıktısı) | ✅ |
| PSR-4 | `"CoreMusic\\": "src/"` (`shared/composer.json:27-29`) | ✅ |
| Path bağlantı | auth/home/api `composer.json` → `repositories: path ../shared` | ✅ (3 consumer) |
| Modüler ayrım | `shared/src/` 24+ namespace (Api, Cache, Config, Contracts, Database, Device, Events, Exception, Log, Middleware, OAuth, PageRouter, Security, Session, Theme, ViewMode, Bootstrap…) | ✅ |
| Test paketi | `shared/tests/` (325 test — 2026-10-07 ölçümü) | ✅ |
| Consumer key'leri | auth `CoreMusic\Auth\→include/`, api `CoreMusic\Auth\→../auth.coremusic.net/include/` (SSOT paylaşımı), home `CoreMusic\Home\→include/` | ✅ |

### §1.2 Kısıtlamalar

- **Yasak:** `src/` içine subdomain'e özel iş kuralı; consumer'ların shared içine girmesi (shared CLAUDE §7).
- ORM yok / PDO yalnız shared üzerinden (ADR-002) — DatabaseManager tek bağlantı kapısıdır (bilinen sapma: media CLI + `--/` stray, P4 kapsamı).

---

## §2 Karar (Decision)

### §2.1 Karar

1. **Tek shared/ + tek composer paketi** (hybrid): fiziksel tek dizin, PSR-4 namespace ile mantıksal modülerlik.
2. Consumer'lar **path repository** ile bağlanır; shared sürümü consumer lock'larında referanslanır (`composer update coremusic/shared-infrastructure`).
3. **auth kodu shared'e kopyalanmaz** — api, `CoreMusic\Auth\` autoload'uyla auth'un include/ dizinini doğrudan kullanır (tek gerçek kaynağı).

### §2.2 Gerekçe

Tek paket = tek sürüm/neşter noktası (güvenlik yaması tek yerde); namespace modülerliği dosya düzenini korur; path repo ile monorepo içi dağıtım kilitlenmeden yapılır. Disk zaten bu yapıda çalışmaktadır.

---

## §3 Alternatifler (Alternatives)

| # | Alternatif | Neden reddedildi |
|---|-----------|------------------|
| 1 | Paket başına ayrı composer package (multi-package) | Sürüm senkron kâbusu; ADR-085 v3.0 "tek paket" kararını bozar. |
| 2 | Path repo yerine git subtree/submodule | Monorepo'da gereksiz karmaşa; lock entegrasyonu zayıflar. |
| 3 | Kodu consumer'lara kopyalamak | SSOT ihlali (auth paylaşımı zaten kopya yasağını kanıtlıyor). |

---

## §4 Sonuçlar (Consequences)

**Olumlu:** tek security patch yüzeyi · paylaşılan test altyapısı (325 test) · consumer kurulumu tek `composer install`.

**Olumsuz / Risk:** consumer lock'ları shared composer.json değişince **güncellenmeli** (`composer update coremusic/shared-infrastructure` — 2026-10-07 JWT örneğinde doğrulandı); aksi halde yeni bağımlılıklar consumer'a sızmaz (runtime class-not-found riski).

**Risk:** Düşük (mevcut yapı + bilinen lock bakımı).

---

## §5 Uygulama (Implementation)

### §5.1 Adımlar (2026-10-07)

Dosya oluşturuldu (brain metni + disk ölçümü); `index.md` satırı gerçek link'e çevrildi; log.md kaydı. Kod değişikliği yok — yapı zaten uygulanmış.

### §5.2 Rollback

Dosya + index satırı geri alınır; brain.md §13 metni durur.

---

## §6 İlgili Dokümanlar

`shared/composer.json` · `shared/src/**` · consumer `composer.json` ×3 · `shared/CLAUDE.md` · [[ADR-084-api-gateway-architecture]] · [[ADR-002-pdo-mandatory-no-orm]] · [[../brain.md]] §13

---

## §7 Onay

| Tarih | Karar Veren | Durum | Not |
|-------|-------------|-------|-----|
| 2026-10-07 | Bayram Ali (Vault Steward) · Claude Code | ACCEPTED | Refactor P2-11 — kullanıcı kararı: "ADR'ları yaz" |
