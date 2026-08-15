---
title: "CoreMusic AI Engineering AGENTS.MD"
type: agent-registry
authority: SSOT
mode:
  - Red Team
  - Truth Mode
  - Human Mode
purpose:
  - Agent Registry & Routing
  - Task Dispatch
  - Handover Protocol
  - Escalation Rules
reference:
  authority: ".ai/AGENTS.md"
  source_of_truth:
    - ".ai/AGENTS.md"
    - ".ai/.agents/AGENTS.md"
    - ".ai/CLAUDE.md"
    - ".ai/WORKFLOW.md"
    - ".ai/brain.md"
    - ".ai/index.md"
    - ".ai/keys.md"
    - ".ai/MEMORY.md"
    - ".ai/log.md"
    - ".ai/engine.md"

  architecture:
    - ".ai/ADR/"
    - "Existing project architecture"
    - "Existing codebase patterns"

  project_structure:
    - "coremusic.net/"
    - "shared/"
    - "api.coremusic.net/"
    - "auth.coremusic.net/"
    - "music.coremusic.net/"
    - "admin.coremusic.net/"
    - "home.coremusic.net/"
    - "car.coremusic.net/"
    - "studio.coremusic.net/"
    - "pro.coremusic.net/"
    - "media.coremusic.net/"
    - "download.coremusic.net/"

  decision_priority:
    - "ADR decisions"
    - "Architecture documentation"
    - "Security requirements"
    - "Existing implementation"
    - "User requirements"

  update_policy:
    preserve_existing_structure: true
    require_approval_for:
      - "file rename"
      - "directory move"
      - "architecture change"
      - "database schema change"
      - "security policy change"

  skills:
    - path: ".opencode/skills/ui-code-generator/SKILL.md"
      purpose: "UI/CSS kod üretimi, responsive tasarım, WCAG erişilebilirlik"
    - path: ".opencode/skills/ui-analyzer/SKILL.md"
      purpose: "UI analizi, mevcut tasarım değerlendirme"
    - path: ".opencode/skills/skill-maker/SKILL.md"
      purpose: "Yeni skill oluşturma, skill template sistemi"
    - path: ".opencode/skills/red-team-truth-mode/SKILL.md"
      purpose: "Güvenlik testi, truth mode, adversarial analiz"
    - path: ".opencode/skills/prompt-maker/SKILL.md"
      purpose: "Prompt mühendisliği, AI talimat tasarımı"
    - path: ".opencode/skills/composer-sync/SKILL.md"
      purpose: "Composer dependency yönetimi, vendor senkronizasyonu"
    - path: ".opencode/skills/agent-orchestrator/SKILL.md"
      purpose: "Agent görev dağıtımı, multi-agent koordinasyonu"
    - path: ".opencode/skills/human-mode/SKILL.md"
      purpose: "İnsan modu iletişimi, onay süreçleri"
    - path: ".opencode/skills/hallucination-control/SKILL.md"
      purpose: "Halüsinasyon kontrolü, doğrulama protokolleri"
    - path: ".opencode/skills/database-normalize-maker/SKILL.md"
      purpose: "BCNF normalizasyonu, şema tasarımı"

  templates:
    adr:
      - path: ".ai/.templates/adr/adr-template.md"
        purpose: "Architecture Decision Record şablonu"
      - path: ".ai/.templates/adr/adr-frontend-template.md"
        purpose: "Frontend ADR şablonu"
      - path: ".ai/.templates/adr/adr-database-template.md"
        purpose: "Database ADR şablonu"
      - path: ".ai/.templates/adr/adr-security-template.md"
        purpose: "Security ADR şablonu"
      - path: ".ai/.templates/adr/adr-audio-template.md"
        purpose: "Audio/Hardware ADR şablonu"
      - path: ".ai/.templates/adr/adr-index.md"
        purpose: "ADR navigasyon rehberi"
    backend:
      - path: ".ai/.templates/backend/php-template.md"
        purpose: "PHP 8.4 backend geliştirme şablonu"
      - path: ".ai/.templates/backend/nodejs-template.md"
        purpose: "Node.js 20+ backend geliştirme şablonu"
    frontend:
      - path: ".ai/.templates/frontend/js-template.md"
        purpose: "Vanilla JS ES6+ frontend geliştirme şablonu"
      - path: ".ai/.templates/frontend/css-template.md"
        purpose: "ITCSS 9-layer, BEM CSS şablonu"
    testing:
      - path: ".ai/.templates/testing/phpunit-template.md"
        purpose: "PHPUnit 10+ test şablonu"
      - path: ".ai/.templates/testing/vitest-template.md"
        purpose: "Vitest JS/TS test şablonu"
    infrastructure:
      - path: ".ai/.templates/infrastructure/migration-template.md"
        purpose: "MySQL 9 BCNF migration şablonu"
      - path: ".ai/.templates/infrastructure/docker-template.md"
        purpose: "Docker 24+ Compose v2 şablonu"
      - path: ".ai/.templates/infrastructure/github-actions-template.md"
        purpose: "GitHub Actions CI/CD şablonu"
    documentation:
      - path: ".ai/.templates/documentation/api-doc-template.md"
        purpose: "API dokümantasyon şablonu"
      - path: ".ai/.templates/documentation/security-audit-template.md"
        purpose: "Güvenlik denetimi şablonu"
      - path: ".ai/.templates/documentation/WikiPage-Template.md"
        purpose: "Wiki sayfası şablonu"
    hardware:
      - path: ".ai/.templates/hardware/arduino-template.md"
        purpose: "Arduino/IoT prototipleme şablonu"
      - path: ".ai/.templates/hardware/avr-template.md"
        purpose: "AVR mikrodenetleyici şablonu"
      - path: ".ai/.templates/hardware/pic-template.md"
        purpose: "PIC mikrodenetleyici şablonu"
    query:
      - path: ".ai/.templates/query/Query-Template.md"
        purpose: "SQL sorgu şablonu"
    other:
      - path: ".ai/.templates/other/c-template.md"
        purpose: "C11 GCC embedded/driver şablonu"
      - path: ".ai/.templates/cpp-template.md"
        purpose: "C++20 JUCE/ASIO şablonu"

changelog:
  - version: 1.0
    date: 2026-08-12
    changes:
      - Initial Agent Registry
      - Added Skills & Templates References
      - Added Routing Rules
---

# AGENTS.md

**Bu dosya bir POINTER FILE'dır — ADR-042 (2026-08-03)**

**Root `AGENTS.md` artık bir bootstrap pointer dosyasıdır.  
Canonical agent registry (ana agent kayıt sistemi) `.ai/AGENTS.md` içerisinde tutulmaktadır.**


## Hızlı Referans (Quick Reference)

| Bilgi | Kaynak |
|------|--------|
| Agent tanımları | [[.ai/AGENTS.md]] |
| Agent profilleri | [[.ai/.agents/AGENTS.md]] |
| Workflow tanımları | [[.ai/WORKFLOW.md]] |
| Mimari kararlar | [[.ai/brain.md]] |
| AI talimatları | [[.ai/CLAUDE.md]] |
| Ana katalog | [[.ai/index.md]] |
| Skills | [[.opencode/skills/*/SKILL.md]] |
| Templates | [[.ai/.templates/index.md]] |

# 11 CoreMusic Agent

| # | Agent | Alan | Katman |
|---|---|---|---|
| 1 | Master Orchestrator | Görev dağıtımı, koordinasyon | Coordination |
| 2 | Backend Architect | PHP 8.4 API, routing | L2 |
| 3 | UI Designer | Vanilla JS, ITCSS, CSS | L3 |
| 4 | Security Engineer | OWASP, CSRF, CSP | L1 |
| 5 | Data Engineer | MySQL 9 BCNF, PDO | L0 |
| 6 | Embedded Engineer | C++20, JUCE, ASIO | L0 |
| 7 | QA Engineer | PHPUnit, Vitest, Playwright | Cross-cutting |
| 8 | DevOps Engineer | CI/CD, Docker, deployment | CI/CD |
| 9 | Audio Hardware Engineer | DAC/ADC, PCB, amplifier | HW |
| 10 | DSP Firmware Engineer | XMOS, PCM3168A | FW |
| 11 | Windows Software Engineer | WASAPI, driver | PLAT |

# Workflow Durum Takibi (Workflow State)

Her workflow aşağıdaki durumları takip etmelidir:

- Pending (Beklemede)
- Approved (Onaylandı)
- Running (Çalışıyor)
- Completed (Tamamlandı)
- Failed (Başarısız)

# Agent İletişim Protokolü (Agent Communication Protocol)

Her agent aktarımı (handoff) aşağıdaki bilgileri içermelidir:

- Context (Bağlam)
- Current State (Mevcut Durum)
- Requested Action (Talep Edilen İşlem)
- Constraints (Kısıtlamalar)
- Expected Output (Beklenen Çıktı)

# ADR Zorunluluğu (ADR Enforcement)

Her mimari değişiklik aşağıdakileri gerektirir:

- ADR oluşturma veya güncelleme
- Etki analizi (Impact Analysis)
- Onay süreci

# Detaylar:
[[.ai/AGENTS.md]]
[[.ai/.agents/AGENTS.md]]

# Related Skills & Templates

## Skills (10)

| Skill | Amaç | Yol |
|-------|------|-----|
| ui-code-generator | UI/CSS kod üretimi, responsive | [[.opencode/skills/ui-code-generator/SKILL.md]] |
| ui-analyzer | UI analizi, tasarım değerlendirme | [[.opencode/skills/ui-analyzer/SKILL.md]] |
| skill-maker | Skill oluşturma, template sistemi | [[.opencode/skills/skill-maker/SKILL.md]] |
| red-team-truth-mode | Güvenlik testi, adversarial analiz | [[.opencode/skills/red-team-truth-mode/SKILL.md]] |
| prompt-maker | Prompt mühendisliği, AI talimat | [[.opencode/skills/prompt-maker/SKILL.md]] |
| composer-sync | Composer dependency yönetimi | [[.opencode/skills/composer-sync/SKILL.md]] |
| agent-orchestrator | Agent görev dağıtımı | [[.opencode/skills/agent-orchestrator/SKILL.md]] |
| human-mode | İnsan modu, onay süreçleri | [[.opencode/skills/human-mode/SKILL.md]] |
| hallucination-control | Halüsinasyon kontrolü | [[.opencode/skills/hallucination-control/SKILL.md]] |
| database-normalize-maker | BCNF normalizasyonu | [[.opencode/skills/database-normalize-maker/SKILL.md]] |

## Templates (25)

| Kategori | Adet | Yol |
|----------|------|-----|
| ADR | 6 | [[.ai/.templates/adr/]] |
| Backend | 2 | [[.ai/.templates/backend/]] |
| Frontend | 2 | [[.ai/.templates/frontend/]] |
| Testing | 2 | [[.ai/.templates/testing/]] |
| Infrastructure | 3 | [[.ai/.templates/infrastructure/]] |
| Documentation | 3 | [[.ai/.templates/documentation/]] |
| Hardware | 3 | [[.ai/.templates/hardware/]] |
| Query | 1 | [[.ai/.templates/query/]] |
| Other | 3 | [[.ai/.templates/other/]] |

Detaylar: [[.ai/.templates/index.md]]

# Yetki (Authority)
**Yetkili:** Bayram Ali / Vault Steward
**Son Güncelleme:** 2026-08-09
**Mod:** Red Team, Human Mode, Truth Mode