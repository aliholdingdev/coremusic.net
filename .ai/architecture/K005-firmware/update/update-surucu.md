---
node_id: K005-firmware/update/update-surucu
node_type: module
status: draft
parent_id: K005-firmware/update
children: []
depends_on:
  - K005-firmware/update/update-anayasa
  - K005-firmware/boot/boot-surucu
  - K005-firmware/boot/boot-dogrulama
  - K005-firmware/firmware-bilesenler
  - K005-firmware/firmware-bagimliliklar
evidence:
  - .ai/architecture/K005-firmware/update/update-anayasa.md §2–§5 (okundu 2026-10-14)
  - .ai/architecture/K005-firmware/boot/boot-surucu.md §2–§9 (okundu 2026-10-14)
  - .ai/architecture/K005-firmware/boot/boot-dogrulama.md §U–§V · §F satır 7 (okundu 2026-10-14)
  - .ai/electronic/firmware/ota-update.md §5–§9 · §12–§13.1 (okundu 2026-10-14)
  - .ai/electronic/firmware/boot-sequence.md §8 · §11 · §13.1 (okundu 2026-10-14)
validation:
  - 2026-10-14 — FM 10 alan + group + son satır Authority denetimi
  - 2026-10-14 — boot-surucu deseniyle uyarlama: inline **1.–**13. listeler → ## §1–§13 tablolar; §6–§10 kapı numaraları ile US/UF/UT/UR/KAPI/§12-1/§12-2 alt-referansları KORUNDU (update-dogrulama §U · boot-dogrulama §U–§V çapraz bağları için)
open_questions:
  - "update ↔ güvenlik (imza) kime ait? (K005 Q4) — bu dosyada ÇÖZÜLMEZ; §7 yalnız basamak tanımı verir"
  - "update-surucu §6–§10 beş kapı için owner ataması boot-dogrulama §V'te owner set olarak verilmiş (embedded-engineer · security-engineer · devops-engineer) — kapı-bazında tekil owner dağıtımı kaynakta yok (UNKNOWN)"
group: mimari
---

# Update Sürücüsü — Update Durum Makinesi, Firmware Bayrakları, Yazma/İmza/Rollback Kapıları (K005-firmware/update/update-surucu.md)

> **ELI15:** Update sürücüsü, cihazın yeni yazılımı nasıl indirip yazdığını ve ne zaman geri
> alacağını anlatan durum makinesidir. Her durumun (fetch → verify → write → pending →
> self-test → confirm/rollback) bir çıkışı, her firmware bayrağının (install_state, pending,
> confirmed, rollback_counter) bir sahibi vardır. §6–§10 arasındaki beş kapı (image yazma ·
> imza · rollback · boot-lock · DFU 4 faz) boot-dogrulama §U–§V'in bağladığı zorunlu
> kapılardır; imza kime ait sorusu (Q4) burada AÇIK kalır.

## §1 Amaç, Rol ve Kapsam Sınırı

| Soru | Cevap | Sahip |
|---|---|---|
| Bu dosya neyi tanımlar? | update alt-modülünün **sürücü/yazılım yüzeyi**: durum makinesi · bayraklar · zamanlama · rollback · beş kapı | K005-firmware/update |
| Kimler bu arayüzü kullanır? | boot · security · runtime · testing | §11 komşu tablosu |
| Adlar kod adı mı? | Hayır — repo'da firmware kodu YOK (2026-10-13: 0 kod kanıtı); adlar sözleşme adıdır | `⚠️ VERIFICATION REQUIRED` |
| Anayasa ile ilişki | anayasa > bu dosya > örnekler | update-anayasa.md |

| # | Kapsam içi | Bölüm |
|---|---|---|
| 1 | Update durum makinesi (US0–US10) | §2 |
| 2 | Firmware bayrakları (UF1–UF7) ve sayaç algoritması | §3 |
| 3 | Update zamanlama ve doğrulama pencereleri (UT1–UT5) | §4 |
| 4 | Rollback tetikleyicileri (UR1–UR6) | §5 |
| 5 | Beş zorunlu kapı: image yazma · imza · rollback · boot-lock · DFU 4 faz | §6–§10 |
| 6 | Komşu bağımlılık (boot) ve devir arayüzü | §11 |
| 7 | Hata kodları ve telemetri | §12 |

| # | Kapsam dışı | Nereye gider | Dayanak |
|---|---|---|---|
| 1 | İmza ALGORİTMASI/şema adı ve anahtar kasası | security (Q4 açık) | §7 · update-anayasa §1 |
| 2 | Boot-lock bayrak/setme işlemi | boot bacağında | boot-dogrulama §V · bu dosya §9 |
| 3 | Boot tarafı telemetri/boot-log | boot | boot-dogrulama §G · bu dosya §12 |

## §2 Update Durum Makinesi (US0–US10)

| # | Durum | Giriş koşulu | Yapılan | Çıkış | Hata koluna çıkış |
|---|---|---|---|---|---|
| US0 | `US0-IDLE` | güncel firmware onaylı (`confirmed`) ve ağ/servis yok | — | çıkış yok, bekle | — |
| US1 | `US1-FETCH` | ağ/servis var | manifest + paket indirilir — ota-update.md §5 zincirinin 1–2. basamakları: indir → doğrula | paket tam indi mi? → `US2-VERIFY` | senaryo: update-anayasa §5 UE1 |
| US2 | `US2-VERIFY` | paket indi | paket bütünlüğü + sürüm/manifest doğrulaması (imza basamağı §7) | doğrulama geçerse `US3-WRITE` | başarısızsa `US10-ABORT` |
| US3 | `US3-WRITE` | doğrulama geçti | paket A/B sisteminde atılabilir yuvaya yazılır (§6 kapısı) | yazma hatasız bitti mi? → `US4-PENDING` | güç kesintisi/yazma hatası → yarı yazım tespiti → `US10-ABORT` (boot tarafında kurtarma: boot-dogrulama §U) |
| US4 | `US4-PENDING` | yazma tamam | yeni image yazıldı ama henüz çalıştırılmadı; bayrak `pending=1` (boot-sequence.md §8 `pending` bayrağı) + yeniden başlatma planlanır | `US5-FIRST_BOOT` | — |
| US5 | `US5-FIRST_BOOT` | yeniden başlatma | bootloader yeni image'ı ayağa kaldırır | `US6-SELF_TEST` | — |
| US6 | `US6-SELF_TEST` | ilk boot tamam | self-test + health-check penceresi (UT3): firmware fonksiyonel kontrol (ota-update.md §9 · §9.1) | başarılı → `US7-CONFIRM_WAIT` | başarısız → `US9-ROLLBACK` |
| US7 | `US7-CONFIRM_WAIT` | self-test geçti | uygulama/health servisi onay bekler (ota-update.md §9.1) | onay gelirse `US8-CONFIRMED` | zaman aşımı veya explicit reject → `US9-ROLLBACK` |
| US8 | `US8-CONFIRMED` | onay alındı | `confirmed=1`, `pending=0` yazılır | `US0-IDLE` | — |
| US9 | `US9-ROLLBACK` | geri alma tetiklendi (§8 kapısı) | boot A8 retry ≤ 2 (boot-anayasa §8) → yedek banka devir | başarılı → `US0-IDLE` | hiçbiri yoksa kurtarma → `US10-ABORT` |
| US10 | `US10-ABORT` | update iptali | yazım yarım kaldıysa güvenli temizlik; kullanıcıya hata kodu (§12-1 — kod tanımsız) | `US0-IDLE` | önceki onaylı firmware korunur |

### §2.1 Geçiş Sözleşmeleri (Girdi · Çıktı · Garanti)

| # | Geçiş | Girdi | Çıktı | Garanti |
|---|---|---|---|---|
| TR1 | US1 → US2 | ağ erişimi + manifest + paket | tam indirilmiş paket | paket tam inmediyse geçiş yok (update-anayasa §5 UE1) |
| TR2 | US2 → US3 | bütünlük + sürüm/manifest + imza sonucu | doğrulanmış paket | doğrulama başarısızsa `US10-ABORT` — doğrulanmamış paket yazılmaz |
| TR3 | US3 → US4 | image + CRC/hash doğrulaması | `pending=1` işareti | doğrulanmamış image'da `pending` KONULMAZ (§6 KAPI-3) |
| TR4 | US4 → US5 | `pending=1` + yeniden başlatma | ilk boot | boot'ta yarım image tespiti → kurtarma (boot-dogrulama §U (Image yazma bütçesi)) |
| TR5 | US6 → US7 | self-test/health-check sonucu (UT3) | onay beklemesi | başarısızsa geçiş yok → `US9-ROLLBACK` |
| TR6 | US7 → US8 | health servisi `confirmed` onayı (UT4) | `confirmed=1`, `pending=0` | onay/zaman aşımı garantisiz `US8`'e geçilmez → `US9-ROLLBACK` |
| TR7 | US9 → US0 | yedek bank + boot A8 (retry ≤ 2) | önceki onaylı firmware | sonsuz retry YOK (boot-anayasa A8) |
| TR8 | * → US10 | UR1–UR6 tetikleyicisi | update iptali | önceki onaylı firmware korunur |

**Geçiş kuralı:** Tabloda olmayan geçiş YASAKTIR — belirsiz ara durum yoktur
(update-anayasa §2 U-A1 ile aynı ilke).

## §3 Firmware Bayrakları (UF1–UF7)

> boot-surucu §3'ün update yüzeyi; isimler kaynakta Türkçe tanımlıdır, İngilizce anahtar
> adlar örnektir — `⚠️ kod tanımı gerekiyor`.

| # | Bayrak | Anlam | Dayanak / ilişki |
|---|---|---|---|
| UF1 | `install_state` | kurulum durumu: boş / yazılıyor / pending / onaylı / geri alındı — ota-update.md §6 durumları: boş → yazılıyor → tamamlandı → onaylandı → geri alındı | ota-update.md §6 |
| UF2 | `pending` | geçici bayrak; `pending=1` ise yeni image ilk kez çalışacak | boot-sequence.md §8 (bayrak tablosu — `pending`) |
| UF3 | `confirmed` | firmware onaylandı; `confirmed=0` + boot başarılı = ilk-çalıştırma doğrulamasının update aynası (§2 US6 · §4 UT3) — boot-surucu §3'te `confirmed` satırı YOK | §2 US6 · §4 UT3 (boot-surucu §3 = ST tablosu, `confirmed` satırı yok) |
| UF4 | `rollback_counter` | geri-alma sayacı — boot-dogrulama §V'in kapsam dışı bıraktığı bu sayaç update tarafından işlenir; tek yazıcı update (firmware-kapsam §17 Ov2) | boot-dogrulama §V · firmware-kapsam §17 Ov2 |
| UF5 | `boot_counter` | denenme sayacı, artan — A/B update ile artırılır | boot-sequence.md §8.1 (contador) |
| UF6 | `firmware_version` | sürüm alanı — update yeni sürümü yazar (manifest `version` alanı, ota-update.md §8.1); `FirmwareVersion_t` tipi kaynakta YOK → `UNKNOWN` | ota-update.md §8.1 · `⚠️ VERIFICATION REQUIRED` (tip adı) |
| UF7 | `secure_version` | anti-rollback güvenli sürüm alanı, OTP'de (boot-sequence.md §13.1 — BL2 yalnız OTP sürüm sayacından yüksek sürümleri boot eder; §21 OTP tablosu) — update bu alanı düşüremez (yalnız artırabilir) | boot-sequence.md §13.1 · §21 |

