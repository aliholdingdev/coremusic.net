---
type: template-index
category: template
title: "CoreMusic — Template Registry Index"
date: 2026-08-09
updated: 2026-08-13
status: active
version: 3.3.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
total_templates: 25
total_lines: 22786
---

# CoreMusic — Template Registry Index

**See also:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[ui-design/00-mockup-index]]

**Skills:** `.opencode/skills/` (10 skill — Guardrail #16 zorunlu)
**Agents:** `.ai/.agents/` (11 agent profile)
**UI Design:** `.ai/ui-design/` (100+ dosya) + `.ai/.png/` (18 PNG mockup)

## 1. Amaç

Bu dosya, CoreMusic ekosistemindeki tüm şablonların (template) merkezi indeksidir. Her şablonun amacını, teknoloji yığınını ve kullanım alanını tanımlar.

**⚠️ ZORUNLULUK (Guardrail #16):** Yeni dosya oluşturulurken bu listeden uygun template seçilmek ZORUNLU. Template olmadan dosya oluşturulamaz. AI ve insan geliştiriciler için aynı kural geçerlidir.

## 2. Template Dizin Yapısı

```
.templates/
├── index.md                    # Bu dosya
├── adr/                        # ADR şablonları
│   ├── adr-template.md
│   ├── adr-frontend-template.md
│   ├── adr-database-template.md
│   ├── adr-security-template.md
│   ├── adr-audio-template.md
│   └── adr-index.md
├── backend/                    # Backend şablonları
│   ├── php-template.md
│   └── nodejs-template.md
├── frontend/                   # Frontend şablonları
│   ├── js-template.md
│   └── css-template.md
├── testing/                    # Test şablonları
│   ├── phpunit-template.md
│   └── vitest-template.md
├── infrastructure/             # Altyapı şablonları
│   ├── migration-template.md
│   ├── docker-template.md
│   └── github-actions-template.md
├── documentation/              # Dokümantasyon şablonları
│   ├── api-doc-template.md
│   ├── security-audit-template.md
│   └── WikiPage-Template.md
├── hardware/                   # Donanım şablonları
│   ├── arduino-template.md
│   ├── avr-template.md
│   └── pic-template.md
├── query/                      # Sorgu şablonları
│   └── Query-Template.md
└── other/                      # Diğer şablonlar
    ├── aspnet-template.md
    └── c-template.md
```

## 3. Template List

### 3.1 ADR Templates (adr/)

| # | Template | Amaç | Satır | Dosya |
|---|----------|------|-------|-------|
| 1 | ADR Template | Architecture Decision Record | 502 | [[adr/adr-template]] |
| 2 | ADR Frontend Template | Frontend ADR | 574 | [[adr/adr-frontend-template]] |
| 3 | ADR Database Template | Database ADR | 514 | [[adr/adr-database-template]] |
| 4 | ADR Security Template | Security ADR | 681 | [[adr/adr-security-template]] |
| 5 | ADR Audio Template | Audio/Hardware ADR | 619 | [[adr/adr-audio-template]] |
| 6 | ADR Index | ADR navigation guide | 315 | [[adr/adr-index]] |

### 3.2 Backend Templates (backend/)

| # | Template | Teknoloji | Amaç | Satır | Dosya |
|---|----------|-----------|------|-------|-------|
| 7 | PHP Template | PHP 8.4, strict_types | Backend development | 815 | [[backend/php-template]] |
| 8 | Node.js Template | Node.js 20+, TypeScript 5+ | Download service | 1245 | [[backend/nodejs-template]] |

### 3.3 Frontend Templates (frontend/)

| # | Template | Teknoloji | Amaç | Satır | Dosya |
|---|----------|-----------|------|-------|-------|
| 9 | JavaScript Template | Vanilla JS ES6+ | Frontend development | 966 | [[frontend/js-template]] |
| 10 | CSS Template | ITCSS 9-layer, BEM | Stylesheet development | 819 | [[frontend/css-template]] |

### 3.4 Testing Templates (testing/)

| # | Template | Teknoloji | Amaç | Satır | Dosya |
|---|----------|-----------|------|-------|-------|
| 11 | PHPUnit Template | PHPUnit 10+, PHP 8.4 | PHP unit testing | 1380 | [[testing/phpunit-template]] |
| 12 | Vitest Template | Vitest, happy-dom | JS/TS unit testing | 1372 | [[testing/vitest-template]] |

### 3.5 Infrastructure Templates (infrastructure/)

| # | Template | Teknoloji | Amaç | Satır | Dosya |
|---|----------|-----------|------|-------|-------|
| 13 | Migration Template | MySQL 9, BCNF | Database migration | 1349 | [[infrastructure/migration-template]] |
| 14 | Docker Template | Docker 24+, Compose v2 | Container build | 1482 | [[infrastructure/docker-template]] |
| 15 | GitHub Actions Template | GH Actions, CI/CD | Pipeline automation | 1466 | [[infrastructure/github-actions-template]] |

### 3.6 Documentation Templates (documentation/)

| # | Template | Format | Amaç | Satır | Dosya |
|---|----------|--------|------|-------|-------|
| 16 | API Doc Template | Markdown, OpenAPI 3.1 | API dokümantasyonu | 1693 | [[documentation/api-doc-template]] |
| 17 | Security Audit Template | Markdown, OWASP | Güvenlik denetimi | 954 | [[documentation/security-audit-template]] |
| 18 | Wiki Page Template | Markdown, Mermaid | Wiki sayfası | 974 | [[documentation/WikiPage-Template]] |

### 3.7 Hardware Templates (hardware/)

| # | Template | Teknoloji | Amaç | Satır | Dosya |
|---|----------|-----------|------|-------|-------|
| 19 | Arduino Template | Arduino CLI, C++17 | Arduino/IoT prototyping | 1511 | [[hardware/arduino-template]] |
| 20 | AVR Template | AVR-GCC, avr-libc | AVR microcontroller | 1133 | [[hardware/avr-template]] |
| 21 | PIC Template | XC8, MPLAB X | PIC microcontroller | 1334 | [[hardware/pic-template]] |

### 3.8 Query Templates (query/)

| # | Template | Format | Amaç | Satır | Dosya |
|---|----------|--------|------|-------|-------|
| 22 | Query Template | SQL, MySQL 9 | Veritabanı sorguları | 1402 | [[query/Query-Template]] |

### 3.9 Other Templates (other/)

| # | Template | Teknoloji | Amaç | Satır | Dosya |
|---|----------|-----------|------|-------|-------|
| 23 | ASP.NET Template | ASP.NET 9, C# 13 | Enterprise backend | 1331 | [[other/aspnet-template]] |
| 24 | C Template | C11, GCC | Embedded, drivers | 933 | [[other/c-template]] |

## 4. Kullanım Kılavuzu

### 4.1 Template Seçimi

```text
Yeni dosya oluştururken:
1. İlgili kategoriden template'i seç (adr/, backend/, frontend/, vb.)
2. Template'i kopyala
3. Değişkenleri doldur ({{VARIABLE}} formatında)
4. Gereksiz bölümleri kaldır
5. Ek bölümler ekle (gerekirse)
```

### 4.2 Template Değişkenleri

| Değişken | Açıklama | Örnek |
|----------|----------|-------|
| `{{PROJECT_NAME}}` | Proje adı | CoreMusic |
| `{{AUTHOR}}` | Yazar | Bayram Ali |
| `{{VERSION}}` | Versiyon | 3.0.0 |
| `{{DATE}}` | Tarih | 2026-08-09 |
| `{{DESCRIPTION}}` | Kısa açıklama | Backend API service |
| `{{TECH_STACK}}` | Teknoloji listesi | PHP 8.4, MySQL 9 |

### 4.3 Agent-Template Eşleştirme Tablosu

| Agent | Kullanacağı Template'ler |
|-------|-------------------------|
| Master Orchestrator | `documentation/WikiPage-Template.md`, `adr/adr-index.md` |
| Backend Architect | `backend/php-template.md`, `adr/adr-template.md` |
| UI Designer | `frontend/js-template.md`, `frontend/css-template.md`, `adr/adr-frontend-template.md` |
| Security Engineer | `adr/adr-security-template.md`, `documentation/security-audit-template.md` |
| Data Engineer | `adr/adr-database-template.md`, `query/Query-Template.md`, `infrastructure/migration-template.md` |
| Embedded Engineer | `other/c-template.md`, `adr/adr-audio-template.md` |
| QA Engineer | `testing/phpunit-template.md`, `testing/vitest-template.md` |
| DevOps Engineer | `infrastructure/docker-template.md`, `infrastructure/github-actions-template.md` |
| Audio Hardware Engineer | `hardware/arduino-template.md`, `hardware/avr-template.md`, `adr/adr-audio-template.md` |
| DSP Firmware Engineer | `other/c-template.md`, `hardware/avr-template.md` |
| Windows Software Engineer | `other/c-template.md` |

### 4.4 Workflow-Template Eşleştirme

| Workflow | Gerekli Template |
|----------|-----------------|
| New Feature | İlgili kategoriden template (§4.3'e bak) |
| Bug Fix | `adr/adr-template.md` (gerekirse) |
| Security Audit | `adr/adr-security-template.md`, `documentation/security-audit-template.md` |
| Database Migration | `infrastructure/migration-template.md`, `query/Query-Template.md` |
| CI/CD Pipeline | `infrastructure/github-actions-template.md`, `infrastructure/docker-template.md` |
| API Documentation | `documentation/api-doc-template.md` |
| Hardware Design | `hardware/arduino-template.md`, `hardware/avr-template.md`, `hardware/pic-template.md` |

## 5. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 3.3.0 |
| **Toplam Template** | 25 (19 language + 6 ADR) |
| **Toplam Satır** | 22,786 |
| **Ortalama Satır/Template** | 1,085 |
| **Minimum Satır** | 315 (adr-index) |
| **Maksimum Satır** | 1,693 (api-doc-template) |
| **Kategori** | 9 (adr, backend, frontend, testing, infra, docs, hardware, query, other) |
| **Dizin Yapısı** | ✅ Alt dizinlere ayrılmış |
| **Frontmatter Uyumlu** | ✅ Tümü (7 zorunlu alan) |

---

*Template Registry Index v3.3.0 — CoreMusic Template System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-08-13*
*Mode: Red Team · Human Mode · Truth Mode*
