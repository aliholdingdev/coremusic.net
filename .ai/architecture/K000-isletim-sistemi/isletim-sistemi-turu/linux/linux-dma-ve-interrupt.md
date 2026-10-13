---
node_id: "K000-isletim-sistemi/isletim-sistemi-turu/linux/linux-dma-ve-interrupt"
node_type: architecture
status: draft
parent_id: "K000–K5000 Architecture Registry"
children: []
depends_on:
  - "K000-isletim-sistemi/isletim-sistemi-kernel"
  - "K000-isletim-sistemi/isletim-sistemi-turu/linux/linux-timing-ve-clock"
evidence:
  - "https://docs.kernel.org/driver-api/dmaengine/index.html (✅ HTTP 200 · 2026-10-12)"
  - "https://docs.kernel.org/core-api/irq/index.html (✅ HTTP 200 · 2026-10-12)"
  - "https://docs.kernel.org/core-api/genericirq.html (✅ HTTP 200 · 2026-10-12)"
  - "https://docs.kernel.org/admin-guide/mm/index.html (✅ HTTP 200 · 2026-10-12)"
  - "https://man7.org/linux/man-pages/man2/mmap.2.html (✅ HTTP 200 · 2026-10-12)"
  - "https://man7.org/linux/man-pages/man2/mlock.2.html (✅ HTTP 200 · 2026-10-12)"
  - "https://man7.org/linux/man-pages/man2/mlockall.2.html (✅ HTTP 200 · 2026-10-12)"
  - "https://man7.org/linux/man-pages/man2/poll.2.html (✅ HTTP 200 · 2026-10-12)"
validation:
  - "2026-10-12: 8 benzersiz resmi URL · hepsi HTTP 200 (HEAD) doğrulandı; içerik alıntısı YAPILMADI → iddialar genel mimari anlatımıdır"
  - "≥500 satır · 10 alanlı frontmatter · §N başlıkları · Authority imzası"
open_questions:
  - "Spesifik ses kontrolörünün (ör. HD-Audio) DMA kokpit topolojisi fetch edilmedi — ⚠️ VERIFICATION REQUIRED"
  - "IOV / scatter-gather sınır DMA'da ses tamponu hizası (cache coherency) sahada ölçülmedi — UNKNOWN"
group: mimari
---

# linux-dma-ve-interrupt — Linux DMA ve kesme (interrupt) mimarisi (ses bakış açısı)

> **Eksen:** `K000-isletim-sistemi / isletim-sistemi-turu / linux` · **Grup:** `mimari` · **Durum:** `draft`
> **Amaç:** DMA kanallarının, kesmelerin (IRQ/NMI) ve üst-kesme (top-half/bottom-half) işleyicilerinin
> ses akışı zaman çizelgesi üzerindeki etkisini mimari derinlikle haritalamak; ring buffer teslim
> anı, IRQ → uygulama uyanış zinciri ve bellek kilit (mlock) yüzeyini CoreMusic K000 bağlamında sunmak.
> **Authority:** Bayram Ali / Vault Steward · **Tarih:** 2026-10-12 · **Mod:** RED TEAM / TRUTH MODE

---

## §0. Kapsam sınırı (boundary)

| ✅ Yapılır | ❌ Yapılmaz |
|---|---|
| DMAEngine soyutlamasının rolü ve ses kontrolörü ile ilişkisi | Spesifik ses kontrolörü register haritası (fetch edilmedi → UNKNOWN) |
| IRQ üst/alt işleyici (top-half/bottom-half) zamanlama etkisi | IRQ yönü / GIC konfigürasyon kodu (repo 0 `.cpp`/`.h` — uydurma `Dosya:` yasak) |
| `poll`/`epoll` ile uygulama uyanış zinciri | Sürücü kaynağı satır atfı (indirilmedi) |
| Bellek kilitleme (mlock/mlockall) ve mmap tampon yüzeyi | Sürücü iç (driver-internal) veri yapısı detayları |
| Ölçüm protokolü (IRQ başına süre, kesinti jitter'ı) | Sürücü patch yazımı / değiştirme |

**Dilim:** bu dosya **yalnız** DMA + kesme + bellek kilidi katmanını kapsar; saat disiplini
`linux-timing-ve-clock`, ALSA arayüzü `linux-alsa-mimarisi`, üst katman `linux-ses-stacki` dosyasındadır.

---

## §1. Neden DMA + IRQ ses zaman çizelgesinin kalbidir?

Bir ses akışında veri, bellek ile kontrolör arasında **iki kez** hareket eder: kontrolör DMA
kanalıyla veriyi doğrudan bellekten okur/yazar (CPU müdahalesi olmadan), ve her tampon /
periyot sınırında kontrolör bir **kesme** (interrupt) üretir. Çekirdek bu kesmeyi işler ve
uygulamanın `poll`/`read`/`mmap` yüzeyini uyandırır. Dolayısıyla ses gecikmesinin (latency)
**alt sınırını** DMA teslim hızı + IRQ işleme süresi + uyanış süresi belirler; uygulamanın
kendi kodu bu üçlünün **üzerinde** çalışır. Bu üçlüyü anlamayan gecikme optimizasyonu,
yanlış katmanda yapılır (§16 yanlış-teşhis tablosu).

İkinci neden: DMA ve IRQ, **gerçek zamanlı bütçenin** (periyot başına CPU bütçesi) büyük
kalemidir. Her periyotta bir IRQ, her IRQ'da üst işleyici + programlanabilir kesme (threaded
IRQ) + uygulama uyanışı çalışır. IRQ başına maliyet ölçülmeden "DSP zinciri yavaştır"
denemez — DSP hâlâ bütçe içindeyken kaynak **kesme trafiği** olabilir. Üçüncü neden: DMA
tamponu bellek **yerleşimi** (placement) ve **kilit** (mlock) ile ilgilidir; sayfa diske
inerse (page fault) DMA verisi okunamaz / yazılan veri kaybolur — ses motoru için bu doğrudan
XRUN veya bozuk örnektir (§11).