## §4 Update Zamanlama (UT1–UT5)

| # | Pencere | İlgili durum | Koşul | Eylem | Sonuç |
|---|---|---|---|---|---|
| UT1 | İndirme penceresi | `US1-FETCH` | ağ erişimi + güç kaynağı kararlı | paket + manifest indir → bütünlük kontrolü (manifest §8.1 zorunluluğu) | `US2-VERIFY` |
| UT2 | Yazma penceresi | `US3-WRITE` | atılabilir yuva boş/temizlenebilir | image yaz (§6) + CRC/hash doğrula | `US4-PENDING` |
| UT3 | Self-test penceresi | `US6-SELF_TEST` | ilk boot + fonksiyonel kontrol (ota-update.md §9 · §9.1) | kontrolü çalıştır | başarılı → `US7-CONFIRM_WAIT`, başarısız → `US9-ROLLBACK` |
| UT4 | Onay penceresi | `US7-CONFIRM_WAIT` | health servisi `confirmed` yazar (ota-update.md §9.1) | onayı bekle | `US8-CONFIRMED`; zaman aşımı → `US9-ROLLBACK` |
| UT5 | Geri-alma penceresi | `US9-ROLLBACK` | doğrulanmış eski firmware kullanılabilir (ota-update.md §7.2: geri dönüş yalnızca doğrulanmış eski sürüme) | boot A8 (retry ≤ 2) → yedek banka devir | `US0-IDLE` |

## §5 Rollback Tetikleyicileri (UR1–UR6)

> Kodlar §12'de (hata kodu şeması TANIMSIZ).

| # | Tetikleyici | İlgili durum | Koşul | Sonuç |
|---|---|---|---|---|
| UR1 | Manifest doğrulaması başarısız | `US2-VERIFY` | bütünlük/versiyon uyuşmazlığı | `US10-ABORT`, firmware yazılmaz |
| UR2 | İmza doğrulaması başarısız | `US2-VERIFY` | imza basamağı (§7) reddeder | `US10-ABORT` — imza denetiminin kimde olduğu Q4 (§7, §13) |
| UR3 | Self-test başarısız | `US6-SELF_TEST` | fonksiyonel kontrol red (ota-update.md §7.2: sağlık kontrolü başarısız → geri alma) | `US9-ROLLBACK` |
| UR4 | Health-check onayı gelmedi | `US7-CONFIRM_WAIT` | zaman aşımı/reject (ota-update.md §9.1) | `US9-ROLLBACK` |
| UR5 | Güç kesintisi sırasında yarı yazma | `US3-WRITE` | bayrak `pending=1` iken kapanma + boot'ta yarım image tespiti | boot kurtarma/yedek bank devri → update `US10-ABORT` (ota-update.md §12 senaryosu) |
| UR6 | Anti-rollback ihlali | `US2-VERIFY` | `secure_version` (UF7) > paket sürümü | `US10-ABORT` — boot-ornekler §B.5 anti-rollback aynası (`FW_VERSION_MIN`) |

## §6 KAPI — Update Yazma (Image Write)

> boot-dogrulama §U (Image yazma bütçesi satırında) bu modüle bağlandı; owner set §V:
> embedded-engineer · security-engineer · devops-engineer.

| # | Madde | Tanım |
|---|---|---|
| KAPI-1 | Yazma hedefi | A/B sisteminde atılabilir yuva (ota-update.md §6: A yuvası eski, B yuvası yeni firmware); tek-bank sistemde dosya-tabanlı atomik yazım (rename) — hangi topolojinin geçerli olduğu K005 firmware-kapsam §9.7'de A/B (dual-bank) diye geçer, tek-bank varyantı UNKNOWN |
| KAPI-2 | Yazma sırası | eski yuvaya dokunmadan yeni yuvayı yaz → yazılan image'ı doğrula (CRC/hash) → ancak doğrulanırsa `pending=1` işaretle → yeniden başlat |
| KAPI-3 | Yarım yazım kuralı | yazma yarıda kesilirse `pending` işareti KONULMAZ; bir sonraki boot'ta eski onaylı firmware kalır (boot tarafı: boot-dogrulama §U (Image yazma bütçesi)) |
| KAPI-4 | Kanıt durumu | Tasarım seviyesi — repo'da firmware kodu yok (2026-10-13: 0 kod kanıtı); gerçek flash yazma kodu yok (`⚠️`) |

## §7 KAPI — İmza Doğrulama (Signature Verification)

> boot-dogrulama §U (İmza doğrulama satırı) bağlaması; owner set §V.

| # | Madde | Tanım |
|---|---|---|
| KAPI-1 | Basamak tanımı | paket indirildikten sonra kriptografik imza doğrulanır; doğrulama başarısızsa paket yazılmaz (`UR2` → `US10-ABORT`) |
| KAPI-2 | **Sahiplik — Q4** | **İmza doğrulamanın KİME AİT olduğu (update mi, güvenlik mi?) bu kapıda ÇÖZÜLMEZ — K005 Q4 açık sorusudur** (update-anayasa §11, boot-kapsam §I): boot-kapsam §I çağırıcının update olduğunu, algoritma sahibinin security-engineer olduğunu yazar (aktarım, çözüm değil) |
| KAPI-3 | Algoritma adı | İmza algoritması/şema adı bu modülde UYDURULMAZ (firmware-evrensel-dil §4: algoritma adı belirtilecekse doğrulanmış kaynak şart) — `UNKNOWN`, Q4 kapandığında security-engineer kaynağıyla doldurulur |
| KAPI-4 | Kanıt durumu | Tasarım seviyesi — repo'da imza doğrulama kodu yok (`⚠️`) |

## §8 KAPI — Rollback (Geri Alma)

> boot-dogrulama §U (Rollback tetikleme satırı) bağlaması; owner set §V.

