---
type: electronic
category: dsp-firmware
title: "CoreMusic — DSP Firmware (XMOS)"
date: 2026-08-09
updated: 2026-10-13
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — DSP Firmware

**See also:** [[electronic/firmware/index]] · [[ADR-017-dsp-hardware-mode]]

---

## 1. Amaç

DSP Firmware, CoreMusic ELECTRONICS platformunun XMOS XU316 üzerindeki DSP yazılımını tanımlar.

---

## 2. XMOS Firmware Mimarisi

```
USB Audio Class 2.0
    ↓
DSP Processing Chain
    ↓
I2S Output (8-kanal)
    ↓
PCM3168A DAC
```

---

## 3. DSP Chain

| Aşama | Görev | Gecikme |
|-------|-------|---------|
| Input | USB veri kabulü | 0 |
| Format | PCM → Float32 | 0 |
| EQ | 31-band parametrik | 1 sample |
| Dynamics | Compressor/Limiter | 1 sample |
| Crossover | Frekans dağıtımı | 1 sample |
| Output | Float32 → PCM | 0 |

---

## 4. Zero-Latency Kuralı

| Kural | Açıklama |
|-------|----------|
| No blocking | I/O yasak |
| No malloc | Heap allocation yasak |
| No threads | Context switching yasak |
| Lock-free | Atomic operations |

---

## 5. ADR Referansları

| ADR | Konu |
|-----|------|
| [[ADR-017-dsp-hardware-mode]] | DSP hardware |
| [[ADR-025-professional-eq-system]] | EQ sistemi |

---

## 6. I2S/TDM ve Clock Hiyerarşisi (PLL)

DSP firmware'in en kritik kısmı clock ağacıdır; ses kalitesi (jitter, SNR) doğrudan clock doğruluğuna bağlıdır. Bölüm 2'deki I2S çıkışının arkasındaki clock hiyerarşisi:

```
Kristal osilatör (Master Clock referansı)
    ↓
PLL (Phase-Locked Loop) → MCLK
    ↓
Clock divider → BCLK / WCLK (LRCLK)
    ↓
I2S/TDM veri hattı → PCM3168A DAC
```

### 6.1 Clock Sinyalleri

| Sinyal | Rol | İlişki |
|--------|-----|--------|
| MCLK (Master Clock) | DAC/ADC master clock | PLL çıkışı |
| BCLK (Bit Clock) | Bit başına clock | fs × bit × kanal |
| WCLK/LRCLK | Word/Sample clock | = fs (sample rate) |
| Data (DOUT/DIN) | Seri ses verisi | BCLK senkron |

### 6.2 Clock Ağacı Tasarım Kuralları

| Kural | Açıklama |
|-------|----------|
| Tek clock kaynağı | Tüm ses zinciri tek master'dan |
| PLL jitter | Düşük jitter PLL; jitter SNR'ı sınırlar |
| Asynchronous domain | USB (asenkron) ↔ I2S (senkron) ara buffer |
| Clock divider | BCLK/WCLK MCLK'tan bölünür |
| Dual clock uyarısı | İki bağımsız clock → slip/pop riski |

> Kaynak (audio PLL/clocking): TI "PLL and Clocking Configuration for Audio Devices" — Bölüm 14.

### 6.3 USB ↔ I2S Asenkron Senkronizasyon

USB Audio Class 2.0 asenkron bir kaynaktır; I2S ise yerel clock'a kilitlidir. İki alan arasındaki sample rate farkı, esnek bir ara tampon (adaptive/flywheel buffer) ile telafi edilir. Buffer taşması (overflow) ya da boşalması (underflow) pop/noise üretir → clock domain crossing (CDC) deseni şarttır.

---

## 7. Sample-Rate ve Clock Domain Matrisi

| Sample Rate | BCLK (stereo, 32-bit) | MCLK (tipik 256×fs) | Kullanım |
|-------------|------------------------|----------------------|----------|
| 44.1 kHz | 2.8224 MHz | 11.2896 MHz | CD kalitesi |
| 48 kHz | 3.072 MHz | 12.288 MHz | Video/stüdyo |
| 96 kHz | 6.144 MHz | 24.576 MHz | Yüksek çözünürlük |
| 192 kHz | 12.288 MHz | 49.152 MHz | En yüksek çözünürlük |

