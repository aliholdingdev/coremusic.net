---
node_id: K016-AI
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
  - ".claude/ dizini: agent/ (profil .md'leri — liste 100 sonuçta kırpıldı, tam sayı UNKNOWN) · skills/ (en az 12 SKILL.md) · hooks/ (skill-mandate.cjs · ai-validate-post.cjs · ai-risk-gate.cjs) · mcp.json · settings.json · rules/skill-usage.md — glob 2026-10-12"
  - ".ai/AGENTS.md §4 — 11 ajan (1 MO + 10 uzman) registry kaydı (vault beyanı)"
validation:
  - "disk ölçümü (glob) 2026-10-12 — .claude/ ve .ai/.agents/ envanteri"
  - "v2 spec §2.16 çocuk listesi birebir alındı (2026-10-12)"
open_questions:
  - "MCP sunucu listesi/sürümleri/star kanıtı yok → mcps/ child'ında evidence: [] — sonraki tur (⚠️ VERIFICATION REQUIRED)"
  - ".claude/agent/ TAM profil sayısı bilinmiyor (glob 100 sonuçta kırptı) — sayım gerekli (UNKNOWN)"
  - "rag/ · kg/ · memory/ · evaluation/ · knowledge/ · agent-testing/ için disk kanıtı bu turda toplanmadı — kanıt bekliyor (⚠️ VERIFICATION REQUIRED)"
  - "K016 runtime bağlamı: ajan altyapısı harici harness'ta mı, in-repo katman mı? IMPLEMENTED/PLANNED etiketi yok (⚠️ VERIFICATION REQUIRED)"
group: mimari
---


# K016-AI — Yapay Zekanın Kendi Çalışma Biçimi (Domain)

> **ELI15:** Bu domain, yapay zekanın *kendini* nasıl çalıştırdığıdır: ajanların kimliği, hafızası,
> bildiği kaynaklar (RAG/KG), kullandığı araçlar, açılış (boot) protokolü, izinleri ve sınanması.
> Şarkı öneren model veya ses analizi **burada değildir** — onlar K006-yapay-zeka'nın ürün özellikleridir.

## 1. Kapsam Ayrımı (K006 ↔ K016 — registry §6'da tescilli)

| | K006-yapay-zeka | K016-AI (bu domain) |
|---|---|---|
| Ne | Şarkı öneren, sesi analiz eden **ML ürün özellikleri** | Ajanların **kendi çalışma biçimi** (altyapı) |
| Çocuklar (v2) | model-management · inference · recommendation · personalization · music-analysis · audio-intelligence · local-inference | agents · knowledge · tools · context · rag · kg · mcps · boot-protocol · orchestration · memory · evaluation · permissions · agent-testing |
| Kaynak | v2 §2.6 | v2 §2.16 · registry §6 ("ikisi de meşru, ayrı domain") |

> **Overlap riski (açık):** K016'da evaluation/ + agent-testing/ varken K006'da da evaluation/ + testing/ var.
> Sınır: K016'dakiler **ajanı** sınar; K006'dakiler **modeli/ürünü** sınar. Sınır kademe-3'te tescillenecek.

## 2. Kademe-2 Çocuk Kataloğu (v2 §2.16 — birebir)

| # | Çocuk | Ne işe yarar (ELI15) | Disk kanıtı (2026-10-12) |
|---|---|---|---|
| 1 | agents/ | Ajanların kimliği, rolü, sınırı | ✅ .ai/.agents/ (12 md) · .claude/agent/ (var, sayı UNKNOWN) |
| 2 | knowledge/ | Bilgi bankası / bilgi modeli | ⚠️ kanıt bekliyor |
| 3 | tools/ | Ajanların kullandığı araç tanımları | ⚠️ kanıt bekliyor (sınırlı temas: hooks) |
| 4 | context/ | Bağlam mühendisliği (boot paketi) | ✅ kök AGENTS.md · .claude/{CLAUDE,AGENTS,CONTEXT,WORKFLOW}.md |
| 5 | rag/ | Retrieval-augmented generation | 🔶 vault'ta @.ai/RAG.md referansı (pipeline PLANNED — ADR-030); disk doğrulanmadı |
| 6 | kg/ | Knowledge graph | ⚠️ kanıt bekliyor |
| 7 | mcps/ | MCP sunucu yapılandırması | 🔶 .claude/mcp.json varlığı görüldü; içerik okunmadı → child evidence: [] |
| 8 | boot-protocol/ | Oturum başlangıç protokolü | ✅ kök AGENTS.md §14 Boot Zinciri · .claude/hooks/skill-mandate.cjs |
| 9 | orchestration/ | Görev dağıtımı, handover, eskalasyon | ✅ .ai/AGENTS.md §6–§12 |
| 10 | memory/ | Ajan hafızası | ⚠️ kanıt bekliyor |
| 11 | evaluation/ | Ajan performans değerlendirmesi | ⚠️ kanıt bekliyor |
| 12 | permissions/ | Ajan yetki/izin sınırları | ✅ .ai/AGENTS.md §5 · .claude/settings.json (varlık) |
| 13 | agent-testing/ | Ajanın kendisinin sınanması | ⚠️ kanıt bekliyor |

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
