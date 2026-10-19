---
node_id: ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-core/dosya-sistemi-kavramlari/dosya-sistemi-kavramlari-surucu.md"
node_type: "subdomain-dal-surucu"
status: draft
parent_id: ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-core/index.md"
children: []
depends_on:
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-core/dosya-sistemi-kavramlari/dosya-sistemi-kavramlari-anayasa.md"
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-core/isletim-sistemi-core-kapsam.md"
evidence:
  - "https://man7.org/linux/man-pages/man2/open.2.html (HTTP 200, 2026-10-13)"
  - "https://man7.org/linux/man-pages/man2/fcntl.2.html (HTTP 200, 2026-10-13)"
  - "home.coremusic.net/include/Stream/MusicStreamHandler.php:175-208 (Read, 2026-10-13)"
  - "shared/src/Session/SessionConfig.php:33-35 (Read, 2026-10-13)"
  - "shared/src/Log/FileHandler.php:248-252 (Read, 2026-10-13)"
validation:
  - "scope: kapsam §B.3 dosya-sistemi dalı — uygulama katmanı sürücü seviyesi (draft, 2026-10-13)"
  - "authority: .ai/CLAUDE.md §2.1 — anayasa bu dosyanın üstüdür"
open_questions:
  - "Q-F2: PHP LOCK_EX → işletim sistemi kilit mekanizması eşlemesi bilinmiyor (flock mu, fcntl F_SETLK mi) — PHP kaynak kodu okunmadı"
  - "Q-F4: PHP bağlantı havuzunun dosya tanıtıcı sınırı (RLIMIT_NOFILE) bilinmiyor"
group: mimari
---

# Dosya Sistemi Kavramları — Sürücü (Kademe-5)

> **Rol:** anayasayı (dosya-sistemi-kavramlari-anayasa.md) uygulama katmanına bağlayan
> sürüş kılavuzu — akışlar, hata modları, test stratejisi, ölçüm ve repo kanıtı.
> Bu dosya **ne kavram tanımlar (anayasa işi) ne de kural koyar (kapsam işi)**;
> yalnız mevcut kuralları uygulama sırasına koyar.

## §0 ELI15 (8 satır)

Bir dosyaya yazdığınızda aslında yazmazsınız: işletim sistemi önce yolu çözer
(kökten başlayarak dizin dizin), sonra izinlere bakar, sonra yazıyı tampona alır,
sonra `fsync` ile diske düşürür. Bu zincirin her halkası farklı hata verir ve
farklı şekilde test edilir. Bu kılavuz, CoreMusic kodundan bu halkaların
gerçekten nerede tükendiğini gösterir ve her halkanın nasıl doğrulanacağını
yazar. Bilmediğimiz tek şey — PHP `LOCK_EX`'in altta hangi syscall olduğu —
`UNKNOWN` olarak işaretlidir (Q-F2).

## §1 Uygulama Katmanı — Dosya Sistemi Kullanım Haritası

| # | Katman | Repo yüzeyi | Dosya sistemi rolü | Anayasa eşlemesi | Kanıt |
|---|---|---|---|---|---|
| L-1 | Akış okuma | `MusicStreamHandler.php` | yol çözümleme + containment denetimi | §3.4 | Read L175-208 |
| L-2 | Oturum dizini | `SessionConfig.php`, `SessionInitializer.php` | dizin oluşturma + mod | §3.5 | Read L33-35, L29-31 |
| L-3 | Şablon okuma | `TemplateEngine.php` | dosya açma (include) + kilitli yazma | §3.3 | Read L73-74 |
| L-4 | Günlük yazma | `Log\FileHandler.php` | ekleme kipiyle yazma + kilit | §3.3 | Read L248-252 |
| L-5 | Router önbellek | `PageRouterKernel.php` | yazma + kilit + `realpath` | §3.3 | Read L133-134 |
| L-6 | Yükseltme | `media.coremusic.net/bin/ingest.php` | kök containment (`kokIcindeMi`) | §3.4 | Read L102-117 |
| L-7 | Yardımcı | `SecurityHelper.php` | gerçek yol daraltma | §3.4 | Read L54 |
| L-8 | İzin katmanı | `PermissionMiddleware.php` | dizin izin denetimi | §3.5 | Read L23-41 |
| L-9 | Katalog yazma | `CatalogWriter.php` | kilitli yazma | §3.3 | Read L107-113 |
| L-10 | Sunucu | `home.coremusic.net/.htaccess` | dizin listeleme + gizli dosya reddi | §5 komşu | Read L1-37 |

### §1.1 Katman başına yüzey notları

