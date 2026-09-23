---
title: "CoreMusic — Agent Alt-Registry (Profiles)"
type: system
category: agent-registry
version: 1.1.0
status: active
updated: 2026-09-23

authority: "Alt Registry — SSOT: .ai/AGENTS.md (v22.0.0)"

governance:
  - Red Team
  - Human Mode
  - Truth Mode

reference:
  authority: ".ai/AGENTS.md"

  source_of_truth:
    - ".ai/CLAUDE.md"
    - ".ai/WORKFLOW.md"
    - ".ai/brain.md"
    - ".ai/index.md"
---

# CoreMusic — Agent Alt-Registry (Profiles)

> **SSOT Uyarısı:** Bu dosya alt registry'dir. Asıl SSOT: [[../AGENTS.md]] (v22.0.0). Çelişkide kök dosya kazanır; bu dosya yalnızca ajan profil/özet detaylarını içerir.

## 1. Amaç

Bu dosya CoreMusic AI agent sisteminin ana yönetim dosyasıdır.

Sorumlulukları:
- Agent kayıt yönetimi
- Agent yetki sınırları
- Domain routing
- Task dispatch
- Handover yönetimi
- Escalation yönetimi
- Agent koordinasyonu

Bu dosya alt registry'dir; **SSOT: [[../AGENTS.md]]** (v22.0.0) — çelişkide kök dosya kazanır.
---

# 2. Agent Mimarisi

CoreMusic agent sistemi:
                  ┌─────────────────────┐
                  │  MASTER ORCHESTRATOR│
                  │  System Intelligence│
                  └──────────┬──────────┘
                             │
     ┌────────────────┬──────┼──────────────────────┐
     │                │              │              │                
┌────▼────┐     ┌─────▼────┐   ┌─────▼─────┐  ┌─────▼────┐
│ Backend │     │    UI    │   │ Security  │  │  Data    │
│ Service │     │ UX Layer │   │ Protection│  │ Platform │
└────┬────┘     └─────┬────┘   └─────┬─────┘  └─────┬────┘
     │                │              │              │
     └────────────────┴──────────────┴──────────────┘
                              │
                 ┌────────────▼────────────┐
                 │        ENGINEERING      │
                 └────────────┬────────────┘
                              │
                ┌───────────┬─▼────────┬────────────┐
                │           │          │            │          
            ┌───▼───┐ ┌─────▼────┐ ┌───▼─────┐ ┌────▼───┐
            │  QA   │ │ DevOps   │ │Embedded │ │Hardware│
            │Test   │ │Infra CI  │ │Firmware │ │System  │
            └───┬───┘ └─────┬────┘ └───┬─────┘ └────┬───┘
                │           │          │            │
                └───────────┴──────────┴────────────┘
                                  │
                        ┌─────────▼─────────┐
                        │ Platform/Firmware │
                        └─────────┬─────────┘
                                  │
            ┌─────────────────────┼─────────────────────┐
            │                     │                     │
       ┌────▼────┐          ┌─────▼────┐          ┌─────▼─────┐
       │ Kernel  │          │ Drivers  │          │ Bootloader│
       └────┬────┘          └─────┬────┘          └─────┬─────┘
            │                     │                     │
            └─────────────────────┼─────────────────────┘
                                  │
                        ┌─────────▼─────────┐
                        │ Hardware Layer    │
                        │ HAL / BSP         │
                        └─────────┬─────────┘
                                  │
            ┌─────────────┬───────┼────────┬─────────────┐
            │             │       │        │             │
         ┌──▼──┐      ┌───▼──┐ ┌──▼──┐  ┌──▼───┐     ┌───▼───┐
         │ CPU │      │ GPU  │ │ MCU │  │ DSP  │     │Sensor │
         └──┬──┘      └───┬──┘ └──┬──┘  └──┬───┘     └───┬───┘
            │             │       │        │             │
            └─────────────┴───────┴────────┴─────────────┘
                                  │
                        ┌─────────▼─────────┐
                        │ Physical Hardware │
                        │ Device / Product  │
                        └───────────────────┘
---

# 3. Agent Listesi


