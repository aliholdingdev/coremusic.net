---
type: system
category: orchestration
updated: 2026-08-09
status: active
version: 19.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Orchestration Engine

**SSOT:** [[AGENTS.md]] (ana agent kayıt defteri ve orkestrasyon protokolü)

---

## 1. Amaç

Bu dosya, CoreMusic orkestrasyon motorunun indeksidir. Detaylı orkestrasyon protokolü [[AGENTS.md]]'de bulunur.

## 2. Orkestrasyon Bölümleri

| Bölüm | Konum | İçerik |
|-------|-------|--------|
| Task Dispatch Algorithm | [[AGENTS.md]] §7 | 7 adım|
| Keyword → Agent Routing | [[AGENTS.md]] §6 | 11 keyword grubu |
| Handover Protocol | [[AGENTS.md]] §9 | Mesaj formatı, kurallar, senaryolar |
| Escalation Protocol | [[AGENTS.md]] §10 | 4 seviye, senaryolar |
| Health Check | [[AGENTS.md]] §11 | 5 durum, akış |
| Context Lock | [[AGENTS.md]] §12 | Kilitleme, deadlock önleme |
| MSA Limit | [[AGENTS.md]] §13 | 15 dosya limiti |
| Priority Levels | [[AGENTS.md]] §8 | CRITICAL/HIGH/MEDIUM/LOW |

## 3. Ek Orkestrasyon İçeriği

Bu bölümler sadece bu dosyada bulunur:

| Bölüm | İçerik |
|-------|--------|
| Task Queue | Kuyruk yapısı ve kuralları |
| Agent Communication | İletişim kanalları ve mesaj formatı |
| Metrics & Monitoring | Metrikler ve izleme |
| Troubleshooting | Sorun giderme senaryoları |

## 4. Quick Reference

| İhtiyaç | İlk Adım |
|---------|----------|
| Agent tanımları | [[AGENTS.md]] §15 |
| Görev dağıtımı | [[AGENTS.md]] §7 |
| Handover | [[AGENTS.md]] §9 |
| Eskalasyon | [[AGENTS.md]] §10 |
| Sağlık kontrolü | [[AGENTS.md]] §11 |
| Context lock | [[AGENTS.md]] §12 |

---

*Orchestration Engine v19.0.0 — CoreMusic Enterprise*
*Last Updated: 2026-08-09*
*Mode: Red Team · Human Mode · Truth Mode*
