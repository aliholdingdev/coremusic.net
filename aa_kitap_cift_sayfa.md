---
title: "CoreMusic — Teknik Dokümantasyon Kitabı (Çift Sayfa Mizanpaj Rehberi)"
type: technical-documentation-book-spread
version: 2.0.0
status: active
target: "Adobe InDesign — Çift Sayfa (Spread: Sol Sayfa + Sağ Sayfa)"
authority: SSOT
created: 2026-09-13
language: tr-TR
---

# CoreMusic — Freelancer Technical Documentation (Çift Sayfa Mizanpajı)

> **InDesign Mizanpaj Rehberi:** Ekran görüntünüzde sol sayfa ile sağ sayfa yan yana bir **çift sayfa (spread)** oluşturmaktadır. Şu an sağ sayfanızın yaklaşık %70'i boştur. Aşağıdaki içerik, **Sol Sayfa** ve **Sağ Sayfa** olarak iki bağımsız veya birbirine bağlı (threaded) metin kutusuna tam oturacak, hiçbir taşma yapmayacak ve kitabınızı dünya standardında profesyonel bir basılı esere dönüştürecek şekilde yapılandırılmıştır.

---

# 📖 SOL SAYFA (LEFT PAGE): BÖLÜM 1.1 — SİSTEM TANIMI & VİZYON

*(Sol sayfadaki kırmızı metin kutusuna eskileri silip doğrudan bu metni yapıştırınız.)*

```markdown
1.1  CoreMusic Nedir?

**CoreMusic**; müzik arşivi yönetimi, **gerçek zamanlı dijital ses işleme (DSP)**, çoklu cihaz ekosistemi ve medya dağıtım süreçlerini tek bir çatı altında birleştiren **yeni nesil kurumsal dijital ses ve medya ekosistemidir**.

Klasik müzik çalarların basit dosya oynatma yaklaşımını kökten reddeden platform; hem **çevrim içi (online bulut akışı)** hem de internetten tamamen bağımsız **çevrim dışı (offline-first)** mimaride çalışarak kullanıcının tüm kişisel müzik arşivini, ses donanımlarını ve kişisel akustik tercihlerini uçtan uca yönetmesini sağlar.

**Mülkiyet ve Özgürlük Odaklı Felsefe:**
Modern streaming servislerinin kullanıcıları mülkiyetsiz kiralama modellerine mahkûm ettiği günümüzde CoreMusic; **kullanıcı veri mülkiyetini** merkeze koyar. Kullanıcının sahip olduğu kayıpsız ses koleksiyonu (FLAC, WAV, MP3 vb.) kalıcı, bağımsız ve ilişkisel bir dijital varlık olarak korunur; telif veya lisans iptalleriyle arşivden silinmez.

**Büyüleyici Canlı Temalar & AI Destekli Theme Maker:**
CoreMusic; sıradan, donuk ve tekdüze arayüzleri tamamen ortadan kaldırır. Çalan parçanın enerjisine ve albüm kapağının harmonik renklerine göre nefes alan **Ambient Aura** ve **canlı dinamik temalarıyla** ev, salon ve masaüstü ortamlarını büyüleyici bir görsel şölene dönüştürür. Dahili **Yapay Zekâ Destekli "Theme Maker Tool"** sayesinde kullanıcılar, doğal dille komut vererek (*"80'ler retro neon synthwave"* veya *"Gece mavisi minimalist akustik oda"*) **aşık olacakları güzellikteki kişiselleştirilmiş arayüz temalarını** saniyeler içinde üretebilirler.

**Çapraz Cihaz Senkronizasyonu & Kesintisiz Geçiş (Handoff):**
Sistem; **akıllı telefon, tablet, kişisel bilgisayar (PC), akıllı TV, araç içi bilgi-eğlence (`car.coremusic.net`) ve ev medya merkezlerinde (`home.coremusic.net` - RPi5)** hibrit bulut ve yerel ağ (WebSocket/WebRTC) protokolleriyle tam senkronize çalışır. Telefonda başlatılan bir parça eve gelindiğinde salondaki sisteme veya arabaya binildiğinde araç amfisine milisaniyelik gecikme olmadan aktarılır.
```

