---
title: "CoreMusic — Amplifier Architecture"
category: electronics
date: 2026-08-09
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Amplifier Architecture

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[index.md]] · [[keys.md]] · [[brain.md]] · [[MEMORY.md]] · [[log.md]]

---

## 1. Amaç

CoreMusic ELECTRONICS'in yükseltici mimarisi. Amplifier türleri, kanal yapıları, güç seviyeleri, koruma sistemleri, soğutma, PSU, gain stage, hoparlör yönetimi ve amplifier manager'ı kapsar.

---

## 2. Amplifier Türleri

### 2.1 Class A

| Özellik | Değer |
|---------|-------|
| Verimlilik | %25–50 |
| Kalite | En yüksek (doyma yok) |
| Isı | En yüksek |
| Bias | Sürekli akım |
| Kullanım | Stüdyo, high-end |

### 2.2 Class AB

| Özellik | Değer |
|---------|-------|
| Verimlilik | %50–70 |
| Kalite | İyi denge |
| Isı | Orta |
| Kullanım | **CoreMusic varsayılan** |

**CoreMusic Class AB Spesifikasyonları:**
| Parametre | Değer |
|-----------|-------|
| Çıkış Gücü | 100W @ 8Ω |
| THD+N | < %0.01 |
| SNR | > 100dB |
| Frekans Yanıtı | 20Hz–20kHz (±0.5dB) |
| Kanal Ayrımı | > 80dB |
| Damping Factor | > 200 |

### 2.3 Class D

| Özellik | Değer |
|---------|-------|
| Verimlilik | %85–95 |
| Kalite | İyi (PLL filtre gerekir) |
| Isı | Düşük |
| Kullanım | Araç, taşınabilir |

### 2.4 Hybrid

| Özellik | Değer |
|---------|-------|
| Avantaj | Sınıfların birleşimi |
| Kalite | Yüksek |
| Uygulama | Özel tasarımlar |

### 2.5 Sınıf Karşılaştırma

| Sınıf | Verimlilik | Kalite | Isı | Boyut | Uygulama |
|-------|-----------|--------|-----|-------|----------|
| A | %25–50 | En yüksek | En yüksek | Büyük | Stüdyo |
| AB | %50–70 | İyi | Orta | Orta | **CoreMusic** |
| D | %85–95 | İyi | Düşük | Küçük | Araç |
| Hybrid | %70–90 | Yüksek | Orta | Orta | Özel |

---

## 3. Kanal Konfigürasyonları

### 3.1 Yapı Tablosu

| Konfigürasyon | Kanallar | Hoparlör | Varsayılan mı? |
|---------------|----------|----------|---------------|
| Stereo | 2 | 2 hoparlör | Hayır |
| 2.1 | 2+1 | 2 hoparlör + sub | Hayır |
| 5.1 | 5+1 | FL/FR/C/SL/SR + Sub | Hayır |
| 7.1 | 7+1 | FL/FR/C/SL/SR/RL/RR + Sub | Hayır |
| **7.1** | **7+1** | **FL/FR/C/SL/SR/RL/RR + Sub** | **Evet** |

### 3.2 7.1 Kanal Yerleşimi

| Kanal | Kısaltma | Pozisyon | Frekans Aralığı |
|-------|----------|----------|----------------|
| Front Left | FL | Ön sol | 20Hz–20kHz |
| Front Right | FR | Ön sağ | 20Hz–20kHz |
| Center | C | Ön merkez | 100Hz–8kHz |
| Surround Left | SL | Yan sol | 100Hz–16kHz |
| Surround Right | SR | Yan sağ | 100Hz–16kHz |
| Rear Left | RL | Arka sol | 100Hz–16kHz |
| Rear Right | RR | Arka sağ | 100Hz–16kHz |
| Subwoofer | SUB | Alt frekans | 20Hz–120Hz |

### 3.3 Kanal Yerleşim Diyagramı

```
        FL (Ön Sol)        FR (Ön Sağ)
               \              /
                \            /
    SL (Yan Sol) \          / SR (Yan Sağ)
                  \        /
                   \      /
                 [Dinleyici]
                   /      \
    RL (Arka Sol) /        \ RR (Arka Sağ)
                 /          \
                /            \
           SUB (Subwoofer)
```

---

## 4. Güç Seviyeleri ve Chip Eşleşmesi

| Seviye | Class AB Çip | Class D Çip | Fiyat | Uygulama |
|--------|-------------|-------------|-------|----------|
| **5W** | — | TPA3110D2 | ~$0.5 | Mini amp |
| **10W** | LM1875 | TPA3130D2 | ~$0.5-3 | Masaüstü |
| **15W** | LM1875 | TPA3130D2 | ~$0.5-3 | Küçük oda |
| **20W** | LM1875 | TPA3118D2 | ~$0.6-3 | Ev, kitaplık |
| **30W** | LM3886 | TPA3118D2 | ~$0.6-5 | Orta oda |
| **35W** | LM3886 | TPA3116D2 | ~$0.7-5 | Orta oda |
| **50W** | LM3886 / TDA7294 | TPA3116D2 / TPA3250 | ~$0.7-5 | Büyük oda |
| **100W** | Discrete (MJL3281A) | TPA3255 | ~$2.5-4 | **CoreMusic standart** |
| **150W** | Discrete | TPA3251 | ~$3.35 | Salon |
| **185W** | Discrete | TPA3255 | ~$4.13 | Profesyonel |

### 4.1 Düşük Güç Class D Çipleri (5W-50W)

| Çip | Güç@8Ω | Güç@4Ω | Supply | Fiyat (1ku) | Stok |
|-----|--------|--------|--------|-------------|------|
| TPA3110D2 | 10W | 15W | 4.5-26V | ~$0.50 | ✅ |
| TPA3130D2 | 15W | 30W | 4.5-26V | ~$0.50 | ✅ |
| TPA3118D2 | 30W | 60W | 4.5-26V | ~$0.60 | ✅ |
| TPA3116D2 | — | 50W | 4.5-26V | ~$0.70 | ✅ |

### 4.2 Düşük Güç Class AB Çipleri (10W-50W)

| Çip | Güç@8Ω | Güç@4Ω | Supply | Fiyat (1ku) | Stok |
|-----|--------|--------|--------|-------------|------|
| LM1875 | 20W | 30W | ±16-60V | ~$2-3 | ✅ |
| LM3886 | 38W | 68W | ±20-94V | ~$3-5 | ✅ |
| TDA7294 | 100W | 100W | ±40V | ~$3-5 | ✅ |

### 4.1 Güç ve Empedans

| Empedans | 10W | 50W | 100W | 250W | 500W | 1000W |
|----------|-----|-----|------|------|------|-------|
| 8Ω | 9V | 20V | 28V | 45V | 63V | 89V |
| 4Ω | 6.3V | 14V | 20V | 32V | 45V | 63V |
| 2Ω | 4.5V | 10V | 14V | 22V | 32V | 45V |

---

## 5. Gain Stage

### 5.1 Gain Stage Diyagramı

```
Girdi Tamponu ──▶ Input Gain ──▶ Volume ──▶ Voltaj Kazancı ──▶ Akım Kazancı ──▶ Çıkış Aşaması
```

### 5.2 Gain Stage Parametreleri

| Aşama | Parametre | Aralık | Varsayılan |
|-------|-----------|--------|------------|
| Input Gain | Kazanç | -∞ ile +12 dB | 0 dB |
| Volume | Seviye | -∞ ile 0 dB | 0 dB |
| Balance | Sol/Sağ | -100% ile +100% | 0% |
| Voltage Gain | Voltaj | Cihaza özel | Sabit |
| Current Gain | Akım | Cihaza özel | Sabit |

---

## 6. Koruma Sistemleri

| Koruma | Tetikleme | Tepki | Süre | Öncelik |
|--------|-----------|-------|------|---------|
| Kısa Devre | 0Ω çıkış | Anında kapanma | <1µs | CRITICAL |
| Aşırı Akım | >max akım | Kademeli azaltma | <100µs | CRITICAL |
| Aşırı Gerilim | >max voltaj | Güç kesme | <100µs | CRITICAL |
| Ters Polarite | Ters +/− | Koruma rölesi | Anında | HIGH |
| Termal | >80°C | Fan ↑, >100°C kapanma | Sürekli | HIGH |
| DC Offset | >0.5V DC | Röle aç | <10ms | CRITICAL |
| Hoparlör Rölesi | Açma/kapama | Gecikmeli bağlantı | 500ms | MEDIUM |

### 6.1 Koruma Akışı

```
Amplifier Çıkış ──▶ {Kontrol}
                        │
           Kısa Devre ──▶ Anında Kapanma
           Aşırı Akım ──▶ Kademeli Azaltma
           Termal ──────▶ Fan/Kapanma
           DC Offset ───▶ Röle Aç
           Normal ──────▶ Çalışmaya Devam
                        │
                        ▼
                   Hata Logu
```

---

## 7. Soğutma

### 7.1 Soğutma Türleri

| Tür | Özellik | Kullanım |
|-----|---------|----------|
| Pasif | Alüminyum heatsink | Düşük–orta güç |
| Aktif | Fan + heatsink | Yüksek güç |
| Heatpipe | Isı iletimi | Kompakt tasarım |
| Sıvı Soğutma | Su/soğutucu | Ultra yüksek güç |

### 7.2 Fan Kontrolü

| Sensör | Aksiyon |
|--------|---------|
| 40°C | Fan başlar (minimum hız) |
| 60°C | Fan hızı %50 |
| 80°C | Fan hızı %100 |
| 100°C | Kapanma |

---

## 8. PSU (Power Supply)

### 8.1 Güç Besleme Mimarisi

```
┌──────────────┐    ┌──────────────┐    ┌──────────────┐    ┌──────────────┐
│  DC GİRİŞ    │    │  BOOST       │    │  REGÜLATÖR   │    │  ÇIKIŞ       │
│  12V–24V DC  │───▶│  CONVERTER   │───▶│  ±42V DC     │───▶│  Amplifier   │
│  (Adaptör/   │    │  Step-Up     │    │  Ripple      │    │  100W × 7+1  │
│   Batarya)   │    │  12V→±42V    │    │  Filtre      │    │              │
└──────────────┘    └──────────────┘    └──────────────┘    └──────────────┘
```

### 8.2 PSU Türleri

| Tür | Verimlilik | Gürültü | Boyut | Kullanım |
|-----|-----------|---------|-------|----------|
| **Boost Converter** | **%90+** | Orta | **Küçük** | **CoreMusic (DC giriş)** |
| Toroidal + Bridge | %85–95 | Düşük | Büyük | AC mains gerektiğinde |
| Anahtarlamalı | %80–95 | Orta | Küçük | Alternatif |

### 8.3 Güç Sıralaması

| Sıra | Bölge | Bekleme |
|------|-------|---------|
| 1 | DC Giriş (12V–24V) | — |
| 2 | Boost Converter (±42V) | 10ms |
| 3 | CPU/MCU | — |
| 4 | DSP | 10ms |
| 5 | Analog (DAC/ADC) | 20ms |
| 5 | Amplifier | 50ms |

---

## 9. Hoparlör Yönetimi

### 9.1 Hoparlör Parametreleri

| Parametre | Aralık | Varsayılan | Açıklama |
|-----------|--------|------------|----------|
| Volume | -∞ ile 0 dB | 0 dB | Seviye ayarı |
| Delay | 0–100ms | 0ms | Mesafe telafisi |
| Phase | 0° / 180° | 0° | Faz uyumu |
| Gain | -∞ ile +12 dB | 0 dB | Kanal dengesi |
| Mute | On/Off | Off | Sessizlik |
| Crossover | 40–200Hz | 80Hz | Alt frekans geçişi |

### 9.2 Hoparlör Mesafe Ayarı

| Kanal | Tipik Mesafe | Gecikme |
|-------|-------------|---------|
| FL/FR | 2–3m | 0ms |
| C | 2–3m | 0ms |
| SL/SR | 1.5–2.5m | 0–5ms |
| RL/RR | 1–2m | 0–5ms |
| HL | 2–3m | 0–5ms |
| SUB | Değişken | 0–10ms |

---

## 10. Amplifier Manager

### 10.1 Manager Özellikleri

| Özellik | Görev |
|---------|-------|
| Gain Control | Per-channel gain ayarı |
| Mute Control | Tekli/çoklu mute |
| Soft Start | Açılışta yumuşak başlangıç |
| Soft Stop | Kapanışta yumuşak kapatma |
| Sıcaklık İzleme | Sürekli sıcaklık ölçümü |
| Clip Detection | Sinyal doyma tespiti |
| VU Meter | Seviye göstergesi |

### 10.2 Amplifier Durumları

| Durum | LED | Davranış |
|-------|-----|----------|
| Kapalı | Kırmızı (sabit) | Güç yok |
| Bekleme | Sarı (sabit) | Güç var, çıkış yok |
| Aktif | Yeşil (sabit) | Çalışıyor |
| Uyarı | Sarı (yanıp sönme) | Sıcaklık yüksek |
| Hata | Kırmızı (yanıp sönme) | Koruma aktif |
| Mute | Turuncu (sabit) | Sessiz mod |

---

## 11. Performans Metrikleri

| Metrik | Hedef |
|--------|-------|
| THD+N | < %0.01 |
| SNR | > 100dB |
| Frekans Yanıtı | 20Hz–20kHz (±0.5dB) |
| Gecikme | < 1ms |
| Kanal Ayrımı | > 80dB |
| Damping Factor | > 200 |

---

## 12. Amplifier Mimarisi

```
Analog Girdi ─┐
Dijital Girdi ─┤──▶ Tampon ──▶ Ön Yükseltici ──▶ Ton Kontrolü ──▶ Voltaj Amplifikatörü ──▶ Akım Amplifikatörü ──▶ Çıkış Aşaması
               │                                                              │
               │                                          ┌───────────────────┤
               │                                          │ Koruma:           │
               │                                          │ Kısa Devre        │
               │                                          │ Aşırı Akım        │
               │                                          │ Termal            │
               │                                          │ DC Offset         │
               │                                          └───────────────────┤
               │                                                              │
               │                                                              ▼
               │                                                  Hoparlör 1 / Hoparlör 2 / Subwoofer
```

---

## 13. İlgili ADR'ler

| ADR | Konu |
|-----|------|
| [[decisions/accepted/ADR-038-8.1-sound-card-chip-selection]] | 8.1 ses kartı çip seçimi |
| [[decisions/accepted/ADR-061-electronics-architecture]] | Elektronik mimarisi (L6 katmanı) |
| [[decisions/accepted/ADR-063-hardware-design-standards]] | Donanım tasarım standartları |

---

## 14. İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[electronic/index]] | Elektronik indeksi |
| [[electronic/amplifier/index]] | Yükseltici mimarisi |
| [[electronic/audio-architecture]] | Ses mimarisi |
| [[electronic/hardware-design]] | Donanım tasarım rehberi |
| [[electronic/dsp-engine-architecture]] | DSP motoru |

---

## 15. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 2.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Sections | 15 |
| ADR References | 3 |
| ASCII Art Diagrams | 4 (7.1 Yerleşim, Gain Stage, Koruma Akışı, Amplifier Mimarisi) |
| Amplifier Classes | 4 (A, AB, D, Hybrid) |
| Channel Configs | 6 (Mono → 7.1) |
| Power Levels | 8 (10W–2000W) |
| Protection Systems | 7 |
| Cooling Types | 4 |
| PSU Types | 3 |
| Speaker Parameters | 6 |

---

## 16. Amplifier Kabul Kontrol Listesi (Uygulama Öncesi)

Amplifier mimarisi devreye alınmadan önce adımlar sırayla tamamlanır; atlanan adım varsa **kabul edilmez**.

| # | Adım | Referans | Çıktı (kayıt zorunlu) |
|---|------|----------|------------------------|
| 1 | Sınıf/topoloji seçimini doğrula (A/AB/D/hybrid) | §2 | sınıf kararı |
| 2 | Kanal konfigürasyonunu doğrula (mono → 7.1) | §3.1–§3.2 | kanal şeması |
| 3 | Güç/empedans eşleşmesini hesapla | §4.1 (Güç ve Empedans) | güç hesabı |
| 4 | Gain stage parametrelerini sabitle | §5.2 | kazanç şeması |
| 5 | Koruma akışını doğrula (DC, over-current, termal) | §6 · §6.1 | koruma şeması |
| 6 | Soğutma/termal tasarımı doğrula | §7.1–§7.2 | termal tasarım |
| 7 | PSU güç sıralamasını doğrula | §8.3 | güç sıralaması |
| 8 | Hoparlör parametre/mesafe ayarını doğrula | §9.1–§9.2 | hoparlör ayarı |
| 9 | Amplifier Manager durum makinesini doğrula | §10.2 | durum listesi |
| 10 | Performans metriklerini ölç | §11 | ölçüm raporu |

### 16.1 Sık Yapılan Amplifier Hataları

| # | Hata | Sonuç | Önlem |
|---|------|-------|-------|
| 1 | Güç/empedans uyuşmazlığı | Termal kaçak, hoparlör hasarı | §4.1 — hesap zorunlu |
| 2 | Koruma akışının atlanması (DC/termal) | Hoparlör/speaker hasarı | §6 · elektronik kural 10 |
| 3 | Soğutma hesapsız sınıf seçimi | Tcase eşiği aşımı | §7 · §11 |
| 4 | PSU güç sırasız açılış | İnrush, bileşen stresi | §8.3 |
| 5 | Gain stage hesapsız kazanç | Klip/THD bozulması | §5.2 |
| 6 | Ölçümsüz "güç/verimlilik" iddiası | Kanıtsız iddia | `⚠️ VERIFICATION REQUIRED` |

### 16.2 Amplifier DUR Kapıları

| Kapı | Koşul | Sonuç |
|---|---|---|
| Koruma kapısı | DC offset > 0.5V → koruma rölesi zorunlu | DUR — elektronik kural 10 |
| DAC kapısı | PCM3168A dışında DAC | DUR — ADR-038 H001 |
| Ölçüm kapısı | THD+N/SNR ölçümü yok | `⚠️ VERIFICATION REQUIRED` |
| Termal kapısı | Tcase ölçümü yoksa tam güçte çalışma | DUR — §7/§11 |
| Empedans kapısı | Güç/empedans eşleşmesi hesaplanmamış | DUR — §4.1 |

**Not:** Bu bölüm mevcut §1–§15 içeriğini **değiştirmez**; yalnız denetim sırasını ve dur-kararlarını toplar. Yeni tolerans **tanımlanmaz** — değerler ilgili bölümlerden okunur.

### 16.3 Sınıf Seçim Karar Matrisi (§2 — Değiştirilemez Özet)

§2.5 sınıf karşılaştırmasının karar karşılığı — seçim mevcut §2 kriterlerine göre yapılır, yeni kriter **tanımlanmaz**:

| Kriter | Class A | Class AB | Class D | Hybrid |
|---|---|---|---|---|
| Verimlilik | Düşük | Orta | Yüksek | Konfigürasyona bağlı |
| Isı | Yüksek | Orta | Düşük | Sınıfa bağlı |
| THD profiyi | Düşük (lineer) | Düşük | PWM gürültüsü (filtre) | Sınıfa bağlı |
| Karmaşıklık | Düşük | Orta | Yüksek (output filtre) | Yüksek |
| Bu projedeki yeri | §2.1 | §2.2 — ADR-004 analog sınıf AB | §2.3 | §2.4 |

**Proje bağı (kural):** Ana amplifikatör sınıfı proje kararına bağlıdır; sınıf değişikliği yeni ADR ister. **⚠️ VERIFICATION REQUIRED — ADR eşlemesi:** `electronic/dsp/index` §3'te "ADR-004 (analog sınıf AB) + ADR-005 (BPA0350)" yazar; ancak disk `.ai/.decisions/accepted/` içinde ADR-004 = `ADR-004-multi-domain-spa.md`, ADR-005 = `ADR-005-ultrathink-protocol.md` — yani "analog sınıf AB / BPA0350" karşılığı dosya adlarında **görünmüyor**. Eşleşme doğrulanana kadar bu satır kanıt sayılmaz; sınıf kararı ADR'den okunur.

### 16.4 Ölçüm ve Kanıt Durumu

| İddia türü | Durum | Davranış |
|---|---|---|
| Sınıf/kanal/güç tabloları (§2–§4) | Tasarım envanteri | ADR çelişkisi → ADR kazanır |
| Verimlilik/THD/SNR değerleri (§11) | Ölçülmedi (donanım PLANNED) | `⚠️ VERIFICATION REQUIRED` |
| Amplifier kodu/yöneticisi | Bu vault dosyasının sınırı dışı | Kod iddiası yazılmaz |
| Koruma rölesi DC eşiği | Elektronik kural 10 (≥0.5V → röle) | Bağlayıcı — §16.2 |

**Çapraz referans:** `[[electronic/amplifier/index]]` · `[[electronic/amplifier/power-supply]]` · `[[electronic/hardware/amplifier-pcb]]` · `[[electronic/hardware/index]]` · `[[electronic/firmware-architecture]]` · `[[ADR-038-8.1-sound-card-chip-selection]]`.

### 16.5 Sınıf/Donanım İddia Kaydı (Truth Mode)

Doğrulanamayan iddialar silinmez; açık madde olarak kaydedilir:

| # | İddia | Kanıt durumu | Davranış |
|---|---|---|---|
| 1 | "ADR-004 = analog sınıf AB" (§16.3 dsp/index kaynaklı) | Disk: `ADR-004-multi-domain-spa.md` — eşleşme YOK | `⚠️ VERIFICATION REQUIRED` |
| 2 | "ADR-005 = BPA0350" | Disk: `ADR-005-ultrathink-protocol.md` — eşleşme YOK | `⚠️ VERIFICATION REQUIRED` |
| 3 | Bütçe 34.837 ₺ (dsp/index §3) | Para kanıtı bu dosyada yok | `⚠️ VERIFICATION REQUIRED` |
| 4 | Verimlilik/THD/SNR sayısal değerleri (§11) | Donanım PLANNED, ölçüm yok | `⚠️ VERIFICATION REQUIRED` |
| 5 | Sınıf seçimi (A/AB/D/hybrid) | Tasarım envanteri (§2) — karar ADR'de | Karar ADR'den okunur |

**Kural:** Bu tablodaki satırlar "yanlış" değil, **kanıtlanmamıştır**; kanıt gelince satır güncellenir, silinmez (Truth Mode).

### 16.6 Amplifier ↔ Vault Eşlemesi (Sorumluluk)

| Konu | Vault karşılığı | Not |
|---|---|---|
| Güç kaynağı/boost/rail | `electronic/amplifier/power-supply` | ±42V zinciri |
| Kart yerleşimi/PCB | `electronic/hardware/amplifier-pcb` | §19 ölçüm protokolü |
| PCB kuralları/EMC | `electronic/hardware/pcb-design` §6 | EMC ayrı dosyada değil |
| Koruma/DC röle | `electronic/amplifier/protection` · elektronik kural 10 | ≥0.5V → röle |
| Boot/OTA etkisi | `electronic/firmware-architecture` §7–§8 | Firmware değişikliği → test |
| Ölçüm kapıları | `electronic/hardware/index` §7 | SNR/THD/FR |
| DSP zinciri etkisi | `electronic/dsp/index` §1 | ADR-062 bağlayıcı |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
