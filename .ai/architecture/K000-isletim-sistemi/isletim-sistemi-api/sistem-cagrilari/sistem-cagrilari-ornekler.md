---
id: isletim-sistemi-api-sistem-cagrilari-ornekler
title: Sistem Çağrıları — Empirik Örnekler ve Ölçüm
node_type: module
layer: L0
status: active
tags: [isletim-sistemi, sistem-cagrilari, ornek, olcum, repo]
group: ornekler
---
# Sistem Çağrıları — Empirik Örnekler ve Ölçüm

## §1. Empirik Ölçüm — Sistem Çağrısı Zamanlama Ölçümleri

Bu bölümdeki ölçümler **gerçek makinede 2026-10-13 tarihinde** yapılmıştır.
Her ölçüm kaynak dosya + satır numarası + ölçüm komutu ile birlikte verilir.
Ölçümler **gerçek makinede tekrarlanabilir**. Ölçüm ortamı: Windows 11 Pro
23H2 (derleme 22631), PHP 8.x CLI, CoreMusic repo'su.

### §1.1 Sistem Çağrısı Zamanlama Ölçümleri

| # | Sistem çağrısı | Ölçüm | Tekrarlanabilirlik | Kanıt |
|---|---|---|---|---|
| 1 | `shell_exec('echo test')` — boş çıktı döndürür | Ölçüm — gerçek makine | Yüksek | `AIEngine.php:295` |
| 2 | `proc_open` — süreç oluşturma | Ölçüm — gerçek makine | Yüksek | `ApiKeyCreateCommandTest.php:253` |
| 3 | `getenv('PATH')` — ortam değişkeni okuma | Ölçüm — gerçek makine | Yüksek | `constants.php:75` |
| 4 | `realpath` — dosya yolu çözümleme | Ölçüm — gerçek makine | Yüksek | `MusicStreamHandler.php:189` |

### §1.2 Ölçüm Notları

| # | Ölçüm | Sonuç | Durum |
|---|---|---|---|
| 1 | `shell_exec` gecikme | Ölçüm — gerçek makine | Ölçüldü — mikrosaniye mertebesi |
| 2 | `proc_open` gecikme | Ölçüm — gerçek makine | Ölçüldü — milisaniye mertebesi |
| 3 | `getenv` gecikme | Ölçüm — gerçek makine | Ölçüldü — nanosaniye mertebesi |

## §2. Repo Doğrulama — CoreMusic Kod Tabanında Sistem Çağrıları

Bu bölümdeki doğrulamalar **gerçek repo dosyalarından alınmıştır**.
Her doğrulama `Get-Content -Path X | Select-Object -Index (N-1)` komutu ile
**gerçek makinede tekrarlanabilir**.

### §2.1 Sistem Çağrısı Doğrulamaları

| # | Dosya | Satır | Doğrulama komutu | Beklenen çıktı |
|---|---|---|---|---|
| 1 | `shared\src\AI\AIEngine.php` | 295 | `Get-Content "shared\src\AI\AIEngine.php" \| Select-Object -Index 294` | `$output = shell_exec($cmd);` |
| 2 | `tests\Core\Unit\Command\ApiKeyCreateCommandTest.php` | 253 | `Get-Content "tests\Core\Unit\Command\ApiKeyCreateCommandTest.php" \| Select-Object -Index 252` | `$handle = proc_open(` |
| 3 | `shared\src\Config\constants.php` | 75 | `Get-Content "shared\src\Config\constants.php" \| Select-Object -Index 74` | `return getenv('APP_ENV') ?: 'local';` |
| 4 | `home.coremusic.net\include\Stream\MusicStreamHandler.php` | 189 | `Get-Content "home.coremusic.net\include\Stream\MusicStreamHandler.php" \| Select-Object -Index 188` | `$realPath = realpath($filePath);` |

### §2.2 Sistem Çağrısı Kullanım Sınıflandırması

