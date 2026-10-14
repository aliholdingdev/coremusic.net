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
validation: 2026-10-14 · dosya uzunlugu >=500 satir olcumu ve kaynak satir dogrulamasi Vault Steward tarafindan yapildi · §F sistem grupli ornekler + §G PHP grubu (17 php.net URL HEAD-200 · 5 VAR + 12 YENI · repo kaniti ApiKeyCreateCommandTest.php:248-269) + §I diger diller (Python/Go/Rust/C#/Node.js · 17 URL HEAD-200 · 17 YENI · kesisim 0 · dogrulama §U) · 4. tur: §H sistem-gruplu örnek haritası (ağaç geneli · 8 yeni URL · 196 URL HEAD turu) + §G.7 PHP syscall aileleri eki (2026-10-14 · 22 URL HEAD-200 · 6 VAR + 16 YENI · repo kaniti 7 satir)
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

---

## §F — Sistem bazlı gruplu örnekler (2026-10-14)

> Bu bölüm, her API çağrısının **hangi sistem grubunda** karşılık bulduğunu
> gösterir. Gruplama eksenidir: Linux/POSIX · Windows · macOS · Android.
> Her kod bloğu, §F.6'daki kanıt tablosunda listelenen resmi sayfalarla
> (2026-10-14 · HTTP 200) doğrulanmıştır. Bloklar derlenebilirlik iddiası
> TAŞIMAZ — çağrı-imzası ve hata-modeli gösterimi amaçlıdır. Detayı olan
> gruplar kendi alt-dal dosyasında genişletilir (§F.0 sütunu).

### §F.0 — Gruplama özeti

| Grup | Birincil API yüzeyi | Örnek | Detay dosyası |
|---|---|---|---|
| Linux/POSIX | syscall + libc sarmalayıcı | §F.1 (C) · §B.2-§B.4 (PHP) | `syscall/` · `sistem-cagrilari/` |
| Windows | Win32 başlık yüzeyi (`windows.h` ailesi) | §F.2 (özet) | `platform-api/windows-api-yuzeyi.md` §12 |
| macOS | POSIX + Apple özel yüzey | §F.3 (kaynak tablosu) | `platform-api/` (Q-P3 açık) |
| Android | POSIX + NDK yüzeyi | §F.4 (kaynak tablosu) | `platform-api/` (Q-P3 açık) |
| Ağ (tüm gruplar) | soket API'si (POSIX ↔ Winsock2) | `ag-api/` | `ag-api/posix-socket-ve-winsock-karsilastirmasi.md` §F |
| Cihaz (tüm gruplar) | `ioctl` ↔ `DeviceIoControl` | `cihaz-erisimi/` | `cihaz-erisimi/cihaz-erisimi-anayasa.md` §10.23 |

### §F.1 — Linux/POSIX grubu · dosya turu (C)

```c
#include <fcntl.h>
#include <unistd.h>
#include <errno.h>
#include <stdio.h>
#include <string.h>

int fd = open("/tmp/ornek.bin", O_WRONLY | O_CREAT | O_TRUNC, 0644);
if (fd == -1) {
    /* errno TEK KEZ okunur ve hemen strerror'e geçilir (§C.9 · errno ayrı okunur) */
    fprintf(stderr, "open: %s\n", strerror(errno));
    return 1;
}

const char *veri = "merhaba";
ssize_t n = write(fd, veri, 7);
if (n == -1) {
    fprintf(stderr, "write: %s\n", strerror(errno));
    close(fd);                     /* hata yolu da kapatır — evre eşleşmesi */
    return 1;
}
/* not: 0 <= n < 7 KISMİ yazma da başarıdır — tam yazma varsayılmaz */

if (fsync(fd) == -1) {             /* diske fiziksel yazma — Windows FlushFileBuffers karşılığı */
    fprintf(stderr, "fsync: %s\n", strerror(errno));
}

close(fd);                         /* her başarı yolunda kapanır */
```

Okuma kuralları: `open` `-1` döner ve `errno` anında okunur; `write` kısmi
yazma yapabilir (tam yazma varsayılmaz); `close` hata yolu dâhil her yolun
sonundadır. PHP yüzeyinin karşılığı §B.2'deki `proc_open` kanıtıdır
(`php.net` proc-open sayfası 2026-10-14 HTTP 200).

### §F.2 — Windows grubu · özet

Windows grubunun kod örnekleri `platform-api/windows-api-yuzeyi.md` §12'dedir
(2026-10-14 · bu oturumda eklendi). Özet eşleme:

| İş | Windows çağrısı | POSIX karşılığı | Detay |
|---|---|---|---|
| dosya açma | `CreateFileW` | `open` | windows-api-yuzeyi §12.1 |
| yazma / okuma | `WriteFile` / `ReadFile` | `write` / `read` | windows-api-yuzeyi §12.1 |
| diske yazma | `FlushFileBuffers` | `fsync` | windows-api-yuzeyi §12.1 |
| kapatma | `CloseHandle` | `close` | windows-api-yuzeyi §12.1 |
| hata okuma | `GetLastError` (anlık) | `errno` (değişken) | windows-api-yuzeyi §12.2 |
| cihaz kontrolü | `DeviceIoControl` | `ioctl` | windows-api-yuzeyi §12.3 |
| COM başlatma | `CoInitializeEx` | — (Windows'a özgü) | windows-api-yuzeyi §11.3 |

### §F.3 — macOS grubu · kaynak tablosu

| Yüzey | Ne sağlar | Resmi kaynak (2026-10-14) | Durum |
|---|---|---|---|
| File System Events | dizin/değişim izleme | https://developer.apple.com/documentation/coreservices/file_system_events | ✅ HTTP 200 (HEAD) |
| AudioToolbox | ses servisi yüzeyi | https://developer.apple.com/documentation/audiotoolbox | ✅ HTTP 200 (HEAD) |
| kqueue | olay kuyruğu (BSD ailesi) | https://man.freebsd.org/cgi/man.cgi?query=kqueue&sektion=2 | ✅ HTTP 200 (HEAD) · FreeBSD üzerinden doğrulandı |

> macOS'un POSIX çağrılarını (`open` · `write` · `fork` …) da sağladığı iddiası
> bu turda macOS'e özgü resmi bir sayfayla doğrulanmadı — ⚠️ VERIFICATION
> REQUIRED (Q-P3 hedef platform setiyle birlikte Vault Steward'a). `fsevents`
> eski yolu 2026-10-14'te 404 döndü; `file_system_events` yolu kullanılmıştır
> (`isletim-sistemi-api-dogrulama.md` §S.2).

### §F.4 — Android grubu · kaynak tablosu

| Yüzey | Ne sağlar | Resmi kaynak (2026-10-14) | Durum |
|---|---|---|---|
| NDK ana sayfası | native (C/C++) geliştirme yüzeyi | https://developer.android.com/ndk | ✅ HTTP 200 (HEAD) |
| NDK ses grubu | native ses API listesi (AAudio dâhil) | https://developer.android.com/ndk/reference/group/audio | ✅ HTTP 200 (HEAD) |

> `ndk/guides/audio/aaudio` yolu 2026-10-12 turunda HTTP 200 olarak
> tescillenmişti (dogrulama §G/5); 2026-10-14 turunda **hem HEAD hem GET 404**
> döndürmüştür — §G/5 tarihsel kaydı korunur, güncel kanıt bu tablodaki
> `reference/group/audio` yoludur. AAudio'nun birebir çağrı-imzası örnekleri
> bu turda üretilmedi — ⚠️ VERIFICATION REQUIRED (Android hedefi Q-P3 kapsamında).

### §F.5 — POSIX taşınabilirlik özeti · çağrı bazlı kanıt matrisi

| Çağrı | POSIX resmi | Linux resmi | Windows karşılığı (doğrulanmış) |
|---|---|---|---|
| `open` | functions/open.html (200) | man2/open.2 (200) | `CreateFileW` (200) |
| `read` | functions/read.html (200) | man2/read.2 (200) | `ReadFile` (200) |
| `write` | functions/write.html (200) | man2/write.2 (200) | `WriteFile` (200) |
| `close` | functions/close.html (200) | man2/close.2 (200) | `CloseHandle` (200) |
| `socket` | functions/socket.html (200) | man2/socket.2 (200) | `socket` (Winsock2, 200) |
| `accept` | functions/accept.html (200) | man2/accept.2 (200) | `accept` (Winsock2, 200) |
| `fork`/`exec` | functions/exec.html (200) · posix_spawn.html (200) | man2/fork.2 (200) · execve.2 (200) | `CreateProcessW` (200) |
| `ioctl` | — (genişletilmiş) | man2/ioctl.2 (200) | `DeviceIoControl` (200) |

Tablo notu: POSIX sütunundaki tam yollar `pubs.opengroup.org/onlinepubs/9799919799/functions/*`
altındadır; Linux sütunu `man7.org` man2 sayfalarıdır. Windows sütunu yalnızca
2026-10-14 turunda HTTP 200 ile doğrulanmış çağrıları içerir (kaynak URL'ler
`platform-api/windows-api-yuzeyi.md` §12.4 ve `ag-api/` §G'dedir).

### §F.6 — 2026-10-14 tur durumu (bu bölümün kanıtı)

| Ölçüm | Değer |
|---|---|
| Doğrulanan benzersiz URL (bu tur, tüm dal) | 163 · 146 HEAD-200 + 1 GET-200 (rfc9293) + 16 × 404 |
| §F'nin yeni kanıtları (envanterle kesişmeyen) | 14 (§S.3 · dogrulama) |
| §F'nin yeniden kullandığı envanter kanıtları | 10 (VAR — tekrar sayılmaz) |
| Ölü URL düzeltmesi | 1 (`com/coinitializeex` → `combaseapi/nf-combaseapi-coinitializeex`) |
| Yeni ölü keşfi | 1 (`ndk/guides/audio/aaudio` — §G/5 tescili 404 oldu) |

### §G — PHP grubu · userland'tan syscall yüzeyine (2026-10-14)

> PHP bu dalın birincil uygulama dilidir (`shared/src/**`, ADR-039). Bu bölüm
> PHP userland fonksiyonlarının §F.5'teki syscall matrisine nasıl oturduğunu
> kanıtlanmış örneklerle gösterir. 17 php.net URL'si 2026-10-14'te HEAD-200
> ile doğrulanmıştır (§G.6).

#### §G.0 — PHP ↔ OS katman eşlemesi

| PHP fonksiyonu | Altındaki syscall (POSIX) | Windows (PHP derlemesi) | Doğrulanmış kaynak |
|---|---|---|---|
| `fopen` / `fread` / `fwrite` / `fclose` | `open` / `read` / `write` / `close` | CRT → `CreateFileW`/`ReadFile`/`WriteFile` | function.fopen/fread/fwrite/fclose.php (200) |
| `file_get_contents` | `open`+`read`+`close` (tek çağrıda) | aynı | function.file-get-contents.php (200) |
| `socket_create`/`bind`/`listen`/`accept`/`connect`/`write` | BSD socket API (§F.5) | **Winsock2** (aynı PHP kodu) | function.socket-*.php (200) |
| `proc_open` | `fork`+`execve` (§6.20) | `CreateProcessW` (§6.20) | function.proc-open.php (200) |
| `pcntl_fork` | `fork` | **yok** (POSIX-only) | function.pcntl-fork.php (200) |
| `stream_select` | `select`/`poll` | Winsock `select` | function.stream-select.php (200) |
| `socket_last_error` / `error_get_last` | `errno` okuması (§F.17) | `GetLastError` köprüsü | function.socket-last-error/error-get-last.php (200) |
| FFI | doğrudan `syscall()` (§J) | Win32 çağrısı | book.ffi.php (200) |

#### §G.1 — Dosya I/O · PHP (Linux ve Windows'ta aynı kod)

```php
<?php
declare(strict_types=1);

// §F.5 matrisindeki open/read/write/close turunun PHP karşılığı.
// Linux'ta libc open/read/write; Windows'ta CRT katmanı CreateFileW/ReadFile/WriteFile çağırır.
$h = fopen('/tmp/k000-demo.bin', 'w+b');           // open(O_RDWR|O_CREAT|O_TRUNC)
if ($h === false) {
    $e = error_get_last();                          // errno okuması — §F.17
    throw new RuntimeException('fopen: ' . ($e['message'] ?? 'bilinmeyen'));
}
fwrite($h, "payload\n");                            // write(2)
rewind($h);                                         // lseek(2) SEEK_SET
$data = fread($h, 64);                              // read(2)
fclose($h);                                         // close(2)
// $data === "payload\n"
```

#### §G.2 — Süreç yürütme · `proc_open` (repo kanıtı)

Gerçek kullanım: `shared/tests/Repository/ApiKeyCreateCommandTest.php:248-269`
(CLI alt sürecini pipe'larla çalıştırır — §6.20'deki fork+execve/CreateProcessW
karşılaştırmasının PHP yüzü):

```php
<?php
declare(strict_types=1);

$command = [PHP_BINARY, 'bin/console', 'apikey:create', '--user=42'];
$pipes   = [];
$handle  = proc_open(
    $command,
    [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],  // fork+exec sonrası stdio pipe'ları
    $pipes,
    dirname(__DIR__, 3)
);
if (!is_resource($handle)) {
    throw new RuntimeException('alt surec acilamadi: fork/exec hatasi');
}
fclose($pipes[0]);                                        // stdin kapat → child SIGPIPE/EPIPE
$out = (string) stream_get_contents($pipes[1]);           // stdout oku (bloklayıcı read)
$err = (string) stream_get_contents($pipes[2]);           // stderr oku
fclose($pipes[1]);
fclose($pipes[2]);
$exit = proc_close($handle);                              // waitpid(2) karşılığı — çıkış kodu
```

> Not: `proc_open` dizi komutu kullanıldığında PHP Windows'ta `CreateProcessW`
> argument-quoting kurallarını, POSIX'te `execve` argv dizisini uygular —
> shell yorumlaması (§6.20 §6.20 notundaki `sh -c`) yalnızca string verilir.

#### §G.3 — Socket sunucu · PHP (POSIX/Winsock ayrımı PHP'de gizli)

```php
<?php
declare(strict_types=1);

// §F: bind→listen→accept turu. PHP socket extension Windows'ta Winsock2'ye bağlanır;
// PHP kodu her iki OS'ta aynıdır, farklılık yalnız derleme zamanındadır.
$srv = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if ($srv === false) { throw new RuntimeException(socket_strerror(socket_last_error())); }

socket_bind($srv, '127.0.0.1', 65432);     // bind(2) — Winsock2 bind
socket_listen($srv, 5);                    // listen(2) — backlog SOMAXCONN yerine 5 (örnek)

$read   = [$srv];
$write  = null;
$except = null;
if (stream_select($read, $write, $except, 5) < 1) {   // select(2) — §G.4
    throw new RuntimeException('istemci gelmedi (5sn timeout)');
}
$client = socket_accept($srv);             // accept(2) — §F.3
if ($client === false) { throw new RuntimeException(socket_strerror(socket_last_error())); }

socket_write($client, "HTTP/1.1 200 OK\r\nContent-Length: 2\r\n\r\nOK");  // write(2)/send
socket_close($client);
socket_close($srv);
```

#### §G.4 — POSIX-only sınır: `pcntl_fork`

`pcntl_*` uzantısı POSIX-only'dir (Windows derlemesinde yoktur) — bu, §F.5
taşınabilirlik matrisinin PHP yüzüdür:

```php
<?php
declare(strict_types=1);

if (!function_exists('pcntl_fork')) {
    throw new RuntimeException('pcntl yok - Windows veya devre disi (POSIX-only)');
}
$pid = pcntl_fork();                 // fork(2)
if ($pid === -1) { throw new RuntimeException('fork basarisiz'); }
if ($pid === 0) {                    // child
    pcntl_exec('/usr/bin/env', ['php', 'worker.php']);   // execve(2) — §6.20
    exit(127);                       // ulaşılamaz
}
pcntl_wait($status);                 // waitpid(2) — §G.2 proc_close karşılığı
```

#### §G.5 — FFI ile doğrudan syscall (Linux)

§J'deki `syscall(SYS_getpid)` yüzeyinin PHP karşılığı — üretimde değil,
kanıt/deneysel amaçlı:

```php
<?php
declare(strict_types=1);

$ffi = FFI::cdef('long syscall(long number, ...);', 'libc.so.6');  // Linux only
$SYS_getpid = 39;   // x86_64 — sayısal değer platforma bağlıdır, §J notu geçerlidir
$pid = $ffi->syscall($SYS_getpid);
// posix_getpid() ile karşılaştırılabilir; FFI Windows'ta MSVCRT değil Win32 bağlamı ister
```

#### §G.6 — PHP kanıt tablosu (2026-10-14 · HEAD-200 · kesişim testi)

| # | URL | Status | Envanter |
|---|---|---|---|
| 1 | `https://www.php.net/manual/en/function.fopen.php` | 200 | VAR |
| 2 | `https://www.php.net/manual/en/function.fwrite.php` | 200 | YENİ |
| 3 | `https://www.php.net/manual/en/function.fread.php` | 200 | YENİ |
| 4 | `https://www.php.net/manual/en/function.fclose.php` | 200 | YENİ |
| 5 | `https://www.php.net/manual/en/function.file-get-contents.php` | 200 | YENİ |
| 6 | `https://www.php.net/manual/en/function.proc-open.php` | 200 | VAR |
| 7 | `https://www.php.net/manual/en/function.socket-create.php` | 200 | YENİ |
| 8 | `https://www.php.net/manual/en/function.socket-bind.php` | 200 | YENİ |
| 9 | `https://www.php.net/manual/en/function.socket-listen.php` | 200 | YENİ |
| 10 | `https://www.php.net/manual/en/function.socket-accept.php` | 200 | YENİ |
| 11 | `https://www.php.net/manual/en/function.socket-connect.php` | 200 | YENİ |
| 12 | `https://www.php.net/manual/en/function.socket-write.php` | 200 | YENİ |
| 13 | `https://www.php.net/manual/en/function.socket-last-error.php` | 200 | YENİ |
| 14 | `https://www.php.net/manual/en/function.error-get-last.php` | 200 | VAR |
| 15 | `https://www.php.net/manual/en/function.stream-select.php` | 200 | YENİ |
| 16 | `https://www.php.net/manual/en/book.ffi.php` | 200 | VAR |
| 17 | `https://www.php.net/manual/en/function.pcntl-fork.php` | 200 | VAR |

Özet: 17/17 HEAD-200 · envanter kesişimi 5 VAR + 12 YENİ (toplam envanter:
995 md · 3303 benzersiz URL, 2026-10-14 ölçümü) · repo kanıtı:
`shared/tests/Repository/ApiKeyCreateCommandTest.php:248-269` (§G.2).

### §G.7 — PHP syscall aileleri eki: kilit · dizin · durum · non-blocking · sinyal (4. tur · 2026-10-14)

§G.1–§G.6 dosya I/O, süreç, socket, `pcntl_fork` ve FFI yüzeyini kapsıyordu; bu ek
**kalan beş syscall ailesinin** PHP karşılığını repo kanıtlarıyla tamamlar. 16 aday URL'nin
tamamı HEAD-200 (§G.7.6); envanter kesişimi **6 VAR + 16 YENİ** (man7 `rmdir.2` ve
`getdents.2` bu ağaçta ilk kez kullanılıyor). macOS/Android'e özgü PHP yüzeyi iddiası
taşınmaz — §H dürüstlük kaydı bu ek için de geçerlidir.

#### §G.7.1 — Kilit: `flock(2)` → `LOCK_EX` / `flock()`

Repo kanıtı (proje kodu):
`shared/src/Component/TemplateEngine.php:74` → `file_put_contents($cachePath, $template, LOCK_EX)` ·
`shared/src/Log/FileHandler.php:251` → `file_put_contents($filepath, $content, FILE_APPEND | LOCK_EX)`.

```php
<?php
declare(strict_types=1);

// flock(2) iki PHP yüzeyinde görünür: (a) file_put_contents LOCK_EX bayrağı,
// (b) fopen + flock() — ikisi de aynı inode kilidine (LOCK_EX) gider.
$dosya = '/tmp/k000-lock.txt';

// (a) Repo kanıtı — TemplateEngine.php:74 deseni: tek çağrıda yaz + kilitle.
$ok = file_put_contents($dosya, "satir-1\n", FILE_APPEND | LOCK_EX);
if ($ok === false) {
    fwrite(STDERR, "yazma veya kilit basarisiz\n");
    exit(1);
}

// (b) Elle flock() — flock(2, LOCK_EX/LOCK_UN) doğrudan eşlemesi.
$h = fopen($dosya, 'c+');                    // open(O_RDWR|O_CREAT) — §G.1 ile aynı fd katmanı
if ($h === false) {
    exit(1);
}
if (flock($h, LOCK_EX) === false) {          // flock(2, LOCK_EX) — yazma kilidi
    fclose($h);
    exit(1);
}
fwrite($h, "kilitli-ek\n");
flock($h, LOCK_UN);                          // close() kilidi de düşürür; bilinçli bırakma tercih edilir
fclose($h);                                  // close(2)
```

#### §G.7.2 — Dizin: `mkdir(2)` / `rmdir(2)` / `getdents(2)` → `mkdir` / `rmdir` / `scandir` / `opendir`

Repo kanıtı: `shared/src/Session/SessionConfig.php:35` → `@mkdir($savePath, 0750, true)` ·
`shared/tests/Unit/Log/LoggerRedactionTest.php:143` (mkdir) · `:51` (`@rmdir`).

```php
<?php
declare(strict_types=1);

// mkdir(2)/rmdir(2)/getdents(2) ailesi PHP'de: yarat/sil + tek çağrı liste + akış.
$dir = '/tmp/k000-dir';
if (!is_dir($dir) && !@mkdir($dir, 0750, true) && !is_dir($dir)) {
    // üçlü denetim — EEXIST yarışı: vendor deseni
    // (vendor/php-di/php-di/src/Compiler/Compiler.php:341);
    // proje kodu basit @mkdir desenini kullanır (SessionConfig.php:35)
    fwrite(STDERR, "mkdir basarisiz\n");
    exit(1);
}

$girdiler = scandir($dir);                   // getdents(2) tek çağrı karşılığı — dizin listesi
if ($girdiler === false) {
    exit(1);
}
// $girdiler[0] = '.', [1] = '..' — dizin listesi filtresiz döner, ayıklanır

$h = opendir($dir);                          // opendir(2) — dizin akışı
if ($h === false) {
    exit(1);
}
while (($g = readdir($h)) !== false) {       // readdir — tek tek getdents turu
    if ($g === '.' || $g === '..') {
        continue;
    }
}
closedir($h);                                // close(dirfd)

@rmdir($dir);                                // rmdir(2) — doluysa başarısız; repo: LoggerRedactionTest.php:51
```

#### §G.7.3 — Dosya durumu: `stat(2)` → `filesize` / `fileperms` / `filemtime` + `clearstatcache`

Repo kanıtı: `home.coremusic.net/include/Stream/MusicStreamHandler.php:72` → `(int)filesize($path)` ·
`shared/src/AI/ToolCalling.php:187` → `filesize($path)` ·
`shared/tests/Unit/Log/LoggerRedactionTest.php:178` → `clearstatcache()`.

```php
<?php
declare(strict_types=1);

// stat(2) PHP'de önbellekli: sonuçlar süreç içi cache'te durur (§G.7.3 notu).
$f = '/tmp/k000-stat.txt';
file_put_contents($f, '1234567890');

$boyut = filesize($f);                       // stat.st_size — repo: MusicStreamHandler.php:72
$mod = fileperms($f);                        // stat.st_mode — izin bitleri
$mtime = filemtime($f);                      // stat.st_mtime — son değişiklik
if ($boyut === false || $mod === false || $mtime === false) {
    fwrite(STDERR, "stat okunamadi\n");
    exit(1);
}

clearstatcache();                            // repo: LoggerRedactionTest.php:178
// Dosya değiştiyse ÖNCE clearstatcache() → SONRA filesize(): aksi hâlde eski (stale)
// stat değeri döner — PHP'nin stat önbelleği bu ailenin tek hata kaynağıdır.
```

#### §G.7.4 — Non-blocking: `fcntl(2)` `O_NONBLOCK` → `stream_set_blocking`

Repo kanıtı yok (bu yüzey proje kodunda kullanılmıyor); akış şeması §C.1 (udp-tcp) ile
`stream_select` kanıtı §G.6 #15'tedir.

```php
<?php
declare(strict_types=1);

// fcntl(2, F_SETFL, O_NONBLOCK) PHP'de stream_set_blocking ile kurulur;
// EAGAIN bekleme döngüsünün karşılığı stream_select (§G.6 #15 kanıtı).
$server = stream_socket_server('tcp://127.0.0.1:8099', $errno, $errstr);
if ($server === false) {
    exit(1);
}
stream_set_blocking($server, false);         // O_NONBLOCK — kabul yığını artık bloklanmaz

$okumalar = [$server];
$yazilabilir = $ek = [];
$n = stream_select($okumalar, $yazilabilir, $ek, 0, 200000); // select(2)/poll(2) — 200 ms
if ($n === false) {
    fwrite(STDERR, "stream_select basarisiz (EINTR/EAGAIN sinifi)\n");
} elseif ($n > 0) {
    // bağlantı bekliyor — non-blocking yığında bu kabul bloklanmaz (§C.1 deseni)
    $istemci = @stream_socket_accept($server, 0);
    if (is_resource($istemci)) {
        fwrite($istemci, "merhaba\n");
        fclose($istemci);                    // close(2) — fd bırakma
    }
}
fclose($server);
```

#### §G.7.5 — Sinyal: `signal(2)` / `signal(7)` → `pcntl_signal` / `pcntl_signal_dispatch`

Repo kodu pcntl kullanmaz (§G.4 ile aynı POSIX-only sınır); bu blok çağrı-imzası
referansıdır — iddia php.net kaynaklıdır (§G.7.6 #21–22).

```php
<?php
declare(strict_types=1);

// POSIX-only: pcntl_* yoksa (Windows, kapalı SAPI) sinyal yüzeyi de yoktur — §G.4 sınırı.
if (!function_exists('pcntl_signal')) {
    fwrite(STDERR, "pcntl yok — sinyal yakalanamaz\n");
    exit(0);
}

pcntl_signal(SIGTERM, static function (int $no): void {
    // Temiz kapanış kapısı: açık fd'ler close, kilitler LOCK_UN — handler içinde
    // yalnız bu sınıf kısa işler yapılır (signal(7)).
    fwrite(STDERR, "SIGTERM {$no} alindi\n");
    exit(0);
});

// pcntl_signal() yalnız kaydeder; pending sinyaller pcntl_signal_dispatch()
// çağrılmazsa handler hiç çalışmaz — döngü başına bir dispatch zorunlu.
for ($i = 0; $i < 5; $i++) {
    pcntl_signal_dispatch();                 // pending sinyalleri bu noktada işler
    usleep(100000);                          // usleep(2) karşılığı — 100 ms bekleme
}
```

#### §G.7.6 — Kanıt tablosu (2026-10-14 · HEAD-200 · kesişim testi)

| # | iddia | URL | durum |
|---|---|---|---|
| 1 | flock(2) LOCK_EX/LOCK_UN | `https://man7.org/linux/man-pages/man2/flock.2.html` | VAR |
| 2 | PHP flock() | `https://www.php.net/manual/en/function.flock.php` | YENİ |
| 3 | PHP file_put_contents + LOCK_EX | `https://www.php.net/manual/en/function.file-put-contents.php` | YENİ |
| 4 | mkdir(2) | `https://man7.org/linux/man-pages/man2/mkdir.2.html` | VAR |
| 5 | rmdir(2) | `https://man7.org/linux/man-pages/man2/rmdir.2.html` | YENİ |
| 6 | getdents(2) | `https://man7.org/linux/man-pages/man2/getdents.2.html` | YENİ |
| 7 | PHP mkdir() | `https://www.php.net/manual/en/function.mkdir.php` | YENİ |
| 8 | PHP rmdir() | `https://www.php.net/manual/en/function.rmdir.php` | YENİ |
| 9 | PHP scandir() | `https://www.php.net/manual/en/function.scandir.php` | YENİ |
| 10 | PHP opendir() | `https://www.php.net/manual/en/function.opendir.php` | YENİ |
| 11 | PHP readdir() | `https://www.php.net/manual/en/function.readdir.php` | YENİ |
| 12 | stat(2) | `https://man7.org/linux/man-pages/man2/stat.2.html` | VAR |
| 13 | PHP filesize() | `https://www.php.net/manual/en/function.filesize.php` | YENİ |
| 14 | PHP fileperms() | `https://www.php.net/manual/en/function.fileperms.php` | YENİ |
| 15 | PHP filemtime() | `https://www.php.net/manual/en/function.filemtime.php` | YENİ |
| 16 | PHP clearstatcache() | `https://www.php.net/manual/en/function.clearstatcache.php` | YENİ |
| 17 | fcntl(2) O_NONBLOCK | `https://man7.org/linux/man-pages/man2/fcntl.2.html` | VAR |
| 18 | PHP stream_set_blocking() | `https://www.php.net/manual/en/function.stream-set-blocking.php` | YENİ |
| 19 | PHP stream_select() | (§G.6 #15 — aynı dosya, tekrar sayılmadı) | VAR |
| 20 | signal(7) | `https://man7.org/linux/man-pages/man7/signal.7.html` | VAR |
| 21 | PHP pcntl_signal() | `https://www.php.net/manual/en/function.pcntl-signal.php` | VAR |
| 22 | PHP pcntl_signal_dispatch() | `https://www.php.net/manual/en/function.pcntl-signal-dispatch.php` | YENİ |
| 23 | PHP usleep() | `https://www.php.net/manual/en/function.usleep.php` | YENİ |

Özet: 23 satır · 22 benzersiz URL **22/22 HEAD-200** (#19 §G.6'ya atıfla, URL
tekrar sayılmadı) · kesişim **6 VAR + 16 YENİ** · repo kanıtı 7 satır (TemplateEngine:74 · FileHandler:251 ·
SessionConfig:35 · LoggerRedactionTest:51/143/178 · MusicStreamHandler:72 ·
ToolCalling:187 + vendor Compiler:341) · envanter ölçümü: §T.8.

## §H — Sistem-gruplu örnek haritası (4. tur · 2026-10-14)

§F/§G bu ağacın merkezî örnek deposudur; bu bölüm 4. turda ağaçtaki **tüm**
gruplu-örnek bölümlerini tek tabloda toplar — "her sistem çağrısı ailesi için
her sistemde + PHP'de örnek" kuralının haritası. Eklenen bölümler 4. turdur.

| Dosya | Bölüm | Linux/POSIX | Windows | PHP | Not |
|---|---|---|---|---|---|
| isletim-sistemi-api-ornekler.md | §F · §G · §H · §I | §F.1 (C) | §F.1 §F.2-win eşlemesi | §B · §G.1-§G.7 | merkezî depo · §I (5. tur): Python·Go·Rust·C#·Node.js — ayrı "dil" sütunu yok, §I.0 eşleme tablosu bakılır |
| syscall/syscall-mekanizmasi.md | §J · §E | §J.1-§J.2 (x86_64/libc C) | §F tablo (metin) | §E.1-§E.3 (repo) | makine katmanı |
| syscall/syscall-hata-kodlari-ve-errno.md | §F.17-§F.18 | §F.17 (C) | — (§F.16 dürüstlük bloğu; errno↔WSAGetLastError eşlemesi bu dosyada YOK — bkz. posix-socket §F) | §F.17 (PHP) | hata katmanı · düzeltme 2026-10-14 |
| sistem-cagrilari/sistem-cagrilari-ornekler.md | §6.20-§6.21 | §6.20: fork+execve · posix_spawn (C) | §6.20: CreateProcessW (C) | §6.20: proc_open · §6.21 kanıt | alt-numara etiketi yok — blok başlıkları düzeltildi |
| sistem-cagrilari/sistem-cagrilari-surucu.md | §10.21 | — | — | §10.21.1-§10.21.2 (PHP · shell_exec/proc_open) | 4. tur eklendi · repo kanıtı 2 |
| cihaz-erisimi/cihaz-erisimi-anayasa.md | §10.23-§10.25 | §10.23 (C open/ioctl) | §12.3 atıf (windows-api-yuzeyi) | §10.25 (PHP fopen) | 4. tur: §10.25 eklendi |
| ag-api/socket-api-temelleri.md | §F.1-§F.6 | §F.1 (C) | §F.2 (Winsock2 C) | §F.3 (PHP stream) | 4. tur eklendi · 3 yeni URL |
| ag-api/udp-tcp-ve-tls-api.md | §F.1-§F.5 | §F.1-§F.2 (C TCP/UDP) | atıf: posix-socket §F.2 | §F.3-§F.4 (PHP) | 4. tur eklendi · 5 yeni URL |
| ag-api/posix-socket-ve-winsock-karsilastirmasi.md | §F.1-§F.5 | §F.1 (C) | §F.2 (C) | §F.5 (PHP stream) | 4. tur: §F.5 eklendi · yeni URL yok |
| platform-api/windows-api-yuzeyi.md | §12-§13 | — | §12.1-§12.4 (C) | §13.1-§13.4 (PHP) | 4. tur: §13 eklendi · repo kanıtı 2 |

**Grup eksikliği kaydı (3. tur dürüstlüğü aynen korunur):** macOS ve Android
(NDK) için birebir çağrı-imzası C/PHP örneği **ağaçta YOKTUR** — macOS POSIX
kimliği iddiası kaynaksız yazılmaz; AAudio imzaları doğrulanmamıştır (⚠️
VERIFICATION REQUIRED). Bu tur da bu iki grubu uydurma kodla
doldurmamıştır; haritadaki boş hücreler bilinçli boşluktur.

## §I — Diğer yazılım dilleri · syscall yüzeyi (2026-10-14 · 5. tur)

> §F (sistem bazlı C) ve §G (PHP) tamamlayıcıdır; bu bölüm aynı syscall
> yüzeyini **diğer 5 dilde** gösterir. 17 resmi URL 2026-10-14'te HEAD-200
> ile doğrulanmış, envanter kesişimi 0'dır (§I.6). Bloklar derlenebilirlik
> iddiası TAŞIMAZ; kaynak §I.6 tablosudur.

#### §I.0 — Dil ↔ katman eşleme tablosu

| Dil | Süreç başlatma | Dosya I/O | Ağ | Hata ≙ errno | Doğrulanmış kaynak |
|---|---|---|---|---|---|
| Python | `subprocess.run` (fork+exec) | `open`/`fread` eşdeğeri | `socket` | `OSError.errno` | docs.python.org (5 URL, 200) |
| Go | `exec.Command` (fork+exec) | `os.OpenFile` | `net.Listen`/`Dial` | `syscall.Errno` | pkg.go.dev (3 URL, 200) |
| Rust | `std::process::Command` | `std::fs` | `std::net` | `io::Error` (OS errno) | doc.rust-lang.org (4 URL, 200) |
| C#/.NET | `Process.Start` (CreateProcessW) | `FileStream` (CreateFileW) | `System.Net.Sockets` | `Win32Exception` | learn.microsoft.com (3 URL, 200) |
| Node.js | `child_process.spawn` | `fs` | `net` | `errno`/`code` özellikleri | nodejs.org (2 URL, 200) |

#### §I.1 — Python grubu

```python
import errno
import socket
import subprocess

# --- süreç: fork+execve (§6.20) sarmalayıcısı ---
proc = subprocess.run(                       # POSIX: fork + execve; Windows: CreateProcessW
    ["php", "bin/console", "apikey:create", "--user=42"],
    capture_output=True, text=True, timeout=30,
)
# proc.returncode ≙ waitpid çıkış kodu (§G.2 proc_close)
if proc.returncode != 0:
    raise RuntimeError(f"alt surec {proc.returncode}: {proc.stderr.strip()}")

# --- ağ: BSD socket (§F.5) sarmalayıcısı ---
srv = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
srv.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1)
srv.bind(("127.0.0.1", 65432))               # bind(2)
srv.listen(5)                                # listen(2)
conn, addr = srv.accept()                    # accept(2)
conn.sendall(b"OK\n")                        # write(2)/send
conn.close()                                 # close(2)
srv.close()

# --- hata: errno okuması (§F.17) ---
try:
    socket.socket(socket.AF_INET, socket.SOCK_STREAM).connect(("127.0.0.1", 1))
except OSError as e:
    if e.errno == errno.ECONNREFUSED:        # errno değişkeni — §F.17 C deseni
        ...                                  # dinleyen yok
```

> Not: `os.fork()` POSIX-only'dir (Windows'ta yok) — §G.4 `pcntl_fork`
> sınırının Python yüzü. `ctypes` ile `libc` çağrısı §G.5 FFI karşılığıdır
> (docs.python.org/3/library/ctypes.html, 200).

#### §I.2 — Go grubu

```go
package main

import (
    "fmt"
    "net"
    "os/exec"
    "syscall"
)

func main() {
    // süreç: fork+execve (§6.20) — Go runtime kendi M:N thread'ini yönetir,
    // StartProcess POSIX'te fork+exec, Windows'ta CreateProcessW çağırır.
    cmd := exec.Command("php", "bin/console", "apikey:create", "--user=42")
    out, err := cmd.Output()                 // wait ile çıkış kodu (§G.2 karşılığı)
    if err != nil {
        if ee, ok := err.(*exec.ExitError); ok {
            fmt.Println("cikis kodu:", ee.ExitCode())
        }
    }
    _ = out

    // ağ: BSD socket (§F.5) sarmalayıcısı
    ln, err := net.Listen("tcp", "127.0.0.1:65432")   // socket+bind+listen
    if err != nil {
        panic(err)
    }
    defer ln.Close()                                  // close(2)
    conn, err := ln.Accept()                          // accept(2)
    if err != nil {
        panic(err)
    }
    defer conn.Close()
    conn.Write([]byte("OK\n"))                        // write(2)/send

    // hata: errno ≙ syscall.Errno (§F.17 karşılığı)
    if pathErr := syscall.Access("yok-dosya", 0); pathErr != nil {
        fmt.Println("errno:", pathErr)                // syscall.Errno = errno_t
    }
}
```

#### §I.3 — Rust grubu

```rust
use std::io::{Read, Write};
use std::net::{TcpListener, TcpStream};
use std::process::{Command, Stdio};

fn main() -> std::io::Result<()> {
    // süreç: fork+execve (§6.20) sarmalayıcısı
    let child = Command::new("php")
        .args(["bin/console", "apikey:create", "--user=42"])
        .stdin(Stdio::null())
        .output()?;                              // wait ile çıkış (§G.2 karşılığı)
    eprintln!("cikis: {}", child.status);

    // ağ: BSD socket (§F.5) sarmalayıcısı
    let listener = TcpListener::bind("127.0.0.1:65432")?;  // socket+bind+listen
    let mut stream: TcpStream = listener.accept()?.0;      // accept(2)
    stream.write_all(b"OK\n")?;                           // write(2)/send

    // hata: io::Error OS errno taşır (§F.17 karşılığı)
    if let Err(e) = TcpStream::connect("127.0.0.1:1") {
        if let Some(errno) = e.raw_os_error() {
            eprintln!("errno: {errno}");                   // C errno ile aynı sayı
        }
    }
    Ok(())                                         // listener/stream Drop → close(2)
}
```

> Not: `std::os::unix` altındaki `AsRawFd`/`FromRawFd` yüzeyi POSIX-only'dir
> (§G.4 sınırının Rust yüzü; doc.rust-lang.org/std/os/unix, 200).

#### §I.4 — C#/.NET grubu

```csharp
using System.Diagnostics;
using System.Net.Sockets;
using System.Runtime.InteropServices;

// --- süreç: fork+execve (POSIX) / CreateProcessW (Windows) sarmalayıcısı ---
var psi = new ProcessStartInfo("php")
{
    ArgumentList = { "bin/console", "apikey:create", "--user=42" },
    RedirectStandardOutput = true,
    UseShellExecute = false,
};
using var proc = Process.Start(psi)!;             // Windows: CreateProcessW (§6.20)
string output = proc.StandardOutput.ReadToEnd();
proc.WaitForExit();                                // waitpid(2) karşılığı (§G.2)
Console.WriteLine($"cikis: {proc.ExitCode}");

// --- ağ: BSD socket / Winsock2 sarmalayıcısı (§F.5) ---
using var listener = new TcpListener(System.Net.IPAddress.Loopback, 65432);
listener.Start();                                  // socket+bind+listen
using TcpClient client = listener.AcceptTcpClient();   // accept(2)
NetworkStream ns = client.GetStream();
ns.Write("OK\n"u8.ToArray());                      // write(2)/send
// Dispose → close(2) (§G.1 fclose karşılığı)

// --- hata: SocketException.NativeErrorCode = errno/WSA (§F.17) ---
try { using var c = new TcpClient("127.0.0.1", 1); }
catch (SocketException ex) { Console.WriteLine($"errno: {ex.NativeErrorCode}"); }

// --- P/Invoke: FFI'nin C# yüzü (§G.5) — doğrudan Win32 ---
[DllImport("kernel32.dll")]
static extern uint GetCurrentProcessId();
Console.WriteLine($"pid: {GetCurrentProcessId()}");   // §J syscall yüzeyinin Win32 karşılığı
```

#### §I.5 — Node.js (V8) grubu

```javascript
const { spawn } = require('node:child_process');   // fork+exec (§6.20)
const net = require('node:net');
const { promisify } = require('node:util');

// --- süreç ---
const child = spawn('php', ['bin/console', 'apikey:create', '--user=42'], {
    stdio: ['ignore', 'pipe', 'pipe'],
});
let out = '';
child.stdout.on('data', (d) => { out += d; });
const code = await new Promise((resolve) => child.on('close', resolve));  // waitpid karşılığı
// code ≙ §G.2 $exit

// --- ağ: BSD socket / Winsock2 sarmalayıcısı (§F.5) ---
const server = net.createServer((sock) => {
    sock.write('OK\n');                            // write(2)/send
    sock.end();                                    // shutdown+close
});
await new Promise((r) => server.listen(65432, '127.0.0.1', r));  // socket+bind+listen
// server.close() → close(2)

// --- hata: errno özelliği (§F.17 karşılığı) ---
try {
    await promisify(net.connect)({ port: 1, host: '127.0.0.1' });
} catch (e) {
    console.log('errno:', e.errno, 'code:', e.code);  // code: 'ECONNREFUSED' ≙ §F.17
}
```

> Not: Node.js Windows'ta libuv üzerinden Winsock2'ye bağlanır; `child_process`
> Windows'ta `CreateProcessW` çağırır — §F.5/§6.20 eşlemeleri aynen geçerlidir.

#### §I.6 — §I kanıt tablosu (2026-10-14 · HEAD-200 · kesişim 0)

| # | URL | Status | Envanter |
|---|---|---|---|
| 1 | `https://docs.python.org/3/library/os.html` | 200 | YENİ |
| 2 | `https://docs.python.org/3/library/subprocess.html` | 200 | YENİ |
| 3 | `https://docs.python.org/3/library/socket.html` | 200 | YENİ |
| 4 | `https://docs.python.org/3/library/errno.html` | 200 | YENİ |
| 5 | `https://docs.python.org/3/library/ctypes.html` | 200 | YENİ |
| 6 | `https://pkg.go.dev/os/exec` | 200 | YENİ |
| 7 | `https://pkg.go.dev/net` | 200 | YENİ |
| 8 | `https://pkg.go.dev/syscall` | 200 | YENİ |
| 9 | `https://doc.rust-lang.org/std/process/struct.Command.html` | 200 | YENİ |
| 10 | `https://doc.rust-lang.org/std/net/struct.TcpListener.html` | 200 | YENİ |
| 11 | `https://doc.rust-lang.org/std/net/struct.TcpStream.html` | 200 | YENİ |
| 12 | `https://doc.rust-lang.org/std/os/unix/index.html` | 200 | YENİ |
| 13 | `https://learn.microsoft.com/en-us/dotnet/api/system.diagnostics.process` | 200 | YENİ |
| 14 | `https://learn.microsoft.com/en-us/dotnet/api/system.net.sockets.socket` | 200 | YENİ |
| 15 | `https://learn.microsoft.com/en-us/dotnet/api/system.runtime.interopservices.dllimportattribute` | 200 | YENİ |
| 16 | `https://nodejs.org/api/child_process.html` | 200 | YENİ |
| 17 | `https://nodejs.org/api/net.html` | 200 | YENİ |

Özet: 17/17 HEAD-200 · envanter kesişimi **0 VAR + 17 YENİ** (envanter: 995 md ·
3303 benzersiz URL, 2026-10-14 ölçümü). Repo-satır iddiası eklenmez — bu
diller CoreMusic üretim kodunda kullanılmaz (PHP 8.4 tek backend dili, ADR-039);
bloklar resmi dokümantasyon sarmalayıcılarıdır, üretim kodu değildir.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
