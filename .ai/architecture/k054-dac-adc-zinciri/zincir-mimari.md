---
title: "DAC-ADC donusum zinciri"
type: architecture
category: architecture
date: 2026-10-06
updated: 2026-10-06
version: 4.0.0
status: active
authority: reference
---

# DAC-ADC donusum zinciri

## §1 Amaç & Kapsam

Bu belge, CoreMusic donanım katmanında **dijital → analog → dijital** sinyal zincirinin mimarisini, saat hiyerarşisini, empedans/filtre düzenini ve tasarım kısıtlarını tanımlar. Kapsam, `k054` düğümünün salt-okunur kaynağı olan `_backup/arch-2026-10-06_1057/architecture/k1-donanim/dac-adc-zinciri.md` dosyasındaki iddialarla sınırlıdır; kaynakta olmayan her değer `⚠️ VERIFICATION REQUIRED` ile işaretlenir.

| Kapsam | Kapsam Dışı |
|--------|-------------|
| DAC→analog çıkış→ADC geri dönüş zincirinin üstten görünümü | Analog yolun iç katman detayı → [[../k060-analog-sinyal-yolu/analog-yol-rehberi]] |
| Saat hiyerarşisi (MCLK/SCK/WS) ve master-slave rolü | I2S protokol zamanlama kuralı → [[../k057-i2s-interface/i2s-ve-tdm-rehberi]] |
| Emipedans eşleşme ve I2S hattı EMI filtresi | PCB katman/yerleşim → [[../k070-pcb-tasarim/pcb-rehber]] |
| Zincirin gecikme ve bant genişliği kısıtları | USB tarafı → [[../k059-usb-audio/usb-audio-yolu]] |
| Bileşen değerleri (ferrite, decoupling, crystal) | Koruma devreleri → [[../k067-koruma-devreleri/koruma-rehberi]] |

