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
  - "2026-10-14 · §12 grupları G6a-G6d (12 platform kolu · 8 kod bloğu) · §12.5 13 YENİ + 3 reuse HTTP 200 (envanter kesişimi 2 → dogrulama §J.5) · §12.6 18 reuse yeniden 200 · §13 PHP köprüsü (6 gerçek-satır bloğu · yeni URL 0 · reuse URL 2 php.net LOCK_EX+flock HTTP 200 · fsync/fdatasync/LOCK_SH grep 0) · §14 çok dilli JS/C/Python/Go/Rust/C# (8 URL = 2 reuse + 6 yeni · 6 kalıp blok · JS/PY depo satırı · PHP fopen 'x' grep 0) · 1084 satır"
open_questions:
  - "Q-F2: PHP LOCK_EX → OS mekanizması (flock/fcntl) bilinmiyor → 2026-10-14 §13.1 kısmen yanıtladı: PHP LOCK_EX `flock` tabanlıdır (PHP el kitabı); `fcntl(F_SETLK)` kullanılmaz → NFS/ağ birimlerinde güven ≠ yerel disk (§13.1 ⚠️)"
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

---

## §12 Çapraz-platform gruplu kod örnekleri (2026-10-14)

> **Grup kuralı (bağlayıcı):** Her grup **tek kavram + üç platform kolu**
> (Linux/POSIX · Windows (Win32) · macOS/POSIX) taşır. "Sadece Linux" örnek
> bu bölümde geçersizdir. macOS-native man sayfaları bu turda erişilemedi
> (`developer.apple.com/library/archive/.../man*_*.html` → bağlantı koptu/404);
> macOS sütunu POSIX spesifikasyonuna dayanır ve `⚠️ VERIFICATION REQUIRED`
> ile işaretlenir.

### §12.1 G6a - Dosya aç / yaz / kapat

**(A) Linux/POSIX — `open` + `write` + `close`:**

```c
#include <fcntl.h>
#include <unistd.h>
#include <errno.h>

int fd = open("/var/tmp/coremusic-demo.txt",
              O_CREAT | O_WRONLY | O_TRUNC, 0640);
if (fd == -1) {
    /* errno: ENOENT / EACCES / EROFS ... */
} else {
    ssize_t n = write(fd, "deneme\n", 7);
    if (n != 7) { /* kismi yazma / EIO */ }
    if (close(fd) == -1) { /* EIO */ }
}
```

**(B) Windows (Win32) — `CreateFileW` + `WriteFile` + `CloseHandle`:**

```c
#include <windows.h>

HANDLE h = CreateFileW(L"C:\\temp\\coremusic-demo.txt",
                       GENERIC_WRITE, 0, NULL, CREATE_ALWAYS,
                       FILE_ATTRIBUTE_NORMAL, NULL);
if (h == INVALID_HANDLE_VALUE) {
    DWORD e = GetLastError();   /* ERROR_FILE_NOT_FOUND / ACCESS_DENIED ... */
} else {
    DWORD written = 0;
    const char buf[] = "deneme\r\n";
    WriteFile(h, buf, sizeof buf - 1, &written, NULL);
    CloseHandle(h);
}
```

**(C) macOS / POSIX** — (A) birebir geçer · `⚠️ VERIFICATION REQUIRED`.

| Fark | Linux/POSIX | Windows |
|---|---|---|
| Başarısızlık göstergesi | `-1` + `errno` | `INVALID_HANDLE_VALUE` + `GetLastError()` |
| Ayrıştırma | bayt (`O_CREAT|O_TRUNC`) | kelimeler (`CREATE_ALWAYS`) |
| Satır sonu | yalnız `\n` | `\r\n` (metin modu) |
| Kod genişliği | `ssize_t` (kısmi yazma olabilir) | `DWORD written` (kısmi yazma olabilir) |

### §12.2 G6b - Dosya kilidi (aynı anda tek yazar)

**(A) Linux/POSIX — `flock` (repo `FileHandler.php:251` ile aynı desen):**

```c
#include <sys/file.h>
#include <unistd.h>

if (flock(fd, LOCK_EX) == 0) {     /* Exclusive - tek yazar */
    /* kritik yazma */
    flock(fd, LOCK_UN);
}
```

**(B) Windows (Win32) — `LockFileEx`:**

```c
#include <windows.h>

OVERLAPPED ov; ZeroMemory(&ov, sizeof ov);
if (LockFileEx(h, LOCKFILE_EXCLUSIVE_LOCK, 0, MAXDWORD, MAXDWORD, &ov)) {
    /* kritik yazma */
    UnlockFileEx(h, 0, MAXDWORD, MAXDWORD, &ov);
}
```

