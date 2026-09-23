---
title: "CoreMusic — shared/src/Theme Bağlam"
type: context
folder: "shared/src/Theme"
category: layer3-presentation
date: 2026-09-21
updated: 2026-09-21
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
---

# Theme — CLAUDE.md (Detaylı)

**Zorunlu Bağlantılar:** · [[../CLAUDE.md]] · [[../../.ai/architecture/k11-ux]]

---

## 1. Bağlam

Tema motoru (ADR-044). Gender-based dinamik tema: female→pink, male→blue, neutral→default. CSS custom properties ile anında geçiş (sayfa yenileme yok).

---

## 2. Mevcut Durum

| Durum | Değer |
|-------|-------|
| Toplam dosya | 1 PHP dosyası |
| ADR | ADR-044 (Dynamic Theme Engine) |

### 2.1 Dosya Envanteri

| Dosya | Amaç |
|-------|------|
| `ThemeManager.php` | Tema yönetimi, DB + user gender çözümleme |

---

## 3. Tema Yapısı

```
DB: user_preferences → user_id, device_type, theme_gender
PHP: ThemeManager.php → DB + user gender çözümleme
JS: ThemeManager.js → CSS custom properties ile anında geçiş
CSS: a-colors.css → Renk token'ları (female/male/neutral)
```

---

## 4. Komşu İlişkileri

| Yön | Hedef | İlişki |
|-----|-------|--------|
| Parent | [[../CLAUDE.md]] | Shared library üst bağlam |
| Kullanıcı | [[../../home.coremusic.net/CLAUDE.md]] | Home tema kullanımı |
| Kullanıcı | [[../../assets.coremusic.net/CLAUDE.md]] | Tema CSS token'ları |
| Referans | [[../../.ai/architecture/k11-ux]] | UX mimarisi |
| ADR | [[../../.ai/decisions/accepted/ADR-044-dynamic-user-theme-engine]] | Tema motoru |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-21
**Mode:** Red Team · Human Mode · Truth Mode
