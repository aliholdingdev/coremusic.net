---
node_id: K010-network/comms/tcp-ip/standart-kanit
node_type: module
status: draft
parent_id: K010-network/comms/tcp-ip
role: kanit
children: []
depends_on: []
evidence:
  - "RFC 9293 — TCP (rfc-editor.org, HTTP 200, 2026-10-15)"
  - "RFC 8200 — IPv6 (HTTP 200, 2026-10-15)"
  - "RFC 3927 — IPv4 Link-Local (HTTP 200, 2026-10-15)"
  - "RFC 6761/6762/6763 — Special-Use · mDNS · DNS-SD (device-communication kesişimi)"
validation:
  - "Tüm URL'ler Invoke-WebRequest ile 2026-10-15'te HTTP 200 doğrulandı (başlık §1)"
open_questions:
  - "Uygulama portu aralığı → ⚠️ UNKNOWN (faz C)"
  - "TCP keepalive parametreleri → ⚠️ UNKNOWN (faz C)"
group: kanit
---

# tcp-ip — standart-kanit (kademe-3 · module · rol: kanit)

**ELI15:** comms/tcp-ip torununun dayandığı **resmi standartlar**. TCP'nin kendisi
IETF RFC 9293 ile standarttır; yerel ağ taşımasının (ev cihazları arası) dayanağı budur.

**Sınır (ne DEĞİL):** Donanım/sürücü (K001) · port ataması politikası (faz C) · keşif (service-discovery torunu).

## §1 Resmi Standartlar (S1 — HTTP 200 · 2026-10-15)

| # | Standart | Konu | URL | Durum |
|---|---|---|---|---|
| 1 | RFC 9293 | TCP — akış kontrolü, yeniden gönderim, keepalive mekanizması | https://www.rfc-editor.org/rfc/rfc9293 | ✅ 200 |
| 2 | RFC 8200 | IPv6 — yerel ağ adresleme çekirdeği | https://www.rfc-editor.org/rfc/rfc8200 | ✅ 200 |
| 3 | RFC 3927 | IPv4 Link-Local (169.254/16) — DHCP yokken yerel ağ davranışı | https://www.rfc-editor.org/rfc/rfc3927 | ✅ 200 |
| 4 | RFC 6761 | Special-Use domain adları (localhost vb.) | https://www.rfc-editor.org/rfc/rfc6761 | ✅ 200 |
| 5 | RFC 8446 | TLS 1.3 — TCP üstü güvenli kanal (politika K009'da) | https://www.rfc-editor.org/rfc/rfc8446 | ✅ 200 |

## §2 Kesişimi Olan Standartlar (komşu torunlar)

| # | Standart | Kesişen torun | URL | Durum |
|---|---|---|---|---|
| 6 | RFC 6762 | mDNS — device-communication keşfi | https://www.rfc-editor.org/rfc/rfc6762 | ✅ 200 |
| 7 | RFC 6763 | DNS-SD — servis kaydı | https://www.rfc-editor.org/rfc/rfc6763 | ✅ 200 |
| 8 | RFC 7252 | CoAP — UDP üstü yerel cihaz protokolü (adayı) | https://www.rfc-editor.org/rfc/rfc7252 | ✅ 200 |

## §3 Kanıt → Master Satırları Eşlemesi

| Master iddiası (§9.4) | Kanıt | Yeni statü |
|---|---|---|
| Rol: yerel ağ düzeyi taşıma | RFC 9293 TCP (§1/1) | ✅ S1 |
| Oturum/yeniden bağlanma (socket yaşam döngüsü) | TCP kendisi güvenilir akıştır; reconnect uygulama katmanı | mekanizma ✅ S1 · strateji ⚠️ TASARIM İDDİASI |
| Port aralığı | standartta atanmış uygulama portu YOK | ⚠️ UNKNOWN (faz C — kullanıcı kararı) |
| Keepalive | RFC 9293 §3.5 mekanizması VARDIR; parametreler işletim sistemi/uygulama | mekanizma ✅ S1 · değerler ⚠️ UNKNOWN |

## §4 Bilinmeyen Envanteri (bu torun)

| # | Bilinmeyen | Durum |
|---|---|---|
| 1 | Uygulama portu aralığı (neva-connect 9742 tekil kayıt — genel aralık belirsiz) | ⚠️ UNKNOWN (faz C) |
| 2 | TCP keepalive süreleri (keepalive_idle/interval/count) | ⚠️ UNKNOWN (faz C) |
| 3 | IPv4/IPv6 öncelik sırası (Happy Eyeballs — RFC 8305 doğrulandı ama uygulama kararı yok) | ⚠️ VERIFICATION REQUIRED (faz C) |

**Kapı:** "TCP/IP yerel ağ taşıma standardıdır" iddiası 5+3 S1 ile kapalı; port/keepalive
değerleri faz C'de.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
