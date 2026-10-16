---
node_id: K005-firmware/update/update-dogrulama
node_type: module
status: draft
parent_id: K005-firmware
children: []
depends_on:
  - K005-firmware/update/update-anayasa
  - K005-firmware/update/update-surucu
  - K005-firmware/update/update-kapsam
  - K005-firmware/boot/boot-dogrulama
  - K005-firmware/firmware-dogrulama
  - K005-firmware/firmware-riskler
evidence:
  - .ai/architecture/K005-firmware/boot/boot-dogrulama.md §A · §E · §F · §U–§V (okundu 2026-10-14)
  - .ai/architecture/K005-firmware/firmware-dogrulama.md §2–§3 (okundu 2026-10-14)
  - .ai/architecture/K005-firmware/firmware-riskler.md §2 · §7 (okundu 2026-10-14)
  - .ai/architecture/K005-firmware/update/update-anayasa.md §2–§7 · §10 (okundu 2026-10-14)
  - .ai/architecture/K005-firmware/update/update-surucu.md §2–§12 (okundu 2026-10-14)
  - .ai/architecture/K005-firmware/update/update-kapsam.md §A–§G (okundu 2026-10-14)
  - HTTP doğrulama — §E'deki 8 URL, erişim 2026-10-14 (okundu 2026-10-14)
validation:
  - 2026-10-14 — FM 10 alan + group + son satır Authority denetimi
open_questions:
  - "update ↔ güvenlik (imza) kime ait? (K005 Q4) — bu dosyada ÇÖZÜLMEZ; DV-U4 yalnız imza REDDİ senaryosunu sınar, sahipliği sorular"
  - "Ölçüm için donanım/HIL yok — tüm Durum sütunu Tasarım seviyesi veya ölçüm PLANNED (asla 'geçti' değil)"
group: kanit
---

# Update Alt-Modülü — Doğrulama (K005-firmware/update/update-dogrulama.md)

> **ELI15:** Bu dosya update'in neyle sınanacağını listeler: düzgün güncelleme + geri
> alma, yarım yazma, güç kesintisi sırasında güncelleme. Henüz donanım/kod olmadığı için
> hiçbir senaryo "geçmedi" — hepsi **Tasarım seviyesi** ya da **ölçüm PLANNED**'dir.
> Bölüm §E'deki 8 kaynak URL'i 2026-10-14'te HTTP ile doğrulanmıştır; bu URL'ler
> K005-firmware update klasöründe ÖNCEDEN HİÇ GEÇMEZ (K-14 tek-üretim: burada üretilir).

## §A Doğrulama Senaryoları (DV-U)

