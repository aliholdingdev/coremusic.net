---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — Hardware Design Template"
type: hardware-template
category: template
version: 2.0.0
status: active
authority: "Template (Guardrail #16) — Registry: .ai/.templates/index.md"
updated: 2026-09-23
date: 2026-09-23
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/brain.md"
---

# CoreMusic — Hardware Design Template

**Zorunlu Bağlantılar / See also:** [[.templates/index]] · [[../CLAUDE.md]] · [[../../AGENTS.md]]

## 1. Amaç

CoreMusic donanım tasarım dokümanını standartlaştırmaktır: bileşen seçimini, devre şeması notlarını (güç kaynağı, sinyal zinciri, amplifikatör), PCB tasarım kurallarını, BOM maliyet analizini, test protokolünü ve hard guardrail'ları tek iskelette toplar. Kaynak: `reference_doc: Freelancer Technical Documentation v1.0`.

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| DAC/ADC, amplifikatör, güç kaynağı, PCB tasarım dokümanı | Yazılım kaynak kodu (`.php`, `.js`) |
| Bileşen seçim tablosu, BOM, pinout/kanal eşlemesi, test protokolü | CI/CD pipeline (bkz. github-actions-template) |
| K1 (Donanım) katmanı / K16-K20 | Migration/DB (bkz. migration-template) |

- **Dosya tipi:** Markdown donanım tasarım dokümanı
- **Kullanan agent:** Audio Hardware Engineer (sorumlu agent · dosya başlığı), Embedded Engineer / DSP Firmware Engineer (ikincil · AGENTS.md §6)
- **Katman:** K1 (Donanım) / K16-K20 · **Guardrail:** #16 (Template Mandatory)

## 3. Mimari

Şablonun tam gövdesi. Not: gömme nedeniyle şablon başlıkları iki seviye derinleştirilmiştir (H1 → `###`, H2 → `####`, H3 → `#####`); tüm `{{PLACEHOLDER}}`, ASCII devre blokları ve tablolar birebir korunmuştur. Hard Guardrail'lar §4.1'dedir.

### {{TITLE}}

**Kategori:** {{HARDWARE_CATEGORY}}
**Katman:** K1 (Donanım) / K16-K20
**Sorumlu Agent:** Audio Hardware Engineer

---

#### 3.1 Bileşen Seçim Tablosu

| Bileşen | Model | Özellik | Neden |
|---------|-------|---------|-------|
| USB Audio | XMOS XU316 | USB Audio Class 2.0 | Hi-Res destek |
| DAC | PCM3168A | 6-in/8-out, 24-bit | 8.1 surround |
| DAC (opsiyonel) | AK4458 | 8-kanal, 32-bit | High-end |
| Amplifikatör | MJL21194/MJL21193 | Class AB Darlington | 50W/kanal |
| Boost | LM5122 | ±35V boost converter | Güç kaynağı |
| Batarya | 6S LiPo | 22.2V nominal | Taşınabilir |

---

#### 3.2 Devre Şeması Notları

##### 3.2.1 Güç Kaynağı

```
6S LiPo (22.2V) → LM5122 Boost → ±35V Simetrik
                                    ├→ +35V → Class AB (NPN tarafı)
                                    └→ -35V → Class AB (PNP tarafı)
```

##### 3.2.2 Sinyal Zinciri

```
USB → XMOS XU316 → I2S → PCM3168A → Analog Out → Class AB → Hoparlör
                                                         
PCM3168A Kanalları:
  CH1: Front Left    → Amp 1 → Hoparlör 1
  CH2: Front Right   → Amp 2 → Hoparlör 2
  CH3: Center        → Amp 3 → Hoparlör 3
  CH4: LFE (Sub)     → Amp 4 → Subwoofer
  CH5: Surround Left → Amp 5 → Hoparlör 5
  CH6: Surround Right→ Amp 6 → Hoparlör 6
  CH7: Rear Left     → Amp 7 → Hoparlör 7
  CH8: Rear Right    → Amp 8 → Hoparlör 8
```

##### 3.2.3 Amplifikatör Devresi (Tek Kanal)

```
                    +35V
                     │
                ┌────┴────┐
                │  MJL21194│ (NPN Output)
                │  (NPN)   │
     Input ─────┤         ├──── Output → Hoparlör
                │  MJL21193│
                │  (PNP)   │
                └────┬────┘
                     │
                    -35V

THD+N < 0.005% @ 1W
Güç: 50W/kanal @ 8Ω
```

---

#### 3.3 PCB Tasarım Kuralları

| Parametre | Değer |
|-----------|-------|
| Layer | 6-layer stackup |
| Copper | 2oz (top/bottom), 1oz (inner) |
| Finish | ENIG |
| Impedans | 90Ω USB, 50Ω I2S |
| Thermal | Thermal vias under power components |
| Ground | Star ground topology |
| Size | 200×100mm (max) |

---

#### 3.4 BOM Maliyet Analizi

| Kategori | Bileşen | Adet | Birim Fiyat | Toplam |
|----------|---------|------|------------|--------|
| DAC | PCM3168A | 1 | $8.50 | $8.50 |
| USB | XMOS XU316 | 1 | $12.00 | $12.00 |
| Amp (kanal) | MJL21194 | 8 | $3.50 | $28.00 |
| Amp (kanal) | MJL21193 | 8 | $3.50 | $28.00 |
| Boost | LM5122 | 2 | $4.50 | $9.00 |
| Pasif | Çeşitli | ~200 | ~$0.10 | ~$20.00 |
| PCB | 6-layer | 1 | $50.00 | $50.00 |
| **TOPLAM** | | | | **~$155** |

---

#### 3.5 Test Protokolü

| Test | Yöntem | Kriter |
|------|--------|--------|
| Güç | Multimetre | ±35V ±%5 |
| THD | Audio Analyzer | <0.005% @ 1W |
| SNR | Audio Analyzer | >100dB |
| Frekans | Sine sweep | 20Hz-20kHz ±0.5dB |
| Termal | Termal kamera | <60°C @ full power |
| DC Offset | Multimetre | <0.5V DC |

---

## 4. Kurallar

Zorunlu / yasak kurallar (Hard Guardrails + kod standartları):

#### 4.1 Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | PCM5122 ile 8.1 surround YAPILAMAZ (ADR-038) | Yanlış donanım |
| 2 | DC-Only güç kaynağı zorunlu | Sistem hatası |
| 3 | Class AB amplifikatör zorunlu (ADR-089) | Yanlış topoloji |
| 4 | 6S LiPo (22.2V) veya 19-24V DC adapter | Güç hatası |
| 5 | Impedans eşleştirme zorunlu | Sinyal kaybı |
| 6 | Thermal hesaplama zorunlu | Aşırı ısınma |

Ek kurallar:

- **Zorunlu:** PCM5122 reddedilmiştir; alternatif olarak PCM3168A veya AK4458 önerilir (AGENTS.md §17, ADR-038).
- **Zorunlu:** §3.3 PCB parametreleri (90Ω USB, 50Ω I2S, star ground, thermal vias) tasarımında birebir uygulanır.
- **Zorunlu:** §3.5 Test Protokolü'nün tüm satırları geçmeden tasarım onaylanamaz.
- **Yasak:** `{{TITLE}}`/`{{HARDWARE_CATEGORY}}` placeholder'ları doldurulmadan doküman commit edilemez.
- **Uyarı:** doğrulanamayan bileşen değeri (fiyat, tolerans) `⚠️ VERIFICATION REQUIRED` ile işaretlenir.

## 5. Workflow

```
ŞABLONU SEÇ → KOPYALA → {{PLACEHOLDER}} DOLDUR → GUARDRAIL #16 DOĞRULA → COMMIT
```

1. **ŞABLONU SEÇ:** `.ai/.templates/hardware/hardware-template.md` (Guardrail #16).
2. **KOPYALA:** dosyayı `electronic/` altındaki ilgili tasarım konumuna kopyala.
3. **{{PLACEHOLDER}} DOLDUR:** `{{TITLE}}`, `{{HARDWARE_CATEGORY}}`; §3.1 bileşen tablosunu gerçek seçimle güncelle; §3.4 BOM fiyatlarını güncelle.
4. **GUARDRAIL #16 DOĞRULA:** 7 alanlı frontmatter + §1-§7 + tüm placeholder'lar doldu + §4.1 Hard Guardrails ihlali yok (ADR-038, ADR-089).
5. **COMMIT:** dokümanı commit et; topoloji/güç kaynağı değişikliği varsa ADR-038/ADR-089'u referans ver, `log.md`'ye giriş ekle.

## 6. Doğrulama

- [ ] 7 alanlı frontmatter var (title, type, category, version, status, authority, updated)
- [ ] §1-§7 var
- [ ] tüm {{PLACEHOLDER}}'lar dolduruldu
- [ ] dosya bu şablona uygun
- [ ] §4.1 Hard Guardrails ihlalsiz, §3.3 PCB + §3.5 test kriterleri tanımlı

**REFACTOR REPORT:** FILE: hardware-template.md · PURPOSE: Hardware Design Template · VALIDATION: 7 alan + §1-§7 + bilgi korunumu · RELATED: [[.templates/index]] · [[../CLAUDE.md]]

## 7. Referanslar

- [[.templates/index]] — şablon registry (`.ai/.templates/index.md`)
- [[../CLAUDE.md]] — AI anayasası, 16 Hard Guardrail
- [[../../AGENTS.md]] — routing (§6: hardware → Audio Hardware Engineer / Embedded Engineer), edge case §17.8 (PCM5122 → PCM3168A/AK4458)
- ADR-038 (PCM5122 reddedildi · PCM3168A), ADR-089 (Class AB amplifikatör) — §4.1 içinde referanslanır
- `.ai/CLAUDE.md` · `.ai/AGENTS.md` · `.ai/brain.md` (frontmatter `reference`)

---

*Hardware Design Template v2.0.0 — CoreMusic Hardware Standards*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: {{DATE}}*
*Mode: Red Team · Human Mode · Truth Mode*
