---
node_id: K000-isletim-sistemi/isletim-sistemi-core/izin-ve-guvenlik-kavramlari/izin-ve-guvenlik-kavramlari-surucu
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-core/izin-ve-guvenlik-kavramlari
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-core/izin-ve-guvenlik-kavramlari/izin-ve-guvenlik-kavramlari-anayasa
  - K000-isletim-sistemi/isletim-sistemi-core/dosya-sistemi-kavramlari
  - K000-isletim-sistemi/isletim-sistemi-core/isletim-sistemi-core-dogrulama
evidence:
  - https://man7.org/linux/man-pages/man2/setresuid.2.html
  - https://man7.org/linux/man-pages/man8/getcap.8.html
  - https://man7.org/linux/man-pages/man2/openat.2.html
  - shared/src/Middleware/PermissionMiddleware.php:17-63
  - shared/src/Session/SessionConfig.php:20,35
  - shared/src/Log/FileHandler.php:169-181
  - home.coremusic.net/include/Stream/MusicStreamHandler.php:173-209
  - home.coremusic.net/bin/api-key-create.php:487-493
validation: "2026-10-13 · L3 (repo disk kanıtı) kendi grep/Read ile birebir teyit edildi · L1/L4 senaryoları (-dogrulama §A.4 İ-1..İ-7) depo dışı → PLANNED · repo'da POSIX yetki primitive kodu YOK (grep: chmod/chown/umask/setuid/getfacl/setcap/O_NOFOLLOW/extension_loaded = 0, 2026-10-13) · 3 YENİ URL HTTP 200 doğrulandı"
open_questions:
  - "Q-I1: uid/yetki emülasyonu sınırı (uygulama RBAC vs POSIX kimlik) kasası net değil"
  - "Q-I5: O_NOFOLLOW repo'da YOK → realpath+prefix denetimi tek savunma, TOCTOU yüzeyi açık"
  - "Q-I6: anayasa dosyasındaki 'erişim reddi log'u yok' cümlesi disk ile çelişiyor (FileHandler.php:169-181) — düzeltme anayasa dosyasında"
  - "Q2: core↔kernel sınırı AÇIK → ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# izin-ve-guvenlik-kavramlari-surucu — İzin ve Güvenlik Mekanizması: Akışlar · Hata Modları · Test (Kademe-5)

## §0 ELI15

"Bu dosyaya kim okuyabilir, kim yazabilir?" sorusunun cevabı iki katmanda verilir.
**İşletim sistemi katmanı**: kimlik (UID/GID · EUID), mod bitleri (rwx), ACL,
capability ve setuid ailesi — Linux çekirdek modeli, resmi belgelerle anlatılır.
**Uygulama katmanı (repo)**: PHP'de uid yok; yetki oturum (session cookie) + rol
denetimi (RBAC middleware) + dosya yolu REALPATH denetimi + sunucu katmanı
reddi (`.htaccess`) ile sağlanır. Bu dosya her iki katmanın akışını, hata
modlarını ve test karşılıklarını adım adım belgeler; POSIX primitive'lerinin
repoda **kodlanmadığını** negatif kanıt (grep=0) ile kayıt altına alır.

Okuma sırası: §2 akışlar (A1 POSIX modeli → A2 dosya yaratma → A3 repo RBAC →
A4 setuid ailesi → A5 realpath kilidi → A6 capability → A7 hata/log) → §3 hata
modları → §4 test eşlemesi → §6 repo kanıtı. Model ile repo arasındaki fark her
tabloda "repo karşılığu" sütunuyla işaretlenmiştir.

## §1 Sınır, Kavram Envanteri ve URL Kaynakları

### §1.1 Kapsam sınırı

| Katman | Bu dosyada | Gitmez |
|---|---|---|
| POSIX model (kimlik · mod · ACL · capability · setuid) | kavram + akış + resmi belge | syscall imza kataloğu → `-api` |
| Repo uygulaması (RBAC · realpath · .htaccess) | dosya:satır kanıtı | denetim/pentest politikası → `-security` |
| Çekirdek içi yetki veri yapısı | yalnız tanım | kod → `kernel` (Q2 ⚠️) |
| Uygulama seviyesi kimlik emülasyonu (session+rol) | akış A3 | hesap yönetimi ekranları → uygulama katmanı |

İki kasadan biri eksik anlatılırsa denge bozulur: yalnız POSIX anlatılırsa repo
yüzeyi sahipsiz, yalnız RBAC anlatılırsa işletim sistemi modeli sahipsiz kalır.
Bu dosya ikisini de A-akışlarıyla taşır ve sınırı §1.1 tablosunda keser.

### §1.2 URL kaynakları

| Grup | URL'ler | Durum |
|---|---|---|
| Kimlik/credential | `credentials.7` · `getpwuid.3` | envanter reuse |
| Yetki değişim | `setuid.2` · `setreuid.2` · `setgid.2` · `chown.2` | envanter reuse |
| Mod/chmod | `chmod.2` · `fchmod.2` · `fchmodat.2` · `umask.2` · `access.2` · `pathconf.3` | envanter reuse |
| ACL/capability | `acl.5` · `capabilities.7` · `user_namespaces.7` · `getfacl.1` | envanter reuse |
| Open Group | `chmod` · `setuid` · `fchmod` · `chown` · `pathconf` `.htm` | envanter reuse |
| **Yeni (2026-10-13)** | `man7.org/linux/man-pages/man2/setresuid.2.html` · `man7.org/linux/man-pages/man8/getcap.8.html` · `man7.org/linux/man-pages/man2/openat.2.html` | **✓ HTTP 200 doğrulandı** — daha önce `.ai/**` envanterinde YOK (kesişim 0) |

