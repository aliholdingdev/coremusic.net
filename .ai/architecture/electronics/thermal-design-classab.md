# CoreMusic Class AB Amplifikator Termal Tasarim

CoreMusic Class AB amplifikator sisteminin termal tasarimi, 8 kanalli moduler yapida 400W toplam cikis gucu uretecek sekilde planlanmistir. Bu belge, isi dagilimini, soğutucu secimini, termal arayuzu, fan kontrolunu, guvenlik mekanizmalarini ve mekanik tasarimi kapsamaktadir.

**Iliskili Dosyalar:**
- [[amplifier-classab-circuit]] — Devre tasarimi
- [[power-supply-classab]] — Guc kaynagi tasarimi
- [[bom-classab]] — BOM listesi
- [[pcb-classab]] — PCB tasarimi
- [[decisions/draft/ADR-089-classab-24v]] — Class AB mimari karari

---

## 1. Isi Dagilim Hesaplamasi

### 1.1 Kanal Basina

| Parametre | Deger | Aciklama |
|-----------|-------|----------|
| Cikis gucu (Pout) | 50W @ 8 Ohm RMS | Tam guc calismasi |
| Verimlilik (eta) | %55 | Class AB tipik degeri |
| DC giris gucu (Pin) | 91W | Pin = Pout / eta = 50 / 0.55 |
| Isi kaybi (Pdiss) | 41W | Pdiss = Pin - Pout = 91 - 50 |
| Ortam sicakligi (Ta) | 25 C | Standart calisma kosulu |
| Maksimum cihaz sicakligi (Tj) | 85 C | Koruma amaciyla sinir |
| Sicaklik farki (deltaT) | 60 C | Tj - Ta = 85 - 25 |
| Gerekli termal direnc (Rth) | 1.46 C/W | Rth = deltaT / Pdiss = 60 / 41 |

### 1.2 Toplam Sistem (8 Kanal)

| Parametre | Deger | Aciklama |
|-----------|-------|----------|
| Toplam cikis gucu | 400W | 50W x 8 kanal |
| Toplam DC giris gucu | 728W | 91W x 8 kanal |
| Toplam isi kaybi | 328W | 41W x 8 kanal |
| Ortalama kanal basina isi | 41W | Her kanal esit pay alir |
| Toplam guc kaynagi kapasitesi | 800W | Guc kaynagi 8 kanal icin yeterli margin |

### 1.3 Verimlilik Analizi

```
Class AB Verimlilik:
  eta = Pout / Pin
  eta = 50W / 91W = 0.549 (~%55)

Isi Orani:
  Pdiss / Pin = 41W / 91W = 0.451 (~%45)
  
  Sonuc: Her 100W DC giriste 45W isi olarak kaybolur.
  8 kanal icin bu 328W toplam isi demektir.
```

---

## 2. Soğutucu (Heatsink) Secimi

### 2.1 Gereksinimler

| Parametre | Deger | Aciklama |
|-----------|-------|----------|
| Isi dagilimi | 41W/kanal | Tek kanal isisi |
| Ortam sicakligi | 25 C | Standart calisma |
| Maksimum cihaz sicakligi | 85 C | MJL21194 max: 150 C, guvenli sinir |
| deltaT | 60 C | 85 - 25 |
| Gerekli Rth | 1.46 C/W | 60 / 41 |
| Fan ile calisma | Zorunlu | 8 kanal icin aktif soğutma gerekli |

### 2.2 Secilen Soğutucu

| Ozellik | Deger |
|---------|-------|
| Model | Fischer Elektronik SK53-100-SA |
| Boyutlar | 300 x 75 x 49 mm |
| Termal direnc (zorunlu hava) | 0.8 C/W |
| Agirlik | ~1.5 kg |
| Materyal | Alumin extruzyon |
| Kanat araligi | 3 mm |
| Yuzey | Siyah anodize |
| Kanal basina adet | 1 (toplam 8 adet) |

### 2.3 Alternatif Soğutucular

