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

## §7 Çapraz Kart Matrisi (K000 içi karşılaştırma)

> **Analiz bölümü** — yeni olgu iddiası YOKTUR. Satırlar bu vault'taki diğer
> çekirdek-güvenlik kartlarının §1 özetlerinden türetilmiştir; her hücre o
> kartın kendi kanıt zincirine bağlıdır.

| OS kartı | Root-of-trust | Ana kontrol mekanizması | Linux = temel satır farkı |
|---|---|---|---|
| **Linux (bu kart)** | Bootloader → çekirdek (donanım bağımlı değil) | LSM (SELinux/AppArmor/Yama/Landlock) + capability + seccomp + cgroup | Politika dağıtım katmanındadır; donanım zorunluluğu yoktur |
| iOS | Boot ROM → LLB → iBoot → kernel; SEP ayrı zincir | Hardware root of trust + sandbox | Her halka donanıma gömülüdür; bkz. `cekirdek-guvenlik-ios` |
| macOS | LocalPolicy (Secure Enclave imzalı, Apple silicon) | SIP + KIP + SSV + Gatekeeper | Boot politikası donanım-analidir; kext/system extension ayrık; bkz. `cekirdek-guvenlik-macos` |
| Windows | UEFI Secure Boot + TPM | WDAC/App Control + sürücü imzalama | Politika binary formundadır; bkz. `cekirdek-guvenlik-windows` |
| ReactOS | NT bootloader + HAL paketleri (NT 5.2 hedefi) | Security Reference Monitor (ACL) | Alpha olgunluk; tasarım iddiası ≠ kanıt; bkz. `cekirdek-guvenlik-reactos` |
| Tizen | İmzalama (en az 2 imza) + SMACK | Sertifika seviyesi → API seviyesi | Privilege manifest'te beyan edilir; bkz. `cekirdek-guvenlik-tizen` |
| webOS | Image derleme zinciri + FOTA | Statik servis + ACG (role/groups) | Boot-time statik yapılandırma; bkz. `cekirdek-guvenlik-webos` |

**Okuma:** Linux tek karttır ki çekirdek-güvenlik katmanı **runtime'da
yığılabilir** (LSM sırası `/sys/kernel/security/lsm`): capability → minor →
major (§1). Bu esneklik aynı zamanda yapılandırma yüzeyini büyütür —
CoreMusic hedef mimarisinde Linux, OS güvenlik kararının en çok
dağıtılmış olduğu platformdur (§10.1).

## §8 Ses/Zamanlama Yüzeyi Etkileşimi (Neva Engine senaryosu)

> **Durum:** `PLANNED` senaryo analizi. Repoda Linux çekirdek-güvenlik
> yapılandırması YOKTUR (§3 negatif kanıt). Kaynaklar:
> `Dosya: .ai/electronic/drivers/audio-drivers.md [Satır: 709]` ·
> `Dosya: .ai/projects/NevaEngine/os/03-linux-alsa-pipewire.md [Satır: 92]`.

### §8.1 HAL zinciri ve güvenlik sınırları

`audio-drivers.md` §1.5 (Satır: 709): Linux'ta ALSA-lib kullanıcı
uzayında HAL'dir; çekirdek tarafında ALSA-PCI/ASoC sürücüleri vardır.
Çağrışım: `snd_pcm_open` → `snd_pcm_hw_params` → `snd_pcm_prepare` →
`snd_pcm_writei`/`readi`. Güvenlik açısı:

| Katman | Dosya-sistemi teması | capability ihtiyacı (analiz) |
|---|---|---|
| ALSA-lib (HAL) | `/dev/snd/*` cihaz düğümleri | DAC yeterli (grup `audio`); capability gerekmez |
| PipeWire (üst katman) | Unix soketleri (`/run/user/...`) | DAC; sandbox içindeyse Landlock eklenir |
| Çekirdek sürücü | yok (kullanıcı teması) | capability bölünmesi §2.3 kalıbıyla ölçülür |

### §8.2 RT zamanlama × LSM: bilinen gerilim

NevaEngine `03-linux-alsa-pipewire.md` §2 (Satır: 92) SCHED_FIFO
gerçek-zamanlı kuyruğu tanımlar. LSM/çekirdek etkileşimi (analiz):
SCHED_FIFO yükseltmesi `CAP_SYS_NICE` ile sınırlıdır; seccomp filtresi
`prctl`/`sched_setscheduler` çağrısını reddedebilir ve bu, RT döngüsünün
sessizce normal önekleme moduna düşmesine yol açar (ses: xrun riski,
audio-drivers.md §2.6). Sonuç (tasarım kuralı, uygulanmadı): RT ses süreci
için seccomp profili `sched_setscheduler` ve `mlockall` çağrılarına izin
vermelidir; whitelist bu kart §2.1'in "filtre sandbox değildir" uyarısıyla
birlikte okunur.

