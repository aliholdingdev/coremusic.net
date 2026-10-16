---
node_id: K003-audio
node_type: domain
status: draft
parent_id: "K000–K5000 Architecture Registry"
children: [engine, device, driver, backend, input, output, routing, mixing, realtime, synchronization, diagnostics, testing]
depends_on: [K000-isletim-sistemi, K001-donanım, K005-firmware]
evidence: ["v2 §2.3 mini-ağaç — 12 çocuk adı (2026-10-12 okundu, birebir)", "registry index.md §5 satır 3 — K003-audio · audio-master.md · draft", "shared/src/AI/AIEngine.php:300-339 — ffmpeg metadata + ISO 31-band EQ"]
validation: "draft — D-kriterleri audio-dogrulama.md §4; çocuk kanıtları <çocuk>-dogrulama.md §1"
open_questions: ["Q5 engine ↔ backend çocuk sınırı — 2026-10-14 tasarım düzeyi çözüm (backend-kapsam.md §8), ADR bekliyor ⚠️", "Q6 input/output ↔ device/driver sorumluluk ayrımı ⚠️", "Q1 ASIO GPLv3 lisans etkisi ✅ KAPANDI 2026-10-14 (GPLv3 açık varyant kararı — audio-dogrulama.md §28 E-3)", "ASIO SDK 2.3.4 sürüm teyidi ⚠️ (vault-içi daralma: 2.3.x ↔ 2.3.4 — audio-dogrulama.md §28 E-7) · K002 ID GAP"]
group: mimari
---

# K003-audio — Ses Motoru

## §1 ELI15

**Sesin üretilip hoparlöre/kulaklığı gittiği motor.** Bir şarkıya bastığınızda
dosya okunur, belleğe alınır ve ses kartının hoparlöre göndereceği hale getirilir.
Sıralama, zamanlama, cihaz seçimi ve hata durumunda geri düşme — hepsi bu
düğümün sorumluluğudur. ELI15 ile: K003, müziği evin içinden hoparlöre taşıyan
**boru hattıdır** — ham veriyi kimin ürettiği (K001) veya tadını değiştiren
(K004) değil, **borunun akışı**dır.

**Ne DEĞİL:** sesi şekillendirmez (→ K004-dsp) · kartı tasarlamaz (→
K001-donanım) · donanım içi yazılımı yazmaz (→ K005-firmware) · ses API'sini
ve sürücü yüklemeyi kendisi uygulamaz (→ K000-isletim-sistemi, sadece çağırır).

## §1.5 İlişkili Proje Kaydı (2026-10-14)

**Neva Engine SDK/Lib geliştirme planı** — `.ai/projects/NevaEngine/`: envanter (00, 11 doğrulanmış SDK/lib + lisans) · geliştirme planı Faz 0–5 (01, kararlar: ASIO=GPLv3-open, JUCE=AGPLv3, kod yolu=`neva.engine/`) · DSP zincir mimari (02) · OS bazlı kod örnekleri (03 + `os/01–04`: Windows ASIO, Windows WASAPI, Linux ALSA/PipeWire+RPi NEON, macOS CoreAudio). K003 çocuklarıyla eşleme: `backend/asio/*` (19 md — 2026-10-14 glob sayımı; 21 iddiası diskle uyuşmuyor) → `os/01-windows-asio.md` · `driver/` → K2 fallback zinciri (ADR-017) · `engine/` → `02-dsp-zincir-mimari` §2 modül eşlemesi · `backend/` kök üçlüsü (kapsam/örnekler/doğrulama) → 2026-10-14 eklendi (Q5 tasarım çözümü `backend-kapsam.md` §8). Karar kayıtları: `audio-dogrulama.md` §28 (E-1–E-8).

## §2 Kademe-2 çocuk katalogu (v2 §2.3 — 12 çocuk, birebir)

| # | Çocuk | Rol (1 satır, addan türetildi) | Kanıt |
|---|---|---|---|
| 1 | engine | Zinciri çalıştıran ana döngü ve yaşam döngüsü | ⚠️ ad-türetme |
| 2 | device | Cihaz envanteri, seçimi ve değişimi | ⚠️ ad-türetme |
| 3 | driver | Sürücü yüzeyiyle tek noktadan etkileşim | ⚠️ ad-türetme |
| 4 | backend | Buffer/kuyruk/çalışma zamanı altyapısı | ⚠️ ad-türetme |
| 5 | input | Kaynak girişi (dosya, mikrofon, akış) | ⚠️ ad-türetme |
| 6 | output | Çıkış tarafı (hoparlör, kulaklık, akış) | ⚠️ ad-türetme |
| 7 | routing | Kanal/akış yönlendirme kuralları | ⚠️ ad-türetme |
| 8 | mixing | Çoklu kaynağı tek akışta birleştirme | ⚠️ ad-türetme |
| 9 | realtime | Zaman baskısı ve lock-free çalışma kuralları | ⚠️ ad-türetme |
| 10 | synchronization | Clock/epoch uyumu, drift telafisi | ⚠️ ad-türetme |
| 11 | diagnostics | Log, ölçüm, hata sınıflandırma | ⚠️ ad-türetme |
| 12 | testing | Test senaryoları ve kabul kriterleri | ⚠️ ad-türetme |

> ⚠️ VERIFICATION REQUIRED: v2 §2.3 çocukları yalnızca **ad** olarak tanımlar; roller adından türetilmiştir (v2 §3 KURAL 3) — uydurma API/parametre yazılmaz. Derin kapsam her çocuğun `<çocuk>-kapsam.md` dosyasındadır.

## §3 Bağımlılık özeti (tasarım iddiası)

| Yön | Bağımlılık | Etiket |
|---|---|---|
| K003 → K000-isletim-sistemi | Ses API'si, sürücü yükleme, thread/öncelik zemini | [TASARIM] |
| K003 → K001-donanım | DAC/ADC, ses kartı, I2S/TDM hattı fiziksel muhattır | [TASARIM] |
| K003 → K005-firmware | Donanım boot sonrası ses yolu kullanılabilir hale gelir | [TASARIM] |
| K004-dsp → K003 | DSP zinciri, motorun akışına graph düğümü olarak eklenir | [TASARIM] |
| K003 → K002 | **ID GAP** — registry'de K002 kimliği belirsiz | ⚠️ registry §6 |

A1 dizisi: K000 → K001 → K003 → K004 → K005 (K003 üçüncü katman). Sınır kuralı (master §2.2): K003 → K004'e **sinyal/parametre gider, kod girmez**.

## §4 Doğrulama durumu

| Kriter | Durum |
|---|---|
| D1 v2 §2.3 birebirliği (12 çocuk) | ✅ 2026-10-12 (glob + Get-ChildItem enum) |
| D2 12 çocuk klasöründe index.md (8 alan) | ✅ 2026-10-12 |
| D3 master + bagimliliklar + dogrulama üçlüsü | ✅ 2026-10-12 |
| D4 açık ⚠️ / UNKNOWN kalmadı | ❌ (NAudio thread · ASIO sürüm daralması · K002 GAP — ASIO lisans 2026-10-14'te kararla kapandı) |
| D5 açık sorular kapandı | ❌ (Q5 ADR bekliyor (tasarım düzeyi çözüm var) · Q6 + NAudio/sürüm — Q1 kapandı 2026-10-14) |
| D6 en az bir [TASARIM] → [KANIT] geçişi | ❌ 0 (repo'da C++ motor kodu yok) |
| D7 status: verified | ❌ D1–D7 tamamlanınca |

**Status: draft** — verified yalnız D1–D7 tamamı ✅ olduğunda yazılır.

## §5 Gezinti

Kök: `audio-master.md` (anayasa) · `audio-bagimliliklar.md` (depends_on/used_by +
riskler) · `audio-dogrulama.md` (iddia·URL·tarih·durum + D-kriterleri) ·
`audio-dil-kapsami.md` (4 dil grubu × 12 çocuk kapsama matrisi — 2026-10-14).
Çocuk: `<çocuk>/index.md` (8 alan) · `<çocuk>-kapsam.md` (içinde/dışında/komşu) ·
`<çocuk>-dogrulama.md` (kanıt satırları) · `<çocuk>-ornekler.md` (repo + web kodu).

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `audio-bagimliliklar.md` | Katman bağı ve used_by haritası | Kademe-1 |
| mimari | `audio-master.md` | Master künye ve sorumluluk haritası | Kademe-1 |
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-1 |
| mimari | `audio-dil-kapsami.md` | 4 dil grubu × 12 çocuk kapsama matrisi (48 hücre, tümü [TASARIM]) · **2026-10-14 eklendi** | Kademe-1 |
| kanit | `audio-dogrulama.md` | İddia, URL ve tarih doğrulama matrisi | Kademe-1 |

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12 · son hizalama 2026-10-14 · Red Team · Truth Mode
