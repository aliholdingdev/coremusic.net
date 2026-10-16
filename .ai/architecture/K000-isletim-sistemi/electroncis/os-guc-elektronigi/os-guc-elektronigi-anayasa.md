---
node_id: K000-isletim-sistemi/electroncis/os-guc-elektronigi/os-guc-elektronigi-anayasa
node_type: module
status: draft
parent_id: K000-isletim-sistemi/electroncis/os-guc-elektronigi
children: []
depends_on: [K002-electroncis, K001-donanım, K000-isletim-sistemi/isletim-sistemi-kernel, K000-isletim-sistemi/os-donanim-tespiti]
evidence:
  - "https://docs.kernel.org/power/index.html (200 OK · 2026-10-14)"
  - "https://docs.kernel.org/power/runtime_pm.html (200 OK · 2026-10-14)"
  - "https://docs.kernel.org/firmware-guide/acpi/index.html (200 OK · 2026-10-14)"
  - "https://docs.kernel.org/thermal/index.html (200 OK · 2026-10-14)"
  - "https://docs.kernel.org/hwmon/index.html (200 OK · 2026-10-14)"
  - "C:\www\coremusic.net\.ai\electronic\firmware\rtos.md (541 satır · salt-okunur atıf)"
validation: "2026-10-14 · yazıldı (Vault Steward Q1 kararı) · anayasa hedef ≥350 · sayısal eşik/voltaj UYDURULMADI · red-team bekliyor · 2026-10-14 · genişletme (Faz C)"
open_questions:
  - "⚠️ VERIFICATION REQUIRED: PS_ON/PWR_OK mantığı seviye-tabanlı anlatıldı; ölçüm voltajları/eşikleri bu dosyada YOKTUR (K002/donanım dokümanı işi)."
  - "⚠️ VERIFICATION REQUIRED: cihaza özgü sıcaklık trip noktaları (°C) ve fan kural sayıları bilinmiyor — kural yapısı yazıldı, sayı yazılmadı."
  - "⚠️ VERIFICATION REQUIRED: Windows güç durumu (S0ix modern standby) karşılıkları bu turda doğrulanmadı — yalnız ACPI/Linux yüzeyi 200 ile doğrulandı."
group: mimari
---

# Anayasa — os-guc-elektronigi (OS↔güç/termal elektronik arayüzü)

> **`electroncis` yazım hatası dokunulmaz** (v2 §3 KURAL 1).

## §0 ELI15

Bu anayasa, işletim sisteminin **güç durumlarını ve sıcaklığı** elektronik seviyede
yönetme kurallarını yazdır. Beş ana sütun taşır: (1) **ACPI durum ağacı** — sistem
durumları S0–S5 ile cihaz durumları D0–D3'ün karşılıklı eşlemesi; (2) **besleme el
sıkışması** — PS_ON/PWR_OK mantık seviyeleri; (3) **uyandırma** — wake kaynakları,
GPE/SCI, wakeup bitleri; (4) **PME/runtime PM** — kartın istirahatı; (5) **termal
zincir** — sensörden kritik kapanışa kadar OS zinciri. Kural olan her şey OS
davranışıdır; **gerilim eşiği, °C trip noktası, RC sabiti gibi sayısal değerler
uydurulmaz** — onlar `K002-electroncis`/donanım dokümanına aittir ve bu anayasada
yalnız "şu sayı nereden gelir" kaydı taşır.

## §1 Kapsam testi

| Soru | Sahibi |
|---|---|
| OS bu durumu nasıl geçer/uyandırır? | **bu anayasa** |
| Voltaj eşiği/koruma devresi nedir? | `K002-electroncis` |
| Sıralama RC'si / besleme katmanı? | `K002-electroncis` |
| Firmware-in ACPI tablosu yazımı? | K001/firmware (yalnız OS **okuması** burada) |
| RTOS içinde güç task'ı? | `.ai/electronic/firmware/rtos.md` (541 satır, atıf) |

## §2 ACPI durum ağacı — S0–S5 ↔ D0–D3

### §2.1 Sistem durumları (OS gözü)

| Durum | OS gözünde anlam | Elektronik arayüz notu |
|---|---|---|
| S0 | çalışır durum | tüm arayüzler aktif/runtime-PM altında |
| S1–S2 | ara durumlar | donanım desteklemiyorsa **ullanılmaz** — destek firmware tarafından bildirilir |
| S3 | uyku (RAM canlı) | RAM self-refresh; kartsal güç büyük ölçüde kesilir |
| S4 | kışlattırma (hibernate) | RAM diske yazılır; besleme neredeyse tamamen iner |
| S5 | kapalı (soft-off) | PS_ON de-asserted'a döner; yalnız wake/devre açık |

