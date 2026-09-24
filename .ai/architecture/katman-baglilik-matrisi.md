---
title: "COREMUSIC Katman Bağımlılık Matrisi"
version: "1.1.0"
date: "2026-09-20"
updated: "2026-09-24"
author: "COREMUSIC Mimari Ekibi"
type: "mimari-referans"
katman_araligi: "K0-K20"
toplam_katman: 21
aciklama: "COREMUSIC sistem mimarisinde katmanlar arası bağımlılık ilişkileri, izin verilen akış yönleri ve yasak bağlantılar"
guncelleme: "2026-09-24"
durum: "aktif"
authority: "Kanonik bağımlılık kaynağı — .ai/architecture/index.md §3 yalnız bu dosyaya bağlanır"
---

# COREMUSIC Katman Bağımlılık Matrisi

## 1. Genel Bakış

Bu doküman, COREMUSIC projesinin 21 katmanı (K0–K20) arasındaki bağımlılık ilişkilerini, izin verilen veri akış yönlerini ve kesinlikle yasaklanan bağlantıları tanımlar. **Bu dosya bağımlılık kurallarının TEK KANONİK KAYNAĞIDIR**; [[index]] §3 yalnızca bağlantı taşır.

### 1.1 Katman Sınıflandırması

| Sınıf | Katmanlar | Amaç |
|-------|-----------|------|
| **Alt Sistem** | K0 | Temel işletim sistemi hizmetleri |
| **Donanım** | K1 | Fiziksel donanım ve elektronik kart (firmware = K1.f) |
| **Fiziksel Üretim** | K16, K17, K18, K19, K20 | Beş BAĞIMSIZ üretim katmanı (hiyerarşi yok, birbirine alt değildir) |
| **Sistem Yazılımı** | K2, K3 | Sürücüler ve ses motoru |
| **Veri & Güvenlik** | K5, K6 | Veri yönetimi ve güvenlik katmanları |
| **Yapay Zeka** | K4 | ML/AI çıkarım ve eğitim |
| **Middleware** | K7 | Ara yazılım hizmetleri |
| **Servis & API** | K8, K9 | Backend servisleri ve API uçları |
| **Uygulama & UX** | K10, K11 | Kullanıcı arayüzü ve deneyimi |
| **İzleme & CI/CD** | K12, K13 | Operasyonel izleme ve sürekli teslimat |
| **Ağ & Medya** | K14, K15 | Ağ iletişimi ve medya işleme |

### 1.2 Akış Yönleri

| Sembol | Anlam |
|--------|-------|
| → | Tek yönlü bağımlılık (kayan katman, hedefin arayüzüne bağımlıdır) |
| ↔ | Çift yönlü bağımlılık (her iki taraf da birbirinin arayüzüne bağımlıdır) |
| ↘ | Dolaylı bağımlılık (ara katmanlar üzerinden) |
| ✕ | Yasak bağımlılık (kesinlikle izin verilmez) |
| ↓ (gösterim) | Gösterim/raporlama verisi akışı — **bağımlılık SAYILMAZ** |

### 1.3 Bağımlılık Okuma Semantiği (KANONİK — 3 turlu tartışma kararı)

> **ok = "kayan katman → hedefin ARAYÜZÜNE bağımlı".**
>
> Yani KX → KY satırı şunu söyler: KX, KY'nin public arayüzünü (sözleşmesini, API'sini) kullanır. KY'nin iç yapısı, özel alanları veya uygulama detayları KX'e görünmez ve değiştirilemez. Bu okuma biçimi matrisin tamamı için geçerlidir; matriste bulunmayan hiçbir çift izinli sayılmaz.

