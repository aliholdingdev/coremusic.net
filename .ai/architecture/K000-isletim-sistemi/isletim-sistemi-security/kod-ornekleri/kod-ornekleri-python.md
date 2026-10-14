---
node_id: K000-isletim-sistemi/isletim-sistemi-security/kod-ornekleri/kod-ornekleri-python
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-security/kod-ornekleri
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-anayasa
  - K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-macos
evidence:
  - https://docs.python.org/3/library/os.html
  - https://docs.python.org/3/library/sys.html
  - https://docs.python.org/3/library/subprocess.html
  - https://docs.python.org/3/library/tempfile.html
  - https://docs.python.org/3/library/platform.html
  - https://docs.python.org/3/using/android.html
  - https://docs.python.org/3/using/ios.html
  - https://docs.python.org/3/faq/windows.html
validation:
  - "2026-10-14: 8 kaynak URL HTTP 200 ile dogrulandi ve icerikleri okundu; 5 aday URL 404, 1 aday 200-konu-disi olarak red; repo olcumu 418 PHP+JS + 1 py / 0 anlamli isabet."
open_questions:
  - "S1: OS guvenlik katmani hedef mimaride devreye alinacak mi? [UNKNOWN]"
  - "S2: Uygulama <-> OS guvenlik sorumluluk siniri? [UNKNOWN]"
group: kod
---

# Kod Örnekleri — Python (Dil × 8 OS)

## §0 ELI15

Bu kart, **tek bir dilin** (Python) **sekiz işletim sistemiyle** kesişen
güvenlik yüzeyini taşır: windows · linux · macos · ios · android · tizen ·
webos · reactos. Python'un kuralı nettir: OS'e özgü her iş `os`/`subprocess`
üstünde **`Availability:`** satırıyla işaretlenir — `os.getuid` "Unix",
`os.setuid` "Unix, not WASI, not Android", `subprocess` ise *"not supported on
mobile platforms"* (Android/iOS). Mobilde Python bir **sistem kaynağı değil,
uygulama içine gömülü yorumlayıcıdır** (embedded mode).

> **Öncelikli gerçek:** Bu repoda Python yüzeyi neredeyse yoktur: `*.py` =
> **1 dosya** (`.ai/scripts/fix-mojibake.py` — vault bakım aracı) ve o dosyadaki
> tek OS-güvenlik deseni `sys.platform == 'win32'` satırıdır; **418 PHP+JS**
> ağacında Python OS-güvenlik deseni **anlamda 0 isabet** (yalnız bir test
> metodu adındaki `TempFile` alt-dizesi yanıltıcı eşleşmedir, §3). Bu kart
> **hedef mimari + negatif kanıt** kartıdır; §2'deki 8 bloğun tamamı
> `kaynak: upstream docs` imzalı **sözleşmesel** referans kalıplarıdır
> (çalıştırılabilir kod **değildir**), repoda karşılığı yoktur.

`group: kod` · S1 ve S2 açık sorudur, bu kartta kapatılmaz (§6). "Repo'da
uygulanıyor" iddiası bu kartta **yasaktır**; §3 yalnız ölçülmüş durumu taşır.

## §1 Python × OS güvenlik yüzeyi matrisi

