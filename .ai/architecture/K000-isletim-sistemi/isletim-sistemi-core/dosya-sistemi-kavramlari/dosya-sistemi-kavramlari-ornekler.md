---
node_id: ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-core/dosya-sistemi-kavramlari/dosya-sistemi-kavramlari-ornekler.md"
node_type: "subdomain-dal-ornekler"
status: draft
parent_id: ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-core/index.md"
children: []
depends_on:
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-core/dosya-sistemi-kavramlari/dosya-sistemi-kavramlari-surucu.md"
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-core/isletim-sistemi-core-dogrulama.md"
evidence:
  - "home.coremusic.net/include/Stream/MusicStreamHandler.php:175-208 (Read, 2026-10-13)"
  - "shared/src/Session/SessionInitializer.php:29-31 (Read, 2026-10-13)"
  - "shared/src/Log/FileHandler.php:248-252 (Read, 2026-10-13)"
  - "https://man7.org/linux/man-pages/man2/open.2.html (HTTP 200, 2026-10-13)"
  - "https://man7.org/linux/man-pages/man2/fcntl.2.html (HTTP 200, 2026-10-13)"
validation:
  - "scope: kapsam §B.3 — kod örnekleri + anti-pattern + test eşlemesi (draft, 2026-10-13)"
  - "tüm kod blokları gerçek dosyalardan Read ile alındı; uydurma satır numarası yok"
open_questions:
  - "Q-F2: PHP LOCK_EX → OS mekanizması (flock/fcntl) bilinmiyor"
  - "Q-F5: kapsam §B.3 eski dosya adları (vfss-ve-dosya-taniticisi.md) bu dosya listesiyle çelişiyor"
  - "D-6/D-7 testleri çalıştırılmadı — yalnız eşlendi"
group: kod
---

# Dosya Sistemi Kavramları — Örnekler (Kademe-5)

> **Rol:** anayasa + sürücüdeki kuralların **gerçek repo kodu** üzerinde gösterimi.
> Her örnek gerçek dosyadan `Read` ile alınmıştır (2026-10-13); uydurma satır
> numarası, uydurma fonksiyon adı yoktur.

## §0 ELI15 (6 satır)

Kuralları okumak yetmez; kodda nasıl göründüğünü görmek gerekir. Bu dosya üç
kova sunar: (1) doğru çalışan örnekler, (2) tespit edilmiş anti-pattern'ler,
(3) test senaryolarıyla eşlenmiş doğrulama adımları. Kovalar `grep`/`Read`
kanıtıyla desteklenir; her iddia dosya:satır taşır. Sayılar (örn. `LOCK_EX`
eşleşmesi = 6) 2026-10-13 tarihli ölçümün sonucudur.

## §1 Örnek Envanteri

| # | Dosya:satır | Konu | Sınıf |
|---|---|---|---|
| E-1 | `home.coremusic.net/include/Stream/MusicStreamHandler.php:175-208` | yol çözümleme + containment | iyi |
| E-2 | `shared/src/Session/SessionConfig.php:33-35` | `mkdir 0750` + `@` | anti-pattern (06a) |
| E-3 | `shared/src/Session/SessionInitializer.php:29-31` | `mkdir 0777` + `@` | anti-pattern (06b) |
| E-4 | `shared/src/Log/FileHandler.php:248-252` | kilitli ekleme | iyi (Q-F2 hariç) |
| E-5 | `shared/src/Component/TemplateEngine.php:73-74` | `LOCK_EX` + dosya yoksa boş | iyi |
| E-6 | `shared/src/PageRouter/PageRouterKernel.php:133-134` | önbellek yazma + kilit | iyi |
| E-7 | `home.coremusic.net/.htaccess:1-37` | sunucu düzeyi denetim | iyi |
| E-8 | `media.coremusic.net/bin/ingest.php:102-117` | `kokIcindeMi` kök containment | iyi |
| E-9 | `shared/src/Security/SecurityHelper.php:54` | gerçek yol daraltma | iyi |
| E-10 | `shared/src/Middleware/PermissionMiddleware.php:30-41` | dizin izin denetimi | iyi |

## §2 İyi Örnekler

### §2.1 E-1 — yol çözümleme + containment (MusicStreamHandler.php)

```php
// L178-181: URL-decode + boşluk normalizasyonu
$temizYol = urldecode($yol);
$temizYol = str_replace(' ', '_', $temizYol);

// L184-187: NUL bayt reddi (çözümleme öncesi)
if (strpos($temizYol, "\0") !== false) {
    http_response_code(400);
    exit('Geçersiz yol');
}

// L189: .. kontrolü
if (!empty($girisAltDizin) && strpos($temizYol, '..') !== false) { ... }

// L191-194: realpath — sembolik link + .. çözümleme (VFS katmanı)
$gercekYol = realpath($yol);
if ($gercekYol === false) { ... }

// L197-201: prefix denetimi — kök içinde mi?
if (substr($gercekYol, 0, strlen($kokDizin)) !== $kokDizin) { ... }

// L204-206: mod denetimi
if (!is_readable($gercekYol)) { ... }
```

**Neden doğru:** savunma derinliği — NUL → `..` → `realpath` → prefix → mod,
beş ayrı katman; anayasa §3.4 (path_resolution) ile birebir eşleşir.

### §2.2 E-4 — kilitli ekleme (Log/FileHandler.php:248-252)

```php
$fh = fopen($yol, 'a');
flock($fh, LOCK_EX);
fwrite($fh, $satir);
flock($fh, LOCK_UN);
fclose($fh);
```

**Doğru olan:** ekleme kipi (`a` → `O_APPEND|O_CREAT|O_WRONLY`, open.2) + açık
kilit → seri yazım. **Eksik:** `fwrite` dönüş değeri ve `fclose` hatası
denetlenmiyor; `fsync` yok (anayasa Q-FS-4). **UNKNOWN:** `LOCK_EX` → OS
mekanizması (Q-F2).

### §2.3 E-6 — önbellek yazma (PageRouterKernel.php:133-134)

```php
file_put_contents($yol, $json, LOCK_EX);
```

Tek satırda yazma + kilit — PHP'nin atomik yazım yolu. `realpath` kullanımı
aynı dosyada L:105, 116, 134 (grep 2026-10-13).

### §2.4 E-8 — kök containment (media.coremusic.net/bin/ingest.php:102-117)

```php
function kokIcindeMi(string $gercekYol, string $kok): bool {
    // realpath sonrası prefix denetimi — E-1 ile aynı desen
}
```

İki ayrı uygulama (home/coremusic handler vs media ingest) **aynı deseni**
kullanıyor → ortak yardımcıya çıkarılabilir (refactor notu, kural değil).

### §2.5 E-10 — dizin izin denetimi (PermissionMiddleware.php:30-41)

```php
if (!is_dir($dizin) || !is_readable($dizin)) {
    ...   // dizin yoksa veya okunamıyorsa reddet
}
```

**Neden doğru:** `stat` (var mı) + okuma izni tek koşulda; başarısızlıkta
reddetme yüzeyi §6'daki 403 akışına bağlı.

### §2.6 E-5 — dosya yoksa üret + kilit (TemplateEngine.php:73-74)

```php
if (file_exists($yol)) {
    // var olan şablon üzerinde LOCK_EX korumalı işlem
}
```

**Doğru:** tek yazıcı kilidi (`LOCK_EX`, grep §3.3). **Risk:** `file_exists`
↔ açma arası TOCTOU penceresi (sürücü §2.7); şablonlar yalnız yönetici
yazdığı için pratik risk sınırlı — **değerlendirme, VERIFICATION REQUIRED**.

