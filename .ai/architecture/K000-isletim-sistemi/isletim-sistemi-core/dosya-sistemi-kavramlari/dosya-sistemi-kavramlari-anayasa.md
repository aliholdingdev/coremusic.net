---
node_id: K000-isletim-sistemi/isletim-sistemi-core/dosya-sistemi-kavramlari/dosya-sistemi-kavramlari-anayasa
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-core/dosya-sistemi-kavramlari
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-core/dosya-sistemi-kavramlari
evidence:
  - https://man7.org/linux/man-pages/man7/path_resolution.7.html
  - https://man7.org/linux/man-pages/man7/inode.7.html
  - https://man7.org/linux/man-pages/man2/open.2.html
  - https://man7.org/linux/man-pages/man2/fcntl.2.html
  - https://docs.kernel.org/filesystems/vfs.html
  - home.coremusic.net/include/Stream/MusicStreamHandler.php:189
validation: "2026-10-13 · 5 URL HTTP 200 (3'ü -dogrulama §E.3 havuzundan yeniden, 2'si bu turda envantere eklendi: open.2 · fcntl.2) · repo satırı Read ile birebir · içerik 380-460 satır"
open_questions:
  - "Kademe etiketi çelişkisi: bu dosya Kademe-5 (bellek şablonu) · kök `index.md` §2 ise içeriği Kademe-4 sayıyor → ⚠️ VERIFICATION REQUIRED (Q-F1)"
  - "Kilit primitive'inin repo'daki karşılığı (PHP `LOCK_EX` → hangi syscall?) → ⚠️ VERIFICATION REQUIRED (Q-F2)"
  - "Hedef üretim dosya sistemi türü (ext4/NTFS/NFS) → UNKNOWN (Q-F3)"
  - "Dosya tanıtıcısı sınır sayısının üretim değeri (`RLIMIT_NOFILE`) → UNKNOWN — ölçüm PLANNED (Q-F4)"
group: mimari
---

# dosya-sistemi-kavramlari-anayasa — Bağlayıcı Tanımlar ve İlkeler (Kademe-5)

## §0 ELI15

Dosya sistemi, işletim sisteminin **her şeyi dosya gibi gösterme** hilesidir: disk
bölmesi, ağ yazıcısı ve normal bir metin dosyası aynı arayüzden çağrılır. Çekirdek
her yola bakar, kimin hangi bayta erişebileceğine karar verir ve erişimi bir
**dosya tanıtıcısına** (fd) bağlar. Bu anayasa altı kavramı — **VFS · inode/dizin ·
dosya tanıtıcısı · yol çözümleme · sembolik bağlantı · kilit** — bağlayıcı biçimde
tanımlar; mekanizma ayrıntısı `-surucu`, repo yüzeyi `-ornekler`dadır.

## §1 Kimlik

| Alan | Değer |
|---|---|
| node_id | `K000-isletim-sistemi/isletim-sistemi-core/dosya-sistemi-kavramlari/dosya-sistemi-kavramlari-anayasa` |
| node_type / status | `module` / `draft` |
| parent_id | `K000-isletim-sistemi/isletim-sistemi-core/dosya-sistemi-kavramlari` |
| Kademe | Kademe-5 (içerik) — bu dalın `index.md`'si Kademe-4 (bkz. Q-F1) |
| group | `mimari` (ADR-097 §5.5 · registry §8.1 — 9. FM alanı) |
| Bağımlılık | üst düğüm `dosya-sistemi-kavramlari` (Kademe-4) → `isletim-sistemi-core` (Kademe-2) |
| Komşular | `-api` (syscall yüzeyi) · `-security` (uygulama) · `-kernel` (iç yapı) · `K001-donanım` (disk) |
| Kapsam kaynağı | `isletim-sistemi-core-kapsam.md` §A.8 madde 3 · §B.3 |
| Doğrulama kaynağı | `isletim-sistemi-core-dogrulama.md` §A.3 · §E.3 · §F.3 |

## §2 Kavram modeli — altı omurga maddesi

### M1 — VFS (sanal dosya sistemi katmanı)

| Alan | Tanım |
|---|---|
| Nedir | Çekirdeğin uygulama ile gerçek dosya sistemi (ext4/NTFS/NFS…) arasına koyduğu **tek arayüz katmanı** |
| Ne işe yarar | Aynı `open`/`read`/`write` çağrıları her depolama türünde aynı davranır; uygulama sürüm bilmez |
| Kaynak | `https://docs.kernel.org/filesystems/vfs.html` (2026-10-13 HTTP 200 · `-dogrulama` §E.3 havuzu) |
| Sınır | Katman **tarif** edilir; VFS nesne modelinin çekirdek kodu `-kernel` dalındadır |
| Bu repoda | Üretemez — repo çekirdek kodu içermez; yalnız çağıran taraf vardır (`-ornekler` §1) |

### M2 — inode, dizin ve kök

| Alan | Tanım |
|---|---|
| Nedir | Bir dosyanın **kendisi**: boyut, sahip, mod, zaman damgaları ve veri bloğu göstergeleri |
| Dizin | Dosya adı → inode numarası eşlemesini tutan özel bir dosya; `.` bu dizin, `..` bir üstü |
| Kök (`/`) | Dizin ağacının başlangıcı; her mutlak yol buradan tırmanır |
| Kaynak | `https://man7.org/linux/man-pages/man7/inode.7.html` · `https://man7.org/linux/man-pages/man7/hier.7.html` (§E.3 havuzu, 2026-10-13) |
| Sınır | İsimle erişim kavramı burada; `stat`/`lstat` syscall yüzeyi `-api` |
| Kritik ayrım | Ad, inode'un kendisi değildir — ad silinince inode link sayısı sıfırsa serbest kalır (`unlink.2`, §E.3) |

### M3 — Dosya tanıtıcısı (fd) ve açık dosya tanımı

| Alan | Tanım |
|---|---|
| Nedir | `open()`'ın döndürdüğü **küçük, negatif olmayan tamsayı**; sürecin açık dosya tablosundaki indeksi |
| Açık dosya tanımı | fd'nin işaret ettiği kayıt: dosya ofseti + durum bayrakları (POSIX adı: *open file description*) |
| Paylaşım kuralı | `dup()`/`fork()` ile **aynı** tanıma giden iki fd ofseti paylaşır; ayrı `open()` çağrıları paylaşmaz |
| Kaynak | `https://man7.org/linux/man-pages/man2/open.2.html` (bu tur envantere eklendi · 2026-10-13 HTTP 200) |
| Sınır | `dup`/`lseek`/`fcntl` çağrı yüzeyi `-api` (kapsam §E.1 O1/O2) — kavram burada, çağrı orada |
| Ölçüm | Üretimde açık fd tavanı (`RLIMIT_NOFILE`) → UNKNOWN (Q-F4) |

### M4 — Yol çözümleme (path resolution)

| Alan | Tanım |
|---|---|
| Nedir | Bir yol dizesinin bir dosyaya **çevrilme zinciri**: her bileşen tek tek çözülür |
| Sıra | Mutlak yol → kökten başla · göreli yol → çalışma dizininden (`chdir` ile değişebilir) |
| Bileşen kuralları | `/` ayırıcı, `.` bu dizin, `..` bir üst dizin, son bileşen dosya olabilir, son öncesindekiler dizin olmak **zorunda** |
| Hata sonuçları | `ENOENT` (yok) · `ENOTDIR` (dizin değil) · `EACCES` (arama izni yok) · `ELOOP` (çok fazla symlink) |
| Kaynak | `https://man7.org/linux/man-pages/man7/path_resolution.7.html` · `https://docs.kernel.org/filesystems/path-lookup.html` (§E.3 havuzu, 2026-10-13) |
| Sınır | Çözümleme **tasarımı** burada; `open`/`stat` syscall listesi `-api` |

### M5 — Sembolik bağlantı (symlink), `.`/`..` ve kaçış

| Alan | Tanım |
|---|---|
| Nedir | İçeriği **başka bir yol** olan özel dosya; çözümleyici onu takip eder |
| Kaçış riski | `..` ve symlink birleşince yol hedef ağacın **dışına** çıkabilir (symlink→`/` gibi) |
| Koruma kavramları | Sembolik linki **reddetme**, gerçek yolu **çözme** ve sonuçtaki kök ile **ön ek karşılaştırması** |
| Kaynak | `https://man7.org/linux/man-pages/man7/symlink.7.html` · `https://man7.org/linux/man-pages/man2/readlink.2.html` · `https://man7.org/linux/man-pages/man2/open.2.html` (`O_NOFOLLOW`, `O_EXCL`) |
| Repo karşılığı | `home.coremusic.net/include/Stream/MusicStreamHandler.php:185` (`is_link`) · `:189` (`realpath`) |
| Sınır | TOCTOU/uçtan uca denetim **politikası** `-security` dalında; burada yalnız kavram + repo sargısı |

### M6 — Kilit (flock · POSIX kayıt kilidi · bayt aralığı kilidi)

| Alan | Tanım |
|---|---|
| Nedir | Eşzamanlı erişimde **çakışmayı** engelleyen mekanizma; kilit dosyanın kendisine değil, açık tanıma/tanımlamaya bağlanır |
| Tür 1 — `flock` | Tüm dosya üzerinde **paylaşılan** (`LOCK_SH`) veya **özel** (`LOCK_EX`) kilit |
| Tür 2 — POSIX kayıt kilidi | `fcntl` ile **belirli bayt aralığına** kilit (`F_SETLK` · `F_SETLKW` · `F_GETLK`) |
| Tür 3 — bayt aralığı (Windows) | `LockFile` ile **belirli ofset/uzunluk bölgesine** özel erişim |
| Kaynak | `https://man7.org/linux/man-pages/man2/flock.2.html` (§E.3 havuzu) · `https://man7.org/linux/man-pages/man2/fcntl.2.html` (bu tur envantere eklendi) · `https://learn.microsoft.com/en-us/windows/win32/api/fileapi/nf-fileapi-lockfile` (bu tur envantere eklendi) |
| Sınır | `flock`/`fcntl` syscall listesi `-api`; **kilit politikası ve uygulama katmanı** `-security`; burada yalnız tanım |
| Repo karşılığı | `LOCK_EX` kullanan 6 PHP satırı (`-ornekler` §1) — OS karşılığı ⚠️ VERIFICATION REQUIRED (Q-F2) |

### M7 — Kalıcılık (fsync · fdatasync · sync)

| Alan | Tanım |
|---|---|
| Nedir | Yazılan verinin **gerçek depoya** ulaşmasını garanti eden senkronizasyon |
| `fsync` | Veri + tüm meta veri diske itilir |
| `fdatasync` | Veri + okumak için gereken meta (boyut vb.) itilir |
| `sync` | Sistem geneli yazma tamponları (kapsam/görev sınırı geniş) |
| Kaynak | `https://man7.org/linux/man-pages/man2/sync.2.html` · `https://man7.org/linux/man-pages/man2/fdatasync.2.html` (§E.3 havuzu · 2026-10-13) |
| Sınır | Kalıcılığın **testi** `-testing` (`-dogrulama` §A.3 D-1); disk/IO ayrıntısı `K001-donanım` |

