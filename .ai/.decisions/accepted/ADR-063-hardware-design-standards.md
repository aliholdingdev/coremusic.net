---
title: "CoreMusic — ADR-063: Hardware Design Standards (PCB Kural Seti · Sinyal Bütünlüğü & EMC · Bileşen/Kart Kalitesi · Dokümantasyon Standardı · AES17 Fabrika Test Hizası)"
type: "architecture-decision"
category: "electronics"
date: "2026-09-30"
updated: "2026-09-30"
version: "1.0.0"
status: "accepted"
authority: "SSOT — CoreMusic hardware design standards (A0/A5) kararı: (a) **PCB kural seti** = `k19-pcb/index.md` tasarım kuralları tablosu bağlayıcı minimumdur (min iz/boşluk 0.1mm · high-speed 0.2mm · drill 0.2mm · annular 0.15mm · copper-to-edge 0.3mm · mask dam 0.075mm) + IPC-2221 (akım) ve IPC-2141 (empedans) hesap referansları; tablo ihlali = yeni ADR, (b) **SI/EMC** = tek sürekli GND + bölme disiplini (ADR-038 §2.2-b ile aynı reçete) + IEC 61000-4-2/4-3/4-4/4-5 immünite matrisi test kapısı; DRC kapısı (k19) fabrikasyon öncesi, EMC kapısı (k19) lansman öncesi **işaretsiz kapatılamaz**, (c) **bileşen/kart kalitesi** = IPC-6012 **Class 2** (rigid board, tedarik dökümüyle belgelenir) + IPC-A-610 Class 2 işçilik + IPC-2611/2614 tedarik dokümantasyonu; bileşen lifecycle/obsolescence ADR-061'in 5 bağlayıcı kuralına (IEC 62402 ruhu) bağlanır — yeniden yazılmaz, (d) **dokümantasyon standardı** = şema + BOM + layout **tek revizyon seti**, her değişiklikte revizyon kaydı; üretim çıktısı IPC-2581 (ya da ODB++/Gerber) — bu standart vault'ta **bugün YOKTUR** (2 isabet/0 standart), bu ADR ile kurulur, (e) **AES17 fabrika testi** = ADR-038 M1-M6 (AES17-2020 + IEC 61606-1 + EIAJ CP-2404) **bağlayıcıdır** ve k19/k20 test dokümanlarına bağlanır — ölçüm tarifi yeniden yazılmaz, (f) **sınır** = L6 üst mimari + bileşen politikası ADR-061'de, DSP pipeline ADR-062'de, platform/L0-L6 geneli ADR-064'te (dosya diskte YOK ⚠️); bu ADR üçünü de yeniden almaz"
kaynak: "Disk kanıtı taraması (2026-09-30: `.ai/.decisions/index.md:94` slug satırı MEVCUT (`ADR-063-hardware-design-standards`) · `.ai/index.md:695` · `.ai/brain.md:1011` · `.ai/keys.md:287` · `.ai/log.md:57` · `.ai/MEMORY.md:684` · bu işlem öncesi `**/ADR-063*.md` = **0 dosya** → metin diskte YOKTU · `architecture/k19-pcb/` = **12** .md (tasarım kuralları `index.md:44-55`, DRC ☐ `:178`, EMC ☐ `:180`, K14/K20 etiket çelişkisi `:161-162`, IEC 61000-4-2/3/4/5 `emc-compliance.md:23-26,296-299`, EMC sertifika ☐ `:369`, IPC-2141 `controlled-impedance.md:48`, IPC-2221 `power-distribution.md:275` + `pcb-fabrication.md:114`, IPC-4101/IPC-4101D/IPC-TM-650 `pcb-fabrication.md:39-48`, flying probe/ICT/Gerber `:226-260,332+`) · `architecture/k20-bom/` = **12** .md (`production-tools.md:118` IPC-A-610 Class 2 %100 · `:119` schematic'e uygunluk · `index.md:78`) · `architecture/k1-donanim/` = **23** .md (`pcb-tasarim.md` · `diff-pair-input.md` · `konnektorler.md` · `termal-yonetim.md`) · repo geneli `git ls-files` `*.kicad_pcb|*.brd|*.sch|*.kicad_sch` = **0 dosya** · `*.cpp|*.h|*.hpp|*.c` = **0 dosya** → uygulama PLANNED · `schematic|şematik|revizyon` grep'i (architecture) yalnız **2 içerik isabeti** (`k20-bom/production-tools.md:119` · `k1-donanim/koruma-devreleri.md:184` "Şematik hazır, layout yok") → şematik/BOM/revizyon **standardı YOK** · AES17 grep'i `k19|k1|k20` = **0 isabet**; yalnız `ADR-038` (`:88-98,156-159,236,259`) + `ADR-090` (`:173,196,242`) · `ADR-064` glob = **0 dosya** → düz metin + ⚠️ · `.ai/architecture/index.md:96,114,115` K1/K19/K20 → yalnız `ADR-038, ADR-089` (**ADR-063 bağlantısı YOK** → §5.1/6) · `faz6-link-ledger.md:89` ADR-061..064 kırık hedef listesi (061 kapandı, 062 kapandı, **063 bu işlemle kapanır**, 064 açık) · `.ai/reports/faz6-link-ledger.md` MEVCUT) + web araştırması (**5 sorgu / 46 kaynak bildirimi / 44 benzersiz** — Q2↔Q5 arası IPC-6012E/F tekrarı düşüldü)"
governance: "Red Team → Human Mode → Truth Mode"
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)"
---

# CoreMusic — ADR-063: Hardware Design Standards

> **Durum:** accepted (**debate ✅ TAMAMLANDI — 18/2/0 KABUL**) — **Tarih:** 2026-09-30 — **Debate:** ✅ **TAMAMLANDI** (3 tur / 20 persona — 18 kabul / 2 çekimser / 0 red → KABUL) — **Tech Lead:** ✅ — **Arch Lead:** ⏳
> **Karar serisi:** `.ai/.decisions/accepted/` — **Slug:** `ADR-063-hardware-design-standards` (dizin otoritesi: [[../index.md]] satır 94 — gerçek disk slug'ı ile birebir hizalı ✅)
> **İlgili kararlar:** [[ADR-061-electronics-architecture]] (L6 üst mimari + 5 bileşen kuralı — `:128`'de "tasarım standartları → ADR-063" delegasyonu **bu ADR ile kapanır**; bu ADR politikayı yeniden almaz) · [[ADR-062-dsp-pipeline-architecture]] (DSP boru hattı — **kapsam dışı**, pipeline'ın fabrika ölçümüyle kesişimi yalnız AES17 satırıdır) · [[ADR-038-8-1-sound-card-chip-selection]] (AES17-2020 + IEC 61606-1 + EIAJ CP-2404 M1-M6 ölçüm tarifi — **bu ADR tarifi yeniden yazmaz**, yalnız k19/k20 test dokümanlarına **bağlar**) · [[ADR-089-classab-24v]] + [[ADR-090-channel-variant-product-family]] (ürün/aile kararları — standartlar bunları **uygular, yeniden almaz**) · [[ADR-017-dsp-hardware-mode]] (katman ve RT kısıtları — standart kapsamı dışısı) · [[ADR-005-ultrathink-protocol]] (`⚠️ VERIFICATION REQUIRED` kanıt standardı) · karar dizini [[../index.md]] **satır 94**.
> **⚠️ VERIFICATION REQUIRED (komşu metin hâlâ yok):** `ADR-064-electronics-platform-architecture` **diskte dosya olarak YOK** (glob = 0) → bu ADR'de **düz metindir, wiki-link değildir**; ayrıca IPC-6012/IPC-A-6012/AES17/IEC standartlarının **tam metinleri ücretli/erişim kısıtlıdır** — bu ADR'de yalnız özet ve atıf vardır, bayt düzeyinde alıntı **yoktur**; "EMC sertifikası alındı" gibi kanıtsız hiçbir iddia yazılmamıştır (k19 checklist'i hâlâ ☐).
> **Bölüm sınırı (ADR-061 §2(d) ile kenetli):** ADR-061 L6 katmanını + kart/modül hiyerarşisini + bileşen **seçim politikasını** yazdı; **tasarım standartlarını bu ADR yazar**; platform/L0-L6 geneli `ADR-064`'e aittir (dosya ⚠️ metinsiz) → bu ADR onun konusunu **yazmaz**.
> **Frozen değil:** ADR-001–037 frozen kapsamı dışındadır; kural 7'deki "yeni ADR ≥ 088" ile arşivin 063 slotu arasındaki **numara çakışması ADR-061 §5.1/9 ve ADR-062 künyesinde raporlandı** — bu ADR aynı çakışmayı **tekrar raporlar, düzelmez**.

---

## 1. Bağlam (Context)

CoreMusic donanım tarafının tasarım kuralları, EMC test kapıları, üretim kalite kriterleri ve şema/BOM revizyon disiplini **üç ayrı klasöre dağılmış** durumda: `architecture/k19-pcb/` (12 doküman — kural seti, SI, EMC, fabrikasyon), `architecture/k20-bom/` (12 doküman — BOM listeleri, üretim/test araçları) ve `architecture/k1-donanim/` (23 doküman — `pcb-tasarim.md`, diff-pair, konektör, termal). Bu dokümanların her biri **teknik olarak dolu**, ama hiçbirinde "bu kurallar **bağlayıcıdır**, ihlali yeni ADR ister" satırı yok; sahiplik belirsiz. Buna karşılık repo'da **tek bir PCB CAD dosyası bile yok** (`*.kicad_pcb|*.brd|*.sch|*.kicad_sch` = 0) ve **şema/BOM/revizyon standardı** vault'ta hiç yok (grep yalnız 2 içerik isabeti, 0 standart). Fabrika ölçüm standardı ise yalnız ADR-038'de (AES17 M1-M6) duruyor ve `k19`/`k20` test dokümanlarına **bağlanmamış**. Bu ADR üç boşluğu tek kayıtta kapatır: **(1)** PCB/SI/EMC/güç kurallarını bağlayıcı hale getirir, **(2)** bileşen/kart kalite ve dokümantasyon standartlarını kurar, **(3)** AES17 fabrika testini üretim dokümanlarına bağlar — kod yazmaz, CAD açmaz, sertifika almaz.

### 1.1 Mevcut Durum (disk kanıtı — dürüst etiket, 2026-09-30 taraması)

| İddia | Kanıt | Etiket |
|-------|-------|--------|
| ADR-063 slotu ayrılmış mı? | [[../index.md]] `:94` → `\| ../brain.md ADR-063-hardware-design-standards \| Hardware Design Standards \| Electronics \|` · [[../../index.md]] `:695` · [[../../brain.md]] `:1011` · [[../../keys.md]] `:287` · [[../../log.md]] `:57` · [[../../MEMORY.md]] `:684` | ✅ **KAYITLI** (6 indeks/katalog satırı) |
| ADR-063 dosyası bu işlem öncesi diskte var mıydı? | glob `.ai/.decisions/**/ADR-063*.md` = **0 dosya** (accepted/ altında ADR-061 ve ADR-062 vardı, 063/064 yok) | ❌ **YOKTU** → bu işlemde yazılıyor |
| **PCB kural seti** | [[../../architecture/k19-pcb/index]] `:44-55` → min iz 0.1mm (4mil) · güç izi 0.3-1.0mm · min boşluk 0.1mm · high-speed 0.2mm · drill 0.2mm · annular 0.15mm · copper-to-edge 0.3mm · mask dam 0.075mm | ✅ **IMPLEMENTED (doküman)** — kural seti var, **bağlayıcılığı bu ADR ile geliyor** |
| Hesap referansları (IPC) | `k19-pcb/power-distribution.md:275` IPC-2221 (iz akım kapasitesi) · `pcb-fabrication.md:114` IPC-2221 · `controlled-impedance.md:48` IPC-2141 (empedans hesabı) · `pcb-fabrication.md:39-48` IPC-4101/IPC-4101D (FR-4, Dk/Df) + IPC-TM-650 (Tg 170°C, Td 340°C, peel 1.4 N/mm) | ✅ **IMPLEMENTED (doküman)** |
| Sinyal bütünlüğü / EMC | `k19-pcb/` → `signal-integrity.md` · `controlled-impedance.md` · `star-grounding.md` · `emc-compliance.md:23-26,296-299` → ESD ±8kV/±15kV (IEC 61000-4-2) · EFT ±1kV (61000-4-4) · Surge ±1kV (61000-4-5) · radyasyonel 3V/m (61000-4-3) | ✅ **IMPLEMENTED (doküman)** — kapılar **işaretsiz** (aşağıda) |
| Kapılar kapalı mı? | `emc-compliance.md:369` `- [ ] EMC sertifikası alınması` · `k19-pcb/index.md:178` `- [ ] DRC/DRC checks` · `:180` `- [ ] EMC sertifikasyon testi` | ⚠️ **AÇIK KAPI (☐)** — üçü de işaretsiz |
| **Kart/komponent kalitesi** | `k20-bom/production-tools.md:118` → lehim kalitesi **IPC-A-610 Class 2** %100 · `k20-bom/index.md:78` aynı standart · flying probe/ICT/Gerber (`pcb-fabrication.md:226-260,332+`) | ✅ **IMPLEMENTED (doküman)** — yalnız işçilik; **kart performans sınıfı (IPC-6012) yazılmamış** |
| **Şematik/BOM/revizyon dokümantasyon standardı** | grep `schematic\|şematik\|revizyon` (architecture) → yalnız **2 içerik isabeti**: `k20-bom/production-tools.md:119` "Electrical test · schematic'e uygunluk %100" · `k1-donanim/koruma-devreleri.md:184` "Şematik hazır, layout yok"; **standart/tanım dosyası = 0** | ❌ **YOK** → bu ADR §2(d) ile kuruyor |
| PCB CAD dosyası | `git ls-files` → `*.kicad_pcb\|*.brd\|*.sch\|*.kicad_sch` = **0 dosya** | ⏳ **PLANNED** — DRC kapısı şimdilik doküman kapısidir |
| **AES17 fabrika testi** | grep `AES17` → `k19-pcb\|k1-donanim\|k20-bom` = **0 isabet**; yalnız [[ADR-038-8-1-sound-card-chip-selection]] `:156-159` (M1-M6 tablosu: THD+N · SNR · kanal ayrımı · frekans/level matrisi) + `:259` (şart 2: fabrika ölçüm protokolü) ve [[ADR-090-channel-variant-product-family]] `:173,196,242` | ⚠️ **BAĞLANTISIZ** — üretim testi dokümanlarında AES17 **yok** |
| ADR-064 (platform) | glob = **0 dosya**; [[../index.md]] `:95` + [[../../index.md]] `:696` + [[../../brain.md]] `:1012` satırları var | ⚠️ **VERIFICATION REQUIRED** (düz metin — wiki-link yok) |
| K1/K19/K20 ↔ ADR bağlantıları | `.ai/architecture/index.md:96` → `K1 … ADR-038, ADR-089` · `:114` → `K19 … ADR-089` · `:115` → `K20 … ADR-089` | ⚠️ **ADR-063 BAĞLANTISI YOK** → §5.1/6 (bu işlemde index'e dokunulmadı) |
| Etiket çelişkisi | `k19-pcb/index.md:161-162` → "K14 **Test**" ve "K20 **Mekanik**" · `.ai/architecture/index.md:109` → K14 = **Ağ & İletişim** · `:115` → K20 = **BOM & Üretim** | ⚠️ **ÇELİŞKİ** → §5.1/7 (raporlandı, düzeltilmedi) |
| Kod kanıtı | `git ls-files` `*.cpp\|*.h\|*.hpp\|*.c` = **0 dosya** | ⏳ **PLANNED** — §2 maddeleri uygulama kapılarına bağlı |
| Delegasyon satırı | [[ADR-061-electronics-architecture]] `:128` → "Tasarım standartları … `ADR-063-hardware-design-standards` … ⚠️ metin yok" | ✅ **bu ADR ile kapanır** (metin yazıldı) · `:193` ölçüm protokolü erteleme fallback'i §2(e) ile bağlanır |

### 1.2 Sorun Tanımı

1. **Kural seti var ama bağlayıcılığı yok.** `k19-pcb/index.md:44-55` tablosu kusursuz bir minimum sunuyor ama "bunu ihlal eden tasarım üretime gitmez" diyen tek satır yok; sonuç: iki agent iki farklı minimum çizgisinde tasarım yapabilir (min iz 0.1mm mı, 0.15mm mı tartışması).
2. **Üç kalite kapısı işaretsiz.** DRC (`k19:178`), EMC sertifikasyon testi (`k19:180`) ve EMC sertifikası (`emc-compliance:369`) üçü de ☐ — kimsenin kapatmakla yükümlü olmadığı açık kapılar; EMC reddi ürün lansmanını doğrudan durdurur.
3. **Dokümantasyon standardı sıfır.** Şema, BOM ve layout'un **hangi revizyon setinde** yaşadığı, değişikliklerin nasıl kaydedildiği hiçbir yerde yazmıyor (2 isabet, 0 standart); CAD dosyası da 0 olduğu için ilk açılan dosya **disiplinsiz** açılacak.
4. **Kalite sınıfı belirsiz.** İşçilik standardı IPC-A-610 **Class 2** olarak yazılmış (`k20:118`), ama **kart performans sınıfı** (IPC-6012 Class 1/2/3) hiçbir yerde yok — tedarikçiye "hangi sınıf?" sorusu sorulamaz, kulağa "Class 3 mü Class 2 mi?" belirsizliği doğar.
5. **AES17 üretim testine bağlanmamış.** ADR-038 M1-M6 ölçüm tarifi vault'ta bağlayıcı, ama `k19`/`k20` test dokümanlarında AES17'den **tek kelime yok** → fabrika testi kâğıtta kalır (ADR-090'un "bir kez kurulur, varyantlara uygulanır" iddiası da burada kapanmaz).

### 1.3 Web'den Araştırma Raporu & Sonuçları

| Alan | Değer |
|------|-------|
| Web Search **Query** | (1) "PCB design rules 2025 clearance trace width IPC-2221 3W rule annular ring DRC split plane EMI" · (2) "IPC-6012 Class 2 vs Class 3 performance class requirements PTH copper thickness IPC-2611 procurement documentation" · (3) "hardware design documentation standard schematic BOM revision control IPC-2581 ODB++ Gerber release process" · (4) "AES17 audio measurement standard factory test IEC 61606 QC of audio devices EVT DVT PVT golden unit" · (5) "IPC-6012 qualification rigid printed boards component qualification JEDEC AEC-Q obsolescence IEC 62402" |
| Web Search **Konusu** | **(1)** PCB kural seti — minimum iz/boşluk, 3W kuralı, annular ring, DRC disiplini, split-plane EMI; **(2)** kart performans sınıfları — IPC-6012 Class 2↔3 farkı, PTH bakır kalınlığı, tedarik dokümanı zorunlulukları; **(3)** dokümantasyon standardı — şema/BOM/revizyon kontrolü, üretim veri formatları (IPC-2581/ODB++/Gerber), release süreci; **(4)** fabrika ölçümü — AES17-2020/IEC 61606 test tarifi, QC/EVT-DVT-PVT akışı, altın birim; **(5)** nitelik — IPC-6012 kualifikasyon testleri, bileşen niteliği (JEDEC/AEC-Q), obsolescence (IEC 62402) ve sahtekârlık önleme. |
| Web Search **Bağlamı** | CoreMusic: kural seti/EMC/BOM dokümanları var (k19=12, k20=12, k1=23 dosya) ama bağlayıcılık, sınıf seçimi, dokümantasyon standardı ve AES17↔üretim bağı **yok**; CAD/kod = 0 dosya; ADR-061 `:128` standartları bu ADR'ye delege etmiş. Araştırma bu dört boşluğu hedefliyor; AES17'nin kendisi ADR-038'de kararlı olduğu için (4) yalnız **hizalama** için okundu, yeniden karar için değil. |
| Web Search **Kısa Açıklama** | **(1)** Endüstri minimum kuralları IPC-2221 tabanlı veriyor (iz/boşluk, 3W kuralı ~%50 korelasyon düşüşü için, annular ring ≥0.15mm) ve DRC'yı **fabrikasyon öncesi zorunlu kapı**; split-plane ve sürekli referans düzlemi EMI'nin birinci kaynağı. **(2)** IPC-6012 Class 1/2/3 üçlüsü IPC-6011'de tanımlanır; Class 2 "dedicated service / extended lifetime" (ürün profiline uygun), Class 3 "hiç kesinti yok" (aşırı); PTH bakır ≥20µm (C2) ↔ ≥25µm (C3); sınıf **tedarik dokümanında açıkça yazılır** (IPC-2611/2614). **(3)** Dokümantasyon standardı üretim verisini **tek format + revizyon kaydı** ister: IPC-2581 (BOM+ECAD+AVL+HistoryRec/FileRevision/ChangeRec taşır), ODB++ veya Gerber (en az Gerber+netlist); release = şema↔BOM↔layout eşit revizyonu, değişiklik ChangeRec ile kayıtlı. **(4)** AES17-2020 ölçüm tarifi (23±5°C, notch Q≥5, ekipman doğruluğu 3×, THD+N/SNR/crosstalk) + IEC 61606-1:2009/61606-3; QC = altın birim + EVT/DVT/PVT aşamalı kabul, üretimde %100 elektrik test. **(5)** IPC-6012 §4 "Qualification / C=0 sampling / periodic conformance" ile kartı niteler; bileşen tarafında AEC-Q100 (otomotiv) ve IEC 62551/62564 ailesi COTS niteliği, obsolescence IEC 62402 + PCN/LTB + sahteciliğe karşı önlem standartları var. |
| Web Search **Uzun Açıklama** | **(i) Kural seti:** schemalyzer/Altium/EMAEDA/HILPCB rehberleri minimum değerleri IPC-2221'e dayandırır: temel boşluk-iz çifti 0.1mm/4mil'den aşağı inmez, high-speed hatlarda 0.2mm/8mil ve 3W (hat merkezleri arası 3× genişlik) kuralı kullanılır, annular ring ≥0.15mm via güvenilirliği için alt sınırdır, DRC **tasarım tamamlanmadan geçilmez**; split-plane'de hat asla yarığı kesmez (kesim = EMI döngüsü + jitter, TI SPRACP4/SLYT512 ile aynı ders — ADR-038 §2.2-b'ye referans), sürekli GND + zonlama + güç ayrımı CEMI'yi düşürür. TI SNLA426 güç/geri-ön yerleşim kurallarını, inorsen 2025 rehberi fabrikasyon-öncesi DRC/DFA listesini verir. **(ii) Sınıf seçimi:** IPC-6012E/F, sınıfları IPC-6011'den alır ve "sınıf tedarik dokümanında belirtilir" der (IPC-2611/2614); Class 2 = özel servis, uzun ömür, kabul edilebilir ara sıra arıza — tüketici/pro-audio ürün için endüstri standardı tercih; Class 3 = kesintisiz kritik (ilaç/askeri/havacılık) — maliyet ve test sertiliği artar; PTH bakır ≥20µm (C2) ↔ ≥25µm (C3), bow&twist C2 ≤%0,75 ↔ C3 ≤%0,5 (inorsen); ULTRONIU karşılaştırması Class 2'nin "geniş kullanım, döngü testleri + görsel/mikroskopik muayene", Class 3'ün "sürekli performans + sıkı tolerans" olduğunu yazar; addendum'lar (IPC-6012FS uzay/askeri, IPC-6012FA otomotiv) yalnız o ortamlar için geçerlidir ve **tek başına kullanılamaz**. **(iii) Dokümantasyon:** IPC-2581B/C tek dosyada BOM + ECAD + AVL + revizyon geçmişi (`HistoryRec`, `FileRevision`, `ChangeRec`) taşıdığı için şema-BOM-layout eşzamanlılığını zorunlu kılar; ODB++ ve Gerber (X2) üretimin klasik çıktısıdır, netlist/electrical test ile birlikte şarttır (Sierra Circuits kontrol listesi, Sparx dosya-tipi rehberi, PCEA 2026 DfX sütunu revizyon disiplinini "release öncesi tek gerçek revizyon" olarak vurgular); bu, vault'taki "schematic'e uygunluk %100" (`k20:119`) tek satırının **eksik kalan yarısıdır**. **(iv) Fabrika ölçümü:** AES17-2020 (aes2.org + ANSI/NSAI kaydı; 23±5°C, notch Q 1-5, ekipman doğruluğu ≥3×) THD+N/SNR/crosstalk'un **tarifini** verir; IEC 61606-1:2009 (genel ses cihazları) ve 61606-3 (gömülü sistemler) aynı ailenin IEC koludur; audioXpress QC makalesi **altın birim + EVT/DVT/PVT** aşamalı kabulü, EETimes ise üretim test faktörlerini (bed test, otomasyon, örneklem) anlatır; FADGI/IASA TC-04 test matrisi (ADR-038 M4 ile aynı) tekrarlanabilirliği sağlar. **(v) Nitelik/ömür:** IPC-6012 §4.1 kualifikasyon + §4.2 **C=0** (sıfır kabul numunesi) örnekleme + §4.3 periyodik conformans ister; tedarikçi "IPC-6012 Class 2 belgesi + test raporu" ile denetlenir (inorsen); bileşen cephesinde IEC 62564-1 (AQEC) COTS niteliğini ve **PCN / last-time-buy / obsolescence / counterfeit prevention** bölümlerini zorunlu kılar, AEC-Q100:2023 nitelik planı aday gösterir, IEC 62239-1 (ECMP) bileşen yönetim planını tanımlar; işaretleme IPC-1066 / IPC-JEDEC J-STD-609 / JEDEC JIG101 / JEITA ETR-7021 ile standardize edilir — bu, ADR-061'in IEC 62402'li 5 kuralının **standart karşılığıdır**. |
| Web Search **Paragraf Veri Uzun** | 5 sorgu / **46 kaynak bildirimi / 44 benzersiz** (Q2↔Q5 arası IPC-6012E/F tekrarı düşüldü): **(1, 7)** schemalyzer.com PCB design guide (2 makale — clearance/3W) · TI **SNLA426** · inorsen "PCB Design Guide 2025" · EMAEDA stackup makalesi · HILPCB handoff rehberi · Altium resource makalesi. **(2, 6)** **IPC-6012E** · **IPC-6012F** · **IPC-6011** (performans sınıfları) · **IPC-A-600** (görsel kabul) · **IPC-2611/2614** (tedarik dokümanı) · ultroniu Class 2↔3 karşılaştırması. **(3, 7)** **IPC-2581B** · **IPC-2581C** · ODB++ (format makalesi) · Gerber (format makalesi) · Sparx Engineering dosya-tipi rehberi · PCEA 2026 DfX sütunu · Sierra Circuits IPC-2581 kontrol listesi. **(4, 8)** aes2.org **AES17-2020** · ANSI/NSAI webstore kaydı · AES17-2020 PDF şartnamesi (23±5°C · notch Q≥5 · ekipman doğruluğu 3×) · **IEC 61606-1:2009** · **IEC 61606-3:2008** · audioXpress "What to Measure for QC of Audio Devices" (altın birim · EVT/DVT/PVT) · EETimes "Manufacturing Test Factors for Audio Products" · FADGI/IASA **TC-04**. **(5, 8 birincil)** IPC-6012E (ANSI önizleme) · IPC-6012F TOC · IPC-6012D TOC · IPC-6012E TOC (fed.de) · **IPC-6012FS** uzay/askeri addendum · **IPC-6012FA** otomotiv addendum · inorsen IPC-6012 sayfası (Class 1/2/3 · bow&tweep · QA testleri) · **IEC 62564-1** AQEC teknik şartnamesi — + atıf: AEC-Q100:2023 · IEC 62239-1 (ECMP) · IPC-1066 · IPC/JEDEC J-STD-609 · JEDEC JIG101 · JEITA ETR-7021 · DSIAC Physics of Failure handbook. |
| Web Search **Sonucu** | **(1) Karar destekleniyor:** k19 `:44-55` tablosu endüstri minimumlarıyla **uyumlu** (0.1mm iz/boşluk, 0.15mm annular, 0.2mm high-speed ↔ 3W ruhu) → tablo bağlayıcı alınır; DRC fabrikasyon öncesi kapı olur. **(2) Karar destekleniyor:** ürün profili (pro-audio, kesintisiz kritik değil) **Class 2**'ye uyar; ADR-061'deki "ölçüm/derating" delegasyonu ve k20'deki IPC-A-610 Class 2 ile **tek sınıf** bütünlüğü sağlanır; Class 3 reddedildi (§3/2), addendum'lar gerekirse ayrı ADR. **(3) Karar destekleniyor (boşluk doğrulandı):** dokümantasyon standardı vault'ta gerçekten yok — IPC-2581/ODB++ + revizyon kaydı şablonu olarak alınır, **PLANNED** etiketli (CAD=0). **(4) Hizalama sağlandı:** AES17-2020 + IEC 61606-1 + EIAJ CP-2404 tarifi ADR-038'te zaten kararlı; bu ADR yalnız **k19/k20 test dokümanlarına bağlama** maddesini yazar (tekrar yok). **(5) Karar destekleniyor:** IPC-6012 §4 kualifikasyon/C=0/periyodik conformans + IPC-2611/2614 tedarik dokümanı + IEC 62402/62239-1 obsolescence, ADR-061'in 5 kuralının standart karşılığını oluşturur → §2(c). **İtiraz/karşıt bulgu:** standartların **tam metinleri ücretli/erişim kısıtlıdır** (ANSI/IEC webstore önizlemeleri) → bu ADR'de yalnız özet+atıf; "tam metinle doğrulandı" iddiası yazılmaz (künye ⚠️ satırı + §4.3 risk 5). |
| Web Search **Alınan Karar** | **(a) PCB kural seti** = `k19-pcb/index.md:44-55` tablosu **bağlayıcı minimum** alınır (min iz/boşluk 0.1mm · high-speed 0.2mm + 3W ruhu · drill 0.2mm · annular 0.15mm · copper-to-edge 0.3mm · mask dam 0.075mm) + **IPC-2221** (akım kapasitesi) ve **IPC-2141** (empedans) hesap referansı; **DRC = fabrikasyon öncesi zorunlu kapı** (`k19:178`), tablo değişimi = yeni ADR. **(b) Sınıf** = **IPC-6012 Class 2** kart + **IPC-A-610 Class 2** işçilik (**tek sınıf** bütünlüğü, `k20:118` ile aynı) + **IPC-2611/2614** tedarik dokümanı; **Class 3 reddedildi** (§3/2 — ürün profili kesintisiz kritik değil), addendum'lar (IPC-6012FS/FA) gerekirse ayrı ADR. **(c) Dokümantasyon standardı (yeni)** = şema + BOM + layout **tek revizyon seti**, her değişiklikte `ChangeRec` mantığıyla gerekçe kaydı, üretim çıktısı **IPC-2581** (tercih) ya da **ODB++/Gerber + netlist + BOM CSV**; CAD = 0 dosya olduğu için **PLANNED** etiketli kurulur. **(d) AES17 fabrika testi** = ADR-038 M1-M6 (AES17-2020 + IEC 61606-1 + EIAJ CP-2404) **bağlayıcıdır**; ölçüm tarifi yeniden yazılmaz, yalnız `k20-bom/production-tools.md` test matrisine bağlanır (§2(e)). **(e) Nitelik/obsolescence** = **IPC-6012 §4** (qualification + C=0 örnekleme + periyodik conformans) + **IEC 62402/62239-1** ruhu → ADR-061'in 5 bağlayıcı kuralına bağlanır, **yeniden alınmaz**. **(f) Sınır** = ADR-061 (L6 + politika) · ADR-062 (pipeline) · ADR-064 (platform — dosya diskte YOK ⚠️) yeniden alınmaz. |
| Web Search **Sonuç** | **5/5 araştırmada karar destekleniyor:** k19 kural seti endüstri minimumlarıyla **uyumlu** (0.1/0.15/0.2mm ↔ IPC-2221/3W), **Class 2** ürün profiline ve k20'deki IPC-A-610 Class 2'ye uyar, **dokümantasyon standardı boşluğu doğrulandı** (2 içerik isabeti / 0 standart dosya), **AES17↔üretim bağı yok** (k19/k1/k20 = 0 isabet), nitelik/obsolescence standartları ADR-061 kural karşılıklarını verir. **İki gerilim açıkça kabul edildi:** (1) standart **tam metinleri ücretli/erişim kısıtlı** → bu ADR'de yalnız özet + atıf; "tam metinle doğrulandı" iddiası **yazılmaz** → §4.3 risk 5 + §5.1/9 tam-metin kapısı; (2) **CAD/kod = 0 dosya** → dokümantasyon standardı ve DRC/EMC kapıları **PLANNED/☐**; "sertifika alındı · DRC geçti · kart uygun" gibi kanıtsız hiçbir iddia yazılmaz. **⚠️ VERIFICATION REQUIRED:** `ADR-064-electronics-platform-architecture` (glob = 0 → düz metin, wiki-link yok) · IPC/IEC/AES tam metinleri · EMC sertifikası (`emc-compliance:369` ☐) · `.ai/architecture/index.md:96,114,115` K1/K19/K20 satırlarında **ADR-063 bağlantısı eksik** (§5.1/6 — bu işlemde rapor-only, index'e dokunulmadı) · `k19-pcb/index.md:161-162` K14/K20 etiket çelişkisi (§5.1/7 — rapor-only). |

### 1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| Frozen ADR'ler (001–037) | Değiştirilemez; bu ADR yalnız okur ve atıf yapar |
| ADR-061 sınırı | L6 katman tanımı, kart/modül hiyerarşisi ve bileşen **seçim politikası** (5 kural, IEC 62402) **zaten kararlandırıldı**; bu ADR bunları **yeniden almaz**, yalnız standart (IPC/IEC) karşılıklarını bağlar — `ADR-061:128` delegasyon satırı bu ADR ile kapanır |
| ADR-062 sınırı | DSP boru hattı (sıra, hard-RT, parametre yolu) kapsam dışı; bu ADR yalnız **fabrika ölçümü** (AES17) satırında kesişir |
| ADR-064 | Dosya diskte **YOK** → platform/L0-L6 mimarisi bu ADR'de **yazılmaz** (düz metin + `⚠️ VERIFICATION REQUIRED`) |
| ADR-038 / ADR-089 / ADR-090 sınırı | Çip/ürün/ölçüm kararları **alt kararlardır**; bu ADR AES17 tarifini **uygular ve bağlar, yeniden almaz** |
| CAD/kod kanıtı = 0 | `*.kicad_pcb/brd/sch` = 0 ve `*.cpp/h/hpp/c` = 0 → §2 maddeleri **PLANNED** etiketlidir; "sertifika alındı / kart uygun / DRC geçti" gibi ifadeler **kanıt gelmeden yazılmaz** |
| Standart tam metinleri | IPC/IEC/AES standartları ücretli/erişim kısıtlı → bu ADR'de özet + atıf; bayt düzeyinde alıntı yok (künye ⚠️ satırı) |
| Numara çakışması | Kural 7 "yeni ADR ≥ 088" ↔ arşiv 063 slotu — ADR-061 §5.1/9 ve ADR-062 künyesinden **aynı çakışma tekrar raporlanır, düzeltilmez** |

---

## 2. Karar (Decision)

CoreMusic donanım tasarım standartları **tek belgede (bu ADR)** bağlayıcı hale getirilir; içerik `k19-pcb`/`k20-bom`/`k1-donanim` dokümanlarında **yaşamaya devam eder** (SSOT — bu ADR onları tekrarlamaz, yalnız **öncelik ve bağlayıcılık** bağlar): **(a) PCB kural seti** = `k19-pcb/index.md:44-55` tablosu bağlayıcı minimum + IPC-2221/IPC-2141 hesap referansı; tablo değişimi = yeni ADR. **(b) SI/EMC** = tek sürekli GND + bölme/köprü disiplini (ADR-038 §2.2-b) + IEC 61000-4-2/4-3/4-4/4-5 immünite matrisi; **DRC kapısı fabrikasyon öncesi**, **EMC kapısı lansman öncesi** zorunlu kapatılır (üç ☐ kutusu bu ADR'nin açık kapılarıdır). **(c) Bileşen/kart kalitesi** = kart **IPC-6012 Class 2** (tedarik dokümanında açık: `IPC-2611/2614` bilgisi + Class 2), işçilik **IPC-A-610 Class 2** (`k20:118` ile aynı), kualifikasyon **IPC-6012 §4** (qualification + C=0 örnekleme + periyodik conformans), bileşen ömrü/sahtecilik **ADR-061 5 kuralı** + IEC 62402/62239-1 ruhu (ADR-061'de kararlıdır — tekrar yok). **(d) Dokümantasyon standardı (yeni)** = şema + BOM + layout **tek revizyon seti**; her değişiklikte revizyon kaydı (`FileRevision`/`ChangeRec` mantığı); üretim çıktısı **IPC-2581** (tercih) ya da ODB++/Gerber + netlist; `%100 electrical test ↔ schematic uygunluğu` (`k20:119`) bu standardın asgari maddesidir. **(e) AES17 fabrika testi** = ADR-038 M1-M6 (AES17-2020 + IEC 61606-1 + EIAJ CP-2404) **bağlayıcıdır**; `k20-bom/production-tools.md` test matrisine AES17 satırı bağlanır, `k19` DRC/EMC kapıları ile birlikte üretim kabul zinciri oluşturur. **(f) Sınır** = ADR-061 (L6 + politika), ADR-062 (pipeline), ADR-064 (platform, dosya yok ⚠️) **yeniden alınmaz**.

### 2.1 Neden Bu Seçenek?

Standartlar zaten vault'ta **var** (k19=12, k20=12, k1=23 dolu doküman); eksik olan **bağlayıcılık, sınıf seçimi, dokümantasyon disiplini ve üretim testi bağı** — yani sorun *içerik değil, karar*. Bu ADR sıfırdan standart yazmak yerine mevcut dokümanları **önceliklendirir ve bağlar**: (1) SSOT korunur (kural metni k19'da kalır, tekrarlanmaz), (2) ADR-061 `:128` delegasyonu kapanır, (3) Class 2 kararı k20'deki IPC-A-610 Class 2 ile **tek sınıf** bütünlüğü sağlar (Class 3 maliyeti reddedilir), (4) dokümantasyon standardı **CAD=0 iken** kurulur — ilk kural koyucu senaryodan ucuzdur, (5) AES17 tekrar yazılmaz, yalnız üretime bağlanır. Web araştırması beş maddede de kararı **destekledi** (§1.3 Sonuç); tek karşıt bulgu (standart tam metinlerinin erişim kısıtı) risk 5'e yazıldı.

### 2.2 Teknik Detaylar

**(a) PCB kural seti — bağlayıcı minimum (kaynak: `k19-pcb/index.md:44-55`):**

| Kural | Değer | Hesap/Standart referansı | Kapı |
|-------|-------|---------------------------|------|
| Min iz genişliği / boşluk | 0.1mm (4mil) | IPC-2221 (temel) | DRC ☐ → zorunlu (`k19:178`) |
| High-speed boşluk | 0.2mm (8mil) + 3W ruhu | controlled-impedance IPC-2141 (`:48`) | DRC + SI incelemesi |
| Güç izi | 0.3–1.0mm (akıma göre) | IPC-2221 akım kapasitesi (`power-distribution:275`) | DRC |
| Min drill / annular ring | 0.2mm / 0.15mm | IPC-6012 kategorisi (fabrication) | DRC |
| Copper-to-edge / mask dam | 0.3mm / 0.075mm | fabrikasyon (`pcb-fabrication:39-48` IPC-4101/TM-650 malzeme) | DRC |
| Toprak/EMC | tek sürekli GND + bölme; saat izi yarığı **kesmez** | ADR-038 §2.2-b (TI SLYT512/SPRACP4 ruhu) · `star-grounding.md` | EMC ☐ (`emc-compliance:369`) |

**(b) EMC immünite matrisi = test kapısı (kaynak: `k19-pcb/emc-compliance.md:23-26,296-299`):** ESD ±8kV temas / ±15kV hava (IEC 61000-4-2) · EFT ±1kV (61000-4-4) · Surge ±1kV satır-satır (61000-4-5) · radyasyonel 3V/m (61000-4-3) — prototipte **raporlanır**, lansman öncesi kapanır; sertifika **alınmamıştır** (☐), bu ADR yalnız kapıyı **zorunlu** kılar, bütçe/takvim §5.1/3'tedir.

**(c) Standart matrisi (alan → standart → vault kaynağı → durum):**

| Alan | Standart | Vault kaynağı | Durum |
|------|----------|---------------|-------|
| Kart performans sınıfı | **IPC-6012 Class 2** (+ IPC-6011 sınıfları, IPC-2611/2614 tedarik) | **yok** → bu ADR §2(c) | 🔵 YENİ (PLANNED) |
| İşçilik / kabul | IPC-A-610 **Class 2** (+ IPC-A-600 görsel) | `k20-bom/production-tools.md:118` · `k20-bom/index.md:78` | ✅ mevcut — sınıf **teyit edildi** |
| Malzeme/fabrikasyon | IPC-4101 / IPC-TM-650 / IPC-2221 / IPC-2141 | `k19-pcb/pcb-fabrication.md:39-48,114` · `power-distribution:275` · `controlled-impedance:48` | ✅ mevcut |
| Üretim testi | flying probe / ICT / %100 electrical test | `k19-pcb/pcb-fabrication.md:226-260,332+` · `k20-bom/production-tools.md:119` | ✅ mevcut (bağlantı §2(e)) |
| EMC | IEC 61000-4-2/4-3/4-4/4-5 | `k19-pcb/emc-compliance.md:23-26,296-299` | ✅ mevcut — **kapı ☐** |
| Fabrika ölçümü | AES17-2020 + IEC 61606-1 + EIAJ CP-2404 (M1-M6) | [[ADR-038-8-1-sound-card-chip-selection]] `:156-159,259` — `k19/k20`'de **0 isabet** | ⚠️ **bağlantısız → §2(e)** |
| Dokümantasyon | IPC-2581 (BOM+ECAD+revizyon) / ODB++ / Gerber+netlist | **yok** (2 içerik isabeti, 0 standart) | 🔵 YENİ (PLANNED) |
| Bileşen ömrü | IEC 62402 ruhu + PCN/LTB/counterfeit | [[ADR-061-electronics-architecture]] `:9,73` (5 kural — **bağlayıcı**) | ✅ mevcut (politiya), standart atfı bu ADR |

**(d) Dokümantasyon standardı (kuruluş — PLANNED):** her tasarım değişikliğinde (i) şema revizyonu, (ii) BOM revizyonu, (iii) layout/gerber revizyonu **aynı revizyon numarasında** güncellenir; değişiklik gerekçesi `ChangeRec` mantığıyla kaydedilir; üretime çıkışta tek format (IPC-2581, yoksa Gerber + netlist + BOM CSV) ve `%100 electrical test ↔ schematic uygunluğu` (`k20:119`) zorunludur. Sorumlu: Audio HW Engineer · şablon `k1-donanim/pcb-tasarim.md` + `k20-bom` altına yazılır (bu ADR metni **değil**).

**(e) AES17 bağlama:** `k20-bom/production-tools.md` test matrisine **"AES17-2020 + IEC 61606-1 + EIAJ CP-2404 (ADR-038 M1-M6)"** satırı eklenir; M1 THD+N ≥90dB, M3 kanal ayrımı ≥95dB, M4 FADGI matrisi (ADR-038 `:156-159`) — eşikler ADR-038'te marjlı yazılmıştır, **burada değişmez**; jitter bütçesi M6 ile kapatılır (ADR-038 `⚠️ VERIFICATION REQUIRED` satırı gerçek sayıyla değişir — o ADR'nin §5.1/6'sı).

**(f) Kapsam dışı (yeniden alınmaz):** L6 katman tanımı ve hiyerarşi (ADR-061) · DSP pipeline/RT bütçesi (ADR-062/ADR-017) · platform/L0-L6 + "5 cihaz/13 servis" iddiası (ADR-064, dosya yok ⚠️) · çip/ürün seçimi (ADR-038/089/090) · güç amplifikatör topolojisi (ADR-089) · uyumluluk sertifikasyon bütçesi ve takvimi (insan kararı, §5.1/3).

---

## 3. Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|-----------------|
| 1 | **Standartları k19/k20 README'lerine dağıtmaya devam et** (bu ADR'yi yazma) | Sıfır süreç maliyeti; içerik zaten var | Bağlayıcılık/sahiplik yok; 3 klasörde tutarsız revizyon riski; ADR-061 `:128` delegasyonu açık kalır | Sorun **içerik değil karar** — sahipliksiz doküman üretim hattında uygulanmaz (§1.2/1,5). Ret. |
| 2 | **IPC-6012 Class 3 + tam sertifika paketi** (uzay/otomotiv addendum'ları dahil) | En yüksek güvenilirlik; tedarikçi denetimi güçlü | Maliyet + test sertiliği artar; ürün profili kesintisiz kritik **değil** (pro-audio); k20'deki IPC-A-610 **Class 2** ile çelişir | Sınıf = ürün kararı; Class 2 endüstri standardı ve mevcut işçilik standardıyla **tek sınıf** (§1.3/2, §2(c)). Class 3 gerekirse **yeni ADR** ile. Ret. |
| 3 | **Dokümantasyon standardını ADR-064'e bırak** (platform ADR'si yazılınca) | Konu mantiksel olarak "genel platform"a benzer | ADR-064 **diskte yok** (glob=0) → belirsiz süresiz erteleme; revizyon boşluğu CAD açılıncaya kadar **açık** kalır | Kritik boşluk erken kapanmalı; ADR-064 yazılsa bile üretim standartları **donanım** konusudur, burada kalır (§2(f)). Ret. |
| 4 | **Standartları tam üyelik/sertifika programına bağla** (ISO 9001 + tam IPC erişimi) | Tam metin erişimi + resmi belge | Bedelli/lisans bağımlılığı; vault içi kural seti yine gerekli; süreç kararı bu ADR'nin değil | Erişim kısıtı §1.3/5 ve risk 5 ile zaten kabul edildi; **kapı olarak sonraki faz** (§5.1/9) — bu ADR'yi bloke etmez. Ret (şu an). |

---

## 4. Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- **Bağlayıcılık tek yerde:** k19/k20/k1 dokümanları dağınık kalır ama **öncelik ve minimum** bu ADR'de → iki agent aynı çizgide çalışır (min iz 0.1mm tartışması kapanır).
- **Üç kalite kapısı yazılı hale gelir:** DRC (fabrikasyon öncesi), EMC (lansman öncesi), AES17 (fabrika testi) — üretim kabul zinciri görünür ve denetlenebilir.
- **Sınıf bütünlüğü:** IPC-6012 Class 2 + IPC-A-610 Class 2 + "ölçüm/derating" delegasyonu tek sınıfta birleşir; tedarikçiye net şart yazılabilir (IPC-2611/2614).
- **Dokümantasyon standardı CAD'den önce kurulur:** ilk PCB dosyası revizyon disipliniyle açılır; "şema hazır, layout yok" durumu (`koruma-devreleri:184`) standartla izlenir hale gelir.
- **Delegasyonlar kapanır:** ADR-061 `:128` (tasarım standartları) ve `faz6-link-ledger.md:89` ADR-063 satırı bu işlemle dolmuş olur; ADR-062'nin "ADR-063/064 doğrulama" şartının **063 ayağı** kapanır.

### 4.2 Olumsuz Sonuçlar

- **Yeni bağlayıcılık = yavaşlama:** her kural değişimi artık ADR disiplini ister (küçük tuning'ler bile kayıtlı olur).
- **Standart tam metinleri elde yok:** IPC/IEC/AES özetleri + atıfla yetinildi; madde numaraları harici kaynak özetlerinden geldi → bayt düzeyinde doğrulama ⚠️ (risk 5).
- **Dokümantasyon standardı henüz uygulanmış değil:** bu ADR onu **kurar**, kendisi PLANNED; ilk CAD dosyasına kadar boşluk süreklidir.
- **EMC sertifika maliyeti/takvimi bilinmiyor:** bütçe onayı insan kararına bağlıdır (§5.1/3); "sertifika alındı" iddiası yazılmaz.
- **Tek ek belge:** vault'ta bir standart nodu daha var; indeks/brain güncellemeleri sonraki vault işlemine kalır (bu işlemde yalnız ADR + log append).

### 4.3 Riskler

| # | Risk | Olasılık | Etki | Mitigasyon |
|---|------|---------|------|-----------|
| R1 | **Standartsız PCB revizyon patlaması** — CAD=0 + dokümantasyon standardı yok; ilk şema/BOM/layout kendi başına revize edilir | 4 (çok olası) | 3 (yüksek) | §2(d) + §5.1/1: şema/BOM/tek-revizyon standardı ilk CAD'den önce yazılır; standart olmadan üretim çıktısı **verilmez** |
| R2 | **EMC sertifika reddi / lansman gecikmesi** — üç kapı ☐, hiçbiri kapanmış değil | 3 (olası) | 4 (yüksek) | §2(b) + §5.1/3: immünite matrisi prototipte ölçülür; sertifika bütçe/takvimi insan kararıyla planlanır; "alındı" kanıtsız yazılmaz |
| R3 | **BOM sürüklenmesi (NRND/EOL/sahtecilik)** — lifecycle yalnız politika kuralında, BOM'da sütun yok | 3 (olası) | 3 (orta) | ADR-061 5 kuralı (IEC 62402) + §5.1/5: k20 BOM'a lifecycle/kaynak sütunu (PLANNED); NRND/EOL yeni tasarıma alınmaz |
| R4 | **Test kapsamı boşluğu** — AES17 ADR-038'te, üretim testinde 0 isabet | 3 (olası) | 4 (yüksek) | §2(e) + §5.1/4: `production-tools.md` test matrisine AES17 M1-M6 satırı bağlanır; ADR-090 "bir kez kur" iddiası burada kapanır |
| R5 | **Standart iddiasının kanıtsızlığı** — tam metin erişimi yok; "uygun/sertifikalı" yazma hataları | 2 (mümkün) | 3 (orta) | Künye ⚠️ satırı + §1.3/5 karşıt bulgu; yalnız özet+atıf; madde no. harici kaynaklara dayanır → tam metin doğrulaması §5.1/9 kapısı |

### 4.4 Cross-Reference

| İlişki | Hedef | Durum |
|--------|-------|-------|
| Delegasyon kapanışı | [[ADR-061-electronics-architecture]] `:128` → bu ADR | ✅ kapandı (metin yazıldı) |
| Ölçüm protokolü erteleme | ADR-061 `:193` fallback "ölçüm protokolü ADR-063'e ertelenir" | ✅ §2(e) ile bağlandı |
| Pipeline ölçüm kesişimi | [[ADR-062-dsp-pipeline-architecture]] şart 3 (`ADR-063* glob ≥1`) | ✅ 063 ayağı kapandı · 064 açık (⚠️) |
| Fabrika ölçümü | [[ADR-038-8-1-sound-card-chip-selection]] `:156-159,259` | ✅ uygulandı/bağlandı (tekrar yok) |
| Platform mimarisi | `ADR-064-electronics-platform-architecture` | ⚠️ dosya yok — düz metin, wiki-link yok |

---

## 5. Uygulama (Implementation)

### 5.1 Adımlar

| # | Adım | Sorumlu | Süre | Durum |
|---|------|---------|------|-------|
| 1 | Şema/BOM/tek-revizyon dokümantasyon standardı yazımı (§2(d)) — `k1-donanim/pcb-tasarim.md` + `k20-bom/` şablonu | Audio HW Engineer | 1 gün | ⏳ PLANNED |
| 2 | DRC kapısını `k19-pcb` sürecine bağla (fabrikasyon öncesi DRC=0 ihlal) + `index.md:178` kutusu kapatma akışı | Audio HW Engineer | 0.5 gün | ⏳ PLANNED |
| 3 | EMC immünite matrisi (IEC 61000-4-2/3/4/5) test planına dönüşür + sertifika bütçe/takvimi insan onayı | QA + Audio HW | 1 gün (ölçüm ayrı) | ⏳ PLANNED |
| 4 | AES17 M1-M6 ↔ `k20-bom/production-tools.md` test matrisi bağlama (§2(e)) | QA + Audio HW | 1 gün | ⏳ PLANNED |
| 5 | k20 BOM'a lifecycle/kaynak sütunu (IEC 62402 ruhu — ADR-061 5 kuralı) | Data + Audio HW | 1 gün | ⏳ PLANNED |
| 6 | `.ai/architecture/index.md` K1(`:96`)/K19(`:114`)/K20(`:115`) satırlarına ADR-063 bağlantısı | MO (vault-updater) | 0.5 gün | ⏳ **bu işlemde dokunulmadı** (raporlandı) |
| 7 | `k19-pcb/index.md:161-162` "K14 Test / K20 Mekanik" ↔ `architecture/index.md:109,115` etiket çelişkisi düzeltmesi | MO (vault-updater) | 0.5 gün | ⏳ **bu işlemde dokunulmadı** (raporlandı) |
| 8 | Debate 3 tur / 20 persona + Tech Lead onayı | MO + persona | 1 gün | ✅ **TAMAMLANDI** (18/2/0 KABUL — §7.2 · şartlar §5.3) |
| 9 | IPC/IEC/AES **tam metin** doğrulaması + üyelik/sertifika programı kararı (§3/4 kapısı) | Vault Steward + insan | sonraki faz | ⏳ PLANNED (erişim kısıtı) |
| 10 | `index.md:94` `[[../brain.md]]` → gerçek ADR hedefine düzeltme | MO (vault-updater) | sonraki vault reset | ⏳ **ertelendi** (bu işlemde rapor-only) |

### 5.2 Geri Dönüş Planı

Debate **RED** çıkarsa: dosya `.ai/.decisions/rejected/` taşınır (dosya adı **değiştirilmez** — In-Place Refactoring), `[[../index.md]]` satır 94 **silinmez** (değişmez kalır), `log.md`'ye `ADR-063 RED (debate …)` append edilir; `k19/k20` bağlayıcılık iddiası kalkar ve dokümanlar eski "öneri" statüsüne döner (içerik değişmez — SSOT'da bozulma olmaz). Karar **kabul edilip değiştirilirse** yeni ADR açılır (≥088 serisi; numara çakışması notuyla), bu metin `superseded by` ile bağlanır; **Frozen olduktan sonra hiçbir düzenleme yapılmaz** (§4 kural 10). Uygulama adımları (1-5) geri alındığında: yazılan standart şablonları `k1-donanim`/`k20-bom` altından kaldırılır, `k19` checklist kutuları ☐'a döner (zaten öyleler) — hiçbir mevcut vault dosyasının içeriği **silinmez**.

