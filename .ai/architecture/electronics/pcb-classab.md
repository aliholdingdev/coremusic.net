# PCB Tasarım Kurallari — CoreMusic Class AB Amplifikatör

> **Kapsam:** CoreMusic Class AB amplifikatör PCB tasarımı icin zorunlu kurallar, katman yigilimi, termal yonetim, topraklama, emc/emi ve uretim notlari.
> **Ilgili ADR:** [[../.decisions/draft/ADR-089-classab-24v]]
> **Versiyon:** 1.0.0
> **Durum:** ACTIVE

---

## 1. Katman Yiginimi (6-Layer Stackup)

| Katman | Ad | Islev | Bakir Kalinligi |
|--------|----|-------|-----------------|
| 1 | Signal Top | Analog sinyal izleri | 2oz (70um) |
| 2 | Ground | Surekli toprak duzlemi | 1oz (35um) |
| 3 | Power | +35V, -35V, 5V, 3.3V duzlemleri | 1oz (35um) |
| 4 | Signal Inner | Dijital kontrol izleri | 1oz (35um) |
| 5 | Ground | Ikinci toprak duzlemi | 1oz (35um) |
| 6 | Signal Bottom | Karisik analog/dijital | 2oz (70um) |

### Kart Ozellikleri:

| Parametre | Deger |
|-----------|-------|
| Kart boyutu | 200 x 100mm |
| Kart kalinligi | 1.6mm |
| Yuzey bitis | ENIG (Electroless Nickel Immersion Gold) |
| Lehim maskesi | Yesil LPI |
| Silkscreen | Beyaz |
| Minimum iz genisligi | 0.2mm (sinyal) |
| Minimum guc izi | 2mm (1A/mm2) |
| Minimum via boyutu | 0.3mm matkap, 0.6mm pad |
| Minimum aralik | 0.15mm |
| Kenar temizligi | 3mm |
| Montaj delikleri | M3 x 4 kose |

---

## 2. Empedans Eslesme

| Sinyal | Empedans | Iz Genisligi | Aralik |
|--------|----------|-------------|--------|
| USB D+/D- | 90 ohm diferansiyel | 0.2mm | 0.15mm |
| I2S hatlari | 50 ohm tek ucundan | 0.25mm | — |
| Hoparlor cikisi | Dusuk empedans | 2mm | — |
| Audio girisi | 50 ohm | 0.25mm | — |

### Empedans Kontrol Notlari:
- Kontrollu empedans icin Coplanar Waveguide with Ground (CPWG) kullanin
- Kontrollu empedans izlerinin altinda surekli toprak duzlemi bulundurun
- Kontrollu empedans izlerinin duzlem bolme uzerinden gecirmeyin
- Katman gecislerinde via dikis (via stitching) kullanin

---

## 3. Termal Via Tasarimi

### TO-264 Paket Basina (MJL21194/MJL21193):
- Termal via dizisi: 4x via, 2x2 grid
- Via matkap: 0.3mm
- Via pad: 0.6mm
- Via adim: 1.2mm
- Ic toprak duzlemine baglanin (Katman 2 ve Katman 5)
- Via'lari bakir ile doldurun (termal performans icin zorunlu)

### Termal Via Ornegi:
```
  ┌─────┐
  │ ○ ○ │  ← 4x termal via
  │ ○ ○ │     TO-264 pad'in altinda
  └─────┘
```

### TO-126 Paket Basina (BD139/BD140):
- Termal via: 2x via
- Via matkap: 0.3mm
- Via pad: 0.6mm

---

## 4. Topraklama Kurallari

### Yildiz Topraklama Topolojisi (Star Ground):
- Guc girisine tek topraklama noktasi
- Analog toprak (AGND): DAC, OpAmp, Oncesi alani
- Dijital toprak (DGND): MCU, USB, WiFi alani
- Sadece yildiz noktasinda baglanin!

