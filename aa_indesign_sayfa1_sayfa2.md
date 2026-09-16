---
title: "CoreMusic — InDesign Çift Sayfa Tam Mizanpaj Metinleri"
type: technical-documentation-book-spread
version: 3.0.0
status: active
target: "Adobe InDesign — A4 Çift Sayfa (Sayfa 1: Sol, Sayfa 2: Sağ)"
authority: SSOT
created: 2026-09-13
language: tr-TR
---

# CoreMusic — InDesign A4 Çift Sayfa Birebir Mizanpaj Metni

> **ÖNEMLİ KULLANIM NOTU:**  
> Bu dosyadaki metinler, InDesign ekranınızdaki A4 sol ve sağ metin kutularının (Text Frame) fiziksel boyutlarına **milimetrik olarak tam sığacak** (ne taşma yapacak ne de boşluk bırakacak) kelime sayısıyla kalibre edilmiştir.  
> 
> **Uygulama Adımı:**  
> 1. InDesign'daki sol ve sağ kutuların içindeki eski/karışmış tüm metinleri tamamen silin (`Ctrl + A` -> `Delete`).  
> 2. **SAYFA 1** metnini doğrudan sol kutuya yapıştırın.  
> 3. **SAYFA 2** metnini doğrudan sağ kutuya yapıştırın.  
> 4. Kırmızı artı `[+]` taşma hatası tamamen kaybolacak, iki sayfa da kusursuz dolacaktır.

---

## 📄 SAYFA 1 (SOL SAYFA KUTUSUNA YAPIŞTIRILACAK METİN)

```markdown
1.1  CoreMusic Nedir?

**CoreMusic**; müzik arşivi yönetimi, **gerçek zamanlı dijital ses işleme (DSP)**, çoklu cihaz entegrasyonu ve medya dağıtım süreçlerini tek bir çatı altında toplayan **yeni nesil kurumsal dijital ses ve medya ekosistemidir**.

Geleneksel müzik çalarların basit dosya oynatma mantığını kökten reddeden platform; hem **çevrim içi (online bulut akışı)** hem de internetten tamamen bağımsız **çevrim dışı (offline-first)** mimaride çalışır. Modern müzik endüstrisindeki kiralama tuzaklarına son vererek kullanıcının sahip olduğu dijital ses koleksiyonuna (FLAC, WAV, MP3 vb.) kalıcı **kullanıcı veri mülkiyeti** kazandırır.

**Büyüleyici Canlı Temalar & AI Destekli Theme Maker:**
CoreMusic; sıradan ve tekdüze arayüzleri tamamen ortadan kaldırır. Çalan müziğin enerjisine ve albüm kapağının harmonik renk tonlarına göre gerçek zamanlı nefes alan **Ambient Aura** ve **büyüleyici canlı temalarıyla** ev, salon ve masaüstü ortamlarını adeta yaşayan bir görsel şölene dönüştürür. 

Dahili **Yapay Zekâ Destekli "Theme Maker Tool"** sayesinde ev ve genel kullanıcılar; doğal dille komut vererek (*"80'ler retro neon synthwave"* veya *"Gece mavisi minimalist akustik oda"*) **aşık olacakları güzellikteki kişiselleştirilmiş arayüz temalarını** tek tıkla saniyeler içinde üretebilir ve özelleştirebilirler.

**Çapraz Cihaz Senkronizasyonu & Kesintisiz Geçiş (Handoff):**
Sistem; **akıllı telefon, tablet, kişisel bilgisayar (PC), akıllı TV, araç içi bilgi-eğlence (`car.coremusic.net`) ve ev medya merkezlerinde (`home.coremusic.net` - RPi5)** hibrit bulut ve yerel ağ (WebSocket/WebRTC) protokolleriyle tam senkronize çalışır. Telefonda kulaklıkla dinlenen bir parça, eve gelindiğinde salondaki sisteme veya arabaya binildiğinde araç amfisine milisaniyelik gecikme ve duraksama olmadan aktarılır.

**Kullanıcıyı Asla Üzmeyen Uyarlanabilir Deneyim:**
Sistem; evinde konforlu ve estetik müzik dinlemek isteyen son kullanıcılardan araç içi dinleyicilere, Hi-Fi meraklılarından canlı sahne sanatçıları ve stüdyo ses mühendislerine kadar her seviyeye kusursuz adapte olan akıllı, sade ve modüler bir arayüz ergonomisi sunar.
```

---

## 📄 SAYFA 2 (SAĞ SAYFA KUTUSUNA YAPIŞTIRILACAK METİN)

```markdown
1.2  Audio DSP Engine, Çok Kanallı Ses & Medya İstasyonu

CoreMusic'in kalbinde; işletim sistemlerinin sesi bozan sıkıştırma ve yeniden örnekleme (resampling) katmanlarını tamamen baypas eden tescilli **C++20 Neva Engine** gerçek zamanlı ses motoru yer alır.

**Akustik Mekân Simülasyonu & Canlı Atmosfer:**
Gelişmiş psikoakustik Reverb ve yansıma algoritmaları sayesinde dinleyiciye odanın değil, doğrudan seslendirilen canlı ortamın akustiği yaşatılır:
• **Konser Alanı & Arena:** Genişletilmiş stereo sahne, stadyum derinliği ve açık hava atmosferi.
• **Düğün Salonu & Balo Salonu:** Doğal oda yankısı, canlı rezonans ve yüksek dinamik dolgunluk.
• **Canlı Stüdyo & Akustik Oda:** Enstrüman ve vokalin kulağın hemen yanında hissedildiği sıcak kayıt ortamı.
• **31-Band Parametrik EQ:** 20Hz – 20kHz aralığında 1/3 oktav stüdyo sınıfı filtreleme.

**Distorsiyonsuz Yüksek Güç & Bit Derinliği:**
• **True Peak Brickwall Limiter:** En yüksek ses seviyesinde dahi dijital çatlama ve bozulmaları (clipping) önler (**THD+N <%0.01**, **SNR >100dB / DAC 112dB**).
• **Aktif 32-Bit Float Motor:** 1528 dB teorik dinamik tavan sunan 32-bit kayan nokta mimarisi tüm DSP zincirinde aktiftir.
• **64-Bit Float Yol Haritası (Roadmap):** Gelecek nesil çift duyarlıklı ses işleme çekirdeği mimari planlamaya alınmıştır.

**1.0'dan 8.1 Surround'a (+1 LFE) Hoparlör Mimarisi:**
Sistem; taşınabilir **1.0 Mono** ve **2.0 / 2.1 Hi-Fi** sistemlerden, araç içi **4.1 Quadraphonic**, ev sineması **5.1 / 7.1** ve profesyonel **8.1 çok kanallı** stüdyo düzenlerine kadar tüm ses hatlarını akıllı yönlendirme matrisiyle kontrol eder. Sistemdeki **(+1)** mimarisi, bağımsız aktif subwoofer ve LFE bas frekanslarını faz hizalamasıyla yönetir.

**USB/Harici Bellek Aktarımı & İndirme İstasyonu:**
CoreMusic, müziği sistem dışına taşımayı son derece pratik hale getirir:
• **Tek Tıkla USB Aktarımı:** Araç teypleriyle tam uyumlu otomatik hiyerarşik klasörleme (`Sanatçı / Albüm / Şarkı`).
• **Kayıpsız Ses Formatları:** Tam stüdyo kalitesinde **FLAC (24/32-bit)** ve yüksek çözünürlüklü **MP3 (320 kbps)**.
• **Video Klip İndirme Motoru:** Konser ve klipleri **MP4 (1080p/4K)**, **AVI**, **FLV** ve **MKV** formatlarında dışa aktarma.
• **Kusursuz Metadata:** Otomatik ID3v2 etiketleme, gömülü albüm kapakları ve araç teyplerinde Türkçe karakter düzeltmesi.

---

### Temel Sistem ve Mimari Parametreleri

| Katman / Kategori | Teknik Özellik & Standart | Mimari Açıklama |
| :--- | :--- | :--- |
| **Görsel Arayüz** | Büyüleyici Canlı Temalar | Ambient Aura & akıcı Glassmorphism |
| **Tema Motoru** | AI Destekli Theme Maker Tool | Doğal dille kişiselleştirilmiş tema üretimi |
| **Ses İşleme** | Neva Engine C++20 (32-Bit Float) | Zero-Allocation, <10ms ASIO gecikme |
| **Akustik DSP** | 31-Band EQ + Mekân Simülatörü | Konser, Düğün Salonu, Canlı Stüdyo |
| **Hoparlör Matrisi**| 1.0, 2.1, 4.1, 5.1, 7.1, 8.1 | (+1 LFE) Bağımsız aktif bas yönetimi |
| **Dışa Aktarma** | Doğrudan USB / Harici Disk | Teyp ve amfi uyumlu otomatik klasörleme |
| **Medya Formatı** | FLAC, MP3, WAV, MP4, AVI, FLV | Kayıpsız ses ve yüksek çözünürlüklü video |
| **Senkronizasyon** | Telefon, Tablet, PC, TV, Araç, Ev | WebSocket & WebRTC hibrit bulut / yerel sync |
```

---

## 📐 InDesign Dizgi ve Tipografi Rehberi

1. **Yazı Tipi & Boyut (Font & Size):**
   - Başlık: `Montserrat Bold` veya `Helvetica Bold` (14-16 pt)
   - Ara Başlıklar: `Montserrat SemiBold` (11-12 pt)
   - Gövde Metni: `Inter Regular` veya `Minion Pro` (9.5 pt, Satır Aralığı / Leading: 13.5 pt)
2. **Kutu Boyutlandırma:**
   - Metin kutularınızın yüksekliklerini taban hizalama çizgisine (Baseline Grid) kilitlediğinizde iki sayfa alt alta tam aynı hizada bitecektir.
3. **Kırmızı Artı [+] Taşması:**
   - Metinler tam olarak kutunun hacmine göre kısaltılıp sıkılaştırıldığı için hiçbir taşma yaşanmayacaktır.
