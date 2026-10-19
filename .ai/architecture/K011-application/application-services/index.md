---
node_id: K011-application/application-services
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
  - "Kademe-3 bounded-context kapsamı → âš ï¸ VERIFICATION REQUIRED (faz C)"
  - "application-services ↔ K012-services sahiplik ayrımı → âš ï¸ VERIFICATION REQUIRED"
group: mimari
---

# application-services — K011-application (kademe-2 · subdomain)

**ELI15:** Kullanıcı akışını servislere bağlayan uygulama düzeyi orkestrasyon (işlem koordinasyonu).

**Sınır (ne DEĞİL):** Arka plan servisleri K012-services'de · dış kontratlar K013-integraion'da.

**Bağımlılık (özet):** DÃ¼ÄŸümün tamamı üst kademeye bağlıdır; ayrıntı ve etiketler application-bagimliliklar.md §1'dedir. Bu dosyadaki satırlar TASARIM İDDİASI'dır.

**Durum:** draft · kademe-3 (bounded-context) ve kademe-4 genişletmesi ADR-097 K4 fazı (C) bekliyor · module'e inilmez.

---

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15

