---
node_id: K014-network/service-discovery
node_type: subdomain
status: draft
parent_id: K014-network
children: []
depends_on: []
evidence:
  - "v2 §2.14 — çocuk adı kaynağı (birebir)"
  - "RFC 6762/6763/6761/7252 + zeroconf kütüphaneleri — 6 URL HTTP 200 (2026-10-15, service-discovery-standart-kanit.md §1–§2)"
validation:
  - "İsim v2 §2.14 ile birebir karşılaştırıldı — 2026-10-12"
  - "Standart kanıtları HTTP 200 ile doğrulandı — 2026-10-15 (service-discovery-standart-kanit.md)"
open_questions:
  - "Kademe-4 module kapsamı → ⚠️ VERIFICATION REQUIRED (faz C)"
  - "Kapsam: yerel cihaz keşfi mi, servis kaydı mı → ⚠️ VERIFICATION REQUIRED (§7/3) — her iki yolun standardı S1 kanıtlandı, SPLIT kararı faz C"
group: mimari
---


# service-discovery — K014-network (kademe-2 · subdomain)

**ELI15:** Ulaşılacak noktayı (cihaz veya servis) bulma ve adresini çözme.

**Sınır (ne DEĞİL):** Servis kaydı/dağıtımı K018-devops · iş servisleri K012-services olabilir → belirsiz.

**Bağımlılık (özet):** Düğümün tamamı üst kademeye bağlıdır; ayrıntı ve etiketler network-bagimliliklar.md §1'dedir. Bu dosyadaki satırlar TASARIM İDDİASI'dır.

**Durum:** draft · kademe-3 (bounded-context) ve kademe-4 genişletmesi ADR-097 K4 fazı (C) bekliyor · module'e inilmez.


## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| kanit | `service-discovery-standart-kanit.md` | mDNS/DNS-SD/CoAP keşif standartları + kütüphane kanıtı | Kademe-2 |
| kod | `service-discovery-kod-ornekleri.md` | 7 dilde TASARIM ÖRNEĞİ — TTL önbellekli servis deposu | Kademe-2 |

## §A Kapilar (bu index)

| # | Kapilar | Durum |
|---|---|---|
| 1 | Kademe-4 module kapsamı → ⚠️ VERIFICATION REQUIRED (faz C) | FM open_questions |
| 2 | Kapsam: yerel cihaz keşfi mi, servis kaydı mı → ⚠️ VERIFICATION REQUIRED (§7/3) — SPLIT kararı faz C | FM open_questions |
| 3 | Kademe-3/4 genisletmesi | ADR-097 K4 faz (C) bekliyor |

> Bu tablo FM `open_questions` alaniyi index`e gorunur kilir; yeni iddia eklemez.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
