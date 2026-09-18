---
title: "ADR-089: Class AB Amplifikatör + 6S LiPo + ±35V Boost Mimarisi"
type: adr
category: electronics
date: 2026-09-18
updated: 2026-09-18
status: draft
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# ADR-089: Class AB Amplifikatör + 6S LiPo + ±35V Boost Mimarisi

## Durum

Draft

## Tarih

2026-09-18

## Bağlam

CoreMusic projesi için Class AB amplifikatör sistemi tasarımı. Yüksek ses kalitesi, doğal bozulma profili, "sıcak" ses hedefi. Mevcut amplifier mimarisi (ADR-061, ADR-064) altında-derived bir karar olarak, spesifik Class AB topoloji, output transistör seçimi ve güç kaynağı mimarisinin tanımlanması gerekir.

Mevcut durum:
- `electronic/amplifier/class-ab.md` v1.0.0: Genel Class AB topolojisi (100W @ 8Ω, THD+N <0.01%)
- `electronic/amplifier/power-supply.md` v1.0.0: Genel PSU topolojisi (±42V DC boost)
- `architecture/amplifier-classab-circuit.md`: Devre şeması detayları
- `architecture/bom-classab.md`: BOM listesi (1018 bileşen)
- `architecture/pcb-classab.md`: 6 katmanlı PCB kuralları
- `architecture/thermal-design-classab.md`: Termal yönetim
- `architecture/power-supply-classab.md`: ±35V boost PSU tasarımı

Bu ADR, tüm bu detayları tek bir mimari karar belgesinde birleştirir.

## Karar

Class AB Darlington topolojisi benimsenir:

- **Output Transistörleri:** MJL21194 (NPN) / MJL21193 (PNP) — ON Semi
- **Topoloji:** Class AB Push-Pull Darlington
- **Çıkış Gücü:** 50W/kanal @ 8Ω (8 kanal toplam 400W)
- **THD+N:** <0.005% (1kHz, 1W)
- **Güç Kaynağı:** 6S LiPo (22.2V nominal) → ±35V boost converter ile simetrik güç
- **Yapı:** 8 kanal modüler — her kanal bağımsız PCB
- **Termal:** Sıcaklık kontrollü sessiz fan (aktif soğutma)

### Output Transistör Seçim Kararı

| Parametre | MJL21194/MJL21193 | MJL3281A/MJL1302A | 2SC5200/2SA1943 |
|-----------|--------------------|--------------------|-----------------|
| Güç | 200W | 200W | 150W |
| Akım | 15A | 15A | 15A |
| VCEO | 260V | 260V | 230V |
| SOA (10ms) | En iyi | İyi | İyi |
| THD Performansı | <0.005% | <0.01% | <0.01% |
| Kullanım | **Seçilen** | Alternatif | Düşük maliyet |

**MJL21194/MJL21193 seçim nedeni:** Düşük THD (<0.005%), en iyi SOA (Safe Operating Area), LineerAudio uygulamaları için optimize edilmiş.

### Güç Kaynağı Mimarisi

```
6S LiPo (22.2V nominal, 25.2V tam şarj)
    │
    ▼
4× LM5122 Interleaved Boost Converter
    │
    ├── +35V rail (8 kanal için)
    └── -35V rail (8 kanal için)
    │
    ▼
Class AB Amplifier (8 kanal × 50W = 400W)
```

**Güç hesaplamaları:**
- 8 kanal × 50W = 400W çıkış
- Class AB verimliliği ~%60 → ~667W giriş gerekli
- 6S LiPo (22.2V × 5000mAh) = 111Wh → ~10 dakika max güçte
- Sıcaklık kontrollü fan: Isıya bağlı hız ayarı (PWM)

## Gerekçe

1. **Class D "camur gibi" ses kalitesi** → Class AB tercih; doğal bozulma profili, daha "sıcak" ve "canlı" ses
2. **MJL21194/MJL21193** → Düşük THD (<0.005%), yüksek lineerlik, geniş SOA (Safe Operating Area)
3. **6S LiPo (22.2V)** → Taşınabilir güç kaynağı; mobil/stüdyo kullanımı için ideal
4. **±35V boost** → Simetrik güç, Class AB topolojisi için ideal; boost verimliliği ile yüksek güç
5. **Sıcaklık kontrollü sessiz fan** → Aktif soğutma ile termal stabilite; düşük gürültü seviyesi

