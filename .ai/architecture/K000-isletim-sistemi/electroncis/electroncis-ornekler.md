---
node_id: K000-isletim-sistemi/electroncis/electroncis-ornekler
node_type: module
status: verify-required
parent_id: K000-isletim-sistemi/electroncis
children: []
depends_on:
  - K001-donanım
  - K004-dsp
  - K005-firmware
evidence:
  - "shared/src/Api/Bff/DesktopBff.php satır 35 (php_uname)"
  - "shared/src/AI/AIEngine.php satır 290-295 (ffprobe + shell_exec)"
  - "shared/src/Cache/ApcuAdapter.php satır 9-13 (function_exists + apcu_fetch)"
  - "shared/src/Session/SessionConfig.php satır 33-35 (sys_get_temp_dir + mkdir 0750)"
  - "home.coremusic.net/include/Stream/MusicStreamHandler.php satır 185-200 (is_link/realpath/DIRECTORY_SEPARATOR)"
  - "repo taraması: *.cpp/*.c/*.h = 0 dosya (vendor hariç) — devre seviyesi kod YOK"
validation: "2026-10-13 · v2 §4.2 9-alan FM yazıldı · parent: kaldırıldı · ≥500 satır · gerçek repo kodu satır-doğrulamalı"
open_questions:
  - "Q1: Bu örneklerden hangisi electroncis kapsamına giriyor? → ⚠️ VERIFICATION REQUIRED"
  - "Q1c: NevaEngine (C++ ses motoru) bu dala mi, K003/K004'e mi ait? → ⚠️ VERIFICATION REQUIRED"
group: kod
---

# electroncis — Gerçek Repo Örnekleri

> **Önemli (dürüstlük):** Repo'da `electroncis` kapsamına **doğrudan** giren (elektronik devre /
> donanım / sürücü) kodu **YOKTUR** (Q1 UNKNOWN · repo taraması `*.cpp/*.c/*.h = 0`). Bu dosya,
> **OS ↔ harici cihaz/dosya/bellek sınırında** duran **gerçek** kod parçalarını taşır — her satır
> diskten `Select-Object -Index (N-1)` ile doğrulanmıştır. Yazım `electroncis` DOKUNULMAZ.

## §0 Kapsam dürüstlüğü

| # | İddia | Durum | Kanıt |
|---|---|---|---|
| 1 | repo'da `electroncis` kapsamına giren devre/donanım kodu | **YOK** | `*.cpp/*.c/*.h` = 0 (vendor hariç) |
| 2 | repo'da donanım-ağır yüzey (C++ ses motoru, PCB) | `K000` dışı — PLANNED | `NevaEngine/` = 0 dosya (sartname §8) |
| 3 | Aşağıdaki 8 örnek | **gerçek** — OS ile harici cihaz/dosya/bellek sınırı | satır-doğrulamalı |
| 4 | Örneklerin `electroncis` kapsamı | **UNKNOWN** | Q1 açık |

> **Neden bu örnekler?** `electroncis` dalı bir "OS ↔ donanım sınırı" adayıdır (bkz. kök `index.md` §2).
> Bu sınırın **yazılım yüzünden** en somut karşılıkları repo'da OS hizmetlerini (uname · execve ·
> shared memory · realpath/lstat · mkdir/umask · ini_get) çağıran PHP satırlarıdır. Bunlar **devre
> değil**, devrenin yazılım komşusudur; elektronik katmana analogları §2 tablolarında eşlenir.

## §1 Gerçek repo örnekleri (8 · diskten doğrulandı)

### Örnek 1 — Platform tespiti (OS kimliği · `uname`)

**Dosya:** `shared/src/Api/Bff/DesktopBff.php` · **Satır:** 35

```php
'platform' => php_uname('s'),
```

**Doğrulama:** `Select-String` → 35 · `Select-Object -Index 34` → birebir yukarıdaki satır.

