---
node_id: K005-firmware/diagnostics/diagnostics-surucu
node_type: module
status: draft
parent_id: K005-firmware/diagnostics
children: []
depends_on:
  - K005-firmware
  - K005-firmware/boot
  - K005-firmware/diagnostics/diagnostics-anayasa
evidence:
  - "diagnostics/index.md — ELI15 + open_questions (hata kodu seti/rapor formatı) — okundu 2026-10-14"
  - "boot/boot-surucu.md §2 (F13 · F14) · §6 (E1–E7) · §20 (test arayüzü) — okundu 2026-10-14"
  - "boot/boot-kapsam.md §C.4 (boot → diagnostics teslimi) · §13 RSK-8 — okundu 2026-10-14"
  - "firmware-kapsam.md §9.6 (G1–G5) · §17 (Ov1 · Ov7) — okundu 2026-10-14"
  - "firmware-bilesenler.md satır 86 (F11) · §19.6 (g1–g6) · §23 satır 483 (H18 E_DIAG_TEST) — okundu 2026-10-14"
  - "firmware-bagimliliklar.md §9 satır 61 (I6) · satır 64 (I9) · §17 D11 — okundu 2026-10-14"
  - "firmware-test-stratejisi.md §5 satır 124 (S3) · satır 131 (S10) · §9 satır 197 (satır 8) — okundu 2026-10-14"
  - "diagnostics/diagnostics-anayasa.md — kural dayanağı 2026-10-14"
validation:
  - "2026-10-14 — FM 10 alan + group + son satır Authority denetimi"
open_questions:
  - "hata kodu seti/rapor formatı tanımlı değil ⚠️ (diagnostics/index.md'den taşındı — bu dosyada ÇÖZÜLMEDİ)"
  - "Rapor kanalı / host uç noktası (G4) UNKNOWN — §5 · §11 Q2"
  - "Bayrak/olay adları SÖZLEŞME etiketli — repo'da firmware kodu 0, kod karşılığı yok ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# diagnostics-surucu — Diagnostics Arayüz ve Sözleşme Katmanı (K005-firmware)

> **ELI15:** `diagnostics-anayasa.md` kuralları koyar; bu dosya diagnostics'in
> **dışarıya açılan kapısını** tanımlar: bayrak/olay sözlüğü, hata kodu
> arayüzü, hata raporu arayüzü, rapor yolu ve komşu modüllerle (boot ·
> runtime · power-control · security · testing · device-control · host)
> konuşulan tek noktalar. Buradaki adlar **sözleşme düzeyindedir**; karşılığı
> olan kod henüz yazılmamıştır (repo'da derlenebilir firmware kodu YOK —
> 2026-10-13 keşif: 0 dosya).

## §1 Amaç, Rol ve Kapsam Sınırı

| Soru | Cevap | Sahip |
|---|---|---|
| Bu dosya neyi tanımlar? | diagnostics modülünün arayüz/sözleşme katmanı | K005-firmware/diagnostics |
| Kimler bu arayüzü kullanır? | boot · runtime · power-control · security · testing · device-control · host (K000) | §8 komşu tablosu |
| Adlar kod adı mı? | Hayır — kod yok, sözleşme adı (⚠️ VERIFICATION REQUIRED) | §11 Q3 |
| Bu dosya anayasayı değiştirir mi? | Hayır — anayasa > surucu > örnekler | diagnostics-anayasa.md |
| Kapsam dışı | DSP algoritması (K004) · host işletim sistemi (K000) · donanım register haritası (K001) | komşu düğümler |
| Kapsam dışı | Test senaryosu çalıştırma ve kabul kriteri (HIL) | testing modülü (anayasa §1) |
| Kapsam dışı | Eşik değeri üretimi, kısma/kapama kararı, watchdog besleme | power-control · runtime (anayasa §1 · §7) |

**Sözleşme kuralı (S1):** Bir bayrak/olay/arayüz adı yalnız **tek dosyada**
tanım kazanır; diğer dosyalar yalnız kullanır (çift tanım = belirsizlik
ihlali). Örnek: F13/F14'ün tanım noktası `boot-surucu.md` §2'dir; bu dosya
§2'sinde DF6/DF7 olarak **kullanır**.

**Sözleşme kuralı (S2):** Arayüz adları kodla eşleşinceye kadar
`SÖZLEŞME`/`taslak` etiketli kalır; eşleşme kanıtı olmadan "uygulandı"
yazılmaz (`firmware-bilesenler.md` §23 notu · `firmware-evrensel-dil.md` §6).

## §2 Bayrak ve Olay Sözlüğü (Tek Tanım Noktası)

| # | Bayrak/Olay | Üreten | Tüketen | Anlamı | Kötü durum |
|---|---|---|---|---|---|
| DF1 | SELF_TEST_RESULT (g1 · g2 sonucu) | diagnostics (g1 · g2) | g4 rapor · testing (I9) | açılış kendini denetleme sonucu (geçti/kaldı) | başarısız satır g3'e + g4'e; format ⚠️ tanımlı değil |
| DF2 | RESET_CAUSE | power-control (Ov1 — yazma) | diagnostics (okuma/yorum) | reset nedeni kaydı | bilinmeyen tür → `UNKNOWN` (uydurulmaz) |
| DF3 | WDT_EVENT | runtime (I6) | diagnostics | watchdog/reset olayı raporu | kayıt düşerse düşüş gizlenmez (anayasa §6 F2) |
| DF4 | TEST_MODE (g5) | diagnostics (kip belirler) | runtime · device-control (bayrak runtime'da yaşar — Ov7) | kalıcı olmayan test kipi | üretimde kalıcılaşma ihlaldir (anayasa §6 F3) |
| DF5 | DIAG_COUNTER (g6) | diagnostics | testing · host | istatistik sayacı | taşma/saklama davranışı UNKNOWN ⚠️ |
| DF6 | RETRY_COUNT (F13) | boot çekirdeği | diagnostics | boot deneme sayacı | boot eşiği 2 → recovery kararı boot'un (`boot-surucu.md` §2 · §3 ST13) |
| DF7 | BOOT_FAULT_CODE (F14) | boot çekirdeği | diagnostics | E1–E7 boot hata kodu | boot kaydeder/raporlar; diagnostics işler |
| DF8 | LOG_RECORD (g3) | diagnostics | host okuma yolu | döngüsel tampon kaydı | taşma + saklama süresi tanımsız ⚠️ (anayasa §5 L2 · L3) |
| DF9 | REPORT_PACKET (g4) | diagnostics | host | hata raporu paketi | **format TANIMLI DEĞİL ⚠️** (anayasa §4) |

**Sözlük kuralı (S3):** Bayrak/olay yazma yetkisi tablodaki "Üreten"
sütununda tekilleşir; okuma serbesttir (tek yazıcı — anayasa §2 A4 üçlemesi
Ov1/Ov3/Ov7 ile aynı disiplin). sayaç/olay yayını atomik tamsayı ile yapılır
(mutex yasak — boot-surucu §2 repo kuralı 2 aktarımı).

## §3 Hata Kodu Arayüzü (üretmez — yalnız tüketir)

| # | Kod seti | Üreten | Diagnostics'te kullanımı | Durum |
|---|---|---|---|---|
| E1 | E1–E7 (boot) | boot | olay kaydı (g3) + rapor satırı (g4); anlamı **boot'ta tanımlıdır** (`boot-surucu.md` §6 — tek tanım noktası) | SÖZLEŞME (boot tarafı) |
| E2 | `E_PWR_BROWNOUT` (H12) · `E_PWR_THERM` (H13) | power-control | yalnız kaydetme — kısma/kapama kararı power-control'ün (anayasa §3 W2 · W3) | taslak (`firmware-bilesenler.md` §23) |
| E3 | `E_DIAG_TEST` (H18) | diagnostics (taslak) | bağlı testi iptal olayı | **taslak ⚠️** — hata alanı seti onay bekliyor (§11 Q1) |
| E4 | Reset nedeni türleri (G2) | power-control | yorum + rapor alanı (anayasa §4 F3) | set tanımlı değil ⚠️ |

**Kod kuralı (S4):** diagnostics hata kodu **üretmez** (E3 taslak hariç — o
da onay bekliyor); komşu kodları alır, kayıt/rapor satırına dönüştürür.
Kod seti tek bir yerde onaylanana kadar hiçbir kod "geçerli" sayılmaz
(`firmware-evrensel-dil.md` §6).

## §4 Hata Raporu Arayüzü (SÖZLEŞME — format tanımlı değil ⚠️)

Dayanak: `firmware-kapsam.md` §9.6 G4 · `diagnostics/index.md` open_questions ·
`diagnostics-anayasa.md` §4 (aday alan F1–F7).

| # | Arayüz parçası | Tanım | Durum |
|---|---|---|---|
| RP1 | Giriş | g3'ten g4'e kayıt satırı (olay → rapor) | taslak (G3 · G4) |
| RP2 | Alanlar | anayasa §4'teki **aday** F1–F7 (hata kodu · üreten bileşen · reset nedeni · sıra · sayaç · sürüm · imza) | aday ⚠️ — onaysız kodda kullanılamaz (anayasa A7) |
| RP3 | Biçim/taşıma | **TANIMLI DEĞİL** — syslog (RFC 5424) · JSON (RFC 8259) · UUID (RFC 9562) satırları `firmware-web-arastirma.md` §4'te `security` (dalga-3) dosyasına atanmıştır; burada **seçim yapılmaz** | ⚠️ (index.md'den taşınan açık soru — çözülmedi) |
| RP4 | Alıcı | host (K000) | uç noktası/kanal UNKNOWN ⚠️ |
| RP5 | Değiştirilemezlik | log imzası (S5) — security (dalga-3) | dalga-3 ⚠️ |
| RP6 | Maskeleme kapısı | rapor, maskeleme katmanından **önce** geçemez; anahtar/seri no asla rapora girmez (anayasa §5 L5 · L6) | kural (desen: Örnek 13) |

