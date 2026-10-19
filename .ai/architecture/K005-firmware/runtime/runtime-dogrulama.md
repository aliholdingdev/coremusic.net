---
node_id: K005-firmware-runtime-dogrulama
node_type: dogrulama
status: active
parent_id: K005-firmware-runtime
children: []
depends_on:
  - K005-firmware-runtime-anayasa
  - K005-firmware-runtime-surucu
  - K005-firmware-runtime-ornekler
  - K005-firmware-testing-dogrulama
evidence:
  - .ai/architecture/K005-firmware/runtime/runtime-kapsam.md
  - .ai/architecture/K005-firmware/boot/boot-dogrulama.md
  - .ai/electronic/firmware/index.md
validation: olcum ReadAllLines
open_questions:
  - olcum gerceklesitirilemedi (PLANNED) -> esik degerleri VERIFICATION REQUIRED
group: kanit
---

# K005 · Runtime Doğrulama (çalışma zamanı senaryoları)

> **Doğrulama dosyası (boot-dogrulama kalıbı):** çalışma zamanı için senaryolar (DV-RT serisi),
> test stratejisi, referans değerler ve dürüstlük blokları. Depoda kod **olmadığı** için
> hiçbir senaryo "geçti" diye raporlanmaz — durum `PLANNED` veya `VERIFICATION REQUIRED`'dır.

## §A Doğrulama Senaryoları (DV-RT serisi)

### A.1 Yaşam döngüsü senaryoları

| ID | Senaryo | Beklenen | Durum |
|---|---|---|---|
| DV-RT-01 | Oturum başlat → 10 sn oynat → durdur | Temiz açılış/kapanış, kilit izi yok | PLANNED |
| DV-RT-02 | Ardışık 20 başlat/durdur | Kaynak sızıntısı yok (statik havuz sabit) | PLANNED |
| DV-RT-03 | Cihaz yokken başlatma | Reddi + net hata kodu, çökme yok | PLANNED |
| DV-RT-04 | ASIO cihazı başka uygulamada meşgul | Exclusive Lock reddi, temiz çıkış | PLANNED |
| DV-RT-05 | Duraklat → devam | Kesintisiz geçiş, tampon sıfırlanması kontrollü | PLANNED |

### A.2 Hot-path (callback) senaryoları

| ID | Senaryo | Beklenen | Durum |
|---|---|---|---|
| DV-RT-11 | 512/48kHz blok işleme, 30 dk sürekli | Overrun 0 | PLANNED |
| DV-RT-12 | Kasti gecikme enjeksiyonu (yük) | DEGRADED bayrağı + sıfırlı çıkış | PLANNED |
| DV-RT-13 | 8.1 kanal eşzamanlı akış | Kanal sırası korunur | PLANNED |
| DV-RT-14 | Zincir modülü devre dışı bırakma | Sıfır pop/klik (crossfade/durum) | PLANNED |
| DV-RT-15 | Bit derinliği/format uyuşmazlığı | Red veya reddetme (şartnameye göre) | PLANNED |

### A.3 Bellek/kilit yasak senaryoları

| ID | Senaryo | Beklenen | Durum |
|---|---|---|---|
| DV-RT-21 | Callback süresince tahsis yokluğu denetimi | 0 allocate | PLANNED (araç yok) |
| DV-RT-22 | Callback süresince kilit denetimi | 0 lock | PLANNED (araç yok) |
| DV-RT-23 | BufferPool tükenme | Oturum reddi, sessiz taşma yok | PLANNED |
| DV-RT-24 | Uzun çalışma (24 sa) bellek eğrisi | Düz bellek (büyüme yok) | PLANNED |
| DV-RT-25 | alignas(64) tampon yerleşimi | Hiza ihlali 0 | PLANNED |

### A.4 Koruma/güncelleme entegrasyon senaryoları

| ID | Senaryo | Beklenen | Durum |
|---|---|---|---|
| DV-RT-31 | DC eşiği aşımı → koruma | PROTECT'e geçiş, röle olayı | PLANNED |
| DV-RT-32 | Update bayrağı set (çalışırken) | UPDATE reddi → UPDATING beklemesi | PLANNED |
| DV-RT-33 | Teşhis kuyruğu taşması | Kuyruk dolu → sayac artar, ses kesilmez | PLANNED |
| DV-RT-34 | Saat kilidi kaybı | DEGRADED + rapor, otomatik yükseltme yok | PLANNED |

