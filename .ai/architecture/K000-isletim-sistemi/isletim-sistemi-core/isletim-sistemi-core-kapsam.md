---
node_id: K000-isletim-sistemi/isletim-sistemi-core/isletim-sistemi-core-kapsam
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-core
children: []
depends_on: []
evidence:
  - https://docs.kernel.org/filesystems/vfs.html
  - https://docs.kernel.org/filesystems/index.html
  - https://man7.org/linux/man-pages/man7/inode.7.html
  - https://docs.kernel.org/process/index.html
validation: "2026-10-13 · 4 yeni URL HTTP 200 (HEAD) · 4 alt-dal + 16 dosya disk kanıtlı · repo satırları ReadAllLines indeksi ile birebir · §G 5 URL envanter kesişimi 0"
open_questions:
  - "Q2: core ↔ kernel sınırı → ⚠️ VERIFICATION REQUIRED (Q-C1)"
  - "Q4: hedef platform seti → UNKNOWN (Q-C3)"
group: mimari
---

# isletim-sistemi-core — Kapsam

> Bu dosya `isletim-sistemi-core` dalının **sınır belgesidir**: ne içerir, ne içermez,
> 8 çekirdek kavramın **hangisinin nerede** durduğu ve 4 alt-dalın envanteri.
> Kapsam dışı maddeler bilgi olarak **silinmez**, sahibi olan dalın adıyla taşınır.

## §A — Kapsam

### §A.1 Rol (ELI15)

İşletim sisteminin **kavramsal çekirdeği**: "bir OS ne *demektir*" sorusunun cevabı —
bellek, süreç, dosya, izin, zaman. Kod değil **kavram haritası**. `os-master.md` §3 bu
kavramları sekize ayırır ve her birini ayrı child'a dağıtır.

Analoji: bir arabanın **şasi ve motorunun tarifi** — tekerlek nereye bağlı, güç nereye
gidiyor; aracın hangi marka olduğu (platform) değil.

Bu turda dal iki parçaya ayrıldı: **kök 4 dosya** (künye · kapsam · doğrulama · örnekler)
sınırı ve kanıtı taşır; **4 alt-dal** (süreç/zamanlama · bellek · dosya sistemi · izin)
kavramın kendisini derinleştirir. Bilgi tek yönlü akar: alt-dal → kök → komşu dal.

### §A.2 Kapsam — İÇİNDE NE var

| # | İçinde | Dayanak | Alt-dal karşılığı |
|---|---|---|---|
| A1 | Bellek yönetimi kavramı (adres alanı, `mmap`, sayfalama) | `-dogrulama` §1/1·2·3 | `bellek-kavramlari/` |
| A2 | Süreç/iş kavramı (yaratma, sonlandırma, zaman paylaşımı) | `-dogrulama` §1/4·5 | `surec-ve-zamanlama-kavramlari/` |
| A3 | Dosya sistemi kavramı + VFS soyutlaması | `-dogrulama` §1/6 | `dosya-sistemi-kavramlari/` |
| A4 | İzin + güvenlik kavramı (rwx, umask, capability, ACL) | `os-master.md` §3.5 | `izin-ve-guvenlik-kavramlari/` |
| A5 | Kavramsal "çekirdek" = OS'un vazgeçilmez asgari işlevi | `os-master.md` §2 katman 1 | kök `index.md` §1 |
| A6 | Startup/init kavramı (zaman dilimi, hata gösterimi) | `os-master.md` §4 | kök `-ornekler.md` §A/5 |
| A7 | Paylaşımlı bellek kavramı (önbellek) | `os-bagimliliklar.md` §2 C1 | `bellek-kavramlari/` |
| A8 | 8 kavramlık haritada "kavram" düzeyinde kalan her şey | `os-master.md` §3 | §C dağılım haritası |

**Sınır ilkesi:** A1-A4 bu turda **alt-dala inmiştir**; A5-A8 kök dosyalarda kalır.
Alt-dal dosyaları kavramı derinleştirir, **komşuya yeni konu taşımaz**.

### §A.3 Kapsam — DIŞINDA NE var (kapsam dışı)

| # | Dışında olan konu | Nereye gider | Dayanak | Bu turda eklendi mi? |
|---|---|---|---|---|
| B1 | Sistem çağrılarının kendisi (syscall yüzeyi, libc wrapper) | `isletim-sistemi-api` | kök `index.md` §4 | Hayır — yalnız adı geçti |
| B2 | Ses donanımı erişimi (WASAPI/ALSA/CoreAudio) | `isletim-sistemi-audio` | `os-master.md` §3.8 | Hayır |
| B3 | Gözlemlenebilirlik (ftrace, ETW, perf) | `isletim-sistemi-observability` | `os-master.md` §3.6 | Hayır |
| B4 | Test altyapısı (kselftest, KUnit, PHPUnit) | `isletim-sistemi-testing` | `os-master.md` §3.7 | Hayır |
| B5 | Yürütme, preemption, sürücü, zamanlama **mekanizması** | `isletim-sistemi-kernel` | kök `index.md` §4 | Hayır |
| B6 | Planlama **politikası** ve karar dokümanı | `isletim-sistemi-plan` | `os-master.md` §8 Q3 | Hayır |
| B7 | OS ailesi karşılaştırması (Windows/Linux/macOS) | `isletim-sistemi-turu` | kök `index.md` §4 | Hayır |
| B8 | Log/metric/trace araçları | `isletim-sistemi-observability` | eski `-kapsam` §3 | Hayır |
| B9 | Uygulama iş mantığı | K001+ | `os-master.md` §2 | Hayır |

**Kritik ayrım (B5 vs A2):** bu dal süreci **kavram olarak** (durumlar, kimlik, fork vs
thread) anlatır; süreci **çalıştıran** zamanlayıcı çekirdeği, kuyruk iç yapısı ve
preemption mekanizması `-kernel` dalındadır. Aynı ayrım bellek için de geçerlidir:
sayfalama **kavramı** burada, sayfa tablosu yürüten kod `-kernel`'dedir.

