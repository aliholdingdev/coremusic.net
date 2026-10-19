---
type: electronic
category: amplifier-protection
title: "CoreMusic — Amplifier Protection Circuits"
date: 2026-08-09
updated: 2026-10-13
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Amplifier Protection

**See also:** [[electronic/amplifier/index]] · [[electronic/hardware/index]]

**Zorunlu Bağlantılar:** [[electronic/amplifier/class-ab]] · [[electronic/amplifier/class-d]] · [[electronic/amplifier/power-supply]] · [[electronic/amplifier/thermal]] · [[electronic/hardware/snr-thd-measurement]]

**Zorunlu sistem kuralı:** DC offset **>0.5V** ise koruma rölesi **zorunludur** (kök AGENTS.md kural 10 — bu dosya §4).

---

## 1. Amaç

Amplifier Protection Circuits, CoreMusic amfi koruma devrelerini tanımlar: hoparlör ve amfi güvenliğini sağlar. Bu dosya aşırı akımı (§1), kısa devreyi (§2), termal kapanmayı (§3), DC offset'i (§4), hoparlör korumasını/relay'yi (§5), koruma sıralamasını (§6), eşik hesaplarını (§7) ve kaynakçayı (§8) kapsar.

**Kritiklik tablosu (kör AGENTS.md kural 10):**

| Eşik | Aksiyon | Kritiklik |
|------|---------|-----------|
| DC offset > 0.5V | Koruma rölesi **zorunlu** | CRITICAL |
| Çıkış kısa devre (0Ω) | Anında kapanma | CRITICAL |
| Over-current > 5A | Güç azaltma | CRITICAL |
| Over-temperature > 100°C | Sistem kapatma | HIGH |

---

## 2. Aşırı Akım (Over-Current)

### 2.1 Mekanizma

Aşırı akım, çıkış transistörlerini SOA (Safe Operating Area) sınırının dışına iter; kalıcı hasara yol açar.

| Tetikleyici | Tipik akım | Risk |
|-------------|-----------|------|
| Düşük empedans yük (2Ω) | 7A (100W @ 4Ω) | SOA ihlali |
| Hoparlör empedans düşüşü (ısı ile) | 5-6A | Termal kaçak |
| Çıkış kısa devre | 15A+ | Anında hasar |

### 2.2 Tespit Yöntemi

| Yöntem | Uygulama | Hız | Doğruluk |
|--------|----------|-----|----------|
| Emitter sense resistor | Output transistör emitter'inde 0.1-0.22Ω | <100µs | Yüksek |
| Current sense transformer | Çıkışta toroid | <1ms | Orta |
| MOSFET Rds(on) sensing | Class D'de (TPA32xx iç) | Çip içi | Yüksek |

### 2.3 Sınırlama (Limiting)

| Aşama | Akım | Aksiyon | Süre |
|-------|------|---------|------|
| Normal | <3.54A (100W@8Ω) | Çalışma | — |
| Uyarı | 3.5-5A | Güç azaltma | <100µs |
| Kritik | >5A | Kapanma | <100µs |
| Kısa devre | >10A | Anında kapanma | <1µs |

**CoreMusic eşiği:** 5A tepe akım (100W@8Ω için Ipeak=3.54A + %40 marj).

### 2.4 Over-Current Kurtarma

| Durum | Aksiyon | Süre |
|-------|---------|------|
| İlk tetikleme | Güç azaltma (bias ↓) | <100µs |
| 3 sn içinde tekrar | Kapanma (mute) | <1ms |
| 30 sn bekleme | Otomatik reset | 30 sn |
| 3. tekrar | Latch-off (manuel reset) | Kalıcı |

**Kurtarma mantığı:** Tek seferlik aşırı akım (ör. hoparlör kablosu kısa teması) →
otomatik kurtarma; tekrarlayan aşırı akım → kalıcı kapanma (donanım hasarı riski).

---

## 3. Kısa Devre (Short-Circuit)

### 3.1 Mekanizma

Çıkış kısa devresi (0Ω), çıkış transistörlerine sınırsız akım çeker; junction sıcaklığı milisaniyeler içinde 150°C'yi aşar.

| Parametre | Değer | Açıklama |
|-----------|-------|----------|
| Kısa devre akımı | 15A+ | Sınırsız (limit yoksa) |
| Hasar süresi | <1ms | Junction erimesi |
| Tespit | Emitter sense / Rds(on) | §2.2 |
| tepki | **Anında kapanma** | <1µs |

### 3.2 Önleme

| # | Önlem | Uygulama |
|---|-------|----------|
| 1 | Over-current limit | §2.3 (current limit devresi) |
| 2 | Fuse (sigorta) | 5A hızlı patlama, çıkış hattı |
| 3 | Koruma rölesi | Kısa devre sonrası hoparlör ayırma (§5) |
| 4 | Class D self-protection | TPA32xx çip içi short-circuit kapatma |

### 3.3 Kurtarma

| Durum | Aksiyon |
|-------|---------|
| Kısa devre giderildi | Otomatik reset (3 sn gecikme) |
| Tekrarlayan kısa devre | Latch-off (manuel reset) |
| Fuse patladı | Manuel değiştirme |

---

## 4. Termal Kapanma (Over-Temperature)

### 4.1 Eşikler

| Sıcaklık | Aksiyon | Süre |
|----------|---------|------|
| <50°C | Normal çalışma | — |
| 50-60°C | Uyarı (log) | — |
| 60-70°C | Fan hızı artır | Sürekli |
| >70°C | Güç azaltma | Sürekli |
| **>80°C** | **Fan maksimum** | Sürekli |
| **>100°C** | **Kapanma** | Anında |

### 4.2 Tespit

| Sensör | Konum | Doğruluk | Hız |
|--------|-------|----------|-----|
| NTC termistör (1kΩ) | Heatsink, output transistör yakını | ±2°C | 1-10s |
| Internal temp (Class D) | TPA32xx çip içi | ±5°C | 100ms |
| Termokupl (K-tipi) | Ölçüm amaçlı | ±1°C | 1s |

### 4.3 tepki Zinciri

```
NTC / Internal temp
    │
    ├─ <60°C  → Fan kapalı
    ├─ 60-80°C → Fan hızı ↑ (PWM)
    ├─ >80°C  → Güç azaltma (bias ↓)
    └─ >100°C → Kapanma (mute + röle aç)
```

Detay: [[thermal]] §4 (kapanma eşiği), [[thermal]] §5 (termokupl ölçümü).

### 4.4 Termal Kurtarma

| Durum | Aksiyon | Süre |
|-------|---------|------|
| 100°C eşiği aşıldı | Kapanma (mute + röle aç) | Anında |
| Sıcaklık <90°C'a düştü | Otomatik reset | 30 sn bekleme |
| Tekrarlayan kapanma (3×) | Latch-off (manuel reset) | Kalıcı |

**Termal zaman sabiti:** Heatsink + transistör termal kütlesi (C_th ≈ 10-50 J/°C)
nedeniyle gerçek junction sıcaklığı, NTC ölçümünden 5-30 sn gecikmeli takip eder.
NTC, heatsink'e monte edildiği için junction'u erken yakalayamaz; **kapanma eşiği
buna göre marjlanır** (NTC 100°C → junction 120-130°C olabilir).

---

## 5. DC Offset Koruması

### 5.1 Neden Kritik?

DC offset, hoparlör bobinine sürekli DC akım çeker; bobini aşırı ısınır ve yakar. 0.5V DC, 8Ω hoparlörde 62.5mW sürekli güçtür — küçük görünür ama bobin bu gücü sürekli olarak atamaz (tasarım AC içindir).

| DC Offset | Hoparlör Akımı | Sonuç |
|-----------|---------------|-------|
| ±50mV | 6.25mA | Güvenli |
| ±0.5V | 62.5mA | **Koruma eşiği (kural 10)** |
| ±2V | 250mA | Bobin ısınır (<10s) |
| ±10V | 1.25A | Bobin yanar (<1s) |

### 5.2 Tespit Devresi

| Bileşen | Görev | Parametre |
|---------|-------|-----------|
| AC coupling kapasitörü | DC'yi ayır, AC geç | 1µF film |
| Precision rectifier | |t| → DC | OPAMP + diyot |
| Comparator | |DC| > 0.5V? | Referans 0.5V |
| Timer (monostable) | Gecikme | 1 saniye |

### 5.3 Koruma Davranışı

| Parametre | Değer |
|-----------|-------|
| DC Offset Limit | ±0.5V |
| Koruma | Röle ile hoparlör ayırma |
| Tetikleme gecikmesi | 1 saniye (pop-noise önleme) |
| Kurtarma | Otomatik reset (offset normale dönerse) |
| tepki süresi | <10ms (tetikleme sonrası) |

### 5.4 Zorunluluk

**Köök AGENTS.md kural 10:** DC Offset >0.5V → koruma rölesi **zorunludur**. Bu CoreMusic amplifikatör serisinde (CM-*-AB, CM-*-D) atlanamaz.

---

## 6. Hoparlör Koruması (Relay)

### 6.1 Relay Görevi

| Görev | Açıklama |
|-------|----------|
| DC offset ayırma | §5.3 tetiklendiğinde hoparlörü amfiden ayır |
| Açılış koruması | Soft-start süresince hoparlör bağlı değil |
| Kısa devre koruması | Kısa devre sonrası hoparlörü ayır |
| Ters polarite | Ters bağlantıyı algılarsa ayır |

### 6.2 Relay Seçimi

| Parametre | Değer | Gerekçe |
|-----------|-------|---------|
| Akım rating | ≥10A (kontakt) | Ipeak 3.54A + marj |
| Gerilim rating | ≥60V DC | ±42V rail |
| Kontakt direnci | <50mΩ | Düşük kayıp, damping factor |
| Bobin gerilimi | 12V DC | Gate drive ile aynı |
| Toplam kanal (7.1) | 8 röle (7 + sub) | Her kanal ayrı |

### 6.3 Soft-Start / Soft-Stop

| Parametre | Değer | Gerekçe |
|-----------|-------|---------|
| Açılış gecikmesi | 2 saniye | Kondansatör şarj + DC settle |
| Kapanış gecikmesi | 0 sn (anında) | Acil durumda hızlı ayırma |
| Pop-noise | Muted açılış | Röle kapalıyken ses |

### 6.4 Röle Sırası (Sıralama)

| Sıra | Aşama | Röle | Gerekçe |
|------|-------|------|---------|
| 1 | Güç açma | Tüm röleler AÇIK | Hoparlör koruma |
| 2 | Soft-start (2s) | Röle kapalı | DC settle |
| 3 | Normal çalışma | Röle KAPALI | Ses |
| 4 | DC offset / kısa devre | Röle AÇIK | Anında ayırma |

**Kural:** Röle, amplifikatör DC stabil olduktan sonra kapanır (pop-noise önleme).

### 6.5 Röle Sürücü Devresi

7.1 sistemde 8 röle (7 kanal + subwoofer) ayrı ayrı sürülmelidir:

| Bileşen | Görev | Parametre |
|---------|-------|-----------|
| NPN transistor (2N2222) | Röle bobin sürücü | Ic = 200mA (bobin akımı) |
| Flyback diyot (1N4148) | Bobin back-EMF koruma | 100V, 200mA |
| Seri direnç | Bobin akımı sınırlama | 12V bobin için 47Ω |
| Gate drive | PWM kontrol | 3.3V/5V MCU |

**Flyback neden kritik?** Röle bobini manyetik enerji depolar; transistor kapandığında
back-EMF 100V+ üretir → transistor ve MCU hasarı. Flyback diyotu bu enerjiyi sürer
(1N4148 yeterli, çünkü enerji küçük).

### 6.6 Röle Kontak Direnci ve Damping Factor Etkisi

Röle kontakt direnci, çıkış empedansına doğrudan eklenir:

```
Z_out(toplam) = Z_out(amplifier) + R_kontakt
Damping factor = R_load / Z_out(toplam)
```

| R_kontakt | Z_out (amplifier 0.1Ω) | Damping (8Ω) | Kabul |
|-----------|-------------------------|--------------|-------|
| 20mΩ | 0.12Ω | 66 | İyi |
| 50mΩ | 0.15Ω | 53 | Sınır |
| 100mΩ | 0.2Ω | 40 | **Kabul edilemez** (hedef >200) |

**Sonuç:** 8Ω'da damping >200 için Z_out <0.04Ω gerekir — amplifikatörün kendi
çıkış empedansı (0.1Ω) bile bu sınırın üzerindedir. 200 damping hedefi için
**Class D closed-loop** (çıkış <0.01Ω, [[class-d]] §6.1) veya **Class AB'de
geri besleme derinliği artırımı** gerekir. Röle kontakt direnci bu hesaba dahil edilir.

---

## 7. Sıralama (Koruma Zinciri)

### 7.1 tepki Sırası (Öncelik)

| Öncelik | Koruma | tepki Süresi | Aksiyon |
|---------|--------|-------------|---------|
| 1 (en hızlı) | Kısa devre | <1µs | Anında kapanma + röle aç |
| 2 | Over-current | <100µs | Güç azaltma |
| 3 | DC offset | <10ms | Röle aç (hoparlör ayır) |
| 4 | Over-temperature | 1-10s | Fan ↑ → güç azalt → kapanma |
| 5 (en yavaş) | Under-voltage | Anında | Amplifier mute |

### 7.2 Koruma Akışı

```
Amplifier Çıkış ──▶ Monitör ──┬──▶ Kısa devre? ──▶ EVET ──▶ Anında kapanma (<1µs)
                               │
                               ├──▶ Over-current >5A? ──▶ EVET ──▶ Güç azalt (<100µs)
                               │
                               ├──▶ DC offset >0.5V? ──▶ EVET ──▶ Röle aç (<10ms)
                               │
                               ├──▶ Over-temp >100°C? ──▶ EVET ──▶ Kapanma (anında)
                               │
                               └──▶ Under-voltage <10V? ──▶ EVET ──▶ Mute (anında)
```

### 7.3 Sıralama Mantığı

| Kural | Gerekçe |
|-------|---------|
| En hızlı koruma önce tetiklenir | Kısa devre en hasar verici |
| DC offset röle ile | Hoparlör koruması (kural 10) |
| Termal yavaş | Termal zaman sabiti büyük |
| Under-voltage mute | Boost başarısız, pop-noise önleme |

---

## 8. Eşik Hesabı

### 8.1 Eşik Tablosu

| Koruma | Eşik | Hesap | tepki |
|--------|------|-------|-------|
| **DC offset** | **±0.5V** | Hoparlör bobin koruma (kural 10) | Röle aç, <10ms |
| Over-current | 5A | Ipeak (100W@8Ω)=3.54A + %40 marj | Güç azalt, <100µs |
| Kısa devre | 0Ω | Direkt kısa | Kapanma, <1µs |
| Over-temp (fan) | 60°C | Heatsink uyarı | Fan başlat |
| Over-temp (limit) | 100°C | Tj limit - 50°C | Kapanma |
| Over-voltage | ±48V | Rail max + marj | Boost kapat |
| Under-voltage | 10V | Boost min giriş | Mute |

### 8.2 Eşik Hesap Örnekleri

**Over-current (5A):**

```
Ipeak = √(P / R) = √(100 / 8) = 3.54A
Marj = 3.54 × 1.4 = 4.96A ≈ 5A
```

**DC offset (0.5V):**

```
Hoparlör akımı = Voffset / R = 0.5 / 8 = 62.5mA
Bobin sürekli güç = V²/R = 0.25 / 8 = 31.25mW (sürekli, AC değil)
Eşik = bobin sürekli DC toleransı (tipik 0.5V)
```

**Over-temp (100°C):**

```
Tj limit = 150°C (datasheet)
Tc (case) ≈ Tj - Pdiss × RθJC = 150 - 32 × 0.83 = 123°C
Kapanma = Tc limit ≈ 100°C (marj)
```

### 8.3 Eşik Doğrulama Testi

| Test | Yöntem | Beklenen |
|------|--------|----------|
| DC offset tetikleme | Giriş DC kaydır, 0.5V üzeri | Röle <10ms açılır |
| Over-current tetikleme | 2Ω yük bağla | Güç azaltma <100µs |
| Kısa devre tetikleme | Çıkış kısa devre | Kapanma <1µs |
| Termal tetikleme | Heat gun ile ısıtma | Kapanma @100°C |

---

## 9. Ölçüm / Tespit Planı

### 9.1 Koruma Test Setupı

```
Signal Generator → Amplifier → Load (değişken)
                     │
              Koruma devresi
                     │
              Oscilloscope (tetikleme ölçümü)
                     │
              DMM (DC offset ölçümü)
```

### 9.2 Test Adımları

| Adım | Aksiyon | Beklenen | Protokol |
|------|---------|----------|----------|
| 1 | DC offset (sessiz) | <±50mV | [[hardware/snr-thd-measurement]] |
| 2 | DC offset tetikle (giriş DC) | Röle @0.5V açılır | Bu dosya §8.3 |
| 3 | Over-current (2Ω) | Limit @5A, <100µs | Bu dosya §8.3 |
| 4 | Kısa devre | Kapanma <1µs | Bu dosya §8.3 |
| 5 | Termal (heat gun) | Kapanma @100°C | [[thermal]] §5 |
| 6 | Soft-start | Röle 2sn'de kapanır, pop yok | Bu dosya §6.3 |

### 9.3 Kabul Kriterleri (Acceptance Criteria)

| # | Test | Kabul Kriteri | Red Kriteri |
|---|------|---------------|-------------|
| 1 | DC offset (sessiz, 8Ω) | <±50mV | >±100mV |
| 2 | DC offset tetikleme | Röle 0.5V'ta <10ms açılır | >20ms veya hiç açılmama |
| 3 | Over-current (2Ω) | Limit 5A, <100µs | >150µs veya 5A aşımı |
| 4 | Kısa devre (10 sn) | Kapanma <1µs, hasar yok | Hasar veya >5µs |
| 5 | Termal | Kapanma @100°C ±5°C | 90°C altında veya 110°C üstünde |
| 6 | Soft-start | Pop-noise <10mV | >50mV pop |
| 7 | Kurtarma | 3 sn'de otomatik reset | Latch-off gerekiyor |

---

## 10. Repo / Kaynak Kanıtı

**Repo'da donanım/devre kodu YOK — kaynak: .ai/electronic/**

CoreMusic deposunda koruma devresine karşılık gelen C/C++/PCB kodu bulunmaz; tasarım yalnızca bu vault dosyalarında yaşar. Depodaki donanıma en yakın yazılım yüzeyi AI orkestrasyon katmanının placeholder alanlarıdır:

| Dosya | Satır | Kanıt | Doğrulama |
|-------|-------|-------|-----------|
| `shared/src/AI/AIEngine.php` | 138 | `public function analyzeHardware(string $deviceId): array` | `Select-Object -Index (137)` ile doğrulandı |
| `shared/src/AI/AIEngine.php` | 142-143 | `$snr = 0.0;` · `$thd = 0.0;` — placeholder | `Select-Object -Index (141..142)` ile doğrulandı |

**Sonuç:** Koruma eşikleri vault tarafında tanımlıdır (§8); gerçek cihaz entegrasyonu PLANNED.

### 10.1 CoreMusic Entegrasyon Durumu

| Bileşen | Durum | Kanıt |
|---------|-------|-------|
| DC offset koruma (§5) | **PLANNED** — Vault tasarımı var, devre YOK | §5.2 şema |
| Röle koruma (§6) | **PLANNED** — 8 röle (7.1) tasarlandı | §6.2-§6.5 |
| Over-current limit (§2) | **PLANNED** — Emitter sense 0.1Ω | §2.2 |
| Termal kapanma (§4) | **PLANNED** — NTC 1kΩ @100°C | §4.1 |
| Soft-start (§6.3) | **PLANNED** — 2 sn gecikme | §6.3 |
| Koruma MCU firmware | **PLANNED** — C++20 (Neva Engine) | `.cpp` YOK (L0 PLANNED) |

---

## 11. Risk Matrisi

| # | Risk | Olasılık | Etki | Önlem | İlgili |
|---|------|----------|------|-------|--------|
| 1 | **DC offset hoparlör yakma** | Orta | CRITICAL | Rölesi zorunlu (kural 10) | §5 |
| 2 | **Kısa devre hasarı** | Düşük | CRITICAL | <1µs kapanma, fuse | §3 |
| 3 | **Over-current SOA ihlali** | Orta | CRITICAL | 5A limit | §2 |
| 4 | **Termal kaçak** | Orta | HIGH | NTC + kapanma @100°C | §4 |
| 5 | **Pop-noise (açılış)** | Yüksek | MEDIUM | Soft-start, röle sıralama | §6 |
| 6 | **Röle kaybı (kontakt)** | Düşük | HIGH | 10A rating, <50mΩ | §6.2 |
| 7 | **Under-voltage pop** | Orta | MEDIUM | Mute @10V | §7 |
| 8 | **Yanlış tetikleme** | Düşük | MEDIUM | Eşik doğrulama testi | §8.3 |
| 9 | **Gecikmeli tepki** | Düşük | HIGH | tepki süresi testi | §9.2 |
| 10 | **Ters polarite** | Düşük | HIGH | Ters polarity koruma | §6.1 |
| 11 | **Flyback hasarı (transistor)** | Düşük | MEDIUM | 1N4148 flyback diyot | §6.5 |
| 12 | **Damping factor düşüşü (röle)** | Orta | MEDIUM | Kontakt <50mΩ | §6.6 |

---

## 12. Terimler (Glossary)

| Terim | Tanım |
|-------|-------|
| Over-current | Güvenli sınırın üzerinde akım |
| Kısa devre | 0Ω çıkış (sınırsız akım) |
| DC offset | Çıkışta istenmeyen sabit gerilim |
| SOA | Safe Operating Area — transistör güvenli çalışma alanı |
| Röle (relay) | Elektrikle kontrollü anahtar (hoparlör ayırma) |
| Soft-start | Açılışta kademeli güç verme (pop-noise önleme) |
| tepki süresi | Tetikleme → koruma aksiyonu süresi |
| Latch-off | Tekrarlayan hatada kalıcı kapanma (manuel reset) |
| Flyback | Röle bobinindeki back-EMF enerjisi (diyot ile sürülür) |
| Pop-noise | Açılış/kapanışta hoparlörde duyulan patlama sesi |
| Damping factor | R_load / Z_out oranı (yük kontrol kabiliyeti) |
| Kontakt direnci | Röle kontaklarının eklediği direnç (damping düşürür) |

### 12.1 Hesap Formülleri

| Formül | Değer | Açıklama |
|--------|-------|----------|
| Ipeak = √(P/R) | √(100/8) = 3.54A | 100W @ 8Ω tepe akım |
| Marj = Ipeak × 1.4 | 3.54 × 1.4 = 4.96A ≈ 5A | Over-current eşiği |
| I_dc = V_offset/R | 0.5/8 = 62.5mA | DC offset akımı |
| P_dc = V²/R | 0.25/8 = 31.25mW | Bobin sürekli DC gücü |
| Tc = Tj - Pdiss×RθJC | 150 - 32×0.83 = 123°C | Case sıcaklığı |
| Damping = R_load/Z_out | 8/0.12 = 66 | R_kontakt 20mΩ dahil |

---

## 13. ADR ve Çapraz Referanslar

| ADR | Konu |
|-----|------|
| [[ADR-038-8.1-sound-card-chip-selection]] | Donanım seçimi (PCM3168A, XMOS) |
| ADR-017 | 32-bit float ses verisi (ölçüm zinciri) |
| ADR-003 | 512 örnek buffer, 48kHz (koruma tepki süresi hesabı) |

| Hedef | İlişki |
|-------|--------|
| [[electronic/amplifier/class-ab]] | Bias/termal koruma bütünlüğü |
| [[electronic/amplifier/class-d]] | Self-protection + harici koruma |
| [[electronic/amplifier/power-supply]] | Over/under-voltage eşikleri |
| [[electronic/amplifier/thermal]] | 80°C/100°C eşik bütünlüğü |
| [[electronic/hardware/snr-thd-measurement]] | DC offset ölçüm protokolü |
| [[electronic/amplifier/index]] | Klasör indeksi ve envanter |

---

## 14. Web Kaynakları (Doğrulanmış URL Tablosu)

Aşağıdaki URL'ler 2026-10-13 tarihinde HTTP HEAD isteği ile doğrulanmıştır; her URL bu dosyada tektir (diğer vault dosyalarıyla kesişim yok).

| İddia | URL | 2026-10-13 | Durum |
|-------|-----|------------|-------|
| TPS3700 supervisör datasheet — over/under-voltage izleme eşikleri (§2, §8) | https://www.ti.com/lit/ds/symlink/tps3700.pdf | Doğrulandı | HTTP 200 |
| LM5060 high-voltage protection controller — güç stage over-current/over-temperature koruma zinciri (§4, §7) | https://www.ti.com/lit/ds/symlink/lm5060.pdf | Doğrulandı | HTTP 200 |
| INA240 current-shunt amplifier — over-current tespiti akım ölçüm yolu (§2) | https://www.ti.com/lit/ds/symlink/ina240.pdf | Doğrulandı | HTTP 200 |
| LM393 comparator — hızlı DC offset/eşik karşılaştırma (§5) | https://www.ti.com/lit/ds/symlink/lm393.pdf | Doğrulandı | HTTP 200 |
| TMP117 high-accuracy temperature sensor — termal koruma sensörü (§4) | https://www.ti.com/lit/ds/symlink/tmp117.pdf | Doğrulandı | HTTP 200 |
| TPS2553 USB/current-limit power switch — giriş akım sınırlama (§8) | https://www.ti.com/lit/ds/symlink/tps2553.pdf | Doğrulandı | HTTP 200 |

Doğrulama yöntemi: `Invoke-WebRequest -Method Head -UseBasicParsing -Uri $u -TimeoutSec 20`.

---

## 15. Açık Konular (VERIFICATION REQUIRED)

Truth Mode gereği eşik çelişkileri silinmez; açık madde olarak kaydedilir:

| # | Konu | Çelişki | Durum |
|---|------|---------|-------|
| 1 | Thermal eşik bütünlüğü | Bu dosya §4 termal kapanmayı 100°C olarak yazabilir; thermal.md §8.2 kademeli zinciri 60/80/95/110°C kullanır | ⚠️ VERIFICATION REQUIRED — thermal.md §8.2 ile hizalanmalı; bağlayıcı eşik seti ADR'ye bağlanmalı |
| 2 | Röle tepki süresi | §6'daki röle açma süresi (~ms) vault içi tahmin; röle üretici datasheet'i eklenmedi | ⚠️ VERIFICATION REQUIRED |
| 3 | DC offset eşiği | §5'teki DC offset koruma eşiği (hoparlör koruma standardı) üretici/ölçüm kaynağıyla çapraz doğrulanmadı | ⚠️ VERIFICATION REQUIRED |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-13
**Mode:** Red Team · Human Mode · Truth Mode