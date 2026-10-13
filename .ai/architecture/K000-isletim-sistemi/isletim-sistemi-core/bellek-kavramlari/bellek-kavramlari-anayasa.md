---
node_id: K000-isletim-sistemi/isletim-sistemi-core/bellek-kavramlari/bellek-kavramlari-anayasa
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-core/bellek-kavramlari
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-core/bellek-kavramlari
evidence:
  - https://man7.org/linux/man-pages/man2/memfd_create.2.html
  - https://man7.org/linux/man-pages/man2/msync.2.html
  - https://man7.org/linux/man-pages/man2/swapon.2.html
  - shared/src/Cache/MemoryAdapter.php:8
validation: "2026-10-13 · 3 yeni URL HTTP HEAD 200 (envanter kesişimi 0) · ≥500 satır disk ölçümü · repo satırı ReadAllLines ile birebir"
open_questions:
  - "ASLR/NX politikası bu dalda mı, `-security` dalında mı? → ⚠️ VERIFICATION REQUIRED (Q-B1)"
  - "Hedef platform sayfa boyu / seviye sayısı → UNKNOWN (Q-B2)"
  - "Üretim bellek eşiği → UNKNOWN — ölçüm PLANNED (Q-B3)"
group: mimari
---

# bellek-kavramlari-anayasa — Bağlayıcı Tanımlar ve İlkeler (Kademe-5)

## §0 ELI15

Bellek, işletim sisteminin **paylaştığı tek fiziksel kaynaktır**. Her sürecin
kendi **sanal adres uzayı** vardır; bu uzam sayfalara bölünür, sayfa tablosuyla
çevrilir, yetkisiz erişim **koruma bitleriyle** durdurulur ve fiziksel bellek
dolduğunda sayfalar **swap'e** sürülür. Bu anayasa beş kavramı — **adresleme ·
sayfalama · bellek koruma · swap · bellek metrikleri** — bağlayıcı biçimde
tanımlar; mekanizma ayrıntısı `-surucu`, repo yüzeyi `-ornekler`dadır.

## §1 Kimlik

| Alan | Değer |
|---|---|
| node_id | `K000-isletim-sistemi/isletim-sistemi-core/bellek-kavramlari/bellek-kavramlari-anayasa` |
| Üst düğüm | `isletim-sistemi-core/bellek-kavramlari` |
| Kademe | 5 (içerik) — alt-dal `index.md` Kademe-4 |
| Konu | bellek kavramları: adresleme · sayfalama · koruma · swap · metrik |
| Bağımlılık | üst düğüm `isletim-sistemi-core` (kavram çerçevesi) |
| Komşu | `isletim-sistemi-kernel` (mekanizma) · `isletim-sistemi-security` (politika) |
| Dosya rolü | bağlayıcı tanım + ilke + eşik (kod yazmaz) |
| Kanıt sınıfı | upstream docs (repo'da OS seviyesi bellek kodu YOK) |
| group | mimari |
| Durum | draft (Q-B1 · Q-B2 · Q-B3 açık) |

**OS dürüstlük cümlesi:** CoreMusic reposunda çekirdek/sürücü kodu **YOK** — bu
dosyadaki OS iddialarının kaynağı **upstream docs** (§9); repo'daki bellek
yüzeyi yalnız PHP katmanıdır ve `-ornekler` dosyasındadır.

## §2 Kavram modeli — beş omurga maddesi

### §2.1 Adresleme — sanal adres uzayı

| Kavram | Tanım | Birim | Sınıf |
|---|---|---|---|
| sanal adres (virtual address) | sürecin gördüğü adres | byte | tanımlı |
| fiziksel adres (physical address) | donanımın gördüğü adres | byte | tanımlı |
| adres uzayı (address space) | bir sürecin erişebildiği sanal aralık | bayt | tanımlı |
| sözleşmeli adres (user-space) | uygulamanın yasal aralığı | bayt | tanımlı |
| çekirdek adresi (kernel-space) | çekirdeğin ayrılmış aralığı | bayt | tanımlı |
| bit genişliği | adresin taşıdığı bilgi | bit (32/48/57) | platforma bağlı |
| sayfa (page) | adreslezmin en küçük birimi | sayfa boyu (4 KiB tipik) | ⚠️ platform doğrulaması gerek |
| segment | eski/korunan modelde bölüm | byte | tarihsel |

**İlke B1 (bağlayıcı):** Uygulama kodu **fiziksel adres yazamaz**; yalnız sanal
adresle çalışır. Fiziksel adres ilişkisi yalnız işletim sistemi ve donanım
katmanındadır.

**İlke B2:** Adres bit genişliği tek başına bellek kapasitesi **değildir**:
48 bit sanal adres 256 TiB sanal aralık verir ama fiziksel bellek donanım ve
sayfa tablosu ile sınırlıdır (kapasite hesabı `-surucu` §5).

### §2.2 Sayfalama — sanal ↔ fiziksel çevirinin birimi

| Bileşen | Görevi | Ne taşır | Bozulursa |
|---|---|---|---|
| sayfa tablosu (page table) | sanal → fiziksel eşleme | giriş (entry) | sayfa hatası / çöküş |
| TLB (translation lookaside buffer) | çeviri önbelleği | en son eşlemeler | performans kaybı (TLB miss) |
| sayfa dizini (page directory) | seviyeli dizin | üst seviye girişler | genişleme hatası |
| frame (çerçeve) | fiziksel bellek dilimi | sayfa verisi | veri kaybı yok — sayfa yeniden yüklenir |
| swap girişi | diske sürülmüş sayfanın yeri | diske taşınmış veri | erişimde gecikme |
| koruma bitleri | erişim izni (R/W/X · kullanıcı/çekirdek) | yetki | ihlal → sinyal |

**İlke B3:** Sayfa boyu **tek** bir platform gerçeğidir ve ölçülmeden yazılmaz
(Q-B2). Bu dosyada "4 KiB" yalnızca **tipik değer** olarak anılır, doğrulanmış
eşik olarak kullanılmaz.

**İlke B4:** TLB vurumu bir **performans** olayıdır, doğruluk olayı değildir;
TLB'siz çeviri de **doğru** sonuç verir, yalnızca yavaştır.

### §2.3 Bellek koruma — erişim izinleri

| Koruma | Açılış | Ne engeller | İhlal belirtisi |
|---|---|---|---|
| oku (R) | read | okuma yoksa erişim hatası | sayfa hatası |
| yaz (W) | write | yetkisiz yazım | ihlal sinyali |
| yürüt (X) | execute | veri belleğinde kod çalıştırma | ihlal sinyali |
| kullanıcı/çekirdek ayrımı | user/kernel | uygulamanın çekirdek erişimi | ihlal sinyali |
| W^X | write XOR execute | aynı anda yaz+yürüt | ihlal sinyali |
| salt-okunur sayfa | read-only | değişiklik | ihlal sinyali |
| özel sayfa (private) | copy-on-write | süreçler arası veri paylaşım ihlali | (tasarım gereği kopya) |
| kilitli sayfa (mlocked) | locked | swap'e sürülme | `-EPERM`/sınırlar |

**İlke B5 (bağlayıcı):** Koruma **mikanizması** bellek katmanındadır (bu dal);
koruma **politikası** (hangi dosyaya hangi yetki, ASLR politikası, NX
uygulaması) `isletim-sistemi-security` dalındadır. İki dal arasında sınır:
**bit tanımı burada, karar kuralı orada.**

**İlke B6:** Bellek koruma ihlali **sessizce geçmez** — işletim sistemi
ihlali yakalar ve süreci öldürür veya sinyal gönderir (detay `-surucu` §3.3).

### §2.4 Swap — fiziksel bellek taşması

| Terim | Tanım | Yön | Sonuç |
|---|---|---|---|
| swap alanı | diske ayrılmış özel bölge | disk | fiziksel bellek kazancı |
| sayfa sürme (paging out) | sayfanın diske yazılması | bellek → disk | erişimde gecikme |
| sayfa geri getirme (paging in) | sayfanın geri okunması | disk → bellek | gecikme (page fault) |
| baskı (memory pressure) | talebin fiziksel belleği aştığı durum | — | swap etkinleşir |
| öncelik (LRU benzeri) | hangi sayfanın sürüleceği kuralı | — | yanlış seçim → thrash |
| thrash | sürekli sürme/geri getirme döngüsü | — | CPU/IO israfı |
| OOM killer | en büyük süreç seçip sonlandırma | Linux | süreç öldürme |
| ölü bellek (dead memory) | kullanılmayan ama paylaşılan sayfa | — | yeniden kullanılabilir |

**İlke B7:** Swap, bellek **sınırı** değildir; performans **sınırıdır**. Sistemi
koruyan şey OOM davranışıdır, swap'in kendisi değil.

**İlke B8:** Üretimde swap kullanımı **ölçülmeden** "var/yok" diye yazılmaz
(Q-B3).

### §2.5 Bellek metrikleri — ne ölçülür

| Metrik | Ne sayar | Birim | Kimde yaşar |
|---|---|---|---|
| süreç RSS (resident set size) | fiziksel bellekteki pay | KiB | OS sayacı |
| süreç VSZ (virtual size) | sanal adres uzayı | KiB | OS sayacı |
| paylaşılan sayfa (shared) | sayfa paylaşımı | KiB | OS sayacı |
| sayfa hatası (page fault) | çeviri olayları | adet | OS sayacı |
| kullanılabilir bellek | boş frame | MiB | OS sayacı |
| swap kullanımı | sürülmüş sayfa | MiB | OS sayacı |
| PHP bellek kullanımı | betik tahsisi | bayt | uygulama (`memory_get_usage`) |
| PHP zirve kullanımı | süreç zirvesi | bayt | uygulama (`memory_get_peak_usage`) |
| önbellek (cache/buffers) | dosya sistemi önbelleği | MiB | OS sayacı |

**İlke B9 (bağlayıcı):** Uygulama ölçümü (PHP) **süreç içi tahsis** ölçer; OS
ölçümü (RSS/VSZ) **fiziksel/kuruluş** ölçer. İkisi asla eşitlenemez ve tek
sayıda özetlenemez.

**İlke B10:** Ölçüm yoksa eşik yok. Bu dalda hiçbir bellek eşiği ("X MiB'de
alarm") uydurulmaz — eşik yalnız ölçüm sonrası yazılır.

### §2.6 Windows bellek modeli — aynı kavram, farklı adlandırma

| Kavram (Linux) | Karşılığı (Windows) | Fark | Sınıf |
|---|---|---|---|
| sanal adres uzayı | process address space | adlandırma | tanımlı |
| `mmap`/`munmap` | `VirtualAlloc`/`VirtualFree` | çağrı adı | tanımlı |
| sayfa (page) | page (granularity) | tipik 4 KiB / 64 KiB granularity ayrımı | ⚠️ platform doğrulaması gerek |
| RSS/VSZ | Working Set / Private Bytes | ölçüm tanımı farklı | tanımlı |
| sayfa hatası | page fault (exception) | aynı olay ailesi | tanımlı |
| swap alanı | page file | diske taşınan sayfa dosyası | tanımlı |
| `/proc/self/status` | `GetProcessMemoryInfo` | okuma arayüzü | tanımlı |
| `free -m` | `GlobalMemoryStatusEx` | sistem bellek okuması | tanımlı |

**İlke B11 (bağlayıcı):** İki platformun adlandırması eşleştirilir ama
**değerleri eşitlenmez**; "Working Set = RSS" yazısı yanlıştır — Working Set
paylaşılan sayfaları dahil eder, RSS'teki gibi yalnızca fizikselde yerleşik
olmayan sayfaları dışarıda bırakabilir (kesin sınır `⚠️ VERIFICATION REQUIRED`,
ölçüm ister). Bu dosyada eşleştirme yalnız **kavram eşlemesi** olarak durur.

**İlke B12:** Windows'ta `VirtualFree` çağrısı **sanal aralığı serbest bırakır**;
fiziksel çerçeve hemen geri alınmaz (sayfa tablosu girişi düşer, frame sonra
geri kazanılır). Linux `munmap` için de aynı kavram geçerlidir — serbest
bırakma ile fiziksel geri kazanım aynı anda **değildir**.

### §2.7 Eşlenmiş bellek (memory-mapped file) — dosya ile bellek sınırı

| Kavram | Tanım | Kullanım | Bölüm |
|---|---|---|---|
| eşleme (mapping) | dosyanın sanal adres uzamına bağlanması | büyük dosya okuma | §2.7 |
| paylaşımlı eşleme | süreçler arası ortak görünüm | IPC | §2.7 |
| özel eşleme (private) | copy-on-write kopya | değişiklik izolasyonu | §2.3 |
| eşleme bayrağı (msync) | değişikliğin diske yazılması | dayanıklılık | §3.4 |
| eşlenen dosya kilidi | dosya kilidi + eşleme | eşzamanlı erişim | `-dosya-sistemi` dalı |
| `memfd` | bellekte doğan dosya | geçici tampon | §2.2 |

**İlke B13:** Eşlenmiş bellek, dosya sistemi ile bellek arasındaki **tek yasal
köprüdür**; bu köprünün yazma/denkleştirme sorumluluğu `msync` benzeri bir
çağrıya bağlıdır ve çağrılmazsa veri diske **yazılmamış sayfa olarak** durur
(kayıp riski §3.4).

**İlke B14:** Eşleme boyutu sayfa boyuna **yuvarlanır**; `N` baytlık eşleme
istendiğinde fiili aralık `ceil(N / sayfa boyu) * sayfa boyu`dur. Sayfa boyu
bilinmeden (Q-B2) bu yuvarlama **hesaplanmaz** — dosyada yalnız bağıntı durur.

## §3 Mekanizma akışı — dört olay zinciri

### §3.1 Adres çevirisi akışı (her bellek erişiminde)

| Adım | Olay | Başarısızlık | Sonuç |
|---|---|---|---|
| 1 | CPU sanal adres üretir | — | devam |
| 2 | TLB'de aranır | vurum yok | 3'e geç (TLB miss) |
| 3 | sayfa tablosu okunur | geçersiz giriş | §3.2 (sayfa hatası) |
| 4 | koruma bitleri denetlenir | izin yok | §3.3 (ihlal) |
| 5 | fiziksel adrese çevir | — | erişim tamam |
| 6 | sonuç önbelleğe alınır (TLB) | — | sonraki erişim hızlı |

### §3.2 Sayfa hatası akışı

| Adım | Olay | Karar |
|---|---|---|
| 1 | geçersiz/olmayan sayfa girişi görülür | çekirdek devreye girer |
| 2 | sayfa hiç yüklenmiş mi? | evet → diskten yükle |
| 3 | sayfa swap'te mi? | evet → geri getir (disk okuma) |
| 4 | sayfa copy-on-write mı? | evet → kopyala, sonra yaz |
| 5 | sayfa yasal mı? | hayır → §3.3 |
| 6 | giriş geçerli olur, erişim sürer | uygulama fark etmez |

### §3.3 Koruma ihlali akışı

| Adım | Olay | Sonuç |
|---|---|---|
| 1 | R/W/X bitine aykırı erişim | ihlal yakalanır |
| 2 | çekirdek karar verir | sinyal / süreç sonu |
| 3 | uygulamada belirti | çöküş ya da hata yakalayıcı |
| 4 | kayıt | OS günlüğü (`-observability` dalı) |

### §3.4 Swap baskısı akışı

| Adım | Olay | Eşik |
|---|---|---|
| 1 | talep > kullanılabilir bellek | — (ölçüm) |
| 2 | sayfa önceliklendirilir | politika (LRU benzeri) |
| 3 | sayfa diske yazılır | disk gecikmesi |
| 4 | erişim gelince geri okunur | sayfa hatası |
| 5 | baskı sürerse | OOM değerlendirmesi |
| 6 | OOM seçilirse süreç sonlanır | veri kaybı riski |

### §3.5 Süreç başlatımında adres uzayının kurulması

| Adım | Olay | Sorumlu | Başarısızlık |
|---|---|---|---|
| 1 | yeni süreç nesnesi/yapısı | çekirdek | başlatma hatası |
| 2 | adres uzayı oluşturulur (boş) | çekirdek | — |
| 3 | çalıştırılabilir dosya eşlenir | yükleyici/loader | format hatası |
| 4 | yığın (stack) ayrılır | çekirdek | tahsis hatası |
| 5 | yığın (heap) başlangıcı kurulur | yükleyici + çekirdek | — |
| 6 | kütüphane bağlantıları (dynamic linking) | yükleyici | kütüphane yok |
| 7 | koruma bitleri yazılır | çekirdek | — |
| 8 | ilk komut çalıştırılır | CPU | — |

**İlke B15:** Adres uzayı **her süreç için ayrıdır**; iki süreç aynı sanal
adresi kullanabilir ve bu **çakışma sayılmaz** — çakışma ancak aynı sanal
adresin *aynı fiziksel frame'e* izinsiz eşlenmesiyle olur.

**İlke B16:** Yığın (stack) ve yığın (heap) **aynı sayfa havuzundan** gelir;
birinin taşması diğerinin erişimini doğrudan kırmaz, ama ikisi de baskı
(§3.4) üretir. Terimlerin ikisi de Türkçe'de "yığın" olduğundan bu dosyada
her kullanımda İngilizce karşılık **(stack)/(heap)** yazılır.

### §3.6 İhlal ve kurtarma olayları — sinyal/sayı tablosu

| Olay | Linux belirtisi | Windows belirtisi | Uygulamadaki karşılığı |
|---|---|---|---|
| okunamayan sayfa | `SIGSEGV` | access violation exception | çöküş / yakalayıcı |
| yürütme ihlali | `SIGSEGV` | access violation | çöküş |
| yazma ihlali | `SIGSEGV` | access violation | çöküş |
| yığına taşma (stack overflow) | `SIGSEGV` | stack overflow exception | derin özyineleme |
| OOM sonlandırma | `SIGKILL` (OOM killer) | working set baskısı altında süreç sonlandırma | anlık kapanış |
| denetimli kapanış | `SIGTERM` | WM_CLOSE / termination | temiz kapanış |

**İlke B17:** Uygulama katmanı bellek ihlalini **yakalayamaz ve düzeltemez**;
yalnız sonlanma biçimini seçebilir (kapanış yakalayıcı, son kayıt). "İhlali
düzeltme" yetkisi uygulamada **yoktur**.

## §4 Ölçüm / eşik — bu dalın ölçüm sözleşmesi

| # | Ölçü | Yöntem | Birim | Eşik durumu |
|---|---|---|---|---|
| Ö1 | PHP bellek kullanımı | `memory_get_usage(true)` | bayt | eşik `UNKNOWN` |
| Ö2 | PHP zirve | `memory_get_peak_usage(true)` | bayt | eşik `UNKNOWN` |
| Ö3 | süreç RSS | OS aracı (`ps`/`/proc`) | KiB | eşik `UNKNOWN` |
| Ö4 | kullanılabilir bellek | OS aracı | MiB | eşik `UNKNOWN` |
| Ö5 | swap kullanımı | OS aracı | MiB | eşik `UNKNOWN` |
| Ö6 | sayfa hatası sayısı | OS sayacı | adet | eşik `UNKNOWN` |
| Ö7 | Windows sistem belleği | `GlobalMemoryStatusEx` (§9) | bayt | eşik `UNKNOWN` |
| Ö8 | paylaşımlı önbellek kullanımı | APCu sayaçları | bayt | repo'da okuma yok → `UNKNOWN` |

**Ölçüm kuralı (bağlayıcı):** Ö1–Ö8 değerleri bu turda **alınmadı**; tablo
yalnızca **yöntem ve birim** sözleşmesidir. Eşik hücresi doldurulmadan alarm
kuralı yazılmaz (İlke B10).

### §4.1 Ölçüm komutları (yeniden üretilebilir, çalıştırılmadı)

```bash
# Linux: süreç RSS/VSZ + sayfa hatası
ps -o pid,rss,vsz,comm -p $$
grep -E 'VmRSS|VmSwap|voluntary_ctxt' /proc/self/status
# Linux: sistem belleği ve swap
free -m
```

Çalıştırma durumu: **bu turda çalıştırılmadı** (ortam kanıtı yok → `UNKNOWN`);
blok yeniden üretilebilir adım listesidir, uydurma çıktı içermez.

### §4.2 Ölçüm tuzakları — yanlış okuma tablosu

| # | Tuzak | Yanlış okuma | Doğru okuma |
|---|---|---|---|
| T1 | PHP `memory_get_usage(false)` | gerçek kullanım | tahsis edilen blok (fragmentasyon hariç) |
| T2 | PHP `memory_get_usage(true)` | yalnız PHP | işletim sisteminin gördüğü yakın değer |
| T3 | VSZ yüksek | bellek şişti | yalnız sanal aralık (sayfa henüz yok olabilir) |
| T4 | RSS düşüyor | sızıntı düzeldi | sayfa çekirdek tarafından geri alınmış olabilir |
| T5 | önbellek dolu | bellek yetersiz | önbellek geri kazanılabilir — baskı **değildir** |
| T6 | tek anlık okuma | eğilim | zirve/ortalama için örneklem gerekir |
| T7 | `memory_get_peak_usage` | süregelen zirve | süreç başlangıcından beri **tek** zirve |
| T8 | swap 0 | baskı yok | swap kapalı olabilir (Q-B6) |
| T9 | paylaşım (shared) PHP'de yok | yok | PHP ölçümü paylaşımı **görmez** |
| T10 | Windows Working Set = RSS | eşitlik | İlke B11 — eşitlenmez |

**İlke B18 (bağlayıcı):** Ölçüm değeri **tek başına yorumlanmaz**; her değer
yanına ölçüm yöntemi (T1–T10 hangisi) yazılır. Yöntemsiz bellek sayısı bu
dalda **geçersizdir**.

### §4.3 Ölçüm örneklemesi — ne sıklıkla, ne zaman

| Durum | Sıklık (öneri) | Birim | Eşik |
|---|---|---|---|
| uygulama içi sayaç | her istek sonu | bayt | `UNKNOWN` |
| sistem gözlemi | ⚠️ PLANNED (monitoring dalı) | KiB | `UNKNOWN` |
| tepe (peak) yakalama | süreç sonunda | bayt | `UNKNOWN` |
| baskı alarmı | sürekli izleme (PLANNED) | MiB | `UNKNOWN` |
| sürme (swap) sayımı | ⚠️ PLANNED | MiB | `UNKNOWN` |

**Ölçüm kuralı tekrarı:** Bu tablo yalnızca **sıklık sözleşmesidir**; hiçbir
eşik değeri burada üretilmez (İlke B10).

## §5 Komşu sınırı

| Sınır | Karşı taraf | Bu dosyada kalan | Giden |
|---|---|---|---|
| çekirdek içi veri yapıları | `isletim-sistemi-kernel` | kavram + akış | sayfa tablosu yerleşimi, buddy/SLUB |
| güvenlik politikası | `isletim-sistemi-security` | koruma **bitleri** | ASLR/NX/W^X kararları, ACL |
| gözlem toplama | `isletim-sistemi-observability` | metrik **tanımı** | okuma aracı, alarm |
| test stratejisi | `isletim-sistemi-testing` | ölçüm **sözleşmesi** | sızıntı/limit test tasarımı |
| API sargıları | `isletim-sistemi-api` | kavram | `memory_get_usage` sargı yüzeyi |
| OS türü karşılaştırması | `isletim-sistemi-turu` | platform satırı | kurulum/dağıtım farkları |
| ses tamponu planı | `isletim-sistemi-audio` | sayfalama kavramı | 512 örnek bellek planı |

### §5.1 Sınır kararları — hangi soru hangi dala gider

| Soru | Giden dal | Burada kalan cevap |
|---|---|---|
| sayfa tablosu kaç seviye? | `isletim-sistemi-kernel` | "seviyeli dizin" kavramı |
| ASLR açık mı? | `isletim-sistemi-security` | koruma biti tanımı |
| RSS alarmı kaç MiB? | `isletim-sistemi-observability` | ölçüm yöntemi (§4) |
| sızıntı testi nasıl yazılır? | `isletim-sistemi-testing` | ölçüm sözleşmesi (§4) |
| `memory_get_usage` sargısı nereye konur? | `isletim-sistemi-api` | fonksiyon adı + birim |
| Linux mu Windows mu? | `isletim-sistemi-turu` | §2.6 eşleme tablosu |
| 512 örneğin bütçesi? | `isletim-sistemi-audio` | sayfalama kavramı |
| `/proc` hangi betikle okunur? | `isletim-sistemi-observability` | okuma arayüzü adı |

**Sınır kuralı (bağlayıcı):** Bir soru bu tabloda "giden dal" sütununa
yazıldıysa, cevabı burada **üretilmez** — yalnızca giden dalın üretmesi
gereken bilgi kaydedilir. Cevasız kalan kısım `UNKNOWN` olarak durur.

### §5.2 Bağımlılık yönü — tek yön

| Yön | İlişki | Kural |
|---|---|---|
| bu dal → kernel | mekanizma kavramı okunur | çekirdek kodu buraya kopyalanmaz |
| bu dal → security | politika kavramı okunur | politika kararı buraya alınmaz |
| bu dal → observability | metrik okuma arayüzü | okuma betiği buraya yazılmaz |
| observability → bu dal | metrik **tanımı** | tanım değiştirilemez |
| testing → bu dal | ölçüm sözleşmesi | eşik testi buraya yazılmaz |

**İlke B19:** Bu dal **bilgi üretir** (tanım, ilke, eşik sözleşmesi) ama
**okuma yapmaz** (sürekli izleme, alarm, test çalıştırma). Okuma yapan dallar
bu daldaki tanımı **değiştiremez** — değişiklik ADR ister.

## §6 Terim sözlüğü

| Terim | İngilizce | Tanım (tek satır) |
|---|---|---|
| sanal adres | virtual address | sürecin gördüğü adres |
| fiziksel adres | physical address | donanımın gördüğü adres |
| sayfa | page | adreslemenin sabit boyutlu birimi |
| çerçeve | frame | fiziksel bellekte sayfa karşılığı |
| sayfa tablosu | page table | sanal→fizik eşlemesi |
| TLB | translation lookaside buffer | çeviri önbelleği |
| sayfa hatası | page fault | geçersiz/olmayan sayfaya erişim |
| koruma biti | protection bit | R/W/X ve kullanıcı/çekirdek izni |
| salt-okunur | read-only | yazım yasağı |
| copy-on-write | copy-on-write | yazarken kopyalama taktiği |
| swap | swap area | diske ayrılmış sayfa alanı |
| baskı | memory pressure | talebin fiziksel belleği aşması |
| thrash | thrash | sürekli sürme/geri getirme döngüsü |
| OOM killer | out-of-memory killer | bellek yetersizliğinde süreç sonlandırma |
| RSS | resident set size | fiziksel bellekteki süreç payı |
| VSZ | virtual size | süreç sanal adres uzayı |
| sayfa hatası sayacı | page fault counter | erişim olayı sayacı |
| tahsis | allocation | süreç için bellek ayırma |
| önbellek | cache | dosya sistemi/çekirdek önbelleği |
| paylaşımlı bellek | shared memory | süreçler arası ortak bellek |
| tahsis serbest bırakma | deallocation | ayrılan aralığın geri verilmesi |
| sanal aralık serbest bırakma | virtual free | `VirtualFree` karşılığı — aralık düşer, frame sonra |
| eşleme | mapping | dosya/tamponu adres uzamına bağlama |
| eşlenen dosya | mapped file | eşleme altındaki dosya |
| granularity | granularity | donanımın en küçük ayrıtım/adres birimi |
| working set | working set | süreçte fiilen kullanılan sayfa kumesi |
| özel bayt | private bytes | sürecin tek başına sahip olduğu fiziksel pay |
| bellek kirliliği | memory fragmentation | parçalanmış tahsis alanı |
| geri kazanım | reclamation | kullanılmayan sayfanın toplanması |
| basınç kaynağı | pressure source | talebi şişiren süreç/talep |
| sayfa sıfırlama | page zeroing | çerçeve yeniden verilmeden önce silinmesi |
| çekirdek önbelleği | kernel cache | sayfa önbelleği (page cache) |
| doğrudan erişim | direct I/O | önbelleği baypas eden dosya erişimi |
| asenkron sürme | async writeback | arka planda denkleştirme |

## §7 Kapsam dışı — bu dosyaya yazılmaz

| Madde | Sahibi | Neden |
|---|---|---|
| buddy/SLUB ayrık dağıtıcı ayrıntısı | `isletim-sistemi-kernel` | çekirdek içi mekanizma |
| ASLR/NX politika kararları | `isletim-sistemi-security` | politika, tanım değil |
| `/proc` okuma betiği | `isletim-sistemi-observability` | gözlem aracı |
| PHP `memory_get_usage` kullanım örnekleri | `-ornekler` (bu dal) | örnek katmanı |
| bellek sızıntısı test kodu | `isletim-sistemi-testing` | test stratejisi |
| DMA/tampon bellek donanım planı | `isletim-sistemi-audio` | ses donanım planı |
| 512/48 kHz tampon bütçesi hesabı | `isletim-sistemi-audio` | ölçüm ADR'si (PLANNED) |

### §7.1 Bu dosyada bulunan ama işlenmeyen kavramlar

| Kavram | Neden işlenmedi | Etiket |
|---|---|---|
| NUMA yerleşimi | hedef donanım bilinmiyor | `UNKNOWN` |
| huge page / dev page | platform ölçümü yok (Q-B2) | `UNKNOWN` |
| bellek sıkıştırma (zswap/zram) | ortam okunmadı | `UNKNOWN` |
| DMA tamponu ayırma | donanım planı `-audio` dalında | PLANNED |
| kuantum/bant genişliği (RAM speed) | ölçüm yapılmadı | `UNKNOWN` |
| ECC/parity bellek | donanım bilgisi yok | `UNKNOWN` |
| bellek denetimi (sanitizer) | test dalının konusu | PLANNED |

**Kural:** Bu tabloda adı geçen kavramlar bu dosyada **tanımlanmaz**; yalnız
sahibi belirtilir. Tanım isteği ilgili dala yönlendirilir (§5.1).

## §8 Açık sorular

| # | Soru | Etiket | Kapanış şartı |
|---|---|---|---|
| Q-B1 | Koruma politikası (ASLR/NX) hangi dalda? | ⚠️ VERIFICATION REQUIRED | karar + ADR |
| Q-B2 | Hedef platform sayfa boyu/seviye sayısı | UNKNOWN | ölçüm/ortam kanıtı |
| Q-B3 | Üretim bellek eşiği | UNKNOWN | ölçüm (§4) |
| Q-B4 | Neva Engine tampon planı bu dala girer mi? | ⚠️ VERIFICATION REQUIRED | ADR-019 sınır kararı |
| Q-B5 | APCu paylaşımlı bellek kapasitesi | UNKNOWN | üretim okuması |
| Q-B6 | Swap açık mı, politika nedir? | UNKNOWN | ortam okuması |
| Q-B7 | Bellek sayacı hangi aralıkta örneklenmeli? | PLANNED | monitoring dalı ölçüm kararı |
| Q-B8 | Windows hedefinde Working Set mi, Private Bytes mı? | ⚠️ VERIFICATION REQUIRED | platform kararı (§2.6 İlke B11) |
| Q-B9 | Eşlenmiş dosya kullanımı repo'da var mı? | UNKNOWN | `mmap`/`file-put-contents` taraması yapılmadı |

Bu sorulardan hiçbiri bu turda kapatılmadı; kapatılmadan dosya `draft`
statüsünden çıkmaz.

## §9 Kaynakça — 3 URL, HTTP HEAD 200, envanter kesişimi 0

| # | İddia | URL | Tarih | Durum |
|---|---|---|---|---|
| B-W1 | `memfd_create(2)` — bellekte dosya/çerçeve yaratma (§2.2 çerçeve) | https://man7.org/linux/man-pages/man2/memfd_create.2.html | 2026-10-13 | 200 |
| B-W2 | `msync(2)` — eşlenmiş belleği diske denkleştirme (§3.2/§3.4) | https://man7.org/linux/man-pages/man2/msync.2.html | 2026-10-13 | 200 |
| B-W3 | `swapon(2)` — swap alanını etkinleştirme (§2.4) | https://man7.org/linux/man-pages/man2/swapon.2.html | 2026-10-13 | 200 |

Doğrulama yöntemi: `Invoke-WebRequest -Method Head -UseBasicParsing -Uri $u -TimeoutSec 20`
→ HTTP 200 (2026-10-13). Envanter kesişimi: `.ai/architecture/**` içindeki 2633
benzersiz URL ile karşılaştırıldı → **0**.

`-surucu` ve `-ornekler` kendi URL'lerini ayrı taşır (benzersizlik korunur).

### §9.1 Kanıt sınıfı ayrımı — hangi iddia nereden gelir

| İddia sınıfı | Kaynak | Bu dosyadaki örnek | Durum |
|---|---|---|---|
| OS davranışı (Linux) | man7 upstream docs | `memfd_create` · `msync` · `swapon` | 3 URL HTTP 200 |
| OS davranışı (Windows) | Microsoft Learn (§2.6) | `VirtualFree` kavramı | ⚠️ URL bu dosyada değil — Q-B8 |
| repo yüzeyi (PHP) | `shared/src/Cache/MemoryAdapter.php` | tahsis serbest bırakma örneği | `-ornekler` dosyasında |
| ölçüm değeri | ortam okuması | Ö1–Ö8 | `UNKNOWN` (çalıştırılmadı) |
| eşik değeri | — | — | yazılmaz (İlke B10) |

**Dürüstlük kuralı:** "Linux'ta böyle" iddiası yalnız upstream URL ile;
"repo'da böyle" iddiası yalnız gerçek dosya/satır ile yazılır. Ölçüm ve eşik
satırları bu iki sınıfın **hiçbirine girmez** — `UNKNOWN` etiketi taşır.

### §9.2 Bu dosyanın ölçülebilir çıktıları (tek bakışta)

| # | Çıktı | Sayı | Ölçüm |
|---|---|---|---|
| 1 | bağlayıcı ilke (B) | 19 | §2–§5 sayımı |
| 2 | mekanizma akışı | 6 | §3.1–§3.6 |
| 3 | ölçüm sözleşmesi | 8 (Ö1–Ö8) | §4 |
| 4 | açık soru | 9 (Q-B1–Q-B9) | §8 |
| 5 | upstream URL | 3 | §9 (HTTP 200) |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
