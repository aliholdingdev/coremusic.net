---
title: "CoreMusic AI Agent Registry"
type: agent-registry
version: 1.0
authority: SSOT

mode:
  - Red Team
  - Truth Mode
  - Human Mode

purpose:
  - Agent Management
  - Task Routing
  - Specialist Coordination
  - Responsibility Definition

reference:
  authority: ".ai/AGENTS.md"

  source_of_truth:
    - ".ai/CLAUDE.md"
    - ".ai/AGENTS.md"
    - ".ai/WORKFLOW.md"
    - ".ai/brain.md"
    - ".ai/index.md"
    - ".ai/MEMORY.md"
    - ".ai/log.md"
    - ".ai/engine.md"

  agent_architecture:
    - ".ai/.agents/"
    - "Agent profiles"
    - "Agent skills"
    - "Agent responsibilities"

  available_agents:
    - "Master Orchestrator"
    - "Backend Architect"
    - "Frontend/UI Engineer"
    - "Security Engineer"
    - "Data Engineer"
    - "QA Engineer"
    - "DevOps Engineer"
    - "Embedded Engineer"
    - "DSP Firmware Engineer"
    - "Audio Hardware Engineer"
    - "Windows Software Engineer"

  routing_priority:
    - "Security requirements"
    - "Architecture requirements"
    - "Agent specialization"
    - "Existing implementation"
    - "User requirements"

  update_policy:
    preserve_existing_structure: true

    require_approval_for:
      - "agent role change"
      - "agent permission change"
      - "agent removal"
      - "new specialist agent creation"
      - "agent workflow change"

changelog:
  - version: 1.0
    date: 2026-08-15
    changes:
      - Initial Agent Registry Constitution
      - Added Agent Routing Rules
      - Added Agent Responsibility Definitions
      - Added Agent Governance Rules
---

# CoreMusic — Workflow Registry

**Zorunlu Bağlantılar:** [[WORKFLOW.md]] · [[CLAUDE.md]] · [[AGENTS.md]]

# 1. Amaç

Bu dosya CoreMusic AI sistemindeki tüm uzman ajanların merkezi kayıt sistemidir.

Sorumlulukları:

- Agent tanımları
- Uzmanlık alanları
- Görev yönlendirme
- Yetki sınırları
- Agent iletişim kuralları
- Agent yaşam döngüsü yönetimi

# 1.2. Agent Sistemi

CoreMusic AI görevleri tek bir genel AI tarafından değil, uzman agent yapısı tarafından yönetilir.

Her agent:

- Belirlenen uzmanlık alanında çalışır.
- Kendi sorumluluk alanı dışına çıkmaz.
- Kritik değişikliklerde onay sürecine uyar.


## 2. Workflow List

| # | Workflow | Amaç | Dosya |
|---|----------|------|-------|
| 1 | Code Review | Çok eksenli inceleme | [[code-review]] |
| 2 | Bug Fix | Kök neden → düzeltme → regresyon | [[bug-fix]] |
| 3 | New Feature | Gereksinim → plan → uygulama → test | [[new-feature]] |
| 4 | Security Audit | OWASP Top 10 uyumluluk | [[security-audit]] |
| 5 | Deployment | Pre-flight → deploy → health check | [[deployment]] |
| 6 | Session Init | Boot protokolü → vault sync | [[session-init]] |
| 7 | Vault Sync | 5 soru → 6 adım | [[vault-sync]] |
| 1 | Master Orchestrator | Görev analizi, koordinasyon, agent seçimi | Coordination |
| 2 | Backend Architect | PHP, API, backend mimarisi | L2 |
| 3 | Frontend/UI Engineer | UI, UX, JavaScript, CSS mimarisi | L3 |
| 4 | Security Engineer | OWASP, güvenlik analizi | L1 |
| 5 | Data Engineer | Database, SQL, veri bütünlüğü | L0 |
| 6 | QA Engineer | Test, doğrulama, kalite kontrol | Cross-cutting |
| 7 | DevOps Engineer | CI/CD, deployment, altyapı | CI/CD |
| 8 | Embedded Engineer | Embedded sistemler, donanım | HW |
| 9 | DSP Firmware Engineer | DSP, firmware, audio processing | FW |
| 10 | Audio Hardware Engineer | DAC, ADC, PCB, audio donanım | HW |
| 11 | Windows Software Engineer | Windows API, driver, WASAPI | Platform |


# 2.1. Agent Sorumluluk Matrisi

| Agent | Sorumluluk |
|---|---|
| Master Orchestrator | Görev dağıtımı ve koordinasyon |
| Backend Architect | Backend mimarisi ve API tasarımı |
| Frontend/UI Engineer | UI sistemi ve frontend geliştirme |
| Security Engineer | Güvenlik inceleme ve risk analizi |
| Data Engineer | Database tasarımı ve veri yönetimi |
| QA Engineer | Test stratejisi ve kalite doğrulama |
| DevOps Engineer | Deployment ve operasyon yönetimi |
| Embedded Engineer | Embedded mimari ve donanım iletişimi |
| DSP Firmware Engineer | DSP algoritmaları ve firmware |
| Audio Hardware Engineer | Audio elektronik sistemleri |
| Windows Software Engineer | Windows platform geliştirme |

# 2.2. Agent Yetki Kuralları

## Agent Yapabilir:

- Teknik analiz yapabilir.
- Öneri oluşturabilir.
- Kod inceleyebilir.
- Test planı hazırlayabilir.
- Teknik rapor oluşturabilir.

## Agent Tek Başına Yapamaz:

- Mimari değiştiremez.
- Güvenlik politikasını değiştiremez.
- Database schema değiştiremez.
- Production davranışını değiştiremez.
- Kritik dosya silemez.

Kritik değişikliklerde:

Gereklidir:

- ADR oluşturma
- Impact Analysis
- Kullanıcı onayı

## 3. Workflow Haritası

```text
┌─────────────────────────────────────────────────┐
│                Workflow Registry                 │
├─────────────────────────────────────────────────┤
│  Code Review → Bug Fix → New Feature            │
│       ↓              ↓              ↓            │
│  Security Audit → Deployment → Session Init     │
│       ↓              ↓              ↓            │
│  Vault Sync → Quality Report → Completion       │
└─────────────────────────────────────────────────┘
```

## 4. Seviye Bazlı Kullanım

| Seviye | Workflow | Kullanım |
|--------|----------|----------|
| **Günlük** | Code Review, Bug Fix | Her geliştirme döngüsü |
| **Haftalık** | New Feature, Security Audit | Periyodik kontrol |
| **Aylık** | Deployment, Session Init | Periyodik operasyon |
| **Sürekli** | Vault Sync | Değişiklik sonrası |

## 5. İlgili Dokümanlar

- [[WORKFLOW.md]] — Ana iş akışları dokümanı
- [[CLAUDE.md]] — Kanonik AI talimatı
- [[AGENTS.md]] — Agent kayıt defteri
- [[brain.md]] — Mimari kararlar
- [[index.md]] — Master katalog

## 6. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 3.0.0 |
| **Toplam Workflow** | 7 |
| **Frontmatter** | ✅ Tamamlandı |
| **Cross-Reference** | ✅ Doğrulandı |

---

*Workflow Registry Index v3.0.0 — CoreMusic Workflow Registry*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-08-08*
*Mode: Red Team · Human Mode · Truth Mode*
