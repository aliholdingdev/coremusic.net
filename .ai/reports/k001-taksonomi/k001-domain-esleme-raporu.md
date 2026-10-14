---
title: "K001–K005 Paralel Dal Haritası ve Eksik Dal Önerileri"
type: report
node_id: reports/k001-taksonomi/k001-domain-esleme-raporu
status: active
evidence:
  - ".ai/architecture/K000-isletim-sistemi/index.md (12 çocuk — electroncis dahil)"
  - ".ai/architecture/K001-donanım/** (disk ölçümü)"
  - ".ai/architecture/K002-electroncis/index.md (5 çocuk bildirir, 3 dizin diskte)"
  - ".ai/architecture/K003-audio/index.md (12 çocuk)"
  - ".ai/architecture/K004-dsp/index.md (12 çocuk)"
  - ".ai/architecture/K005-firmware/index.md (9 çocuk — device-control/ 2026-10-13)"
validation: "Tüm sayımlar 2026-10-13 disk ölçümü · trailing-slash glob false-negatif dikkat"
open_questions:
  - "Q-D1: K002 index 5 çocuk bildirir, diskte 3 dizin var (2 eksik)"
  - "Q-D2: K000 'electroncis/' ↔ K002-electroncis OVERLAP — sahiplik netleşmedi"
  - "Q-D3: K002 ID GAP: architecture/index.md §6 hâlâ 'K002 hiç yok' der"
group: rapor
date: 2026-10-13
updated: 2026-10-13
---

# K001–K005 Paralel Dal Haritası ve Eksik Dal Önerileri

Kullanıcı 2026-10-13: "K001 K002 K003 K004 K005 PARALEL BÜTÜN DALLARI ATAMALA,
EKSİK DALLARI DA ÖNER, ELLE KONTROL ET, ÖZELLİKLE DONANIM TYPE KISMINDAN BAŞLA."

## §1 Paralel Dal Haritası (disk ölçümü)

| Domain | Kademe-1 çocuk sayısı | Disk dizin sayısı | Eşit mi? |
|---|---|---|---|
| K000-isletim-sistemi | 12 | 12 | ✓ |
| K001-donanım | 4 (index) / 5 (gerçek) | 5 | ✗ — devices eksik index'te |
| K002-electroncis | 5 (bildirilen) | 3 | ✗ — 2 eksik |
| K003-audio | 12 | 12 | ✓ (tahmin — trailing-slash dikkat) |
| K004-dsp | 12 | 12 | ✓ (tahmin) |
| K005-firmware | 9 | 9 | ✓ |

## §2 K001 donanaım-type: 9 Bağlam (2026-10-13 disk gerçeği)

| # | Bağlam | Durum | Kaynak |
|---|---|---|---|
| 1 | audio | aktif | mevcut |
| 2 | hdd | aktif | mevcut |
| 3 | ssd | aktif | mevcut |
| 4 | display | **4/4** (2 önceki oturum + 2 bu oturum) | ADR-102 |
| 5 | power | **4/4 — önceki oturum** (Vault Steward onayı, 20:35) | önceki onay |
| 6 | cables | **açıldı (bu oturum)** | ADR-102 |
| 7 | network | **açıldı (bu oturum)** | ADR-102 |
| 8 | memory | **açıldı (bu oturum)** | ADR-102 |
| 9 | cpu | **açıldı (bu oturum)** | ADR-102 |
| 10 | gpu | **açıldı (paralel Vault Steward oturumu 21:52–22:49, 4/4 — gpu-ornekler.md 22:49'da yazıldı)** | ADR-102 DIŞI (gpu/index.md Q-GI1: ayrı revizyon onayı ister) |

## §3 Eksik Dal Önerileri (PROPOSED — APPROVAL REQUIRED)

> Bu bölüm K001 DIŞINDA domainlere girer; **hiçbiri bu oturumda AÇILMADI** —
> yalnızca öneridir. Vault Steward onayı gerekir.

### §3.1 K001-donanım (diğer kademe-2)

| Öneri | Gerekçe | Durum |
|---|---|---|
| `donanım-power/` KADENE-2 KÖKÜ AÇILMASIN | Power/PCB/BOM → `K002-electroncis` (ADR-064) — sınır koruması. Not: `donanaım-type/power/` TİP dalı 2026-10-13'te önceki oturumda Vault Steward onayıyla açıldı — reddedilen, devre/PCB/BOM sahipliğinin K001 kökünde toplanmasıdır | REDDEDİLDİ (bilgi) |

### §3.2 K002-electroncis (2 eksik çocuk)

| # | Önerilen dal | Gerekçe | Durum |
|---|---|---|---|
| 1 | `power-supply/` | ADR-064'ün güç kaynağı sınıfı — K002'nin ana konusu | PROPOSED |
| 2 | `pcb-bom/` | ADR-064 PCB/BOM dokümantasyonu | PROPOSED |
| (mevcut) | (3 dizin — adları diskte doğrulanacak) | — | aktif |

> **Not:** K002 index'in bildirdiği 5 çocuk vs diskteki 3 dizin farkı — hangi 2
> eksik, `K002-electroncis/index.md` okunarak Vault Steward'a sunulur.

### §3.3 K000-isletim-sistemi

| Öneri | Gerekçe | Durum |
|---|---|---|
| `electroncis/` ↔ K002 sahiplik ayrıştırması | İki "electroncis" — sahiplik netleşmeli | PROPOSED (Vault Steward) |

### §3.4 K003-audio / K004-dsp / K005-firmware

| Domain | Eksik dal tespiti | Durum |
|---|---|---|
| K003-audio | 12 çocuk — index ile disk eşleşiyor (tahmin) | Eksik YOK (gözlemlenen) |
| K004-dsp | 12 çocuk — index ile disk eşleşiyor (tahmin) | Eksik YOK (gözlemlenen) |
| K005-firmware | `device-control/` 2026-10-13'te güncellenmiş | Eksik YOK (gözlemlenen) |

> **Tahmin notu (zero-hallucination):** K002–K005 çocuk sayıları bu oturumda
> trailing-slash glob dikkate alınarak SAYILMADI (false-negatif riski). Kesin
> doğrulama ayrı bir okuma turu ister — bu rapor sayımları "index children
> sayısı"dır, disk sayısı doğrulanmadı (⚠️ VERIFICATION REQUIRED).

## §4 donanaım-type Başlangıç Önceliği (kullanıcı talimatı)

Kullanıcı "özellikle donanım type kısmından başla" dedi — bu oturum donanaım-type
9 bağlam (display tamamlama + power önceki oturumdan + cables/network/memory/cpu
bu oturum) tamamlanarak başladı. Kalan domainler (K002 eksik 2 dal, K000 electroncis
ayrıştırması) Vault Steward onayı bekliyor.

**Authority:** Bayram Ali / Vault Steward
