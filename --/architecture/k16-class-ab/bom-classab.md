---
title: "bom-classab — Class AB BOM (stub-with-truth)"
type: docs
category: architecture
version: 1.0.0
status: active
authority: "SSOT: .ai/architecture/00-master-index.md — bu dosya yalnız eski link uyumu stub'udur"
updated: 2026-10-07
tier: 3
domain: architecture-hardware
ssot: false
risk: low
owner: "Vault Steward"
depends-on: ["architecture/00-master-index.md"]
---

# bom-classab — Class AB BOM (stub)

**Durum:** `architecture/k16-class-ab/bom-classab.md` **diskte YOK** (eski ağaç silindi) — 2 link.
`.ai/brain.md` §5 Electronics Registry bu dosyayı referans verir (2026-09-26 kaydı) → **ölü atıf**.

## Bugünkü Karşılığı (gerçek kanıt — sayılar vault'tan, dosya silinmiş)

| İddia | Kaynak | Durum |
|-------|--------|-------|
| 639 bileşen · ~$1.067,44 (8 kanal) / ~$133,43 (kanal) | `.ai/brain.md` §5 (Electronics Registry satırı, 2026-09-26) | DESIGN (kayıt) |
| 1.775 BOM satırı · ~$682 sistem maliyeti · Mouser/Digikey | `.claude/CLAUDE.md` §5 (K20 satırı) | DESIGN (kayıt) |
| Kanal varyant SKU politikası | `.ai/.decisions/accepted/ADR-090-channel-variant-product-family.md` | DESIGN |
| BOM dosyasının kendisi | ⚠️ VERIFICATION REQUIRED — **silinmiş** (glob 0, 2026-10-07) | DEPRECATED (dosya) |

⚠️ İki BOM rakamı **birbirinden farklı** (639/1.067$ vs 1.775/682$) — kaynaklar ayrı
ölçümlere dayanır; yeniden üretimde hangisinin geçerli olduğu Vault Steward onayı gerektirir
(Contradiction Gate — `.ai/CLAUDE.md` §7 #12).

**Domain:** d10 → [[architecture/19-domain-d10-elektronik-tasarim]] (K471–K472)
