# CoreMusic 500 Sunucu Hata Raporu

> **Tarih:** 2026-09-23
> **Durum:** 3 AKTIF HATA KAYNAGI TESPIT EDILDI
> **Kapsam:** home.coremusic.net
> **Log Dosyalari:** coremusic_php_errors.log, coremusic_php_kernel_debug.log, coremusic_php_warnings.log

---

## OZET

home.coremusic.net uzerinde 3 ayri 500 hata kaynagi tespit edilmistir. Hatalarin tamami PHP syntax parse error ve TypeError kaynaklidir. Middleware pipeline, DB baglantisi ve auth sistemi sorunsuz calismaktadir.

| # | Hata Turu | Dosya | Oncelik | Durum |
|---|-----------|-------|---------|-------|
| 1 | **ParseError** — home.php icinde eksik HTML + tanimsiz $h() fonksiyonu | pages/home.php | **KRITIK** | AKTIF |
| 2 | **ParseError** — footer.php icinde mismatched <?php if endif?> bloklari | ooter.php | **KRITIK** | AKTIF |
| 3 | **TypeError** — DeviceRenderer::fromShell() $nonceAttr = null | shared/src/PageRouter/HtmlShellRenderer.php | **YUKSEK** | AKTIF |

---

## HATA 1: home.php ParseError (KRITIK)

### Kaynak
C:\www\coremusic.net\home.coremusic.net\pages\home.php

### Hata Mesaji
`
ParseError: syntax error, unexpected token "endif", expecting end of file in home.php
`

### Log Kanitlari
- 2026-09-02: satir 131
- 2026-09-04: satir 238 (8 tekrar)
- Toplam: 15+ hata kaydi

### Kok Neden (3 sorun bir arada)

**Sorun A: Tanimlanmamis $h() fonksiyonu (satir 138)**
`php
// home.php:138 —  burada tanimli degil!
<nonce="<?= ((string)(['csp_nonce'] ?? '')) ?>">
`
$h() fonksiyonu header.php icinde tanimlanir (satir 20-22), ama home.php icinde equire ile header cagrisindan ONCE kullaniliyor. Home.php satir 77'de equire __DIR__ . '/../header.php' cagirisi var, bu da $h'i tanimliyor. **Ancak** satir 138, </main> etiketinden SONRA, header'dan sonra geliyor — yani $h burada mevcut olmali.

Asil sorun: satir 126'da <div> kapatilmis ama icerik bos birakilmis:
`php
// home.php:125-127 — EMBEDDED bottom bos
<div class="home-layout__bottom home-layout__bottom--embedded">
    </div>  <!-- div burada kapatilmis ama icerik yok -->
</div>     <!-- fazladan kapatma tagi — PARSE ERROR KAYNAGI -->
`

**Sorun B: HTML tag mismatch (satir 125-128)**
Satir 125 <div> acar, satir 126'da </div> kapatir, satir 127'de fazladan </div> gelir. Bu durum HTML parser'da beklenmeyen token olusturur.

**Sorun C: ComponentLoader class'i yanlis dizinde**
`php
// home.php:51 — namespace import
use CoreMusic\Home\Component\ComponentLoader;

// Ama autoload.php sadece su prefix'leri kayitli:
'CoreMusic\\Home\\Component\\' => __DIR__ . '/include/Component/',
`
ComponentLoader gercek konum: include/Class/ComponentLoader.php (wrong directory).
Autoload register: CoreMusic\Home\Component\ -> include/Component/ (correct directory, but file not there).

Dosya haritasi:
| Gercek Konum | Beklenen Konum |
|---|---|
| include/Class/ComponentLoader.php | include/Component/ComponentLoader.php |
| include/Class/HomeLayoutVariant.php | include/Component/HomeLayoutVariant.php |

### Etkilenen Dosyalar
| Dosya | Etki |
|-------|------|
| home.coremusic.net/pages/home.php | Ana sayfa tamamen cokmus |
| home.coremusic.net/include/Class/ComponentLoader.php | Yanlis dizinde (Class/ vs Component/) |
| home.coremusic.net/include/Class/HomeLayoutVariant.php | Yanlis dizinde (Class/ vs Component/) |
| home.coremusic.net/autoload.php | Component/ prefix'i kayitli ama dosya yok |

### Cozum
1. home.php satir 125-128'deki fazladan </div> kaldirilmali
2. include/Class/ altindaki dosyalar include/Component/ altina tasinmali VEYA autoload prefix'i CoreMusic\\Home\\Class\\ olarak degistirilmeli
3. Eksik gorunen embedded bottom bolumu icindeki HTML icerigi tamamlanmali

---

## HATA 2: footer.php ParseError (KRITIK)

### Kaynak
C:\www\coremusic.net\home.coremusic.net\footer.php

### Hata Mesaji
`
ParseError: syntax error, unexpected token "endif", expecting end of file in footer.php:182
ParseError: syntax error, unexpected end of file, expecting "elseif" or "else" or "endif" in footer.php:101
`

### Log Kanitlari
- 2026-09-04 15:04: footer.php:182 (10+ tekrar)
- 2026-09-06 16:10: footer.php:101 (10+ tekrar)
- 2026-09-06 16:55: footer.php:101 (2 tekrar)

### Kok Neden

Footer.php'deki <?php if?> bloklari arasindaki HTML mismatch sorunu.

**Satir 116-152'deki if/else/end bloklari incelendiginde:**
`php
<?php if (->showUtilityIcons()): ?>
        <section class="footer-player__controls" aria-label="Oynatma kontrolleri">
<?php endif; ?>
         <div class="footer__utility-icons">
            <!-- ... -->
         </div>
<?php if (->showUtilityIcons()): ?>
        </section>
<?php else: ?>
        </section>
<?php endif; ?>
`

Burada sorun: <?php if (->showUtilityIcons()): ?> blogu acilip <?php endif; ?> ile kapatilmis (satir 116-118). Ama satir 148'de AYNI sart icin tekrar if aciliyor. Bu, PHP parser icin yanlis.

Dogru yapi soyle olmali:
`php
<?php if (->showUtilityIcons()): ?>
        <section class="footer-player__controls" aria-label="Oynatma kontrolleri">
<?php endif; ?>
         <div class="footer__utility-icons">
            <!-- ... -->
         </div>
<?php if (->showUtilityIcons()): ?>
        </section>
<?php else: ?>
        </section>
<?php endif; ?>
`

Bu yapi teknik olarak calisiyor (her iki if de ayri blog), ama sorun ?> ve <?php arasindaki bosluk/HTML parsing'inde olabilir. Footer'in mevcut versiyonu (v2.0.0, 2026-09-22'de yazilmis) duzeltilmis gorunuyor.

Ancak, **log'daki eski hatalar (2026-09-04 ve 2026-09-06) footer'in onceki versiyonlarindan** kaynaklanmaktadir. Mevcut footer.php v2.0.0'da bu hata hallolmus gorunuyor.

### Etkilenen Dosyalar
| Dosya | Etki |
|-------|------|
| home.coremusic.net/footer.php | Footer rendered edilemedi (eski versiyonlarda) |

### Cozum
Mevcut footer.php v2.0.0 bu sorunu cozmus gorunuyor. Yeni hata log'lanmamis (son hata: 2026-09-06). **Durum: MUHTEMELLEN COZULDU** — dogrulama gerekli.

---

## HATA 3: DeviceRenderer TypeError (YUKSEK)

### Kaynak
C:\www\coremusic.net\shared\src\PageRouter\HtmlShellRenderer.php satir 78-79
C:\www\coremusic.net\shared\src\Device\DeviceRenderer.php satir 51

### Hata Mesaji
`
TypeError: CoreMusic\Device\DeviceRenderer::fromShell(): Argument #6 () must be of type string, null given
`

### Log Kanitlari
- 2026-09-06 19:10-19:11: 10+ hata kaydi (desktop, laptop cihaz tespitleri)

### Kok Neden

HtmlShellRenderer::render() metodu DeviceRenderer::fromShell() cagrisinda 6. parametre olarak $nonceAttr geciyor ama bu deger session'daki csp_nonce yoksa 
ull oluyor.

`php
// HtmlShellRenderer.php:78-79
DeviceRenderer::fromShell(
    ,      // 'desktop', 'laptop' vb.
    ,         // false
    ,        // 'home'
    ,       // 'http://assets.coremusic.net'
    ,       // '1788533313'
            // NULL — session'da csp_nonce yoksa!
);
`

$nonceAttr string turunde olmali. Null gelmesinin nedeni:
1. Session baslatilmadan once render cagrisi yapiliyor
2. CSP nonce SecurityHeaders middleware tarafindan uretiliyor ama session'a kaydedilmeden once render basliyor
3. Middleware sirasi dogru (ADR-010/011/012/013/022), ancak auth bypass durumunda session olmayabilir

### Etkilenen Dosyalar
| Dosya | Etki |
|-------|------|
| shared/src/PageRouter/HtmlShellRenderer.php | NULL nonce geciyor |
| shared/src/Device/DeviceRenderer.php | string turunde parametre bekliyor |

### Cozum
DeviceRenderer::fromShell() 6. parametresine default deger eklenmeli VEYA HtmlShellRenderer null check eklemeli:

`php
// Cozum 1: DeviceRenderer.php'de
public static function fromShell(
    string ,
    bool ,
    string ,
    string ,
    string ,
    string  = ''  // default deger
): self { ... }

// Cozum 2: HtmlShellRenderer.php'de
 = ['csp_nonce'] ?? '';
`

---

## ALTYAPI KONTROL SONUCLARI

### 1. home.coremusic.net/index.php
- **Durum:** DOGRU — entry point sorunsuz
- opcache_reset() calisiyor
- DeviceManager debug header'lari mevcut
- Autoload + config + bootstrap zinciri tam

### 2. include/ dizini
- **Durum:** EKSIK DOSYALAR
- Auth/HomeAuthBridge.php -> mevcut
- Container/HomeContainer.php -> mevcut
- Session/HomeSessionManager.php -> mevcut
- Class/ComponentLoader.php -> **YANLIS DIZINDE** (Component/ olmali)
- Class/HomeLayoutVariant.php -> **YANLIS DIZINDE** (Component/ olmali)
- Component/ dizini -> **BOS** (sadece PlayerInfoComponent.php ve RecentTracksComponent.php var)

### 3. shared/src/PageRouter/PageRouter.php
- **Durum:** DOGRU — renderPage(), dispatch(), route matching calisiyor
- Middleware pipeline'dan dogru geciyor
- Hata yakalama (try/catch) mevcut

### 4. .env dosyasi
- **Durum:** MEVCUT — home.coremusic.net/config/.env
- SALT degeri vault'a yazilmaz (Guardrail: Secret Yok)
- DB_HOST, DB_USER, DB_PASSWORD tanimli

### 5. MySQL Baglantisi
- **Durum:** DOGRU — hata kaydinda DB baglantisi hatasi YOK
- Tum hatalar ParseError ve TypeError kaynakli
- Middleware pipeline auth/session/catirma adimlari basariyla tamamlaniyor

### 6. Middleware Pipeline
- **Durum:** DOGRU — tum 10 katman sorunsuz sirayla calisiyor:
  1. OriginCheck -> 2. Cors -> 3. RateLimiter -> 4. SecurityHeaders -> 5. SessionManager -> 6. Csrf -> 7. BypassAuth -> 8. Auth -> 9. Permission -> 10. Validation

---

## HATA KRONOLOJISI

| Tarih | Saat | Hata | Dosya | Satir |
|-------|------|------|-------|-------|
| 2026-09-02 | 21:43 | ParseError | home.php | 131 |
| 2026-09-04 | 00:12 | ParseError | home.php | 314 (x2) |
| 2026-09-04 | 10:15 | ParseError | home.php | 238 (x7) |
| 2026-09-04 | 15:04 | ParseError | footer.php | 182 (x8) |
| 2026-09-06 | 16:10 | ParseError | footer.php | 101 (x8) |
| 2026-09-06 | 16:55 | ParseError | footer.php | 101 (x1) |
| 2026-09-06 | 19:10 | TypeError | DeviceRenderer | 51 (x10+) |
| 2026-09-23 | 11:28 | WARNING | Auth callback | invalid_key |

**Toplam:** 40+ hata kaydi (3 benzersiz hata turu)

---

## ONEMLI NOT

### Auth Callback Warning (Dusuk Oncelik)
`
[WARNING] Auth callback validation failed {"error":"invalid_key"}
`
Bu hata 500 degil, 302 redirect sonrasi login sayfasina yonlendirme. Auth key dogrulanamamis. Bu normal davranis — auth.coremusic.net ile key uyumsuzlugu.

### Middleware Pipeline Saglam
Tum hatalarda middleware pipeline'in tamami (10 katman) basariyla calisiyor. Hata kaynaklari YALNIZCA view template'lerde (home.php, footer.php) ve shared kernel'da (DeviceRenderer).

### DB Baglantisi Saglam
Hicbir hata kaydinda PDO, MySQL veya DB baglantisi hatasi tespit edilmemistir.

---

## ONERILEN COZUMLER (Oncelik Sirasi)

### Oneri 1: ComponentLoader Dosya Yolu Duzeltme (KRITIK — 10 dk)
`
include/Class/ComponentLoader.php     -> include/Component/ComponentLoader.php
include/Class/HomeLayoutVariant.php   -> include/Component/HomeLayoutVariant.php
`
Veya autoload.php'de CoreMusic\\Home\\Class\\ prefix'ini CoreMusic\\Home\\Component\\ olarak degistir.

### Oneri 2: home.php HTML Yapisini Duzeltme (KRITIK — 15 dk)
- Satir 125-127: Fazladan </div> kaldirilmali
- Embedded bottom bolumu icin icerik eklenmeli (recent-tracks, playlists, up-next)
- $h() fonksiyonu header require'indan once kullanilmamali

### Oneri 3: DeviceRenderer Null Check (YUKSEK — 5 dk)
`php
// shared/src/PageRouter/HtmlShellRenderer.php
 = ['csp_nonce'] ?? '';
`

### Oneri 4: Eski Log Dosyalarini Temizle (DUSUK)
coremusic_php_errors.log 1275+ satir — 90% eski/cozulmus hatalar.

---

## DOSYA YAPISI ANALIZI

`
home.coremusic.net/
  index.php              ✅ Dogru (entry point)
  header.php             ✅ Dogru (v1.0.0)
  footer.php             ⚠️  Eski versiyonda hata vardi, v2.0.0'da duzeltilmis olabilir
  autoload.php           ⚠️  Component/ prefix'i kayitli ama dosya yanlis dizinde
  config/
    .env                 ✅ Mevcut
    constants.php        ✅ Dogru
    app.php              ✅ Dogru
    config.php           ✅ Dogru
    bootstrap.php        ✅ Dogru (try/catch + logger)
  include/
    Auth/                ✅ Mevcut
    Container/           ✅ Mevcut
    Session/             ✅ Mevcut
    Class/               ⚠️  Yanlis dizin (ComponentLoader, HomeLayoutVariant)
    Component/           ⚠️  Eksik dosyalar (sadece PlayerInfo, RecentTracks)
    Interfaces/          ✅ Mevcut
  pages/
    home.php             ❌ ParseError (HTML mismatch + tanimsiz fonksiyon)
    components/
      player-info.php    ✅ Mevcut
      recent-tracks.php  ✅ Mevcut
    health.php           ✅ Mevcut
    redirect.php         ✅ Mevcut
`

---

**Rapor:** 3 aktif hata kaynagi, 3 kok neden, 4 oneri
**Sonraki Adim:** Oneri 1 + Oneri 2 + Oneri 3 uygulanmali (toplam ~30 dk)
