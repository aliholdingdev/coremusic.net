---
type: system
category: amplifier-architecture
title: "CoreMusic Electronics — Amplifier Architecture Index"
date: 2026-08-09
updated: 2026-10-13
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic Electronics — Amplifier Architecture

**Zorunlu Bağlantılar:** [[electronic/index]] · [[brain.md]] · [[dsp/index]] · [[hardware/index]]

**Alt Dosyalar:** [[class-ab]] · [[class-d]] · [[power-supply]] · [[protection]] · [[thermal]]

## 1. Amaç

Amplifier Architecture, CoreMusic ELECTRONICS platformunun tüm amplifikatör tasarımını, koruma sistemlerini, güç yönetimini ve soğutma altyapısını kapsar. Uzun çip matrisleri ve kural listeleri derinleştirildikleri alt dosyalara taşınmıştır; bu indeks karar özeti ve yönlendirme verir. Bağlayıcı ADR'ler: [[ADR-038-8.1-sound-card-chip-selection]] (PCM3168A + XMOS XU316 — amplifikatör girişi bu DAC'dan beslenir) · [[ADR-017-dsp-hardware-mode]] (DSP hardware mode — DSP çıkışı zincirin parçasıdır).

## 2. Dosya Envanteri (diskten ölç — 2026-10-13)

| Dosya | Satır | Rol | Durum |
|-------|-------|-----|-------|
| `class-ab.md` | 507 | Class AB topoloji, bias hesabı, THD, termal | active |
| `class-d.md` | 502 | Class D PWM, LC filtre, EMI, verimlilik | active |
| `power-supply.md` | 501 | Boost converter, filtre, regülatör, ripple | active |
| `protection.md` | 530 | Aşırı akım, kısa devre, termal, DC offset, relay | active |
| `thermal.md` | 515 | Termal direnç zinciri, heatsink, hava akışı, kapanma | active |

## 3. Katman / Komşu İlişkisi

| Komşu Katman | Yön | Arayüz |
|--------------|-----|--------|
| [[dsp/index]] | Gelen | Düşük seviyeli analog sinyal (DAC çıkışı) |
| [[hardware/audio-interface]] | Gelen | Seviye (dBu/dBV), empedans eşleşmesi → [[hardware/audio-interface]] §3-§4 |
| [[power-supply]] | Dahili | ±42V (Class AB) / PVDD 18-53.5V (Class D) |
| [[protection]] | Dahili | DC offset, over-current, termal kapanma tetikleri |
| [[thermal]] | Dahili | Heatsink, fan, termal direnç zinciri |
| [[hardware/amplifier-pcb]] | Giden | Fiziksel yerleşim, güç yolu, topraklama |
| [[hardware/frequency-response]] | Ölçüm | Tepki doğrulama protokolü |
| [[hardware/snr-thd-measurement]] | Ölçüm | THD+N/SNR doğrulama protokolü |

## 4. Sınıf Seçim Özeti

| Kriter | Ağırlık | Class AB | Class D | Kazanan |
|--------|---------|----------|---------|---------|
| Verimlilik (tam yük) | Yüksek | ~%45-55 | ~%85-92 | Class D |
| Isı üretimi (100W) | Yüksek | ~90W kayıp | ~12W kayıp | Class D |
| EMİ yayılımı | Orta | Düşük (doğrusal) | Yüksek (anahtarlama) | Class AB |
| Hoparlör uyumu (reaktif yük) | Yüksek | Çok iyi (damping) | İyi (LC filtre ile) | Class AB |

**Aile dağılımı:** Class AB = CoreMusic ana amfi serisi (CM-71/51/21/20/10-AB discrete MJL3281A · CM-07/06/05-AB entegre TDA7294/LM3886/LM1875) · Class D = ev cihazı/oda imalatı/test serisi (TPA3255/3251/3250/3116/3118/3130/3110D2). Tam matrisler bu revizyonda silindi; çip seçimi ve aile tabloları [[class-ab]] §2 ve [[class-d]] §2-§5 içindedir.

## 5. Koruma Sistemleri (özet)

| Koruma | Açıklama | Kritiklik |
|--------|----------|-----------|
| Kısa Devre | Çıkış kısa devresi koruması | CRITICAL |
| Aşırı Akım | Maksimum akım sınırı | CRITICAL |
| DC Offset | >0.5V DC offset koruma rölesi | CRITICAL |

Eşik hesapları, tetikleme süreleri ve relay davranışı: [[protection]] §1-§7. Güç sıralaması kuralı: önce DSP, sonra analog, en son amplifier (pop-noise/hasar önleme). Kapsam sınırı: topoloji/bias/termal/güç kaynağı bu klasörde; DAC/ADC, genel PCB ve ölçüm protokolleri [[hardware/index]] altındadır.

## 6. PCB ve Besleme Özeti

- **PCB katman:** 4 katman (L1 sinyal · L2 analog ground · L3 güç · L4 sinyal) — Class D için zorunlu, 2 katman yetersiz.
- **Besleme:** 12-24V DC giriş → boost converter (LTC3862 / LM5122) → ±42V (Class AB) veya PVDD 18-53.5V (Class D); ripple <5mV RMS, rail başına min. 10.000µF.
- **7.1 crossover:** Linkwitz-Riley 4. mertebe, 80Hz; aktif crossover DSP'de, amfi tam bant — [[dsp/index]].

## 7. Kaynakça (Web Araştırması)

TI, OPA1656 düşük gürültülü op-amp ürün sayfası (amplifier sinyal zinciri referans op-amp) — https://www.ti.com/product/OPA1656 · 2026-10-13 · HTTP 200 doğrulandı.

## 8. Repo Kanıtı ve Doğrulama Durumu

**Repo'da donanım/devre kodu YOK — kaynak: .ai/electronic/** — amplifikatör tanımı yalnız vault dosyalarında yaşar; repo'daki en yakın yazılım yüzeyi AI orkestrasyon katmanıdır: `shared/src/AI/AIEngine.php` Satır 138 `analyzeHardware(string $deviceId): array` · Satır 142-143 `$snr = 0.0;` · `$thd = 0.0;` sabit placeholder (gerçek ölçüm YOK) · `shared/src/Contracts/AI/AIEngineInterface.php` Satır 50 donanım metrik sözleşmesi.

**Sonuç:** `analyzeHardware()` snr/thd alanlarını her zaman `0.0` döndürür; gerçek cihaz entegrasyonu PLANNED. Ölçüm protokolü: [[hardware/snr-thd-measurement]] · [[hardware/frequency-response]]. Çapraz referanslar: [[electronic/drivers/index]] · [[architecture/07-security/index]] · [[hardware/pcb-design]] · [[hardware/amplifier-pcb]] · [[hardware/audio-interface]].

**Sıkıştırma notu (2026-10-13):** 454 satırlık indeks 40–100 bandına indirildi; silinen çip/fiyat/stok matrisleri ve kural tabloları bilgi olarak [[class-ab]] · [[class-d]] · [[power-supply]] · [[protection]] · [[thermal]] dosyalarında mevcuttur.

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-13
**Mode:** Red Team · Human Mode · Truth Mode
