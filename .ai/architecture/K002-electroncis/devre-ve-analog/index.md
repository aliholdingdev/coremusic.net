---
node_id: K002-electroncis/devre-ve-analog
node_type: subdomain
status: draft
parent_id: K002-electroncis
children: [devre-ve-analog-anayasa, devre-ve-analog-ornekler, devre-ve-analog-dogrulama]
depends_on: []
evidence:
  - ".ai/electronic/amplifier/class-ab.md satır 21-22, 38-60 (100W @ 8Ω, THD+N <0.01%, sinyal zinciri)"
  - ".ai/electronic/amplifier/class-d.md satır 42-47 (verim >90%, damping >200)"
  - ".ai/electronic/frequency-response.md satır 50-56 (20Hz-20kHz ±0.5dB)"
validation: "draft — 2026-10-13 yazıldı · 4 dosya · ≥500 ölçümü yazım sonrası"
open_questions:
  - "Q-DVA1: analog filtre tasarım sınırı K004-dsp ile netleşti mi? → ⚠️ VERIFICATION REQUIRED"
  - "Q-DVA2: DC offset algılama devresi (0.5V röle tetiği) hangi alt-konunun birinciliği? → UNKNOWN"
group: mimari
---

# devre-ve-analog — Alt-Konu İndeksi (Kademe-3)

> **Yazım dokunulmaz:** `electroncis` (v2 §3 KURAL 1 — `electronics` DEĞİL, düzeltilmez).
> K002-electroncis'in ilk alt-konusu: pasif/aktif bileşenler, op-amp, filtre,
> osilatör ve DAC/ADC ön zinciri — yani ses sinyalinin fiziksel hali.

## §0 ELI15

Bu alt-konu, sesin **elektrik akımı olarak yaşadığı her şeyi** kapsar: bileşenlerin
nasıl seçildiğini, sinyalin amplifikatörden nasıl geçtiğini, filtrenin nasıl
tasarlandığını ve ölçüm masasında hangi eşiğin tutması gerektiğini. Basitçe:
dijital dünya burada biter (`dijital-ve-mikroislemci` başlar), güç dünyası
başkadır (`guc-yonetimi`); burada **sinyalin kendisi** yaşar.

## §1 Amaç

| # | Amaç | Çıktı dosya |
|---|---|---|
| 1 | Tasarım anayasası: bileşen + topoloji + eşik + ölçüm kapısı | `devre-ve-analog-anayasa.md` |
| 2 | Örnekler: vault ham → işlenmiş dönüşüm + kural eşlemesi | `devre-ve-analog-ornekler.md` |
| 3 | Doğrulama: satır kanıtları + web URL (HTTP 200) + format kontrolü | `devre-ve-analog-dogrulama.md` |

## §2 Kademe / rol

| # | Öğe | Değer |
|---|---|---|
| 1 | Kademe | 3 — subdomain (içerik dosyaları Kademe-4) |
| 2 | parent_id | `K002-electroncis` |
| 3 | node_type | `subdomain` (bu indeks) · `module` (3 içerik) |
| 4 | Durum | `draft` (A1 tur-1) |
| 5 | Grup ağırlığı | `mimari` (anayasa) · `kod` (örnekler) · `kanit` (doğrulama) |
| 6 | Yazım | `electroncis` + `devre-ve-analog` — dokunulmaz |

## §3 Dosya envanteri (3)

| # | Dosya | node_id sonu | group | Hedef satır |
|---|---|---|---|---|
| 1 | `devre-ve-analog-anayasa.md` | `/devre-ve-analog-anayasa` | mimari | ≥500 |
| 2 | `devre-ve-analog-ornekler.md` | `/devre-ve-analog-ornekler` | kod | ≥500 |
| 3 | `devre-ve-analog-dogrulama.md` | `/devre-ve-analog-dogrulama` | kanit | ≥500 |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — 9. alan `group` (tirnaksız).

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `devre-ve-analog-anayasa.md` | Bileşen + topoloji + eşik + ölçüm kapısı | 4 |
| kod | `devre-ve-analog-ornekler.md` | Vault dönüşüm örnekleri + kural eşlemesi | 4 |
| kanit | `devre-ve-analog-dogrulama.md` | Satır kanıtı + URL HTTP 200 + format denetimi | 4 |

## §5 Komşu sınırı

| # | Komşu | Sınır konusu | Ayrım kuralı | Etiket |
|---|---|---|---|---|
| 1 | `dijital-ve-mikroislemci` | Saat devresi / register / I2S | Analog tasarım sonu = dijital giriş | taslak |
| 2 | `guc-yonetimi` | Besleme topolojisi, koruma | Sinyal ≠ güç; sinyal yolu burada | taslak |
| 3 | `sensor-ve-akteyici` | Arayüz buffer/filtre | Sensörün ön zinciri burada, Dünya arayüzü orada | taslak |
| 4 | `imalat-ve-test` | Ölçüm kapısı (SNR/THD) | Tasarım eşiği burada, üretim testi orada | taslak |
| 5 | `K004-dsp` | Analog ≠ dijital filtre algoritması | Algoritma K004, devre K002 | ⚠️ VERIFY (Q-DVA1) |
| 6 | `K001-donanım` | Bileşen envanteri vs tasarımı | "VAR" K001, "NASIL" K002 | taslak |

## §6 depends_on

| # | Bağımlılık | Tür | Not |
|---|---|---|---|
| 1 | K001-donanım | sınır (parça envanteri) | Q-K2-1 kök açık |
| 2 | K004-dsp | sınır (filtre algoritması) | Q-DVA1 açık |
| 3 | `guc-yonetimi/` | kardeş (güç gereksinimi) | bu K-node içi |
| 4 | ADR-038 | kısıt (DAC = PCM3168A) | frozen — en güçlü |

## §7 Açık sorular

| # | Soru | Etiket | Sahip |
|---|---|---|---|
| Q-DVA1 | Analog filtre ↔ K004-dsp sınırı hangi belgeyle kapanır? | ⚠️ VERIFICATION REQUIRED | Vault Steward |
| Q-DVA2 | DC offset algılama devresi birincil mi, `guc-yonetimi` mi? | UNKNOWN | Vault Steward |
| Q-DVA3 | Filtre topoloji vault satırları okunmadı (Q-D1) | ⚠️ VERIFICATION REQUIRED | 3 ajan |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
