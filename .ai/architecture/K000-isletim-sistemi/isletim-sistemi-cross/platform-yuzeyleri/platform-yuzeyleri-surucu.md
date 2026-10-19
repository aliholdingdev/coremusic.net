---
node_id: K000-isletim-sistemi/isletim-sistemi-cross/platform-yuzeyleri/platform-yuzeyleri-surucu
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-cross/platform-yuzeyleri
children: []
depends_on: []
evidence:
  - "home.coremusic.net/.htaccess:18"
  - "home.coremusic.net/web.config:20"
  - "https://learn.microsoft.com/en-us/windows/win32/debug/pe-format"
  - "https://learn.microsoft.com/en-us/windows/win32/api/winsvc/nf-winsvc-createservicea"
validation: "2026-10-13 · 4 URL HEAD 200 · envanter kesişimi 0 · repo satırları Select-Object -Index (N-1) ile okundu"
open_questions:
  - "Q5 — Windows'ta üretim servisi (servis/SCT) kurulacak mı UNKNOWN"
  - "Q-X12 — dosya koruma kuralları (.htaccess ↔ web.config) tek kaynaktan üretilmiyor"
group: mimari
---

# platform-yuzeyleri-surucu — Kademe-4 Sürücü & Servis Yüzeyi

> **Kapsam:** Bu dosya, işletim sistemi **sürücü / servis / yükleyici (loader)** yüzeyinin
> karşılaştırmasıdır: PE-ELF-Mach-O ikili biçimi, DLL/.so/.dylib yükleme, servis yönetim
> yüzeyi (SCM vs systemd vs launchd), uygulama verisi depolama yüzeyi, dosya-koruma
> eşlemesi ve repo kanıtı. **Kanıt dili:** S1 = birebir repo satırı · S4 = mimari yorum ·
> S7 = bilinmeyen (UNKNOWN).

| Öğe | Değer |
|---|---|
| node_id | `K000-isletim-sistemi/isletim-sistemi-cross/platform-yuzeyleri/platform-yuzeyleri-surucu` |
| parent_id | `…/isletim-sistemi-cross/platform-yuzeyleri` |
| group | `mimari` |
| Komşular | `isletim-sistemi-kernel` · `isletim-sistemi-runtime` · `isletim-sistemi-security` · `../derleme-ve-paketleme` |
| Repo gerçeği | Native kod yok (`.cpp`/`.h` glob = 0) → kanıtlar web/CI katmanından; sürücü kodu PLANNED |

## §0 ELI15

Bir işletim sistemi, diske yazılmış ikili dosyayı (`.exe`/`.dll`, ELF bin/.so, Mach-O)
**yükleyici (loader)** ile hafızaya alır; arka planda kalan uzun ömürlü işleri **servis**
(SCM), **systemd birimi** veya **launchd** başlatır. Depo bu dünyada henüz native kod
içermiyor; buradaki tek gerçek yüzey, web sunucusunun dosya-koruma ve yeniden-yazma
kurallarıdır (`.htaccess` ↔ `web.config` çifti) — bu da "platform farkı yutulmaz, iki
yapılandırma yazılır" ilkesinin canlı kanıtıdır.

| Yüzey | Windows | Linux | macOS |
|---|---|---|---|
| İkili biçim | PE (`.exe`/`.dll`) | ELF (bin/`.so`) | Mach-O |
| Yükleyici | Windows loader (PEB/TEB) | `ld.so` (dynamic linker) | `dyld` |
| Uzun ömürlü iş | Servis (SCM) | systemd birimi | launchd kare |
| Uygulama verisi | `%APPDATA%` (Roaming) | `XDG_DATA_HOME` | `~/Library/Application Support` |
| Dosya koruma yüzeyi | `web.config` (IIS) | `.htaccess` (Apache) | `.htaccess` (Apache) |

## §1 İkili biçim matrisi (PE / ELF / Mach-O)

| # | Özellik | PE (Windows) | ELF (Linux) | Mach-O (macOS) |
|---|---|---|---|---|
| 1 | Başlık sihirli sayısı | `MZ` (0x5A4D) | `\x7fELF` | `0xFEEDFACF` (64-bit) |
| 2 | Bölüm/bölge adı | section | section | segment + section |
| 3 | Yeniden konumlandırma | `.reloc` | `R_*` reloc türleri | rebase/bind bilgisi |
| 4 | Sembol dışa açma | export table (`.edata`) | dynamic symbol table | export trie / symbol table |
| 5 | Bağımlılık listesi | import table (`.idata`) | `DT_NEEDED` | `LC_LOAD_DYLIB` |
| 6 | Çalışma zamanı bilgisi | TLS directory | `PT_TLS` | `LC_THREAD` |
| 7 | Yapılandırma | manifest (RT_MANIFEST) | yok (dosya/ortam) | Info.plist |
| 8 | İmza/diagnostik | Authenticode | notu (distro) | code signature (zorunlu) |
| 9 | Yüklenen adres (varsayılan) | rebase (ASLR) | PIE varsayılan | ASLR (sliding) |
| 10 | Hata ayıklama | CodeView (PDB) | DWARF | DWARF + dSYM |

