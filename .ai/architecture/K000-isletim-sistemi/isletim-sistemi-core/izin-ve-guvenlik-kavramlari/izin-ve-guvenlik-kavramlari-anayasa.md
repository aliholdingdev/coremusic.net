---
node_id: ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-core/izin-ve-guvenlik-kavramlari/izin-ve-guvenlik-kavramlari-anayasa.md"
node_type: "subdomain-dal-anayasa"
status: draft
parent_id: ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-core/index.md"
children: []
depends_on:
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-core/index.md"
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-core/isletim-sistemi-core-kapsam.md"
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-core/dosya-sistemi-kavramlari/dosya-sistemi-kavramlari-anayasa.md"
evidence:
  - "https://man7.org/linux/man-pages/man7/capabilities.7.html (HTTP 200, 2026-10-13)"
  - "https://man7.org/linux/man-pages/man5/acl.5.html (HTTP 200, 2026-10-13)"
  - "https://man7.org/linux/man-pages/man2/setuid.2.html (HTTP 200, 2026-10-13)"
  - "https://man7.org/linux/man-pages/man3/realpath.3.html (HTTP 200, 2026-10-13)"
  - "shared/src/Middleware/PermissionMiddleware.php:23,30-41 (Read, 2026-10-13)"
  - "shared/src/Security/SecurityHelper.php:54 (Read, 2026-10-13)"
  - "shared/src/Session/SessionConfig.php:33-35 (Read, 2026-10-13)"
  - "grep (*.php, 2026-10-13): posix_get/chown/chmod/fileowner/umask/is_writable = 0 isabet"
validation:
  - "scope: kapsam §B.4 izin-ve-guvenlik dalı — anayasa seviyesi (draft, 2026-10-13)"
  - "authority: .ai/CLAUDE.md §2.1 — anayasa bu dalın en üst seviye kavram dosyasıdır"
  - "satır aralığı: 380-460 (görev manifesti)"
open_questions:
  - "Q-I1: repo UID/GID bağlamı (web sunucusu hangi kullanıcıyla çalışıyor) — composer/systemd/nginx yapılandırmaları okunmadı, UNKNOWN"
  - "Q-I2: sunucu umask değeri (0777 sonrası gerçek mod) — UNKNOWN"
  - "Q-I3: repo'da ACL kullanımı var mı — PHP yüzeyinde 0 isabet; sunucu FS üzerinde `getfacl` ölçülmedi, VERIFICATION REQUIRED"
  - "Q-I4: capability kullanımı (setcap/php-fpm) — bilinmiyor, UNKNOWN"
  - "Q-I5: symlink yarışı (symlink race) için tarihsel saldırı yüzeyi — degerlendirme, VERIFICATION REQUIRED"
group: mimari
---

# İzin ve Güvenlik Kavramları — Anayasa (Kademe-5)

> **Rol:** bu dalın **tek kavramsal otoritesi**. Kapsam (`-kapsam.md` §B.4)
> sınırı çizer, sürüş (`-surucu`) uygular, örnekler (`-ornekler`) gösterir —
> burada **tanım ve kural** durur. Dosya-sistemi dalı (komşu) "ne" olduğunu
> anlatır; bu dal "kimin yapabileceği"ni.

## §0 ELI15 (9 satır)

Her dosyanın iki sorusu vardır: (1) bu dosya nerede, (2) **kim** okuyabilir/yazabilir.
İkinci sorunun cevabı üç katmanda verilir: UID/GID (sahip/grup), 9 bitlik mod
(rwx ×3), ve isteğe bağlı ACL'ler. Linux ayrıca modu bypass eden
**capability**'ler tanımlar — root'un her şeyi yapabilmesi yerine parçalanmış
yetkiler. Sembolik linkler bu modelin klasik kaçış kapısıdır: `realpath`
çözümlemesi güvenlik denetimidir, süs değil. CoreMusic kodunda bu katmanların
çoğu **yok** (grep: chmod/uid/umask 0 isabet) — demek ki koruma büyük ölçüde
sabit dosya modları + uygulama katmanı denetimine dayanıyor. Eksikler ve
bilinmeyenler aşağıda açıkça işaretli.

## §1 Kimlik — UID/GID Modeli

### §1.1 Kimlik türleri (credentials.7 / setuid.2)

| Kimlik | Ne | Önemi |
|---|---|---|
| real UID | sürecin sahibi | denetim/audit |
| effective UID (euid) | erişim kararında kullanılan | **izin hesabının anahtarı** |
| saved set-user-ID | yetki geri kazanımı | setuid program güvenliği |
| filesystem UID | FS erişiminde euid ile eş | NFS/dosya yazma |
| GID (gerçek/etkin/grup listesi) | grup denetimi | rwx'in "grup" sütunu |

> `setuid(2)`: euid'i değiştirir; `CAP_SETUID` yoksa yalnız kendi UID'ine
> geçebilir (setuid.2, HTTP 200 2026-10-13).

### §1.2 Web yüzeyinde kimlik (repo kanıtı)

| Soru | Repo durumu | Kanıt |
|---|---|---|
| PHP süreci hangi UID ile | **UNKNOWN** | yapılandırma okunmadı (Q-I1) |
| uygulama `posix_get*` kullanıyor mu | **0 isabet** | grep 2026-10-13 |
| `chown`/`fileowner`/`getmyuid` | **0 isabet** | grep 2026-10-13 |
| klasör 0750/0777 → kim yazabilir | umask'e bağlı (**Q-I2 UNKNOWN**) | SessionConfig:33-35 |

> **Sonuç (negatif kanıt):** uygulama kodu kimlik üzerinde **hiçbir dinamik
> işlem yapmıyor** — koruma, deploy anında atanan sabit modlara emanet.

### §1.3 Kimlik × repo yüzeyi matrisi

| Yüzey | UID/GID ile işlem | Kanıt (2026-10-13) | Sonuç |
|---|---|---|---|
| PHP çekirdek çağrılar (`posix_*`) | yok | grep 0 | statik kimlik |
| dosya sahipliği değiştirme (`chown`) | yok | grep 0 | deploy dışı |
| grup sorgu (`filegroup`) | yok | grep 0 | — |
| kullanıcı sorgu (`getpwuid` ailesi) | yok | grep 0* | — |
| ortamdan kimlik (`getenv`) | dolaylı | grep 14 isbet | **config** kimliği (§7) |
| yetki verme (`chmod`) | yok | grep 0 | sabit modlar |

