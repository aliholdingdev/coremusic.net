---
node_id: K001-donanım/donanaım-type/mikrodenetleyici
node_type: bounded-context
status: draft
parent_id: "K001-donanım/donanaım-type"
children:
  - mikrodenetleyici-kapsam.md
  - mikrodenetleyici-dogrulama.md
  - mikrodenetleyici-ornekler.md
depends_on:
  - K000-isletim-sistemi
  - K005-firmware
evidence:
  - "v2 §2.2 mini-ağaç — donanaım-type ağaç kökü (yazım kaynağa sadık)"
  - ".ai/.templates/hardware/pic-template.md v1.0.0 (2026-09-29) — Guardrail #16 PIC şablonu"
  - ".ai/.templates/hardware/avr-template.md v1.0.0 (2026-09-29) — Guardrail #16 AVR şablonu"
  - ".ai/.templates/hardware/arduino-template.md v1.0.0 (2026-09-29) — Guardrail #16 Arduino şablonu"
validation: "draft — 2026-10-14 genişleme (Vault Steward): 11. bounded-context AÇILDI — PIC (Microchip XC8) · Atmel/ATmega (avr-gcc) · Arduino (PlatformIO) entegrasyonları için kapsam/kod/doğrulama 3'lüsü — 8 URL HEAD 200 · envanter kesişimi 0"
open_questions:
  - "Q-M1 — hangi PIC serisi hedef (PIC16F/18F/24F/dsPIC)? şablon 3 seriyi de kapsar → ⚠️ VERIFICATION REQUIRED"
  - "Q-M2 — ATmega hedefi ATmega328P mi (Arduino Uno çekirdeği)? onay bekliyor"
  - "Q-M3 — repo'da .c/.ino dosyası yok (0 glob) → tüm kod PLANNED"
group: mimari
---

# mikrodenetleyici — Kademe-3 Bounded-Context İndeksi

> **Kapsam:** Bu bağlam, **gömülü mikrodenetleyici entegrasyonlarını** kapsar: **PIC**
> (Microchip XC8/MPLAB X) · **Atmel AVR / ATmega** (avr-gcc/avr-libc) · **Arduino**
> (PlatformIO/Arduino çekirdeği). Amaç: üç platformda **aynı katmanlı mimarinin**
> (uygulama → sürücü/HAL → register/driver) kod örnekleri ve proje planı.

## Durum

| Alan | Değer |
|---|---|
| node_type | bounded-context |
| parent | `K001-donanım/donanaım-type` (11. çocuk) |
| children | 3 dosya (kapsam · dogrulama · ornekler) |
| status | draft |
| Repo gerçeği | `.c` · `.h` · `.ino` · `platformio.ini` **0** (glob) → tüm içerik **PLANNED** |
| Şablon bağımlılığı | Guardrail #16: `.templates/hardware/{pic,avr,arduino}-template.md` v1.0.0 |

## §GRUPLAR (Kademe-3)

| Grup | Dosya | Ne taşır |
|---|---|---|
| mimari | `mikrodenetleyici-kapsam.md` | platform matrisi (PIC/AVR/Arduino) · katmanlı mimari (uygulama→HAL→driver) · proje planı (7 faz) |
| kod | `mikrodenetleyici-ornekler.md` | [KALIP-PLANNED] kod blokları: ATmega blink+UART (avr-gcc) · PIC16F blink (XC8) · Arduino sketch + `platformio.ini` · HAL arayüz kalıbı |
| kanit | `mikrodenetleyici-dogrulama.md` | URL HEAD 200 matrisi · repo glob ölçümleri · kapılar K-M1…K-M5 · açık sorular |

## Komşu sınır matrisi

| Komşu | Sınır | Taşınmayan |
|---|---|---|
| `cpu` · `gpu` · `memory` | host donanımı (x86/ARM SoC) | mikrodenetleyici = ayrı sınıf (ROM/RAM KB mertebesi) |
| `power` | güç kaynağı/ağacı | MCU'nun güç **tüketicisi** olduğu yerde bu dal |
| `audio` · `audio/cipler` | DAC/ADC/codec çip künyeleri | MCU'nun bu çiplere **komut veren** tarafı bu dalda |
| `K005-firmware` | cihaz firmware'i (Neva Engine dahil) | bu dal yalnız MCU **geliştirme/entegrasyon** katmanı |
| `K000-isletim-sistemi/…/derleme-ve-paketleme-cross-platform-plani` | host OS derleme/paketleme | MCU `.hex` dağıtımı bu dalda (§4 kardeş plan) |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
