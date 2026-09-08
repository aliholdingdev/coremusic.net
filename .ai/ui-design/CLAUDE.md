---
title: "CoreMusic — .ai/ui-design Bağlam"
type: context
folder: ".ai/ui-design"
category: vault
date: 2026-09-06
status: active
version: 1.0.0
authority: SSOT
---

# .ai/ui-design — CLAUDE.md

**Zorunlu Bağlantılar:** [[./AGENTS.md]]

## 1. Bağlam
UI Designer'ın birincil çalışma alanı; boot protocol 11-13. adım kaynakları buradadır (mockup index, component inventory, design tokens master).

## 2. Mevcut Durum
| Durum | Değer |
|-------|-------|
| Kök dosya | 6 |
| Alt ağaç | flow(4), mockups, prompt(4), reference, screens(8), tokens |
| Bileşen seti | C01-C16 kanonik |
| Ekran seti | auth, home, music, player, filemanager, quickpanel |

## 3. Komşu İlişkiler
Parent [[../CLAUDE.md]] · PNG SSOT [[../.png/home-1024/CLAUDE.md]] *(üretilecek)* · Kod [[../../assets.coremusic.net/CLAUDE.md]] · Home kaydı [[../subdomains/home.coremusic.net/CLAUDE.md]]

## 4. Değişiklik Protokolü
1. Bileşen/ekran değişimi → PNG + MD + ASCII üçlü senkron
2. Token değişimi → `01_Abstracts` CSS karşılığı aynı iş biriminde
3. Log + vault-sync.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
