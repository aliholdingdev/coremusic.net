---
title: "testing — Kapsam"
type: architecture-scope
node_id: K004-dsp/testing
node_type: subdomain-scope
status: draft
parent_id: K004-dsp
tags: []
date: 2026-10-12
updated: 2026-10-12
group: mimari
---

# testing — Kademe-2 Kapsam Kaydı

## §1 Rol (ELI15)

`dsp-master.md` §3 satır 12 bu dalı **"DSP testleri ve bozulma kontrolü"** olarak
yazar. Basitçe: DSP bloklarının doğru çalıştığını kanıtlayan senaryolar — referans
sinyalle giriş ver, beklenen çıkışla karşılaştır, bozulma (distortion) eşiğini kontrol
et. `index.md` bunu "iddia değil, doğrulama" diye çağırır. testing, K004'ün
kabul-kriteri kapısının test senaryosu tarafıdır (ölçüm measurement'ın işi).

## §2 Kapsam sınırı (içinde / dışında / komşu)

| # | İçinde (bu dal) | Dışında | Sınır komşusu |
|---|---|---|---|
| 1 | DSP blok test senaryoları (referans sinyal → beklenen çıkış) | Frekans/THD ölçümünün kendisi | measurement |
| 2 | Bozulma (distortion) kontrol senaryosu | Ölçüm eşiklerinin kaynağı | measurement · dsp-master §4 T7 |
| 3 | Test kapsamı hedefi (coverage kriteri) | Ölçüm standardı (R128) | measurement |
| 4 | Regresyon/altın-örnek (golden sample) karşılaştırma | Ölçüm araç zinciri | measurement |
| — | *(dışında)* test kapsamı hedefi tanımlı değil | — | **UNKNOWN — R1** |

**Yasak:** testing measurement ile sınır — testing senaryoyu/doğrulamayı, measurement
gerçek ölçümü üretir. Kapsam hedefi (R1) kanıtsız sayı yazılmaz.

## §3 Alt ayrım noktaları

| # | Ayrım | Durum | Kaynak |
|---|---|---|---|
| A1 | testing ↔ measurement (senaryo ↔ gerçek ölçüm) | ⚠️ VERIFICATION REQUIRED | dsp-master §7 Q4 |
| A2 | DSP test kapsamı hedefi (ör. %/eşik) | **UNKNOWN** — tahmin yasak | testing/index.md open_questions |
| A3 | bozulma kontrolü hangi metrikle (THD vs görse/eşik) | ⚠️ bağlı (Q4 + T7) | dsp-master §4 T7 |
| A4 | test altyapısı (framework/CI) | **UNKNOWN** | — |

## §4 Disk kanıtları

| # | İddia | Gerçek dosya yolu / URL | Durum |
|---|---|---|---|
| 1 | Çocuk rolü = "DSP testleri ve bozulma kontrolü" | `dsp-master.md` §3 satır 12 | ✅ dosya okundu |
| 2 | "test kapsamı hedefi tanımlı değil" açık soru | `testing/index.md` open_questions | ✅ dosya okundu |
| 3 | CoreMusic `shared/tests/` PHP testleri var ama DSP testi yok | `shared/AGENTS.md` §2 · `shared/tests/` (PHP, alakasız) | ✅ dosya okundu (negatif sınır) |
| 4 | measurement ↔ testing ayrımı Q4'te açık | `dsp-master.md` §7 Q4 | ✅ dosya okundu |
| 5 | DSPark `tests/` + `conformance/` dizinleri + CI (Windows/Linux/macOS/WASM/embedded) | https://github.com/CristianMoresi/DSPark | ✅ 2026-10-12 doğrulandı |
| 6 | Patina Catch2 v3 ile 55+ test dosyası | https://github.com/ShmKnd/Patina | ✅ 2026-10-12 doğrulandı |
| 7 | Signalsmith-Audio/dsp testleri ayrı depoda (output-quality ölçer) | https://signalsmith-audio.co.uk/code/dsp | ✅ 2026-10-12 doğrulandı |
| 8 | Bozulma kontrolü THD+N < 0.01% @ 1 kHz eşiği [KANIT] | `dsp-master.md` §4 T7 | ✅ dosya okundu |

## §5 Açık sorular

| # | Soru | İşaret |
|---|---|---|
| R1 | DSP test kapsamı hedefi (kapsanacak bloklar/eşik)? | **UNKNOWN** |
| R2 | testing ↔ measurement sınırı (Q4)? | ⚠️ VERIFICATION REQUIRED |
| R3 | Test altyapısı/framework seçimi? | **UNKNOWN** |

**Kapanış:** status: draft kalır. R1 kapsam-hedefi + R3 altyapı kapanmadan bu
dosyada test-iddiası bağlayıcı sayılmaz (v2 §3 KURAL 3 · zero-hallucination).

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