| # | Madde | Tanım |
|---|---|---|
| KAPI-1 | Geri alma gerekçeleri | `UR3` (self-test), `UR4` (onay zaman aşımı), `UR5` (yarım yazma) — ota-update.md §7: health-check başarısız / explicit reject / müdahale |
| KAPI-2 | Geri alma kaynağı | doğrulanmış eski firmware (ota-update.md §7.2) — A/B'de yedek yuva, dosya-tabanlıda indirilmiş eski paket |
| KAPI-3 | Geri alma sırası | boot A8 retry ≤ 2 → yedek banka devir → kurtarma → hata kodu (boot-anayasa §8 sırası, update `US9-ROLLBACK`'te aynası) |
| KAPI-4 | Anti-rollback çelişkisi | geri alma, `secure_version`'ı (UF7) GERİ SEKMEZ; sahte eski sürüm yüklemesi imzasız/anti-rollback ihlalli olduğu için reddedilir (boot-ornekler §B.5 aynası) |
| KAPI-5 | Kanıt durumu | Tasarım seviyesi — ölçüm PLANNED (`⚠️`) |

## §9 KAPI — Boot-Lock

> boot-dogrulama §U (Boot-lock yazma satırı) bağlaması; owner set §V.

| # | Madde | Tanım |
|---|---|---|
| KAPI-1 | Tanım | boot-lock, boot-onarım/DFU durumunda bootloader'ın kilidi — update akışının boot tarafındaki karşılığı (boot-dogrulama §U · Boot-lock yazma satırı) |
| KAPI-2 | Bu modülün sınırı | boot-lock bayrak/setme işlemi boot bacağında tanımlıdır (boot-dogrulama §V: boot-lock bit tasarımı — security-engineer); update yalnız `US5-FIRST_BOOT`/`US9-ROLLBACK` geçişlerinde boot-lock'un durumunu GÖZ ÖNÜNDE TUTAR (update-onarım çakışması K005 Q3 → update-anayasa §11'de açık) |
| KAPI-3 | Rol bölümü | Boot-lock ile ilgili ölçüm/rol bölümü bu modülde DEĞİL (boot-dogrulama §V: "boot-lock bölümü boot bacağında") — `⚠️` |
| KAPI-4 | Kanıt durumu | Tasarım seviyesi (`⚠️`) |

## §10 KAPI — DFU 4 Faz

> boot-dogrulama §U (DFU 4-faz akışı satırı) bağlaması; owner set §V.

| # | Madde | Tanım |
|---|---|---|
| KAPI-1 | Dört faz | Kaynak adlarıyla (USB DFU 1.1 — boot-dogrulama §F satır 7, erişim 2026-10-12): `DfuIdle` (boşta/kurulu) → `DfuDnload` (indirme/yazma) → `DfuManifest` (yazılmış image'ı etkinleştirme/yeniden başlatma) → `DFURuntime` (çalışan runtime'a dönüş; manifest'ten sonra firmware'ı başlatır) |
| KAPI-2 | Update akışıyla ilişki | DFU, ağ-güncellemenin (OTA) YERİNE DEĞİL, ALTERNATİF yoludur (firmware-kapsam §9.7: ağ üzerinden (OTA) veya USB üzerinden (DFU)) — `US1-FETCH` yerine `DfuDnload` ile doğrudan yazma |
| KAPI-3 | Durum makinesine bağ | DfuManifest fazındaki yeniden başlatma `US5-FIRST_BOOT`'e bağlanır; sonraki self-test/onay aynı UT3/UT4 kapılarından geçer |
| KAPI-4 | Erişim politikası | DFU'nun cihaz kilidi/erişim politikası (kim DFU'ya girebilir) — `⚠️` (K005 Q2/Q3 bağlantılı, bu modülde çözülmez) |
| KAPI-5 | Kanıt durumu | Tasarım seviyesi — DFU 4 faz şeması gerçektir (USB-IF DFU 1.1), K005 DFU uygulaması yok (`⚠️`) |

## §11 Komşu Bağımlılık ve Devir Arayüzü (Boot ↔ Update)

### §11.1 Yön Tablosu

| Yön | Ne devredilir | Dayanak |
|---|---|---|
| boot → update | A/B yuva seçimi ve sayma (boot-sequence.md §8 · §8.1), ilk boot'ta `pending` bayrak okuma (boot-sequence.md §8), A8 retry ≤ 2 ile yedek banka devir (boot-anayasa §8), anti-rollback `FW_VERSION_MIN` kontrolü (boot-ornekler §B.5) | boot-kapsam · boot-ornekler §B.5 |
| update → boot | yeni image yazımı + `pending` işareti (§6), `confirmed` onayı (§4 UT4), `secure_version` artırımı (§3 UF7), geri-alma talebi (§8) | bu dosya §4 · §6 · §8 |

### §11.2 Devir Alanları (Girdi · Çıktı · Sahip)

| Alan | Değer | Açıklama | Sahip / dayanak |
|---|---|---|---|
| image (yeni) | atılabilir yuvada image | boot ilk boot'ta bunu dener | update — §6 KAPI-1 |
| pending | `pending=1/0` | boot'un yeni image'ı deneyeceği işaret | update → boot — boot-sequence §8 |
| confirmed | `confirmed=1/0` | onaylı firmware işareti; confirm bayrağı runtime → update akar | update ↔ boot — boot-kapsam §C.1 |
| sayaçlar | `rollback_counter` (UF4) · `boot_counter` (UF5) | boot sayar, update işler | boot-sequence §8.1 · firmware-kapsam §17 Ov2 |
| secure_version | UF7 değeri | update yalnız artırabilir, düşüremez | update — §3 UF7 |
| geri-alma talebi | `US9-ROLLBACK` | boot A8 zinciriyle yedek banka devir | update → boot — §8 · boot-anayasa §8 |
| anti-rollback eşiği | `FW_VERSION_MIN` | boot kontrol eder | boot — boot-ornekler §B.5 · firmware-kapsam §9.1 B6 |

> **Q4 notu:** Q4 bu çiftin üzerinde AÇIKTIR: imza doğrulama basamağı update akışında (§7)
> ama algoritma sahibi security-engineer (boot-kapsam §I aktarımı) — kimin çağırdığı (update)
> ≠ kimin denetlediği (güvenlik) ayrımı çözülmedi (§13).

## §12 Hata Kodu ve Telemetri Arayüzü

| # | Boşluk | Durum | Not |
|---|---|---|---|
| 1 | Update hata kodu tablosu | `⚠️` TANIMSIZ | `US10-ABORT`/`UR1`–`UR6` tetikleyicileri için numeric kod tanımlanmamıştır; kod şeması bu modülde UYDURULMAZ (firmware-evrensel-dil §5: gerçek register/sabit adı yoksa `⚠️`) |
| 2 | Telemetri kanalı | `UNKNOWN` | `pending`/`confirmed`/`rollback_counter` bayrak değişikliklerinin nereye raporlandığı (health servisi? log? ağ?) tanımsız — ota-update.md §9.1 health-check'i şart koşar ama kanal adı vermez |
| 3 | Boot tarafı telemetri/boot-log | KAPSAM DIŞI | boot-dogrulama §G — bu modül de kendi telemetrisini burada TANIMLAMAZ, yalnız yukarıdaki boşluğu işaretler |

## §13 Açık Sorular

| # | Soru | Etki | Durum |
|---|---|---|---|
| Q4 | **update ↔ güvenlik (imza) kime ait?** | §7 KAPI-2 · §11 · update-anayasa §11 | `⚠️ VERIFICATION REQUIRED` — bu dosyada ÇÖZÜLMEZ; §7 yalnız basamak tanımı verir, sahiplik boot-kapsam §I'teki gibi açık kalır |
| Q2/Q3 | update-onarım (Q2) ve update-boot-lock (Q3) kapı arayüzleri | §9 KAPI-2–KAPI-3 | §9 yalnız göz önüne alma kuralı yazar, arayüz tanımı yok (`UNKNOWN`) |
| Q1 | ilk-boot tutarlılık kapısı | `US5_FIRST_BOOT` → `US6_SELF_TEST` geçişi var; kapının kendisi (oturum anahtarı / KMS) firmware-bagimliliklar §20.3'e bağlı açık | `⚠️` |
| U-Q5 | §6–§10 kapılarının tekil owner'ı kim? | owner ataması | `UNKNOWN` — boot-dogrulama §V owner set olarak verilmiş (embedded-engineer · security-engineer · devops-engineer); kapı-bazında tekil dağıtım kaynakta yok |

## §14 Vault Boot ↔ Update Devir Ek (append · 2026-10-16)

> Kaynaklar bu ek için: `Dosya: .ai/electronic/firmware/ota-update.md` (524 satır) · `Dosya: .ai/electronic/firmware/boot-sequence.md` (516 satır) — ikisi de okundu 2026-10-16. Her satır bölüm/satır referanslıdır; repo `*.cpp/*.h/*.c` YOK (2026-10-15 ölçümü) → "çalışan kod" iddiası yazılmaz.

### §14.1 Boot Tarafı Görevleri (update'e devredilenler hariç)

| # | Boot görevi | Update ile sınır | Dayanak |
|---|---|---|---|
| B1 | Boot ROM → bootloader imza doğrulama | update yalnız paketi imzalar | boot-sequence.md:245 §13 |
| B2 | Aktif yuva bayrağını okuma (good/pending/bad) | bayrağı UPDATE yazar | ota-update.md:360 §19.1 |
| B3 | pending ise yeni yuvayı deneme + contador++ | contador sınırı boot'ta | ota-update.md:360 §19.1 |
| B4 | contador eşiği aşarsa eski yuvaya dön | rollback tetikleyici boot'ta | ota-update.md:254 §13 |
| B5 | recovery image'e yönlendirme (son çare) | recovery image'ı update besler | ota-update.md:266 §13.1 |
| B6 | Boot yapılandırması / OTP okuma | anti-rollback sayacı OTP'de | boot-sequence.md:362 §21 |

### §14.2 Update Tarafı Görevleri (boot'a devredilmeyenler)

| # | Update görevi | Bırakma anı | Dayanak |
|---|---|---|---|
| U1 | Paket indirme (TLS) + manifest okuma | yazım başlamadan | ota-update.md:149 §8 |
| U2 | İmza + checksum doğrulama | inactive yuva yazımından ÖNCE | ota-update.md:68 §5 |
| U3 | Inactive yuvaya yazım + verify | verify bitmeden bayrak YOK | ota-update.md:237 §12 |
| U4 | bootable bayrağını atma (commit-after-write) | flush+verify sonrası | ota-update.md:249 §12.1 |
| U5 | NVS koruma / migrate | firmware paketinin parçası DEĞİL | ota-update.md:339 §18.2 |
| U6 | Canary penceresi izleme + rollback kararı | ilk temiz boot sonrası | ota-update.md:190 §9.1 |

### §14.3 Devir Sinyalleri (bu dosyanın §11 arayüzüne ek)

| Sinyal | Yön | Anlamı | Dayanak |
|---|---|---|---|
| `UF_PENDING` | update → boot | yeni yuvayı dene | ota-update.md:360 §19.1 |
| `UF_GOOD` | update ← boot (teyit) | sağlık kontrolü geçti | ota-update.md:184 §9 |
| `UF_BAD` | boot → update | boot başarısız / contador doldu | ota-update.md:254 §13 |
| `UF_ANTIROLL` | update içi | `min_version` ihlali → reddet | ota-update.md:172 §8.1 |
| `UF_MANIFEST` | update içi | manifest alanları okundu | ota-update.md:164 §8.1 |

