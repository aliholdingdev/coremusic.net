---
title: "K001 Cihaz Envanteri Raporu — devices (birim ↔ sınıf ayrımı)"
type: report
node_id: reports/k001-taksonomi/k001-cihaz-envanteri-raporu
status: active
evidence:
  - ".ai/architecture/K001-donanım/donanım-devices/index.md (94 satır, 2026-10-13)"
  - ".ai/architecture/K001-donanım/donanım-devices/donanım-devices-kapsam.md (26 alanlık şema)"
  - ".ai/.decisions/accepted/ADR-064-electronics-platform-architecture.md"
  - "shared/src/Device/DeviceManager.php · DeviceDetector.php · DeviceRenderer.php · DeviceCssMap.php"
validation: "donanım-devices/ diskte mevcut (3 dosya) · K001/index.md children'da YOK"
open_questions:
  - "Q-DEV1: donanım-devices/ K001/index.md children listesinde eksik (stale — edit gerekir)"
  - "Q-DEV2: anayasa §1 'çocuk sayısı 4' — devices 5. çocuk (PROPOSED — APPROVAL REQUIRED)"
  - "Q-DEV3: 26 alanlık şemanın vault geneli SSOT olması PROPOSED"
group: rapor
date: 2026-10-13
updated: 2026-10-13
---

# K001 Cihaz Envanteri Raporu (devices)

## §1 Kavram Ayrımı (ADR-063)

| Dal | Kapsam | Soru |
|---|---|---|
| `donanaım-type` | **Sınıf** (audio · hdd · ssd · display · ...) | *Neyin* kategorisi? |
| `donanım-devices` | **Birim** (masaüstü PC, dizüstü, telefon, SBC, otomotiv ünitesi, monitör, harici ses kartı) | *Hangi tekil cihazın* envanteri? |

## §2 2↔8↔9↔10 Eksen Omurgası

`donanım-devices-kapsam.md` §4'teki sınıflandırma omurgası:

| Eksen | Anlam |
|---|---|
| 2 | Tamamlanmış cihaz |
| 8 | OS'nin keşfettiği cihaz |
| 9 | Sürücünün eriştiği cihaz |
| 10 | Firmware'in yönettiği cihaz |

## §3 26 Alanlı Cihaz Şeması (devices dalı — PROPOSED SSOT)

`donanım-devices-kapsam.md` içinde türetilmiştir. **25 alanlık tür şemasından FARKLI** —
tür (sınıf) ≠ birim (cihaz). Tüm cihaz kayıt örüntüleri şu an **UNKNOWN/NOT PROVIDED**
(bu makinede gerçek cihaz envanteri yok).

> **Çelişki notu (Q-T1):** Rapor-2'deki 25 alan (tür) ile bu rapordaki 26 alan (birim)
> ayrı şemalardır. Fark 1 alan: cihaz şeması `fiziksel_envanter` + `envanter_kaynak`
> yerine cihaz-bazlı `seri_no`/`kurulum_yeri` gibi alanlar taşır — ama içeriği
> `donanım-devices-kapsam.md` §5'te tanımlıdır ve bu rapor onu ÖZETLER, yeniden yazmaz.

## §4 Çelişki Kayıtları (gizleme yok)

| # | Çelişki | Durum |
|---|---|---|
| D1 | `donanım-devices/` diskte var ama `K001/index.md` children listesinde YOK | stale — Vault Steward edit'i onayladı mı? |
| D2 | anayasa §1 · v2 §2.2 "çocuk sayısı 4" — devices 5. çocuk | PROPOSED — APPROVAL REQUIRED |
| D3 | `donanım-devices/index.md` §44: "donanaım-type = sınıf (audio · hdd · ssd · display · **power**)" — bu oturumdan sonra 9 bağlam oldu (cables/network/memory/cpu eklendi) → devices sınır listesi stale | devices dosyası revizyonu ayrı edit ister (ADR-102 §6/C4) |
| D4 | 26 alan şeması vault geneli SSOT mu, devices-dalına özel mi? | PROPOSED |

## §5 Kod Kanıtı (repo — gerçek, satır referanslı)

`donanım-devices/index.md` evidence satırı:

| Dosya | Rol |
|---|---|
| `shared/src/Device/DeviceManager.php` | Cihaz yönetimi |
| `shared/src/Device/DeviceDetector.php` | Cihaz tespiti |
| `shared/src/Device/DeviceRenderer.php` | Cihaz görselleştirme |
| `shared/src/Device/DeviceCssMap.php` | CSS eşleme |

**Kritik ayrım:** Bu PHP kodu **donanım cihazının kendisi değil**, web UI'daki
*cihaz gösterimi*dir (UI bileşeni). Gerçek donanım envanteri ≠ UI cihaz listesi.
`donanım-anayasa.md` §10/7: "K001 için gerçek repo karşılığı hangi modül? — ⚠️"

## §6 Sınır (devices ↔ type ↔ K002)

| Bu dal | Bu dal DEĞİL | Kaynak |
|---|---|---|
| Tekil cihaz envanteri | Sınıf/tür sınıflandırması | `donanaım-type/index.md` |
| Cihaz kaydı + durum | Kernel inventory | anayasa §2 |
| OS'nin keşfettiği cihaz (eksen 8) | Ring-0 sürücü (K000) | anayasa §2 |
| ADR-064'ün 5 cihaz sınıfının envanter boyutu | Electronics domain sahipliği | ADR-064 §2 |

**Authority:** Bayram Ali / Vault Steward
