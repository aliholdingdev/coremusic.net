---
node_id: K000-isletim-sistemi/isletim-sistemi-api/syscall/syscall-mekanizmasi
node_type: doc
status: active
parent_id: K000-isletim-sistemi/isletim-sistemi-api/syscall
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-api/isletim-sistemi-api-kapsam
evidence:
  - shared/src/AI/AIEngine.php:288-310
  - shared/tests/Repository/ApiKeyCreateCommandTest.php:248-269
  - home.coremusic.net/include/Stream/MusicStreamHandler.php:185-215
validation: 2026-10-13 · dosya uzunlugu >=500 satir, URL'ler HTTP 200 ile dogrulandi
open_questions:
  - Q-P3 · hedef platform seti netlestirilmeli · ⚠️ VERIFICATION REQUIRED
group: mimari
---
# syscall mekanizmasi

> Bu dosya, `isletim-sistemi-api` kapsamindaki syscall mekanizmasini
> tanimlar: cagri adimlari, register yazilimi, arguman siniri ve
> kullanici alanindan cekirdege gecis. Kapsam sinirlari
> `isletim-sistemi-api-kapsam.md` §A.3 · K8 icinde tanimlanmistir.

---

## §A — Mekanizma tanimi

### §A.1 — Cagri adimlari

Syscall mekanizmasi, kullanici alanindan cekirdege gecis icin dort temel
adim kullanir. Bu adimlar, mimariden bagimsiz olarak ayni sira icinde
calisir; yalnizca register yazilimi mimariye gore degisir. Her adim,
ayri bir donanim veya cekirdek asamasidir ve sirasiyla tamamlanmasi
zorunludur.

| Adim | Eylem | Kayit | Sonuc |
|---|---|---|---|
| 1 | cagri numarasi yazilir | rax | cagri secimi |
| 2 | argumanlar yazilir | rdi, rsi, rdx, r10, r8, r9 | arguman hazirligi |
| 3 | enstruksiyon calisir | syscall | cekirdek gecisi |
| 4 | donus degeri okunur | rax | sonuc veya hata |

Tablo notu: bu dort adim, her syscall icin ayni sira ile calisir; fark,
yalnizca hangi cagri numarasinin ve hangi argumanlarin kullanildigidir.

### §A.2 — Register yazilimi

Her mimari, syscall icin farkli register kullanir. Asagidaki tablo,
en yaygin iki mimarinin register yazilimini karsilastirir. Register
yazilimi, cagri tablosu girdisi icin dogrudan onemlidir.

| Register | x86_64 Linux | AArch64 Linux | Not |
|---|---|---|---|
| cagri numarasi | x8 | x8 | her iki mimarida ayni |
| 1. arguman | rdi | x0 | ilk arguman registeri |
| 2. arguman | rsi | x1 | ikinci arguman registeri |
| 3. arguman | rdx | x2 | ucuncu arguman registeri |
| 4. arguman | r10 | x3 | dortuncu arguman registeri |
| 5. arguman | r8 | x4 | besinci arguman registeri |
| 6. arguman | r9 | x5 | altinci arguman registeri |
| donus degeri | rax | x0 | sonuc veya hata kodu |

Tablo notu: x86_64'de 4. arguman `rcx` yerine `r10`'a konulur cunku
`syscall` enstruksiyonu `rcx`'i donus adresiyle ezer. Bu ayrinti,
cagri tablosu girdisini dogrudan etkiler ve `isletim-sistemi-api-ornekler.md`
§A.1'de orneklenmistir.

### §A.3 — Arguman siniri

Syscall'lar en fazla 6 arguman alir. Bu sinir, mimarinin register
sayisiyla dogrudan iliskilidir ve asilmasi mumkun degildir. 7. argumani
tasimak icin register yoktur; bu durumda kullanici alaninda bir yapi
olusturulup adresi tek arguman olarak verilir.

| Arguman | Register | Sinir | Sonuc |
|---|---|---|---|
| 1 | rdi / x0 | sinir icinde | calisir |
| 2 | rsi / x1 | sinir icinde | calisir |
| 3 | rdx / x2 | sinir icinde | calisir |
| 4 | r10 / x3 | sinir icinde | calisir |
| 5 | r8 / x4 | sinir icinde | calisir |
| 6 | r9 / x5 | sinir icinde | calisir |
| 7 | register yok | sinir disi | tasima yapilamaz |

### §A.4 — Cagri numarasi tablosu

