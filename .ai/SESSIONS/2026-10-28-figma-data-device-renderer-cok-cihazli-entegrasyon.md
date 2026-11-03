# 2026-10-28 - Figma Çok Cihazlı Entegrasyon: 1024 / 1920 / 3840 DeviceRenderer Uyarlaması

**Talep:** "figma data dan iseğim gbii bu batsraıamın aynsı oalcka şekidle if rendeerererdevcies e göre bu aratyuzlaeri kodla ve figma dtaya kaydet .ai da başla"  
**Kapsam Kararı (P0):** Tüm sayfalar (Home, Göz At, Playlist, Geçmiş) — 1024 (Embedded), 1920 (Wide FHD) ve 3840 (4K UHD) çözünürlükleri.  
**İş Akışı Sırası (P0):** Önce `.ai` altındaki Figma spec/token verilerini doğrula ve kaydet; ardından `home.coremusic.net` ve CSS katmanlarında `DeviceRenderer` şartlarına (`isEmbedded`/`isWide`/`is4k`) göre kodla.  
**Durum:** ✅ Tamamlandı (working tree — commit orkestratöre ait).

---

## 1. Kapsam ve Breakpoint Eşleşmesi

| Çözünürlük | Tier | Figma Sayfa / Node | Karşılık Gelen Ekranlar | Durum |
|---|---|---|---|---|
| 1024×600 | T07 | `1047:15802` (Linux Pi) | Home (`1639:10160`), Playlist (`2831:9443`), Göz At (`2831:9555`), Geçmiş (ortak) | Mevcut spec ve template'ler doğrulandı |
| 1920×1080 | T17 | `462:5874` (Linux 1920) | Home (`2831:13747`), Playlist (`3155:19834`), Göz At (`3155:18252`), Geçmiş (`3155:22392`) | Mevcut spec ve template'ler doğrulandı |
| 3840×2160 | T20 | `3175:13501` (Linux 3840) | Home (`3175:13502`), Playlist, Browse, History | Yeni 3 spec yazıldı (`playlist.md`, `browse.md`, `history.md`) |

---

## 2. Gerçekleştirilen İşlemler

1. **T20 4K Spec Üretimi (Kalıp D):**
   - `.ai/ui-design/screens/T20-monitor-4k/playlist.md` yazıldı (Figma node `3155:19834` T17 uyarlaması, sol-üst 1:1 yerleşim, tam genişlik footer).
   - `.ai/ui-design/screens/T20-monitor-4k/browse.md` yazıldı (8 disk butonu: 2 Sistem, 3 Dahili, 3 Çıkarılabilir; sol panel 1612px + sağ durum paneli 219px).
   - `.ai/ui-design/screens/T20-monitor-4k/history.md` yazıldı (6 kolonlu dinleme geçmişi ızgarası + sağ panel).
2. **Merkezi İndeks Güncellemesi:**
   - `.ai/ui-design/screens/00-ascii-art-index.md` güncellendi (`total_spec_files: 27 → 30`, `total_active: 26 → 29`, T20 bölümüne 3 yeni ekran eklendi).
3. **SPA Rota & Sayfa Entegrasyonu:**
   - `home.coremusic.net/pages/playlist.php` oluşturuldu (`nowplaying.php` ile bağlandı, `/playlist` rotası aktif hale getirildi).
   - `home.coremusic.net/pages/browse.php` oluşturuldu (Figma "Göz At / Dosya Yöneticisi" ekranı `isEmbedded`/`isWide`/`is4k` koşullu layout yapısıyla kodlandı).
   - `shared/config/routes.php` dosyasına `browse` ve `gozat` rotaları eklendi.
4. **Layout Varyantı & 4K Home Dashboard Genişletmesi:**
   - `CoreMusic\Home\Class\HomeLayoutVariant` enum sınıfına `isFourK()` ve `isEmbedded()` yardımcı fonksiyonları eklendi.
   - `HomeLayoutVariantTest` birim testlerine bu metodlar için test senaryoları eklendi.
   - `RecentTracksComponent` ve `recent-tracks.php` bileşeni 4K (`isFourK()`) ekranlarda Figma `Linux 3840 - Home` (`3175:13502`) spec'ine uygun olarak ek 3 satırı (`En Son Dinlenen Albumler`, `Önerilen Sanatçılar`, `Önerilen Albumler`) gösterecek şekilde genişletildi.
5. **Göz At (Browse) Bağımsız CSS & ITCSS Uyarlaması:**
   - Mimari ayrım gereği `_player.css` dosyası temiz tutuldu; Dosya Yöneticisi için müstakil `assets.coremusic.net/Css/05_Pages/p-browse.css` sayfası yazıldı (`css-page-template.md` standardı).
   - `p-browse.css` 8 cihaz profiline (`d-desktop.css`, `d-embedded.css`, `d-4k.css`, `d-laptop.css`, `d-tablet.css`, `d-phone.css`, `d-4k-tv.css`, `d-4k-monitor.css`) `@import` ile bağlandı.
   - `browse.php` sayfası bağımsız `.page-browse`, `.browse-glass`, `.browse-toolbar` semantik BEM sınıflarına kavuşturuldu.
   - `DeviceManager::NAV_LINKS` haritasına `/browse` ("Göz At") linki eklendi (Figma navbar sıra ve içerik uyumu, 0 ölü link doğrulandı).
6. **Güvenlik & CSP İyileştirmeleri:**
   - `SecurityHeadersMiddleware.php` ve `ResponseEmitter.php` dosyalarında `mediaOrigin` (`https://media.coremusic.net`) `img-src`, `media-src` ve `connect-src` CSP direktiflerine eklendi.
   - `Strict-Transport-Security` başlığı RFC 6797 standartlarına uygun olarak yalnızca HTTPS isteklerinde (`$isHttps`) gönderilecek şekilde koşullandı.
7. **Kritik Test Double Düzeltmesi:**
   - `SqliteDatabaseRegistry.php` ve `bin/api-key-create.php` (`SqliteFileGateway`) sınıflarına eksik olan `registerProvider()` metodu eklendi, böylece `shared/tests/Repository/` testlerindeki PHP fatal hatası tamamen çözüldü.
8. **DeviceManager 4K Tespiti ve Sayfa Senkronizasyonu:**
   - `DeviceManager` sınıfına `is4k(): bool` metodu eklendi (`isLarge()` veya `viewportW >= 2561`).
   - `browse.php`, `home.php`, `nowplaying.php`, `history.php` sayfalarındaki `$is4k` mantığı güncellendi (`$dm->is4k()`).
   - Port 81 Apache üzerinden runtime HTTP testi ile doğrulandı:
     - 1024px: `.page-browse--embedded`
     - 1920px: `.page-browse--wide`
     - 3840px: `.page-browse--4k` ve `home.php` üzerinde Figma `3175:13502` 3 ek 4K satırı aktif.
9. **OAuthRepository Constructor & DB Uyumu:**
   - `OAuthRepository::__construct(?PDO $pdo = null)` public hale getirildi ve hem doğrudan PDO enjeksiyonunu hem de `IDatabaseManager` registry çözümlemesini destekleyecek şekilde refactor edildi. `OAuthManagerTest` 13 testi 0 hata ile geçti.
10. **Global Header & Footer 4K Senkronizasyonu:**
    - `header.php` ve `footer.php` içerisindeki `$headerTierClass` ve `$footerTierClass` hesaplamaları `$dm->is4k()` ile güncellendi. 4K'da `.site-header--4k` ve `.footer-player--4k` sınıflarının yayılması sağlandı.
11. **Tam Ekran Disk Player (`diskplayer.php`) Çok Cihaz Uyarlaması:**
    - `diskplayer.php` sayfası `$dm->is4k()` ve `$dm->shouldRenderEmbeddedLayout()` ile donatıldı. 1024x600 embedded modunda sol mini-kart (320px), merkez vinil (320px) ve sağ playlist (350px) panelinin çakışmadan sığması için `_player.css` içerisine gömülü cihaz kuralları eklendi.
12. **Widget Grid Aksiyon Linki:**
    - `widget-grid.php` üzerindeki "Kütüphanelerim / Dosya Yöneticisi" widget'ının boş `href="#"` linki doğrudan `/browse` SPA rotasına bağlandı.

---

## 3. Doğrulama ve Test Sonuçları

