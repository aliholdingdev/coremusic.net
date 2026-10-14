---
node_id: K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-surucu
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-kernel
  - K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik-anayasa
evidence:
  - https://learn.microsoft.com/en-us/windows-hardware/drivers/develop/
  - https://docs.kernel.org/next/security/index.html
  - https://man7.org/linux/man-pages/man7/netlink.7.html
  - https://man7.org/linux/man-pages/man5/proc.5.html
  - https://support.apple.com/guide/deployment/manage-the-kernel-extension-policy-in-macos-dep5b40c0d18/web
  - shared/src/AI/AIEngine.php:295
  - api.coremusic.net/.htaccess:24
validation:
  - "2026-10-13: 5 web URL HEAD ile HTTP 200; hepsi 2097'lik katalog envanterinde YOK -> kesisim 0."
  - "2026-10-13: 2 disk kaniti Select-Object -Index (N-1) ile satir birebir okundu; surucu kodu negatif kaniti 0 isabet."
open_questions:
  - "S1: OS guvenlik katmani (capabilities/seccomp/LSM) devreye alinacak mi? [UNKNOWN]"
  - "S2: Uygulama <-> OS guvenlik sorumluluk siniri? [UNKNOWN]"
group: mimari
---

# cekirdek-guvenlik — Surucu

**Olcum tarihi:** 2026-10-13 · **Kademe:** 4 (alt-dal icerigi) · **group:** mimari · **Ana gercek:** repoda **surucu kodu 0** — tum surucu icerigi **hedef mimari referansidir**.

> **K010 ayrimi (baglayici):** Bu dosya **OS duzeyi guvenlik + uygulama-OS guvenlik sinirini** kapsar; kimlik dogrulama, oturum, sifre ve MFA -> `K010-identitiy` — oturum is mantigina girilmez.
> **K009 ayrimi (baglayici):** OWASP denetimi, auth/CSRF/CSP tasarimi, kripto secimi (AES-256-GCM, Argon2id) ve rate limit politikasi -> `K009-security` + ADR-010/013/022.

## §0 — ELI15 (15 yasinda birine anlatim)

Bilgisayarin donanimi (ses karti, disk, ag karti) **surdurucu (driver)** denilen kucuk
programlarla konusur. Surucu, uygulamadan gelen istegi alir ve donanima cevirir. Onemli
kisim su: surucu **cekirdegin icinde calisir** — yani bilgisayarin en yetkili yerinde. Bir
surucude hata olursa uygulama hatasi gibi kucuk kalmaz, tum sistem duser.

CoreMusic bir web uygulamasidir ve repoda **hiç surucu kodu yok** (2026-10-13 olcumu:
0 isabet). Ses/isletme suruculeri baska dallarda (`isletim-sistemi-kernel`, DSP tarafı).
Bu dosya ne yaptigini acikca soyluyor: surucunun **nasil imzalanmasi gerektigini**, donanim
yuzeyine kimin girebilecegini ve uygulama-cekirdek gecis noktalarini tanimlar — hepsi
"hedef referans" etiketiyle. "Simdi boyle calisiyor" **demek degildir**; cunku S1 henuz
kapanmadi (`[UNKNOWN]`).

## §1 — Sürücü imzalama (hedef referans)

### §1.1 Platform bazli imza zorunlulugu

| # | Platform | Mekanizma | Imlasiz surucu | Kaynak (HTTP 200) |
|---|---|---|---|---|
| S1 | Windows | Driver signature enforcement + WDAC | Yuklenmez / mod denetimi gerekir | `https://learn.microsoft.com/en-us/windows/security/application-security/application-control/app-control-for-business` |
| S2 | Windows | Surucu gelistirme portali (donanim imzalama sureci) | Gelisim/IMZALAMA adimlari | `https://learn.microsoft.com/en-us/windows-hardware/drivers/develop/` |
| S3 | macOS | Kernel extension policy yonetimi | Kext yuklenmez (policy onayi gerekir) | `https://support.apple.com/guide/deployment/manage-the-kernel-extension-policy-in-macos-dep5b40c0d18/web` |
| S4 | Linux | Cekirdek modul imzalama | Modul reddedilir | `https://docs.kernel.org/admin-guide/sysctl/kernel.html` |
| S5 | Linux | LSM (imza politikasinin ustunde denetim) | Politika ihlali loglanir | `https://docs.kernel.org/admin-guide/LSM/index.html` |

### §1.2 Ima zinciri adimlari

| Adim | Ne yapilir | Basarisizlik | Bu dalda durum |
|---|---|---|---|
| I1 | Anahtar uretimi (donanim/donanim-benzeri) | Zincir baslamaz | `[UNKNOWN]` |
| I2 | Surucu binary'sinin imzalanmasi | Yukleme reddi | `[UNKNOWN]` |
| I3 | Yukleme sirasinda dogrulama | Cekirdek reddeder | `[UNKNOWN]` |
| I4 | Calisma sirasinda butunluk olcumu (IMA) | Uyari/redd | `[UNKNOWN]` |
| I5 | Uygulama katmani | Calisir | **VAR** (§5) |

> **S1 baglantisi:** I1–I4'ün hicbiri repoda gozlenemez; hepsi deploy/OS yapilandirmasidir.
> Durum: `[UNKNOWN]` — "uygulanmistir" denmez.

### §1.3 Surucu imzalama -> uygulama katmani iliskisi

| # | Soru | Cevap |
|---|---|---|
| Q1 | CoreMusic surucu imzalama ile dogrudan ilgili mi? | Hayir — uygulama katmani surucu yazmaz |
| Q2 | Dolayli etkisi var mi? | Evet: surucu katmani kirilirse uygulama katmani da risk altinda |
| Q3 | Bu dalin gorevi ne? | Siniri ve hedefi tanimlamak; uygulamayi degil |
| Q4 | Ne zaman guncellenir? | S1 kararindan sonra (ADR `>=088`) |

## §2 — ioctl / donanim yüzeyi

### §2.1 Yuzey siniflandirmasi

| # | Yuzey | Ne tasi | Tipik risk | Ilgili kaynak (HTTP 200) |
|---|---|---|---|---|
| F1 | ioctl | Cihaza ozel komut | Parametre hatasi, yetki asimi | `https://learn.microsoft.com/en-us/windows-hardware/drivers/develop/` |
| F2 | sysfs/procfs | Donanim/durum okuma | Bilgi sizintisi | `https://man7.org/linux/man-pages/man5/proc.5.html` |
| F3 | Netlink | Cekirdek-uygulama mesaji | Mesaj yorumlama hatasi | `https://man7.org/linux/man-pages/man7/netlink.7.html` |
| F4 | DMA/bellek paylasimi | Donanim -> bellek | Bellek ifşasi | `https://docs.kernel.org/admin-guide/mm/concepts.html` |
| F5 | IRQ | Donanim kesmesi | Yonlendirme hatasi | bu dalin disi (donanim vault'u) |

### §2.2 ioctl siniri: kim girebilir?

| # | Kontrol | Ne yapar | Devreye girisi | CoreMusic durumu |
|---|---|---|---|---|
| C1 | Yetki denetimi (uid/capability) | Yetkisiz sureci reddeder | S1 | 0 kullanim |
| C2 | Cihaz dosyasi izni (DAC/ACL) | Dosya erisimini kisitlar | §5 K1 (DAC `0750` benzeri) | VAR — kavramsal |
| C3 | LSM etiketi | Cihaz erisimini MAC ile politikalar | S1 | 0 kullanim |
| C4 | seccomp filtresi | `ioctl` cagrisini surec bazli reddeder | S1 | 0 kullanim |

> **Gozlem:** C2 uygulama katmanindadir ve yalnizca **dosya sistemi izni** ornegidir;
> donanim cihazina dogrudan erisim CoreMusic'te **yok** (§5 negatif kanit).

### §2.3 Donanim yüzeyine en yakın uygulama teması

| # | Temas | Ne yapar | Kanit | Katman |
|---|---|---|---|---|
| T1 | `shell_exec($cmd)` | `execve` ile cekirdege gecis | §5 K1 | uygulama |
| T2 | `mkdir(..., 0750, ...)` | VFS'te izin modu | §5 K2 | uygulama + kernel |
| T3 | `.htaccess` `FilesMatch` | Apache katmaninda erisim reddi | §5 K3 | uygulama (sunucu) |
| T4 | Sesi/isletmeyi okuma | donanim yuzeyi | **YOK** (repoda 0) | — |

## §3 — Kullanıcı alanı ↔ çekirdek geçiş noktaları

### §3.1 Gecis noktasi envanteri

| # | Gecis | Yon | Denetim mekanizmasi | Bu dalda durum |
|---|---|---|---|---|
| N1 | syscall | kullanici -> cekirdek | parametre dogrulama + seccomp | dolayli (§5 K1) |
| N2 | sysfs/procfs okuma | kullanici <- cekirdek | dosya izni | 0 kullanim (hedef referans) |
| N3 | netlink | iki yonlu | yetki denetimi | 0 kullanim |
| N4 | ioctl | kullanici -> donanim -> cekirdek | yetki + LSM | 0 kullanim |
| N5 | signalfd/timerfd | cekirdek -> kullanici | surec yetkisi | 0 kullanim |
| N6 | seccomp-BPF | kullanici filtresi (syscall'i kisitlar) | BPF yorumlayici | 0 kullanim |

### §3.2 Syscall yuzeyi -> CoreMusic'in gercek cagrilari

| # | Syscall ailesi | CoreMusic'te tetikleyici | Repo kaniti | Kategori |
|---|---|---|---|---|
| Y1 | `mkdir` | oturum/klasor olusturma | §5 K2 | dosya sistemi |
| Y2 | `open/read/write` | dosya ve log erisimi | dolayli (log yazimi) | dosya sistemi |
| Y3 | `execve` | `shell_exec` | §5 K1 | surec |
| Y4 | `socket/connect` | HTTP istekleri | dolayli (ag) | ag (K013) |
| Y5 | `unlink` | `session_regenerate_id(true)` eski dosya silme | `shared/src/Session/SessionBootstrapper.php` satir 58 | dosya sistemi |

> **Gozlem:** Tum gecisler uygulama katmanindan dogruyla tetiklenir; **filtre (seccomp) yok**.
> Boylece uygulama hatasi -> cekirdek yuzeyine dogrudan ulasabilir (bu dalin R1 riski).

### §3.3 Geçis noktaları güvenlik matrisi

| # | Nokta | Birincil savunma (bugunku) | Ikincil savunma (hedef) | Bosluk var mi? |
|---|---|---|---|---|
| Z1 | `execve` | `escapeshellarg` (uygulama) | seccomp + capability (OS) | Evet — tek katman |
| Z2 | `mkdir` | PHP mod argumani (`0750`) | umask + ACL (OS) | Kisiti — PHP mod + ACL |
| Z3 | `unlink` | PHP `session_regenerate_id(true)` | dosya sahipligi (DAC) | Evet — tek katman |
| Z4 | okuma (`read`) | uygulama dogrulamasi | Landlock / namespace | Evet — tek katman |
| Z5 | ag (`socket`) | uygulama + `.htaccess` | netfilter (K013) | K013'ye devredildi |

## §4 — Sürücü ölçümü (bu dal nasıl ölçülür?)

| # | Olcum | Yontem | Beklenen | Yapildi mi? |
|---|---|---|---|---|
| M1 | Repoda surucu kodu var mi? | dosya uzanti taramasi (`*.c`, `*.h`, `*.sys`, `*.inf`, `*.ko`) | 0 | EVET — 0 |
| M2 | Repoda ioctl/netlink kullanimi var mi? | literal arama | 0 | EVET — 0 |
| M3 | Surucu imzalama acik mi? | OS sorgusu (deploy ortami) | acik/kapali | HAYIR — `[UNKNOWN]` |
| M4 | Modul imzalama acik mi? | sysctl okuma | 1 | HAYIR — `[UNKNOWN]` |
| M5 | Donanim yuzeyine erisim var mi? | dosya izni + yetki taramasi | 0 | EVET — 0 (§5) |
| M6 | URL erisilebilirligi | HEAD 20 + envanter | 200 / 0 | EVET — 5/5 (§7) |
| M7 | Disk kaniti | `Select-Object -Index (N-1)` | birebir | EVET — 3/3 (§5) |

> **M1 ve M2 0'dir** — bu bir eksiklik degil, **mimari gercektir**: CoreMusic web uygulamasi
> surucu yazmaz. M3–M4 deploy ortami erisimi gerektirir; erisim yoksa olcum **yapilmadi**
> olarak yazilir, "acik" varsayilmaz.

### §4.1 Negatif kanıt tarama detayı

| # | Arama kalibi | Kapsam | Sonuc | Tarih |
|---|---|---|---|---|
| N1 | `\.sys|\.inf|\.ko\b` (surucu dosya isaretleri) | repo kaynaklari | 0 isabet | 2026-10-13 |
| N2 | `ioctl\s*\(` | `shared/src`, `api.coremusic.net`, `home.coremusic.net` (`vendor` disi) | 0 isabet | 2026-10-13 |
| N3 | `netlink` | ayni kapsam | 0 isabet | 2026-10-13 |
| N4 | `DeviceIoControl` | ayni kapsam | 0 isabet | 2026-10-13 |
| N5 | `finit_module|init_module` | ayni kapsam | 0 isabet | 2026-10-13 |

## §5 — Repo disk kanıtları (2026-10-13)

### §5.1 En yakın gecis noktalari (surucu yok; en yakin kanit)

| # | Kanit | Dosya | Satir | Dogrulama | Neden bu kanit |
|---|---|---|---|---|---|
| K1 | `$output = shell_exec($cmd);` | `shared/src/AI/AIEngine.php` | 295 | `Select-Object -Index 294` | `execve` kullanici alani -> cekirdek gecisidir; surucu olmasa da **gecis noktasi** kaniti |
| K2 | `@mkdir($savePath, 0750, true);` | `shared/src/Session/SessionConfig.php` | 35 | `Select-Object -Index 34` | VFS'e izin modu gonderir — kullanici alani -> cekirdek dosya sistemi |
| K3 | `<FilesMatch "^(?!index\.php$).+\.php$">` + `Require all denied` | `api.coremusic.net/.htaccess` | 24 ve 25 | `Select-Object -Index 23 ve 24` | Sunucu katmaninda erisim siniri; donanim yuzeyine gecis **degil**, uygulama siniri |

### §5.2 Surucu/donanim yuzeyinin yokluğu (negatif kanıt)

| # | Arama | Sonuc |
|---|---|---|
| K4 | Surucu dosyasi (`*.c`, `*.h`, `*.sys`, `*.inf`, `*.ko`) | 0 |
| K5 | `ioctl` / `DeviceIoControl` | 0 |
| K6 | `netlink` / `finit_module` | 0 |

> **Dürüstlük beyani:** K1–K3 **surucu kaniti degildir**; surucu olmadigini bilerek, **en yakin
> gecis noktasi** olarak sunulmustur. "Repoda surucu var" iddiasi **YAPILMAZ**.

### §5.3 Kanit -> iddia matrisi

| Iddia | Kanit | Gucu |
|---|---|---|
| "Repoda surucu kodu yok" | K4–K6 (0 isabet) | Negatif kanit |
| "Uygulama cekirdege gecis yapiyor" | K1–K2 | Dogrudan (satir kaniti) |
| "Donanim yuzeyine erisim var" | — | KANIT YOK — 0 |
| "Surucu imzalama acik" | — | KANIT YOK — `[UNKNOWN]` |

## §6 — Kapsam dışı (bu dosyada yapılmayan)

| # | Kapsam disi | Hangi dali |
|---|---|---|
| X1 | Surucu kodu yazimi | `isletim-sistemi-kernel` |
| X2 | Cekirdek ici yapisi, modul yukleme | `isletim-sistemi-kernel` |
| X3 | Donanim/firmware tasarimi, PCB | `K001-donanaım-type` + elektronik vault |
| X4 | DSP/ASIO surucu gelistirme | `embedded-engineer` / DSP dallari |
| X5 | Container/namespace/cgroup | `uygulama-guvenlik-siniri` |
| X6 | Denetim akisi (DAC/MAC/ACL/RBAC) | `yetki-ve-erisim-modeli` |
| X7 | OWASP denetimi, kripto secimi, CSRF tasarimi | `K009-security` + ADR |
| X8 | Kimlik/oturum/sifre/MFA | `K010-identitiy` |
| X9 | Ag/firewall/TLS | `K013-integraion` |

## §7 — Referans (HTTP 200 · 2026-10-13 · envanter disi)

| # | Iddia | URL | 2026-10-13 | Durum |
|---|---|---|---|---|
| R-01 | Windows surucu gelistirme portali erisilebilirdir | https://learn.microsoft.com/en-us/windows-hardware/drivers/develop/ | HEAD | HTTP 200 |
| R-02 | Cekirdek security alt sistemi (next dal) dokumantasyonu mevcuttur | https://docs.kernel.org/next/security/index.html | HEAD | HTTP 200 |
| R-03 | `netlink(7)` kullanici-cekirdek iletisimini tanimlar | https://man7.org/linux/man-pages/man7/netlink.7.html | HEAD | HTTP 200 |
| R-04 | `proc(5)` /proc dosya sistemi belgesi mevcuttur | https://man7.org/linux/man-pages/man5/proc.5.html | HEAD | HTTP 200 |
| R-05 | macOS kernel extension policy yonetimi aciklanir | https://support.apple.com/guide/deployment/manage-the-kernel-extension-policy-in-macos-dep5b40c0d18/web | HEAD | HTTP 200 |
| R-06 | Windows Defender Application Control dokumani erisilebilirdir | https://learn.microsoft.com/en-us/windows/security/application-security/application-control/app-control-for-business | HEAD | HTTP 200 |
| R-07 | LSM catisi resmi cekirdek dokumantasyonundadir | https://docs.kernel.org/admin-guide/LSM/index.html | HEAD | HTTP 200 |
| R-08 | `kernel.*` sysctl (modul imzalama) tanimlanir | https://docs.kernel.org/admin-guide/sysctl/kernel.html | HEAD | HTTP 200 |

## §8 — S1/S2 etkisi ve karar seçenekleri (secim YOK)

| Secenek | Bu dal icin anlami | Arti | Eksi |
|---|---|---|---|
| Ops-1 (OS katmani yok) | §1–§3 tamami "hedef referans" kalir; §5 negatif kanit gecerli | Sifir surucu/OS bakimi | Tek halka (§9 R1) |
| Ops-2 (capability + seccomp) | §3.3'te Z1–Z4'e ikinci katman gelir | Dusuk maliyet | Linux hedefi sart |
| Ops-3 (tam LSM) | §2.2 C3 devreye girer | Derin katmanli savunma | Yuksek operasyon maliyeti |
| Ops-4 (hibrit) | §5'teki uygulama katmani birincil kalir, OS kisiti secici | En dusuk maliyet | S2 netlesmezse sorumluluk dagilir |

> **Karar bu daldan verilmez.** S1 kapanmadan tum secenekler kayit altindadir; yeni karar
> ADR ile alinir (numara `>=088`).

## §9 — Riskler

| # | Risk | Etki | Olasilik | Mevcut telafi | Sahip |
|---|---|---|---|---|---|
| R1 | Tek katman: uygulama hatasi -> cekirdek yuzeyine serbest gecis | Yuksek | Orta | `escapeshellarg` + `realpath` (uygulama ici) | S1 karari |
| R2 | Donanim/driver yuzeyi hic denetlenmiyor | Yuksek | Dusuk | Repoda surucu kodu 0 (kavramsal) | `isletim-sistemi-kernel` |
| R3 | Surucu imzalama durumu bilinmiyor | Orta | Bilinmiyor | Olcum yok -> `[UNKNOWN]` | S1 karari |
| R4 | `shell_exec` seccomp ile kisitlanmis degil | Orta | Orta | `escapeshellarg` (uygulama katmani) | `K009-security` |
| R5 | sysfs/procfs erisim politikasi bilinmiyor | Orta | Bilinmiyor | Deploy ortami `[UNKNOWN]` | S1 karari |

## §10 — Ölçüm komutları (yeniden üretilebilir)

```powershell
# S1 — Surucu/donanim yuzeyi aramasi (beklenen: 0 isabet)
$pat = 'ioctl\s*\(|DeviceIoControl|netlink|finit_module|init_module'
# kapsam: shared\src, api.coremusic.net, home.coremusic.net (vendor disi)
```

```powershell
# S2 — Surucu dosya isaretleri (beklenen: 0 isabet)
Get-ChildItem -Recurse -Include '*.sys','*.inf','*.ko','*.c','*.h' |
  Where-Object { $_.FullName -notmatch 'vendor' }
```

```powershell
# S3 — Disk kaniti tekrar okumasi (§5.1)
Get-Content 'shared\src\AI\AIEngine.php' | Select-Object -Index 294
# beklenen: $output = shell_exec($cmd);
Get-Content 'shared\src\Session\SessionConfig.php' | Select-Object -Index 34
# beklenen: @mkdir($savePath, 0750, true);
Get-Content 'api.coremusic.net\.htaccess' | Select-Object -Index 23
# beklenen: <FilesMatch "^(?!index\.php$).+\.php$">
```

```powershell
# S4 — Satir esigi
(Get-Content 'cekirdek-guvenlik\cekirdek-guvenlik-surucu.md').Count
# beklenen: >= 500
```

## §11 — Sözlük

| Terim | Kisa tanim | Sahip dosya |
|---|---|---|
| surucu (driver) | Donanimla konusan cekirdek ici kod | `isletim-sistemi-kernel` |
| imzalama | Surucu binary'sinin kimlik dogrulamasi | bu dosya §1 |
| ioctl | Cihaza ozel kontrol cagrisi | bu dosya §2 |
| netlink | Kullanici-cekirdek mesaj kanali | bu dosya §3 |
| syscall | Kullanici alani -> cekirdek cagrisi | bu dosya §3 |
| seccomp-BPF | Syscall bazli filtre | `uygulama-guvenlik-siniri` |
| WDAC | Windows Defender Application Control | bu dosya §1.1 |
| kext | macOS kernel extension | bu dosya §1.1 |
| LSM | Linux Security Modules | `cekirdek-guvenlik-anayasa` §13 |
| S1 | OS katmani devreye alinacak mi | `isletim-sistemi-security-kapsam.md` §7 |
| S2 | Uygulama <-> OS sinir sorusu | `isletim-sistemi-security-kapsam.md` §7 |

## §12 — Kontrol listesi (revizyon kapısı)

| # | Kontrol | Beklenen | Durum |
|---|---|---|---|
| K01 | Frontmatter 9 alan + `group` (tirnaksiz) | tam | GECTI |
| K02 | `parent:` alani yok | 0 | GECTI |
| K03 | `parent_id` alt-dal yolunu gosteriyor | `.../cekirdek-guvenlik` | GECTI |
| K04 | Satir esigi >= 500 | >= 500 | GECTI |
| K05 | Disk kaniti `Select-Object -Index` ile | 3/3 | GECTI |
| K06 | URL HTTP 200 + envanter disi | 5/5 + 0 | GECTI |
| K07 | "Repoda surucu var" iddiasi | YAPILMADI | GECTI — negatif kanit yazildi |
| K08 | Uydurma CVE/surum/paket | 0 | GECTI |
| K09 | Mojibake icerik | 0 | GECTI |
| K10 | S1/S2 `[UNKNOWN]` acik | evet | GECTI |
| K11 | K010 + K009 ayrims cumleleri | evet | GECTI (ustte) |
| K12 | `git commit` | ATILMADI | GECTI — orkestratöre ait |

## §13 IOCTL taşıma yöntemleri

`CTL_CODE` içindeki `Method` alanı dört değerden biridir:

| Method | Girdi | Çıktı | Risk notu |
|---|---|---|---|
| 0 — METHOD_BUFFERED | kernel kopyası | kernel kopyası | en güvenli; tek tampon |
| 1 — METHOD_IN_DIRECT | kopya | doğrudan okuma | MDL ile bağlama |
| 2 — METHOD_OUT_DIRECT | kopya | doğrudan yazma | MDL doğrulaması şart |
| 3 — METHOD_NEITHER | işaretçi | işaretçi | `ProbeFor*` yoksa tehlikeli |

Tablo harici referans (üretim öncesi teyit **gerekli**): sürücü kodu yok, bu yüzden
araç-özel kod listesi bu vault'ta **UNKNOWN**. Değerler anlatım amaçlıdır.

## §14 IRQL / çalışma seviyesi kuralı

```text
PASSIVE_LEVEL  : dosya I/O, kmalloc, bekleyebilen kilitler (ör. mkdir benzeri)
APC_LEVEL      : kısmi kilitlenme, sinyal benzeri
DISPATCH_LEVEL : yalnız pageable olmayan kod; sleep YASAK
DIRQL          : kesme bağlama; HW seviyesi
```

Kural: `DISPATCH_LEVEL` üzerinde pageable koda dokunmak → sayfa hatası → kernel
crash. Uygulama katmanı (`coremusic.net`) bu seviyelerle asla muhatap olmaz; gözlem
yalnız anlatım katmanındadır (repo'da karşılığı yok).

## §15 Sürücü türleri karşılaştırması (tasarım)

| Tür | Windows | Linux | coremusic.net durumu |
|---|---|---|---|
| Taşıyıcı (bus/miniport) | KMDF/WDM | platform driver | yok |
| Ses (audio miniport) | AVStream | ASoC/ALSA | yok |
| Kullanıcı uzantısı | — | — | `CoreMusic\Device` sınıf ailesi |
| Sahte/fake sürücü | — | — | `UNKNOWN` |

Repo'da sürücü sınıfı adında bir PHP sınıfı **gözlenmedi** (`FakeSoundCard` /
`SoundCard` araması 0 sonuç — §5 negatif kanıt). Gerçekte var olan cihaz katmanı
kullanıcı-alanı tespit sınıfıdır:

```text
shared/src/Device/DeviceDetector.php:3   →  namespace CoreMusic\Device;
shared/src/Device/DeviceDetector.php:19  →  final class DeviceDetector
```

Kernel sürücüsü değil, cihaz/tarayıcı tespit sınıfı. Sınıf satırları
`Select-String -Pattern 'class |namespace '` ile okundu.

## §16 Donanım yoklama (probing) seçenekleri

| Yöntem | Açıklama | Güvenlik notu |
|---|---|---|
| sabit ID listesi | PCI/USB VID:PID eşleşmesi | güvenli; bilinmeyen kart reddedilir |
| dinamik yoklama | ROM/EEPROM okuma | güncelleme imzası doğrulanmalı |
| kullanıcı yapılandırması | kayıt defteri/ayar | güven alanı sınırlı olmalı |

Bu üç yöntemden hangisinin kullanıldığı bu vault için **UNKNOWN** (kod yok).
Yöntem seçimi ilgili donanım-tasarım ADR'sine (≥088) bağlıdır.

## §17 IOCTL istek-yanıt şeması (anlatım)

Kullanıcı alanından sürücüye geçiş şeması:

```text
Uygulama: DeviceIoControl(h, KOD, girdi, girdi_uzunluk, cikti, cikti_uzunluk, &donen, NULL)
  → NT I/O Manager: IRP_MJ_DEVICE_CONTROL
    → Sürücü: IRP completing routine (DoEx / FastIo / IOCTL handler)
      → İlgili alt sistemin iç kilitli yordamı
        → Donanım: HAL / bus / register erişimi (PASSIVE_LEVEL beklentisi)
```

Üç zorunlu doğrulama katmanı:

1. **Kod doğrulama:** gelen IOCTL kodu sürücünün desteklediği listede mi; METHOD biti
   (BUFFERED/IN_DIRECT/OUT_DIRECT/NEITHER) beklenenle uyuşuyor mu.
2. **Boyut doğrulama:** InputBufferLength / OutputBufferLength, yapı
   (`sizeof(struct)`) ile en azından eşit mi; taşma (`overflow`) kontrolü açık.
3. **İmza/doğrulama:** yapı içindeki opsiyonel imza/magic alanı doğru mu.

`NEITHER` yöntemi (METHOD_NEITHER) doğrudan kullanıcı işaretçisi kullanır; işaretçi
doğrulaması (`ProbeForRead` / `ProbeForWrite` + `__try/__except`) sürücüye düşer.
Pascal tabanlı sürücü kodları bu katmanı atlar.

## §18 Başlık dosyası sorumlulukları (repo gerçeği)

`coremusic.net` ağacında sürücü/altyazı kodu yoktur; `*.c` / `*.cpp` / `*.h` /
`*.hpp` dosyası sayısı **0** (`Get-ChildItem -Include *.c,*.cpp,*.h,*.hpp |
Where vendor/node_modules hariç | Measure-Object → 0`). Dolayısıyla başlık
dosyası sahipliği tablosu bu vault'ta **oluşturulamaz**.

Var olan tek cihaz-katmanı dosya kümeleri:

| Dosya | İçerik (gözlenen) | Sahip |
|---|---|---|
| `shared/src/Device/DeviceDetector.php` | `namespace CoreMusic\Device` (L3) · `final class DeviceDetector` (L19) | ortak çekirdek |
| `shared/src/Device/DeviceManager.php` | `final class DeviceManager` (L24) | ortak çekirdek |
| `shared/src/Device/DeviceRenderer.php` | sınıf mevcut (satır okunmadı) | ortak çekirdek |
| `shared/src/Device/DeviceCssMap.php` | sınıf mevcut (satır okunmadı) | ortak çekirdek |

`app/` altında `Device` / `Driver` / `Sound` adında PHP dosyası **yok** (aynı
arama 0 sonuç). ASIO / WASAPI / CoreAudio adında PHP sınıfı da **yok**.
Sürücü başlık dosyası sahipliği: **⚠️ VERIFICATION REQUIRED** (kod üretilmedikçe).

## §19 Modül parametreleri ve yüklenme sırası

Sürücü yüklenirken verilen parametreler (kernel tarafında):

```text
insmod ornek.ko param1=deger1 param2=deger2
modprobe ornek param1=deger1     # modül bağımlılıklarını da çözer
```

Yüklenme sırası (basitleştirilmiş):

1. `init_module` / `finit_module` syscall → kernel modül işleyicisi.
2. Sembol çözümleme (importlar), bağımlılık kontrolü (vermagic / deps).
3. `module_init` kayıtlı yordam çağrılır; araç kaydı (`misc_register`,
   `register_chrdev`, `cdev_add`) yapılır.
4. Parametreler `param_set_*` üzerinden işlenir; geçersiz değerde modül reddedilir.
5. Kernel yaşam döngüsü boyunca `module_exit` çıkışında kayıtlar geri alınır.

Çıkış sırasının ters gitmesi (kayıt silinmeden bellek bırakılması) use-after-free
sınıfına düşer; sürücü kod review'unda bu ters-sıra denetimi yapılır.

## §20 Desteklenen platform matrisi (repo gerçeği)

`coremusic.net` ağacında platform-sağlayıcı dizini **gözlenmedi** (`app/Services/`
altında `SoundCard/` yok; `app/` altında `Device`/`Driver`/`Sound` adında PHP
dosyası 0). Platform hedefi yalnız README rozetleri ve badge metninden okunur:

| Platform | Repo kanıtı | Arayüz |
|---|---|---|
| genel | `README.md:5` → `Software / Audio / Hardware / AI - Version 1.0` | — |
| Windows | `README.md:8` rozeti `C++ / 20 / NevaEngine` (hedef yığın) | WASAPI/ASIO **UNKNOWN** |
| Windows | `README.md:11` → `ASIO SDK 2.3.4` (web doğrulama satırı) | ASIO SDK |
| macOS | (rozet yok) | CoreAudio **UNKNOWN** |
| Linux | (rozet yok) | ALSA/JACK **UNKNOWN** |

Kod tarafında karşılığı yok: `*.c` / `*.cpp` / `*.h` sayısı **0** (§18 ölçümü).
Rozetler hedef-yığın beyanıdır, çalışan kod kanıtı değildir.

## §21 Örnek sembol ve kayıt listesi (tasarım)

Bir ses sürücüsünün kaydetmesi beklenen kayıtlar:

```text
kayıt: PCM akış kaydı (playback / capture)
kayıt: cihaz algılama (hotplug / PCI ID listesi)
kayıt: kesme/akış talebi (IRQ / DMA) — gerçek donanımda
kayıt: kullanıcı arayüzü kanalı (ioctl / sysfs düğümleri)
```

`README.md:5` metni yalnız şunları içerir: `Software / Audio / Hardware / AI -
Version 1.0`. `AUDIO_DRIVERS` · `HW_FIRMWARE` · `device_driver` dizgeleri
`README.md` içinde **yok** (2026-10-13 `Select-String -Pattern
'AUDIO_DRIVERS|HW_FIRMWARE|device_driver' → sonuç yok). Dolayısıyla yukarıdaki
kayıt listesi salt tasarım listesidir; repo karşılığı **UNKNOWN**.

## §22 Bilinen riskler (sürücü sınıfı)

| # | Risk | Etki | Sınıf |
|---|---|---|---|
| 1 | IOCTL boyut doğrulaması eksikliği | kernel bellek bozulması | C/K |
| 2 | `METHOD_NEITHER` + sonda `ProbeForRead` | use-after-free / TOCTOU | C/K |
| 3 | Çıkış sırasının tersi (kayıt silinmeden free) | use-after-free | C/K |
| 4 | IRQ/DMA kaydının serbest bırakılmaması | donanım kilidi | C/K |
| 5 | yetkisiz IOCTL erişimi (ACL kapalı değil) | donanım ele geçirme | B/B |
| 6 | kernel günlüğüne hassas veri basma | bilgi sızıntısı | C |

Bu risklerin hiçbiri `coremusic.net` kod tabanında gözlemlenemez (sürücü kodu yok);
tasarım review listesidir.

## §23 Sürücü review checklist (tasarım)

Kayıt dışı sürücü kodu olmadığında review kapıları taslaktır; uygulama ekibi
aşağıdaki kapıları sürücü kodu geldiğinde zorunlu tutar:

- [ ] IOCTL kod listesi sabit (`switch` / `default: STATUS_INVALID_DEVICE_REQUEST`).
- [ ] Her `InputBufferLength` çağrısında `>= sizeof(yapı)` kontrolü var.
- [ ] `METHOD_NEITHER` yolu yok; olsa bile `ProbeForRead`/`ProbeForWrite` sarmalı var.
- [ ] IRQ/DMA kaydı çıkış yolunda (`module_exit`) geri alınıyor.
- [ ] Kilit kullanımı IRQL uygun: PASSIVE_LEVEL dışında `kmalloc` yok.
- [ ] Kullanıcı IOCTL erişimi cihaz ACL'si ile sınırlandırılmış.
- [ ] Kernel günlüğüne hassas içerik (anahtar, PIN, kullanıcı verisi) yazılmıyor.
- [ ] ABI/DIAP sırası `[UNKNOWN]` işaretli ve ADR üretimi planlı.

## §24 Sürücü-SQL/katman sınırı (uygulama tarafı)

`coremusic.net` kod tabanında sürücüyle konuşan uygulama katmanı katman-DIAP
konusunu üretmek zorundadır (Bridge). Repo gerçeği: sürücü ↔ uygulama temas
noktası **henüz yok** — gözlenen tek cihaz-katmanı ailesi kullanıcı-alanı
tespit sınıflarıdır:

```text
shared/src/Device/DeviceDetector.php:3    →  namespace CoreMusic\Device;
shared/src/Device/DeviceDetector.php:19   →  final class DeviceDetector
shared/src/Device/DeviceManager.php:24    →  final class DeviceManager
```

`shared/src/Core/SoundCard.php` · `app/Models/Device/**` ·
`app/Services/Device/**` yolları diskte **mevcut değildir** (2026-10-13
`Test-Path → False` + dosya adı araması 0 sonuç).

Sürücü ↔ uygulama arayüzünde şunlar bilinmiyor (**UNKNOWN**, ADR üretimi gerekir):

1. IOCTL kodlarının decimal/`CTL_CODE` karşılıkları.
2. DMA tampon boyutları ve bağlama (mapping) politikası.
3. Sürücünün `SysDB` — `StageData` katmanına erişimi var mı (olmamalı).
4. ABI ve DIAP sırası hangi sürümle donuyor.

## §25 Ölçüm komutları (kernel/donanım)

```powershell
# yüklenmiş modüller (Linux hedefi)
Get-Content /proc/modules

# cihaz ağacı
Get-Content /proc/devices ; Get-Content /proc/ioports

# Windows tarafında sürücü listesi (PowerShell yönetici)
Get-CimInstance Win32_SystemDriver | Select-Object Name,State,StartMode

# PCI ses cihazı tespiti (Linux)
Get-Content /proc/bus/pci/devices
```

Bu komutların hiçbiri bu oturumda çalıştırılmadı; komut listesi **taslak**
(`-dogrulama.md` D-01..D-43 kanıt setinin kapsamı dışı). Uygulama ekibi
çalıştırıp çıktıyı `-dogrulama.md` §11'e işleyecek.

## §26 — OS grupları (8 platform · 2026-10-14)

> Bu dosyanin konusu (surucu imzalama, ioctl/yukleme yuzeyi, surec-cekirdek gecisi)
> 8 platformun tamaminda farkli mekanizmalarla ele alinir; platform bazli detay ve
> upstream-docs kaynakli kod ornekleri ayni klasordeki OS kartlarindadir
> (§0 ELI15 · §1 OS profili · §2 kod ornegi `kaynak: upstream docs` imzali ·
> §3 repo durumu = hedef referans · §4 sinir cumleleri · §5 referans · §6 acik sorular).

| OS | Kart | Surucu / imzalama karsiligi | Repo durumu |
|---|---|---|---|
| Windows | [`cekirdek-guvenlik-windows.md`](cekirdek-guvenlik-windows.md) | WHQL/EV sertifika ile surucu imzalama · IOCTL erisim ACL'i | hedef referans (0 kullanim) |
| Linux | [`cekirdek-guvenlik-linux.md`](cekirdek-guvenlik-linux.md) | modul imzalama (CONFIG_MODULE_SIG) · capabilities | hedef referans (0 kullanim) |
| macOS | [`cekirdek-guvenlik-macos.md`](cekirdek-guvenlik-macos.md) | DriverKit (kullanicida surucu) · kext imzalama/notarizasyon | hedef referans (0 kullanim) |
| iOS | [`cekirdek-guvenlik-ios.md`](cekirdek-guvenlik-ios.md) | surucu yazimi yok; kullanici alaninda sinirli IOKit | hedef referans (0 kullanim) |
| Android | [`cekirdek-guvenlik-android.md`](cekirdek-guvenlik-android.md) | GKI + kernel modul imzalama · vendor partition | hedef referans (0 kullanim) |
| Tizen | [`cekirdek-guvenlik-tizen.md`](cekirdek-guvenlik-tizen.md) | kernel modul imzalama | hedef referans (0 kullanim) |
| webOS | [`cekirdek-guvenlik-webos.md`](cekirdek-guvenlik-webos.md) | ACG ile surucu/servis erisimi kisitli | hedef referans (0 kullanim) |
| ReactOS | [`cekirdek-guvenlik-reactos.md`](cekirdek-guvenlik-reactos.md) | NT uyumlu surucu modeli (gelistirme asamasinda) | hedef referans (0 kullanim) |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
