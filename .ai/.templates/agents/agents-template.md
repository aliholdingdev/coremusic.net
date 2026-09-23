---
title: "CoreMusic — Agent Template"
type: template
category: template
version: 2.0.0
status: active
authority: "Template (Guardrail #16) — Registry: .ai/.templates/index.md"
updated: 2026-09-23
governance:
  - Red Team
  - Human Mode
  - Truth Mode
---

# CoreMusic — Agent Profile Template

**Zorunlu Bağlantılar / See also:** [[.templates/index]] · [[../CLAUDE.md]] · [[../../AGENTS.md]] · [[../../.agents/AGENTS.md]]

## 1. Amaç

Bu şablon, CoreMusic ekosistemindeki bir agent profil dosyasının (`.ai/.agents/*.md`) üretilmesi için zorunludur. **Guardrail #16:** yeni agent profili oluşturulurken bu şablon kullanılmak ZORUNLUDUR; şablonsuz profil dosyası yazılamaz. Şablon, agent'ın kimliğini, yetki sınırlarını, mimari kurallarını, workflow'unu, handover ve failure handling alanlarını standart biçimde taşır.

## 2. Kapsam

- **Geçerli dosya tipi:** agent profil dosyaları (`.ai/.agents/<agent-adı>.md`) ve profil revizyonları.
- **Kullananlar:** Master Orchestrator (profil koordinasyonu), Vault Steward (kayıt), ilgili uzman agent'ların profillerini dolduran editörler.
- **Kapsam dışı:** agent routing/handover/escalation kurallarının kendisi (→ `[[../../AGENTS.md]]`); şablon listesi (→ `[[.templates/index]]`).

## 3. Mimari

Şablonun tam iskeleti (frontmatter + 11 bölüm, eksiksiz):

````markdown
---
title: "CoreMusic — Agent Template"
type: agent-profile-template

version:
  1.0.0

authority:
  Single Source of Truth (SSOT)

governance:
  - Red Team
  - Human Mode
  - Truth Mode
---

# CoreMusic — [Agent Name]


# 1. Agent Identity


| Field | Value |
|------|--------|
| Name | |
| Code Name | |
| Domain | |
| Layer | |
| Priority | |
| Status | ACTIVE |
---

# 2. Mission
Bu agent:
- 
- 
- 
sorumludur.
---

# 3. Responsibilities
| # | Responsibility |
|-|-|
| 1 | |
| 2 | |
| 3 | |


---

# 4. Allowed Scope



Allowed:



---

# 5. Forbidden Scope



Forbidden:



---

# 6. Technology Stack


| Area | Technology |
|-|-|
| Language | |
| Framework | |
| Database | |
| Tools |


---

# 7. Architecture Rules


Must follow:


- SOLID
- Clean Architecture
- Domain Boundary
- ADR Decisions
- SSOT Rules


---

# 8. Workflow



READ

↓

PLAN

↓

IMPLEMENT

↓

TEST

↓

VALIDATE

↓

LOG



---

# 9. Handover



FROM:

TO:

TASK:

STATUS:

FILES:

VALIDATION:



---

# 10. Failure Handling



STATUS:

REASON:

ACTION:



---

# 11. Version


| Version | Date | Change |
|-|-|-|
|1.0.0|2026-09-23|Created|
````

## 4. Kurallar

1. **Guardrail #16:** Profil dosyası bu şablondan üretilir; dış iskelet H1 + §1-§11 (Identity → Version) silinemez.
2. **Mimari kurallar (iskelet §7 — profil içinde zorunlu):** SOLID · Clean Architecture · Domain Boundary · ADR Decisions · SSOT Rules.
3. **Allowed/Forbidden ayrımı zorunlu (iskelet §4-§5):** Her profilin Allowed Scope ve Forbidden Scope alanları doldurulur; boş profil yayımlanamaz.
4. **SSOT self-claim:** Bu şablon dosyasının kendi frontmatter'ı registry'ye bağlıdır (`Template (Guardrail #16)`); profil örneğindeki eski `authority: Single Source of Truth (SSOT)` değeri iskelet örneği olarak korunur — profil doldurulurken profilin gerçek authority değeri yazılır.
5. **Registry senkronu:** Yeni profil eklendiğinde registry (`[[.templates/index]]`) + kök referans tabloları + log güncellenir (Değişiklik Protokolü).
6. **Belirsizlik:** Doğrulanamayan alan `⚠️ VERIFICATION REQUIRED` ile işaretlenir.

## 5. Workflow

ŞABLONU SEÇ → KOPYALA → `{{PLACEHOLDER}}` DOLDUR → GUARDRAIL #16 DOĞRULA → COMMIT

1. **ŞABLONU SEÇ:** `agents/agents-template.md` (`.ai/.templates/agents/` altında).
2. **KOPYALA:** `.ai/.agents/<agent-adı>.md` yolu hedefine kopyala.
3. **DOLDUR:** Identity / Mission / Responsibilities / Allowed / Forbidden / Technology Stack alanlarını doldur; Workflow ve Handover alanları profil sahibine göre güncellenir; Version tablosuna revizyon satırı ekle.
4. **GUARDRAIL #16 DOĞRULA:** §6 kontrol listesi.
5. **COMMIT:** `[[../../AGENTS.md]]` §15 profil linki + registry + log güncellemesiyle birlikte commit edilir.

## 6. Doğrulama

- [ ] 7 alanlı frontmatter var (`title`, `type`, `category`, `version`, `status`, `authority`, `updated`)
- [ ] 8 bölüm var (H1 + §1-§7)
- [ ] tüm `{{PLACEHOLDER}}`'lar dolduruldu (profilde: Name / Code Name / Domain / Layer / Priority dolu)
- [ ] dosya bu şablona uygun (agent profil dosyası)
- [ ] §4 Allowed + §5 Forbidden + §7 Architecture Rules dolduruldu

**REFACTOR REPORT:** FILE: agents-template.md · PURPOSE: Agent profil şablonu (Guardrail #16) · VALIDATION: 7 alan + §1-§7 + bilgi korunumu · RELATED: [[.templates/index]] · [[../CLAUDE.md]]

## 7. Referanslar

- [[.templates/index]] — Template Registry (bu şablonun kaydı)
- [[../CLAUDE.md]] — ana sözleşme
- [[../../AGENTS.md]] — Agent Registry (profil tüketicisi)
- [[../../.agents/AGENTS.md]] — agent profilleri (hedef dizin)
- [[../../WORKFLOW.md]] — süreçler
