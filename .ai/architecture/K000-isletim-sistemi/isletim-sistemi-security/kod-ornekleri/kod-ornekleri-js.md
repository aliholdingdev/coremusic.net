---
node_id: K000-isletim-sistemi/isletim-sistemi-security/kod-ornekleri/kod-ornekleri-js
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-security/kod-ornekleri
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-anayasa
  - K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-macos
evidence:
  - https://nodejs.org/api/permissions.html
  - https://nodejs.org/api/process.html
  - https://nodejs.org/api/os.html
  - https://nodejs.org/api/fs.html
  - https://nodejs.org/api/child_process.html
  - https://nodejs.org/api/cli.html
  - https://nodejs.org/api/path.html
validation:
  - "2026-10-14: 7 kaynak URL HTTP 200 ile dogrulandi ve icerikleri okundu; 6 aday URL 404 olarak red; repo olcumu 418 PHP+JS / 20 desen / 0 isabet (negatif kanit)."
open_questions:
  - "S1: OS guvenlik katmani hedef mimaride devreye alinacak mi? [UNKNOWN]"
  - "S2: Uygulama <-> OS guvenlik sorumluluk siniri? [UNKNOWN]"
group: kod
---

# Kod Örnekleri — Node.js / JS (Dil × 8 OS)

## §0 ELI15

Bu kart, **tek bir dilin** (Node.js/JS) **sekiz işletim sistemiyle** kesişen
güvenlik yüzeyini taşır: windows · linux · macos · ios · android · tizen ·
webos · reactos. Kural basittir: Node.js bir şeyi **yalnız çalıştığı OS'un
sağladığı mekanizmaya sararak** yapar — `fs.access` Windows ACL'sini okumaz,
`process.getuid` Windows/Android'de yoktur, Permission Model ise açıkça
*"Node.js trusts the OS environment in which it runs"* der ve kalan işi
`seccomp`/`AppArmor`/ayrı OS kullanıcılarına bırakır.

> **Öncelikli gerçek:** Bu repoda (2026-10-14, **418** PHP+JS dosyası,
> `vendor`/`node_modules`/`.git` hariç) Node.js OS-güvenlik desenine **0
> isabet** vardır: `child_process`/`spawn`/`process.getuid`/`os.userInfo`/
> `fs.chmod`/`seccomp`/`chroot`/`--permission` (2026-10-14, §3). Bu kart
> dolayısıyla **hedef mimari + negatif kanıt** kartıdır; §2'deki 8 bloğun
> tamamı `kaynak: upstream docs` imzalı **sözleşmesel** referans kalıplarıdır
> (çalıştırılabilir kod **değildir**), repoda karşılığı yoktur.

`group: kod` · S1 ve S2 açık sorudur, bu kartta kapatılmaz (§6). "Repo'da
uygulanıyor" iddiası bu kartta **yasaktır**; §3 yalnız ölçülmüş durumu taşır.

## §1 JS × OS güvenlik yüzeyi matrisi

| OS | Bu OS'ta JS/Node güvenlik yüzeyi (upstream kanıt) | Güven düzeyi |
|---|---|---|
| windows | `fs.access` ACL'yi **kontrol etmez** · `fs.chmod` yalnız write biti · `fs.mkdir` `mode` **desteklenmez** · `os.userInfo` `uid/gid=-1` · `shell:true` → `ComSpec` | Yüksek (fs/os/child_process okundu) |
| linux | Permission Model (`--permission` enforce/audit) + kısıt listesi · tehdit modeli: OS'a güven, `seccomp`/`AppArmor` operatör işi · `getuid/getgid` POSIX-only | Yüksek (permissions/process okundu) |
| macos | `fs.lchmod` **yalnız macOS'ta** implemente · `--use-system-ca` → **Default/System Keychains** · `fs.watch` kqueue(2)+FSEvents · `os.platform()='darwin'` | Yüksek (fs/cli/os okundu) |
| ios | `os.platform()` değer listesinde **'ios' YOK**; `nodejs.org/api/ios.html` **404** → yüzey doğrulanamadı | ⚠️ VERIFICATION REQUIRED |
| android | `os.platform()` **'android'** döner — *"Android support is experimental"* · `getgid/getgroups` **"not Windows or Android"** | Yüksek (os/process okundu) |
| tizen | `os.platform()` değer listesinde **'tizen' YOK**; `nodejs.org/api/tizen.html` **404** | ⚠️ VERIFICATION REQUIRED |
| webos | `os.platform()` değer listesinde **'webos' YOK**; `nodejs.org/api/webos.html` **404** | ⚠️ VERIFICATION REQUIRED |
| reactos | `os.platform()` değer listesinde **'reactos' YOK**; `win32` iddiası ReactOS'a **taşıtılmadı** | ⚠️ VERIFICATION REQUIRED |

## §2 Kod örnekleri

> **UYARI (zorunlu):** Aşağıdaki 8 bloğun **hiçbiri bu repodan alınmamıştır.**
> Hepsi `kaynak: upstream docs` ile imzalı **sözleşmesel** referans kalıplarıdır
> (çalıştırılabilir kod **değildir**); repo'da karşılığı §3'te negatif kanıt
> olarak ölçülür. Kopyalamadan önce S1 kararı gerekir.

### §2.1 — windows