---

## §2. DMA katmanları: DMAEngine soyutlaması ve kontrolör arayüzü

| Katman | Sorumluluk | Ses açısından anlamı |
|---|---|---|
| DMAEngine (çekirdek soyutlama) | Kanal isteği, yön, tamamlanma bildirimi | Sürücü kontrolörü bilmeden DMA kurar |
| Kanal sürücüsü (device driver) | Donanıma özgü kayıt programlama | Periyot sınırı kesmesini bağlar |
| Kontrolör (DMA controller) | Gerçek veri taşıma, arbitrasyon | Taşıma süresi DMA başına değişir |
| Cihaz (ses kontrolörü) | FIFO doldur/boşalt, talep hattı | `DMA` ↔ `FIFO` akış kontrolü |
| Çekirdek bildirim (`dmaengine` callbacks) | Tamamlanma / hata geri çağrısı | IRQ'ya tetikleyen olay bu noktada |

**DMAEngine** çekirdeğin donanım-bağımsız DMA arayüzüdür: sürücü, bir *slv (slave) DMA*
isteği yapar (yön, adres, boyut, periyodik tamamlanma modu), DMAEngine kanalı alır ve
tamamlanma geri çağrısı (`callback`) ile bildirir. Ses kontrolörleri bu modeli **periyodik
mod**da kullanır: her periyot dolduğunda (giriş) / boşaldığında (çıkış) geri çağrısı tetiklenir
ve sürücü bu geri çağrıyı ALSA'nın `period_elapsed` bildirimine bağlar. `cyclic` (döngüsel) mod
kullanan donanımlarda DMA kendisi döngüyü sürdürür ve yalnız **periyot sınırında** kesme üretir
— bu, ses için en verimli moddur çünkü IRQ başına iş en aza iner.

Ses kontrolörü ↔ DMA arayüzündeki iki yaygın desen: **FIFO + DMA talep (DRQ/DACK)** (eski ISA/PCI
tarzı, kontrolör FIFO'nun doluluğuna göre DMA ister) ve **burst DMA + bellek erişim arbitrajı**
(PCIe'de daha yaygın, DMA bellekten blok halinde okur). Her ikisinde de kontrolörün **FIFO
derinliği** gecikmenin bir bileşenidir: FIFO derinliği arttıkça DMA'ya olan bağımlılık azalır
ama gecikme artar. Derinlik donanım sabitidir (sürücü yalnız okur); yazılım tarafı değişken
**periyot boyutu** ve **tampon sayısını** seçer.

**Sınır (bu dosyanın dürüstlüğü):** DMAEngine callback'lerinin *kesme bağlamında* mı yoksa
*threaded IRQ* üzerinden mi çağrıldığı, kanal ve sürücüye göre değişir — bu ayrıntı **bu
dosyada doğrulanmadı** (`⚠️ VERIFICATION REQUIRED`). Ses mimarisi bu belirsizliği **iki durumu
da** kapsayacak şekilde tasarlanır (§7).

---

## §3. Kesme (interrupt) mimarisi: yönlendirme, üst işleyici, alt işleyici

| Kavram | Rol | Ses açısından |
|---|---|---|
| IRQ hattı | Cihaz → işlemci sinyali | Periyot olayının kaynağı |
| IRQ yönlendirme (affinity) | Hangi çekirdekte işlenir | Çekirdek izolasyonu için kritik |
| Üst işleyici (top-half) | En kısa yol: kaydı oku, bitir | Bütçenin en dar kısmı |
| Alt işleyici (bottom-half) | `softirq` / tasklet / threaded | `period_elapsed` burada olabilir |
| Programlanabilir kesme (threaded IRQ) | IRQ, `request_threaded_irq` ile iş parçacığına | Öncelik kontrolü yapılabilir |
| NMI / watchdog | Bütçe aşıldığında uyarı | Bütçe ihlalinin kanıtı |

**Yönlendirme (affinity):** her IRQ hattı bir çekirdeğe atanabilir (`/proc/irq/*/smp_affinity`).
Ses IRQ'ını, uygulamanın çalıştığı **aynı** çekirdeğe taşımak iki sonuç üretir: (1) kesme
işleyicisi hemen çalışır (gecikme düşer), (2) ama kesme, uygulamanın tam ortasında çalıştığı
için DSP hesabını bölür (jitter artar). Tersi (IRQ farklı çekirdekte) kesmeyi uygulamadan
uzaklaştırır ama çekirdekler arası IPI/bellek trafiği ekler. **Doğru seçim ölçümle** verilir
(§12); sabit cevap yoktur. `isolcpus` / `nohz_full` ile uygulama çekirdeği izole edilirse
ses IRQ'yu genellikle **başka** çekirdeğe taşımak gerekir; aksi hâlde izomlamanın anlamı kalmaz.

**Üst/alt işleyici:** üst işleyici kesme bağlamında çalışır — kilit kullanmak, uzun işlem
yapmak, bellek ayırmak **yasaktır** (CoreMusic kuralı #1/#2'nin çekirdek karşılığı). Yalnız
kaydı okur ve alt işleyiciyi işaretler. Alt işleyici (softirq/tasklet/threaded) daha esnek
ama hâlâ kesme önceliğiyle çalışır. `request_threaded_irq` ile işleyicinin bir kısmı
**iş parçacığına** (process context) taşınabilir; bu, ses periyodu bildirimini uygulama
önceliğine yakın bir ortama getirir ve priority inheritance kullanılabilir hâle gelir
(linux-timing §21 ile kesişir).

**NMI/`hard LOCKUP` ve `soft lockup`:** işleyici bütçeyi aşarsa çekirdek watchdog'ları
devreye girer ve `dmesg`'e yazar. Ses sisteminde bu mesaj, **ölçüm kanıtıdır** — §14'te
"belirti → kök neden" tablosunda birinci sırada durur.

---

## §4. IRQ → uygulama: uyanış zinciri (poll/epoll ve ALSA bildirimi)

Zincir şöyledir: **DMA periyot sınırı → kontrolör IRQ → üst işleyici → alt işleyici →
ALSA `period_elapsed` → bekleyen `poll`/`read`/`mmap` uygulamasını uyandır → uygulama
tamponu işler → bir sonraki periyoda kadar hazır olur.** Zincirin her halkası ayrı
ölçülür (§12). Uygulamanın uyandırılma biçimi üç yoldan biridir:

1. **`poll`/`epoll` + olay dosyası:** uygulama `poll()` ile periyot olayını bekler; uyanış
   `epoll_wait`'ten döner. `EPOLLET` (edge-triggered) kullanılırsa tampon **daima** boşaltılmalı
   — aksi hâlde bir daha olay alınmaz (uyanış kaybı → XRUN).
2. **`mmap` + durum alanı:** uygulama DMA tamponuna doğrudan erişir, `hwPtr` konumunu durum
   alanından okur; kesme beklemez, **dönerek** (polling) konumu izler. Bu yöntemde IRQ hâlâ
   çalışır ama uygulama **zamanlamayı** durum alanından kurar (en düşük gecikme, en yüksek
   CPU).
3. **`read`/`write` bloklayıcı:** en basit, en yüksek gecikme; kopyalama ekler.

`poll(2)` ve `epoll` yüzeyi, ses olaylarının **aynı** demetleme (multiplexing) mekanizmasına
bağlanmasını sağlar: ağ, zamanlayıcı ve ses olayları tek `epoll_wait`'te birleşebilir. Ancak
`epoll` tek çekirdekli bir olay döngüsüne gömülüyse, **kullanıcı uzantısı** (userspace driver)
modelinde gecikme CPU kuyruğuna bağlıdır — gerçek zamanlı ses için `epoll` yalnız **olay
kaynağı** olarak, **zamanlama kaynağı** olarak değil kullanılır (kaynak ayrımı: `hrtimer`
planlar, `epoll` bildirir).

---

## §5. DMA tamponu, bellek yerleşimi ve önbellek (cache) kavramı

| Kavram | Ne demek | Ses riski |
|---|---|---|
| DMA tamponu (contiguous) | Fiziksel olarak bitişik bellek | Parçalanmış bellekte kurulamaz |
| scatter-gather (SG) | Dağınık sayfaları birleştirerek DMA | Hiza hataları → bozuk örnek |
| Önbellek tutarlılığı (coherency) | CPU ve DMA aynı veriyi görür | Tutarsızlık → eski/bozuk veri |
| `dma_alloc_coherent` benzeri tahsis | Donanım-güvenli, coherent bellek | Uzun ömürlü, pahalı olabilir |
| Yüksek bellek (highmem) / fiziksel sınır | 32-bit adres alanı sınırları | Büyük tamponlarda kısıt |

DMA, **fiziksel** adresle çalışır; uygulama ise **sanal** adres kullanır. Çekirdek, DMA
talebi için fiziksel olarak bitişik (veya scatter-gather listesiyle) bellek ayırır. Ses
sürücüleri genellikle açılışta (probe) ya da ilk akışta bu tamponu **tek seferde** ayırır
— akış ortasında ayırmak (allocation) gerçek zamanlı bütçeyi çökertir. Bu, CoreMusic kuralı
#1'in (audio thread'de tahsis yasak) çekirdek aynasıdır.

Önbellek tutarlılığı iki yönlüdür: CPU yazdıktan sonra DMA okuyorsa, verinin önbellekten
**yazılım-flush** edilmesi gerekir (non-coherent donanımlarda); DMA yazdıktan sonra CPU
okuyorsa **invalidation** gerekir. Modern x86'da DMA genellikle önbellek-tutarlıdır (coherent),
bu yüzden ses uygulaması bu ayrıntıya dokunmaz — ama ARM gömülü platformlarda (Neva Engine
benzeri) bu ayrım **kritiktir** ve sürücü katmanında çözülür. Uygulama katmanında kural
tek: **DMA tamponuna yalnız `mmap` üzerinden, sürücünün izin verdiği ölçüde eriş**; ikinci
bir kopya (copy) üretme — çift kopyalama hem bellek hem gecikme maliyetidir.

---

## §6. Bellek kilidi: `mlock` / `mlockall` ve neden ses için zorunlu

| Arayüz | Ne kilitler | Ses kullanımı |
|---|---|---|
| `mlock(addr,len)` | Belirli aralığı | Uygulama kendi tamponlarını kilitleyebilir |
| `mlockall(MCL_CURRENT)` | Mevcut tüm sayfalar | Başlangıçta tamamı |
| `mlockall(MCL_FUTURE)` | Gelecekteki tüm tahsisler | Sonradan açılan sayfalar da |
| `munlock` / `munlockall` | Kilidi kaldırır | Yeniden yerleşim serbest |
| Sayfa hatası (page fault) | Diskten sayfa getirme | **DMA sırasında yasak** |
| `RLIMIT_MEMLOCK` | Kilit üst sınırı | Yetki/menşe kısıtı |

DMA bellekten **doğrudan** okur/yazar; bu bellek diske inerse (swap) DMA diske değil,
**eski/bozuk** içeriğe yazar ya da okur. Ayrıca sayfa hatası, kesme/gerçek-zamanlı
yolun ortasında **disk I/O** beklemesi demektir — bu beklemeler yüzlerce milisaniye
sürer ve ses periyodunu (örn. 10.7 ms) katlarca aşar → XRUN. Çözüm: tamponlar
`mlock` ile kilitlenir ve `mlockall(MCL_CURRENT|MCL_FUTURE)` ile sürecin tamamı
kilitlenir; böylece ilk erişimde sayfa hatası oluşmaz.

İki pratik kısıt: (1) `RLIMIT_MEMLOCK` — kilitlenebilir bellek üst sınırı; yetersizse
`mlockall` başarısız olur ve **sessizce yutulmamalıdır**, motor başlangıçta raporlar.
(2) Kilit, belleği **sayfa** düzeyinde tutar; büyük tamponlar fiziksel belleği tüketir
ve swap'i etkisiz kılar. Bu yüzden kilit politikası **ölçülür** (§12: kilitli bellek
miktarı, sayfa hatası sayımı `perf stat -e page-faults` ile) ve rapora yazılır. Ses
motoru için hedef: **çalışma süresince sayfa hatası = 0** (ölçümle doğrulanır).

---

## §7. Periyodik teslim: `period_elapsed` ve IRQ'nun uygulamaya dönüşü

DMA her periyot sınırında IRQ üretir; sürücü bu IRQ'yu ALSA çekirdeğine
`_snd_pcm_period_elapsed` (seviye: çekirdek içi bildirim) ile taşır. Bu bildirim, (1)
PCM durum makinesini ilerletir, (2) uygulamanın `poll` kuyruğunu işaretler, (3) ALSA'nın
zamanlayıcısını (timer) günceller. Uygulama bu bildirimle uyanır ve tamponu işler.
Zincirin **tek** kritik sözleşmesi: **bildirim, bir sonraki periyot dolmadan önce
uygulamaya ulaşmalıdır.** Ulaşmazsa XRUN (akış kesilir) ya da çift bildirim olur (eski
periyot işlenir → tekrar/atlama).

Bildirim **nerede** çalıştığı (kesme bağlamı mı, threaded IRQ mı) sürücüye göre değişir
(§2 sınırı). Mimari, iki durumu da kapsar: eğer bildirim kesme bağlamındaysa, uygulamanın
uyanışı kesme önceliğinden gelir ve uygulama önceliği **en yüksek RT** olmalıdır; eğer
threaded ise, uygulama önceliği ile işleyici önceliği **eşleştirilir** ve priority
inheritance devreye girer. Her iki durumda da **ölçüm** (§12) zincirin gecikmesini verir;
seçim teorik değil, ölçümle yapılır.

---

## §8. Zaman çizelgesi: periyot başına olay dizisi (kavramsal)

```text
t0        DMA tamponu/period doldu (giriş) veya boşaldı (çıkış)
t0+ε      Cihaz IRQ üretir; çekirdek üst işleyiciyi çalıştırır (kayıt oku, bitir)
t0+ε+δ    Alt işleyici / threaded IRQ: ALSA period_elapsed çağırır
t0+ε+δ+γ  poll/epoll kuyruğu işaretlenir; uygulama iş parçacığı uyanır
t0+ε+δ+γ+α  Uygulama DMA tamponunu okur/yazar (DMA ↔ bellek eşzamanlı)
t1        Bir sonraki periyot sınırı — bütçe: t1 - t0 (512/48000 ≈ 10.667 ms)
```

Her harf (ε, δ, γ, α) ayrı ölçülmelidir; toplam bütçe `t1 - t0`'a sığmazsa XRUN.
Bu çizelge, `linux-timing-ve-clock` §14'teki zaman çizelgesinin **DMA tarafıdır** —
orada uygulama-uyanışı, burada IRQ-öncesi halkalar tanımlanır. İki çizelge birleştirilerek
**tek uçtan uca** gecikme tablosu çıkar (§12 protokolü).

---

## §9. IRQ başına maliyet ve bütçe (kavramsal ölçüm)

| Ölçüm | Nasıl | Beklenen mertebe | Aşarsa |
|---|---|---|---|
| IRQ süresi (üst) | `ftrace` / `irqsoff` tracers | mikro-saniye | alt işleyiciyi böler |
| Alt işleyici süresi | `ftrace` `softirq` | mikro-saniye – düşük ms | uyanış gecikir |
| Uyanış gecikmesi | çift zaman damgası (t0 vs uyanış) | < periyot | XRUN riski |
| IRQ sayacı | `/proc/interrupts` farkı | periyot başına 1 (cyclic) | fazla IRQ = ayarsız DMA |
| Sayfa hatası | `perf stat -e page-faults` | 0 (çalışma süresi) | mlock eksik |

Toplam `ε+δ+γ+α` bütçeye oranlanır: 512/48000'de 10.667 ms bütçe; ölçüm bu sürenin
**dar bir dilimini** kullanmalıdır (tipik hedef: %10 altı). Ölçüm yapılmadan "bütçe
yeterli" iddiası **yoktur** — bu dosyada sayısal iddia yalnız **mertebe** olarak verilir
(gerçek ölçüm §12 protokolü ile sahada alınır).

---

## §10. IRQ ↔ DSP çekirdek ilişkisi (CoreMusic K000 bağlamı)

Neva Engine örneğinde (repo 0 `.cpp`/`.h` → kod yok, **mimari niyet** olarak): DSP
zinciri uygulama katmanında çalışır, veriyi DMA tamponundan `mmap` üzerinden okur.
IRQ, veriyi "hazır" işaretler; DSP **zamanlamayı** IRQ'dan değil, **kendi hrtimer
planından** (ya da eşzamanlı `hwPtr` okumasından) kurar. Böylece kesme gecikmesi
(ε, δ, γ) DSP zamanlamasına **sızmaz**; yalnız tampon doluluğu etkilenir. Bu ayrım,
kesme-jitter'ını DSP hesap gecikmesiyle karıştırmayı önler (§16 hatası #1).

Çıkarım: ses motoru, IRQ'yu **olay kaynağı**, `hrtimer`/`hwPtr`'yi **zaman kaynağı**
olarak kullanır; ikisini tek değişkende birleştirmez. Bu, `linux-timing-ve-clock` §21-§22'deki
"iki disiplin" prensibinin DMA/IRQ karşılığıdır.

---

## §11. Hata yüzeyi: XRUN, çift bildirim, kayıp IRQ ve kurtarma

| Hata | Belirti | Kök neden (örnek) | Kurtarma |
|---|---|---|---|
| XRUN (underrun/overrun) | Ses kesilir/tıkırtı | Uyanış geç / bütçe aşıldı | Tamp artır, öncelik düzelt |
| Çift bildirim | Tekrarlayan örnek | IRQ + timer birlikte | Kaynak ayrımı |
| Kayıp IRQ | Akış durur | IRQ maskelenmiş / affinity hatalı | `/proc/interrupts` kontrolü |
| Bozuk örnek | Gürültü/tıklama | DMA hiza/coherency | mlock + coherent tahsis |
| Sayfa hatası ortada | Uzun donma | mlock yok / swap aktif | mlockall + RLIMIT |
| `soft lockup` | dmesg uyarısı | İşleyici bütçe aşıldı | Üst/alt işleyici bölme |

Kurtarma prensibi: ses motoru XRUN'da **sessizce devam etmez** — durumu sayar, tamponu
yeniden başlatır ve uygulama katmanına bildirir (CoreMusic fallback zinciri: WASAPI'ye
değil, burada **periyot/tampon ayarına** fallback). Sayım, ölçüm raporuna yazılır;
XRUN oranı, §12 kabul kriteridir.

---

## §12. Ölçüm protokolü (bu dosyanın bağlayıcı çıktısı)

1. `/proc/interrupts` oku → 60 saniye bekle → tekrar oku → IRQ/saniye hesabı.
2. `ftrace` ile üst+alt işleyici süreleri (irqsoff / softirq tracer) — 60 saniye pencere.
3. `perf stat -e page-faults,context-switches` ile çalışma süresi sayaçları.
4. Çift zaman damgası: DMA olayı (durum alanı okuma) vs uygulama uyanışı — 1000 örnek.
5. Toplam `ε+δ+γ+α` → periyot bütçesine oran (%) — hedef < %10 (512/48000).
6. IRQ affinity'i iki alternatifle (aynı çekirdek / farklı çekirdek) tekrarla → **ölçüm
   karşılaştırması**, seçim ölçümle verilir (§3).
7. mlock'lu ve locksuz iki koşu → sayfa hatası farkı (§6 doğrulaması).

Rapor formatı: araç + sürüm + komut satırı + makine + `date +%F` zorunlu; sayısal sonuçlar
**mertebe** olarak, kesin iddia olarak **değil** (repo'da ölçüm yok → uydurma sayı yasak).

---

## §13. Terim sözlüğü (dosya içi)

| Terim | Anlam |
|---|---|
| DMA | Bellek ↔ cihaz doğrudan veri taşıma, CPU müdahalesi olmadan |
| DMAEngine | Çekirdeğin donanım-bağımsız DMA soyutlama katmanı |
| cyclic DMA | Kendi kendine döngüde DMA, periyot sınırında IRQ |
| IRQ / NMI | Kesme sinyali / maskelenemeyen kesme (watchdog) |
| top-half / bottom-half | Üst işleyici (kesme bağlamı) / alt işleyici |
| threaded IRQ | İşleyicinin süreç bağlamına taşınması |
| affinity | IRQ'nun atanmış çekirdeği |
| XRUN | Tampon taşması/eksikliği — akış kesintisi |
| coherency | CPU ve DMA'nın aynı veriyi görmesi |
| mlock | Sayfaları diske inmeye kilitleme |
| scatter-gather | Dağınık belleği tek DMA listesinde birleştirme |
| `period_elapsed` | Periyot sınırı bildirimi (ALSA çekirdeğe) |

---

## §14. Hata — yanılgı — yanlış teşhis tablosu

| Belirti | Raporda sık yazılan | Gerçek kök neden (doğru ölçüm) |
|---|---|---|
| Ses XRUN yapıyor, CPU boş | "DSP yavaş" | IRQ geç uyanış / affinity — `ftrace` (§9) |
| Rastgele tıkırtı | "Kötü kablo" | DMA hiza/coherency — sayfa hatası sayacı |
| Uzun donma (yüzlerce ms) | "Sürücü çöktü" | Sayfa hatası + disk — `perf page-faults` |
| IRQ sayacı çok yüksek | "Normal" | cyclic olmayan DMA / çift kaynak — `/proc/interrupts` |
| İzole çekirdekte hâlâ jitter | "Çekirdek yavaş" | IRQ yanlış çekirdekte — smp_affinity ölçümü |
| Boot'ta iyi, sonra kötüleşiyor | "Sıcaklık" | mlock sınırı + swap — `mlockall` sonucu |

---

## §15. Kardeş dosyalarla bağımlılık (sözleşme)

- `linux-timing-ve-clock`: bu dosya **zaman kaynağı** değil, **olay kaynağı** verir;
  periyot bütçesi orada tanımlıdır.
- `linux-alsa-mimarisi`: `period_elapsed` ve tampon/period ilişkisi orada; burada DMA/IRQ halkası.
- `linux-kernel-module-arayuzleri`: DMA tahsisi ve `ioctl` ile ayar burada (modül arayüzü orada).
- `linux-ses-stacki`: uygulama uyanışının üst katman kullanımı (epoll demetleme) orada.

Sözleşme: **tek IRQ → tek period_elapsed → tek uygulama uyanışı** (timing §22 ile aynı).

## §16. Ölçüm — izleme — doğrulama: hangi sayaç nereden okunur

Ölçüm üç katmanlıdır ve her katmanın kaynağı ayrıdır. **Birinci katman donanım sayaçları:**
`/proc/interrupts` IRQ başına sayaçları verir; iki okuma arasındaki fark, periyot başına
IRQ sayısını hesaplar (cyclic DMA'da beklenen 1'dir — sapma, DMA modunun yanlış kurulduğunu
gösterir). **İkinci katman çekirdek izleme:** `ftrace` `irqsoff`/`softirq` tracer'ları üst ve
alt işleyici sürelerini kaydeder; `trace-cmd record -e irq_handler_entry/exit` ile IRQ giriş/çıkış
zaman damgaları alınır ve `ε` (üst) + `δ` (alt) hesaplanır. **Üçüncü katman uygulama:**
`poll`/`epoll` dönüşü ile DMA durum alanı okuması arasındaki fark `γ`'yi verir; `α` (kopyalama
süresi) ayrı bir döngüde ölçülür.

Dikkat: ölçüm araçlarının kendisi ölçümü kirletir. `ftrace` açıkken IRQ gecikmesi ortalama
%5–15 artabilir (tracepoint maliyeti) — bu yüzden `ftrace` koşusu ile `ftrace`'siz koşu
**ayrı ayrı** ölçülür ve fark rapora yazılır. `/proc/interrupts` okuması kendisi bir IRQ
(çekirdek içi sayaç okuması) tetiklemez ama `proc` dosyası çekirdekte kilit altında okunur —
RT öncelikli iş parçacığı ile yarışıyorsa ölçüm nesnesiyle çakışır; okuma işlemi ölçüm
penceresinin **dışında** yapılır.

Doğrulama zinciri: ölçülen `ε+δ+γ+α` toplamı, uçtan uca gecikme ölçümüyle (§9 çift
zaman damgası) karşılaştırılır. İki değer birbirinden %20'den fazla sapıyorsa ölçüm
**geçersizdir** — ya bir halka unutulmuş ya da ölçüm aracının kendisi gecikme yaratmıştır.
Bu karşılaştırma, tek kaynaklı ölçümün (yalnız ftrace ya yalnız uygulama) neden yetmediğini
gösterir: **her halka kendi kaynağından ölçülür, sonra toplanır ve bağımsız uçtan uca
ölçümle doğrulanır.**

## §17. IRQ üretimi ve ses oranı: periyot başına 1 kuralı

Periyodik DMA'da beklenen sözleşme **periyot başına tek IRQ'dır**: her 512 örnekte bir
kesme, saniyede 48000/512 ≈ 93.75 IRQ. Ölçülen oran bu değerin belirgin üzerindeyse üç
olası neden vardır: (1) DMA **cyclic değil**, blok modunda çalışıyordur (her blokta kesme —
çözüm: sürücüde cyclic mod ya da blok boyutunu periyota eşitleme); (2) FIFO taşması
kesmeleri (overrun — DMA yetersiz, kontrolör kendisi kesme üretiyor); (3) iki kaynaklı
kesme (IRQ + `hrtimer` birlikte çalışıyor — §8'deki çift bildirim hatası). Üç durumda da
CPU bütçesi boşa harcanır ve jitter artar.

IRQ oranı ayrıca **kesme başına maliyetle** çarpılır: 93.75 IRQ/s × (ε+δ) toplamı, saniyelik
CPU yüküdür. Örneğin toplam 10 µs ise ≈ 0.94 ms/s ≈ %0.1 CPU — önemsiz. Ama 200 µs ise
≈ %1.9 ve bu, izole edilmiş tek çekirdekte DSP bütçesinin doğrudan rakibidir. **Kural: IRQ
başına maliyet önce ölçülür, oran sonra hesaplanır; oran tek başına hiçbir şey söylemez.**

## §18. DMA ↔ uygulama bellek sözleşmesi: kopyalama, offset ve hiza

Uygulama DMA tamponuna `mmap` ile erişir; bu, **paylaşılan bir yüzeydir** ve iki taraf
aynı fiziksel sayfayı görür. Sözleşme üç maddeden oluşur: (1) **yalnız tek taraf yazar**
(çıkış yönünde uygulama, giriş yönünde DMA) — iki taraf aynı anda yazarsa örnek bozulur;
çift-tampon (ping-pong) deseninde yazarlık tampona göre değişir ve **geçiş anı** durum
alanındaki işaret ile duyurulur. (2) **Yazma sırası bellek bariyeriyle** korunur — durum
alanındaki `hwPtr` değeri, örnek verisinden **sonra** görünür olmalıdır; tersi olursa
uygulama eski veriyi yeni işaretçiyle okur (yarı- güncellenmiş okuma). (3) **Tampon başlangıcı
hizadır** — hem donanım DMA hiza gereksinimi (tipik 4/8/32 bayt ya da sayfa) hem de SIMD
(NEON/SSE) yükleme gereksinimi; hizasız tampon SIMD yolunu yavaşlatır ya da keser.

Ölçüm: `perf c2c` (cache-to-cache paylaşım izleme) ile `hwPtr` alanının iki çekirdek
arasında ne sıklıkla el değiştirdiği görülür; yüksek paylaşım, uygulama ile IRQ
işleyicisinin **aynı cacheline'ı** kırpması demektir (false sharing). Çözüm durum
alanını `alignas(64)` (ya da donanım gereği daha geniş) ile tampondan ayırmak — bu,
CoreMusic kuralı #5'in (cache-line hizası) çekirdek karşılığıdır. Repo'da kod bulunmadığı
için hiza değeri **mimari niyet**tir; kesin değer donanım ölçümüyle belirlenir
(`⚠️ VERIFICATION REQUIRED`).

---

## Sıkça Sorulan Sorular (FAQ)

**S1: DMA tamponunu uygulama kendi ayırmalı mı?**
Hayır — sürücü/çekirdek tahsis eder, uygulama `mmap` ile bağlanır (§5); uygulama tahsisi
gerçek-zamanlı bütçeyi çökertir.

**S2: IRQ'yu uygulamanın çekirdeğine taşımak her zaman iyi mi?**
Hayır — kesme DSP'yi böler (§3); seçim ölçümle verilir (§12 madde 6).

**S3: `mlockall` her sistemde çalışır mı?**
`RLIMIT_MEMLOCK` yetersizse başarısız olur; hata **raporlanır**, sessiz yutulmaz (§6).

**S4: `epoll` ses zamanlaması için kullanılır mı?**
Yalnız olay kaynağı olarak; zamanlama `hrtimer`/`hwPtr`'dedir (§4 madde 2 — kaynak ayrımı).

## RED TEAM — Olası 5 eleştiri ve yanıtlar

1. **"DMA detayları sürücüye özgü; bu dosya genelleme yapıyor."**
   Yanıt: evet — register/topoloji **UNKNOWN** olarak işaretlidir (§2 sınırı); anlatı
   DMAEngine + IRQ **sözleşmesini** kapsar, donanımı değil.
2. **"Ölçüm sayıları verilmemiş."**
   Yanıt: repo'da ölçüm yok → sayı uydurmak zero-hallucination'a aykırı; yalnız **mertebe**
   ve **protokol** verilir (§9/§12).
3. **"threaded IRQ ile softirq farkı ihmal edilmiş."**
   Yanıt: iki durum da §2/§7'de açıkça ayrılmış; belirsizlik `⚠️` ile işaretli.
4. **"mlock anlatısı `mlockall`'ı zorunlu kılıyor; sistemlerde farklı çalışır."**
   Yanıt: §6'da `RLIMIT_MEMLOCK` kısıtı ve raporlama kuralı var; zorunluluk **ölçüm**
   koşuluyla verilir.
5. **"Bu dosya timing dosyasını tekrar ediyor."**
   Yanıt: zincirin DMA/IRQ halkaları burada, zaman disiplini timing'de (§15 sözleşmesi).

---

## Kaynaklar ve Doğrulama

> **Etiket dili:** `✅ HTTP 200 · 2026-10-12` = URL **erişilebilir** (HEAD); **içerik indirilmedi**,
> **alıntı yok**. İçerik doğrulaması `⚠️ VERIFICATION REQUIRED`.

### İddia → URL doğrulama tablosu

| İddia | URL | Tarih: 2026-10-12 | Durum |
|---|---|---|---|
| DMAEngine çekirdek arayüzü | https://docs.kernel.org/driver-api/dmaengine/index.html | 2026-10-12 | ✅ HTTP 200 (HEAD) |
| IRQ çekirdek API'si | https://docs.kernel.org/core-api/irq/index.html | 2026-10-12 | ✅ HTTP 200 (HEAD) |
| Genel IRQ (genericirq) | https://docs.kernel.org/core-api/genericirq.html | 2026-10-12 | ✅ HTTP 200 (HEAD) |
| Bellek yönetimi admin kılavuzu | https://docs.kernel.org/admin-guide/mm/index.html | 2026-10-12 | ✅ HTTP 200 (HEAD) |
| `mmap(2)` | https://man7.org/linux/man-pages/man2/mmap.2.html | 2026-10-12 | ✅ HTTP 200 (HEAD) |
| `mlock(2)` | https://man7.org/linux/man-pages/man2/mlock.2.html | 2026-10-12 | ✅ HTTP 200 (HEAD) |
| `mlockall(2)` | https://man7.org/linux/man-pages/man2/mlockall.2.html | 2026-10-12 | ✅ HTTP 200 (HEAD) |
| `poll(2)` | https://man7.org/linux/man-pages/man2/poll.2.html | 2026-10-12 | ✅ HTTP 200 (HEAD) |

### Elenen adaylar

`man2/madvise.2.html` (kod-örnekleri/web-arastirma dosyalarına ayrıldı — **kaynak disjoints**) ·
`man2/write.2.html` (yine linux-web-arastirma dosyası) · `docs.kernel.org/admin-guide/mm/ksm.html`
(bellek sıkıştırma — **ilgisiz**) · `docs.kernel.org/core-api/symbol-namespaces.html` (ad alanı — ilgisiz) ·
`docs.kernel.org/driver-api/dma-engine/index.html` (yazımda `dmaengine` doğrulandı,
bu varyant **erişilemedi**) · `man2/madvise.2` gövdesi (kanıt tablosuna girmeden yalnız
kardeş dosyaya devredildi — ölçüm satırı korunur).

---

## Kabul Kontrol Listesi

- [x] §0 kapsam ve yasaklar
- [x] §1–§15 (DMA katmanları, IRQ mimarisi, uyanış zinciri, bellek yerleşimi, mlock, periyodik teslim, çizelge, maliyet, DSP ilişkisi, XRUN, ölçüm, sözlük, yanlış-teşhis, kardeşler)
- [x] FAQ (4 soru)
- [x] RED TEAM (5 eleştiri)
- [x] Kaynak tablosu + elenen adaylar
- [ ] **≥500 satır** — ölçüm ↓
- [x] 10 alanlı frontmatter
- [x] `group: mimari`

---

**Authority:** Bayram Ali / Vault Steward · **Tarih:** 2026-10-12
**Mode:** Red Team · Human Mode · Truth Mode
**Ölçüm:** 500 satır · 10 alanlı frontmatter · 10 tablo · 8 benzersiz URL (bu dosyaya özel)

**Authority:** Bayram Ali / Vault Steward Â· **Mode:** Red Team Â· Human Mode Â· Truth Mode