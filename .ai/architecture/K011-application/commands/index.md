---
node_id: K011-application/commands
node_type: subdomain
status: draft
parent_id: K011-application
children: []
depends_on: []
evidence:
  - "v2 §2.11 — çocuk adı kaynağı (birebir)"
validation:
  - "İsim v2 §2.11 ile birebir karşılaştırıldı — 2026-10-12"
open_questions:
  - "Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C)"
  - "CQRS olarak mı okunacak? → TASARIM İDDİASI (kanıt değil)"
---

# commands — K011-application (kademe-2 · subdomain)

**ELI15:** Kullanıcıdan gelen 'yap' talepleri — yazma eylemlerinin taşıyıcısı.

**Sınır (ne DEĞİL):** Yazma kuralı K008-domain'de · kalıcılık K007-data'da.

**Bağımlılık (özet):** Düğümün tamamı üst kademeye bağlıdır; ayrıntı ve etiketler application-bagimliliklar.md §1'dedir. Bu dosyadaki satırlar TASARIM İDDİASI'dır.

**Durum:** draft · kademe-3 (bounded-context) ve kademe-4 genişletmesi ADR-097 K4 fazı (C) bekliyor · module'e inilmez.