> Kaynak: `docs.kernel.org/firmware-guide/acpi/index.html` (200 OK).

### §2.2 Cihaz durumları (D-state) — eşleştirme

| D-state | Anlam | OS davranışı |
|---|---|---|
| D0 | tam çalışıyor | runtime-get aktif |
| D1–D2 | ara güç durumları | donanım destekliyorsa; destek yoksa kullanılmaz |
| D3hot | fiilen kapalı ama yeniden konfigüre edilebilir | register kaybı kabul edilir |
| D3cold | tamamen kesik | wake yeteneği ayrı bayt/kuralla korunabilir |

### §2.3 Bağlayıcı eşleşme kuralları

| # | Kural |
|---|---|
| 1 | S-geçişi her cihaz için **D-geçişi ile eşzamanlıdır**; cihaz D0'da kalıp sistem S3'e geçemez |
| 2 | resume sırasında OS cihazı **yeniden konfigüre eder** — D1–D3 kayıpları varsayılır |
| 3 | Firmware/fişin **desteklemediği** durum OS tarafından **zorlanmaz** (destek bitleri okunur) |
| 4 | S3/S4/S5'te ses/hotplug arayüzleri önce askı (donanim-arayuz-protokolleri anayasa §6 ile birlikte) |
| 5 | Geçiş başarısızsa OS **güvenli duruma** döner (açma/kapama kombinasyonu tekrarlanmaz) |

### §2.4 D↔S geçiş sıralaması — cihaz başına derinlik

| # | Kural | Açıklama |
|---|---|---|
| 1 | askı sırası | S-geçişinde cihazlar **ters bağımlılık sırasıyla** askıya alınır: önce tüketiciler, sonra sağlayıcılar (kavram düzeyi; bağımlılık grafiği keşiften gelir — `os-donanim-tespiti`) |
| 2 | resume sırası | resume'da sıra **tersidir**: sağlayıcılar önce D0'a, tüketiciler sonra |
| 3 | kısmi başarısızlık | herhangi bir cihazın D-geçişi başarısızsa S-geçişi **iptal edilir**, sistem S0'a döner — yarı-uyku (kısmi askı) yasak |
| 4 | D3cold kaybı | D3cold sonrası register içeriği **kayıp varsayılır**; firmware'in koruduğu wake bitleri istisnadır (kanıt firmware'den) |
| 5 | envanter sınırı | sıralama grafiği/hangi cihazın hangi durumu desteklediği envanter işi — bu anayasa yalnız kuralı yazar, cihaz listesi **uydurmaz** |

### §2.5 Destek matrisi okuması (bağlayıcı)

| Kural | Açıklama |
|---|---|
| Destek biti okuma | firmware/fişin bildirdiği destek matrisi **okunur**; OS desteklenmeyen durumu **zorlamaz** (§2.3-3) |
| Belirsiz destek | destek biti okunamıyorsa o cihaz için **en güvenli durum** seçilir (D0↔D3hot döngüsü — runtime PM §5) |
| Yeniden okuma | resume sonrası destek matrisi **yeniden** okunur (firmware değişmiş olabilir); eski matrisle kurulum yasağı |

## §3 Besleme el sıkışması — PS_ON / PWR_OK (mantık seviyesi)

| Sinyal | Yön | OS/firmware gözünde anlam |
|---|---|---|
| PS_ON# (mantık-düşük) | anakart → güç kaynağı | asserted → ana besleme AÇIK · de-asserted → S5/soft-off |
| PWR_OK | güç kaynağı → anakart | high → çıkışlar stabil, sıralama sürer · low → güç iyi değil → hata senaryosu |
| wake devreleri | kart → sistem | S5'te bile beslenebilir (wake-on-LAN/USB/klavye) |

### §3.1 Bağlayıcı kurallar

1. **Seviye ≠ eşik:** bu anayasa yalnız **mantık seviyesi** (asserted/de-asserted,
   high/low) yazar; **voltaj eşiği ölçülecekse K002/donanım dokümanı** açılır
   (uydurma eşik yasak — doğrulanamayan sayı yazılmaz).
2. **PWR_OK low** firmware/OS ortak hata yoludur: OS ya yeniden başlatır ya da
   güvenli kapanış ister; kullanıcıya "anlık kesinti" gibi **gizlenmez**.
3. **S5'te wake devreleri** ayrı beslemedir — S5 = tamamen ölü değil, **kontrollü
   kapalı** (wake kaynağı iddiası §4'e bağlı kanıt ister).
4. PS_ON/PWR_OK **OS tarafından bit-bang edilmez**; firmware/EC üzerinden gözlemlenir
   (OS yüzeyi: ACPI/EC nesneleri).

## §4 Uyandırma (wake) — kaynaklar ve disiplin

| Kaynak | OS arayüzü | Kural |
|---|---|---|
| Wake-on-LAN | ağ arayüzü wake biti | kapalıysa **arm edilmez** (boşa bekleme yok) |
| Wake-on-USB/klavye | port/hid wake baytı | hangi port(lar) wake verir → envanter (UNKNOWN: sayı yok) |
| GPE/SCI | ACPI genel amaçlı olay | firmware bildirimi → OS dinleyici |
| Alarm/rtc | zamanlayıcı uyanışı | uyanış anı OS/RTC üzerinden |

### §4.1 Bağlayıcı sıralama

```text
Suspend öncesi: wake kaynaklarını ARM ET (yalnız istenen) → GPE/SCI eşlemesi → firmware'e geç
Uyanış:        wake kaynağı tespiti → resume sırası → cihazlar D0'a + yeniden konfigürasyon
Yasak:         wake baytını arm etmeden S3'e girmek (uyanamama → "donmuş makine" raporu)
```

> `⚠️ VERIFICATION REQUIRED`: hangi portun hangi sistemde wake verdiği **envanter
> işidir** (`os-donanim-tespiti`); bu dosyada sayı yok.

### §4.2 Wake arm disiplini — kontrol listesi

| # | Kural |
|---|---|
| 1 | yalnızca istenen kaynakların wake biti **yazılır**; kalanlar açık bırakılmaz |
| 2 | arm etme sonrası **geri okuma** yapılır (yaz-oku-doğrula ilkesinin wake ayağı — donanim-arayuz-protokolleri §3.2-1) |
| 3 | uyanışta wake kaynağı **tespit + log** edilir (§12) |
| 4 | S5'te wake beslemesini firmware taşır; OS wake'i "açık unutma" cihaz işi değildir (§13 sınırı) |

## §5 PME / runtime güç yönetimi

| Kavram | Anlam | Kural |
|---|---|---|
| Runtime PM | kullanılmayan kartın askıya alınması | anayasa §2 D0 ↔ D3hot döngüsü (kaynak: `power/runtime_pm.html`, 200 OK) |
| PME (power management event) | kartın OS'e "uyandır beni / uyandım" demesi | olay kaynağı doğrulanır; **yanlış PME = uyku-uyanış döngüsü** (bilinen arıza sınıfı) |
| Idle güç | kullanılmayan arayüzün kapanması | donanim-arayuz-protokolleri §6 clock/bus askısı ile aynı disiplin |

### §5.1 PME olay işleme disiplini (§11 bütçesine bağlantı)

| Kural | Açıklama |
|---|---|
| Kaynak doğrulama | PME geldiğinde kaynak kaydı okunur; kaynağı doğrulanmayan PME **izole edilir** (§5-2) |
| Uyku-uyanış döngüsü | arm edilmemiş PME uyanma üretir → kaynak izole + log (§12) |
| Runtime PM ↔ termal | sık askı-uyanış döngüsü termal stres üretir → döngü sınırlandırılır (`-ornekler.md` Senaryo E) |
| Kesme bütçesi | PME işi §11 kuralına tabidir: kesme üstü düzlemde uzun iş yok |

## §6 Termal zincir — sensörden kritik kapanışa

### §6.1 Zincir (bağlayıcı sıra)

```text
1. SENSÖR ÖRNEĞİ      → OS okuma (hwmon/ACPI thermal-zone nesnesi)   [kaynak: thermal · hwmon, 200 OK]
2. NORMAL YÖNETİM     → sıcaklık yükseldi → aktif soğutma kuralı (fan vb.)
3. TRIP POINT         → OS KISITLAMA (throttle): frekans/akım düşürme  ← sayı: cihaz işi, YOK burada
4. KRİTİK TRIP        → güvenli KAPANIŞ (firmware/OS ortak kararı)
5. UYANIŞ             → kritikten sonra yeniden açılış koşulları firmware'a bağlı
```

### §6.2 Bağlayıcı kurallar

| # | Kural |
|---|---|
| 1 | Sıcaklık verisi **tek noktadan** okunur (çoklu kopya-sayfa → yarış) |
| 2 | Trip noktası **sayı** — envanter/datasheet gelmeden **yazılmaz** (`⚠️ VERIFICATION REQUIRED`) |
| 3 | Throttle, kapanıştan **önce** denenir (yeniden başlatma yerine kısıtlama) |
| 4 | Sensör okunamıyorsa (I2C/hata) OS **fail-safe**: kısıtlama/kapanış senaryosu — sessiz geçiş **yok** |
| 5 | Fan kuralı (açma/kapama histereresi) OS politikasıdır; PWM devresi K002'dir |
| 6 | Termal veri yolu §6-1'de register okuması ise, gerçek-zamanlı ses yolunda ** yapılmaz** (donanim-arayuz-protokolleri §3.2-4 ile aynı) |

### §6.3 Termal throttling — OS tarafı mekanizmaları

> Kaynak: `https://docs.kernel.org/driver-api/thermal/index.html` (200 OK · 2026-10-14 —
> canlı URL; eski `docs.kernel.org/thermal/index.html` düzeltmesi `-dogrulama.md` §2).

| Mekanizma | OS gözünde | Kural |
|---|---|---|
| Kaynak kısıtlama | CPU frekans/çekirdek kısıtlama (kavram düzeyi) | throttle **kademeli** uygulanır; kritik seviyeye aniden sıçrama yerine §6.1 kademesi izlenir |
| Fan politikası | aktif soğutma kuralı (aç/kapa histereresi — **sayı yok**) | PWM devresi/devresi K002; OS yalnız **politika** taşır |
| İş yükü reddi | yeni ağır iş kabulü azaltılır | reddetme politikası kullanıcıya **görünür** olur — gizli kısıtlama yasak |
| Kademe tutmazlık | kısıtlama işe yaramazsa kritik trip | §6.1-4: firmware/OS ortak kapanış kararı |
| Geri kazanım | sıcaklık düştü → kademeler geri alınır | geri alma da **kademelidir**; salıncak (aç-kapa titreşimi) önlenir |

> Throttle sayıları (°C trip, frekans/akım kademesi) **bu vault'ta YOKTUR**
> (open_questions #2); kural yapısı bağlayıcı, sayı envanter/datasheet işidir.

## §7 Pil / şarj (EC/ACPI nesneleri)

| Nesne | OS gözünde | Kural |
|---|---|---|
| ACPI battery object | kapasite/durum (yüzde, bayrak) | EC üzerinden okunur; **kimyası/ eşikleri yok** (K002/BMS) |
| Adaptör takılı mı | AC durumu | güç politikasını belirler |
| Düşük şarj | OS uyarı + senaryo | kapanış eşiği **sayısal** → envanter işi |

### §7.1 Pil olay disiplini (ek — bağlayıcı)

| Olay | Kural |
|---|---|
| Adaptör tak/çıkar | olay anında güç politikası değişir; kullanıcıya **görünür** bilgilendirme zorunlu — sessiz politika değişimi yasak |
| Düşük şarj | uyarı → kapanış senaryosu (§10.4 veri boşaltma sırası) — sıra ihlal edilmez |
| Pil/EC okuma hatası | fail-safe: bilinen son değer + işaretleme; "tam şarj" gibi **sahte değer yazılmaz** |
| Şarj oranı/kimyası/BMS | **bu vault'ta YOK** — `K002-electroncis` (open_questions #1 ile aynı sınır) |
| Pil yok (masaüstü) | ACPI nesnesi yoksa OS **varsayım üretmez**; politika yalnız AC durumuyla çalışır |

## §8 Sınır komşuları tablosu

| Komşu | Sınır | Ne geçer |
|---|---|---|
| `donanim-arayuz-protokolleri` | arayüz ↔ güç | suspend'te bus/clock askı; resume'da yeniden kur |
| `isletim-sistemi-kernel` | PM çekirdeği | suspend/resume sıralaması, process |
| `os-donanim-tespiti` | keşif | EC/hwmon cihaz keşfi, wake port envanteri |
| `K002-electroncis/guc-yonetimi` | elektronik | eşik/koruma/sıralama devresi — **sayılar orada** |
| `K001-donanım` | fiziksel | besleme/kart düzeyi |
| `.ai/electronic/firmware/rtos.md` | firmware | EC/RTOS güç task'ı (atıf) |

## §9 Açık sorular

1. Voltaj eşikleri/trip sayıları hangi kayıtta tutulacak? (öneri: `K002-electroncis`
   — bu anayasa **sayı yazmaz**)
2. Windows S0ix (modern standby) doğrulaması yapılacak mı? (`⚠️ VERIFICATION REQUIRED`)
3. EC firmware vault bağlantısı kesinleşti mi? (şu an atıf — `rtos.md` 541 satır)
4. "uyanamama / donmuş makine" arıza sınıfı için ayrı postmortem node'u açılsın mı?

---

> §10–§13, Faz C genişletmesiyle eklenmiştir; §0–§9 numaraları **değişmemiştir**
> (diğer dosyalardaki § referansları korunur). Senaryo düzeyi karşılıkları:
> `-ornekler.md` §2–§4 (bu dosya kural düzeyi taşır).

## §10 Uyku / kapanış senaryoları (bağlayıcı kural zinciri)

### §10.1 S3 — uyku (RAM canlı)

| # | Kural |
|---|---|
| 1 | veri boşaltma: uygulama/servis kapanışı → dosya sistemi temizlenir (veri kaybı yasağı) |
| 2 | arayüz askısı: ses hattı/clock/bus önce durur (donanim-arayuz-protokolleri §6.3 sırası) |
| 3 | wake ARM: **yalnız** istenen kaynak (§4-1); arm edilmemiş kaynak kapalı kalır |
| 4 | GPE/SCI eşlemesi doğrulanır → firmware'e devir → RAM self-refresh |
| 5 | herhangi bir adım başarısız → S3 **iptal**, sistem S0'da kalır (yarı-uyku yasak) |

### §10.2 S4 — kışlattırma (hibernate)

| # | Kural |
|---|---|
| 1 | S3 adımları 1–3 ile aynı hazırlık |
| 2 | RAM görüntüsü diske yazılır; **yazma doğrulanmadan** besleme indirilmez |
| 3 | yazma hatası → S4 iptal, sistem S0'a döner; "yazıldı varsayımı" yasak |
| 4 | besleme indirme sırası firmware/OS ortak; OS kendi payını bırakır |

### §10.3 S5 — kapalı (soft-off)

| # | Kural |
|---|---|
| 1 | tüm arayüz askısı (§10.1-2 sırası) tamamlanmadan geçiş yok |
| 2 | PS_ON de-assert'e döner (§3 tablosu) — yalnız wake/devreleri beslemede kalır |
| 3 | wake kaynakları iddiası §4'e bağlı kanıt ister (S5 = kontrollü kapalı, "ölü" değil) |
| 4 | S5'te kalan arayüz → §10.1-2 ihlali sayılır; kayıt `-dogrulama.md`'ye girer |

### §10.4 Kritik kapanış ve kurtarma

| Senaryo | Kural |
|---|---|
| Termal kritik trip | firmware/OS ortak kararı (§6.1-4); "yeniden başlatma yerine kısıtlama" ilkesi kritik dışında her yerde geçerlidir |
| PWR_OK low (çalışırken) | §3.1-2: yeniden dene/güvenli durum; kullanıcıya gizlenmez |
| Geçiş kurtarması | her başarısız geçiş → S0'a geri + log (§12) |
| Tekrar sınırı | art arda başarısız denemeler → güvenli durum + **üst karar** (vault §6 escalation); döngüsel tekrar yasak |

## §11 DPC/ISR gecikme bütçesi (güç/termal olaylar)

> Kaynak: `https://docs.kernel.org/driver-api/basics.html` (200 OK · 2026-10-14 —
> workqueue/kuyruk/ertelenmiş iş kavramı). Windows DPC/IRQL karşılığı
> `⚠️ VERIFICATION REQUIRED` (MS alt sayfaları 2026-10-14 ölçümünde **404** —
> kayıt: `-dogrulama.md` §1.2).

| # | Kural |
|---|---|
| 1 | kesme üstü düzlemde (ISR/düzlem-dışı) **uzun iş yok**: GPE/SCI/PME olayı yalnız kaynak doğrulama + kuyruklama yapar |
| 2 | gerçek iş ertelenmiş işe (workqueue/kuyruk — driver-api/basics 200) devredilir |
| 3 | bütçe **sayısal değeri (µs) bilinmiyor** → `⚠️ VERIFICATION REQUIRED`; kural cümlesi: "kesmede bekleyen her iş bütçeyi aşar" |
| 4 | güç/termal olay işi, ses akış kesmesiyle **aynı kuyruğa konmaz** — öncelik ayrımı: akış > güç/termal kayıt (ses bozulmasızlığı §5 anayasası) |
| 5 | olay fırtınası (PME/SCI storm) → sınırlı tekrar + kaynak izolasyonu (§5-2) |

### §11.1 Linux/Windows çift taraflı not

| Yüzey | Linux (kanıtlı) | Windows (durum) |
|---|---|---|
| ertelenmiş iş | workqueue/kuyruk (`driver-api/basics.html`, 200 OK) | DPC karşılığı `⚠️ VERIFICATION REQUIRED` (alt sayfa 404) |
| kesme düzlemi kuralı | "uzun iş yok" (bu §) | aynı ilke **iddia edilmez**, işaretli kalır |
| ölçüm | sayı yok (§11-3) | sayı yok — çift taraflı uydurma yasak |

## §12 Güç/ısı olay loglama

> Kaynak: `https://docs.kernel.org/power/basic-pm-debugging.html` (200 OK · 2026-10-14).

| Olay sınıfı | Ne loglanır | Kural |
|---|---|---|
| S/D geçişi | hedef durum, sonuç (başarı/hata), sıra adımı | her başarısız geçiş **loglanır**; gizleme yasak |
| Wake | wake kaynağı türü, uyanma nedeni | arm edilmemiş kaynak olayı **ayrı işaretli** (§4.1 arıza sınıfı) |
| PS_ON/PWR_OK olayı | firmware/EC nesnesi olayı (mantık seviyesi — sayı yok) | "anlık kesinti" gibi yorum **gizlenmez** (§3.1-2) |
| Termal | kademe geçişi, throttle uygulama/geri alma, kritik olay | trip **sayısal değeri** logda K002/envanter referansı taşır — uydurma °C yok |
| Sensör hatası | okuma hatası + uygulanan fail-safe eylemi | sessiz geçiş yasak (§6.2-4) |
| Log bütçesi | loglama akışı **durdurmaz** | log yazımı ertelenmiş işe kuyruklanır; kesmede log write yasak (§11-1) |

### §12.1 Log kaydı yerleşimi kuralları

| Kural | Açıklama |
|---|---|
| Tek kayıt sahibi | güç/termal olay kaydı **tek** alt sistemdedir; çoklu kopya → yarış (§6.2-1 ile aynı ilke) |
| Geriye dönüklik | kayıtlar S/D geçiş zincirini yeniden kurabilir (postmortem) — kayıt **silinmez** |
| Sahte veri yasağı | loga voltaj/°C/ID **uydurulmaz**; bilinen değer K002/envanter referansıyla taşınır |
| Döngü sınırı | tekrarlayan aynı olay **sayaçlanır**, her tekrar tek satır **doldurmaz** (storm ⇒ §11-5) |

## §13 Firmware ↔ K005 sınırı

| Yüzey | Sahibi | Sınır |
|---|---|---|
| ACPI tablosu yazımı / güç yöntemleri firmware tarafı | `K005-firmware` vault'u · EC/RTOS atıf: `.ai/electronic/firmware/rtos.md` (541 satır, salt-okunur) | OS yalnız **okur/çağırır** |
| EC güç task'ı (RTOS içinde) | K005 / firmware vault | bu dal yalnız OS yüzeyini yazar |
| Firmware yükleme/güncelleme | firmware işi (K005) | OS yüzeyi: yalnız taşıma/boot — derinlik yok |
| Çelişki durumu | firmware iddiası ↔ OS gözlemi | **OS gözlemi** (log/ölçüm) kazanır; kayıt iki taraflı tutulur |
| K005 dosyaları | `K005-firmware/` (index, firmware-master vb.) | bu dal K005 dosyalarına **yazmaz** (salt-okunur atıf) |

### §13.1 K005↔OS kanal yüzeyleri

| Kanal | OS gözü | Kural |
|---|---|---|
| ACPI tablosu | açılışta okunan sabit tablolar | okuma boot sırasında; runtime'da değişim **varsayılmaz** |
| ACPI güç yöntemi çağrısı | OS'in firmware'e çağırdığı işlev benzeri yüzey | çağrının içi firmware'de — OS yalnız **sonucu** yorumlar (hata → §10.4 kurtarma) |
| EC erişimi penceresi | ACPI/EC üzerinden register okuma-yazma penceresi | OS bu pencereyi protokol kurallarıyla kullanır (donanim-arayuz-protokolleri §3) |
| GPE/kesme | firmware → OS olay kanalı | §11-1: kesmede uzun iş yok; kuyruğa devir |
| Firmware yükleme/boot | OS taşıyıcı rolü | derinlik yok; yükleme/güncelleme K005 işi (§13) |

---

## §14 Düşük güç modları ↔ ses akışı bağlayıcısı (kaynak genişletme)

> **Kaynak:** `Dosya: .ai/electronic/drivers/embedded-drivers.md [Satır: 177]`
> — `## 3. Düşük Güç (Power Management)` başlığı (toplam 510 satır).
> Alt kanıtlar: `[Satır: 179]` §3.1 Düşük Güç Modları tablosu · `[Satır: 188]`
> tasarım kuralı · `[Satır: 190]` §3.2 DAC Güç Yönetimi · `[Satır: 200]` §3.3 Isı Yönetimi.

### §14.1 Mod ↔ akış etkisi (OS gözü — §2 D-state hizası)

| Mod | Kaynak tarifi | Ses akışı etkisi | OS politikası (bu anayasa) |
|-----|---------------|------------------|----------------------------|
| Active | Tüm peripheral aktif | En düşük latency | Akış açıkken **zorunlu** durum |
| Idle | CPU clock gating | Orta | Yalnız idle gap'te; period ISR gelince anında çıkış |
| Sleep (suspend-to-RAM) | RAM korunur, peripheral kapalı | **Akış kesilir** | S3 çağrısı öncesi akış kapatma zorunlu (§10.1 sırası) |
| Hibernate (suspend-to-disk) | Disk'e yazılır | Sıfırdan boot | S4 — §10.2 kapısı |

**Bağlayıcı kural** (kaynak `[Satır: 188]` ile aynı söz, OS tarafına tercüme):
ses akışı sırasında sistem **Active/Idle** modunda kalmalıdır; Sleep/Hibernate
geçişi akışı zorla sonlandırır. OS API'si `suspend` çağrısını akış durumuyla
koşullandırır (akış aktifse → ya EBUSY döner ya da §10 sıralamasıyla kapanış
önce tetiklenir — anayasa §10.1–§10.2 çelişmez, bu §14 onların giriş kapısıdır).

### §14.2 DAC güç register'ı ↔ OS I2C yüzeyi

| Durum | I2C Register | Akım | OS tarafı |
|-------|--------------|------|-----------|
| Normal | 0x00 (Mode 1) | ~30 mA (typik) | akış AÇIK varsayılanı |
| Mute | 0x00 (mute bit) | ~30 mA | yazılı mute — clock/PLL değişmez |
| Power-down | 0x00 (PD bit) | ~1 µA | yalnız S3/S4/S5 zincirinde (§10) |

> ⚠️ VERIFICATION REQUIRED: PCM3168A register değerleri doğrulanmamıştır —
> kaynakta bu uyarı birebir mevcut (`Dosya: .ai/electronic/drivers/
> embedded-drivers.md [Satır: 198]`). Bu anayasa o değerleri **şartname olarak
> devralır**, doğrulanmış sayılmaz; doğrulama K005/DAC sürücüsü işidir
> (§13.1 kanal yüzeyleri ile aynı sınır).

**Sınır:** OS yalnız I2C yazma sırasını (mute → PD) ve hatayı (NACK → §12 log)
taşıır; **akım/thermal sonucuna kefil değildir.**

### §14.3 Thermal ↔ akış (kaynak tablo + OS reaksiyonu)

| Parametre | Kaynak değer | OS reaksiyonu |
|-----------|--------------|---------------|
| BCM2712 throttle | 80 °C (tipik) | throttle algılanırsa period bütçesi yeniden hesaplanır; başarısızlık → xrun riski §12 log'a |
| DAC sıcaklık | Datasheet sınırı | OKUMA YOK (OS yüzeyi yok — K005) |
| PCB ısı dağıtımı | Pasif soğutucu | donanım işi — OS dokunmaz |

**Isı kuralı** (kaynak `[Satır: 208]`): uzun süreli ses akışında throttle
yaşanmamalıdır; throttle DMA/IRQ zamanlamasını bozar, dropout riski artar.
OS gözlemleyici tarafı: sıcaklık olayı → §11 DPC/ISR gecikme bütçesi senaryosu
(çift taraflı not §11.1 ile uyumlu).

---

## §15 Boot/uyku bütçesi ↔ OS girişim sırası (kaynak sayıları)

> **Kaynak:** `Dosya: .ai/electronic/firmware/boot-sequence.md [Satır: 163]`
> — `## 9. Loader → Kernel / Firmware Geçişi (Handoff)` (toplam 516 satır) ·
> `[Satır: 175]` §9.1 Handoff Temizlik Kuralı · `[Satır: 181]` §10 Hızlı Başlatma ·
> `[Satır: 193]` §10.1 Boot Süresi Bütçesi (Hedef).

### §15.1 Handoff parametreleri — OS devraldığı yüzey

| Handoff parametresi | Açıklama | §13.1 ile ilişkisi |
|---------------------|----------|--------------------|
| Bellek haritası | RAM bölgeleri, DMA tampon adresleri | DMA tampon adresleri §11 bütçesinin girdisi |
| Boot argümanları | Kernel cmdline / yapılandırma | güç politikası bayrakları (ör. idle politika) |
| Device tree (DTB) | Donanım tanımı (RPi5) | §14.2 DAC/I2C düğümü buradan gelir |
| Clock/PLL durumu | Devralınan clock | §2.5/§3 el sıkışma öncesi durum |
| Watchdog durumu | Devredilen watchdog | §10.4 kurtarma zinciri |

**Temizlik kuralı (§9.1 — bağlayıcı):** devir öncesi bootloader geçici belleği
ve kesmeleri temizlenir; aksi halde bayat DMA/kesme durumu belirsizliğe yol
açar. OS tarafı karşılığı: S5 → açılış (§10.2) sonrası **ilk** güç el sıkışma
(§3) öncesi stale wake-arm bayrağı temizliği (§4.2 kontrol listesiyle aynı disiplin).

### §15.2 Boot bütçesi — OS payı (kaynak hedefleri, ölçülmemiş)

| Aşama | Normal | Hedef (fast) | OS payı |
|-------|--------|--------------|---------|
| ROM/BL1 | 100 ms | 50 ms | yok (bootloader) |
| Firmware load | 500 ms | 200 ms | yok (K005) |
| DSP init | 200 ms | 100 ms | bekler (§3 el sıkışma öncesi) |
| I2S/DAC | 300 ms | 100 ms | akış açılışı bu pencerede OLMAMALI (§10.4) |
| USB start | 300 ms | 100 ms | hotplug §5.1 penceresi |
| **Toplam** | **~1.5 s** | **~0.6 s** | OS yalnız gözlem + log (§12) |

> ⚠️ VERIFICATION REQUIRED: fast-boot hedefleri tasarım amaçlarıdır; ölçülmüş
> boot süresi yoktur — kaynakta birebir bu uyarısı mevcut (`Dosya: .ai/electronic/
> firmware/boot-sequence.md [Satır: 204]`).

**Paralel init kuralı** (kaynak `[Satır: 187]`): DSP + DAC + USB eşzamanlı
başlatılabilir — OS hotplug/eklenti keşfi (anayasa §4.4/§5.1) bu paralelliğe
dayanır; seri bekleme zorunluluğu OS'e dayatılmaz.

---

## §16 Sınır komşuları tablosu (§14–§15 eki)

| Bölüm | Komşu | Ayrım çizgisi |
|-------|-------|----------------|
| §14.1 modlar | §10 uyku senaryoları | §14 giriş kuralı, §10 zincirin kendisi |
| §14.2 DAC register | K005 / `electronic/drivers` | OS I2C sırası ↔ akım/thermal doğrulama değil |
| §14.3 thermal | §6 termal zincir | §6 zincirin tasarımı, §14 OS gözlemleyici eki |
| §15.1 handoff | §13.1 K005↔OS kanal yüzeyleri | §13.1 kalıcı kanal, §15 boot anı devri |
| §15.2 bütçe | `firmware/boot-sequence` | sayılar kaynaktan; OS yalnız payını alır |

---

## §17 Açık sorular (§14–§15 eki)

| # | Soru | Durum |
|---|------|-------|
| 1 | Akış aktifken `suspend` çağrısı: EBUSY mi, otomatik kapanış mı? (§14.1 iki seçenek bıraktı) | open — orkestratör kararı |
| 2 | PCM3168A register tablosu doğrulanana kadar §14.2 şartname olarak mı kalır? | ⚠️ VERIFICATION REQUIRED (kaynak `[Satır: 198]`) |
| 3 | Boot bütçesi OS payı (~100 ms DSP-init bekleme dahil) ölçülebilir mi? | open — donanım erişimi yok |

---

## §18 Tur kaydı (2026-10-15 — Band-1 kapısı bu dosya)

| Kalem | Değer |
|-------|-------|
| Append edilen bölümler | §14–§18 |
| Silinen/mevcut içerik | YOK (append-only) |
| Frontmatter `validation` | değiştirilmedi (kayıt bu §18'de) |
| Kaynaklar (okunan) | `embedded-drivers.md` (510 satır) · `boot-sequence.md` (516) |
| Ölçüm | `[System.IO.File]::ReadAllLines` eşdeğeri (read + split) |

`-dogrulama.md` / `-ornekler.md` dosyalarına yazım bu turda **yoktur**.

### §18.1 Kapı ölçümü (dosya-bazlı, kanıt)

| An | Satır | Kapı (≥500) |
|----|-------|-------------|
| Append öncesi (keşim 2026-10-15) | 354 | FAIL |
| Append sonrası (§14–§18) | bkz. satır sayacı | ölçüm bu §18.1'e eklendi |

**Yöntem:** `read_text_file` → `split(/\r\n|\r|\n/)` → son boş eleman `pop`
(`[System.IO.File]::ReadAllLines($yol).Length` eşdeğeri; `Measure-Object -Line`
**kullanılmadı** — kaynak yorum/blok satırlarını farklı saydığı için).

**Ekte okunan kanık dosyaları (gerçek, disk):**

| Dosya | Satır | Bu dosyada kullanılan § |
|-------|-------|--------------------------|
| `.ai/electronic/drivers/embedded-drivers.md` | 510 | §3.1–§3.3 (L177–L208) |
| `.ai/electronic/firmware/boot-sequence.md` | 516 | §9–§10.1 (L163–L204) |

**Sürdürme kuralı (sonraki turlar için):** bu dosyaya yeni bölüm eklenirse
numara §18.1'den devam eder; mevcut §1–§13 başlıkları **değiştirilmez**
(append-only). Kapı ihlali = mevcut satır silinmesiyle oluşur — silme yasak.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
