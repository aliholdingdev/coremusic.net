---
title: "K016-AI — Bağımlılıklar"
node_id: K016-AI
node_type: domain-dependencies
status: draft
updated: 2026-10-12
version: 1.0.0
---

# K016-AI — ai-bagimliliklar.md

> **Etiket sözlüğü:** kanıtlı = gerçek dosya/okuma ile gösterildi · planlanan = mimari yargı
> (üretim kararı, kod kanıtı yok) · vault beyanı = vault dosyası iddia ediyor, bağımsız ölçüm yok.

## §1 Amaç

Bu dosya K016-AI'nin **neye muhtaç olduğunu** (depends_on), **kimin ona muhtaç olduğunu** (used_by),
entegrasyon sınırlarını ve riskli bağımlılıkları taşır. Kaynak: v2 §2.16 + registry §5/§6 + disk ölçümü 2026-10-12.

## §2 depends_on (bu domain kime muhtaç)

| # | Hedef | Etiket | Gerekçe | Kanıt / dayanak |
|---|---|---|---|---|
| 1 | K007-data | planlanan | RAG/knowledge tabanı veri katmanını kullanır | v2 §2.16 (rag/knowledge children) — kod kanıtı yok |
| 2 | K009-security | planlanan | permissions/ ajan yetkilerini güvenlik politikalarına bağlar | .ai/AGENTS.md §5 (dosya tipi sahipliği) |
| 3 | K011-application | planlanan | ajanlar uygulama yüzeyini çağırabilir | v2 §2.11 · genel mimari yargı |
| 4 | K012-services | planlanan | ajan tool çağrısı servis katmanına gider | v2 §2.12 · genel mimari yargı |
| 5 | Harici harness (OpenCode/Claude Code) | kanıtlı (doküman) | ajanlar bu harness'larda çalışıyor; .claude/ + boot paketi buna bağlı | .claude/settings.json · kök AGENTS.md boot zinciri |

## §3 used_by (bu domain kimleri besliyor)

| # | Kullanıcı | Etiket | Gerekçe |
|---|---|---|---|
| 1 | Vault günlük süreçleri (AGENTS/WORKFLOW/RAG) | kanıtlı (doküman) | boot-protocol + orchestration + permissions aktif kullanılıyor (.ai/AGENTS.md §6–§14) |
| 2 | K020-recomenataion | planlanan | öneri motoru ajan altyapısını kullanır (v2 K020 children'ı) |
| 3 | K006-yapay-zeka | planlanan (sınırlı) | K006 eval/test süreçleri ajan değerlendirme araçlarını kullanabilir — sınır dikkatli çekilecek (§5 risk-3) |

## §4 Entegrasyon Sınırları

| Sınır | Kural | Kaynak |
|---|---|---|
| K016 ↔ K006 | K016 ajanı **altyapıyı**, K006 modeli **ürünü** tanımlar; eval/test isim benzerliği ≠ aynı kapsam | registry §6 |
| K016-permissions ↔ K009-security | Ajan yetkisi (hangi dosyaya dokunabilir) K016'da; sistem auth/encryption K009'da | .ai/AGENTS.md §5 vs §2 |
| K016-agent-testing ↔ K019-testing | Ajan davranışı testi K016'da; genel test altyapısı (PHPUnit/vitest/E2E) K019'da | v2 §2.16/§2.19 |
| K016-tools ↔ K012-services | Ajan aracı, servis katmanının **kamuya açık** yüzeyini çağırır; servis içine sızamaz | mimari yargı (planlanan) |
| vault yazma yetkisi | Ajanların vault yazımı MO yetkisindedir; diğer ajanlar okuma serbest | .ai/AGENTS.md §5 |

## §5 Riskli Bağımlılıklar

| # | Risk | Etki | Azaltma (öneri) |
|---|---|---|---|
| 1 | **Harness bağımlılığı:** ajan altyapısı büyük ölçüde harici harness config'lerinde (.claude/ · global ~/.config/opencode/) — repo içi katman ince | harness değişirse boot/orchestration kırılır | vault-içi özgün tanımları (.ai/AGENTS.md) SSOT tut; harness config'lerini **türev** say |
| 2 | **Kanıtsız alt alanlar:** 6/13 çocuk (knowledge · tools · kg · memory · evaluation · agent-testing) kanıtlanamadı | domain kağıtta geniş, tabanda dar | sonraki turda disk sayımı; kanıt gelmezse child status: verify-required |
| 3 | **K006 ile isim örtüşmesi:** evaluation/ + testing benzerliği | yanlış dosyada iş, kapsam kayması | kademe-3'te sınır ADR'si (K016-eval = ajan · K006-eval = model) |
| 4 | **mcps kanıtsızlığı:** mcp.json içeriği okunmadı → sürüm/health bilinmiyor | kırık MCP sessiz ajan arızası yapar | sonraki turda mcp.json okuma + sunucu bazlı sürüm kaydı |
| 5 | **Yetki çatışması potansiyeli:** permissions/ (K016) vs security-engineer yetkisi (K009) | iki yerde farklı yetki tanımı | tek SSOT: .ai/AGENTS.md §5; çelişkide registry kazanır |

## §6 Açık Sorular (⚠️ VERIFICATION REQUIRED)

1. K007-data ile entegrasyonun somut yüzeyi nedir (RAG store: MySQL mi, dosya mi, vektör db mi)? — bilinmiyor.
2. K020-recomenataion bu altyapıyı ne zaman, hangi ajanla kullanacak? — plan yok.
3. Harness bağımlılığı repo-dışı → lisans/taşınabilirlik notu gerekli mi? (K020-licence'ın konusu.)

## §7 Kaynaklar

- v2 spec §2.16 · §2.6 (ayrım) · ADR-097 §2/§5 · .ai/architecture/index.md §5/§6
- Disk ölçümü: .claude/ · .ai/.agents/ · .ai/AGENTS.md (2026-10-12)

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
