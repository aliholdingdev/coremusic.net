---
node_id: K002-electroncis/imalat-ve-test
node_type: subdomain
status: draft
parent_id: K002-electroncis
children: [imalat-ve-test-anayasa, imalat-ve-test-ornekler, imalat-ve-test-dogrulama]
depends_on: []
evidence:
  - ".ai/architecture/K002-electroncis/electroncis-kapsam.md §B.5 satır 98-109 + §H.5 satır 247-256 — disk okundu"
  - "Repo'da elektronik kodu YOK dürüstlük kaydı (K-node kök index)"
validation: "draft — 2026-10-14 yazıldı · 4 dosya · ≥500 ölçümü yazım sonrası"
open_questions:
  - "Q-IM1: EMC standart numaraları doğrulanmadı → ⚠️ VERIFICATION REQUIRED (kapsam §N-4)"
  - "Q-IM2: Ölçüm değerleri (SNR/THD) PLANNED — fiziksel ölçüm yapılmadı (kapsam §N-7)"
  - "Q-IM3: DFT/AOI kriterleri vault satır kanıtı ⚠️ (§H.5-6)"
group: mimari
---

# imalat-ve-test — Alt-Konu İndeksi (Kademe-3)

> **Yazım dokunulmaz:** `electroncis` (v2 §3 KURAL 1 — `electronics` DEĞİL, düzeltilmez).
> K002-electroncis'in üretim/ölçüm alt-konusu: PCB üretimi, DFT, muayene kriteri,
> kalibrasyon testi (SNR/THD+N), EMC ön hazırlık ve burn-in — yani tasarımın
> **fiziksel dünyada kanıtlandığı** yer.

## §0 ELI15

Bu alt-konu **tasarımın fabrikadaki kaderini** kapsar: PCB çizimlerinin üretime
gitmesi (Gerber/panel/reflow), devrenin test edilebilir yapılması (test noktaları,
ICT/FCT), gözle/makineyle muayene (AOI/X-ray), ölçüm masasında performans
kanıtı (SNR >100 dB, THD+N <0.01% @1 kHz hedefleri), EMC'nin sertifika öncesi
ön provası ve 8 saatlik burn-in. Basitçe: tasarım `devre/` ve `dijital/`'de
**çizilir**, burada **üretilir ve ölçülür** — ölçüm sonucu olmadan hiçbir cihaz
"geçti" sayılmaz.

## §1 Amaç

| # | Amaç | Çıktı dosya |
|---|---|---|
| 1 | Tasarım anayasası: üretim + DFT + muayene + ölçüm + EMC + burn-in | `imalat-ve-test-anayasa.md` |
| 2 | Örnekler: test planı iskeleti + ölçüm kayıt formatı + OS/dil kapsamı | `imalat-ve-test-ornekler.md` |
| 3 | Doğrulama: atıf + satır kanıtı + format + ölçüm tablosu | `imalat-ve-test-dogrulama.md` |

## §2 Kademe / rol

| # | Öğe | Değer |
|---|---|---|
| 1 | Kademe | 3 — subdomain (içerik dosyaları Kademe-4) |
| 2 | parent_id | `K002-electroncis` |
| 3 | node_type | `subdomain` (bu indeks) · `module` (3 içerik) |
| 4 | Durum | `draft` (10 eksik dosya turu — 2026-10-14) |
| 5 | Grup ağırlığı | `mimari` (anayasa) · `kod` (örnekler) · `kanit` (doğrulama) |
| 6 | Yazım | `electroncis` + `imalat-ve-test` — dokunulmaz |

## §3 Dosya envanteri (3)

| # | Dosya | node_id sonu | group | Hedef satır |
|---|---|---|---|---|
| 1 | `imalat-ve-test-anayasa.md` | `/imalat-ve-test-anayasa` | mimari | ≥500 |
| 2 | `imalat-ve-test-ornekler.md` | `/imalat-ve-test-ornekler` | kod | ≥500 |
| 3 | `imalat-ve-test-dogrulama.md` | `/imalat-ve-test-dogrulama` | kanit | ≥500 |

## §GRUPLAR

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `imalat-ve-test-anayasa.md` | Üretim + DFT + muayene + ölçüm + EMC + burn-in | 4 |
| kod | `imalat-ve-test-ornekler.md` | Test planı + ölçüm kayıt formatı + OS/dil | 4 |
| kanit | `imalat-ve-test-dogrulama.md` | Satır kanıtı + kesişim + format + ölçüm | 4 |

## §5 Komşu sınırı

| # | Komşu | Sınır konusu | Ayrım kuralı | Etiket |
|---|---|---|---|---|
| 1 | `devre-ve-analog` | Decoupling değeri | Değer devrede, yerleşim üretimde | taslak |
| 2 | `dijital-ve-mikroislemci` | I2S trace 8mil | Arayüz dijitalde, kural üretimde | taslak |
| 3 | `guc-yonetimi` | Güç testi (verim/ripple) | Eşik güçte, üretim testi burada | taslak |
| 4 | `sensor-ve-akteyici` | Test noktası ihtiyacı | Yerleşim burada, ihtiyacı sensör bildirir | taslak |
| 5 | Dış laboratuvar | EMC sertifikasyonu | Ön hazırlık burada, sertifika dışarıda | taslak |
| 6 | QA/K005 | Test otomasyon kodu | Plan burada, kod orada | taslak |

## §6 depends_on

| # | Bağımlılık | Tür | Not |
|---|---|---|---|
| 1 | K001-donanım | sınır (bileşen envanteri) | Q-K2-1 kök açık |
| 2 | `.ai/electronic/development-workflow.md:416-434` | kanıt (Faz 15 test) | §B.5 |
| 3 | ADR-017 · ADR-038 · L0 | kısıt (ölçüm hedefleri) | frozen |

## §7 Açık sorular

| # | Soru | Etiket | Sahip |
|---|---|---|---|
| Q-IM1 | EMC standart numaraları doğrulanmadı mı | ⚠️ VERIFICATION REQUIRED | Vault Steward |
| Q-IM2 | SNR/THD fiziksel ölçümü yapıldı mı (PLANNED) | ⚠️ VERIFICATION REQUIRED | test istasyonu |
| Q-IM3 | DFT/AOI kriterleri vault satır kanıtı | ⚠️ VERIFICATION REQUIRED | 3 ajan |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