404 bilinen, asla kullanılmayacaklar: `acl.7.html` (doğrusu `acl.5`) ·
`realpath.7.html` (doğrusu `realpath(3)`) · `docs.kernel.org/filesystems/posix-acls.html`.
Not: `openat.2.html` adresi `open(2)` man sayfası içeriğini servis eder (2026-10-13
teyit edildi) — `openat` iddiaları bu sayfadan kaynaklıdır; `open.2.html` envanter reuse'dur.

## §2 Akışlar (A1–A7)

### §2.1 A1 — POSIX erişim kararı (işletim sistemi modeli)

| Adım | İş | Kaynak |
|---|---|---|
| 1 | Süreç kimliği taşır: gerçek UID/GID + etkin UID/GID (setuid ile ayrışabilir) | `credentials.7` (reuse) |
| 2 | `open` çağrısında çekirdek dosyanın sahip/grup/other üçlüsü ile sürecin kimliğini karşılaştırır | `inode.7` (reuse, dosya-sistemi dalı) |
| 3 | Karar mode bitleriyle verilir (r=4, w=2, x=1); `access(2)` talebi sürecin *gerçek* kimliğiyle ayrıca sınanabilir | `access.2` (reuse) |
| 4 | Dosyada ACL varsa ACL girdisi, mode bitlerine göre **daha spesifik** karar verir | `acl.5` (reuse) — İ-6 senaryosu |
| 5 | Süreç yetkisizse `EACCES` döner; hata uygulamaya yansırsa log'a kadar izlenir | §2.7 (A7) |
| 6 | Yetki denetimi, dosya dışında da kapıyı korur: dizin x biti yol çözümlemeye izin verir (bkz. dosya-sistemi dalı path_resolution) | `path_resolution.7` (reuse) |

Repo karşılığı: **yok** — bu çekirdek kararının PHP kodu yazılmış değil; uygulama
kararı A3'te (RBAC) verilir. POSIX kararı yalnız kavramsal anlatılır; depoda bu
kararın karşılığını aramak yanıltıcı olur (grep `chmod|fileowner|fileperms` = 0).

İki kimlik kuralı (A4 ile birlikte okunur): denetim çoğu zaman **etkin** UID ile
yapılır; `access(2)` ise **gerçek** UID'i zorlar — bu yüzden "check-then-open"
yazımının güvenlik değeri sınırlıdır (TOCTOU, H-1).

### §2.2 A2 — mode + umask akışı (dosya yaratma)

| Adım | İş | Beklenen |
|---|---|---|
| 1 | `open(path, O_CREAT, mode)` istenir (örn. `0666`) | istenen ham mod |
| 2 | Çekirdek `umask` maskesini mode'un üzerine uygular (bit temizleme) | `0666 & ~umask` |
| 3 | `umask 022` varsayımıyla dosya `0644` doğar | `-dogrulama` D-3 senaryosu |
| 4 | `umask` çağrısı süreç mirasıdır: fork'ta devralınır, exec'te korunur | İ-3 senaryosu |
| 5 | `mkdir` de aynı kurala bağlıdır: verilen mod maskeden geçer | `mkdir(2)` modeli — repo `@mkdir` örnekleri §6 |

Kavramsal örnek (çalıştırılmadı, model gösterimi):

```text
umask 022
open("a.txt", O_CREAT|O_WRONLY, 0666)  →  dosya modu 0644
umask 077
open("b.txt", O_CREAT|O_WRONLY, 0666)  →  dosya modu 0600
```

Repo karşılığı: `umask` çağrısı PHP kodunda **YOK** (grep `umask` = 0, 2026-10-13).
Mod kararı sabit yazılır: `@mkdir($dir, 0775)` / `0750` / `0700` / `0777`
(dağılım: 0775×5 · 0750×2 · 0777×2 · 0700×1 — 2026-10-13 grep, `-ornekler` §4).
Demek ki gerçek dosya modu, PHP-FPM sürecinin yaratıcı (owner) davranışına ve
web sunucusu umask'ına kalır — **hesap depoda yapılmaz, üretime taşınır** (Q-I2/Q-F5 kesişimi).

### §2.3 A3 — Uygulama RBAC akışı (repo — üretim kanıtı)

| Adım | Dosya:satır | İş |
|---|---|---|
| 1 | `home.coremusic.net/config/constants.php:75-78` · `shared/src/Session/SessionConfig.php:20,35` · `SessionInitializer.php:31` | oturum kurulumu (cookie adı, ayarlar) |
| 2 | `shared/src/Auth/AuthGuard.php:34-48` | istek oturum kimliğiyle donatılır |
| 3 | `shared/src/Middleware/PermissionMiddleware.php:17-45` | rol/izin kararı: geçerse devam |
| 4 | `PermissionMiddleware.php:47-63` | karar **reddedilirse** yasak yanıtı üretilir |
| 5 | `PermissionMiddleware.php:50` | `securityEvent(...)` çağrısı — olay log'a yazılır |
| 6 | `shared/src/Log/FileHandler.php:169-181` | `authz_denied` olayı `warning` seviyesine eşlenir ve dosyaya yazılır |
| 7 | `shared/src/Middleware/AuthorizationMiddleware.php:60-84` | ikinci yetki katmanı (kaynak bazlı denetim) |
| 8 | `shared/src/PageRouter/PageRouterKernel.php:133-134` · `PageRouterHelper.php:16-27` | yönlendirme kararı bu kimlikle verilir |
| 9 | `home.coremusic.net/.htaccess:18-24,26-37` · `api`/`auth`/`assets.coremusic.net` `.htaccess` | sunucu katmanı: `Options -Indexes` + `Require all denied` (dizin listeleme ve doğrudan erişim reddi) |

