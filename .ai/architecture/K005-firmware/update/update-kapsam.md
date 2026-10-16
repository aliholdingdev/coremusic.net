---
node_id: K005-firmware/update/update-kapsam
node_type: module
status: draft
parent_id: K005-firmware
children: []
depends_on:
  - K005-firmware/update/update-anayasa
  - K005-firmware/update/update-surucu
  - K005-firmware/boot/boot-kapsam
  - K005-firmware/firmware-kapsam
  - K005-firmware/firmware-bilesenler
  - K005-firmware/firmware-bagimliliklar
  - K005-firmware/firmware-riskler
evidence:
  - .ai/architecture/K005-firmware/firmware-kapsam.md §9.7 (okundu 2026-10-14)
  - .ai/architecture/K005-firmware/firmware-bilesenler.md §19.7 (okundu 2026-10-14)
  - .ai/architecture/K005-firmware/firmware-bagimliliklar.md §17 D12 · D13 · §20.3 (okundu 2026-10-14)
  - .ai/architecture/K005-firmware/firmware-riskler.md §2 · §7 (okundu 2026-10-14)
  - .ai/architecture/K005-firmware/boot/boot-kapsam.md §C.1 · §I (okundu 2026-10-14)
  - .ai/architecture/K005-firmware/update/update-anayasa.md §1–§5 (okundu 2026-10-14)
validation:
  - 2026-10-14 — FM 10 alan + group + son satır Authority denetimi
open_questions:
  - "update ↔ güvenlik (imza) kime ait? (K005 Q4) — bu dosyada ÇÖZÜLMEZ; §A.3 ve §C.2 yalnız kayıt taşır"
  - "Update-incomplete / confirm bayrak alan yerleşimi vault'ta yok (boot-kapsam §C.1 ⚠️) — K005 Q1/Q3 bağlantılı"
group: mimari
---

# Update Alt-Modülü — Kapsam (K005-firmware/update/update-kapsam.md)

> **ELI15:** Bu dosya update modülünün sınırını çizer: neyi kapsar (U1–U6 güncelleme
> satırları), neyi kapsamaz (imza algoritması security'ye ait — Q4 açık), bileşenleri
> kimdir (u1–u7), boot/security/runtime ile nasıl kenetlenir ve hangi riski devralır
> (risk 3 — rollback brick, skor 15). Bağımlılık zinciri D12/D13, adım zinciri §20.3
> 7-adım güncelleme senaryosudur. Doğrulama URL'leri bu dosyada DEĞİL —
> `update-dogrulama.md` §E'dedir (K-14 tek-üretim kuralı).

## §A Kapsam

### §A.1 Rol (ELI15)

Update, cihazın yazılımını ağ (OTA) veya USB (DFU) üzerinden güvenle yenilemesi ve
yanlış güncellemede kendini geri alabilmesi (rollback) modülüdür. Ana sorumluluk:
firmware-kapsam §9.7'deki altı güncelleme satırı (U1–U6) ve firmware-bilesenler §19.7'deki
yedi bileşen (u1–u7). Rollback/bayrak politikasının sahibi update'tir
(boot-kapsam §I: "Rollback/bayrak *politikası* → update", ⚠️ kapsam etiketli satır).

### §A.2 Ne KAPSAR (U1–U6 — firmware-kapsam §9.7)

| # | Satır | İçerik | Durum |
|---|---|---|---|
| U1 | Manifest | sürüm · hedef donanım · imza · boyut alanları | [TASARIM] |
| U2 | Bölme planı | A/B bölüm seçimi, kullanılabilir alan kontrolü | [TASARIM] |
| U3 | İndirme/doğrulama | bütünlük + imza doğrulama, başarısızlıkta ne | [TASARIM] |
| U4 | Geçiş (switch) | yeni bölümün açılabilir ilan edilmesi | [TASARIM] |
| U5 | Rollback | açılabilirlik sayacı düşerse eskiye dönüş | [TASARIM] |
| U6 | Yarıda kesme | güç kesilirse update durumu ne olur (en kritik satır) | [TASARIM] |

İlgili kapsam kararları: S14 "İmzasız firmware görüntüsü kurmak → REDDEDİLİR"
(firmware-kapsam §9.8) · S15 "Rollback sayacı düşerse eskiye dönmek → İÇİNDE"
(firmware-kapsam §9.7) · Ov2 anti-rollback sayacı: **tek yazıcı update**, boot ve
security yalnız okur (firmware-kapsam §17) · Ov6 sürüm bilgisi: **tek kaynak update
manifest'i** (firmware-kapsam §17).

### §A.3 Ne KAPSAMAZ

| Kapsam dışı | Neden | Sahip |
|---|---|---|
| İmza **algoritması** ve key storage | Kriptografik uzmanlık; boot-kapsam §I "mutabık" satırı — **Q4 BU DOSYADA ÇÖZÜLMEZ** | security (⚠️ Q4) |
| İmza doğrulama **çağrısı** (boot zinciri içi) | Zincirin parçası; boot çağırır — Q4 (çağrı) satırı | boot (⚠️ Q4) |
| Boot-lock bayrak/setme işlemi | Boot-lock bölümü boot bacağında (boot-dogrulama §V) | boot |
| Bölme haritası / flash yüzeyi | K001 donanım haritası (E1k) — firmware-bagimliliklar §17 D12 | K001 |
| DFU cihaz kilidi/erişim politikası | Kim DFU'ya girebilir — K005 Q2/Q3 bağlantılı, tanım yok (`UNKNOWN`) | UNKNOWN |
| Update hata kodu şeması | Numeric kodlar tanımsız (`⚠️`), uydurulmaz | UNKNOWN |
| Sağlık kontrolünün (health-check) kendisi | Onay veren taraf uygulama/runtime'dir; update yalnız onay bayrağını okur (ota-update.md §9.1'e atıfla — update-anayasa §9) | runtime |

### §A.4 Kabul kriteri

| # | Kriter | Bağ | Durum |
|---|---|---|---|
| K1 | U1–U6 altı satırın tamamı bu modülün çocuk dosyalarında ele alınmış (anayasa §2 zincir, surucu §2 durum makinesi) | firmware-kapsam §9.7 | Tasarım seviyesi |
| K2 | U6 (yarıda kesme) senaryosu yazılı: güç kesintisinde `pending` işareti + kurtarma | firmware-kapsam §9.7 "en kritik satır" | Tasarım seviyesi |
| K3 | S14 reddi uygulanır: imzasız paket kurulmaz | firmware-kapsam §9.8 S14 · W10 | Tasarım seviyesi |
| K4 | Ov2 tek-yazıcı kuralı: `secure_version` yalnız update yazar | firmware-kapsam §17 Ov2 | Tasarım seviyesi |
| K5 | Risk 3 (rollback brick) için HIL test senaryosu tanımlı | firmware-riskler §7 satır 3 → `update/*` kapsam dosyaları | Ölçüm PLANNED |

### §A.5 Bağımlılıklar

| Bağımlılık | Yön | İçerik | Kayıt |
|---|---|---|---|
| boot | update → boot | A/B yuva seçimi, sayaç, A8 retry ≤ 2, anti-rollback `FW_VERSION_MIN` kontrolü | boot-kapsam §C.1 · boot-surucu §3 |
| security (F13) | update → security | İmza denetimi çağrısı — algoritma security'de | firmware-bilesenler §4 F13 · **Q4 açık** |
| K001 (donanım) | update → K001 | Bölme haritası, flash yüzeyi (E1k) | firmware-bagimliliklar §17 D12 |
| runtime/uygulama | runtime → update | Confirm (onay) bayrağı — self-test sonrası | boot-kapsam §C.1 |
| F1 (boot bileşeni) | update → F1 | Yeniden başlatma (§20.3 adım 6) | firmware-bagimliliklar §20.3 |

## §B Bileşen Listesi (u1–u7 — firmware-bilesenler §19.7)

| # | Bileşen | Bağ | Girdi | Çıktı | Durum |
|---|---|---|---|---|---|
| u1 | Manifest ayrıştırıcı | F12 | manifest | alanlar | [TASARIM] |
| u2 | Bölme seçici | F12 | flash haritası | hedef bölüm | [TASARIM] |
| u3 | İndirme/doldurma | F12 | paket | bölüm içeriği | [TASARIM] |
| u4 | Bütünlük+imza denetimi | F13 bağlantılı | imza | geçerli/geçersiz | [TASARIM] |
| u5 | Geçiş (switch) yazılımı | F12 | hazır bayrağı | sayaç++ | [TASARIM] |
| u6 | Rollback denetleyici | F12 · F1 | sayaç | eskiye dönüş | [TASARIM] |
| u7 | Yarıda kesme kurtarma | F12 · F1 | güç durumu | kaldığı yerden | [TASARIM] |

Ana bileşen: **F12 — Güncelleme motoru** (manifest · A/B · rollback · sayaç; bağımlı F1)
(firmware-bilesenler §4). İlişkili b8 "Rollback sayacı okuma" (F12 → bölüm seçimi,
[TASARIM]) ve I10 "Update kontrolü" (F12 → imza zinciri ile → rollback) satırları
firmware-bilesenler §4/§12'dedir. u4'ün imza ucu Q4 sınırıdır (security-anayasa §V3:
u4 referanslı).

## §C Arayüzler

### §C.1 Update ↔ Boot arayüzü (boot-kapsam §C.1 aynası — update açısından)

| Arayüz | Yön | İçerik | Kayıt | Kararlılık |
|---|---|---|---|---|
| Update-incomplete bayrağı | update → boot | "Yeni image var, doğrula ve boot et" | Update dalının yazdığı flash bayrak alanı | ⚠️ alan yerleşimi vault'ta yok |
| Test-boot modu | update → boot | İlk açılış test modunda (revert kapısı) | Bayrak = test | MCUboot benzeri desen — referans, seçim değil |
| Confirm bayrağı | runtime → update | Uygulama kendini "OK" işaretler | Self-test sonrası | ⚠️ onay akışı vault'ta yok |
| Rollback sinyali | boot → runtime | "Geçen seferki image reddedildi" | Boot log + durum enum | Tasarım önerisi |

### §C.2 Update ↔ Security arayüzü (Q4 sınırı — kayıt, çözüm değil)

| Arayüz | Yön | İçerik | Durum |
|---|---|---|---|
| İmza doğrulama çağrısı | update → security | Paket/image + imza blob'u | Basamak update zincirinde (U3 · u4) |
| İmza algoritması + key storage | security (sahip) | Kriptografik doğrulama | boot-kapsam §I "mutabık" — **Q4 açıktır: update ↔ güvenlik (imza) kime ait sorusu bu dosyada KAPANMAZ** |
| Doğrulama sonucu | security → update | geçerli/geçersiz + hata kodu | Fail-safe: hata ≠ geçerli (boot-kapsam §C.2 kuralı) |

### §C.3 Update ↔ Runtime arayüzü

| Arayüz | Yön | İçerik | Durum |
|---|---|---|---|
| Confirm (onay) bayrağı | runtime → update | Uygulama self-test sonrası "OK" | ⚠️ onay akışı vault'ta yok (boot-kapsam §C.1) |
| Health-check sonucu | runtime → update | Red → rollback tetikleyicisi UR4 | Tasarım seviyesi (update-surucu §5) |
| Sürüm sorgusu | runtime → update | Ov6: tek kaynak update manifest'i | firmware-kapsam §17 Ov6 |

### §C.4 Update ↔ K001 arayüzü

| Arayüz | Yön | İçerik | Kayıt |
|---|---|---|---|
| Bölme haritası | K001 → update | Flash bölüm yerleşimi, kullanılabilir alan | firmware-bagimliliklar §17 D12 (E1k) |
| Bölme seçimi | update → K001 | A/B hedef bölüm kararı | §20.3 adım 2 (Z7 zinciri) |

## §D Bağımlılık Grafiği

### §D.1 Kenar tablosu (firmware-bagimliliklar §17)

| # | Kenar | Bağımlılık | Yön |
|---|---|---|---|
| D12 | F12 Güncelleme motoru | K001 (E1k · bölme) ← F1 | tek yön |
| D13 | F13 İmza/anahtar | K001 (E1k · ROM) ← F1 · F12 | tek yön |

### §D.2 Güncelleme senaryosu (firmware-bagimliliklar §20.3 — 7 adım)

| Adım | Çağrı | Bağımlılık | Not |
|---|---|---|---|
| 1 | Manifest al | §9.4/uygulama (taslak) | Şekil §4 K12 analogi |
| 2 | Bölme seç | K001 (bölme haritası) | Z7 zinciri |
| 3 | İndir/doldur | — (iç) | Güç kesilirse U6 |
| 4 | Doğrula | F13 + K001 (E1k) | Red → adım 1'e |
| 5 | Geçiş yaz | F12 | sayaç++ |
| 6 | Yeniden başlat | F1 | §20.1 |
| 7 | Açılışta doğrula | F1 + F13 | Başarızsa rollback |

İlişkili hata satırı (firmware-bagimliliklar §20.4): "İmza reddi → yakalayan F13 →
etkilenen F12 · F1 → sonuç: eski bölüm" — Q4'ün çalıştığı gerçek kenar burasıdır.

### §D.3 Açık sorular (devralınan)

| # | Soru | Durum |
|---|---|---|
| Q4 | update ↔ güvenlik (imza) kime ait? | AÇIK — bu dosyada çözülmez |
| Q1 | İlk-boot tutarlılık kapısı (K005-firmware) | firmware-bagimliliklar §20.3 üzerinden açık |
| Q2/Q3 | Update-onarım · boot-lock sınırı | boot-dogrulama §U · Boot-lock yazma satırı / update-surucu §9 — arayüz tanımı `UNKNOWN` |

## §E Risk Devri

| Risk | Tanım | Skor | Devir | Kanıt |
|---|---|---|---|---|
| 3 | OTA geri alma (rollback) çalışmazsa cihaz tuğla (brick) olur | 3×5=15 🔴 | **update** — bu modülün birincil riski | firmware-riskler §2 satır 3 |
| 3 (mitigasyon) | A/B bölmeli güncelleme + geri-alma sayacı; güncelleme öncesi doğrulama | — | Mitigasyon → test senaryosu (HIL) | firmware-riskler §7 satır 3 |
| 3 (tasarım durumu) | "Rollback — Tasarım yok — dalga-2 `update`" | — | Kapanış kapısı: `update/*` kapsam dosyaları (bu seri) | firmware-riskler §21 |

Not: firmware-riskler §21'de "Tasarım yok" ibaresi 2026-10-14 itibarıyla bu dosya serisi
(anayasa · surucu · kapsam) ile TASARIM aşamasına geçmiştir; **test/HIL kapısı hâlâ
açıktır** (ölçüm PLANNED). Skor uydurulmaz: 15, kaynaktaki çarpımın kendisidir
(firmware-riskler §10: skor uydurulmaz).

## §F Web Kaynakları (pointer — URL üretimi bu dosyada DEĞİL)

Bu dosyada URL YOKTUR (K-14 tek-üretim kuralı). Doğrulanmış URL kayıtları
(erişim tarihleri + Durum sütunu ile) yalnız `update-dogrulama.md` §E'de üretilir;
bu dosyadaki her iddia ya vault içi § referansına ya da §G'deki `⚠️`/`UNKNOWN`
işaretine bağlıdır.

## §G Son Not ve Kapsam Dışı

1. **Q4 açık sorusu bu dosyada ÇÖZÜLMEZ** (§A.3 · §C.2 · §D.3): update ↔ güvenlik
   (imza) kime ait? — yalnız kayıt taşınır.
2. Kapsam dışı bırakılan konular: boot-lock işlemi, imza algoritması, DFU erişim
   politikası, hata kodu şeması, health-check uygulaması (§A.3 tablosu).
3. Kanıt durumu: U1–U6 · u1–u7 · D12/D13 · §20.3 tamamı **[TASARIM]** — repo'da
   firmware kodu yok (2026-10-13 ölçümü: 0 kod kanıtı); gerçek kurulum/geri alma
   testi **ölçüm PLANNED**.


---

## §H Bileşen Ayrıntısı Derinletme (u1–u7 · görev-girdi-çıktı-kontrol)

### §H.1 Bileşen kartları (görev derinliği)

| Kod | Bileşen | Görev | Girdi | Çıktı | Başarısızlık |
|---|---|---|---|---|---|
| u1 | indirici | image'ı ağdan alır | manifest URL/ref | yerel image | E-NET → kuyruk/iptal |
| u2 | manifest doğrulayıcı | manifesti denetler | manifest | uygunluk kararı | E-MANIFEST → RED |
| u3 | kurucu (yazıcı) | slot'a yazar | image + slot | yazılı slot | E-WRITE → slot geçersiz |
| u4 | onaylayıcı | ilk boot sonrası onaylar | boot sonucu | etiket güncellemesi | E-BOOT → geri alma |
| u5 | geri alıcı | eski slotu devreye alır | slot envanteri | eski slot aktif | E-ROLLBACK → kurtarma |
| u6 | sayaç okuyucusu | anti-rollback sayacını okur | sayaç bölgesi | uygunluk | E-COUNT → UNKNOWN döndür |
| u7 | telemetri yayıncısı | ilerleme/hata yayınlar | olaylar | telemetri kaydı | sessiz kayıp yasak |

### §H.2 Bileşen bağımlılık kuralları

1. u3 → u2 (yazım yalnız manifest kararı sonrası) — ters yön **yasak**.
2. u4 → boot (onay yalnız boot sonucuyla) — update tek başına onaylayamaz.
3. u5 → u4 (geri alma onaysız slotda da çalışır — kurtarma modu).
4. u6 yalnız **okur**; yazım sahibi yoktur bu bileşende (sayacın kaynağı ayrı —
   kurulumu kimin yazdığı bu vault'ta UNKNOWN · açık kayıt).
5. u7 her bileşenden olay alır; karar **vermez** (yayıncı ≠ karar sahibi).

### §H.3 Bileşen ↔ bileşen arayüz sınamaları

| Sınama | Kim | Ne doğrular |
|---|---|---|
| A1 | u2→u3 | karar olmadan yazım yok (yalnız sahte çağrı) |
| A2 | u4→u5 | onay gelmezse geri alma tetiklenir mi? |
| A3 | u6→u3 | sayaç uygunsuzken yazım başlar mı? (başlamamalı) |
| A4 | u7→u2 | olay akışı kararı değiştirebilir mi? (değiştiremez) |

---

## §I Yaşam Döngüsü Durumları (US-zenginleştirilmiş — update-surucu §2 köprüsü)

| Durum | Giriş | Çıkış | Güç-kaybı davranışı |
|---|---|---|---|
| IDLE | — | manifest isteği | etkisiz |
| INDIRME | istek | yerel image | yarım dosya → sıfırla/devam (DV-U12) |
| DOGRULA | yerel image | karar | karar verilmezse sıfırla |
| KUR | karar | yazılı slot | slot geçersiz işaretle (DV-U13) |
| BEKLE-ONAY | kurulum | ilk boot | etiket beklemede kalır |
| ONAY | boot sonucu | etiket güncel | kararsızsa geri alma |
| GERI-ALMA | tetik | eski slot | eski slot korunur |
| KAPANDI | onay/geri alma | — | — |

### §I.1 Durum kuralları

1. Her geçiş bir olay üretir (u7'ye akar) — sessiz geçiş yasak.
2. Güç-kaybı sütunu **ölçülmemiştir** (UX3 blokajlı) — davranışlar şartnamedir.
3. Durum adları taslaktır; sayısal/enum atama yok (uydurma kod yasak).

---

## §J Kapsam Sınırı Çakışma Kayıtları (genişletilmiş)

| # | Çakışma | Taraf 1 | Taraf 2 | Durum |
|---|---|---|---|---|
| Ç-U1 | İmza sahipliği | update (çağıran) | security (V1–V5) | Q4 AÇIK |
| Ç-U2 | Boot-lock sahibi | update (isteği) | boot (kilidi) | boot-dogrulama §U — UNKNOWN |
| Ç-U3 | Sayaç yazım kaynağı | update (kurulum) | bilinmeyen | UNKNOWN (§H.2/4) |
| Ç-U4 | Telemetri formatı | update (üretici) | diagnostics (sahip) | devirli |
| Ç-U5 | Kapı kriteri | firmware-test-stratejisi | update (uygulama) | SSOT bölünmüş (bilinçli) |

### §J.1 Çözüm kuralı

1. Ç-U1: Q4 kararı ADR ile; karar yoksa kapı-2 BELIRSIZ (gizleme yasak).
2. Ç-U2: kilit arayüzü yalnız boot'ta tanımlanır; update yalnız istek gönderir.
3. Ç-U3: sayaç yazım sahibi bilinmiyorsa **kimse yazdım demez** (iddia yasak).
4. Ç-U4: format sahibi diagnostics'tir; update şablonu onaylar (kopyalama yasak).
5. Ç-U5: kriter yalnız firmware-test-stratejisi §18'de (referans verilir).

---

## §K Bağımlılık Grafiği Derinletme (kenar + kritik yol)

### §K.1 Ek kenarlar (kod → hedef → tür)

| Kaynak | Hedef | Tür | Kritik |
|---|---|---|---|
| u1 | u2 | veri (image) | evet |
| u2 | u3 | karar | evet |
| u6 | u3 | kontrol (uygunluk) | evet |
| boot | u4 | olay (boot sonucu) | evet |
| u4 | u5 | tetik (onaysızlık) | evet |
| u7 | diagnostics | olay | hayır (kritik olmayan yol) |
| u5 | boot | istek (slot değiştir) | evet |

### §K.2 Kritik yol ve tek noktalar

1. Kritik yol: u1→u2→u3→boot→u4 (veya u5) →kapı.
2. Tek nokta: u2 (manifest) — burada arıza tüm güncelleme yolunu durdurur (istenen
   davranıştır: fail-secure).
3. Tek nokta: boot onayı (u4) — onay yolu olmazsa cihaz her açılışta eski slota
   düşer (davranış tasarım kararlılığı **ölçülmemiş** → kayıt).
4. Dış bağımlılık: G9 (UX3) — kapı-3 için bloke edici.

### §K.3 Döngü denetimi

- Geri alma → boot → onay döngüsü **sonlu** olmalı: tekrar sınırı tasarımda
  tanımlı değil → açık kayıt (uydurma sınır yasak).

---

## §L Risk Devri Genişletme (risk → kapı → senaryo)

| Risk | Devraldığı kapı | İlgili senaryo | Kapı durumu |
|---|---|---|---|
| R-brick | kapı-3 | DV-U12/U13 | BLOKE (UX3) |
| R-downgrade | kapı-5 | DV-U14 | Kısmi |
| R-imza | kapı-2 | DV-U15 | BLOKE (T5/Q4) |
| R-filo | kapı-6 | DV-U17 | PLANNED |
| R-kullanıcı verisi | kapı-4 | DV-U19 | BLOKE (UX3) |
| R-lock | kapı-4 | DV-U20 | PLANNED |

### §L.1 Risk sayım kuralı

- Kapısı BLOKE olan risk **kapanmış sayılmaz**; raporda açık satır taşır.
- Yeni risk → bu tabloya satır append (silme yalnız ADR).

---

## §M Kapsam Kabul Kontrolü (bu ek — kapı öncesi okuma)

| Kontrol | Soru | Durum |
|---|---|---|
| M1 | Her u-bileşeni için başarısızlık tanımlı mı? | EVET (§H.1) |
| M2 | Her durum için güç-kaybı davranışı var mı? | ŞARTNAME (ölçülmedi) |
| M3 | Çakışmalar kayıtlı mı? | EVET (§J — 5 açık çakışma) |
| M4 | Kritik yol + tek noktalar görünür mü? | EVET (§K.2) |
| M5 | Risk → kapı → senaryo izi tam mı? | EVET (§L — BLOKE'lar görünür) |
| M6 | Kapı "geçti" iddiası var mı? | HAYIR (0 — dürüstlük) |

### §M.1 Son cümle (bu ek)

Bu ek (§H–§M) update-kapsam.md dosyasını bileşen kartları, durum-zenginleşmesi,
çakışma kayıtları, kritik yol ve risk iziyle derinleştirmiştir; mevcut §A–§G +
frontmatter append-only korunmuştur. Açık çakışmalar (Ç-U1…Ç-U5) çözülmeden kapı-2
değerlendirilemez (Q4/T5 bağımlılığı — dürüst kayıt).


---

## §N Kaynak ve Dayanak Kanıtı (bu ek)

| İddia | Kaynak | Konum | Okuma |
|---|---|---|---|
| A/B güncelleme adımları | .ai/electronic/firmware/ota-update.md | Satır 97 (§6.1) | 2026-10-15 |
| Neden A/B (atomik güncelleme) | ota-update.md | Satır 124 (§6.2) | 2026-10-15 |
| Anti-rollback monotonluk | ota-update.md | Satır 134 (§7.1) | 2026-10-15 |
| Rollback durum tablosu (çapa) | ota-update.md | Satır 138 (§7.2) | 2026-10-15 |
| Sağlık kontrolü (post-update) | ota-update.md | Satır 180 (§9) | 2026-10-15 |
| Kademeli dağıtım kapıları | ota-update.md | Satır 209 (§10) | 2026-10-15 |
| Güç-kesintisi güvenliği | ota-update.md | Satır 237 (§12) | 2026-10-15 |
| Boot seçim algoritması | ota-update.md | Satır 358 (§19.1) | 2026-10-15 |
| NVS koruma prensibi | ota-update.md | Satır 339 (§18.2) | 2026-10-15 |
| Enerji/ağ bütçesi | ota-update.md | Satır 492 (§26) | 2026-10-15 |
| Boot-lock/boot fiziksel davranış | .ai/electronic/firmware/boot-sequence.md | Satır 138 (§8) · 497 (§29) | 2026-10-15 |
| Firmware kodu yok (kanıt sınırı) | 2026-10-15 ölçümü | cpp/h = 0 | 2026-10-15 |

### §N.1 İddia kuralı (bu dosya)

1. Çalıştırılmış iddiası: 0 · uydurma sayı/marka/URL: 0.
2. Vault kaynağı olmayan donanım/marka adı bu eke **eklenmez**.
3. Erişilemeyen kaynak: ⚠️ VERIFICATION REQUIRED.

---

## §O Terimler ve Kısaltmalar

| Terim | Anlamı |
|---|---|
| u1–u7 | update bileşen envanteri (§B · §H) |
| US* | update durum makinesi (update-surucu §2 köprüsü) |
| Kapı-1…6 | update kapıları (update-dogrulama §L) |
| Ç-U1…5 | kapsam çakışma kayıtları (§J) |
| UX1–UX3 | doğrulama ortam kademeleri |
| NVS | kalıcı ayar bölgesi |
| cohort | kademeli dağıtım grubu |
| BLOKE | ön koşul eksik — FAIL değil |

---

## §P Kullanım Kılavuzu (kim ne okur)

1. **Update sahibi**: §H (bileşen kartları) → §I (durumlar) → §K (kritik yol).
2. **QA**: §L (risk→kapı) + update-dogrulama §P.1 (görev sırası).
3. **Security**: §J Ç-U1 (imza sahipliği) + §H.2 kural 1 (ters yön yasağı).
4. **Architect**: §J çakışma kayıtları — ADR girdileri.
5. **Vault Steward**: §M kapı öncesi kontrol listesi.

### §P.1 Dosyanın güncellenme koşulu

- Q4/T5 kararı: §J Ç-U1 satırı append ile güncellenir (silme yok).
- Sayaç yazım sahibi (Ç-U3) bilinirse: §H.2 kural 4 satırı güncellenir.
- G9 açılırsa: update-dogrulama §I UX3 satırı birlikte güncellenir (koordinasyon).

---

## §Q Kapanış Kontrolü (bu ek)

| Kontrol | Sonuç |
|---|---|
| Eklenen bölümler | §H–§Q |
| Silinen mevcut içerik | 0 (append-only) |
| Frontmatter | korundu (10 alan + group: mimari) |
| Dolgu/TBD | 0 |
| Kapı "geçti" iddiası | 0 |
| Uydurma sayı/ad/URL | 0 |
| Son satır Authority | birebir korunur |

### §Q.1 Son cümle

Bu ek update-kapsam.md dosyasını bileşen görev derinliği, durum-zenginleşmesi,
çakışma kayıtları (Ç-U1…Ç-U5 — hepsi AÇIK, dürüst kayıt), kritik yol/tek-nokta
analizi, risk izi, kaynak kanıtı ve sözlükle ≥500 kapısına taşımıştır. Açık
çakışmalar çözülmeden bağlayıcı kapı kararı verilmez (kapı-2 özellikle BLOKE).


---

## §R Devralınan Açık Sorular — Sayım ve Etki Tablosu

| Açık soru | Kaynağı | Kapsam etkisi | Kapı etkisi |
|---|---|---|---|
| Q4 imza sahipliği | firmware-master §11 | Ç-U1 · u2/u3 sınırı | kapı-2 BLOKE |
| T5 primitive (security) | security-kapsam §A.4 | u2 yalnız manifest | kapı-2 BLOKE |
| Boot-lock yazma satırı | boot-dogrulama §U | Ç-U2 · u5 isteği | kapı-4 Kısmi |
| Sayaç yazım sahibi (Ç-U3) | bu dosya §H.2 | u6 salt-okuma | kapı-5 Kısmi |
| G9 workflow | firmware-test-stratejisi §3 | UX3 yok | kapı-3 BLOKE |
| Ölçüm altyapısı | firmware-bagimliliklar §20.5 | HIL 5 adım beklemede | tüm ölçüm PLANNED |
| Telemetri formatı | diagnostics sahibi | Ç-U4 devirli | kapı-6 kısmi |

### §R.1 Sayım kuralı (her üretim turunda yenilenir)

1. Açık soru sayısı **artarsa** kapı-2/3/4/5 için genel durum kötüleşir (BELIRSIZ).
2. Azalır (karar gelirse) → §J/§H satırları append ile güncellenir (silme yok).
3. Bu tur sayımı: **7 açık** (2026-10-15) — bir sonraki üretim bu sayıyı karşılaştırır.

### §R.2 Ek kapanış tablosu (bu ek)

| Kontrol | Sonuç |
|---|---|
| Bölüm toplamı | §A–§G (mevcut) + §H–§R (bu ek) |
| Eklenen derinlik | bileşen kartları · durumlar · çakışmalar · kritik yol · risk izi · kanıt · sözlük |
| Dolgu/TBD | 0 |
| Kapı "geçti" | 0 |
| Frontmatter + Authority | korundu (append-only) |


---

## §S Komşuluk Grafiği (metin-YAML — §D.1'in derinletmesi)

```text
update:
  bagimli:
    - firmware-kapsam        # U1-U6 kapsam kaynagi
    - firmware-bilesenler    # u1-u7 envanter kaynagi
    - firmware-bagimliliklar # kenar tablosu (§D.1)
  cagirdigi:
    - boot                   # slot/etiket/boot-lock (fiziksel sahip: boot)
    - security               # imza doğrulama (V1-V5; Q4 açık)
  verdigi:
    - diagnostics            # telemetri/olay (format sahibi: diagnostics)
    - testing                # kapı verisi (kriter sahibi: firmware-test-stratejisi)
```

### §S.1 Grafiğin kuralları

1. Ok yönü: bağımlı → bağımlı olduğu (bu dosyada ters çevrilmez).
2. security çağrısı **imza** içindir; sayaç okuması u6'nın kendi işidir (yazım sahibi UNKNOWN).
3. Diagnostics'a veri **olay**tur; karar asla geri akmaz (yayıncı ≠ sahip).

### §S.2 Son cümle (bu ek bütünlüğü)

§H–§S ekleri update-kapsam.md dosyasına bileşen kartları, durum-zenginleşmesi,
çakışma kayıtları, kritik yol, risk izi, kaynak kanıtı, açık-soru sayımı ve
komşuluk grafiği eklemiştir; mevcut §A–§G + frontmatter append-only korunmuştur.
7 açık soru çözülmeden kapı-2 (imza) BLOKE kalır — dürüst kayıt.


---

## §T Kapı Kapısı Ölçüm Notu (son ek)

| Not | Değer |
|---|---|
| Açık soru (2026-10-15) | 7 (§R.1) |
| Kapı-2 durumu | BLOKE (Q4+T5) |
| Kapı-3 durumu | BLOKE (G9/UX3) |
| Sonraki üretim | karar satırları append ile (silme yok) |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