## §B Test Stratejisi (runtime katmanı)

### B.1 Katman yerleşimi

| Katman | Kapsam | Sahibi (dal) |
|---|---|---|
| Birim | DSP modülü matematiği, durum makinesi geçişleri | testing |
| Bileşen | Engine ↔ DspChain ↔ BufferPool sözleşmeleri | testing + runtime |
| Entegrasyon | Gerçek cihaz/saatle akış | runtime (DV-RT-1x) |
| Performans | Overrun/jitter/latansı ölçümü | runtime (DV-RT-11) |
| Dayanıklılık | Uzun çalışma, kaynak eğrisi | runtime (DV-RT-24) |

### B.2 Çalıştırma ortamı öncelik sırası

1. **Öncelik 1 — hedef donanım** (gerçek cihaz + saat): DV-RT-1x/3x bu ortamda bağlayıcıdır.
2. **Öncelik 2 — simüle cihaz** (yazılım sürücü soyutlaması): yalnız sözleşmeyi doğrular.
3. **Öncelik 3 — statik denetim** (kaynak kod incelemesi/araç): kural yasaklarını tarar —
   ancak depoda kod **olmadığından** bu alt adım şu an UYGULANAMAZ (§Z dürüstlük).

### B.3 Kabul eşiği (kural bazlı, ölçülmemiş)

| Ölçüm | Eşik | Kaynak | Ölçüm durumu |
|---|---|---|---|
| Overrun | 0 (30 dk) | koşul | PLANNED |
| Allocate/lock | 0 (callback) | CoreMusic kuralları 1–2 | PLANNED |
| Bellek büyümesi | 0 (24 sa) | koşul | PLANNED |
| Kilitlenme/kilit izi | 0 | koşul | PLANNED |
| Kanal sırası hatası | 0 | 8.1 şartnamesi | PLANNED |

## §D Referans Değerler

| Değer | Sayı | Kaynak | Not |
|---|---|---|---|
| Örnek/blok | 512 | sartname (ölçüm PLANNED) | Varsayılan blok |
| Örnekleme | 48 kHz | sartname (ölçüm PLANNED) | Varsayılan |
| Kanal | 8.1'e kadar | K005 kapsamı | Üst sınır |
| Veri tipi | 32-bit float | ADR-017 | Zincir içi |
| RTOS görev üst sınırı | 32 | firmware index §5 | Statik tahsis |
| Bellek modeli | statik/önceden ayrılmış | firmware index §5 | Heap yasak |
| Watchdog | donanım zorunlu | firmware index §5 | Runtime'da beslenir |
| ASIO SDK sürümü | — | kanıt yok | ⚠️ VERIFICATION REQUIRED |
| JUCE sürümü | — | kanıt yok | ⚠️ VERIFICATION REQUIRED |
| Ölçüm sonuçları (gerçek) | — | ölçüm yapılmadı | PLANNED |

## §X Dürüstlük Blokları

### X.1 Bu dosyanın üretimi hakkında

1. **0 URL, 0 repo iddiası, 0 sürüm uydurması** ile üretilmiştir.
2. **0 çalıştırılan test, 0 ölçüm** — tüm senaryolar `PLANNED` işaretlidir; "geçti" iddiası yok.
3. **Kanıt dosyaları (salt okunur):** `.ai/architecture/K005-firmware/runtime/runtime-anayasa.md` ·
   `runtime-surucu.md` · `runtime-ornekler.md` · `boot/boot-dogrulama.md` ·
   `.ai/electronic/firmware/index.md` (§5 RTOS, §9 cihaz tablosu).
4. **Kod gerçeği:** 2026-10-15 glob → depoda `*.cpp` 0 · `*.h` 0. Bu dosyadaki hiçbir senaryonun
   karşılığı depoda çalışan bir test **yoktur**.
5. **B.2-3 statik denetim** de kod olmadığından uygulanamaz durumdadır; uygulanabilirlik için
   depoya gerçek kaynak gelmesi gerekir.

### X.2 Erişim sınırı

1. Erişilemeyen tekil iddialar `⚠️ VERIFICATION REQUIRED` ile işaretlenmiştir
   (ASIO SDK/JUCE sürümü, gerçek ölçüm sonuçları).
