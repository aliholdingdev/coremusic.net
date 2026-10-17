---
node_id: K000-isletim-sistemi/isletim-sistemi-security/uygulama-guvenlik-siniri/uygulama-guvenlik-siniri-linux
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-security/uygulama-guvenlik-siniri
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-security/uygulama-guvenlik-siniri/uygulama-guvenlik-siniri-anayasa
  - K000-isletim-sistemi/isletim-sistemi-security/uygulama-guvenlik-siniri/uygulama-guvenlik-siniri-surucu
evidence:
  - https://www.kernel.org/doc/html/latest/userspace-api/seccomp_filter.html
  - https://github.com/seccomp/libseccomp
  - https://github.com/containers/bubblewrap
  - https://man7.org/linux/man-pages/man2/chroot.2.html
  - https://man7.org/linux/man-pages/man2/pivot_root.2.html
  - home.coremusic.net/include/Stream/MusicStreamHandler.php:189
  - home.coremusic.net/include/Stream/MusicStreamHandler.php:200
  - shared/src/AI/AIEngine.php:292
  - shared/src/Middleware/CsrfMiddleware.php:42
validation:
  - "2026-10-14: URL'ler erisim ile dogrulandi; kod ornekleri kaynak: upstream docs imzali; repo OS araci yokligi 2026-10-14 taramasinda 0 isabet."
open_questions:
  - "S1: OS guvenlik katmani hedef mimaride devreye alinacak mi? [UNKNOWN]"
  - "S2: Uygulama <-> OS guvenlik sorumluluk siniri? [UNKNOWN]"
group: kod
---

# Uygulama Güvenlik Sınırı — Linux OS Kartı

## §0 ELI15

Bu kart, uygulamanın **OS ile sınırına** koyacağı mekanizmaları taşır:
seccomp-BPF syscall filtresi, bubblewrap namespace sandbox'ı,
`chroot`/`pivot_root` kök değişimi ve `no_new_privs` yetki kilidi. Hepsi
"uygulama ne kadar syscall yüzeyi görür, ne kadar kök dosya ağacına erişir"
sorusunu yanıtlar.

> **Öncelikli gerçek:** Repoda `seccomp`/`bwrap`/`chroot`/`pivot_root`/
> `no_new_privs` çağrısına dair **0 isabet** vardır (293 dosya,
> `vendor`/`node_modules` hariç, 2026-10-14 — negatif kanıt). Sınır şu an
> **yalnızca uygulama katmanında** duruyor: `realpath` + `str_starts_with`
> (path daraltma), `escapeshellarg` (shell argüman kaçışı), `hash_equals`
> (timing-safe token) — bunlar OS sandbox'ı **değildir** (§3).

`chroot`'un upstream man sayfasının bizzat kendisini "güvenlik aracı değil"
diye uyardığını unutma (§1): bu kart `chroot`'u tarihsel/kırılgan sınır olarak,
`pivot_root` + namespace + seccomp kombinasyonunu ise ciddi sınır adayı olarak
taşıtır. S1/S2 açık kalır (§6).

## §1 Linux güvenlik profili

