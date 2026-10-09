---
title: "Doğrulama & Referanslar"
type: template-index
category: template
version: 4.10.0
status: active
authority: "Template (Guardrail #16) — Registry: .ai/.templates/index.md"
updated: 2026-10-07
date: 2026-08-09
governance: Red Team · Human Mode · Truth Mode
total_templates: 63
total_files: 65
total_lines: 26813
---# Doğrulama & Referanslar## 6. Doğrulama

- [ ] 7 alanlı frontmatter var (`title`, `type`, `category`, `version`, `status`, `authority`, `updated`)
- [ ] 8 bölüm var (H1 + §1-§7)
- [ ] tüm `{{PLACEHOLDER}}`'lar dolduruldu
- [ ] dosya bu şablona uygun
- [ ] `total_*` sayıları disk sayımıyla senkron

**Quality Report (registry metrikleri):**

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 4.10.0 |
| **Toplam Dosya (registry)** | 65 md (63 şablon + 2 meta: index.md, CLAUDE.md) |
| **Toplam Template** | 63 kayıt (`total_templates` = registry kayıtları = şablon sayısı) |
| **500+ derinlik** | 37/63 şablon 500+ satır (ölçüm 2026-10-07: `find \| wc -l` — ge500=37, <500=28/65 dosya, meta 2 hariç) — **26 şablon bilinçli olarak <500**: `session-log` 143 + 14 frontend CSS katman şablonu (132-186) + 4 katman şablonu + 4 yeni doküman şablonu (`agents-md` 179 · `workflow-md` 177 · `context-md` 178 · `rag-md` 195) + `adr-nygard` 208 + `agent-tartisma` 184 + `prompt-maker/README` 102 (görev şartı: açıklamalı dolgu 100-250 satır) — eski kayıt: 33/37 (2026-09-29) |
| **Kısa şablon** | 9 — `session-log-template.md` (143 satır; 500+ kuralı kapsamı dışı, bilinçli kısa şablon) + 4 katman şablonu + 4 yeni doküman şablonu 100-250 aralığında (`agents-md` 179 · `workflow-md` 177 · `context-md` 178 · `rag-md` 195 — 2026-10-07, görev şartı: açıklamalı dolgu 100-250 satır) |
| **Planlanan (Faz 6)** | **0 — KAPANDI (2026-09-29)**: `arduino` (766), `avr` (650), `pic` (687) üretildi → §7.1.7 |
| **Toplam Satır** | 26.813 (2026-10-07, 65 md dosyası; `find -name "*.md" \| cat \| wc -l` sayımı) — eski: 26.049 (2026-10-07, 61 md) · 20.757 (2026-10-03, 48 md) · 20.088 (2026-09-29, 41 md) · 17.984 (2026-09-28, 38 md) · 17.209 (2026-09-26, 37 md) · 16.503 (2026-09-24, 36 md) · 15.764 (32 md) · 14.695 (28 md) · 12.549 (2026-09-23, 26 md) |
| **Ortalama Satır/Template** | 412 (26.813 ÷ 65 dosya, 2026-10-07) |
| **Minimum Satır** | 98 (templates/CLAUDE.md — meta) · şablon min 143 (`session-log-template.md`) · sonra 157 (`documentation/alt-katman-template.md`) |
| **Maksimum Satır** | 810 (coremusic-vault-template.md) — eski: 766 (`hardware/arduino-template.md`, 2026-09-29) · 695 (personas/persona-template.md) · 648 (frontend/js-template.md) |
| **Kategori** | 13 dizin (adr 7, agents 2, backend 2, documentation **11**, frontend **16**, hardware **4**, infrastructure 2, other 3, personas 1, prompt-maker 6, query 1, testing 2, ui-design 4) + kök (index.md, CLAUDE.md, session-log-template, coremusic-vault-template) |
| **Dizin Yapısı** | ✅ Alt dizinlere ayrılmış (§2 disk gerçeği) |
| **Frontmatter Uyumlu** | ✅ 39/39 7-alanlı FM (2026-09-23 betik doğrulaması: FM BAD=0; 2026-09-24 yeni 8 şablon; 2026-09-26 personas + coremusic-vault; 2026-09-29 hardware {arduino,avr,pic} de 7 alan) |
| **Ölçüm notu** | ✅ Tazelendi (2026-10-07): 65 md disk sayımı (`find . -name "*.md"`), 26.813 satır (`cat + wc -l`); `total_*` bu yazımla senkron (+4 şablon: `documentation/{agents-md,workflow-md,context-md,rag-md}` = 729 satır). Önceki ölçüm (2026-10-03): 48 md / 20.757 satır (`Get-Content .Count`); ondan önce 2026-09-29: 41 md / 20.088. |
| **Düzeltilen eski iddialar** | 25.000/5.546 satır → 12.549 · 17 şablon/19 dosya → 24 şablon/26 dosya · Planlanan 10 → 3 → **0 (2026-09-29 kapandı)** · ort. 292 → 490 · min 90/max 649 → 143/810 · "arduino/avr/pic diskte (2026-06 eski vault iddiası) → hardware tek dosya → **2026-09-29 4/4 dosya gerçekten diskte** · "10 klasör (session dahil)" → 12 dizin + kök, `session/` klasörü yok · 26 dosya/12.549 satır → 28/14.695 → 32/15.764 → 36/16.503 → 37/17.209 → 38/17.984 (2026-09-28) → **41 dosya/20.088 satır** (2026-09-29, +3: hardware arduino/avr/pic) · `ui-design` "115 dosya" → **136 dosya (119 md)** |

**REFACTOR REPORT:** FILE: index.md · PURPOSE: Template Registry Index (şablon registry + dizin) · VALIDATION: 7 alan + §1-§7 + bilgi korunumu + envanter 41/41 disk sayımı (2026-09-29, +3 şablon: hardware {arduino,avr,pic} — 5. geçiş, Faz 6 kapandı) · RELATED: [[.templates/index]] · [[../CLAUDE.md]]

## 7. Referanslar

### 7.1 Template List (registry tabloları — disk durumuyla, 2026-09-29 gerçek ölçüm)

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
| 9 | JavaScript Template | Vanilla JS ES6+ | Frontend development | 648 | ✅ Mevcut (2026-09-23) | [[frontend/js-template]] |
| 10 | CSS Template | ITCSS 11-layer, BEM, cihaz token ayrımı | Stylesheet development (ana şablon) | 407 | ✅ Mevcut (2026-10-03 — v3.0.0 yeniden yazım, disk kanıtı) | [[frontend/css-template]] |
| 42 | CSS Abstracts Token Template | 01_Abstracts, custom property | Token dosyası (`a-*.css`) — cihaz token ayrımı | 120 | ✅ Mevcut (2026-10-03 — katman şablonu) | [[frontend/css-abstracts-token-template]] |
| 43 | CSS Component Template | 04_Components, BEM | Bileşen CSS'i (`c-*.css`) | 134 | ✅ Mevcut (2026-10-03 — katman şablonu) | [[frontend/css-component-template]] |
| 44 | CSS Page Template | 05_Pages | PHP sayfası CSS'i (`p-*.css`) | 123 | ✅ Mevcut (2026-10-03 — katman şablonu) | [[frontend/css-page-template]] |
| 45 | CSS Device Template | 08_Devices | Cihaz import zinciri + davranış (`d-*.css`) | 109 | ✅ Mevcut (2026-10-03 — katman şablonu) | [[frontend/css-device-template]] |
| 46 | CSS Auth Device Template | 08_Devices/d-auth-* + auth-bundled | Auth subdomain cihaz varyantı (`d-auth-*.css`) | 109 | ✅ Mevcut (2026-10-03 — katman şablonu) | [[frontend/css-auth-device-template]] |
| 47 | CSS Utility Template | 06_Utilities | Utility sınıf (`u-*.css`) | 94 | ✅ Mevcut (2026-10-03 — katman şablonu) | [[frontend/css-utility-template]] |
| 48 | CSS Helper Template | 10_Helpers | Helper desen (`h-*.css`) | 111 | ✅ Mevcut (2026-10-03 — katman şablonu; katman diskte bekleniyor) | [[frontend/css-helper-template]] |
| 49 | Context Documentation Template | Markdown, 7-bölüm iskelet | Klasör context dokümanı — CONTEXT/CLAUDE/AGENTS/WORKFLOW ortak iskelet, `docType` ayrımı | 215 | ✅ Mevcut (2026-10-03) | [[frontend/context-template]] |
| 50 | CSS Base Template | 02_Base, önek `b-`/`l-`/`page-` | Bare HTML reset + site geneli giriş iskeleti (token tüketir, üretmez) | 163 | ✅ Mevcut (2026-10-06 — katman şablonu) | [[frontend/css-base-template]] |
| 51 | CSS Layout Template | 03_Layout, önek `_` | Sayfa düzeni — header/footer/sidebar/widget grid (`_{{konu}}.css`) | 142 | ✅ Mevcut (2026-10-06 — katman şablonu) | [[frontend/css-layout-template]] |
| 52 | CSS ViewMode Template | 09_ViewModes, önek `v-` | Görünüm modu (home/pro/studio/car) token override (`v-{{mode}}.css`) | 151 | ✅ Mevcut (2026-10-06 — katman şablonu) | [[frontend/css-viewmode-template]] |
| 53 | CSS OAuth Template | 11_OAuth, `oauth.css` (öneksiz) | OAuth/giriş (social login) akış stilleri — en dar katman, Security denetimi | 160 | ✅ Mevcut (2026-10-06 — katman şablonu) | [[frontend/css-oauth-template]] |
| 54 | CSS Vendor Template | 07_Vendors, `v-*` + upstream `bootstrap*` | 3. taraf stiller (Bootstrap ailesi) — salt okunur karantina katmanı | 132 | ✅ Mevcut (2026-10-06 — katman şablonu) | [[frontend/css-vendor-template]] |
| 55 | CSS Device Token Template | 01_Abstracts, `a-layout-tokens-{width}.css` | Cihaz genişliği layout token'ı — BASE (1024) + cihaz override ayrımı | 165 | ✅ Mevcut (2026-10-06 — katman şablonu) | [[frontend/css-device-token-template]] |

#### 7.1.3a Prompt Maker Templates (prompt-maker/)

> **Çıktı sözleşmesi (v1.1.0 §0):** Bu kategorideki şablonla üretilen HER final prompt
> **≥ 500 satır** olmalıdır (3-PASS: taslak → derinleştirme → sayım); kısa teslim YASAK.

| # | Template | Teknoloji | Amaç | Satır | Durum | Dosya |
|---|----------|-----------|------|-------|-------|-------|
| 56 | Prompt Maker Template | 21 bölüm + §0 | Ana prompt maker — min-500 satır çıktı sözleşmesi, 24 bölüm bütçesi, 3-PASS | 1546 | ✅ Mevcut (2026-10-07 — v1.1.0) | [[prompt-maker/template]] |
| 57 | Prompt Maker Format Templates | 8 format | Format Seçim Matrisi + SYSTEM/TASK/REVIEW/AUDIT/RE/REFACTOR/ARCH/MIGRATION iskeletleri + format min bütçeleri | 557 | ✅ Mevcut (2026-10-07) | [[prompt-maker/formats/format-templates]] |
| 58 | PM Example — Backend API | Format 2 TASK, PHP 8.4 | Streaming endpoint örneği (üretilmiş, ≥500 satır) | 565 | ✅ Mevcut (2026-10-07) | [[prompt-maker/examples/coremusic-backend-api]] |
| 59 | PM Example — Frontend Vanilla | Format 2 TASK, Vanilla JS | Footer player örneği (≥500 satır; React örneği silindi — Forbidden Pattern) | 505 | ✅ Mevcut (2026-10-07) | [[prompt-maker/examples/coremusic-frontend-vanilla]] |
| 60 | PM Example — Security Audit | Format 4 AUDIT, OWASP Top 10:2025 | Güvenlik denetimi örneği (≥500 satır) | 506 | ✅ Mevcut (2026-10-07) | [[prompt-maker/examples/coremusic-security-audit]] |
| 61 | Prompt Maker README | Dizin rehberi | §0 özeti, dosya rolleri, kullanım akışı | 102 | ✅ Mevcut (2026-10-07) | [[prompt-maker/README]] |

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
| 62 | AGENTS.md Template | Markdown, master agent rules pointer | Kök `AGENTS.md` (boot pointer) üretimi | 179 | ✅ Mevcut (2026-10-07 — yeni üretim; 100-250 görev şartı) | [[documentation/agents-md-template]] |
| 63 | WORKFLOW.md Template | Markdown, workflow pointer + WARNING | `WORKFLOW.md` (süreç pointer) üretimi | 177 | ✅ Mevcut (2026-10-07 — yeni üretim; 100-250 görev şartı) | [[documentation/workflow-md-template]] |
| 64 | CONTEXT.md Template | Markdown, envanter + `docType: context` | `CONTEXT.md` envanter üretimi | 178 | ✅ Mevcut (2026-10-07 — yeni üretim; 100-250 görev şartı) | [[documentation/context-md-template]] |
| 65 | RAG.md Template | Markdown, retrieval indeksi + pipeline | `RAG.md` (indeks + durum-sütunlu pipeline) üretimi | 195 | ✅ Mevcut (2026-10-07 — yeni üretim; 100-250 görev şartı) | [[documentation/rag-md-template]] |

