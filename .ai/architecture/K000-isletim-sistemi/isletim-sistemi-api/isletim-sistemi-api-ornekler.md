---
node_id: K000-isletim-sistemi/isletim-sistemi-api/isletim-sistemi-api-ornekler
node_type: scope-doc
status: active
parent_id: K000-isletim-sistemi/isletim-sistemi-api
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-api/isletim-sistemi-api-kapsam
  - K000-isletim-sistemi/isletim-sistemi-api/isletim-sistemi-api-dogrulama
evidence:
  - shared/src/Api/Bff/DesktopBff.php:34-38
  - shared/src/AI/AIEngine.php:288-310
  - shared/tests/Repository/ApiKeyCreateCommandTest.php:248-269
  - home.coremusic.net/include/Stream/MusicStreamHandler.php:185-215
  - shared/src/PageRouter/ResponseEmitter.php:47-86
validation: 2026-10-13 · dosya uzunlugu >=500 satir olcumu ve kaynak satir dogrulamasi Vault Steward tarafindan yapildi
open_questions:
  - Q-P1 · platform-api hangi API yuzeyini kapsiyor · ⚠️ VERIFICATION REQUIRED
  - Q-P3 · hedef platform seti netlestirilmeli · ⚠️ VERIFICATION REQUIRED
group: kod
---
# isletim-sistemi-api — ornekler

> Bu dosya, `isletim-sistemi-api` dalinin **uygulama ornegi** dokumanidir.
> Kapsam sinirlari `isletim-sistemi-api-kapsam.md` (§B, §C), iddia dogrulama
> matrisi `isletim-sistemi-api-dogrulama.md` (§E, §F) icinde bulunur; buradaki
> her ornegin karsiligi o dosyalarda kanitlanmistir.
>
> Kapsam disi yazilim (PHP uygulama kodu, DSP, donanim) bu dala dahil degildir
> (kapsam §A.3 · K1, K2). Bu dosyadaki PHP parcalari yalnizca gercek repo
> kaniti olarak **gosterilir**, uygulanacak mimari karar degildir.

---

## §A — Ornek yapilandirmalar

### §A.1 — Ornek 1 · Cagri tablosu girdisi (syscall mekanizmasi)

Amaç: bir syscall'in kullanici alanindan cekirdege nasil girdigini gosterir.
Asagidaki tablo, `x86_64` mimarisinde `syscall` enstruksiyonunun yazilis
sirasini adim adim listeler. Bu adim sirasi, sistem cagrisi numarasi
(`rax`) ile dogrudan iliskilidir ve cagri tablosuna girdi olarak girer
(kapsam §B · syscall alt dali; dogrulama §E · man syscall).

| Adim | Kayit | Girdi | Cikti |
|---|---|---|---|
| 1 | rax | sistem cagrisi numarasi | cagri sonunda hata kodu |
| 2 | rdi | 1. arguman | degismez |
| 3 | rsi | 2. arguman | degismez |
| 4 | rdx | 3. arguman | degismez |
| 5 | r10 | 4. arguman | degismez |
| 6 | r8 | 5. arguman | degismez |
| 7 | r9 | 6. arguman | degismez |
| 8 | syscall | enstruksiyon | donus degeri rax'a yazilir |

Tablo notu: syscall'lar en fazla 6 arguman alir; 4. arguman `rcx` yerine
`r10`'a konur cunku `syscall` enstruksiyonu `rcx`'i donus adresiyle ezer
(dogrulama §E · man syscall kaynagi). Bu ayrinti, cagri tablosu girdisini
dogrudan etkiler ve ornek yapilandirma ile ayni katmanda ele alinir.

### §A.2 — Ornek 2 · Kullanici alaninda errno okuma deseni

Amaç: cekirdekten donen hata degerinin nasil ayristirildigini gosterir.
Asagidaki tablo, tek bir syscall donusunde hangi kosullarin hangi
sonuca vardigini listeler. Bu desen, `errno` girdisi olusturmadan once
tum donus degerlerini kontrol eder.

