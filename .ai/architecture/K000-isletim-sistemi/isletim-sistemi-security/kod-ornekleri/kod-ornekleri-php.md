---
node_id: K000-isletim-sistemi/isletim-sistemi-security/kod-ornekleri/kod-ornekleri-php
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-security/kod-ornekleri
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-macos
evidence:
  - https://www.php.net/manual/en/book.posix.php
  - https://www.php.net/manual/en/function.posix-setuid.php
  - https://www.php.net/manual/en/function.posix-kill.php
  - https://www.php.net/manual/en/function.posix-setsid.php
  - https://www.php.net/manual/en/function.posix-getpid.php
  - https://www.php.net/manual/en/function.chmod.php
  - https://www.php.net/manual/en/function.umask.php
  - https://windows.php.net/
  - https://suhosin.org/
  - https://www.php.net/releases/5_4_0.php
  - https://php-legacy-docs.zend.com/manual/php5/en/migration53.deprecated
  - https://php-legacy-docs.zend.com/manual/php5/en/features.safe-mode
  - shared/src/Session/SessionConfig.php:20
  - shared/src/Session/SessionConfig.php:35
validation:
  - "2026-10-14: 12 kaynak URL HEAD/GET 200 ile dogrulandi (7'sinin icerigi okundu); repo PHP+JS olcumu 418 dosya (PHP=320, JS=98; vendor/node_modules/.git haric); posix_/chmod/umask/pcntl_/safe_mode/suhosin desenleri 0 isabet (negatif kanit)."
open_questions:
  - "S1: posix_*/chmod/umask OS-guvenlik yuzeyi hedef mimaride devreye alinacak mi? [UNKNOWN]"
  - "S2: Suhosin-NG/Snuffleupagus gibi hardening uzantisi mi, uygulama katmani savunmasi mi? [UNKNOWN]"
group: kod
---

# Kod Örnekleri — PHP × 8 OS

## §0 ELI15

Bu kart, PHP'nin 8 işletim sistemi kombinasyonundaki **OS-güvenlik yüzeyi**
kod desenlerini taşır: POSIX uzantısı (`posix_*`), dosya modu
(`chmod`/`umask`) ve güvenlik uzantısı tarihi (safe_mode, Suhosin). Hepsi
`kaynak: upstream docs` imzalı **sözleşmesel** referans kalıplarıdır.

> **Öncelikli gerçek (dürüstlük):** PHP bu repodun **gerçeğidir** — 2026-10-14
> ölçümünde 418 PHP+JS dosyasının **320'si PHP'dir**. Bu kart "repo'da PHP
> yok" **demez**; ölçülen şey OS-güvenlik işlev yüzeyidir: `posix_*`,
> `chmod(`, `umask`, `pcntl_*`, `safe_mode`, `suhosin` → 418 dosyada
> **0 isabet** (§3, negatif kanıt). Kod blokları bu yüzden repodan alınmış
> değil, php.net doküman kalıplarıdır.

Kombinasyonların 3'ü (Linux, Windows, macOS) upstream kanıt taşır; 5'i (iOS,
Android, Tizen, webOS, ReactOS) doğrulanamadı → `⚠️` · S1/S2 kapatılmaz (§6).

## §1 Kombinasyon matrisi

| Kombinasyon | Upstream durumu | Kanıt (2026-10-14) |
|---|---|---|
| PHP × Linux | POSIX uzantısı IEEE 1003.1 (POSIX.1) arayüzüdür; Windows dışında mevcuttur | book.posix.php (içerik okundu) |
| PHP × Windows | POSIX uzantısı **yok** (resmi not); `chmod` yalnız "read only" bayrağı (kullanıcı notu); resmi derlemeler windows.php.net'de | book.posix.php · chmod notu · windows.php.net |
| PHP × macOS | POSIX "Windows dışı" kapsamı; php.net ayrı bir macOS destek matrisi **yayınlamaz** | book.posix.php; supported-platforms.php → 404 (§5) |
| PHP × iOS | Doğrulanmış upstream kaynak **YOK** | ⚠️ VERIFICATION REQUIRED |
| PHP × Android | Doğrulanmış upstream kaynak **YOK** | ⚠️ VERIFICATION REQUIRED |
| PHP × Tizen | Doğrulanmış upstream kaynak **YOK** | ⚠️ VERIFICATION REQUIRED |
| PHP × webOS | Doğrulanmış upstream kaynak **YOK** | ⚠️ VERIFICATION REQUIRED |
| PHP × ReactOS | Resmi Windows derlemeleri ReactOS uyumluluk iddiası **taşımaz** | ⚠️ VERIFICATION REQUIRED |
| safe_mode (geçmiş) | PHP 5.3'te deprecated, PHP 5.4.0'da kaldırıldı | migration53.deprecated + releases/5_4_0 (içerik okundu) |
| Suhosin (hardening) | Eski Suhosin PHP 5'e taşındı; Suhosin-NG = Snuffleupagus tabanlı WIP | suhosin.org (içerik okundu) |

