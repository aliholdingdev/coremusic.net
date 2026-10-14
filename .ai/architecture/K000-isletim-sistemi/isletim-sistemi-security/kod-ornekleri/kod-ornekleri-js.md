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

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
