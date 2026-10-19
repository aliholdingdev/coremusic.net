---
node_id: electroncis-evrensel-dil
node_type: subdomain
status: draft
parent_id: K002-electroncis
children: []
depends_on: [K000-isletim-sistemi, K001-donanım, K004-dsp, K005-firmware]
evidence:
  - ".ai/architecture/K002-electroncis/electroncis-master.md §8 Terim Sözlüğü (30 terim) · 2026-10-18"
  - ".ai/.decisions/accepted/ADR-038-8-1-sound-card.md (PCM3168A terimleri) · ADR-017-dsp-hardware-mode.md (32-bit float) · 2026-10-18"
validation: "30 terim birebir master §8'den alınır; yeni terim uydurulmaz; TR karşılık yoksa İngilizce korunur ve '⚠️ TR karşılık: UNKNOWN' düşülür."
open_questions:
  - "⚠️ 'GVDD' TR karşılığı Türkçe literatürde standart değil — 'kapı sürme beslemesi' geçici çeviridir; kullanıcı onayı bekleniyor."
  - "⚠️ 'Yield' için 'ürün verimi' çevirisi endüstriyel kullanımla örtüşmüyor olabilir — 'kabul oranı' alternatifi Vault Steward'a sorulacak."
group: mimari
---

# K002-electroncis — Evrensel Dil (EN/TR Terim Sözlüğü)

> **Kaynak kuralı:** Bu dosyadaki 30 terim **birebir** `electroncis-master.md` §8'den
> alınmıştır; katalog dışı terim **uydurulmaz**. TR karşılık vault standartlarında
> bulunamazsa İngilizce terim korunur ve `UNKNOWN` işaretlenir.

## §1 ELI15

Elektronik konuşurken herkes aynı kelimeleri kullanmalı: "gürültü", "bozulma",
"besleme" gibi. Bu dosya, CoreMusic elektronik ekibinin İngilizce datasheet
terimlerini Türkçe karşılıklarıyla eşler. Amaç: bir mühendis ADR okurken,
başka bir mühendis datasheet okurken aynı kavramı kastetsin — çeviri hatası
devre hatasına dönüşmesin.

## §2 Çekirdek Ölçüm Terimleri (Sinyal Kalitesi)

| # | Terim (EN) | TR Karşılık | Tanım | K002 kullanımı |
|---|---|---|---|---|
| 1 | SNR (Signal-to-Noise Ratio) | Sinyal/gürültü oranı | Sinyal gücünün gürültü gücüne oranı (dB) | Kapı: **>100 dB** (master §6) |
| 2 | THD+N (Total Harmonic Distortion + Noise) | Toplam bozulma + gürültü | Harmonik bozulma + gürültü toplamı (%) | Kapı: **<0.01 % @1W · <0.001 % @10W** (master §6) |
| 3 | PSRR (Power Supply Rejection Ratio) | Besleme reddetme oranı | Besleme gürültüsünün çıktıya geçişini ölçer (dB) | SMPS ripple'ının ses yoluna etkisi |
| 4 | CMRR (Common Mode Rejection Ratio) | Ortak mod bastırma oranı | Diferansiyel girişte ortak mod sinyali bastırma (dB) | XLR diferansiyel giriş tasarımı |
| 5 | GBW (Gain-Bandwidth Product) | Kazanç-bant genişliği çarpımı | Op-amp kazanç × bant genişliği sabiti (MHz) | VAS aşaması op-amp seçimi |

## §3 Bileşen & Devre Terimleri

| # | Terim (EN) | TR Karşılık | Tanım | K002 kullanımı |
|---|---|---|---|---|
| 6 | SOA (Safe Operating Area) | Güvenli çalışma alanı | Transistörün gerilim×akım güvenli sınırı | Çıkış aşaması MOSFET/BJT seçimi |
| 7 | ESR (Equivalent Series Resistance) | Eşdeğer seri direnç | Kondansatörün iç direnci (mΩ) | Ripple <10 mV kapısı — bulk kapasitör seçimi |
| 8 | SRF (Self-Resonant Frequency) | Kendi rezonans frekansı | Bobinin indüktif→kapasitif geçiş frekansı | Filtre bobinleri, choke seçimi |
| 9 | MLCC (Multilayer Ceramic Capacitor) | Çok katmanlı seramik kapasitör | SMD seramik kapasitör ailesi (0603/0805) | Decoupling, C0G/X7R teknoloji seçimi |
| 10 | TCR (Temperature Coefficient of Resistance) | Sıcaklık katsayısı | Direncin sıcaklıkla değişimi (ppm/°C) | Hassas ölçüm dirençleri, kalibrasyon |
| 11 | Push-pull | İti-çekiş (tahrik) | NPN+PNP tamamlayıcı çıkış aşaması | Class AB çıkış aşaması topolojisi |
| 12 | Class AB | AB sınıfı | 180°–360° iletim açısı; crossover bozulma azaltmalı | Ana amfi topolojisi (ADR-089) |
| 13 | Class D | D sınıfı | PWM anahtarlamalı amfi; >%90 verimlilik | PVDD 18–53.5 V boost besleme |

## §4 Dijital Ses & Mikrodenetleyici Terimleri

| # | Terim (EN) | TR Karşılık | Tanım | K002 kullanımı |
|---|---|---|---|---|
| 14 | MCLK (Master Clock) | Ana saat | DAC/ADC ana saat girişi | PCM3168A clock ağacı (256×fs) |
| 15 | BCLK (Bit Clock) | Bit saati | Her bit için bir clock | I2S hattı — 3.072 MHz @48 kHz/64-bit |
| 16 | LRCLK (Left-Right Clock) | Kanal seçim saati | L/R kanal çerçevesi (fs) | I2S/TDM kareleme |
| 17 | PLL (Phase-Locked Loop) | Faz kilitli döngü | Saat sentez/çarpan devresi | MCLK türetilmesi |
| 18 | PVDD (Power-stage drain supply) | Güç aşaması besleme gerilimi | Class D çıkış aşaması besleme | 18–53.5 V (datasheet — KD-E06) |
| 19 | GVDD (Gate drive supply) | Kapı sürme beslemesi | MOSFET gate sürücü beslemesi | UCC27524/DRV sınıfı sürücüler |
| 20 | VAS (Voltage Amplifier Stage) | Gerilim yükselteç aşaması | Amfi öncül gerilim kazancı aşaması | Class AB öncül kademe |

## §5 Üretim & Test Terimleri

| # | Terim (EN) | TR Karşılık | Tanım | K002 kullanımı |
|---|---|---|---|---|
| 21 | DFT (Design for Test) | Test edilebilirlik tasarımı | Üretim testi için pin/nokta planı | Test noktaları, boundary-scan |
| 22 | AOI (Automated Optical Inspection) | Otomatik optik muayene | Kamera ile lehim gözlemi | PCB üretim hattı kontrolü |
| 23 | ICT (In-Circuit Test) | Devre içi test | Bed-of-nails ile bileşen testi | Fabrika test kademesi |
| 24 | FCT (Functional Test) | İşlevsel test | Son ürün tam işlev testi | AES17 fabrika testi (ADR-063) |
| 25 | HIL (Hardware-in-the-Loop) | Donanım döngüde test | Simülasyon+gerçek donanım kombinasyonu | K005 firmware doğrulaması |
| 26 | DRC (Design Rule Check) | Tasarım kural denetimi | PCB kural kontrolü (aralık, genişlik) | PCB revizyon kapısı |
| 27 | BGA (Ball Grid Array) | Bilye dizisi paket | Alt yüzey balli paketleme | XMOS XU316 paket tipi |
| 28 | Soft-start | Yumuşak açılış | Açılışta rush akımını sınırlayan gecikme | Boost converter ön şarj |
| 29 | Burn-in | Yaşlanma testi | Erken hata yakalama amaçlı çalıştırma | Fabrika kalite kapısı |
| 30 | Yield | Üretim verimi | Geçerli ürün oranı (%) | Üretim ekonomisi takibi |

## §6 Kullanım Kuralları

| # | Kural | Gerekçe |
|---|---|---|
| 1 | ADR metinlerinde bu tablodaki TR karşılıklar kullanılır | Tutarlılık |
| 2 | Datasheet atfında İngilizce orijinal terim korunur | Kaynak sadakati |
| 3 | Tabloda olmayan terim ilk kullanımda `⚠️ eklenecek` işaretlenir | Uydurma yasağı |
| 4 | Sayaç birimleri SI: V, A, W, Ω, Hz, dB, % | Ölçüm standardı |
| 5 | K004-dsp ile ortak terimler (BCLK, MCLK, PLL) tek kaynaktan okunur | Çifte çeviri yasağı |
| 6 | Kısaltma ilk kullanımda tam açılımıyla yazılır (ör. "THD+N (Total Harmonic Distortion + Noise)") | Okunabilirlik |
| 7 | TR karşılık tartışmaları bu dosyada `⚠️` ile açılır, sessiz çeviri yapılmaz | Denetim izi |
| 8 | Sözlük sürümü master §8 sürümüyle birlikte artar (kopya sürümleme yok) | SSOT sadakati |

## §7 Sık Karıştırılan Terim Çiftleri (dikkat tablosu)

| # | Karıştırılan ikili | Ayırıcı kural | Yanlış kullanımı engelleyen |
|---|---|---|---|
| 1 | SNR ↔ THD+N | SNR yalnız gürültüyü, THD+N gürültü+harmoniği ölçer | Ayrı kapılar: >100 dB vs <0.01 % |
| 2 | Class AB ↔ Class D | AB linear (180–360°), D PWM anahtarlamalı (> %90 verim) | PVDD yalnız D için anlamlı |
| 3 | MCLK ↔ BCLK | MCLK sistem saati, BCLK bit başına clock (64×fs'te) | PCM3168A pin adları |
| 4 | ESR ↔ SRF | ESR direnç (DC), SRF frekans sınırı (AC) | Filtre tasarımında ikisi ayrı |
| 5 | PVDD ↔ GVDD | PVDD çıkış MOSFET beslemesi, GVDD gate sürücü beslemesi | KD-E06 PVDD = 18–53.5 V |
| 6 | ICT ↔ FCT | ICT bileşen bazlı, FCT tam işlevsel ürün testi | ADR-063 fabrika kademeleri |
| 7 | DRC ↔ DFM | DRC kural ihlali yakalar, DFM üretilebilirliği değerlendirir | PCB revizyon akışı |
| 8 | Yield ↔ Burn-in | Yield oranı ölçer, Burn-in süreci zorlar | Üretim raporu ayrı sütun |

## §8 Çapraz Referanslar

| Hedef | İlişki |
|---|---|
| `electroncis-master.md` §8 | SSOT — 30 terim kaynağı |
| `K004-dsp/dsp-master.md` | Ortak dijital ses terimleri (BCLK/MCLK/PLL) |
| `K005-firmware/firmware-evrensel-dil.md` | Firmware tarafı aynı TR karşılıkları kullanır |
| `.ai/.decisions/accepted/ADR-038` | PCM3168A clock terimleri (MCLK/BCLK/LRCLK) |
| `electroncis-kod-ornekleri.md` §4 | Terim → blok eşlemesi (kod içindeki kullanımlar) |
| `.ai/glossary.md` | Vault-geneli sözlük — bu dosya K002 alt kümesidir |