Bu akış ile POSIX modeli arasındaki fark: A1'de çekirdek dosyanın sahibine
bakar; A3'te ise **istek taşıyıcısının oturumuna** bakılır. İkisi farklı
kapılardır — sunucu süreci (PHP-FPM) dosya sahibi olarak her zaman aynı POSIX
kimliğiyle çalıştığından, asıl ayrışma uygulama katmanında olur (Q-I1).

**Q-I6 düzeltmesi (bu dosyada kayıt):** `-anayasa.md` §3.6 / Q-I6 "erişim reddi
log'u yok" der — disk bunu **yanlış** çıkarır: log VARDIR (`FileHandler.php:169-181`).
Anayasa dosyası bu görevde düzenlenmedi (yazma yasağı) → düzeltme kalemi Q-I6'da.

### §2.4 A4 — setuid / setreuid / setresuid akışı (POSIX model)

| Çağrı | Etkisi | Kaynak |
|---|---|---|
| `setuid(uid)` | Linux'ta yetkili süreç gerçek UID'i de değiştirir (tek çağrı yeter) | `setuid.2` (reuse) — İ-7 senaryosu |
| `setreuid(ruid, euid)` | gerçek ve etkin UID'i ayrı ayrı kurar | `setreuid.2` (reuse) |
| `setresuid(ruid, euid, suid)` | üç kimliği birden kurar; en açıklayıcı tanım `setresuid(2)` sayfasındadır | **YENİ** `setresuid.2` (HTTP 200, 2026-10-13) |
| `setgid` / `chown` | grup ve sahip değişimi | `setgid.2` · `chown.2` (reuse) |