2. `⚠️` işaretli satırlar **geçmiş** gibi okunamaz; kapı (gate) değerlendirmesinde
   "kanıtlanmış" sayılmaz.

## §E Ölçüm Yöntemi (derinleştirme eki)

### E.1 Ölçüm araç zinciri (mantıksal)

| # | Adım | Araç sınıfı | Durum |
|---|---|---|---|
| 1 | Callback süresi zaman damgası | donanım/sürücü sayacı | PLANNED |
| 2 | Overrun/xrun sayacı okuma | runtime sayacı (F.3 benzeri) | PLANNED |
| 3 | Bellek eğrisi örnekleme | sistem sayacı | PLANNED |
| 4 | Tahsis/kilit denetimi | statik analiz aracı | KOD YOK → UYGULANAMAZ |
| 5 | Sonuç kaydı (vault) | Markdown rapor | Üretildiğinde |

1. **Zincir kuralı:** 1–3 ancak gerçek cihazda anlamlıdır; 4 depoya kod gelmeden **çalıştırılamaz**.
2. **Araç adı uydurmak yasaktır** — bu dosya araç **adı vermez**; yalnız araç **sınıfı** tarif eder.

### E.2 Örnekleme düzeni (mantıksal)

| Parametre | Değer | Gerekçe |
|---|---|---|
| Sıcak test süresi | 30 dk kesintisiz | DV-RT-11 için yeterlilik (kabul eşiği) |
| Dayanıklılık süresi | 24 sa | DV-RT-24 bellek eğrisi |
| Bellek örnekleme aralığı | dakikada 1 | Büyüme tespiti için pratik aralık |
| Tekrar sayısı (yaşam döngüsü) | 20 | DV-RT-02 sızıntı birikimi |
| Sonuç saklama | vault Markdown | Tekrar üretilebilirlik |

1. **Bu düzen bir sonuç DEĞİLDİR** — henüz hiçbir ölçüm çalışmamıştır (0 test).

### E.3 Sonuç kayıt şeması (boş kalıp)

| Alan | İçerik | Dolu mu? |
|---|---|---|
| Senaryo ID | DV-RT-xx | Hayır |
| Tarih/ortam | cihaz + sürücü | Hayır |
| Örneklenen sayı | overrun/xrun/bellek | Hayır |
| Eşikle karşılaştırma | §B.3 tablosu | Hayır |
| Karar | GEÇTİ / KALDI | Hayır |
| Kanıt bağlantısı | log/rapor yolu | Hayır |

1. **Doldurma kuralı:** yalnız gerçek çalıştırma sonrası; şablonun "Hayır" satırları
   "Evet" olmadan hiçbir senaryo "geçti" sayılmaz.

## §F Test Verisi ve Hedefler (derinleştirme eki)

### F.1 Uyarıcı (stimulus) sınıfları

| # | Uyarıcı | Amaç | İlgili senaryo |
|---|---|---|---|
| 1 | Sürekli sinüs (referans) | Bozulmadan geçiş | DV-RT-11 |
| 2 | Beyaz gürültü | Zincir yükü | DV-RT-12 |
| 3 | Sessizlik + ani geçiş | Pop/klik | DV-RT-14 |
| 4 | DC kaydırmalı sinyal | DC koruma | DV-RT-31 |
| 5 | Kanal başına ayrı sinyal | Kanal sırası | DV-RT-13 |
| 6 | Format uyuşmaz paket | Açılış reddi | DV-RT-15 |

1. **Üretim:** uyarıcılar **dahili** üretilir (vault şartnamesi düzeyinde tarif); harici
   dosya/URL iddiası yok.

### F.2 Gözlemlenen (observed) ölçütler

| Ölçüt | Tanım | Eşik (kural) |
|---|---|---|
| Overrun sayısı | blok süresi aşımı olayı | 0 |
| Xrun sayısı | donanım taşması olayı | 0 |
| Allocate/lock | callback içi tahsis/kilit | 0 |
| Bellek artışı | başlangıçtan fark | 0 (24 sa) |
| Kanal sırası hatası | beklenen ≠ gelen kanal | 0 |
| Pop/klik olayı | ani sıçrama (sınır aşıldı) | 0 |
| Kilit izi | bırakılmayan lock | 0 |

## §K Senaryo → Kural Bağı (derinleştirme eki)

