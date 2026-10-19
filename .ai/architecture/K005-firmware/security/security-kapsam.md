---
node_id: K005-firmware/security/security-kapsam
node_type: module
status: draft
parent_id: K005-firmware
children: []
depends_on:
  - K005-firmware
  - K005-firmware/security/index
evidence:
  - "K005-firmware/boot/boot-kapsam.md — lettered §A-§P iskeleti + FM — okundu 2026-10-14"
  - "K005-firmware/security/index.md — ELI15 + açık soru — okundu 2026-10-14"
  - "K005-firmware/firmware-kapsam.md §9.8 S1-S5 · §15 komşu kataloğu · §17 Ov2/Ov8 — okundu 2026-10-14"
  - "K005-firmware/firmware-bilesenler.md satır 88 (F13) · satır 395-400 (s1-s6) — okundu 2026-10-14"
  - "K005-firmware/firmware-bagimliliklar.md §3 I8 · §9.1 E1k · §10 Z3/CY5/D13 — okundu 2026-10-14"
  - "K005-firmware/firmware-master.md §3.4 S1-S8 · §11 Q1-Q5 — okundu 2026-10-14"
  - "K005-firmware/firmware-test-stratejisi.md §18 G2a-G3/G9 — okundu 2026-10-14"
validation:
  - "2026-10-14 — FM 10 alan + group + son satır Authority denetimi"
open_questions:
  - "boot ↔ security sınırı ve imza akışı tanımsız ⚠️"
  - "T5 imza algoritması SEÇİLMEDİ (UNKNOWN) — kapsam hangi primitive'ı kapsadığını söyleyemez"
  - "Q4 (risk 2 secure boot) ve Q3 bloke edici açık sorular — kapsam onayı bekliyor"
group: mimari
---

# Security Alt-Modülü — Kapsam (K005-firmware/security/security-kapsam.md)

## §A — Kapsam

### §A.1 Rol (ELI15)

`security/index.md` ELI15'i: **"Firmware'e yetkisiz müdahaleyi engelleyen
katman: imza doğrulama, erişim kilidi, korumalı bölgeler. Kapının kilitli
olması."** `firmware-master.md` §3.4 S1'i ekler: *"Secure boot zinciri
imza doğrulamadan geçmeden firmware çalışmaz"* — [TASARIM] etiketlidir,
kanıt değildir. Basitçe: cihaz yalnız mühürlü ve geri alınmamış yazılımı
açar; kapı kilitliyse içerideki her şey güvendedir.

### §A.2 Ne KAPSAR

| # | Kapsam alanı | Ayrıntı | Sınır notu |
|---|---|---|---|
| 1 | İmza doğrulama zinciri (s1) | ROM → boot → app kök güveni | boot sınırı ⚠️ tanımsız |
| 2 | Anahtar kasası (s2 · F13) | salt-okunur bölgede anahtar erişimi | yazma yok (anayasa SA3) |
| 3 | Yetki denetleyici (s3) | komut seviyesi kabul/red | F7 bağlantılı |
| 4 | Anti-rollback denetimi (s4) | sürüm kabul/red (salt okuma) | sayacı kim yazar? ⚠️ M2 |
| 5 | Kayıt imzalayıcı (s5) | log bloğu imzası | F11 bağlantılı |
| 6 | Güvenli silme (s6) | hassas alan silme | F13 bağlantılı |
| 7 | Fail-safe kilit | reddetme/güvenli dur (E4 · H16) | fail-open yasak |
| 8 | Güvenlik gözlemi (audit) | SEC_* olay akışı | maskeleme (S6 Redactor) |
| 9 | Korumalı bölge politikası | P1-P6 (anayasa §2) | yazma yetkisi update'te |

### §A.3 Ne KAPSAMAZ

| # | Kapsam dışı | Gerekçe | Sahip |
|---|---|---|---|
| 1 | Flaşa yazma (herhangi bir bölüm) | tek yazıcı = update (Ov2) | update |
| 2 | OTA paketleme · A/B slot geçişi | paket formatı + rollback uygulaması | update |
| 3 | Boot durum makinesi (ST0-ST16) | boot anayasası | boot |
| 4 | Boot imza çağrısının fiziksel protokolü | ⚠️ tanımsız — bu dosya da uydurmaz | Q4/boundary |
| 5 | Host tarafı kripto/anahtar (web) | K009-security domaini | K009 |
| 6 | Donanım topolojisi · PCB · JTAG pinleri | K001 | K001-donanım |
| 7 | DSP/algoritma · ses akışı · host OS | K004/K003/K000 | komşular |
| 8 | OWASP web kuralının (Argon2id · AES-256-GCM) firmware'e uygulandığı iddiası | atıf serbest, uygulama iddiası yasak | anayasa §7 ilke 6 |

