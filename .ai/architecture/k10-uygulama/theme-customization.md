---
title: "K10 Theme Customization - Tema Özelleştirme"
layer: K10
category: "Uygulama"
date: 2026-09-20
---

# K10 Theme Customization

## Genel Bakış

Theme Customization modülü, COREMUSIC'in tema ve görünüm özelleştirme motorunu yönetir. Dark/Light mod geçişleri, custom tema oluşturma, renk paleti yönetimi, font seçimi ve CSS custom properties ile dinamik tema değişikliklerini içerir. Kullanıcıların kişisel tercihlerine göre arayüzü tamamen özelleştirmesini sağlar.

## Ekran/Diyagram

```
┌─────────────────────────────────────────────────────────┐
│  🎨 Tema Özelleştirme                [👁 Önizleme]     │
├──────────┬──────────────────────────────────────────────┤
│          │  ┌──────────────────────────────────────┐    │
│ 🌓 Mods │  │  🌓 Tema Modu                       │    │
│          │  │                                      │    │
│ 🎨 Renk │  │  ┌─────────┐ ┌─────────┐ ┌─────────┐│    │
│          │  │  │ ☀ Light │ │ 🌙 Dark │ │ 🔄 Auto ││    │
│ 🔤 Font │  │  │  ● Sec  │ │         │ │         ││    │
│          │  │  └─────────┘ └─────────┘ └─────────┘│    │
│ 📐 Spacing│ │                                      │    │
│          │  │  Sistem Tercihi: [☑]                 │    │
│ 🖼 Custom│  │  Geçiş Süresi: [300ms] ▼            │    │
│ Themes  │  │                                      │    │
│          │  └──────────────────────────────────────┘    │
│ 📦 Themes│                                            │
│          │  ┌──────────────────────────────────────┐    │
│          │  │  🎨 Renk Paleti                      │    │
│          │  │                                      │    │
│          │  │  Primary:    [■ #6C63FF] [Değiştir]  │    │
│          │  │  Secondary:  [■ #FF6584] [Değiştir]  │    │
│          │  │  Accent:     [■ #00D9FF] [Değiştir]  │    │
│          │  │  Background: [■ #0f0f23] [Değiştir]  │    │
│          │  │  Surface:    [■ #1a1a2e] [Değiştir]  │    │
│          │  │  Text:       [■ #ffffff] [Değiştir]  │    │
│          │  │                                      │    │
│          │  │  Hazır Paletler:                     │    │
│          │  │  [🟣 Cosmic] [🔵 Ocean] [🟢 Forest] │    │
│          │  │  [🔴 Sunset] [⚪ Minimal] [🎨 Custom]│    │
│          │  └──────────────────────────────────────┘    │
│          │                                             │
│          │  ┌──────────────────────────────────────┐    │
│          │  │  🔤 Tipografi                        │    │
│          │  │                                      │    │
│          │  │  Başlık Fontu: [Inter] ▼             │    │
│          │  │  Gövde Fontu: [Inter] ▼              │    │
│          │  │  Monospace Font: [Fira Code] ▼       │    │
│          │  │                                      │    │
│          │  │  Font Boyutu: [16px] ▼              │    │
│          │  │  Satır Yüksekliği: [1.5] ▼          │    │
│          │  │  Harf Aralığı: [0] ▼               │    │
│          │  └──────────────────────────────────────┘    │
│          │                                             │
│          │  ┌──────────────────────────────────────┐    │
│          │  │  📐 Bileşen Stilleri                 │    │
│          │  │                                      │    │
│          │  │  Border Radius: [12px] ▼             │    │
│          │  │  Shadow Intensity: [Medium] ▼       │    │
│          │  │  Card Style: [Filled] ▼             │    │
│          │  │  Button Style: [Rounded] ▼          │    │
│          │  │                                      │    │
│          │  │  [💾 Kaydet] [↺ Varsayılana Dön]    │    │
│          │  └──────────────────────────────────────┘    │
└──────────┴──────────────────────────────────────────────┘
```

## Teknik Detaylar

### Bileşen Yapısı

