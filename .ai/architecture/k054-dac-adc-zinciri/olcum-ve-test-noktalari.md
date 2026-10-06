---
title: "DAC-ADC donusum zinciri — olcum ve test noktalari"
type: architecture
category: architecture
date: 2026-10-06
updated: 2026-10-06
version: 4.0.0
status: active
authority: reference
---

# DAC-ADC donusum zinciri — ölçüm ve test noktaları

> **Kapsam:** k054 zinciri için **ölçüm hedefleri** (kaynak satırı ile), **test noktaları** (zincir boyunca) ve **K1 donanım / K2 sürücü ölçüm sınırı**. Her hedef salt-okunur yedek kaynağına `dosya · satır` ile bağlanır; kaynakta olmayan hedef **uydurulmaz**.
> **Kardeş dosyalar:** `[[../k054-dac-adc-zinciri/index]]` · `[[../k054-dac-adc-zinciri/zincir-mimari]]` · `[[../k054-dac-adc-zinciri/clock-jitter-analizi]]` · `[[../k054-dac-adc-zinciri/zincir-kaynak-karsilastirma]]`

## §1 Amaç & Kapsam

| Kapsam | Kapsam Dışı |
|---|---|
| SNR / THD+N / jitter / DC offset / termal ölçüm hedefleri ve test adımları | Clock iddialarının karara bağlanması → `[[../k054-dac-adc-zinciri/clock-jitter-analizi]]` §3 |
| K1 (donanım) ↔ K2 (sürücü) ölçüm sorumluluğu sınırı | Sürücü iç implementasyon → k2-surucu |

## §2 Ölçüm Hedefleri (kaynak değerli)

| # | Ölçüm | Hedef | Kaynak · satır |
|---|---|---|---|
| 1 | AK4458 SNR | 125 dB (A-Weighted) | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/ak4458-dac.md` L22 |
| 2 | AK4458 THD+N | −112 dB (%0.00025) | `ak4458-dac.md` L23 |
| 3 | PCM3168A SNR | 118 dB (A-Weighted) | `pcm3168a-dac-adc.md` L22 |
| 4 | PCM3168A THD+N | −100 dB (%0.01) | `pcm3168a-dac-adc.md` L23 |
| 5 | Analog yol THD+N | < %0.001 (1 kHz, 1 W) | `analog-sinyal-yolu.md` L20 |
| 6 | Analog yol SNR | > 120 dB (A-Weighted) | `analog-sinyal-yolu.md` L21 |
| 7 | Analog yol kabul kriteri | THD < %0.001 (LTSpice verified) · SNR > 120 dB (calculated) | `analog-sinyal-yolu.md` L213–L214 |
| 8 | Katman THD hedefi | < 0.005 % | `k1-donanim/README.md` L36 |
| 9 | Katman SNR hedefi | > 100 dB | `README.md` L37 |
| 10 | Çıkış seviyesi | 2.1 Vrms (differential) | `dac-adc-zinciri.md` L24 |
| 11 | Clock jitter / tolerans | < 100 ps RMS · ±50 ppm | `dac-adc-zinciri.md` L103–L107 |
| 12 | Çıkış DC offset (montaj kontrolü) | 0 V hedef · son kontrol < 0.5 V DC offset | `README.md` L168–L169, L171 |

> **Sıralama notu:** 1–4 **bileşen datasheet hedefidir** (doğrulanmış kaynak satırı), 5–9 **katman/bölüm hedefidir**, 12 **montaj-adımı hedefidir**. Hedeflerden hiçbiri bu görevce "ölçüldü/ulaşıldı" diye yazılmaz — durum §5'tedir.

## §3 Test Noktaları (zincir boyunca)

| # | Zincir noktası | Test / yöntem | Kaynak · satır | Sorumlu katman |
|---|---|---|---|---|
| T1 | Clock sync (XMOS → DAC/ADC) | LTSpice simülasyonu · **clock sync test** (bir sonraki adım) | `dac-adc-zinciri.md` L155 · `ozet-durum.md` L34 | K1 |
| T2 | I2S hatları (SCK/WS/SD) | Eye diagram analizi | `dac-adc-zinciri.md` L156 | K1 |
| T3 | EMI / ferrite bead seçimi | Pre-compliance test | `dac-adc-zinciri.md` L157 · `ozet-durum.md` L68 | K1 |
| T4 | DAC çıkışı (AK4458) | SNR / THD+N ölçümü — hedef §2 no.1–2 | `ak4458-dac.md` L22–L23 | K1 |
| T5 | ADC çıkışı (PCM3168A) | SNR / THD+N ölçümü — hedef §2 no.3–4 | `pcm3168a-dac-adc.md` L22–L23 | K1 |
| T6 | Analog yol (k060) | THD+N / SNR — hedef §2 no.5–7 (LTSpice) | `analog-sinyal-yolu.md` L20–L21, L213–L214 | K1 |
| T7 | Amplifikatör çıkışı | Multimetre ile DC offset ölçümü, bias potansiyometresi ile 0 V hedefleme | `README.md` L168–L169, L171 | K1 |
| T8 | Koruma — DC offset eşiği | Eşik testi: > ±1V DC → 0.5 s → speaker disconnect | `koruma-devreleri.md` L18 | K1 |
| T9 | Termal (çıkış transistörleri) | NTC 10 kΩ @ 25 °C ile sıcaklık ↔ direnç ↔ ADC voltaj okuması | `termal-yonetim.md` L23, L94 · `ozet-durum.md` L67 | K1 |
| T10 | PCB I2S routing | Length-match doğrulaması (±1 mm tolerans) | `dac-adc-zinciri.md` L159 | K1 |

## §4 K1 Donanım ↔ K2 Sürücü Ölçüm Sınırı

Ölçüm sorumluluğu iki katmana ayrılır; sınır `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` L63–L71 (Performans Metrikleri) ile tanımlıdır.

| Sınır | Ölçüm | Hedef | Kaynak · satır |
|---|---|---|---|
| **K1 (donanım)** | SNR · THD+N · jitter · DC offset · termal · EMI | §2 tablosu | bu dosya §2 |
| **K2 (sürücü)** | Round-trip latency | < 0.5 ms (ASIO Exclusive) | `k2-surucu/index.md` L67 |
| **K2 (sürücü)** | Buffer boyutu | 32–64 sample @ 96 kHz | `k2-surucu/index.md` L68 |
| **K2 (sürücü)** | CPU kullanımı | < %5 (boşta) | `k2-surucu/index.md` L69 |
| **K2 (sürücü)** | Maksimum kanal sayısı | 128 giriş + 128 çıkış | `k2-surucu/index.md` L70 |
| **K2 (sürücü)** | Desteklenen örnekleme hızları | 44.1k · 48k · 88.2k · 96k · 176.4k · 192k · 352.8k · 384k | `k2-surucu/index.md` L71 |

**Sınır kuralı:** zincirin donanım tarafındaki SNR/THD/jitter sonuçları K1'de, uçtan uca gecikme/önbellek/kanal sonuçları K2'de ölçülür; bir katmanın ölçümü diğerinin hedefi sayılmaz. K2 metrikleri bu görevce **hedef olarak** alınır (gerçekleşen değer `UNKNOWN` — ölçüm kanıtı kaynakta yok).

## §5 Durum & Sonraki Adım

| Kalem | Değer | Kaynak · satır |
|---|---|---|
| Zincir durumu | 🟡 Simülasyon Aşamasında | `dac-adc-zinciri.md` L153 |
| Bir sonraki adım | **Clock sync test** | `ozet-durum.md` L34 |
| Genel ilerleme | %60 | `ozet-durum.md` L47 · L85 |
| Kritik yol | PCB tasarımı → termal simülasyon → prototype → test → assembly (toplam 9 hafta) | `ozet-durum.md` L53–L59 |

## §6 Açık Kalemler (⚠️ işaretli)

| # | Açık | Kanıt | İşaret |
|---|---|---|---|
| 1 | BCLK/MCLK iddia çelişkileri henüz ölçülmedi (test T1 bekliyor) | `clock-jitter-analizi.md` §3 | ⚠️ VERIFICATION REQUIRED |
| 2 | 38 dB jitter-SNR tavanı kestirimdir, ölçüm yok | `zincir-mimari.md` §4.4 | ⚠️ VERIFICATION REQUIRED |
| 3 | K2 metrikleri hedef olarak yazılı; gerçekleşen değer ölçülmedi | §4 | ⚠️ (gerçekleşen: UNKNOWN) |
| 4 | Bileşen hedefleri (§2 no.1–4) datasheet iddiasıdır; bu görevce bağımsız doğrulanmadı | §2 | ⚠️ VERIFICATION REQUIRED |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-06
**Mode:** Red Team · Human Mode · Truth Mode
