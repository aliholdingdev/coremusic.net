---
title: "CoreMusic — Template Registry Index"
type: template-index
category: template
version: 4.4.0
status: active
authority: "Template (Guardrail #16) — Registry: .ai/.templates/index.md"
updated: 2026-09-24
date: 2026-08-09
governance: Red Team · Human Mode · Truth Mode
total_templates: 36
total_files: 36
total_lines: 16503
---

# CoreMusic — Template Registry Index

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[ui-design/01-mockup-index]]

**Skills:** `.opencode/skills/` (10 skill — Guardrail #16 zorunlu)
**Agents:** `.ai/.agents/` (11 agent profile)
**UI Design:** `.ai/ui-design/` (136 dosya · 119 md) + `.ai/.png/` (19 PNG)

---

## 1. Amaç

Bu dosya, CoreMusic ekosistemindeki tüm şablonların (template) merkezi indeksidir. Her şablonun amacını, teknoloji yığınıını ve kullanım alanını tanımlar. Şablon registry'sinin tek otoritesi bu dosyadır; hiçbir alt dosya kendini Single Source of Truth ilan edemez.

**⚠️ ZORUNLULUK (Guardrail #16):** Yeni dosya oluşturulurken bu listeden uygun template seçilmek ZORUNLU. Template olmadan dosya oluşturulamaz. AI ve insan geliştiriciler için aynı kural geçerlidir.

## 2. Kapsam ve Mimari Dizin Yapısı

Bu bölüm, `.templates/` dizininin disk gerçeğiyle birebir listingidir (2026-09-24 taze doğrulama — **36/36 dosya diskte**; aynı günün önceki kayıtları: 32/32 → 28/28; 2026-09-23 Faz 2 kaydı: 26/26). Kullanıcılar: 11 agent profilinin tamamı (Guardrail #16) + insan geliştiriciler + Vault Steward.

```
.templates/
├── index.md · CLAUDE.md · session-log-template.md                     (kök — 3)
├── adr/adr-template.md · adr-frontend-template.md · adr-database-template.md ·
│   adr-security-template.md · adr-audio-template.md · adr-index.md ·
│   adr-nygard-template.md                                             (7)
├── agents/agents-template.md · agent-tartisma-turu-template.md        (2)
├── backend/php-template.md · nodejs-template.md                        (2)
├── documentation/api-doc-template.md · security-audit-template.md ·
│   WikiPage-Template.md · claude-md-template.md · docs-md-template.md ·
│   katman-readme-template.md · alt-katman-template.md                 (7)
├── frontend/css-template.md · js-template.md                           (2)
├── hardware/hardware-template.md                                       (1)
├── infrastructure/github-actions-template.md · migration-template.md   (2)
├── other/cpp-template.md · aspnet-template.md · c-template.md          (3)
├── query/Query-Template.md                                             (1)
├── testing/phpunit-template.md · vitest-template.md                    (2)
└── ui-design/reference-template.md · flow-template.md ·
    prompt-template.md · screen-spec-template.md                        (4)
```

**Toplam: 36 dosya** = 34 şablon (33'ü klasör içi + `session-log-template.md` kök) + 2 meta (`index.md`, `CLAUDE.md`). **Kategori: 11 dizin + kök.** `session/` klasörü YOKTUR (kayıt köktedir). *(2026-09-24: +2 şablon — `claude-md-template`, `docs-md-template`; aynı gün +4 şablon — `ui-design/` altı: `reference-template`, `flow-template`, `prompt-template`, `screen-spec-template`; aynı gün son +4 şablon — `documentation/katman-readme`, `documentation/alt-katman`, `adr/adr-nygard`, `agents/agent-tartisma-turu` → #33-#36, §7.1'e bak.)*

✅ **2026-09-23 üretim tamamlandı — 26/26 mevcut** (2026-09-24: +2 → 28/28 — documentation/claude-md + docs-md, §7.1.6'ya bak; aynı gün +4 → 32/32 — ui-design/*, §7.1.12'ye bak; aynı gün +4 → **36/36** — katman/ADR/tartışma şablonları, §7.1.1/#7.1.6/#7.1.11'e bak) (Faz 2'de üretilen `adr-frontend`, `adr-database`, `adr-security`, `adr-audio`, `adr-index`, `aspnet`, `c` artık disktedir).

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
5. **Sayı senkronu:** `total_*` alanları disk gerçeğiyle tutarlıdır — `total_templates: 36` (registry kaydı: 34 şablon + 2 meta), `total_files: 36`, `total_lines: 16503` (16.503 — 2026-09-24 2. geçiş sayımı, 36 md). Uyuşmazlık → güncelleme zorunlu.
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
| Master Orchestrator | `documentation/WikiPage-Template.md`, `documentation/claude-md-template.md`, `documentation/docs-md-template.md`, `adr/adr-index.md`, `documentation/katman-readme-template.md`, `documentation/alt-katman-template.md`, `adr/adr-nygard-template.md` |
| Backend Architect | `backend/php-template.md`, `adr/adr-template.md` |
| UI Designer | `frontend/js-template.md`, `frontend/css-template.md`, `adr/adr-frontend-template.md`, `ui-design/reference-template.md` (Kalıp A), `ui-design/flow-template.md` (Kalıp B), `ui-design/prompt-template.md` (Kalıp C), `ui-design/screen-spec-template.md` (Kalıp D) |
| Security Engineer | `adr/adr-security-template.md`, `documentation/security-audit-template.md` |
| Data Engineer | `adr/adr-database-template.md`, `query/Query-Template.md`, `infrastructure/migration-template.md` |
| Embedded Engineer | `other/c-template.md`, `adr/adr-audio-template.md` |
| QA Engineer | `testing/phpunit-template.md`, `testing/vitest-template.md` |
| DevOps Engineer | `infrastructure/github-actions-template.md` |
| Audio Hardware Engineer | `hardware/hardware-template.md`, `adr/adr-audio-template.md` |
| DSP Firmware Engineer | `other/c-template.md`, `hardware/hardware-template.md` |
| Windows Software Engineer | `other/c-template.md` |
| Tüm ajanlar (tartışma protokolü) | `agents/agent-tartisma-turu-template.md` (3 tur/20 persona — `agent-debate` becerisi) |

✅ **2026-09-23 üretim tamamlandı — 26/26 mevcut** (2026-09-24: +2 → 28/28, +4 → **32/32**; yeni atıflar §7.1.6 documentation ve §7.1.12 ui-design satırlarında). Tablodaki `adr-index`, `adr-frontend`, `adr-database`, `adr-security`, `adr-audio`, `c-template` atıfları disktedir; diskte olmayan `arduino`/`avr`/`pic` atıfları `hardware/hardware-template.md` ile eşleştirilmiştir (o şablon üretilmedi — §2 "Planlanan").

### 5.2 Workflow-Template Eşleştirme

| Workflow | Gerekli Template |
|----------|-----------------|
| New Feature | İlgili kategoriden template (§5.1'e bak) |
| Bug Fix | `adr/adr-template.md` (gerekirse) |
| Security Audit | `adr/adr-security-template.md`, `documentation/security-audit-template.md` |
| Database Migration | `infrastructure/migration-template.md`, `query/Query-Template.md` |
| CI/CD Pipeline | `infrastructure/github-actions-template.md` |
| API Documentation | `documentation/api-doc-template.md` |
| UI Design Prompt üretimi | `ui-design/prompt-template.md` (Kalıp C) |
| UI Design Flow / Ekran spec'i | `ui-design/flow-template.md` (Kalıp B), `ui-design/screen-spec-template.md` (Kalıp D) |
| UI Design Referans / Token dokümanı | `ui-design/reference-template.md` (Kalıp A) |
| Hardware Design | `hardware/hardware-template.md` |
| ADR yazımı (mimari seri / Nygard) | `adr/adr-nygard-template.md` (`.ai/architecture/adr/` — `.workflows/adr-creation.md` Adım 3) |
| Katman/alt-katman dokümanı | `documentation/katman-readme-template.md`, `documentation/alt-katman-template.md` (K0-K20 / A0-A5) |
| Multi-agent tartışma (3 tur) | `agents/agent-tartisma-turu-template.md` (Tur1 öneri → Tur2 çapraz → Tur3 uzlaşma+ADR) |

✅ **2026-09-23 üretim tamamlandı — 26/26 mevcut** (2026-09-24: +2 → 28/28, +4 → **32/32** — ui-design 3 yeni workflow satırı). Bu tablodaki `adr-security` atıfı disktedir; Hardware Design satırı, diskteki tek donanım şablonu olan `hardware/hardware-template.md`'yi gösterir (arduino/avr/pic üretilmedi — §2 "Planlanan").

## 6. Doğrulama

- [ ] 7 alanlı frontmatter var (`title`, `type`, `category`, `version`, `status`, `authority`, `updated`)
- [ ] 8 bölüm var (H1 + §1-§7)
- [ ] tüm `{{PLACEHOLDER}}`'lar dolduruldu
- [ ] dosya bu şablona uygun
- [ ] `total_*` sayıları disk sayımıyla senkron

**Quality Report (registry metrikleri):**

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 4.4.0 |
| **Toplam Dosya (registry)** | 36 md (34 şablon + 2 meta: index.md, CLAUDE.md) |
| **Toplam Template** | 36 kayıt (`total_templates` = registry kayıtları; şablon alt kümesi 34) |
| **500+ derinlik** | 29/33 klasör içi şablon 500+ satır (ölçüm 2026-09-24: min 501 `other/cpp` · max 649 `frontend/js`; aynı gün eklenen 4 ui-design: `reference` 509 · `flow` 506 · `prompt` 507 · `screen-spec` 545) — **istisna: aynı gün eklenen 4 şablon bilinçli olarak 100-250 aralığında** (`katman-readme` 174 · `alt-katman` 158 · `adr-nygard` 209 · `agent-tartisma-turu` 185 — görev şartı: açıklamalı dolgu 100-250 satır) |
| **Kısa şablon** | 5 — `session-log-template.md` (144 satır; 500+ kuralı kapsamı dışı, bilinçli kısa şablon) + 4 yeni 100-250 aralığı şablon (yukarıdaki istisna satırı) |
| **Planlanan (Faz 6)** | 3 — `arduino`, `avr`, `pic` (§2'ye bak) |
| **Toplam Satır** | 16.503 (2026-09-24, 36 md dosyası; 2. geçiş ölçümü — `index.md` bu yazımla 325 → 338 oldu, delta +13 yansıtıldı) — eski: 15.764 (32 md) · 14.695 (28 md) · 12.549 (2026-09-23, 26 md) |
| **Ortalama Satır/Template** | 458 (16.503 ÷ 36 dosya) |
| **Minimum Satır** | 99 (templates/CLAUDE.md — meta) · şablon min 144 (`session-log-template.md`) · sonra 158 (`documentation/alt-katman-template.md`) |
| **Maksimum Satır** | 649 (frontend/js-template.md) |
| **Kategori** | 11 dizin (adr 7, agents 2, backend 2, documentation 7, frontend 2, hardware 1, infrastructure 2, other 3, query 1, testing 2, ui-design 4) + kök (index.md, CLAUDE.md, session-log-template) |
| **Dizin Yapısı** | ✅ Alt dizinlere ayrılmış (§2 disk gerçeği) |
| **Frontmatter Uyumlu** | ✅ 34/34 7-alanlı FM (2026-09-23 betik doğrulaması: FM BAD=0; 2026-09-24 yeni 8 şablon da 7 alan + `reference` bloğu) |
| **Ölçüm notu** | ✅ Tazelendi (2026-09-24): 36 md disk sayımı; `total_*` bu yazımla senkron (32 eski şablonun satır sayısı değişmedi, +4 şablon eklendi — adr-nygard 209, agent-tartisma 185, katman-readme 174, alt-katman 158 = 726 satır). Ölçüm yöntemi: dosya içeriğinin `\n` ile bölünmesi — `vault-utf8-writer verify` → `lines` ile aynı. `index.md` bu yazımla kendi satır sayısını değiştirir; `total_lines` 2. geçişte tazelenir. |
| **Düzeltilen eski iddialar** | 25.000/5.546 satır → 12.549 · 17 şablon/19 dosya → 24 şablon/26 dosya · Planlanan 10 → 3 (7'si Faz 2'de üretildi) · ort. 292 → 458 · min 90/max 649 → 144/649 · "arduino/avr/pic diskte" → hardware tek dosya (`hardware-template.md`, 512) · "10 klasör (session dahil)" → 11 dizin + kök, `session/` klasörü yok · 26 dosya/12.549 satır → 28/14.695 → 32/15.764 → **36 dosya/16.503** (2026-09-24, +2: claude-md, docs-md; +4: ui-design/*; +4: katman-readme, alt-katman, adr-nygard, agent-tartisma-turu) · `ui-design` "115 dosya" → **136 dosya (119 md)** |

**REFACTOR REPORT:** FILE: index.md · PURPOSE: Template Registry Index (şablon registry + dizin) · VALIDATION: 7 alan + §1-§7 + bilgi korunumu + envanter 36/36 disk sayımı (2026-09-24, +4 şablon: katman/ADR/tartışma) · RELATED: [[.templates/index]] · [[../CLAUDE.md]]

## 7. Referanslar

### 7.1 Template List (registry tabloları — disk durumuyla, 2026-09-24 gerçek ölçüm)

#### 7.1.1 ADR Templates (adr/)

| # | Template | Amaç | Satır | Durum | Dosya |
|---|----------|------|-------|-------|-------|
| 1 | ADR Template | Architecture Decision Record | 512 | ✅ Mevcut (2026-09-23) | [[adr/adr-template]] |
| 2 | ADR Frontend Template | Frontend ADR | 519 | ✅ Mevcut (2026-09-23 — Faz 2 üretimi) | [[adr/adr-frontend-template]] |
| 3 | ADR Database Template | Database ADR | 507 | ✅ Mevcut (2026-09-23 — Faz 2 üretimi) | [[adr/adr-database-template]] |
| 4 | ADR Security Template | Security ADR | 516 | ✅ Mevcut (2026-09-23 — Faz 2 üretimi) | [[adr/adr-security-template]] |
| 5 | ADR Audio Template | Audio/Hardware ADR | 509 | ✅ Mevcut (2026-09-23 — Faz 2 üretimi) | [[adr/adr-audio-template]] |
| 6 | ADR Index | ADR navigation guide | 504 | ✅ Mevcut (2026-09-23 — Faz 2 üretimi) | [[adr/adr-index]] |
| 33 | ADR Nygard Template | Michael Nygard ADR (bağlam/alternatifler/sonuçlar) — `.ai/architecture/adr/` mimari seri | 209 | ✅ Mevcut (2026-09-24 — yeni üretim; 100-250 görev şartı) | [[adr/adr-nygard-template]] |

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
| 18 | CLAUDE.md Template | Markdown, AI yönerge | Proje CLAUDE.md üretimi | 552 | ✅ Mevcut (2026-09-24 — yeni üretim) | [[documentation/claude-md-template]] |
| 19 | Docs Markdown Template | Markdown, 8-bölüm iskelet | Genel dokümantasyon .md | 558 | ✅ Mevcut (2026-09-24 — yeni üretim) | [[documentation/docs-md-template]] |
| 35 | Katman README Template | Markdown, K0-K20 / A0-A5 | Katman düzeyi README (`architecture/<katman>/README.md`) | 174 | ✅ Mevcut (2026-09-24 — yeni üretim; 100-250 görev şartı) | [[documentation/katman-readme-template]] |
| 36 | Alt Katman Template | Markdown, K{n}.a.b | Alt-katman dosyası (`architecture/<katman>/<kod>-<slug>.md`) | 158 | ✅ Mevcut (2026-09-24 — yeni üretim; 100-250 görev şartı) | [[documentation/alt-katman-template]] |

*(2026-09-23: envanter #1-#26 arasında yeniden numaralandırıldı; eski kayıtta #15 numarası hiç kullanılmamıştı — bu bilgi korunur.)* *(2026-09-24: documentation/ 2 yeni şablon eklendi — #18-#19; #18-#26 → #20-#28 kaydırıldı. Aynı gün ui-design/ 4 yeni şablon — #27-#30; meta #27-#28 → #31-#32, envanter #1-#32.)*

#### 7.1.7 Hardware Templates (hardware/)

| # | Template | Teknoloji | Amaç | Satır | Durum | Dosya |
|---|----------|-----------|------|-------|-------|-------|
| 20 | Hardware Template | Donanım genel | Hardware development | 512 | ✅ Mevcut (2026-09-23) | [[hardware/hardware-template]] |

*(Gerçek disk durumu: `hardware/` altında TEK dosya vardır — `hardware-template.md`. Eski kayıtta geçen `arduino`/`avr`/`pic` şablonları diskte YOKTUR ve üretilmemiştir; §2 "Planlanan" listesindedir.)*

#### 7.1.8 Query Templates (query/)

| # | Template | Format | Amaç | Satır | Durum | Dosya |
|---|----------|--------|------|-------|-------|-------|
| 21 | Query Template | SQL, MySQL 9 | Veritabanı sorguları | 514 | ✅ Mevcut (2026-09-23) | [[query/Query-Template]] |

#### 7.1.9 Other Templates (other/)

| # | Template | Teknoloji | Amaç | Satır | Durum | Dosya |
|---|----------|-----------|------|-------|-------|-------|
| 22 | ASP.NET Template | ASP.NET 9, C# 13 | Enterprise backend | 517 | ✅ Mevcut (2026-09-23 — Faz 2 üretimi) | [[other/aspnet-template]] |
| 23 | C Template | C11, GCC | Embedded, drivers | 516 | ✅ Mevcut (2026-09-23 — Faz 2 üretimi) | [[other/c-template]] |
| 24 | C++ Template | C++20, GCC | Embedded, drivers | 501 | ✅ Mevcut (2026-09-23) | [[other/cpp-template]] |

#### 7.1.10 Session Templates (kök)

| # | Template | Format | Amaç | Satır | Durum | Dosya |
|---|----------|--------|------|-------|-------|-------|
| 25 | Session Log Template | Markdown | Oturum kaydı | 144 | ✅ Mevcut (2026-09-23, v2.0.0) — 500+ kuralı kapsamı dışı kısa şablon | [[session-log-template]] (kök) |

*(Eski kayıtta `session/` klasörü geçiyordu; diskte `session/` klasörü YOKTUR, dosya köktedir.)*

#### 7.1.11 Agents Templates (agents/)

| # | Template | Teknoloji | Amaç | Satır | Durum | Dosya |
|---|----------|-----------|------|-------|-------|-------|
| 26 | Agent Profile Template | Markdown | Agent profil şablonu | 527 | ✅ Mevcut (2026-09-23) | [[agents/agents-template]] |
| 34 | Agent Tartışma Türü Template | Markdown, 3 tur/20 persona | Multi-agent tartışma protokolü (Tur1 → Tur2 → Tur3+ADR) | 185 | ✅ Mevcut (2026-09-24 — yeni üretim; 100-250 görev şartı) | [[agents/agent-tartisma-turu-template]] |

#### 7.1.12 UI Design Templates (ui-design/)

| # | Template | Kalıp | Amaç | Satır | Durum | Dosya |
|---|----------|-------|------|-------|-------|-------|
| 27 | Reference/Spec/Token Template | A | Referans, spec, token dokümanı (`ui-design/` kök + `tokens/` + `reference/` + indexler) | 509 | ✅ Mevcut (2026-09-24 — yeni üretim) | [[ui-design/reference-template]] |
| 28 | Flow Template | B | Ekran akış dosyası (`flow/<kategori>/NN-*.md`) | 506 | ✅ Mevcut (2026-09-24 — yeni üretim) | [[ui-design/flow-template]] |
| 29 | Prompt Template | C | Kod üretim promptu (`prompt/{component,page,screen,layout}/*.md`) | 507 | ✅ Mevcut (2026-09-24 — yeni üretim) | [[ui-design/prompt-template]] |
| 30 | Screen Spec Template | D | Ekran spesifikasyonu (`screens/<tier>/*.md`) | 545 | ✅ Mevcut (2026-09-24 — yeni üretim) | [[ui-design/screen-spec-template]] |

*(Kalıp A-D tanımları: `.ai/ui-design/reference/legacy-inventory.md` §"Şablon kalıpları" — salt-okunur kaynak. Bu 4 şablon 500+ satır derinlik kuralını karşılar.)*

#### 7.1.13 Meta Dosyalar (kök — registry'nin kendisi)

| # | Dosya | Amaç | Satır | Durum | Not |
|---|-------|------|-------|-------|-----|
| 31 | Registry Index | Şablon registry indeksi (bu dosya) | — | ✅ Mevcut | `index.md` — self-referans; ölçüm anı kendi sayımı (bu yazımla değişir) |
| 32 | Templates CLAUDE | Klasör bağlam + değişiklik protokolü | — | ✅ Mevcut | `CLAUDE.md` — meta; ölçüm: 99 satır |

*(Satır sütunu: #1-#17 ve #20-#26 = 2026-09-23 gerçek disk ölçümü (Faz 2 üretim sonrası); #18-#19 ve #27-#30 = 2026-09-24 yeni üretim ölçümü; #33-#36 = 2026-09-24 son üretim ölçümü (katman/ADR/tartışma şablonları — kategori tablolarına eklendi, global dizi #33-#36; meta #31-#32 korundu, yeniden numaralandırma yok — bilgi korunumu ilkesi); eski 2026-08 tahmini değerler kaldırıldı. #31-#32 self-referans olduğu için sayısal iddia taşımaz — bkz. §6 "Ölçüm notu".)*

### 7.2 Wiki-link Referansları

- [[.templates/index]] — bu registry
- [[../CLAUDE.md]] — ana sözleşme
- [[../AGENTS.md]] — agent registry (Guardrail #16 routing)
- [[../WORKFLOW.md]] — süreçler
- [[ui-design/01-mockup-index]] — mockup indeksi (düzeltildi: `00-mockup-index` → `01-mockup-index`, diskte doğrulandı)
- [[ui-design/reference-template]] · [[ui-design/flow-template]] · [[ui-design/prompt-template]] · [[ui-design/screen-spec-template]] — UI Design şablonları (2026-09-24, Kalıp A-D)
- [[documentation/katman-readme-template]] · [[documentation/alt-katman-template]] — katman/alt-katman doküman şablonları (2026-09-24, K0-K20 / A0-A5)
- [[adr/adr-nygard-template]] — Nygard ADR şablonu (2026-09-24, `.ai/architecture/adr/` mimari seri)
- [[agents/agent-tartisma-turu-template]] — 3 turlu/20 persona tartışma şablonu (2026-09-24)

---

*Template Registry Index v4.4.0 — CoreMusic Template System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-24*
*Mode: Red Team · Human Mode · Truth Mode*
