---
node_id: K003-audio
node_type: domain
status: draft
parent_id: "K000–K5000 Architecture Registry"
children:
  - engine
  - device
  - driver
  - backend
  - input
  - output
  - routing
  - mixing
  - realtime
  - synchronization
  - diagnostics
  - testing
depends_on:
  - K000-isletim-sistemi
  - K001-donanım
evidence:
  - "v2 §2.3 mini-ağaç — 12 çocuk adı (2026-10-12 okundu)"
validation: "draft — A1 tur-1; D-kriterleri audio-dogrulama.md §2"
open_questions:
  - "ASIO SDK GPLv3 lisans etkisi (⚠️ VERIFY REQUIRED) · NAudio/ASIO 2026-09-28 başlığı (⚠️) · JUCE 8811★ (⚠️)"
---

# K003-audio — Ses Motoru

## §1 ELI15

**Sesin üretilip hoparlöre/kulaklığı gittiği motor.**

Bir şarkıya bastığınızda ses dosyadan okunur, belleğe alınır ve ses kartının
hoparlöre göndereceği hale getirilir. Sıralama, zamanlama, cihaz seçimi ve
hata durumunda geri düşme — hepsi bu düğümün sorumluluğudur. ELI15 ile: K003,
müziği evin içinden hoparlöre taşıyan **boru hattıdır**; ham veriyi kimin
ürettiği (K001) veya tadını değiştiren (K004) değil, borunun akışıdır.

Ne DEĞİL: sesi **şekillendirmez** (→ K004-dsp) · kartı **tasarlamaz**
(→ K001-donanım) · donanım içi **yazılımı yazmaz** (→ K005-firmware).

## §2 Kademe-2 çocuk katalogu (v2 §2.3 — 12 çocuk)

| # | Çocuk | Rol (1 satır) | Kanıt durumu |
|---|---|---|---|
| 1 | engine | Ses motorunun çekirdeği — zinciri çalıştıran ana döngü | ⚠️ adından türetildi |
| 2 | device | Hangi cihazın kullanılacağı — cihaz listesi ve seçimi | ⚠️ adından türetildi |
| 3 | driver | Cihaz sürücüsüyle konuşan katman | ⚠️ adından türetildi |
| 4 | backend | Arkada yürüyen altyapı — kuyruk, buffer, çalışma zamanı | ⚠️ adından türetildi |
| 5 | input | Sese giren taraf — mikrofon / kaynak girişi | ⚠️ adından türetildi |
| 6 | output | Sese çıkan taraf — hoparlör / kulaklık çıkışı | ⚠️ adından türetildi |
| 7 | routing | Hangi verinin nereye gideceği — kanal yönlendirme | ⚠️ adından türetildi |
| 8 | mixing | Birden çok kaynağın tek akışta birleştirilmesi | ⚠️ adından türetildi |
| 9 | realtime | Zaman baskısı altında çalışma kuralları | ⚠️ adından türetildi |
| 10 | synchronization | Cihaz / kaynak arası zaman uyumu (clock) | ⚠️ adından türetildi |
| 11 | diagnostics | Sorun bulma — log, ölçüm, hata çıkışı | ⚠️ adından türetildi |
| 12 | testing | Doğrulama ve test senaryoları | ⚠️ adından türetildi |

⚠️ VERIFICATION REQUIRED: v2 §2.3 çocukları yalnızca ad olarak tanımlar;
tablodaki roller adından türetilmiştir (v2 §3 KURAL 3) — uydurma API yazılmaz.

## §3 Bağımlılık özeti (tasarım iddiası)

| Yön | Bağımlılık | Etiket |
|---|---|---|
| K003 → K000-isletim-sistemi | Ses API'si ve sürücü zemini olmadan cihaza ulaşılamaz | [TASARIM] |
| K003 → K001-donanım | DAC/ADC ve ses kartı fiziksel muhatabıdır | [TASARIM] |
| K004-dsp → K003 | DSP zinciri ses akışının üstüne oturur | [TASARIM] |
| K003 → K005-firmware | Donanım başlatıldığında ses yolu kullanılabilir | [TASARIM] |

## §4 Doğrulama durumu

| Kriter | Durum |
|---|---|
| D1 v2 §2.3 birebirliği (12 çocuk) | ✅ 2026-10-12 |
| D2 12 çocuk klasöründe index.md | ✅ 2026-10-12 |
| D3 master + bagimliliklar + dogrulama üçlüsü | ✅ 2026-10-12 |
| D4 açık ⚠️ / UNKNOWN kalmadı | ❌ 3 açık (ASIO lisans · NAudio başlığı · JUCE yıldızı) |
| D5 açık sorular kapandı | ❌ 3 açık |
| D6 en az bir [TASARIM] → [KANIT] geçişi | ❌ 0 |
| D7 status: verified | ❌ D1–D7 tamamlanınca |

**Status: draft** — verified yalnız D1–D7 tamamı ✅ olduğunda yazılır.

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