### M8 — Bağlantı sayısı (sert bağ / `link` · `unlink`)

| Alan | Tanım |
|---|---|
| Nedir | Aynı inode'a giden **ad sayacı**; ad ≠ inode |
| `link` | Aynı dosya sistemi içinde ikinci ad üretir (semablotik link değil) |
| `unlink` | Sayacı azaltır; 0'a inince inode serbest kalır |
| Kaynak | `https://man7.org/linux/man-pages/man2/link.2.html` · `https://man7.org/linux/man-pages/man2/unlink.2.html` (§E.3 havuzu · 2026-10-13) |
| Sınır | Symlink ile ayrım M5'tedir; atomik ad değişimi M9 + §3 A5'e bakılır |

### M9 — Mount (bağlama)

| Alan | Tanım |
|---|---|
| Nedir | Bir dosya sisteminin ağaçtaki bir **noktaya** yerleştirilmesi (mount point) |
| Ne değiştirir | Bağlama noktasının altındaki yol çözümleme artık o dosya sistemine gider |
| Sınır kuralı | `rename` yalnız **aynı mount içinde** atomiktir; mount'lar arası kopyala-sil olur |
| Kaynak | `https://man7.org/linux/man-pages/man2/mount.2.html` · `https://docs.kernel.org/filesystems/mount_api.html` (§E.3 + §I.1 havuzları · 2026-10-13) |
| Sınır | Bağlama **kavramı** burada; mount syscall ve bayrakları `-api` |

### §2.1 — Bu dalın kavram → dosya dağılımı

| # | Kavram | Gittiği dosya | Bölüm |
|---|---|---|---|
| 1 | VFS · inode/dizin/kök · mount | `-anayasa` (bu dosya) | §2 M1 · M2 · M9 |
| 2 | Dosya tanıtıcısı · paylaşılan ofset | `-anayasa` + `-surucu` | §2 M3 · `-surucu` §1 |
| 3 | Yol çözümleme · `.`/`..` · symlink | `-anayasa` + `-surucu` | §2 M4 · M5 · §3 A1-A2 |
| 4 | Kilit (flock · POSIX · bayt aralığı) | `-anayasa` + `-ornekler` | §2 M6 · §3 A3 |
| 5 | `umask` → mode → ACL etkileşimi | `-anayasa` + `izin-ve-guvenlik-kavramlari` | §3 A4 · O7 sınırı |
| 6 | Repo gerçek örnekleri, anti-pattern | `-ornekler` | §1-§4 |

### §2.2 — Platform yansımaları (kavram sabit, ad değişir)

| Kavram | POSIX/Linux | Windows | Not |
|---|---|---|---|
| Dosya erişim nesnesi | dosya tanıtıcısı (fd) | `HANDLE` | her ikisi de açık tanıma bağlanır |
| Açma | `open(2)` | `CreateFileA` | §E.3 havuzu · 2026-10-13 |
| Erişim kontrolü | mod bitleri + ACL | DACL / ACE | §E.3 (`file-security-and-access-rights` · `access-control-entries`) |
| Kilitleme | `flock` · `fcntl` kayıt kilidi | `LockFile` / `LockFileEx` | bu tur: `fcntl.2` + `LockFile` eklendi |
| Atomik ad değiştirme | `rename` (aynı mount) | `MoveFile` ailesi | `rename.2` §E.3 · Windows `MoveFile` **doğrulanmadı → kullanılmaz** |
| Kalıcılık | `fsync` / `fdatasync` | `FlushFileBuffers` | ⚠️ `FlushFileBuffers` bu turda **doğrulanmadı → kullanılmaz (UNKNOWN)** |

## §3 Mekanizma akışı — altı olay zinciri (A1-A6)

### A1 — Mutlak yol çözümleme zinciri

| # | Adım | Ne olur | Hata |
|---|---|---|---|
| 1 | Yol ayrıştırma | `/` ile bölünür, boş bileşenler atılır | `ENAMETOOLONG` |
| 2 | Başlangıç seçimi | Mutlak → kök; göreli → çalışma dizini | `ENOTDIR` |
| 3 | Bileşen çözümleme | Her bileşen üst dizinde aranır | `ENOENT` |
| 4 | Dizin denetimi | Her adımda dizinde **arama (x) izni** gerekir | `EACCES` |
| 5 | Link takibi | Bileşen symlink ise hedefi okunup devam edilir; sayaç +1 | `ELOOP` (aşımda) |
| 6 | Son bileşen | Dosya/dizin; `O_CREAT` ile yoksa oluşturulur | `EEXIST` (`O_CREAT|O_EXCL`) |
| 7 | Sonuç | Dosya tanıtıcısı üretilir, ofset 0'a konur | — |

> Kaynak: `path_resolution.7` · `path-lookup.html` (§E.3 havuzu · 2026-10-13) —
> adım sırası bu iki resmi kaynağın tarifidir; gözlem senaryosu `-dogrulama` §A.3 D-5.

### A2 — Symlink kaçışı denetimi zinciri (repo sargısı)

