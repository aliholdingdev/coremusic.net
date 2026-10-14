---
node_id: K000-isletim-sistemi/electroncis/donanim-arayuz-protokolleri/donanim-arayuz-protokolleri-dogrulama
node_type: module
status: draft
parent_id: K000-isletim-sistemi/electroncis/donanim-arayuz-protokolleri
children: []
depends_on: [K002-electroncis, K000-isletim-sistemi/os-donanim-tespiti]
evidence:
  - "https://docs.kernel.org/driver-api/i2c.html (200 OK · 2026-10-14)"
  - "https://docs.kernel.org/driver-api/spi.html (200 OK · 2026-10-14)"
  - "https://docs.kernel.org/driver-api/usb/index.html (200 OK · 2026-10-14)"
  - "https://docs.kernel.org/driver-api/gpio/index.html (200 OK · 2026-10-14)"
  - "https://docs.kernel.org/driver-api/clk.html (200 OK · 2026-10-14)"
  - "https://docs.kernel.org/sound/index.html (200 OK · 2026-10-14)"
  - "https://docs.kernel.org/power/runtime_pm.html (200 OK · 2026-10-14)"
validation: "2026-10-14 · yazıldı (Vault Steward Q1 kararı) · web: 7×200 bu alt-dalda · şema regex denetimi §5 · red-team bekliyor · 2026-10-14 · genişletme (Faz C)"
open_questions:
  - "⚠️ VERIFICATION REQUIRED: i2c/spi/usb/gpio/clk sayfalarının İÇERİK derinliği (tek tek paragraf) değil yalnız canlılık doğrulandı — tırnak planı §7'ye bağlı."
  - "clocksource/clockevents 404: bu alt-dal kapsamı dışında tutuldu — doğrulama listesinde hiç anılmaz (yanlış link yazma yasağı)."
group: kanit
---

# Doğrulama — donanim-arayuz-protokolleri

> **`electroncis` yazım hatası dokunulmaz** (v2 §3 KURAL 1).

## §0 ELI15

Bu dosya, alt dalın **iddialarını kanıta** bağlar: hangi URL'ler canlı (200), hangileri
ölü (404 ve asla yazılmaz), repo'da C/C++ hâlâ yok mu ( dürüstlük), şema 10 alanı
sağlıyor mu ve anayasa↔örnekler↔bu dosya birbirine çelişiyor mu. Üst düğüm
`electroncis-dogrulama.md` **tek genel kayıt**tır; burası alt-dala özgü **ölçüm
detayı**dır (tekrarlanan genel kayıtlar imha edilir, yalnız alt-dala özgü satırlar kalır).

## §1 Web doğrulama matrisi (2026-10-14)

### §1.1 Yaşayan (200 OK) — bu alt-dala özgü

| # | URL | Ne doğrular |
|---|---|---|
| 1 | `https://docs.kernel.org/driver-api/i2c.html` | §3 register erişimi davranışı |
| 2 | `https://docs.kernel.org/driver-api/spi.html` | §2/-ornekler §2 SPI |
| 3 | `https://docs.kernel.org/driver-api/usb/index.html` | §4 enumerasyon |
| 4 | `https://docs.kernel.org/driver-api/gpio/index.html` | §5 GPIO/hotplug |
| 5 | `https://docs.kernel.org/driver-api/clk.html` | §6 clock ağacı |
| 6 | `https://docs.kernel.org/sound/index.html` | §2 ses yığını |
| 7 | `https://docs.kernel.org/power/runtime_pm.html` | §3.2-2 runtime PM |
| 8 | `https://www.usb.org/` | USB-IF (sınıf standartları ana kaynağı) |

### §1.2 Ölü (404 · KULLANMA)

`docs.kernel.org/driver-api/sound.html` · `driver-api/clocksource.html` ·
`timers/clocksource.html` · `timers/clockevents.html` ·
`www.kernel.org/doc/html/latest/sound/hd-audio.html`

**Denetim:** bu beş dize bu alt dalda **0 geçmeli** (§5 regex R4).

### §1.3 Doğrulanmadı — `⚠️ VERIFICATION REQUIRED`

| Yüzey | Neden |
|---|---|
| Windows UAC/WDM sayfa derinliği | genel WDK uçları 200; içerik madde madde okunmadı |
| Sayfaların iç tırnakları | canlılık doğrulandı, paragraf derinliği **hayır** |

## §2 Repo dürüstlük ölçümü (devamlılık)

| Ölçüm | Sonuç (2026-10-14) | Beklenen (bu alt dal) |
|---|---|---|
| C/C++ glob | 0 | `-ornekler` şartname kalır |
| `acpi|thermal|ps_on|pwr_ok|wakeup|i2s|tdm|i2c|spi|usb_audio` grep (php/js) | 0 isabet | her büyük turda **tekrar** |
| PHP satır kanıtları | 7/7 okundu (üst düğüm §4.2) | bu alt dal yeni iddia eklemez |

**Kural:** C/C++ sayısı ≠ 0 olduğu gün bu alt dalın `-ornekler` dosyası gerçek kod
bloklarına geçirilir ve dürüstlük tablosu yeniden ölçülür (üç başarısız turda dur +
üst karar).

## §3 İç tutarlılık denetimi (anayasa ↔ örnekler ↔ kapsam)

| İddia | Anayasa | Örnekler | Sonuç |
|---|---|---|---|
| Başlatma sırası (clock→DMA→codec→yol) | §2.3 | §5 (indirgenmiş) | ✅ tutarlı |
| Yaz-oku-doğrula | §3.2-1 | §2 (SPI) | ✅ tutarlı |
| Hotplug ≠ güç durumu | §5-4 | kök ornekler §Örnek D | ✅ tutarlı |
| Runtime PM askı | §3.2-2 · §6 | §6 | ✅ tutarlı |
| Register adresi uydurma yasağı | §3.1 · §9-1 | §1 · §4 | ✅ tutarlı |
| rollback zorunlu | §7 | §5-c | ✅ tutarlı |

## §4 Beyazliste denetimi (bu alt dal = 4 dosya)

| Dosya | Zemin | Satır hedefi | group |
|---|---|---|---|
| `index.md` | mimari | 40–100 | `mimari` |
| `-anayasa.md` | mimari | ≥350 | `mimari` |
| `-ornekler.md` | kod | ≥250 | `kod` |
| `-dogrulama.md` | kanit | ≥250 | `kanit` (bu dosya) |

**Başka dosya bu alt dala üretilmez** (eski `devre-tasarimi/`·`guc-ve-koruma/`
 kapsam dışı — dokunma yok).

## §5 Şema regex denetimi (10 alan)

| # | Kontrol | Beklenen |
|---|---|---|
| R1 | alan sayısı | TAM 10 (`node_id, node_type, status, parent_id, children, depends_on, evidence, validation, open_questions, group`) |
| R2 | `^parent:` | **0** (yalnız `parent_id`) |
| R3 | `group:` değeri | tırnaksız · `mimari`/`kod`/`kanit` |
| R4 | 404 URL dizesi | bu alt dalda **0** (§1.2) |
| R5 | `status` | `draft` |
| R6 | `node_type` | index=`subdomain`, içerik=`module` |
| R7 | `children` | index: 3 içerik dosyası · içerik: `[]` |
| R8 | `depends_on` | gerçek K-node; komşu **tam yol**; uydurma ID 0 |
| R9 | `evidence` | gerçek yol/200 URL; doğrulanmayan `⚠️` işaretli |
| R10 | mojibake | 0 |

## §6 Komşu sınır denetimi

| Sınır | Denetim | Sonuç |
|---|---|---|
| `os-guc-elektronigi` | güç durumu iddiaları buraya sızmamış (S0/S5 yalnız orada) | ✅ |
| `K002-electroncis` | register/eşik/eşitleme **bu dosyalarda yok** | ✅ (uydurma 0) |
| `os-donanim-tespiti` | envanter/ID listesi burada yok | ✅ |
| `isletim-sistemi-audio` | üst katman akış politikası tekrarı yok | ✅ |
| yasaklı yüzeyler | `devre-tasarimi/`·`guc-ve-koruma/`·`os-master.md` vb. yazma 0 | ✅ (yalnız read) |

## §7 Açık sorular

1. URL içerik derinliği (sayfa paragrafı) doğrulaması yapılacak mı?
   (`⚠️ VERIFICATION REQUIRED`)
2. 404 listesi her turda yeniden mi ölçülür? (öneri: EVET — link çürümesi sessizdir)
3. Üst düğüm genel kayıt + burada alt-dal detayı formatı **tekrar** sayımında
   kalemleştirilsin mi? (madde: §1.1'in bir kısmı üst düğümle ortak)

---

> §8–§11, Faz C genişletmesiyle eklenmiştir; §0–§7 numaraları **değişmemiştir**
> (diğer dosyalardaki § referansları korunur).

## §8 Arayüz kanıt doğrulama matrisi (canlı erişim testi · 2026-10-14)

> **Yöntem:** her URL `GET` (redirect takibi) ile ölçüldü; **yalnız durum kodu**
> kanıt sayılır. Sayfa **içerik derinliği** (hangi paragraf hangi kuralı destekliyor)
> bu turda okunmadı → ilgili sütun `⚠️ VERIFICATION REQUIRED` taşır.

### §8.1 Erişim testi — `-anayasa.md` evidence'ından alınan kanıtlar

| # | URL (anayasa evidence) | Durum | Ne doğrular | İçerik derinliği |
|---|---|---|---|---|
| 1 | `https://docs.kernel.org/driver-api/i2c.html` | 200 | §3 I2C register erişimi | ⚠️ okunmadı |
| 2 | `https://docs.kernel.org/driver-api/spi.html` | 200 | §3 SPI yaz-oku-doğrula | ⚠️ okunmadı |
| 3 | `https://docs.kernel.org/driver-api/usb/index.html` | 200 | §4.4 USB keşif/zincir | ⚠️ okunmadı |
| 4 | `https://docs.kernel.org/driver-api/gpio/index.html` | 200 | §5 GPIO/hotplug olay sırası | ⚠️ okunmadı |
| 5 | `https://docs.kernel.org/driver-api/clk.html` | 200 | §6 clock ağacı/devralma | ⚠️ okunmadı |
| 6 | `https://docs.kernel.org/sound/index.html` | 200 | §2 ses yığını | ⚠️ okunmadı |
| 7 | `https://www.usb.org/` | 200 | USB-IF sınıf standartları ana kaynağı | ⚠️ okunmadı |
| 8 | `C:\www\coremusic.net\.ai\electronic\drivers\audio-drivers.md` | yerel · 709 satır | tam elektronik atıf | okundu (üst düğüm § Evidence) |

### §8.2 Erişim testi — anayasa gövdesindeki (frontmatter dışı) ek kanıtlar

| # | URL (anayasa gövdesi) | Durum | İlgili bölüm | İçerik derinliği |
|---|---|---|---|---|
| 1 | `https://docs.kernel.org/driver-api/usb/usb.html` | 200 | §4.4 USB keşif sırası | ⚠️ okunmadı |
| 2 | `https://docs.kernel.org/driver-api/acpi/index.html` | 200 | §10 ACPI↔driver | ⚠️ okunmadı |
| 3 | `https://docs.kernel.org/firmware-guide/acpi/index.html` | 200 | §10 firmware rehberi | ⚠️ okunmadı |
| 4 | `https://docs.kernel.org/driver-api/basics.html` | 200 | §11 workqueue/kuyruk kavramı | ⚠️ okunmadı |
| 5 | `https://docs.kernel.org/driver-api/thermal/index.html` | 200 | §6.3 thermal (düzeltilmiş adres) | ⚠️ okunmadı |
| 6 | `https://docs.kernel.org/driver-api/dmaengine/index.html` | 200 | §2.5 DMA ring | ⚠️ okunmadı |
| 7 | `https://docs.kernel.org/driver-api/dmaengine/client.html` | 200 | §2.5 DMA istemci API | ⚠️ okunmadı |
| 8 | `https://learn.microsoft.com/en-us/windows-hardware/drivers/kernel/` | 200 | §11 Windows genel uç | ⚠️ okunmadı |
| 9 | `https://docs.kernel.org/power/runtime_pm.html` | 200 | §3.2-2 runtime PM | ⚠️ okunmadı |

### §8.3 ⚠️ Erişim kuralı (bağlayıcı)

| # | Kural |
|---|---|
| 1 | Kanıt olarak **yalnız 200 ile doğrulanmış** URL yazılır; doğrulanamayan iddia `⚠️ VERIFICATION REQUIRED` olur. |
| 2 | Durum kodu ≠ içerik doğrulaması: 200, sayfanın **canlı** olduğunu kanıtlar; iddia edilen kuralın o sayfada geçtiğini **kanıtlamaz** → madde derinliği ayrıca okunur. |
| 3 | 404 adres hiçbir dosyaya **yazılmaz** (yalnız §1.2 ve §10 kara liste tablosunda "KULLANMA" kaydı olarak tutulur). |
| 4 | Ölçüm tarihi her satırda zorunlu (bugün: 2026-10-14); tarihsiz kanıt satırı geçersiz sayılır. |
| 5 | Bu matris **her büyük yazım turunda yeniden** ölçülür — link çürümesi sessizdir (§7-2 önerisi ile aynı). |

## §9 Sıcaklık / denetim checklist'i (bu alt dal)

### §9.1 Sıcaklık (tekrar-ölçüm) checklist'i

| # | Kontrol | Sıklık | Bugün (2026-10-14) |
|---|---|---|---|
| 1 | §1.1 + §8.1 + §8.2 canlı URL'leri yeniden ölç | her tur | 16 × 200 · 0 yeniden-ölen |
| 2 | §1.2 404 listesi hâlâ 404 mü (kara liste canlı) | her tur | 5 × 404 doğrulandı |
| 3 | Repo C/C++ glob hâlâ 0 mu | her tur | 0 → şartname kalır |
| 4 | `acpi\|thermal\|ps_on\|pwr_ok\|wakeup\|usb_audio` grep (php/js) | her tur | 0 isabet |
| 5 | `\bi2s\b\|\btdm\b\|\bi2c\b\|\bspi\b` grep (php/js) | her tur | 0 isabet |
| 6 | Frontmatter alan sayısı = 10 | her tur | 10/10 |
| 7 | `group` değeri tırnaksız ve kümede | her tur | `kanit` ✓ |
| 8 | Yazım-hatası dizeleri (`electroncis`, `donanim-arayuz-protokolleri`) sabit mi | her tur | sabit ✓ |

### §9.2 Denetim checklist'i (dosya-bazlı kabul)

| # | Kriter | Kanıt | Durum |
|---|---|---|---|
| 1 | Yeni bölüm mevcut bölümlerin **ardına** eklendi (silme yok) | §8–§11 eklendi, §0–§7 korundu | ✓ |
| 2 | URL envanter tablosunda **boş satır/sütun yok** | §10: 17 satır × 4 sütun dolu | ✓ |
| 3 | 404 adresler yalnız kara listede | §1.2 · §10 "ölüm" sütunu | ✓ |
| 4 | Sayısal eşik/voltaj uydurulmadı | bu dosyada eşik/voltaj **0** | ✓ |
| 5 | Validation satırına Faz C notu eklendi | frontmatter `validation` | ✓ |
| 6 | Frontmatter'in other alanlarına dokunulmadı | `node_id`/`evidence`/`open_questions` aynen korundu | ✓ |
| 7 | C/C++ kod bloğu yok | bu dosyada kod bloğu 0 | ✓ |
| 8 | `git commit` atılmadı | subagent yasağı | ✓ |

## §10 URL envanter tablosu (bu alt dal · boş satır/sütun YOK)

| # | URL | Durum (2026-10-14) | Sınıf | Kullanım |
|---|---|---|---|---|
| 1 | `https://docs.kernel.org/driver-api/i2c.html` | 200 | yaşıyor | kanıt — I2C |
| 2 | `https://docs.kernel.org/driver-api/spi.html` | 200 | yaşıyor | kanıt — SPI |
| 3 | `https://docs.kernel.org/driver-api/usb/index.html` | 200 | yaşıyor | kanıt — USB çekirdek |
| 4 | `https://docs.kernel.org/driver-api/usb/usb.html` | 200 | yaşıyor | kanıt — USB keşif |
| 5 | `https://docs.kernel.org/driver-api/gpio/index.html` | 200 | yaşıyor | kanıt — GPIO/hotplug |
| 6 | `https://docs.kernel.org/driver-api/clk.html` | 200 | yaşıyor | kanıt — clock ağacı |
| 7 | `https://docs.kernel.org/sound/index.html` | 200 | yaşıyor | kanıt — ses yığını |
| 8 | `https://docs.kernel.org/driver-api/acpi/index.html` | 200 | yaşıyor | kanıt — ACPI↔driver |
| 9 | `https://docs.kernel.org/firmware-guide/acpi/index.html` | 200 | yaşıyor | kanıt — firmware ACPI |
| 10 | `https://docs.kernel.org/driver-api/basics.html` | 200 | yaşıyor | kanıt — workqueue kavramı |
| 11 | `https://docs.kernel.org/driver-api/thermal/index.html` | 200 | yaşıyor | kanıt — thermal (düzeltme) |
| 12 | `https://docs.kernel.org/driver-api/dmaengine/index.html` | 200 | yaşıyor | kanıt — DMA ring |
| 13 | `https://docs.kernel.org/driver-api/dmaengine/client.html` | 200 | yaşıyor | kanıt — DMA istemci |
| 14 | `https://docs.kernel.org/power/runtime_pm.html` | 200 | yaşıyor | kanıt — runtime PM |
| 15 | `https://www.usb.org/` | 200 | yaşıyor | kanıt — USB-IF standart |
| 16 | `https://learn.microsoft.com/en-us/windows-hardware/drivers/kernel/` | 200 | yaşıyor | kanıt — Windows genel uç |
| 17 | `https://docs.kernel.org/driver-api/sound.html` | 404 | ölü | **KULLANMA** |
| 18 | `https://docs.kernel.org/driver-api/clocksource.html` | 404 | ölü | **KULLANMA** |
| 19 | `https://docs.kernel.org/timers/clocksource.html` | 404 | ölü | **KULLANMA** |
| 20 | `https://docs.kernel.org/timers/clockevents.html` | 404 | ölü | **KULLANMA** |
| 21 | `https://www.kernel.org/doc/html/latest/sound/hd-audio.html` | 404 | ölü | **KULLANMA** |

**Sayım:** 21 kayıt · 16 × 200 · 5 × 404 · boş hücre 0.

## §11 Red-team soruları (bu alt dal)

| # | Soru | Beklenen cevap/kanıt |
|---|---|---|
| 1 | `usb.org` ana sayfası USB **ses sınıfı** (UAC) şartnamesi mi? | Hayır — ana sayfa kanıt; sınıf derinliği `⚠️ VERIFICATION REQUIRED` |
| 2 | 200 durum kodu "kural doğru" demek mi? | Hayır — §8.3-2: yalnız canlılık; içerik ayrıca okunur |
| 3 | §1.2'deki 404 adreslerden biri bir dosyaya sızmış mı? | Sızdıysa §5 R4 regex ihlali → RED (bu tur: 0) |
| 4 | DMA ring §2.5 iddiaları gerçek kod tırnağı mı? | Hayır — `{niyet}` şartnamesi (`-ornekler.md` §1 dürüstlük kaydı) |
| 5 | Bu dosya üst düğüm `electroncis-dogrulama.md`'yi tekrar ediyor mu? | Bölüm §0-§1.1'in bir kısmı ortak → tekrar kalemleştirilmeli (§7-3 açık soru) |
| 6 | Windows §11 satırındaki "MS alt sayfaları 404" kaydı nerede tutulur? | `os-guc-elektronigi-dogrulama.md` §1.2 — bu alt dalda **değil** (kapsam sınırı) |
| 7 | Ölçüm tekrarlanmadan "200" iddiası bayrak taşır mı? | Taşır — tarih satırı zorunlu (§8.3-4); tarihi eskimiş satır yeniden ölçülür |
| 8 | Bu dosyada sayısal eşik/voltaj var mı? | Yok — bu alt dal yalnız arayüz davranışı; sayı K002 işi |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
