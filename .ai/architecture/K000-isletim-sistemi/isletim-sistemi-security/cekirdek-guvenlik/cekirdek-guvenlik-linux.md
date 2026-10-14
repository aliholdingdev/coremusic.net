---
node_id: K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-linux
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-anayasa
  - K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-surucu
evidence:
  - https://docs.kernel.org/security/self-protection.html
  - https://docs.kernel.org/admin-guide/LSM/index.html
  - https://docs.kernel.org/admin-guide/LSM/SELinux.html
  - https://www.kernel.org/doc/html/latest/admin-guide/LSM/Yama.html
  - https://docs.kernel.org/userspace-api/landlock.html
  - https://docs.kernel.org/admin-guide/cgroup-v2.html
  - https://man7.org/linux/man-pages/man5/proc.5.html
  - shared/src/Session/SessionConfig.php:20
  - shared/src/Session/SessionConfig.php:35
validation:
  - "2026-10-14: URL'ler erisim ile dogrulandi; kod ornekleri kaynak: upstream docs imzali; repo OS araci yokligi 2026-10-14 taramasinda 0 isabet."
open_questions:
  - "S1: OS guvenlik katmani hedef mimaride devreye alinacak mi? [UNKNOWN]"
  - "S2: Uygulama <-> OS guvenlik sorumluluk siniri? [UNKNOWN]"
group: kod
---

# Çekirdek Güvenlik — Linux OS Kartı

## §0 ELI15

Bu kart, CoreMusic hedef mimarisinde **Linux çekirdek güvenlik katmanının** ne
olduğunu anlatır: LSM çatısı (SELinux/AppArmor/Yama/Landlock), çekirdeğin
kendini koruması (self-protection), modül imzalama, KASLR/SMEP/SMAP ve
cgroup-v2. Hepsi **işletim sistemi katmanıdır** — PHP uygulamasının OWASP
yüzeyi değil.

> **Öncelikli gerçek:** Bu repoda (2026-10-14, 293 dosya, `vendor`/`node_modules`
> hariç) Linux çekirdek-güvenlik aracına dair **0 isabet** vardır: LSM politika
> dosyası, sysctl hardening, modül imzalama, cgroup/seccomp yapılandırması
> **yoktur** (negatif kanıt). Bu kart dolayısıyla **hedef mimari + negatif
> kanıt** kartıdır; §2'deki 3 bloğun tamamı `kaynak: upstream docs` imzalı
> referans kalıplarıdır, repoda karşılığı yoktur.

Repodaki tek OS-yakın satır çifti `SessionConfig.php:20` ve `:35`
(session dizini `0750`) — o da bir **POSIX DAC** ayarıdır, çekirdek koruması
değildir (§3). S1 ve S2 açık sorudur, bu kartta kapatılmaz (§6).

## §1 Linux güvenlik profili

| Mekanizma | Ne yapar | Bu alt-dal açısı | Güven düzeyi |
|---|---|---|---|
| LSM çatısı (`/sys/kernel/security/lsm`) | Güvenlik kontrollerinin çekirdeğe hook'lanması; build-time `CONFIG_DEFAULT_SECURITY`, boot-time `security=...` ile seçilir; liste virgül ayrımıdır, `capability` hep ilk sırada, sonra minor (ör. Yama), en fazla bir major (ör. SELinux) | çatının kendisi + kontrol sırası | Yüksek — LSM index 2026-10-14 okundu |
| SELinux | Linux çekirdeğine gömülü MAC; mevcut DAC üstünde daha granüler erişim kontrolü (SELinuxUserspace README); minimum destek çekirdek v4.3 | major LSM adayı | Yüksek — GitHub README 2026-10-14 okundu |
| AppArmor | DAC'ı MAC ile tamamlar; süreçleri profil bazında kısırlaştırır (dosya erişimi, capability, ağ soketi, DBus, IPC); mainline'a 2.6.36'tan beri girer; politika çekirdekte uygulanır, sınırlanan uygulama bypass edemez | major LSM adayı | Yüksek — apparmor.net 2026-10-14 okundu |
| Yama (`ptrace_scope`) | Çekirdeğin ele almadığı sistem-geneli DAC korumaları toplar; `ptrace_scope=0` klasik (aynı UID), `1` restricted (ön tanımlı ilişki: yalnız alt süreçler ya da `prctl(PR_SET_PTRACER)`) | `ptrace` yüzeyi | Yüksek — Yama dokümanı 2026-10-14 okundu |
| Landlock | Yığılabilir (stackable) LSM; yetkisiz süreçlerin **kendini** kısıtlamasını sağlar; dosya sistemi + ağ (TCP ABI v4, UDP ABI v10) kuralları; **denied-by-default**; ABI `landlock_create_ruleset(...,VERSION)` ile sorgulanır | kendini koruma / ek sandbox katmanı | Yüksek — Landlock dokümanı 2026-10-14 okundu |
| Kernel self-protection | `CONFIG_STRICT_KERNEL_RWX` / `CONFIG_STRICT_MODULE_RWX` ile kod yazılabilir olmasın; SMEP/SMAP (x86) ve PXN/PAN (ARM) kullanıcı belleğini çekirdekten ayırır; KASLR `CONFIG_RANDOMIZE_BASE`; stack kanaryası `CONFIG_STACKPROTECTOR` | çekirdeğin kendini koruması | Yüksek — self-protection 2026-10-14 okundu |
| Modül imzalama | Yetkisiz kullanıcı modül yükleyemesin; privileged'e karşı bile `modules_disabled` sysctl veya imzalı modül (`CONFIG_MODULE_SIG_FORCE`, LoadPin) | modül imzası | Yüksek — self-protection 2026-10-14 okundu |
| Lockdown | Çekirdek kilitlenme katmanı olarak anılır; **bu turda upstream kaynağı erişilemedi** (CRAWL_NOT_FOUND) → davranış iddiası yazılmıyor | lockdown | ⚠️ VERIFICATION REQUIRED |
| cgroup-v2 | Tek hiyerarşi; süreçleri hiyerarşik organize edip kaynak dağıtımı; controller hiyerarşiktir — köze yakın kısım uzaktan aşılamaz; mount: `mount -t cgroup2 none $MOUNT_POINT` | kaynak izolasyonu (kaynak kontrolü — **MAC değildir**) | Yüksek — cgroup-v2 2026-10-14 okundu |
| `/proc` `hidepid=` | `hidepid=0` herkese açık; `1` kendi `/proc/pid` dışındakini yasaklar; `2` başka kullanıcının pid dizinlerini görünmez kılar | süreç bilgisi yüzeyi | Yüksek — proc(5) 2026-10-14 okundu |

> **Kaynak notu:** 10 satırın 9'u yukarıdaki upstream dokümanların 2026-10-14
> webfetch okumasına dayanır; 1 satır (Lockdown) okunamadığı için
> `⚠️ VERIFICATION REQUIRED` ile işaretlidir ve iddia üretilmemiştir.

## §2 Kod örnekleri

> **UYARI (zorunlu):** Aşağıdaki 3 bloğun **hiçbiri bu repodan alınmamıştır.**
> Hepsi `kaynak: upstream docs` referanslı **referans kalıplarıdır**; repo'da
> karşılığı yoktur (§3 negatif kanıt). Kullanım öncesi S1 kararı gerekir.

### §2.1 — Aktif LSM listesi okuma kalıbı (referans)

```sh
# kaynak: upstream docs - admin-guide/LSM/index.html
# liste virgul ayrilmistir; capability her zaman ilk siradadir,
# onu minor moduller (or. Yama) ve varsa tek bir major modul (or. SELinux) izler
cat /sys/kernel/security/lsm
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans) | YOK — 2026-10-14 taraması 0 isabet |

### §2.2 — Self-protection yapılandırma okuma kalıbı (referans)

```sh
# kaynak: upstream docs - security/self-protection.html
# KASLR: CONFIG_RANDOMIZE_BASE | modul imzasi: CONFIG_MODULE_SIG_FORCE | RWX: CONFIG_STRICT_KERNEL_RWX
grep -E 'CONFIG_RANDOMIZE_BASE|CONFIG_MODULE_SIG_FORCE|CONFIG_STRICT_KERNEL_RWX' /boot/config-$(uname -r)
# not: /boot/config yolu dagitima gore degisir; dosya okunamazsa sonuc UNKNOWN sayilir
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans) | YOK — repoda kernel config / sysctl scripti yok |

### §2.3 — Landlock etkinlik doğrulama kalıbı (referans)

```sh
# kaynak: upstream docs - userspace-api/landlock.html
# beklenti (docs): "landlock: Up and running"
dmesg | grep landlock || journalctl -kb -g landlock
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans) | YOK — repoda landlock çağrısı 0 |

## §3 CoreMusic repo durumu

Tarama: `shared` + `home.coremusic.net` + `auth.coremusic.net` ağaçları,
`*.php,*.js,*.sh,*.conf,*.yml,*.yaml`, `vendor`/`node_modules` hariç,
desen = `seccomp|bwrap|bubblewrap|setfacl|getfacl|capget|capset|setcap|no_new_privs|landlock|apparmor|selinux|pivot_root|chroot`.

| Yüzey | Kanıt yöntemi | Sonuç (2026-10-14) |
|---|---|---|
| LSM politika dosyası (`.te`, `apparmor.d/`) | glob + grep | **YOK** (negatif kanıt) |
| modül imzalama anahtarı / `.ko` modülü | glob | **YOK** (negatif kanıt) |
| kernel config (`CONFIG_*`) / sysctl hardening scripti | glob | **YOK** (negatif kanıt) |
| cgroup/seccomp yapılandırması | grep | **YOK** (negatif kanıt) |
| OS güvenlik aracı çağrısı (toplam) | grep 14 desen | **0 isabet / 293 dosya** (negatif kanıt) |
| session dizin izni sabiti | `SessionConfig.php:20` idx 19 | `DIR_PERMISSIONS = 0750` — **uygulama katmanının OS'a tek teması** (POSIX DAC, çekirdek koruma değil) |
| session dizin oluşturma | `SessionConfig.php:35` idx 34 | `@mkdir($savePath, 0750, true);` — **uygulama katmanının OS'a tek teması** (mkdir izni OS'ta umask ile birlikte uygulanır) |

**Okuma:** 7 satırın 5'i negatif, 2'si tek satır çiftinin pozitif kanıtıdır.
Çekirdek güvenlik yüzeyi bu repoda **hiç başlamamıştır**; bu, bu kartın
karakteridir (hedef mimari + negatif kanıt).

## §4 Sınır cümleleri

> **K010 ayrimi (baglayici):** Kimlik dogrulama, oturum, sifre ve MFA -> `K010-identitiy`; bu kartta yalnizca Linux dosya/surec/erisim kisitlari (or. session dizini 0750) ele alinir.
> **K009 ayrimi (baglayici):** OWASP denetimi, auth/CSRF/CSP tasarimi, kripto secimi (ADR-022) -> `K009-security`; bu kart yalnız OS katmani + sinir isaretlerini tasinir.
> **Komşu dal:** Cekirdek ici yapisi ve surucu yazimi -> `isletim-sistemi-kernel`; Linux platform geneli -> `isletim-sistemi-turu/linux`.

Ek sınır: bu kart Linux **çekirdek** güvenlik katmanını taşır; POSIX DAC
(rwx/ACL/capability) detayı `yetki-ve-erisim-modeli-linux`, syscall filtresi ve
sandbox `uygulama-guvenlik-siniri-linux` kartlarındadır. `0750` bir DAC ayarıdır
— "çekirdek koruması" diye adlandırılması abartıdır.

## §5 Referans

| URL | İddia | 2026-10-14 durum |
|---|---|---|
| https://docs.kernel.org/security/self-protection.html | `CONFIG_STRICT_KERNEL_RWX`/`CONFIG_MODULE_SIG_FORCE`/`CONFIG_RANDOMIZE_BASE`/SMEP-SMAP/PXN-PAN self-protection iddiaları | HTTP 200 · webfetch ile okundu |
| https://docs.kernel.org/admin-guide/LSM/index.html | LSM hook çatısı; `/sys/kernel/security/lsm` virgül listesi; capability→minor→major sırası | HTTP 200 · webfetch ile okundu |
| https://docs.kernel.org/admin-guide/LSM/SELinux.html | SELinux kernel dokümanı; kernel/userspace repolarına çıkış | HTTP 200 · webfetch ile okundu |
| https://github.com/SELinuxProject/selinux | SELinux = çekirdeğe gömülü MAC; DAC üstünde granülerlik; min çekirdek v4.3 | HTTP 200 · webfetch ile okundu |
| https://www.kernel.org/doc/html/latest/admin-guide/LSM/Yama.html | Yama = core kernel'in almadığı sistem-geneli DAC korumaları; `ptrace_scope` 0/1 davranışı | HTTP 200 · webfetch ile okundu |
| https://docs.kernel.org/userspace-api/landlock.html | Landlock stackable LSM; yetkisiz kendini kısıtlama; denied-by-default; ABI sorgusu | HTTP 200 · webfetch ile okundu |
| https://docs.kernel.org/admin-guide/cgroup-v2.html | cgroup-v2 tek hiyerarşi; controller hiyerarşisi; mount örneği | HTTP 200 · webfetch ile okundu |
| https://man7.org/linux/man-pages/man5/proc.5.html | `/proc` `hidepid=0/1/2` davranışı | HTTP 200 · webfetch ile okundu |
| https://gitlab.com/apparmor/apparmor | AppArmor userspace geliştirme projesi (upstream repo) | HTTP 200 · webfetch ile okundu |
| https://apparmor.net/ | AppArmor DAC'ı MAC ile tamamlar; mainline 2.6.36; profil kısırlaştırma; çekirdekte enforce | HTTP 200 · webfetch ile okundu |

> **Dürüstlük notu:** Lockdown LSM başlığı için upstream doküman bu turda
> erişilemedi (CRAWL_NOT_FOUND) — **§5 tablosuna alınmadı**, §1'de
> `⚠️ VERIFICATION REQUIRED` olarak işaretlendi ve hiçbir davranış iddiası
> yazılmadı.

## §6 Açık sorular

| # | Soru | Durum |
|---|---|---|
| S1 | OS güvenlik katmanı (LSM/capability/seccomp/cgroup) hedef mimaride devreye alıncak mı? | **[UNKNOWN]** — bu kartta KAPATMA YOK |
| S2 | Uygulama ↔ OS güvenlik sorumluluk sınırı (nereye kadar uygulama, nereden itibaren OS)? | **[UNKNOWN]** — bu kartta KAPATMA YOK |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