| # | Adım | Repo satırı | Ne reddeder |
|---|---|---|---|
| 1 | Boş/NUL denetimi | `MusicStreamHandler.php:175-178` | NUL bayt yolu kesme |
| 2 | `..` bileşeni reddi | `:180-183` | Üst dizine kaçış |
| 3 | Sembolik link reddi | `:185-187` (`is_link`) | Link üzerinden hedef değiştirme |
| 4 | Gerçek yol çözümü | `:189-192` (`realpath` + `is_file`) | Var olmayan / çözülmeyen yol |
| 5 | Kök çözümü | `:194-197` | Kök çözülemiyorsa 404 |
| 6 | Ön ek karşılaştırması | `:199-202` (`str_starts_with`) | Kök dışına taşma |
| 7 | Okunabilirlik | `:204-206` (`is_readable`) | Mod reddi |
| 8 | Dönüş | `:208` | Güvenli gerçek yol |

> Kavram karşılığı: M5 · `realpath` POSIX tanımı `https://www.opengroup.org/apex/lv4/xsh/realpath.htm`
> (§E.3 havuzu · 2026-10-13 HTTP 200). Adım 2 + 3 birlikte `O_NOFOLLOW` + `..`
> reddinin uygulama düzeyindeki karşılığıdır; TOCTOU yorumu `-security` ve `-dogrulama` §A.4 İ-4.

### A2.1 — Gerçek yolda ön ek karşılaştırması (adım adım)

| # | Girdi | İşlem | Başarısızlıkta |
|---|---|---|---|
| 1 | `realpath($kullaniciYolu)` | kanonik gerçek yol | `false` → 404 (repo `:189-192`) |
| 2 | `realpath($kir kok)` | kanonik kök | `false` → 404 (repo `:194-197`) |
| 3 | `rtrim($kok, '\/') + DIRECTORY_SEPARATOR` | ayırıcı ekle | — (repo `:199`) |
| 4 | `str_starts_with($gercek, $on ek)` | **ön ek** kontrolü | değilse 403 (repo `:200-202`) |
| 5 | Sonuç | yalnız kök altındaki gerçek yollar geçer | — |

| Dikkat | Açıklama |
|---|---|
| Ön ek ≠ dizin kontrolü | `/srv/media` ile `/srv/media-evil` aynı öneke sahiptir; ayırıcı eklemek zorunlu |
| Sıralama | kontrol, `realpath` **sonrasında** yapılabilir; `is_link` kontrolü ondan önce yapılır |
| Kalıcılık | yol çözümleme ile açma arasında zaman geçerse hedef değişebilir (TOCTOU) — `O_NOFOLLOW`/`O_EXCL` bu yüzden `open.2`'dedir |

### A3 — Kilit kazanma zinciri (üç primitive)

| # | Primitive | Kapsam | Bekleyen çağıran | Kaynak |
|---|---|---|---|---|
| 1 | `flock(LOCK_EX)` | tüm dosya | ikinci süreç bekler (`LOCK_EX` bloke) | `flock.2` (§E.3) |
| 2 | `fcntl(F_SETLKW)` | bayt aralığı | `F_SETLKW` bekler; `F_SETLK` hemen `EAGAIN`/`EACCES` | `fcntl.2` (bu tur) |
| 3 | `fcntl(F_GETLK)` | bayt aralığı | çakışan kilit sorgulanır | `fcntl.2` (bu tur) |
| 4 | `LockFile` | bayt aralığı | **bloke olmaz**, başarısızsa hemen FALSE döner | MS `LockFile` (bu tur) |
| 5 | `LockFileEx` (bayraksız) | bayt aralığı | bloke ederek kilit alır | MS `LockFile` metni (bu tur) |
| 6 | Serbest bırakma | `LOCK_UN` / `F_SETLK` (kalkmış kilit) / `UnlockFile` | süreç ölürse OS kilitleri kaldırır | üç kaynak |

> Dürüstlük notu: 4. satır MS `LockFile` sayfasındaki "It does not block" ifadesidir
> (2026-10-13 HTTP 200 ile okundu); 5. satır aynı sayfadaki `LockFileEx` yönlendirmesidir.

### A4 — `umask` → mode → ACL etkileşim zinciri

| # | Adım | Ne olur | Kaynak |
|---|---|---|---|
| 1 | İstenen mode | `open(O_CREAT, mode)` çağrısında mode verilir | `open.2` (bu tur) |
| 2 | Maske uygulanır | Fiili mode = `mode & ~umask` (varsayılan ACL yoksa) | `open.2` (bu tur) |
| 3 | ACL devreye girer | Dizinin varsayılan ACL'si varsa yeni nesne onu miras alır | `acl.5` (bu tur, §E.3 havuzuna eklendi) |
| 4 | Mode↔ACL eşlemesi | sahip↔`ACL_USER_OBJ` · grup↔`ACL_MASK`(varsa) · diğer↔`ACL_OTHER` | `acl.5` (bu tur) |
| 5 | Sonraki erişim denetimi | 5 adımlı ACL algoritması: sahip → `ACL_USER` → grup → `ACL_OTHER` → reddet | `acl.5` (bu tur) |
| 6 | Değiştirme | `chmod` hem mode bitlerini hem karşılık gelen ACL girişlerini değiştirir | `chmod.2` + `acl.5` (§E.3 + bu tur) |

> Repo sargısı: `shared/src/Session/SessionConfig.php:35` `@mkdir($savePath, 0750, true)` —
> 0750 istenen mode'dur, fiili değer maskelenerek oluşur (`-dogrulama` §F.3 F-6).

### A5 — Kalıcılık zinciri (`write` → disk)

| # | Adım | Ne olur | Sonuç |
|---|---|---|---|
| 1 | `write` | Veri çekirdek tamponuna (sayfa önbelleği) yazılır | Çağrı döner ≠ diskte |
| 2 | Arka plan yazımı | Çekirdek zamanı gelince bloklara iter | gözlemlenemez |
| 3 | `fdatasync` | Veri + okuma için gereken meta diske itilir | boyut/veri kalıcı |
| 4 | `fsync` | Veri + **tüm** meta (zaman damgası dahil) diske itilir | tam kalıcılık |
| 5 | `sync` | Sistem geneli yazma tamponları itilir | süreç/kapsam dışı |
| 6 | Kesme (`kill -9` / enerji) | 3-4 olmadan veri kaybı **mümkün** | `-dogrulama` §A.3 D-1 |

> Kaynak: `fdatasync.2` · `sync.2` (§E.3 havuzu · 2026-10-13 HTTP 200). Ölçüm senaryosu
> D-1'dir; eşik uydurulmaz, gözlem PLANNED olarak kalır.

### A6 — Bağlantı sayısı zinciri (`link` / `unlink`)

| # | Adım | Ne olur | Kaynak |
|---|---|---|---|
| 1 | Dosya oluşturma | link sayısı 1 | `inode.7` (§E.3) |
| 2 | `link()` | aynı dosya sisteminde yeni ad → link sayısı +1 | `link.2` (§E.3) |
| 3 | Symlink mi? | `link()` **sembolik link oluşturmaz**; onun ayrı primitive'i vardır | `symlink.7` (§E.3) |
| 4 | `unlink()` | link sayısı −1 | `unlink.2` (§E.3) |
| 5 | Sayı 0 | inode + veri serbest kalır; açık tanımlar tutuyorsa içerik son kapanışta silinir | `unlink.2` (§E.3) |
| 6 | Silinen ama açık dosya | süreç yazmaya devam edebilir — ad gizlenmiştir, veri yok olmamıştır | `unlink.2` (§E.3) |

## §4 Ölçüm / eşik — bu dalın ölçüm sözleşmesi

| # | Ne ölçülür | Araç / yöntem | Eşik | Durum |
|---|---|---|---|---|
| 1 | Yol çözümleme başarısızlığı | uygulama log'unda 403/404 sayımı | 0 (kaçış girişimi) | PLANNED |
| 2 | Açık dosya tanıtıcısı sayısı | `/proc/<pid>/fd` (Linux) | `RLIMIT_NOFILE` altında | UNKNOWN (Q-F4) |
| 3 | Kilit çakışması | `flock`/`fcntl` hata kodu (`EAGAIN`/`EACCES`) | 0 beklenmeyen | PLANNED |
| 4 | Kalıcılık (fsync) | `write` + `kill -9` senaryosu | veri kaybı gözlenebilir | ` -dogrulama` §A.3 D-1 |
| 5 | Atomik yeniden adlandırma | `rename` iki süreç | eski ya da yeni görünür | §A.3 D-2/D-6 |
| 6 | Repo kilit yüzeyi | `grep 'LOCK_EX\|flock(\|lockf(' *.php` | — | **6 isabet, 2026-10-13** (`-ornekler` §2) |
| 7 | Yol kaçış reddi (repo) | `MusicStreamHandler.php:180-202` okuması | her `..`/symlink → 403 | OKU (2026-10-13 Read) |
| 8 | Aşırı geniş dizin izni | repo `@mkdir` çağrıları | `0777` → 0 hedef | tespit: `SessionInitializer.php:31` (`-ornekler` §4) |
| 9 | Yol çözme başarısızlığı | `realpath() === false` dalları | 0 beklenmeyen | `realpath(` 10 isabet (2026-10-13) |

> Ölçüm kuralı: eşik değeri uydurulmaz; değer üretilemiyorsa hücre `UNKNOWN` kalır
> (kapsam §F · `isletim-sistemi-core-dogrulama.md` §C.2).

## §5 Komşu sınırı

| Konu | Gittiği dal | Bu daldaki karşılığı |
|---|---|---|
| Dosya tanıtıcısı syscall yüzeyi (`dup` · `fcntl` · `lseek`) | `isletim-sistemi-api` | fd **kavramı** kalır, çağrı gitmez (kapsam §E.1 O1/O2) |
| `open`/`read`/`write` imzaları ve bayrak listesi | `isletim-sistemi-api` | bayrak **anlamı** kalır, çağrı gitmez |
| Kilit ve erişim denetiminin **uygulanması** (politika, ACL dağıtımı) | `isletim-sistemi-security` | kilit **tanımı** kalır, politika gitmez (kapsam §E.1 O3) |
| VFS/inode/dizin iç veri yapısı, journal, cache | `isletim-sistemi-kernel` | katman **tanımı** kalır, kod gitmez |
| Disk kuyruğu / IO scheduler | `isletim-sistemi-kernel` | — |
| Disk donanım ayrıntısı (blok boyutu, sektör) | `K000 → K001-donanım` | yalnız kalıcılık kavramı kalır |
| Dosya sistemi türü karşılaştırması (ext4 vs NTFS) | `isletim-sistemi-turu` | örnek düzeyinde |
| `fsync` davranışı için test stratejisi | `isletim-sistemi-testing` | senaryo doküman düzeyinde |
| Log **okuma**/toplama araçları | `isletim-sistemi-observability` | log **dosyası** kavramı kalır |
| `mmap` dosya → sayfa hatası | `bellek-kavramlari` + `-kernel` | yalnız çapraz referans (kapsam §A.9) |

