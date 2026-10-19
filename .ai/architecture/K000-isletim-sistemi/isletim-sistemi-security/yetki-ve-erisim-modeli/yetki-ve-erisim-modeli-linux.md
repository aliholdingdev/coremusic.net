---
node_id: K000-isletim-sistemi/isletim-sistemi-security/yetki-ve-erisim-modeli/yetki-ve-erisim-modeli-linux
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-security/yetki-ve-erisim-modeli
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-security/yetki-ve-erisim-modeli/yetki-ve-erisim-modeli-anayasa
  - K000-isletim-sistemi/isletim-sistemi-security/yetki-ve-erisim-modeli/yetki-ve-erisim-modeli-surucu
evidence:
  - https://pubs.opengroup.org/onlinepubs/9699919799/functions/chmod.html
  - https://man7.org/linux/man-pages/man2/umask.2.html
  - https://man7.org/linux/man-pages/man2/capget.2.html
  - https://man7.org/linux/man-pages/man1/setfacl.1.html
  - https://man7.org/linux/man-pages/man1/getfacl.1.html
  - https://docs.kernel.org/security/credentials.html
  - https://man7.org/linux/man-pages/man7/attributes.7.html
  - shared/src/Session/SessionConfig.php:20
  - shared/src/Session/SessionConfig.php:35
validation:
  - "2026-10-14: URL'ler erisim ile dogrulandi; kod ornekleri kaynak: upstream docs imzali; repo OS araci yokligi 2026-10-14 taramasinda 0 isabet."
open_questions:
  - "S1: OS guvenlik katmani hedef mimaride devreye alinacak mi? [UNKNOWN]"
  - "S2: Uygulama <-> OS guvenlik sorumluluk siniri? [UNKNOWN]"
group: kod
---

# Yetki ve Erişim Modeli — Linux OS Kartı

## §0 ELI15

Bu kart, Linux'ta **"kim ne yapabilir"** sorusunun mekanizmalarını taşır:
POSIX DAC (rwx/owner), dosya ACL'leri (`setfacl`/`getfacl`), capability
modeli (`capget`), `umask` ve bunların bir syscall çağrısında izlediği
kavramsal denetim akışı (syscall → VFS → DAC → ACL → MAC).

> **Öncelikli gerçek:** Repoda `setfacl`/`getfacl`/`chmod(`/`umask(` çağrısı
> **yoktur** (PHP/JS ağacı, `vendor`/`node_modules` hariç, 293 dosya,
> 2026-10-14 = 0 isabet — negatif kanıt). Uygulamanın OS'a temas eden tek satır
> çifti `SessionConfig.php:20` ve `:35`tir: session dizini `0750` — işte bu bu
> kartın **tek pozitif repo kanıtıdır** ve doğrudan POSIX DAC'dır.

Repoda uygulama katmanı erişim kontrolleri vardır (`realpath` + `str_starts_with`,
`hash_equals`) ama bunlar **OS katmanı değildir** — §3'te sınıfı açıkça
ayrıştırılır. S1/S2 açık kalır (§6).

## §1 Linux güvenlik profili

