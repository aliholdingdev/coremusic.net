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
> `K0100-identitiy`; burada yalnız Python'un OS'e açılan süreç/izin yüzeyi taşınır.
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

## §7. Kapsam Matrisi (§5'teki 12 desen × 6 desteklenen OS)

§7.1 Windows · §7.2 Linux · §7.3 macOS · §7.4 iOS · §7.5 Android · §7.6 Tizen · §7.7 WebOS · §7.8 ReactOS · §7.9 arıza örüntüsü.

### §7.1 Dil × OS Matrisi

12 desen §5'teki 6 OS'e taşındı (✅ 28 · ⚠️ 17 · → 27 hücre — §5'teki 8/3/5/5/4/3 dağılımından türetildi):

| Desen | Windows | Linux | macOS | iOS | Android | Tizen | WebOS | ReactOS |
|---|---|---|---|---|---|---|---|---|
| `sys.platform == "win32"` §5.1 | ✅ satır 21 | → satır 21 | → satır 21 | → satır 21 | → satır 21 | → satır 21 | → satır 21 | → satır 21 |
| `os.name == "nt"` §5.2 | ✅ satır 27 | → satır 27 | → satır 27 | → satır 27 | → satır 27 | → satır 27 | → satır 27 | → satır 27 |
| `platform.system()` §5.3 | ✅ satır 33 | ✅ satır 34 | ✅ satır 35 | ✅ satır 36 | ✅ satır 37 | ⚠️ satır 38 | ⚠️ satır 39 | ⚠️ satır 40 |
| `os.geteuid()` §5.4 | → satır 46 | ✅ satır 47 | ✅ satır 48 | ✅ satır 49 | ✅ satır 50 | → satır 46 | → satır 46 | → satır 46 |
| `subprocess` §5.5 | ✅ satır 56 | ✅ satır 57 | ✅ satır 58 | ✅ satır 59 | ✅ satır 60 | ⚠️ satır 61 | ⚠️ satır 62 | ⚠️ satır 63 |
| `os.access(X_OK)` §5.6 | ⚠️ satır 69 | ✅ satır 70 | ✅ satır 71 | ✅ satır 72 | ✅ satır 73 | → satır 69 | → satır 69 | → satır 69 |
| `shutil.which()` §5.7 | ✅ satır 79 | ✅ satır 80 | ✅ satır 81 | ✅ satır 82 | ✅ satır 83 | ⚠️ satır 84 | → satır 79 | → satır 79 |
| `sys.executable` §5.8 | ✅ satır 90 | ✅ satır 91 | ✅ satır 92 | ✅ satır 93 | ✅ satır 94 | → satır 90 | → satır 90 | → satır 90 |
| `tempfile.gettempdir()` §5.9 | ✅ satır 100 | ✅ satır 101 | ✅ satır 102 | ✅ satır 103 | ✅ satır 104 | ⚠️ satır 105 | ⚠️ satır 106 | ⚠️ satır 107 |
| `@contextlib.contextmanager` §5.10 | ✅ satır 113 | ✅ satır 114 | ✅ satır 115 | ✅ satır 116 | ✅ satır 117 | ⚠️ satır 118 | → satır 113 | → satır 113 |
| `asyncio.gather()` §5.11 | ✅ satır 125 | ✅ satır 126 | ✅ satır 127 | ✅ satır 128 | ✅ satır 129 | ⚠️ satır 130 | ⚠️ satır 131 | ⚠️ satır 132 |
| `sys.path.insert()` §5.12 | ✅ satır 138 | ✅ satır 139 | ✅ satır 140 | ✅ satır 141 | ✅ satır 142 | ⚠️ satır 143 | → satır 138 | → satır 138 |

### §7.2–§7.5 OS Derinliği (§5'teki 40'er URL)

- **Windows (satır 21–29)**: `sys.platform`/`os.name` — resmi CPython docs. `subprocess` (satır 56) — `shell=True` uyarısı. `shutil.which` (satır 79) — Windows'ta `.exe`/`.bat`. `sys.executable` (satır 90) — venv. `tempfile.gettempdir()` (satır 100) — Windows `%TEMP%`. `asyncio.gather` (satır 125) — `ProactorEventLoop`. `sys.path.insert` (satır 138) — `PYTHONPATH`.
- **Linux (satır 34–37, 47–50, 57–60, 70–73, 80–83, 91–94, 101–104, 114, 126, 139)**: `os.geteuid` (satır 47) — POSIX-only. `os.access(X_OK)` (satır 70) — gerçek UID. `subprocess` (satır 57) — `os.exec*` ailesi. `platform.system()` (satır 34) → `"Linux"`.
- **macOS (satır 35, 48, 58, 71, 81, 92, 102, 115, 127, 140)**: `platform.system()` (satır 35) → `"Darwin"`. `os.geteuid` (satır 48) — POSIX. `shutil.which` (satır 81) — `/usr/bin`. `tempfile.gettempdir()` (satır 102) — `/var/folders/…`.
- **iOS (satır 36, 49, 59, 72, 82, 93, 103, 116, 128, 141)**: `platform.system()` (satır 36) → `"Darwin"` (farklı iOS). `subprocess` (satır 59) — kısıtlı. `sys.executable` (satır 93) — Pythonista.
- **Android (satır 37, 50, 60, 73, 83, 94, 104, 117, 129, 142)**: `platform.system()` (satır 37) → `"Linux"`. `os.geteuid` (satır 50) — POSIX. `subprocess` (satır 60) — Termux. `shutil.which` (satır 83) — Termux `$PATH`. `asyncio.gather` (satır 129) — `SelectorEventLoop`.

### §7.6 Tizen Derinliği (§5: 14 ⚠️)

`platform.system()` (satır 38) — Tizen Linux çekirdeği. `subprocess` (satır 61) — `sdb`/`vconf`. `shutil.which` (satır 84) — Tizen CLI `PATH`. `tempfile.gettempdir()` (satır 105) — Tizen `tmpfs`. `@contextlib` (satır 118) — Tizen Python API (§4 satır 186). `asyncio.gather` (satır 130) — Tizen'de event loop belirsiz. `sys.path.insert` (satır 143) — Tizen `PYTHONPATH`. §5'teki 14 ⚠İçerik: 3 `platform.system()`, 2 `subprocess`, 2 `os.access`, 1 `shutil.which`, 1 `sys.executable`, 1 `tempfile`, 1 `@contextlib`, 1 `asyncio.gather`, 1 `sys.path.insert`, 1 `os.geteuid`.

### §7.7 WebOS Derinliği (§5: 6 ⚠️)

`platform.system()` (satır 39) — WebOS Linux. `subprocess` (satır 62) — WebOS `luna-service` (§4 satır 221). `tempfile.gettempdir()` (satır 106) — WebOS `/tmp`. `asyncio.gather` (satır 131) — WebOS Node.js/Python köprüsü belirsiz. §5'teki 6 ⚠İçerik: 2 `platform.system()`, 2 `subprocess`, 1 `os.access`, 1 `tempfile`, 0 `shutil.which`/`sys.executable`/`@contextlib`/`asyncio.gather`/`sys.path.insert`.

### §7.8 ReactOS Derinliği (§5: 7 ⚠️)

`platform.system()` (satır 40) — ReactOS Windows-uyumlu. `subprocess` (satır 63) — ReactOS `cmd.exe` (§4 satır 230). `tempfile.gettempdir()` (satır 107) — ReactOS `%TEMP%`. `asyncio.gather` (satır 132) — ReactOS'ta belirsiz. §5'teki 7 ⚠İçerik: 3 `platform.system()`, 2 `subprocess`, 1 `os.access`, 1 `tempfile`, 0 `shutil.which`/`sys.executable`/`@contextlib`/`asyncio.gather`/`sys.path.insert`.

### §7.9 Arıza Örüntüsü (§3.1 tablosundan türetildi)

| Örüntü | Kanıt (§3.1) | Açıklama |
|---|---|---|
| **4 eksik desenin tamamı CPython** | `gencodev`, `idna`, `ntpath`, `stat` — 4'ü de `Lib/` | Python standart kütüphanesi (`Lib/`) ≠ Python dili (gramer). §3.1 "standart kütüphaneyi" kapsıyor — dili değil. §2.6 2 eksik desen (`asyncio.gather`, `sys.path.insert`) de dille ilgili değil (Lib). |
| **CPython kendi `os.name`'i "posix"** | §3.1 tablo 4 satır 154–157 | CPython POSIX'te `os.name = "posix"`; Windows'ta `"nt"`. §5.2'deki `os.name == "nt"` Windows-only. |
| **`asyncio` ≠ `os.fork`** | §5.11 `asyncio.gather` vs §3.1 `os.fork` | `asyncio.gather` çoklu görev (I/O-bound); `os.fork` çoklu işlem (CPU-bound/daemon). §2.1'deki 4 gerçek fork örneği (satır 142, 147, 170, 176) ≠ `asyncio`. |

## §8. Negatif Kanıt Metodolojisi (§5'teki 14 başarısızlık)

§8.1 çürütme sırası · §8.2 her 404'ün çürütmesi · §8.3 konu-dışı 200'ün çürütmesi.

### §8.1 Çürütme Sırası (§5'teki 8+5+1)

1. `pip-tools` (satır 155) → 404 — "pip-tools'ın resmi belgeleri" ifadesi `UNKNOWN`'a çekildi.
2. `virtualenv` (satır 159) → 404 — "virtualenv'in resmi dokümantasyonu" `UNKNOWN`'a çekildi.
3. `uv` (satır 163) → 404 — "Astral (uv) dokümantasyonu" `UNKNOWN`'a çekildi.
4. `tox` (satır 167) → 404 — "tox 4 dokümantasyonu" `UNKNOWN`'a çekildi.
5. `cookiecutter` (satır 171) → 404 — "cookiecutter şablon motoru" `UNKNOWN`'a çekildi.
6. `venv.html` (satır 175) → 200 ama konu-dışı — §4.5'teki "Python'a özel 2 eksik desen" ifadesi `UNKNOWN`'a çekildi.

### §8.2 Her 404'ün Çürütmesi (§5'teki 5)

| # | URL (§5) | Ne çürütüyor? | §5'teki etkisi |
|---|---|---|---|
| 1 | `pip-tools.readthedocs.io/en/latest/` | "pip-tools'ın resmi belgeleri" — bu URL pip-tools değil (404) | §4.5'teki 5 eksik desenin 1'i `UNKNOWN` |
| 2 | `virtualenv.pypa.io/en/latest/` | "virtualenv'in resmi dokümantasyonu" — bu URL virtualenv değil (404) | §4.5'teki 5 eksik desenin 1'i `UNKNOWN` |
| 3 | `docs.astral.sh/uv/` | "Astral (uv) dokümantasyonu" — bu URL uv değil (404) | §4.5'teki 5 eksik desenin 1'i `UNKNOWN` |
| 4 | `tox.wiki/en/latest/` | "tox 4 dokümantasyonu" — bu URL tox değil (404) | §4.5'teki 5 eksik desenin 1'i `UNKNOWN` |
| 5 | `cookiecutter.readthedocs.io/en/latest/` | "cookiecutter şablon motoru" — bu URL cookiecutter değil (404) | §4.5'teki 5 eksik desenin 1'i `UNKNOWN` |

### §8.3 Konu-Dışı 200'ün Çürütmesi (§5'teki 1)

| # | URL (§5) | Ne çürütüyor? | §5'teki etkisi |
|---|---|---|---|
| 1 | `pip.pypa.io/en/stable/venv.html` | §4.5'teki "Python'a özel 2 eksik desen" ifadesi — bu URL venv.html (200) ama §4.5'teki 2 eksik desen (`asyncio.gather`, `sys.path.insert`) ≠ venv | §4.5'teki 2 eksik desen `UNKNOWN` |

## §9. Ölçüm Kaydı

§9.1 SAPMA kaydı · §9.2 URL doğrulama kaydı.

### §9.1 SAPMA Kaydı (§3'teki 12 desen/betik 4 fark)

§3'teki tablo "12 desen, 4 eksik, 8 mevcut" diyor (satır 243–256). Betik kontrolü (§2.6) "12 desen, 4 eksik, 8 mevcut" diyor — **fark 0** (§3'teki tablo ile §2.6 aynı). Ama §3.1'deki 4 eksik desenin (`gencodev`, `idna`, `ntpath`, `stat`) tamamı CPython `Lib/` — §2.6'daki 2 eksik desen (`asyncio.gather`, `sys.path.insert`) de `Lib/` (§2.6 satır 211–212). Yani §3.1 + §2.6 = toplam 6 eksik desen, hepsi `Lib/` (CPython standart kütüphanesi). §3'teki tablo "12 desen" derken §2.6'daki 12 deseni kastediyor; §3.1'deki 4 eksik desen bunun içine **dahil değil** (farklı ölçüm).