| Kural | Kaynak | Senaryo | Durum |
|---|---|---|---|
| Kural 1 — 0 tahsis | CoreMusic kural seti | DV-RT-21 | PLANNED (araç yok) |
| Kural 2 — lock-free | CoreMusic kural seti | DV-RT-22 | PLANNED (araç yok) |
| Kural 3 — noexcept | CoreMusic kural seti | statik denetim | KOD YOK |
| Kural 5 — alignas(64) | CoreMusic kural seti | DV-RT-25 | PLANNED |
| Kural 7 — 32-bit float | ADR-017 | DV-RT-15 | PLANNED |
| Kural 8 — 512/48k | sartname (ölçüm PLANNED) | DV-RT-11 | PLANNED |
| Kural 9 — tek Exclusive Lock | CoreMusic kural seti | DV-RT-04 | PLANNED |
| Kural 10 — DC >0.5V röle | CoreMusic kural seti | DV-RT-31 | PLANNED |
| Heap yasak | firmware index §5 | DV-RT-23/24 | PLANNED |
| 32 görev sınırı | firmware index §5 | RtosBridge denetimi | PLANNED |

1. **Eksik eşleşme:** her kuralın en az bir senaryosu vardır; karşılığı olmayan kural
   **eklenmez** (uydurma yasak).

## §L Risk → Senaryo Bağı (derinleştirme eki)

| # | Risk (runtime-kapsam §O) | Senaryo | Senaryonun sınamadığı şey |
|---|---|---|---|
| 1 | Exclusive Lock çakışması | DV-RT-04 | Temiz reddetme davranışı |
| 2 | Callback overrun | DV-RT-11/12 | Sıfırlı çıkış + bayrak |
| 3 | Serbest tahsis sızıntısı | DV-RT-24 | 24 sa düz bellek eğrisi |
| 4 | 8.1 kanal eşleşmezliği | DV-RT-13/15 | Kanal sırası + format reddi |
| 5 | Saat kilidi kaybı | DV-RT-34 | DEGRADED'e geçiş |
| 6 | Kilitli yol | DV-RT-22 | 0 lock (araç gelince) |
| 7 | Kod yokken "çalışıyor" raporu | X.1-4 | Bu dosyanın dürüstlük beyanı |

## §N Kapsanan Kapsam (coverage) Matrisi

| Kapsam alanı (runtime-kapsam) | Senaryo grubu | Kapsama | Açıklık |
|---|---|---|---|
| §A.1 yaşam döngüsü | DV-RT-01..05 | Tam (planlı) | Ölçüm yok |
| §B bileşen envanteri | DV-RT-01..34 | Kısmi | Kod yok → bileşen testi yok |
| §C.3 hata sözleşmesi | DV-RT-03/15/34 | Tam (planlı) | — |
| §E.3 DSP zinciri | DV-RT-14 | Kısmi | Modül içi doğrulama testing dalında |
| §E.4 bellek havuzu | DV-RT-23/24 | Tam (planlı) | — |
| §F.2 görev haritası | DV-RT-12 | Kısmi | Bare-metal/FreeRTOS farkı ölçülmüyor |
| §J durum makinesi | DV-RT-31/32/34 | Kısmi | `UPDATING` geçişi yalnız bayrak düzeyi |
| §M kural uyumu | DV-RT-21/22/25 | Kısmi | Statik denetim UYGULANAMAZ (kod yok) |

1. **Açık alan:** §C.1 (dahili arayüz sözleşmeleri) — bileşenler kod olmadığından **doğrudan
   senaryo yok**; yalnız yaşam döngüsü senaryolarıyla dolaylı sınanır.

## §R Raporlama ve Kapı İlişkisi (derinleştirme eki)

### R.1 Rapor akışı

```text
DV-RT-xx çalıştır (hedef donanım)
  → E.3 şemasını doldur
  → §B.3 eşiğiyle karşılaştır
  → GEÇTİ/KALDI kararı (testing dalı kapıyı verir)
  → vault raporu (dosya yolu + tarih)
```

### R.2 Karar yetkisi

| Adım | Sahibi | Not |
|---|---|---|
| Senaryo çalıştırma | runtime | Bu dosyanın senaryoları |
| Ölçüm kaydı | runtime | E.3 şeması |
| Kapı kararı (GEÇTİ/KALDI) | testing dalı | runtime kararı **vermez** |
| Vault raporu | Vault Steward | Kapanışta |

1. **Ayrım zorunlu:** runtime **kanıt toplar**, testing **karar verir** — ikisi birleştirilmez.

## §Z Erişilemeyen / Üretilmemiş Öğeler

| # | Öğe | Neden | İşaret |
|---|---|---|---|
| 1 | Gerçek test sonuçları | 0 test çalıştırıldı | PLANNED |
| 2 | Araç adları/log yolları | Araç kurulmadı; ad uydurmak yasak | — |
| 3 | ASIO SDK / JUCE sürümleri | Sürüm kanıtı yok | ⚠️ VERIFICATION REQUIRED |
| 4 | Ölçülmüş gecikme/overrun sayıları | Ölçüm yapılmadı | ⚠️ VERIFICATION REQUIRED |
| 5 | Depo kod satırı referansı | 0 cpp/h (2026-10-15) | Kod YOK |
| 6 | Harici URL/doküman | 0 URL ile üretildi | — |

## §W Öncelik ve Sıra (derinleştirme eki)

### W.1 Senaryo öncelik sırası (ilk çalıştırma planı)

| Öncelik | Senaryo grubu | Neden ilk | Tahmini yer |
|---|---|---|---|
| P0 | DV-RT-01/03/04 | Yaşam döngüsü + lock reddi — temel çalışırlık | İlk oturum |
| P0 | DV-RT-11 | Overrun=0 kabul eşiği (B.3) | İlk oturum |
| P1 | DV-RT-21/22 | Kural 1–2 denetimi (araç gelince) | Araç kurulumundan sonra |
| P1 | DV-RT-13/15 | Kanal/format reddi — 8.1 şartnamesi | Cihaz bağlanınca |
| P1 | DV-RT-31 | DC koruma — kural 10 | Koruma yolu hazırken |
| P2 | DV-RT-24 | 24 sa dayanıklılık | Gece döngüsü |
| P2 | DV-RT-32/33/34 | Entegrasyon bayrakları | İlgili dallarla |

1. **Sıra kuralı:** P0 grubu geçmeden P1/P2 **başlamaz** — temiz çalışırlık önce, derinlik sonra.
2. **Bağımlılık:** DV-RT-21/22 öncelikle **araca** bağlıdır; araç yoksa bu iki senaryo
   `UYGULANAMAZ` olarak kalır, atlanıp "geçti" sayılmaz.

### W.2 Ortam matrisi

| Ortam | Senaryo kapsamı | Bağlayıcılık |
|---|---|---|
| Hedef donanım (gerçek cihaz) | DV-RT-01..05 · 11..15 · 23..25 · 31..34 | **Evet** — kapı kararı bu ortamdan |
| Simüle cihaz (yazılım soyutlama) | DV-RT-01/05/14/23 | Kısmi — yalnız sözleşmeyi sınar |
| Statik denetim (araç) | DV-RT-21/22 + kural 3 | Araç yok → UYGULANAMAZ |
| Kod yokluğu durumu | — | 2026-10-15: tüm statik adımlar UYGULANAMAZ |

1. **Öncelik hiyerarşisi** (B.2 ile aynı): hedef donanım > simülasyon > statik; hiyerarşi
   tersine çevrilemez (simülasyon "geçti" diye hedef donanım "geçti" sayılmaz).

## §Y Senaryo Ayrıntıları (seçili, derinleştirme eki)

### Y.1 DV-RT-11 — kesintisiz akış (detay)

| Adım | Eylem | Gözlem |
|---|---|---|
| 1 | 512/48kHz, 8.1 kanal oturumu başlat | Açıkılış kodu 0 |
| 2 | Referans sinyali 30 dk besle | overrun=0, xrun=0 |
| 3 | Her dk teşhis sayacını oku | Sayıcı düzlemsi (artış=0) |
| 4 | Oturumu durdur | Temiz kapanış, kilit izi 0 |

**Başarı:** 30 dk boyunca overrun 0 ve xrun 0 · **Başarısızlık:** herhangi bir olay → KALDI
**Durum:** PLANNED — çalıştırılmadı.

### Y.2 DV-RT-04 — Exclusive Lock çakışması (detay)

| Adım | Eylem | Beklenen |
|---|---|---|
| 1 | Cihazı başka bir süreç meşgul tut (simüle) | — |
| 2 | engine.open() çağır | Lock reddi |
| 3 | Hata kodunu gözle | Tek net hata, çökme yok |
| 4 | Açıkılan kaynak var mı | Geri alma izi temiz |

**Başarı:** reddetme + temiz çıkış · **Kural:** 9 — tek uygulama Exclusive Lock
**Durum:** PLANNED.

### Y.3 DV-RT-31 — DC koruma (detay)

| Adım | Eylem | Beklenen |
|---|---|---|
| 1 | Çalışan oturumda DC kaydırmalı sinyal | Zincir DC filtresi çalışır |
| 2 | Eşik aşıldığını bildir | PROTECT bayrağı set |
| 3 | Koruma olayı sayacı | +1 |
| 4 | Eşik düşüşü | Kontrollü kapanış (J-11) |

**Başarı:** PROTECT her durumdan geçer, otomatik "yükselme" yok
**Kural:** 10 (DC >0.5V → röle) · **Durum:** PLANNED.

### Y.4 DV-RT-24 — 24 sa bellek eğrisi (detay)

| Adım | Eylem | Gözlem |
|---|---|---|
| 1 | Oturumu başlat, referans akış | Başlangıç belleği (taban) |
| 2 | 24 sa çalıştır, dakikada 1 örnekle | Eğri |
| 3 | Başlangıç - bitiş karşılaştırması | Fark = 0 hedefi |
| 4 | Zaman aşımı/overrun birikimi | Sayıcılar düzlemsi |

**Başarı:** bellek farkı 0 · **Başarısızlık:** eğilim (büyüme) → KALDI
**Durum:** PLANNED.

## §Ç Hata Sınıfları ve Tepki Tablosu (derinleştirme eki)

| # | Hata sınıfı | Tetikleyici | Tepki | Teşhis |
|---|---|---|---|---|
| 1 | `OPEN_REFUSED_LOCK` | Başka süreç lock'ta | Reddi, temiz çıkış | Hata kodu |
| 2 | `OPEN_REFUSED_FORMAT` | 48k/512/float uyuşmazlığı | Reddi | Beklenen≠bulunan |
| 3 | `OPEN_REFUSED_POOL` | Havuz tükenme | Reddi (çalışma anı tahsis yok) | Kapasite kaydı |
| 4 | `RUN_OVERRUN` | Blok süresi aşıldı | DEGRADED + sıfırlı çıkış | overrun sayacı |
| 5 | `RUN_XRUN` | Donanım taşması | xrun sayacı + kurtarma denemesi | xrun sayacı |
| 6 | `RUN_CLOCK_LOST` | Saat kilidi kaybı | DEGRADED, otomatik dönüş yok | Son hata kodu |
| 7 | `PROTECT_DC` | DC eşiği | PROTECT + röle olayı | protect sayacı |
| 8 | `PROTECT_THERMAL` | Termal eşik | PROTECT + röle olayı | protect sayacı |
| 9 | `QUEUE_FULL` | Teşhis kuyruğu dolu | Sayaç artar, ses kesilmez | Kayıp sayacı |

1. **Ortak kural:** hiçbir hata sınıfı ses yolunda **bloke edici** tepki üretmez (kural 2).
2. **Kayıp sayacı:** kuyruk taşmasında olay **kaybolur ama sayılır** — sessiz kayıp yoktur.

## §İ İz ve Kanıt Alanları (derinleştirme eki)

| Alan | Tip | Kanıt değeri | Dolu mu? |
|---|---|---|---|
| Açılış/kapanış kodu | sabit kod | Yaşam döngüsü kanıtı | Hayır (0 çalıştırma) |
| overrun/xrun sayacı | sayaç | Hot-path kanıtı | Hayır |
| Bellek taban/bitiş | sayı | Sızıntı kanıtı | Hayır |
| Kilit alma/bırakma izi | dizi | Kural 9 kanıtı | Hayır |
| Protect olay sayısı | sayaç | Kural 10 kanıtı | Hayır |
| Oturum parametre kaydı | yapı | Şartname uyumu (512/48k) | Hayır |

1. **Kanıt zinciri:** alan → E.3 şeması → vault raporu → testing kapı kararı; zincirde halka
   eksikse "kanıt" **tamamlanmamıştır**.
2. **Şu anki halka durumu:** yalnız **plan** var; çalışan/ölçülmüş halka **yok** (0 test).

## §F2 (devam) Fabrika/Kabul İlişkisi (derinleştirme eki)

