---
node_id: K004-dsp
node_type: domain
status: draft
parent_id: "K000–K5000 Architecture Registry"
children: [pipeline, equalizer, filters, dynamics, compressor, limiter, reverb, delay, crossover, spatial-audio, measurement, testing]
depends_on: [K003-audio, K000-isletim-sistemi]
evidence: ["v2 §2.4 mini-ağaç — 12 çocuk adı (2026-10-12 okundu)", "dsp-master.md §4 — doğrulanmış web kanıtları (Patina, Signalsmith basics/dsp, DSPark ⚠️)", "dsp-dogrulama.md — iddia·URL·2026-10-12·durum satırları"]
validation: "draft — D-kriterleri dsp-dogrulama.md §4; çocuk kanıtları <çocuk>-dogrulama.md §1"
open_questions: ["Q1 DSPark v1 '90+' ↔ GitHub 2★ çelişkisi ⚠️", "Q2 Signalsmith basics MIT lisans teyidi ⚠️", "Q3 dynamics ↔ compressor/limiter hiyerarşisi mi kardeş mi ⚠️", "Q4 measurement ↔ testing sorumluluk ayrımı ⚠️", "Q5 DSP hedefi host mu (K003 üstü) donanım içinde mi (K005) ⚠️"]
group: mimari
---

# K004-dsp — DSP Alanı

## §1 ELI15

**Sesin şekillendirildiği yer — bass, tiz, ekolayzır, oda hissi.** Bir şarkının "daha sıcak" ya da "daha net" duyulduğu hali burada üretilir: önce tepki ölçülür, bantlar ayrılır, seviyeler tırpılanır, gerçek odayı taklit eden yankı eklenir. **K003 sesi taşır, K004 taşıdığı suyun tadını değiştirir** — hoparlörün kendisi değil, stüdyo miksaj masası.

**Ne DEĞİL:** ham sesin taşınması ve cihaza bağlanması (→ `K003-audio`) · amplifikatör/DAC devresinin tasarımı (→ `K001-donanım`) · cihaz içindeki kontrol yazılımı (→ `K005-firmware`) · kendi başına ayrı bir thread/clock iddiası.

## §1.5 İlişkili Proje Kaydı (2026-10-14)

**Neva Engine DSP zinciri planı** — `.ai/projects/NevaEngine/02-dsp-zincir-mimari.md`: 8 aşamalı sinyal zinciri (gain→31-band EQ→compressor→4 reverb→LR4 crossover→true-peak limiter→maximizer→8.1 matrix) + libebur128 LUFS + juce::dsp::FFT RTA. SDK/lib envanteri `.ai/projects/NevaEngine/00-sdk-lib-envanter.md` (Airwindows MIT referans, JUCE 9 dsp, kissfft). K004 çocuklarıyla eşleme: `equalizer/` → 31-band TPT biquad · `compressor/` · `limiter/` (01-gelistirme-plani §3) · `reverb/` (4 mod) · `crossover/` (LR4 80Hz) · `measurement/` (LUFS/TP).

## §2 Kademe-2 çocuk katalogu (v2 §2.4 — 12 çocuk, birebir)

| # | Çocuk | Rol (1 satır, addan türetildi) | Kanıt |
|---|---|---|---|
| 1 | pipeline | İşlem basamaklarının sırası ve akış grafiği | ⚠️ ad-türetme |
| 2 | equalizer | Frekans bantlarına göre seviye ayarı (bass/tiz) | ⚠️ ad-türetme |
| 3 | filters | Alçak/yüksek/geçiren bant filtreleri | ⚠️ ad-türetme |
| 4 | dynamics | Seviye davranışının tümü (sıkıştırma ailesi şemsiyesi) | ⚠️ ad-türetme |
| 5 | compressor | Eşik üstü seviyeyi tırpanlama | ⚠️ ad-türetme |
| 6 | limiter | Aşırı seviyeyi kesen güvenlik katmanı | ⚠️ ad-türetme |
| 7 | reverb | Yansıma/yankı taklidi — oda hissi | ⚠️ ad-türetme |
| 8 | delay | Belirli gecikmeli kopya ekleme | ⚠️ ad-türetme |
| 9 | crossover | Sesi frekansa göre hoparlör türlerine ayırma | ⚠️ ad-türetme |
| 10 | spatial-audio | Konum/hissiyat — saran ses katmanı | ⚠️ ad-türetme |
| 11 | measurement | Frekans/zaman tepkisi ölçümü ve raporu | ⚠️ ad-türetme |
| 12 | testing | DSP testleri ve bozulma (distortion) kontrolü | ⚠️ ad-türetme |