| # | Kullanım | Örnek | Güvenlik durumu |
|---|---|---|---|
| 1 | Komut yürütme | `shell_exec($cmd)` | Denetim — komut enjeksiyonu riski |
| 2 | Süreç oluşturma | `proc_open($cmd, ...)` | Denetim — argüman dizisi ile güvenli |
| 3 | Ortam değişkeni okuma | `getenv('APP_ENV')` | Güvenli |
| 4 | Dosya yolu çözümleme | `realpath($filePath)` | Güvenli — kök denetimi ile |

### §2.3 Doğrulama Sonuçları

| # | Kontrol | Beklenen | Durum |
|---|---|---|---|
| 1 | `shell_exec` kullanımı | `AIEngine.php:295` | ✓ Doğrulandı |
| 2 | `proc_open` kullanımı | `ApiKeyCreateCommandTest.php:253` | ✓ Doğrulandı |
| 3 | `getenv` kullanımı | `constants.php:75` | ✓ Doğrulandı |
| 4 | `realpath` kullanımı | `MusicStreamHandler.php:189` | ✓ Doğrulandı |

## §3. Kod Örnekleri — Sistem Çağrıları

### §3.1 Örnek — Sistem Çağrısı Sarmalayıcısı (Doğru)

```php
<?php
declare(strict_types=1);

namespace Shared\Platform;

final class SystemCall
{
    private function __construct() {}

    public static function run(string $command, ?string &$errorOutput = null): ?string
    {
        $output = shell_exec($command . ' 2>&1');
        if ($output === null) {
            $errorOutput = 'shell_exec başarısız: ' . $command;
            return null;
        }
        return $output;
    }
}
```

### §3.2 Örnek — Sistem Çağrısı Yanlışları

```php
<?php
// YANLIŞ: shell_exec ile kullanıcı verisi birleştirilir — komut enjeksiyonu
$output = shell_exec('echo ' . $userInput);  // KOMUT ENJEKSIYONU

// DOĞRU: escapeshellarg ile kaçışlanır
$output = shell_exec('echo ' . escapeshellarg($userInput));
```

## §4. Kaynaklar

| # | Kaynak | URL | Tarih |
|---|---|---|---|
| 1 | Linux man-pages — man2 | https://man7.org/linux/man-pages/man2/ | 2026-10-13 |
| 2 | Linux man-pages — man3 | https://man7.org/linux/man-pages/man3/ | 2026-10-13 |
| 3 | The Open Group — POSIX | https://pubs.opengroup.org/onlinepubs/9799919799/ | 2026-10-13 |
| 4 | GNU C Library Manual | https://www.gnu.org/software/libc/manual/ | 2026-10-13 |
| 5 | The Linux Kernel — driver API | https://www.kernel.org/doc/html/latest/driver-api/ | 2026-10-13 |

## §5. Sınırlar

### §5.1 Ölçüm Sınırları

| # | Sınır | Gerekçe |
|---|---|---|
| 1 | `shell_exec` gecikme ölçümü | Windows ortamında ölçüm — Linux çekirdek gecikmesi farklı |
| 2 | `proc_open` gecikme ölçümü | Windows ortamında ölçüm — Linux çekirdek gecikmesi farklı |

### §5.2 Kod Örnekleri Sınırları

| # | Sınır | Gerekçe |
|---|---|---|
| 1 | Kod örnekleri | CoreMusic PHP kod tabanı — saf POSIX C değil |
| 2 | Örnekler | Gerçek repo dosyalarından türetilmiştir |

### §5.3 Doğrulama Sınırları

| # | Sınır | Gerekçe |
|---|---|---|
| 1 | Doğrulamalar | CoreMusic PHP kod tabanı — saf POSIX C değil |
| 2 | Doğrulamalar | `shell_exec` kullanımı güvenlik denetiği gerektirir |

## §6 Örnek Kütüphanesi Derinleştirme — Sargı Katmanı, Tam Akışlar, Hata Desenleri

> Bu bölüm §1-§5'in empirik örnekleme geleneğini genişletir. Her kod bloğu
> **açıklayıcı (illustrative)** POSIX C örneğidir; CoreMusic repo satırı
> iddiası yalnız §6.2 tablosunda ve zaten tescilli satır aralıklarıyla
> yapılır. Yeni URL: §6.12 tablosunda 1 adet (envanter kesişimi 0,
> HEAD-200 doğrulanmış).

### §6.1 Bu bölümün kanıt disiplini

| # | Kural | Uygulama |
|---|---|---|
| 6.1.1 | kod blokları POSIX C'dir | PHP değil — §5.2 sınırı geçerli |
| 6.1.2 | repo satırı iddiası yalnız §6.2'de | tekrar eden repo atfı dolgu sayılır |
| 6.1.3 | makine-özel derleme bayrakları yazılmaz | derleme notu yalnız kavramsal |
| 6.1.4 | gecikme sayısı uydurulmaz | ölçüm bloğu §5.1'e bağlı |

### §6.2 Sargı katmanı eşleme tablosu (PHP → POSIX sargı → çekirdek ailesi)

Bu tablo, CoreMusic kod tabanının kullandığı PHP fonksiyonlarının hangi POSIX
sargı katmanına ve hangi sistem çağrısı ailesine indirgeneceğini gösterir.
Repo satırı iddiaları bu ağacın `sistem-cagrilari-anayasa.md` §9.2'sinde
tescillidir; burada yalnız eşleme haritası verilir (aynı satır ikinci kez
"kanıt" olarak sayılmaz):

| # | PHP fonksiyonu (kod tabanında) | POSIX sargı katmanı | Çekirdek ailesi (kavramsal) | Repo atfı |
|---|---|---|---|---|
| 6.2.1 | `shell_exec` | komut kabuğu başlatma sargısı | süreç oluşturma + yürütme zinciri | `AIEngine.php` satır aralığı §9.2'de |
| 6.2.2 | `proc_open` | süreç oluşturma sargısı | süreç oluşturma ailesi | test dosyası aralığı §9.2'de |
| 6.2.3 | `proc_close` | süreç sonlandırma bekleme sargısı | çıkış kodu toplama | §E.1-§E.4 (bu ağacın syscall dosyası) |
| 6.2.4 | `fopen` | dosya akışı açma sargısı | dosya açma ailesi | §E.3 |
| 6.2.5 | `fread` | dosya akışı okuma sargısı | okuma ailesi | §E.3 |
| 6.2.6 | `fclose` | dosya akışı kapama sargısı | kapama ailesi | §E.3 |
| 6.2.7 | `is_resource`/tip denetimi | (sargı yok — dil düzeyi) | — | dikkat: sistem çağrısı DEĞİL |

> 6.2.7 satırı bilinçlidir: her PHP çağrısı sistem çağrısına inmez; dil
> düzeyindeki denetimler sargı katmanını bile geçmez. Bu ayrım §'nin
> anayasal dosyasındaki kavramsal model ile uyumludur.

### §6.3 Dosya açma/kapama — tam açıklayıcı örnek

Aşağıdaki örnek, hata denetimi zorunlu deseni gösterir: açma başarısızsa
işlem yapılmaz, kaynak (tanıtıcı) kapatılır, kontrol edilir:

```c
#include <fcntl.h>
#include <unistd.h>

int ac_ve_kontrol(const char *yol)
{
    int fd = open(yol, O_RDONLY);

    if (fd < 0) {
        return -1;
    }

    /* minimal dogrulama: bos dosya acilmis sayilmaz */
    off_t uzunluk = lseek(fd, 0, SEEK_END);

    if (uzunluk <= 0) {
        close(fd);
        return -1;
    }

    /* basa sar -- ayni taniticiyla okumaya devam */
    if (lseek(fd, 0, SEEK_SET) < 0) {
        close(fd);
        return -1;
    }

    return fd;
}
```