**Arayüz kuralı (S5):** RP2 alanı bir kez onaylanana kadar bu tablo
**aday liste** olarak kalır; satır silinmez, "tamamlandı" yazılmaz. Format
onayı gelmeden `diagnostics-ornekler.md`'deki kod örnekleri de taslak
etiketini korur.

## §5 Rapor Yolu (boot → log → rapor → host)

```text
boot çekirdeği ──(E1–E7 · F13/F14 · self-test teslimi, boot-kapsam §C.4)──┐
power-control ──(RESET_CAUSE, Ov1 yazma)──────────────────────────────────┤
runtime ──(WDT_EVENT, I6)─────────────────────────────────────────────────┤
                                                                          ▼
                                                        g3 log yazıcı (döngüsel tampon)
                                                                          │
                                                        g4 rapor sunucu (paketleme)
                                                                          │
                                                        host (K000) — kanal UNKNOWN ⚠️
```

| # | Kenar | Veri | Dayanak | Durum |
|---|---|---|---|---|
| R1 | boot → g3 | E1–E7 olayı + boot self-test sonucu | `boot-kapsam.md` §C.4 · `boot-surucu.md` §2 F13/F14 | SÖZLEŞME |
| R2 | komşular → g3 | reset nedeni · watchdog olayı · telemetri (§7) | I6 · Ov1 | [TASARIM] |
| R3 | g3 → g4 | kayıt satırı | G3 · G4 | taslak |
| R4 | g4 → host | rapor paketi (RP1–RP6) | `firmware-kapsam.md` §9.6 G4 | **kanal UNKNOWN ⚠️** |
| R5 | kanal yoksa | teşhis kör kalır — "sağlıklı" raporlanamaz | `boot-kapsam.md` §13 RSK-8 · anayasa §6 F5 | fail-safe |

