---
node_id: K010-network/comms/websocket/standart-kanit
node_type: module
status: draft
parent_id: K010-network/comms/websocket
role: kanit
children: []
depends_on: []
evidence:
  - "RFC 6455 — The WebSocket Protocol (rfc-editor.org, HTTP 200, 2026-10-15)"
  - "RFC 8441 — Bootstrapping WebSockets with HTTP/2 (HTTP 200, 2026-10-15)"
  - "MDN WebSockets API + WebSocket (HTTP 200, 2026-10-15)"
  - "Ratchet (PHP WS sunucusu) + libwebsockets (C/C++) ekosistem (HTTP 200, 2026-10-15)"
validation:
  - "Tüm URL'ler Invoke-WebRequest ile 2026-10-15'te HTTP 200 doğrulandı (başlık §1)"
open_questions:
  - "Keep-alive ping/pong periyodu → ⚠️ UNKNOWN (değer faz C)"
  - "Mesaj çerçevesi JSON mu binary mi → ⚠️ VERIFICATION REQUIRED (faz C)"
group: kanit
---

# websocket — standart-kanit (kademe-3 · module · rol: kanit)

**ELI15:** comms/websocket torununun dayandığı **resmi standart ve ekosistem kanıtları**.
WebSocket'in çift yönlü, sürekli bağlantı modeli RFC 6455 ile standarttır — bu, master §9.2'deki
"TASARIM İDDİASI" satırlarını **S1 kanıtına** yükseltir.

**Sınır (ne DEĞİL):** Mesaj içeriği (K012/K015) · şifreleme (K009 — wss: yerleşik TLS) · kütüphane seçimi (faz C).

## §1 Resmi Standartlar (S1 — HTTP 200 · 2026-10-15)

| # | Standart | Konu | URL | Durum |
|---|---|---|---|---|
| 1 | RFC 6455 | The WebSocket Protocol — açılış (handshake), çerçeveleme, ping/pong, kapanış | https://www.rfc-editor.org/rfc/rfc6455 | ✅ 200 |
| 2 | RFC 8441 | HTTP/2 üzerinde WebSocket açılışı (Extended CONNECT) | https://www.rfc-editor.org/rfc/rfc8441 | ✅ 200 |
| 3 | RFC 9110 | HTTP Semantics — 101 Switching Protocols dahil ortak çekirdek | https://www.rfc-editor.org/rfc/rfc9110 | ✅ 200 |

## §2 Ekosistem Kaynakları (S1 — HTTP 200 · 2026-10-15)

| # | Kaynak | Konu | URL | Durum |
|---|---|---|---|---|
| 1 | MDN WebSockets API | Tarayıcı istemci yüzeyi (JS kolu) | https://developer.mozilla.org/en-US/docs/Web/API/WebSockets_API | ✅ 200 |
| 2 | MDN WebSocket | Tarayıcı WebSocket nesnesi (olaylar, readyState) | https://developer.mozilla.org/en-US/docs/Web/API/WebSocket | ✅ 200 |
| 3 | Ratchet | PHP WebSocket sunucusu (ekosistem — seçilmemiş) | https://github.com/ratchetphp/Ratchet | ✅ 200 |
| 4 | libwebsockets | C/C++ WS/HTTP kütüphanesi (ekosistem — seçilmemiş) | https://github.com/warmcat/libwebsockets | ✅ 200 |

## §3 Kanıt → Kapsam Eşlemesi (master §9.2 satırlarının akıbeti)

| Master §9.2 iddiası | Destekleyen kanıt | Yeni statü |
|---|---|---|
| Rol: sürekli çift yönlü canlı bağlantı | RFC 6455 (§1/1) — çerçeveleme + ping/pong mekanizması standartta | ✅ S1 (TASARIM İDDİASI → kanıtlanmış protokol) |
| Keep-alive ping/pong | RFC 6455 §5.5.2 — ping/pong kareleri VARDIR; periyot değeri uygulamaya bağlı | mekanizma ✅ S1 · periyot ⚠️ UNKNOWN |
| Yeniden bağlanma (backoff) | RFC 6455 reconnect tanımaz — uygulama katmanı sorunu | ⚠️ TASARIM İDDİASI korunur (istemci davranışı) |
| Mesaj çerçevesi JSON/binary | RFC 6455 text/binary frame ayrımı VARDIR; hangisinin kullanılacağı karar | ayrım ✅ S1 · seçim ⚠️ faz C |
| wss: güvenliği | TLS yerleşik (RFC 8446) — K009 politikası | ✅ S1 (kanal) · üretim K009 |

## §4 Bilinmeyen Envanteri (bu torun)

| # | Bilinmeyen | Durum |
|---|---|---|
| 1 | Ping/pong periyodu (sn) | ⚠️ UNKNOWN (faz C) |
| 2 | Mesaj çerçevesi seçimi (JSON/binary/protobuf) | ⚠️ VERIFICATION REQUIRED (faz C) |
| 3 | Maks mesaj boyutu | ⚠️ UNKNOWN (faz C) |
| 4 | Sunucu kütüphanesi seçimi (Ratchet/libwebsockets/yerleşik) | ⚠️ VERIFICATION REQUIRED (faz C) |

**Kapı:** websocket'in "standart, çift yönlü, keepalive mekanizmalı" kimliği S1 ile kapalı;
değer kararları (periyot/çerçeve/boyut/kütüphane) faz C'ye devredilmiştir.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
