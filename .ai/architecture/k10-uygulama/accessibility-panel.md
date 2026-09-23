---
title: "K10 Accessibility Panel - Erişilebilirlik"
layer: K10
category: "Uygulama"
date: 2026-09-20
---

# K10 Accessibility Panel

## Genel Bakış

Accessibility Panel, COREMUSIC'in WCAG 2.2 AA standartlarına uygun erişilebilirlik özelliklerini yönetir. Screen reader desteği, keyboard navigasyonu, renk kontrastı ayarları, font ölçekleme ve ARIA label yönetimi gibi kapsamlı erişilebilirlik çözümleri sunar.

## Ekran/Diyagram

```
┌─────────────────────────────────────────────────────────┐
│  ♿ Erişilebilirlik Ayarları                           │
├──────────┬──────────────────────────────────────────────┤
│          │  ┌──────────────────────────────────────┐    │
│ 👁       │  │  👁 Görüş Ayarları                   │    │
│ Görüş   │  │                                      │    │
│          │  │  Yazı Boyutu: [100%] ▼              │    │
│ 🔊 Ses  │  │  ○ %75  ○ %100  ● %125  ○ %150     │    │
│          │  │                                      │    │
│ ⌨       │  │  Yüksek Kontrast: [☐]               │    │
│ Klavye  │  │  Az Kontrast: [☐]                   │    │
│          │  │  az Kontrast: [☐]                   │    │
│ 🎨 Renk │  │  az Kontrast: [☐]                   │    │
│          │  │                                      │    │
│ 📖      │  │  Animasyon Azalt: [☐]               │    │
│ Okunabilirlik│  │  Odak Göstergesi: [☑]           │    │
│          │  │  Cursor Büyüklüğü: [Normal] ▼      │    │
│          │  └──────────────────────────────────────┘    │
│          │                                             │
│          │  ┌──────────────────────────────────────┐    │
│          │  │  🔊 Ses Ayarları                     │    │
│          │  │                                      │    │
│          │  │  Ekran Okuyucu: [☑]                 │    │
│          │  │  Talk Back: [☑]                     │    │
│          │  │  VoiceOver: [☑]                     │    │
│          │  │                                      │    │
│          │  │  Ses Bildirimleri: [☑]              │    │
│          │  │  Titreşim: [☑]                      │    │
│          │  └──────────────────────────────────────┘    │
│          │                                             │
│          │  ┌──────────────────────────────────────┐    │
│          │  │  ⌨ Klavye Navigasyonu               │    │
│          │  │                                      │    │
│          │  │  Tab → Sonraki öğe                  │    │
│          │  │  Shift+Tab → Önceki öğe             │    │
│          │  │  Enter → Seç/Onayla                 │    │
│          │  │  Escape → Kapat/Geri dön            │    │
│          │  │  Space → Play/Pause                 │    │
│          │  │  →←↑↓ → Hareket                    │    │
│          │  │  Home → Başa                        │    │
│          │  │  End → Sona                         │    │
│          │  │  F1 → Yardım                        │    │
│          │  │                                      │    │
│          │  │  [📋 Kısayol Listesini Göster]      │    │
│          │  └──────────────────────────────────────┘    │
└──────────┴──────────────────────────────────────────────┘
```

## Teknik Detaylar

### Bileşen Yapısı

```
AccessibilityPanel/
├── AccessibilityLayout.tsx       # Ana layout
├── Vision/
│   ├── VisionSettings.tsx        # Görüş ayarları
│   ├── FontScaler.tsx            # Font ölçekleme
│   ├── ContrastSelector.tsx      # Kontrast seçici
│   ├── HighContrastMode.tsx      # Yüksek kontrast modu
│   ├── ReducedMotion.tsx         # Animasyon azaltma
│   ├── FocusIndicator.tsx        # Odak göstergesi
│   └── CursorSettings.tsx        # Cursor ayarları
├── Audio/
│   ├── AudioSettings.tsx         # Ses ayarları
│   ├── ScreenReaderConfig.tsx    # Ekran okuyucu yapılandırma
│   ├── CaptionsSettings.tsx      # Altyazı/caption ayarları
│   └── AudioDescriptions.tsx     # Sesli betimleme
├── Keyboard/
│   ├── KeyboardNav.tsx           # Klavye navigasyonu
│   ├── ShortcutList.tsx          # Kısayol listesi
│   ├── TabTrap.tsx               # Tab trap yönetimi
│   ├── FocusManager.tsx          # Focus yönetimi
│   └── SkipLinks.tsx             # Skip link'ler
├── Color/
│   ├── ColorSettings.tsx         # Renk ayarları
│   ├── ColorBlindModes.tsx       # Renk körlüğü modları
│   └── ThemeContrast.tsx         # Tema kontrast kontrolü
├── ARIA/
│   ├── ARIALabels.tsx            # ARIA label yönetimi
│   ├── ARIARegions.tsx           # ARIA region'ları
│   ├── ARIAAnnouncements.tsx     # Live region duyuruları
│   └── ARIARoles.tsx             # ARIA rol atamaları
├── Testing/
│   ├── A11yChecker.tsx           # Erişilebilirlik kontrolü
│   ├── ContrastChecker.tsx       # Kontrast kontrolü
│   └── AuditReport.tsx           # Audit raporu
└── Shared/
    ├── LiveRegion.tsx            # ARIA live region
    ├── VisuallyHidden.tsx        # Görünmez ama okunabilir
    └── FocusTrap.tsx             # Focus trap bileşeni
```

### State Management

```typescript
// Accessibility Store - Zustand
interface AccessibilityState {
  // Vision
  fontScale: number;            // 0.75, 1, 1.25, 1.5
  highContrast: boolean;
  reducedMotion: boolean;
  focusIndicator: boolean;
  cursorSize: 'small' | 'medium' | 'large';

  // Audio
  screenReaderEnabled: boolean;
  captionsEnabled: boolean;
  audioDescriptions: boolean;
  soundEffects: boolean;

  // Keyboard
  keyboardNavEnabled: boolean;
  tabTrapEnabled: boolean;
  skipLinksVisible: boolean;

  // Color
  colorBlindMode: 'none' | 'protanopia' | 'deuteranopia' | 'tritanopia';

  // Actions
  setFontScale: (scale: number) => void;
  toggleHighContrast: () => void;
  toggleReducedMotion: () => void;
  toggleFocusIndicator: () => void;
  setCursorSize: (size: string) => void;
  toggleScreenReader: () => void;
  toggleCaptions: () => void;
  toggleAudioDescriptions: () => void;
  toggleKeyboardNav: () => void;
  setColorBlindMode: (mode: string) => void;
  announceToScreenReader: (message: string, priority: 'polite' | 'assertive') => void;
}
```

### WCAG 2.2 AA Uyumluluk Matrisi

| Kriter | Seviye | Durum | Açıklama |
|--------|--------|-------|----------|
| 1.1.1 Non-text Content | A | ✅ | Alt metin, ARIA labels |
| 1.3.1 Info and Relationships | A | ✅ | Semantic HTML, ARIA |
| 1.4.1 Use of Color | A | ✅ | Renk tek başına bilgi taşımaz |
| 1.4.3 Contrast Minimum | AA | ✅ | 4.5:1 normal, 3:1 büyük |
| 1.4.4 Resize Text | AA | ✅ | %200'e kadar ölçekleme |
| 1.4.10 Reflow | AA | ✅ | 320px'de yeniden akış |
| 1.4.11 Non-text Contrast | AA | ✅ | 3:1 UI kontrastı |
| 2.1.1 Keyboard | A | ✅ | Tüm işlevler klavye ile |
| 2.1.2 No Keyboard Trap | A | ✅ | Tab trap çıkış yolu |
| 2.4.1 Bypass Blocks | A | ✅ | Skip link'ler |
| 2.4.3 Focus Order | A | ✅ | Mantıksal sıralama |
| 2.4.7 Focus Visible | AA | ✅ | Görünür odak göstergesi |
| 2.5.8 Target Size | AA | ✅ | Min 24x24px |
| 3.1.1 Language of Page | A | ✅ | lang attribute |
| 3.3.1 Error Identification | A | ✅ | Hata tanımlama |
| 4.1.2 Name, Role, Value | A | ✅ | ARIA özellikleri |

