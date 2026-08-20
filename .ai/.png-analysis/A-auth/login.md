---
title: CoreMusic — Login PNG Analysis (1024×600)
date: 2026-08-19
updated: 2026-08-19
type: png-analysis
status: active
version: 1.0.0
source: Linux 1024 - Login Girl.png
dimensions: 1024x600
platform: Linux Embedded / Raspberry Pi 5
---

# CoreMusic — Login PNG Analysis

**Source:** Linux 1024 - Login Girl.png
**Dimensions:** 1024×600px (RPi5 embedded)
**Status:** Verified against `screens/A-auth/login.md`

## Layout Analysis

### Full-Screen Layout
- 78/22 split (left hero / right glass panel)
- Full-bleed background image: Woman in pink floral landscape (same as Select Gender)

### Left Side (Hero)
- CoreMusic logo (stylized "C" with diamond/gem icon) + "Core Music" text (pink/rose)
- "Aşkınla" (With your love) — decorative script text (Bickham Script)
- "milkyenine!" — decorative script text
- "sistem. Milyonlarca şarkı, özel oluşturulmuş playlistler, sonsuz müzik keyfi. Senin için" — description text

### Right Side (Glass Panel)
- Semi-transparent glass panel (~224px wide)
- Top: Decorative line art illustration (woman's face with stars, white)
- "Hoş Geldin" heading (large, white)
- "Hesabına giriş yap, müziğin keyfini çıkar" subtitle (smaller, gray)

### Login Form
- **Email/Telefon veya Kullanıcı Adı** label (pink/rose text)
- Input field (pink/rose border, pink background)
- **Şifre** label (pink/rose text)
- Input field (pink/rose border, pink background)
- **Beni Hatırla** checkbox (pink/rose) + **Şifremi Unuttum** link (pink/rose text, right-aligned)
- **Giriş Yap** button (pink/rose background, white text)

### Divider
- "────────── veya ──────────" divider line

### Social Login Buttons (3 rows)
Row 1: Apple (black), Google (red/white), Facebook (blue)
Row 2: WhatsApp (green), Instagram (pink/purple), TikTok (black)
Row 3: Spotify (green) — NOT microphone as spec stated

### Bottom
- "Hesabın yok mu? **Kayıt Ol**" (Don't have an account? Sign Up) — "Kayıt Ol" in pink/rose

## Edits Made to Spec
1. Social buttons row 3: Changed from 🎤 Microphone to 🎵 Spotify
2. Social button colors: Added platform-specific colors (Apple=black, Google=red, Facebook=blue, WhatsApp=green, Instagram=pink, TikTok=black, Spotify=green)
3. Left side text: Updated to match PNG ("Aşkınla milkyenine!" + description)
4. Background description: Updated to "kadın fotoğrafı (pembe çiçekli manzara)"
5. ASCII wireframes: Updated all 4 platform variants (RPi5, Desktop, Mobile, TV)
6. Timestamp updated to 2026-08-19
