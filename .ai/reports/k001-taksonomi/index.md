---
title: "K001 Taksonomi Raporları — İndeks"
type: report-index
node_id: reports/k001-taksonomi
status: active
children:
  - k001-mevcut-durum-raporu
  - k001-tur-envanteri-raporu
  - k001-cihaz-envanteri-raporu
  - k000-os-donanima-esleme-raporu
  - k001-domain-esleme-raporu
  - k001-celiski-raporu
  - k001-onerilen-agac-raporu
  - k001-uygulama-plani-raporu
  - k001-kabul-kriterleri-raporu
evidence:
  - ".ai/architecture/K001-donanım/** (disk ölçümü 2026-10-13)"
  - ".ai/architecture/K000-isletim-sistemi/index.md"
  - ".ai/architecture/K002-electroncis/index.md"
  - ".ai/.decisions/draft/ADR-102-tur-taksonomi-genislemesi-5-dal.md"
validation: "9 rapor + bu indeks = 10 dosya · 2026-10-13 · tüm iddialar disk kanıtlı"
open_questions:
  - "K002 ID GAP: architecture/index.md §6 'K002 yok' der, K002-electroncis/ diskte var — Vault Steward kararı bekliyor"
group: rapor
date: 2026-10-13
updated: 2026-10-13
---

# K001 Taksonomi Raporları — İndeks

K001-donanım tür taksonomisinin (types · devices · OS) kapsamlı analizi.
Kullanıcı 2026-10-13 talebi: K001–K005 paralel dal incelemesi, eksik dalların
önerilmesi, özellikle `donanaım-type` bölümünden başlanması.

## Rapor Kataloğu

| # | Rapor | Odak | Durum |
|---|---|---|---|
| 1 | `k001-mevcut-durum-raporu` | K001 ağacı, dosya sayıları, dal durumları | aktif |
| 2 | `k001-tur-envanteri-raporu` | **types** — tür sınıflandırma, 10 bağlam (gpu ADR-102 dışı), 25 alan şeması | aktif |
| 3 | `k001-cihaz-envanteri-raporu` | **devices** — birim cihaz envanteri, sınıf↔birim ayrımı | aktif |
| 4 | `k000-os-donanima-esleme-raporu` | **os** — K000 OS ↔ K001 donanım sınırı | aktif |
| 5 | `k001-domain-esleme-raporu` | K001–K005 paralel dal haritası, eksik dal önerileri | aktif |
| 6 | `k001-celiski-raporu` | Tüm çelişkiler (gizleme yok) | aktif |
| 7 | `k001-onerilen-agac-raporu` | Önerilen dosya ağacı (PROPOSED) | aktif |
| 8 | `k001-uygulama-plani-raporu` | Uygulama adımları ve sorumlular | aktif |
| 9 | `k001-kabul-kriterleri-raporu` | 12 kabul kriteri | aktif |

## Kapsam Kuralları

- **Yazım yasağı:** `donanaım-type` v2 kaynağına sadık — düzeltme YASAK.
- **Zero-hallucination:** Bilinmeyen = UNKNOWN · doğrulanamayan = ⚠️ VERIFICATION REQUIRED.
- **Sınır:** Power/PCB/BOM → `K002-electroncis` cross-ref (K001'de değil).
- **Değişmez:** ADR-038 (XMOS XU316 + PCM3168A) · ADR-089 (Class AB) · PCM5122 yasak.
- **Documentation ≠ physical production:** Rapordaki tür tanımları üretim değil, dokümantasyondur.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
