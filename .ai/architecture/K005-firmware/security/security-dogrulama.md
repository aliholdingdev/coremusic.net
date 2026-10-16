---
node_id: K005-firmware/security/security-dogrulama
node_type: module
status: draft
parent_id: K005-firmware
children: []
depends_on:
  - K005-firmware
  - K005-firmware/security/index
  - K005-firmware/security/security-anayasa
  - K005-firmware/security/security-surucu
evidence:
  - "K005-firmware/boot/boot-dogrulama.md — FM + §A DV-B3/DV-B4 kolon yapısı + §C.3 + §E URL satırları — okundu 2026-10-14"
  - "K005-firmware/firmware-dogrulama.md §2 OWASP/NVD/TUF/PSA URL satırları (2026-10-13) + D1-D10 — okundu 2026-10-14"
  - "K005-firmware/firmware-test-stratejisi.md §18 G2a/G2b/G2c/G3 aktif · G9 SBOM eksik — okundu 2026-10-14"
  - "K005-firmware/firmware-master.md §3.4 S1-S8 (S1 [TASARIM] · S6 [KANIT]) — okundu 2026-10-14"
  - "K005-firmware/firmware-riskler.md satır 52 (risk 2) · 156 (Q4) — okundu 2026-10-14"
  - "6 URL yeniden doğrulandı — 2026-10-14 (NIST SP 800-193 · MCUboot · TUF · RFC 8032 · OWASP · PSA — hepsi erişildi)"
validation:
  - "2026-10-14 — FM 10 alan + group + son satır Authority denetimi"
open_questions:
  - "boot ↔ security sınırı ve imza akışı tanımsız ⚠️"
  - "DV-S serisinin tamamı 'Tasarım seviyesi' — kod/ölçüm kanıtı YOK (firmware kodu 0)"
  - "T5 imza primitive UNKNOWN → DV-S7 (algoritma) ölçümü başlatılamaz"
group: kanit
---

# Security Alt-Modülü — Doğrulama (K005-firmware/security/security-dogrulama.md)

> **Kaynak notu:** (a) boot-dogrulama DV-B3/DV-B4 çaprazları · (b)
> firmware-dogrulama §2 URL kayıtları · (c) 2026-10-14 oturumunda
> erişim-Doğrulanan 6 URL · (d) gerçek vault bölüm referansları.
> Yasak: URL/sürüm/ölçüm uydurmak; "geçti" yazmak (kod yok) — her satır
> **Durum = Tasarım seviyesi / UNKNOWN** taşır; uydurulan satır sayılmaz.

## §A — Doğrulama senaryoları

| # | Senaryo | Ön koşul | Beklenen | Ölçüm yöntemi | Durum |
|---|---|---|---|---|---|
| DV-S1 | Geçerli imza güvenli açılışı geçirir | V1-V5 tamamı + imza geçerli | F4 set → boot ST6 | boot log + F4 okuması (boot-dogrulama §C.3 true kolu) | Tasarım seviyesi (kod yok) |
| DV-S2 | Geçersiz/imsız görüntü açılamaz | boot'a bozuk/imsız image | E4 → ST16 HALT · H16 ile açamama | test image'ı + boot log (boot-dogrulama DV-B3 çapraz: S7→S8) | Tasarım seviyesi |
| DV-S3 | Doğrulama çağrı hatası fail-safe | sahte/çökmüş security stub | geçersiz say → asla "aç" (S9 asla S10) | sahte security stub (boot-dogrulama DV-B4 çapraz) | Tasarım seviyesi |
| DV-S4 | security flaşa yazamaz | yazma çağrısı denenir | her koşulda red (-1) | kod denetimi / statik analiz | UNKNOWN (firmware kodu 0) |
| DV-S5 | Geçerli imza + eski sürüm reddedilir | sayaç > gelen sürüm | rollback_check → -1 | sürüm sayacı enjeksiyonu (boot-ornekler §B.5 · B-04 köprüsü) | Tasarım seviyesi |
| DV-S6 | Günlük hassas veri içermez | SEC_* olay akışı | ham anahtar/görüntü yok (Redactor) | log denetimi (firmware-master S6 [KANIT] host kanıtı) | Tasarım seviyesi (host kanıtlı · firmware taslak) |
| DV-S7 | İmza primitive seçimi doğrulanır | T5 kararı | algoritma + anahtar uzunluğu kayıtlı | karar/ADR denetimi | UNKNOWN (T5 SEÇİLMEDİ) |
| DV-S8 | Yetki denetimi her komutta çalışır | s3 | kabul/red kaydı (SEC_ERISIM) | komut enjeksiyonu | Tasarım seviyesi |
| DV-S9 | Anahtar kasasından ham anahtar sızmaz | SA3 | olaylarda yalnız anahtar no | audit akışı denetimi | Tasarım seviyesi |
| DV-S10 | Tamper olayı boot zincirini durdurmaz | SG5 | olay yayılır, boot devam eder | SG5 enjeksiyonu (boot-surucu §20) | Tasarım seviyesi |
| DV-S11 | Risk 2 kapanış ön koşulu (Q4) | Q4 kararı | ADR/erteleme kararı kayıtlı | firmware-kararlar denetimi | AÇIK ⚠️ (Q4) |
| DV-S12 | SBOM/tedarik zinciri kapısı | G9 | SBOM üretilir | firmware-test-stratejisi §18 G9 | EKSİK (G9) |

### §A.1 Senaryo ↔ anayasa/sürücü eşlemesi

| Senaryo | Doğrulanan kural | Kaynak § |
|---|---|---|
| DV-S1 | SA4 (V1-V5 atomik sıra) · SS5 | anayasa §3 · surucu §3 |
| DV-S2 | SA5/SA6 fail-safe · H16 | anayasa §6 · surucu §5 |
| DV-S3 | SA5 (fail-closed) | anayasa §6 |
| DV-S4 | SA2 (tek yazıcı = update) | anayasa §2 |
| DV-S5 | AR1/AR3 anti-rollback | anayasa §4 |
| DV-S6 | SS8 maskeleme · S6 | surucu §6.1 |
| DV-S9 | SA3 anahtar kuralı | anayasa §2 |

## §B — Test stratejisi

| # | Katman | Bu dalda | Not |
|---|---|---|---|
| 1 | Birim | rollback_check · anahtar_erişim_red (taslak) | kod yok → uygulanamaz |
| 2 | Entegrasyon | boot ↔ security stub (DV-S2/S3) | boot-dogrulama §C.3 metodu |
| 3 | HIL | SG5 · JTAG politikası · sayaç enjeksiyonu | donanım K001'e bağlı |
| 4 | Statik/kapı | G2a imza · G2b secret · G2c SBOM(eksik) · G3 | firmware-test-stratejisi §18 |

**Pass/BLOCK kapısı (bu dal):** DV-S1..S12'den **hiçbiri "geçti" olarak
kayıtlanamaz** — kod yok. Kapılar yalnız "tasarım seviyesi tamam" /
"BLOKE (Q4 · T5 · G9)" olarak raporlanır. `firmware-master` §3.4'teki
S1-S8 arasında yalnız S6 [KANIT]tır (host PHP kanıtı); S1 (secure boot)
[TASARIM] kalır — security bu kanıtı dalga-3'te üretecektir.

## §C — Ölçüm metodolojisi

| # | Ölçüm | Metod | Bağ |
|---|---|---|---|
| C1 | İmza sonucu kolonları | boot-dogrulama §C.3: geçerli→true · bozuk/imsız→false · çağrı hatası→fail-safe | DV-S1/S2/S3 |
| C2 | Reddi hedef durum | boot durum makinesi: imza reddi → S8 (uygulama çalışmaz); boot tarafı E4→ST16 | DV-S2 |
| C3 | Flash yazma denemesi | derlenmiş taslakta yazma yolu = 0 çağrı (SA2) | DV-S4 |
| C4 | Log redaksiyonu | SEC_* kayıtlarında anahtar/görüntü byte'ı aranır | DV-S6 |

## §D — Eşikler ve kapılar

| # | Kapı | Eşik | Durum |
|---|---|---|---|
| 1 | G2a imza doğrulama | %100 reddetme (TS13) | aktif (test-stratejisi §18) |
| 2 | G2b secret taraması | 0 sızıntı | aktif |
| 3 | G3 güvenlik testi | tamamı | aktif |
| 4 | G9 SBOM | tamam üretim | EKSİK ⚠️ |
| 5 | DV-S11 Q4 | ADR kaydı | AÇIK ⚠️ |

## §E — Web araştırma tablosu (2026-10-14 yeniden doğrulama)

| # | Amaç (bu dal) | URL | Tarih | Durum |
|---|---|---|---|---|
| 1 | NIST SP 800-193 — Protection · Detection · Recovery · RTU (anayasa §6 fail-safe karşılığı) | https://csrc.nist.gov/pubs/sp/800/193/final | 2026-10-14 | ✅ erişildi |
| 2 | MCUboot design — primary/secondary slot · revert · imza TLV (DV-S5 / A-B rollback) | https://docs.mcuboot.com/design.html | 2026-10-14 | ✅ erişildi |
| 3 | TUF spesifikasyonu — metadata bağlama · geri alma direnci (güncelleme imzası referansı) | https://theupdateframework.github.io/specification/latest/ | 2026-10-14 | ✅ erişildi |
| 4 | RFC 8032 — EdDSA (T5 seçenek adayı referans; SEÇİLMEDİ demek değildir) | https://www.rfc-editor.org/rfc/rfc8032 | 2026-10-14 | ✅ erişildi |
| 5 | OWASP Cheat Sheets — atıf kaynağı; firmware'e otomatik UYGULANMAZ (anayasa §7 ilke 6) | https://cheatsheetseries.owasp.org/ | 2026-10-14 | ✅ erişildi |
| 6 | PSA Certified — güvenlik sınıflandırma referansı | https://www.psacertified.org/security-certification | 2026-10-14 | ✅ erişildi |

> K-9/K-14 (URL tek-yazır) notu: 1/2/4/5/6 satırları boot-dogrulama §E ve
> firmware-dogrulama §2 havuzuyla **aynı URL'lerdir** — boot dosyaları da
> havuzu tekrar eder (mevcut emsal). Uyum yolu: her satır **kendi tarihi
> (2026-10-14) ve kendi dal amacı** ile taşınır, kaynak dosya atıfla
> belirtilir; kopya-sayım yapılmaz. Tarihsel kayıtlar: boot-dogrulama §E
> (2026-10-12/13 · HTTP-200 HEAD) · firmware-dogrulama §2 (2026-10-13).

## §F — Çapraz referanslar (boot dalı)

| Boot kaydı | Bu daldaki karşılık | İlişki |
|---|---|---|
| boot-dogrulama DV-B3 (imsız reddi · S7→S8) | DV-S2 | aynı olay, iki bakış: boot reddeder · security üretir |
| boot-dogrulama DV-B4 (çağrı hatası → S9, asla S10) | DV-S3 | fail-safe garantisi ortak |
| boot-ornekler §B.5 (rollback_check taslağı) | DV-S5 | okuma/uygulama boot · denetim security |
| boot-surucu §2 F4 (security üretir) | DV-S1 | F4 doğruluğu bu dalın kapısı |
| boot-surucu §6 E4 / ST16 | DV-S2 | reddetme hedefi |

## §G — Dürüstlük bloğu

- **"geçti" yasağı:** DV-S serisinde gerçek test çalıştırılmadı; kod yok
  → hiçbir satır "geçti/geçerli" **değildir**. Durum sütunu yalnız
  "Tasarım seviyesi / UNKNOWN / AÇIK / EKSİK" olabilir.
- **UYDURMA YOK:** 6 URL 2026-10-14'te fiilen erişilerek doğrulandı;
  ölçüm/sürüm/register değeri uydurulmadı. T3-algoritma, MCU, register
  adresi, SBOM içeriği **UNKNOWN** bırakıldı.
- **Bilinen konum çelişkileri (raporlandı, düzeltilmedi):** "Örnek 5.5"
  → gerçekte `boot-ornekler.md §B.5`; "Örnek 19 ADR referansı" →
  gerçekte `firmware-kod-ornekleri.md` satır 303 (`shared/src/Api/
  Registry/ServiceRegistry.php` satır 4-9 hedefi); `firmware-kararlar.md`
  §2 K-1…K-16 arasında **imza/sürüm zinciri kararı YOK**.
- **Q4 açık:** risk 2 (secure boot · skor 15) sahibi "security
  (dalga-3)" — erteleme/ADR kararı verilmeden DV-S11 kapanamaz.


---

## §H — DV-S Senaryo Ayrıntıları (DV-S1…DV-S8 · PLANNED — çalıştırılmadı)

### §H.1 DV-S1 — Bozuk imza ile boot denemesi