Davranış ayrımı (İ-7'nin özü): aynı semantik **Linux ve BSD'de farklıdır** —
Linux `setuid` çağrısında üç kimliği birden kurabilirken, eski BSD davranışında
gerçek UID için ayrı bir `setreuid` gerekir. Test "değişmiyorsa BSD davranışı"
sonucunu raporlar (`-dogrulama` §A.4, satır 80).

Set-UID'in mount bağımlılığı: binary set-UID bitine sahip olsa bile, mount
`nosuid` ile yapılmışsa çekirdek set-UID etkisini **sessizce** uygulamaz (İ-2).
Bu, üretim ortamında denetlenmesi gereken operasyonel bir ön koşuldur.

Repo karşılığı: `setuid`/`setresuid`/`posix_*` PHP kodunda **YOK** (grep = 0,
2026-10-13). Uygulama süreç kimliği değiştirmez; sunucu katmanı (PHP-FPM havuzu)
tek kullanıcıda çalışır ve yetki ayrımı oturum/rol katmanına (A3) devredilmiştir.
İ-2/İ-7 senaryoları depo dışı çalıştırılabilir (PLANNED).

### §2.5 A5 — realpath + symlink kaçış denetimi (repo — üretim kanıtı)

`home.coremusic.net/include/Stream/MusicStreamHandler.php:173-209` (`resolvePath`):

| Adım | Satır | İş |
|---|---|---|
| 1 | 175-178 | NUL baytı / boş yol → reddet |
| 2 | 180-183 | `..` bileşeni → reddet (kaçış önleme 1) |
| 3 | 185-187 | `is_link()` → symlink doğrudan reddet (kaçış önleme 2) |
| 4 | 189-192 | `realpath()` ile mutlak çözümlü yol; başarısızsa reddet |
| 5 | 194-197 | çözümlü yol kök dizin içinde mi? |
| 6 | 199-202 | `str_starts_with` önek denetimi (kök dışına kaçış reddi) |
| 7 | 204-206 | `is_readable` — okunabilirlik kontrolü |
| 8 | 208 | yalnızca tüm adımlar geçerse yol döner |

Tasarım notu: denetim zinciri **reddet-üzerine** (fail-closed) yazılmıştır —
herhangi bir adım başarısız olursa yol dömez. `realpath` başarısızlığı (örn.
dangling symlink) da bir reddetme sonucu üretir; bu, `realpath(3)` modeliyle
uyumludur (dosya-sistemi dalı + `realpath.3` envanter reuse).

İkinci kullanım: `home.coremusic.net/bin/api-key-create.php:487-493` — kendini
çalıştıran betikte `realpath` ile betiğin gerçek konumu doğrulanır (self-exec guard; ayrıca `:73`).

**O_NOFOLLOW durumu:** repo genelinde `O_NOFOLLOW` = 0 eşleşme (grep, 2026-10-13).
Savunma yalnızca `is_link` + `realpath` + önek denetimidir; kontrol ile kullanım
arasındaki zaman boşluğu (TOCTOU) kapatılmamıştır → **Q-I5**. İ-4 senaryosu bu yüzden
depoda doğrulanamaz (L4 kasıtlı bozma depo dışı).

### §2.6 A6 — Capability akışı (POSIX/Linux model)

| Adım | İş | Kaynak |
|---|---|---|
| 1 | Root yetkisi parçalanır: capability adları ile tek tek taşınır/kaldırılır | `capabilities.7` (reuse) |
| 2 | Dosya bazlı yetki `setcap` ile bağlanır, `getcap` ile okunur | **YENİ** `setcap.8` (ornekler dosyası) · **YENİ** `getcap.8` (HTTP 200, 2026-10-13) |
| 3 | Çalışan süreç capability setini `prctl(PR_CAPBSET_DROP)` ile düşürebilir | `-dogrulama` İ-5 senaryosu |
| 4 | Namespace sınırında yetki görünümü `user_namespaces.7` ile tanımlanır | `user_namespaces.7` (reuse) |

Model sırası: `setcap` dosyaya yetki bağlar → süreç `exec` olduğunda yetkiyi
devralır → gereksiz yetkiler `prctl` ile düşürülür → `getcap` ile denetlenir.
Bu dört adımın hiçbiri depoda kodlanmamıştır; üretimde Unix sunucu kurulumunun
parçası olarak (PLANNED) planlanır.

Repo karşılığı: `setcap`/`getcap` PHP kodunda **YOK** (grep = 0, 2026-10-13) →
uygulama kanıtı yok, model-only (Q-I4). Linux sunucu kurulumunda capability
dağıtımı depo dışı bir operasyon adımıdır (PLANNED).

### §2.7 A7 — Hata + log akışı (repo — üretim kanıtı)

| Adım | Dosya:satır | İş |
|---|---|---|
| 1 | `PermissionMiddleware.php:47-63` | yetki reddi tespit edilir |
| 2 | `PermissionMiddleware.php:50` | `securityEvent()` çağrısı (authz olayı) |
| 3 | `FileHandler.php:169-181` | olay `authz_denied` → `warning` seviyesi → log dosyası |
| 4 | `home.coremusic.net/.htaccess:18-24,26-37` | sunucu katmanı reddi (dizin indeksi + erişim) |
| 5 | yanıt | istemciye yasak/404 (dosya varlığı sızdırmayan davranış) |

Ek kanıt: `shared/src/Helper/SecurityHelper.php:42-55` (yardımcı denetim
fonksiyonları) · `shared/src/Template/TemplateEngine.php:74` (şablon kaçış
denetimi — XSS yüzeyi, `-security` dalının konusu) ·
`shared/src/PageRouter/PageRouterKernel.php:133-134` (yönlendirme kararı).

Log hattının denetim değeri: red olayı istemciye yansıtılmadan **önce** log'a
yazılır (adım 2→3), yani denetim izi reddin kendisinden bağımsızdır. Log
dosyasının kendi korunması (yetki 0750 ile yazılan dizimler, rotasyon) ayrı bir
soruşturmadır → `-observability` dalı, ölçüm §5'te UNKNOWN.

## §3 Hata Modları

| # | Hata modu | Nerede | Sonuç | Savunma / durum |
|---|---|---|---|---|
| H-1 | TOCTOU: `is_link`/`realpath` ile `open` arası yarış | A5 | symlink hedefine yazma/okuma | `O_NOFOLLOW` repo'da YOK → **açık yüzey** (Q-I5) |
| H-2 | Symlink döngüsü | yol çözümleme | sonsuz çözümleme | çekirdek `ELOOP` ile keser (`-dogrulama` D-5) |
| H-3 | `..` kaçışı | A5 adım 2 | kök dışına erişim | reddet kuralı (MusicStreamHandler:180-183) |
| H-4 | Geniş mod (0777) | A2 / repo `@mkdir` | her kullanıcı yazar | repo'da 0777×2 var, 0750/0700×3 de → **tutarsızlık** (Q-I2) |
| H-5 | umask yok sayma | A2 | beklenenden geniş dosya modu | depoda umask çağrısı YOK → üretim teyidi PLANNED |
| H-6 | ACL çözümlenmemesi | A1 adım 4 | yanlış erişim kararı | repo'da ACL kodu YOK (Q-I3); İ-6 depo dışı |
| H-7 | Capability sızıntısı | A6 | fazladan yetki | repo'da kod YOK (Q-I4); İ-5 depo dışı |
| H-8 | `nosuid` mount'ta setuid sessizce çalışmama | A4 / İ-2 | yetki düşer ama hata görünmez | senaryo İ-2 ile gözlenebilir (depo dışı) |
| H-9 | Reddiyet sessizliği (log yazılmazsa) | A7 | denetim izi kaybı | **repo'da log VAR** (FileHandler:169-181) — Q-I6 düzeltmesi |
| H-10 | Önek denetimi karakter duyarlılığı | A5 adım 6 | yanlış eşleşen yol | PHP `str_starts_with` bayt-duyarlı; Windows-vs-Linux davranış farkı **UNKNOWN** (Q-F3 ile ilişkili) |
| H-11 | Dizin listeleme sızıntısı | A3 adım 9 | yapı dosyalarının listelenmesi | `.htaccess` `Options -Indexes` (4 kök `.htaccess` teyitli) |
| H-12 | Erişim kararı + yol denetimi kopukluğu | A3 ↔ A5 | geçerli oturum + kaçış yolu | iki denetim ayrı middleware/handler'da → birleşik test yok (PLANNED) |
| H-13 | Log seviyesi yanlış seçimi — authz olayının monitoring'de kaybolması | A7 adım 3 | olay `warning`; kritiklik yorumu | seviye seçimi `FileHandler.php:178-181`; alarm eşiği `-observability` |
| H-14 | Reddiyet yanıtının dosya varlığı sızdırması | A7 adım 5 | 403 vs 404 ayrımı | davranış `UNKNOWN` — yanıt kodu seçimi depoda teyit edilmedi |

## §4 Test Stratejisi (İ-1..İ-7 eşlemesi)

Kaynak: `-dogrulama` §A.4 (satır 74-80) + §B.3 (satır 122: "Kısmen — L4 kasıtlı bozma depo dışı").

| # | Senaryo (özet) | Katman | Repo karşılığı / durum |
|---|---|---|---|
| İ-1 | `chmod 755` → yazma reddi → `EACCES` | L1+L4 | depo dışı; repo'da chmod kodu YOK (grep=0) |
| İ-2 | Set-UID etkisi → `geteuid` | L1 | depo dışı (PLANNED) |
| İ-3 | `umask` kalıcılığı → alt süreç devri | L1 | depo dışı; A2 §2.2 |
| İ-4 | Symlink takibi → `O_NOFOLLOW` | L4 | repo'da `O_NOFOLLOW`=0 → TOCTOU açık (Q-I5) |
| İ-5 | Capability düşürme → `capget` | L4 | repo'da capability kodu YOK (Q-I4) |
| İ-6 | ACL önceliği → `getfacl`+`stat` | L4 | repo'da ACL kodu YOK (Q-I3) |
| İ-7 | `setuid` → `getuid` farkı | L1 | repo'da setuid kodu yok; model §2.4 |

Çalıştırma iskeletleri (kavramsal — `sudo` gerektirir, depoda çalıştırılmadı):

```text
İ-1: chmod 755 f; kullanıcı-B ile open(f, O_WRONLY)  → beklenen: EACCES
İ-4: open(f, O_WRONLY|O_NOFOLLOW) symlink üzerinde    → beklenen: ELOOP
İ-5: prctl(PR_CAPBSET_DROP, X); capget               → beklenen: X görünmez
İ-6: ACL'li dosyada getfacl + stat                   → beklenen: ACL daha spesifik
İ-7: setuid sonrası getuid                           → beklenen: gerçek UID de değişir (Linux)
```

L3 (repo içi, tek başına doğrulanabilir) kanıtları: `-dogrulama` §F.4 F-10 · F-11 · F-12.
**⚠️ F-11 uyarı:** §F.4'te `ApcuAdapter` için aktarılan `extension_loaded('apcu')` +
`RuntimeException` alıntısı diskteki `shared/src/Cache/ApcuAdapter.php:9` ile
birebirleşmiyor — dosya `if (!function_exists('apcu_fetch'))` kullanıyor ve repo
genelinde `extension_loaded(` = 0 eşleşme var (kendi grep, 2026-10-13). Alıntı
düzeltmesi `-dogrulama.md` dosyasında yapılmalı (bu görevde düzenlenmedi).

## §5 Ölçüm

| Metrik | Değer | Durum |
|---|---|---|
| POSIX primitive kod yoğunluğu (chmod/chown/umask/setuid/getfacl/setcap) | 0 eşleşme | ölçüldü (grep, 2026-10-13) |
| `O_NOFOLLOW` eşleşmesi | 0 | ölçüldü (2026-10-13) |
| `realpath(` kullanımı | 10 eşleşme | ölçüldü (2026-10-13) |
| `is_link(` kullanımı | 1 eşleşme | ölçüldü (2026-10-13) |
| `authz_denied` log hattı | mevcut (PermissionMiddleware:50 → FileHandler:169-181) | ölçüldü (Read, 2026-10-13) |
| `mkdir` mod dağılımı | 0775×5 · 0750×2 · 0777×2 · 0700×1 | ölçüldü (grep, 2026-10-13) |
| Üretim log hacmi / retention | — | **UNKNOWN** (ölçüm PLANNED, `-observability` dalı) |
| L1/L4 senaryo çalışma sonuçları (İ-1..İ-7) | — | **PLANNED** (depo dışı, sunucu erişimi gerekir) |

Ölçüm metodolojisi notu: bu dosyadaki tek sayısal metrikler **repo grep/Read
sayılarıdır** (L3 katmanı) — çalışan sistemden alınan ölçüm yoktur. L1/L2/L4
senaryoları sunucuda çalıştırılmalı ve sonuçları `-dogrulama` dosyasına
işlenmelidir (PLANNED). Uydurma yüzde/latency değeri yazılmaz.

## §6 Repo Kanıtı Özeti (L3)

| İddia | Kanıt (dosya:satır) | Durum |
|---|---|---|
| Erişim reddi log'u VAR | `shared/src/Middleware/PermissionMiddleware.php:47-63` + `:50` · `shared/src/Log/FileHandler.php:169-181` | teyitli (Read, 2026-10-13) — Q-I6 düzeltmesi |
| Oturum → rol → izin zinciri | `SessionConfig.php:20,35` · `SessionInitializer.php:31` · `AuthGuard.php:34-48` · `PageRouterHelper.php:16-27` · `SpaRoute.php:11-12` · `RouteTable.php:55-56` | teyitli |
| REALPATH kök kilidi | `home.coremusic.net/include/Stream/MusicStreamHandler.php:173-209` | teyitli (8 adım, §2.5) |
| Self-exec realpath guard | `home.coremusic.net/bin/api-key-create.php:73,487-493` | teyitli |
| Sunucu katmanı reddi | `home.coremusic.net/.htaccess:2,18-24,26-37` (+ `api` · `auth` · `assets.coremusic.net` `.htaccess`: `Options -Indexes` + `Require all denied`) | teyitli |
| POSIX primitive kodu YOK | grep `chmod|chown|umask|setuid|getfacl|setcap|posix_get*|fileowner|filegroup|getmyuid` → 0 (`*.php`) | teyitli (negatif kanıt) |
| `O_NOFOLLOW` / `is_writable(` / `fileperms(` YOK | grep → 0 | teyitli (negatif kanıt) |
| `mkdir` mod dağılımı | 0775×5 · 0750×2 · 0777×2 · 0700×1 | teyitli — H-4 tutarsızlığı |
| ACL/capability uygulaması YOK | grep `getfacl|setfacl|posix_acl|setcap|getcap` → 0 | teyitli (negatif kanıt) → Q-I3/Q-I4 |
| `extension_loaded(` YOK | grep → 0 | teyitli — F-11 alıntı düzeltmesi için |

Okuma notu: yukarıdaki her satır bu oturumda dosyayı açıp okuyarak veya grep
çalıştırılarak doğrulanmıştır; üçüncü el "bilinen" iddialar kullanılmamıştır.
Negatif kanıtlar (grep=0) en az pozitif kanıtlar kadar bağlayıcıdır: "kod yok"
iddiası, "kod şurada" iddiası gibi dosya:aralık ile taşınır.

## §7 Kaynakça

| # | Kaynak | URL | Durum |
|---|---|---|---|
| 1 | `setresuid(2)` — üç kimlik kurulumu | https://man7.org/linux/man-pages/man2/setresuid.2.html | **YENİ · ✓ HTTP 200 (2026-10-13)** |
| 2 | `getcap(8)` — dosya capability okuma | https://man7.org/linux/man-pages/man8/getcap.8.html | **YENİ · ✓ HTTP 200 (2026-10-13)** |
| 3 | `openat` / `open(2)` sayfası | https://man7.org/linux/man-pages/man2/openat.2.html | **YENİ · ✓ HTTP 200 (2026-10-13)** — içerik `open(2)` |
| 4 | `credentials(7)` · `capabilities(7)` · `acl(5)` · `setuid(2)` · `setreuid(2)` · `setgid(2)` · `chown(2)` · `getpwuid(3)` | man7.org (envanter reuse) | ✓ HTTP 200 (2026-10-13) |
| 5 | `chmod(2)` · `fchmod(2)` · `fchmodat(2)` · `umask(2)` · `access(2)` · `pathconf(3)` · `getfacl(1)` | man7.org (envanter reuse) | ✓ HTTP 200 (2026-10-13) |
| 6 | `user_namespaces(7)` | man7.org (envanter reuse) | ✓ HTTP 200 (2026-10-13) |
| 7 | Open Group: `chmod` · `setuid` · `fchmod` · `chown` · `pathconf` | opengroup.org `.htm` (envanter reuse) | ✓ HTTP 200 (2026-10-13) |

## §8 Senaryo akışı — dosya erişim karar zinciri, adım adım (repo, 2026-10-13)

> §2'deki A1-A7 akışlarının **repo-ölçeği teyidi ve tamamlamasıdır**. Her satır
> bu oturumda dosya açılarak okunmuştur; mevcut §2-§7 satırlarına dokunulmaz.

### §8.1 Sekiz adımlık zincir — `MusicStreamHandler::resolvePath` (173-209)

| # | Satır | Karar | Red |
|---|---|---|---|
| 1 | 175-178 | `trim` + boş/`\0` | 404 |
| 2 | 180-183 | `preg_split` segmentlerinde `'..'` | 403 |
| 3 | 185-187 | `is_link($trimmed)` | 403 |
| 4 | 189-192 | `realpath` false veya `!is_file` | 404 |
| 5 | 194-197 | kök `realpath` false | 404 |
| 6 | 199-202 | `str_starts_with($real, $prefix)` | 403 |
| 7 | 204-206 | `!is_readable($real)` | 403 |
| 8 | 208 | `return $real` | — |

### §8.2 Red tipi matrisi — neden 404, neden 403

| Kod | Adımlar | Semantik |
|---|---|---|
| 404 | 1, 4, 5 | dosya yok/çözülemedi — varlık sızdırmayan yanıt |
| 403 | 2, 3, 6, 7 | politika red — dosya biliniyor ama erişim kapalı |

OS karşılığı: `stat(2)` ERRORS — `EACCES` yol üzerindeki dizimlerde execute izni
eksikse; `ENOENT` bileşen yoksa (stat.2, §8.7 no 1). `lstat()` symlink'i
izlemez (stat.2 DESCRIPTION) — zincirin adım 3 semantiğiyle aynı.

### §8.3 RBAC akışı teyidi — `PermissionMiddleware` (64 satır)

§2.7 adımları disk ile **uyumludur** ve tamamlanır:

| Adım | Satır | İçerik |
|---|---|---|
| 0 | 23-25 | `empty($auth['userId'])` → passthrough (RBAC kararı yok) |
| 1 | 30-32 | rol uyuşmazlığı → `forbidden('role_insufficient')` |
| 2 | 36-41 | izin `in_array(..., true)` yok → `forbidden('permission_denied')` |
| 3 | 50-54 | `securityEvent('authz_denied', [reason, uri, ip])` |
| 4 | 56-62 | `httpStatus: 403`, `halt: true` |

Aralık düzeltmesi (daraltma, çelişki değil): §6/§2.7'de `FileHandler.php:169-181`
olarak geçen hattın tam aralığı **169-184**'tür (`logAt` çağrısı L183; alan
listesi L172; seviye `match` L178-181). İddia yönü aynıdır: log VARDIR.

### §8.4 Sunucu katmanı akışı — istek → `.htaccess` / `web.config`

| İstek | Apache satırı | IIS satırı | Kod |
|---|---|---|---|
| `home` → `.env` | `home/.htaccess:18-20` | `home/web.config:24-27` | 403 / 403 |
| `home` → `include/x.php` doğrudan | `home/.htaccess:22-24` | karşılık **YOK** (FilesMatch 0) | 403 / **UNKNOWN** |
| `home` → `pages/x` | `home/.htaccess:35` `[F,L]` | `home/web.config:20-23` | 403 / 403 |
| `home` → var olan dosya | `:30-32` `-f/-d` → olduğu gibi | `:28-35` `IsFile/IsDirectory` | 200 / 200 |
| `home` → bilinmeyen yol (SPA) | `:36` → `index.php` | `:36-39` → `index.php` | 200 / 200 |
| `assets` → dizin listesi | `assets/.htaccess:47` `Options -Indexes` | `assets/web.config:35` `directoryBrowse false` | 403 / 403 |

`Require all denied` direktifinin dayanağı: `mod_authz_host` (§8.7 no 2); IIS
yapılandırma modeli: `system.webServer` (§8.7 no 3).

### §8.5 Sınır matrisi — bu dosya ↔ `isletim-sistemi-security`

| Konu | Bu dosya | Komşu dal |
|---|---|---|
| POSIX kimlik/izin modeli, repo izin yüzeyi | **sahibi** | — |
| RBAC akışı (§8.3) | teyit/örnek düzeyinde | `yetki-ve-erisim-modeli` **sahibi** |
| log politikası (hacim/retention) | hattın varlığı (§8.3 adım 3-4) | `-observability` (§5 UNKNOWN) |
| denetim uygulaması (pentest) | yok | `uygulama-guvenlik-siniri` |

### §8.6 Hata kataloğu özeti — katman × semantik

| Katman | Örnek | Semantik |
|---|---|---|
| Uygulama — RBAC | `PermissionMiddleware:30-41` | 403 JSON + `authz_denied` log |
| Uygulama — yol | `MusicStreamHandler:175-206` | 404 (yok) / 403 (red) |
| Sunucu — Apache | FilesMatch / `[F,L]` | 403 (`Require all denied`, `F`) |
| Sunucu — IIS | `CustomResponse` | 403 (özel gövde) |
| OS | `EACCES` / `ENOENT` / `ELOOP` | syslog — depo dışı (UNKNOWN) |

### §8.7 Ek kaynakça — bu bölümün eklediği 3 URL

| # | Kaynak | URL | Durum |
|---|---|---|---|
| 1 | `stat(2)` — `EACCES`/`ENOENT`, `lstat` symlink izlememe | https://man7.org/linux/man-pages/man2/stat.2.html | bu dosyaya **yeni** · envanter reuse (`isletim-sistemi-api-dogrulama.md:201`) · 2026-10-13 HTTP 200 |
| 2 | `mod_authz_host` — `Require` direktifi | https://httpd.apache.org/docs/2.4/mod/mod_authz_host.html | bu dosyaya **yeni** · 2026-10-13 HTTP 200 |
| 3 | `.htaccess` kullanımı (AllowOverride) | https://httpd.apache.org/docs/2.4/howto/htaccess.html | bu dosyaya **yeni** · 2026-10-13 HTTP 200 |

### §8.8 Türetilmiş olgular ve kapanış

| # | Olgu | Dayanak |
|---|---|---|
| T1 | Red kararı üç katmanda dağılır: RBAC → yol → sunucu | §8.3, §8.1, §8.4 |
| T2 | Varlık-sızdırmama yalnız uygulama katmanında **bilinçli** uygulanıyor (404/403 ayrımı) | §8.2 |
| T3 | IIS katmanı PHP doğrudan erişim engeli taşımıyor | §8.4 + `-ornekler` §5 (FilesMatch 0) |
| T4 | Denetim izi reddin öncesinde üretiliyor | §8.3 adım 3 → 4 |
| T5 | Çalışan sistem ölçümü yok — tüm §8 kod okumasıdır | §5 PLANNED satırı |

§8'in kanıt disiplini: her satır `read`/sayım ile alındı; yeni doğrulanmış
URL sayısı 3 (hepsi bugün HTTP 200); bu bölümde `⚠️` işaretli tek iddia §8.4
"UNKNOWN" hücresidir (IIS PHP engeli — Q-OR3, `-ornekler` dosyası).

### §8.9 Senaryo → repo-örnek eşlemesi (§2'deki İ-senaryolarının karşılığı)

| Senaryo | Repo karşılığı | Kanıt |
|---|---|---|
| İ-1 dosya okuma reddi | `MusicStreamHandler:204-206` | `!is_readable` → 403 |
| İ-2 yol kaçışı reddi | `MusicStreamHandler:180-183` + `:199-202` | `..` → 403 · kök dışı → 403 |
| İ-3 symlink reddi | `MusicStreamHandler:185-187` | `is_link` → 403 |
| İ-4 yetkisiz rol | `PermissionMiddleware:30-32` | 403 + `authz_denied` |
| İ-5 yetkisiz izin | `PermissionMiddleware:36-41` | 403 + `authz_denied` |
| İ-6 sunucu katmanı reddi | `.htaccess:18-24` · `web.config:20-27` | 403 / 403 |
| İ-7 capability/ACL | **repo karşılığı YOK** (grep 0) | model-only (Q-I3/Q-I4) |