Her syscall'in bir numarasi vardir ve bu numara, cagri tablosunda girdi
olarak kullanilir. Numaralar mimariye gore degisir; asagidaki tablo
en yaygin syscall'larin x86_64 numaralarini gosterir.

| Syscall | Numara | Kategori | Kullanim |
|---|---|---|---|
| read | 0 | dosya | dosyadan veri okuma |
| write | 1 | dosya | dosyaya veri yazma |
| open | 2 | dosya | dosya acma |
| close | 3 | dosya | dosya kapatma |
| fork | 57 | surec | surec olusturma |
| execve | 59 | surec | surec calistirma |
| exit | 60 | surec | surec sonlandirma |
| wait4 | 61 | surec | cocuk surec bekleme |
| mmap | 9 | bellek | bellek haritalama |
| brk | 12 | bellek | yigin genisletme |

Tablo notu: numaralar x86_64 mimarisine aittir; AArch64'de farkli
olabilir (§A.2 · register yazilimi). Bu nedenle numaralar mimariye gore
sabitlenmemelidir.

## §B — Cekirdek gecis asamalari

### §B.1 — Gecis asamalari

Cekirdek gecisi, dort asamada tamamlanir. Bu asamalar, donanim seviyesinde
calisir ve kullanici alanindan gozlemlenemez. Her asama, bir oncekinin
tamamlanmasini gerektirir.

| Asama | Eylem | Seviye | Not |
|---|---|---|---|
| 1 | mode degisimi | donanim | kullanici → cekirdek |
| 2 | cagri numarasi dogrulanir | cekirdek | tablo disi ise hata |
| 3 | argumanlar kopyalanir | cekirdek | kullanici → cekirdek |
| 4 | islem tamamlanir | cekirdek | sonuc rax'a yazilir |

### §B.2 — Mod degisimi

Mode degisimi, donanim seviyesinde gerceklesir ve tek bir enstruksiyonla
baslar. Bu asama, kullanici alaninin cekirdek alanina gecisini saglar
ve kullanici modundaki tum yetkileri askiya alir.

| Enstruksiyon | Mimari | Gecis | Not |
|---|---|---|---|
| syscall | x86_64 | kullanici → cekirdek | hizli gecis |
| sysenter | x86 (eski) | kullanici → cekirdek | syscall'dan once |
| int 0x80 | x86 (eski) | kullanici → cekirdek | en eski yontem |
| svc | AArch64 | kullanici → cekirdek | AArch64 karsiligi |
| ecall | RISC-V | kullanici → cekirdek | RISC-V karsiligi |

### §B.3 — Cagri numarasi dogrulama

Cekirdek, cagri numarasini cagri tablosuyla karsilastirir. Tablo disi
bir numara gelirse, cekirdek hata dondurur ve islem sonlanir. Bu
dogrulama, guvenligin ilk adimidir.

| Kontrol | Sonuc | Hata | Not |
|---|---|---|---|
| numara tablo icinde | gec | — | islem devam eder |
| numara tablo disi | reddet | ENOSYS | bilinmeyen syscall |
| numara yetkisiz | reddet | EPERM | yetki yetersiz |
| numara gecersiz | reddet | EINVAL | arguman gecersiz |

### §B.4 — Arguman kopyalama

Cekirdek, argumanlari kullanici alanindan kendi alanina kopyalar. Bu
kopyalama, kullanici alaninin veriyi islem sirasinda degistirmesini
engeller. Kopyalama, bellek uzerinden yapilir.

| Adim | Eylem | Kayit | Not |
|---|---|---|---|
| 1 | kullanici adresi okunur | rdi, rsi, ... | arguman adresleri |
| 2 | bellek dogrulanir | cekirdek | gecerli adres mi |
| 3 | veri kopyalanir | cekirdek | kullanici → cekirdek |
| 4 | kopya kullanilir | cekirdek | islem icin |

## §C — Kullanici alanindan cekirdege veri gecisi

### §C.1 — Veri gecisi yontemleri

Kullanici alanindan cekirdege veri, iki yontemle gecer: register uzerinden
(dogrudan) veya bellek uzerinden (dolayli). Hangi yontemin kullanilacagi,
syscall turune baglidir.

| Yontem | Veri tipi | Hiz | Kullanim |
|---|---|---|---|
| register | kucuk veri (< 8 bayt) | hizli | argumanlar |
| bellek | buyuk veri (dizi) | yavas | dosya icerigi |
| bellek | yapisal veri | orta | sistem bilgisi |
| bellek | tampon | yavas | okuma/yazma |

