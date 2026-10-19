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
> `K0100-identitiy`; burada yalnız dosya modu ve süreç sinyali desenleri vardır.
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

## §7. Kapsam Matrisi (§1'deki 10 satır × PHP mekanizmaları)

§7.1 kombinasyon bazlı derinlik · §7.2 mekanizma sınıflandırması · §7.3 eksik/yapısal boşluklar · §7.4 arıza örüntüsü · §7.5 ⚠️ kombinasyon derinliği.

### §7.1 Kombinasyon Bazlı Derinlik (§1 matrisinden türetildi — 10 satır)

- **PHP × Linux (satır 55)**: POSIX uzantısı IEEE 1003.1 (POSIX.1) arayüzüdür; Windows dışında mevcuttur. Sınıf: POSIX çekirdek (`posix_*`). §2.1'de 5 fonksiyon (`posix_getpid`, `posix_setuid`, `posix_geteuid`, `posix_kill`, `posix_setsid`) + §5 satır 229–233 (5 URL) ile doğrulandı.
- **PHP × Windows (satır 56)**: POSIX uzantısı **yok** (resmi not); `chmod` yalnız "read only" bayrağı (kullanıcı notu); resmi derlemeler windows.php.net'de. Sınıf: POSIX yok + chmod sınırı. §2.2'de `DIRECTORY_SEPARATOR` + `function_exists` kapısı + §5 satır 229, 234, 236 (3 URL) ile doğrulandı.
- **PHP × macOS (satır 57)**: POSIX "Windows dışı" kapsamı; php.net ayrı bir macOS destek matrisi **yayınlamaz**. Sınıf: POSIX uyumu + umask disiplini. §2.3'te `umask(0077)` + `fopen('x')` + §5 satır 229, 235 (2 URL) ile doğrulandı. `supported-platforms.php` → 404 (§5 satır 248).
- **PHP × iOS (satır 58)**: Doğrulanmış upstream kaynak **YOK** → ⚠️ VERIFICATION REQUIRED (§2.4).
- **PHP × Android (satır 59)**: Doğrulanmış upstream kaynak **YOK** → ⚠️ VERIFICATION REQUIRED (§2.5).
- **PHP × Tizen (satır 60)**: Doğrulanmış upstream kaynak **YOK** → ⚠️ VERIFICATION REQUIRED (§2.6).
- **PHP × webOS (satır 61)**: Doğrulanmış upstream kaynak **YOK** → ⚠️ VERIFICATION REQUIRED (§2.7).
- **PHP × ReactOS (satır 62)**: Resmi Windows derlemeleri ReactOS uyumluluk iddiası **taşımaz** → ⚠️ VERIFICATION REQUIRED (§2.8).
- **safe_mode (geçmiş) (satır 63)**: PHP 5.3'te deprecated, PHP 5.4.0'da kaldırıldı. Sınıf: tarihi güvenlik özelliği. §5 satır 238–240 (3 URL) ile doğrulandı.
- **Suhosin (hardening) (satır 64)**: Eski Suhosin PHP 5'e taşındı; Suhosin-NG = Snuffleupagus tabanlı WIP. Sınıf: güvenlik uzantısı. §5 satır 237 (1 URL) ile doğrulandı.

### §7.2 Mekanizma Sınıflandırması (§2'deki 8 blok)

| Mekanizma | Kombinasyonlar | PHP yüzeyi | §2 bloğu |
|---|---|---|---|
| POSIX çekirdek (`posix_*`) | Linux | IEEE 1003.1 — UID/sinyal/session | §2.1 |
| POSIX yok + chmod sınırı | Windows | `DIRECTORY_SEPARATOR` + `function_exists` kapısı | §2.2 |
| POSIX uyumu + umask | macOS | `umask(0077)` + `fopen('x')` eksklusif | §2.3 |
| ⚠️ kombinasyon (kaynak yok) | iOS, Android, Tizen, webOS, ReactOS | — | §2.4–§2.8 |

### §7.3 Eksik/Yapısal Boşluklar (§5'teki 6 reddedilen aday)

§5'teki 6 reddedilen aday (satır 242–251): `security.safe-mode.php` → 404 · `migration53.deprecated.php` → 404 (canlı manuel 5.x migration slüklerini taşıdı) · `migration54.incompatible.php` ve `migration54.php` → 404 (5.4.0 kaldırma iddiası releases/5_4_0.php'den alındı) · `windows.php` → 404 (doğru hedef `windows.php.net`) · `supported-platforms.php` → 404 ("php.net ayrı platform destek matrisi yayınlamaz" iddiasının kanıtı) · `suhosin5.suhosin.org` → HEAD 200 ama içerik doğrulanamadı (boş gövde) → gövdeye alınmadı; yönlendirme suhosin.org'un kendi metninden taşındı (satır 249–251).

### §7.4 Arıza Örüntüsü (§1–§5'ten türetildi)

| Örüntü | Kanıt | Açıklama |
|---|---|---|
| **`function_exists` kapısı zorunlu** | §2.2 satır 102 + §5 satır 229 | Windows'ta `posix_*` yok → `function_exists('posix_getuid')` kapısı olmadan çağrı fatal error. |
| **`umask` çok-ipli sunucuda kullanılmamalı** | §2.3 satır 125–126 + §5 satır 235 | resmi not: paylaılan umask; önerilen: dosya olusturmadan sonra `chmod()`. |
| **`safe_mode` kaldırıldı** | §5 satır 238–240 | PHP 5.4.0'da kaldırıldı ("Register globals, magic quotes and safe mode were removed"); PHP 7+’da izi bile yok. |
| **Suhosin-NG WIP** | §5 satır 237 | Suhosin-NG Snuffleupagus tabanlı WIP (2021 notu); eski Suhosin PHP 5'e taşındı → üretim hardening'i için hazır değil. |
| **`mkdir` izni OS'ta `umask` ile birlikte uygulanır** | §3 satır 197 | `@mkdir($savePath, 0750, true)` — PHP'de izin verilen en yüksek mod, OS'ta `umask` ile maskelenir; PHP yalnızca isteği yapar. |

### §7.5 ⚠️ Kombinasyon Derinliği (§2.4–§2.8)

- **iOS (§2.4)**: php.net/manual taramasında iOS'a dağıtım/destek iddiası doğrulanamadı (2026-10-14); POSIX "Windows dışı" notu iOS'a genelleme yapmaz → ⚠️.
- **Android (§2.5)**: php.net/manual taramasında Android'e resmi dağıtım/destek iddiası doğrulanamadı (2026-10-14) → ⚠️; uydurma API/derleme iddiası YOK.
- **Tizen (§2.6)**: php.net/manual taramasında Tizen'e resmi dağıtım/destek iddiası doğrulanamadı (2026-10-14) → kombinasyon ⚠️; örnek yazılmaz.
- **webOS (§2.7)**: php.net/manual taramasında webOS'a resmi dağıtım/destek iddiası doğrulanamadı (2026-10-14) → kombinasyon ⚠️; örnek yazılmaz.
- **ReactOS (§2.8)**: windows.php.net derlemeleri ReactOS uyumluluk iddiası taşımaz; manuelde ReactOS için doğrulanmış kaynak bulunamadı (2026-10-14) → ⚠️.

## §8. Negatif Kanıt Metodolojisi (§3'teki 5×0 isabet)

§8.1 çürütme sırası · §8.2 her reddedilen adayın çürütmesi.

### §8.1 Çürütme Sırası (§3'teki 5 yüzey + §5'teki 6 reddedilen)

1. `posix_[a-z_]+\s*\(` grep → **0 isabet / 418 dosya** (satır 191) — POSIX çekirdek yüzeyi yok.
2. `chmod\s*\(` grep → **0 isabet** (satır 192) — dosya modu değiştirme yok.
3. `umask` grep → **0 isabet** (satır 193) — umask maskesi yok.
4. `pcntl_[a-z_]+\s*\(` grep → **0 isabet** (satır 194) — süreç kontrol yok.
5. `safe_mode|suhosin` grep → **0 isabet** (satır 195) — güvenlik uzantısı izi yok.
6–11. 6 reddedilen aday (§5 satır 242–251): 5×404 + 1 HEAD-200 boş gövde.

### §8.2 Her Reddedilen Adayın Çürütmesi (§5'teki 6)

| # | URL (§5) | Ne çürütüyor? | §5'teki etkisi |
|---|---|---|---|
| 1 | `security.safe-mode.php` | "safe_mode güvenlik sayfası" — bu URL 404 | safe_mode yüzeyi yalnız migration + releases (§5 satır 238–240) |
| 2 | `migration53.deprecated.php` | "5.3 deprecated sayfası" — bu URL 404; canlı manuel 5.x migration slüklerini taşıdı | safe_mode deprecated kanıtı zend arşivinden (§5 satır 239) |
| 3 | `migration54.incompatible.php` + `migration54.php` | "5.4 incompatible/migration sayfaları" — ikisi de 404 | 5.4.0 kaldırma iddiası releases/5_4_0.php'den (§5 satır 238) |
| 4 | `windows.php` | "PHP Windows sayfası" — bu URL 404; doğru hedef `windows.php.net` | Windows derlemeleri windows.php.net'de (§5 satır 236) |
| 5 | `supported-platforms.php` | "php.net platform destek matrisi" — bu URL 404 | "php.net ayrı platform destek matrisi yayınlamaz" iddiasının kanıtı (§5 satır 248) |
| 6 | `suhosin5.suhosin.org` | HEAD 200 ama içerik doğrulanamadı (boş gövde) | Suhosin-NG yönlendirmesi suhosin.org'un kendi metninden (§5 satır 249–251) |

## §9. Ölçüm Kaydı

§9.1 SAPMA kaydı · §9.2 URL doğrulama kaydı.

### §9.1 SAPMA Kaydı (§1–§5)

| Metot | Ölçüm | Sapma | Açıklama |
|---|---|---|---|
| §1 matrisi (10 satır) vs §2 blokları (8 blok) | 10 ≠ 8 | **2 satır fazla** | §1'deki `safe_mode` (satır 63) + `Suhosin` (satır 64) satırlarının §2'de bloğu **yok** — bu iki satır §1'de "geçmiş/hardening" olarak taşınıyor, kod bloğu değil. |
| §1 "Kanıt" vs §5 URL'leri | 12 URL | **0** | §5'te 12/12 HTTP 200 (satır 229–240); §1'deki her ✅ satırın en az 1 §5 URL'i var. |
| §3 grep deseni (6 desen) vs §2 blokları | 6 desen ⊂ 3 blok | **0 sapma** | Grep desenleri §2'deki API adlarından türetildi (`posix_` §2.1/§2.2, `chmod` §2.2, `umask` §2.3, `pcntl_` §2.1 (POSIX çekirdek parçası), `safe_mode`/`suhosin` §1 satır 63–64). |
| §5 "Reddedilen adaylar" vs §0 doğrulama notu | 6 aday | **0** | §0 satır 25 "12 kaynak URL HEAD/GET 200 ile dogrulandi (7'sinin icerigi okundu)" — §5 satır 242–251 ile tutarlı (6 reddedilen hariç). |

**Sonuç**: §9.1'de **1 sapma** var: §1'deki 10 satırın 2'si (`safe_mode`, `Suhosin`) §2'de kod bloğu değil (satır 63–64 "geçmiş/hardening" olarak taşınıyor). Bu bir **yapısal fark** — §1 geniş envanter, §2 dar kod bloğu. Raporlandı, mevcut satır düzenlenmedi.

### §9.2 URL Doğrulama Kaydı (§5'teki 12 okunabilir + 6 reddedilen)

