---
node_id: K000-isletim-sistemi/isletim-sistemi-api/syscall/syscall-hata-kodlari-ve-errno
node_type: doc
status: active
parent_id: K000-isletim-sistemi/isletim-sistemi-api/syscall
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-api/isletim-sistemi-api-kapsam
evidence:
  - shared/src/AI/AIEngine.php:288-310
  - shared/tests/Repository/ApiKeyCreateCommandTest.php:248-269
validation: 2026-10-13 · dosya uzunlugu >=500 satir, URL'ler HTTP 200 ile dogrulandi
open_questions:
  - Q-P3 · hedef platform seti netlestirilmeli · ⚠️ VERIFICATION REQUIRED
group: mimari
---
# syscall hata kodlari ve errno

> Bu dosya, syscall'larin donus degerlerini ve hata ayristirma
> mekanizmasini tanimlar. Cekirdek, hata durumlarini donus degeri uzerinden
> bildirir; kullanici alanindaki sarmalayicilar bu degeri errno'ye cevirir.
> Kapsam sinirlari `isletim-sistemi-api-kapsam.md` §A.3 · K8 icinde
> tanimlanmistir.

---

## §A — Donus degeri modeli

### §A.1 — Donus degeri araligi

Cekirdek, syscall donuslerini iki genis aralikta bildirir: basari
araligi (>= 0) ve hata araligi (-1 ile -4095). Bu aralik, mimariden
bagimsizdir ve her syscall icin gecerlidir.

| Aralik | Deger | Yorum | Sonuc |
|---|---|---|---|
| >= 0 | dogal sayi | basarili islem | sonuc kullanilir |
| -1 | tek deger | hata durumu | errno okunur |
| -4095 | -1 ile -4095 | cekirdek hata araligi | errno'ye cevrilir |
| -4096 | sinir degeri | cekirdek donmez | errno okunur |

### §A.2 — Basari araligi

Basari araligi, syscall'in basariyla tamamlandigini ve donus degerinin
kullanilabilecegini gosterir. Her syscall'in basari degeri farklidir.

| Syscall | Basari degeri | Anlam | Not |
|---|---|---|---|
| read | >= 0 | okunan bayt | 0 = dosya sonu |
| write | >= 0 | yazilan bayt | tam yazildi mi kontrol |
| open | >= 0 | dosya tanimlayici | 0 = stdin degil |
| mmap | >= 0 | bellek adresi | 0 = basarisiz degil |
| fork | 0 veya pid | 0 = cocuk, pid = ebeveyn | cift deger |

### §A.3 — Hata araligi

Hata araligi, syscall'in basarisiz oldugunu gosterir. Cekirdek, hata
kodunu errno olarak saklar ve kullanici alanina dondurur. Hata araligi,
tum syscall'lar icin aynidir.

| Hata | Donus | Anlam | Not |
|---|---|---|---|
| genel hata | -1 | errno okunur | en yaygin |
| cekirdek hata | -4095 - errno | cekirdek ici hata | nadir |
| bilinmeyen | -ENOSYS | syscall yok | tablo disi |
| yetki | -EPERM | yetki yetersiz | guvenlik |

### §A.4 — errno donusumu

Cekirdek, hata durumunda donus degerini `errno` ile cevirir. Bu donusum,
kullanici alanindaki sarmalayicilar tarafindan yapilir ve hata kodunu
okunabilir hale getirir.

| Adim | Eylem | Seviye | Not |
|---|---|---|---|
| 1 | cekirdek hata kodu dondurur | cekirdek | -errno |
| 2 | sarmalayici donus degerini okur | kullanici | -1 veya -4095 |
| 3 | sarmalayici errno'yu set eder | kullanici | hata kodu yazilir |
| 4 | uygulama errno'yu okur | kullanici | hata ayristirma |

## §B — Yaygin hata kodlari

### §B.1 — Dosya hatalari

| Kod | Isim | Anlam | Not |
|---|---|---|---|
| 1 | EPERM | yetki yetersiz | guvenlik |
| 2 | ENOENT | dosya yok | yol gecersiz |
| 5 | EIO | giris/cikis hatasi | donanim hatasi |
| 9 | EBADF | gecersiz dosya tanimlayici | fd gecersiz |
| 13 | EACCES | erisim reddedildi | izin yok |
| 17 | EEXIST | dosya zaten var | acma modu |
| 20 | ENOTDIR | dizin degil | yol gecersiz |
| 21 | EISDIR | dizin | yazma denemesi |

