---
node_id: K014-network/comms/webrtc/standart-kanit
node_type: module
status: draft
parent_id: K014-network/comms/webrtc
role: kanit
children: []
depends_on: []
evidence:
  - "W3C WebRTC 1.0 Recommendation — https://www.w3.org/TR/webrtc/ (HTTP 200, 2026-10-15)"
  - "RFC 8825 — Overview of Real-Time Communication in WebRTC (ietf.org, HTTP 200, 2026-10-15)"
  - "RFC 8826 — Security Considerations (HTTP 200, 2026-10-15)"
  - "RFC 8445 — ICE (HTTP 200, 2026-10-15) · RFC 5389 STUN · RFC 5766 TURN · RFC 9147 DTLS 1.3 · RFC 3711 SRTP"
validation:
  - "Tüm URL'ler Invoke-WebRequest ile 2026-10-15'te HTTP 200 doğrulandı (başlık §1)"
  - "master §7/4 açık sorusu (WebRTC resmi kaynağı) bu dosya ile KAPANDI — 2026-10-15"
open_questions:
  - "TURN sunucusu sahibi/konumu → ⚠️ UNKNOWN (altyapı kararı, faz C)"
  - "Sinyal kanalı hangi taşıyıcı (websocket varsayımı) → ⚠️ VERIFICATION REQUIRED (faz C)"
group: kanit
---

# webrtc — standart-kanit (kademe-3 · module · rol: kanit)

**ELI15:** comms/webrtc torununun dayandığı **resmi standartlar**. WebRTC hem W3C
(tarayıcı API'si) hem IETF (protokol paketi: ICE/STUN/TURN/DTLS-SRTP) tarafından
standarttır — master §4/1'deki "⚠️ URL yok" satırı bu dosyayla **S1 kanıtına** yükselir.

**Sınır (ne DEĞİL):** Medya codec'i (K015) · şifreleme üretimi (K009 — DTLS/SRTP yerleşik) · sinyal kanalı taşıyıcısı (faz C kararı).

## §1 Resmi Standartlar (S1 — HTTP 200 · 2026-10-15)

| # | Standart | Kurum/Konu | URL | Durum |
|---|---|---|---|---|
| 1 | WebRTC 1.0 | W3C Recommendation — tarayıcı API'si (RTCPeerConnection, data channel) | https://www.w3.org/TR/webrtc/ | ✅ 200 |
| 2 | RFC 8825 | IETF — WebRTC genel bakış: protokol paketi haritası | https://www.rfc-editor.org/rfc/rfc8825 | ✅ 200 |
| 3 | RFC 8826 | IETF — güvenlik değerlendirmesi (DTLS-SRTP zorunluluğu) | https://www.rfc-editor.org/rfc/rfc8826 | ✅ 200 |
| 4 | RFC 8445 | IETF — ICE (NAT traversal aday keşfi) | https://www.rfc-editor.org/rfc/rfc8445 | ✅ 200 |
| 5 | RFC 5389 | IETF — STUN (NAT çözümleme) | https://www.rfc-editor.org/rfc/rfc5389 | ✅ 200 |
| 6 | RFC 5766 | IETF — TURN (relay — symmetric NAT arkası) | https://www.rfc-editor.org/rfc/rfc5766 | ✅ 200 |
| 7 | RFC 9147 | IETF — DTLS 1.3 (SRTP anahtar anlaşmasının taşıyıcısı) | https://www.rfc-editor.org/rfc/rfc9147 | ✅ 200 |
| 8 | RFC 3711 | IETF — SRTP (medya şifreleme) | https://www.rfc-editor.org/rfc/rfc3711 | ✅ 200 |
| 9 | RFC 8446 | IETF — TLS 1.3 (ilgili; güvenli kanal) | https://www.rfc-editor.org/rfc/rfc8446 | ✅ 200 |

## §2 Ekosistem (S1 — HTTP 200 · 2026-10-15)

| # | Kaynak | Konu | URL | Durum |
|---|---|---|---|---|
| 1 | MDN WebRTC API | Tarayıcı yüzeyi (JS kolu) | https://developer.mozilla.org/en-US/docs/Web/API/WebRTC_API | ✅ 200 |

## §3 Açık Soru Kapanış Kaydı (master §7/4)

| Soru | Önceki durum | Kanıt | Yeni durum |
|---|---|---|---|
| WebRTC standardının resmi kaynağı (URL)? | ⚠️ VERIFY REQUIRED (S3 beyan, 2026-10-12) | W3C https://www.w3.org/TR/webrtc/ + IETF RFC 8825–8826 (§1) | ✅ **KAPANDI** (S1 — 2026-10-15) |
| "WebRTC = IETF standardı" iddiası | S3 beyan | IETF RFC 8825/8826 + W3C Rec. — ikili kurum | ✅ doğrulandı (IETF protokolleri + W3C API) |

> **Kanıt dersi:** iddia "IETF" için dar idi — gerçekte WebRTC **ikili** bir standarttır:
> protokol paketi IETF (RFC 8825–8826, ICE 8445, STUN 5389, TURN 5766, DTLS 9147,
> SRTP 3711), tarayıcı yüzeyi W3C (WebRTC 1.0). Her ikisi de S1 ile kapalıdır.

## §4 Kanıt → Master Satırları Eşlemesi

| Master iddiası (§9.3) | Kanıt | Yeni statü |
|---|---|---|
| Rol: doğrudan medya/veri yolu | W3C WebRTC 1.0 (§1/1) — peer connection + data channel | ✅ S1 |
| Standard durumu | §3 kapanış kaydı | ✅ S1 |
| Traversal: STUN/TURN gerektirir | ICE RFC 8445 + STUN 5389 + TURN 5766 (§1/4–6) | ✅ S1 |
| Güvenlik: DTLS/SRTP | RFC 8826 + 9147 + 3711 (§1/3,7,8) | ✅ S1 (protokol) · üretim K009 |
| Medya codec'i | K015 sınırı — burada değil | sınır korundu |
| TURN sunucusu sahibi | kanıt YOK — altyapı kararı | ⚠️ UNKNOWN (faz C) |

## §5 Bilinmeyen Envanteri (bu torun)

| # | Bilinmeyen | Durum |
|---|---|---|
| 1 | TURN sunucusu sahibi/konumu | ⚠️ UNKNOWN (faz C) |
| 2 | Sinyal kanalı taşıyıcısı (websocket varsayımı — doğrulanmadı) | ⚠️ VERIFICATION REQUIRED (faz C) |
| 3 | Medya vs data-channel önceliği (handoff'ta hangisi) | ⚠️ VERIFICATION REQUIRED (faz C) |
| 4 | Kütüphane/çatı seçimi (tarayıcı yerleşik; native taraf belirsiz) | ⚠️ UNKNOWN (faz C) |

**Kapı:** "WebRTC standarttır ve K014/comms/webrtc'ye aittir" iddiası 9 S1 kanıtla kapalı;
altyapı ve taşıyıcı kararları faz C'de.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