**Kaynak:** PE biçimi tanımı (§9 #1); ELF/Mach-O satırları **S4** sınıfındadır — bu depoda
native derleme yok, doğrudan kod kanıtı üretilemez (§6 dürüstlük satırı).

## §2 Yükleyici / kütüphane yükleme yüzeyi

### §2.1 Yükleme API'leri

| # | İş | Windows | Linux | macOS |
|---|---|---|---|---|
| 1 | Kütüphane açma | `LoadLibraryW` | `dlopen` | `dlopen` |
| 2 | Sembol bulma | `GetProcAddress` | `dlsym` | `dlsym` |
| 3 | Kapatma | `FreeLibrary` | `dlclose` | `dlclose` |
| 4 | Son hata | `GetLastError` | `dlerror` | `dlerror` |
| 5 | Sabit bağlama | import table (derleme anı) | `RTLD_NOW` | `RTLD_NOW` |
| 6 | Yerel sembol | `GetProcAddress` (yalnız export) | `RTLD_LOCAL` | `RTLD_LOCAL` |
| 7 | Erişilebilir listeye ekleme | loader listesi | `RTLD_GLOBAL` | `RTLD_GLOBAL` |
| 8 | Sıra | yüklenme sırası (import zinciri) | `LD_PRELOAD` ile öne alma | `DYLD_INSERT_LIBRARIES` |

### §2.2 Yükleme hataları

| # | Hata | Windows yüzeyi | POSIX yüzeyi |
|---|---|---|---|
| 1 | Dosya yok | ERROR_MOD_NOT_FOUND (126) | `dlopen` → NULL + `dlerror` |
| 2 | Bağımlılık eksik | ERROR_DLL_NOT_FOUND (126) | `ld.so` "cannot open shared object" |
| 3 | Mimar uyumsuz | ERROR_BAD_EXE_FORMAT (193) | "wrong ELF class" |
| 4 | Sembol yok | ERROR_PROC_NOT_FOUND (127) | `dlsym` → NULL |
| 5 | Sürüm uyuşmazlığı | SxS/manifest hatası | `DT_VERNEED` (glibc) |

## §3 Servis / süreç başlatma yüzeyi

> Servis API'sinin bağlayıcı tanımı §9 #2'dedir; burada yalnız karşılaştırma tablosu var.

| # | İş | Windows SCM | Linux systemd | macOS launchd |
|---|---|---|---|---|
| 1 | Kayıt | `CreateService`/`OpenSCManager` | birim dosyası (`systemctl enable`) | plist (LaunchDaemons) |
| 2 | Başlatma | `StartService` | `systemctl start` | `launchctl load` |
| 3 | Durdurma | `ControlService` | `systemctl stop` | `launchctl unload` |
| 4 | Durum sorgu | `QueryServiceStatus` | `systemctl is-active` | `launchctl list` |
| 5 | Otomatik başlangıç | SERVICE_AUTO_START | `WantedBy=multi-user.target` | `RunAtLoad` |
| 6 | Yeniden başlatma | SCM recovery (SC_ACTION_RESTART) | `Restart=on-failure` | `KeepAlive` |
| 7 | Bağımlılık | SERVICES_DEPENDENT_SERVICE | `After=`/`Requires=` | `LaunchOnce` sırası |
| 8 | Kimlik | hesap (LocalSystem vb.) | `User=`/`Group=` | `UserName` |
| 9 | Günlük | Event Log | journald | unified log |
| 10 | Port bekçi (socket activation) | yok (uygulama kendi açar) | `socket` birimi | launchd socket |

**Bu depoda karşılığı:** servis kavramı **yoktur** — web sunucusu zaten ayaktadır; en yakın
kanıt Apache/IIS yapılandırma çiftidir (§6 #5-7).

## §4 Uygulama verisi / yapılandırma depolama yüzeyi

| # | Veri türü | Windows | Linux | macOS |
|---|---|---|---|---|
| 1 | Kullanıcı verisi (roaming) | `%APPDATA%` | `XDG_DATA_HOME` (vars. `~/.local/share`) | `~/Library/Application Support` |
| 2 | Kullanıcı yapılandırması | `%APPDATA%` | `XDG_CONFIG_HOME` (vars. `~/.config`) | `~/Library/Preferences` |
| 3 | Önbellek | `%LOCALAPPDATA%` | `XDG_CACHE_HOME` | `~/Library/Caches` |
| 4 | Makine geneli | `ProgramData` | `/etc` | `/Library` |
| 5 | Log | `%ProgramData%\...\Logs` | `/var/log` | `~/Library/Logs` |
| 6 | Geçici | `%TEMP%` | `$TMPDIR`/`/tmp` | `$TMPDIR` |
| 7 | Uygulama sandbox verisi | UWP `ApplicationData` (§9 #4) | yok (Flatpak `~/.var/app`) | App Sandbox container |

**Repo bağlantısı:** PHP geçici dizini `sys_get_temp_dir()` ile platformdan soyutlanır —
kanıt `shared/src/Component/TemplateEngine.php:73` (bkz. `platform-yuzeyleri-anayasa.md` §6 #1).

## §5 Dosya-koruma yüzeyi eşlemesi (repo kanıt matrisi)

| # | Kural | Apache (`.htaccess`) | IIS (`web.config`) | Eşit mi? |
|---|---|---|---|---|
| 1 | Hassas dosya reddi | `<FilesMatch "\.(env|log|json|…)$">` satır 18-20 | `<match url="\.(env|log|json|…)$">` satır 24-27 | uzantı kümesi benzer, söz dizimi farklı |
| 2 | Dizin engeli | yok ayrı kural | `^(config|include|pages)(/.*)?$` satır 20-23 | **hayır** — IIS kuralı Apache'de ayrı kurala bağlanmalı |
| 3 | PHP doğrudan erişim reddi | `<FilesMatch "^(?!index\.php$)(?!debug\.php$).+\.php$">` satır 22-24 | yok (IIS handler ile) | **hayır** |
| 4 | Statik dosya | `mod_rewrite` zinciri | `<rule name="Static files">` satır 16-19 | farklı mekanizma, benzer sonuç |
| 5 | Sunucu imzası gizleme | `Header always unset Server` satır 14-15 | IIS `<serverRuntime>` (ayrı dosya) | **hayır** |

**Sonuç (S4):** Dosya-koruma yüzeyi **tek yazım değil, iki yapılandırmadır**; ikisinin
eşitliği elle korunur ve her yeni kural **iki dosyaya birden** yazılmalıdır — bu,
platform-yüzeyi farkının depodaki tek somut tezahürüdür.

## §6 Repo kanıtı

> Bu depoda sürücü/servis native kodu **YOKTUR** (`*.cpp`/`*.h` glob = 0). Aşağıdaki
> satırlar, depodaki **tek** platform-yüzeyi kanıtlarıdır: web sunucusu dosya-koruma çifti.

**Kanıt 1 — Apache dosya reddi (S1):** `home.coremusic.net/.htaccess:18-24`

```apache
<FilesMatch "\.(env|log|json|lock|md|sql|sh|bak|git|ini|htpasswd|DS_Store)$">
    Require all denied
</FilesMatch>

<FilesMatch "^(?!index\.php$)(?!debug\.php$).+\.php$">
    Require all denied
</FilesMatch>
```

**Kanıt 2 — IIS karşılığı (S1):** `home.coremusic.net/web.config:20-27`

```xml
<rule name="Block config/include/pages" stopProcessing="true">
    <match url="^(config|include|pages)(/.*)?$" ignoreCase="false" />
    <action type="CustomResponse" statusCode="403" statusReason="Forbidden" statusDescription="Forbidden" />
</rule>
<rule name="Block sensitive files" stopProcessing="true">
    <match url="\.(env|log|json|lock|md|sql|sh|bak|git|ini|htpasswd)$" ignoreCase="false" />
    <action type="CustomResponse" statusCode="403" statusReason="Forbidden" statusDescription="Forbidden" />
</rule>
```

| # | Kanıt | Dosya:Satır | Ne kanıtlar | Kanıt sınıfı |
|---|---|---|---|---|
| 1 | `<FilesMatch>` reddi | `home.coremusic.net/.htaccess:18-20` | Apache yüzeyi | S1 |
| 2 | PHP erişim reddi (negative lookahead) | `home.coremusic.net/.htaccess:22-24` | Apache yüzeyi | S1 |
| 3 | IIS kural engeli | `home.coremusic.net/web.config:20-23` | IIS yüzeyi | S1 |
| 4 | IIS hassas dosya reddi | `home.coremusic.net/web.config:24-27` | IIS yüzeyi | S1 |
| 5 | Uzantı kümesi farkı (`.DS_Store` yalnız Apache'de) | `.htaccess:18` ↔ `web.config:25` | eşleme elle, sürüklenmeye açık | S1 |
| 6 | 4 alt-alanda çift yapılandırma | `api/`·`assets/`·`auth/`·`home/` | platform farkı yutulmaz | S1 |
| 7 | Native sürücü kodu | `*.cpp`/`*.h` glob = 0 | §1-§4 PLANNED | S1 |
| 8 | Servis kurulumu | kayıt yok | S7 (UNKNOWN) |

## §7 Riskler

| # | Risk | Olasılık | Etki | Bağlantı |
|---|---|---|---|---|
| R1 | `.htaccess` kuralı `web.config`'e taşınmadan unutulur (`.DS_Store` gibi) | yüksek | yüksek | §5 #5 |
| R2 | IIS `^(config|include|pages)` engeli Apache'de ayrı kurala bağlı değilse boşluk | orta | yüksek | §5 #2 |
| R3 | PE/ELF ayrımı Neva Engine PLANNED iken göz ardı edilir (yanlış `long`/alignment) | orta | yüksek | anayasa §2.3 |
| R4 | Servis olarak çalıştırma (SCM) düşünülmeden dosya yolları sabitlenir | düşük | orta | §3 |
| R5 | `%APPDATA%`/`XDG` farkı — kullanıcı verisi yanlış dizine yazılır | düşük | orta | §4 |
| R6 | `.htaccess` Apache-only — IIS'de etkisiz, nginx'de yok (üretim sunucusu UNKNOWN) | orta | yüksek | §6 #8 |
| R7 | Dosya-koruma kuralları tek kaynaktan üretilmiyor; drift tespiti manuel | yüksek | orta | Q-X12 |
| R8 | Servis kimliği/Yetki hesabı kararı yok (S7) | orta | orta | Q5 |

## §8 Kapsam dışı

| # | Konu | Nereye gider | Neden bu dalda değil |
|---|---|---|---|
| 1 | Sürücü geliştirme (WDM/KMDF) | `isletim-sistemi-kernel` | Çekirdek katmanı sınır dışı |
| 2 | PE başlığının bayt bayt dökümü | `../tasinabilirlik-siniri` | Biçim derinliği orada |
| 3 | Paket formatları (MSI/deb/rpm) | `../derleme-ve-paketleme` | Dağıtım ayrı alt-dal |
| 4 | Servis güvenlik politikası | `isletim-sistemi-security` | Güvenlik ayrı dal |
| 5 | WASAPI/CoreAudio cihaz sürücüleri | `isletim-sistemi-audio` | Ses katmanı ayrı dal |
| 6 | Gözlemlenebilirlik (Event Log/journald) | `isletim-sistemi-observability` | Ölçüm ≠ gözetim |
| 7 | Runtime/örtük bağlayıcı davranışı | `isletim-sistemi-runtime` | Çalışma zamanı ayrı dal |
| 8 | Kod örnekleri | `platform-yuzeyleri-ornekler.md` | Komşu modül |

## §9 Kaynakça (web araştırma · 2026-10-13 · HEAD · envanter kesişimi 0)

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| PE biçimi (MZ/PE imzası, section yapısı) Microsoft tarafından tanımlanır | https://learn.microsoft.com/en-us/windows/win32/debug/pe-format | 2026-10-13 | ✅ HTTP 200 |
| `CreateService` Windows servis kaydını SCM üzerinden yapar | https://learn.microsoft.com/en-us/windows/win32/api/winsvc/nf-winsvc-createservicea | 2026-10-13 | ✅ HTTP 200 |
| `DeviceIoControl` sürücüye özel IOCTL göndermek için kullanılır | https://learn.microsoft.com/en-us/windows/win32/api/ioapiset/nf-ioapiset-deviceiocontrol | 2026-10-13 | ✅ HTTP 200 |
| UWP `ApplicationData` uygulama verisi deposunu tanımlar | https://learn.microsoft.com/en-us/uwp/api/Windows.Storage.ApplicationData | 2026-10-13 | ✅ HTTP 200 |

## §10 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| Q5 | Windows'ta üretim servisi (SCM) kurulacak mı? | ⚠️ VERIFICATION REQUIRED |
| Q-X12 | `.htaccess` ↔ `web.config` eşlemesi tek kaynaktan üretilecek mi (drift kapısı)? | UNKNOWN |
| Q-X13 | Üretim sunucusu Apache mi IIS mi (nginx)? | UNKNOWN |
| Q-X14 | Dosya-koruma uzantı listesi güvenlik ekibi (isletim-sistemi-security) ile paylaşıldı mı? | ⚠️ VERIFICATION REQUIRED |
| Q-X22 | Kullanıcı verisi dizini kararı (§4 matrisi hangi satır seçilecek?) | UNKNOWN |

## §11 IOCTL / sürücü iletişim yüzeyi (S4 — PLANNED, Neva Engine için)

> Bu bölüm depoda **kod kanıtı olmadan** durur (native kod yok); Neva Engine PLANNED
> olduğunda sürücü-kullanıcı uzayına çıkacak IOCTL/disk yüzeyinin **tasarım notlarıdır**.
> Doğrulayıcı kaynak §9 #3'tedir (`DeviceIoControl`, ioapiset).

### §11.1 IOCTL iletim kontrol kodu yapısı (Windows)

| # | Alan | Bit | Anlam |
|---|---|---|---|
| 1 | DeviceType | 16-31 | cihaz sınıfı (FILE_DEVICE_*) |
| 2 | Access | 14-15 | erişim hakkı (ANY/READ/WRITE) |
| 3 | Function | 2-13 | kod (0x800 tabanı METHOD_* bitleriyle) |
| 4 | Method | 0-1 | iletim yöntemi |
| 5 | Custom | 16 | üreticiye özel bit |

### §11.2 İletim yöntemi (METHOD_*) matrisi

| # | Yöntem | Veri kopyası | Bellek | Kullanım |
|---|---|---|---|---|
| 1 | METHOD_BUFFERED | çekirdek kopyalar | tek tampon | küçük veri, güvenli |
| 2 | METHOD_IN_DIRECT | giriş doğrudan | MDL | DMA okuma |
| 3 | METHOD_OUT_DIRECT | çıkış doğrudan | MDL | DMA yazma |
| 4 | METHOD_NEITHER | yok | user pointer | dikkatli doğrulama gerek |
| 5 | (POSIX karşılığı) | `ioctl(2)` | sürücüye özel | POSIX'te tek çağrı |

### §11.3 Sürücü-kullanıcı yüzeyi karşılaştırma matrisi

| # | İş | Windows | Linux | macOS |
|---|---|---|---|---|
| 1 | Genel sürücü çağrısı | `DeviceIoControl` | `ioctl` | `ioctl` (IOKit farklı) |
| 2 | Cihaz açma | `CreateFileW("\\.\Ad")` | `open("/dev/x")` | `open("/dev/x")` |
| 3 | Doğrudan disk erişimi | `\\.\PhysicalDriveN` | `/dev/sdX` | `/dev/rdiskN` |
| 4 | Ayrık/buffer | kendi tamponu | `O_DIRECT` | `F_NOCACHE` |
| 5 | Sürücü imzalama | zorunlu (WHQL/test) | yok (modül imzası seçili) | imzalama zorunlu |
| 6 | Kullanıcı kütüphanesi | Winsock/WinUSB | libusb | IOKit |

**Neva Engine bağlamı (PLANNED):** depoda disk/IOCTL kodu yoktur (`*.cpp`/`.h` = 0);
ses motoru doğrudan disk erişimi gerektirmez — bu tablo yalnızca komşu `isletim-sistemi-kernel`
dalı için sınır tanımıdır.

## §12 Servis yaşam döngüsü — ayrıntılı durum matrisi

| # | Durum | Windows SCM | systemd | launchd |
|---|---|---|---|---|
| 1 | Kayıtlı/başlamamış | SERVICE_STOPPED | loaded (disabled) | unloaded |
| 2 | Başlatılıyor | SERVICE_START_PENDING | activating | (kare aktifleşiyor) |
| 3 | Çalışıyor | SERVICE_RUNNING | active (running) | çalışıyor |
| 4 | Duruyor | SERVICE_STOP_PENDING | deactivating | durduruluyor |
| 5 | Hata | SERVICE_STOPPED (exit code) | failed | çıkış kodu |
| 6 | Yeniden başlatma kararı | recovery tablosu | `Restart=` politikası | `KeepAlive` |
| 7 | Zamanlanmış başlatma | Task Scheduler (SCM dışı) | `timer` birimi | `StartCalendarInterval` |
| 8 | Sınıf (desktop/oturum) | ServiceType bitleri | sistem/kullanıcı birimi | LaunchDaemons/LaunchAgents |

## §13 Dosya biçimi yükleme sırası (her platform, 8 adım)

| # | Adım | Windows (PE) | Linux (ELF) | macOS (Mach-O) |
|---|---|---|---|---|
| 1 | Doğrulama | imza/manifest | notu/ELF başlık | code signature |
| 2 | Bağımlılık çözümü | import zinciri | `DT_NEEDED` + `ld.so` | `dyld` + `LC_LOAD_DYLIB` |
| 3 | Yerleştirme | rebase (ASLR) | PIE load | slide (ASLR) |
| 4 | Relokasyon | `.reloc` | `R_*` | rebase bind |
| 5 | Sembol çözümü | IAT dolgusu | lazy bind (PLT/GOT) | lazy bind |
| 6 | TLS başlatma | TLS directory | `PT_TLS` | `LC_THREAD` |
| 7 | DllMain/constructor | `DllMain(DLL_PROCESS_ATTACH)` | `.init_array` | `__attribute__((constructor))` |
| 8 | Giriş noktası | `AddressOfEntryPoint` | `_start` → `main` | `_main` → `dyld` |

## §14 Uygulama verisi yolları — karar tablosu (hangi veri nereye)

| # | Veri | Karar | Windows | Linux | macOS |
|---|---|---|---|---|---|
| 1 | Kullanıcı ayarları | yazılabilir | `%APPDATA%` | `~/.config` | `~/Library/Preferences` |
| 2 | Kullanıcı medyası | yazılabilir | `Documents` | `~/` | `~/Documents` |
| 3 | Önbellek | silinebilir | `%LOCALAPPDATA%` | `~/.cache` | `~/Library/Caches` |
| 4 | Log | silinebilir | `%LOCALAPPDATA%\Logs` | `~/.local/state/...` | `~/Library/Logs` |
| 5 | Sistem yapılandırması | salt-okunur | `ProgramData` | `/etc` | `/Library/Preferences` |
| 6 | Geçici dosya | silinebilir | `%TEMP%` | `/tmp` (veya `$TMPDIR`) | `$TMPDIR` |
| 7 | Uygulama verisi (UWP) | sandbox | `ApplicationData` (§9 #4) | — | — |
| 8 | PHP temp | platform soyutlu | `sys_get_temp_dir()` (§6 #1) | aynı | aynı |

## §15 Ölçüm / kabul kriterleri

| # | Ölçüm | Yöntem | Eşik |
|---|---|---|---|
| 1 | Dosya-koruma çifti drift | `.htaccess` uzantı listesi ↔ `web.config` kümesi karşılaştırması | fark 0 (şu an fark var: `.DS_Store`) |
| 2 | IIS engel eşlemesi | `^(config|include|pages)` ↔ Apache karşılığı var mı | var/eşleştirilmiş olmalı |
| 3 | Servis kurulumu | kayıt/SCM kanıtı | yoksa S7 (UNKNOWN) işaretli kalır |
| 4 | Native sürücü kodu | `*.cpp`/`*.h` glob | 0 (PLANNED) |
| 5 | URL doğrulama | HEAD 200 + envanter kesişimi | kesişim 0 |

## §16 Riskler — genişletme (komşu matris)

| # | Risk | Bağlantı | Not |
|---|---|---|---|
| R9 | IIS kuralı `ignoreCase="false"` — Linux dosya sistemi duyarsızlığına güvenip büyük harf yazımı sızabilir | §5 #2 | S1 |
| R10 | `.DS_Store` yalnız Apache'de — IIS'de servis edilebilir | §5 #5 | S1 |
| R11 | `web.config` yalnız 4 alt-alanda — yeni alt-alanda unutulursa IIS açıkta kalır | §6 #6 | S1 |
| R12 | Servis olarak çalıştırma kararı yoksa dosya yolları çalışma dizinine gömülür | §3 | S7 |
| R13 | launchd `KeepAlive` ↔ systemd `Restart=` eşlemesi yapılmazsa davranış farkı | §12 #6 | S4 |
| R14 | IOCTL METHOD_NEITHER seçimi doğrulama açığı yaratır | §11.2 #4 | S4 (PLANNED) |

## §17 Kapsam dışı — genişletme

| # | Konu | Nereye |
|---|---|---|
| 9 | WHQL/imzalama süreci | `K001-donanım` |
| 10 | IOKit ailesi detayı | `isletim-sistemi-turu` |
| 11 | Windows kayıt defteri API detayı | `isletim-sistemi-api` |
| 12 | Uygulama paketleme (MSI/AppImage) | `../derleme-ve-paketleme` |

## §18 Açık sorular — genişletme

| # | Soru | Etiket |
|---|---|---|
| Q-X15 | `.DS_Store` uzantısı `web.config`'e eklenecek mi (drift kapatma)? | UNKNOWN |
| Q-X16 | Yeni alt-alanda `web.config` zorunluluğu yazılı bir kural mı? | UNKNOWN |
| Q-X18 | Servis kimliği için hedef hesap (gizli servis hesabı?) seçildi mi? | UNKNOWN |
| Q-X19 | IOCTL yüzeyi Neva Engine kapsamına giriyor mu (disk doğrudan erişim gerekli mi)? | UNKNOWN |

## §19 Yükleme hataları — genişletilmiş karar tablosu (PLANNED)

| # | Semptom | Windows kodu | POSIX yüzeyi | Teşhis adımı |
|---|---|---|---|---|
| 1 | Kütüphane bulunamadı | 126 MOD_NOT_FOUND | `dlopen` + `dlerror` | yol/`PATH` denetimi |
| 2 | Bağımlılık zinciri eksik | 126 (aynı kod!) | "cannot open shared object file: ... needed by" | `ldd`/Dependency Walker |
| 3 | Mimar uyumsuz (x86↔x64) | 193 BAD_EXE_FORMAT | "wrong ELF class, ELFCLASS32" | `file`/PE başlığı |
| 4 | Sembol bulunamadı | 127 PROC_NOT_FOUND | `dlsym` → NULL | export listesi |
| 5 | CRT sürüm uyuşmazlığı | SxS 14007 | GLIBC_xx bulunamadı | manifest/`ldd` |
| 6 | Entry point bulunamadı | "Entry Point Not Found" | `_start` yok | bağlantı haritası |
| 7 | TLS/ctor çökmesi | DllMain hatası | `.init_array` çökmesi | debugger |
| 8 | Yetki reddi | ERROR_ACCESS_DENIED (5) | EACCES | dosya izinleri |

## §20 Servis yapılandırma dosyaları — örnek şema matrisi

| # | Öğe | systemd birimi | launchd plist | SCM (kod içi) |
|---|---|---|---|---|
| 1 | Dosya | `/etc/systemd/system/x.service` | `/Library/LaunchDaemons/x.plist` | `CreateService` parametreleri |
| 2 | Ad | `[Unit] Description=` | `Label` | `lpServiceName` |
| 3 | Komut | `ExecStart=` | `ProgramArguments` | `lpBinaryPathName` |
| 4 | Kullanıcı | `User=` | `UserName` | `lpServiceStartName` |
| 5 | Otomatik | `WantedBy=` + `enable` | `RunAtLoad` | `dwStartType` |
| 6 | Yeniden başlatma | `Restart=` | `KeepAlive` | recovery tablosu |
| 7 | Ortam | `Environment=` | `EnvironmentVariables` | `CreateService` sonrası düzenleme |
| 8 | Bağımlılık | `After=`/`Requires=` | sıralı yükleme | `lpDependencies` |
| 9 | Kısıt (hardening) | `ProtectSystem=` | sandbox plist | hesap yetkisi |
| 10 | Log | journald (`StandardOutput=`) | unified log | Event Log |

## §21 Uygulama verisi — PHP yüzeyi ile eşleme (repo bağlamı)

| # | PHP çağrısı | Karşılık | Platform soyutlaması |
|---|---|---|---|
| 1 | `sys_get_temp_dir()` | `%TEMP%`/`$TMPDIR` | tam soyutlama (kanıt §6 #1) |
| 2 | `php_uname('s')` | OS adı | gizleme yok, raporlama (anayasa §6 #2) |
| 3 | `DIRECTORY_SEPARATOR` | `\` veya `/` | tam soyutlama (kanıt §6 #1) |
| 4 | `getenv('APPDATA')` | Windows yolu | PHP-üstü ortam |
| 5 | `PHP_OS` / `PHP_OS_FAMILY` | OS ailesi | dal eşlemesi (`turu`) |
| 6 | `extension_loaded('apcu')` | uzantı | §5 #13 (anayasa riski) |
| 7 | `glob()` dosya duyarsızlığı | platforma bağlı | anayasa §1.1 #19 |
| 8 | `opcache` dosya izleme | `filemtime` döngüsü | anayasa §4 #17 |

## §22 Ölçüm protokolü — dosya-koruma drift denetimi (el ile, adım adım)

| # | Adım | Komut/yöntem | Beklenen |
|---|---|---|---|
| 1 | Apache uzantı listesini çıkar | `.htaccess:18` FilesMatch regex'i | küme A |
| 2 | IIS uzantı listesini çıkar | `web.config:25` match url regex'i | küme B |
| 3 | Farkı al | `A - B`, `B - A` | `.DS_Store` farkı (bilinen) |
| 4 | Dizin engel eşlemesi | `web.config:21` ↔ Apache karşılığı | eşleştirilmiş olmalı (şu an boş) |
| 5 | Alt-alan taraması | 4 alt-alanda çift dosya var mı | 4/4 (§6 #6) |
| 6 | Sonucu raporla | §15 #1 tablosuna yaz | fark 0 hedefi |

## §23 Referans — önceki dosyalara bağlantı

| # | Konu | Bağlantı |
|---|---|---|
| 1 | Yüzey matrisi (dosya/hata/syscall) | `platform-yuzeyleri-anayasa.md` §1-§3 |
| 2 | ABI/endianness | `platform-yuzeyleri-anayasa.md` §2 |
| 3 | Repo kanıtı özeti | `platform-yuzeyleri-anayasa.md` §6 |
| 4 | Kod örnekleri (PHP + PLANNED C++) | `platform-yuzeyleri-ornekler.md` |
| 5 | Sub-dal indeksi ve komşu sınır matrisi | `platform-yuzeyleri/index.md` |
| 6 | Paket formatları | `../derleme-ve-paketleme/index.md` |
| 7 | `#ifdef` guard kalıpları | `../tasinabilirlik-siniri/index.md` |

## §24 Dosya-koruma uzantı listeleri — tam karşılaştırma (S1, satır kanıtlı)

### §24.1 Apache kümesi (`.htaccess:18`)

| # | Uzantı | Apache | IIS (`web.config:25`) | Fark |
|---|---|---|---|---|
| 1 | `.env` | var | var | — |
| 2 | `.log` | var | var | — |
| 3 | `.json` | var | var | — |
| 4 | `.lock` | var | var | — |
| 5 | `.md` | var | var | — |
| 6 | `.sql` | var | var | — |
| 7 | `.sh` | var | var | — |
| 8 | `.bak` | var | var | — |
| 9 | `.git` | var | var | — |
| 10 | `.ini` | var | var | — |
| 11 | `.htpasswd` | var | var | — |
| 12 | `.DS_Store` | var | **yok** | Q-X15 |

### §24.2 IIS özgün kuralları

| # | Kural | Satır | Apache karşılığı | Durum |
|---|---|---|---|---|
| 1 | `^(config|include|pages)(/.*)?$` | 20-23 | ayrı kural gerekli | eşleştirilmemiş |
| 2 | `\.(css|js|png|…)$` statik | 16-19 | `mod_rewrite` zinciri | farklı mekanizma |
| 3 | SPA router | 28-29 | `RewriteRule` fallback | eşleştirilmeli |
| 4 | `mod_headers` imza gizleme | 14-15 | IIS `<serverRuntime>` (ayrı dosya) | eşleştirilmemiş |
| 5 | `RewriteEngine On` | 26-27 | IIS `<rewrite>` bloğu (`web.config:14`) | mekanizma farklı |

## §25 Neva Engine PLANNED — sürücü/servis başlangıç kontrol listesi

| # | Adım | Karar | Bağ |
|---|---|---|---|
| 1 | Hedef OS seti | Q4 UNKNOWN | anayasa §10 |
| 2 | Servis mi uygulama mı | Q5 açık | §3 |
| 3 | İkili biçim derleme hedefi | PE + ELF + Mach-O matrisi | §1 |
| 4 | Kütüphane yükleme sarmalayıcı | LoadLibrary/dlopen/dylib tek kapı | §2 |
| 5 | Kullanıcı verisi yolu | Q-X22 (§4 matrisi) | §4 |
| 6 | Dosya-koruma çifti kuralı | drift kapısı kapatılmalı | §5 · Q-X12 |
| 7 | IOCTL gerekli mi | Q-X19 | §11 |
| 8 | Servis sertleştirme | systemd `ProtectSystem=` ↔ Windows hesap | §20 #9 |

## §26 Sözlük — bu dosyaya özgü terimler

| # | Terim | Tanım |
|---|---|---|
| 1 | Yükleyici (loader) | İkili dosyayı hafızaya alıp bağımlılıkları çözen sistem bileşeni (§2) |
| 2 | Servis | Otomatik başlatılan, oturumsuz çalışan uzun ömürlü süreç (§3) |
| 3 | IOCTL | Sürücüye özel denetim kodu; `DeviceIoControl` ile gönderilir (§11) |
| 4 | METHOD_BUFFERED | IOCTL verisinin çekirdek tarafından kopyalandığı iletim yöntemi (§11.2) |
| 5 | rebase | PE image'ının ASLR ile yeniden yerleştirme bilgisi (§1 #9, §13 #4) |
| 6 | lazy bind | Sembolün ilk çağrıda çözülmesi (PLT/GOT) (§13 #5) |
| 7 | Drift | İki platform yapılandırısı arasındaki sessiz fark (§5, §15 #1) |
| 8 | LaunchAgent/LaunchDaemon | launchd'ın oturum içi/sistemi kareleri (§12 #8) |
| 9 | XDG | Linux masaüstü veri yolu standardı (`XDG_*_HOME`) (§4) |
| 10 | SxS (Side-by-Side) | Windows DLL sürümleme/manifest sistemi (§2.2 #5) |

## §27 Ölçüm kaydı (bu dosyanın doğrulaması)

| # | Ölçüm | Sonuç |
|---|---|---|
| 1 | URL HEAD 200 | 4/4 (`pe-format` · `createservicea` · `ioapiset-deviceiocontrol` · `uwp-appdata`) |
| 2 | Envanter kesişimi | 0 (2097 URL'lik pre-run envanterine göre) |
| 3 | Repo kanıtı satır okuma | `Select-Object -Index (N-1)` ile `.htaccess:18-24` · `web.config:20-27` |
| 4 | `.DS_Store` drifti | bilinen ve raporlanan (§24.1 #12) |
| 5 | Mojibake | 0 |
| 6 | Satır sayısı (raw) | ≥500 hedefi karşılandı |
| 7 | `parent:` alanı | yok (yalnız `parent_id`) |
| 8 | `group` | `mimari` (surucu eşlemesi) |
| 9 | Son satır Authority | var (her dosyada) |
| 10 | `parent:` alanı | 0 adet (tarama) |
| 11 | Dolgu/TBD/`// ...` | 0 |
| 12 | Yazılan dosyalar | yalnız whitelist (`platform-yuzeyleri/**`) |
| 13 | Git commit | atılmadı (orchestrator'a ait) |
| 14 | Hedef satır (raw) | ≥500 |

## §28 Sürüm geçmişi

| # | Sürüm | Tarih | Değişiklik |
|---|---|---|---|
| 1 | v1.0.0 | 2026-10-13 | İlk yazım — 4 URL doğrulandı (pe-format · createservicea · ioapiset-deviceiocontrol · uwp-appdata), repo kanıtları `.htaccess:18-24` + `web.config:20-27` |
| 2 | v1.0.1 | 2026-10-13 | `DeviceIoControl` URL düzeltmesi: `deviceiocontrol-nf-…` (404) → `ioapiset/nf-ioapiset-deviceiocontrol` (200) · Q-X22 eklendi · §24 uzantı karşılaştırma tablosu |
| 3 | v1.0.2 | 2026-10-13 | §27 ölçüm kaydı + §26 sözlük genişletildi · Q-X15/Q-X16/Q-X18/Q-X19/Q-X22 açık soru seti tamamlandı |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode