---
title: "CoreMusic — ADR-089: Class AB Amplifikatör Sistemi (8×50W, 120dB+, 12–24V Boost, Hibrit MCU)"
type: "architecture-decision"
category: "electronics"
date: "2026-09-24"
updated: "2026-09-24"
version: "1.0.0"
status: "accepted"
authority: "SSOT — Class AB güç amplifikatörü mimarisi; K16/K17/K18/K19/K20 katmanları bu karara bağlanır"
kaynak: "3 turlu agent tartışması 20 persona"
governance: "Red Team · Human Mode · Truth Mode"
---

# CoreMusic — ADR-089: Class AB Amplifikatör Sistemi (8×50W, 120dB+, 12–24V Boost, Hibrit MCU)

> **Durum:** ✅ **ACCEPTED** · **Tarih:** 2026-09-24 · **Önceki durum:** Draft → accepted terfi (2026-09-24)
> **Karar serisi:** `.ai/.decisions/accepted/` · **Slug:** `ADR-089-classab-24v`
> **İlgili kararlar:** [[ADR-081-multi-provider-data-sync]] (Nygard kalıp) · [[../../brain.md]] · [[../../CLAUDE.md]] · [[../index.md]]
> **Numara gerekçesi:** ADR-088 (Gender-Based Social OAuth) sonrası ilk boş numaradır; frozen **001–037'ye dokunulmadı** ve **ADR-090 açılmadı**.
> **Slug notu:** `ADR-` büyük harftir. `.ai/.templates/adr/adr-index.md` §2'deki "slug küçük harf" kuralı bu seride uygulanmamıştır; **disk konvansiyonu esastır** (ADR-001…088 ile aynı yazım).

---

## §1 Bağlam

### §1.1 Mevcut Durum

CoreMusic güç amplifikatörü katmanı (K16) tasarımdır ve vault içinde **tek bir karar dosyası ile** yönetilmektedir. Vault'un mevcut, çapraz doğrulanmış kanıtları:

| Kaynak | Kanıt |
|---|---|
| [[../../CLAUDE.md]] L116 | **K16** Class AB Amplifikatör — MJL21194/93 Darlington, 50W/kanal, 8 kanal modüler, THD <0.005% |
| [[../../CLAUDE.md]] L117 | **K17** Güç Kaynağı ±35V — LM5122 ×2 (boost + inverting), 6S LiPo (22.2V), OR-ing, %96 verim, UVP/OVP/OCP/OTP |
| [[../../CLAUDE.md]] L118 | **K18** Termal — Fischer SK53-100-SA, 80mm PWM fan, KSD301 thermal cutoff, 41W/kanal ısı yönetimi |
| [[../../CLAUDE.md]] L119 | **K19** PCB — 6-layer stackup, impedance matched, thermal vias, star ground, ENIG, 2oz copper |
| [[../../CLAUDE.md]] L120 | **K20** BOM & Üretim — 1.775 BOM satırı, Mouser/Digikey, ~$682 sistem maliyeti |
| [[../../CLAUDE.md]] L113 | **K1** Donanım — XMOS XU316, PCM3168A, AK4458, 8×Class AB, 14 konnektör, Boost; *DC-Only + Class AB, sinyal zincirine parazit yasak* |
| [[../../CLAUDE.md]] L126–L127 | **H1** Class AB 50W/kanal × 8 kanal (7.1), MJL21194/93, Fischer SK53 72°C, C++20 DSP + **STM32/RP2040 MCU** · **H2** ±40V Push-Pull (SG3525/LM5122), **12V–24V DC girişi**, %92–96 verim, EMI filtresi |
| [[../../PROJECTS.md]] L305–L311 | SNR >105dB · **Kanal 1-8 (modüler, her kanal bağımsız PCB)** · Fischer SK53-100-SA (300×75×49mm) · Noctua NF-A8 PWM (80mm) · KSD301 + DC offset koruma rölesi · 6-layer 200×100mm 2oz IPC Class 3 · *Durum: TASARIM AŞAMASINDA* |
| [[../../architecture/k16-class-ab/CLAUDE.md]] | Darlington çift, MJL21194/MJL21193, 50W@8Ω, THD+N <0.005%, SNR >100dB, Fischer SK53-100-SA, KSD301 72°C, BOM <$430 |
| [[../../brain.md]] L41 / L56 | 8.1 Surround — Class AB 8×50W, XMOS XU316, PCM3168A · **PCM5122 REDDEDİLDİ** |
| `README.md` L73 / L171 | 8x50W modüler discrete Class AB + ±35V · **±35V LM5122 Dual Boost, 6S LiPo (22.2V) veya 19–24V DC adaptör, %96 tepe verim, sıfır 50Hz şebeke gürültüsü** |

