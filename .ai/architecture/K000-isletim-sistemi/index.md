---
type: architecture
category: layer
title: "K000 — İşletim Sistemi"
date: 2026-10-09
status: active
version: 2.0.0
authority: SSOT
---

# K000 — İşletim Sistemi

## §1 Kimlik
- Katman: K000 · Alan: **A0** (K0-K5 — Altyapı/Donanım, `.ai/AGENTS.md` §5).
- Kapsam: web platformunun çalışma-zemini: subdomain dizin yerleşimi, PHP 8.4 runtime,
  composer autoload, `shared/` tek PSR-4 package, PDO bağlantı zemini (plan §1, §3).

## §2 Sorumluluk
1. Subdomain barındırma ve giriş noktaları (`index.php`, `autoload.php`, `.htaccess`/`web.config`).
2. `shared/` package'ının sağlanması: `Middleware`, `PageRouter`, `Database`, `Security`, `Session`.
3. Ortam/yapılandırma okuma stratejisi (ADR-015 env parser).
4. Veritabanı zemini: `shared/src/Database/` (DatabaseManager · DatabaseRegistry).

## §3 Bağlantılar (Dependency Rule)
- **Alt katman (bağımlı):** yok — K000 en alt zemin katmanıdır (plan §2-1).
- **Üst katman (çağıran):** K001–K020 tamamı bu zemine oturur.
- Kural: her katman yalnız alt katmana bağımlı; ihlal → revert + log ERROR (plan §4).

## §4 ADR Bağlantıları
| ADR | Başlık |
|---|---|
| ADR-015 | Env Parser Strategy (Infrastructure) |
| ADR-082 | Dev/Staging Environment Architecture |
| ADR-085 | Shared Library Hybrid (tek shared/ + PSR-4 namespace) |

## §5 Durum
**IMPLEMENTED** — kanıt (2026-10-09): kök dizin 5 subdomain (`api.` `auth.` `home.` `media.` `assets.coremusic.net`) · `shared/composer.json` · `shared/src/Database/{DatabaseManager,DatabaseRegistry}.php`.

## §6 Risk / Not
- ADR-015'in uygulama dosyası bu görevde okunmadı → implementasyon kanıtı **UNKNOWN**.
- `assets.coremusic.net/` altında `composer.json` yok (yalnız statik varlık) — kasıt **UNKNOWN**.
