---
node_id: K002-electroncis/sensor-ve-akteyici
node_type: subdomain
status: draft
parent_id: K002-electroncis
children: [sensor-ve-akteyici-anayasa, sensor-ve-akteyici-ornekler, sensor-ve-akteyici-dogrulama]
depends_on: []
evidence:
  - ".ai/architecture/K002-electroncis/electroncis-kapsam.md §B.4 satır 87-96 + §H.4 satır 236-245 — disk okundu"
  - "Repo'da elektronik kodu YOK dürüstlük kaydı (K-node kök index)"
validation: "draft — 2026-10-14 yazıldı · 4 dosya · ≥500 ölçümü yazım sonrası"
open_questions:
  - "Q-SN1: Sensör füzyonu K004-dsp'ye devredildi — sınır kesin mi? → §B.4(1)"
  - "Q-SN2: Kalibrasyon UI sahibi (K0xx) belirsiz → ⚠️ VERIFICATION REQUIRED"
  - "Q-SN3: Sensör vault kanıtı satır bazlı yok → ⚠️ VERIFICATION REQUIRED (kapsam §N-3)"
group: mimari
---

# sensor-ve-akteyici — Alt-Konu İndeksi (Kademe-3)

> **Yazım dokunulmaz:** `electroncis` (v2 §3 KURAL 1 — `electronics` DEĞİL, düzeltilmez).
> K002-electroncis'in sensör/aktüatör alt-konusu: ölçüm front-end'i, kestirim ön
> filtresi, kalibrasyon düzeltmesi, aktüatör sürücü devresi, kapalı çevrim donanım
> halkası ve hot-plug devre düzeyi — yani sistemin dünyayı ölçdüğü ve hareket
> ettirdiği yer.

## §0 ELI15

Bu alt-konu **iki ucu** kapsar: bir yanda elektriği/sinyali ölçen **sensör front-end'i**
(shunt/INA akım ölçümü, ADC arayüzü, kestirim ön filtresi, kalibrasyon düzeltme
tablosu), diğer yanda hareketi üreten **aktüatör sürücü devresi** (DRV sürücü entegresi,
kapalı çevrim donanım halkası). Basitçe: burada **ölçü aleti ve motor sürücüsü**
yaşar; ölçülen verinin DSP'deki füzyonu `K004-dsp`'dedir, sürücünün mikro-kodu
`K005-firmware`'dedir, ölçüm devresinin güç topolojisi `guc-yonetimi`'dedir.
Hot-plug'un **devre düzeyi** burada, **OS mantığı** K000'dedir.

## §1 Amaç

| # | Amaç | Çıktı dosya |
|---|---|---|
| 1 | Tasarım anayasası: front-end + ADC + sürücü + kapalı çevrim + hot-plug + kalibrasyon | `sensor-ve-akteyici-anayasa.md` |
| 2 | Örnekler: shunt hesabı + ADC arayüz iskeleti + kural eşlemesi + OS/dil kapsamı | `sensor-ve-akteyici-ornekler.md` |
| 3 | Doğrulama: atıf + envanter kesişimi + format denetimi + ölçüm tablosu | `sensor-ve-akteyici-dogrulama.md` |

## §2 Kademe / rol

| # | Öğe | Değer |
|---|---|---|
| 1 | Kademe | 3 — subdomain (içerik dosyaları Kademe-4) |
| 2 | parent_id | `K002-electroncis` |
| 3 | node_type | `subdomain` (bu indeks) · `module` (3 içerik) |
| 4 | Durum | `draft` (10 eksik dosya turu — 2026-10-14) |
| 5 | Grup ağırlığı | `mimari` (anayasa) · `kod` (örnekler) · `kanit` (doğrulama) |
| 6 | Yazım | `electroncis` + `sensor-ve-akteyici` — dokunulmaz |

## §3 Dosya envanteri (3)

| # | Dosya | node_id sonu | group | Hedef satır |
|---|---|---|---|---|
| 1 | `sensor-ve-akteyici-anayasa.md` | `/sensor-ve-akteyici-anayasa` | mimari | ≥500 |
| 2 | `sensor-ve-akteyici-ornekler.md` | `/sensor-ve-akteyici-ornekler` | kod | ≥500 |
| 3 | `sensor-ve-akteyici-dogrulama.md` | `/sensor-ve-akteyici-dogrulama` | kanit | ≥500 |

## §GRUPLAR

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `sensor-ve-akteyici-anayasa.md` | Front-end + ADC + sürücü + kapalı çevrim + hot-plug + kalibrasyon | 4 |
| kod | `sensor-ve-akteyici-ornekler.md` | Shunt/ADC iskeletleri + kural eşlemesi + OS/dil kapsamı | 4 |
| kanit | `sensor-ve-akteyici-dogrulama.md` | Atıf + kesişim + format + ölçüm kanıtı | 4 |

## §5 Komşu sınırı

| # | Komşu | Sınır konusu | Ayrım kuralı | Etiket |
|---|---|---|---|---|
| 1 | `devre-ve-analog` | Op-amp referans şeması | Referans şeması devrede, front-end burada | taslak |
| 2 | `guc-yonetimi` | Shunt/INA ölçüm kaynağı | Ölçüm devresi sensörde, güç/görüş orada | taslak |
| 3 | `dijital-ve-mikroislemci` | Hot-plug GPIO yapılandırması | Devre sensörde, register dijitalde | taslak |
| 4 | `imalat-ve-test` | Test noktaları talebi | Yerleşim üretimde, ihtiyacı burası bildirir | taslak |
| 5 | `K004-dsp` / `K005-firmware` | Füzyon + sürücü kodu | Algoritma/kod dışarıda, donanım burada | taslak |

## §6 depends_on

| # | Bağımlılık | Tür | Not |
|---|---|---|---|
| 1 | K001-donanım | sınır (bileşen envanteri) | Q-K2-1 kök açık |
| 2 | `guc-yonetimi/` | kardeş (ölçüm kaynağı) | shunt görüşü |
| 3 | ADR-017 · ADR-038 | kısıt | frozen |

## §7 Açık sorular

| # | Soru | Etiket | Sahip |
|---|---|---|---|
| Q-SN1 | Sensör füzyonu/kestirim DSP'si tamamen K004'te mi? | UNKNOWN | Vault Steward |
| Q-SN2 | Kalibrasyon UI sahibi (K0xx) hangi vault? | ⚠️ VERIFICATION REQUIRED | Vault Steward |
| Q-SN3 | Sensör vault kanıtı satır bazlı yok (kapsam §N-3) | ⚠️ VERIFICATION REQUIRED | 3 ajan |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
