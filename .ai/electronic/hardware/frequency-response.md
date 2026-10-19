---
type: electronic
category: frequency-response
title: "CoreMusic — Frequency Response Measurement"
date: 2026-08-09
updated: 2026-10-13
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Frequency Response

**See also:** [[electronic/hardware/index]] · [[electronic/hardware/test-protocols]]

---

## 1. Amaç

Frequency Response Measurement, CoreMusic ELECTRONICS platformunun frekans yanıtı ölçüm protokollerini tanımlar.

---

## 2. Ölçüm Parametreleri

| Parametre | Değer |
|-----------|-------|
| Frekans Aralığı | 20Hz - 20kHz |
| Ölçüm Noktası | 1/3 oktav |
| Düzey | -3dB'den fazla sapma yok |
| Referans | 0dB @ 1kHz |

---

## 3. Ölçüm Setupı

```
PC (Signal Generator) → DAC → Amplifier → Hoparlör → Measurement Mic
                                                         │
                                                    Audio Interface
                                                         │
                                                    PC (Analyzer)
```

---

## 4. Frekans Yanıtı Hedefleri

| Frekans | Hedef | Tolerans |
|---------|-------|----------|
| 20Hz | 0dB | ±1dB |
| 100Hz | 0dB | ±0.5dB |
| 1kHz | 0dB | ±0.5dB |
| 10kHz | 0dB | ±0.5dB |
| 20kHz | 0dB | ±1dB |

---

## 5. ADR Referansları

| ADR | Konu |
|-----|------|
| [[ADR-006-performance-targets]] | Performans hedefleri |

---

## 6. Ölçüm Yöntemleri

### 6.1 Sweep Yöntemi

| Parametre | Değer | Açıklama |
|-----------|-------|----------|
| Sinyal tipi | Sinüs (sine sweep) | 20Hz → 20kHz logaritmik |
| Adım | 1/3 oktav (24 nokta) veya 1/6 oktav (48 nokta) | Frekans çözünürlüğü |
| Düzey | Sabit (0dBFS) veya sabit genlik | Amplitude linearity testi |
| Örnekleme süresi | Her noktada 100ms (settling) | Transiyant bekleme |
| Pencereleme | Hanning (FFT) | Leakage önleme |

**1/3 oktav ayrımı:** Her oktav 3 noktada ölçülür (f, f×2^(1/3), f×2^(2/3)) →
20Hz-20kHz = 10 oktav × 3 = 30 nokta (1/3 oktav) veya 60 nokta (1/6 oktav).

### 6.2 Multi-tone (ESS) Yöntemi

| Özellik | Değer | Avantaj |
|---------|-------|---------|
| Sinyal | Exponential Sine Sweep (ESS) | Tek geçişte tüm band |
| Süre | 6-10 sn | Hızlı ölçüm |
| SNR | Sweep sonrası post-processing ile yüksek | Dinamik aralık |
| Impulse response | FFT ile ayrıştırma | Zaman alanı bilgisi |

**ESS avantajı:** Tek sweep ile hem frekans yanıtı hem impulse response (odanın
reverberation'ı) elde edilir; ayrıca harmonikler sweep'ten ayrıştırılabilir (THD
ölçümü paralel).

### 6.3 Ölçüm Belirsizliği

| Kaynak | Tipik etki | Azaltma |
|--------|-----------|---------|
| Mikrofon frekans düzeltmesi | ±1-2dB (20Hz-20kHz) | Kalibrasyon dosyası (FRF) |
| Oda akustiği | ±3-6dB (odadaki dip/tepe) | Kturnstile ölçüm, near-field |
| Mikrofon pozisyonu | ±1-2dB | Sabit mesafe, eksen hizası |
| Termal drift | ±0.5dB (ısınma) | Burn-in (thermal §15) |
| ADC/DAC düzeltmesi | ±0.5dB | Kalibrasyon |

**Kturnstile (near-field) ölçüm:** Hoparlör mikrofona 0.5-1m mesafede yerleştirilir;
oda akustiği etkisi minimuma iner. Ancak subwoofer/woozer için near-field,
tweeter için far-field gerekebilir (crossover bölgesi).

---

## 7. Frekans Yanıtı Analizi

### 7.1 Dip/Tepi Yorumu

| Belirti | Olası Neden | Konum |
|---------|------------|-------|
| 20-50Hz dip | Hoparlör free-air resonance (Fs) | Cabinet tuning |
| 100-300Hz tepi | Room mode (odalı resonans) | Oda geometrisi |
| 2-5kHz dip | Crossover phase cancellation | Crossover tasarımı |
| 10-20kHz dalgalanma | Tweeter cone breakup | Driver limiti |
| Tüm bantta eğim | EQ slope (tilt) | DSP/ton kontrolü |

### 7.2 Faz Yanıtı

| Parametre | Değer | Açıklama |
|-----------|-------|----------|
| Faz kayması (20Hz-20kHz) | <±90° (hedef) | Minimum phase distortion |
| Group delay | <20ms (subwoofer crossover'da) | Zaman tutarlılığı |
| Crossover faz kayması | 180° (12dB/oct) veya 360° (24dB/oct) | Filter order |

### 7.3 Harmonik Bozulma ile Frekans Yanıtı Etkileşimi

THD+N frekans yanıtı ölçümüne dahil edilir; yüksek THD, "düz" yanıtı yanıltır:

| Frekans | THD+N etkisi | Düzeltme |
|---------|-------------|----------|
| 20-100Hz | HUM + THD (gürültü) | A-wtd yerine unweighted |
| 1-5kHz | Düşük THD | Minimal etki |
| 10-20kHz | THD artışı (driver) | Harmonik subtraction |

---

## 8. Ölçüm Ekipmanları

| # | Ekipman | Amaç | Alternatif |
|---|---------|------|-----------|
| 1 | Measurement mic (düz) | Odasal ölçüm | Studio condenser (kalibrasyonlu) |
| 2 | Audio interface (24-bit) | ADC (ölçüm geri besleme) | PCM3168A (§6 hardware) |
| 3 | Signal generator (PC) | Sweep/multi-tone üretimi | Hardware generator |
| 4 | FFT analyzer | Frekans ayrıştırma | MATLAB/Python (scipy) |
| 5 | Reference mic calibrator | 94dB @ 1kHz referans | Sonar calibrator |
| 6 | Kturnstile / near-field aparat | Oda etkisi azaltma | Odak (anechoic) |

---

## 9. Frekans Yanıtı Hedef Tablosu (Detaylı)

| Frekans | CoreMusic (stüdyo) | Ev sinema | Tolerans |
|---------|-------------------|-----------|----------|
| 20Hz | 0dB | -3dB (sub) | ±1dB |
| 50Hz | 0dB | 0dB | ±0.5dB |
| 100Hz | 0dB | 0dB | ±0.5dB |
| 500Hz | 0dB | 0dB | ±0.3dB |
| 1kHz | **0dB (referans)** | 0dB | ±0.2dB |
| 5kHz | 0dB | 0dB | ±0.3dB |
| 10kHz | 0dB | 0dB | ±0.5dB |
| 20kHz | 0dB | -1dB | ±1dB |

---

## 10. Repo / Kaynak Kanıtı

**Repo'da donanım/devre kodu YOK — kaynak: .ai/electronic/**

CoreMusic deposunda frekans yanıtı ölçüm kodu (C++/Python) bulunmaz; protokol yalnızca
bu vault dosyasında yaşar. Depodaki donanıma en yakın yazılım yüzeyi AI orkestrasyon
katmanının placeholder alanlarıdır:

| Dosya | Satır | Kanıt | Doğrulama |
|-------|-------|-------|-----------|
| `shared/src/AI/AIEngine.php` | 138 | `public function analyzeHardware(string $deviceId): array` | `Select-Object -Index (137)` ile doğrulandı |
| `shared/src/AI/AIEngine.php` | 142-143 | `$snr = 0.0;` · `$thd = 0.0;` — placeholder (frekans verisi YOK) | `Select-Object -Index (141..142)` ile doğrulandı |

**Sonuç:** Frekans yanıtı ölçümü vault protokolü olarak tanımlıdır (§6-§9); gerçek
cihaz entegrasyonu PLANNED (Neva Engine C++20, L0 ALTYAPI PLANNED).

---

## 11. Risk Matrisi

| # | Risk | Olasılık | Etki | Önlem |
|---|------|----------|------|-------|
| 1 | Oda modu (dip/tepı) | Yüksek | MEDIUM | Kturnstile, near-field |
| 2 | Mikrofon kalibrasyonu eksik | Orta | HIGH | Reference calibrator |
| 3 | Crossover faz kayması | Orta | MEDIUM | Faz ölçümü, allpass |
| 4 | Termal drift | Düşük | MEDIUM | Burn-in (thermal §15) |
| 5 | Sweep settling yetersiz | Düşük | LOW | 100ms bekleme |
| 6 | Harmonik interference | Düşük | MEDIUM | Harmonik subtraction |

---

## 12. Terimler (Glossary)

| Terim | Tanım |
|-------|-------|
| Frekans yanıtı | Sistemın frekans başına kazanç/faz davranışı |
| 1/3 oktav | Frekans ayrımı (f, f×2^(1/3), f×2^(2/3)) |
| ESS | Exponential Sine Sweep — tek geçişte frekans + impulse |
| Group delay | Fazın frekans türevi (zaman gecikmesi) |
| Near-field | Yakın alan ölçümü (oda etkisi minimum) |
| Kturnstile | Oda akustiği düzeltme yöntemi |
| Cone breakup | Tweeter/woofer konisinin resonansı (yüksek frekans) |
| Crossover | Bant ayrımı filtresi (tweeter/woofer) |

---

## 13. Web Kaynakları (Doğrulanmış URL Tablosu)

| İddia | URL | 2026-10-13 | Durum |
|-------|-----|------------|-------|
| XTZ Room Analyzer II Pro — oda akustiği ölçüm cihazı (near-field/kturnstile referansı) | https://www.xtz.com/product/room-analyzer-ii-pro | 2026-10-13 | HTTP 200 doğrulandı |
| miniDSP UMIK-1 — kalibrasyonlu measurement mic (USB, frekans düzeltme dosyası) | https://www.minidsp.com/products/acoustic-measurement/umik-1 | 2026-10-13 | HTTP 200 doğrulandı |

Doğrulama yöntemi: `Invoke-WebRequest -Method Head -UseBasicParsing -Uri $u -TimeoutSec 20`.

---

## 14. Hoparlör Sistemi Frekans Yanıtı (Bileşen Bazlı)

### 14.1 Driver Sınıflandırması

| Sınıf | Frekans Aralığı | Tipik Kullanım |
|-------|----------------|----------------|
| Subwoofer | 20-80Hz | LF, LFE |
| Woofer | 80Hz-3kHz | MF |
| Midrange | 300Hz-5kHz | Orta bant |
| Tweeter | 3kHz-20kHz+ | HF |
| Full-range | 60Hz-20kHz | Tek sürücülü sistem |

### 14.2 Crossover Frekansları

| Crossover | Frekans | Sıra | Faz Kayması |
|-----------|---------|------|-------------|
| Sub ↔ Main | 80Hz | 24dB/oct (Linkwitz-Riley) | 0° (eşleşmiş) |
| Woofer ↔ Tweeter | 2-3kHz | 12-24dB/oct | 180°/360° |

**Linkwitz-Riley (LR) tercih edilir:** Butterworth (BW) yerine LR, crossover
frekansında 0dB toplam yanıt (BW'da 3dB tepi) ve faz kayması eşleşmesi sağlar.

### 14.3 7.1 Sistem Kanal Dengeleme

| Kanal | Frekans Hedefi | Tolerans |
|-------|---------------|----------|
| Front L/R | 20Hz-20kHz düz | ±0.5dB |
| Center | 80Hz-20kHz düz | ±0.5dB |
| Surround L/R | 80Hz-20kHz düz | ±0.5dB |
| Surround Back L/R | 100Hz-20kHz düz | ±0.5dB |
| Subwoofer (LFE) | 20-80Hz düz | ±1dB |

**Kanal tutarlılığı:** Tüm kanallar arası 1kHz'de ±0.5dB sapma (kullanıcı algısı
için kritik).

---

## 15. Frekans Yanıtı Ölçüm Yazılımı

### 15.1 Yazılım Araçları

| # | Araç | Platform | Özellik |
|---|------|----------|---------|
| 1 | Room EQ Wizard (REW) | Windows/macOS/Linux | Ücretsiz, sweep + ESS |
| 2 | ARTA | Windows | Impulse + frekans |
| 3 | MATLAB (Audio Toolbox) | Çok platformlu | Script tabanlı |
| 4 | Python (scipy.signal) | Çok platformlu | FFT, filtre analizi |
| 5 | VituixCAD | Windows | Simülasyon + ölçüm |

### 15.2 Python Ölçüm Örneği (scipy)

```python
import numpy as np
from scipy import signal
from scipy.io import wavfile

# Sweep okuma + FFT
fs, x = wavfile.read('sweep.wav')
X = np.fft.rfft(x)
f = np.fft.rfftfreq(len(x), 1/fs)
mag_db = 20 * np.log10(np.abs(X) + 1e-12)
# 1kHz referansına normalize
ref_idx = np.argmin(np.abs(f - 1000))
mag_db -= mag_db[ref_idx]
```

### 15.3 Otomatik Raporlama

| Alan | İçerik |
|------|--------|
| Frekans aralığı | 20Hz-20kHz |
| Ölçüm tipi | Sweep / ESS |
| Ortalama yanıtı | ±dB (referans 1kHz) |
| Maksimum sapma | dB + frekans |
| Dip/tepo listesi | Frekans + genlik |

---

## 16. Frekans Yanıtı Simülasyonu

### 16.1 Hoparlör Simülasyon Parametreleri

| Parametre | Değer | Açıklama |
|-----------|-------|----------|
| Fs (free-air resonance) | 30-60Hz (woofer) | Driver resonansı |
| Qts | 0.3-0.7 | Toplam damping |
| Vas | 20-100L | Eşdeğer hacim |
| Xmax | 5-15mm | Maksimum diyafram hareketi |
| Sensitivity | 86-92dB @ 2.83V/1m | Verim |

### 16.2 Kapalı Kutu (Sealed Box) Hesabı

```
Fb = Fs × √(1 + Vas/Vb)
Qb = Qts × √(1 + Vas/Vb)
```

| Vb (kutu hacmi) | Fb (kutu resonansı) | Qb (damping) |
|------------------|---------------------|---------------|
| 0.5 × Vas | 1.22 × Fs | 1.41 × Qts |
| 1.0 × Vas | 1.41 × Fs | 1.73 × Qts |
| 2.0 × Vas | 1.87 × Fs | 2.24 × Qts |

### 16.3 Bass Reflex (Ported) Hesabı

```
Fb = Fs × √(Vb + Vas/QL) / Vb)  (basitleştirilmiş)
```

| Parametre | Değer |
|-----------|-------|
| Port frekansı | Fb = Fs (QB7 hedefi) |
| Port çapı | ≥ 2× sürücü çapı (hız limiti) |
| Port uzunluğu | Tuning frekansına göre hesap |

**Fark:** Sealed box 12dB/oct düşük geçiş (düzgün faz), bass reflex 24dB/oct
(faz kayması, port resonansı).

---

## 17. Frekans Yanıtı Ölçüm Protokolü (Kabul Testi)

### 17.1 Test Setupı

| Adım | Aksiyon | Beklenen |
|------|---------|----------|
| 1 | Referans mic kalibrasyonu | 94dB @ 1kHz ±0.5dB |
| 2 | Near-field / kturnstile konumu | 0.5-1m, eksen hizası |
| 3 | Sweep 20Hz-20kHz (1/3 oktav) | 30 nokta |
| 4 | Referans normalize (1kHz = 0dB) | ±0.2dB |
| 5 | Nokta başına ortalama (3×) | Gürültü azaltma |

### 17.2 Kabul Kriterleri

| Kriter | Hedef | Red |
|--------|-------|-----|
| 100Hz-10kHz | ±0.5dB | >±1dB |
| 20Hz-100Hz | ±1dB | >±2dB |
| 10kHz-20kHz | ±1dB | >±2dB |
| Tüm bant (20Hz-20kHz) | ±1dB | >±2dB |
| Kanallar arası (7.1) | ±0.5dB @ 1kHz | >±1dB |

### 17.3 Kayıt Tablosu

| Alan | Değer |
|------|-------|
| Ölçüm tarihi | YYYY-MM-DD |
| Ekipman (mic, interface) | Model + serial |
| Oda (near-field/far-field) | Konfigürasyon |
| Sweep tipi | Sine / ESS |
| Maks sapma (f, dB) | Frekans + genlik |
| Kabul/Red | §17.2 kriterlerine göre |

---

## 18. Frekans Yanıtı Düzeltme (EQ)

### 18.1 Parametrik EQ

| Parametre | Aralık | Adım |
|-----------|--------|------|
| Frekans | 20Hz-20kHz | Octave (Q=0.7) veya 1/3 oktav |
| Genlik | ±12dB | 0.1dB |
| Q (bandwidth) | 0.3-10 | Sürekli |
| Filtre tipi | Peaking, shelf, notch | — |

### 18.2 Parametrik EQ Örnekleri

| Hedef | Frekans | Genlik | Q |
|-------|---------|--------|---|
| Sub resonans sönümleme | 40Hz | -6dB | 3.0 |
| Room mode (100Hz dip) | 100Hz | +3dB | 2.0 |
| Crossover tepisi | 2kHz | -2dB | 1.5 |
| Air (tweeter boost) | 12kHz | +2dB | 0.7 |
| High shelf (tilt) | 8kHz | -1dB | 0.7 (shelf) |

### 18.3 EQ Kuralları

| # | Kural | Gerekçe |
|---|-------|---------|
| 1 | EQ, düzeltme amaçlı — asla "güzelleştirme" | Doğal ses korunur |
| 2 | ±6dB üzerinde düzeltme | Tasarım hatası → driver/cabinet düzelt |
| 3 | Minimum faz EQ (minimum phase) | Zaman alanı bozulmaz |
| 4 | Filtre sayısı ≤8/kritik kanal | Computational cost + faz kümülatif |

---

## 19. Frekans Yanıtı Ölçüm Hata Kaynakları

| # | Hata | Etki | Önlem |
|---|------|------|-------|
| 1 | Mikrofon ekseni dışında | 10kHz+ yanıltıcı | Eksen hizası, ±15° kabul |
| 2 | Oda yansıması (early reflection) | 2-5ms dip/tepe | Near-field, kturnstile |
| 3 | Hoparlör konumu değişimi | ±1-2dB | Sabit referans (lazer mesafe) |
| 4 | Termal drift (ısınma) | ±0.5dB | 15dk burn-in, sonra ölç |
| 5 | Sweep settling yetersiz | Düşük frekans yanıltıcı | 100ms/nokta |
| 6 | ADC clipping | Harmonik artışı | -6dBFS headroom |
| 7 | Kalibrasyon dosyası olmaması | ±1-2dB (20Hz-20kHz) | UMIK-1 FRF dosyası |

**En sık hata:** 1-4. oda yansıması ve ekseni; kturnstile + near-field + lazer
mesafe ile minimize edilir (§6.3, §17.1).

---

## 20. Frekans Yanıtı Ölçüm Kayıtları

### 20.1 Kayıt Şablonu

| Alan | Format | Zorunlu |
|------|--------|---------|
| Cihaz ID | CM-XXX-YY (ör. CM-71-AB) | ✅ |
| Ölçüm tarihi | YYYY-MM-DD | ✅ |
| Kanal | FL/FR/C/SL/SR/SBL/SBR/Sub | ✅ |
| Sürücü | Marka + model | ✅ |
| Sweep tipi | Sine / ESS | ✅ |
| Örnekleme | 48kHz / 96kHz / 192kHz | ✅ |
| Adım | 1/3 / 1/6 oktav | ✅ |
| Oda | Near-field / far-field / anechoic | ✅ |
| Ortalama | 1× / 3× / 5× | ✅ |
| Referans | 0dB @ 1kHz | ✅ |
| Maks sapma | dB + frekans noktası | ✅ |
| Durum | PASS / FAIL (§17.2) | ✅ |
| Ölçümlü kişi | İsim | ✅ |

### 20.2 Kayıt Arşivleme

| Kural | Uygulama |
|-------|----------|
| Ham veri saklama | WAV sweep + FFT çıktısı (CSV) |
| Versiyon kontrolü | Her revizyon için ayrı klasör |
| Kalibrasyon dosyası | Mic FRF dosyası kayıtla birlikte |
| Fotoğraf | Setup konumu (mic, hoparlör mesafesi) |

---

## 21. Frekans Yanıtı ile İlgili Dosyalar (Cross-Reference)

| Dosya | İlişki |
|-------|--------|
| [[electronic/hardware/snr-thd-measurement]] | THD+N + frekans yanıtı birlikte ölçülür |
| [[electronic/hardware/audio-interface]] | Ölçüm ADC/DAC zinciri (PCM3168A) |
| [[electronic/amplifier/class-ab]] | Class AB amfi frekans yanıtı hedefi (§8.2) |
| [[electronic/amplifier/class-d]] | Class D LC filtresi etkisi (§5.2) |
| [[electronic/amplifier/thermal]] | Termal drift etkisi (§15 burn-in) |
| [[electronic/hardware/pcb-design]] | PCB parasiter frekans etkisi |

---

## 22. Frekans Yanıtı SSS (Sıkça Sorulan Sorular)

| # | Soru | Cevap |
|---|------|-------|
| 1 | 1/3 oktav mı, 1/6 oktav mı? | Kabul testi 1/3 (30 nokta), tasarım doğrulama 1/6 (60 nokta) |
| 2 | Sweep mi, ESS mi? | Sweep: daha yüksek SNR, ESS: daha hızlı + impulse |
| 3 | Near-field ne zaman? | Subwoofer/woozer (oda modu dominant); tweeter far-field |
| 4 | Ölçüm ne zaman? | 15dk burn-in sonrası (termal drift, thermal §15) |
| 5 | Hoparlör kaç cm mesafede? | Near-field: 0.5-1m; far-field: 2m+ (oda akustiği dahil) |
| 6 | EQ ile düzeltilebilir mi? | ±6dB'e kadar (§18.3 kural 2); faz bozulması dikkat |
| 7 | 7.1 kanallar arası fark sınırı? | ±0.5dB @ 1kHz (§14.3) |
| 8 | Neden 1kHz referans? | İnsan algısı orta band referanslı (phon eğrileri) |

### 22.1 Ölçüm Sonrası Kontrol

| # | Kontrol | Kabul |
|---|---------|-------|
| 1 | Ham sweep verisi saklandı | §20.2 arşivleme |
| 2 | Kalibrasyon dosyası uygulandı | Mic FRF (§6.3) |
| 3 | Kabul/Red kararı verildi | §17.2 kriterler |
| 4 | Anomali varsa kök neden notu | §15-1 hata kaynakları |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-13
**Mode:** Red Team · Human Mode · Truth Mode