### §A.4 Kabul kriteri

| # | Kriter | Doğrulayıcı | Durum |
|---|---|---|---|
| K1 | V1-V5 sırası bütündür; adım atlanamaz | security-dogrulama DV-S1 | Tasarım seviyesi |
| K2 | İmza reddi → güvenli dur (E4/ST16 · H16) | DV-S2 (boot DV-B3 çapraz) | Tasarım seviyesi |
| K3 | Çağrı hatası → fail-safe (asla "aç") | DV-S3 (boot DV-B4 çapraz) | Tasarım seviyesi |
| K4 | security flaşa yazmaz (SA2 ihlali = 0) | DV-S4 kod denetimi (kod yok) | UNKNOWN (kod yok) |
| K5 | Anti-rollback: geçerli imza + eski sürüm = red | DV-S5 (boot-ornekler §B.5 köprüsü) | Tasarım seviyesi |
| K6 | Günlükte ham anahtar/hassas veri yok | DV-S6 (S6 Redactor) | Tasarım seviyesi |
| K7 | Sürücü §3 garanti kolonları test edilebilir | SS5 denetimi | Tasarım seviyesi |

### §A.5 Bağımlılıklar

| # | Bağımlılık | Tür | Yön | Not |
|---|---|---|---|---|
| 1 | K001-donanım | ROM · kök güven · SG5 · JTAG politikası | security → K001 | index.md depends_on |
| 2 | boot | F4 üretimi için çağrı alır · ST5'te tüketer | security ↔ boot | sınır ⚠️ tanımsız |
| 3 | update | görüntüyü alır, sonucu döner | security ↔ update | M1/M2 açık |
| 4 | diagnostics | audit olayları | security → | SG8 yolu |
| 5 | firmware-bagimliliklar I8/E1k/Z3 | kök-güven/OTP/anahtar saklama | tasarım bağımlılığı | dalga-3 |
| 6 | OWASP/NIST/TUF/PSA kaynakları | referans (atıf) | okuma | otomatik uygulama yok |

## §B — Bileşen listesi

| # | Bileşen | Karşılık | Dalga | Durum |
|---|---|---|---|---|
| B1 | Zincir doğrulayıcı | s1 | dalga-3 | [TASARIM] |
| B2 | Anahtar kasası | s2 · F13 | dalga-3 | [TASARIM] |
| B3 | Yetki denetleyici | s3 | dalga-3 | [TASARIM] |
| B4 | Anti-rollback (salt okunur) | s4 | dalga-3 | [TASARIM] |
| B5 | Kayıt imzalayıcı | s5 | dalga-3 | [TASARIM] |
| B6 | Güvenli silme | s6 | dalga-3 | [TASARIM] |
| B7 | Fail-safe kilit | E4 · H16 · SA5/SA6 | dalga-3 | taslak |
| B8 | Audit/redaksiyon | S6 Redactor (PHP host kanıtı) | mevcut (host) | [KANIT] host · firmware taslak |

## §C — Arayüzler

### §C.1 Security ↔ Boot arayüzü

| Yön | Arayüz | Veri | Sözleşme durumu |
|---|---|---|---|
| boot → security | imza doğrulama çağrısı | görüntü | ⚠️ protokol TANIMSIZ |
| security → boot | F4 FW_SIG_OK | bool | SÖZLEŞME (boot-surucu §2 F4) |
| security → boot | E4 / güvenlik olayı | kod | SÖZLEŞME (boot-surucu §6) |

### §C.2 Security ↔ Update arayüzü

| Yön | Arayüz | Veri | Sözleşme durumu |
|---|---|---|---|
| update → security | imzalanacak görüntü + sürüm | blok | ⚠️ M1/M2 açık |
| security → update | imza/red sonucu | sonuc + kod | TASLAK (H14 update · H16 security) |
| — | sayacı kim yazar | anti-rollback | ⚠️ ÇÖZÜLMEDİ (surucu §4 M2) |

### §C.3 Security ↔ Diagnostics / Testing arayüzü

| Yön | Arayüz | Veri | Sözleşme durumu |
|---|---|---|---|
| security → diagnostics | §6.1 SEC_* olayları | audit kaydı | TASLAK |
| testing → security | SI1-SI4 enjeksiyon tetiği | kapı komutu | ⚠️ tanımsız |

### §C.4 Register/bölge erişim kategorileri (adres düzeyi bilinmiyor)

