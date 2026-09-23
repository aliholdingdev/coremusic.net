---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — Architecture Decision Record Template"
type: adr-template
category: template
date: {{DATE}}
updated: {{DATE}}
status: draft
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/brain.md"
---

# {{TITLE}}

**Durum:** {{STATUS}} (Draft/Review/Active/Frozen)
**Tarih:** {{DATE}}
**Karar Veren:** {{AUTHOR}}
**İlgili ADR'ler:** {{RELATED_ADRS}}

---

## 1. Bağlam (Context)

<!-- Bu karar neden alındı? Hangi sorun çözülüyor? -->

{{CONTEXT_DESCRIPTION}}

### 1.1 Mevcut Durum

{{CURRENT_STATE}}

### 1.2 Sorun Tanımı

{{PROBLEM_STATEMENT}}

### 1.3 Web den araşrıma rpaoru & Sonucları

{{Web Search **Query**}}
{{Web Search **Konusu**}}

{{Web Search **BAĞLAM**}}

{{Web Search **Kısa Acıklam**a}}

{{Web Search **uzun Acıklama**}}

{{Web Search **Paragraf Veri uzun**}}

{{Web Search **Sonucu**}}

{{Web Search **Alınan Kara**r}}

{{Web Search **Sonu**c}}

### 1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| {{CONSTRAINT_1}} | {{CONSTRAINT_1_DESC}} |
| {{CONSTRAINT_2}} | {{CONSTRAINT_2_DESC}} |

---

## 2. Karar (Decision)

<!-- Ne kararı alındı? -->

{{DECISION_DESCRIPTION}}

### 2.1 Neden Bu Seçenek?

{{RATIONALE}}

### 2.2 Teknik Detaylar

{{TECHNICAL_DETAILS}}

---

## 3. Alternatifler (Alternatives)

<!-- Hangi alternatifler değerlendirildi? -->

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|-----------------|
| 1 | {{ALT_1}} | {{ALT_1_PROS}} | {{ALT_1_CONS}} | {{ALT_1_REJECT}} |
| 2 | {{ALT_2}} | {{ALT_2_PROS}} | {{ALT_2_CONS}} | {{ALT_2_REJECT}} |
| 3 | {{ALT_3}} | {{ALT_3_PROS}} | {{ALT_3_CONS}} | {{ALT_3_REJECT}} |

---

## 4. Sonuçlar (Consequences)

<!-- Bu kararın sonuçları neler? -->

### 4.1 Olumlu Sonuçlar

- {{POSITIVE_1}}
- {{POSITIVE_2}}

### 4.2 Olumsuz Sonuçlar

- {{NEGATIVE_1}}
- {{NEGATIVE_2}}

### 4.3 Riskler

| Risk | Olasılık | Etki | Mitigasyon |
|------|---------|------|-----------|
| {{RISK_1}} | {{RISK_1_PROB}} | {{RISK_1_IMPACT}} | {{RISK_1_MITIG}} |

---

## 5. Uygulama (Implementation)

<!-- Nasıl uygulanacak? -->

### 5.1 Adımlar

| # | Adım | Sorumlu | Süre |
|---|------|---------|------|
| 1 | {{STEP_1}} | {{STEP_1_OWNER}} | {{STEP_1_DURATION}} |
| 2 | {{STEP_2}} | {{STEP_2_OWNER}} | {{STEP_2_DURATION}} |

### 5.2 Geri Dönüş Planı

{{ROLLBACK_PLAN}}

---

## 6. İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| [[CLAUDE.md]] | Ana sözleşme |
| [[brain.md]] | Mimari kararlar |
| [[WORKFLOW.md]] | Süreçler |

---

## 7. Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | {{AUTHOR}} | {{DATE}} | ✅ |
| Tech Lead | {{TECH_LEAD}} | {{DATE}} | ⏳ |
| Arch Lead | {{ARCH_LEAD}} | {{DATE}} | ⏳ |

---

*ADR Template v1.0.0 — CoreMusic Architecture Decision Record*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: {{DATE}}*
*Mode: Red Team · Human Mode · Truth Mode*