### §B.2 — Surec hatalari

| Kod | Isim | Anlam | Not |
|---|---|---|---|
| 10 | ECHILD | cocuk surec yok | wait hatasi |
| 11 | EAGAIN | tekrar dene | bloklayici islem |
| 12 | ENOMEM | bellek yok | mmap, brk |
| 14 | EFAULT | bellek adresi gecersiz | kullanici hatasi |
| 22 | EINVAL | gecersiz arguman | parametre hatasi |
| 24 | EMFILE | acik dosya siniri | fd havuzu dolu |
| 25 | ENFILE | sistem dosya siniri | sistem geneli |

### §B.3 — Bellek hatalari

| Kod | Isim | Anlam | Not |
|---|---|---|---|
| 12 | ENOMEM | bellek yok | mmap, brk |
| 14 | EFAULT | bellek adresi gecersiz | kullanici hatasi |
| 22 | EINVAL | gecersiz arguman | hizalama hatali |
| 12 | ENOMEM | yetersiz bellek | yigin asimi |

### §B.4 — Ag hatalari

| Kod | Isim | Anlam | Not |
|---|---|---|---|
| 98 | EADDRINUSE | adres kullanimda | port baglama |
| 99 | EADDRNOTAVAIL | adres mevcut degil | bagli adres |
| 104 | ECONNRESET | baglanti sifirlandi | karsi taraf |
| 110 | ETIMEDOUT | zaman asimi | baglanti kurulamadi |
| 111 | ECONNREFUSED | baglanti reddedildi | dinleyen yok |
| 113 | EHOSTUNREACH | ana bilgisayara erisilemiyor | ag hatasi |

## §C — Hata ayristirma desenleri

### §C.1 — Basit kontrol

En temel hata ayristirma deseni, donus degerini kontrol etmektir.
Bu desen, her syscall icin gecerlidir ve errno okumadan once yapilir.

| Adim | Eylem | Sonuc | Not |
|---|---|---|---|
| 1 | syscall cagir | donus degeri | rax'a yazilir |
| 2 | donus degerini kontrol et | >= 0 veya < 0 | basari veya hata |
| 3 | hata ise errno oku | errno | hata kodu |
| 4 | hata kodunu ayristir | hata tipi | uygun tepki |

### §C.2 — Coklu kontrol

Bazi syscall'lar, farkli donus degerleriyle farkli hatalari bildirir.
Bu durumda, donus degerleri sirayla kontrol edilir.

| Kontrol | Donus | Anlam | Not |
|---|---|---|---|
| 1 | 0 | basari | islem tamam |
| 2 | -1 | errno okunur | genel hata |
| 3 | -errno | cekirdek hata | dogrudan kod |
| 4 | > 0 | kismi basari | kalan is |

### §C.3 — errno temizleme

errno, her syscall'dan once temizlenmelidir; cunku cekirdek, hata
durumunda errno'yu yalnizca hata oldugunda yazar. Temizlenmeyen errno,
onceki bir islemden kalabilir.

| Adim | Eylem | Neden | Not |
|---|---|---|---|
| 1 | errno = 0 | eski degeri temizle | hata onegorme |
| 2 | syscall cagir | islem yap | donus degeri |
| 3 | donus degerini kontrol et | basari veya hata | >= 0 veya < 0 |
| 4 | errno oku | hata kodu | yalnizca hata ise |

### §C.4 — Hata siniflandirma

Hatalar, etkilerine gore siniflandirilir: geri alinabilir (EAGAIN),
geri alinamaz (EINVAL) ve onarilabilir (EIO). Her sinif, farkli bir
tepki gerektirir.

| Sinif | Ornek | Tepki | Not |
|---|---|---|---|
| geri alinabilir | EAGAIN | tekrar dene | bekleyip tekrar dene |
| geri alinamaz | EINVAL | hata ver | arguman gecersiz |
| onarilabilir | EIO | log + devam | donanim hatasi |
| kritik | ENOMEM | hata ver | bellek tukendi |