*(2026-09-23: envanter #1-#26 arasında yeniden numaralandırıldı; eski kayıtta #15 numarası hiç kullanılmamıştı — bu bilgi korunur.)* *(2026-09-24: documentation/ 2 yeni şablon eklendi — #18-#19; #18-#26 → #20-#28 kaydırıldı. Aynı gün ui-design/ 4 yeni şablon — #27-#30; meta #27-#28 → #31-#32, envanter #1-#32.)*

#### 7.1.7 Hardware Templates (hardware/)

| # | Template | Teknoloji | Amaç | Satır | Durum | Dosya |
|---|----------|-----------|------|-------|-------|-------|
| 20 | Hardware Template | Donanım genel | Hardware development | 511 | ✅ Mevcut (2026-09-23) | [[hardware/hardware-template]] |
| 39 | Arduino Template | Arduino, PlatformIO, C++17 | Arduino firmware governance | 766 | ✅ Mevcut (2026-09-29 — Faz 6 üretimi) | [[hardware/arduino-template]] |
| 40 | AVR Template | AVR bare-metal, avr-gcc, C11 | AVR firmware governance | 650 | ✅ Mevcut (2026-09-29 — Faz 6 üretimi) | [[hardware/avr-template]] |
| 41 | PIC Template | Microchip PIC, XC8, C99 | PIC firmware governance | 687 | ✅ Mevcut (2026-09-29 — Faz 6 üretimi) | [[hardware/pic-template]] |

*(2026-09-29: `hardware/` altında artık 4 dosya vardır — Faz 6 defterindeki `arduino`/`avr`/`pic` şablonları eski vault içeriğinden güncel iskelete dönüştürülerek üretildi; registry'ye #39-#41 olarak eklendi (meta #31-#32 korundu, yeniden numaralandırma yok).)*

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
| 38 | CoreMusic Vault Template | Markdown, playbook (vault iskeleti) | Yeniden kullanılabilir `.ai/` vault iskeleti (başka projeye proje şablonu aktarımı) | 810 | ✅ Mevcut (2026-09-26 — registry hizalama) | [[coremusic-vault-template]] (kök) |

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

#### 7.1.14 Persona Templates (personas/)

| # | Template | Format | Amaç | Satır | Durum | Dosya |
|---|----------|--------|------|-------|-------|-------|
| 37 | Persona Template | Markdown, 8-bölüm + 11 alan havuzu | Persona profili (`.ai/personas/<segment>/kirik-ad-surname-mood.md`) | 695 | ✅ Mevcut (2026-09-26 — yeni üretim) | [[personas/persona-template]] |

*(2026-09-26: `personas/` klasörü açıldı — klasör sayısı 11 → 12; envanter #1-#36 üzerine **#37** eklendi, meta #31-#32 korundu (yeniden numaralandırma yok — bilgi korunumu ilkesi).)*

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
- [[personas/persona-template]] — persona şablonu (2026-09-26, 8-bölüm + 11 alan havuzu, 695 satır)
- [[coremusic-vault-template]] — vault iskeleti şablonu (2026-09-26, yeniden kullanılabilir .ai/ iskeleti, 810 satır)
- [[hardware/arduino-template]] · [[hardware/avr-template]] · [[hardware/pic-template]] — hardware teknoloji şablonları (2026-09-29, Faz 6: 766 · 650 · 687 satır)
- [[frontend/css-abstracts-token-template]] · [[frontend/css-component-template]] · [[frontend/css-page-template]] · [[frontend/css-device-template]] · [[frontend/css-auth-device-template]] · [[frontend/css-utility-template]] · [[frontend/css-helper-template]] — CSS katman şablonları (2026-10-03, #42-#48; ana şablon [[frontend/css-template]] v3.0.0)
- [[frontend/css-base-template]] · [[frontend/css-layout-template]] · [[frontend/css-viewmode-template]] · [[frontend/css-oauth-template]] · [[frontend/css-vendor-template]] · [[frontend/css-device-token-template]] — CSS katman şablonları (2026-10-06, #50-#55: 02_Base · 03_Layout · 09_ViewModes · 11_OAuth · 07_Vendors · 01_Abstracts cihaz token)

---

*Template Registry Index v4.9.0 — CoreMusic Template System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-10-06*
*Mode: Red Team · Human Mode · Truth Mode*