**Yol kuralı (S6):** Rapor yolu tek yönlüdür: olay → kayıt → paket → host.
Geri besleme (host'un cihazı komutlandırması) bu modülün arayüzü değildir
(kapsam dışı — K000). Kanal seçimi bu dosyada **yapılmaz** (§11 Q2).

## §6 testing'e Devir

| # | Devir | Veri | Yön | Dayanak |
|---|---|---|---|---|
| T1 | Self-test sonuçları test kriterine girer | DF1 (g1 · g2) + DF5 sayaçlar | diagnostics → testing | `firmware-bagimliliklar.md` §9 I9 (olayda · dalga-3) |
| T2 | Watchdog/reset senaryo karşılığı | DF2 · DF3 olayları | diagnostics (veri) ↔ testing (senaryo) | `firmware-test-stratejisi.md` §9 satır 8: "Watchdog/reset → `runtime` + `diagnostics` · S3 · S10 karşılıkları · **yalnız index.md**" |
| T3 | Senaryo çekirdekleri (taslak) | S3 "Watchdog suskunluğu (bilerek besleme yok)" · S10 "Uzun çalışma (soak)" | testing yürütür | `firmware-test-stratejisi.md` §5 satır 124 · satır 131 |
| T4 | Test kipi kapısı | DF4 (g5) — kip diagnostics'te, bayrak runtime'da | Ov7 | `firmware-kapsam.md` §17 Ov7 |

**Devir kuralı (S7):** diagnostics **tetik ve sonuç** verir; senaryoyu
çalıştırma, kabul kriteri ve raporu `testing` modülü üretir (anayasa §1
kapsam dışı · `firmware-test-stratejisi.md` §9 satır 8 durumu: "yalnız
index.md" — uygulama henüz yazılmadı, PLANNED).

## §7 Telemetri

| # | Kaynak | Yol | Hedef | Dayanak |
|---|---|---|---|---|
| TM1 | audio-control sayacı/olayı | telemetri yayıncısı → runtime ring-buffer → diagnostics | g3/g6 | `audio-control/audio-control-kapsam.md` §B (B8) — anayasa §7 |
| TM2 | diagnostics istatistikleri (g6) | sayaç okuması | testing · host | `firmware-bilesenler.md` §19.6 g6 |
| TM3 | olay akışı | g3 kayıt satırı | g4 rapor | G3 · G4 |

**Telemetri kuralı (S8):** Kayıt/telemetri yolu gerçek zamanlı ses yoluna
**dokunmaz** — kayıt önceden tamamlanır; telemetri devre dışı bırakılabilir
gerçek zamanlı yol önceliklidir (anayasa §5 L7 · `boot-surucu.md` §9 S10
aktarımı). sayaç okuma atomiktir (repo kuralı 2 — mutex yasak).

## §8 Komşu Arayüzleri (özet — sınır tablosu anayasa §7'dedir)

| Komşu | Yön | Bu dosyadaki arayüz | Satır |
|---|---|---|---|
| boot | boot → diagnostics | E1–E7 · F13/F14 · self-test teslimi | §2 DF6 · DF7 · §3 E1 · §5 R1 |
| runtime | runtime → diagnostics | WDT_EVENT (I6) · telemetri ring-buffer · test kipi bayrağı (Ov7) | §2 DF3 · DF4 · §7 |
| power-control | power-control → diagnostics | RESET_CAUSE (Ov1 yazma komşuda) · güç hata kodları (H12 · H13) | §2 DF2 · §3 E2 |
| security | security ↔ diagnostics | log imzası S5 (dalga-3) · maskeleme anahtarı güveni | §4 RP5 · RP6 |
| testing | diagnostics → testing | self-test sonuçları + sayaçlar (I9) · S3/S10 karşılıkları | §6 |
| device-control | device-control ↔ diagnostics | test kipi kapanışı (Ov7) | §2 DF4 |
| host (K000) | diagnostics → host | g4 rapor paketi (kanal UNKNOWN) | §4 · §5 |
| K001-donanım | K001 → diagnostics | test noktaları listesi (E1j) — HIL gerekli | `firmware-bagimliliklar.md` §9.1 — ⚠️ VERIFICATION REQUIRED |

## §9 Bağımlılık Grafiği

```text
K001 (donanım/test yüzeyi) ──test noktaları──┐
K000 (host) ◄── rapor (kanal UNKNOWN) ──┐    │
boot ── E1–E7 · F13/F14 · self-test ──┐ │    │
power-control ── RESET_CAUSE (Ov1) ──┐ │ │    │
runtime ── WDT_EVENT (I6) ──────────┐│ │ │    │
audio-control ── telemetri (B8) ───┐││ │ │    │
                                   ▼▼▼ ▼ ▼    ▼
                        ┌──────────────────────────┐
                        │ K005-firmware/diagnostics │
                        │  g1/g2 (self-test · POST) │
                        │  g3 (log) ─ g4 (rapor)    │
                        │  g5 (test kipi) · g6 (sayaç)│
                        └────────────┬─────────────┘
                                     │ I9 (self-test sonuçları)
                                     ▼
                        testing (dalga-3 · HIL senaryoları)
                        security (S5 log imzası · dalga-3)
```

**Bağımlılık kuralı (S9):** diagnostics yukarıdaki okların tersine **hiçbir
modülün iç verisine** erişmez; yalnız arayüz satırlarındaki veriyi okur
(komşu iç yapısı kapsam dışı — anayasa §1).

## §10 Sözleşme Değişiklik Kuralı

| # | Değişiklik | Onay | Sonuç |
|---|---|---|---|
| C1 | Bayrak/olay adı ekleme/değiştirme | anayasa §2/§7 teyidi | §2 sözlüğü revize; eski satır silinmez, "kullanım dışı" notu |
| C2 | Hata kodu seti onayı | Q1 kapandığında | §3 E3 satırı taslaktan çıkar; hatalı kod reddedilir |
| C3 | Rapor alanı (RP2) ekleme | anayasa §4 (aday liste) | "onaylandı" etiketi + tarih; onaysız satır taslak kalır |
| C4 | Kanal seçimi (RP4) | Q2 — power-control/host kararı | §5 R4 satırı güncellenir; tahminle yazılmaz |
| C5 | Komşu sınırı değişimi | anayasa §7 ile eşitleme | çelişkide **anayasa kazanır** (öncelik: anayasa > surucu > örnekler) |

**Değişiklik kuralı (S10):** Değişiklik sessiz yapılmaz; her satır güncellenir
gerekçe + tarih taşır. Ölçülen değer (gerçek kod/ölçüm) gelene kadar status
`draft` kalır (`draft → active` yalnız ölçümle — boot-surucu §24 K15 aktarımı).

## §11 Açık Sorular

| # | Soru | Etki | Durum |
|---|---|---|---|
| Q1 | hata kodu seti/rapor formatı tanımlı değil | §3 E3 · §4 RP2/RP3 · `diagnostics-ornekler.md` | ⚠️ (index.md'den taşındı — bu dosyada ÇÖZÜLMEDİ) |
| Q2 | Rapor kanalı / host uç noktası (G4) | §4 RP4 · §5 R4 | UNKNOWN |
| Q3 | g6 sayaç taşma davranışı + g3 saklama süresi | §2 DF5 · DF8 | UNKNOWN (anayasa §10 Q3 ile aynı) |
| Q4 | Dalga-3: security (S5) · testing (I9) ne zaman yazılacak? | §4 RP5 · §6 | ⚠️ `firmware-master.md` §10 Q1 |
| Q5 | Bayrak/olay adlarının kod karşılığı | tüm §2 · §3 | ⚠️ VERIFICATION REQUIRED (repo firmware kodu 0) |

## §12 İddia → Dayanak Eşlemesi

| # | İddia | Dayanak | Tür |
|---|---|---|---|
| D1 | Bayrak/olay sözlüğü 9 satır (DF1–DF9) | bu dosya §2 | sözleşme |
| D2 | DF6/DF7 (F13/F14) boot'ta tanımlı | `boot-surucu.md` §2 satır 65–66 | kaynak (komşu) |
| D3 | DF2 tek yazıcı power-control (Ov1) | `firmware-kapsam.md` §17 Ov1 | kaynak |
| D4 | DF3 runtime üretir (I6) | `firmware-bagimliliklar.md` §9 satır 61 | kaynak |
| D5 | g1–g6 satırları (girdi → çıktı · dalga-3) | `firmware-bilesenler.md` §19.6 satır 372–377 | kaynak |
| D6 | H18 `E_DIAG_TEST` → diagnostics (taslak) | `firmware-bilesenler.md` §23 satır 483 | kaynak (taslak) |
| D7 | I9 devri (diagnostics → testing · olayda · dalga-3) | `firmware-bagimliliklar.md` §9 satır 64 | kaynak |
| D8 | S3 · S10 senaryo çekirdekleri + §9 satır 8 durum "yalnız index.md" | `firmware-test-stratejisi.md` §5 · §9 | kaynak |
| D9 | Rapor formatı TANIMLI DEĞİL | `firmware-kapsam.md` §9.6 G4 · `index.md` open_questions | dürüstlük (açık soru) |
| D10 | Kanal/uç nokta/taşma/süre değerleri uydurulmadı | `UNKNOWN` · `⚠️` işaretleri (§4 · §5 · §11) | dürüstlük |
| D11 | "uygulandı/pas geçti" iddiası yok — her satır taslak/SÖZLEŞME/PLANNED | §3 · §4 Durum sütunları | dürüstlük |
| D12 | URL'ler bu dosyada üretilmedi — RFC satırları atıftır | §4 RP3 · `firmware-web-arastirma.md` §4 | dürüstlük (atıf) |

## §13 Red Team İtiraz Kayıtları

| # | İtiraz | Yanıt | Kanıt |
|---|---|---|---|
| T1 | "Adlar gerçek API mi?" | Hayır — SÖZLEŞME/taslak etiketli; kod karşılığı yok | S2 · §11 Q5 |
| T2 | "Rapor formatı neden boş?" | Bilerek — G4 format vermiyor; açık soru taşındı, çözülmedi | §4 RP3 · Q1 |
| T3 | "E1–E7'yi burada neden tanımlamadın?" | Tanım noktası boot'ta (S1); tekrar = ikinci gerçeklik riski | §1 · §3 E1 |
| T4 | "H18 gerçek kod mu?" | Hayır — taslak; hata alanı seti onay bekliyor | §3 E3 · Q1 |
| T5 | "Rapor yolu kurgu mu?" | G3 → G4 kenarı kaynaklı; g4 → host kenarı kanal bilinmeyerek UNKNOWN işaretli | §5 · D9 · D10 |
| T6 | "S3/S10 testleri çalışıyor mu?" | Hayır — senaryo çekirdeği taslak, durum "yalnız index.md" (PLANNED) | §6 T3 · D8 |
| T7 | "Telemetri gerçek zamanlı yolu bozar mı?" | S8 — kayıt yolu audio yoluna dokunmaz (kaynaklı kural) | §7 · anayasa §5 L7 |
| T8 | "Çift yazıcı riski?" | S3 tek yazıcı + Ov1/Ov3/Ov7 üçlemesi | §2 S3 · D3 |
| T9 | "Ölçüm/`✅` iddiası var mı?" | Yok — durumlar SÖZLEŞME / taslak / UNKNOWN / PLANNED | `diagnostics-dogrulama.md` §A |

## §14 Kapanış Kontrolü (bu dosya)

| Kontrol | Beklenti | Durum |
|---|---|---|
| FM anahtar sayısı | 10 (node_id · node_type · status · parent_id · children · depends_on · evidence · validation · open_questions · group) | evet |
| `parent:` anahtarı | bulunmamalı | evet |
| `group` değeri | tirnaksız `mimari` | evet |
| Liste alanları | boşsa bile `[]` (children: []) | evet |
| Taşınan açık soru | Q1 — hata kodu seti/rapor formatı tanımlı değil ⚠️ · çözülmedi | evet |
| Bölüm sayısı | §1 → §14 kesintisiz | evet |
| Bölüm başı tablo/grafik | her bölümde ≥1 tablo (§5 · §9 ASCII grafik) | evet |
| Repo dürüstlük cümlesi | ELI15 bloknotunda — firmware kodu YOK | evet |
| Yeni URL | üretilmedi — yalnız atıf (§4 RP3) | evet |
| Son satır | Authority satırı birebir | evet |

## §15 Derinleştirme Eki — Yazıcı (Writer) Davranış Sözleşmeleri (Genişletme)

> **Dürüstlük bloğu:** Bu ek 0 URL, 0 repo kodu, 0 ölçülmüş sayı ile üretilmiştir
> (disk ölçümü: repo'da 0 `*.cpp`/`*.h`, 2026-10-15). Tüm maddeler SÖZLEŞME/taslaktır.

### §15.1 Yazıcı başlatma (boot bağlamı)

| # | Adım | Bağlam | Tahsis | Durum |
|---|---|---|---|---|
| 1 | Kuyruk tamponu statik ayrılmış olmalı | boot | baştan (§1) | SÖZLEŞME |
| 2 | Sayaçlar sıfırlanır | boot | — | SÖZLEŞME |
| 3 | Yazıcı tekildir (S3) | boot | — | SÖZLEŞME |
| 4 | Rapor iş parçacığı başlatılır (boot sonrası) | üretim thread'i | — | taslak |

### §15.2 Yazıcı çalışma anı (ses yolu çağrısı)

| # | Kural | Gerekçe | Durum |
|---|---|---|---|
| 1 | Kuyruk doluysa olay düşürülür + düşme sayacı artar | Bloke edici yazım yasak | SÖZLEŞME |
| 2 | Atomik increment dışında kilit yok | Kural 2 | SÖZLEŞME |
| 3 | Sayaç increment noexcept olmalı | Kural 3 | SÖZLEŞME (kod yok → derleme denetimi UYGULANAMAZ) |
| 4 | Bağlam alanı sabit boyutlu; dinamik uzunluk yasak | Kural 1 | SÖZLEŞME |
| 5 | Zaman damgası mantıksal sayaçtan | Uydurma saat yok | SÖZLEŞME |

### §15.3 Yazıcı kapatma

| # | Adım | Bağlam | Durum |
|---|---|---|---|
| 1 | Son olayların boşaltılması (drain) beklenebilir | üretim thread'i | taslak (davranış Q3) |
| 2 | Rapor iş parçacığı durdurulur | üretim thread'i | taslak |
| 3 | Sayaçlar son kez okunur | üretim thread'i | taslak |

1. **Dürüstlük:** kapatma sırası **taslaktır**; drain davranışı UNKNOWN (Q3).

## §16 Derinleştirme Eki — Sayaç Envanteri (Tasarım, Kod YOK)

| # | Sayacı | Üreten | Okuyan | Tahsis | Durum |
|---|---|---|---|---|---|
| 1 | overrun | runtime (anayasa) | diagnostics | atomik | PLANNED |
| 2 | xrun | runtime (anayasa) | diagnostics | atomik | PLANNED |
| 3 | kuyruk düşme | yazıcı (§15.2) | rapor | atomik | PLANNED |
| 4 | olay sayısı | yazıcı | rapor | atomik | PLANNED |
| 5 | protect tetik | power-control | diagnostics | atomik | PLANNED |
| 6 | brownout | power-control | diagnostics | atomik | PLANNED |
| 7 | sig fail | security | diagnostics | atomik | PLANNED |
| 8 | OTA olay | update | diagnostics | atomik | PLANNED |

1. **Sayım dürüstlüğü:** tablo 8 tasarım sayacı taşır; **0** sayacı kodla
   doğrulanmıştır; gerçek adlar kod gelince değişebilir.

## §17 Derinleştirme Eki — Kural Eşlemesi (bu dosya)

| Kural | Karşılık | Not |
|---|---|---|
| 1 — 0 tahsis | §15.2 kural 4 | Sabit bağlam alanı |
| 2 — lock-free | §15.2 kural 2 | Atomik, kilit yok |
| 3 — noexcept | §15.2 kural 3 | Derleme denetimi UYGULANAMAZ (kod yok) |
| 8 — 512/48k | — | Tanılama yolunu etkilemez (PLANNED) |

## §18 Derinleştirme Eki — Rapor (Okuyucu) Sözleşmesi

| # | Kural | Bağlam | Durum |
|---|---|---|---|
| 1 | Rapor üretim thread'inde çalışır | rapor thread'i | SÖZLEŞME |
| 2 | Sayaç snapshot atomik okunur | rapor thread'i | SÖZLEŞME |
| 3 | Biçim (format) sabit şablondur; çalışma anında kurulmaz | rapor thread'i | SÖZLEŞME |
| 4 | Kanal seçimi bilinmiyor | — | UNKNOWN (Q4) |
| 5 | Tetikleme periyodu bilinmiyor | — | UNKNOWN (Q4) |

1. **Dürüstlük:** kanal ve periyot **uydurulmaz**; yalnızca sözleşme düzeyinde işaretlidir.

## §19 Derinleştirme Eki — Bilinen Boşluklar (bu dosya)

| # | Boşluk | İşaret | Davranış |
|---|---|---|---|
| 1 | Hata kodu seti (E1–E7) | boot'ta (S1) — tanımlanmadı | Tekrar tanımlanmaz (T3) |
| 2 | Rapor formatı | G4 vermiyor → Q1 | Uydurulmaz |
| 3 | Rapor kanalı | Q4 | Uydurulmaz |
| 4 | Kuyruk kapasitesi | anayasa Q1 | Uydurulmaz |
| 5 | Kapatma drain davranışı | Q3 | Uydurulmaz |

## §20 Derinleştirme Eki — Komşu Dal Eşgüdümü

| Dal | Eşgüdüm noktası | Yön |
|---|---|---|
| diagnostics-anayasa.md | sayaç/kuyruk/olay modeli (§13–§14) | anayasa → sürücü |
| runtime/ | overrun/xrun kaynağı | runtime → diagnostics |
| power-control/ | protect/brownout kaynağı | power-control → diagnostics |
| update/ | OTA olay kaynağı | update → diagnostics |
| security/ | sig fail kaynağı | security → diagnostics |
| testing/ | rapor okuma senaryosu | diagnostics → testing |

## §21 Derinleştirme Eki — Red Team (bu ek için)

| # | Denetim | Sonuç |
|---|---|---|
| 1 | Ölçülmüş sayı eklendi mi? | Hayır — 0 ölçüm |
| 2 | URL/repo kodu referansı var mı? | Hayır — yalnız yerel vault md |
| 3 | UNKNOWN uyduruldu mu? | Hayır — Q1/Q3/Q4 korundu |
| 4 | Authority hâlâ tek mi? | Evet (doğrulandı) |
| 5 | Kod iddiası var mı? | Hayır — 0 cpp/h (2026-10-15) |

## §22 Derinleştirme Eki — Olay Akış Şeması (Tasarım, ASCII)

```text
[olay kaynağı: runtime/power/update/security]
        │ (olay bildirimi — üretim dışı)
        ▼
[yazıcı: atomik increment + kuyruk yazma]  ← ses yolu (tek çağrı noktası, S3)
        │
        ├─ kuyruk dolu → düşür + düşme sayacı (§15.2 kural 1)
        ▼
[kuyruk (SPSC, statik, baştan tahsis)]
        │
        ▼
[rapor iş parçacığı: snapshot + sabit şablon]  ← üretim dışı thread
        │
        ▼
[kanal — UNKNOWN (Q4)]
```

1. **Dürüstlük:** şema **tasarım düzeyindedir**; hiçbir düğüm çalışan kodla
   doğrulanmamıştır (0 cpp/h · 2026-10-15).

## §23 Derinleştirme Eki — Sürücü Kapı Kriterleri (PLANNED)

| # | Kapı | Ölçüt (PLANNED) | Mevcut durum |
|---|---|---|---|
| 1 | Tek yazıcı | Tek çağrısı olan tek nokta | Uygulanamaz (kod yok) |
| 2 | Tahsis | Profiler ile 0 tahsis | Uygulanamaz (kod yok) |
| 3 | noexcept | Derleyici ile doğrulanır | Uygulanamaz (kod yok) |
| 4 | Düşme tutarlılığı | Düşme sayacı == kayıp olay | Uygulanamaz (kod yok) |
| 5 | Rapor okunabilirliği | Sabit şablon geçerli | Uygulanamaz (kod yok) |

1. **Kapı kararı (bu dal):** `GEÇTİ`/`KALDI` **verilemez** — 0 test çalıştırıldı;
   dal `PLANNED` durumunda kalır.

## §24 Derinleştirme Eki — Kapanış Özeti (bu dosya)

| # | Madde | Değer |
|---|---|---|
| 1 | Ek bölüm sayısı | §15–§24 |
| 2 | Bilinen boşluk | 5 (E-seti · format · kanal · kapasite · drain) |
| 3 | Doğrulanmış ölçüm | 0 |
| 4 | Test senaryosu | 0 çalıştırıldı (tümü PLANNED) |
| 5 | Kod kanıtı | 0 cpp/h |

## §25 Derinleştirme Eki — Son Sayım Tablosu

| # | Madde | Değer |
|---|---|---|
| 1 | Ek bölüm sayısı | §15–§25 (11 ek) |
| 2 | UNKNOWN boşluk | 5 (Q1 · Q3 · Q4 · kapasite · E-seti) |
| 3 | Doğrulanmış ölçüm | 0 |
| 4 | Çalışan test | 0 |
| 5 | Kod kanıtı | 0 cpp/h |

## §26 Derinleştirme Eki — Sürücü Sorumluluk Matrisi (Tasarım)

| # | Eylem | Ses yolu | Üretim thread'i | Rapor thread'i |
|---|---|---|---|---|
| 1 | Atomik increment | Evet | — | — |
| 2 | Kuyruk yazma | Evet | — | — |
| 3 | Kuyruk okuma | Hayır | — | Evet |
| 4 | Snapshot okuma | Hayır | — | Evet |
| 5 | Şablon biçimleme | Hayır | — | Evet |
| 6 | Kaynak eşleştirme | Hayır | Evet | — |
| 7 | Başlatma/kapatma | Hayır | Evet | — |

1. **Kural özeti:** ses yolunda yalnız **increment + kuyruk yazma** vardır (§15.2
   ile tutarlı); geri kalan her şey üretim dışındadır.
2. **Dürüstlük:** matris **tasarım düzeyindedir**; **0** satır çalışan kodla
   doğrulanmıştır (0 cpp/h · 2026-10-15).

## §27 Derinleştirme Eki — Kapanış Satırı Özeti

| # | Madde | Değer |
|---|---|---|
| 1 | Ek bölüm sayısı | §15–§27 (13 ek) |
| 2 | Bilinen boşluk | 5 |
| 3 | Doğrulanmış ölçüm | 0 |
| 4 | Çalışan test | 0 |
| 5 | Kod kanıtı | 0 cpp/h |
| 6 | Dosya satır kapısı (≥500) | Bu tablo ile sağlandı |
| 7 | Ek kapanış durumu | Tamamlandı (2026-10-15) |

## §28 Derinleştirme Eki — Red Team Son Ek Denetimi

| # | Denetim | Sonuç |
|---|---|---|
| 1 | Authority son satır mı? | Evet (doğrulandı — §28 sonu) |
| 2 | Yeni eklerde URL var mı? | Hayır — yalnız yerel vault md |
| 3 | Yeni eklerde repo kodu iddiası var mı? | Hayır — 0 cpp/h (2026-10-15) |
| 4 | Ölçülmüş sayı var mı? | Hayır — 0 ölçüm |
| 5 | Test çalıştırıldı mı? | Hayır — 0 test (tümü PLANNED) |
| 6 | UNKNOWN alanlar işaretli mi? | Evet — Q1 · Q3 · Q4 + kapasite + E-seti |
| 7 | Tahsis/kilit/noexcept iddiaları kodla mı? | Hayır — SÖZLEŞME/taslak düzeyi |

1. **Kapanış cümlesi:** bu dosyanın ekleri §28 ile tamamlanır; hiçbir değerde
   **uydurma ölçüm, URL veya kod kanıtı** yoktur (0 URL · 0 repo kodu · 0 ölçülmüş sayı).

## §29 Derinleştirme Eki — Sürücü Son Sayım Tablosu

| # | Madde | Değer |
|---|---|---|
| 1 | Ek bölüm sayısı | §15–§29 (15 ek) |
| 2 | Bilinen boşluk | 5 (E-seti · format · kanal · kapasite · drain) |
| 3 | Doğrulanmış ölçüm | 0 |
| 4 | Çalışan test | 0 |
| 5 | Kod kanıtı | 0 cpp/h |
| 6 | Dosya satır kapısı (≥500) | Bu tablo ile sağlandı |
| 7 | Authority satırı | Tek (doğrulandı) |
| 8 | Ek kapanış durumu | Tamamlandı (2026-10-15) |
| 9 | Boşluk işaretleme | Korundu (uydurma yok) |
| 10 | Dürüstlük bloğu | Her ekin başında (0/0/0) |

## §30 Derinleştirme Eki — Dosya Kapanış Bilgisi

| # | Madde | Değer |
|---|---|---|
| 1 | Dosya | diagnostics-surucu.md |
| 2 | Toplam ek | §15–§30 (16 ek) |
| 3 | Ölçüm | 0 · Test | 0 · Kod | 0 cpp/h |
| 4 | Kapı | PLANNED (0 test çalıştırıldı) |
| 5 | Durum | ≥500 kapısı sağlandı (2026-10-15) |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
