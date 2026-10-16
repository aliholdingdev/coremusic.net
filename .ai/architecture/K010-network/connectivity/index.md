---
node_id: K014-network/connectivity
node_type: subdomain
status: draft
parent_id: K014-network
children:
  - K014-network/connectivity/neva-connect-wlan-dallanma-mimarisi
depends_on: []
evidence:
  - "v2 §2.14 — çocuk adı kaynağı (birebir)"
  - "RFC 8305/6555/3927 + MDN online/offline + WLAN API — 7 URL HTTP 200 (2026-10-15, connectivity-standart-kanit.md §1–§3)"
validation:
  - "İsim v2 §2.14 ile birebir karşılaştırıldı — 2026-10-12"
  - "Standart kanıtları HTTP 200 ile doğrulandı — 2026-10-15 (connectivity-standart-kanit.md)"
open_questions:
  - "Kademe-4 module kapsamı → ⚠️ VERIFICATION REQUIRED (faz C)"
  - "Yeniden bağlanma ve fallback STRATEJİSİ (parametreler/sıra) → ⚠️ UNKNOWN (faz C) — yol tarafı RFC 8305/6555 ile S1 kanıtlandı"
group: mimari
---


# connectivity — K014-network (kademe-2 · subdomain)

**ELI15:** Bağlantıyı kurma, sürdürme ve koptuğunda yeniden kurma; bağlantı durumunun tutulması.

**Sınır (ne DEĞİL):** Fiziksel donanım K001-donanım · şifreleme K009-security · anlam/protokol comms/altında.

**Bağımlılık (özet):** Düğümün tamamı üst kademeye bağlıdır; ayrıntı ve etiketler network-bagimliliklar.md §1'dedir. Bu dosyadaki satırlar TASARIM İDDİASI'dır.

**Durum:** draft · kademe-3 (bounded-context) ve kademe-4 genişletmesi ADR-097 K4 fazı (C) bekliyor · module'e inilmez.


## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| mimari | `neva-connect-wlan-dallanma-mimarisi.md` | Neva Connect Windows WLAN çok-exe + sürüm gate mimarisi (2026-10-15) | Kademe-3 |
| kanit | `connectivity-standart-kanit.md` | Happy Eyeballs · online/offline · WLAN API — URL kanıt defteri | Kademe-2 |
| kod | `connectivity-kod-ornekleri.md` | 7 dilde TASARIM ÖRNEĞİ — backoff yeniden bağlanma denetçisi | Kademe-2 |

## §A Kapilar (bu index)

| # | Kapilar | Durum |
|---|---|---|
| 1 | Kademe-4 module kapsamı → ⚠️ VERIFICATION REQUIRED (faz C) | FM open_questions |
| 2 | Yeniden bağlanma ve fallback stratejisi → ⚠️ UNKNOWN | FM open_questions |
| 3 | Kademe-3/4 genisletmesi | ADR-097 K4 faz (C) bekliyor |

> Bu tablo FM `open_questions` alaniyi index`e gorunur kilir; yeni iddia eklemez.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