### 5.3 Debate Şartları (bağlayıcı — 3/3)

> Debate sonucu **KABUL** (18 kabul / 2 çekimser / 0 red) bu **üç şartla** verilmiştir; şartlar §5.1 adımlarıyla kenetlidir ve **kapanmadan bu ADR Frozen'a geçmez** (Arch Lead ⏳ — §7.2).

| # | Şart | Kapsam | İlgili adım | Durum |
|---|------|--------|-------------|-------|
| 1 | **Uygulama fazı: doküman şablonu + DRC kapısı + V.R. kapısı** | **1a)** tek-revizyon doküman şablonu (`k1-donanim/pcb-tasarim.md` + `k20-bom/`) + **DRC gate** (fabrikasyon öncesi, `k19-pcb/index.md:178`) — doküman standardı 0 + CAD 0 koşuluyla; **1b)** ücretli IPC/IEC/AES tam metinleri için **erişilebilir özet + `⚠️ VERIFICATION REQUIRED` doğrulama kapısı** ("tam metinle doğrulandı" ancak erişim kanıtıyla yazılır — §4.3/R5, §5.1/9) | §5.1/1 · §5.1/2 · §5.1/9 | ⏳ PLANNED |
| 2 | **AES17'nin ADR-038'e bağlanması** | `k20-bom/production-tools.md` test matrisine ADR-038 M1-M6 (AES17-2020 + IEC 61606-1 + EIAJ CP-2404) satırı eklenir → **üretim hattına ölçüm eklenir**; ölçüm tarifi yeniden yazılmaz (§2(e)) | §5.1/4 | ⏳ PLANNED |
| 3 | **Çapraz düzeltmeler** | `k19-pcb/index.md:161-162` K14/K20 etiket çelişkisi + `.ai/architecture/index.md:96,114,115` K1/K19/K20 → ADR-063 bağlantı eksikliği + `brain.md` kırık hedefi — üçü de **vault reset'e** ertelendi (bu işlemde rapor-only) | §5.1/6 · §5.1/7 · §5.1/10 | ⏳ ERTELENDİ (reset) |

