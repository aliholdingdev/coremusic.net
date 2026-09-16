---
title: "CoreMusic — Teknik Dokümantasyon Kitapçığı (Sayfa 1 - 8 Master Mizanpaj)"
type: technical-documentation-book
version: 3.0.0
status: active
target: "Adobe InDesign — Freelancer Technical Documentation (A4 Kitapçık Sayfa 1–8)"
authority: SSOT
created: 2026-09-14
language: tr-TR
reference:
  - "coremusic.net/AGENTS.md"
  - "coremusic.net/CLAUDE.md"
  - "coremusic.net/WORKFLOW.md"
  - "coremusic.net/brain.md"
  - "coremusic.net/aa.md"
  - "coremusic.net/aa_1_2_temel_ozellikler.md"
  - "coremusic.net/aa_indesign_birebir_sayfa2_sayfa3.md"
---

# CoreMusic — Freelancer Technical Documentation
## Master Mizanpaj ve İkonlu Tipografi Kılavuzu (Sayfa 1 – Sayfa 8)

> **Mizanpaj & Tasarım Entegrasyonu Notu:**  
> Bu doküman, elinizdeki InDesign sayfalarında (`Sayfa 1'den Sayfa 8'e kadar`) tespit edilen tüm metin taşmalarını (overset text), imla hatalarını (`Freelencer`, `3.3 Sistem çalışma şekli` vb.) ve kelime bölünmelerini giderir.  
> Referans tasarım şablonlarındaki **İkonik Özellik Rozetleri**, **Büyük Tırnaklı Alıntı Kutuları (Quote Callout)**, **5 Renkli Not Kutusu Sistemi (Info, Success, Warning, Critical, Tip)** ve **Teknik Özellik Tabloları** sayfa sayfa birebir kopyalanıp yapıştırılacak şekilde entegre edilmiştir.

---

## 📋 BÖLÜM I: MEVCUT INDESIGN DOKÜMANI DENETİM RAPORU (FACT-CHECK)

Mevcut 8 sayfalık InDesign PDF çıktınız incelenmiş ve aşağıdaki düzeltmeler yapılmıştır:

1. **Header Yazım Hatası:** Tüm sayfalarda `Freelencer Technical Documentation` yazan başlık, doğru İngilizce imlası olan **`Freelancer Technical Documentation`** olarak düzeltildi.
2. **İçindekiler (TOC) Hataları:**
   - Sayfa 2'de `3.3 Sistem çalışma şekli` yazan satır, Bölüm 02'ye ait olduğu için **`2.3 Sistem Çalışma Şekli`** olarak düzeltildi.
   - `Core Music nedir ?` ifadesindeki boşluklar kaldırılarak kurumsal tescilli adı olan **`1.1 CoreMusic Nedir?`** yapıldı.
   - Sayfa numaraları gerçek metin akışına göre güncellendi: `1.1` (Sayfa 4), `1.2` (Sayfa 6), `02 Sistem Mimarisi` (Sayfa 10).
3. **Sayfa 3 Hayalet Metin (Ghost Frame) Temizliği:** Sayfa 3'ün arka planında sol sayfadan taşan metin kalıntıları temizlendi; yerine görsel alıntı kutusu, 4 ikon rozeti ve anahtar kelimeler yerleştirildi.
4. **Sayfa 4 & Sayfa 5 Kelime Bölünmesi (Overset Break):** Sayfa 4'ün altındaki `Enstrüman ve vokalin kulağın hemen yanında hisse-` bölünmesi kaldırıldı; Sayfa 4 kendi içinde bağımsız tamamlandı, Sayfa 5 ise tam donanım ve DSP analiz sayfası olarak yapılandırıldı.
5. **Sayfa 6, 7 & 8 Dengeli Mizanpajı:** 10 temel özellik; Deemix (Deezer FLAC 16/24/32-Bit), YouTube Music, Nova Search Engine, USB/CD yazma ve AI dinleme alışkanlığı döngüsüyle zenginleştirilerek 3 sayfaya tam sığacak şekilde dağıtıldı. Sayfa 8'in altına kurumsal **Teknik Özellikler Matrisi** ve **Note Box** eklendi.

---

# 📄 SAYFA SAYFA INDESIGN BİREBİR YAPIŞTIRMA METİNLERİ

---

## 🎨 SAYFA 1: ÖN KAPAK (COVER PAGE)

*(InDesign Sayfa 1: Pembe çiçekli, piyanolu kız görselinin üzerindeki tipografik katmanlar)*

```text
[ÜST LOGO ALANI]
CoreMusic
MUSIC BEYOND LIMITS

[ORTA BAŞLIK ALANI]
Freelancer Technical Documentation
Software • Audio • Hardware • AI
Version 1.0.0 — September 2026 — CoreMusic Confidential

[SLOGAN / MANŞET]
A Smarter Audio Life For Everyone
Music Connects Everywhere...

[ALT KİTAP SLOGANI / EL YAZISI ROZETİ]
"Hayatın ritmi sende gizli...
 Müziğinle parla!"
```

---

