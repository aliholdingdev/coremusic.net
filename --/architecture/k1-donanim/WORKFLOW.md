---
title: "k1 Donanım — Katman Workflow"
type: docs
category: architecture
version: 1.0.0
status: active
authority: "Derived: .ai/WORKFLOW.md (süreç SSOT) — bu dosya katman akışı"
updated: 2026-10-07
tier: 3
domain: k1-donanim
ssot: false
risk: low
owner: "audio-hw"
depends-on: [".ai/architecture/k1-donanim/index.md", ".ai/WORKFLOW.md"]
---

# k1 — Katman Workflow

> Genel süreç [[WORKFLOW]] (SSOT). Bu dosya: bu katmana **özel** kapı ve akış.

## Envanter Doldurma Akışı (bu katman)

```text
[1] K-matrix K1 satırını al (anayasa §5 — 120 bileşen) + domain d03 tablosu K100–K149
        ↓
[2] Web doğrulama — xmos.com · ti.com · akm.com · onsemi.com (2026-10-07 ✅ kök CLAUDE §Web Verification)
        ↓
[3] Repo taraması — kod kanıtı var mı? (2026-10-07: C++/firmware/kod YOK → DESIGN/PLANNED)
        ↓
[4] Envanter kaydı yaz (K1.yy.zzz + 12 alan) → inventory/k1-{tür}-partNN.md
        ↓
[5] Hibrit zenginleştirme (Q38): risk/guvenlik/not alanları elle kanıtla tamamlanır
        ↓
[6] validator --check TEMİZ (11 check) → Katman onayı (R10) → R11 tam tarama
        ↓
[7] index §Durum: 🅿️ → ✅ active · katalog + context güncelle · log append
```

## Kapılar (bu katmanda)

| Kapı | Koşul | Onay |
|---|---|---|
| k1 envanter part'ı | validator 0 ihlal + part ≥500 satır (R4.3) | 👤 audio-hw sahipli + insan |
| risk:high kayıt (güç / termal / DC-offset) | U2 — taslak aşamasında bile insan onayı | 👤 insan her seferinde |
| Durum → IMPLEMENTED | yalnız repo kod kanıtı ile | 👤 + kod linki |

## Hata Akışı

- **validator ihlali** → düzelt → tekrar `--check` (3 başarısız → dur + soru, anti-overthink).
- **Kanıt yok** → `UNKNOWN` / `PLANNED` / `DESIGN` yaz; uydurma `IMPLEMENTED` YASAK (R9.3 · H1).
- **Çelişki** (spec ≠ ADR ≠ anayasa) → R8 formatı (Kaynak A / Kaynak B / Etki / Otorite / Öneri / Karar) → eskalasyon.
- **Kapsam sızması** (k2 sürücü / k3 DSP / firmware içeriği bu dizine girerse) → DUR + doğru katmana transfer (AGENTS §3 D9 · R18.5).