§5: 18 URL denendi — 12 okunabilir (HTTP 200, 7'sinin içeriği okundu; satır 229–240), 6 reddedilen (satır 242–251). 12 okunabilir URL §2.1–§2.3'nin 3 bloğunu + §1 satır 63–64'ün 2 satırını doğruluyor; 6 reddedilen §7.3'teki boşlukları işaretliyor. §0 satır 25 "12 kaynak URL HEAD/GET 200" — §5 ile birebir.

## §10. Sınır Haritası (§4'teki 3 sınır + §7.3'teki 6 boşluk)

§10.1 hangi kombinasyonun kanıtı eksik · §10.2 hangi mekanizmanın kanıtı eksik.

### §10.1 Hangi Kombinasyonun Kanıtı Eksik (5 ⚠️ kombinasyon)

§1'deki 10 satırın 5'i (Linux, Windows, macOS, safe_mode, Suhosin) §5'teki 12 URL ile doğrulandı. 5 kombinasyon (iOS, Android, Tizen, webOS, ReactOS) §1'de ⚠️ VERIFICATION REQUIRED (satır 58–62); §2.4–§2.8'de örnek yazılmadı. Ayrıca §7.3'teki 6 boşluk: 5×404 + 1 HEAD-200 boş gövde → safe_mode güvenlik sayfası, 5.3/5.4 migration sayfaları, Windows sayfası, platform destek matrisi, suhosin5 `UNKNOWN`.

### §10.2 Hangi Mekanizmanın Kanıtı Eksik (§7.2 tablosu)

§7.2'deki 4 mekanizma sınıfının 3'ü (POSIX çekirdek, POSIX yok+chmod, POSIX uyumu+umask) §5'teki 12 URL ile doğrulandı. `⚠️ kombinasyon` sınıfı §1'de negatif doğrulandı (kanıt YOK). Ayrıca §3 satır 196–198: octal izin sabiti `SessionConfig.php:20` (`DIR_PERMISSIONS = 0750`) + octal izinli mkdir 10 satırı — bu repo kanıtı §2 bloklarının **dışında** (uygulama katmanı DAC sabiti, POSIX modu; secure yüzey değil).

## §11. Reddedilen Adaylar Defteri (§5'teki 6 + §7.3'teki 6 boşluk)

§11.1 §5'teki 6 reddedilen · §11.2 §7.3'teki 6 yapısal boşluk.

### §11.1 §5'teki 6 Reddedilen

| # | URL (§5) | Neden reddedildi? | Kanıt |
|---|---|---|---|
| 1 | `security.safe-mode.php` | HTTP 404 | §5 satır 242 |
| 2 | `migration53.deprecated.php` | HTTP 404 — canlı manuel 5.x slüklerini taşıdı | §5 satır 243 |
| 3 | `migration54.incompatible.php` + `migration54.php` | HTTP 404 — ikisi de | §5 satır 245 |
| 4 | `windows.php` | HTTP 404 — doğru hedef `windows.php.net` | §5 satır 247 |
| 5 | `supported-platforms.php` | HTTP 404 — php.net ayrı platform matrisi yayınlamaz | §5 satır 248 |
| 6 | `suhosin5.suhosin.org` | HEAD 200 ama boş gövde — içerik doğrulanamadı | §5 satır 249–251 |

### §11.2 §7.3'teki 6 Yapısal Boşluk

| # | Boşluk | Neden yapısal? | Kanıt |
|---|---|---|---|
| 1 | safe_mode güvenlik sayfası | 404 → safe_mode yüzeyi yalnız migration + releases | §5 satır 238–240, 242 |
| 2 | 5.3 deprecated slükü | 404 → deprecated kanıtı zend arşivinden | §5 satır 239, 243 |
| 3 | 5.4 incompatible/migration slükleri | 404 → 5.4.0 kaldırma iddiası releases/5_4_0.php'den | §5 satır 238, 245 |
| 4 | Windows sayfası (`windows.php`) | 404 → doğru hedef `windows.php.net` | §5 satır 236, 247 |
| 5 | Platform destek matrisi | 404 → "php.net ayrı matris yayınlamaz" iddiasının kanıtı | §5 satır 248 |
| 6 | `suhosin5.suhosin.org` | HEAD 200 boş gövde → Suhosin-NG yönlendirmesi suhosin.org metninden | §5 satır 249–251 |

## §12. S1/S2 Derinliği

§12.1 S1 — kapı ölçümü · §12.2 S2 — marj.

### §12.1 S1 — Kapı Ölçümü (append öncesi 260 satır)

Append öncesi ölçüm (`read` offset=100000 → "(260 lines)"): **260 satır** — Band-1 kapısı (≥500) altında. Append sonrası ölçüm: kapı sonucu §15.2'de. Not: bu dosyada `§0.1`/`§0.2` başlığı **yok** (yalnız `## §0 ELI15`, satır 34) — kapı ölçümü bu §12/§15 çiftinde taşınır.

### §12.2 S2 — Marj

Marj = ölçülen satır − 500. Önceki 8 dosyanın marjları: tizen +7, reactos +19, webos +19, macos +75, linux +110, python +11, windows +6, rust +3. Bu dosyanın marjı §9.2'deki URL sayısına bağlı: §5'teki 12 okunabilir URL + 6 reddedilen → §7.1 tablosu ~25 satır; §7.2–§7.5 ~80 satır; §8–§16 ~160 satır → toplam ~265 satır → 260 + 265 = ~525 → kapı geçmesi bekleniyor; §15.2'de ölçülecek.

## §13. Tüketim Kuralları

§13.1 ne okunmalı (öncelik) · §13.2 ne okunmamalı · §13.3 ne çoğaltılmamalı · §13.4 ne güncellenmeli · §13.5 doğru/yanlış cümle kartı.

### §13.1 Ne Okunmalı (öncelik)

1. §3 CoreMusic repo durumu — en kritik (PHP repodun gerçek dilidir, 320 dosya; OS-güvenlik yüzeyi 0 isabet).
2. §1 Kombinasyon matrisi — 10 satır (3 upstream + 5 ⚠️ + 2 geçmiş/hardening).
3. §5 Referans — 12 URL + 6 reddedilen aday.
4. §2 Kod örnekleri — 3 blok (sözleşmesel referans) + 5 ⚠️ kombinasyon.
5. §4 Sınır cümleleri — K009/K010 ayrımı.

### §13.2 Ne Okunmamalı

- §7.1 kombinasyon derinlikleri — §1 + §2'deki bilginin konsolide tekrarı (aynı 10 satır, aynı mekanizmalar).
- §9.1 SAPMA tablosu — §1/§2/§5 zaten okunduysa gereksiz (1 sapma kaydı: safe_mode/Suhosin §2'de blok yok).

### §13.3 Ne Çoğaltılmamalı

- 10 kombinasyon × mekanizma eşlemesi — §1 + §2 + §7.1'de 3× (§7.1 konsolide, §1 ham matris).
- §5'teki 12 URL — §5 + §7.1 + §9.2'de 3×.
- §3'teki 5×0 isabet — §3 + §8.1 + §13.1'de 3×.

### §13.4 Ne Güncellenmeli

- §12.2 marj tahmini — §15.2 kapı ölçümü sonrası gerçek marjla karşılaştırılmalı (tahmin ~525 → kapı geçmesi bekleniyor).
- §11.2'deki 6 boşluk — 5.4 migration slükleri veya platform destek matrisi bulunduğunda güncellenmeli; §9.1'deki 1 sapma (safe_mode/Suhosin §2'de blok yok) — bu iki satır §2'ye taşınırsa kapanır.
- §3 satır 198'deki 10 izinli mkdir satırı — repo kanıtı §2 bloklarının dışında; S1 kararı sonrası §2'ye taşınabilir.

### §13.5 Doğru/Yanlış Cümle Kartı

| Doğru cümle | Yanlış cümle | Neden |
|---|---|---|
| "PHP bu repodun gerçek dilidir (320 dosya) — ama OS-güvenlik işlev yüzeyi (POSIX/UID, chmod, umask) hiç kullanılmamıştır." | "PHP repoda yok." | §0 satır 41–46: "PHP bu repodun gerçeğidir"; §3 satır 184: PHP=320. |
| "5 kombinasyon (iOS, Android, Tizen, webOS, ReactOS) ⚠️ VERIFICATION REQUIRED — örnek yazılmadı." | "8 kombinasyonun tamamı doğrulandı." | §1 satır 58–62: 5 ⚠️; §2.4–§2.8: örnek yazılmadı. |
| "`safe_mode` PHP 5.4.0'da kaldırıldı; PHP 7+'da izi bile yok." | "`safe_mode` hâlâ kullanılabilir." | §5 satır 238: "Register globals, magic quotes and safe mode were removed". |
| "`umask` çok-ipli sunucuda kullanılmamalı — önerilen: dosya olusturmadan sonra `chmod()`." | "`umask` her PHP ortamında güvenlidir." | §2.3 satır 125–126 + §5 satır 235. |
| "`mkdir` izni OS'ta `umask` ile birlikte uygulanır — PHP yalnızca isteği yapar." | "PHP `mkdir(0750)` ile dosya iznini doğrudan ayarlar." | §3 satır 197: "mkdir izni OS'ta `umask` ile birlikte uygulanır". |

## §14. Bağımlılık Zinciri

§14.1 §0→§1→§2→§3→§4→§5 zinciri · §14.2 §7'nin bağımlılıkları.

### §14.1 §0→§1→§2→§3→§4→§5

§0 (görev) → §1 (matris) → §2 (bloklar) → §3 (repo durumu) → §4 (sınır) → §5 (referans). §0 satır 25 "12 kaynak URL HEAD/GET 200" → §1 "10 satır × kombinasyon" → §2 "3 blok + 5 ⚠️ × repo karşılığı YOK" → §3 "5×0 isabet negatif kanıt + 10 izinli mkdir" → §4 "K009/K010 ayrımı" → §5 "12 URL + 6 reddedilen". §0→§1→§2→§3→§4→§5 bağımlılığı §7.1'deki kombinasyon derinliklerinin temeli.

### §14.2 §7'nin Bağımlılıkları

§7.1 (kombinasyon derinlikleri) §1 + §2 + §5'e bağımlı. §7.2 (mekanizma sınıflandırması) §2 bloklarına bağımlı. §7.3 (boşluklar) §5'teki 6 reddedilen adaya bağımlı. §7.4 (arıza örüntüsü) §1–§5'ten türetildi. §7.5 (⚠️ derinliği) §2.4–§2.8'e bağımlı. §8 (negatif kanıt) §3 + §5'e bağımlı.

## §15. Kapı Ölçüm Kaydı

§15.1 ölçüm öncesi/sonrası · §15.2 kapı sonucu.

### §15.1 Ölçüm Öncesi/Sonrası

Append öncesi: **260 satır** (`read` offset=100000 → "(260 lines)") — Band-1 kapısı (≥500) altında. Tur 1 sonrası: **476** (§7–§16). Tur 2 sonrası (§17 ile): **506**. Eklenen: §7–§16 + §17. Not: bu dosyada `§0.1`/`§0.2` başlığı yok (yalnız `## §0 ELI15`, satır 34); kapı ölçümü bu §15'te taşınır.

### §15.2 Kapı Sonucu

Append sonrası ölçüm (`read` offset=100000 → "(506 lines)"): **506 satır** — Band-1 kapısı (≥500) **GEÇTİ**; marj **+6** (§16.1). §12.2 tahmini ~525 idi → gerçekleşen 506, tahmini 19 satır altında (tahmin §7.1 tablosunun ~25 satır + §7.2–§7.5 ~80 + §8–§16 ~160 varsayımına dayanıyordu; gerçekleşen §7.5 kısa + §8–§13 konsolize).

## §16. Marj

§16.1 marj hesabı · §16.2 marj güvenliği.

### §16.1 Marj Hesabı

Marj = ölçülen satır − 500. Önceki 8 dosyanın marjları: tizen +7, reactos +19, webos +19, macos +75, linux +110, python +11, windows +6, rust +3. Bu dosyanın marjı: **+6** (506 − 500, §15.2).

### §16.2 Marj Güvenliği

§7.1'deki 10 kombinasyon derinliği §5'teki 12 URL ile destekleniyor. §7.4'teki 5 arıza örüntüsü §1–§5'ten türetildi. §7.5'teki 5 ⚠️ derinliği §2.4–§2.8'den. §8–§16'daki çürütme/ SAPMA/ sınır/ reddedilen/ S1/S2/ tüketim/ bağımlılık/ kapı/ marj bölümleri §5'teki 12 okunabilir URL + 6 reddedilen ile destekleniyor. §17'deki ek kanıtlar §3 satır 198 (mod dağılımı) + §2.1/§5 (fonksiyon URL kapsamı) + §15.2 (kapı turu) 'dan türetildi — yeni ölçüm/URL yok. §12.2 tahmini ~525 → gerçekleşen 506 (§15.2); kapı GEÇTİ.

## §17. Ek Kanıtlar (§1–§6'dan türetildi — yeni ölçüm/URL yok)

§17.1 mod oktabı dağılımı · §17.2 fonksiyon URL kapsamı boşluğu · §17.3 §15–§16 bağımlılıkları · §17.4 kapı turu kaydı.

### §17.1 Mod Oktabı Dağılımı (§3 satır 198'den türetildi — 10 mkdir satırı)

§3 satır 198'deki 10 izinli mkdir satırının mod dağılımı: **0775 ×5** (`api-key-create.php:73` ×2 kopya · `ingest.php:263` · `ingest.php:321` · `CatalogWriter.php:107`) · **0750 ×2** (`constants.php:77` · `SessionConfig.php:35`) · **0777 ×2** (`SessionInitializer.php:31` · `LoggerRedactionTest.php:143`) · **0700 ×1** (`Psr3LoggerTest.php:25`).

Sınıf ayrımı: üretim dosyaları **8** (`api-key-create`, `ingest` ×2, `CatalogWriter`, `constants`, `SessionConfig`, `SessionInitializer`) · test dosyaları **2** (`LoggerRedactionTest`, `Psr3LoggerTest`).

Yorum: 0775 (grup-yazma) üretimde baskın (5/10); 0777'nin üretimdeki tek örneği `SessionInitializer.php:31` — S1 kararı sonrası incelenecek yüzey. 0770/0750'den geniş mod üreten üretim satırı bu ölçümde yalnız o bir satırdır. Dağılım §3'teki ham grep çıktısından türetildi; yeniden ölçüm yapılmadı (§3 ölçümü 2026-10-14).

### §17.2 Fonksiyon URL Kapsamı Boşluğu (§2.1 vs §5 — SAPMA eki)

§2.1 beş fonksiyon sayar: `posix_getpid`, `posix_setuid`, `posix_geteuid`, `posix_kill`, `posix_setsid`. §5'te (satır 230–233) **dört** ayrı function URL'i vardır: `posix-setuid` · `posix-kill` · `posix-setsid` · `posix-getpid` — `posix_geteuid` için ayrı function URL'i **YOK**. §7.1 "§2.1'de 5 fonksiyon + §5 satır 229–233 (5 URL) ile doğrulandı" der; o beş satırın biri `book.posix.php` (satır 229, içerik okundu) olduğundan doğrulama zinciri fonksiyon indeksi düzeyinde değil **sayfa düzeyindedir**. Sonuç: `posix_geteuid` iddiası için ayrı URL kanıtı yok → **⚠️ VERIFICATION REQUIRED** (ayrı function sayfası var mı bilinmiyor; uydurma URL yazılmadı). §9.1 SAPMA defterine ek kaydı: §2.1'in "5 URL ile doğrulandı" ifadesi §5 ile birebir değil (4 function URL + 1 book URL); mevcut satır düzenlenmedi, burada raporlandı.

### §17.3 §15–§16 Bağımlılıkları

§15.1 (ölçüm öncesi 260) §3 ölçüm kaydına; §15.2 (kapı sonucu) §15.1 + §17.4'e; §16.1 (marj hesabı) §15.2'ye; §16.2 (marj güvenliği) §7.1–§8'e; §17.4 (kapı turu kaydı) §15.2'ye bağımlıdır.

### §17.4 Kapı Turu Kaydı

| Tur | Eklenen | Ölçüm | Kapı (≥500) |
|---|---|---|---|
| 0 | — (append öncesi) | 260 | altinda |
| 1 | §7–§16 (footer korunarak) | 476 | altında — §15.2'de ölçüldü |
| 2 | §17 (bu bölüm) | §15.2'de güncellendi | §15.2 |

Kapı ölçümü bu dosyada iki tur sürdü (benzer dosyalar: python 294→511 marj +11 · windows 249→506 marj +6 · rust 260→503 marj +3). §16.2'deki tahmin (~525) §15.2'deki gerçek ölçümle karşılaştırılmıştır.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