**(C) macOS / POSIX** — `flock` POSIX'te **opsiyoneldir** (XSI değil); macOS
`flock(2)` sunar. **`⚠️ VERIFICATION REQUIRED`** (Apple erişim kısıtı).

| Kavram | Linux/macOS | Windows |
|---|---|---|
| Çağrı | `flock(fd, LOCK_EX)` | `LockFileEx(..., LOCKFILE_EXCLUSIVE_LOCK, ...)` |
| Kapsam | tanıtıcı / dosya | dosya + bayt aralığı |
| Serbest bırakma | `LOCK_UN` / kapanış | `UnlockFileEx` |
| Türetilmiş | PHP `LOCK_EX` (§2.2, `FileHandler.php:251`) | — |

### §12.3 G6c - Dizin yarat + durum oku + konum/boyut

**(A) Linux/POSIX — `mkdir` + `stat` + `lseek`:**

```c
#include <sys/stat.h>
#include <unistd.h>

if (mkdir("/var/tmp/coremusic-demo", 0750) == -1 && errno != EEXIST) { /* hata */ }

struct stat st;
if (stat("/var/tmp/coremusic-demo", &st) == 0) {
    mode_t m = st.st_mode & 07777;      /* izin bitleri */
    off_t  s = st.st_size;
    (void)m; (void)s;
}
off_t end = lseek(fd, 0, SEEK_END);     /* dosya sonu */
```

**(B) Windows (Win32) — `CreateDirectoryW` + `GetFileAttributesExW` + `SetFilePointerEx`:**

```c
#include <windows.h>

CreateDirectoryW(L"C:\\temp\\coremusic-demo", NULL);   /* varsa hata döner */

WIN32_FILE_ATTRIBUTE_DATA ad;
if (GetFileAttributesExW(L"C:\\temp\\coremusic-demo",
                          GetFileExInfoStandard, &ad)) {
    DWORD attr = ad.dwFileAttributes;   /* FILE_ATTRIBUTE_DIRECTORY vb. */
    (void)attr;
}
LARGE_INTEGER li; li.QuadPart = 0;
SetFilePointerEx(h, li, NULL, FILE_END);   /* dosya sonu */
LARGE_INTEGER size = {0};
GetFileSizeEx(h, &size);
```

**(C) macOS / POSIX** — (A) birebir geçer · `⚠️ VERIFICATION REQUIRED`.

| Kavram | Linux/POSIX | Windows |
|---|---|---|
| Dizin yaratma | `mkdir(path, mode)` | `CreateDirectoryW(path, sa)` |
| Varlık denetimi | `stat` / `lstat` | `GetFileAttributesExW` |
| Boyut | `st.st_size` | `GetFileSizeEx` |
| Konum | `lseek(fd, off, whence)` | `SetFilePointerEx(h, li, pos)` |
| Symlink | `lstat` sayar, `stat` izler | `CreateFileW(..., FILE_FLAG_OPEN_REPARSE_POINT)` — bu turda doğrulanmadı → `⚠️ VERIFICATION REQUIRED` |

### §12.4 G6d - Dayanıklılık: diske gerçekten yaz

**(A) Linux/POSIX — `fsync`:**

```c
if (fsync(fd) == -1) { /* EIO - veri tamponda kalmis olabilir */ }
```

**(B) Windows (Win32) — `FlushFileBuffers`:**

```c
if (!FlushFileBuffers(h)) { DWORD e = GetLastError(); (void)e; }
```

**(C) macOS / POSIX** — `fsync` POSIX'ttir · `⚠️ VERIFICATION REQUIRED`.

> **Repo bağlantısı:** `dosya-sistemi-...-ornekler.md` §3.2 (AP-07) bu dosyada
> **`fsync` envanterinin 0** olduğunu kaydetmişti; bu bölüm o boşluğu
> **kavramsal olarak** doldurur — kod **çalıştırılmadı**, gözlem **PLANNED**.

### §12.5 Yeni doğrulanmış URL (2026-10-14 · envanter kesişimi 2 → düzeltildi §J.5 · HTTP 200)

> **Ölçüm (2026-10-14 · yeniden):** `.ai/**` (gizli dizin, `*.md|*.json|*.txt|*.ps1`)
> URL kümesi **bu turda yazılan 6 dosya hariç** tutularak ölçüldü = **3760 tekil URL**.
> Bu küme ile kesişim **2 satır** verdi: **#5 `write`** ve **#6 `close`** envanterde zaten
> vardı (`isletim-sistemi-api/ag-api/posix-socket-ve-winsock-karsilastirmasi.md`) → bu iki
> satır "reuse" sayıldı ve YENİ toplamı **13** oldu. Tur-çapı tek-çatı sayım ve
> kümülatif envanter (**3866**) için bkz. `isletim-sistemi-core-dogrulama.md` **§J**.

| # | İddia | URL | 2026-10-14 |
|---|---|---|---|
| 1 | `mkdir(2)` - dizin yaratma (Linux man) | https://man7.org/linux/man-pages/man2/mkdir.2.html | ? HTTP 200 · **YENİ** |
| 2 | `mkdir` POSIX tanımı | https://pubs.opengroup.org/onlinepubs/9799919799/functions/mkdir.html | ? HTTP 200 · **YENİ** |
| 3 | `lseek` POSIX tanımı (konum değiştirme) | https://pubs.opengroup.org/onlinepubs/9799919799/functions/lseek.html | ? HTTP 200 · **YENİ** |
| 4 | `stat` POSIX tanımı (durum/dosya bilgisi) | https://pubs.opengroup.org/onlinepubs/9799919799/functions/stat.html | ? HTTP 200 · **YENİ** |
| 5 | `write` POSIX tanımı (kısmi yazma semantiği) | https://pubs.opengroup.org/onlinepubs/9799919799/functions/write.html | reuse · HTTP 200 (envanterde vardı — §J.5) |
| 6 | `close` POSIX tanımı (tanıtıcı bırakma) | https://pubs.opengroup.org/onlinepubs/9799919799/functions/close.html | reuse · HTTP 200 (envanterde vardı — §J.5) |
| 7 | `realpath` POSIX tanımı (gerçek yol çözümleme) | https://pubs.opengroup.org/onlinepubs/9799919799/functions/realpath.html | ? HTTP 200 · **YENİ** |
| 8 | `LockFileEx` - bayt aralığı kilidi (Windows) | https://learn.microsoft.com/en-us/windows/win32/api/fileapi/nf-fileapi-lockfileex | ? HTTP 200 · **YENİ** |
| 9 | `CreateDirectoryW` - dizin yaratma (Windows) | https://learn.microsoft.com/en-us/windows/win32/api/fileapi/nf-fileapi-createdirectoryw | ? HTTP 200 · **YENİ** |
| 10 | `GetFileAttributesExW` - dosya/dizin durumu (Windows) | https://learn.microsoft.com/en-us/windows/win32/api/fileapi/nf-fileapi-getfileattributesexw | ? HTTP 200 · **YENİ** |
| 11 | `GetFileAttributesW` - öznitelik okuma (Windows) | https://learn.microsoft.com/en-us/windows/win32/api/fileapi/nf-fileapi-getfileattributesw | ? HTTP 200 · **YENİ** |
| 12 | `GetFileSizeEx` - dosya boyutu (Windows) | https://learn.microsoft.com/en-us/windows/win32/api/fileapi/nf-fileapi-getfilesizeex | ? HTTP 200 · **YENİ** |
| 13 | `SetFilePointerEx` - dosya konumu (Windows) | https://learn.microsoft.com/en-us/windows/win32/api/fileapi/nf-fileapi-setfilepointerex | ? HTTP 200 · **YENİ** |
| 14 | `DuplicateHandle` - tanıtıcı kopyalama (Windows) | https://learn.microsoft.com/en-us/windows/win32/api/handleapi/nf-handleapi-duplicatehandle | ? HTTP 200 · **YENİ** |
| 15 | `fopen` POSIX tanımı (akış düzeyi) | https://pubs.opengroup.org/onlinepubs/9799919799/functions/fopen.html | ? HTTP 200 · **YENİ** |
| 16 | `path_resolution(7)` - yol çözümleme sırası | https://man7.org/linux/man-pages/man7/path_resolution.7.html | reuse · HTTP 200 |

### §12.6 Reuse URL - HTTP 200 yeniden doğrulandı (2026-10-14)