### §2.7 E-9 — gerçek yol daraltma (SecurityHelper.php:54)

```php
$gercekYol = realpath($yol);   // L54
```

`realpath` → sembolik link + `..` tek hamlede çözülür (anayasa §3.4). Bu
yardımcı, E-1/E-8'deki aynı adımı paylaşırsa ortaklaşa kullanılabilir
(refactor notu).

### §2.8 E-7 — sunucu düzeyi ikinci katman (.htaccess:1-37)

```apache
Options -Indexes                          # dizin listeleme kapalı
<FilesMatch "^\.">  Require all denied     # .env, .git vb. gizli dosya reddi
RewriteCond %{REQUEST_FILENAME} !-f        # dosya yoksa SPA router'a devir
RewriteCond %{REQUEST_FILENAME} !-d
```

**Önemi:** dosya OS'ta `0644` ile okunabilir olsa bile Apache reddedebilir →
**iki bağımsız denetim katmanı** (savunma derinliği). Eksik: bu katman yalnız
HTTP isteklerini kapsar — CLI/cron yolları doğrudan FS'e gider (kapsam dışı
not).

### §2.9 E-2/E-3 — mod karşılaştırması (kırmızı örnek)

| Dosya | Kod | Gerçek mod (umask `0022` varsayımı) | Sorun |
|---|---|---|---|
| SessionConfig.php:33-35 | `mkdir(..., 0750, true)` | `0750` | tutarlı (grup okur/yazar) |
| SessionInitializer.php:29-31 | `mkdir(..., 0777, true)` | `0755` | herkes okur — umask'e bağlı |

> Gerçek değer umask ile belirlenir; repo'da `umask()` çağrısı **0 isabet**
> (grep §3.3) → süreç umask'i sunucu yapılandırmasında **UNKNOWN**.

### §2.10 E-11 — CLI yüzeyi kilitli yazma (bin/api-key-create.php, grep §3.3)

`LOCK_EX` 6 isabetinden biri de CLI betiğinde. CLI süreçleri daemon'a göre
kısa ömürlüdür → kilit yalnız süreç içi koruma sağlar; çok-istemci senaryosu
CLI için **kapsam dışı** (not).

### §2.11 Örnek ↔ anayasa eşlemesi

| Örnek | Anayasa § | Mekanizma |
|---|---|---|
| E-1 | §3.4 | path_resolution |
| E-2/E-3 | §3.5 + §4 | mkdir mod + umask |
| E-4/E-6/E-11 | §3.3 | kilit (flock/POSIX) |
| E-5 | §3.3 + §3.5 | kilit + TOCTOU |
| E-7 | §5 komşu | sunucu denetimi |
| E-8/E-9 | §3.4 | realpath + containment |
| E-10 | §3.5 | mod denetimi |

> Hiçbir örnek `fsync` (§3.6) kullanmıyor → anayasa mekanizması repo'da
> **temsilsiz** (negatif kanıt, §3.2 AP-07).

## §3 Anti-Pattern'ler

### §3.1 AP-06 — çelişkili mod + hata susturma

```php
// SessionConfig.php:33-35
if (!is_dir($oturumKok)) { @mkdir($oturumKok, 0750, true); }
// SessionInitializer.php:29-31
if (!is_dir($dizin))     { @mkdir($dizin, 0777, true); }
```

| Sorun | Açıklama |
|---|---|
| mod tutarsızlığı | `0750` vs `0777` — aynı iş, iki semantik |
| `@` hata yutma | `mkdir` başarısızsa sessiz devam → sonra `EACCES`/`ENOENT` |
| umask bağımlılığı | gerçek mod umask'e bağlı → ölçülmeli (sürücü §5) |

**Düzeltme yönü (kural değil):** tek sabit mod (`0700` önerilir, anayasa §4) +
`@` yerine `if (!mkdir(...)) { hata }`.

### §3.2 AP-07 — fsync yok (envanter negatifi)

2026-10-13 grep'i: repo'da `fsync` **0 isabet**. Kritik dosyalar (log,
önbellek) `fclose`'e dayanıyor → `EIO`/güç kesintisinde veri kaybı riski
(anayasa Q-FS-4).

### §3.3 AP-08 — `LOCK_EX` envanteri (grep, 2026-10-13)

| Desen | İsabet | Dosyalar |
|---|---|---|
| `LOCK_EX` | 6 | TemplateEngine:73 · FileHandler:251 · PageRouterKernel:134 · CatalogWriter:107/111/113 · PermissionMiddleware · bin/api-key-create |
| `flock(` | 0 | — |
| `lockf(` | 0 | — |
| `umask(` (PHP) | 0 | — |
| `is_link` | 1 | MusicStreamHandler:191 |
| `realpath` | 10 | MusicStreamHandler · PageRouterKernel · TemplateEngine · ingest.php |

> `flock`/`lockf` = 0 isabet PHP kaynağından; `LOCK_EX` 6'sı **bayrak kullanımı**,
> altında hangi syscall olduğu **UNKNOWN** (Q-F2) — "PHP flock çağırıyor"
> denemez, kanıtsızlıktır.

### §3.4 AP-09 — denetlenmemiş yazım (FileHandler, §2.2)

`fwrite($fh, $satir)` dönüşü denetlenmiyor → kısa yazım (`false`/kısmi)
sessiz geçer. Log kaybı = gözlemlenemeyen veri kaybı. Kanıt: Read
L248-252 (dönüş değeri karşılaştırması yok).

### §3.5 AP-10 — kod tekrarı: containment (E-1 vs E-8)

Aynı algoritma (realpath + prefix) iki bağımsız dosyada: `MusicStreamHandler`
L191-201 ve `ingest.php` L102-117. Drift riski: biri güncellenirse diğeri
kalır. Kural değil — **refactor notu** (anayasa Q-FS-6).

### §3.6 AP-11 — TOCTOU penceresi (E-1, §2.1)

`is_readable` (adım 6) ↔ `readfile` (adım 7) arasında dosya değişebilir.
Pratikte URL parametresi sınırlı + dosyalar sabit içerikli → risk **düşük
değerlendirmesi, VERIFICATION REQUIRED**. Sıfır-pencere çözümü (tek
`fopen` + denetim) önerilir — kural değil, not.

### §3.7 Anti-pattern özeti

| AP | Bulgu | Ağırlık | Kanıt |
|---|---|---|---|
| AP-06 | çelişkili mod + `@` | yüksek | §3.1 |
| AP-07 | fsync yok | orta (dayanıklılık) | §3.2 |
| AP-08 | kilit türü UNKNOWN | bilgi | §3.3 |
| AP-09 | `fwrite` denetimsiz | orta | §3.4 |
| AP-10 | containment kod tekrarı | düşük | §3.5 |
| AP-11 | TOCTOU penceresi | düşük | §3.6 |

> Ağırlıklar **değerlendirme**dir (ölçüm yok) — VERIFICATION REQUIRED.

## §4 Test Senaryoları Eşlemesi (dogrulama §A.3)

| Senaryo | Konu | İlgili örnek | Beklenen |
|---|---|---|---|
| D-1 | `..` / sembolik link kaçışı | E-1 | 400/403, dosya okunmaz |
| D-2 | URL-decode kaçışı (`%2e%2e`) | E-1 L178 + L189 | reddedilir |
| D-3 | NUL bayt (`%00`) | E-1 L184-187 | 400 |
| D-4 | kök dışı gerçek yol | E-1 L197-201 / E-8 | reddedilir |
| D-5 | `mkdir 0777` gerçek mod | E-3 | stat ile umask sonrası mod |
| D-6 | eşzamanlı kilitli yazma | E-4/E-6 | veri kaybı 0 |
| D-7 | yazma izni olmayan dosya + fd sınırı | E-10 / `chmod 444` | `EACCES`/`EMFILE` |

> Senaryo tanımları `-dogrulama.md` §A.3'tedir; bu dosya yalnız eşler
> (çalıştırma kanıtı yok → **status: draft**).

### §4.1 Senaryo ↔ anti-pattern matrisi

| Senaryo | Hedef AP | Örnek | Ne ölçer |
|---|---|---|---|
| D-1..D-4 | AP-11 (pencere) | E-1 | reddi katmanları bütünüyle çalışıyor mu |
| D-5 | AP-06 | E-2/E-3 | gerçek mod + `@` yutması |
| D-6 | AP-08/AP-09 | E-4/E-6 | kilit + yazım bütünlüğü |
| D-7 | AP-07 (fd/fsync dışı) | E-10 | mod reddi + EMFILE yüzeyi |

### §4.2 Negatif kanıt defteri (bu dalın "yok"ları)

| Aranan | İsabet | Anlamı |
|---|---|---|
| `fsync` | 0 | dayanıklılık mekanizması temsilsiz (AP-07) |
| `flock(` (doğrudan) | 0 | PHP yalnız `LOCK_EX` bayrağı kullanıyor |
| `lockf(` | 0 | POSIX advisory lock doğrudan çağrılmıyor |
| `umask(` (runtime) | 0 | mod yalnız dosya bağlamında sabit |
| `chdir` | 0 | göreli yol tuzağı yok (grep 2026-10-13, PHP) |
| `symlink(` | 0 | repo sembolik link üretmiyor (PHP taraması 2026-10-13) |
| `link(` (dosya) | 0 | eşleşen 6 satır `$this->link()` **HTML metodu** — dosya link'i değil |

> Negatifler **kanıttır**: "yok" demek, "var" iddiasından daha güçlüdür —
> uydurma tamamlama yapılmaz.

## §5 Platform Karşılaştırması (örnek düzeyi)

| Mekanizma | Linux (repo) | Windows | macOS |
|---|---|---|---|
| ekleme kipi | `fopen('a')` → `O_APPEND` | `FILE_APPEND_DATA` | `O_APPEND` |
| kilit | `LOCK_EX` → **Q-F2: UNKNOWN** | `LockFile` (LockFile API, HTTP 200 2026-10-13) | `flock`/`fcntl` |
| gerçek yol | `realpath()` | `GetFinalPathNameByHandle` — **VERIFICATION REQUIRED** | `realpath()` |
| dizin oluşturma | `mkdir(mode)` | `CreateDirectory` (mod yok) — **VERIFICATION REQUIRED** | `mkdir(mode)` |
| dayanıklılık | `fsync` (repo: 0) | `FlushFileBuffers` — **VERIFICATION REQUIRED** | `fsync` |

## §6 Sözlük Bağlantısı

Terim tanımları anayasa §6'dadır (22 terim). Bu dosya yalnız kod yüzü:

| Terim (anayasa §6) | Kod yüzü | Örnek |
|---|---|---|
| path_resolution | `realpath` + prefix | E-1, E-8, E-9 |
| inode | mod denetimi (`is_readable`) | E-1, E-10 |
| fd | `fopen`/`fclose` | E-4 |
| advisory lock | `LOCK_EX` | E-4, E-5, E-6, E-11 |
| umask | `mkdir` modu | E-2, E-3 |
| TOCTOU | `file_exists` ↔ açma | E-5, §3.6 |
| containment | `substr` prefix | E-1, E-8 |
| fsync | — (repo'da yok) | AP-07 |

## §7 Kapsam Dışı

- Kavram tanımı (→ anayasa), sürüş kılavuzu (→ sürücü), doğrulama senaryosu
  tanımları (→ `-dogrulama.md` §A.3), sınır kararları (→ `-kapsam.md` §B.3).
- `.htaccess` Apache modülleri (rewrite motoru içeriği).
- İzin/capability/ACL örnekleri → `izin-ve-guvenlik-kavramlari-ornekler.md`
  (dal sınırı: bu dosya yalnız **dosya sistemi** yüzeyi).

### §7.1 Komşu sınırı (bu dosyanın bittiği yer)

| Yüzey | Sorumlu dosya | Bu dosyada değil çünkü |
|---|---|---|
| syscall imzaları (`open`, `flock`) | `dosya-sistemi-api` (varsa) | sınır: kullanım → api |
| ACL/capability/`setuid` | `izin-ve-guvenlik-*` | konu izin |
| kilit çekirdek içi uygulaması | `dosya-sistemi-kernel` | sınır: kavram → kernel |
| yönlendirme/kontrol akışı | `dosya-sistemi-guvenlik` | sınır: denetim akışı → güvenlik |

> Not: bu dal dosya listesi kapsam §B.3'te 4 dosyadır (index + 3 içerik);
> tabloda adı geçen `-api`/`-kernel`/`-guvenlik` dosyaları **bu görevin
> listesinde yok** — varlıkları **UNKNOWN** (kapsam §B.3 kontrolü gerekir).

## §8 Açık Sorular (dosya içi)

| ID | Soru | Durum |
|---|---|---|
| Q-F2 | PHP `LOCK_EX` → OS mekanizması | UNKNOWN |
| Q-F5 | kapsam §B.3 eski dosya adı çelişkisi | açık |
| Q-F4 | PHP fd havuzu sınırı | UNKNOWN |
| Q-FS-2 | `0777` sonrası gerçek mod (sunucu umask'i) | UNKNOWN |
| Q-FS-6 | containment ortak yardımcısına çıkarım | refactor notu |
| AP-09 | `fwrite` kısa yazım gözlemi | kanıt yok (denetim yok) |

## §9 Kaynakça

| # | URL | Doğrulama |
|---|---|---|
| 1 | https://man7.org/linux/man-pages/man2/open.2.html | 2026-10-13 HTTP 200 |
| 2 | https://man7.org/linux/man-pages/man2/fcntl.2.html | 2026-10-13 HTTP 200 |
| 3 | https://man7.org/linux/man-pages/man7/path_resolution.7.html | envanter (`-dogrulama` §E.3) |
| 4 | https://learn.microsoft.com/en-us/windows/win32/api/fileapi/nf-fileapi-lockfile | 2026-10-13 HTTP 200 |

> Kanıt disiplini: bu dosyadaki her dosya:satır referansı `Read`, her sayı
> (6/10/1/0) 2026-10-13 `grep` çıktısıdır; doğrulanmayan iddia `⚠️`,
> bilinmeyen `UNKNOWN` ile işaretlidir (anayasa §9).

## §10 Senaryo akışı — dosyaya erişim kararı, istek bazında (2026-10-13)

> Bu bölüm §2'deki iyi örneklerin **senaryo karşılıklarını** taşır: her satır
> bir istek/bir karar. Repo satırları bu oturumda yeniden okundu; komşu daldan
> gelen her teyit kaynak gösterilir.

### §10.1 Zincir teyidi — `resolvePath` 8 adım (cross-ref: `izin-...-ornekler` §1.1)

| # | Satır | Karar | Red |
|---|---|---|---|
| 1 | 175-178 | boş/`\0` | 404 |
| 2 | 180-183 | `..` segmenti | 403 |
| 3 | 185-187 | `is_link` | 403 |
| 4 | 189-192 | `realpath`/`is_file` | 404 |
| 5 | 194-197 | kök çözülemedi | 404 |
| 6 | 199-202 | kök dışı | 403 |
| 7 | 204-206 | `is_readable` | 403 |
| 8 | 208 | karar bitti | — |

§3'teki AP-03/`realpath` anti-pattern'inin **doğru hâli** budur: kaçış kontrolü
string'de (adım 2), doğrulama kanonik yolda (adım 4-6), izin en sonda (adım 7).

### §10.2 Şablon dosyası erişimi — `TemplateEngine` hata modları

`shared/src/Component/TemplateEngine.php`:

| Senaryo | Satır | Sonuç |
|---|---|---|
| şablon dosyası yok | 37-39 | `RuntimeException("Template not found")` — dosya hiç `require` edilmez |
| cache yazımı başarısız (disk/kilit) | 74-76 | `file_put_contents(..., LOCK_EX) === false` → `RuntimeException` |
| render hatası | 44-52 | `ob_end_clean()` + istisna yeniden yayılır (buffer sızıntısı yok) |
| render sonrası temizlik | 87-89 | `is_file` ise `@unlink` — geçici dosya silinir |

### §10.3 Dizin hazırlama örnekleri — gerçek `mkdir` desenleri

| Dosya:satır | Desen | Mod |
|---|---|---|
| `bin/api-key-create.php:73` | `!is_dir($dir) && !@mkdir($dir, 0775, true) && !is_dir($dir)` | 0775 |
| `media.coremusic.net/src/Media/CatalogWriter.php:107` | aynı üçlü koruma | 0775 |
| `media.coremusic.net/bin/ingest.php:263` | rapor dizini | 0775 |
| `media.coremusic.net/bin/ingest.php:321` | hedef dizin | 0775 |
| `shared/src/Session/SessionConfig.php:34-36` | `!is_dir` → `@mkdir($savePath, 0750, true)` | **0750** |
| `home.coremusic.net/config/constants.php:76` | `!is_dir($sessionPath)` (hazırlık koşulu) | — (mod satırı bu dosyada okunmadı) |

Ortak kalıp: **double-check** (`!is_dir && !@mkdir && !is_dir`) — yarış koşulu
koruması; §2.3'teki `flock` tartışmasının mkdir eşleniği. Mod tutarsızlığı
H-4 (0775/0750/0777/0700) bu örneklerle görünür hale gelir.

### §10.4 Sunucu katmanı — dosya erişim kararı (özet; tam matris: `izin-...-ornekler` §5)

| Karar | Apache | IIS |
|---|---|---|
| hassas uzantı reddi | `Require all denied` (home:18-20 vb., 6 isabet/3 dosya) | `CustomResponse 403` (home:24-27 vb., 6/3) |
| PHP doğrudan erişim | FilesMatch 3/4 | **0/4** (UNKNOWN: Q-OR3) |
| dizin listeleme | `Options -Indexes` 4/4 | `directoryBrowse false` **1/4** |
| `.DS_Store` | htaccess listesinde VAR | web.config listesinde YOK |

### §10.5 Hata kataloğu — hangi hata hangi katmandan doğar

| Katman | Hata | Ürettiği şey |
|---|---|---|
| uygulama (yol) | kaçış/kanonikleşme reddi | 403/404 (`fail()`) |
| uygulama (şablon) | dosya yok / cache yazılamaz | `RuntimeException` (§10.2) |
| uygulama (dizin) | hazırlık yarışı | çift `is_dir` koruması (§10.3) |
| sunucu | uzantı/klasör reddi | 403 (§10.4) |
| OS | `EACCES`/`ENOENT`/`ELOOP` | `stat(2)` ERRORS — §10.6 no 2 |

### §10.6 Ek kaynakça — bu bölümün eklediği 2 URL

| # | Kaynak | URL | Durum |
|---|---|---|---|
| 1 | `ls(1)` — izin sütunlarını okuma aracı (§10.3 modlarının gözlemi) | https://man7.org/linux/man-pages/man1/ls.1.html | bu dosyaya **yeni** · 2026-10-13 HTTP 200 |
| 2 | `stat(2)` — `EACCES`/`ENOENT`, `lstat` | https://man7.org/linux/man-pages/man2/stat.2.html | bu dosyaya **yeni** · envanter reuse (`isletim-sistemi-api-dogrulama.md:201`) · 2026-10-13 HTTP 200 |

### §10.7 Kapanış — bu bölümün ölçüm ve handover satırı

Ölçüm: §10.1-§10.4 sayımları 2026-10-13 repo ölçümüdür (read-only); §10.4'teki
sayılar `izin-...-ornekler` §3-§5'te tek tek kanıtlanmıştır, burada özet
olarak taşınır. Handover: §10.2 hata modları test dalına (TS eşlemesi §4'tedir);
§10.3 desenleri `-observability`/refactor notlarına (Q-FS-6 containment
ortak yardımcısı ile aynı soru ailesi). `⚠️` sayısı bu bölümde: **1**
(§10.4 "PHP engeli UNKNOWN" hücresi).

### §10.8 Gözlem planı — §10 senaryolarının test karşılığı (PLANNED)

| # | Gözlem | Beklenen (kod okuması) | Sonuç |
|---|---|---|---|
| G1 | `TemplateEngine::renderString` disk doluyken | `RuntimeException` (L75) — cache yazılamazsa render başlamaz | PLANNED (depо dışı) |
| G2 | oturum dizini ilk oluşumunda `ls -l` | `drwxr-x---` (0750) — umask düşürmediyse | PLANNED |
| G3 | aynı dizine eşzamanlı `mkdir` (double-check) | yarışta tek süreç kazanır, ikinciği `is_dir` ile geçer | PLANNED |
| G4 | `home` altından `include/x.php` doğrudan istek | 403 (FilesMatch) | PLANNED |
| G5 | aynı istek IIS ayağı | **UNKNOWN** (web.config'te FilesMatch 0 — Q-OR3) | PLANNED |
| G6 | `assets` kökünde `.md`/`.json` | erişim açık olabilir (Q-OR2) | PLANNED |

Kural: sonuçlar `-dogrulama` dosyasına işlenir; bu dosyaya gözlem sonucu
yazılmadan "çalışıyor" iddiası girmez (zero-hallucination).

## §11. EK — Ölçüm ve test kapısı teyidi (2026-10-13)

- Dolu satır ölçüm komutu (salt-okunur): `Get-Content -Encoding UTF8 <dosya> | Where-Object { $_ -ne '' }).Count`.
- EK öncesi bu dosya: 479 toplam / 370 dolu satır; EK sonrası ≥380 hedefi.
- 4 `.htaccess` satır ölçümü: api 42 · auth 37 · home 37 · assets 47.
- 4 `web.config` satır ölçümü: api 41 · assets 37 · auth 43 · home 43.
- `Options -Indexes` 4/4; `Require all denied` kod 6 (api:20,25 · auth:19,23 · home:19,23); assets 0 (Q-OR2).
- `directoryBrowse` 1/4 (assets:35); `Permissions-Policy` 3/4; `CustomResponse 403` kod 6.
- PHP envanter 17 isabet (`is_dir` 14 · `is_readable` 3 · `is_writable` 0).
- Bu dosyaya eklenen doğrulanmış URL: 2 (§10.6); ikisi de 2026-10-13 HTTP 200 ile teyitli.
- G1-G6 gözlem planı PLANNED; gözlem sonucu yazılmadan "çalışıyor" iddiası bu dosyaya girmez.
- Açık kayıtlar: Q-OR1 (api:19 `json` yok) · Q-OR2 (assets Require 0) · Q-OR3 (IIS FilesMatch 0) · Q-OR4 (`--\` kopya dizini) — dördü de açık.
- T1-T6 / G1-G6 sonuçları `-dogrulama` dosyasına işlenir; bu dosya sonuç sahibi değildir.
- `git commit` bu oturumda ATILMADI — değişiklikler working tree'dedir (commit orkestratöre ait).

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
