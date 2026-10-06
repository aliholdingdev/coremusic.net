---
title: "DAC-ADC donusum zinciri — clock & jitter analizi"
type: architecture
category: architecture
date: 2026-10-06
updated: 2026-10-06
version: 4.0.0
status: active
authority: reference
---

# DAC-ADC donusum zinciri — clock & jitter analizi

> **Kapsam:** k054 clock zinciri (MCLK · SCK/BCLK · WS) — fs aileleri, kaynak çelişkileri, jitter bütçesi ve çift crystal kurgusu. Bu dosya **içerik-bazlı** üretilmiştir: her satır salt-okunur yedek kaynağına `dosya · satır` ile bağlanır; kaynakta olmayan değer **uydurulmaz**.
> **Kardeş dosyalar:** `[[../k054-dac-adc-zinciri/index]]` · `[[../k054-dac-adc-zinciri/zincir-mimari]]` · `[[../k054-dac-adc-zinciri/olcum-ve-test-noktalari]]` · `[[../k054-dac-adc-zinciri/zincir-kaynak-karsilastirma]]`

## §1 Amaç & Kapsam

DAC → ADC zincirinde saat, XMOS XU316'dan üretilir ve hem AK4458 (DAC) hem PCM3168A (ADC) **slave** olarak beslenir; kaynak: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/dac-adc-zinciri.md` L63–L75 (Clock Hierarchy), L86 (AK4458 Slave), L92 (PCM3168A Slave).

| Kapsam | Kapsam Dışı |
|--------|-------------|
| fs aileleri, BCLK/MCLK iddiaları, jitter bütçesi, çift crystal, clock sync doğrulama kanıtları | I2S protokolünün tam spesifikasyonu → `[[../k055-ak4458-dac/ak4458-dac-rehberi]]` değil; protokol ayrı dosyada: k057 |
| K1 donanım clock kurgusu (kaynak değerli) | K2 sürücü önbellek/latency metrikleri → `[[../k054-dac-adc-zinciri/olcum-ve-test-noktalari]]` §4 |

## §2 Clock Aileleri (kaynak değerli)

| Parametre | 44.1k ailesi | 48k ailesi | Oran | Kaynak · satır |
|---|---|---|---|---|
| Clock Frequency (ana MCLK) | 22.5792 MHz | 24.576 MHz | 512 fs | `dac-adc-zinciri.md` L20 |
| MCLK (spesifikasyon tablosu) | 11.2896 MHz | 12.288 MHz | 256 fs | `dac-adc-zinciri.md` L23 · `i2s-interface.md` L22 |
| I2S Bit Clock (SCK) | 1.4112 MHz | 1.536 MHz | 32 fs | `dac-adc-zinciri.md` L21 |
| Word Select (WS) | 44.1 kHz | 48 kHz | 1 fs | `dac-adc-zinciri.md` L22 |
| Clock tolerance | ±50 ppm | *kaynakta ayrı satır yok* | — | `dac-adc-zinciri.md` L103–L107 |
| Jitter | < 100 ps RMS | *kaynakta ayrı satır yok* | — | `dac-adc-zinciri.md` L103–L107 |

> Clock Accuracy tablosu (L103–L107) yalnız **44.1 kHz** değerlerini listeler (MCLK 22.5792 MHz · SCK 1.4112 MHz · WS 44.1 kHz); 48k ailesi için ayrı tolerans/jitter satırı kaynakta **yoktur** → `⚠️ VERIFICATION REQUIRED` (§7 no.4).

## §3 Çelişkiler (HM-01) — BCLK ve MCLK

Salt-okunur kaynaklarda BCLK için **üç**, MCLK için **iki** farklı iddia vardır; hiçbiri bu dosyada karara bağlanmaz.

| # | İddia | Kaynak · satır | Aritmetik kontrol | Durum |
|---|---|---|---|---|
| B1 | `1.4112 MHz / 1.536 MHz` (32 fs) | `dac-adc-zinciri.md` L21 · L82 | 44.100 × 32 = 1.4112 MHz | ✅ tutarlı |
| B2 | `64fs × 32-bit = 2.1168 MHz` | `i2s-interface.md` L23 | 64 fs @ 44.1 kHz = 2.8224 MHz; yazan 2.1168 MHz = 48 fs | ⚠️ tutarsız |
| B3 | `2 × 2 × 32 × 44100 = 4.2336 MHz` | `i2s-interface.md` L36 | ifade 5.6448 MHz (128 fs) verir; yazan 4.2336 MHz = 96 fs | ⚠️ tutarsız |
| M1 | `MCLK 256fs = 11.2896 / 12.288 MHz` | `dac-adc-zinciri.md` L23 · `i2s-interface.md` L22 | 44.100 × 256 = 11.2896 MHz | ✅ tutarlı (256 fs) |
| M2 | `Clock Frequency 22.5792 / 24.576 MHz` | `dac-adc-zinciri.md` L20 | 44.100 × 512 = 22.5792 MHz | ✅ tutarlı (512 fs) — **M1 ile çelişir** |