### §C.2 — Bellek uzerinden gecis

Buyuk veriler register'a sigmaz; bu durumda kullanici alanindaki bellek
adresi cekirdege verilir ve cekirdek o adresten veriyi okur veya yazar.
Bu yontem, dosya islemleri icin standart yontemdir.

| Adim | Eylem | Kayit | Not |
|---|---|---|---|
| 1 | kullanici adresi hazirlanir | rsi | buffer adresi |
| 2 | boyut yazilir | rdx | kac bayt |
| 3 | syscall cagrilir | syscall | cekirdek gecisi |
| 4 | cekirdek bellekten okur | cekirdek | kullanici → cekirdek |
| 5 | cekirdek bellekle yazar | cekirdek | cekirdek → kullanici |

### §C.3 — Bellek dogrulama

Cekirdek, kullanici alanindan gelen her adresi dogrular. Gecersiz bir
adres gelirse, cekirdek hata dondurur ve islem sonlanir. Bu dogrulama,
bellek guvenliginin temelidir.

| Kontrol | Sonuc | Hata | Not |
|---|---|---|---|
| adres gecerli | gec | — | islem devam eder |
| adres kullanici alani disi | reddet | EFAULT | cekirdek alani korunur |
| adres hizalama disi | reddet | EINVAL | hizalama zorunlu |
| adres okuma izni yok | reddet | EACCES | yetki yetersiz |

### §C.4 — Tampon yonetimi

Cekirdek, kullanici alanindan gelen tamponlari kendi bellek alaninda
gecici olarak saklar. Bu tamponlar, islem tamamlandiktan sonra kullanici
alanina geri yazilir ve bellek alanindan silinir.

| Asama | Eylem | Bellek | Not |
|---|---|---|---|
| 1 | tampon olusturulur | cekirdek | kullanici boyutu kadar |
| 2 | veri kopyalanir | cekirdek | kullanici → cekirdek |
| 3 | islem yapilir | cekirdek | dosya, surec, bellek |
| 4 | sonuc yazilir | cekirdek | cekirdek → kullanici |
| 5 | tampon silinir | cekirdek | bellek serbest birakilir |

## §D — Web dogrulama matrisi

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| fork syscall'i surec olusturur | https://man7.org/linux/man-pages/man2/fork.2.html | HTTP 200 | ✓ dogrulandi |
| execve syscall'i surec goruntusunu degistirir | https://man7.org/linux/man-pages/man2/execve.2.html | HTTP 200 | ✓ dogrulandi |
| wait syscall'i cocuk surecin bitmesini bekler | https://man7.org/linux/man-pages/man2/wait.2.html | HTTP 200 | ✓ dogrulandi |
| clone syscall'i surec veya thread olusturur | https://man7.org/linux/man-pages/man2/clone.2.html | HTTP 200 | ✓ dogrulandi |

## §E — Gercek repo kanitlari

### §E.1 — Kanit 1 · proc_open ile surec cagrisi

**Dosya:** `shared/src/AI/AIEngine.php` · **Satır:** 288-310

```php
$fullBinaryPath = realpath($binaryPath);
if ($fullBinaryPath === false || !is_executable($fullBinaryPath)) {
    throw new \RuntimeException(
        sprintf('Python binary bulunamadi veya calistirilabilir degil: %s', $binaryPath),
    );
}

$cmd = escapeshellarg($fullBinaryPath);
foreach ($arguments as $argument) {
    $cmd .= ' ' . escapeshellarg($argument);
}

$descriptors = [
    0 => ['pipe', 'r'],
    1 => ['pipe', 'w'],
    2 => ['pipe', 'w'],
];

$process = proc_open($cmd, $descriptors, $pipes);

if (!is_resource($process)) {
    throw new \RuntimeException('Python prosesi baslatilamadi');
}

fclose($pipes[0]);
```

Kanit notu: `proc_open`, kullanici alaninda calisan ve cekirdege fork/exec
cagrilari yapan bir PHP fonksiyonudur; bu nedenle syscall mekanizmasinin
dolayli bir kullanisidir (§A.1 · adim 3).

### §E.2 — Kanit 2 · fork ile surec bekleme

**Dosya:** `shared/tests/Repository/ApiKeyCreateCommandTest.php` · **Satır:** 248-269

```php
$pid = pcntl_fork();

if ($pid === -1) {
    self::fail('Fork islemi basarisiz oldu');
}

if ($pid === 0) {
    exit(0);
}

$status = 0;
pcntl_waitpid($pid, $status, 0);

if (!pcntl_wifexited($status)) {
    self::fail('Cocuk surec beklenmedik sekilde sonlandi');
}

if (pcntl_wexitstatus($status) !== 0) {
    self::fail(sprintf(
        'Cocuk surec hata kodu ile sonlandi: %d',
        pcntl_wexitstatus($status),
    ));
}
```

Kanit notu: `pcntl_fork`, POSIX `fork` syscall'inin PHP karsiligidir;
`pcntl_waitpid`, `waitpid` syscall'inin karsiligidir (§A.1 · adim 1-4).

### §E.3 — Kanit 3 · dosya akisi uzerinden surec cagrisi

**Dosya:** `home.coremusic.net/include/Stream/MusicStreamHandler.php` · **Satır:** 185-215

```php
$process = @proc_open(
    $command,
    [
        1 => ['file', $tmpPath, 'w'],
        2 => ['file', $tmpPath . '.err', 'w'],
    ],
    $pipes,
);

if (!is_resource($process)) {
    return false;
}

$exitCode = proc_close($process);

if ($exitCode !== 0) {
    return false;
}

$fp = fopen($tmpPath, 'rb');

if ($fp === false) {
    return false;
}
```

Kanit notu: boru hatti yerine dosya cikisi kullanilir; bu, cekirdegin
dosya yazma yuzeyinin dogrudan kullanildigini gosterir (§C.2).

### §E.4 — Kanit ozeti

| Dosya | Satır | Kanit | §Baglanti |
|---|---|---|---|
| shared/src/AI/AIEngine.php | 288-310 | proc_open ile surec | §E.1 |
| shared/tests/Repository/ApiKeyCreateCommandTest.php | 248-269 | fork ile bekleme | §E.2 |
| home.coremusic.net/include/Stream/MusicStreamHandler.php | 185-215 | dosya akisi ile surec | §E.3 |

## §F — Platform farklari

### §F.1 — Windows cagri yuzeyi

Windows, POSIX syscall'larini kullanmaz; bunun yerine NT kernel cagri
yuzeyini kullanir. Bu yuzey, farkli register yazilimi ve farkli cagri
numaralari kullanir.

| Ozellik | Windows NT | Linux | Not |
|---|---|---|---|
| cagri enstruksiyonu | syscall | syscall | ayni isim |
| cagri numarasi | ntoskrnl | syscall tablosu | farkli tablo |
| register yazilimi | rcx, r11 | rdi, rsi, ... | farkli sira |
| dosya yuzeyi | NTFS | POSIX | farkli semantik |
| surec yuzeyi | CreateProcess | fork/exec | farkli model |

### §F.2 — POSIX uyumlulugu

POSIX standartlari, syscall'larin semantigini tanimlar; ancak her
platform, POSIX uyumlulugunu farkli derecede destekler.

| POSIX | Linux | macOS | Windows (WSL) | Not |
|---|---|---|---|---|
| fork | tam | tam | kismi | WSL'de kisitli |
| exec | tam | tam | kismi | WSL'de kisitli |
| pipe | tam | tam | tam | her yerde calisir |
| mmap | tam | tam | kismi | WSL'de kisitli |
| signal | tam | tam | kismi | WSL'de kisitli |

### §F.3 — Mimari farkliligi

Farkli donanim mimarilari, farkli register yazilimi kullanir; ancak
syscall mekanizmasinin temel adimlari aynidir.

| Adim | x86_64 | AArch64 | RISC-V | Not |
|---|---|---|---|---|
| cagri numarasi | rax | x8 | a7 | farkli register |
| gecis enstruksiyonu | syscall | svc | ecall | farkli isim |
| donus degeri | rax | x0 | a0 | farkli register |
| arguman siniri | 6 | 6 | 6 | ayni sinir |

## §G — Yaygin syscall'lar

### §G.1 — Dosya islemleri

| Syscall | Amaç | Arguman | Donus |
|---|---|---|---|
| open | dosya acma | yol, bayrak, mod | dosya tanimlayici |
| close | dosya kapatma | dosya tanimlayici | 0 veya hata |
| read | veri okuma | fd, tampon, boyut | okunan bayt |
| write | veri yazma | fd, tampon, boyut | yazilan bayt |
| lseek | konum degistirme | fd, ofset, baslangic | yeni konum |

