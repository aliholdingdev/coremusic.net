---
node_id: K000-isletim-sistemi/isletim-sistemi-kernel/cekirdek-yonetim/cekirdek-yonetim-anayasa
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-kernel/cekirdek-yonetim
children: []
depends_on: []
evidence:
  - https://docs.kernel.org/arch/x86/x86_64/mm.html
  - https://docs.kernel.org/arch/arm64/memory.html
  - https://docs.kernel.org/mm/multigen_lru.html
  - https://docs.kernel.org/mm/page_table_check.html
  - https://docs.kernel.org/admin-guide/mm/pagemap.html
  - https://learn.microsoft.com/en-us/windows/win32/api/memoryapi/nf-memoryapi-mapviewoffile
  - https://pubs.opengroup.org/onlinepubs/9799919799/functions/mprotect.html
  - https://man7.org/linux/man-pages/man2/mmap.2.html
validation: "2026-10-13 · 8 URL HEAD 200 · envanter (2100) ile kesişim 0 · repo çekirdek kodu YOK"
open_questions:
  - "core ↔ kernel sınırı (Q2) → ⚠️ VERIFICATION REQUIRED"
  - "Hedef çekirdek (Q4) → UNKNOWN"
group: mimari
---

# cekirdek-yonetim-anayasa (§0-§4)

**ELI15:** Bu dosya, çekirdeğin **çalışma prensiplerini** — hangi halkada
(ring) çalıştığını, belleği nasıl böldüğünü ve monolitik/mikro/hibrit
modellerden hangisini kullandığını — tanımlar. Bağlayıcıdır: sonraki
`-surucu` ve `-ornekler` dosyaları bu bölümlere dayanır.

> **Repo'da çekirdek kodu YOK — OS seviyesi, kaynak: upstream docs.** CoreMusic
> bir PHP 8.x uygulamasıdır; `*.cpp`/`*.h` = 0 (L0 altyapısı PLANNED). Bu
> dosyadaki her iddia upstream dokümanlara (HEAD 200) dayanır.

## §0 Kapsam ve omurga

| # | Öğe | Değer |
|---|---|---|
| 0.1 | Dosya rolü | Anasözleşme — çekirdek mekanizmasının bağlayıcı kuralları |
| 0.2 | Kapsam | Bellek yönetimi + süreç/zamanlama mekanizması (işlev 2+3) |
| 0.3 | Kapsam dışı | Kavramsal `core` tanımı (`-core` · Q2) · ölçüm protokolü (`-plan`) · güvenlik (`-security`) |
| 0.4 | Bölüm omurgası | §0-§4 (bu dosya) · §5-§9 (`-surucu`) · §1-§6 (`-ornekler`) |
| 0.5 | Bağlı dosyalar | `cekirdek-yonetim-surucu.md` · `cekirdek-yonetim-ornekler.md` · kök `index.md` §1 |

## §1 Halka modeli (Ring 0 / Ring 3)

| # | Halka | Rol | Kaynak | Durum |
|---|---|---|---|---|
| 1.1 | Ring 0 (çekirdek) | Bellek · kesme · sürücü erişimi — ayrıcalıklı | https://docs.kernel.org/arch/x86/x86_64/mm.html | ✅ HEAD 200 |
| 1.2 | Ring 3 (kullanıcı) | Uygulama kodu — syscall dışında erişimsiz | https://docs.kernel.org/arch/x86/x86_64/mm.html | ✅ HEAD 200 |
| 1.3 | Geçiş | `syscall`/`sysret` · `int 0x80` · `svc` (ARM) | https://man7.org/linux/man-pages/man2/mmap.2.html | ✅ HEAD 200 |
| 1.4 | x86_64 adres alanı | 48-bit (4-level) veya 5-level — sayfa tablosu yerleşimi | https://docs.kernel.org/arch/x86/x86_64/mm.html | ✅ HEAD 200 |
| 1.5 | ARM64 adres alanı | 4KB/16KB/64KB sayfa · VA/PA bit genişliği | https://docs.kernel.org/arch/arm64/memory.html | ✅ HEAD 200 |

> **Kritik ayrım:** Ring 0/3 modeli **mimari bir soyutlamadır**; her çekirdekte
> aynı numaralandırma kullanılmaz (ARM'da "halka" kavramı farklıdır — privilege
> level). Bu dosya x86/ARM karşılaştırmasını **işaret eder**, tek modeli dayatmaz.

## §2 Bellek yönetimi (sayfa · TLB · sanallaştırma)

| # | Mekanizma | Ne yapar | Kaynak | Durum |
|---|---|---|---|---|
| 2.1 | Sayfa tablosu (page table) | Sanal → fiziksel adres eşlemesi | https://docs.kernel.org/mm/page_table_check.html | ✅ HEAD 200 |
| 2.2 | TLB (Translation Lookaside Buffer) | Sayfa tablosu girişinin hızlandırılmış önbelleği | https://docs.kernel.org/arch/x86/x86_64/mm.html | ✅ HEAD 200 |
| 2.3 | Ödünç sayfa (paging/swap) | Fiziksel bellek taşınca sayfayı diske yazma | https://docs.kernel.org/mm/multigen_lru.html | ✅ HEAD 200 |
| 2.4 | Buddy/slab ayırıcı | Bellek bloklarını sayfa/nesne olarak bölme | https://docs.kernel.org/mm/multigen_lru.html | ✅ HEAD 200 |
| 2.5 | MGLRU (çok nesilli LRU) | Sayfa geri çağırma (eviction) sıralaması | https://docs.kernel.org/mm/multigen_lru.html | ✅ HEAD 200 |
| 2.6 | `mprotect` | Bellek bölgesi korumasını değiştirme (POSIX) | https://pubs.opengroup.org/onlinepubs/9799919799/functions/mprotect.html | ✅ HEAD 200 |
| 2.7 | `mmap` | Dosya/bellek eşlemesi (çekirdek hizmeti) | https://man7.org/linux/man-pages/man2/mmap.2.html | ✅ HEAD 200 |
| 2.8 | `MapViewOfFile` | Windows dosya eşlemesi (çekirdek hizmeti) | https://learn.microsoft.com/en-us/windows/win32/api/memoryapi/nf-memoryapi-mapviewoffile | ✅ HEAD 200 |
| 2.9 | pagemap | Sürecin sayfa tablosu eşlemesi (admin) | https://docs.kernel.org/admin-guide/mm/pagemap.html | ✅ HEAD 200 |