---

## 6. İlgili Dokümanlar

### 6.1 Kaynak Kanıtlar

| Dosya | Satır | Ne | Etiket |
|-------|-------|----|--------|
| `.ai/.decisions/index.md` | `:94` | slug `ADR-063-hardware-design-standards` | ✅ hizalı |
| `.ai/architecture/k19-pcb/index.md` | `:44-55` · `:161-162` · `:178,180` | kural seti · K14/K20 etiket çelişkisi · DRC/EMC ☐ | ✅ / ⚠️ çelişki → §5.1/7 |
| `.ai/architecture/k19-pcb/emc-compliance.md` | `:23-26,296-299` · `:369` | IEC 61000-4-2/3/4/5 matrisi · sertifika ☐ | ✅ / ⚠️ kapı açık |
| `.ai/architecture/k19-pcb/pcb-fabrication.md` | `:39-48` · `:114` · `:226-260,332+` | IPC-4101/TM-650 · IPC-2221 · flying probe/ICT/Gerber | ✅ IMPLEMENTED (doküman) |
| `.ai/architecture/k19-pcb/controlled-impedance.md` · `power-distribution.md` | `:48` · `:275` | IPC-2141 · IPC-2221 | ✅ IMPLEMENTED (doküman) |
| `.ai/architecture/k20-bom/production-tools.md` | `:118,119` | IPC-A-610 Class 2 · schematic'e uygunluk %100 | ✅ / 🔵 standart boşluğu §2(d) |
| `.ai/architecture/k1-donanim/pcb-tasarim.md` · `koruma-devreleri.md` | dosya · `:184` | PCB tasarım kuralları · "Şematik hazır, layout yok" | ✅ / ⚠️ |
| `.ai/.decisions/accepted/ADR-061-electronics-architecture.md` | `:9,73,128,193` | 5 bileşen kuralı · delegasyon · ölçüm erteleme | ✅ dosya var |
| `.ai/.decisions/accepted/ADR-038-8-1-sound-card-chip-selection.md` | `:88-98,156-159,236,259` | AES17/IEC 61606/EIAJ M1-M6 + şart 2 | ✅ dosya var |
| `.ai/architecture/index.md` | `:96,114,115` · `:109` | K1/K19/K20 → yalnız ADR-038, ADR-089 · K14=Ağ | ⚠️ ADR-063 bağlantısı yok → §5.1/6 |
| `.ai/reports/faz6-link-ledger.md` | `:89` | ADR-061..064 kırık hedef listesi | ✅ 063 bu işlemle kapandı · 064 açık |
| `git ls-files` | — | `*.kicad_pcb\|brd\|sch` = 0 · `*.cpp\|h\|hpp\|c` = 0 | ⏳ PLANNED |

### 6.2 Bağlantılar

- Şablon: [[../../.templates/adr/adr-template.md]] (Guardrail #16) — format referansı: [[ADR-062-dsp-pipeline-architecture]] · sınır kaynağı: [[ADR-061-electronics-architecture]]
- İlgili ADR'ler: [[ADR-038-8-1-sound-card-chip-selection]] · [[ADR-017-dsp-hardware-mode]] · [[ADR-089-classab-24v]] · [[ADR-090-channel-variant-product-family]] · [[ADR-005-ultrathink-protocol]]
- Vault kökü: [[../../index.md]] · [[../../brain.md]] · [[../../keys.md]] · [[../../MEMORY.md]] · [[../../log.md]] · [[../../AGENTS.md]] · [[../index.md]]
- Spec dokümanları: [[../../architecture/index]] · [[../../architecture/k19-pcb/index]] · [[../../architecture/k19-pcb/emc-compliance]] · [[../../architecture/k19-pcb/pcb-fabrication]] · [[../../architecture/k19-pcb/controlled-impedance]] · [[../../architecture/k19-pcb/power-distribution]] · [[../../architecture/k19-pcb/signal-integrity]] · [[../../architecture/k20-bom/index]] · [[../../architecture/k20-bom/production-tools]] · [[../../architecture/k1-donanim/pcb-tasarim]]
- Dizin kayıtları (düz metin): `architecture/k19-pcb/` (12 .md) · `architecture/k20-bom/` (12 .md) · `architecture/k1-donanim/` (23 .md) — **dizin hedefidir, .md dosyası değildir → wiki-link değil**
- Diskte **olmayan** (düz metin + ⚠️): `ADR-064-electronics-platform-architecture` · `electronic/` · PCB CAD dosyaları (`*.kicad_pcb/brd/sch`) · IPC/IEC/AES tam metinleri (ücretli/erişim kısıtlı)

### 6.3 Debate Kaydı

| Kayıt | Değer |
|-------|-------|
| Debate | 3 tur / 20 persona → §7.2 (bu dosya) — **18 kabul / 2 çekimser / 0 red → KABUL** |
| Bağlayıcı şartlar | 3/3 → §5.3 (doküman şablonu + DRC + V.R. kapısı · AES17 ADR-038 · çapraz düzeltmeler) |
| Tech Lead | ✅ (2026-09-30) — Arch Lead ⏳ |
| Audit | `.ai/log.md` append (append-only): `ADR-063 debate 3/20 kaydedildi (18/2/0 KABUL) + Tech Lead ✅ + 3 şart` |

---

## 7. Onay

### 7.1 Onay Akışı

| Rol | Kişi | Tarih | Durum |
|-----|------|-------|-------|
| Vault Steward | CoreMusic Vault Steward | 2026-09-30 | ✅ |
| Tech Lead | CoreMusic Tech Lead | 2026-09-30 | ✅ |
| Arch Lead | — | — | ⏳ |

### 7.2 Debate

| Alan | Değer |
|------|-------|
| Debate | ✅ **TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)** |
| Tur sayısı | **3 / 3** |
| Oy | **18 kabul / 2 çekimser / 0 red → KABUL** |
| Tech Lead | ✅ |
| Arch Lead | ⏳ |