| Alan | Değer |
|---|---|
| Amaç | V1–V5 akışının bozuk imzada "geç" vermesini engellediğini göstermek |
| Ön koşul | Boot dalı çalışır durumda · G1 arayüzü sözleşmesi (security-surucu §3) |
| Uyarıcı | imza baytları rastgele bozulmuş geçerli uzunlukta image |
| Beklenen | boot **durdur** (M1) · olay kodu üretilir · yeniden deneme YOK |
| Ölçüt | durma kararı + olay kaydı (diagnostics) — sayısal eşik YOK |
| Durum | PLANNED — depoda kod yok (cpp/h = 0 · 2026-10-15) |

Adımlar: (1) image'a bozulmuş imza ekle · (2) boot başlat · (3) G1 kararını gözlemle ·
(4) M1 olayını doğrula · (5) otomatik tekrar olmadığını birden fazla başlatmayla doğrula ·
(6) kanıtı kanıt dosyasına yaz (dosya+satır) · (7) kapı K-S1 sonucunu kaydet.

### §H.2 DV-S2 — Eksik/uzun imza uzunluğu

- Uyarıcı: imza uzunluğu 0 · sınırın altında · sınırın üstünde (3 varyant).
- Beklenen: üçünde de **kal** — ayrım yapma, reddet; G2 referansı okunmadan önce
  uzunluk denetimi (erken reddetme) varsayımı **doğrulanacak varsayım**, kesin değil.
- Kanıt: her varyant için karar satırı + olay kodu; 3/3 kal yoksa kapı K-S1 FAIL.

### §H.3 DV-S3 — Anahtar referansı okunamazlığı (M2)

1. G2 kasa erişimini engelle (test düzeneği — L5 test alanı).
2. Boot başlat → beklenen: fail-secure kal (asla "geç").
3. Doğrula: hiçbir dalda "ulaşamadıysa geç" davranışı **yok** (fail-open taraması).
4. Kanıt: olay M2 + durma kararı; dashboard yok → el kaydı (şema §K.1).

### §H.4 DV-S4 — Anti-rollback: eski sürüm image'ı (T4)

- Uyarıcı: sayaç = N, image sürümü = N−1 (salt okunur G4 üzerinden).
- Beklenen: G4 uygunsuz → boot/update kal; sayaç **değişmez** (salt okuma).
- Kırılma testi: sayaç yazma denemesi security kodundan → **olmamalı** (K-S3 ile çiftli).
- Durum: PLANNED — sayaç deposu/alanı UNKNOWN (update dalına devir).

### §H.5 DV-S5 — Güvenli debug kilidi (T6 · boot sahibi, security politika)

1. Politika denetimi G6'yı çağır: bağlam eksik → varsayılan **reddet** (M4).
2. Beklenen: üretim derlemesinde test alanı erişimi başarısız (L5 kapalı).
3. Bu senaryonun fiziksel debug kilidi varyantı boot-dogrulama kaydına **devredilir**
   (burada uygulanmaz — SSOT).

### §H.6 DV-S6 — Audit olay bütünlüğü (T7 · diagnostics sahibi)

- Uyarıcı: olay üret, kaydı tekrar üretme girişimi (aynı bağlam).
- Beklenen: olaylar **tekrar-sız** değildir (zaman/sayaç alanı adaydır — alan tasarımı
  UNKNOWN, bu dosyada ad uydurulmaz).
- Çapraz kapı: K-S6 (olay kodları diagnostics ile birebir).

### §H.7 DV-S7 — Kasa arayüzü kötüye kullanımı (T5 · arayüz düzeyi)

1. Doğrulayıcının kasayı **çağıramadığını** (tek-yönlülük §H.3 güvenlik-kapsam) denetle.
2. Uyarıcı: ters yön çağrı denemesi (derleme/sözleşme ihlali — tasarım denetimi).
3. Beklenen: sözleşme ihlali derleme/runtime'da **yakalanır**; sessiz geçiş YOK.
4. Durum: PLANNED — derleyici/araç zinciri bu vault'ta yok (⚠️ araç GEREKLİ).

### §H.8 DV-S8 — Olay taşması (M5)

- Uyarıcı: audit tamponunu bilerek doldur (test ortamı — L5).
- Beklenen: sessiz kayıp YOK; sayacın arttığı + en eski kaydın işaretlendiği görülür.
- Ölçüt: kayıp sayacı okunabilir olmalı → ölçüm şeması §K.1 (doluluk ≠ 0 iddiası ölçülür).

### §H.9 Senaryo → tehdit kapsam matrisi

| Senaryo | T1 | T2 | T3 | T4 | T5 | T6 | T7 |
|---|---|---|---|---|---|---|---|
| DV-S1 | X | | | | | | |
| DV-S2 | X | | | | | | |
| DV-S3 | | | | | X | | |
| DV-S4 | | | X | X | | | |
| DV-S5 | | X | | | | X | |
| DV-S6 | | | | | | | X |
| DV-S7 | | | | | X | | |
| DV-S8 | | | | | | | X |

Kapsam boşluğu kaydı: **T3 yalnız DV-S4 üzerinden dolaylı** — manifest doğrulama
senaryosu update dalının DV-U serisindedir (update-dogrulama §A); burada tekrarlanmaz.

---

## §I — İzlenebilirlik Matrisi (anayasa ↔ senaryo ↔ kapı)

| Anayasa kuralı | Senaryo | Kapı | Kanıt tipi | Durum |
|---|---|---|---|---|
| İmza sırası zorunlu (V1–V5) | DV-S1 · DV-S2 | K-S1 | karar satırı | PLANNED |
| Primitive'siz doğrulayıcı hazır sayılmaz | DV-S7 (kısmi) | K-S2 | anayasa denetimi | FAIL-BLOCKED |
| security flash'a yazmaz | DV-S4 (çiftli) | K-S3 | kod incelemesi | PLANNED |
| Sayaç salt-okunur | DV-S4 | K-S4 | arayüz denetimi | PLANNED |
| Açık soru ↔ G bileşeni izi | §J kaydı | K-S5 | dosya sayımı | Kısmi |
| Olay kodu eşleşmesi | DV-S6 | K-S6 | çapraz tablo | PLANNED |
| Fail-secure (varsayılan kal) | DV-S3 · DV-S5 | K-S1/K-S3 | karar satırı | PLANNED |
| Sessiz kayıp yasak | DV-S8 | K-S6 | sayaç okuması | PLANNED |

### §I.1 Çift yönlü iz kontrolü

1. İleri: her anayasa kuralının ≥1 senaryosu var mı? — tabloda evet (7/7).
2. Geri: her senaryonun bir kapısı var mı? — evet; **kapı geçiş oranı 0** (hiçbiri çalışmamış).
3. Boşluk: K-S2 bloke edici; T5 çözülmeden ileri iz **bilerek** kırıktır (bilinçli kayıt).

---

## §J — Ortam ve Düzeneğin Matrisi (DX1–DX4 · PLANNED)

| Ortam | Ne içerir | Kullanıldığı senaryolar | Durum |
|---|---|---|---|
| DX1 birim (host) | G1 karar mantığı sahte (mock) G2/G4 ile | DV-S1/S2/S7 | PLANNED — test altyapısı yok |
| DX2 bütünleşik | gerçek G1+G2 arayüzü, sahte flash | DV-S1…DV-S3 | PLANNED |
| DX3 hedef (HIL) | gerçek flash + boot yolu | DV-S4 · DV-S5 fiziksel | G9 workflow TANIMLI DEĞİL |
| DX4 sağıklık (uzun) | tekrarlı başlatma · olay sayaçları | DV-S8 · kırılma taraması | PLANNED |

1. Ortam önceliği: DX1 → DX2 → DX3; DX4 en son (stabil arayüz gerektirir).
2. DX3 olmadan kapıların tamamı **kısmi** sayılır (ölçüm iddiası kurulamaz).
3. Her ortam için kayıt yeri §K.1 şeması; CSV/format uydurulmaz (şema boş kalıp).

---

## §K — Ölçüm Yöntemi ve Sonuç Kayıt Şeması

### §K.1 Sonuç satırı alanları (boş kalıp — doldurulacak)

| Alan | Açıklama | Örnek değer (uydurulmaz) |
|---|---|---|
| ts | çalıştırma zaman damgası | (doluşacak) |
| scen | DV-S kodu | (doluşacak) |
| env | DX kodu | (doluşacak) |
| verdict | PASS / FAIL / BLOCKED | (doluşacak) |
| evidence | dosya + satır referansı | (doluşacak) |
| actor | çalıştıran (agent/kişi) | (doluşacak) |

### §K.2 Kanıt kuralları

1. Elde edilen her sonuç, gerçek dosya yolu + satır ile kaydedilir (zero-hallucination).
2. "Geçti" iddiası yalnız **çalıştırılmış** senaryo için kullanılır; tasarım satırı
   kapı sonucu üretemz.
