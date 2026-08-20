---
title: CoreMusic — Bluetooth Quick Page Base (1024×600)
date: 2026-08-19
updated: 2026-08-19
type: png-analysis
status: active
version: 1.0.0
source: Linux 1024 - Bluethoot Qucik Page Base.png
dimensions: 1024x600
platform: Linux Embedded / Raspberry Pi 5
---

# CoreMusic — Bluetooth Quick Page Base PNG Analysis

**Source:** Linux 1024 - Bluethoot Qucik Page Base.png
**Dimensions:** 1024×600px (RPi5 embedded)
**Status:** Verified against `screens/F-quickpanel/bluetooth.md`

## Layout Analysis

### Modal Structure
- Center-positioned modal (~380px wide)
- Semi-transparent dark overlay background
- Glass/frosted effect on modal

### Header
- Blue ✳ (asterisk) icon
- "Bluetooth" title (large, white)
- "Cihaz Bağlantıları" subtitle (smaller, gray)

### Bluetooth Toggle
- "Bluetooth" label on left
- Toggle switch on right — ON state (pink/rose color, circular thumb on right)
- Toggle size: ~50×28px

### Connected Devices Section
- "Bağlı Olan Cihaz" section header
- Single row:
  - Device icon: 🎧 (headphone emoji, blue circle background)
  - Name: "Kim - 50"
  - Badges: Güçlü (green), A2DP (pink), HFP (purple), Müzik (green)
  - Details: "Tarayıcı · Mükemmel · 100%"
  - Action: "Bağlantıyı Kes" button (pink/rose text)

### Available Devices Section
- "Kullanılabilir Cihazlar" section header
- Three device rows:
  1. 🎧 Kim - 50 — Güçlü, A2DP, HFP — "Tarayıcı · Mükemmel · 100%" — [Eşle] button
  2. 🚗 Car BT — Orta (yellow), A2DP, HFP — "Tarayıcı · İyi · -70BS" — [Eşle] button
  3. 📺 Samsung TV — Zayıf (red), A2DP — "Televizyon · Mükemmel · 100%" — [Eşle] button

### Bottom Action Buttons
- "Tümünü Eşleştir" (Pair All) — pink/rose background
- "Eşleşmeleri Sil" (Clear Pairings) — pink/rose background
- "Yenile" (Refresh) — pink/rose background

## Verified Against Spec
- ✅ Modal layout matches
- ✅ Header structure matches
- ✅ Toggle position/state matches
- ✅ Connected device section matches
- ✅ Available devices list matches
- ✅ Badge colors match (A2DP=pink, HFP=purple, Müzik=green, Güçlü=green, Orta=yellow, Zayıf=red)
- ✅ Bottom action buttons present
- ✅ Timestamp updated

## Edits Made
- Timestamp updated to 2026-08-19
