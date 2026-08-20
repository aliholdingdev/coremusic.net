---
title: CoreMusic — Register Step 1 PNG Analysis (1024×600)
date: 2026-08-19
updated: 2026-08-19
type: png-analysis
status: active
version: 1.0.0
source: Linux 1024 - Register Girl.png
dimensions: 1024x600
platform: Linux Embedded / Raspberry Pi 5
---

# CoreMusic — Register Step 1 PNG Analysis

**Source:** Linux 1024 - Register Girl.png
**Dimensions:** 1024×600px (RPi5 embedded)
**Status:** Verified against `screens/A-auth/register-step1.md`

## Layout Analysis

### Full-Screen Layout
- 78/22 split (left hero / right glass panel)
- Full-bleed background image: Same as Login/Select Gender (woman in pink floral landscape)

### Left Side (Hero)
- Same as Login: CoreMusic logo, "Aşkınla milkyenine!" decorative text, description text

### Right Side (Glass Panel)
- Semi-transparent glass panel (~224px wide)
- Top: Decorative line art illustration (woman's face with stars, white)
- "Hesap Oluştur" heading (large, white)
- "CoreMusic ailesine katıl, müziğin keyfini çıkar" subtitle (smaller, gray)

### Register Form (Step 1)
- **Kullanıcı Adı** label (pink/rose text)
- Input field (pink/rose border, pink background)
- **E-posta** label (pink/rose text)
- Input field (pink/rose border, pink background)
- **Devam Et** button (pink/rose background, white text)

### Divider
- "────────── veya ──────────" divider line

### Social Login Buttons (3 rows)
Row 1: Apple (black), Google (red/white), Facebook (blue)
Row 2: WhatsApp (green), Instagram (pink/purple), TikTok (black)
Row 3: Spotify (green)

### Bottom
- "Hesabın var mı? **Giriş Yap**" (Have an account? Login) — "Giriş Yap" in pink/rose

## Edits Made to Spec
1. Social buttons: Changed from single line to 3 rows (matching Login)
2. Social button colors: Added platform-specific colors
3. Bottom text: Changed from "Hesabın yok mu? Kayıt Ol" to "Hesabın var mı? Giriş Yap"
4. Divider text: Simplified from "veya alternatif ile devam et" to "veya"
5. Left side: Added note that it matches Login background
6. Timestamp updated to 2026-08-19
