---
title: "k0 İşletim Sistemi — Katman Workflow"
type: docs
category: architecture
version: 1.0.0
status: active
authority: "Derived: .ai/WORKFLOW.md (süreç SSOT) — bu dosya katman akışı"
updated: 2026-10-07
tier: 3
domain: k0-isletim-sistemi
ssot: false
risk: low
owner: "win-sw"
depends-on: [".ai/architecture/k0-isletim-sistemi/index.md", ".ai/WORKFLOW.md"]
---

# k0 — Katman Workflow

> Genel süreç [[WORKFLOW]] (SSOT). Bu dosya: bu katmana **özel** kapı ve akış.

## Envanter Doldurma Akışı (bu katman)

```text
[1] K-matrix K0 satırını al (CLAUDE.md §5)
        ↓
[2] 01-04 alt bölümlerin konu başlıklarını aç (index §5)
        ↓
[3] Web doğrulama (deepwiki/exa) — her teknoloji için güncel kaynak
        ↓
[4] Repo taraması — kanıt var mı? (2026-10-07: YOK → PLANNED)
        ↓
[5] Envanter kaydı yaz (Kx.yy.zzz + 12 alan)  → inventory/k0-platformlar-partNN.md
        ↓
[6] Hibrit zenginleştirme (Q38): script iskeleti → elle risk/guvenlik/not
        ↓
[7] validator --check TEMİZ → Katman onayı (R10) → R11 tam tarama
        ↓
[8] index §Durum: 🅿️ → ✅ active · katalog + context güncelle · log append
```

## Kapılar (bu katmanda)

| Kapı | Koşul | Onay |
|---|---|---|
| Faz 1 k0 pilotu | validator 0 ihlal + envanter part ≥500 satır (Q21) | 👤 win-sw sahipli + insan |
| Derinleşme (01-04) | yeni dosya her seferinde | 👤 (Q32) |
| Durum → IMPLEMENTED | yalnız repo kod kanıtı ile | 👤 + kod linki |

## Hata Akışı

- **validator ihlal** → düzelt → tekrar `--check` (3 başarısız → dur + soru, anti-overthink).
- **Kanıt yok** → `UNKNOWN`/`PLANNED` yaz, uydurma (R9.3).
- **Çelişki** → R8 formatı (Kaynak A/B/Etki/Otorite/Öneri/Karar) → eskalasyon.
