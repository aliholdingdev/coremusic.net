---
title: "CoreMusic — Hardware & Audio Architecture (5 Domain Blok Diyagramı)"
type: diagram
category: architecture
date: 2026-10-12
updated: 2026-10-12
status: draft
version: 1.0.0
authority: "Kaynak: v2 spec §2.1 · §2.2 · §2.3 · §2.4 · §2.5 (mini ağaçlar) · §4.3 (kenarlar)"
---

# Hardware / Audio / DSP / Firmware / OS — Sınır Diyagramı

> Beş domainin v2'deki **mini ağaçlarından** türetilmiş blok diyagramıdır.
> Düğüm listeleri birebir taşınmış, **uydurma donanım parametresi yazılmamıştır**;
> tek kenar kaynağı v2 §4.3'tür (`K003-audio` `depends_on`).

## §A Doğrulanmış Zincir (kenarlar: v2 §4.3)

```text
┌─ K001-donanım · v2 §2.2 ───────────────────────────────────┐
│ index.md                                                   │
│ donanım-kernel/  ·  donanım-core/  ·  donanım-api/         │
│ donanaım-type/                                             │
│   ├── hdd/                                                 │
│   ├── audio/                                               │
│   └── ssd/                                                 │
└────────────────────────────────────────────────────────────┘
                              ▲ depends_on: K003 → K001 (§4.3)
┌─ K003-audio · v2 §2.3 ─────────────────────────────────────┐
│ index.md  ·  audio-master.md                               │
│ engine/ · device/ · driver/ · backend/                     │
│ input/ · output/ · routing/ · mixing/                      │
│ realtime/ · synchronization/                               │
│ diagnostics/ · testing/                                    │
└────────────────────────────────────────────────────────────┘
                              │ depends_on: K003 → K004 (§4.3)
                              ▼
┌─ K004-dsp · v2 §2.4 ───────────────────────────────────────┐
│ index.md  ·  dsp-master.md                                 │
│ pipeline/ · equalizer/ · filters/ · dynamics/              │
│ compressor/ · limiter/ · reverb/ · delay/                  │
│ crossover/ · spatial-audio/ · measurement/                 │
│ testing/                                                   │
└────────────────────────────────────────────────────────────┘
```

## §B Kenarsız Bloklar (v2 §2.1 · §2.5 — `depends_on` verisi YOK)

```text
┌─ K005-firmware · v2 §2.5 ──────────────────────────────────┐
│ index.md  ·  firmware-master.md                            │
│ boot/ · runtime/ · device-control/ · audio-control/        │
│ power-control/ · diagnostics/ · update/                    │
│ security/ · testing/                                       │
└────────────────────────────────────────────────────────────┘

┌─ K000-isletim-sistemi · v2 §2.1 ───────────────────────────┐
│ index.md  ·  os-master.md                                  │
│ electroncis/                                               │
│ isletim-sistemi-api/ · isletim-sistemi-audio/              │
│ isletim-sistemi-core/ · isletim-sistemi-cross/             │
│ isletim-sistemi-kernel/ · isletim-sistemi-observability/   │
│ isletim-sistemi-plan/ · isletim-sistemi-runtime/           │
│ isletim-sistemi-security/ · isletim-sistemi-testing/       │
│ isletim-sistemi-turu/                                      │
└────────────────────────────────────────────────────────────┘
```

⚠️ **VERIFICATION REQUIRED:** v2 spec'te `K005-firmware` ve `K000-isletim-sistemi`
için hiçbir `depends_on` kenarı yazılmamıştır → bu ikisi zincire **bağlanmamıştır**
(ara bağlantı uydurulmadı). Kenar verisi Band-1 üretiminden doldukça bu dosya genişler.

## Lejant

| Sembol | Anlam |
|---|---|
| `┌─ … ┐ / │ … │` | **blok** — bir K domain'i ve v2 mini-ağacındaki düğümleri |
| `▲` / `▼` | `depends_on` yönü — okun ucundaki kutu **bağımlılığın hedefidir** |
| `──·──` | blok içindeki alt düğüm listesi (mini ağaç satırı) |
| `├── / └──` | alt-klasör dallanması (`donanaım-type/` örneği) |
| `K00x · v2 §2.y` | blok başlığı = kaynak bölüm (izlenebilirlik) |
| ⚠️ | `VERIFICATION REQUIRED` — kenar/veri yok, uydurma yasak |

## Kaynak Atfı

- **v2 §2.1** K000-isletim-sistemi · **§2.2** K001-donanım · **§2.3** K003-audio ·
  **§2.4** K004-dsp · **§2.5** K005-firmware → blok içerikleri (mini ağaç birebir).
- **v2 §4.3** "Örnek kayıt (boş şablon — K003-audio)" → §A'daki **iki tek kenar**.
- **v2 §2.3/§2.4/§2.5 ELI15 tanımları** → blok sırasının okunuşu: donanım → ses motoru →
  DSP → firmware → işletim sistemi zemini (bu sıra v2 metinlerinin tanım sırasıdır; **kenar değildir**).
- **Dipnot:** teknoloji kanıtları (amplifikatör/DAC/çip modelleri vb.) bu diyagrama ait
  değildir → **K001/K005 master §4** pointer'ı (ilgili master dosyaları üretim aşamasında —
  ⚠️ VERIFICATION REQUIRED).
- Eş kaynak: `.ai/architecture/index.md` §5 (25 klasör tablosu) · §6 (GAP/CONFLICT).

## Durum Notu

- `status: draft` — diyagram **kaynak sadakati** taşır, tamamlanmışlık iddiası taşımaz.
- **Kenarlar doldukça genişler:** Band-1 üretiminde her `index.md` `depends_on`
  alanı dolduğunda §B blokları zincire bağlanır; bağlantı **kanıtsız çizilmez**.