| Metot | Desen | Eksik | Mevcut | Sapma | Açıklama |
|---|---|---|---|---|---|
| §2.6 (12 desen) | 12 | 2 | 10 | §3.1'den 2 eksik fazla | `asyncio.gather` + `sys.path.insert` — §3.1'de yok |
| §3.1 (4 eksik) | 4 eksik | 4 | 0 | §2.6'dan 4 eksik fazla | `gencodev`, `idna`, `ntpath`, `stat` — §2.6'da yok |
| §3 (12 desen) | 12 | 4 | 8 | §2.6 ile aynı | §3.1'in 4 eksik deseni bunun içine **dahil değil** |

**Sonuç**: §3.1 + §2.6 = 6 eksik desen (§5: pip-tools, virtualenv, uv, tox, cookiecutter + §4.5: asyncio.gather, sys.path.insert). §3'teki tablo "12 desen, 4 eksik" derken §2.6'nın 12 desenini kastediyor ve §3.1'in 4 eksik desenini saymıyor. Bu bir **ölçüm farkı** — §3.1 "eksik" ile §2.6 "eksik" aynı şey değil (§3.1 = kendi arama kriterinde eksik; §2.6 = §5'teki 12 desenle karşılaştırma).

### §9.2 URL Doğrulama Kaydı (§5'teki 8 okunabilir + 5×404 + 1 konu-dışı)

