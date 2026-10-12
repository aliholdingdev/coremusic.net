---
node_id: K014-network/service-discovery
node_type: subdomain
status: draft
parent_id: K014-network
children: []
depends_on: []
evidence:
  - "v2 §2.14 — çocuk adı kaynağı (birebir)"
validation:
  - "İsim v2 §2.14 ile birebir karşılaştırıldı — 2026-10-12"
open_questions:
  - "Kademe-4 module kapsamı → ⚠️ VERIFICATION REQUIRED (faz C)"
  - "Kapsam: yerel cihaz keşfi mi, servis kaydı mı → ⚠️ VERIFICATION REQUIRED (§7/3)"
---

# service-discovery — K014-network (kademe-2 · subdomain)

**ELI15:** Ulaşılacak noktayı (cihaz veya servis) bulma ve adresini çözme.

**Sınır (ne DEĞİL):** Servis kaydı/dağıtımı K018-devops · iş servisleri K012-services olabilir → belirsiz.

**Bağımlılık (özet):** Düğümün tamamı üst kademeye bağlıdır; ayrıntı ve etiketler network-bagimliliklar.md §1'dedir. Bu dosyadaki satırlar TASARIM İDDİASI'dır.

**Durum:** draft · kademe-3 (bounded-context) ve kademe-4 genişletmesi ADR-097 K4 fazı (C) bekliyor · module'e inilmez.
