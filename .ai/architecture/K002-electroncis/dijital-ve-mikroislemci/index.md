---
node_id: K002-electroncis/dijital-ve-mikroislemci
node_type: subdomain
status: draft
parent_id: K002-electroncis
children: [dijital-ve-mikroislemci-anayasa, dijital-ve-mikroislemci-ornekler, dijital-ve-mikroislemci-dogrulama]
depends_on:
  - K001-donanım
  - K004-dsp
  - K005-firmware
evidence:
  - ".ai/electronic/dsp/dsp-engine-architecture.md — dijital motor mimarisi (satır atıfı içerik dosyalarında)"
  - ".ai/electronic/hardware/** — saat/arayüz donanım notları"
  - "URL doğrulama: 15 benzersiz URL · HEAD HTTP 200 · envanter kesişimi 0"
validation: "draft — 2026-10-13 yazıldı · 4 dosya · ≥500 ölçümü yazım sonrası"
open_questions:
  - "Q-DVM1: Register map'in sahibi K005-firmware mi, bu alt-konu mu? → ⚠️ VERIFICATION REQUIRED"
  - "Q-DVM2: 512/48 kHz tampon zamanlama bütçesi hangi belgede kapanır? → UNKNOWN"
group: mimari
---

# dijital-ve-mikroislemci — Alt-Konu İndeksi (Kademe-3)

> **Yazım dokunulmaz:** `electroncis` (v2 §3 KURAL 1 — `electronics` DEĞİL).
> K002'nin ikinci alt-konusu: mikroislemci/DSP mimarisi, GPIO/PLL, saat ve
> kararlılık, bellek haritası, dijital arayüzler.

## §0 ELI15

Sesin dijital hali burada yaşar: mikroislemci ve DSP **sayılarla** çalışır,
ama sayıların **zamanlaması** analog kadar kritiktir. Saat titremesi (jitter)
doğrudan gürültüye, hatalı pin tanımı doğrudan çalışmadan başlatmaya yol açar.
Basitçe: analog burada biter (`devre-ve-analog`), güç ayrıdır
(`guc-yonetimi`); burada **mikroislemci + saat + dijital arayüz** sahiplenilir.

## §1 Amaç

| # | Amaç | Çıktı dosya |
|---|---|---|
| 1 | Tasarım anayasası: MCU/DSP mimarisi · GPIO/PLL · saat/kararlılık · bellek haritası · dijital arayüz (I2S/SPI/I2C) | `dijital-ve-mikroislemci-anayasa.md` |
| 2 | Örnekler: yapılandırma + kod yüzeyi + anti-pattern + eşik | `dijital-ve-mikroislemci-ornekler.md` |
| 3 | Doğrulama: senaryo/test/ölçüm + web URL (HTTP 200) + repo kanıtı | `dijital-ve-mikroislemci-dogrulama.md` |

## §2 Kademe / rol

| # | Öğe | Değer |
|---|---|---|
| 1 | Kademe | 3 — subdomain (içerik dosyaları Kademe-4) |
| 2 | parent_id | `K002-electroncis` |
| 3 | node_type | `subdomain` (bu indeks) · `module` (3 içerik) |
| 4 | Durum | `draft` (A1 tur-1) |
| 5 | Grup ağırlığı | `mimari` (anayasa) · `kod` (örnekler) · `kanit` (doğrulama) |
| 6 | Yazım | `electroncis` + `dijital-ve-mikroislemci` — dokunulmaz |

## §3 Dosya envanteri (3)

| # | Dosya | node_id sonu | group | Hedef satır |
|---|---|---|---|---|
| 1 | `dijital-ve-mikroislemci-anayasa.md` | `/dijital-ve-mikroislemci-anayasa` | mimari | ≥500 |
| 2 | `dijital-ve-mikroislemci-ornekler.md` | `/dijital-ve-mikroislemci-ornekler` | kod | ≥500 |
| 3 | `dijital-ve-mikroislemci-dogrulama.md` | `/dijital-ve-mikroislemci-dogrulama` | kanit | ≥500 |

## §GRUPLAR

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `dijital-ve-mikroislemci-anayasa.md` | MCU/DSP + saat + arayüz kuralları ve eşikleri | 4 |
| kod | `dijital-ve-mikroislemci-ornekler.md` | Yapılandırma/kod örnekleri + anti-pattern | 4 |
| kanit | `dijital-ve-mikroislemci-dogrulama.md` | Satır kanıtı + URL HTTP 200 + format denetimi | 4 |

## §5 Komşu sınırı

| # | Komşu | Sınır konusu | Ayrım kuralı | Etiket |
|---|---|---|---|---|
| 1 | `devre-ve-analog` | Saat devresi / analog ön zincir | analog tasarım sonu = dijital giriş | taslak |
| 2 | `guc-yonetimi` | Çekirdek/IO gerilim rails | besleme burada değil, gereksinim burada | taslak |
| 3 | `sensor-ve-akteyici` | Sensör arayüzünün MCU ucu | sensör tarafı kardeşte | taslak |
| 4 | `imalat-ve-test` | JTAG/test erişimi | DFT tasarımı burada, üretim testi kardeşte | taslak |
| 5 | `K004-dsp` | DSP zinciri / algoritma | mimari + kaynak burada, katsayı K004 | ⚠️ (Q-DVM2) |
| 6 | `K005-firmware` | Register map / kod | şartname burada, kod K005 | ⚠️ (Q-DVM1) |

## §6 depends_on

| # | Bağımlılık | Tür | Not |
|---|---|---|---|
| 1 | K001-donanım | sınır (part/envanter) | "VAR" K001 |
| 2 | K004-dsp | sınır (algoritma) | DSP zinciri |
| 3 | K005-firmware | sınır (kod yüzeyi) | register yazımı K005 |

## §7 Açık sorular

| # | Soru | Etiket | Sahip |
|---|---|---|---|
| Q-DVM1 | Register map sahipliği bu alt-konuda mı, K005'te mi? | ⚠️ VERIFICATION REQUIRED | Vault Steward |
| Q-DVM2 | 512/48 kHz zamanlama bütçesi hangi belgede kapanır? | UNKNOWN | üst karar |
| Q-DVM3 | JUCE 9 / ASIO SDK 2.3.4 sürüm kanıtı yok (L0 kural 12) | ⚠️ VERIFICATION REQUIRED | üst karar |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