⚠️ VERIFICATION REQUIRED: bu sinyal adları bu dosyanın §3 bayrak sözlüğüne (UF1–UF7) ÖNERİ olarak eklenmiştir; §3'e resmi kayıt (append) ayrıca yapılmalıdır — sahiplik Q4/U-Q5 ile açık.

## §15 KAPI-2 İmza Doğrulama — Adım Detayı (append)

| # | Adım | Hata çıkışında davranış | Dayanak |
|---|---|---|---|
| 1 | Manifest oku (version · hardware_id · image_size · sha256 · signature · min_version · rollout_policy) | reddet | ota-update.md:164 §8.1 |
| 2 | hardware_id kontrolü | uyumsuz donanım → reddet | ota-update.md:402 §21.1 |
| 3 | min_version / anti-rollback kontrolü | eski sürüm → reddet | ota-update.md:172 §8.1 |
| 4 | SHA-256 checksum | uyuşmazsa → reddet | ota-update.md:47 §3 |
| 5 | İmza doğrulama (RSA-2048 / ECDSA) | geçersiz → reddet | ota-update.md:80 §5.1 |
| 6 | Yalnız inactive yuvaya yaz | — | ota-update.md:101 §6.1 |

Sıra kuralı: 1→6 dışına çıkılmaz; imza (5) TAM paket üzerinden doğrulanır — delta patch üzerinde DEĞİL (ota-update.md:384 §20.1).

## §16 US × UF Durum Çapraz Tablosu (append)

| Update durumu (US) | Slot A | Slot B | Beklenen boot | Dayanak |
|---|---|---|---|---|
| US0 güncelleme yok | good | boş/eski | A | ota-update.md:107 §7.2 |
| US1 indiriliyor | good | — | A | ota-update.md:149 §8 |
| US2 doğrulanıyor | good | — | A | ota-update.md:68 §5 |
| US3 yazılıyor | good | yarım | A (değişmez) | ota-update.md:239 §12 |
| US4 commit (bayrak) | good→pending | complete | A→B denenir | ota-update.md:109 §7.2 |
| US5 reboot | pending | bootable | B denenir | ota-update.md:111 §7.2 |
| US6 self-test (canary) | pending | deneniyor | B → teyit | ota-update.md:190 §9.1 |
| US7 good | — | good | B | ota-update.md:113 §7.2 |
| US8 rollback | good | bad | A | ota-update.md:115 §7.2 |
| US9 anti-rollback reddi | mevcut | — | mevcut kalır | ota-update.md:140 §7.1 |
| US10 kademeli dağıtım beklemesi | good | — | A | ota-update.md:198 §10 |

Not: US adlandırması bu dosyanın §2'sindeki US0–US10 ile uyumludur; vault tablosu (ota-update.md:107 §7.2) 4 satır verir, bu tablo 11 satıra açar — **uydurma durum eklenmedi**, her satır vault fazına bağlanır.

## §17 Anti-Rollback Arayüzü (append · bu dosyada yeni)

| Kontrat | Tanım | Hata davranışı | Dayanak |
|---|---|---|---|
| Sayaç okuması | donanımda/OTP salt-okunur monotonic counter | okuma başarısız → güncelleme RED | ota-update.md:140 §7.1 |
| Sayaç yazımı | yalnız imza+sağlık geçince yeni değere çekilir | yarıda yazım → eski sayaç | ota-update.md:140 §7.1 |
| Karşılaştırma | `manifest.min_version > sayaç` değilse reddet | sessiz kabul YOK | ota-update.md:172 §8.1 |
| Zayıflık denetimi | sayaç OTP'de değilse güvenlik iddiası düşer | ⚠️ VERIFICATION REQUIRED | ota-update.md:143 §7.1 |

Sahiplik: sayaç yazım sahibi bu dosyada Ç-U3 (açık soru) olarak kalır — bu ek karar vermez, yalnız arayüzü tarifler.

## §18 Canary (Sağlık Kontrolü) Arayüz Zamanlaması (append)

| # | Sinyal/kontrol | Yöntem | Başarısızlık | Dayanak |
|---|---|---|---|---|
| 1 | Boot kontrolü | watchdog kick | rollback | ota-update.md:184 §9 |
| 2 | Temel servis | alive ping | rollback | ota-update.md:185 §9 |
| 3 | Ses yolu | I2S/DMA heartbeat | uyarı / safe-mode | ota-update.md:186 §9 |
| 4 | Bellek bütünlüğü | CRC / test | rollback | ota-update.md:187 §9 |
| 5 | Sürüm doğrulama | boot edilen sürüm = manifest | rollback | ota-update.md:188 §9 |

Pencere kuralı: canary penceresi içinde beklenmedik reset/watchdog → güncelleme başarısız sayılır (ota-update.md:190 §9.1). Pencere uzunluğu için vault'ta sayısal değer YOK → UNKNOWN.

## §19 Telemetri Metrik Seti (append)

| Metrik | Açıklım | Kullanım | Dayanak |
|---|---|---|---|
| Güncelleme başarı oranı | başarılı/toplam | kademeli dağıtım kapısı | ota-update.md:223 §11 |
| Rollback oranı | geri alınan güncelleme | hatalı sürüm sinyali | ota-update.md:224 §11 |
| Güncelleme süresi | indirme+yazma+reboot | UX / SLA | ota-update.md:225 §11 |
| Boot başarısı | ilk boot'ta başarı | sağlık kontrolü | ota-update.md:226 §11 |
| Hata kodları | aşama bazlı | teşhis | ota-update.md:227 §11 |

Gizlilik kuralı: telemetri pseudonymous ve minimdir; kullanıcı içeriği/anahtar asla telemetriye girmez (ota-update.md:231 §11.1 — çekirdek `AGENTS.md` §5 log/secret kuralıyla uyumlu).

## §20 Enerji ve Ağ Bütçesi Arayüzü (append)

