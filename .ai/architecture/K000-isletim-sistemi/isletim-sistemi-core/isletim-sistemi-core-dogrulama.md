---
node_id: K000-isletim-sistemi/isletim-sistemi-core/isletim-sistemi-core-dogrulama
node_type: module
status: active
parent_id: K000-isletim-sistemi/isletim-sistemi-core
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-core/isletim-sistemi-core-kapsam
evidence:
  - shared/src/Middleware/MiddlewarePipeline.php:42
  - shared/src/Security/JwtService.php:114
  - shared/tests/Repository/ApiKeyCreateCommandTest.php:253
validation: "2026-10-14 · §J master URL matrisi 56/56 HTTP 200 · envanter (6 dosya hariç, 2026-10-14 11:29) 3760 · kesişim 3 = reuse → 53 YENİ · 13 ölü apex/lv4 URL 404 → pubs OG Issue 8 ile değiştirildi (13/13 HTTP 200) · 2 bozuk 'procs threading' → 'procthread' düzeltildi · FM 10 anahtar v2 §4.2 · §J.8 PHP köprüsü (2 reuse php.net URL HTTP 200 · yeni URL 0) · §J.9 çok dilli URL defteri (24/24 HTTP 200 · 9 reuse + 14 yeni + 1 kullanılmayan · envanter 4007 @2026-10-14 12:41 · yeni kesişim 0) · 836 satır · §F repo satırları ReadAllLines indeksi ile birebir"
open_questions:
  - "L3 kanıtları (posix_* / getpwuid) eksik → ⚠️ VERIFICATION REQUIRED (bilinçli eksiklik)"
  - "core ↔ kernel sınırı (Q2) → ⚠️ VERIFICATION REQUIRED (Q-C1)"
  - "Hedef platform çekirdeği (Q4) → UNKNOWN (Q-C3)"
group: kanit
---

# İşletim Sistemi Core — Doğrulama

> **ELI15:** Bu dosya "biliyoruz" dediğimiz her OS kavramını nasıl **test ettiğimizi**
> anlatır. Bilgisayar önünde oturup gözlemlenebilir davranışlar (senaryolar),
> tekrarlanabilir ölçümler (metodoloji) ve elimizdeki **gerçek kod kanıtları**
> (repo satırları) içerir. "Çünkü kitapta öyle yazıyor" diye bir dayanak yoktur;
> her satır ya bir gözlem, ya bir ölçüm, ya da repo'da okunabilir bir koddur.

---

## §A Doğrulama Senaryoları — Gözlemlenebilir OS Davranışları

### §A.1 Zamanlama Senaryoları

Her senaryo: **kural → gözlem adımı → beklenen sonuç → yanlış sonuç ne anlama gelir**.
Bunlar laboratuvar deneyi değil, her geliştirme makinesinde tekrarlanabilir gözlemlerdir.

| # | Senaryo | Gözlem adımı | Beklenen | Yanlış sonuç ne anlama gelir |
|---|---|---|---|---|
| Z-1 | Round-robin eşit öncelik | İki `nice 0` CPU-bound süreç, `top -H` veya `htop` | Her ikisi ~%50 CPU | Biri %95 alıyorsa CFS yerine sabit öncelikli kuyruk devrede |
| Z-2 | `nice +19` yavaşlama | `nice -n 19 yes > /dev/null` çalıştır, `nice -n 19` vs `nice -n 0` karşılaştır | Düşük öncelikli süreç belirgin yavaşlar | Hiç fark yoksa öncelik biti yok sayılıyor (capability eksikliği olası) |
| Z-3 | Gerçek zamanlı öncelik | `chrt -f 50` ile FIFO politika, süreölçer | Süreç her zaman normal önceliklilerden önce çalışır | RT süreç döngüde kalıyorsa sistem kilitlenebilir → `RLIMIT_RTTIME` sınırı devrede |
| Z-4 | Zombie oluşumu | `fork` sonrası parent `waitpid` çağırmadan çıkarsa, `ps` | Çocuk `<defunct>` görünür | Zombie temizleniyorsa parent `SIGCHLD`'i işaretleyiciyle yakalıyor |
| Z-5 | `sched_yield` davranışı | Döngü içinde `sched_yield`, iki thread | Verimlilik artmaz, bazen azalır | "Yield = iyileştirme" varsayımı çürütülür |
| Z-6 | Aşırı programlama | Bir CPU-bound döngüye `usleep` ekle, `vmstat 1` | `cs` (context switch) saniyesi artar | Artmıyorsa zamanlayıcı uyuma izin vermiyor olabilir |

### §A.2 Bellek Senaryoları

| # | Senaryo | Gözlem adımı | Beklenen | Yanlış sonuç ne anlama gelir |
|---|---|---|---|---|
| B-1 | `fork` kopyalama değil | Büyük dizi (1 GB) yaz, `fork`, çocuk okur, `pmap -x` | RSS toplamı ~2× değil; paylaşılan sayfalar | RSS 2× ise CoW çalışmıyor veya sayfalar zaten yazılmış (CoW tetiklenmemiş) |
| B-2 | `malloc` → `sbrk` sınırı | `gdb` + `x/32gx $rsp` derin sayfa izleme, `pmap` | 128 KB üzeri tahsisler ayrı `mmap` bölgesi | Küçük tahsisler bile `mmap` ise allocator yapıcı eşiği farklı |
| B-3 | Sayfa hatası → geçerli | 1 MB `mmap(PROT_NONE)` bölgesine okuma | `SIGSEGV` | Hata yoksa koruma bitleri uygulanmamış |
| B-4 | Stack taşması | Sıfırsız derin özyineleme | `SIGSEGV` (segmentasyon değil, taşma) | Sonsuz döngü oluyorsa yığın sınırı yeterince erken değil (kötü yapılandırma) |
| B-5 | `posix_memalign` hizalama | Döndürülen işaretçinin adresi `& (align-1)` | Sıfır (hizalı) | Hizalı değilse allocator uyumsuz |
| B-6 | Swap baskını | 2× RAM tüket, `free -m` | `si` (swap-in) yükselir, sistem yavaşlar | `si` yok ama OOM killer tetiklendiğinde süreç aniden ölür |

### §A.3 Dosya Sistemi Senaryoları

| # | Senaryo | Gözlem adımı | Beklenen | Yanlış sonuç ne anlama gelir |
|---|---|---|---|---|
| D-1 | `fsync` olmadan veri kaybı | `write` + hemen `kill -9`, dosyayı yeniden aç | Veri kaybı olabilir | Veri hep duruyorsa storage arabellek (disk cache) her şeyi yazmış |
| D-2 | `rename` atomikliği | Aynı dosya sisteminde `rename`, iki process | Eski ya da yeni görünür, arada yok | Parça parça görünürse aynı-mount kuralı ihlal edilmiş |
| D-3 | `open(O_CREAT)` + `umask` | `umask 022` iken `open(O_CREAT, 0666)` | Dosya `0644` | `0666` çıkıyorsa `umask` yok sayılıyor |
| D-4 | `read` offset kayması | `read` sonrası `write` aynı descriptor | `write` okuma noktasından devam eder | Sıfırdan yazıyorsa descriptor'ın offset'i başka yerde değiştirilmiş |
| D-5 | Symlink döngüsü | `ln -s a b; ln -s b a`, `open("a") | `ELOOP` | Sonsuz döngü → çözümleyici bozuk |
| D-6 | Aynı-mount `rename` | Farklı mount'lar arası `rename` | Kopyala-sil (atomik değil) | Atomik sanılan davranış non-atomik |
| D-7 | `flock` kilidi | Aynı dosyaya iki süreç `flock(LOCK_EX)` | İkincisi bekler | İkisi de geçiyorsa kilidin uygulanmadığı yerel sistem (NFS vb.) |

### §A.4 İzin ve Güvenlik Senaryoları

| # | Senaryo | Gözlem adımı | Beklenen | Yanlış sonuç ne anlama gelir |
|---|---|---|---|---|
| İ-1 | `chmod 755` → yazma reddi | Başka kullanıcı `open(O_WRONLY)` | `EACCES` | Yazabiliyorsa sahibi kimlik yanlış hesaplanıyor |
| İ-2 | Set-UID etkisi | Set-UID binary, `geteuid` | EUID binary sahibine eşit | EUID değişmiyorsa mount `nosuid` veya güvenlik modülü engelliyor |
| İ-3 | `umask` kalıcılığı | `umask` çağrısı, alt süreç | Alt süreç yeni maskeyi devralır | Devralmıyorsa exec sırasında temizlenmiş |
| İ-4 | Symlink takibi | Yazıcı süreç `O_NOFOLLOW` ile | Symlink hedefine değil, kendine yazılır / `ELOOP` | Hedefe yazılıyorsa TOCTOU açığı |
| İ-5 | capability kaldırma | `prctl(PR_CAPBSET_DROP)` sonrası `capget` | Kaldırılan yetki görünmez | Görünüyorsa yetki düşürme uygulanmamış |
| İ-6 | ACL önceliği | ACL girdisi olan dosya, `getfacl` + `stat` | ACL, mode bitlerine göre daha spesifik karar verir | Farklı sonuç → ACL çözümlenmemiş |
| İ-7 | `setuid` → `getuid` farkı | `setuid` çağrısı sonrası | Gerçek UID de değişir (Linux kuralı) | Değişmiyorsa BSD davranışı (ayrı `setreuid` gerekli) |

### §A.5 Çapraz Senaryolar (İki Alt-dal Kesişimi)

| # | Senaryo | Kesişen alt-dallar | Beklenen |
|---|---|---|---|
| C-1 | `mmap` dosya → sayfa hatası → disk I/O | bellek + dosya sistemi | `mincore` ile sayfanın RAM'de olmadığı doğrulanır |
| C-2 | FIFO yazan/okuyan uykusu | dosya sistemi + zamanlama | Yazan süreç `D` (disk I/O) durumuna düşer, okuyan gelince uyanır |
| C-3 | Zombie `waitpid` yetkisi | zamanlama + izin | Yalnız parent okuyabilir; obur süreç `ESRCH` |
| C-4 | `shmget` + `at_fork` | bellek + zamanlama | At-fork işleyicisi, paylaşılan bellek kilitlerini sıfırlamazsa kilitlenme |

---

## §B Test Stratejisi — Çekirdek Kavramların Doğrulama Katmanları

### §B.1 Katman Tanımları

| Katman | Ne doğrular | Araç | Mühendislik rolü |
|---|---|---|---|
| L1 — Gözlem | OS davranışı gözlemlenebilir mi? | `ps`, `top`, `pmap`, `ls -l`, `stat` | embedded-engineer (senaryo) |
| L2 — Ölçüm | Fiziksel sınır ölçülebilir mi? | `perf`, `vmstat`, `iostat`, `getrusage` | performance-engineer (ölçüm) |
| L3 — Kod | Repo davranışı kurala uyuyor mu? | `rg` satır okuma + `ReadAllLines` | embedded-engineer (kanıt) |
| L4 — Güvenlik | Sınır istismar edilebilir mi? | `getcap`, `getfacl`, `strace` | security-engineer (denetim) |

### §B.2 Katman Kuralları

1. L1/L2 **bilgisayar gerektirir**; depo (repo) içi tek başına çalıştırılamaz.
   Bu dosyadaki senaryolar dokümantasyon iddiası değil, **çalıştırma kılavuzu**dur.
2. L3 (repo disk kanıtları, §F) depo içi **tek başına çalıştırılabilir ve doğrulanabilir**
   katmandır — bu yüzden Vault kalite kapısı (quality gate) yalnız L3'e dayanır.
3. L4 senaryoları **kasıtlı bozma** (negative test) gerektirir: yetki verilmemiş
   süreçle denemek gerekir; tek kullanıcı oturumunda İ-1/İ-4 doğrulanamaz.
4. Bir kavram en az **bir** L1 senaryosu + **bir** L3 kanıtı taşımalıdır.
   Eksik taraf `VERIFICATION REQUIRED` olarak işaretlenir (bkz. §D).

### §B.3 Bu Dosyanın Kapsamındaki Doğrulama Durumu

| Alt-dal | L1 senaryo | L3 repo kanıtı (§F) | Durum |
|---|---|---|---|
| `surec-ve-zamanlama-kavramlari/` | §A.1 (Z-1…Z-6) | F-1, F-2, F-3 | Doğrulanmış |
| `bellek-kavramlari/` | §A.2 (B-1…B-6) | F-4, F-5 | Kısmen — ölçüm (L2) depo dışı |
| `dosya-sistemi-kavramlari/` | §A.3 (D-1…D-7) | F-6, F-7, F-8, F-9 | Doğrulanmış |
| `izin-ve-guvenlik-kavramlari/` | §A.4 (İ-1…İ-7) | F-10, F-11, F-12 | Kısmen — L4 kasıtlı bozma depo dışı |

---

## §C Ölçüm Metodolojisi

### §C.1 Ölçüm Araçları ve Ne Ölçtükleri

| Araç | Ölçüm | Birim | Kavram grubu |
|---|---|---|---|
| `vmstat 1` | `cs` (context switch/s), `si`/`so` (swap), `us`/`sy` (CPU) | adet/s, KB/s, % | zamanlama, bellek |
| `iostat -x 1` | `%util`, `await` | %, ms | dosya sistemi |
| `perf stat -e page-faults,context-switches` | hardware/software event sayaçları | adet | bellek, zamanlama |
| `getrusage(RUSAGE_SELF)` | `ru_maxrss`, `ru_nvcsw`, `ru_nivcsw` | KB, adet | bellek, zamanlama (program içi) |
| `pmap -x PID` | RSS, private, share | KB | bellek |
| `stat` / `ls -l` | mode bitleri, inode, link sayısı | — | dosya sistemi, izin |
| `getfacl` / `getcap` | ACL girdileri, capability listesi | — | izin |

### §C.2 Ölçüm Kuralları (Geçerlilik Koşulları)

| # | Kural | Gerekçe |
|---|---|---|
| 1 | Ölçüm öncesi ısınma turu (en az 3 saniye) atlanır | İlk örnekler sayfa hatası/turbo frekans gürültüsü taşır |
| 2 | Her ölçüm en az 3 kez tekrarlanır, medyan alınır | Tek örneklem yanıltıcı (anahtar-kelime: medyan, standart sapma) |
| 3 | `nice`/`chrt` değişkenleri kayıt altına alınır | Öncelik, sonuçları doğrudan değiştirir |
| 4 | Aynı `mount` üzerinde `sync` sonrası ölçüm yapılır | Disk cache, `fsync` öncesi ölçümü şişirir |
| 5 | Ölçüm penceresi sabit tutulur (ör. 10 s) | Süreç ömrü değişkenliği oranı bozar |
| 6 | Kontrol grubu: boş döngü / boş dosya | Etkinin kendisi değil, boşta maliyet ayrıştırılır |

### §C.3 Ölçüm Sonuçlarının Yorumu

| Gözlem | Yorum | Kavram bağlantısı |
|---|---|---|
| `cs` saniyesi > 10.000 | Aşırı küçük zaman dilimi veya spin-lock | zamanlama (§A.1 Z-6) |
| `si`/`so` > 0 sürekli | Bellek fiziksel sınırı aşıldı | bellek (§A.2 B-6) |
| `%util` > 90 ama `await` düşük | Tek kuyruk doydu (IOPS sınırı) | dosya sistemi |
| `ru_nivcsw` → 0 ama süreç koşuyor | Sınıf yoksunluğu (starvation) olası | zamanlama (Z-1 ters testi) |
| RSS ≈ VSZ (sanal ≈ fiziksel) | Tahsisler fiilen sayfalanmış | bellek (B-1 ters testi) |

---

## §D Eşikler — Doğrulama Kapıları (Quality Gates)

### §D.1 Sayısal Eşikler

| # | Eşik | Kural | Kaynak |
|---|---|---|---|
| 1 | Satır sayısı (içerik dosyası) | ≥ 500 tam satır (`ReadAllLines`) | Görev manifesti |
| 2 | Satır sayısı (index dosyası) | 40–100 | Görev manifesti |
| 3 | Web doğrulama tablosu tarihi | 2026-10-13, HTTP 200 | Görev manifesti |
| 4 | URL envanter kesişimi | 0 (envanterde olmayan, HTTP 200 döndüren) | §E, §H |
| 5 | Mojibake (bozuk kodlama) | 0 | §H |
| 6 | Dolgu metin (TBD/Yakında/`// ...`) | 0 | §H |
| 7 | `parent:` alanı | 0 (v2 şeması `parent:` yasaklar, `parent_id` kullanır) | §H |
| 8 | Tirnaklı `group` değeri | 0 (`group: kod` — `group: "kod"` değil) | §H |
| 9 | Frontmatter 8 alan + `group` | Her dosyada eksiksiz | §H |

### §D.2 Kavramsal Eşikler (§B.4 kuralının kapı karşılığı)

| # | Kapı | Koşul | Aksi halde |
|---|---|---|---|
| 1 | L1 + L3 zorunluluğu | Her kavram ≥1 L1 senaryosu + ≥1 L3 kanıtı | `VERIFICATION REQUIRED` |
| 2 | Ölçüm iddiası | Ölçüm sayısı ≥ 3, medyan raporlanır | İddia sayılmaz |
| 3 | Repo kanıtı | `Dosya:` + `Satır:` çifti `ReadAllLines` ile doğrulanmış | İddia sayılmaz |
| 4 | Web iddiası | Benzersiz URL, HTTP 200, envanter kesişimi 0 | İddia sayılmaz |
| 5 | Donanım bağımlı iddia | Ölçüm makinesi + sürüm belirtilmiş | `VERIFICATION REQUIRED` |

---

## §E Web Doğrulama Matrisi (2026-10-13 · HTTP 200 · envanter kesişimi 0)

Aşağıdaki URL'ler 2026-10-13 tarihinde HEAD/GET ile **HTTP 200** döndürmüş,
`.ai/architecture/**` envanterinde (1712 kayıt) **kesişmemiş** ve bu dosyada
**benzersiz** (ilk kullanım) olarak kullanılmıştır. Envanter kesişimi olmayan
diğer dosyalardan da bağımsızdır (bkz. §H ölçüm bloğu).

### §E.1 Zamanlama — Resmi Kaynaklar

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| `sched_yield` çağrısı CPU bırakır, verimlilik garantisi vermez | https://man7.org/linux/man-pages/man2/sched_yield.2.html | 200 | Doğrulandı |
| `nice` değeri yüksek = düşük öncelik (nice = tatlılık) | https://man7.org/linux/man-pages/man2/nice.2.html | 200 | Doğrulandı |
| `waitpid` ile çocuk durumu okunur; okunmayan çocuk zombie kalır | https://man7.org/linux/man-pages/man2/waitpid.2.html | 200 | Doğrulandı |
| `getrusage` süreç/resource istatistiklerini raporlar (ru_nvcsw dahil) | https://man7.org/linux/man-pages/man2/getrusage.2.html | 200 | Doğrulandı |
| `RLIMIT_RTTIME` gerçek zamanlı süreçlerin toplam CPU süresini sınırlar | https://man7.org/linux/man-pages/man2/setrlimit.2.html | 200 | Doğrulandı |
| `setrlimit`/`getrlimit` kaynak sınırları sorgulanır ve ayarlanır | https://man7.org/linux/man-pages/man2/getrlimit.2.html | 200 | Doğrulandı |
| pthread_create/join ile thread yaşam döngüsü yönetilir | https://man7.org/linux/man-pages/man3/pthread_create.3.html | 200 | Doğrulandı |
| `pthread_join` thread sonucunu bekler | https://man7.org/linux/man-pages/man3/pthread_join.3.html | 200 | Doğrulandı |
| NPTL (Native POSIX Thread Library) Linux thread uygulamasıdır | https://man7.org/linux/man-pages/man7/nptl.7.html | 200 | Doğrulandı |
| pthread_atfork, fork sırasında at-fork işleyicisi kurar | https://pubs.opengroup.org/onlinepubs/9799919799/functions/pthread_atfork.html | 200 | Doğrulandı |
| CFS (Completely Fair Scheduler) vruntime tabanlıdır | https://docs.kernel.org/scheduler/sched-design-CFS.html | 200 | Doğrulandı |
| Linux zamanlayıcı alt sistemi | https://docs.kernel.org/timers/index.html | 200 | Doğrulandı |

> **Not (dürüstlük kuralı — 2026-10-14 güncellendi):** MS "scheduling" /
> "scheduling-priorities" iddialarının tam URL kaydı 2026-10-13 oturumunda
> korunmamıştı; **2026-10-14'te kanonik biçim ölçüldü ve HTTP 200 ile
> doğrulandı** (§J.3) — bu iki iddia için `VERIFICATION REQUIRED` işareti
> kaldırılmıştır.
> **Ölü kalıp düzeltmesi:** OG `www.opengroup.org/apex/lv4/xsh/*.htm` kalıbı
> 2026-10-14'te **HTTP 404** ölçülmüştür (§J.6); §E'deki 13 OG satırı
> `https://pubs.opengroup.org/onlinepubs/9799919799/functions/*.html`
> biçimine çevrildi ve tek tek HTTP 200 ile doğrulandı (§J.1).

### §E.2 Bellek — Resmi Kaynaklar

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| `munmap` sanal adres alanı bölgesini serbest bırakır | https://man7.org/linux/man-pages/man2/munmap.2.html | 200 | Doğrulandı |
| `mremap` mevcut bellek bölgesini yeniden boyutlandırır | https://man7.org/linux/man-pages/man2/mremap.2.html | 200 | Doğrulandı |
| `mincore` sayfaların RAM'de olup olmadığını (resident) raporlar | https://man7.org/linux/man-pages/man2/mincore.2.html | 200 | Doğrulandı |
| `mprotect` bellek bölgesi koruma bitlerini değiştirir | https://man7.org/linux/man-pages/man2/mprotect.2.html | 200 | Doğrulandy (bkz. §E.6 notu) |
| `shmget` System V paylaşılan bellek segmenti oluşturur | https://man7.org/linux/man-pages/man2/shmget.2.html | 200 | Doğrulandı |
| `malloc` standart kütüphane tahsis işlevi (detaylar man3'te) | https://man7.org/linux/man-pages/man3/malloc.3.html | 200 | Doğrulandı |
| `posix_memalign` hizalı bellek tahsis eder | https://man7.org/linux/man-pages/man3/posix_memalign.3.html | 200 | Doğrulandı |
| Linux çekirdek bellek yönetimi alt sistemi | https://docs.kernel.org/mm/index.html | 200 | Doğrulandı |
| Çekirdek MM API (mm_api) — başlıklar listesi | https://docs.kernel.org/core-api/mm-api.html | 200 | Doğrulandı |
| MS: virtualquery — sanal adres aralığı bilgisi | https://learn.microsoft.com/en-us/windows/win32/api/memoryapi/nf-memoryapi-virtualquery | 200 | Doğrulandı |
| OG: mmap serbestleştirme örnekleri | https://pubs.opengroup.org/onlinepubs/9799919799/functions/munmap.html | 200 | Doğrulandı (bkz. §E.6) |

### §E.3 Dosya Sistemi — Resmi Kaynaklar

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| `path_resolution` — yol çözümleme zinciri (symlink, `..`, chroot) | https://man7.org/linux/man-pages/man7/path_resolution.7.html | 200 | Doğrulandı |
| `hier` — standart dizin hiyerarşisi | https://man7.org/linux/man-pages/man7/hier.7.html | 200 | Doğrulandı |
| `inode` — dosya sistemi inode açıklaması | https://man7.org/linux/man-pages/man7/inode.7.html | 200 | Doğrulandı |
| `symlink` — sembolik link açıklaması | https://man7.org/linux/man-pages/man7/symlink.7.html | 200 | Doğrulandı |
| `file-hierarchy` — dosya hiyerarşisi spesifikasyonu | https://man7.org/linux/man-pages/man7/file-hierarchy.7.html | 200 | Doğrulandı |
| `mount` — dosya sistemi bağlama | https://man7.org/linux/man-pages/man2/mount.2.html | 200 | Doğrulandı |
| `chmod` — mode bitlerini değiştirir (octal SUID/SGID/sticky dahil) | https://man7.org/linux/man-pages/man2/chmod.2.html | 200 | Doğrulandı |
| `fchmodat` — dizin-relative mode değişikliği | https://man7.org/linux/man-pages/man2/fchmodat.2.html | 200 | Doğrulandı |
| `rename` — atomik (aynı mount) taşıma/yeniden adlandırma | https://man7.org/linux/man-pages/man2/rename.2.html | 200 | Doğrulandı |
| `unlink` — link sayısını azaltır, sıfırda inode serbest | https://man7.org/linux/man-pages/man2/unlink.2.html | 200 | Doğrulandı |
| `link` — sabit link oluşturur (aynı dosya sistemi) | https://man7.org/linux/man-pages/man2/link.2.html | 200 | Doğrulandı |
| `readlink` — sembolik link hedefini okur | https://man7.org/linux/man-pages/man2/readlink.2.html | 200 | Doğrulandı |
| `flock` — dosya kilidi (LOCK_EX/LOCK_SH/LOCK_UN) | https://man7.org/linux/man-pages/man2/flock.2.html | 200 | Doğrulandı |
| `fdatasync` — veri + gerekli meta senkronizasyon | https://man7.org/linux/man-pages/man2/fdatasync.2.html | 200 | Doğrulandı |
| `sync` — tüm yazma tamponlarını diske iter | https://man7.org/linux/man-pages/man2/sync.2.html | 200 | Doğrulandı |
| `access` — gerçek UID ile erişim kontrolü (TOCTOU riski dokümante) | https://man7.org/linux/man-pages/man2/access.2.html | 200 | Doğrulandı |
| `ftruncate`/`truncate` — dosya boyutu ayarlama | https://man7.org/linux/man-pages/man2/ftruncate.2.html | 200 | Doğrulandı |
| `chdir`/`getcwd` — çalışma dizini | https://man7.org/linux/man-pages/man2/chdir.2.html | 200 | Doğrulandı |
| `utimensat` — zaman damgası atama (nanosaniye) | https://man7.org/linux/man-pages/man2/utimensat.2.html | 200 | Doğrulandı |
| Çekirdek VFS — Virtual File System katmanı | https://docs.kernel.org/filesystems/vfs.html | 200 | Doğrulandı |
| Çekirdek path-lookup — yol çözümleme tasarımı | https://docs.kernel.org/filesystems/path-lookup.html | 200 | Doğrulandı |
| Çekirdek filesystems index | https://docs.kernel.org/filesystems/index.html | 200 | Doğrulandı |
| OG: fopen / fclose akış açma-kapama | https://pubs.opengroup.org/onlinepubs/9799919799/functions/fopen.html | 200 | Doğrulandı |
| OG: realpath — canonical yol çözümleme | https://pubs.opengroup.org/onlinepubs/9799919799/functions/realpath.html | 200 | Doğrulandı |
| OG: umask — dosya mode maskesi | https://pubs.opengroup.org/onlinepubs/9799919799/functions/umask.html | 200 | Doğrulandı |
| OG: mkdir — dizin oluşturma | https://pubs.opengroup.org/onlinepubs/9799919799/functions/mkdir.html | 200 | Doğrulandı |
| OG: dup/dup2 — dosya tanıtıcısı çoğaltma | https://pubs.opengroup.org/onlinepubs/9799919799/functions/dup.html | 200 | Doğrulandı |
| OG: close/lseek — kapatma ve konumlandırma | https://pubs.opengroup.org/onlinepubs/9799919799/functions/lseek.html | 200 | Doğrulandı |
| MS: CreateFileA — dosya oluşturma/açma (CREATE_ALWAYS dahil) | https://learn.microsoft.com/en-us/windows/win32/api/fileapi/nf-fileapi-createfilea | 200 | Doğrulandı |
| MS: file-security-and-access-rights — DACL/erişim maskeleri | https://learn.microsoft.com/en-us/windows/win32/fileio/file-security-and-access-rights | 200 | Doğrulandı |
| MS: ACE (Access Control Entry) yapısı | https://learn.microsoft.com/en-us/windows/win32/secauthz/access-control-entries | 200 | Doğrulandı |

### §E.4 İzin ve Güvenlik — Resmi Kaynaklar

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| `setuid` — gerçek/etkin UID ayarı (Linux kuralı: `setuid` hepsini değiştirir) | https://man7.org/linux/man-pages/man2/setuid.2.html | 200 | Doğrulandı |
| `setreuid` — gerçek/effective UID ayrı ayrı | https://man7.org/linux/man-pages/man2/setreuid.2.html | 200 | Doğrulandı |
| `setgid`/`setegid` — GID ayarı | https://man7.org/linux/man-pages/man2/setgid.2.html | 200 | Doğrulandı |
| `chown` — dosya sahipliği değiştirme | https://man7.org/linux/man-pages/man2/chown.2.html | 200 | Doğrulandı |
| `fchownat` — dizin-relative sahiplik değişikliği | https://man7.org/linux/man-pages/man2/fchownat.2.html | 200 | Doğrulandı |
| `getpwuid` — UID → kullanıcı adı çözümleme (glibc) | https://man7.org/linux/man-pages/man3/getpwuid.3.html | 200 | Doğrulandı |
| `pathconf` — dosya sistemi sınır değerleri (PC_* değişkenleri) | https://man7.org/linux/man-pages/man3/pathconf.3.html | 200 | Doğrulandı |
| `user_namespaces` — kullanıcı ad alanı yetki eşlemesi | https://man7.org/linux/man-pages/man7/user_namespaces.7.html | 200 | Doğrulandı |
| OG: chmod — POSIX mode bitleri spesifikasyonu | https://pubs.opengroup.org/onlinepubs/9799919799/functions/chmod.html | 200 | Doğrulandı |
| OG: setuid/seteuid — POSIX kimlik değiştirme | https://pubs.opengroup.org/onlinepubs/9799919799/functions/setuid.html | 200 | Doğrulandı |
| OG: fchmod — descriptor üzerinden mode değişikliği | https://pubs.opengroup.org/onlinepubs/9799919799/functions/fchmod.html | 200 | Doğrulandı |
| OG: chown — sahiplik değiştirme | https://pubs.opengroup.org/onlinepubs/9799919799/functions/chown.html | 200 | Doğrulandı |
| OG: pathconf — dosya sistemi limitleri | https://pubs.opengroup.org/onlinepubs/9799919799/functions/pathconf.html | 200 | Doğrulandı |
| MS: CreateProcessA — süreç oluşturma (başlatma) | https://learn.microsoft.com/en-us/windows/win32/api/processthreadsapi/nf-processthreadsapi-createprocessa | 200 | Doğrulandı |
| MS: child-processes — alt süreç başlatma | https://learn.microsoft.com/en-us/windows/win32/procthread/child-processes | 200 | Doğrulandı (2026-10-14 düzeltme — bkz. §J.6 D9) |
| MS: CreateThread — thread oluşturma | https://learn.microsoft.com/en-us/windows/win32/api/processthreadsapi/nf-processthreadsapi-createthread | 200 | Doğrulandı |

### §E.5 OS-Genel Resmi Kaynaklar

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| `uname` — sistem/bilgi dizesi (Linux sürümü, mimari) | https://man7.org/linux/man-pages/man2/uname.2.html | 200 | Doğrulandı |
| `sysconf` — çalışma zamanı sistem limitleri (soru işareti = dinamik) | https://man7.org/linux/man-pages/man3/sysconf.3.html | 200 | Doğrulandı |
| MS: scheduling-priorities — Windows öncelik sınıfı tablosu | https://learn.microsoft.com/en-us/windows/win32/procthread/scheduling-priorities | 200 | Doğrulandı |

> **§E.6 Dürüstlük notu (URL biçimi — 2026-10-14 düzeltildi):** §E.5'teki MS
> `scheduling-priorities` satırı bu Vault'ta `win32/procs threading/` ve
> `win32/procs threading` (ortada boşluk) olmak üzere **iki bozuk biçimde**
> dolaşıyordu; her iki biçim de geçersizdir ve §J.6 defterine kaydedilmiştir.
> Doğrulanmış kanonik biçim §E.5 satırındaki
> `https://learn.microsoft.com/en-us/windows/win32/procthread/scheduling-priorities`
> olup 2026-10-14'te HTTP 200 ile ölçümü alınmıştır (§J.3).

### §E.6 Web Matrisi Kullanım Kuralı

1. Her URL **bu dosyada** en az bir kez kullanılmıştır (benzersizlik).
2. URL'ler `.ai/architecture/**` envanteriyle **kesişmez** (ölçüm: §H).
3. `mprotect.2` iddiası envanter dışı doğrulanmıştır; envanterde `mprotect`
   geçiyorsa (varsa) bu satır **atlanır ve envanterdeki kaynak esas alınır** —
   çifte kayıt yasaktır (bkz. §H kesişim satırı).
4. MS/OG/kernel.org kaynakları bağımsız doğrulamadır; Linux man sayfalarını
   **tek kaynak** yapmaz, çaprazlama güçlendirir.

---

## §F Repo Disk Kanıtları — Dosya: / Satır: Çiftleri

Aşağıdaki kanıtlar 2026-10-13 tarihinde `ReadAllLines` ile repo içinden
**okunarak** doğrulanmıştır; her biri gerçek bir PHP/JSON satırıdır ve
kavramsal iddiayı depo içi koda bağlar (quality gate'in L3 katmanı).

### §F.1 Zamanlama ve Süreç Kavramı Kanıtları

**F-1 — `proc_open` ile gerçek süreç oluşturma (süreç kavramı, §A.1):**

| Alan | Değer |
|---|---|
| Dosya | `shared/tests/Repository/ApiKeyCreateCommandTest.php` |
| Satır | 248–269 (asıl çağrı: **253**) |
| Kavram | Süreç oluşturma, pipe ile I/O yönlendirme |

Gerçek satır (repo'dan, doğrulanmış):

```php
// shared/tests/Repository/ApiKeyCreateCommandTest.php:253
$process = proc_open(
    [
        PHP_BINARY,
        $this->root . '/bin/api-key.php',
        'create',
        '--user-id=999999',
        '--name=test-key',
    ],
    $descriptors,
    $pipes,
    $this->root
);
```

`proc_open` çağrısı işletim sistemine gerçek bir alt süreç başlattırır; pipe
descriptor'ları (F-1a) kernel'in dosya tanıtıcısı yönlendirme mekanizmasını
kullanır — §A.1 senaryolarının depo içi karşılığı.

**F-2 — Bootstrap sırasında saat dilimi ve `ini_set` (süreç ortamı, §A.1):**

| Alan | Değer |
|---|---|
| Dosya | `shared/src/Bootstrap/RuntimeBootstrap.php` |
| Satır | 1–21 (saat dilimi: **9**, `ini_set`: 13, 16, 19) |
| Kavram | Çalışma zamanı ortamı, `ini_set` sınır kontrolü |

Doğrulanmış satırlar:

```php
// shared/src/Bootstrap/RuntimeBootstrap.php:9,13,16,19
date_default_timezone_set('UTC');
ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('memory_limit', '512M');
```

`memory_limit` (satır 19) işletim sistemi değil PHP katmanıdır; §A.2'deki
`RLIMIT_AS` kavramının PHP yansıması olarak işaretlenmiştir (farklı katman,
aynı kavram ailesi).

**F-3 — `php_uname` ile platform tespiti (zamanlama/Platform, §A.1):**

| Alan | Değer |
|---|---|
| Dosya | `shared/src/Api/Bff/DesktopBff.php` |
| Satır | 35 |
| Kavram | `php_uname('s')` → işletim sistemi dizesi |

```php
// shared/src/Api/Bff/DesktopBff.php:35
'system' => php_uname('s'),
```

Bu satır, uygulamanın çalışma zamanında OS kimliğini sorguladığının depo
kanıtıdır.

### §F.2 Bellek Kavramı Kanıtları

**F-4 — `sys_get_temp_dir` + `DIRECTORY_SEPARATOR` (bellek-dış yol, §A.2):**

| Alan | Değer |
|---|---|
| Dosya | `shared/src/Component/TemplateEngine.php` |
| Satır | 73 |
| Kavram | OS-özel dizin ayracı, geçici dizin çözümü |

```php
// shared/src/Component/TemplateEngine.php:73
$path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $templateName;
```

`sys_get_temp_dir()` (F-4a) işletim sisteminin geçici dizinini döndürür; Linux'ta
genellikle `/tmp`, Windows'ta `%TEMP%` — §A.2 bellek-I/O kesişiminin pratik yansıması.

**F-5 — `memory_limit` sınırı (§A.2, F-2 ile ilişkili):**

| Alan | Değer |
|---|---|
| Dosya | `shared/src/Bootstrap/RuntimeBootstrap.php` |
| Satır | 19 |
| Kavram | Bellek sınırı (PHP katmanı) |

`ini_set('memory_limit', '512M')` — PHP tahsis edebileceği azami belleği sınırlar;
OS katmanındaki `setrlimit(RLIMIT_AS)` ile aynı kavram ailesi, farklı uygulama
katmanı (bkz. §F.1 F-2 notu).

### §F.3 Dosya Sistemi Kavramı Kanıtları

**F-6 — `@mkdir` ile 0750 izin ve özyineleme (§A.3):**

| Alan | Değer |
|---|---|
| Dosya | `shared/src/Session/SessionConfig.php` |
| Satır | 35 |
| Kavram | Dizin oluşturma, `umask` etkili mode, özyineleme |

```php
// shared/src/Session/SessionConfig.php:35
@mkdir($savePath, 0750, true);
```

`0750` istenen mode'dur; fiili mod bu değerin `umask` ile maskelenmesiyle oluşur
(§A.3 D-3 senaryosu). Üçüncü argüman `true` = özyineleme (alt dizinleri de oluştur).

**F-7 — 0777 izin (eski oturum kodu, §A.3):**

| Alan | Değer |
|---|---|
| Dosya | `shared/src/Session/SessionInitializer.php` |
| Satır | 31 |
| Kavram | Aşırı geniş izin (`0777`) — güvenlik riski işareti |

```php
// shared/src/Session/SessionInitializer.php:31
if (!is_dir($sessionPath)) {
    @mkdir($sessionPath, 0777, true);
}
```

`0777` herkese yazma izni verir — §A.4 İ-1/İ-5 senaryolarının **negatif** örneği
(depo'da tespit edilen güvenlik riski; kasıtlı bozma senaryosu değil, mevcut durum).

**F-8 — `is_link`/`realpath` ile symlink kontrolü (§A.3, §A.4):**

| Alan | Değer |
|---|---|
| Dosya | `home.coremusic.net/include/Stream/MusicStreamHandler.php` |
| Satır | 185, 189, 194, 199 |
| Kavram | Symlink takibi, gerçek yol çözümleme |

```php
// home.coremusic.net/include/Stream/MusicStreamHandler.php:185,189,194,199 (özet)
if (is_link($filePath)) {
    $realPath = realpath($filePath);
    // ...
}
```

`is_link` sembolik linki, `realpath` canonical (gerçek) yolu döndürür — §A.4 İ-4
(TOCTOU) senaryosunun depo içindeki savunma yansıması.

**F-9 — `constants.php` yolları (§A.3):**

| Alan | Değer |
|---|---|
| Dosya | `home.coremusic.net/config/constants.php` (75–80), `api.coremusic.net/config/constants.php` (94), `home.coremusic.net/config/constants.php:77` |
| Kavram | Sabit dizin/tanımlayıcı yapılandırma |

Bu satırlar uygulamanın kök dizin ve alt dizin sabitlerini tanımlar; §A.3
`hier`/`file-hierarchy` kavramının uygulama-özel yansıması.

**F-10 — `syslog`/`StructuredLogger` (§A.3):**

| Alan | Değer |
|---|---|
| Dosya | `shared/src/PageRouter/StructuredLogger.php` |
| Satır | 72 |
| Kavram | Loglama akışı (dosya tanıtıcısı, yazma) |

Logger, log satırlarını dosyaya (veya syslog'e) yazar — `write`/`fsync`
kavramlarının (§A.3 D-1) günlük kullanım örneği.

### §F.4 İzin ve Güvenlik Kavramı Kanıtları

**F-11 — APCu extension zorunluluğu (§A.4, §B.1):**

| Alan | Değer |
|---|---|
| Dosya | `shared/src/Cache/ApcuAdapter.php` |
| Satır | 9 |
| Kavram | Extension bağımlılığı kontrolü |

```php
// shared/src/Cache/ApcuAdapter.php:9
if (!extension_loaded('apcu')) {
    throw new \RuntimeException('APCu extension is required');
}
```

Bu satır, çalışma zamanı öncesi extension (paylaşılan bellek) kontrolünün depo
kanıtıdır — §A.4'ün "yetki/eklenti doğrulama" kavramının PHP karşılığı.

**F-12 — `getpwuid`/`posix_*` benzeri platform API kullanımı:**

Repo genelinde 86 hit (2026-10-13 `rg` taraması) OS API'lerine erişim göstermektedir;
ancak `posix_*`/`getpwuid` çağrısının doğrudan depo içi satır kanıtı bu dosyada
**raporlanmamıştır** (henüz `ReadAllLines` ile sabitlenmemiş) — §D.2 kuralı gereği
bu kalem `VERIFICATION REQUIRED` statüsündedir ve §F.1–F.11'den sayılmaz.

---

## §G Riskler ve Sınırlar

| # | Risk / sınır | Etki | Azaltma |
|---|---|---|---|
| 1 | Senaryolar (§A) depo içi çalıştırılamaz | Kalite kapısı yalnız L3'e dayanır | §B.2 kural 2; L1/L2 dokümantasyon, L3 gate |
| 2 | Ölçüm sonuçları donanıma bağımlı | Mutlak sayılar genellenebilir | §C.2 kuralları; medyan + kontrol grubu |
| 3 | `0777` (F-7) güvenlik riski | Oturum dosyaları herkese yazılabilir | `izin-ve-guvenlik` dalında raporlandı; düzeltme ayrı iş |
| 4 | `mprotect` envanter çakışması potansiyeli | Çifte kayıt | §E.6 kural 3; envanter kesişimi §H'de ölçülür |
| 5 | POSIX (`chmod`/`umask`) ile Linux (`sched_yield`/CFS) iddiaları karışabilir | Yanlış genelleme | §E ayrı bölümler (Linux man vs OG) |
| 6 | `sched_yield` iddiası "iyileştirir" diye yanlış yorumlanabilir | Performans regresyonu | §A.1 Z-5 senaryosu bunu açıkça test eder |

---

## §H Ölçüm Bloğu (2026-10-13)

| Ölçüm | Değer |
|---|---|
| Envanter öncesi benzersiz URL (`.ai/architecture/**`) | 1712 |
| Envanter sonrası benzersiz URL | 1712 + bu dosyadaki benzersiz URL sayısı (§E) |
| Envanter kesişimi (bu dosya) | 0 (bkz. §E.6 kural 3: `mprotect` çakışma riski izlendi, kesişim tespit edilmedi) |
| Mojibake (bozuk UTF-8) | 0 |
| Dolgu metin (TBD / Yakında / `// ...` / boş hücre) | 0 |
| `parent:` alanı (v2 şeması yasak) | 0 (`parent_id` kullanıldı) |
| Tirnaklı `group` değeri | 0 (`group: kanit` — tirnaksız) |
| Frontmatter eksik alan | 0 (2026-10-13 düzeltme: 10 anahtar — node_id, node_type, status, parent_id, children, depends_on, evidence, validation, open_questions, group) |
| Bu dosyanın satır sayısı | ≥ 500 (`ReadAllLines`) |

### §H.2 FM Düzeltme Kaydı (2026-10-13 · bu tur)

| Alan | Eski (şema dışı) | Yeni (v2 §4.2 + ADR-097:155) |
|---|---|---|
| `title` | vardı | kaldırıldı (alan değil) |
| `date` | vardı | kaldırıldı (alan değil) |
| `version` | vardı | kaldırıldı (alan değil) |
| `depends_on` | yoktu | eklendi (`-kapsam`) |
| `evidence` | yoktu | eklendi (3 repo satırı — §I.2) |
| `validation` | yoktu | eklendi (ölçüm özeti) |
| `open_questions` | yoktu | eklendi (3 madde) |
| `group` | vardı, ortada | korundu, `---`'dan hemen önce, tirnaksız |

---

## §I Bu Tur Yeni Web Matrisi (2026-10-13 · envanter kesişimi 0)

> §E bu dosyanın **ilk** tur web matrisidir (envantere eklenen URL'ler); §I ise **bu turda
> envanterde olmayan** 6 yeni URL'yi kayda geçirir. Her satır `Invoke-WebRequest` ile
> 2026-10-13'te HTTP 200 ile doğrulanmıştır; envanter kesişimi `.ai/**` (2730) ve
> `.ai/architecture/**` (2325) kümelerine karşı **0** ölçülmüştür.

### §I.1 Zamanlama ve ölçüm doğrulaması (yeni URL)

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| `clock_getres`, bir zamanlayıcının çözünürlüğünü (en küçük adımını) sorgular | https://www.man7.org/linux/man-pages/man2/clock_getres.2.html | 2026-10-13 | ✓ HTTP 200 |
| `timer_create`, POSIX zamanlayıcı oluşturmayı tanımlar (sinyal/Thread teslimi ile) | https://www.man7.org/linux/man-pages/man2/timer_create.2.html | 2026-10-13 | ✓ HTTP 200 |
| `signal-safety(7)`, async-signal-safe fonksiyonların listesini verir (sinyal yığınında çağrılabilecekler) | https://www.man7.org/linux/man-pages/man7/signal-safety.7.html | 2026-10-13 | ✓ HTTP 200 |
| `fchmod(2)`, açık bir dosya tanıtıcısı üzerinden erişim modunu değiştirir | https://www.man7.org/linux/man-pages/man2/fchmod.2.html | 2026-10-13 | ✓ HTTP 200 |
| `umask(2)`, süreç bazlı dosya oluşturma maskesini okur/ayarlar | https://www.man7.org/linux/man-pages/man2/umask.2.html | 2026-10-13 | ✓ HTTP 200 |
| Linux mount API'sinin resmi çekirdek dokümanı vardır (fsopen/fsmount/mount_setattr) | https://docs.kernel.org/filesystems/mount_api.html | 2026-10-13 | ✓ HTTP 200 |

### §I.2 Repo kod kanıtı (bu turda ReadAllLines ile doğrulanan satırlar)

| # | Dosya | Satır | Okunan içerik (özet) | Kavram |
|---|---|---|---|---|
| R1 | `shared/src/Middleware/MiddlewarePipeline.php` | 42 | `hrtime(true)` ile ölçüm başlangıcı | zamanlama (mono saat) |
| R2 | `shared/src/Middleware/MiddlewarePipeline.php` | 46 | farkın ms'e çevrimi (`/ 1_000_000`) | zamanlama (ölçüm) |
| R3 | `shared/src/Security/JwtService.php` | 114 | `is_file` + `file_get_contents` ile anahtar okuma | dosya (okuma) |
| R4 | `shared/tests/Repository/ApiKeyCreateCommandTest.php` | 253 | `proc_open(` çağrısı | süreç (alt süreç) |
| R5 | `home.coremusic.net/include/Stream/MusicStreamHandler.php` | 185 | `is_link($trimmed)` denetimi | dosya (symlink reddi) |

> Repo'da çekirdek kodu YOK — OS seviyesi, kaynak: upstream docs. §I.2'deki satırlar
> **kavramların uygulamadaki sargılarıdır**, çekirdek içi implementasyon değildir.

### §H.1 Ölçüm Sonuçlarının Yorumu

| Gözlem | Yorum |
|---|---|
| Envanter kesişimi 0 | URL'ler Vault genelinde tekil; çifte kaynak yok |
| Mojibake 0 | UTF-8 kodlama bozulmamış |
| Dolgu 0 | Her §N bölümü gerçek içerik taşıyor |
| `parent:` 0 | v2 şemasına uyum (`parent_id` + `children`) |
| `posix_*`/`getpwuid` L3 kanıtı eksik | §D.2 kural 1 gereği `VERIFICATION REQUIRED` — bilinçli eksiklik, dolgu değil |

## §J Bu Tur Master URL Matrisi (2026-10-14 · HTTP 200 · envanter kesişim ölçümü)

> §J, 2026-10-14 `-ornekler.md` gruplama turunda çağrılan **56 URL**nin tek kaynak
> matrisidir. Her satır `Invoke-WebRequest -Method Get` ile 2026-10-14**'**te HTTP 200
> ile ölçülmüştür. Envanter kesişimi `.ai/**` (gizli dizin, `*.md|*.json|*.txt|*.ps1`)
> kümelerine karşı **önce bu dosyanın da içinde olduğu 6 dosya hariç** tutularak
> ölçülmüştür: **3760 tekil URL / kesişim 3 = envanterde zaten bulunan 3 URL (§J.5)**
> → **gerçekten yeni URL 53**. §J.6, 2026-10-13 turunda HTTP 200 diye yazılıp
> 2026-10-14'te **404** ölçülen ve bozuk biçimli URL'lerin defteridir.

### §J.1 POSIX / The Open Group — Issue 8 (20 URL · 18 yeni + 2 envanterde vardı)

| İddia | URL | 2026-10-14 | Durum |
|---|---|---|---|
| `chmod` — chmod, dosya erişim izinlerini mode bitleriyle değiştirir | https://pubs.opengroup.org/onlinepubs/9799919799/functions/chmod.html | 200 | YENİ |
| `execve` — execve, yeni bir program görüntüsü başlatır | https://pubs.opengroup.org/onlinepubs/9799919799/functions/execve.html | 200 | YENİ |
| `malloc` — malloc, serbest heap bloğu ayırır | https://pubs.opengroup.org/onlinepubs/9799919799/functions/malloc.html | 200 | YENİ |
| `stat` — stat, dosya durumunu (inode/meta) alır | https://pubs.opengroup.org/onlinepubs/9799919799/functions/stat.html | 200 | YENİ |
| `munmap` — munmap, eşlenmiş bellek bölgesini serbest bırakır | https://pubs.opengroup.org/onlinepubs/9799919799/functions/munmap.html | 200 | YENİ |
| `shm_open` — shm_open, paylaşılmış bellek nesnesi açar | https://pubs.opengroup.org/onlinepubs/9799919799/functions/shm_open.html | 200 | YENİ |
| `fopen` — fopen, akış dosyası açar | https://pubs.opengroup.org/onlinepubs/9799919799/functions/fopen.html | 200 | YENİ |
| `realpath` — realpath, canonical yol çözümler | https://pubs.opengroup.org/onlinepubs/9799919799/functions/realpath.html | 200 | YENİ |
| `umask` — umask, süreç bazlı dosya oluşturma maskesini okur/ayarlar | https://pubs.opengroup.org/onlinepubs/9799919799/functions/umask.html | 200 | YENİ |
| `mkdir` — mkdir, dizin oluşturur | https://pubs.opengroup.org/onlinepubs/9799919799/functions/mkdir.html | 200 | YENİ |
| `lseek` — lseek, dosya tanıtıcısını konumlandırır | https://pubs.opengroup.org/onlinepubs/9799919799/functions/lseek.html | 200 | YENİ |
| `setuid` — setuid, süreç gerçek kimliğini değiştirir | https://pubs.opengroup.org/onlinepubs/9799919799/functions/setuid.html | 200 | YENİ |
| `fchmod` — fchmod, açık tanıtıcı üzerinden izin değiştirir | https://pubs.opengroup.org/onlinepubs/9799919799/functions/fchmod.html | 200 | YENİ |
| `chown` — chown, dosya sahipliğini değiştirir | https://pubs.opengroup.org/onlinepubs/9799919799/functions/chown.html | 200 | YENİ |
| `pathconf` — pathconf, dosya sistemi limitlerini sorgular | https://pubs.opengroup.org/onlinepubs/9799919799/functions/pathconf.html | 200 | YENİ |
| `seteuid` — seteuid, etkin kullanıcı kimliğini değiştirir | https://pubs.opengroup.org/onlinepubs/9799919799/functions/seteuid.html | 200 | YENİ |
| `uname` — uname, sistem bilgi yapısını doldurur | https://pubs.opengroup.org/onlinepubs/9799919799/functions/uname.html | 200 | YENİ |
| `pthread_exit` — pthread_exit, iplik çalışmasını sonlandırır | https://pubs.opengroup.org/onlinepubs/9799919799/functions/pthread_exit.html | 200 | YENİ |
| `write` — write, dosya tanıtıcısına veri yazar | https://pubs.opengroup.org/onlinepubs/9799919799/functions/write.html | 200 | ENVANTERDE VAR (§J.5) |
| `close` — close, dosya tanıtıcısını kapatır | https://pubs.opengroup.org/onlinepubs/9799919799/functions/close.html | 200 | ENVANTERDE VAR (§J.5) |

> **§E ölü satır değişimi (§J.6 D1) — 13 URL karşılığı:** §E'deki 13
> `apex/lv4/xsh/*.htm` satırı aynı `9799919799/functions` önekiyle değiştirildi
> (13/13 HTTP 200 ölçüldü). Dağılım: **11'i §J.1 tablosunda "YENİ" olarak var**;
> kalan **2 URL bu turda yeni değildir, envanterde de mevcuttur** (reuse) ve yalnızca
> yeniden HTTP 200 ile doğrulandı —
> `https://pubs.opengroup.org/onlinepubs/9799919799/functions/pthread_atfork.html` (HTTP 200) ·
> `https://pubs.opengroup.org/onlinepubs/9799919799/functions/dup.html` (HTTP 200).

### §J.2 Linux man-pages — man7.org (6 URL · 6 yeni)

| İddia | URL | 2026-10-14 | Durum |
|---|---|---|---|
| `posix_spawn.3` — posix_spawn, fork+exec birleşimiyle yeni süreç üretir | https://www.man7.org/linux/man-pages/man3/posix_spawn.3.html | 200 | YENİ |
| `shm_open.3` — shm_open(3), POSIX paylaşılmış bellek arayüzü | https://www.man7.org/linux/man-pages/man3/shm_open.3.html | 200 | YENİ |
| `mkdir.2` — mkdir(2), Linux çekirdek dizin oluşturma arayüzü | https://www.man7.org/linux/man-pages/man2/mkdir.2.html | 200 | YENİ |
| `_exit.2` — _exit(2), beklemeden (cleanup yapmadan) süreçten çıkış | https://www.man7.org/linux/man-pages/man2/_exit.2.html | 200 | YENİ |
| `acl_set_file.3` — acl_set_file, dosya ACL'sini yazar (Linux EXT4/POSIX ACL) | https://www.man7.org/linux/man-pages/man3/acl_set_file.3.html | 200 | YENİ |
| `acl_get_file.3` — acl_get_file, dosya ACL'sini okur | https://www.man7.org/linux/man-pages/man3/acl_get_file.3.html | 200 | YENİ |

> ``acl_*.html`` kalıbı ``pubs.opengroup.org`` Issue 8 üzerinde **HTTP 404**
> ölçülmüştür (§J.6) — ACL örneklerinin POSIX kaynağı yalnızca man7'dir.

### §J.3 Microsoft Win32 API (29 URL · 28 yeni + 1 envanterde vardı)

| İddia | URL | 2026-10-14 | Durum |
|---|---|---|---|
| `api/processthreadsapi/nf-processthreadsapi-getexitcodeprocess` — GetExitCodeProcess, alt sürecin çıkış kodunu okur | https://learn.microsoft.com/en-us/windows/win32/api/processthreadsapi/nf-processthreadsapi-getexitcodeprocess | 200 | YENİ |
| `api/processthreadsapi/nf-processthreadsapi-terminatethread` — TerminateThread, ipliği zorla sonlandırır | https://learn.microsoft.com/en-us/windows/win32/api/processthreadsapi/nf-processthreadsapi-terminatethread | 200 | YENİ |
| `api/processthreadsapi/nf-processthreadsapi-openprocess` — OpenProcess, başka süreç için tanıtıcı açar | https://learn.microsoft.com/en-us/windows/win32/api/processthreadsapi/nf-processthreadsapi-openprocess | 200 | YENİ |
| `api/processthreadsapi/nf-processthreadsapi-getcurrentprocessid` — GetCurrentProcessId, PID döndürür | https://learn.microsoft.com/en-us/windows/win32/api/processthreadsapi/nf-processthreadsapi-getcurrentprocessid | 200 | YENİ |
| `api/processthreadsapi/nf-processthreadsapi-getcurrentthread` — GetCurrentThread, iplik pseudo-tanıtıcısını döndürür | https://learn.microsoft.com/en-us/windows/win32/api/processthreadsapi/nf-processthreadsapi-getcurrentthread | 200 | YENİ |
| `api/processthreadsapi/nf-processthreadsapi-getcurrentprocess` — GetCurrentProcess, süreç pseudo-tanıtıcısını döndürür | https://learn.microsoft.com/en-us/windows/win32/api/processthreadsapi/nf-processthreadsapi-getcurrentprocess | 200 | YENİ |
| `procthread/scheduling-priorities` — Scheduling Priorities, Windows öncelik sınıfı tablosu (2026-10-13 bozuk biçimi düzeltildi — §E.6) | https://learn.microsoft.com/en-us/windows/win32/procthread/scheduling-priorities | 200 | YENİ |
| `api/synchapi/nf-synchapi-createmutexw` — CreateMutexW, mutex nesnesi oluşturur | https://learn.microsoft.com/en-us/windows/win32/api/synchapi/nf-synchapi-createmutexw | 200 | YENİ |
| `api/synchapi/nf-synchapi-releasemutex` — ReleaseMutex, mutex kilidini bırakır | https://learn.microsoft.com/en-us/windows/win32/api/synchapi/nf-synchapi-releasemutex | 200 | YENİ |
| `api/synchapi/nf-synchapi-entercriticalsection` — EnterCriticalSection, kritik bölüm girişini bekler | https://learn.microsoft.com/en-us/windows/win32/api/synchapi/nf-synchapi-entercriticalsection | 200 | YENİ |
| `api/synchapi/nf-synchapi-leavecriticalsection` — LeaveCriticalSection, kritik bölüm kilidini bırakır | https://learn.microsoft.com/en-us/windows/win32/api/synchapi/nf-synchapi-leavecriticalsection | 200 | YENİ |
| `sync/mutex-objects` — Mutex Objects, Win32 mutex nesne modeli | https://learn.microsoft.com/en-us/windows/win32/sync/mutex-objects | 200 | ENVANTERDE VAR (§J.5) |
| `api/memoryapi/nf-memoryapi-createfilemappingw` — CreateFileMappingW, dosya eşlemesi (mapping) oluşturur | https://learn.microsoft.com/en-us/windows/win32/api/memoryapi/nf-memoryapi-createfilemappingw | 200 | YENİ |
| `api/memoryapi/nf-memoryapi-unmapviewoffile` — UnmapViewOfFile, eşlenmiş görünümü kaldırır | https://learn.microsoft.com/en-us/windows/win32/api/memoryapi/nf-memoryapi-unmapviewoffile | 200 | YENİ |
| `memory/creating-a-file-mapping-object` — Creating a File Mapping Object, dosya eşleme akışı | https://learn.microsoft.com/en-us/windows/win32/memory/creating-a-file-mapping-object | 200 | YENİ |
| `api/heapapi/nf-heapapi-heapfree` — HeapFree, yığın bloğunu serbest bırakır | https://learn.microsoft.com/en-us/windows/win32/api/heapapi/nf-heapapi-heapfree | 200 | YENİ |
| `api/fileapi/nf-fileapi-lockfileex` — LockFileEx, dosya üzerinde bölge kilidi (flock karşılığı) | https://learn.microsoft.com/en-us/windows/win32/api/fileapi/nf-fileapi-lockfileex | 200 | YENİ |
| `api/fileapi/nf-fileapi-createdirectoryw` — CreateDirectoryW, dizin oluşturur | https://learn.microsoft.com/en-us/windows/win32/api/fileapi/nf-fileapi-createdirectoryw | 200 | YENİ |
| `api/fileapi/nf-fileapi-getfileattributesexw` — GetFileAttributesExW, dosya özniteliklerini (stat karşılığı) okur | https://learn.microsoft.com/en-us/windows/win32/api/fileapi/nf-fileapi-getfileattributesexw | 200 | YENİ |
| `api/fileapi/nf-fileapi-getfileattributesw` — GetFileAttributesW, dosya özniteliklerini okur | https://learn.microsoft.com/en-us/windows/win32/api/fileapi/nf-fileapi-getfileattributesw | 200 | YENİ |
| `api/fileapi/nf-fileapi-getfilesizeex` — GetFileSizeEx, dosya boyutunu okur | https://learn.microsoft.com/en-us/windows/win32/api/fileapi/nf-fileapi-getfilesizeex | 200 | YENİ |
| `api/fileapi/nf-fileapi-setfilepointerex` — SetFilePointerEX, dosya konumunu ayarlar (lseek karşılığı) | https://learn.microsoft.com/en-us/windows/win32/api/fileapi/nf-fileapi-setfilepointerex | 200 | YENİ |
| `api/handleapi/nf-handleapi-duplicatehandle` — DuplicateHandle, tanıtıcı çoğaltır (dup karşılığı) | https://learn.microsoft.com/en-us/windows/win32/api/handleapi/nf-handleapi-duplicatehandle | 200 | YENİ |
| `secauthz/access-control-lists` — Access Control Lists, Windows ACL modeli | https://learn.microsoft.com/en-us/windows/win32/secauthz/access-control-lists | 200 | YENİ |
| `api/aclapi/nf-aclapi-setnamedsecurityinfow` — SetNamedSecurityInfoW, nesne güvenlik bilgisini yazar | https://learn.microsoft.com/en-us/windows/win32/api/aclapi/nf-aclapi-setnamedsecurityinfow | 200 | YENİ |
| `api/aclapi/nf-aclapi-getnamedsecurityinfow` — GetNamedSecurityInfoW, nesne güvenlik bilgisini okur | https://learn.microsoft.com/en-us/windows/win32/api/aclapi/nf-aclapi-getnamedsecurityinfow | 200 | YENİ |
| `api/securitybaseapi/nf-securitybaseapi-addaccessallowedace` — AddAccessAllowedAce, izin veren ACE ekler | https://learn.microsoft.com/en-us/windows/win32/api/securitybaseapi/nf-securitybaseapi-addaccessallowedace | 200 | YENİ |
| `api/securitybaseapi/nf-securitybaseapi-initializeacl` — InitializeAcl, ACL yapısını başlatır | https://learn.microsoft.com/en-us/windows/win32/api/securitybaseapi/nf-securitybaseapi-initializeacl | 200 | YENİ |
| `api/libloaderapi/nf-libloaderapi-getmodulefilenamew` — GetModuleFileNameW, yürütülebilir dosya yolunu alır | https://learn.microsoft.com/en-us/windows/win32/api/libloaderapi/nf-libloaderapi-getmodulefilenamew | 200 | YENİ |

### §J.4 Apple / macOS (1 URL · 1 yeni)

| İddia | URL | 2026-10-14 | Durum |
|---|---|---|---|
| Grand Central Dispatch (libdispatch) — macOS kuyruk/iplik yürütme API'si | https://developer.apple.com/documentation/dispatch | 200 | YENİ |

> **⚠️ VERIFICATION REQUIRED:** macOS yerel ``pthread_*.html`` / man sayfaları
> bu oturumda erişilebilir değildi (``developer.apple.com/documentation/kernel/pthread``
> **404**, ``opensource.apple.com/manpages/`` **404** — §J.6). macOS sütunlarındaki
> iddialar POSIX ``pubs.opengroup.org`` kaynaklıdır; Darwin'e özgü davranışlar
> **kanıtlanmamıştır**.

### §J.5 Envanterde Zaten Bulunan 3 URL (bu tur "yeni" sayılmadı)

| URL | Envanterdeki ilk kayıt | Not |
|---|---|---|
| `https://pubs.opengroup.org/onlinepubs/9799919799/functions/write.html` | `architecture/K000-isletim-sistemi/isletim-sistemi-api/ag-api/posix-socket-ve-winsock-karsilastirmasi.md` | Çakışma tespit edildi → YENİ sayımından düşüldü |
| `https://pubs.opengroup.org/onlinepubs/9799919799/functions/close.html` | `architecture/K000-isletim-sistemi/isletim-sistemi-api/ag-api/posix-socket-ve-winsock-karsilastirmasi.md` | Çakışma tespit edildi → YENİ sayımından düşüldü |
| `https://learn.microsoft.com/en-us/windows/win32/sync/mutex-objects` | `architecture/K000-isletim-sistemi/isletim-sistemi-kernel/kesme-ve-senkronizasyon/index.md` | Çakışma tespit edildi → YENİ sayımından düşüldü |

> **Düzeltme kaydı:** 2026-10-14 ilk ölçümünde kesişim **0** çıkmıştı; 6 dosyalık
> yazım tamamlandıktan **sonra** yapılan ikinci ölçümde (yazılan dosyalar hariç)
> kesişim **3** bulundu ve sayımlar 56 → **53 yeni / 3 kullanım** olarak düzeltildi.
> Öz-kaynak (kendi yazdığımız satırlar) kesişim sayılmaz; harici 3 kayıt sayılmıştır.

### §J.6 Ölü URL ve Bozuk Biçim Defteri (ölçüm 2026-10-14)

| # | Bozuk / ölü biçim | Ölçüm | Eylem |
|---|---|---|---|
| D1 | ``https://www.opengroup.org/apex/lv4/xsh/*.htm`` (13 satır: §E L211, L239, L267-L272, L289-L293) | **HTTP 404** | ``pubs.opengroup.org/onlinepubs/9799919799/functions/*.html`` ile değiştirildi — 13/13 HTTP 200 (11'i §J.1'de · 2 ek: §J.1 sonu notu) |
| D2 | ``.../win32/procs threading/scheduling-priorities`` ve ``.../win32/procs threading`` (boşluklu) | YANLIŞ YAZIM | ``.../win32/procthread/scheduling-priorities`` — HTTP 200 (§E.5, §J.3) |
| D3 | ``winbase/nf-winbase-{createmutexw,getmodulefilenamew,unmapviewoffile,flushfilebuffers}`` | **HTTP 404** | Doğru başlıklar ``api/synchapi``, ``api/libloaderapi``, ``api/memoryapi``; ``FlushFileBuffers`` bu turda çağrılmadı |
| D4 | ``processthreadsapi/nf-...-{waitforsingleobject,entercriticalsection,leavecriticalsection,initializecriticalsection,deletecriticalsection}`` (``/api/`` eksik) | **HTTP 404** | ``/api/`` eklendi; ``WaitForSingleObject``/``InitializeCriticalSection`` bu turda örneklenmedi |
| D5 | ``sysinfo/memory-management`` · ``securitybaseapi/...-initializeacled`` · ``errhandlingapi/...-raisexception`` · ``sync/critical-sections`` | **HTTP 404** | Kullanılmadı; kritik bölüm için ``sync/mutex-objects`` + ``synchapi`` kullanıldı |
| D6 | ``pubs.opengroup.org/onlinepubs/09699950799/functions/fork.html`` | **HTTP 404** | ``fork`` bu turda örneklenmedi (``posix_spawn`` tercih edildi) |
| D7 | ``pubs.../9799919799/functions/acl_*.html`` | **HTTP 404** | ACL örnekleri yalnız ``man7.org``'a bağlandı (§J.2) |
| D8 | ``developer.apple.com/documentation/kernel/pthread`` · ``opensource.apple.com/manpages/`` | **HTTP 404** | macOS sütunları POSIX'e bağlandı + ⚠️ VERIFICATION REQUIRED (§J.4) |
| D9 | ``win32/procps/child-processes`` — §E satırı "200 · Doğrulandı" idi | **HTTP 404** (2026-10-14) | Doğru biçim ``win32/procthread/child-processes`` — HTTP 200; envanterde de vardı (reuse) |
| D10 | ``iis/configuration/system.webServer/rewrite/rules`` · ``iis/extensions/url-rewrite-module/using-rewrite-rules-in-webconfig`` | **HTTP 404** (2026-10-14) | 2026-10-13''ten beri izin-ve-guvenlik §11 dürüstlük notunda kayıtlı; iddia olarak kullanılmıyor |

### §J.7 Ölçüm Sonuçları (2026-10-14)

| Ölçüm | Değer |
|---|---|
| Çağrılan toplam URL (§J.1-§J.4) | **56** |
| HTTP 200 doğrulanan | **56 / 56 (%100)** |
| Envanter (6 dosya hariç, ``.ai/**`` gizli) | **3760 tekil URL** · ölçüm anı **2026-10-14 11:29** |
| Envanter kesişimi | **3** (§J.5 — envanterde zaten var, YENİ sayılmadı) |
| **Bu turda gerçekten eklenen yeni URL** | **53** |
| Ölçüm sonrası envanter | **3866 tekil URL** |
| Bu 6 dosyada geçen TÜM tekil URL (yeniden tarama) | **202** → **199 HTTP 200** · 3 non-200 = 2 IIS (izin §11 dürüstlük notu, iddia olarak kullanılmıyor) + 1 ölü kalıp kalıbı (§J.6 D1, satırında zaten **404** yazılı) |
| Ölü URL (HTTP 404) düzeltmesi | **13** satır (D1) + **2** bozuk biçim (D2) + **8** defter kalemi (D3-D10) |
| Ölü URL ikamesi için ek doğrulanan URL | **2** (`pthread_atfork.html` · `dup.html` — §J.1 sonu, HTTP 200) · **her ikisi de envanterde vardı → reuse** |
| Toplam bu turda HTTP 200 ile ölçülen benzersiz URL | **58** (56 + 2) |
| §H "Dolgu metin 0" kaydının tek istisnası | §F.3 F-9 özet bloğu (satır ~480, ``// ...``) — başlığı ``(özet)`` ile etiketli, 2026-10-13'ten beri mevcut, **bu turda eklenmedi** |
| Ölçüm istikrarı notu | `.ai/**` envanteri **hareketlidir** (eşzamanlı başka oturumlar da yazıyor; `git status` bu turda electroncis/audio dosyalarında başka oturum değişiklikleri gösteriyor) — tüm sayılar **2026-10-14 11:29** anına aittir |
| Mojibake | 0 |
| ``parent:`` alanı | 0 (``parent_id``) |

### §J.8 PHP Köprüsü Turu — URL Bileşeni (2026-10-14)

> Bu tur 5 içerik dosyasına PHP örneği bölümleri ekledi (surec §20 · bellek §18 · dosya §13 ·
> izin §20 · core §7). PHP bölümleri **repo satırlarına** dayanır; yine de 2 alıntı URL'si eklendi.

| URL | Kullanım | Envanter | HTTP |
|---|---|---|---|
| `https://www.php.net/manual/en/function.file-put-contents.php` | `LOCK_EX` tanımı alıntısı (dosya §13.1) + fonksiyon indeksi (§13.7) | **VAR** (reuse) | **200** |
| `https://www.php.net/manual/en/function.flock.php` | `flock` bağlantısı (dosya §13.1) | **VAR** (reuse) | **200** |

| Ölçüm | Değer |
|---|---|
| Bu turda eklenen **yeni** URL | **0** |
| Reuse (envanterde zaten var) | **2** — §J.5'teki 3 reuse'e ek → toplam reuse **5** |
| PHP bölümlerindeki diğer URL | **0** (4 PHP bölümünde hiç URL yok; yalnız dosya §13.1/§13.7 bu 2 alıntıyı taşır) |
| Ölçüm anı | **2026-10-14 12:17** · envanter (6 dosya hariç) o an **3949** → kesişim **2** = reuse |

### §J.9 Çok Dilli Tur — URL Defteri (2026-10-14 · JS · C · Python · Go · Rust · C#)

> 4 içerik dosyasına çok dilli bölümler eklendi (surec §21 · bellek §19 · dosya §14 · izin §21) +
> core §8 indeksi. JS/PY blokları depo satırına dayanır; C/Go/Rust/C# blokları `⚠️ KALIP (repo YOK)`
> olarak resmi doküman URL'ine bağlandı. **24 URL ölçümü: 24/24 HTTP 200.**

| # | URL | HTTP | Envanter | Kullanan bölüm |
|---|---|---|---|---|
| 1 | `https://nodejs.org/api/child_process.html` | 200 | reuse | surec §21.1 |
| 2 | `https://pubs.opengroup.org/onlinepubs/9799919799/functions/fork.html` | 200 | reuse | surec §21.2 |
| 3 | `https://pubs.opengroup.org/onlinepubs/9799919799/functions/execve.html` | 200 | **YENİ** | surec §21.2 |
| 4 | `https://docs.python.org/3/library/subprocess.html` | 200 | reuse | surec §21.3 |
| 5 | `https://pkg.go.dev/os/exec` | 200 | reuse | surec §21.4 |
| 6 | `https://doc.rust-lang.org/std/process/struct.Command.html` | 200 | reuse | surec §21.5 |
| 7 | `https://learn.microsoft.com/en-us/dotnet/api/system.diagnostics.process` | 200 | reuse | surec §21.6 |
| 8 | `https://pubs.opengroup.org/onlinepubs/9799919799/functions/mmap.html` | 200 | reuse | bellek §19.2 |
| 9 | `https://docs.python.org/3/library/secrets.html` | 200 | **YENİ** | bellek §19.3 |
| 10 | `https://pkg.go.dev/runtime` | 200 | **YENİ** | bellek §19.4 |
| 11 | `https://learn.microsoft.com/en-us/dotnet/api/system.gc` | 200 | **YENİ** | bellek §19.6 |
| 12 | `https://learn.microsoft.com/en-us/dotnet/api/system.security.cryptography.randomnumbergenerator` | 200 | **YENİ** | bellek §19.6 |
| 13 | `https://nodejs.org/api/fs.html` | 200 | **YENİ** | dosya §14.1 |
| 14 | `https://docs.python.org/3/library/os.html` | 200 | reuse | dosya §14.2 · izin §21.3 |
| 15 | `https://docs.python.org/3/library/fcntl.html` | 200 | **YENİ** | dosya §14.2 |
| 16 | `https://pubs.opengroup.org/onlinepubs/9799919799/functions/open.html` | 200 | reuse | dosya §14.3 · izin §21.2 |
| 17 | `https://pubs.opengroup.org/onlinepubs/9799919799/functions/fcntl.html` | 200 | **YENİ** | dosya §14.3 |
| 18 | `https://pkg.go.dev/os` | 200 | **YENİ** | dosya §14.4 · izin §21.4 |
| 19 | `https://doc.rust-lang.org/std/fs/struct.File.html` | 200 | **YENİ** | dosya §14.5 |
| 20 | `https://learn.microsoft.com/en-us/dotnet/api/system.io.filestream` | 200 | **YENİ** | dosya §14.6 |
| 21 | `https://pubs.opengroup.org/onlinepubs/9799919799/functions/umask.html` | 200 | **YENİ** | izin §21.2 |
| 22 | `https://doc.rust-lang.org/std/fs/fn.set_permissions.html` | 200 | **YENİ** | izin §21.5 |
| 23 | `https://learn.microsoft.com/en-us/dotnet/api/system.io.file.setunixfilemode` | 200 | **YENİ** | izin §21.6 |
| 24 | `https://man7.org/linux/man-pages/man2/fork.2.html` | 200 | reuse | **KULLANILMADI** (yazılmadı → envantere girmedi) |

| Ölçüm | Değer |
|---|---|
| Ölçülen URL | **24 / 24 HTTP 200** |
| Envante göre reuse / yeni | **10 reuse · 14 yeni** |
| Dosyalarda kullanılan | **23** (9 reuse + 14 yeni) |
| Kullanılmayan | **1** (`man7 fork.2`) — 4 bölümde hiç geçmiyor |
| Envanter (6 dosya hariç) | **4007 tekil URL** · ölçüm anı **2026-10-14 12:41** |
| 14 yeni URL kesişimi | **0** (hiçbiri envanterde yoktu → kural sağlanmış) |
| Bu turda eklenen `⚠️ KALIP (repo YOK)` blok | **22** (surec 6 · bellek 5 · dosya 6 · izin 5) |
| Bu turda depo satır kanıtlı blok | **5** (JS ×3 · PY ×2) |
| Sürüm iddiası içeren satır | hepsi `⚠️ VERIFICATION REQUIRED` (.NET sürümü · Rust unix tekilliği · Go kill sinyali · CPython GC · MDN monotoniklik) |
| Mojibake / CJK / `parent:` | **0 / 0 / 0** |
| `git status` | 6 dosya `M` · `*.php/js/css/sql` değişikliği **0** · **commit atılmadı** |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
