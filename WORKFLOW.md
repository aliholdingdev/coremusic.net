---
title: "CoreMusic AI Engineering WORKFLOW.MD"
type: workflow-instruction
authority: SSOT
mode:
  - Red Team
  - Truth Mode
  - Human Mode

purpose:
  - AI Session Initialization
  - Workflow Governance
  - Vault Management
  - Document Lifecycle Control
  - Architecture Change Process

reference:
  authority: ".ai/WORKFLOW.md"

  source_of_truth:
    - ".ai/CLAUDE.md"
    - ".ai/AGENTS.md"
    - ".ai/WORKFLOW.md"
    - ".ai/brain.md"
    - ".ai/index.md"
    - ".ai/keys.md"
    - ".ai/MEMORY.md"
    - ".ai/log.md"
    - ".ai/engine.md"

  workflows:
    - ".workflows/session-init.md"
    - ".workflows/adr-creation.md"
    - ".workflows/vault-sync.md"
    - ".workflows/security-audit.md"
    - ".workflows/deployment.md"
    - ".workflows/hallucination-control.md"

  architecture:
    - ".ai/ADR/"
    - "Existing project architecture"
    - "Existing workflow definitions"

  process_priority:
    - "User approval"
    - "Architecture decisions"
    - "Security requirements"
    - "Workflow rules"
    - "Documentation consistency"

  update_policy:
    preserve_existing_structure: true

    require_approval_for:
      - "workflow change"
      - "process change"
      - "automation change"
      - "vault structure change"
      - "document lifecycle change"

  mandatory_rules:
    - "Read workflow context before execution"
    - "Log vault changes"
    - "Use vault-sync after vault modifications"
    - "Never bypass approval gates"

  skills:
    - path: ".opencode/skills/ui-code-generator/SKILL.md"
      purpose: "UI/CSS kod üretimi, responsive tasarım"
    - path: ".opencode/skills/ui-analyzer/SKILL.md"
      purpose: "UI analizi, tasarım değerlendirme"
    - path: ".opencode/skills/skill-maker/SKILL.md"
      purpose: "Skill oluşturma, template sistemi"
    - path: ".opencode/skills/red-team-truth-mode/SKILL.md"
      purpose: "Güvenlik testi, adversarial analiz"
    - path: ".opencode/skills/prompt-maker/SKILL.md"
      purpose: "Prompt mühendisliği, AI talimat tasarımı"
    - path: ".opencode/skills/composer-sync/SKILL.md"
      purpose: "Composer dependency yönetimi"
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
        purpose: "ADR şablonu"
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
      - Initial Workflow Constitution
      - Added Session Boot Protocol
      - Added Vault Refactoring Process
      - Added Workflow Governance Rules
---
# WORKFLOW.md

**⚠️ CRITICAL: This file is loaded at the start of EVERY session. Follow the boot protocol below.**

**Bu dosya bir POINTER FILE'dır — ADR-042 (2026-08-03)**

**Root `WORKFLOW.md` artık bir bootstrap pointer dosyasıdır.  
Canonical workflow'lar `.ai/WORKFLOW.md` içerisinde tutulmaktadır.**
**workflow'lar `.workflows` klasörü içerisinde tutulmaktadır.**

## 10 Adımlı Başlatma Protokolü (Mandatory — Her Session Başında)

**ZORUNLULUK:** Her AI asistanı her oturumda aşağıdaki 9 dosyayı OKUMALIDIR. Bu dosyaları okumadan HİÇBİR İŞLEM YAPMA.

| # | Dosya | Amaç | Timeout |
|---|-------|------|---------|
| 1 | `.ai/CLAUDE.md` | AI anayasası, 16 Hard Guardrails | 3s |
| 2 | `.ai/AGENTS.md` | Agent sınırları, routing, domain boundary | 3s |
| 3 | `.ai/WORKFLOW.md` | Süreçler, fazlar, workflow kuralları | 3s |
| 4 | `.ai/index.md` | Master katalog, tüm vault yapısı | 4s |
| 5 | `.ai/keys.md` | Keyword haritası, yönlendirme | 3s |
| 6 | `.ai/brain.md` | Mimari kararlar, ADR 001-087 | 4s |
| 7 | `.ai/MEMORY.md` | Session hafızası, persistent state | 3s |
| 8 | `.ai/log.md` | Audit trail (son 20 satır) | 2s |
| 9 | `.ai/engine.md` | Orkestrasyon motoru indeksi | 2s |

**Toplam boot süresi:** Max 30 saniye.

**Kural:** Bu dosyaları okumadan kod yazma, plan yapma veya herhangi bir işlem başlatma. İlk adım HER ZAMAN vault okumaktır.

## 12 Aşamalı Vault Refactoring (Summary)

```text
1. Repository Discovery
   Repository Keşfi

2. AI Knowledge Discovery
   AI Bilgi Keşfi

3. Existing Markdown Analysis
   Mevcut Markdown Analizi

4. Conflict Detection
   Çakışma Tespiti

5. Duplicate Detection
   Tekrar Eden İçerik Tespiti

6. Gap Detection
   Eksik Alan Tespiti

7. -> Improvement Proposal -> WAIT USER APPROVAL
   -> İyileştirme Önerisi -> KULLANICI ONAYI BEKLE

8. Document Refactoring (In-Place)
   Doküman Refactoring (Yerinde)

9. Cross Reference Update
   Çapraz Referans Güncelleme

10. Index Update
    Index Güncelleme

11. Validation
    Doğrulama

12. Quality Report & Vault Sync
    Kalite Raporu ve Vault Senkronizasyonu
```

# Workflow Durum Takibi (Workflow State)

Her workflow aşağıdaki durumları takip etmelidir:

- Pending (Beklemede)
- Approved (Onaylandı)
- Running (Çalışıyor)
- Completed (Tamamlandı)
- Failed (Başarısız)

## 4 Temel Kurallar (Süreç)

1. **Kullanıcı Onay Kapısı:** İyileştirme önerisi veya mimari plan, kod/dokümantasyon revizyonu başlamadan önce onaylanmalıdır.
2. **Yerinde Değişiklik:** Doküman güncellemeleri aynı dosya üzerinde yapılmalıdır; onay olmadan dosya adı/konumu DEĞİŞTİRİLEMEZ.
3. **Halüsinasyon Yok:** Doğrulanamayan bilgiler UYDURULMAMALIDIR; bunun yerine `**VERIFICATION REQUIRED**` yazılmalıdır.
4. **Skill Zorunluluğu:** Vault değişikliği varsa, her oturum sonunda `vault-sync` zorunludur. Tüm işlemler `.ai/log.md` içerisine kaydedilmelidir.

## Workflow Dosyaları (`.workflows/`)

| Workflow | Amaç |
|----------|---------|
| `session-init.md` | Yeni oturum başlatma |
| `adr-creation.md` | ADR oluşturma |
| `vault-sync.md` | Vault senkronizasyonu |
| `security-audit.md` | Güvenlik denetimi |
| `deployment.md` | Dağıtım süreci |
| `hallucination-control.md` | Halüsinasyon kontrolü |


**Yetki:** Bayram Ali / Vault Steward  
**Son Güncelleme:** 2026-08-04  
**Mod:** Red Team + Human Mode + Truth Mode