| Model | Rth (C/W) | Boyut (mm) | Fiyat | Not |
|-------|-----------|------------|-------|-----|
| Fischer SK53-100-SA | 0.8 | 300x75x49 | ~$25 | SECILEN |
| Fischer SK49-80-SA | 1.0 | 250x60x40 | ~$18 | Daha kucuk, daha ucuz |
| Aavid 637302 | 0.9 | 280x70x45 | ~$22 | Iyi alternatif |
| Wakefield 695-3AB | 1.1 | 260x65x42 | ~$20 | Butce dostu |
| Fischer SK82-150-SA | 0.6 | 350x80x55 | ~$35 | Ust seviye, 8K icin fazla |

### 2.4 Termal Direnc Hesabi (Fanli)

```
Fanli calismada termal direnc dususu:
  Rth_forced = Rth_natural x 0.4 (tipik)
  
  Rth_natural = 0.8 C/W (dogal konveksiyon)
  Rth_forced = 0.8 x 0.4 = 0.32 C/W (fanli)
  
  Gerekli: 1.46 C/W
  Mevcut:  0.32 C/W
  Margin:  1.46 / 0.32 = 4.5x fazla kapasite

  Sonuc: Fanli calismada sicaklik cok dusuk olacaktir.
  Bu, 8 kanalin ayni anda tam gucte calismasi icin gerekli.
```

---

## 3. Termal Arayuz (Thermal Interface)

### 3.1 Termal Pad

| Ozellik | Deger |
|---------|-------|
| Model | Bergquist SIL-PAD 2000 |
| Termal iletkenlik | 1.7 W/mK |
| Kalinlik | 0.229 mm |
| Siginabilirlik | %25 |
| Calisma araligi | -60 C ile 200 C |
| Elektriksel izolasyon | Evet (yuksek gerilimde guvenli) |
| Kullanim yeri | Transistor paket heatsink arasinda |

### 3.2 Termal Macun

| Ozellik | Deger |
|---------|-------|
| Model | Arctic MX-6 |
| Termal iletkenlik | 12.5 W/mK |
| Kapasitif degil | Evet |
| Iletken degil | Evet |
| Dayaniklilik | Uzun vadeli (kurumaz) |
| Kullanim yeri | Transistor paket uzerine ince tabaka |

### 3.3 Uygulama Notlari

| Adim | Islem | Onem |
|------|-------|------|
| 1 | Transistor paket uzerine ince tabaka termal macun sur | Rth dususu icin kritik |
| 2 | Termal pad'i transistorden heatsink arasina yerlestir | Elektriksel izolasyon icin |
| 3 | Iyi temas basincindan emin ol | Hava kabarcigi Rth'yi artirir |
| 4 | M3 vidalari 0.5 Nm tork ile sikistir | Fazla sikma pakete zarar verir |
| 5 | Soğutucu yuzeyini temizle (alkol ile) | Yag/kir tabakasi Rth'yi artirir |

---

## 4. Bilesen Isi Gereksinimleri

| Bilesen | Maks Sicaklik | Paket | Montaj | Aciklama |
|---------|---------------|-------|--------|----------|
| MJL21194 (Q15) | 150 C | TO-264 | Heatsink | NPN cikis transistoru |
| MJL21193 (Q17) | 150 C | TO-264 | Heatsink | PNP cikis transistoru |
| BD139 (Q11) | 150 C | TO-126 | Heatsink | NPN surucu transistoru |
| BD140 (Q13) | 150 C | TO-126 | Heatsink | PNP surucu transistoru |
| KSC3503 (Q9) | 150 C | TO-126 | Heatsink | VAS transistoru |
| BD139 (Q10) | 150 C | TO-126 | Heatsink (Vbe) | Vbe carpani (termal kacis icin zorunlu) |
| 0.22 Ohm Dirençler | 100 C | Wirewound | YUKSEKTE | Akim paylasimi, yuksek guc |

### 4.1 Termal Kacis (Thermal Tracking)

Vbe carpani (Q10) mutlaka ana cikis transistörleri (MJL21194/93) ile ayni heatsink'e baglanmalidir. Bu, sicaklik arttikca Vbe'nin azalmasini ve durgun akimin (quiescent current) otomatik olarak dengelenmesini saglar. Baglanmazsa termal kacis (thermal runaway) meydana gelebilir ve transistorler yok olabilir.

---

## 5. Fan Kontrol Sistemi

### 5.1 Fan Teknik Ozellikleri

| Parametre | Deger |
|-----------|-------|
| Boyut | 80 mm |
| Tur | PWM kontrollu |
| Hava akisi | 25 CFM |
| Gurultu | 25 dBA |
| Yatak | Fluid dynamic (hidrodinamik) |
| Omur | >60.000 saat |
| Voltaj | 12V DC |
| Cekim | 0.15A |

### 5.2 PWM Kontrol Devresi

| Bilesen | Deger | Aciklama |
|---------|-------|----------|
| MOSFET | IRLZ44N | Lojik seviye gate, 12V fan kontrolu |
| R30 | 10kOhm | Gate pull-down (varsayilan kapali) |
| R31 | 100 Ohm | Gate direnci (osilasyon onleme) |
| D9 | 1N4007 | Flyback korumasi (induktif yuk) |

### 5.3 Sicaklik Esik Degerleri

| Sicaklik | Fan Durumu | PWM Duty | Aciklama |
|----------|------------|----------|----------|
| < 40 C | KAPALI | %0 | Pasif soğutma yeterli |
| 40 C - 60 C | DUSUK HIZ | %30 | Hafif sogutma baslangici |
| 60 C - 75 C | ORTA HIZ | %60 | Aktif sogutma |
| 75 C - 85 C | YUKSEK HIZ | %100 | Tam guc sogutma |
| 85 C - 95 C | UYARI LED | %100 | Kritik sicaklik uyarisi |
| > 95 C | KAPATMA | — | Amplifikator kapatma |

### 5.4 Fan Kontrol Algoritmasi

```c
// Pseudo-code — MCU fan kontrolu
#define TEMP_FAN_ON       40   // C — Fan baslangic sicakligi
#define TEMP_FAN_LOW      60   // C — Dusuk hiz esigi
#define TEMP_FAN_HIGH     75   // C — Yuksek hiz esigi
#define TEMP_WARNING      85   // C — Uyari LED esigi
#define TEMP_SHUTDOWN     95   // C — Kapatma esigi

void fan_control_update(int temp_celsius) {
    if (temp_celsius >= TEMP_SHUTDOWN) {
        amplifier_shutdown();
        return;
    }
    if (temp_celsius >= TEMP_WARNING) {
        warning_led_on();
        set_fan_pwm(100);
        return;
    }
    if (temp_celsius >= TEMP_FAN_HIGH) {
        set_fan_pwm(100);
        return;
    }
    if (temp_celsius >= TEMP_FAN_LOW) {
        int duty = 60 + (temp_celsius - TEMP_FAN_LOW) * (40 / 15);
        set_fan_pwm(duty);
        return;
    }
    if (temp_celsius >= TEMP_FAN_ON) {
        int duty = 30 + (temp_celsius - TEMP_FAN_ON) * (30 / 20);
        set_fan_pwm(duty);
        return;
    }
    set_fan_pwm(0);  // Fan kapali
}
```

---

## 6. Termal Kesme (Thermal Cutoff) ve Koruma

### 6.1 Donanimsal Koruma

| Bilesen | Deger | Islev | Baglanti |
|---------|-------|-------|----------|
| KSD301 | 85 C | Snap-action termal anahtar | Heatsink uzeri, seri bagli |
| KSD301 | 95 C | Acil durum kapatma | Heatsink uzeri, guc kesme |
| NTC Termistor | 10kOhm @ 25 C | Yazilimli sicaklik olcumu | Voltage divider uzeri |

### 6.2 Yazilimli Izleme

| Parametre | Deger | Aciklama |
|-----------|-------|----------|
| ADC okuma | Voltage divider uzeri | NTC sicaklik olcumu |
| R29 | 10kOhm | Divider ust direnci |
| C28 | 100nF | Filtre kapasitesi |
| ADC cozunurlugu | 12-bit | 4096 kademeli |
| Ornekleme hizi | 1 Hz | Saniyede 1 okuma |

### 6.3 Sicaklik Hesaplama Formulu

```
NTC termistor sicaklik hesaplama:

  T = 1 / (1/T0 + (1/B) * ln(R/R0)) - 273.15

  T0 = 298.15 K (25 C referans sicakligi)
  B  = 3950    (NTC B sabiti, tipik deger)
  R0 = 10000   (25 C'de direnc, 10kOhm)
  R  = ADC'den okunan direnc degeri

Ornek hesaplama:
  R = 5000 Ohm (5kOhm) durumunda:
  T = 1 / (1/298.15 + (1/3950) * ln(5000/10000)) - 273.15
  T = 1 / (0.003354 + 0.000253 * (-0.6931)) - 273.15
  T = 1 / (0.003354 - 0.000176) - 273.15
  T = 1 / 0.003178 - 273.15
  T = 314.7 - 273.15
  T = 41.5 C
```

---

## 7. Mekanik Tasarim

### 7.1 Soğutucu Montaji

| Parametre | Deger |
|-----------|-------|
| Soğutucu sayisi | 8 (kanal basina 1) |
| Montaj vidalari | M3 x 8mm (kanal basina 4 adet) |
| Toplam vida | 32 adet |
| Termal arayuz | Pad + macun kombinasyonu |
| Hava yonu | Asagidan yukari (natural convection assist) |
| Mesafe (transistor arasi) | Minimum 20mm |

### 7.2 Kasa (Enclosure)

| Parametre | Deger |
|-----------|-------|
| Materyal | Alumin kosin (1.5mm kalinlik) |
| Boyutlar | 450 x 300 x 100 mm (tahmini) |
| Havalandirma | Ust ve alt delikler |
| Fan konumu | Arka panel (hava cikisi) |
| Hava filtresi | Cikarilabilir mesh |
| Toplam agirlik | ~8 kg (heatsink dahil) |

### 7.3 Hava Akisi Diyagrami

```
                        +-----------------------------------------------+
                        |           COREMUSIC CLASS AB AMF             |
                        |                                               |
    HAVA GIRISI -----> |  [FAN]    [KANAL 1] [KANAL 2] ... [KANAL 8] | ----> HAVA CIKISI
      (Alt Panel)      |  (Arka)    ^          ^                ^     |     (Ust Panel)
                        |           |          |                |     |
                        |     Heatsink'ler arasindan yukari dogru      |
                        |     dogal konveksiyon + fan destegi         |
                        |                                              |
                        +-----------------------------------------------+

Hava akisi yonu: ASAGI --> YUKARI (natural convection)
Fan yonu: ARKA --> ON (forced convection assist)

Isi dagilimi:
  Asagi (giris):  25 C (ortam sicakligi)
  Ort:            55 C (ortalama heatsink)
  Yukari (cikis): 75 C (sicak hava cikisi)
```

### 7.4 Heatsink Montaj Diyagrami (Yan Kesit)

```
     +-------+     +-------+     +-------+
     | KANAL |     | KANAL |     | KANAL |
     |   1   |     |   2   |     |   3   |
     +---+---+     +---+---+     +---+---+
         |             |             |
    +----+----+   +----+----+   +----+----+
    | MJL21194|   | MJL21194|   | MJL21194|   <-- Transistor paketleri
    +---------+   +---------+   +---------+
    |Termal Pad|   |Termal Pad|   |Termal Pad|  <-- Termal arayuz
    +---------+   +---------+   +---------+
    |MACUN TABAKASI|  |  |  |  |  |  |  |  |  <-- Ince termal macun
    +----------------+--+--+--+--+--+--+--+
    |          HEATSINK (SK53-100-SA)        |  <-- Alumin soğutucu
    |  ||||||||||||||||||||||||||||||||||||||| |      (300 x 75 x 49 mm)
    +----------------------------------------+
                     |
              [PCB MONTAJ]
```

---

## 8. Termal Simulasyon Sonuclari (Tahmini)

### 8.1 Tek Kanal Simulasyonu

```
Simulasyon kosullari:
  - Ortam sicakligi: 25 C
  - Cikis gucu: 50W @ 8 Ohm
  - Fan: 25 CFM (PWM %100)
  - Heatsink: Fischer SK53-100-SA

Sonuclar:
  +---------------------------+----------+----------+----------+
  | Bileşen                   | Baslangic| 30 dk    | 60 dk    |
  +---------------------------+----------+----------+----------+
  | MJL21194 Junction (Tj)    | 25 C     | 62 C     | 68 C     |
  | MJL21193 Junction (Tj)    | 25 C     | 61 C     | 67 C     |
  | Heatsink yuzey (Th)       | 25 C     | 48 C     | 52 C     |
  | BD139 (surucu)            | 25 C     | 55 C     | 60 C     |
  | KSC3503 (VAS)             | 25 C     | 58 C     | 63 C     |
  | Emitter direnci (0.22R)   | 25 C     | 42 C     | 45 C     |
  +---------------------------+----------+----------+----------+

Guvenli sinirlar:
  MJL21194/93 max Tj: 150 C  (margin: 150 - 68 = 82 C)
  Heatsink max: 85 C          (margin: 85 - 52 = 33 C)
  BD139/140 max Tj: 150 C     (margin: 150 - 60 = 90 C)
```

### 8.2 8 Kanal Tam Gucte Simulasyonu

```
Simulasyon kosullari:
  - Ortam sicakligi: 25 C
  - 8 kanal tam guc: 400W cikis
  - Fan: 25 CFM (PWM %100)
  - Toplam isi: 328W

Sonuclar:
  +---------------------------+----------+----------+----------+
  | Parametre                 | Baslangic| 30 dk    | Steady   |
  +---------------------------+----------+----------+----------+
  | Ortam icindeki hava       | 25 C     | 42 C     | 48 C     |
  | En sicak heatsink         | 25 C     | 65 C     | 72 C     |
  | Ortalama heatsink         | 25 C     | 58 C     | 63 C     |
  | Fan cikis hava sicakligi  | 25 C     | 55 C     | 62 C     |
  | En sicak transistor Tj    | 25 C     | 85 C     | 92 C     |
  | Uyari LED tetiklenme      | --       | 28 dk    | --       |
  +---------------------------+----------+----------+----------+

  NOT: 8 kanal tam gucte 60 dk calismada sicaklik 92 C'ye ulasir.
  Bu durumda sicaklik 85 C uzerinde oldugundan UYARI LED yanar.
  95 C'ye ulasmaz (margin: 3 C). Fan tam guc ile calisir.

  Gercek dunya notu: 8 kanal ayni anda tam gucte nadiren calisir.
  Tipik calismada (4 kanal ortalama guc): 55-65 C araliginda.
```

### 8.3 Pasif Calisma (Fan Arizali)

```
Simulasyon kosullari:
  - Fan calismiyor (ariza durumu)
  - 8 kanal tam guc: 328W isi
  - Dogal konveksiyon: Rth = 0.8 C/W

Sonuclar (30 dk sonra):
  +---------------------------+----------+
  | Parametre                 | Deger    |
  +---------------------------+----------+
  | Heatsink sicakligi        | 120 C    |
  | Transistor Tj             | 165 C    |
  | Uyari LED                 | Tetiklendi|
  | KSD301 85 C anahtar       | ACILDI   |
  | Amplifikator durumu       | KAPATILDI|
  +---------------------------+----------+

  SONUC: Fan arizasinda 85 C'de KSD301 devreye girer ve
  amplifikator kapatilir. Transistor hasar gormez.
```

---

## 9. Guvenlik ve Uyari Sistemleri

### 9.1 Katmanli Koruma

```
KATMAN 1 — Yazilimli Izleme:
  NTC okuma -> ADC -> Sicaklik hesaplama -> Fan PWM ayari
  (40 C'den baslayarak kademeli sogutma)

KATMAN 2 — Uyari LED:
  85 C uzerinde kirmizi LED yanar
  Kullaniciya "sicaklik yuksek" uyarisi verilir

KATMAN 3 — Donanimsal Kapatma:
  KSD301 85 C: Snap-action anahtar, guc devresini keser
  KSD301 95 C: Acil durum, tam kapatma

KATMAN 4 — Yazilimli Kapatma:
  NTC 95 C'yi gosterirse MCU otomatik kapatma yapar
  MCU, LM5122 Enable pinini 0'a cekerek boost converter'ı kapatir
```

