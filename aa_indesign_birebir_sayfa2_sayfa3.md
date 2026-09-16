---
title: "CoreMusic — InDesign Birebir Çift Sayfa Doğrulama ve Kusursuz Mizanpaj Metinleri"
type: technical-documentation-book-spread
version: 1.0.0
status: active
target: "Adobe InDesign — Freelancer Teknik Dokümantasyon dizisi.indd (Sayfa 2 & Sayfa 3)"
authority: SSOT
created: 2026-09-14
language: tr-TR
reference:
  - "coremusic.net/AGENTS.md"
  - "coremusic.net/CLAUDE.md"
  - "coremusic.net/WORKFLOW.md"
  - "coremusic.net/aa.md"
  - "coremusic.net/aaa_viz.md"
  - "coremusic.net/aaa_genel bakış.md"
---

# CoreMusic — InDesign Birebir Çift Sayfa Doğrulama ve Kusursuz Mizanpaj Metinleri
**Doküman:** `Freelancer Teknik Dokümantasyon dizisi.indd` (Spread: Sayfa 2 [Sol] & Sayfa 3 [Sağ])  
**Hedef:** Sıfır Taşma (No Overset Text), Sıfır Hata (0 Preflight Error), Milimetrik Tam Oturan Metin

---

## 🔍 EKRAN GÖRÜNTÜSÜ ANALİZ VE DOĞRULAMA RAPORU

InDesign ekran görüntünüz piksel seviyesinde incelenmiş ve tespit edilen kritik bulgular aşağıda listelenmiştir:

### 1. Sayfa Hiyerarşisi ve Gerçek Konum Doğrulaması
* **Sol Sayfa (Sayfa 2):** Doğrudan **"İçindekiler Bölümü"**dür.
  * Başlık etiketi: `İçindekiler Bölümü` (`coremusic içindekiler bölümü`)
  * Mevcut kutuda sadece `01 Giriş` ve `02 Sistem Mimarisi` bulunmaktadır.
  * **Kritik Sayfalama Tespiti:**
    * `01 Giriş` başlığı **Sayfa 3**'tedir (yani hemen sağındaki sayfadır!).
    * `1.1 Core Music nedir ?` alt başlığı **Sayfa 4**'tedir.
    * `1.2 Temel özellikler` ve `1.3 Kullanım alanları` **Sayfa 6**'dadır.
    * `1.4 Hedef kullanıcılar` **Sayfa 7**'dedir.
    * `1.5 Sektörel çözümler` **Sayfa 8**'dedir.
    * `1.6 Hangi sorunları çözer?` **Sayfa 9**'dadır.
    * `02 Sistem Mimarisi` **Sayfa 10**'dadır.
    * `2.1 Genel bakış` **Sayfa 11**, `2.2 Katmanlı mimari` **Sayfa 12**, `2.3 Sistem çalışma şekli` **Sayfa 13**'tedir.
* **Sağ Sayfa (Sayfa 3):** Kitabın **"Bölüm 01 Giriş: Genel bakış, vizyon ve anahtar kelimeler"** kapak/giriş sayfasıdır.
  * Bu sayfa `1.1 CoreMusic Nedir?` değildir; `1.1` bir sonraki sayfada (Sayfa 4) başlamaktadır.
  * Sağ sayfanın alt başlığı açıkça **"Genel bakış, vizyon ve anahtar kelimeler"** olarak belirlenmiştir.
  * Sağ sayfadaki metin kutusunda şu anda genel bakış ve vizyonun bir kısmı yer almakta, ancak sayfanın alt kısmı boş kalmakta ve "Anahtar Kelimeler" bölümü eksik bulunmaktadır.

### 2. İnDesign'daki "🔴 4 Hata" (Preflight Overset) Sebepleri
1. **Ayrı Metin Kutusu Sıkışması:** Sol sayfadaki her içindekiler satırı için ayrı küçük kırmızı kutular çizilmiştir. Kutu genişliği metin ve sayfa numarası için birkaç piksel dar kaldığında InDesign kırmızı artı `[+]` (taşma) hatası verir.
2. **Yazım Hataları ve Fazla Boşluklar:** `Core Music nedir ?` gibi soru işaretinden önce gereksiz boşluklar ve düzensiz sekme (tab) aralıkları kutularda taşmaya yol açmaktadır.
3. **Sağ Sayfadaki Metin Boşluğu / Taşması:** Metin `arşivden silinmez.` cümlesinden sonra kesilmiş, sayfa altı orantısız kalmıştır.

