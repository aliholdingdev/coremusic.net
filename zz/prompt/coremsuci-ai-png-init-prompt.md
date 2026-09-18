Red Team , human , truth mode -> şimdi frontend/UI kararlarımız var, bunları .ai ya ve .claude ye işlememiz lazım

*artık* *C:\www\coremusic.net\.ai\architecture* buradan mevcut mimariyi önce oku sonra devam et *değişiklik yapmadan* sadece aşağıdaki *promtu* uygula;

*ROLE*

Sen 50+ yıllık Senior Frontend Architect, UI/UX Systems Engineer, Design System Architect, Accessibility Engineer ve Technical Writer'sin.

Uzmanlık alanların:

•⁠  ⁠Vanilla JavaScript (ES6+, framework YASAK — ADR-001)
•⁠  ⁠ITCSS Mimarisi
•⁠  ⁠BEM Metodolojisi
•⁠  ⁠CSS Custom Properties / Design Token Sistemleri
•⁠  ⁠Responsive & Adaptive Layout
•⁠  ⁠Glassmorphism / backdrop-filter
•⁠  ⁠WCAG 2.2 Erişilebilirlik
•⁠  ⁠Touch-First / Kiosk / Embedded UI
•⁠  ⁠Raspberry Pi 5 gömülü arayüz
•⁠  ⁠PHP 8.x şablon entegrasyonu
•⁠  ⁠SPA Router / DOM Patching
•⁠  ⁠Web Performance (CLS, LCP, font loading)
•⁠  ⁠Figma → Kod dönüşümü
*deneyim:* Yaklaşık 50 yıllık Aşkın Deneyim

*Sistem sadece normal bir web arayüzü değildir.* Hedef cihaz Raspberry Pi 5 + 7 inç dokunmatik ekrandır. Fare yoktur. Hover yoktur. Klavye yoktur. Parmak vardır.

---

# ZORUNLU OKUMA SIRASI

Aşağıdaki dosyaları BU SIRAYLA oku. Sıra atlanamaz.

| # | Dosya | Amaç |
|---|-------|------|
| 1 | ⁠ C:\www\coremusic.net\.ai\CLAUDE.md ⁠ | Kanonik AI talimatı |
| 2 | ⁠ C:\www\coremusic.net\.ai\AGENTS.md ⁠ | Agent kayıt defteri |
| 3 | ⁠ C:\www\coremusic.net\.ai\WORKFLOW.md ⁠ | Süreçler |
| 4 | ⁠ C:\www\coremusic.net\.ai\index.md ⁠ | Master katalog |
| 5 | ⁠ C:\www\coremusic.net\.ai\keys.md ⁠ | Anahtar kelime haritası |
| 6 | ⁠ C:\www\coremusic.net\.ai\brain.md ⁠ | Mimari kararlar |
| 7 | ⁠ C:\www\coremusic.net\.ai\architecture\ ⁠ | Mevcut mimari (değiştirme, sadece oku) |
| 8 | ⁠ C:\www\coremusic.net\.ai\ui-design\00-index.md ⁠ | UI tasarım indeksi |
| 9 | ⁠ C:\www\coremusic.net\.claude\rules\css-standards.md ⁠ | CSS kuralları |
| 10 | ⁠ C:\www\coremusic.net\.ai\log.md ⁠ | Aktivite günlüğü |

---

# ADIM 0 — GÖRSELLERİ OKU (ZORUNLU, ATLANAMAZ)

*Bu adım tamamlanmadan tek satır CSS, HTML veya JS planı yazılamaz.*

Mockup dizini:


C:\www\coremusic.net\.ai\.png\home-1024\


## 0.1 — Bilinen Problem ve Çözümü

Bu dizin nokta ile başlıyor ve yine nokta ile başlayan ⁠ .ai\ ⁠ içinde. Dosya arama araçları nokta ile başlayan dizinleri varsayılan olarak ATLAR. Bu yüzden bugüne kadar hiçbir agent mockup'ları görmedi.

*İlk iş olarak dizini .ai ya ve caldue code claude.md ye çekirdek dm elre kaydet :*


Sonra tüm ⁠ .md ⁠ dosyalarında ⁠ .ai/.png/ ⁠ geçen referansları ekle Özellikle: ⁠ .ai/ui-design/00-index.md ⁠ satır 103 ve 152.

## 0.2 — Görsel Eşleme Tablosu

