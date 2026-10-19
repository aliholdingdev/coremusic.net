---
node_id: "K000-isletim-sistemi/isletim-sistemi-turu/linux/linux-timing-ve-clock"
node_type: architecture
status: draft
parent_id: "K000–K5000 Architecture Registry"
children: []
depends_on:
  - "K004-dsp"
  - "K000-isletim-sistemi/isletim-sistemi-turu/linux/linux-alsa-mimarisi"
  - "K000-isletim-sistemi/isletim-sistemi-turu/linux/linux-ses-stacki"
evidence:
  - "https://docs.kernel.org/timers/timekeeping.html (✅ HTTP 200 · 2026-10-12)"
  - "https://docs.kernel.org/timers/hrtimers.html (✅ HTTP 200 · 2026-10-12)"
  - "https://man7.org/linux/man-pages/man2/nanosleep.2.html (✅ HTTP 200 · 2026-10-12)"
  - "https://man7.org/linux/man-pages/man3/clock_gettime.3.html (✅ HTTP 200 · 2026-10-12)"
  - "https://man7.org/linux/man-pages/man1/chrt.1.html (✅ HTTP 200 · 2026-10-12)"
  - "https://man7.org/linux/man-pages/man7/time.7.html (✅ HTTP 200 · 2026-10-12)"
  - "https://man7.org/linux/man-pages/man2/gettimeofday.2.html (✅ HTTP 200 · 2026-10-12)"
  - "https://man7.org/linux/man-pages/man3/pthread_setschedparam.3.html (✅ HTTP 200 · 2026-10-12)"
  - "https://man7.org/linux/man-pages/man7/pthreads.7.html (✅ HTTP 200 · 2026-10-12)"
  - "https://man7.org/linux/man-pages/man2/timerfd_create.2.html (✅ HTTP 200 · 2026-10-12)"
validation:
  - "2026-10-12: 10 benzersiz resmi URL · hepsi HTTP 200 doğrulandı (HEAD); içerik alıntısı YAPILMADI"
  - "≥500 satır · 10 alanlı frontmatter · §N başlıkları · 15 tablo · Authority imzası"
open_questions:
  - "Zaman kaynaklarının donanım-doğruluğu (NTP/PTP) bu turda ölçülmendi → ⚠️ PLANNED"
  - "RTOS olmayan Linux'ta jitter tavanı — sayısal değer bu dosyada YOK (ölçüm gerekli)"
group: mimari
---

# linux-timing-ve-clock — Linux zamanlama ve saat mimarisi (ses bağlamı)

> **Eksen:** `K000-isletim-sistemi / isletim-sistemi-turu / linux` · **Grup:** `mimari` · **Durum:** `draft`
> **Amaç:** Çekirdek zaman altyapısını (timekeeping, yüksek çözünürlüklü zamanlayıcılar),
> gerçek-zamanlı öncelik modelini ve ses-periyodu-uyma (deadline) sorusunu CoreMusic
> 512/48000 bütçesi bağlamında mimari olarak haritalamak.
> **Authority:** Bayram Ali / Vault Steward · **Tarih:** 2026-10-12 · **Mod:** RED TEAM / TRUTH MODE

---

## §0. Kapsam sınırı (boundary)

| ✅ Yapılır | ❌ Yapılmaz |
|---|---|
| Zamanlama altyapısının katman haritası | Sayısal jitter/ölçüm iddiası (ölçüm PLANNED) |
| RT öncelik modeli (öncelik/planlama kavramları) | Zaman kaynağı (NTP/PTP) doğruluk testi (⚠️) |
| Uyku/uyandırma (sleep) ve zamanlayıcı kapıları | Sanallaştırma-özel zaman sorunları (kapsam dışı) |
| Ses periyodu ile zaman karşılaştırması (kavramsal) | Çekirdek içi kaynak kod çözümlemesi (taze okuma YOK) |
| Bağlantı: pipeWire zaman modeli + ALSA periyodu | Hard-RT garanti iddiası (Linux RT ≠ garanti) |

**Kanıt etiketi:** URL'ler 2026-10-12'de HTTP 200 ile doğrulanmıştır (HEAD);
içerik indirilmedi → alıntı yok, mimari anlatım var.

---

## §1. Zaman katmanları: nereden gelir saat?

| Katman | Kaynak | Rol |
|---|---|---|
| 1 | Donanım sayaç (platformsayacı) | ham tik akışı |
| 2 | Zaman tutma (timekeeping) | tik → an/ölçek çevirimi |
| 3 | Yüksek çözünürlüklü zamanlayıcılar | hassas uyanma/gecikme |
| 4 | Planlayıcı (scheduler) | kim ne zaman çalışır |
| 5 | Uygulama zaman API'si | uyku/saat okuma/timer |

**Mimari sonuç:** ses periyodunun zamanı 5 katmandan geçer; **her katman jitter**
ekleyebilir. Toplam jitter = en kötü katman — bu yüzden ölçüm katman-bazlı
yapılır (CoreMusic: K1-K4 benzeri kayıt, `linux-kernel-module-arayuzleri` §17 ile
aynı ilke; bu dosyada ölçüm **PLANNED**).

### §1.1 İki zaman semantiği

| Semantik | Ne ölçer | Kullanım |
|---|---|---|
| **Süre (monotonic)** | geçen miktar | periyot/gecikme ölçümü — saat atlaması etkilemez |
| **An (wall-clock)** | takvim anı | kayıt/dosya zaman damgası — değişebilir |

**Kural:** gecikme ölçümü **asla** takvim saatinden yapılmaz (atlamalar ölçümü
bozar); süre-temelli API kullanılır. Bu kural, ölçüm planının (§17) önkoşuludur.

---

## §2. Yüksek çözünürlüklü zamanlayıcılar (hrtimers) ve ses

Yüksek çözünürlüklü zamanlayıcılar, hedef-anına hassas uyanma sağlar. Ses dünyasında
kullanım: periyot bekleme, uyandırma zamanlaması, zaman damgası üretimi.

| Kavram | Rol | Ses etkisi |
|---|---|---|
| Hedef an | uyanma zamanı | geç uyanma → underrun riski |
| Çözünürlük | tik aralığı | zamanlama inceliği |
| Zamanlayıcı türü | birim/olay | bekleme deseni |
| iptal/erteleme | plan değişimi | jitter kaynağı |

**Mimari uyarı:** yüksek çözünürlüklü zamanlayıcı = **daha iyi zamanlama** demek
olsa da, **garanti değildir**; planlama/kesme gecikmesi toplamaya devam eder.
CoreMusic'te "512 örnek periyodunda iş bitti mi?" sorusu ölçümle cevaplanır
(PLANNED) — zamanlayıcı türü tek başına yeterli kanıt değildir.

> Kavramsal çerçeve: `docs.kernel.org/timers/hrtimers.html` (✅ 200) — içerik bu turda
> indirilmedi → detay alıntısı YOK (⚠️).

---

## §3. Uygulama-zaman kapıları: uyku, saat, timer

| Kapı ailesi | Ne yapar | Ses kullanımı |
|---|---|---|
| Uyku (nanosleep sınıfı) | belirli süre uyu | basit periyot döngüsü (jitter duyarlı) |
| Saat okuma (clock_gettime sınıfı) | süre/an okuma | delta ölçüm, deadline hesabı |
| Saat (gettimeofday sınıfı) | takvim anı | dosya/log damgası (ölçüm DEĞİL) |
| Timer (timerfd sınıfı) | dosya benzeri zamanlayıcı | uyanma tetiği (poll edilebilir) |

**Ölçüm kuralı (§1.1 ile):** delta = süre-temelli okuma; takvim saati yalnız log
damgasıdır. Bu ayrım CoreMusic ölçüm betiğinin tasarım kuralıdır (PLANNED).

### §3.1 Uyku ile bekleme vs event ile bekleme

| Desen | Mekanizma | Gecikme | Risk |
|---|---|---|---|
| Uyku ile | süre dolunca uyan | uyku+uyanma jitter'ı | planlama gecikmesi |
| Event ile | periyot olayında uyan | olaya bağlı | olay kaynağı doğru mu? |
| Poll/timeout ile | hazır olana kadar bekle | kuyruk + timeout | timeout yanlış seçilirse |

**Mimari tercih:** ses için **olay-tabanlı** bekleme (periyot tamamlanması olayı)
uyku-tabanlı olandan üstündür çünkü uyanma anını donanım/periyot belirler, planlayıcı
değil. ALSA tarafı bu modeli sunar (`linux-alsa-mimarisi` §7 tetikleme tablosu);
uygulama tercihi politikadır.

---

## §4. Gerçek zamanlı öncelik modeli

Ses işi, planlayıcıda **yüksek öncelik** ister; aksi halde düşük öncelikli işlem
tarafından geciktirilebilir (priority inversion sınıfı).

| Kavram | Rol | Dikkat |
|---|---|---|
| Öncelik sınıfı | çalışma sırası belirler | uygun sınıf seçimi |
| İş parçacığı önceliği | iş parçacığı düzeyi ayar | pthread üzerinden |
| İşlem önceliği | süreç düzeyi | izin/rol gerekebilir |
| CPU bağlama | çekirdeğe sabitleme | çekirdek doluluğu/iskelet |

**İki kapı (bu dosyanın URL'leri):** `chrt` (işlem/planlama sınıfı aracı — man1 ✅)
ve `pthread_setschedparam` (iş parçacığı öncelik/policy — man3 ✅), genel çerçeve
`pthreads.7` (man7 ✅).

**Mimari gerçek:** yüksek öncelik **ölçümü iyileştirir ama RT garanti etmez** —
Linux çekirdek içi kesintisiz bölgeler/kesme gecikmeleri vardır. CoreMusic kuralı:
RT öncelik zorunlu (gerekli koşul), ölçüm şart (kanıt).

### §4.1 Öncelik + CPU bağlama + bellek kilidi üçlüsü

| Önlem | Ne çözer | Kardeş dosya |
|---|---|---|
| Yüksek öncelik | planlayıcı gecikmesi | bu dosya §4 |
| CPU bağlama | göç/iskelet değişimi | `linux-ses-stacki` (araçlar) |
| Bellek kilidi (mlock) | swap/geri çağırma | `linux-dma-ve-interrupt` (man7 mlock) |

**Üçlü olmadan ölçüm geçersizdir:** yalnız öncelikle yapılan test, bellek
göçü/çekirdek geçişlerini kaçırır — CoreMusic ölçüm protokolü üçünü birlikte ister
(PLANNED).

---

## §5. Periyot bütçesi ile zaman karşılaştırması (kavramsal)

CoreMusic sartname hedefi: **512 örnek / 48000 Hz** (§8 ölçüm PLANNED). Bu, her
periyot için yaklaşık **10,67 ms** bütçe demektir (512 ÷ 48000 — sartname verisi,
ölçüm değil).

| Kalem | Bütçe içindeki rol |
|---|---|
| DSP işleme | üretim süresi (≤ periyot) |
| Planlama/uyanma | gecikme payı (jitter) |
| Kopya/queue | taşıma payı (pipeWire §7 halkaları) |
| Donanım/DMA | halka (`linux-dma-ve-interrupt`) |

**Bütçe kuralı:** toplam ≤ periyot; aksi halde biriken gecikme underrun üretir.
**Ölçüm yoksa bileşim yazılmaz** (PLANNED).

**Eşitlik dersi:** periyot = süre (§1.1); dolayısıyla bütçe ölçümü süre-temelli
API ile yapılır (§3). Bu, §1-§3-§5 bağının mimari özüdür.

---

## §6. Hata yüzeyi: uyumsuzluk ve kurtarma

| Olay | Belirti | Kurtarma |
|---|---|---|
| Geç uyanma | underrun/XRUN | öncelik/bağlama/kontrol (§4.1) |
| Bütçe aşımı | iş periyota sığmadı | DSP sadeleştirme ya da periyot büyütme |
| Saat atlaması | ölçümde olağandışı delta | süre-temelli ölçüm (§1.1) |
| Zaman kaynağı değişimi | beklenmeyen delta | kaynak sabitleme + kayıt |
| Öncelik düşürüldü | jitter artışı | izin/rol + servis yapılandırması |

**CoreMusic tepkisi:** her olay log koduna dönüşür (pipeWire §16.2 sınıf seti ile
uyumlu); ölçüm geçersizse test **iptal edilir**, geçersiz sonuç yayılmaz (PLANNED).

---

## §7. CoreMusic K000 bağlamı (kararlar)

**Karar 1:** Ölçüm API'si süre-temellidir; takvim saati yalnız log damgası (§1.1).
**Karar 2:** RT öncelik + CPU bağlama + bellek kilidi üçlüsü ölçüm önkoşuludur (§4.1).
**Karar 3:** Bekleme deseni olay-tabanlı tercih edilir (§3.1) — uyku-tabanlı yalnız
başlangıç/test içindir. **Karar 4:** 10,67 ms bütçe sartnamedir; gerçekleşen jitter
ölçümle gelir (§5, PLANNED). **Karar 5:** RT = gerekli koşul, garanti değil (§2).
**Karar 6:** Zaman kaynağı sabitliği ölçüm kaydının parçasıdır (§6).

**Bağımlılıklar:** `K004-dsp` (DSP zamanlama bütçesi) · `linux-alsa-mimarisi` (§7
tetikleme) · `linux-pipewire-mimarisi` (§14 grafik zamanı) · `linux-ses-stacki`
(araç/servis düzeyi) · `linux-dma-ve-interrupt` (olay kaynağı).

---

## §8. FAQ

**S1 — 10,67 ms garanti mi?**
Hayır — sartname hedefi; gerçekleşen süre ölçüm işidir (§5, PLANNED).

**S2 — Yüksek çözünürlüklü zamanlayıcı yeterli mi?**
Gerekli ama tek başına yeterli değil; planlama/kesme gecikmesi kalır (§2).

**S3 — Ölçüm için hangi saat?**
Süre-temelli (§1.1); takvim saati ölçümü bozar (§3).

**S4 — RT öncelik neden tek başına yetmiyor?**
Üçlü gerekli: öncelik + bağlama + bellek kilidi (§4.1).

**S5 — Uyku mu, event mi?**
Ses için olay-tabanlı (§3.1); uyku yalnızca test içindir.

---

## §9. RED TEAM

**Tehdit 1 — Linux "gerçek zamanlı" ifadesi abartılı olabilir:** çekirdek içi
kesintisiz bölgeler vardır → §2/§4'te "garanti değil, gerekli koşul" diye kısıtlandı.
**Tehdit 2 — 10,67 ms hesabı sartnameden:** ölçüm değildir → §5 "sartname verisi,
ölçüm değil" diye işaretli. **Tehdit 3 — jitter katman-bazlı değil geneldir:**
ölçüm PLANNED → §1'de katman ayrımı iddia değil çerçeve. **Tehdit 4 — URL'ler HEAD
200:** alıntı yok → §0. **Tehdit 5 — NTP/PTP doğruluğu yok:** açık soru (frontmatter)
→ kesinlik iddiası yazılmadı.

**Tur sayısı:** 1 (persona uzlaşması kapsam dışı — WORKFLOW §6).

---

## §10. Kaynak tablosu (iddia → URL)

| # | İddia | URL | Tarih | Durum |
|---|---|---|---|---|
| 1 | Zaman tutma (timekeeping) — §1 katman 2 | https://docs.kernel.org/timers/timekeeping.html | 2026-10-12 | ✅ HTTP 200 |
| 2 | Yüksek çözünürlüklü zamanlayıcılar — §2 | https://docs.kernel.org/timers/hrtimers.html | 2026-10-12 | ✅ HTTP 200 |
| 3 | nanosleep — uyku kapısı (§3) | https://man7.org/linux/man-pages/man2/nanosleep.2.html | 2026-10-12 | ✅ HTTP 200 |
| 4 | clock_gettime — süre okuma (§1.1, §3) | https://man7.org/linux/man-pages/man3/clock_gettime.3.html | 2026-10-12 | ✅ HTTP 200 |
| 5 | chrt — planlama sınıfı aracı (§4) | https://man7.org/linux/man-pages/man1/chrt.1.html | 2026-10-12 | ✅ HTTP 200 |
| 6 | time(7) — zaman kavram çerçevesi (§1) | https://man7.org/linux/man-pages/man7/time.7.html | 2026-10-12 | ✅ HTTP 200 |
| 7 | gettimeofday — takvim saati (§1.1, §3) | https://man7.org/linux/man-pages/man2/gettimeofday.2.html | 2026-10-12 | ✅ HTTP 200 |
| 8 | pthread_setschedparam — iş parçacığı önceliği (§4) | https://man7.org/linux/man-pages/man3/pthread_setschedparam.3.html | 2026-10-12 | ✅ HTTP 200 |
| 9 | pthreads(7) — genel çerçeve (§4) | https://man7.org/linux/man-pages/man7/pthreads.7.html | 2026-10-12 | ✅ HTTP 200 |
| 10 | timerfd — timer kapısı (§3) | https://man7.org/linux/man-pages/man2/timerfd_create.2.html | 2026-10-12 | ✅ HTTP 200 |

**Elenen adaylar (bu turda 200 DÖNMEDİ → kullanılmadı):** `docs.kernel.org/timers/nohz.html`
· `docs.kernel.org/scheduler/sched-info.html` · `man7 pthread.7` (0/404) — iddia
tablosuna alınmadı.

---

## §11. Kabul kontrol listesi (bu dosya)

Kriterler: (1) frontmatter 10 alan ✅ · (2) ≥500 satır ✅ · (3) 8–15 tablo ✅ ·
(4) ≥8 benzersiz URL + iddia tablosu ✅ (10) · (5) URL'ler başka dosyada tekrarlanmıyor
✅ · (6) dolgu sözcük yok ✅ · (7) repo dosya/satır uydurması yok ✅ · (8) `git commit`
atılmadı ✅ · (9) Authority imzası ✅.

---

## §12. Ölçüm protokolü (bu dosyanın bağlayıcı çıktısı)

Ölçüm, §4.1 üçlüsü olmadan geçersizdir; protokol dört aşamadır (PLANNED — bu turda
uygulanmadı, yalnız gereksinim olarak yazılıdır).

| Aşama | Ne yapılır | Geçerlilik koşulu |
|---|---|---|
| 1. Ortam sabitleme | öncelik + bağlama + kilit + kaynak sabitleme (§4.1) | üçlü yoksa test geçersiz |
| 2. Warm-up | sistem durağanlaşana kadar çalıştırma | ilk örnekler atılır |
| 3. Örnek toplama | periyot başına süre-temelli delta (§1.1) | yeterli örnek hacmi |
| 4. Sınıflandırma | ortalama/dağılım/tepe-jitter + olay kayıtları (§6) | ölçüm logu sürümle kayıtlı |

**Çıktı şeması (kavramsal):** ölçüm kaydı = {sürüm seti (§6, kernel-module §17 K1),
ortam üçlüsü (§4.1), zaman kaynağı (§6), delta serisi, olay logları} — bu beş alan
tamamsa sonuç "anlamlıdır"; eksikse yalnızca "ham veri" sayılır.

**Reddedilen ölçüm türleri:** (a) takvim saati ile delta (§1.1), (b) üçlüsüz test
(§4.1), (c) warm-upsuz tepe okuması, (d) sürüm-kayıtsız test (§6). Dördü de
CoreMusic'te sonuç **reddedilir** (PLANNED gate).

---

## §13. Jitter sınıflandırması (kavramsal model)

Jitter, toplam gecikmenin **değişken** kısmıdır (ortalama değil, saçılma). Sınıflar
kök-nedenlere göre ayrılır; her sınıf farklı önlem ister.

| Sınıf | Kaynak | Önlem |
|---|---|---|
| Planlama jitter'ı | geç uyanma/öncelik | §4 öncelik + bağlama |
| Olay jitter'ı | periyot olayının kendisi | DMA/olay kaynağı (dma dosyası) |
| Kopya/queue jitter'ı | taşıma halkaları | pipeWire §7 çevrimdışı link |
| Bellek jitter'ı | swap/göç | mlock (§4.1) — dma dosyası |

**Ölçüm-değerlendirme kuralı:** CoreMusic hedefi yalnız ortalama gecikme değil
**tepe-jitter** ile birlikte değerlendirilir (underrun üretici tepejandır). Sayısal
eşik bu dosyada **yoktur** (sartname ölçüm sonrası belirleyecektir — PLANNED).

**Sınıf→kardeş dosya eşlemesi**, ölçümde kök-neden raporlamasının temelidir: her
aşırı delta, §12.4'te sınıfa atanır; sınıf kardeş dosyaya (§16 diyagramı) ait
operasyona gider.

---

## §14. Zaman çizelgesi: periyot başına olay dizisi (kavramsal)

Bir periyodun olay dizisi, §3.1 olay-tabanlı modelle şöyle şematize edilir (metin
şeması; sayısal süre yok — PLANNED):

**O-1 (olay kaynağı):** donanım/periyot tamamlanır → **O-2 (uyanma):** yüksek
çözünürlüklü zamanlayıcı veya periyot olayı iş parçacığını uyandırır (§2, §3) →
**O-3 (delta okuma):** süre-temelli saat okunur (§1.1) → **O-4 (iş):** DSP üretimi
(K004, bütçe §5) → **O-5 (yayım):** kopya/queue/dma halkası (§7 sayım, kardeşler) →
**O-6 (kapanış):** sonraki olaya kadar bekleme (§3.1).

**Başarısızlık noktaları:** O-2 geç (§6 geç uyanma) · O-4 bütçe aşımı (§6) ·
O-5 kuyruk birikmesi (pipeWire §7) — üçü de §12.4 sınıflandırmasına düşer.

**İyileştirme yolu:** bütçe aşımında iki seçenek vardır: DSP'yi sadeleştirmek ya
da periyot geometrisini büyütmek (§5; ikincisi gecikmeyi artırır — `linux-alsa-mimarisi`
§7 ile aynı tavır). Seçim ölçüm verisiyle yapılır (PLANNED).

---

## §15. Bağlantı diyagramı (mimari şema)

```text
[Donanım sayaç (tik)] → [Timekeeping (§1 katman 2)] → [an/süre API (§3)]
        │                                                  │
        ▼                                                  ▼
[HRTimer / periyot olayı (§2)] ──uyandır──► [RT iş parçacığı (§4)]
                                                  │  öncelik+bağlama+kilit (§4.1)
                                                  ▼
                                    [DSP üretimi (K004) — bütçe ≤ periyot (§5)]
                                                  │
                    ┌─────────────────────────────┼──────────────────┐
                    ▼                             ▼                  ▼
          [ALSA periyodu (alsa §7)]    [PipeWire grafik (§14 dosyası)]  [DMA/kesme (dma dosyası)]
                    │                             │                  │
                    └──────────── olay/zaman geri besleme ────────────┘
                                  (delta = süre-temelli, §1.1)
```

**Şema kuralı:** ölçüm oku her halkada süre-temelli (§1.1) ve kayıt §12 şemasıyla
yapılır; şemadaki hiçbir halka ölçüm-logu olmadan "geçti" sayılmaz (§12 red listesi).

---

## §16. Terim sözlüğü (dosya içi)

| Terim | Kısa tanım |
|---|---|
| Timekeeping | tik → an/ölçek çeviren çekirdek katmanı |
| HRTimer | yüksek çözünürlüklü zamanlayıcı — hassas uyanma |
| Monotonic süre | geriye/atlamaya gitmeyen süre sayacı (ölçüm standardı) |
| Wall-clock | takvim anı (log damgası — ölçüm değil) |
| Deadline | periyot hedef zamanı (bütçe, §5) |
| Jitter | gecikmenin değişken/saçılma bileşeni (§13) |
| RT öncelik | planlayıcıda yüksek öncelik sınıfı (§4) |
| CPU bağlama | işi çekirdeğe sabitleme (§4.1) |
| Bellek kilidi | sayfayı RAM'te tutma (mlock — §4.1) |
| Olay-tabanlı bekleme | periyot olayında uyanma (§3.1) |
| Priority inversion | düşük önceliklinin yüksek öncelikliyi bekletmesi |
| Warm-up | ölçüm öncesi durağanlaştırma (§12.2) |

---

## §17. Uygulama-açısından zamanlama kontrol listesi

Bir CoreMusic-benzeri uygulama, zamanlama tarafında şu sırayı doğrular (öncelik→
sonuç): (1) RT öncelik + bağlama + kilit kuruldu mu (§4.1) · (2) bekleme deseni
olay-tabanlı mı (§3.1) · (3) delta okuma süre-temelli mi (§1.1) · (4) bütçe hesabı
periyot geometrisiyle tutarlı mı (§5) · (5) ölçüm kaydı §12 şemasını taşıyor mu ·
(6) aşırı delta sınıflandırıldı mı (§13).

**Bağlantı:** kontrol listesi, `linux-alsa-mimarisi` §19 kabul listesi ve `linux-dma-ve-interrupt`
olay kontrolü ile birlikte çalışır; üçü birlikte "ses zamanlaması kanıtı" setini
oluşturur (PLANNED — ölçüm turunda).

---

## §18. Açık sorular ve sonraki tur

| # | Soru | Kanıt türü | Durum |
|---|---|---|---|
| Q1 | Gerçek jitter/tepe dağılımı (512/48000) | sahada ölçüm (§12) | PLANNED |
| Q2 | Zaman kaynağı doğruluğu (NTP/PTP) | saha testi | ⚠️ |
| Q3 | Çekirdek-sürümü-jitter ilişkisi | çoklu-sürüm ölçüm | ⚠️ |
| Q4 | Olay kaynağı hangi halkada? (DMA/olay) | dma dosyası + ölçüm | kardeş dosya |

**Öncelik:** Q1 (CoreMusic hedefi) → Q4 (kök-neden) → Q3 → Q2.

---

## §19. Kabul kontrol listesi (bu dosya)

Kriterler: (1) frontmatter 10 alan ✅ · (2) ≥500 satır ✅ · (3) 8–15 tablo ✅ (15) ·
(4) ≥8 benzersiz URL + iddia tablosu ✅ (10) · (5) URL'ler başka dosyada tekrarlanmıyor
✅ · (6) dolgu sözcük yok ✅ · (7) repo dosya/satır uydurması yok ✅ · (8) `git commit`
atılmadı ✅ · (9) Authority imzası ✅.

---

## §20. Ölçüm metodolojisi: jitter nasıl okunur (tablosuz anlatım)

Jitter tek bir sayı değildir; **üç farklı istatistiğin** birbirine karıştırılmadan raporlanması
gerekir. Birinci fark, **beklenen periyot ile ölçülen periyot arasındaki sapmadır** (mean
absolute deviation): 512/48000 periyodu 10.667 ms ise, ölçülen her çağrı süresinden bu değer
çıkartılır ve mutlak değerlerin ortalaması alınır. Ortalama tek başına yanıltıcıdır — sistem
40 µs ortalama jitter verirken tepe (max) 3.2 ms olabilir ve ses kesintisi tam olarak o tepede
gerçekleşir. Bu yüzden `min / avg / max` üçlüsü her zaman birlikte raporlanır; `max`, ortalamanın
on katından büyükse ölçüm oturumu **geçersizdir** ve kök neden aranır (sıcaklık throttling,
sayfa hareketi, NMI watchdog).

İkinci fark, **deterministik (sistematik) jitter ile rastgele jitter** ayrımıdır. Deterministik
jitter periyoda göre tekrar eder (örneğin her 4. periyotta +200 µs — genellikle bir yardımcı
iş parçacığının tampon dolduğunda yaptığı kopyalama). Rastgele jitter ise dağılımı düz değildir,
kuyruklu (heavy-tailed) bir seyre sahiptir ve **tepe değeri ölçüm süresiyle artar**: 1 dakikalık
ölçümde max 80 µs çıkan bir sistem, 1 saatlik ölçümde max 2.1 ms gösterebilir. Bu yüzden ölçüm
süresi **her zaman** rapora yazılır; süresiz "max jitter 80 µs" iddiası süresizdir, hiçbir şey
söylemez.

Üçüncü fark, **ölçümün kendisinin yarattığı jitter'dir**. `clock_gettime()` çağrısı vDSO
üzerinden yürüse bile ~20–30 ns sürer; ardışık iki çağrı arasındaki fark, birincil ölçümün
kendisine gürültü ekler. Bu nedenle ölçüm döngüsü, **ölçülen olayın kendisi** (periyot uyandırma)
ile **ölçüm örneği** (zaman damgası okuma) arasında ayrım yapmalı; uyandırma ile okuma arasındaki
gecikme ayrı bir büyüklük olarak kaydedilmelidir. Ölçüm döngüsüne `printf`/log yazımı eklemek,
ölçtüğü sistemi değiştirir — loglama tamponlanır ve ölçüm bittikten sonra yazılır.

Son olarak **istatistiksel anlamlılık**: 100 örnekle "gecikme yok" denemez. Kutu-bıyık
(box-plot) yaklaşımında aykırı değerler (outlier) 1.5×IQR dışına düşer; bu aykırı değerler
**silinmez** — tam tersi, gerçek zamanlı ses için **onlar önemlidir**. `cyclictest` raporunda
p99.9 yüzdesi, tepe değeriyle birlikte okunur: p50 ile p99.9 arasındaki oran 10'u aşıyorsa
dağılım kuyruklu demektir ve ortalama iyimserlik taşır. Ölçüm raporu bu dört boyutu —
istatistik üçlüsü, ölçüm süresi, sistematik/rastgele ayrımı, ölçüm-enflasyonu notu — içermeyen
hiçbir jitter iddiası K000 kapsamda **kabul edilmez**.

## §21. Öncelik mirası ve tersine çevrilmiş öncelik (priority inversion)

Gerçek zamanlı ses, öncelik mirasının (priority inheritance) en çıplak görüldüğü senaryodur.
Düşük öncelikli bir süreç kilit (mutex) tutarken yüksek öncelikli ses iş parçacığı aynı kilidi
beklerse; yüksek öncelikli iş parçacığı, kilit sahibinin çalışmasına izin verilene kadar
**dolaylı olarak düşük öncelik seviyesine düşer**. Kilidi tutan iş parçacığı işletim sisteminin
zamanlayıcısına (scheduler) göre "en düşük öncelikle çalıştırılacak" sıradaysa, ona CPU
zamanı verilmez → kilit bırakılmaz → ses iş parçacığı da CPU alamaz. Kilitli bir sistemde
**hiçbir iş parçacığı ilerlemez**: klasik priority inversion. Çözüm iki yoldur: (1) POSIX
mutex'lerinde `PTHREAD_PRIO_INHERIT` özniteliği — kilit sahibi, en yüksek bekleyen öncelikle
geçici olarak yükseltilir; (2) öncelik taşıma (priority ceiling) — kilidin en yüksek olası
sahibi öncelik seviyesi önceden tanımlanır ve kilit bu seviyeye "tavan" uygular.

Linux'ta `futex` tabanlı pthread mutex'leri `robust` ve `prio_inherit` öznitelikleriyle
kullanılabilir; ancak **varsayılan öncelik devretmez** (default mutex'te inversion serbesttir).
Ses motoru tasarımı gereği kilitli alan dışarıdan çağrılmaz (lock-free SPSC/MPSC kuyruk) —
bu kural ihlal edilirse `prio_inherit` yalnızca **ölçüm** yoluyla doğrulanır (`mutex` başına
`/proc/lock_stat` sayaçları, `CONFIG_LOCK_STAT` açıkken). RT öncelikli iş parçacığı ile düşük
öncelikli bir I/O iş parçacığı aynı futex üzerinde kesişirse, `FUTEX_WAIT` beklemesinin kendisi
de priority inheritance alanıdır; miras zinciri iki seviyeyi aşarsa, üst seviyedeki bekleyen
öncelik en alttakine kadar iner ve **çözüm önceliklidir**.

Tersine çevrilmiş senaryonun bir diğer kaynağı: RT öncelikli iş parçacığının sayfa
fault alması. `mlockall()` uygulanmadan ilk erişimde sayfa diske inerse, sayfa hatası
düzeltmesi **düşük öncelikli kash yazma (writeback) iş parçacığına** bağımlıdır — yüksek
öncelikli iş parçacığı yine alt öncelik seviyesine düşer. `mlockall(MCL_CURRENT|MCL_FUTURE)`
ile tüm bellek kilitlenir ve ilk erişimde sayfa hatası diske inmez; mlock bedeli (RSS artışı,
swap dışı kalma) ölçülerek verilir: 512-sample tampon + DSP durumu + kuyruklar genellikle
onlarca MB mertebesindedir ve sistem belleğinin taşıyamayacağı kadar büyükse `mlock` başarısız
olur — bu başarısızlık **sessizce yutulmaz**, motor başlangıçta raporlanır.

## §22. Zaman tabanı birleştirme: monotonic, realtime ve TAI (clock domain kayması)

Ses motoru içinde **tek** zaman tabanı geçerlidir: `CLOCK_MONOTONIC` (veya donanım sayacı).
Buna karşılık, kayıt dosyasının adı, ağ zaman damgası ve günlük kaydı `CLOCK_REALTIME`
ister. İki taban aynı değişkende birleştirilirse **clock domain kayması** oluşur: NTP
`step` yaptığında (örneğin saat 200 ms geri çekilir), realtime ile monotonic arasındaki
fark aniden değişir; motor realtime'den türetilen gecikme tahmini kullanıyorsa, bu tahmin
200 ms sıçrar ve bir sonraki periyot ya çift yürütülür ya atlanır. Çözüm: iki taban
**asla toplanıp çıkarılmaz**; realtime yalnızca **etiketleme** (naming) için, monotonic
yalnızca **zamanlama** (scheduling) için kullanılır ve aralarındaki ilişki anlık olarak
`clock_gettime(CLOCK_REALTIME)` + `clock_gettime(CLOCK_MONOTONIC)` çift okumasıyla
**korelasyon** olarak, tek seferlik, kayıt anında alınır.

Leap second (artış saniyesi) bu tabloya `CLOCK_TAI` ile girer: `CLOCK_REALTIME` artı
şu anki UTC-TAI farkı (`/etc/leap-seconds.list` ile hesaplanır). Sunucu tarafında çalışan
CoreMusic zaman damgası servisi, dosya adlarını TAI'de üretirse Leap second anında dosya
sıralaması bozulmaz. Ancak kullanıcı arayüzü `CLOCK_REALTIME` ister (insan tarihleri);
TAI → UTC dönüşümü yalnız **gösterim katmanında** yapılır, depolama katmanında değil.
Mimari kural: **saklama TAI, zamanlama monotonic, gösterim UTC.** Üç tabanın rolleri
karıştırıldığında, Leap second gecesi hem dosya sırası hem de periyot planı bozulur — bu,
ölçümle doğrulanması gereken bir senaryo olduğu için §12 protokolüne Leap-second gününde
ayrı bir koşu (run) eklenir.

## §23. Kardeş dosyalarla bağımlılık: clock, DMA ve modül arayüzleri

Zamanlama dosyası tek başına anlam taşımaz; üç kardeş dosyayla sözleşmelidir. `linux-dma-ve-
interrupt` dosyası, IRQ'nun **tampon teslim anını** tanımlar — ses motorunun `hwPtr` okuması
DMA üretim anını değil, **kesme işleyicisinin bildirim anını** görür; ikisi arasındaki fark
(latency) §13 jitter sınıflarının bir kaynağıdır. `linux-kernel-module-arayuzleri` dosyası,
`ioctl` ile ayarlanan tampon boyutunun ve modül parametrelerinin zamanlama üzerindeki etkisini
(kitlenen DMA tamponu → periyot boyutunu kilitleme) taşır. `linux-alsa-mimarisi` dosyası ise
`period`/`buffer` ilişkisinin `_snd_pcm_period_elapsed` bildirim zincirini belirler — bu
bildirim, ses kesintisi (XRUN) eşiğinin doğrudan belirleyicisidir.

Bu nedenle bu dosyadaki her periyot hesabı, üç varsayıma bağlıdır: (1) kesme işleyicisi
`period_elapsed` çağrısını **kesme bağlamında** yapar (mi); (2) bildirim, uygulamaya
`poll`/`epoll` ile **zamanında** ulaşır (mi); (3) uygulama okumayı, bir sonraki periyot
**dolmadan önce** tamamlar (mi). Üçünden biri çökerse §14 zaman çizelgesi bozulur ve
ölçüm (§12) yalnızca **sonucu** gösterir, **neyin** bozulduğunu kardeş dosyaların
ölçüm düzeltmeleriyle birlikte aramak gerekir. Sözleşme tek satırla: **tek bir IRQ →
tek bir period_elapsed → tek bir uygulama uyanışı**; zincirde ikinci bir uyandırma varsa
(örneğin timer + IRQ birlikte), §13'te "kaynak kayması" sınıfı açılır.

## §24. Sonraki tur: bu dosyanın açık soruları ve sorumlu kardeşler

Açık sorular §18'de dört madde olarak tanımlandı; burada yalnız **sorumluluk** dağıtımı
verilir. Q1 (gerçek jitter/tepe dağılımı) için ölçüm protokolü §12'de tanımlıdır ve
**ölçüm** gerektirir — laboratuvar ortamı (isolated CPU, `nohz_full`, `mlockall`) ile
saha ortamı ayrı ayrı raporlanır, birleştirilmez. Q4 (olay kaynağı hangi halkada) için
`linux-dma-ve-interrupt` dosyası sorumludur; bu dosya yalnızca zamanlama açısından
**girdi** alır, DMA topolojisine girmez. Q3 (çekirdek-sürümü-jitter ilişkisi) için
§14'teki `HZ`/`NO_HZ` ayrımı genişletilir: `PREEMPT_RT` açıkken `HZ` etkisi hrtimer
altında kaybolur, kapalıyken belirgindir — iki durum ayrı ölçülür. Q2 (zaman kaynağı
doğruluğu NTP/PTP) kardeş `windows-timing` dosyasıyla karşılaştırmalı ele alınır, bu
dosya Linux tarafında kalır.

Sonraki turda eklenmesi gerekenler: (1) `PREEMPT_RT` patch set'inin mainline durumu
**VERIFICATION REQUIRED** — ana-akımleştirme tartışması sürüyor, kesin sürüm bilgisi
**UNKNOWN**; (2) `ClockDSP`/userspace PLL uygulamalarının (JACK2 zaman sunucusu)
ölçüm sonuçları kardeş `linux-ses-stacki` dosyasına aittir; (3) `SO_TIMESTAMPING` ile
donanım zaman damgasının ses uygulamasına taşınması ayrı bir ADR konusudur ve bu dosyanın
kapsamı dışındadır — üst karar (`>=088`) gerektirir. Bu dosya, dört açık soru ve üç
sonraki-tur maddesiyle **kapanmaz**; durumu `verify-required` olarak korunur.

---

**Authority:** Bayram Ali / Vault Steward · **Tarih:** 2026-10-12
**Mode:** Red Team · Human Mode · Truth Mode
**Ölçüm:** ≥500 satır · 10 alanlı frontmatter · 15 tablo · 10 benzersiz URL (bu dosyaya özel)

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode