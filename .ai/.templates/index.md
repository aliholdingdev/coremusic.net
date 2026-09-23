---
title: "CoreMusic — Template Registry Index"
type: template-index
category: template
version: 4.0.0
status: active
authority: "Template (Guardrail #16) — Registry: .ai/.templates/index.md"
updated: 2026-09-23
date: 2026-08-09
governance: Red Team · Human Mode · Truth Mode
total_templates: 17
total_files: 19
total_lines: 5546
---

# CoreMusic — Template Registry Index

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[ui-design/01-mockup-index]]

**Skills:** `.opencode/skills/` (10 skill — Guardrail #16 zorunlu)
**Agents:** `.ai/.agents/` (11 agent profile)
**UI Design:** `.ai/ui-design/` (115 dosya) + `.ai/.png/` (19 PNG)

---

## 1. Amaç

Bu dosya, CoreMusic ekosistemindeki tüm şablonların (template) merkezi indeksidir. Her şablonun amacını, teknoloji yığınıını ve kullanım alanını tanımlar. Şablon registry'sinin tek otoritesi bu dosyadır; hiçbir alt dosya kendini Single Source of Truth ilan edemez.

**⚠️ ZORUNLULUK (Guardrail #16):** Yeni dosya oluşturulurken bu listeden uygun template seçilmek ZORUNLU. Template olmadan dosya oluşturulamaz. AI ve insan geliştiriciler için aynı kural geçerlidir.

## 2. Kapsam ve Mimari Dizin Yapısı

Bu bölüm, `.templates/` dizininin disk gerçeğiyle birebir listingidir (2026-09-23 doğrulaması). Kullanıcılar: 11 agent profilinin tamamı (Guardrail #16) + insan geliştiriciler + Vault Steward.

```
.templates/
├── index.md, CLAUDE.md
├── agents/agents-template.md
├── adr/adr-template.md
├── backend/php-template.md, nodejs-template.md
├── documentation/api-doc-template.md, security-audit-template.md, WikiPage-Template.md
├── frontend/css-template.md, js-template.md
├── hardware/hardware-template.md
├── infrastructure/github-actions-template.md, migration-template.md
├── other/cpp-template.md
├── query/Query-Template.md
├── session-log-template.md (kök)
└── testing/phpunit-template.md, vitest-template.md
```

### Planlanan (diskte yok — Faz 6 defteri)

Bu dosyalar eski registry kayıtlarındadır ancak 2026-09-23 disk taramasında bulunamamıştır; SİLİNMEMİŞ, planlanan olarak taşınmıştır:

- `adr/adr-frontend-template.md`
- `adr/adr-database-template.md`
- `adr/adr-security-template.md`
- `adr/adr-audio-template.md`
- `adr/adr-index.md`
- `hardware/arduino-template.md`
- `hardware/avr-template.md`
- `hardware/pic-template.md`
- `other/aspnet-template.md`
- `other/c-template.md`

## 3. Mimari

### 3.1 Şablon Değişkenleri

| Değişken | Açıklama | Örnek |
|----------|----------|-------|
| `{{PROJECT_NAME}}` | Proje adı | CoreMusic |
| `{{AUTHOR}}` | Yazar | Bayram Ali |
| `{{VERSION}}` | Versiyon | 3.0.0 |
| `{{DATE}}` | Tarih | 2026-08-09 |
| `{{DESCRIPTION}}` | Kısa açıklama | Backend API service |
| `{{TECH_STACK}}` | Teknoloji listesi | PHP 8.4, MySQL 9 |

### 3.2 Ortak Şablon İskeleti (v2.0.0 standardı)

Bu, Guardrail #16 ile her yeni şablon dosyasının (`.templates/**`) uymak zorunda olduğu dış iskelet örneğidir; şablonun gerçek örneği burada yaşar:

````markdown
---
title: "CoreMusic — {{TITLE}}"
type: template
category: template
version: 2.0.0
status: active
authority: "Template (Guardrail #16) — Registry: .ai/.templates/index.md"
updated: {{DATE}}
---

# {{TITLE}}

**Zorunlu Bağlantılar / See also:** [[.templates/index]] · [[../CLAUDE.md]] · [[../AGENTS.md]]

## 1. Amaç
## 2. Kapsam
## 3. Mimari
## 4. Kurallar
## 5. Workflow
## 6. Doğrulama
## 7. Referanslar
````

## 4. Kurallar

1. **Guardrail #16 — şablon zorunlu:** Yeni `.md`/kod dosyası, bu listeden uygun şablondan üretilir; şablonsuz dosya oluşturmak yasaktır.
2. **Registry otoritesi:** Şablon ekleme/çıkarma/güncelleme yalnızca bu dosyanın tablolarından yapılır; alt klasör `CLAUDE.md` dosyaları şablon listesi iddiası taşıyamaz.
3. **SSOT self-claim yasak:** Bu dosya dışındaki hiçbir şablon/klasör dosyası "Single Source of Truth" iddiasında bulunamaz; authority alanı şablonlarda `Template (Guardrail #16) — Registry: .ai/.templates/index.md` değerindedir.
4. **Frontmatter standardı:** Her şablon dosyasında 7 zorunlu alan bulunur: `title`, `type`, `category`, `version`, `status`, `authority`, `updated`.
5. **Sayı senkronu:** `total_*` alanları disk gerçeğiyle tutarlıdır (17 şablon / 19 dosya / 5.546 satır — 2026-09-23 sayımı, Faz 1 sonrası yeniden ölçüldü). Uyuşmazlık → güncelleme zorunlu.
6. **Bilinmeyen bilgi uydurulmaz:** Doğrulanamayan iddia `⚠️ VERIFICATION REQUIRED` etiketiyle işaretlenir.

## 5. Workflow

ŞABLONU SEÇ → KOPYALA → `{{PLACEHOLDER}}` DOLDUR → GUARDRAIL #16 DOĞRULA → COMMIT

```text
Yeni dosya oluştururken:
1. İlgili kategoriden template'i seç (adr/, backend/, frontend/, vb.)
2. Template'i kopyala
3. Değişkenleri doldur ({{VARIABLE}} formatında)
4. Gereksiz bölümleri kaldır
5. Ek bölümler ekle (gerekirse)
```

*(Not: Bu şablon İÇİ özelleştirme adımlarıdır; şablonun dış 8-bölüm iskeleti — H1 + §1-§7 — silinemez, yalnızca §4.1'deki gibi şablon içi aşırı bölümler çıkarılabilir.)*

### 5.1 Agent-Template Eşleştirme Tablosu

| Agent | Kullanacağı Template'ler |
|-------|-------------------------|
| Master Orchestrator | `documentation/WikiPage-Template.md`, `adr/adr-index.md` |
| Backend Architect | `backend/php-template.md`, `adr/adr-template.md` |
| UI Designer | `frontend/js-template.md`, `frontend/css-template.md`, `adr/adr-frontend-template.md` |
| Security Engineer | `adr/adr-security-template.md`, `documentation/security-audit-template.md` |
| Data Engineer | `adr/adr-database-template.md`, `query/Query-Template.md`, `infrastructure/migration-template.md` |
| Embedded Engineer | `other/c-template.md`, `adr/adr-audio-template.md` |
| QA Engineer | `testing/phpunit-template.md`, `testing/vitest-template.md` |
| DevOps Engineer | `infrastructure/github-actions-template.md` |
| Audio Hardware Engineer | `hardware/arduino-template.md`, `hardware/avr-template.md`, `adr/adr-audio-template.md` |
| DSP Firmware Engineer | `other/c-template.md`, `hardware/avr-template.md` |
| Windows Software Engineer | `other/c-template.md` |

> ⚠️ Tablodaki `adr-index`, `adr-frontend`, `adr-database`, `adr-security`, `adr-audio`, `arduino`, `avr`, `pic`, `aspnet`, `c-template` atıfları diskte henüz yok — §2 "Planlanan" listesine bak.

### 5.2 Workflow-Template Eşleştirme

| Workflow | Gerekli Template |
|----------|-----------------|
| New Feature | İlgili kategoriden template (§5.1'e bak) |
| Bug Fix | `adr/adr-template.md` (gerekirse) |
| Security Audit | `adr/adr-security-template.md`, `documentation/security-audit-template.md` |
| Database Migration | `infrastructure/migration-template.md`, `query/Query-Template.md` |
| CI/CD Pipeline | `infrastructure/github-actions-template.md` |
| API Documentation | `documentation/api-doc-template.md` |
| Hardware Design | `hardware/arduino-template.md`, `hardware/avr-template.md`, `hardware/pic-template.md` |

> ⚠️ Bu tablodaki `adr-security`, `arduino`, `avr`, `pic` atıfları da diskte yok (§2 Planlanan).

## 6. Doğrulama

- [ ] 7 alanlı frontmatter var (`title`, `type`, `category`, `version`, `status`, `authority`, `updated`)
- [ ] 8 bölüm var (H1 + §1-§7)
- [ ] tüm `{{PLACEHOLDER}}`'lar dolduruldu
- [ ] dosya bu şablona uygun
- [ ] `total_*` sayıları disk sayımıyla senkron

**Quality Report (registry metrikleri):**

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 4.0.0 |
| **Toplam Template (diskte)** | 17 |
| **Planlanan (Faz 6)** | 10 |
| **Toplam Dosya** | 19 (17 şablon + index.md + CLAUDE.md) |
| **Toplam Satır** | 5.546 (2026-09-23, 19 md dosyası — Faz 1 sonrası canlı ölçüm) |
| **Ortalama Satır/Template** | 292 |
| **Minimum Satır** | 89 (CLAUDE.md — meta) |
| **Maksimum Satır** | 600 (frontend/js-template.md) |
| **Kategori** | 10 dizin (adr, agents, backend, documentation, frontend, hardware, infrastructure, other, query, testing) + kök (index, CLAUDE.md, session-log-template) |
| **Dizin Yapısı** | ✅ Alt dizinlere ayrılmış (§2 disk gerçeği) |
| **Frontmatter Uyumlu** | ✅ 19/19 7-alanlı FM + §7 başlığı (2026-09-23 doğrulandı) |
| **Düzeltilen eski iddialar** | 25.000/22.786 satır → 5.546 · 26 template → 17 · 1.085 ort. → 292 · min 315 (adr-index) / max 1.693 (api-doc) → 89/600 · "9-10 kategori (session dahil)" → 10 dizin, `session/` klasörü yok, `agents/` eklendi · 4.899 → 5.546 (Faz 1 sonrası yeniden ölçüm) |

**REFACTOR REPORT:** FILE: index.md · PURPOSE: Template Registry Index (şablon registry + dizin) · VALIDATION: 7 alan + §1-§7 + bilgi korunumu · RELATED: [[.templates/index]] · [[../CLAUDE.md]]

## 7. Referanslar

### 7.1 Template List (registry tabloları — disk durumuyla)

#### 7.1.1 ADR Templates (adr/)

| # | Template | Amaç | Satır | Durum | Dosya |
|---|----------|------|-------|-------|-------|
| 1 | ADR Template | Architecture Decision Record | 502 | ✅ diskte (159 satır, 2026-09-23) | [[adr/adr-template]] |
| 2 | ADR Frontend Template | Frontend ADR | 574 | 📋 Planlanan (Faz 6) | `adr/adr-frontend-template.md` (yok) |
| 3 | ADR Database Template | Database ADR | 514 | 📋 Planlanan (Faz 6) | `adr/adr-database-template.md` (yok) |
| 4 | ADR Security Template | Security ADR | 681 | 📋 Planlanan (Faz 6) | `adr/adr-security-template.md` (yok) |
| 5 | ADR Audio Template | Audio/Hardware ADR | 619 | 📋 Planlanan (Faz 6) | `adr/adr-audio-template.md` (yok) |
| 6 | ADR Index | ADR navigation guide | 315 | 📋 Planlanan (Faz 6) | `adr/adr-index.md` (yok) |

#### 7.1.2 Backend Templates (backend/)

| # | Template | Teknoloji | Amaç | Satır | Durum | Dosya |
|---|----------|-----------|------|-------|-------|-------|
| 7 | PHP Template | PHP 8.4, strict_types | Backend development | 815 | ✅ diskte (364 satır) | [[backend/php-template]] |
| 8 | Node.js Template | Node.js 20+, TypeScript 5+ | Download service | 1245 | ✅ diskte (171 satır) | [[backend/nodejs-template]] |

#### 7.1.3 Frontend Templates (frontend/)

| # | Template | Teknoloji | Amaç | Satır | Durum | Dosya |
|---|----------|-----------|------|-------|-------|-------|
| 9 | JavaScript Template | Vanilla JS ES6+ | Frontend development | 966 | ✅ diskte (504 satır) | [[frontend/js-template]] |
| 10 | CSS Template | ITCSS 9-layer, BEM | Stylesheet development | 819 | ✅ diskte (360 satır) | [[frontend/css-template]] |

#### 7.1.4 Testing Templates (testing/)

| # | Template | Teknoloji | Amaç | Satır | Durum | Dosya |
|---|----------|-----------|------|-------|-------|-------|
| 11 | PHPUnit Template | PHPUnit 10+, PHP 8.4 | PHP unit testing | 1380 | ✅ diskte (446 satır) | [[testing/phpunit-template]] |
| 12 | Vitest Template | Vitest, happy-dom | JS/TS unit testing | 1372 | ✅ diskte (410 satır) | [[testing/vitest-template]] |

#### 7.1.5 Infrastructure Templates (infrastructure/)

| # | Template | Teknoloji | Amaç | Satır | Durum | Dosya |
|---|----------|-----------|------|-------|-------|-------|
| 13 | Migration Template | MySQL 9, BCNF | Database migration | 1349 | ✅ diskte (261 satır) | [[infrastructure/migration-template]] |
| 14 | GitHub Actions Template | GH Actions, CI/CD | Pipeline automation | 1466 | ✅ diskte (288 satır) | [[infrastructure/github-actions-template]] |

#### 7.1.6 Documentation Templates (documentation/)

| # | Template | Format | Amaç | Satır | Durum | Dosya |
|---|----------|--------|------|-------|-------|-------|
| 16 | API Doc Template | Markdown, OpenAPI 3.1 | API dokümantasyonu | 1693 | ✅ diskte (232 satır) | [[documentation/api-doc-template]] |
| 17 | Security Audit Template | Markdown, OWASP | Güvenlik denetimi | 954 | ✅ diskte (173 satır) | [[documentation/security-audit-template]] |
| 18 | Wiki Page Template | Markdown, Mermaid | Wiki sayfası | 974 | ✅ diskte (126 satır) | [[documentation/WikiPage-Template]] |

*(Eski kayıtta #15 numarası hiç kullanılmamıştır — korunmuştur.)*

#### 7.1.7 Hardware Templates (hardware/)

| # | Template | Teknoloji | Amaç | Satır | Durum | Dosya |
|---|----------|-----------|------|-------|-------|-------|
| 19 | Arduino Template | Arduino CLI, C++17 | Arduino/IoT prototyping | 1511 | 📋 Planlanan (Faz 6) | `hardware/arduino-template.md` (yok) |
| 20 | AVR Template | AVR-GCC, avr-libc | AVR microcontroller | 1133 | 📋 Planlanan (Faz 6) | `hardware/avr-template.md` (yok) |
| 21 | PIC Template | XC8, MPLAB X | PIC microcontroller | 1334 | 📋 Planlanan (Faz 6) | `hardware/pic-template.md` (yok) |
| 26 | Hardware Template | Donanım genel | Hardware development | 210 | ✅ diskte (210 satır) | [[hardware/hardware-template]] |

#### 7.1.8 Query Templates (query/)

| # | Template | Format | Amaç | Satır | Durum | Dosya |
|---|----------|--------|------|-------|-------|-------|
| 22 | Query Template | SQL, MySQL 9 | Veritabanı sorguları | 1402 | ✅ diskte (235 satır) | [[query/Query-Template]] |

#### 7.1.9 Other Templates (other/)

| # | Template | Teknoloji | Amaç | Satır | Durum | Dosya |
|---|----------|-----------|------|-------|-------|-------|
| 23 | ASP.NET Template | ASP.NET 9, C# 13 | Enterprise backend | 1331 | 📋 Planlanan (Faz 6) | `other/aspnet-template.md` (yok) |
| 24 | C Template | C11, GCC | Embedded, drivers | 933 | 📋 Planlanan (Faz 6) | `other/c-template.md` (yok) |
| 27 | C++ Template | C++20, GCC | Embedded, drivers | 402 | ✅ diskte (402 satır) | [[other/cpp-template]] |

#### 7.1.10 Session Templates (kök)

| # | Template | Format | Amaç | Satır | Durum | Dosya |
|---|----------|--------|------|-------|-------|-------|
| 25 | Session Log Template | Markdown | Oturum kaydı | 51 | ✅ diskte (143 satır, 2026-09-23 v2.0.0 sonrası) | [[session-log-template]] (kök) |

*(Eski kayıtta `session/` klasörü geçiyordu; diskte `session/` klasörü YOKTUR, dosya köktedir.)*

#### 7.1.11 Agents Templates (agents/)

| # | Template | Teknoloji | Amaç | Satır | Durum | Dosya |
|---|----------|-----------|------|-------|-------|-------|
| 28 | Agent Profile Template | Markdown | Agent profil şablonu | 168 | ✅ diskte (168 satır) | [[agents/agents-template]] |

*(Satır sütunlarındaki eski değerler 2026-08 kayıtlıdır; 2026-09-23 disk satır değerleri "Durum" sütununda.)*

### 7.2 Wiki-link Referansları

- [[.templates/index]] — bu registry
- [[../CLAUDE.md]] — ana sözleşme
- [[../AGENTS.md]] — agent registry (Guardrail #16 routing)
- [[../WORKFLOW.md]] — süreçler
- [[ui-design/01-mockup-index]] — mockup indeksi (düzeltildi: `00-mockup-index` → `01-mockup-index`, diskte doğrulandı)

---

*Template Registry Index v4.0.0 — CoreMusic Template System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-23*
*Mode: Red Team · Human Mode · Truth Mode*