### §A.3.1 Kapsam dışı — senaryo bazlı yönlendirme

Bir konu geldiğinde **hangi dala gideceğinin** karar tablosu (bu dalın yetkisi dışındakiler):

| # | Gelen konu | Yönlendirme | Gerekçe |
|---|---|---|---|
| S1 | `read()`/`write()` çağrısının imzası ve errno değeri | `-api` | Çağrı yüzeyi B1 |
| S2 | ALSA/WASAPI buffer doldurma döngüsü | `-audio` | Donanım erişimi B2 |
| S3 | ftrace ile trace noktası ekleme | `-observability` | Araç B3 |
| S4 | kselftest ile çekirdek testi yazma | `-testing` | Test altyapısı B4 |
| S5 | EEVDF kodunun nasıl çalıştığı | `-kernel` | Mekanizma B5 |
| S6 | Zamanlama politikası seçimi (nice/latency) | `-plan` | Politika B6 |
| S7 | Windows ↔ Linux API karşılaştırması | `-turu` | Platform ailesi B7 |
| S8 | `/proc/self/status` okunarak log üretme | `-observability` | Araç B3 |
| S9 | Ext4'ün journal iç yapısı | `K001-donanım` + `-kernel` | Disk/çekirdek |
| S10 | PHP'de `file_get_contents` çağrısı | K001+ (uygulama) | İş mantığı B9 |
| S11 | Sayfa tablosu kurma kodu | `-kernel` | Mekanizma (A.2 notu) |
| S12 | Capability bitinin çekirdekte denetlenmesi | `-security` + `-kernel` | §E N8/N11 |

**Yönlendirme kuralı:** konu *tarif* ediliyorsa bu dalda, *çağrılıyor/uygulanıyor* ise
komşudadır. Karar verilemiyorsa dosya **üretilmez**, `UNKNOWN` yazılır.

### §A.3.2 Kapsam genişletme yasağı

| # | Yasak | Sonuç |
|---|---|---|
| Y1 | Alt-dal dosyasına komşu konu (syscall/ses/gözlem/test) yazmak | Domain boundary ihlali → revert |
| Y2 | Kök dosyada yeni kavram icat etmek (os-master §3 dışı) | Hallüsinasyon → `⚠️ VERIFICATION REQUIRED` |
| Y3 | `children` listesinde olmayan dosya üretmek | Envanter dışı dosya → DUR |
| Y4 | Beyazliste dışı dizine yazmak | 7 paralel ajanla çakışma riski → yasak |
| Y5 | Q2/Q4'ü bu turda kapatmış gibi yazmak | Bilgi kaybı → açık soru olduğu gibi kalır |

### §A.4 Alt ayrım noktaları — Q2 ÇEKİRDEĞİ

| # | Ayrım | Tanım var mı? | Durum | Bu turun tutumu |
|---|---|---|---|---|
| C1 | `core` ↔ `kernel` | **YOK** | ⚠️ VERIFICATION REQUIRED (Q2) | Sınır genişletilmedi, yalnız kavram taşındı |
| C2 | Kavram ↔ mekanizma | kısmen — `os-master.md` §3 ayrımı | `[TASARIM]` | Kavram = bu dal, mekanizma = `-kernel` |
| C3 | Çekirdek ↔ userland | POSIX/OS literatüründe var | ✅ §1/7 | §A.6'da web ile doğrulandı |
| C4 | Platform-döngüsel ↔ platform-özel | yok — Q4 bağlı | UNKNOWN | Alt-dallar platform-yazısız yazıldı |
| C5 | Kavram ↔ uygulama yansıması | repo'da dolaylı kanıt var | ✅ `-ornekler.md` | Örnek dosyasında `Dosya:`/`Satır:` ile |

**C1, bu dalın en kritik belirsizliğidir.** İki dal da kapsamlarını **genişletmez**;
yalnızca doğrulanmış kavramları taşır. Sınır kararı Vault Steward'dadır.

### §A.5 Repo ile sınır (çekirdek kavramların uygulamadaki karşılığı)

| # | Kavram | Gerçek dosya : satır | OS hizmeti | Alt-dal |
|---|---|---|---|---|
| R1 | Geçici dizin (bellek/dosya) | `shared/src/Component/TemplateEngine.php:73` | `sys_get_temp_dir` | bellek |
| R2 | Dizin + POSIX izin modu | `shared/src/Session/SessionConfig.php:33-36` | `mkdir` · `umask` | izin |
| R3 | Süreç (alt süreç) | `shared/tests/Repository/ApiKeyCreateCommandTest.php:248-269` | `proc_open` | süreç |
| R4 | Paylaşımlı bellek | `shared/src/Cache/ApcuAdapter.php:9` | APCu uzantısı | bellek |
| R5 | Startup (zaman/hata) | `shared/src/Bootstrap/RuntimeBootstrap.php:1-21` | `ini_set` · tz | kök |
| R6 | Yol çözümleme (dosya) | `home.coremusic.net/include/Stream/MusicStreamHandler.php:185-215` | `realpath` | dosya |
| R7 | Platform tespiti | `shared/src/Api/Bff/DesktopBff.php:35` | `php_uname('s')` | Q4 kanıtı |
| R8 | Log yazımı | `shared/src/PageRouter/StructuredLogger.php:72` | `error_log` | `-observability` |

> **Dürüstlük notu:** CoreMusic'te çekirdek/bellek seviyesinde **doğrudan kod yoktur**;
> tablolar **dolaylı kanıttır** (PHP → libc → syscall zinciri, `os-bagimliliklar.md` §1 B13).