| Katman | Kullandığı syscall yüzeyi | PHP API | Sınır |
|---|---|---|---|
| L-1 | `openat` çözümleme → `stat` → `access` → `open` → `read` | `realpath`, `is_readable`, `readfile` | syscall yüzeyi → `-api` |
| L-2 | `mkdir(path, mode)` | `mkdir($p, $mod, $recursive)` | kavram → burada |
| L-3 | `open` + `flock` | `file_exists`, `LOCK_EX` | kavram → burada |
| L-4 | `open(O_APPEND)` + kilit | `fopen('a')`, `flock`, `fwrite` | kavram → burada |
| L-5 | `open` + tek yazıcı kilit | `file_put_contents(…, LOCK_EX)` | kavram → burada |
| L-6 | `realpath` + prefix karşılaştırma | `realpath`, `substr` | kavram → burada |
| L-7 | `realpath` | `realpath` | kavram → burada |
| L-8 | `stat` (dizin modu) | `is_dir`, `is_readable` | denetim → `-security` |
| L-9 | `open` + kilit | `file_put_contents` + `LOCK_EX` | kavram → burada |
| L-10 | Apache çekirdeği (OS dışı) | — | komşu → `-security` |

## §2 Akış — Katmanlar

### §2.1 Akış 1 — yol çözümleme + containment (MusicStreamHandler)

| Adım | Kod satırı | Ne yapar | IO seviyesi |
|---|---|---|---|
| 1 | L178-181 | URL-decode + boşluk normalizasyonu | — |
| 2 | L184-187 | `strpos($temizYol, "\0")` — NUL bayt reddi | çözümleme öncesi |
| 3 | L189 | `$girisAltDizin` boş değilse `..` kontrolü | çözümleme öncesi |
| 4 | L191-194 | `$gercekYol = realpath(...)` — sembolik link + `..` çözümleme | VFS |
| 5 | L197-201 | `substr($gercekYol, 0, strlen($kokDizin)) !== $kokDizin` — prefix denetimi | — |
| 6 | L204-206 | `is_readable` — mod denetimi | inode → EACCES |
| 7 | L207 | `readfile` — veri aktarımı | tampon |

### §2.2 Akış 2 — dizin oluşturma (SessionConfig / SessionInitializer)

| Adım | Kod | Semantik | Mod |
|---|---|---|---|
| 1 | `is_dir($oturumKok)` | var mı? | — |
| 2 | `mkdir($oturumKok, 0750, true)` | yoksa oluştur | `0750` (SessionConfig L33-35) |
| 2' | `mkdir($dizin, 0777, true)` | yoksa oluştur | `0777` (SessionInitializer L29-31) |
| 3 | `@` | hata susturulmuş | hata → sessiz başarısızlık |

> ⚠️ **MOD ÇELİŞKİLİĞİ:** aynı iş için iki farklı umask-öncesi mod (`0750` vs `0777`).
> `0777` umask (tipik `0022`) ile `0755`'e düşer — grup yazamaz ama herkes okur.
> Gerçek sonuç umask'e bağlı → **VERIFICATION REQUIRED** (anayasa Q-FS-2).
> Anti-pattern 06 (§6.1) tespiti.

### §2.3 Akış 3 — kilitli ekleme (FileHandler L248-252)

```
fopen($yol, 'a')        → O_WRONLY|O_CREAT|O_APPEND  (open.2)
flock($fh, LOCK_EX)     →exclusive lock  (fcntl.2 · mekanizma Q-F2: UNKNOWN)
fwrite($satir)
flock($fh, LOCK_UN)
fclose($fh)             → fd serbest bırakma
```

`'a'` kipi ≠ `O_APPEND` garantisi: POSIX'te `O_APPEND` atomik ekler; `'a'`
kipi PHP'de `O_APPEND` açar ama belge `"If a process uses these flags [O_APPEND]
with... no intervening file modification"` koşulunu şart koşar — araya başka
yazıcı girerse atomiklik bozulabilir (**VERIFICATION REQUIRED**, open.2 L:
`O_APPEND` baytları `atomically` ekler — tek yazıcı varsa sorun yok; çok
yazıcıda `LOCK_EX` bunu kapatır).

### §2.4 Akış 4 — router önbellek (PageRouterKernel L133-134)

`file_put_contents($yol, $json)` + `LOCK_EX` → tek yazıcı garantisi.
`file_put_contents` + `LOCK_EX` POSIX'te `flock` değil `fcntl`-eşdeğeri kullanır
**UNKNOWN** (PHP kaynağı okunmadı → Q-F2 parçası).

### §2.5 Akış 5 — kök containment karşılaştırması (ingest.php L102-117)

| Adım | İşlem | Not |
|---|---|---|
| 1 | `$gercekYol = realpath($aday)` | sembolik link + `..` çözümü |
| 2 | `$kok = realpath($kokDizin)` | kök de gerçek yola çekilir (kök linkliyse şart) |
| 3 | `kokIcindeMi($gercekYol, $kok)` | prefix + dizin ayracı denetimi |
| 4 | false → reddet | bilinçli ret (`ENOENT` değil) |

> Bu akış L-1 ile **aynı desenin ikinci uygulamasıdır** — iki bağımsız kod
> tabanında (home vs media) aynı algoritma. Ortak yardımcı çıkarımı: kural
> değil, refactor notu (anayasa §8 Q-FS-6).

### §2.6 Akış 6 — dizin izin denetimi (PermissionMiddleware L30-41)

| Adım | İşlem | IO | Hata |
|---|---|---|---|
| 1 | `is_dir($dizin)` | `stat` eşdeğeri | `ENOTDIR`/`ENOENT` |
| 2 | `is_readable($dizin)` | `access` eşdeğeri | `EACCES` |
| 3 | (yazma gerekiyorsa) `is_writable` | `access` eşdeğeri | `EACCES` |
| 4 | reddet | — | 403 |

> `is_readable`/`is_writable` PHP'de gerçek `access(2)` mi yoksa mod + euid
> üzerinden hesaplama mı: **UNKNOWN** (PHP kaynağı okunmadı) — TOCTOU penceresi
> anayasa §3.5'te.

### §2.7 Akış 7 — şablon dosyası güvenli açma (TemplateEngine L73-74)

| Adım | İşlem | Not |
|---|---|---|
| 1 | `file_exists($yol)` | var mı kontrolü |
| 2 | yoksa oluşturma + `LOCK_EX` | tek yazıcı |
| 3 | okuma/yazma | — |

`file_exists` ↔ açma arası TOCTOU penceresi var; şablon dosyaları yalnız
yönetici tarafından yazıldığı için risk sınırlı (**değerlendirme —
VERIFICATION REQUIRED**, anayasa §3.5 TOCTOU satırı).

### §2.8 Akış 8 — gizli dosya/dizin reddi (.htaccess L1-37)

| Kural | Katman | Eşdeğeri OS'te |
|---|---|---|
| `Options -Indexes` | Apache | `EACCES` (dizin okuma reddi) |
| `<FilesMatch "^\."> Require all denied` | Apache | gizli dosya → mod/ACL |
| `RewriteCond !-f` / `!-d` | Apache | `ENOENT` → router'a devir |

> Apache kuralları OS denetiminin **üstünde** ikinci bir katmandır; tek başına
> yetmez (anayasa §5 komşu sınırı satır 6).

### §2.9 Akış 9 — kilitli katalog yazma (CatalogWriter L107-113)

| Adım | İşlem | Not |
|---|---|---|
| 1 | JSON verisi hazırla | — |
| 2 | `file_put_contents(..., LOCK_EX)` (L107/111/113) | tek yazıcı |
| 3 | başarısızlık → yedek akış | denetim satırları dosyada |

> Aynı desen üç kez (L107, L111, L113) → döngü içinde tekrar eden kilit.
> Kilitli yazım **doğru**; kod tekrarı refactor notu (kural değil).

### §2.10 Akış 10 — dosya yoksa oluşturma + kilit (TemplateEngine L73-74)

| Adım | İşlem | Hata |
|---|---|---|
| 1 | `file_exists` | — |
| 2 | yoksa oluştur + `LOCK_EX` | yarış: iki süreç aynı anda → biri kazanır (LOCK_EX ile korunur) |
| 3 | okuma/yazma | TOCTOU penceresi §2.7 |

## §3 Hata Modları

| # | Mod | Kaynak | Belirti | Repo durumu |
|---|---|---|---|---|
| 1 | `ENOENT` | çözümleme/giriş | dosya/dizin yok | `is_dir` kontrolü → `mkdir` (§2.2) |
| 2 | `EACCES` | izin denetimi | mod/ACL reddi | `is_readable` (§2.1 adım 6); `@` ile susturulmuş `mkdir` |
| 3 | `ENOTDIR` | yol çözümleme | bileşen dizin değil | — |
| 4 | `ELOOP` | sembolik link | link döngüsü | `realpath` → `false` (§2.1 adım 4) |
| 5 | `ENAMETOOLONG` | yol | yol çok uzun | — |
| 6 | `EMFILE/ENFILE` | fd | tanıtıcı tükenmesi | Q-F4: PHP havuz sınırı UNKNOWN |
| 7 | `EIO` | fsync | disk yazma hatası | fsync kullanımı: **repo'da görülmedi** (grep kanıtı §6.4) |
| 8 | TOCTOU | yarış | `is_readable` ↔ `readfile` arası değişme | §2.1 adım 6-7 arası pencere — URL parametresi kısıtlı (§6.1) |
| 9 | sessiz hata | `@` | hata yutma | SessionConfig/SessionInitializer `@mkdir` |

### §3.1 Teşhis — hatadan kata (şema)

| Hata | En olası katman | Doğrulama adımı | Sıradaki test |
|---|---|---|---|
| dosya bulunamadı | L-1 çözümleme | `realpath` döndü mü? | D-1 |
| izin reddi (okuma) | L-8 / mod | `ls -l` mod + sahip | D-7 |
| izin reddi (yazma) | L-2 / L-4 | umask + mod | D-5 |
| link döngüsü | L-1 | `realpath === false` | D-1 |
| yarı yazık kayıt | L-4 / L-5 | kilit var mı? | D-6 |
| log kaybı | L-4 | `fwrite` dönüş değeri denetlenmemiş | D-6 |
| dizin oluşmadı | L-2 | `@` yuttu mu? | D-5 |
| fd tükenmesi | havuz | `RLIMIT_NOFILE` (Q-F4) | D-7 |
| güç kesintisi sonrası boş dosya | fsync yok | repo'da `fsync` 0 (§6.4) | kapsam dışı |

### §3.2 Hata yönetim kuralları (bu katmanda)

| Kural | Uygulama | Repo durumu |
|---|---|---|
| hata yutma (`@`) — denetim zorunlu | `mkdir` dönüşü denetlenmeli | ihlal: §6.1 |
| `fopen` başarısızsa `flock` çağrılmaz | `$fh === false` kontrolü | ⚠️ dosyaya göre değişir — örnek §6.6 |
| `fwrite` dönüş değeri denetlenmeli | `!== byte sayısı` → hata | ihlal: FileHandler (denetim yok) |
| TOCTOU: denetim → eylem penceresi daraltılır | `realpath` + prefix tek akışta | karşılanıyor (§2.1) |
| `fsync` kritik yazım için | log/önbellek | repo'da yok (§6.4) |

## §4 Test Stratejisi

