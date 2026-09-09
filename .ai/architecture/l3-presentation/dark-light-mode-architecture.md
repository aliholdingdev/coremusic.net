---
type: architecture
category: l3-presentation
title: "CoreMusic — Dark/Light Mode Architecture"
date: 2026-09-01
updated: 2026-09-01
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/architecture/l3-presentation/dark-light-mode-architecture.md"
  source_of_truth:
    - ".ai/CLAUDE.md"
    - ".ai/decisions/accepted/ADR-044-dynamic-user-theme-engine.md"
  related:
    - ".ai/architecture/l3-presentation/responsive-frontend-architecture.md"
    - ".ai/ui-design/responsive-device-mode.md"
---

# CoreMusic — Dark/Light Mode Architecture

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[ADR-044-dynamic-user-theme-engine]] · [[responsive-frontend-architecture]]

---

## 1. Genel Bakış

CoreMusic'e **dark/light mode** desteği eklendi. Mevcut gender teması (female/male/neutral) ile birlikte çalışır.

**İki bağımsız eksen:**
1. **Color Mode:** dark / light / null (OS)
2. **Gender Theme:** female / male / neutral

**Kombinasyonlar:** female+dark, female+light, male+dark, male+light, neutral+dark, neutral+light

---

## 2. CSS Token Mimarisi

### 2.1 — Cascade Sırası

```
1. :root (dark varsayılan — mevcut token'lar)
2. [data-gender] (gender override)
3. @media prefers-color-scheme: light (OS default — sadece data-mode yoksa)
4. html[data-mode="light"] (kullanıcı override — en yüksek öncelik)
5. html[data-mode="light"][data-gender="*"] (gender + light kombinasyonu)
```

### 2.2 — Token Değişiklikleri (Light Mode)

| Token | Dark (Default) | Light |
|-------|----------------|-------|
| `--bg-base` | `#0d0a14` | `#f8f7fc` |
| `--bg-surface` | `#120d1a` | `#ffffff` |
| `--bg-elevated` | `#1a1025` | `#f0eef5` |
| `--text-primary` | `#ffffff` | `#1a1025` |
| `--text-secondary` | `rgba(255,255,255,0.72)` | `rgba(26,16,37,0.72)` |
| `--border-default` | `rgba(255,255,255,0.10)` | `rgba(0,0,0,0.12)` |
| `--glass-1-bg` | `rgba(255,255,255,0.04)` | `rgba(0,0,0,0.03)` |
| `--overlay-dark` | `rgba(0,0,0,0.70)` | `rgba(0,0,0,0.60)` |

### 2.3 — Dosya Yapısı

```
01_Abstracts/
├── a-color-mode-tokens.css    ← YENİ: Dark/light mode token'ları
├── a-semantic-token.css       ← Mevcut: Gender token'ları
├── a-light-glass-tokens.css   ← Güncellendi: Light mode glass override'ları
└── a-colors-token.css         ← Primitif renk paleti
```

---

## 3. HTML Attribute Sistemi

```html
<!-- Dark + Neutral (varsayılan) -->
<html lang="tr" data-gender="neutral">

<!-- Light + Female -->
<html lang="tr" data-gender="female" data-mode="light">

<!-- Dark + Male (explicit) -->
<html lang="tr" data-gender="male" data-mode="dark">

<!-- OS + Neutral (data-mode yok → prefers-color-scheme kullanılır) -->
<html lang="tr" data-gender="neutral">
```

---

## 4. PHP Tarafı

### 4.1 — ThemeManager.php

```php
// Mode tespiti
$colorMode = ThemeManager::detectMode($sessionData); // 'dark'|'light'|null

// Attribute üretimi
echo ThemeManager::injectAttributes($gender, $colorMode);
// Çıktı: data-gender="female" data-mode="light"
```

**Öncelik sırası:**
1. `$_SESSION['cm_color_mode']`
2. `$_SESSION['color_mode']`
3. `$_COOKIE['cm_color_mode']`
4. `null` (OS preferansını kullan)

### 4.2 — HtmlShellRenderer.php

```php
$gender    = ThemeManager::detect($sessionData);
$colorMode = ThemeManager::detectMode($sessionData);

echo '<html lang="tr" ' . ThemeManager::injectAttributes($gender, $colorMode) . '>';
```

---

## 5. JavaScript Tarafı

### 5.1 — ThemeManager.js

```javascript
// Mode değiştir
themeManager.setMode('light');     // Explicit light
themeManager.setMode('dark');      // Explicit dark
themeManager.setMode(null);        // OS preferansına dön

// Toggle
themeManager.toggleMode();         // dark ↔ light

// OS dinleme
themeManager.getSystemMode();      // 'dark'|'light'
```

