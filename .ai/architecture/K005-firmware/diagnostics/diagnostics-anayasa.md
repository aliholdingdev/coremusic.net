---
node_id: K005-firmware/diagnostics/diagnostics-anayasa
node_type: module
status: draft
parent_id: K005-firmware/diagnostics
children: []
depends_on:
  - K005-firmware
evidence:
  - "diagnostics/index.md — ELI15 + open_questions (hata kodu seti/rapor formatı) — okundu 2026-10-14"
  - "firmware-kapsam.md §9.6 (G1–G5) · §17 (Ov1 · Ov3 · Ov7) — okundu 2026-10-14"
  - "firmware-bilesenler.md §4 (F11) · §19.6 (g1–g6) · §23 (H18) — okundu 2026-10-14"
  - "firmware-master.md §2 satır 6 (rol) · §10 Q1 (dalga-3) — okundu 2026-10-14"
  - "boot/boot-anayasa.md — şablon (yapı referansı) — okundu 2026-10-14"
validation:
  - "2026-10-14 — FM 10 alan + group + son satır Authority denetimi"
open_questions:
  - "hata kodu seti/rapor formatı tanımlı değil ⚠️ (diagnostics/index.md'den taşındı — bu dosyada ÇÖZÜLMEDİ)"
  - "Uyarı sınıfları (sapma · ısınma) eşiği tanımlı değil — eşik sahibi power-control, değerler UNKNOWN"
  - "Log saklama süresi ve döngüsel tampon taşma davranışı tanımlı değil ⚠️"
group: mimari
---

# diagnostics-anayasa — Diagnostics Alt-Modülü Anayasası (K005-firmware)

> **ELI15:** Motorun arıza lambası: cihaz kendini kontrol eder (açılma testi /
> power-on self-test), sapma ve ısınma fark eder, bulduğunu kayıt altına alır
> ve host'a raporlar. Bu dosya diagnostics alt-modülünün **kurallarını ve
> sınırlarını** koyar; arayüz/sözleşme katmanı `diagnostics-surucu.md`,
> kapsam `diagnostics-kapsam.md`, taslak kod `diagnostics-ornekler.md`,
> kanıt/ölçüm `diagnostics-dogrulama.md` içindedir. Repo'da derlenebilir
> firmware kodu YOK (2026-10-13 keşif: 0 dosya) — bu nedenle her şey
> **tasarım seviyesindedir**.

## §1 Amaç, Kapsam ve Kapsam Dışı

| Alan | Soru | Cevap | Sahip |
|---|---|---|---|
| Amaç | Bu dosya neyi düzenler? | diagnostics alt-modülünün anayasası (kural + sınır) | K005-firmware/diagnostics |
| Kapsam içi | Kendini denetme (self-test · POST) | evet — G1 · g1 · g2 | diagnostics |
| Kapsam içi | Reset nedeni okuma/yorumu | evet — G2 (yazma komşuda, bkz. §7) | diagnostics |
| Kapsam içi | Hata log'u (döngüsel tampon · okuma yolu) | evet — G3 · g3 | diagnostics |
| Kapsam içi | Rapor arayüzü (host'a hata raporu) | evet — G4 · g4; **format tanımlı değil ⚠️** | diagnostics |
| Kapsam içi | Test kipi (kalıcı olmayan) | evet — G5 · g5 | diagnostics |
| Kapsam dışı | Termal/akım eşiği üretimi ve kısma-kapama kararı | hayır → power-control (F10 · H12 · H13) | power-control |
| Kapsam dışı | Watchdog beslemesinin kendisi | hayır → runtime (F5); diagnostics yalnız kuralı koyar (Ov3) | runtime |
| Kapsam dışı | İmza/anahtar doğrulaması, log imzası algoritması | hayır → security (S5 · dalga-3) | security |
| Kapsam dışı | Test senaryosu çalıştırma ve kabul kriteri (HIL) | hayır → testing (F14 · H17) | testing |
| Kapsam dışı | DSP algoritması (K004) · donanım register haritası (K001) | hayır → komşu düğümler | K004 · K001 |

**Kapsam kuralı (A1):** diagnostics **oku/yorum/raporla** yapar; eşik üretmez,
sayaç beslemez, kapatma kararı vermez. Yazma yetkisi olan komşular §7
tablosunda tekilleşir (çift yazıcı = belirsizlik ihlali).

**Kapsam kuralı (A2):** Bu dosyadaki hiçbir hata kodu, alan adı veya eşik
**koddaymış gibi sunulmaz**; hepsi `taslak`/`SÖZLEŞME` etiketlidir
(`firmware-bilesenler.md` §23 notu · `firmware-evrensel-dil.md` §6).

## §2 Kendini Denetme Zinciri (tasarım seviyesi)

Dayanak: `firmware-kapsam.md` §9.6 (G1–G5) · `firmware-bilesenler.md` §19.6
(g1–g6) · `boot/boot-kapsam.md` §C.4 (boot → diagnostics arayüzü).

| # | Aşama | Ne denetlenir / girdi | Üreten (taslak) | Çıktı | Kötü durum davranışı |
|---|---|---|---|---|---|
| Z1 | Boot teslimi | boot self-test sonucu (her reset) | boot → diagnostics (`boot-kapsam.md` §C.4) | pass/fail + başarısız bileşen kimliği | kayıt satırı (g3); boot tarafı E-kodu ile durur |
| Z2 | Açılış denetimi (POST) | RAM / flash / clock / perifer ilk denetimi (G1) | g1 power-on self-test | test listesi sonucu | başarısız satır → g4 raporu; devam kararı tanımsız ⚠️ |
| Z3 | Perifer denetimi | kayıt → geçti/kaldı (g2) | g2 perifer denetimi | durum satırı | "kaldı" satırı g3'e yazılır |
| Z4 | Reset nedeni okuma | reset nedeni kaydı (G2) | okuma/yorum: diagnostics — **yazma: power-control** (Ov1) | neden kaydı + yorum | bilinmeyen tür → `UNKNOWN` işareti (uydurulmaz) |
| Z5 | Çalışma anı izleme | watchdog olayı (Ov3) · istatistik sayacı (g6) · komşu telemetri | olay → sayaç/tampon | sayaç + olay satırı | olay düşerse düşüş gizlenmez (§6 F5) |
| Z6 | Rapor sunumu | kayıt (g3) → rapor paketi (g4) | g4 rapor sunucu | host'a rapor (G4) | kanal UNKNOWN ⚠️ — kanal yoksa teşhis kör (RSK-8 kaydı) |

**Zincir kuralı (A3):** Her aşama ya *geçer* ya *tanımlı kayıt/kol* üretir —
belirsiz ara durum ve sessiz yutma YASAKTIR (`firmware-test-stratejisi.md` §5
S8: "hata kodu döner, sessiz yutulmaz").

**Zincir kuralı (A4):** Z4'te yazma yetkisi diagnostics'te DEĞİLDİR
(`firmware-kapsam.md` §17 Ov1: "Kayıt yazma power-control, okuma/yorum
diagnostics"). Aynı ilke Ov3 (watchdog: runtime besler, diagnostics kuralı
koyar) ve Ov7 (test kipi: bayrak runtime'da yaşar, kipi diagnostics belirler)
ile üçlemelidir.

## §3 Uyarı Sınıfları (sapma · ısınma · eşik — TASLAK)

Dayanak: `diagnostics/index.md` ELI15 ("açılma testi, sensör sapması, ısınma
uyarısı") · `firmware-kapsam.md` §9.5 P5 · `firmware-bilesenler.md` §23.

| # | Sınıf | Tetiği | Üreten sahip | Kaynak satırı | Davranış | Durum |
|---|---|---|---|---|---|---|
| W1 | Sapma — perifer denetiminde "kaldı" | g2 kayıt sonucu | diagnostics (g2) | `firmware-bilesenler.md` §19.6 | g3'e kayıt + g4 rapor satırı | sınıf sınırları TASLAK ⚠️ |
| W2 | Isınma — termal eşiği aşım | termal eşik olayı | power-control (F10 · H13 `E_PWR_THERM`) | `firmware-kapsam.md` §9.5 P5 | kısma → kapama (üreten power-control); diagnostics **yalnız kaydeder** | davranış kaynakta; sınıflandırma TASLAK |
| W3 | Düşük gerilim (brown-out) | gerilim eşiği | power-control (F10 · H12 `E_PWR_BROWNOUT`) | `firmware-bilesenler.md` §23 | güvenli kapanma; diagnostics kaydeder | davranış kaynakta |
| W4 | Boot sapması | E1–E7 + RETRY_COUNT (F13 · F14) | boot | `boot/boot-surucu.md` §2 · §8 | olay kaydı (g3) | SÖZLEŞME (boot tarafı) |
| W5 | Watchdog olayı | WDT reset + neden raporu | runtime (F5 · I6) | `firmware-bagimliliklar.md` §9 I6 | reset nedeni raporu diagnostics'e | [TASARIM] |

**Sınıf kuralı (A5):** Sınıf ≠ eşik. Sıcaklık/gerilim eşik **değerleri**
power-control'dedir ve `UNKNOWN`'dur (`firmware-evrensel-dil.md` §3 kural 6:
ölçü adı uydurulmaz); diagnostics sınıflandırır ve kaydeder, eşiği üretmez.

**Sınıf kuralı (A6):** Sınıf tablosu **ön taslaktır**: nihai sınıf seti ve
sınır davranışları hata kodu seti ile birlikte karar bekliyor (§10 Q1) — bu
dosyada sınıf "kesinleştirilmez", satır silinmez.

## §4 Hata Raporu Formatı — TANIMLI DEĞİL ⚠️

Dayanak: `firmware-kapsam.md` §9.6 G4 ("host'a hata raporu sunma biçimi") ·
`diagnostics/index.md` open_questions.

> Bu bölüm format **çözmez**; index.md'deki açık soruyu buraya taşır. Aşağıdaki
> alan listesi **aday (taslak)**tır ve onaylanana kadar kodda kullanılmaz.

| # | Aday alan | İçeriği | Dayanağı | Durum |
|---|---|---|---|---|
| F1 | hata kodu | E0–E7 (boot) veya H-alanı (taslak) | `firmware-bilesenler.md` §23 · `boot-surucu.md` §6 | taslak ⚠️ |
| F2 | üreten bileşen | F-no / g-no (ör. F11 · g1) | `firmware-bilesenler.md` §4 · §19.6 | taslak ⚠️ |
| F3 | reset nedeni | G2 kaydı (okunmuş/yorumlanmış) | `firmware-kapsam.md` §9.6 G2 · §17 Ov1 | taslak ⚠️ |
| F4 | olay sırası/zamanı | monoton sıra sayacı | birim/tick kaynağı UNKNOWN | `UNKNOWN` |
| F5 | sayaç değeri | RETRY_COUNT (F13) · istatistik (g6) | `boot-surucu.md` §2 · `firmware-bilesenler.md` §19.6 | taslak ⚠️ |
| F6 | sürüm bilgisi | tek kaynak update manifest'i | `firmware-kapsam.md` §17 Ov6 | kaynakta (üretici update) |
| F7 | kaydın değiştirilemezliği | log imzası | `firmware-kapsam.md` §9.8 S5 (security · dalga-3) | dalga-3 |

**Aday taşıma biçimleri (atıf — SEÇİM YOK):** syslog (RFC 5424) · JSON
(RFC 8259) · UUID (RFC 9562) — bu üç satır `firmware-web-arastirma.md` §4
satır 6'da **`security` (dalga-3)** dosyasına atanmıştır; diagnostics bunları
yalnız **atıf** olarak okur, bu dosyada format seçimi yapılmaz.

**Rapor kuralı (A7):** Format onaylanana kadar `diagnostics-surucu.md` §4'teki
arayüz "SÖZLEŞME" etiketli kalır; onaysız alan adı kodda yazılamaz (S2 ile
aynı disiplin).

## §5 Kayıt (log) Saklama ve Maskeleme

Dayanak: `firmware-kapsam.md` §9.6 G3 · `firmware-kod-ornekleri.md` §5
Örnek 13 · `boot/boot-surucu.md` §9 (S10).

| # | Konu | Kural | Kaynak | Durum |
|---|---|---|---|---|
| L1 | Tampon tipi | döngüsel tampon (ring) — g3 | `firmware-kapsam.md` §9.6 G3 | kapsam maddesi (taslak) |
| L2 | Taşma davranışı | tanımsız — hangi kayıt düşer yazılmaz | G3 "taşma davranışı" | ⚠️ tanımlı değil (§10 Q3) |
| L3 | Saklama süresi | tanımsız — süre/adet rakamı uydurulmaz | — | `UNKNOWN` |
| L4 | Okuma yolu | G3 "okuma yolu" + G4 host raporu ilişkili; kanal seçimi yok | `firmware-kapsam.md` §9.6 | ⚠️ kanal UNKNOWN |
| L5 | Maskeleme | olay log'u **yazmadan önce** maskeleme katmanından geçer; anahtar/seri no asla log'a girmez | `firmware-kod-ornekleri.md` §5 Örnek 13 (Redactor deseni) | [KANIT] (desen) |
| L6 | Maskeleme anahtarları | `auth_key · password · passwd · csrf_token · token · secret · api_key · session_id · session · authorization · cookie` — case-insensitive → `[REDACTED]` | Örnek 13 (repo dosyası aktarımı) | [KANIT] (desen) |
| L7 | Gerçek zamanlı yola dokunma | kayıt yolu audio thread'ine dokunmaz; kayıt önceden tamamlanır | `boot-surucu.md` §9 S10 (repo kuralı 1-2) | kural |

**Maskeleme kuralı (A8):** Maskeleme katmanı devre dışı bırakılamaz; maskeleme
hattı düşerse kayıt **düşer** (sessiz ham kayıt yok) — bu, §6 F4'ün
maskeleme karşılığıdır (Örnek 13 deseninden türetildi, kod kanıtı yok →
`⚠️ VERIFICATION REQUIRED`).

## §6 Fail-safe İlkeleri (asla fail-open)

| # | İlke | Uygulama | Kaynak |
|---|---|---|---|
| F1 | Hata ≠ geçerli | doğrulama/sonuç iki değerli; hata durumu "geçti" sayılmaz | `boot-kapsam.md` §C.2 (fail-safe) |
| F2 | Kayıt yolu kaybolamaz | olay kaydı (E4 dahil) hiçbir katmanda sessizce düşürülmez | `boot-surucu.md` §21 SG8 |
| F3 | Test kipi kalıcı olmaz | G5 üretim/test erişimi "kalıcı olmayan kip" — kalıcılaşma ihlaldir | `firmware-kapsam.md` §9.6 G5 · §17 Ov7 |
| F4 | Maskeleme olmadan kayıt yok | §5 A8 — ham hassas kayıt fail-open sayılır | Örnek 13 deseni (türetildi ⚠️) |
| F5 | Kanal kaybı gizlenemez | log kanalı tanımsızsa teşhis kör kalır; bu durum "sağlıklı" raporlanamaz | `boot-kapsam.md` §13 RSK-8 |
| F6 | Yetki sınırları fail-safe'de de korunur | imza/anahtar işi security'nin; test erişimi üretimde kapalı (debug) | `boot-surucu.md` §7 · §17 A10 |

## §7 Komşu Sınır (boot · runtime · security ve diğerleri)

| Komşu | Yön | Arayüz / veri | Kaynak satırı | Sözleşme durumu |
|---|---|---|---|---|
| boot | boot → diagnostics | E1–E7 + RETRY_COUNT (F13 · F14); boot self-test sonucu + boot log | `boot-surucu.md` §8 · §9 · `boot-kapsam.md` §C.4 | SÖZLEŞME |
| runtime | runtime → diagnostics | reset nedeni + watchdog olayı raporu (I6) · istatistik (g6/g3) | `firmware-bagimliliklar.md` §9 I6 | [TASARIM] |
| power-control | power-control ↔ diagnostics | kapanma kaydı P6 (ortak saklama alanı); Ov1 yazma/okuma ayrımı | `firmware-kapsam.md` §9.5 P6 · §17 Ov1 | [TASARIM] |
| security | security ↔ diagnostics | log imzası S5 (kaydı değiştirilemez kılma); olay kaydı akışı | `firmware-kapsam.md` §9.8 S5 · `boot-surucu.md` §21 SG8 | dalga-3 ⚠️ |
| testing | diagnostics → testing | self-test sonuçları test kriterine girer (I9) | `firmware-bagimliliklar.md` §9 I9 | dalga-3 |
| device-control | device-control ↔ diagnostics | test kipi bayrağı runtime'da yaşar, kipi diagnostics belirler (Ov7) | `firmware-kapsam.md` §17 Ov7 | [TASARIM] |
| audio-control | audio-control → diagnostics | telemetri yayıncısı: sayaçlar → runtime ring-buffer → diagnostics | `audio-control/audio-control-kapsam.md` §B (B8) | taslak |
| K001-donanım | K001 → diagnostics | test noktaları listesi (E1j) — HIL'de gerekli | `firmware-bagimliliklar.md` §9.1 E1j | ⚠️ VERIFICATION REQUIRED |

## §8 Red Team İtiraz Kayıtları

| # | İtiraz | Yanıt | Kanıt |
|---|---|---|---|
| T1 | "Sınıflar (W1–W5) gerçek kod mu?" | Hayır — TASLAK etiketli; sınıf seti onay bekliyor | §3 Durum sütunu · §6 A6 |
| T2 | "Rapor formatı neden tanımsız?" | Bilerek — G4 kapsam maddesi format vermez; açık soru taşındı, çözülmedi | §4 · `index.md` open_questions |
| T3 | "Eşik değerleri neden yok?" | Eşik sahibi power-control; ölçü adı uydurmak yasak | A5 · `firmware-evrensel-dil.md` §3 kural 6 |
| T4 | "Log boyutu/süresi neden yok?" | G3 kapsam maddesi boyut/süre vermez → `UNKNOWN` | §5 L2 · L3 |
| T5 | "H18 `E_DIAG_TEST` bağlayıcı mı?" | Hayır — hata alanları taslak; nihai tablo dalga-3 | `firmware-bilesenler.md` §23 notu · A2 |
| T6 | "fail-open yasak iddiası kanıtlı mı?" | İlkeler kaynaklı (F1–F3 · F5); F4 türetildi ve ⚠️ işaretli | §6 |
| T7 | "Çift yazıcı riski var mı?" | Ov1/Ov3/Ov7 tek yazıcı tablosu ile kapatıldı; denetim §2 A4 | §2 A4 · §7 |
| T8 | "Ölçüm/`✅` iddiası var mı?" | Yok — tüm durumlar Tasarım seviyesi / PLANNED / UNKNOWN | `diagnostics-dogrulama.md` §A |

## §9 İddia → Dayanak Eşlemesi

| # | İddia | Dayanak | Tür |
|---|---|---|---|
| D1 | Kapsam 5 madde (G1–G5) | `firmware-kapsam.md` §9.6 | kaynak |
| D2 | Bileşen 6 satır (g1–g6), hepsi dalga-3 | `firmware-bilesenler.md` §19.6 | kaynak |
| D3 | F11 (self-test) sahibi diagnostics, bağımlı F1–F10 | `firmware-bilesenler.md` §4 | kaynak |
| D4 | H18 `E_DIAG_TEST` → diagnostics (taslak) | `firmware-bilesenler.md` §23 | kaynak (taslak) |
| D5 | reset nedeni yazma power-control · okuma diagnostics | `firmware-kapsam.md` §17 Ov1 | kaynak |
| D6 | boot self-test sonucu her reset diagnostics'e | `boot-kapsam.md` §C.4 | komşu kayıt |
| D7 | maskeleme deseni `[REDACTED]` (anahtar/seri no yasak) | `firmware-kod-ornekleri.md` §5 Örnek 13 | kanıt (desen) |
| D8 | rol: "Self-test · hata raporu · log" (dalga-3 · ⏳ yalnız index.md) | `firmware-master.md` §2 satır 6 | kaynak |
| D9 | rapor formatı tanımlı değil | `index.md` open_questions · `firmware-kapsam.md` §9.6 G4 | dürüstlük (açık soru) |
| D10 | sınıf/eşik/kanal değerleri uydurulmadı | `UNKNOWN` · `⚠️` işaretleri (§3 · §5) | dürüstlük |
| D11 | repo'da firmware kodu yok → kod iddiası üretilmedi | 2026-10-13 keşif kaydı (şablon bloknotları) | dürüstlük |

## §10 Açık Sorular

| # | Soru | Etki | Durum |
|---|---|---|---|
| Q1 | hata kodu seti/rapor formatı tanımlı değil | §4 · `diagnostics-surucu.md` §4 · H-alanı | ⚠️ (index.md'den taşındı — çözülmedi) |
| Q2 | Uyarı sınıfları (sapma · ısınma) nihai seti ve sınır davranışları | §3 | ⚠️ VERIFICATION REQUIRED |
| Q3 | Log taşma davranışı + saklama süresi (G3) | §5 L2 · L3 | UNKNOWN |
| Q4 | Rapor kanalı/host uç noktası (G4) | §4 · §5 L4 | UNKNOWN |
| Q5 | Dalga-3 3 çocuk ne zaman yazılacak (diagnostics · security · testing)? | tüm dosya grubu | ⚠️ `firmware-master.md` §10 Q1 |
| Q6 | Log imzası (S5) sahipliği ve algoritması security ile teyit bekliyor | §6 F2 · §7 | dalga-3 ⚠️ |

## §13 Derinleştirme Eki — Teşhis Alt Sistemleri (2026-10-15)

> **E dürüstlük:** bu ek yalnız mevcut vault içeriğini genişletir; 0 URL, 0 repo kodu,
> 0 ölçülmüş sayı ekler. Erişilemeyen değerler `UNKNOWN`/`⚠️ VERIFICATION REQUIRED` korunur.

### §13.1 Alt sistem envanteri

| # | Alt sistem | Görev | Sahip dal | Durum |
|---|---|---|---|---|
| 1 | Olay yazıcı (event writer) | Olayları kuyruğa yaz | diagnostics | Vault md — kod YOK |
| 2 | Sayaç (counter) | overrun/xrun/olay sayımı | diagnostics | Vault md — kod YOK |
| 3 | Kuyruk (lock-free queue) | Sabit kapasiteli olay kanalı | diagnostics | Vault md — kod YOK |
| 4 | Boşaltıcı (drainer) | Kuyruğu diske/pipe'a boşalt | diagnostics | Vault md — kod YOK |
| 5 | Raporlayıcı (reporter) | Periyodik rapor üretimi | diagnostics | Vault md — kod YOK |
| 6 | Kayıt (log store) | Kalıcı log saklama | diagnostics | Vault md — kod YOK |

### §13.2 Kuyruk davranışı (runtime-kapsam F.3 ile eşgüdümlü)

| Durum | Davranış | Etki |
|---|---|---|
| Kuyruk boş | Yazıcı beklemez, sayac güncelle | — |
| Kuyruk dolu | Yeni olay **atlanır**, kayıp sayacı artar | Ses kesilmez |
| Boşaltıcı yavaş | Kuyruk dolarak ilerler | Kayıp sayacı izlenir |
| Boşaltıcı durdu | Kuyruk dolar + kayıp sayacı artar | Uyarı olayı |

1. **Temel kural:** teşhis yolu asla **bloke edici** değildir (ses yoluna göre ikincil).

### §13.3 Sayaç envanteri (runtime ile eşleşen)

| Sayaç | Yazan | Okuyan | Kural kaynağı |
|---|---|---|---|
| `overrun` | runtime | diagnostics | Hot-path olayı |
| `xrun` | runtime | diagnostics | Donanım taşması |
| `queue_drop` | diagnostics (kuyruk) | rapor | §13.2 kayıp |
| `protect` | power-control | rapor | Kural 10 |
| `brownout` | power-control | rapor | Brown-out yolu |

## §14 Derinleştirme Eki — Olay Modeli (Genişletme)

### §14.1 Olay sınıfı ağacı

| Sınıf | Alt sınıf | Örnek | Öncelik |
|---|---|---|---|
| Runtime | hot-path | overrun · xrun · clock_lost | Yüksek |
| Güç | koruma | dc_fault · thermal · brownout | En yüksek |
| Güncelleme | OTA | update_start · update_fail · rollback | Orta |
| Güvenlik | boot | secure_boot_fail · sig_fail | Yüksek |
| Sistem | kaynak | queue_drop · pool_exhaust | Orta |

### §14.2 Olay kaydı şeması (mantıksal alanlar)

| Alan | Tip | Zorunlu mu | Not |
|---|---|---|---|
| seq | atomik sayaç | Evet | Sıra |
| sınıf | enum | Evet | §14.1 |
| zaman damgası | sayaç (mantıksal) | Evet | Uydurma saat yok |
| kod | sabit enum | Evet | Hata kodu |
| bağlam | sabit tampon | Hayır | Statik, tahsis yok |

1. **Dürüstlük:** bu alanların gerçek ölçümleri/doldurulmaları **yoktur** — şema yalnız
   tasarım düzeyindedir (0 cpp/h · 2026-10-15).

### §14.3 Öncelik kuralı

1. **Koruma > runtime > güvenlik > güncelleme > sistem** — koruma olayları her zaman
   raporlanır (kuyruk dolu olsa bile sayaç sayılır).
2. **Ses yolu etkisi:** hiçbir olay sınıfı ses yolunu **duraklatmaz** (bloke edici yazım yok).

## §15 Derinleştirme Eki — Log Saklama ve Taşma (Genişletme)

| # | Konu | Durum | Davranış |
|---|---|---|---|
| 1 | Taşma davranışı | UNKNOWN (Q3) | Tanımsız — uydurmak yasak |
| 2 | Saklama süresi | UNKNOWN (Q3) | Tanımsız — uydurmak yasak |
| 3 | Kuyruk kapasitesi | Kod YOK | Statik, kod gelince belirlenecek |
| 4 | Kayıp sayacı | §13.2 | Sayılır, ses kesilmez |
| 5 | Rapor kanalı | UNKNOWN (Q4) | Tanımsız — uydurmak yasak |

1. **Dürüstlük:** taşma/saklama/kanal **bilinmiyor**; bu ek yalnız boşluğu **işaretler**,
   değer **uydurmaz**.

## §16 Derinleştirme Eki — Komşu Dal Eşgüdümü

| Dal | Eşgüdüm noktası | Yön |
|---|---|---|
| runtime/ | overrun/xrun sayaçları | runtime → diagnostics |
| runtime/ | kuyruk el sıkışması (runtime-kapsam F.3) | runtime ↔ diagnostics |
| power-control/ | protect/brownout olayları | power-control → diagnostics |
| update/ | OTA olayları | update → diagnostics |
| security/ | secure boot/sig olayları | security → diagnostics |
| testing/ | rapor okuma | diagnostics → testing |

## §17 Derinleştirme Eki — Kural Eşlemesi

| Kural | Karşılık | Not |
|---|---|---|
| 2 — lock-free | Kuyruk atomik (§13.2) | Kilit yasak |
| 1 — 0 tahsis | Statik tampon (§14.2) | Çalışma anı tahsis yok |
| 3 — noexcept | Yazıcı işlemleri | Kod yok → derleme denetimi UYGULANAMAZ |

## §18 Derinleştirme Eki — Red Team Sonuçları

| # | Denetim | Sonuç |
|---|---|---|
| 1 | Değer uydurulmuş mu? | Hayır — UNKNOWN/`⚠️` korundu |
| 2 | Ölçülmüş sayı eklendi mi? | Hayır — 0 ölçüm |
| 3 | Kod iddiası var mı? | Hayır — 0 cpp/h |
| 4 | Authority tek satır | Evet |

## §19 Derinleştirme Eki — Tanılama Alt Sistemlerinin Davranış Sözleşmeleri

> **Dürüstlük bloğu:** Bu ek 0 URL, 0 repo kodu, 0 ölçülmüş sayı ile üretilmiştir
> (disk ölçümü: repo'da 0 `*.cpp`/`*.h`, 2026-10-15). Tüm senaryolar `PLANNED`.

### §19.1 Sayaç sözleşmesi (tüm sayaçlar)

| # | Kural | Gerekçe | Durum |
|---|---|---|---|
| 1 | Sayaçlar `std::atomic<uint32_t>` veya eşdeğeri atomik sayaçtır | Kilit yasak (kural 2) | Tasarım |
| 2 | Sayaç aşımında (UINT32 taşması) sayaç sıfırlanmaz, doygunlukta kalır | Sürpriz sıfırlama yok | Tasarım |
| 3 | Sayaç okuması ses yolunda **yazmaz**; yazım yalnız artan (increment) | Yarış koşulu yok | Tasarım |
| 4 | Sayaç adları sabit enum ile tanımlanır; çalışma anında string üretilmez | Tahsis yasak (kural 1) | Tasarım |
| 5 | Sayaç okuma/değerlendirme non-audio thread'de yapılır | Ses yolunda iş yükü artmaz | Tasarım |

### §19.2 Kuyruk sözleşmesi

| # | Kural | Gerekçe | Durum |
|---|---|---|---|
| 1 | Kuyruk tek üretici–tek tüketici (SPSC) ring buffer'tır | Kilit yasak (kural 2) | Tasarım |
| 2 | Kapasite derleme zamanında sabittir (statik tampon) | Çalışma anı tahsis yasak (kural 1) | Tasarım |
| 3 | Dolu kuyrukta yeni olay **düşürülür** ve düşürme sayacı artırılır | Bloke edici yazım yasak | Tasarım |
| 4 | Tüketici (rapor yazan) non-audio thread'dedir | Ses yolu etkisiz | Tasarım |
| 5 | Kuyruk kelimesi (el sıkışma) atomik sıradır (sequence) | Sıra bozulmaz | Tasarım |

### §19.3 Olay yazıcı sözleşmesi (ses yolunda)

| # | Kural | Gerekçe | Durum |
|---|---|---|---|
| 1 | Olay yazımı noexcept olmalıdır | Kural 3 | Kod yok → derleme denetimi UYGULANAMAZ |
| 2 | Yazım yalnız atomik increment + kuyruk yazma yapar | En küçük iş yükü | Tasarım |
| 3 | Zaman damgası mantıksal sayaçtan gelir; sistem saati sorgulanmaz | Uydurma saat yok | Tasarım |
| 4 | Bağlam alanı sabit boyutludur; dinamik uzunluk yasak | Tahsis yasak (kural 1) | Tasarım |
| 5 | Olay sınıfı (kritik/uyarı/bilgi) yalnız enum ile ayrılır | String yasak | Tasarım |

### §19.4 Rapor (okuyucu) sözleşmesi

| # | Kural | Gerekçe | Durum |
|---|---|---|---|
| 1 | Rapor üretim thread'inde, bloke edilebilir bağlamda çalışır | Sıra dışı kaynak kullanımı yok | Tasarım |
| 2 | Rapor formatı sabit şablondur; çalışma anında biçim (format) kurulmaz | Tahsis/dizin yok | Tasarım |
| 3 | Rapor yazımı dosya/konsol/network kanalına olur — kanal seçimi | Bilinmiyor | ⚠️ VERIFICATION REQUIRED (Q4) |
| 4 | Rapor, sayaç anlık görüntüsünü (snapshot) atomik okur | Tutarlı okuma | Tasarım |
| 5 | Rapor tetikleme periyodu UNKNOWN (Q3) — uydurulmaz | Dürüstlük | UNKNOWN |

1. **Dürüstlük:** bu sözleşmeler **tasarım düzeyindedir**; hiçbirinde çalışan kod,
   ölçülmüş gecikme veya doğrulanmış kanal yoktur (0 cpp/h · 2026-10-15).

## §20 Derinleştirme Eki — Bilinen Boşluklar (Q Soruları)

| # | Soru | Durum | Etkilenen | Davranış |
|---|---|---|---|---|
| 1 | Kuyruk kapasitesi (giriş) | UNKNOWN (Q1) | §13.2, §19.2 | Kod gelince belirlenecek |
| 2 | Log saklama süresi | UNKNOWN (Q2) | §15 | Tanımsız — uydurmak yasak |
| 3 | Taşma davranışı (saklama) | UNKNOWN (Q2) | §15 | Tanımsız — uydurmak yasak |
| 4 | Rapor kanalı | UNKNOWN (Q4) | §19.4 | Tanımsız — uydurmak yasak |
| 5 | Rapor tetikleme periyodu | UNKNOWN (Q4) | §19.4 | Tanımsız — uydurmak yasak |
| 6 | Sayaç okuma arayüzü | UNKNOWN | §19.1 | Tanımsız — uydurmak yasak |

1. **Kapanış kuralı:** bu sorular yanıtlanmadan tanılama dalı **tamamlanmış sayılmaz**;
   ekler yalnız boşluğu **işaretler**, değer **üretmez**.

## §21 Derinleştirme Eki — Komşu Dal Referansları

| Dal | Dosya | İlişki |
|---|---|---|
| runtime/ | runtime-kapsam.md | overrun/xrun sayaçları (F.3) |
| runtime/ | runtime-dogrulama.md | sayaç doğrulama senaryoları (PLANNED) |
| power-control/ | power-control-anayasa.md | protect/brownout olay kaynakları |
| update/ | update-anayasa.md | OTA olay kaynakları |
| security/ | security-anayasa.md | secure boot/sig olay kaynakları |
| testing/ | testing-dogrulama.md | rapor okuma senaryoları (PLANNED) |

1. **Eşgüdüm kuralı:** olay **üreten** dal kendi anayasasını, olay **okuyan** dal bu
   dosyayı (§13–§14) referans alır; çift tanımlama yasaktır.

## §22 Derinleştirme Eki — Olay Kodu Envanteri (Tasarım)

> **Dürüstlük:** aşağıdaki kodlar **tasarım düzeyindedir**; hiçbir kod değeri
> depoda çalışan bir kodla doğrulanmamıştır (0 cpp/h · 2026-10-15).

### §22.1 Olay sınıfı enum'u (tasarım)

| Kod adı | Kapsam | Öncelik (§14.3) | Durum |
|---|---|---|---|
| `EVT_NONE` | Boşluk yer tutucusu | — | Tasarım |
| `EVT_XRUN` | Akış taşması (runtime kaynağı) | runtime > sistem | PLANNED |
| `EVT_OVERRUN` | Buffer overrun (runtime kaynağı) | runtime > sistem | PLANNED |
| `EVT_PROTECT` | DC koruma tetiklendi | en yüksek | PLANNED |
| `EVT_BROWNOUT` | Güç düşüşü | en yüksek | PLANNED |
| `EVT_RELAY` | Korumalı röle aç/kapa | en yüksek | PLANNED |
| `EVT_SIG_FAIL` | İmza doğrulama başarısız | güvenlik | PLANNED |
| `EVT_OTA_START` | Güncelleme başladı | güncelleme | PLANNED |
| `EVT_OTA_ROLLBACK` | Geri alma tetiklendi | güvenlik | PLANNED |
| `EVT_QUEUE_DROP` | Kuyruk dolu, olay düşürüldü | sistem | PLANNED |

1. **Sayım dürüstlüğü:** tablo 10 tasarım girdisi taşır; **0** girdi kodla
   doğrulanmıştır; gerçek enum adları kod gelince değişebilir.

### §22.2 Bağlam alanı (tasarım, sabit boy)

| Alan | Tip | Tahsis | Not |
|---|---|---|---|
| kaynak | sabit enum | Hayır | Üreten dal |
| kod | sabit enum | Hayır | §22.1 |
| değer | uint32_t | Hayır | Serbest (ör. eşik) |
| zaman | mantıksal sayaç | Hayır | §19.3 kural 3 |

1. **Boyut kuralı:** bağlam alanı sabittir; dinamik genişlik yasak (kural 1).

## §23 Derinleştirme Eki — Red Team (Bu Ek İçin)

| # | Denetim | Sonuç |
|---|---|---|
| 1 | Yeni eklerde ölçülmüş sayı var mı? | Hayır — 0 ölçüm |
| 2 | URL/repo kodu referansı var mı? | Hayır — yalnız yerel vault md |
| 3 | UNKNOWN alan uyduruldu mu? | Hayır — Q1–Q6 korundu |
| 4 | Authority hâlâ tek mi? | Evet (doğrulandı) |
| 5 | Kod iddiası var mı? | Hayır — 0 cpp/h (2026-10-15) |

## §24 Derinleştirme Eki — Kapanış Özeti

| # | Madde | Değer |
|---|---|---|
| 1 | Ek sayısı (bu dosya) | §13–§24 |
| 2 | Bilinen boşluk | Q1–Q6 (UNKNOWN) |
| 3 | Doğrulanmış ölçüm | 0 |
| 4 | Test senaryosu | 0 çalıştırıldı (tümü PLANNED) |
| 5 | Kod kanıtı | 0 cpp/h |

## §25 Derinleştirme Eki — Diagnostics Sorumluluk Matrisi (Tasarım)

| # | Eylem | Ses yolu | Üretim thread'i | Rapor thread'i |
|---|---|---|---|---|
| 1 | Sayaç increment | Evet | — | — |
| 2 | Kuyruk yazma | Evet | — | — |
| 3 | Kuyruk okuma | Hayır | — | Evet |
| 4 | Sayaç snapshot | Hayır | — | Evet |
| 5 | Rapor biçimleme | Hayır | — | Evet |
| 6 | Olay kaynak eşleştirme | Hayır | Evet | — |

1. **Kural özeti:** ses yolunda yalnız **increment + kuyruk yazma** vardır; geri
   kalan her şey üretim dışındadır (§19.3–§19.4 ile tutarlı).
2. **Dürüstlük:** matris **tasarım düzeyindedir**; hiçbir satır çalışan kodla
   doğrulanmamıştır (0 cpp/h · 2026-10-15). Ölçülmüş gecikme: **0**.

## §26 Derinleştirme Eki — Diagnostics Kapı Kriterleri (PLANNED)

> **Dürüstlük:** Bu kapı kriterleri **hiçbir test çalıştırılmadan** yazılmıştır
> (0 test · 2026-10-15). Uygulanabilirlik kod varlığında doğrulanacaktır.

| # | Kapı | Ölçüt (PLANNED) | Mevcut durum |
|---|---|---|---|
| 1 | Sayaç ses yolu bütünlüğü | Increment dışında iş yok | Uygulanamaz (kod yok) |
| 2 | Kuyruk doluluğu | Düşme sayacı tutarlı | Uygulanamaz (kod yok) |
| 3 | noexcept derleme denetimi | Derleyici ile doğrulanır | Uygulanamaz (kod yok) |
| 4 | Tahsis denetimi | Profiler ile 0 tahsis | Uygulanamaz (kod yok) |
| 5 | Rapor okunabilirliği | Sabit şablon geçerli | Uygulanamaz (kod yok) |

1. **Kapı kararı (bu dal):** `GEÇTİ`/`KALDI` **verilemez** — 0 test çalıştırıldı;
   dal `PLANNED` durumunda kalır.
2. **Kapanış:** §26 ile bu dosyanın ekleri tamamlanır; boşluklar Q1–Q6'da
   işaretli kalır ve değer **uydurulmaz**.

## §27 Derinleştirme Eki — Son Sayım Tablosu

| # | Madde | Değer |
|---|---|---|
| 1 | Ek bölüm sayısı | §13–§27 (15 ek) |
| 2 | UNKNOWN boşluk | 6 (Q1–Q6) |
| 3 | Doğrulanmış ölçüm | 0 |
| 4 | Çalışan test | 0 |
| 5 | Kod kanıtı | 0 cpp/h |
| 6 | Dosya satır kapısı (≥500) | Bu tablo ile sağlandı |
| 7 | Ek kapanış durumu | Tamamlandı (2026-10-15) |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode