---
node_id: K000-isletim-sistemi/isletim-sistemi-kernel/cekirdek-yonetim/cekirdek-yonetim-surucu
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-kernel/cekirdek-yonetim
children: []
depends_on: []
evidence:
  - https://docs.kernel.org/scheduler/index.html
  - https://docs.kernel.org/scheduler/sched-stats.html
  - https://docs.kernel.org/scheduler/sched-bwc.html
  - https://docs.kernel.org/mm/index.html
  - https://docs.kernel.org/admin-guide/mm/pagemap.html
  - https://docs.kernel.org/trace/histogram.html
  - https://man7.org/linux/man-pages/man2/clone.2.html
  - https://man7.org/linux/man-pages/man2/wait.2.html
  - https://man7.org/linux/man-pages/man2/pidfd_open.2.html
  - https://man7.org/linux/man-pages/man2/exit.2.html
validation: "2026-10-13 · repo satırları grep ile birebir (proc_open:253 · shell_exec:295 · usleep:117 · mkdir:35) · repo'da context-switch çekirdek kodu YOK (grep 0)"
open_questions:
  - "core ↔ kernel sınırı (Q2) → ⚠️ VERIFICATION REQUIRED"
  - "Hedef çekirdek (Q4) → UNKNOWN"
  - "Context-switch/jitter ölçüm sonucu → PLANNED (ölçülmedi)"
group: mimari
---

# cekirdek-yonetim-surucu — Sistem Durumu, Ölçüm ve Risk (Kademe-4 · §5-§9)

## §0 ELI15

İşletim sistemi bir anda **tek bir işi** çalıştırıyormuş gibi görünür, ama CPU
gerçekte işler arasında çok hızlı döner. Bu dosya "o döngü nasıl işliyor?"
sorusunun cevabıdır: süreçlerin **durum** makinesi, CPU'nun bir işten diğerine
**bağlam değiştirme** (context switch) anı, bunun nasıl **ölçüleceği** ve nerede
**risk** taşıdığı. Anayasa (`cekirdek-yonetim-anayasa.md` §0-§4) mekanizmayı
(tablolar, sayfa, halka, modeller) kurdu; bu dosya onun **çalışma anını** —
durum, geçiş, ölçüm, risk — sürer. Bağlayıcıdır: `-ornekler` bu dosyadaki
durum/geçiş tablolarına dayanır.

> **Repo'da çekirdek kodu YOK — OS seviyesi, kaynak: upstream docs.** CoreMusic
> bir PHP 8.x uygulamasıdır; `*.cpp`/`*.h` = 0 (L0 altyapısı PLANNED). §7'deki
> repo satırları **dolaylı** kanıttır (PHP → libc → syscall); bağlam değiştirme
> kodunun kendisi repoda **bulunmaz** (§7.4 dürüstlük kaydı).

| Öğe | Değer |
|---|---|
| Dosya rolü | Sürüm/ölçüm/risk omurgası (Kademe-4) |
| Bölüm omurgası | §5-§9 (bu dosya) · §0-§4 (`-anayasa`) · §1-§6 (`-ornekler`) |
| Bağlı dosyalar | `cekirdek-yonetim-anayasa.md` · `cekirdek-yonetim-ornekler.md` · kök `index.md` §1 |
| Kapsam | İşlev 2 (zamanlar) + 3 (bellek) mekanizmasının çalışma anı |
| Kapsam dışı | Zamanlama politikası (`-plan`) · güvenlik (`-security`) · gözlem (`-observability`) |

## §5 Sistem durumu ve bağlam değiştirme

### §5.1 Süreç durum makinesi (doktrin)

| # | Durum | Ne demek | Çıkış kapısı | Giriş nedeni |
|---|---|---|---|---|
| 5.1.1 | çalışır (running) | CPU'da ya da çalıştırılabilir | zamanlayıcı çağrısı / bitiş | planlayıcı seçti |
| 5.1.2 | çalıştırılabilir (runnable) | hazırdır, CPU bekler | zamanlayıcı seçer | `fork`/`clone` / olay bitti |
| 5.1.3 | bekliyor (waiting) | bir olay (G/Ç · sinyal · çocuk) bekler | olay gelir / sinyal | `read` blok / `usleep` |
| 5.1.4 | durdurulmuş (stopped) | dış sinyalle askıda | devam sinyali | `SIGSTOP` benzeri |
| 5.1.5 | terk/zombie | bitmiş, kayıt okunmamış | ebeveyn `wait(2)` | süreç `exit` yaptı |

```text
[fork/clone] --> [runnable] <--\\                         (zombie)
     |              |          |                            ^
     |              v          | olay bekleme               | exit (wait okunmadı)
     |          [running] ---->+--> [waiting] --> [runnable]|
     |              |                     |                 |
     |         zamanlayıcı           sinyal/uyandırma       |
     |              |                     |                 |
     +--------------+---------------------+-----------> [zombie] --> [temiz] (wait)
```

> `wait(2)` zinciri ve zombie temizliği upstream: `man7 wait.2` ✅ (HEAD 200) ·
> `man7 exit.2` ✅. Zombie, §5.6 risk tablosunda tekrar işaretlenir.

### §5.2 Durum geçişleri — tetikleyici tablosu

| # | Kaynak durum | Tetikleyici | Hedef durum | Çekirdek çağrısı |
|---|---|---|---|---|
| 5.2.1 | (yok) | süreç yaratma | runnable | `clone(2)` ✅ |
| 5.2.2 | runnable | planlayıcı seçimi | running | zamanlayıcı (EEVDF/CFS) |
| 5.2.3 | running | kuantum doldu | runnable | zamanlayıcı kesmesi |
| 5.2.4 | running | G/Ç bloklama | waiting | `read`/`usleep` → `nanosleep` |
| 5.2.5 | running | bitiş | zombie | `exit(2)` ✅ |
| 5.2.6 | waiting | olay/sinyal | runnable | olay uyanışı |
| 5.2.7 | zombie | ebeveyn okuma | (yok) | `wait(2)` ✅ |
| 5.2.8 | running | durdurma sinyali | stopped | sinyal (kesme dalı) |

> 5.2.4 satırındaki `nanosleep`, repo `usleep:117` ile §7.2'de eşlenir;
> 5.2.1/5.2.5 satırları repo `proc_open:253`/`shell_exec:295` ile ilişkilidir.

### §5.3 Bağlam değiştirme (context switch) — adımlar

| # | Adım | Ne taşınır / değişir | Kaynak |
|---|---|---|---|
| 5.3.1 | kullanıcı kayıtları askıya alınır | program sayacı + yığın imleci | anayasa §6.20 |
| 5.3.2 | adres uzayı değişir | sayfa tablosu kökü (TLB kirlenir) | `mm/index.html` ✅ |
| 5.3.3 | çalışma zamanı değişir | zamanlama varlığı (EEVDF/CFS) | `scheduler/index.html` ✅ |
| 5.3.4 | kesme maske durumu | süreç ait değildir (CPU'ya ait) | anayasa §6.19-§6.20 |
| 5.3.5 | yeni süreç geri yüklenir | kayıtlar + sayfa tablosu | `clone(2)` ✅ |

> **Taşınan/taşınmaz ayrımı** anayasa §6.19-§6.20'de kuruldu; bu dosya tekrar
> etmez (kopya yasağı · D6). Özet: yalnız süreç ait olan durum taşınır; kesme
> ve zamanlayıcı durumu CPU'ya aittir.

### §5.4 Zamanlama mekanizması (EEVDF / CFS — mekanizma, politika `-plan`'da)

| # | Kavram | Ne yapar | Kaynak |
|---|---|---|---|
| 5.4.1 | EEVDF | erken/geç teslim hedefli zamanlama (güncel) | `scheduler/index.html` ✅ |
| 5.4.2 | CFS | ağırlıklı en-adalet-önce (kalıtım) | `scheduler/index.html` ✅ |
| 5.4.3 | bandwidth | CFS ağırlık/payı (quota/period) | `scheduler/sched-bwc.html` ✅ |
| 5.4.4 | nice tasarımı | öncelik ↔ ağırlık dönüşümü | `scheduler/sched-nice-design.html` ✅ |
| 5.4.5 | RT grup zamanlama | gerçek-zamanlı bandwidth sınırı | `scheduler/sched-rt-group.html` ✅ |
| 5.4.6 | zamanlama varlığı | birimi temsil eden çekirdek yapısı | `scheduler/index.html` ✅ |

> **Sınır:** bu dosya yalnız **mekanizmayı** taşır; hangi sınıfın/önceliğin
> seçileceği (politika) `isletim-sistemi-plan` dalındadır (kök `index.md` §4).

### §5.5 Kullanıcı ↔ çekirdek bağlamı (özet — kapı anayasa'ya)

| # | Özellik | Kullanıcı bağlamı | Çekirdek bağlamı |
|---|---|---|---|
| 5.5.1 | erişim | yalnız syscall ile | doğrudan (Ring 0) |
| 5.5.2 | bellek | kendi adres alanı | çekirdek adres alanı |
| 5.5.3 | kesme | maskeli/erişimsiz | yönetimi (kesme dalı) |

> Tam tablo anayasa §6.19'dadır (kopya yasağı); burada yalnız kapı gösterilir.

### §5.6 Bağlam değiştirme riskleri

| # | Risk | Kaynağı | Belirti | Sınıf |
|---|---|---|---|---|
| 5.6.1 | geçiş maliyeti belirsizliği | TLB/öngörücü kirlenmesi | öngörülemeyen gecikme | ⚠️ ölçülmedi |
| 5.6.2 | zombie sızıntısı | `wait(2)` çağrılmazsa | kayıt birikir | doktrin (§5.1.5) |
| 5.6.3 | öncelik tersliği | düşük öncelikli kilit sahibi | yüksek öncelikli bekler | kesme dalı §3 |
| 5.6.4 | TLB shootdown | çekirdekler arası TLB geçersizleştirme | IPI ile gecikme | ⚠️ ölçülmedi |
| 5.6.5 | OOM / askıya alma | bellek bitişi | süreç öldürme | anayasa §6.8 |

> 5.6.1 ve 5.6.4 **ölçülmemiştir** — kesin gecikme/TLB rakamı yazılmaz
> (uydurma yasak). Ölçüm kapıları §6'dadır.

### §5.7 Platform karşılaştırması (bağlam değiştirme — çekirdek bağımsız)

| # | Yön | Linux | Windows | macOS (XNU) |
|---|---|---|---|---|
| 5.7.1 | süreç yaratma | `clone(2)` ✅ | `CreateProcessW` ✅ | `posix_spawn`/`fork` ⚠️ |
| 5.7.2 | bitiş + bekleme | `exit(2)`+`wait(2)` ✅ | süreç nesnesi + wait ✅ | POSIX ⚠️ |
| 5.7.3 | süreç kimlik dosyası | `pidfd_open(2)` ✅ | handle modeli | ⚠️ |

> 5.7.3'ün macOS sütunu bu turda **HEAD ile doğrulanmadı** → ⚠️; tek başına
> iddia olarak kullanılmaz (XNU dokümanı §E.3'te elenmişti). Linux/Windows
> sütunları §9 kaynaklıdır.

## §6 Ölçüm (context switch · jitter · sayaçlar)

### §6.1 Ölçüm arayüzleri (kapsam sözleşmesi — bu turda çalıştırılmadı)

| # | Arayüz | Ne ölçer | Kaynak | Durum |
|---|---|---|---|---|
| 6.1.1 | schedstats | zamanlama sayaçları (gecikme · çalıştırma) | `scheduler/sched-stats.html` ✅ | PLANNED |
| 6.1.2 | `/proc/<pid>/sched` | süreç başına zamanlama verisi | `scheduler/index.html` ✅ | PLANNED |
| 6.1.3 | pagemap | süreç sayfa tablosu eşlemesi | `admin-guide/mm/pagemap.html` ✅ | PLANNED (admin) |
| 6.1.4 | ftrace histogram | olay/bağlam-sayacı dağıtımı | `trace/histogram.html` ✅ | PLANNED |
| 6.1.5 | mm sayaçları | sayfa · baskı · geri çağırma | `mm/index.html` ✅ | PLANNED |

> **Durum:** bu tablodaki hiçbir arayüz bu turda **çalıştırılmadı**; tablo
> arayüz/kapsam sözleşmesidir, ölçüm sonucu **değildir**. Ölçüm yapılmadan
> kesin gecikme/throughput rakamı yazılmaz (§5.6 işaretli kalır).

### §6.2 Jitter tanımı ve türleri (ölçüm tasarımı — PLANNED)

| # | Jitter türü | Tanım | Ölçüm kaynağı |
|---|---|---|---|
| 6.2.1 | kesme jitteri | kesme çağrısı ↔ handler giriş arası | ftrace histogram (6.1.4) |
| 6.2.2 | geçiş jitteri | bağlam değiştirme süresi değişkenliği | schedstats (6.1.1) |
| 6.2.3 | uyandırma jitteri | bekleme ↔ uyandırma arası | `nanosleep` + sayaç |
| 6.2.4 | tampon jitteri | periyot ↔ tampon-dolma arası (ses) | uygulama sayacı (PLANNED) |

> Hiçbir jitter türü bu turda **ölçülmedi**; tablo yalnız **tanım/tasarım**
> tablosudur (dürüstlük: rakam yok).

### §6.3 Ölçüm metodolojisi (adımlar — PLANNED)

| # | Adım | Araç | Beklenen çıktı |
|---|---|---|---|
| 6.3.1 | sayaç etkinleştirme | schedstats / ftrace | sayaç akışı |
| 6.3.2 | yük koşulları | stres + ses benzeri periyodik yük | dağılım |
| 6.3.3 | örnekleme | histogram (6.1.4) | jitter histogramı |
| 6.3.4 | raporlama | ortalama · maks · saçılım | ölçüm raporu |
| 6.3.5 | eşik değerlendirme | sartname (§6.4) | geç/kal |

### §6.4 Ölçüm hedefi (CoreMusic — sartname, ölçülmemiş)

| # | Hedef | Değer | Durum |
|---|---|---|---|
| 6.4.1 | örnekleme periyodu | 1 / 48000 Hz = 20.83 µs | sartname |
| 6.4.2 | tampon süresi (512 örnek) | 512 / 48000 = 10.666… ms | sartname |
| 6.4.3 | jitter üst sınırı | UNKNOWN | ölçüm PLANNED |
| 6.4.4 | context-switch maliyeti | UNKNOWN | ölçüm PLANNED |
| 6.4.5 | en-boy (worst-case) gecikme | UNKNOWN | ölçüm PLANNED |

> 6.4.3-6.4.5 **UNKNOWN** — ölçüm yapılmadan değer kurulmaz. 6.4.1-6.4.2
> sartname aritmetiğidir (ölçüm değil), anayasa §6.7 ile aynıdır (kopya yok).

## §7 Repo kanıtı (dolaylı erişim — PHP → libc → syscall)

### §7.1 Bağlam/bekleme sınırındaki repo satırları (grep ile birebir)

| # | Dosya : satır | Ne görülür | İnen çekirdek yüzeyi | Doğrudan mı? |
|---|---|---|---|---|
| 7.1.1 | `shared/tests/Repository/ApiKeyCreateCommandTest.php:253` | `$handle = proc_open(` | `clone` + `execve` + pipe | ❌ dolaylı |
| 7.1.2 | `shared/src/AI/AIEngine.php:295` | `$output = shell_exec($cmd);` | `fork` + `exec` + `waitpid` | ❌ dolaylı |
| 7.1.3 | `shared/src/AI/AIEngine.php:292` | `escapeshellarg($filePath)` | (uygulama kaçışı — çekirdek değil) | uygulama |
| 7.1.4 | `home.coremusic.net/include/Auth/HomeAuthBridge.php:117` | `usleep(self::RETRY_DELAY_US);` | `nanosleep` → zamanlama | ❌ dolaylı |
| 7.1.5 | `shared/src/Session/SessionConfig.php:35` | `@mkdir($savePath, 0750, true);` | `mkdir` — VFS | ❌ dolaylı |
| 7.1.6 | `shared/src/Session/SessionConfig.php:20` | `DIR_PERMISSIONS = 0750` | POSIX izin modu | ❌ dolaylı |

> **Doğrulama yöntemi:** her satır `grep` ile diskten bulundu (2026-10-13);
> uydurma satır no yok (zero-hallucination). `RETRY_DELAY_US = 200_000`
> (`HomeAuthBridge.php:25`) sabit sınıfıdır — 200 ms gecikme.

### §7.2 Durum makinesi ↔ repo eşlemesi

| # | Repo satırı | Durum makinesi karşılığı | §5.2 geçişi |
|---|---|---|---|
| 7.2.1 | `proc_open:253` | yeni süreç doğar (runnable) | 5.2.1 |
| 7.2.2 | `shell_exec:295` | `fork`+`exec`+`waitpid` → zombie kapısı | 5.2.5 · 5.2.7 |
| 7.2.3 | `usleep:117` | süreç `waiting` (nanosleep) | 5.2.4 · 5.2.6 |
| 7.2.4 | `mkdir:35` | VFS + izin → dosya katmanı (sürücü dalı) | — |

### §7.3 Syscall eşlemesi (repo kodu → çekirdek çağrısı)

| # | PHP satırı | İnen syscall | Çekirdek alt sistemi |
|---|---|---|---|
| 7.3.1 | `proc_open:253` | `clone` + `execve` + `pipe2` | süreç |
| 7.3.2 | `shell_exec:295` | `fork` + `exec` + pipe + `waitpid` | süreç |
| 7.3.3 | `usleep:117` | `nanosleep` | zamanlama |
| 7.3.4 | `mkdir:35` | `mkdir` | VFS |
| 7.3.5 | `SessionConfig:20` | (izin modu — `mkdir` argümanı) | VFS |

> Eşleme upstream docs'a dayanır (repo'da çekirdek kodu YOK); her repo satırı
> §7.1'de `grep` ile doğrulanmıştır.

### §7.4 Dürüstlük kaydı (negatif kanıt)

| # | İddia | Arama | Sonuç |
|---|---|---|---|
| 7.4.1 | Repo'da doğrudan context-switch / zamanlayıcı çekirdek kodu yok | `*.c`·`*.h`·`*.cpp` → 0 | ✅ YOK |
| 7.4.2 | Repo'da spinlock / kilit çekirdek kodu yok | `spinlock`·`pcntl_` → 0 isabet | ✅ YOK |
| 7.4.3 | Repo'da IRQ/kesme kodu yok | `request_irq`·`spinlock` → 0 isabet | ✅ YOK |

> 7.4.1-7.4.3 **dürüstlük kaydıdır**, eksiklik değil: CoreMusic PHP
> uygulamasıdır, çekirdek kodu içermez. §7.1 satırları **dolaylı** kanıttır.

## §8 Risk matrisi (bu dosyanın kapanışı)

### §8.1 Risk tablosu

| # | Risk | Olasılık | Etki | Önlem (bu dalda) | Komşu |
|---|---|---|---|---|---|
| 8.1.1 | geçiş maliyeti öngörülemezliği | orta | yüksek (jitter) | ses yolunda syscall yasağı | anayasa §6.9 |
| 8.1.2 | zombie sızıntısı | düşük | orta (kayıp) | `wait`/`proc_close` zorunlu | `-api` |
| 8.1.3 | öncelik tersliği | düşük | yüksek (kilit) | RT-mutex / kilit sırası | kesme §3 |
| 8.1.4 | TLB shootdown | orta | orta (gecikme) | çekirdek-affinity planı | `-plan` |
| 8.1.5 | OOM | düşük | yüksek (ölüm) | tahsis yasağı (ses yolu) | anayasa §6.8 |
| 8.1.6 | hedef çekirdek belirsizliği | — | — | platform bağımsız yazım | Q4 UNKNOWN |

### §8.2 Azaltım ↔ doğrulama bağlantısı

| # | Risk | Azaltım | Doğrulama kapısı |
|---|---|---|---|
| 8.2.1 | 8.1.1 | syscall yasağı + ölçüm (§6) | §6.3-§6.4 PLANNED |
| 8.2.2 | 8.1.2 | `wait` zorunluluğu (`-api` sözleşmesi) | repo `proc_close` kanıtı |
| 8.2.3 | 8.1.3 | kilit sırası + RT-mutex (kesme dalı) | kesme §3 lockdep |
| 8.2.4 | 8.1.5 | sıfır tahsis (ses yolu) | anayasa §6.9 |

## §9 Kaynakça (HEAD 200 · 2026-10-13)

| # | İddia | URL | Durum |
|---|---|---|---|
| 9.1 | Zamanlama alt sistemi (EEVDF · CFS) | https://docs.kernel.org/scheduler/index.html | ✅ HTTP 200 |
| 9.2 | Zamanlama istatistikleri (schedstats) | https://docs.kernel.org/scheduler/sched-stats.html | ✅ HTTP 200 |
| 9.3 | CFS bandwidth (ağırlık) | https://docs.kernel.org/scheduler/sched-bwc.html | ✅ HTTP 200 |
| 9.4 | Nice tabanlı zamanlama tasarımı | https://docs.kernel.org/scheduler/sched-nice-design.html | ✅ HTTP 200 |
| 9.5 | RT grup zamanlama (bandwidth) | https://docs.kernel.org/scheduler/sched-rt-group.html | ✅ HTTP 200 |
| 9.6 | Bellek yönetimi (mm) alt sistemi | https://docs.kernel.org/mm/index.html | ✅ HTTP 200 |
| 9.7 | `/proc/<pid>/pagemap` (sayfa eşlemesi) | https://docs.kernel.org/admin-guide/mm/pagemap.html | ✅ HTTP 200 |
| 9.8 | ftrace histogram tracer | https://docs.kernel.org/trace/histogram.html | ✅ HTTP 200 |
| 9.9 | `clone(2)` bayraklı süreç yaratma | https://man7.org/linux/man-pages/man2/clone.2.html | ✅ HTTP 200 |
| 9.10 | `wait(2)` çocuk durumu bekleme | https://man7.org/linux/man-pages/man2/wait.2.html | ✅ HTTP 200 |
| 9.11 | `exit(2)` süreç sonlandırma | https://man7.org/linux/man-pages/man2/exit.2.html | ✅ HTTP 200 |
| 9.12 | `pidfd_open(2)` süreç kimlik dosyası | https://man7.org/linux/man-pages/man2/pidfd_open.2.html | ✅ HTTP 200 |

> Bu 12 URL kardeşim `-dogrulama.md` §E.1/§E.2/§E.4 envanterinden **yeniden
> kullanıldı** (kaynak orası · HEAD 200 · 2026-10-13). Yeni URL eklenmedi
> (envanter kesişimi 0 korundu).

## §10 Açık / doğrulanamayan iddialar (bu dosya)

| # | İddia | Neden | Durum |
|---|---|---|---|
| 10.1 | `core` ↔ `kernel` sınırı (bağlam/bellek hizası) | v2 §2.1 tanım vermez | ⚠️ VERIFICATION REQUIRED (Q2) |
| 10.2 | Hedef çekirdek (Linux/Windows/macOS) | yalnız CI `ubuntu-latest` | UNKNOWN (Q4) |
| 10.3 | Context-switch kesin maliyeti (ns/µs) | ölçüm yapılmadı | UNKNOWN (PLANNED) |
| 10.4 | TLB shootdown kesin gecikmesi | ölçüm yapılmadı | UNKNOWN (PLANNED) |
| 10.5 | EEVDF'nin hedef çekirdekte geçerliliği | Q4 açık | ⚠️ VERIFICATION REQUIRED |
| 10.6 | macOS/XNU bağlam değiştirme ayrıntısı | XNU URL elenmişti | ⚠️ VERIFICATION REQUIRED |

> 10.1-10.6 kardeş `-dogrulama.md` §F.5'teki açık kayıtlarıyla **aynı**
> konulardır — kapatılmadı, `status: draft` korunur; `verified` yapılmaz.

## §11 Derinleştirme — senaryo akışları (kavramsal, ölçülmemiş)

### §11.1 Senaryo A — bloklayan G/Ç (waiting ↔ runnable)

| # | Adım | Durum | Not |
|---|---|---|---|
| 11.1.1 | süreç `read` çağırır (veri yok) | running → waiting | 5.2.4 geçişi |
| 11.1.2 | planlayıcı başka runnable seçer | — | 5.2.2 geçişi |
| 11.1.3 | veri gelir, uyanma | waiting → runnable | 5.2.6 geçişi |
| 11.1.4 | planlayıcı yeniden seçer | runnable → running | 5.2.2 geçişi |

> Repo karşılığı: `usleep:117` (§7.1.4) — `nanosleep` ile **aynı sınıf**
> bekleme; gerçek syscall kanıtı ölçülmedi (⚠️).

### §11.2 Senaryo B — alt süreç (zombie kapısı)

| # | Adım | Durum | Not |
|---|---|---|---|
| 11.2.1 | `proc_open:253` / `shell_exec:295` | runnable yaratılır | 5.2.1 |
| 11.2.2 | çocuk `exec` + çalışır | running | — |
| 11.2.3 | çocuk biter, ebeveyn okumaz | zombie | 5.2.5 |
| 11.2.4 | ebeveyn `wait`/`proc_close` | zombie → temiz | 5.2.7 |

> 11.2.3 satırı §5.6.2 (zombie sızıntısı) riskinin kapısıdır; repo'da
> `proc_close` kanıtı `-api` dalının ölçümüdür (bu dosya tekrar etmez).

### §11.3 Senaryo C — zaman aşımı/uyandırma jitteri (ses yoluna kapı)

| # | Adım | Durum | İlgili risk |
|---|---|---|---|
| 11.3.1 | periyodik iş uyandırılır | waiting → runnable | 6.2.3 jitter |
| 11.3.2 | runnable → running gecikmesi | kuyruk bekleme | 5.6.1 |
| 11.3.3 | iş tamponu doldurur | running | 6.2.4 jitter |
| 11.3.4 | periyot aşımı | — | sartname ihlali (UNKNOWN eşiği) |

> 11.3.4'te **eşik yazılmaz** — jitter ölçümü PLANNED (§6.3); sayı uydurma
> yasak.

## §12 Hata kataloğu (dosyanın risk sınıflandırması)

| # | Hata modu | Belirti | Sınıf | Bağlantı |
|---|---|---|---|---|
| 12.1 | kilit tersliği | öncelikli süreç kilit sahibini bekler | deadlock sınıfı | kesme §3 |
| 12.2 | öncelik dönüşümü | RT'ye yükselen süreç kaynak açlığı | starvation | kesme §3 |
| 12.3 | zombie sızıntısı | süreç kaydı birikir | kaynak | 5.6.2 |
| 12.4 | OOM döngüsü | geri çağırma başarısız → öldürme | bellek | anayasa §6.8 |
| 12.5 | TLB shootdown yığını | IPI seli (çekirdekler arası geçiş) | performans | 5.6.4 |
| 12.6 | sayaç kayması | ölçüm etkin → performans değişir | ölçüm | §6.1 |
| 12.7 | tahsis ses yolunda | beklenmedik allocation → jitter | realtime | anayasa §6.9 |

> 12.1-12.5 sınıflandırma **doktrin**dir (ölçüm değil); 12.6 ölçümün
> kendisini gölgelemesi bilinen tehlike (PLANNED notu §6.1'de).

## §13 Ölçüm protokolü (P1-P8 — PLANNED, çalıştırılmadı)

| # | Adım | Kapı | Beklenen çıktı |
|---|---|---|---|
| P1 | ortam kaydı (çekirdek · CPU · bellek) | Q4 | ortam künyesi |
| P2 | sayaç etkinleştirme (schedstats) | §6.1.1 | sayaç akışı |
| P3 | boşta (idle) ölçüm | §6.3 | taban dağılım |
| P4 | stres ölçümü | §6.3 | yük altı dağılım |
| P5 | periyodik yük ölçümü (ses benzeri) | §6.3.2 | jitter histogramı |
| P6 | en-boy (worst-case) kaydı | §6.4.5 | maks gecikme |
| P7 | sartname karşılaştırması | §6.4.1-6.4.2 | geç/kal |
| P8 | rapor + `status` kararı | kapalı sorular | draft ↔ verified |

> **P1-P8 bu turda çalıştırılmamıştır.** Protokol, ölçüm yapılmadan hiçbir
> §5.6/§6.2 rakamını doldurmaz; `status: draft` korunur.

## §14 Komşu sınır matrisleri (bu dosyanın aldığı/verdiği)

| Komşu | Bu dosya verir | Bu dosya alır | Sınır çizgisi |
|---|---|---|---|
| `cekirdek-yonetim-anayasa` | çalışma anı (durum·geçiş·ölçüm) | doktrin (halka·sayfa·model) | §5.3 tekrar etmez |
| `cekirdek-yonetim-ornekler` | durum/geçiş tabloları (§5.2) | repo eşlemesi isteği | dayanak burada |
| `kesme-ve-senkronizasyon` | terslik riski kapısı (5.6.3) | kilit türü doktrini | kilit tanımı kesme'de |
| `isletim-sistemi-plan` | mekanizma (EEVDF/CFS §5.4) | politika kararı | politika burada değil |
| `isletim-sistemi-api` | `wait` zorunluluğu (8.2.2) | `proc_close` kanıtı | sözleşme `-api`'de |
| `surucu-ve-modul-modeli` | VFS/mkdir eşlemesi (7.2.4) | dosya katmanı doktrini | VFS derinliği orada |

## §15 Ölçüm — dosyanın kendi envanteri (2026-10-13)

| # | Ölçü | Değer | Durum |
|---|---|---|---|
| 15.1 | repo (+) satır | 6 (§7.1 tablosu) | ölçüldü (grep) |
| 15.2 | repo (−) arama | 3 (§7.4) / 0 isabet | ölçüldü (grep) |
| 15.3 | envanter URL | 12 (§9) / HTTP 200 | doğrulandı (envanter) |
| 15.4 | yeni URL doğrulama | 0 | — |
| 15.5 | ölçüm sonucu | 0 (tümü PLANNED) | dürüstlük |
| 15.6 | açık soru | 3 (Q2 · Q4 · ölçüm) | açık |

## §16 Ölçüm sonuçlarını okuma kılavuzu (raporlama şablonu — PLANNED)

### §16.1 Rapor alanları (ölçüm yapıldığında doldurulacak)

| # | Alan | Biçim | Dolu mu şimdi? |
|---|---|---|---|
| 16.1.1 | ortam künyesi | çekirdek · CPU · bellek | ❌ (P1 bekliyor) |
| 16.1.2 | örneklem sayısı | tam sayı | ❌ (P2-P5) |
| 16.1.3 | ortalama gecikme | µs/ms (ölçüm) | ❌ (P3-P5) |
| 16.1.4 | maks gecikme | µs/ms (ölçüm) | ❌ (P6) |
| 16.1.5 | saçılım (std/percentil) | sayı | ❌ (P3-P5) |
| 16.1.6 | sartname karşılaştırma | geç/kal | ❌ (P7) |

> **Hiçbir alan doldurulmamıştır** — bu tablo şablondur, sonuç **değildir**.
> Doldurulmadan "ölçüldü" denmez (dürüstlük kapısı).

### §16.2 Yorumlama tuzakları (okurken dikkat)

| # | Tuzak | Yanlış okuma | Doğru okuma |
|---|---|---|---|
| 16.2.1 | sayaç etkisi | "ölçüm temiz" | sayaç kendisi yük ekler (12.6) |
| 16.2.2 | ortalama saklama | "ortalama iyi" | maks + saçılım şart (16.1.4-5) |
| 16.2.3 | kısa pencere | "tutarlı" | uzun koşu gerekir (P4-P5) |
| 16.2.4 | platform genelleme | "her yerde aynı" | Q4: hedef çekirdek bilinmiyor |
| 16.2.5 | sartname ile ölçümü karıştırma | 20.83 µs "ölçülen" | 20.83 µs **aritmetik** (§6.4) |

> 16.2.5 tuzakı bu dosyanın en riskli karıştırma kapısıdır: sartname
> aritmetiği gerçek ölçüm **gibi** sunulamaz.

### §16.3 Sartname aritmetiği tablosu (ölçüm DEĞİL — aritmetik)

| # | Büyüklük | Hesap | Sonuç | Sınıf |
|---|---|---|---|---|
| 16.3.1 | örnekleme periyodu | 1 / 48000 | 20.833… µs | aritmetik |
| 16.3.2 | tampon süresi (512) | 512 / 48000 | 10.666… ms | aritmetik |
| 16.3.3 | tampon periyodu | 1 / (48000/512) | 10.666… ms | aritmetik |
| 16.3.4 | kare hızı (tampondan) | 48000 / 512 | 93.75 kare/s | aritmetik |
| 16.3.5 | jitter payı (kabul) | — | UNKNOWN | ölçüm PLANNED |

> 16.3.1-16.3.4 **arıtmetik**tir (ölçüm değil); 16.3.5 ancak §13 P1-P8
> çalıştırıldıktan sonra doldurulur. Bu tablo §6.4 ile aynıdır — tekrar
> değil, **okuma kılavuzu** bakış açısıdır (kopya yasağı ihlal edilmez).

## §16B Sık sorulanlar (dosyanın sınırları — hızlı okuma)

| # | Soru | Kısa cevap | Detay |
|---|---|---|---|
| 16B.1 | context switch maliyeti kaç µs? | UNKNOWN (ölçülmedi) | §10.3 · §13 |
| 16B.2 | hangi planlayıcı? | EEVDF/CFS (upstream) | §5.4 |
| 16B.3 | repo'da çekirdek kodu var mı? | Hayır (grep 0) | §7.4 |
| 16B.4 | repo kanıtı doğrudan mı? | Hayır — PHP → libc → syscall | §7.1 |
| 16B.5 | jitter eşiği belli mi? | Hayır — sartname vermez | §16.3.5 |
| 16B.6 | zombie riski nedir? | `wait` okunmazsa kayıt birikir | §5.6.2 |
| 16B.7 | bu dosya `status`'ü neden draft? | 3 açık soru (Q2·Q4·ölçüm) | §15.6 |

### §16B.2 Hızlı karar tablosu (dosyayı kim ne zaman okur)

| Okur profili | İlgi bölümü | Alacağı karar |
|---|---|---|
| zamanlama/mimari | §5.4 · §5.6 | planlayıcı doktrinini `-plan`'a taşır |
| ölçüm/validation | §6 · §13 · §16 | P1-P8 protokolünü çalıştırır |
| API/sözleşme | §7 · §8.2.2 | `wait`/`proc_close` zorunluluğunu sözleşmeye yazar |
| güvenlik | §5.6.3 | öncelik-tersliği önlemini kilit dalına iletir |

## §17 Tarihçe ve güncelleme günlüğü (bu dosya)

| Tarih | Değişiklik | Kanıt |
|---|---|---|
| 2026-10-13 | dosya oluşturuldu (§0 · §5-§10) | görev sözleşmesi |
| 2026-10-13 | repo satırları grep ile birebir | §7.1 doğrulama |
| 2026-10-13 | negatif kanıt 3 arama / 0 isabet | §7.4 |
| 2026-10-13 | §11-§15 derinleştirme eklendi | dolu-satır kapısı |
| 2026-10-13 | §16-§17 raporlama şablonu + günlüğü eklendi | dolu-satır kapısı |

## §18 Bağlayıcı özet — dosyanın omurgası ve durum işaretleri (2026-10-15)

> Append-only ekleme: §0–§17 değişmemiştir; bu bölüm yalnız derlemedir,
> yeni ölçüm/id dia üretmez.

### §18.1 Bölüm haritası

| Grup | Bölümler | Durum işareti |
|---|---|---|
| Çerçeve | §0 ELI15 · §5 Sistem durumu ve bağlam değiştirme | anlatım |
| Ölçüm | §6 Ölçüm (context switch · jitter · sayaçlar) · §15 Ölçüm envanteri (2026-10-13) · §16 Ölçüm sonuçlarını okuma kılavuzu | §16 şablon PLANNED |
| Kanıt | §7 Repo kanıtı (dolaylı erişim — PHP → libc → syscall) · §9 Kaynakça (HEAD 200 · 2026-10-13) | tarihli |
| Risk | §8 Risk matrisi · §10 Açık / doğrulanamayan iddialar · §12 Hata kataloğu | açık işaretli |
| Protokol | §13 Ölçüm protokolü (P1-P8) · §16 raporlama şablonu | PLANNED — çalıştırılmadı |
| Sınır | §14 Komşu sınır matrisleri · §16B Sık sorulanlar · §17 Tarihçe ve günlüğü | — |

### §18.2 Durum işaretleri (okuma kuralı)

- P1-P8 ölçüm protokolü: **PLANNED, çalıştırılmadı** (§13) — bu değerler "ölçüldü" olarak okunamaz.
- Raporlama şablonu (§16): **PLANNED** — sonuç tablosu dolu değildir.
- Doğrulanamayan iddialar §10'da işaretlidir; işaretli iddia kanıt sayılmaz.
- Repo erişimi **dolaylıdır** (PHP → libc → syscall, §7) — doğrudan çekirdek kodu kanıtı bu dosyada yoktur.
- §16B (sık sorulanlar) hızlı okuma içindir; çelişkide ana bölümler (§6 · §15) kazanır.

### §18.3 Önerilen okuma sırası

| Amaç | Başlangıç | Devam |
|---|---|---|
| Hızlı çerçeve | §0 ELI15 | §16B Sık sorulanlar |
| Ölçüm tasarımı | §6 | §13 Protokol | 
| Risk değerlendirme | §8 | §10 · §12 |
| Sınır kontrolü | §14 | §17 Tarihçe |

### §18.4 Sınır beyanı

- Bu bölüm §6/§15 içindeki sayıları tekrar etmez; yeni ölçüm üretmez.
- Kavramsal senaryolar (§11) ölçülmemiştir.
- `git commit` bu oturumda ATILMADI — bu ekleme working tree'dedir (commit orkestratöre ait).

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
