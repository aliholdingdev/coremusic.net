---
node_id: K010-network/comms/device-communication/standart-kanit
node_type: module
status: draft
parent_id: K010-network/comms/device-communication
role: kanit
children: []
depends_on: []
evidence:
  - "RFC 6762/6763 — mDNS + DNS-SD (rfc-editor.org, HTTP 200, 2026-10-15)"
  - "RFC 7252/7641/8323 — CoAP · Observe · CoAP over TCP (HTTP 200, 2026-10-15)"
  - "RFC 3927/6761 — IPv4 link-local (zeroconf) + special-use DNS (HTTP 200, 2026-10-15)"
validation:
  - "Tüm URL'ler Invoke-WebRequest ile 2026-10-15'te HTTP 200 doğrulandı (başlık §1)"
open_questions:
  - "Yerel protokol KARARI (hangisi kullanılacak) → ⚠️ VERIFICATION REQUIRED (faz C) — bu dosya yalnız aday kanıtı taşır"
  - "Kademe-4 module listesi → ⚠️ VERIFICATION REQUIRED (faz C)"
group: kanit
---

# device-communication — standart-kanit (kademe-3 · module · rol: kanit)

**ELI15:** comms/device-communication torununun "yerel ağ içi cihaz konuşması"
aday protokollerinin URL kanıt defteri. Her satır 2026-10-15'te HTTP 200 ile
doğrulanmıştır (S1 kanıtı).

**Sınır (ne DEĞİL):** Protokol **seçimi** (faz C kararı) · cihaz kimliği (K010-identity)
· cihaz envanteri (K012/device-service) · donanım (K001).

## §1 Servis Keşfi — mDNS/DNS-SD (S1 — HTTP 200 · 2026-10-15)

| # | Standart | Konu | URL | Durum |
|---|---|---|---|---|
| 1 | RFC 6762 | Multicast DNS — yerel ağda ad çözme (`.local`) | https://www.rfc-editor.org/rfc/rfc6762 | ✅ 200 |
| 2 | RFC 6763 | DNS-SD — servis kaydı keşfi (`_service._proto`) | https://www.rfc-editor.org/rfc/rfc6763 | ✅ 200 |
| 3 | RFC 6761 | Special-Use Domain Names (`.local` tanımı) | https://www.rfc-editor.org/rfc/rfc6761 | ✅ 200 |

## §2 Yerel Cihaz Mesajlaşması — CoAP ailesi (S1 — HTTP 200 · 2026-10-15)

| # | Standart | Konu | URL | Durum |
|---|---|---|---|---|
| 4 | RFC 7252 | CoAP — UDP tabanlı hafif istek/yanıt protokolü | https://www.rfc-editor.org/rfc/rfc7252 | ✅ 200 |
| 5 | RFC 7641 | CoAP Observe — kayıtlı bildirim akışı (push) | https://www.rfc-editor.org/rfc/rfc7641 | ✅ 200 |
| 6 | RFC 8323 | CoAP over TCP/TLS — güvenilir taşıma kolu | https://www.rfc-editor.org/rfc/rfc8323 | ✅ 200 |

## §3 Adreslenme (S1 — HTTP 200 · 2026-10-15)

| # | Standart | Konu | URL | Durum |
|---|---|---|---|---|
| 7 | RFC 3927 | IPv4 Link-Local — DHCP yokken yerel adreslenme (zeroconf) | https://www.rfc-editor.org/rfc/rfc3927 | ✅ 200 |

## §4 Kanıt → Kapsam Eşlemesi (hangi iddiayı destekler)

| İddia (index open_questions) | Destekleyen kanıt | Sınıf |
|---|---|---|
| "device-communication hangi yerel protokolleri kullanır" — **aday kümesi** | mDNS/DNS-SD (§1/1–3) + CoAP (§2/4–6) + link-local (§3/7) | S1 ✅ |
| Cihaz keşfi yerel ağda mümkündür (DHCP/DNS sunucusu olmadan) | RFC 6762 + RFC 3927 (§1/1, §3/7) | S1 ✅ |
| Hafif cihaz mesajlaşması + sunucuz bildirim | RFC 7252 + 7641 (§2/4–5) | S1 ✅ |
| **Hangisi kullanılacak** (protokol seçimi) | Seçim YOK — aday listesi karar değil | ⚠️ faz C |

## §5 Kanıt Sınıfı Özeti

| Sınıf | Sayı |
|---|---|
| S1 (URL + HTTP 200) | 7 |
| S3 (beyan, URL'siz) | 0 |
| UNKNOWN (karar bekleyen) | 1 (yerel protokol seçimi) |

**Kapı:** "yerel protokol adayları standarttır" iddiası S1 ile kapalıdır;
"hangi protokol" sorusu faz C'ye devredilmiştir (index open_questions #1'in
aday tarafı kapanır, seçim tarafı açık kalır).

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