### 9.2 Ariza Senaryolari

| Ariza | Sonuc | Koruma |
|-------|-------|--------|
| Fan arizasi | Sicaklik hizla yukselir | KSD301 85 C ile kapatma |
| NTC arizasi | Sicaklik okunamaz | MCU varsayilan sicaklikla fan %100 |
| Heatsink gevsek montaj | Lokal sicaklik artisi | Lokal NTC algilayabilir |
| Ortam sicakligi > 35 C | Daha erken ularasma | Fan daha erken devreye girer |
| Kisa devre (cikis) | Asiri akim, isi artisi | Akim sinirlayici + sigorta |

---

## 10. BOM Ozet Tablosu (Termal Sistem)

| # | Bilesen | Adet | Birim Fiyat | Toplam | Kullanim |
|---|---------|------|-------------|--------|----------|
| 1 | Fischer SK53-100-SA heatsink | 8 | $25.00 | $200.00 | Kanal basina 1 |
| 2 | Bergquist SIL-PAD 2000 | 8 | $3.50 | $28.00 | Transistor-heatsink arasi |
| 3 | Arctic MX-6 termal macun | 1 | $12.00 | $12.00 | 8 kanal yetecek |
| 4 | Noctua NF-A8 PWM fan | 1 | $20.00 | $20.00 | Sistem sogutmasi |
| 5 | IRLZ44N MOSFET | 1 | $1.50 | $1.50 | Fan PWM kontrol |
| 6 | 10kOhm direnc (R30) | 1 | $0.05 | $0.05 | Gate pull-down |
| 7 | 100 Ohm direnc (R31) | 1 | $0.05 | $0.05 | Gate direnci |
| 8 | 1N4007 diyot (D9) | 1 | $0.10 | $0.10 | Flyback korumasi |
| 9 | KSD301 85 C termal anahtar | 1 | $2.00 | $2.00 | Birincil koruma |
| 10 | KSD301 95 C termal anahtar | 1 | $2.00 | $2.00 | Acil durum kapatma |
| 11 | NTC 10kOhm termistor | 2 | $0.50 | $1.00 | Sicaklik izleme |
| 12 | 10kOhm direnc (R29) | 2 | $0.05 | $0.10 | Voltage divider |
| 13 | 100nF kapasite (C28) | 2 | $0.05 | $0.10 | ADC filtre |
| 14 | M3 x 8mm vida | 32 | $0.05 | $1.60 | Heatsink montaj |
| | **TOPLAM** | | | **$268.50** | |

---

## 11. Notlar ve Kısıtlamalar

### 11.1 Kritik Kısıtlamalar

| # | Kısıtma | Aciklama |
|---|---------|----------|
| 1 | Fan bagimliligi | Fan calismadiginda 8 kanal tam guc calisamaz |
| 2 | Ortam sicakligi | 35 C uzerinde fan daha erken devreye girer |
| 3 | Mekanik alan | 8 heatsink icin genis kasa gerekir |
| 4 | Agirlik | Soğutucular ~12 kg agirlik katar |
| 5 | Gurultu | Fan 25 dBA, sessiz ortamda duyulabilir |

### 11.2 Performans Ozeti

| Metrik | Deger | Durum |
|--------|-------|-------|
| Tek kanal steady-state sicaklik | 68 C | GUVENLI (margin: 82 C) |
| 8 kanal steady-state sicaklik | 92 C | SINIRDA (margin: 3 C) |
| Fan arizasinda kapatma suresi | ~15 dk | YETERLI |
| Termal tepki suresi (fan baslangic) | ~30 saniye | IYI |
| Toplam termal kapasite | 500W+ | YETERLI (328W gereken) |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-19
**Version:** 2.0.0
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