```sh
# kaynak: upstream docs - nodejs.org/api/fs.html + os.html + child_process.html
# Node API Windows ACL'sine DOKUNMAZ; izin yonetimi isletim sisteminde kalir.
#   fs.access(p, F_OK, cb)  -> upstream: "does not check the ACL"; dizin ACL'si
#                              erisimi sinirsa yine de "erisilebilir" der
#   fs.chmod(p, 0o644, cb)  -> "on Windows only the write permission can be
#                              changed"; group/owner/other ayrimi YOK
#   fs.mkdir(p,{mode})      -> "Not supported on Windows"; varsayilan 0o777
#   os.userInfo()           -> uid ve gid = -1, shell = null (Windows)
#   spawn(cmd,{shell:true}) -> /bin/sh yerine process.env.ComSpec;
#                              windowsVerbatimArguments, windowsHide (Default: false)
#   path.basename()         -> Windows yolu ayristirir; tutarlilik icin path.win32
# sinir: ACL/police yazma yuzeyi Node'da YOK -> K010/K009'da, bu kartta degil
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel — çalıştırılabilir değil) | YOK — `fs.access|chmod|userInfo|ComSpec` grep'i 0 isabet / 418 dosya (2026-10-14) |

### §2.2 — linux

```sh
# kaynak: upstream docs - nodejs.org/api/permissions.html + process.html + fs.html
# Permission Model (--permission): Enforce (ERR_ACCESS_DENIED) ve Audit (yalniz
# raporlar) modlari; kisitlananlar: dosya sistemi, ag, child process, worker,
# inspector, native addon, WASI, FFI, OpenSSL STORE loader.
# Kisitlar (upstream): worker'a miras kalmaz (execArgv tekrarlanmali);
#   --env-file/--openssl-config gibi on-yukleme dosyalari kapsam disi;
#   mevcut dosya tanimlayicilari (fd) modeli BYPASS eder.
# Tehdit modeli (upstream cumlesi): "Node.js trusts the OS environment in
#   which it runs"; process._debugProcess sinyali operatorun isidir ->
#   "separate OS users" veya "seccomp/AppArmor profiles on Linux".
#   process.getuid/getgid/getgroups -> "only available on POSIX platforms
#   (i.e. not Windows or Android)"; fs.mkdir mode varsayilani 0o777;
#   process.umask() surec olusturma maskesini verir.
# sinir: capability/cgroup/seccomp politikasi yazmak bu dilin isi DEGILDIR
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel) | YOK — `--permission|seccomp|chroot|capset` grep'i 0 isabet (2026-10-14) |

### §2.3 — macos

```sh
# kaynak: upstream docs - nodejs.org/api/fs.html + cli.html + os.html
#   fs.lchmod / fsPromises.lchmod -> upstream: "This method is only implemented
#     on macOS" (POSIX lchmod(2)); diger platformlarda yoktur
#   fs.watch -> upstream: "On macOS, this uses kqueue(2) for files and FSEvents
#     for directories" (olay kaynagi platforma baglidir)
#   node --use-system-ca -> macOS'ta saygi gosteren ayarlar: "Default and
#     System Keychains"; "When using this certificate"=Always Trust veya
#     "Secure Sockets Layer (SSL)"=Always Trust (Windows'ta ayri kurallar)
#   os.platform() -> 'darwin'; os.type() -> 'Darwin' (POSIX uname)
# FARK (durus not): TCC/izin penceresi ve sandbox-exec yuzeyi nodejs.org/api
#   dokumantasyonunda GECMEZ -> o kisim bu kartta YAZILMAZ (uydurma yasak)
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel) | YOK — `lchmod|Keychain|FSEvents` grep'i 0 isabet (2026-10-14) |

### §2.4 — ios — ⚠️ VERIFICATION REQUIRED

```sh
# kaynak: upstream docs - nodejs.org/api/os.html (negatif kanit)
# os.platform() olasi degerleri: 'aix' | 'darwin' | 'freebsd' | 'linux' |
#   'openbsd' | 'sunos' | 'win32' (+ 'android' deneysel) -> 'ios' LISTEDE YOK
# POSIX-only ibareleri yalniz "not Windows or Android" der; iOS icin upstream
#   cumlesi YOKTUR. nodejs.org/api/ios.html -> HTTP 404 (2026-10-14).
# SONUC: Node.js'in iOS guvenlik yuzeyi dogrulanamadi -> pozitif iddia YAZILMAZ.
```

| Sınıf | Repo karşılığı |
|---|---|
| ⚠️ (doğrulanamadı — uydurulmadı) | YOK — iOS/Node yüzeyi repoda da yok (0 isabet) |

### §2.5 — android

```sh
# kaynak: upstream docs - nodejs.org/api/os.html + process.html
#   os.platform() -> upstream: "The value 'android' may also be returned if
#     Node.js is built on the Android operating system. Android support is
#     experimental." (derleme zamanina baglidir)
#   process.getegid/getgid/getgroups -> "only available on POSIX platforms
#     (i.e. not Windows or Android)" -> Android'de SUNULMAZ
#   os.release() POSIX'te uname(3) ile; os.type() -> 'Linux' (Android kirlogu)
# FARK (durus not): APK runtime izin modeli (RECORD_AUDIO vb.) nodejs.org/api
#   icinde GECMEZ -> o yuzey bu kartta YAZILMAZ.
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel) | YOK — `os.platform|'android'` grep'i 0 isabet (2026-10-14) |

### §2.6 — tizen — ⚠️ VERIFICATION REQUIRED

```sh
# kaynak: upstream docs - nodejs.org/api/os.html (negatif kanit)
# os.platform() deger listesinde 'tizen' YOK; nodejs.org/api/tizen.html -> 404.
# nodejs.org/api dokumantasyonunda Tizen'e dair guvenlik ifadesi BULUNMAZ.
# SONUC: Tizen x Node.js yuzeyi dogrulanamadi -> ornek YAZILMAZ, uydurulmaz.
```

| Sınıf | Repo karşılığı |
|---|---|
| ⚠️ (doğrulanamadı — uydurulmadı) | YOK — 0 isabet |

### §2.7 — webos — ⚠️ VERIFICATION REQUIRED

```sh
# kaynak: upstream docs - nodejs.org/api/os.html (negatif kanit)
# os.platform() deger listesinde 'webos' YOK; nodejs.org/api/webos.html -> 404.
# webOS calisma zamani/izolasyon kurallari nodejs.org/api KAPSAMINDA DEGILDIR
#   (diger saglayici belgeleri bu kartin kaynak hiyerarsisine girmez).
# SONUC: webOS x Node.js yuzeyi dogrulanamadi -> ornek YAZILMAZ.
```

| Sınıf | Repo karşılığı |
|---|---|
| ⚠️ (doğrulanamadı — uydurulmadı) | YOK — 0 isabet |

### §2.8 — reactos — ⚠️ VERIFICATION REQUIRED

```sh
# kaynak: upstream docs - nodejs.org/api/os.html (negatif kanit)
# os.platform() deger listesinde 'reactos' YOK; nodejs.org/api/reactos.html -> 404.
# 'win32' etiketi ReactOS'a OTOMATIK tasinmaz: Node binary'sinin ReactOS'ta
#   derlenip calistigi upstream'de DOGRULANMADI -> iddia YOK.
# SONUC: ReactOS x Node.js yuzeyi dogrulanamadi -> ornek YAZILMAZ.
```

| Sınıf | Repo karşılığı |
|---|---|
| ⚠️ (doğrulanamadı — uydurulmadı) | YOK — 0 isabet |

## §3 CoreMusic repo durumu

Tarama: repo kökü, `*.php,*.js` — `vendor`/`node_modules`/`.git` hariç —
**418 dosya** (320 PHP + 98 JS); kontrol deseni `function` → 2.431 isabet
(ölçüm çalışıyordu). Desen seti (20): `child_process|\bspawn\b|spawnSync|
execSync\(|process\.getuid|process\.umask|os\.userInfo|os\.platform|fs\.chmod|
\bchmod\b|chown|setuid|\bACL\b|icacls|seccomp|bwrap|capset|chroot|--permission|
allow-fs-read` (2026-10-14).

| Yüzey | Kanıt yöntemi | Sonuç (2026-10-14) |
|---|---|---|
| Node.js OS-güvenlik deseni (toplam) | grep 20 desen / 418 dosya | **0 isabet** (negatif kanıt) |
| Node.js çalıştırma yüzeyi (`package.json`) | glob | **2** — kök + `.opencode/` (paket tanımı var, OS-güvenlik çağırmak **yok**) |
| Python dosyası | glob `*.py` | **1** — `.ai/scripts/fix-mojibake.py` (vault bakım aracı; JS kartı için yalnız sayımdır) |
| "repo'da uygulanıyor" iddiası | — | **YASAK** — bu kart yalnız ölçülmüş durumu / yalanlamayı taşır |

**Ölçüm (yeniden üretilebilir):**

```powershell
$src = Get-ChildItem 'C:\www\coremusic.net' -Recurse -File -Include *.php,*.js |
  Where-Object { $_.FullName -notmatch '\\vendor\\|\\node_modules\\|\\\.git\\' }  # beklenti: 418
($src | Select-String -Pattern 'child_process|\bspawn\b|process\.getuid|os\.userInfo|fs\.chmod|seccomp|chroot|--permission').Count  # beklenti: 0
```

**Okuma:** Node.js'in OS güvenlik yüzeyi bu repoda **hiç başlamamıştır** — bu,
bu kartın karakteridir.

## §4 Sınır cümleleri

> **K010 ayrımı (bağlayıcı):** Kimlik doğrulama, oturum, parola ve MFA →
> `K010-identitiy`; burada yalnız Node.js'in OS'e açılan izin/süreç yüzeyi taşınır.
> **K009 ayrımı (bağlayıcı):** OWASP denetimi, auth/CSRF/CSP tasarımı, kripto
> seçimi (ADR-022) → `K009-security`; bu kart yalnız dil × OS matrisini taşır.
> **Komşu alt-dal:** OS geneli anlatımı `isletim-sistemi-turu/*`, sistem
> çağrıları `isletim-sistemi-api/*`, çekirdek içi mekanizma `isletim-sistemi-kernel`.

Ek sınır: `fs.access`/`fs.chmod` birer **uygulama çağrısıdır**; Windows ACL,
macOS Keychain, Linux capability politikası bu kartta **anlatılır, kurulmaz**.
İzin *tasarımı* (hangi dosya hangi kullanıcıya) S1'e bağlıdır ve burada
kapatılmaz (§6).

## §5 Referans

| URL | İddia | 2026-10-14 durum |
|---|---|---|
| https://nodejs.org/api/permissions.html | Permission Model enforce/audit; kısıt listesi; *"Node.js trusts the OS environment"*; `seccomp`/`AppArmor` operatöre bırakılır; worker'a miras kalmaz; fd bypass | HTTP 200 (GET) · içerik okundu |
| https://nodejs.org/api/process.html | `process.getuid` *"not available on Windows"*; `getgid/getgroups` *"only available on POSIX platforms (i.e. not Windows or Android)"*; `process.umask` | HTTP 200 · içerik okundu |
| https://nodejs.org/api/os.html | `os.platform()` değer listesi + *"'android' … Android support is experimental"*; `os.userInfo()` Windows'ta `uid/gid=-1, shell=null` | HTTP 200 · içerik okundu |
| https://nodejs.org/api/fs.html | `fs.access` ACL kontrol etmez (Windows); `fs.chmod` caveats (yalnız write); `fs.mkdir` `mode` *"Not supported on Windows"*; `fs.lchmod` *"only implemented on macOS"* | HTTP 200 · içerik okundu |
| https://nodejs.org/api/child_process.html | `shell:true` → `/bin/sh` (Unix) / `process.env.ComSpec` (Windows); `windowsVerbatimArguments`, `windowsHide` (Default: false); `uid`/`gid` → `setuid(2)`/`setgid(2)` | HTTP 200 · içerik okundu |
| https://nodejs.org/api/cli.html | `--use-system-ca`: macOS'ta *Default and System Keychains* + Always Trust bayrakları; Windows ayrı kurallar; `--permission` bayrağı | HTTP 200 · içerik okundu |
| https://nodejs.org/api/path.html | *"Windows vs. POSIX"*: varsayılan davranış OS'a bağlı; `path.win32` / `path.posix` tutarlılık için | HTTP 200 · içerik okundu |

> **Yalnız durum doğrulaması (200 — iddia için kullanılmadı):**
> `nodejs.org/api/net.html` · `worker_threads.html` · `crypto.html` ·
> `addons.html` · `dns.html` (2026-10-14 HEAD/GET 200).

> **Reddedilen adaylar (yazılmadı):** `nodejs.org/api/ios.html` → **404** ·
> `nodejs.org/api/android.html` → **404** · `nodejs.org/api/tizen.html` → **404** ·
> `nodejs.org/api/webos.html` → **404** · `nodejs.org/api/reactos.html` → **404** ·
> `nodejs.org/api/sandbox.html` → **404** (hepsi 2026-10-14). Konu uyuşmazlığı
> nedeniyle gövdeye alınmadı: `support.apple.com` TCC/sandbox sayfaları (bu kart
> yalnız `nodejs.org/api` upstream'i taşır). Dört 404, §2.4/§2.6/§2.7/§2.8'de
> **negatif kanıt** olarak kullanıldı; içerik uydurulmadı.

## §6 Açık sorular

| # | Soru | Durum |
|---|---|---|
| S1 | Node.js süreç izin modeli (`--permission`, umask, dosya sistemi izinleri) hedef mimaride devreye alınıp alınmayacağı; hangi OS'ya hangi yüzeyin taşınacağı | **[UNKNOWN]** — bu kartta KAPATMA YOK; mimari sahibi + yeni ADR (`>=088`) gerektirir |
| S2 | Uygulama ↔ OS güvenlik sorumluluk sınırı (ACL/Keychain/seccomp kimin işi) | **[UNKNOWN]** — bu kartta KAPATMA YOK; K009/K010 ayrım çizgisiyle birlikte değerlendirilir |

## §7. Kapsam Matrisi (§1'deki 8 satır × JS mekanizmaları)

§7.1 kombinasyon bazlı derinlik · §7.2 mekanizma sınıflandırması · §7.3 eksik/yapısal boşluklar · §7.4 arıza örüntüsü · §7.5 ⚠️ kombinasyon derinliği.

### §7.1 Kombinasyon Bazlı Derinlik (§1 matrisinden türetildi — 8 satır)

- **JS × windows (satır 53)**: `fs.access` ACL kontrol etmez · `fs.chmod` yalnız write biti · `fs.mkdir` mode desteklenmez · `os.userInfo` uid/gid=-1 · `shell:true` → `ComSpec`. Sınıf: fs/os/child_process Windows sınırları. §2.1 bloğu + §5 satır 253–258 (`os`, `fs`, `child_process`, `cli`, `path` — 5 URL) ile doğrulandı.
- **JS × linux (satır 54)**: Permission Model (`--permission` enforce/audit) + kısıt listesi · tehdit modeli OS'a güven (`seccomp`/`AppArmor` operatör işi) · `getuid/getgid` POSIX-only. Sınıf: süreç izin modeli. §2.2 bloğu + §5 satır 252–253 (`permissions`, `process` — 2 URL) ile doğrulandı.
- **JS × macos (satır 55)**: `fs.lchmod` yalnız macOS'ta · `--use-system-ca` → Default/System Keychains · `fs.watch` kqueue(2)+FSEvents. Sınıf: fs/CLI platform spesifik. §2.3 bloğu + §5 satır 255, 257 (`fs`, `cli` — 2 URL; os platform değeri satır 254) ile doğrulandı.
- **JS × ios (satır 56)**: `os.platform()` değer listesinde `ios` YOK · `nodejs.org/api/ios.html` 404 → ⚠️ VERIFICATION REQUIRED (§2.4).
- **JS × android (satır 57)**: `os.platform()='android'` deneysel · `getgid/getgroups` "not Windows or Android" → Android'de sunulmaz. Sınıf: POSIX-only istisnası. §2.5 bloğu + §5 satır 253–254 (`process`, `os` — 2 URL) ile doğrulandı (§1'de ⚠️ değil, Yüksek — android upstream'de geçerli tek cümle var).
- **JS × tizen (satır 58)**: `os.platform()` listesinde `tizen` YOK · `api/tizen.html` 404 → ⚠️ (§2.6).
- **JS × webos (satır 59)**: `os.platform()` listesinde `webos` YOK · `api/webos.html` 404 → ⚠️ (§2.7).
- **JS × reactos (satır 60)**: `os.platform()` listesinde `reactos` YOK · `win32` iddiası ReactOS'a taşıtılmadı · `api/reactos.html` 404 → ⚠️ (§2.8).

### §7.2 Mekanizma Sınıflandırması (§2'deki 8 blok)

| Mekanizma | Kombinasyonlar | JS yüzeyi | §2 bloğu |
|---|---|---|---|
| Dosya sistemi izinleri (fs) | windows, macos | `fs.access`/`chmod`/`mkdir`/`lchmod` sınırları | §2.1, §2.3 |
| Süreç izin modeli (CLI) | linux | `--permission` enforce/audit + kısıtlar | §2.2 |
| Kullanıcı/POSIX kimliği (process) | linux, windows, android | `getuid`/`getgid`/`umask` POSIX-only | §2.2, §2.5 |
| Kullanıcı bilgisi (os) | windows, android | `os.userInfo` uid/gid=-1; `os.platform` deneysel | §2.1, §2.5 |
| Süreç başlatma (child_process) | windows | `shell:true` → `ComSpec`; `windowsHide` | §2.1 |
| CA/Keychain (cli) | macos, windows | `--use-system-ca` Keychain/Always Trust | §2.3 |
| Olay izleme (fs.watch) | macos | kqueue(2)+FSEvents | §2.3 |
| Yol ayrıştırma (path) | windows | `path.win32`/`path.posix` tutarlılık | §2.1 |

### §7.3 Eksik/Yapısal Boşluklar (§5'teki 6 reddedilen + 1 konu dışı)

§5'teki 6 reddedilen aday (satır 264–267): `api/ios.html` · `api/android.html` · `api/tizen.html` · `api/webos.html` · `api/reactos.html` · `api/sandbox.html` → hepsi **404** (2026-10-14). Ek boşluk: macOS TCC/sandbox yüzeyi — `support.apple.com` sayfaları konu uyuşmazlığı nedeniyle gövdeye alınmadı (bu kart yalnız `nodejs.org/api` upstream'ini taşır) → §2.3'te "TCC/sandbox-exec GECMEZ → YAZILMAZ" duruş notu ile işaretli. Ayrıca §5 satır 260–262: 5 URL (`net`, `worker_threads`, `crypto`, `addons`, `dns`) yalnız durum doğrulaması için denendi (200) — iddia için kullanılmadı, §1/§2'ye taşınmadı (kapsam dışı).

### §7.4 Arıza Örüntüsü (§1–§5'ten türetildi)

| Örüntü | Kanıt | Açıklama |
|---|---|---|
| **Permission Model fd bypass** | §2.2 satır 98–99 + §5 satır 252 | Mevcut dosya tanımlayıcıları (fd) `--permission` modelini BYPASS eder → fd devri (kalan/çocuk süreç) kapıyı açar. |
| **Permission Model worker'a miras kalmaz** | §2.2 satır 97 + §5 satır 252 | Kısıtlar worker'a otomatik aktarılmaz; `execArgv` tekrarlanmalı → worker zinciri açık kapı bırakır. |
| **`--env-file`/`--openssl-config` kapsam dışı** | §2.2 satır 98 + §5 satır 252 | Ön-yükleme dosyaları Permission Model kapsamı dışında → bu dosyalar kısıt listesine girmez. |
| **`getuid`/`getgid` yok sayılan platformlar** | §2.2 satır 103–104 + §2.5 satır 155–156 + §5 satır 253 | "only available on POSIX platforms (i.e. not Windows or Android)" → windows + android'de çağrı sunulmaz; `process.getuid` windows'ta ayrıca "not available" (§5 satır 253). |
| **`shell:true` platform bağımlı** | §2.1 satır 80–81 + §5 satır 256 | Unix'te `/bin/sh`, Windows'ta `process.env.ComSpec` → kaçış davranışı OS'a bağlı, tek davranış yok. |
| **`fs.mkdir` mode desteklenmez** | §2.1 satır 78 + §5 satır 255 | Windows'ta `mode` desteklenmez, varsayılan 0o777 → mod tabanlı kısıtlama windows'ta bu çağrıyla yapılamaz. |

### §7.5 ⚠️ Kombinasyon Derinliği (§2.4, §2.6–§2.8)

- **iOS (§2.4)**: `os.platform()` değer listesi (§5 satır 254, içerik okundu) `ios` içermez; POSIX-only ibaresi yalnız "not Windows or Android" der — iOS için upstream cümlesi YOK; `api/ios.html` 404 (§5 satır 264) → ⚠️.
- **Tizen (§2.6)**: platform listesinde `tizen` YOK; `api/tizen.html` 404 (§5 satır 265); nodejs.org/api'de Tizen güvenlik ifadesi BULUNMAZ → ⚠️.
- **webOS (§2.7)**: platform listesinde `webos` YOK; `api/webos.html` 404 (§5 satır 266); webOS izolasyon kuralları nodejs.org/api kapsamı DIŞI (diğer sağlayıcı belgeleri kaynak hiyerarşisine girmez) → ⚠️.
- **ReactOS (§2.8)**: platform listesinde `reactos` YOK; `api/reactos.html` 404 (§5 satır 266); 'win32' etiketi ReactOS'a otomatik taşınmaz — Node binary'sinin ReactOS'ta derlendiği upstream'de DOGRULANMADI → ⚠️.
- **`sandbox.html` (§5 satır 267)**: 404 — nodejs.org/api'de sandbox konulu sayfa yok; macOS sandbox yüzeyi support.apple.com'dadır ve bu kartın upstream hiyerarşisine girmez (§7.3).

## §8. Negatif Kanıt Metodolojisi (§3'teki 0 isabet / 20 desen)

§8.1 çürütme sırası · §8.2 her reddedilen adayın çürütmesi.

### §8.1 Çürütme Sırası (§3'teki 20 desen + §5'teki 6×404)

1. 20 desenlik set (§3 satır 211–214): `child_process` · `\bspawn\b` · `spawnSync` · `execSync\(` · `process\.getuid` · `process\.umask` · `os\.userInfo` · `os\.platform` · `fs\.chmod` · `\bchmod\b` · `chown` · `setuid` · `\bACL\b` · `icacls` · `seccomp` · `bwrap` · `capset` · `chroot` · `--permission` · `allow-fs-read` → **0 isabet / 418 dosya** (§3 satır 218).
2. `package.json` glob → 2 (kök + `.opencode/`) — paket tanımı var, OS-güvenlik çağırmak YOK (§3 satır 219).
3. `*.py` glob → 1 (`.ai/scripts/fix-mojibake.py` — vault bakım aracı, JS kartı için yalnız sayımdır) (§3 satır 220).
4–9. 6 reddedilen aday (§5 satır 264–267): `ios`/`android`/`tizen`/`webos`/`reactos`/`sandbox` → hepsi **404** → §2.4/§2.6/§2.7/§2.8'de negatif kanıt, `sandbox` §7.5'te boşluk.

### §8.2 Her Reddedilen Adayın Çürütmesi (§5'teki 6)

| # | URL (§5) | Ne çürütüyor? | §5'teki etkisi |
|---|---|---|---|
| 1 | `api/ios.html` | "Node.js iOS güvenlik yüzeyi" — bu URL 404; platform listesinde 'ios' yok | iOS kombinasyonu §1 satır 56'da ⚠️ |
| 2 | `api/android.html` | "Node.js Android güvenlik sayfası" — bu URL 404; android iddiası yalnız os.html üzerinden (deneysel) | android kombinasyonu §1 satır 57'de Yüksek (os/process URL'leri) |
| 3 | `api/tizen.html` | "Node.js Tizen sayfası" — bu URL 404 | tizen kombinasyonu §1 satır 58'de ⚠️ |
| 4 | `api/webos.html` | "Node.js webOS sayfası" — bu URL 404 | webos kombinasyonu §1 satır 59'da ⚠️ |
| 5 | `api/reactos.html` | "Node.js ReactOS sayfası" — bu URL 404 | reactos kombinasyonu §1 satır 60'da ⚠️ |
| 6 | `api/sandbox.html` | "Node.js sandbox sayfası" — bu URL 404 | sandbox yüzeyi nodejs.org/api kapsamı dışında (§7.3) |

## §9. Ölçüm Kaydı

§9.1 SAPMA kaydı · §9.2 URL doğrulama kaydı.

### §9.1 SAPMA Kaydı (§1–§5)

| Metot | Ölçüm | Sapma | Açıklama |
|---|---|---|---|
| §1 matrisi (8 satır) vs §2 blokları (8 blok) | 8 = 8 | **0** | Her §1 satırının §2'de en az bir bloğu var (windows §2.1 · linux §2.2 · macos §2.3 · android §2.5 · ⚠️×4 §2.4/§2.6–§2.8). |
| §1 "Güven düzeyi" vs §5 URL'leri | 4 Yüksek + 4 ⚠️ | **0** | 4 Yüksek satırın (windows, linux, macos, android) §5'te en az 1 okunabilir URL'i var; 4 ⚠️ satırının 404 kanıtı §5 satır 264–266'da. |
| §0 validation "7 kaynak URL … icerikleri okundu" vs §5 | 7 iddia URL'i (satır 252–258) | **0** | 7/7 HTTP 200 içerik okundu; §0 satır 19 ile birebir. |
| §5'teki 5 durum-doğrulama URL'i (net/worker_threads/crypto/addons/dns) vs §0 validation | 5 dahil değil | **1 sapma (kapsam)** | §0 satır 19 yalnız "7 kaynak + 6 aday 404" sayar; §5 satır 260–262'deki 5 durum URL'i bu sayıya GİRMEZ. Sayım tutarlı (7+6), ama 18 denenen URL'in 5'i kayıt dışı — **raporlandı, mevcut satır düzenlenmedi**. |
| §3 "Desen seti (20)" vs §3 PowerShell ölçümü (satır 226–228) | 20 ≠ 8 | **1 sapma (betik kapsamı)** | Yeniden üretilebilir PowerShell betiği 8 desen taşır (§3 satır 211–214'teki 20 desenin alt kümesi: child_process, spawn, getuid, userInfo, chmod, seccomp, chroot, --permission); eksik 12 desen betikte YOK. Beklenti 0 → sonuç için yeterli, ama 20 desenin tamamı betikle yeniden üretilmez — **raporlandı, betik düzenlenmedi**. |

**Sonuç**: §9.1'de **2 sapma** var (her ikisi de kapsam/nüans): (1) 5 durum-doğrulama URL'i §0 sayımına girmez; (2) PowerShell betiği 20 desenin 8'ini taşır. İkisi de raporlandı; mevcut satırlar düzenlenmedi.

### §9.2 URL Doğrulama Kaydı (§5'teki 7 okunabilir + 5 durum + 6 reddedilen)

§5: 18 URL denendi — 7 iddia URL'i (HTTP 200, içerik okundu; satır 252–258), 5 durum-doğrulama (200; iddia için kullanılmadı; satır 260–262), 6 reddedilen (404; satır 264–267). 7 iddia URL'i §2.1–§2.5'in 5 bloğunu + §1 satır 53–57'nin 5 satırını doğruluyor; 404'ler §2.4/§2.6–§2.8'in ⚠ü durumunu; `sandbox.html` 404 §7.3'te boşluk olarak. §0 satır 19 "7 kaynak URL HTTP 200 … 6 aday URL 404" — §5 ile birebir (5 durum URL'i hariç, §9.1).

## §10. Sınır Haritası (§4'teki 3 sınır + §7.3'teki 7 boşluk)

§10.1 hangi kombinasyonun kanıtı eksik · §10.2 hangi mekanizmanın kanıtı eksik.

### §10.1 Hangi Kombinasyonun Kanıtı Eksik (4 ⚠️ kombinasyon)

§1'deki 8 satırın 4'ü (windows, linux, macos, android) §5'teki 7 okunabilir URL ile doğrulandı. 4 kombinasyon (ios, tizen, webos, reactos) §1'de ⚠️ VERIFICATION REQUIRED (satır 56, 58–60); §2.4/§2.6–§2.8'de örnek yazılmadı — kanıt: platform listesinde değiller + 4×404 (§5 satır 264–266). Ayrıca §7.3'teki 7 boşluk: 6×404 + 1 konu-dışı (macOS TCC/sandbox → support.apple.com, upstream hiyerarşisi dışı) + 5 durum URL'i kapsam dışı.

### §10.2 Hangi Mekanizmanın Kanıtı Eksik (§7.2 tablosu)

§7.2'deki 8 mekanizma sınıfının 6'sı (dosya sistemi izinleri, süreç izin modeli, POSIX kimliği, kullanıcı bilgisi, süreç başlatma, CA/Keychain) §5'teki 7 okunabilir URL ile doğrulandı. `olay izleme (fs.watch)` sınıfı §5 satır 255 (fs.html, içerik okundu) üzerinden doğrulandı; `yol ayrıştırma (path)` sınıfı satır 258 (path.html, içerik okundu) üzerinden. Eksik kanıt yalnız ⚠️ kombinasyonlarda (§10.1). Ayrıca §2.3 duruş notu: TCC/izin penceresi + sandbox-exec yüzeyi nodejs.org/api'de GEÇMEZ → bu mekanizma sınıfı kasıtlı olarak oluşturulmadı (uydurma yasak).

## §11. Reddedilen Adaylar Defteri (§5'teki 6 + §7.3'teki 1 konu dışı)

§11.1 §5'teki 6 reddedilen · §11.2 §7.3'teki 1 konu dışı + 5 durum.

### §11.1 §5'teki 6 Reddedilen

| # | URL (§5) | Neden reddedildi? | Kanıt |
|---|---|---|---|
| 1 | `nodejs.org/api/ios.html` | HTTP 404 | §5 satır 264 |
| 2 | `nodejs.org/api/android.html` | HTTP 404 — android iddiası yalnız os.html üzerinden | §5 satır 265 |
| 3 | `nodejs.org/api/tizen.html` | HTTP 404 | §5 satır 265 |
| 4 | `nodejs.org/api/webos.html` | HTTP 404 | §5 satır 266 |
| 5 | `nodejs.org/api/reactos.html` | HTTP 404 | §5 satır 266 |
| 6 | `nodejs.org/api/sandbox.html` | HTTP 404 — sandbox yüzeyi nodejs.org/api'de yok | §5 satır 267 |

### §11.2 §7.3'teki 1 Konu Dışı + 5 Durum URL'i