### 7.1 Clock Domain Izgarası

Her sample-rate için PLL yapılandırması (MCLK oranı ve divider) baştan tanımlanır; çalışma zamanında rate değişimi, PLL'in yeniden kilitlenmesi ve I2S yeniden yapılandırmasını gerektirir (Bölüm 9).

---

## 8. DMA ve Buffer Zinciri

DSP firmware, veriyi CPU kopyalamadan DMA ile taşır; Bölüm 4'teki "No blocking/No malloc" kuralları bu zincirle sağlanır.

| Bileşen | Rol | Bellek |
|---------|-----|--------|
| USB audio buffer | Host'tan gelen paket | Statik ring |
| DSP input buffer | İşlenmemiş frame | Statik, cache-line aligned |
| DSP output buffer | İşlenmiş frame | Statik |
| I2S/DMA buffer | DAC'a giden veri | Statik, double-buffer |
| Descriptor chain | DMA zinciri | Statik |

### 8.1 Double-Buffering (Ping-Pong)

I2S DMA, bir tamponu okurken DSP diğerini işler; tamponlar arasında geçiş (toggle) atomik bir bayrakla senkronize edilir. Bu desen, DMA ile DSP'nin örtüşmesini (overlap) sağlar ve gecikme bütçesini küçültür.

### 8.2 Sıfır-Kopya Prensibi

USB → DSP → I2S arasında veri kopyalanmaz; her aşama, bir sonraki aşamanın hazır tamponuna doğrudan yazar. Kopyalama, hem CPU yükü hem gecikme ekler → yasak.

---

## 9. Gerçek Zamanlı DSP Döngüsü (The Audio Loop)

Bölüm 3'teki zincir, gerçek zamanlı bir döngü içinde çalışır. Döngü, her frame'de tetiklenir ve sabit adımlar içerir.

```
DMA frame hazır (interrupt/bayrak)
  → Input buffer'dan oku (USB→DSP)
  → Format dönüştürme (PCM→Float32, Bölüm 3)
  → EQ (biquad zinciri)          ← [[electronic/dsp/equalizer]]
  → Dynamics (compressor/limiter) ← [[electronic/dsp/dynamics]]
  → Crossover (frekans dağıtımı)  ← [[electronic/dsp/crossover]]
  → Output (Float32→PCM)
  → I2S DMA tamponuna yaz
  → Bayrak toggle (double-buffer)
```

### 9.1 Döngü Zamanlama ve Budget

| Adım | Budget (tipik) | Not |
|------|----------------|-----|
| DMA bekleyişi | frame periyodu | Frame boyutuna bağlı |
| Format dönüşümü | ~%5 CPU | Sabit |
| EQ (N biquad) | ~%20 CPU | Bant sayısına bağlı |
| Dynamics | ~%10 CPU | Detector karmaşıklığı |
| Crossover | ~%5 CPU | FIR/IIR |
| DMA yazma | ~%5 CPU | Sıfır-kopya |

Toplam CPU budget'ı frame periyodunun altında kalmalıdır (bkz. [[rtos]] §7 RMA). ⚠️ VERIFICATION REQUIRED: Gerçek XU316 üzerinde ölçülmüş CPU budget henüz yoktur; değerler tasarım hedefidir.

### 9.2 Rate Değişimi Sırası

Sample-rate değişimi güvenli bir şekilde, audio yolunu kısa süre susturarak (mute) ve DMA'yı durdurarak yapılır: mute → DMA stop → PLL yeniden kilit → I2S yeniden yapılandır → buffer flush → DMA start → unmute. Böylece pop/noise önlenir.

---

## 10. Katsayı ve Firmware Yükleme

DSP zincirinin katsayıları (EQ, crossover, dynamics) firmware'e gömülü değildir; yapılandırma tarafından yüklenir.

| Yükleme Yolu | Zamanlama | Kullanım |
|--------------|-----------|----------|
| Gömülü default | Boot | Fabrika ayarı |
| Host konfigürasyonu | USB control | Kullanıcı EQ |
| Kalıcı config (NVS) | Boot sonrası | Son ayar |
| Gerçek zamanlı güncelleme | Çalışırken | Parametre tweaking |

### 10.1 Katsayı Güncelleme (Atomic Toggle)

Çalışırken katsayı güncellenirken, audio döngüsü asla yarım-katsayı okumaz. Double-buffer'lı katsayı seti kullanılır: yeni katsayılar hazırlanır, atomik bir toggle ile döngü yeni sete geçer (bkz. [[electronic/dsp/dsp-pipeline]] parametre güncelleme).

### 10.2 Katsayı Kuantizasyonu

XU316 sabit nokta (fixed-point) üzerinde çalışabilir; katsayılar biquad formatına kuantize edilir. Kuantizasyon hatası, low-frequency / high-Q biquad'larda kararlılığı düşürebilir → katsayı tasarımı (Bölüm 11) ve kuantizasyon aralığı dikkatle seçilmelidir.

---

## 11. Biquad Zinciri ve Katsayı Tasarımı

EQ ve crossover, biquad (ikinci derece IIR) bloklarından oluşur. Katsayılar, sample-rate'e duyarlıdır.

| Biquad Parametresi | Etki |
|--------------------|------|
| b0, b1, b2 | Giriş katsayıları |
| a1, a2 | Geri besleme katsayıları (a0=1 normalize) |
| Sample rate | Katsayıların fs'ye bağımlılığı |
| Q / bandwidth | Rezonans / bant genişliği |

Detay: [[electronic/dsp/filters]] · [[electronic/dsp/equalizer]] · katsayı formülleri için W3C Audio EQ Cookbook (Bölüm 14).

---

## 12. Çok-Çekirdekli XMOS Kullanımı

XMOS xCORE çok-çekirdeklidir; DSP zinciri çekirdeklere dağıtılabilir.

| Strateji | Açıklama | Gecikme |
|----------|----------|---------|
| Pipeline (şeritleme) | Her çekirdek bir aşama | Daha yüksek throughput |
| Block processing | Her çekirdek bir bant/grup | Düşük overhead |
| Master/slave | Bir çekirdek I2S, diğerleri DSP | Dengeli |
| Channel splitting | Kanallar çekirdeklere bölünür | Ölçeklenebilir |

### 12.1 Çekirdekler Arası İletişim

Çekirdekler arası veri, XMOS communication channels ile taşınır; bu kanallar hardware destekli ve deterministiktir (bloklanma maliyeti öngörülebilir). Cross-core buffer, bölünmüş tampon (partitioned buffer) deseniyle kopyasız paylaşılır.

---

## 13. Repo Kanıtı

Repo'da DSP firmware (C++/C / xCORE) kodu YOK — 2026-10-13 ölçümü: `0` `.cpp/.h/.c` dosyası, `0` `CMakeLists.txt`, `NevaEngine`/XMOS proje dizini yok. Bu dosya salt spesifikasyon; kaynak `.ai/electronic/**`. Uydurma `Dosya:/Satır:` referansı yazılmaz.

---

## 14. Web Araştırması

| İddia | URL | Tarih | Durum |
|-------|-----|-------|-------|
| TI "PLL and Clocking Configuration for Audio Devices" — ses cihazları için PLL/clock divider yapılandırması | https://www.ti.com/lit/an/slaa892/slaa892.pdf | 2026-10-13 | HTTP 200 doğrulandı |
| DSPRelated ücretsiz DSP kitapları — biquad/zaman-domain işleme teorisi | https://www.dsprelated.com/freebooks/ | 2026-10-13 | HTTP 200 doğrulandı |

---

## 15. Kaynakça

1. TI SLAA892 — PLL and Clocking Configuration for Audio Devices: https://www.ti.com/lit/an/slaa892/slaa892.pdf
2. DSPRelated — free books (DSP theory): https://www.dsprelated.com/freebooks/
3. W3C Audio EQ Cookbook — biquad katsayıları: https://www.w3.org/TR/audio-eq-cookbook/ (bkz. [[electronic/dsp/filters]])
4. XMOS — xCORE mimarisi / XU316 ⚠️ VERIFICATION REQUIRED (resmi dokümantasyon URL'si bu revizyonda doğrulanmadı).
5. CoreMusic Vault — `.ai/electronic/firmware/index.md`, `.ai/electronic/dsp/index.md`, `.ai/electronic/hardware/index.md`

---

## 16. Terimler ve Kısaltmalar

| Terim | Açıklama |
|-------|----------|
| I2S | Inter-IC Sound (seri ses veri yolu) |
| TDM | Time-Division Multiplexing (çok kanallı I2S) |
| MCLK | Master Clock |
| BCLK | Bit Clock |
| WCLK/LRCLK | Word/Sample Clock |
| PLL | Phase-Locked Loop |
| CDC | Clock Domain Crossing |
| DMA | Direct Memory Access |
| biquad | İkinci derece IIR filtre bloğu |
| fixed-point | Sabit nokta aritmetik |

---

## 17. Cross-Referanslar

| Konu | İlgili Dosya |
|------|--------------|
| Firmware genel bakış | [[electronic/firmware/index]] |
| Boot (DSP init) | [[boot-sequence]] |
| RTOS / zamanlama | [[rtos]] |
| DSP zinciri (EQ/Dynamics/Crossover) | [[electronic/dsp/index]] |
| Filters / biquad | [[electronic/dsp/filters]] |
| DAC arayüzü | [[electronic/hardware/index]] · [[ADR-038-8.1-sound-card-chip-selection]] |
| ADR-017 / ADR-025 | Bölüm 5 |

---

## 18. USB Audio Class 2.0 Katmanı

Bölüm 2'deki USB girişinin detayı. USB Audio Class 2.0 (UAC2), yüksek kanal sayısı ve yüksek sample-rate destekler.

| Özellik | Değer / Not |
|---------|-------------|
| Sınıf | USB Audio Class 2.0 |
| Maks. kanal | Çok kanallı (8.1 hedefi) |
| Sample rate | 44.1k–192k (Bölüm 7) |
| Transfer tipi | Asenkron (adaptive/async endpoint) |
| Bandwidth | MCLK/BCLK'e göre USB bant genişliği yeterli olmalı |
| Control | UAC2 request (mute/volume/feature) |

### 18.1 UAC2 Endpoint Yapısı

| Endpoint | Yön | Kullanım |
|----------|-----|----------|
| Isochronous IN | DSP → Host | Kayıt (record) |
| Isochronous OUT | Host → DSP | Çalma (playback) |
| Interrupt IN | DSP → Host | Durum/feedback |
| Control | Bidirectional | Feature/mute/volume |

### 18.2 Asenkron Feedback

UAC2 asenkron modda, cihaz host'a bir feedback endpoint üzerinden gerçek clock hızını bildirir; host buna göre gönderim oranını uyarlar. Bu, USB↔I2S clock farkının (Bölüm 6.3) telafi mekanizmasıdır.

---

## 19. Firmware Image Yapısı

DSP firmware image, boot edilebilir ve doğrulanabilir bir pakettir.

| Bölüm | İçerik |
|-------|--------|
| Header | Sürüm, magic, boyut |
| Bootloader stub | İlk çalıştırma |
| DSP code | Çekirdek işlem kodu |
| Default coefficients | Fabrika EQ/crossover |
| Config/NVS pointer | Kalıcı ayar referansı |
| Checksum/signature | Bütünlük (bkz. [[ota-update]]) |

### 19.1 Firmware Yükleme Yolu

```
Boot ROM → loader stub → DSP image doğrula (checksum/imza)
  → DSP code yükle → default katsayı yükle → DSP döngüsü başlat
```

Firmware güncelleme, [[ota-update]] §6'daki A/B mekanizmasıyla; doğrulama §5'teki imza zinciriyle yapılır.

---

## 20. Hata Yönetimi ve Sağlamlık

| Hata | Tespit | Kurtarma |
|------|--------|----------|
| USB detach | Endpoint hata | Bekleme / yeniden enumerate |
| DMA underflow | Buffer boş | Mute / clock recovery |
| DMA overflow | Buffer taşma | Drop / clock recovery |
| Clock kaybı (unlock) | PLL unlock flag | Yeniden kilit / reset |
| Katsayı geçersiz | Range check | Default'a dön |
| DSP stack overflow | Canary | Reset (bkz. [[rtos]] §21) |

### 20.1 Fail-Safe Davranış

Kritik hata durumunda ses yolu mute edilir ve safe-mode'a geçer; kontrol yolu log tutar ve kurtarma dener. DC offset koruma rölesi (elektronik kural 10) ile koordine olur.

---

## 21. Güç Yönetimi ve Clock Gating

| Durum | DSP | Clock | Güç |
|-------|-----|-------|-----|
| Aktif | Çalışır | Tam | Normal |
| Idle (ses yok) | Döngü hazır | Kısmi | Düşük |
| Suspend | Duraklat | Kapalı | Çok düşük |
| Uyku | Kapalı | Kapalı | Minimum |

### 21.1 Clock Gating Uyarısı

Audio clock'u (MCLK) ani kapatmak DAC'ı bozabilir; clock gating, mute ve pop-free sıralamayla yapılır (sessizce kapat/aç). Aksi halde açılış/kapanışta pop noise oluşur.

---

## 22. Gecikme (Latency) Bütçesi

Toplam ses yolu gecikmesi, giriş (USB) ile çıkış (I2S) arasındaki tüm tamponların toplamıdır.

| Gecikme Kaynağı | Tipik katkı |
|-----------------|-------------|
| USB buffer (frame) | 1 ms (1 frame @1k) |
| DSP processing | 1 frame (double-buffer) |
| I2S/DMA buffer | 1 frame |
| DAC filtre gecikmesi | DAC'a bağlı |
| **Toplam (tipik)** | birkaç ms |

### 22.1 Gecikme Azaltma Teknikleri

| Teknik | Etki |
|--------|------|
| Küçük frame boyutu | Düşük buffer gecikmesi |
| Block processing yerine sample | Daha düşük (ama daha yüksek CPU) |
| Double-buffer minimal | 1 frame overlap |
| Linear-phase filtre yerine minimum-phase | Faz gecikmesi azalır (bkz. [[electronic/dsp/filters]]) |

---

## 23. Doğrulama ve Test

| Test | Yöntem | Kabul |
|------|--------|-------|
| Clock jitter | Scope/analiz | SNR'ı sınırlamayan jitter |
| DMA continuity | Uzun süre stream | 0 underflow/overflow |
| Rate-change pop | Rate değiştirme | Pop-free (mute sıralama) |
| CPU budget | Profil | < frame periyodu |
| Gecikme ölçümü | Loopback | Bütçe içinde |
| USB uyumluluk | UAC2 test | Enumerate + stream |

> ⚠️ VERIFICATION REQUIRED: Bu testler gerçek XU316 donanımında henüz yapılmamıştır; tasarım/plan seviyesindedir.

---

## 24. Yol Haritası

| Aşama | İçerik | Durum |
|-------|--------|-------|
| Faz 1 | Clock zinciri + DSP döngüsü tasarımı (bu dosya) | Tasarım |
| Faz 2 | UAC2 + DMA zinciri implementasyonu | PLANNED (repo kodu YOK) |
| Faz 3 | Katsayı yükleme + rate-change | PLANNED |
| Faz 4 | Donanım üzerinde jitter/latency doğrulama | PLANNED (ölçüm) |

---

## 25. 8.1 Kanal Haritalama

Platform, 8.1 (8 kanal + LFE) ses çıkışını hedefler. Kanal haritası, DSP zinciri ile DAC çıkışı arasında eşleştirilir.

| Kanal | Rol | DSP Çıkışı | DAC Kanalı |
|-------|-----|------------|------------|
| 1 | Front Left | Ch 0 | PCM3168A Out 1 |
| 2 | Front Right | Ch 1 | Out 2 |
| 3 | Center | Ch 2 | Out 3 |
| 4 | LFE (Subwoofer) | Ch 3 | Out 4 |
| 5 | Surround Left | Ch 4 | Out 5 |
| 6 | Surround Right | Ch 5 | Out 6 |
| 7 | Back Left | Ch 6 | Out 7 |
| 8 | Back Right | Ch 7 | Out 8 |
| LFE/Sub | Subwoofer (crossover çıkışı) | Ch 3 (crossover) | Out 4 |

### 25.1 Crossover Kanal Dağıtımı

Bölüm 3'teki crossover aşaması, düşük frekansları LFE/subwoofer kanalına yönlendirir; yüksek frekanslar hoparlör kanallarında kalır. Kanal haritası, [[electronic/dsp/crossover]] ve donanım [[ADR-038-8.1-sound-card-chip-selection]] ile uyumlu olmalıdır.

---

## 26. DAC (PCM3168A) Entegrasyonu

DSP firmware'in çıktığı I2S, PCM3168A DAC'ına gider. DAC'ın clock ve format beklentileri DSP firmware ile senkron olmalıdır.

| DAC Parametresi | DSP Firmware Karşılığı |
|-----------------|------------------------|
| MCLK oranı | PLL yapılandırması (Bölüm 6) |
| Format (I2S/TDM) | I2S modu (Bölüm 6.1) |
| Sample rate | Rate matrisi (Bölüm 7) |
| Kanal sayısı | 8.1 harita (Bölüm 25) |
| Reset/init | Boot sırasında init (Bölüm 2 / [[boot-sequence]]) |

### 26.1 DAC Init Sırası

DAC init, clock kararlı olduktan sonra yapılır (Bölüm 9.2 sıralaması): clock stabil → DAC reset → DAC format/rate yapılandır → mute kaldır. Tersi sıralama pop noise üretir.

---

## 27. Anahtar Kararlar ve Özet

| # | Karar | Dayanak |
|---|-------|---------|
| 1 | Tek master clock + PLL | Jitter/SNR (Bölüm 6) |
| 2 | USB↔I2S asenkron CDC | Clock domain (Bölüm 6.3) |
| 3 | DMA + double-buffer, sıfır-kopya | Gecikme (Bölüm 8) |
| 4 | Katsayı atomic toggle | Pop-free güncelleme (Bölüm 10.1) |
| 5 | Rate-change mute sıralama | Pop-free (Bölüm 9.2) |
| 6 | Çok-çekirdek pipeline | Ölçeklenebilirlik (Bölüm 12) |
| 7 | Fail-safe mute + safe-mode | Sağlamlık (Bölüm 20.1) |

---

## 28. Bağlantılar (Summary Links)

`[[electronic/firmware/index]]` · `[[boot-sequence]]` · `[[rtos]]` · `[[ota-update]]` · `[[electronic/dsp/index]]` · `[[electronic/dsp/filters]]` · `[[electronic/dsp/equalizer]]` · `[[electronic/dsp/crossover]]` · `[[electronic/hardware/index]]` · `[[ADR-017-dsp-hardware-mode]]` · `[[ADR-038-8.1-sound-card-chip-selection]]`

---

## 29. Teşhis ve Log (Non-Realtime Yol)

DSP firmware, real-time olmayan bir teşhis/log yolu içerir; bu yol asla audio döngüsünü (Bölüm 9) bloklamaz.

| Teşhis | Kaynak | Aktarım |
|--------|--------|---------|
| Clock lock durumu | PLL flag | Atomik sayaç |
| DMA underflow/overflow | Buffer sayacı | Atomik sayaç |
| CPU budget kullanımı | Döngü sayaçları | Atomik sayaç |
| USB detach/attach | Endpoint olay | Interrupt → log ring |
| Firmware sürüm | Header | Boot okuması |

### 29.1 Log Prensibi

Log verisi, audio yolundan atomik sayaçlar/queue ile çekilir; ayrıştırma ve aktarım (USB control / [[rtos]] §26 monitoring) düşük öncelikli yolda yapılır. Böylece gözlemlenebilirlik, zero-latency kuralını (Bölüm 4) bozmaz.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-13
**Mode:** Red Team · Human Mode · Truth Mode