## §6 Terim sözlüğü

| Terim | Tanım |
|---|---|
| **VFS** | Uygulama ile gerçek dosya sistemi arasındaki soyutlama katmanı |
| **inode** | Dosyanın kendisi (metaveri + veri göstergeleri); adı değil |
| **Dizin** | Ad → inode eşlemesi tutan özel dosya |
| **fd (dosya tanıtıcısı)** | Açık dosyanın süreç içi indeksi |
| **Açık dosya tanımı** | Ofset + durum bayraklarını taşıyan sistem geneli kayıt |
| **Ofset** | Bir sonraki okuma/yazma bayt konumu |
| **Yol çözümleme** | Yol dizesinin dosyaya çevrilme zinciri |
| **Symlink** | İçeriği başka bir yol olan dosya |
| **Kanonik yol** | `.`/`..`/symlink çözülmüş, tek biçimli yol (`realpath`) |
| **Ön ek kaçışı** | Hedefin izinli kök dışına taşması |
| **`umask`** | Yeni nesnelerin mode'undan çıkarılan bit maskesi |
| **Rehber kilit (`flock`)** | Dosya/tanım genelinde paylaşılan-özel kilit |
| **Kayıt kilidi (POSIX)** | Bayt aralığına kilit (`fcntl`) |
| **Kalıcılık (`fsync`/`fdatasync`)** | Veri ve gerekli meta'nın diske itilmesi |
| **Atomik `rename`** | Aynı mount içinde eski↔yeni geçişinin gözlemlenemez olması |
| **Çalışma dizini** | Göreli yolların başlangıç noktası; `chdir` ile değişir (`chdir.2`, §E.3) |
| **Bağlama noktası** | Ağaçta dosya sisteminin oturduğu nokta (M9) |
| **`O_APPEND`** | Yazma ofsetini sonda konumlar; konum güncelleme + yazma tek atomik adım |
| **`readlink`** | Sembolik linkin hedef yolunu **okuyan** primitive (takip etmeden) |
| **Link sayısı** | Bir inode'a giren **ad** sayısı (M8) |
| **`errno` ailesi** | `ENOENT` · `ENOTDIR` · `EACCES` · `ELOOP` · `EAGAIN` — çözümleme/kilit dili |
| **Rehber (advisory) kilit** | Yalnız uygulamalar kilit kurallarına uyarsa çalışan kilit; zorlayıcı (mandatory) değildir |

## §7 Kapsam dışı — bu dosyaya yazılmaz

| # | Konu | Nereye |
|---|---|---|
| 1 | `open`/`read`/`write`/`dup` syscall imzası listesi | `isletim-sistemi-api` |
| 2 | VFS/inode çekirdek kodu, dcache, journal | `isletim-sistemi-kernel` |
| 3 | ACL/izin denetiminin **uygulanması** ve politika yazımı | `isletim-sistemi-security` |
| 4 | Disk/IO scheduler/blk-mq | `isletim-sistemi-kernel` + `K001-donanım` |
| 5 | Dosya sistemi türü karşılaştırma matrisi | `isletim-sistemi-turu` |
| 6 | `fsync`/kilit için PHPUnit/CI stratejisi | `isletim-sistemi-testing` |
| 7 | Log **toplama** araçları (`/proc` okuyucular) | `isletim-sistemi-observability` |
| 8 | Repo somut örnekleri ve anti-pattern listesi | bu dalın `-ornekler.md` dosyası |

## §8 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| Q-F1 | Kademe etiketi: bu dosya Kademe-5 (bellek şablonu) mi, kök `index.md` §2'nin dediği Kademe-4 mü? | ⚠️ VERIFICATION REQUIRED |
| Q-F2 | PHP `LOCK_EX` → üretimde hangi primitive? (`flock`/`fcntl`/platform bağımlı) | ⚠️ VERIFICATION REQUIRED (php.net izinli kaynak listesinde değil) |
| Q-F3 | Hedef üretim dosya sistemi türü (ext4 / NTFS / NFS) — kilit ve `rename` davranışı buna bağlı | UNKNOWN |
| Q-F4 | Üretimde açık dosya tanıtıcısı tavanı (`RLIMIT_NOFILE`) | UNKNOWN — ölçüm PLANNED |
| Q-F5 | `kapsam.md` §B.3'teki dosya adları (`vfss-ve-dosya-taniticisi.md`, `dizin-ve-yol-cozumleme.md`) bu turda `-surucu`/`-ornekler` adlandırmasıyla yazıldı → hangisi geçerli? | ⚠️ VERIFICATION REQUIRED (disk/şablon çelişkisi) |

## §9 Kaynakça — 16 URL, HTTP 200, 2026-10-13

| # | Kaynak | URL | Durum |
|---|---|---|---|
| 1 | `path_resolution(7)` — yol çözümleme zinciri | https://man7.org/linux/man-pages/man7/path_resolution.7.html | 2026-10-13 HTTP 200 · `-dogrulama` §E.3 (yeniden) |
| 2 | `inode(7)` — dosya sistemi inode | https://man7.org/linux/man-pages/man7/inode.7.html | 2026-10-13 HTTP 200 · §E.3 (yeniden) |
| 3 | `symlink(7)` — sembolik bağlantı | https://man7.org/linux/man-pages/man7/symlink.7.html | 2026-10-13 HTTP 200 · §E.3 (yeniden) |
| 4 | `hier(7)` — standart dizin hiyerarşisi | https://man7.org/linux/man-pages/man7/hier.7.html | 2026-10-13 HTTP 200 · §E.3 (yeniden) |
| 5 | `open(2)` — fd üretimi, `O_CREAT`/`O_EXCL`/`O_NOFOLLOW`, mode & ~umask | https://man7.org/linux/man-pages/man2/open.2.html | 2026-10-13 HTTP 200 · **bu tur envantere eklendi** |
| 6 | `fcntl(2)` — advisory record locking (`F_SETLK`/`F_SETLKW`/`F_GETLK`) | https://man7.org/linux/man-pages/man2/fcntl.2.html | 2026-10-13 HTTP 200 · **bu tur envantere eklendi** |
| 7 | `flock(2)` — dosya kilidi | https://man7.org/linux/man-pages/man2/flock.2.html | 2026-10-13 HTTP 200 · §E.3 (yeniden) |
| 8 | Çekirdek VFS dokümanı | https://docs.kernel.org/filesystems/vfs.html | 2026-10-13 HTTP 200 · §E.3 (yeniden) |
| 9 | Çekirdek path-lookup tasarımı | https://docs.kernel.org/filesystems/path-lookup.html | 2026-10-13 HTTP 200 · §E.3 (yeniden) |
| 10 | POSIX `realpath` — kanonik yol | https://www.opengroup.org/apex/lv4/xsh/realpath.htm | 2026-10-13 HTTP 200 · §E.3 (yeniden) |
| 11 | `rename(2)` — atomik taşıma (aynı mount) | https://man7.org/linux/man-pages/man2/rename.2.html | 2026-10-13 HTTP 200 · §E.3 (yeniden) |
| 12 | `unlink(2)` — link sayacı azaltma | https://man7.org/linux/man-pages/man2/unlink.2.html | 2026-10-13 HTTP 200 · §E.3 (yeniden) |
| 13 | `link(2)` — sert bağ oluşturma | https://man7.org/linux/man-pages/man2/link.2.html | 2026-10-13 HTTP 200 · §E.3 (yeniden) |
| 14 | `readlink(2)` — symlink hedefini okuma | https://man7.org/linux/man-pages/man2/readlink.2.html | 2026-10-13 HTTP 200 · §E.3 (yeniden) |
| 15 | `fdatasync(2)` / `sync(2)` — kalıcılık | https://man7.org/linux/man-pages/man2/fdatasync.2.html | 2026-10-13 HTTP 200 · §E.3 (yeniden) |
| 16 | `mount(2)` — dosya sistemi bağlama | https://man7.org/linux/man-pages/man2/mount.2.html | 2026-10-13 HTTP 200 · §E.3 (yeniden) |

> Başarısız denemeler (dürüstlük kaydı): `https://man7.org/linux/man-pages/man7/acl.7.html`
> (404 · önceki oturum) ve `https://docs.kernel.org/filesystems/posix-acls.html`
> (404 · bu oturum) — ACL kaynağı yerine **`acl.5`** kullanıldı (`-dogrulama` §A.4 İ-6
> ile uyumlu); bu iki URL hiçbir satırda kullanılmaz.

## §10 Ölçüm teyidi — dosya erişim yüzeyi sayımları (2026-10-13)

> §3'teki mekanizma zincirinin (A1-A6) **repo-ölçeği sayımsal teyididir**.
> Tüm sayımlar repo kökünden salt-okunur komutlarla alındı; mevcut §1-§9'a
> dokunulmaz. Tam kanıt matrisleri kardeş dosyalardadır: uygulama/sunucu
> örnekleri `izin-...-ornekler` §3-§6, akışlar `-surucu` §8.

### §10.1 Sunucu yüzeyi — dosya erişim kararı veren yapılandırma

| Ölçüm | Değer | Anlamı (§3 ile ilişki) |
|---|---|---|
| `.htaccess` dosyası | 4 (api · auth · assets · home) | A5 sunucu katmanı 4 alt alanda da var |
| `web.config` dosyası | 4 (aynı alt alanlar) | IIS karşılığı mevcut |
| `Options -Indexes` | 4/4 | dizin listeleme kapatma tutarlı |
| `Require all denied` (kod) | 6 isabet · 3 dosya (assets 0) | A5 reddi dosya-erişim düzeyinde 3 alt alanla sınırlı |
| `CustomResponse 403` (web.config) | 6 isabet · 3 dosya | IIS karşılığı aynı 3 alt alan |
| `directoryBrowse enabled="false"` | 1/4 (yalnız assets:35) | IIS'te dizin listeleme engeli **istisnai** |
| `FilesMatch`/`<location>` (web.config) | 0 | IIS'te PHP doğrudan erişim engeli yok (UNKNOWN, Q-OR3) |
| `Permissions-Policy` header | web.config 3/4 · `.htaccess` 0 | iki sunucu dili arasında asimetri |

### §10.2 Uygulama yüzeyi — §2 kavramlarının repo karşılığı

