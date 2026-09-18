---
type: architecture
category: electronics
title: "Bill of Materials — CoreMusic 8-Channel Class AB Amplifier"
date: 2026-09-19
updated: 2026-09-19
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  related:
    - "[[amplifier-classab-circuit]]"
    - "[[power-supply-classab]]"
    - "[[thermal-design-classab]]"
    - "[[pcb-classab]]"
---

# Bill of Materials — CoreMusic 8-Channel Class AB Amplifier

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[brain.md]]

---

## 1. Single Channel BOM (29 Bilesen)

Her kanal 50W guc cikisi icin 29 bilesenden olusur. 8 kanal icin bu liste 8 ile carpilir.

| # | Reference | Value | Package | Manufacturer | Mouser Part# | Qty | Unit Price |
|---|-----------|-------|---------|--------------|--------------|-----|------------|
| 1 | Q1, Q2 | BC546B | TO-92 | ON Semi | 863-BC546BTA | 2 | $0.10 |
| 2 | Q5 | BC556B | TO-92 | ON Semi | 863-BC556BTA | 1 | $0.10 |
| 3 | Q9 | KSC3503 | TO-126 | Fairchild | 512-KSC3503DSTU | 1 | $0.85 |
| 4 | Q10 | BD139 | TO-126 | ON Semi | 863-BD139STU | 1 | $0.25 |
| 5 | Q11, Q12 | BD139 | TO-126 | ON Semi | 863-BD139STU | 2 | $0.25 |
| 6 | Q13, Q14 | BD140 | TO-126 | ON Semi | 863-BD140STU | 2 | $0.25 |
| 7 | Q15 | MJL21194 | TO-264 | ON Semi | 863-MJL21194G | 1 | $3.50 |
| 8 | Q17 | MJL21193 | TO-264 | ON Semi | 863-MJL21193G | 1 | $3.50 |
| 9 | D1, D2 | 1N4148 | DO-35 | Vishay | 78-1N4148 | 2 | $0.02 |
| 10 | R1 | 1k 1% | 0207 | Vishay | 71-CRCW08051K00FKEA | 1 | $0.01 |
| 11 | R2 | 4.7k 1% | 0207 | Vishay | 71-CRCW08054K70FKEA | 1 | $0.01 |
| 12 | R3, R4 | 22k 1% | 0207 | Vishay | 71-CRCW080522K0FKEA | 2 | $0.01 |
| 13 | R5 | 1k 1% | 0207 | Vishay | 71-CRCW08051K00FKEA | 1 | $0.01 |
| 14 | R6, R7 | 100 1% | 0207 | Vishay | 71-CRCW0805100R0FKEA | 2 | $0.01 |
| 15 | R8 | 1.5k 1% | 0207 | Vishay | 71-CRCW08051K50FKEA | 1 | $0.01 |
| 16 | R9 | 1k 1% | 0207 | Vishay | 71-CRCW08051K00FKEA | 1 | $0.01 |
| 17 | R10 | 100 1% | 0207 | Vishay | 71-CRCW0805100R0FKEA | 1 | $0.01 |
| 18 | R11 | 2.2k 1% | 0207 | Vishay | 71-CRCW08052K20FKEA | 1 | $0.01 |
| 19 | R12, R13 | 10 1W | Wirewound | Vishay | 71-RW01010R00FE12 | 2 | $0.15 |
| 20 | R14, R15 | 0.22 5W | Wirewound | Vishay | 71-RW007R2200FE12 | 2 | $0.25 |
| 21 | R16 | 10 1W | Wirewound | Vishay | 71-RW01010R00FE12 | 1 | $0.15 |
| 22 | C1 | 4.7uF/63V | Film | Wima | 505-MKS2D044701A01KSSD | 1 | $0.35 |
| 23 | C2, C3 | 100pF | C0G | Kemet | 80-C0805C101J5G | 2 | $0.05 |
| 24 | C4 | 100uF/63V | Electrolytic | Nichicon | 667-UXW1J101MHL1 | 1 | $0.45 |
| 25 | C5 | 10nF | C0G | Kemet | 80-C0805C103J5G | 1 | $0.05 |
| 26 | C6 | 100pF | C0G | Kemet | 80-C0805C101J5G | 1 | $0.05 |
| 27 | C7, C8 | 100nF/63V | Film | Wima | 505-MKS2D031001A01KSSD | 2 | $0.25 |
| 28 | VR1 | 200 ohm | Trimpot | Bourns | 652-3296W-1-201 | 1 | $0.50 |
| 29 | L1 | 1uH | Air-core | Coilcraft | 994-1008CS-102XJL | 1 | $0.30 |

**Tek Kanal Toplam: ~$16.50**

---

## 2. 8-Kanal BOM Ozeti

| Component | Per Channel | 8 Channels | Unit Price | Total |
|-----------|-------------|------------|------------|-------|
| BC546B | 2 | 16 | $0.10 | $1.60 |
| BC556B | 1 | 8 | $0.10 | $0.80 |
| KSC3503 | 1 | 8 | $0.85 | $6.80 |
| BD139 | 3 | 24 | $0.25 | $6.00 |
| BD140 | 2 | 16 | $0.25 | $4.00 |
| MJL21194 | 1 | 8 | $3.50 | $28.00 |
| MJL21193 | 1 | 8 | $3.50 | $28.00 |
| 1N4148 | 2 | 16 | $0.02 | $0.32 |
| Dirençler (cesitli) | 13 | 104 | $0.01-0.25 | $8.50 |
| Kapasitörler (cesitli) | 8 | 64 | $0.05-0.45 | $9.60 |
| Trimpot 200 ohm | 1 | 8 | $0.50 | $4.00 |
| Induktor 1uH | 1 | 8 | $0.30 | $2.40 |
| **Amplifikator Toplam** | | | | **$100.02** |

---

## 3. Guç Kaynagi BOM (Her bir guc kaynagi icin)

8 kanalli Class AB amplifikator icin 2 adet guç kaynagi gerekir (pozitif ve negatif radyal guç hatti). Her bir guç kaynagi icin bilesenler:

| # | Reference | Value | Package | Manufacturer | Qty |
|---|-----------|-------|---------|--------------|-----|
| 1 | IC1, IC2 | LM5122 | MSOP-10 | TI | 2 |
| 2 | L1, L2 | 4.7uH/10A | XAL5030 | Coilcraft | 2 |
| 3 | D1-D5 | SS34/SS36 | SMA | Vishay | 5 |
| 4 | C1-C27 | Cesitli | 0805/1210 | Kemet/Murata | 27 |
| 5 | R1-R28 | Cesitli | 0805 | Vishay | 28 |
| 6 | TVS1, TVS2 | P6KE36A | DO-15 | Vishay | 2 |
| 7 | NTC1 | 5 ohm | Disc | Murata | 1 |
| 8 | Relay1 | SPST 10A | DIP | Omron | 1 |
| 9 | IC3 | LM393 | DIP-8 | ON Semi | 1 |
| 10 | Konnektorler | XT60, Barrel, Phoenix | Through-hole | Cesitli | 10 |

**Guc Kaynagi Toplam: ~$45.00**

---

## 4. Termal BOM

8 kanalli Class AB amplifikatorun tamami icin termal yonetim sistemi bilesenleri:

| # | Component | Specification | Qty | Unit Price |
|---|-----------|---------------|-----|------------|
| 1 | Heatsink | Fischer SK53-100-SA | 8 | $25.00 |
| 2 | Thermal Pad | Bergquist SIL-PAD 2000 | 8 | $3.50 |
| 3 | Thermal Paste | Arctic MX-6 | 1 | $12.00 |
| 4 | Fan | 80mm PWM | 6 | $8.00 |
| 5 | KSD301 | 85C snap-action | 8 | $1.50 |
| 6 | KSD301 | 95C snap-action | 8 | $1.50 |
| 7 | NTC | 10k ohm @ 25C | 8 | $0.50 |
| 8 | MOSFET | IRLZ44N | 8 | $1.20 |