Bu ADR, taslak halindeki `ADR-089` kararını **kabul edilmiş (accepted)** tek otoriteye dönüştürür ve spec'i kesinleştirir.

### §1.2 Sorun

Spec beş eksende karar gerektiriyordu ve taslak bu beşi birden kapsamıyordu:

1. **Topoloji** — Class AB mi, Class D (TPA3255) mi, Class DC mi?
2. **Güç** — 12–24V gibi düşük DC girişten simetrik yüksek barın üretilmesi (boost şartı).
3. **Kanal** — 1/2/4/6/8 kanal varyantlarının tek tasarımdan karşılanması.
4. **Gürültü/dinamik** — **120dB+** hedefinin ne anlama geldiği ve vault ölçülebilir referanslarıyla ilişkisi.
5. **Kontrol** — MCU'nun tek başına mı yoksa hibrit bir zincirin parçası mı olacağı.

### §1.3 Kısıtlar

- **Yasaklar üstünlük sağlar:** frozen ADR-001–037 **immutable**; `PCM5122` yasaklı desen ([[../../brain.md]] L56); AGPL/uyumsuz lisanslı donanım dosyası (EAGLE/KiCad source) kopyalanmaz.
- **Serbestlikler:** tek kanal bağımsız olmalı, enable pinli ([[../../CLAUDE.md]] L116); sinyal zincirine parazit yasak ([[../../CLAUDE.md]] L113); DC-only mimari (şebekesiz) ([[../../CLAUDE.md]] L18).
- **Bütçe/üretim:** K20 BOM ~$682 sistem maliyeti, k16 sınıfı BOM <$430 ([[../../CLAUDE.md]] L120, [[../../architecture/k16-class-ab/CLAUDE.md]]).
- **Araç zinciri:** KiCad 9.0 (EDA), Verilog (HDL) ([[../../CLAUDE.md]] L219).

### §1.4 Araştırma (19 exa araması)

19 exa araştırmasının **3'ü güç kaynağı ekseniyle** doğrudan bu kararı besledi (`.ai/ecosystem/donanım-devre-referanslari.md` §3.11'e işlenmiştir):

| Kaynak | Bulgu | Karara etkisi |
|---|---|---|
| hifisonix **Ripple Eater** | ±20–63V, 5A/20A; **40dB @20Hz–300kHz**, **50dB @200Hz–20kHz** | Boost çıkış filtrelemesi için olgun referans |
| prydin **2SK1058/2SJ162** (lateral MOSFET) | ±30V Class AB çalışma | Alternatif çıkış devresi (bakınız §3.2) |
| **mini12** (TDA2030) | 12V besleme ile çalışan mini amplifikatör | 12V uç değerin uygulanabilirliğini gösterir |

---

## §2 Karar

**Class AB topolojisi, 8×50W modüler discrete çıkış, 12–24V DC girişli boost tabanlı ±35V güç kaynağı, 1/2/4/6/8 kanal varyantı ve hibrit MCU kontrolü ile kabul edilir.**

| # | Spec kalemi | Kesin değer | Vault bağlantısı |
|---|---|---|---|
| 1 | **Topoloji** | Class AB (çıkış), Darlington MJL21194/MJL21193 | K16 · [[../../architecture/k16-class-ab/CLAUDE.md]] |
| 2 | **Güç** | **8 × 50W @ 8Ω** (aralık 25–75W) | K16, README L73, [[../../brain.md]] L41 |
| 3 | **Gürültü/dinamik** | **120dB+ hedef** *(ölçüm tanımı §4.3)* | vault ölçülebilir referans: SNR >105dB (PROJECTS L305), SNR >100dB / THD+N <0.005% (brain L284, k16) |
| 4 | **Kanal** | **1 / 2 / 4 / 6 / 8** — modüler, her kanal bağımsız PCB | PROJECTS L306 "Kanal 1-8 (modüler)" |
| 5 | **Güç kaynağı** | **12–24V DC giriş → boost → ±35V** (LM5122 interleaved dual, %96) | CLAUDE L18/L127, README L171, K17 |
| 6 | **Kontrol** | **Hibrit MCU** (aşağıda §2.1) | CLAUDE L126 (STM32/RP2040), L193 (STM32H7/RP2040), K0 RPi5, XMOS XU316 |

### §2.1 Hibrit MCU Tanımı

Hibrit MCU, tek bir denetleyiciye indirgenmeyen **üç katmanlı** bir kontrol zinciridir:

| Katman | Rol | Vault kanıtı | Durum |
|---|---|---|---|
| **XMOS XU316** | USB ses + DSP köprüsü, I2S anahtarı | CLAUDE L193, brain L41/L56, ADR-038 | ✅ vault'ta var |
| **STM32H7 / RP2040** | Gerçek zamanlı MCU: koruma, telemetri, fan/termal, enable pinleri | CLAUDE L126 (STM32/RP2040), L193 (STM32H7/RP2040), L136 (K16 → *"C++20, STM32/RP2040 MCU telemetri"*) | ✅ vault'ta var |
| **RPi5 (host/AP)** | Üst katman: işletim sistemi, ağ, arayüz | brain L220 (K0: RPi5), L343/L533 (ARM64 Debian, embedded) | ✅ vault'ta var |

> ⚠️ **VERIFICATION REQUIRED:** Debate sırasında **PIC32MZ** adayı da gündeme gelmiştir; **bu isim vault'ta hiçbir yerde geçmemektedir** (`.ai/` recursive taraması: 0 eşleşme). Bu ADR, PIC32MZ'yi **seçilmiş parça olarak kaydetmez** — STM32H7/RP2040 + XMOS XU316 + RPi5 zinciri vault kanıtıyla sabittir. PIC32MZ tercih edilecekse ayrı bir ADR gerekir (ADR-090 açılmamıştır).

**MCU ↔ modül haberleşmesi zorunludur:** her kanalın enable/telemetri/koruma hatları MCU'ya bağlanır; kanallar birbirinden bağımsızdır ([[../../CLAUDE.md]] L116).

---

## §3 Alternatifler

### §3.1 Alternatifler Tablosu

| # | Alternatif | Sonuç | Bir cümle gerekçe |
|---|---|---|---|
| A1 | **Class D (TPA3255)** | ❌ **REDDEDİLDİ** | Ana ses zincirinde ana topoloji olarak **Class D yoktur**; TPA3255 yalnızca başka depolarda "güç amplifikatörü" etiketiyle geçer — bu karar onu kullanmaz. |
| A2 | **Class DC** | ❌ **REDDEDİLDİ** | Spec Class AB'dir; Class DC ayrı bir topoloji olup bu mimariye dahil edilmemiştir. |
| A3 | **PCM5122** | 🚫 **YASAKLI** | ADR-038 / brain L56 / CLAUDE §21 — hiçbir yerde referans edilemez, eşleştirilemez, alternatif önerilemez. |
| A4 | **Tek PCB üzerinde monolitik 8-kanal** | ❌ **REDDEDİLDİ** | PROJECTS L306 "her kanal bağımsız PCB" ile çelişir; modülerlik ve servis edilebilirlik kaybolur. |
| A5 | **Boost'suz doğrudan besleme** | ❌ **REDDEDİLDİ** | 12–24V giriş şartı, ±35V bar için boost'yu zorunlu kılar (K17, H2). |
| A6 | **Tek katman MCU (sadece MCU)** | ❌ **REDDEDİLDİ** | USB ses DSP, gerçek zamanlı koruma ve üst katman işlevleri aynı çipte toplanamaz; hibrit mimari şart. |
| A7 | **Şebeke gücü (AC/DC adaptör yerine)** | ❌ **REDDEDİLDİ** | README L171 *sıfır 50Hz şebeke gürültüsü* ve DC-ONLY mimarisi (CLAUDE L18) bunu reddeder. |
| A8 | **Lateral MOSFET çıkış (2SK1058/2SJ162)** | ⏸ **KAPSAM DIŞI** | Exa bulgusu (prydin, ±30V) bir alternatif değil, gelecekteki bir varyant adayıdır; bu ADR kapsamı dışındadır. |

### §3.2 Reddedilen Alternatiflerin Ayrıntısı

- **A1 — Class D / TPA3255:** Ana topoloji Class AB olduğu için, Class D'ye geçiş bu kararla **açıkça reddedilmiştir**. Ekosistem dokümanındaki `§3.1` satırındaki *"K16 güç amplifikatöründe bu topoloji referans senaryodur"* ifadesiyle **çelişir ve bu ADR ile geçersizdir** (bkz. `.ai/ecosystem/donanım-devre-referanslari.md` §3.1 — oradaki ifade DÜZELTME bloğuyla *"yalnızca topoloji dersi, entegre edilmez"* hâline getirilmiştir).
- **A2 — Class DC:** Spec, tartışmada Class AB olarak sabitlenmiştir; Class DC'nin vault'ta karşılığı yoktur.
- **A4 — Monolitik 8-kanal:** Isıl yönetim (41W/kanal → K18) ve arıza izolasyonu kanal başına ayrı PCB ile sağlanır; tek PCB'de tek bir kanal arızası tüm sistemi indirir.
- **A7 — AC besleme:** 6S LiPo (22.2V) / 19–24V DC adaptör girişi, 50Hz köprü gürültüsünü kaynağında ortadan kaldırır.

### §3.3 Ekler

#### §3.3.1 REDDEDİLDİ Alternatifler (özet)

`Class D/TPA3255` · `Class DC` · `PCM5122 (yasak)` · `Monolitik 8-kanal PCB` · `Boost'suz besleme` · `Tek katman MCU` · `AC/şebeke besleme`

#### §3.3.2 İlgili Kararlar

| Karar | Bağ |
|---|---|
| [[ADR-081-multi-provider-data-sync]] | Nygard şablon kalıbı ve §7.1 debate kaydı formatı |
| [[../../brain.md]] ADR-089-classab-24v | Kararın brain indeks karşılığı |
| [[../../CLAUDE.md]] §K16–K20 | Katman tanımı bu karara bağlanır |
| [[../../architecture/k16-class-ab/CLAUDE.md]] | Devre seviyesi kısıtlar (Darlington, termal, BOM) |
| [[../index.md]] §4 | Karar indeksi — ADR-089 artık **Active/accepted** satırıdır |

#### §3.3.3 Kabul Kriteri

1. Her kanal bağımsız PCB olarak tasarlanır ve tek başına test edilebilir (enable pinli).
2. Çıkış: 8 × 50W @ 8Ω, THD+N **< 0.005%**.
3. Gürültü: **120dB+ hedefi** §4.3'teki ölçüm tanımına göre doğrulanır; arada kalırsa vault referansı (SNR >105dB) min kabul eşiği olarak uygulanır.
4. Güç: 12–24V DC girişten ±35V boost, UVP/OVP/OCP/OTP korumaları etkin.
5. Termal: 41W/kanal → KSD301 72°C kesme + 80mm PWM fan ile sürekli çalışma güvenli.
6. Hibrit MCU zinciri (XMOS + STM32/RP2040 + RPi5) haberleşme sözleşmesi yazılı olarak sabitlenir.

#### §3.3.4 Yasaklar

- ❌ **PCM5122** kullanılamaz, referans edilemez, alternatif önerilemez.
- ❌ **Class D / TPA3255** ana topoloji olarak kullanılamaz.
- ❌ **AGPL/uyumsuz lisanslı** donanım kaynağı (EAGLE/KiCad) kopyalanamaz — yalnız şema fikri referans alınır.
- ❌ **Frozen ADR-001–037** metinlerine dokunulamaz; **ADR-090 ve ADR-027 açılmaz**.
- ❌ Sinyal zincirine **parazit** sokan topraklama/güç düzeni uygulanamaz (star ground şart, K19).
- ⚠️ **PIC32MZ** bu ADR ile onaylanmamıştır; ayrı ADR gerektirir.

> Debate özeti için bkz. **§7.1**.

---

## §4 Sonuçlar

### §4.1 Olumlu

- **Modülerlik:** 1/2/4/6/8 kanal varyantları tek tasarım ailesinden çıkar; tasarım tekrarı yok.
- **DC-only avantaj:** 50Hz şebeke gürültüsü kaynağından elenir; %96 boost verimi ile 6S LiPo taşınabilir kullanım.
- **Servis edilebilirlik:** Kanal başına ayrı PCB, tek kanal arızasının sistem arızasına dönüşmesini engeller.
- **Termal öngörülebilirlik:** Fischer SK53-100-SA + 80mm PWM + KSD301 ile 41W/kanal ısı yönetimi kanıtlanmış parçalarla tanımlı.
- **Bütçe:** K20 ~$682 sistem / k16 sınıfı <$430 amplifikatör BOM'u ölçülebilir.

### §4.2 Olumsuz / Riskler

| Risk | Etki | Azaltım |
|---|---|---|
| **120dB+ hedefi vault ölçülebilir referansıyla (105dB) örtüşmüyor** | Kabul kriteri belirsiz kalabilir | §4.3'te ölçüm tanımı zorunlu kılındı; tanım yapılmadan üretim kararı verilemez |
| Boost gürültüsü ±35V barına sızabilir | SNR düşer | Ripple Eater tarzı aktif filtreleme referansı (§1.4) değerlendirilir |
| 8 kanal × 41W = 328W ısıl yük | Termal tırmanma | K18 fan eğrisi + KSD301 kesmesi; hava akışı doğrulaması gerekir |
| Hibrit MCU zincirinde sözleşmesizlik | Firmware entegrasyon riski | §3.3.3/6 — haberleşme sözleşmesi yazılı olarak sabitlenir |
| Debate transcript'i bulunamadı | Karar gerekçesinin ham kaydı yok | §7.1'de ⚠️ ile işaretli; özete dayanır |

### §4.3 Ölçüm Tanımı (120dB+ ne demek?)

Bu ADR, **120dB+** hedefini iki ayrı metrik olarak tanımlar ve ikisini de ölçmeyi zorunlu kılar:

| Metrik | Hedef | Vault referansı | Not |
|---|---|---|---|
| **SNR** (sinyal/gürültü) | **≥ 105dB** (min kabul) | PROJECTS L305 *"SNR >105dB"* | Doğrudan ölçülebilir |
| **Dinamik aralık (DR)** | **120dB+** | vault'ta karşılığı **yok** | ⚠️ Dijital ses seviyesi + ADC/çıkış bütçesiyle tanımlanmalı |

> ⚠️ **VERIFICATION REQUIRED:** "120dB+" ifadesi vault içinde hiçbir yerde **dB** olarak geçmez ([[../../CLAUDE.md]] L113/L116'daki `120` değerleri **bileşen sayısı** sütunundadır, dB değildir). Bu ADR, 120dB+'yı **spec hedefi** olarak kaydeder; doğrulanması için ölçüm düzeneği tanımlanması gerekir. Ölçüm tanımlanana kadar **SNR >105dB** uygulanabilir minimum eştir.

### §4.4 Fallback

Ölçüm 120dB+ DR'yi tutturamazsa karar **topoloji olarak değişmez**; yalnız gürültü hedefi revize edilir ve bu ADR `updated` alanı güncellenerek yeniden onaylanır. Topoloji değişikliği (ör. Class D'ye geçiş) **yeni bir ADR** gerektirir.

---

## §5 Uygulama