### §8.3 cgroup-v2 ↔ RT ses önceliği

§1'deki cgroup-v2 satırı kaynak-izolasyonudur (MAC değildir). Senaryo:
RT ses süreci bir cgroup içine alınırsa `cpu` controller'ı RT önceliğini
kısırlaştırabilir. Analiz kuralı: SCHED_FIFO süreci `cpu.rt_runtime_us`
tanımlı bir cgroup'e **konmaz** ya da controller o süreç için devre dışı
bırakılır. Bu satır upstream cgroup-v2 dokümanına (§1 satırı) bağlı
mühendislik çıkarımıdır; politika uygulanmadı (§3: 0 isabet).

### §8.4 PipeWire üst katmanı güvenlik yüzeyi

PipeWire (NevaEngine §3, Satır: 92) oturum-uzayı bir sunucudur; ACL
yüzeyi kullanıcı dizinindedir. Sandbox senaryosunda (Landlock, §1) sunucu
ile bağlantı için Landlock ağ kuralı (TCP ABI v4 / UDP ABI v10, §1 satırı)
yalnızca kendi oturum soketine izin verecek şekilde yazılmalıdır; aksi halde
ses sunucusuna erişim sandbox duvarını aşan bir IPC yüzeyi olur.

## §9 Başarısızlık Modları ve Risk Matrisi

| # | Başarısızlık | Konum | Belirti | Çıkış kodu/etki (kaynaklı) | CoreMusic etkisi (senaryo) |
|---|---|---|---|---|---|
| F1 | LSM politika reddi | hook uygulaması | yetkisiz işlem reddedilir | audit kaydı (audit2audit) | Uygulama katmanı çalışmaz; fail-closed |
| F2 | seccomp EACCES | `prctl(PR_SET_SECCOMP, ...)` | `-EACCES` | no_new_privs yok ve CAP_SYS_ADMIN yok (§2.1 upstream) | Sandbox kurulamaz → süreç başlatılmaz |
| F3 | capability düşmesi | `capset` | RT önceliği verilemez | `sched_setscheduler` → izin hatası | xrun riski (§8.2) |
| F4 | cgroup mount hatası | `mount -t cgroup2` | mount başarısız | §1 upstream mount kalıbı | Kaynak izolasyonu yok; MAC değil |
| F5 | Landlock reddi | `landlock_create_ruleset` | `-EPERM`/`-ENOSYS` | denied-by-default + ABI sorgusu (§1) | Sandbox katmanı yok; fail-safe: başlatma |
| F6 | Xrun (ALSA) | akış | ses kesintisi | `-EPIPE` (audio-drivers.md §6.3) | Kurtarma: prepare (§6.4 aynı dosya) |
| F7 | modül imzasız | `insmod` | modül reddi | `CONFIG_MODULE_SIG_FORCE` (§1) | Sürücü yüklenemez; fail-closed |

**Örüntü:** 7 modun 5'i (F1–F5) fail-closed davranır; Linux kartının
karakteri "reddet-varsay" tarafındadır. F6-F7 ses zinciri tarafıdır ve
kurtarma akışları ayrı kartlarda (`isletim-sistemi-audio/alsa`).

## §10 Entegrasyon Senaryoları (hedef mimari)

### §10.1 Senaryo A — DAC + grup yeterli (minimum yüzey)

Süreç normal kullanıcı; `/dev/snd/*` erişimi grup `audio` DAC'ı ile;
capability yok; seccomp yok. §3'teki repodaki tek OS teması (`0750` mkdir)
bu modelle uyumludur. S1'e "hayır" cevabının karşılığı. Risk: uygulama
açığı ses sunucusuna tam kullanıcı yetkisiyle yayılır.

### §10.2 Senaryo B — Capability ayrıştırma

Root yerine: `CAP_SYS_NICE` (RT, §8.2) + `CAP_IPC_LOCK` (mlockall) verilir,
geri kalan her şey düşürülür. Ölçüm kalıbı: §2.3 (capget). S1'in "evet"
cevabıyla değerlendirilir; ADR (`>=088`) gerektirir.

### §10.3 Senaryo C — Kendini kısırlaştırma (Landlock + seccomp)