| Kural | Açıklama |
|-------|----------|
| K1 | Matriste olmayan izin yoktur (ör. K3 → K4, K11 → K12, K13 → K14 ve K15'in üretim/bileşen katmanlarına izni matriste YOKTUR → izinli değildir) |
| K2 | K15'in tek hedefi K14'tür; üretim/bileşen katmanlarına bağımlılığı TAMAMEN silinmiştir |
| K3 | K16, K17, K18, K19, K20 beş bağımsız katmandır; 21 katman sabittir |
| K4 | K12 → K8 tek resmi bağımlılıktır (izleme, servis katmanını arayüzünden dinler) |
| K5 | Layer Violation denetimi bu matrise göre yapılır; A0-A5 yalnız alan etiketidir |

### 1.4 A0-A5 Alan Etiketleri ↔ K Katmanları Eşlemesi

> **A etiketleri yalnızca ALAN ETİKETİDİR; bağımlılık kuralı üretmezler.** Bağımlılık kuralı için tek kaynak §2.1 matristir. (AGENTS.md §5/§15 bu eşlemeye bağlanır — AGENTS.md ikinci kaynak değil, bağlantıdır.)

| Etiket | Kapsam (K katmanları) | Alan | Tipik Agent sorumluluğu |
|--------|----------------------|------|--------------------------|
| **A0** | K0-K5 | Altyapı (OS, donanım, sürücü, ses motoru, yapay zeka, veri) | Data Engineer, Embedded Engineer, DSP Firmware, Windows SW |
| **A1** | K6-K7 | Güvenlik & middleware | Security Engineer |
| **A2** | K8-K9 | Hizmet & yönlendirme (API) | Backend Architect |
| **A3** | K10-K11 | Sunum (uygulama paneli + kullanıcı deneyimi) | UI Designer |
| **A4** | K12-K15 | Operasyon (izleme, CI/CD, ağ, medya streaming) | DevOps Engineer, Media/Data |
| **A5** | K16-K20 | Fiziksel üretim (Class AB, güç kaynağı, termal, PCB, BOM) | Audio Hardware Engineer |

**A ile K arasındaki ilişki:** Her A etiketi ardışık K katmanlarını GRUPLAR; bir A etiketinin içindeki katmanların birbirine bağımlılığı yine §2.1'deki ok ile belirlenir (ör. A0 içindeki K5 → K0 oku matristedir, etiketten gelmez). A → A tersi (alt → üst) bağımlılık etiketten ÖTÜRÜ İZİN KAZANMAZ.

---

## 2. Katman Bağımlılık Matrisi

### 2.1 Ana Bağımlılık Tablosu

> **Not:** Her satır KAYAN (kayan = bağımlı olan) katmanı, her sütun HEDEF katmanı temsil eder.
> ok = "satırdaki katman, sütundaki katmanın ARAYÜZÜNE bağımlıdır" (§1.3).
> Boş (—) = bağımlılık yok · ✕ = yasak (§5).

| Kaynak ↓ / Hedef → | K0 | K1 | K2 | K3 | K4 | K5 | K6 | K7 | K8 | K9 | K10 | K11 | K12 | K13 | K14 | K15 | K16 | K17 | K18 | K19 | K20 |
|---------------------|----|----|----|----|----|----|----|----|----|----|-----|-----|-----|-----|-----|-----|-----|-----|-----|-----|-----|
| **K0** | — | — | — | — | — | — | — | — | — | — | — | — | — | — | — | — | — | — | — | — | — |
| **K1** | → | — | — | — | — | — | — | — | — | — | — | — | — | — | — | — | ↔ | ↔ | ↔ | ↔ | ↔ |
| **K2** | — | → | — | — | — | — | — | — | — | — | — | — | — | — | — | — | — | — | — | — | — |
| **K3** | — | — | → | — | — | — | — | — | — | — | — | — | — | — | — | — | — | — | — | — | — |
| **K4** | — | — | — | — | — | → | — | — | — | — | — | — | — | — | — | — | — | — | — | — | — |
| **K5** | → | — | — | — | — | — | — | — | — | — | — | — | — | — | — | — | — | — | — | — | — |
| **K6** | — | — | — | — | — | → | — | — | — | — | — | — | — | — | — | — | — | — | — | — | — |
| **K7** | — | — | — | — | — | — | → | — | — | — | — | — | — | — | — | — | — | — | — | — | — |
| **K8** | — | — | — | — | — | — | — | → | — | — | — | — | — | — | — | — | — | — | — | — | — |
| **K9** | — | — | — | — | — | — | — | — | → | — | — | — | — | — | — | — | — | — | — | — | — |
| **K10** | — | — | — | — | — | — | — | — | — | → | — | — | — | — | — | — | — | — | — | — | — |
| **K11** | — | — | — | — | — | — | — | — | — | — | → | — | — | — | — | — | — | — | — | — | — |
| **K12** | — | — | — | — | — | — | — | — | → | — | — | — | — | — | — | — | — | — | — | — | — |
| **K13** | — | — | — | — | — | — | — | — | — | — | — | — | → | — | — | — | — | — | — | — | — |
| **K14** | — | — | — | — | — | — | — | — | — | → | — | — | — | — | — | — | — | — | — | — | — |
| **K15** | — | — | — | — | — | — | — | — | — | — | — | — | — | — | → | — | — | — | — | — | — |
| **K16** | — | ↔ | — | — | — | — | — | — | — | — | — | — | — | — | — | — | — | — | — | — | — |
| **K17** | — | ↔ | — | — | — | — | — | — | — | — | — | — | — | — | — | — | — | — | — | — | — |
| **K18** | — | ↔ | — | — | — | — | — | — | — | — | — | — | — | — | — | — | — | — | — | — | — |
| **K19** | — | ↔ | — | — | — | — | — | — | — | — | — | — | — | — | — | — | — | — | — | — | — |
| **K20** | — | ↔ | — | — | — | — | — | — | — | — | — | — | — | — | — | — | — | — | — | — | — |

**Matris hücre doğrulaması (2026-09-24):**

- K15 satırında tek ok K14 sütunundadır; üretim/bileşen katmanı sütunlarının tamamı (sağ uç) boştur → K15'in başka hedefi yoktur.
- K11 satırında tek ok K10 sütunundadır; K12 sütunu boştur → K11 → K12 bağımlılık değildir (yalnız gösterim verisi, §3.3).
- K12 satırındaki tek ok K8 sütunundadır → K12 → K8 tek resmi bağımlılık.
- K13 satırındaki tek ok K12 sütunundadır; K14 sütunu boştur → K13 → K14 izni yoktur.
- K3 satırındaki tek ok K2 sütunundadır; K4 sütunu boştur → K3 → K4 izni yoktur.

### 2.2 Sıkıştırılmış Bağımlılık Listesi

| Kaynak Katman | Doğrudan Bağımlılıklar | Bağımlılık Türü |
|---------------|----------------------|-----------------|
| **K0** (İşletim Sistemi) | Yok (temel katman) | Kök |
| **K1** (Donanım) | K0, K16, K17, K18, K19, K20 | K0 → tek yönlü; K16-K20 ↔ çift yönlü |
| **K2** (Sürücü) | K1 | Tek yönlü → |
| **K3** (Ses Motoru) | K2 | Tek yönlü → |
| **K4** (Yapay Zeka) | K5 | Tek yönlü → |
| **K5** (Veri) | K0 | Tek yönlü → |
| **K6** (Güvenlik) | K5 | Tek yönlü → |
| **K7** (Middleware) | K6 | Tek yönlü → |
| **K8** (Servis) | K7 | Tek yönlü → |
| **K9** (API) | K8 | Tek yönlü → |
| **K10** (Uygulama) | K9 | Tek yönlü → |
| **K11** (UX) | K10 | Tek yönlü → (K12 bağımlılığı YOK — §3.3) |
| **K12** (İzleme) | K8 | Tek yönlü → (tek resmi bağımlılık) |
| **K13** (CI/CD) | K12 | Tek yönlü → |
| **K14** (Ağ) | K9 | Tek yönlü → |
| **K15** (Medya) | K14 | Tek yönlü → (TEK HEDEF K14) |
| **K16** (Class AB Amplifikatör) | K1 | Çift yönlü ↔ — bağımsız katman |
| **K17** (Güç Kaynağı ±35V) | K1 | Çift yönlü ↔ — bağımsız katman |
| **K18** (Termal Tasarım) | K1 | Çift yönlü ↔ — bağımsız katman |
| **K19** (PCB Tasarım) | K1 | Çift yönlü ↔ — bağımsız katman |
| **K20** (BOM & Üretim) | K1 | Çift yönlü ↔ — bağımsız katman |

---

## 3. İzin Verilen Akış Yönleri

### 3.1 Temel Akış Zincirleri

#### Zincir A: Medya İşleme Hattı

    K15 (Medya) → K14 (Ağ) → K9 (API) → K8 (Servis) → K7 (Middleware) → K6 (Güvenlik) → K5 (Veri) → K0 (İşletim Sistemi)

**Toplam derinlik:** 8 katman | **Kritiklik:** Yüksek — ses akışı için zorunlu yol
**Not:** Zincir K14'te biter ve K9'a atlar; K15'in üretim/bileşen katmanlarına BİR ADIM DAHİ YOKTUR.

#### Zincir B: Donanım-Harici İşlem Hattı

    K3 (Ses Motoru) → K2 (Sürücü) → K1 (Donanım) → K0 (İşletim Sistemi)

**Toplam derinlik:** 4 katman | **Kritiklik:** Yüksek — düşük seviyeli ses işleme
**Not:** K1'in yanında firmware/ klasörü K1.f olarak K1'e bağlıdır (ayrı katman değildir).

#### Zincir C: Yapay Zeka Hattı

    K4 (Yapay Zeka) → K5 (Veri) → K0 (İşletim Sistemi)

**Toplam derinlik:** 3 katman | **Kritiklik:** Orta — ML çıkarım için veri erişimi

#### Zincir D: Kullanıcı Yüzeyi Hattı

    K11 (UX) → K10 (Uygulama) → K9 (API) → K8 (Servis)

**Toplam derinlik:** 4 katman | **Kritiklik:** Yüksek — kullanıcı etkileşimi
**Not:** K11'in K12'ye bağımlılığı YOKTUR (eski zincirden kaldırıldı).

#### Zincir E: Operasyonel Hat

    K13 (CI/CD) → K12 (İzleme) → K8 (Servis)

**Toplam derinlik:** 3 katman | **Kritiklik:** Orta — deployment ve monitoring
**Not:** K13 → K14 izni matriste yoktur, bu zincirde de yoktur.

#### Zincir F: Elektronik İşlem Hattı (beş bağımsız kol)

    K16 ↔ K1 (Donanım) ↔ K0 (İşletim Sistemi)
    K17 ↔ K1 (Donanım) ↔ K0 (İşletim Sistemi)
    K18 ↔ K1 (Donanım) ↔ K0 (İşletim Sistemi)
    K19 ↔ K1 (Donanım) ↔ K0 (İşletim Sistemi)
    K20 ↔ K1 (Donanım) ↔ K0 (İşletim Sistemi)

**Toplam derinlik:** 3 katman (çift yönlü) | **Kritiklik:** Yüksek — fiziksel donanım erişimi
**Not:** Beş kol birbirine bağımlı DEĞİLDİR; her kol yalnız K1 ↔ ilişkisiyle bağlıdır.

#### Zincir G: Ses İşleme Hattı (K2 → K3, sürücü arayüzü)

    K3 (Ses Motoru) → K2 (Sürücü) → K1 (Donanım) → K0 (İşletim Sistemi)

**Toplam derinlik:** 4 katman | **Kritiklik:** Yüksek — gerçek zamanlı ses yolu (Zincir B ile aynı rota, işlevsel adlandırma)

### 3.2 İzin Verilen Akış Yönleri Tablosu

| Akış Yönü | Kaynak → Hedef | Gerekçe |
|-----------|----------------|---------|
| → | K1 → K0 | Donanım işletim sistemi arayüzüne bağımlıdır |
| → | K2 → K1 | Sürücüler donanım arayüzüne erişir |
| → | K3 → K2 | Ses motoru sürücü arayüzünü çağırır |
| → | K4 → K5 | AI modeli veri katmanının arayüzünden beslenir |
| → | K5 → K0 | Veri katmanı OS servislerini kullanır |
| → | K6 → K5 | Güvenlik veri şifreleme/doğrulama arayüzünü kullanır |
| → | K7 → K6 | Middleware güvenlik doğrulamasından geçer |
| → | K8 → K7 | Servisler middleware üzerinden iletişir |
| → | K9 → K8 | API uçları servis arayüzlerini çağırır |
| → | K10 → K9 | Uygulama API'ye istek gönderir |
| → | K11 → K10 | UX katmanı uygulama arayüzünü kontrol eder |
| → | K12 → K8 | İzleme servisleri K8 arayüzünden dinler (tek resmi bağımlılık) |
| → | K13 → K12 | CI/CD izleme arayüzünü kullanır |
| → | K14 → K9 | Ağ katmanı API üzerinden bağlanır |
| → | K15 → K14 | Medya akışı ağ arayüzü üzerinden iletilir (K15'in TEK hedefi) |
| ↔ | K1 ↔ K0 | Donanım ve OS arasındaki çift yönlü arayüz |
| ↔ | K1 ↔ K16 | Amplifikatör ↔ Donanım |
| ↔ | K1 ↔ K17 | Güç kaynağı ↔ Donanım |
| ↔ | K1 ↔ K18 | Termal sistem ↔ Donanım |
| ↔ | K1 ↔ K19 | PCB ↔ Donanım |
| ↔ | K1 ↔ K20 | BOM/üretim ↔ Donanım |

### 3.3 Gösterim Verisi Akışı (bağımlılık DEĞİL — 3 turlu tartışma kararı)

| Yön | Kaynak → Hedef | Tür | Açıklama |
|-----|----------------|-----|----------|
| ↓ (gösterim) | K11 → K12 | **Gösterim verisi (üst → alt)** | K11 (UX), K12'ye (İzleme) kullanıcı deneyimi metriklerini/gösterim verisini **raporlar**. Bu bir bağımlılık değildir, §2.1 matrisinde ok olarak yer almaz; K11'in K12 arayüzüne bağımlı olmasını doğurmaz. Tersi (K12 → K11) zaten yoktur. |

**Kural:** K11 → K12 yalnızca "gösterim/raporlama verisi akışı" olarak yazılır; hiçbir zincirde (§3.1), derinlik hesabında (§6) veya Layer Violation denetiminde bağımlılık sayılmaz. K12'nin resmi bağımlılığı tek taraflı ve K8'e dönüktür (K12 → K8).

---

## 4. Kritik Bağımlılıklar

### 4.1 Yüksek Kritiklik (Sistem Çökmesine Neden Olur)

| # | Bağımlılık | Etki | Kurtarma Süresi | Yedek Mekanizma |
|---|-----------|------|-----------------|-----------------|
| 1 | **K1 → K0** | Donanım tüm yazılım katmanlarının temeli | 0 saniye (kritik) | Donanım arızası → sistem tamamen durur |
| 2 | **K5 → K0** | Veri katmanı OS olmadan çalışamaz | < 1 saniye | Veritabanı bağlantı havuzu redundant |
| 3 | **K2 → K1** | Sürücüler donanıma bağlı | 0 saniye (kritik) | Driver fallback modu mevcut |
| 4 | **K3 → K2** | Ses motoru sürücü olmadan ses üretimi durur | < 1 saniye | Yazılım tabanlı ses işleme (yavaş) |
| 5 | **K16 ↔ K1 · K17 ↔ K1 · K18 ↔ K1 · K19 ↔ K1 · K20 ↔ K1** | Beş bağımsız elektronik katman, donanımla çift yönlü bağlı | 0 saniye (kritik) | Donanım arızası → fiziksel onarım |

### 4.2 Orta Kritiklik (Performans Düşüşüne Neden Olur)

| # | Bağımlılık | Etki | Kurtarma Süresi | Yedek Mekanizma |
|---|-----------|------|-----------------|-----------------|
| 6 | **K6 → K5** | Güvenlik katmanı veriye erişemez | < 5 saniye | Cached security tokens |
| 7 | **K7 → K6** | Middleware güvenlik doğrulaması başarısız | < 5 saniye | Graceful degradation modu |
| 8 | **K8 → K7** | Servisler middleware üzerinden geçemez | < 10 saniye | Circuit breaker pattern |
| 9 | **K9 → K8** | API uçları servislere ulaşamaz | < 10 saniye | API gateway fallback |
| 10 | **K4 → K5** | AI modeli veri beslemesi kesilir | < 30 saniye | Model cache (stale data riski) |

### 4.3 Düşük Kritiklik (Kullanıcı Deneyimini Etkiler)

| # | Bağımlılık | Etki | Kurtarma Süresi | Yedek Mekanizma |
|---|-----------|------|-----------------|-----------------|
| 11 | **K10 → K9** | Uygulama API'ye bağlanamaz | < 10 saniye | Offline mod |
| 12 | **K11 → K10** | UX katmanı uygulamayı kontrol edemez | < 5 saniye | Responsive fallback UI |
| 13 | **K15 → K14** | Medya akışı ağ üzerinden geçemez | < 15 saniye | Local media cache |
| 14 | **K14 → K9** | Ağ katmanı API'ye bağlanamaz | < 10 saniye | Retry with exponential backoff |
| 15 | **K12 → K8** | İzleme verileri toplanamaz | < 60 saniye | Buffered metrics (çevrimdışı toplama) |
| 16 | **K13 → K12** | CI/CD izleme verilerini kullanamaz | < 120 saniye | Manual deployment approval |

> **Mojibake düzeltmesi (2026-09-24):** §4.3'te 15 numaralı satırdaki "60 saniye" süresinde Kıril alfabesi artığı vardı → "60 saniye" olarak düzeltildi.

---

## 5. Yasak Bağımlılıklar

### 5.1 Kesinlikle Yasaklanan Bağlantılar

> **UYARI:** Aşağıdaki bağımlılıklar mimari olarak kesinlikle yasaktır. Bu bağlantıların tespit edilmesi durumunda derhal düzeltilmelidir.

| # | Kaynak | Hedef | Yasak Nedeni | Risk Seviyesi |
|---|--------|-------|-------------|---------------|
| 1 | **K11 → K1** | UX → Donanım | Doğrudan donanım erişimi güvenlik açığı yaratır | **KRİTİK** |
| 2 | **K11 → K2** | UX → Sürücü | Doğrudan sürücü erişimi sistem bütünlüğünü bozar | **KRİTİK** |
| 3 | **K10 → K1** | Uygulama → Donanım | Doğrudan donanım erişimi izinsiz kullanım riski | **KRİTİK** |
| 4 | **K10 → K5** | Uygulama → Veri | Doğrudan veri tabanı erişimi güvenlik açığı | **YÜKSEK** |
| 5 | **K15 → K1** | Medya → Donanım | Doğrudan donanım erişimi ses arızasına neden olur | **YÜKSEK** |
| 6 | **K15 → K5** | Medya → Veri | Doğrudan veri erişimi veri bütünlüğünü bozar | **YÜKSEK** |
| 7 | **K4 → K0** | AI → OS | Doğrudan OS erişimi güvenlik açığı yaratır | **YÜKSEK** |
| 8 | **K4 → K1** | AI → Donanım | Doğrudan donanım erişimi sistem kararlılığını bozar | **YÜKSEK** |
| 9 | **K3 → K0** | Ses Motoru → OS | Doğrudan OS erişimi zamanlama çakışmasına neden olur | **ORTA** |
| 10 | **K12 → K0** | İzleme → OS | Doğrudan OS erişimi izinsiz değişiklik riski | **ORTA** |
| 11 | **K13 → K0** | CI/CD → OS | Doğrudan OS erişimi yetkisiz deployment riski | **ORTA** |
| 12 | **K16 → K0, K17 → K0, K18 → K0, K19 → K0, K20 → K0** | Beş üretim katmanı → OS | Doğrudan OS erişimi donanım arızasına neden olur | **YÜKSEK** |
| 13 | **K9 → K1** | API → Donanım | Doğrudan donanım erişimi API güvenliğini bozar | **YÜKSEK** |
| 14 | **K9 → K5** | API → Veri | Doğrudan veri erişimi veri sızıntısına neden olur | **YÜKSEK** |
| 15 | **K7 → K0** | Middleware → OS | Doğrudan OS erişimi middleware kararlılığını bozar | **ORTA** |
| 16 | **K6 → K0** | Güvenlik → OS | Doğrudan OS erişimi güvenlik ihlali riski | **KRİTİK** |

### 5.2 Yeni Eklenen Yasaklar (2026-09-24 — tartışma kararı)

| # | Kaynak | Hedef | Yasak Nedeni | Risk Seviyesi |
|---|--------|-------|-------------|---------------|
| 17 | **K15 → beş fiziksel üretim/bileşen katmanının tamamı (§1.1 Fiziksel Üretim sınıfı)** | Medya → Fiziksel üretim | Medya katmanının üretim katmanlarına doğrudan bağımlılığı kurgu dışıdır; K15'in tek hedefi K14'tür | **YÜKSEK** |
| 18 | **K3 → K4** | Ses Motoru → Yapay Zeka | Ses motoru AI katmanına bağımlı olamaz (matriste bu ok yok) | **ORTA** |
| 19 | **K13 → K14** | CI/CD → Ağ | CI/CD'in ağ katmanına bağımlılığı matriste yoktur | **ORTA** |
| 20 | **K11 → K12 (bağımlılık olarak)** | UX → İzleme | K11'in K12 arayüzüne bağımlılığı izinli değildir; yalnız gösterim verisi akışıdır (§3.3) | **ORTA** |
| 21 | **K16 ↔ K17, K16 ↔ K18, K16 ↔ K19, K17 ↔ K20 … (üretim katmanları arası)** | Üretim katmanları ↔ birbirleri | Beş katman bağımsızdır; birbirine doğrudan bağımlılık YOKTUR — tüm bağlantılar K1 üzerinden yürür | **YÜKSEK** |

### 5.3 Yasak Bağlantı Haritası

    YASAK YOLLAR (✕):
    ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

    K11 (UX) ──✕──→ K1 (Donanım)        [KRİTİK]
    K11 (UX) ──✕──→ K2 (Sürücü)         [KRİTİK]
    K11 (UX) ──✕──→ K12 (İzleme) bağımlılık olarak [ORTA — yalnız gösterim verisi serbest]
    K10 (Uygulama) ──✕──→ K1 (Donanım)  [KRİTİK]
    K10 (Uygulama) ──✕──→ K5 (Veri)     [YÜKSEK]
    K15 (Medya) ──✕──→ K1 (Donanım)     [YÜKSEK]
    K15 (Medya) ──✕──→ K5 (Veri)        [YÜKSEK]
    K15 (Medya) ──✕──→ Fiziksel üretim katmanları (§1.1)   [YÜKSEK — tamamen kaldırıldı]
    K4 (AI) ──✕──→ K0 (OS)              [YÜKSEK]
    K4 (AI) ──✕──→ K1 (Donanım)         [YÜKSEK]
    K3 (Ses Motoru) ──✕──→ K0 (OS)      [ORTA]
    K3 (Ses Motoru) ──✕──→ K4 (Yapay Zeka) [ORTA]
    K12 (İzleme) ──✕──→ K0 (OS)         [ORTA]
    K13 (CI/CD) ──✕──→ K0 (OS)          [ORTA]
    K13 (CI/CD) ──✕──→ K14 (Ağ)         [ORTA]
    K16 … K20 (üretim) ──✕──→ K0 (OS)   [YÜKSEK]
    K16 … K20 (üretim) ──✕──→ birbirleri [YÜKSEK — bağımsız katmanlar]
    K9 (API) ──✕──→ K1 (Donanım)        [YÜKSEK]
    K9 (API) ──✕──→ K5 (Veri)           [YÜKSEK]
    K7 (Middleware) ──✕──→ K0 (OS)       [ORTA]
    K6 (Güvenlik) ──✕──→ K0 (OS)        [KRİTİK]

    ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

---

## 6. Bağımlılık Derinliği Analizi

### 6.1 Katman Derinlikleri

| Katman | Derinlik (K0'dan uzaklık) | Erişim Yolu |
|--------|--------------------------|-------------|
| K0 | 0 | Kök katman |
| K1 | 1 | K1 → K0 |
| K5 | 1 | K5 → K0 |
| K2 | 2 | K2 → K1 → K0 |
| K6 | 2 | K6 → K5 → K0 |
| K3 | 3 | K3 → K2 → K1 → K0 |
| K7 | 3 | K7 → K6 → K5 → K0 |
| K4 | 2 | K4 → K5 → K0 |
| K8 | 4 | K8 → K7 → K6 → K5 → K0 |
| K9 | 5 | K9 → K8 → … → K0 |
| K10 | 6 | K10 → K9 → … → K0 |
| K11 | 7 | K11 → K10 → … → K0 |
| K12 | 5 | K12 → K8 → … → K0 |
| K13 | 6 | K13 → K12 → K8 → … → K0 |
| K14 | 6 | K14 → K9 → … → K0 |
| K15 | 7 | K15 → K14 → … → K0 |
| K16 | 2 | K16 ↔ K1 → K0 |
| K17 | 2 | K17 ↔ K1 → K0 |
| K18 | 2 | K18 ↔ K1 → K0 |
| K19 | 2 | K19 ↔ K1 → K0 |
| K20 | 2 | K20 ↔ K1 → K0 |

**Not:** K11 → K12 gösterim verisi derinliğe EKLENMEZ (bağımlılık değildir); K13'ün derinliği K12 üzerinden hesaplanır (K13 → K14 izni olmadığı için eski 7 derinlik geçersizdir — düzeltilmiş değer 6).

### 6.2 Maksimum Derinlik Uyarıları

> **UYARI:** Aşağıdaki katmanlar 5+ derinliktedir. Bu, gecikme (latency) ve hata yayılımı riskini artırır.

| Katman | Derinlik | Uyarı |
|--------|----------|-------|
| **K9** (API) | 5 | Tek hata noktası (single point of failure) riski |
| **K10** (Uygulama) | 6 | Hata yayılımı çok katmanlı |
| **K11** (UX) | 7 | En derin katman — maksimum latency riski |
| **K12** (İzleme) | 5 | İzleme gecikmesi artabilir |
| **K13** (CI/CD) | 6 | Deployment gecikmesi riski |
| **K14** (Ağ) | 6 | Ağ gecikmesi katmanlı |
| **K15** (Medya) | 7 | Medya akışı gecikmesi — en kritik risk |

---

## 7. Bağımlılık Güvenlik Kontrol Listesi

### 7.1 Mimari İhlal Kontrolü

| Kontrol | Durum | Açıklama |
|---------|-------|----------|
| Hiçbir UX katmanı doğrudan donanıma erişiyor mu? | ☐ Kontrol et | K11 → K1 yasak |
| Hiçbir uygulama doğrudan veri tabanına erişiyor mu? | ☐ Kontrol et | K10 → K5 yasak |
| Hiçbir medya katmanı doğrudan donanıma erişiyor mu? | ☐ Kontrol et | K15 → K1 yasak |
| Hiçbir AI katmanı doğrudan OS'e erişiyor mu? | ☐ Kontrol et | K4 → K0 yasak |
| K15'in üretim/bileşen katmanına izni dosyalarda geçiyor mu? | ☐ K15 satırında tek hedef K14 olmalı | 2026-09-24: tek hedef K14 ✓ (sütun başlığı hariç) |
| K11 → K12 bağımlılık olarak mı yazıldı? | ☐ Kontrol et | Yalnız gösterim verisi olarak yazılabilir (§3.3) |
| A0-A5 etiketi bağımlılık kuralı gibi mi kullanıldı? | ☐ Kontrol et | A yalnız etikettir; kural §2.1 matristedir |
| Tüm bağımlılıklar tabloda tanımlı mı? | ☐ Kontrol et | Tanımsız bağımlılık = hata |
| Derinlik 7+ katman olan yollar var mı? | ☐ Kontrol et | Optimizasyon gerekli |
| Çift yönlü bağımlılıklar sadece K1 ↔ {K16-K20} ve K1 ↔ K0 mı? | ☐ Kontrol et | Diğer çift yönlü bağımlılıklar yasak |

### 7.2 Performans Kontrolü

| Metrik | Hedef | Mevcut Durum |
|--------|-------|--------------|
| Maksimum bağımlılık derinliği | ≤ 7 katman | K11, K15 = 7 katman |
| Tek hata noktası sayısı | ≤ 3 | K0, K1, K5 |
| Çift yönlü bağımlılık sayısı | ≤ 6 | K1↔K0 + K1↔K16 … K1↔K20 = 6 |
| Yasak bağımlılık ihlali | 0 | ☐ Kontrol et |

---

## 8. Acil Durum Yolları

### 8.1 K0 (İşletim Sistemi) Çökmesi Durumu

    Etkilenen Katmanlar: TÜMÜ (K1-K20)
    Kurtarma Önceliği: KRİTİK
    Tahmini Kurtarma Süresi: 5-15 dakika

    Kurtarma Adımları:
    1. Donanım düzeyinde otomatik yeniden başlatma (K1-K20 ↔ K1)
    2. Sürücü yeniden yükleme (K2 → K1)
    3. Veri katmanı bağlantısı yeniden kurma (K5 → K0)
    4. Güvenlik token'ları yenileme (K6 → K5)
    5. Tüm servisleri sıralı başlatma (K8 → K7 → K6 → K5 → K0)

### 8.2 K1 (Donanım) Çökmesi Durumu

    Etkilenen Katmanlar: K2, K3, K16, K17, K18, K19, K20 (+ K1.f firmware)
    Kurtarma Önceliği: KRİTİK
    Tahmini Kurtarma Süresi: 15-60 dakika (fiziksel müdahale)

    Kurtarma Adımları:
    1. Donanım arıza teşhisi (K16-K20 ↔ K1)
    2. Elektronik devre kontrolü (K17 güç kaynağı, K18 termal, K19 PCB)
    3. Sürücü yeniden yükleme (K2 → K1)
    4. Ses motoru sıfırlama (K3 → K2 → K1)
    5. Firmware doğrulama (firmware/ = K1.f) ve test

### 8.3 K5 (Veri) Çökmesi Durumu

    Etkilenen Katmanlar: K4, K6, K7, K8, K9, K10, K11, K12, K13, K14, K15
    Kurtarma Önceliği: YÜKSEK
    Tahmini Kurtarma Süresi: 2-10 dakika

    Kurtarma Adımları:
    1. Veritabanı bağlantı havuzunu sıfırla
    2. Cache tabanlı geçici çözüm etkinleştir
    3. Veri katmanı bağlantısını yeniden kur (K5 → K0)
    4. Güvenlik token'larını yenile (K6 → K5)
    5. Servisleri sıralı başlat

---

## 9. Değişiklik Geçmişi

| Tarih | Sürüm | Değişiklik | Sorumlu |
|-------|-------|-----------|---------|
| 2026-09-20 | 1.0.0 | İlk oluşturma — 21 katman bağımlılık matrisi | COREMUSIC Mimari Ekibi |
| 2026-09-24 | 1.1.0 | 3 turlu mimari tartışma bağlayıcı kararları: (1) ok semantiği kanonikleşti (§1.3); (2) medya katmanının üretim/bileşen katmanlarına izni tamamen silindi; (3) K16-K20 beş bağımsız katman; (4) K12 → K8 tek resmi bağımlılık + K11 → K12 yalnız gösterim verisi (§3.3); (5) A0-A5 alan etiketleri eklendi (§1.4); (6) §10 disk klasör listesi; K19 = PCB; mojibake temizliği; §7 başlık düzeltmesi; yeni yasaklar §5.2 | Vault Steward |

---

## 10. İlgili Dokümanlar (GERÇEK DISK — 2026-09-24 sayımı)

| Doküman | Yol | Açıklama |
|---------|-----|----------|
| Master Architecture Index | **index.md** | Projenin tüm katmanlarını tanımlayan ana referans (eski §9/legacy ad kaldırıldı — fiziksel dosya YOK, gerçek hedef index.md) |
| K0 İşletim Sistemi | k0-isletim-sistemi/ | İşletim sistemi katmanı detayları (15 dosya) |
| K1 Donanım | k1-donanim/ | Donanım mimarisi ve spesifikasyonlar (23 dosya) |
| K1.f Firmware | firmware/ | Mikrodenetleyici firmware — K1'e bağlıdır, ayrı katman değildir (8 dosya) |
| K2 Sürücü | k2-surucu/ | Sürücü yazılımı ve API'leri (14 dosya — ADR-024 birleşim hedefi) |
| Eski sürücü klasörü — BİRLEŞİM SONRASI YOK | (ADR-024) | **YOK** — 12 dosyası k2-surucu/ klasörüne taşındı (silinmedi) |
| K3 Ses Motoru | k3-ses-motoru/ | Ses işleme ve üretim motoru (18 dosya) |
| K4 Yapay Zeka | k4-yapay-zeka/ | ML/AI çıkarım ve eğitim (14 dosya) |
| K5 Veri | k5-veri-yonetimi/ | Veri yönetimi ve depolama (14 dosya) |
| K6 Güvenlik | k6-guvenlik/ | Güvenlik protokolleri ve sertifika yönetimi (16 dosya) |
| K7 Middleware | k7-middleware/ | Ara yazılım hizmetleri (14 dosya) |
| K8 Servis | k8-servis/ | Backend servisleri (14 dosya) |
| K9 API | k9-api-routing/ | API uçları ve spesifikasyonlar (14 dosya) |
| K10 Uygulama | k10-uygulama/ | Kullanıcı uygulaması (17 dosya) |
| K11 UX | k11-ux/ | Kullanıcı deneyimi ve arayüz (16 dosya) |
| K12 İzleme | k12-izleme/ | Operasyonel izleme ve alerting (13 dosya) |
| K13 CI/CD | k13-cicd/ | Sürekli entegrasyon ve teslimat (14 dosya) |
| K14 Ağ | k14-ag/ | Ağ iletişimi protokolleri (16 dosya) |
| K15 Medya | k15-medya-streaming/ | Medya işleme ve akış (16 dosya) |
| K16 Amplifikatör | k16-class-ab/ | Class AB amplifikatör devresi — bağımsız katman (22 dosya) |
| K17 Güç Kaynağı | k17-guc-kaynagi/ | Güç kaynağı ve regülasyon — bağımsız katman (16 dosya) |
| K18 Termal | k18-termal/ | Termal tasarım ve soğutma — bağımsız katman (12 dosya) |
| K19 PCB | k19-pcb/ | **PCB Tasarım** (6-layer, kontrollü empedans alt konudur) — bağımsız katman (12 dosya) |
| K20 BOM & Üretim | k20-bom/ | Malzeme listesi ve üretim — bağımsız katman (12 dosya) |
| GitHub Referansları | github-referanslari.md | 21 katman başına açık kaynak referans bölümü |
| Frontend Yeniden Yapılandırma | frontend-restructuring-plan.md | Frontend restructuring planı |
| Katman Bağımlılık Matrisi | katman-baglilik-matrisi.md | Bu dosya (kanonik) |
| Katman Adlandırma Kuralı | adlandirma-kurali.md | K{n}.a.b.c adlandırma dili ve A0-A5 eşlemesi (SSOT) |
| Katman Sayım Rehberi | katman-sayim-rehberi.md | Düğüm sayım birimi, 21 katman tablosu (ADR-026) |
| ADR Metinleri | adr/ | ADR-023 hibrit derinlik · ADR-024 sürücü-firmware birleşimi · ADR-025 K8.2-K15 sınırı · ADR-026 sayım birimi (4 dosya — frozen) |

**Sayım özeti:** 24 klasör (21 katman + firmware + adr + scripts) · 6 kök dosya · toplam 340 MD (2026-09-24 disk: 322 + 8 + 4 + 6). scripts/ klasöründe 1 dosya vardır (katman-sayim.ps1) — `.md` olmadığından toplama girmez. Kök .md dosyaları: index.md · github-referanslari.md · frontend-restructuring-plan.md · katman-baglilik-matrisi.md · adlandirma-kurali.md · katman-sayim-rehberi.md. `.ai\architecture\` altında `.md` OLMAYAN tek dosya: `scripts\katman-sayim.ps1` (dipnot).

---

## 11. Doğrulama Kaydı (2026-09-24)

| # | Doğrulama | Sonuç |
|---|-----------|-------|
| 1 | K15 izin taraması (.ai/ geneli) | üretim/bileşen katmanına izin 0 ✓ (yalnız §2.1 sütun başlığı hariç; log.md'deki tarihsel kayıt kapsam dışı) |
| 2 | Eski sürücü klasörü adı (.ai/architecture/ geneli) | Düzeltilen dosyalarda 0 ✓ (k0 README linki k2-surucu/ olarak düzeltilmişti; adlandirma-kurali.md §6 kural dosyası kapsam dışıdır — kural metni adı zorunlu taşır) |
| 3 | Eski sürücü klasörü dosya sayısı | 0 (12 dosya taşındı, silinmedi) |
| 4 | k2-surucu/ dosya sayısı | 14 (12 taşınan + README + CLAUDE) |
| 5 | mojibake (Kıril alfabesi artığı) | 0 ✓ (§4.3 "60 saniye" olarak düzeltildi) |
| 6 | §7 başlık mojibake | "Güvenlik" olarak düzeltildi ✓ |
| 7 | §10 eski master-index adı | index.md olarak düzeltildi ✓ |
| 8 | K19 tanımı | "PCB" — eski yanlış katman tanımı kaldırıldı (controlled-impedance.md alt konudur) ✓ |
| 9 | Satır sayısı | ≥500 ✓ |

---

*Bu doküman COREMUSIC projesinin resmi mimari referansıdır. Herhangi bir değişiklik için Mimari Komite onayı gereklidir.*
*Authority: Bayram Ali / Vault Steward · Last Updated: 2026-09-24 · Mode: Red Team · Human Mode · Truth Mode*
