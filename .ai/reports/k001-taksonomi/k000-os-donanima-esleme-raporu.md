---
title: "K000 OS ↔ K001 Donanım Sınır Raporu"
type: report
node_id: reports/k001-taksonomi/k000-os-donanima-esleme-raporu
status: active
evidence:
  - ".ai/architecture/K000-isletim-sistemi/index.md (children: 12 — electroncis dahil)"
  - ".ai/architecture/K000-isletim-sistemi/os-master.md"
  - ".ai/architecture/K001-donanım/donanım-anayasa.md §9 (bağımlılık zinciri) · §10/5 · §10/6"
validation: "K000 index children disk ölçümü 2026-10-13"
open_questions:
  - "Q-OS1: K000/index.md depends_on: [K002-altyapi] — diskte K002-electroncis var (isim uyuşmazlığı)"
  - "Q-OS2: anayasa §10/5 'Ses donanımı envanteri K001 mi K003 mü?' — açık soru, cevaplanmadı"
  - "Q-OS3: anayasa §10/6 'firmware-sürüm uyuşmazlığı kimin işi (K005 sınırı)?' — açık"
group: rapor
date: 2026-10-13
updated: 2026-10-13
---

# K000 OS ↔ K001 Donanım Sınır Raporu

## §1 K000-isletim-sistemi Çocukları (disk ölçümü — 12)

| # | Çocuk | Konu |
|---|---|---|
| 1 | `isletim-sistemi-api/` | Syscall · platform API |
| 2 | `isletim-sistemi-audio/` | AAudio · stream yaşam döngüsü |
| 3 | `isletim-sistemi-ag/` | POSIX socket · Winsock karşılaştırma |
| 4 | `electroncis/` | **K000'e ait electroncis alt klasörü** (K002 ile OVERLAP — çelişki) |
| 5–12 | (diğerleri) | ... |

**Kritik çelişki (B7):** K000'ın kendi `electroncis/` alt klasörü var. K002-electroncis
ayrı bir kademe-1 domain olarak açılmış (2026-10-13). İki "electroncis" ne zaman,
nasıl ayrışacak — Vault Steward kararı.

## §2 OS ↔ Donanım Sınırı (anayasa §9 bağımlılık zinciri)

| Kural | Kaynak |
|---|---|
| OS sürücü bağlamı donanım mı K000 mı? | anayasa §10/5 — **AÇIK SORU** ⚠️ |
| Donanım envanteri K001'in; OS sürücü katmanı K000'in | anayasa §9 |
| Ring-0 sürücü/çekirdek kodu = K000 (K001'de değil) | donanım-devices §7 |

## §3 OS Perspektifinden Yeni 5 Dal

| Dal | OS ile sınır | OS'e bağımlı mı? |
|---|---|---|
| display | Ekran kontrolcüsü sürücüsü K000'de (K001'de panel sınıfı) | Hayır — bağımsız sınıf |
| cables | Kablo fiziksel — OS'e bağımlı değil | Hayır |
| network | Ağ PHY donanımı K001; ağ protokolü + driver K000 | Sürücü K000'de |
| memory | Bellek yongası K001; bellek yönetim algoritması K000 | Evet — yönetim K000'de |
| cpu | İşlemci sınıfı K001; çekirdek/schedule K000 | Evet — K000'de |

## §4 ADR-102 Etki Analizi (OS boyutu)

ADR-102 (draft) toplam 9 bağlam tanımlar (display·power önceki oturum + cables·network·memory·cpu bu oturum); **hiçbiri K000'e dosya eklemez**. OS sınırı
anayasa §9'da kalır. Ancak §10/5 (ses donanımı K001 mi K003 mü?) açık sorusu,
audio dalının sınırını etkilemeye devam eder — bu ADR kapsamı DIŞINDA.

**Authority:** Bayram Ali / Vault Steward