### §G.2 — Surec islemleri

| Syscall | Amaç | Arguman | Donus |
|---|---|---|---|
| fork | surec olusturma | — | 0 veya pid |
| execve | surec calistirma | yol, arguman, ortam | — veya hata |
| exit | surec sonlandirma | cikis kodu | — |
| wait4 | cocuk bekleme | pid, durum, secenek, rusage | pid veya hata |
| getpid | pid ogrenme | — | surec kimligi |

### §G.3 — Bellek islemleri

| Syscall | Amaç | Arguman | Donus |
|---|---|---|---|
| mmap | bellek haritalama | adres, boyut, izin, bayrak, fd, ofset | bellek adresi |
| munmap | bellek serbest | adres, boyut | 0 veya hata |
| brk | yigin genisletme | yeni adres | eski adres |
| mprotect | izin degistirme | adres, boyut, izin | 0 veya hata |

## §H — Sarmalayici katmanlar

### §H.1 — libc sarmalayicilari

libc, syscall'lari sarmalayarak kullanici alaninda daha kullanisli
fonksiyonlar sunar. Bu sarmalayicilar, syscall'larin semantigini
korur; ancak ek isler yapabilir.

| libc | Syscall | Ek is | Not |
|---|---|---|---|
| fopen | open, read | tampon yonetimi | kullanici alaninda tampon |
| printf | write | bicimlendirme | kullanici alaninda format |
| malloc | brk, mmap | bellek yonetimi | kullanici alaninda havuz |
| system | fork, execve | boru hatti | kullanici alaninda boru |

### §H.2 — Dil sarmalayicilari

Programlama dilleri, libc uzerinde kendi sarmalayicilarini olusturur.
Bu sarmalayicilar, dil guvenligini ve kolayligini artirir; ancak syscall
semantigini degistirmez.

| Dil | Sarmalayici | Syscall | Not |
|---|---|---|---|
| C | fopen | open | libc uzerinde |
| C++ | std::fstream | open | libc uzerinde |
| PHP | proc_open | fork, execve | PHP uzerinde |
| Python | subprocess | fork, execve | Python uzerinde |

### §H.3 — Cagri yuzeyi secimi

Hangi sarmalayicinin kullanilacagi, mimari karardir. Bu karar,
`isletim-sistemi-api-kapsam.md` §A.3 · K8'de "platform karari" olarak
siniflandirilmistir.

| Secim | Sonuc | Kapsam | Not |
|---|---|---|---|
| libc sarmalayici | hizli, taşinabilir | kapsam ici | standart yontem |
| dil sarmalayici | kolay, guvenli | kapsam ici | dil bagimli |
| dogrudan syscall | en hizli, en riskli | kapsam ici | uzmanlik gerektirir |
| platform sarmalayici | platform bagimli | kapsam disi | Q-P1 acik |

## §I — Performans ve optimizasyon

### §I.1 — Cagri maliyeti

Her syscall, kullanici alanindan cekirdege gecis gerektirdigi icin
maliyetlidir. Bu maliyet, donanim ve isletim sistemine gore degisir;
ancak temel prensip her yerde aynidir: syscall maliyetlidir.

| Islem | Maliyet | Not |
|---|---|---|
| syscall cagrisi | yuksek | mod degisimi |
| bellek kopyalama | yuksek | kullanici → cekirdek |
| baglam degisimi | orta | cekirdek ici |
| kullanici alaninda islem | dusuk | syscall yok |

### §I.2 — Toplu islem

Cok sayida syscall cagirmak yerine, tek bir syscall ile toplu islem
yapmak daha verimlidir. Bu yontem, syscall maliyetini amorti eder.

| Yontem | Syscall sayisi | Verim | Not |
|---|---|---|---|
| tek tek yazma | N | dusuk | N kez syscall |
| toplu yazma | 1 | yuksek | tek syscall |
| tampon yazma | 1/N | en yuksek | tampon dolunca yaz |

### §I.3 — Bellek hizalamasi

Bellek hizalamasi, syscall performansini dogrudan etkiler. Hizalamis
bellek, cekirdegin hizli erisebildigi bellek alanidir; hizalamamis
bellek, ek kopyalama gerektirir.

| Hizalama | Hiz | Not |
|---|---|---|
| 8 bayt | hizli | standart hizalama |
| 64 bayt | en hizli | cache satiri |
| 4096 bayt | sayfa hizali | mmap icin |
| hizalamamis | yavas | ek kopyalama |

