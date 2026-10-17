---
node_id: K015-AI
node_type: domain
status: draft
parent_id: null
children:
  - agents
  - knowledge
  - tools
  - context
  - rag
  - kg
  - mcps
  - boot-protocol
  - orchestration
  - memory
  - evaluation
  - permissions
  - agent-testing
depends_on:
  - K007-data        # RAG/bilgi veri katmanı — planlanan (mimari yargı)
  - K009-security    # ajan izinleri/permission sınırı — planlanan
  - K011-application # uygulama katmanı — planlanan
  - K012-services    # ajanların çağırdığı servisler — planlanan
evidence:
  - ".ai/.agents/ = 12 .md (11 uzman profili + AGENTS.md indeksi) — glob 2026-10-12"
  - ".claude/ dizini: agent/ (profil .md'leri = 118 md, sayıldı 2026-10-17) · skills/ (161 dosya, sayıldı 2026-10-17) · hooks/ (3 dosya: skill-mandate.cjs · ai-validate-post.cjs · ai-risk-gate.cjs) · mcp.json · settings.json · rules/skill-usage.md - glob 2026-10-12 · sayım 2026-10-17"
  - ".ai/AGENTS.md §4 — 11 ajan (1 MO + 10 uzman) registry kaydı (vault beyanı)"
validation:
  - "disk ölçümü (glob) 2026-10-12 — .claude/ ve .ai/.agents/ envanteri"
  - "v2 spec §2.16 çocuk listesi birebir alındı (2026-10-12)"
open_questions:
  - "MCP sunucu listesi/sürümleri/star kanıtı yok → mcps/ child'ında evidence: [] — sonraki tur (⚠️ VERIFICATION REQUIRED)"
  - ".claude/agent/ TAM profil sayısı: 118 md (sayıldı 2026-10-17 — kapatıldı)"
  - "rag/ · memory/ · tools/ · orchestration/ · context/ · knowledge/ · boot-protocol/ · kg/ · evaluation/ · mcps/ · permissions/ · agent-testing/ kapatıldı (2026-10-17) — TÜM 13 DAL 3/3 TAMAM (33/33 içerik dosyası)"
  - "K015 runtime bağlamı: ajan altyapısı harici harness'ta mı, in-repo katman mı? IMPLEMENTED/PLANNED etiketi yok (⚠️ VERIFICATION REQUIRED)"
group: mimari
---


# K015-AI — Yapay Zekanın Kendi Çalışma Biçimi (Domain)

> **ELI15:** Bu domain, yapay zekanın *kendini* nasıl çalıştırdığıdır: ajanların kimliği, hafızası,
> bildiği kaynaklar (RAG/KG), kullandığı araçlar, açılış (boot) protokolü, izinleri ve sınanması.
> Şarkı öneren model veya ses analizi **burada değildir** — onlar K006-yapay-zeka'nın ürün özellikleridir.

## 1. Kapsam Ayrımı (K006 ↔ K015 — registry §6'da tescilli)

| | K006-yapay-zeka | K015-AI (bu domain) |
|---|---|---|
| Ne | Şarkı öneren, sesi analiz eden **ML ürün özellikleri** | Ajanların **kendi çalışma biçimi** (altyapı) |
| Çocuklar (v2) | model-management · inference · recommendation · personalization · music-analysis · audio-intelligence · local-inference | agents · knowledge · tools · context · rag · kg · mcps · boot-protocol · orchestration · memory · evaluation · permissions · agent-testing |
| Kaynak | v2 §2.6 | v2 §2.16 · registry §6 ("ikisi de meşru, ayrı domain") |

> **Overlap riski (açık):** K015'da evaluation/ + agent-testing/ varken K006'da da evaluation/ + testing/ var.
> Sınır: K015'dakiler **ajanı** sınar; K006'dakiler **modeli/ürünü** sınar. Sınır kademe-3'te tescillenecek.

## 2. Kademe-2 Çocuk Kataloğu (v2 §2.16 — birebir)

| # | Çocuk | Ne işe yarar (ELI15) | Disk kanıtı (2026-10-12) |
|---|---|---|---|
| 1 | agents/ | Ajanların kimliği, rolü, sınırı | ✅ .ai/.agents/ (12 md) · .claude/agent/ (118 md, 2026-10-17 sayımı) |
| 2 | knowledge/ | Bilgi bankası / bilgi modeli | ✅ .ai/RAG.md §3 · .ai/keys.md · KnowledgeBase.php · içerik 3/3 üretildi |
| 3 | tools/ | Ajanların kullandığı araç tanımları | ✅ ToolCalling.php + AIOrchestrator.php (kod kanıtı) · .claude/hooks/*.cjs + settings.json · içerik 3/3 üretildi |
| 4 | context/ | Bağlam mühendisliği (boot paketi) | ✅ root AGENTS.md §14 · .ai/AGENTS.md §24/§13/§12 · .ai/scripts/*.ps1 · içerik 3/3 üretildi |
| 5 | rag/ | Retrieval-augmented generation | ✅ .ai/RAG.md (177 satır, disk doğrulandı 2026-10-17) · ADR-030 pipeline PLANNED · içerik 3/3 üretildi |
| 6 | kg/ | Knowledge graph | ✅ .ai/architecture/index.md (K-graph) · wiki-link-check.ps1 (43 sat) + kalip-abc-check.ps1 (231 sat) · KnowledgeBase.php · içerik 3/3 üretildi |
| 7 | mcps/ | MCP sunucu yapılandırması | ✅ .claude/mcp.json (1 sunucu) · settings.json mcpServers L48-L99 (3 aktif + 3 disabled) · içerik 3/3 üretildi |
| 8 | boot-protocol/ | Oturum başlangıç protokolü | ✅ .ai/MEMORY.md §5 · .claude/hooks/*.cjs (3 dosya, künyeli) · içerik 3/3 üretildi |
| 9 | orchestration/ | Görev dağıtımı, handover, eskalasyon | ✅ .ai/AGENTS.md §6–§12 · AIOrchestrator.php (AGENT_MAP/TASK_TYPES) · içerik 3/3 üretildi |
| 10 | memory/ | Ajan hafızası | ✅ .ai/MEMORY.md (728 satır, 2026-10-17) · MemorySystem.php (kod kanıtı) · içerik 3/3 üretildi |
| 11 | evaluation/ | Ajan performans değerlendirmesi | ✅ .ai/AGENTS.md §24.4-§24.5 + §16 · wiki-link-check/kalip-abc/frontmatter-check betikleri · içerik 3/3 üretildi |
| 12 | permissions/ | Ajan yetki/izin sınırları | ✅ .ai/AGENTS.md §5/§10-§12/§17-§18 · settings.json hook kapıları · Redactor.php (repo kanıtı) · içerik 3/3 üretildi |
| 13 | agent-testing/ | Ajanın kendisinin sınanması | ✅ .ai/AGENTS.md §6/§11/§16 · shared/tests/ envanteri · wiki-link-check betiği · içerik 3/3 üretildi |

## 3. Durum

| Alan | Değer |
|---|---|
| status | draft (registry §5 — Band-1 üretim aşaması) |
| Kademe-3/4 | 13 subdomain → Bounded Context/Module bu fazda AÇILMADI (ADR-097 K4 — sonraki faz) |
| Üreten pay | K-Space V3 Band-1 — A4 (2026-10-12) |
| Bağlı dosyalar | ai-master.md · ai-bagimliliklar.md · ai-dogrulama.md |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-1 |
| mimari | `ai-bagimliliklar.md` | Katman bağı ve used_by haritası | Kademe-1 |
| kanit | `ai-dogrulama.md` | İddia, URL ve tarih doğrulama matrisi | Kademe-1 |
| mimari | `ai-master.md` | Master künye ve sorumluluk haritası | Kademe-1 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
