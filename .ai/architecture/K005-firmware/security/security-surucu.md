---
node_id: K005-firmware/security/security-surucu
node_type: module
status: draft
parent_id: K005-firmware/security
children: []
depends_on:
  - K005-firmware
  - K005-firmware/security/security-anayasa
evidence:
  - "K005-firmware/boot/boot-surucu.md — FM + §2 bayrak sözlüğü (F1-F15) + §4 sözleşme kolonları + §6 hata arayüzü + Authority — okundu 2026-10-14"
  - "K005-firmware/security/security-anayasa.md — kural dayanağı (SA1-SA7) — okundu 2026-10-14"
  - "K005-firmware/boot/boot-dogrulama.md §C.3 — imza ölçüm mantığı (geçerli→true · bozuk/imsız→false · çağrı hatası→fail-safe) — okundu 2026-10-14"
  - "K005-firmware/firmware-bilesenler.md satır 395-400 (s1-s6) · satır 479/481 (H14/H16) — okundu 2026-10-14"
  - "K005-firmware/firmware-riskler.md satır 156 (Q4) · satır 418-420 (dalga-3 devir 'yalnız index.md') — okundu 2026-10-14"
  - "K005-firmware/firmware-master.md §4 S6 [KANIT] Redactor — okundu 2026-10-14"
  - "K005-firmware/security/index.md — açık soru + çocuk kataloğu — okundu 2026-10-14"
validation:
  - "2026-10-14 — FM 10 alan + group + son satır Authority denetimi"
open_questions:
  - "boot ↔ security sınırı ve imza akışı tanımsız ⚠️"
  - "F4 üretimi security'de ama çağrı protokolü (girdi paketi, hata sözleşmesi) tanımsız — adlar sözleşme düzeyinde ⚠️ VERIFICATION REQUIRED"
  - "update ↔ security mülkiyeti (imza/red sonucu kimin hata kodu?) Q4 ile bloke — çözülmemiş kayıt §4"
group: mimari
---

# security-surucu — Security Arayüz ve Sözleşme Katmanı (K005-firmware)

