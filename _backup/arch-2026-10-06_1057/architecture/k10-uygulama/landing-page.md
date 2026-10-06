---
title: "K10 Landing Page - Ürün Tanıtım Sayfası"
layer: K10
category: "Uygulama"
date: 2026-09-20
---

# K10 Landing Page

## Genel Bakış

Landing Page, COREMUSIC'in kamuya açık tanıtım ve pazarlama sayfasıdır. Ürün özelliklerini, fiyatlandırma bilgilerini, müşteri yorumlarını ve demo videolarını sergiler. Dönüşüm optimizasyonu için A/B test desteği ve analitik entegrasyonu ile tasarlanmıştır.

## Ekran/Diyagram

```
┌─────────────────────────────────────────────────────────┐
│  🎵 COREMUSIC          Özellikler  Fiyatlar  Demo  [Giriş]│
├─────────────────────────────────────────────────────────┤
│                                                         │
│  ╔═══════════════════════════════════════════════════╗   │
│  ║  Müziğin Gücünü Keşfedin                         ║   │
│  ║                                                   ║   │
│  ║  🎧 Profesyonel ses kalitesi, sınırsız müzik     ║   │
│  ║  evreni. Ev, araç ve stüdyo için tek platform.   ║   │
│  ║                                                   ║   │
│  ║  [🚀 Hemen Başla]  [▶ Demo İzle]                 ║   │
│  ║                                                   ║   │
│  ╚═══════════════════════════════════════════════════╝   │
│                                                         │
│  ┌──────────┬──────────┬──────────┬──────────┐         │
│  │ 🎵       │ 🏠       │ 🎙       │ 🚗       │         │
│  │ Müzik    │ Ev       │ Stüdyo   │ Araç     │         │
│  │ Paneli   │ Merkezi  │ Kayıt    │ İçi      │         │
│  │──────────│──────────│──────────│──────────│         │
│  │Playlist  │Multi-room│Recording │CarPlay   │         │
│  │Equalizer │Scenes    │Mixing    │Navigation│         │
│  │Queue     │Devices   │Effects   │Voice Cmd │         │
│  └──────────┴──────────┴──────────┴──────────┘         │
│                                                         │
│  ┌─────────────────────────────────────────────────┐   │
│  │  💰 Fiyatlandırma                                │   │
│  │                                                  │   │
│  │  ┌───────────┐ ┌───────────┐ ┌───────────┐     │   │
│  │  │ 🆓 Free   │ │ ⭐ Pro    │ │ 🏢 Enterprise│  │   │
│  │  │           │ │           │ │           │     │   │
│  │  │ $0/ay     │ │ $9.99/ay  │ │ $29.99/ay │     │   │
│  │  │           │ │           │ │           │     │   │
│  │  │ ✓ 50 saat │ │ ✓ Sınırsız│ │ ✓ Sınırsız│     │   │
│  │  │ ✓ Temel EQ│ │ ✓ 10-band │ │ ✓ Custom  │     │   │
│  │  │ ✓ 1 oda   │ │ ✓ 5 oda   │ │ ✓ Sınırsız│     │   │
│  │  │ ✗ Stüdyo  │ │ ✓ Stüdyo  │ │ ✓ Stüdyo+ │     │   │
│  │  │ ✗ API     │ │ ✓ API     │ │ ✓ API     │     │   │
│  │  │           │ │           │ │           │     │   │
│  │  │ [Başla]   │ │ [Satın Al]│ │ [İletişim]│     │   │
│  │  └───────────┘ └───────────┘ └───────────┘     │   │
│  └─────────────────────────────────────────────────┘   │
│                                                         │
│  ┌─────────────────────────────────────────────────┐   │
│  │  💬 Müşteri Yorumları                            │   │
│  │  "COREMUSIC ile stüdyo kayıtlarım inanılmaz    │   │
│  │   kaliteye ulaştı!" - Ahmet, Müzik Prodüktörü   │   │
│  │                                                  │   │
│  │  "Evimdeki tüm odaları tek ekrandan kontrol    │   │
│  │   etmek muhteşem." - Mehmet, Teknoloji Sever    │   │
│  └─────────────────────────────────────────────────┘   │
│                                                         │
│  🎵 COREMUSIC © 2026  │ Gizlilik  │ Koşullar  │ İletişim│
└─────────────────────────────────────────────────────────┘
```

## Teknik Detaylar

### Bileşen Yapısı

```
LandingPage/
├── LandingLayout.tsx            # Ana layout
├── Header/
│   ├── Navbar.tsx               # Üst navigasyon
│   ├── Logo.tsx                 # Logo
│   ├── NavLinks.tsx             # Menü linkleri
│   └── CTAButton.tsx            # Call-to-action butonu
├── Hero/
│   ├── HeroSection.tsx          # Ana hero bölümü
│   ├── HeroTitle.tsx            # Başlık
│   ├── HeroSubtitle.tsx         # Alt başlık
│   ├── HeroCTA.tsx              # CTA butonları
│   └── HeroImage.tsx            # Hero görseli
├── Features/
│   ├── FeaturesSection.tsx      # Özellikler bölümü
│   ├── FeatureCard.tsx          # Özellik kartı
│   ├── FeatureGrid.tsx          # Özellik grid
│   └── FeatureIcon.tsx          # Özellik ikonları
├── Pricing/
│   ├── PricingSection.tsx       # Fiyatlandırma bölümü
│   ├── PricingCard.tsx          # Fiyat kartı
│   ├── PricingToggle.tsx        # Aylık/Yıllık toggle
│   └── PricingFeature.tsx       # Fiyat özelliği
├── Testimonials/
│   ├── TestimonialSection.tsx   # Yorum bölümü
│   ├── TestimonialCard.tsx      # Yorum kartı
│   ├── TestimonialCarousel.tsx  # Yorum carousel
│   └── StarRating.tsx           # Yıldız değerlendirmesi
├── Demo/
│   ├── DemoSection.tsx          # Demo bölümü
│   ├── VideoPlayer.tsx          # Video oynatıcı
│   └── InteractiveDemo.tsx      # İnteraktif demo
├── CTA/
│   ├── CTASection.tsx           # Final CTA bölümü
│   └── NewsletterSignup.tsx     # Bülten kaydı
├── Footer/
│   ├── Footer.tsx               # Alt bilgi
│   ├── FooterLinks.tsx          # Footer linkleri
│   ├── SocialLinks.tsx          # Sosyal medya
│   └── Copyright.tsx            # Telif hakkı
└── Shared/
    ├── ScrollToTop.tsx          # Yukarı kaydır
    ├── LazyImage.tsx            # Lazy loading image
    └── AnimatedSection.tsx      # Animasyonlu bölüm
```

### State Management

```typescript
// Landing Store - Zustand
interface LandingState {
  // Pricing
  billingCycle: 'monthly' | 'yearly';
  selectedPlan: string | null;

  // Form
  email: string;
  isSubscribed: boolean;

  // Demo
  isVideoPlaying: boolean;
  currentDemo: string;

  // Analytics
  scrollDepth: number;
  ctaClicks: Record<string, number>;
  timeOnPage: number;

  // Actions
  setBillingCycle: (cycle: 'monthly' | 'yearly') => void;
  selectPlan: (planId: string) => void;
  subscribeNewsletter: (email: string) => Promise<boolean>;
  trackCTAClick: (ctaId: string) => void;
  trackScrollDepth: (depth: number) => void;
}
```

### SEO Optimizasyonu

Next.js Metadata API ile kapsamlı SEO:
- **Title**: COREMUSIC - Profesyonel Müzik Platformu
- **Description**: Ev, araç ve stüdyo için tek platform...
- **Open Graph**: Sosyal medya paylaşım görselleri
- **Twitter Cards**: Twitter paylaşım optimizasyonu
- **Structured Data**: JSON-LD ile zengin snippet
- **Canonical URL**: Duplicate içerik önleme
- **Sitemap**: Otomatik sitemap oluşturma
- **Robots.txt**: Arama motoru yönlendirmeleri

### A/B Test Altyapısı

Dönüşüm optimizasyonu için A/B test desteği:
- **Variant Routing**: %50/%50 veya özel dağılım
- **CTA Test**: Farklı buton renkleri, metinleri
- **Hero Test**: Farklı görseller, başlıklar
- **Pricing Test**: Farklı fiyat noktaları
- **Analytics**: Tüm varyantlar için dönüşüm takibi

### Performans Metrikleri

Landing page performans hedefleri:
- **LCP (Largest Contentful Paint)**: <2.5s
- **FID (First Input Delay)**: <100ms
- **CLS (Cumulative Layout Shift)**: <0.1
- **TTFB (Time to First Byte)**: <200ms
- **Lighthouse Score**: >95

### Responsive Tasarım

Mobil öncelikli responsive tasarım:
- **Mobile First**: 320px'den başlayarak yukarı
- **Breakpoints**: sm(640), md(768), lg(1024), xl(1280), 2xl(1536)
- **Touch Friendly**: Minimum 44x44px dokunma alanı
- **Fluid Typography**: clamp() ile akışkan yazı boyutu
- **Lazy Loading**: viewport dışı içerikler için lazy loading

## Bağımlılıklar

| Katman/Bileşen | Bağımlılık | Açıklama |
|----------------|-----------|----------|
| K8 | Payment Service | Ödeme entegrasyonu |
| K8 | User Service | Kayıt, giriş |
| K5 | Veri Yönetimi | Analytics verisi |
| K7 | Rate Limit | API koruması |
| K10 | Theme Engine | Tema özelleştirme |
| K0 | CDN | Statik varlık dağıtımı |
| K6 | Network | API çağrıları |

## Durum: Implementasyon

**Durum**: 🟡 Planlama Aşamasında
**Öncelik**: Yüksek (Pazarlama kritik)
**Kapsam**: Hero, Features, Pricing, Testimonials, Demo, CTA, Footer
**Test Kapsamı**: Unit test, Visual regression test, A/B test, Lighthouse audit
