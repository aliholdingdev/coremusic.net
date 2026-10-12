---
title: "CoreMusic — Domain Architecture (25 K Domain Haritası)"
type: diagram
category: architecture
date: 2026-10-12
updated: 2026-10-12
status: draft
version: 1.0.0
authority: "Kaynak: v2 spec §1.1 (ana ağaç) · §4.7 (band cetveli) · §3.1 (GAP/CONFLICT)"
---

# Domain Architecture — Band-1 Domain Haritası

> v2 §1.1 ana ağacının ASCII render'ı + §4.7 band cetvelinin mini hali.
> İmla ve yapı kaynağa sadıktır: `donanaım-type` · `identitiy` · `integraion` ·
> `comilance` · `comere` · `licence` · `recomenataion` **dokunulmaz** (v2 §3 KURAL 1).

## §A Ana Ağaç — v2 §1.1 (birebir)

```text
.ai/
└── architecture/
    ├── index.md
    ├── README.md
    │
    ├── adr/
    │   ├── index.md
    │   ├── templates/
    │   └── decisions/
    │
    ├── diagrams/
    │   ├── enterprise-architecture.md
    │   ├── domain-architecture.md
    │   ├── dependency-graph.md
    │   ├── runtime-architecture.md
    │   └── hardware-audio-architecture.md
    │
    ├── K000-isletim-sistemi/
    ├── K001-donanım/
    ├── K003-audio/
    ├── K004-dsp/
    ├── K005-firmware/
    ├── K006-yapay-zeka/
    ├── K007-data/
    ├── K008-domain/
    ├── K009-security/
    ├── K010-identitiy/
    ├── K011-application/
    ├── K012-services/
    ├── K013-integraion/
    ├── K014-network/
    ├── K015-media/
    ├── K016-AI/
    ├── K017-observability/
    ├── K018-devops/
    ├── K019-testing/
    ├── K020-adr/
    ├── K020-comilance/
    ├── K020-comere/
    ├── K020-licence/
    ├── K020-discovery/
    ├── K020-recomenataion/
    └── K21.....k5000 etc.../
```

**Yapısal işaretler (v2 §1.1 + §3.1 — düzeltilmedi, kaydedildi):**

| İşaret | Tür | Durum |
|---|---|---|
| `K002` hiç yok — `K001 → K003` atlanıyor | ID GAP | ⚠️ VERIFICATION REQUIRED |
| 6 klasör aynı `K020` önekinde (adr · comilance · comere · licence · discovery · recomenataion) | ID CONFLICT | ⚠️ VERIFICATION REQUIRED |
| `K020-comere` içeriği bilinmiyor | UNKNOWN | ⚠️ VERIFICATION REQUIRED — tahmin yasak |
| Band-1 klasör toplamı: **19 tekil K** (K000–K019, K002 hariç) **+ 6× K020 = 25 klasör** | sayım | ✅ `index.md` §5 ile aynı |

## §B Band Cetveli (mini) — v2 §4.7 + ADR-097

| Bant | Aralık | Slot | Durum |
|---|---|---|---|
| Band-1 | K000 – K020 | 21 | ✅ TANIMLI (19 tekil K + 6× K020 = 25 klasör) |
| Band-2 | K021 – K120 | 100 | ⏸ RESERVED · ADR-097 — klasör AÇILMAZ |
| Band-3 | K121 – K320 | 200 | ⏸ RESERVED · ADR-097 — klasör AÇILMAZ |
| Band-4 | K321 – K720 | 400 | ⏸ RESERVED · ADR-097 — klasör AÇILMAZ |
| Band-5 | K721 – K1520 | 800 | ⏸ RESERVED · ADR-097 — klasör AÇILMAZ |
| Band-6 | K1521 – K3120 | 1.600 | ⏸ RESERVED · ADR-097 — klasör AÇILMAZ |
| Band-7 | K3121 – K5000 | 1.880 | ⏸ RESERVED · ADR-097 — klasör AÇILMAZ |
| **TOPLAM** | **K000 – K5000** | **5.001** | 20 tanımlı slot + 1 GAP + 4.980 rezerve |

```text
   K000                                                              K5000
     │                                                                  │
     ▼                                                                  ▼
     ├─B1─┼────────B2────────┼───────────B3───────────┼────────B4────────┼──B5───┼─B6──┼B7┤
     K000 K020              K021                    K121                K321 ... K1520  K3120 K5000
     ▲                                                                 ▲
     │ 21 TANIMLI                                                     │ 4.980 BOŞ SLOT
     │ (diskte var)                                                   │ (yalnız ADR ile doldurulur)
```

## Lejant

| Sembol | Anlam |
|---|---|
| `├── X/` · `└── X/` | **Klasör düğümü** — bir domain (kademe 1) |
| `│` · `└──` | ağaç dallanması (iç içe hiyerarşi) |
| `K21.....k5000 etc.../` | v2'de yazıldığı gibi rezerve alan (isim verilmemiş) |
| `B1..B7` | band aralığı — slot cetveli, **klasör değil** |
| `▲ · ⏸` | tanımlı / rezerve durum işareti |

## Kaynak Atfı

- **v2 §1.1** "Ana Ağaç" → §A (birebir taşındı; düğüm eklenmedi/silinmedi).
- **v2 §4.7** "BANT (BAND) YERLEŞİM ŞEMASI" → §B tablo (mini hali) + slot cetveli (birebir).
- **v2 §3.1** "Bu ağacdaki iç çakışmalar" + **v2 §1.1** alt notu (2 yapısal nokta) → işaret tablosu.
- **v2 §4.7 kuralı:** band yalnız **rezervasyon aralığıdır**; dosya/klasör açılmaz, numara ADR ile atanır.
- Eş kaynak: `.ai/architecture/index.md` §4 (band cetveli) · §5 (25 klasör tablosu) · §6 (GAP/CONFLICT).

## Durum Notu

- `status: draft` — kaynak sadakati taşır; Band-2..7 **klasör olarak açılana kadar bu harita genişlemez**.
- **Kenarlar doldukça genişler:** kademe-2+ node'lar (`K0xx/<alt-ad>`, v2 §4.5) eklendikçe ağaç derinleşir.
