---
node_id: K000-isletim-sistemi/electroncis/devre-tasarimi
node_type: subdomain
status: draft
parent_id: K000-isletim-sistemi/electroncis
children: [devre-tasarimi-anayasa, analog-ve-dijital-devre-temelleri, devre-simulasyonu-ve-dogrulama]
depends_on: []
evidence:
  - ".ai/architecture/K000-isletim-sistemi/electroncis/electroncis-kapsam.md §B.2 · §C.2"
  - ".ai/electronic/** (60 md · 10.323 sat) — devre içeriği orada derinleştiriliyor"
validation: "2026-10-13 · Kademe-3 alt-dal indeksi yazıldı · 3 içerik dosyası ≥500"
open_questions:
  - "Q-DT1: devre tasarimi siniri K001 (PCB girişi) ile netleşti mi? → ⚠️ VERIFICATION REQUIRED"
  - "Q-DT2: analog filtre tasarimi K004-dsp ile cakisiyor mu? → UNKNOWN"
group: mimari
---

# devre-tasarimi — Deep-Dal İndeksi (Kademe-4)

> **Yazım dokunulmaz:** `electroncis` (v2 §3 KURAL 1). Bu alt-dal, `electroncis` dalının
> **devre tasarımı** (şema → simülasyon → PCB girişi) yüzeyini kataloglar. Kapsam Q1 öncesi
> `draft`; tahmin üretmez, yalnız kanıt + sınır + açık soru taşır.

## §0 ELI15

Devre tasarımı = bir ses/güç cihazının **elektronik kalbini** çizme işi: doğru bileşenleri seçip
şemaya dökmek, bilgisayarda (SPICE) doğrulamak, sonra PCB ekibine teslim etmek. Bu alt-dal bu
sürecin **tasarım-anı** kısmını kapsar; PCB'nin kendisi K001-donanım, dijital filtre algoritması
K004-dsp, firmware K005'tir.

## §1 Amaç

| # | Amaç | Çıktı |
|---|---|---|
| 1 | Devre tasarım akışını tarif et (şema→sim→PCB→üretim) | `devre-tasarimi-anayasa.md` |
| 2 | Analog/dijital temel blokları belgele (bileşen·op-amp·ADC/DAC) | `analog-ve-dijital-devre-temelleri.md` |
| 3 | Simülasyon + ölçüm doğrulama planını taşı (SPICE·Monte Carlo·THD/SNR) | `devre-simulasyonu-ve-dogrulama.md` |

## §2 Kademe / rol

| # | Öğe | Değer |
|---|---|---|
| 1 | Kademe | 3 · subdomain (içerik dosyaları Kademe-4) |
| 2 | parent_id | `K000-isletim-sistemi/electroncis` |
| 3 | node_type | `subdomain` (indeks) · `module` (içerik) |
| 4 | Durum | `draft` (kapsam Q1 açık) |
| 5 | Grup ağırlığı | `mimari` |

## §3 3 dosya envanteri

| # | Dosya | node_id sonu | group | Hedef satır |
|---|---|---|---|---|
| 1 | `devre-tasarimi-anayasa.md` | `/devre-tasarimi-anayasa` | mimari | ≥500 |
| 2 | `analog-ve-dijital-devre-temelleri.md` | `/analog-ve-dijital-devre-temelleri` | mimari | ≥500 |
| 3 | `devre-simulasyonu-ve-dogrulama.md` | `/devre-simulasyonu-ve-dogrulama` | mimari | ≥500 |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — 9. alan `group`.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `devre-tasarimi-anayasa.md` | Tasarım akışı + temel bloklar + analog/dijital sınır | Kademe-4 |
| mimari | `analog-ve-dijital-devre-temelleri.md` | Bileşen · op-amp · ADC/DAC · gürültü · ölçüm | Kademe-4 |
| mimari | `devre-simulasyonu-ve-dogrulama.md` | SPICE analizleri · Monte Carlo · ölçüm planı | Kademe-4 |

## §5 Komşu sınırı

| # | Komşu | Sınır konusu | Ayrım kuralı | Etiket |
|---|---|---|---|---|
| 1 | K001-donanım | PCB · kart · mekanik · ısıl | şema sonu = PCB girişi | ⚠️ VERIFY |
| 2 | K004-dsp | dijital filtre · EQ | analog tasarım sonu = DSP girişi | taslak |
| 3 | K005-firmware | boot · RTOS · koruma döngüsü | devre eşiği = firmware gözetimi | taslak |
| 4 | `guc-ve-koruma/` | besleme + koruma devresi | sinyal ≠ güç | bu K-node içi |
| 5 | `isletim-sistemi-kernel` | sürücü · modül | devre ≠ sürücü | taslak |

## §6 `depends_on`

| # | Bağımlılık | Tür | Not |
|---|---|---|---|
| 1 | K001-donanım | sınır (PCB girişi) | ⚠️ sınır boşluğu (Q1) |
| 2 | K004-dsp | sınır (dijital filtre) | taslak |
| 3 | K005-firmware | sınır (koruma döngüsü) | taslak |
| 4 | `guc-ve-koruma/` | güç gereksinimi | bu K-node içi kardeş |

## §7 Açık sorular

| # | Soru | Etiket | Sahip |
|---|---|---|---|
| Q1 | devre tasarımı K001 PCB girişiyle netleşti mi? | ⚠️ VERIFICATION REQUIRED | Vault Steward |
| Q2 | analog filtre ↔ K004-dsp sınırı hangi belgeyle kapanır? | UNKNOWN | Vault Steward |
| Q3 | `node_type: module` Kademe-4 için geçerli mi? | ⚠️ VERIFICATION REQUIRED | v2 §4.2 |
| Q4 | repo'da devre seviyesi kod yok → şartname mi doğar? | UNKNOWN | PLANNED |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
