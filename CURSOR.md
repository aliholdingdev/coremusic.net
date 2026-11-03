---
title: "CoreMusic — Cursor IDE Rules & Karpathy Guidelines Pointer"
type: pointer
category: boot
version: 1.0.0
status: active
authority: "SSOT: .ai/CLAUDE.md · .cursor/rules/karpathy-guidelines.mdc · .cursorrules"
updated: 2026-11-01
---

# CoreMusic — CURSOR.md (Cursor IDE Setup & Karpathy Guidelines)

Bu dosya, Cursor IDE kullanan mühendisler ve ajanlar için konfigürasyon ve davranış standartlarını tanımlar.

## 1. Cursor Kural Mimarisi

CoreMusic deposunda Cursor için iki katmanlı kural sistemi aktiftir:

1. **Modern Proje Kuralı (`.cursor/rules/karpathy-guidelines.mdc`):**
   - `alwaysApply: true` ile işaretlenmiştir.
   - Andrej Karpathy'nin 4 temel davranış ilkesini (**Think Before Coding**, **Simplicity First**, **Surgical Changes**, **Goal-Driven Execution**) otomatik olarak her sohbete enjekte eder.
2. **Kök Kural Dosyası (`.cursorrules`):**
   - CoreMusic'in 16 Hard Guardrail'ını, Senior Mode analizini, Light Mode zorunluluğunu ve `.ai/` vault SSOT referanslarını tutar.

---

## 2. Dört Temel Karpathy İlkesi (Özet)

1. **Think Before Coding:** Varsayım yapma. Şüpheyi ve belirsizliği gizleme. Seçenekleri ve ödünleşimleri sun. Basit bir yol varsa itiraz et.
2. **Simplicity First:** Sorunu çözen minimum kod. Spekülatif hiçbir şey yok. İstenmeyen soyutlama üretme. 200 satırlık iş 50 satırda çözülebiliyorsa yeniden yaz.
3. **Surgical Changes:** Yalnızca dokunman gereken yere dokun. İlgisiz kodlara, formatlara ve yorumlara dokunma. Kendi izini temizle (yetim import/değişkenleri sil).
4. **Goal-Driven Execution:** Başarı kriterini testlerle tanımla. Doğrulanana kadar döngüde kal. Çok adımlı işlerde kısa plan belirt (`[Step] → verify: [check]`).

---

## 3. Doğrulama ve Nasıl Çalıştığı (Verification)

Bu yönergelerin başarılı şekilde işlediği şu belirtilerden anlaşılır:
- **Diff'lerde sıfır gereksiz satır:** Yalnızca kullanıcının talep ettiği değişiklikler diff'e yansır.
- **Aşırı mühendislikten kaçınma:** Kod ilk seferde sade, anlaşılır ve tek amaca odaklı yazılır.
- **Açıklayıcı sorular kod yazmadan önce gelir:** Hatalar oluştuktan sonra değil, uygulama öncesi netleştirme yapılır.
- **Surgical PR'lar:** Temiz, tek odaklı ve alakasız refactoring barındırmayan değişiklikler.

---

## 4. Bağlantılar ve SSOT

| Kapsam | Dosya | Amaç |
|---|---|---|
| **Cursor Rule** | [`.cursor/rules/karpathy-guidelines.mdc`](.cursor/rules/karpathy-guidelines.mdc) | Her promptta çalışan Karpathy direktifleri (`alwaysApply: true`) |
| **Cursor Root** | [`.cursorrules`](.cursorrules) | Kök Cursor kuralları |
| **Kod Örnekleri** | [`.claude/skills/karpathy-guidelines/references/EXAMPLES.md`](.claude/skills/karpathy-guidelines/references/EXAMPLES.md) | Karşılaştırmalı anti-pattern vs doğru kod örnekleri |
| **Claude Skill** | [`.claude/skills/karpathy-guidelines/SKILL.md`](.claude/skills/karpathy-guidelines/SKILL.md) | Claude / OpenCode için Karpathy skill'i (Skill #14) |
| **Claude Boot** | [`CLAUDE.md`](CLAUDE.md) | Claude Code kök göstergesi |
| **OpenCode Boot** | [`AGENTS.md`](AGENTS.md) | OpenCode V2 kök kuralları |
| **Gemini Boot** | [`GEMINI.md`](GEMINI.md) | Gemini / Antigravity kök göstergesi |
| **Vault Anayasası**| [`.ai/CLAUDE.md`](.ai/CLAUDE.md) | 16 Hard Guardrail & Master Anayasa |
| **Oturum Hafızası**| [`.ai/durum.md`](.ai/durum.md) | Tek sayfalık oturum devir dosyası |

---

*CoreMusic Cursor Pointer v1.1.0 — Updated: 2026-11-01*