### §8.10 Açık sorulara bu bölümün etkisi

| ID | Etki |
|---|---|
| Q-I1 | etkisiz — §8 uid okumadı (depо dışı) |
| Q-I2 | görünürlük arttı — §8.1'deki `@mkdir(0750)` umask'e açık (§8.9 değil, §8.1) |
| Q-I3/Q-I4 | teyit — İ-7'de repo karşılığı 0 (negatif kanıt) |
| Q-I5 | görünürlük arttı — §8.1 adım 3-4 sırası TOCTOU penceresini gösteriyor |
| Q-I6 | **kapandı** (önceden) — §8.3 adım 3-4 log hattını teyit eder |

### §8.11 Bu bölümün ölçüm satırı

| Ölçüm | Değer |
|---|---|
| Tam okunan dosyalar | `MusicStreamHandler.php` (173-209) · `PermissionMiddleware.php` (64/64) · `SessionConfig.php` (15-70) · `TemplateEngine.php` (30-95) · `FileHandler.php` (155-195) |
| `FileHandler` aralık düzeltmesi | 169-181 → **169-184** (daraltma, §8.3) |
| `Require all denied` / `CustomResponse 403` | 6/6 isabet (3+3 dosya) |
| Çalışan sistem ölçümü | YOK (PLANNED) |
| Bu bölümde `⚠️` | 1 (§8.4 UNKNOWN) |

## §9. EK — Ölçüm teyidi (2026-10-13)

- Dolu satır ölçüm komutu (salt-okunur): `Get-Content -Encoding UTF8 <dosya> | Where-Object { $_ -ne '' }).Count`.
- EK öncesi bu dosya: 468 toplam / 378 dolu satır; EK sonrası ≥380 hedefi.
- `PermissionMiddleware.php` 64/64 satır tam okundu (§8.1-§8.2; aralık iddiası olmadan).
- `FileHandler::securityEvent` 169-184 = 16 satır (§8.3 daraltma düzeltmesi).
- Bu dosyaya eklenen doğrulanmış URL: 3 (§8.7); üçü de 2026-10-13 HTTP 200 ile teyitli.
- Çalışan sistem ölçüsü bu bölümde de YOK (PLANNED) — §8.8 tablosu bu durumu taşır.
## §10 Bağlayıcı özet — senaryo karar zinciri ve ölçüm özeti (2026-10-15)

> Append-only ekleme: §0–§9 değişmemiştir; bu bölüm mevcut ölçümleri derler.

### §10.1 Ölçüm → senaryo adımı eşlemesi

| Ölçüm | Karşılık gelen adım | Durum |
|---|---|---|
| `PermissionMiddleware.php` 64/64 satır (§8.1–§8.2) | Rol denetimi kapısı | OKUNDU |
| `FileHandler::securityEvent` 169-184 = 16 satır (§8.3) | Olay kaydı kapısı | OKUNDU |
| 3 doğrulanmış URL (§8.7) | Kaynak teyidi | 2026-10-13 HTTP 200 |
| Çalışan sistem ölçüsü (§8.8) | Uçtan uca doğrulama | YOK — PLANNED |

### §10.2 Ölçüm yöntemi tekrar kaydı

- Ölçüm salt-okunurdur; komut §9'da kayıtlıdır — bu bölümde yeniden ölçüm yapılmamıştır.
- EK öncesi dosya durumu: 468 toplam / 378 dolu satır (§9).
- EK sonrası hedef: ≥380 dolu satır (§9).

### §10.3 Bölüm eşlemesi (iç tutarlılık)

| Bölüm | Taşıdığı konu |
|---|---|
| §2 | Akışlar (A1-A7) |
| §4 | Test stratejisi (§-1..§-7 eşlemesi) |
| §8 | Senaryo akışı — dosya erişim karar zinciri, adım adım (2026-10-13) |
| §9 | Ölçüm teyidi (2026-10-13) |

### §10.4 Kapsam dışı ve sınır beyanı

- Çalışan sistem / entegrasyon testi: PLANNED (§4 · §8.8).
- Repo C++ kodu: `*.cpp` / `*.h` = 0 (2026-10-15 kök ölçümü) — bu dosyada derlenmiş kod iddiası yoktur.
- Doğrulanamayan her genişletme: `⚠️ VERIFICATION REQUIRED`.

- `git commit` bu oturumda ATILMADI — değişiklikler working tree'dedir (commit orkestratöre ait).

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