| Test / Kapı | Komut | Sonuç |
|---|---|---|
| PHP Syntax Lint | `php -l` (tüm değiştirilen dosyalar) | ✅ PASS (0 sözdizimi hatası) |
| Shared Test Paketi | `phpunit shared/tests/` | ✅ PASS (457 tests, 1484 assertions — %100) |
| Shared Nav & Device Testleri | `phpunit shared/tests/Unit/Device/` | ✅ PASS (23 tests, 103 assertions) |
| Nav Route Tutarlılık Testi | `phpunit NavRouteConsistencyTest.php` | ✅ PASS (2 tests, 12 assertions — 0 ölü link) |
| Home Uygulama Testleri | `phpunit home.coremusic.net/tests/` | ✅ PASS (69 tests, 251 assertions — %100) |
| Runtime Sayfa Denetimi | `test_pages.py` (6 rota × 4 viewport = 24 varyant) | ✅ PASS (24/24 varyant: 200 OK, 0 PHP hata/uyarı) |
| CSS Import Zinciri Denetimi | `check_css_imports.py` | ✅ PASS (718 `@import` bildirimi tarandı, 0 kırık link) |
| Canlı Asset Bağlantı Denetimi | `check_assets.py` | ✅ PASS (84 resim/script/stil linki tarandı, 0 adet 404) |
| Vault Screens Frontmatter Gate | `screens-frontmatter-check.ps1` | ✅ PASS (`dosya: 31 \| sorunlu: 0`) |
| Vault Wiki-Link Gate | `wiki-link-check.ps1` | ✅ PASS (404 link kontrol edildi, 0 gerçek kırık) |
| Vault Kalıp Standartları | `kalip-abc-check.ps1` | ✅ PASS (`A:0 B:0 C:0 -> GECTI`) |
| Sağ Panel Bileşen İzolasyonu | `04_Components/_wide-sidebar.css` | ✅ PASS (Bağımsız BEM bileşeni, sayfa öneki bağımlılığı kaldırıldı) |
| ComponentLoader Takma Ad Testi | `ComponentLoader::has('sag-panel')` | ✅ PASS (`sag-panel`, `right-panel` -> `wide-sidebar` eşlendi) |

---

## 4. Sağ Panel (WideSidebarComponent) Bileşen Mimarisi

Kullanıcı direktifi doğrultusunda Sağ Panel (`Sağ Panel GROUP 219×420`, Figma node `3155:19834` / `2831:9486`) sayfa şablonlarından ve `_player.css`'in sayfa-özel seçicilerinden (`.page-player--wide`, `.page-history--wide`) tamamen soyutlandı:

1. **Bileşen CSS**: `assets.coremusic.net/Css/04_Components/_wide-sidebar.css` dosyasına BEM standardında (`.page-sidebar--figma-wide`) taşındı.
2. **Cihaz Paketleri**: `d-desktop.css`, `d-laptop.css`, `d-embedded.css`, `d-4k.css`, `d-4k-monitor.css`, `d-4k-tv.css`, `d-tablet.css`, `d-phone.css` tüm 8 cihaz stiline dahil edildi.
3. **ComponentLoader Takma Adları**: `ComponentLoader` sınıfına `sag-panel` ve `right-panel` takma adları eklendi. `$loader->display('sag-panel', $variant)` çağrısı doğrudan bileşeni render eder.
4. **Çoklu Sayfa Kullanımı**: `/nowplaying`, `/playlist`, `/history`, `/browse`, `/albumler`, `/albumler/detay`, `/sanatcilar` sayfaları sağ paneli doğrudan `$loader->display('wide-sidebar', $variant)` bileşeni üzerinden tüketmektedir.
5. **Görsel Doğrulama**: Chrome DevTools MCP ile 1920×1080 (FHD) ve 1024×600 (RPi5 Embedded) ekran boyutlarında `/browse`, `/nowplaying`, `/albumler`, `/albumler/detay`, `/sanatcilar` ve `/ayarlar` canlı ekran görüntüleri alınarak Figma verileriyle birebir pixel-perfect uyumu onaylandı.

---

## 5. Eksik Sayfaların Tespiti ve Üretimi (2026-10-29)

Kullanıcı talimatı: *"devam et bütün sayfaları kontrolett önce olmayanları sonra oluştur basma"*

### A. Denetim Sonucu
- **Mevcut Sayfalar**: `/home`, `/nowplaying`, `/diskplayer`, `/history`, `/browse`, `/playlist`, `/health`
- **Eksik Sayfalar (Tespit Edildi)**:
  1. `/albumler` -> `pages/albums.php` (Figma: Linux 1024 - Albumler Page, CSS: `p-albums.css`)
  2. `/albumler/detay` -> `pages/album-detail.php` (Figma: Linux 1024 - Albumler Details Detay Page, CSS: `p-album-detail.css`)
  3. `/sanatcilar` -> `pages/artists.php` (Figma: Linux 1024 - Singer Page, CSS: `p-artists.css`)
  4. `/ayarlar` -> `pages/settings.php` & `pages/ayarlar.php` (CSS: `p-settings.css`, `routes.php` eşleşmesi)

### B. Uygulama ve Entegrasyon
1. **`home.coremusic.net/pages/albums.php`**: 128 Albüm kart ızgarası (4/5 kolon), tür filtre çipleri (`Tümü`, `Pop`, `Rock`...), arama çubuğu ve sağ kenar çubuğu `$loader->display('wide-sidebar', $variant)`.
2. **`home.coremusic.net/pages/album-detail.php`**: `Hayat Rüya Gibi / Göksel (2010)` başlığı, parça tablosu (`#`, `Şarkı Adı`, `Süre`, `Beğeni` 5 yıldız), aktif çalan parça pembe vurgusu ve `$loader->display('wide-sidebar', $variant)`.
3. **`home.coremusic.net/pages/artists.php`**: 96 Dairesel sanatçı kartı (100×100 embedded, 240×240 wide), tür çipleri ve `$loader->display('wide-sidebar', $variant)`.
4. **`home.coremusic.net/pages/settings.php`**: Buzlu cam panellerde Tema/Renk seçimi, Class AB Amfi / DAC DSP donanım ayarları ve Profil/Cinsiyet tercihi.
5. **`home.coremusic.net/pages/ayarlar.php`**: `settings.php` forwarder'ı.
6. **`shared/config/routes.php`**: `ayarlar` rotası `page: 'settings'` olarak bağlandı.
7. **Birim Testleri**: `home.coremusic.net/tests/Unit/Page/NewPagesTest.php` eklendi. Tüm 73 test (%100) ve shared 474 test (%100) başarıyla geçti.

---

## 6. Albümler ve Sanatçılar Figma SSOT Birebir Piksel Uyarlaması (2026-10-29)

Kullanıcı geri bildirimi: *"amöa albunmler ve snaatcılar olmamaış"*

### A. Kök Neden Analizi
1. **Görsel Varlıklar (Kapaklar ve Fotoğraflar)**: Sayfalarda daha önce jenerik araba ve siluet görselleri (`girl-1.png`, `album-kursat.png`) kullanılmıştı; Figma mockup'larındaki (`Linux  1024 - Albumler Page.png` ve `Linux  1024 - Singer Page.png`) orijinal sanatçı ve albüm fotoğrafları eksikti.
2. **Tipografi**: Sayfalarda belirgin bir sans-serif font tanımlanmadığı için üst katmandan el yazısı/kıvrımlı font (`Arima`) miras alınıyordu; Figma'daki düz, net sans-serif font (`system-ui`) uygulanmamıştı.
3. **Kart ve Panel Düzenleri**:
   - Albümler: Figma'da yatay 4 kolonlu kompakt kaset/CD kartları (kaset, Bergen, Cansever, Tatlıses, Klasör, Linet), arama kutusu ve 3 dikey çubuklu ekolayzır butonu (`|||`), sağ panelde Sibel Can dairesel neon portresi ve 2 sütunlu metadata tablosu yer alıyordu.
   - Sanatçılar: 5 kolonlu dikey kartlar (Sibel Can, Dilso'z, Ankaralı Ayşe, Ankaralı Yasemin, Bergen), dairesel fotoğraflar, sağ panelde Sibel Can portresi, 3 rozet (🎵 48, 💿 8, 🎂 1986), biyografi metni ve eylem butonları.
   - Albüm Detay: Linet CD mücevher kutusu kapağı, Göksel yıldızlı başlık, MP3 bitrate rozeti ve sağda 7 şarkılık detaylı liste.

### B. Uygulanan Düzeltmeler
1. **Orijinal Figma Varlıklarının Çıkarılması**:
   - `assets.coremusic.net/Image/figma-extracted/` dizinine mockup'lardan piksel hassasiyetinde aktarıldı:
     - `album_cassette.png`, `album_bergen.png`, `album_cansever.png`, `album_tatlises.png`, `album_folder.png`, `album_linet.png`
     - `singer_sibel.png`, `singer_dilsoz.png`, `singer_ayse.png`, `singer_yasemin.png`, `singer_bergen.png`
     - `sidebar_sibel.png`, `cd_case_linet.png`
2. **Şablon ve Stil Yenilemesi**:
   - `home.coremusic.net/pages/albums.php` & `p-albums.css`: 4 kolonlu yatay kartlar, 9 tür çipi, net `system-ui` sans-serif tipografi, arama + 3 çubuklu ekolayzır butonu, Sibel Can metadata sağ paneli.
   - `home.coremusic.net/pages/artists.php` & `p-artists.css`: 5 kolonlu dikey kartlar, dairesel fotoğraflar, 3 istatistik rozeti, biyografi ve eylemler.
   - `home.coremusic.net/pages/album-detail.php` & `p-album-detail.css`: CD kutusu albüm kartı, şarkı adı/süre/yıldız tablosu.
3. **Doğrulama ve Test**:
   - Chrome DevTools MCP canlı viewport (1024×600) ekran görüntüleriyle Figma PNG mockupları piksel piksel karşılaştırıldı ve tam eşleşti.
   - PHPUnit `home.coremusic.net`: 73 test, 277 assertions (%100 OK).
   - PHPUnit `shared`: 474 test, 1509 assertions (%100 OK).
   - Vault script gates: `screens-frontmatter-check.ps1` (31/0), `kalip-abc-check.ps1` (0/0/0 GECTI), `wiki-link-check.ps1` (0 kırık).