---

# 📖 SAĞ SAYFA (RIGHT PAGE): BÖLÜM 1.2 & 1.3 — SES DSP, ÇOK KANALLI HOPARLÖR & MEDYA AKTARIMI

*(Sağ sayfadaki boş alana yapıştırılacak zengin teknik ve mimari metin.)*

```markdown
1.2  Audio DSP Engine, Mekân Akustiği & Çok Kanallı Ses

CoreMusic'in kalbinde, Windows ve Linux işletim sistemlerinin sesi bozan dahili mikser katmanlarını tamamen baypas eden tescilli **C++20 Neva Engine** gerçek zamanlı ses motoru yer alır.

**Akustik Mekân Simülatörü (Konser & Düğün Salonu Atmosferi):**
Gelişmiş psikoakustik Reverb ve yansıma algoritmaları sayesinde dinleyiciye odanın değil, doğrudan seslendirilen ortamın akustiği yaşatılır:
• **Konser Alanı & Arena Modu:** Genişletilmiş stereo sahne ve açık hava atmosferi.
• **Düğün Salonu & Balo Salonu (Ballroom):** Doğal oda yankısı, canlı yüksek rezonans ve dolgun dinamik atmosfer.
• **Canlı Stüdyo & Akustik Oda:** Enstrüman ve vokalin dinleyicinin kulağının hemen dibinde hissedildiği sıcak kayıt ortamı.
• **31-Band Parametrik EQ:** 20Hz – 20kHz aralığında stüdyo seviyesi 1/3 oktav frekans kontrolü.

**Distorsiyonsuz Yüksek Güç & Bit Derinliği Standartları:**
• **True Peak Brickwall Limiter:** En yüksek ses seviyesinde dahi dijital çatlama ve bozulmaları (clipping) önler (**THD+N <%0.01**, **SNR >100dB / DAC 112dB**).
• **Aktif 32-Bit Float Motor:** Güncel sürümde 1528 dB teorik dinamik tavan sunan 32-bit kayan nokta mimarisi tüm DSP zincirinde aktiftir.
• **64-Bit Float Yol Haritası (Roadmap):** Gelecek nesil ses çekirdeği için çift duyarlıklı (double precision) altyapı mimari planlamaya alınmıştır.

**1.0'dan 8.1 Surround'a (+1 LFE) Hoparlör Dağıtımı:**
• **1.0 Mono & 2.0 / 2.1:** Taşınabilir hoparlörler ve masaüstü Hi-Fi sistemleri.
• **4.1 Quadraphonic:** Araç içi 4 kapı + subwoofer akustiği için özel yönlendirme.
• **5.1 & 7.1 Surround:** Ev sinema sistemlerinde kusursuz çevresel ses deneyimi.
• **8.1 Çok Kanallı Stüdyo Çıkışı:** Tescilli PCM3168A donanım kartı ile bağımsız kanal matrisi.
• **(+1) Aktif Subwoofer Yönetimi:** Bağımsız LFE bas frekans süzme ve faz hizalama.

---

1.3  USB/Harici Bellek Aktarımı & İndirme İstasyonu

CoreMusic, müziği sistem dışına taşımayı son derece kolaylaştıran bağımsız bir indirme ve arşivleme motoruna (`download.coremusic.net`) sahiptir:
• **Tek Tıkla USB Aktarımı:** Araç teypleri ve harici cihazlarla tam uyumlu otomatik klasörleme (`Sanatçı / Albüm / Şarkı`).
• **Kayıpsız Ses İndirme:** Tam stüdyo kalitesinde **FLAC (24/32-bit)** ve yüksek çözünürlüklü **MP3 (320 kbps)** desteği.
• **Video Klip İndirme Motoru:** Sahne performansları ve video klipleri **MP4 (1080p/4K)**, **AVI**, **FLV** ve **MKV** formatlarında dışa aktarma.
• **Kusursuz Metadata:** Otomatik ID3v2 etiketleme, yüksek çözünürlüklü albüm kapakları ve araç teyplerinde Türkçe karakter bozulmasını önleyen akıllı normalizasyon.
```

---

## 📊 SAĞ SAYFA ALTI İÇİN TEKNİK ÖZET TABLOSU (SIDEBAR / SPEC BOX)

*(Sağ sayfanın en altına veya kenarına yerleştirebileceğiniz kurumsal teknik parametre tablosu.)*

| Sistem Katmanı | Özellik / Standart | Açıklama & Değer |
| :--- | :--- | :--- |
| **Görsel Arayüz** | Canlı Temalar & Ambient Aura | Müziğe göre yaşayan akıcı görsel şölen |
| **Tema Motoru** | AI Destekli Theme Maker Tool | Doğal dille kişisel tema üretimi ve CSS kontrolü |
| **Ses İşleme Motoru** | Neva Engine C++20 (32-Bit Float) | Sıfır bellek tahsisi (Zero-Allocation), <10ms gecikme |
| **Akustik DSP** | 31-Band EQ + Mekân Simülasyonu | Konser alanı, Düğün salonu, Canlı stüdyo, Kulüp |
| **Hoparlör Desteği** | 1.0, 2.1, 4.1, 5.1, 7.1, 8.1 Surround | (+1) Bağımsız LFE subwoofer faz yönetimi |
| **Distorsiyon & Güç** | True Peak Limiter (THD+N <%0.01) | Yüksek seste bozulmasız kristal netlik |
| **Dışa Aktarma (Export)**| USB Bellek & Harici Sürücüler | Otomatik teyp/araç uyumlu hiyerarşik klasörleme |
| **Medya Formatları** | FLAC, MP3, WAV, MP4, AVI, FLV | Kayıpsız ses ve yüksek çözünürlüklü video klipler |
| **Cihaz Ekosistemi** | Telefon, Tablet, PC, TV, Araç, Ev | Bulut ve yerel LAN WebSocket/WebRTC senkronizasyonu |

---

## 🎯 SAĞ SAYFAYA EKLENEBİLECEK DİĞER ALTERNATİF BLOKLAR

Eğer sağ sayfada daha fazla tematik kutu kullanmak isterseniz, aşağıdaki hazır modüllerden birini de seçebilirsiniz:

### Alternatif Kutu A: "Neden CoreMusic? — 4 Temel Sütun"
> **1. Tam Ses Sadakati:** İşletim sisteminin sesi bozan katmanlarını baypas eden stüdyo referansı.  
> **2. Kullanıcı Veri Mülkiyeti:** Kiralama bağımlılığı olmadan kalıcı kişisel müzik kütüphanesi.  
> **3. Büyüleyici Görsel Deneyim:** Canlı temalar ve yapay zekâ ile kişiselleşen estetik arayüz.  
> **4. Kesintisiz Çoklu Ortam:** Evden araca, cepten stüdyoya aynı akışın milisaniyesiz devam etmesi.

### Alternatif Kutu B: "8 Seviyeli Adaptif Kullanıcı Deneyimi"
> • **Profesyonel Stüdyo & Mühendisler:** LUFS, 2048-nokta FFT spektrum analizi, faz korelasyonu.  
> • **Müzisyenler & Canlı Performans:** Steinberg ASIO ile <10ms ultra düşük gecikme.  
> • **Hi-Fi & Audiophile:** 24/32-bit FLAC, doğrudan donanım DAC çıkışı (112dB SNR).  
> • **Kişisel & Ev Kullanıcıları:** Karaoke şarkı sözleri, dinamik canlı temalar, zahmetsiz akış.  
> • **Son Kullanıcılar:** Tek dokunuşla başlatılan, sıfır teknik karmaşa içeren konforlu deneyim.