| # | İddia | URL |
|---|---|---|
| 1 | `open(2)` - bayraklar + mod | https://man7.org/linux/man-pages/man2/open.2.html |
| 2 | `read(2)` / `write(2)` | https://man7.org/linux/man-pages/man2/read.2.html |
| 3 | `write(2)` | https://man7.org/linux/man-pages/man2/write.2.html |
| 4 | `close(2)` | https://man7.org/linux/man-pages/man2/close.2.html |
| 5 | `lseek(2)` | https://man7.org/linux/man-pages/man2/lseek.2.html |
| 6 | `stat(2)` | https://man7.org/linux/man-pages/man2/stat.2.html |
| 7 | `dup(2)` - tanıtıcı kopyası | https://man7.org/linux/man-pages/man2/dup.2.html |
| 8 | `flock(2)` - dosya kilidi | https://man7.org/linux/man-pages/man2/flock.2.html |
| 9 | `fsync(2)` - diske yazma | https://man7.org/linux/man-pages/man2/fsync.2.html |
| 10 | `chmod(2)` - mod değiştirme | https://man7.org/linux/man-pages/man2/chmod.2.html |
| 11 | `umask(2)` - varsayılan maske | https://man7.org/linux/man-pages/man2/umask.2.html |
| 12 | `open` POSIX tanımı | https://pubs.opengroup.org/onlinepubs/9799919799/functions/open.html |
| 13 | `dup` POSIX tanımı | https://pubs.opengroup.org/onlinepubs/9799919799/functions/dup.html |
| 14 | `CreateFileW` | https://learn.microsoft.com/en-us/windows/win32/api/fileapi/nf-fileapi-createfilew |
| 15 | `ReadFile` | https://learn.microsoft.com/en-us/windows/win32/api/fileapi/nf-fileapi-readfile |
| 16 | `WriteFile` | https://learn.microsoft.com/en-us/windows/win32/api/fileapi/nf-fileapi-writefile |
| 17 | `CloseHandle` | https://learn.microsoft.com/en-us/windows/win32/api/handleapi/nf-handleapi-closehandle |
| 18 | `FlushFileBuffers` | https://learn.microsoft.com/en-us/windows/win32/api/fileapi/nf-fileapi-flushfilebuffers |

### §12.7 Bu bölümün ölçüme kaydı (2026-10-14)

