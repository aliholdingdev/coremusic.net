---
node_id: K000-isletim-sistemi/electroncis/os-guc-elektronigi
node_type: subdomain
status: draft
parent_id: K000-isletim-sistemi/electroncis
children: [os-guc-elektronigi/-anayasa.md, os-guc-elektronigi/-ornekler.md, os-guc-elektronigi/-dogrulama.md]
depends_on: [K002-electroncis, K001-donanım, K000-isletim-sistemi/os-donanim-tespiti, K000-isletim-sistemi/isletim-sistemi-kernel]
evidence:
  - "https://docs.kernel.org/power/index.html (200 OK · 2026-10-14)"
  - "https://docs.kernel.org/firmware-guide/acpi/index.html (200 OK · 2026-10-14)"
  - "https://docs.kernel.org/thermal/index.html (200 OK · 2026-10-14)"
  - "https://docs.kernel.org/hwmon/index.html (200 OK · 2026-10-14)"
validation: "2026-10-14 · yazıldı (Vault Steward Q1 kararı) · kök: electroncis/index.md children[1] · voltaj/eşik UYDURULMADI · red-team bekliyor"
open_questions:
  - "⚠️ VERIFICATION REQUIRED: PS_ON/PWR_OK mantık seviyesi düzlemi donanım dokümanından tırnaklandı; ölçülecek voltaj/eşik bu vault'ta YOK (uydurma yasak → K002 işi)."
  - "⚠️ VERIFICATION REQUIRED: cihaza özgü trip point sıcaklıkları (°C) envanterle gelecek — sayı yok, kural var."
group: mimari
---

# os-guc-elektronigi — alt düğüm index

> **`electroncis` yazım hatası dokunulmaz** (v2 §3 KURAL 1) — üst düğüm adı aynı imla.

## §0 ELI15

`os-guc-elektronigi`, işletim sisteminin **güç ve sıcaklık durumlarıyla** elektronik
seviyede yüzleştiği yüzeydir: ACPI uyku durumları (S0–S5) ile kart/donanım güç
durumlarının (D0–D3) el sıkışması, PS_ON/PWR_OK mantığı, uyandırma kaynakları
(wake-on-LAN/USB), sıcaklık zinciri (sensör → thermal zone → throttle → kritik
kapanış) ve pil/EC nesneleri. Testi basittir: **"OS bu cihazı açıyor/kapatıyor/uyandırıyor
mu ve sıcaklığı yönetiyor mu?"** Gerilim eşiği, devre, koruma elemanı tam elektronik
alanıdır → `K002-electroncis`.

## §1 Kapsam — İÇİNDE / DIŞI

| İÇİNDE (OS gözü) | DIŞI (K002 / K001) |
|---|---|
| ACPI S0–S5 ↔ D0–D3 yaşam döngüsü (OS/firmware el sıkışması) | Besleme devresi, regülatör, eşik voltajları |
| PS_ON / PWR_OK **mantık seviyesi** davranışı (seviye ≠ sayısal eşik) | Voltaj ölçümü, tolerans, koruma (OVP/OCP) |
| Wake kaynakları (wake-on-LAN/USB/klavye), GPE/SCI | Uyandırma devresi tasarımı |
| PME / runtime power management | Kart üstü güç sıralama RC'si |
| Termal zincir: sensör register → thermal zone → fan → throttle → kritik kapanış | Sensör yerleşimi, fan PWM devresi, histeresis analog tasarımı |
| Pil/şarj nesneleri (ACPI battery/EC okuması) | Pil kimyası/BMS devresi |

## §2 Dosyalar

| Dosya | Ne verir | Zemin |
|---|---|---|
| `-anayasa.md` | S0–S5/D0–D3 ↔ PS_ON/PWR_OK · wake · termal zincir · PME/runtime PM | mimari · ≥350 |
| `-ornekler.md` | S3 uyku-uyanış · güç el sıkışması · termal zincir · pil senaryoları (şartname) | kod · ≥250 |
| `-dogrulama.md` | güç/termal URL matrisi · sıcaklık eşiklerinin yokluğu kaydı · şema regex | kanit · ≥250 |

## §3 Sınır komşuları

| Komşu | Ne alır / ne verir |
|---|---|
| `electroncis/donanim-arayuz-protokolleri` | arayüz yaşam döngüsü — suspend'te bus/clock askı |
| `isletim-sistemi-kernel` | PM çekirdeği (suspend/resume sırası, process) |
| `os-donanim-tespiti` | güç/termal cihaz keşfi (EC/hwmon cihaz ID) |
| `K002-electroncis/guc-yonetimi` | tam güç elektronik — sınır (eşik/koruma/devre) |
| `K001-donanım` | fiziksel besleme/kart düzeyi |

## §4 Açık sorular

1. Voltaj eşiği/trip noktası sayıları girerse sahibi K002 mi? (öneri: EVET — bu dal
   yalnız OS **kuralı** taşır, sayı taşımaz)
2. `K002-altyapi` ID GAP'i (üst düğüm `os-master.md` satır 7) bu dalın `depends_on`
   süresince çözülürse kök güncellenir mi? (bu dal kendi ID'sine uydurma yazmaz)
3. EC (embedded controller) firmware tarafı hangi vault'ta? (şu an: `.ai/electronic/firmware/` atıf)

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