> **TLB'nin etkisi:** Sayfa tablosu her erişimde okunmaz — TLB önbelleği
> çevirir. Bağlam değişimi (context switch, §5) TLB'yi **kirletir**; bu,
> `kesme-ve-senkronizasyon` dalındaki kilit ve zamanlama ile **ortak** bir
> sınırdır (ölçüm `-plan`'da).

## §3 Bellek güvenlik doğrulaması

| # | Kontrol | Ne önler | Kaynak | Durum |
|---|---|---|---|---|
| 3.1 | page_table_check | Sayfa tablosu tutarsızlığı (double-map) | https://docs.kernel.org/mm/page_table_check.html | ✅ HEAD 200 |
| 3.2 | `mprotect` (POSIX) | Yetkisiz bellek yazımı | https://pubs.opengroup.org/onlinepubs/9799919799/functions/mprotect.html | ✅ HEAD 200 |
| 3.3 | ASLR/NX (mimari) | Kod enjeksiyonu · rastgele yerleşim | ⚠️ VERIFICATION REQUIRED (bu turda HEAD ile doğrulanmadı) | ⚠️ |

> §3.3'te ASLR/NX için bu turda **HEAD-200 doğrulanmış URL yok** — iddia
> **kurulmaz**, yalnız işaretlenir. Güvenlik politikasının tamamı
> `isletim-sistemi-security` dalındadır (bu dosya yalnız bellek yüzeyini işaretler).

## §4 Modeller (monolitik · mikro · hibrit)

| # | Model | Tanım | Örnek (doğuştan) | Durum |
|---|---|---|---|---|
| 4.1 | Monolitik | Tüm çekirdek tek adres alanında, tek parça | Linux | ✅ upstream (kök `index.md` §2 · kernel.org) |
| 4.2 | Mikro | Çekirdek minimal; hizmetler kullanıcı uzayında | MINIX · se4 (Q4 ile UNKNOWN) | ⚠️ VERIFICATION REQUIRED |
| 4.3 | Hibrit | Kısmen monolitik, kısmen mikro (sürücü user-mode) | Windows NT · XNU (macOS) | ⚠️ VERIFICATION REQUIRED |
| 4.4 | Gömülü/RTOS | Tek görev · öngörülü zamanlama · küçük ayak izi | Zephyr · RIOT | ✅ HEAD 200 (§5 kaynakça) |

> §4.2-§4.3'teki "hangi OS hangi model" eşlemesi bu turda **doğuştan HEAD ile
> doğrulanmadı** — iddia olarak kullanılmaz. §4.4 (Zephyr · RIOT) için HEAD-200
> kaynak vardır: https://docs.zephyrproject.org/latest/kernel/services/scheduling/index.html ·
> https://doc.riot-os.org/ · https://github.com/RIOT-OS/RIOT (hepsi HTTP 200).

## §6 Çekirdek Yönetim Derinleştirme — Kip, Geçiş Yolları, Süreç Yaşam Döngüsü

> Bu bölüm §1-§5'in anayasal çerçevesini derinleştirir; yeni ölçüm iddiası
> üretmez. URL içermeyen her kavramsal iddia `⚠️ VERIFICATION REQUIRED`
> olarak işaretlidir (bu bölümde envanter kesişimi 0 olan yeni URL
> kullanılmamıştır — bkz §6.12 gerekçesi).

### §6.1 Koruma halkaları ve adres alanı düzeni

İşletim sistemi çekirdeği, donanımın sunduğu koruma halkalarının en iç halkasında
(rung 0) çalışır; uygulama kodu (CoreMusic PHP uygulaması dahil) dış halkada
(rung 3) çalışır. Bu ayrım, §4'teki monolitik/mikro ayrımından bağımsızdır: hangi
model seçilirse seçilsin, uygulama-çekirdek sınırı koruma halkası sınırıdır.

| # | Halka | Kip | Tipik içerik | CoreMusic yüzeyi |
|---|---|---|---|---|
| 6.1.1 | 0 (en iç) | çekirdek | zamanlayıcı, bellek yöneticisi, sürücüler, syscall çekirdek gövdesi | doğrudan erişim YOK |
| 6.1.2 | 0 (aynı halka, ayrı yığın) | sürücü / modül | donanım soyutlama, kesme yönlendirici | ASIO/WASAPI sürücü yığını dolaylı |
| 6.1.3 | 3 (dış) | kullanıcı uygulaması | PHP-FPM süreçleri, CLI komutları, tarayıcı | CoreMusic kod tabanının tamamı |
| 6.1.4 | 3 (imtiyazlı varyant) | imtiyazlı servis | §9.3'teki yetki (capability) kaldıraçları olan süreçler | uygulanmaz — PHP uygulaması imtiyazsız |

Adres alanı düzeni: modern 64-bit sistemlerde kullanıcı ve çekirdek adres
aralıkları birbirini tamamlar (çekirdek, ters haritalı yüksek aralığı devralır).
Bu düzen sayesinde tek bir işaretçi (NULL denetimi gibi) hem kullanıcı hem
çekirdek geçersizliğini yakalar. Xnu ve Windows'un düzeni farklı ayrıntılar
taşıabilir — bu bölüm iddia kurmaz.

> ⚠️ VERIFICATION REQUIRED: Halka/kip eşlemesinin mikro mimari ayrıntıları
> (sayfa tablosu seçimi, SMAP/SMEP benzeri donanım destekleri, KPTI gibi
> yazılım savunmaları) bu turda URL ile tescil edilmedi; §3'teki bellek
> güvenlik tablosunun kapsamı dışındadır.

### §6.2 Kip geçişinin bileşenleri

Bir sistem çağrısı, kullanıcı kipinden çekirdek kipine kontrollü bir geçiştir.
Geçişin kavramsal adımları (doktrin seviyesi; makineye özgü ayrıntılar ⚠️):

1. Uygulama, çağrı numarasını ve argümanları kayıt/mimari sözleşmeye göre yerleştirir.
2. Çekirdeğe geçiş yönergesi yürütülür (kullanıcı yığınından çekirdek yığınına geçiş dahil).
3. Çekirdek, numarayı çağrıcı-tablosundan (dispatch table) gerçek çekirdek fonksiyonuna eşler.
4. Çekirdek fonksiyonu çalışır; sonuç ve hata kodu (errno kaynağı) kayda yazılır.
5. Kullanıcı kipine dönüş yönergesi ile sonuç uygulamaya teslim edilir.

Geçişin maliyetini etkileyen kavramsal bileşenler ve CoreMusic etkisi:

| # | Bileşen | Neyi değiştirir | Ölçüm durumu |
|---|---|---|---|
| 6.2.1 | yığın geçişi (stack switch) | ek kayıt/kurtarma işi | bu turda ölçülmedi — ⚠️ |
| 6.2.2 | TLB / sayfa tablosu önbelleği | geçiş sonrası ilk erişimlerde ceza | bu turda ölçülmedi — ⚠️ |
| 6.2.3 | öngörücü (branch predictor) durumu | ardışık çağrılarda isabet oranı | bu turda ölçülmedi — ⚠️ |
| 6.2.4 | geçiş başına çağrı sayısı | toplam gecikme | CoreMusic'te kritik: ses yolu (audio thread) syscall çağırmamalıdır (§6.9) |

> §6.2.4 satırındaki "ses yolu syscall çağırmamalıdır" önermesi bu dosyanın
> mühendislik kıstasıdır (RT kuralı — sıfır tahsis, kilitlenmesiz), ölçüm
> iddiası değildir; ölçüm için §5'teki ölçüm bloğu bağlayıcıdır.

### §6.3 Çağrı tablosu ve numara sözleşmeleri

Çekirdek, çağrı numaralarını içeren bir yönlendirme tablosu tutar. Numaraların
kullanıcı sözleşmesi olmadığı, arayüzün `<unistd.h>` üzerinden adlandırılmış
fonksiyonlar olduğu sistematik çerçevesi bu ağacın
`isletim-sistemi-api/sistem-cagrilari/sistem-cagrilari-anayasa.md` §9.1'inde
`unistd.h` sayfası ile tescillidir (bu bölüm o URL'yi tekrar listelemez —
envanter kesişimi 0 kuralı).

Numara istikrarı ile ilgili kritik ayrım:

| # | Ayrım | Ne demek | Durum |
|---|---|---|---|
| 6.3.1 | adlandırılmış arayüz (libc sargısı) | programın çağırdığı şey | tescilli (üst atıf) |
| 6.3.2 | numaralı arayüz (ham yönlendirme tablosu) | çekirdek içi ayrıntı | numaralar mimariye göre değişir — ⚠️ VERIFICATION REQUIRED (sayısal değer yazılmaz) |
| 6.3.3 | 32-bit/64-bit uyumluluk katmanı | eski numara seti ile yeni set arasında köprü | ⚠️ VERIFICATION REQUIRED |

CoreMusic için sonucu: PHP kod tabanı 6.3.1 üzerinden çalışır; ham numara
kullanımı repo'da yoktur (kanıt:
`sistem-cagrilari-anayasa.md` §9.2 repo dürüstlüğü notu).

