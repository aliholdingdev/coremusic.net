---
title: "Kurallar & Workflow"
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
---# Kurallar & Workflow## 4. Kurallar

1. **Guardrail #16 — şablon zorunlu:** Yeni `.md`/kod dosyası, bu listeden uygun şablondan üretilir; şablonsuz dosya oluşturmak yasaktır.
2. **Registry otoritesi:** Şablon ekleme/çıkarma/güncelleme yalnızca bu dosyanın tablolarından yapılır; alt klasör `CLAUDE.md` dosyaları şablon listesi iddiası taşıyamaz.
3. **SSOT self-claim yasak:** Bu dosya dışındaki hiçbir şablon/klasör dosyası "Single Source of Truth" iddiasında bulunamaz; authority alanı şablonlarda `Template (Guardrail #16) — Registry: .ai/.templates/index.md` değerindedir.
4. **Frontmatter standardı:** Her şablon dosyasında 7 zorunlu alan bulunur: `title`, `type`, `category`, `version`, `status`, `authority`, `updated`.
5. **Sayı senkronu:** `total_*` alanları disk gerçeğiyle tutarlıdır — `total_templates: 63` (registry kaydı: 63 şablon), `total_files: 65` (63 şablon + 2 meta), `total_lines: 26813` (2026-10-07 `find -name "*.md" | cat | wc -l` sayımı, 65 md). Uyuşmazlık → güncelleme zorunlu.
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
| UI Designer | `frontend/js-template.md`, `frontend/css-template.md` + katman şablonları `frontend/css-abstracts-token-template.md`, `frontend/css-component-template.md`, `frontend/css-page-template.md`, `frontend/css-device-template.md`, `frontend/css-auth-device-template.md`, `frontend/css-utility-template.md`, `frontend/css-helper-template.md`, `adr/adr-frontend-template.md`, `ui-design/reference-template.md` (Kalıp A), `ui-design/flow-template.md` (Kalıp B), `ui-design/prompt-template.md` (Kalıp C), `ui-design/screen-spec-template.md` (Kalıp D) |
| Security Engineer | `adr/adr-security-template.md`, `documentation/security-audit-template.md` |
| Data Engineer | `adr/adr-database-template.md`, `query/Query-Template.md`, `infrastructure/migration-template.md` |
| Embedded Engineer | `other/c-template.md`, `adr/adr-audio-template.md`, `hardware/avr-template.md`, `hardware/pic-template.md`, `hardware/arduino-template.md` |
| QA Engineer | `testing/phpunit-template.md`, `testing/vitest-template.md`, `personas/persona-template.md` |
| DevOps Engineer | `infrastructure/github-actions-template.md` |
| Audio Hardware Engineer | `hardware/hardware-template.md`, `adr/adr-audio-template.md` |
| DSP Firmware Engineer | `other/c-template.md`, `hardware/hardware-template.md` |
| Windows Software Engineer | `other/c-template.md` |
| Enterprise Prompt Architect | `documentation/docs-md-template.md` — çok bölümlü Markdown prompt çıktısı (atama 2026-10-06; `ui-design/prompt-template` Kalıp C yalnız frontend kod promptu üretirse devrede) |
| Prompt Normalization Architect | `documentation/docs-md-template.md` — 23 bölümlük normalize prompt çıktısı (atama 2026-10-06) |
| Visual Render Studio | `documentation/docs-md-template.md` — image/negative prompt çıktısı (atama 2026-10-06) |
| Image Analysis Engineer | `documentation/docs-md-template.md` — kanıt tabanlı analiz raporu (atama 2026-10-06) |
| UI/UX Analyzer | `documentation/docs-md-template.md` — ASCII layout + FACT/INFERENCE raporu (atama 2026-10-06) |
| Electronics Engineer | `hardware/hardware-template.md`, `adr/adr-audio-template.md` (BOM/tedarik bölümü ⚠️ VERIFICATION REQUIRED — dedicated template yok) |
| Tüm ajanlar (tartışma protokolü) | `agents/agent-tartisma-turu-template.md` (3 tur/20 persona — `agent-debate` becerisi) |

✅ **2026-09-23 üretim tamamlandı — 26/26 mevcut** (2026-09-24: +2 → 28/28, +4 → **32/32**; yeni atıflar §7.1.6 documentation ve §7.1.12 ui-design satırlarında). Tablodaki `adr-index`, `adr-frontend`, `adr-database`, `adr-security`, `adr-audio`, `c-template` atıfları disktedir; `arduino`/`avr`/`pic` atıfları **2026-09-29'da üretilip** `hardware/arduino-template`, `hardware/avr-template`, `hardware/pic-template` olarak disktedir (§7.1.7).

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
| Persona üretimi (Level 1/2/3) | `personas/persona-template.md` (8-bölüm + 11 alan havuzu, ≥500 satır) |

✅ **2026-09-23 üretim tamamlandı — 26/26 mevcut** (2026-09-24: +2 → 28/28, +4 → **32/32** — ui-design 3 yeni workflow satırı). Bu tablodaki `adr-security` atıfı disktedir; Hardware Design satırı artık `hardware/` altındaki 4 şablonu kapsar (`hardware-template`, `arduino`, `avr`, `pic` — son 3'ü 2026-09-29'da üretildi, §7.1.7).