**Birincil kanıt:** `_backup/arch-2026-10-06_1057/architecture/k1-donanim/dac-adc-zinciri.md` (satır indeksi §9'da).

## §2 Zincir Mimarisi & Düğümler

```
  [k058 XMOS XU316] --MCLK/SCK/WS/SD--> [k057 I2S/TDM Bus] --> [k055 AK4458 DAC]
         ^                                                             |
         |                                                             v
         |                                                    [k060 Analog Yol]
         |                                                             |
         |                                                             v
  [k056 PCM3168A ADC] <--I2S geri dönüş-- [Analog Input] <-------------+
         |
         v
   [DSP Engine / k03 ses motoru]
```

| # | Düğüm | Yön | Kaynak iddiası |
|---|-------|-----|----------------|
| 1 | XMOS XU316 | Master clock üretici | `Clock Hierarchy: Primary Clock Source: XMOS XU316 (Master)` |
| 2 | I2S Bus | Saat + veri dağıtımı | `MCLK Output / SCK Output / WS Output / SD0-SD3` |
| 3 | AK4458 (DAC) | Slave rolünde | `AK4458 (Slave) — SCKI, TDMCLK, TDMFS, TDMD[0:3]` |
| 4 | Analog Output | Differential çıkış | `Sinyal Seviyesi: 2.1Vrms (differential)` |
| 5 | PCM3168A (ADC) | Slave rolünde | `PCM3168A (Slave) — SCKI, BCK, LRCK, DOUTA/B` |
| 6 | DSP Engine | Üst katman alıcısı | `Bağımlılık: K3 DSP / Üst / Dijital sinyal işleme` |

### §2.1 Saat Hiyerarşisi

| Kaynak | Hedef pin/grup | Rol | Frekans (kaynak iddiası) |
|--------|----------------|-----|--------------------------|
| MCLK | AK4458 SCKI · PCM3168A SCKI | Master clock | 22.5792 MHz (44.1k ailesi) / 24.576 MHz (48k ailesi) |
| SCK | AK4458 TDMCLK · PCM3168A BCK | Bit clock | 1.4112 MHz (44.1k) / 1.536 MHz (48k) |
| WS | AK4458 TDMFS · PCM3168A LRCK | Word select | 44.1 kHz / 48 kHz |
| SD[0:3] | AK4458 TDMD[0:3] · PCM3168A DOUTA/B | Seri veri | kanal başına kbps `⚠️ VERIFICATION REQUIRED` |

## §3 Parametreler (kaynak değerli)

| Parametre | Değer | Kaynak satır özeti |
|-----------|-------|--------------------|
| DAC | AK4458 (32-bit, 8-kanal) | `DAC | AK4458 (32-bit, 8-kanal)` |
| ADC | PCM3168A (32-bit, 8-kanal) | `ADC | PCM3168A (32-bit, 8-kanal)` |
| Clock Frequency | 22.5792 MHz / 24.576 MHz | 44.1k ailesi / 48k ailesi |
| I2S Bit Clock | 1.4112 MHz / 1.536 MHz | fs × 32 (hesap §4.1) |
| Word Select | 44.1 kHz / 48 kHz | örnekleme hızı |
| MCLK (tablo) | 256fs = 11.2896 MHz / 12.288 MHz | kaynak `MCLK` satırı |
| Sinyal seviyesi | 2.1 Vrms (differential) | çıkış genliği |
| Empedans | 100 Ω differential | zincir genel empedansı |
| Clock tolerance | ±50 ppm | MCLK/SCK/WS için |
| Jitter | < 100 ps RMS | MCLK/SCK/WS için |
| Ferrite bead | BLM18AG601SN1, 600 Ω @ 100 MHz, 0603 | I2S hattı filtresi |
| Decoupling | 100 nF MLCC, 0402 | I2S dekuplaj |
| Trace length | < 50 mm | I2S hattı |
| Hat empedansı | 90 Ω differential | I2S hattı hedefi |
| Crystal (44.1k) | 22.5792 MHz × 1 | çift crystal düzeni |
| Crystal (48k) | 24.576 MHz × 1 | çift crystal düzeni |
| Load cap | 18 pF C0G × 4 | crystal load |
| Termination | 100 Ω × 8 | I2S sonlandırma |
| Ferrite adet | 16 | I2S hat filtresi |
| Decoupling adet | 16 | I2S dekuplaj |

**Kanıt satırı:** `_backup/arch-2026-10-06_1057/architecture/k1-donanim/dac-adc-zinciri.md` — §9 dizininde satır numarasıyla listelenir.

> **Saat çelişkisi notu (2026-10-06 — HM-01 öncesi):** Bu bölümdeki `BCLK = fs × 32 = 1.4112 MHz` ifadesi tek başına bağlayıcı değildir; salt-okunur kaynaklarda **üç farklı BCLK** ve **iki farklı MCLK** iddiası yan yana durur ve hiçbiri bu görevce karara bağlanmaz:
>
> | İddia | Kaynak · satır | Aritmetik kontrol |
> |---|---|---|
> | `1.4112 MHz / 1.536 MHz` (32 fs) | `k1-donanim/dac-adc-zinciri.md` L21 · L82 | 44.100 × 32 = 1.4112 MHz ✓ tutarlı |
> | `64fs × 32-bit = 2.1168 MHz` | `k1-donanim/i2s-interface.md` L23 | 64 fs @ 44.1 kHz = 2.8224 MHz; yazan değer 2.1168 MHz = 48 fs → **tutarsız** |
> | `2 × 2 × 32 × 44100 = 4.2336 MHz` | `k1-donanim/i2s-interface.md` L36 | ifade 5.6448 MHz (128 fs) verir; yazan değer 4.2336 MHz (96 fs) → **tutarsız** |
> | `MCLK 256fs = 11.2896 / 12.288 MHz` | `dac-adc-zinciri.md` L23 · `i2s-interface.md` L22 | 44.100 × 256 = 11.2896 MHz ✓ (256 fs) |
> | `Clock Frequency 22.5792 / 24.576 MHz` | `dac-adc-zinciri.md` L20 | 44.100 × 512 = 22.5792 MHz ✓ (512 fs) — 256 fs ile **çelişir** |
>
> **⚠️ VERIFICATION REQUIRED (HM-01):** kesin BCLK/MCLK oranı clock sync testinde ölçülmeden yazılmaz; §4.1–§4.3 hesapları bu nedenle **koşulludur**. Ayrıntı: `[[../k054-dac-adc-zinciri/clock-jitter-analizi]]` §3 · `[[../k054-dac-adc-zinciri/zincir-kaynak-karsilastirma]]` §4.
### §3.1 Empedans Eşleşme Tablosu (kaynak birebir)

| Junction | Source Z | Load Z | Matched? | Yorum |
|----------|---------|--------|----------|-------|
| XMOS → DAC | 50 Ω | 100 Ω | No (high-Z input) | gerilim kipli giriş, yansıma düşük |
| DAC → ADC | 25 Ω | 10 kΩ | No (voltage mode) | kaynak empedansı düşük |
| ADC → DSP | 100 Ω | 50 Ω | Yes (differential) | differential eşleşme |

## §4 Hesaplar (türetilmiş)

**Hesap 4.1 — Bit clock doğrulaması (türetilmiş).**
Formül: `BCLK = fs × kanal_başı_bit × kanal_grubu`.
44.1 kHz × 32 bit × 1 (tek slot varsayımı) = 1.4112 MHz → kaynak tablosundaki `1.4112MHz (44.1kHz)` ile **örtüşür**.
48 kHz × 32 = 1.536 MHz → kaynak `1.536MHz (48kHz)` ile **örtüşür**.
Sonuç: tablo tutarlıdır.

**Hesap 4.2 — MCLK/fs oranı çelişkisi (türetilmiş).**
Kaynakta iki ayrı MCLK ifadesi var: `MCLK: 256fs = 11.2896MHz / 12.288MHz` ve `Clock Frequency: 22.5792MHz / 24.576MHz`.
44.1 kHz × 256 = 11.2896 MHz ✓ (256fs).
44.1 kHz × 512 = 22.5792 MHz ✓ (512fs).
İki değer aynı fs için farklı oranlara karşılık gelir → **kaynak içi çelişki**, §7 Hata Modları HM-01.

**Hesap 4.3 — MCLK / BCLK oranı (türetilmiş).**
22.5792 MHz / 1.4112 MHz = 16 → yani 512fs / 32 = 16 slot (64fs slot yapısı için 8 slot). 64fs slot varsa 22.5792/1.4112 = 16, 64fs slot isteyen 8 kanal × 2 slot yapısında `⚠️ VERIFICATION REQUIRED` (slot yapısı kaynakta yok).

**Hesap 4.4 — Jitter kaynaklı SNR tavanı (türetilmiş, kestirim).**
Formül: `SNR_jitter = -20·log10(2π·f_in·t_j)`; f_in = 20 kHz, t_j = 100 ps RMS.
2π × 20000 × 1e-7 = 1.2566e-2 → -20·log10(0.012566) ≈ 38 dB? Hesap: log10(1.2566e-2) = -1.9008 → ×-20 = 38.02 dB.
Sonuç ≈ **38 dB** — bu, kaynak jitter değerinden türetilmiş **üst sınır kestirimdir**; gerçek sistem SNR'si (AK4458 125 dB iddiası) bu tavanın üzerindedir, dolayısıyla 100 ps jitter değeri bu ölçüt için **yetersiz görülmektedir**. İşaret: `⚠️ VERIFICATION REQUIRED` (jitter ölçüm yöntemi kaynakta yok).

**Hesap 4.5 — Ferrite + trace gecikmesi (türetilmiş).**
FR4'de ~6 ps/mm (εr≈4.2 varsayımı — `⚠️ VERIFICATION REQUIRED`, kaynak εr vermiyor).
50 mm × 6 ps/mm ≈ 300 ps → 20 kHz tam dalga boyu (50 µs) karşısında ≈ 0.0006° faz kayması (hesap: 300e-12 / 50e-6 × 360 ≈ 0.0022°).

**Hesap 4.6 — Empedans yansımaları (türetilmiş).**
Γ = (ZL − ZS)/(ZL + ZS).
XMOS→DAC: (100−50)/(100+50) = 0.333 → |Γ| = 0.33 → ≈ −9.5 dB yansıma.
DAC→ADC: (10000−25)/(10000+25) = 0.995 → yüksek empedans girişte yansıma belirleyici değil (gerilim kipli).
ADC→DSP: (50−100)/(50+100) = −0.333 → |Γ| = 0.33; differential hat 90 Ω hedefiyle iyileştirilir (`⚠️ VERIFICATION REQUIRED` — ölçüm sonucu kaynakta yok).

## §5 Bağımlılıklar

| Yön | Düğüm | İlişki | Wiki-link |
|-----|-------|--------|-----------|
| Yukarı (kaynak) | k058 XMOS xu316 | Master clock ve I2S veri kaynağı | [[../k058-xmos-xu316/xu316-entegrasyon]] |
| Yukarı (kaynak) | k059 USB audio | USB→kokteyl veri girişi | [[../k059-usb-audio/usb-audio-yolu]] |
| Aynı katman | k055 AK4458 | DAC ucu | [[../k055-ak4458-dac/ak4458-dac-rehberi]] |
| Aynı katman | k056 PCM3168A | ADC ucu | [[../k056-pcm3168a-dac-adc/pcm3168a-dac-adc-rehberi]] |
| Aynı katman | k057 I2S/TDM | Protokol taşıyıcısı | [[../k057-i2s-interface/i2s-ve-tdm-rehberi]] |
| Aşağı | k060 analog yol | Analog segment | [[../k060-analog-sinyal-yolu/analog-yol-rehberi]] |
| Yatay | k070 PCB | Hat empedansı/uzunluk kuralları | [[../k070-pcb-tasarim/pcb-rehber]] |
| Yatay | k068 analog besleme | DAC/ADC besleme hattı | [[../k068-guc-kaynagi-analog/analog-besleme]] |
| Üst katman | k03 ses motoru (DSP) | Dijital işleme | kaynak: `K3 DSP / Üst` |

## §6 Kenar Durumları

| # | Senaryo | Etki | Davranış |
|---|---------|------|----------|
| 1 | Tek crystal aktif (24.576 MHz kayıp) | 44.1k ailesi fs üretilemez | Çift crystal düzeni korunur; kayıp crystal `⚠️ VERIFICATION REQUIRED` |
| 2 | MCLK 256fs'e düşürülürse | §4.2 çelişkisi çözülür mü? | Slot yapısı kaynağından doğrulanmadan değiştirilmez |
| 3 | XMOS master'ı slave'a düşerse | Clock kaynak kaybı | Kaynakta alternatif rol tanımı yok → `⚠️ VERIFICATION REQUIRED` |
| 4 | Ferrite bead sayısı 16→12 | Filtre bant genişliği değişir | Adet kaynakta 16; sapma onaysız yapılmaz |
| 5 | ±1 mm uzunluk sapması | Faz kaybı ihmal edilebilir (§4.5) | Kaynak: `length-matched (±1mm tolerance)` |
| 6 | 48k/44.1k aile geçişi | MCLK/SCK frekans değişimi | Crystal seçimi çift crystal ile karşılanır |
| 7 | 10 kΩ ADC yükü | Gerilim kipli kayıp yok | Kaynak tablo `No (voltage mode)` der |
| 8 | 90 Ω hat empedansı ihlali | Yansıma artışı | PCB hedefi k070 kapsamında |

## §7 Hata Modları

| # | Belirti | Kök neden (kaynak/ türetme) | Aksiyon |
|---|---------|-----------------------------|---------|
| HM-01 | DAC çıkışında frekans kayması | §4.2 MCLK 256fs/512fs çelişkisi | Slot/MCLK oranı doğrulanır → `⚠️ VERIFICATION REQUIRED` |
| HM-02 | I2S verisinde bit kayması | BCLK/WS faz ilişkisi bozuk | Eye diagram tekrarı (kaynak: `I2S timing: Eye diagram analizi`) |
| HM-03 | Artık gürültü yüksek | Ferrite/decoupling eksik montaj | 16+16 BOM adedi doğrulanır |
| HM-04 | DAC tarafında yansıma | 50 Ω/100 Ω uyuşmazlığı (§4.6) | Gerilim kipli girişte kabul; doğrulama ölçümü `⚠️ VERIFICATION REQUIRED` |
| HM-05 | ADC→DSP hattında krosover | Eşleşmemiş differential hat | 90 Ω hedefi + sonlandırma 100 Ω × 8 |
| HM-06 | 48 kHz'te kilit yok | 24.576 MHz crystal hatası | Crystal/load cap (18 pF × 4) kontrolü |
| HM-07 | Jitter tavanı (§4.4) | 100 ps RMS ölçüm belirsizliği | Ölçüm yöntemi sorulur → `⚠️ VERIFICATION REQUIRED` |
| HM-08 | Zincir tam çalışmıyor | Clock sync simülasyonu eksik | Kaynak durum: `Simülasyon Aşamasında` |

## §8 Doğrulama & Kanıt

| # | İddia | Kanıt yolu | Durum |
|---|-------|-----------|-------|
| 1 | DAC = AK4458 8 kanal | `k1-donanim/dac-adc-zinciri.md` | ✅ disk |
| 2 | ADC = PCM3168A 8 kanal | `k1-donanim/dac-adc-zinciri.md` | ✅ disk |
| 3 | Clock hiyerarşisi XMOS master | `k1-donanim/dac-adc-zinciri.md` | ✅ disk |
| 4 | BOM 16 ferrite + 16 cap | `k1-donanim/dac-adc-zinciri.md` | ✅ disk |
| 5 | Durum: Simülasyon Aşamasında | `k1-donanim/dac-adc-zinciri.md` | ✅ disk |
| 6 | MCLK 256fs vs 512fs çelişkisi | iki ayrı kaynak satırı (§4.2) | ⚠️ açık |
| 7 | Jitter-SNR eşlemesi | türetme (§4.4), ölçüm yok | ⚠️ VERIFICATION REQUIRED |
| 8 | FR4 εr / gecikme katsayısı | kaynakta yok | ⚠️ VERIFICATION REQUIRED |
| 9 | Slot yapısı (32/64 bit) | kaynakta yok | ⚠️ VERIFICATION REQUIRED |
| 10 | DSP üst katman kodu | `K3 DSP / Üst` bağımlılık satırı | ✅ disk (idialı) |


## §9 Kaynak Kanıt Dizini (birincil kaynak: `dac-adc-zinciri.md`)

> **Dedup notu (2026-10-06 revizyonu):** Bu bölümün önceki sürümü (689 satır) `index.md` §9'daki README kanıt diziniyle **bayt-birebir aynı kopyaydı** (satır 190–871 ≡ `index.md` satır 111–792, fark 0) ve üst satırda kaynağı yanlışlıkla `README.md` olarak etiketliyordu — bu dosyanın birincil kaynağı ise `dac-adc-zinciri.md`'dir. Kopya kaldırılıp yerine **aşağıdaki kısa, iddia-bazlı** kanıt tablosu kondu; hiçbir bilgi silinmedi, yalnızca tekrar eden 689 satır tekilleştirildi. Tam dizinler için §9 altındaki pointerlar kullanılır.
>
> **Birincil kaynak:** `_backup/arch-2026-10-06_1057/architecture/k1-donanim/dac-adc-zinciri.md` (159 satır — salt-okunur)

| Bölüm | İddia (özet) | Kaynak · satır | Kanıt |
|---|---|---|---|
| §1 | Zincir tanımı: clock synchronization · impedance matching · EMI filtering kritik öneme sahip | `dac-adc-zinciri.md` L12 | ✅ disk |
| §3 | DAC = AK4458 (32-bit, 8-kanal) | `dac-adc-zinciri.md` L18 | ✅ disk |
| §3 | ADC = PCM3168A (32-bit, 8-kanal) | `dac-adc-zinciri.md` L19 | ✅ disk |
| §3 · §4.1–4.2 | Clock Frequency 22.5792 / 24.576 MHz (512 fs) ↔ MCLK 256 fs = 11.2896 / 12.288 MHz — **çelişki** | `dac-adc-zinciri.md` L20 · L23 | ✅ disk |
| §3 · §4.1 | I2S Bit Clock 1.4112 / 1.536 MHz (32 fs) · WS 44.1 / 48 kHz | `dac-adc-zinciri.md` L21 · L22 | ✅ disk |
| §3 | Sinyal seviyesi 2.1 Vrms (differential) · empedans 100 Ω differential | `dac-adc-zinciri.md` L24 · L25 | ✅ disk |
| §2.1 | Clock hierarchy: XMOS XU316 master → MCLK/SCK/WS/SD[0:3] → AK4458 + PCM3168A slave | `dac-adc-zinciri.md` L63–L75, L86, L92 | ✅ disk |
| §3 · clock-jitter §2 | Clock accuracy: MCLK · SCK · WS = ±50 ppm, jitter < 100 ps RMS | `dac-adc-zinciri.md` L103–L107 | ✅ disk |
| §3.1 · §4.6 | Empedans eşleşme tablosu (XMOS→DAC · DAC→ADC · ADC→DSP) | `dac-adc-zinciri.md` L125–L129 | ✅ disk |
| §3 | EMI/hat filtresi: BLM18AG601SN1 600 Ω @ 100 MHz · 100 nF · < 50 mm · 90 Ω differential | `dac-adc-zinciri.md` L113–L121 | ✅ disk |
| §3 | Bileşen değerleri: ferrite 16 · decoupling 16 · crystal 22.5792 MHz ×1 + 24.576 MHz ×1 · 18 pF C0G ×4 · 100 Ω ×8 | `dac-adc-zinciri.md` L133–L140 | ✅ disk |
| §5 | Bağımlılıklar: K1 XMOS (clock) · K1 DAC (çıkış) · K1 ADC (giriş) · K3 DSP (üst) | `dac-adc-zinciri.md` L144–L149 | ✅ disk |
| §8 · §11 | Durum 🟡 Simülasyon Aşamasında + LTSpice · eye diagram · pre-compliance · dual crystal · ±1 mm length-match | `dac-adc-zinciri.md` L151–L159 | ✅ disk |

### §9.1 İkincil Dizinler (pointer)

| Dizin | Kapsam | Yer |
|---|---|---|
| README kanıt dizini (**birincil tutucu**) | `k1-donanim/README.md` satır indeksi (~680 satır) | `[[../k054-dac-adc-zinciri/index]]` §9 |
| Birincil kaynak tam satır dizini | `dac-adc-zinciri.md` 159 satır / 130 dolu satır | `[[../k054-dac-adc-zinciri/zincir-kaynak-karsilastirma]]` §9 |
| README dizini ikinci kopyası | `README.md` satır indeksi (⚠️ tekilleştirilmemiş — bkz. `index.md` §6 no.10) | `[[../k054-dac-adc-zinciri/zincir-kaynak-karsilastirma]]` §13 |

## §10 Bağımlılık Matrisi (D01 — k054…k071)

> Bu dosyanın içindeki wiki-link'ler ve D01 aralığının tamamı. İlişki gerekçesi bu klasörün konusundan türetildi; komşu dosyaların içeriği salt-okunur kaynaklardan gelir.

| Klasör | Wiki-link | İlişki (bu dosyaya) |
|---|---|---|
| `k054-dac-adc-zinciri` (DAC-ADC donusum zinciri) | [[../k054-dac-adc-zinciri/zincir-mimari]] | → bu klasör (bu dosya burada) |
| `k055-ak4458-dac` (AK4458 ana DAC) | [[../k055-ak4458-dac/ak4458-dac-rehberi]] | k057 I2S uzerinden beslenir; k060 analog ciktisini devralir |
| `k056-pcm3168a-dac-adc` (PCM3168A DAC+ADC) | [[../k056-pcm3168a-dac-adc/pcm3168a-dac-adc-rehberi]] | k057 I2S uzerinden beslenir; k054 zincirinin ADC ucu |
| `k057-i2s-interface` (I2S / TDM haberlesme) | [[../k057-i2s-interface/i2s-ve-tdm-rehberi]] | k058 xu316 kaynagi ile k055/k056 alim noktasi arasindaki protokol |
| `k058-xmos-xu316` (XMOS xu316 USB ses kokteyi) | [[../k058-xmos-xu316/xu316-entegrasyon]] | k059 USB hattini isler; k057 I2S ciktisini uretir |
| `k059-usb-audio` (USB Audio Class yolu) | [[../k059-usb-audio/usb-audio-yolu]] | k058 xu316 USB ucu; k2-surucu usb-audio-class ile ayni konu zinciri |
| `k060-analog-sinyal-yolu` (Analog sinyal yolu) | [[../k060-analog-sinyal-yolu/analog-yol-rehberi]] | k055 ciktisi ile k063 cikis asamasi arasindaki yol |
| `k061-diff-pair-input` (Differential pair giris) | [[../k061-diff-pair-input/diff-pair-tasarim]] | k060 giris ucunda; k070 PCB differential cizgi kurallarina bagli |
| `k062-vas-stage` (VAS (voltage amplification stage)) | [[../k062-vas-stage/vas-stage-tasarim]] | k064 feedback ile kararlilik; k063 cikis asamasini surer |
| `k063-output-stage` (Cikis (output) asamasi) | [[../k063-output-stage/output-stage-tasarim]] | k065 guc op-amp'i ile kurulur; k069 hoparlor yukunu surer |
| `k064-feedback-network` (Geri besleme agi) | [[../k064-feedback-network/feedback-tasarim]] | k062/k063 kazancini belirler; k060 yolunu kapsar |
| `k065-mjle21194-93` (MJL21194/93 op-amp kurulumu) | [[../k065-mjle21194-93/mjle-op-amp-kurulum]] | k063 cikis asamasinin guc transistorsleri; k071 termal yuku |
| `k066-konnektorler` (Konnektor envanteri) | [[../k066-konnektorler/konnektor-envanteri]] | k060 giris ve k069 cikis ucundaki fiziksel arayuz |
| `k067-koruma-devreleri` (Koruma devreleri) | [[../k067-koruma-devreleri/koruma-rehberi]] | k063 cikis ve k066 konnektor korumasi; k068 besleme sigortasi |
| `k068-guc-kaynagi-analog` (Analog guc kaynagi) | [[../k068-guc-kaynagi-analog/analog-besleme]] | k060/k062/k063 icin analog besleme hattini besler |
| `k069-hoparlor-dizilimi` (Hoparlor dizilimi) | [[../k069-hoparlor-dizilimi/hoparlor-dizilim]] | k063 cikis asamasinin yukunu olusturur |
| `k070-pcb-tasarim` (PCB tasarimi) | [[../k070-pcb-tasarim/pcb-rehber]] | tum D01 dugumlerinin fiziksel tasiyicisi (k061 differential cizgiler dahil) |
| `k071-termal-yonetim` (Termal yonetim) | [[../k071-termal-yonetim/termal-rehber]] | k065 dissipation'i; k063/k069 sicaklik sinirlari |

### §10.1 Bu Dosyadaki Wiki-Link Envanteri

| Hedef | Durum |
|---|---|
| `../k060-analog-sinyal-yolu/analog-yol-rehberi` | ✅ disk |
| `../k057-i2s-interface/i2s-ve-tdm-rehberi` | ✅ disk |
| `../k070-pcb-tasarim/pcb-rehber` | ✅ disk |
| `../k059-usb-audio/usb-audio-yolu` | ✅ disk |
| `../k067-koruma-devreleri/koruma-rehberi` | ✅ disk |
| `../k054-dac-adc-zinciri/clock-jitter-analizi` | ✅ disk |
| `../k054-dac-adc-zinciri/zincir-kaynak-karsilastirma` | ✅ disk |
| `../k058-xmos-xu316/xu316-entegrasyon` | ✅ disk |
| `../k055-ak4458-dac/ak4458-dac-rehberi` | ✅ disk |
| `../k056-pcm3168a-dac-adc/pcm3168a-dac-adc-rehberi` | ✅ disk |
| `../k068-guc-kaynagi-analog/analog-besleme` | ✅ disk |
| `../k054-dac-adc-zinciri/index` | ✅ disk |
| `../k054-dac-adc-zinciri/zincir-mimari` | ✅ disk |
| `../k061-diff-pair-input/diff-pair-tasarim` | ✅ disk |
| `../k062-vas-stage/vas-stage-tasarim` | ✅ disk |
| `../k063-output-stage/output-stage-tasarim` | ✅ disk |
| `../k064-feedback-network/feedback-tasarim` | ✅ disk |
| `../k065-mjle21194-93/mjle-op-amp-kurulum` | ✅ disk |
| `../k066-konnektorler/konnektor-envanteri` | ✅ disk |
| `../k069-hoparlor-dizilimi/hoparlor-dizilim` | ✅ disk |
| `../k071-termal-yonetim/termal-rehber` | ✅ disk |

## §11 Doğrulama Protokolü

> **Revizyon notu (2026-10-06):** 1. adımdaki "≥500 kapısı" yalnız bu klasörün **üç eski dosyasına** (`index.md` · `zincir-mimari.md` · `zincir-kaynak-karsilastirma.md`) uygulanır; içerik-bazlı kanıtla yazılan `clock-jitter-analizi.md` ve `olcum-ve-test-noktalari.md` bu kapıya tabi **değildir** (görev kararı). 4. adım (wiki-link kırıklığı) ve §12 sayımı bu revizyonda yeniden üretilerek doğrulanmıştır.

| # | Adım | Komut (repo kökünden) | Beklenen |
|---|---|---|---|
| 1 | Satır sayısı (≥500 kapısı) | `[System.IO.File]::ReadAllLines('<bu dosya>').Length` | ≥500 (boş satır dahil) |
| 2 | UTF-8 / mojibake | `node .ai/scripts/vault-utf8-writer.mjs verify --file .ai/architecture/k054-dac-adc-zinciri/zincir-mimari` | `mojibake: 0`, `hasBom: false` |
| 3 | Klasör taraması | `node .ai/scripts/vault-utf8-writer.mjs scan --dir .ai/architecture/k054-dac-adc-zinciri` | 0 bulgu |
| 4 | Wiki-link kırıklığı | her wiki-link'in hedefi `.ai/architecture/` altında `.md` dosyası olarak var mı | kırık 0 |
| 5 | Frontmatter | 7 zorunlu alan: title · type · category · updated · version · status · authority (+ date) | eksik yok |
| 6 | Sürüm/tarih | `version: 4.0.0` · `updated: 2026-10-06` | birebir |
| 7 | Commit kapısı | `git status --porcelain -- .ai/architecture/k054-dac-adc-zinciri` | `??` (bu görevde commit ATILMAZ) |
| 8 | Kaynak dokunulmazlık | `git status --porcelain -- _backup/` | bu görevce değişiklik yok (salt-okunur) |
| 9 | REDACTED | dosyada secret/token/anahtar bulunmaması | 0 eşleşme |

## §12 Açık Kalemler (⚠️ işaretli)

> Tarama: bu dosyanın yazar gövdesinde `⚠️` içeren satır sayısı = **16** (otomatik sayım; §9 kaynak dizini ve §12 bölgesi hariç, gövde-satır numarası = dosya satırı − 13).

| Satır | İşaretli madde |
|---|---|
| 3 | Bu belge, CoreMusic donanım katmanında **dijital → analog → dijital** sinyal zincirinin mimarisini, … |
| 46 | \| SD[0:3] \| AK4458 TDMD[0:3] · PCM3168A DOUTA/B \| Seri veri \| kanal başına kbps `⚠️ VERIFICATION… |
| 85 | > **⚠️ VERIFICATION REQUIRED (HM-01):** kesin BCLK/MCLK oranı clock sync testinde ölçülmeden yazılma… |
| 109 | 22.5792 MHz / 1.4112 MHz = 16 → yani 512fs / 32 = 16 slot (64fs slot yapısı için 8 slot). 64fs slot … |
| 114 | Sonuç ≈ **38 dB** — bu, kaynak jitter değerinden türetilmiş **üst sınır kestirimdir**; gerçek sistem… |
| 117 | FR4'de ~6 ps/mm (εr≈4.2 varsayımı — `⚠️ VERIFICATION REQUIRED`, kaynak εr vermiyor). |
| 124 | ADC→DSP: (50−100)/(50+100) = −0.333 → \|Γ\| = 0.33; differential hat 90 Ω hedefiyle iyileştirilir (`… |
| 144 | \| 1 \| Tek crystal aktif (24.576 MHz kayıp) \| 44.1k ailesi fs üretilemez \| Çift crystal düzeni ko… |
| 146 | \| 3 \| XMOS master'ı slave'a düşerse \| Clock kaynak kaybı \| Kaynakta alternatif rol tanımı yok → … |
| 157 | \| HM-01 \| DAC çıkışında frekans kayması \| §4.2 MCLK 256fs/512fs çelişkisi \| Slot/MCLK oranı doğr… |
| 160 | \| HM-04 \| DAC tarafında yansıma \| 50 Ω/100 Ω uyuşmazlığı (§4.6) \| Gerilim kipli girişte kabul; d… |
| 163 | \| HM-07 \| Jitter tavanı (§4.4) \| 100 ps RMS ölçüm belirsizliği \| Ölçüm yöntemi sorulur → `⚠️ VER… |
| 175 | \| 6 \| MCLK 256fs vs 512fs çelişkisi \| iki ayrı kaynak satırı (§4.2) \| ⚠️ açık \| |
| 176 | \| 7 \| Jitter-SNR eşlemesi \| türetme (§4.4), ölçüm yok \| ⚠️ VERIFICATION REQUIRED \| |
| 177 | \| 8 \| FR4 εr / gecikme katsayısı \| kaynakta yok \| ⚠️ VERIFICATION REQUIRED \| |
| 178 | \| 9 \| Slot yapısı (32/64 bit) \| kaynakta yok \| ⚠️ VERIFICATION REQUIRED \| |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-06
**Mode:** Red Team · Human Mode · Truth Mode