| Mekanizma | Ne yapar | Bu alt-dal açısı | Güven düzeyi |
|---|---|---|---|
| seccomp-BPF | Gelen syscall'ların BPF programıyla filtresi; syscall numarası + argümanları üzerinde çalışır; BPF işaretçi çözemediğinden TOCTOU saldırıları imkânsızlaşır | syscall yüzeyi daraltma | Yüksek — seccomp_filter 2026-10-14 okundu |
| seccomp sınırı (ne DEĞİL) | Upstream: *"System call filtering isn't a sandbox"* — yalnızca yüzey azaltma aracıdır; politika/bilgi akışı başka tekniklerle yönetilir | yanlış beklenti uyarısı | Yüksek — seccomp_filter 2026-10-14 okundu |
| `no_new_privs` önkoşulu | Filtre takmadan önce `prctl(PR_SET_NO_NEW_PRIVS, 1)` **veya** namespace içinde `CAP_SYS_ADMIN` yoksa `-EACCES`; bu, filtrelenen alt süreçlerin daha fazla yetkiyle doğmasını engeller | yetki yükseltme kilidi | Yüksek — seccomp_filter 2026-10-14 okundu |
| Filtre mirası & öncelik | `fork`/`clone`+`execve` izinliyse child aynı filtreye tabi; çoklu filtrede en yüksek öncelikli değer kazanır (`SECCOMP_RET_KILL_PROCESS` en üstte) | kalıtım | Yüksek — seccomp_filter 2026-10-14 okundu |
| libseccomp | Linux syscall filtreleme mekanizması için platform-bağımsız arayüz; BPF dilini soyutlayıp fonksiyon çağrı arayüzü sunar | uygulama kütüphanesi (opsiyonel) | Yüksek — GitHub README 2026-10-14 okundu |
| bubblewrap (`bwrap`) | Yetkisiz düzeyde sandbox kurma aracı (Flatpak benzeri projeler kullanır); user namespace ile kurulur; yeni mount namespace + tmpfs root; `PR_SET_NO_NEW_PRIVS` ile setuid binary'leri kapatır | namespace sandbox | Yüksek — GitHub README 2026-10-14 okundu |
| bwrap güvenlik modeli | README: *"not a complete, ready-made sandbox"* — koruma düzeyi **tamamen argümanlara bağlıdır**; sınırlar (`Limitations`): `TIOCSTI` için seccomp ya da `--new-session` (README CVE-2017-5226 referansı), sandbox'a bağlanan her şey ayrıcalık yükseltme potansiyeli taşır | sorumluluk: politikayı kuran uygulama | Yüksek — GitHub README 2026-10-14 okundu |
| bwrap namespace envanteri | `CLONE_NEWUSER` (yalnız aktif uid/gid), `CLONE_NEWIPC`, `CLONE_NEWPID`, `CLONE_NEWNET` (yalnız loopback), `CLONE_NEWUTS`; seccomp filtreleri dışarıdan geçirilebilir | izolasyon birimleri | Yüksek — GitHub README 2026-10-14 okundu |
| `chroot` | Yalnız yol çözümünün kökünü değiştirir; `CAP_SYS_CHROOT` gerekir; man bizzat uyarır: *"not intended to be used for any kind of security purpose"*; açık fd'leri kapatmaz; superuser `cd ..` ile çıkabilir | **zayıf/tarihsel sınır** | Yüksek — chroot.2 2026-10-14 okundu (uyarı kaynağıyla) |
| `pivot_root` | Mount namespace içinde root mount'u değiştirir; `CAP_SYS_ADMIN` (mount namespace'in sahibi user namespace'de); kısıtlar: `put_old` `new_root` altında, `new_root` mount noktası, `MS_SHARED` yayılım yasak; sonrası `chdir("/")` | mount-seviyesi kök değişimi | Yüksek — pivot_root.2 2026-10-14 okundu |

## §2 Kod örnekleri

> **UYARI (zorunlu):** 3 bloğun hiçbiri bu repodan alınmamıştır; tamamı
> `kaynak: upstream docs` imzalı referans kalıplarıdır. 2. blok upstream
> README'deki örnek **birebir**dir; 1. ve 3. bloklar çağrı sözleşmesini
> belge satırından aktarır (çalıştırılabilir komut **değildir**).

### §2.1 — seccomp filtre takma sözleşmesi (referans)

```sh
# kaynak: upstream docs - userspace-api/seccomp_filter.html
# cagri: prctl(PR_SET_SECCOMP, SECCOMP_MODE_FILTER, prog)
# onkosul: prctl(PR_SET_NO_NEW_PRIVS, 1) VEYA namespace'de CAP_SYS_ADMIN (yoksa -EACCES)
# uyari (docs): "System call filtering isn't a sandbox"
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel — çalıştırılabilir değil) | YOK — `seccomp` grep'i 0 isabet (2026-10-14) |

### §2.2 — bubblewrap sandbox kalıbı (referans — upstream README örneği)

```sh
# kaynak: upstream docs - github.com/containers/bubblewrap README (Usage)
bwrap \
    --ro-bind /usr /usr \
    --symlink usr/lib64 /lib64 \
    --proc /proc \
    --dev /dev \
    --unshare-pid \
    --new-session \
    bash
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans) | YOK — `bwrap` grep'i 0 isabet (2026-10-14) |

### §2.3 — pivot_root kök değişim kalıbı (referans)

