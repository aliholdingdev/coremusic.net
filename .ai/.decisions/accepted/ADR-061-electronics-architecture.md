---
title: "CoreMusic — ADR-061: Electronics Architecture (L6) — Katman Tanımı, Kart/Modül Hiyerarşisi, Bölüm Sınırı ve Bileşen Seçim Politikası"
type: "architecture-decision"
category: "electronics"
date: "2026-09-29"
updated: "2026-09-29"
version: "1.0.0"
status: "accepted"
authority: "SSOT — CoreMusic electronics (L6) kararı: (a) **L6 katman tanımı** = Hardware, firmware, driver, DSP, audio engine (`.ai/index.md:91`) ve **L0-L6 bağımlılık zinciri tek yön** (L6→L5→L4→L3→L2→L1→L0; geri dönüş yasak — `.ai/index.md:87`), (b) **L uzayı ≠ K uzayı** — L6'nın vault karşılığı K0-K5 + K16-K20 (A0/A5) kademesidir; `adlandirma-kurali.md §7.3` gereği **otomatik L→K dönüşümü YASAKTIR**, (c) **kart/modül hiyerarşisi** = Sistem → Kart/Modül → Blok/Devre (3 seviye; `architecture/k1-donanim/index.md` blok diyagramı = USB-C → XMOS XU316 → I2S → DAC AK4458 / ADC PCM3168A → diff-pair → VAS → Output → hoparlör), (d) **bölüm sınırı** — bu ADR yalnız L6 katmanını, hiyerarşiyi ve bileşen seçim politikasını yazar; DSP boru hattı `ADR-062`'ye, tasarım standartları `ADR-063`'e, platform/L0-L6 geneli `ADR-064`'e aittir, (e) **bileşen seçim politikası** 5 bağlayıcı kural (lifecycle = birinci sınıf kısıt · çoklu kaynak + FFF yedek · IEC 62402 uyumlu süreç · kararlı süreç düğümü + geniş ekosistem · NRND/EOL yeni tasarıma alınmaz)"
kaynak: "Disk kanıtı taraması (2026-09-29: `.ai/.decisions/index.md:92` slug satırı MEVCUT · `**/ADR-06*.md` glob = **0 dosya** → ADR-061/062/063/064 metinleri diskte YOK · `.ai/index.md:87-98` L0-L6 zinciri MEVCUT · `architecture/l6-electronics` Test-Path = **False** · `architecture/l4-domain` Test-Path = **False** · `architecture/katman-baglilik-matrisi.md` L0-L6 grep = **0 isabet** (yalnız K0-K20 + A0-A5) · `adlandirma-kurali.md:347` L6=K6 Güvenlik + `:363` §7.3 dönüşüm yasağı · `k1-donanim` **23** .md · `k3-ses-motoru` **18** .md · `firmware` **8** .md · `electronic/` Test-Path = **False**, `.ai/*.md` kökünde **69** referans · repo geneli `*.cpp|*.h|*.c|*.hpp` (vendor/node_modules/.git hariç) = **0** → firmware kodu yok · `brain.md:1009-1012`, `keys.md:285,288`, `MEMORY.md:656`, `reports/faz6-link-ledger.md:89`) + web araştırması (**4 sorgu / 30 adlandırılmış kaynak**)"
governance: "Red Team → Human Mode → Truth Mode"
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)"
---

# CoreMusic — ADR-061: Electronics Architecture (L6)

> **Durum:** ✅ **ACCEPTED** — **Tarih:** 2026-09-29 — **Debate:** ✅ **TAMAMLANDI** (3 tur / 20 persona · 18/2/0 KABUL) — **Tech Lead:** ✅ — **Arch Lead:** ⏳
> **Karar serisi:** `.ai/.decisions/accepted/` — **Slug:** `ADR-061-electronics-architecture` (dizin otoritesi: [[../index.md]] satır 92)
> **Slug sapması raporu:** görev talimatı bu dosyayı `ADR-061-electronics-l6` (veya `-l6` eki) ile hedeflemişti; disk/dizin gerçeği `ADR-061-electronics-architecture` → **indeks kazandı**, dosya adı indekse hizalandı (**In-Place Refactoring: dosya adı değişikliği YOK**).
> **İlgili kararlar:** [[ADR-017-dsp-hardware-mode]] (DSP'nin donanım modu — bu ADR L6'nın DSP bloğunu **sahiplenir**, çalışma modunu **yeniden almaz**) · [[ADR-038-8-1-sound-card-chip-selection]] (PCM3168A/AK4458/XMOS XU316 seçimi — **çip seçiminin kendisi bu ADR'nin değil, ADR-038'in**; bu ADR yalnız **seçim politikasını** bağlar) · [[ADR-039-7-service-platform-architecture]] (servis platformu — L5 sınırının sahibi) · [[ADR-040-database-authority]] (veri otoritesi — L0 sınırı) · [[ADR-089-classab-24v]] (Class-AB 24V) · [[ADR-090-channel-variant-product-family]] (kanal varyantı ürün ailesi) · [[ADR-042-vault-restructuring-2026-08-03]] (vault yeniden yapılandırması) · [[../index.md]] · [[../../index.md]] · [[../../brain.md]] · [[../../keys.md]] · [[../../AGENTS.md]] · [[../../.templates/adr/adr-template.md]]
> **⚠️ VERIFICATION REQUIRED (kayıp ADR metinleri):** `ADR-062-dsp-pipeline-architecture` · `ADR-063-hardware-design-standards` · `ADR-064-electronics-platform-architecture` — üçü de **diskte dosya olarak YOK** (glob `**/ADR-06*.md` = 0; [[../index.md]] satır 93-95 ve [[../../index.md]] satır 694-696 bu slotları **kaydetmiş**, metinleri **yok**). Bu ADR o numaraları **doldurmaz**; yalnız düz metinle sınır çizer.
> **Bölümlendirme notu:** ADR-064 satırı "L0-L6, 5 cihaz, 13 servis" iddiası taşır ([[../../brain.md]] `:1012`, [[../../keys.md]] `:288`) — **bu iddia kanıtlanamadı** (doğrulama ADR-064'ün işidir), bu ADR'de **tekrarlanmaz, doğrulanmış sayılmaz**.
> **Frozen değil:** ADR-001–037 frozen kapsamı dışındadır; bu dosya arşiv/indeksin ayırdığı **061** slotunu doldurur (kural 4'teki ADR-088+ aralığı bu seriyle çelişir → **numara çakışması raporlanır, düzeltilmez**, §5.1 adım 9).

---

## 1. Bağlam (Context)

CoreMusic'in donanım/firmware tarafı vault içinde **dağınık** durumda: `.ai/index.md` L0-L6 katman zincirini tanımlıyor ve L6'yı `architecture/l6-electronics` hedefine bağlıyor, ama o dizin **diskte yok**; aynı bilginin karşılığı olan içerik `architecture/k1-donanim/` (23 dosya), `architecture/firmware/` (8 dosya) ve `architecture/k3-ses-motoru/` (18 dosya) altında yatıyor. Buna karşılık `keys.md`/`.agents` dosyaları hâlâ **var olmayan** `electronic/` ağacına referans veriyor (`.ai/*.md` kökünde **69** isabet). Diğer yandan vault'ta **üç ayrı "L" isim uzayı** yaşıyor: `.ai/index.md`'deki L0-L6 (Electronics→Infrastructure), `adlandirma-kurali.md §7.2`'deki L0-L20↔K0-K20 (L6 = **Güvenlik**) ve `.agents/master-orchestrator.md:129`'daki L6→…→L0 (L1 = Security, L0 = Infrastructure). Bu ADR, electronics (L6) katmanının **tanımını**, **kart/modül hiyerarşisini**, **ADR-062/063/064 ile bölüm sınırını** ve **bileşen seçim politikasını** tek kayıtta bağlar — üç L uzayını birleştirmez, yalnız hangi uzayın bu kararda geçerli olduğunu söyler.

### 1.1 Mevcut Durum (disk kanıtı — dürüst etiket, 2026-09-29 taraması)

| İddia | Kanıt | Etiket |
|-------|-------|--------|
| ADR-061 slotu ayrılmış mı? | [[../index.md]] `:92` → `\| ../brain.md ADR-061-electronics-architecture \| Electronics Architecture (L6) \| Electronics \|` · [[../../index.md]] `:693` · [[../../brain.md]] `:1009` · [[../../keys.md]] `:285` | ✅ **KAYITLI** (4 indeks satırı) |
| ADR-061 dosyası diskte var mıydı? | glob `**/ADR-06*.md` = **0** (bu işlem öncesi) | ❌ **YOKTU** → bu işlemde yazılıyor |
| ADR-062/063/064 metinleri | glob = **0**; [[../index.md]] `:93-95` + [[../../index.md]] `:694-696` satırları var | ⚠️ **VERIFICATION REQUIRED** (düz metin) |
| L0-L6 zinciri | [[../../index.md]] `:87` → `✅ L6→L5, L5→L4, L4→L3, L3→L2, L2→L1, L1→L0 \| ❌ L0→L2/L3, L1→L3, L3→L0`; `:91-98` katman tablosu | ✅ **IMPLEMENTED** (metin) |
| L6 hedefi `architecture/l6-electronics` | `Test-Path` = **False** (wiki-link kırık) | ⚠️ **KIRIK HEDEF** |
| L4 hedefi `architecture/l4-domain` | `Test-Path` = **False** (wiki-link kırık) | ⚠️ **KIRIK HEDEF** |
| K matrisinde L0-L6 var mı? | `architecture/katman-baglilik-matrisi.md` içinde `\bL[0-6]\b` grep = **0 isabet** — matris yalnız **K0-K20 + A0-A5** | ✅ **L YOK = ayrı isim uzayı** |
| L6'nın K karşılığı | `architecture/adlandirma-kurali.md:347` → `\| L6 \| K6 \| Güvenlik Katmanı (K6) \| Güvenlik \|`; `:363` §7.3 "İkinci 'L' İsim Uzayı (Otomatik Dönüşüm YASAK)" | ⚠️ **ÇELİŞKİ — iki uzay, dönüşüm yok** |
| Üçüncü L uzayı | `.agents/master-orchestrator.md:129` → `L6 → L5 → L4 → L3 (Presentation) → L2 (Routing) → L1 (Security) → L0 (Infrastructure)`; `:78` "Layer violation üretme (L0→L2/L3, L1→L3)" | ✅ **IMPLEMENTED** (aynı zincir, ayrı uzay) |
| Donanım dokümantasyonu | `architecture/k1-donanim/` = **23** .md (içinde `index.md`, `xmos-xu316.md`, `pcm3168a-dac-adc.md`, `ak4458-dac.md`, `i2s-interface.md`, `pcb-tasarim.md`, `class-ab-amplifikator.md`, `koruma-devreleri.md`) | ✅ **IMPLEMENTED** (doküman) |
| Sinyal yolu blok diyagramı | `architecture/k1-donanim/index.md:22` → `USB-C ──▶ XMOS ──▶ I2S ──▶ DAC`; `:32` → `DAC ──▶ Diff ──▶ VAS ──▶ Output`; `:54` XMOS XU316 · `:56` AK4458 · `:61` XLR/RCA/USB-C | ✅ **IMPLEMENTED** (doküman) |
| DSP motoru dokümanları | `architecture/k3-ses-motoru/` = **18** .md (`dsp-chain.md`, `mixer-routing.md`, `neva-engine-core.md`, `stream-buffer.md`, …) | ✅ **IMPLEMENTED** (doküman) |
| Firmware dokümanları | `architecture/firmware/` = **8** .md (`bootloader.md`, `dsp-firmware.md`, `gpio-control.md`, `i2s-driver.md`, `mcu-support.md`, `usb-audio-firmware.md`, `xmos-firmware.md`, `index.md`) | ✅ **IMPLEMENTED** (doküman) |
| Firmware **kodu** | repo geneli `*.cpp\|*.h\|*.c\|*.hpp` (vendor/node_modules/.git/dist/build hariç) = **0 dosya** | ⏳ **PLANNED** (kod yok) |
| `electronic/` dizini | `Test-Path` = **False**; `.ai/*.md` kökünde `electronic/` = **69** isabet (`keys.md:141-152` açıkça "vault'ta yok / DOĞRULAMA GEREKLİ" der) | ❌ **KIRIK — SSOT DEĞİL** |
| "3 yeni ADR (061-063)" iddiası | [[../../MEMORY.md]] `:656` → `✅ 50+ dosya, L6 katmani, 3 yeni ADR (061-063)` — üç dosya da glob'da **0** | ⚠️ **VAULT BEYANI — DOĞRULANAMADI** |
| ADR-061..064 kırık mı? | [[../../reports/faz6-link-ledger.md]] `:89` → kırık hedefler arasında `ADR-061..064` | ✅ **RAPORLANDI** (bu işlem bu ADR'yi kapatır; 062-064 açık kalır) |
| Çip seçimi / amfi kararları | [[ADR-038-8-1-sound-card-chip-selection]] · [[ADR-089-classab-24v]] · [[ADR-090-channel-variant-product-family]] · [[ADR-017-dsp-hardware-mode]] dosyaları diskte **MEVCUT** | ✅ **IMPLEMENTED** (ayrı ADR'ler) |

### 1.2 Sorun Tanımı

1. **L6'nın kapsamı tek yerde yazmıyor.** `.ai/index.md:91` "Hardware, firmware, driver, DSP, audio engine" diyor; bu üç dizine (k1-donanim · firmware · k3-ses-motoru) yayılmış **49 doküman** demek, ama hiyerarşi (sistem → kart → blok) ve "hangi karar hangi ADR'de" çizgisi yok.
2. **Üç "L" isim uzayı çakışıyor.** `adlandirma-kurali §7.2` L6'yı **K6 Güvenlik**e eşlerken `index.md:91` L6'yı **Electronics**a eşliyor; `§7.3` otomatik dönüşümü yasaklıyor. Kimse hangi uzayın geçerli olduğunu yazmadığı için bir sonraki agent L6'yı Güvenlik sanabilir (Layer Violation = revert).
3. **`electronic/` hayalet SSOT.** 69 kök referans var, dizin yok. AGENTS §24.3 zaten uyarıyor; bu ADR **gerçek SSOT'u** (`architecture/k1-donanim` + `firmware` + `k3-ses-motoru`) yazmak zorunda.
4. **Bileşen ömrü politikası yok.** ADR-038 çip **seçimini** yapmış, ama "seçim nasıl yapılır / EOL gelince ne olur" kuralı hiçbir yerde yok.
5. **Dördüncü komşu ADR (062-064) metinsiz.** Sınır çizilmezse dört ADR birbirinin konusunu yutar.

### 1.3 Web'den Araştırma Raporu & Sonuçları

| Alan | Değer |
|------|-------|
| Web Search **Query** | (1) "modular hardware architecture layered board design carrier board SoM best practices 2025 electronics system architecture" · (2) "component selection policy obsolescence long lifecycle parts EOL risk electronics design 2025 guidelines" · (3) "multi-room audio streaming architecture distributed synchronised playback zone 2025 ESP-ADF SoundWire AES67" · (4) "layered hardware software co-design architecture embedded firmware driver HAL application layers separation of concerns" |
| Web Search **Konusu** | **(1)** Kart/modül (SoM–carrier) ayrımı ve katmanlı PCB mimarisi; **(2)** bileşen seçim politikası + tedarik ömrü/obsolescence yönetimi; **(3)** çok-odaklı (multi-room) ses mimarisi ve senkron oynatma; **(4)** donanım-yazılım katman ayrımı (HAL/driver/middleware) — hepsi electronics (L6) katmanının dört sorusuna karşılık gelir. |
| Web Search **Bağlamı** | CoreMusic: L6 zinciri metin olarak var (`.ai/index.md:87-98`) ama hedef dizinler yok; içerik `k1-donanim`(23) + `firmware`(8) + `k3-ses-motoru`(18) altında; `electronic/` = 0 dosya / 69 referans; firmware kodu 0; çip seçimi ADR-038'de, ama seçim **politikası** hiçbir yerde yok; üç ayrı "L" isim uzayı çakışıyor. |
| Web Search **Kısa Açıklama** | **(1)** SoM–carrier ayrımı "hızlı değişen silisyum" ile "yavaş değişen I/O"yu ayırır; yaşam döngüsü yönetimi, risk bölümü ve sertifikasyon kaldıracı üretir (Embedded Systems Engineering 2025); endüstri standartları COM Express / COM-HPC / SMARC / OSM (Institution of Electronics 2025); chip-down 8-12 katmanlı HDI isterken SoM'lü tasarım 4-6 katmanlı carrier'a iner (PCBSync 2026); yüksek hızlı diferansiyel çift **asla pin header'dan** geçirilmez (NW Engineering / PCBSync); carrier tasarımında güç giriş aşaması + sıralama (power sequencing) ve EMC zonlama birinci önceliktir (Critical Link MitySOM Kılavuzu, Xilinx Kria UG1091). **(2)** 2022'de ~750.000, 2023'te ~470.000 parça EOL oldu (Altium/Mouser); 2023 EOL olaylarının ~%30'u resmi bildirimsiz geldi (x-refs); resmi çerçeve **IEC 62402:2019**; lifecycle "traffic light" skoru (Active/NRND/EOL) + çoklu kaynak (≥2 üretici veya 3 yetkili kanal) + form-fit-function yedek + swap-ready footprint anahtarları (Altium, Luminovo); parça ömrü 6 evrede (introduction→growth→maturity→decline→phase-out→obsolescence) (BENCOR/Luminovo). **(3)** MIPI **SoundWire I3S v1.0** (Ekim 2025) I²S+I²C/SPI'yi tek 2-tel linkte birleştirir: 76 Mbps'e kadar, ~300 ns uçtan uca gecikme, link başına 12 çevre birimi · 32 port · 16 kanal (ESP-IDF #17757 / ESP-ADF #1549); **AES67/RAVENNA** PTP (IEEE-1588) + RTP ile 44.1/48 kHz, 0.125-4 ms paket süresi, L2 kancasıyla ~0.7 ms (DatanoiseTV/aes67-esp32p4); ESP-ADF multi-room multicast grubu log'larda -13 ms / -3 ms senkron farkı verir; SonicStream UDP multicast ile <30 ms playout gecikmesi hedefler; Matter'ta ses casting standardı **yok** (esp-adf #1310 talebi). **(4)** AUTOSAR üç katman (Application / RTE / Basic Software) ve BSW içinde Services → ECU Abstraction → µC Abstraction → Complex Drivers; embvm-core kuralı "her katman yalnız bir alttakini kullanır" (boot istisnası hariç); ESP-IDF **LL → HAL → Driver** sırası (her katman altındakine bağımlıdır); NuttX **upper-half / lower-half** sürücü ayrımı ile MCU başına kod tekrarını önler; NXP register→HAL→driver→middleware→application dilimi; TF-M, kaynak kısıtlı cihazlarda **çok katmanlı soyutlama kaçınır**. |
| Web Search **Uzun Açıklama** | **(i) Kart/modül hiyerarşisi:** Literatürde mimari, "bölme kararı" olarak anlatılıyor: hesaplama (compute) alanı ile uygulama I/O alanı ayrıldığında, silisyum yol haritası hızla değişirken carrier/I/O tarafı sabit kalır — bu, CoreMusic'in "ana ses kartı + fonksiyonel bloklar" ihtiyacıyla birebir örtüşür. Kılavuzlar (Kria UG1091, MitySOM-A5E, NW Engineering) tipik ayrımı şöyle verir: **modül** = SoC/DRAM/flash/PMIC + yüksek hızlı fabric; **carrier** = güç girişi (ters kutupluk, inrush, EMI filtresi), PHY/konnektör, ADC/DAC + sensör koşullandırma. Carrier tasarımının "sessiz hata noktası" güç mimarisidir: surge, inrush, reverse-polarity ve **power sequencing / supervision** (yoksa I/O pinlerinden back-powering → latch-up). EMC son test değil **tasarım kısıtıdır**: zonlama (anahtarlama regülatörü ↔ analog ada), konektör girişi EMC sınırı, shield'ın şasiye bağlanması. Yüksek hızlıda sürekli referans düzlemi, kontrol edilen empedans ve via mühendisliği (backdrill/teardrop) zorunlu; pin header yüksek hızlı diferansiyel için **uygun değildir**. Modül–carrier ayrımı, sertifika kapsamında daraltma (recertification leverage) da sağlar. **(ii) Bileşen ömrü:** Kaynaklar politikayı "tasarım aşamasında" kuruyor: lifecycle, güç/performans/maliyet yanında **birinci sınıf kısıt** olmalı; en üst 50-100 parça için risk register'ı + traffic-light skoru; kütüphanede kural (Active + çoklu kaynak + RoHS/REACH + hazır yedek); footprint'ler birden fazla aileyi taşıyacak şekilde esnek; uyumluluk belgeleri parça kaydına değil PO'ya bağlanmamalı; periyodik "BOM clinic" (çeyrek/yıllık); LTB (last-time buy) bilinçli ve tahminli yapılmalı, gri pazardan kaçınılmalı (sahte parça riski). Standartlaşma tarafında IEC 62402:2019 obsolescence yönetimini resmileştiriyor: çapraz fonksiyonlu ekip, lifecycle/kritiklik bazlı BOM risk değerlendirmesi, sürekli izleme. **(iii) Multi-room:** Üç farklı olgunlukta kanal görülüyor: (a) MCU-üstü yerel multicast grup senkronizasyonu (ESP-ADF MRM — master/slave, PTS tabanlı düzeltme, ~onlarca ms), (b) yayın standardı **AES67/RAVENNA** (PTP ile saat senkronu, RTP, SAP/SDP keşfi — gerçek zaman senkronizasyonu profesyonel ağ sesinde kanıtlanmış), (c) yeni nesil **MIPI SWI3S** (tek linkte veri+kontrol+senkron; 44.1/48/96 kHz referans saat; karışık örneklemeye izin; ~300 ns). **(iv) Katman ayrımı:** Tüm kaynaklar aynı prensipte uzlaşıyor: **alt katman üst katmanı çağırmaz**, soyutlama kırılma noktası (HAL/driver) donanımı değiştirilebilir kılar; istisnalar (AUTOSAR Complex Drivers, embvm boot süreci, ESP-IDF LL sızıntısı) **açıkça tanımlanır ve istisna olarak yazılır**. |
| Web Search **Paragraf Veri Uzun** | 4 sorgu / **30 adlandırılmış kaynak**; her iddia en az 2 bağımsız kaynakla çaprazlandı. **(1) Kart/modül + katmanlı PCB:** Embedded Systems Engineering "The Ultimate Guide to Carrier Board Design" (2025-09-03) · NW Engineering "PCB Design for FPGA SoMs and Carrier Boards" (2022-09-16) · IntechHouse "Essential Guide to Multilayer PCB Design" (2025-10-17) · Institution of Electronics "Computer-on-module architectures drive sustainability" (2025-10-31) · Critical Link "MitySOM-A5E Carrier Board Design Guide" · Xilinx "Kria SOM Carrier Card Design Guide (UG1091)" · PCBSync "SoM Design Guide" (2026-08-20) · Electronics Weekly "Five reasons to consider a SOM vs a chip-down design" (2024-06-05). **(2) Bileşen seçimi/ömür:** Altium "Streamlining Component Selection for Long Product Lifecycles" (2025-10-23) · Altium "Future-Proofing Your Design Against Component Obsolescence" (2025-09-24) · Altium "Component Obsolescence: Aerospace Best Practices" (2025-05-09) · Luminovo "Component Obsolescence: Lifecycle & EOL Management" (2025-08-04) · Mouser "Getting Ahead of Component Obsolescence" (2025-09-29) · BENCOR "Component Lifecycle Management" (2025-10-21) · Suntsu "Strategies for Mitigating Obsolete Electronic Components" (2025-04-23) · x-refs "Strategies for Managing Component Obsolescence" (2025-07-29 — IEC 62402:2019). **(3) Multi-room ses:** Espressif ESP-ADF multi-room README · ESP-IDF issue #17757 (MIPI SWI3S v1.0, 2025-10-21) · ESP-ADF issue #1549 (SWI3S kapsamı) · DatanoiseTV/aes67-esp32p4 (AES67/RAVENNA) · Espressif Components registry `datanoisetv/aes67` v2.6.0 · ESP-ADF issue #1310 (açık multi-room standardı talebi, 2024-11-07) · chagoguila/SonicStream v4.3 · ESP-ADF "Audio Pipeline" dokümantasyonu. **(4) Katmanlı mimari:** AUTOSAR "Layered Software Architecture" (EXP, R22-11) · NXP "Advanced MCU Design — Layered View" (2014-09-02) · Altera "Nios V Embedded Processor Design Handbook §7.2 Software Architecture" (2026-04-15) · embvm-core "Layer View" mimari dokümanı · ESP-IDF "Hardware Abstraction" (ESP32-P4 v6.0) · ESP-IDF "Hardware Abstraction" (ESP32-C6 v5.1) · Apache NuttX "OS Drivers Design" · Trusted Firmware-M "HAL" tasarım dokümanı. |
| Web Search **Sonucu** | **(1) Karar destekleniyor:** katmanlı + modüler kart hiyerarşisi (sistem→kart/modül→blok) ve "compute / I/O ayrımı" endüstri standardı; power-entry + EMC zonlama + sürekli referans düzlemi taşıyıcı tasarım kurallarıdır. **Çekirdek bulgu:** soğuk/ucuz taraf (carrier, konektör, koruma) ile pahalı taraf (yüksek hızlı routing, PMIC) ayrılınca yaşam döngüsü uzar → CoreMusic'te bu, **blok sınırı** ile korunur. **(2) Karar destekleniyor + zorunluluk:** obsolescence **tasarım aşamasında** yönetilir; resmi standart IEC 62402:2019; çoklu kaynak + FFF yedek + swap-ready footprint + düzenli BOM taraması anahtarlardır. **Karşıt bulgu:** gri pazar/aftermarket tek "kurtuluş" gibi görünür ama sahte parça riski nedeniyle **önerilmiyor**; LTB tahminle yapılır. **(3) Karar destekleniyor ama bağlam ayrışıyor:** çok-odalı senkronizasyonun olgun kanalı AES67/PTP, yeni standardı MIPI SWI3S; MCU-üstü multicast ise **prototip/yerel** seviyede. CoreMusic'in ADR-064 iddiası olan "5 cihaz / 13 servis" bu kaynaklarla **doğrulanamaz** → ⚠️ VERIFICATION REQUIRED. **(4) Karar destekleniyor:** tek yön bağımlılık + açıkça tanımlanmış istisna (Complex Driver / boot / LL) ortak model; çok katmanlı soyutlama yalnız kaynak kısıtlı hedeflerde kırpılır. |
| Web Search **Alınan Karar** | **(a) L6 kapsamı** = hardware + firmware + driver + DSP + audio engine; **(b) zincir tek yön** L6→L0, geri dönüş yasak; **istisnalar** (boot, complex driver, LL erişimi) yalnız bu ADR'de yazılırsa geçerli; **(c) hiyerarşi 3 seviye** = Sistem → Kart/Modül → Blok/Devre; kartlar arası sınır **konektör + güç girişi + EMC zonu**; **(d) bölüm sınırı** = DSP boru hattı → `ADR-062`, tasarım standartları → `ADR-063`, platform/L0-L6 geneli + 5 cihaz/13 servis iddiası → `ADR-064` (üçü de ⚠️ metinsiz); **(e) bileşen politikası 5 kural** (lifecycle birinci sınıf kısıt · ≥2 üretici veya 3 yetkili kanal + FFF yedek + swap-ready footprint · IEC 62402 uyumlu risk register + düzenli BOM taraması + bilinçli LTB · kararlı süreç düğümü + geniş ekosistem · NRND/EOL yeni tasarıma alınmaz, alınırsa gerekçesi §4.3'te kayıtlı olur); **(f) multi-room** bu ADR'nin **kapsamı dışındadır** (AES67/PTP ve SWI3S yalnız §3.5'te "gelecek seçenek" olarak not edilir). |
| Web Search **Sonuç** | Karar **destekleniyor**: kart/modül hiyerarşisi, katmanlı ayrım ve bileşen ömrü politikası bağımsız kaynaklarda **oybirliğiyle** var (AUTOSAR · embvm-core · ESP-IDF · NXP — katman; Altium ×3 · Luminovo · Mouser · x-refs/IEC 62402 · BENCOR — ömür; Embedded Systems Engineering · Kria UG1091 · MitySOM · PCBSync — kart). **İki gerilim** açıkça kabul edildi: (1) **L6 = Electronics ile L6 = K6 Güvenlik çelişkisi** → §4.3 risk 5, ikinci uzay olarak yaşatılır ve otomatik dönüşüm yasaklanır; (2) **`electronic/` hayalet SSOT + `l6-electronics`/`l4-domain` kırık hedefleri** → §5.1 adımlar 3/4'te düzeltme **sonraki vault reset'ine ertelenir** (bu işlem In-Place Refactoring + SRP gereği report-only). **⚠️ VERIFICATION REQUIRED:** "5 cihaz / 13 servis" (ADR-064), firmware kodu (0 dosya), `electronic/*` içerikleri ve L4 hedefi **kanıtlanamadı** — hiçbiri bu ADR'de "var" olarak yazılmadı. |

### 1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| Frozen ADR'ler (001–037) | Değiştirilemez; bu ADR yalnız okur ve atıf yapar |
| ADR-038 sınırı | Çip seçimi (XMOS XU316 · AK4458 · PCM3168A) **zaten kararlaştırıldı**; bu ADR çipi **yeniden seçmez**, yalnız seçim *politikasını* bağlar |
| ADR-017 sınırı | DSP donanım modu kararına dokunulmaz; bu ADR L6'nın DSP bloğunu sahiplenir, çalışma modunu **yeniden almaz** |
| ADR-039 / ADR-040 sınırı | Servis platformu (L5) ve veri otoritesi (L0) bu ADR'nin **kapsamı dışındadır**; yalnız bağımlılık yönü referans edilir |
| ADR-089 / ADR-090 sınırı | Class-AB 24V ve kanal varyantı ürün ailesi **alt kararlardır**; bu ADR bunları **uygular, yeniden karar almaz** |
| ADR-062/063/064 sınırı | Dosyaları diskte **YOK** → bu ADR onların konusunu **yazmaz**, yalnız sınır çizer (düz metin + ⚠️) |
| L isim uzayları | Üç uzay (index.md L0-L6 · adlandirma-kurali L0-L20↔K0-K20 · master-orchestrator L0-L6) **birleştirilmez**; `§7.3` otomatik L→K dönüşümü yasak |
| In-Place Refactoring | Dosya adı değişikliği **yok**; `index.md` satır 92 zaten doğru slug'u taşıyor → **yeni satır eklenmedi** (report-only) |
| UTF-8 yazım protokolü | Tüm vault yazımları `vault-utf8-writer.mjs` üzerinden; `log.md` yalnız `append` |
| Hallucination sweep | Diskte olmayan ADR'ye **wiki-link yok**; `electronic/*` hedeflerine wiki-link **yok**; `l6-electronics`/`l4-domain` düz metin + ⚠️ |
| Kanıt = disk | Yalnız `Test-Path`/glob/satır numarası ile doğrulanmış iddialar; firmware kodu 0 → her şey **PLANNED** (H019) |
| Debate | `debate: ✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)` — Tur 1-3 tamamlandı; Tech Lead ✅, Arch Lead ⏳ (§7) |
| REDACTED | `.env`, sertifika/anahtar, BOM fiyat/parça numarası hassas değerleri, tedarikçi sözleşmesi **hiçbir** ADR'ye yazılmaz |
| Numara serisi | Kural 4 yeni ADR'leri ADR-088+'ya ayırır; bu dosya arşivin atadığı **061** slotunu doldurur → çakışma **raporlanır, düzeltilmez** (§5.1 adım 9) |

---

## 2. Karar (Decision)

### (a) L6 katmanı tanımı ve L0-L6 zincir sınırı

| # | Karar | Değer | Durum |
|---|-------|-------|-------|
| 1 | **L6 kapsamı** | Hardware · firmware · driver · DSP · audio engine (kaynak: [[../../index.md]] `:91`) | ✅ **IMPLEMENTED** (metin) |
| 2 | **L6'nın vault karşılığı** | K uzayında **K0-K5 + K16-K20** (A0 Altyapı/Donanım + A5 Bileşenler kademesi) — `architecture/katman-baglilik-matrisi.md` + [[../../AGENTS.md]] §5 A-tablosu | ✅ **IMPLEMENTED** |
| 3 | **Zincir yönü** | `L6→L5→L4→L3→L2→L1→L0` **tek yön**; ters yön (L0→L2/L3, L1→L3, L3→L0) = **Layer Violation → revert + log ERROR** (kaynak: [[../../index.md]] `:87`, `.agents/master-orchestrator.md:78`) | ✅ **IMPLEMENTED** |
| 4 | **L→K otomatik çeviri** | **YASAK** (`adlandirma-kurali.md §7.3`); çelişki raporlanır, dönüştürülmez — bu ADR'de L6 = **Electronics**tur, K6 = **Güvenlik** ayrı kalır | ✅ **IMPLEMENTED** |
| 5 | **Katman istisnaları** | Yalnız yazılırsa geçerli: (i) **boot** (alt katman kendi kendini başlatır, sonra üst devralır), (ii) **complex driver** (zamanlama kısıtı olan yol — AUTOSAR modeli), (iii) **LL/register erişimi** (performans kritik tek nokta). Sınırı yazmayan kod istisna **iddia edemez** | ⏳ PLANNED (kural) |
| 6 | **`architecture/l6-electronics` hedefi** | Dizin **YOK**; gerçek içerik `architecture/k1-donanim/` + `architecture/firmware/` + `architecture/k3-ses-motoru/`. Wiki-link düzeltmesi **sonraki vault reset'ine ertelendi** | ⚠️ **KIRIK — ERTELENDİ** |

### (b) Kart / modül hiyerarşisi (3 seviye)

| Seviye | Tanım | CoreMusic karşılığı | Sınır |
|--------|-------|---------------------|-------|
| **L1 — Sistem** | Ürünün tamamı; kullanıcıya satılan birim | Ürün ailesi (kanal varyantları [[ADR-090-channel-variant-product-family]]) | Cihaz ≠ kart: sistem seviyesi kararlar **ürün ADR'lerinde** |
| **L2 — Kart / Modül** | Fonksiyonel bütünlük taşıyan tek kart ya da modül; konektörle diğerlerine bağlanır | **Ana ses kartı** (USB-C → XMOS XU316 → I2S → DAC AK4458 / ADC PCM3168A) · **güç kartı** · **çıkış/koruma kartı** | Kartlar arası sınır = **konektör + güç girişi + EMC zonu**; bir kartın içine diğerinin bloğu **sokulmaz** |
| **L3 — Blok / Devre** | Tek bir işi yapan devre bloğu (ayrı ayrı dokümante edilmiş) | `k1-donanim` içindeki 23 blok: `diff-pair-input` · `pcm3168a-dac-adc` · `ak4458-dac` · `i2s-interface` · `vas-stage` · `output-stage` · `class-ab-amplifikator` · `koruma-devreleri` · `guc-kaynagi-analog` · `termal-yonetim` · `konnektorler` · `pcb-tasarim` … | Blok sınırı = **sinyal/güç arayüzü** (ör. `DAC → Diff → VAS → Output`, `index.md:32`); blok değişiminde komşu bloğun dokümanı **değişmez** |

**Kural (a)-(b) bağla:** L6 katmanı, **L2 kartlarını** birbirine; L2 kartları ise **L3 bloklarını** birleştirir. Firmware (`architecture/firmware/` 8 doküman) bir **kart seviyesi varlıktır** (bootloader · i2s-driver · gpio-control · xmos-firmware · usb-audio-firmware · dsp-firmware · mcu-support) — ama **kodu 0 dosya** olduğu için hepsi ⏳ PLANNED'dır.

### (c) Bölüm sınırı — ADR-061 vs ADR-062 / 063 / 064

> Bu üç dosya **diskte YOK** → aşağısatırlar **düz metindir**, wiki-link **değildir**.

| Konu | Sahip ADR | Bu ADR'nin tutumu |
|------|-----------|-------------------|
| L6 katman tanımı · zincir · kart/modül hiyerarşisi · bileşen seçim politikası | **ADR-061 (bu dosya)** | ✅ burada kararlaştırılır |
| **DSP boru hattı** yapısı (pipeline aşamaları, kanal iş zinciri, `dsp-chain`/`mixer-routing` akışı) | `ADR-062-dsp-pipeline-architecture` | ⚠️ **VERIFICATION REQUIRED** — metin yok; bu ADR pipeline'ı **yazmaz**, yalnız L6 içindeki yerini gösterir (`k3-ses-motoru`) |
| **Tasarım standartları** (PCB kural seti, tolerans/derating, BOM standardı, ölçüm protokolü) | `ADR-063-hardware-design-standards` | ⚠️ **VERIFICATION REQUIRED** — metin yok; bu ADR standart **içeriğini** yazmaz, yalnız **nerede yaşayacağını** söyler (`k19-pcb`, `k20-bom`, `k1-donanim/pcb-tasarim.md`) |
| **Platform mimarisi** (L0-L6 geneli, "5 cihaz / 13 servis" iddiası) | `ADR-064-electronics-platform-architecture` | ⚠️ **VERIFICATION REQUIRED** — metin yok; iddia [[../../brain.md]] `:1012` + [[../../keys.md]] `:288`'de vault beyanı olarak durur, **doğrulanmadı** |
| DSP'nin **donanım modu** (donanım hızlandırma yolu) | [[ADR-017-dsp-hardware-mode]] | ✅ dosya var — bu ADR **yeniden karar almaz** |
| Çip seçimi (XMOS/AK4458/PCM3168A) | [[ADR-038-8-1-sound-card-chip-selection]] | ✅ dosya var — bu ADR yalnız **politika** bağlar |

### (d) Bileşen seçim politikası (5 bağlayıcı kural)

| # | Kural | Karar | Dayanak |
|---|-------|-------|---------|
| 1 | **Lifecycle = birinci sınıf kısıt** | Güç/performans/maliyet ile **eş düzeyde** değerlendirilir; lifecycle = Active olmalı. Üst 50-100 parça için **risk register** + traffic-light skoru (yeşil = Active + geniş kaynak · sarı = daralan düğüm/tek kaynak · kırmızı = EOL/NRND) | Altium (2025-10-23), Mouser (2025-09-29) |
| 2 | **Çoklu kaynak + FFF yedek** | Yüksek etkili parça: **≥2 üretici veya 3 yetkili kanal**; form-fit-function yedek **önceden niteliklendirilmiş**; footprint en az iki aileyi taşıyabilmeli (swap-ready); tek kaynaklı parça **yeni tasarıma varsayılan olarak alınmaz** | Altium, Luminovo (2025-08-04), Suntsu |
| 3 | **Süreç + standart** | Obsolescence yönetimi **IEC 62402:2019** ile hizalı: çapraz fonksiyonlu sorumluluk · lifecycle/kritiklik bazlı BOM risk değerlendirmesi · düzenli BOM taraması (aktif ürün: çeyrek/yıllık) · PCN/PDN uyarıları · **bilinçli LTB** (gri pazar **yok** — sahte parça riski) | x-refs (IEC 62402), BENCOR (2025-10-21), Mouser |
| 4 | **Kararlı çekirdek** | Çekirdek silisyum (MCU/DAC/ADC/DSP) **kararlı süreç düğümü + geniş ekosistem** ile seçilir; yetenek farkı ABS/AM interface arkasına **sınır arayüzüyle** gizlenir ki parça değişimi dalgalanma üretmesin | Altium (2025-09-24), NXP/embvm (arayüz soyutlama) |
| 5 | **NRND/EOL kuralı** | NRND/EOL parça **yeni tasarıma alınmaz**; istisna yalnız proje sahibinin yazılı onayıyla ve §4.3 risk satırı **açık gerekçe** ile mümkündür. Mevcut kartta EOL gelirse sırası: yedek niteliklendirme → footprint uyarlama → **kontrollü yeniden tasarım** | Altium, Luminovo, x-refs |

> **Kapsam notu:** Bu 5 kural, CoreMusic'in **mevcut** çip seçimlerini **geçersiz kılmaz** ([[ADR-038-8-1-sound-card-chip-selection]] · [[ADR-089-classab-24v]] korunur); kural yalnızca **sonraki** seçimler ve yedekleme kararları için geçerlidir.

### (e) Hizalama — hangi L uzayı bu ADR'de geçerli?

| Kaynak | Ne diyor | Bu ADR'deki durum |
|--------|----------|-------------------|
| [[../../index.md]] `:87-98` | L6 = Electronics, L0 = Infrastructure | ✅ **GEÇERLİ** (bu karar bu uzaydadır) |
| `architecture/adlandirma-kurali.md:347` + `:363` §7.2/§7.3 | L6 = K6 Güvenlik · otomatik dönüşüm yasak | ⚠️ **AYRI UZAY** — raporlanır, dönüştürülmez |
| `.agents/master-orchestrator.md:129` | L6→…→L0 (L1 Security, L0 Infrastructure) | ✅ **aynı zincir** — Electronics/Security ayrımı `index.md` ile aynı |
| `architecture/katman-baglilik-matrisi.md` | Yalnız K0-K20 + A0-A5 (L grep = 0) | ✅ **K uzayının SSOT'u** — L6 burada **K0-K5 + K16-K20** olarak okunur |

---

## 3. Alternatifler (Alternatives)

| # | Alternatif | Neden reddedildi |
|---|-----------|------------------|
| 1 | **Dört ADR'yi tek "Electronics" dosyasında birleştirmek** (061-064 tek metin) | [[../index.md]] `:92-95` ve [[../../index.md]] `:693-696` **dört ayrı slot** kaydetmiş; tek dosya indeks/keys/brain beyanlarıyla çelişir ve sınır çizilemez. Ret. |
| 2 | **L6'yı K6'ya otomatik dönüştürmek** (`adlandirma-kurali §7.2` tablosuna bakıp "L6 = Güvenlik" demek) | `§7.3` otomatik dönüşümü **açıkça yasaklıyor**; iki uzay içerik olarak farklı (Electronics ≠ Güvenlik). Çakışma **raporlanır**, çeviri yapılmaz. Ret. |
| 3 | **`electronic/` dizinini SSOT ilan etmek** | `Test-Path` = **False** (0 dosya), `.ai/*.md` kökünde **69** kırık referans. SSOT = `architecture/k1-donanim` + `architecture/firmware` + `architecture/k3-ses-motoru`. Ret. |
| 4 | **Bileşen seçimini tek seferlik satın alma kararına indirgemek** | 2022'de ~750 bin, 2023'te ~470 bin parça EOL; EOL'ların ~%30'u resmi bildirimsiz. Tasarım sonrası müdahale yeniden tasarım demek → politika **tasarım aşamasında** bağlanır. Ret. |
| 5 | **SoM + carrier ayrımını hemen uygulamak** (endüstri standardı modülerlik) | Literatür bunu **öneriyor**, ama CoreMusic bugün **tek ana kart** üzerinde (k1-donanim blok diyagramı). Hemen uygulamak mevcut kart tasarımını geçersiz kılarak ADR-038/089/090'ı dolaylı yeniden karar alır. **Ret (ertelendi)** — §5.1 adım 8'de tetiklenecek koşul olarak saklanır. |
| 6 | **Çok-odalo senkronizasyonu (AES67/PTP, MIPI SWI3S) L6 kapsamına almak** | CoreMusic'in bugün ağ çok-oda topolojisi **kanıtlanamadı**; AES67/PTP ve SWI3S yalnız "gelecek seçenek" olarak not edilir → **kapsam dışı**, §4.3 risk 6'da izlenir. Ret (kapsam). |

---

## 4. Sonuçlar (Consequences)

### 4.1 Pozitif

- **Tek tanım:** L6 = hardware · firmware · driver · DSP · audio engine + tek yön zincir — bir sonraki agent "L6 nedir?" sorusunu tek satırda bulur.
- **Üç L uzayı ayrıştırıldı:** otomatik L→K dönüşümü engellendi (§7.3 ile uyumlu); Layer Violation yüzeyi daraldı.
- **Bölüm sınırı:** 061/062/063/064 birbirine girmez; `faz6-link-ledger.md:89`'daki ADR-061 kırık hedefi kapanır.
- **Politika = süreç:** çip seçimi bir kez değil, **ömür boyu** yönetilir; EOL sürprizi tasarımda yakalanır.
- **SSOT netleşti:** `electronic/` hayaleti yerine üç gerçek dizin yazıldı.

### 4.2 Negatif / maliyet

- **Çift yazım yükü:** L6 ve K uzayları yan yana yaşamaya devam eder; her yeni dokümanda hangi uzayın kullanıldığı **elle** belirtilmeli.
- **Kırık hedefler onarılmadı:** `architecture/l6-electronics` ve `architecture/l4-domain` + `electronic/` 69 referansı **ertelendi** (In-Place Refactoring / SRP) → kırık link sayacı geçici olarak yüksek kalır.
- **Firmware kodu 0:** 8 firmware dokümanının hiçbiri koda bağlı değil → "IMPLEMENTED" iddiası **yazılamaz**, her adım PLANNED olarak başlar.
- **062-064 metinsiz:** sınır yazılı olsa da komşu ADR'ler boş → konu taşması riski sürer.

### 4.3 Riskler

| # | Risk | Olasılık/Etki | Azaltma / Fallback |
|---|------|---------------|---------------------|
| 1 | **Katman sızıntısı** — L6 içinden doğrudan L5/L3'e erişim (veya ters yön çağrı) | Orta / Yüksek | Fallback: (i) sınır arayüzleri yazılı olmayan kod üretilmez, (ii) katman ihlali tespitinde **derhal revert + log ERROR** (AGENTS §18), (iii) §5.1 adım 5 ile sınır sözleşmesi dokümanı |
| 2 | **Tek kart bağımlılığı** — tüm ses yolu tek PCB'de (tek nokta arıza + tek yeniden tasarım) | Orta / Yüksek | Fallback: (i) blok sınırları (L3) **kart değişse de korunur**, (ii) §3.5'teki SoM/carrier ayrımı **koşul belirli** devreye girer (§5.1 adım 8: ikinci kart revizyonu ya da tedarik darboğazında) |
| 3 | **Tedarik ömrü (EOL)** — XMOS XU316 / AK4458 / PCM3168A tek kaynak + dar ömür | Orta / Yüksek | Fallback: (i) §2(d) kural 2-3 uygulanır (FFF yedek + risk register), (ii) kritik parça için bilinçli **LTB** (gri pazar yok), (iii) yedek niteliklendirilemezse **kontrollü yeniden tasarım** — ADR-038 yeniden açılır (yeni ADR ile, o metin düzenlenmez) |
| 4 | **Sinyal bütünlüğü** — diff-pair / I2S / güç ayrımı ihlali → gürültü, EMI | Orta / Orta | Fallback: (i) zonlama + sürekli referans düzlemi (k1-donanim `pcb-tasarim.md`), (ii) EMC test kapısı prototipte zorunlu, (iii) ölçüm protokolü ADR-063'e ertelenir |
| 5 | **L6/K6 isim çakışması** — agent "L6 = Güvenlik" okursa yanlış katman denetimi | Yüksek / Orta | Fallback: (i) bu ADR §2(e) tablosu **iki uzayı** yazar, (ii) otomatik dönüşüm yasak (§7.3), (iii) `adlandirma-kurali.md` §7.2'ye **çapraz uyarı satırı** sonraki reset'te eklenir (§5.1 adım 6) |
| 6 | **Kanıtsız iddialar** — "5 cihaz / 13 servis" (ADR-064) + `electronic/*` içerikleri + firmware kodu | Yüksek / Orta | Fallback: hepsi **⚠️ VERIFICATION REQUIRED** altında; kanıt gelmeden "var/implement edildi" **denmez**; ADR-062/063/064 kendi kanıtıyla doldurulmalı |
| 7 | **Numara çakışması** — kural 4 (ADR-088+) ↔ arşiv-serisi 061-064 | Düşük / Düşük | Fallback: raporlanır, **düzeltilmez** (§5.1 adım 9) — seri arşiv/indeks otoritesine bağlı kalır |

---

## 5. Uygulama (Implementation)

### 5.1 Adımlar

| # | Adım | Sorumlu | Durum |
|---|------|---------|-------|
| 1 | ADR-061 dosyasını `.ai/.decisions/accepted/` altına **indeks slug'ıyla** yaz (frontmatter 7 alan, debate ⏳) | Vault Steward | ✅ tamamlandı (bu işlem) |
| 2 | `log.md`'ye **tek satır** append: `ADR-061 yazıldı (debate PENDING)` | Vault Steward | ✅ tamamlandı (bu işlem) |
| 3 | `architecture/l6-electronics` + `architecture/l4-domain` kırık hedeflerinin **onarımı** (dizin oluşturmak yerine linkin gerçek hedefe yönlendirilmesi) | Vault Steward | ⏳ sonraki vault reset'i |
| 4 | `.ai/*.md` kökündeki **69 `electronic/` referansının** gerçek dizinlerle hizalanması | Vault Steward | ⏳ sonraki vault reset'i (AGENTS §24.3 uyarısı korunur) |
| 5 | **Sınır sözleşmesi** dokümanı: L6 ↔ L5 arayüzü + §2(a) madde 5'teki 3 istisnanın yazılı hali | Embedded + Audio HW | ⏳ debate sonrası |
| 6 | `adlandirma-kurali.md` §7.2'ye "L6 iki uzayda iki anlam taşır — otomatik dönüşüm yasak" **çapraz uyarısı** | Vault Steward | ⏳ sonraki vault reset'i |
| 7 | `keys.md`/`brain.md` satırlarında ADR-061'e **gerçek dosya bağının** eklenmesi (satır 92'deki `../brain.md` yerine dosya hedefi) | MO (vault-updater) | ⏳ vault-sync |
| 8 | **SoM/carrier ayrımı değerlendirmesi** (§3.5) — tetikleyici: ikinci kart revizyonu **veya** §4.3 risk 3'ün gerçekleşmesi | Audio HW + Product | ⏳ koşul bekliyor |
| 9 | **Numara çakışması raporu** (kural 4 ADR-088+ ↔ seride 061) | Vault Steward | ⏳ raporlandı (bu bölüm) |
| 10 | **Debate** (3 tur / 20 persona) → Tur 1-3 oylaması (18/2/0 KABUL) → Tech Lead onayı → `debate` alanı güncellendi | Debate ekibi + Tech Lead | ✅ tamamlandı (bu işlem) |
| 11 | ADR-062/063/064'ün **kendi kanıtıyla** doldurulması (her biri ayrı işlem) | Audio HW + Embedded | ⏳ bekliyor |
| 12 | **Şart 1a — hayalet SSOT kararı:** `electronic/` (0 dosya / 69 kırık ref) için ya dizin kurulur ya da 69 referans gerçek SSOT'a (`architecture/k1-donanim/` + `architecture/firmware/` + `architecture/k3-ses-motoru/`) yönlendirilir — adım 4 ile birlikte kapanır | Vault Steward | ⏳ debate şartı |
| 13 | **Şart 1b — K6 sınır düzeltmesi:** L6 Electronics ↔ K6 Güvenlik çelişkisi (§4.3 risk 5) `architecture/katman-baglilik-matrisi.md` katman matrisinde sınır düzeltmesiyle giderilir | Vault Steward | ⏳ debate şartı |
| 14 | **Şart 2 — ADR-064 doğrulama kapısı:** "5 cihaz / 13 servis" iddiası kanıtlanana kadar ⚠️ VERIFICATION REQUIRED kalır; kanıtsız sayı ADR-061'de tekrarlanmaz | Audio HW + Embedded | ⏳ debate şartı |
| 15 | **Şart 3 — tek "L" isim uzayı standardı:** üç ayrı "L" uzayı (index.md L0-L6 · adlandirma-kurali L0-L20 · master-orchestrator L0-L6) için K matrisi (`katman-baglilik-matrisi.md`) tek SSOT kabul edilir | Vault Steward | ⏳ debate şartı |

### 5.2 Geri Dönüş Planı

1. **Adım 1 geri dönüşü:** debate olumsuz çıkarsa dosya `status: rejected` veya `superseded by` ile işaretlenir (`../../.templates/adr/adr-template.md` §6.3) — **bu metin düzenlenmez**, yeni ADR ile bağlanır.
2. **Adım 3/4 geri dönüşü:** hedefler yanlış onarılmışsa `git checkout` + son commit (AGENTS §18 satır 5); vault-utf8-writer `.bak` yedeği (`C:/temp/opencode/vault-backups`) son çare.
3. **Adım 5 geri dönüşü:** sınır sözleşmesi yanlış yazılmışsa revert + log ERROR; mevcut dokümanlara **dokunulmaz**.
4. **Adım 8 geri dönüşü:** SoM/carrier'a geçiş ters giderse mevcut tek-kart tasarımı (ADR-038/089/090) **etkilenmez** — alternatif yalnız değerlendirilmedir, uygulama ayrı ADR gerektirir.
5. **Adım 10 geri dönüşü:** debate 3 tur sonunda RED olursa ADR `rejected` olur; indeks satırı (satır 92) **silinmez**, durum alanı güncellenir.

---

## 6. İlgili Dokümanlar

### 6.1 Çapraz Referans

| Dosya | Satır | İddia | Bu ADR ile durum |
|-------|-------|-------|------------------|
| `.ai/.decisions/index.md` | `:92` | slug = `ADR-061-electronics-architecture` | ✅ dosya adı bu satıra hizalandı (sapma raporlandı) |
| `.ai/.decisions/index.md` | `:93-95` | ADR-062/063/064 satırları | ⚠️ satır var, **dosya yok** — sınır §2(c)'de çizildi |
| `.ai/index.md` | `:87` | L0-L6 zincir yönü + yasaklı yönler | ✅ bu ADR §2(a) madde 3'te bağladı |
| `.ai/index.md` | `:91,93` | `architecture/l6-electronics` · `architecture/l4-domain` | ⚠️ ikisi de **diskte yok** → §5.1 adım 3 |
| `.ai/index.md` | `:693-696` | ADR-061..064 kayıtları | ✅ hizalı (061 dosyalandı) |
| `.ai/brain.md` | `:1009` | `ADR-061 \| Electronics Architecture (L6 Layer)` | ✅ bu ADR o özeti doldurur |
| `.ai/brain.md` | `:1012` | `ADR-064 … (L0-L6, 5 cihaz, 13 servis)` | ⚠️ **doğrulanamadı** → ADR-064'ün işi |
| `.ai/keys.md` | `:285,288` | ADR-061 / ADR-064 anahtar satırları | ✅ 061 bağlandı · 064 ⚠️ |
| `.ai/keys.md` | `:141-152` | `electronic/*` yolları ("vault'ta yok / DOĞRULAMA GEREKLİ") | ⚠️ **69 kırık referans** → §5.1 adım 4 |
| `.ai/MEMORY.md` | `:656` | "3 yeni ADR (061-063)" | ⚠️ dosyalar yoktu → bu işlem 061'i yazdı, **062/063 hâlâ yok** |
| `.ai/reports/faz6-link-ledger.md` | `:89` | `ADR-061..064` kırık hedef listesi | ✅ 061 kapatıldı · 062-064 açık |
| `.ai/architecture/adlandirma-kurali.md` | `:347`, `:363` | L6=K6 Güvenlik · §7.3 dönüşüm yasağı | ✅ §2(e) ile birlikte **iki uzay** olarak kayıtlı |
| `.ai/architecture/katman-baglilik-matrisi.md` | — | K0-K20 + A0-A5 (L grep = 0) | ✅ K uzayının SSOT'u (L6 → K0-K5 + K16-K20) |
| `.ai/architecture/k1-donanim/index.md` | `:22,:32,:54,:56,:61` | USB-C→XMOS→I2S→DAC→Diff→VAS→Output blok diyagramı | ✅ §2(b) hiyerarşisinin kanıtı |
| `.agents/master-orchestrator.md` | `:78,:129` | L6→L0 zinciri + Layer Violation kuralı | ✅ üçüncü L uzayı — §2(e) |

### 6.2 Bağlantılar

- Şablon: [[../../.templates/adr/adr-template.md]] (Guardrail #16)
- Format referansı: [[ADR-059-jwt-library-and-mfa]]
- İlgili ADR'ler: [[ADR-017-dsp-hardware-mode]] · [[ADR-038-8-1-sound-card-chip-selection]] · [[ADR-039-7-service-platform-architecture]] · [[ADR-040-database-authority]] · [[ADR-089-classab-24v]] · [[ADR-090-channel-variant-product-family]]
- Vault kökü: [[../../index.md]] · [[../../brain.md]] · [[../../keys.md]] · [[../../MEMORY.md]] · [[../../log.md]] · [[../../AGENTS.md]] · [[../index.md]]
- Debate: §7.2 · §7.2.1 (tur kaydı) · §6.3 (3 şart) · §5.1 adımlar 12-15
- Dizin: `architecture/k1-donanim/` (23) · `architecture/firmware/` (8) · `architecture/k3-ses-motoru/` (18) · `architecture/k17-guc-kaynagi` · `architecture/k19-pcb` · `architecture/k20-bom` · `architecture/k16-class-ab` · `architecture/k2-surucu` — **düz metin yollarıdır** (wiki-link değil: hedefler dizin, .md dosyası değil)

### 6.3 Debate Şartları (3 tur / 20 persona — 18/2/0 KABUL)

| # | Şart | Tur 2 kaynağı | §5.1 karşılığı |
|---|------|---------------|----------------|
| 1a | `electronic/` hayalet SSOT kararı + 69 kırık referansın gerçek dizinlere yönlendirilmesi | İtiraz 1 (DevOps: hayalet SSOT şart) | adım 12 |
| 1b | L6↔K6 sınır düzeltmesi — `architecture/katman-baglilik-matrisi.md` katman matrisinde sınır düzeltmesi | İtiraz 2 (Security: K6 çelişkisi şart) | adım 13 |
| 2 | ADR-064 "5 cihaz / 13 servis" için doğrulama kapısı (⚠️ VERIFICATION REQUIRED korunur) | İtiraz 3 (Critic: V.R. şart) | adım 14 |
| 3 | Tek "L" isim uzayı standardı — K matrisi tek SSOT | İtiraz 4 (DSP: isim uzayı) | adım 15 |

---

## 7. Onay

### 7.1 Onay Akışı

| Rol | Kişi | Tarih | Durum |
|-----|------|-------|-------|
| Vault Steward | CoreMusic Vault Steward | 2026-09-29 | ✅ |
| Tech Lead | CoreMusic Tech Lead | 2026-09-29 | ✅ |
| Arch Lead | — | — | ⏳ |

### 7.2 Debate

| Alan | Değer |
|------|-------|
| Debate | ✅ **TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)** |
| Tur sayısı | 3 |
| Oy | **18 kabul / 2 çekimser / 0 red → KABUL** |
| Tech Lead | ✅ |
| Arch Lead | ⏳ (ayrı onay — bu işlem kapsamında değil) |

#### 7.2.1 Tur Kaydı

| Tur | Kapsam | Sonuç |
|-----|--------|-------|
| Tur 1 | 20 persona bulgu turu — L6 = hardware+firmware+driver+DSP+audio engine; zincir tek yön L6→L0 + 3 yazılı istisna; 3 seviye Sistem→Kart/Modül→Blok (sınır = konektör+güç+EMC); ADR-062/063/064 diskte YOK → düz metin + V.R.; 5 kural bileşen politikası (IEC 62402, EOL/obsolescence, gri pazar yasak); `electronic/` 0 dosya/69 ref hayalet SSOT; `l6-electronics`/`l4-domain` dizinleri yok; firmware kod 0; "5 cihaz/13 servis" doğrulanamaz ⚠️ (ADR-064); 3 ayrı "L" isim uzayı; L6 Electronics ↔ K6 Güvenlik çelişkisi (§4.3 risk 5); 3 wiki-link düz metne çevrildi (59/59 çözülüyor); 30 kaynak / 4 sorgu | 16 kabul/neutral · 4 uyarı (DevOps: hayalet SSOT · Security: K6 çelişkisi · Critic: V.R. · DSP: isim uzayı) |
| Tur 2 | İtiraz→çözüm: (1) `electronic/` hayalet SSOT + 69 kırık ref → dizin kararı, (2) L6↔K6 → katman matrisinde sınır düzeltmesi, (3) "5 cihaz/13 servis" kanıtsız → ADR-064 V.R. + doğrulama kapısı, (4) 3 "L" isim uzayı → tek isim uzayı standardı (K matrisi SSOT) | 4 şart → §5.1 adımlar 12-15 + §6.3 |
| Tur 3 | Oy | **18 kabul / 2 çekimser / 0 red → KABUL** |

> **Not:** `status: accepted` bu ADR'nin **kapsam kararının** (§2 a-e) vault tarafından kabul edildiğini gösterir; **debate ✅ TAMAMLANDI** (3 tur / 20 persona · 18/2/0 KABUL · Tech Lead ✅); **Arch Lead ⏳** ve §5.1 adımları (debate şartları 12-15 dâhil) yerine getirilmedikçe **Frozen'a geçiş YOK**tur. ADR-001–037 frozen kapsamı dışındadır.
