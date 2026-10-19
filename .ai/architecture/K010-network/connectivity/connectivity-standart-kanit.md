---
node_id: K010-network/connectivity/standart-kanit
node_type: module
status: draft
parent_id: K010-network/connectivity
role: kanit
children: []
depends_on: []
evidence:
  - "RFC 8305/6555 — Happy Eyeballs v2/v1 (rfc-editor.org, HTTP 200, 2026-10-15)"
  - "RFC 3927 — IPv4 link-local (HTTP 200, 2026-10-15)"
  - "MDN Navigator.onLine + online/offline event (developer.mozilla.org, HTTP 200, 2026-10-15)"
  - "learn.microsoft.com WLAN API reference (HTTP 200, 2026-10-15)"
validation:
  - "Tüm URL'ler Invoke-WebRequest ile 2026-10-15'te HTTP 200 doğrulandı (başlık §1–§3)"
open_questions:
  - "Yeniden bağlanma ve fallback STRATEJİSİ (parametreler/sıra) → ⚠️ UNKNOWN (faz C) — standart yol kanıtlanmıştır, seçim değildir"
  - "Kademe-4 module listesi → ⚠️ VERIFICATION REQUIRED (faz C)"
group: kanit
---

# connectivity — standart-kanit (kademe-2 · module · rol: kanit)

**ELI15:** Bağlantı kurma/sürdürme/yeniden kurma iddialarının URL kanıt defteri.
Her satır 2026-10-15'te HTTP 200 ile doğrulanmıştır (S1 kanıtı).

**Sınır (ne DEĞİL):** Fiziksel donanım (K001) · şifreleme (K009) · protokol anlamı
(comms/). Bu dosya yalnız **bağlantı davranışı** kanıtı taşır.

## §1 Bağlantı Kurma — Happy Eyeballs / fallback (S1 — HTTP 200 · 2026-10-15)

| # | Standart | Konu | URL | Durum |
|---|---|---|---|---|
| 1 | RFC 8305 | Happy Eyeballs v2 — çoklu aday arasında hızlı bağlantı yarışı + aileler arası fallback | https://www.rfc-editor.org/rfc/rfc8305 | ✅ 200 |
| 2 | RFC 6555 | Happy Eyeballs v1 — IPv4/IPv6 yarışı (v2'nin temeli) | https://www.rfc-editor.org/rfc/rfc6555 | ✅ 200 |
| 3 | RFC 3927 | IPv4 Link-Local — DHCP yokken bağlanılabilir yerel adres | https://www.rfc-editor.org/rfc/rfc3927 | ✅ 200 |

## §2 Bağlantı Durumu Tutma — istemci yüzeyi (S1 — HTTP 200 · 2026-10-15)

| # | Kaynak | Konu | URL | Durum |
|---|---|---|---|---|
| 4 | MDN Navigator.onLine | Tarayıcı online/offline durum sorgusu | https://developer.mozilla.org/en-US/docs/Web/API/Navigator/onLine | ✅ 200 |
| 5 | MDN online event | Bağlantı geldiğinde olay | https://developer.mozilla.org/en-US/docs/Web/API/Window/online_event | ✅ 200 |
| 6 | MDN offline event | Bağlantı koptuğunda olay | https://developer.mozilla.org/en-US/docs/Web/API/Window/offline_event | ✅ 200 |

## §3 Windows WLAN Yüzeyi (S1 — HTTP 200 · 2026-10-15)

| # | Kaynak | Konu | URL | Durum |
|---|---|---|---|---|
| 7 | learn.microsoft.com WLAN API | Windows Native Wifi API referansı (neva-connect-wlan-dallanma-mimarisi.md'nin platform kaynağı) | https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/ | ✅ 200 |

## §4 Kanıt → Kapsam Eşlemesi (hangi iddiayı destekler)

| İddia (index open_questions / tasarım) | Destekleyen kanıt | Sınıf |
|---|---|---|
| Fallback / çoklu aday bağlantı yolu standarttır (index §A/2 — yol tarafı) | RFC 8305 + RFC 6555 (§1/1–2) | S1 ✅ |
| Bağlantı durumu istemcide gözlemlenebilir (online/offline) | MDN onLine + olaylar (§2/4–6) | S1 ✅ |
| DHCP olmayan yerel ağda bağlantı kurulabilir | RFC 3927 (§1/3) | S1 ✅ |
| Windows WLAN yüzeyi gerçektir (WLAN çok-exe mimarisi referansı) | WLAN API (§3/7) | S1 ✅ |
| Yeniden bağlanma STRATEJİSİ (tETİK, backoff, sıralama parametreleri) | Seçim YOK — standart yol ≠ strateji | ⚠️ faz C |

## §5 Kanıt Sınıfı Özeti

| Sınıf | Sayı |
|---|---|
| S1 (URL + HTTP 200) | 7 |
| S3 (beyan, URL'siz) | 0 |
| UNKNOWN (karar bekleyen) | 1 (yeniden bağlanma/fallback stratejisi parametreleri) |

**Kapı:** "fallback ve durum tutma mümkün mü" sorusu S1 ile kapalıdır;
"hangi strateji/sıra/parametre" sorusu (index open_questions #2) açık kalır —
Happy Eyeballs yalnız *yolu* kanıtlar, K014 seçimini değil.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
