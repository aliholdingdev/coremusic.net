---
title: "CoreMusic — shared/src/Device Bağlam"
type: context
folder: "shared/src/Device"
category: layer3-presentation
date: 2026-09-21
updated: 2026-09-21
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
---

# Device — CLAUDE.md (Detaylı)

**Zorunlu Bağlantılar:** · [[../CLAUDE.md]] · [[../../.ai/architecture/k11-ux]]

---

## 1. Bağlam

Cihaz tespiti ve hybrid rendering server tarafı. DeviceDetector 11 tespit kuralı ile cihaz tipini belirler, DeviceManager 4-tier layout kararını verir, DeviceCssMap CSS haritasını sağlar.

---

## 2. Mevcut Durum

| Durum | Değer |
|-------|-------|
| Toplam dosya | 3 PHP dosyası |
| Cihaz türü | 7 (phone, tablet, embedded, laptop, desktop, 4k-tv, 4k-monitor) |
| Tier | 4 (Phone, Embedded, Wide, 4K) |
| Feature toggle | 9 |

### 2.1 Dosya Envanteri

| Dosya | Amaç |
|-------|------|
| `DeviceDetector.php` | 11 kural ile cihaz tespiti |
| `DeviceManager.php` | 4-tier layout kararları, feature toggles |
| `DeviceCssMap.php` | Cihaz → CSS dosyası eşlemesi |

---

## 3. DeviceDetector Tespit Önceliği

```
1. HTTP Header: X-Device-Type: embedded → 'embedded'
2. User-Agent: "Raspberry Pi" içeriği → 'embedded'
3. User-Agent: "Tizen/webOS/SmartTV" → '4k-tv'
4. Viewport: ≤767px → 'phone'
5. Viewport: 768-1024px + h≤600 → 'embedded'
6. Viewport: 768-1024px + h≥768 → 'laptop'
7. Viewport: ≤1440px → 'laptop'
8. Viewport: ≤2560px → 'desktop'
9. Viewport: ≤3840px + TV UA → '4k-tv'
10. Viewport: ≤3840px + Desktop OS → '4k-monitor'
11. Hiçbiri eşleşmezse → 'desktop' (varsayılan)
```

---

## 4. DeviceManager 4-Tier

| Tier | Cihazlar | Viewport | Layout |
|------|----------|----------|--------|
| Phone | PHONE | ≤767px | Tek sütun, dikey scroll |
| Embedded | EMBEDDED, TABLET | ≤1024px | 42/58 split, 2×2 widget |
| Wide | LAPTOP, DESKTOP | 1025-2560px | 3-sütun, tam widget |
| 4K | FOUR_K_TV, FOUR_K_MON | ≥2561px | 4K ölçeklendirilmiş |

---

## 5. Komşu İlişkileri

| Yön | Hedef | İlişki |
|-----|-------|--------|
| Parent | [[../CLAUDE.md]] | Shared library üst bağlam |
| Kullanıcı | [[../../home.coremusic.net/CLAUDE.md]] | Home device rendering |
| Kullanıcı | [[../../assets.coremusic.net/CLAUDE.md]] | Device CSS haritası |
| Referans | [[../../.ai/architecture/k11-ux]] | UX mimarisi |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-21
**Mode:** Red Team · Human Mode · Truth Mode