## 📑 SAYFA 2: İÇİNDEKİLER BÖLÜMÜ (TABLE OF CONTENTS)

*(InDesign Sayfa 2: Sol Sayfa. Parçalı kutuları silip tek bir metin çerçevesine yapıştırınız. `Shift + Tab` ile sayfa numaralarını sağa yaslayınız.)*

```markdown
İçindekiler Bölümü
Freelancer Technical Documentation — Version 1.0

01  Giriş (Genel Bakış, Vizyon ve Anahtar Kelimeler) ............................................................................ 3
    1.1  CoreMusic Nedir? ............................................................................................................................ 4
    1.2  Temel Özellikler ve Sistem Yetenekleri .................................................................................... 6
    1.3  Kullanım Alanları ve Sektörel Çözümler ................................................................................... 8
    1.4  Hedef Kullanıcı Kitleleri (8 Seviyeli UX) ..................................................................................... 9
    1.5  Pazar Problemleri ve CoreMusic Çözümü .................................................................................. 9

02  Sistem Mimarisi (L0–L6 Katmanlı Yapı & 10 Subdomain) ............................................................... 10
    2.1  Mimari Genel Bakış (Clean Architecture & Hexagonal) .......................................................... 11
    2.2  L0–L6 3D Katman Dağılımı ve Görev Sorumlulukları ............................................................. 12
    2.3  Sistem Bileşenleri ve 10 Alt Alan Adı Topolojisi ...................................................................... 13

03  Kurulum ve Hazırlık (Sistem Gereksinimleri & 6 Adımlı Kurulum) ................................................... 14
04  Sistem Yapılandırması (.env Parametreleri & Mikro Servisler) ........................................................... 16
05  API Dokümantasyonu (REST Uç Noktaları & Hibrit Kimlik Doğrulama) ................................................. 18
06  Ses Motoru ve Donanım (C++20 Neva Engine, 32-Bit DSP & 8.1 Surround) ............................................ 20
07  Kullanıcı Arayüzü (19 Kanonik PNG Mockup & Bileşen Envanteri C01–C16) ........................................ 22
08  Güvenlik ve Uyumluluk (10 Adımlı Güvenlik Hattı & AES-256-GCM Vault) ........................................ 24
09  Dağıtım ve Operasyon (RPi5 Home OS & Docker Mikro Servisleri) ....................................................... 26
10  Geliştirici Standartları (PHP 8.4 Strict, Sıfır ORM & Vanilla JS) ........................................................ 28
11  Ekler ve Sorun Giderme (Dizin Ağacı, Hata Matrisi & Vault SSOT) ..................................................... 30

---
[ℹ️ BİLGİ NOTU KUTUSU - SAYFA ALTI]
ℹ️ Bilgi: Bu dokümantasyon, CoreMusic ekosisteminde görev alan freelancer yazılım, ses ve donanım mühendisleri için tek doğruluk kaynağı (SSOT) olarak hazırlanmıştır.
```

---

## 📖 SAYFA 3: BÖLÜM 01 GİRİŞ (GENEL BAKIŞ, VİZYON VE ANAHTAR KELİMELER)

*(InDesign Sayfa 3: Sağ Sayfa. Üst başlıktan alt kenar kılavuzuna kadar milimetrik oturur; boşluk veya taşma bırakmaz.)*

```markdown
Bölüm
01 Giriş
Genel bakış, vizyon ve anahtar kelimeler

**CoreMusic**; trilyon dolarlık küresel dijital medya, otomotiv ses sistemleri ve tüketici elektroniği pazarındaki yapısal açıkları kapatmak ve doğrudan yüksek kârlılığa dönüştürmek amacıyla geliştirilmiş kurumsal seviyede bir **Ticari Dijital Medya Ekosistemi ve Gelir Platformudur**. Sistem; son kullanıcıların müzik arşivlerini ve dinleme deneyimini radikal biçimde iyileştirirken, ev ve araç içi ses donanımlarını merkezi olarak yönetmek, profesyonel stüdyo ortamlarında stüdyo referansında ses kontrolü sağlamak ve farklı cihazlar arasında **kesintisiz, kayıpsız ve yüksek marjlı bir medya omurgası** inşa etmek üzere tasarlanmıştır.

Günümüz dijital müzik pazarı, kullanıcıları **mülkiyetsiz kiralama modellerine**, **platform bağımlılıklarına** ve **kayıplı (lossy) sıkıştırma standartlarına** mahkûm eden verimsiz bir tekel altındadır. Tüketiciler farklı cihazlarda farklı uygulamalar kullanmakta; bu durum **müzik arşivlerinin dağılmasına**, **cihazlar arası senkronizasyonun kopmasına**, **ses kalitesinin işletim sistemi mikserlerinde çiğnenmesine** ve kullanıcının ödediği abonelik karşılığında **hiçbir kalıcı dijital varlığa sahip olamamasına** neden olmaktadır.

CoreMusic bu pazar krizini doğrudan **ölçeklendirilebilir bir gelir modeline** dönüştürür. Platform, **Offline-First** felsefesiyle kullanıcıya kendi yüksek çözünürlüklü dijital arşivinin **mutlak mülkiyetini** sunarken; müzik içeriklerini, tescilli ses donanımlarını, mikro servisleri ve kullanıcı tercihlerini tek bir mimari omurga (`api.coremusic.net`) altında toplayarak **merkezi, akıllı, yüksek kârlı ve yönetilebilir bir ekosistem** sunar.

---
[BÜYÜK ALINTI KUTUSU / QUOTE CALLOUT - RESİM 1 & 3 STİLİ]
❝ Great sound is not a luxury. It's a better way to live. ❞
— CoreMusic Felsefesi
---

[DÖRT İKONLU ÖZELLİK ŞERİDİ - RESİM 1 STİLİ]
┌─────────────────────────┬─────────────────────────┬─────────────────────────┬─────────────────────────┐
│  📱 Any Device Anytime  │  🎧 Music Everywhere    │  🤖 Smarter With AI     │  🎼 For a Musical Life  │
│  Tüm Ekranlarda Anlık   │  Kayıpsız ve Sınırsız   │  Akustik Zekâ Küratörü  │  Stüdyo Referansında    │
└─────────────────────────┴─────────────────────────┴─────────────────────────┴─────────────────────────┘

**Mülkiyet ve Özgürlük Odaklı Felsefe:**
Modern streaming servislerinin kullanıcıları mülkiyetsiz kiralama modellerine mahkûm ettiği günümüzde CoreMusic, **kullanıcı veri mülkiyetini** merkeze koyar. Kullanıcının sahip olduğu kayıpsız ses koleksiyonu (**FLAC, WAV, MP3** vb.) **kalıcı, bağımsız ve ilişkisel bir dijital varlık** olarak korunur; telif veya lisans iptalleriyle arşivden silinmez.

**Kesintisiz Bütünleşik Yaşam Deneyimi:**
CoreMusic yalnızca bir yazılım değil; akıllı telefondan araç içi bilgi-eğlence panellerine (`car.coremusic.net`), ev medya merkezlerinden (`home.coremusic.net` - RPi5) profesyonel mastering stüdyolarına kadar her temas noktasında yaşayan **bütünleşik bir ses standardıdır**. Salonda başlatılan bir parça, arabaya binildiğinde veya iş istasyonuna geçildiğinde tek bir milisaniye dahi duraksamadan, aynı akustik profille devam eder.

**Bölümün Anahtar Kelimeleri ve Temel Kavramları:**
• **Offline-First & Veri Mülkiyeti:** İnternet bağımsız tam çalışma ve kişisel müzik koleksiyonunun mutlak mülkiyeti.
• **Neva Engine C++20:** Sıfır bellek tahsisi (**Zero-Allocation**) ve kilitlenmesiz (**Lock-Free**) çalışan gerçek zamanlı ses çekirdeği.
• **32-Bit Float & Bit-Perfect:** İşletim sistemi mikserini baypas eden, **1528 dB** teorik dinamik tavanlı saf ses sadakati.
• **Çapraz Cihaz Handoff:** Mobil, PC, Smart TV, Araç ve Ev sunucusu arasında anlık durum ve çalma senkronizasyonu.
• **Çok Kanallı Gelir Mimarisi:** Donanım satışı, araç içi OEM lisanslama, stüdyo paketleri ve kurumsal ekosistem abonelikleri.

---
[ℹ️ NOT KUTUSU - RESİM 1 STİLİ]
ℹ️ Bilgi Notu: CoreMusic mimarisinde her katmanın sorumlulukları ve bağımlılık sınırları kesindir. Bağımlılıklar daima L6'dan L0'a tek yönlü akar; ters yönlü katman ihlalleri derleme aşamasında engellenir.
```

---

## 📄 SAYFA 4: 1.1 COREMUSIC NEDİR? (SİSTEM TANIMI, CANLI TEMALAR & ERGONOMİ)

*(InDesign Sayfa 4: Sol Sayfa. Sayfa altında kelime bölünmesi yapmadan kendi sınırları içinde tam biter.)*

```markdown
1.1  CoreMusic Nedir?

**CoreMusic**; müzik arşivi yönetimi, **gerçek zamanlı dijital ses işleme (DSP)**, çoklu cihaz entegrasyonu ve medya dağıtım süreçlerini tek bir çatı altında toplayan **yeni nesil kurumsal dijital ses ve medya ekosistemidir**.

Geleneksel müzik çalarların basit dosya oynatma mantığını kökten reddeden platform; hem **çevrim içi (online bulut akışı)** hem de internetten tamamen bağımsız **çevrim dışı (offline-first)** mimaride çalışır. Modern müzik endüstrisindeki kiralama tuzaklarına son vererek kullanıcının sahip olduğu dijital ses koleksiyonuna (**FLAC, WAV, MP3** vb.) kalıcı **kullanıcı veri mülkiyeti** kazandırır.

**Büyüleyici Canlı Temalar & AI Destekli Theme Maker:**
CoreMusic; sıradan ve tekdüze arayüzleri tamamen ortadan kaldırır. Çalan müziğin enerjisine ve albüm kapağının harmonik renk tonlarına göre gerçek zamanlı nefes alan **Ambient Aura** ve **büyüleyici canlı temalarıyla** ev, salon ve masaüstü ortamlarını adeta yaşayan bir görsel şölene dönüştürür.

Dahili **Yapay Zekâ Destekli "Theme Maker Tool"** sayesinde ev ve genel kullanıcılar; doğal dille komut vererek (*"80'ler retro neon synthwave"* veya *"Gece mavisi minimalist akustik oda"*) **aşık olacakları güzellikteki kişiselleştirilmiş arayüz temalarını** tek tıkla saniyeler içinde üretebilir ve özelleştirebilirler. Üretilen tüm temalar WCAG 2.2 AA kontrast kurallarına tam uyumludur; metinler her zaman kristal netliğinde okunur.

**Çapraz Cihaz Senkronizasyonu & Kesintisiz Geçiş (Handoff):**
Sistem; **akıllı telefon, tablet, kişisel bilgisayar (PC), akıllı TV, araç içi bilgi-eğlence (`car.coremusic.net`) ve ev medya merkezlerinde (`home.coremusic.net` - RPi5)** hibrit bulut ve yerel ağ (WebSocket/WebRTC) protokolleriyle tam senkronize çalışır. Telefonda kulaklıkla dinlenen bir parça, eve gelindiğinde salondaki sisteme veya arabaya binildiğinde araç amfisine milisaniyelik gecikme ve duraksama olmadan aktarılır.

**Kullanıcıyı Asla Üzmeyen Uyarlanabilir Deneyim:**
Sistem; evinde konforlu ve estetik müzik dinlemek isteyen son kullanıcılardan araç içi dinleyicilere, Hi-Fi meraklılarından canlı sahne sanatçıları ve stüdyo ses mühendislerine kadar her seviyeye kusursuz adapte olan akıllı, sade ve modüler bir arayüz ergonomisi sunar.

---
[💡 İPUCU KUTUSU / PRO TIP - RESİM 2 STİLİ]
💡 İpucu: Ana ekran bileşenlerini, dinamik renk paletini ve canlı cam efektlerini (Glassmorphism) `Ayarlar > Görünüm` menüsünden dilediğiniz zaman kişiselleştirebilirsiniz.
```

---

## 📄 SAYFA 5: SES MOTORU, AKUSTİK DSP, 8.1 SURROUND & TESCİLLİ DONANIM

*(InDesign Sayfa 5: Sağ Sayfa. Sayfa 4'ün karşısıdır. Ses mühendisliği, mekan simülasyonları ve donanım dökümünü içerir.)*

```markdown
1.1  Audio DSP Engine, Çok Kanallı Ses & Medya İstasyonu

CoreMusic'in kalbinde; işletim sistemlerinin sesi bozan sıkıştırma ve yeniden örnekleme (resampling) katmanlarını tamamen baypas eden tescilli **C++20 Neva Engine** gerçek zamanlı ses motoru yer alır.

**Akustik Mekân Simülasyonu & Canlı Atmosfer (3D Reverb):**
Gelişmiş psikoakustik Reverb ve yansıma algoritmaları sayesinde dinleyiciye odanın değil, doğrudan seslendirilen canlı ortamın akustiği yaşatılır:
• **Konser Alanı & Arena:** Genişletilmiş stereo sahne, stadyum derinliği ve açık hava atmosferi.
• **Düğün Salonu & Balo Salonu:** Doğal oda yankısı, canlı rezonans ve yüksek dinamik dolgunluk.
• **Canlı Stüdyo & Akustik Oda:** Enstrüman ve vokalin kulağın hemen yanında hissedildiği sıcak kayıt ortamı.
• **31-Band Parametrik EQ:** 20Hz – 20kHz aralığında 1/3 oktav stüdyo sınıfı filtreleme.

**Distorsiyonsuz Yüksek Güç & Bit Derinliği Standartları:**
• **True Peak Brickwall Limiter:** En yüksek ses seviyesinde dahi dijital çatlama ve bozulmaları (clipping) önler (**THD+N <%0.01**, **SNR >100dB / DAC 112dB**).
• **Aktif 32-Bit Float Motor:** 1528 dB teorik dinamik tavan sunan 32-bit kayan nokta mimarisi tüm DSP zincirinde aktiftir.
• **64-Bit Float Yol Haritası (Roadmap):** Gelecek nesil çift duyarlıklı (double precision) ses işleme çekirdeği mimari AR-GE planlamasına alınmıştır.

**1.0'dan 8.1 Surround'a (+1 LFE) Hoparlör Mimarisi:**
Sistem; taşınabilir **1.0 Mono** ve **2.0 / 2.1 Hi-Fi** sistemlerden, araç içi **4.1 Quadraphonic**, ev sineması **5.1 / 7.1** ve profesyonel **8.1 çok kanallı** stüdyo düzenlerine kadar tüm ses hatlarını akıllı yönlendirme matrisiyle kontrol eder. Sistemdeki **(+1)** mimarisi, bağımsız aktif subwoofer ve LFE bas frekanslarını faz hizalamasıyla yönetir.

**Tescilli Ses Donanımı ve Güç Katı (ADR-038):**
• **USB Ses İşlemcisi:** **XMOS XU316** çok çekirdekli USB Audio Class 2.0 denetleyicisi.
• **DAC Dönüştürücü:** 8 kanallı **Texas Instruments PCM3168A** (24-bit 192kHz, 112dB SNR).
• **Analog Güç Amfisi:** Kanal başına **100W @ 8Ω Class AB** analog amplifikasyon devresi; >0.5V DC offset röle koruması ile hoparlörleri korur.

---
[✅ BAŞARI KUTUSU / SUCCESS BOX - RESİM 2 STİLİ]
✅ Ses Sadakati Doğrulaması: Neva Engine; Steinberg ASIO ile <10ms, WASAPI Exclusive ile <15ms ultra düşük gecikme sunar. Sinyal yolunda sıfır yeniden örnekleme (bit-perfect) garantilenmiştir.
```

---

## 📄 SAYFA 6: 1.2 TEMEL ÖZELLİKLER (MADDELER 1 – 6)

*(InDesign Sayfa 6: Sol Sayfa. Temel yeteneklerin ilk 6 maddesini eksiksiz kapsar.)*

```markdown
1.2  Temel Özellikler ve Sistem Yetenekleri

**CoreMusic**; sıradan bir medya oynatıcısının sınırlarını aşarak, dijital müzik yönetimini donanım seviyesinde ses sadakati, yapay zekâ destekli estetik arayüz ve bağımsız veri mülkiyeti ile birleştiren kurumsal bir ekosistemdir. Sistemin sunduğu temel yetenekler 10 ana başlık altında toplanmıştır:

1. Hibrit Çalışma Mimarisi (Online Cloud & Offline-First)
• **Kesintisiz Yerel Akış:** İnternet bağlantısının koptuğu tünellerde, otoparklarda veya kırsal yollarda müzik tek bir milisaniye dahi duraksamadan yerel SSD önbelleğinden çalmaya devam eder.
• **Çift Yönlü Bulut Eşitlemesi:** İnternet erişimi sağlandığı anda çalma sıraları, kişisel listeler ve dinleme geçmişi tüm cihazlarla sessizce senkronize edilir.
• **Kullanıcı Veri Mülkiyeti:** Kiralama tuzaklarını ortadan kaldırarak kullanıcının sahip olduğu kayıpsız koleksiyonun (**FLAC, WAV, MP3**) bağımsız ve kalıcı dijital varlık olarak korunmasını garanti eder.

2. Audio DSP Engine & Canlı Mekân Akustiği
• **Gerçek Zamanlı C++20 Çekirdeği:** Sıfır bellek tahsisi (**Zero-Allocation**) ve kilitlenmesiz (**Lock-Free**) kuyruklarla çalışan tescilli **Neva Engine**.
• **Canlı Mekân Modellemesi:** Dinleyiciye odanın değil, bizzat seslendirilen ortamın akustiğini yaşatan **Düğün Salonu**, **Konser Alanı & Arena**, **Canlı Stüdyo** ve **Kulüp** psikoakustik simülasyonları.
• **31-Band Parametrik EQ:** 20Hz – 20kHz aralığında 1/3 oktav stüdyo seviyesi profesyonel filtreleme.
• **Distorsiyonsuz Yüksek Güç:** Ses sonuna kadar açılsa dahi dijital çatlamayı engelleyen **True Peak Brickwall Limiter** (**THD+N <%0.01**, **SNR >100dB**).

3. Ses İşleme Hassasiyeti & Gelecek Yol Haritası
• **Aktif 32-Bit Float Motoru:** 1528 dB teorik dinamik tavan sunan 32-bit kayan nokta mimarisi tüm dahili ses işleme hattında anlık olarak aktiftir.
• **64-Bit Float Yol Haritası (Roadmap):** Çift duyarlıklı (double precision) gelecek nesil ses işleme çekirdeği mimari AR-GE planlamasına alınmıştır.

4. 1.0'dan 8.1 Surround'a (+1 LFE) Hoparlör Matrisi
• **Esnek Kanal Mimarisi:** Taşınabilir **1.0 Mono**, masaüstü **2.0 / 2.1 Hi-Fi**, araç içi **4.1 Quadraphonic**, ev sineması **5.1 / 7.1** ve stüdyo referansı **8.1 çok kanallı** çıkış desteği.
• **(+1) Aktif Subwoofer Yönetimi:** Bağımsız LFE bas frekans süzme ve faz hizalaması ile kristal netliğinde dinamik vuruşlar.

5. Büyüleyici Canlı Temalar & AI Destekli Theme Maker
• **Ambient Aura & Canlı Dinamik Işıklandırma:** Çalan albüm kapağının ve müziğin enerjisine göre gerçek zamanlı nefes alan akıcı **Glassmorphism** arayüzü.
• **Yapay Zekâ ile Tema Tasarımı:** Kullanıcıların doğal dille komut vererek (*"80'ler retro neon"* veya *"Gece mavisi minimalist akustik oda"*) **aşık olacakları güzellikteki kişisel temaları** tek tıkla üretebilmesini sağlayan dahili **Theme Maker Tool**.

6. Çapraz Cihaz Ekosistemi & Kesintisiz Geçiş (Handoff)
• **Tüm Ekranlarda Tek Deneyim:** Mobil, tablet, kişisel bilgisayar (PC), akıllı TV, araç içi bilgi-eğlence (`car.coremusic.net`) ve ev medya merkezinde (`home.coremusic.net` - RPi5) tam senkronize çalışma.
• **Çok Odalı Ses (Multi-Room Audio):** WebRTC ve WebSocket altyapısıyla evin farklı odalarındaki hoparlörlere sıfıra yakın faz gecikmesiyle eşzamanlı ses dağıtımı.
```

---

## 📄 SAYFA 7: 1.2 TEMEL ÖZELLİKLER (MADDELER 7 & 8: OTONOM İNDİRME, AI DÖNGÜSÜ & MEDYA DEPOSU)

*(InDesign Sayfa 7: Sağ Sayfa. Nova Search, Deemix, YouTube, USB, CD Yazma ve AI Dinleme Alışkanlığı mimarisi.)*

```markdown
1.2  Temel Özellikler (Devam) — Otonom Medya Edinimi & Yapay Zekâ

7. Merkezi Medya Depolama (`media.coremusic.net`), Çok Kaynaklı Otonom İndirme & Dışa Aktarım
• **Merkezi Medya Deposu (`media.coremusic.net`):** Normal kullanıcı veya admin ayrımı olmaksızın; kullanıcıların müzikal zevkine, arama geçmişine ve taleplerine göre belirlediği sanatçı bazlı veya tür bazlı tüm parçalar sistem tarafından otomatik olarak çekilerek merkezi depolama sunucusuna (`media.coremusic.net`) indirilir ve ilişkisel veritabanına işlenir.
• **Nova Search Engine & Modüler Downloader Sürücü Mimarisi (Driver / Adapter Pattern):** YouTube ve YouTube Music aramaları, doğrudan tescilli **Nova Search Engine** API arama motoru üzerinden yürütülür. Kodlama katmanında her indirme kaynağının sürücüsü birbirinden bağımsız modüller ve sınıflar (classes) olarak yazılır ve sisteme `include` edilir:
  - **`NovaSearchEngine` / `NovaSearchAdapter`:** YouTube ve YouTube Music API üzerinden parça, albüm, sanatçı, canlı konser ve remix aramalarını yürüten, en doğru akış kimliğini (stream ID / URL) yakalayan tescilli arama sürücüsü.
  - **`DeezerDownloader` (Deemix Çekirdeği):** Açık kaynak GitHub `deemix` mimarisiyle geliştirilen; doğrudan Deezer API akışları üzerinden tam stüdyo kalitesinde kayıpsız **FLAC (16/24/32-bit Float, 1411–9216 kbps)**, stüdyo referansı **32-bit Float WAV** ve **MP3 (320 kbps)** parçaları indiren, 32-bit hassasiyetinde kodlayan ve etiketleyen bağımsız indirici sınıfı.
  - **`YouTubeDownloader` / `YouTubeMusicDownloader`:** Nova Search Engine'den gelen link ve ID'leri alarak canlı konser performanslarını, akustik kayıtları ve **MP4 (1080p/4K)** video klipleri ses/video ayrıştırmasıyla otonom indiren bağımsız sürücü modülü.
  - **`DownloaderInterface`:** Tüm indirici sürücülerin ortak bir sözleşmeye bağlı kalmasını sağlayarak sisteme gelecekte yeni müzik platformlarının tek satır kodla tak-çıkar (pluggable) eklenebilmesini garanti eder.
• **Admin Paneli & Zamanlanmış Otomasyon (Cron / Queue Worker):** Sistem yöneticisi admin konsolundan sanatçı, tür veya albüm bazlı zamanlanmış indirme kuyrukları oluşturabilir; 32-bit ses kodlama kalitesini, saatlik/gecelik kotaları ve bant genişliği limitlerini merkezi olarak yönetir (`download.coremusic.net`).
• **Çoklu Dışa Aktarma (Yerel Depolama, Kişisel Bulut, USB Bellek & CD Yazma):** Kullanıcılar merkezi depodaki parçaları yalnızca çevrim içi akışla dinlemekle kalmaz, diledikleri fiziksel ve dijital ortama özgürce aktarabilirler:
  - **Yerel Depolama (Local Storage / Cihaz Hafızası):** İnternet bağlantısı olmasa dahi cihazın kendi SSD/bellek alanına doğrudan kayıpsız indirme.
  - **Kişisel Bulut Deposu:** Kullanıcının kendi özel bulut alanına otomatik senkronizasyon.
  - **Tek Tıkla USB / Harici Disk Aktarımı:** Araç teypleri ve harici amfilerle %100 uyumlu hiyerarşik klasör yapısında (`Sanatçı / Albüm / Şarkı`), gömülü ID3v2 albüm kapakları ve Türkçe karakter normalizasyonuyla FAT32/exFAT/NTFS disklere aktarma.
  - **Optik CD / DVD Yazma (Audio CD & MP3 CD Burning):** Klasik müzik setleri ve eski model araç CD çalarları için müzikleri doğrudan optik disklere (CD-R/RW) yazdırma ve disk formatlama desteği.

8. Yapay Zekâ (`coremusic_ai`), Dinleme Alışkanlığı Modellemesi & Otonom Arşivleme Döngüsü
• **Onboarding & Editoryal Müzik Zevki Profilleme:** Kullanıcı ilk hesap açarken sevdiği müzik türlerini, favori sanatçılarını ve dinleme tercihlerini görsel bir küratörlük arayüzüyle belirler; aynı zamanda editörler admin panelinden öne çıkan sanatçıları ve editoryal listeleri sisteme tanımlar. Bu veriler AI için temel eğitim çekirdeğini oluşturur.
• **Dinleme Alışkanlıklarıyla Eğitilen AI Modeli:** Kullanıcının dinleme geçmişi, şarkı atlama (skip), tekrar dinleme (replay), günün hangi saatlerinde hangi parçaları tercih ettiği ile şarkıların tempo (BPM), müzikal ton (Key) ve spektral enerji yoğunluğunu eşleştiren derin öğrenme modeli.
• **Otonom Arama, Link Yakalama ve Otomatik İndirme Döngüsü:** AI, kullanıcının zevkine uygun yeni keşif parçalarını ve kütüphanedeki eksik albümleri otomatik tespit eder; arama API'si üzerinden doğru linki yakalar, Deemix (FLAC 32-bit) veya YouTube Music motoruna iletir; parçaları arka planda kullanıcı müdahalesine gerek kalmadan `media.coremusic.net` merkezi deposuna ve kullanıcının yerel SSD önbelleğine ekler.
• **Adaptif Akustik DSP & Cihaz Eşleşmesi:** Kulaklık, araç amfisi veya stüdyo monitörü bağlandığında ortama ve müzik türüne en uygun parametrik EQ eğrilerini ve akustik mekan simülasyonunu otomatik olarak önerir.
```

---

## 📄 SAYFA 8: 1.2 TEMEL ÖZELLİKLER (MADDELER 9 & 10) + TEKNİK ÖZELLİKLER MATRİSİ

*(InDesign Sayfa 8: Sol Sayfa. Maddeler 9-10, kurumsal teknik özellik tablosu ve alt uyarı kutusuyla sayfayı %100 doldurur.)*

```markdown
1.2  Temel Özellikler (Son) — Donanım, Güvenlik & Sistem Parametreleri

9. Donanım ve Düşük Gecikmeli Sürücü Bütünleşmesi
• **Ultra Düşük Gecikme:** Windows ortamında Steinberg ASIO ile **<10ms**, WASAPI Exclusive modu ile **<15ms** stüdyo tepki hızı.
• **Tescilli Donanım Desteği:** Çok çekirdekli **XMOS XU316** USB ses işlemcisi, 8 kanallı **TI PCM3168A (112dB SNR)** DAC ve kanal başına **100W Class AB** analog amplifikatör kartı entegrasyonu.

10. Kurumsal Güvenlik ve Sıfır Framework Performansı
• **Sıfır Çerçeve Hızı:** Ağır framework'lerin (React/Vue) getirdiği sanal DOM gecikmeleri olmadan doğrudan **Vanilla JS ES6+** ve 9 katmanlı **ITCSS** ile her cihazda 60 FPS akıcılık.
• **Kaya Gibi Güvenli Mimari:** **NIST SP 800-38D AES-256-GCM Credential Vault**, Argon2id parola güvenliği, dinamik CSP nonce koruması ve **18 bağımsız BCNF veritabanı**.

---

### 📊 BÖLÜM 1 TEKNİK ÖZELLİKLER MATRİSİ (SPEC TABLE — RESİM 2 & 4 STİLİ)

| Kategori | Temel Standart & Mimari | Teknik Değer & Sağlanan Çözüm |
| :--- | :--- | :--- |
| **Ses İşleme Hassasiyeti** | 32-Bit Float Aktif / 64-Bit Roadmap | Sıfır dahili clipping, 1528 dB teorik dinamik tavan |
| **Gecikme Süresi (Latency)**| Steinberg ASIO SDK 2.3.4 / WASAPI Excl. | <10ms (ASIO) / <15ms (WASAPI) profesyonel tepki |
| **Distorsiyon & Gürültü** | THD+N <%0.01 @ 1kHz, SNR >100dB | True Peak Limiter ile yüksek seste sıfır çatlama |
| **Akustik Mekân DSP** | 3D Psikoakustik Reverb Simülatörü | Konser Alanı, Düğün Salonu, Canlı Stüdyo, Kulüp |
| **Hoparlör Matrisi** | 1.0, 2.0, 2.1, 4.1, 5.1, 7.1 ve 8.1 | (+1 LFE) Bağımsız subwoofer faz ve bas yönetimi |
| **Görsel Arayüz (UI)** | Ambient Aura & AI Theme Maker | Glassmorphism canlı temalar, doğal dil ile tema üretimi |
| **Merkezi Medya Deposu** | `media.coremusic.net` | Sanatçı/tür bazlı otomatik merkezi indirme ve kataloglama |
| **Arama Motoru** | Nova Search Engine API | YouTube & YouTube Music için tescilli hızlı arama sürücüsü |
| **İndirme Sürücüleri** | `DeezerDownloader` + `YouTubeDownloader`| FLAC (16/24/32-Bit Float), 32-Bit WAV, MP3 ve MP4 4K video |
| **Yapay Zekâ (AI)** | Dinleme Alışkanlığı Modellemesi | Onboarding zevk analizi, otonom arama ve otomatik indirme |
| **Çoklu Dışa Aktarma** | Yerel Depo, Kişisel Bulut, USB & CD | Cihaz hafızası, bulut sync, teyp uyumlu USB ve optik CD yazma |
| **Yazılım & Veritabanı** | PHP 8.4 Strict Types + 18 BCNF MySQL | Sıfır ORM, ham PDO, UUID v7 zaman indeksli anahtarlar |

---
[⚠️ UYARI KUTUSU / WARNING BOX - RESİM 2 & 4 STİLİ]
⚠️ Mimari Kuralı: Veritabanı sorgularında ORM (Doctrine/Eloquent) kullanımı ve `SELECT *` ifadeleri kesinlikle yasaktır. Tüm veri erişimleri L0 katmanında ham PDO ve parametreli sorgularla yürütülür.
```

---

## 🛠️ INDESIGN İÇİN BİREBİR MİZANPAJ, STİL VE KUTU REHBERİ

### 1. Paragraf Stilleri (Paragraph Styles) Tablosu

| Stil Adı | Yazı Tipi (Font Family) | Boyut (Font Size) | Satır Aralığı (Leading) | Renk (Color) | Hizalama & Boşluk |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **`Chapter_Number`** | Montserrat Bold | 38–42 pt | Otomatik | Kurumsal Pembe/Mor (`#D946EF` veya `#8B5CF6`) | Sola Dayalı |
| **`Chapter_Title`** | Montserrat Bold | 18–20 pt | 22 pt | Koyu Gri (`#0F172A`) | Sola Dayalı |
| **`Section_Heading`**| Montserrat Bold | 13–14 pt | 17 pt | Kurumsal İndigo (`#4F46E5`) | Öncesi: 4 mm, Sonrası: 2 mm |
| **`Subsection_Title`**| Inter SemiBold | 10.5 pt | 14 pt | Koyu Gri (`#1E293B`) | Öncesi: 3 mm, Sonrası: 1 mm |
| **`Body_Text`** | Inter Regular / Minion Pro | 9.5 pt | 13.5 pt | Gövde Grisi (`#334155`) | İki Yana Yasla (Justify Left) |
| **`Bullet_Item`** | Inter Regular | 9 pt | 13 pt | Gövde Grisi (`#334155`) | Sol Girinti: 4 mm, İlk Satır: -4 mm |
| **`Quote_Text`** | Playfair Display / Cormorant Italic | 13–15 pt | 18 pt | Canlı Pembe/Mor (`#C026D3`) | Ortalanmış, Öncesi/Sonrası: 5 mm |
| **`Table_Cell`** | Inter Regular | 8.5 pt | 11.5 pt | Koyu Gri (`#1E293B`) | Üst/Alt Hücre Boşluğu: 1.5 mm |
| **`Table_Header`** | Inter SemiBold | 9 pt | 12 pt | Beyaz (`#FFFFFF`) | Zemin: Kurumsal İndigo (`#312E81`) |

### 2. İkonlu Not Kutuları (Callout Boxes) Nesne Stilleri (Object Styles)

InDesign'da `Pencere > Stiller > Nesne Stilleri` (Object Styles) panelinde 5 adet kutu stili oluşturun:

1. **`Box_Information` (Mavi / Bilgi):**
   - Dolgu (Fill): `%10` Açık Mavi (`#EFF6FF`)
   - Kontur (Stroke): `1 pt`, Mavi (`#3B82F6`), Sol kenar `3 pt` kalın
   - Köşe Yuvarlama (Corner Radius): `2 mm`
2. **`Box_Success` (Yeşil / Başarı):**
   - Dolgu: `%10` Açık Yeşil (`#F0FDF4`)
   - Kontur: `1 pt`, Yeşil (`#22C55E`), Sol kenar `3 pt`
3. **`Box_Warning` (Sarı / Uyarı):**
   - Dolgu: `%10` Açık Sarı (`#FEFCE8`)
   - Kontur: `1 pt`, Amber (`#EAB308`), Sol kenar `3 pt`
4. **`Box_Critical` (Kırmızı / Kritik):**
   - Dolgu: `%10` Açık Kırmızı (`#FEF2F2`)
   - Kontur: `1 pt`, Kırmızı (`#EF4444`), Sol kenar `3 pt`
5. **`Box_Tip` (Mor / İpucu):**
   - Dolgu: `%10` Açık Mor (`#FAF5FF`)
   - Kontur: `1 pt`, Mor (`#A855F7`), Sol kenar `3 pt`