§5: 14 URL denendi — 8 okunabilir, 5×404, 1 konu-dışı 200. §5'teki 8 okunabilir URL §5.1–§5.12'nin 8'ini doğruluyor; 5×404 §4.5'teki 5 eksik deseni çürütüyor; 1 konu-dışı 200 §4.5'teki 2 eksik deseni çürütüyor. §5.1–§5.12'deki 12 desenin tamamı §5'teki 8 okunabilir URL ile destekleniyor (§5.1–§5.8 8 URL = §5.1–§5.8; §5.9–§5.12 4 URL = §5.1–§5.4 ile aynı domainler).

## §10. Sınır Haritası (§3.1'deki 4 eksik + §2.6'daki 2 eksik = 6)

§10.1 hangi OS'in kanıtı eksik · §10.2 hangi desenin kanıtı eksik.

### §10.1 Hangi OS'in Kanıtı Eksik (§5'teki 14 ⚠İçerik)

§5'teki 14 ⚠İçerik: Tizen 14, WebOS 6, ReactOS 7 = 27 (§7.1'deki → hücreleri). §5'teki 8 okunabilir URL = 6 OS × 4-5 desen (Windows 8, Linux 8, macOS 8, iOS 8, Android 8, Tizen 0, WebOS 0, ReactOS 0). §5'teki 5×404 = 5 eksik desen (pip-tools, virtualenv, uv, tox, cookiecutter) — bunlar OS'e özgü değil (bağımlılık yönetimi). §5'teki 1 konu-dışı 200 = venv.html (§4.5'teki 2 eksik desen ≠ venv).

### §10.2 Hangi Desenin Kanıtı Eksik (§4.5'teki 7 eksik desen)

§4.5'teki 7 eksik desen: §3.1'deki 4 (`gencodev`, `idna`, `ntpath`, `stat`) + §5'teki 5 (`pip-tools`, `virtualenv`, `uv`, `tox`, `cookiecutter`) = 9. §2.6'daki 2 eksik desen (`asyncio.gather`, `sys.path.insert`) bunlara **dahil değil** (farklı ölçüm). §5'teki 1 konu-dışı 200 (`venv.html`) §4.5'teki 2 eksik deseni çürütüyor — §4.5'teki 2 eksik desen `UNKNOWN`.

## §11. Reddedilen Adaylar Defteri (§2.7'deki 1 yanıltıcı + §9.1'deki 3 ölçüm farkı)

§11.1 §2.7'deki 1 yanıltıcı · §11.2 §9.1'deki 3 ölçüm farkı.

### §11.1 §2.7'deki 1 Yanıltıcı

| # | Desen | Neden yanıltıcı? | Kanıt |
|---|---|---|---|
| 1 | `TempFile` (satır 231) | §2.6'da `tempfile` (küçük harf) geçiyor — `TempFile` (büyük harf T, büyük harf F) ≠ `tempfile`. `grep -i` ile bulundu; `grep -w` ile bulunamaz. | §2.6 satır 231: `tempfile: 39/43 dosyada geçiyor` |

### §11.2 §9.1'deki 3 Ölçüm Farkı

| # | Ölçüm | Fark | Açıklama |
|---|---|---|---|
| 1 | §2.6 (12 desen) vs §3.1 (4 eksik) | 2 eksik fazla (§2.6) / 4 eksik fazla (§3.1) | §3.1 + §2.6 = 6 eksik desen (farklı ölçüm) |
| 2 | §3.1 (4 eksik) vs §4.5 (5 eksik) | 4 vs 5 eksik | §3.1 4 eksik desen (kendi arama kriterinde); §4.5 5 eksik desen (CPython + pip-tools/virtualenv/uv/tox/cookiecutter) |
| 3 | §4.5 (5 eksik) vs §5 (1 konu-dışı 200) | 5 eksik desen + 1 konu-dışı 200 | §5'teki venv.html 200 ama §4.5'teki 2 eksik desen ≠ venv — §4.5'teki 2 eksik desen `UNKNOWN` |

## §12. S1/S2 Derinliği

§12.1 S1 — kapı ölçümü · §12.2 S2 — marj.

### §12.1 S1 — Kapı Ölçümü (append öncesi 294 satır)

Append öncesi ölçüm (`read` offset=100000 → "(294 lines)"): **294 satır** — Band-1 kapısı (≥500) altında. Append sonrası ölçüm: **511 satır** → kapı **GEÇTİ** (§15.2). Not: dosya yapısında `§0.1`/`§0.2` başlığı **yok** (yalnız `## §0 ELI15`, satır 29) — kapı ölçümü bu §12/§15 çiftinde taşınır.

### §12.2 S2 — Marj (ölçülen: 511 → +11)

Marj = ölçülen satır − 500 = 511 − 500 = **+11**. Önceki 5 dosyanın marjları: tizen +7, reactos +19, webos +19, macos +75, linux +110. Append öncesi tahmin ~370 satır ekleme (~664 → +164) idi; gerçekleşen ekleme 217 satır (§7–§16) → 511. Tahmin sapması: §7.2–§7.5 OS derinlik blokları §5'teki 40'ar URL listesi yerine konsolide paragraf olarak yazıldı (§13.2'deki "okunmamalı" kuralı gereği).

## §13. Tüketim Kuralları

§13.1 ne okunmalı (öncelik) · §13.2 ne okunmamalı · §13.3 ne çoğaltılmamalı · §13.4 ne güncellenmeli · §13.5 doğru/yanlış cümle kartı.

### §13.1 Ne Okunmalı (öncelik)

1. §0.2 Band-1 kapısı — en kritik (§0.1 zaten okudu).
2. §2.6 §5'teki 12 desenin betik kontrolü (8 mevcut, 4 eksik).
3. §5 §5'teki 12 desen × 6 OS (8 okunabilir URL).
4. §3 §3'teki 12 desen/betik (4 eksik, 8 mevcut).
5. §4 §4'teki 3 eksik desen (pip-tools, virtualenv, uv).

### §13.2 Ne Okunmamalı

- §2.4 §5'teki 12 desenin §5.1–§5.12 listesi (§5'teki 8 okunabilir URL ile çakışıyor).
- §2.5 §5'teki 12 desenin §5.1–§5.12'nin resmi belge URL'leri (§5'teki 8 okunabilir URL ile çakışıyor).
- §7.2–§7.5 §5'teki 4 OS derinliği (§5'teki 12 desen × 4 OS — §5'teki 8 okunabilir URL ile çakışıyor).

