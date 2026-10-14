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

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
