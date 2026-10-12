---
title: "K016-AI — Doğrulama"
node_id: K016-AI
node_type: domain-validation
status: draft
updated: 2026-10-12
version: 1.0.0
---

# K016-AI — ai-dogrulama.md

## §1 Amaç

K016-AI domaininin iddialarını **ölçülebilir ölçütlerle** doğrular. Kanıt yoksa UNKNOWN /
⚠️ VERIFICATION REQUIRED yazılır; tahmin üretilmez (kök AGENTS.md §3 Zero-Hallucination).

## §2 Ölçütler → Durum

| # | Ölçüt | Kanıt (gerçek yol) | Durum |
|---|---|---|---|
| 1 | Ajan profilleri diskte mevcut mu? | .ai/.agents/ = 12 .md (11 uzman + indeks) — glob 2026-10-12 | ✅ verified |
| 2 | Skill altyapısı mevcut mu? | .claude/skills/ — en az 12 SKILL.md (listeleme 2026-10-12) | ✅ verified |
| 3 | Hook/gate altyapısı mevcut mu? | .claude/hooks/skill-mandate.cjs · ai-validate-post.cjs · ai-risk-gate.cjs | ✅ verified |
| 4 | Boot protokolü dokümante mi? | kök AGENTS.md §14 (Boot Zinciri tablosu) + .claude/rules/skill-usage.md | ✅ verified (doküman) |
| 5 | Orkestrasyon kuralları mevcut mu? | .ai/AGENTS.md §6 routing · §7 dispatch · §9 handover · §10 escalation · §12 lock | ✅ verified (doküman) |
| 6 | Permissions/domain boundary tanımlı mı? | .ai/AGENTS.md §5 (12 dosya tipi sahipliği) | ✅ verified (doküman) |
| 7 | MCP yapılandırma dosyası var mı? | .claude/mcp.json — varlık görüldü; **içerik okunmadı** | 🔶 partial |
| 8 | RAG altyapısı uygulanmış mı? | vault beyanı: RAG pipeline PLANNED (ADR-030) — disk kanıtı yok | ⚠️ verify-required |
| 9 | knowledge/tools/kg/memory/evaluation/agent-testing diskte var mı? | bu turda ölçüm YAPILMADI | ⚠️ verify-required |
| 10 | Ajan sayıları tutarlı mı (11 vs 118)? | 11 = proje ajanları (.ai/AGENTS.md §4) · 118 = global registry (boot beyanı) — farklı kümeler, bağımsız sayım yok | 🔶 vault beyanı |

## §3 Doğrulanmayan Yüzeyler (kanıt bekliyor — sonraki tur)

knowledge/ · tools/ · kg/ · memory/ · evaluation/ · agent-testing/ · MCP içerik/sürümü · .claude/agent/ tam sayı.

## §4 Web-Kanıt Kayıtları

> **Bu turda web araştırması YAPILMADI** — A4 payının kanıt planı disk-öncelikliydi; MCP star/sürüm
> araştırması bilinçli olarak sonraki tura bırakıldı (görev talimatı). Tablo iskelettir.

| # | İddia | URL | Tarih | Durum |
|---|---|---|---|---|
| — | (boş — kanıt bekliyor) | — | — | ⏳ |

**Bekleyen web-iddiaları (sonraki tur):**

1. MCP (Model Context Protocol) güncel sürümü ve resmi sunucu listesi — mcps/ için.
2. Agent evaluation best-practice dayanakları — evaluation/ için.
3. RAG pipeline tasarım dayanakları (ADR-030 revizyonu) — rag/ için.

## §5 Open Questions (⚠️ VERIFICATION REQUIRED)

| # | Soru |
|---|---|
| 1 | mcps/: MCP sunucu adları, sürümleri, lisansları? (mcp.json içeriği okunacak) |
| 2 | .claude/agent/ dosya sayısı kesin kaç? |
| 3 | memory/: MEMORY.md diskte var mı, oturum hafızası nasıl kalıcılaştırılıyor? |
| 4 | agent-testing/: ajan davranış testi için harness var mı (eval seti vb.)? |
| 5 | evaluation/: çıktı kalitesi ölçütleri tanımlı mı (rubrik skoru vb.)? |
| 6 | K016 runtime entegrasyonu PLANNED mi, IMPLEMENTED mı? etiket yok |

## §6 Kapanış Kuralı

Bu domain verified statüsüne, §2'deki tüm ⚠️ satırları kapanmadan geçemez. Kanıt yalnız
gerçek dosya yolu / URL ile eklenir (ADR-097 §5.3: doğrulanmayan iddia yazılmaz).

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12 · Mode: Truth Mode