| # | Senaryo | Ön koşul | Beklenen | Ölçüm yöntemi | Durum |
|---|---|---|---|---|---|
| DV-U1 | Düzgün güncelleme (güncelleme/geri alma çiftinin güncelleme yarısı) | Geçerli manifest + imzalı paket + A/B yuvası boş | US1→US8 tam akış: indir → doğrula → yaz → pending → self-test → confirm; `confirmed=1` | Bayrak okuma (UF1–UF3) + akış logu | Tasarım seviyesi (kod yok) |
| DV-U2 | Geri alma (güncelleme/geri alma çiftinin geri-alma yarısı) | Yeni image self-test'te başarısız veya onay gelmedi (UR3/UR4) | US9-ROLLBACK: boot A8 retry ≤ 2 → yedek yuva → eski doğrulanmış firmware çalışır | Sahte bozuk image + bayrak/boot log okuma | Tasarım seviyesi (kod yok) |
| DV-U3 | Yarım yazma | Image yazması yarıda kesilebilir (program hatası enjeksiyonu) | `pending` KONULMAZ; sonraki boot'ta eski onaylı firmware kalır (update-surucu §6 KAPI-3) | Kasıtlı yazma hatası + tekrar-boot kontrolü | Tasarım seviyesi (kod yok) |
| DV-U4 | İmza reddi | Paket imzasız/bozuk (u4 · F13 bağlantılı) | US2-VERIFY → US10-ABORT; slot'a HİÇ yazılmaz (UR2 · S14 RED) | Test paketi + imza sonucu logu | Tasarım seviyesi (kod yok) — **sahiplik Q4: algoritma security, açık** |
| DV-U5 | Güç kesintisi sırasında güncelleme | `US3-WRITE`/`US4-PENDING` anında gücü kesme imkânı | Güç gelince boot kurtarır: yarım image reddedilir, eski firmware veya yedek yuva devreye girer (U6 "en kritik satır") | Güç kesme devresi + tekrar-boot logu | Tasarım seviyesi (donanım yok) |
| DV-U6 | Anti-rollback ihlali (UR6) | Paket sürümü `secure_version` (UF7) eşik altındaysa | `US10-ABORT` — kurulum reddedilir (boot-ornekler §B.5 `FW_VERSION_MIN` aynası) | Eşik altı test paketi | Tasarım seviyesi (kod yok) |
| DV-U7 | Onay zaman aşımı (UR4) | Self-test geçti ama confirm bayrağı hiç yazılmadı | UT4 zaman aşımı → US9-ROLLBACK (risk 3'ün tetik senaryosu) | Sahte health stub + zaman aşımı enjeksiyonu | Tasarım seviyesi (kod yok) |
| DV-U8 | DFU 4 faz akışı | USB üzerinden `DfuIdle → DfuDnload → DfuManifest → DFURuntime` | Manifest'ten sonra yeniden başlatma US5-FIRST_BOOT'e bağlanır; sonrası UT3/UT4 kapılarından geçer | USB analizörü + DFU host aracı (USB DFU 1.1 — boot-dogrulama §F satır 7) | Ölçüm PLANNED (donanım yok) |
| DV-U9 | Rollback risk kapanışı (risk 3 — brick) | firmware-riskler §7: "test senaryosu (HIL)" kapısı | A/B + sayaç ile brick senaryosu üretilmez; üretilirse geri alma kurtarır | HIL tezgâhı + güç kesme + bayrak okuma | Ölçüm PLANNED (HIL yok) |

> **Durum kuralı:** Bu tabloda hiçbir satır "geçti" DEĞİLDİR. Kod kanıtı 2026-10-13
> ölçümünde 0'dır; ölçüm gerektiren satırlar (DV-U8, DV-U9) **ölçüm PLANNED**, gerisi
> **Tasarım seviyesi**.

## §B Senaryo ↔ İzlenebilirlik (DV-U → kapı/kriter)

| DV-U | Bağlı kapı/kaynak | Bağlı kriter |
|---|---|---|
| DV-U1 | update-surucu §2 (US1→US8) · update-anayasa §2 zinciri | update-kapsam §A.4 K1 |
| DV-U2 | update-surucu §8 KAPI · boot-anayasa §8 (retry ≤ 2) | update-kapsam §A.4 K5 |
| DV-U3 | update-surucu §6 KAPI-3 · update-kapsam §A.2 U6 | update-kapsam §A.4 K2 |
| DV-U4 | update-surucu §7 KAPI · update-kapsam §A.3 (Q4 satırı) | update-kapsam §A.4 K3 (S14) |
| DV-U5 | update-surucu §5 UR5 · §6 KAPI · boot-dogrulama §U · Image yazma bütçesi | update-kapsam §A.4 K2 (U6) |
| DV-U6 | update-surucu §5 UR6 · §A.6 anti-rollback aynası | update-kapsam §A.4 K4 (Ov2) |
| DV-U7 | update-surucu §4 UT4 · §5 UR4 | firmware-riskler §2 risk 3 |
| DV-U8 | update-surucu §10 KAPI (DFU 4 faz) · boot-dogrulama §U · DFU 4-faz akışı | update-kapsam §A.2 U3/U4 |
| DV-U9 | firmware-riskler §7 satır 3 (HIL) · update-kapsam §E | firmware-riskler §21 (kapanış: `update/*`) |

## §C Sahiplik ve Öncelik (boot-dogrulama §V köprüsü)

| Kapı/Senaryo | Owner set (kaynak: boot-dogrulama §V) | Not |
|---|---|---|
| DV-U1 · DV-U3 · DV-U5 (image yazma kapıları) | embedded-engineer · security-engineer · devops-engineer | Kapı-bazında tekil owner dağıtımı kaynakta yok — UNKNOWN (update-surucu §13) |
| DV-U4 (imza) | security-engineer (algoritma — boot-kapsam §I "mutabık") + update (çağrı — ⚠️ Q4) | **Q4 açık: çağrı≠algoritma ayrımı çözülmedi** |
| DV-U2 · DV-U7 (rollback) | embedded-engineer · devops-engineer · security-engineer (set) | firmware-riskler §7 sahibi: `update` |
| DV-U8 (DFU) | embedded-engineer · devops-engineer · security-engineer (set) | DFU erişim politikası `UNKNOWN` |
| DV-U9 (HIL risk kapısı) | qa-engineer + embedded-engineer | firmware-riskler §7: test senaryosu (HIL) |

## §D Kanıt ve Ölçüm Durumu

| Veri | Amaç | Üretim | Durum |
|---|---|---|---|
| Bayrak okumaları (UF1–UF7) | DV-U1/U2/U6/U7 beklentisi | Yazılım bayrak dump'u | Tasarım seviyesi — bayrak yerleşimi vault'ta ⚠️ (boot-kapsam §C.1) |
| Akış logu (US0–US10 geçişleri) | DV-U1/U2 akış tutarlılığı | Update log kanalı | `UNKNOWN` — telemetri kanalı tanımsız (update-surucu §12-2) |
| Güç kesme tekrar-boot | DV-U5 | Güç kesme devresi | Ölçüm PLANNED (donanım yok) |
| HIL tezgâhı | DV-U9 | firmware-test-stratejisi §5 gate | Ölçüm PLANNED |
| Hata kodu eşlemesi | Tüm DV-U negatif yollar | Kod şeması | ⚠️ TANIMSIZ (update-surucu §12-1) — kod uydurulmaz |

## §E Doğrulanmış Web Kaynakları (bu dosyada üretilir — K-14 tek-üretim)

> 8 URL'in tamamı **2026-10-14** tarihinde HTTP ile erişilip doğrulanmıştır.
> Kesişim denetimi: bu 8 URL'in hiçbiri `K005-firmware/` altında (update dahil) önceden
> geçmez → **0 çakışma**, K-14 sağlanır. `update-kapsam.md` §F bu tabloya pointer taşır.

| # | İddia (bu modülde kullandığı yer) | URL | Tarih | Durum |
|---|---|---|---|---|
| E1 | Firmware güncelleme yaşam döngüsü güvenli indirme/doğrulama/geri-alma ister (DV-U1/U2 · update-anayasa §2) | https://www.rfc-editor.org/rfc/rfc9019 | 2026-10-14 | ✅ HTTP-200 doğrulandı |
| E2 | OTA indirme + doğrulama + yazma zinciri ve hata senaryoları (DV-U1/U3 · update-surucu §2) | https://docs.espressif.com/projects/esp-idf/en/latest/esp32/api-reference/system/ota.html | 2026-10-14 | ✅ HTTP-200 doğrulandı |
| E3 | OTA ile ağ üzerinden image indirme-uygulama akışı (DV-U1 · update-surucu §4 UT1) | https://docs.espressif.com/projects/esp-idf/en/latest/esp32/api-reference/system/esp_https_ota.html | 2026-10-14 | ✅ HTTP-200 doğrulandı |
| E4 | Firmware yükleme/imza doğrulama güven gerekçeleri (DV-U4 · Q4 sınırı — sahiplik değil, teknik zorunluluk) | https://www.rfc-editor.org/rfc/rfc9124 | 2026-10-14 | ✅ HTTP-200 doğrulandı |
| E5 | Güvenli firmware güncelleme tasarımı: imza + rollback gerekçesi (DV-U2/U6 · update-surucu §8) | https://www.rfc-editor.org/rfc/rfc8240 | 2026-10-14 | ✅ HTTP-200 doğrulandı |
| E6 | Donanım güncelleme güvenliği: anti-rollback/bekleme modeli (DV-U6 · update-surucu §3 UF7) | https://www.rfc-editor.org/rfc/rfc6024 | 2026-10-14 | ✅ HTTP-200 doğrulandı |
| E7 | Cihaz sınıfları — indirme/depolama boyutu kısıtları (DV-U3/U5 · update-kapsam §A.2 U2) | https://www.rfc-editor.org/rfc/rfc7228 | 2026-10-14 | ✅ HTTP-200 doğrulandı |
| E8 | Güvenli boot + güncelleme güvenlik modeli (DV-U4/U5 · boot↔update sınırı) | https://docs.espressif.com/projects/esp-idf/en/latest/esp32/security/security.html | 2026-10-14 | ✅ HTTP-200 doğrulandı |

**URL üretim kuralı (K-14):** Bu sekiz URL bu modülün tek üretim yeridir; `update-anayasa`
§10 ve `update-kapsam` §F yalnızca bu tabloya pointer verir, URL kopyalamaz. Daha önce
doğrulanmış ve **bu sekizle kesişmeyen** URL kayıtları (boot-dogrulama §F: USB DFU 1.1
PDF ✅ erişim 2026-10-12, mcuboot design ✅, NIST SP 800-193 ✅ · ota-update.md §15:
docs.mcuboot.com ✅ erişim 2026-10-13) kendi dosyalarında kalır — burada tekrar üretilmez.
Bilinen 404'ler (espressif `storage/ota.html`, zephyr `services/dfu/mcuboot.html` vb.)
ASLA cite edilmez.

## §F Açık Sorular

| # | Soru | Durum |
|---|---|---|
| Q4 | update ↔ güvenlik (imza) kime ait? | **AÇIK — bu dosyada çözülmez** (DV-U4 sahipliğini de etkiler: çağrı update, algoritma security — boot-kapsam §I kaydı) |
| Q1 | İlk-boot tutarlılık kapısı | DV-U1/U2 içinde geçilir; kapının kendisi firmware-bagimliliklar §20.3 üzerinden açık |
| Q2/Q3 | Update-onarım · boot-lock sınırı | DV-U5 yalnız kurtarma beklentisini yazar; arayüz/ölçüm `UNKNOWN` (boot-dogrulama §U · Boot-lock yazma satırı) |
| Ölçüm | DV-U8 · DV-U9 için donanım/HIL | Ölçüm PLANNED — "geçti" iddiası YOK |


---

## §H DV-U Senaryo Ayrıntıları (DV-U10…DV-U21 · PLANNED — çalıştırılmadı)

### §H.1 DV-U10 — A/B slot geçişi sonrası ilk boot

| Alan | Değer |
|---|---|
| Amaç | Slot etiketinin "kurulu"ya çevrilmesinin ilk başarılı boot'ta olduğunu göstermek |
| Uyarıcı | yeni image kurulmuş · slot etiketi beklemede · ilk boot |
| Beklenen | boot başarılı → etiket **boot onayıyla** değişir (kendi kendine değil) |
| Kanıt | etiket okuma satırı + boot sonucu (dosya + satır) |
| Durum | PLANNED — firmware kodu yok (cpp/h = 0 · 2026-10-15) |

Referans çapa: ota-update.md §6.1 A/B güncelleme adımları [Satır: 97] · §9.1 sağlık
kontrolü zamanlaması [Satır: 192] — bu vault kaynağından okunarak üretildi (2026-10-15).

Adımlar: (1) bekleme etiketini kur · (2) ilk boot'u başlat · (3) sağlık kontrolünü
gözlemle · (4) etiket değişiminin yalnız onay sonrası olduğunu doğrula · (5) kanıtı deftere yaz.

### §H.2 DV-U11 — Sağlık kontrolü başarısız → otomatik geri alma

1. Yeni image'ı boz (test fixture — mutasyon, imzasız değil).
2. İlk boot → sağlık kontrolü başarısız (ota-update.md §9 [Satır: 180]).
3. Beklenen: etiket **geri alma** yazılır · eski slot devreye girer · sayaç sabit
   (anti-rollback sayacı geri düşürülmez — §7.1 [Satır: 134]).
4. Kanıt: sağlık sonucu + etiket geçişi + slot değişimi (3 satır).

### §H.3 DV-U12 — Güç kesintisi indirme sırasında (DFU faz 1)

- Uyarıcı: indirme sırasında enerji kes (update-surucu §10 DFU 4 faz).
- Beklenen: indirme **kaldığı yerden** veya sıfırdan; etiketler değişmez · boot
  bozulmaz (güç-kesinti güvenliği §12 [Satır: 237]).
- Kanıt: kesinti sonrası etiket + boot sonucu; "kısmi indirme güvenliği" ölçülür.
- Durum: DX3/HIL gerekir → G9 blokajı (workflow tanımsız).

### §H.4 DV-U13 — Güç kesintisi yazma sırasında (DFU faz 3)

1. Slot yazımını yarıda kes (DX3).
2. Beklenen: hedef slot **geçersiz işaretli** · eski slot kullanılabilir · boot zinciri
   korunur (ota-update.md §19.1 boot seçim algoritması [Satır: 358]).
3. Kanıt: slot işaretleri + boot kararı; "eski slot her zaman kurtarıcı" doğrulanır.

### §H.5 DV-U14 — Rollback tetikleyici (geri alma) senaryosu

- Uyarıcı: kullanıcı/sistem geri alma isteği (update-surucu §5 UR1–UR6).
- Beklenen: geri alma yalnız **geçerli** eski slotla mümkün · anti-rollback sayacı
  aşılamaz (sayacın altına düşen sürüm REDDEDİLİR — §7.1 [Satır: 134]).
- Kanıt: istek + karar + sayaç okuması (3 satır).

### §H.6 DV-U15 — İmza doğrulama kapısı (Q4 bağımlı)

- Uyarıcı: imzasız/bozuk image (security köprüsü).
- Beklenen: kurulum **başlamaz** · imza kararı security'nin V1–V5 akışından gelir
  (Q4 sahipliği açık — bu senaryo K-S2 blokajına bağlıdır).
- Durum: BLOKE — imza primitive'si seçilmemiş (T5) + Q4 açık.

### §H.7 DV-U16 — Kısmi indirme devam (resume)

- Uyarıcı: yarım indirme + bağlantı kesilmesi → yeniden bağlanma.
- Beklenen: devam yalnız **aynı manifest** için (manifest değiştiyse sıfırla).
- Kanıt: manifest kimlik karşılaştırması + bayt sayacı (değer uydurulmaz — okunur).
- Not: delta update dikkati ota-update.md §20.1 [Satır: 384] — bu vault kaynağı.

### §H.8 DV-U17 — Kademeli dağıtım (canary) kapısı

1. Sürüm cohort'ları (ör. erken/sonraki gruplar) — ota-update.md §10.2 [Satır: 213].
2. Beklenen: cohort dışı cihaz **almaz** · erken cohort hata sinyali → dağıtım durur.
3. Kanıt: cohort kararı + duraklama kaydı (L6 katmanı — testing-kapsam §H.1).
4. Durum: PLANNED — filo telemetri sahibi update; testing yalnız kapı.

### §H.9 DV-U18 — Boot seçim algoritması çakışması

- Uyarıcı: iki slot da "kurulu" işaretli (bozuk durum).
- Beklenen: algoritma **deterministik** tercih eder + olay üretir (ota-update.md §19.1
  [Satır: 358] üzerinden); belirsiz durumda eski slot (güvenli tercih) varsayımı
  **doğrulanacak varsayım** (kesin değil).
- Kanıt: slot işaretleri + seçim kararı + olay satırı.

### §H.10 DV-U19 — NVS/ayar koruması (kullanıcı verisi)

- Uyarıcı: güncelleme + rollback sonrası kullanıcı ayarları.
- Beklenen: NVS **korunur** (ota-update.md §18.2 [Satır: 339] — NVS koruma prensibi).
- Kanıt: ayar öncesi/sonrası karşılaştırma (dosya+satır; değer uydurma yok).

### §H.11 DV-U20 — Manuel rollback kilidi (boot-lock)

- Uyarıcı: boot-lock etkinken geri alma isteği (update-surucu §9).
- Beklenen: geri alma **reddedilir** (kilit sahibi boot; bu senaryo yalnız red bildirir).
- Kanıt: istek + RED kararı + kilit durumu (boot-dogrulama §U üzerinden).

### §H.12 DV-U21 — İndirme bütçesi aşımı (enerji/ağ)

- Uyarıcı: güncelleme penceresi dar · indirme uzar (ota-update.md §26 [Satır: 492]).
- Beklenen: güncelleme **iptal edilebilir** · slotlar etkilenmez · kuyruğa alınır.
- Kanıt: iptal kararı + slot durumu (enerji bütçesi ölçümü PLANNED).

### §H.13 Senaryo → DFU fazı → kapı matrisi

| Senaryo | Faz 1 | Faz 2 | Faz 3 | Faz 4 | Kapı |
|---|---|---|---|---|---|
| DV-U10 | | | | X | boot onayı |
| DV-U11 | | | | X | geri alma |
| DV-U12 | X | | | | güç-güvenli |
| DV-U13 | | | X | | yazma-güvenli |
| DV-U14 | | X | | | anti-rollback |
| DV-U15 | X | | | | imza (BLOKE) |
| DV-U16 | X | | | | manifest |
| DV-U17 | | | | | kademeli |
| DV-U18 | | | | X | boot seçim |
| DV-U19 | | | | | NVS |
| DV-U20 | | | | | boot-lock |
| DV-U21 | X | | | | bütçe |

---

## §I Ortam ve Düzeneği (UX1–UX3 · PLANNED)

| Ortam | İçerik | Senaryolar | Durum |
|---|---|---|---|
| UX1 sahte | manifest/slot/sayaç mock | DV-U14/U16/U17 | PLANNED — arayüz yok |
| UX2 bütünleşik | gerçek manifest doğrulama + sahte flash | DV-U10/U11/U14/U16 | PLANNED — kod yok |
| UX3 HIL | gerçek flash + güç kesme anahtarı | DV-U12/U13/U19 | BLOKE (G9 yok) |

1. Öncelik UX1 → UX2 → UX3; UX3 en pahalı — erken planlanır (G9 bağımlılığı).
2. UX3süz kapılar kısmi sayılır (güç-kesintisi iddiası ölçülemez).
3. Her ortam kayıt satırı §J.1 şemasında.

---

## §J Ölçüm ve Sonuç Şeması (boş kalıp)

| Alan | Açıklama |
|---|---|
| ts | zaman damgası (üreten doldurur) |
| scen | DV-U kodu |
| env | UX kodu |
| verdict | PASS / FAIL / BLOKE / INVALID / BELIRSIZ |
| evidence | dosya + satır |
| actor | çalıştırıcı |

### §J.1 Ölçüm kuralları (update-özel)

1. "Güvenli" iddiası (güç-kesinti vs.) yalnız UX3'te ölçülür — UX2'de **kismen**.
2. Sayaç/etiket değerleri okunarak yazılır (uydurma sayı yasak).
3. Eşik/tekrar sayısı sabitlenmez (ölçülmemiş hedef yasak — anayasa dürüstlük).

---

## §K Enjeksiyon Senaryoları (güç/faz kırma)

| # | Enjeksiyon | Hedef | Kabul |
|---|---|---|---|
| E1 | indirme sırasında kesinti | etiket değişmez | boot korunur |
| E2 | yazma sırasında kesinti | hedef slot geçersiz | eski slot kurtarıcı |
| E3 | onay öncesi kesinti | etiket beklemede | ilk boot'ta karar |
| E4 | sayaç düşürme denemesi | red | sayaç sabit |
| E5 | manifest değiştirme | sıfırlama/RED | eski indirme kullanılmaz |
| E6 | boot-lock etkin geri alma | RED | kilit ihlal edilmez |
| E7 | sağlık kontrolünü engelleme | geri alma | zaman aşımı RED değil — davranış UNKNOWN (kayıt) |

1. Her enjeksiyon bir DV-U ile çiftlenir (E1↔DV-U12, E2↔DV-U13, E4↔DV-U14…).
2. E7'nin beklenen davranışı **tanımsızdır** (açık kayıt — uydurma tepki yasak).

---

## §L Kapı Yetkisi ve Sıra (update kapısı)

| Kapı | Sahip | Min. kanıt | Durum |
|---|---|---|---|
| Kapı-1 manifest doğrulama | update + security (Q4) | UX2 sonucu | Kısmi (Q4 açık) |
| Kapı-2 imza doğrulama | security (K-S1 köprüsü) | UX2 + primitive | **BLOKE** (T5) |
| Kapı-3 yazma güvenliği | update | UX3 sonucu | BLOKE (G9) |
| Kapı-4 geri alma | update + boot | UX2/UX3 | Kısmi |
| Kapı-5 anti-rollback | update (sayaç) + security (okuma) | UX2 | Kısmi |
| Kapı-6 kademeli dağıtım | update (filo) | telemetri | PLANNED |

### §L.1 Sıra kuralı

1. Kapı-2 bloke iken Kapı-1 yalnız "manifest bütünlüğü" için değerlendirilir
   (imza iddiası yazılmaz).
2. Kapı-3 için UX3 zorunlu; UX3 yoksa kapı BLOKE satırı taşır (gizleme yasak).
3. Karar yetkisi agent'a **devredilmez** (yalnız kanıt üretimi).

---

## §M Red Team Notları (bu ek için)

1. **İtiraz:** "UX2 yeterli, güç kesintisi simüle edilir." **Karar:** hayır — simülasyon
   fiziksel yazma sırasını kanıtlamaz; kapı-3 UX3 ister (ölçüm dürüstlüğü).
2. **İtiraz:** "Rollback sayaçla birlikte düşsün." **Karar:** hayır — anti-rollback
   ihlali (ota-update.md §7.1 [Satır: 134] monotonluk).
3. **İtiraz:** "Sağlık kontrolü engellenirse devam edelim." **Karar:** hayır — geri alma
   yolu güvenlik yoludur; davranış tanımsızsa RED (E7 kaydı).
4. **İtiraz:** "Boot-lock atlanabilir." **Karar:** hayır — kilit sahibi boot (DV-U20).
5. **İtiraz:** "Cohortları atlayıp herkese verelim." **Karar:** hayır — kademeli kapı
   (DV-U17 · tehdit: filo geneli arıza).

---

## §N Çapraz Bağlantılar (SSOT — kopyalama yasak)

1. Kapı kriterleri: firmware-test-stratejisi.md §18 (G-kapıları).
2. Boot tarafı: boot-dogrulama.md (boot-lock · slot seçimi fiziksel).
3. İmza akışı: security-dogrulama.md §H (DV-S serisi · K-S1).
4. Kapı çalıştırma altyapısı: testing-surucu.md §11–§13.
5. Güç-kesinti güvenliği kaynağı: .ai/electronic/firmware/ota-update.md §12 [Satır: 237].
6. A/B mekanizması kaynağı: ota-update.md §6.1 [Satır: 97] · §6.2 [Satır: 124].

---

## §O Kapanış Kontrolü (bu ek)

| Kontrol | Sonuç |
|---|---|
| Mevcut §A–§F silindi mi? | HAYIR (append-only) |
| Frontmatter | korundu (10 alan + group: kanit) |
| Dolgu/TBD | 0 |
| Çalıştırılmış iddiası | 0 (12 kart PLANNED/BLOKE) |
| Uydurma sayı/ad/URL | 0 |
| Son satır Authority | birebir korunur |


---

## §P Görev, Fixture ve Risk Şemaları (tamamlama)

### §P.1 Görev sırası (PLANNED)

| Adım | İş | Bağımlılık | Sahip |
|---|---|---|---|
| U-DV1 | arayüz sözleşmeleri dondurma (manifest/slot/sayaç) | Q4/T5 kararları | update + security |
| U-DV2 | UX1 sahte ortam | U-DV1 | test-automator |
| U-DV3 | UX2 bütünleşik kurulum | U-DV2 + firmware kodu | QA |
| U-DV4 | UX3 HIL (güç kesme) | G9 workflow | DevOps + QA |
| U-DV5 | DV-U10…DV-U21 çalıştırma | U-DV3/U-DV4 | QA |
| U-DV6 | Kapı değerlendirmesi | U-DV5 | Vault Steward + sahipler |

### §P.2 Fixture şartnamesi (uygulama yok — şartname)

| Kod | Fixture | Kullanım | Üretim |
|---|---|---|---|
| FX-U1 | bozuk image (imzasız) | DV-U15 | mutasyon |
| FX-U2 | bozuk ama imzalı image (sağlık hatalı) | DV-U11 | mutasyon + imzalama (test) |
| FX-U3 | yarım indirilmiş dosya | DV-U12/U16 | kesme betiği (taslak) |
| FX-U4 | iki "kurulu" slot | DV-U18 | etiket elleme (DX3) |
| FX-U5 | eski sürüm image | DV-U14 | sürüm sabitleme |
| FX-U6 | NVS dolu | DV-U19 | ayar üretici (taslak) |

1. Fixture envanteri her çalıştırmadan önce sayılır; eksik = senaryo BLOKE.
2. Değerler (imza/karma/sayaç) uydurulmaz — okunup §J.1'e yazılır.

### §P.3 Risk → senaryo izi

| Risk | Kök | Senaryo | Durum |
|---|---|---|---|
| R-brick (cihaz tuğlalaşma) | yazma+güç kesintisi | DV-U12/U13 | UX3 bloke |
| R-downgrade | sayaç | DV-U14 | Kısmi |
| R-imza (T5/Q4) | primitive | DV-U15 | BLOKE |
| R-manifest sahtesi | transport | DV-U16 | PLANNED |
| R-filo (canary) | dağıtım | DV-U17 | PLANNED |
| R-boot-lock ihlali | kilit | DV-U20 | PLANNED |
| R-kullanıcı verisi | NVS | DV-U19 | UX3 bloke |

### §P.4 Açık soru ↔ senaryo etkisi

| Açık soru | Etkilenen | Etki |
|---|---|---|
| Q4 imza sahipliği | DV-U15 · Kapı-2 | kapı kısmin/BLOKE |
| T5 primitive (security) | DV-U15 | BLOKE |
| Boot-lock yazma satırı (boot-dogrulama §U) | DV-U20 | arayüz UNKNOWN |
| G9 workflow tanımsızlığı | DV-U12/U13/U19 | BLOKE |
| Ölçüm/HIL altyapısı yok | §J.1 ölçüm | PLANNED |

---

## §Q Kullanım Kılavuzu (kim ne okur)

1. **QA**: §H (kartlar) → §P.1 (sıra) → §J.1 (kayıt şeması).
2. **Update sahibi**: §H.1/H.2 (etiket/slot davranışları) + §P.3 (risk izi).
3. **DevOps**: §I (UX3) + §P.1 U-DV4 — G9 kurulumu.
4. **Security**: §H.5 (DV-U15) + §P.4 (Q4/T5 bağımlılığı).
5. **Vault Steward**: §L (kapı yetkisi) + §O (kapanış sayımı).

### §Q.1 Dosyanın güncellenme koşulu

- Q4/T5 çözülürse: §P.4 satırı append ile güncellenir · DV-U15 BLOKE'dan çıkar.
- G9 açılırsa: §I UX3 satırı + §P.1 U-DV4 güncellenir.
- Gerçek sonuçlar: yalnız §J.1 şemasıyla append (uydurma sonuç yasak).

---

## §R Terimler ve Kısaltmalar

| Terim | Anlamı |
|---|---|
| DV-U* | update doğrulama senaryosu serisi |
| UX1–UX3 | update ortam kademeleri (§I) |
| FX-U* | update fixture kodları (§P.2) |
| DFU faz 1–4 | indirme/akış güncelleme fazları (update-surucu §10) |
| Kapı-1…6 | update kapıları (§L) |
| cohort | kademeli dağıtım grubu (DV-U17) |
| NVS | kalıcı ayar bölgesi (DV-U19) |
| BLOKE | ön koşul eksik — FAIL değildir |

### §R.1 Son cümle

Bu ek (§H–§R) update-dogrulama.md dosyasını 12 senaryo kartı, 3 ortam kademesi,
7 enjeksiyon, kapı yetki tablosu, görev/fixture/risk şemaları ve sözlükle
derinleştirmiştir; **hiçbir senaryo çalıştırılmamıştır** (PLANNED/BLOKE). Mevcut
§A–§F + frontmatter append-only korunmuştur. Vault çapaları: ota-update.md
§6.1/§6.2/§7.1/§9/§10.2/§12/§18.2/§19.1/§20.1/§26 (okuma: 2026-10-15).


---

## §S Öncelik, Rapor ve Son Sayım (bu ek kapanışı)

### §S.1 Senaryo öncelik sırası (ilk çalıştırma planı)

1. UX1 ile: DV-U16 (manifest) → DV-U14 (sayaç) → DV-U17 (canary mantığı).
2. UX2 ile: DV-U10 → DV-U11 → DV-U18 → DV-U20.
3. UX3 ile: DV-U12 → DV-U13 → DV-U19.
4. Blokajlı: DV-U15 (T5/Q4) → karar sonrası.
5. Bütçe: DV-U21 — UX1/UX2'de yalnız iptal mantığı (ölçüm UX3).

### §S.2 Kapı raporu satırı iskeleti (taslak — dolgu yok)

```text
# kapi raporu iskeleti (taslak): kod | durum | kanit(dosya+satir) | sahip
# durum degerleri: PASS / FAIL / BLOKE / INVALID / BELIRSIZ
# kapi-2 (imza) icin zorunlu not: T5/Q4 acikken BELIRSIZ/BLOKE (gizleme yasak)
```

### §S.3 Red team ek itirazları

1. **İtiraz:** "DV-U15'i kapatalım, gözükmesin." **Karar:** hayır — kapı BLOKE satırı
   taşır (P21 · testing-anayasa §20).
2. **İtiraz:** "Kesinti testi için simülasyon yeter." **Karar:** kısmi — kapı-3 UX3
   ister; simülasyon ölçüm sayılmaz (§M.1).

### §S.4 Son sayım (bu ek)

| Kontrol | Sonuç |
|---|---|
| Eklenen bölümler | §H–§S |
| Senaryo kartı | 12 (DV-U10…DV-U21) |
| Silinen mevcut içerik | 0 (append-only) |
| Dolgu/TBD | 0 |
| Çalıştırılmış iddiası | 0 |
| Uydurma sayı/ad/URL | 0 |
| Son satır Authority | birebir korunur |


---

## §T Ek Kapanış Tablosu (son satır öncesi son ek)

| Kontrol | Sonuç |
|---|---|
| Bölüm toplamı | §A–§F (mevcut) + §H–§T (bu ek) |
| Kapı "geçti" iddiası | 0 — tümü PLANNED/BLOKE/BELIRSIZ |
| Vault çapaları | ota-update.md · boot-sequence.md (§J/§N — dosya+satır) |
| Erişilemeyen kaynak | ⚠️ VERIFICATION REQUIRED (uydurma yok) |
| Frontmatter + Authority | korundu (append-only) |


---

## §U Son Ek Satırı (kapı kapısı ölçüm notu)

| Not | Değer |
|---|---|
| Toplam senaryo kartı (dosya geneli) | §A DV-U1…U9 (mevcut) + §H DV-U10…U21 (bu ek) = 21 |
| Çalıştırılmış | 0 |
| BLOKE | DV-U12/U13/U15/U19 (+ UX3 bağımlıları) |
| Kısmi kapı | Kapı-1 · Kapı-4 · Kapı-5 (Q4/G9 bağımlı) |
| Sonraki eylem | Q4/T5 kararı + G9 workflow → §V eklenir (append-only) |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