| Bölge | Kategori | Yazma | Not |
|---|---|---|---|
| P1/P2 slotlar | salt okuma | update | anayasa §2 |
| P3 anahtar kasası | salt okuma + denetim | OTP/üretim | UNKNOWN adres |
| P4 sayaç | erişim sahibi ⚠️ | belirsiz | M2 açık |
| P5 audit | imzalama | diagnostics/yazıcı | s5 |

## §D — Bağımlılık grafiği ve açık sorular

### §D.1 Komşuluk grafiği (YAML benzeri)

```yaml
security:
  okur_bağımlılık: [K001-donanım(ROM/SG5), boot(çağrı), update(görüntü)]
  yazar: [audit(SG8 yolu)]
  yazmaz: [flash(P1-P6 → update), boot-ST-makinesi, K009, K004, K000]
  acik_sinir: [boot↔security-imza-akışı ⚠️, M1/M2 mülkiyet, T5 primitive]
```

### §D.2 Kenar tablosu

| # | Kenar | Tür | Durum |
|---|---|---|---|
| 1 | K001 → security | ROM kök güveni | ⚠️ VERIFICATION REQUIRED (boot FM notu) |
| 2 | boot → security | imza çağrısı | ⚠️ TANIMSIZ |
| 3 | security → boot | F4 | SÖZLEŞME |
| 4 | update → security | görüntü+sürüm | ⚠️ AÇIK |
| 5 | security → diagnostics | SEC_* | TASLAK |

### §D.3 Sıralı katman (katmanlı mimari)

```text
Presentation : (yok — bare-metal güvenlik katmanı)
Application  : security-surucu (arayüz/sözleşme)
Domain       : security-anayasa (kural + sınır)
Infrastructure: K001 ROM/OTP · diagnostics audit yolu
```

### §D.4 Açık sorular (bu dosyadan devralınan)

| # | Soru | Kaynak | Durum |
|---|---|---|---|
| 1 | boot ↔ security sınırı ve imza akışı tanımsız ⚠️ | security/index.md open_question | AÇIK (taşındı) |
| 2 | Q4: risk 2 dalga-3/ADR? | firmware-riskler satır 156 | AÇIK ⚠️ |
| 3 | T5 imza primitive SEÇİLMEDİ | firmware-bilesenler satır 99 | UNKNOWN |
| 4 | M1/M2 mülkiyet (H14/H16 · sayaç) | security-surucu §4 | AÇIK ⚠️ |
| 5 | G9 SBOM kapısı eksik | firmware-test-stratejisi §18 | AÇIK |

## §E — Web araştırma tablosu

> Bu dosya yeni URL üretmez; referanslar `security-dogrulama.md §E`de
> 2026-10-14 tarihli yeniden-doğrulama ile taşınır (boot-dogrulama §E ve
> firmware-web-arastira §3.2 ile aynı havuz, kopya sayılmaz).

## §F — Korunan önceki kayıt

`security/index.md` (tek mevcut dosya) bu üretimde **değiştirilmemiştir**;
çocuk kataloğu (3 dosya) ve açık sorusu korunmuştur. Görevle yazılan 5
dosya: anayasa · surucu · kapsam · ornekler · dogrulama — index.md'nin
çocuk listesi 3 satır kalır (kayıt: liste genişletilmedi, değişiklik yok).


---

## §G — Tehdit Modeli ve Savunma Derinliği Kapsamı (derinleştirme eki)

### §G.1 Kapsamdaki tehdit yüzeyleri (T1–T8)

| # | Tehdit yüzeyi | Vektör | Kapsam sahibi | Kapsam durumu |
|---|---|---|---|---|
| T1 | Sahte/Değiştirilmiş boot image | flash yazım yolu · downgrade | security + boot | KAPSAR (imza denetimi V1–V5) · primitive UNKNOWN |
| T2 | Yetkisiz flash okuma/yazma | debug arayüzü · servis kodu | security | KAPSAR (yazma kilidi — security flash'a YAZMAZ) |
| T3 | İndirme kanalı manipülasyonu | ağ (OTA transport) | update (sahip) · security (doğrulama) | KAPSAR sınırı — Q4 açık |
| T4 | Anti-rollback sayacı aşımı | eski image yeniden yükleme | security (salt okunur denetim) | KAPSAR (okuma; yazım sahibi değil) |
| T5 | Kalıcı anahtar sızıntısı | bellek okuma · yan kanal | security | KAPSAR kasası arayüzü — primitive UNKNOWN |
| T6 | Güvenli debug'un açık bırakılması | JTAG/SWD | boot (kilit) · security (politika) | Kısmi — boot-dogrulama üzerinden |
| T7 | Telemetri/audit sızıntısı | log yolu | diagnostics (yayın) · security (içerik) | KAPSAR yalnız içerik politikası düzeyinde |
| T8 | Yan kanal (zamanlama/güç) | ölçüm sondası | — | KAPSAM DIŞI (kayıt §H.4) |

### §G.2 STRIDE × Katman Kesişim Matrisi

| Tehdit (STRIDE) | Boot | Runtime | Update | Diagnostics | Testing |
|---|---|---|---|---|---|
| Spoofing | imza zinciri (BL1→BL2) | — | manifest kimliği | log kaynağı etiketi | sahte cihaz senaryosu |
| Tampering | image bütünlüğü | RAM bütünlüğü (koruma bölgesi) | A/B slot yazımı | kayıt bütünlüğü | bozuk fixture |
| Repudiation | boot sayaçları | audit olayları | rollback kaydı | telemetri | CI kanıt zinciri |
| Information Disclosure | secure debug kilidi | kilitli bellek bölgesi | güvenli transport | log gizleme | hata çıktısı filtresi |
| Denial of Service | boot watchdog | hot-path bütçesi | güncelleme kilitlenmesi | log taşması | test süresi bütçesi |
| Elevation of Privilege | strap/mod pin | izin tablosu | bootloader kilidi | servis hesabı | CI secret erişimi |

> **Sınır notu:** hücreler **sahiplik atfıdır**, uygulama iddiası değildir; her hücre
> ilgili alt-dal dosyasında (anayasa/surucu) üretilir ve orada kanıtlanır.

### §G.3 Tehdit → Kapsam Kalemleri Eşlemesi

1. T1 → §B kalemleri (boot imza denetleyicisi sözleşmesi) + §C.1 arayüzü.
2. T2 → §B kalemi (flash yazma kilidi politikası) + §C.4 erişim kategorileri.
3. T3 → update-ornekler §A.1 (manifest doğrulama) ile Q4 üzerinden bağlantı.
4. T4 → §B kalemi (anti-rollback okuyucusu) — salt okunur; sayaç yazımı update'te.
5. T5 → §B kalemi (anahtar kasası arayüzü) — primitive'siz tasarım bu dosyada bağlayıcıdır.
6. T6 → boot dalı sahibi; security yalnız politika satırı üretir (§C.1 üzerinden).
7. T7 → diagnostics yayınlar; security içerik filtresini tanımlar (§C.3).
8. T8 → kayıt: kapsam dışı; açılış koşulu yan kanal analizi ayrı ADR gerektirir.

---

## §H — Kapsam İçi Bileşen Ayrıntısı (G1–G7 envanter derinliği)

| Kod | Bileşen | Görev | Girdi | Çıktı | Kapsam dışı bırakılanlar |
|---|---|---|---|---|---|
| G1 | Boot imza doğrulayıcı | imzayı V1–V5 sırasıyla denetler | image + imza + anahtar referansı | geç/kal sinyali | algoritma seçimi (UNKNOWN — T5) |
| G2 | Anahtar kasası arayüzü | anahtar referansını sağlar (İÇERMEZ) | anahtar kimliği | referans/temas sözleşmesi | anahtar üretimi · saklanması |
| G3 | Flash yazma kilidi | yazma yetkisi politikasını uygular | erişim isteği | izin/reddet | fiziksel yazım sırası (boot sahibi) |
| G4 | Anti-rollback okuyucu | sayaç ile image sürümünü karşılaştırır | sayaç + image sürümü | uygunluk bayrağı | sayacın yazımı (update sahibi) |
| G5 | Audit olay üreticisi | güvenlik olaylarını kodlanmış üretir | olay kodu + bağlam | olay kaydı (devir) | kayıt formatı/saklama (diagnostics) |
| G6 | Politika denetleyici | debug/servis erişimini değerlendirir | istek + bağlam | karar | fiziksel arayüz kilidi (boot) |
| G7 | Kapsam denetim kapısı (K-x) | §I listesini kapıda değerlendirir | bu dosya + kanıt | PASS/BLOCK | ölçüm — HIL PLANNED |

### §H.1 Bileşen ↔ arayüz bağı

- G1 ↔ §C.1 (boot çağrısı) · G4 ↔ §C.2 (update sayacı) · G5 ↔ §C.3 (diagnostics olayı).
- G3 ↔ §C.4 (bölge/öncelik kategorisi) · G6 ↔ boot-dogrulama (debug kilidi kaydı).
- Hiçbir bileşen tek başına deploy edilemez: G1, G2 arayüzü olmadan **çalıştırılamaz** —
  araçsüz (primitive'siz) doğrulayıcı, doğrulayıcı değildir; bkz. §A.4.

### §H.2 Bileşen yaşam döngüsü (tasarım → kapı)

1. Tasarım: bu dosya (kapsam) + ilgili surucu (sözleşme).
2. Anayasa: ilgili alt-dal anayasasında sert kurala dönüşür.
3. Örnek: ilgili ornekler dosyasında taslak akış (primitive'siz).
4. Doğrulama: dogrulama dosyasında DV senaryosu (PLANNED).
5. Kapı: K-x kapı listesi (§I) + firmware-test-stratejisi G-kapıları.

### §H.3 Bağımlılık yönü (kırmızı çizgi)

- G1 → G2 (arayüz); ters yön **yasak** (doğrulayıcı kasayı çağırmaz — kasa referans üretir).
- G5 → diagnostics; audit kaydının sahibi diagnostics'tir; security yalnız olay kodunu üretir.
- G4 → update; sayaç yazımının sahibi update'tir; security **salt okur** (§C.2).

### §H.4 Kapsam dışı bileşenler (bilinçli — kayıt)

| Bileşen | Neden kapsam dışı | Sahibi |
|---|---|---|
| Yan kanal dayanıklılığı (SPA/DPA) | ayrı uzmanlık + ölçüm altyapısı gerekir | ADR bekliyor |
| Fiziksel manipülasyon direnci (glitch) | donanım kanıt gerektirir | donanım dalı |
| Ağ güvenlik politikası (transport detayı) | update transport'unun konusu | update |
| Uygulama katmanı auth (kullanıcı) | firmware dışı | K001 |

---

## §I — Kapı Kriterleri ve Kapsam Kabul Listesi (K-x · PLANNED)

| Kapı | Kriter | Kanıt tipi | Durum |
|---|---|---|---|
| K-S1 | G1 akışı V1–V5 sırasız çalışmaz (sıra zorunlu) | kod incelemesi + test | PLANNED (repo'da kod yok) |
| K-S2 | Primitive seçilmeden G1 "hazır" sayılmaz | anayasa denetimi | FAIL-BLOCKED (T5 açık) |
| K-S3 | security flash'a yazmaz (G3 politika istisnasız) | kod incelemesi | PLANNED |
| K-S4 | Anti-rollback: sayaç okuması salt-okunur | arayüz sözleşmesi denetimi | PLANNED |
| K-S5 | Her G bileşeni için açık soru kapanmış veya UNKNOWN işaretli | bu dosya + açık sorular | Kısmi (Q3/Q4/T5 açık) |
| K-S6 | Audit olay kodları diagnostics ile birebir eşleşir | çapraz tablo denetimi | PLANNED |

> **Kapı dürüstlüğü:** hiçbiri "geçti" olarak işaretlenmemiştir; ölçüm/çalıştırma
> yoktur (repo'da cpp/h dosyası yok — 2026-10-15 ölçümü). Kapılar **tanımlı, beklemede**.

### §I.1 Kapı değerlendirme sırası

1. K-S2 önce değerlendirilir (bloke edici — T5 çözülmeden K-S1'e geçilmez).
2. K-S3/K-S4 birlikte (flash yazım yolu tek incelemede ele alınır).
3. K-S5 her üretim turunda yeniden sayılır (açık soru sayısı artarsa kapı FAIL).
4. K-S6 yalnız anayasa/surucu/olay kodu eşleşmesinde (CI otomasyonu dalga-3).

### §I.2 Kapı → test ilişkisi (devir)

- K-S1 → testing dalı DV-T serisi (firmware-test-stratejisi §18 G-kapıları üzerinden).
- K-S3 → testing-ornekler §C izlenebilirlik tablosu.
- HIL gerektirenler (K-S1 ölçüm varyantı) → G9 firmware HIL workflow **TANIMLI DEĞİL**
  (firmware-test-stratejisi §3 · testing-ornekler §B.2 — workflow YOK kaydı).

---

## §J — Kaynak ve Dayanak Kanıtı (bu dosyanın derinleştirmesi)

| İddia | Kaynak | Konum | Okuma |
|---|---|---|---|
| Root of Trust / imza zinciri kapsama alınır | .ai/electronic/firmware/ota-update.md | Satır 68 (§5) | 2026-10-15 |
| İmza algoritması seçimi ayrı karar (bizde UNKNOWN) | .ai/electronic/firmware/ota-update.md | Satır 79 (§5.1) | 2026-10-15 |
| Anti-rollback monoton sürüm kuralı | .ai/electronic/firmware/ota-update.md | Satır 134 (§7.1) | 2026-10-15 |
| Tehdit modeli + savunma derinliği referansı | .ai/electronic/firmware/ota-update.md | Satır 408 (§22) · 419 (§22.1) | 2026-10-15 |
| Secure boot kontrolü boot tarafında | .ai/electronic/firmware/boot-sequence.md | Satır 245 (§13) | 2026-10-15 |
| Anti-rollback boot kontrolü | .ai/electronic/firmware/boot-sequence.md | Satır 255 (§13.1) | 2026-10-15 |
| Secure debug kilidi | .ai/electronic/firmware/boot-sequence.md | Satır 411 (§23.1) | 2026-10-15 |
| Repo firmware kodu yok (kanıt sınırı) | 2026-10-15 ölçümü | cpp/h = 0 dosya | 2026-10-15 |
| Vault komşu kanıtları | K005-firmware/firmware-bagimliliklar.md · firmware-master.md | §I8 · §3.4 | 2026-10-14 |

### §J.1 İddia ↔ kanıt kuralı (bu dosyaya özgü)

1. Bu dosyadaki her tasarım satırı ya bir vault kaynağına ya da açık bir mantıksal
   türetime dayanır; **çalıştırılmış/ölçülmüş iddiası taşımaz**.
2. Erişilemeyen kaynak burada "⚠️ VERIFICATION REQUIRED" ile işaretlenir (uydurma yok).
3. Donanım/marka/model iddiaları yalnız vault kaynaklıdır; bu ekte **yeni marka adı üretilmez**.

---

## §K — Kapsam Dışı ve Açık Soru Bağlantısı (kapanış)

### §K.1 Açık soru ↔ kapsam etkisi

| Açık soru | Kapsam etkisi | Bu dosyadaki kayıt |
|---|---|---|
| T5 imza primitive'si SEÇİLMEDİ | G1 tasarımı bağlayıcı değil; yalnız akış sırası bağlayıcı | §A.4 · §I K-S2 |
| Q4 imza sahipliği (update ↔ security) | G1'in çağrısı kimde belirsiz | §C.2 · §H.1 |
| Q3 bloke edici açık sorular | K-S5 kısmi kalır | §I K-S5 |
| Yan kanal kapsamı (§H.4) | bilinçli kapsam dışı | §H.4 |

### §K.2 Bu dosyanın sonraki üretim koşulu

1. T5 seçilirse: §H G1 satırı "primitive'siz" ibaresinden kurtulur, §I K-S2 değerlendirilir.
2. Q4 çözülürse: §C.2 gerçek arayüz sözleşmesine dönüşür (surucu §3–§5 güncellenir).
3. Her iki durumda da **append-only** kuralı geçerlidir: mevcut metin silinmez,
   karar değişikliği yeni bölüm (§L) olarak üretilir.

### §K.3 Devir özeti

- Boot'a devredilen: debug kilidi fiziksel uygulaması, image yazım sırası.
- Update'e devredilen: sayaç yazımı, transport güvenliği, manifest.
- Diagnostics'a devredilen: audit kaydının saklanması/formatı.
- Testing'e devredilen: K-x kapılarının çalıştırılması (CI/HIL).


---

## §L — Çalışma Zamanı Güvenlik Politikası Kapsamı (L1–L6 tasarım düzeyi)

### §L.1 Bellek/bölge politikası (tasarım — ölçülmedi)

| Kod | Bölge | Erişim | İçindekiler | Kırılırsa |
|---|---|---|---|---|
| L1 | Sabit kod bölgesi | salt-oku-ürüt | bootloader + doğrulayıcı | imza zinciri güvensiz |
| L2 | Anahtar bölgesi | salt-oku-çekirdek | kasa referansları (G2) | T5 gerçekleşir |
| L3 | Politika/sayaç bölgesi | salt-oku (security) | anti-rollback sayacı (G4) | T4 gerçekleşir |
| L4 | Audit tamponu | yaz (G5) + oku (diagnostics) | olay kayıtları | repudiation zayıflar |
| L5 | Test/servis alanı | koşullu | teşhis | T2/T6 vektörü açılır |
| L6 | Uygulama yığını | R/W | iş mantığı | yetki yükselme riski |

1. Bölge tablosu **tasarım düzeyidir**; gerçek MPU/SPU register haritası repo'da yok
   (cpp/h = 0 · 2026-10-15) → register karşılıkları UNKNOWN.
2. Kural: L2 ve L3'e yalnız çekirdek doğrulayıcı yazma yetkisi ister; istisna loglanır.
3. Kural: L5, üretim (release) derlemesinde devre dışı bırakılır; devre dışı bırakma
   başarısızsa boot fail-secure ile durur (bkz. §M.2).
4. Bu tablo, runtime-surucu (izin/zaman) ve boot (fiziksel kilit) ile çakışırsa
   **çakışma kaydı** bu dosyada tutulur; karar orkestratör/ADR seviyesindedir.

### §L.2 Olay üretimi ve gecikme bütçesi (mantıksal)

- Kritik olaylar (T1/T2/T4 tetiklenmesi) **senkron** üretilir: doğrulama akışı
  içinde, karar verilmeden önce (gecikme = doğrulama süresine dahil).
- Rutin olaylar (politika reddi gibi) **asenkron** kuyruğa alınır; kuyruk doluysa
  olay **düşürülmez, sayaçlanır** (düşürme repudiation açığı yaratır).
- Bütçe: olay üretiminin doğrulama yoluna eklemesi UNKNOWN (ölçüm PLANNED —
  rtos.md §12 WCET protokolü [Satır: 219] üzerinden ölçülecek).

### §L.3 Zamanlama ↔ güvenlik etkileşimi

| Durum | Zamanlama davranışı | Güvenlik tepkisi |
|---|---|---|
| Doğrulama zaman aşımı | bekleyen iş bırakılır | **kal kararı** (fail-secure; süre artışı değil) |
| Öncelik tersine çevirme riski | kilitli kapı (priority ceiling) | doğrulayıcı kritik bölgede kilit kullanmaz |
| Gözlem yolu gecikir | sayaç artmaya devam eder | audit sayacı bağımsız (L4) |
| Kuyruk taşması | en yeni olay saymacaya gider | olay kaybı **sayılarak** raporlanır |

### §L.4 Güncelleme ile etkileşim (kapsam sınırı)

1. Update başlarken: G4 sayaç okunur (salt-oku) → uygunluk kararı update'e yazılır.
2. Update sırasında: G3 yazma kilidi **açık** bırakılmaz — istisna yalnız update'in
   kendi slotuna yazımıdır ve bu istisna update-kapsam §C.1'de kayıtlıdır.
3. Update sonrası ilk boot: G1 akışı değişmeden çalışır (V1–V5); kapı K-S2 değişmez.

### §L.5 Diagnostics ile etkileşim

- Diagnostics, güvenlik olaylarını **okuyabilir, değiştiremez** (L4 tek-yönlü yazım).
- Telemetri dışarı çıkarken içerik filtresi security'nin §G.1/T7 sorumluluğudur:
  anahtar materyali **asla** olay payload'ına girmez (payload alanı bu dosyada
  tanımlı değil → UNKNOWN).

### §L.6 Testing ile etkileşim

- Test düzeneği, L5 (test alanı) üzerinden yalnız **test** derlemesinde açılır.
- K-S3 testi (security flash'a yazmaz) iki derlemede de çalıştırılmalıdır (test + release).
- HIL gereken varyantlar G9 kapısına bağlıdır; G9 workflow'u **tanımlı değildir**.

---

## §M — Hata Durumu ve Fail-Secure Davranışı (M1–M5)

| Kod | Hata | Algılama | Tepki | Kayıt |
|---|---|---|---|---|
| M1 | İmza doğrulama başarısız (V1–V5) | G1 kararı | boot **durdur** · tekrar deneme yok | olay kodu (G5) |
| M2 | Anahtar referansı okunamıyor (G2) | kasa erişim hatası | kal — fail-secure (asla "geç" değil) | olay kodu |
| M3 | Sayaç okuma hatası (G4) | I/O hatası | update'e UNKNOWN döndür; update karar vermez | olay kodu |
| M4 | Politika denetimi belirsiz (G6) | bağlam eksik | **reddet** (varsayılan güven duyma) | olay kodu |
| M5 | Audit tamponu dolu (L4) | doluluk | sayaçla + en eski kaydı işaretle; sessiz kayıp yasak | olay kodu |

### §M.1 Fail-safe yönü (bağlayıcı)

1. Belirsizlik → **reddet/kal**; asla "geç" değil (fail-secure; fail-open yasak).
2. Hata durumu asla sessiz geçilmez: her M kodu bir olay üretir (G5).
3. Kurtarma: kurtarma yolu boot dalında tanımlıdır (boot-dogrulama §U); bu dosya
   kurtarma uygulamasını **kopyalamaz** (SSOT).

### §M.2 Üretim vs test derlemesi farkı

| Alan | Test derlemesi | Release derlemesi |
|---|---|---|
| L5 test alanı | açık (loglanır) | kapalı — açık bulunursa fail-secure |
| M4 varsayılanı | reddet (aynı) | reddet (aynı) — **fark yasak** |
| Audit olay seviyesi | detaylı | detaylı (fark yalnız gizlilik filtresinde) |
| Kapı davranışı | K-S2 bloke | K-S2 bloke (T5 çözülmeden) |

### §M.3 Hata kodu kapsamı (bu dosyanın sınırı)

- Bu dosya hata kodlarının **kategorilerini** kapsar (imza · sayaç · politika · kasa · audit).
- Kodların sayısal/atamalı karşılıkları surucu §5 üzerinden tanımlanır — orada da
  "⚠️ tanımsız" kaydı vardır (security-surucu §5 · 2026-10-14 üretimi korunur).
- Sayısal kod uydurmak bu dosyada **yasaktır** (anayasa P-dürüstlük).

### §M.4 Tepki → komşu devir

1. M1/M2 → boot (durdurma fiziksel eylemi).
2. M3 → update (karar döndürme).
3. M4 → ilgili çağıran (boot/update/runtime — bağlam belirler).
4. M5 → diagnostics (kayıt taşması yönetimi).

### §M.5 Kurtarma ≠ yeniden deneme

- İmza hatasında (M1) otomatik yeniden deneme **yasaktır** (brute-force/zamanlama kanalı açar).
- Sayaç okuma hatasında (M3) sınırlı yeniden deneme mantıksaldır; üst sınır
  ölçüm gerektirir → sınır UNKNOWN, davranış "sınırsız tekrar" **yasak**.

---

## §N — Uyum, Standart ve Referans Çerçevesi (N1–N4 · yalnız çapa)

| Çapa | Kapsama girdiği yer | Bu dosyadaki durum |
|---|---|---|
| Güvenli yükleme/imzalama zinciri | §G T1 · §H G1 | kavramsal bağ — ürün adı üretilmez |
| Sürüm monotonluğu (anti-rollback) | §G T4 · §H G4 | kavramsal bağ — sayaç yazımı update'te |
| Savunma derinliği (katmanlı kontrol) | §G.2 · §M | kavramsal bağ — tehdit modeli tablosu |
| Güvenli debug (kilitli erişim) | §G T6 | boot sahibi — politika yalnız |

1. Standart adı/ürün adı **üretilmez**; yalnız vault kaynaklı çapraz referans kullanılır
   (§J tablosu). İlgili vault kaynağı: ota-update.md §23 "Uyum ve Referans Standartlar"
   [Satır: 425] — okunmadan bu dosyada madde adı yazılmaz (⚠️ VERIFICATION REQUIRED).
2. Uyum iddiası ("sığar/geçer") bu vault'ta **yasaktır**; yalnız "kapsam ilişkisi" yazılır.
3. Uyum değerlendirmesi ayrı bir kanıt dosyası ister (kanıt grubu — K005 kökü).

### §N.1 Kapsam ↔ denetim sorusu eşlemesi

- Denetçi sorusu "eski sürüm engelleniyor mu?" → §G T4 + §H G4 + §I K-S4.
- Denetçi sorusu "anahtar nerede?" → §H G2 (arayüz) + §K (primitive açık).
- Denetçi sorusu "gizli kurtarma yolu var mı?" → §M.4 devri + boot-dogrulama kaydı.
- Denetçi sorusu "test modu üretimde açık kalır mı?" → §M.2 tablosu + §L.1 L5.

---

## §O — Devam ve Kapanış Kaydı (bu ek için)

### §O.1 Bu ekin eklediği derinlik (özet)

1. §G tehdit yüzeyi + STRIDE matrisi (8 tehdit · 6×5 kesişim).
2. §H G1–G7 bileşen envanteri + yaşam döngüsü + kapsam dışı kaydı.
3. §I K-S1…K-S6 kapıları (hiçbiri geçmedi — PLANNED/BLOCKED dürüstlüğü).
4. §J kanıt tablosu (9 satır · dosya+satır künyeli).
5. §L–§N çalışma zamanı politikası · fail-secure · uyum çapaları.

### §O.2 Değişmeyenler (korunan)

- Frontmatter (10 alan + group) · §A–§F mevcut içerik · açık sorular (T5/Q3/Q4) ·
  son satır Authority — birebir korunmuştur.

### §O.3 Sonraki tur koşulu

- T5 primitive kararı + Q4 sahiplik kararı gelmeden bu dosyada yalnız
  **açıklık azaltıcı** ekler üretilir; bağlayıcı karar üretilmez (kapı K-S2).

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