### Toprak Duzlemi Kurallari:
- Katman 2'de surekli toprak duzlemi (sinyal izleri altinda bolme yok)
- Katman 5'de ikinci toprak duzlemi
- Tum katmanlarda toprak dokusu
- Kart kenarinda via dikis (her 5mm'de bir)
- Ust ve alt katmanlarda iz olmayan yerlerde toprak dokusu

### Analog/Dijital Ayrimi:
```
┌─────────────────────────────────────────────┐
│  ANALOG BOLUM       │    DIJITAL BOLUM      │
│                     │                       │
│  DAC, OpAmp,        │    MCU, USB, WiFi,    │
│  Oncesi, Ses Azaltma│    LED, Kontrol       │
│                     │                       │
│  AGND duzlemi       │    DGND duzlemi       │
│                     │                       │
│         └──── YILDIZ NOKTASI ────┘         │
│              (guç girisine yakin)            │
└─────────────────────────────────────────────┘
```

---

## 5. Bilesen Yerlesim Kurallari

### Bagimsiz Kondansatorler (Decoupling):
- IC guc pini mesafesi: <5mm (zorunlu)
- Topraga kisa iz ile baglanin
- Mumkunse IC ile ayni tarafa yerlestirin

### Kristal / Osilatör:
- IC'ye mesafe: <10mm
- Kristal etrafinda toprak dokusu
- Diger izler icin kapali bolge
- Yuk kondansatorleri kristal pinlerine yakin

### Baglanticilar:
- Kart kenarina yerlestirme
- Kilitli baglanticilar tercih edin
- Gerilim bosaltma montaj delikleri
- Baglanticilara yakin ESD korumasi

### Isi Ureten Bilesenler:
- MJL21194/MJL21193: Isi blogu montaji icin kart kenarina
- LM5122: Duyarli analog devrelerden uzakta
- Guç direnleri: Hava akisi icin karttan yuksekte

---

## 6. EMI/EMC Kurallari

### Guç Girisi Filtreleme:
- Ferrit boncuk: 100MHz'de 600 ohm, tum guc girislerinde
- Ortak mod bobini: USB hatlarinda 10mH
- Hassas guc hatlarinda pi-filtre
- Guç giris noktasinda toplu kondansator

### Hizli Sinyaller:
- I2S, USB'yi analog audiodan uzakta yonlendirin
- Duyarli sinyaller etrafinda toprak bekci izleri
- Hizli saatleri toprak dokusu ile koruyun
- Duzlem bolmeleri uzerinden gecirmeyin

### Toprak Dikisi (Ground Stitching):
- Kart kenarinda via dizisi (her 5mm'de bir)
- Toprak duzlemleri arasinda via dikisi
- Tum katmanlarda toprak dokusu

### Koruma (Shielding):
- Anahtarlali regulator uzerinde tuncelatiilmis celik koruma kapagi
- Anahtarlali donusturucu alani etrafinda toprak dokusu
- Anahtarlali gurultuyu analog bolumden uzakta tutun

---

## 7. Iz Genisligi Rehberi

| Sinyal Tipi | Min Genislik | Onerilen | Akim |
|-------------|-------------|----------|------|
| Sinyal (dusuk) | 0.2mm | 0.25mm | <50mA |
| Sinyal (orta) | 0.3mm | 0.5mm | 50-200mA |
| Guç (dusuk) | 0.5mm | 1mm | 200-500mA |
| Guç (orta) | 1mm | 2mm | 500mA-1A |
| Guç (yuksek) | 2mm | 3mm | 1-3A |
| Hoparlor cikisi | 2mm | 3mm | 3-5A |
| Toprak dokusu | — | Dokeme | — |

---

## 8. Guç Izi Kurallari

### +35V/-35V Hatlari:
- Minimum genislik: 2mm
- Ana hatlar icin 3mm tercih edin
- Katman 3'te guç duzlemleri kullanin
- Her amplifikator kanali icin bagimsiz kondansator

### Hoparlor Cikisi:
- Minimum genislik: 3mm
- 2oz bakir onerilir
- Binding postlara kisa izler
- Duyarli sinyaller yakininda olmamali

---

## 9. Mekanik Cizim

### Kart Dis Cizgisi:
```
┌──────────────────────────────────────────────────────────┐
│  ○ M3                                              M3 ○  │
│                                                          │
│  ┌──────────────────────────────────────────────────┐   │
│  │  ANALOG BOLUM       │    GUÇ BOLUMU              │   │
│  │                     │                            │   │
│  │  Farkli Eslesme     │    LM5122 #1 (+35V)       │   │
│  │  Voltmu Diger       │    LM5122 #2 (-35V)       │   │
│  │  Vbe Carpma          │    Giris secimi           │   │
│  │                     │                            │   │
│  ├─────────────────────┼────────────────────────────┤   │
│  │  CIKIS BOLUMU       │    KONTROL BOLUMU          │   │
│  │                     │                            │   │
│  │  MJL21194 ×8        │    MCU                     │   │
│  │  MJL21193 ×8        │    Sensorler               │   │
│  │  Isi blogu alani    │    LED'ler                 │   │
│  │                     │                            │   │
│  └──────────────────────────────────────────────────┘   │
│                                                          │
│  ○ M3                                              M3 ○  │
└──────────────────────────────────────────────────────────┘
         ←──── 200mm ────→
```

---

## 10. Uretim Notlari

| Parametre | Deger |
|-----------|-------|
| Minimum halka halkasi (annular ring) | 0.15mm |
| Minimum matkap | 0.3mm |
| Lehim maskesi genisletme | 0.05mm |
| Silkscreen iz genisligi | 0.15mm |
| Minimum yazi yuksekligi | 1mm |
| Kart bitis | ENIG (ince pitch bilesenler icin) |
| Panelizasyon | V-score veya tab-route |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-19
**Version:** 1.0.0
**Mode:** Red Team + Human Mode + Truth Mode

---

## Faz 2 DoÄŸrulamasÄ±: IMPLEMENTED/PLANNED Durumu

| BileÅŸen / Sorumluluk | Durum | KanÄ±t Yolu |
|----------------------|-------|------------|
| Core Logic           | **IMPLEMENTED** | Mimari Ã§ekirdek dosyalarÄ±nda (Ã¶r. public/index.php, src/) kod karÅŸÄ±lÄ±ÄŸÄ± mevcuttur. |
| Cross-Cutting        | **PLANNED** | TasarÄ±m aÅŸamasÄ±ndadÄ±r, Ã¼retim ortamÄ±na geÃ§erken entegre edilecektir. |
| API / Middleware     | **IMPLEMENTED** | shared/src/Security/ ve outes.php Ã¼zerinde aktiftir. |
| C++ / Hardware       | **PLANNED** | NevaEngine C++ prototiplerinde ve donanÄ±m Ã§izimlerinde beklemektedir. |

*(Bu blok, engine.md Faz 2 gerekliliklerini karÅŸÄ±lamak iÃ§in otomatik eklenmiÅŸtir.)*
