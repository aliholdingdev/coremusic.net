---
title: "reverb — Doğrulama"
type: architecture-verification
node_id: K004-dsp/reverb
node_type: subdomain-scope
status: draft
parent_id: K004-dsp
tags: []
date: 2026-10-12
updated: 2026-10-12
group: kanit
---

# reverb — Kanıt Tablosu

> **Kaynak notu:** (a) bu oturumda doğrulanan URL'ler · (b) bu oturumda okunan
> gerçek disk dosyaları. Yasak: URL, sürüm, sınıf uydurmak.

## §1 İddia tablosu

| # | İddia | URL / Dosya | Tarih | Durum |
|---|---|---|---|---|
| 1 | Çocuk rolü = "Yansıma/yankı taklidi — oda hissi" | `.ai/architecture/K004-dsp/dsp-master.md` §3 satır 7 | 2026-10-12 | ✅ dosya okundu |
| 2 | "oda modeli seçimi/ölçüm verisi yok" açık soru | `reverb/index.md` open_questions | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| 3 | PHP katmanında roomProfile reverb parametresi (`?? 0.5`) | `shared/src/AI/AIEngine.php` satır 114 | 2026-10-12 | ✅ dosya okundu |
| 4 | AIWorkflow'ta reverb istek parametresi (`$request['reverb'] ?? 0.5`) | `shared/src/AI/AIWorkflow.php` satır 159 | 2026-10-12 | ✅ dosya okundu |
| 5 | Bunlar tahmin/placeholder; gerçek reverb motoru C++ tarafında yok | `shared/src/AI/AIEngine.php` satır 317 (negatif) | 2026-10-12 | ✅ doğrulandı |
| 6 | DSPark `AlgorithmicReverb` = true-stereo 32-line FDN, binaural early field, freq-dependent decay | https://github.com/CristianMoresi/DSPark | 2026-10-12 | ✅ doğrulandı (v1.8.0 notes) |
| 7 | DSPark zaman-işlem ailesinde `Delay`, `Reverb`, `PitchShifter` yan yana | https://github.com/CristianMoresi/DSPark | 2026-10-12 | ✅ doğrulandı (Capabilities) |
| 8 | Patina `ReverbEngine` (Spring & plate; `type`, `decay`, `mix`) | https://github.com/ShmKnd/Patina | 2026-10-12 | ✅ doğrulandı |
| 9 | Signalsmith-Audio/basics `Reverb` başlığı | https://github.com/Signalsmith-Audio/basics | 2026-10-12 | ✅ doğrulandı |
| 10 | JUCE `juce_dsp` reverb içerir (sınıf adı doğrulanmadı) | https://github.com/juce-framework/JUCE/tree/master/modules | 2026-10-12 | ⚠️ — modül ✅, içerik **UNKNOWN** |
| 11 | CoreMusic oda modeli seçimi + IR ölçüm verisi | — | 2026-10-12 | **UNKNOWN** |
| 12 | reverb ↔ delay / spatial-audio sınır ölçütü | — | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |

## §2 Sayım

| Metrik | Değer |
|---|---|
| Toplam satır | 12 |
| İşlenen benzersiz URL | 3 (6 · 7 DSPark · 8 Patina · 9 Signalsmith · 10 JUCE) |
| Gerçek dosya yolu satırı | 5 (1 · 2 · 3 · 4 · 5) |
| ✅ doğrulandı | 8 (1 · 3 · 4 · 5 · 6 · 7 · 8 · 9) |
| ⚠️ VERIFICATION REQUIRED | 3 (2 · 10 · 12) |
| UNKNOWN | 1 (11) |

**Geçiş kuralı:** satır 2/11 oda-verisi kararıyla, 10 içerik-kanıtı, 12 üst kararla
kapanır.

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
