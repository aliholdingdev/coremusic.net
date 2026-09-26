---
id: ADR-038
title: "Sound Card Chip Selection — PCM3168A + XMOS XU316 (8-kanal ses kartı çip seçimi · pin/şema entegrasyonu · saat/jitter bütçesi · firmware besleme · test/ölçüm planı)"
type: adr
category: audio
date: 2026-09-26
updated: 2026-09-26
version: 1.0.0
status: accepted
authority: ADR-038 Karar Metni (SSOT)
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)"
deciders: ["Vault Steward", "Master Orchestrator"]
consulted: ["Audio Hardware Engineer", "DSP Firmware Engineer", "Embedded Engineer"]
informed: ["QA Engineer", "Backend Architect", "Windows Software Engineer"]
supersedes: null
superseded-by: null
related:
  - "[[.ai/.decisions/accepted/ADR-017-dsp-hardware-mode.md]]"
  - "[[.ai/.decisions/accepted/ADR-019-per-os-neva-player.md]]"
  - "[[.ai/.decisions/accepted/ADR-025-professional-eq-system.md]]"
  - "[[.ai/.decisions/accepted/ADR-032-ipc-contract-versioning.md]]"
  - "[[.ai/brain.md]]"
---

# ADR-038: Sound Card Chip Selection — PCM3168A + XMOS XU316

**Durum:** accepted (Draft → Review → Active → **Active**; **frozen YOK**)
**Debate:** ✅ TAMAMLANDI (3 tur / 20 persona — 18 kabul / 2 çekimser / 0 red → **KABUL**) · **Tech Lead:** ✅ · **Arch Lead:** ⏳
**Tarih:** 2026-09-26
**Karar Veren:** Vault Steward (kullanıcı onaylı karar kapsamı: **(a)** seçim gerekçesi + alternatif redleri · **(b)** pin/şema entegrasyonu (I2S/TDM, saat ağacı) · **(c)** saat/jitter bütçesi · **(d)** firmware besleme (ADR-017 XMOS katmanı) · **(e)** test/ölçüm planı) + Master Orchestrator (disk kanıt taraması + §1.3 web araştırması)
**İlgili ADR'ler:** [[.ai/.decisions/accepted/ADR-017-dsp-hardware-mode.md]] (XMOS DSP katmanı + hard-RT kısıtları — firmware beslemesinin sınırı) · [[.ai/.decisions/accepted/ADR-019-per-os-neva-player.md]] (IAudioBackend arayüzü — ses kartının bağlanacağı yazılım sınırı) · [[.ai/.decisions/accepted/ADR-025-professional-eq-system.md]] (EQ zinciri — 8 kanal akışının tüketicisi) · [[.ai/.decisions/accepted/ADR-032-ipc-contract-versioning.md]] (firmware↔host IPC sözleşmesi)

**Karar türü:** **TEYİT** — çip seçimi zaten [[.ai/brain.md]] §8/§8.1'de verilmiş ve **değişmez** (`ADR-038 | XMOS XU316 + PCM3168A (PCM5122 REDDEDİLMİŞ)` — `:998`). Bu ADR seçimi **gerekçe + entegrasyon + ölçüm planıyla** kayda bağlar; yeni bir seçim yapmaz.

---

## 1. Bağlam (Context)

CoreMusic ses zincirinin donanım çekirdeği, **8 kanal çıkış + 6 kanal giriş** ve **USB üzerinden UAC2.0** gerektirir (8.1 surround hedefi — [[.ai/brain.md]] `:292`). Çekirdek iki çipten oluşur: konvertör (DAC/ADC) ve USB/akış denetleyicisi. Bu ADR, bu iki çipin **neden PCM3168A + XMOS XU316 olduğunu**, **pin/saat/seviye düzeyinde nasıl bağlandığını**, **jitter bütçesinin nasıl dağıtıldığını**, **firmware'in (ADR-017) bu donanımı nasıl beslediğini** ve **kabulün nasıl ölçülüp doğrulanacağını** tek kayıtta toplar.

### 1.1 Mevcut Durum (disk + kod kanıtı)

**A) Karar zaten vault'ta (IMPLEMENTED — karar olarak):**

| Kaynak | Kanıt |
|--------|-------|
| [[.ai/brain.md]] `:280-286` | §8 Hardware: XU316 **UAC2.0**; **PCM3168A 6-in/8-out, 24-bit, 192 kHz, 112 dB SNR**; AK4458 "opsiyonel"; **PCM5122 2-kanal → RED**; Class AB; ASIO 512/48 kHz ≈ **10.67 ms** |
| [[.ai/brain.md]] `:292` | §9: **8.1 surround** hedefi |
| [[.ai/brain.md]] `:998` | `ADR-038 | XMOS XU316 + PCM3168A (PCM5122 REDDEDİLMİŞ)` — bu dosyanın slotu |
| [[.ai/brain.md]] `:866` · `:878` | §19 edge-case slotu · §20 **H001** = PCM5122 ailesi yasağı |
| [[.ai/architecture/k1-donanim/CLAUDE.md]] `:20,30,31,40,48` | PCM5122 red gerekçesi, XMOS UAC2.0, DAC = PCM3168A 8-out |
| [[.ai/architecture/firmware/xmos-firmware.md]] · `i2s-driver.md` · `index.md` `:90-92` | XMOS firmware + I2S sürücü **spec dokümanı = IMPLEMENTED** |

**B) KOD — implementasyon 0 (dürüst bulgu):**

Tarama: `PCM3168 | xmos | i2s | soundcard` → repo genelinde **tek eşleşme**; `\bDAC\b` → **tek eşleşme**:

| Dosya:satır | İçerik | Dürüst etiket |
|-------------|--------|---------------|
| `shared/src/AI/AIEngine.php:147` | yorum satırında PCM3168A/XMOS geçişi | yorum — kod değil |
| `shared/src/AI/AIEngine.php:25` · `:155` | `DAC` kelimesi + `analyzeHardware()` **placeholder** (kendi yorumu: "placeholder") | iskelet — implementasyon yok |
| `*.cpp / *.h / *.xc` | **0 dosya** | XMOS/I2S/TDM firmware kodu repoda yok |

→ **Sonuç: donanım seçimi karar olarak IMPLEMENTED, kod/firmware karşılığı = 0 → entegrasyon ve ölçüm planının tamamı PLANNED.**

**C) Spec çelişkisi (açıkça işaretlenir):**

| Dosya | İddia | Durum |
|-------|-------|-------|
| [[.ai/architecture/k1-donanim/dac-adc-zinciri.md]] `:18-19,:63-92` | **DAC = AK4458**, **ADC = PCM3168A** (rol ters) | ADR-038/brain §8 ile **çelişir** → SSOT bu ADR'dir (brain `:998`), zincir dosyası düzeltme §5.1 adım 4 |
| [[.ai/architecture/k1-donanim/ak4458-dac.md]] `:44-64` | XMOS → AK4458 **TDM pin** planı | AK4458 "opsiyonel" konumuyla uyumlu (§3 alt.2) |
| [[.ai/ecosystem/donanım-devre-referanslari.md]] `:94,:113,:237` | **ES9039** referansı | PCM3168A/AK4458 geçişi **açık soru** → §3 alt.4 + §4.3 R3 |