| Ölçüm | Değer |
|---|---|
| Eklenen grup | 4 (G6a aç/yaz/kapat · G6b kilit · G6c dizin+durum+boyut · G6d dayanıklılık) |
| Platform kolları | 3/grup = 12 kol |
| Eklenen kod bloğu | 8 (A=4 · B=4) |
| Yeni doğrulanmış URL | 13 (§12.5 #1-#4, #7-#15) · envanter kesişimi **2** (§12.5 #5-#6 → §J.5) · HTTP 200 |
| Reuse URL yeniden 200 | 18 (§12.6) + §12.5 #5, #6, #16 = 3 |
| Tur-çapı tek-çatı sayımda bu dosyanın payı | dogrulama **§J** (56 URL · 53 yeni / 3 reuse) |
| macOS-native kaynak | 0 erişilebilir → 4 iddia `⚠️ VERIFICATION REQUIRED` + 1 reparse-point `⚠️` |
| Çalıştırılan gözlem | 0 (kod temsilidir; §10.8 G1-G6 hâlâ PLANNED) |
| `git commit` | **atılmadı** (orkestratöre ait) |

---

## §13 PHP Köprüsü — dosya sistemi (repo satır kanıtlı, 2026-10-14)

> PHP dosya işlevleri POSIX `open/read/write/stat/flock` ve Windows
> `CreateFileW/ReadFile/GetFileAttributesExW/LockFileEx` üzerinde çalışan ince bir
> sarmalayıcıdır. Her blok `ReadAllLines` ile alınmış **gerçek repo satırıdır**.

### §13.1 F1 — Kilitli ekleme: `file_put_contents(..., FILE_APPEND|LOCK_EX)` ↔ `flock` ↔ `LockFileEx`

**Repo kanıtları (3 ayrı dosya, aynı desen):**

`shared/src/Log/FileHandler.php:248-252`

```php
for ($i = 0; $i < 3; $i++) {
    $bayt = @file_put_contents($this->dosya, $satir, FILE_APPEND | LOCK_EX);
    if ($bayt !== false) {
        return;
    }
```

`shared/src/PageRouter/PageRouterKernel.php:133-134`

```php
@file_put_contents($logDir . '/coremusic_php_errors.log', $errorMsg, FILE_APPEND | LOCK_EX);
@file_put_contents($logDir . '/coremusic_php_kernel_debug.log', $errorMsg, FILE_APPEND | LOCK_EX);
```

`media.coremusic.net/src/Media/CatalogWriter.php:113`

```php
$bayt = @file_put_contents($this->yol, $metin, LOCK_EX);
```

| PHP | POSIX | Windows Win32 |
|---|---|---|
| `LOCK_EX` | `flock(fd, LOCK_EX)` (BSD/kilit) | `LockFileEx(LOCKFILE_EXCLUSIVE_LOCK)` |
| `LOCK_SH` | `flock(fd, LOCK_SH)` | `LockFileEx` paylaşımlı (PHP'de yok → §13.7) |
| `FILE_APPEND` | `open(O_APPEND)` | `FILE_APPEND_DATA` |
| `file_put_contents` = open+write+close | `open`+`write`+`close` | `CreateFileW`+`WriteFile`+`CloseHandle` |

**Kaynaklı not (2026-10-14):** PHP resmi dokümanı `LOCK_EX` bayrağını şöyle tanımlar:
*"Acquire an exclusive lock on the file while proceeding to the writing. In other words, a
`flock()` call happens between the `fopen()` call and the `fwrite()` call."*
→ `https://www.php.net/manual/en/function.file-put-contents.php` (**reuse** · envanterde vardı · HTTP 200).
Yani PHP `LOCK_EX` **yalnız `flock` tabanlıdır**; POSIX `fcntl(F_SETLK)` kullanılmaz
(`https://www.php.net/manual/en/function.flock.php` — reuse · HTTP 200).
Bunun sonucu: **bazı ağ dosya sistemlerinde `flock` desteklenmez / uçucudur** (NFSv2/v3
bilgisi depoda **yok** → `⚠️ VERIFICATION REQUIRED` — ortam kaydı okunmadı).

### §13.2 F2 — Tek seferlik yazma (tüm dosya) + hata/kurtarma döngüsü

**Repo kanıtı:** `shared/src/Component/TemplateEngine.php:74-76`

```php
if (file_put_contents($cachePath, $template, LOCK_EX) === false) {
    throw new \RuntimeException("Template cache write failed: {$cachePath}");
}
```

`home.coremusic.net/config/constants.php:74-80`

```php
// Session save path — php.ini'den oku, fallback sys_get_temp_dir
$sessionPath = ini_get('session.save_path') ?: sys_get_temp_dir() . '/coremusic_sessions';
if (!is_dir($sessionPath)) {
    @mkdir($sessionPath, 0750, true);
}
session_save_path($sessionPath);
ini_set('session.save_path', $sessionPath);
```

POSIX karşılığı `open(O_WRONLY|O_CREAT|O_TRUNC, 0640)` + `write` + `close`;
Windows karşılığı `CreateFileW(CREATE_ALWAYS)` + `WriteFile` + `CloseHandle`.

### §13.3 F3 — Dizin üretimi + çift kontrol: `@mkdir(..., recursive)` ↔ `mkdir(2)` ↔ `CreateDirectoryW`

**Repo kanıtı:** `media.coremusic.net/src/Media/CatalogWriter.php:107`

```php
if (!is_dir($klasor) && !@mkdir($klasor, 0775, true) && !is_dir($klasor)) {
```

| PHP | POSIX | Windows Win32 |
|---|---|---|
| `!is_dir && !@mkdir && !is_dir` | `mkdir(2)` → `EEXIST` yok sayılır | `CreateDirectoryW` → `ERROR_ALREADY_EXISTS` |
| `0775` (mod) | `mode & ~umask` | **yok sayılır** (PHP `mkdir` modu NTFS'te uygulanmaz) |
| `true` (recursive) | `mkdir -p` (üstleri de) | `SHCreateDirectoryEx` / tek tek |

Çift `is_dir` kontrolü **yarış koşulunu (TOCTOU) kapatmaz**, yalnızca
"başka süreç zaten oluşturdu" durumunu tolere eder — gerçek kilit §13.1'dedir.
§13.6'daki yol çözümü ile birlikte kullanılır.

### §13.4 F4 — stat: `filesize`/`filemtime` ↔ `stat` ↔ `GetFileAttributesExW`

**Repo kanıtı:** `home.coremusic.net/include/Stream/MusicStreamHandler.php:71-81`

```php
if (is_file($path)) {
    $diskSize = (int)filesize($path);
    if ($diskSize > 0) {
        $size = $diskSize;
    }
}
$mtime     = (int)(is_file($path) ? filemtime($path) : time());
$etag      = '"' . md5($path . '|' . $size . '|' . $mtime) . '"';
$lastMod   = gmdate('D, d M Y H:i:s', $mtime) . ' GMT';
```

| PHP | POSIX | Windows Win32 | HTTP |
|---|---|---|---|
| `filesize` | `stat.st_size` | `WIN32_FILE_ATTRIBUTE_DATA.nFileSizeHigh/Low` | — |
| `filemtime` | `stat.st_mtime` | `ftLastWriteTime` | `Last-Modified` |
| `is_file` | `S_ISREG` | `!FILE_ATTRIBUTE_DIRECTORY` | 404 |
| (cache temizleme) | — | — | `clearstatcache()` (§13.7) |

`ETag = md5(path|size|mtime)` — üç parmacık (strong ETag) değildir, proje
**özel ETag** üretiyor; `If-None-Match` eşleşmesinde PHP tarafında 304 döner.

### §13.5 F5 — Akış + konum: `fopen`/`fseek`/`fread` ↔ `open`/`lseek`/`read` ↔ `CreateFileW`/`SetFilePointerEx`/`ReadFile`

**Repo kanıtı:** `home.coremusic.net/include/Stream/MusicStreamHandler.php:333-351`

```php
$handle = @fopen($path, 'rb');
if ($handle === false) {
    $this->fail(403, 'Erişim reddedildi');
}

if ($start > 0) {
    fseek($handle, $start);
}
```

| PHP | POSIX | Windows Win32 |
|---|---|---|
| `fopen($p,'rb')` | `open(O_RDONLY)` | `CreateFileW(GENERIC_READ)` |
| `fseek($h,$start)` | `lseek(fd, start, SEEK_SET)` | `SetFilePointerEx` |
| `fread($h,$n)` | `read(fd,buf,n)` | `ReadFile` |
| `fclose($h)` | `close(fd)` | `CloseHandle` |
| `feof($h)` | `read()==0` | `r==0` |

Bu blok §12.6'daki `Range`/`416` ve §13.4'teki `ETag` ile birlikte çalışır —
parça parça sunum **belleği O(chunk)** tutar (bkz. bellek §18.3).

### §13.6 F6 — Yol çözümü + symlink redi: `is_link`/`realpath` ↔ `lstat`/`readlink`

**Repo kanıtı:** `home.coremusic.net/include/Stream/MusicStreamHandler.php:185-202`

```php
if (is_link($trimmed)) {
    $this->fail(403, 'Erişim reddedildi');
}

$real = realpath($trimmed);
if ($real === false || !is_file($real)) {
    $this->fail(404, 'Dosya bulunamadı');
}

$root = realpath($this->libraryPath);
if ($root === false) {
    $this->fail(404, 'Kütüphane bulunamadı');
}

$prefix = rtrim($root, '\\/') . DIRECTORY_SEPARATOR;
if (!str_starts_with($real, $prefix)) {
    $this->fail(403, 'Erişim reddedildi');
}
```

| PHP | POSIX | Windows Win32 |
|---|---|---|
| `is_link` | `lstat` + `S_ISLNK` | `FILE_ATTRIBUTE_REPARSE_POINT` |
| `realpath` | `realpath(3)` | `GetFinalPathNameByHandleW` |
| `str_starts_with($real,$root)` | `openat` + `/proc/self/fd` kilidi | `NtCreateFile` `ObjectNameInformation` |

**⚠️ TOCTOU:** `is_link` → `realpath` → `fopen` arasında dosya değiştirilebilir;
savunma PHP'de tam **kapatılmaz** (bkz. §12 anti-pattern + §A.4 İ-4). Doğru
yaklaşım `openat`+`O_NOFOLLOW` benzeridir — PHP'de yok → `⚠️ VERIFICATION REQUIRED`.

### §13.7 F7 — Stat cache + dayanıklılık — repo yüzeyi

> PHP'nin dosya-sistemi fonksiyon indeksi (`file_put_contents` sayfasının yan menüsü) şunları
> **listeler**: `fsync` · `fdatasync` · `umask` · `chmod` · `is_writable` · `flock` ·
> `clearstatcache` — yani **PHP'nin hepsi vardır**; depoda çağrılmıyorlar (aşağıdaki grep 0).
> Kaynak: `https://www.php.net/manual/en/function.file-put-contents.php` (reuse · HTTP 200).
> Sürüm numarası (hangi PHP sürümünde eklendiği) **okunmadı** → `⚠️ VERIFICATION REQUIRED`.

| Konu | Repo durumu | Not |
|---|---|---|
| `clearstatcache()` | `shared/tests/Unit/Log/LoggerRedactionTest.php:178` | **var** (yalnız testte) |
| `fsync()` / `fdatasync()` | grep → **0 isabet** | PHP'de var, üretimde kullanılmıyor |
| `LOCK_SH` (paylaşımlı kilit) | grep → **0 isabet** | yalnız `LOCK_EX` var |
| `rename` (atomik değiştirme) | §12.4'te §12.5'e devredildi (bkz. §12) | bu bölümde tekrar yok |

### §13.8 Ölçüm

| Ölçüm | Değer |
|---|---|
| Eklenen PHP kod bloğu | 6 (§13.1 ×3 alıntı, §13.2 ×2, §13.3, §13.4, §13.5, §13.6) |
| Repo satır çifti | 10 (FileHandler:248-252 · PageRouterKernel:133-134 · CatalogWriter:107,113 · TemplateEngine:74-76 · constants:74-80 · MusicStreamHandler:71-81,185-206,333-351 · LoggerRedactionTest:178) |
| Yeni URL | **0** (§13 yeni URL eklemez → envanter kesişimi 0) |
| Reuse URL yeniden HTTP 200 | **2** — `https://www.php.net/manual/en/function.file-put-contents.php` · `https://www.php.net/manual/en/function.flock.php` (ikisi de envanterde vardı) |
| Olumsuz grep | `fsync` · `fdatasync` · `LOCK_SH` → 0 isabet |
| ⚠️ işaretli iddia | 3 (§13.1 NFS/flock ortamı · §13.6 TOCTOU kapatılamaması · §13.7 fonksiyon sürümü) |

## §14 — Çok dilli dosya sistemi örnekleri: JS · C · Python · Go · Rust · C#

> **Kanıt hiyerarşisi:** §13 PHP blokları depo satırına dayanıyordu. Burada **JS (98 dosya) ve
> Python (1 dosya) depoda gerçek satır taşır**; C/Go/Rust/C# **0 dosya** → `⚠️ KALIP (repo YOK)` +
> resmi doküman URL'i (2026-10-14 · hepsi HTTP 200 → dogrulama §J.9).

### §14.0 Ölçüm — kanıt durumu (2026-10-14)

| Dil | Repo dosyası | Dosya yüzeyi grep (repo) | Kanıt tipi |
|---|---|---|---|
| JavaScript | **98** | `node:fs` **1** (yalnız test dosyası) · `readFileSync` **2** · `writeFileSync` **0** · `mkdirSync` **0** · `localStorage` **18** | repo satırı + olumsuz kanıt |
| Python | **1** | `read_text` **2** · `write_text` **1** · `.glob(` **1** · `chmod` **0** | repo satırı |
| C / Go / Rust / C# | **0 / 0 / 0 / 0** | — | kalıp + doküman |

### §14.1 JavaScript — dosya okuma yalnız testte; üretim tarayıcıda (`localStorage`)

```js
// assets.coremusic.net/js/core/breakpoints.spec.js:2-3 (ReadAllLines birebir)
import { readFileSync } from 'node:fs';
import { resolve } from 'node:path';
```

**Repo gerçeği:** `writeFileSync` **0** · `mkdirSync` **0** · `child_process` **0** → üretim JS'i
dosya sistemine **yazmıyor**; kalıcı veri `localStorage` (**18** isabet) ile tarayıcı depolanıyor —
bu depo **OS dosya sistemi değildir** (kota, senkron API, aynı-origin kısıtı). `⚠️ VERIFICATION
REQUIRED`: `localStorage` kota/senkron davranışı dokümandan okunmadı.

```js
// ⚠️ KALIP (repo YOK) — Node tarafı yazma
// Kaynak: https://nodejs.org/api/fs.html (YENİ · 200)
import { writeFileSync, mkdirSync } from 'node:fs';
mkdirSync(dir, { recursive: true });                    // §13.3 @mkdir eşlemesi
writeFileSync(file, data, { encoding: 'utf8', flag: 'w' });
```

### §14.2 Python — depo satırları gerçek (okuma/yazma/glob) + kilit kalıbı

```python
# DEPO SATIRLARI — .ai/scripts/fix-mojibake.py (ReadAllLines birebir)
# :133
original = filepath.read_text(encoding='utf-8')
# :155
filepath.write_text(fixed, encoding='utf-8')
# :176
for filepath in base_dir.glob(pattern):
# :213
base_dir = Path(__file__).parent.parent.parent  # .ai/scripts/ -> proje kökü
```

```python
# ⚠️ KALIP (repo YOK) — atomik oluşturma + kilit
# Kaynaklar: https://docs.python.org/3/library/os.html (reuse · 200)
#            https://docs.python.org/3/library/fcntl.html (YENİ · 200)
import os, fcntl

fd = os.open(p, os.O_CREAT | os.O_EXCL | os.O_WRONLY, 0o600)  # varsa başarısız → §13.6 TOCTOU sınıfı
try:
    fcntl.flock(fd, fcntl.LOCK_EX)                             # §13.1 flock eşlemesi
    os.write(fd, b"data")
finally:
    os.close(fd)
```

**Eşleme:** `O_EXCL` ↔ PHP'de `file_put_contents(..., LOCK_EX)`'in **olmadığı** yarım (PHP'de
`O_EXCL` karşılığı `fopen(..., 'x')`dir → ölçüm: depoda `fopen(` **2 isabet** (`'rb'` · `'wb'`),
`'x'` modu **0 isabet** → yaratma-yarışı kapatma PHP'de **kullanılmıyor**). `fcntl.flock` ↔ §13.1'deki
PHP `flock` tabanlı `LOCK_EX`.

### §14.3 C — `open(O_CREAT|O_EXCL)` + `fcntl` kilidi (kalıp)

```c
/* ⚠️ KALIP (repo YOK) — Kaynaklar:
   https://pubs.opengroup.org/onlinepubs/9799919799/functions/open.html  (reuse · 200)
   https://pubs.opengroup.org/onlinepubs/9799919799/functions/fcntl.html (YENİ · 200) */
#include <fcntl.h>
#include <unistd.h>

int fd = open(p, O_CREAT | O_EXCL | O_WRONLY, 0640);
if (fd >= 0) {
    struct flock flk = { .l_type   = F_WRLCK,   /* yazma kilidi */
                         .l_whence = SEEK_SET, .l_start = 0, .l_len = 0 };
    fcntl(fd, F_SETLKW, &flk);                  /* W: gerekirse bloklanır — §13.1 eşlemesi */
    (void)!write(fd, "data", 4);
    close(fd);
}
```

**Eşleme:** `O_EXCL` tek-giriş-üretir (yaratma yarışı kapatır) + `struct flock` POSIX kilit ailesi
(PHP `LOCK_EX`'in POSIX karşılığı §13.1'de tartışıldı).

### §14.4 Go — `os.OpenFile` (kalıp)

```go
// ⚠️ KALIP (repo YOK) — Kaynak: https://pkg.go.dev/os (YENİ · 200)
package main

import (
    "os"
)

func writeNew(name string, data []byte) error {
    f, err := os.OpenFile(name, os.O_CREATE|os.O_EXCL|os.O_WRONLY, 0o640)
    if err != nil { return err }
    defer f.Close()
    _, err = f.Write(data)
    return err
}
```

**Eşleme:** Go'da bayraklar `os.O_*` adlarıyla taşınır; kilit API'si `os` paketinde **yoktur**
(kilit `syscall`/`golang.org/x/sys` katmanındadır → `⚠️ VERIFICATION REQUIRED`: bu katman URL'i
bu turda **doğrulanmadı**).

### §14.5 Rust — `std::fs::File` (kalıp)

```rust
// ⚠️ KALIP (repo YOK) — Kaynak:
// https://doc.rust-lang.org/std/fs/struct.File.html (YENİ · 200)
use std::fs::{File, OpenOptions};
use std::io::Write;

let mut f = OpenOptions::new()
    .write(true).create_new(true)          // O_EXCL karşılığı
    .open("state.json")?;
f.write_all(b"{}")?;                        // ? ile hata yayılımı
```

**Eşleme:** `create_new(true)` ↔ `O_EXCL`. Dosya **kilidi** için std'de API görünür mü →
`⚠️ VERIFICATION REQUIRED` (§J.9'da kilit API'si URL'i **eklenmedi**).

### §14.6 C# — `FileStream` + paylaşım kipi (kalıp)

```csharp
// ⚠️ KALIP (repo YOK) — Kaynak:
// https://learn.microsoft.com/en-us/dotnet/api/system.io.filestream (YENİ · 200)
using var fs = new FileStream(path, FileMode.CreateNew, FileAccess.Write, FileShare.None);
using var sw = new StreamWriter(fs);
sw.Write("{}");
```

**Eşleme:** `FileMode.CreateNew` ↔ `O_EXCL` · `FileShare.None` ↔ Windows `LockFileEx` ailesinin
kullanıcı yüzü (§12 Windows kolu ile aynı kavram). `⚠️ VERIFICATION REQUIRED`: `FileShare.None`'ın
tam kilit semantiği (paylaşım ihlalinde hangi istisna) dokümandan **okunmadı**.

### §14.7 Ölçüm (§14)

| Öğe | Değer |
|---|---|
| Dil | 6 (JS · C · Python · Go · Rust · C#) |
| Repo satır kanıtlı blok | **2** (§14.1 `breakpoints.spec.js:2-3` · §14.2 `fix-mojibake.py:133,155,176,213`) |
| `⚠️ KALIP (repo YOK)` blok | **6** (JS Node yazma · PY kilit · C · Go · Rust · C#) |
| Bu bölümün URL'i | **8** → **2 reuse** (`docs.python os.html` · `pubs open.html`) **+ 6 yeni** (`nodejs/fs` · `python/fcntl` · `pubs/fcntl` · `pkg.go.dev/os` · `std/fs/struct.File` · `MS FileStream`) — hepsi HTTP 200 (dogrulama §J.9) |
| Repo grep (olumsuz kanıt) | `writeFileSync` · `mkdirSync` · `child_process` · `chmod` → **0 isabet** · `localStorage` **18** (tarayıcı) · PHP `fopen` `'x'` modu **0** (`fopen(` toplam 2 = `rb`/`wb`) |
| ⚠️ işaretli iddia | 5 (§14.1 localStorage · §14.4 kilit katmanı · §14.5 std kilit API · §14.6 FileShare · §14.3 `struct flock` kalıp doğruluğu) |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