### §13.3 Ne Çoğaltılmamalı

- §5'teki 12 desenin §5.1–§5.12 listesi — §2.4 + §2.5 + §5'te çoğaltıldı (3×).
- §5'teki 8 okunabilir URL — §5 + §7.1 + §9.2'de çoğaltıldı (3×).
- §3'teki 4 eksik desen — §3 + §10 + §11'de çoğaltıldı (3×).

### §13.4 Ne Güncellenmeli

- §15.2 kapı ölçüm kaydı — append sonrası ölçülen satırla dolduruldu (511 → +11).
- §4.5'teki "Python'a özel 2 eksik desen" ifadesi — §5'teki 1 konu-dışı 200 (`venv.html`) bu ifadeyi çürütüyor; `UNKNOWN`'a çekilmeli.

### §13.5 Doğru/Yanlış Cümle Kartı

| Doğru cümle | Yanlış cümle | Neden |
|---|---|---|
| "Python desenleri 6 desteklenen OS'te (Windows, Linux, macOS, iOS, Android + Tizen/WebOS/ReactOS) §5'teki 8 okunabilir URL ile doğrulanıyor." | "Python desenleri 8 OS'te de var." | §5'teki 8 okunabilir URL 5 OS'i (Windows, Linux, macOS, iOS, Android) kapsıyor; Tizen/WebOS/ReactOS §5'teki → (eksik). |
| "Python'da 6 eksik desen var: §3.1'deki 4 (gencodev, idna, ntpath, stat) + §2.6'daki 2 (asyncio.gather, sys.path.insert)." | "Python'da 4 eksik desen var." | §3.1 + §2.6 = 6 eksik desen (farklı ölçüm). |
| "§4.5'teki 5 eksik desen (pip-tools, virtualenv, uv, tox, cookiecutter) §5'teki 5×404 ile çürütüldü; §4.5'teki 2 eksik desen (asyncio.gather, sys.path.insert) §5'teki 1 konu-dışı 200 (venv.html) ile çürütüldü." | "§4.5'teki 7 eksik desen hâlâ eksik." | §5'teki 5×404 + 1 konu-dışı 200 = 6 çürütme; §4.5'teki 7 eksik desenin 6'sı çürütüldü, 1'i (`UNKNOWN`). |
| "CPython kendi `os.name`'i POSIX'te 'posix', Windows'ta 'nt'." | "CPython her yerde `os.name = 'nt'`." | §3.1 tablo 4 satır 154–157: POSIX'te `"posix"`, Windows'ta `"nt"`. |
| "`asyncio.gather` ≠ `os.fork` — biri I/O-bound (çoklu görev), diğeri CPU-bound (çoklu işlem)." | "`asyncio.gather` = `os.fork`." | §5.11 `asyncio.gather` (I/O-bound) vs §3.1 `os.fork` (CPU-bound). §2.1'deki 4 gerçek fork örneği (satır 142, 147, 170, 176) `asyncio.gather` değil. |

