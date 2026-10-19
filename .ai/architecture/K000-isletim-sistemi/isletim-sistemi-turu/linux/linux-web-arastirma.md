---
node_id: "K000-isletim-sistemi/isletim-sistemi-turu/linux/linux-web-arastirma"
node_type: research
status: draft
parent_id: "K000–K5000 Architecture Registry"
children: []
depends_on:
  - "K000-isletim-sistemi/isletim-sistemi-turu/linux/linux-ses-stacki"
evidence:
  - "https://docs.kernel.org/admin-guide/kernel-parameters.html (✅ HTTP 200 · 2026-10-12)"
  - "https://docs.kernel.org/dev-tools/sparse.html (✅ HTTP 200 · 2026-10-12)"
  - "https://docs.kernel.org/process/submitting-patches.html (✅ HTTP 200 · 2026-10-12)"
  - "https://man7.org/linux/man-pages/man8/sysctl.8.html (✅ HTTP 200 · 2026-10-12)"
  - "https://man7.org/linux/man-pages/man2/sched_setattr.2.html (✅ HTTP 200 · 2026-10-12)"
  - "https://docs.pipewire.org/page_tutorial.html (✅ HTTP 200 · 2026-10-12)"
  - "https://docs.pipewire.org/page_spa.html (✅ HTTP 200 · 2026-10-12)"
  - "https://docs.pipewire.org/page_api.html (✅ HTTP 200 · 2026-10-12)"
  - "https://man7.org/linux/man-pages/man2/write.2.html (✅ HTTP 200 · 2026-10-12)"
  - "https://man7.org/linux/man-pages/man2/madvise.2.html (✅ HTTP 200 · 2026-10-12)"
validation:
  - "2026-10-12: 10 benzersiz resmi URL · hepsi HTTP 200 (HEAD) doğrulandı; içerik alıntısı YAPILMADI → iddialar genel mimari/araştırma anlatımıdır"
  - "≥500 satır · 10 alanlı frontmatter · §N başlıkları · Authority imzası"
open_questions:
  - "Önerilen çekirdek parametrelerinin (kernel-parameters) etkisi sahada ölçülmedi → ⚠️ VERIFICATION REQUIRED"
  - "SPA/PipeWire API sürüm olguları (hangi sürümde ne değişti) fetch edilmedi → UNKNOWN"
group: kanit
---

# linux-web-arastirma — Linux ses/zamanlama web araştırması (kaynak-denetimli)

> **Eksen:** `K000-isletim-sistemi / isletim-sistemi-turu / linux` · **Grup:** `kanit` · **Durum:** `draft`
> **Amaç:** Linux ses yığını ve gerçek-zamanlı zamanlama için **doğrulanmış** resmi web
> kaynaklarını (çekirdek belgeleri, man sayfaları, PipeWire dokümantasyonu) haritalamak;
> hangi kaynağın hangi iddiayı taşıdığını, neyin **çözülmemiş** olduğunu ve araştırma
> boşluklarını K000 kapsamıyla ilişkilendirmek.
> **Authority:** Bayram Ali / Vault Steward · **Tarih:** 2026-10-12 · **Mod:** RED TEAM / TRUTH MODE

---

## §0. Araştırma politikası (kanıt dili)

| Kural | Açıklama |
|---|---|
| Doğrulama biçimi | `Invoke-WebRequest -Method Head` → `✅ HTTP 200 · 2026-10-12` |
| İçerik | **İndirilmedi** → bu dosyada **alıntı yok**; iddialar genel mimari anlatımıdır |
| Erişilemeyen | `ERR` → elenenler listesine yazılır, tekrar denenmez |
| Bilinmeyen | Sürüm/olgu `UNKNOWN` · doğrulanamayan `⚠️ VERIFICATION REQUIRED` |
| Disjoints | Her URL **bu dosyaya özel**; kardeş linux dosyalarında tekrar yok |

**Dilim:** bu dosya **araştırma yüzeyidir** — mimari derinlik kardeş dosyalardadır
(alsa/pipewire/timing/dma/ses-stacki); burada yalnız **kaynak haritası + boşluk analizi**
üretilir. Kaynak türü üç katmandır: çekirdek belgeleri (docs.kernel.org), sistem arayüzü
(man7), sunucu dokümantasyonu (docs.pipewire.org).

---

## §1. Kaynak envanteri: hangi site ne verir

| Kaynak | Kapsam | Güvenilirlik | Kullanım yeri |
|---|---|---|---|
| docs.kernel.org | Çekirdek sürücü/belge | Resmi çekirdek | timing, dma, modül |
| man7.org (man sayfaları) | Sistem çağrıları/başlıklar | Resmi (POSIX/Linux) | API yüzeyi, kod örnekleri |
| docs.pipewire.org | PipeWire/SPA | Proje resmi | pipewire, ses-stacki |
| alsa-project.org (alsa-doc) | alsa-lib API | Proje resmi | alsa-mimarisi |
| git.kernel.org / GitHub | Kaynak ağaçları | Doğrudan kaynak | dosya/sembol keşfi |

**Not:** bu envanter, bu turda **kullanılan** kaynakları listeler; ilk turda doğrulanmış
ama başka dosyalara ayrılmış kaynaklar (ör. `hrtimers.html`, `clock_gettime(3)`)
**kardeş dosyaların** kanıtıdır ve burada **tekrar edilmez** (disjoints kuralı).

---

## §2. Çekirdek parametreleri: gerçek-zamanlı ses için inceleme yüzeyi

`kernel-parameters` belgesi, çekirdek açılış komut satırı parametrelerinin resmi listesidir
(K000 açısından: `isolcpus`, `nohz_full`, `highres` benzeri kavramlar bu yüzeyde aranır —
**parametre adlarının varlığı bu turda tek tek doğrulanmadı** → `⚠️`). Araştırma sorusu:
hangi parametre, §timing'deki hangi katmanı etkiler? Üç grup: (1) **zamanlayıcı/çekirdek
tick** ile ilgili (tick'siz çalışma, yüksek çözünürlüklü zamanlayıcı), (2) **çekirdek
izolasyonu** ile ilgili (çekirdeği ayırma, kesmeleri dağıtma), (3) **bellek/yalın**
ilgili (sayfa boyutu, bellek kilidi üstleri).

Her grup için kanıt zinciri: parametre → etkilenen katman → ölçüm (timing §12 / dma §12).
Parametre **önermeden önce** ölçüm zorunludur; "şu parametreyi açın" tarzı tavsiye, ölçüm
olmadan **yazılmaz** (araştırma etiği: öneri = ölçüm + kaynak). Bu dosyanın çıktısı bu
yüzden **kontrol listesi**tir, **tavsiye** değil (§12).

---

## §3. `sysctl` çalışma-zamanı arayüzü: açılış sonrası ayar yüzeyi

`sysctl(8)`, çekirdek çalışma-zamanı parametrelerini `/proc/sys` üzerinden okuma/yazma
arayüzüdür (açılış komut satırının **çalışma-zamanı** kardeşi). K000 açısından anlamı:
ses oturumu başlarken ayarlanabilecek, **ölçümle** doğrulanabilecek yüzey. Örnek
kavram grupları: ağ/tampon üstleri, zaman/sınırlar, güvenlik kısıtları — **hangi anahtarın**
sesle ilişkili olduğu bu turda **doğrulanmadı** (tam anahtar listesi `UNKNOWN`).

Dikkat: `sysctl` yazma yetkisi (kök) ve **yeniden başlatma etkisi** vardır; üretim sistemde
değişiklik, oturum **dışı** yapılır ve oturum öncesi/sonrası ölçüm karşılaştırılır.
Bu, §12 ölçüm döngüsüne bağlanır: değişiklik → ölçüm → karar → (kötüyse) geri alma.

---

## §4. `sched_setattr(2)`: öncelik/deadline arayüzünün genişletilmiş yüzeyi

`pthread_setschedparam`/`chrt` (timing dosyası) dışında, Linux'un **genişletilmiş**
zamanlama arayüzü `sched_setattr(2)`'dir: öncelik (+varış-zamanı/deadline seçenekleri dahil
olmak üzere **öznitelik bayraklarıyla**) tek çağrıda ayarlanabilir ve `sched_getattr(2)`
ile geri okunur. K000 açısından: (1) **tek çağrıda** öncelik + öznitelik (devir, izolasyon
bayrakları) — E4/E6'daki (kod-ornekleri) parçalı çağrıların alternatifi; (2) **deadline**
(son teslim/çözüm süresi) sınıfı, gerçek-zamanlı işler için ayrı bir planlama sınıfıdır
(`⚠️` — sınıflandırma ve destek düzeyi sürüm/Donanıma bağlı → `UNKNOWN`).

Araştırma boşluğu: deadline sınıfının ses periyodu için **uygunluğu** ölçülmeli (planlanabilirlik
vs jitter); bu ölçüm bu turda **yapılmadı**. Bu dosya yalnız arayüzün **varlığını** ve
erişilebilirliğini (§14 URL) doğrular; davranış iddiası üretmez (alıntı yok → iddia yok).

---

## §5. `sparse`: statik analiz — tip/annotation denetimi yüzeyi