**Tur 1 — 20 persona · bulgu turu (17 kabul/neutral · 3 uyarı):**

- **Bulgu:** dokümantasyon standardı **YOK** (grep yalnız 2 içerik isabeti: `k20-bom/production-tools.md:119` · `k1-donanim/koruma-devreleri.md:184` — **0 standart dosyası**); PCB CAD dosyası **0** (`*.kicad_pcb|brd|sch`) + kod **0**; AES17 yalnız [[ADR-038-8-1-sound-card-chip-selection]] (`:156-159,259`) ve ADR-090'da, `k19|k1|k20` = **0 isabet**; `ADR-064` diskte **YOK** → düz metin + ⚠️ VERIFICATION REQUIRED.
- **Karar:** `k19-pcb/index.md:44-55` tablosu **bağlayıcı** + DRC kapısı; **IPC-6012 Class 2 tek sınıf** (işçilik IPC-A-610 Class 2 ile aynı — PTH 25µm şartı **⚠️ VERIFICATION REQUIRED**: §1.3/2'ye göre C2 ≥20µm ↔ C3 ≥25µm karşıtlığı tedarik dokümanında teyit edilir); tek-revizyon doküman standardı **PLANNED**; AES17 ADR-038'e bağlanır; nitelik ADR-061'e bağlanır.
- **Kanıt:** 5 sorgu / **44 benzersiz kaynak** (IPC-2221 · IPC-6012E/F · IPC-2581/ODB++ · AES17-2020/IEC 61606 · EVT-DVT-PVT · AEC-Q/IEC 62402); `.ai/.decisions/index.md:94` slug hizalı; **karşıt bulgu:** standart **tam metinleri ücretli**.
- **3 uyarı:** Critic — doküman/CAD **0** şart · Security — ücretli standartlar **V.R.** · Data — `k19:161-162` etiket çelişkisi. **Ertelenenler (reset'e):** `.ai/architecture/index.md:96,114,115` eksik bağlantı · `k19-pcb/index.md:161-162` etiket · `brain.md` kırık hedef.

**Tur 2 — İtiraz → çözüm (4):**

| # | İtiraz | Çözüm | Şart |
|---|--------|-------|------|
| 1 | Doküman standardı 0 + CAD 0 | uygulama fazı: tek-revizyon şablonu + DRC gate | **şart 1a** |
| 2 | Ücretli IPC tam metinleri | V.R. + erişilebilir özet doğrulama kapısı | **şart 1b** |
| 3 | AES17 yalnız ADR-038'de | ADR-038 şartına bağlanır (üretim hattına ölçüm eklenir) | **şart 2** |
| 4 | `k19:161-162` etiket çelişkisi + `architecture/index.md` eksik bağlantılar | çapraz düzeltme (reset'e) | **şart 3** |

**Tur 3 — Oy:** **18 kabul / 2 çekimser / 0 red → KABUL** — 3 şart §5.3'e bağlandı.

> **Debate notu (2026-09-30):** 3 şart §5.3'te bağlayıcıdır; **Arch Lead ⏳** olduğundan **Frozen'a geçiş YOK**tur. Debate RED çıksaydı §5.2 uygulanırdı.

> **Not:** `status: accepted` bu ADR'nin **kapsam kararının** (§2 a-f) vault tarafından yazıldığını gösterir; debate ✅ **TAMAMLANDI (18/2/0 KABUL, §7.2)** ve Tech Lead ✅ olduğundan **Frozen'a geçiş Arch Lead ⏳'ye bağlıdır**. ADR-001–037 frozen kapsamı dışındadır. Debate RED çıkarsa §5.2/1 uygulanır (dosya `rejected` olur, indeks satırı silinmez). Debate §5.1/8'de başlatılır; sonuç `log.md`'ye append edilir.

---

*1.0.0 | 2026-09-30 | Created — ADR-063 Hardware Design Standards (debate 18/2/0 KABUL + 3 şart)*
*Authority: CoreMusic Vault Steward · Mode: Red Team · Human Mode · Truth Mode*
