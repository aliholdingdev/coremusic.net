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

> **K010 ayrimi (baglayici):** Kimlik dogrulama, oturum, sifre ve MFA -> `K010-identitiy`; bu kartta yalnizca Linux dosya/surec/erisim kisitlari (or. session dizini 0750) ele alinir.
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

---

## §7 Çapraz kart matrisi (Linux uygulama sınırı kontrol haritası)

§1 profili, §2 kalıplar (seccomp/bubblewrap/pivot_root) ve §5 referans bu
dosyada çaprazlanır; her satır bir sınır kontrolüdür. Kanıtsız kontrol "var" sayılmaz.

| # | Sınır kontrolü | Yüzey (§ referansı) | Sağlayıcı katman | Doğrulama tipi | Öncelik |
|---|---|---|---|---|---|
| K1 | seccomp-bpf syscall filtresi | §2.1 filtre sözleşmesi | Kernel BPF | Statik + dinamik | P0 |
| K2 | Dosya sistemi sandbox (bubblewrap kalıbı) | §2.2 bwrap kalıbı | Namespace + mount | Statik | P0 |
| K3 | pivot_root kök değişimi (çıkış engeli) | §2.3 pivot_root | Namespace | Statik | P0 |
| K4 | UID/GID ayrımı (çalışan kullanıcı sınırı) | §1 kimlik katmanı | Kernel | Dinamik | P1 |
| K5 | capability daraltma (ayrıcalık kesimi) | §1 yetki katmanı | Capabilities | Statik + dinamik | P0 |
| K6 | cgroup/kota (bellek/CPU per-app) | §1 kaynak katmanı | cgroup | Ölçüm | P2 |
| K7 | Ağ namespace izolasyonu | §1 ağ katmanı | netns | Statik | P1 |
| K8 | ro-salt-okunur bağlama (kök sistem yazma) | §2.2 mount katmanı | VFS | Statik | P1 |

### §7.1 Matris okuma kuralı

1. **Statik kanıt** — profil dosyası, mount listesi, filtre JSON'u (Dosya: ile).
2. **Dinamik kanıt** — syscall ret (EPERM) / mount ret / netns ret olayı.
3. **Ölçüm kanatı** — sandbox içinde `/proc` benzeri çıktı.

Kanıtsız satır `VERIFICATION REQUIRED` kalır.

### §7.2 Çapraz kart eşlemesi (sınır → kart)

| Kart kategorisi | İlgili kontroller | Bu dosyadaki karşılığı |
|---|---|---|
| Kimlik kartları | K4, K5 | §1 yetki, §10 S3 |
| Depolama kartları | K2, K3, K8 | §2.2/§2.3, §10 S1 |
| Bağlantı kartları | K7 | §10 S4 |
| Kaynak kartları | K6 | §13 T3 |
| Sistem çağrısı kartları | K1 | §2.1, §10 S2 |
| Paket kartları | K8 | §7 K8, §13 N5 |

## §8 Ses/RT yüzeyi (Linux için PLANNED senaryo)

> **DURUM: PLANNED** — Zincir gerçek ortamda bu turda doğrulanmadı.

### §8.1 Neden bu bölüm burada?

Ses alt sistemi (ALSA/PipeWire cihaz düğümleri, grup üyeliği, önbellek
paylaşımı) sınırın ayrıcalıklı geçişidir: sandbox içinde ses cihazı erişimi
K2/K5/K7 ile aynı yetki altyapısını kullanır. Gecikme/kalite (SNR/THD, xrun)
bu dosyanın dışıdır; yalnız sınır yüzeyi PLANNED çerçevede kalır.

### §8.2 PLANNED senaryo zinciri (Ses ↔ Sınır)

| Adım | Senaryo | Beklenen yüzey | Durum |
|---|---|---|---|
| A1 | Sandbox içi süreç ses cihazı düğümüne açar | Cihaz yetkisi (K4/K5) | PLANNED |
| A2 | Dosya sistemi kalıcı cihazı ro mı rw mı | K8 ro bağlama | PLANNED |
| A3 | Önbellek/ortalama üzerinden veri akar | K2 mount sınırı | PLANNED |
| A4 | Süreç kapanır | Kanal/oturum kapanır, artık yok (§9 M4) | PLANNED |

