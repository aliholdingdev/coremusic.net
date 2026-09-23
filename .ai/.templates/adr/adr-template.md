---
title: "CoreMusic — Architecture Decision Record Template"
type: template
category: template
version: 2.0.0
status: active
authority: "Template (Guardrail #16) — Registry: .ai/.templates/index.md"
updated: 2026-09-23
date: 2026-09-23
reference_doc: Freelancer Technical Documentation v1.0
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/brain.md"
---

# CoreMusic — Architecture Decision Record Template

**Zorunlu Bağlantılar / See also:** [[.templates/index]] · [[../CLAUDE.md]] · [[../../AGENTS.md]] · [[CLAUDE.md]] · [[brain.md]] · [[WORKFLOW.md]]

## 1. Amaç

Bu şablon, CoreMusic mimari kararlarının (Architecture Decision Record — ADR) standart biçimde kaydedilmesi için zorunludur. **Guardrail #16:** yeni bir ADR dosyası oluşturulurken bu şablondan başlamak ZORUNLUDUR. Şablon; Bağlam (Context), Karar (Decision), Alternatifler, Sonuçlar, Uygulama ve Onay bölümlerini sabitleyerek kararların izlenebilir, gerekçeli ve denetlenebilir kalmasını sağlar. **ADR-042 hibrit kuralı:** `.templates/*` dosyaları tam yeniden yazıma açıktır (bu dosya v2.0.0 ile yeniden yazıldı).

## 2. Kapsam

- **Geçerli dosya tipi:** ADR dosyaları — adlandırma: `ADR-NNN-<slug>.md` (ör. `ADR-008-bypass-auth-middleware.md`, `ADR-017-dsp-hardware-mode.md` — `[[../../AGENTS.md]]` wiki-link kanıtı).
- **Durum döngüsü:** Draft → Review → Active → Frozen (iskelet künyesi: `{{STATUS}} (Draft/Review/Active/Frozen)`).
- **Kullananlar:** Vault Steward (yazar + onay), Tech Lead (onay), Arch Lead (onay); karar domainini üreten uzman agent'lar (Backend/Security/Data/Embedded vb.).

## 3. Mimari

Şablonun tam iskeleti (placeholder'lı frontmatter + H1 + 7 domain bölümü, eksiksiz):

````markdown
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
````

## 4. Kurallar

1. **Guardrail #16:** Yeni ADR bu şablondan üretilir; dış iskelet (künye + §1 Bağlam → §7 Onay) silinemez, yalnızca alanlar doldurulur.
2. **Durum akışı:** Draft → Review → Active → Frozen; `status` alanı gerçek durumla yazılır (şablon dosyasının kendi `status` değeri `active`'dir).
3. **Frozen dokunulmaz:** Frozen ADR metinleri okunur ve referans edilir — değiştirilmez (AGENTS.md §25.3 kural 2).
4. **Onay zorunlu:** §7 Onay tablosu Vault Steward ✅ → Tech Lead ⏳ → Arch Lead ⏳ akışını tamamlamadan karar Active olamaz.
5. **Placeholder disiplini:** Tüm `{{...}}` alanları (özellikle §1.3 web araştırması raporu ve §5.2 geri dönüş planı) gerçek değerlerle doldurulur; boş ADR yayımlanamaz. `⚠️ VERIFICATION REQUIRED` etiketi bilinmeyen bilgi için kullanılır.
6. **Authority ayrımı:** Bu şablon dosyasının kendi frontmatter'ı registry'ye bağlıdır (`Template (Guardrail #16) — Registry: .ai/.templates/index.md`); §3 iskeletteki eski `authority: Single Source of Truth (SSOT)` örneği birebir korunur — üretilen ADR'nin authority değeri karar metninin kendisidir, şablon SSOT iddiası taşımaz.
7. **Governance:** Red Team · Human Mode · Truth Mode.

## 5. Workflow

ŞABLONU SEÇ → KOPYALA → `{{PLACEHOLDER}}` DOLDUR → GUARDRAIL #16 DOĞRULA → COMMIT

1. **ŞABLONU SEÇ:** `.ai/.templates/adr/adr-template.md` (kullanım amacına göre ilgili domain ADR şablonu — diskte yoksa Faz 6 Planlanan listesine bak).
2. **KOPYALA:** Hedefe `ADR-NNN-<slug>.md` adıyla kopyala (NNN = sıradaki numara, slug = karar özeti).
3. **`{{PLACEHOLDER}}` DOLDUR:** Durum/Tarih/Karar Veren + §1-§5 domain alanları + §7 Onay; web araştırması bölümünü gerçek sonuçlarla doldur.
4. **GUARDRAIL #16 DOĞRULA:** §6 kontrol listesi + onay akışı (Vault Steward → Tech Lead → Arch Lead).
5. **COMMIT:** İlgili wiki-link'leri (`[[ADR-NNN-...]]`) ilgili vault dosyalarına eklenir; registry + log güncellenir.

## 6. Doğrulama

- [ ] 7 alanlı frontmatter var (`title`, `type`, `category`, `version`, `status`, `authority`, `updated`)
- [ ] 8 bölüm var (H1 + §1-§7)
- [ ] tüm `{{PLACEHOLDER}}`'lar dolduruldu (Bağlam/Karar/Alternatifler/Sonuçlar/Uygulama/Onay dahil)
- [ ] dosya bu şablona uygun (ADR dosyası)
- [ ] Frozen kuralı + onay akışı biliniyor; SSOT self-claim yok

**REFACTOR REPORT:** FILE: adr-template.md · PURPOSE: Architecture Decision Record şablonu (Guardrail #16) · VALIDATION: 7 alan + §1-§7 + bilgi korunumu · RELATED: [[.templates/index]] · [[../CLAUDE.md]]

## 7. Referanslar

- [[CLAUDE.md]] — ana sözleşme (iskelet §6 İlgili Dokümanlar tablosu)
- [[brain.md]] — mimari kararlar
- [[WORKFLOW.md]] — süreçler
- [[.templates/index]] — Template Registry (bu şablonun kaydı)
- [[../CLAUDE.md]] — vault ana sözleşme
- [[../../AGENTS.md]] — agent registry (ADR-NNN wiki-link kanıtları)

---

*ADR Template v2.0.0 — Vault Refactor Engine yeniden yazımı (2026-09-23)*