**⚠️ VERIFICATION REQUIRED (HM-01):** 256 fs ↔ 512 fs ve 32 fs ↔ 48/96/128 fs iddiaları **clock sync testinde ölçülmeden** karara bağlanmaz; karar `audio-hw` + `dsp-fw` görüşü ve Vault Steward onayı ister. Bu dosyanın §2 tablosu iki iddiayı da **yan yana** taşır, tekini seçmez.

## §4 Jitter Bütçesi & SNR Tavanı

| Kalem | Değer | Kaynak · satır |
|---|---|---|
| Jitter hedefi (MCLK · SCK · WS) | < 100 ps RMS | `dac-adc-zinciri.md` L105–L107 |
| Tolerans | ±50 ppm | `dac-adc-zinciri.md` L105–L107 |
| Jitter → SNR tavanı kestirimi | ≈ **38 dB** | `zincir-mimari.md` §4.4 (türetme) |
| Jitter risk kaydı | Clock jitter · olasılık Düşük · etki Düşük · mitigasyon *Crystal selection* | `k1-donanim/ozet-durum.md` L70 |

Kestirim (`zincir-mimari.md` §4.4): `SNR_jitter = −20·log10(2π·f_in·t_j)`, f_in = 20 kHz, t_j = 100 ps RMS → 2π × 20.000 × 10⁻⁷ = 1.2566×10⁻² → ≈ **38.02 dB ≈ 38 dB**. Bu değer **üst sınır kestirimdir** (ölçüm değildir): AK4458 için kaynak 125 dB SNR hedefi verir (`ak4458-dac.md` L22) ve 100 ps jitter bu hedefin altında kalır → `⚠️ VERIFICATION REQUIRED` (jitter'ın **ölçüm yöntemi kaynakta yok**).

## §5 Çift Crystal & Clock Sync Kurgusu

| Öğe | Değer | Kaynak · satır |
|---|---|---|
| Crystal (44.1k) | 22.5792 MHz × 1 | `dac-adc-zinciri.md` L137 |
| Crystal (48k) | 24.576 MHz × 1 | `dac-adc-zinciri.md` L138 |
| Load cap | 18 pF C0G × 4 | `dac-adc-zinciri.md` L139 |
| Çift crystal kararı | "Dual crystal (22.5792MHz + 24.576MHz) seçildi" | `dac-adc-zinciri.md` L158 |
| Clock Sync bloğu | XMOS master ↔ Clock Sync ↔ Crystal Osc. | `dac-adc-zinciri.md` L39–L41 |

Doğrulama kanıtları (durum satırı): Clock synchronization **LTSpice** ile simulate edildi (L155) · I2S timing **eye diagram** analizi yapıldı (L156) · EMI **pre-compliance** ile ferrite bead seçimi doğrulandı (L157) · PCB routing I2S traces **±1 mm** length-matched (L159) — kaynak: `dac-adc-zinciri.md` L155–L159.

## §6 Durum & Sonraki Adım

| Kalem | Değer | Kaynak · satır |
|---|---|---|
| Zincir durumu | 🟡 Simülasyon Aşamasında | `dac-adc-zinciri.md` L153 |
| Bir sonraki adım | **Clock sync test** | `k1-donanim/ozet-durum.md` L34 |
| Genel ilerleme | %60 | `k1-donanim/ozet-durum.md` L47 · L85 |

Sonraki adım clock sync testi tamamlanmadan §3'teki BCLK/MCLK iddiaları ve §4'teki jitter bütçesi **kesin sayılmaz**.

## §7 Açık Kalemler (⚠️ işaretli)

| # | Açık | Kanıt | İşaret |
|---|---|---|---|
| 1 | BCLK üç iddia (1.4112 / 2.1168 / 4.2336 MHz) | §3 B1–B3 | ⚠️ VERIFICATION REQUIRED (HM-01) |
| 2 | MCLK 256 fs ↔ 512 fs çelişkisi | §3 M1–M2 | ⚠️ VERIFICATION REQUIRED (HM-01) |
| 3 | Jitter ölçüm yöntemi kaynakta yok; 38 dB tavanı kestirim | §4 | ⚠️ VERIFICATION REQUIRED |
| 4 | 48k ailesi için tolerans/jitter satırı kaynakta yok | §2 | ⚠️ VERIFICATION REQUIRED |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-06
**Mode:** Red Team · Human Mode · Truth Mode