| Mekanizma | Ne yapar | Bu alt-dal açısı | Güven düzeyi |
|---|---|---|---|
| POSIX DAC (rwx/owner) | `chmod()`: dosyanın izin bitleri + `S_ISUID`/`S_ISGID`/`S_ISVTX`; süreç efektif UID'i dosya sahibiyle eşleşmeli **veya** uygun yetkiye sahip olmalı (POSIX chmod) | temel yetki katmanı | Yüksek — OpenGroup chmod 2026-10-14 okundu |
| Sahiplik (inode UID/GID) | credentials: nesnenin **objektif bağlamı** inode'daki UID/GID; **subjektif bağlam** (FSUID/FSGID + tamamlayıcı gruplar) gerçek UID'den ayrıdır ve dosya erişim hesabında kullanılır | "kimin" hesabı | Yüksek — credentials.html 2026-10-14 okundu |
| `umask` | `mask & 0777`; `open(2)`/`mkdir(2)` yaratım izinlerinden bitleri **kapatır**; tipik varsayılan `022`; `fork` ile miras, `execve` ile değişmez; Linux 4.7+'da `/proc/pid/status` `Umask` alanından değiştirilmeden okunur; üst dizinde **varsayılan ACL varsa umask yok sayılır** | yaratım maskesi | Yüksek — umask.2 2026-10-14 okundu |
| POSIX ACL (`setfacl`/`getfacl`) | 3 base girdi (owner/group/other) + named user/group + **effective rights mask**; default ACL yalnız dizinlerde; dosya sahibi **veya** `CAP_FOWNER` sahibi değiştirebilir; dosya sistemi ACL desteklemiyorsa dosya mod bitlerine düşer | DAC'ın genişletilmesi | Yüksek — setfacl.1/getfacl.1 2026-10-14 okundu |
| Capability modeli (`capget`/`capset`) | Thread capability'lerinin **raw kernel arayüzü**; alanlar `effective` / `permitted` / `inheritable` bitmask; glibc sarmalayıcı **vermez** → `syscall(2)` gerekir; taşınabilir arayüz `cap_set_proc(3)`/`cap_get_proc(3)` | root yetkisinin parçalanması | Yüksek — capget.2 2026-10-14 okundu |
| LSM / MAC | Erişim hesabının son katmanı (kontrol sırası LSM index'te: capability → minor → major) | sınır işareti — detay `cekirdek-guvenlik-linux` kartında | Yüksek (aktarım) |
| syscall → VFS → DAC → ACL → MAC akışı | Kavramsal denetim zinciri: çağrı → yol çözümü → izin bitleri → ACL (varsa) → LSM hook | bu kartın ana şeması | **Orta/Düşük** — evrelerin varlığı ayrı ayrı kaynaklıdır (chmod/umask/getfacl/LSM index); evreler arası **birebir sıralama bu turda doğrudan okunmadı → ⚠️ VERIFICATION REQUIRED** |

> **Dürüstlük notu (önsel kaynak tuzağı):** Havuzdaki `attributes(7)` sayfası
> dosya izinlerini **değil**, POSIX **fonksiyon güvenliği** kavramlarını
> (`MT-Safe`/`MT-Unsafe`) tanımlar — rwx kaynağı olarak **kullanılmamıştır**.
> rwx iddiaları `chmod` (POSIX) ve `umask(2)` kaynaklıdır.

## §2 Kod örnekleri

> **UYARI (zorunlu):** 3 bloğun hiçbiri bu repodan alınmamıştır; tamamı
> `kaynak: upstream docs` imzalı referans kalıplarıdır. 1. blok repodaki tek
> pozitif kanıtla (`0750`) **aynı katmanı** (DAC) gösterir, aynı dosya değildir.

### §2.1 — umask okuma/değiştirme kalıbı (referans)

```sh
# kaynak: upstream docs - man2/umask.2.html
umask 027                     # mask & 0777 - open/mkdir modundan izinleri kapatir
grep Umask /proc/self/status  # Linux 4.7+ ; degistirmeden okuma (docs NOTES)
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans) | YOK — repoda `umask(` çağrısı 0; pozitif kanıt yalnız `SessionConfig.php:20,35` (`0750`, DAC) |

### §2.2 — ACL okuma/yazma kalıbı (referans)

```sh
# kaynak: upstream docs - man1/setfacl.1.html (EXAMPLES) + man1/getfacl.1.html
setfacl -m u:lisa:r file          # docs EXAMPLES birebir: "Granting an additional user read access"
getfacl /var/lib/sessions         # cikti bicimi (docs): user::rwx / group::r-x / mask::r-x / other::---
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans) | YOK — `setfacl`/`getfacl` grep'i 0 isabet (2026-10-14) |

### §2.3 — Capability okuma arayüzü kalıbı (referans)

```sh
# kaynak: upstream docs - man2/capget.2.html
# capget/capset raw kernel arayuzudur - glibc wrapper VERMEZ, syscall(2) ile cagrilir
# taşinabilir arayuz: cap_set_proc(3) / cap_get_proc(3)
# alanlar: effective | permitted | inheritable (bitmask; CAP_* bit index -> OR oncesi shift)
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; çalıştırılabilir değil — syscall sarmalayıcısız) | YOK — `capget`/`setcap` grep'i 0 isabet |

## §3 CoreMusic repo durumu

| Yüzey | Kanıt yöntemi | Sonuç (2026-10-14) |
|---|---|---|
| `setfacl`/`getfacl`/`chmod(`/`umask(`/`chown(` çağrısı | grep, PHP/JS, `vendor`/`node_modules` hariç | **YOK** — 293 dosya / 0 isabet (negatif kanıt) |
| ACL dosyası / `setcap` / capability ayarı | grep | **YOK** (negatif kanıt) |
| session dizin izni sabiti — `SessionConfig.php:20` | idx 19 birebir | `DIR_PERMISSIONS = 0750` — **uygulama katmanının OS'a tek teması** (POSIX DAC: sahibi rwx, grup r-x, diğer `---`) |
| session dizin oluşturma — `SessionConfig.php:35` | idx 34 birebir | `@mkdir($savePath, 0750, true);` — **uygulama katmanının OS'a tek teması** (mkdir izni OS'ta `umask` ile birlikte uygulanır — umask.2) |
| path çözümleme — `MusicStreamHandler.php:189` | idx 188 birebir | `$real = realpath($trimmed);` — **uygulama katmanı**, OS DAC'si değildir |
| sınır önleme — `MusicStreamHandler.php:200` | idx 199 birebir | `if (!str_starts_with($real, $prefix)) {` — **uygulama katmanı** |
| token karşılaştırma — `CsrfMiddleware.php:42` | idx 41 birebir | `!hash_equals($sessionToken, (string)$submitted)` — **uygulama katmanı** (timing-safe) |

**Okuma:** 7 satırın 3'ü negatif kanıt, 2'si tek çiftlik pozitif DAC kanıtı
(`0750`), 2'si uygulama katmanıdır. `0750` dışındaki hiçbir OS yetki mekanizması
(capability/ACL/umask/LSM) bu repoda **başlamamıştır**.

## §4 Sınır cümleleri

> **K010 ayrimi (baglayici):** Kimlik dogrulama, oturum, sifre ve MFA -> `K0100-identitiy`; bu kartta yalnizca Linux dosya/surec/erisim kisitlari (or. session dizini 0750) ele alinir.
> **K009 ayrimi (baglayici):** OWASP denetimi, auth/CSRF/CSP tasarimi, kripto secimi (ADR-022) -> `K009-security`; bu kart yalnız OS katmani + sinir isaretlerini tasinir.
> **Komşu dal:** Cekirdek ici yapisi ve surucu yazimi -> `isletim-sistemi-kernel`; Linux platform geneli -> `isletim-sistemi-turu/linux`.

Ek sınır: `hash_equals`/`realpath`/`str_starts_with` bu kartın kanıtı **değildir**
— onlar uygulama katmanı kontrolleridir; burada yalnızca "OS'a temas etmiyor"
diye sınıfı işaretlenir. LSM/MAC detayı `cekirdek-guvenlik-linux`,
syscall filtresi/sandbox `uygulama-guvenlik-siniri-linux` kartlarındadır.

## §5 Referans

| URL | İddia | 2026-10-14 durum |
|---|---|---|
| https://pubs.opengroup.org/onlinepubs/9699919799/functions/chmod.html | `chmod` izin bitleri + `S_ISUID`/`S_ISGID`; efektif UID sahiplik şartı | HTTP 200 · webfetch ile okundu |
| https://man7.org/linux/man-pages/man2/umask.2.html | `mask & 0777`; `open`/`mkdir` üzerinde etkisi; fork/execve mirası; `/proc/pid/status` `Umask` (4.7+); varsayılan ACL varsa umask yok sayılır | HTTP 200 · webfetch ile okundu |
| https://man7.org/linux/man-pages/man1/setfacl.1.html | ACL girdi sözdizimi (`[default:] [u[ser]:]uid[:perms]`), mask, `CAP_FOWNER`, EXAMPLES komutları | HTTP 200 · webfetch ile okundu |
| https://man7.org/linux/man-pages/man1/getfacl.1.html | getfacl çıktı biçimi; base girdiler; mask satırı; yalnız dizinlerde default ACL | HTTP 200 · webfetch ile okundu |
| https://man7.org/linux/man-pages/man2/capget.2.html | capget/capset raw syscall; glibc wrapper yok; effective/permitted/inheritable bitmask | HTTP 200 · webfetch ile okundu |
| https://docs.kernel.org/security/credentials.html | objektif/subjektif bağlam; FSUID/FSGID gerçek UID'den ayrı; güvenlik hesabı kavramı | HTTP 200 · webfetch ile okundu |
| https://man7.org/linux/man-pages/man7/attributes.7.html | `MT-Safe`/`MT-Unsafe` POSIX **fonksiyon güvenliği** kavramları — dosya izni **DEĞİL** (rwx kaynağı olarak kullanılmadı) | HTTP 200 · webfetch ile okundu |

## §6 Açık sorular

| # | Soru | Durum |
|---|---|---|
| S1 | OS güvenlik katmanı (capability/ACL/umask/LSM) hedef mimaride devreye alıncak mı? | **[UNKNOWN]** — bu kartta KAPATMA YOK |
| S2 | Uygulama ↔ OS güvenlik sorumluluk sınırı (nereye kadar uygulama, nereden itibaren OS)? | **[UNKNOWN]** — bu kartta KAPATMA YOK |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