```sh
# kaynak: upstream docs - man2/pivot_root.2.html
# syscall(SYS_pivot_root, new_root, put_old) - glibc wrapper yoktur, syscall(2) gerekir
# CAP_SYS_ADMIN gerekir; put_old new_root'un altinda olmali; new_root mount point olmali
# sonrasinda chdir("/") gerekir (docs NOTES)
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel — çalıştırılabilir değil) | YOK — `pivot_root`/`chroot` grep'i 0 isabet (2026-10-14) |

## §3 CoreMusic repo durumu

| Yüzey | Kanıt yöntemi | Sonuç (2026-10-14) |
|---|---|---|
| `seccomp` / BPF programı / `seccomp_rule` | grep 14 deseni, PHP/JS+sh+conf, `vendor`/`node_modules` hariç | **YOK** — 293 dosya / 0 isabet (negatif kanıt) |
| `bwrap`/`bubblewrap` sandbox scripti | grep | **YOK** (negatif kanıt) |
| `chroot`/`pivot_root` çağrısı | grep | **YOK** (negatif kanıt) |
| `no_new_privs` bayrağı | grep | **YOK** (negatif kanıt) |
| path daraltma — `MusicStreamHandler.php:189` | idx 188 birebir | `$real = realpath($trimmed);` — uygulama katmanı; **OS sandbox değildir** |
| sınır önleme — `MusicStreamHandler.php:200` | idx 199 birebir | `if (!str_starts_with($real, $prefix)) {` — uygulama katmanı prefix denetimi |
| shell kaçışı — `AIEngine.php:292` | idx 291 birebir | `escapeshellarg($filePath)` — uygulama katmanı; shell yüzeyini daraltır, syscall yüzeyini **değil** |
| token karşılaştırma — `CsrfMiddleware.php:42` | idx 41 birebir | `!hash_equals($sessionToken, (string)$submitted)` — uygulama katmanı (timing-safe) |
| session dizin izni — `SessionConfig.php:20,35` | idx 19/34 birebir | `0750` — **uygulama katmanının OS'a tek teması** (DAC; bkz. D1/D2 kartları) |

**Okuma:** 9 satırın 4'ü negatif kanıt, 5'i uygulama katmanı kanıtı. Sınır
katmanı şu an **OS'ta değil, PHP'dedir**; seccomp/namespace katmanı repoda
**hiç başlamamıştır** — bu, S1'in dayanağıdır.

## §4 Sınır cümleleri

> **K010 ayrimi (baglayici):** Kimlik dogrulama, oturum, sifre ve MFA -> `K0100-identitiy`; bu kartta yalnizca Linux dosya/surec/erisim kisitlari (or. session dizini 0750) ele alinir.
> **K009 ayrimi (baglayici):** OWASP denetimi, auth/CSRF/CSP tasarimi, kripto secimi (ADR-022) -> `K009-security`; bu kart yalnız OS katmani + sinir isaretlerini tasinir.
> **Komşu dal:** Cekirdek ici yapisi ve surucu yazimi -> `isletim-sistemi-kernel`; Linux platform geneli -> `isletim-sistemi-turu/linux`.

Ek sınır: `realpath`/`str_starts_with`/`escapeshellarg`/`hash_equals` bu kartın
**OS kanıtı değildir** — uygulama katmanı savunmalarıdır; onları "sandbox" diye
adlandırmak abartıdır. Tersi de doğrudur: bu kart PHP auth/CSRF/CSP tasarımını
taşımaz (K009). `chroot` tek başına güvenlik sınırı sayılmaz (upstream man
uyarısı, §1).

## §5 Referans

| URL | İddia | 2026-10-14 durum |
|---|---|---|
| https://www.kernel.org/doc/html/latest/userspace-api/seccomp_filter.html | BPF filtresi mekanizması; TOCTOU imkânsızlığı; "isn't a sandbox"; `PR_SET_NO_NEW_PRIVS` önkoşulu; `-EACCES`; filtre öncelik sırası | HTTP 200 · webfetch ile okundu |
| https://github.com/seccomp/libseccomp | libseccomp = syscall filtreleme arayüzü; BPF dilini soyutlar | HTTP 200 · webfetch ile okundu |
| https://github.com/containers/bubblewrap | yetkisiz sandbox; user namespace; `PR_SET_NO_NEW_PRIVS`; "not a complete, ready-made sandbox"; Usage örneği; namespace envanteri; `TIOCSTI`/`--new-session` (CVE-2017-5226 README referansı) | HTTP 200 · webfetch ile okundu |
| https://man7.org/linux/man-pages/man2/chroot.2.html | `CAP_SYS_CHROOT`; "not intended ... any kind of security purpose"; açık fd'ler; superuser kaçış örneği | HTTP 200 · webfetch ile okundu |
| https://man7.org/linux/man-pages/man2/pivot_root.2.html | `CAP_SYS_ADMIN`; `put_old`/mount-point/`MS_SHARED` kısıtları; `chdir("/")` zorunluluğu; glibc wrapper yok | HTTP 200 · webfetch ile okundu |

## §6 Açık sorular

| # | Soru | Durum |
|---|---|---|
| S1 | OS güvenlik katmanı (seccomp/namespace/no_new_privs) hedef mimaride devreye alıncak mı? | **[UNKNOWN]** — bu kartta KAPATMA YOK |
| S2 | Uygulama ↔ OS güvenlik sorumluluk sınırı (uygulama kontrolleri nerede biter, OS sandbox nerede başlar)? | **[UNKNOWN]** — bu kartta KAPATMA YOK |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