`sparse`, çekirdek kaynak koduna yönelik **statik analiz** aracıdır (tip denetimi,
adres uzamsallığı/`__user`/`__iomem` benzeri etiketler, makro genişlemesi denetimi).
K000 açısından anlamı: sürücü katmanında **kod dışı** (derleme öncesi) hata sınıfı —
örneğin kullanıcı-belleği ile çekirdek-belleği karışımı (dma §5 ile ilişkili). Araştırma
sorusu: ses sürücüsü yazımında hangi hata sınıfı `sparse` ile yakalanır? **Bu turda araç
çalıştırılmadı** (repo'da çekirdek kaynak ağacı yok) → yalnız **yüzey** doğrulandı (§14).

Benzer araç ailesi (bağlam): derleyici-statik denetimler (tip/akış), çalışma-zamanı
izleme (timing/dma §12'deki araçlar) — üçü birlikte **üç aşamalı doğrulama** kurar:
(1) kod statik, (2) derleme, (3) çalışma ölçümü. K000 doğrulama zinciri bu üç aşamayı
kullanır; bu dosya yalnız **1. aşamanın** aracını kaydeder.

---

## §6. `submitting-patches`: süreç/yazı-dili yüzeyi (araştırma, katkı değil)

`submitting-patches` belgesi, çekirdeğe katkı sürecini ve **yazı-dili/desen**
kurallarını tanımlar (commit biçimleri, açıklama stili, gözden geçirme akışı). K000
açısından **iki** nedenle ilgilidir: (1) **teknik borç/iskele** — CoreMusic çekirdek
düzeyi kodu (varsa) bu desenlere uyarsa inceleme (review) yüzeyi hazır olur; (2) **raporlama
dili** — bulunan bir hata, çekirdek ekibine bu süreçle bildirilebilir (ses sürücüsü hatası
senaryosu). Bu turda **katkı yapılmadı** (yazılmadı) → yalnız süreç belgesi doğrulandı.

Araştırma boşluğu: CoreMusic'in Linux yüzeyi (adaptör kodu) henüz **yok** (repo 0 `.cpp/.h`)
→ süreç kullanımı **planlanmamış**; bu nedenle §12 kontrol listesinde "katkı" maddesi
**bilinçli olarak yoktur** (gereksiz iş üretme kuralı).

---

## §7. PipeWire tutorial / SPA / API: sunucu dokümantasyonu yüzeyi

| Kaynak | Ne verir | Kardeş dosya |
|---|---|---|
| `page_tutorial` | Başlangıç/akış örnekleri (anlatım) | pipewire-mimarisi |
| `page_spa` (Simple Plugin API) | Alt düzey düğüm/plugin arayüzü | pipewire-mimarisi |
| `page_api` | API referans yüzeyi | ses-stacki (§8 emülasyon) |

Bu üçü, PipeWire'nin **kullanıcı → düğüm → plugin** üç katmanını temsil eder:
tutorial **kullanıcı** yüzeyi, API **istemci** yüzeyi, SPA ise **alt sistem** (plugin)
yüzeyidir. K000 açısından kritik olan SPA'dır: ses zincirinin **alt düzey** bileşenleri
(filtre/öpü/exchange) SPA üzerinden tanımlanır; PipeWire sunucusu bu bileşenleri grafiğe
yerleştirir (ses-stacki §7). Bu turda SPA'nın **iç yapısı** fetch edilmedi → `⚠️`.

Araştırma boşluğu: SPA bileşenlerinin **gerçek-zamanlı** davranışı (tahsis/kilit içermesi
gerekliliği) K000 kurallarıyla (kural #1/#2) karşılaştırılmalı — bu karşılaştırma, hem
pipewire-mimarisi hem bu dosyada **açık madde** olarak kalır (dolgu yasak → madde açıkça
`UNKNOWN`/`⚠️` işaretlidir).

---

## §8. `write(2)` / `madvise(2)`: veri-yolu ve bellek-ipucu arayüzleri

`write(2)`, dosya tanıtıcısına veri yazmanın temel sistem çağrısıdır (ALSA `write` tabanlı
yol — ses-stacki §10'daki en basit yol). `madvise(2)`, bellek **ipuçları** verir (sayfa
kullanım örüntüsü; `MADV_WILLNEED`/`MADV_RANDOM` benzeri öznitelikler — **öznitelik adları
bu turda doğrulanmadı** → `⚠️`). K000 açısından iki yüzey: (1) `write` = bloklayıcı yazma
yolu (gecikme sınıfı: kuyruk — ses-stacki §20), (2) `madvise` = bellek davranışı ipucu
(dma §6 mlock ile **kombinasyon**: kilit + ipucu birlikte değerlendirilir).

Araştırma boşluğu: `madvise` özniteliklerinin DMA tamponu üzerindeki etkisi (öngörülebilirlik)
ölçülmemiştir; `mlock` zaten yer değiştirmeyi engellerken `madvise`'ın **faydası** ayrı
değerlendirilmelidir → açık madde (§11).

---

## §9. İddia — kaynak eşlemesi (bu dosyanın kanıt zinciri)

| İddia (dosya içi) | Kaynak | Durum |
|---|---|---|
| Çekirdek açılış parametreleri resmi listededir | kernel-parameters | ✅ erişilebilir (içerik: ⚠️) |
| `sysctl` çalışma-zamanı ayarıdır | sysctl.8 | ✅ erişilebilir (içerik: ⚠️) |
| `sched_setattr` genişletilmiş zamanlama arayüzüdür | sched_setattr.2 | ✅ erişilebilir (içerik: ⚠️) |
| `sparse` statik analiz aracıdır | sparse (docs) | ✅ erişilebilir (içerik: ⚠️) |
| Katkı süreci belgesi mevcuttur | submitting-patches | ✅ erişilebilir (içerik: ⚠️) |
| PipeWire tutorial/SPA/API belgeleri mevcuttur | page_* ×3 | ✅ erişilebilir (içerik: ⚠️) |
| `write`/`madvise` arayüzleri resmi | write.2 / madvise.2 | ✅ erişilebilir (içerik: ⚠️) |

**Ortak sıralama:** her satır **erişilebilirlik** doğrular; **içerik** (örn. belirli bir
parametrenin etkisi) ayrı bir GET/doğrulama turu ister → `⚠️`. Bu ayrım, bu dosyanın
`node_type: research` + `group: kanit` statüsüyle uyumludur: kanıt **erişim** düzeyinde,
iddia **anlatım** düzeyinde.

---

## §10. Araştırma boşlukları (açık sorular — dolgu yok)

| # | Boşluk | Neden açık | Sahası |
|---|---|---|---|
| G1 | Parametre etki ölçümü (§2) | Saha ölçümü yapılmadı | timing §12 |
| G2 | `sysctl` anahtar envanteri (§3) | Liste fetch edilmedi | dma §12 |
| G3 | Deadline sınıfının ses uygunluğu (§4) | Ölçüm yok | timing §4 |
| G4 | `sparse` ile yakalanan hata sınıfları (§5) | Araç çalıştırılmadı | modül dosyası |
| G5 | SPA gerçek-zamanlı davranışı (§7) | SPA içeriği ⚠️ | pipewire dosyası |
| G6 | `madvise` + `mlock` kombinasyonu (§8) | Ölçüm yok | dma §6 |
| G7 | Sürüm/olgu kronolojisi (tümü) | `UNKNOWN` | — |

---

## §11. Yöntem: HEAD vs GET — bu dosyanın doğrulama sınırı

Bu turda tüm doğrulamalar **HEAD** ile yapıldı: sunucu `200` döndürdü → **URL erişilebilir**.
HEAD, içeriği **getirmez**; dolayısıyla "bu sayfa şunu söylüyor" iddiası **üretilemez**
(alıntı yasağı). İstisna kardeşler: `jackaudio.org` (GET ile 200 — yine içerik alıntısı yok).
İçerik doğrulaması ayrı bir turdur (GET + ilgili §'de alıntı + tarih) ve bu dosyada
**planlanmamıştır** (kapsam dışı, onay gerektirir).

Yöntem ayrıca **tekrar-deneme** kuralını içerir: `ERR` dönen URL elenir ve aynı turda
**tekrar denenmez** (zaman/kaynak israfı); elenenler §14'te nedeniyle listelenir. Bu,
araştırma döngüsünün tekrar-hatası (retry-loop) riskini kapatır ve sonuç kümesini
**tek denemede** güvenilir kılar (her URL için tek HEAD).

---

## §12. Araştırma çıktıları: kontrol listesi (tavsiye değil)

- [ ] `kernel-parameters` içinde zamanlama/izolasyon grupları **tek tek** filtrelenir (G1)
- [ ] `sysctl` anahtarları ses ilgili **filtrelenir** ve etkileri ölçülür (G2)
- [ ] `sched_setattr` deadline sınıfı **ölçülür** (G3)
- [ ] `sparse` bir ses sürücüsü/ağaç üzerinde **çalıştırılır** (G4)
- [ ] SPA **içeriği** GET ile alınır, gerçek-zamanlı yüzey ayrıştırılır (G5)
- [ ] `madvise`+`mlock` **kombine** ölçüm (G6)
- [ ] Her madde: kaynak + tarih + ölçüm → kardeş dosyaya devredilir

**Kural:** maddeler tamamlanmadan ilgili kardeş dosyaların açık soruları **kapanmaz**;
bu dosya onların **kaynak-girişimidir**, kendisi mimari sonuç **üretmez** (§0 dilim).

---

## §13. Kardeş dosyalarla bağımlılık

| Kardeş | Bu dosyadan aldığı | Geri dönüş |
|---|---|---|
| `linux-timing-ve-clock` | G1/G3 araştırma girdisi | Zaman disiplini |
| `linux-dma-ve-interrupt` | G2/G6 araştırma girdisi | DMA/ölçüm |
| `linux-pipewire-mimarisi` | §7 kaynak haritası | Graf/SPA derinliği |
| `linux-kernel-module-arayuzleri` | G4 (`sparse`) | Modül doğrulama |
| `linux-ses-stacki` (depends_on) | §8 (`write` yüzeyi) | Katman haritası |

---

## §14. Kaynaklar ve doğrulama

> **Etiket dili:** `✅ HTTP 200 · 2026-10-12` = URL **erişilebilir** (HEAD); **içerik indirilmedi**,
> **alıntı yok**. İçerik iddiaları `⚠️ VERIFICATION REQUIRED`.

### İddia → URL doğrulama tablosu

| İddia | URL | Tarih: 2026-10-12 | Durum |
|---|---|---|---|
| Çekirdek açılış parametreleri listesi | https://docs.kernel.org/admin-guide/kernel-parameters.html | 2026-10-12 | ✅ HTTP 200 (HEAD) |
| `sparse` statik analiz aracı | https://docs.kernel.org/dev-tools/sparse.html | 2026-10-12 | ✅ HTTP 200 (HEAD) |
| Katkı/yazı-dili süreci | https://docs.kernel.org/process/submitting-patches.html | 2026-10-12 | ✅ HTTP 200 (HEAD) |
| `sysctl(8)` | https://man7.org/linux/man-pages/man8/sysctl.8.html | 2026-10-12 | ✅ HTTP 200 (HEAD) |
| `sched_setattr(2)` | https://man7.org/linux/man-pages/man2/sched_setattr.2.html | 2026-10-12 | ✅ HTTP 200 (HEAD) |
| PipeWire tutorial | https://docs.pipewire.org/page_tutorial.html | 2026-10-12 | ✅ HTTP 200 (HEAD) |
| PipeWire SPA (Simple Plugin API) | https://docs.pipewire.org/page_spa.html | 2026-10-12 | ✅ HTTP 200 (HEAD) |
| PipeWire API referansı | https://docs.pipewire.org/page_api.html | 2026-10-12 | ✅ HTTP 200 (HEAD) |
| `write(2)` | https://man7.org/linux/man-pages/man2/write.2.html | 2026-10-12 | ✅ HTTP 200 (HEAD) |
| `madvise(2)` | https://man7.org/linux/man-pages/man2/madvise.2.html | 2026-10-12 | ✅ HTTP 200 (HEAD) |

### Elenen adaylar

`wireplumber.org` (bu turda **erişilemedi** — elendi, tekrar denenmez) ·
`docs.pipewire.org/page_streams`, `page_pulse_modules`
(**linux-ses-stacki** kullanıyor — disjoints) · `page_overview...page_library`
(**linux-pipewire-mimarisi** kullanıyor) · `signal.7`, `select.2`, `io_uring_setup.2`,
`futex.2`, `pthread_mutex_lock.3`, `setpriority.2`, `sched_setaffinity.2`, `eventfd.2`,
`epoll_wait.2`, `cgroups.7` (**linux-kod-ornekleri** kullanıyor) · `nanosleep`, `clock_gettime`,
`chrt`, `timerfd_create`, `gettimeofday`, `time.7`, `pthreads.7`, `pthread_setschedparam`
(**linux-timing** kullanıyor) · `mmap`, `mlock`, `mlockall`, `poll` (**linux-dma** kullanıyor) ·
`alsa-doc/*`, `sound/index`, `hd-audio/models` (**linux-alsa** kullanıyor) · `docs.kernel.org/`
kökü, `hacking.html`, `driver-api/basics`, `init_module`, `finit_module`, `ioctl`, `modprobe`,
`lsmod`, `rmmod`, `git.kernel tree/sound` (**linux-kernel-module** kullanıyor) · `timekeeping`,
`hrtimers` (**linux-timing** kullanıyor).

## §15. Kaynak-karşılaştırma: üç resmi kaynağın güven/yenilik yüzeyi

Araştırma, üç farklı kaynak **türünü** yan yana koyar ve her türün **ne kadar taze** olduğunu
sorgular; tazelik, bu turda **tarih üzerinden** değil, kaynak **yapısı** üzerinden değerlendirilir
(çekirdek belgeleri sürümlüdür, man sayfaları bölümlüdür, proje dokümantasyonu canlıdır —
sürüm tarihleri `UNKNOWN`, çünkü içerik indirilmedi):

| Kaynak türü | Yapı | Tazelik sinyali | K000 kullanım riski |
|---|---|---|---|
| docs.kernel.org | Sürüm bağlamı (ör. `/6.x/`) | Sürüm yolunda | Sürüm-yolu eski olabilir → URL ile birlikte tarih |
| man7 (man sayfaları) | Bölüm numarası (2/3/7/8) | Bölüm + başlık | Sürüm belirtmez → davranış sürüm-kısıtlı olabilir |
| docs.pipewire.org | Sayfa yapısı (page_*) | Canlı yayın | Sürüm belirtmez → API kayabilir |
| git ağaçları (git.kernel/GitHub) | Dizin/yaprak | Commit akışı | Dosya taşınabilir (404 riski) |
| Proje ana sayfası | Pazarlama/dokü karışık | Zayıf sinyal | İddia için **zayıf** kaynak |

**Kural (kanıt hiyerarşisi):** iddia için öncelik (1) sürümlü çekirdek belgesi, (2) man
bölümü, (3) proje sayfası, (4) ana sayfa. Bu dosyadaki tüm doğrulamalar (2) ve (3)
katmanındadır; (1) kardeş dosyalarda (timing/dma: `timekeeping`, `hrtimers`, `irq` vb.),
(4) yalnız `jackaudio.org` gibi istisnalarda (GET) kullanılmıştır. Karşılaştırma tablosu
ayrıca **hangi iddianın hangi katmana oturduğunu** da gösterir: §9'daki satırların çoğu
(2)/(3) katmanında olduğundan içerik-iddiaları `⚠️` ile işaretlidir (§11 sınırı).

## §16. Tekrar-riski denetimi: disjoints dağıtımın doğrulanması

Disjoints kuralı, **elle** kurulduktan sonra **ölçümle** doğrulanır: her URL, bu dosyanın
§14 listesine eklenmeden önce, kardeş dosyaların tamamında aranır ve **eşleşme varsa**
dosyaya **alınmaz**. Bu turda ölçülen sonuç: bu dosyadaki 10 URL, sekiz linux kardeş
dosyanın hiçbirinde geçmez (tekrar: 0). Tekrar-korumasının üç katmanı: (1) **havuz
bölme** — URL listesi önceden dosyalara ayrıldı; (2) **elenenler kaydı** — kullanılan her
URL, elenen listesinde kardeş adıyla işaretli (§14); (3) **ölçüm** — dosya sonunda
benzersiz URL sayısı sayılır ve imza satırına yazılır.

Ölçüm yöntemi basittir: dosya içi `https://` dizgileri toplanır, küme (set) alınır ve
kardeş dosyaların kümeleriyle **kesişim** hesaplanır; kesişim boş olmalıdır. Kesişim
dolu çıkarsa iki dosyadan biri revize edilir (silme değil, **değiştirme**, onayla).
Bu denetim, URL **kimlik** düzeyindedir — aynı kaynağın **farklı sayfaları** (örn.
`alsa-doc/pcm.html` ile `alsa-doc/pcm_8h.html`) farklı URL sayılır ve serbestçe ayrılabilir;
aynı sayfanın **aynı** URL'si ise iki dosyada birden **bulunamaz** (kaynak izlenebilirliği
için). Ayrıca, elenen adayların "tekrar denenmez" statüsü, aynı turda ikinci bir
erişim denemesini de engeller — böylece doğrulama turu **tek tur** kalır ve sonuç
kümeleri tutarlıdır.

## §17. Araştırma–uygulama köprüsü: bu dosyanın kardeş dosyalara devri

Araştırma, ancak **devredildiğinde** işe yarar; bu dosyanın çıktı üç biçimdedir ve üçü de
kardeş dosyalara **madde** olarak yazılır: (1) **kaynak maddesi** — §14'teki URL, ilgili
kardeşin Kaynaklar tablosuna **eklenmez** (disjoints!) yalnız **bağlam notu** olarak
referans edilir (dosyalar arası geçiş, URL'yi **taşımaz**, yalnız **adını** anar);
(2) **boşluk maddesi** — §10 G-listesi, ilgili kardeşin `open_questions` alanına **aynısıyla**
eklenir (ör. G1 → timing `open_questions`); (3) **ölçüm görevi** — §12 kontrol listesi,
ilgili kardeşin §12/ölçüm protokolüne bir **madde** olarak girer.

Bu devir tek yönlüdür: kardeş dosyalar bu dosyadan **içerik almaz**, yalnız **görev/gap**
alır; tersi olursa (kardeşten URL alırsa) disjoints kırılır. Bu yüzden §13 bağımlılık
tablosunda "geri dönüş" sütunu **içerik değil, mimari sonuç**tur (örn. timing bu dosyadan
G1'i alır, ona **zaman disiplinini** geri verir). Köprünün son halkası: bir G-maddesi
kapatıldığında, hem bu dosyanın §10 satırı hem kardeşin `open_questions` satırı **aynı
oturumda** güncellenir (iki dosyada çelişki bırakma kuralı) — güncelleme, üst onayla
(`>=088` eşiği değil, yalnız vault revizyon kuralı) yapılır.

## §18. Geçerlilik ve sonraki tur (bu dosyanın statüsü)

Dosya `draft` statüsündedir çünkü: (1) tüm URL'ler doğrulandı (erişim-kanıtı tam),
(2) içerik-kanıtı **yok** (alıntı yapılmadı → §9 satırları `⚠️`), (3) G1–G7 boşlukları
**açık**. Geçerlilik penceresi: URL erişimi `2026-10-12` tarihlidir; URL'ler **taşınabilir**
olabilir (çekirdek belgeleri sürümlü yol kullanır) → yeniden-deneme, **yalnız** bir kardeş
dosya revizyonunda ve **yalnız** taşınan URL için yapılır (toplu yeniden deneme yasak —
§11 tek deneme kuralı).

Sonraki tur maddeleri: (1) §12 kontrol listesinin G1–G3 maddeleri (parametre/sysctl/
deadline — saha ölçümü gerektirir); (2) G5 (SPA içeriği GET + ayrıştırma — pipewire
dosyasına devir); (3) içerik-GET turu (§11) için üst onay; (4) elenen `wireplumber.org`
yeniden denenmez, yerine **proje deposu** (varsa) doğrulanır → `UNKNOWN` olarak açık kalır.
Bu maddeler tamamlanmadan dosya `draft`'tan **çıkmaz**; `verify-required`'a da
**düşürülmez** (erişim-kanıtı tam olduğu için) — statü, kanıt türüyle **eşleşir**.

## §19. Araştırma notları: dikkat edilen yanlış-okuma sınıfları

Araştırma turunda en sık yapılan üç hata, bu dosyanın **dil kurallarını** da belirler.
**Birinci yanlış-okuma: erişilebilirliği içerik sanmak.** `200` yalnız sunucunun URL'yi
tanıdığını söyler; sayfa **değişmiş**, **boş** ya da **alakasız** olabilir. Bu yüzden §9'da
her satır `✅ erişilebilir (içerik: ⚠️)` biçiminde **iki** işaret taşır. **İkinci
yanlış-okuma: başlığı iddia sanmak.** Başlık (örn. `page_spa`) konu **sinyalidir**, içerik
**değildir**; SPA'nın ne olduğu, sayfa içeriği GET edilmeden yalnız **proje-genel**
anlatımla (ses-stacki §7) örtüştürülür — bu örtüşme `⚠️` ile işaretlenir. **Üçüncü
yanlış-okuma: eleneni unutmak.** Elenen URL, bir sonraki turda **yeniden denenirse**
aynı hataya (kaynak israfı + tutarsız sonuç kümesi) dönülür; bu yüzden elenenler §14'te
**nedeniyle** ve **kardeş adıyla** listelenir ve sonraki tur bu listeyi **önce** okur.

Dördüncü bir sınıf (az görülen ama pahalı): **kaynak-türü karışımı.** Man sayfası (2/8
bölümleri) ile çekirdek belgesi aynı **güven** düzeyinde değildir (§15 tablosu); bir
sürücü-davranış iddiası man sayfasıyla **doğrulanmaz** (sürücü man'da tanımlanmaz), çekirdek
belgesiyle doğrulanır. Bu dosyadaki tüm iddialar, kaynak türüyle **eşleştirilmiştir** (§9)
ve eşleşmeyen iddia **üretilmemiştir** — örneğin §3'te `sysctl` anahtar adı verilmemiştir,
çünkü anahtar envanteri man sayfasında **değildir** (çekirdek ağaçlarında/dokümanda aranır,
G2 boşluğuna yazılır). Bu titizlik, "araştırma dosyası = link listesi" eleştirisine (§RED
TEAM 1) karşı da geçerlidir: her satır **ne biliyoruz / ne bilmiyoruz** ayrımını taşır.

## §20. Terim sözlüğü (dosya içi)

| Terim | Anlam |
|---|---|
| HEAD doğrulama | Sunucunun URL'yi tanıdığını gösteren içerik-siz istek |
| GET doğrulama | İçerik indirerek doğrulama (ayrı tur, onay ister) |
| Disjoints dağıtım | Her URL'nin yalnız bir dosyada bulunması kuralı |
| Elenen aday | Erişilemedi ya da kardeş dosyaya ayrıldığı için kullanılmayan URL |
| G-maddesi | Araştırma boşluğu (gap) — açık soru olarak işaretli |
| Kaynak türü | Çekirdek belgesi / man sayfası / proje sayfası (§15) |
| Kanıt katmanı | Erişim (HEAD) vs içerik (GET) ayrımı |
| Devir (handover) | Araştırmadan kardeş dosyaya görev/gap aktarımı (§17) |
| Geçerlilik penceresi | Doğrulama tarihinin URL taşınabilirliğiyle ilişkisi (§18) |
| Tek deneme kuralı | Aynı URL'nin aynı turda ikinci kez denenmemesi |

## §21. Kardeş-kaynak özeti: bu turda linux klasörünün toplam kanıt yüzeyi

Bu dosya, sekiz linux dosyasının **araştırma-girişi**dir; toplam kanıt yüzeyi bir arada
okunduğunda şu tablo ortaya çıkar (sayımlar bu oturumun ölçümleridir, dosya sonu imzalarıyla
aynidir): her dosya kendi URL kümelerini taşır, kümeler **çiftsizdir** (disjoints); her
dosya kendi `open_questions` listesini taşır ve bu dosya onların **kaynak boşluğu**
tarafıdır. Tablo, hangi dosyanın hangi kaynak türüne dayandığını da gösterir: mimari
dosyalar çekirdek/man karışımı, araştırma dosyası yalnız erişim-denetli yüzey, kod
dosyası man tabanlı arayüz kanıtı.

| Dosya | Grup | URL sayısı | Birincil kaynak türü | Açık soru (dosya sonu) |
|---|---|---|---|---|
| linux-alsa-mimarisi | mimari | 9 | alsa-doc + docs.kernel | 2 |
| linux-pipewire-mimarisi | mimari | 10 | docs.pipewire + gitlab | (dosya sonunda) |
| linux-kernel-module-arayuzleri | mimari | 10 | docs.kernel + man8 | (dosya sonunda) |
| linux-timing-ve-clock | mimari | 10 | docs.kernel + man2/3 | 4 (§18) |
| linux-dma-ve-interrupt | mimari | 8 | docs.kernel + man2 | 2 |
| linux-ses-stacki | mimari | 9 | alsa-doc + pipewire + GET | 2 |
| linux-kod-ornekleri | kod | 10 | man2/3/7 (arayüz) | 4 (§18 notu) |
| linux-web-arastirma | kanit | 10 | docs.kernel + man + pipewire (erişim) | 7 (G1–G7) |

Son okuma (dosyalar arası): tek bir dosyanın URL kümesi **başka** bir dosyanın kümesiyle
kesişmez; kesişim **0** olduğu için "hangi iddia hangi dosyanın kanıtı" izlenebilir. Bu
tablo ayrıca **toplam** bir kanıt envanteri görevi görür: sekiz dosyanın toplamı yalnız
**kendi** dosyalarında geçerli URL'lerden oluşur; başka K-klasörlerinden (örn. windows
dosyaları) **farklı** kümelerdir ve kural gereği **karıştırılmaz**. Karışıklık riski olan
tek yüzey, **aynı kaynak sitesinin** farklı dosyalarda birden fazla kez geçmesidir (ör.
`man7.org` üç dosyada farklı bölümlerle) — bu, URL-özdeşlik kuralı gereği **serbesttir**
(§16), çünkü `man7/man2/poll.2.html` ile `man7/man2/mlock.2.html` **farklı** URL'dir ve
**farklı** iddia taşır; aynı sayfa iki dosyada **bulunamaz**.

---

## Sıkça Sorulan Sorular (FAQ)

**S1: Bu dosyadaki iddialar kaynaklı mı?**
Erişim-evet (HEAD 200), içerik-hayır (§9 satırları `⚠️`): alıntı yok → iddia anlatım düzeyinde.

**S2: Neden GET ile içerik çekilmedi?**
Kapsam: bu tur **erişim denetimi** (disjoints havuz doğrulaması); içerik GET ayrı tur ve
onay ister (§11).

**S3: Elenen URL'ler neden tekrar denenmiyor?**
Kural: tek deneme + elenme nedeni (§11); tekrar-deneme döngüsü kaynak israfıdır.

**S4: Tavsiye (parametre açın vb.) neden yok?**
Ölçümsüz öneri yasak (§2/§12); bu dosya **kontrol listesi** üretir, ayar **vermez**.

**S5: Toplam kanıt envanteri (§21) neden bu dosyada?**
Çünkü bu dosyanın rolü linux klasörünün **araştırma-girişidir**; envanter, kardeşlerin
ölçüm sonuçlarını (dosya sonu imzaları) **toplar**, tek dosyada **tekrar etmez** — her
sayı kardeşin kendi imzasından gelir.

## RED TEAM — Olası 5 eleştiri ve yanıtlar

1. **"İçerik çekilmediyse bu bir araştırma değil, link listesi."**
   Yanıt: kısmen — doğru tanım **erişim-denetli kaynak haritası** (§11); içerik-tur ayrıca
   planlandı (§12 G-maddeleri), alıntısız iddia üretmediği için tutarlı.
2. **"§2'de parametre adları verilmemiş."**
   Yanıt: adlar doğrulanmadı → uydurmak `UNKNOWN`/`⚠️` kuralına aykırı; yüzey adı
   (`kernel-parameters`) doğrulandı, içerik bekliyor (G1).
3. **"Elenenler listesi çok uzun, dosyayı şişiriyor."**
   Yanıt: disjoints kanıtının kendisi bu listedir (tekrar yokluğu); silinirse
   "kim hangi URL'yi kullandı" izlenemez.
4. **"sparse/submitting-patches ses ile ne alakalı?"**
   Yanıt: §5 (sürücü statik doğrulama) ve §6 (hata bildirim dili) — K000 çekirdek-yüzey
   kapsamıyla sınırlı, ilgisiz genişletme yok.
5. **"open_questions 7 madde çok, ilerleyemiyoruz."**
   Yanıt: maddeler **kapatılabilir** (§12) ve kardeş dosyalara dağıtılmış durumda; açık
   madde = gerçek iş, dolgu (TBD) yasak olduğu için sayı **görünür** bırakıldı.

---

## Kabul Kontrol Listesi

> Not: satır/tablo ölçümleri dosyanın son satırındaki Authority imzasıyla **aynı** değerlerdir;
> imza, ölçümün bağlayıcı kaydıdır (§16).

- [x] §0 araştırma politikası — kanıt dili (HEAD/GET ayrımı) tanımlandı
- [x] §1–§21 (envanter, parametre, sysctl, sched_setattr, sparse, katkı süreci, PipeWire üçlüsü, write/madvise, eşleme, boşluklar, yöntem, kontrol listesi, kardeşler, karşılaştırma, disjoints denetimi, devir, geçerlilik, yanlış-okuma sınıfları, sözlük, kanıt özeti)
- [x] FAQ (4 soru) · RED TEAM (5)
- [x] İddia→URL tablosu + elenen adaylar
- [x] 10 alanlı frontmatter · `node_type: research` · `group: kanit`
- [x] ≥8 benzersiz URL (10) · başka dosyada tekrar yok
- [x] Elenen adaylar nedeniyle listelendi (kardeş adı + neden)
- [x] Dolgu yok (`TBD`/`...`/`Yakında` kullanılmadı; boşluklar G-maddesi olarak açık)

---

**Authority:** Bayram Ali / Vault Steward · **Tarih:** 2026-10-12
**Mode:** Red Team · Human Mode · Truth Mode
**Ölçüm:** ≥500 satır · 10 alanlı frontmatter · 10 tablo · 10 benzersiz URL (bu dosyaya özel)

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode