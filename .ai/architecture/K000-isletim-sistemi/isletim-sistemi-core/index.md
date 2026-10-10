---
type: architecture
category: layer-subindex
title: "K000 · isletim-sistemi-core — Zemin Çekirdeği"
date: 2026-10-10
status: active
version: 1.0.0
authority: SSOT
---

# isletim-sistemi-core — Zemin Çekirdeği

> Üst indeks: `[[../index]]` · Makro: `[[../os-master]]`

## §1 Kapsam
K000'in "çalışma zemini çekirdeği": subdomain giriş noktaları, composer autoload,
paylaşımlı package, PHP 8.4 runtime — üst katmanların hiçbiri OS'ye doğrudan dokunmaz.

## §2 Disk Kanıtları (2026-10-10 ölçüm)

| Varlık | Yol | Durum |
|---|---|---|
| 5 subdomain kökü | `api.` `auth.` `home.` `media.` `assets.coremusic.net/` | IMPLEMENTED |
| Paylaşımlı autoload | `shared/composer.json` (`coremusic/shared-infrastructure`, php `>=8.4`) | IMPLEMENTED |
| 4 subdomain composer | `api/auth/home/media.coremusic.net/composer.json` (name + `php >=8.4`) | IMPLEMENTED |
| Giriş yapılandırması | 8 dosya: `.{htaccess,web.config}` × 4 subdomain (`assets` dâhil `.htaccess`+`web.config`) | IMPLEMENTED |
| Bootstrap | `shared/src/Bootstrap/RuntimeBootstrap.php` · `shared/src/PageRouter/PageRouterKernel.php` | IMPLEMENTED |
| DB zemini | `shared/src/Database/{DatabaseManager,DatabaseRegistry,Config/DatabaseConfig}.php` | IMPLEMENTED |

## §3 Bağımlılık
- **Alt:** yok (en dip).
- **Üst (çağıran):** `isletim-sistemi-api` · `isletim-sistemi-kernel` · `isletim-sistemi-cross` · tüm K001–K020.

## §4 Durum
**IMPLEMENTED** — yukarıdaki dosya yolları disk kanıtlı.

## §5 Risk / Not
- `PageRouterKernel` bir **PHP uygulama çekirdeğidir**, OS kernel'i değil (ayrıca `isletim-sistemi-kernel/`) — ikisi karıştırılmaz.
- `assets.coremusic.net/` composer.json'sız (yalnız `.htaccess`+`web.config`) — kasıt UNKNOWN (os-master §7-2).
