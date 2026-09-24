---
title: "CoreMusic Scripts Index"
type: index
category: scripts-registry
version: 1.0.0
status: active
authority: SSOT
updated: 2026-09-24
---

# CoreMusic — Scripts Index

**Amaç:** `.ai/scripts/` klasöründeki yardımcı script'lerin kaydı. Vault yardımcı araçları, doküman değildir; bu indeks yalnızca dosya envanterini ve durumunu tutar.

**İlgili:** [[.workflows/vault-sync]] · [[index]] · [[log]]

---

## §1 Mevcut Script'ler (disk — 2026-09-24)

| # | Dosya | Tip | Amaç |
|---|-------|-----|------|
| 1 | `.ai/scripts/vault-utf8-writer.mjs` | Node.js | Vault dosyalarını UTF-8 olarak güvenli yazar (mojibake önleme) |
| 2 | `.ai/scripts/vault-faz4-sweep.mjs` | Node.js | Faz 4 taraması — vault geneli toplu işlem |
| 3 | `.ai/scripts/fix-mojibake.py` | Python | Mojibake (bozuk karakter) taraması ve düzeltme |

**Toplam:** 3 dosya (2 .mjs + 1 .py).

---

## §2 Eksik / Diskte OLMAYAN Öğeler

| Öğe | Durum | Referanslar |
|-----|-------|-------------|
| `session-save.mjs` | ⚠️ YOK — referanslar: WORKFLOW/vault-sync, manuel senkron 2026-09-24 | [[.workflows/vault-sync]] |
| `vault-post-update.mjs` | ⚠️ YOK — referanslar: WORKFLOW/vault-sync, manuel senkron 2026-09-24 | [[.workflows/vault-sync]] |
| `project-state.md` | ⚠️ YOK — referanslar: WORKFLOW/vault-sync, manuel senkron 2026-09-24 | [[.workflows/vault-sync]] |

**Not:** Bu üç öğe geçmiş vault kayıtlarında geçmişte bulunabilir; 2026-09-24 disk taramasında `.ai/` genelinde (rekürsif) bulunamamıştır. Ekleme/üretme yapılmamış, yalnızca durum kaydedilmiştir.

---

## §3 İlgili Ayrı Klasör: architecture/scripts/

`.ai/architecture/scripts/` klasörü bu indeksin **kapsamı dışındadır** (farklı klasör), envanter notu:

| Dosya | Tip | Not |
|-------|-----|-----|
| `.ai/architecture/scripts/katman-sayim.ps1` | PowerShell | Katman sayım/denetim script'i — ADR-026 sayım birimiyle ilgili |

---

## §4 Kullanım Kuralları

1. Bu klasöre yeni script eklendiğinde §1 tablosuna satır eklenir (append).
2. Bir script silinirse/düşerse §2'ye ⚠️ YOK olarak taşınır — uydurma yol yazılmaz.
3. Vault ana senkronu `.workflows/vault-sync.md` akışıyla yürütülür; bu indeks akışın kendisi değildir, yalnızca envanterdir.
4. Session kapanış kaydı: manuel senkron 2026-09-24 (script'leşmemiş işler için).

---

## §5 Değişiklik Geçmişi

| Tarih | Sürüm | Değişiklik | Sorumlu |
|-------|-------|-----------|---------|
| 2026-09-24 | 1.0.0 | İlk oluşturma — 3 mevcut script + 3 eksik öğe kaydı (uydurma yok, disk okuması) | Vault Steward |

---

*Last Updated: 2026-09-24*
*Mode: Red Team · Human Mode · Truth Mode*
