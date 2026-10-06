---
title: "DC Offset ve Termal Koruma - k067-koruma-devreleri"
type: architecture-sublayer
category: architecture
version: 1.0.0
status: active
authority: "Vault (.ai/) SSOT - verbatim aktarım: _backup/arch-2026-10-06_1057/architecture/"
updated: 2026-10-06
---

# DC Offset ve Termal Koruma

> Klasör: `k067-koruma-devreleri` · Dosya: `dc-offset-ve-termal-koruma.md`
> Sorumlu persona: `audio-hardware-engineer` (CoreMusic Audio Hardware Engineer)
> Kaynak: `_backup/arch-2026-10-06_1057/architecture/` — aktarım bölüm bazında L aralığı ile kanıtlanmıştır.

## Genel Bakış

DC offset koruma devresi ve çalışma prensibi, thermal shutdown profili, NTC sıcaklık izleme devresi ve sıcaklık-hassasiyet tablosu; Class AB bias/termal hesaplama ve K1.6 PCB & Termal bölümü.

## Kapsam ve Sınırlar

- **Kapsam:** DC offset tespiti/koruması, termal shutdown, sıcaklık izleme ve termal hesaplama.
- **Kapsam dışı:** PWM fan kontrolü (→ [[../k071-termal-yonetim/index]]); overcurrent/short circuit (→ [[koruma-devreleri-turleri.md]]).
- **Bağlı olduğu klasör:** [[index.md]]
- **Çapraz referanslar:** [[../k071-termal-yonetim/index]] · [[../k069-hoparlor-dizilimi/index]]

## Kaynak Aktarımı (Verbatim)

> Başlık hiyerarşisi ve içerik kaynaktan değiştirilmeden kopyalanmıştır; her bloğun üstündeki `> Aktarım:` satırı kaynak dosyanın disk satır aralığını gösterir.
### `_backup/arch-2026-10-06_1057/architecture/k1-donanim/koruma-devreleri.md` (189 satır)

#### 1. koruma-devreleri.md — DC offset koruması (devre şeması + çalışma prensibi)

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/koruma-devreleri.md` - L25-L68

## DC Offset Koruması

### Devre Şeması

```
Output Node (Amplifier Output)
     │
     ├─ C1 ──▶ 10µF (DC block)
     │           │
     │       ┌───┴───┐
     │       │  R1   │  100kΩ
     │       └───┬───┘
     │           │
     │       ┌───┴───┐
     │       │  D1   │  BAT54S (±0.7V clamp)
     │       └───┬───┘
     │           │
     │       ┌───┴───┐
     │       │ Comp  │  Comparator (LM393)
     │       │       │
     │       └───┬───┘
     │           │
     │           ├────▶ Relay Driver (BC337)
     │           │         │
     │           │         ▼
     │           │    Speaker Relay
     │           │    (disconnect)
     │           │
     │           └────▶ Fault LED (Red)
     │
     └─ R2 ──▶ Feedback Network
```

### Çalışma Prensibi

```
1. Amplifikatör çıkışı DC coupled olarak comparator girişine bağlanır
2. C1 (10µF) DC component'i ayırır
3. Comparator, çıkış voltajını ±1V referans ile karşılaştırır
4. |Vout| > 1V olduğunda comparator çıkışı HIGH olur
5. BC337 MOSFET'i aktif eder ve speaker relay'ı açar
6. 0.5s gecikme (RC time constant) ile yanlış tetikleme önlenir
```


#### 2. koruma-devreleri.md — thermal shutdown (devre şeması + thermal profile)

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/koruma-devreleri.md` - L107-L140

## Thermal Shutdown

### Devre Şeması

```
NTC Thermistor (10kΩ @ 25°C)
     │
     └─▶ Voltage Divider
              │
              ├─ R_pullup = 10kΩ
              │
              └─▶ Comparator (LM393)
                      │
                      ├─ Reference: 0.35V (85°C)
                      │
                      └─▶ RC Delay (5s)
                              │
                              └─▶ Latch Circuit
                                      │
                                      ├─▶ Speaker Relay (disconnect)
                                      │
                                      └─▶ PSU Enable (shutdown)
```

### Thermal Profile

| Sıcaklık (°C) | Davranış |
|---------------|----------|
| < 70 | Normal çalışma |
| 70-80 | Uyarı LED (sarı) |
| 80-85 | Fan maksimum hız |
| > 85 | Thermal shutdown |
| < 75 | Otomatik restart |


### `_backup/arch-2026-10-06_1057/architecture/k1-donanim/termal-yonetim.md` (169 satır)

#### 3. termal-yonetim.md — sıcaklık izleme (NTC sensör devresi + hassasiyet tablosu)

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/termal-yonetim.md` - L71-L101

## Sıcaklık İzleme

### NTC Sensör Devresi

```
              +3.3V
               │
           ┌───┴───┐
           │  R1   │  10kΩ (Pull-up)
           └───┬───┘
               │
           ┌───┴───┐
           │  NTC  │  10kΩ @ 25°C
           │ Sensor│  β = 3950
           └───┬───┘
               │
               ├──────▶ ADC Input (PCM3168A spare channel)
               │
              GND