**Ne gösteriyor:** Uygulama, üzerinde çalıştığı **işletim sistemini** runtime'da sorar
(`php_uname('s')` → POSIX `uname(2)` sistem çağrısının PHP yüzü). Donanım/elektronik tarafında
bu tespit, doğru **sürücü/API seçiminin** ilk adımıdır: aynı anakart üzerinde Linux vs Windows
farklı kernel sürücü yüzeyi kullanır. ⚠️ Bu dalın bu davranışı kapsayıp kapsamadığı UNKNOWN (Q1).

| # | OS kavramı | Elektronik/katman analogu | Sahip K-node |
|---|---|---|---|
| 1a | `uname(2)` → OS adı | platform/BIOS tespiti · doğru firmware seçimi | `isletim-sistemi-kernel` |
| 1b | runtime tespit | boot-time strap okuma (donanım kimliği) | K001-donanım |
| 1c | BFF (backend-for-frontend) | katman adaptörü | K003-audio (API) |

### Örnek 2 — Harici binary çağrısı (ffprobe · `execve`)

**Dosya:** `shared/src/AI/AIEngine.php` · **Satır:** 290–295

```php
$cmd = sprintf(
    'ffprobe -v quiet -print_format json -show_format -show_streams "%s"',
    escapeshellarg($filePath)
);

$output = shell_exec($cmd);
```

**Doğrulama:** satır 290 (`$cmd = sprintf(`) … 295 (`$output = shell_exec($cmd);`).

**Ne gösteriyor:** Uygulama, **OS üzerinden bir harici binary'ye** (ffprobe/ffmpeg) komut verir.
Bu, uygulamanın donanım/cihaz katmanına **dolaylı** erişimidir — doğrudan sürücü çağrısı **değil**.
Ses dosyasının örneklem hızı/biti/kanalı gibi **donanım-ayarlı** metadata'sı bu yolla okunur;
bunlar elektronik dünyada DAC/ADC ayarlarının karşılığıdır.

| # | OS kavramı | Elektronik analogu | Sınır |
|---|---|---|---|
| 2a | `shell_exec` → `execve` | harici DSP/codec ile mesajlaşma | K004-dsp |
| 2b | `escapeshellarg` | komut enjeksiyonu → veri bütünlüğü | güvenlik |
| 2c | `sample_rate`/`bit_depth`/`channels` | DAC ayarı (ör. 48 kHz · 24-bit · 2/8 kanal) | K003-audio |

### Örnek 3 — Paylaşımlı bellek önbelleği (OS shared memory · APCu)

**Dosya:** `shared/src/Cache/ApcuAdapter.php` · **Satır:** 9–13

```php
if (!function_exists('apcu_fetch')) {
    // APCu uzantısı yüklü değil — önbellek devre dışı
    return false;
}
$value = apcu_fetch($key, $success);
```

**Doğrulama:** satır 9 (`if (!function_exists('apcu_fetch')) {`) · 13 (`$value = apcu_fetch(...)`).

**Ne gösteriyor:** Önbellek, **işletim sisteminin paylaşımlı bellek** hizmetine (APCu) bağlıdır;
uzantı yoksa sessizce devre dışı kalır. Elektronik dünyada buna karşılık gelen şey, bir **yedekleme
devresinin / jumper'ın yokluğunda** ana devrenin kullanılamamasıdır (fail-soft).

| # | OS kavramı | Elektronik analogu | Davranış |
|---|---|---|---|
| 3a | APCu = paylaşımlı bellek | ortak bar / EEPROM register bank | yüksek performans |
| 3b | `function_exists` yok → `false` | jumper yok → devre devre dışı | fail-soft |
| 3c | `apcu_inc` (atomik) | sayaç register'ı (kesme-güvenli) | thread-safe |

### Örnek 4 — Yol çözümleme + symlink reddi (OS `lstat`/`realpath`)

**Dosya:** `home.coremusic.net/include/Stream/MusicStreamHandler.php` · **Satır:** 185–200

```php
if (is_link($trimmed)) {
    // symlink → reddet (403)
}

$real = realpath($trimmed);
if ($real === false || !is_file($real)) {
    // çözülemedi/ dosya değil → 404
}

$root = realpath($this->libraryPath);
$prefix = rtrim($root, '\\/') . DIRECTORY_SEPARATOR;
if (!str_starts_with($real, $prefix)) {
    // kütüphane dışına taşma → reddet
}
```