## Etki Alanı

- **K1: Donanım** — Class AB amplifikatör PCB tasarımı (6 katman, 200×100mm)
- **K16: Class AB topoloji** — Darlington push-pull, bias ayarı, termal tracking
- **K17: Güç kaynağı** — ±35V boost converter (4× LM5122 interleaved, 800W, %96 verimlilik)
- **K18: Termal tasarım** — Sıcaklık kontrollü sessiz fan (PWM), heatsink seçimi, KSD301 NTC

## Alternatifler

| # | Alternatif | Neden Reddedildi |
|---|-----------|-----------------|
| 1 | **Class D (TPA3255)** | "Camur gibi" ses kalitesi — switching noise, higher-order harmonics |
| 2 | **Class A** | Çok yüksek ısı (~%25 verimlilik), verimsiz; 400W çıkış için ~1600W giriş gerekli |
| 3 | **LM3886 Entegre** | Entegre çip sınırlamaları; 38W @ 8Ω — 50W hedefini karşılamıyor |
| 4 | **TDA7294 Entegre** | 100W tek çip ama discrete topoloji kadar esnek değil, soğutma zorluğu |

## GitHub Referansları

- [prydin/classab-amp](https://github.com/prydin/classab-amp) — MJL3281/1302 tabanlı Class AB
- [prydin/opamp-frontend-class-ab](https://github.com/prydin/opamp-frontend-class-ab) — THD 0.00045%
- [analoghifi/Elektor-Fortissimo-100](https://github.com/analoghifi/Elektor-Fortissimo-100) — THD 0.0008%
- [ufelectronics/classab-based-on-motorola-an-422](https://github.com/ufelectronics/classab-based-on-motorola-an-422) — AN-422 referans devresi

## BOM Özeti

| Parametre | Değer |
|-----------|-------|
| Toplam bileşen | 1018 |
| BJT | 56 (BC546B, BC556B, KSC3503, BD139, BD140) |
| Output Transistör | 16 (MJL21194 × 8, MJL21193 × 8) |
| Diot | 30 |
| Direnç | 260 (¼W, 1W, 5W wirewound, 0402 SMD) |
| Kapasitör | 200 (Ceramic, Film, Electrolytic, MLCC SMD) |
| MOSFET | 20 (IRLZ44N, IPB017N06N, IRFB4110) |
| IC | 10 (LM5122 × 8, LM393 × 2) |
| Endüktör | 10 (XAL5030-472 × 8, µMetal post-filter × 2) |
| Termal | 32 (Heatsink × 8, Fan × 6, KSD301 × 16, NTC × 8) |
| **Maliyet (1+)** | **~$415** |
| **Maliyet (100+)** | **~$293** |
| **Toplam güç (8 kanal)** | **400W** |

## İlgili ADR'ler

| ADR | İlişki |
|-----|--------|
| ADR-001 | Vanilla JS + ITCSS — korunuyor (frontend tarafında değişiklik yok) |
| ADR-002 | PDO Mandatory — korunuyor (veritabanı tarafında değişiklik yok) |
| ADR-038 | 8.1 Sound Card (PCM3168A + XMOS) — korunuyor; Class AB amplifikatör PCM3168A çıkışına bağlanır |
| ADR-061 | Electronics Architecture — bu ADR ile güncelleniyor (Class AB detay eklendi) |
| ADR-062 | DSP Pipeline Architecture — korunuyor |
| ADR-063 | Hardware Design Standards — bu ADR ile uyumlu |
| ADR-064 | Electronics Platform Architecture — bu ADR ile güncelleniyor |

## Doğrulama

- [ ] Devre şeması doğrulandı (`architecture/amplifier-classab-circuit.md`)
- [ ] BOM doğrulandı (`architecture/bom-classab.md`)
- [ ] PCB kuralları doğrulandı (`architecture/pcb-classab.md`)
- [ ] Termal tasarım doğrulandı (`architecture/thermal-design-classab.md`)
- [ ] PSU tasarımı doğrulandı (`architecture/power-supply-classab.md`)
- [ ] Sıcaklık kontrollü fan_PWM ayarları doğrulandı

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-18
**Mode:** Red Team · Human Mode · Truth Mode
**Status:** Draft — onay bekliyor
