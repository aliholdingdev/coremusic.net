---
node_id: K011-application/desktop
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
  - "desktop ile electron sınırı → ⚠️ VERIFICATION REQUIRED (§4/6)"
group: mimari
---

# desktop — K011-application (kademe-2 · subdomain)

**ELI15:** Yerel masaüstü penceresi ve kabuğu (OS seviyesinde pencere, menü, dosya erişimi).

**Sınır (ne DEĞİL):** Electron paketleme K011/electron'da · web tarayıcısı K011/web'de.

**Bağımlılık (özet):** Düğümün tamamı üst kademeye bağlıdır; ayrıntı ve etiketler application-bagimliliklar.md §1'dedir. Bu dosyadaki satırlar TASARIM İDDİASI'dır.

**Durum:** draft · kademe-3 (bounded-context) ve kademe-4 genişletmesi ADR-097 K4 fazı (C) bekliyor · module'e inilmez.

---

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward

