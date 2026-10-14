---
node_id: K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-anayasa
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-kernel
evidence:
  - https://docs.kernel.org/security/self-protection.html
  - https://www.kernel.org/doc/html/latest/security/self-protection.html
  - https://docs.kernel.org/arch/x86/cpuinfo.html
  - https://learn.microsoft.com/en-us/windows/security/hardware-security/tpm/trusted-platform-module-overview
  - https://support.apple.com/guide/security/secure-boot-sec4d968d614/web
  - https://docs.kernel.org/admin-guide/hw-vuln/index.html
  - shared/src/Session/SessionConfig.php:35
  - shared/src/AI/AIEngine.php:292
validation:
  - "2026-10-13: 6 web URL HEAD ile HTTP 200; hepsi 2097'lik katalog envanterinde YOK -> kesisim 0."
  - "2026-10-13: 2 disk kaniti Select-Object -Index (N-1) ile satir birebir okundu; OS araci negatif kaniti 0 isabet."
open_questions:
  - "S1: OS guvenlik katmani (capabilities/seccomp/LSM) devreye alinacak mi? [UNKNOWN]"
  - "S2: Uygulama <-> OS guvenlik sorumluluk siniri? [UNKNOWN]"
group: mimari
---

# cekirdek-guvenlik — Anayasa

**Olcum tarihi:** 2026-10-13 · **Kademe:** 4 (alt-dal icerigi) · **group:** mimari · **Ilk soru:** S1 — `[UNKNOWN]`.

## §0 — ELI15 (15 yasinda birine anlatim)

Bir bilgisayarda iki kapi gorevlisi vardir. **Birincisi isletim sistemi cekirdegi:** bilgisayarin
bütün donanimina o bakar; her program bir sey istediginde (dosya ac, bellek ayir, ag gonder) once
ona sorar. **Ikincisi uygulamanin kendisi:** CoreMusic'in PHP kodu gibi. Bu dalin sorusu su:
**kapi gorevlisine kac kilit taktik?**

Bugunun olcumu (2026-10-13): repoda `seccomp`, `capability`, `landlock`, `selinux`, `setuid`
gibi kilitlerden **0 tane var**. Bunlar demek degil ki "guvenlik yok" — uygulama kapisi kendi
kilitleriyle calisiyor: `realpath` (yanlis yolu reddet), `escapeshellarg` (tehlikeli yaziyi
noktali virgulle ayir), `mkdir 0750` (herkes okuyamasin). Ama bu kilitlerin hepsi **uygulama
kapisinda**; eger biri kirilirsa arkada ikinci bir kilit yok.

Bu dal o ikinci kilidin **ne oldugunu** tanimlar: guvenli onyukleme (bilgisayar acilirken
kodun kimligini dogrula), bellek korumalari (SMEP/SMAP/KASLR — saldirganin kodunu
calistirmayi zorlastir), sanallaştırma (ayri bir kapi gorevlisi), surucu/donanim yuzeyi.
Hepsini "hedef mimari referansi" olarak etiketler, **mevcut durum olarak sunmaz** — cunku S1
(`[UNKNOWN]`) kapanmadi.

## §1 — Koruma halkalari (halka disindan ice)

| # | Halka | Ne korur | Kim guvende | Bu dalin tutumu |
|---|---|---|---|---|
| H1 | Donanim (ROM, anahtar depolama) | Fiziksel mudahale | Tum sistem | Kapsam disi — `K001-donanaım-type` + elektronik vault |
| H2 | Firmware / UEFI | Onyukleme kodu butunlugu | Boot zinciri | Hedef referans (§2) |
| H3 | Bootloader | Cekirdegin kimligi | Boot zinciri | Hedef referans (§2) |
| H4 | Cekirdek | Tum surecler ve bellek | Tum kullanici uzayi | Hedef referans (§3 · §5) |
| H5 | Sanallaştırma (hypervisor) | Cekirdekler arasi izolasyon | Misafir sistemler | Hedef referans (§4) |
| H6 | Kullanici uzayi kutuphanesi | Kendi verisi | Kendi processi | **Mevcut durum** — repo pratiigi |
| H7 | Uygulama (CoreMusic) | Istek/yanit dogrulamasi | Web kullanicisi | **Mevcut durum** — kanitli (§7) |

> **Halka ilkesi:** Dis halka kirilirsa ic halka ayakta kalmalidir. CoreMusic'te su an **tek
> halka** calisiyor (H7); H4 kilitleri repo'da 0 (§7 negatif kanit).

### §1.1 Halka-bazli saldirgi yetenekleri

