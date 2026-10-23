---
title: "app-cpp-projects — Surface Context"
type: context
category: surface
version: 1.0.0
status: active
authority: "SSOT: .ai/CLAUDE.md — Full rules read @.ai/AGENTS.md"
updated: 2026-10-23
---
# app-cpp-projects — Surface Context

> **Görev:** Bu yüzey CoreMusic'in C++/sürekli-zaman ses motoru yüzeyidir.
> **Önemli gerçek (2026-10-23 disk kanıtı):** `app-cpp-projects/` klasörü
> **boştur (0 dosya)** — C++ kodu `--/neva-engine/` altına taşınmıştır
> (commit 9fd9991). Taşımanın kalıcı olup olmadığı vault'ta **açık karar
> maddesidir** (`⚠️ VERIFICATION REQUIRED` — `.ai/PLAN.md:330` TODO).

## eli10 — Öğretici

Bu klasör şu an boş; gerçek kod `--/neva-engine/` altında durur. Orada CMake ile
kurulan **C++20** bir ses motoru var: yalnız **WASAPI** ses çıkışı, ctest ile
test edilen 15 kaynak dosya. Sen bu yüzeye girdiğinde önce taşınma kararını
doğrular, sonra kodu `--/neva-engine/` içinde bulursun.

## eli15 — Derinlemesine

C++ tarafının mimarisi üç katman: (1) **motor çekirdeği** (`--/neva-engine/`
altında, CMake + C++20, RAII/smart pointer, const-correctness), (2) **platform
ses backend'i** — şu an yalnız WASAPI (Windows), OS backend soyutlama katmanı
üzerinden genişletilebilir, (3) **test katmanı** — ctest ile derlenen ve
çalıştırılan testler. Prensipler: RT audio callback **lock-free**, buffer'lar
baştan allocate, MISRA-C:2025 disiplini, ölçüm (SNR/THD+N) olmadan kalite iddiası
yok. Taşıma kararı onaylanana kadar bu klasör yalnız bir **işaretçi/doküman
yüzeyidir** — kod buraya geri taşınmaz ve silinmez.

## 1 — Mimari Şema

```text
app-cpp-projects/        : (BOŞ — 0 dosya, 2026-10-23 disk kanıtı)
--/neva-engine/          : GERÇEK KOD KONUMU (commit 9fd9991)
  CMakeLists.txt         : C++20 derleme + ctest
  (15 kaynak dosya)      : motor çekirdeği + WASAPI backend + testler
ai/PLAN.md:330           : taşıma TODO'su (açık)
architecture doc §19     : "kullanıcı onayı ⏳" (açık karar)
```

Katman bağımlılığı: `uygulama → motor API → DSP çekirdek → platform backend`
tek yön; `platform backend → DSP çekirdek` ters bağımlılık yasak.

## 2 — Doğrulama

| Kapı | Komut | Beklenen |
|---|---|---|
| Configure | `cmake -S . -B build` (neva-engine) | 0 hata |
| Build | `cmake --build build` | 0 hata (C++20, `/W4`-düzey ⚠️ VERIFICATION REQUIRED) |
| Test | `ctest --test-dir build` | 0 fail |
| Karar | `.ai/PLAN.md:330` TODO durumu | kapalı olmalı (açık → üretim yasak) |

## 3 — İlgili Dosyalar

| Dosya | Ne verir | Otorite |
|---|---|---|
| `--/neva-engine/` | gerçek C++ kodu (CMake, 15 dosya) | Doğrudan (disk) |
| `.ai/PLAN.md` (satır 330) | taşıma kararının açık TODO'su | Doğrudan |
| `.ai/projects/NevaEngine/coremusic-neva-engine-prd.md` | ürün PRD'si (8004 satır, 182 gereksinim, K-6 onaylı) | SSOT |
| `[[.ai/.decisions/index]]` | ADR-017 (DSP donanım modu, frozen), ADR-0105 (native media stack) | SSOT |
| `@PRD.md` (§5.1) | Neva ürün PRD indeksi | SSOT |

## 4 — Kısıtlar / Yasaklar

- `git commit` ATILMAZ (orkestratöre aittir).
- **Silme yasak:** boş klasör dahil hiçbir mevcut dosya/dizin silinmez.
- Kod yüzeyine (`*.php/js/css/sql`) yazım yasak; **C++ kaynak kodu da bu
  görevde yazılmaz** (yalnız doküman).
- RT callback'te lock/alloc yasak; buffer'lar baştan allocate.
- Ölçümsüz SNR/THD+N iddiası yazılmaz (`⚠️ VERIFICATION REQUIRED`).

## 5 — Tarihçe / Kararlar

| Tarih | Karar | Kaynak |
|---|---|---|
| 2026-10-19 | ADR-0105 native media stack: draft → accepted | `[[.ai/.decisions/index]]` |
| — | C++ kodu `app-cpp-projects/` → `--/neva-engine/` taşındı (commit 9fd9991) | disk + git |
| 2026-10-23 | Taşımanın kalıcılığı: **KULLANICI ONAYI BEKLİYOR** (TODO açık) | `.ai/PLAN.md:330` · mimari doc §19 ⏳ |
| 2026-10-23 | Bu belge üretildi (prompt-maker çıktısı) | [[PRD]] |

## 6 — Bakım / Sorumluluk

- **Sahip:** Embedded Engineer (Expert #4) · Kısmi: performance-engineer (RT ölçüm)
- **Yenileme:** Taşınma kararı kapanınca (kod yeri değişirse) → bu belge revize.
- **Kanıt:** `--/neva-engine/` disk keşfi (2026-10-23) + `.ai/PLAN.md` + Neva Engine PRD.

## 7 — İletişim

`admin@coremusic.net` · yayın yok (dahili yüzey).
