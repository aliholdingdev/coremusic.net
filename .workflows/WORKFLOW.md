---
title: "CoreMusic — Spec Execution Workflow (Kiro Workflow Spec)"
type: spec-workflow
category: workflow
version: 1.0.0
status: active
authority: "SSOT: .workflows/workflow.md · companion: design.md · tasks.md · PRD.md"
updated: 2026-11-01
---

# CoreMusic — Spec Execution Workflow (Kiro Workflow Spec)

> **Amaç:** Kiro IDE / Agentic Spec standardında, yapay zekanın (AI) bir göreve başladığında neyi, hangi sırayla yapacağını, hangi kapılardan geçeceğini ve sonuçları nasıl doğrulayacağını belirleyen yürütme iş akışı (`workflow.md`).

**Zorunlu Bağlantılar:** [[design.md]] · [[tasks.md]] · [[../PRD.md]] · [[../TODO.md]] · [[../RAG.md]] · [[../CONTEXT.md]] · [[../.ai/durum.md]]

---

## 🔄 6 ADIMLI KIRO AJAN ÇALIŞMA PROTOKOLÜ

```text
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│     ADIM 1      │     │     ADIM 2      │     │     ADIM 3      │
│  Gereksinimler  ├────►│ Teknik Tasarım  ├────►│  Görev Listesi   │
│   (PRD.md)      │     │   (design.md)   │     │ (tasks/TODO.md) │
└─────────────────┘     └─────────────────┘     └────────┬────────┘
                                                         │
┌─────────────────┐     ┌─────────────────┐              │
│     ADIM 6      │     │     ADIM 5      │     ┌────────▼────────┐
│  Durum Devri    │◄────┤ Test & Doğrula  │◄────┤     ADIM 4      │
│   (durum.md)    │     │(Playwright/Unit)│     │  Cerrahi Kod    │
└─────────────────┘     └─────────────────┘     └─────────────────┘
```

---

### ADIM 1: Gereksinimleri Anlama & Kapsam Belirleme (Requirements Gate)
- **Okunacak Belgeler:** [`PRD.md`](file:///c:/www/coremusic.net/PRD.md) ve ilgili yüzeyin alt PRD'si.
- **Eylem:** Görevin kullanıcı gereksinimleri, kabul kriterleri ve kapsam sınırları netleştirilir.
- **Kural:** Kapsamı belirsiz veya çelişkili görevlerde kod yazılmaz; soru sorulur (Asking Questions Mode).

### ADIM 2: Mimari & Teknik Tasarım Hizalaması (Design Gate)
- **Okunacak Belgeler:** [`design.md`](file:///c:/www/coremusic.net/.workflows/design.md) ve ilgili ADR kararları.
- **Eylem:** 
  - UI ise: ITCSS katman sırası (`01_Abstracts` → `05_Pages`), Light Mode kuralı ve Figma ölçümleri incelenir.
  - Backend ise: Repository sözleşmesi (`I*` + `final class`), raw PDO ve tek route tablosu kontrol edilir.
  - Donanım ise: Discrete Class AB topolojisi doğrulanır.

### ADIM 3: Görev Planlama & Bağımlılık Ayrıştırması (Tasks Breakdown)
- **Okunacak Belgeler:** [`tasks.md`](file:///c:/www/coremusic.net/.workflows/tasks.md) veya [`TODO.md`](file:///c:/www/coremusic.net/TODO.md).
- **Eylem:** Görev küçük, atomik ve test edilebilir alt maddelere ayrılır. Bağımlılık zinciri belirlenir.

### ADIM 4: Cerrahi Uygulama & Kodlama (Implementation Execution)
- **Eylem:** Kodlama cerrahi bir hassasiyetle yapılır.
- **Sert Kısıtlar (Hard Guardrails):**
  - **Light Mode Zorunluluğu (`HL-01`):** Dark mode kodu veya sınıfı yazmak KESİNLİKLE YASAKTIR.
  - **0 Raw Hex / Token Purity (`HL-12`):** CSS değerleri yalnızca `01_Abstracts/` tokenlerinden alınır.
  - **Raw PDO (`ADR-002`):** ORM yasaktır, parametreli PDO kullanılır.
  - **Class AB Amfi (`HL-02`):** Class D yasaktır.
  - **Subagent Commit Yasağı (`HL-03`):** Subagent asla `git commit` atamaz.

### ADIM 5: Otomatik Test & Runtime Doğrulaması (Verification Gate)
- **Eylem:** Yapılan değişiklikler otomatik test süitleri ile doğrulanır:
  ```bash
  # E2E Testleri
  npx playwright test tests/e2e/pages.spec.ts
  # Component Testleri
  npm run test
  # Backend Testleri
  vendor/bin/phpunit
  ```
- **Kural:** Testler yeşile dönmeden "iş bitti" denilemez.

### ADIM 6: Pre-Flight Hata Kontrolü & Durum Devri (Handoff Gate)
- **Okunacak Belgeler:** [`.ai/.rules/hata-listesi.md`](file:///c:/www/coremusic.net/.ai/.rules/hata-listesi.md) (`HL-01..12`).
- **Eylem:** Bilinen 12 proje tuzağına karşı son kontrol sağlanır.
- **Kapanış:** İlgili modülün `durum.md` dosyası ve [`.ai/durum.md`](file:///c:/www/coremusic.net/.ai/durum.md) güncellenerek oturum kapatılır.

---

## 🛑 ONAY KAPILARI (HUMAN APPROVAL GATES)

Aşağıdaki işlemler kullanıcı (Bayram Ali) onayı olmadan BAŞLATILAMAZ:
1. Canlı veritabanında migration / DDL çalıştırma.
2. Yeni harici paket/kütüphane ekleme.
3. Mimari şema ve frozen ADR değişikliği.
4. Dosya silme veya taşıma işlemleri.

---

*CoreMusic Spec Execution Workflow v1.0.0 — Kiro Standard — 2026-11-01*