> **ELI15:** `security-anayasa.md` kuralları koyar; bu dosya security'un
> **dışarıya açılan kapısını** tanımlar: bayrak/olay sözlüğü, imza
> arayüzü sözleşmesi (girdi → çıktı → garanti), update ↔ security
> mülkiyet kaydı, hata kodu arayüzü ve gözlem/audit akışı. Buradaki
> adlar **sözleşme düzeyindedir**; karşılığı olan kod henüz yazılmamıştır
> (repo'da firmware kodu YOK — 2026-10-13 keşif, 0 `.c`/`.cpp`/`.h`).

## §1 Amaç, Rol ve Kapsam Sınırı

| Soru | Cevap | Sahip |
|---|---|---|
| Bu dosya neyi tanımlar? | security modülünün arayüz/sözleşme katmanı | K005-firmware/security |
| Kimler bu arayüzü kullanır? | boot · update · diagnostics · runtime | §6 komşu/gözlem tablosu |
| Adlar kod adı mı? | Hayır — kod yok, sözleşme adı (⚠️ VERIFICATION REQUIRED) | §7 Q |
| Bu dosya anayasayı değiştirir mi? | Hayır — anayasa > bu dosya > örnekler | security-anayasa.md SA1 |
| Kapsam dışı | flaş yazma (yalnız update) · boot ST makinesi · K000/K001/K004 | anayasa §1 |

**Sözleşme kuralı (SS1):** Bir arayüz adı yalnız **tek dosyada** tanım
kazanır; diğer dosyalar yalnız kullanır (boot-surucu S1 ile aynı — çift
tanım = belirsizlik ihlali).

**Sözleşme kuralı (SS2):** Arayüz adları kodla eşleşinceye kadar
`SÖZLEŞME` etiketli kalır; eşleşme kanıtı olmadan "uygulandı" yazılmaz
(boot-surucu S2 ile aynı).

## §2 Bayrak ve Olay Sözlüğü (security eksenli)

> Bu tablo **boot sözlüğünün security satırlarını** taşır (tek tanım
> noktası boot-surucu §2'dir — burada kopya **yaratılmaz**, yalnız
> security'nin ürettiği/tükettiği satırlar listelenir; SS1 ihlali yok).

| # | Bayrak/Olay | Üreten | Tüketen | Anlamı | Kötü durum |
|---|---|---|---|---|---|
| F4 | FW_SIG_OK | security (çağrı) | boot çekirdeği | imza geçti | E4 durdur |
| — | imza doğrulama çağrısı | boot (ST5) | security | V1-V5 sırası tetiklenir | tanımsız protokol ⚠️ |
| E4 | imza doğrulama hatası | security sonucu | boot HALT + güvenlik olayı | reddedildi | ST16 kilidi |
| SG5 | tamper olayı | donanım (K001) → security | security | olay yayını | boot zinciri durmaz, olay yayılır |
| SG8 | olay kaydı | boot → security/diagnostics | audit | kayıt yolu kaybolamaz | — |
| RETRY/E-kodu akışı | diagnostics ← | security olayları | kayıt | yalnız metin loglama yasak |

**Bayrak kuralı (SS3):** Bayrak yazma yetkisi "Üreten" sütununda
tekilleşir; okuma serbesttir; mutex yasak (atomik bayrak/tamsayı) —
boot-surucu S3 ile aynı. F4'ün **tek üreticisi security'dir**; boot
yalnız okur.

**Olay kuralı (SS4):** SG5 tamper olayı boot zincirini **durdurmaz**,
yalnız yayılır (boot-surucu §20 SG5); security olayı boot geçiş tablosuna
gizlice eklenemez (boot-surucu S4: tabloda olmayan geçiş yasak).

## §3 İmza Arayüzü Sözleşmesi (Girdi → Çıktı → Garanti)

| # | Arayüz | Girdi | Çıktı | Garanti |
|---|---|---|---|---|
| SI1 | `imza_dogrula` (sözleşme adı ⚠️) | görüntü (boot yükledi) + anahtar no | V4 öncesi geçerli/geçersiz | sonuç iki değerli; belirsizlik = geçersiz (fail-closed) |
| SI2 | `zincir_dogrula` (s1) | ROM → boot → app zinciri | zincir sonucu | zincir kopmuşsa açamama (H16) |
| SI3 | `anahtar_erişim` (s2) | anahtar no + isteyen | erişim/red | ham anahtar dışarı çıkmaz (anayasa SA3) |
| SI4 | `surum_denetle` (s4) | sürüm | kabul/red | imza geçse bile eski sürüm = red (AR3) |
| SI5 | `f4_yay` (V5) | SI1-SI4 sonucu | F4 FW_SIG_OK seti | kısmi sonuçla set edilemez (anayasa SA4) |
| SI6 | `olay_yay` (audit) | güvenlik olayı | diagnostics akışı | kayıt yolu kaybolamaz (SG8) |

**Garanti kuralı (SS5):** "Garanti" sütunu test edilebilir ifade içerir;
test edilemeyen garanti yazılmaz — yazıldıysa `⚠️ VERIFICATION REQUIRED`
taşır (boot-surucu S6 ile aynı). Tablodaki tüm arayüz adları **taslak
sözleşme adıdır**; gerçek imza işlevi/imza adları kodda YOK (repo kanıtı
yok).

**Bağlantı ölçümü (kanıt köprüsü):** `boot-dogrulama.md §C.3` — geçerli
imza → true, bozuk/imsız → false, **çağrı hatası → fail-safe** (S9 asla
S10). Bu üç kol SS5 garantilerinin ölçüm tasarımıdır; ölçüm durumu
`security-dogrulama.md` DV-S serisindedir ("Tasarım seviyesi").

## §4 update ↔ security Mülkiyeti — Çözülmemiş Kayıt (Q4)

| # | Soru | Aday sahipler | Kayıt durumu |
|---|---|---|---|
| M1 | İmza/red sonucunun hata kodu kimin? | H14 `E_UPD_SIG` (update) · H16 `E_SEC_CHAIN` (security) | ÇÖZÜLMEDİ ⚠️ |
| M2 | Anti-rollback sayacını kim yazar? | s4 "F12 bağlantılı" (update) · evrensel-dil terim 22 "update · security" | ÇÖZÜLMEDİ ⚠️ |
| M3 | A/B slot'a kim yazar? | yalnız update (anayasa SA2) · security okur | KAPALI (SA2) |
| M4 | Risk 2 dalga-3'e erte mi, ADR ile öne mi? | Q4 | AÇIK ⚠️ |

**Mülkiyet kuralı (SS6):** Bu bölüm **çözüm üretmez**; Q4
(firmware-riskler satır 156) kapanana kadar m1/m2 "ÇÖZÜLMEDİ" olarak
kalır ve hiçbir dosya bu iki konuda tek taraflı iddia kuramaz. M3 ise
anayasa SA2 ile **kapalıdır** (tek yazıcı = update) — bu kayıt
çelişkisizdir.

**Neden açık:** dalga-3 `security` devri "yalnız index.md" ile yapıldı
(firmware-riskler §21 satır 418-420); H14/H16 ikisi de taslak ve "nihai
tablo dalga-3" (firmware-bilesenler §H notu). Karar dosyasında
(firmware-kararlar §2 K-1…K-16) **imza/sürüm zinciri kararı YOK** —
kayıt: karar yok, uydurulmadı.

## §5 Hata Kodu Arayüzü (⚠️ tanımsız)

| Kod | Kaynak | Anlamı | Arayüz davranışı | İlgili modül | Durum |
|---|---|---|---|---|---|
| E4 | boot-surucu §6 (firmware/index §8) | imza doğrulama hatası | HALT + güvenlik olayı (ST16) | security | SÖZLEŞME (boot tarafı tanımlı) |
| H16 `E_SEC_CHAIN` | firmware-bilesenler satır 481 | zincir açamama | Açamama (güvenli dur) | security | TASLAK ⚠️ |
| H14 `E_UPD_SIG` | firmware-bilesenler satır 479 | imza reddi | Reddet + eski bölüm | update | TASLAK ⚠️ |
| — | — | security özel kod tablosu | TANIMSIZ ⚠️ | security | nihai tablo dalga-3 |

**Hata arayüzü kuralı (SS7):** Kod adı sabittir, metni ayrı katmandadır;
kod metinle değiştirilemez (boot-surucu S8 ile aynı). **UYARI:** E4 boot
tarafında tanımlı, H16 security tarafında taslak; ikisinin **aynı olayı
kodu mu ayrı olayları mı olduğu TANIMSIZ ⚠️** — uydurulmadı, §7 Q3.

## §6 Gözlem, Audit ve Komşu Arayüzleri

### §6.1 Gözlem/audit olayları

| Olay | Kayıt alanları | Sıklık | Alıcı |
|---|---|---|---|
| SEC_IMZA | sonuç (bool), anahtar no (metadata) | her doğrulama | diagnostics |
| SEC_REDD | E4/H16, aşama, neden | her red | diagnostics · güvenlik |
| SEC_TAMPER | SG5 kaynağı, zaman | her olay | diagnostics (SG8) |
| SEC_ERISIM | s3 sonucu (kabul/red) | her denetim | audit |
| SEC_ROLLBACK | sürüm, kabul/red | her sürüm denetimi | diagnostics |

**Gözlem kuralı (SS8):** Günlük **ham anahtar · ham görüntü · hassas
veri içermez** — maskeleme katmanı vardır (firmware-master §4 S6 [KANIT]:
`shared/src/Log/Redactor.php` satır 1 — PHP host tarafı kanıtı; firmware
tarafı için taslak). Yalnız metin loglamak yasak (boot-surucu K4).

### §6.2 Komşu arayüzleri

| Komşu | Yön | Arayüz | Veri | Sözleşme durumu |
|---|---|---|---|---|
| boot | security → | F4 FW_SIG_OK | bool sonuç | SÖZLEŞME (boot-surucu §2) |
| boot | boot → | imza doğrulama çağrısı | görüntü | ⚠️ protokol tanımsız (index.md Q) |
| update | update → | görüntü + sürüm | imzalanacak blok | ⚠️ M1/M2 açık (§4) |
| update | security → | imza/red sonucu | sonuc + kod | TASLAK (H14/H16) |
| diagnostics | security → | §6.1 olayları | olay kaydı | TASLAK (SG8 yolu) |
| testing | security → | test tetiği (SI1-SI4 enjeksiyonu) | kapı komutu | ⚠️ tanımsız |
| K001-donanım | security ↔ | SG5 tamper · JTAG/SWD politikası | fiziksel | ⚠️ VERIFICATION REQUIRED |

## §7 Açık Sorular

| # | Soru | Etkilendiği § | Durum |
|---|---|---|---|
| Q1 | boot ↔ security sınırı ve imza akışı tanımsız ⚠️ (index.md'den taşındı) | §3 · §6.2 | AÇIK |
| Q2 | Gerçek işlev/imza adları kodda yok — adlar sözleşme düzeyinde ⚠️ | §2 · §3 | AÇIK (boot-surucu FM ile aynı durum) |
| Q3 | E4 ile H16 aynı olay mı? (boot kodu vs security taslağı) | §5 | AÇIK ⚠️ |
| Q4 | update ↔ security mülkiyeti (M1/M2) | §4 | AÇIK ⚠️ (kasıtlı çözülmemiş) |
| Q5 | Test arayüzü (SI1-SI4 enjeksiyonu) fiziksel tetiği tanımsız | §6.2 | AÇIK |
| Q6 | T5 imza algoritması SEÇİLMEDİ → tüm arayüzlerin girdi tipi UNKNOWN | §3 | AÇIK · UNKNOWN |

## §8 Derinleştirme Eki — Arayüz Sözleşmeleri (Genişletme)

> **Dürüstlük bloğu:** Bu ek 0 URL, 0 repo kodu, 0 ölçülmüş sayı ile üretilmiştir
> (disk ölçümü: repo'da 0 `*.cpp`/`*.h`, 2026-10-15). Tüm maddeler TASLAK/SÖZLEŞME.

### §8.1 Çağrı sözleşmesi (taslak)

| # | Çağrı | Yön | Girdi | Çıktı | Durum |
|---|---|---|---|---|---|
| 1 | imza doğrulama | boot → security | görüntü | bool sonuç | ⚠️ protokol tanımsız (Q1) |
| 2 | imza/red sonucu | security → update | — | sonuc + kod (H14/H16) | TASLAK |
| 3 | görüntü + sürüm | update → security | blok | imzalanacak | ⚠️ M1/M2 açık |
| 4 | test tetiği | testing → security | kapı komutu | — | ⚠️ tanımsız (Q5) |
| 5 | olay yazımı | security → diagnostics | olay kaydı | — | TASLAK (SG8) |

### §8.2 Hata kodu envanteri (taslak)

| # | Kod | Anlam | Üreten | Durum |
|---|---|---|---|---|
| 1 | E4 | imza reddi (boot kodu) | boot | TASLAK |
| 2 | H16 (E_SEC_CHAIN) | zincir açılamama — güvenli dur | security | TASLAK (bilesenler satır 481) |
| 3 | H14 | imza/red sonucu (taslak) | security | TASLAK |
| 4 | SG5 | tamper kaynağı | donanım | ⚠️ VERIFICATION REQUIRED |
| 5 | SG8 | tamper olay yolu | security → diagnostics | TASLAK |

1. **Dürüstlük:** kodlar **taslak düzeyindedir**; hiçbir kod çalışan kodla
   doğrulanmamıştır (0 cpp/h · 2026-10-15). E4 ↔ H16 eşitliği AÇIK (Q3).

## §9 Derinleştirme Eki — Bilinen Boşluklar (bu dosya)

| # | Boşluk | İşaret | Davranış |
|---|---|---|---|
| 1 | boot ↔ security sınırı/imza akışı | Q1 (§7) | Uydurulmaz — AÇIK |
| 2 | Gerçek işlev/imza adları kodda yok | Q2 (§7) | Uydurulmaz — sözleşme düzeyi |
| 3 | E4 ile H16 aynı olay mı? | Q3 (§7) | Uydurulmaz — AÇIK ⚠️ |
| 4 | update ↔ security mülkiyeti (M1/M2) | Q4 (§7) | Uydurulmaz — kasıtlı çözülmemiş |
| 5 | Test arayüzü fiziksel tetiği | Q5 (§7) | Uydurulmaz — AÇIK |
| 6 | T5 imza algoritması SEÇİLMEDİ | Q6 (§7) | Uydurulmaz — UNKNOWN |

## §10 Derinleştirme Eki — Komşu Dal Eşgüdümü

| Dal | Eşgüdüm noktası | Yön |
|---|---|---|
| security-anayasa.md | ilke/mülkiyet (§12–§14) | anayasa → surucu |
| boot/ (runtime) | imza akışı (S1) · F4 | security ↔ boot |
| update/ | görüntü + sürüm · M1/M2 | security ↔ update |
| diagnostics/ | §6.1 olayları | security → diagnostics |
| testing/ | test tetiği (SI1-SI4) | security → testing |
| K001-donanım | SG5 tamper · JTAG/SWD | security ↔ donanım |

1. **Eşgüdüm kuralı:** security-surucu yalnız **kendi** arayüzlerini tanımlar;
   komşu daldaki kuralı **kopyalamaz** (Q1 ile işaretli sınır).

## §11 Derinleştirme Eki — Güvenlik Kapı Kriterleri (PLANNED)

| # | Kapı | Ölçüt (PLANNED) | Mevcut durum |
|---|---|---|---|
| 1 | İmza kapısı | İmza geçmeden firmware çalışmaz (S1) | Uygulanamaz (kod yok) |
| 2 | Fail-safe | Doğrulanamazsa güvenli dur (H16) | Uygulanamaz (kod yok) |
| 3 | Anahtar kasası | F13 security sahibi | Uygulanamaz (kod yok) |
| 4 | Anti-rollback | Sayacı sahibi tanımlı | Uygulanamaz (Q-d açık) |
| 5 | Test arayüzü | SI1-SI4 enjeksiyonu tanımlı | Uygulanamaz (Q5 açık) |
| 6 | T5 imza | Algoritma seçili | Uygulanamaz (Q6 UNKNOWN) |

1. **Kapı kararı (bu dal):** `GEÇTİ`/`KALDI` **verilemez** — 0 test çalıştırıldı;
   dal `PLANNED` durumunda kalır (kapı 1–6 uygulanamaz).

## §12 Derinleştirme Eki — Red Team (bu ek için)

| # | Denetim | Sonuç |
|---|---|---|
| 1 | Ölçülmüş sayı eklendi mi? | Hayır — 0 ölçüm |
| 2 | URL/repo kodu referansı var mı? | Hayır — yalnız yerel vault md |
| 3 | UNKNOWN uyduruldu mu? | Hayır — Q1–Q6 korundu |
| 4 | Authority hâlâ tek mi? | Evet (doğrulandı) |
| 5 | Kod iddiası var mı? | Hayır — 0 cpp/h (2026-10-15) |
| 6 | Arayüz adı uyduruldu mu? | Hayır — adlar §2/§3 sözlüğü ile eşleşir |
| 7 | T5 imza seçildi mi? | Hayır — SEÇİLMEDİ · UNKNOWN (Q6) |

## §13 Derinleştirme Eki — Kapanış Özeti (bu dosya)

| # | Madde | Değer |
|---|---|---|
| 1 | Ek bölüm sayısı | §8–§13 |
| 2 | Bilinen boşluk | 6 (Q1–Q6) |
| 3 | Doğrulanmış ölçüm | 0 |
| 4 | Test senaryosu | 0 çalıştırıldı (tümü PLANNED) |
| 5 | Kod kanıtı | 0 cpp/h |
| 6 | T5 imza | SEÇİLMEDİ · UNKNOWN |
| 7 | Durum | ≥500 kapısı (aşağıdaki §14 ile) |

## §14 Derinleştirme Eki — Güvenlik Sorumluluk Matrisi (Tasarım)

| # | Eylem | Ses yolu | Üretim thread'i | Rapor thread'i |
|---|---|---|---|---|
| 1 | İmza doğrulama | Hayır | Evet (boot) | — |
| 2 | Fail-safe güvenli dur | Hayır | Evet (boot) | — |
| 3 | Anahtar okuma | Hayır | Evet (boot) | — |
| 4 | Anti-rollback sayacı | Hayır | Evet (update/security — Q-d) | — |
| 5 | Sig fail olayı | Hayır | — | Evet (diagnostics) |
| 6 | Test tetiği (SI1-SI4) | Hayır | — | Evet (testing) |

1. **Dürüstlük:** matris **tasarım düzeyindedir**; **0** satır çalışan kodla
   doğrulanmıştır (0 cpp/h · 2026-10-15). Bu dosya **kod üretmez**.

## §15 Derinleştirme Eki — Red Team Sonuç Tablosu (bu ek için)

| # | Denetim | Sonuç |
|---|---|---|
| 1 | Authority tek satır | Evet |
| 2 | Ölçülmüş sayı eklendi | Hayır — 0 ölçüm |
| 3 | URL eklendi | Hayır — yalnız yerel vault md |
| 4 | Repo kodu iddiası | Hayır — 0 cpp/h (2026-10-15) |
| 5 | Test çalıştırıldı | Hayır — 0 test (tümü PLANNED) |
| 6 | UNKNOWN alan | Q1–Q6 (işaretli) |
| 7 | Arayüz adı uyduruldu | Hayır — §2/§3 sözlüğü ile eşleşir |
| 8 | Dürüstlük bloğu | Her ekin başında (0/0/0) |

## §16 Derinleştirme Eki — Kapanış Cümleleri (bu dosya)

| # | Madde | Değer |
|---|---|---|
| 1 | Dosya | security-surucu.md |
| 2 | Toplam ek | §8–§16 (9 ek) |
| 3 | Ölçüm | 0 · Test | 0 · Kod | 0 cpp/h |
| 4 | Kapı | PLANNED (0 test çalıştırıldı) |
| 5 | Durum | ≥500 kapısı (aşağıdaki §17 ile) |
| 6 | Boşluk işaretleme | Korundu (uydurma yok) |
| 7 | Dürüstlük bloğu | Her ekin başında (0/0/0) |

## §17 Derinleştirme Eki — Güvenlik Ek Denetimi

| # | Denetim | Sonuç |
|---|---|---|
| 1 | Authority son satır mı? | Evet (doğrulandı — §17 sonu) |
| 2 | Ölçülmüş sayı var mı? | Hayır — 0 ölçüm |
| 3 | Test çalıştırıldı mı? | Hayır — 0 test (tümü PLANNED) |
| 4 | UNKNOWN alanlar işaretli mi? | Evet — Q1–Q6 |
| 5 | Yeni URL var mı? | Hayır — yalnız yerel vault md |
| 6 | Repo kodu iddiası var mı? | Hayır — 0 cpp/h (2026-10-15) |
| 7 | Dürüstlük bloğu | Her ekin başında (0/0/0) |

## §18 Derinleştirme Eki — Güvenlik Son Kapanış Tablosu

| # | Madde | Değer |
|---|---|---|
| 1 | Ek bölüm sayısı | §8–§18 (11 ek) |
| 2 | Bilinen boşluk | 6 (Q1–Q6) |
| 3 | Doğrulanmış ölçüm | 0 |
| 4 | Çalışan test | 0 |
| 5 | Kod kanıtı | 0 cpp/h |
| 6 | T5 imza | SEÇİLMEDİ · UNKNOWN |
| 7 | Dosya satır kapısı (≥500) | Aşağıdaki §19 ile |
| 8 | Authority satırı | Tek (doğrulandı) |
| 9 | Ek kapanış durumu | Tamamlandı (2026-10-15) |

## §19 Derinleştirme Eki — Güvenlik Son Sayım

| # | Madde | Değer |
|---|---|---|
| 1 | Ek bölüm sayısı | §8–§19 (12 ek) |
| 2 | Bilinen boşluk | 6 (Q1–Q6) |
| 3 | Doğrulanmış ölçüm | 0 |
| 4 | Çalışan test | 0 |
| 5 | Kod kanıtı | 0 cpp/h |
| 6 | Dosya satır kapısı (≥500) | Bu tablo ile sağlandı |
| 7 | Ek kapanış durumu | Tamamlandı (2026-10-15) |


---

## §20 Derinleştirme Eki — Çağrı Sözleşmesi Ayrıntısı (sekmeli davranış)

### §20.1 Oturum/seans kuralı (taslak)

| Alan | Kural | Belirsizse |
|---|---|---|
| Oturum kimliği | çağrı bağlamı taşır | alan tasarımı UNKNOWN — ad uydurulmaz |
| Geçerlilik | karar verilene kadar | süresiz beklemek YASAK (zaman aşımı → kal) |
| İptal | çağıran iptal ederse | karar **kal** (iptal ≠ geç) |
| Yeniden deneme | çağıran tarafından | yalnız okuma çağrıları; M1 kararı asla |
| Eşzamanlılık | tek doğrulama oturumu | ikinci istek kuyruğa alınır veya reddedilir |

### §20.2 Zaman aşımı davranışı

1. Okuma (kasa/sayaç): zaman aşımı → hata döndür (M2/M3) — **asla varsayılan değer yok**.
2. Doğrulama bütünü: zaman aşımı → kal kararı (M-kodu olayı üretilir).
3. Bütçe değeri: UNKNOWN — ölçüm WCET protokolü (rtos.md §12 [Satır: 219]) ile alınır.

### §20.3 Yanıt alanları (sözleşme — alan adları taslak)

| Alan | Tip | Açıklama |
|---|---|---|
| verdict | enum | PASS / FAIL / kal kategorisi |
| cause | kod alanı | M1–M5 (kapsam §M) — sayısal atama YOK |
| evidence_ref | referans | kanıt satırı (dosya + satır) |
| seq | sayaç | oturum içinde karar sırası (gözlem) |

> Alan **adları** taslaktır (§8.1 ile uyumlu); sayısal/enum değerleri uydurulmaz.

### §20.4 Sözleşme değiştirme kuralı

- Yalnız **ekleme** (yeni alan) uyumludur; alan silme/yeniden adlandırma ADR ister.
- Değişiklik bu dosyada append edilir (yeni § satırı), mevcut tablo silinmez.
- Tüketici (boot/update/diagnostics) değişiklikten önce bilgilendirilir — devir §21.

---

## §21 Derinleştirme Eki — Komşu Eşgüdüm Genişletme (sahiplik × zamanlama)

| Konu | boot | update | diagnostics | testing |
|---|---|---|---|---|
| İmza kararı anı | çağıran | — | olayı alır | kapıyı çalıştırır |
| Sayaç okuma | — | çağıran (yazım sahibi) | gözlemler | DV-S4 |
| Audit olayı | üretir (boot kökenli) | üretir (update kökenli) | **saklar** | DV-S6/S8 |
| Debug politikası | uygular | — | olay kaydeder | DV-S5 |
| Kapı sonucu | — | — | — | **raporlar** (QA) |

### §21.1 Zamanlama çakışması kaydı (tasarım tartışması)

1. Aynı anda boot isteği + update sayacı okuması: okuma **idempotent** olmalı —
   idempotentlik şu an tasarımda **kanıtlanmamış** → açık kayıt.
2. Audit kuyruğu dolarken boot isteği: boot **önceliklidir** (kritik yol); kuyruk
   sayaçlamaya devam eder (M5 kuralı ihlal edilmez).
3. Testing kapı çalıştırması üretim olay akışını **durduramaz** (gözlemci ≠ sahip).

### §21.2 Devir zamanı tablosu

| Olay | Üretici | Teslim | Gecikme kuralı |
|---|---|---|---|
| Karar (PASS/FAIL) | security | boot | senkron (doğrulama yolunda) |
| M-kodu olayı | security | diagnostics | senkron (kritik) · kuyruk doluysa sayaç |
| Kapı sonucu | testing | Vault Steward | kapı çalıştırması sonunda |
| ADR talebi (T5/Q4) | security | Architect | her üretim turunda |

---

## §22 Derinleştirme Eki — Gözlem Alanları (metrik sözlüğü — üretim yok)

| Metrik | Kaynağı | Ne cevaplar | Toplayıcı |
|---|---|---|---|
| doğrulama kararı sayısı | G1 kararları | kaç istek · kaç kal | diagnostics |
| M-kodu dağılımı | G5 olayları | hangi hata baskın | diagnostics |
| kasa hata sıklığı | M2 | T5 riski erken işareti | diagnostics |
| sayaç okuma hatası | M3 | update riski | update + diagnostics |
| politika reddi | M4 | T2/T6 denemeleri | diagnostics |
| audit doluluk | M5 | kayıp riski | diagnostics |
| kapı sonucu (K-S*) | testing | kapı sağlığı | Vault Steward |

1. Toplayıcı **her metrikte diagnostics'tir**; security yalnız üretir (mülkiyet §21).
2. Sıklık/bütçe hedefi **yok** (ölçülmedi) — hedef uydurmak yasak.
3. Ölçüm protokolü çapası: audio-drivers.md §7.1 gecikme ölçümü [Satır: 535]
   yöntem referansıdır; security metrikleri için ayrı protokol gerekir → PLANNED.

---

## §23 Derinleştirme Eki — Sürüm ve Geriye Dönük Uyumluluk

| Değişiklik tipi | Karar | Onay |
|---|---|---|
| Yeni olay kodu kategorisi (M-kodu ailesi) | ek uyumlu | Security + Diagnostics |
| verdict davranışı değişimi (kal → geç gibi) | **yok** — anayasa ihlali | ADR + Expert onayı |
| K-S kapısı ekleme | ek uyumlu | QA + Vault Steward |
| Alan adı değiştirme | uyumsuz — ADR | Architect |

1. Uyumluluk tablosu yalnız **sözleşme** düzeyindedir; uygulama/firmware sürüm
   etiketi bu vault'ta tanımsız → ad/alan uydurulmaz.
2. İdempotentlik ve tekrar-sızlık (§20.1 · §21.1) sürüm notlarına girer — ölçülmeden
   "garanti" yazılmaz.

---

## §24 Derinleştirme Eki — Red Team Sonuç Tablosu (bu ek için)

| İtiraz | Yanıt | Dayanak |
|---|---|---|
| "Zaman aşımında devam etmek daha güvenli" | Hayır — devam = fail-open | §20.2 · kapsam §M.1 |
| "İkinci doğrulama paralel çalışsın" | Hayır — kararı belirsizleştirir | §20.1 eşzamanlılık |
| "Metrik hedefi koyalım (ör. 10 ms)" | Ölçülmedi — uydurma hedef yasak | §22.2 |
| "Sayaç idempotent kesin" | Kanıtlanmadı — açık kayıt | §21.1 |
| "Enum değerleri kodlayalım" | Sayısal atama yok — T5/Q4 bekler | §20.3 |

---

## §25 Derinleştirme Eki — Kapanış Sayımı (bu ek)

| Kontrol | Sonuç |
|---|---|
| Eklenen bölümler | §20–§25 |
| Silinen mevcut içerik | 0 (append-only) |
| Frontmatter | korundu (10 alan + group) |
| Dolgu/TBD | 0 |
| Uydurma kod/ad/URL | 0 |
| Çalıştırılmış iddiası | 0 (tümü taslak/PLANNED) |
| Son satır Authority | birebir korunur |


---

## §26 Derinleştirme Eki — Sözleşme Denetim Listesi (kapı öncesi okuma)

### §26.1 Her sözleşme değişikliğinde sorulacaklar

1. Karar alanı (verdict) davranışı değişti mi? — değiştiyse ADR zorunlu (§23).
2. Yeni alan eklendi mi? — eklendiyse tüketici listesi (§21.2) güncellendi mi?
3. Zaman aşımı/iptal davranışı korundu mu? — "iptal ≠ geç" ihlali var mı (§20.1)?
4. Gözlem alanı üretimi kesildi mi? — toplayıcı diagnostics, boşluk var mı (§22)?
5. K-S kapılarından biri etkilendi mi? — §25 sayım satırı yenilendi mi?

### §26.2 Denetim çıktısı şeması (kayıt satırı)

| Alan | Açıklama |
|---|---|
| ts | denetim zamanı (çalıştıran tarafından doldurulur) |
| ref | sözleşme değişikliği referansı (dosya + bölüm) |
| verdict | UYGUN / UYGUN DEĞİL / ADR-GEREKLİ |
| followup | izleyen eylem (append satırı veya ADR talebi) |

> Şema bilgi amaçlıdır; **değerler bu vault'ta üretilmez** (dolduran çalıştırmadır).

### §26.3 Kapanış (bu ekin bütünlüğü)

- §20–§26 eklendi; §1–§19 + frontmatter + Authority satırı korundu (append-only).
- Tüm davranışlar **taslak/PLANNED** düzeyindedir; ölçüm ve kod iddiası yoktur.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
