---
node_id: K014-network/service-discovery/standart-kanit
node_type: module
status: draft
parent_id: K014-network/service-discovery
role: kanit
children: []
depends_on: []
evidence:
  - "RFC 6762/6763/6761 — mDNS · DNS-SD · special-use DNS (rfc-editor.org, HTTP 200, 2026-10-15)"
  - "RFC 7252 — CoAP (+ resource discovery) (HTTP 200, 2026-10-15)"
  - "grandcat/zeroconf + python-zeroconf (github.com, HTTP 200, 2026-10-15)"
validation:
  - "Tüm URL'ler Invoke-WebRequest ile 2026-10-15'te HTTP 200 doğrulandı (başlık §1–§2)"
open_questions:
  - "Kapsam: yerel cihaz keşfi mi, servis kaydı mı → ⚠️ VERIFICATION REQUIRED (§7/3) — her iki yolun da standardı S1 ile kanıtlandı, SPLIT kararı faz C"
  - "Kademe-4 module listesi → ⚠️ VERIFICATION REQUIRED (faz C)"
group: kanit
---

# service-discovery — standart-kanit (kademe-2 · module · rol: kanit)

**ELI15:** "Ulaşılacak noktayı bulma" iddialarının URL kanıt defteri — yerel cihaz
keşfi ve servis kaydı yollarının ikisi de resmi standartlara dayanır. Her satır
2026-10-15'te HTTP 200 ile doğrulanmıştır (S1 kanıtı).

**Sınır (ne DEĞİL):** Servis kaydı/dağıtım altyapısı (K018-devops) · iş servisleri
(K012) · keşif **kapsam split'i** kararı (faz C).

## §1 Keşif Standartları (S1 — HTTP 200 · 2026-10-15)

| # | Standart | Konu | URL | Durum |
|---|---|---|---|---|
| 1 | RFC 6762 | mDNS — yerel ağda ad çözümleme (cihaz keşfi yolu) | https://www.rfc-editor.org/rfc/rfc6762 | ✅ 200 |
| 2 | RFC 6763 | DNS-SD — servis kaydı keşfi (`_service._proto._tcp`; servis kaydı yolu) | https://www.rfc-editor.org/rfc/rfc6763 | ✅ 200 |
| 3 | RFC 6761 | Special-Use Domain Names (`.local` tanımı — keşif alan adı sınırı) | https://www.rfc-editor.org/rfc/rfc6761 | ✅ 200 |
| 4 | RFC 7252 | CoAP — resource discovery dahil hafif istek/yanıt (kaynak keşfi) | https://www.rfc-editor.org/rfc/rfc7252 | ✅ 200 |

## §2 Ekosistem Kaynakları (S1 — HTTP 200 · 2026-10-15)

| # | Kaynak | Konu | URL | Durum |
|---|---|---|---|---|
| 5 | grandcat/zeroconf (GitHub) | Go mDNS/DNS-SD kütüphanesi (seçilmemiş) | https://github.com/grandcat/zeroconf | ✅ 200 |
| 6 | python-zeroconf (GitHub) | Python mDNS/DNS-SD kütüphanesi (seçilmemiş) | https://github.com/python-zeroconf/python-zeroconf | ✅ 200 |

## §3 Kanıt → Kapsam Eşlemesi (hangi iddiayı destekler)

| İddia (index open_questions / §7/3) | Destekleyen kanıt | Sınıf |
|---|---|---|
| **Yerel cihaz keşfi** yolu standarttır | RFC 6762 + RFC 3927 (comms/device-communication §3/7) (§1/1) | S1 ✅ |
| **Servis kaydı** yolu standarttır | RFC 6763 (§1/2) | S1 ✅ |
| Keşif, DNS sunucusu olmadan yerel ağda yapılabilir | RFC 6762 + RFC 6761 (§1/1, §1/3) | S1 ✅ |
| Kaynak seviyesinde keşif (hafif cihaz) mümkündür | RFC 7252 (§1/4) | S1 ✅ |
| **Hangi yol K014'e ait** (cihaz keşfi vs servis kaydı split'i) | Seçim YOK — iki standard da var, sahiplik belirsiz | ⚠️ faz C |

## §4 Kanıt Sınıfı Özeti

| Sınıf | Sayı |
|---|---|
| S1 (URL + HTTP 200) | 6 |
| S3 (beyan, URL'siz) | 0 |
| UNKNOWN (karar bekleyen) | 1 (kapsam split'i — §7/3) |

**Kapı:** "keşif standartsız mı" sorusu S1 ile kapanır — her iki yolun da resmi
standartı vardır (§1). §7/3'ün **seçim/sahiplik** tarafı açık kalır: standart
varlığı, hangi kavme ait olduğunun kanıtı değildir.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
