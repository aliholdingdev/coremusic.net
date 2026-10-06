---
title: "K10 Mobile Responsive - Mobil Responsive Tasarım"
layer: K10
category: "Uygulama"
date: 2026-09-20
---

# K10 Mobile Responsive

## Genel Bakış

Mobile Responsive modülü, COREMUSIC'in tüm panellerinin mobil cihazlarda sorunsuz çalışmasını sağlayan tasarım sistemi ve gesture yönetimini içerir. Touch-first tasarım prensipleri, responsive breakpoint'leri, gesture tanıma ve mobil-specific UI bileşenlerini kapsar.

## Ekran/Diyagram

```
┌───────────────────────┐  ┌───────────────────────┐
│  📱 Mobil Ana Ekran   │  │  📱 Music Panel       │
│                       │  │                       │
│  🎵 COREMUSIC        │  │  ☰  Müzik Paneli  🔍  │
│  ─────────────────── │  │  ─────────────────── │
│                       │  │                       │
│  ┌─────────────────┐ │  │  ┌─────────────────┐  │
│  │ 🎵 Music        │ │  │  │ 🎵 Now Playing  │  │
│  │ ▸ Playlists     │ │  │  │ ┌─────────────┐ │  │
│  │ ▸ Albums        │ │  │  │ │  🎶 Cover    │ │  │
│  │ ▸ Artists       │ │  │  │ │             │ │  │
│  │                 │ │  │  │ │ Song Title  │ │  │
│  │ 🏠 Home         │ │  │  │ │ Artist      │ │  │
│  │ ▸ Rooms         │ │  │  │ └─────────────┘ │  │
│  │ ▸ Devices       │ │  │  │ ⏮ ⏯ ⏭        │  │
│  │                 │ │  │  │ ═══════●═══════ │  │
│  │ 🎙 Studio       │ │  │  └─────────────────┘  │
│  │ ▸ Recording     │ │  │                       │
│  │ ▸ Mixing        │ │  │  📋 Playlist           │
│  │                 │ │  │  ┌─────────────────┐  │
│  │ ⚙ Settings     │ │  │  │ 1. Song A    ▶  │  │
│  │                 │ │  │  │ 2. Song B       │  │
│  │ ─────────────── │ │  │  │ 3. Song C       │  │
│  │ [🏠][🎵][🎙][⚙]│ │  │  │ 4. Song D       │  │
│  └─────────────────┘ │  │  └─────────────────┘  │
│                       │  │                       │
│  Swipe ↑: Menu       │  │  ← Swipe: Back        │
│  Swipe ←: Back       │  │  Swipe ↓: Mini Player  │
└───────────────────────┘  └───────────────────────┘

┌───────────────────────┐  ┌───────────────────────┐
│  📱 Gesture Haritası   │  │  📱 Touch Areas       │
│                       │  │                       │
│  ┌─────────────────┐ │  │  ┌─────────────────┐  │
│  │    ┌───────┐    │ │  │  │ ████░░░░░░░░░░░ │  │
│  │    │ Swipe │    │ │  │  │ Safe Area (Top)  │  │
│  │    │  ↑↓   │    │ │  │  │                 │  │
│  │    └───────┘    │ │  │  │ ░░░░░░░░░░░░░░░ │  │
│  │                 │ │  │  │ Touch Zone       │  │
│  │  ← ─── ○ ─── → │ │  │  │ (44x44px min)   │  │
│  │  Swipe ←→      │ │  │  │                 │  │
│  │                 │ │  │  │ ░░░░░░░░░░░░░░░ │  │
│  │    ┌───────┐    │ │  │  │ Bottom Nav       │  │
│  │    │ Pinch │    │ │  │  │ (88px height)   │  │
│  │    │ Zoom  │    │ │  │  │                 │  │
│  │    └───────┘    │ │  │  │ ████████████████│  │
│  │                 │ │  │  │ Home Indicator    │  │
│  │    ┌───────┐    │ │  │  └─────────────────┘  │
│  │    │Long   │    │ │  │                       │
│  │    │Press  │    │ │  │  Min Touch: 44x44px   │
│  │    └───────┘    │ │  │  Min Spacing: 8px     │
│  └─────────────────┘ │  └───────────────────────┘
└───────────────────────┘
```

## Teknik Detaylar

### Bileşen Yapısı

```
MobileResponsive/
├── Layout/
│   ├── MobileLayout.tsx         # Mobil layout
│   ├── MobileNav.tsx            # Alt navigasyon
│   ├── MobileHeader.tsx         # Mobil üst bar
│   ├── BottomSheet.tsx          # Alt sheet
│   └── SwipeableView.tsx        # Swipe alanları
├── Gestures/
│   ├── GestureHandler.tsx       # Gesture handler
│   ├── SwipeDetector.tsx        # Swipe tanıma
│   ├── PinchZoom.tsx            # Pinch-to-zoom
│   ├── LongPress.tsx            # Uzun basma
│   ├── PullToRefresh.tsx        # Pull-to-refresh
│   └── DragAndDrop.tsx          # Mobil sürükle-bırak
├── Components/
│   ├── MobileCard.tsx           # Mobil kart
│   ├── MobileList.tsx           # Mobil liste
│   ├── MobileModal.tsx          # Mobil modal
│   ├── MobileToast.tsx          # Mobil toast
│   ├── MobileTabs.tsx           # Mobil tab
│   └── MobileAccordion.tsx      # Mobil accordium
├── Responsive/
│   ├── BreakpointProvider.tsx   # Breakpoint sağlayıcı
│   ├── ContainerQuery.tsx       # Container queries
│   ├── FluidTypography.tsx      # Akışkan tipografi
│   └── AdaptiveLayout.tsx       # Adaptif layout
├── Touch/
│   ├── TouchRipple.tsx          # Touch ripple efekti
│   ├── HapticFeedback.tsx       # Dokunsal geri bildirim
│   ├── TouchSlop.tsx            # Touch slop yönetimi
│   └── PreventZoom.tsx          # Zoom engelleme
└── Shared/
    ├── ResponsiveImage.tsx      # Responsive image
    ├── ViewportUnits.tsx        # Vh/vw yardımcıları
    └── SafeArea.tsx             # Safe area insets
```

### Breakpoint Sistemi

```css
/* Tailwind CSS 4 breakpoints */
/* Mobile First Yaklaşım */

/* Default: 0px+ (Mobil) */
/* sm: 640px+ (Büyük mobil) */
/* md: 768px+ (Tablet) */
/* lg: 1024px+ (Masaüstü) */
/* xl: 1280px+ (Büyük masaüstü) */
/* 2xl: 1536px+ (Çok büyük) */
```

Breakpoint özelinde bileşen davranışları:
- **0-639px**: Tek sütun, alt navigasyon, swipe menü
- **640-767px**: İki sütun, compact sidebar
- **768-1023px**: Sidebar + content, tablet optimizasyonu
- **1024-1279px**: Tam masaüstü layout
- **1280+**: Geniş ekran, çoklu panel desteği

### Gesture Sistemi

Tanınan gesture'lar:

| Gesture | İşlem | Bölge |
|---------|-------|-------|
| Swipe Left | Geri git / Next | Tüm ekran |
| Swipe Right | İleri git / Previous | Sol kenar |
| Swipe Up | Menü aç / Queue göster | Alt kısım |
| Swipe Down | Menü kapat / Mini player | Üst kısım |
| Pinch In | Uzaklaş / Zoom out | Medya alanı |
| Pinch Out | Yakınlaş / Zoom in | Medya alanı |
| Long Press | Context menü | Liste öğeleri |
| Double Tap | Play/Pause | Video alanı |
| Pull Down | Yenile | Liste başı |
| Two-finger Swipe | Hız değiştir | Progress bar |

### Touch Optimizasyonları

Mobil cihazlar için dokunma optimizasyonları:
- **Touch Target**: Minimum 44x44px dokunma alanı
- **Touch Slop**: 8px'lik dokunma toleransı
- **Passive Listeners**: Performans için passive event listener
- **Touch Action**: `touch-action: manipulation` ile pinch zoom engelleme
- **Overscroll**: `overscroll-behavior: contain` ile zincirleme scroll engelleme
- **Will-Change**: Animasyonlu öğeler için will-change optimizasyonu

### Responsive Tipografi

`clamp()` ile akışkan yazı boyutları:

```css
/* Mobil: 14px → Masaüstü: 16px */
body { font-size: clamp(0.875rem, 0.8rem + 0.25vw, 1rem); }

/* H1: Mobil 24px → Masaüstü 48px */
h1 { font-size: clamp(1.5rem, 1rem + 2vw, 3rem); }

/* H2: Mobil 20px → Masaüstü 36px */
h2 { font-size: clamp(1.25rem, 0.9rem + 1.5vw, 2.25rem); }
```

### Haptic Feedback

Dokunsal geri bildirim desteği:
- **Light**: hapticFeedbackImpactOccurred('light')
- **Medium**: hapticFeedbackImpactOccurred('medium')
- **Heavy**: hapticFeedbackImpactOccurred('heavy')
- **Selection**: hapticFeedbackSelectionChanged()
- **Success**: hapticFeedbackNotificationOccurred('success')
- **Error**: hapticFeedbackNotificationOccurred('error')

### Mobil-Specific Bileşenler

- **Bottom Navigation**: Alt navigasyon çubuğu
- **Bottom Sheet**: Aşağıdan kayan panel
- **Pull-to-Refresh**: Aşağı çekerek yenileme
- **Infinite Scroll**: Sonsuz kaydırma
- **Swipe-to-Action**: Kaydırarak işlem (sil, arşivle)
- **Floating Action Button**: Yüzen işlem butonu
- **Snackbar**: Alt bildirim çubuğu
- **Chip**: Filtre/etiket çipleri

### Safe Area Yönetimi

Modern mobil cihazlar için safe area:
- **iPhone Notch**: Üst safe area (dynamic island)
- **Home Indicator**: Alt safe area
- **Landscape**: Yatay mod safe area
- **Status Bar**: Durum çubuğu yüksekliği

```css
/* Safe area padding */
padding-top: env(safe-area-inset-top);
padding-bottom: env(safe-area-inset-bottom);
padding-left: env(safe-area-inset-left);
padding-right: env(safe-area-inset-right);
```

## Bağımlılıklar

| Katman/Bileşen | Bağımlılık | Açıklama |
|----------------|-----------|----------|
| K10 | Tüm Paneller | Responsive layout sağlama |
| K10 | Theme Engine | Mobil temalar |
| K10 | Accessibility | Erişilebilirlik |
| K0 | Browser API | Touch events, viewport |
| K6 | Network | Offline durum yönetimi |
| K10 | PWA Features | PWA entegrasyonu |

## Durum: Implementasyon

**Durum**: 🟡 Planlama Aşamasında
**Öncelik**: Yüksek
**Kapsam**: Breakpoints, Gestures, Touch Optimizations, Safe Area, Mobile Components
**Test Kapsamı**: Unit test (gesture), Visual regression, Cross-device testing, Touch testing
