---
title: "CoreMusic — Template Registry Index"
type: template-index
category: template
version: 4.1.0
status: active
authority: "Template (Guardrail #16) — Registry: .ai/.templates/index.md"
updated: 2026-09-23
date: 2026-08-09
governance: Red Team · Human Mode · Truth Mode
total_templates: 26
total_files: 26
total_lines: 12549
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

Bu bölüm, `.templates/` dizininin disk gerçeğiyle birebir listingidir (2026-09-23 Faz 2 doğrulaması — **26/26 dosya diskte**). Kullanıcılar: 11 agent profilinin tamamı (Guardrail #16) + insan geliştiriciler + Vault Steward.

```
.templates/
├── index.md · CLAUDE.md · session-log-template.md                     (kök — 3)
├── adr/adr-template.md · adr-frontend-template.md · adr-database-template.md ·
│   adr-security-template.md · adr-audio-template.md · adr-index.md    (6)
├── agents/agents-template.md                                          (1)
├── backend/php-template.md · nodejs-template.md                        (2)
├── documentation/api-doc-template.md · security-audit-template.md ·
│   WikiPage-Template.md                                               (3)
├── frontend/css-template.md · js-template.md                           (2)
├── hardware/hardware-template.md                                       (1)
├── infrastructure/github-actions-template.md · migration-template.md   (2)
├── other/cpp-template.md · aspnet-template.md · c-template.md          (3)
├── query/Query-Template.md                                             (1)
└── testing/phpunit-template.md · vitest-template.md                    (2)
```

**Toplam: 26 dosya** = 24 şablon (23'ü klasör içi + `session-log-template.md` kök) + 2 meta (`index.md`, `CLAUDE.md`). **Kategori: 10 dizin + kök.** `session/` klasörü YOKTUR (kayıt köktedir).

✅ **2026-09-23 üretim tamamlandı — 26/26 mevcut** (Faz 2'de üretilen `adr-frontend`, `adr-database`, `adr-security`, `adr-audio`, `adr-index`, `aspnet`, `c` artık disktedir).

### Planlanan (diskte hâlâ yok — Faz 6 defteri)

Faz 2 üretiminden sonra yalnız aşağıdaki 3 dosya diskte değildir; SİLİNMEMİŞ, planlanan olarak taşınmıştır:

- `hardware/arduino-template.md`
- `hardware/avr-template.md`
- `hardware/pic-template.md`

*(Eski "Planlanan" listesindeki 7 dosya — adr-frontend/database/security/audio/index, aspnet, c — 2026-09-23'te üretilip listeden çıkarılmıştır.)*

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
5. **Sayı senkronu:** `total_*` alanları disk gerçeğiyle tutarlıdır — `total_templates: 26` (registry kaydı: 24 şablon + 2 meta), `total_files: 26`, `total_lines: 12.549` (2026-09-23 Faz 2 sayımı, üretim sonu). Uyuşmazlık → güncelleme zorunlu.
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
| Audio Hardware Engineer | `hardware/hardware-template.md`, `adr/adr-audio-template.md` |
| DSP Firmware Engineer | `other/c-template.md`, `hardware/hardware-template.md` |
| Windows Software Engineer | `other/c-template.md` |

✅ **2026-09-23 üretim tamamlandı — 26/26 mevcut.** Tablodaki `adr-index`, `adr-frontend`, `adr-database`, `adr-security`, `adr-audio`, `c-template` atıfları disktedir; diskte olmayan `arduino`/`avr`/`pic` atıfları `hardware/hardware-template.md` ile eşleştirilmiştir (o şablon üretilmedi — §2 "Planlanan").

### 5.2 Workflow-Template Eşleştirme

| Workflow | Gerekli Template |
|----------|-----------------|
| New Feature | İlgili kategoriden template (§5.1'e bak) |
| Bug Fix | `adr/adr-template.md` (gerekirse) |
| Security Audit | `adr/adr-security-template.md`, `documentation/security-audit-template.md` |
| Database Migration | `infrastructure/migration-template.md`, `query/Query-Template.md` |
| CI/CD Pipeline | `infrastructure/github-actions-template.md` |
| API Documentation | `documentation/api-doc-template.md` |
| Hardware Design | `hardware/hardware-template.md` |

✅ **2026-09-23 üretim tamamlandı — 26/26 mevcut.** Bu tablodaki `adr-security` atıfı disktedir; Hardware Design satırı, diskteki tek donanım şablonu olan `hardware/hardware-template.md`'yi gösterir (arduino/avr/pic üretilmedi — §2 "Planlanan").

## 6. Doğrulama

- [ ] 7 alanlı frontmatter var (`title`, `type`, `category`, `version`, `status`, `authority`, `updated`)
- [ ] 8 bölüm var (H1 + §1-§7)
- [ ] tüm `{{PLACEHOLDER}}`'lar dolduruldu
- [ ] dosya bu şablona uygun
- [ ] `total_*` sayıları disk sayımıyla senkron

**Quality Report (registry metrikleri):**

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 4.1.0 |
| **Toplam Dosya (registry)** | 26 md (24 şablon + 2 meta: index.md, CLAUDE.md) |
| **Toplam Template** | 26 kayıt (`total_templates` = registry kayıtları; şablon alt kümesi 24) |
| **500+ derinlik** | 23/23 klasör içi şablon 500+ satır (min 501 `cpp` · max 649 `js`) |
| **Kısa şablon** | 1 — `session-log-template.md` (144 satır; 500+ kuralı kapsamı dışı, bilinçli kısa şablon) |
| **Planlanan (Faz 6)** | 3 — `arduino`, `avr`, `pic` (§2'ye bak) |
| **Toplam Satır** | 12.549 (2026-09-23, 26 md dosyası — Faz 2 üretim sonu ölçümü; o an: index.md 297 · CLAUDE.md 90) |
| **Ortalama Satır/Template** | 483 (12.549 ÷ 26 dosya) |
| **Minimum Satır** | 90 (CLAUDE.md — meta) · şablon min 144 (`session-log-template.md`) |
| **Maksimum Satır** | 649 (frontend/js-template.md) |
| **Kategori** | 10 dizin (adr, agents, backend, documentation, frontend, hardware, infrastructure, other, query, testing) + kök (index.md, CLAUDE.md, session-log-template) |
| **Dizin Yapısı** | ✅ Alt dizinlere ayrılmış (§2 disk gerçeği) |
| **Frontmatter Uyumlu** | ✅ 26/26 7-alanlı FM (2026-09-23 betik doğrulaması: FM BAD=0) |
| **Ölçüm notu** | ⚠️ Tüm sayılar 2026-09-23 Faz 2 üretim sonu anına aittir (index.md 297 · CLAUDE.md 90 dahil). Bu birleştirme yazımı bu iki meta dosyayı yeniden yazdığı için canlı `total_lines` değişmiştir; 24 şablonun satır sayısı değişmez. Bir sonraki senkromda `total_*` tazelenmeli. |
| **Düzeltilen eski iddialar** | 25.000/5.546 satır → 12.549 · 17 şablon/19 dosya → 24 şablon/26 dosya · Planlanan 10 → 3 (7'si Faz 2'de üretildi) · ort. 292 → 483 · min 89/max 600 → 90/649 · "arduino/avr/pic diskte" → hardware tek dosya (`hardware-template.md`, 512) · "10 klasör (session dahil)" → 10 dizin + kök, `session/` klasörü yok |

**REFACTOR REPORT:** FILE: index.md · PURPOSE: Template Registry Index (şablon registry + dizin) · VALIDATION: 7 alan + §1-§7 + bilgi korunumu + envanter 26/26 disk sayımı · RELATED: [[.templates/index]] · [[../CLAUDE.md]]

## 7. Referanslar

### 7.1 Template List (registry tabloları — disk durumuyla, 2026-09-23 gerçek ölçüm)

#### 7.1.1 ADR Templates (adr/)

| # | Template | Amaç | Satır | Durum | Dosya |
|---|----------|------|-------|-------|-------|
| 1 | ADR Template | Architecture Decision Record | 512 | ✅ Mevcut (2026-09-23) | [[adr/adr-template]] |
| 2 | ADR Frontend Template | Frontend ADR | 519 | ✅ Mevcut (2026-09-23 — Faz 2 üretimi) | [[adr/adr-frontend-template]] |
| 3 | ADR Database Template | Database ADR | 507 | ✅ Mevcut (2026-09-23 — Faz 2 üretimi) | [[adr/adr-database-template]] |
| 4 | ADR Security Template | Security ADR | 516 | ✅ Mevcut (2026-09-23 — Faz 2 üretimi) | [[adr/adr-security-template]] |
| 5 | ADR Audio Template | Audio/Hardware ADR | 509 | ✅ Mevcut (2026-09-23 — Faz 2 üretimi) | [[adr/adr-audio-template]] |
| 6 | ADR Index | ADR navigation guide | 504 | ✅ Mevcut (2026-09-23 — Faz 2 üretimi) | [[adr/adr-index]] |

#### 7.1.2 Backend Templates (backend/)

| # | Template | Teknoloji | Amaç | Satır | Durum | Dosya |
|---|----------|-----------|------|-------|-------|-------|
| 7 | PHP Template | PHP 8.4, strict_types | Backend development | 551 | ✅ Mevcut (2026-09-23) | [[backend/php-template]] |
| 8 | Node.js Template | Node.js 20+, TypeScript 5+ | Download service | 509 | ✅ Mevcut (2026-09-23) | [[backend/nodejs-template]] |

#### 7.1.3 Frontend Templates (frontend/)

| # | Template | Teknoloji | Amaç | Satır | Durum | Dosya |
|---|----------|-----------|------|-------|-------|-------|
| 9 | JavaScript Template | Vanilla JS ES6+ | Frontend development | 649 | ✅ Mevcut (2026-09-23) | [[frontend/js-template]] |
| 10 | CSS Template | ITCSS 9-layer, BEM | Stylesheet development | 531 | ✅ Mevcut (2026-09-23) | [[frontend/css-template]] |

#### 7.1.4 Testing Templates (testing/)

| # | Template | Teknoloji | Amaç | Satır | Durum | Dosya |
|---|----------|-----------|------|-------|-------|-------|
| 11 | PHPUnit Template | PHPUnit 10+, PHP 8.4 | PHP unit testing | 506 | ✅ Mevcut (2026-09-23) | [[testing/phpunit-template]] |
| 12 | Vitest Template | Vitest, happy-dom | JS/TS unit testing | 518 | ✅ Mevcut (2026-09-23) | [[testing/vitest-template]] |

#### 7.1.5 Infrastructure Templates (infrastructure/)

| # | Template | Teknoloji | Amaç | Satır | Durum | Dosya |
|---|----------|-----------|------|-------|-------|-------|
| 13 | Migration Template | MySQL 9, BCNF | Database migration | 505 | ✅ Mevcut (2026-09-23) | [[infrastructure/migration-template]] |
| 14 | GitHub Actions Template | GH Actions, CI/CD | Pipeline automation | 546 | ✅ Mevcut (2026-09-23) | [[infrastructure/github-actions-template]] |

#### 7.1.6 Documentation Templates (documentation/)

| # | Template | Format | Amaç | Satır | Durum | Dosya |
|---|----------|--------|------|-------|-------|-------|
| 15 | API Doc Template | Markdown, OpenAPI 3.1 | API dokümantasyonu | 513 | ✅ Mevcut (2026-09-23) | [[documentation/api-doc-template]] |
| 16 | Security Audit Template | Markdown, OWASP | Güvenlik denetimi | 506 | ✅ Mevcut (2026-09-23) | [[documentation/security-audit-template]] |
| 17 | Wiki Page Template | Markdown, Mermaid | Wiki sayfası | 530 | ✅ Mevcut (2026-09-23) | [[documentation/WikiPage-Template]] |

*(2026-09-23: envanter #1-#26 arasında yeniden numaralandırıldı; eski kayıtta #15 numarası hiç kullanılmamıştı — bu bilgi korunur.)*

#### 7.1.7 Hardware Templates (hardware/)

| # | Template | Teknoloji | Amaç | Satır | Durum | Dosya |
|---|----------|-----------|------|-------|-------|-------|
| 18 | Hardware Template | Donanım genel | Hardware development | 512 | ✅ Mevcut (2026-09-23) | [[hardware/hardware-template]] |

*(Gerçek disk durumu: `hardware/` altında TEK dosya vardır — `hardware-template.md`. Eski kayıtta geçen `arduino`/`avr`/`pic` şablonları diskte YOKTUR ve üretilmemiştir; §2 "Planlanan" listesindedir.)*

#### 7.1.8 Query Templates (query/)

| # | Template | Format | Amaç | Satır | Durum | Dosya |
|---|----------|--------|------|-------|-------|-------|
| 19 | Query Template | SQL, MySQL 9 | Veritabanı sorguları | 514 | ✅ Mevcut (2026-09-23) | [[query/Query-Template]] |

#### 7.1.9 Other Templates (other/)

| # | Template | Teknoloji | Amaç | Satır | Durum | Dosya |
|---|----------|-----------|------|-------|-------|-------|
| 20 | ASP.NET Template | ASP.NET 9, C# 13 | Enterprise backend | 517 | ✅ Mevcut (2026-09-23 — Faz 2 üretimi) | [[other/aspnet-template]] |
| 21 | C Template | C11, GCC | Embedded, drivers | 516 | ✅ Mevcut (2026-09-23 — Faz 2 üretimi) | [[other/c-template]] |
| 22 | C++ Template | C++20, GCC | Embedded, drivers | 501 | ✅ Mevcut (2026-09-23) | [[other/cpp-template]] |

#### 7.1.10 Session Templates (kök)

| # | Template | Format | Amaç | Satır | Durum | Dosya |
|---|----------|--------|------|-------|-------|-------|
| 23 | Session Log Template | Markdown | Oturum kaydı | 144 | ✅ Mevcut (2026-09-23, v2.0.0) — 500+ kuralı kapsamı dışı kısa şablon | [[session-log-template]] (kök) |

*(Eski kayıtta `session/` klasörü geçiyordu; diskte `session/` klasörü YOKTUR, dosya köktedir.)*

#### 7.1.11 Agents Templates (agents/)

| # | Template | Teknoloji | Amaç | Satır | Durum | Dosya |
|---|----------|-----------|------|-------|-------|-------|
| 24 | Agent Profile Template | Markdown | Agent profil şablonu | 527 | ✅ Mevcut (2026-09-23) | [[agents/agents-template]] |

#### 7.1.12 Meta Dosyalar (kök — registry'nin kendisi)

| # | Dosya | Amaç | Satır | Durum | Not |
|---|-------|------|-------|-------|-----|
| 25 | Registry Index | Şablon registry indeksi (bu dosya) | — | ✅ Mevcut | `index.md` — self-referans; Faz 2 ölçüm anı: 297 satır (bu yazımla değişti) |
| 26 | Templates CLAUDE | Klasör bağlam + değişiklik protokolü | — | ✅ Mevcut | `CLAUDE.md` — meta; Faz 2 ölçüm anı: 90 satır (bu yazımla değişti) |

*(Satır sütunu: #1-#24 = 2026-09-23 gerçek disk ölçümü — Faz 2 üretim sonrası; eski 2026-08 tahmini değerler kaldırıldı. #25-#26 self-referans olduğu için sayısal iddia taşımaz — bkz. §6 "Ölçüm notu".)*

### 7.2 Wiki-link Referansları

- [[.templates/index]] — bu registry
- [[../CLAUDE.md]] — ana sözleşme
- [[../AGENTS.md]] — agent registry (Guardrail #16 routing)
- [[../WORKFLOW.md]] — süreçler
- [[ui-design/01-mockup-index]] — mockup indeksi (düzeltildi: `00-mockup-index` → `01-mockup-index`, diskte doğrulandı)

---

*Template Registry Index v4.1.0 — CoreMusic Template System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-23*
*Mode: Red Team · Human Mode · Truth Mode*