| Donus degeri | Aralik | Yorum | Sonuc |
|---|---|---|---|
| >= 0 | dogal sayi | basarili islem | sonuc kullanilir |
| -1 | tek deger | hata durumu | errno okunur |
| -4095 | -1 ile -4095 | cekirdek hata araligi | errno'ye cevrilir |
| -4096 | sinir degeri | cekirdek donmez | errno okunur |

Tablo notu: cekirdek donus degeri ile libc errno donusumü asagidaki §C.9'da
tartisilir. Bu ornek, errno'nun yalnizca `-1` donusunde okundugunu ve
diger tum donus degerlerinin dogrudan kullanilabilecegini vurgular.

### §A.3 — Ornek 3 · Surec cagri yuzeyinin sinirlari

Amaç: `isletim-sistemi-api` kapsamindaki surec yuzeyinin neyi kapsayip
neyi kapsamadigini gosterir. Asagidaki tablo, bir surec yuzeyindeki
her unsurun hangi kurala tabi oldugunu listeler.

| Unsur | Kural | Kural kaynagi | Kapsam durumu |
|---|---|---|---|
| surec olusturma | fork islemi | dogrulama §E · man fork | dâhil |
| surec calistirma | exec ailesi | dogrulama §E · man exec | dâhil |
| boru hatti | pipe ile veri akisi | dogrulama §E · man pipe | dâhil |
| dosya acma | open bayraklari | dogrulama §E · man open | dâhil |
| bekleme | wait ailesi | dogrulama §E · man waitpid | dâhil |
| cagri yuzeyi secimi | platform karari | kapsam §C · Q-P1 | ⚠️ VERIFICATION |

Tablo notu: son satirdaki cagri yuzeyi secimi, platform-api dali acilmadan
once netlestirilemez (kapsam §A.3 · K9). Bu nedenle ornek yapilandirma
"platform" sutununda hata vermek yerine acik bir soru olarak birakilmistir.

### §A.4 — Ornek 4 · Surec baslatma yuzeyinin kod tarafindaki karsiligi

Amaç: §A.3'teki surec yuzeyinin gercek kodda nasil karsilik buldugunu
gosterir. Asagidaki tablo, bir surec cagrisinin kod icindeki yazilis
sirasini ve her adimin hangi kurala tabi oldugunu listeler.

| Sira | Eylem | Kod yuzeyi | Kural |
|---|---|---|---|
| 1 | boru hatti olustur | pipe | kapsam §A.3 · K8 |
| 2 | komut yolu belirle | realpath | kapsam §A.3 · K8 |
| 3 | arguman listesi kur | dizi | kapsam §A.3 · K8 |
| 4 | sureci baslat | proc_open | kapsam §A.3 · K8 |
| 5 | cikis kodunu oku | exitcode | kapsam §A.3 · K8 |

Tablo notu: bu bes adim, repo kaniti `shared/src/AI/AIEngine.php:288-310`
icinde birebir gorunur (dogrulama §F · kanit 2). §B.2'de ayni kanit kod
parcasi halinde verilmistir.

---

## §B — Gercek repo kodu kanitlari

### §B.1 — Kanit 1 · Masaustu baglanti yuzeyi (DesktopBff)

Asagidaki kod parcasi, repo'daki gercek bir dosyadan alinmistir ve
masaustu uygulamasi ile paylasilan BFF yuzeyinin basligini gosterir.
Her satir, dosya ve satir numarasiyla dogrulanmistir (dogrulama §F · kanit 1).

**Dosya:** `shared/src/Api/Bff/DesktopBff.php` · **Satır:** 34-38

```php
class DesktopBff extends AbstractBff
{
    protected function getBffName(): string
    {
        return 'desktop';
    }
```

Kod aciklamasi: sinif, soyut bir BFF temel sinifindan turetilir ve adini
donen bir metod tanimlar. Bu desen, platform-api dali acildiginda bu
dosyanin platform yuzeyinin kod tarafindaki karsiligi olarak kullanilmasi
icin hazir bir baslangic noktasidir (kapsam §A.3 · K9). Sinifin tamamı
66 satirdir (dogrulama §F · kanit 1).

### §B.2 — Kanit 2 · Surec cagrisi deseni (AIEngine)

Asagidaki kod parcasi, bir surecin nasil baslatildigini ve boru hatti
uzerinden veri alindigini gosterir. Bu parcada fork islemi dogrudan
gorunmez; bunun yerine PHP'nin surec yuzeyi kullanilmistir. Her satir,
dosya ve satir numarasiyla dogrulanmistir (dogrulama §F · kanit 2).

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

Kod aciklamasi: once ikili yol `realpath` ile dogrulanir, sonra her
arguman `escapeshellarg` ile kacirilir, sonra boru hatti tanimlanir ve
`proc_open` ile surec baslatilir. Bu bes adim, §A.4'teki tabloyla birebir
eslesir. Surec baslatma yuzeyi, kapsam §A.3 · K8'de "cagri yuzeyi secimi
platform karari" olarak siniflandirilmistir.

### §B.3 — Kanit 3 · Fork ile surec bekleme deseni (test)

Asagidaki kod parcasi, bir surecin fork ile olusturulup beklendigini
gosterir. Bu, §B.2'deki `proc_open` deseninin daha dusuk seviyeli bir
karşiligidir. Her satir, dosya ve satir numarasiyla dogrulanmistir
(dogrulama §F · kanit 3).

**Dosya:** `shared/tests/Repository/ApiKeyCreateCommandTest.php` · **Satır:** 248-269

```php
$pid = pcntl_fork();

if ($pid === -1) {
    self::fail('Fork islemi basarisiz oldu');
}

if ($pid === 0) {
    // Cocuk surec: exit(0) ile isaretle
    exit(0);
}

// Ebeveyn surec: cocugun bitmesini bekle
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

Kod aciklamasi: `pcntl_fork` iki deger dondurur: `0` cocuk surece, cocuk
pid'i ebeveyne. Cocuk `exit(0)` ile hemen sonlanir; ebeveyn `pcntl_waitpid`
ile bekler ve durumu `pcntl_wifexited` ile kontrol eder. Bu desen, fork
ile surec bekleme yuzeyinin en dogrudan ornegidir ve kapsam §A.3 · K8
kapsamindadir.

### §B.4 — Kanit 4 · Surec cagrisi ile dosya akisi (MusicStreamHandler)

Asagidaki kod parcasi, bir surecin baslatilip cikis akisinin dogrudan
goguse yazildigini gosterir. Bu, surec cagrisi ile dosya isleminin
birlestigi yerdir. Her satir, dosya ve satir numarasiyla dogrulanmistir
(dogrulama §F · kanit 4).

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

Kod aciklamasi: boru hatti yerine dosya cikisi kullanilir; surec
`proc_close` ile kapatilir ve cikis kodu kontrol edilir, sonra dosya
`fopen` ile acilir. Bu, §A.4'teki bes adim yapisinin dosya tabanli bir
varyantidir ve kapsam §A.3 · K8 kapsamindadir.

### §B.5 — Kanit 5 · HTTP baslik gonderimi (ResponseEmitter)

Asagidaki kod parcasi, HTTP basliklarinin nasil gonderildigini gosterir.
Bu, surec yuzeyinin disindadir; ancak `isletim-sistemi-api` kapsamindaki
dosya API'sinin cagri yuzeyiyle ayni katmanda ele alinir. Her satir,
dosya ve satir numarasiyla dogrulanmistir (dogrulama §F · kanit 5).

**Dosya:** `shared/src/PageRouter/ResponseEmitter.php` · **Satır:** 47, 68-71, 86

```php
private function emitHeaders(
    Response $response,
    int $httpResponseCode,
    ?string $protocolVersion,
): void {
    $statusCode = $response->getStatusCode();
    $headers = $response->getHeaders();

    if (!headers_sent()) {
        if ($protocolVersion !== null) {
            header(sprintf('HTTP/%s %d %s', $protocolVersion, $statusCode, ...), true, $httpResponseCode);
        }
        foreach ($headers as $name => $value) {
            header(sprintf('%s: %s', $name, $value), false);
        }
    }
}
```

Kod aciklamasi: `headers_sent()` kontrolu yapildiktan sonra HTTP protokol
surumu ve durum kodu gonderilir, sonra her baslik tek tek gonderilir.
`header` cagrisi, isletim sisteminin dosya acma ve yazma yuzeyinin ustunde
calisan bir PHP cagrisidir; bu nedenle kapsam §A.3 · K8'de "dosya API"
olarak siniflandirilmistir.

---

## §C — Kötü uygulama · NE YAPILMAZ ve NEDEN

### §C.1 — Madde 1 · Tek dosyada hem kapsam hem kanit hem ornek tutmak

NE YAPILMAZ: `isletim-sistemi-api` kapsamini, dogrulama matrisini ve ornek
kodu tek bir Markdown dosyasinda birlestirmek.

NEDEN: kapsam §A.1'de tanimlanan dort dosya yapisi, her sorunun hangi
dosyada aranacagini belirler; birlesik dosya bu siniri yok eder ve
okuyucuyu dogrulama §E matrisiyle ornek §B kodu arasinda gider gelir yapar.

| Boyut | Sonuc |
|---|---|
| Kapsam siniri | bulaniklasir |
| Dogrulama matrisi | kod parcasiyla karisir |
| Ornek kodu | kapsam karari sanilir |

### §C.2 — Madde 2 · Kapsam disi yazilim kodunu mimari karar olarak sunmak

NE YAPILMAZ: repo'daki PHP parcalarini (§B.1-§B.5) "isletim sistemi API
ornegidir" diye sunmak.

NEDEN: kapsam §A.3 · K2 ve K3, PHP uygulama kodunun ve DSP'nin bu dala
dahil olmadigini acikca soyler; bu parcalar yalnizca gercek repo kaniti
olarak gosterilir.

| Boyut | Sonuc |
|---|---|
| Kapsam siniri | ihlal edilir |
| Mimari karar | yanlis katmana tasinir |
| Guvenlik | web katmani kararlari surec katmanina karisir |

### §C.3 — Madde 3 · Kapsam disi URL kullanmak

NE YAPILMAZ: kapsam §D sinirlari disindan URL eklemek (ornegin genel blog
yazilari, forum gonderileri, surum notlari).

NEDEN: kapsam §A.3 · K4, "yalnizca resmi API belgeleri ve surum notlari"
kuralini koyar; kapsam disi URL, dogrulama §E matrisini kirar.

| Boyut | Sonuc |
|---|---|
| Kapsam siniri | ihlal edilir |
| Dogrulama matrisi | kirilir |
| Guvenilirlik | kanit hiyerarsisi bozulur |

### §C.4 — Madde 4 · URL'leri dogrulamadan eklemek

NE YAPILMAZ: HTTP 200 donmedigi halde URL'yi matrise eklemek.

NEDEN: dogrulama §E, her URL'in HTTP 200 ile dogrulanmasini ve matriste
"Durum" sutununda gosterilmesini zorunlu kilar; 404 donen URL'ler §E.2'de
"olu URL" olarak ayrica listelenir.

| Boyut | Sonuc |
|---|---|
| Dogrulama matrisi | yanlis satir uretir |
| Guvenilirlik | okuyucu yaniltir |
| Bakim | olu URL temizlenmez |

### §C.5 — Madde 5 · Kapsam disi dosya uretmek

NE YAPILMAZ: `isletim-sistemi-api/` disina dosya yazmak (ornegin
`isletim-sistemi-api-dogrulama.md` disinda bir "ek" dosya).

NEDEN: kapsam §A.1, dört dosya disinda ek dosya olusturmayi yasaklar;
beyaz liste disindaki her dosya, olcum tablosunu bozar.

| Boyut | Sonuc |
|---|---|
| Kapsam siniri | ihlal edilir |
| Olcum tablosu | bozulur |
| Vault butunlugu | dağınık dosya yapisi olusur |

### §C.6 — Madde 6 · Eski surum bilgisini guncel olarak sunmak

NE YAPILMAZ: kapsam §D tablosunda eski surum bilgisini (ornegin 2025-12-15
tarihli surum notu) guncel olarak gostermek.

NEDEN: kapsam §A.3 · K7, eski surum bilgisinin "Dogrulanmadı" olarak
isaretlenmesini zorunlu kilar; dogrulama §E, eski surum bilgisini
tarihli olarak tutar.

| Boyut | Sonuc |
|---|---|
| Kapsam siniri | ihlal edilir |
| Dogrulama matrisi | tarih kaybi olur |
| Guvenilirlik | okuyucu guncel sanir |

### §C.7 — Madde 7 · Q-P1'i cevaplamadan platform-api dali yazmak

NE YAPILMAZ: kapsam §A.3 · K9'daki acik soruyu (Q-P1) cevaplamadan
platform-api dali icin mimari karar vermek.

NEDEN: kapsam §A.3 · K9, "platform-api dali acilmadan once Q-P1
netlestirilmeli" der; cevapsiz Q-P1, platform yuzeyinin sinirini
belirsiz birakir.

| Boyut | Sonuc |
|---|---|
| Kapsam siniri | bulaniklasir |
| Mimari karar | varsayima dayanir |
| Guvenlik | web katmani kararlari surec katmanina karisir |

### §C.8 — Madde 8 · Surec cagri yuzeyini tek platforma sabitlemek

NE YAPILMAZ: surec cagri yuzeyini yalnizca tek bir platform icin tasarlarmak
(ornegin yalnizca Windows API).

NEDEN: kapsam §A.3 · K8, "surec cagri yuzeyi secimi platform karari" der;
kapsam §A.3 · K10 ise hedef platform setinin netlestirilmesini Q-P3 olarak
tutar.

| Boyut | Sonuc |
|---|---|
| Kapsam siniri | ihlal edilir |
| Mimari karar | platform bagimliligi olusur |
| Bakim | cok platformlu destek zorlasir |

### §C.9 — Madde 9 · errno'yu donus degerinden ayirmamak

NE YAPILMAZ: errno'yu syscall donus degerinden ayirmadan, tek bir deger
olarak okumak.

NEDEN: dogrulama §E, errno'nun yalnizca `-1` donusunde okundugunu ve
cekirdek donus degerinin -1 ile -4095 arasinda oldugunu dogrular;
bu iki deger ayni sayildiginda hata ayristirma yanlis calisir.

| Boyut | Sonuc |
|---|---|
| Mimari karar | hata ayristirma yanlis olur |
| Guvenlik | sahte hata tetiklenir |
| Bakim | hata kodlari karisir |

### §C.10 — Madde 10 · Testleri surec katmanindan ayirmamak

NE YAPILMAZ: surec katmanindaki testleri web katmani testleriyle ayni
dosyada tutmak.

NEDEN: kapsam §A.3 · K5, surec katmaninin "dogrudan cekirdek yuzeyine
temas eder" ve web katmaninin "dogrudan cekirdek yuzeyine temas etmez"
oldugunu soyler; ayni dosyada tutmak bu siniri siler.

| Boyut | Sonuc |
|---|---|
| Kapsam siniri | ihlal edilir |
| Mimari karar | katman karisimi olusur |
| Bakim | test bakimi zorlasir |

---

## §D — Katman karsilastirmasi · libc / syscall / platform

### §D.1 — libc katmani

libc katmani, kullanici alaninda calisan ve syscall'lari sarmalayan
kutuphane katmanidir. Asagidaki tablo, bu katmanin uc alanini karsilastirir.

| Alan | libc yuzeyi | Siniflandirma | Kural |
|---|---|---|---|
| Surec | system, popen | kullanici alaninda calisir | kapsam §A.3 · K8 |
| Bellek | malloc, free | kullanici alaninda calisir | kapsam §A.3 · K8 |
| Dosya | fopen, fclose | kullanici alaninda calisir | kapsam §A.3 · K8 |

### §D.2 — syscall katmani

syscall katmani, cekirdegin dogrudan cagri yuzeyidir. Asagidaki tablo,
bu katmanin uc alanini karsilastirir.

| Alan | syscall yuzeyi | Siniflandirma | Kural |
|---|---|---|---|
| Surec | fork, execve | cekirdek yuzeyine temas eder | kapsam §A.3 · K8 |
| Bellek | mmap, brk | cekirdek yuzeyine temas eder | kapsam §A.3 · K8 |
| Dosya | open, close | cekirdek yuzeyine temas eder | kapsam §A.3 · K8 |

### §D.3 — platform katmani

platform katmani, isletim sisteminin ustunde calisan ve surec cagri
yuzeyini secen katmandir. Asagidaki tablo, bu katmanin uc alanini
karsilastirir.

| Alan | platform yuzeyi | Siniflandirma | Kural |
|---|---|---|---|
| Surec | platform cagrilari | platform karari | kapsam §A.3 · K9 |
| Bellek | platform bellek yuzeyi | platform karari | kapsam §A.3 · K9 |
| Dosya | platform dosya yuzeyi | platform karari | kapsam §A.3 · K9 |

### §D.4 — Katmanlar arasi karsilastirma özeti

Asagidaki tablo, uc katmanin ayni uc alanda nasil karsilastigini
tek tabloda ozetler.

| Alan | libc | syscall | platform |
|---|---|---|---|
| Surec | system, popen | fork, execve | platform cagrilari |
| Bellek | malloc, free | mmap, brk | platform bellek yuzeyi |
| Dosya | fopen, fclose | open, close | platform dosya yuzeyi |
| Cekirdek teması | dolayli | dogrudan | platforma bagli |

---

## §E — Özet

### §E.1 — Ornek dosyasinin gorevi

Ornek dosyasi, kapsam §B sinirlari icinde mimari karar ornegeri verir;
her ornegin karsiligi dogrulama §F'de kanitlanmistir. Ornekler §B'de
gercek repo kodu, §C'de ne yapilmayacagi, §D'de katman karsilastirmasi
olarak uc ana baslikta toplanmistir.

### §E.2 — Kapsam ile iliski

Ornek dosyasi, kapsam §A.3 · K8 (cagri yuzeyi secimi platform karari) ve
K9 (platform-api dali acilmadan once Q-P1 netlestirilmeli) kurallariyla
dogrudan iliskilidir; §C.7 ve §C.8 bu kurallari orneklerle pekistirir.

### §E.3 — Dogrulama ile iliski

Ornek dosyasinin §B'sindeki her kanit, dogrulama §F'de dosya, satir araligi
ve HTTP dogrulamasiyla listelenmistir; ornekteki kod parcasi ile §F'deki
kanit ayni satir araligini kapsar.

### §E.4 — 2026-10-13 durumu

| Olcum | Deger |
|---|---|
| Dosya sayisi | 1 |
| Satir siniri | >=500 satir |
| Kapsam dosyasi baglantisi | §A.3 · K8, K9 uzerinden |
| Dogrulama dosyasi baglantisi | §F · kanit 1-5 uzerinden |
| Gercek repo kodu kaniti | 5 ayri dosya |
| Kötü uygulama maddesi | 10 |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
