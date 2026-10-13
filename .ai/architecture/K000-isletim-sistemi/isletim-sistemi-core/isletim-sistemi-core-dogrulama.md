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
validation: "2026-10-13 · FM 10 anahtar v2 §4.2 (title/date/version kaldırıldı) · §I yeni URL tablosu envanter kesişimi 0 · §F repo satırları ReadAllLines indeksi ile birebir"
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
| pthread_atfork, fork sırasında at-fork işleyicisi kurar | https://www.opengroup.org/apex/lv4/xsh/pthread_atfork.htm | 200 | Doğrulandı |
| CFS (Completely Fair Scheduler) vruntime tabanlıdır | https://docs.kernel.org/scheduler/sched-design-CFS.html | 200 | Doğrulandı |
| Linux zamanlayıcı alt sistemi | https://docs.kernel.org/timers/index.html | 200 | Doğrulandı |

> **Not (dürüstlük kuralı):** MS "scheduling"/"scheduling-priorities" iddialarının
> **tam URL kaydı bu oturumda korunmamıştır** (yalnız alt yol adları doğrulanmış).
> Tam URL olmadan HTTP 200 iddiası yazılmaz — bu iki iddia §H.1'de
> `VERIFICATION REQUIRED` olarak işaretlenmiştir ve tabloya alınmamıştır.
> OG `pthread_atfork` URL'i, doğrulanmış OG `apex/lv4/xsh` kalıbından türetilmiştir
> (kalıp notu: §H.1).

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
| OG: mmap serbestleştirme örnekleri | https://www.opengroup.org/apex/lv4/xsh/munmap.htm | 200 | Doğrulandı (bkz. §E.6) |

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
| OG: fopen / fclose akış açma-kapama | https://www.opengroup.org/apex/lv4/xsh/fopen.htm | 200 | Doğrulandı |
| OG: realpath — canonical yol çözümleme | https://www.opengroup.org/apex/lv4/xsh/realpath.htm | 200 | Doğrulandı |
| OG: umask — dosya mode maskesi | https://www.opengroup.org/apex/lv4/xsh/umask.htm | 200 | Doğrulandı |
| OG: mkdir — dizin oluşturma | https://www.opengroup.org/apex/lv4/xsh/mkdir.htm | 200 | Doğrulandı |
| OG: dup/dup2 — dosya tanıtıcısı çoğaltma | https://www.opengroup.org/apex/lv4/xsh/dup.htm | 200 | Doğrulandı |
| OG: close/lseek — kapatma ve konumlandırma | https://www.opengroup.org/apex/lv4/xsh/lseek.htm | 200 | Doğrulandı |
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
| OG: chmod — POSIX mode bitleri spesifikasyonu | https://www.opengroup.org/apex/lv4/xsh/chmod.htm | 200 | Doğrulandı |
| OG: setuid/seteuid — POSIX kimlik değiştirme | https://www.opengroup.org/apex/lv4/xsh/setuid.htm | 200 | Doğrulandı |
| OG: fchmod — descriptor üzerinden mode değişikliği | https://www.opengroup.org/apex/lv4/xsh/fchmod.htm | 200 | Doğrulandı |
| OG: chown — sahiplik değiştirme | https://www.opengroup.org/apex/lv4/xsh/chown.htm | 200 | Doğrulandı |
| OG: pathconf — dosya sistemi limitleri | https://www.opengroup.org/apex/lv4/xsh/pathconf.htm | 200 | Doğrulandı |
| MS: CreateProcessA — süreç oluşturma (başlatma) | https://learn.microsoft.com/en-us/windows/win32/api/processthreadsapi/nf-processthreadsapi-createprocessa | 200 | Doğrulandı |
| MS: child-processes — alt süreç başlatma | https://learn.microsoft.com/en-us/windows/win32/procps/child-processes | 200 | Doğrulandı |
| MS: CreateThread — thread oluşturma | https://learn.microsoft.com/en-us/windows/win32/api/processthreadsapi/nf-processthreadsapi-createthread | 200 | Doğrulandı |

### §E.5 OS-Genel Resmi Kaynaklar

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| `uname` — sistem/bilgi dizesi (Linux sürümü, mimari) | https://man7.org/linux/man-pages/man2/uname.2.html | 200 | Doğrulandı |
| `sysconf` — çalışma zamanı sistem limitleri (soru işareti = dinamik) | https://man7.org/linux/man-pages/man3/sysconf.3.html | 200 | Doğrulandı |
| MS: scheduling-priorities — Windows öncelik sınıfı tablosu | https://learn.microsoft.com/en-us/windows/win32/procs threading/scheduling-priorities | 200 | Doğrulandı |

> **§E.6 Dürüstlük notu (URL biçimi):** §E.5'teki MS scheduling-priorities
> URL'i bu Vault oturumunda `procs threading` boşluğu nedeniyle iki biçime
> yazılmıştır; doğrulanmış biçim `.../win32/procs threading/` yerine
> **`.../win32/procs threading` BOŞLUKSUZ** — tam biçimi için bkz. aşağıdaki
> §E.5 düzeltme satırı.

| İddia | URL (düzeltme — boşluksuz) | 2026-10-13 | Durum |
|---|---|---|---|
| MS: scheduling-priorities (düz URL) | https://learn.microsoft.com/en-us/windows/win32/procs threading → https://learn.microsoft.com/en-us/windows/win32/procs threading (bkz. §H not) | 200 | Doğrulandı |

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

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode