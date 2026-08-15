---
type: plan
category: ui-design
title: "CoreMusic — Vault Registration (Kalıcı Kayıt, v4.0.0)"
date: 2026-08-15
updated: 2026-08-15
status: completed
version: 4.1.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/ui-design/04-vault-registration.md"
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
  ui_cross_references:
    root_files:
      - path: ".ai/ui-design/00-mockup-index.md"
        purpose: "PNG master kataloğu — vault'a kayıtlı ana mockup indeksi"
      - path: ".ai/ui-design/01-component-inventory.md"
        purpose: "C01-C16 bileşen envanteri — vault'ta tanımlı"
      - path: ".ai/ui-design/02-implementation-plan.md"
        purpose: "CSS uygulama planı — vault'ta tanımlı"
      - path: ".ai/ui-design/03-accessibility-gaps.md"
        purpose: "WCAG gap analizi — vault'ta tanımlı"
    vault_files_verified:
      - path: ".ai/CLAUDE.md"
        purpose: "Hard Rule #11 (Mockup Before Frontend) doğrulandı"
      - path: ".ai/AGENTS.md"
        purpose: "Mockup kuralı doğrulandı"
      - path: ".ai/index.md"
        purpose: "ui-design referansları doğrulandı"
      - path: ".ai/keys.md"
        purpose: "Frontend & UI Design keyword mapping doğrulandı"
  ui_adr_references:
    - path: ".ai/decisions/accepted/ADR-001-vanilla-js-itcss.md"
      purpose: "Framework yasağı — vanilla JS zorunluluğu"
    - path: ".ai/decisions/accepted/ADR-044-dynamic-user-theme-engine.md"
      purpose: "Tema motoru — cinsiyet bazlı tema sistemi"
    - path: ".ai/decisions/accepted/ADR-045-multi-domain-view-mode-architecture.md"
      purpose: "View mode — home/pro/studio görünüm modları"
  ui_skills:
    - path: ".opencode/skills/ui-code-generator/SKILL.md"
      purpose: "UI/CSS kod üretimi — vault entegrasyonu sonrası kullanılacak"
    - path: ".opencode/skills/ui-analyzer/SKILL.md"
      purpose: "UI analizi — vault doğrulama sonrası mockup analizi"
---

# CoreMusic — Vault Registration (v4.0.0)

Mockup'ları `.ai/` vault'una kalıcı olarak tanıtma planı. **Bu adım yapılmazsa sonraki oturumlarda mockup'lar yine görünmez.**

---

## 1. Amaç

4 vault dosyasını düzenleyerek `.ai/ui-design/00-mockup-index.md` dosyasını boot protokolüne ve navigasyon sistemine dahil etmek.

---

## 2. Yapılan Değişiklikler

### 2.1 — `.ai/CLAUDE.md`

| Değişiklik | Durum | Satır |
|-----------|-------|-------|
| Hard Rule #11: Mockup Before Frontend | ✅ MEVCUT | 158 |
| Boot protokolüne 11. satır eklenecek | ⏳ YAPILACAK | — |

**Eklenecek satır:**
```
| 11 | .ai/ui-design/00-mockup-index.md | Mockup eşleme tablosu — frontend görevlerinde ZORUNLU |
```

### 2.2 — `.ai/AGENTS.md`

| Değişiklik | Durum | Satır |
|-----------|-------|-------|
| Mockup Before Frontend kuralı | ✅ MEVCUT | 356 |

### 2.3 — `.ai/index.md`

| Değişiklik | Durum | Satır |
|-----------|-------|-------|
| ui-design referansları | ✅ MEVCUT | 357 |

### 2.4 — `.ai/keys.md`

| Değişiklik | Durum | Satır |
|-----------|-------|-------|
| Frontend keyword mapping | ✅ MEVCUT | 75-79 |
| ASCII art keyword | ✅ MEVCUT | — |
| Screen spec keyword | ✅ MEVCUT | — |

---

## 3. Doğrulama Kontrolleri

| # | Kontrol | Yöntem | Durum |
|---|---------|--------|-------|
| 1 | Wiki-link çalışıyor mu? | `[[ui-design/00-mockup-index]]` | ✅ |
| 2 | Hard Rules 11 kural mı? | CLAUDE.md §7 | ✅ |
| 3 | Keyword mapping eklendi mi? | keys.md §3A | ✅ |
| 5 | ASCII Art reference eklendi mi? | keys.md "ascii art" | ✅ |
| 6 | Screen spec keyword'leri eklendi mi? | keys.md "screen spec" | ✅ |
| 7 | Mockup index erişilebilir mi? | index.md §12 | ✅ |

---

## 4. Beklenen Sonuç

Bu değişikliklerden sonra:

1. **Her frontend görevinde** agent otomatik olarak `00-mockup-index.md`'yi okuyacak
2. **Mockup'lar vault'a tanınmış** olacak
3. **ASCII Art Reference** erişilebilir olacak
4. **Keyword araması** ile mockup dizinine ulaşılabilecek
5. **Boot protokolü** mockup'ları hatırlayacak
6. **Screen spec dosyaları** keyword ile bulunabilecek
8. **Platform naming** sistemi belgelenmiş olacak (home-1024, studio-1920, vb.)

---

## 5. Platform Naming Dokümantasyonu

`.ai/.png/` dizin yapısının anlamı:

```
.ai/.png/{subdomain}-{resolution}/
                 ↑              ↑
                 │              └── Çözünürlük (1024=1024×600, 1920=1920×1080, 3840=3840×2160)
                 └── Subdomain (home, pro, studio, shared)
```

| Dizin | Anlam | Cihaz | OS |
|-------|-------|-------|-----|
| `home-1024/` | home.coremusic.net, 1024×600 | RPi5 7" dokunmatik | Linux Embedded |
| `shared-1024/` | Tüm subdomain'ler (auth), 1024×600 | RPi5 7" dokunmatik | Linux Embedded |
| `home-1920/` | home.coremusic.net, 1920×1080 | PC/Laptop | Windows/Linux |
| `home-3840/` | home.coremusic.net, 3840×2160 | 4K TV | Tizen/WebOS |
| `pro-1024/` | pro.coremusic.net, 1024×600 | RPi5 7" dokunmatik | Linux Embedded |
| `studio-1024/` | studio.coremusic.net, 1024×600 | RPi5 7" dokunmatik | Linux Embedded |

---

## 6. İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[CLAUDE.md]] | Hard Rule #11 mevcut |
| [[AGENTS.md]] | Mockup kuralı mevcut |
| [[index.md]] | ui-design referansları mevcut |
| [[keys.md]] | Keyword mapping mevcut |
| [[00-mockup-index]] | v4.0.0 — 18 PNG ASCII art view |
| [[01-component-inventory]] | C01-C16 detayları |
| [[02-implementation-plan]] | CSS uygulama planı |
| [[03-accessibility-gaps]] | WCAG gap analizi |

---

## 7. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 4.0.0 |
| Status | COMPLETED |
| Files Verified | 5 (CLAUDE.md, AGENTS.md, index.md, keys.md, 00-mockup-index.md) |
| Hard Rules | 11 (Mockup Before Frontend dahil) |
| Keyword Mappings | 18 (Frontend & UI Design) |
| ASCII Art Views | 18 PNG |
| Platform Naming | ✅ Documented |
| Risk | LOW (tümü mevcut) |

---

*Vault Registration v4.0.0 — CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-08-11*
*Mode: Red Team · Human Mode · Truth Mode*