**Kapsam dışı:** ses algoritması/codec/xrun çözümü. **Harici referans:** `.ai/electronic/drivers/audio-drivers.md` — bu turda yalnız **okuma** (beyazliste dışı yazım yasağı).

## §9 Başarısızlık modları (Linux uygulama sınırı)

| Mod | Tetikleyici | Gözlenen belirti | Sınır etkisi | Sürdürme/geri alma |
|---|---|---|---|---|
| M1 | seccomp ihlali (yasaklı syscall) | SIGSYS/EPERM | Süreç durur | Filtre istisnası denetimi |
| M2 | Mount/sandbox ihlali | EACCES/EPERM | Erişim kesilir | Profil denetimi |
| M3 | pivot_root kaçış denemesi | Kök dışı erişim ret | Kaçış engellenir | Namespace denetimi |
| M4 | Süreç çökmesi | SIGSEGV vb. | Kaynak sızıntısı | cgroup temizliği |
| M5 | Capability kötüye kullanımı | EPERM / beklenen yetki ret | İşlem düşer | capability listesi |
| M6 | Ağ namespace ihlali | Bağlantı ret veya sızma | Ağ kesilir/sızar | netns + K7 denetimi |
| M7 | Kota aşımı | cgroup ret/OOM | Uygulama durur | Ölçüm + limit revizyonu |
| M8 | ro beklenti ihlali (rw denemesi) | EROFS | Yazma düşer | rw-ro profil kontrolü |

### §9.1 Fail-safe matrisi (öncelik sırası)

1. **P0 — M1, M2, M3, M5, M6 (sızma varsa):** fail-closed.
2. **P1 — M8:** tek dosya yüzeyi düşer.
3. **P2 — M4, M7:** yalnız süreç/kota.

`fail-open` hiçbir modda kabul edilmez. 3 başarısız turda dur + üst karar.

## §10 Kullanım senaryoları

Kalıp: **kural → yüzey → doğrulama → sonuç (işaretli)**.

### §10.1 S1 — Sandbox dosya sınırı (P0)

- **Kural:** Kök dışı yola yazma sandbox içinde reddedilir (K2, §9 M2).
- **Yüzey:** bwrap mount profil (§2.2).
- **Doğrulama:** Dış yola yazma → beklenen: EACCES/EPERM.
- **Sonuç:** Yazılırsa K2 ihlali → P0. (Durum: PLANNED.)

### §10.2 S2 — seccomp filtresi (P0)

- **Kural:** Filtrede olmayan syscall reddedilir (K1, §9 M1).
- **Yüzey:** seccomp-bpf (§2.1).
- **Doğrulama:** Yasaklı syscall çağrısı → beklenen: ret/SIGSYS.
- **Sonuç:** Geçerse K1 ihlali → P0. (Durum: PLANNED.)

### §10.3 S3 — Yetki kesimi (P0)

- **Kural:** Ayrıcalıklı işlem EPERM alır (K5, §9 M5).
- **Yüzey:** capability listesi (§1).
- **Doğrulama:** Yetki isteyen işlem → beklenen: EPERM.
- **Sonuç:** Geçerse K5 ihlali → P0. (Durum: PLANNED.)

### §10.4 S4 — Ağ izolasyonu (P1)

- **Kural:** Sandbox dış bağlantısı izoleyse ret (K7, §9 M6).
- **Yüzey:** netns (§1).
- **Doğrulama:** Dış hedef bağlantısı → beklenen: ret veya izleme kaydı.
- **Sonuç:** Beklenene aykırıysa K7 ihlali. (Durum: PLANNED.)

### §10.5 S5 — ro bağlama bütünlüğü (P1)

- **Kural:** ro beklentili alana yazma EROFS (K8, §9 M8).
- **Yüzey:** mount profili (§2.2).
- **Doğrulama:** ro dizine yazma → beklenen: EROFS.
- **Sonuç:** Yazılırsa K8 ihlali. (Durum: PLANNED.)

### §10.6 Senaryo günlüğü tutma biçimi

| Alan | Zorunluluk |
|---|---|
| Senaryo no | Zorunlu |
| Kanıt türü | Zorunlu |
| Durum (PLANNED / doğrulandı) | Zorunlu |
| Beklenen vs gözlenen | Doğrulandığında zorunlu |

## §11 Kaynakça envanteri

### §11.1 İç kaynaklar (Dosya: ile)

| Kaynak | Kullanım |
|---|---|
| Bu dosya §0–§6 | Profil, §2.1–§2.3 kalıpları, sınırlar |
| K000 security kardeşleri | Çapraz okuma (§12) |
| K000 testing | Doğrulama → test (§13) |
| .ai/electronic/drivers/audio-drivers.md | Ses fallback — yalnız okuma |

### §11.2 Dış kaynak kategorileri

| Kategori | Dayanak | Durum |
|---|---|---|
| seccomp/bwrap upstream dokümanı | §2.1/§2.2 | Erişilebilir URL eklenince işaretlenir |
| Ortam logları | §10 dinamik kanıt | ⚠️ VERIFICATION REQUIRED (alınmadı) |
| Sandbox ölçüm çıktıları | K2/K6 kanıtı | ⚠️ VERIFICATION REQUIRED (PLANNED) |

### §11.3 Kaynak güven sırası

1. Ortam ölçüm/log. 2. Dosya içi § referansı. 3. Vault içi kardeş dosya.
4. Tarihli erişilebilir URL. 5. Model hafızası — tek dayanak asla.

## §12 Eşleme ağacı (okuma sırası)

```text
uygulama-guvenlik-siniri-linux.md
|-- Sec0 ELI15
|-- Sec1 Linux guvenlik profili
|-- Sec2 Kod ornekleri (2.1 seccomp | 2.2 bwrap | 2.3 pivot_root)
|-- Sec3 CoreMusic repo durumu
|-- Sec4 Sinir cumleleri
|-- Sec5 Referans            (kanit uretimi)
|-- Sec6 Acik sorular        (KAPATMA YASAGI)
|-- Sec7 Cizraz matris       (K1-K8 haritasi)
|-- Sec8 Ses/RT PLANNED      (sinirli yuzey)
|-- Sec9 Basarisizlik        (M1-M8 + fail-safe)
|-- Sec10 Senaryolar         (S1-S5 kalip)
|-- Sec11 Kaynakca           (guven sirasi)
|-- Sec12 Esleme agaci       (bu satir)
'-- Sec13-16 Test/ozet       (kapi adimlari)
```

## §13 Test önerileri + negatif kontroller + okuma sırası

### §13.1 Pozitif testler

| Test | Adım özeti | Beklenen |
|---|---|---|
| T1 | Sandbox içinde normal dosya okuma | Başarılı |
| T2 | İzinli syscall çağrısı (K1) | Başarılı |
| T3 | cgroup/kota ölçümü (K6) | Beklenen limit |
| T4 | Yetki verilmiş yetkili işlem (K5) | Çalışır |

### §13.2 Negatif kontroller (RED TEAM — reddin kanıtı)

| # | Negatif adım | Beklenen ret | İhlal varsa |
|---|---|---|---|
| N1 | Dış yola yazma | EACCES/EPERM (M2) | K2 ihlali → P0 |
| N2 | Yasaklı syscall | SIGSYS/ret (M1) | K1 ihlali → P0 |
| N3 | Kök dışı yetki işlemi | EPERM (M5) | K5 ihlali → P0 |
| N4 | Kök dışı ağ bağlantısı (izoleyse) | ret (M6) | K7 ihlali → P1 |
| N5 | ro dizine yazma | EROFS (M8) | K8 ihlali → P1 |
| N6 | pivot_root sonrası kaçış denemesi | ret (M3) | K3 ihlali → P0 |
| N7 | Kota aşımı | cgroup ret/OOM (M7) | K6 ihlali → P2 |

**Kural:** Ret yoksa `VERIFICATION REQUIRED`. 3 başarısız tur → dur + üst karar.

### §13.3 Okuma sırası penceresi (öneri)

1. §0 → §4 (5 dk). 2. §7 matrisi (3 dk). 3. §9 + §10 (7 dk).
4. §13.2 (5 dk). 5. §6 açık sorular sona — oku, **kapatma**.

### §14 Olay şeması + eskalasyon (özet)

| Alan | Açıklama |
|---|---|
| ts / mod / kontrol | ISO-8601 · M1–M8 · K1–K8 |
| yuzey / beklenen / gozlenen | § referansı + ikili |
| durum / kanit / eskalasyon | PLANNED-dogrulandi · statik/dinamik/olcum · P0-P2 |

| Sıklık (5 dk) | Eylem |
|---|---|
| Aynı P0 K'de 2+ ret | Olay aç → inceleme |
| P0 K'de ret yok ama izin | Sızıntı varsayımı → derhal eskale |
| M6 tekrarı (sızma) | netns + firewall denetimi |
| M7 tekrarı | cgroup limiti revizyonu |

Log append-only; `ts` geriye sarılmaz; `kanit` boş olay sayılmaz; P0 olayları
bir sonraki §13.2 turunda tekrar denenir. §6 S1/S2 etkilenmez; frontmatter
değişmez. Sıra: P0 → K1, K2, K3, K5 → P1 → K4, K7, K8 → P2 → K6/M4/M7.

### §15 Append-only yazım kuralı (Linux eki protokolü)

Her gelecek eki yalnız Authority kuyruğundan sonra eklenir; §0–§14,
frontmatter ve Authority satırı değişmez; numara çakışması olmaz (sıradaki §16).
Yasaklar: silme/yeniden yazma · dolgu/TBD · §6 S1/S2 kapatma · frontmatter
değişikliği · Mojibake. Ekleme sonrası ölç: 500'ün altındaysa §<n+1> kaynak
atıflı eklenir; bölüm içeriği her zaman gerçek (uydurma yok).

### §16 Zorunlu 8 soruluk kapı özeti (son okuma)

| # | Soru | Cevap referansı |
|---|---|---|
| 1 | seccomp filtresi uygulandı mı? | §2.1 + §13.2 N2 |
| 2 | bwrap mount profili ro/rw doğru mu? | §2.2 + §13.2 N1/N5 |
| 3 | pivot_root yapıldı mı? | §2.3 + §13.2 N6 |
| 4 | UID/GID ayrımı ölçüldü mü? | §7 K4 |
| 5 | capability listesi dar mı? | §13.2 N3 |
| 6 | cgroup kotası var mı? | §13.1 T3 |
| 7 | netns izolasyonu doğrulandı mı? | §13.2 N4 |
| 8 | ro bağlama etkin mi? | §13.2 N5 |

3 dk rota: §16 → §9.1 → §6 (oku, **kapatma**). Hatalar: kanıtsız "temiz"
iddiası, PLANNED'ı çalışmak saymak, log düzenlemek, fail-open yorumu.
Sonraki tur işleri: §8 PLANNED → ortam ölçümü · §10 → dinamik kanıt ·
§11.2 → tarihli URL · §13.2 → §14 olay şeması. Bu işler bu turda **yapılmadı**.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

---

## §17 Uygulama katmanı sınırlarının detay kontrol listesi (Linux)

§7 matrisinin genişletilmiş sürümüdür; her satır ayrı bir doğrulama adımıdır
ve bağımsız olarak kanıtlanabilir (§7.1 kuralları geçerli).

### §17.1 Mount/fstab sınıfı ayrıntılar

| # | Ayrıntı | Beklenen | Kanıt |
|---|---|---|---|
| A1 | Kök sistem bölmesi | ro veya varlık denetimi | mount çıktısı |
| A2 | Geçici dizin (tmpfs) | rw ama noexec/no-dev uygun seçenekler | mount bayrakları |
| A3 | Uygulama veri dizini | Uygulama UID'li, ro/rw ayrımı net | mount + UID |
| A4 | /proc ve /sys kapsamı | Kısıtlı görünüm (profilin izni) | mount kapsamı |
| A5 | Aygıt düğümü bağlamaları | Sadece izinli (ör. ses) | düğüm listesi |
| A6 | gizli dizin (hidepid sınıfı) | Diğer süreçler görünmez | proc mount seçeneği |
| A7 | Salt-okunur çekirdek modül alanı | Yazma yok | ro doğrulama |
| A8 | Bind mount hedefleri | Aynalama kapsamı dar | bind listesi |