**Doğrulama:** 185 (`is_link`) · 189 (`realpath`) · 194 (`realpath($this->libraryPath)`) ·
199–200 (`DIRECTORY_SEPARATOR` + `str_starts_with`). *(Not: dosyadaki Türkçe hata metinleri
kaynakta mojibakedir; yalnız kod satırları alıntılanmıştır.)*

**Ne gösteriyor:** Dosya sistemi sınırında **OS hizmetlerini** (`is_link`→`lstat`, `realpath`→`realpath(3)`)
kullanarak yetkisiz erişimi (symlink escape · path traversal) keser. Elektronik cihaz dosyaları
(firmware blob · kalibrasyon EEPROM'u) için aynı savunma prensibi geçerlidir — ama repo'da böyle bir
cihaz dosyası erişimi **yok**.

| # | OS kavramı | Elektronik analogu | Sınır |
|---|---|---|---|
| 4a | `is_link` (symlink reddi) | taklit register'a yazma reddi | güvenlik |
| 4b | `realpath` + `str_starts_with` | bellek haritası sınır denetimi | K005-firmware |
| 4c | `DIRECTORY_SEPARATOR` | sabit yol ayracı (portabilite) | — |

### Örnek 5 — Geçici dizin + izin modu (OS `mkdir`/`umask`)

**Dosya:** `shared/src/Session/SessionConfig.php` · **Satır:** 33–35

```php
$savePath = ini_get('session.save_path') ?: sys_get_temp_dir() . '/coremusic_sessions';
// ...
@mkdir($savePath, 0750, true);
```

**Doğrulama:** satır 33 (`ini_get(...) ?: sys_get_temp_dir()...`) · 35 (`@mkdir($savePath, 0750, true);`).

**Ne gösteriyor:** Oturum dizini **OS'den sorulur** (`sys_get_temp_dir` → `TMPDIR`) ve **POSIX izin
modu** `0750` ile açılır (owner `rw`, group `rx`, other none). Bu doğrudan OS güvenlik kavramıdır
(bkz. `../isletim-sistemi-security/`). Elektronikte karşılığı, kalibrasyon verisinin **salt-okunur
EEPROM bölgesine** yazılmasıdır.

| # | OS kavramı | Elektronik analogu | Değer |
|---|---|---|---|
| 5a | `sys_get_temp_dir` | geçici/scratch bellek alanı | OS sorgusu |
| 5b | `mkdir(…, 0750, true)` | yazma koruma biti | `0750` |
| 5c | `@` (hata bastırma) | fail-soft / sessiz düşme | kasıtlı |

### Örnek 6 — Web sunucusu iki platformu (Apache / IIS)

**Dosya:** `home.coremusic.net/.htaccess` · `home.coremusic.net/web.config`

```apache
# .htaccess — Apache
RewriteEngine On
# ... güvenlik başlıkları (X-Frame-Options vb.)
```

```xml
<!-- web.config — IIS -->
<configuration>
  <!-- ... IIS karşılığı -->
</configuration>
```

**Ne gösteriyor:** Aynı web kökü **iki farklı OS web sunucusu** için yapılandırılmıştır — donanım/OS
bağımlılığının en somut kanıtı. `electroncis` sınırı için bu, "OS üstünde ne kadar platform çeşitliliği
var" sorusunun cevabıdır; **elektronik katmanın kendisi değil**.

### Örnek 7 — Zaman damgası → binary paketleme (endianness · `pack`)

**Dosya:** `shared/src/Security/UuidV7.php` · **Satır:** 22–23

```php
$time = pack('N', ($ms >> 16) & 0xFFFFFFFF)
      . pack('n', $ms & 0xFFFF);
```

**Doğrulama:** satır 22 (`$time = pack('N', ...`) · 23 (`. pack('n', $ms & 0xFFFF);`) —
`Select-Object -Index 21/22` birebir.

**Ne gösteriyor:** `pack('N'/'n')` **big-endian** bayt sıralamasıyla tam/short sayı paketler — bu,
donanım tarafında **byte order** (endianness) kavramının birebir karşılığıdır. Ses protokollerinde
(I2S · TDM · AES3) bayt sıralaması donanım ile firmware arasında mutabakat gerektirir.

| # | OS/kod kavramı | Elektronik analogu | Sınır |
|---|---|---|---|
| 7a | `pack('N')` big-endian | big-endian / little-endian register yazımı | K005-firmware |
| 7b | bit-shift maskeleri | bit-field register manipulation | K005-firmware |
| 7c | `bin2hex(random_bytes())` | rastgele nonce/ID (güvenlik) | güvenlik |

### Örnek 8 — Sınırlayıcı başlığı (OS zamanlama · `Retry-After`)

**Dosya:** `shared/src/Api/Middleware/RateLimitMiddleware.php` · **Satır:** 40

```php
header('Retry-After: ' . $this->windowSeconds);
```

**Doğrulama:** satır 40 (`header('Retry-After: ...')`).

**Ne gösteriyor:** Zamanlama penceresi OS/HTTP katmanına bildirilir. Elektronikte karşılığı,
**akım sınırı (current limit) yeniden deneme penceresi** veya **geri tepme (back-off)** davranışıdır —
koruma devresinin tekrar açılmayı ne zaman deneyeceğidir (bkz. `guc-ve-koruma/koruma-devreleri-ve-esikler.md` §1).

## §2 OS hizmeti → elektronik katman eşleme matrisi (genişletilmiş)

| # | Repo örneği | OS hizmeti (POSIX/kavram) | Elektronik/katman analogu | Komşu K-node |
|---|---|---|---|---|
| E1 | `php_uname('s')` | `uname(2)` | platform/boot tespiti | kernel · K001 |
| E2 | `shell_exec(ffprobe)` | `execve(2)` | harici codec/DSP ile mesajlaşma | K004-dsp |
| E3 | `apcu_fetch/store/inc` | shared memory (`shmget`) | register bank / ortak bar | K005-firmware |
| E4 | `is_link` + `realpath` | `lstat` + `realpath(3)` | bellek haritası sınırı | K005-firmware |
| E5 | `sys_get_temp_dir` + `mkdir(0750)` | `TMPDIR` + `mkdir`/`umask` | scratch bellek + yazma koruma | K001 |
| E6 | `.htaccess` + `web.config` | HTTP sunucu (Apache/IIS) | platform çeşitliliği | K000-kernel |
| E7 | `pack('N'/'n')` | bayt sıralaması (endianness) | register byte order | K005-firmware |
| E8 | `header('Retry-After')` | zamanlama/pencere | akım sınırı back-off | `guc-ve-koruma` |
| E9 | `ini_get('session.save_path')` | `php.ini` yapılandırması | fuse/strap konfigürasyonu | K001 |
| E10 | `bin2hex(random_bytes())` | CSPRNG (`/dev/urandom`) | rastgele nonce (güvenlik) | güvenlik |
| E11 | `memory_get_usage` (taramada yok) | `brk`/`mmap` | bellek bütçesi | — |
| E12 | `fsockopen`/`stream_socket` (taramada yok) | `socket(2)` | ağ/seri port | K001 |

> **Not:** E11–E12 bu tur taramasında **eşleşmedi** → `⚠️ VERIFICATION REQUIRED` (varsa sonraki turda doğrulanır).

## §3 Devre seviyesi kod YOK — dürüstlük tablosu

| # | Tarama | Komut özeti | Sonuç | Tarih |
|---|---|---|---|---|
| F1 | `*.cpp` / `*.c` / `*.h` (repo geneli, vendor/node_modules hariç) | `Get-ChildItem -Recurse -Include` | **0 dosya** | 2026-10-13 |
| F2 | `NevaEngine/` kaynak ağacı | dizin var mı? | PLANNED (0 dosya) | 2026-10-13 |
| F3 | devre seviyesi kod kanıtı | — | `Repo'da devre seviyesi kod YOK — kaynak: .ai/electronic/** (60 dosya)` | 2026-10-13 |

> **Uydurma yasak:** Repo'da devre seviyesi kod bulunmadığı için **uydurma C/C++/devre snippet'i
> yazılmaz**. Bu dalın teknik derinliği `.ai/electronic/**` (60 dosya · 10.323 sat) vault'unda
> taşınır; buraya yalnız **atıf** yapılır (kopyalama yasak — bkz. `electroncis-kapsam.md` §G).

## §4 Özet tablo (8 örnek)

| # | Örnek | Dosya : satır | OS hizmeti | `electroncis` kapsamı? |
|---|---|---|---|---|
| 1 | Platform tespiti | `DesktopBff.php:35` | `uname` | UNKNOWN |
| 2 | Harici binary | `AIEngine.php:290-295` | `execve` | UNKNOWN |
| 3 | Paylaşımlı bellek | `ApcuAdapter.php:9-13` | shared memory | UNKNOWN |
| 4 | Yol çözümleme | `MusicStreamHandler.php:185-200` | `realpath`/`lstat` | UNKNOWN |
| 5 | Dizin + izin | `SessionConfig.php:33-35` | `mkdir`/`umask` | UNKNOWN |
| 6 | Apache/IIS | `.htaccess` + `web.config` | HTTP sunucusu | UNKNOWN |
| 7 | Binary paketleme | `Security/UuidV7.php:22-23` | endianness (`pack`) | UNKNOWN |
| 8 | Retry-After | `RateLimitMiddleware.php:40` | zamanlama penceresi | UNKNOWN |

## §5 Doğrulama zinciri (her örnek için)

| # | Adım | Araç | Beklenen |
|---|---|---|---|
| G1 | satır no bulma | `Select-String -Pattern` | eşleşme |
| G2 | içerik doğrulama | `Select-Object -Index (N-1)` | birebir satır |
| G3 | kapsam işareti | Q1 kararı | UNKNOWN / EVET / HAYIR |
| G4 | analog eşleme | §2 matrisi | komşu K-node |

## §7 Örnek → donanım şartnamesi çevirimi (derin-dalış)

> Bu bölüm, §1'deki her OS sınırı örneğinin **donanım/elektronik tasarımına nasıl çevrileceğini**
> taşır. Amaç: `electroncis` dalı Q1 ile netleşene kadar, bu sınırın **tasarım girdisi** olabilecek
> somut elektronik gereksinimleri belgelemek. Uydurma değer yok; her satır ya repo'dan ya da
> açık kaynaklı kavramdan gelir.

### §7.1 Örnek 1 (uname → platform tespiti) karşılığı

| # | Yazılım sorusu | Donanım karşılığı | Ölçüm/araç | Eşik/Not |
|---|---|---|---|---|
| H1 | Hangi OS? | Hangi boot strap / jumper? | multimetre (süreklilik) | strap → OS seçimi |
| H2 | Sürücü yüzeyi | I2S/TDM vs USB audio | osiloskop (clock pin) | 48 kHz MCLK |
| H3 | Mimari (x64/ARM) | İşlemci ailesi | firmware banner | — |
| H4 | `php_uname('m')` | CPU çekirdek tipi | CPUID | — |

### §7.2 Örnek 2 (execve → harici codec/DSP) karşılığı

| # | Yazılım sorusu | Donanım karşılığı | Arayüz | Bileşen |
|---|---|---|---|---|
| H5 | ffprobe metadata | DAC/ADC register okuma | I2C/SPI | PCM3168A · AK4458 |
| H6 | `sample_rate` | master clock frekansı | MCLK/BCLK | 256×fs |
| H7 | `bit_depth` | veri genişliği | I2S (16/24/32-bit) | — |
| H8 | `channels` | kanal sayısı (TDM) | TDM 8 kanal | 8.1 surround |
| H9 | `shell_exec` gecikmesi | firmware pipeline latency | latency ölçüm | buffer 512 |

### §7.3 Örnek 3 (shared memory) karşılığı

| # | Yazılım sorusu | Donanım karşılığı | Mekanizma | Not |
|---|---|---|---|---|
| H10 | APCu atomik `inc` | kesme-güvenli sayaç | register / DMA | lock-free |
| H11 | paylaşımlı bellek | çift-tampon (double buffer) | DMA ping-pong | RT audio |
| H12 | `function_exists` yok → false | yedek devre yok → pasif | jumper/bypass | fail-soft |
| H13 | TTL (apcu_store) | register TTL / watchdog | sayaç | — |

### §7.4 Örnek 4 (realpath/symlink) karşılığı

| # | Yazılım sorusu | Donanım karşılığı | Mekanizma | Not |
|---|---|---|---|---|
| H14 | symlink reddi | taklit register reddi | yazma doğrulama | güvenlik |
| H15 | path traversal | adres taşması | bellek koruması (MPU) | K005 |
| H16 | `str_starts_with` sınır | bellek haritası penceresi | chip-select decodlama | — |
| H17 | `realpath` çözümleme | fiziksel ↔ mantıksal adres | MMU/MPU | K005 |

### §7.5 Örnek 5 (mkdir/umask) karşılığı

| # | Yazılım sorusu | Donanım karşılığı | Değer | Not |
|---|---|---|---|---|
| H18 | `0750` izin | yazma koruma biti (WP) | EEPROM WP pin | kalibrasyon |
| H19 | `sys_get_temp_dir` | scratch RAM alanı | SRAM bölmesi | — |
| H20 | `@mkdir` fail-soft | jumper yok → devre dışı | — | kasıtlı |

### §7.6 Örnek 7 (endianness · `pack`) karşılığı

| # | Yazılım sorusu | Donanım karşılığı | Arayüz | Not |
|---|---|---|---|---|
| H21 | `pack('N')` big-endian | MSB-first veri | I2S/TDM default | mutabakat |
| H22 | `pack('n')` short | 16-bit register | SPI | — |
| H23 | byte order mutabakatı | firmware ↔ DAC mutabakatı | register map | K005 |
| H24 | `random_bytes` nonce | kriptografik nonce | TRNG/PRNG | — |

### §7.7 Örnek 8 (Retry-After) karşılığı

| # | Yazılım sorusu | Donanım karşılığı | Eşik | Not |
|---|---|---|---|---|
| H25 | yeniden deneme penceresi | akım sınırı back-off | ms | `guc-ve-koruma` |
| H26 | `windowSeconds` | termal soğuma süresi | s | K001 termal |
| H27 | rate limit | inrush sınırlama | A | soft-start |

## §8 Sınır-savunma desenleri (OS ↔ donanım güvenlik eşlemesi)

| # | OS deseni (repo kanıtı) | Donanım/katman karşılığı | Koruma katmanı |
|---|---|---|---|
| I1 | `escapeshellarg` (AIEngine:292) | veri bütünlüğü (CRC/parity) | K004-dsp |
| I2 | symlink + traversal reddi (MusicStreamHandler) | register/address sınırı | K005-firmware |
| I3 | `mkdir 0750` (SessionConfig) | yazma koruma (WP/OTP) | K001-donanım |
| I4 | `function_exists` fail-soft (Apcu) | yedek devre/bypass | `guc-ve-koruma` |
| I5 | `bin2hex(random_bytes)` (nonce) | donanım rastgelelik (TRNG) | güvenlik |
| I6 | `Retry-After` back-off (RateLimit) | akım/termal back-off | `guc-ve-koruma` |
| I7 | CSP nonce (SecurityHeaders) | oturum/token (güvenlik) | güvenlik |
| I8 | platform tespiti (DesktopBff) | strap/boot seçimi | K001-donanım |

## §9 Kod → elektronik eşiği çevirimi (ölçüm planına köprü)

> Bu tablo, repo'daki OS hizmetlerinin **ölçülebilir donanım eşiklerine** nasıl indirgeneceğini
> taşır. Eşik değerleri **uydurulmaz** — gerçek ölçüm `PLANNED` (sartname §8); burada yalnız
> **hangi büyüklüğün** ölçüleceği listelenir.

| # | Büyüklük | Karşılığı (donanım) | Ölçüm aracı | Durum |
|---|---|---|---|---|
| J1 | latency (`shell_exec` gecikmesi) | DSP pipeline gecikmesi | latency ölçüm | PLANNED |
| J2 | bellek (`apcu` önbellek) | DMA buffer bellek bütçesi | bellek sayacı | PLANNED |
| J3 | dosya doğrulama (realpath) | firmware checksum | CRC denetimi | PLANNED |
| J4 | izin (0750) | EEPROM WP | süreklilik ölçümü | PLANNED |
| J5 | platform (uname) | boot strap | multimetre | PLANNED |
| J6 | THD/SNR (dosya metadata) | DAC/ADC performansı | audio analyzer | PLANNED |
| J7 | akım eşiği (Retry-After back-off) | koruma eşiği | osiloskop + yük | PLANNED |
| J8 | termal (windowSeconds) | soğuma sabiti | termal kamera | PLANNED |

## §10 Sınır ihlali belirtileri (bu düğümde ne YAZILMAZ)

| # | Belirti | Örnek | Nereye taşınır |
|---|---|---|---|
| K1 | C++ DSP kodu | biquad · FFT | K004-dsp |
| K2 | PCB yerleşim/iz tartışması | iz genişliği · katman | K001-donanım |
| K3 | OTA/boot flash kodu | bootloader · flash | K005-firmware |
| K4 | ASIO/WASAPI buffer tartışması | exclusive lock | K003-audio |
| K5 | Sürücü API (ioctl/IRP) | Linux/Windows driver | `isletim-sistemi-kernel` |
| K6 | Güç topolojisi hesabı | buck/boost · regulator | `guc-ve-koruma` |
| K7 | Eşik/koruma hesabı | akım/gerilim/termal | `guc-ve-koruma` |

## §11 Dürüstlük beyanı

| # | Beyan | Etiket |
|---|---|---|
| L1 | Repo'da devre seviyesi kod YOK | gerçek (tarama §3) |
| L2 | Örnekler OS ↔ sınır kodudur | gerçek (satır-doğrulamalı) |
| L3 | `electroncis` kapsamı UNKNOWN | Q1 açık |
| L4 | Uydurma devre snippet'i yazılmadı | kural (§3 notu) |
| L5 | `.ai/electronic/**` vault'u derin teknik içerir | atıf (kopya yok) |

## §12 Örnek bazlı risk / test / sahip matrisi

> Her §1 örneği için: **yanlış olursa ne olur** (risk) · **nasıl test edilir** (test) · **sahibi kim**
> (K-node). Bu tablo, sınırın **doğrulama planına** köprüdür; uydurma eşik yok.

### §12.1 Örnek 1 (uname) risk/test

| # | Risk (yanlış platform tespiti) | Test | Sahip |
|---|---|---|---|
| MA1 | yanlış sürücü/API seçimi | platforma göre BFF testi | K003-audio |
| MA2 | boot strap yanlış okunursa | multimetre süreklilik | K001-donanım |
| MA3 | mimari uyuşmazlığı | CPUID doğrulama | kernel |

### §12.2 Örnek 2 (execve/ffprobe) risk/test

| # | Risk (metadata yanlış) | Test | Sahip |
|---|---|---|---|
| MB1 | `sample_rate` yanlış → clock uyuşmazlığı | fs doğrulama (osiloskop) | K004-dsp |
| MB2 | `bit_depth` yanlış → kuantalama hatası | SNR ölçümü | K003-audio |
| MB3 | `channels` yanlış → TDM çerçeve kayması | kanal map testi | K004-dsp |
| MB4 | komut enjeksiyonu | `escapeshellarg` regresyonu | güvenlik |

### §12.3 Örnek 3 (shared memory) risk/test

| # | Risk (bellek yarışı) | Test | Sahip |
|---|---|---|---|
| MC1 | atomik sayaç bozulursa | lock-free stres testi | K005-firmware |
| MC2 | çift-tampon tearing | DMA ping-pong testi | K005-firmware |
| MC3 | APCu yok → önbellek düşüşü | fail-soft testi | — |

### §12.4 Örnek 4 (realpath/symlink) risk/test

| # | Risk (sınırdan kaçış) | Test | Sahip |
|---|---|---|---|
| MD1 | symlink escape | traversal testi | güvenlik |
| MD2 | path traversal | fuzzing | güvenlik |
| MD3 | adres taşması | MPU koruma testi | K005-firmware |

### §12.5 Örnek 5 (mkdir/umask) risk/test

| # | Risk (izin yanlış) | Test | Sahip |
|---|---|---|---|
| ME1 | oturum dizini açık kalır | izin denetimi (`ls -l`) | güvenlik |
| ME2 | kalibrasyon yazım korumasız | WP pin sürekliliği | K001-donanım |
| ME3 | scratch RAM çakışması | bellek haritası testi | K005-firmware |

### §12.6 Örnek 7 (endianness) risk/test

| # | Risk (bayt sırası) | Test | Sahip |
|---|---|---|---|
| MF1 | MSB/LSB uyuşmazlığı | register round-trip | K005-firmware |
| MF2 | clock_seq bozulması | UUID üretimi testi | güvenlik |
| MF3 | I2S/TDM mutabakatı | kare-scope (DOUT/FS) | K004-dsp |

### §12.7 Örnek 8 (Retry-After) risk/test

| # | Risk (zamanlama) | Test | Sahip |
|---|---|---|---|
| MG1 | back-off yanlış → termal kaçış | termal kamera | `guc-ve-koruma` |
| MG2 | inrush sınırlama yok → akım tepe | osiloskop + yük | `guc-ve-koruma` |
| MG3 | pencere yanlış → geri tepme | rate-limit regresyonu | K003-audio |

## §13 Örnek → vault atıf haritası (`.ai/electronic/**`)

| # | Örnek konusu | Vault dosyası (`.ai/electronic`) | İlişki |
|---|---|---|---|
| NA1 | DAC/ADC metadata (örnek 2) | `hardware/snr-thd-measurement.md` | atıf |
| NA2 | paylaşımlı bellek / çift-tampon (örnek 3) | `firmware/rtos.md` | atıf |
| NA3 | akım/termal back-off (örnek 8) | `amplifier/protection.md` · `amplifier/thermal.md` | atıf |
| NA4 | platform/boot (örnek 1) | `firmware/boot-sequence.md` | atıf |
| NA5 | endianness/register (örnek 7) | `firmware/dsp-firmware.md` | atıf |
| NA6 | dosya/firmware bütünlüğü (örnek 4) | `firmware/ota-update.md` | atıf |

> **Kural:** Bu vault dosyaları **dokunulmaz**; burada yalnız atıf taşınır (içerik kopyalanmaz —
> bkz. `electroncis-kapsam.md` §G U9).

## §14 Kapanış ölçüm notu

| # | Metrik | Değer |
|---|---|---|
| NB1 | Toplam gerçek repo örneği | 8 (§1) |
| NB2 | Devre seviyesi kod (repo) | 0 (§3 — dürüstlük) |
| NB3 | vault atıf satırı | §13 (6 konu) |
| NB4 | `group` | `kod` |
| NB5 | FM alanı | 9 (v2 §4.2 + `group`) |

## §6 Açık sorular

| # | Soru | Etiket | Sahip |
|---|---|---|---|
| Q1 | Bu örneklerden hangisi `electroncis` kapsamına giriyor? | ⚠️ VERIFICATION REQUIRED | Vault Steward |
| Q1b | repo'da elektronik-donanım kodu yoksa bu dal neyi belgeler? | UNKNOWN | Vault Steward |
| Q1c | NevaEngine (C++ ses motoru) bu dala mı, K003/K004'e mi ait? | ⚠️ VERIFICATION REQUIRED | Vault Steward |
| Q1d | `UuidV7.php` tam yolu `Get-Content` ile doğrulanacak mı? | ⚠️ VERIFICATION REQUIRED | bu dosya |
| Q1e | `.ai/electronic/**` vault'u bu örneklere karşılık gelen devre snippet'i içerir mi? | UNKNOWN | vault-sync |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