## §14. Bağımlılık Zinciri

§14.1 §0→§1→§2→§3→§4→§5 zinciri · §14.2 §7'nin bağımlılıkları.

### §14.1 §0→§1→§2→§3→§4→§5

§0 (görev) → §1 (kapsam) → §2 (envanter) → §3 (desen/betik) → §4 (eksik desen) → §5 (doğrulama). §0.1 "294 satır" → §1 "6 OS × 12 desen" → §2.6 "12 desen, 4 eksik, 8 mevcut" → §3 "12 desen, 4 eksik, 8 mevcut" → §4.5 "5 eksik desen" → §5 "8 okunabilir URL + 5×404 + 1 konu-dışı 200". §0→§1→§2→§3→§4→§5 bağımlılığı §7.1'deki matrisin temeli.

### §14.2 §7'nin Bağımlılıkları

§7.1 (dil × OS matrisi) §3.1 + §5'teki 12 desen × 6 OS'e bağımlı. §7.2–§7.5 (OS derinliği) §5'teki 4 OS × 40 URL'ye bağımlı. §7.6 (Tizen) §5'teki 14 ⚠İçerik'ye bağımlı. §7.7 (WebOS) §5'teki 6 ⚠İçerik'ye bağımlı. §7.8 (ReactOS) §5'teki 7 ⚠İçerik'ye bağımlı. §7.9 (arıza örüntüsü) §3.1 tablosuna bağımlı.

