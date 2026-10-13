---
title: "testing — Doğrulama"
type: architecture-verification
node_id: K004-dsp/testing
node_type: subdomain-scope
status: draft
parent_id: K004-dsp
tags: []
date: 2026-10-12
updated: 2026-10-12
group: kanit
---

# testing — Kanıt Tablosu

> **Kaynak notu:** (a) bu oturumda doğrulanan URL'ler · (b) bu oturumda okunan
> gerçek disk dosyaları. Yasak: URL, sürüm, sınıf uydurmak.

## §1 İddia tablosu

| # | İddia | URL / Dosya | Tarih | Durum |
|---|---|---|---|---|
| 1 | Çocuk rolü = "DSP testleri ve bozulma kontrolü" | `.ai/architecture/K004-dsp/dsp-master.md` §3 satır 12 | 2026-10-12 | ✅ dosya okundu |
| 2 | "test kapsamı hedefi tanımlı değil" açık soru | `testing/index.md` open_questions | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| 3 | `shared/tests/` PHP testleri var; DSP testi yok (negatif sınır) | `shared/AGENTS.md` §2 (9 dizin, PHP) | 2026-10-12 | ✅ dosya okundu |
| 4 | measurement ↔ testing ayrımı Q4'te açık | `dsp-master.md` §7 Q4 | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| 5 | Bozulma kontrolü THD+N < 0.01% @ 1 kHz eşiği [KANIT] | `dsp-master.md` §4 T7 · `dsp-bagimliliklar.md` §3 | 2026-10-12 | ✅ dosya okundu |
| 6 | DSPark `tests/` + `conformance/` dizinleri var | https://github.com/CristianMoresi/DSPark | 2026-10-12 | ✅ doğrulandı |
| 7 | DSPark v1.8.0 CI matrisi: Windows/Linux/macOS/WASM/embedded + sanitizers | https://github.com/CristianMoresi/DSPark | 2026-10-12 | ✅ doğrulandı |
| 8 | Patina Catch2 v3 ile 55+ test dosyası | https://github.com/ShmKnd/Patina | 2026-10-12 | ✅ doğrulandı |
| 9 | Signalsmith-Audio/dsp testleri ayrı depoda; output-quality ölçer | https://signalsmith-audio.co.uk/code/dsp | 2026-10-12 | ✅ doğrulandı |
| 10 | CoreMusic DSP test kapsamı hedefi | — | 2026-10-12 | **UNKNOWN** |
| 11 | DSP test altyapısı/framework seçimi | — | 2026-10-12 | **UNKNOWN** |

## §2 Sayım

| Metrik | Değer |
|---|---|
| Toplam satır | 11 |
| İşlenen benzersiz URL | 3 (6 · 7 DSPark · 8 Patina · 9 Signalsmith site) |
| Gerçek dosya yolu satırı | 5 (1 · 2 · 3 · 4 · 5) |
| ✅ doğrulandı | 8 (1 · 3 · 5 · 6 · 7 · 8 · 9 + 4'ün dosya-okuma kanıtı) |
| ⚠️ VERIFICATION REQUIRED | 2 (2 · 4) |
| UNKNOWN | 2 (10 · 11) |

**Geçiş kuralı:** satır 2/10 kapsam-hedefiyle, 11 altyapı-seçimiyle, 4 Q4 üst
kararıyla kapanır. Bozulma eşiği (satır 5) [KANIT]tır, bağlayıcıdır.

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