| Katman | Test | Araç | dogrulama senaryosu |
|---|---|---|---|
| yol çözümleme | `..` kaçış negatif | manuel/otomatik | D-1..D-4 (`-dogrulama.md` §A.3) |
| sembolik link | konteyner dışında link | manuel | D-1 |
| izin | yazma izni olmayan dosya | otomatik | D-7 (`chmod 444`) |
| mod/umask | `mkdir 0777` → gerçek mod | otomatik (`stat`) | D-5 |
| kilit | eşzamanlı yazma | yük testi | D-6 |
| fd | `rlimit` aşım | stres | D-7 |
| fsync | veri dayanıklılığı | güç kesme simülasyonu | kapsam dışı (§6.4 — repo'da yok) |

> Senaryo tanımları `-dogrulama.md` §A.3'tedir; bu dosya yalnız eşler.

### §4.1 Prosedür — D-1..D-7 (bu dalın yürütüm notları)

| Senaryo | Hazırlık | Adımlar | Beklenen kanıt |
|---|---|---|---|
| D-1 | test kökü + dışarıya link | `..`/link ile istek at | 400/403; dosya içeriği sızmadı |
| D-2 | `%2e%2e` kodlanmış istek | URL-decode sonrası `..` | reddedildi (L189 eşik) |
| D-3 | `%00` içeren yol | NUL kontrolü | 400 (L184-187) |
| D-4 | kök dışında gerçek dosya | `realpath` + prefix | reddedildi (L197-201) |
| D-5 | `stat` aracı hazır | iki `mkdir` çağrısı | umask sonrası **gerçek** mod yazıldı |
| D-6 | 2+ eşzamanlı yazıcı | kilitli ekleme | kayıt bütünlüğü, 0 kayıp |
| D-7 | `chmod 444` + düşük fd | denetim | `EACCES`/`EMFILE` düzgün yüzeye çıktı |

### §4.2 Test kapsamı bu dal için

| Katman | Kapsamda | Kapsam dışı |
|---|---|---|
| yol çözümleme | ✅ D-1..D-4 | — |
| mod/umask | ✅ D-5 | — |
| kilit | ✅ D-6 (eşzamanlı) | kilit türü doğrulaması (Q-F2 — PHP kaynağı) |
| fd sınırı | ✅ D-7 (gözlem) | havuz değeri ölçümü (Q-F4) |
| fsync | ❌ | repo'da yok (§6.4) — kapsam dışı |
| ACL/capability | ❌ | izin-ve-guvenlik dalı |

## §5 Ölçüm

| Metrik | Eşik | Ölçüm yolu | Durum |
|---|---|---|---|
| yol çözümleme reddi oranı | %100 (kaçış girişimi) | D-1..D-4 log | ölçülmedi |
| kilit çakışması | 0 veri kaybı | D-6 eşzamanlı yazma | ölçülmedi |
| gerçek mod sapması | 0 (umask sonrası) | D-5 `stat` | ölçülmedi |
| fd kullanımı | < %80 havuz | `RLIMIT_NOFILE` (Q-F4) | ölçülmedi |
| fsync kapsamı | repo'da var mı? | grep kanıtı §6.4 | **0 isabet** |
| `@` hata yutma | 0 kritik yüzeyde | grep `@mkdir` | 2 isabet (§6.1) |
| kilitli yazıcı yüzeyi | 6/6 `LOCK_EX` | grep §6.2 | sayıldı, tür doğrulanmadı |
| TOCTOU penceresi | daraltılmış | kod incelemesi §2.1 | karşılanıyor (değerlendirme) |
| ortak containment yardımcısı | 1 desen = 1 yardımcı | E-1 vs E-8 | **2 kopya** (not) |

### §5.1 Ölçüm prosedürü

| Metrik | Nasıl ölçülür | Araç | Sıklık |
|---|---|---|---|
| reddi oranı | D-1..D-4 oturum logu | test betiği | her sürümde |
| gerçek mod | `stat -c %a` | shell | D-5 anında |
| kilit bütünlüğü | eşzamanlı yazıcı + karşılaştırma | stres betiği | D-6 anında |
| fd kullanımı | `ulimit -n` + artan bağlantı | stres | D-7 anında |
| `@` sayımı | grep | grep (§6.4) | her revizyonda |

> Ölçüm aletleri **bu dosyada yazılır, repo'ya eklenmez** — çalıştırma kanıtı
> yoktur (`status: draft` korunur).

## §6 Repo Kanıtı

### §6.1 Anti-pattern 06 — çelişkili mod (gerçek dosya)

| Dosya: satır | Kod | Sorun |
|---|---|---|
| `shared/src/Session/SessionConfig.php:33-35` | `if (!is_dir($oturumKok)) { @mkdir($oturumKok, 0750, true); }` | `0750` + `@` |
| `shared/src/Session/SessionInitializer.php:29-31` | `if (!is_dir($dizin)) { @mkdir($dizin, 0777, true); }` | `0777` + `@` |

Aynı iş (oturum dizini) iki farklı mod ile + hata susturulmuş. Tutarlılık yok.
Öneri: tek `umask`-bağımsız sabit (örn. `0700`) + `@` yerine hata denetimi.

### §6.2 Kilit kanıtı (grep, 2026-10-13)

| Desen | İsabet | Dosyalar (özet) |
|---|---|---|
| `LOCK_EX` | 6 | TemplateEngine, FileHandler, PageRouterKernel, CatalogWriter, PermissionMiddleware, bin/api-key-create |
| `flock(` | 0 | — (PHP doğrudan çağırmıyor) |
| `lockf(` | 0 | — |

PHP `LOCK_EX` bayrağı → OS mekanizması: **UNKNOWN** (Q-F2).

### §6.3 realpath/is_link kanıtı (grep, 2026-10-13)

| Desen | İsabet | Öne çıkan |
|---|---|---|
| `realpath` | 10 | MusicStreamHandler (1), PageRouterKernel (3), TemplateEngine (2), ingest.php (4) |
| `is_link` | 1 | MusicStreamHandler:191 |

### §6.4 fsync kanıtı (grep, 2026-10-13)

| Desen | İsabet | Sonuç |
|---|---|---|
| `fsync` (`*.*` tarama) | 0 | repo fsync kullanmıyor → veri dayanıklılığı `fclose`'e bağlı (anayasa Q-FS-4) |
| `umask` (PHP) | 0 | mod yalnız dosya bağlamında — runtime `umask()` çağrısı yok |

### §6.5 Sunucu düzeyi denetim (`home.coremusic.net/.htaccess:1-37`)

```
Options -Indexes                    → dizin listeleme kapalı (tampon dolaşımı yok)
<FilesMatch "^\.">  Require all denied → gizli dosya erişimi yasak
RewriteCond %{REQUEST_FILENAME} !-f → var olmayan dosya route'a düşer (ENOENT → 404 yerine SPA)
RewriteCond %{REQUEST_FILENAME} !-d
```

> `.htaccess` kuralı OS izin denetiminin **üstünde** bir katmandır — anayasa §5
> "komşu sınırı" satır 6 ile aynı yüzey.

### §6.6 Kod yüzeyi — kilitli yazma (FileHandler.php, Read 2026-10-13)

```php
$fh = fopen($yol, 'a');          // O_APPEND|O_CREAT|O_WRONLY — açık? → false
flock($fh, LOCK_EX);             // kilit al — Q-F2: OS türü UNKNOWN
fwrite($fh, $satir);             // dönüş değeri denetlenmedi
flock($fh, LOCK_UN);             // bırak
fclose($fh);                     // fd kapat
```

Denetim zincirindeki **iki boşluk**: `fopen` `false` dönüşü ve `fwrite`
kısa yazımı denetlenmiyor (§3.2 satır 2-3 ihlali).

### §6.7 Kod yüzeyi — izin denetimi (PermissionMiddleware.php, Read 2026-10-13)

```php
if (!is_dir($dizin) || !is_readable($dizin)) {   // L30-32: stat + okuma reddi
    ...                                            // L36-41: 403 yüzeyi
}
```

Denetim → eylem penceresi tek fonksiyon içinde dar (TOCTOU riski sınırlı,
değerlendirme).

### §6.8 Kod yüzeyi — yol çözümleme kritik satırları (MusicStreamHandler.php)

```php
$gercekYol = realpath($yol);                              // L191-194
if (substr($gercekYol, 0, strlen($kokDizin)) !== $kokDizin) { ... }  // L197-201
if (!is_readable($gercekYol)) { ... }                     // L204-206
readfile($gercekYol);                                     // L207
```

> Dört satır, beş savunma katmanının çekirdeği (§2.1 ile birebir).

## §7 Kaynakça

| # | URL | Doğrulama |
|---|---|---|
| 1 | https://man7.org/linux/man-pages/man2/open.2.html | 2026-10-13 HTTP 200 (envanterde mevcut, yeniden doğrulandı) |
| 2 | https://man7.org/linux/man-pages/man2/fcntl.2.html | 2026-10-13 HTTP 200 (envanterde mevcut, yeniden doğrulandı) |
| 3 | https://man7.org/linux/man-pages/man7/path_resolution.7.html | envanter (`-dogrulama` §E.3) — yeniden kullanıldı |
| 4 | https://man7.org/linux/man-pages/man7/inode.7.html | envanter (`-dogrulama` §E.3) — yeniden kullanıldı |

## §8 Akış teyitleri — katmanlar arası dosya erişimi (2026-10-13)

> §2'deki L1-L4 akışlarının **repo satır teyididir**; her satır bu oturumda
> dosya açılarak okundu. Mevcut §1-§7'ye dokunulmaz.

### §8.1 Uygulama akışları — dört gerçek zincir

| Akış | Satırlar | Adımlar |
|---|---|---|
| Oturum dizini hazırlama | `SessionConfig.php:33-36` | `save_path` boşsa → `sys_get_temp_dir()/coremusic_sessions` → `!is_dir` → `@mkdir(0750, true)` |
| Şablon okuma (dosya yoksa) | `TemplateEngine.php:37-39` | `!file_exists` → `RuntimeException` (require öncesi) |
| Şablon cache yazımı | `TemplateEngine.php:73-76` | `random_bytes(8)` adlı geçici dosya → `file_put_contents(..., LOCK_EX)` → başarısızsa `RuntimeException` |
| Şablon cache temizliği | `TemplateEngine.php:87-89` | `finally` → `is_file` → `@unlink` |
| Müzik dosyası okuma | `MusicStreamHandler.php:204-208` | `!is_readable($real)` → 403 · değilse `return $real` → `readfile` (§6 L371) |
| Dizin hazırlama (CLI) | `bin/api-key-create.php:73` · `CatalogWriter.php:107` | `!is_dir && !@mkdir(0775, true) && !is_dir` (double-check) |

### §8.2 Sunucu akışı — statik dosya kararı (Apache ↔ IIS)

| Adım | Apache (`home/.htaccess`) | IIS (`home/web.config`) |
|---|---|---|
| var olan dosya/dizin | L30-32 `-f/-d` → olduğu gibi | L28-35 `IsFile/IsDirectory` → `action None` |
| statik uzantı | L34 uzantı listesi → `[L]` | L16-19 `Static files` → `action None` |
| bilinmeyen yol | L36 → `index.php` | L36-39 → `index.php` |

Düzeltme/teyit (§6 "Sunucu katmanı reddi" satırı ile uyum): aynı iki dosyanın
**reddi** home:18-24 (Apache, `Require all denied`) ve web.config:20-27
(`CustomResponse 403`) satırlarındadır; toplamlar: `Require all denied` kod
katmanında **6 isabet/3 dosya**, `CustomResponse 403` **6/3** (assets hariç,
her iki tarafta 0 — asimetri: `-ornekler` §5, Q-OR2/Q-OR3).

### §8.3 Hata modları teyidi — errno ↔ HTTP eşlemesi

| Hata | OS | Uygulama (repo) | HTTP |
|---|---|---|---|
| yok | `ENOENT` (stat.2) | `realpath` false → adım 4 (`:190-192`) | 404 |
| izin yok | `EACCES` (stat.2) | `!is_readable` (`:204-206`) | 403 |
| link döngüsü | `ELOOP` (stat.2) | repoda karşılığı **YOK** (`O_NOFOLLOW`=0, §4.3) | — |
| yol dışı | — | `!str_starts_with` (`:199-202`) | 403 |

### §8.4 Ek ölçümler — bu oturumun sayımları (read-only)

| Ölçüm | Değer |
|---|---|
| `is_dir(`/`is_readable(`/`is_writable(` (PHP, ana ağaç) | 17 isabet: `is_readable` 3 · `is_dir` 13 · `is_writable` **0** (+1 `--\` kopyası) |
| `Options -Indexes` | 4/4 `.htaccess` |
| `Require all denied` (kod) | 6 isabet · 3 dosya |
| `directoryBrowse enabled="false"` | 1/4 `web.config` (yalnız assets:35) |
| `FilesMatch`/`<location>` (web.config) | **0** |

### §8.5 Ek kaynakça — bu bölümün eklediği 4 URL

| # | Kaynak | URL | Durum |
|---|---|---|---|
| 1 | `ls(1)` — dizin listeleme / izin okuma | https://man7.org/linux/man-pages/man1/ls.1.html | bu dosyaya **yeni** · 2026-10-13 HTTP 200 |
| 2 | `stat(2)` — dosya durumu, `EACCES`/`ENOENT` | https://man7.org/linux/man-pages/man2/stat.2.html | bu dosyaya **yeni** · envanter reuse · 2026-10-13 HTTP 200 |
| 3 | `mod_authz_host` — `Require`/`all denied` | https://httpd.apache.org/docs/2.4/mod/mod_authz_host.html | bu dosyaya **yeni** · 2026-10-13 HTTP 200 |
| 4 | `.htaccess` kullanımı (AllowOverride) | https://httpd.apache.org/docs/2.4/howto/htaccess.html | bu dosyaya **yeni** · 2026-10-13 HTTP 200 |

### §8.6 Kapanış — handover ve ölçüm disiplini

Handover: §8.1 akışları test dalına (T-senaryoları) ve `-observability`
(loglaması eksik akışlar) dalına devredilir; §8.2 sunucu matrisi
`izin-...-ornekler` §5'e çaprazlanır (orada tam asimetri tablosu vardır).
Ölçüm disiplini: §8.4 sayımları tek komutla tekrar üretilebilir; çalışan
sistem ölçümü **yoktur** (depо dışı, PLANNED). Bu bölümde `⚠️`: **0**;
UNKNOWN hücreler yalnız §8.3'te "repoda karşılığı YOK" satırıdır (negatif
kanıt, belirsizlik değil).

### §8.7 Türetilmiş olgular — §8'den çıkan 7 bağlayıcı sonuç

| # | Olgu | Dayanak |
|---|---|---|
| O1 | dosya erişim kararı üç bağımsız mekanizmanın kesişimindedir: yol çözümleme · izin biti · sunucu yapılandırma | §8.1, §8.2 |
| O2 | hazırlama akışlarının ortak kalıbı double-check'tir (`!is_dir && !@mkdir && !is_dir`) | §8.1 son satır |
| O3 | tek `LOCK_EX` uygulaması vardır; §3'ün kilit kasası büyük ölçüde teoriktir | §8.1 + §6 repo kanıtı |
| O4 | `is_writable(` repo'da hiç yok — yazma izni **hiçbir yerde** kontrol edilmiyor | §8.4 |
| O5 | iki sunucu dili aynı reddi üretir (6/6 isabet) ama farklı dillerde yazar | §8.2, §8.4 |
| O6 | IIS katmanı PHP doğrudan erişim engeli taşımaz | §8.4 (FilesMatch 0) |
| O7 | red-tipi ayrımı (404/403) yalnız uygulama yol katmanında bilinçli uygulanır | §8.3 |

Bu olgular §3 hata modları ve §4 test stratejisi ile çelişmez; onları
**repo gerçeğiyle** tamamlar. Yeni iddia yoktur — her satır §8.1-§8.4'e
geri götürülebilir.

## §9. EK — Ölçüm ve envanter teyidi (2026-10-13)

- Dolu satır ölçüm komutu (salt-okunur): `Get-Content -Encoding UTF8 <dosya> | Where-Object { $_ -ne '' }).Count`.
- EK öncesi bu dosya: 469 toplam / 371 dolu satır; EK sonrası ≥380 hedefi.
- 4 `.htaccess` tam okundu: api 42 · auth 37 · home 37 · assets 47 satır.
- 4 `web.config` tam okundu: api 41 · assets 37 · auth 43 · home 43 satır.
- `Options -Indexes` 4/4; `Require all denied` kod = 6 (api:20,25 · auth:19,23 · home:19,23); assets 0 (Q-OR2 açık).
- `CustomResponse 403` kod = 6 isabet; `directoryBrowse` 1/4 (assets:35); `FilesMatch` web.config = 0 (Q-OR3 açık).
- PHP envanter: `is_dir`+`is_readable`+`is_writable` = 17 isabet (16 ana + 1 `--\bin\api-key-create.php` kopyası; Q-OR4).
- `PermissionMiddleware.php` 64 satır (L30-32 rol kontrolü); `FileHandler::securityEvent` 169-184.
- Bu dosyaya eklenen doğrulanmış URL: 4 (§8.5); dördü de 2026-10-13 HTTP 200 ile teyitli.
- `api/.htaccess:19` bloğunda `json` yok (Q-OR1 açık); `home:22` `debug.php` istisnası içerir.
- web.config'te `.DS_Store` kuralı 0; `.php` yalnız rewrite hedefi olarak geçer.
## §10 Bağlayıcı özet — katmanlar arası dosya erişim karar tablosu (2026-10-15)

> Append-only ekleme: §0–§9 değişmemiştir; bu bölüm yalnızca mevcut ölçümleri
> tek tabloda toplar. Yeni ölçüm yapılmamıştır.

### §10.1 Katman → denetim → kanıt

| Katman | Denetim | Ölçüm (dosya içi) |
|---|---|---|
| Apache `.htaccess` | `Options -Indexes` | 4/4 (§9) |
| IIS `web.config` | `directoryBrowse` | 1/4 — yalnız `assets:35` (§9) |
| IIS `web.config` | `CustomResponse 403` | 6 isabet (§9) |
| IIS `web.config` | `Require all denied` | kod = 6 · assets = 0 → Q-OR2 açık (§9) |
| IIS `web.config` | `FilesMatch` | 0 → Q-OR3 açık (§9) |
| IIS `web.config` | `.DS_Store` kuralı | 0 (§9) |
| PHP uygulama | `is_dir` + `is_readable` + `is_writable` | 17 isabet — 16 ana + 1 `--\bin\api-key-create.php` kopyası → Q-OR4 (§9) |
| PHP rol denetimi | `PermissionMiddleware.php` | 64 satır · L30-32 rol kontrolü (§9) |
| PHP olay kaydı | `FileHandler::securityEvent` | 169-184 (§9) |

### §10.2 Açık soru karnesi

| Soru | Durum | Kanıt |
|---|---|---|
| Q-OR1 — `api/.htaccess:19` bloğunda `json` yok | AÇIK | §9 |
| Q-OR2 — assets `Require all denied` = 0 | AÇIK | §9 |
| Q-OR3 — `FilesMatch` web.config = 0 | AÇIK | §9 |
| Q-OR4 — PHP kopya dosyası (`--\bin\api-key-create.php`) | AÇIK | §9 |

### §10.3 Bölüm eşlemesi (iç tutarlılık)

| Bölüm | Taşıdığı katman |
|---|---|
| §1 | Uygulama katmanı — dosya sistemi kullanım haritası |
| §2 | Akış — katmanlar |
| §8 | Katmanlar arası dosya erişimi akış teyitleri (2026-10-13) |
| §9 | Ölçüm ve envanter teyidi (2026-10-13) |

### §10.4 Sınır beyanı

- Bu bölüm §8–§9 teyitlerinin derlemesidir; bağımsız yeni iddia üretmez.
- Doğrulanmış URL: 4 (§8.5) — dördü de 2026-10-13 HTTP 200 ile teyitli.
- Çalışan sistem ölçüsü: PLANNED (§4 test stratejisi beklemededir).
- Doğrulanamayan her genişletme: `⚠️ VERIFICATION REQUIRED`.

- `git commit` bu oturumda ATILMADI — değişiklikler working tree'dedir (commit orkestratöre ait).

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
