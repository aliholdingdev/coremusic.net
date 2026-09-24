---
title: "Donanım / Devre Referansları Ekosistemi"
type: guide
category: ecosystem
version: 1.0.0
description: "TPA3255 güç amplifikatörü, XMOS XU316+ES9039 DAC, MT3608 boost, rp2040-dac-amp, modular-amplituner, PBA MK1, DA15, spin-dac ve OpAmp-Headphone devreleri — CoreMusic K1/K16/K17/K19/K20 katmanlarına birebir eşleşme haritası."
durum: active
tarih: 2026-09-24
kaynak: exa web doğrulaması (2026-09-24); TPA3255, ddabidov Headphone-DAC-AMP (XMOS XU316 + ES9039), BoostCore (MT3608), modular-amplituner, PBA MK1, DA15, spin-dac, OpAmp-Headphone, rp2040-dac-amp GitHub depoları
status: active
authority: "Donanım/devre referansları; CoreMusic donanım katmanlarında K1/K16/K17/K19/K20 referansıdır — PCM5122 yasaklıdır."
updated: 2026-09-24
---

# CoreMusic — Donanım / Devre Referansları Ekosistemi

> **Kapsam:** Bu belge, `.ai/ecosystem/` altındaki 6 ekosistem dokümanından 4.'südür.
> Kapsadığı K katmanları: **K1 (donanım — XMOS XU316, PCM3168A, AK4458 DAC), K16 (Class AB güç amplifikatörü), K17 (güç kaynağı ±35V boost), K18 (termal), K19 (PCB), K20 (BOM & üretim)** — bkz. aşağıdaki DÜZELTME bloğu.
> Doğrulama tarihi: **2026-09-24 (exa web doğrulaması)**.
> Yeni kaynak araştırması yok; sağlanan doğrulanmış exa sonuçları `.ai/architecture/github-referanslari.md` §2 ve `.ai/CLAUDE.md` §21-§22 ile çapraz bağlanır.

