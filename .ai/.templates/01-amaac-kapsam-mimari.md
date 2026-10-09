---
title: "Amaç, Kapsam & Mimari"
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
---# Amaç, Kapsam & Mimari## 1. Amaç

Bu dosya, CoreMusic ekosistemindeki tüm şablonların (template) merkezi indeksidir. Her şablonun amacını, teknoloji yığınıını ve kullanım alanını tanımlar. Şablon registry'sinin tek otoritesi bu dosyadır; hiçbir alt dosya kendini Single Source of Truth ilan edemez.

**⚠️ ZORUNLULUK (Guardrail #16):** Yeni dosya oluşturulurken bu listeden uygun template seçilmek ZORUNLU. Template olmadan dosya oluşturulamaz. AI ve insan geliştiriciler için aynı kural geçerlidir.

## 2. Kapsam ve Mimari Dizin Yapısı

Bu bölüm, `.templates/` dizininin disk gerçeğiyle birebir listingidir (2026-09-29 taze doğrulama — **41/41 dosya diskte**; önceki kayıtlar: 2026-09-26 38/38 (coremusic-vault-template) → 2026-09-26 37/37 (personas/persona-template) → 2026-09-24 36/36 → 32/32 → 28/28; 2026-09-23 Faz 2 kaydı: 26/26). Kullanıcılar: 11 agent profilinin tamamı (Guardrail #16) + insan geliştiriciler + Vault Steward.

```
.templates/
├── index.md · CLAUDE.md · session-log-template.md · coremusic-vault-template.md                     (kök — 4)
├── adr/adr-template.md · adr-frontend-template.md · adr-database-template.md ·
│   adr-security-template.md · adr-audio-template.md · adr-index.md ·
│   adr-nygard-template.md                                             (7)
├── agents/agents-template.md · agent-tartisma-turu-template.md        (2)
├── backend/php-template.md · nodejs-template.md                        (2)
├── documentation/api-doc-template.md · security-audit-template.md ·
│   WikiPage-Template.md · claude-md-template.md · docs-md-template.md ·
│   katman-readme-template.md · alt-katman-template.md ·
│   agents-md-template.md · workflow-md-template.md ·
│   context-md-template.md · rag-md-template.md                        (11)
├── frontend/css-template.md · css-abstracts-token-template.md ·
│   css-component-template.md · css-page-template.md ·
│   css-device-template.md · css-auth-device-template.md ·
│   css-utility-template.md · css-helper-template.md · js-template.md ·
│   context-template.md · css-base-template.md · css-layout-template.md ·
│   css-vendor-template.md · css-viewmode-template.md · css-oauth-template.md ·
│   css-device-token-template.md                                    (16)
├── hardware/arduino-template.md · avr-template.md · pic-template.md ·
│   hardware-template.md                                  (4)
├── infrastructure/github-actions-template.md · migration-template.md   (2)
├── other/cpp-template.md · aspnet-template.md · c-template.md          (3)
├── personas/persona-template.md                                        (1)
├── query/Query-Template.md                                             (1)
├── testing/phpunit-template.md · vitest-template.md                    (2)
├── prompt-maker/template.md (1546) · README.md (102) ·
│   formats/format-templates.md (557) ·
│   examples/coremusic-backend-api.md (565) ·
│   examples/coremusic-frontend-vanilla.md (505) ·
│   examples/coremusic-security-audit.md (506)                      (6)
└── ui-design/reference-template.md · flow-template.md ·
    prompt-template.md · screen-spec-template.md                        (4)
```

**Toplam: 65 dosya** = 63 şablon (61 klasör içi + `session-log-template.md` ve `coremusic-vault-template.md` kök) + 2 meta (`index.md`, `CLAUDE.md`). **Kategori: 13 dizin + kök.** `session/` klasörü YOKTUR (kayıt köktedir). **(2026-10-07 disk ölçümü: `find .templates -name "*.md"` = 61 dosya / 26.049 satır — gövde sayımı bu ölçümle hizalandı; önceki gövde kayıtları 7 `frontend/` dosyasını (`context`, `css-base`, `css-layout`, `css-vendor`, `css-viewmode`, `css-oauth`, `css-device-token`) saymıyordu.)** *(2026-09-24: +2 şablon — `claude-md-template`, `docs-md-template`; aynı gün +4 şablon — `ui-design/` altı: `reference-template`, `flow-template`, `prompt-template`, `screen-spec-template`; aynı gün son +4 şablon — `documentation/katman-readme`, `documentation/alt-katman`, `adr/adr-nygard`, `agents/agent-tartisma-turu` → #33-#36, §7.1'e bak.)* *(2026-09-26: +1 şablon — `personas/persona-template` → #37, §7.1.14'e bak.)* *(2026-09-26: +1 şablon — `coremusic-vault-template` → #38, §7.1.10'a bak.)* *(2026-09-29: +3 şablon — `hardware/arduino-template`, `hardware/avr-template`, `hardware/pic-template` → #39-#41, §7.1.7'ye bak; Faz 6 defteri kapandı.)* *(2026-10-03: **+7 şablon** — `frontend/` CSS katman şablonları #42-#48 (`css-abstracts-token`, `css-component`, `css-page`, `css-device`, `css-auth-device`, `css-utility`, `css-helper`) + `css-template` v3.0.0 yeniden yazım → 48/48, §7.1.3'e bak.)* *(2026-10-07: **+6 dosya** — `prompt-maker/` kategorisi: `template.md` (v1.1.0, §0 min-500 çıktı sözleşmesi), `README.md`, `formats/format-templates.md`, `examples/` ×3 (her biri ≥500 satır) → **61/61 disk ölçümü**, §7.1.15'e bak; React örneği silindi — Forbidden Pattern, `coremusic-frontend-vanilla.md` ile değiştirildi.)* *(2026-10-07: **+4 şablon** — `documentation/` kategorisi: `agents-md-template` (#62), `workflow-md-template` (#63), `context-md-template` (#64), `rag-md-template` (#65) → **65/65 disk ölçümü**, §7.1.6'ya bak; AI analiz seti (CLAUDE/AGENTS/WORKFLOW/RAG/CONTEXT) görevi.)*

✅ **2026-09-23 üretim tamamlandı — 26/26 mevcut** (2026-09-24: +2 → 28/28 — documentation/claude-md + docs-md, §7.1.6'ya bak; aynı gün +4 → 32/32 — ui-design/*, §7.1.12'ye bak; aynı gün +4 → **36/36** — katman/ADR/tartışma şablonları, §7.1.1/#7.1.6/#7.1.11'e bak) (Faz 2'de üretilen `adr-frontend`, `adr-database`, `adr-security`, `adr-audio`, `adr-index`, `aspnet`, `c` artık disktedir). (2026-09-26: +1 → **37/37** — `personas/persona-template`, §7.1.14'e bak; aynı gün +1 → **38/38** — `coremusic-vault-template`, §7.1.10'a bak) (2026-09-29: +3 → **41/41** — `hardware/{arduino,avr,pic}-template`, §7.1.7'ye bak — Faz 6 defteri kapandı)

### Planlanan (diskte hâlâ yok — Faz 6 defteri)

**✅ 2026-09-29 KAPANDI — 3/3 dosya üretildi ve diskte:** `hardware/arduino-template.md` (766), `hardware/avr-template.md` (650), `hardware/pic-template.md` (687). Kaynak: eski vault `.ai/.templates/` (arduino 1273 / avr 1119 / pic 1205 satır) → güncel 7 alanlı frontmatter + §1-§7 iskeletine dönüştürüldü; sürüm/standart iddiaları web'den doğrulandı (avr-gcc 15/16, XC8 4.00, MPLAB X 6.35, EN IEC 63000, MISRA C:2025). Artık planlanan dosya YOKTUR.

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

