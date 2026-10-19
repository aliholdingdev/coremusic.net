---
title: "K001 Önerilen Dosya Ağacı Raporu"
type: report
node_id: reports/k001-taksonomi/k001-onerilen-agac-raporu
status: active
evidence:
  - ".ai/.decisions/draft/ADR-102-tur-taksonomi-genislemesi-5-dal.md §7"
  - "donanaım-type/{hdd,ssd,audio}/** (mevcut 4-dosya pattern)"
  - "donanaım-type/display/** (2 dosya mevcut + 2 eksik → bu oturumda tamamlandı)"
validation: "PROPOSED — APPROVAL REQUIRED (ADR-102 draft)"
group: rapor
date: 2026-10-13
updated: 2026-10-13
---

# K001 Önerilen Dosya Ağacı Raporu

## §1 donanaım-type/ — Önerilen Ağaç (10 bağlam · 46 dosya)

> **Düzeltme (2026-10-13):** İlk yazım "8 bağlam / power yok" derdi; `power/`
> diskte mevcut (4 dosya, önceki oturum) — ağaç 9 bağlam ve 42 dosya olarak
> hizalandı (zero-hallucination: disk > taslak).

```
donanaım-type/
├── index.md                          (orchestrator günceller: children + §GRUPLAR ✓)
├── donanaım-type-kapsam.md           (§7.3 stale-citation düzeltildi — 4 edit #3 ✓)
├── donanaım-type-dogrulama.md
├── audio/                            (7 dosya — MEVCUT, değişmez)
│   ├── index.md · audio-kapsam.md · audio-dogrulama.md · audio-ornekler.md
│   └── cipler/ (ak4458-dac.md · pcm3168a-codec.md · tpa3255-amplifier.md)
├── hdd/                              (4 dosya — MEVCUT, değişmez)
├── ssd/                              (4 dosya — MEVCUT, değişmez)
├── display/                          (4/4 ✓ — 2 önceki oturum + 2 bu oturum)
├── power/                            (4/4 ✓ — önceki oturum, Vault Steward onayı)
├── cables/                           (4/4 ✓ — bu oturum, subagent)
│   ├── index.md · cables-kapsam.md · cables-dogrulama.md · cables-ornekler.md
├── network/                          (4/4 ✓ — bu oturum, subagent · 1.970 satır)
│   ├── index.md · network-kapsam.md · network-dogrulama.md · network-ornekler.md
├── memory/                           (4/4 ✓ — bu oturum, subagent · 1.290 satır)
│   ├── index.md · memory-kapsam.md · memory-dogrulama.md · memory-ornekler.md
└── cpu/                              (4/4 ✓ — bu oturum, subagent · ~85 KB)
    ├── index.md · cpu-kapsam.md · cpu-dogrulama.md · cpu-ornekler.md
└── gpu/                              (4/4 — paralel oturum 21:52–22:49, ADR-102 DIŞI)
    ├── index.md · gpu-kapsam.md · gpu-dogrulama.md · gpu-ornekler.md (✓ 22:49)
```

**Toplam donanaım-type dosyası:** 42 (ADR-102 kapsamı: kök 3 + audio 7 + 8 dal × 4) + gpu 4 (paralel oturum, kapsamları dışında) = **46 dosya** (2026-10-13 22:55 glob sayımı: 46 ✓ — 22:30 sayımında 45 idi, `gpu-ornekler.md` 22:49'da eklendi)

## §2 Dosya Pattern'i (her yeni dal — hdd şablonuyla birebir)

| Dosya | node_type | group | Amaç |
|---|---|---|---|
| `index.md` | bounded-context | mimari | Künye, children, ELI15, PROPOSED uyarısı |
| `{dal}-kapsam.md` | module | mimari | Kapsam sınırı, tip kayıtları, komşu düğüm sınırı |
| `{dal}-dogrulama.md` | module | kanit | İddia→kanıt matrisi, URL (HEAD, tarihli), repo ölçüm |
| `{dal}-ornekler.md` | module | kod | §1 gerçek repo kodu ("— yok" olabilir), §2+ adım şablonları |

## §3 Rapor Ağacı (.ai/reports/k001-taksonomi/)

```
.ai/reports/k001-taksonomi/
├── index.md                          (indeks — 10 dosya)
├── k001-mevcut-durum-raporu.md
├── k001-tur-envanteri-raporu.md      (types — 25 alan şeması)
├── k001-cihaz-envanteri-raporu.md    (devices — 26 alan şeması özeti)
├── k000-os-donanima-esleme-raporu.md (os — K000 sınırı)
├── k001-domain-esleme-raporu.md      (K001-K005 paralel + eksik dal önerileri)
├── k001-celiski-raporu.md            (tüm çelişkiler — gizleme yok)
├── k001-onerilen-agac-raporu.md      (bu dosya)
├── k001-uygulama-plani-raporu.md
└── k001-kabul-kriterleri-raporu.md
```

## §4 ADR Ağacı

```
.ai/.decisions/draft/
├── ADR-097-k-space-v3-...md          (MEVCUT — draft)
└── ADR-102-tur-taksonomi-genislemesi-5-dal.md   (YENİ — bu oturum)
```

`.ai/.decisions/index.md` §4A: ADR-102 satırı eklenir, draft sayısı 1→2.

**Authority:** Bayram Ali / Vault Steward