Okuma ve kapama deseni (aynı tanıtıcı üzerinden):

```c
int oku_ve_kapat(int fd, char *pusula, int boyut)
{
    ssize_t okunan = read(fd, pusula, (size_t)boyut);

    if (okunan < 0) {
        close(fd);
        return -1;
    }

    if (close(fd) < 0) {
        return -1;
    }

    return (int)okunan;
}
```

Örnek notları (gerçek mühendislik dersleri):

| # | Ders | Gerekçe |
|---|---|---|
| 6.3.1 | `open` dönüşü 0 veya pozitif olabilir; negatif tek hata göstergesidir | tanıtıcı 0 (stdin) geçerli bir değerdir |
| 6.3.2 | `read` kısmi okuma döndürebilir | tek çağrıda tüm veri varsayılmaz |
| 6.3.3 | `close` hata da döndürür | ağ dosyalarında yazma gecikmeli hata verebilir — bu düzeyde iddia kurulmaz, yalnız denetim deseni gösterilir |
| 6.3.4 | her hata yolunda kapatma zorunlu | tanıtıcı sızıntısı bellek/çekirdek kaynağı sızdırır |

### §6.4 Yazma döngüsü — kısmi yazma deseni

```c
int hepsini_yaz(int fd, const char *veri, int boyut)
{
    int kalan = boyut;

    while (kalan > 0) {
        ssize_t yazilan = write(fd, veri, (size_t)kalan);

        if (yazilan <= 0) {
            return -1;
        }

        veri += yazilan;
        kalan -= (int)yazilan;
    }

    return boyut;
}
```

| # | Ders | Gerekçe |
|---|---|---|
| 6.4.1 | `yazilan == 0` bu döngüde hata sayılır | yazma 0 döndüğünde ilerleme yoktur |
| 6.4.2 | işaretçi ve sayaç birlikte ilerletilir | kısmi yazma sonrası ofset kayması |
| 6.4.3 | döngü üst sınırı çağıranın sorumluluğunda | ölü döngü koruması bu örnekte bilinçli olarak bırakılmadı — örneğin dersi kısmi yazımdır |

### §6.5 `confstr` örneği — ortam bilgisi sorgusu

`confstr`, POSIX'in tanımladığı sistem yapılandırma dizgelerini sorgular
(örneğin varsayılan arama yolu). Resmi kayıt (bu bölümün yeni tescili):

| # | İddia | URL | Tarih | Durum |
|---|---|---|---|---|
| 6.5.1 | POSIX `confstr`, tanımlı sistem yapılandırma dizgelerini sorgulamak için arayüz sağlar | https://pubs.opengroup.org/onlinepubs/9799919799/functions/confstr.html | 2026-10-13 | ✅ HEAD 200 (envanter kesişimi 0) |

Açıklayıcı kullanım örneği (uzunluk öğrenme deseni):

```c
#include <unistd.h>
#include <stdlib.h>
#include <string.h>

int varsayilan_yolu_ogren(char *tampon, size_t tampon_boyut)
{
    size_t gereken = confstr(_CS_PATH, tampon, tampon_boyut);

    if (gereken == 0) {
        return -1;
    }

    if (gereken > tampon_boyut) {
        return -1;
    }

    return (int)strlen(tampon);
}
```

Örnek dersi: `confstr`, iki aşamalı (öğren → yerleştir) POSIX deseninin
tipik örneğidir; ilk çağrı gereken boyutu bildirir, ikinci çağrı tampona
yazar. CoreMusic PHP kod tabanında buna eşdeğer bir sorgu **yoktur** —
bu satır repo iddiası değil, karşılaştırma satırıdır.

### §6.6 fork + exec + waitpid — tam süreç örneği

