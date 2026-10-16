---
type: technical-spec
project: Neva Engine
component: eq-user-modes
updated: 2026-06-03
---

# Neva Engine — EQ User Modes

> 5 kullanıcı modu: Ev, Araba, TV, Stüdyo, Gamer.
> Kayıt sırasında seçilir, ayarlardan değiştirilebilir.
> Her mod için varsayılan EQ tipi, UI ve özellikler farklıdır.

---

## 1. Mod Tanımları

| Mod | Varsayılan EQ | UI Stili | Target Kullanıcı |
|-----|-------------|---------|-----------------|
| **Ev Kullanıcısı** | 5-band Graphic | Büyük butonlu, basit | Günlük müzik dinleyici |
| **Araba** | 10-band Graphic | Büyük touch, gece modu | Sürücü |
| **TV** | 5-band + Presets | Uzaktan kumanda, büyük font | Oturma odası |
| **Stüdyo** | 31-band + 6-cell Parametric | Tam kontrol, spectrum analyzer | Ses mühendisi |
| **Gamer** | 10-band + HRTF + Low Latency | Karanlık tema, hızlı toggle | Oyuncu |

---

## 2. Kayıt Sırasında Sorulacak

```
1. "Nasıl kullanacaksınız?"
   ○ Ev kullanıcısı (günlük müzik dinleme)
   ○ Arabada (araç ses sistemi)
   ○ TV'de (ev sinema sistemi)
   ○ Profesyonel stüdyo (ses mühendisliği)
   ○ Oyun için (gamer)

2. "Ne kadar detaylı EQ kontrolü istersiniz?"
   ○ Basit (2 bant: bass/treble)
   ○ Orta (5-10 bant)
   ○ Profesyonel (31 bant + parametric)

3. "Müzik türü tercihiniz?" (opsiyonel, AI için)
   ○ Arabesk / Pop / Rock / Electronic / Hepsi

→ Bu seçimlere göre varsayılan UI ve EQ tipi set edilir.
```

---

## 3. Ev Kullanıcısı Modu

| Özellik | Değer |
|---------|-------|
| EQ Tipi | 5-band Graphic (varsayılan), 2-band veya 10-band seçilebilir |
| UI | Büyük butonlar, basit slider'lar, tek tıkla preset |
| Presets | Arabesk, Pop, Rock, Klasik, Film, Ses |
| AI | Auto-EQ aktif, genre detection |
| Ayarlar | Bass/Treble hızlı kontrol, volume normalization |

---

## 4. Araba Modu

| Özellik | Değer                                                        |
| ------- | ------------------------------------------------------------ |
| EQ Tipi | 10-band Graphic (varsayılan)                                 |
| UI      | Büyük touch hedefler (minimum 48px), yüksek kontrast         |
| Özel    | **Gece modu** (kırmızı/siyah tema, düşük parlaklık)          |
| Özel    | **Sürüş güvenliği** (basit kontroller, dikkat dağıtmayan UI) |
| Presets | Rock, Pop, HipHop, Electronic, Voice (radyo)                 |
| AI      | Yol gürültüsüne göre adaptif EQ (mikrofon input)             |

### Araba EQ Profili

```cpp
// Yol gürültüsü kompanzasyonu
// 60-120Hz: motor gürültüsü → slight cut
// 200-500Hz: yol gürültüsü → cut
// 2-4kHz: vokal → boost (anlaşılırlık için)
```

---

## 5. TV Modu

| Özellik | Değer |
|---------|-------|
| EQ Tipi | 5-band + Presets |
| UI | Uzaktan kumanda dostu, büyük font |
| Presets | Film, Müzik, Haber/Ses, Oyun, Gece |
| AI | İçerik türüne göre otomatik geçiş |

### TV Preset'leri

| Preset | Bass | Mid | Treble | Diyalog Boost |
|--------|------|-----|--------|-------------|
| Film | +4dB | 0dB | +1dB | +2dB (center channel) |
| Müzik | +3dB | +1dB | +2dB | 0dB |
| Haber/Ses | -2dB | +3dB | 0dB | +4dB |
| Gece | -4dB | 0dB | -2dB | +3dB (düşük ses) |

---

## 6. Stüdyo Modu

| Özellik | Değer |
|---------|-------|
| EQ Tipi | 31-band Graphic + 6-cell Parametric + Custom mix |
| UI | Tam kontrol paneli, detaylı metrikler |
| Analiz | Spectrum analyzer, LUFS meter, phase meter, correlation meter |
| Plugin | VST3/LV2/CLAP hosting (per-strip + master) |
| AI | Room calibration, reference track matching |

### Stüdyo UI Panelleri

```
┌─────────────────────────────────────────────────────────────┐
│ [Spectrum Analyzer (FFT, 20Hz-20kHz)]    [LUFS Meter]       │
├─────────────────────────────────────────────────────────────┤
│ [31-band Graphic EQ Sliders]                                 │
├─────────────────────────────────────────────────────────────┤
│ [6-cell Parametric: Freq | Q | Gain | Type | Bypass]        │
├─────────────────────────────────────────────────────────────┤
│ [FX Chain: Denoiser|Gate|Comp|Chorus|Spatial|Limiter]       │
├─────────────────────────────────────────────────────────────┤
│ [Plugin Slots: VST3/AU/LV2/CLAP per Strip]                  │
└─────────────────────────────────────────────────────────────┘
```

---

## 7. Gamer Modu

| Özellik | Değer |
|---------|-------|
| EQ Tipi | 10-band Graphic |
| UI | Karanlık gamer teması, hızlı toggle'lar |
| Özel | **Footsteps Enhancer** (2-4kHz band boost, narrow Q) |
| Özel | **3D Spatial Audio** (HRTF binaural, Dolby Atmos virtual) |
| Özel | **Low-Latency Mode** (< 5ms ASIO buffer) |
| Özel | **Voice Chat Ducking** (Discord/TeamSpeak → oyun sesi -6dB) |
| Presets | FPS, RPG, Racing, MOBA, Horror |

### Gamer EQ Profilleri

| Oyun Türü | Bass | Low-Mid | Mid | High-Mid | High | Özel |
|----------|------|---------|-----|---------|------|------|
| **FPS** | -3dB | +1dB | +4dB | +5dB | +2dB | Footsteps @ 3kHz +6dB |
| **RPG** | +4dB | +2dB | +1dB | +2dB | +3dB | Cinematic sound |
| **Racing** | +5dB | +1dB | 0dB | +1dB | +2dB | Engine roar |
| **MOBA** | +1dB | +2dB | +3dB | +3dB | +2dB | Ability clarity |
| **Horror** | +5dB | +1dB | -1dB | +3dB | -1dB | Suspense bass |

### Gamer Mode Activation

```
Gamer modu:
  1. Gamer teması yüklendiğinde veya
  2. Ayarlardan manuel "Gamer Mode" toggle açıldığında AKTIF
  3. Low-latency ASIO profili otomatik yüklenir
  4. Voice chat ducking devreye girer
  5. Oyun türüne göre EQ preset seçilir
```

---

## 8. Modlar Arası Geçiş

```json
// WebSocket komutu
{"cmd":"mode.set","params":{"mode":"studio"}}

// Her mod değişiminde:
// 1. EQ band sayısı ve tipi değişir
// 2. UI teması değişir
// 3. Preset listesi güncellenir
// 4. FX zinciri yeniden yapılandırılır
```

---

## 9. Ayarlardan Band Sayısı Seçimi

Kullanıcı istediği zaman ayarlardan EQ band sayısını değiştirebilir:

```
Settings → EQ Configuration → Band Count:
  ○ 2-band (Basic: Bass + Treble)
  ○ 5-band (Standard)
  ○ 10-band (Enhanced)
  ○ 31-band (Professional)
  ○ 6-cell Parametric
  ○ Custom (mix graphic + parametric)

Info notu: "Daha fazla bant = daha hassas kontrol, 
ancak ayarlaması daha karmaşık. Ev kullanıcıları için 
5-band önerilir. Profesyoneller 31-band veya parametric 
tercih edebilir."
```

---

*Neva Engine EQ User Modes v1 · 2026-06-03*