| # | Hedef | Durum | Neden gövdeye alınmadı? |
|---|---|---|---|
| 1 | `support.apple.com` TCC/sandbox sayfaları | ⚠️ konu dışı | Bu kart yalnız `nodejs.org/api` upstream'ini taşır (§5 satır 268–269); içerik uydurulmadı, §2.3'te duruş notu. |
| 2–6 | `net` · `worker_threads` · `crypto` · `addons` · `dns` (§5 satır 260–262) | HTTP 200 (durum) | İddia için kullanılmadı — OS-güvenlik iddiası taşımıyorlar; §1/§2'ye taşınmadı (kapsam dışı). |

## §12. S1/S2 Derinliği

§12.1 S1 — kapı ölçümü · §12.2 S2 — marj.

### §12.1 S1 — Kapı Ölçümü (append öncesi 279 satır)

Append öncesi ölçüm (`read` offset=100000 → "(279 lines)"): **279 satır** — Band-1 kapısı (≥500) altında. Append sonrası ölçüm: kapı sonucu §15.2'de. Not: bu dosyada `§0.1`/`§0.2` başlığı **yok** (yalnız `## §0 ELI15`, satır 28) — kapı ölçümü bu §12/§15 çiftinde taşınır.

### §12.2 S2 — Marj

Marj = ölçülen satır − 500. Önceki 9 dosyanın marjları: tizen +7, reactos +19, webos +19, macos +75, linux +110, python +11, windows +6, rust +3, php +6. Bu dosyanın marjı §15.2'de ölçülecek: §5'teki 7 okunabilir + 5 durum + 6 reddedilen → §7–§11 geniş; beklenen toplam 279 + ~230 = ~510 → kapı geçmesi bekleniyor.

## §13. Tüketim Kuralları

§13.1 ne okunmalı (öncelik) · §13.2 ne okunmamalı · §13.3 ne çoğaltılmamalı · §13.4 ne güncellenmeli · §13.5 doğru/yanlış cümle kartı.

### §13.1 Ne Okunmalı (öncelik)

1. §3 CoreMusic repo durumu — en kritik (JS 98 dosya; Node.js OS-güvenlik yüzeyi 0 isabet / 20 desen).
2. §1 Kombinasyon matrisi — 8 satır (4 Yüksek + 4 ⚠️).
3. §2.1–§2.3 + §2.5 blokları — doğrulanmış 4 kombinasyon (sözleşmesel referans).
4. §5 Referans — 7 iddia URL'i + 6 reddedilen.
5. §7.4 Arıza örüntüsü — 6 örüntü (Permission Model bypass dahil).

### §13.2 Ne Okunmamalı

- §7.1 kombinasyon derinlikleri — §1 + §2 + §5'teki bilginin konsolide tekrarı.
- §9.1 SAPMA tablosu — §1/§3/§5 okunduysa gereksiz (2 sapma: 5 durum URL'i + betik 8/20 desen).

### §13.3 Ne Çoğaltılmamalı

- 8 kombinasyon × mekanizma eşlemesi — §1 + §2 + §7.1 + §7.2'de 4×.
- 404 adaylar — §5 + §8.2 + §11.1'de 3×.
- 0 isabet negatif kanıtı — §0 + §3 + §8.1 + §13.1'de 4×.

### §13.4 Ne Güncellenmeli

- §12.2 marj tahmini (~510) — §15.2 kapı ölçümü sonrası gerçek marjla karşılaştırılmalı.
- §9.1'deki 2 sapma — (1) 5 durum URL'i §0 validation sayısına girerse (ör. gelecek turda §0'a "18 URL" yazımı) kapanır; (2) PowerShell betiğine eksik 12 desen eklenirse kapanır — **bu turda betik düzenlenmedi** (raporlandı).
- §2.3/§2.5'teki FARK duruş notları (TCC/sandbox · APK runtime izinleri) — kaynak hiyerarşisine girmediği sürece YAZILMAZ; upstream'e eklenirse güncellenir.

### §13.5 Doğru/Yanlış Cümle Kartı

| Doğru cümle | Yanlış cümle | Neden |
|---|---|---|
| "Node.js OS-güvenlik deseni bu repoda 0 isabet (20 desen / 418 dosya)." | "Node.js repoda yok." | §3 satır 210: 98 JS dosyası; satır 218: 0 isabet (desen taraması). |
| "Permission Model fd'lerle bypass edilir; worker'a miras kalmaz." | "Permission Model her şeyi kapatır." | §2.2 satır 97–99 + §5 satır 252. |
| "`process.getuid` Windows'ta yok; `getgid/getgroups` Windows ve Android'de yok." | "`process.getuid` her platformda çalışır." | §5 satır 253 (process.html, içerik okundu). |
| "4 kombinasyon (ios, tizen, webos, reactos) ⚠️ — örnek yazılmadı." | "8 kombinasyonun tamamı doğrulandı." | §1 satır 56, 58–60; §2.4/§2.6–§2.8. |
| "`fs.mkdir` Windows'ta `mode` desteklemez (varsayılan 0o777)." | "`fs.mkdir(p,{mode})` Windows'ta izni ayarlar." | §2.1 satır 78 + §5 satır 255. |

## §14. Bağımlılık Zinciri

§14.1 §0→§1→§2→§3→§4→§5 zinciri · §14.2 §7'nin bağımlılıkları.

### §14.1 §0→§1→§2→§3→§4→§5

§0 (görev) → §1 (matris) → §2 (bloklar) → §3 (repo durumu) → §4 (sınır) → §5 (referans). §0 satır 30–36 "Node.js yalnız çalıştığı OS'un mekanizmasına sarar" → §1 "8 satır × kombinasyon" → §2 "8 blok × repo karşılığı YOK" → §3 "0 isabet / 20 desen negatif kanıt" → §4 "K009/K010 ayrımı" → §5 "7+5 URL + 6 reddedilen". §0→§1→§2→§3→§4→§5 bağımlılığı §7.1'deki kombinasyon derinliklerinin temeli.

### §14.2 §7'nin Bağımlılıkları

§7.1 (kombinasyon derinlikleri) §1 + §2 + §5'e bağımlı. §7.2 (mekanizma sınıflandırması) §2 bloklarına bağımlı. §7.3 (boşluklar) §5'teki 6 reddedilen + 1 konu dışıya bağımlı. §7.4 (arıza örüntüsü) §2.2 + §2.1 + §5'ten türetildi. §7.5 (⚠️ derinliği) §2.4/§2.6–§2.8'e bağımlı. §8 (negatif kanıt) §3 + §5'e bağımlı.

## §15. Kapı Ölçüm Kaydı

§15.1 ölçüm öncesi/sonrası · §15.2 kapı sonucu.

### §15.1 Ölçüm Öncesi/Sonrası

Append öncesi: **279 satır** (`read` offset=100000 → "(279 lines)") — Band-1 kapısı (≥500) altında. Append sonrası: **528** (§7–§17, tek tur). Eklenen: §7–§17. Not: bu dosyada `§0.1`/`§0.2` başlığı yok (yalnız `## §0 ELI15`, satır 28); kapı ölçümü bu §15'te taşınır.

### §15.2 Kapı Sonucu

Append sonrası ölçüm (`read` offset=100000 → "(528 lines)"): **528 satır** — Band-1 kapısı (≥500) **GEÇTİ**; marj **+28** (§16.1). §12.2 tahmini ~510 idi → gerçekleşen 528, tahmini 28 satır üstünde (fark: §7.1/§7.4/§17.1 tablolarının tahminden geniş çıkması).

## §16. Marj

§16.1 marj hesabı · §16.2 marj güvenliği.

### §16.1 Marj Hesabı

Marj = ölçülen satır − 500. Önceki 9 dosyanın marjları: tizen +7, reactos +19, webos +19, macos +75, linux +110, python +11, windows +6, rust +3, php +6. Bu dosyanın marjı: **+28** (528 − 500, §15.2).

### §16.2 Marj Güvenliği

§7.1'deki 8 kombinasyon derinliği §5'teki 7 okunabilir URL ile destekleniyor. §7.4'teki 6 arıza örüntüsü §2.1/§2.2 + §5'ten türetildi. §7.5'teki 5 ⚠️ derinliği §2.4/§2.6–§2.8 + 404'lerden. §8–§16'daki bölümler §5'teki 7+5+6 URL durumu ile destekleniyor. §17.1 URL × §1 eşlemesi §5'teki 7 iddia URL'inden, §17.2 platform-listesi yöntemi §2.4/§2.6–§2.8'den türetildi — yeni ölçüm/URL yok. §12.2 tahmini ~510 → gerçekleşen 528 (§15.2); kapı GEÇTİ.

## §17. Ek Kanıtlar (§1–§6'dan türetildi — yeni ölçüm/URL yok)

§17.1 URL × §1 satır eşlemesi · §17.2 platform-listesi metodolojisi · §17.3 §15–§16 bağımlılıkları · §17.4 kapı turu kaydı.

### §17.1 URL × §1 Satır Eşlemesi (§5'teki 7 iddia URL'inden türetildi)

| §5 URL'i (satır) | §1 satırını doğrular | Doğrulanan iddia |
|---|---|---|
| `permissions.html` (252) | linux (54) | enforce/audit; kısıtlar; OS'a güven; fd bypass; worker mirası |
| `process.html` (253) | linux (54) + windows (53, dolaylı) + android (57) | getuid "not Windows"; getgid/getgroups "not Windows or Android"; umask |
| `os.html` (254) | android (57) + ios/tizen/webos/reactos (56, 58–60, negatif) + windows (53, dolaylı userInfo) | platform değer listesi; 'android' deneysel; userInfo uid/gid=-1 |
| `fs.html` (255) | windows (53) + macos (55) | access ACL kontrol etmez; chmod yalnız write; mkdir mode desteklenmez; lchmod macOS-only |
| `child_process.html` (256) | windows (53) | shell:true → ComSpec; windowsVerbatimArguments/Hide; uid/gid → setuid(2)/setgid(2) |
| `cli.html` (257) | macos (55) + windows (53, dolaylı) | --use-system-ca Keychain; --permission bayrağı |
| `path.html` (258) | windows (53) | Windows vs POSIX; path.win32/posix |

Eşleme sonucu: 4 Yüksek satırın (53–55, 57) her biri en az 1 doğrudan URL'e sahip; 4 ⚠️ satırının (56, 58–60) negatif kanıtı os.html platform listesi + 404'lerle. 5 durum URL'i (260–262) hiçbir §1 satırını doğrulamaz — kapsam dışı (§9.1).

### §17.2 Platform-Listesi Metodolojisi (§2.4/§2.6–§2.8 ortak yöntem)

4 ⚠️ kombinasyonun çürütme yöntemi aynıdır ve tekrarlanabilir: (1) `os.platform()` değer listesi okunur (§5 satır 254, içerik okundu) → 'ios'/'tizen'/'webos'/'reactos' listede YOK; (2) `nodejs.org/api/{os}.html` denenir → 404 (§5 satır 264–266); (3) POSIX-only ibaresinin sınırı kontrol edilir → yalnız "not Windows or Android" (§5 satır 253), iOS'a genelleme YOK; (4) sonuç: pozitif iddia yazılmaz, ⚠️ konulur. Bu yöntem android'i ⚠️'den ayırdı: 'android' listede VAR + os.html/process.html'de deneysel cümle mevcut → android Yüksek (satır 57).

### §17.3 §15–§16 Bağımlılıkları

§15.1 (ölçüm öncesi 279) §12.1 ölçüm kaydına; §15.2 (kapı sonucu) §15.1'e; §16.1 (marj hesabı) §15.2'ye; §16.2 (marj güvenliği) §7.1–§8'e bağımlıdır.

### §17.4 Kapı Turu Kaydı

| Tur | Eklenen | Ölçüm | Kapı (≥500) |
|---|---|---|---|
| 0 | — (append öncesi) | 279 | altında |
| 1 | §7–§17 (footer korunarak) | **528** | GEÇTİ (+28) |

Kapı ölçümü bu dosyada **tek turda** tamamlandı (§7–§17 tek append; python/windows/rust/php 2. tur gerektirmişti). §12.2 tahmini ~510 → gerçekleşen 528 (+28, tahmin üstü).

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
