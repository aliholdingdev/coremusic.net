---
node_id: K010-network/multi-room/standart-kanit
node_type: module
status: draft
parent_id: K010-network/multi-room
role: kanit
children: []
depends_on: []
evidence:
  - "RFC 5905 — NTPv4 saat senkronizasyonu (rfc-editor.org, HTTP 200, 2026-10-15)"
  - "RFC 3550 — RTP (akış zaman damgası + sink unsurları) (HTTP 200, 2026-10-15)"
  - "MDN Performance.now() — monotonik tarayıcı saati (HTTP 200, 2026-10-15)"
validation:
  - "Tüm URL'ler Invoke-WebRequest ile 2026-10-15'te HTTP 200 doğrulandı (başlık §1–§2)"
open_questions:
  - "Zaman hizalama YÖNTEMİ (NTP tabanlı mu? drift düzeltmesi ne kadar?) → ⚠️ UNKNOWN (§7/8) — araç kanıtlandı, seçim değil"
  - "Tolerans eşiği (ms cinsinden kabul edilebilir kayma) → ⚠️ UNKNOWN (faz C)"
group: kanit
---

# multi-room — standart-kanit (kademe-2 · module · rol: kanit)

**ELI15:** "Birden fazla odada zaman hizası" iddiasının araçlarının URL kanıt defteri.
Her satır 2026-10-15'te HTTP 200 ile doğrulanmıştır (S1 kanıtı).

**Sınır (ne DEĞİL):** Oda/çihaz envanteri (K012/device-service) · ses üretimi
(K003-audio) · zaman hizalama **yöntem seçimi** (§7/8 — kararı faz C).

## §1 Saat Senkronizasyonu (S1 — HTTP 200 · 2026-10-15)

| # | Standart | Konu | URL | Durum |
|---|---|---|---|---|
| 1 | RFC 5905 | NTPv4 — ağ üzerinden saat senkronizasyonu (çoklu cihaz ortak saat kaynağı) | https://www.rfc-editor.org/rfc/rfc5905 | ✅ 200 |
| 2 | MDN Performance.now() | Monotonik tarayıcı saati (sayfa-içi yüksek çözünürlüklü zaman; duvar saati kaymasından etkilenmez) | https://developer.mozilla.org/en-US/docs/Web/API/Performance/now | ✅ 200 |

## §2 Akış Zamanlaması (S1 — HTTP 200 · 2026-10-15)

| # | Standart | Konu | URL | Durum |
|---|---|---|---|---|
| 3 | RFC 3550 | RTP — akış zaman damgaları + senkronizasyon kaynakları (akış tarafı zaman zemini; Neva Engine medya yolu K003 sınırındadır) | https://www.rfc-editor.org/rfc/rfc3550 | ✅ 200 |

## §3 Kanıt → Kapsam Eşlemesi (hangi iddiayı destekler)

| İddia (index open_questions / §7/8) | Destekleyen kanıt | Sınıf |
|---|---|---|
| Cihazlar arası ortak saat **aracı** standarttır (yöntem adayı) | RFC 5905 NTP (§1/1) | S1 ✅ |
| Tarayıcı tarafı monotonik zaman kaynağı vardır | MDN Performance.now() (§1/2) | S1 ✅ |
| Akış zaman damgası taşıyan protokol standarttır (akış tarafı) | RFC 3550 RTP (§2/3) | S1 ✅ |
| **Zaman hizalama yöntemi seçimi** (NTP mü, lokal drift mi, hybrid mi) | Seçim YOK — araç ≠ yöntem | ⚠️ §7/8 faz C |
| Kabul edilebilir kayma toleransı (ms) | Seçim YOK | ⚠️ faz C |

## §4 Kanıt Sınıfı Özeti

| Sınıf | Sayı |
|---|---|
| S1 (URL + HTTP 200) | 3 |
| S3 (beyan, URL'siz) | 0 |
| UNKNOWN (karar bekleyen) | 2 (yöntem §7/8 · tolerans) |

**Kapı:** "zaman hizası mümkün mü" sorusu araç kanıtıyla S1'e oturur;
"hangi yöntem ve tolerans" (index §A/2 = master §7/8) açık kalır — NTP standarttır,
K014'in onu kullanacağı **seçilmemiştir**.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
