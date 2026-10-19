---
node_id: K002-electroncis/electroncis-kapsam
node_type: module
status: draft
parent_id: K002-electroncis
children: []
depends_on:
  - K001-donanım
  - K004-dsp
  - K005-firmware
evidence:
  - ".ai/architecture/K000-isletim-sistemi/electroncis/** (4 dosya — komşu sınır kanıtı)"
  - ".ai/electronic/** (60 dosya / 8.095 satır — ham envanter ölçümü 2026-10-13)"
  - "K002 index.md §6 açık sorular (Q-K2-1..5)"
validation: "draft — bu turda yazıldı; kapsam ayrım tablosu §G, 3 açık soru (Q-K2-1..3) üst karar bekliyor"
open_questions:
  - "Q-K2-1: Kart/PCB tasarımı K002'de mi K001'de mi?"
  - "Q-K2-2: Gömülü kod yüzeyi K005'te mi, donanım şartnamesi K002'de mi?"
  - "Q-K2-3: DSP zinciri K004 mü, analog ön zincir K002 mi?"
group: mimari
---

# electroncis-kapsam — Kapsam ve Sınır Defteri

> **Yazım dokunulmaz:** `electroncis` (v2 §3 KURAL 1). Bu dosya K002 domain'inin
> neyi İÇERDİĞİNİ ve neyi DIŞARIDA BIRAKTIĞINI kayıt altına alır; §G ayrım
> tablosu üç katmanı (K002 · K000/electroncis · .ai/electronic) tek yerde ayırır.

## §A ELI15 — Kapsamın tek cümlesi

Bu domain **elektronik donanımın tasarımı, üretimi ve testi**ni kapsar: şemadan
karttan cihaza, oradan da ölçüm masasına kadar. İşletim sistemi kodu, DSP
algoritması, gömülü firmware ve ham içerik vault'u bu domainin DIŞINDADIR —
onlar komşu katmanlardır; bu dosya sınırı çizer, içeriği tekrar etmez.

| # | Alan | Değer |
|---|---|---|
| 1 | node_id | `K002-electroncis/electroncis-kapsam` |
| 2 | Kademe | 1-domain altı modül (Kademe-2 alt-konuların üstünde) |
| 3 | Kapsam özeti | 5 alt-konu sınırı + 7 komşu ayrımı |
| 4 | Ham kaynak | `.ai/electronic/**` (60 dosya / 8.095 satır) |
| 5 | Durum | `draft` (A1 tur-1) |
| 6 | Üst karar bekleyen | 3 sınır sorusu (Q-K2-1..3) |
| 7 | Yazım | `electroncis` — dokunulmaz |

## §B Kapsam İÇİ — 5 Alt-Konu Sınırı

### §B.1 devre-ve-analog

| # | Kapsam içi | Kapsam dışı (gitdiği yer) |
|---|---|---|
| 1 | Pasif bileşenler (R/C/L), tolerans, ESR, SRF | Satın alma envanteri → K001-donanım |
| 2 | Op-amp topolojileri, kazanç bant | DSP filtre algoritması → K004-dsp |
| 3 | Class AB / Class D güç aşaması şeması | Amfi firmware koruması → K005-firmware |
| 4 | Filtre devreleri (Sallen-Key, LC) | Yazılım EQ → K004-dsp |
| 5 | Osilatör, PLL, saat devresi (analog yüzey) | PLL register programlama → K005-firmware |
| 6 | ADC/DAC ön zinciri (referans, anti-aliasing) | DAC register map kodu → K005-firmware |

**Kanal:** ADR-038 H001 — DAC yolu yalnız PCM3168A; PCM5122 ABSOLUTELY FORBIDDEN.

### §B.2 dijital-ve-mikroislemci

| # | Kapsam içi | Kapsam dışı (gittiği yer) |
|---|---|---|
| 1 | MCU/DSP mimarisi kararı (XMOS XU316 — PLANNED) | Çekirdek boot kodu → K000-isletim-sistemi |
| 2 | GPIO/PLL/pin mux donanım tasarımı | HAL/Sürücü kodu → K000/electroncis |
| 3 | Saat kararlılığı, jitter şartnamesi | Saat senkron algoritması → K004-dsp |
| 4 | Bellek haritası (donanım tarafı) | Bellek yönetimi OS → K000 |
| 5 | I2S/SPI/I2C fiziksel arayüz şeması | I2S driver kodu → K005-firmware |
| 6 | Boot zinciri zamanlama ŞARTNAMESİ | Boot kodu uygulaması → K005-firmware |

**Kanıt:** `.ai/electronic/firmware/boot-sequence.md:28-43` (boot zinciri 8 aşama).

### §B.3 guc-yonetimi

