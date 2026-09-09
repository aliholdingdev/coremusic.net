---
title: "CoreMusic — .ai/ui-design Agent Talimatları"
type: agent-registry
folder: ".ai/ui-design"
category: vault
date: 2026-09-06
status: active
version: 1.0.0
authority: SSOT
governance: Red Team · Human Mode · Truth Mode
---

# .ai/ui-design — AGENTS.md

**Zorunlu Bağlantılar:** [[../CLAUDE.md]] · [[./CLAUDE.md]] · [[../../assets.coremusic.net/AGENTS.md]]

## 1. Amaç
UI tasarım SSOT: mockup index, bileşen envanteri (C01-C16), uygulama planı, erişilebilirlik, akışlar, prompt'lar, ASCII ekranlar, design token'lar.

## 2. İçerik Envanteri

| Yol | Amaç |
|-----|------|
| `00-mockup-index.md` | 19 PNG mockup indeksi — UI işlerinin İLK okunacak dosyası |
| `01-component-inventory.md` | C01-C16 kanonik bileşen envanteri (BEM sınıfları + ölçüler) |
| `02-implementation-plan.md` | 15-step CSS uygulama planı |
| `03-accessibility-gaps.md` | WCAG 2.2 AA boşlukları |
| `04-vault-registration.md` | Vault kayıt notu |
| `05-md-pattern-standard.md` | MD kanonik şema referansı (9-alanlı frontmatter + footer standardı) |
| `responsive-device-mode.md` | Device mod rehberi |
| `flow/` | Akışlar (auth, music, navigation, settings) |
| `mockups/` | Mockup tanım dokümanları |
| `prompt/` | Bileşen/layout/sayfa/ekran prompt'ları |
| `reference/` | Kaynak referanslar (PHP mimari, token'lar, doğrulama) |
| `screens/` | ASCII art wireframe'ler |
| `tokens/` | Design token master + CSS |

## 3. Kurallar
1. Guardrail #11: UI kodu öncesi `00-mockup-index.md` + ilgili PNG okunur
2. C01-C16 BEM sınıf adları değiştirilemez; yeni bileşen C17+ olarak eklenir
3. PNG > MD > ASCII sadakat sırası

## 5. İlgili Kaynaklar
[[../../architecture/l3-presentation/AGENTS.md]] · [[./tokens/design-tokens-master.md]] · [[../../decisions/accepted/ADR-044-dynamic-user-theme-engine.md]]

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-08