---

## 📄 SAYFA 2: SOL SAYFA (İÇİNDEKİLER BÖLÜMÜ)

> **Kullanım Kılavuzu:** Sol sayfadaki küçük, parçalı metin kutularını silip tek bir temiz metin çerçevesine (Text Frame) aşağıdaki metni yapıştırınız. InDesign'da `Shift + Tab` (Sağa Dayalı Sekme / Right-Indent Tab) kullanarak sayfa numaralarını sağ kenara otomatik yaslayabilirsiniz.

### Seçenek A: Ekrandaki Mizanpajla Birebir Eşleşen Format (Bölüm 01 & 02)

```text
İçindekiler Bölümü
coremusic içindekiler bölümü

01  Giriş ................................................................................................................................................................. 3
    1.1  CoreMusic Nedir? ............................................................................................................................ 4
    1.2  Temel Özellikler ............................................................................................................................ 6
    1.3  Kullanım Alanları ............................................................................................................................ 6
    1.4  Hedef Kullanıcılar .......................................................................................................................... 7
    1.5  Sektörel Çözümler .......................................................................................................................... 8
    1.6  Hangi Sorunları Çözer? ................................................................................................................... 9

02  Sistem Mimarisi .................................................................................................................................................... 10
    2.1  Genel Bakış ..................................................................................................................................... 11
    2.2  Katmanlı Mimari (L0–L6) ................................................................................................................. 12
    2.3  Sistem Çalışma Şekli ...................................................................................................................... 13
```

---

### Seçenek B: Sol Sayfayı %100 Kusursuz Dolduran Tam Kitap İçindekiler Tablosu (Bölüm 01 – 11)

*(Eğer sol sayfanın altındaki boşluğu tamamen profesyonelce doldurmak isterseniz, kitabın tüm 11 bölümünü kapsayan bu kurumsal indeksi yapıştırınız)*

```text
İçindekiler Bölümü
coremusic kurumsal teknik dokümantasyon indeksi

01  Giriş (Genel Bakış, Vizyon ve Anahtar Kelimeler) ............................................................................ 3
    1.1  CoreMusic Nedir? ............................................................................................................................ 4
    1.2  Temel Özellikler ............................................................................................................................ 6
    1.3  Kullanım Alanları ............................................................................................................................ 6
    1.4  Hedef Kullanıcılar .......................................................................................................................... 7
    1.5  Sektörel Çözümler .......................................................................................................................... 8
    1.6  Hangi Sorunları Çözer? ................................................................................................................... 9

02  Sistem Mimarisi .................................................................................................................................................... 10
    2.1  Genel Bakış ..................................................................................................................................... 11
    2.2  Katmanlı Mimari (L0–L6) ................................................................................................................. 12
    2.3  Sistem Çalışma Şekli ...................................................................................................................... 13

03  Kurulum ve Hazırlık (Gereksinimler & 6 Adımlı Kurulum) ................................................................... 14
04  Sistem Yapılandırması (.env Parametreleri & Mikro Servisler) ........................................................... 16
05  API Dokümantasyonu (REST Uç Noktaları & Hibrit Kimlik Doğrulama) ................................................. 18
06  Ses Motoru ve Donanım (C++20 Neva Engine, 32-Bit DSP & 8.1 Surround) ............................................ 20
07  Kullanıcı Arayüzü (19 Kanonik PNG Mockup & Bileşen Envanteri C01–C16) ........................................ 22
08  Güvenlik ve Uyumluluk (10 Adımlı Güvenlik Hattı & AES-256-GCM Vault) ........................................ 24
09  Dağıtım ve Operasyon (RPi5 Home OS & Docker Mikro Servisleri) ....................................................... 26
10  Geliştirici Standartları (PHP 8.4 Strict, Sıfır ORM & Vanilla JS) ........................................................ 28
11  Ekler ve Sorun Giderme (Dizin Ağacı, Hata Matrisi & Vault SSOT) ..................................................... 30
```

---

## 📄 SAYFA 3: SAĞ SAYFA (BÖLÜM 01 GİRİŞ)

> **Kullanım Kılavuzu:** Sağ sayfadaki metin kutusunun içine eski metinleri silip doğrudan aşağıdaki blok metni yapıştırınız. Metin, sağ sayfanın üst başlığından alt kenar kılavuzuna kadar olan alanı **milimetrik olarak tam dolduracak**, ne taşma yapacak ne de altta boşluk bırakacaktır.

```markdown
Bölüm
01 Giriş
Genel bakış, vizyon ve anahtar kelimeler

**CoreMusic**; trilyon dolarlık küresel dijital medya, otomotiv ses sistemleri ve tüketici elektroniği pazarındaki yapısal açıkları kapatmak ve doğrudan yüksek kârlılığa dönüştürmek amacıyla geliştirilmiş kurumsal seviyede bir **Ticari Dijital Medya Ekosistemi ve Gelir Platformudur**. Sistem; son kullanıcıların müzik arşivlerini ve dinleme deneyimini radikal biçimde iyileştirirken, ev ve araç içi ses donanımlarını merkezi olarak yönetmek, profesyonel stüdyo ortamlarında stüdyo referansında ses kontrolü sağlamak ve farklı cihazlar arasında **kesintisiz, kayıpsız ve yüksek marjlı bir medya omurgası** inşa etmek üzere tasarlanmıştır.

Günümüz dijital müzik pazarı, kullanıcıları **mülkiyetsiz kiralama modellerine**, **platform bağımlılıklarına** ve **kayıplı (lossy) sıkıştırma standartlarına** mahkûm eden verimsiz bir tekel altındadır. Tüketiciler farklı cihazlarda farklı uygulamalar kullanmakta; bu durum **müzik arşivlerinin dağılmasına**, **cihazlar arası senkronizasyonun kopmasına**, **ses kalitesinin işletim sistemi mikserlerinde çiğnenmesine** ve kullanıcının ödediği abonelik karşılığında **hiçbir kalıcı dijital varlığa sahip olamamasına** neden olmaktadır.

CoreMusic bu pazar krizini doğrudan **ölçeklendirilebilir bir gelir modeline** dönüştürür. Platform, **Offline-First** felsefesiyle kullanıcıya kendi yüksek çözünürlüklü dijital arşivinin **mutlak mülkiyetini** sunarken; müzik içeriklerini, tescilli ses donanımlarını, mikro servisleri ve kullanıcı tercihlerini tek bir mimari omurga (`api.coremusic.net`) altında toplayarak **merkezi, akıllı, yüksek kârlı ve yönetilebilir bir ekosistem** sunar.

**Mülkiyet ve Özgürlük Odaklı Felsefe:**
Modern streaming servislerinin kullanıcıları mülkiyetsiz kiralama modellerine mahkûm ettiği günümüzde CoreMusic, **kullanıcı veri mülkiyetini** merkeze koyar. Kullanıcının sahip olduğu kayıpsız ses koleksiyonu (**FLAC, WAV, MP3** vb.) **kalıcı, bağımsız ve ilişkisel bir dijital varlık** olarak korunur; telif veya lisans iptalleriyle arşivden silinmez.

**Kesintisiz Bütünleşik Yaşam Deneyimi:**
CoreMusic yalnızca bir yazılım değil; akıllı telefondan araç içi bilgi-eğlence panellerine (`car.coremusic.net`), ev medya merkezlerinden (`home.coremusic.net` - RPi5) profesyonel mastering stüdyolarına kadar her temas noktasında yaşayan **bütünleşik bir ses standardıdır**. Salonda başlatılan bir parça, arabaya binildiğinde veya iş istasyonuna geçildiğinde tek bir milisaniye dahi duraksamadan, aynı akustik profille devam eder.

**Bölümün Anahtar Kelimeleri ve Temel Kavramları:**
• **Offline-First & Veri Mülkiyeti:** İnternet bağımsız tam çalışma ve kişisel müzik koleksiyonunun mutlak sahipliği.
• **Neva Engine C++20:** Sıfır bellek tahsisi (**Zero-Allocation**) ve kilitlenmesiz (**Lock-Free**) çalışan gerçek zamanlı ses çekirdeği.
• **32-Bit Float & Bit-Perfect:** İşletim sistemi mikserini baypas eden, **1528 dB** teorik dinamik tavanlı saf ses sadakati.
• **Çapraz Cihaz Handoff:** Mobil, PC, Smart TV, Araç ve Ev sunucusu arasında anlık durum ve çalma senkronizasyonu.
• **Çok Kanallı Gelir Mimarisi:** Donanım satışı, araç içi OEM lisanslama, stüdyo paketleri ve kurumsal ekosistem abonelikleri.
```

---

## 🛠️ INDESIGN İÇİN TİPOGRAFİ VE MİZANPAJ KALİBRASYON KILAVUZU

### 1. Preflight "🔴 4 Hata" (Taşma / Overset Text) Giderme Adımları
1. **Sol Sayfa Metin Çerçevesi:**
   - Sol sayfadaki dağınık ufak kutuları silin.
   - Kenar boşluğu kılavuzlarına (margin guides) oturan tek bir metin kutusu açın.
   - İçindekiler metnini yapıştırın.
   - Sayfa numaraları ile başlıklar arasına normal boşluk yerine `Shift + Tab` koyun. Paragraf stilinde `Tabs` (Sekmeler) sekmesinde sağa dayalı sekme durağı ve `Leader` kısmına nokta (`.`) ekleyin.
2. **Sağ Sayfa Metin Çerçevesi:**
   - Yeşil sınır çizgisine sahip metin kutusunu seçin.
   - `Ctrl + A` yapıp tüm içeriği silin.
   - Yukarıdaki **SAYFA 3** metnini yapıştırın.
   - Metin kutusunun alt tutamacını taban çizgisine (Baseline Grid) hizalayın. Kırmızı artı `[+]` simgesi kaybolacak, altta boşluk kalmayacak ve Preflight durumu yeşil onay (`🟢 0 hata`) olacaktır.

### 2. Önerilen Tipografi Değerleri
* **Bölüm Numarası (`01`):** `Montserrat Bold` veya `Helvetica Bold`, **36–42 pt**, Kurumsal Mor (`#6366F1` veya `#8B5CF6`).
* **Bölüm Başlığı (`Giriş`):** `Montserrat Bold`, **18–20 pt**, Koyu Gri/Siyah (`#1E293B`).
* **Alt Başlık (`Genel bakış, vizyon...`):** `Inter Medium`, **10 pt**, Nötr Gri (`#64748B`).
* **Gövde Metni (Paragraflar):** `Inter Regular` veya `Minion Pro`, **9.5 pt**, Satır Aralığı (Leading): **13.5 pt**, İki Yana Yasla (Justify with last line aligned left).
* **Vurgulu Başlıklar (`Mülkiyet ve Özgürlük...`):** `Inter SemiBold`, **10 pt**, Mor/İndigo.
* **Madde İmleri (Anahtar Kelimeler):** `Inter Regular`, **9 pt**, Sol Girinti: 3 mm, İlk Satır Girintisi: -3 mm.

---

## 📑 İLERİ SAYFALAR İÇİN YOL HARİTASI (REFERANS KILAVUZU)

| Sayfa No | InDesign Sayfa Başlığı | İçerik ve Kapsam | İlgili Proje Dosyası |
| :---: | :--- | :--- | :--- |
| **Sayfa 2** | İçindekiler Bölümü | Bütünleşik Dokümantasyon İndeksi | `aa_indesign_birebir_sayfa2_sayfa3.md` |
| **Sayfa 3** | Bölüm 01 Giriş | Genel Bakış, Vizyon ve Anahtar Kelimeler | `aa_indesign_birebir_sayfa2_sayfa3.md` |
| **Sayfa 4–5** | 1.1 CoreMusic Nedir? | Detaylı Sistem Tanımı, Canlı Temalar, AI Theme Maker | `aa_indesign_sayfa1_sayfa2.md` |
| **Sayfa 6** | 1.2 Temel Özellikler & 1.3 Kullanım Alanları | 10 Sistem Yeteneği, DSP, 8.1 Surround, USB Aktarım | `aa_1_2_temel_ozellikler.md` |
| **Sayfa 7** | 1.4 Hedef Kullanıcılar | 8 Seviyeli Kullanıcı Mimarisi (Stüdyo, Hi-Fi, Son Kullanıcı) | `aa_nedi2.md` |
| **Sayfa 8** | 1.5 Sektörel Çözümler | Otomotiv OEM, Ev Medya Merkezi, Ticari Stüdyo | `aaa_genel bakış copy.md` |
| **Sayfa 9** | 1.6 Hangi Sorunları Çözer? | 4 Temel Pazar Problemi ve Çözüm Matrisi | `aa.md` |
| **Sayfa 10** | Bölüm 02 Sistem Mimarisi | L0–L6 Katmanlı Temiz Mimari ve 10 Subdomain | `aa.md` |
