---
title: "K001-donanım/donanım-devices — Tamamlanmış Cihaz Envanteri Dizini"
type: architecture-index
node_id: K001-donanım/donanım-devices
node_type: architecture-index
status: active
parent_id: K001-donanım
children:
  - K001-donanım/donanım-devices/donanım-devices-kapsam
  - K001-donanım/donanım-devices/donanım-devices-dogrulama
  - K001-donanım/donanım-devices/donanım-devices-ornekler
depends_on:
  - K001-donanım
  - K000-isletim-sistemi
evidence:
  - .ai/architecture/K001-donanım/donanım-devices/donanım-devices-kapsam.md
  - .ai/architecture/K001-donanım/donanım-devices/donanım-devices-dogrulama.md
  - .ai/architecture/K001-donanım/donanım-devices/donanım-devices-ornekler.md
  - ".ai/.decisions/accepted/ADR-064-electronics-platform-architecture.md"
  - "shared/src/Device/DeviceManager.php"
  - "shared/src/Device/DeviceDetector.php"
  - "shared/src/Device/DeviceRenderer.php"
  - "shared/src/Device/DeviceCssMap.php"
  - ".ai/architecture/K001-donanım/donanım-anayasa.md"
validation: "4 dosya · içerik dosyalarının her biri en az 500 satır · Authority satırı tam olarak 1 · parent: anahtarı 0 · ölçüm 2026-10-13"
open_questions:
  - "anayasa §1 ve mimari ağaç v2 §2.2 'K001 çocuk sayısı 4' metni — 5. çocuk ile çelişir, metin revizyonu Vault Steward onayı bekliyor (PROPOSED)"
  - "Bu makinede gerçek cihaz envanteri yok — 26 alanlık kayıt örüntüleri tümü UNKNOWN/NOT PROVIDED"
date: 2026-10-13
updated: 2026-10-13
group: mimari
---

# K001-donanım/donanım-devices — Tamamlanmış Cihaz Envanteri (Dizin)

## Genel Bakış

Bu dizin, K001-donanım kademe-2 dalının **5. çocugu** olarak 2026-10-13'te
açılmıştır: **fiziksel olarak tamamlanmış cihaz envanteri** (tek tek birleşmiş,
çalışır cihazlar) — sınıf/tür envanteri olan `donanaım-type`'ın karşıtı.

Kapsam ayrımı (kapsam dosyası §2):

- `donanaım-type` = **sınıf** (audio · hdd · ssd · display · power) — *neyin* kategorisi.
- `donanım-devices` = **birim** (masaüstü PC, dizüstü, telefon/tablet, gömülü SBC,
  otomotiv multimedya ünitesi, monitör, harici ses kartı) — *hangi tekil cihazın* envanteri.

Sınıflandırma omurgası, görev promptu §7 eksenleridir: **2 ↔ 8 ↔ 9 ↔ 10**
(tamamlanmış cihaz ↔ OS keşfeden ↔ sürücü erişen ↔ firmware yönetilen).

> **Çelişki kaydı (gizleme yok):** Vault v2 §2.2 ve anayasa §1, K001 çocuk sayısını
> **4** olarak tanımlar (`donanım-kernel` · `donanım-core` · `donanım-api` · `donanaım-type`).
> Bu dizin 5. çocuktur. Klasör açılışı Vault Steward tarafından 2026-10-13'te
> onaylanmıştır; anayasa/index metin revizyonu henüz **APPROVAL REQUIRED**'dır.
> Kayıt: `donanım-devices-kapsam.md` §1.1 (`PROPOSED — APPROVAL REQUIRED` tablosu).

## Çocuk Dizinleri

Yok — bu dizin yaprak sınıfındadır (içinde alt dizin yoktur).

## Çocuk Dosyalar

| Dosya | group | Satır | Ne verir |
|---|---|---|---|
| `donanım-devices-kapsam.md` | mimari | ≥500 | Kavram ayrımı, 2↔8↔9↔10 eksen omurgası, 26 alanlık tür şeması, sınır tablosu, sahiplik kanıtları (ADR-064 + Device* kodu satır referanslı) |
| `donanım-devices-dogrulama.md` | kanit | ≥500 | Kanıt seviyeleri, iddia→kanıt matrisi, HEAD ile doğrulanmış URL listesi (2026-10-13), envanter kapıları (tümü UNKNOWN), kapı özeti |
| `donanım-devices-ornekler.md` | kod | ≥500 | Depodaki gerçek kod örnekleri (satır referanslı), 26 alanlık boş cihaz kayıt örüntüsü, eksen üzerinden sınıflandırma örnekleri |

## GRUPLAR

| group | Dosya(lar) | Görev |
|---|---|---|
| `mimari` | `index.md`, `donanım-devices-kapsam.md` | Dizin iskeleti + kavram/sınır/şema tanımı |
| `kanit` | `donanım-devices-dogrulama.md` | Doğrulanabilirlik, URL kanıtı, envanter kapıları |
| `kod` | `donanım-devices-ornekler.md` | Gerçek kod örnekleri ve kayıt örüntüleri |

## Sınır (özet — tam tablo kapsam §7)

| Bu dal | Bu dal DEĞİL | Kaynak |
|---|---|---|
| Tekil tamamlanmış cihaz envanteri | Sınıf/tür sınıflandırması (`donanaım-type`) | `donanaım-type/index.md` |
| Cihaz kaydı ve durum envanteri | Envanterin tek yazıcısı (Kernel inventory) | `donanım-anayasa.md` §2 |
| OS'nin keşfettiği cihaz (eksen 8) | Ring-0 sürücü/çekirdek kodu (K000) | `donanım-anayasa.md` §2 |
| ADR-064'teki 5 cihaz sınıfının envanter boyutu | Sınıfların Electronics domaini sahipliği | ADR-064 §2 |

## Onay Durumu

| Öğe | Durum |
|---|---|
| Klasör açılışı (4 dosya) | ✓ Vault Steward onayı — 2026-10-13 |
| anayasa §1 / v2 §2.2 metin revizyonu (çocuk 4 → 5) | **PROPOSED — APPROVAL REQUIRED** |
| 26 alanlık şemanın vault geneli SSOT olması | PROPOSED (bu dal içinde türetilmiştir) |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