Süreç `no_new_privs` + Landlock (dosya + ağ) + seccomp (whitelist §8.2
notlu) ile kendini kısırlaştırır. Linux'un özgünlüğü: bunu **yetkisiz**
süreç de yapabilir (§1 Landlock satırı). En yüksek yüzey; test yükü en
büyük (§15).

### §10.4 Senaryo karşılaştırma

| Senaryo | Root gerekir mi | S1 | RT uyumu | Yüzey |
|---|---|---|---|---|
| A | Hayır | hayır | DAC yeterli | En küçük |
| B | capability verimi | evet | Tam (SYS_NICE) | Orta |
| C | Hayır (kendini kısırlaştırır) | evet | whitelist'e bağlı | En büyük |

## §11 Kaynakça — envanter ve ölçüm

### §11.1 Atıf yapılan vault dosyaları (2026-10-16 ölçümü)

| Dosya | Satır | Kullanım |
|---|---|---|
| `.ai/electronic/drivers/audio-drivers.md` | 709 | §8.1 ALSA HAL, §8.2/§9 F6 xrun, §6.3 kodlar |
| `.ai/projects/NevaEngine/os/03-linux-alsa-pipewire.md` | 92 | §8.2 SCHED_FIFO, §8.3 PipeWire |
| `.ai/projects/NevaEngine/09-open-source-virtual-audio-driver.md` | 200 | §13.3 referans |
| `.ai/electronic/drivers/embedded.md` | 510 | §14 RPi5/NEON bağlamı |
| `isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-{ios,macos,windows,reactos,tizen,webos}.md` | (bu vault) | §7 çapraz satırlar |
| `isletim-sistemi-audio/alsa/` | (bu vault) | §9 F6 kurtarma kartı |

### §11.2 Upstream kanıt durumu (bu kart §1'deki URL'ler)

| URL grubu | §1'de durum | Bu ekte kullanım |
|---|---|---|
| docs.kernel.org LSM/SELinux/Yama/Landlock | 2026-10-14 okundu | §7, §9, §10 |
| self-protection + modül imzalama | 2026-10-14 okundu | §9 F7 |
| cgroup-v2 | 2026-10-14 okundu | §8.3 |
| proc(5) hidepid | 2026-10-14 okundu | §9 (süreç bilgisi yüzeyi) |
| Lockdown | ⚠️ VERIFICATION REQUIRED (§1) | Bu ekte iddia edilmedi |

## §12 Karar Ağaçları

### §12.1 Linux sandbox seçimi

```
Neva Engine Linux (PLANNED)
    │
    ├─ Dağıtım biliniyor mu (Ubuntu/Debian/Fedora/Arch)?
    │     HAYIR → Senaryo A (DAC) + runtime capability yok
    │     EVET  → LSM varsayılanını oku: cat /sys/kernel/security/lsm (§2.1)
    │
    ├─ RT gerekli mi (canlı monitör)?
    │     EVET  → Senaryo B: CAP_SYS_NICE (§8.2 whitelist notu)
    │
    └─ Kendini kısırlaştırma hedefi var mı?
          EVET → Senaryo C: no_new_privs → Landlock → seccomp sırası (§10.3)
```

### §12.2 xrun ↔ güvenlik karar ağacı

```
Xrun görülür (audio-drivers.md §6.4)
    ├─ seccomp açık mı? → whitelist sched_setscheduler/mlockall (§8.2)
    ├─ cgroup içinde mi? → cpu controller RT engeli (§8.3)
    └─ capability düştü mü? → capget ölçümü (§2.3, Senaryo B)
```

## §13 Zaman Çizelgesi (vault ölçümleri)

| Tarih | Olay | Kaynak |
|---|---|---|
| 2026-10-14 | LSM/cgroup/landlock/proc upstream dokümanları okundu | bu kart `validation:` |
| 2026-10-14 | Repo taraması: LSM/sysctl/modül/cgroup 0 isabet (293 dosya) | bu kart §3 |
| 2026-10-14 | `SessionConfig.php:20/:35` — POSIX DAC (`0750`) tek temas | bu kart §3 |
| 2026-10-15 | NevaEngine Linux backend dosyası ölçüldü (92 satır) | `.ai/projects/NevaEngine/os/03` |
| 2026-10-16 | Bu ek yazıldı | bu dosya |

## §14 Sürüm/Yenileme Tetikleyicileri

