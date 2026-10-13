---
type: electronic
category: pcb-design
title: "CoreMusic — PCB Design Guidelines"
date: 2026-08-09
updated: 2026-10-13
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — PCB Design Guidelines

**See also:** [[electronic/hardware/index]] · [[ADR-038-8.1-sound-card-chip-selection]]

**Zorunlu Bağlantılar:** [[electronic/hardware/amplifier-pcb]] · [[electronic/hardware/audio-interface]] · [[electronic/amplifier/power-supply]] · [[electronic/amplifier/thermal]]

---

## 1. Amaç

PCB Design Guidelines, CoreMusic ELECTRONICS platformunun PCB tasarım kurallarını tanımlar: katman yapısı, analog/dijital ayırma, trace kuralları, bileşen yerleşimi, EMC ve üretim kapıları. Bu dosya tüm kartların (amfi, audio interface, güç) ortak layout dilidir.

Bu dosya şu soruları yanıtlar:

- 4 katmanlı kartta güç/sinyal/toprak nasıl dağıtılır?
- Analog ve dijital toprak nerede ve nasıl birleştirilir?
- ±42V güç yolu, I2S ve clock izleri için minimum genişlik/aralık nedir?
- EMC uyumu için hangi stitch via / filter / shield kuralları uygulanır?

---

## 2. Katman Yapısı

### 2.1 4-Katmanlı Yığın (Varsayılan)

| Katman | Amaç | Not |
|--------|------|-----|
| Top (L1) | Sinyal, component placement | Bileşenler + clock/sinyal |
| Inner 1 (L2) | Ground plane (kesintisiz) | Analitik referans düzlemi |
| Inner 2 (L3) | Power plane (±42V, 5V, 3.3V) | Bölünmüş rail polygon |
| Bottom (L4) | Sinyal, routing | Düşük öncelikli izler |

**Kural:** L2 ground plane **kesintisiz** tutulur — clock ve yüksek hızlı izlerin altı oyulmaz; L2'de slot açılması yasak (return path bozulması → EMI).

### 2.2 Katman Kural Matrisi

| Kural | Değer | Gerekçe |
|-------|-------|---------|
| L2 referans | Her HIGH_SPEED iz L2'ye referanslı | Kontrollü empedans |
| L2 slot yasağı | Clock/sinyal altı oyulmaz | Return path kesintisi |
| L3 bölme | Güç rail'leri arası 2× clearance | ±42V ↔ 3.3V kısa devre payı |
| Top GND pour | Boş alanlarda toprak polygon | EMI kapanımı |
| Bottom GND pour | Boş alanlarda toprak polygon | EMI kapanımı |
| Via stitching | Sınır boyunca λ/20 aralık | Faraday kafesi etkisi |

---

## 3. Analog/Dijital Ayırma

### 3.1 Topraklama Topolojisi

| Kural | Açıklama |
|-------|----------|
| Analog ground | Ayrı bölge (aynı plane üzerinde bölge ayrımı) |
| Digital ground | Ayrı bölge |
| Tek nokta birleşme | Power supply girişinde (star point) |
| Analog koruma | Dijital sinyallerden uzak bölge |

**Gerçekleştirme:** 4 katmanlı kartta fiziksel iki toprak plane yerine **tek L2 plane üzerinde bölge ayrımı** tercih edilir; analog bölge ile dijital bölge arasında 2mm boşluk (keepout) bırakılır ve yalnız power supply girişinde köprülenir. Bu, slot'un EMI yarattığı bilinen tuzağı (§2.2) atlar.

### 3.2 Bölge Ayrım Kuralı

| Bölge | İçerik | Komşuluk |
|-------|--------|----------|
| Analog | Input/VAS, DAC analog çıkış, filtreler | XLR/TRS girişlerine yakın |
| Digital | XU316, I2S bus, SPI, GPIO | USB konektörüne yakın |
| Power | Boost converter, röle, rail regülatörler | Karta giriş noktası |
| Ölçüm | Shunt amplifikatörler, NTC divider | İlgili stage'e yakın |

**Geçiş bölgesi:** Analog↔digital sınırında sinyal geçişi yalnız **tek yönlü** ve **serbest empedanslı** (serbest bölge üstünden) yapılır; sinyal iki bölge arasında gidip gelmez.

---

## 4. Trace Kuralları

### 4.1 Minimum Genişlik / Aralık

| Sinyal | Minimum Genişlik | Aralık | Gerekçe |
|--------|-------------------|--------|---------|
| Power (±42V) | 50mil | 2× genişlik (25mil/1000V kuralı) | Akım + izolasyon |
| Power (50A boost yolu) | 100mil+ (bakır bus) | 2× genişlik | I²R kaybı |
| Analog sinyal | 10mil | 2× trace genişliği | Coupling |
| Digital (I2S) | 8mil | 3× trace genişliği | Crosstalk |
| Clock (24.576MHz) | 8mil | 5× trace genişliği | EMI hassasiyeti |
| Toprak döneli (return) | — | Referans plane L2 | Kesintisiz return |

### 4.2 Akım Kapasitesi Hesabı (IPC-2221 basitleştirme)

| Genişlik (mil) | Kalınlık (1oz) | Taşıdığı akım (tipik, 10°C rise) |
|----------------|----------------|-----------------------------------|
| 10 | 1.4 mil | ~1A |
| 25 | 1.4 mil | ~2.5A |
| 50 | 1.4 mil | ~4.5A |
| 100 | 1.4 mil | ~8A |

**±42V/3.54A rail için:** 50mil yeterli; boost çıkışından röleye kadar olan ana yol **100mil+ veya bakır polygon** (thermal §7-1: boost 25W ısıtır — iz kaybı ek ısı demektir).

### 4.3 Clock Kuralları

| Kural | Değer |
|-------|-------|
| Seri direnç | 22-33Ω source termination (driver ucunda) |
| Referans | Kesintisiz L2 |
| Uzunluk | ≤ 50mm (XU316↔PCM3168A) |
| Komşuluk | ≥5× genişlik boşluk |
| Cross-layer | Clock izi L3 power plane'i üzerinden geçmez |

---

## 5. Bileşen Yerleşimi

### 5.1 Decoupling

| Kural | Açıklama |
|-------|----------|
| Decoupling | Her güç pinine 100nF ceramic (X7R), pin mesafesi ≤3mm |
| Bulk | Her IC'ye 10µF bulk (40-100mm yakınlık) |
| Yüksek hızlı | Clock kaynaklarına yakın |
| Analog | Dijital kaynaklardan uzak |

### 5.2 Sıralama Kuralı

Decoupling kondansatörü, güç pini ile toprak pini arasındaki **kısa döngü** üzerinde bulunur; via, kondansatör ile pin arasına girmez (via indüktansı decoupling etkinliğini düşürür).

| # | Kural | Yanlış | Doğru |
|---|-------|--------|-------|
| 1 | Pin-via-kap | Via, pin ve kap ortasında | Kap, pin ile via arasında |
| 2 | GND via | Uzun toprak izi | Doğrudan L2'ye via |
| 3 | Seri 100nF | 2×100nF aynı via'da | Her biri kendi GND via'sıyla |

---

## 6. EMC Kuralları

| Kural | Açıklama |
|-------|----------|
| Ground pour | Tüm katmanlarda boş alanlar toprak polygon |
| Stitching via | Analog/dijital sınırında, λ/20 aralık |
| Filter | Her güç girişinde LC/π filtre |
| Shield | Hassas analog bölgeler (gölgeleme etiketi) |
| Cable return | Konektör toprağı, kart kenarına yakın via |
| Edge clearance | Karta kenar boşluğu ≥1mm (gövde/şasi teması) |

### 6.1 EMC Giriş Filtresi (Güç Girişi)

| Aşama | Eleman | İşlev |
|-------|--------|-------|
| 1 | CM choke | Common-mode gürültü |
| 2 | X kapasitör (100nF) | Differential mode |
| 3 | Y kapasitör (2.2nF) | Mode → toprak |
| 4 | Ferrite bead | Yüksek frekans emilimi |

---

## 7. PCB Ölçüm & Doğrulama Kapıları

### 7.1 Tasarım Öncesi

| # | Kontrol | Araç |
|---|---------|------|
| 1 | DRC (design rule check) | PCB CAD |
| 2 | Impedans (clock) | Stackup hesabı |
| 3 | Creepage/clearance (±42V) | IPC-2221 tablosu |
| 4 | Termal via yoğunluğu | thermal §9.3 |

### 7.2 Üretim Öncesi

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Gerber çıktısı | Fab ile stackup uyumu |
| 2 | BOM + pick&place | SMD/THT ayrımı |
| 3 | AOI (görsel) | Lehim kalitesi |
| 4 | İzolasyon testi | >1MΩ @500V (güç ↔ gövde) |

---

## 8. Repo / Kaynak Kanıtı

**Repo'da PCB layout kodu/gerber dosyası YOK — kaynak: .ai/electronic/**

CoreMusic deposunda `.kicad_pcb`, gerber veya layout script'i bulunmaz; tasarım kuralları yalnız bu vault dosyalarında yaşar. Depodaki donanıma en yakın yazılım yüzeyi AI orkestrasyon katmanıdır:

| Dosya | Satır | Kanıt | Doğrulama |
|-------|-------|-------|-----------|
| `shared/src/AI/AIEngine.php` | 138 | `public function analyzeHardware(string $deviceId): array` — cihaz analizi | `Select-Object -Index (137)` ile doğrulandı |
| `shared/src/AI/AIEngine.php` | 142-143 | `$snr = 0.0;` · `$thd = 0.0;` — placeholder (donanım ölçüm entegrasyonu YOK) | `Select-Object -Index (141..142)` ile doğrulandı |

**Sonuç:** Donanım doğrulama zinciri vault'ta tanımlıdır (§7); gerçek üretim/ölçüm entegrasyonu PLANNED.

---

## 9. Risk Matrisi

| # | Risk | Olasılık | Etki | Önlem |
|---|------|----------|------|-------|
| 1 | Return path slot (L2) | Orta | HIGH | §2.2 slot yasağı |
| 2 | ±42V creepage ihlali | Düşük | CRITICAL | §4.2 tablo + §7.1-3 |
| 3 | Clock EMI (24.576MHz) | Orta | HIGH | §4.3 kuralları |
| 4 | Analog/dijital coupling | Orta | MEDIUM | §3.2 bölge + geçiş kuralı |
| 5 | Decoupling via hatası | Orta | MEDIUM | §5.2 sıralama tablosu |
| 6 | EMC filtresi eksik | Düşük | HIGH | §6.1 güç girişi filtresi |
| 7 | Termal via yetersiz | Orta | HIGH | thermal §9.3 |
| 8 | Gerber/fab uyumsuzluğu | Düşük | MEDIUM | §7.2-1 |

---

## 10. Terimler (Glossary)

| Terim | Tanım |
|-------|-------|
| Creepage | Yalıtkan yüzey boyunca iki iletken arası mesafe |
| Clearance | Hava aralığı (iki iletken arası düz mesafe) |
| Stackup | Kart katman dizilimi (kalınlık, dielektrik) |
| Return path | Sinyalin toprak/plan üzerinden dönüş yolu |
| Stitching via | Plane katmanlarını bağlayan tekrarlı via dizisi |
| Decoupling | IC güç pinindeki yüksek frekans filtresi (100nF) |
| Star point | Toprakların tek noktada birleştiği fiziksel nokta |
| DRC | Design Rule Check — CAD kuralları taraması |
| AOI | Automated Optical Inspection — üretim görsel kontrol |

---

## 11. Web Kaynakları (Doğrulanmış URL Tablosu)

Aşağıdaki URL'ler 2026-10-13 tarihinde HTTP HEAD isteği ile doğrulanmıştır; her URL bu dosyada tektir (diğer vault dosyalarıyla kesişim yok).

| İddia | URL | 2026-10-13 | Durum |
|-------|-----|------------|-------|
| TI, INA180 current-shunt amplifier datasheet'ini resmi alan adında yayımlar (Kelvin bağlantı layout örneği — §4.2/§5 ilişkisi) | https://www.ti.com/lit/ds/symlink/ina180.pdf | Doğrulandı | HTTP 200 |
| TI, OPA2134 op-amp datasheet'ini resmi alan adında yayımlar (analog bölge yerleşimi referansı — §3) | https://www.ti.com/lit/ds/symlink/opa2134.pdf | Doğrulandı | HTTP 200 |

Doğrulama yöntemi: `Invoke-WebRequest -Method Head -UseBasicParsing -Uri $u -TimeoutSec 20`.

---

## 12. Açık Konular (VERIFICATION REQUIRED)

| # | Konu | Çelişki | Durum |
|---|------|---------|-------|
| 1 | 25mil/1000V kuralı | §4.1'de ±42V için "2× genişlik" izolasyon kuralı basitleştirilmiş; IPC-2221 B4 sınıfı (zorlu çalışma) tam creepage hesabı yapılmadı | ⚠️ VERIFICATION REQUIRED — kart yüzey kaplaması (HASL/ENIG) belirlenince yeniden hesapla |
| 2 | Via stitching λ/20 | §6'da λ/20 aralık yazıldı; kart stackup dielektrik sabiti bilinmediği için somut mm değeri verilmedi | ⚠️ VERIFICATION REQUIRED — stackup seçilince somutlaştır |
| 3 | 100mil+ boost yolu | §4.2'deki 50A/100mil iddiası IPC-2221 10°C rise tablosundan sadeleştirildi; gerçek akım (§power-supply 3.54A) için 50mil yeterli olabilir | ⚠️ VERIFICATION REQUIRED — nihai akım ile doğrula |
| 4 | ADR-063 uyumu | hardware/AGENTS.md §3-2 ADR-063 (Hardware Design Standards) uyumu zorunlu; ADR-063 metni bu revizyonda çapraz okunmadı | ⚠️ VERIFICATION REQUIRED |

---

## 13. ADR ve Çapraz Referanslar

| ADR | Konu |
|-----|------|
| [[ADR-038-8.1-sound-card-chip-selection]] | Donanım seçimi (PCM3168A, XMOS) |

| Hedef | İlişki |
|-------|--------|
| [[electronic/hardware/index]] | Klasör indeksi |
| [[electronic/hardware/amplifier-pcb]] | Amfi kart yerleşimi (bu dosyanın uygulaması) |
| [[electronic/hardware/audio-interface]] | I2S/clock layout (§4.3) |
| [[electronic/amplifier/power-supply]] | Güç yolu genişlik/akım (§4.2) |
| [[electronic/amplifier/thermal]] | Termal via/kaplama (§7.1-4) |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-13
**Mode:** Red Team · Human Mode · Truth Mode