### F2.1 Düzey ayrımı

| Düzey | Ne sınamaz | Sahibi | Bu dosyadaki karşılığı |
|---|---|---|---|
| Fabrika (üretim sonu) | Yazılım davranışı | Üretim hattı | Kapsam dışı |
| Alıcı kabul (SA) | Donanım + firmware bütünü | testing + hardware | DV-RT-31/34 (parça) |
| Yazılım doğrulama | Runtime davranışı | runtime | DV-RT serisi (bu dosya) |

1. **Ayrım nedeni:** fabrika testi "geçti" diye runtime senaryoları **geçmiş sayılmaz**;
   iki düzey farklı kanıt üretir ve birbirinin yerine kullanılamaz.

### F2.2 Bu dosyanın test türü dağılımı

| Test türü | Senaryolar | Oran (planlı) |
|---|---|---|
| Birim (dahili) | Yok — bileşen kodu yok | 0% |
| Bileşen/entegrasyon | DV-RT-01..05 · 13..15 · 31..34 | ~%60 |
| Performans (hot-path) | DV-RT-11/12 | ~%20 |
| Dayanıklılık | DV-RT-24 | ~%10 |
| Statik denetim | DV-RT-21/22/25 | ~%10 (UYGULANAMAZ: kod yok) |

1. **Boşluk beyanı:** birim test oranı **%0'dır** — depoda 0 cpp/h olduğu için birim test
   **mümkün değildir**; bu bir eksiklik değil, mevcut durumun dürüst kaydıdır.

## §O Ortak Hatalar ve Kaçınılan Tuzaklar (derinleştirme eki)

| # | Tuzak | Bu dosyadaki koruma |
|---|---|---|
| 1 | "Planlı senaryo"yu "geçen test" gibi raporlamak | Her satırda `Durum: PLANNED` (§A) |
| 2 | Araç adı/dosya yolu uydurmak | E.1-2: yalnız araç **sınıfı**, ad yok |
| 3 | Simülasyon "geçti"yi hedef donanım "geçti" saymak | W.2 bağlayıcılık sütunu |
| 4 | Eşikleri ölçüm sonucu gibi sunmak | B.3 "kural bazlı, ölçülmemiş" |
| 5 | Kod yokken kod referansı vermek | §Z-5: 0 cpp/h (2026-10-15) |
| 6 | Sürüm iddiası eklemek | §Z-3: ASIO/JUCE `⚠️ VERIFICATION REQUIRED` |
| 7 | Test kararını runtime'ın vermesi | R.2: kapı kararı testing dalında |
| 8 | Boş şablonu dolu kanıt sanmak | E.3 "Dolu mu? = Hayır" satırları |

## §U Kullanım Kılavuzu (derinleştirme eki)

### U.1 Kim ne zaman okur?

| Okuyucu | Ne arar | Bölüm |
|---|---|---|
| runtime uygulayıcısı (gelecek kod) | Çalıştırılacak senaryo listesi | §A, §Y |
| performans sorumlusu | Ölçüm yöntemi + eşik | §B, §E |
| testing (kapı sahibi) | Senaryo→kural/risk bağı + karar yetkisi | §K, §L, §R |
| Vault Steward | Dürüstlük beyanları + açık öğeler | §X, §Z |
| Gözden geçiren (review) | Coverage boşlukları | §N |

### U.2 Bu dosyanın güncellenme koşulu

| Tetikleyici | Güncelleme | Kural |
|---|---|---|
| Depoya ilk cpp/h eklendiği | §Z-5, §F2-2, §E.1-4 satırları | Yeni ölçüme kadar "KOD YOK" kalır |
| Gerçek ölçüm çalıştırıldığı | E.3 şeması doldurulur + §A "Durum" sütunu | "PLANNED" yalnız çalışan senaryoda değişir |
| Yeni kural/ADR eklendiği | §K tablosuna satır | Karşılığı olmayan kural eklenmez |
| Yeni risk tanımı | §L tablosuna satır | runtime-kapsam §O ile eşleşmeli |

1. **Append-only:** geçmiş ölçüm/plan satırları **silinmez**; düzeltme yeni satır/ek ile yapılır.

## §GRUPLAR (özet)

| # | Dosya | `group` | Rol |
|---|---|---|---|
| 1 | `runtime-anayasa.md` | mimari | Çalışma zamanı anayasası |
| 2 | `runtime-surucu.md` | mimari | Hot-path / sürücü sözleşmesi |
| 3 | `runtime-ornekler.md` | kod | Md örnek şablonları (0 cpp/h) |
| 4 | `runtime-kapsam.md` | mimari | Sınır/bileşen/bağımlılık (yeni) |
| 5 | `runtime-dogrulama.md` | kanit | Bu dosya — DV-RT serisi (yeni) |
| 6 | `runtime/index.md` | mimari | Dal indeksi |

## §B Açıklama Sözlüğü (derinleştirme eki)

| Kısaltma/Terim | Tam anlamı | Bu dosyadaki kullanımı |
|---|---|---|
| DV-RT | Doğrulama — Runtime | Senaryo kimlik serisi (§A) |
| Xrun | Donanım tampon taşması (eksik/fazla veri) | §F.2 ölçütü |
| Overrun | İşlemenin blok süresini aşması | §F.2 ölçütü |
| PLANNED | Planlanmış, çalıştırılmamış | Tüm senaryo durumları |
| UYGULANAMAZ | Koşum (ortam/araç) yok | Statik denetim adımları |
| Kapı (gate) | GEÇTİ/KALDI kararı veren eşik | R.2 testing yetkisi |
| Dürüstlük bloğu | Kanıt durumu beyanı bölümü | §X, §Z |
| Append-only | Yalnız ekleme, silme yok | U.2-1 |
| Statik denetim | Derleme/çalışma öncesi kod incelemesi | E.1-4 (kod yok → UYGULANAMAZ) |
| Hedef donanım | Gerçek cihaz + saat zinciri | W.2 (bağlayıcı ortam) |

## §B2 Dosya İçi Bağlantılar (derinleştirme eki)

| Bağlantı | Hedef | Amaç |
|---|---|---|
| Senaryo → Kapsam iddiası | `runtime-kapsam.md` §A–§S | Her senaryonun sınadığı kapsam satırı |
| Eşik → Kural | Kural 1–10 + ADR-017 + firmware index §5 | Sayısal sınırın kaynağı |
| Karar → testing dalı | `testing-dogrulama.md` | Kapı yetkisi devri |
| Teşhis → diagnostics dalı | `diagnostics-kapsam.md` | Kuyruk/olay akışı |
| Koruma → power-control dalı | `power-control-anayasa.md` | PROTECT bayrağı kaynağı |
| Güncelleme → update dalı | `update-anayasa.md` | UPDATING kapısı kaynağı |

1. **Tek yönlülük:** bu dosya yalnızca **bağlantı verir**; bağlantının sahibi olduğu dosyanın
   içeriğini **kopyalamaz** (SSOT korunur).
2. **Kapanış durumu (2026-10-15):** tüm DV-RT senaryoları `PLANNED`; 0 test çalıştırıldı;
   0 ölçüm alındı; depoda 0 cpp/h.

## §T Zamanlama Doğrulama Eki — RTOS Kaynağı Bağlantısı (derinleştirme eki)

> Dayanak: `Dosya: .ai/electronic/firmware/rtos.md` (541 satır, okundu 2026-10-16) — bu ek yalnız SSOT'tan **işaretçe** yararlanır, içeriğini kopyalamaz.

| # | Doğrulama konusu | RTOS kaynağı (satır) | Bu dosyadaki karşılık |
|---|---|---|---|
| T1 | WCET ölçümü ve zamanlama doğrulama yöntemi | rtos.md:219 (§12) | §E ölçüm yöntemi |
| T2 | Zaman kısıtlı test/doğrulama matrisi | rtos.md:403 (§22) | §B test stratejisi |
| T3 | Watchdog / sağlık izleme kapıları | rtos.md:377 (§21) | §Ç hata sınıfları |
| T4 | Priority inversion ↔ ceiling doğrulama | rtos.md:157 (§9) | §A DV-RT senaryoları |
| T5 | ISR süresi / kesme tasarımı kısıtları | rtos.md:201 (§11) | §A senaryo ön koşulu |

**Kapanış (2026-10-16):** ek satır = artı doğrulama bağı; DV-RT senaryoları hâlâ `PLANNED`, çalıştırılan test 0, ölçüm 0, depoda 0 cpp/h — değişiklik yok, yalnız bağıl referans eklendi.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
