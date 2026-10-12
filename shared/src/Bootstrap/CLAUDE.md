---
title: "CoreMusic — shared/src/Bootstrap Bağlam"
type: context
folder: "shared/src/Bootstrap"
category: layer0-infrastructure
date: 2026-09-21
updated: 2026-09-21
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
---

# Bootstrap — CLAUDE.md (Detaylı)

**Zorunlu Bağlantılar:** · [[../CLAUDE.md]]

---

## 1. Bağlam

Ortak çalışma zamanı kurulumu. RuntimeBootstrap tüm subdomainler tarafından kullanılır.

---

## 2. Mevcut Durum

| Durum | Değer |
|-------|-------|
| Dosya | 1 PHP dosyası |

| Dosya | Amaç |
|-------|------|
| `RuntimeBootstrap.php` | Uygulama başlatma, DI container, middleware kaydı |

---

## 3. Bootstrap Akışı

```
1. index.php → RuntimeBootstrap::init()
2. .env yükle (EnvParser)
3. Config yükle (ConfigManager)
4. DB bağlantısı kur (DatabaseManager)
5. Session başlat (SessionBootstrapper)
6. Middleware kaydet
7. Route'ları yükle
8. Request'i handle et
```

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-21
**Mode:** Red Team · Human Mode · Truth Mode