### §A.6 Web doğrulama matrisi

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| VFS, tüm dosya sistemi erişiminin tek soyutlama katmanıdır | https://docs.kernel.org/filesystems/vfs.html | 2026-10-13 | ✅ HTTP 200 |
| Dosya sistemi alt sisteminin resmi doküman dizini vardır | https://docs.kernel.org/filesystems/index.html | 2026-10-13 | ✅ HTTP 200 |
| inode, dosyanın çekirdekteki tanıtıcısıdır (mod, sahip, boyut) | https://man7.org/linux/man-pages/man7/inode.7.html | 2026-10-13 | ✅ HTTP 200 |
| Süreç/iş alt sisteminin resmi doküman dizini vardır | https://docs.kernel.org/process/index.html | 2026-10-13 | ✅ HTTP 200 |

Bu matris **sadece bu dosyanın** iddialarını kapsar; alt-dal dosyalarının URL'leri kendi
`Referanslar` bölümünde, kök `-dogrulama.md` §E ise tek çatı matrisindedir.

### §A.7 Disk kanıtları (bu dosyanın dayandığı yerler)

| # | İddia | Gerçek dosya yolu | Okundu mu? |
|---|---|---|---|
| E1 | Katman 1 = çekirdek (süreç, bellek, dosya, izin, ağ) | `os-master.md` §2 | ✅ 2026-10-13 |
| E2 | 8 çekirdek kavram tablosu | `os-master.md` §3 | ✅ 2026-10-13 |
| E3 | Açık soru Q2: `core` ↔ `kernel` sınırı | `os-master.md` §8 | ✅ 2026-10-13 |
| E4 | B13 bağımlılığı: tüm çağrılar PHP→libc→syscall | `os-bagimliliklar.md` §1 B13 | ✅ 2026-10-13 |
| E5 | Çocuk açık sorusu: `core` ↔ `kernel` ayrım tanımı yok | `isletim-sistemi-core/index.md` FM | ✅ 2026-10-13 |
| E6 | 4 alt-dal klasörü diskte var, her birinde 4 md | `isletim-sistemi-core/` glob | ✅ 2026-10-13 |
| E7 | Komşu dal adları (`-api` · `-audio` · `-observability` · `-testing`) | `K000-isletim-sistemi/` dizin listesi | ✅ 2026-10-13 |

### §A.8 Kavram-bazlı kapsam detayı (bu dalın 4 ana kavramı)

**1 — Süreç & zamanlama** (`surec-ve-zamanlama-kavramlari/`):

| Alt konu | Kapsamda mı? | Dosya |
|---|---|---|
| Süreç kimliği (PID/PPID), doğum/ölüm | Evet | `...-anayasa.md` §2 |
| `fork` vs `exec` vs `thread` ayrımı | Evet | `surec-ve-parcacik-ayrimi.md` §3 |
| Ready/running/blocked durum diyagramı | Evet | `zamanlama-durum-ve-gecisleri.md` §1-§2 |
| Öncelik ve adillik **kavramı** | Evet | `...-anayasa.md` §5 |
| Zombie/orphan/kurtarma kavramı | Evet | `...-anayasa.md` §6 |
| EEVDF/CFS algoritmasının kodu | Hayır → `-kernel` | §A.3 B5 |
| CFS ağırlık hesabı, tick süresi | Hayır → `-kernel` | §A.3 B5 |
| `SCHED_FIFO` politika seçimi | Hayır → `-plan` | §A.3 B6 |

**2 — Bellek** (`bellek-kavramlari/`):

| Alt konu | Kapsamda mı? | Dosya |
|---|---|---|
| Adres alanı kavramı (32/64-bit harita) | Evet | `...-anayasa.md` §2 |
| Sanal ↔ fiziksel eşleme **kavramı** | Evet | `...-anayasa.md` §4 |
| Sayfa tablosu/TLB/demand paging | Evet | `sanal-bellek-ve-sayfalama.md` §1-§4 |
| r/w/x koruma bayrakları | Evet | `bellek-koruma-ve-ayristirma.md` §1 |
| Stack/heap/Kod segmentleri | Evet | `...-anayasa.md` §6 |
| `mmap` çağrı imzası ve bayrakları | Hayır → `-api` | §A.3 B1 |
| Sayfa hatası yakalama kodu | Hayır → `-kernel` | §A.3 B5 |
| Swap politikası / OOM killer | Hayır → `-kernel` | §A.3 B5 |

**3 — Dosya sistemi** (`dosya-sistemi-kavramlari/`):

| Alt konu | Kapsamda mı? | Dosya |
|---|---|---|
| VFS soyutlama katmanı | Evet | `...-anayasa.md` §2 |
| inode/dizin/kök ilişkisi | Evet | `...-anayasa.md` §3 |
| Dosya tanıtıcısı (fd) ve offset paylaşımı | Evet | `vfss-ve-dosya-taniticisi.md` §2-§3 |
| Yol çözümleme sırası, `.`/`..`/symlink | Evet | `dizin-ve-yol-cozumleme.md` §2-§4 |
| Mount kavramı | Evet | `...-anayasa.md` §6 |
| `open(2)` bayrakları (`O_CREAT`...) | Hayır → `-api` | §A.3 B1 |
| NTFS/ext4 iç yapısı, journal | Hayır → `K001-donanım` | §A.3 S9 |
| Disk kuyruğu/IO scheduler | Hayır → `-kernel` | §A.3 B5 |

**4 — İzin & güvenlik** (`izin-ve-guvenlik-kavramlari/`):

| Alt konu | Kapsamda mı? | Dosya |
|---|---|---|
| user/group/other üçlüsü | Evet | `...-anayasa.md` §2 |
| rwx bitleri ve umask | Evet | `dosya-izinleri-ve-maske.md` §1-§2 |
| setuid/setgid/sticky | Evet | `dosya-izinleri-ve-maske.md` §3 |
| Capability kavramı (root parçalama) | Evet | `capability-ve-ayristirma-kavramlari.md` §1 |
| ACL ve sahiplik/devir | Evet | `...-anayasa.md` §4-§5 |
| seccomp filtresinin kodu | Hayır → `-security` | §A.3 B4 |
| LSM/SELinux politika yazımı | Hayır → `-security` | §A.3 B4 |
| Namespace'ın çekirdek uygulaması | Hayır → `-kernel` | §E N11 |

## §B — 4 alt-dal envanteri

**Ölçüm (2026-10-13):** `isletim-sistemi-core/` altında bu tur öncesi **4 dosya · 421 satır ·
≥500: 0 · `children: []` · alt dizin YOK**. Bu tur sonrası: **kök 4 md + 4 klasör × 4 md = 20 md**.

| # | Alt-dal (Kademe-3) | İçerik dosyası (Kademe-4) | Grup | Eşik |
|---|---|---|---|---|
| D1 | `surec-ve-zamanlama-kavramlari/` | `index.md` + 3 içerik | mimari | index 40-100 · içerik ≥500 |
| D2 | `bellek-kavramlari/` | `index.md` + 3 içerik | mimari | index 40-100 · içerik ≥500 |
| D3 | `dosya-sistemi-kavramlari/` | `index.md` + 3 içerik | mimari | index 40-100 · içerik ≥500 |
| D4 | `izin-ve-guvenlik-kavramlari/` | `index.md` + 3 içerik | mimari | index 40-100 · içerik ≥500 |

### §B.1 `surec-ve-zamanlama-kavramlari/`

| Dosya | Odak | § yapısı |
|---|---|---|
| `index.md` | Alt-dal künyesi, çocuk kataloğu, sınır notları | Künye · dosya indeksi · açık sorular · §GRUPLAR |
| `surec-ve-zamanlama-kavramlari-anayasa.md` | Bağlayıcı kavram tanımı: süreç, scheduler, durum diyagramı, adillik, zombie/orphan | §0-§10 |
| `surec-ve-parcacik-ayrimi.md` | Bellek/durum paylaşımı, göç vs çağrı, senkronizasyon, platform farkları | §0-§7 |
| `zamanlama-durum-ve-gecisleri.md` | Ready/running/blocked, geçişler, kuyruk seviyeleri, preemption, deadlock | §0-§7 |

### §B.2 `bellek-kavramlari/`

| Dosya | Odak | § yapısı |
|---|---|---|
| `index.md` | Alt-dal künyesi ve çocuk kataloğu | Künye · dosya indeksi · açık sorular · §GRUPLAR |
| `bellek-kavramlari-anayasa.md` | Adres alanı, sanal bellek, eşleme, koruma bayrakları, segmentler | §0-§10 |
| `sanal-bellek-ve-sayfalama.md` | Sayfa tablosu, TLB, demand paging, swap, büyük sayfalar | §0-§7 |
| `bellek-koruma-ve-ayristirma.md` | r/w/x, ASLR, stack guard, koruma ihlali ve sinyal | §0-§6 |

### §B.3 `dosya-sistemi-kavramlari/`

| Dosya | Odak | § yapısı |
|---|---|---|
| `index.md` | Alt-dal künyesi ve çocuk kataloğu | Künye · dosya indeksi · açık sorular · §GRUPLAR |
| `dosya-sistemi-kavramlari-anayasa.md` | VFS, inode/dizin/kök, dosya tanıtıcısı, yol çözümleme, mount | §0-§10 |
| `vfss-ve-dosya-taniticisi.md` | VFS katmanı, fd tablosu, paylaşılan offset, kalıcı vs geçici dosya | §0-§6 |
| `dizin-ve-yol-cozumleme.md` | Dizin girişi, symlink, `.`/`..`, çözümleme sırası, hata modları | §0-§6 |

### §B.4 `izin-ve-guvenlik-kavramlari/`

| Dosya | Odak | § yapısı |
|---|---|---|
| `index.md` | Alt-dal künyesi ve çocuk kataloğu | Künye · dosya indeksi · açık sorular · §GRUPLAR |
| `izin-ve-guvenlik-kavramlari-anayasa.md` | user/grup/other, capability, ACL, sahiplik ve devir | §0-§9 |
| `dosya-izinleri-ve-maske.md` | rwx bitleri, umask, setuid/setgid/sticky, dizin izinleri | §0-§6 |
| `capability-ve-ayristirma-kavramlari.md` | Root yerine capability, asgari yetki, namespace, uygulama↔OS sınırı | §0-§6 |

### §B.5 Kök 4 dosya (bu turda yeniden yazılanlar)

| Dosya | Eski satır | Yeni satır | Rol |
|---|---|---|---|
| `index.md` | 114 | 97 | Künye · 8 kavram · sınır · gruplar (children 4 alt-dal) |
| `isletim-sistemi-core-kapsam.md` | 93 | ≥500 | Sınır belgesi (bu dosya) |
| `isletim-sistemi-core-dogrulama.md` | 77 | ≥500 | Kanıt, eşik, URL matrisi |
| `isletim-sistemi-core-ornekler.md` | 137 | ≥500 | Repo örnekleri, kötü uygulama, karşılaştırma |

### §B.6 Frontmatter şeması (bu turda HER dosyada zorunlu — v2 §4.2 + `group`)

| # | Alan | İçerik | Kök index | Alt-dal index | İçerik dosyası |
|---|---|---|---|---|---|
| 1 | `node_id` | dosya yolu · uzantısız · `/` | klasör yolu (korundu) | tam yol | tam yol |
| 2 | `node_type` | `subdomain` / `module` | `subdomain` | `subdomain` | `module` |
| 3 | `status` | `draft` (Q2/Q4 açık) | `draft` | `draft` | `draft` |
| 4 | `parent_id` | üst düğüm | `K000-isletim-sistemi` (korundu) | `.../isletim-sistemi-core` | dalın `index.md`'si |
| 5 | `children` | çocuk listesi | 4 alt-dal (satır içi) | o dalın 3 dosyası | `[]` |
| 6 | `depends_on` | bağımlılık | `[]` | `[]` | `[]` |
| 7 | `evidence` | URL/dosya kanıtı | 4 URL | dal URL'leri | konu URL'leri |
| 8 | `validation` | tarih + ölçüm | 2026-10-13 | 2026-10-13 | 2026-10-13 |
| 9 | `open_questions` | açık soru | Q2 · kavram seti · Q4 | dal özel soru | `[]` veya konu sorusu |
| 10 | `group` | **tirnaksız** | `mimari` | `mimari` | anayasa/konu → `mimari` |

**Yasaklar:** `parent:` (şema dışı) · `group: "mimari"` (tırnak) · FM dışı `group` ·
eksik liste alanı (boş olsa bile `[]`). Ölçüm: `parent:` = 0 · tırnaklı `group` = 0 ·
FM kesik = 0.

### §B.7 Dosya adlandırma ve kademe kuralı

| # | Kural | Uygulama |
|---|---|---|
| K1 | Klasör adı = konu adı, `kebab-case` | `surec-ve-zamanlama-kavramlari/` |
| K2 | İçerik dosyası = `{dal-adi}-{konu}.md` | `bellek-kavramlari-anayasa.md` |
| K3 | Anayasa dosyası her dalda **ilk** sırada | `{dal}-anayasa.md` |
| K4 | Kademe-3 klasör, dosyalar Kademe-4 | başlık `Deep-Dal İndeksi (Kademe-4)` |
| K5 | Mevcut dosya adı **değiştirilmez** | 0 dosya adı değişikliği |
| K6 | Uzantısız `node_id` = dosya yolu | `.../bellek-kavramlari/bellek-kavramlari-anayasa` |

## §C — 8 kavramın dağılım haritası

`os-master.md` §3'teki 8 kavramın **hangisi bu dalda, hangisi komşuda** olduğu:

| # | Kavram | Konum | Sahibi | Bu tur |
|---|---|---|---|---|
| 1 | Süreç + zaman paylaşımı | Bu dal | `surec-ve-zamanlama-kavramlari/` | ✅ alt-dal AÇILDI |
| 2 | Bellek yönetimi | Bu dal | `bellek-kavramlari/` | ✅ alt-dal AÇILDI |
| 3 | Dosya sistemi (VFS) | Bu dal | `dosya-sistemi-kavramlari/` | ✅ alt-dal AÇILDI |
| 4 | Sistem çağrıları | Komşu | `isletim-sistemi-api` | ❌ dokunulmadı |
| 5 | İzin + güvenlik | Bu dal | `izin-ve-guvenlik-kavramlari/` | ✅ alt-dal AÇILDI |
| 6 | Gözlemlenebilirlik | Komşu | `isletim-sistemi-observability` | ❌ dokunulmadı |
| 7 | Test | Komşu | `isletim-sistemi-testing` | ❌ dokunulmadı |
| 8 | Ses donanımı | Komşu | `isletim-sistemi-audio` | ❌ dokunulmadı |

### §C.1 Haritanın dosya karşılığı

| # | Kavram | Bu dalda hangi dosya | Komşuda hangi dosya |
|---|---|---|---|
| 1 | Süreç | `surec-ve-zamanlama-kavramlari-anayasa.md` §2 | — |
| 1a | Zamanlama kavramı | `zamanlama-durum-ve-gecisleri.md` §1-§4 | politika → `-plan` |
| 2 | Bellek | `bellek-kavramlari-anayasa.md` §2-§6 | mekanizma → `-kernel` |
| 2a | Sanal bellek | `sanal-bellek-ve-sayfalama.md` §1-§5 | — |
| 3 | Dosya sistemi | `dosya-sistemi-kavramlari-anayasa.md` §2-§6 | syscall → `-api` |
| 3a | Dizin/yol | `dizin-ve-yol-cozumleme.md` §1-§5 | — |
| 5 | İzin | `izin-ve-guvenlik-kavramlari-anayasa.md` §2-§5 | uygulaması → `-security` |
| 5a | Capability | `capability-ve-ayristirma-kavramlari.md` §1-§4 | çekirdek uygulaması → `-security` |
| 4 | Syscall | yok (kapsam dışı) | `isletim-sistemi-api/` |
| 6 | Gözlem | yok (kapsam dışı) | `isletim-sistemi-observability/` |
| 7 | Test | yok (kapsam dışı) | `isletim-sistemi-testing/` |
| 8 | Ses | yok (kapsam dışı) | `isletim-sistemi-audio/` |

**Sonuç (Q-C2 için bu turun cevabı — kısmi):** 8 kavramın **4'ü bu dalda 4 alt-dala
dağıtıldı**, **4'ü komşuda** kaldı. 8'inin de tek dalda toplanması **kararı verilmedi**;
Q-C2 Vault Steward'da açık kalır.

### §C.2 Komşu dal envanteri (yalnız ad ve kök dosya — içerik okunmadı)

| # | Komşu dal | Kök dosyaları (disk) | Bu haritadaki payı |
|---|---|---|---|
| 1 | `isletim-sistemi-api/` | `index.md` · `-kapsam` · `-dogrulama` · `-ornekler` (+ `ag-api/`) | kavram 4 |
| 2 | `isletim-sistemi-observability/` | `index.md` · `-kapsam` · `-dogrulama` · `-ornekler` | kavram 6 |
| 3 | `isletim-sistemi-testing/` | `index.md` · `-kapsam` · `-dogrulama` · `-ornekler` | kavram 7 |
| 4 | `isletim-sistemi-audio/` | `index.md` · `-kapsam` · `-dogrulama` · `-ornekler` | kavram 8 |
| 5 | `isletim-sistemi-kernel/` | `index.md` · `-kapsam` · `-dogrulama` · `-ornekler` | mekanizma sınırı |
| 6 | `isletim-sistemi-plan/` | `index.md` · `-kapsam` · `-dogrulama` · `-ornekler` | politika sınırı |
| 7 | `isletim-sistemi-security/` | `index.md` · `-kapsam` · `-dogrulama` · `-ornekler` | uygulama sınırı |
| 8 | `isletim-sistemi-runtime/` · `-cross` · `-turu` | kök 4 dosya (her biri) | katman 4/5/0 |

> Bu tablo **envanteridir, okuma değildir**: komşu dosyaların içeriği bu turda
> **okunmadı ve yazılmadı** (beyazliste ihlali olurdu). Yalnız dizin adları `glob` ile
> doğrulandı (2026-10-13).

## §D — Bağımlılık grafiği

```text
os-master.md §2-§3  (katman 1 — 8 kavram)
        │
        ▼
isletim-sistemi-core/index.md  (kök künye · children: 4)
        │
        ├──► surec-ve-zamanlama-kavramlari/  ──► komşu: -plan (politika), -kernel (mekanizma)
        ├──► bellek-kavramlari/              ──► komşu: -kernel (sayfalama kodu), -api (mmap yüzeyi)
        ├──► dosya-sistemi-kavramlari/       ──► komşu: -api (open/read), K001-donanım (disk)
        └──► izin-ve-guvenlik-kavramlari/    ──► komşu: -security (uygulama), -kernel (capability çekirdeği)
```

| # | Kenar | Tür | Yön |
|---|---|---|---|
| G1 | `os-master.md` → kök `index.md` | referans | yukarıdan aşağı |
| G2 | kök `index.md` → 4 alt-dal | `children` | yukarıdan aşağı |
| G3 | 4 alt-dal → kök `-kapsam.md` §B | envanter | aşağıdan yukarı |
| G4 | 4 alt-dal → kök `-dogrulama.md` §E | kanıt | aşağıdan yukarı |
| G5 | 4 alt-dal → kök `-ornekler.md` §B | repo örnek | aşağıdan yukarı |
| G6 | `bellek-kavramlari` → `isletim-sistemi-api` | sınır | yan |
| G7 | `surec-ve-zamanlama` → `isletim-sistemi-plan` | sınır | yan |
| G8 | `surec-ve-zamanlama` → `isletim-sistemi-kernel` | sınır | yan |
| G9 | `dosya-sistemi-kavramlari` → `isletim-sistemi-api` | sınır | yan |
| G10 | `izin-ve-guvenlik-kavramlari` → `isletim-sistemi-security` | sınır | yan |
| G11 | `izin-ve-guvenlik-kavramlari` → `isletim-sistemi-kernel` | sınır | yan |
| G12 | `dosya-sistemi-kavramlari` → `K001-donanım` | sınır (disk) | dış |
| G13 | kök `-ornekler.md` → repo PHP dosyaları | dolaylı kanıt | dış |
| G14 | kök `-dogrulama.md` → resmi URL'ler | web kanıtı | dış |

**Yük kuralı:** oklar yukarıdan aşağı **kavram**, aşağıdan yukarı **kanıt** taşır;
yan oklar **sınır bildirimidir**, kod/üretim taşımaz. Bu turda tek yönlü genişleme
`index.md` → 4 alt-dal olarak gerçekleşti (G2).

### §D.1 Katman ↔ alt-dal eşlemesi

| Katman (`os-master.md` §2) | Bu dalda karşılığı | Yok |
|---|---|---|
| 1 — Çekirdek (süreç·bellek·dosya·iz | 4 alt-dal | — |
| 2 — Kullanıcı↔kernel sınırı | yok | `-api` · `-security` |
| 3 — C kütüphanesi (libc) | yok | `-api` |
| 4 — PHP runtime + SAPI | yok | `-runtime` |
| 5 — Uygulama | yok | `-cross` · `-observability` |
| 0 — Sürücüler + donanım | yok | `-audio` · `-turu` |

### §D.2 Graf kuralları (bu tur için bağlayıcı)

| # | Kural | Uygulanan yer |
|---|---|---|
| R1 | `children` = diskte **gerçekten var** olan dosyalar | kök index 4 satır · 4 index × 3 satır |
| R2 | `depends_on` = üst/alt dal değil, **bilgi bağımlılığı** | her dosyada `[]` (bu tur bilgi bağımlılığı eklemedi) |
| R3 | Yan kenarlar (G6-G12) **ad** taşır, dosya değil | §E tablosu |
| R4 | Aşağı akış = kanıt, yukarı akış = kavram | G3-G5 |
| R5 | Graf tek sayfada kapanır (kök `index.md` §4) | kök index §4 + §GRUPLAR |
| R6 | Döngü (cycle) yasak — `core` ↔ `kernel` sınırı döngü riskidir | Q-C1 açık → kenar çizilmedi |

## §E — Sınır komşuları

| # | Komşu dal | Bu dalın ona **vermediği** | Bu dalın ondan **almadığı** | Kaynak |
|---|---|---|---|---|
| N1 | `isletim-sistemi-api` | syscall yüzeyinin kendisi, libc sarmalayıcıları | — (kavram tek başına yeterli) | kök `index.md` §4 |
| N2 | `isletim-sistemi-kernel` | yürütme, preemption, sürücü, sayfalama kodu | mekanizma tanımı (`C2`) | kök `index.md` §4 |
| N3 | `isletim-sistemi-plan` | zamanlama **politikası** | — | `os-master.md` §8 Q3 |
| N4 | `isletim-sistemi-observability` | ftrace/ETW/perf araçları | — | kök `index.md` §1/6 |
| N5 | `isletim-sistemi-testing` | kselftest/KUnit/PHPUnit | — | kök `index.md` §1/7 |
| N6 | `isletim-sistemi-audio` | WASAPI/ALSA/CoreAudio | — | kök `index.md` §1/8 |
| N7 | `isletim-sistemi-turu` | platform ailesi karşılaştırması | — | kök `index.md` §4 |
| N8 | `isletim-sistemi-security` | capability'nin çekirdekte **uygulanması** | güvenlik politikası çerçevesi | `os-master.md` §3.5 |
| N9 | `K001-donanım` | disk/sürücü donanım ayrıntısı | — | `os-master.md` §2 katman 0 |
| N10 | `K001+` uygulama | iş mantığı | repo örnek kanıtı | `os-master.md` §2 katman 5 |

**Çakışma kuralı:** iki dal aynı kavramı **kendi ağzından** yazamaz; kavramı tarif eden
dal burası, **çalıştıran** dal `-kernel`/`-api`'dir. Çelişki çıkarsa `UNKNOWN` işaretlenir
ve Vault Steward'a gider (Q-C1).

### §E.1 Ortak sınır durumları (iki dala birden değen konular)

| # | Konu | Tarif eden | Uygulayan | Çözüm |
|---|---|---|---|---|
| O1 | Dosya tanıtıcısı (fd) | bu dal (`vfss-ve-dosya-taniticisi.md` §2) | `-api` (`dup`/`fcntl` yüzeyi) | Kavram burada, çağrı orada |
| O2 | Paylaşılan offset | bu dal (§3) | `-api` (`lseek`) | Aynı ayrım |
| O3 | Capability | bu dal (`capability-...md` §1) | `-security` (politika) + `-kernel` (denetim) | Üç katman, tek tanım |
| O4 | Zamanlama önceliği | bu dal (`...-anayasa.md` §5) | `-plan` (seçim) + `-kernel` (uygulama) | Kavram/seçim/uygulama ayrımı |
| O5 | Bellek koruma bayrağı | bu dal (`bellek-koruma-...md` §1) | `-api` (`mprotect`) + `-kernel` (sayfa) | Kavram burada |
| O6 | Sembolik bağlantı | bu dal (`dizin-ve-yol-cozumleme.md` §2) | `-api` (`readlink`) + uygulama (repo `is_link`) | Kavram + repo kanıtı |
| O7 | Umask | bu dal (`dosya-izinleri-ve-maske.md` §2) | `-api` (`umask(2)`) | Kavram burada |
| O8 | Zombie kurtarma | bu dal (`...-anayasa.md` §6) | `-api` (`waitpid`) + uygulama | Kavram burada |

### §E.2 Bu turda **yazılmayan** yüzeyler (kanıt)

| # | Yüzey | Ölçüm (2026-10-13) |
|---|---|---|
| W1 | Beyazliste dışı dizine yazılan dosya | 0 |
| W2 | `isletim-sistemi-{cross,kernel,observability,plan,runtime,security,testing}/**` | 0 (yalnız `glob` okundu) |
| W3 | `isletim-sistemi-{audio,api,turu}/**` · `electroncis/**` | 0 |
| W4 | `K000-isletim-sistemi/index.md` · `os-*.md` | 0 |
| W5 | `.ai/architecture/index.md` · `.ai/.decisions/**` · `.ai/electronic/**` | 0 |
| W6 | `*.php` `*.js` `*.css` `*.sql` | 0 |
| W7 | Dosya/dizin adı değişikliği | 0 |
| W8 | `git commit` | 0 (subagent commit atmaz) |

## §F — Açık sorular

| # | Soru | Etiket | Sahip | Bu turda kapandı mı? |
|---|---|---|---|---|
| Q-C1 | `core` ↔ `kernel` sınırı (Q2)? | ⚠️ VERIFICATION REQUIRED | Vault Steward | Hayır |
| Q-C2 | 8 kavramın tamamı bu dalın mı, parçaları child'lara mı dağıtıldı? | ⚠️ VERIFICATION REQUIRED | Vault Steward | Kısmen — §C 4+4 dağılımı **kanıtlandı**, karar değil |
| Q-C3 | Hedef platform çekirdeği (Q4)? | UNKNOWN | Vault Steward | Hayır — alt-dallar platform-yazısız yazıldı |
| Q-C4 | `electroncis` bu dala komşu mu (Q1)? | UNKNOWN | Vault Steward | Hayır |
| Q-C5 | `isletim-sistemi-plan` = scheduling mi, plan dokümanı mı (Q3)? | ⚠️ VERIFICATION REQUIRED | Vault Steward | Hayır — G7 kenarı **sınır bildirimi** olarak kaldı |
| Q-C6 | 4 alt-dalın derinlik eşiği (≥500) sonrası içerik tekrarı var mı? | `[TASARIM]` | Vault Steward | Bu turda karşılaştırma `-ornekler.md` §D'ye taşındı |

> **Q2/Q4 kapalı değildir.** Bu tur yalnız **kavram yerleşimini** kanıtladı (§C);
> `core`↔`kernel` sınır tanımı ve hedef platform kararı hâlâ Vault Steward'dadır —
> kök `index.md` FM `open_questions` alanında **olduğu gibi** durmaktadır.

---

## §A.9 Mekân Kavramları — Dosya ve Bellek Sınırları

`core/` dört alt-dalın mekânsal sınırları:

| Alt-dal | Mekân | Birim | Kavramsal Sınır |
|---|---|---|---|
| `surec-ve-zamanlama-kavramlari/` | CPU zamanı | nanosaniye | Thread'ler CPU'yu paylaşır; sıralama politikası belirler |
| `bellek-kavramlari/` | sanal adres alanı | bayt | Her sürecin kendi adres uzayı; sayfa tablosu eşlemesi |
| `dosya-sistemi-kavramlari/` | kalıcı depolama | bayt / blok | Dizin ağacı; inode → veri blokları; fsync ↔ power-loss |
| `izin-ve-guvenlik-kavramlari/` | korunan kaynaklar | yetki biti | UID/GID + mode bitleri + capability; ACL uzantısı |

Kesişim noktaları (tek dosyanın yetki alanı dışına taşan kavramlar):

| Kesişim | İlgili dosyalar | Kapsam |
|---|---|---|
| `mmap` | `bellek-kavramlari` + `dosya-sistemi-kavramlari` | Dosyayı sanal adres alanına eşleme; sayfa hatası → disk I/O |
| FIFO / `pipe` | `dosya-sistemi-kavramlari` + `surec-ve-zamanlama-kavramlari` | Adlandırılmış kanal; yazan uyur, okuyan uyandırır |
| `O_NOFOLLOW` / symlink | `dosya-sistemi-kavramlari` + `izin-ve-guvenlik-kavramlari` | TOCTOU; `is_link` kontrolü |
| Zombie / `waitpid` | `surec-ve-zamanlama-kavramlari` + `izin-ve-guvenlik-kavramlari` | Yalnız parent okuyabilir; yetki sınırı process hiyerarşisinde |

Kesişim kavramları ilgili alt-dal dosyalarında da işlenir; `core/` kök dosyaları
tek başına derinleşmez, yalnız sınırı ve yönlendirmeyi tanımlar (bkz. §1 ayrıştırma notu).

---

## §A.10 Gerçekçilik Düzeyleri ve Kapsam Sınırı

`os-master.md` §A.4-§A.8 beş "gerçekçilik düzeyi" tanımlar (ELI10'dan metal'e).
`core/` bu düzeylerin **hiçbiriyle birebir örtüşmez** — dört kavram grubunu
düzeyler-arası sabit çekirdek olarak ele alır:

| Kavram grubu | ELI10'da | Metal'de | `core/` konumu |
|---|---|---|---|
| zamanlama | "işletim sistemi sıraya koyar" | CFS, hedef vruntime, EEVDF | §A.5 — mekanizma/policy ayrımı sabit |
| bellek | "kendine yer ayırır" | sayfa tablosu, TLB, swap | §A.6 — fiziksel/virtual ayrımı sabit |
| dosya | "dosyalar klasörlerde durur" | inode, VFS, dentry cache | §A.7 — path → inode zinciri sabit |
| izin | "bazı dosyalara sadece sen bakarsın" | LSM, capability, DAC + MAC | §A.8 — UID/GID + mode + capability çekirdeği |

Kapsam dışı (bu `core/` dalında işlenmez, ilgili başka dalda):

| Konu | Nerede işlenir |
|---|---|
| `isletim-sistemi-cross-*` — platformlar-arası soyutlama | `K000-isletim-sistemi/isletim-sistemi-cross-*` |
| `isletim-sistemi-kernel-*` — çekirdek içi veri yapıları | `K000-isletim-sistemi/isletim-sistemi-kernel-*` |
| `isletim-sistemi-security-*` — güvenlik denetimi (OWASP vb.) | `K000-isletim-sistemi/isletim-sistemi-security-*` |
| PHP'ye özgü akış (session, cache, router) | `K000-*` PHP dalları (ör. `K003-veri-ve-depolama`) |
| Donanım/PCB/BOM | `.ai/electronic/**` (farklı uzmanlık alanı) |

---

## §G Ek Web Matrisi ve Kod Kanıtı (2026-10-13 · bu tur)

### §G.1 Yeni URL tablosu (envanter kesişimi 0)

> §A.6 bu dosyanın ilk web matrisidir; §G.1 **bu turda** envanterde olmayan (`.ai/**` 2730 ·
> `.ai/architecture/**` 2325 kümesine karşı kesişim 0) beş URL'yi ekler. Hepsi 2026-10-13'te
> `Invoke-WebRequest` ile HTTP 200 döndürmüştür.

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| `/proc` dosya sistemi, süreçlere ilişkin bilgiyi dosya arayüzü üzerinden sunar (Linux) | https://www.man7.org/linux/man-pages/man5/proc.5.html | 2026-10-13 | ✓ HTTP 200 |
| `credentials(7)`, süreç kimliklerinin (UID/GID/set*) semantiğini tanımlar | https://www.man7.org/linux/man-pages/man7/credentials.7.html | 2026-10-13 | ✓ HTTP 200 |
| `environ(7)`, süreç ortam değişkenlerinin API ile erişimini tanımlar | https://www.man7.org/linux/man-pages/man7/environ.7.html | 2026-10-13 | ✓ HTTP 200 |
| `setenv(3)`, ortam değişkeni tanımlama fonksiyonunun POSIX sözleşmesidir | https://www.man7.org/linux/man-pages/man3/setenv.3.html | 2026-10-13 | ✓ HTTP 200 |
| `sysinfo(2)`, sistem geneli bellek/yük istatistiklerini tek çağrıda okur | https://www.man7.org/linux/man-pages/man2/sysinfo.2.html | 2026-10-13 | ✓ HTTP 200 |

### §G.2 Repo kod kanıtı (ReadAllLines ile doğrulanan satırlar)

| # | Dosya | Satır | Okunan içerik (özet) | Kapsamla ilişkisi |
|---|---|---|---|---|
| R1 | `shared/src/Session/SessionConfig.php` | 33 | `ini_get('session.save_path') ?: sys_get_temp_dir() . '/coremusic_sessions'` | §A.5 — dosya/bellek sınırı: yol OS'den sorulur |
| R2 | `shared/src/Session/SessionConfig.php` | 35 | `@mkdir($savePath, 0750, true)` | §A.5 — izin: POSIX erişim modu `0750` |
| R3 | `shared/src/Cache/ApcuAdapter.php` | 13 | `apcu_fetch($key, $success)` | §A.5 — paylaşımlı bellek (süreçler-arası) |

> Repo'da çekirdek kodu YOK — OS seviyesi, kaynak: upstream docs. §G.2 satırları
> kavramların PHP sargısıdır; çekirdek içi implementasyon iddiası taşımaz.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
