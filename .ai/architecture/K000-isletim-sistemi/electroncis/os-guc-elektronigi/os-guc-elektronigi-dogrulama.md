---
node_id: K000-isletim-sistemi/electroncis/os-guc-elektronigi/os-guc-elektronigi-dogrulama
node_type: module
status: draft
parent_id: K000-isletim-sistemi/electroncis/os-guc-elektronigi
children: []
depends_on: [K002-electroncis, K001-donanım, K000-isletim-sistemi/isletim-sistemi-kernel, K000-isletim-sistemi/os-donanim-tespiti]
evidence:
  - "https://docs.kernel.org/power/index.html (200 OK · 2026-10-14)"
  - "https://docs.kernel.org/power/runtime_pm.html (200 OK · 2026-10-14)"
  - "https://docs.kernel.org/power/basic-pm-debugging.html (200 OK · 2026-10-14)"
  - "https://docs.kernel.org/driver-api/thermal/index.html (200 OK · 2026-10-14)"
  - "https://docs.kernel.org/hwmon/index.html (200 OK · 2026-10-14)"
  - "https://docs.kernel.org/firmware-guide/acpi/index.html (200 OK · 2026-10-14)"
  - "https://docs.kernel.org/driver-api/basics.html (200 OK · 2026-10-14)"
  - "https://learn.microsoft.com/en-us/windows-hardware/drivers/kernel/ (200 OK · 2026-10-14)"
  - "C:\www\coremusic.net\.ai\electronic\firmware\rtos.md (541 satır · salt-okunur atıf)"
validation: "2026-10-14 · yazıldı (Vault Steward Q1 kararı) · web: 8×200 kanıt · 404 kayıtları §1.2 · şema regex §6 · red-team bekliyor · 2026-10-14 · genişletme (Faz C)"
open_questions:
  - "⚠️ VERIFICATION REQUIRED: 200 OK yalnız canlılık kanıtıdır; sayfa içerik derinliği (paragraf madde madde) okunmadı."
  - "⚠️ VERIFICATION REQUIRED: PS_ON/PWR_OK eşik voltajları ve termal trip sayıları bu dosyada YOKTUR (K002/donanım işi) — tekrar ölçümde de uydurulmaz."
  - "⚠️ VERIFICATION REQUIRED: anayasa frontmatter evidence'ındaki `docs.kernel.org/thermal/index.html (200 OK)` satırı 2026-10-14 ölçümünde 404 vermiştir — çelişki §2'de kayıtlı; frontmatter düzeltmesi üst karardır (yalnız validation satırına ekleme yetkisi)."
group: kanit
---

# Doğrulama — os-guc-elektronigi (OS↔güç/termal elektronik arayüzü)

> **`electroncis` yazım hatası dokunulmaz** (v2 §3 KURAL 1).

## §0 ELI15

Bu dosya, `os-guc-elektronigi` alt dalının **iddialarını kanıta** bağlar: hangi
URL'ler canlı (200), hangileri ölü (404 ve asla kanıt yazılmaz), anayasa §6.3'ün
havale ettiği **thermal URL düzeltmesi** nerede tutuluyor (§2), anayasa §11'in
havale ettiği **MS 404 kaydı** nerede tutuluyor (§1.2), repo'da C/C++ hâlâ yok mu
(dürüstlük), şema 10 alanı sağlıyor mu ve anayasa↔örnekler↔bu dosya birbirine
çelişiyor mu. Üst düğüm `electroncis-dogrulama.md` **tek genel kayıt**tır; burası
alt-dala özgü **ölçüm detayı**dır (tekrarlanan genel kayıtlar imha edilir, yalnız
alt-dala özgü satırlar kalır).

## §1 Web doğrulama matrisi (2026-10-14)

### §1.1 Yaşayan (200 OK) — bu alt-dala özgü

| # | URL | Ne doğrular | Başlık kanıtı |
|---|---|---|---|
| 1 | `https://docs.kernel.org/power/index.html` | anayasa §2 ACPI güç yönetimi çerçevesi | "Power Management" ✓ |
| 2 | `https://docs.kernel.org/power/runtime_pm.html` | anayasa §5 runtime PM | ⚠️ başlık okunmadı (yalnız 200) |
| 3 | `https://docs.kernel.org/power/basic-pm-debugging.html` | anayasa §12 loglama/denetim | "Debugging hibernation and suspend" ✓ |
| 4 | `https://docs.kernel.org/driver-api/thermal/index.html` | anayasa §6 termal zincir (düzeltilmiş adres — §2) | "Thermal" ✓ |
| 5 | `https://docs.kernel.org/hwmon/index.html` | anayasa §6.1 sensör okuma yüzeyi | "Hardware Monitoring" ✓ |
| 6 | `https://docs.kernel.org/firmware-guide/acpi/index.html` | anayasa §13 firmware↔K005 sınırı | ⚠️ başlık okunmadı (yalnız 200) |
| 7 | `https://docs.kernel.org/driver-api/basics.html` | anayasa §11 workqueue/kuyruk kavramı | ⚠️ başlık okunmadı (yalnız 200) |
| 8 | `https://docs.kernel.org/driver-api/acpi/index.html` | anayasa §13 ACPI↔driver yüzeyi | ⚠️ başlık okunmadı (yalnız 200) |
| 9 | `https://learn.microsoft.com/en-us/windows-hardware/drivers/kernel/` | anayasa §11.1 Windows genel uç | ⚠️ başlık okunmadı (yalnız 200) |
| 10 | `https://learn.microsoft.com/en-us/windows-hardware/drivers/kernel/introduction-to-dpcs` | anayasa §11.1 DPC kavramı (kısmi Windows kanıtı) | "Introduction to DPCs" ✓ |
| 11 | `https://learn.microsoft.com/en-us/windows-hardware/drivers/kernel/device-power-states` | anayasa §2 Windows D-state karşılığı | "Device Power States" ✓ |
| 12 | `https://learn.microsoft.com/en-us/windows-hardware/drivers/kernel/system-power-states` | anayasa §2 Windows S-state karşılığı | "System Power States" ✓ |
| 13 | `https://learn.microsoft.com/en-us/windows-hardware/drivers/kernel/interrupt-service-routines` | anayasa §11.1 ISR kavramı | "Interrupt Service Routines" ✓ |

### §1.2 Ölü (404 · KULLANMA) — MS 404 kaydı + thermal