> ⚠️ VERIFICATION REQUIRED: v2 §2.4 çocukları yalnızca **ad** olarak tanımlar; roller adından türetilmiştir (v2 §3 KURAL 3) → uydurma parametre/API/ölçü yazılmaz. Yazım hatası/typo'lar v2'deki yazılışıyla korunur (KURAL 1).

## §3 Bağımlılık özeti (tasarım iddiası — `depends_on` alanı)

| Yön | Bağımlılık | Etiket |
|---|---|---|
| K004 → K003-audio | DSP, motorun akışına **graf düğümü olarak** eklenir; bağımsız ses kaynağı yoktur | [TASARIM] |
| K004 → K000-isletim-sistemi | CPU, thread, bellek ve zaman kaynağı | [TASARIM] |
| K004 → K001-donanım | Nihai çıkış amplifikatöre/hoparlöre iner (fiziksel sınır) | [TASARIM] |
| K004 → K005-firmware | Bazı DSP hedefleri donanım içinde çalışabilir | [TASARIM] ⚠️ Q5 |

**Sınır kuralı:** ters yön (K003'ün K004'ü bilmesi) katman ihlalidir — K003
yalnızca soyut bir *işlem noktası* tanır (A1 dizisi: K000 → K001 → K003 → K004).

## §4 Format şeması

KöK (12 dosya): `index.md` · `dsp-master.md` · `dsp-kapsam.md` · `dsp-bagimliliklar.md` · `dsp-dogrulama.md` · `dsp-web-arastirma.md` · `dsp-bilesenler.md` · `dsp-kod-ornekleri.md` · `dsp-kararlar.md` · `dsp-riskler.md` · `dsp-test-stratejisi.md` · `dsp-evrensel-dil.md`.
Çocuk (6 dosya × 12): `index.md` · `-kapsam.md` · `-dogrulama.md` · `-web-arastirma.md` · `-ornekler.md` · `-bilesenler.md`. Tier-3 (7 dal × 8 dosya): `equalizer/eq` · `pipeline/resampler` · `measurement/fft-analiz` · `measurement/loudness` · `reverb/reverb` · `compressor/compressor` · `limiter/limiter`.

## §5 Doğrulama durumu

| Kriter | Durum |
|---|---|
| D1 v2 §2.4 birebirliği (12 çocuk) | ✅ 2026-10-12 |
| D2 12 çocuk klasöründe index.md (8 alan) | ✅ 2026-10-12 |
| D3 master + bagimliliklar + dogrulama üçlüsü | ✅ 2026-10-12 |
| D4 açık ⚠️ / UNKNOWN satırı kalmadı | ❌ Q1–Q5 + doğrulanamayan ölçümler |
| D5 açık sorular kapandı | ❌ 5 açık |
| D6 en az bir [TASARIM] → [KANIT] geçişi | ❌ 0 |
| D7 status: verified | ❌ D1–D7 tamamı ✅ olunca |

**Status: draft** — `verified` yalnız D1–D7 tamamı ✅ olduğunda yazılır (kısmi yok).

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-1 |
| mimari | `dsp-master.md` | Master künye ve sorumluluk haritası | Kademe-1 |
| mimari | `dsp-kapsam.md` | K004 kapsamı ve tier-3 listesi | Kademe-1 |
| mimari | `dsp-bagimliliklar.md` | Katman bağı ve used_by haritası | Kademe-1 |
| mimari | `dsp-bilesenler.md` | Bileşen ağacı (12 çocuk + 7 tier-3) | Kademe-1 |
| mimari | `dsp-kararlar.md` | K004 iç mimari kararları | Kademe-1 |
| mimari | `dsp-evrensel-dil.md` | TR/EN terim sözlüğü | Kademe-1 |
| kod | `dsp-kod-ornekleri.md` | Repo DSP kanıtı + şartname kodu | Kademe-1 |
| kod | `dsp-test-stratejisi.md` | Test piramidi ve eşikler | Kademe-1 |
| kanit | `dsp-dogrulama.md` | D-kriterleri doğrulama matrisi | Kademe-1 |
| kanit | `dsp-web-arastirma.md` | İddia·URL·tarih·durum tablosu | Kademe-1 |
| kanit | `dsp-riskler.md` | Risk ve azaltma tablosu | Kademe-1 |

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