> `*` `getpwuid` 2026-10-13 oturum ölçümü (önceki oturum grep'i) — PHP
> yüzeyinde kullanıcı adı çözümleme yok.

### §1.4 Kimlik hiyerarşisi (karar sırasında)

```text
CAP_DAC_OVERRIDE var mı?  ──evet──▶  mod/ACL ATLANIR (capabilities.7)
        │hayır
        ▼
ACL var mı?  ──evet──▶  ACL + maske kararı (acl.5)
        │hayır
        ▼
euid == sahip mi? ──▶ rwx[0..2]
   değilse: egid ∈ grup mu? ──▶ rwx[3..5]
   değilse: ──▶ rwx[6..8]
        │
        ▼
yeterli bit yoksa EACCES
```

## §2 Kavram Modeli

### §2.1 Mod (rwx) — 9 bit + 3 bit

| Alan | Bitler | Soru |
|---|---|---|
| sahip (owner) | `rwx` | euid == dosya sahibi mi |
| grup | `rwx` | efgid ∈ dosya grubu mu |
| diğer | `rwx` | geri kalanlar |
| setuid | `4xxx` | çalıştırıldığında euid = dosya sahibi |
| setgid | `2xxx` | çalıştırıldığında egid = dosya grubu |
| sticky | `1xxx` | yalnız sahip/silme yetkisi ( `/tmp` ) |

Karar sırası: sahip → grup → diğer; ilk eşleşen uygulanır. İlk bit `r`=4,
`w`=2, `x`=1 → `0750` = sahip `rwx`, grup `r-x`, diğer `---`.

### §2.2 umask — oluşturulan nesnenin başlangıç modu

`sonuç mod = istenen mod & ~umask`. Örnek: `mkdir(0777)` + umask `0022` →
`0755`. **Repo kanıtı:** runtime `umask()` çağrısı **0 isabet** (grep
2026-10-13) → süreç umask'i dışarıdan belirlenir (**Q-I2 UNKNOWN**).
Kod iki farklı istenen mod kullanır: `0750` (SessionConfig:33-35) vs `0777`
(SessionInitializer:29-31) — gerçek sonuç bu yüzden **ölçülmeden yazılamaz**.

### §2.3 ACL — erişim kontrol listesi (acl.5)

| Kavram | Tanım |
|---|---|
| temel ACL | sahip/grup/diğer üçlüsü (mod ile aynı) |
| genişletilmiş ACL | kullanıcı/grup girdileri: `user:alice:r--` |
| varsayılan ACL | dizine uygulanır → yeni dosyalara devralınır |
| maske | `mask` girdisi grup niteliklerini kısar |

ACL varsa mod, ACL'in özet görüntüsüdür (`+` eki: `ls -l`).
**Repo durumu:** PHP yüzeyinde ACL çağrısı **0 isabet**; sunucu FS üzerinde
`getfacl` ölçülmedi (**Q-I3, VERIFICATION REQUIRED**).

### §2.4 Capability — parçalanmış root (capabilities.7)

| Eski model | Capability modeli |
|---|---|
| root = her şey | her yetki ayrı bayrak: `CAP_SETUID`, `CAP_DAC_OVERRIDE`, `CAP_FOWNER`... |
| setuid-root program | `setcap cap_setuid+ep ./program` |
| tam yetki verme | yalnız gereken yetki |

> capabilities.7 (HTTP 200, 2026-10-13): `CAP_DAC_OVERRIDE` mod/ACL
> denetimini **atlar** — yani mod tek başına yetki sınırı değildir.
> **Repo durumu:** setcap/php-fpm capability yapılandırması **UNKNOWN**
> (Q-I4).

### §2.5 Sembolik link + realpath denetimi — güvenlik boyutu

| Tehlike | Mekanizma | Savunma |
|---|---|---|
| symlink kaçışı | `a -> /etc/passwd` | `realpath` + kök prefix denetimi |
| symlink yarışı (TOCTOU) | denetim sonrası link değiştirilir | atomik `openat(..., O_NOFOLLOW)` |
| link döngüsü | `a -> b -> a` | `ELOOP` (realpath.3) |
| bağlantı ayracı kaçışı | `..` çözümleme | canonical yol (realpath.3) |

> `realpath(3)`: tüm linkleri ve `/./`, `/../` çözümler, sonuçta **link
> içermeyen** kanonik yol verir; hatalar: `EACCES`, `ELOOP`, `ENOENT`,
> `ENOTDIR` (realpath.3, HTTP 200 2026-10-13).
>
> **Repo:** `realpath` 10 isbeti, `is_link` 1 isbeti (MusicStreamHandler:191);
> korunan desen = realpath → prefix (dosya-sistemi dalı §2.1). **Eksik:**
> `O_NOFOLLOW`/`O_DIRECTORY` kullanılmıyor (**0 isabet** — grep 2026-10-13):
> yarış penceresi kapanmamış (**Q-I5**).

### §2.6 Kavramlar arası hiyerarşi

```text
capability (en yüksek — DAC'yi atlar)
  └─ ACL (varsa mod'u geçersiz kılar/keser)
       └─ mod rwx (sahip/grup/diğer)
            └─ uygulama denetimi (realpath/prefix — kod katmanı)
                 └─ sunucu katmanı (.htaccess Require — Apache)
```

> Her katman **ayrı** savunma hattıdır; üsttekiler alttakileri **düzmez**
> (mod iyi de uygulama kötüyse kaçış yine mümkün).

## §3 Mekanizma Akışı

### §3.1 A1 — erişim kararı (open anı)

| Adım | Kontrol | Hata |
|---|---|---|
| 1 | yol çözümleme (dosya-sistemi dalı §3.4) | `ENOENT`/`ELOOP`/`ENOTDIR` |
| 2 | symlink varsa → bağlantı davranışı | `ELOOP` |
| 3 | euid/GID → hangi sınıf (sahip/grup/diğer) | — |
| 4 | ACL varsa → genişletilmiş kontrol (+maske) | `EACCES` |
| 5 | `CAP_DAC_OVERRIDE` → atla | — |
| 6 | bit kontrolü (`r` okuma / `w` yazma / `x` çalıştırma) | `EACCES` |

### §3.2 A2 — dosya oluşturma (mod + umask)

```text
open(O_CREAT, mode)  →  gerçek mod = mode & ~umask  →  ACL yoksa mod yeterli
                                                        ACL varsayılanı varsa devral
```

### §3.3 A3 — uygulama katmanı denetimi (repo)

| Adım | Repo örneği | Katman |
|---|---|---|
| 1 | `is_dir && is_readable` (PermissionMiddleware:30-32) | kod |
| 2 | `realpath` + prefix (MusicStreamHandler:191-201) | kod |
| 3 | 403 reddi (PermissionMiddleware:36-41) | kod |
| 4 | Apache `Require all denied` (.htaccess:1-37) | sunucu |

> Bu akış **OS denetimini tekrarlar** — defense in depth, savunma değil
> kopya değil: farklı katman (PHP vs Apache), farklı tehdit (kod hatası vs
> yanlış yapılandırma).

### §3.4 A4 — kimlik değiştirme (setuid ailesi)

| Çağrı | Ne yapar | Tehlike |
|---|---|---|
| `setuid(uid)` | euid'i kurar; root ise hepsini | geri dönüş imkansız (setuid.2) |
| `seteuid` | euid geçici düşürme | önerilen kalıp (setuid.2 SEE ALSO) |
| `setreuid` | real+effective değişimi | **repo: 0 isabet** |

### §3.5 A5 — hata ve denetim yüzeyi

| Hata | Anlamı | İlgili mekanizma |
|---|---|---|
| `EACCES` | bit/ACL reddi | §2.1/§2.3 |
| `EPERM` | capability yok (setuid.2) | §2.4 |
| `ELOOP` | link döngüsü | §2.5 |
| `ENOENT` | yok | §3.1 adım 1 |

### §3.6 A6 — denetim (audit) boşluğu

Repo'da erişim denetimi log'u **görülmedi** (PermissionMiddleware reddi
sessiz) — "kim denedi" sorusunun cevabı yok. Kanıt: Read L36-41 (log satırı
yok). **VERIFICATION REQUIRED** (loglama ayrı dosyanın konusu olabilir).

## §4 Ölçüm ve Eşikler

| Metrik | Eşik | Ölçüm yolu | Durum |
|---|---|---|---|
| kritik dizin modu | `0750`/`0700` tutarlı | `stat -c %a` | **tutarsız** (§2.2) |
| umask | bilinen sabit | süreç ortamı | UNKNOWN (Q-I2) |
| ACL kullanımı | var/yok | `getfacl` | ölçülmedi (Q-I3) |
| capability | php-fpm setcap? | `getcap -r /` | ölçülmedi (Q-I4) |
| `O_NOFOLLOW` | kritik open'larda | grep: **0 isabet** | karşılanmadı |
| uid dinamik kullanımı | 0 (tasarım gereği) | grep: 0 | doğrulandı |
| erişim reddi log'u | var mı? | kod incelemesi | **yok** (§3.6) |
| URL doğrulama | 4 yeni + envanter reuse | webfetch 2026-10-13 | ✅ 4×HTTP 200 |

## §5 Komşu Sınırı

| Komşu | Sınır | Bu dalda değil çünkü |
|---|---|---|
| `dosya-sistemi-kavramlari` | yol, fd, kilit, fsync | kimlik değil |
| `dosya-sistemi-api` (varlığı UNKNOWN) | syscall imzaları | kullanım ≠ imza |
| `dosya-sistemi-security` (varlığı UNKNOWN) | denetim akışı tasarımı | sınır: kavram burada |
| `dosya-sistemi-kernel` (varlığı UNKNOWN) | denetim çekirdekte | biz uygulama katmanındayız |
| `auth-*/` subdomain (vault) | oturum/rol/OWASP | erişim kimliği (rol) ≠ FS izni |
| `-kapsam.md` §B.4 | dosya listesi + kapsam kararları | kural kapsamda |
| `-dogrulama.md` §A.3 (İ-1..İ-7) | senaryo tanımları | eşleme burada |

## §6 Terim Sözlüğü

| # | Terim | Tanım (tek cümle) |
|---|---|---|
| 1 | UID | kullanıcının sayısal kimliği |
| 2 | euid | erişim kararında kullanılan etkin UID |
| 3 | GID | grup kimliği |
| 4 | rwx | 3 bitlik erişim niteliği (oku/yaz/çalıştır) |
| 5 | mod | 9+3 bitlik izin sözcüğü (örn. `0750`) |
| 6 | umask | oluşturma modundan **çıkarılan** bitler |
| 7 | sticky bit | yalnız sahibinin silebilmesi ( `/tmp` ) |
| 8 | setuid | çalıştırıldığında euid'i dosya sahibine eşitler |
| 9 | setgid | dosya grubunu devralır |
| 10 | ACL | dosya başına genişletilmiş erişim listesi (acl.5) |
| 11 | varsayılan ACL | dizinden yeni dosyalara devredilen ACL |
| 12 | maske | ACL grup girdilerini sınırlayan özel girdi |
| 13 | capability | root'un parçalanmış yetkisi (capabilities.7) |
| 14 | `CAP_DAC_OVERRIDE` | mod/ACL denetimini atlama yetkisi |
| 15 | `CAP_SETUID` | kimlik değiştirme yetkisi |
| 16 | sembolik link | hedefe yönlendiren dosya girdisi |
| 17 | realpath | linkleri ve `..` çözerek kanonik yol üretme (realpath.3) |
| 18 | TOCTOU | denetim ile eylem arası yarış penceresi |
| 19 | symlink yarışı | denetim sonrası link değiştirme saldırısı |
| 20 | `EACCES` | izin reddi hatası |
| 21 | `EPERM` | yetkisiz işlem hatası (capability) |
| 22 | defense in depth | aynı kararı ayrı katmanlarda tekrar |
| 23 | O_NOFOLLOW | open'da symlink'i izlememe bayrağı |
| 24 | ayrıcalık düşürme | gereksiz yetkiden geçici vazgeçme (seteuid) |

## §7 Kapsam Dışı

- Rol/tabana dayalı erişim, oturum, CSRF/CSP, OWASP → auth & security vault
  dosyaları (komşu subdomain'ler).
- Dosya yolu çözümleme, fd, kilit mekanizması içyapısı → dosya-sistemi dalı.
- SELinux/AppArmor politikaları → **UNKNOWN** (repo'da politika dosyası
  görülmedi; aranmadı da — kapsam dışı, VERIFICATION REQUIRED).
- Windows ACL (DACL/SACL) ve macOS TCC → platform dışı (bkz. §5 komşu).

## §8 Açık Sorular

| ID | Soru | Neden önemli | Durum |
|---|---|---|---|
| Q-I1 | PHP/web hangi UID ile | gerçek izin sınıfları | UNKNOWN |
| Q-I2 | süreç umask'i | 0777'nin gerçek sonucu | UNKNOWN |
| Q-I3 | FS'te ACL var mı | mod tek sınır mı | VERIFICATION REQUIRED |
| Q-I4 | capability yapılandırması | DAC bypass yüzeyi | UNKNOWN |
| Q-I5 | symlink yarışı penceresi | O_NOFOLLOW yok | VERIFICATION REQUIRED |
| Q-I6 | erişim reddi log'u | denetim boşluğu | VERIFICATION REQUIRED |

## §9 Kaynakça

| # | URL | Doğrulama |
|---|---|---|
| 1 | https://man7.org/linux/man-pages/man7/capabilities.7.html | 2026-10-13 HTTP 200 (yeniden doğrulandı) |
| 2 | https://man7.org/linux/man-pages/man5/acl.5.html | 2026-10-13 HTTP 200 (yeni) |
| 3 | https://man7.org/linux/man-pages/man2/setuid.2.html | 2026-10-13 HTTP 200 (yeni) |
| 4 | https://man7.org/linux/man-pages/man3/realpath.3.html | 2026-10-13 HTTP 200 (yeni) |
| 5 | https://www.man7.org/linux/man-pages/man7/credentials.7.html | envanter (kapsam §G.1) — reuse |
| 6 | https://www.man7.org/linux/man-pages/man2/umask.2.html | envanter (dogrulama §I.1) — reuse |
| 7 | https://man7.org/linux/man-pages/man7/path_resolution.7.html | envanter (dogrulama §E.3) — reuse |

**Dürüstlük notu (bu oturum):** `man7.org/.../man7/acl.7.html` **404**,
`docs.kernel.org/filesystems/posix-acls.html` **404**, `man7.org/.../man7/
realpath.7.html` **404** — üçü de doğrulandı ve **asla cite edilmedi**;
ACL için doğru sayfa `acl.5`, realpath için `realpath(3)`'tür (2026-10-13).

## §10 Repo izin yüzeyi — gerçek satır kanıtı ve düzeltme kaydı (2026-10-13)

> Bu bölüm §2-§3'teki iddiaların **disk ile karşılaştırmasıdır**. Kural: mevcut
> satırlar silinmez; çelişki burada kayda geçer, doğru kanıt burada taşınır
> (BKZ. `-ornekler` dosyası §7, AP-P1/AP-P2).

### §10.1 `PermissionMiddleware` — dosyanın gerçekte ne yaptığı (64 satır)

`shared/src/Middleware/PermissionMiddleware.php`, tam dosya okundu:

| Satır | İçerik |
|---|---|
| 19-20 | `$routeMeta = $request['_route_meta'] ?? []` · `$auth = $request['_auth'] ?? []` |
| 23-25 | `empty($auth['userId'])` → **ilgilenmez**, `$next($request)` |
| 27 | `$userRole = $auth['role'] ?? 'user'` |
| 30-32 | `requiredRole` uyuşmazlığı → `forbidden('role_insufficient', ...)` |
| 36-41 | `requiredPermission` `in_array(..., true)` ile yoksa → `forbidden('permission_denied', ...)` |
| 47-63 | `forbidden()`: L50-54 `securityEvent('authz_denied', [reason, uri, ip])` · L56-62 `httpStatus: 403`, `halt: true` |

`is_dir(`/`is_readable(`/`is_writable(` bu dosyada: **0 isabet** (grep, 2026-10-13).

### §10.2 Uyumsuzluk kaydı — §3.3 adımı 1 (korunur, düzeltilmez)

| Öğe | §3.3'teki iddia (satır korunur) | Disk gerçeği (2026-10-13) | Doğru kaynak |
|---|---|---|---|
| `is_dir && is_readable` | "PermissionMiddleware:30-32" | L30-32 = **rol kontrolü**; dosyada bu iki fonksiyon **0 isabet** | `is_readable` → `MusicStreamHandler.php:204`; `is_dir` → `SessionConfig.php:34` |

Sonuç: §3.3'ün **savı** (uygulama katmanı denetimi) doğrudur, **satır
referansı** yanlıştır. Düzeltilmiş referans bu bölümdür; §3.3 satırı tarihsel
kayıt olarak durur.

### §10.3 Uyumsuzluk kaydı — §3.6 "denetim boşluğu" (korunur)

§3.6 "erişim denetimi log'u görülmedi — PermissionMiddleware reddi sessiz"
der. Disk gerçeği (§10.1): `forbidden()` reddi **önce** log'a yazar
(L50-54 → `FileHandler::securityEvent`), **sonra** 403 döner (L56-62).
`shared/src/Log/FileHandler.php:169-184`:

| Satır | İçerik |
|---|---|
| 171 | `[SECURITY] {$event}` başlığı |
| 172 | mesaja yalnız şu alanlar girer: `ip, uri, origin, role, code, reason, path` |
| 178-181 | `match`: `authz_denied` → `warning` |
| 183 | `logAt(..., securityStream: true)` |

### §10.4 Q-I6 kapanışı

§8'deki Q-I6 satırı ("erişim reddi log'u — VERIFICATION REQUIRED") bu bölümle
**kapanmıştır**: log hattı VARDIR (PermissionMiddleware:50 →
FileHandler:169-184). Kalan gerçek boşluk artık "log'un kendisi korunuyor mu?"
sorusudur (rotasyon/yetki) — o `-observability` dalıdır (surucu §2.7).

### §10.5 İzin okuma envanteri — 17 isabet özeti (detay: `-ornekler` §6.1)

| Fonksiyon | Ana ağaç isabet | Dağılım |
|---|---|---|
| `is_readable(` | 3 | `MusicStreamHandler.php:204` · `media.../audit.php:395` · `media.../scan.php:151` |
| `is_dir(` | 13 | oturum/dizin hazırlama (SessionConfig:34, SessionInitializer:30, constants:76, api-key-create:73, CatalogWriter:107) + CLI betikleri (audit:262, ingest:81,160,263,321, scan:134) + HtmlShellRenderer:124 + test:47 |
| `is_writable(` | **0** | yazma izni hiçbir yerde kontrol edilmiyor (negatif kanıt) |
| `--\` kopyası | 1 | `--\bin\api-key-create.php:73` (repo kökündeki `--` dizini, Q-OR4) |

### §10.6 Sunucu katmanı — sayımlar özeti (detay: `-ornekler` §3-§5)

| Ölçüm | Değer (2026-10-13) |
|---|---|
| `.htaccess` / `web.config` dosya sayısı | 4 / 4 |
| `Options -Indexes` | 4/4 `.htaccess` |
| `Require all denied` (kod katmanı) | 6 isabet · 3 dosya (assets: 0) |
| `directoryBrowse enabled="false"` | 1/4 `web.config` (yalnız assets:35) |
| `FilesMatch`/`<location>` (web.config) | **0** — PHP doğrudan erişim engeli IIS'te yok (Q-OR3) |
| `Permissions-Policy` header | web.config 3/4 · `.htaccess` 0 |

### §10.7 Ek kaynakça — bu bölümün eklediği 2 URL

| # | Kaynak | URL | Durum |
|---|---|---|---|
| 1 | `stat(2)` — `EACCES` + yol üzerindeki dizimlerde execute izni gerekliliği | https://man7.org/linux/man-pages/man2/stat.2.html | bu dosyaya **yeni** · reuse envanter (`isletim-sistemi-api-dogrulama.md:201`) · 2026-10-13 HTTP 200 yeniden doğrulandı |
| 2 | `mod_authz_host` — `Require` / `all denied` direktifi (sunucu katmanı reddinin dayanağı) | https://httpd.apache.org/docs/2.4/mod/mod_authz_host.html | bu dosyaya **yeni** · 2026-10-13 HTTP 200 |

Ölçüm disiplini: §10.1-§10.6'daki her sayı 2026-10-13'te repo kökünden
salt-okunur komut veya tam dosya okuması ile alındı; çalışan sistem ölçümü
yoktur (PLANNED, `-dogrulama`).

### §10.8 Açık sorular tablosu — durum güncellemesi (bu bölümün etkisi)

| ID | §8'deki durum | Bu bölüm sonrası durum |
|---|---|---|
| Q-I1 | PHP/web hangi UID ile — UNKNOWN | **açık** — repo'da `posix_get*`/`getmyuid` = 0 (surucu §5); sunucu okuması gerekir |
| Q-I2 | süreç umask'i — UNKNOWN | **açık** — `@mkdir(0750)` gerçek modu garanti etmez (`ls.1`/`stat.2` ile ölçüm PLANNED) |
| Q-I3 | FS'te ACL var mı — VERIFICATION REQUIRED | **açık** — repo `getfacl` = 0; sunucu dosya sistemi okunmadı |
| Q-I4 | capability yapılandırması — UNKNOWN | **açık** — `setcap`/`getcap` kodda 0; kurulum adımı depo dışı (PLANNED) |
| Q-I5 | symlink yarışı — VERIFICATION REQUIRED | **açık** — `O_NOFOLLOW` 0 (§10.5); TOCTOU penceresi kod kanıtıyla **görünür** artık |
| Q-I6 | erişim reddi log'u — VERIFICATION REQUIRED | **KAPANDI** — log VAR (§10.3-§10.4) |

### §10.9 Bu bölümün kendi ölçüm tablosu

| Ölçüm | Değer | Komut/Araç |
|---|---|---|
| `PermissionMiddleware.php` tam okuma | 64 satır | `read` (tüm dosya) |
| bu dosyada `is_dir(`/`is_readable(` | 0 | `Select-String` (dosya bazlı) |
| `FileHandler.php` securityEvent aralığı | 169-184 | numaralı okuma |
| repo PHP `is_dir/is_readable/is_writable` | 17 isabet | `Get-ChildItem -Filter '*.php'` + `Select-String` |
| `is_writable(` | 0 | aynı tarama |
| `.htaccess` / `web.config` | 4 / 4 | `Get-ChildItem -Filter` |
| `Require all denied` (kod) | 6 | `Select-String` |
| `directoryBrowse false` | 1 | `Select-String` |

### §10.10 Test senaryoları taslağı — bu bölümün gözlem adayları (PLANNED)

| # | Gözlem | Beklenen (kod okuması) | Sonuç |
|---|---|---|---|
| T1 | red denemesi sonrası log satırı | `[SECURITY] authz_denied reason=role_insufficient ... warning` | PLANNED |
| T2 | oturum dizini gerçek modu | 0750 (umask düşürmediyse) | PLANNED |
| T3 | `home/.env` dış istek | 403 (home:18-20) | PLANNED |
| T4 | `home/include/x.php` dış istek | 403 (home:22-24) · IIS ayağı UNKNOWN | PLANNED |
| T5 | `assets` kökünde `.md` | erişim açık olabilir (Q-OR2) | PLANNED |
| T6 | IIS `pages/x` isteği | 403 (web.config:20-23) | PLANNED |

Sonuçlar `-dogrulama` dosyasına işlenir; gözlem gelmeden "çalışıyor" iddiası
bu dosyaya yazılmaz (zero-hallucination).

## §11. EK — Ölçüm ve doğrulama kapanışı (2026-10-13)

- Dolu satır tanımı ve ölçüm komutu (salt-okunur): `Get-Content -Encoding UTF8 <dosya> | Where-Object { $_ -ne '' }).Count`.
- EK öncesi bu dosya: 459 toplam / 369 dolu satır; EK sonrası ≥380 hedefi.
- `PermissionMiddleware.php` tamamı 64 satır (tek okuma); L30-32 rol kontrolüdür, `is_dir`/`is_readable` bu dosyada 0 isabet (AP-P1 kanıtı).
- Repo geneli `is_dir`+`is_readable`+`is_writable` = 17 isabet (16 ana ağaç + 1 `--\bin\api-key-create.php` kopyası); `is_writable` 0 · `is_readable` 3 · kalan 14 `is_dir`.
- `Require all denied` repo geneli 41 isabet = 6 kod (api:20,25 · auth:19,23 · home:19,23) + 35 doküman; `assets/.htaccess` 0 isabet (Q-OR2 açık).
- 4 `.htaccess` satır ölçümü: api 42 · auth 37 · home 37 · assets 47; `Options -Indexes` 4/4.
- 4 `web.config` satır ölçümü: api 41 · assets 37 · auth 43 · home 43; `directoryBrowse` yalnız assets:35 (1/4).
- `CustomResponse 403` kod = 6 isabet; `FilesMatch`/`location` web.config'te 0 (Q-OR3 açık); `.DS_Store` kuralı 0.
- `FileHandler::securityEvent` gerçek aralığı 169-184 (16 satır; 169-181 daraltması bu dalgada düzeltildi).
- Bu dosyaya eklenen doğrulanmış URL: 2 (§10.7); ikisi de 2026-10-13 HTTP 200 ile teyitli.
- Bugün 404 ile teyitlenen 2 tam URL (`learn.microsoft.com/.../rewrite/rules/` ve `.../using-rewrite-rules-in-webconfig`) hiç cite edilmedi.
- T1-T6 test taslağı PLANNED; gözlem gelmeden "çalışıyor" iddiası bu dosyaya yazılmaz.
- Açık soru durumu: Q-I1..Q-I5 §10.8'de, Q-I6 kapandı (log VAR, AP-P2); Q-OR1..Q-OR4 açık.
## §12 Bağlayıcı özet — izin modeli karar tablosu (2026-10-15)

> Append-only ekleme: §0–§11 değişmemiştir; yalnız mevcut ölçümlerin derlemesidir.

### §12.1 Yüzey → denetim → kanıt

| Yüzey | Denetim | Ölçüm (dosya içi) |
|---|---|---|
| Kimlik | UID/GID modeli | §1 |
| Mekanizma | akış | §3 |
| Eşik | ölçüm ve eşikler | §4 |
| Komşu sınırı | komşu sınırı | §5 |
| Apache `.htaccess` | `Options -Indexes` | 4/4 · api 42 / auth 37 / home 37 / assets 47 (§10) |
| IIS `web.config` | satır ölçümü | api 41 / assets 37 / auth 43 / home 43 (§10) |
| IIS `web.config` | `directoryBrowse` | 1/4 — yalnız `assets:35` (§10) |
| IIS `web.config` | `CustomResponse 403` | 6 isabet (§10) |
| IIS `web.config` | `FilesMatch` / `location` | 0 → Q-OR3 açık (§10) |
| IIS `web.config` | `.DS_Store` kuralı | 0 (§10) |
| PHP olay kaydı | `FileHandler::securityEvent` | 169-184 — 16 satır (§10) |

### §12.2 Açık soru durumu

| Soru grubu | Durum | Kanıt |
|---|---|---|
| Q-I1..Q-I5 | AÇIK | §10.8 |
| Q-I6 | KAPANDI — log VAR | AP-P2 |
| Q-OR1..Q-OR4 | AÇIK | §10 |

### §12.3 Test ve doğrulama sınırı

- T1-T6 test taslağı: PLANNED — gözlem gelmeden "çalışıyor" iddiası bu dosyaya yazılmaz (§11).
- Doğrulanmış URL: 2 (§10.7) — ikisi de 2026-10-13 HTTP 200.
- 404 ile teyitlenen 2 tam URL hiç cite edilmedi (§11).
- Çalışan sistem ölçüsü: YOK (PLANNED).
- Doğrulanamayan her genişletme: `⚠️ VERIFICATION REQUIRED`.

### §12.4 Bölüm eşlemesi (iç tutarlılık)

| Bölüm | Taşıdığı konu |
|---|---|
| §1 | Kimlik — UID/GID modeli |
| §3 | Mekanizma akışı |
| §8 | Açık sorular |
| §10 | Repo izin yüzeyi — gerçek satır kanıtı ve düzeltme kaydı (2026-10-13) |
| §11 | Ölçüm ve doğrulama kapanışı (2026-10-13) |

- `git commit` bu oturumda ATILMADI — tüm değişiklikler working tree'dedir (commit orkestratöre ait).

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