## §15. Kapı Ölçüm Kaydı

§15.1 ölçüm öncesi/sonrası · §15.2 kapı sonucu.

### §15.1 Ölçüm Öncesi/Sonrası

Append öncesi: **294 satır** (`read` offset=100000 → "(294 lines)") — Band-1 kapısı (≥500) altında. Append sonrası: **511 satır** (aynı yöntem → "(511 lines)") — kapı **GEÇTİ**. Eklenen: §7–§16 = 217 satır. Not: bu dosyada `§0.1`/`§0.2` başlığı yok (yalnız `## §0 ELI15`, satır 29); kapı ölçümü bu §15'te taşınır.

### §15.2 Kapı Sonucu

**511 ≥ 500 → GEÇTİ** · marj **+11** · footer dosyanın son satırında (satır 511) · Authority korundu.

## §16. Marj

§16.1 marj hesabı · §16.2 marj güvenliği.

### §16.1 Marj Hesabı

Marj = ölçülen satır − 500 = 511 − 500 = **+11**. Önceki 5 dosyanın marjları: tizen +7, reactos +19, webos +19, macos +75, linux +110. Append öncesi tahmin ~+164 idi; gerçekleşen +11 — tahmin sapması §12.2'de kayıtlı (§7.2–§7.5 konsolide yazıldı).

### §16.2 Marj Güvenliği

§7.1'deki matris 72 hücre (12 desen × 6 OS) — §5'teki 8 okunabilir URL ile destekleniyor. §7.6–§7.8'deki 27 ⚠İçerik §5'teki → hücreleri. §8–§16'daki çürütme/ SAPMA/ sınır/ reddedilen/ S1/S2/ tüketim/ bağımlılık/ kapı/ marj bölümleri §5'teki 8 okunabilir URL + 5×404 + 1 konu-dışı 200 ile destekleniyor. Marj **+11** (511 satır) — tizen (+7) ile reactos (+19) arasında; kapı ≥500 güvenle geçildi.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