## §D — Web dogrulama matrisi

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| fork syscall'i hata durumunda -1 dondurur | https://man7.org/linux/man-pages/man2/fork.2.html | HTTP 200 | ✓ dogrulandi |
| execve syscall'i hata durumunda errno set eder | https://man7.org/linux/man-pages/man2/execve.2.html | HTTP 200 | ✓ dogrulandi |
| wait syscall'i hata durumunda ECHILD dondurur | https://man7.org/linux/man-pages/man2/wait.2.html | HTTP 200 | ✓ dogrulandi |

## §E — Gercek repo kanitlari

### §E.1 — Kanit 1 · proc_open hata kontrolu

**Dosya:** `shared/src/AI/AIEngine.php` · **Satır:** 288-310

```php
$fullBinaryPath = realpath($binaryPath);
if ($fullBinaryPath === false || !is_executable($fullBinaryPath)) {
    throw new \RuntimeException(
        sprintf('Python binary bulunamadi veya calistirilabilir degil: %s', $binaryPath),
    );
}

$process = proc_open($cmd, $descriptors, $pipes);

if (!is_resource($process)) {
    throw new \RuntimeException('Python prosesi baslatilamadi');
}
```

Kanit notu: `proc_open`, syscall sonucunu `is_resource` ile kontrol eder;
bu, §C.1'deki basit kontrol deseninin PHP karsiligidir.

### §E.2 — Kanit 2 · fork hata kontrolu

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

Kanit notu: `pcntl_fork`, `-1` donusunu kontrol eder; `pcntl_wifexited`
ve `pcntl_wexitstatus`, cikis kodunu ayristirir (§C.2 · coklu kontrol).

### §E.3 — Kanit 3 · dosya akisi hata kontrolu

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

Kanit notu: `proc_close`, cikis kodunu dondurur; `fopen`, dosya acma
hatasini kontrol eder (§C.1 · basit kontrol).

### §E.4 — Kanit ozeti

| Dosya | Satır | Kanit | §Baglanti |
|---|---|---|---|
| shared/src/AI/AIEngine.php | 288-310 | proc_open hata kontrolu | §E.1 |
| shared/tests/Repository/ApiKeyCreateCommandTest.php | 248-269 | fork hata kontrolu | §E.2 |
| home.coremusic.net/include/Stream/MusicStreamHandler.php | 185-215 | dosya akisi hata kontrolu | §E.3 |

## §F — Errno Derinleştirme: Kategoriler, Geçersizleşme Kuralı, PHP Köprüsü

> Bu bölüm §A-§E kanıt eklerini derinleştirir. Yeni tesciller: §F.9
> tablosunda 3 URL (hepsi envanter kesişimi 0 · HEAD-200 · 2026-10-13).
> Kod adları (errno sınıflandırma) doktrin seviyesinde; sayısal değerler
> yazılmaz (mimariye göre değişir — bu ağacın anayasal kuralı).

### §F.1 errno kategori tablosu (sayısız kodun sınıflandırılması)

| # | Kategori | Tipik errno adı | Ne bildirir | Uygulama dersi |
|---|---|---|---|---|
| F.1.1 | erişim/red | EACCES sınıfı | yetki yetersiz | izin denetimi ayrı katman |
| F.1.2 | varlık | ENOENT sınıfı | yol/nesne yok | "yok" hata değil durum olabilir |
| F.1.3 | kaynak bitişi | EMFILE/ENFILE sınıfı | süreç/ağaç sınırı | tanıtıcı sızıntısı alarmı |
| F.1.4 | askıya alma | EINTR sınıfı | kesintiyle bölünme | kısa bekleme + yeniden deneme |
| F.1.5 | kısmi işlem | EAGAIN/EWOULDBLOCK sınıfı | şimdi değil | bloklamayan desenlerin temeli |
| F.1.6 | boyut/taşma | EFBIG/EOVERFLOW sınıfı | sınır aşıldı | boyut denetimi |
| F.1.7 | cihaz/hat | EIO sınıfı | g/ç hata | aygıt sınıfı olay |
| F.1.8 | kilit | EDEADLK/EINPROGRESS sınıfı | senkronizasyon çakışması | RT yasak desenlerinin kökü |

> §F.1 tablosu errno kodlarının tamamını değil, sınıflandırma iskeletini
> verir; her kategori içindeki tam kod listesi mimariye göre değişir —
> sayısal/eksiksiz liste bu dosyanın sorumluluğunda değildir (daraltma
> kuralı: iddia, sınırıyla birlikte yazılır).

### §F.2 Geçersizleşme kuralı (errno'nun ömrü)

| # | Kural | Sonuç |
|---|---|---|
| F.2.1 | başarısız dönüş değeri görülmeden errno okunmaz | yanlış hata raporlaması |
| F.2.2 | başarılı çağrı errno'u geçersiz kılmayabilir | eski errno'a güvenme |
| F.2.3 | errno okuma, başarı yolunda gereksiz iş | sıcak yolda yalnız hata yolunda oku |
| F.2.4 | errno değeri kopyalanarak saklanır | sonraki çağrılar ezmez |

> F.2.2-F.2.3 satırları doktrin seviyesindedir; bu turda tescil edilmedi —
> ⚠️ VERIFICATION REQUIRED (kesişim 0 kuralı gereği doğrulanmadan adres yazılmaz).

### §F.3 strerror ailesi — thread güvenliği uyarısı

Hata kodunun metne çevrilmesi pratikte yaygındır; ancak çeviri tamponunun
sahipliği iki farklı desende değişir (ikisi de doktrin — ⚠️ tescil edilmedi):

| # | Desen | Sahiplik | Risk |
|---|---|---|---|
| F.3.1 | klasik çeviri | döndürülen dizge dahili tampondadır | eşzamanlı çağrıda ezilme |
| F.3.2 | tamponu çağıranın verdiği çeviri | döndürülen dizge çağırana ait | boyut denetimi şart |

CoreMusic yüzeyi: PHP kodu bu desenin doğrudan çağırıcısı değildir; bu dosya
yalnız sargı katmanı uyarısı olarak taşır.

### §F.4 PHP köprüsü — süreç ve akış hatası yüzeyi

PHP'nin sargı katmanı, hata bilgisini iki ayrı araçla görünür kılar: süreç
yüzeyinde dönüş/çıkış kodu denetimi, akış yüzeyinde akış durumu denetimi.
İki resmi referans bu bölümün yeni tescilleridir:

| # | İddia | URL | Tarih | Durum |
|---|---|---|---|---|
| F.4.1 | PHP `pclose`, süreç kapatma/bekleme işlevinin resmi referansıdır | https://www.php.net/manual/en/function.pclose.php | 2026-10-13 | ✅ HEAD 200 (kesişim 0) |
| F.4.2 | PHP `error_get_last`, son hata kaydının resmi referansıdır | https://www.php.net/manual/en/function.error-get-last.php | 2026-10-13 | ✅ HEAD 200 (kesişim 0) |

| # | PHP aracı | Ne verir | errno'a göreliği |
|---|---|---|---|
| F.4.3 | süreç dönüş değeri + çıkış kodu | dolaylı hata sınıfı | errno okumaz, kod okur |
| F.4.4 | akış sonu/durum denetimi | akış hatası sınıfı | errno okumaz |
| F.4.5 | son hata kaydı (dil/çalışma zamanı katmanı) | PHP katmanı hataları | errno'dan **bağımsız** katman |
| F.4.6 | kaynak (resource) tipi denetimi | yanlış kullanım sınıfı | dil hatası, sistem hatası değil |

> F.4.5 satırı kritik ayrım: PHP'nin son-hata kaydı, sorgulanan errno'un
> karşılığı değildir; iki katman (dil vs çekirdek) karıştırılmaz. Bu ayrım
> bu dosyanın §C'deki dosya akışı örneğiyle aynı kademede durur.

### §F.5 POSIX akış durumu — stdio.h üzerinden tescil

Akışların C yüzeyinde durum denetimi (`feof`/`ferror` sınıfı davranışlar)
POSIX'in `<stdio.h>` tanımının içindedir:

| # | İddia | URL | Tarih | Durum |
|---|---|---|---|---|
| F.5.1 | POSIX `<stdio.h>`, akış arayüzlerini (okuma/yazma/durum) tanımlar | https://pubs.opengroup.org/onlinepubs/9799919799/basedefs/stdio.h.html | 2026-10-13 | ✅ HEAD 200 (kesişim 0) |

