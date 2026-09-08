---
title: "CoreMusic — .ai/.templates/backend Agent Talimatları"
type: agent-registry
folder: ".ai/.templates/backend"
category: vault
date: 2026-09-06
status: active
version: 1.0.0
authority: SSOT
---

# .ai/.templates/backend — AGENTS.md

**Zorunlu Bağlantılar:** [[../AGENTS.md]]

## 1. Amaç
Backend şablonları: `php-template.md` (PHP 8.4), `nodejs-template.md` (Node 20+).

## 2. İçerik Envanteri
2 şablon.

## 3. Kurallar
1. Tüm yeni PHP dosyaları php-template'den türetilir (declare strict, type-hint, PDO prensipleri)
2. Node şablonu yalnızca ADR izin verdiği alanda kullanılır (backend PHP zorunlu — R-010 reddi)

## 5. İlgili Kaynaklar
[[../../.agents/backend-architect.md]] · [[../../architecture/05-data/repository-pattern.md]]

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
