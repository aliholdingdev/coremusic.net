---
title: CoreMusic — Register Step 3 PNG Analysis (1024×600)
date: 2026-08-19
updated: 2026-08-19
type: png-analysis
status: active
version: 1.0.0
source: Linux 1024 - Register Girl step 3.png
dimensions: 1024x600
platform: Linux Embedded / Raspberry Pi 5
---

# CoreMusic — Register Step 3 PNG Analysis

**Source:** Linux 1024 - Register Girl step 3.png
**Dimensions:** 1024×600px (RPi5 embedded)
**Status:** Verified against `screens/A-auth/register-step2-3.md`

## Layout Analysis

### Full-Screen Layout
- 78/22 split (left hero / right glass panel)
- Same background as other auth screens

### Right Side — Register Panel (Step 3)
- "Hesap Oluştur" heading
- "CoreMusic ailesine katıl, müziğin keyfini çıkar" subtitle

### Form (Step 3)
- **Telefon** label (pink/rose text)
- Input field (pink/rose border, pink background)
- **KVKK Checkbox:** "Gizlilik Politikası ve Kullanım Şartlarını kabul ediyorum" (small text)
- **Kayıt Ol** button (pink/rose background, white text) — final submit button

### Divider + Social buttons
- "────────── veya ──────────" divider
- 3 rows: Apple/Google/Facebook, WhatsApp/Instagram/TikTok, Spotify

### Bottom
- "Hesabın var mı? **Giriş Yap**"

## Verified Against Spec
- ✅ Form fields match (Telefon + KVKK checkbox)
- ✅ Button text matches ("Kayıt Ol" not "Devam Et")
- ✅ Social buttons layout matches (3 rows)
- ✅ Bottom text matches ("Hesabın var mı? Giriş Yap")
- ✅ Timestamp already updated from Step 2 analysis

## Edits Made
- No edits needed — spec was already corrected in Step 2 analysis
- Timestamp already current (2026-08-19)