### Keyboard Navigasyon Sistemi

Tüm interaktif öğeler klavye ile erişilebilir:

```typescript
// Focus yönetimi
const focusManager = {
  // Primer navigasyon
  tab: 'Sonraki öğeye geç',
  shiftTab: 'Önceki öğe',
  
  //iero navigasyon
  arrowRight: 'Sağa hareket',
  arrowLeft: 'Sola hareket',
  arrowUp: 'Yukarı hareket',
  arrowDown: 'Aşağı hareket',
  
  // Eylemler
  enter: 'Seç/Onayla',
  space: 'Oynat/Duraklat',
  escape: 'Kapat/Geri dön',
  
  // Kısayollar
  home: 'Listenin başına',
  end: 'Listenin sonuna',
  pageUp: 'Bir sayfa yukarı',
  pageDown: 'Bir sayfa aşağı',
  
  // Özel
  'ctrl+home': 'Sayfanın başına',
  'ctrl+end': 'Sayfanın sonuna',
};
```

### Screen Reader Desteği

Ekran okuyucular için optimizasyon:
- **ARIA Live Regions**: Dinamik içerik değişiklikleri
- **ARIA Labels**: Tüm interaktif öğeler için etiketler
- **ARIA Descriptions**: Karmaşık bileşenler için açıklamalar
- **ARIA Roles**: Özel roller için doğru rol atamaları
- **State Changes**: Durum değişiklikleri için bildirim
- **Headings Hierarchy**: Doğru başlık hiyerarşisi
- **Landmarks**: Sayfa landmark'ları (main, nav, aside)

### Renk Körlüğü Desteği

Renk körlüğü modları:
- **Protanopia**: Kırmızı renk körlüğü
- **Deuteranopia**: Yeşil renk körlüğü
- **Tritanopia**: Mavi renk körlüğü
- **High Contrast**: Yüksek kontrast modu
- **Dark Mode**: Karanlık mod
- **Light Mode**: Aydınlık mod

Her mod için renk paleti CSS custom properties ile değiştirilir.

### Focus Trap Mekanizması

Modal ve dialog bileşenleri için focus trap:
- **Tab**: Focus'utrap içinde tutma
- **Shift+Tab**: Ters yönde trap
- **Escape**: Trap'ten çıkış
- **Auto-focus**: Trap'e girişte ilk öğeye odaklanma
- **Restore Focus**: Trap'ten çıkışta eski focus'u geri yükleme

### Test ve Audit Entegrasyonu

Otomatik erişilebilirlik testleri:
- **axe-core**: Entegre accessibility testing
- **Lighthouse**: A11y skoru kontrolü
- **Pa11y**: CI/CD entegrasyonu
- **Custom Checks**: COREMUSIC-specific checks

## Bağımlılıklar

| Katman/Bileşen | Bağımlılık | Açıklama |
|----------------|-----------|----------|
| K10 | Tüm Paneller | Erişilebilirlik uygulama |
| K10 | Theme Engine | Tema kontrastları |
| K10 | Mobile Responsive | Touch erişilebilirliği |
| K0 | Browser API | Screen reader API |
| K7 | Middleware | ARIA header'ları |
| K10 | PWA Features | Offline erişilebilirlik |

## Durum: Implementasyon

**Durum**: 🟡 Planlama Aşamasında
**Öncelik**: Yüksek (Yasal zorunluluk)
**Kapsam**: WCAG 2.2 AA, Screen Reader, Keyboard Nav, Color Blind, Focus Management
**Test Kapsamı**: Unit test, axe-core audit, Manual screen reader testing, Keyboard testing
