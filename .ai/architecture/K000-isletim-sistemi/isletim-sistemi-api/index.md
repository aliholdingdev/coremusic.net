---
type: architecture
category: layer-subindex
title: "K000 · isletim-sistemi-api — Zemin Arayüzü"
date: 2026-10-10
status: active
version: 1.0.0
authority: SSOT
---

# isletim-sistemi-api — Zemin Arayüzü

> Üst indeks: `[[../index]]` · Makro: `[[../os-master]]` · Kontratlar: `shared/src/Contracts/`

## §1 Kapsam
K000'in üst katmanlara açtığı **zemin sözleşmeleri**: config/DB erişim kontratları,
gateway/BFF arayüzleri, route/caching portları. (Bu katman DTO/iş API'si değil —
iş API'si K009'dadır; burada yalnız K000'in yayınladığı kontratlar listelenir.)

## §2 Disk Kanıtları — Contracts (PSR-4 kontrat dosyaları)

| Kontrat | Dosya |
|---|---|
| DB | `shared/src/Contracts/Database/IDatabaseManager.php` · `IDatabaseRegistry.php` |
| Config | `shared/src/Contracts/Config/IConfigManager.php` |
| Auth/Sess | `shared/src/Contracts/Auth/{IAuthService,ISessionManager,IUserRepository}.php` |
| Middleware | `shared/src/Contracts/Middleware/IMiddleware.php` (PSR-15 **değil** — AGENTS notu §23-2) |
| Olaylar | `shared/src/Contracts/Events/{DomainEventInterface,IntegrationEventInterface}.php` |
| API/BFF | `shared/src/Contracts/Api/{GatewayInterface,BffInterface,ServiceRegistryInterface}.php` |
| Env (ADR-015) | `shared/src/Config/EnvParser.php` + `*.coremusic.net/config/constants.php` (4 subdomain çağırıyor) |

## §3 Bağımlılık
- **Alt:** `isletim-sistemi-core` (zemin) .
- **Üst (çağıran):** K007 (middleware) · K008 (servisler) · K009 (API).

## §4 Durum
**IMPLEMENTED** — kontrat dosyaları diskte mevcut. Method imzaları bu görevde **okunmadı** → imza detayı `UNKNOWN`.

## §5 Risk / Not
- `IMiddleware` proje-özel; PSR-15 `MiddlewareInterface` grep = 0 (master index §4-2) → `⚠️ VERIFICATION REQUIRED`.