3. Sonuç yoksa satır "ÇALIŞTIRILMADI" olarak kalır (boş bırakma / dolgu yasak değil —
   dürüst boşluk).

### §K.3 Ölçüm araç zinciri (beklenen — tanımsız)

- Beklenen araçlar: hedef debug arayüzü + log çıkışı + kapı betiği.
- Bu vault'ta araç adı/tanımı **yok** → zincir UNKNOWN, araç adı uydurulmaz.
- İhtiyaç kaydı: firmware-test-stratejisi §21 (geliştirici makinesi ortamı) üzerinden
  tanımlanacak; sahiplik DevOps + QA (§I2 kapı devri).

---

## §L — Enjeksiyon ve Kırılma Senaryoları (DX3 öncelikli)

| # | Enjeksiyon | Hedef davranış | Kabul |
|---|---|---|---|
| E1 | imza baytına tek-bayt değiştirme | kal (M1) | tek bayt ≠ geç |
| E2 | sayaç'ı düşürme denemesi | okuma yolu etkilenmez · yazım reddi | sayaç sabit |
| E3 | kasa yolunu yanıltıcı erişim | reddet (M4/M2) | "geç" üretilemez |
| E4 | audit tamponu doldurma | sayaç + işaretleme | sessiz kayıp yok |
| E5 | boot sırasında debug denemesi | reddet (politika) | boot zinciri bozulmaz |
| E6 | olay yolunu kasma (kuyruk) | sayaçlama | olay kaybı görünür |

1. Enjeksiyonlar DX3'te anlamlıdır; DX1'de yalnız arayüz düzeyi.
2. Her enjeksiyon bir DV-S ile çiftlenir (E1↔DV-S1, E4↔DV-S8, E5↔DV-S5…).
3. Yeni enjeksiyon fikri → anayasa üzerinden onay; burada yalnız **talep** kaydedilir.

---

## §M — Kabul Eşiği ve Karar Tablosu (kapı yetkisi)

| Kapı | Minimum kanıt | Karar yetkisi | Şu anki durum |
|---|---|---|---|
| K-S1 | DX1+DX2 sonuçları | QA (gate sahibi) | Beklemede |
| K-S2 | T5 kararı (ADR) | Architect + Security | **Bloke** |
| K-S3 | kod incelemesi (2 gözden geçirme) | Code review | Beklemede |
| K-S4 | DX2 + DX3 kısmı | QA + boot sahibi | Beklemede |
| K-S5 | açık soru sayımı (her üretimde) | Vault Steward | Kısmi |
| K-S6 | DV-S6 + çapraz tablo | Diagnostics sahibi | Beklemede |

1. Sayısal eşik **uydurulmaz**: hiçbir kapı için yüzde/tekrar sayısı sabitlenmemiştir
   (anayasa dürüstlük kuralı — ölçülmemiş hedef koymak yasak).
2. Karar yetkisi devredilemez: agent "geçti" **bilemez**; yalnız kanıt üretir.
3. K-S2 bloke iken genel kapı sonucu = BLOCKED (kismen geçerli sayım yok).

---

## §N — Öncelik ve Sıra Planı (ilk çalıştırma sırası)

1. DV-S1 → DV-S2 (en düşük bağımlılık — yalnız G1 kararı).
2. DV-S3 (kasa engeli) → DV-S7 (ters yön denetimi) — birlikte DX1.
3. DV-S6 + DV-S8 (olay yolu) — DX1/DX2.
4. DV-S4 (sayaç) — update arayüzü hazır olunca DX2.
5. DV-S5 fiziksel varyantı — DX3 + boot sahibi koordinasyonu.
6. Sağıklık DX4 — yalnız kapı öncesi.

Sıra gerekçesi: risk × bağımlılık; K-S2 bloke iken DV-S1'in sonuçları **bilgi**,
kapı **değildir** (kapı kararı K-S2 kapanınca verilir).

---

## §O — Red Team Notları (bu ek için)

1. **İtiraz:** "DV-S1 geçti deriz, kapı kapanır." **Karar:** geçersiz — K-S2 T5'e
   bağlı; erken kapanış anayasa ihlali (ölçülmemiş kapı).
2. **İtiraz:** "Mock ile DX3 yerine geçer." **Karar:** kısmi — DX3 yalnız fiziksel
   flash/debug yolu kanıtlar; DX3 yoksa K-S4 açık kalır.
3. **İtiraz:** "Olay kaybı affedilir." **Karar:** hayır — M5 sessiz kayıp yasak;
   affedilirse repudiation (T7) açılır.
4. **İtiraz:** "Sayaç yazımını security de yazabilir." **Karar:** hayır — K-S3/K-S4
   ihlali; yazım update'in (kapsam §H.3).
5. **İtiraz:** "Araç yoksa eşik koyalım." **Karar:** hayır — ölçülmüş eşik uydurmadır;
   araç tanımı önce gelir (§K.3).

---

## §P — Sözlük ve Kısaltmalar

| Terim | Bu dosyadaki anlamı |
|---|---|
| DV-S | Security doğrulama senaryosu serisi |
| DX1–DX4 | Ortam kademeleri (bütünleşik → HIL → sağıklık) |
| K-S* | Security kapsam kapıları (kapsam §I) |
| G1–G7 | Security bileşen envanteri (kapsam §H) |
| M1–M5 | Fail-secure hata tepkileri (kapsam §M) |
| PLANNED | tanımlı ama çalıştırılmamış |
| BLOCKED | ön koşul kararı verilmemiş (örn. T5) |
| fail-secure | belirsizlikte kal/karar (fail-open karşıtı) |

---

## §Q — Çapraz Bağlantılar (tek-yönlü — SSOT)

1. Kapı tanımları: security-kapsam.md §I (bu dosya kopyalamaz, çağırır).
2. Arayüz sözleşmeleri: security-surucu.md §3–§8.
3. Boot tarafı işleri: boot-dogrulama.md (fiziksel kilit · durma yolu).
4. Sayaç yazımı: update-dogrulama.md (DV-U serisi).
5. Kapı çalıştırma altyapısı: firmware-test-stratejisi.md §3/§18 (G-kapıları).
6. Olay yayını: diagnostics dalı (format/saklama sahibi).

---

## §R — Kapanış Kontrolü (bu ek)

| Kontrol | Sonuç |
|---|---|
| Mevcut §A–§G silindi mi? | HAYIR — append-only |
| Frontmatter korundu mu? | EVET (10 alan + group: kanit) |
| Dolgu/TBD satırı | 0 |
| Çalıştırılmış iddia | 0 (tümü PLANNED) |
| Uydurma dosya/URL/marka | 0 |
| Son satır Authority | birebir korunur |


---

## §S — Ek Senaryo Kartları (DV-S9…DV-S12 · tamamlama)

### §S.1 DV-S9 — Aynı boot oturumunda art arda bozma denemesi

- Uyarıcı: art arda 3 bozuk deneme (aynı oturum).
- Beklenen: her denemede ayrı M1 olayı · boot her seferinde **kal** · sayaç/artış
  davranışı yalnız boot sayaçlarına ait (boot dalı sahibi — burada yalnız gözlem).
- Kanıt: 3 ayrı olay kaydı + 3 durma kararı (aynı satır sayısı kadar kayıt).
- Durum: PLANNED (DX2).

### §S.2 DV-S10 — Doğrulama sırasında güç kesilmesi (sınır varyantı)

1. G1 kararı verilmeden reset at (DX3 varyantı).
2. Beklenen: yarı-göçmüş imaj **kullanıma girmez** (boot tarafı A/B kuralı —
   boot-dogrulama/ota-update §12 güç-fail güvenliği [Satır: 237] kaynağında).
3. Bu senaryonun uygulanması DX3 gerektirir → G9 kapı bağı.
4. Not: reset sonrası sayaç davranışı update/boot sahibi — yalnızca olay gözlemlenir.

### §S.3 DV-S11 — Sahte olay basma (repudiation denetimi)

- Uyarıcı: olay yoluna dışarıdan sahte M-kodu enjeksiyonu (test arayüzü L5).
- Beklenen: üretim derlemesinde yol **kapalı** (L5); test derlemesinde işaretli.
- Kanıt: üretim derlemesinde reddetme + test derlemesinde işaretli üretim kaydı.

### §S.4 DV-S12 — Uzun bekleme (idle) sonrası politika bayrağı

- Uyarıcı: uzun süre bekleme → uyanış → politika denetimi (G6).
- Beklenen: bayrak bayatlamaz (yeniden okunur); "varsayılan reddet" korunur.
- Ölçüt: bayrak okuma zaman damgası + karar (alanlar §K.1 şemasında).

### §S.5 Kart ↔ ortam güncel matrisi

| Senaryo | DX1 | DX2 | DX3 | DX4 |
|---|---|---|---|---|
| DV-S9 | evet | evet | isteğe bağlı | evet |
| DV-S10 | hayır | hayır | zorunlu | hayır |
| DV-S11 | evet | evet | hayır | hayır |
| DV-S12 | evet | evet | isteğe bağlı | evet |

---

## §T — Test Verisi ve Fixture Tasarımı (boş şema — uydurma veri yok)

### §T.1 Fixture sınıfları

| Kod | Fixture | Üretim yolu | Güvenlik |
|---|---|---|---|
| FX1 | bozuk imza image | geçerli image + bayt değişimi (test) | yalnız test derlemesi |
| FX2 | eski sürüm image | bilinen sürüm sabitleme | sayaç yalnız DX3'te |
| FX3 | kasa-engel | test asistanı (G2 engelleyici) | mock |
| FX4 | dolu olay kuyruğu | üretim betiği (DX1–DX2) | veri içermez (payload yok) |

1. Fixture'lar **bu vault'ta üretilmez**; yalnız şartname buradadır (uygulama kodu yok).
2. FX1/FX2 imajları gerçek imzalı olabilir — bu, geçerli imzayı bozar; güvenlik riski
   yoktur (test cihazı izole · DX3 kuralı).
3. Fixture envanteri her çalıştırmadan önce sayılır (eksik fixture = senaryo BLOCKED).

### §T.2 Veri bütünlüğü

- Her fixture için beklenen özet değeri (karma/uzunluk) kayıt satırına yazılır —
  **değer uydurulmaz**, çalıştırmada okunup yazılacak (şema §K.1).
- Değiştirilmiş fixture beklenenle uyuşmazsa senaryo **INVALID** sayılır (FAIL değil).

---

## §U — Görev Dağılımı ve Bağımlılık Sırası (PLANNED)

| Adım | İş | Bağımlılık | Sahip |
|---|---|---|---|
| U1 | G1 arayüz sözleşmesi dondurma | T5 kararı | Architect + Security |
| U2 | DX1 test iskeleti | U1 | QA (test-writer) |
| U3 | DV-S1/S2/S3/S7 çalıştırma | U2 | QA |
| U4 | Olay yolu (DV-S6/S8) | diagnostics olay kodları | Diagnostics + QA |
| U5 | DV-S4 (sayaç) | update arayüzü | Update + QA |
| U6 | DX3/G9 kurulumu | DevOps onayı (workflow yok) | DevOps |
| U7 | Kapı değerlendirmesi (K-S1…K-S6) | U3–U6 | QA + Vault Steward |

### §U.1 Zaman sırası kuralı

1. U1 olmadan U2–U3 **anlamsızdır** (arayüz dondurulmadan test neyi doğrular?).
2. U6 en pahalı adım — erken başlar, kapı öncesi biter; erken tamamlanmazsa kapı
   kısmi kalır (DX3süz K-S4).
3. Her adım çıktısı bu dosyaya append edilir (sonuç satırları §K.1).

---

## §V — Risk → Senaryo Bağı (devralınan risklerin izi)

| Risk | Kök | İlgili senaryo | Kalan açıksa |
|---|---|---|---|
| R-secure-boot (Q4) | sahiplik belirsiz | DV-S1 bağlamı | kapı K-S2 kapalı kalır |
| R-downgrade | sayaç yolu | DV-S4 | DX3 yoksa kısmi |
| R-key (T5) | primitive yok | DV-S3 · DV-S7 | FAIL-BLOCKED |
| R-debug | fiziksel kilidi | DV-S5 | boot dalına devri |
| R-audit | olay kaybı | DV-S6 · DV-S8 · DV-S11 | diagnostics bağımlı |
| R-partial-write | güç kesintisi | DV-S10 | DX3 bağımlı |

---

## §W — Kapanış Cümlesi (bu ekin sonu)

Bu ek (§H–§V) security-dogrulama.md dosyasını 8 + 4 senaryo kartı, 4 ortam kademesi,
6 enjeksiyon, kapı yetki tablosu, görev/veri/risk şemalarıyla derinleştirmiştir;
**hiçbir bölüm çalıştırılmamıştır** (PLANNED) ve mevcut §A–§G ile frontmatter
append-only korunmuştur. Sonraki üretim yalnız §K.1 şemasına **gerçek sonuç satırı**
eklenerek yapılır (uydurma sonuç yasak).

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
