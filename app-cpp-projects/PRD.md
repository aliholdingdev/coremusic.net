---
title: "app-cpp-projects — Product Requirements Document"
type: prd
category: surface
version: 1.0.0
status: active
authority: "SSOT: .ai/PRD.md — Ürün PRD'si @.ai/projects/NevaEngine/coremusic-neva-engine-prd.md (K-6 onaylı)"
updated: 2026-10-23
---
# app-cpp-projects — PRD

> **Yüzey:** C++/sürekli-zaman ses motoru yüzeyi. **Gerçek kod konumu:**
> `--/neva-engine/` (commit 9fd9991); bu klasör 2026-10-23 itibarıyla **boş**
> ve taşıma kararı **açık** (`⚠️ VERIFICATION REQUIRED`). Şablon 15 başlık.

## 1. Amaç

Neva Engine'in (C++20, WASAPI) derlenebilir, test edilebilir ve ölçümü
yapılabilir kalmasını sağlamak; kod konumu kararını (boş yüzey ↔ taşınmış kod)
disk kanıtıyla sürekliliğini belgelemek.

## 2. Kapsam

- ✅ `--/neva-engine/` CMake derlemesi + ctest (15 kaynak dosya)
- ✅ WASAPI backend + OS audio soyutlama sözleşmesi
- ✅ Taşınma karar durumunun takibi (`.ai/PLAN.md:330` · mimari §19)
- ❌ `app-cpp-projects/`e kod yazımı/taşıma (onaysız) · ❌ `*.php/js/css/sql` ·
  ❌ PCB/fabrication dosyası · ❌ `git commit`

## 3. Kullanıcı Hikâyeleri

- **US-1** — *Embedded Engineer:* "Tek komutla motoru derleyip test edeyim"
  → CMake + ctest.
- **US-2** — *Ürün sahibi:* "Kod nerede, karar kapandı mı göreyim" → bu PRD §5.
- **US-3** — *Kalite:* "SNR/THD+N iddiası ölçümle gelsin" → ölçüm raporu kapısı.

## 4. Gereksinimler

| # | Gereksinim | Öncelik |
|---|---|---|
| G-1 | C++20 + CMake derleme hattı (sıfır hata) | P0 |
| G-2 | ctest ile otomatik test (0 fail) | P0 |
| G-3 | WASAPI-only backend + OS soyutlama katmanı | P0 |
| G-4 | RT callback lock-free; buffer baştan allocate | P0 |
| G-5 | Taşınma kararının kapatılması (PLAN.md:330) | P0 |
| G-6 | Ölçüm (SNR/THD+N/latency) raporu | P1 |

## 5. Kabuk Şema

```text
app-cpp-projects/  (BOŞ, 0 dosya — silinmez)
--/neva-engine/    (gerçek kod: CMakeLists.txt + 15 kaynak + testler)
open: .ai/PLAN.md:330 TODO · mimari doc §19 "kullanıcı onayı ⏳"
```

## 6. Öncelik Sırası (P0 → P3)

`P0` karar kapanışı + derleme/test · `P1` ölçüm raporu · `P2` backend genişleme
(ADR ile) · `P3` doküman cilası.

## 7. Kabul Kriterleri

1. `cmake --build` → **0 hata**.
2. `ctest` → **0 fail**.
3. `.ai/PLAN.md:330` TODO **kapalı** (onaylı karar) — açıkken yüzey "aktif tamam"
   sayılmaz.
4. Ölçümsüz kalite iddiası dosyada **0**.

## 8. Dış Bağımlılıklar

Windows SDK (WASAPI) · CMake · C++20 derleyici (MSVC ⚠️ VERIFICATION REQUIRED) ·
Neva Engine ürün PRD'si (K-6 onaylı 2026-10-23).

## 9. Riskler

- 🔴 Kod konumu belirsizliği (boş klasör ↔ taşınmış kod) → yanlış dizinde üretim.
- 🟡 Tek backend (WASAPI) → taşınabilirlik riski (ADR gerekir).
- 🟡 Ölçüm altyapısı yoksa kalite taahhüdü yazılamaz.

## 10. Başarı Ölçütleri

derleme/test 0 · karar açık değilken iddiasız raporlama · RT yasakları ihlali 0 ·
ölçüm raporlu SNR/THD+N.

## 11. Atılacak Adımlar (24–48h)

1. `.ai/PLAN.md:330` ve mimari §19 onayını kapat/karar ver (kullanıcı).
2. `cmake` + `ctest` kanıta çek.
3. Karar sonucunu bu PRD §5 + kök `PRD.md`'ye işle.

## 12. Referanslar

- `.ai/projects/NevaEngine/coremusic-neva-engine-prd.md` (8004 satır, 182 gereksinim) — SSOT
- `.ai/PLAN.md:330` · mimari doc §19 · `[[.ai/.decisions/index]]` (ADR-017, ADR-0105)
- `[[CONTEXT]]` · `[[AGENTS]]` · `[[CLAUDE]]` · `@PRD.md` §5.1

## 13. Sürüm Geçmişi

| Sürüm | Tarih | Değişiklik |
|---|---|---|
| 1.0.0 | 2026-10-23 | İlk sürüm (boş klasör + `--/neva-engine/` disk kanıtı) |

## 14. Ekipler / Sahiplik

Embedded Engineer (ana) · performance-engineer (ölçüm) · architect (ADR).

## 15. Onay

**Onaylamayan sorumlu:** Embedded Engineer · **Onay tarihi:** 2026-10-23 ·
**Karar mercii:** Ürün Sahibi — **açık karar maddesi:** kod konumu taşıması
(`⏳ bekliyor`).