> **MS 404 kaydı (anayasa §11 havalesi — bu dosya §1.2'de tutulur):**
> anayasa §11'in "`⚠️ VERIFICATION REQUIRED` (MS alt sayfaları 404)" ifadesinin
> ölçüm kaydı aşağıdadır. Dürüstlük notu: bu ifade ölçümlerle **yalnızca kısmen**
> örtüşür — 8 MS alt sayfası gerçekten 404'tür (aşağıda), ancak 4 MS alt sayfası
> **200**'dür (§1.1 madde 10–13). "MS yüzeyi tamamen ölü" **denmez**; "8 alt sayfa
> 404, 4 alt sayfa 200, içerik derinliği okunmadı" denir.

| # | URL (learn.microsoft.com/en-us/windows-hardware/drivers/) | Durum | Anayasa ilgisi |
|---|---|---|---|
| 1 | `kernel/irql` | 404 | §11 kesme düzlemi |
| 2 | `kernel/deferring-procedure-calls-and-dpcs` | 404 | §11.1 DPC karşılığı |
| 3 | `kernel/using-an-interrupt-service-routine` | 404 | §11.1 ISR karşılığı |
| 4 | `kernel/about-deferred-procedure-calls` | 404 | §11.1 DPC (eski ad) |
| 5 | `kernel/processors-and-dispatching` | 404 | §11.1 iş dağıtım |
| 6 | `kernel/power-management-for-devices-in-windows` | 404 | §5 PME karşılığı |
| 7 | `kernel/acpi-support` | 404 | §13 ACPI↔Windows |
| 8 | `wdf/irql-requirements-for-usb-drivers` | 404 | §11 (USB kesme) |

| # | URL (kernel) | Durum | Not |
|---|---|---|---|
| 9 | `https://docs.kernel.org/thermal/index.html` | 404 | **thermal URL düzeltme kaydı → §2** (anayasa frontmatter evidence ile çelişiyor) |

**Denetim:** bu dokuz dize bu alt dalda **kanıt satırı olarak 0 geçmeli** (§6 regex
R4); yalnız bu §1.2/§10 kara liste tablolarında "KULLANMA" kaydı olarak bulunabilir.
(Not: `driver-api/sound.html` · `driver-api/clocksource.html` ·
`timers/clocksource.html` · `timers/clockevents.html` ·
`www.kernel.org/doc/html/latest/sound/hd-audio.html` 404'leri **donanim-arayuz-protokolleri**
kapsamındadır — `donanim-arayuz-protokolleri-dogrulama.md` §1.2 kayıtlıdır; burada
tekrarlanmaz, bu alt dal onlara atıf yapmaz.)

### §1.3 Doğrulanmadı — `⚠️ VERIFICATION REQUIRED`

| Yüzey | Neden |
|---|---|
| 200 OK sayfalarının içerik derinliği | yalnız canlılık ölçülüyor; kural cümlesinin sayfada geçtiği **okunarak** doğrulanmadı |
| anayasa §11 "MS 404" ifadesinin eşdeğerliği | 4 MS sayfası 200 (§1.1 madde 10–13); hangi sayfanın hangi kurala karşılık geldiği okunmadı → eşdeğerlik iddiası ⚠️ |
| Windows S0ix modern standby karşılıkları | anayasa open_questions #3 ile aynı — bu turda doğrulanmadı |
| anayasa frontmatter evidence thermal satırı | 200 iddiası 404 ile çelişiyor (§2) — düzeltme üst karar |

## §2 Thermal URL düzeltme kaydı (anayasa §6.3 havalesi)

> Anayasa §6.3: "`https://docs.kernel.org/driver-api/thermal/index.html`
> (200 OK — canlı URL; eski `docs.kernel.org/thermal/index.html` düzeltmesi
> `-dogrulama.md` §2)." Bu §, o havaleyi yerine getirir.

| # | Adres | Ölçüm (2026-10-14) | Karar |
|---|---|---|---|
| 1 | `https://docs.kernel.org/driver-api/thermal/index.html` | **200** — başlık "Thermal" ✓ | **geçerli kanıt** — anayasa §6.3 gövde atıfı |
| 2 | `https://docs.kernel.org/thermal/index.html` | **404** | **ölü** — hiçbir dosyaya kanıt olarak yazılmaz |

### §2.1 Bilinen çelişki kaydı (dokunma yasağı nedeniyle açık bırakılır)

| Kaynak | İddia | Ölçüm | Durum |
|---|---|---|---|
| anayasa **frontmatter** `evidence` (satır 12) | `docs.kernel.org/thermal/index.html (200 OK · 2026-10-14)` | 404 (aynı tarih) | **ÇELİŞKİ** — frontmatter yalnız validation satırına ekleme yapılabilir; düzeltme üst karar/sonraki tur işidir |
| anayasa **gövde** §6.3 | eski adres "düzeltmesi -dogrulama.md §2" | 200 + 404 ayrımı yukarıda | ✅ havale yerine getirildi |
| üst düğüm `electroncis-dogrulama.md` §5.1 | "thermal 200" iddiası | hangi adresle ölçüldüğü belirsiz | ⚠️ `driver-api/thermal` ise 200 doğru; `thermal/index` ise 404 — üst düğümde hangi adresin kastedildiği doğrulanmadı |

**Kural:** bu çelişki çözülene kadar thermal kanıtı **yalnız**
`driver-api/thermal/index.html` adresinden alınır; eski adres kanıt satırına
**yazılmaz** (§6 R4 regex'i bunu denetler).

## §3 Repo dürüstlük ölçümü (devamlılık)

| Ölçüm | Sonuç (2026-10-14) | Beklenen (bu alt dal) |
|---|---|---|
| C/C++ glob | 0 | `-ornekler` şartname/pseudocode/PHP kalır |
| literal `spi` grep | 15 isabet | **sahte isabet** — tamamı Türkçe "tespit" sözcüğünün içinde geçiyor |
| `\bspi\b` grep (sınır-ayarlı) | 0 isabet | 0 |
| `\bi2s\b\|\btdm\b\|\bi2c\b` grep (sınır-ayarlı) | 0 isabet | 0 |
| `acpi\|thermal\|ps_on\|pwr_ok\|wakeup\|usb_audio` grep (php/js) | 0 isabet | her büyük turda **tekrar** |
| PHP satır kanıtları | `-ornekler` §13/§14 `strict_types` PHP örneği okundu | bu alt dal yeni iddia eklemez |

**Dürüstlük notu (üst düğüm §4.1 ilişkisi):** üst düğüm
`electroncis-dogrulama.md` §4.1'deki "`spi` = 0 isabet" kaydı **yalnızca
sınır-ayarlı ölçümle doğrudur** — literal `spi` araması 15 sahte isabet verir
(Türkçe "tespit" içinde). Bu alt dalda dürüst ölçüm sınır-ayarlı yapılır; literal
sayım "var" raporlanır ama arayüz kanıtı sayılmaz.

**Kural:** C/C++ sayısı ≠ 0 olduğu gün bu alt dalın `-ornekler` dosyası gerçek kod
bloklarına geçirilir ve dürüstlük tablosu yeniden ölçülür (üç başarısız turda dur +
üst karar).

## §4 İç tutarlılık denetimi (anayasa ↔ örnekler ↔ kapsam)

| İddia | Anayasa | Örnekler | Sonuç |
|---|---|---|---|
| S0–S5 ↔ D0–D3 eşlemesi | §2 | §11 (güç durumu geçiş denetleyici) | ✅ tutarlı |
| PS_ON/PWR_OK seviye-tabanlı (voltaj yok) | §3 | §3 (S5 → açılış el sıkışması) | ✅ tutarlı — sayı iki dosyada da 0 |
| wake kaynakları / wake-arm | §4 | §13 (Wake-arm doğrulama PHP) | ✅ tutarlı |
| PME olay işleme + kesme bütçesi | §5.1 · §11 | §11-1 (kesmede uzun iş yok) | ✅ tutarlı |
| termal zincir (sayı yok, kural yapısı) | §6 | §12 (termal kademe izleyici) | ✅ tutarlı |
| uyku-öncesi rollback zinciri | §10 | §14 (uyku-öncesi kontrol listesi PHP) | ✅ tutarlı |
| DPC/ISR Windows karşılığı | §11.1 ⚠️ işaretli | örnekler Windows iddiası taşımıyor | ✅ tutarlı — çelişki yok, işaret korunuyor |
| thermal adresi | §6.3 (gövde) | örnekler adres taşımıyor | ✅ tutarlı — düzeltme §2'de |
| sayısal eşik/voltaj/°C | YOK (open_questions) | YOK (§15 sınır tablosu) | ✅ tutarlı — çift taraflı uydurma yasak |

## §5 Beyazliste denetimi (bu alt dal = 4 dosya)

| Dosya | Zemin | Satır hedefi | group |
|---|---|---|---|
| `index.md` | mimari | 40–100 | `mimari` |
| `-anayasa.md` | mimari | ≥350 | `mimari` |
| `-ornekler.md` | kod | ≥250 | `kod` |
| `-dogrulama.md` | kanit | ≥250 | `kanit` (bu dosya) |

**Başka dosya bu alt dala üretilmez** (eski `devre-tasarimi/`·`guc-ve-koruma/`
kapsam dışı — dokunma yok).

## §6 Şema regex denetimi (10 alan)

| # | Kontrol | Beklenen |
|---|---|---|
| R1 | alan sayısı | TAM 10 (`node_id, node_type, status, parent_id, children, depends_on, evidence, validation, open_questions, group`) |
| R2 | `^parent:` | **0** (yalnız `parent_id`) |
| R3 | `group:` değeri | tırnaksız · `mimari`/`kod`/`kanit` |
| R4 | 404 URL dizesi kanıt satırında | bu alt dalda **0** (§1.2 dokuz adres yalnız kara liste tablosunda) |
| R5 | `status` | `draft` |
| R6 | `node_type` | index=`subdomain`, içerik=`module` |
| R7 | `children` | index: 3 içerik dosyası · içerik: `[]` |
| R8 | `depends_on` | gerçek K-node; komşu **tam yol**; uydurma ID 0 |
| R9 | `evidence` | yalnız 200 ile doğrulanmış URL + gerçek yerel yol; doğrulanmayan `⚠️` işaretli |
| R10 | mojibake | 0 |

## §7 Komşu sınır denetimi

| Sınır | Denetim | Sonuç |
|---|---|---|
| `donanim-arayuz-protokolleri` | arayüz/register/clock iddiaları buraya sızmamış (onlar komşu alt dal) | ✅ |
| `K002-electroncis` | eşik voltajı/°C trip/register **bu dosyalarda yok** | ✅ (uydurma 0) |
| `os-donanim-tespiti` | envanter/ID listesi burada yok | ✅ |
| `isletim-sistemi-audio` | üst katman akış politikası tekrarı yok | ✅ |
| `isletim-sistemi-kernel` | kesme/workqueue kavramı yalnız atıf düzeyinde, tekrar tanım yok | ✅ |
| yasaklı yüzeyler | `devre-tasarimi/`·`guc-ve-koruma/`·`os-master.md` vb. yazma 0 | ✅ (yalnız read) |

## §8 Checklist (sıcaklık + denetim)

### §8.1 Sıcaklık (tekrar-ölçüm) checklist'i

| # | Kontrol | Sıklık | Bugün (2026-10-14) |
|---|---|---|---|
| 1 | §1.1 canlı URL'leri yeniden ölç | her tur | 13 × 200 · 0 yeniden-ölen |
| 2 | §1.2 dokuz 404 hâlâ 404 mü (kara liste canlı) | her tur | 9 × 404 doğrulandı |
| 3 | thermal düzeltme (§2) hâlâ geçerli mi | her tur | `driver-api/thermal` 200 ✓ |
| 4 | Repo C/C++ glob hâlâ 0 mu | her tur | 0 → şartname kalır |
| 5 | sınır-ayarlı `\bspi\b\|\bi2s\b\|\btdm\b\|\bi2c\b` grep | her tur | 0 isabet |
| 6 | `acpi\|thermal\|ps_on\|pwr_ok\|wakeup\|usb_audio` grep (php/js) | her tur | 0 isabet |
| 7 | Frontmatter alan sayısı = 10 | her tur | 10/10 |
| 8 | `group` değeri tırnaksız ve kümede | her tur | `kanit` ✓ |
| 9 | Yazım-hatası dizeleri (`electroncis`, `os-guc-elektronigi`, `donanim-arayuz-protokolleri` atfı) sabit mi | her tur | sabit ✓ |

### §8.2 Denetim checklist'i (dosya-bazlı kabul)

| # | Kriter | Kanıt | Durum |
|---|---|---|---|
| 1 | Frontmatter 10 alan · sibling yapısı birebir | `node_type: module` · `status: draft` · `group: kanit` · `children: []` | ✓ |
| 2 | evidence yalnız 200 URL + gerçek yerel yol | 8×200 URL + `rtos.md` (404 URL 0) | ✓ |
| 3 | validation satırına Faz C notu eklendi | frontmatter `validation` sonu ` · 2026-10-14 · genişletme (Faz C)` | ✓ |
| 4 | anayasa §11 havalesi (MS 404 kaydı) yerine getirildi | §1.2 madde 1–8 | ✓ |
| 5 | anayasa §6.3 havalesi (thermal düzeltme) yerine getirildi | §2 | ✓ |
| 6 | Sayısal eşik/voltaj/°C uydurulmadı | bu dosyada sayısal eşik **0** | ✓ |
| 7 | C/C++ kod bloğu yok | bu dosyada kod bloğu 0 | ✓ |
| 8 | `git commit` atılmadı | subagent yasağı | ✓ |

## §9 Açık sorular

1. anayasa frontmatter evidence thermal satırı (`200 OK`) düzeltilsin mi?
   (bu dosya §2.1 — düzeltme yetkisi üst karar; **bu turda dokunulmadı**)
2. MS 200 sayfalarının (§1.1 madde 10–13) içerik derinliği okunup anayasa §11.1
   "DPC karşılığı" satırı güncellensin mi? (`⚠️ VERIFICATION REQUIRED`)
3. 404 listesi her turda yeniden mi ölçülür? (öneri: EVET — link çürümesi sessizdir)
4. Üst düğüm §5.1 "thermal 200" iddiasındaki adres belirsizliği üst düğümde
   not edilsin mi? (madde: §2.1 — şu an yalnız burada kayıtlı)

---

## §10 Red-team soruları (bu alt dal)

| # | Soru | Beklenen cevap/kanıt |
|---|---|---|
| 1 | 200 durum kodu "kural doğru" demek mi? | Hayır — §1.3: yalnız canlılık; içerik ayrıca okunur |
| 2 | §1.2'deki 404 adreslerden biri bir kanıt satırına sızmış mı? | Sızdıysa §6 R4 regex ihlali → RED (bu tur: 0) |
| 3 | anayasa §11 "MS alt sayfaları 404" ifadesi tam mı? | Hayır — 8×404 + 4×200 (§1.2 dürüstlük notu); "tamamen ölü" denmez |
| 4 | thermal düzeltmesi uydurma mı? | Hayır — §2: 200/404 ölçüm kaydı + başlık "Thermal" ✓ |
| 5 | Bu dosya üst düğüm `electroncis-dogrulama.md`'yi tekrar ediyor mu? | Hayır — yalnız alt-dala özgü ölçümler; genel 404 listesi (sound/clocksource) bilinçli olarak tekrarlanmadı (§1.2 son not) |
| 6 | `spi` literal 15 isabet arayüz kanıtı mı? | Hayır — §3: tamamı Türkçe "tespit" içinde sahte isabet; sınır-ayarlı ölçüm 0 |
| 7 | Bu dosyada sayısal eşik/voltaj/°C var mı? | Yok — §4 son satır; sayı K002 işi, çift taraflı uydurma yasak |
| 8 | evidence'daki `rtos.md` atıfı doğrulanmış mı? | Yerel dosya (salt-okunur atıf, anayasa evidence ile aynı biçim) — canlılık ölçümüne tabi değil |
| 9 | Faz C genişletmesi mevcut içeriği sildi mi? | Hayır — bu dosya sıfırdan yazıldı; kardeş dosyalarda silme 0, ekleme sona |

---

## §11 Ölçüm günlüğü (2026-10-14 — bu dosyanın kanıt üretimi)

### §11.1 Yöntem (bağlayıcı)

| # | Adım | Araç/yöntem | Kural |
|---|---|---|---|
| 1 | URL canlılık testi | `GET` (redirect takibi) — `execute` + `fetch` ile toplu | yalnız durum kodu kanıt sayılır |
| 2 | Başlık doğrulama | yanıt gövdesindeki `<title>` | başlık okunan satırda `✓` taşır |
| 3 | Gövde derinliği okuması | **YAPILMADI** | içerik derinliği okunmayan satır `⚠️` taşır (§1.3) |
| 4 | Repo dürüstlük ölçümü | glob + sınır-ayarlı regex | literal sayım sahte isabet üretir (§3) |
| 5 | Frontmatter denetimi | alan sayımı + regex R1–R10 | 10 alan dışında ekleme/düzeltme yok |

### §11.2 Ölçüm anı kaydı

| Kalem | Değer |
|---|---|
| Ölçüm tarihi | 2026-10-14 |
| Yöntem | HTTP durum kodu + `<title>` doğrulaması (gövde derinliği hariç) |
| Toplam ölçüm | 13 × 200 (§1.1) + 9 × 404 (§1.2) = 21 adres |
| Yeniden-ölen URL | 0 |
| Kanıt satırına yazılan 404 | 0 (yalnız kara liste tablolarında) |
| Bu dosyada ölçülen sayısal eşik/voltaj/°C | **0** — ölçümde de uydurulmadı |

### §11.3 Kanıt sınıfı tanımları (bu dosyada kullanılan)

| Sınıf | Tanım | Kullanım |
|---|---|---|
| 200 + başlık ✓ | canlılık + sayfa kimliği doğrulanmış | kanıt satırına yazılabilir |
| 200 + ⚠️ başlık | yalnız canlılık; içerik okunmadı | kanıt satırına yazılır ama iddia derinliği işaretli |
| 404 | ölü | **yalnız** kara liste; kanıt satırı yasak |
| yerel dosya | disk kanıtı (satır sayılı) | canlılık ölçümüne tabi değil; salt-okunur atıf |

## §12 Faz C genişletme günlüğü (bu dosya)

| # | Değişiklik | Durum |
|---|---|---|
| 1 | Dosya **sıfırdan** yazıldı (sibling `-dogrulama` yapısı referans) | ✓ |
| 2 | Frontmatter 10 alan · sibling ile aynı `node_type/status/group/children` biçimi | ✓ |
| 3 | anayasa §11 → §1.2 MS 404 kaydı havalesi | ✓ |
| 4 | anayasa §6.3 → §2 thermal URL düzeltme havalesi | ✓ |
| 5 | §2.1 bilinen çelişki kaydı (anayasa frontmatter thermal 200 iddiası) | ✓ — dokunmadan kayıt |
| 6 | §3 dürüstlük: `spi` literal 15 sahte isabet (tespit) notu | ✓ |
| 7 | Frontmatter `validation` satırına Faz C notu eklendi; diğer alanlar aynen | ✓ |
| 8 | `git commit` | **atılmadı** (subagent yasağı — orkestratöre ait) |

### §12.1 Sınır beyanı (Faz C — bu dosyaya özgü)

| # | Beyan |
|---|---|
| 1 | Bu dosya yalnız **doğrulama** üretir; mimari kural/kod **yazmaz** (kural anayasada, kod örnekte). |
| 2 | Ölçümler tek tarihlidir (2026-10-14); tarihsiz hiçbir kanıt satırı geçerli sayılmaz (§11.3). |
| 3 | 404 kara listesi dokuz adresi **sessizce büyür** — yeni 404 ölçümü bu §12 günlüğüne değil, §1.2 tablosuna işlenir. |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