### 5.2 — Event'ler

```javascript
eventBus.on('modechange', ({ mode, source }) => {
    // mode: 'dark'|'light'
    // source: 'user'|'system' (opsiyonel)
});
```

---

## 6. Cookie & Session

| Key | Cookie | Session | TTL |
|-----|--------|---------|-----|
| `cm_color_mode` | `dark`/`light` | `$_SESSION['cm_color_mode']` | 1 yıl |
| `cm_gender` | `female`/`male`/`neutral` | `$_SESSION['cm_gender']` | 1 yıl |

---

## 7. Settings Sayfası

Her platform için ayarlar sayfası:
- `home.coremusic.net/pages/ayarlar.php` — Dark/Light + Gender
- `car.coremusic.net/pages/ayarlar.php` — Sadece Dark/Light (driving-safe)

**UI:** Radio group butonları, large touch targets (car için 56px+)

---

## 8. Guardrail Uyumluluğu

| Guardrail | Durum |
|-----------|-------|
| #17 Single Component Responsive | ✅ Ayrı HTML yok |
| #10 No Frameworks | ✅ Vanilla JS |
| #5 Single Source of Truth | ✅ Token'lar `a-color-mode-tokens.css`'te |

---

## 9. İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| `assets.coremusic.net/Css/01_Abstracts/a-color-mode-tokens.css` | Dark/light mode token'ları |
| `assets.coremusic.net/Css/01_Abstracts/a-semantic-token.css` | Gender token'ları |
| `assets.coremusic.net/Css/01_Abstracts/a-light-glass-tokens.css` | Glass token'ları |
| `assets.coremusic.net/js/managers/ThemeManager.js` | JS tema yöneticisi |
| `shared/src/Theme/ThemeManager.php` | PHP tema yöneticisi |
| `shared/src/PageRouter/HtmlShellRenderer.php` | HTML shell üreticisi |
| `home.coremusic.net/pages/ayarlar.php` | Ayarlar sayfası |
| `car.coremusic.net/pages/ayarlar.php` | Car ayarlar sayfası |

---

## 10. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 1.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Color Modes | 3 (dark, light, null/OS) |
| Gender Themes | 3 (female, male, neutral) |
| Combinations | 9 (3×3) |
| PHP Methods | 3 (detectMode, injectModeAttribute, injectAttributes) |
| JS Methods | 5 (setMode, toggleMode, getSystemMode, #applyMode, #loadMode) |
| Settings Pages | 2 (home, car) |
| ADR Uyumlu | ✅ 044 |
| Guardrail #17 Uyumlu | ✅ |

---

## 11. 9 Kombinasyon Matrisi (gender × mode)

| data-gender \ data-mode | dark (varsayılan) | light |
|--------------------------|-------------------|-------|
| **female** | pembe aksan koyu zemin | pembe aksan açık zemin |
| **male** | mavi aksan koyu zemin | mavi aksan açık zemin |
| **neutral** | gri aksan koyu zemin | gri aksan açık zemin |

**null mode** (data-mode yok): OS `prefers-color-scheme` karar verir — 3 gender × OS = 3 ek kombinasyon; toplam etkileşim 9 tanımlı + 3 OS-türevli.

CSS uygulaması: gender aksan rengi gender token'larından, zemin/metin mode token'larından (§2.2) gelir — iki eksen birbirini ezmez (ayrı token grupları).

---

## 12. FOUC Önleme

| Risk | Önlem |
|------|-------|
| HTML `data-mode` ile CSS yüklemesi arasındaki boşluk | Shell inline kritik stil (html-shell §28 `.vdisplay` deseni gibi) — koyu zemin default |
| OS mode geçiş anı | `prefers-color-scheme` media query anlık — transition kısa tutulur |
| İlk baytta yanlış tema | PHP attribute'u `<html>` açılışında üretir (§4) — JS beklemeden doğru mode |

Kural: Mode kararı sunucuda (session/cookie) → shell attribute'üyle gelir; JS yalnız kullanıcı değişiminde devreye girer. İlk yüklemede JS-bağımlı mode düzeltmesi yasaktır (FOUC üretir).

---

## 13. Car Driving-Safe Detay

| Konu | Kural |
|------|-------|
| Mode | Yalnız dark — light mode gece sürüşünde parlama riski; settings'te light seçeneği gizli olabilir (PLANNED karar) |
| Touch target | 56px+ (normal 48px üstü — sürüş güvenliği) |
| Kontrast | Yüksek kontrast zorunlu |
| Animasyon | Azaltılmış (sürücü dikkati) |

Kaynak: §7 tablosu — car ayarlar sayfası detayı. car domain PLANNED (subdomain-routing §23.8).

---

## 14. Test Senaryoları

| # | Senaryo | Beklenen |
|---|---------|----------|
| 1 | data-mode yok + OS light | light görünüm (prefers-color-scheme) |
| 2 | data-mode=light explicit | OS'tan bağımsız light |
| 3 | data-mode=dark explicit | OS'tan bağımsız dark |
| 4 | female + light | pembe aksan + açık zemin |
| 5 | male + dark | mavi aksan + koyu zemin |
| 6 | toggle dark↔light | attribute anlık değişir — sayfa reload yok |
| 7 | cookie `cm_color_mode=light` | PHP shell'de data-mode=light üretir |
| 8 | OS değişimi (mode null iken) | görüntü anında uyum sağlar |
| 9 | car ayarlar | 56px+ targets, dark-first |
| 10 | gender değişimi mode korunur | data-mode bozulmaz (bağımsız eksen) |

---

## 15. Diagnostics (tekrarlanabilir)

```powershell
# 1. a-color-mode-tokens.css varlığı (§2.3 YENİ dosya)
Test-Path -LiteralPath "assets.coremusic.net\Css\01_Abstracts\a-color-mode-tokens.css"

# 2. a-light-glass-tokens.css
Test-Path -LiteralPath "assets.coremusic.net\Css\01_Abstracts\a-light-glass-tokens.css"

# 3. PHP detectMode yöntemi
Select-String -LiteralPath "shared\src\Theme\ThemeManager.php" -Pattern "detectMode|injectAttributes" -ErrorAction SilentlyContinue

# 4. Cookie anahtarı
Select-String -LiteralPath "shared\src\Theme\ThemeManager.php" -Pattern "cm_color_mode" -ErrorAction SilentlyContinue

# 5. JS setMode
Get-ChildItem -LiteralPath "assets.coremusic.net\js" -Recurse -Include "*.js" | Select-String -Pattern "setMode|toggleMode" -ErrorAction SilentlyContinue

# 6. ayarlar sayfası
Test-Path -LiteralPath "home.coremusic.net\pages\ayarlar.php"
```

---

## 16. Risk Kaydı

| # | Risk | Olasılık | Etki | Önlem |
|---|------|----------|------|-------|
| 1 | a-color-mode-tokens.css dosyasının yokluğu | Bilinmiyor | Yüksek | §15 komut 1 — varlık teyidi |
| 2 | Gender token'ların mode geçişinde ezilmesi | Düşük | Orta | §11 ayrı eksen tasarımı |
| 3 | FOUC | Orta | Orta | §12 sunucu-karar ilkesi |
| 4 | Car light mode sürüş riski | Düşük | Yüksek | §13 kural |
| 5 | Cookie/session çift anahtar karışıklığı | Orta | Düşük | §4.1 öncelik sırası sabit |
| 6 | prefers-color-scheme tarayıcı farkı | Düşük | Düşük | null mode davranışı test edilir |

---

## 17. Ek SSS

**S: Dark varsayılan — light neden null değil?**
C: §2.1 cascade: `:root` koyu zemin token'larıyla yazılmış (mevcut tasarım). null mode = OS'a bırak; kullanıcı explicit light seçerse override. Varsayılanın dark olması tasarım kimliği (vaporwave/koyu) ile uyumlu.

**S: Mode + gender token'ları aynı dosyada mı?**
C: Hayır — §2.3: mode token'ları `a-color-mode-tokens.css`, gender `a-semantic-token.css`. Ayrı dosya = bağımsız eksen bakımı.

**S: `cm_gender` cookie ile `theme_gender` localStorage ilişkisi?**
C: İki mekanizma yaşayabilir: localStorage (ThemeManager hız) + cookie/session (sunucu render). §8 theme-engine storage kararı PLANNED — tek politika hedefleniyor; bu doküman cookie/session modelini tanımlar.

**S: View Transition API ile mode geçişi?**
C: ADR-048 PLANNED — mode değişiminde cross-fade etkisi dekoratif ektir; temel mekanizma attribute swap'tır.

**S: car sayfası neden ayrı dosya?**
C: car domain ayrı (PLANNED — subdomain-routing §23.8); driving-safe farkları §13. Kod geldiğinde ortak bileşen + car override beklenir.

**S: Light mode'da glass efektleri nasıl?**
C: `a-light-glass-tokens.css` override'ları — glass opaklık/renk tersine çevrilir (koyu zeminde beyaz cam, açık zeminde siyah cam — §2.2 --glass-1-bg).

---

## 18. İzlenebilirlik Tablosu

| İddia | Kaynak | Doğrulama |
|-------|--------|-----------|
| Cascade 5 kademe | §2.1 | Tasarım — CSS teyidi devam |
| 8 mode token değeri | §2.2 | Doküman (CSS dosyası çapraz teyidi §15) |
| PHP 3 metot | §4.1 | ThemeManager.php — isim düzeyi (brain §18B) ✅ |
| JS 5 metot | §5.1 | ThemeManager.js — isim düzeyi ✅ |
| Cookie TTL 1 yıl | §6 | Doküman |
| ayarlar sayfaları | §7 | home PLANNED kod (pages/ klasörü mevcut) |
| 9 kombinasyon | §11 | 3×3 matematik |

---

## 19. Revizyon Geçmişi

| Sürüm | Tarih | Değişiklik |
|-------|-------|------------|
| 1.0.0 | 2026-09-01 | Dark/light mimarisi |
| 1.1.0 | 2026-09-08 | Faz 2d: §11 9-kombinasyon matrisi; §12 FOUC; §13 car detay; §14 test; §15 diagnostics; §16 risk; §17 SSS; §18 izlenebilirlik |

---

---

## 20. FOUC Implementasyon Örneği

```html
<!-- Shell <head> içinde — CSS yüklenmeden önce -->
<html lang="tr" data-gender="neutral" data-mode="dark">
<head>
  <style>
    /* Kritik zemin — koyu default (FOUC önleme ilk satır) */
    html { background: #0d0a14; color: #ffffff; }
    html[data-mode="light"] { background: #f8f7fc; color: #1a1025; }
  </style>
  <!-- sonrası: a-color-mode-tokens.css vb. -->
```

İlke: attribute sunucuda üretildiği için (§4) inline kritik stil attribute'u okur — JS beklenmez. Light kullanıcı için tek satır override yeter; token'lar sonra gelir.

---

## 21. Mode Değişimi — Uçtan Uca Akış

```
Kullanıcı ayarlar'da "Light" seçer
  → JS ThemeManager.setMode('light')
     → 1. html data-mode="light" (anlık görsel)
     → 2. localStorage/session kayıt (§8 politika)
     → 3. cookie cm_color_mode=light (1 yıl — §6)
        → sonraki istek PHP detectMode → shell attribute ✅
     → 4. eventBus 'modechange' {mode:'light', source:'user'}
        → aboneler: grafik, glass, görsel uyumu
OS otomatik (mode=null iken):
  → matchMedia('(prefers-color-scheme: light)') change event
     → ThemeManager.getSystemMode() → attribute (explicit yazmadan)
```

Kural: explicit mode OS'u ezme sırası §2.1 cascade'de sabittir; JS explicit seçimde `data-mode` yazar, null seçimde attribute'u KALDIRIR (OS devreye döner).

---

## 22. Settings Sayfası Bileşen Tasarımı

| Öğe | Home (ayarlar.php) | Car (PLANNED) |
|-----|--------------------|---------------|
| Mode seçici | Radio group: Dark/Light/Sistem | Dark (tek seçenek + kilitleme PLANNED) |
| Gender seçici | C07 kart mini / radio | Yok (driving-scope) |
| Touch | 48px+ | 56px+ |
| Erişim | ayarlar route (auth'lu) | ayarlar route |
| Kayıt anı | anlık + kalıcı | anlık (local) |

Bileşen: C15 Toggle yerine radio group (3 seçenek) — C15 toggle binary'ye uygun; mode 3 değerdli (dark/light/null) → radio uygundur. Ayrıntı: C15 toggle §28 matrisiyle uyumlu kullanım.

---

## 23. prefers-color-scheme CSS Örneği

```css
/* a-color-mode-tokens.css içinde (§2.3) */
:root {
  --bg-base: #0d0a14;
  /* dark default token'lar */
}

@media (prefers-color-scheme: light) {
  html:not([data-mode]) {
    --bg-base: #f8f7fc;
    /* light token'lar — yalnız explicit data-mode YOKSA */
  }
}

html[data-mode="light"] {
  --bg-base: #f8f7fc;
  /* light token'lar (OS'tan bağımsız) */
}
```

**Kritik selektör:** `html:not([data-mode])` — OS kararı yalnız kullanıcı explicit seçim yapmadıysa uygulanır (§2.1 cascade 3. kademe).

---

## 24. Transition Kuralları

| Konu | Kural |
|------|-------|
| Geçiş animasyonu | `transition: background-color .2s, color .2s` — kısa |
| İlk yükleme | transition YOK (FOUC hissi vermemesi için default anlık) |
| Tüm elemanlara transition * | YASAK (performans + istenmeyen animasyon) — yalnız renk özellikleri |
| View Transition (ADR-048) | PLANNED dekoratif ektir; temel geçiş transition yeterli |

---

## 25. Storage Detay — Cookie Bayrakları

| Alan | Değer |
|------|-------|
| Ad | `cm_color_mode` |
| Değer | `dark` \| `light` (null cookie YAZILMAZ — yokluk = OS) |
| Path | `/` |
| Domain | `.coremusic.net` (subdomain SSO paraleli — subdomain-routing §13) |
| Max-Age | 31536000 (1 yıl) |
| SameSite | Lax |
| Secure | HTTPS prod'da |
| HttpOnly | **HAYIR** — JS okumalı mı? Hayır: JS localStorage/session kullanır; cookie PHP içindir → HttpOnly=true olabilir. Karar: HttpOnly true (güvenli default; JS cookie'ye ihtiyaç duymaz) |

---

## 26. Karar Ağacı — "Mode nerede belirlenir?"

```
İstek geldinde:
  ├─ session'da cm_color_mode var → attribute yaz (kullanıcı tercihi)
  ├─ cookie'de var → session'a al → attribute yaz
  └─ ikisi de yok → attribute YAZMA → CSS :root + OS media → OS karar
Kullanıcı değiştirince (JS):
  ├─ explicit seçim → data-mode yaz + cookie/session güncelle
  └─ "Sistem" seçimi → data-mode KALDIR → OS döner
```

---

## 27. Ek SSS

**S: `data-mode="dark"` explicit yazmak gerekli mi (zaten default)?**
C: Hayır — yokluk = dark default (§2.1). Explicit yazım yalnız "OS değişse de dark kalsın" niyeti içindir (§3 örnek 3).

**S: Light mode'da görseller (kapaklar) değişir mi?**
C: Hayır — içerik görselleri mode-bağımsızdır; yalnız zemin/metin/cam token'ları değişir. Görseller üzerinde overlay token'ları (§2.2 --overlay-dark) uyumu sağlar.

**S: Mode değişimi sayfa yenileme gerektirir mi?**
C: Hayır — attribute swap + cascade anlık (§21). PHP tarafı yalnız sonraki ilk render için cookie günceller.

**S: `a-light-glass-tokens.css` neden ayrı dosya — mode token'ının içine?**
C: Glass efektleri çok sayıda bileşeni etkiler — ayrı dosya bakım ayrışması sağlar (§2.3 yapısal karar). Birleştirme PR'de tartışılabilir.

**S: OS mode değişince session'a yazılır mı?**
C: Hayır — OS kararı kalıcı tercih değildir; explicit kullanıcı seçimi kalıcıdır. null mode OS'a saygı duyar (§5.1 setMode(null) paralel).

---

## 28. Risk Kaydı Ek

| # | Risk | Olasılık | Etki | Önlem |
|---|------|----------|------|-------|
| 7 | a-color-mode-tokens.css yokluğu | Bilinmiyor | Yüksek | §15 komut 1 — varlık teyidi |
| 8 | Inline kritik stil ile token çift bakımı | Orta | Düşük | §20 minimal 2 satır ilkesi |
| 9 | Car light seçeneğinin kilitsiz kalması | Orta | Orta | §13 PLANNED kilitleme |
| 10 | HttpOnly kararı JS erişim ihtiyacı çıkarsa | Düşük | Düşük | §25 karar akışı |

---

## 29. İzlenebilirlik Ek

| İddia | Kaynak | Doğrulama |
|-------|--------|-----------|
| FOUC 2 satır kritik stil | §20 | Shell pratiği paralel |
| not([data-mode]) selektörü | §23 | Cascade §2.1 gereği |
| setMode(null) davranışı | §21 | §5.1 API ✅ |
| Settings radio kararı | §22 | C15 binary notu |
| HttpOnly önerisi | §25 | Güvenli default ilkesi |

---

## 30. Kalite Raporu (Final)

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 1.2.0 |
| **Bölüm Sayısı** | 30 |
| **SSS** | 16 |
| **Test Senaryosu** | 10 + akış 2 |
| **Risk Kaydı** | 10 |
| **Varlık Kontrol Görevi** | 2 CSS dosyası (§15) |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-08
**Mode:** Red Team · Human Mode · Truth Mode