| Tetikleyici | Etkilenen bölüm | Aksiyon |
|---|---|---|
| Linux çekirdek sürüm değişimi (LSM değişikliği) | §1, §9 | upstream URL'leri yeniden okunur |
| Landlock ABI değişimi | §1, §10.3 | ABI sorgu kalıbı (§2.3) yeniden ölçülür |
| PipeWire sürüm/değişimi | §8.4 | soket/ACL modeli yeniden okunur |
| Neva Engine Linux port başlangıcı | §8, §10 | senaryo seçimi ADR'ye taşınır |

## §15 Test ve Doğrulama Önerileri (senaryo, uygulanmadı)

| # | Doğrulama | Yöntem (referans kalıp) | Beklenen | Kanıt durumu |
|---|---|---|---|---|
| V1 | Aktif LSM listesi | §2.1 kalıbı | capability + minor + (major) | ⚠️ ortam gerekli |
| V2 | Self-protection config | §2.2 kalıbı | `CONFIG_RANDOMIZE_BASE` vb. | ⚠️ ortam gerekli |
| V3 | Landlock etkinliği | §2.3 kalıbı | "landlock: Up and running" | ⚠️ ortam gerekli |
| V4 | Capability ölçümü | §2.3 capget kalıbı | Effective: yalnız istenen bitler | Kod yazılınca (PLANNED) |
| V5 | RT × seccomp whitelist | strace/audit + xrun sayacı | reddedilen syscall 0, xrun <%0.01 | Kod yazılınca |
| V6 | cgroup RT engeli | §8.3 senaryosu | RT önceliği korunur veya süreç cgroup dışı | Kod yazılınca |

## §16 Sınır İhlali Kayıtları (negatif kontroller)

| Kontrol | Sonuç |
|---|---|
| Bu ekte çekirdek kodu (C/asm) uyduruldu mu? | HAYIR — yalnız referans sh kalıpları (§2'deki bloklar kaynaklı) |
| Repo'da LSM/sysctl var diye iddia edildi mi? | HAYIR — §3 negatif kanıt korunur |
| Lockdown davranışı yazıldı mı? | HAYIR — §1 `⚠️ VERIFICATION REQUIRED` korunur |
| S1/S2 kapatıldı mı? | HAYIR |
| Frontmatter/`status:` değişti mi? | HAYIR (append-only) |

## §17 Ek: SSS — Bu Kartın Sınırı (kısa)

| Soru | Kısa cevap | Derinlemesine |
|---|---|---|
| LSM politikası MAC mı, DAC tamamlayıcısı mı? | İkisi de olabilir — major LSM (SELinux/AppArmor) MAC; Yama/Landlock ek koruma | §1 |
| seccomp bir sandbox mudur? | Docs: "System call filtering isn't a sandbox" — yalnızca çağrı yüzeyi | §2.1 |
| cgroup-v2 güvenlik (MAC) sağlar mı? | Hayır — kaynak izolasyonudur | §1, §8.3 |
| Landlock'u yetkisiz süreç kurabilir mi? | Evet — Linux özgünlüğü (kendini kısırlaştırma) | §1, §10.3 |
| Repoda Linux güvenlik yapılandırması var mı? | Hayır — 0 isabet (negatif kanıt) | §3 |
| `SessionConfig.php` çekirdek koruma sağlar mı? | Hayır — POSIX DAC (`0750`), uygulama katmanı | §3 |
| RT ses × seccomp çakışır mı? | Evet — whitelist `sched_setscheduler`/`mlockall` ister | §8.2 |
| Lockdown davranışı bu kartta yazıldı mı? | Hayır — `⚠️ VERIFICATION REQUIRED` | §1, §11.2 |

## §18 Ek: LSM Yığın Karşılaştırmalı Notlar (upstream §1 satırlarına bağlı)

> Bu bölüm §1 tablosunun satırlarını detaylandırır; her satır §1'de
> "2026-10-14 okundu" işaretli upstream dokümana dayanır. Yeni olgu yoktur.

### §18.1 LSM adaylarının kontrol yüzeyi karşılaştırması

| LSM | Kontrol yüzeyi (upstream) | CoreMusic senaryosundaki rolü | Bağlantı |
|---|---|---|---|
| SELinux | Process/domain etiketleri + politika (`.te`→`pp`); major LSM | Dağıtım-özel sert politika isteyen kurulumlar | §3: repoda 0 isabet |
| AppArmor | Profil bazlı dosya/capability/soket/DBus/IPC kısıtı; mainline 2.6.36+ | Uygulama profili ile ses süreci kısırlaştırma adayı | §10.2 (Senaryo B ile birleşik) |
| Yama | `ptrace_scope` (0 klasik / 1 restricted) | Ayıklama yüzeyi kısıtı; ses zincirine doğrudan etkisi yok | §9 F1 |
| Landlock | Dosya sistemi + ağ (TCP v4 / UDP v10); denied-by-default; yığılabilir | Kendini kısırlaştırma (Senaryo C) | §10.3, §12.1 |
| capability | `capget`/`capset` bitmask (effective/permitted/inheritable) | RT/mlock yeteneklerinin ayrıştırılması | §2.3, §8.2 |

### §18.2 Sıralamanın anlamı (§1 "capability hep ilk" kuralı)

LSM listesi virgül ayrımıdır ve **kontrol sırasıdır**: capability her zaman
ilk; sonra minor modüller (ör. Yama); en fazla bir major (SELinux ya da
AppArmor). Pratik sonuç (analiz): bir reddedilen işlemde audit kaydı önce
capability'ye, sonra LSM hook'larına düşer; teşhis sırası bu yüzden
`getcap`/`capsh` → `aa-status`/`semanage` → `dmesg/audit` olarak planlanır
(§15 V1 bu sırayla okunur).

### §18.3 hidepid üçlüsü (§1 satırı proc(5) kaynağına bağlı)

| Değer | Davranış | Senaryo etkisi |
|---|---|---|
| `hidepid=0` | `/proc` herkese açık | Süreç bilgisi sızıntısı (varsayılan) |
| `hidepid=1` | Başkasının `/proc/pid` dizinleri yasak | Teşhis zorlaşır, gizlilik artar |
| `hidepid=2` | Başkasının pid dizinleri görünmez | En sıkı; `hidepid=2` + `gid=` grubu ile teşhis yetkisi verilir |

Senaryo C (§10.3) ile birlikte `hidepid=2` düşünülebilir; ancak
`journalctl`/`audit` teşhisi için `gid=` grubu şarttır — aksi halde §15 V1
ölçülemez. Bu çıkarım proc(5) satırına bağlı mühendislik notudur.

### §18.4 self-protection bayrakları ↔ §9 F7 (modül imzası)

§1 self-protection satırı üç bayrak sayar: `CONFIG_STRICT_KERNEL_RWX` /
`CONFIG_STRICT_MODULE_RWX` (kod yazılabilir olmasın), `CONFIG_RANDOMIZE_BASE`
(KASLR), `CONFIG_STACKPROTECTOR`. Modül imzalama (`CONFIG_MODULE_SIG_FORCE`)
§2.2 kalıbıyla okunur. Üç okuma birlikte §15 V2'yi oluşturur; tek tek
değil **toplu** okunması önerilir (aynı `grep -E` kalıbı).

### §18.5 cgroup-v2 mount + controller hiyerarşisi (§1 satırı)

Mount kalıbı (upstream): `mount -t cgroup2 none $MOUNT_POINT`. Controller
hiyerarşiktir: köke yakın kısım uzaktan aşılamaz. §8.3'ün RT uyarısı bu
hiyerarşiden doğar: `cpu` controller'ı üst düzeyde tanımlandıysa alt
süreçler için `cpu.rt_runtime_us` **ayıramazsınız** — RT ses süreci ya
cgroup dışında kalır ya da controller üst düzeyde bırakılır (Senaryo A'ya
kayış). Bu satır mount + hiyerarşi satırlarının birleşik çıkarımıdır.

### §18.6 Denetim listesi — upstream satır ↔ kart bölümü eşlemesi

| Upstream kanıt (§1, §11.2) | Kart bölümü | Kullanım |
|---|---|---|
| LSM index (liste/sıra) | §2.1, §18.2 | Teşhis sırası |
| SELinux userspace README | §1, §10 | major LSM adayı |
| apparmor.net | §1, §18.1 | profil modeli |
| Yama dokümanı | §1, §18.1 | ptrace yüzeyi |
| Landlock dokümanı | §1, §10.3, §12.1 | Senaryo C |
| self-protection | §2.2, §9 F7, §18.4 | Bayrak okuması |
| cgroup-v2 | §1, §8.3, §18.5 | RT engeli |
| proc(5) | §1, §18.3 | hidepid |

## §19 Ek: Uygulama ↔ OS Sorumluluk Paylaşımı Tablosu (S2 işaretleri, karar değil)

> S2 (§6) bu kartta kapatılmaz; bu tablo yalnız **işaret**tır — hangi
> sorumluluğun hangi katmanda durduğu upstream mekanizmalara göre
> çizilmiştir. Karar mimari sahibine aittir.

| Sorumluluk | Uygulama katmanı (PHP/Neva) | OS katmanı (Linux) | Upstream dayanağı |
|---|---|---|---|
| Kimlik doğrulama/oturum | Evet (K010) | Hayır | K010 ayrımı (§4) |
| Şifre kriptografisi | Evet (K009, ADR-022) | Hayır | K009 ayrımı (§4) |
| Dosya izni (session `0750`) | Evet — uygulama ayarlar | DAC uygular | §3 `SessionConfig.php:20/:35` |
| ACL/capability bölümü | Hayır | Evet | §2.2/§2.3 kalıpları |
| Sandbox (seccomp/Landlock) | Hayır — OS'a bırakılır | Evet | §2.1, §10.3 |
| Kaynak izolasyonu | Hayır | Evet (cgroup) | §1, §8.3 |
| RT önceliği | İstek (SCHED_FIFO) | Yetkilendirme (SYS_NICE) | §8.2 |
| Süreç bilgisi gizliliği | Hayır | Evet (hidepid) | §18.3 |
| Sürücü imzalama | Hayır | Evet (MODULE_SIG) | §18.4 |

**Okuma:** 9 satırın 3'ü (dosya izni, RT isteği, kimlik) uygulama ile
OS **arasında** paylaşımdır; bu üçü S2'nin gerçek tartışma yüzeyidir.
Kalan 6 satır tek taraflıdır ve bu kartın dışında karar gerektirmez.

## §20 Ek: Kısa SSS (bu kartın dışına çıkarma)

| Soru | Cevap | Bölüm |
|---|---|---|
| AppArmor ile SELinux aynı anda çalışır mı? | LSM index: en fazla **bir** major modül | §1, §18.2 |
| seccomp filtresi kernel panic üretir mi? | Hayır — reddedilen çağrı `-EACCES`/`-EPERM`; fail-closed süreç | §9 F2 |
| Landlock kuralları kaldırılabilir mi? | Sınırlandırılmış süreç geri genişleyemez (tek yönlü) — ayrıntı upstream'de, bu kart iddia etmez | ⚠️ VERIFICATION REQUIRED |
| `0750` mkdir umask ile birlikte nasıl uygulanır? | OS umask ile maskelenir; gerçek izin ölçülmeden bilinmez | §3 |
| Bu kart Neva Engine'i bağlar mı? | Hayır — `PLANNED`; §8 yalnız senaryo | §8 |
| Repoda çekirdek politikası ne zaman başlar? | S1 kararı + ADR (`>=088`) sonrası | §6, §10 |
| Audit kaydı nereye düşer? | `auditd`/journal — dağıtıma göre; okuma upstream audit dokümanındadır, bu kart iddia etmez | ⚠️ VERIFICATION REQUIRED |
| Senaryo C'de sandbox ihlali sessiz mi? | Hayır — LSM fail-closed (F1); uygulama katmanına hata döner | §9 F1 |
| xrun kurtarması güvenlik olayı mı? | Hayır — zamanlama olayı; güvenlikle kesişimi yalnız whitelist (§8.2) | §8.2, §9 F6 |
| `audio` grubu DAC yerine capability ile değiştirilebilir mi? | Evet (Senaryo B) — ölçüm §2.3 kalıbıyla | §10.2, §15 V4 |
| PipeWire sunucusu sandbox duvarını aşar mı? | Aşabilir — Unix soketi IPC yüzeyidir; Landlock ağ kuralıyla sarılır | §8.4 |
| KASLR okuması hangi kalıpla? | §2.2 `grep -E` kalıbı (`/boot/config-$(uname -r)`); dosya yoksa UNKNOWN | §2.2 |

## §21 Ek: Bu Kartın Okuma Sırası (yeni okuyucu için)

1. §0 ELI15 — çerçeve.
2. §1 profil tablosu — 10 mekanizma, her satır upstream'imzası ile.
3. §3 repo durumu — **negatif kanıt** bu kartın karakteridir; §2'deki
   blokların "repo karşılığı YOK" tablolarını atlamayın.
4. §7 çapraz matris — diğer altı OS kartıyla farklar.
5. §8–§10 senaryo katmanı — Neva Engine `PLANNED`; S1/S2'ye bağlanır.
6. §12–§13 ağaçlar — uygulama kararını hızlı gösterir.
7. §15–§16 — ölçüm ve negatif kontroller.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