| # | Durum denetimi | Anlamı | §C.3 örneğiyle ilişkisi |
|---|---|---|---|
| F.5.2 | son-ağzı (EOF) denetimi | akış bitti | §C.3 döngüsü bu deseni kullanır |
| F.5.3 | akış-hatası denetimi | hatası sonlandı | §C.3 kapatma deseni bunu destekler |
| F.5.4 | temizlik (flush) hatası | yazma gecikmeli hata | kapatma dönüşü denetlenir |

### §F.6 Çıkış kodu sözleşmeleri

| # | Sözleşme | Değer sınıfı | Kullanım |
|---|---|---|---|
| F.6.1 | sıfır | başarı | evrensel pratik (§E.1 kanıtı: sıfır olmayan = başarısız) |
| F.6.2 | sıfır olmayan | hata sınıfı | ayrıştırma çağıranın sözleşmesi |
| F.6.3 | ayrık kodlar (ör. yürütme başarısızlığı için ayrılan değer) | araç-özel | bu ağacın örnek dosyasındaki fork örneğiyle aynı ders |
| F.6.4 | sinyalle ölüm | çıkış kodu yok | `WIFEXITED` sınıfı ayrım gerekir (§6.6, örnek dosyası) |
| F.6.5 | sistem-hata-listesi başlıkları | belge sınıfı | bu turda doğrulanamadı (aday adres 404) — ⚠️ |

### §F.7 Hata sonrası karar ağacı (açıklayıcı)

```text
hata goruldu
   |
   v
kategoriles (F.1) --+--> tekrar denenebilir mi? --evet--> sinirli tekrar
                    |
                    +--> kaynak sizintisi mi?  --evet--> kapat + raporla
                    |
                    +--> yetki/red mi?        --evet--> eskalasyon (guvenlik)
                    |
                    +--> bilinmiyor           ------> olay kaydi + don
```

Ağacın her dalı bu dosyanın §E'deki üç repo deseniyle kapanır (denetim,
kapatma, çıkış kodu); yeni dal eklenmez — ağacın sınıfı daraltma kuralıyla
yazılmıştır (iddia sınırıyla birlikte).

### §F.8 Repo kanıtlarıyla bağlantı (atıf — tekrar değil)

| # | Kanıt kökü | Ne taşır | §F bağlantısı |
|---|---|---|---|
| F.8.1 | §E.1 (yapay zekâ motoru dosyası) | süreç başlatma + kapatma + kod denetimi | F.1.7 · F.6.1-6.2 |
| F.8.2 | §E.2 (depo testi) | alt süreç hata kontrolü | F.6.2 |
| F.8.3 | §E.3 (akış işleyici) | dosya akışı hata kontrolü | F.5.2-5.4 |
| F.8.4 | §E.4 özet tablosu | üç desenin satır aralıkları | bu bölüm tekrar etmez |

### §F.9 Web araştırma tablosu (bu bölüm)

| # | İddia | URL | Tarih | Durum |
|---|---|---|---|---|
| 1 | PHP `pclose` resmi referansı | https://www.php.net/manual/en/function.pclose.php | 2026-10-13 | ✅ HEAD 200 |
| 2 | PHP `error_get_last` resmi referansı | https://www.php.net/manual/en/function.error-get-last.php | 2026-10-13 | ✅ HEAD 200 |
| 3 | POSIX `<stdio.h>` tanımı | https://pubs.opengroup.org/onlinepubs/9799919799/basedefs/stdio.h.html | 2026-10-13 | ✅ HEAD 200 |

> Doğrulama: `Invoke-WebRequest -Method Head -UseBasicParsing -TimeoutSec 20`;
> kesişim: `.ai/architecture` ağacının tüm markdown dosyalarında desen
> taraması → 0 eşleşme. Aday sistem-hata-listesi başlığı bu turda 404 döndü
> → yazılmadı, §F.6.5'te işaretle taşındı.

### §F.10 Dürüstlük bloğu

Bu bölüm kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt değildir; mevcut
iddia·URL·tarih·durum sayıları aynen kalır. §F'nin eklediği tek yeni
tesciller §F.9'daki üç URL satırıdır; §F.4 ve §F.5 gövde satırları aynı
tescillerin anlatımıdır (ayrı sayılmaz). Yeni repo satırı iddiası 0.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

### §F.11 Kategori karar tablosu — koddan kategoriye tek yönlü eşleme (genişletme)

§F.1'teki kategori haritasına bu bölüm karar sütunu ekler:

| Adım | Soru | Evet | Hayır |
|---|---|---|---|
| 1 | hata, çağrının kendisinden mi döndü (dönüş değeri üzerinden) | kategori tablosuna in | adıma 2 |
| 2 | hata, akış durumundan mı geliyor (akıştan sonraki okuma) | akış kategorisi | adıma 3 |
| 3 | hata, çağrı tamamlandıktan sonra sonradan mı belirir (sonradan-beliren durum) | sonradan-beliren kategori | adıma 4 |
| 4 | hiçbir adım eşleşmiyor | bilinmeyen-kod kategorisi | — |

**Tek yönlü kural:** eşleme koddan kategoriye yapılır; kategoriden koda
geri dönüş simetrik değildir (bir kategori birçok kod barındırır). Geriye
doğru okuma yapıp "bu kategorideki her kod aynı davranır" denmez — denirse
§F.7'deki karar ağacı bozulur, çünkü ağaç kodun kendisine bakar.

### §F.12 İki yorum katmanı — C yüzeyi ile betik yüzeyi arasındaki fark (doktrin)

§F.4-§F.5'te tescillenen iki yüzey arasında yorum farkı vardır; bu fark
kaynak değil, okuma kuralıdır:

| Konu | C yüzeyi | Betik (PHP) yüzeyi |
|---|---|---|
| hata bildirimi | dönüş değeri + hata-numarası değişkeni | uyarı/hata kanalları |
| zamanlama | çağrı anında, akış-başarısızlığı sonradan | yürütme sonunda gözlemlenebilir |
| kurtarma | karar ağacı ile (§F.7) | kanalı yakalama ile |
| tekillik | çağrı başına tek okuma | akış başına tekrar okuma |
| kategori kaybı | hayır (kod korunur) | evet (kod metne dönüşebilir) |

**Okuma kuralı:** iki yüzeyde aynı olay farklı satır sayısında görünür —
C yüzeyi olayı bir kez raporlar, betik yüzeyi yakalama biçimine göre birden
fazla kez. Sayı karşılaştırması yapılırken yüzey birbirine karıştırılmaz;
karşılaştırma yalnız aynı yüzeyin içindedir anlamlıdır.

### §F.13 Dürüstlük bloğu (tamamlama eki 2)

Bu bölüm kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt değildir; mevcut
iddia·URL·tarih·durum sayıları aynen kalır. §F.11-§F.12 ekleri 0 URL, 0 repo
iddiası, 0 ölçüm sonucu içerir; yalnız karar tablosu ve yüzey-karşılaştırma
doktrininden oluşur. §F.9'daki üç URL tescili ve §F.10'daki dürüstlük kaydı
aynen korunur — bu ek tescil satırı eklemez, tescillerin okuma kuralını
tarif eder.

### §F.14 Hata kodu okuma sırası — derinleştirme eki 3 (tarif)

§F.7'deki karar ağacının adımları bu bölümde okuma sırası olarak
pekiştirilir; yeni karar eklenmez, sıra doktrini sabitlenir:

| Adım | Okuma | Kötü okuma biçimi | Sonuç |
|---|---|---|---|
| 1 | dönüş-değerine bak | değere bakmadan hata-numarasına koş | eski değer (stale) okunur |
| 2 | hata-numarasını bir kez oku | numarayı iki kez okur | ikinci okuma farklı değer verebilir |
| 3 | kategori eşlemesini uygula | kategoriye doğrudan kod varsay | simetrik olmayan eşleme ihlal edilir |
| 4 | kararı uygula (yeniden-dene / kurtar / geç) | kararı beklemeden kurtar | geçici arıza kalıcı sanılır |

**Sıra kuralı:** dört adım atlanamaz; adım 1 başarısızsa (belirsiz dönüş)
adım 2'ye geçilmez — belirsizlik ayrı bir sonuçtur, hata-numarası onu
doldurmaz.

### §F.15 Kod ailelerinde tekrar-okuma matrisi (tarif)

Aynı kodun iki okuma arasındaki davranışı, kodun ailesine göre değişir
tarif edilir:

| Aile | Tek okuma | Tekrar okuma | Karar |
|---|---|---|---|
| kalıcı-durum (izin, yokluk) | aynı kod | aynı kod | tekrar anlamlı değil |
| geçici-durum (zaman aşımı, kaynak-bağımlı) | kod A | kod B olası | tekrar anlamlı |
| akış-durumu (akıştan sonraki) | akışa bağlı | akışa bağlı | akış-koruma zorunlu |

**Koruma kuralı:** geçici-durum ailesinde tekrar okumadan önce akışın
korunduğunu doğrula; korumasız tekrar-okuma yeni bir akış başlatır ve
ilk hatanın kaydı kaybolur (§F.12'deki tekillik kuralının ihlali).

### §F.16 Dürüstlük bloğu (derinleştirme eki 3)

Bu ek (§F.14-§F.15) kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt
değildir: 0 URL, 0 repo iddiası, 0 ölçüm sonucu eklenmiştir; yalnız
okuma-sırası doktrini ve tekrar-okuma matrisi genişletilmiştir. §F.9'daki
üç URL tescili ve §F.13 dürüstlük kaydı aynen korunur; ölçüm kapıları
dosyanın §7 kapısına bağlıdır ve bu ekle kapanmaz.

### §F.17 errno okuma kod örnekleri (2026-10-14)

§F.14'teki okuma sırasının iki yüzeyde kod karşılığı. Bloklar derlenebilirlik
iddiası TAŞIMAZ; her örneğin kaynağı §F.18 tablosundadır.

**C yüzeyi — tek okuma deseni (§F.14 · adım 1-2):**

```c
#include <fcntl.h>
#include <errno.h>
#include <string.h>
#include <stdio.h>

int fd = open("/var/log/ornek.log", O_RDONLY);
if (fd == -1) {
    int kayit = errno;                       /* TEK okuma — hemen yerel değişkene */
    fprintf(stderr, "open hatasi (%d): %s\n", kayit, strerror(kayit));
    /* karar: kayit EACCES / ENOENT → kurtar-dalı (§F.7 karar ağacı) */
    return kayit;
}
```

**Betik (PHP) yüzeyi — kanal yakalama deseni (§F.12):**

```php
$cikti = @shell_exec('ornek-komut 2>&1');
if ($cikti === null) {
    $son = error_get_last();                 /* betik yüzeyi — kod metne dönüşür */
    fwrite(STDERR, 'calistirma hatasi: ' . ($son['message'] ?? 'bilinmiyor') . PHP_EOL);
}
```

> Sınır notu: `shell_exec`'in `null` dönüşü **yalnız hata** demek değildir
> (boş çıktı da `null` döndürebilir) — kesin ayrım `proc_open` kanallarıyla
> yapılır (`ornekler.md` §B.2 repo kanıtı). Bu nedenle betik örneği §F.12'deki
> "kod metne dönüşür" doktrinini gösterir, hata-ayrıştırma sözleşmesi kurmaz.

### §F.18 §F.17 kanıt tablosu

| # | Iddia | Resmi kaynak | Tarih | Durum |
|---|---|---|---|---|
| 1 | `errno` hata-numarasi değişkenidir | https://man7.org/linux/man-pages/man3/errno.3.html | 2026-10-14 | ✅ HTTP 200 (HEAD) |
| 2 | `strerror` kodu metne çevirir | https://man7.org/linux/man-pages/man3/strerror.3.html | 2026-10-14 | ✅ HTTP 200 (HEAD) |
| 3 | POSIX `errno.h` tanımı | https://pubs.opengroup.org/onlinepubs/9799919799/basedefs/errno.h.html | 2026-10-14 | ✅ HTTP 200 (HEAD) |
| 4 | PHP `error_get_last` yüzeyi | https://www.php.net/manual/en/function.error-get-last.php | 2026-10-14 | ✅ HTTP 200 (HEAD) |
| 5 | PHP `shell_exec` yüzeyi | https://www.php.net/manual/en/function.shell-exec.php | 2026-10-14 | ✅ HTTP 200 (HEAD) |

Envanter kesişimi (2026-10-14 · 3303 benzersiz URL): satır 1 bu dosyada
**yeni** kanıttır (envanterle kesişmez); satır 2-5 envanterde mevcuttur
(VAR — tekrar sayılmaz). Ayrıntı: `isletim-sistemi-api-dogrulama.md` §S.3.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
