---
title: "CoreMusic — shared/src/ViewMode Bağlam"
type: context
folder: "shared/src/ViewMode"
category: layer3-presentation
date: 2026-09-21
updated: 2026-09-21
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
---

# ViewMode — CLAUDE.md (Detaylı)

**Zorunlu Bağlantılar:** · [[../CLAUDE.md]] · [[../../.ai/architecture/k11-ux]]

---

## 1. Bağlam

Görünüm modu yönetimi (ADR-045). Home, Pro, Studio, Car olmak üzere 4 görünüm modu. Her panel için geçerli.

---

## 2. Mevcut Durum

| Durum | Değer |
|-------|-------|
| Toplam dosya | 1 PHP dosyası |
| ViewMode | 4 (home, pro, studio, car) |
| ADR | ADR-045 (Multi-Domain View Mode) |

---

## 3. ViewMode Yapısı

```
DB: user_preferences → view_mode
PHP: ViewModeManager.php → View mode yönetimi
JS: ViewModeManager.js → View mode geçişi
CSS: 09_ViewModes/ → v-home.css, v-pro.css, v-studio.css, v-car.css
```

---

## 4. Komşu İlişkileri

| Yön | Hedef | İlişki |
|-----|-------|--------|
| Parent | [[../CLAUDE.md]] | Shared library üst bağlam |
| Kullanıcı | [[../../home.coremusic.net/CLAUDE.md]] | Home view mode |
| Kullanıcı | [[../../assets.coremusic.net/CLAUDE.md]] | ViewMode CSS |
| Referans | [[../../.ai/architecture/k11-ux]] | UX mimarisi |
| ADR | [[../../.ai/decisions/accepted/ADR-045-multi-domain-view-mode-architecture]] | View mode |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-21
**Mode:** Red Team · Human Mode · Truth Mode