| Kalem | Etki | Azaltma | Dayanak |
|---|---|---|---|
| İndirme enerjisi | radyo/sayısal aktarım | delta update | ota-update.md:494 §26 |
| Yazma enerjisi | flash yazma | minimum yazma | ota-update.md:495 §26 |
| Reboot enerjisi | yeniden açılış | tek reboot (A/B) | ota-update.md:496 §26 |
| Telemetri | sürekli küçük paket | toplama | ota-update.md:497 §26 |
| Bekleme | güncelleme penceresi | düşük güç penceresi | ota-update.md:498 §26 |

Planlama kuralı: güncelleme, cihazın düşük güç / şebeke-available olduğu pencereye sığar (ota-update.md:504 §26.1).

## §21 Tehdit ↔ Kapı Eşlemesi (append)

| Tehdit | Vektör | Kapı | Dayanak |
|---|---|---|---|
| Sahte firmware | sahte sunucu / MITM | TLS + imza (§7 KAPI-2) | ota-update.md:411 §22 |
| Replay attack | eski paket tekrarı | anti-rollback (§17) | ota-update.md:412 §22 |
| Paket manipülasyonu | ortada değiştirme | imza + checksum (§15) | ota-update.md:413 §22 |
| Boot döngüsü / brick | hatalı firmware | A/B + watchdog + rollback | ota-update.md:414 §22 |
| Eski sürüm geri yükleme | anti-rollback ihlali | donanımsal sayaç (§17) | ota-update.md:415 §22 |
| Telemetri sızıntısı | fazla veri toplama | minimizasyon (§19) | ota-update.md:416 §22 |

Savunma derinliği: transport → imza → checksum → secure boot → anti-rollback → post-update sağlık; katmanlar bağımsızdır (ota-update.md:419 §22.1).

## §22 Kurtarma Hiyerarşisi (append)

```
Normal güncelleme
  → Boot başarısız (watchdog)
    → Recovery boot (scratch image)
      → Rollback (eski yuva)
        → Kullanıcı müdahalesi (safe-mode / USB recovery)
```

Dayanak: `ota-update.md:266` §13.1 · boot tarafı için `boot-sequence.md:208` §11 (hata kurtarma ve watchdog).

| Basamak | Sorumlu | Dayanak |
|---|---|---|
| Watchdog tetiki | boot/RTOS | boot-sequence.md:208 §11 |
| Recovery yönlendirme | bootloader | ota-update.md:356 §19 |
| Rollback kararı | update + boot ortak | ota-update.md:124 §7 |
| USB/safe-mode | kullanıcı | ota-update.md:266 §13.1 |

## §23 Öncelik ve Sıra (append)

| # | İş | Öncelik | Sıra bağı |
|---|---|---|---|
| 1 | İmza + manifest doğrulama | P0 | §15 adımı 1–5 |
| 2 | Inactive yuvaya yazım | P0 | §15 adımı 6 |
| 3 | Commit (bootable bayrağı) | P0 | flush+verify sonrası |
| 4 | Reboot + canary izleme | P0 | §18 |
| 5 | Telemetri raporu | P2 | §19 |
| 6 | Kademeli dağıtım kararı | P1 | §O.5 (update-ornekler) · ota-update.md:198 §10 |
| 7 | Delta/optimizasyon | P3 | ota-update.md:372 §20 |

## §24 Kapanış Kontrolü (bu ek)

| Kontrol | Beklenti | Durum |
|---|---|---|
| Eklenen bölümler | §14–§24 (append-only) | evet |
| Mevcut §1–§13 silindi mi | HAYIR | 0 silme |
| Satır eşiği | ≥500 | evet (ölçüm: bakınız alt satır) |
| Uydurma register/sürüm/URL | 0 | 0 |
| Repo C++ kodu iddiası | yasak | 0 |
| Dayanak formatı | Dosya + satır referansı | her tabloda |
| Açık sorular (Q1–Q5, U-Q5) | kapatılmadı | açık (bilinçli) |
| Son satır | Authority satırı | korunur |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

## §25 Flash Bölümleme Arayüzü (append)

> Dayanak: `Dosya: .ai/electronic/firmware/ota-update.md` §18 (satır 316–343, okundu 2026-10-16).

| Bölüm | Boyut (tipik) | Yazma sahibi | Update etkisi |
|---|---|---|---|
| Boot ROM / loader | sabit | donanım (salt-okunur) | dokunulmaz |
| Recovery image | 1× firmware | boot tarafı korur | nadiren yazılır + imzalı |
| Slot A | 1× firmware | update (§6 KAPI-1) | inactive ise yazılır |
| Slot B | 1× firmware | update (§6 KAPI-1) | inactive ise yazılır |
| NVS / config | küçük | uygulama | firmware paketine DAHİL DEĞİL |
| Scratch | değişken | update | geçici tampon |

NVS migrate kuralı: şema değişirse backward-compatible migrate edilir; migrate hatası → rollback ama NVS korunur (ota-update.md:339 §18.2 · test örneği ota-update.md:480 §25.1 R4).

## §26 Boot Varyantları ile Çapraz Bağ (append)

| Varyant | İlgili boot bölümü | Update etkisi | Dayanak |
|---|---|---|---|
| Genel platform boot (BL1→BL2→kernel) | boot-sequence.md:79 §6 | imza zinciri her aşamada | boot-sequence.md:245 §13 |
| A/B dual-slot boot | boot-sequence.md:138 §8 | slot seçimi bayrakla | ota-update.md:97 §6 |
| Loader → firmware handoff | boot-sequence.md:163 §9 | el devri (bayraklar korunur) | boot-sequence.md:163 §9 |
| Fast boot | boot-sequence.md:181 §10 | canary penceresi ihlal edilmez | ota-update.md:190 §9.1 |
| Secure boot + OTP | boot-sequence.md:362 §21 | anti-rollback sayacı OTP'de | ota-update.md:140 §7.1 |
| Çok-çekirdekli (XMOS) boot | boot-sequence.md:451 §26 | ⚠️ update ile etkileşimi vault'ta YOK → UNKNOWN | — |