| OS | Bu OS'ta Python güvenlik yüzeyi (upstream kanıt) | Güven düzeyi |
|---|---|---|
| windows | `sys.platform='win32'` · `shell=True` → `%COMSPEC%` (3.12: `%PATH%`+cwd yerine) · `.bat/.cmd` OS tarafından system shell'de açılabilir (shell injection notu) · `close_fds` Windows handle davranışı | Yüksek (subprocess/sys/faq okundu) |
| linux | `os.getuid` **Availability: Unix** · `os.setuid` "Unix, not WASI, not Android" · `tempfile.TemporaryFile` → `os.O_TMPFILE` (*Linux-specific, kernel 3.11+*) | Yüksek (os/tempfile okundu) |
| macos | `os.getresuid/getresgid` **"not macOS"** · `os.uname()` → `'Darwin'` · `platform.platform()` 3.8+ macOS sürümünü `mac_ver()` ile alır | Yüksek (os/platform okundu) |
| ios | `using/ios.html`: dağıtım birimi = **app**, konsol yok, **embedded mode** · `subprocess` **desteklenmiyor** · `getuid()` emülasyon/stub | Yüksek (os/subprocess/ios okundu) |
| android | `using/android.html`: yalnız **embedded mode** (libpython) · `os.setuid` **"not Android"** · `sys.platform='android'` · `os.uname()` → `'Linux'` | Yüksek (os/sys/android okundu) |
| tizen | `sys.platform` değer listesinde **'tizen' YOK**; `docs.python.org/3/using/tizen.html` **404** | ⚠️ VERIFICATION REQUIRED |
| webos | `sys.platform` değer listesinde **'webos' YOK**; `docs.python.org/3/using/webos.html` **404** | ⚠️ VERIFICATION REQUIRED |
| reactos | `sys.platform` değer listesinde **'reactos' YOK**; `win32` iddiası ReactOS'a **taşıtılmadı** | ⚠️ VERIFICATION REQUIRED |

## §2 Kod örnekleri

> **UYARI (zorunlu):** Aşağıdaki 8 bloğun **hiçbiri bu repodan alınmamıştır.**
> Hepsi `kaynak: upstream docs` ile imzalı **sözleşmesel** referans kalıplarıdır
> (çalıştırılabilir kod **değildir**); repo'da karşılığı §3'te negatif kanıt
> olarak ölçülür. Kopyalamadan önce S1 kararı gerekir.

### §2.1 — windows

```sh
# kaynak: upstream docs - docs.python.org/3/library/subprocess.html + sys.html + faq/windows.html
#   subprocess.run([a,b], shell=False) -> "this library will not implicitly
#     choose to call a system shell"; shell=True ILE metacarakterlerin
#     kacirilmasi "application's responsibility" (shell injection riski)
#   Windows uyari (Security Considerations): "*.bat or *.cmd may be launched
#     by the operating system in a system shell regardless of the arguments
#     passed to this library" -> guvensiz kaynaktan arguman icin oneri:
#     shell=True ile Python'a kacis biraktir (gh-114539)
#   3.12 degisikligi: Windows shell arama sirasi -> cwd + %PATH% yerine
#     %COMSPEC% ve %SystemRoot%\System32\cmd.exe ("cmd.exe" dusurme isine yarar)
#   close_fds=True -> "On Windows ... no handles will be inherited"
#     (handle_list / std handle yonlendirme disinda)
#   sys.platform == 'win32' | faq/windows: .py -> Python.File dosya tipi +
#     PATHEXT ile calistirilabilir yapma (kurulum, kod degil)
# sinir: ACL/privilege yazmak Python'da degil, OS katmanindadir
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel — çalıştırılabilir değil) | YOK — 418 PHP+JS'te `shell=True|close_fds|sys.platform` 0 anlamda isabet (2026-10-14) |

### §2.2 — linux

```sh
# kaynak: upstream docs - docs.python.org/3/library/os.html + tempfile.html
#   os.getuid() -> "Availability: Unix." (WASI'de stub)
#   os.setuid(uid) -> "Availability: Unix, not WASI, not Android."
#   os.setresuid/setresgid -> "Unix, not WASI, not Android, not macOS, not iOS"
#   os.getresgid() -> "Unix, not WASI, not macOS, not iOS"
#   os.setsid()/os.umask -> "Availability: Unix, not WASI" (surec/izin maskesi)
#   tempfile.TemporaryFile -> "The os.O_TMPFILE flag is used if it is
#     available and works (Linux-specific, requires Linux kernel 3.11 or later)"
#   os modulu genel notu: fork()/execve() gibi surec API'leri mobil ve
#     WebAssembly'de yok -> Linux tarafinda tam calisir
# sinir: capability / cgroup / seccomp politikasi bu dilin API'sinde YOKTUR
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel) | YOK — `os.setuid|O_TMPFILE|seccomp` grep'i 0 isabet (2026-10-14) |

### §2.3 — macos

```sh
# kaynak: upstream docs - docs.python.org/3/library/os.html + platform.html
#   os.getresuid() / os.getresgid() -> "Availability: Unix, not WASI, not
#     macOS, not iOS." (macOS'e OZGUR negatif kanit: bu API'ler sunulmaz)
#   os.uname() -> "On macOS, iOS and Android, this returns the kernel name and
#     release (i.e., 'Darwin' on macOS and iOS; 'Linux' on Android)"
#   os.setresuid/setresgid -> "Unix, not WASI, not Android, not macOS, not iOS"
#   platform.platform() -> 3.8+: "On macOS, the function now uses mac_ver(),
#     if it returns a non-empty release string, to get the macOS version
#     rather than the darwin version"
# FARK (durus not): TCC/izin penceresi, sandbox-exec ve kod imzalama
#   python.org dokumantasyonunda anlatilmaz -> o yuzey bu kartta YAZILMAZ.
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel) | YOK — `mac_ver|getresuid|uname` grep'i 0 isabet (2026-10-14) |

### §2.4 — ios

```sh
# kaynak: upstream docs - docs.python.org/3/using/ios.html + library/os.html + subprocess.html
#   using/ios.html: "Python on iOS is unlike Python on desktop platforms";
#     "The only unit of software distribution is an 'app'"; "there is no
#     console"; kullanim yalniz embedded mode (yorumlayici app icine gomulur)
#   os modulu: "On WebAssembly platforms, Android and iOS, large parts of the
#     os module are not available or behave differently ... Others like
#     getuid() and getpid() are emulated or stubs."
#   subprocess: "Availability: not Android, not iOS, not WASI. This module is
#     not supported on mobile platforms or WebAssembly platforms."
#   os.uname() -> iOS'ta 'Darwin' dondurur (kirim adi, kullanici surfu degil)
# sinir: iOS'ta surec baslatma / parolali dosya izni yuzeyi Python'da YOK
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel) | YOK — repoda iOS/Python yok (0 isabet) |

### §2.5 — android

```sh
# kaynak: upstream docs - docs.python.org/3/using/android.html + library/os.html + sys.html
#   using/android.html: "there is no concept of installing as a system
#     resource. The only unit of software distribution is an 'app'." -> tek
#     kullanim embedded mode (libpython + Python embedding API);
#     "The Python standard library has some notable omissions and restrictions"
#   os.setuid -> "Availability: Unix, not WASI, not Android." (Android'de YOK)
#   os modulu: Android'de fork()/execve()/nice() YOK; getuid() emulasyon/stub
#   subprocess -> "not supported on mobile platforms"
#   sys.platform == 'android' | sys.getandroidapilevel() -> "Availability:
#     Android" | platform.android_ver() | os.uname() -> 'Linux' (kirlog adi)
# sinir: APK runtime izin modeli Python dokumantasyonunda anlatilmaz -> YAZILMAZ
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel) | YOK — repoda Android/Python yok (0 isabet) |

### §2.6 — tizen — ⚠️ VERIFICATION REQUIRED

```sh
# kaynak: upstream docs - docs.python.org/3/library/sys.html (negatif kanit)
# sys.platform bilinen degerleri: 'aix'|'android'|'emscripten'|'freebsd'|'ios'|
#   'linux'|'darwin'|'win32'|'cygwin'|'wasi' -> 'tizen' LISTEDE YOK
# docs.python.org/3/using/tizen.html -> HTTP 404 (2026-10-14).
# SONUC: Tizen x Python guvenlik yuzeyi dogrulanamadi -> ornek YAZILMAZ.
```

| Sınıf | Repo karşılığı |
|---|---|
| ⚠️ (doğrulanamadı — uydurulmadı) | YOK — 0 isabet |

### §2.7 — webos — ⚠️ VERIFICATION REQUIRED

```sh
# kaynak: upstream docs - docs.python.org/3/library/sys.html (negatif kanit)
# sys.platform deger listesinde 'webos' YOK (yukaridaki kume ile ayni olcum).
# docs.python.org/3/using/webos.html -> HTTP 404 (2026-10-14).
# SONUC: webOS x Python guvenlik yuzeyi dogrulanamadi -> ornek YAZILMAZ.
```

| Sınıf | Repo karşılığı |
|---|---|
| ⚠️ (doğrulanamadı — uydurulmadı) | YOK — 0 isabet |

### §2.8 — reactos — ⚠️ VERIFICATION REQUIRED

```sh
# kaynak: upstream docs - docs.python.org/3/library/sys.html (negatif kanit)
# sys.platform deger listesinde 'reactos' YOK; docs.python.org'da ReactOS
#   belgesi YOKTUR (kullanim bolumunde boyle bir sayfa 404).
# 'win32' etiketi ReactOS'a OTOMATIK tasinmaz: CPython'un ReactOS'ta
#   desteklendigi upstream'de DOGRULANMADI -> iddia YOK.
# SONUC: ReactOS x Python guvenlik yuzeyi dogrulanamadi -> ornek YAZILMAZ.
```

| Sınıf | Repo karşılığı |
|---|---|
| ⚠️ (doğrulanamadı — uydurulmadı) | YOK — 0 isabet |

## §3 CoreMusic repo durumu

Tarama A: repo kökü, `*.php,*.js` — `vendor`/`node_modules`/`.git` hariç —
**418 dosya** (320 PHP + 98 JS; kontrol `function` → 2.431). Tarama B: `*.py`
→ **1 dosya**. Desen seti (12): `os\.setuid|os\.getuid|os\.getresuid|
subprocess|shell=True|tempfile|sys\.platform|0o600|os\.uname|os\.chmod|
close_fds|shlex` (2026-10-14).

| Yüzey | Kanıt yöntemi | Sonuç (2026-10-14) |
|---|---|---|
| Python OS-güvenlik deseni / 418 PHP+JS | grep 12 desen | **1 eşleşme = yanıltıcı** — `shared/tests/Component/TemplateEngineTest.php:55` içinde `TempFile` (PHP test metodu; Python **yok**) |
| Python dosyası envanteri | glob `*.py` | **1** — `.ai/scripts/fix-mojibake.py` (vault bakım aracı, OS güvenliği **değil**) |
| O dosyadaki tek OS deseni | idx 24 birebir | `if sys.platform == 'win32':` (`fix-mojibake.py:25`) — **dosya ayıracı**, izin/erişim kararı **değildir** |
| "repo'da uygulanıyor" iddiası | — | **YASAK** — bu kart yalnız ölçülmüş durumu / yalanlamayı taşır |

**Ölçüm (yeniden üretilebilir):**

```powershell
Get-ChildItem 'C:\www\coremusic.net' -Recurse -File -Include *.py |
  Where-Object { $_.FullName -notmatch '\\vendor\\|\\node_modules\\|\\\.git\\' }   # beklenti: 1
(Get-ChildItem 'C:\www\coremusic.net' -Recurse -File -Include *.php,*.js |
  Where-Object { $_.FullName -notmatch '\\vendor\\|\\node_modules\\|\\\.git\\' } |
  Select-String -Pattern 'os\.setuid|subprocess|shell=True|sys\.platform').Count  # beklenti: 1 (TempFile)