**D) Kırık referanslar (kapsam dışı, §5.1'e yazılıdır):** `.ai/electronic/` dizini **YOK** → [[.ai/glossary]] `:491` (`electronic/hardware/audio-interface.md`), `[[.ai/index.md]]` `:268` (`electronic/xmos-pcm3168a-design` — kırık, onarım §5.1 adım 5) kırık; `.ai/reports/broken-files-report.md` `:169,183` + `broken-links-report.md` `:44,53` bu ADR'nin eski noktalı slug'ını kırık listeliyor.

### 1.2 Sorun Tanımı

1. Karar vault'ta var ama **tek satırlık slot**; neden PCM3168A olduğu (alternatif redleri), **pin/saat nasıl bağlanacağı**, **jitter nereye dağıtılacağı** ve **nasıl ölçülüp kabul edileceği** yazılı değil — gelecekteki biri "neden böyle?" sorusuna cevap bulamaz (şablon §2.2.4).
2. **Kod 0** olduğu için "donanım hazır" yanılgısı var: spec dokümanları IMPLEMENTED, firmware/kod PLANNED.
3. Vault içinde **rol çelişkisi** (C tablosu) ve **H001 PCM5122 yasağı ile XMOS referans kartının 4×PCM5122 dizilimi** arasında gerilim var — ikisi de bu ADR'de dürüstçe işaretlenmezse sonraki oturumda yeniden tartışılır.

### 1.3 Web'den Araştırma Raporu & Sonuçları

> Protokol: [[.claude/skills/prompt-maker/references/10-web-research-protocol.md]] — **5 sorgu** bu protokolle çalıştırıldı (resmi/anahtar kaynak önce: ti.com, xmos.com, aes.org, iec.ch; her ana iddia ≥2 çapraz kaynak; kanıtsız iddia → `⚠️ VERIFICATION REQUIRED`).

| Alan | Değer |
|------|-------|
| Web Search **Query** | `1)` `TI PCM3168A datasheet 24-bit 6-in 8-out DAC ADC SNR THD+N` · `2)` `XMOS XU316 USB audio reference design lib_xua I2S TDM 8 channel` · `3)` `audio DAC clock jitter budget THD+N sensitivity PLL reclocking` · `4)` `AES17 IEC 61606 audio measurement THD+N SNR channel separation standard` · `5)` `audio DAC ADC PCB layout best practices I2S clock routing ground plane split multichannel` |
| Web Search **Konusu** | Konvertör seçimi (PCM3168A veri sayfası), USB denetleyici referans mimarisi (XMOS XU316 / `lib_xua`), saat-jitter ilişkisi, ölçüm standartları (AES17-2020 / IEC 61606), çok-kanallı PCB yerleşim kuralları |
| Web Search **Bağlam** | ADR-038 TEYİT kapsamı: (a) seçim+alternatif red, (b) pin/şema+saat ağacı, (c) jitter bütçesi, (d) firmware besleme, (e) test/ölçüm — her başlığın iddiasının dış kaynakla doğrulanması gerekiyor |
| Web Search **Kısa Açıklama** | PCM3168A tek çipte 6-in/8-out + 112 dB SNR verir; XU316 `lib_xua` ile UAC2.0 ve 8×I2S/TDM üretir; jitter THD+N'i doğrudan sınırlar; AES17/IEC 61606 ölçümün tarifidir; PCB'de tek sürekli GND + bölme disiplini şarttır |
| Web Search **Uzun Açıklama** | **(1)** TI PCM3168A: 24-bit, 6-in/8-out, DAC **THD+N −94 dB**, **SNR/DR 112 dB (EIAJ A-weighted)**, ADC THD+N −93 dB / SNR 107 dB diferansiyel, kanal ayrımı **102/108 dB**, diferansiyel 8 VPP, **8–192 kHz (192 yalnız DAC; ADC 96 kHz)**, sistem saati **128–768 fS**, formatlar **I2S/LJ/RJ/DSP/TDM**, HTQFP-64, 5 V analog + 3.3 V digital, durum **ACTIVE**. **(2)** XMOS: `sw_usb_audio` 9.2.0 → **`lib_xua`**, hedef `app_usb_aud_xk_316_mc` / **XK-AUDIO-316-MC (XU316-1024-TQ128)**, **8-in + 8-out ≤192 kHz I2S** (fazlası TDM), **UAC2 async**, I2S/TDM master+slave, codec yapılandırması **I2C** (`AudioHwInit`/`AudioHwConfig`), `clk_mode` = CLK_FIXED/CLK_PLL/CLK_CS2100, `I2S_CHANS_DAC=8`; saat yaklaşımı "local clocking, async USB, PLL clock recovery". **DİKKAT:** XMOS referans kartı **4× PCM5122 + 2× PCM1865** kullanır (H001 ile gerilim — §4.3 R2). **(3)** Jitter: 1 ns RMS taban bant jitter ≈ 20 kHz'te **81 dB** D+N sınırı (Cirrus WP); PLL bant <1 kHz'e düşürülünce ~3 ns ≈ 71 dB; jitter-dayanıklı DAC 1 kHz'te **−108 dB THD+N** sınırını jitter'e bağlar (EDN); saat hattı en gürültü-kritik hat, düşük gürültülü LDO şart (ADI). **(4)** Ölçüm: **AES17-2020** (notch Q 1–5, THD+N, cross-talk §10 −20 dBFS sine), **IEC 61606-1:2009 / 61606-3** (idle channel noise = SNR, DR, kanal ayrımı, jitter tanımı), TI SBAA055 + EIAJ CP-2404 (4 Hz–20 kHz passband, A-weighted), FADGI/AES17 test matrisi (−1/−10/−20/−60 dBFS × 41/997/6597 Hz @48k & 96k). **(5)** PCB: tek sürekli GND düzlemi + **bölme (partition)** tercih; split zorunluysa izler köprüden geçmeli (TI SLYT512); saat izleri düzlem yarığı **kesmemeli** (kesim = EMI döngüsü + jitter artışı — TI SPRACP4, C-Media); I2S çoklu-hat **daisy-chain** + **22 Ω seri sönümleme** + **300 mil'de bir toprak via'lı guard** (Aivon); çok ADC'li sistemde **eşit clock iz uzunluğu** (~60 ps/cm → 5 cm fark = 300 ps kayma) (TI SBAA520). |
| Web Search **Paragraf Veri Uzun** | PCM3168A'nın 112 dB SNR / −94 dB THD+N değerleri, XU316'ın `lib_xua` üzerinden 8×I2S + UAC2 async üretimi, "1 ns jitter ≈ 81 dB D+N" bağıntısı, AES17-2020 + IEC 61606 ölçüm tarifi ve "tek GND + bölme + köprü" PCB kuralı birlikte, ADR-038'in beş kapsam maddesinin (a–e) tamamını dış kaynakla destekleyen tek bir veri gövdesi oluşturur; bu beş başlık dışındaki iddialar (ör. belirli bir jitter-sayısal bütçe) vault içi kanıta dayanır ve `⚠️ VERIFICATION REQUIRED` ile işaretlenir. |
| Web Search **Sonucu** | 5/5 sorgu tamamlandı, **20 kaynak** derlendi (aşağıda); PCM3168A ve XU316 varlık/yapı iddiaları **resmi kaynakla (ti.com, xmos.com) doğrulandı**; jitter-ölçüm-PCB iddiaları üretici uygulama notlarıyla çaprazlandı; **kanıtlanamayan tek sayısal** = CoreMusic'e özel jitter bütçe sayıları (§2.2-c, `⚠️ VERIFICATION REQUIRED`) |
| Web Search **Alınan Karar** | **PCM3168A + XU316 seçimi teyit edildi** (tek çip 8-out → H001 gereği PCM5122 red'i doğrulandı); saat ağacı: **tek kaynak XU316 → PCM3168A sistem saati (128–768 fS) + I2S/TDM master**; ölçüm tarifi **AES17-2020 + IEC 61606-1 + EIAJ CP-2404** olarak alındı; PCB kuralı **tek sürekli GND + bölme + köprü** (§2.2-b); XMOS referans kartının 4×PCM5122 dizilimi **H001 gerekçesiyle kopyalanmadı** |
| Web Search **Sonuç** | Dış kaynaklar seçimi **destekliyor**; tek gerilim (XMOS referans kartı ≠ H001) ve tek belirsiz (sayısal jitter bütçesi) açıkça kayda geçti → §4.3 R1/R2 + §2.2-c |

**Kaynak listesi (20):** 1) ti.com/lit/ds/symlink/pcm3168a.pdf · 2) ti.com/product/PCM3168A · 3) xmos.com/documentation/XM-008854-UG (XUA user guide) · 4) github.com/xmos/sw_usb_audio (`lib_xua`) · 5) xmos.com/xk-audio-316-mc-ab (XK-AUDIO-316-MC) · 6) Cirrus Logic WP — jitter & baseband · 7) EDN — jitter sensitivity of audio DACs · 8) Analog Devices — phase/supply noise (CN-0085 hattı) · 9) TI SLAA520 (PFD/jitter) · 10) AES17-2020 standardı · 11) IEC 61606-1:2009 · 12) IEC 61606-3 · 13) TI SBAA055 (THD+N/DR/separation ölçümü) · 14) EIAJ CP-2404 · 15) FADGI/AES17 test matrisi · 16) Electronic Design — AES3 jitter tolerance (0.25 UI) · 17) TI SLYT512 (grounding part 2) · 18) C-Media CM6648 layout guide · 19) Aivon — PCB design guidelines for audio interface circuits · 20) TI SPRACP4 + TI SBAA520 (clock tree/iz uzunluğu) · ayrıca ADI mixed-signal layout makalesi ve nodeloop I2S rehberi (20'ye dahil tekrar değil, çapraz kaynak).

### 1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| **H001 — PCM5122 yasağı** | [[.ai/brain.md]] `:878` + `k1-donanim/CLAUDE.md:20`: PCM5122 ailesi **2 kanal** olduğu için yasak; alternatiflerde PCM5122 tabanlı hiçbir dizilim önerilemez (edge-case #8: "PCM5122 kullanımı → PCM3168A/AK4458 öner" — [[.ai/AGENTS.md]] §17) |
| **ADR-017 hard-RT kısıtları** | DSP/akış katmanı gerçek zamanlı; ses kartı firmware beslemesi bu kısıtların içinde kalır — donanım yan yolları (I2C konfigürasyonu hariç) RT yoluna girmez |
| **Frozen 001–036 immutabel** | ADR-001…ADR-036 değiştirilemez; bu ADR yalnız onlara **atıf** yapar, düzeltme yapmaz |
| **Kod 0 → PLANNED disiplini** | `*.cpp/*.h/*.xc` = 0; entegrasyon, pin planı ve ölçüm adımlarının tamamı **PLANNED** olarak etiketlenir (IMPLEMENTED yalnız diskte/spec'te olan) |
| **REDACTED** | Tedarikçi fiyat, stok kodu dışı sır/credential hiçbir koşulda yazılmaz |
| **Tek yazma kanalı** | Tüm vault yazımı `.ai/scripts/vault-utf8-writer.mjs` ile; `log.md` yalnız append |

---

## 2. Karar (Decision)

**PCM3168A (24-bit · 6-in/8-out çok-kanallı konvertör) + XMOS XU316 (USB UAC2.0 denetleyicisi, I2S/TDM master)** çifti, CoreMusic ses kartının çekirdeği olarak **TEYİT edilir**. XU316 USB'den asenkron UAC2.0 akışını alır, kanalları **I2S (8 kanal sınırı) veya TDM (8+ kanal)** olarak üretir, PCM3168A'nın sistem saatini ve veri hatlarını sürer; PCM3168A analog çıkışları 8-out/6-in sağlar. **PCM5122 ailesi H001 gereği reddedilmiştir.**

### 2.1 Neden Bu Seçenek?

1. **Tek çip = 8 çıkış:** PCM3168A tek pakette 8-out sağlar → 8.1 surround ([[.ai/brain.md]] `:292`) tek entegrasyonla karşılanır; PCM5122 gibi 2-kanal çiplerden kaç çip gerektiğini artırır (H001 red gerekçesi).
2. **Ölçülebilir spesifikasyon:** 112 dB SNR / −94 dB THD+N (DAC, EIAJ A-weighted) — hedef Class AB çıkış zinciri için ölçümle teyit edilebilir tavan verir (§2.2-d).
3. **Denetleyici olgunluğu:** XU316 + `lib_xua` resmi referans mimarisi **8-in/8-out UAC2.0**'ı doğrudan destekler; I2C ile codec konfigürasyonu (`AudioHwInit`/`AudioHwConfig`) firmware beslemesini temiz ayırır (§2.2-e).
4. **Format esnekliği:** PCM3168A I2S/LJ/RJ/DSP/**TDM** + 128–768 fS saat desteği → 8 kanal TDM'e ölçeklenir, saat ağacı tek noktada toplanır (§2.2-b).
5. **Vault tutarlılığı:** Seçim zaten brain §8'de; bu ADR onu **tek SSOT** kayıtta toplar, çelişkili dosyaları işaretler (§5.1 adım 4).

### 2.2 Teknik Detaylar

#### (b) Pin / şema entegrasyonu — I2S/TDM, saat ağacı

| Arayüz | Yön | Sinyaller | Not |
|--------|-----|-----------|-----|
| **USB** | Host ↔ XU316 | USB 2.0 D+/D−, VBUs | UAC2.0 **async** — host saatine kilitlenmez |
| **I2S/TDM (çıkış)** | XU316 → PCM3168A | **BCLK, LRCK/FS, SDATA (TDM'de 8 slot)** | XU316 **master** (BCLK/LRCK üretir); `I2S_CHANS_DAC=8` |
| **Saat (MCLK/SCK)** | XU316 → PCM3168A | sistem saati **128–768 fS** | tek kaynak; PLL/CS2100 `clk_mode` seçimi firmware'de (`CLK_FIXED/CLK_PLL/CLK_CS2100`) |
| **I2S (giriş)** | PCM3168A → XU316 | ADC DOUT + BCLK/LRCK | 6 kanal; 96 kHz üstü sınır (§4.2) |
| **Kontrol** | XU316 → PCM3168A | **I2C** (SCL/SDA) | register init `AudioHwInit` — RT yolunun dışında |
| **Analog** | PCM3168A ↔ çıkış zinciri | diferansiyel **8 VPP** | Class AB sürücüye ([[.ai/architecture/k1-donanim/class-ab-amplifikator.md]]) |

- TDM tercih edilirse tek SDATA'da 8 slot (slot genişliği 16/24/32 b); standart I2S'te 4×2 kanal hattı gerekir → **saat hattı çoğaltılmaz, daisy-chain** yapılır (§1.3 kaynak 19).
- Pin eşlemesi detayı: [[.ai/architecture/k1-donanim/i2s-interface.md]] + `ak4458-dac.md:44-64` (TDM pin örneği) → **PLANNED** (kod 0).

#### (c) Saat / jitter bütçesi

| Kalem | Hedef | Kanıt/durum |
|-------|-------|-------------|
| Saat kaynağı | tek kaynak: XU316 iç osilatör/PLL → PCM3168A | §2.2-b — IMPLEMENTED (spec) |
| Bağıl jitter etkisi | "1 ns RMS ≈ 20 kHz'te 81 dB D+N" bağıntısı → bütçe **<1 ns RMS** hedeflenir | dış kaynak (6,7) — hedef burada |
| CoreMusic'e özel sayısal bütçe (BCLK, MCLK, LRCK payları) | `⚠️ VERIFICATION REQUIRED` — ölçümle belirlenecek | **kanıt yok, uydurulmadı** |
| PLL stratejisi | yavaş PLL + re-clock; PLL bant genişliği <1 kHz (Cirrus: ~3 ns → ~71 dB riski) | dış kaynak — firmware seçimi PLANNED |
| Güç/jitter izolasyonu | saat hattı için düşük gürültülü LDO; saat izleri GND yarığını kesmez | dış kaynak (8, 17, 18) |
| Ölçüm kapısı | jitter bütçesi ancak §2.2-d ölçümüyle **kapanır** (THD+N @1 kHz farkı) | PLANNED |

#### (d) Test / ölçüm planı (kabul kriterleri)

| # | Ölçüm | Tarif | Hedef (PCM3168A veri sayfası + standart) |
|---|-------|-------|------------------------------------------|
| M1 | THD+N @ 1 kHz, 0 dBFS | **AES17-2020** notch (Q 1–5), **EIAJ CP-2404** 4 Hz–20 kHz passband, A-weighted | **≥ 90 dB** (veri sayfası −94 dB'e yakın; ölçüm ortamı farkıyla marj) |
| M2 | SNR / dinamik aralık (idle channel noise) | **IEC 61606-1** idle channel noise | **≥ 105 dB** (veri sayfası 112 dB) |
| M3 | Kanal ayrımı (cross-talk) | AES17 §10, −20 dBFS sine | **≥ 95 dB** (veri sayfası 102/108 dB) |
| M4 | Frekans/level matrisi | FADGI/AES17: −1/−10/−20/−60 dBFS × 41/997/6597 Hz @ 48k & 96k | sapma raporlanır, eşiği aşarsa R1'e düşer |
| M5 | Kanal-senkron (kayma) | çoklu-kanal eşit clock iz kontrolü (~60 ps/cm) | ≤ 100 ps kanal arası |
| M6 | Jitter duyarlılığı | M1'in clock kaynağı değişimiyle (PLL on/off) tekrarı | fark ≤ 1 dB (büyük fark → §4.3 R1) |

> Ölçüm tarifi standart kaynaklıdır; **kabul eşikleri** veri sayfası değerlerinden **marjlı** alınmıştır (tam eşikler laboratuvar kurulumuna göre revize edilir → §5.1 adım 6).

#### (e) Firmware besleme (ADR-017 XMOS katmanı)

| Katman | Sorumluluk | Durum |
|--------|-----------|-------|
| UAC2.0 async USB | `lib_xua` / XU316 — 8-out/6-in akış | spec IMPLEMENTED (`xmos-firmware.md`), **kod 0 → PLANNED** |
| Codec init | I2C → `AudioHwInit`/`AudioHwConfig` (PCM3168A registerleri) | PLANNED |
| I2S/TDM sürücü | master mod, `I2S_CHANS_DAC=8`, TDM slot config | spec: `i2s-driver.md` — kod 0 |
| RT kısıtları | DSP/akış hard-RT; donanım konfigüasyonu RT dışı | [[.ai/.decisions/accepted/ADR-017-dsp-hardware-mode.md]] `:260` (frozen — okunur, değişmez) |
| Host↔firmware sözleşmesi | kanal eşlemesi/IPC sürümleme | [[.ai/.decisions/accepted/ADR-032-ipc-contract-versioning.md]] |
| Yazılım tüketimi | 8 kanalın backend'e ulaşması | [[.ai/.decisions/accepted/ADR-019-per-os-neva-player.md]] (IAudioBackend) + [[.ai/.decisions/accepted/ADR-025-professional-eq-system.md]] (EQ) |

ADR-017 adım 5'teki kanal eşlemesi (PCM3168A/AK4458 girişi) bu planla **birebir** örtüşür (frozen ADR-017:260 — çelişki yok).

---

## 3. Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|------------------|
| 1 | **PCM5122 (×4 veya ×8 dizilim)** | Olgun I2C DAC, XMOS referans kartında kullanılan çip (§1.3 kaynak 5) | Çip başına **yalnız 2 kanal** → 8-out için 4+ çip, pin/PCB karmaşası | **H001 yasağı** ([[.ai/brain.md]] `:878`, `k1-donanim/CLAUDE.md:20`) — edge-case #8: PCM5122 → PCM3168A/AK4458 öner |
| 2 | **AK4458 tek DAC (8-out)** + ayrı ADC | XMOS referans TDM pin planı hazır (`ak4458-dac.md:44-64`), yüksek SNR | **DAC-only — ADC yok**; ayrı ADC çipi + kanal sayısında ADC 6'yı karşılama ek yük | brain §8'de AK4458 **"opsiyonel"** — birincil konvertör olarak tek başına ADC'siz kalır; çift-çip maliyeti PCM3168A'nın tek-çip 6-in/8-out'unu yenmez |
| 3 | **XMOS referans kart topolojisi (4× PCM5122 + 2× PCM1865)** | Üretici referansı = kanıtlanmış debug yolu | PCM5122 → **H001 ihlali**; 6 çip + 2 ayrı ADC | H001 yasağı bu dizilimi **düz reddeder**; referans yalnız firmware/I2S mimarisi için kullanılır (§2.2-e), çip listesi kopyalanmaz → §4.3 R2 |
| 4 | **ES9039 (veya üstün ayrı DAC) + ayrı ADC** | Datasheet'te daha yüksek tavan (ör. >120 dB sınıfı) | **Farklı mimari** (TDM/kontrol/register), vault'ta yalnız ekosistem referansı var; PCM3168A ile pin-uyumsuz | **Açık soru olarak kaldı** ([[.ai/ecosystem/donanım-devre-referanslari.md]] `:94,:113,:237`) — mevcut karar STM'de değil; yeni çip = **yeni ADR** ister (§4.3 R3 fallback'i) |
| 5 | **Harici USB çok-kanallı ses kartı (satın alınmış modül)** | Sıfır PCB riski, sürücü hazır | Cihaz bagajına bağımlılık, 8.1 hedefi için fiyat/kontrol kaybı, CoreMusic donanım-vizyonuyla (K1, Class AB zinciri) uyuşmaz | Proje K0–K5 kendi donanımını üretir ([[.ai/AGENTS.md]] §5 domain boundary); vizyon "üretim" tarafında |

---

## 4. Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- **Tek çip 8-out + 6-in** → 8.1 surround tek entegrasyon, H001 ile tam uyum.
- **Tek saat kaynağı + TDM** → saat ağacı tek noktada; jitter bütçesi izole edilebilir (§2.2-c).
- **Ölçümle doğrulanabilir kabul** (AES17/IEC 61606/EIAJ) → "iyi ses" tartışması ölçüme bağlanır (§2.2-d).
- **`lib_xua` referansı + I2C init** → firmware beslemesi ADR-017/ADR-032 sözleşmelerine temiz oturur (§2.2-e).
- Karar tek SSOT'da toplandı; brain slotu (`:998`) artık dosyaya işaret edilebilir.

### 4.2 Olumsuz Sonuçlar

- **Kod 0** → hiçbir entegrasyon/ölçüm bugün çalışmıyor; tamamı **PLANNED** (B bölümü).
- **PCM3168A ADC 96 kHz sınırı** → 192 kHz yalnız **çıkış** kanalında; giriş tarafı 96 kHz ile sınırlı (§1.3 kaynak 1).
- **HTQFP-64 + 5 V analog / 3.3 V digital** → iki rail, PCB tasarımı artar (bölme/köprü kuralı §1.3 kaynak 17-18).
- **Rol çelişkisi** (`dac-adc-zinciri.md` AK4458=DAC) bu ADR ile çözülmez, yalnız işaretlenir → §5.1 adım 4.
- **Sayısal jitter bütçesi yok** → `⚠️ VERIFICATION REQUIRED`; ölçüm M6'ya kadar açık kalır.

### 4.3 Riskler

| Risk | Olasılık | Etki | Mitigasyon |
|------|---------|------|-----------|
| **R1** Sayısal jitter bütçesi tutmaz (ölçüm M6'da THD+N >1 dB kötüleşme) | Orta (%40-70) | Orta | Yavaş PLL + re-clock + saat LDO (§2.2-c); bütçe ölçümle kapatılır; gerekirse CS2100 `clk_mode` (§1.3 kaynak 3) |
| **R2** XMOS referans kartının 4×PCM5122 dizilimi kopyalanması / referanstan sapma yanlışı | Düşük-Orta | Yüksek | H001 kuralı zaten yasak (§1.4); referans yalnız firmware mimarisi için okunur; pin planı bağımsız doğrulanır (§5.1 adım 3) |
| **R3** ES9039 vb. üstün-çip baskısı → geçiş talebi | Düşük (%10-40) | Yüksek (yeniden tasarım) | **Fallback: yeni ADR** (bu ADR superseded-by ile bağlanır); mevcut PCM3168A pin/saat planı geçiş maliyetini §2.2-b'de görünür kılar |
| **R4** Spec çelişkisi (`dac-adc-zinciri.md` AK4458=DAC) sonraki oturumda SSOT sanılır | Orta | Orta | §5.1 adım 4: düzeltme + log; bu ADR §1.1-C çelişki tablosu kalıcı kayıt |
| **R5** Kod 0 iken "hazır" sayılması (spec IMPLEMENTED etiketi yanılgısı) | Orta | Yüksek | Her başlıkta IMPLEMENTED/PLANNED etiketi (§1.1-B); §5.1 adım 7'de firmware iskeleti ayrı görev |
| **R6** Ölçüm eşikleri laboratuvar ortamında tutmaz (M1-M3) | Orta | Orta | Eşikler marjlı yazıldı (§2.2-d notu); AES17 tarifi + FADGI matrisi tekrarlanabilirlik sağlar |

**Fallback zinciri (özet):** Ölçüm tutmazsa → R1 (saat zinciri revizyonu) → R2/R4 (pin/spec düzeltmesi) → R3 (yeni çip = **yeni ADR**, ADR-038 `superseded-by` ile kapanır). Geri dönüş: §5.2.

---

## 5. Uygulama (Implementation)

### 5.1 Adımlar

| # | Adım | Sorumlu | Süre | Durum |
|---|------|---------|------|-------|
| 1 | Bu ADR'yi şablondan üret + `.ai/.decisions/index.md:80` slug'ını `8.1` → `8-1` düzelt (nokta yasak) | Vault Steward | 2 dk | ✅ UYGULANDI (2026-09-26) |
| 2 | `.ai/log.md`'ye 1 satır append (`ADR-038 yazıldı (debate PENDING)`) | Vault Steward | 1 dk | ✅ UYGULANDI (2026-09-26) |
| 3 | Pin/saat planı şema doğrulaması: `i2s-interface.md` + `ak4458-dac.md:44-64` ile §2.2-b hizası; XMOS referans sapması = R2 | Audio HW Engineer | 1 gün | ⏳ PLANNED |
| 4 | Rol çelişkisi düzeltmesi: `k1-donanim/dac-adc-zinciri.md:18-19,63-92` → PCM3168A=8-out DAC, AK4458=opsiyonel (SSOT: bu ADR) + log | Audio HW Engineer | 1 saat | ⏳ PLANNED |
| 5 | `.ai/electronic/` kırık wiki-linkleri (`glossary.md:491`, `index.md:268`) — raporlandı, onarım ayrı iş | MO (vault-updater) | 1 saat | ⏳ PLANNED |
| 6 | Ölçüm protokolü kurulumu: M1-M6 (AES17/IEC 61606/EIAJ) + eşik revizyonu | QA Engineer + Audio HW | 2 gün | ⏳ PLANNED |
| 7 | Firmware iskeleti: `lib_xua` tabanlı UAC2.0 + I2C `AudioHwInit` + `I2S_CHANS_DAC=8` (ADR-017/ADR-032 sınırları içinde) | DSP Firmware Engineer | 3 gün | ⏳ PLANNED (kod 0) |
| 8 | Debate 3 tur / 20 persona + Tech Lead onayı (§7) | MO | 2 dk | ✅ UYGULANDI (2026-09-26 — 3 tur / 20 persona, 18/2/0 KABUL, 3 şart §5.3) |
| 9 | Jitter bütçesi kapanışı: M6 ölçümü → §2.2-c `⚠️ VERIFICATION REQUIRED` satırının yerine gerçek sayı | Audio HW Engineer | 1 gün | ⏳ PLANNED |

### 5.2 Geri Dönüş Planı

1. **Karar seviyesi:** ADR-038 `status: accepted` ama **frozen değil** → §4.3 R3'teki çip geçişi istenirse **yeni ADR** yazılır, bu dosya `superseded-by: ADR-NNN` ile bağlanır; metin **silinmez/değiştirmez** (şablon §4.10).
2. **Dizin seviyesi:** `index.md:80` düzeltmesi geri alınırsa eski noktalı slug (`ADR-038-8.1-...`) dosya adıyla eşleşmez → geri dönüş `8-1` satırıdır (dosya adı In-Place Refactoring ile değişmez).
3. **Kod seviyesi:** Kod yazılmadığı için revert gerekmez; ileride firmware iskeleti (adım 7) başarısız olursa yalnız o modül geri alınır, karar metni değişmez.
4. **Log seviyesi:** `log.md` append-only → geri dönüş de **yeni satır** olarak yazılır, geçmiş satır silinmez.
5. **Bozulma durumunda:** `vault-utf8-writer.mjs repair --file <dosya>` (yedek alır) → gerekirse `git checkout` (AGENTS §18 #5).

---

### 5.3 Debate Şartları (bağlayıcı — 3 şart)

> Debate sonucu: **3 tur / 20 persona · 18 kabul / 2 çekimser / 0 red → KABUL** (§7.1). Aşağıdaki şartlar KABUL'ün koşuludur; her biri ilgili maddeye bağlıdır.

| # | Şart | Debateden kaynak | İlgili madde | Durum |
|---|------|------------------|--------------|-------|
| **1a** | **Referans netleştirme:** XMOS referans kartının hangi referans/hangi DAC modeli olduğu açıkça yazılır (4×PCM5122 + 2×PCM1865 dizilimi); **H001 PCM5122 yasağı** ile referans arasındaki gerilim §4.3 R2'de bağlanır; referans **yalnız** firmware/I2S mimarisi için okunur, çip listesi kopyalanmaz | Tur 2-1 (DevOps: referans çelişkisi) | §1.3-2, §3 alt.3, §4.3 R2 | ✅ KAYITLI (bu § + §4.3 R2) · uygulama: §5.1 adım 3 |
| **1b** | **Rol çelişkisi düzeltmesi:** [[.ai/architecture/k1-donanim/dac-adc-zinciri.md]] `:18-19,:63-92` rol ters (AK4458=DAC, PCM3168A=ADC) → düzeltilir: **PCM3168A = 8-out DAC**, AK4458 = opsiyonel (SSOT: bu ADR, brain `:998`) + log | Tur 2-2 (Critic: rol çelişkisi şart) | §1.1-C, §4.3 R4 | ⏳ PLANNED (§5.1 adım 4) |
| **2** | **AES17 ölçüm planı:** fabrika/ölçüm protokolü **AES17-2020 + IEC 61606-1 + EIAJ CP-2404** (M1–M6) kurulur; **jitter ölçüm bütçesi** M6 ile kapatılır (§2.2-c `⚠️ VERIFICATION REQUIRED` satırı gerçek sayıyla değişir) | Tur 2-3 (QA: fabrika testi) | §2.2-c, §2.2-d | ⏳ PLANNED (§5.1 adım 6, 9) |
| **3** | **PLANNED netliği:** kod 0 (`shared/src/AI/AIEngine.php:147` yorum; `*.cpp/*.h/*.xc` = 0 dosya) → entegrasyon + ölçüm adımlarının tamamı **PLANNED**; spec'in IMPLEMENTED etiketi kod kanıtı sanılmaz (**sahte kanıt temizliği**) | Tur 2-4 (Critic: sahte kanıt) | §1.1-B, §1.4, §5.1 adım 7 | ✅ KAYITLI (bu § + §1.1-B) |

**Kanal varyant yönlendirmesi (kullanıcı kararı):** maliyet/kanal varyant ürün ailesi (mono / 2 / 2+1 / 4-8 / 7+1 / 8+1) ayrı karar olarak **ADR-083**'e yönlendirildi — bu ADR'nin kapsamı dışındadır. ⚠️ VERIFICATION REQUIRED: [[.ai/brain.md]] `:1021` bu slotu "ADR-083 = SPA Router Architecture" olarak gösteriyor; numara çakışması sonraki oturumda teyit edilmelidir.

---

## 6. İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| [[.ai/CLAUDE.md]] | Ana sözleşme + 16 Hard Guardrail |
| [[.ai/brain.md]] | §8 Hardware (`:280-286`), §9 8.1 (`:292`), slot `:998`, H001 `:878` |
| [[.ai/.decisions/index.md]] | Dizin kaydı (§5.1 adım 1) |
| [[.ai/.decisions/accepted/ADR-017-dsp-hardware-mode.md]] | XMOS katmanı + hard-RT kısıtları (§2.2-e) — frozen, okunur |
| [[.ai/.decisions/accepted/ADR-019-per-os-neva-player.md]] | IAudioBackend — ses kartının yazılım sınırı |
| [[.ai/.decisions/accepted/ADR-025-professional-eq-system.md]] | 8 kanal EQ zinciri tüketicisi |
| [[.ai/.decisions/accepted/ADR-032-ipc-contract-versioning.md]] | firmware↔host IPC sözleşmesi |
| [[.ai/architecture/firmware/xmos-firmware.md]] | XMOS firmware spec (IMPLEMENTED doküman, kod PLANNED) |
| [[.ai/architecture/firmware/i2s-driver.md]] | I2S/TDM sürücü spec |
| [[.ai/architecture/k1-donanim/pcm3168a-dac-adc.md]] | PCM3168A donanım dokümanı |
| [[.ai/architecture/k1-donanim/i2s-interface.md]] | Pin/arayüz şeması (§2.2-b doğrulama hedefi) |
| [[.ai/architecture/k1-donanim/ak4458-dac.md]] | TDM pin örneği (`:44-64`) |
| [[.ai/architecture/k1-donanim/dac-adc-zinciri.md]] | **Rol çelişkisi** (§1.1-C, §5.1 adım 4) |
| [[.ai/ecosystem/donanım-devre-referanslari.md]] | ES9039 açık sorusu (§3 alt.4, §4.3 R3) |
| [[.claude/skills/prompt-maker/references/10-web-research-protocol.md]] | §1.3 protokolü |
| Debate kaydı | §7.1 (3 tur / 20 persona, 18/2/0 KABUL) + §5.3 (3 bağlayıcı şart: referans/rol, AES17 ölçüm, PLANNED netliği) — kanal varyant ürün ailesi → ADR-083 (kullanıcı kararı; §5.3'te slot çelişkisi VERIFICATION REQUIRED) |

---

## 7. Onay

### 7.1 Debate Kaydı (3 tur / 20 persona)

| Tur | Kapsam | Sonuç |
|-----|--------|-------|
| **1** | 20 persona — kanıt: PCM3168A (24-bit/192 kHz 8-kanal DAC) + XMOS teyit; kod 0 (`AIEngine.php:147` yorum, `*.cpp/*.h/*.xc` = 0) → entegrasyon + ölçüm **PLANNED**; 5 alternatif red; jitter bütçesi `⚠️ VERIFICATION REQUIRED`; XMOS referans kartı **4×PCM5122 ↔ PCM3168A** çelişkisi (R2); rol çelişkisi `dac-adc-zinciri.md:18-19`; AES17/IEC 61606 ölçüm standardı; 20 kaynak | **15 kabul/neutral · 4 uyarı** (QA: fabrika testi; DevOps: referans çelişkisi; Critic: sahte kanıt + rol çelişkisi şart) |
| **2** | İtiraz → çözüm: (1) referans 4×PCM5122 ↔ PCM3168A + H001 yasağı gerilimi → **şart 1a**; (2) `dac-adc-zinciri.md:18-19` rol çelişkisi → düzeltme → **şart 1b**; (3) ölçüm yok → AES17 fabrika testi + jitter ölçüm bütçesi → **şart 2**; (4) kod 0 → entegrasyon PLANNED netliği + sahte kanıt temizliği → **şart 3** | 3 şart bağlayıcı → **§5.3** |
| **3** | Oy | **18 kabul / 2 çekimser / 0 red → KABUL** |

**Kanal varyant notu (kullanıcı kararı):** maliyet/kanal varyant ürün ailesi (mono/2/2+1/4-8/7+1/8+1) ayrı karar olarak **ADR-083**'e yönlendirildi (§5.3).

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Bayram Ali / Vault Steward | 2026-09-26 | ✅ |
| Tech Lead | Tech Lead (debate 3/20 onayı) | 2026-09-26 | ✅ |
| Arch Lead | — | ⏳ | ⏳ |

**Statü özeti:** `status: accepted` · debate **✅ TAMAMLANDI (3 tur / 20 persona — 18/2/0 KABUL, §7.1)** · Tech Lead **✅** · Arch Lead **⏳** · **3 şart** bağlayıcı (§5.3) · **frozen YOK** (ADR-001…036 frozen; bu dosya 038 = Active aralığı).

---

*ADR-038 v1.0.0 — CoreMusic Architecture Decision Record*
*Authority: ADR-038 Karar Metni (SSOT)*
*Last Updated: 2026-09-26*
*Mode: Red Team · Human Mode · Truth Mode*