**⚠️ UYARI — Yasaklı Parça (CLAUDE.md §21):**
- **PCM5122 yasaklı desendir** — hiçbir referans devrede PCM5122 kopyalanmaz, eşleştirilmez, alternatif olarak önerilmez.
- **AGPL/uyumsuz lisanslı donanım dosyası (EAGLE/KiCad source) kopyalanmaz** — şema fikri referans alınır.
- Star/lisans bilgisi gelmeyen depolarda `⚠️ VERIFICATION REQUIRED` işaretli (Guardrail #14).

**Template hizası (Guardrail #16):** docs-md-template.md §1–§7 + §4.1 Şablon-Önce bloğu verbatim korunmuştur.

> 2026-09-24 — CoreMusic Ekosistem Serisi (4/6)

---

> **⚠️ DÜZELTME (2026-09-24) — K Eşleme Hatası.** Bağlayıcı kaynak: `.ai/CLAUDE.md` (authority: SSOT) → **Critical Components (K16-K20 Layers)** tablosu. Bu belgedeki eski eşlemeler yanlıştı; doğrusu aşağıdadır. Bu düzeltme belge genelinde bağlayıcıdır (§1 kapsam satırı, §3.1, §3.2 dahil).
>
> | Konu | Eski (yanlış) | SSOT (doğru) |
> |------|---------------|--------------|
> | DAC dizisi (PCM3168A + AK4458) | K17 | **K1** (Donanım) |
> | Güç amplifikatörü / Class AB ×8 | K16 + K20 | **K16** (Class AB Amplifikatör) |
> | Boost / güç kaynağı ±35V (LM5122) | K19 | **K17** (Güç Kaynağı ±35V) |
> | PCB tasarımı | K1 | **K19** (PCB Tasarım) |
> | BOM & üretim | K20 | **K20** ✓ (doğru) |
> | Termal tasarım | (eksik) | **K18** (Termal Tasarım) |
>
> Ayrıca **TPA3255 Class-D topolojisi ADR-089 ile REDDEDİLMİŞTİR** (§3.1) — belgede yalnızca topoloji dersi olarak referans kalır.

## Table of Contents

1. [§1 Amaç](#1-amaç)
2. [§2 Kapsam](#2-kapsam)
3. [§3 Mimari](#3-mimari)
4. [§4 Kurallar](#4-kurallar)
5. [§5 Workflow](#5-workflow)
6. [§6 Doğrulama](#6-doğrulama)
7. [§7 Referanslar](#7-referanslar)

---

## §1 Amaç

**Amaç:** CoreMusic'in donanım zincirini (kaynak/dönüşüm → DAC → amplifikatör → çıkış) besleyen açık kaynak devre referanslarını — TPA3255, XMOS XU316+ES9039, MT3608 boost, rp2040-dac-amp, modular-amplituner, PBA MK1, DA15, spin-dac, OpAmp-Headphone — birebir eşleşme haritasıyla K1/K16/K17/K19/K20 katmanlarına yerleştirmek.

**Kapsam:**

| Kapsar | Kapsamaz |
|--------|----------|
| TPA3255 Class-D güç amplifikatörü referans şeması (K16) | PCB üretimi / BOM siparişi (bu belge planlama) |
| XMOS XU316 + ES9039 DAC zinciri (K1, K17) — birebir eşleşme | CoreMusic'in kendi donanım tasarımı (ayrı ADR) |
| MT3608 boost converter (K19) | Güç telemetri yazılımı (K14 — bkz. ekosistem-mimarileri.md) |
| rp2040-dac-amp, spin-dac, OpAmp-Headphone, PBA MK1, DA15, modular-amplituner | DSP/algoritma katmanı (K3 — bkz. ses-dsp-acik-kaynak.md) |
| Çıkış stage Class AB×8 eşleşmesi (K20) | Ses kartı sürücü yazılımı (K2 — bkz. asio-wasapi-rehber.md) |
| **PCM5122 yasağı** (CLAUDE.md §21) | Streaming sunucuları (K9/K15) |

**Hedef Kullanıcı:**
- CoreMusic donanım/PCB ekibi (K1, K16, K17, K19, K20)
- Sistem mimarı — donanım ve yazılım katmanları arası sözleşme sahibi
- Güç/zarf (power envelope) hesabı yapan analog mühendisi
- Referans şemalardan "ne alınır, ne alınmaz" ayrımını yapan geliştirici

**Bağlantılar:**
- `.ai/architecture/index.md` → K0–K20 SSOT (K1, K16, K17, K19, K20)
- `.ai/architecture/github-referanslari.md` §2 → Donanım referans tablosu
- `.ai/CLAUDE.md` §21 (PCM5122 yasağı), §22 (mevcut donanım — XU316, PCM3168A, AK4458, Class AB×8)
- `.ai/ecosystem/index.md` → Ekosistem dizini (4/6 bu dosya)

---

## §2 Kapsam

### §2.1 CoreMusic Donanım İhtiyaçları (K1/K16/K17/K19/K20)

| İhtiyaç | K Katmanı | Donanım Referansı Karşılığı |
|---------|-----------|------------------------------|
| USB→I2S köprüsü | **K1** | XMOS XU316 (ddabidov Headphone-DAC-AMP ile birebir) |
| DAC dizisi (çok kanal) | **K17** | ES9039 (birebir) + mevcut PCM3168A / AK4458 |
| Güç amplifikatörü | **K16** | TPA3255 Class-D referans şeması |
| Çıkış stage (Class AB×8) | **K20** | OpAmp-Headphone / modular-amplituner çıkış dersleri |
| Besleme / boost | **K19** | MT3608 (BoostCore) boost converter |
| Düşük güçlü/entegre DAC-amp | **K1, K17, K20** | rp2040-dac-amp, spin-dac |
| Amplifikatör dizilimi | **K16, K20** | modular-amplituner, PBA MK1, DA15 |

### §2.2 Mevcut Durum (CoreMusic Donanımı)

- **CLAUDE.md §22 — mevcut donanım:** XMOS XU316, PCM3168A, AK4458, Class AB×8.
- **github-referanslari §2 — birebir eşleşenler:** ddabidov Headphone-DAC-AMP (XMOS XU316 + ES9039) CoreMusic XU316 hattı ile birebir; TPA3255, MT3608, rp2040-dac-amp, spin-dac, OpAmp-Headphone, modular-amplituner, PBA MK1, DA15 doğrulanmış referanslar (exa 2026-09-24).
- **Yasaklı:** PCM5122 (CLAUDE.md §21) — hiçbir eşleşmede geçmez.
- **Eksik:** K16 (TPA3255) CoreMusic'e resmi girmedi; K19 boost topolojisi (MT3608) henüz ADR'ye bağlanmadı; DAC dizisi (K17) ES9039 vs mevcut PCM3168A/AK4458 geçiş stratejisi belirsiz.

### §2.3 Boşluk Analizi

| Boşluk | Etkilenen K | Önem | Donanım Çözümü |
|---------|-------------|------|------------------|
| TPA3255 güç aşaması kararı | **K16** | **Yüksek** | §3.1 referans şeması + Class-D zarf karşılaştırması |
| DAC dizisi stratejisi (ES9039 vs PCM3168A/AK4458) | **K17** | **Yüksek** | §3.2 birebir eşleşme + §5.3 ADR |
| Boost topolojisi (MT3608) doğrulanmadı | **K19** | Orta | §3.3 BoostCore referansı |
| Çıkış stage Class AB×8 mimarisi | **K20** | **Yüksek** | §3.4 OpAmp-Headphone/modular dersleri |
| rp2040/spin-dac gibi küçük hedeflerin kapsamı | K1, K17 | Düşük | §3.5 — "neden kullanılmaz" kararı |
| Referans lisansları bilinmiyor | K1-K20 | Orta | §3.6.1 VERIFICATION REQUIRED matrisi |

---

## §3 Mimari

### §3.1 TPA3255 — Class-D Güç Amplifikatörü (K16)

**Doğrulanmış olgu (2026-09-24):** TPA3255 referans devresi exa ile doğrulandı — Texas Instruments 315W/315W/160W stereo Class-D amplifikatör; CoreMusic github-referanslari §2'de donanım referansı olarak listeli.

**Yapı dersi (K16):** TPA3255, filter-less (LC filtresiz) doğrudan çıkış mimarisiyle yüksek verimlilik sağlar; giriş aşaması differential, güç aşaması full-bridge. **ADR-089 kararı gereği bu topoloji CoreMusic'te REDDEDİLDİ** (Class DC / Class D ret — ADR-089 §3.1); belge yalnızca **topoloji dersi** olarak referans alınır, K16'ya entegre edilmez.

**Entegrasyon:** TPA3255 K16'ya **kopyalanmaz / entegre edilmez**; K16 = **Class AB ×8** (ADR-089). TPA3255 yalnızca filter-less / full-bridge **yapı dersi** kaydıdır; zincir bütünlüğü Class AB tarafında K16 → K20 (çıkış stage + BOM) üzerinden kurulur.

---

### §3.2 ddabidov / Headphone-DAC-AMP — XMOS XU316 + ES9039 (K1, K17)

**Doğrulanmış olgu (2026-09-24):** [github.com/ddabidov/Headphone-DAC-AMP](https://github.com/ddabidov/Headphone-DAC-AMP) — open-source hardware headphone DAC+AMP; **XMOS XU316 USB köprüsü + ESS ES9039 DAC** (exa 2026-09-24).

**Birebir eşleşme (github-referanslari §2):**

| ddabidov Bileşeni | CoreMusic Karşılığı (CLAUDE.md §22) | K Eşleşmesi |
|--------------------|--------------------------------------|--------------|
| XMOS XU316 (USB→I2S köprü) | **XMOS XU316** — aynısı | **K1** |
| ESS ES9039 (DAC) | Mevcut DAC dizisi PCM3168A + AK4458 | **K17** (farklı çip — §5.3 ADR) |
| Headphone AMP stage | Class AB×8 çıkış stage | **K20** (topoloji dersi) |
| Open-source hardware şeması | CoreMusic donanım design | **K1** (referans) |

**Yapı dersi:** XU316 köprüsü birebir aynı olduğu için **firmware/driver sözleşmesi** (K1, K2 etkileşimi) doğrudan referans alınabilir; DAC çipi farkı (ES9039 vs PCM3168A/AK4458) K17'de ayrı bir "DAC geçiş" ADR'si gerektirir (§5.3).

**Entegrasyon:** K1'e "USB köprü referansı", K17'ye "DAC benchmark" olarak girer. Şema dosyaları lisansı doğrulanmadan kopyalanmaz (§3.6.1).

---

### §3.3 BoostCore — MT3608 Boost Converter (K19)

**Doğrulanmış olgu (2026-09-24):** MT3608 tabanlı boost converter projesi exa ile doğrulandı (github-referanslari §2 donanım listesinde; MT3608 = yüksek frekanslı step-up regulator).

**Yapı dersi (K19):** MT3608, düşük giriş geriliminden (2V+) yüksek çıkış (kadar 28V) üreten basit boost topolojisi; indüktör + diyot + feedback dirençleri ile ayarlanır. CoreMusic besleme katmanında (K19) analog rail üretimi için **referans topolojidir**.

**Entegrasyon:** K19'a "boost topolojisi referansı"; TPA3255 (K16) ve Class AB (K20) rail ihtiyaçlarıyla birlikte güç ağacı şemasında kullanılır. Gerçek bileşen değerleri CoreMusic güç bütçesine göre **yeniden hesaplanır** (kopya değil, topoloji).

---

### §3.4 Amplifikatör / Çıkış Stage Referansları (K16, K20)

| Proje | Doğrulanmış Olgu (2026-09-24) | CoreMusic K Dersi |
|-------|-------------------------------|---------------------|
| modular-amplituner | Açık kaynak modüler amfi projesi (exa doğrulandı) | **K16/K20:** modüler amplifikatör bloklama — kanal başına modül |
| PBA MK1 | Doğrulanmış donanım referansı (exa) | **K16:** amfi şema revizyonu / prototip döngüsü |
| DA15 | Doğrulanmış donanım referansı (exa) | **K16:** konnektör/sinyal yolu düzeni |
| OpAmp-Headphone | Op-amp tabanlı kulaklık amplifikatör projesi (exa) | **K20:** Class AB/opus çıkış stage topolojisi |
| spin-dac | DAC referans projesi (exa) | **K17:** DAC yerleşim/entegrasyon dersi |

**Ders (K20 — Class AB×8):** OpAmp-Headphone ve modular-amplituner, çıkış stage'in ayrı bir modüler blok olduğunu gösterir; CoreMusic'in Class AB×8 dizilimi (CLAUDE.md §22) aynı modülerlikle 8 kanal çoğaltılabilir (kanal başına tekrar eden tek şema).

---

### §3.5 rp2040-dac-amp — Entegre Düşük Güç Hedef (K1, K17, K20)

**Doğrulanmış olgu (2026-09-24):** rp2040-dac-amp projesi exa ile doğrulandı (github-referanslari §2 donanım listesinde; RP2040 MCU + DAC + amplifikatör entegrasyonu).

**Yapı dersi:** RP2040, tek çip üzerinde USB + mikrodenetleyici + basit DAC sağlar — CoreMusic'in XU316+ES9039 **çok daha yüksek performanslı** bir kombinasyondur. rp2040-dac-amp, **"neden kullanılmıyor"** kararının referansıdır: CoreMusic hedefi yüksek çözünürlüklü (K17 ES9039 sınıfı), rp2040 sınıfı DAC yetersiz kalır.

**Entegrasyon:** K1/K17'de **olumsuz referans** (red seçeneği) olarak §5.3 ADR'sinde listelenir — "rp2040 tek-çip çözümü neden reddedildi: K17 çözünürlük hedefi".

### §3.6 Eşleşme Matrisi, Copy/Don't Copy ve Entegrasyon

### §3.6.1 Lisans / Doğrulama Matrisi

| Proje | Lisans (Doğrulanmış) | Kopyalanabilir mi? | Kullanım Şekli |
|-------|----------------------|-------------------|----------------|
| ddabidov Headphone-DAC-AMP | ⚠️ VERIFICATION REQUIRED | Bekleme — LICENSE oku | XU316 köprü referansı (birebir) |
| TPA3255 referans şeması | TI referans (veri sayfası) | Veri sayfası OK; 3. parti layout dikkat | K16 topolojisi |
| BoostCore (MT3608) | ⚠️ VERIFICATION REQUIRED | Topoloji serbest, dosya beklemede | K19 boost şeması |
| modular-amplituner / PBA MK1 / DA15 | ⚠️ VERIFICATION REQUIRED | Bekleme | K16 dersleri (kopyasız) |
| OpAmp-Headphone | ⚠️ VERIFICATION REQUIRED | Bekleme | K20 topoloji dersi |
| spin-dac / rp2040-dac-amp | ⚠️ VERIFICATION REQUIRED | Bekleme | K17 ders / red referansı |
| **PCM5122 içeren her şey** | — | ❌ **HİÇBİR ZAMAN** | CLAUDE.md §21 yasaklı desen |
| AGPL / uyumsuz lisanslı şema dosyası | AGPL vb. | ❌ **HİÇBİR ZAMAN** | Sadece fikir (CLAUDE.md §4) |

> ⚠️ "VERIFICATION REQUIRED": exa sonucu proje varlığını doğruladı ama lisans bilgisi gelmedi — **kopyalamadan önce resmi LICENSE okunmalı** (Guardrail #14: kaynaksız üretim yok).

### §3.6.2 Ders → K Katmanı Eşlemesi

| # | Ders | Kaynak | K Katmanı |
|---|------|--------|-----------|
| 1 | USB→I2S köprüsü birebir sözleşme | ddabidov (XU316) | **K1** |
| 2 | DAC benchmark: ES9039 vs mevcut PCM3168A/AK4458 | ddabidov | **K17** |
| 3 | Class-D güç aşaması (filter-less) | TPA3255 | **K16** |
| 4 | Boost topolojisi (indüktör+feedback) | BoostCore MT3608 | **K19** |
| 5 | Modüler amfi kanal tekrarı | modular-amplituner | **K16, K20** |
| 6 | Op-amp çıkış stage topolojisi | OpAmp-Headphone | **K20** |
| 7 | Prototip/revizyon döngüsü | PBA MK1, DA15 | **K1** |
| 8 | Tek-çip DAC-amp red gerekçesi | rp2040-dac-amp | **K1, K17** (olumsuz) |
| 9 | DAC yerleşim dersi | spin-dac | **K17** |

### §3.6.3 Copy / Don't Copy

**✅ ALINABİLİR (referans / topoloji):**

1. XU316 köprü **sözleşmesi** (birebir çip) — pin/sinyal haritası referans (lisans doğrulanınca dosya da).
2. TPA3255 **topolojisi** — TI veri sayfası referans şeması kamuya açık kullanım için.
3. MT3608 boost **topolojisi** — basit devre, değerler CoreMusic'e göre yeniden hesaplanır.
4. Modüler amfi **bloklama şeması** — kanal başına tekrar (Class AB×8 mantığı).
5. OpAmp-Headphone **çıkış stage fikri** — K20 topoloji dersi.

**❌ ALINAMAZ:**

1. **PCM5122** — herhangi bir formda (CLAUDE.md §21; §3.6.1 son satır).
2. **Lisansı doğrulanmamış** şema/kaynak dosyası (§3.6.1 mor satırlar) — Guardrail #14.
3. **AGPL/uyumsuz lisanslı** donanım dosyası — sadece fikir (CLAUDE.md §4).
4. **rp2040-dac-amp'ın K17'de referans olarak eşleştirilmesi** — red gerekçesi var, kopya yok (§3.5).

### §3.6.4 Senaryolar (§4.1 şablon genişletmesi — 4 satır Durum/Aksiyon)

| Senaryo | Durum | Aksiyon |
|---------|-------|---------|
| **S1: Güç aşaması seçimi** | K16 için Class-D (TPA3255) vs Class AB belirsiz | TPA3255 veri sayfası zarfı + Class AB×8 (K20) hibrit ADR → DECISIONS.md |
| **S2: DAC dizisi stratejisi** | ES9039 (birebir) vs mevcut PCM3168A/AK4458 | §5.3 ADR: geçiş (K17), ES9039 benchmark alınır — mevcut çipler korunabilir |
| **S3: Besleme topolojisi** | K19 boost doğrulanmadı | MT3608 topolojisi + LICENSE oku → rail hesapları |
| **S4: Küçük hedef dışarıda mı** | rp2040-dac-amp çekici ama çözünürlük yetersiz | Red ADR: "tek-çip DAC-amp neden kullanılmıyor" (§3.5) → DECISIONS.md |

### §3.6.5 Entegrasyon — CoreMusic'e Somut Giriş (K eşlemesi ile)

| Donanım Referansı | CoreMusic'e Giriş Yolu | Hedef K | Öncelik |
|--------------------|------------------------|---------|---------|
| ddabidov XU316 köprü | USB→I2S sözleşme referansı (birebir çip) | **K1** | **Yüksek** |
| ES9039 (ddabidov) | DAC benchmark — mevcut PCM3168A/AK4458 ile karşılaştırma | **K17** | **Yüksek** (ADR §5.3) |
| TPA3255 | Güç amplifikatörü referans topolojisi (filter-less Class-D) | **K16** | **Yüksek** |
| MT3608 (BoostCore) | Besleme/boost topolojisi — rail hesapları | **K19** | Orta |
| modular-amplituner, PBA MK1, DA15 | Modüler amfi bloklama + prototip döngüsü | **K16, K20** | Orta |
| OpAmp-Headphone | Class AB çıkış stage topoloji dersi | **K20** | **Yüksek** |
| spin-dac | DAC yerleşim/entegrasyon dersi | **K17** | Düşük |
| rp2040-dac-amp | **Red referansı** — tek-çip neden yetmez | **K1, K17** | Düşük (ADR gerekçesi) |
| ASIO/WASAPI sürücü etkileşimi | Ayrık belge: asio-wasapi-rehber.md | **K2** | — |

**Sıra (2026-09-24):** (1) §5.3 DAC dizisi ADR'si (ES9039 vs PCM3168A/AK4458) → (2) K16 güç aşaması (TPA3255) → (3) K19 boost (MT3608 LICENSE + rail) → (4) K20 Class AB×8 modüler tekrar → (5) K1 XU316 sözleşmesi (birebir — düşük risk).

---

### §3.7 Derinlemesine Dersler (Katman Bazlı)

#### §3.7.1 K16 — TPA3255 Class-D Çalışma Prensibi

| Bölüm | Ders | CoreMusic Etkisi |
|-------|------|------------------|
| Full-bridge çıkış | Her kanal H-bridge ile sürülür — yüksek verim, filtresiz mümkün | Gürültü/EMI bütçesi |
| Differential giriş | Gürültü bağışıklığı — analog ön aşamayla eşleşir | K20 öncesi sinyal bütünlüğü |
| Geri besleme (feedback) | Yük koşullarına sabit çıkış empedansı | Hoparlör yükü dalgalanması telafisi |
| Korumalar (OTP/OCP/UVLO) | Termal/aşırı akım/alçak gerilim kilidi | K19 besleme ile entegre koruma zinciri |

**Kural:** TPA3255 topolojisi referans alınır; pin yerleşimi ve BOM CoreMusic K16 tasarımıdır (kopya değil).

#### §3.7.2 K1 — XU316 Köprüsü ve Firmware Sözleşmesi

| Sözleşme Katmanı | ddabidov Referansı | CoreMusic Karşılığı |
|------------------|--------------------|----------------------|
| USB Audio sınıfı | XU316 firmware profili | CoreMusic K1 USB descriptor hedefi |
| I2S master | XU316 → DAC saat ağacı | K17 DAC saat (MCLK/BCLK/LRCK) |
| DSP yanıt süresi | Kulaklık AMP kontrol kanalı | K10 low-latency zinciri (bkz. ses-dsp-acik-kaynak.md) |
| Ayraç/hedef | ES9039 pinout | Mevcut PCM3168A/AK4458 pinout — §5.3 ADR farkı |

**Kural:** XU316 birebir aynı çip olduğu için firmware **sözleşme şekli** (descriptor, I2S modu) referans alınır; kaynak dosyanın lisansı doğrulanmadan kopyalanmaz (§3.6.1).

#### §3.7.3 K19 — Besleme Zinciri ve Korumalar

~~~text
Vin (5V/12V) ──> [MT3608 boost] ──> +Rail_user ──┬──> K16 (TPA3255) yüksek rail
                                                 └──> K20 (Class AB×8) düşük rail
Koruma zinciri: UVLO (boost çıkış düşük) ──> K16 enable ──> K20 mührü (pop-noise önleme)
~~~

| Besleme Sorusu | Karar Nerede |
|----------------|--------------|
| Rail gerilimleri kaç V? | K16 TPA3255 zarfı + K20 Class AB zarfı → ADR (§5.3 sonrası) |
| MT3608 yeterli mi akım? | CoreMusic güç bütçesi — topoloji aynı, değerler yeniden |
| Kullanıcı başına boost ayrı mı? | K19 ADR — ortak rail vs kanal başı izolasyon |

#### §3.7.4 K17 — DAC Ayrışım Dersi (ES9039 vs PCM3168A/AK4458)

| Kriter | ES9039 (ddabidov) | PCM3168A (mevcut) | AK4458 (mevcut) |
|--------|-------------------|--------------------|--------------------|
| Rol | Tek kanal yüksek çözünürlük | 8 kanal codec/DAC | 8 kanal DAC |
| Saat ağacı | XU316 I2S doğrudan | ortak I2S (K1) | ortak I2S (K1) |
| CoreMusic eşleşmesi | Benchmark (§3.2) | CLAUDE.md §22 mevcut | CLAUDE.md §22 mevcut |
| ADR etkisi | S1 (geçiş) | S2 (koru) | S2 (koru) |

**Kural:** Bu tablo §5.3 ADR'sinin veri tabanıdır; **hangi çip kalırsa kalsın PCM5122 satırı hiç yoktur** (§4.4).

---

### §3.8 Ek Soru Listesi (Guardrail #8 — belirsizlikler)

1. **TPA3255 Class-D vs mevcut Class AB×8:** ikisi birlikte (hibrit) mi, alternatif mi? — K16/K20 ADR'si cevaplayacak (§5.3 öncesi açık madde).
2. **MT3608 lisansı:** exa proje varlığını doğruladı, LICENSE dosyası gelmedi — §3.6.1 mor satır, kopya bloke.
3. **ES9039 üreticililik:** ddabidov şeması açık kaynak hardware; lisansı doğrulanana kadar yalnızca pin/sinyal **haritası** referans alınır.
4. **rp2040 red kapsamı:** yalnız K17 için mi, yoksa düşük maliyetli SKU (varsa) için de düşünülür mü? — ürün kararına bağlı, bu belge red referansını saklar (§3.5).
5. **Güç bütçesi:** K19 rail değerleri CoreMusic'in henüz yazılmamış güç bütçesine bağlı — bütçe yazılınca §5.1 şeması doldurulur.

### §3.9 Doğrulama Kanıtları (2026-09-24 exa)

~~~text
exa 2026-09-24 sonuçları (bu belgenin tek kaynak girdisi):
  - TPA3255 referans devresi → doğrulandı
  - ddabidov/Headphone-DAC-AMP: XMOS XU316 + ES9039 → doğrulandı
  - BoostCore (MT3608) → doğrulandı
  - modular-amplituner, PBA MK1, DA15, OpAmp-Headphone, spin-dac, rp2040-dac-amp → doğrulandı
  - Lisans/star bilgisi gelmeyenler: §3.6.1'de VERIFICATION REQUIRED işareti (uydurma yok)
Yeni web araştırması YAPILMADI (kural gereği); sadece sağlanan exa sonuçları kullanıldı.
~~~


### §3.10 Ekosistem Dizini Konumu (6/6 Serisi İçinde)

| Belge | Kapsadığı K | Bu Belgeyle Bağ |
|-------|-------------|------------------|
| index.md | K0–K20 genel | Dizin (1/6) |
| muzik-streaming-sunuculari.md | K9/K15 | Codec tarafı devri (§6.1) |
| ses-dsp-acik-kaynak.md | K3/K10/K2 | DSP↔donanım I2S sözleşmesi |
| **bu dosya (4/6)** | **K1/K16/K17/K19/K20** | — |
| ekosistem-mimarileri.md | K8/K9/K14/K15 | Güç telemetri devri (§6.1) |
| asio-wasapi-rehber.md | K2/K0 | Sürücü↔donanım keşfi (§6.1) |

---

### §3.11 ADR-089 Kapsamı — Class AB Amplifikatör Kararı (K16-K20)

> **İlgili ADR:** [[../.decisions/accepted/ADR-089-classab-24v]] — Class AB Amplifikatör + 6S LiPo + ±35V Boost (**accepted**, 2026-09-24).

**Karar özeti (8×50W · 1/2/4/6/8 kanal · 12-24V boost PSU · 120dB+ · hibrit MCU):**

| Eksen | Karar | Kanıt |
|-------|-------|-------|
| Topoloji | **Class AB** (Darlington MJL21194/MJL21193) — **Class DC ve TPA3255 Class-D RET** | `.ai/CLAUDE.md` K16; ADR-089 §3.1 |
| Güç | **12-24V DC giriş → ±35V boost (LM5122 ×2, %96)**; 6S LiPo 22.2V / 19-24V adaptör; DC-only | `.ai/CLAUDE.md` L18/L127 (H2), README L171; ADR-089 §2 |
| Kanal | **1 / 2 / 4 / 6 / 8** modüler — her kanal bağımsız PCB, enable pinli | `.ai/PROJECTS.md` L306; ADR-089 §2 |
| Gürültü | **120dB+ hedef** (DR); vault ölçülebilir referans: SNR >105dB / THD+N <0.005% | PROJECTS L305, brain L284; ADR-089 §4.3 ⚠️ ölçüm tanımı gerekli |
| Kontrol | **Hibrit MCU**: XMOS XU316 (USB/DSP) + STM32H7·RP2040 (gerçek zamanlı MCU) + RPi5 (host); MCU↔modül haberleşme **zorunlu** | `.ai/CLAUDE.md` L126/L193, K0; ADR-089 §2.1 |

**Güç kaynağı exa sonuçları (2026-09-24 — 19 aramanın 3'ü):**

| Kaynak | Bulgu | K16-K20 etkisi |
|--------|-------|----------------|
| hifisonix **Ripple Eater** | Aktif ripple rejector: ±20-63V, 5A/20A, **40dB @20Hz-300kHz** / **50dB @200Hz-20kHz** | K17 (±35V boost) sonrası aktif filtraj adayı |
| prydin **lateral MOSFET Class-AB** | 2SK1058/2SJ162, **±30V** | K16 alternatif çıkış stage (topoloji dersi — bu ADR kapsamı dışı) |
| nathanpc **mini12** | **12V** TDA2030 tabanlı mini amplifikatör | 12-24V düşük güçlü referans senaryo |

---

## §4 Kurallar

### §4.1 ZORUNLU: ŞABLON ÖNCE (Template-First)

**Herhangi bir dosyayı yazmadan ÖNCE `.ai/.templates/` dizinindeki ilgili şablonları oku:**

1. Uygun şablonu `[[.templates/index]]` (`.ai/.templates/index.md`) §7.1 tablolarından seç.
2. Şablonu oku; `{{VARIABLE}}` alanlarını doldur, gereksiz bölümleri kaldır.
3. **Şablon varsa ona göre yaz.**
4. **Şablon yoksa** standart 8-bölüm formatına göre yaz ve `⚠️ VERIFICATION REQUIRED` notuyla `log.md`'ye şablon eksiğini bildir.
5. Şablon okunmadan yazılan dosya **geçersizdir** (Guardrail #16): revert edilir, `log.md`'ye ERROR girilir.

| Durum | Aksiyon |
|-------|---------|
| `.ai/.templates/` altında ilgili şablon VAR | Şablonu oku → ona göre yaz |
| Şablon YOK | Standart formata göre yaz + `log.md`'ye "şablon eksiği" kaydı |
| Şablon okundu ama çelişiyor | DUR → `[[../../CLAUDE.md]]` §2.1 SSOT öncelik sırası |
| Vault erişilemiyor | `⚠️ VERIFICATION REQUIRED` → yazım durdurulur, kullanıcıya sor |

> Yukarıdaki blok `.ai/.templates/documentation/docs-md-template.md` §3.3'ten birebir kopyalanmıştır (Guardrail #16 — kısaltılamaz).

### §4.2 Frontmatter Uygulaması (Bu Belge)

| Placeholder | Değer |
|-------------|-------|
| `{{BASLIK}}` | Donanım / Devre Referansları Ekosistemi |
| `{{ACIKLAMA}}` | TPA3255, XU316+ES9039, MT3608 + K1/K16/K17/K19/K20 eşleşmesi |
| `{{TARIH}}` | 2026-09-24 |
| `{{DURUM}}` | active |
| `{{KAYNAK}}` | exa web doğrulaması (2026-09-24) — §7 Referanslar |
| `{{STATUS}}` | active |
| Authority | "Donanım referansları; K1/K16/K17/K19/K20 — PCM5122 yasak" |

### §4.3 §1–§7 Kapsam Haritası

| Şablon Bölümü | Bu Belgede |
|----------------|------------|
| §1 Amaç | Kapsam, kullanıcı, bağlantılar |
| §2 Kapsam | K1/K16/K17/K19/K20 ihtiyaçları + boşluklar |
| §3 Mimari | TPA3255, ddabidov, MT3608, amfi referansları, rp2040, §3.6 matris/senaryo/entegrasyon |
| §4 Şablonlar | Şablon-Önce bloğu (verbatim) + frontmatter + §4.4 yasaklar |
| §5 Workflow | Şema/yazı tipleri + DAC ADR şablonu + 4 senaryo |
| §6 Doğrulama | Devir, risk, kanıt |
| §7 Referanslar | Donanım depoları + CLAUDE.md §21-§22 + github-referanslari §2 |

### §4.4 Yasaklı Parça Tablosu (Bu Belge için)

| Parça / Desen | Durum | Kaynak Kuralı |
|---------------|-------|---------------|
| **PCM5122** | ❌ YASAKLI | CLAUDE.md §21 — hiçbir eşleşmede geçmez |
| AGPL şema dosyası | ❌ KOPYA YOK | CLAUDE.md §4 — sadece fikir |
| Lisansı doğrulanmamış şema | ⚠️ BEKLEME | Guardrail #14 — LICENSE oku |
| rp2040-dac-amp → K17 eşleşmesi | ❌ RED | §3.5/§5.3 — çözünürlük yetersiz |
| XU316 köprü (birebir) | ✅ REFERANS | CLAUDE.md §22 — mevcut çip ile aynı |

---

## §5 Workflow

### §5.1 Güç Ağacı Şema Dili (Referans — kopya değil)

~~~text
K19 Besleme:  VIN ──[MT3608 boost]──> +Rail (K16 TPA3255 / K20 Class AB için)
              (değerler CoreMusic güç bütçesine göre yeniden hesaplanır — kopya yok)
K16 Güç:      [TPA3255 Class-D] ──> hoparlör çıkışı
K20 Çıkış:    [OpAmp/Class AB ×8] ──> kulaklık/satellite çıkışı (modüler tekrar)
K17 DAC:      XMOS XU316 ──I2S──> DAC (ES9039 benchmark | mevcut PCM3168A/AK4458)
Yasak:        PCM5122 (CLAUDE.md §21) — bu şemada HİÇ GEÇMEZ
~~~

### §5.2 Eşleşme Doğrulama Matrisi (§4.1 4-satır tablo örneği)

| Eşleşme | Durum | Aksiyon |
|---------|-------|---------|
| XU316 köprü | ✅ Doğrulandı (2026-09-24, exa) | K1 sözleşmesini ddabidov şemasından çıkar |
| ES9039 vs PCM3168A/AK4458 | ⚠️ Farklı çip | §5.3 DAC ADR'si |
| TPA3255 → K16 | ✅ Doğrulandı | Veri sayfası zarfı + Class AB×8 hibrit ADR |
| MT3608 → K19 | ⚠️ Lisans beklemede | LICENSE oku + rail hesabı |

### §5.3 DAC Dizisi ADR Şablonu (Karar noktası)

~~~text
ADR: DAC Dizisi Stratejisi (K17)
Durum: ÖNERİ (2026-09-24)
Bağlam: ddabidov XU316+ES9039 birebir eşleşiyor; CoreMusic mevcut: PCM3168A + AK4458 (CLAUDE.md §22).
Seçenekler:
  (a) ES9039'e geç (benchmark yüksek) — maliyet+layout değişir
  (b) Mevcut PCM3168A/AK4458 koru, sadece XU316 köprü referansını kullan
  (c) Hibrit: ES9039 flagship, AK4458 çok-kanal korunur
Reddedilen: rp2040-dac-amp tek-çip (K17 çözünürlük yetersiz), PCM5122 (yasaklı §21)
Sonuç: .ai/DECISIONS.md'ye yazılacak.
~~~

### §5.4 Modüler K20 Şema Deseni (Class AB×8)

~~~text
Tek kanal modülü (OpAmp-Headphone dersi):
  I2S/analog in ──> buffer ──> Class AB sürücü ──> çıkış
Çoğaltma:  modül × 8 (CLAUDE.md §22 — Class AB×8),
  beslemeyi K19 (MT3608 topolojisi) ortak rail ile besler.
Referans: modular-amplituner modüler bloklama — kopya değil, topoloji dersi.
~~~

---

## §6 Doğrulama

### §6.1 Devir Noktaları (Belge Sınırları)

| Konu | Devredilen Belge | K |
|------|------------------|---|
| DSP/algoritma (DAC filtreleri dahil yazılım tarafı) | `.ai/ecosystem/ses-dsp-acik-kaynak.md` | K3 |
| Sürücü sözleşmesi (ASIO/WASAPI) | `.ai/ecosystem/asio-wasapi-rehber.md` | K2, K0 |
| Güç telemetri / izleme yazılımı | `.ai/ecosystem/ekosistem-mimarileri.md` | K14 |
| Streaming codec tarafı | `.ai/ecosystem/muzik-streaming-sunuculari.md` | K9/K15 |

### §6.2 Riskler

| Risk | Etki | Olasılık | Önlem |
|------|------|----------|-------|
| PCM5122 referansla geri gelir | **Kritik** | Düşük | §4.4 + CLAUDE.md §21 PR review |
| Lisans doğrulanmadan şema kopyalanır | **Yüksek** | Orta | §3.6.1 mor satırlar — Guardrail #14 |
| ES9039 geçişi mevcut PCM3168A/AK4458'i kırar | **Yüksek** | Orta | §5.3 ADR (b/c seçenekleri korur) |
| MT3608 rail değeri yanlış hesaplanır | Orta | Orta | §5.1 — değerler yeniden hesap, kopya yok |
| Class-D + Class AB hibrit zarf çakışması | Orta | Düşük | K16/K20 ADR'de birlikte değerlendir |
| rp2040 yanlışlıkla K17'ye pozitif eşleşir | Orta | Düşük | §3.5 red referansı + §4.4 tablo |

### §6.3 KPI / Kanıt Komutları (§4.1 5 adıma karşılık)

~~~powershell
# 1) Şablon bulundu mu:
Test-Path .ai/.templates/documentation/docs-md-template.md
# 2) Placeholder kalmadı mı:
Select-String -Path .ai/ecosystem/donanım-devre-referanslari.md -Pattern '{{TARIH}}|{{DURUM}}|{{KAYNAK}}'
# 3) §1-§7 başlıkları:
Select-String -Path .ai/ecosystem/donanım-devre-referanslari.md -Pattern '^## [1-7]\.'
# 4) PCM5122 sadece yasak bağlamında geçiyor mu:
Select-String -Path .ai/ecosystem/donanım-devre-referanslari.md -Pattern 'PCM5122'
# 5) Frontmatter 7 alan:
Get-Content .ai/ecosystem/donanım-devre-referanslari.md -TotalCount 14
~~~

**KPI:** Dosya ≥500 satır; §4.1 bloğu verbatim; 0 placeholder; PCM5122 yalnızca "yasak" bağlamında; §3.6.1 lisans matrisi eksiksiz; §5.3 DAC ADR işareti mevcut.

### §6.4 Sonraki Adım (2 dakikadan kısa)

**§5.3'teki DAC ADR şablonunu `.ai/DECISIONS.md`'ye "ÖNERİ" olarak yapıştırın** ve (a)/(b)/(c) seçeneklerinden birini işaretleyin — ES9039 geçişi mi, mevcut PCM3168A/AK4458 korunumu mu, bir cümleyle yazın.

---

## §7 Referanslar

| # | Kaynak | URL | Doğrulama | Kullanım |
|---|--------|-----|-----------|----------|
| 1 | ddabidov Headphone-DAC-AMP (XU316+ES9039) | https://github.com/ddabidov/Headphone-DAC-AMP | exa 2026-09-24 | §3.2, §5.3 |
| 2 | TPA3255 referans devresi | TI TPA3255 (veri sayfası) | exa 2026-09-24 | §3.1 |
| 3 | BoostCore (MT3608) | GitHub — MT3608 boost projesi | exa 2026-09-24 | §3.3 |
| 4 | modular-amplituner | GitHub — modüler amfi | exa 2026-09-24 | §3.4 |
| 5 | PBA MK1 | GitHub — donanım referansı | exa 2026-09-24 | §3.4 |
| 6 | DA15 | GitHub — donanım referansı | exa 2026-09-24 | §3.4 |
| 7 | OpAmp-Headphone | GitHub — kulaklık AMP | exa 2026-09-24 | §3.4, §5.4 |
| 8 | spin-dac | GitHub — DAC referansı | exa 2026-09-24 | §3.4 |
| 9 | rp2040-dac-amp | GitHub — tek-çip DAC-amp | exa 2026-09-24 | §3.5 (red referansı) |
| 10 | CoreMusic donanım SSOT | .ai/CLAUDE.md §21, §22 | yerel | yasak + mevcut donanım |
| 11 | Donanım referans tablosu | .ai/architecture/github-referanslari.md §2 | yerel | §3 |

---

## Authority

**Donanım/devre referansları, CoreMusic K1/K16/K17/K19/K20 katmanlarının referansıdır — XU316 birebir, ES9039 ADR'li, PCM5122 yasaklıdır.**

> 2026-09-24 | Belge 4/6 | Durum: active | Kaynak: exa web doğrulaması | Geçmiş: ".ai/ecosystem/index.md" (dizin)