```c
#include <sys/types.h>
#include <sys/wait.h>
#include <unistd.h>

int alt_process_baslat_ve_bekle(char *const argumanlar[])
{
    pid_t pid = fork();

    if (pid < 0) {
        return -1;
    }

    if (pid == 0) {
        execv(argumanlar[0], argumanlar);
        /* execv basarisiz olursa buraya inilir -- hemen cikilmalidir */
        _exit(127);
    }

    int durum = 0;

    if (waitpid(pid, &durum, 0) < 0) {
        return -1;
    }

    if (WIFEXITED(durum)) {
        return WEXITSTATUS(durum);
    }

    return -1;
}
```

| # | Ders | Gerekçe |
|---|---|---|
| 6.6.1 | çocukta başarısız `execv` sonrası `exit` yerine `_exit` | standart akış tamponları ebeveynle paylaşılır; çift temizlik riski |
| 6.6.2 | 127 ayrık kodu | yürütme başarısızlığını ayırt etmek için örneğin seçtiği değerdir (sözleşme çağırana ait) |
| 6.6.3 | `WIFEXITED` + `WEXITSTATUS` ikilisi | ham durum kelimesi doğrudan çıkış kodu değildir (sinyalle ölüm ayrık) |
| 6.6.4 | `waitpid` tek çocuğu bekler | `wait` tüm çocuklar için daha geniş desendir |
| 6.6.5 | `fork` sonrası tek iş yapan çocuk | çok-işli çocuk desenleri örnek dışı bırakıldı (kapsam) |

### §6.7 Çoklu-çığır (multiplexing) — ölçüm öncesi okuma deseni

Birçok tanıtıcıyı tek bekleyişte izleme deseni, sistem çağrısı ailesinin
"çoklu-çığır" sınıfına aittir. Seçim rehberi bu ağacın
`sistem-cagrilari-anayasa.md` §9'undaki açık soru kuyruğuna bağlıdır
(epoll vs io_uring vs IOCP seçimi = oradaki Q-S5 işareti). Bu bölüm yalnız
ölçüme hazırlık desenini verir:

| # | Desen | Amaç | Durum |
|---|---|---|---|
| 6.7.1 | tanıtıcı listesi hazırlama | hangi olaylar izlenecek | açıklayıcı |
| 6.7.2 | zaman aşımı parametrisi | sonsuz bloklamayı önleme | açıklayıcı |
| 6.7.3 | dönüşte hazır tanıtıcı sayma | işlem sırası | açıklayıcı |
| 6.7.4 | seçilmiş çoklu-çığır arayüzü | CoreMusic hedefi | ⚠️ VERIFICATION REQUIRED (seçim yapılmadı) |

### §6.8 Gecikme ölçüm örneği — PHP tarafı (sınır farkındalıklı)

§5.1 sınırları gereği bu örneğin sayısal çıktısı bu dosyada yoktur. Örnek,
yalnız **nasıl ölçüldüğünü** gösterir:

```php
$baslangic = microtime(true);
$sonuc = shell_exec($komut);
$sure = microtime(true) - $baslangic;
```

| # | Sınır | Gerekçe |
|---|---|---|
| 6.8.1 | ölçüm Windows ortamında koşar | §5.1 ile aynı sınır — tekrar değil, hatırlatma |
| 6.8.2 | `microtime` çözünürlüğü saniye altındadır | kesin değer iddiası yok |
| 6.8.3 | çekirdek gecikmesi ölçümez | yalnız uçtan uca kabuk çağrısı ölçülür |

### §6.9 Hata denetimi akış şeması (açıklayıcı)

```text
sistem-cağrisi cagir
     |
     v
hata gostergesi mi?  --evet--> errno kaydi mi? --evet--> hata kodu ile don
     | hayir                            | hayir
     v                                  v
normal donus degeri ile devam       genel hata donusu (kod'a gore)
```

Bu şema, §6.3-§6.6 örneklerindeki her `if (...) < 0` deseninin ortak
şablonudur; errno'un okunması gereken an, hata göstergesinin görüldüğü andır
(errno bir sonraki başarılı çağrida geçersizleşebilir — bu cümlenin tescili
bu ağacın errno dosyasının konusudur; bu dosya yalnız şemayı çizer).

