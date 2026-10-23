---
title: "api.coremusic.net — TODO Listesi"
type: todo
category: project-tracking
version: 1.0.0
status: active
authority: SSOT
updated: 2026-10-23
---

# api.coremusic.net — TODO Listesi

**Zorunlu Bağlantılar:** [[PRD.md]] · [[CONTEXT.md]] · [[../TODO]] · [[../PRD.md]] · [[../.ai/TODO.md]]

**Kural (Zero-Hallucination):** Her madde PRD §7 / disk kanıtına dayanır; kanıtlanamayan madde yazılmaz. Durum: `[ ]` açık · `[x]` kapalı.

**eli10:** API'nin kalan işleri: shared sözleşmesiyle çapraz kontrol, test kapsamı, uç genişleme.
**eli15:** Maddeler `PRD.md §7`'den türetildi; QA kapısı `composer test`'tir. Ölçümsüz iddia yazılmaz — gerekirse `⚠️ VERIFICATION REQUIRED`.

## Açık Maddeler

- [ ] **#7 (shared) + ADR-020 route sözleşmesi çapraz kontrolü** — API route'ları shared sözleşmesiyle karşılaştırılır · Kaynak: `[[PRD.md]]` §7.1
- [ ] **PRD-API-006 test ölçümü** — route + controller + test üçlüsü; QA kapısı `composer test` · Kaynak: `[[PRD.md]]` §7.2
- [ ] **Uç genişlemesi** — yeni uç için route + controller + test birlikte (ölçüm gerekirse `⚠️ VERIFICATION REQUIRED` ile üst `[[../PRD.md]]`'ye bildirilir) · Kaynak: `[[PRD.md]]` §7.3

## Referanslar

- `[[PRD.md]]` (§7 Sonraki Adım) · `[[CONTEXT.md]]` · `[[WORKFLOW.md]]` · `[[AGENTS.md]]` · `[[CLAUDE.md]]`
- Aile indeksi: `[[../TODO]]` · `[[../PRD.md]]` · Vault TODO: `[[../.ai/TODO.md]]`

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Last Updated:** 2026-10-23