```

**Okuma:** Python'un OS güvenlik yüzeyi bu repoda **hiç başlamamıştır** — bu,
bu kartın karakteridir.

## §4 Sınır cümleleri

> **K010 ayrımı (bağlayıcı):** Kimlik doğrulama, oturum, parola ve MFA →
> `K010-identitiy`; burada yalnız Python'un OS'e açılan süreç/izin yüzeyi taşınır.
> **K009 ayrımı (bağlayıcı):** OWASP denetimi, auth/CSRF/CSP tasarımı, kripto
> seçimi (ADR-022) → `K009-security`; `secrets`/`hashlib` token-üretimi bu kartın
> **konusu değildir**, uygulama katmanıdır.
> **Komşu alt-dal:** OS geneli anlatımı `isletim-sistemi-turu/*`, sistem
> çağrıları `isletim-sistemi-api/*`, çekirdek içi mekanizma `isletim-sistemi-kernel`.

Ek sınır: `subprocess` ile süreç başlatmak **bir uygulama çağrısıdır**; batch
çalıştırma, handle devri ve `Availability:` sınırları burada **anlatılır,
çalıştırılmaz**. İzin *tasarımı* S1'e bağlıdır ve burada kapatılmaz (§6).

## §5 Referans

| URL | İddia | 2026-10-14 durum |
|---|---|---|
| https://docs.python.org/3/library/os.html | `os.getuid` "Availability: Unix" · `os.setuid` "not Android" · `getresuid/getresgid` "not macOS, not iOS" · mobil/Wasm'de `os` yarı-stub · `os.uname()` Darwin/Linux adları | HTTP 200 (GET) · içerik okundu |
| https://docs.python.org/3/library/sys.html | `sys.platform` değer tablosu ('linux','darwin','win32','android','ios', …) · `sys.getandroidapilevel()` "Availability: Android" | HTTP 200 · içerik okundu |
| https://docs.python.org/3/library/subprocess.html | Security Considerations (shell metacarakterleri) · Windows `.bat/.cmd` system shell uyarısı (gh-114539) · 3.12 shell arama sırası · `close_fds` Windows davranışı · "not supported on mobile platforms" | HTTP 200 · içerik okundu |
| https://docs.python.org/3/library/tempfile.html | `TemporaryFile` POSIX'te isim girmez · `os.O_TMPFILE` *Linux-specific, kernel 3.11+* | HTTP 200 · içerik okundu |
| https://docs.python.org/3/library/platform.html | `platform.platform()` 3.8+: macOS'ta `mac_ver()` · `win32_ver()` · `android_ver()` | HTTP 200 · içerik okundu |
| https://docs.python.org/3/using/android.html | Android'de dağıtım birimi = app; yalnız embedded mode (libpython); stdlib'de kısıtlar | HTTP 200 · içerik okundu |
| https://docs.python.org/3/using/ios.html | iOS'ta dağıtım birimi = app; konsol yok; embedded mode (Russell Keith-Magee, 2024-03) | HTTP 200 · içerik okundu |
| https://docs.python.org/3/faq/windows.html | Windows'ta `.py` → `Python.File` dosya tipi eşleşmesi; `PATHEXT` ile uzantısız çalıştırma | HTTP 200 · içerik okundu |

> **Yalnız durum doğrulaması (200 — iddia için kullanılmadı):**
> `docs.python.org/3/library/secrets.html` · `hashlib.html` · `ctypes.html` ·
> `pathlib.html` · `socket.html` (2026-10-14 GET 200; hepsi bu kartın
> kapsamı dışında — K009).

> **Reddedilen adaylar (yazılmadı):** `docs.python.org/3/using/tizen.html` →
> **404** · `docs.python.org/3/using/webos.html` → **404** ·
> `docs.python.org/3/using/reactos.html` → **404** ·
> `docs.python.org/3/library/ios.html` → **404** (iOS doğru adres
> `using/ios.html`) · `docs.python.org/3/using/macos.html` → **404** (macOS
> doğru adres `library/*` + `platform`) · hepsi 2026-10-14. Ayrıca
> `docs.python.org/3/library/venv.html` → HTTP 200 ama **konu dışı** (OS
> güvenlik yüzeyi değil) → gövdeye alınmadı. Dört 404, §2.6/§2.7/§2.8'de
> **negatif kanıt** olarak kullanıldı; içerik uydurulmadı.

## §6 Açık sorular

| # | Soru | Durum |
|---|---|---|
| S1 | Python süreç/izin yüzeyi (`subprocess`, `os.setuid`, umask, geçici dosya izinleri) hedef mimaride devreye alınıp alınmayacağı; mobil (iOS/Android) tarafın kapsamı | **[UNKNOWN]** — bu kartta KAPATMA YOK; mimari sahibi + yeni ADR (`>=088`) gerektirir |
| S2 | Uygulama ↔ OS güvenlik sorumluluk sınırı (batch çalıştırma / handle devri / izin maskesi kimin işi) | **[UNKNOWN]** — bu kartta KAPATMA YOK; K009/K010 ayrım çizgisiyle birlikte değerlendirilir |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