| Kavram (§2) | Repo karşılığı | Ölçüm |
|---|---|---|
| kanonik yol | `realpath(` kullanımı | 10 isabet (reuse: `izin-...-surucu` §5, grep 2026-10-13) |
| symlink kaçınma | `is_link(` | 1 isabet — `MusicStreamHandler.php:185` (adım 3) |
| dosya okunabilirliği | `is_readable(` | 3 isabet — `MusicStreamHandler.php:204` · `audit.php:395` · `scan.php:151` |
| dizin varlığı | `is_dir(` | 13 isabet (hazırlama akışları, `-ornekler` §6.1) |
| yazma kontrolü | `is_writable(` | **0** — negatif kanıt |
| kilitleme (§3 kilit kasası) | `LOCK_EX` | 1 isabet — `TemplateEngine.php:74` (cache yazımı) |
| POSIX primitive (chmod/setuid/...) | — | **0** — izin kasası devrede değil (`izin-...-surucu` §5) |

Çıkarım: §2'nin "izin veren/check eden" soyut katmanı repoda **tek bir yerde**
fizikselleşiyor (adım 7 `is_readable`); geri kalan her şey ya yol çözümleme ya
da sunucu yapılandırmasıdır. Bu, §4'ün "ölçüm sözleşmesi" ile uyumludur:
uygulama katmanı izin **vermez**, erişim **kararı** verir.

### §10.3 Ek kaynakça — bu bölümün eklediği 3 URL

| # | Kaynak | URL | Durum |
|---|---|---|---|
| 1 | `stat(2)` — `EACCES`/`ENOENT`/`ELOOP` semantikleri (§3.5 errno tablosu) | https://man7.org/linux/man-pages/man2/stat.2.html | bu dosyaya **yeni** · envanter reuse (`isletim-sistemi-api-dogrulama.md:201`) · 2026-10-13 HTTP 200 |
| 2 | `ls(1)` — izin sütunlarının gözlem aracı (§10.2 ölçüm dili) | https://man7.org/linux/man-pages/man1/ls.1.html | bu dosyaya **yeni** · 2026-10-13 HTTP 200 |
| 3 | `.htaccess` kullanımı — sunucu katmanı mekanizmasının kendisi | https://httpd.apache.org/docs/2.4/howto/htaccess.html | bu dosyaya **yeni** · 2026-10-13 HTTP 200 |

### §10.4 Kapanış — handover ve ölçüm disiplini

| Öğe | Durum |
|---|---|
| Bu bölümün iddiaları | tamamı dosya:satır veya tarihli grep sayımı — §10.1-§10.2 |
| Komşu devir | tam asimetri matrisi → `izin-ve-guvenlik-kavramlari-ornekler` §5 · akışlar → `-surucu` §8 · senaryolar → `-ornekler` §13 |
| Çalışan sistem ölçümü | **YOK** (depо dışı; `-dogrulama` dalı, PLANNED) |
| Bu bölümde `⚠️` | 1 (§10.1 "IIS PHP engeli UNKNOWN" hücresi) · Q-I1/Q-I2 §8'de açık kalır |

### §10.5 Ölçüm — komşu kasalarla kesişen sayımlar (paylaşılan envanter)

| Ölçüm | Değer | Sahip dosya (kanıt orada) | Bu dosyadaki anlamı |
|---|---|---|---|
| `realpath(` kullanımı | 10 isabet | `izin-...-surucu` §5 | A2'deki kanonik yol adımı tek fonksiyonda yoğunlaşıyor |
| `is_link(` | 1 (`MusicStreamHandler.php:185`) | `-ornekler` §6.1 | symlink reddi tek noktada |
| `is_readable(` | 3 | `-ornekler` §6.1 | izin okuma nadir (web akışında 1) |
| `mkdir` mod dağılımı | 0775×5 · 0750×2 · 0777×2 · 0700×1 | `izin-...-surucu` §5 | A5 öncesi hazırlama adımının gerçek yüzeyi |
| `LOCK_EX` | 1 (`TemplateEngine.php:74`) | bu dosya §10.2 | §3'teki kilit kavramının tek uygulaması |
| POSIX primitive | 0 | `izin-...-surucu` §5 | A1/A4/A6 katmanları kodda değil, modeldedir |

Kapanış notu: bu tablo **yeniden sayım yapmaz** — komşu kasaların 2026-10-13
grep sonuçlarını bu dosyanın §2-§4 kavramlarına eşler. Sayım tekrarı
gerektiğinde komşu dosya yeniden ölçülür (çifte ölçüm kaynak doğmasını
engeller: tek sayı, tek sahip).

## §11. EK — Ölçüm teyidi (2026-10-13)

- Dolu satır ölçüm komutu (salt-okunur): `Get-Content -Encoding UTF8 <dosya> | Where-Object { $_ -ne '' }).Count`.
- EK öncesi bu dosya: 452 toplam / 374 dolu satır; EK sonrası ≥380 hedefi.
- 4 `.htaccess` satır ölçümü: api 42 · auth 37 · home 37 · assets 47; `Options -Indexes` 4/4.
- 4 `web.config` satır ölçümü: api 41 · assets 37 · auth 43 · home 43.
- `Require all denied` kod = 6 isabet (api:20,25 · auth:19,23 · home:19,23); `directoryBrowse` 1/4 (assets:35).
- PHP fonksiyon envanteri: `is_dir`+`is_readable`+`is_writable` = 17 isabet (`is_writable` 0 · `is_readable` 3).
- Bu dosyaya eklenen doğrulanmış URL: 3 (§10.3); teyit tarihi 2026-10-13 (HTTP 200) veya önceden teyitli reuse.
- Komşu sayaç sahipliği korunur: bu dosya §2-§4'te sayım tekrarı yapmaz (tek sayım, tek sahip).
- `git commit` bu oturumda ATILMADI — değişiklikler working tree'dedir (commit orkestratöre ait).

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
