---
title: "spatial-audio — Doğrulama"
type: architecture-verification
node_id: K004-dsp/spatial-audio
node_type: subdomain-scope
status: draft
parent_id: K004-dsp
tags: []
date: 2026-10-12
updated: 2026-10-12
group: kanit
---

# spatial-audio — Kanıt Tablosu

> **Kaynak notu:** (a) bu oturumda doğrulanan URL'ler · (b) bu oturumda okunan
> gerçek disk dosyaları. Yasak: URL, sürüm, sınıf uydurmak.

## §1 İddia tablosu

| # | İddia | URL / Dosya | Tarih | Durum |
|---|---|---|---|---|
| 1 | Çocuk rolü = "Konum/hissiyat katmanı" | `.ai/architecture/K004-dsp/dsp-master.md` §3 satır 10 | 2026-10-12 | ✅ dosya okundu |
| 2 | "hedef format (binaural vb.) seçilmedi" açık soru | `spatial-audio/index.md` open_questions | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| 3 | CoreMusic `shared/src/` içinde spatial/HRTF kodu yok | `shared/src/` grep (negatif) | 2026-10-12 | ✅ doğrulandı |
| 4 | DSPark `AlgorithmicReverb` binaural early field içerir | https://github.com/CristianMoresi/DSPark | 2026-10-12 | ✅ doğrulandı (v1.8.0 notes) |
| 5 | DSPark time/pitch/spatial ailesi: `Delay`, `AlgorithmicReverb`, `PitchShifter`, `TimeStretch`, `PitchCorrector`, `GranularProcessor` | https://github.com/CristianMoresi/DSPark | 2026-10-12 | ✅ doğrulandı (Capabilities) |
| 6 | JUCE `modules/juce_dsp` modülü var; spatial içerik doğrulanmadı | https://github.com/juce-framework/JUCE/tree/master/modules | 2026-10-12 | ⚠️ — modül ✅, içerik **UNKNOWN** |
| 7 | CoreMusic hedef format (binaural / Ambisonics / stereo) seçimi | — | 2026-10-12 | **UNKNOWN** |
| 8 | HRTF veri seti + kafa-takibi kaynağı | — | 2026-10-12 | **UNKNOWN** |
| 9 | spatial ↔ reverb / delay sınır ölçütü | — | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |

## §2 Sayım

| Metrik | Değer |
|---|---|
| Toplam satır | 9 |
| İşlenen benzersiz URL | 2 (4 · 5 DSPark · 6 JUCE) |
| Gerçek dosya yolu satırı | 3 (1 · 2 · 3) |
| ✅ doğrulandı | 6 (1 · 3 · 4 · 5 + 6'nın modül kısmı) |
| ⚠️ VERIFICATION REQUIRED | 3 (2 · 6 · 9) |
| UNKNOWN | 2 (7 · 8) |

**Geçiş kuralı:** satır 2/7 format kararıyla, 8 veri-kaynağı kararıyla, 6
içerik-kanıtı ve 9 üst kararla (A1/A2) kapanır.

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