| Katman | Uygulama adımı | Çıktı |
|---|---|---|
| **K16** | Darlington çift + diferansiyel giriş + Class AB çıkış stage, kanal başına ayrı PCB, enable pinli | 8 × modül PCB |
| **K17** | LM5122 interleaved dual boost: 12–24V DC → ±35V, OR-ing (6S LiPo / DC adaptör), UVP/OVP/OCP/OTP | ±35V bar |
| **K18** | Fischer SK53-100-SA (300×75×49mm) + Noctua NF-A8 PWM (80mm) + KSD301 72°C kesme | Termal koruma |
| **K19** | 6-layer, 200×100mm, 2oz, ENIG, IPC Class 3, impedance matched, star ground, thermal vias | PCB stackup |
| **K20** | 1.775 satır BOM, Mouser/Digikey tedarik, DC offset koruma rölesi | Üretim paketi |
| **MCU** | XMOS XU316 + STM32H7/RP2040 + RPi5; MCU ↔ kanal modülü haberleşme sözleşmesi | Firmware arayüzü |

---

## §6 Dokümanlar

| Doküman | Yol |
|---|---|
| Karar indeksi | `.ai/.decisions/index.md` §4 |
| Vault ana indeksi | `.ai/index.md` §5.2 |
| Katman tanımı | `.ai/CLAUDE.md` (K16–K20, H1–H5) |
| Proje durumu | `.ai/PROJECTS.md` L305–L311 |
| Devre kısıtları | `.ai/architecture/k16-class-ab/CLAUDE.md` |
| Beyin indeksi | `.ai/brain.md` L41, L284, L907, L1027 |
| Ekosistem / exa | `.ai/ecosystem/donanım-devre-referanslari.md` §3.1, §3.11 |
| Seri özeti | `README.md` L73, L171 |

---

## §7 Onay

| Alan | Değer |
|---|---|
| **Durum** | ✅ accepted |
| **Tarih** | 2026-09-24 |
| **Karar veren** | Vault sahibi (doğrudan icazet) + 3 turluk 20 persona ajan tartışması |
| **Önceki durum** | Draft (`ADR-089-classab-24v`) |
| **Onay kaynağı** | Kullanıcı: *"ok devam et başla"* / *"bunu direk c:\www\coremusic.net içinde yap"* |

### §7.1 Tartışma Kaydı (Debate)

**Biçim:** 3 tur / 20 persona ajan tartışması — kayıt, bu ADR'nin `kaynak` frontmatter alanında belgelenmiştir.

> ⚠️ **VERIFICATION REQUIRED — Ham transkript bulunamadı.**
> `deb-*.md` dosyası vault'ta **yoktur** (0 eşleşme); claude-mem worker **offline** durumdadır; `mem-search` sorgusu başarısız olmuştur. Bu nedenle:
> - **Oy dağılımı sayısal olarak kaydedilmemiştir** (uydurma sayılmaz).
> - Aşağıdaki özet, tartışma **kayıt özeti**ne dayanır; ham persona bazlı döküm **diskte değildir**.

**Özet — tartışılan ve sonuçlanan eksenler:**

| Tur | Eksen | Sonuç |
|---|---|---|
| 1 | Topoloji seçimi (Class AB vs Class D vs Class DC) | **Class AB** kabul; Class D/TPA3255 ve Class DC **reddedildi** |
| 2 | Güç mimarisi + kanal mimarisi | **12–24V → boost → ±35V**; **1/2/4/6/8 modüler kanal** kabul |
| 3 | Gürültü hedefi + kontrol zinciri | **120dB+ hedef** + **hibrit MCU** (XMOS / STM32·RP2040 / RPi5) kabul |

**Açık kalanlar:** 120dB+ DR ölçüm tanımı (§4.3) ve PIC32MZ adayının vault'ta kaydı olmaması (§2.1) — ikisi de bu ADR'de açıkça işaretlidir ve üretim öncesi kapatılmalıdır.

---

*ADR-089 · accepted · 2026-09-24 · vault UTF-8 writer ile üretilmiştir · Red Team · Human Mode · Truth Mode*