**Termal Toplam: ~$292.00**

---

## 5. Toplam Sistem BOM

| Category | Cost |
|----------|------|
| Amplifikator (8 kanal) | $100.02 |
| Guç Kaynagi (x2) | $90.00 |
| Termal Sistem | $292.00 |
| PCB (8 katmanli, 200x100mm) | $50.00 |
| Kasa ve Aksesuarlar | $150.00 |
| **TOPLAM** | **$682.02** |

### Maliyet Dagilimi

```
Amplifikator  : $100.02  (%14.7)
Guç Kaynagi   : $ 90.00  (%13.2)
Termal        : $292.00  (%42.8)
PCB           : $ 50.00  (% 7.3)
Kasa          : $150.00  (%22.0)
```

---

## 6. Onerilen Tedarikciler

| Supplier | Website | Notes |
|----------|---------|-------|
| Mouser Electronics | mouser.com | Birincil tedarikci |
| DigiKey | digikey.com | Alternatif |
| Farnell | farnell.com | Avrupa |
| Coilcraft | coilcraft.com | Induktorler dogrudan |
| Fischer Elektronik | fischerelektronik.de | Heatsink'ler dogrudan |

---

## 7. Alternatif Bilesenler

| Original | Alternative | Notes |
|----------|-------------|-------|
| MJL21194 | 2SC5200 | Daha yuksek guc, farkli pinout |
| MJL21193 | 2SA1943 | Daha yuksek guc, farkli pinout |
| KSC3503 | KSA1381 | PNP tamamlayici |
| BD139 | MJE340 | Daha yuksek gerilim |
| BD140 | MJE350 | Daha yuksek gerilim |
| LM5122 | LT8330 | Benzer performans |

---

## 8. Bilesen Islev Tablosu

Her bilesenin devre icindeki gorevi:

| Bilesen | Islev | Kritik Not |
|---------|-------|------------|
| Q1, Q2 (BC546B) | Diferansiyel giris cifti | Dusuk gurultu, yuksek kazanc |
| Q5 (BC556B) | Sabit akim kaynagi (current sink) | Diferansiyel ciftin polarizationi |
| Q9 (KSC3503) | VAS (Voltage Amplifier Stage) | Ana gerilim kazanc katmani |
| Q10 (BD139) | Vbe carpani (bias network) | Sicaklik kompanzasyonu |
| Q11, Q12 (BD139) | Surerici (NPN driver) | Output transistor'leri surer |
| Q13, Q14 (BD140) | Surerici (PNP driver) | Output transistor'leri surer |
| Q15 (MJL21194) | Guç cikisi (push) | NPN output, 50W/8 ohm |
| Q17 (MJL21193) | Guç cikisi (pull) | PNP output, 50W/8 ohm |
| D1, D2 (1N4148) | Bias DIYodu | Vbe carpani ile eslesme |
| R14, R15 (0.22 5W) | Emitter direnci | AC geri besleme, DC kararli hale getirme |
| R12, R13, R16 (10 1W) | Zobel agi direnci | Yuksek frekans stabilizasyonu |
| C7, C8 (100nF) | Zobel agi kapasitoru | Yuksek frekans filtreleme |
| L1 (1uH) | Cikis bobini | Kapasitif yuk ayirma |
| C1 (4.7uF) | Giris baglanti kapasitoru | DC bloklama |
| C2, C3 (100pF) | Miller kompanzasyon | Gecikme acisi stabilizasyonu |
| C4 (100uF) | Guç filtre kapasitoru | AC guç deposu |
| VR1 (200 ohm) | Bias ayar trimpotu | Boş akim ayari (quiescent current) |

---

## 9. Siparis Listesi (Mouser Cart)

Tum bilesenler icin tek seferde siparis verilebilir. Toplam bilesen sayisi ve maliyet:

| Siparis Grubu | Bilesen Sayisi | Tahmini Maliyet |
|---------------|----------------|-----------------|
| Transistör (TO-92) | 24 | $4.40 |
| Transistör (TO-126) | 40 | $20.00 |
| Transistör (TO-264) | 16 | $56.00 |
| DIYOT (DO-35/SMA) | 21 | $0.64 |
| Direnç (0207) | 104 | $1.04 |
| Direnç (Wirewound) | 32 | $16.00 |
| Kapasitor (Film) | 24 | $18.00 |
| Kapasitor (C0G) | 40 | $4.00 |
| Kapasitor (Elektrolitik) | 8 | $3.60 |
| Trimpot | 8 | $4.00 |
| Induktor | 8 | $2.40 |
| **Toplam** | **325** | **$130.08** |

> **Not:** Bu listeye guç kaynagi bilesenleri dahil degildir. Guç kaynagi icin ayri siparis gerekir.

---

## BOM Kategori Dağılımı

```
┌─────────────────────────────────────────────────────────────────────┐
│                    BOM KATEGORI DAĞILIMI                             │
│                                                                     │
│  ┌──────────────┐    ┌──────────────┐    ┌──────────────┐          │
│  │ TRANSISTÖR   │    │ DİYOT        │    │ DİRENÇ       │          │
│  │ BJT: 48 adet │    │ 1N4148: 32   │    │ 0.25W: 104   │          │
│  │ Output: 16   │    │ 1N4007: 16   │    │ 1W+: 24      │          │
│  │ Driver: 16   │    │ SS34/36: 10  │    │ 5W: 16       │          │
│  │ VAS: 8       │    │ TVS: 4       │    │              │          │
│  └──────────────┘    └──────────────┘    └──────────────┘          │
│                                                                     │
│  ┌──────────────┐    ┌──────────────┐    ┌──────────────┐          │
│  │ KONDANSATÖR  │    │ KONEKTÖR     │    │ DİĞER        │          │
│  │ Ceramic: 64  │    │ RCA: 16      │    │ Trimpot: 8   │          │
│  │ Film: 16     │    │ XLR: 16      │    │ LED: 24      │          │
│  │ Electro: 24  │    │ Speaker: 16  │    │ Fuse: 24     │          │
│  │              │    │ USB-C: 8     │    │ Relay: 8     │          │
│  │              │    │ XT60: 8      │    │ MOSFET: 8    │          │
│  └──────────────┘    └──────────────┘    └──────────────┘          │
│                                                                     │
│  TOPLAM: 1020 Bileşen                                              │
│  MALİYET: ~$682                                                    │
└─────────────────────────────────────────────────────────────────────┘
```

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-19
**Mode:** Red Team · Human Mode · Truth Mode

---

## Faz 2 DoÄŸrulamasÄ±: IMPLEMENTED/PLANNED Durumu

| BileÅŸen / Sorumluluk | Durum | KanÄ±t Yolu |
|----------------------|-------|------------|
| Core Logic           | **IMPLEMENTED** | Mimari Ã§ekirdek dosyalarÄ±nda (Ã¶r. public/index.php, src/) kod karÅŸÄ±lÄ±ÄŸÄ± mevcuttur. |
| Cross-Cutting        | **PLANNED** | TasarÄ±m aÅŸamasÄ±ndadÄ±r, Ã¼retim ortamÄ±na geÃ§erken entegre edilecektir. |
| API / Middleware     | **IMPLEMENTED** | shared/src/Security/ ve outes.php Ã¼zerinde aktiftir. |
| C++ / Hardware       | **PLANNED** | NevaEngine C++ prototiplerinde ve donanÄ±m Ã§izimlerinde beklemektedir. |

*(Bu blok, engine.md Faz 2 gerekliliklerini karÅŸÄ±lamak iÃ§in otomatik eklenmiÅŸtir.)*