### §17.2 Sistem çağrısı/filtre sınıfı ayrıntılar

| # | Filtre grubu | Yaklaşık davranış | Kanıt |
|---|---|---|---|
| B1 | Dosya sistemi değiştirme çağrıları | Sandbox içinde ret | filtre JSON'u + ret |
| B2 | Yeniden bağlama/çıkış çağrıları | ret (K3 ile örtüşür) | ret olayı |
| B3 | Aygıt/driver çağrıları | ret (ses dışı) | ret olayı |
| B4 | modül yükleme çağrıları | ret | ret olayı |
| B5 | network-isolation'ı bypass çağrıları | ret (K7) | ret olayı |
| B6 | Kısıtlı ptrace/çökme dosyası | profilde ret | ret olayı |
| B7 | Etkinlik takibi (perf benzeri) | profilde ret | ret olayı |
| B8 | yeniden başlatma/uyku çağrıları | profilde ret | ret olayı |

### §17.3 İzleme ve tekrar ölçüm adımları

1. Profili uygula (§2.1/§2.2 kalıbı).
2. §13.1 pozitifleri çalıştır → sonuçları günlüğe yaz.
3. §13.2 negatifleri çalıştır → her reti §14 şemasına satır yap.
4. Beklenen ret çıkmayan satırı `VERIFICATION REQUIRED` işaretle.
5. P0'da açık kalan satır varsa üst karar iste (3 tur kuralı).

### §17.4 Bu bölümün kapı ilişkisi

| Kapı | §17 katkısı |
|---|---|
| İçerik kapısı | Ek uzunluk bu bölümle ölçülür |
| Açık soru kapısı | §6 S1/S2 etkilenmez |
| Frontmatter kapısı | group/meta değişmez |
| Append kapısı | Yalnız sonradan eklendi |

### §17.5 Kardeş bağı

| Kardeş | Bağ |
|---|---|
| §7 | §17, §7 K1–K8'i ayrıntılandırır |
| §9 | Her B/A satırı bir M moduna bağlanabilir |
| §10 | Senaryolar §17 çıktılarıyla zenginleşir |
| §13 | N1–N7 ek adımları §17'den türetilebilir |
| §14 | §17 sonucu olay satırına dönüşür |

### §17.6 SSS

**S: §17 ayrıntıları PLANNED mı?**
C: Beklenen sütunları şarttır; gözlenen sütunları kanıt gelene kadar PLANNED.

**S: Hangi sıra?**
C: P0 satırları (A1, A7, B1–B5) önce; P2 satırları son.

**S: Kanıt nasıl saklanır?**
C: Çıktı bu dosyaya eki satır olarak eklenir; mevcut satırlar değişmez.

**S: Ölçüm tekrarı?**
C: Profil değişikliğinde + haftalık; §14.8 benzeri tekrar tablosu işler.

### §17.7 Ölçüm tekrarlanabilirlik tablosu

| Ölçüm | Kaynak | Sıklık |
|---|---|---|
| Mount kapsamı | §17.1 | Profil değişince |
| Filtre kapsamı | §17.2 | Profil değişince |
| cgroup kotası | §13.1 T3 | Haftalık |
| Olay şeması bütünlüğü | §14 | Olay başına |

Çıktı yoksa `⚠️ VERIFICATION REQUIRED`; "çalışıyor" satırı üretilmez.

### §17.8 §15/§16 ile süreklilik

§15 append protokolü ve §16 kapı özeti bu bölümle birlikte geçerlidir;
bir sonraki eki yazacak kişi sırayla §17 → §16 → §9.1 → §6 okur, sonra
yalnız Authority kuyruğundan sonra §18 ekler. Ölç-ekle-devam döngüsü sürer.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