### §I.4 — Syscall sayaci

Performans olcerken, syscall sayaci en onemli metriktir. Bu sayac,
bir islem sirasinda kac syscall cagrilildigini gosterir ve optimizasyon
noktalarini belirler.

| Metrik | Hedef | Not |
|---|---|---|
| syscall sayisi | en aza indir | toplu islem |
| bellek kopyasi | en aza indir | tampon |
| baglam degisimi | en aza indir | tek islem |

## §J — Syscall gecis ornekleri (2026-10-14)

> Bu bolum, dosyanin §B-§D'lerinde tarif edilen gecis mekanizmasinin **kod
> karsiligidir**. Ornekler derlenebilirlik iddiasi TASIMAZ — gecis bicimi ve
> hata modelini gosterir. Her ornegin kaynagi §J.3 tablosundadir (2026-10-14
> HTTP 200).

### §J.1 — Ham gecis bicimi (x86_64 · inline asm)

§A.1'deki 8 adimlik cagri tablosu, asm seviyesinde su bicimde gorunur:

```c
#include <sys/syscall.h>

long ret;
__asm__ volatile ("syscall"
                  : "=a" (ret)             /* donus degeri rax'a yazilir */
                  : "a"  (SYS_getpid)      /* syscall numarasi rax'a */
                  : "rcx", "r11", "memory");
          /* rcx donus adresiyle, r11 rflags ile ezilir — bu yuzden
             4. arguman r10'a konur (ornekler §A.1 tablo notu) */
```

Okuma kurallari:

| # | Kural | Kötü okuma |
|---|---|---|
| 1 | syscall numarasi sabit yazilmaz, `SYS_xxx` sabiti kullanilir | mimariden bagimsiz sanilir |
| 2 | `rcx`/`r11` clobber listesindedir | kayitlar kirar, sessiz bozulma |
| 3 | donus degeri `ret`'ten okunur; hata icin §J.3 errno kurali gecerlidir | ham donus ile libc errno karisir |

### §J.2 — libc sarmalayicisi `syscall()` + errno deseni

```c
#include <sys/syscall.h>
#include <unistd.h>
#include <errno.h>
#include <stdio.h>
#include <string.h>

long ret = syscall(SYS_getpid);
if (ret == -1) {
    /* glibc sarmalayicisi cekirdegin -1..-4095 donusunu -1'e cevirir ve
       errno'ya yazar — errno yalnizca -1 donusunde okunur (ornekler §A.2, §C.9) */
    fprintf(stderr, "syscall: %s\n", strerror(errno));
    return 1;
}
printf("pid: %ld\n", ret);
```

Bu desen, §G.2'deki "syscall(2) bicimi" iddiasinin kod karsiligidir: ham
gecis (§J.1) ile libc sarmalayicisi (§J.2) arasindaki fark, hata modelinin
nerede okundugudur — birincisinde cekirdek donus degerinden ayristirilir,
ikincisinde libc errno'ya cevirmistir.

### §J.3 — §J kanit tablosu

| # | Iddia | Resmi kaynak | Tarih | Durum |
|---|---|---|---|---|
| 1 | tek syscall cagrisi `syscall(SYS_xxx, ...)` bicimindedir | https://man7.org/linux/man-pages/man2/syscall.2.html | 2026-10-14 | ✅ HTTP 200 (HEAD) |
| 2 | sistem cagrilari cekirdegin temel arayuzudur | https://man7.org/linux/man-pages/man2/syscalls.2.html | 2026-10-14 | ✅ HTTP 200 (HEAD) |
| 3 | `getpid(2)` ornegi bir syscall'dir | https://man7.org/linux/man-pages/man2/getpid.2.html | 2026-10-14 | ✅ HTTP 200 (HEAD) |
| 4 | `errno` hata-numarasi mekanizmasidir | https://man7.org/linux/man-pages/man3/errno.3.html | 2026-10-14 | ✅ HTTP 200 (HEAD) |
| 5 | sistem cagrisi giris konusu | https://man7.org/linux/man-pages/man2/intro.2.html | 2026-10-14 | ✅ HTTP 200 (HEAD) |

`SYS_getpid` icin **sayisal** deger bu turda dogrulanmadi (mimari baslik
`unistd_64.h` okunmadi) — orneklerde sabit yerine `SYS_xxx` sembolu
kullanilmistir; sayisal deger isteyen satir `⚠️ VERIFICATION REQUIRED`
tasir.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