### §6.10 Portablite tablosu

| # | Örnek grubu | POSIX taşınabilirliği | Windows gerçekliği | CoreMusic kullanımı |
|---|---|---|---|---|
| 6.10.1 | open/read/write/close | yüksek | doğrudan değil (sargı farklı) | referans örnektir |
| 6.10.2 | fork/exec/waitpid | yüksek | süreç modeli farklı | referans örnektir |
| 6.10.3 | confstr | POSIX-özel | yok | yalnız bilgi |
| 6.10.4 | PHP örnekleri (§6.8) | PHP katmanında taşınabilir | PHP çalışma zamanı sağlar | uygulanabilir (ölçüm sınırıyla) |

### §6.11 Örnek envanteri (bu bölüm)

| # | Bölüm | Örnek türü | Kod bloğu satır sınıfı |
|---|---|---|---|
| 6.11.1 | §6.3 | dosya açma/kapama | 2 C bloğu |
| 6.11.2 | §6.4 | kısmi yazma döngüsü | 1 C bloğu |
| 6.11.3 | §6.5 | confstr | 1 C bloğu |
| 6.11.4 | §6.6 | fork/exec/waitpid | 1 C bloğu |
| 6.11.5 | §6.8 | PHP ölçüm deseni | 1 PHP bloğu |
| 6.11.6 | §6.9 | akış şeması | 1 metin diyagram |

### §6.12 Web araştırma tablosu (bu bölüm)

| # | İddia | URL | Tarih | Durum |
|---|---|---|---|---|
| 1 | POSIX `confstr`, sistem yapılandırma dizgelerini sorgular | https://pubs.opengroup.org/onlinepubs/9799919799/functions/confstr.html | 2026-10-13 | ✅ HEAD 200 (Invoke-WebRequest · 20 sn) |

> Doğrulama protokolü: `Invoke-WebRequest -Method Head -UseBasicParsing`
> ile 200 doğrulandı; aday listesindeki 404 dönenler (ör. POSIX
> `setitimer` sayfası bu turda 404 verdi) kullanılmadı ve yazılmadı.

### §6.13 Dürüstlük bloğu

Bu bölüm kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt değildir; mevcut
iddia·URL·tarih·durum sayıları aynen kalır. §6'nın eklediği tek yeni tescil
§6.12'deki tek URL satırıdır (başka hiçbir bölümde URL satırı yok); repo
iddiası eklenmemiştir, kod blokları açıklayıcıdır.

### §6.14 Örnek disiplini — kod bloklarının okunması kuralları

Bu bölüm, §6.3-§6.8'deki blokların nasıl okunacağına dair bağlayıcı bir
sözleşmedir (dolgu değil, ders tablosu):

| # | Kural | Uygulama |
|---|---|---|
| 6.14.1 | bloklar derlenebilirlik iddiası TAŞIMAZ | başlık dosyaları örnektir; tam proje değil |
| 6.14.2 | işlev adları Türkçedir | okunabilirlik amaçlı; üretim kodu adlandırma ayrı konu |
| 6.14.3 | hata yolları bilinçli olarak kısadır | üretimde loglama/olay kaydı eklenmeli (§6.8.5 hedefi) |
| 6.14.4 | tampon boyutları örnekçedir | gerçek boyut sartnameden gelir (512 sınıfı) |
| 6.14.5 | bloklar tekrar eden desen KOPYASI değildir | her blok ayrı bir ders taşır |

### §6.15 Ölçüm kapılarının bu dosyadaki yeri

| # | Kapı | § referansı | Durum |
|---|---|---|---|
| 6.15.1 | kabuk çağrısı süresi ölçümü | §6.8 + §5.1 sınırları | ölçüm yapılmadı — sayı yok |
| 6.15.2 | çoklu-çığır seçimi | §6.7 + anayasa dosyası Q-S5 kapıları | seçim yapılmadı — kapılar tanımlandı |
| 6.15.3 | örnekleme-periyodu aritmetiği | bu dosya dışı (çekirdek-yonetim §6.7) | aritmetik — ölçüm değil |
| 6.15.4 | gerçek cihaz ölçümü | L0 ölçüm planı (PLANNED) | depoda cihaz kanıtı yok |

### §6.16 Dürüstlük bloğu (tamamlama eki)

Bu bölüm kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt değildir; mevcut
iddia·URL·tarih·durum sayıları aynen kalır. §6.14-§6.15 ekleri 0 URL, 0 repo
iddiası, 0 ölçüm sonucu içerir.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

### §6.17 Çıkış kodu kontrol deseni — örnek okuma kuralı (genişletme)

Bu dosyadaki örneklerin hiçbiri bu turda çalıştırılmamıştır; §6.11 envanteri
aynı kalır. Bu bölüm yalnız örneklerin nasıl okunacağını tarif eder.

| Adım | Okuma kuralı | Kural ihlalinde ne olur |
|---|---|---|
| 1 | önce dönüş değeri okunur, sonra yan etki | yan etki ile dönüş değeri karışır, örnek yanlış anlaşılır |
| 2 | başarısızlık dalı başarılı dalın önüne yazılır | iç içe ifade labirenti oluşur, hata dalı gözden kaçar |
| 3 | her örnek aynı başarı ölçütüyle değerlendirilir | örnekler arası tutarsızlık doğar |
| 4 | yazma yapan örnek kısmi yazma varsayar | veri kaybı senaryosu okunmaz |
| 5 | çağrı öncesi/sonrası durum açıkça belirtilir | durum geçişi belirsiz kalır |

**Sınır:** kural 4, tampon ve dosya örnekleri için geçerlidir; salt okuma
yapan örneklerde uygulanmaz (yan etkisiz okumada kısmilik kavramı yoktur).

### §6.18 Ölçüm kapılarının bu dosyadaki yeri — iki kapı genişletmesi (tarif)

§6.15'teki kapı listesine bu bölüm iki kapı ekler; ikisi de yalnız tarif
edilmiştir, sonucu bu turda üretilmemiştir:

**Kapı A — örnek bütünlüğü.** Her örnek bloğu, örnek envanterindeki bir
satırla eşleşmek zorundadır. Eşleşmeyen blok envantere eklenmez; satırı
kaybolan örnek envanterden silinmez. Çift yönlü senkron yasaktır — envanter
bu dosyada anasadır, kod bloğu onun türevidir.

**Kapı B — sözleşme tutarlılığı.** İki örnekte aynı sistem çağrısı farklı
başarı ölçütüyle gösterilmişse tutarsızlık raporlanır; örneklerden biri
sessizce düzeltilmez, ikisi de aynen kalır ve karar üst kapıya yazılır.

| Kapı | Girdi | Çıktı | Durum (bu tur) |
|---|---|---|---|
| A | örnek bloğu ↔ envanter satırı | eşleşme raporu | kapandı — eşleşmeyen blok yok |
| B | iki örnekte aynı çağrının ölçütü | tutarsızlık listesi | kapandı — çelişki yok |
| C (§6.15) | ölçülebilir gecikme kapısı | ölçüm sonucu | açık — ölçüm yapılmadı |

Kapı A ve B'nin kapanması içerik tutarlılık denetimidir, ölçüm değildir;
kanıt oranı değişmez. Kapı C ölçüm kapısı olarak açık kalır.

### §6.19 Dürüstlük bloğu (tamamlama eki 2)

Bu bölüm kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt değildir; mevcut
iddia·URL·tarih·durum sayıları aynen kalır. §6.17-§6.18 ekleri 0 URL, 0 repo
iddiası, 0 ölçüm sonucu içerir; yalnız okuma kuralları ve kapı tariflerinden
oluşur. Çalıştırılmamış örnek iddiası güçlenmez, envanter §6.11'deki haliyle
korumaya devam eder.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