| Ekran | PNG dosyası | Rota | Hedef CSS |
|---|---|---|---|
| Ana sayfa | ⁠ Linux  1024 - Home Page.png ⁠ | ⁠ / ⁠ | ⁠ 05_Pages/_home-*.css ⁠ |
| Hoş geldin modalı | ⁠ Linux  1024 - Home Page Welcome Popup.png ⁠ | ⁠ / ⁠ ilk giriş | yok |
| Albümler | ⁠ Linux  1024 - Albumler Page.png ⁠ | ⁠ /albumler ⁠ | yok |
| Albüm detay | ⁠ Linux  1024 - Albumler Details Detay Page.png ⁠ | ⁠ /album/:id ⁠ | yok |
| Sanatçı | ⁠ Linux  1024 - Singer Page.png ⁠ | ⁠ /sanatci/:id ⁠ | yok |
| Göz At | ⁠ Linux  1024 - Göz At Page.png ⁠ | ⁠ /goz-at ⁠ | yok |
| Göz At (seçili) | ⁠ Linux  1024 - Göz At - Tıklama Clikced.png ⁠ | ⁠ /goz-at ⁠ | yok |
| Playlist | ⁠ Linux  1024 - Playlist Page.png ⁠ | ⁠ /playlist/:id ⁠ | yok |
| Playlist (video) | ⁠ Linux  1024 - Playlist Page - Video Played.png ⁠ | ⁠ /playlist/:id ⁠ | yok |
| WiFi hızlı panel | ⁠ Linux  1024 - Wifi Qucik Page Base.png ⁠ | overlay | yok |
| WiFi bağlan | ⁠ Linux  1024 - Wifi Coonect Light.png ⁠ | overlay | yok |
| Bluetooth panel | ⁠ Linux  1024 - Bluethoot Qucik Page Base.png ⁠ | overlay | yok |

Ayrıca ⁠ shared-1024\ ⁠ altında 6 kimlik doğrulama ekranı var (Login, Register, Register step 2-3, Select Gender, Select Gender - selected).

eğer .ai daki veriler iel çelilem varsa bana uyar deki nasıl yaapkım ben sana oıarda yölendirecem 

## 0.3 — Görsel açılamıyorsa

DUR. Kod yazma. Tahmin etme. Şunu bildir:


MOCKUP ERİŞİLEMEDİ — [dosya adı]
Neden: [erişim hatası / dizin bulunamadı / format okunamadı]


Görsel okumadan üretilen frontend planı GEÇERSİZDİR ve reddedilecektir.

---

# DOĞRULANMIŞ ÖLÇÜLER

Aşağıdaki değerler mockup'lardan piksel düzeyinde ÖLÇÜLMÜŞTÜR. Varsayım değildir. Bunları değiştirme, üzerine tahmin ekleme.

## Tüm ekranlarda sabit


Ekran            : 1024 x 600 (RPi5 7" dokunmatik)
Header           : 60px    (y 0-60)
İçerik bölgesi   : 450px   (y 60-510)
İçerik paneli    : y 71-495 (üstte 11px, altta 15px dış boşluk)
Footer / player  : 90px    (y 510-600)
Footer üst kenarı: y=510 — ilerleme çubuğu burada
Oynatma daireleri: y 533-566, 33px çap
Accent rengi     : #ff4fd8
Gövde fontu      : Arima
Logo fontu       : Bickham Script Two
Arka plan        : tam kaplama fotoğraf + backdrop-filter cam efekti

ama renk ler suanlık pembe ama geelckte mavi etc.. başak renekrld e oalcka ozyuden teheme sistemi gerkeiyor 

tema sistemi  planla buan göre gelcte not düş md lere 

## ÇELİŞKİ — KARAR GEREKTİRİR

⁠ assets.coremusic.net\Css\01_Abstracts\a-layout-tokens.css ⁠ satır 25:

⁠ css
--footer-h-compact: 104px;
 ⁠

Mockup'ta ölçülen değer *90px*. Aradaki fark 14px.

Bu çelişki çözülmeden footer yüksekliği sabit kodlanmayacak. Planında hangisinin doğru olduğuna dair gerekçeli bir öneri sun, ama tek başına karar verme.

## Sayfaya özel düzenler


Ana sayfa / Albümler / Sanatçı / Playlist : sidebar YOK
Göz At                                    : 3 sütun
    sol sidebar      x  16-183   → 167px
    orta liste       x 186-759   → 573px
    sağ bilgi paneli x 784-1004  → 220px
Playlist (video)                          : sağ panel x 782-970
Hoş geldin modalı                         : 600x308, tam ortalanmış
                                            (merkez x=512, y=299.5)


*DİKKAT:* ⁠ --sidebar-w ⁠ token'ı 280px, mockup 167px. Ayrıca sidebar GLOBAL değil, yalnızca Göz At sayfasına özel. Mevcut ⁠ _home-layout.css ⁠ sidebar'ı global layout gibi kurgulamış — bu yanlış. Planında ⁠ .browse-layout ⁠ (3 sütun) ile ⁠ .home-layout ⁠ (sidebar'sız) ayrımını yap.

---

# MEVCUT KOD DURUMU (ÖNCE DOĞRULA)

Planlama yapmadan önce şu iddiaları kaynaktan doğrula. Doğrulayamadığın maddeyi ⁠ VERIFICATION REQUIRED ⁠ olarak işaretle.

| İddia | Dosya | Durum |
|---|---|---|
| Header markup mockup'a uygun | ⁠ home.coremusic.net\header.php ⁠ | ⁠ .site-header ⁠ BEM yapısı kullanılıyor |
| Ana sayfa markup'ı boş | ⁠ home.coremusic.net\pages\home.php ⁠ | 34 byte, sadece ⁠ <?php declare(strict_types=1); ?> ⁠ |
| Accent kaynakta tanımlı | ⁠ Css\03_Layout\_footer.css:24 ⁠ | ⁠ --fp-accent: #ff4fd8 ⁠ |
| Cihaz CSS'i yüklenmiyor | ⁠ js\device-loader.js:108 ⁠ | ⁠ 'embedded': 'tablet' ⁠ — d-embedded.css ölü |
| Tablet CSS boş | ⁠ Css\08_Devices\d-tablet.css ⁠ | 0 byte |
| CSRF açığı | ⁠ coremusic-shared\src\Middleware\CsrfMiddleware.php:37 ⁠ | ⁠ ['set-gender'] ⁠ bypass |

---

# GÖREV

*Kod yazma. PLAN yaz.* (Zero Code Before Plan — ADR-042)

Aşağıdaki çıktıları üret:

## Çıktı 1 — ⁠ .ai/ui-design/00-mockup-index.md ⁠

Yukarıdaki eşleme tablosunu ve doğrulanmış ölçüleri içeren kanonik indeks. Bundan sonra her frontend görevinin başlangıç noktası bu dosya olacak.

## Çıktı 2 — ⁠ .ai/ui-design/10-component-inventory.md ⁠

12 mockup'ın her birini tek tek aç ve içindeki her bileşeni çıkar. Her bileşen için:

•⁠  ⁠BEM sınıf adı önerisi (mevcut şemayla uyumlu, yeni şema uydurma)
•⁠  ⁠x / y konumu, genişlik / yükseklik
•⁠  ⁠Kullanılacak token (renk, boşluk, yazı tipi)
•⁠  ⁠Dokunmatik hedef boyutu ve 44px kuralına uygunluğu
•⁠  ⁠Hangi ITCSS katmanına ait olduğu
•⁠  ⁠Hangi PNG'den çıkarıldığı

## Çıktı 3 — ⁠ .ai/ui-design/11-implementation-plan.md ⁠

Ekran ekran uygulama sırası. Her adım için: hangi dosya, hangi mockup, hangi bileşenler, bağımlılıklar, tahmini zorluk.

Sıra önerisi: Header → Footer → Ana sayfa → Albümler → Sanatçı → Playlist → Göz At → Overlay panelleri → Hoş geldin modalı.

## Çıktı 4 — ⁠ .ai/ui-design/12-accessibility-gaps.md ⁠

Mockup'lardaki WCAG ihlalleri ve çözüm önerileri. Bilinen iki tanesi:

•⁠  ⁠Hoş geldin modalı "Başla" butonu: *105 x 25 px* (minimum 44px, RPi5 için 48px)
•⁠  ⁠Göz At sidebar satırları: *28px aralık, 21px yükseklik* (minimum 44px)

Her ihlal için: mockup'ı mı değiştirelim, kodu mu? Gerekçeli öneri sun.

---

# KATI KURALLAR

| # | Kural |
|---|---|
| 1 | *Framework YASAK.* Vanilla JS. React, Vue, Alpine, jQuery, Tailwind, Bootstrap yeni bileşen için kullanılmaz (ADR-001) |
| 2 | *Yeni sınıf şeması uydurma.* Mevcut BEM yapısını sürdür: ⁠ .site-header → __inner → __logo \| __nav (.nav-link) \| __actions ⁠ |
| 3 | *Yeni renk / boşluk değeri uydurma.* Sadece ⁠ 01_Abstracts ⁠ altındaki token'ları kullan. Token yoksa önce token öner |
| 4 | *ITCSS katman sırası bozulmaz.* 01→09. Üst katman alt katmanı ezmez |
| 5 | *Dokunmatik hedef minimum 44px*, RPi5 için 48px (⁠ --touch-min ⁠, ⁠ --touch-recommended ⁠) |
| 6 | *Hover'a iş yükleme.* Dokunmatik cihazda hover yok. Focus-visible kullan |
| 7 | *Mockup'ta olmayan öğe ekleme. Mockup'ta olan öğeyi atlama* |
| 8 | *In-Place Refactoring.* Dosya adı/yolu kullanıcı onayı olmadan değişmez (⁠ .ai/.png ⁠ yeniden adlandırması hariç — o onaylıdır) |
| 9 | *Her etkileşim ⁠ .ai/log.md ⁠'ye yazılır.* Gerçek zaman damgasıyla. Uydurma damga yasak |
| 10 | *Doğrulanamayan bilgi ⁠ VERIFICATION REQUIRED ⁠ olarak işaretlenir* |

## MSA LİMİTİ — İSTİSNA

Görev başına 15 dosya limiti geçerlidir, *ancak görsel referanslar bu limite DAHİL DEĞİLDİR.* ⁠ .ai/design-refs/** ⁠ altındaki PNG'ler her zaman okunur. Limit yüzünden mockup atlanamaz.

---

# YASAKLAR

•⁠  ⁠Mockup'a bakmadan CSS/HTML planı yazmak
•⁠  ⁠"Muhtemelen şöyledir" diyerek ölçü tahmin etmek
•⁠  ⁠Var olmayan dosya/dizin referansı vermek
•⁠  ⁠Yapılmamış işi yapılmış gibi raporlamak
•⁠  ⁠⁠ .ai/log.md ⁠'ye uydurma zaman damgası yazmak
•⁠  ⁠Tek turda 12 ekranı birden planlamaya çalışmak
•⁠  ⁠Mevcut çalışan kurguyu "düzeltme" adına bozmak (⁠ main.css ⁠ cihaz katmanlarını bilerek import etmez — ⁠ device-loader.js ⁠ runtime'da yükler, statik import EKLEME)

---

# RED TEAM • HUMAN MODE • TRUTH MODE

Planı bitirdikten sonra kendi planını onaylama. Stres testinden geçir ve cevapları yaz:

1.⁠ ⁠Bu planın başarısız olmasının en güçlü 3 sebebi nedir?
2.⁠ ⁠Hangi varsayımım yanlışsa plan tamamen çöker?
3.⁠ ⁠Zeki bir şüphecinin ilk itirazı ne olurdu?
4.⁠ ⁠Karar öncesi neyi doğrulamam gerekiyor?
5.⁠ ⁠Mockup ile mevcut kod arasında kaç çelişki buldum, hangileri karara bağlanmadı?

---

# ÇIKTI FORMATI

•⁠  ⁠Türkçe yaz, teknik terimler İngilizce kalsın
•⁠  ⁠Her ⁠ .md ⁠ dosyası maddeler halinde, tablolarla
•⁠  ⁠Her iddianın yanında kaynak dosya ve satır numarası
•⁠  ⁠Kaynak gösteremediğin iddiayı ⁠ VERIFICATION REQUIRED ⁠ işaretle
•⁠  ⁠Todo listesini güncel tut, tamamlananları geçmişte tut
•⁠  ⁠⁠ /prompt-maker ⁠ ve ⁠ /vault-sync ⁠ skill'lerini kullan

---

# BAŞLANGIÇ

İlk turda SADECE şunu yap:

1.⁠ ⁠Zorunlu okuma listesindeki 10 dosyayı oku
2.⁠ ⁠⁠ .ai/.png ⁠ → ⁠ .ai/design-refs ⁠ yeniden adlandırmasını yap ve referansları güncelle
3.⁠ ⁠*⁠ Linux  1024 - Home Page.png ⁠* görselini aç, oku, içindeki her bileşeni ölç ve bana yazılı rapor et
4.⁠ ⁠Çıktı 1'i (⁠ 00-mockup-index.md ⁠) oluştur

Bu dördü bitmeden diğer ekranlara geçme. Onayımı bekle.

ok ama yanlzı buardaki isimlerin bir anlamı var .ai\.png\home-1024\Linux  1024 burada hom subdomaini goenum modu ve 1024x600 emebwnd sistem tarsaımı ui uix ve linux rpi5 için olduğu söyleniyor onu dikat et oan göre olan yap yani geelckte hoem or studio or pro or tizen tiv leri için or tv le için 4k ve 2 k tv ler için tizen tv ve nromal tv ler için web için nwavtive için bazı atrsıamlr değişiemektşr durma os a ve platforma göre değişriemktir oan gör eplan yap suan lisdmzşkei linux 1024x400 hoem emebdnrt için var 


2) GÖRSEL KONUMU — teyit için sağ olun. Prompt zaten
.ai/.png/home-1024/ yolunu kullanıyor, değişiklik gerekmiyor.

3) "EK SORUN" KONUSUNDA SİZ HAKLISINIZ, BEN YANILDIM
İlk mesajınızda "onlar planlama içinde" demiştiniz, ben bunu atlamışım.
00-index.md satır 110-116'ya tekrar baktım:

    | .ai/.png/home-1920/ | 1920×1080 | Empty |
    | .ai/.png/pro-1024/  | 1024×600  | Empty |
    | .ai/.png/pro-1920/  | 1920×1080 | Empty |
    | .ai/.png/studio-*   |           | Empty |

Bunlar zaten "Empty" işaretli. Yol haritası girdisi, hata değil.
SİLMEYİN. Önceki mesajımdaki "bu satırları sil" talimatını iptal edin.

Yanlış olan tek satır 104:

    | .ai/.png/home-3840/ | 3840×2160 | 6 PNGs | Fully analyzed |

Dizin yok, 6 PNG yok, analiz edilmemiş. "Planlanan" ile "tamamlandı"
farklı şeyler ve agent bunu tamamlanmış sanıp olmayan dosyalara
güveniyor. Sadece o satır şuna çevrilsin:

    | .ai/.png/home-3840/ | 3840×2160 | PLANLANDI — henüz oluşturulmadı |

4) "YENİ PROMPT İLE .ai YA TANITMAK" — EN ÖNEMLİ NOKTA, HAKLISINIZ
Prompt'ta gerçek bir eksik varmış, siz yakaladınız.

Mevcut haliyle prompt 00-mockup-index.md dosyasını OLUŞTURUYOR, ama o
dosyayı boot protokolüne EKLEMİYOR. Sonuç: index dosyası ortada durur,
bir sonraki oturumda agent onu okumaz, mockup'ları yine görmez ve
çözmeye çalıştığımız sorun geri döner.

Kalıcı çözüm için prompt'a bir çıktı daha eklemek gerekiyor. Prompt'un
"GÖREV" bölümündeki 4 çıktının SONUNA aşağıdakini ekleyin:


## Çıktı 5 — VAULT'A KALICI KAYIT (en kritik adım)

Mockup'ları .ai vault'una kalıcı olarak tanıt. Aşağıdaki 4 dosyayı
düzenle. Bu adım yapılmazsa sonraki oturumlarda mockup'lar yine
görünmez olur ve tüm bu iş boşa gider.

### 5.1 — ⁠ .ai/CLAUDE.md ⁠
Boot protokolü tablosuna 11. satır olarak ekle:

| 11 | ⁠ .ai/ui-design/00-mockup-index.md ⁠ | Mockup eşleme tablosu — frontend görevlerinde ZORUNLU |

Ve dosyanın "Quick Rules" bölümüne şu kuralı ekle:

| Mockup Before Frontend | Frontend görevinde mockup okunmadan kod yazılamaz | [[.ai/ui-design/00-mockup-index.md]] |

### 5.2 — ⁠ .ai/AGENTS.md ⁠
"5 Hard Rules" tablosuna 6. kural olarak ekle:

| 6 | *Mockup Before Frontend* | CSS/HTML/JS/layout/bileşen görevlerinde ⁠ .ai/design-refs/ ⁠ altındaki ilgili görsel okunmadan kod yazılamaz. Görsel okunamıyorsa DUR ve bildir. |

MSA Limit bölümüne istisnayı ekle:

	⁠*İstisna:* Görsel referanslar (⁠ .ai/design-refs/** ⁠) 15 dosya
	⁠limitine dahil değildir.

### 5.3 — ⁠ .ai/index.md ⁠
Master kataloğa satır ekle:

| [[ui-design/00-mockup-index]] | *Mockup eşleme tablosu — frontend başlangıç noktası* |

### 5.4 — ⁠ .ai/keys.md ⁠
Anahtar kelime haritasına ekle:

  frontend, css, html, ui, layout, bileşen, ekran, sayfa, tasarım,
  mockup, görsel  →  .ai/ui-design/00-mockup-index.md
                     .ai/design-refs/home-1024/

### 5.5 — Doğrulama
Bu 4 dosyayı düzenledikten sonra kendini test et:
".ai/keys.md üzerinden 'frontend' kelimesini arasam, mockup dizinine
ulaşabilir miyim?" Cevap hayırsa düzenleme eksiktir, tamamla.


Bu Çıktı 5 aslında işin en önemli parçası. Diğer 4 çıktı bir kerelik
analiz üretiyor; Çıktı 5 ise sistemi kalıcı olarak düzeltiyor. Ondan
sonra "frontend kodla" dediğinizde agent mockup'a kendiliğinden gidecek,
her seferinde elle hatırlatmanıza gerek kalmayacak.

Bu ek, prompt'un tek gerçek boşluğunu kapatıyor. Kalanı olduğu gibi geçerli.

Bu mesajı bir atabilir misiniz?

ok ama yanlzı buardaki isimlerin bir anlamı var .ai\.png\home-1024\Linux  1024 burada hom subdomaini goenum modu ve 1024x600 emebwnd sistem tarsaımı ui uix ve linux rpi5 için olduğu söyleniyor onu dikat et oan göre olan yap yani geelckte hoem or studio or pro or tizen tiv leri için or tv le için 4k ve 2 k tv ler için tizen tv ve nromal tv ler için web için nwavtive için bazı atrsıamlr değişiemektşr durma os a ve platforma göre değişriemktir oan gör eplan yap suan lisdmzşkei linux 1024x400 hoem emebdnrt için var 

Kullanıcı büyük bir frontend/UI planlama görevi veriyor. Beni CoreMusic Plan Agent olarak tanıtmış. Şimdi bu karmaşık görevi analiz etmem gerekiyor.

Önce mevcut durumu anlamalıyım. Kullanıcı .ai ve .claude dosyalarında frontend/UI kararlarını işlemek istiyor. Öncelikle okuma listesindeki dosyaları okumalıyım, sonra mevcut mimariyi anlamalıyım.

Görev çok detaylı: Mockup analizi, bileşen envanteri, uygulama planı, erişilebilirlik boşlukları ve vault'a kalıcı kayıt. Ayrıca platform isimlerinin anlamını vurguluyor: home-1024, studio, pro, tizen tv, 4k/2k tvler için farklı tasarım gereksinimleri var.

Plan mode'dayım, sadece okuma ve plan yapabilirim. Hiçbir dosyayı düzenleyemem. O zaman önce mevcut yapıyı anlamalıyım.

Adım 1: Zorunlu okuma listesindeki 10 dosyayı okumalıyım (CLAUDE.md, AGENTS.md, WORKFLOW.md, index.md, keys.md, brain.md, architecture/, ui-design/00-index.md, css-standards.md, log.md). Bu dosyalar zaten system prompt'ta verilmiş, ama tam içeriği okumalıyım.

Adım 2: .ai/.png dizinini kontrol etmeliyim (home-1024 altındaki PNG'leri).

Adım 3: Mevcut ui-design yapısını anlamalıyım.

Kullanıcı ayrıca "adım adım planlayalım" diyor. Yani benden bir plan çıkarmamı istiyor, hemen uygulamamı değil.

Önce mevcut durumu anlamak için dosya okumaları yapayım.

# Questions
PNG mockup'larını göremiyorum (araç görsel okuyamıyor). Planı neye göre oluşturayım?
PNG LERİ OKUAMNI İSTYORUM VE ASCİ ART VİEW SCREEN OLARAK MD ELR AYAKEYMANI MD ASLEİR REFABRS MD ELR GİBİ YAPASIKLRMAI İSTYOPRUM Aİ FORNMETET PO BİLGİELRİ KUYALACKA GERİERĞİİDNE PNG ELRİ OKUYUACASK
5 dosyanın tamamını tek seferde planlayayım mı, yoksa sırayla mı?
2. AMA SADECXE KALOR VE İEÇRKEİK AYAPLERISNI YAPIP PEROMT HALİDNE ALACĞIM SENDEN


.ai/ui-design/
├── 00-mockup-index.md              ← Master katalog (18 PNG → meta veri)
├── 01-component-inventory.md       ← Bileşen envanteri (BEM, token, ITCSS)
├── 02-implementation-plan.md       ← 15 adımlık uygulama planı
├── 03-accessibility-gaps.md        ← WCAG 2.2 AA gap analizi
└── 04-vault-registration.md        ← Vault'a kayıt değişiklikleri  BU YAPI LAZIM BUNUN İÇİNde worklow prompt screen ama secreen goruplanmalıoamn göre devam ert refans al **"C:\Users\Bayram Ali\Desktop\Bayram Ali Gecici\.ai.backup\ui-design"** buardan refanas oalrak bu promptu uygula yapaılandır ama önce erişim izni iste!!!

yenid izin ayspını kontrole t uyumnlu ise kullan 

```
.ai/ui-design/
│
├── 00-mockup-index.md                    ← Master katalog (18 PNG → meta)
├── 01-component-inventory.md             ← C01-C16 + token envanteri
├── 02-implementation-plan.md             ← 15 adımlık CSS uygulama
├── 03-accessibility-gaps.md              ← WCAG 2.2 AA gap analizi
├── 04-vault-registration.md              ← Vault değişiklik listesi
│
├── screens/                              ← Ekran bazlı organizasyon
│   │
│   ├── _layout-patterns/                 ← 5 layout şablonu
│   │   ├── 01-standard-60-40.md          ← Albums, Artists, Files, Queue
│   │   ├── 02-split-home.md              ← Home (45/55 split)
│   │   ├── 03-fullscreen.md              ← Video playback
│   │   ├── 04-modal.md                   ← WiFi, BT, EQ
│   │   └── 05-auth-screen.md             ← Login, Register, Gender
│   │
│   ├── _components/                      ← 16 bileşen detayı
│   │   ├── C01-nav-link.md
│   │   ├── C02-status-widget.md
│   │   ├── C03-user-pill.md
│   │   ├── C04-primary-button.md
│   │   ├── C05-secondary-button.md
│   │   ├── C06-form-input.md
│   │   ├── C07-gender-button.md
│   │   ├── C08-social-login.md
│   │   ├── C09-media-card.md
│   │   ├── C10-detail-panel.md
│   │   ├── C11-genre-tabs.md
│   │   ├── C12-star-rating.md
│   │   ├── C13-track-list.md
│   │   ├── C14-modal.md
│   │   ├── C15-toggle.md
│   │   └── C16-network-row.md
│   │
│   ├── A-auth/                           ← Auth ekranları (6 PNG)
│   │   ├── login.md                      ← Login Girl
│   │   ├── register-step1.md             ← Register Girl
│   │   ├── register-step2.md             ← Register Girl step 2
│   │   ├── register-step3.md             ← Register Girl step 3
│   │   ├── gender-select.md              ← Select Gender
│   │   └── gender-selected.md            ← Select Gender - selected
│   │
│   ├── B-home/                           ← Ana sayfa (2 PNG)
│   │   ├── dashboard.md                  ← Home Page
│   │   └── welcome-popup.md              ← Home Page Welcome Popup
│   │
│   ├── C-music/                          ← Müzik sayfaları (3 PNG)
│   │   ├── artists.md                    ← Singer Page
│   │   ├── albums.md                     ← Albumler Page
│   │   └── album-detail.md              ← Albumler Details
│   │
│   ├── D-player/                         ← Oynatıcı (2 PNG)
│   │   ├── playlist.md                   ← Playlist Page
│   │   └── video-playback.md            ← Playlist - Video Played
│   │
│   ├── E-filemanager/                    ← Dosya yöneticisi (2 PNG)
│   │   ├── disk-browser.md              ← Göz At Page
│   │   └── file-list.md                 ← Göz At - Tıklama
│   │
│   └── F-quickpanel/                     ← Hızlı paneller (3 PNG)
│       ├── bluetooth.md                  ← Bluethoot Qucik Page
│       ├── wifi.md                       ← Wifi Qucik Page
│       └── wifi-connect.md              ← Wifi Coonect Light
│
├── flow/                                 ← Kullanıcı akışları (17 dosya)
│   ├── 00-flow-index.md
│   ├── auth/
│   │   ├── 01-login.md
│   │   ├── 02-register.md
│   │   ├── 03-forgot-password.md
│   │   ├── 04-select-gender.md
│   │   └── 05-logout.md
│   ├── music/
│   │   ├── 01-playback.md
│   │   ├── 02-playlist-queue.md
│   │   ├── 03-album-browse.md
│   │   ├── 04-artist-browse.md
│   │   └── 05-search.md
│   ├── settings/
│   │   ├── 01-wifi-connect.md
│   │   ├── 02-bluetooth-connect.md
│   │   ├── 03-equalizer.md
│   │   └── 04-general.md
│   └── navigation/
│       ├── 01-spa-routing.md
│       ├── 02-header-nav.md
│       └── 03-footer-player.md
│
├── prompt/                               ← Prompt şablonları (35 dosya)
│   ├── 00-prompt-index.md
│   ├── screen/
│   │   ├── 01-1024-embedded.md
│   │   ├── 02-1920-desktop.md
│   │   ├── 03-3840-tv.md
│   │   └── 04-mobile.md
│   ├── component/
│   │   ├── 01-header.md ... 14-gender-button.md
│   ├── layout/
│   │   ├── 01-pattern-standard-60-40.md
│   │   ├── 02-pattern-split-home.md
│   │   ├── 03-pattern-fullscreen.md
│   │   ├── 04-pattern-modal.md
│   │   └── 05-pattern-auth-screen.md
│   └── page/
│       ├── 01-home.md ... 12-reset-password.md
│
└── reference/                            ← Referans dosyaları
    ├── 01-php-source-architecture.md
    ├── 02-design-tokens.md
    ├── 05-php-ui-mapping.md
    ├── 06-text-strings.md
    ├── 07-verification.md
    ├── 08-session-notes.md
    ├── backend-reference.md
    ├── frontend-reference.md
    └── icon-asset-catalog.md
```


```
.ai/ui-design/
│
├── 00-mockup-index.md              ← 18 PNG'nin master kataloğu
├── 01-component-inventory.md       ← C01-C16 bileşen envanteri
├── 02-implementation-plan.md       ← 15 adımlık CSS uygulama planı
├── 03-accessibility-gaps.md        ← WCAG 2.2 AA gap analizi
├── 04-vault-registration.md        ← Vault'a kayıt değişiklikleri
│
├── screens/                        ← Ekran bazlı organizasyon
│   ├── _layout-patterns/           ← 5 layout şablonu
│   ├── _components/                ← 16 bileşen detayı
│   ├── A-auth/                     ← 6 auth ekranı
│   ├── B-home/                     ← 2 ana sayfa
│   ├── C-music/                    ← 3 müzik sayfası
│   ├── D-player/                   ← 2 oynatıcı
│   ├── E-filemanager/              ← 2 dosya yöneticisi
│   └── F-quickpanel/               ← 3 hızlı panel
│
├── flow/                           ← 17 kullanıcı akışı
└── reference/                      ← 9 referans dosyası
```


Şimdi buradan bakarak screen dosyalarını ve çekirdek dosyaları yeniden yaz. Bozuk olan dosyaları sen kendin zaten yeniden yazmak zorundasın ve Devam et. Yeniden plan oluştur, plana göre devam et. Kafana göre bir şey yapma. bu ve PowerShell asla kullanma sana PowerShell kullanmayı yasaklıyorum bunu emdiğleri not et  Yalnız veriler derinlemesine derin kaliteli PNG'lerle aynı olması lazım. Uzun ve kaliteli doğrulanmış veriler olması lazım ki eğer bunlar referans olarak kodlamak için kullanacaktır. 


Hayır önce selekkenli cinsel seçimin sonu login registre gelmesi gerekmektedir.

Yalnız veriler uzun derinlemesine derin kaliteli. IA.PNG.PNG'deki her şey bütün detaylar olmalı. ASC, Art View'lar olmalı. Sakın PowerShare kullanma hepsini düzgün bir şekilde onar yeniden yaz. Eksikler olmasın. Endini aynısını yeniden yaz.

oluştur yalnız referancede backup demişim backup dosyasının linkini belirt izinli ve izin alması gerektiğini permission olayı gerektiğini söyle ve .ei'deki katman mimariden falan oradan .ei'yi göre ea'yi göre oradan referansları belirt ne varsa belirt gerekli ne varsa

asci art view screen Oluştur derinlemesini uzun detaylı kaliteli veriler ile doğrulanmış veriler ile yap bunları.