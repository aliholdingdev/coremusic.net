---
node_id: K010-network/handoff/standart-kanit
node_type: module
status: draft
parent_id: K010-network/handoff
role: kanit
children: []
depends_on: []
evidence:
  - "MDN Remote Playback API + Presentation API — cihazlar arası teslim yüzeyi (HTTP 200, 2026-10-15)"
  - "RFC 6455 + 9110 + MDN WebSockets API — kontrol kanalı taşıyıcısı (HTTP 200, 2026-10-15)"
validation:
  - "Tüm URL'ler Invoke-WebRequest ile 2026-10-15'te HTTP 200 doğrulandı (başlık §1–§2)"
open_questions:
  - "handoff ↔ multi-room sınırı (teslim mi senkron mu) → ⚠️ VERIFICATION REQUIRED (§7/1) — teslim yolu kanıtlandı, sınır kararı faz C"
  - "Teslim anı/pozisyon aktarım protokolü (ne paketle) → ⚠️ UNKNOWN (faz C)"
group: kanit
---

# handoff — standart-kanit (kademe-2 · module · rol: kanit)

**ELI15:** "Çalanı bir cihazdan diğerine teslim etme" iddialarının URL kanıt defteri.
Her satır 2026-10-15'te HTTP 200 ile doğrulanmıştır (S1 kanıtı).

**Sınır (ne DEĞİL):** Çalma durumu sahibi (K012/playback-service) · zaman hizası
(multi-room) · handoff↔multi-room **sınırı** (§7/1 — kararı faz C).

## §1 Teslim Yüzeyleri — tarayıcı (S1 — HTTP 200 · 2026-10-15)

| # | Kaynak | Konu | URL | Durum |
|---|---|---|---|---|
| 1 | MDN Remote Playback API | Tarayıcıdaki medyayı uzak cihaza teslim/kontrol etme yüzeyi | https://developer.mozilla.org/en-US/docs/Web/API/Remote_Playback_API | ✅ 200 |
| 2 | MDN Presentation API | Sunum (ekran/içerik) cihazına teslim yüzeyi | https://developer.mozilla.org/en-US/docs/Web/API/Presentation_API | ✅ 200 |

## §2 Kontrol Kanalı — handoff mesajlarının taşıyıcısı (S1 — HTTP 200 · 2026-10-15)

| # | Kaynak | Konu | URL | Durum |
|---|---|---|---|---|
| 3 | RFC 6455 | WebSocket — handoff komutları için çift yönlü kanal (comms/websocket ile ortak) | https://www.rfc-editor.org/rfc/rfc6455 | ✅ 200 |
| 4 | RFC 9110 | HTTP Semantics — handoff REST uçları (başlat/teslim/al) | https://www.rfc-editor.org/rfc/rfc9110 | ✅ 200 |
| 5 | MDN WebSockets API | Tarayıcı WS yüzeyi | https://developer.mozilla.org/en-US/docs/Web/API/WebSockets_API | ✅ 200 |

## §3 Kanıt → Kapsam Eşlemesi (hangi iddiayı destekler)

| İddia (index open_questions / §7/1) | Destekleyen kanıt | Sınıf |
|---|---|---|
| Cihazlar arası **teslim** yolu teknik olarak vardır (tarayıcı tarafı) | Remote Playback + Presentation (§1/1–2) | S1 ✅ |
| Handoff kontrol mesajları çift yönlü kanalda taşınabilir | RFC 6455 + MDN WS (§2/3, §2/5) | S1 ✅ |
| Teslim komutları REST ile verilebilir | RFC 9110 (§2/4) | S1 ✅ |
| **handoff ↔ multi-room sınırı** (teslim mi senkron mu) | Seçim YOK — yol ≠ sınır | ⚠️ §7/1 faz C |
| Teslim anı / pozisyon paket formatı | Seçim YOK | ⚠️ faz C |

## §4 Kanıt Sınıfı Özeti

| Sınıf | Sayı |
|---|---|
| S1 (URL + HTTP 200) | 5 |
| S3 (beyan, URL'siz) | 0 |
| UNKNOWN (karar bekleyen) | 2 (sınır §7/1 · pozisyon paketi) |

**Kapı:** "handoff mümkün mü" sorusu S1 ile kapalıdır; "sınır kimde ve ne paketle"
soruları (index §A/2 = master §7/1) açık kalır.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