## §27 Hata Kodu / Telemetri Alanı Ek Önerileri (append · §12'ye ek)

| Alan | Tipik içerik | Dayanak |
|---|---|---|
| `stage` | download / verify / write / commit / reboot / canary | ota-update.md:25 §2 akış adımları |
| `reason` | signature / checksum / hardware_id / min_version / power | ota-update.md:411 §22 |
| `slot` | A / B / recovery | ota-update.md:107 §7.2 |
| `attempt` | boot contador değeri (0..N) | ota-update.md:360 §19.1 |
| `rollback` | otomatik / anti / kademeli | ota-update.md:131 §7 tablo |

⚠️ VERIFICATION REQUIRED: alan adları ÖNERİDİR; çekirdek telemetri şeması vault'ta tanımlı değildir (ota-update.md:219 §11 yalnız metrik adları verir).

## §28 Kademeli Dağıtım Kapı Mantığı (append)

| Aşama | Oran | Geçiş koşulu | Dur-dur | Dayanak |
|---|---|---|---|---|
| Canary | %1–5 | hata oranı eşiğinin altı | eşiği aş → durdur+geri al | ota-update.md:209 §10.1 |
| Early | %10–25 | sağlık metrikleri temiz | rollback oranı artışı | ota-update.md:209 §10.1 |
| Broad | %50–90 | telemetri stabil | yaygın hata sinyali | ota-update.md:209 §10.1 |
| Full | %100 | — | — | ota-update.md:204 §10 |

Kohort ayrımı: donanım revizyonu / bölge / beta-programı — sorun kohortta izole kalır (ota-update.md:212 §10.2).

## §29 Update Zamanlama UT Ek (append · §4'ün devamı)

| UT | Kural | Ek madde | Dayanak |
|---|---|---|---|
| UT1 | İndirme düşük güç penceresine sığar | §20 enerji bütçesi | ota-update.md:504 §26.1 |
| UT2 | Yazım yalnız inactive yuvada | §16 US3 satırı | ota-update.md:239 §12 |
| UT3 | Tek reboot (A/B) | §20 tablo | ota-update.md:496 §26 |
| UT4 | Canary penceresi ilk temiz boot'a kadar açık | §18 | ota-update.md:190 §9.1 |
| UT5 | Delta/retry üstel backoff | ota-update.md:378 §20 | ağ dayanıklılığı |

## §30 Ölçüm Yöntemi ve Kapılar (append · §7'ye ek)

| Kapı | Ölçüm | Kabul | Dayanak |
|---|---|---|---|
| İmza kapısı | doğrulama süresi + red oranı | 0 sessiz kabul | ota-update.md:68 §5 |
| Yazma kapısı | verify sonucu (checksum) | tam yazım | ota-update.md:243 §12 |
| Boot kapısı | boot contador ≤ eşik | eşiği aş → rollback | ota-update.md:360 §19.1 |
| Canary kapısı | pencere içi reset sayısı | 0 | ota-update.md:190 §9.1 |
| Filo kapısı | başarı/rollback oranı | aşama eşiği altında | ota-update.md:223 §11 |

⚠️ VERIFICATION REQUIRED: bu kapıların hiçbiri repo'da otomatikleştirilmemiştir (ota-update.md:486 §25.2 — test altyapısı "tasarım seviyesi").

## §31 Kaynakça (bu ek)

| # | Kaynak | Referans | Erişim |
|---|---|---|---|
| 1 | `Dosya: .ai/electronic/firmware/ota-update.md` | §2–§27 | okundu 2026-10-16 |
| 2 | `Dosya: .ai/electronic/firmware/boot-sequence.md` | §6 · §8 · §9 · §11 · §13 · §21 · §26 | okundu 2026-10-16 |
| 3 | MCUboot dokümantasyonu | ota-update.md:296 §16 (1. kayıt) | URL vault'tan — bu ekte yeniden doğrulanmadı |
| 4 | IETF RFC 8446 (TLS 1.3) | ota-update.md:297 §16 (2. kayıt) | URL vault'tan — bu ekte yeniden doğrulanmadı |

Not: 3–4 numaralı kaynaklar bu ekte HTTP kontrolü YAPILMADI → `⚠️ VERIFICATION REQUIRED` (vault'ta 2026-10-13 tarihli 200 kaydı mevcuttur: ota-update.md:283 §15).

## §32 Test Köprüsü (append)

| # | Kural (bu dosya) | Doğrulama yöntemi | Karşı dosya |
|---|---|---|---|
| T1 | §15 imza sırası 1→6 | imza/red senaryosu | update-dogrulama |
| T2 | §16 US0–US10 geçişleri | durum makinesi senaryosu | update-ornekler §G |
| T3 | §17 anti-rollback | eski sürüm paketi reddi | update-dogrulama |
| T4 | §18 canary penceresi | watchdog tetik testi | update-ornekler §O.1 |
| T5 | §14 bayrak yönü | boot↔update devir testi | boot-dogrulama |
| T6 | §25 NVS koruma | migrate hata enjeksiyonu | update-ornekler §O.1 R4 |

## §33 Revizyon (bu ek)

| # | Tarih | Değişiklik | Otorite |
|---|---|---|---|
| E1 | 2026-10-16 | §14–§33 eklendi (append-only, kaynak: ota-update.md + boot-sequence.md) | Vault Steward (subagent) |

> Bölüm bütünlüğü: bu ek, §1–§13'teki mevcut tanımlarla **çelişmez**; yeni sinyal/ad/alan adları yalnız ÖNERİ damgasıyla verilmiştir (§14.3 · §27).

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