```
ThemeCustomization/
├── ThemeManager.tsx              # Ana tema yöneticisi
├── Modes/
│   ├── ModeSelector.tsx          # Dark/Light/Auto seçici
│   ├── DarkMode.tsx              # Dark mod ayarları
│   ├── LightMode.tsx             # Light mod ayarları
│   ├── AutoMode.tsx              # Sistem tercihi modu
│   └── Transition.tsx            # Mod geçiş animasyonu
├── Colors/
│   ├── ColorPalette.tsx          # Renk paleti
│   ├── ColorPicker.tsx           # Renk seçici
│   ├── ColorSwatch.tsx           # Renk swatch'ı
│   ├── PresetPalettes.tsx        # Hazır paletler
│   └── ColorHarmony.tsx          # Renk uyumu araçları
├── Typography/
│   ├── FontSelector.tsx          # Font seçici
│   ├── FontPreview.tsx           # Font önizleme
│   ├── FontScaler.tsx            # Font ölçekleme
│   └── LineHeight.tsx            # Satır yüksekliği
├── Components/
│   ├── BorderRadius.tsx          # Border radius ayarı
│   ├── ShadowSettings.tsx        # Shadow ayarları
│   ├── CardStyle.tsx             # Kart stili
│   ├── ButtonStyle.tsx           # Buton stili
│   └── SpacingScale.tsx          # Spacing ölçeği
├── Custom/
│   ├── CustomThemeEditor.tsx     # Özel tema editörü
│   ├── ThemeImport.tsx           # Tema içe aktarma
│   ├── ThemeExport.tsx           # Tema dışa aktarma
│   └── ThemeShare.tsx            # Tema paylaşma
├── Preview/
│   ├── ThemePreview.tsx          # Tema önizleme
│   ├── LivePreview.tsx           # Canlı önizleme
│   └── MockupView.tsx            # Mockup görünümü
└── Shared/
    ├── ThemeProvider.tsx         # Tema sağlayıcı
    ├── CSSVariables.tsx          # CSS custom properties
    └── ThemeContext.tsx          # Tema context
```

### State Management

```typescript
// Theme Store - Zustand
interface ThemeState {
  // Mode
  mode: 'light' | 'dark' | 'auto';
  systemPreference: 'light' | 'dark';

  // Colors
  colors: ThemeColors;
  activePreset: string | null;

  // Typography
  fonts: ThemeFonts;
  fontScale: number;

  // Components
  borderRadius: number;
  shadowIntensity: 'none' | 'sm' | 'md' | 'lg' | 'xl';
  cardStyle: 'filled' | 'outlined' | 'elevated';
  buttonStyle: 'rounded' | 'square' | 'pill';
  spacingScale: number;

  // Custom
  customThemes: CustomTheme[];
  activeThemeId: string | null;

  // Actions
  setMode: (mode: 'light' | 'dark' | 'auto') => void;
  setColor: (key: string, value: string) => void;
  setPreset: (presetId: string) => void;
  setFont: (type: string, family: string) => void;
  setFontScale: (scale: number) => void;
  setBorderRadius: (radius: number) => void;
  setShadowIntensity: (intensity: string) => void;
  setCardStyle: (style: string) => void;
  setButtonStyle: (style: string) => void;
  createCustomTheme: (theme: Omit<CustomTheme, 'id'>) => string;
  updateCustomTheme: (id: string, updates: Partial<CustomTheme>) => void;
  deleteCustomTheme: (id: string) => void;
  applyTheme: (themeId: string) => void;
  exportTheme: (themeId: string) => string;
  importTheme: (themeJson: string) => string;
  resetToDefault: () => void;
}

// ThemeColors Tipi
interface ThemeColors {
  primary: string;
  secondary: string;
  accent: string;
  background: string;
  surface: string;
  text: string;
  textSecondary: string;
  border: string;
  error: string;
  warning: string;
  success: string;
  info: string;
}

// ThemeFonts Tipi
interface ThemeFonts {
  heading: string;
  body: string;
  mono: string;
}

// CustomTheme Tipi
interface CustomTheme {
  id: string;
  name: string;
  description: string;
  mode: 'light' | 'dark';
  colors: ThemeColors;
  fonts: ThemeFonts;
  components: ThemeComponents;
  createdAt: Date;
  author: string;
  isPublic: boolean;
}
```

### CSS Custom Properties Yapısı

```css
:root {
  /* Renkler */
  --color-primary: #6C63FF;
  --color-secondary: #FF6584;
  --color-accent: #00D9FF;
  --color-background: #0f0f23;
  --color-surface: #1a1a2e;
  --color-text: #ffffff;
  --color-text-secondary: #a0a0b0;
  --color-border: #2a2a3e;

  /* Durum Renkleri */
  --color-error: #ff5252;
  --color-warning: #ffab40;
  --color-success: #69f0ae;
  --color-info: #40c4ff;

  /* Fontlar */
  --font-heading: 'Inter', sans-serif;
  --font-body: 'Inter', sans-serif;
  --font-mono: 'Fira Code', monospace;

  /* Font Boyutları */
  --font-size-xs: 0.75rem;
  --font-size-sm: 0.875rem;
  --font-size-base: 1rem;
  --font-size-lg: 1.125rem;
  --font-size-xl: 1.25rem;
  --font-size-2xl: 1.5rem;
  --font-size-3xl: 2rem;

  /* Spacing */
  --spacing-unit: 4px;
  --spacing-xs: calc(var(--spacing-unit) * 1);
  --spacing-sm: calc(var(--spacing-unit) * 2);
  --spacing-md: calc(var(--spacing-unit) * 3);
  --spacing-lg: calc(var(--spacing-unit) * 4);
  --spacing-xl: calc(var(--spacing-unit) * 6);

  /* Border Radius */
  --radius-sm: 4px;
  --radius-md: 8px;
  --radius-lg: 12px;
  --radius-xl: 16px;
  --radius-full: 9999px;

  /* Shadows */
  --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.3);
  --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.4);
  --shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.5);
  --shadow-xl: 0 20px 25px rgba(0, 0, 0, 0.6);

  /* Transitions */
  --transition-fast: 150ms ease;
  --transition-normal: 300ms ease;
  --transition-slow: 500ms ease;
}

/* Light Mode Override */
[data-theme="light"] {
  --color-background: #ffffff;
  --color-surface: #f5f5f5;
  --color-text: #1a1a2e;
  --color-text-secondary: #666680;
  --color-border: #e0e0e0;
  --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
  --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
  --shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.15);
}
```

### Tema Geçiş Animasyonu

Dark/Light mod geçişleri için smooth animasyon:
- **Transition Duration**: 300ms varsayılan
- **Easing**: ease-in-out
- **Per-element**: Her CSS property için ayrı transition
- **Reduced Motion**: prefers-reduced-motion desteği
- **System Sync**: prefers-color-scheme media query

### Renk Paleti Oluşturucu

Renk uyumu algoritmaları:
- **Complementary**: Tamamlayıcı renkler
- **Analogous**: Benzer renkler
- **Triadic**: Üçlü renk uyumu
- **Split-Complementary**: Bölünmüş tamamlayıcı
- **Monochromatic**: Tek renk tonları
- **Custom**: Manuel renk seçimi

### Tema Paylaşım Sistemi

Kullanıcılar temalarını paylaşabilir:
- **Export**: JSON formatında tema dışa aktarma
- **Import**: JSON'dan tema içe aktarma
- **Share Link**: Tema paylaşım linki
- **Public Gallery**: Topluluk tema galerisi
- **Version Control**: Tema versiyonlama

### Performans Optimizasyonu

Tema değişikliklerinde performans:
- **CSS Variables**: Runtime'da değişken değiştirme
- **will-change**: Animasyonlu öğeler için optimize
- **RAF**: requestAnimationFrame ile animasyon
- **Batch Updates**: Toplu CSS güncelleme
- **Debounce**: Hızlı değişikliklerde debounce

## Bağımlılıklar

| Katman/Bileşen | Bağımlılık | Açıklama |
|----------------|-----------|----------|
| K10 | Tüm Paneller | Tema uygulama |
| K0 | Browser API | prefers-color-scheme |
| K0 | Dosya Sistemi | Tema kaydetme/yükleme |
| K5 | Veri Yönetimi | Tema tercihleri |
| K10 | Mobile Responsive | Mobil tema optimizasyonu |
| K10 | Accessibility | Kontrast erişilebilirliği |
| K10 | PWA Features | Manifest tema renkleri |

## Durum: Implementasyon

**Durum**: 🟡 Planlama Aşamasında
**Öncelik**: Yüksek
**Kapsam**: Dark/Light/Auto Mode, Color Palette, Typography, Custom Themes, Presets
**Test Kapsamı**: Unit test, Visual regression, Contrast audit, Cross-browser testing
