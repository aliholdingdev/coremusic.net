---
title: CoreMusic — Select Gender Female Selected PNG Analysis (1024×600)
date: 2026-08-19
updated: 2026-08-19
type: png-analysis
status: active
version: 1.0.0
source: Linux 1024 - Select Gender - selected.png
dimensions: 1024x600
platform: Linux Embedded / Raspberry Pi 5
---

# CoreMusic — Select Gender Female Selected PNG Analysis

**Source:** Linux 1024 - Select Gender - selected.png
**Dimensions:** 1024×600px (RPi5 embedded)
**Status:** Verified against `screens/A-auth/gender-select.md`

## Layout Analysis

### Same as Unselected State
- Full-screen layout, 72/28 split
- Same background, same left side content
- Same right panel structure

### Selected State Differences
- **"Kız" button:** Pink/rose border (2px), pink background (rgba(255,79,216,0.2)), glow effect
- **"Devam Et" button:** NOW ACTIVE — pink/rose background, white text (was transparent/pasif before)
- Other gender buttons remain unselected (default state)

## Verified Against Spec
- ✅ Selected state matches §5.2 (pink border, pink background, glow)
- ✅ "Devam Et" active state matches §6.2 (pink background, white text)
- ✅ Timestamp already updated from PNG #13 analysis

## Edits Made
- Source image filename corrected (extra spaces removed)
- No other edits needed — spec accurately describes both states
