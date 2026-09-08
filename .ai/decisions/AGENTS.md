---
title: "CoreMusic — .ai/decisions Agent Talimatları"
type: agent-registry
folder: ".ai/decisions"
category: vault
date: 2026-09-06
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# .ai/decisions — AGENTS.md

**Zorunlu Bağlantılar:** [[../CLAUDE.md]] · [[./CLAUDE.md]] · [[../brain.md]]

## 1. Amaç

ADR kayıt merkezi: kabul edilen (accepted), reddedilen (rejected) ve taslak (draft) mimari kararlar. Tüm mimari değişikliklerin tek doğruluk kaynağı.

## 2. İçerik Envanteri

| Yol | Amaç |
|-----|------|
| `index.md` | ADR dizini (66 kabul, 12 red, durum sayıları) |
| `accepted/` | ADR-001...ADR-087 kabul edilen kararlar (68 dosya) |
| `rejected/` | R-001...R-012 reddedilenler + index |
| `draft/` | Taslak ADR'ler |

## 3. Agent Kuralları

### Zorunlu
1. Yeni ADR → `[[../../.workflows/adr-creation.md]]` akışı + `adr-template.md` şablonu + `draft/` → onay → `accepted/`
2. Frozen ADR (001-037) değiştirilemez; değişiklik gerekiyorsa yeni ADR ile süpersede edilir
3. `index.md` her ADR eklemesinde güncellenir

### Yasak
1. ADR numarası atlamak veya çakıştırmak
2. Onaysız `accepted/` dosyasını silmek/durum değiştirmek
3. Draft'ı direkt accepted'a taşıyıp index güncellemememek

## 5. İlgili Kaynaklar

| Kaynak | Yol |
|--------|-----|
| Karar anayasası | [[../brain.md]] |
| Şablonlar | [[../.templates/adr/adr-template.md]] |
| Yaşam döngüsü | [[../architecture/04-decisions/adr-lifecycle.md]] |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