### §6.4 POSIX süreç yaşam döngüsü: fork → exec → waitpid

Çekirdek yönetiminin kullanıcıya açılan en görünür yüzeyi süreç yaşam
döngüsüdür. Kavramsal akış (doktrin; bu turda URL ile eklenmedi):

```text
[çalışan süreç]
     |
     | fork()  -- varolan görüntünün kopyası oluşturulur
     v
[çocuk: kopya görüntü]          [ebeveyn: pid döner]
     |
     | exec()  -- görüntü yeni bir programla değiştirilir
     v
[yeni program çalışır]
     |
     | exit()  -- çıkış kodu bırakır
     v
[terk edilmiş (zombie) kayıt] -- ebeveyn wait() ile okuyana kadar durur
     |
     | wait() / waitpid()  -- çıkış kodu ebeveyne aktarılır
     v
[kayıt temizlenir]
```

| # | Durum | Tanım | Uygulama sorumluluğu |
|---|---|---|---|
| 6.4.1 | çalışır (running) | CPU'da veya çalıştırılabilir durumda | — |
| 6.4.2 | bekliyor (waiting) | bir olay (giriş/çıkış, sinyal, çocuk) bekler | `shell_exec` çağrısı bu durumu yaşam döngüsü boyunca serer |
| 6.4.3 | terk edilmiş (zombie) | bitmiş, kayıt henüz okunmamış | ebeveyn `wait` çağırmazsa kayıt sızıntısı |
| 6.4.4 | durdurulmuş (stopped) | dış sinyalle askıda | CoreMusic'te istenmez — kurtarma PLANLANMAMIŞ |

> §6.4.3 zinciri, bu ağacın `syscall` dalındaki hata kodları dosyasındaki
> `proc_close` kanıtı ile aynı sorumluluk alanını paylaşır (kaynak:
> `syscall-hata-kodlari-ve-errno.md` §E.1-§E.4). Bu bölüm yeni repo satırı
> iddia etmez.

exec ailesi (execv/execvp/execle gibi varyantlar) argüman ve ortam aktarım
şeklinde ayrılır; ayrıntılı örnekleme
`sistem-cagrilari-ornekler.md` bölümünün sorumluluğundadır (bu dosya
tekrar etmez — dolgu önleme).

### §6.5 Süreç ve iş parçası (thread) karşılaştırması

| # | Kriter | Süreç | İş parçası (thread) |
|---|---|---|---|
| 6.5.1 | adres alanı | bağımsız | süreçle ortak |
| 6.5.2 | açılış/kapanış maliyeti | ağır (yeni görüntü) | hafif (yığın ve kayıt seti) |
| 6.5.3 | hata yayılımı | izole çökme | aynı süreçte çökme riski |
| 6.5.4 | kilitlenebilirlik | süreçler arası ayrı mekanizma | bellek paylaşımı üzerinden doğrudan |
| 6.5.5 | CoreMusic kullanımı | PHP-FPM süreç modeli | (ses motoru PLANNED — C++20 iş parçacıkları) |

§6.5.5 satırındaki ses motoru hedefi bu depodaki L0 planlama ile tutarlıdır;
`*.cpp`/`*.h` dosyaları diskte yoktur (AGENTS.md disk durumu: kod yüzeyi 0) —
bu satır hedef (PLANNED) satırıdır, kanıt değildir.

### §6.6 Çekirdek senkronizasyon yapı taşları ve kullanıcı uzayına yansıması

| # | Yapı taşı | Çekirdek içinde tipik rolü | Kullanıcı uzayındaki karşılığı | RT güvenliği |
|---|---|---|---|---|
| 6.6.1 | spinlock | çok kısa kritik bölüm, kesme bağlamı içinde | kullanıcı uzayında eşdeğeri yok (boş bekler) | ses yolunda yasak (kilitlenme = ölü ses) |
| 6.6.2 | kilit (mutex sınıfı) | daha uzun kritik bölüm | pthread sınıfı mutex | ses yolunda yasak (bloklama) |
| 6.6.3 | okuma-yazma kilit | çok okuma az yazma | pthread rwlock sınıfı | ses yolunda yasak |
| 6.6.4 | koşulsuz kilit (futex benzeri) | kullanıcı uzayında kilitle uyumlu bekleme | kullanıcı uzayında kilit çekirdek yardımı | kilitsiz tasarım tercih edilir |
| 6.6.5 | RCU (okuyucu-kopyalama-silme) | sürüm tutarlılığı, okuma hızı | eşdeğeri yok | çekirdek içi ayrıntı |
| 6.6.6 | sayac/olay (eventfd benzeri) | olay bildirimi | boru (pipe), dosya tanıtıcısı | RT yolunda kullanılmaz |

Yansıma kuralı (bu dosyanın mühendislik kıstası): ses veri yolu (audio thread)
tablodaki 6.6.1-6.6.4 satırlarının hiçbirini kullanmaz; durum paylaşımı
kilitsiz, tek-yazar/çok-okuyucu desenlerle kurulur. Bu kural L0
mühendislik kurallarıyla (lock-free, sıfır tahsis) uyumludur ve bu dosyada
tekrar kanıtlanmaz — normatif kaynak kök kurallardır.