| # | Kapsam içi | Kapsam dışı (gittiği yer) |
|---|---|---|
| 1 | AC-DC, DC-DC topoloji seçimi (boost, flyback) | PSU firmware PFC kodu → K005-firmware |
| 2 | LDO seçimi, dropout, PSRR şartı | — |
| 3 | Termal tasarım (heatsink, θ<1°C/W) | Fan kontrol algoritması → K005/K004 ⚠️ VERIFICATION REQUIRED |
| 4 | Koruma devresi (DC offset rölesi, over-current) | Koruma gömülü kodu → K005-firmware |
| 5 | Güç kaynağı şeması (vault: power-supply.md:24-80) | — |
| 6 | Soft-start, inrush, kademeli açılış | — |

**Kanal:** DC Offset >0.5V → koruma rölesi ZORUNLU (kural 10).

### §B.4 sensor-ve-akteyici

| # | Kapsam içi | Kapsam dışı (gittiği yer) |
|---|---|---|
| 1 | Sensör arayüz devresi (INA219, ADS1115 front-end) | Sensör füzyon algoritması → K004-dsp |
| 2 | Kestirim/pre-filter (analog veya basit dijital) | Kestirim DSP → K004-dsp |
| 3 | Kalibrasyon prosedürü ve düzeltme tablosu | Kalibrasyon UI → K0xx ⚠️ VERIFICATION REQUIRED |
| 4 | Aktüatör sürücü devresi (DRV8825, DRV8874) | Sürücü mikro-kodu → K005-firmware |
| 5 | Kapalı çevrim donanım halkası | Kapalı çevrim kontrol yasası → K004-dsp |
| 6 | Cihaz algılama/hot-plug devre düzeyi | Hot-plug OS mantığı → K000/electroncis |

### §B.5 imalat-ve-test

| # | Kapsam içi | Kapsam dışı (gittiği yer) |
|---|---|---|
| 1 | PCB üretimi (Gerber, panel, reflow) | Web üretim takibi → PHP katmanı (K0xx) |
| 2 | DFT: test noktaları, ICT/FCT planı | Test otomasyon kodu → K005/QA ajanı |
| 3 | AOI/X-ray muayene kriteri | — |
| 4 | Kalibrasyon testi (SNR, THD+N) | Ölçüm UI/script → QA/K004 ⚠️ VERIFICATION REQUIRED |
| 5 | EMC ön hazırlık (pre-scan) | Sertifikasyon → dış laboratuvar |
| 6 | Burn-in / yaşlanma (8 saat) | — |

**Kanıt:** `.ai/electronic/development-workflow.md:416-434` (Faz 15 test).

## §C Kapsam DIŞI — Komşu Ayrım (özet; detay §G)

| # | Dış katman | Neden dışarıda | Ayrım cümlesi |
|---|---|---|---|
| 1 | `K000-isletim-sistemi` | OS ≠ elektronik | Çekirdek, sürücü, dosya sistemi |
| 2 | `K000-isletim-sistemi/electroncis` | OS↔donanım KESİŞİMİ | Sürücü ve cihaz algılama yazılımı |
| 3 | `K001-donanım` | Var olan ≠ tasarlanan | Fiziksel envanter (disk, ses kartı) |
| 4 | `K004-dsp` | Algoritma ≠ devre | Sinyal işleme zinciri |
| 5 | `K005-firmware` | Kod ≠ donanım | Gömülü yazılım |
| 6 | `.ai/electronic/**` | Ham içerik ≠ işlenmiş vault | 60 dosya kaynak, bu node işlenmiş |
| 7 | `.ai/architecture/index.md` | Registry ≠ içerik | YALNIZ orkestratör yazar |

## §D Kısıtlar (bağlayıcı — bu domainde geçerli)

| # | Kısıt | Kaynak | Domain etkisi |
|---|---|---|---|
| 1 | DAC yalnız PCM3168A; PCM5122 yasak | ADR-038 H001 | §B.1, tüm DAC referansları |
| 2 | Ses verisi 32-bit float | ADR-017 | §B.2 dijital yol |
| 3 | 512 örnek buffer, 48kHz | sartname (ölçüm PLANNED) | §B.2 zamanlama bütçesi |
| 4 | DC Offset >0.5V → röle zorunlu | Kural 10 | §B.3 koruma |
| 5 | SNR >100dB · THD+N <0.01% @1kHz | sartname (ölçüm PLANNED) | §B.5 test kapıları |
| 6 | ASIO Exclusive Lock — tek uygulama | Kural 9 | K000 sınırı (bu domainde değil) |
| 7 | JUCE 9 / ASIO SDK 2.3.4 sürüm kanıtı yok | Kural 12 | K000/K005 — bu domainde değil |
| 8 | Yazım `electroncis` | v2 §3 KURAL 1 | Tüm dosya/dizin adları |

## §E Ölçüm Kapıları (kapsam içi — ne ölçülür)

| # | Kapı | Eşik | Durum |
|---|---|---|---|
| 1 | SNR (A-wtd) | >100dB | sartname — ölçüm PLANNED |
| 2 | THD+N @1kHz 1W | <0.01% | sartname — ölçüm PLANNED |
| 3 | Frekans yanıt | 20Hz-20kHz ±0.5dB | sartname — ölçüm PLANNED |
| 4 | DC offset koruması | 0.5V eşiği | tasarım kuralı (zorunlu) |
| 5 | Termal | >80°C kapanma | vault kanıtlı (thermal.md:56) |
| 6 | Boot bütçesi | ~1.1s toplam | vault kanıtlı (boot-sequence.md:42) |

## §F Kapsam Altında Yatan Varsayımlar

| # | Varsayım | Doğrulama | Durum |
|---|---|---|---|
| V1 | `.ai/electronic/**` 60 dosyanın tamamı işlenebilir içerik | 3 ajan derinleştirmesi | BEKLEMEDE |
| V2 | XMOS XU316 hedef donanım olarak kalır | Kullanıcı onayı | ⚠️ VERIFICATION REQUIRED |
| V3 | 8.1 surround topolojisi bu domain kapsar | Sınır sorusu Q-K2-5 | ⚠️ VERIFICATION REQUIRED |
| V4 | Class AB 100W hedefi üretim hedefidir | vault: class-ab.md:21 | Aktif (kanıtlı) |
| V5 | Class D TPA3255 alternatif olarak kalır | vault: class-d.md:42-47 | Aktif (kanıtlı) |
| V6 | Envanter 10.323 satır iddiası | Disk ölçümü 8.095 satır | ÇÜRÜTÜLDÜ — disk kazanır |

**V6 notu:** Görev metninde `.ai/electronic/**` için "10.323 satır" iddia
edilmiştir; 2026-10-13 disk ölçümü **60 dosya / 8.095 satır** vermiştir. İki
sayı da yazıldı, hangisinin geçerli olduğu disk kanıtına bağlandı —
uydurma hizalama yapılmadı.

## §G Ayrım Tablosu — K002 ↔ K000/electroncis ↔ .ai/electronic

Üç katman sık sık karıştırılır; bu tablo tek yetkili ayrımıdır.

| # | Boyut | K002-electroncis (BU NODE) | K000-isletim-sistemi/electroncis | .ai/electronic/** |
|---|---|---|---|---|
| 1 | Tür | İşlenmiş vault (Kademe-1 domain) | Komşu K-node alt-dalı (2 alt-dal) | Ham içerik (60 dosya) |
| 2 | Odak | Donanım tasarımı + üretim + test | OS↔donanım kesişimi (sürücü, hot-plug) | Toplanmış bilgi, işlenmemiş |
| 3 | Dosya sayısı | 26 (bu tur) | 4 (disk ölçümü 2026-10-13) | 60 (disk ölçümü 2026-10-13) |
| 4 | Satır | 26 dosyanın tamamı ≥500 veya 40-100 | ⚠️ VERIFICATION REQUIRED (satır sayısı okunmadı) | 8.095 satır |
| 5 | Kod yazar mı | Hayır (yalnız `.md`) | Hayır (yalnız `.md`) | Hayır (yalnız `.md`) |
| 6 | Örnek soru | "Boost konvertör şeması nasıl?" | "ASIO cihaz kaybında sürücü ne yapar?" | "Class AB nedir?" (ham not) |
| 7 | İçerik olgunluğu | draft (A1 tur-1) | mevcut (önceden yazılmış) | ham (3 ajan bekleniyor) |
| 8 | Yazıma izin | EVET (bu görev beyazlistesi) | HAYIR (yazım yasak) | HAYIR (yalnız atıf) |
| 9 | İlişki | Üst katman — işler ve sıralar | Eş katman — sınır komşusu | Alt katman — kaynağı |
| 10 | Tekrar riski | Düşük (ayrım §G ile korunur) | Orta — tekrar olursa bu tablo yetkili | Yüksek — ham olduğu için |

### §G.1 Kesişim Kuralları (hangi konu hangisine gider)

| # | Soru | Gider | Gitmez |
|---|---|---|---|
| 1 | "Sürücü hot-plug'u nasıl yakalar?" | K000/electroncis | K002 |
| 2 | "Hot-plug algılama pin'i hangi devrede?" | K002 (sensor-ve-akteyici) | K000 |
| 3 | "Class AB nedir?" (ham tanım) | .ai/electronic (kaynak) | K002 tekrar yazmaz — işler |
| 4 | "Class AB şeması + ölçüm kapısı nedir?" | K002 (devre-ve-analog) | — |
| 5 | "ASIO Exclusive Lock nedir?" | K000 (OS kuralı) | K002 |
| 6 | "DAC hangi PCM3168A pin'ine bağlanır?" | K002 (dijital-ve-mikroislemci) | — |

### §G.2 Çakışma Halinde Yetki Sırası

| Sıra | Kaynak | Neden |
|---|---|---|
| 1 | Disk ölçümü (gerçek dosya) | Zero-hallucination — disk kazanır |
| 2 | Frozen ADR'ler (ADR-038, ADR-017) | Değiştirilemez |
| 3 | K002 işlenmiş vault (bu node) | Kapsam sahibi |
| 4 | K000/electroncis | Komşu — sınır çatışmasında §G tablosu |
| 5 | `.ai/electronic/**` ham | Kaynak — çelişirse işlenmiş vault kazanır |

## §H Alt-Konu Bileşen Envanteri (hangi alt-konu neyi taşır)

### §H.1 devre-ve-analog bileşen envanteri

| # | Bileşen sınıfı | Vault kaynağı | Satır kanıtı | İşlem |
|---|---|---|---|---|
| 1 | Direnç ailesi | `.ai/electronic/components/*` | ⚠️ VERIFICATION REQUIRED (dosya bazlı okuma yapılmadı) | anayasa §3 |
| 2 | Kondansatör ailesi | `.ai/electronic/components/*` | ⚠️ VERIFICATION REQUIRED | anayasa §3 |
| 3 | Op-amp seçimi | `.ai/electronic/amplifier/class-ab.md` | 38-60 (sinyal zinciri) | anayasa §2 |
| 4 | Class D güç aşaması | `.ai/electronic/amplifier/class-d.md` | 42-47 (verim, damping) | anayasa §4 |
| 5 | Filtre topolojileri | `.ai/electronic/**` filtre notları | ⚠️ VERIFICATION REQUIRED | anayasa §5 |
| 6 | Osilatör/kristal | `.ai/electronic/**` | ⚠️ VERIFICATION REQUIRED | anayasa §6 |

### §H.2 dijital-ve-mikroislemci bileşen envanteri

| # | Bileşen | Vault kaynağı | Satır kanıtı | İşlem |
|---|---|---|---|---|
| 1 | Boot zinciri | `.ai/electronic/firmware/boot-sequence.md` | 28-43, 51-57 | anayasa §2 |
| 2 | RTOS/koşu zamanı | `.ai/electronic/firmware/rtos.md` | 29-54 | anayasa §3 |
| 3 | Cihaz entegrasyonu | `.ai/electronic/drivers/device-integration.md` | 27-34 | anayasa §4 |
| 4 | Ses sürücü yüzeyi | `.ai/electronic/drivers/audio-drivers.md` | 25-53 | anayasa §5 (sınırlı — OS K000'de) |
| 5 | I2S/TDM arayüzü | `.ai/electronic/firmware/audio-interface.md` | 32, 49-50, 58 | anayasa §6 |
| 6 | Geliştirme akışı | `.ai/electronic/development-workflow.md` | 225-232, 416-434 | anayasa §7 |

### §H.3 guc-yonetimi bileşen envanteri

| # | Bileşen | Vault kaynağı | Satır kanıtı | İşlem |
|---|---|---|---|---|
| 1 | Class AB besleme | `.ai/electronic/amplifier/power-supply.md` | 24-80 | anayasa §2 |
| 2 | Koruma devresi | `.ai/electronic/amplifier/protection.md` | 37-53 | anayasa §3 |
| 3 | Termal tasarım | `.ai/electronic/amplifier/thermal.md` | 50-56 | anayasa §4 |
| 4 | PCB güç katmanı | `.ai/electronic/hardware/pcb-design.md` | 24-30, 44-52 | anayasa §5 |
| 5 | Topoloji seçimi | `.ai/electronic/**` güç notları | ⚠️ VERIFICATION REQUIRED | anayasa §6 |
| 6 | PFC/pilot devre | `.ai/electronic/**` | ⚠️ VERIFICATION REQUIRED | anayasa §7 |

### §H.4 sensor-ve-akteyici bileşen envanteri

| # | Bileşen | Vault kaynağı | Satır kanıtı | İşlem |
|---|---|---|---|---|
| 1 | Akım ölçüm (shunt/INA) | `.ai/electronic/**` | ⚠️ VERIFICATION REQUIRED | anayasa §2 |
| 2 | ADC arayüz (ADS serisi) | `.ai/electronic/**` | ⚠️ VERIFICATION REQUIRED | anayasa §3 |
| 3 | Aktüatör sürücü (DRV) | `.ai/electronic/**` | ⚠️ VERIFICATION REQUIRED | anayasa §4 |
| 4 | Kapalı çevrim donanım | `.ai/electronic/**` | ⚠️ VERIFICATION REQUIRED | anayasa §5 |
| 5 | Hot-plug algılama | `device-integration.md` | 27-34 | anayasa §6 |
| 6 | Kalibrasyon düzeltmesi | `.ai/electronic/**` ölçüm notları | ⚠️ VERIFICATION REQUIRED | anayasa §7 |

### §H.5 imalat-ve-test bileşen envanteri

| # | Bileşen | Vault kaynağı | Satır kanıtı | İşlem |
|---|---|---|---|---|
| 1 | PCB katman/trace | `pcb-design.md` | 24-30, 44-52 | anayasa §2 |
| 2 | SNR/THD ölçüm kurulumu | `snr-thd-measurement.md` | 29, 45-48, 55-59 | anayasa §3 |
| 3 | Frekans yanıt ölçümü | `frequency-response.md` | 50-56 | anayasa §4 |
| 4 | Test fazları | `development-workflow.md` | 416-434 | anayasa §5 |
| 5 | EMI/EMC ön hazırlık | `development-workflow.md` | 232 | anayasa §6 |
| 6 | DFT/AOI kriterleri | `.ai/electronic/**` | ⚠️ VERIFICATION REQUIRED | anayasa §7 |

**Ortak not:** `⚠️ VERIFICATION REQUIRED` satırları, ham vault dosyalarının bu turda
satır bazlı okunmadığını dürüstçe işaretler — içerik VAROLDUĞU VARSAYILMAZ,
okunduğunda doldurulur. Uydurma satır kanıtı yazılmaz.

## §I Sınır Karar Geçmişi

| # | Tarih | Soru | Karar | Durum |
|---|---|---|---|---|
| 1 | 2026-10-13 | K002 kurulmalı mı? | Band-1 GAP (registry §8.1) — kuruldu | KAPANDI |
| 2 | 2026-10-13 | Yazım `electroncis` mi? | v2 §3 KURAL 1 — düzeltilmez | KAPANDI |
| 3 | 2026-10-13 | Alt-konu sayısı 5 mi? | Görev tanımı — dokunulmaz | KAPANDI |
| 4 | 2026-10-13 | Kart/PCB kimde? (Q-K2-1) | — | AÇIK — üst karar |
| 5 | 2026-10-13 | Firmware sınırı? (Q-K2-2) | — | AÇIK — üst karar |
| 6 | 2026-10-13 | DSP sınırı? (Q-K2-3) | — | AÇIK — üst karar |
| 7 | 2026-10-13 | Envanter satır sayısı | Görev 10.323 / disk 8.095 → disk kazandı | KAPANDI (V6) |
| 8 | 2026-10-13 | K000/electroncis yazılacak mı? | HAYIR — yazım yasak, yalnız atıf | KAPANDI |

## §J Senaryo → Dosya Yönlendirme (bu domain içi)

| # | Senaryo | Giden dosya | Alt-konu |
|---|---|---|---|
| S1 | "Op-amp gürültüsü nasıl seçilir?" | `{alt}-anayasa` (devre-ve-analog) | devre-ve-analog |
| S2 | "I2S clock jitter şartı nedir?" | `{alt}-anayasa` (dijital) | dijital-ve-mikroislemci |
| S3 | "Boost konvertör verim hesabı" | `{alt}-anayasa` (guc) | guc-yonetimi |
| S4 | "INA219 ile akım ölçüm devresi" | `{alt}-anayasa` (sensor) | sensor-ve-akteyici |
| S5 | "Gerber gönderim kontrol listesi" | `{alt}-anayasa` (imalat) | imalat-ve-test |
| S6 | "PCM3168A pin bağlantısı" | `devre-ve-analog` + `dijital` | iki alt-konu |
| S7 | "SNR ölçümü nasıl yapılır?" | `imalat-ve-test-dogrulama` + vault atıf | imalat-ve-test |
| S8 | "ASIO cihaz kaybı" | DIŞARI — K000-isletim-sistemi | bu domain değil |
| S9 | "PHP session güvenlik" | DIŞARI — PHP katmanı | bu domain değil |
| S10 | "Class AB nedir?" (ham tanım) | Kaynak `.ai/electronic/**` — K002 tekrar etmez | §G.1 kural 3 |

## §K Kapsam Dışı Ama SIK KAÇIRILAN Konular

| # | Konu | Yanlış ev | Doğru ev | Not |
|---|---|---|---|---|
| 1 | ASIO/WASAPI davranışı | K002 | K000-isletim-sistemi | OS ≠ elektronik |
| 2 | JUCE/Neiva Engine kodu | K002 | L0 (Embedded) | Kod ≠ şema |
| 3 | MySQL/PHP | K002 | İlgili K-node | Bu domainde kod yok |
| 4 | `*.php/*.js/*.css/*.sql` yazımı | K002 | İlgili ajan | Beyazliste dışı |
| 5 | `.ai/architecture/index.md` güncellemesi | K002 | YALNIZ orkestratör | Registry kilitli |
| 6 | PCM5122 referansı | K002 | — | ADR-038: yasak, yazılmaz |
| 7 | 10.323 satır envanter iddiası | K002 | Disk: 8.095 | V6 — disk kazanır |
| 8 | ADR-019 per-OS player değişikliği | K002 | FROZEN — değişiklik ≥088 | Dokunulmaz |

## §L Değişiklik Geçmişi

| # | Tarih | Değişiklik | Sorumlu |
|---|---|---|---|
| 1 | 2026-10-13 | Kapsam defteri ilk yazım (§A-§G) | Vault Steward |
| 2 | 2026-10-13 | §H envanter + §I karar geçmişi + §J/§K yönlendirme | Vault Steward |
| 3 | 2026-10-13 | V6 çürütme kaydı (10.323 → 8.095) | Vault Steward |
| 4 | — | Q-K2-1..3 üst karar bekliyor | BEKLEMEDE |

## §M Çapraz Referanslar

| # | Hedef | İlişki |
|---|---|---|
| 1 | `electroncis-master.md` §9 | Kapsam dışı — tek satır özet |
| 2 | `electroncis-bagimliliklar.md` §2 | Bağımlılık matrisi |
| 3 | `electroncis-dogrulama.md` §5-§6 | Web + repo kanıtları |
| 4 | `index.md` §6 | Açık sorular Q-K2-1..5 |
| 5 | 5 × `{alt}-anayasa.md` | Alt-konu derinliği |
| 6 | `.ai/architecture/index.md` §8.1 | Registry (yazım YASAK) |
| 7 | `.ai/electronic/**` | Ham kaynak (yalnız atıf) |

## §N Alt-Konu Kapsam Ayrım Matrisi (iç çakışma önleme)

Beş alt-konu bazen aynı bileşeni sahiplenir; bu matris önceliği çözer.

| # | Ortak konu | Birincil alt-konu | İkincil (yalnız atıf) | Ayrım kuralı |
|---|---|---|---|---|
| 1 | Op-amp (sinyal yolu) | devre-ve-analog | guc-yonetimi (bias kaynağı) | Sinyal ≠ besleme |
| 2 | Op-amp (referans pin'i) | devre-ve-analog | sensor-ve-akteyici | Referans şeması devrede |
| 3 | Kristal/PLL | dijital-ve-mikroislemci | devre-ve-analog (analog osilatör) | Saat dijitalde, osilatör devrede |
| 4 | Decoupling kondansatörü | devre-ve-analog | imalat-ve-test (yerleşim) | Değer devrede, yerleşim üretimde |
| 5 | Güç pin'i + filtre | guc-yonetimi | dijital-ve-mikroislemci | Besleme topolojisi güçte |
| 6 | DC offset rölesi | guc-yonetimi | devre-ve-analog (sinyal algılama) | Koruma kararı güçte |
| 7 | I2S trace | dijital-ve-mikroislemci | imalat-ve-test (8mil kural) | Arayüz dijitalde, kural üretimde |
| 8 | Shunt/INA devresi | sensor-ve-akteyici | guc-yonetimi (ölçüm kaynağı) | Ölçüm devresi sensörde |
| 9 | Test noktaları | imalat-ve-test | Tüm alt-konular (ihtiyaç bildirir) | Yerleşim üretimde |
| 10 | Termal sensör yerleşimi | guc-yonetimi | imalat-ve-test (montaj) | Eşik güçte, montaj üretimde |
| 11 | Hot-plug pin'i | sensor-ve-akteyici | dijital (GPIO yapılandırması) | Devre sensörde, register dijitalde |
| 12 | EMC filtresi | imalat-ve-test | guc-yonetimi (giriş topolojisi) | Şema güçte, sertifika üretimde |

## §O Kapsam İçi Halihazırda Bilinen Boşluklar

| # | Boşluk | Etkilenen alt-konu | Eylem | Durum |
|---|---|---|---|---|
| 1 | `.ai/electronic/**` 60 dosya işlenmedi | hepsi | 3 ajan derinleştirmesi | BEKLEMEDE |
| 2 | Filtre topoloji vault kanıtı satır bazlı yok | devre-ve-analog | dosya okuma | AÇIK |
| 3 | Sensör vault kanıtı satır bazlı yok | sensor-ve-akteyici | dosya okuma | AÇIK |
| 4 | EMC standart numaraları doğrulanmadı | imalat-ve-test | ⚠️ VERIFICATION REQUIRED | AÇIK |
| 5 | XMOS XU316 hedef onayı yok | dijital-ve-mikroislemci | Kullanıcı onayı | AÇIK |
| 6 | 8.1 surround sınırı belirsiz | devre / dijital | Q-K2-5 üst karar | AÇIK |
| 7 | Ölçüm değerleri (SNR/THD) PLANNED | imalat-ve-test | Fiziksel ölçüm | PLANNED |
| 8 | `.ai/electronic` envanter 8.095 / görev 10.323 | hepsi | V6 kaydı — disk kazandı | KAPANDI |
| 9 | K000/electroncis satır sayısı okunmadı | sınır (§G.4) | ⚠️ VERIFICATION REQUIRED | AÇIK |

## §P Kapsam Dışı Ayrım Örnekleri (gerçek senaryolar)

### §P.1 Senaryo: "ASIO Exclusive Lock ihlali var, elektronikte mi?"

| Adım | Karar |
|---|---|
| 1 | Konu OS cihaz kilidi → K000-isletim-sistemi (kural 9) |
| 2 | K002'ye etkisi: yalnız donanım tarafı — DAC aynı anda iki uygulama isterse NE YAPAR? (hardware arbitration) bu domainde |
| 3 | Yazım: K002 yalnız atıf yapar, K000 dosyasına dokunmaz |

### §P.2 Senaryo: "PCM5122 kullanalım mı?"

| Adım | Karar |
|---|---|
| 1 | ADR-038 H001 — PCM5122 ABSOLUTELY FORBIDDEN |
| 2 | Alternatif: PCM3168A (tek izinli) |
| 3 | K002 her DAC referansında bunu yazar; ihlal → §D kısıt 1 |

### §P.3 Senaryo: "Neiva Engine DSP zinciri elektronikte mi?"

| Adım | Karar |
|---|---|
| 1 | DSP algoritması → K004-dsp (veya L0 — ⚠️ VERIFICATION REQUIRED sınır) |
| 2 | K002'de olan: analog ön zincir + DAC ön kısım (devre-ve-analog) |
| 3 | Ayrım: sinyal BEFORE DAC (analog/K002) vs AFTER DAC (dijital DSP) |

### §P.4 Senaryo: "PHP'de elektronik ölçüm kaydı?"

| Adım | Karar |
|---|---|
| 1 | PHP/DB katmanı bu domainin DIŞINDA (§K.3) |
| 2 | K002 rolü: ölçüm ŞARTNAMESINI verir (ne ölçülür, eşik ne) |
| 3 | Yazım: `*.php` beyazliste dışı — oku, yazma |

### §P.5 Senaryo: "PCB tasarımını K001 mi sahiplenir?"

| Adım | Karar |
|---|---|
| 1 | Q-K2-1 açık — üst karar bekliyor |
| 2 | Bu tur geçici: PCB tasarımı K002'de (imalat-ve-test), K001 yalnız envanter |
| 3 | Karar gelirse §B.5 ve §I revize edilir |

## §Q Terim Sözlüğü (kapsam özeli)

| # | Terim | Tanım |
|---|---|---|
| 1 | Kapsam içi | Bu domainin dosya ürettiği alan |
| 2 | Kapsam dışı | Komşu K-node'un alanı — yalnız atıf |
| 3 | Beyazliste | Bu turda yazıma izin verilen yüzey (`K002-electroncis/**`) |
| 4 | Ham içerik | `.ai/electronic/**` — işlenmemiş, kaynak |
| 5 | İşlenmiş vault | K002 dosyaları — sınırları ve kanıtları yazılmış |
| 6 | Sınır sorusu | Komşu çatışmasını üst karara taşıyan etiket |
| 7 | V6 çürütme | 10.323 satır iddiasının disk ölçümüyle (8.095) çürütülmesi |
| 8 | Zamanlama bütçesi | 512/48kHz altında toplam gecikme payı (≤25ms hedef) |

## §R Uygulama Kuralları (bu dosyayı okuyan ajan için)

| # | Kural | Sonuç |
|---|---|---|
| 1 | Bu dosyadaki §G tablosu yetkilidir — çatışmada diğerine değil buna bak | Tekrar önlenir |
| 2 | Yeni alt-konu eklenmeyecek (5 dokunulmaz) | Yapı sabit |
| 3 | Kapsam dışı konu gelirse §K tablosuna bak, evine git | Layer violation önlenir |
| 4 | `⚠️ VERIFICATION REQUIRED` satırları doldurulmadan "bitti" denmez | Zero-hallucination |
| 5 | Envanter iddiası disk ölçümüyle çelişirse disk kazanır (V6) | Zero-hallucination |
| 6 | Yazım `electroncis` — hiçbir yerde `electronics` yapılmaz | v2 §3 KURAL 1 |
| 7 | `.ai/architecture/index.md`'ye bu turdan sonra da YAZMA | Registry orkestratöre ait |
| 8 | Commit atma — orkestratöre ait | §25.3 kuralı |

## §S Kapsam Onay Listesi (bu tur)

- [x] 5 alt-konu sınırı yazıldı (§B)
- [x] 7 komşu ayrımı yazıldı (§C, §G)
- [x] 8 bağlayıcı kısıt kaydedildi (§D)
- [x] 6 ölçüm kapısı kaydedildi (§E)
- [x] 6 varsayım işaretlendi — 1 çürütüldü (§F, V6)
- [x] K002 ↔ K000/electroncis ↔ .ai/electronic ayrım tablosu (§G)
- [x] 3 sınır sorusu açık bırakıldı (§I) — uydurma karar YOK
- [ ] Q-K2-1..3 üst karar — BEKLEMEDE
- [ ] `.ai/electronic/**` 60 dosya derinleştirme — BEKLEMEDE

## §T Komşu K-Node Envanter Referansı (disk ölçümü 2026-10-13)

| # | Komşu | Disk durumu | K002 ilişkisi | Yazım |
|---|---|---|---|---|
| 1 | `K000-isletim-sistemi/` | mevcut (önceden yazılmış) | Üst sınır — OS | YASAK |
| 2 | `K000-isletim-sistemi/electroncis/` | 4 dosya (§G.3) | Eş sınır — kesişim | YASAK |
| 3 | `K001-donanım/` | mevcut | Alt sınır — envanter | YASAK |
| 4 | `K002-electroncis/` | 26 dosya (bu tur) | BU NODE | İZİNLİ (beyazliste) |
| 5 | `K004-dsp/` | ⚠️ VERIFICATION REQUIRED (okunmadı) | DSP algoritması | YASAK |
| 6 | `K005-firmware/` | ⚠️ VERIFICATION REQUIRED (okunmadı) | Gömülü kod | YASAK |
| 7 | `.ai/architecture/index.md` | registry | K002 GAP kaydı §8.1 | YASAK (orkestratör) |
| 8 | `.ai/electronic/**` | 60 dosya / 8.095 satır | Ham kaynak | YASAK (yalnız atıf) |

**Not:** K004 ve K005 disk durumları bu turda okunmadı; "var" varsayımı
yazılmadı → `⚠️ VERIFICATION REQUIRED`. K000 ve K001 diskten doğrulanmış,
`.ai/architecture/index.md` §8.1 kaydı görev öncesi keşifte okunmuştur.

## §U Bu Turun Kapsam Sınırı (ne YAPILDI / ne YAPILMADI)

| # | İşlem | Durum | Kanıt |
|---|---|---|---|
| 1 | 26 dosya yazımı (6 kök + 20 alt-konu) | TAMAM (bu dosya dahil) | disk ölçüm tablosu |
| 2 | `.ai/architecture/index.md`'ye giriş | YAPILMADI | git status — registry değişmedi |
| 3 | Commit | YAPILMADI | subagent commit atmaz (§25.3) |
| 4 | `.ai/electronic/**` değiştirme | YAPILMADI | yalnız okuma |
| 5 | `K000/K001..K019` yazma | YAPILMADI | beyazliste dışı |
| 6 | `*.php/*.js/*.css/*.sql` yazma | YAPILMADI | beyazliste dışı |
| 7 | Q-K2-1..3 kararı | YAPILMADI | üst karar bekliyor |
| 8 | Envanter derinleştirme (60 dosya) | YAPILMADI | 3 ajan bekleniyor |

## §V Kapsam Sınırı Testleri (bu defterin kendi doğrulaması)

| # | Test | Beklenen | Sonuç |
|---|---|---|---|
| 1 | §G tablosu 3 katmanı ayırıyor mu? | K002 · K000/electroncis · .ai/electronic | EVET |
| 2 | Her alt-konu sınırı var mı? | §B.1..B.5 = 5 satır grubu | EVET |
| 3 | Kısıt listesi ADR'leri taşıyor mu? | ADR-038, ADR-017 (§D.1-2) | EVET |
| 4 | Uydurma karar var mı? | Açık sorular açık kaldı (§I) | EVET — yok |
| 5 | Çürütülmüş iddia görünür mü? | V6 + §G notu (8.095) | EVET |
| 6 | Yazım hatası korundu mu? | `electroncis` her başlıkta | EVET |
| 7 | Son satır Authority mü? | tek Authority satırı | EVET |

## §W Sınır Öncelik Karar Ağacı (özet — akış)

```text
Yeni elektronik konusu gelir
  → Konu PHP/JS/CSS/SQL kodu mu?          → EVET → Bu domain DIŞI (§K.3)
  → Konu OS/sürücü/ASIO-WASAPI mı?        → EVET → K000-isletim-sistemi
  → Konu DSP algoritması mı?              → EVET → K004-dsp
  → Konu gömülü KOD mu (şartname değil)?  → EVET → K005-firmware
  → Konu fiziksel envanter (hangi parça)? → EVET → K001-donanım
  → Konu ham vault'tan ham alıntı mı?     → EVET → .ai/electronic/** (atıf)
  → Konu donanım tasarımı / üretim / test?→ EVET → BU DOMAIN (K002)
  → Emin değilsem                          → ⚠️ VERIFICATION REQUIRED + Q etiketi
```

**Uygulama:** Ağaç her yeni görevde çalıştırılır; karar §G.1 tablosuyla
çapraz kontrol edilir. Çelişki varsa §G.2 yetki sırası uygulanır (disk →
ADR → K002 → K000 → ham kaynak).

| # | Sık yapılan hata | Doğru davranış |
|---|---|---|
| 1 | "Elektronik olduğu için her şey K002'ye" | Ağaç sırasıyla uygula — OS/DSP/firmware önce elenir |
| 2 | Ham vault'tan olduğu gibi kopyalama | İşi K002 yapar: sınırla, kanıtla, ölçüm kapısına bağla |
| 3 | Açık soruya cevap uydurma | Q etiketi + üst karar (§I) |
| 4 | Registry'ye K002 satırı ekleme | YASAK — orkestratöre bildir, yazma |
| 5 | Envanter sayısını iddiaya uydurma | Disk ölçümü yaz (V6 dersi: 8.095) |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