```

### Sıcaklık-Hassasiyet Tablosu

| Sıcaklık (°C) | NTC Direnci (kΩ) | ADC Voltajı (V) | ADC Değeri |
|---------------|-------------------|-----------------|------------|
| 25 | 10.0 | 1.65 | 2048 |
| 50 | 3.60 | 0.89 | 1114 |
| 75 | 1.52 | 0.46 | 576 |
| 85 | 1.14 | 0.35 | 437 |
| 100 | 0.87 | 0.28 | 350 |


### `_backup/arch-2026-10-06_1057/architecture/k1-donanim/README.md` (805 satır)

#### 4. README.md — §4.2 Bias Ayar Prosedürü + §4.3 Termal Hesaplama

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/README.md` - L163-L185

### 4.2 Bias Ayar Prosedürü

| Adım | İşlem | Değer |
|------|-------|-------|
| 1 | Güç kaynağı ayarla | ±35V DC |
| 2 | Multimetre çıkışa bağla | DC offset ölç |
| 3 | Bias potansiyometresi ayarla | 0V DC offset hedefle |
| 4 | Sıcaklık stabilizasyonu | 5-10 dk bekle |
| 5 | Son kontrol | <0.5V DC offset |

### 4.3 Termal Hesaplama

| Parametre | Değer |
|-----------|-------|
| Güç (kanal başına) | 50W @ 8Ω |
| Verimlilik | ~%65 (Class AB) |
| Isı (kanal başına) | ~17.5W |
| Toplam ısı (8 kanal) | ~140W |
| Heatsink gereksinimi | >140W/C° thermal resistance |
| Fan gereksinimi | 80mm PWM, >50 CFM |

---


#### 5. README.md — K1.6 PCB & Termal

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/README.md` - L502-L529

### K1.6 — PCB & Termal

| Kod | Ad | Kanıt (dosya · satır) |
|-----|----|-----------------------|
| **K1.6.1** | PCB Tasarım (3. katman) | pcb-tasarim.md — 8 yaprak |
| K1.6.1.1 | Genel Bakış | pcb-tasarim.md L10 |
| K1.6.1.2 | Teknik Spesifikasyonlar | pcb-tasarim.md L14 |
| K1.6.1.3 | Katman Stackup | pcb-tasarim.md L30 |
| K1.6.1.4 | Star Grounding Sistemi | pcb-tasarim.md L76 |
| K1.6.1.5 | Controlled Impedance | pcb-tasarim.md L101 |
| K1.6.1.6 | Bileşen Yerleşimi | pcb-tasarim.md L142 |
| K1.6.1.7 | Impedans Hesaplaması | pcb-tasarim.md L103 |
| K1.6.1.8 | Differential Pair (I2S, USB) | pcb-tasarim.md L123 |
| **K1.6.2** | Termal Yönetim (3. katman) | termal-yonetim.md — 13 yaprak |
| K1.6.2.1 | Genel Bakış | termal-yonetim.md L10 |
| K1.6.2.2 | Teknik Spesifikasyonlar | termal-yonetim.md L14 |
| K1.6.2.3 | Isı Dağılım Analizi | termal-yonetim.md L26 |
| K1.6.2.4 | Soğutucu Seçimi | termal-yonetim.md L46 |
| K1.6.2.5 | Sıcaklık İzleme | termal-yonetim.md L71 |
| K1.6.2.6 | Fan Kontrolü | termal-yonetim.md L102 |
| K1.6.2.7 | Termal Pad Uygulaması | termal-yonetim.md L127 |
| K1.6.2.8 | Toplam Isı (8 Kanal) | termal-yonetim.md L38 |
| K1.6.2.9 | Alüminyum Ekstrüzyon Soğutucu | termal-yonetim.md L48 |
| K1.6.2.10 | Termal Ped | termal-yonetim.md L61 |
| K1.6.2.11 | NTC Sensör Devresi | termal-yonetim.md L73 |
| K1.6.2.12 | Sıcaklık-Hassasiyet Tablosu | termal-yonetim.md L92 |
| K1.6.2.13 | PWM Fan Driver | termal-yonetim.md L104 |


### `_backup/arch-2026-10-06_1057/architecture/k1-donanim/index.md` (96 satır)

#### 6. index.md — durum: implementasyon

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/index.md` - L83-L96

## Durum: Implementasyon

**K1 Katman Durumu**: 🟡 Tasarım Aşamasında

| Alt Modül | Durum | Not |
|-----------|-------|-----|
| XMOS XU316 | 🟢 Hazır | USB Audio Class 2.0 firmare mevcut |
| PCM3168A ADC | 🟢 Hazır | Pin konfigürasyonu belirlendi |
| AK4458 DAC | 🟢 Hazır | DSD modu yapılandırıldı |
| Class AB Amp | 🟡 Devam | Simülasyon aşamasında |
| Güç Kaynağı | 🟡 Devam | LM5122 layout çalışıyor |
| PCB | 🔴 Başlamadı | 6-katman stackup planlandı |
| Soğutma | 🔴 Başlamadı | Termal simülasyon bekliyor |
| Koruma Devreleri | 🔴 Başlamadı | Şematiği hazır, layout yok |
## İlgili Dosyalar

[[index.md]] · [[koruma-devreleri-turleri.md]] · [[../k071-termal-yonetim/index]]