## §2 Kod örnekleri

> **UYARI (zorunlu):** Aşağıdaki 3 bloğun **hiçbiri bu repodan alınmamıştır.**
> Hepsi `kaynak: upstream docs` imzalı **sözleşmesel** referans kalıplarıdır
> (çalıştırılabilir kod **değildir**); repo karşılığı §3'te negatif kanıt —
> kopyalamadan önce S1 kararı gerekir.

### §2.1 — PHP × Linux (POSIX çekirdek deseni)

```php
<?php
// kaynak: upstream docs - php.net/manual/en/book.posix.php + function.posix-setuid.php + function.posix-kill.php + function.posix-getpid.php + function.posix-setsid.php
// POSIX uzantisi IEEE 1003.1 (POSIX.1) arayuzudur; resmi not: "This extension is not available on Windows platforms".
// resmi uyari: posix_getpwnam() ve dostlari hassas veri dondurur (book.posix.php).
$pid = posix_getpid();   // "Return the current process identifier"
if (function_exists('posix_setuid') && posix_geteuid() === 0) {
    posix_setuid(65534); // "Set the UID of the current process" - root isen asag dusur (or. nobody)
}
posix_kill($pid, 15);    // "Send a signal to a process" - 15 = SIGTERM (POSIX sinyal numarasi)
posix_setsid();          // "Make the current process a session leader" - bagimsiz oturum
// not: satirlar bagimsiz sozlesme kirpintilaridir, bir program degildir -> calistirilmaz, uydurulmaz
?>
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel) | YOK — `posix_[a-z_]+\s*\(` grep'i 418 PHP+JS dosyada **0 isabet** (2026-10-14, §3) |

### §2.2 — PHP × Windows (POSIX yok + chmod sınırı)

```php
<?php
// kaynak: upstream docs - php.net/manual/en/book.posix.php (Not: "This extension is not available on Windows platforms")
//          + php.net/manual/en/function.chmod.php (kullanici notu - Windows davranisi) + windows.php.net (resmi Windows derlemeleri)
if (DIRECTORY_SEPARATOR === '\\') {
    // posix_* bu platformda YOK; function_exists kapisi zorunlu (resmi not)
    if (function_exists('posix_getuid')) { /* yalniz POSIX platformlarinda calisir */ }
    // php.net chmod sayfasindaki kullanici notu: Windows'ta chmod yalnizca
    // "read only" bayragini degistirir ($mode & 0200 yoksa bayrak acilir);
    // calistirilabilirlik dosya uzantisindan, yazma erisimi ACL'lerden gelir.
    chmod($file, 0644); // Windows'ta etki siniri: read-only bayragi
}
?>
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel) | YOK — `posix_` ve `chmod\s*\(` grep'i 0 isabet (2026-10-14); ACL yolu repoda ölçülmedi |

### §2.3 — PHP × macOS (POSIX uyumu + umask disiplini)

```php
<?php
// kaynak: upstream docs - php.net/manual/en/function.umask.php (umask() ornegi + notlar)
//          + php.net/manual/en/book.posix.php (POSIX uzantisi "Windows disi" kapsami)
$old = umask(0077);    // "umask() sets PHP's umask to mask & 0777" - once sikistir
$fh = @fopen($path, 'x'); // 'x' = eksklusif olusturma (dosya zaten varsa basarisiz)
if ($fh !== false) { fwrite($fh, $secret); fclose($fh); }
umask($old);           // eski mask geri - sunucu modunda her istekte zaten geri yuklenir
// resmi not: umask() cok-ipli web sunucularinda kullanilmamali (paylasilan umask);
// onerilen: dosya olusturmadan sonra chmod(). POSIX yuzey kirpintisi icin bkz. §2.1.
// php.net macOS icin ayri destek matrisi yayinlamaz -> iddia "Windows disi" ile sinirli.
?>
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel) | YOK — `umask` grep'i 0 isabet; en yakın repo kanıtı octal izinli `mkdir(` (§3) |

### §2.4 — PHP × iOS (⚠️)

php.net/manual taramasında iOS'a dağıtım/destek iddiası **doğrulanamadı**
(2026-10-14); POSIX "Windows dışı" notu iOS'a genelleme **yapmaz** → `⚠️`.

| Sınıf | Repo karşılığı |
|---|---|
| ⚠️ VERIFICATION REQUIRED | YOK — kombinasyon upstream kanıtsız; repo ölçümü §3'te |

### §2.5 — PHP × Android (⚠️)

php.net/manual taramasında Android'e resmi dağıtım/destek iddiası
**doğrulanamadı** (2026-10-14) → `⚠️`; uydurma API/derleme iddiası YOK.

| Sınıf | Repo karşılığı |
|---|---|
| ⚠️ VERIFICATION REQUIRED | YOK — kombinasyon upstream kanıtsız; repo ölçümü §3'te |

### §2.6 — PHP × Tizen (⚠️)

php.net/manual taramasında Tizen'e resmi dağıtım/destek iddiası
**doğrulanamadı** (2026-10-14) → kombinasyon `⚠️`; örnek yazılmaz.

| Sınıf | Repo karşılığı |
|---|---|
| ⚠️ VERIFICATION REQUIRED | YOK — kombinasyon upstream kanıtsız; repo ölçümü §3'te |

### §2.7 — PHP × webOS (⚠️)

php.net/manual taramasında webOS'a resmi dağıtım/destek iddiası
**doğrulanamadı** (2026-10-14) → kombinasyon `⚠️`; örnek yazılmaz.

| Sınıf | Repo karşılığı |
|---|---|
| ⚠️ VERIFICATION REQUIRED | YOK — kombinasyon upstream kanıtsız; repo ölçümü §3'te |

### §2.8 — PHP × ReactOS (⚠️)

windows.php.net derlemeleri ReactOS uyumluluk iddiası **taşımaz**; manuelde
ReactOS için doğrulanmış kaynak **bulunamadı** (2026-10-14) → `⚠️`.

| Sınıf | Repo karşılığı |
|---|---|
| ⚠️ VERIFICATION REQUIRED | YOK — kombinasyon upstream kanıtsız; repo ölçümü §3'te |

## §3 CoreMusic repo durumu

Tarama: repo kökü (`shared` + `home.coremusic.net` + `media.coremusic.net` +
`bin` ve diğer ağaçlar; `--\` adında bir kopya dizini 2 satırı ikiye katlar),
`*.php,*.js`, `vendor`/`node_modules`/`.git` hariç — **418 dosya (PHP=320 ·
JS=98)**; desen =
`posix_[a-z_]+\s*\(|chmod\s*\(|umask|safe_mode|suhosin|pcntl_[a-z_]+\s*\(`
(2026-10-14).

| Yüzey | Kanıt yöntemi | Sonuç (2026-10-14) |
|---|---|---|
| POSIX çekirdek (`posix_*`) | grep | **0 isabet / 418 dosya** (negatif kanıt) |
| Dosya modu değiştirme (`chmod(`) | grep | **0 isabet** (negatif kanıt) |
| Umask maskesi (`umask`) | grep | **0 isabet** (negatif kanıt) |
| Süreç kontrol (`pcntl_*`) | grep | **0 isabet** (negatif kanıt) |
| safe_mode / Suhosin izi | grep | **0 isabet** (negatif kanıt) |
| octal izin sabiti — `SessionConfig.php:20` | idx 19 birebir | `public const DIR_PERMISSIONS = 0750;` — uygulama katmanı DAC sabiti (POSIX modu; secure yüzey değil) |
| octal izinli mkdir — `SessionConfig.php:35` | idx 34 birebir | `@mkdir($savePath, 0750, true);` — mkdir izni OS'ta `umask` ile birlikte uygulanır |
| izinli mkdir toplamı | grep `mkdir\s*\(` | 10 satır: `0750`×2 (`constants.php:77`, `SessionConfig.php:35`) · `0775`×5 (`api-key-create.php:73` ×2 kopya, `ingest.php:263`, `ingest.php:321`, `CatalogWriter.php:107`) · `0777`×2 (`SessionInitializer.php:31`, `LoggerRedactionTest.php:143`) · `0700`×1 (`Psr3LoggerTest.php:25`) |

**Ölçüm (yeniden üretilebilir):**

```powershell
$src = Get-ChildItem 'C:\www\coremusic.net' -Recurse -File -Include *.php,*.js |
  Where-Object { $_.FullName -notmatch '\\vendor\\|\\node_modules\\|\\\.git\\' }   # beklenti: 418 (PHP 320)
($src | Select-String -Pattern 'posix_|chmod\s*\(|umask|safe_mode|suhosin|pcntl_').Count  # beklenti: 0
```

**Okuma:** PHP repodun gerçek dilidir (320 dosya) — ama OS-güvenlik işlev yüzeyi
(POSIX/UID, chmod, umask) **hiç kullanılmamıştır**; tek dosya-modu teması octal
izinli `mkdir(` satırlarıdır. S1 kararı olmadan §2 blokları repoya taşınmaz.

## §4 Sınır cümleleri

> **K009 ayrımı (bağlayıcı):** OWASP denetimi, auth/CSRF/CSP tasarımı, kripto
> seçimi (ADR-022) → `K009-security`; bu kart yalnız OS yüzeyine özgü PHP
> desenlerini + negatif kanıtı taşır.
> **K010 ayrımı (bağlayıcı):** kimlik doğrulama, oturum, sifre ve MFA →
> `K010-identitiy`; burada yalnız dosya modu ve süreç sinyali desenleri vardır.
> **Komşu dal:** macOS OS kartı → `cekirdek-guvenlik/cekirdek-guvenlik-macos`
> (depends_on); POSIX DAC detayı → `yetki-ve-erisim-modeli-*` kartları.

Ek sınır: §2 blokları **R sınıfıdır** (sözleşmesel, çalıştırılabilir değil);
hardening uzantısı seçimi S2'ye bağlıdır ve bu kartta karar **verilmez**.

## §5 Referans

| URL | İddia | 2026-10-14 durum |
|---|---|---|
| https://www.php.net/manual/en/book.posix.php | POSIX = IEEE 1003.1 arayüzü; *"This extension is not available on Windows platforms"*; hassas veri uyarısı (`posix_getpwnam` ve dostları) | HTTP 200 (HEAD) · içerik okundu |
| https://www.php.net/manual/en/function.posix-setuid.php | `posix_setuid` — Set the UID of the current process (fonksiyon indeksi) | HTTP 200 (HEAD) |
| https://www.php.net/manual/en/function.posix-kill.php | `posix_kill` — Send a signal to a process | HTTP 200 (HEAD) |
| https://www.php.net/manual/en/function.posix-setsid.php | `posix_setsid` — Make the current process a session leader | HTTP 200 (HEAD) |
| https://www.php.net/manual/en/function.posix-getpid.php | `posix_getpid` — Return the current process identifier | HTTP 200 (HEAD) |
| https://www.php.net/manual/en/function.chmod.php | `chmod` imzası + mod örnekleri; Windows davranışı sayfadaki kullanıcı notunda ("read only" bayrağı) | HTTP 200 (HEAD) · içerik okundu |
| https://www.php.net/manual/en/function.umask.php | `umask` imzası; modül modunda istek sonunda geri yüklenir; çok-ipli sunucu uyarısı | HTTP 200 (HEAD) · içerik okundu |
| https://windows.php.net/ | Windows için resmi PHP derlemeleri | HTTP 200 (HEAD) |
| https://suhosin.org/ | Suhosin-NG Snuffleupagus tabanlı WIP (2021 notu); eski Suhosin PHP 5 → suhosin5.suhosin.org | HTTP 200 · içerik okundu |
| https://www.php.net/releases/5_4_0.php | *"Register globals, magic quotes and safe mode were removed"* (PHP 5.4.0) | HTTP 200 · içerik okundu |
| https://php-legacy-docs.zend.com/manual/php5/en/migration53.deprecated | `safe_mode` dahil deprecated ini listesi (PHP 5.3) | HTTP 200 · içerik okundu |
| https://php-legacy-docs.zend.com/manual/php5/en/features.safe-mode | Eski safe_mode sayfası (PHP 5 arşiv manueli) | HTTP 200 · içerik okundu |

> **Reddedilen adaylar (yazılmadı):** `php.net/manual/en/security.safe-mode.php`
> → **404** · `php.net/manual/en/migration53.deprecated.php` → **404** (canlı
> manuel 5.x migration slüklerini taşıdı; içerik PHP 5 arşiv manuelinden
> doğrulandı) · `.../migration54.incompatible.php` ve `.../migration54.php` →
> **404** (5.4.0 kaldırma iddiası releases/5_4_0.php'den alındı) ·
> `php.net/manual/en/windows.php` → **404** (doğru hedef `windows.php.net`) ·
> `php.net/supported-platforms.php` → **404** ("php.net ayrı platform destek
> matrisi yayınlamaz" iddiasının kanıtı) · `suhosin5.suhosin.org` → HEAD **200**
> ama içerik doğrulanamadı (boş gövde) → gövdeye alınmadı; yönlendirme
> suhosin.org'un kendi metninden taşındı.

## §6 Açık sorular

| # | Soru | Durum |
|---|---|---|
| S1 | `posix_*`/`chmod`/`umask` OS-güvenlik yüzeyinin hedef mimaride kullanılıp kullanılmayacağı | **[UNKNOWN]** — bu kartta KAPATMA YOK; mimari sahibi + yeni ADR gerektirir |
| S2 | Hardening uzantısı (Suhosin-NG/Snuffleupagus) mi, uygulama katmanı savunması mı | **[UNKNOWN]** — bu kartta KAPATMA YOK; K009 ile birlikte değerlendirilir |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
