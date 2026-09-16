# CoreMusic — Teknik Vizyon Belgesi

CoreMusic; dijital müzik tüketiminin, arşiv yönetiminin, profesyonel ses işlemenin ve donanım entegrasyonunun parçalandığı modern ses ekosistemindeki temel yapısal problemleri çözmek amacıyla geliştirilmiş kurumsal seviyede bir **Dijital Ses Ekosistemi ve Merkezi Medya Yönetim Platformudur**. 

Platform; modern dijital müzik servislerinin kullanıcıyı mülkiyetsiz kiralama modellerine, platform bağımlılıklarına ve kayıplı (*lossy*) sıkıştırma algoritmalarına mahkûm eden kapalı mimarilerine kurumsal bir alternatif sunar. **Offline-First** felsefesiyle kullanıcı mülkiyetini korurken; ev medya merkezlerinden (Home NAS / Raspberry Pi 5) araç içi bilgi-eğlence sistemlerine (Car Infotainment), masaüstü iş istasyonlarından profesyonel kayıt stüdyolarına kadar uzanan dikey entegre bir yazılım ve tescilli donanım omurgası inşa etmeyi amaçlamaktadır.

---

### 1. Bütünleşik Dijital Ekosistem ve Cihaz Bağımsızlığı
CoreMusic, parçalanmış ses donanımlarını tek bir merkezi iletişim omurgası (`api.coremusic.net`) ve istemciye özel BFF (*Backend for Frontend*) katmanlarıyla birbirine bağlar:
- **Ev Medya Merkezi (`home.coremusic.net`):** Raspberry Pi 5 veya yerel NAS üzerinde çalışan merkezi kütüphane; WebRTC ve WebSocket tabanlı çok odalı (*multi-room*) kablosuz kayıpsız ses dağıtımı.
- **Araç İçi Bilgi-Eğlence (`car.coremusic.net`):** Dokunmatik araç ünitelerine özel, dikkat dağıtmayan yüksek kontrastlı arayüz (minimum 48px dokunma hedefleri); doğrudan SoC I2S/TDM ses hattı.
- **Masaüstü & Profesyonel Stüdyo (`music/studio/pro.coremusic.net`):** Windows (Tier 1), Linux (Tier 2) ve macOS (Tier 3) sistemlerinde yerel ses sunucuları; 8.1 surround izleme ve 31-band parametrik ekolayzır kontrolü.
- **Kesintisiz Deneyim Sürekliliği:** Merkezi durum senkronizasyonu ile evde dinlenen bir parçanın araçta kaldığı saniyeden ve kişisel DSP profiliyle kesintisiz devam etmesi hedeflenir.

---

### 2. Yazılım ve Veri Mimarisi Disiplini (L0-L6 Katmanları)
Sistem; katı **Clean Architecture**, **SOLID**, **Hexagonal Architecture (Ports & Adapters)** ve **Domain-Driven Design (DDD)** prensiplerine dayanır:
- **Katman Hiyerarşisi:** `L6 Electronics → L5 Services → L4 Domain → L3 Presentation → L2 Routing → L1 Security → L0 Infrastructure`. Ters yönlü bağımlılıklar (*Layer Violation*) mimari olarak kesinlikle yasaktır.
- **API-First & CQRS (ADR-084/086):** Koddan önce OpenAPI sözleşmesi; `api.coremusic.net` Gateway; PSR-14 Event Bus ile gevşek bağlı servisler; Yazma (Command) ve Okuma (Query) hatlarının tam izolasyonu.
- **18 BCNF Veritabanı (156 Tablo — ADR-040):** Sıfır ORM, ham PDO güvenliği, UUID v7 zaman indeksli birincil anahtarlar ve parametrik sorgular (`SELECT *` yasak).
- **Kurumsal Güvenlik:** Değişmez 10 adımlı middleware hattı; CSP nonce tabanlı `strict-dynamic`; NIST SP 800-38D uyumlu AES-256-GCM Credential Vault; Argon2id parola hashleme ve timing-safe CSRF (`csrf_token`).
- **Sıfır Çerçeve Frontend (ADR-001):** React/Vue bağımlılığı olmadan Vanilla JS ES6+, 9 katmanlı ITCSS/BEM, 19 PNG Mockup SSOT referansı ve 4-Tier responsive mimari (Phone, Embedded 1024×600, Wide, 4K No-Center fluid).

---

### 3. Ses Teknolojileri ve Donanım Entegrasyonu (Neva Engine)
Ses zincirinde sıfır gecikme, tavizsiz donanım hakimiyeti ve gerçek zamanlı determinizm esastır:
- **C++20 Neva Engine:** Ses döngüsünde (*Audio Thread*) sıfır bellek tahsisi (*Zero-Allocation*), kilitsiz mimari (*Lock-Free ring buffer*), 64-bayt önbellek hizalaması (`alignas(64)`), `noexcept` güvencesi ve 32-bit Float PCM dahili işleme.
- **Düşük Gecikme:** Windows ortamında Steinberg ASIO SDK 2.3.4 ile **<10ms**, WASAPI Exclusive modunda **<15ms** gecikme süresi.
- **Profesyonel DSP Zinciri (ADR-025):** 31-band parametrik EQ → stüdyo reverb simülatörü → dinamik kompresör → tepe sınırlayıcı (*peak limiter*).
- **8.1 Surround & Bass Yönetimi:** 8 uydu kanal + 1 LFE subwoofer; 4. derece Linkwitz-Riley 80Hz aktif crossover filtreleri.
- **Özel Donanım Katı (ADR-038):** XMOS XU316 USB Audio Class 2.0 MCU + TI PCM3168A (24-bit 192kHz DAC, 112dB SNR, 6-in/8-out) codec; kanal başına 100W @ 8Ω Class AB amplifikasyon (THD+N <%0.01, SNR >100dB, >0.5V DC offset röle koruması).

---

### 4. Yapay Zekâ Felsefesi ve Gelecek Stratejisi
- **Yapay Zekâ Katmanı (`coremusic_ai`):** AI karar merci olmak yerine; müzikal zevki modelleyen, akustik parmak izlerini (BPM, enerji, spektral yoğunluk) analiz eden, eksik metadata ve sözleri zenginleştiren ve ortama göre dinamik EQ/DSP önerileri sunan destekleyici bir katmandır.
- **Stratejik Yol Haritası:**
  1. *Faz 1 (MVP):* PC/Laptop üzerinde çalışan 10 panelli hibrit SPA, PHP 8.4 servisleri ve 18 BCNF veritabanı.
  2. *Faz 2 (Premium):* C++20 Neva Engine, ASIO/WASAPI optimizasyonu, XMOS XU316 + PCM3168A ses kartı donanımı ve 31-band EQ.
  3. *Faz 3 (Professional):* 8.1 surround stüdyo sistemi, araç içi gömülü ünite kabini ve çok odalı kablosuz P2P ses ağı.

---
*CoreMusic; verinin kullanıcı mülkiyetinde kaldığı, sesin en saf haliyle işlendiği ve donanım ile yazılımın mükemmel uyumla bütünleştiği bağımsız bir dijital ses standardı inşa etmeyi hedeflemektedir.*