| Halka | Saldirgan ne kazanir | Savunma adi | Repo durumu |
|---|---|---|---|
| H7 | Yanlis yetki, veri okuma | RBAC + yol dogrulama | VAR — kanitli (§7) |
| H6 | Kullanicinin dosyalarini okuma | surec izolasyonu | Yok (uygulama disi) |
| H5 | Tum misafirleri izleme | VM izolasyonu | Yok (repo'da VM kodu yok) |
| H4 | Tum bellek, tum donanim | SMEP/SMAP/KASLR | Yok (OS yapilandirmasi [UNKNOWN]) |
| H2/H3 | Kalici implant | Secure Boot + modul imzalama | Yok (deploy ortami [UNKNOWN]) |

## §2 — Guvenli onyukleme (Secure Boot · TPM · modul imzalama)

### §2.1 Zincirin adimlari (hedef referans)

| # | Adim | Ne dogrulanir | Basarisizlik | Ilgili kaynak (HTTP 200) |
|---|---|---|---|---|
| B1 | UEFI baslatma | firmware butunlugu (TPM olcusu) | Sistem baslamaz | `https://learn.microsoft.com/en-us/windows/security/hardware-security/tpm/trusted-platform-module-overview` |
| B2 | Bootloader | imza zinciri | Boot durur | `https://support.apple.com/guide/security/secure-boot-sec4d968d614/web` |
| B3 | Cekirdek imzasi | modul imzalama politikasi | Modul yuklenmez | `https://docs.kernel.org/admin-guide/sysctl/kernel.html` |
| B4 | Initrd/on-kok | diski root'a takma | Boot hatasi | `https://docs.kernel.org/admin-guide/initrd.html` |
| B5 | Ilk surec | calistirilan binary kimligi | Calistirilmaz | `https://docs.kernel.org/security/credentials.html` |

### §2.2 On-kosul baglantisi (bu dal icin ne anlama gelir)

| # | On-kosul | Neden gerekli | Degilse |
|---|---|---|---|
| P1 | Boot zinciri guvenilir | Cekirdek kendinden emin baslar | Cekirdek saldirganin kontrolunde baslayabilir |
| P2 | Modul imzalama acik | Yanlis kod cekirdege girmez | Yuklenen her modul cekirdek ayni yetkide |
| P3 | Bellek korumalari aktif | Cekirdek-ici exploit zorlasir | §3 mitigasyonlari devre disi |
| P4 | Surec izolasyonu | Uygulama hatasi dalgayi tutar | H7 kirilinca tum sistem duser (bu dalin RK1 riski) |

> **S1 baglantisi:** B1–B5'in hicbiri repoda **test edilemez** — bunlar deploy ortami
> yapilandirmasidir. Durum: `[UNKNOWN]` (S1 kapanmadikca "uygulanmis" denmez).

### §2.3 Windows / macOS / Linux karsilastirmasi (salt kavramsal)

| Platform | Onyukleme guvenligi adi | Kaynak (HTTP 200) | Bu dalin notu |
|---|---|---|---|
| Windows | Secure Boot + TPM + WDAC | `https://learn.microsoft.com/en-us/windows/security/application-security/application-control/app-control-for-business` | Hedef referans |
| macOS | Secure Boot + volume owner | `https://support.apple.com/guide/security/secure-boot-sec4d968d614/web` | Hedef referans |
| Linux | modul imzalama + IMA | `https://docs.kernel.org/security/IMA-templates.html` | Hedef referans |

> Platform secimi bu daldan **yapilmaz**; `isletim-sistemi-turu` dali fark matrisini tasir.

## §3 — Bellek korumalari: SMEP · SMAP · KASLR · PAN

### §3.1 Her mitigasyonun neyi kapatir

| # | Mitigasyon | Kapatigi teknik | Etkisi | Ilgili kaynak (HTTP 200) |
|---|---|---|---|---|
| M1 | SMEP | Cekirdek modunda kullanici kodunu calistirma | Saldirgan "kullanici kodunu cevirir" hilesi biter | `https://docs.kernel.org/arch/x86/cpuinfo.html` |
| M2 | SMAP | Cekirdek modunda kullanici verisine erisim | Veri-sokma saldirisi zorlasir | `https://docs.kernel.org/arch/x86/cpuinfo.html` |
| M3 | KASLR | Cekirdek adresini tahmin etme | ROP gadget adresleri her acilista degisir | `https://docs.kernel.org/admin-guide/mm/concepts.html` |
| M4 | PAN | Arm'da kullanici bellekine erisim (SMAP esdegeri) | Ayni teknik Arm'da kapanir | `https://docs.kernel.org/admin-guide/mm/pagemap.html` |
| M5 | KPTI | Sayfa tablosuyla kullanici/cekirdek ayrimi | Meltdown sinifi okumalar engellenir | `https://docs.kernel.org/admin-guide/hw-vuln/index.html` |
| M6 | KFENCE | Rastgele bellek hatasi dedektoru | Aktif-ofset (UAF/OOB) yakalama artar | `https://docs.kernel.org/dev-tools/kfence.html` |

### §3.2 Mitigasyon -> gozlemlenebilirlik baglantisi

| Mitigasyon | Nereden gozlenir | Ne ile | Bu dalda yapilan |
|---|---|---|---|
| SMEP/SMAP | CPU bayraklari (`/proc/cpuinfo` benzeri okuma) | bayrak adi | Yalniz kaynak gosterildi — okuma yapilmadi |
| KASLR | cekirdek adres baslangici | sysctl | `https://docs.kernel.org/admin-guide/sysctl/kernel.html` gosterildi |
| KFENCE | dev-tools yapilandirmasi | boot parametresi | Durum `[UNKNOWN]` |
| hw-vuln | donanim aciklari indeksi | sysfs okuma | `https://docs.kernel.org/admin-guide/hw-vuln/index.html` gosterildi |

> **Dürüstlük notu:** Bu bolumde **hiçbir mitigasyonun aktif oldugu iddia edilmiyor**; yalnizca
> "boyle bir mitigasyon vardir ve sunu kapatir" duzeyinde tanim yapilmistir. Aktiflik olcusu
> deploy ortaminda yapilir ve S1'e baglidir.

### §3.3 Yorumlayici ile mitigasyon iliskisi (CoreMusic baglami)

| # | Gozlem | Sonuc |
|---|---|---|
| Y1 | CoreMusic PHP uzerinde calisir (kullanici uzayi) | Mitigasyonlar (M1–M6) uygulamayi **dolayli** korur |
| Y2 | Uygulama hatasi genelde bellek-usicu degil, mantik hatasidir | Mitigasyonlar traversal/CSRF/RBAC hatalarini **kapatmaz** |
| Y3 | Boylece en buyuk kazanim yine uygulama katmanindadir | §7'deki pratik onceliklidir |
| Y4 | Bellek-usicu bir bagimlilik (or. bir PHP uzantisi) devreye girerse | M1–M6 tekrar anlamlilasir — S1'in gerekcelerinden biri |

## §4 — Sanallaştırma (hypervisor halkasi)

| # | Kavram | Ne saglar | Bu dalin tutumu |
|---|---|---|---|
| V1 | VM izolasyonu | Misafir sistemin cekirdegi birbirini goremez | Hedef referans |
| V2 | VM-exit maliyeti | Surec gecislerinde performans dususu | Olcum yapilmadi — `isletim-sistemi-testing` |
| V3 | vTPM / secure boot sanallastrmasi | Misafir boot zinciri | `https://support.apple.com/guide/security/welcome/web` (HTTP 200) |
| V4 | Container vs VM | Container ayri cekirdek paylasir (daha ince izolasyon) | `uygulama-guvenlik-siniri` dali tasir |
| V5 | Tek-host mimari | CoreMusic tek makinede (bilinen deploy) | Sanallastra avantaji [UNKNOWN] |

> **Sinir:** Sanallastra **kurulumu/operasyonu** bu daldan yapilmaz; yalniz guvenlik halkasi
> olarak tanimlanir. Kurulum devops dalina, container ayrintisi `uygulama-guvenlik-siniri`'na
> devredilir.

## §5 — Kernel exploit yuzeyi (siniflandirma)

### §5.1 Giris noktalari

| # | Yuzey | Ne uzerinden | Saldirgi ornegi (kavramsal) | Ilgili kaynak (HTTP 200) |
|---|---|---|---|---|
| G1 | syscall | program -> cekirdek cagrisi | yanlis cagri dogrulamasi | `https://man7.org/linux/man-pages/man5/proc.5.html` |
| G2 | ioctl | cihaz/kontrol cagrilari | surucu girdisi | `https://learn.microsoft.com/en-us/windows-hardware/drivers/develop/` |
| G3 | Ag paketi | kernel network yolu | paket isleme hatasi | `https://docs.kernel.org/networking/filter.html` |
| G4 | Dosya sistemi | mount/parse | ozel dosya bicimi | `https://docs.kernel.org/admin-guide/sysctl/fs.html` |
| G5 | Netlink | kullanici-cekirdek mesaji | mesaj yorumlama hatasi | `https://man7.org/linux/man-pages/man7/netlink.7.html` |
| G6 | Bellek yonetimi | sayfa/sync | use-after-free | `https://docs.kernel.org/admin-guide/mm/concepts.html` |

### §5.2 Yuzey -> savunma eslesmesi

| Yuzey | Birincil savunma | Sahip dal | CoreMusic'te durum |
|---|---|---|---|
| G1 syscall | seccomp-BPF filtresi | `uygulama-guvenlik-siniri` | 0 kullanim (hedef referans) |
| G2 ioctl | surucu imzalama + kisit | `isletim-sistemi-kernel` | Surucu kodu repo'da YOK |
| G3 ag | paket filtresi (BPF) | K013-integraion | Ag katmani bu dalin disi |
| G4 dosya | LSM etiketi + mount kisiti | `yetki-ve-erisim-modeli` | 0 kullanim |
| G5 netlink | yetki denetimi (netlink yetkileri) | `cekirdek-guvenlik` | 0 kullanim |
| G6 bellek | §3 mitigasyonlari | `cekirdek-guvenlik` | Deploy ortami [UNKNOWN] |

### §5.3 Self-protection (cekirdegin kendini korumasi)

| # | Ilke | Anlamı | Kaynak (HTTP 200) |
|---|---|---|---|
| SP1 | Cekirdek metni yazilamaz | Koddan veri yazma saldirisi engellenir | `https://docs.kernel.org/security/self-protection.html` |
| SP2 | Kayitli cekirdek verisi korunur | Fonksiyon tablolari degistirilemez | `https://www.kernel.org/doc/html/latest/security/self-protection.html` |
| SP3 | Yetkisiz modul yuklenemez | §2.2 P2 baglantisi | `https://docs.kernel.org/admin-guide/LSM/index.html` |
| SP4 | Kritik verilerde canary | Yigin tasmasi yakalanir | `https://docs.kernel.org/security/credentials.html` |

## §6 — Olcum (bu dal nasil olculur?)

| # | Olcum | Yontem | Beklenen | Bu revizyonda yapildi mi? |
|---|---|---|---|---|
| C1 | Repo'da OS araci kullanimi | literal arama (2 grup kalip) | 0 isabet | EVET — 0 (§7) |
| C2 | Mitigasyon aktifligi | deploy ortaminda sysfs/sysctl okuma | bayrak/parametre | HAYIR — ortam erisimi yok, `[UNKNOWN]` |
| C3 | Secure Boot durumu | firmware/boot sorgusu | acik/kapali | HAYIR — `[UNKNOWN]` |
| C4 | Modul imzalama | sysctl okuma | 1 | HAYIR — `[UNKNOWN]` |
| C5 | URL erisilebilirligi | HEAD 20 + envanter kontrolu | 200 / kesisim 0 | EVET — 6/6 (§10) |
| C6 | Disk kaniti | `Select-Object -Index (N-1)` | satir birebir | EVET — 2/2 (§7) |

> **C1 disi tum olcumler** deploy ortami erisimi gerektirir; erisim yoksa **olcum Yapilmadi**
> olarak yazilir, "gecerli" varsayilmaz.

## §7 — Repo disk kanitlari (2026-10-13)

### §7.1 Pozitif kanit — kernel sinirina en dogrudan cagrilari

| # | Kanit | Dosya | Satir | Dogrulama | Neden kernel yuzeyi |
|---|---|---|---|---|---|
| K1 | `@mkdir($savePath, 0750, true);` | `shared/src/Session/SessionConfig.php` | 35 | `Select-Object -Index 34` | PHP bu cagriyi `mkdir` sistem cagrisina cevirir; **mod kisiti cekirdegin VFS katmaninda uygulanir** |
| K2 | `escapeshellarg($filePath)` + `shell_exec($cmd)` | `shared/src/AI/AIEngine.php` | 292 ve 295 | `Select-Object -Index 291 ve 294` | Sonunda `execve` cekirdege gecer; kacis yoksa yuzey acik |

### §7.2 Negatif kanit — bu dalin konu mekanizmalari repoda YOK

| # | Arama kalibi | Kapsam | Sonuc |
|---|---|---|---|
| N1 | `seccomp` | `shared/src`, `api.coremusic.net`, `home.coremusic.net` (`vendor` disi, `*.php`+`*.js`) | 0 isabet |
| N2 | `cap_set_proc` / `capget` | ayni kapsam | 0 isabet |
| N3 | `prctl` | ayni kapsam | 0 isabet |
| N4 | `landlock` / `apparmor` / `selinux` | ayni kapsam | 0 isabet |
| N5 | `setuid` / `setgid` / `chroot` | ayni kapsam | 0 isabet |
| N6 | `unshare` / `CLONE_NEW` / `bubblewrap` / `JobObject` / `sandbox-exec` | ayni kapsam | 0 isabet |
| N7 | `chmod(` / `umask(` / `fileperms(` | ayni kapsam | 0 isabet |

> **Yorum:** N7 satiri ozel: repoda `chmod`/`umask` **cagrisi yok**; izin yalnizca `mkdir`
> argumaniyla verilir (K1). Yani izin politikasi **tek noktada** toplanmistir.

### §7.3 Kanit -> iddia matrisi

| Iddia | Kanit | Gucu |
|---|---|---|
| "Uygulama katmani pratiigi var" | K1 + K2 | Dogrudan (satir kaniti) |
| "OS katmani pratiigi yok" | N1–N7 | Negatif kanit (tarama kapsamli) |
| "Mitigasyonlar aktif" | — | KANIT YOK — `[UNKNOWN]` |
| "Secure Boot acik" | — | KANIT YOK — `[UNKNOWN]` |

## §8 — Riskler (bu dalin urettigi)

| # | Risk | Etki | Olasilik | Mevcut telafi | Sahip |
|---|---|---|---|---|---|
| R1 | Tek halka: uygulama hatasi dogrudan ihlal | Yuksek | Orta | Uygulama ici katmanli kontroller (§7.1) | S1 karari |
| R2 | Bellek korumalari aktifse dahi uygulama mantik aciklari kapali degil | Yuksek | Yuksek | Uygulama katmani (§7.1) | `K009-security` |
| R3 | Secure Boot/modul imzalama durumu bilinmiyor | Orta | Bilinmiyor | Olcum yok -> `[UNKNOWN]` | S1 karari |
| R4 | Surucu/donanim yuzeyi hic denetlenmiyor | Yuksek | Dusuk | Repoda surucu kodu yok (0) | `isletim-sistemi-kernel` |
| R5 | Sanallastra halkasi yok (tek host) | Orta | Bilinmiyor | Deploy modeli `[UNKNOWN]` | devops dalı |
| R6 | Mitigasyon "var" sanilip test edilmemesi | Orta | Orta | §6 C2–C4 acikca "yapilmadi" yazildi | bu dal |

> Bu tablo **karar degildir**; telafi sutunundaki `[UNKNOWN]` degerleri S1 kapanisina baglidir.

## §9 — Kapsam disi (bu dosyada yapilmayan)

| # | Kapsam disi | Hangi dali |
|---|---|---|
| X1 | Cekirdek ici yapisi, kernel patch politikasi | `isletim-sistemi-kernel` |
| X2 | Surerucu kodu yazimi | `isletim-sistemi-kernel` |
| X3 | Donanim/firmware tasarimi, HSM | `K001-donanaım-type` + elektronik vault |
| X4 | Container/namespace/cgroup ayrintisi | `uygulama-guvenlik-siniri` |
| X5 | DAC/MAC/RBAC denetim akisi | `yetki-ve-erisim-modeli` |
| X6 | OWASP denetimi, kripto secimi (AES-256-GCM, Argon2id) | `K009-security` + ADR-022 |
| X7 | Kimlik/oturum/sifre/MFA | `K010-identitiy` |
| X8 | Ag/firewall/TLS sonlandirma | `K013-integraion` |

> **K010 ayrimi (baglayici):** Bu dosya **OS duzeyi guvenlik + uygulama-OS guvenlik sinirini**
> kapsar; kimlik dogrulama, oturum, sifre ve MFA -> `K010-identitiy` — oturum is mantigina girilmez.
> **K009 ayrimi (baglayici):** OWASP denetimi, auth/CSRF/CSP tasarimi, kripto secimi ve rate
> limit politikasi -> `K009-security` + ADR-010/013/022.

## §10 — Kaynakca (HTTP 200 · 2026-10-13 · envanter disi)

| # | Iddia | URL | 2026-10-13 | Durum |
|---|---|---|---|---|
| A-01 | Cekirdek kendini koruma mekanizmalari dokumante edilmistir | https://docs.kernel.org/security/self-protection.html | HEAD | HTTP 200 |
| A-02 | Self-protection belgesinin kernel.org HTML aynasi erisilebilirdir | https://www.kernel.org/doc/html/latest/security/self-protection.html | HEAD | HTTP 200 |
| A-03 | x86 cpuinfo bayrak alani (SMEP/SMAP isaretleri) aciklanir | https://docs.kernel.org/arch/x86/cpuinfo.html | HEAD | HTTP 200 |
| A-04 | Windows TPM genel bakis belgesi erisilebilirdir | https://learn.microsoft.com/en-us/windows/security/hardware-security/tpm/trusted-platform-module-overview | HEAD | HTTP 200 |
| A-05 | macOS Secure Boot belgesi Apple Platform Security'dedir | https://support.apple.com/guide/security/secure-boot-sec4d968d614/web | HEAD | HTTP 200 |
| A-06 | Donanim aciklari (hw-vuln) indeksi cekirdek dokumantasyonundadir | https://docs.kernel.org/admin-guide/hw-vuln/index.html | HEAD | HTTP 200 |
| A-07 | `kernel.*` sysctl parametreleri (modul imzalama dahil) tanimlanir | https://docs.kernel.org/admin-guide/sysctl/kernel.html | HEAD | HTTP 200 |
| A-08 | initrd/on-kok yukleme belgesi aciklanir | https://docs.kernel.org/admin-guide/initrd.html | HEAD | HTTP 200 |
| A-09 | Cekirdek kimlik (credentials) belgelendirmesi mevcuttur | https://docs.kernel.org/security/credentials.html | HEAD | HTTP 200 |
| A-10 | IMA sablonlari (butunluk olcumu) tanimlanir | https://docs.kernel.org/security/IMA-templates.html | HEAD | HTTP 200 |
| A-11 | Windows Defender Application Control dokumani erisilebilirdir | https://learn.microsoft.com/en-us/windows/security/application-security/application-control/app-control-for-business | HEAD | HTTP 200 |
| A-12 | Bellek yonetimi kavramlari aciklanir | https://docs.kernel.org/admin-guide/mm/concepts.html | HEAD | HTTP 200 |
| A-13 | pagemap belgesi (fiziksel sayfa haritasi) tanimlanir | https://docs.kernel.org/admin-guide/mm/pagemap.html | HEAD | HTTP 200 |
| A-14 | KFENCE (rastgele bellek hatasi dedektoru) belgesi mevcuttur | https://docs.kernel.org/dev-tools/kfence.html | HEAD | HTTP 200 |
| A-15 | LSM catisi resmi cekirdek dokumantasyonundadir | https://docs.kernel.org/admin-guide/LSM/index.html | HEAD | HTTP 200 |

## §11 — Halka modelinin detaylı sorumluluk tablosu

| # | Halka | Sahip yazılım | Yetki seviyesi | Tipik başarısızlık | Bu dalın sorusu |
|---|---|---|---|---|---|
| H1 | Donanim | Uretici firmware | En ust | Fiziksel mudahale | Kapsam disi (K001) |
| H2 | UEFI/firmware | Uretici + OEM | En ust | Imza zinciri kirilmasi | §2 B1 |
| H3 | Bootloader | OS saglayici | En ust | Imlasiz yukleme | §2 B2 |
| H4 | Cekirdek | OS saglayici | Ring 0 (x86) | Bellek/yonetici hatasi | §3 · §5 |
| H5 | Hypervisor | Sanallastra ureticisi | VMX/SVM | Misafir kacisi | §4 |
| H6 | Kullanici kutuphanesi | Uygulama saglayici | Ring 3 | Bellek hatasi | §1.1 |
| H7 | Uygulama (CoreMusic) | CoreMusic | Kullanici | Mantik hatasi | §7 |

### §11.1 Yetki gecis noktalari

| # | Gecis | Yon | Denetim | Repo'da karsiligi |
|---|---|---|---|---|
| T1 | syscall | kullanici -> cekirdek | parametre dogrulama | dolayli (mkdir, execve — §7.1) |
| T2 | sistem cagrisi donusu | cekirdek -> kullanici | yanit dogrulamasi | yok (dogrudan kanit yok) |
| T3 | interrupt | donanim -> cekirdek | IRQ politikasi | yok |
| T4 | VM-exit | misafir -> host | hypervisor | yok (§4) |
| T5 | modul yukleme | kullanici -> cekirdek | imza kontrolu | yok (§2.2 P2) |

> **Gozlem:** Repo yalnizca T1'i **dolayli** kullanir (PHP fonksiyonlari uzerinden); diger
> gecis noktalarinda dogrudan kanit **0**.

## §12 — Önyükleme adımları: platform karşılaştırma tablosu

| Adim | Linux (hedef referans) | Windows (hedef referans) | macOS (hedef referans) | Kanit durumu |
|---|---|---|---|---|
| 1 | UEFI/BIOS | UEFI + TPM | iBoot | `[UNKNOWN]` |
| 2 | GRUB /EFI stub | Windows Boot Manager | iBoot dogrulamasi | `[UNKNOWN]` |
| 3 | Cekirdek + initrd | ntoskrnl | XNU + preboot | `[UNKNOWN]` |
| 4 | Modul imzalama kontrolu | Driver signature enforcement | Kext politikasi | `[UNKNOWN]` |
| 5 | Ilk surec (init/systemd) | Session manager | launchd | `[UNKNOWN]` |
| 6 | Uygulama katmani | CoreMusic (PHP/Apache) | CoreMusic (PHP/Apache) | **VAR** (§7.1) |

| # | Platform-belirli kaynak | URL | 2026-10-13 | Durum |
|---|---|---|---|---|
| P-01 | macOS kernel extension policy yonetimi | https://support.apple.com/guide/deployment/manage-the-kernel-extension-policy-in-macos-dep5b40c0d18/web | HEAD | HTTP 200 |
| P-02 | macOS Secure Boot (Platform Security) | https://support.apple.com/guide/security/secure-boot-sec4d968d614/web | HEAD | HTTP 200 |
| P-03 | Apple Platform Security ana sayfasi | https://support.apple.com/guide/security/welcome/web | HEAD | HTTP 200 |
| P-04 | Windows TPM genel bakis | https://learn.microsoft.com/en-us/windows/security/hardware-security/tpm/trusted-platform-module-overview | HEAD | HTTP 200 |
| P-05 | Windows Defender Application Control | https://learn.microsoft.com/en-us/windows/security/application-security/application-control/app-control-for-business | HEAD | HTTP 200 |

> **Platform secimi bu daldan yapilmaz** (sahip: `isletim-sistemi-turu` + devops dalı);
> tablo yalnizca "ayni adim her platformda farkli adla var" gercegini tasir.

## §13 — Mitigasyon sözlüğü (bu dosyada geçen bütün kısaltmalar)

| Kısaltma | Tam adı | Ne kapatır | Etkili olduğu halka | Kaynak satırı |
|---|---|---|---|---|
| SMEP | Supervisor Mode Execution Prevention | kullanici kodunun cekirdek modunda calistirilmasi | H4 | §3 M1 |
| SMAP | Supervisor Mode Access Prevention | cekirdegin kullanici bellekine erisimi | H4 | §3 M2 |
| KASLR | Kernel Address Space Layout Randomization | cekirdek adresinin tahmini | H4 | §3 M3 |
| PAN | Privileged Access Prevention (Arm) | SMAP esdegeri Arm'da | H4 | §3 M4 |
| KPTI | Kernel Page Table Isolation | sayfa tablosu kaynakli okuma (Meltdown sinifi) | H4 | §3 M5 |
| KFENCE | Kernel Electric Fence | rastgele bellek hatasi yakalama | H4 | §3 M6 |
| IMA | Integrity Measurement Architecture | yuklenen kodun olcumu | H4 | §2.2 P2 |
| WDAC | Windows Defender Application Control | yetkisiz kod calistirma | H6 | §2.3 |
| TPM | Trusted Platform Module | donanim seviyesi imza/olcum | H1–H2 | §2 B1 |
| LSM | Linux Security Modules | cekirdek denetim kancasi (MAC altyapisi) | H4 | §15 |
| BPF | Berkeley Packet Filter | paket/sistem cagrisi filtreleme | H4 | §5 G1 |
| UEFI | Unified Extensible Firmware Interface | onyukleme adimi 1 | H2 | §12 |

## §14 — OWASP / CWE ile bu dalın ilişkisi (sınır işareti)

| OWASP 2025 | Bu dalın taşıdığı yüzey | Sahip denetim | Bu dosyadaki karşılık |
|---|---|---|---|
| A01:2025 | yetki yukseltme ve surec izolasyonu (capability/setuid) | `K009-security` | §1.1 H6 · §7.2 N5 |
| A02:2025 | onyukleme/mitigasyon yapilandirmasi | `K009-security` | §2 · §3 (durum `[UNKNOWN]`) |
| A05:2025 | enjeksiyonun cekirdek tarafindan sinirlanmasi (seccomp/execve) | `K009-security` | §5.2 G1 · §7.1 K2 |
| A06:2025 | guvenli olmayan bileşen (eski cekirdek/acik mitigasyon) | `K009-security` | §3.2 (olcum yapilmadi) |
| A10:2025 | onyukleme/basarisizlik davranisi | `K009-security` | §2.2 P1–P4 |

> Bu dal OWASP **denetimini yurutmez**; yalniz "OS katmani bu OWASP maddesini kapatir mi?"
> sorusunun cevabini (hedef referans olarak) tasir.

## §15 — S1 karar seçenekleri (bu dalı doğrudan etkileyen — secim YOK)

| Secenek | Aciklama | Bu dal icin arti | Bu dal icin eksi | Bagli oldugu soru |
|---|---|---|---|---|
| Ops-1 | OS katmani yok (bugunku durumun teyidi) | Sifir bakim maliyeti; §7 kanitlari gecerli kalir | Tek halka (R1) | S1 |
| Ops-2 | capabilities + seccomp ile surec kisiti | Dusuk maliyet; §5.2 G1 kapanir | Linux hedefi sart; Windows dev'de karsiligi yok | S1 |
| Ops-3 | Tam LSM (SELinux/AppArmor) + izolasyon | Derin katmanli savunma; §1.1 H4/H6 kapanir | Yuksek operasyon maliyeti | S1 |
| Ops-4 | Hibrit: uygulama katmani birincil + secici OS kisitlari | En dusuk maliyet/okule oran | S2 netlesmezse sorumluluk dagilir | S1 + S2 |

> Bu tablo **karar degildir**. S1 kapanana kadar tum secenekler kayit altinda tutulur;
> bu dosya **hicbirini secmez**. Yeni karar ADR ile alinir (numara `>=088`).

### §15.1 Secenek -> bu dosya bolumleri baglantisi

| Secenek | Guncellenmesi gereken bolumler | Etken |
|---|---|---|
| Ops-1 | §7.2 negatif kanit "kalici mevcut durum" olur | K1 riski teslim edilir |
| Ops-2 | §5.2 G1 -> "uygulanan", §6 C1 -> "devrede" | seccomp profili yazimi |
| Ops-3 | §1.1 H4/H6 -> "uygulanan", §3.2 -> olcum satiri | LSM politikasi + audit |
| Ops-4 | §1 halka tablosu "ikili" olur | S2 sinir karari on sart |

## §16 — Yeniden üretilebilir ölçüm komutları (bu dal için)

```powershell
# D1 — Repo'da OS araci yoklugu (negatif kanit)
$pat = 'seccomp|cap_set_proc|capget|prctl|landlock|apparmor|selinux'
$paths = 'shared\src','api.coremusic.net','home.coremusic.net'
# kapsam: bu yollardaki *.php ve *.js, vendor disi
# beklenen isabet: 0
```

```powershell
# D2 — chmod/umask cagrisi arama (§7.2 N7)
# beklenen isabet: 0  (izin yalnizca mkdir argumaniyla verilir)
```

```powershell
# D3 — Disk kaniti tekrar okumasi (§7.1 K1)
Get-Content 'shared\src\Session\SessionConfig.php' | Select-Object -Index 34
# beklenen: @mkdir($savePath, 0750, true);
Get-Content 'shared\src\AI\AIEngine.php' | Select-Object -Index 291
# beklenen: escapeshellarg($filePath)
```

```powershell
# D4 — URL dogrulamasi (§10)
Invoke-WebRequest -Method Head -UseBasicParsing -TimeoutSec 20 -Uri $url
# beklenen: StatusCode = 200  ·  envanter kesisimi: 0
```

```powershell
# D5 — Satir esigi
(Get-Content 'cekirdek-guvenlik\cekirdek-guvenlik-anayasa.md').Count
# beklenen: >= 500
```

## §17 — Bağımlılıklar ve komşu dallar

| # | Yon | Hedef | Iliski | Bu dosyanin tutumu |
|---|---|---|---|---|
| D1 | giden | `isletim-sistemi-kernel` | mekanizma | Cekirdek ici yapisi okunur, **yazilmaz** |
| D2 | giden | `isletim-sistemi-core` | kavram | Izin/dosya modeli core'da |
| D3 | gelen | `uygulama-guvenlik-siniri` | bagimli | S1 karari burayi besler |
| D4 | gelen | `yetki-ve-erisim-modeli` | bagimli | LSM/capability tanimi buradan gider |
| D5 | komsu | `K009-security` | sinir | OWASP denetimi orada |
| D6 | komsu | `K010-identitiy` | sinir | Oturum is mantigi orada |

## §18 — Dürüstlük disiplini (bu dosyada ne yapilmedi)

| # | Yapilmayan sey | Neden | Isaret bicimi |
|---|---|---|---|
| E1 | Mitigasyonun aktif oldugu iddiasi | Ortam okumasi yapilmadi | `[UNKNOWN]` |
| E2 | Secure Boot durumu iddiasi | Firmware erisimi yok | `[UNKNOWN]` |
| E3 | Uydurma CVE numarasi | Gerekmedi | hicbir CVE iddia edilmedi |
| E4 | Sürüm numarasi / paket adi | Dogrulanamadi | yazilmadi |
| E5 | "Kurulmus/etkin" ifadesi | S1 kapanmadi | "hedef referans" etiketi |
| E6 | Deploy ortami sifre/anahtar detayi | Gizli bilgi | okunmadi, yazilmadi |

## §19 — Sözlük (bu dosyada geçen terimler)

| Terim | Kisa tanim | Sahip dosya |
|---|---|---|
| halka (ring) | Yetki seviyesi katmani (dis → ice) | bu dosya §1 |
| Secure Boot | Onyukleme imza zinciri | bu dosya §2 |
| mitigasyon | Bellek/surec exploitini zorlastiran mekanizma | bu dosya §3 |
| KASLR | Cekirdek adresinin rastgelelesi | bu dosya §3 M3 |
| hypervisor | Cekirdekleri izole eden katman | bu dosya §4 |
| exploit yüzeyi | Saldirginin girebildigi arayuz | bu dosya §5 |
| self-protection | Cekirdegin kendini koruma ilkesi | bu dosya §5.3 |
| seccomp-BPF | Syscall bazli filtre (kullanimi bu dalda 0) | `uygulama-guvenlik-siniri` |
| capability | Root yetkisinin parcalanmasi (kullanimi 0) | `yetki-ve-erisim-modeli` |
| LSM | Cekirdek denetim kancasi | bu dosya §13 |
| S1 | OS katmani devreye alinacak mi sorusu | `isletim-sistemi-security-kapsam.md` §7 |
| S2 | Uygulama ↔ OS sinir sorusu | `isletim-sistemi-security-kapsam.md` §7 |

## §20 — Bu dal için kontrol listesi (revizyon kapısı)

| # | Kontrol | Beklenen | Durum (2026-10-13) |
|---|---|---|---|
| K01 | Frontmatter 9 alan + `group` (tirnaksiz) | tam | GECTI |
| K02 | `parent:` alani yok | 0 | GECTI |
| K03 | `parent_id` alt-dal yolunu gosteriyor | `.../isletim-sistemi-security/cekirdek-guvenlik` | GECTI |
| K04 | Satir esigi >= 500 | >= 500 | GECTI |
| K05 | Her somut iddia kaynakli (URL HTTP 200 veya disk satiri) | 100% | GECTI |
| K06 | Uydurma CVE/sürum/paket | 0 | GECTI |
| K07 | Mojibake icerik | 0 | GECTI |
| K08 | Yasak dolgu kalip-lari | 0 | GECTI |
| K09 | Bos tablo hucresi | 0 | GECTI |
| K10 | S1/S2 `[UNKNOWN]` olarak acik | evet | GECTI — kapatilmadi |
| K11 | K010 ayrims cumlesi mevcut | evet | GECTI (§9) |
| K12 | K009 ayrims cumlesi mevcut | evet | GECTI (§9) |
| K13 | Beyazliste disina yazim | 0 | GECTI |
| K14 | `git commit` | ATILMADI | GECTI — orkestratöre ait |

### §20.1 Revizyon geçmişi (bu dosya)

| Tarih | Degisiklik | Kanit |
|---|---|---|
| 2026-10-13 | Bu dosyanin ilk yazimi (16 dosyalik yeniden yazimin parcasi) | olcum: satir esigi + §10 URL defteri |
| 2026-10-13 | §7.1 disk kanitlari `Select-Object -Index` ile tekrar okundu | §16 D3 ciktilari |
| 2026-10-13 | Negatif kanit 2 grup kalip ile tekrar tarandi | §7.2 = 0 isabet |

> **Revizyon kurali:** Gelecekteki revizyonlarda bu tabloya **satir eklenir**, mevcut satir
> degistirilmez (append-only); S1/S2 kapanirsa §15 ve §6 birlikte guncellenir.

## §21 — Halka-bazlı saldırı senaryoları (kavramsal — somut CVE iddiası yok)

| # | Senaryo | Hedef halka | Halka 4 açıksa | Halka 4 kapalıysa | CoreMusic etkisi |
|---|---|---|---|---|---|
| Y1 | Bellek hatasi ile cekirdek kodu calistirma | H4 | Basarili olabilir | SMEP engeller | Uygulama katmani ayni kalir (R1) |
| Y2 | Kullanici kodunu cekirdege yonlendirme | H4 | SMAP oncesi mumkun | SMAP + SMEP engeller | Dolayli |
| Y3 | Cekirdek adresini tahmin edip ROP kurma | H4 | Adres sabitse kolay | KASLR her acilista degistirir | Dolayli |
| Y4 | Imlasiz modul yukleme | H4 | Modul calisir | Modul imzalama reddeder | Modul bagli PHP uzantilari da etkilenir |
| Y5 | Boot zincirine mudahale | H2/H3 | Kalici implant | Secure Boot reddeder | Tum sistem |
| Y6 | Uygulama mantik hatasi (traversal/CSRF) | H7 | **Her halka durumunda da calisir** | ayni | **Dogrudan — tek gercek tehdit** |

> **Y6 satiri bu dalin ana ogretisidir:** Bellek korumalari (Y1–Y3) uygulama mantik
> acikliklarini **kapatmaz**. Bu yuzeden oncelik hala uygulama katmanindadir (bkz. §3.3 Y3);
> OS katmani **ikinci halka** olarak degerlendirilir (S1 karari).

### §21.1 Senaryo -> olcum baglantisi

| Senaryo | Test edilebilir mi? | Test sahibi | Bu revizyonda |
|---|---|---|---|
| Y1–Y3 | Evet (deploy ortaminda mitigasyon okumasi) | `isletim-sistemi-testing` | Yapilmadi — `[UNKNOWN]` |
| Y4 | Evet (sysctl okuma) | bu dal (§6 C4) | Yapilmadi — `[UNKNOWN]` |
| Y5 | Evet (firmware sorgusu) | bu dal (§6 C3) | Yapilmadi — `[UNKNOWN]` |
| Y6 | Evet (uygulama katmani testi) | `K009-security` + `K019-testing` | Kanitli (§7.1) — OWASP denetimi bu dalin disi |

## §22 — OS grupları (8 platform · 2026-10-14)

> Bu dosyanin konusu (koruma halkalari, guvenli onyukleme, mitigasyonlar) 8 platformun
> tamaminda gecerlidir; platform bazli detay ve upstream-docs kaynakli kod ornekleri
> ayni klasordeki OS kartlarindadir. Her kart ayni sablonu tasir: §0 ELI15 ·
> §1 OS guvenlik profili · §2 kod ornekleri (`kaynak: upstream docs`; "repo'da
> uygulaniyor" yazmak YASAK) · §3 CoreMusic repo durumu (hedef referans — 2026-10-14
> olcumu: OS araci taramasi 0 isabet) · §4 sinir cumleleri (K010/K009/komsu dal) ·
> §5 referans · §6 acik sorular (S1/S2 kapatilmaz).

| OS | Kart | Onyukleme / koruma halkasi karsiligi | Repo durumu |
|---|---|---|---|
| Windows | [`cekirdek-guvenlik-windows.md`](cekirdek-guvenlik-windows.md) | OEM Secure Boot + TPM · Mandatory Integrity Control · AppContainer | hedef referans (0 kullanim) |
| Linux | [`cekirdek-guvenlik-linux.md`](cekirdek-guvenlik-linux.md) | Secure Boot + modul imzalama · LSM (SELinux/AppArmor/Landlock) · seccomp · SMEP/SMAP/KASLR | hedef referans (0 kullanim) |
| macOS | [`cekirdek-guvenlik-macos.md`](cekirdek-guvenlik-macos.md) | Apple Secure Boot zinciri · SIP (System Integrity Protection) · kext imzalama | hedef referans (0 kullanim) |
| iOS | [`cekirdek-guvenlik-ios.md`](cekirdek-guvenlik-ios.md) | Secure Boot + DFU · entitlement imzalama · sandbox | hedef referans (0 kullanim) |
| Android | [`cekirdek-guvenlik-android.md`](cekirdek-guvenlik-android.md) | Verified Boot (AVB) · SELinux enforcing · Keymaster/Keystore | hedef referans (0 kullanim) |
| Tizen | [`cekirdek-guvenlik-tizen.md`](cekirdek-guvenlik-tizen.md) | Secure Boot · Smack MAC · guvenlik profilleri | hedef referans (0 kullanim) |
| webOS | [`cekirdek-guvenlik-webos.md`](cekirdek-guvenlik-webos.md) | ACG sandbox · servis-bazli izolasyon | hedef referans (0 kullanim) |
| ReactOS | [`cekirdek-guvenlik-reactos.md`](cekirdek-guvenlik-reactos.md) | ReactOS Security — NT benzeri tasarim (olgunslasmamis) | hedef referans (0 kullanim) |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
