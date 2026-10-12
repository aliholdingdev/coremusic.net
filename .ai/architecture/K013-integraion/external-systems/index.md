---
node_id: K013-integraion/external-systems
node_type: subdomain
status: draft
parent_id: K013-integraion
children: []
depends_on: []
evidence:
  - "v2 §2.13 — çocuk adı kaynağı (birebir)"
validation:
  - "İsim v2 §2.13 ile birebir karşılaştırıldı — 2026-10-12"
open_questions:
  - "Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C)"
  - "YouTube Music resmi public API yok iddiası → ⚠️ VERIFY REQUIRED (TOS riski)"
---

# external-systems — K013-integraion (kademe-2 · subdomain)

**ELI15:** Tanımlı dış sistemlerin envanteri: hangisi var, hangisi bekliyor, hangisi yasaklı.

**Sınır (ne DEĞİL):** Sistem başına entegrasyon kodu adapters/child'da; envanter yalnız kayıt tutar.

**Bağımlılık (özet):** Düğümün tamamı üst kademeye bağlıdır; ayrıntı ve etiketler integraion-bagimliliklar.md §1'dedir. Bu dosyadaki satırlar TASARIM İDDİASI'dır.

**Durum:** draft · kademe-3 (bounded-context) ve kademe-4 genişletmesi ADR-097 K4 fazı (C) bekliyor · module'e inilmez.