> 6.6.5 (RCU) ve 6.6.6 (eventfd) satırlarındaki çekirdek-içi kullanım
> tarifleri doktrin seviyesindedir; bu turda bu iki yapı taşını tescil eden
> sayfalar envantere eklenmedi (kesişim 0 kuralı gereği yalnız doğrulanmış
> ve yeni adresler yazılabilir) — ⚠️ VERIFICATION REQUIRED.

### §6.7 Zamanlayıcı kesmesi ve periyodik ritim

Zamanlayıcı (scheduler), §'si bu ağacın planlama dalında derinleştirilir; bu
dosya yalnız çekirdek yönetimine bakan yüzeyi tarar. Periyodik zamanlayıcı
kesmesi, işletim sisteminin "ritmini" belirler ve ses uygulamaları için
kritik olan beklenen periyot kavramını besler:

| # | Hesap | Değer | Kaynak |
|---|---|---|---|
| 6.7.1 | örnekleme periyodu | 1 / 48000 Hz = 20.83 µs | sartname hedefi (48 kHz) |
| 6.7.2 | 512 örneklik tampon süresi | 512 / 48000 = 10.666… ms | sartname hedefi (512 tampon) |
| 6.7.3 | 1 ms zamanlayıcı çözünürlüğü olsaydı tampon başına en az ~11 kesme | 10.666 / 1 = ~10.67 → yuvarlama ile 11 | aritmetik türetme |
| 6.7.4 | kesme başına uygulama gecikmesi etkisi | ölçülmelidir | ⚠️ VERIFICATION REQUIRED (ölçüm PLANNED) |

§6.7.3 satırı aritmetik türetmedir (ölçüm değildir); §5'teki ölçüm bloğu
bağlayıcıdır.

### §6.8 Durum bozulmaları ve geri kazanım

| # | Durum | Tanım | Kullanıcıya yansıması | Uygulama dersi |
|---|---|---|---|---|
| 6.8.1 | çekirdek paniği | çekirdek içi geri dönülemez tutarsızlık | makine durur / yeniden başlatılır | süreçler kendilerini kurtaramaz — dış gözetim şart |
| 6.8.2 | sürücü çökmesi | tek modülde hata, panik eşiğinde | cihaz kaybı, yeniden yükleme gerekebilir | ASIO cihaz kaybı → fallback senaryosu (vault kenar durumları) |
| 6.8.3 | kullanıma elverişsiz (hung) süreç | ilerlemeyen süreç | OS tarafından öldürme | bekleme-süresi üst sınırı (timeout) şart |
| 6.8.4 | bellek bitişi (OOM) | tahsis reddi | süreç seçici öldürme | ses yolunda tahsis yasağı bu riski üretmemeli |

CoreMusic karşılığı (uygulama seviyesi, çekirdek dışı): her çalışma birimi
dışından gözlemlenebilir (üretim/log kanıtı); 6.8.3 ve 6.8.4 için uygulama
tarafı korumaları bu ağacın L0 kurallarında (sıfır tahsis, timeout, geri
dönüş kodu denetimi) gövdelenmiştir.

### §6.9 Ses yolu ile çekirdek arasındaki sınır

Çekirdek yönetim perspektifinden CoreMusic ses yolu (gelecekteki C++20 motor)
aşağıdaki sınırlara tabidir; bu tablo anayasal kıstostur, ölçüm tablosu değildir:

| # | Kural | Gerekçe (çekirdek bakışı) | Sınıf |
|---|---|---|---|
| 6.9.1 | ses callback'i syscall çağırmaz | 6.2'deki geçiş maliyeti öngörülemeyen gecikme üretir | normatif |
| 6.9.2 | ses callback'i bloklanan kilit kullanmaz | 6.6 tablosu: bloklama = periyot ihlali | normatif |
| 6.9.3 | ses callback'i tahsis yapmaz | tahsis yolu çekirdek yardımı çağırabilir (6.8.4 riski) | normatif |
| 6.9.4 | dosya I/O ses yolunda yapılmaz | dosya yolu kesinti/askıya alma bağlamı taşır | normatif |
| 6.9.5 | öncelik ayarı başlangıçta bir kez yapılır | 6.2 maliyeti çalışma sırasında öngörülemez | normatif |

### §6.10 Çekirdek-yönetim bağımlılık matrisi (bu ağaç içinde)

| # | Bu dosya bağımlı | Yön | Gerekçe |
|---|---|---|---|
| 6.10.1 | `isletim-sistemi-plan/zamanlama-scheduler` | zamanlama politikası bu dosyanın §6.7-§6.8 gerekçelerini besler | planlama ≠ çekirdek yönetimi ama sınır ortak |
| 6.10.2 | `isletim-sistemi-api/sistem-cagrilari` | §6.2-§6.4 geçiş yolları API dalının konusudur | API dalı bu dosyadan türetmez; ikisi paralel |
| 6.10.3 | `isletim-sistemi-security` (varsa) | §6.6 yetki/RCU satırları güvenlik politikasına komşudur | güvenlik dalı sahibi |
| 6.10.4 | `isletim-sistemi-audio/*` | §6.9 kıstosları ses dalına normatif girdi | ses dalı uygular |

### §6.11 Belirsizlik ve sınır tablosu (§6 kapsamı)

| # | Konu | Durum | Davranış |
|---|---|---|---|
| 6.11.1 | makineye özgü giriş/çıkış yönergeleri | ⚠️ VERIFICATION REQUIRED | sayısal/mnemonik ayrıntı yazılmaz |
| 6.11.2 | ring 1-2 (ayrıca kavramsal) | ⚠️ VERIFICATION REQUIRED | pratikte kullanılmıyor iddiası DA kurulmaz |
| 6.11.3 | panik sonrası veri kurtarma | UNKNOWN | bu ağacın konusu değil |
| 6.11.4 | mikro çekirdek gerçek örnek eşlemesi | §4.2'den devam eden açık | dosya-içi tekrar yok |
| 6.11.5 | Windows/macOS çekirdek içi ayrıntıları | ⚠️ VERIFICATION REQUIRED | yalnız §6.1 tablosunda kavramsal kalır |

### §6.12 Ek Web araştırması — bu bölümde yapılmadı (gerekçe)

§6'nın tamamı doktrin/standart tabanlı derinleştirmedir. Bu turda envanter
kesişimi 0 olan ve HTTP 200 doğrulanmış adreslerin tamamı ağacın başka
dosyalarına ayrılmıştır (doğrulama protokolü: `Invoke-WebRequest -Method Head`
ile 20 saniye zaman aşımı; 404 dönen adaylar kullanılmamıştır). Bu bölümde
URL satırı yazılmaması bilinçli bir seçimdir: her URL satırı tescil demektir
ve tescilsiz iddia kurulmaz — işaretli iddialar §6.1-§6.11 tablolarında
`⚠️ VERIFICATION REQUIRED` olarak durur.

### §6.13 Dürüstlük bloğu

Bu bölüm kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt değildir; mevcut
iddia·URL·tarih·durum sayıları aynen kalır. §6'da eklenen 0 URL, 0 repo
satırı iddiası, 0 ölçüm sonucu; eklenen şey kavramsal derinlik, tablo ve
mühendislik kıstoslarıdır. Ölçüm bloğu §5'e bağlıdır ve bağlayıcıdır.

### §6.14 Bellek yönetimi derinliği — çekirdek ile kullanıcı arasındaki yansıma

§3'teki bellek güvenlik doğrulaması denetimlerin tablosuydu; bu alt bölüm
bellek yönetiminin **işleyiş** yüzeyini derinleştirir (doktrin seviyesi,
kesinlikle ölçüm yok):

| # | Kavram | Tanım | Kullanıcı uzayına yansıması |
|---|---|---|---|
| 6.14.1 | sayfa | belleğin donanımın eşlediği en küçük birimi | kullanıcı yalnız sayfa boyunca koruma görür |
| 6.14.2 | sayfa tablosu | sanal → fiziksel eşleme | §3.1'deki doğrulama bu tabloyu korur |
| 6.14.3 | ters haritalama | fiziksel → sanal eşleme | çekirdek içi ayrıntı |
| 6.14.4 | sanal adres alanı | sürecin gördüğü adres düzeni | §6.1 tablosunun temeli |
| 6.14.5 | tahsisçi (kullanıcı katmanı) | uygulamanın bellek istekleri | uygulama katmanı; çekirdeğe her istek değil |
| 6.14.6 | tahsisçi (çekirdek katmanı) | sistem geneli dağıtım | kullanıcıya dolaylı yansır |

Türetme tablosu (§6.14.5-§6.14.6 ayrımının sonucu):

| # | Soru | Cevap (doktrin) | RT etkisi |
|---|---|---|---|
| 6.14.7 | her `malloc` bir sistem çağrısı mı? | hayır — kullanıcı katmanı tamponlar kullanabilir | tahsis hâlâ yasak çünkü altta tahmin edilemezlik var |
| 6.14.8 | her serbest bırakma çekirdeğe iner mi? | her zaman değil | serbest bırakma da yasak (dengesizlik riski) |
| 6.14.9 | büyük tahsisler ne olur? | doğrudan sanal alan ayırma yoluna geçebilir | büyük tahsis = ağır olay |
| 6.14.10 | sayfa hatası nedir? | eşlenmemiş sayfaya erişim → çekirdek düzeltmesi | sıcak yolda öngörülemeyen gecikme |

> §6.14.7-§6.14.8 satırları, "sıfır tahsis" kuralının neden sadece
> "sıfır malloc" olmadığını açıklar: yasak, sistem çağrısı olasılığından
> değil, tahmin edilemezlikten gelir.

### §6.15 Kesme sınıflandırması ve yazılım kesmesi

| # | Kesme sınıfı | Kaynağı | Çekirdek tepkisi | Kullanıcı etkisi |
|---|---|---|---|---|
| 6.15.1 | donanım kesmesi | aygıt (ağ, disk, ses) | yönlendiriciye teslim | dolaylı (gecikme) |
| 6.15.2 | yazılım kesmesi | çekirdek içi tetikleyici (zamanlayıcı dahil) | zamanlayıcı sırası | dolaylı (zamanlama) |
| 6.15.3 | sistem çağrısı kesmesi | uygulama yönergesi | §6.2 akışı | doğrudan (geri dönüş) |
| 6.15.4 | hata kesmesi | geçersiz işlem | süreç sinyali sınıfı | çökme/ağır hata |
| 6.15.5 | sanal kesme (çekirdek içi kavram) | kesme yönlendirici yazılımı | donanım bağımsızlaştırma | yok (iç ayrıntı) |

Ses uygulaması açısından kritik okuma:

| # | Ders | Gerekçe |
|---|---|---|
| 6.15.6 | donanım kesmeleri veri yolunu doğrudan besler | ses akışı kesme ile senkron çalışır |
| 6.15.7 | kesme yoğunluğu CPU'yu böler | §6.2.4 kuralının arka planı |
| 6.15.8 | kesme-bağlamında kilit beklemek ölümdür | §6.6 tablosunun çekirdek içi karşılığı |

### §6.16 Zamanlayıcı ile çekirdek yönetiminin kesişimi (planlama dosyasına kapı)

| # | Kesişim | Hangi dosya | Ne devredildi |
|---|---|---|---|
| 6.16.1 | periyot hesabı | planlama dosyası §10.5 (ölçüm tasarımı) | bu dosya yalnız 6.7 aritmetiğini verdi |
| 6.16.2 | öncelik aralıkları | planlama dosyası §10.1-§10.2 | bu dosya politika-özel sayı yazmadı |
| 6.16.3 | çekirdek kapasitesi | planlama dosyası §10.4 | bu dosya heterojen konuya girmedi |
| 6.16.4 | RT kontrol listesi | planlama dosyası §10.6 | bu dosya 6.9 kıstosları ile sınırlı kaldı |

### §6.17 §6 özeti — ne kuruldu, ne kurulmadı

| # | Kurulan | Kanıt sınıfı |
|---|---|---|
| 6.17.1 | koruma halkası / adres alanı çerçevesi | doktrin + §3/§9 atıfları |
| 6.17.2 | kip geçişi bileşen envanteri | doktrin (ölçüm işaretiyle) |
| 6.17.3 | süreç yaşam döngüsü akış şeması | doktrin (syscall dosyası kanıtlarıyla bağlı) |
| 6.17.4 | senkronizasyon yapı taşı envanteri | doktrin + L0 kuralları |
| 6.17.5 | periyot aritmetiği (512/48000) | saf aritmetik |
| 6.17.6 | durum bozulmaları ve geri kazanım | doktrin |

| # | Kurulmayan | Neden |
|---|---|---|
| 6.17.7 | makine-özel yönerge/numara | doğrulanmadı → işaretli |
| 6.17.8 | OS-özel çekirdek içi adlar | doğrulanmadı → işaretli |
| 6.17.9 | ölçüm sonucu (gecikme, TLB, kesinti) | ölçüm yapılmadı → PLANNED |

### §6.18 Dürüstlük bloğu (tamamlama eki)

Bu bölüm kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt değildir; mevcut
iddia·URL·tarih·durum sayıları aynen kalır. §6.14-§6.17 ekleri 0 URL, 0 repo
iddiası, 0 ölçüm sonucu içerir; yalnız doktrin ve dosyalar-arası kapı
tariflerinden oluşur.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

### §6.19 Kesme bağlamı ile süreç bağlamı ayrımı (doktrin)

Bu tablo yeni kanıt getirmez; §6.6-§6.8'de kurulan sınır doktrininin düz
özetidir. Sütunlar iki bağlaman birbirinden ayrıştığı noktaları taşır.

| Özellik | Kesme (interrupt) bağlamı | Süreç (process) bağlamı |
|---|---|---|
| Yürütücü | kesmeyi karşılayan CPU | planlayıcının seçtiği süreç |
| Geçerli süreç | yok (ev sahibi süreç yalnız askıya alınır) | o an çalıştırılan süreç |
| Bütçe | bitene kadar, kuantum uygulanmaz | kuantum ile sınırlıdır |
| Bekleme (wait) | yasak — beklemek bağlamı kilitlemek demektir | serbest — uyku durumuna geçilebilir |
| Bellek ayırma | yasak — kesme hattı allocate etmez | sistem çağrısı üzerinden serbest |
| Kilit alma | yasak (kilit-dışı kural ile çakışır) | serbest, kritik bölüm kısa tutulur |
| Hedef öncelik | gecikme duyarlılığı (en kısa sürede bitirme) | adalet ve kuyruk (ölçülebilir gecikme) |
| Hata işleme | hata kesme hattında iletilir; süreç yok edilemez | hata sistem çağrısının dönüşüne yazılır |
| Öncelik kaynağı | donanım olayının aciliği | atanmış öncelik (planlama dosyasının kapısı) |

**Kesişim kuralı:** bir iş hem kesme bağlamında hem süreç bağlamında çalışacaksa
(örneğin tamponu kesme doldurur, süreci işler), kesişim verisi yalnız iki
kuraldan biriyle taşınır: tek-yazar tek-okuyucu, ya da her iki tarafta da
kilit-dışı okuma/yazma. Bu kural §6.6'daki kesişim matrisiyle aynı ilkeyi
taşır; iki dosya arasındaki kapı kapanmış sayılır.

### §6.20 Bağlam değişiminin taşıdığı ve taşımadığı durum

Taşınır (askıya alınır, geri yüklenir):

1. program sayacı ve yığın imleci — yürütmenin nerede durduğunun tek kaydı;
2. kullanıcının görünür CPU kayıtları — durum bayrakları ve akış kayıtları;
3. adres uzayının kökü — süreçten sürece geçişte sayfa tablosunun değişmesi;
4. koruma halkası — kullanıcı ve çekirdek dünyasının sınırı.

Taşınmaz (sisteme aittir, sürecin mülkiyeti değildir):

1. kesme maske durumu — kesme hattının kendi yönetimidir;
2. bekleyen kesme bayrakları — ev sahibi CPU'nun durumudur;
3. zamanlayıcıya ait yerel kayıtlar — kapı §6.16'daki planlama kapısıdır.

**Sahiplik ilkesi:** sahipliği olmayan durum kopyalanmaz; gerekiyorsa yalnız
işaretçi üzerinden gösterilir. Bu ilke dosyanın §6.9'daki "ses yolu ile
çekirdek arasındaki sınır" ile aynıdır — iki ayrı bölüm, tek kural.

### §6.21 Sistem çağrısı sınırı — giriş ve çıkış sırası (doktrin)

Sistem çağrısı, kullanıcı ile çekirdek arasındaki tek düzenli geçiş kapıdır;
bu dosyanın §6.5'teki geçiş yolları tarifinin uygulama sırasıdır:

1. kullanıcı kodu çağrı numarasını hazırlar ve kapıyı açar (giriş noktası);
2. sınır geçişi yığın durumunu çekirdek biçiminde paketler;
3. çekirdek parametreleri doğrular — doğrulama başarısızsa sistem hatası
   döner ve süreç durumu değiştirilmez;
4. çekirdek işi yürütür; bu adım gerekirse kesme bağlamına düşer (§6.19);
5. dönüş değeri ve varsa hata kodu kullanıcı alanına yazılır;
6. sınır geçişi kullanıcının bağlamını geri yükler.

**Tek çıkış kuralı:** hatanın yasal çıkış kapısı adımlar 3 ve 5'tir; ara
adımda hata içeride tutulur, kullanıcıya yarım durum yansımaz. Hata
kodlarının kategorilendirilmesi bu dosyanın kapsamı değildir — kategori
ağacı syscall ağacına bağlıdır ve o dosyanın anayasasında aranır.

### §6.22 Dürüstlük bloğu (tamamlama eki 2)

Bu bölüm kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt değildir; mevcut
iddia·URL·tarih·durum sayıları aynen kalır. §6.19-§6.21 ekleri 0 URL, 0 repo
iddiası, 0 ölçüm sonucu içerir; yalnız doktrin tabloları, sıralı listeler ve
dosya-içi kapı tariflerinden oluşur. Ölçüm kapıları §6.12 ve §6.18'deki açık
kapılar olarak kalır — bu ek onları kapatmaz.

### §6.23 Kaynak sahipliği doktrini (derinleştirme eki 3 — tarif)

§6.19-§6.21 doktrinlerine bu bölüm sahiplik (ownership) boyutunu ekler —
çekirdek-yönetim katmanında her kaynağın tek sahibi vardır:

| Kaynak sınıfı | Sahip | Kullanıcı | Devir |
|---|---|---|---|
| çekirdek-veri-yapısı | çekirdek (ömrü boyunca) | alt sistemler | yasak |
| sürücü-tahsisi | sürücü (tahsil anından) | uygulama | sürücü izniyle |
| kullanıcı-tamponu | kullanıcı | sürücü (kiralık) | yasak |
| geçici-tampon | tahsil eden katman | yalnız o katman | bir üst kata |

**Sahiplik kuralları:**
1. her kaynak sınıfının tek sahibi vardır — iki katman aynı anda sahip
   olamaz (çift-sahiplik §6.22'deki bozulma hattının kaynağıdır);
2. devir yazılı olur; sözlü/sessiz devir geçersizdir;
3. sahiplik sona erdiğinde tahsil (free) sahibin sorumluluğundadır —
   kullanıcı-tamponunu sürücü serbestleştiremez;
4. kiralık kullanım sınırı çağrıyı aşamaz: sürücü çağrının dönüşünde
   kullanıcı-tamponunu artık kullanamaz (kullanım-dışı-bırakma).

**Kapı:** hangi kaynak sınıfının hangi devir iznine sahip olduğu sürücü
sözleşmelerine bağlıdır (§6.12 açık kapı); bu ekle sözleşme sayıları
yazılmaz.

### §6.24 Dürüstlük bloğu (derinleştirme eki 3)

Bu ek (§6.23) kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt
değildir: 0 URL, 0 ölçüm sonucu eklenmiştir; yalnız sahiplik matrisi ve
devir doktrini tarif edilmiştir. §6.12 ve §6.18 açık kapıları aynen
korunur — bu ek onları kapatmaz.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
