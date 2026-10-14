---
node_id: K000-isletim-sistemi/os-master
node_type: doc
status: draft
parent_id: K000-isletim-sistemi
children: []
depends_on: [K002-altyapi]
evidence: [https://man7.org/linux/man-pages/man2/syscalls.2.html, https://docs.kernel.org/core-api/index.html, https://learn.microsoft.com/en-us/windows/win32/coreaudio/about-the-windows-core-audio-apis, https://pubs.opengroup.org/onlinepubs/9799919799/, https://www.kernel.org/]
validation: "2026-10-13 · §K'daki tüm URL'ler HTTP HEAD 200 ile doğrulandı · §B/§G repo dosyalarından okundu · 3 ölü URL §K sonuna taşındı"
open_questions: ["Q1 electroncis kapsamı (KAPANDI 2026-10-14)", "Q2 core↔kernel sınırı", "Q3 plan kapsamı", "Q4 hedef platform seti", "Q5 turu↔altyapı gap"]
group: mimari
---

# K000 — İşletim Sistemi · Master Anayasa

> Bu dosya bu domainin **ana hikâyesidir**: OS nedir, CoreMusic onu neden kullanır,
> hangi katmanda hangi hizmeti alır, 12 kademe-2 düğümün sorumluluğu nedir. Ağır teknik
> ayrıntı child `*-kapsam.md` dosyalarındadır. Bu dosya **≥500 satır** anayasa belgesidir.

---

## §A — ELI15 · "İşletim sistemi nedir, CoreMusic'e ne yapar?"

Bilgisayarın kendisi yalnızca **sayıları çok hızlı yapan bir makinedir**. Bu sayılar
"şu dosyayı oku", "şu sesi çal" gibi şeyler değildir — makine bunları **anlamaz**. Bu çeviri
işini yapan katmana **işletim sistemi (OS)** denir.

CoreMusic bir **PHP uygulamasıdır**. PHP tek başına hiçbir şey yapamaz: dosya açamaz,
bellek ayıramaz, ses çalamaz. Bu işlerin hepsini **işletim sisteminden ister**.

Düşün: sen bir restoran sahibisin (PHP uygulaması), ama mutfakta ocak, buzdolabı ve
bulaşık makinesi (donanım) sana ait değil. Bunları **yöneten bir mutfak şefi** var (OS).
Sen "1 kg domates" dersin (syscall), şef gidip buzdolabından alır ve sana verir.
Sen asla buzdolabının anahtarını kendin çevirmezsin — **çevirirsen güvenlik açığı olur**.

İşletim sistemi üç şeyi **aynı anda** sağlar:

| # | Görev | Basit hali | CoreMusic karşılığı |
|---|---|---|---|
| 1 | **Donanımı paylaştırma** | "Herkes aynı diskten yazarsa kimse okuyamaz" | `sys_get_temp_dir()` → geçici dizin |
| 2 | **Koruma** | "Bir program çökerse tüm sistem çökmesin" | `php_uname('s')` → platformu tanı, doğru yolu seç |
| 3 | **Ara hizmet sunmak** | "Herkes aynı dosyayı açmak için tekerlek icat etmesin" | `header()` → HTTP başlığı zaten SAPI veriyor |

**Kritik nokta:** CoreMusic ile işletim sistemi arasındaki **tek geçiş noktası** sistem
çağrılarıdır (syscall). Bir PHP satırı `file_get_contents('/x')` yazdığında altta şunlar olur:

```
PHP kodu → Zend VM → PHP runtime (C) → libc → syscall → kernel → donanım
                                    ▲
                    CoreMusic'in DEĞİNEBİLDİĞİ EN SON KATMAN BURASI
```

Bu ok'un her basamağında CoreMusic'in **kontrolü azalır**, işletim sisteminin **kontrolü artar**.
Bu domainin tamamı bu **ok** hakkındaki mimari kararlardır.

---

## §B — Kimlik ve amaç

| Alan | Değer |
|---|---|
| Domain kimliği | `K000-isletim-sistemi` |
| Kök dosyalar | `index.md` · `os-master.md` · `os-bagimliliklar.md` · `os-dogrulama.md` |
| Kademe-2 düğüm | 12 (aşağıda §D) |
| Amaç | CoreMusic'in OS'ye **güvendiği sözleşmeyi** sabitlemek; kod üretmez |
| Kapsam dışı | PHP iş mantığı (K001) · sunucu kurulumu (K002) · devre şeması (Q1) |
| Hedef platform | Windows / Linux / macOS — **tek nokta tanımı yok** (Q4) |
| Test platformu | Linux (`ubuntu-latest`) — `.github/workflows/ci.yml:1` |
| Kanıt standardı | disk satır no (repo) **veya** HTTP 200 resmi URL (2026-10-13) |

**Amaç üç cümlede:**

1. CoreMusic'in **OS'ye bağımlılığını** tek tek listelemek (bkz. `os-bagimliliklar.md`).
2. Bu bağımlılıkların **doğruluğunu** kanıtlamak (bkz. `os-dogrulama.md`).
3. 12 alt alanın **sorumluluk sınırını** çizmek — kim hangi OS kavramının sahibi (§D, §F).

---

## §C — İşletim sisteminin 8 çekirdek kavramı (kısa özet)

> Her kavramın **tam** anlatımı child `isletim-sistemi-core/index.md` ve ilgili `-kapsam.md`
> içindedir. Burada kök seviye özet + kaynak + repo karşılığı verilir.

### §C.1 Süreç (process) ve zaman paylaşımı

Kernel, CPU'yu **ölçerek** dağıtır: hiçbir süreç sonsuza kadar sahip olamaz. Linux
`Documentation/scheduler/` altında EEVDF ve CFS'yi listeler
(https://docs.kernel.org/scheduler/index.html — 2026-10-13 ✅; CFS tasarımı:
https://docs.kernel.org/scheduler/sched-design-CFS.html ✅).

- **Çekirdek/iş ayrımı:** çekirdek (kernel) ve süreç (process) — birbirine karıştırılmaz.
- **`fork(2)`:** mevcut sürecin bir kopyası — POSIX.1-2024 `fork()` davranışı:
  https://pubs.opengroup.org/onlinepubs/9799919799/functions/fork.html (✅) ·
  Linux `fork(2)`: https://man7.org/linux/man-pages/man2/fork.2.html (✅).
- **`execve(2)`:** süreci yeni programa çevirir: https://man7.org/linux/man-pages/man2/execve.2.html (✅).
- CoreMusic karşılığı: **`proc_open`** ile borulu alt süreç
  (`shared/tests/Repository/ApiKeyCreateCommandTest.php:248-269`).

### §C.2 Bellek yönetimi (virtual memory)

Her sürecin **kendi adres alanı** vardır; biri çökerse öteki etkilenmez.

- `mmap(2)` — dosyayı belleğe eşleme: https://man7.org/linux/man-pages/man2/mmap.2.html (✅)
- Linux mm alt sistemi: https://docs.kernel.org/core-api/mm-api.html (✅)
- Windows bellek yönetimi: https://learn.microsoft.com/en-us/windows/win32/memory/memory-management (✅)
- CoreMusic'te: `sys_get_temp_dir()` (`shared/src/Component/TemplateEngine.php:73`),
  `ini_get('session.save_path') ?: sys_get_temp_dir()` (`shared/src/Session/SessionConfig.php:33`).

### §C.3 Dosya sistemi ve VFS

Tüm disk erişimi **tek bir soyutlamadan** geçer: VFS (Virtual File System). Aynı `open()`
çağrısı NTFS'e de ext4'e de gider. `open(2)`: https://man7.org/linux/man-pages/man2/open.2.html (✅).

Yol çözümleme (`path_resolution(7)`): https://man7.org/linux/man-pages/man7/path_resolution.7.html (✅) —
CoreMusic'te en kritik kullanım **yol zehirlenmesi savunmasıdır**:

```php
// home.coremusic.net/include/Stream/MusicStreamHandler.php:185-208
if (is_link($trimmed)) { $this->fail(403, 'Erişim reddedildi'); }  // symlink reddet
$real = realpath($trimmed);                                         // gerçek yol
$root = realpath($this->libraryPath);
$prefix = rtrim($root, '\\/') . DIRECTORY_SEPARATOR;
if (!str_starts_with($real, $prefix)) { $this->fail(403, ...); }    // sınır denetimi
```

### §C.4 Sistem çağrıları (syscall)

**Kullanıcı kodu ile kernel arasındaki tek geçiş.** Linux'ta yüzlerce syscall var;
`syscalls(2)` listeler: https://man7.org/linux/man-pages/man2/syscalls.2.html (✅) —
> "Linux provides many system calls for performing various low-level operations."

Tek bir syscall çağrısı: `syscall(SYS_xxx, arg1, arg2, ...)`
(https://man7.org/linux/man-pages/man2/syscall.2.html ✅). `read(2)`/`open(2)` gibi temel
çağrılar: https://man7.org/linux/man-pages/man2/read.2.html (✅).

> ⚠️ Kaynak kısıtı: her C kütüphanesi kendi syscall kapsayıcısını kullanır
> (glibc: https://www.gnu.org/software/libc/ ✅) — CoreMusic bunu **asla doğrudan**
> çağırmaz, PHP runtime üzerinden dolaylı kullanır.

### §C.5 İzinler ve güvenlik (capability, LSM, seccomp)

| Mekanizma | Kaynak (2026-10-13 ✅) | Ne yapar |
|---|---|---|
| POSIX izinleri | https://pubs.opengroup.org/onlinepubs/9799919799/ | dosya/dizin okuma-yazma hakları |
| Linux capability | https://man7.org/linux/man-pages/man7/capabilities.7.html | `root`'u küçük parçalara böler |
| seccomp | https://man7.org/linux/man-pages/man2/seccomp.2.html | süreç izinli syscall listesini daraltır |
| LSM | https://docs.kernel.org/security/index.html | güvenlik politikalarını bağlar |
| symlink denetimi | https://man7.org/linux/man-pages/man7/symlink.7.html | zincir/kaçış denetimi |

CoreMusic'te:
- `home.coremusic.net/.htaccess` — `X-Frame-Options`, `X-Content-Type-Options` başlıkları
  **web sunucusu düzeyinde** güvenlik sınırıdır (OS düzeyi değil).
- `MusicStreamHandler.php` `realpath()` + `is_link()` — dosya sistemi düzeyinde sınır.

### §C.6 Gözlemlenebilirlik (observability)

OS kendi içini görmeyi sağlar:

- **Linux ftrace:** https://docs.kernel.org/trace/ftrace.html (✅) — kernel içi izleme
- **trace events:** https://docs.kernel.org/trace/events.html (✅)
- **ETW (Windows):** https://learn.microsoft.com/en-us/windows/win32/etw/event-tracing-portal (✅) ·
  `WNODE_HEADER`: https://learn.microsoft.com/en-us/windows/win32/etw/wnode-header (✅)
- **perf:** https://man7.org/linux/man-pages/man2/perf_event_open.2.html (✅)

CoreMusic'te: `error_log()` → `shared/src/PageRouter/StructuredLogger.php:72`. Bunlar OS
gözlemlenebilirliği değil, **uygulama loglamasıdır** — ayrı katman.

### §C.7 Test altyapısı (OS düzeyi)

- **kselftest:** https://docs.kernel.org/dev-tools/kselftest.html (✅ — 2026-10-13 düzeltildi)
- **KUnit:** https://kunit.dev/ (✅) · kaynak ağacı:
  https://git.kernel.org/pub/scm/linux/kernel/git/torvalds/linux.git/tree/Documentation/dev-tools/kunit (✅)
- **PHP düzeyi:** `shared/phpunit.xml` 8 testsuite (`<testsuite name="Api">` … `</testsuite>`).

### §C.8 Ses donanımına erişim

Bu, CoreMusic'in **tek gerçek donanım-yüzeyi**dir:

| Platform | API | Kaynak (2026-10-13 ✅) |
|---|---|---|
| Windows | WASAPI / Core Audio | https://learn.microsoft.com/en-us/windows/win32/coreaudio/about-the-windows-core-audio-apis |
| Linux | ALSA | https://www.alsa-project.org/wiki/Main_Page |
| Android (ref) | AAudio/NdkAudio | https://developer.android.com/ndk/guides/audio |
| macOS | Core Audio (AudioToolbox) | https://developer.apple.com/documentation/audiotoolbox |

⚠️ **Repo'da buna karşılık gelen kod yok** — CoreMusic sesi **ffmpeg ile WAV'e çevirir**
(`shell_exec` + `escapeshellarg`, `shared/src/AI/AIEngine.php:288-310`), donanımı **doğrudan**
sürmez. Ayrıntı: `isletim-sistemi-audio/wasapi/`.

---

## §D — 12 kademe-2 düğümün tamamı

> Dosya/≥500 durumu **diskten ölçüm** (2026-10-13 · **turu + electroncis satırları 2026-10-14 taze**): 133 md · 44.152 satır · ≥500: 67 · <500: 66. (Diğer satırlar 2026-10-13 sayımıdır — domain-geneli yeniden sayım ayrı denetim konusu; TOPLAM satırı 2026-10-14'te satırların toplamından yeniden hesaplandı.)

| # | Düğüm | Sorumluluk | Dosya | Satır | ≥500 | Komşular |
|---|---|---|---|---|---|---|
| 1 | `electroncis` | OS↔donanım elektronik arayüzü (Q1 KAPANDI) | 12 | 2968 | 0 | turu, audio, K002-electroncis |
| 2 | `isletim-sistemi-api` | kullanıcı-kernel sınırı, libc/API | 6 | 1478 | 2 | kernel, core, runtime |
| 3 | `isletim-sistemi-audio` | ses yığını (WASAPI/ALSA/CoreAudio) | 17 | 6187 | 7 | turu, kernel, electroncis |
| 4 | `isletim-sistemi-core` | çekirdek kavramlar (bellek/süreç/dosya) | 4 | 432 | 0 | kernel, plan, api |
| 5 | `isletim-sistemi-cross` | platform bağımsızlık (PATH/sep) | 4 | 423 | 0 | turu, runtime, api |
| 6 | `isletim-sistemi-kernel` | süreç/syscall/sürücü | 4 | 384 | 0 | core, api, security |
| 7 | `isletim-sistemi-observability` | ftrace/ETW/perf, log | 4 | 191 | 0 | kernel, testing |
| 8 | `isletim-sistemi-plan` | scheduling/plan (Q3) | 4 | 206 | 0 | kernel, core |
| 9 | `isletim-sistemi-runtime` | PHP SAPI + web sunucusu | 4 | 217 | 0 | api, cross, testing |
| 10 | `isletim-sistemi-security` | izin/capability/sandbox | 4 | 206 | 0 | kernel, api, observability |
| 11 | `isletim-sistemi-testing` | OS test (kselftest/KUnit) | 4 | 202 | 0 | kernel, observability, runtime |
| 12 | `isletim-sistemi-turu` | OS ailesi (Windows/Linux/macOS + 8 platform) | 66 | 31258 | 58 | audio, cross, electroncis |
| — | **TOPLAM** | 12 düğüm | **133** | **44.152** | **67** | kök 4 hariç · turu + electroncis satırları 2026-10-14 |

**Not:** `electroncis` (Q1) kapsamı **KAPANDI (2026-10-14)** — OS↔donanım elektronik arayüzü,
2 alt-dal (bkz. §J). `isletim-sistemi-plan` (Q3) kapsamı hâlâ [BİLİNMEYEN] — adlandırma tek
başına belirsiz, diske bakılmadan doldurulmadı.

### §D.1 Düğüm bazlı sorumluluk detayı (12 satır)

1. **`electroncis`** — OS↔donanım elektronik arayüzü (Q1 **KAPANDI** 2026-10-14 · Vault Steward).
   İki alt-dal: `donanim-arayuz-protokolleri` (I2S/TDM · I2C/SPI · USB · GPIO/clock · DMA ring) +
   `os-guc-elektronigi` (D0–D3 güç durumları · termal · DPC/ISR · ACPI). Tam elektronik devre
   içeriği `K002-electroncis`'e devredildi (Faz A/B). `audio`/`turu` ile komşu;
   `K002-electroncis`'e bağımlı. Dosya 12 · satır 2968 · ≥500: 0 (2026-10-14 recursive ölçüm).
2. **`isletim-sistemi-api`** — kullanıcı-kernel sınırı ve libc/Windows API yüzeyi. `php_uname`,
   `sysconf`, syscall kapsayıcısı. `kernel`/`core`/`runtime` ile komşu. Dosya 6 · satır 1478 ·
   ≥500: 2 (kısmi hazır).
3. **`isletim-sistemi-audio`** — ses yığını: WASAPI (Windows), ALSA (Linux), CoreAudio (macOS).
   CoreMusic'in **tek donanım-yüzeyi**. `wasapi/` deep-dal var. Repo'da karşılığı ffmpeg WAV
   (`AIEngine.php:288-310`). Dosya 17 · satır 6187 · ≥500: 7.
4. **`isletim-sistemi-core`** — çekirdek kavramlar: bellek, süreç, dosya, izin. §C'nin tam
   karşılığı. `kernel`/`plan`/`api` ile komşu; `kernel` sınırı **Q2 AÇIK**. Dosya 4 · 432 · 0.
5. **`isletim-sistemi-cross`** — platform bağımsızlık: `DIRECTORY_SEPARATOR`, PATH, çekirdek/repl
   ayrımı. `turu`/`runtime`/`api` ile komşu. Dosya 4 · 423 · 0.
6. **`isletim-sistemi-kernel`** — tek çekirdeğin içi: süreç, syscall, sürücü, zamanlayıcı.
   `core`/`api`/`security` ile komşu. Linux `kernel.org` kaynağı bu düğümün ana referansı.
   Dosya 4 · 384 · 0.
7. **`isletim-sistemi-observability`** — OS araçları: ftrace, trace events, ETW, perf. Uygulama
   loglaması (`error_log`) **bu düğümün konusu değil** (§F F7). `kernel`/`testing` komşu.
   Dosya 4 · 191 · 0.
8. **`isletim-sistemi-plan`** — scheduling/plan (Q3). Kapsam [BİLİNMEYEN]: "plan" tek başına
   belirsiz. `kernel`/`core` komşu. Dosya 4 · 206 · 0.
9. **`isletim-sistemi-runtime`** — PHP SAPI + web sunucusu: Apache mod_php / php-fpm / IIS.
   `api`/`cross`/`testing` komşu. Dosya 4 · 217 · 0.
10. **`isletim-sistemi-security`** — OS düzeyi izin/capability/sandbox/seccomp. OWASP/CSRF bu
    düğümde **değil** (§F F6). `kernel`/`api`/`observability` komşu. Dosya 4 · 206 · 0.
11. **`isletim-sistemi-testing`** — OS düzeyi test: kselftest, KUnit, süreç izolasyonu. PHP
    testleri (`phpunit.xml`) ayrı katman. `kernel`/`observability`/`runtime` komşu.
    Dosya 4 · 202 · 0.
12. **`isletim-sistemi-turu`** — OS ailesi karşılaştırması: Windows/Linux/macOS + 8 platform
    alt-dalı. En derin düğüm. `audio`/`cross`/`electroncis` komşu. Dosya 66 · 31.258 · ≥500: 58 (2026-10-14 — kök `kod-seti` eklendi).

---

## §E — Katman haritası (K000 ↔ K001 donanım ↔ K002 altyapı ↔ K011 uygulama)

```
┌──────────────────────────────────────────────────────────────────────┐
│ K011 Uygulama (PHP 8.4)  — home.coremusic.net/** · shared/src/**     │
├──────────────────────────────────────────────────────────────────────┤
│ K001 İşletim Sistemi (bu domain) — 6 katmanlı harita (aşağıda)       │
│   5. cross / observability   4. runtime   3. api                     │
│   2. security   1. kernel/core/plan   0. audio/turu                  │
├──────────────────────────────────────────────────────────────────────┤
│ K002 Altyapı — sunucu kurulumu, Apache/IIS yapılandırma, deploy      │
├──────────────────────────────────────────────────────────────────────┤
│ K003..K010 Donanım/altyapı domainleri — ses kartı, disk, ağ, PCB     │
└──────────────────────────────────────────────────────────────────────┘
```

| Katman | Child alt alanı | Bu katman ne verir? |
|---|---|---|
| 5 | `isletim-sistemi-cross`, `-observability` | log, platform bağımsızlık |
| 4 | `isletim-sistemi-runtime` | SAPI, süreç modeli |
| 3 | `isletim-sistemi-api` | libc / Windows API yüzeyi |
| 2 | `isletim-sistemi-security` | syscall filtreleme (seccomp/capability) |
| 1 | `isletim-sistemi-kernel`, `-core`, `-plan` | çekirdek kavramları |
| 0 | `isletim-sistemi-audio`, `isletim-sistemi-turu` | ses yığını, platform ailesi |

**Sınır notu:** K000 ↔ K001 ayrımı yapaydır — K000 "OS'nin CoreMusic'e verdiği sözleşme" iken
K001 "CoreMusic'in OS üzerindeki donanım-yüzeyi (Neva Engine / WASAPI)"dir. K000 kod üretmez,
K001 kod üretir. K002 ise OS'nin **kuruluşunu** yönetir (K000 OS'nin **kavramlarını** tarif eder).

**Q1 kapanış notu (2026-10-14):** `electroncis` bu haritada **katman-0 hizasında** durur
(`audio`/`turu` ile birlikte) — Q1 kararıyla kapsamı OS↔donanım **arayüzü** olarak netleşti
(2 alt-dal: `donanim-arayuz-protokolleri` + `os-guc-elektronigi`); tam elektronik devre
içeriği `K002-electroncis` domainine devredildi (Faz A/B). Not: bu dosyanın frontmatter
`depends_on: [K002-altyapi]` kaydı ile diskteki `K002-electroncis/` dizini arasında
**bilinen ID adlandırma farkı** vardır — çözülmedi, yalnızca kayda geçilmiştir (Q5).

---

## §F — Sınır ve ayrım kuralları (turu ≠ api ≠ audio ≠ kernel)

| Kural | Ayrım | Gerekçe |
|---|---|---|
| F1 | `turu` ≠ `kernel` | turu **OS ailesi karşılaştırması** (Windows/Linux/macOS), kernel **tek çekirdeğin içi** (süreç/syscall/sürücü) |
| F2 | `api` ≠ `kernel` | api **kullanıcı-kernel sınırı ve libc yüzeyi**, kernel **sınırın altındaki çekirdek** |
| F3 | `audio` ≠ `turu` | audio **ses yığını** (WASAPI/ALSA/CoreAudio), turu **platform ailesi karşılaştırması** — audio tek platforma, turu hepsine bakar |
| F4 | `core` ↔ `kernel` | **Q2 AÇIK** — ikisi de "çekirdek kavramı" iddiasında; sınır diske bakılmadan çizilmez |
| F5 | `plan` kapsamı | **Q3 AÇIK** — scheduling mi, doküman mı belirsiz |
| F6 | `security` ≠ uygulama güvenliği | security **OS düzeyi** (izin/capability/sandbox), OWASP/CSRF **K001/K011** katmanı |
| F7 | `observability` ≠ uygulama logu | observability **OS araçları** (ftrace/ETW/perf), `error_log()` **uygulama loglaması** |

### §F.1 OS ailesi hızlı karşılaştırma (turu düğümünün özeti — kaynaklar §K)

| Özellik | Linux | Windows | macOS |
|---|---|---|---|
| Çekirdek | monolitik (kernel.org) | hibrit NT (learn.microsoft.com) | XNU / Darwin (developer.apple.com) |
| Ses API | ALSA (+ PulseAudio/PipeWire) | WASAPI / Core Audio | Core Audio (AudioToolbox) |
| İzleme | ftrace, perf, trace events | ETW | DTrace / Instruments (⚠️ detay VERIFICATION REQUIRED) |
| İzin modeli | POSIX + capability + LSM | ACL + token + seccomp-benzeri | POSIX + Sandbox (kext/EndpointSecurity) |
| Test altyapısı | kselftest, KUnit | WDK test (⚠️ detay VERIFICATION REQUIRED) | XCTest (⚠️ detay VERIFICATION REQUIRED) |
| CoreMusic kanıtı | CI `ubuntu-latest` ✓ | `.htaccess`+`web.config` (deploy) | **repo kanıtı yok** (Q4) |

> ⚠️ macOS/Windows izleme ve test araçlarının **detaylı** davranışı bu dosyada doğrulanmadı —
> yalnızca resmi portal URL'leri §K'da HTTP 200 ile kanıtlandı; işlevsel iddia yazılmadı.

---

## §G — Mimari kararlar (bu dosyanın kaydı)

| # | Karar | Gerekçe | Kanıt |
|---|---|---|---|
| G1 | CoreMusic **doğrudan** syscall çağırmaz | PHP runtime → libc → syscall zinciri güvenli | `AIEngine.php:288-310` |
| G2 | Platform tespiti `php_uname('s')` | tek nokta platform kararı | `DesktopBff.php:35` |
| G3 | Yol denetimi `realpath` + `is_link` + `DIRECTORY_SEPARATOR` | yol zehirlenmesi savunması | `MusicStreamHandler.php:185-208` |
| G4 | Ses erişimi **ffmpeg üzerinden** (WAV), donanım sürme yok | web ölçeğinde donanım kilidi riski | `AIEngine.php:288-310` |
| G5 | Test/CI platformu **Linux** | tek doğrulanmış platform | `ci.yml:1` |
| G6 | Web sunucusu Apache **veya** IIS (çift yapılandırma) | cross-platform | `.htaccess` + `web.config` |
| G7 | `ext-apcu` CLI testte açık | önbellek davranışı testte de tutarlı | `ci.yml:20` |

### §G.1 CoreMusic ↔ OS: hangi çağrı hangi katmanda (repo kanıtlı)

| CoreMusic davranışı | OS hizmeti | Kod (repo, satır no doğrulandı) |
|---|---|---|
| Platform tespiti | `uname` (POSIX) / `php_uname` | `shared/src/Api/Bff/DesktopBff.php:35` |
| Geçici dizin | `getenv`/`sys_get_temp_dir` | `shared/src/Component/TemplateEngine.php:73` |
| Oturum dizini | `mkdir` + `umask` (0750) | `shared/src/Session/SessionConfig.php:33-36` |
| Harici süreç | `posix_spawn`/`proc_open` | `shared/tests/Repository/ApiKeyCreateCommandTest.php:248-269` |
| Komut satırı güvenliği | `execve` + argüman kaçışı | `shared/src/AI/AIEngine.php:288-310` (`escapeshellarg`) |
| Log yazma | `write(2)` | `shared/src/PageRouter/StructuredLogger.php:72` |
| HTTP yanıt | SAPI `header()` | `shared/src/PageRouter/ResponseEmitter.php` |
| Yol denetimi | `realpath`/`lstat` | `home.coremusic.net/include/Stream/MusicStreamHandler.php:185-208` |
| Zaman dilimi | `setenv`/`tzset` | `shared/src/Bootstrap/RuntimeBootstrap.php:9` (`date_default_timezone_set`) |
| Hata gösterimi | `ini_set` (display/log_errors) | `shared/src/Bootstrap/RuntimeBootstrap.php:11-19` |
| Uzantılar | yüklenmiş PHP eklentileri | `shared/composer.json:8-12` (`ext-apcu`, `ext-pdo`) |

### §G.2 Platform bağımlılığı: nerede kırılıyor? (repo gerçekleri)

1. CI `runs-on: ubuntu-latest` — `.github/workflows/ci.yml:1`
2. Web sunucusu iki aday: Apache `.htaccess` + IIS `web.config`
   (`home.coremusic.net/.htaccess`, `home.coremusic.net/web.config`)
3. Yol ayracı `DIRECTORY_SEPARATOR` ile veriliyor — Windows/Linux ayrımı
4. Uzantı `ext-apcu` — paylaşımlı bellek önbelleği (platformlarda farklı davranış)

**Sonuç:** CoreMusic **platform bağımsız PHP** yazar, **Linux (ubuntu) üzerinde test eder**,
**Apache + IIS** için yapılandırma taşır. Windows/macOS üzerinde **çalışma kanıtı repo içinde
yok** → Q4 açık (bkz. §I R1).

---

## §H — Bağımlılık grafiği (12 düğüm — özet; tam matris `os-bagimliliklar.md` §B)

```
                     ┌──────────── K002-altyapi ────────────┐
                     │  (OS kurulumu, Apache/IIS, deploy)    │
                     └───────────────────┬──────────────────┘
                                         │ bağımlı (K000 → K002)
     ┌───────────┬───────────┬───────────┼───────────┬───────────┬──────────┐
     │           │           │           │           │           │          │
  turu        audio       kernel       api        core        plan      security
 (L0)        (L0)        (L1)        (L3)        (L1)        (L1)       (L2)
     │           │           │           │           │           │          │
     └─────┬─────┘           │           │           │           │          │
         cross             RCU/sched   libc        mm-api    (Q3)      seccomp
        (L5)               (L1)        (L3)        (L1)                (L2)
           │                 │           │
     observability        testing     runtime
       (L5)                (L0)        (L4)
```

**Yön:** CoreMusic (K001/K011) → K000 kök → 12 düğüm → OS syscall/kernel. Döngü denetimi
için `os-bagimliliklar.md` §D. Katman kodu: L0=driver/hw · L1=çekirdek · L2=sınır/güvenlik ·
L3=libc/API · L4=SAPI · L5=uygulama-yakın (cross/observability).

---

## §I — Riskler

| # | Risk | Etki | Azaltma |
|---|---|---|---|
| R1 | Hedef platform tek nokta tanımsız (Q4) | üretim OS bilinmiyor, port riski | Q4 açık; CI Linux dışında doğrulama yok |
| R2 | `core` ↔ `kernel` sınırı bulanık (Q2) | sorumluluk kayması | child `index.md`'de `⚠️ VERIFICATION REQUIRED` |
| R3 | Ses donanımına doğrudan erişim yok | gerçek donanım davranışı bilinmiyor | ffmpeg WAV; WASAPI exclusive ⚠️ doğrulanamadı |
| R4 | `electroncis` kapsamı UNKNOWN (Q1) — **KAPANDI 2026-10-14** | ~~domain sınırları kesin değil~~ sınır artık tanımlı: OS↔donanım arayüzü, tam elektronik K002'de | Vault Steward kararı + Faz A/B/C uygulandı |
| R5 | `plan` kapsamı UNKNOWN (Q3) | scheduling mi doküman mı belirsiz | adlandırma tek başına yetersiz |
| R6 | `ext-apcu` platform davranışı farklı | önbellek tutarsızlığı | CLI testte açık (`ci.yml:20`); üretim doğrulanmadı |
| R7 | `runtime` SAPI/modeli tek nokta tanımsız | mod_php vs php-fpm vs IIS belirsiz | `.htaccess`+`web.config` var; aktif SAPI [BİLİNMEYEN] |
| R8 | `cross` sınırı `DIRECTORY_SEPARATOR`'a bağlı | yeni platform eklenince yollar kırılabilir | tek merkezî yutucu yok; her kullanım noktasında denetim |
| R9 | observability OS araçları repo'da kullanılmıyor | gerçek zamanlı teşhis kanıtı yok | yalnız `error_log()`; ftrace/ETW entegrasyonu PLANNED |
| R10 | testing kselftest/KUnit repo'da yok | OS düzeyi test kapsamı 0 | PHP `phpunit.xml` 8 testsuite; OS testi ayrı katman |

---

## §J — Açık sorular (bağlayıcı)

- **Q1** `electroncis` kapsamı — donanım mı, devre şeması mı? → **KAPANDI (2026-10-14 ·
  Vault Steward): OS↔donanım elektronik ARAYÜZÜ — iki alt-dal `donanim-arayuz-protokolleri`
  (I2S/TDM · I2C/SPI · USB · GPIO/clock) + `os-guc-elektronigi` (güç durumları · termal ·
  DPC/ISR). Tam elektronik devre içeriği `K002-electroncis`'e taşındı (21 blok · 13 URL ·
  Faz A) ve `electroncis/devre-tasarimi` + `guc-ve-koruma` dizinleri silindi (Faz B).**
- **Q2** `isletim-sistemi-core` ↔ `isletim-sistemi-kernel` sınırı — ikisi de "çekirdek kavramı"
  diyorsa fark ne? → [BİLİNMEYEN] (child `index.md` içinde `⚠️ VERIFICATION REQUIRED`)
- **Q3** `isletim-sistemi-plan` — scheduling mi, plan dokümanı mı? → [BİLİNMEYEN]
- **Q4** hedef platform seti tek noktada tanımlı değil → [BİLİNMEYEN]
- **Q5** `isletim-sistemi-turu` ↔ `K002-altyapi` gap — platform turu mu, altyapı mı? → [BİLİNMEYEN]

### §J.1 Q çözüm durumu takip tablosu

| Q | Konu | Durum | Çözüm için gereken | Sahip |
|---|---|---|---|---|
| Q1 | `electroncis` kapsamı | **KAPANDI (2026-10-14)** | disk incelemesi + Vault Steward onayı yapıldı — OS↔donanım arayüzü, 2 alt-dal; tam elektronik → `K002-electroncis` | Vault Steward |
| Q2 | `core` ↔ `kernel` sınırı | AÇIK | child `index.md` sınır tanımı + ADR | architect |
| Q3 | `plan` kapsamı | AÇIK | `isletim-sistemi-plan/` diske bakma | Vault Steward |
| Q4 | hedef platform seti | AÇIK | tek nokta platform tanımı (ADR) | architect + devops |
| Q5 | `turu` ↔ `K002` gap | AÇIK | domain sınır ADR'si | architect |

> **Q1 kapatıldı** (2026-10-14 · disk kanıtı + Vault Steward onayı); Q2–Q5 hâlâ AÇIK — açık
> kalanlar doldurulmadı, uydurma çözüm yazılmadı. Kapatma yalnız disk kanıtı + onay ile olur.

---

## §K — Kaynakça (2026-10-13 · HTTP HEAD 200)

| # | Konu | URL | Durum |
|---|---|---|---|
| 1 | Linux syscall listesi | https://man7.org/linux/man-pages/man2/syscalls.2.html | ✅ |
| 2 | Tek syscall çağrısı | https://man7.org/linux/man-pages/man2/syscall.2.html | ✅ |
| 3 | Çekirdek/driver API | https://docs.kernel.org/core-api/index.html | ✅ |
| 4 | Çekirdek sürüm sayfası | https://www.kernel.org/ | ✅ |
| 5 | Çekirdek doküman portalı | https://docs.kernel.org/ | ✅ |
| 6 | Windows Core Audio (WASAPI) | https://learn.microsoft.com/en-us/windows/win32/coreaudio/about-the-windows-core-audio-apis | ✅ |
| 7 | POSIX.1-2024 (Base Specs Issue 8) | https://pubs.opengroup.org/onlinepubs/9799919799/ | ✅ |
| 8 | POSIX fork() | https://pubs.opengroup.org/onlinepubs/9799919799/functions/fork.html | ✅ |
| 9 | Linux fork(2) | https://man7.org/linux/man-pages/man2/fork.2.html | ✅ |
| 10 | Linux execve(2) | https://man7.org/linux/man-pages/man2/execve.2.html | ✅ |
| 11 | Linux mmap(2) | https://man7.org/linux/man-pages/man2/mmap.2.html | ✅ |
| 12 | Linux open(2) | https://man7.org/linux/man-pages/man2/open.2.html | ✅ |
| 13 | Linux read(2) | https://man7.org/linux/man-pages/man2/read.2.html | ✅ |
| 14 | Linux ioctl(2) | https://man7.org/linux/man-pages/man2/ioctl.2.html | ✅ |
| 15 | capability(7) | https://man7.org/linux/man-pages/man7/capabilities.7.html | ✅ |
| 16 | seccomp(2) | https://man7.org/linux/man-pages/man2/seccomp.2.html | ✅ |
| 17 | path_resolution(7) | https://man7.org/linux/man-pages/man7/path_resolution.7.html | ✅ |
| 18 | symlink(7) | https://man7.org/linux/man-pages/man7/symlink.7.html | ✅ |
| 19 | ftrace | https://docs.kernel.org/trace/ftrace.html | ✅ |
| 20 | trace events | https://docs.kernel.org/trace/events.html | ✅ |
| 21 | ETW portalı | https://learn.microsoft.com/en-us/windows/win32/etw/event-tracing-portal | ✅ |
| 22 | ETW WNODE_HEADER | https://learn.microsoft.com/en-us/windows/win32/etw/wnode-header | ✅ |
| 23 | ETW EVENT_RECORD | https://learn.microsoft.com/en-us/windows/win32/api/evntcons/ns-evntcons-event_record | ✅ |
| 24 | Güvenlik alt sistemi (LSM) | https://docs.kernel.org/security/index.html | ✅ |
| 25 | perf_event_open(2) | https://man7.org/linux/man-pages/man2/perf_event_open.2.html | ✅ |
| 26 | Scheduler (EEVDF/CFS) | https://docs.kernel.org/scheduler/index.html | ✅ |
| 27 | CFS tasarımı | https://docs.kernel.org/scheduler/sched-design-CFS.html | ✅ |
| 28 | RCU | https://docs.kernel.org/RCU/ | ✅ |
| 29 | mm alt sistemi | https://docs.kernel.org/core-api/mm-api.html | ✅ |
| 30 | Symbol namespaces | https://docs.kernel.org/core-api/symbol-namespaces.html | ✅ |
| 31 | sysconf(3) | https://man7.org/linux/man-pages/man3/sysconf.3.html | ✅ |
| 32 | glibc | https://www.gnu.org/software/libc/ | ✅ |
| 33 | Windows bellek yönetimi | https://learn.microsoft.com/en-us/windows/win32/memory/memory-management | ✅ |
| 34 | Windows CreateFile | https://learn.microsoft.com/en-us/windows/win32/api/fileapi/nf-fileapi-createfilea | ✅ |
| 35 | Apple Kernel API | https://developer.apple.com/documentation/kernel | ✅ |
| 36 | Apple AudioToolbox | https://developer.apple.com/documentation/audiotoolbox | ✅ |
| 37 | ALSA Projesi | https://www.alsa-project.org/wiki/Main_Page | ✅ |
| 38 | Android NDK Audio (AAudio ref) | https://developer.android.com/ndk/guides/audio | ✅ |
| 39 | kselftest (düzeltildi) | https://docs.kernel.org/dev-tools/kselftest.html | ✅ |
| 40 | kselftest (mirror) | https://www.kernel.org/doc/html/latest/dev-tools/kselftest.html | ✅ |
| 41 | KUnit (düzeltildi) | https://kunit.dev/ | ✅ |
| 42 | KUnit kaynak ağacı | https://git.kernel.org/pub/scm/linux/kernel/git/torvalds/linux.git/tree/Documentation/dev-tools/kunit | ✅ |

**Ölü URL — 2026-10-13 HTTP 404 (kullanılmayacak):**

| URL | HTTP | Not |
|---|---|---|
| https://developer.android.com/ndk/guides/audio/aaudio | 404 | alt sayfa kaldırıldı; ana `.../guides/audio` kullanıldı |
| https://docs.kernel.org/kselftest.html | 404 | eski yol; `dev-tools/kselftest.html` kullanıldı |
| https://docs.kernel.org/dev-tools/kunit.html | 404 | docs yolu yok; `kunit.dev` kullanıldı |
| https://docs.kernel.org/introduction/index.html | 404 | `introduction` dizini docs.kernel.org'ta yok |
| https://docs.kernel.org/introduction.html | 404 | aynı |
| https://learn.microsoft.com/en-us/windows/win32/winprog/windows-apis | 404 | sayfa kaldırılmış |

---

## §L — İlişkili dosyalar

- Bağımlılık haritası: [`os-bagimliliklar.md`](os-bagimliliklar.md)
- Kanıt kaydı: [`os-dogrulama.md`](os-dogrulama.md)
- Dizin: [`index.md`](index.md)
- Alt alanlar: `isletim-sistemi-kernel/index.md` · `isletim-sistemi-api/index.md` ·
  `isletim-sistemi-audio/index.md` · `isletim-sistemi-turu/index.md` (ve diğer 8 child)

### §L.1 Belge kontrol ve durum

| Alan | Değer |
|---|---|
| Belge | `os-master.md` — K000 anayasa |
| Sürüm | 2026-10-13 derinleştirme revizyonu |
| Uzunluk hedefi | ≥500 satır (kök 4'ten 3'ü anayasa seviyesine çıkarıldı) |
| Kanıt standardı | repo satır no (Select-Object -Index ile doğrulandı) + HTTP HEAD 200 URL |
| Ölü URL temizliği | 6 ölü URL §K sonunda listelendi; hiçbirine atıf yapılmadı |
| Açık soru | Q1–Q5 hiçbiri doldurulmadı (§J) |
| Değişiklik yasağı | `git commit` subagent'a ait değil — bu dosya yalnız beyazliste revizyonu |
| İlişki | `index.md` (envanter) · `os-bagimliliklar.md` (matris) · `os-dogrulama.md` (kanıt) |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
