# DSP Zincir Haritası — Sinyal Yolu · 31-band EQ · 4 Reverb · 1.0→8.1 Matris

> **Otorite:** `.ai/VISION.md` §17.2 (Ses Sinyal Zinciri Detayı) ·
> `.ai/CLAUDE.md` §19 (DSP Efektleri + Reverb Modları) · §5 K3/K2 satırları ·
> `.ai/PROJECTS.md` §2 (başlık 2 ve 4) · §7.1.1 / §7.1.7 / §7.1.9.
> Bu dosya SKILL.md §4 üzerinden bağlayıcıdır; zincir K3 → K2 → K1 sırası bozulamaz
> (K3 katmanı K2 donanım sürücüsüne sıkı bağımlıdır — CLAUDE §5 K3).

## §1 Uçtan Uca Sinyal Zinciri (VISION §17.2)

```text
[Kaynak] → [XMOS XU316 USB] → [PCM3168A ADC] → [NevaEngine DSP / K3]
                                                          │
                                          ┌───────────────┘
                                          ▼
                        DSP PIPELINE: EQ → Reverb → Crossover → Limiter
                                          │
                                          ▼
                              [AK4458 DAC] → [8× Class AB 50W / K16]
                                          │
                                          ▼
                            [8.1 Surround Hoparlörler]  (+1 LFE aktif subwoofer)
                                          │
                            [±35V LM5122 Güç Kaynağı] (6S LiPo / DC Adapter)
```

Her bileşen bir öncekinin üzerine inşa edilir; hiçbiri diğerini atlayamaz. Bu zincir
kırılırsa ses kalitesi düşer veya sistem çalışmaz (VISION §17.2 bağlayıcı cümlesi).

## §2 DSP Pipeline Aşamaları (sıra: EQ → Reverb → Crossover → Limiter)

| # | Aşama | Detay | Kaynak |
|---|-------|-------|--------|
| 1 | **Parametrik/Grafik EQ** | **31-band**, 20Hz–20kHz, **1/3 oktav** stüdyo seviyesi filtreleme; parametrik + grafik + AI otomatik türleri | PROJECTS §2 başlık 2 · §7.1.9 |
| 2 | **Reverb** | **4 mod:** Geniş Konser · Düğün Salonu · Oda · Stüdyo (psikoakustik algoritmalar) | `.ai/CLAUDE.md` §19 |
| 3 | **Crossover** | Frekans bandı bölünmesi — LFE/kanal ayrımı dahil | PROJECTS §7.1.1 / §7.1.7 |
| 4 | **Limiter** | **True Peak Brickwall Limiter** — ses sonuna kadar açılsa da dijital çatlamayı önler; **THD+N <%0.005 · SNR >105dB** | PROJECTS §2 başlık 2 |

Ek stage'ler (aynı zincirin parçası): **Compressor** (CLAUDE §19 DSP Efektleri).

### Reverb mod adı çelişkisi (Contradiction Gate — Guardrail #12)

- **CLAUDE §19 (bu skillin kullandığı bağlayıcı liste):** Geniş Konser, Düğün Salonu, Oda, Stüdyo.
- **PROJECTS §2 başlık 2 (farklı pazarlama adları):** Düğün Salonu, Konser Alanı & Arena,
  Canlı Stüdyo, Kulüp.
- İkisi vault içinde çelişir → kod yazarken mod adlarını enum'da sabitlemeden önce
  Vault Steward'a sor / kullanıcıya DUR (§7A #2). SSOT sırası: `.ai/CLAUDE.md` > diğerleri.

## §3 1.0 → 8.1 Yönlendirme Matrisi (PROJECTS §2 başlık 4 · §7.1.1)

| Mod | Kanallar | Not |
|-----|----------|-----|
| 1.0 | Mono | Tek kanal |
| 2.0 / 2.1 | Stereo (+ subwoofer) | Hi-Fi |
| 4.1 | Quadraphonic | LFE aktif |
| 5.1 / 7.1 | Ev sineması | LFE aktif |
| **8.1** | 7.1 surround + **1 LFE** | Stüdyo referansı — "(+1 LFE aktif subwoofer)" |

- Kanallar **matris düzeyinde** denetlenir (PROJECTS §1.1 "matris düzeyinde denetler").
- **Aktif Subwoofer Yönetimi:** bağımsız LFE bas frekans süzme + faz hizalaması (PROJECTS §2 başlık 4).
- Donanım doğrulama: ASIO/WASAPI/ALSA üzerinden (PROJECTS §1.1); K2 katmanı ayrıca
  PipeWire, CoreAudio, I2S taşır (kök CLAUDE §5 K2 satırı).
- Çıkış: AK4458 DAC (32-bit 8ch) → 8× Class AB 50W → 8.1 hoparlörler.

## §4 Hedef Kalite & Yol Haritası

| Kriter | Değer | Kaynak |
|--------|-------|--------|
| Aktif pipeline | 32-bit Float (Float32) | CLAUDE §19 · PROJECTS §7.1.1 |
| Yol haritası | **64-bit float** (double precision çekirdek AR-GE) | PROJECTS §2 başlık 3 |
| THD+N | <%0.005 | PROJECTS §2 başlık 2 |
| SNR | >105dB | PROJECTS §2 başlık 2 |
| Bit-perfect / OS mikser bypass | Neva Engine OS mikser baypas eder, bit-perfect aktarım | PROJECTS §1.1/§7.1.1 · `.ai/glossary.md` Bit-Perfect |
| Test | Google Test · coverage ≥80% min / ≥90% hedef | PROJECTS §7.1.1 |

## §5 Değişiklik Kuralları (bu haritaya müdahale ederken)

1. Aşama sırası (EQ → Reverb → Crossover → Limiter) değiştirilemez; değişiklik
   mimari karardır → Human Approval Gate (Guardrail #14) + vault'a ADR notu.
2. Yeni efekt/mod ekleme akışı → `examples/add-reverb-mode.md`.
3. Hot-path kodu → `references/realtime-rules.md` (zero-allocation/lock-free/noexcept/64B).
4. Sürücü/gecikme etkisi → `references/latency-targets.md`.
5. K2'den K3'e doğrudan bağımlılık zorunlu; K3 asla K2'yi atlamaz (CLAUDE §5 K3).

*CoreMusic Skill v3.0 — references/dsp-chain-map.md — Updated: 2026-10-07*