| ID | Agent | Kod | Domain |
|----|-------|-----|--------|
| 01 | Master Orchestrator | MO | Coordination |
| 02 | Backend Architect | backend | PHP/API |
| 03 | UI Designer | ui | Frontend/UI |
| 04 | Security Engineer | security | Security |
| 05 | Database Engineer | data | Database |
| 06 | DevOps Engineer | devops | Infrastructure |
| 07 | QA Engineer | qa | Testing |
| 08 | Embedded Engineer | embedded | C++/Audio |
| 09 | DSP Firmware Engineer | dsp-fw | DSP/Firmware |
| 10 | Audio Hardware Engineer | audio-hw | Electronics |
| 11 | Windows Software Engineer | win-sw | Windows Platform |
---

# 4. Domain Boundary
Her agent sadece kendi alanında çalışır.

## Kural
Bir agent başka agent domain dosyasını değiştiremez.

---
# 5. Task Routing

Görev geldiğinde:

INPUT
|
|
Keyword Analysis
|
|
Agent Selection
|
|
Domain Check
|
|
Context Lock
|
|
Execute
|
|
Validation
|
|
Log

---

# 6. Keyword Routing

| Keyword | Agent |
|-|-|
| PHP, API, Controller, Middleware | backend |
| JS, CSS, UI, Responsive | ui |
| CSRF, CSP, Auth, Encryption | security |
| SQL, Database, Schema | data |
| Docker, CI/CD, Deploy | devops |
| Test, Coverage, QA | qa |
| C++, JUCE, ASIO, DSP | embedded |
| XMOS, Firmware | dsp-fw |
| DAC, PCB, Amplifier | audio-hw |
| Windows, Driver, WASAPI | win-sw |

---

# 7. Task Lifecycle

Her görev:

PENDING
↓
ANALYSIS
↓
PLAN
↓
APPROVED
↓
IMPLEMENTATION
↓
TEST
↓
VALIDATION
↓
COMPLETED
↓
LOGGED

---

# 8. Priority

| Level | Kullanım |
|-------|----------|
| CRITICAL | Security, data loss, production failure |
| HIGH | Architecture problem |
| MEDIUM | Feature / improvement |
| LOW | Documentation / cosmetic |

---

# 9. Hard Rules

## Zero Code Before Plan
Kod yazmadan önce:

- Gereksinim analiz edilir
- Mimari kontrol edilir
- Etkilenen dosyalar belirlenir

---

## Zero Hallucination
Bilinmeyen bilgi: **VERIFICATION REQUIRED** olarak işaretlenir. **Tahmin yapılmaz.**

---

## No Architecture Bypass
Yasak:
UI
|
Database

Doğru:
UI
↓
API
↓
Service
↓
Database
---

# 10. Handover Protocol

Agent başka domain ihtiyacı olduğunda:

HANDOVER REQUEST

FROM:
[current agent]

TO:
[target agent]

TASK:
[description]

REASON:
[why]

FILES:
[affected files]

STATUS:
[current state]

VALIDATION:
[test criteria]

PRIORITY:
[level]
---

# 11. Context Lock

Aynı dosya üzerinde:

2 agent aynı anda çalışamaz.

Lock:
LOCKED

Agent:
[file owner]

Reason:
[task]

Time:
[timestamp]
---

# 12. Failure Protocol

Sorun oluşursa:
STATUS: BLOCKED

REASON:
[problem]

AFFECTED AREA:
[file/domain]

REQUIRED ACTION:
[action]

ESCALATION:
[level]

---

# 13. Quality Gate

Görev kapanmadan önce:
Kontrol:

[ ] Kod çalışıyor
[ ] Test geçti
[ ] Security kontrol edildi
[ ] Architecture uygun
[ ] Documentation güncel
[ ] Log oluşturuldu

---

# 14. Agent Files

Detay profilleri:
.ai/.agents/

├── master-orchestrator.md
├── backend-architect.md
├── frontend-ui-designer.md
├── security-engineer.md
├── database-engineer.md
├── devops-engineer.md
├── qa-engineer.md
├── embedded-engineer.md
├── dsp-firmware-engineer.md
├── audio-hardware-engineer.md
└── windows-software-engineer.md

---

# 15. Version History


| Version | Date | Change |
|-|-|-|
| 1.0.0 | 2026-09-23 | Initial Agent System |
| 1.1.0 | 2026-09-23 | Alt registry'ye indirgendi (SSOT: .ai/AGENTS.md v22.0.0); 7 alanlı frontmatter + SSOT uyarısı |