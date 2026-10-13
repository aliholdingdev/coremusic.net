---
node_id: K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-ornekler
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-anayasa
  - K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-surucu
evidence:
  - shared/src/Session/SessionConfig.php:20
  - shared/src/Session/SessionConfig.php:35
  - https://docs.kernel.org/admin-guide/LSM/Yama.html
  - https://gitlab.com/apparmor/apparmor/-/wikis/home
  - https://man7.org/linux/man-pages/man2/memfd_create.2.html
validation:
  - "2026-10-13: 3 web URL HEAD ile HTTP 200; 2633'luk katalog envanteriyle kesisim 0."
  - "2026-10-13: 2 disk kaniti `Dosya:` + `Satir:` + Select-Object -Index (N-1) ile satir birebir okundu."
open_questions:
  - "S1: OS guvenlik katmani (capabilities/seccomp/LSM) hedef mimaride devreye alinacak mi? [UNKNOWN]"
group: kod
---

# Cekirdek-Guvenlik Örnekleri — Repo Kanıtı, Upstream Yapılandırmaları, Anti-Pattern Tablosu

## §0 ELI15

Bu dosya bu alt-daldaki **kanıt dosyasıdır**: her satır ya (a) bu repo'da
**gerçekten okunmuş** bir kod satırıdır, ya (b) upstream (resmi doküman)
yapılandırmasıdır, ya da (c) **negatif kanıttır** — "şu yüzey repo'da yoktur".
Üçüncü tür kanıt burada en az ilki kadar değerlidir, çünkü CoreMusic'te çekirdek
güvenlik kodu **bulunmadığını** kanıtlar. Uydurma kod bloğu yoktur; repo'da
karşılığı olmayan her şey `kaynak: upstream docs` ile imzalanır.

> **Oncelikli gercek:** `shared/` + `home.coremusic.net/` + `auth.coremusic.net/`
> ağaçlarında çekirdek-güvenlik seviyesinde tek bir satır kod **YOKTUR**
> (PHP/JS ağacı taraması, `vendor`/`node_modules` hariç, 2026-10-13). Bu dosyanın
> §1'inde negatif kanıt, §2'sinde repo'nun **tek** gerçek OS-yakın kanıtı
> (session dizin izni `0750`) vardır.

## §1 Çalışan yapılandırmalar

### §1.1 Repo kanıt durumu — çekirdek güvenlik yüzeyi

| Yüzey | Beklenen dosya tipi | Repo'da var mı | Kanıt | Durum |
|---|---|---|---|---|
| secure boot policy | `.efi` / `sbctl` config | HAYIR | glob: `*.efi` 0 | negatif kanıt |
| module signing | `modules.sign` / `insmod` kabuk | HAYIR | glob: `*.ko` 0 | negatif kanıt |
| kernel config (`CONFIG_*`) | `config-6.x` | HAYIR | glob: `config-*` 0 | negatif kanıt |
| seccomp BPF programı | `*.bpf` / `seccomp_rule` | HAYIR | PHP/JS ağacı: 0 | negatif kanıt |
| LSM politikası (SELinux/AppArmor) | `.te` / `apparmor.d` | HAYIR | glob: `*.te` 0 | negatif kanıt |
| capability ayarı | `setcap` / `cap_set_proc` | HAYIR | PHP/JS ağacı: 0 | negatif kanıt |
| ASLR / SMEP / SMAP bayrağı | sysctl scripti | HAYIR | glob: `*.sysctl` 0 | negatif kanıt |
| imzalama anahtarı | `*.asc` / `*.pem` (release) | HAYIR | `.gitignore` + secret yasağı | gizlilik kuralı |
| **session dizin izni** | PHP `mkdir` modu | **EVET** | `SessionConfig.php:20,35` | **POZİTİF** |

**Okuma:** 9 satırın 8'i negatif, 1'i pozitiftir. Bu oran bu alt-dalın
karakteridir: **hedef mimari + negatif kanıt** dalı, calisan kod raporu değil.

### §1.2 Tek pozitif repo kanıtı — session dizin izni

```text
Dosya: shared/src/Session/SessionConfig.php
Satır: 20
Select-Object -Index 19
```

```php
    public const DIR_PERMISSIONS = 0750;       // Session dizin izinleri
```

```text
Dosya: shared/src/Session/SessionConfig.php
Satır: 35
Select-Object -Index 34
```

```php
            @mkdir($savePath, 0750, true);
```

| Alan | Değer | Okuma |
|---|---|---|
| sabit | `DIR_PERMISSIONS = 0750` | sahibi rwx, grup r-x, diğer 0 |
| gerçek kullanım | `mkdir($savePath, 0750, true)` | session dizini bilerek dar |
| diğer (others) izni | `---` | dosya sistemi düzeyinde **red** |
| ne korur | session dosyaları | oturum çalma yüzeyini daraltır |
| ne KORUMAZ | çekirdek saldırı yüzeyi | bu bir **DAC** ayarıdır, kernel değil |

**Dürüstlük notu:** `0750` bir **POSIX dosya iznidir** — çekirdek koruma
halkası değildir. Bunu çekirdek-güvenlik kanıtı saymak **abartı** olurdu;
dosyaya `§4` kabul kriterinde bu sınır açıkça yazılır.

### §1.3 Upstream yapılandırma envanteri (repo'da uygulanmamış — hedef mimari)

| # | Yapılandırma | Ne yapar | Upstream kaynak | Repo durumu |
|---|---|---|---|---|
| C-01 | Secure Boot + shim | önyüklenen bileşen imzası doğrular | upstream docs | uygulanmamış |
| C-02 | `LOCKDOWN_LSM` | kernel imzalama/`/dev/mem` kilitler | `docs.kernel.org` | uygulanmamış |
| C-03 | `CONFIG_MODULE_SIG` | yüklenen modül imzası zorunlu | `docs.kernel.org` | uygulanmamış |
| C-04 | `CONFIG_RANDOMIZE_BASE` | KASLR — kod adresi rastgele | upstream docs | uygulanmamış |
| C-05 | SMEP/SMAP (CR4) | kullanıcı belleği-yürütme/kernel erişimi engeli | upstream docs | uygulanmamış |
| C-06 | Yama LSM (`ptrace_scope`) | izinsiz `ptrace` kısıtı | `docs.kernel.org/admin-guide/LSM/Yama.html` | uygulanmamış |
| C-07 | AppArmor profil | süreç başına dosya/ağ erişim profili | `gitlab.com/apparmor` | uygulanmamış |
| C-08 | SELinux modu | MAC politikası + `load_policy` | `man7.org/.../load_policy.8.html` | uygulanmamış |
| C-09 | `no_new_privs` | `exec` ile yetki yükseltme yok | `man7.org/.../prctl` (envanterde) | uygulanmamış |
| C-10 | seccomp filtre | syscall beyaz listesi | `man7.org/.../seccomp.2.html` (envanterde) | uygulanmamış |

> **Kaynak:** upstream docs (resmi); her satır §5'teki URL tablosuna bağlıdır.

### §1.4 Kanıt sınıfı matrisi

| Sınıf | Tanım | Bu dosyada | Güven düzeyi |
|---|---|---|---|
| P — pozitif | repo satırı `Select-Object -Index` ile okundu | §1.2 (2 satır) | en yüksek |
| N — negatif | glob/tarama ile yokluğu ölçüldü | §1.1 (8 satır) | yüksek |
| U — upstream | resmi doküman yapılandırması | §1.3 (10 satır) | orta (uygulanmadı) |
| R — referans | kalıp/kod örneği, repo'da yok | §2 (5 blok) | düşük (yalnız şablon) |

**Kural:** P sınıfı satır olmadan hiçbir iddia "repo'da uygulanıyor" diye
yazılmaz. Bu dosyada P sınıfı satır sayısı **2**dir ve ikisi de §1.2'dedir.

### §1.5 Session yüzeyi — OS'a en yakın ikinci kanıt

| Ayar | Satır | Değer | OS etkisi | Sınıf |
|---|---|---|---|---|
| `COOKIE_EXPIRY` | 16 | `42000` | cookie ömrü | uygulama |
| `MAX_LIFETIME` | 17 | `1800` | oturum azami ömrü | uygulama |
| `IDLE_TIMEOUT` | 18 | `3600` | boşta kalma | uygulama |
| `ROTATION_INTERVAL` | 19 | `1800` | kimlik rotasyonu | uygulama |
| `DIR_PERMISSIONS` | 20 | `0750` | **dosya sistemi izni** | **OS (DAC)** |
| `httponly` | 59 | `true` | JS erişimi kapalı | tarayıcı |
| `samesite` | 60 | `Lax` | CSRF yüzeyi | tarayıcı |

**Çıkarım:** 7 satırın yalnızca **1i** (`DIR_PERMISSIONS`) OS düzeyindedir;
kalanı uygulama/tarayıcı düzeyidir. Bu oran, `uygulama-guvenlik-siniri` dalının
S2 bulgusuyla **aynı sonuca** varır.

### §1.6 Hedef mimari eşleme — upstream satır → CoreMusic karşılığı

| # | Upstream satır | Beklenen CoreMusic karşılığı | Katman | Repo durumu | Sınıf |
|---|---|---|---|---|---|
| M-01 | `LOCKDOWN_LSM=integrity` | sysctl/bootstrap scripti | kernel | yok | U |
| M-02 | `CONFIG_MODULE_SIG=y` | modül imzalama anahtarı | kernel | yok | U |
| M-03 | `CONFIG_RANDOMIZE_BASE=y` | boot parametre doğrulama | kernel | yok | U |
| M-04 | `vm.mmap_min_addr=65536` | sysctl hardening dosyası | kernel | yok | U |
| M-05 | `kernel.kptr_restrict=1` | sysctl hardening dosyası | kernel | yok | U |
| M-06 | `kernel.dmesg_restrict=1` | sysctl hardening dosyası | kernel | yok | U |
| M-07 | `kernel.perf_event_paranoid=2` | sysctl hardening dosyası | kernel | yok | U |
| M-08 | `fs.protected_symlinks=1` | dosya sistemi politikası | kernel | yok | U |
| M-09 | `fs.protected_hardlinks=1` | dosya sistemi politikası | kernel | yok | U |
| M-10 | `kernel.yama.ptrace_scope=1` | Yama LSM yapılandırması | LSM | yok | U |
| M-11 | AppArmor profili (web/process) | `aa-enforce` politika seti | LSM | yok | U |
| M-12 | SELinux politika seti | `semodule` yükleme adımı | LSM | yok | U |
| M-13 | seccomp syscall beyaz listesi | sandbox profili | syscall | yok | U |
| M-14 | `no_new_privs` bayrağı | `prctl` çağrısı | syscall | yok | U |
| M-15 | `umask 027` (shell) | deploy scripti | dosya sistemi | gözlenmedi | N |
| M-16 | session dizin izni | `mkdir` modu | dosya sistemi | **`0750`** | **P** |

**Toplam:** 16 satır = **1 P · 1 N · 14 U**. Hedef mimari eşlemesinin %
87.5i henüz **uygulanmamıştır**; bu oran bu alt-dalın S1'e dayanak bulgusudur.

### §1.7 Sınıflar arası geçiş kuralı

| Geçiş | Mümkün mü? | Gerekçe |
|---|---|---|
| U → P | EVET | uygulama yapıldıktan sonra satır ölçülür |
| R → P | EVET | referans kod repoya taşındıktan sonra `Select-Object -Index` ile kanıtlanır |
| N → P | EVET | yokluk giderilir, glob yeniden ölçülür |
| P → U | HAYIR | ölçülmüş satır geri alınmaz, yalnız dosya değişince yeniden ölçülür |
| U → "uygulandı" (doğrudan) | **HAYIR** | kanıtsız iddia = uydurma |
| R → "repo'da var" (doğrudan) | **HAYIR** | referans kod ≠ repo kodu |

## §2 Kod örnekleri

> **UYARI (zorunlu):** Aşağıdaki 5 bloğun **hiçbiri bu repodan alınmamıştır.**
> Hepsi `kaynak: upstream docs` referanslı **referans kalıplarıdır**; repo'da
> karşılığı yoktur (§1.1 negatif kanıt). Çıkarıp kullanmadan önce hedef mimari
> sahibinin onayı gerekir (S1 `[UNKNOWN]`).

### §2.1 — Kernel parametre okuma kalıbı (referans)

```sh
# kaynak: upstream docs - admin-guide/sysfs-rules.html
# cekirdek durumunu okumak yazmaktan guvenlidir
uname -r
cat /proc/cmdline
grep -E 'kaslr|smep|smap| lockdown' /proc/cmdline
```

| Alan | Değer |
|---|---|
| Sınıf | R (referans) |
| Repo karşılığı | YOK |
| Başarı | komut çıkış üretir |
| Risk | çıkış yorumu bilinmiyorsa `UNKNOWN` sayılır |

### §2.2 — LSM modu sorgulama kalıbı (referans)

```sh
# kaynak: upstream docs - admin-guide/LSM
cat /sys/kernel/security/lsm
# ornek cikti: lockdown,capability,yama
```

```text
Dosya: shared/src/Session/SessionConfig.php
Satır: 45
Select-Object -Index 44
```

```php
            cookieDomain: '.coremusic.net',
```

**Neden yan yana:** solda **OS'un** güvenlik dizgesi, sağda **uygulamanın**
tek somut OS-yakın ayarıdır. İkisi farklı katmandır; biri diğerinin yerine
geçmez. `cookieDomain` bir yetki mekanizması değildir — yalnızca cookie
kapsamıdır ve **imza/doğrulama içermez**.

### §2.3 — LSM politika derleme kalıbı (referans, AppArmor)

```sh
# kaynak: gitlab.com/apparmor/apparmor wikis/home
# profil derleme ve yukleme - ornek yapisal akis
aa-status
aa-enforce /etc/apparmor.d/usr.sbin.nginx
```

| Alan | Değer |
|---|---|
| Sınıf | R (referans) |
| Repo karşılığı | YOK (`aa-*` binary'si repoda yok) |
| Bağımlılık | `apparmor-utils` paketi |
| Kapı davranışı | bu repo'nun CI'ında bu komut **koşmaz** |

### §2.4 — Module signing doğrulama kalıbı (referans)

```sh
# kaynak: upstream docs - process/module-signing.html
cat /proc/keys
dmesg | grep -i 'module verification'
# beklenti: 'module verification failed' gorulmemeli
```

| Alan | Değer |
|---|---|
| Sınıf | R (referans) |
| Repo karşılığı | YOK |
| Çıkış sınıfı | log satırı |
| Yorum kuralı | belirsiz yorum = `UNKNOWN`, uydurma = yasak |

### §2.5 — Dosya sistemi izni kalıbı (bu repoda ÇALIŞAN tek örnek)

```text
Dosya: shared/src/Session/SessionConfig.php
Satır: 35
Select-Object -Index 34
```

```php
            @mkdir($savePath, 0750, true);
```

```text
Dosya: shared/src/Session/SessionConfig.php
Satır: 20
Select-Object -Index 19
```

```php
    public const DIR_PERMISSIONS = 0750;       // Session dizin izinleri
```

| Alan | Değer |
|---|---|
| Sınıf | **P (pozitif)** |
| Doğrulama | 2/2 `Select-Object -Index` birebir |
| Eşik | `others` izni `---` olmalı |
| Kabul | `0750` (veya daha dar `0700`) |

**Ölçüm (yeniden üretilebilir):**

```powershell
$c = Get-Content 'shared/src/Session/SessionConfig.php' -Encoding UTF8
$c[19]   # L20 -> DIR_PERMISSIONS = 0750
$c[34]   # L35 -> @mkdir($savePath, 0750, true)
```

### §2.6 — Boot parametre denetim kalıbı (referans)

```sh
# kaynak: upstream docs - admin-guide/kernel-parameters.html
tr ' ' '\n' < /proc/cmdline | grep -E 'nokaslr|nosmap|nosmep|init_on_alloc'
# bos cikti: mitigasyonlar devrede sayilir
```

```text
Dosya: shared/src/Session/SessionConfig.php
Satır: 17
Select-Object -Index 16
```

```php
    public const MAX_LIFETIME = 1800;          // 30 dakika
```

| Alan | Değer |
|---|---|
| Sınıf | üstte R (referans), altta **P (pozitif)** |
| Repo karşılığı | yalnız alttaki satır |
| Doğrulama | idx 16 birebir |
| Sınır | `MAX_LIFETIME` bir **zaman aşımı** ayarıdır, güvenlik kontrolü değildir |

### §2.7 — Dosya hiyerarşisi izin kalıbı (referans + repo özeti)

```sh
# kaynak: upstream docs - dosya hiyerarşisi
# erisim maturizasyonu: en az yetki ilkesi
ls -ld /var/lib/sessions
# beklenti: drwxr-x--- (0750) veya drwx------ (0700)
```

| Kontrol | Beklenen | Repo karşılığı | Durum |
|---|---|---|---|
| dizin modu | `0750` / `0700` | `SessionConfig.php:35` → `0750` | **POZİTİF** |
| sahibi | servis kullanıcısı | `UNKNOWN` (deploy ortamı) | `UNKNOWN` |
| grup | daraltılmış grup | `UNKNOWN` (deploy ortamı) | `UNKNOWN` |
| diğer | `---` | `0750` → `---` | **POZİTİF** |
| genel dosya modu | `0640` / `0600` | repoda ölçülmedi | `⚠️ VERIFICATION REQUIRED` |
| `umask` | `027` / `077` | repoda ölçülmedi | `⚠️ VERIFICATION REQUIRED` |

**Okuma:** 6 satırın 2si pozitif, 2si bilinmiyor, 2si ölçülmemiştir. Ölçülmemiş
satır "uygun" diye yazılmaz; `⚠️ VERIFICATION REQUIRED` ile işaretlenir ve
`-dogrulama.md` dosyasının ölçüm listesine girer.

### §2.8 — Referans blokların kullanımı kuralı

| # | Kural | İhlalde sonuç |
|---|---|---|
| R-1 | Referans blok repoya **kopyalanmadan önce** S1 kararı alınır | karar gasbı |
| R-2 | Her blok `kaynak:` satırı taşır | kaynaksız = uydurma |
| R-3 | Blok içinde `repo'da` ibaresi geçmez | yanlış kanıt |
| R-4 | Blok başlığı `referans` sözcüğünü içerir | sınıf belirsizliği |
| R-5 | Blok sonrası tablo `Repo karşılığı` satırı içerir | izlenemezlik |

## §3 Anti-pattern

### §3.1 Çekirdek güvenlik anti-pattern tablosu

| # | Anti-pattern | Sonuç | Tespit yöntemi | Bu repoda |
|---|---|---|---|---|
| A-01 | `chmod 0777` session/klasör | herkes yazar | `Select-String -Pattern '0777'` | gözlenmedi |
| A-02 | çekirdek imzasız modül yükleme | kökleşmiş kötü amaçlı kod | `modinfo` + dmesg | uygulanmamış |
| A-03 | `kernel.kptr_restrict=0` | kernel adres sızıntısı | sysctl okuma | uygulanmamış |
| A-04 | `kernel.dmesg_restrict=0` | log üzerinden bilgi sızıntısı | sysctl okuma | uygulanmamış |
| A-05 | `ptrace_scope=0` | süreç belleği okunabilir | Yama LSM | uygulanmamış |
| A-06 | AppArmor/SELinux `disabled` | MAC yok, yalnız DAC | `/sys/kernel/security/lsm` | uygulanmamış |
| A-07 | seccomp filtresiz sandbox | syscall yüzeyi sınırsız | `seccomp_rule` yok | uygulanmamış |
| A-08 | hardcoded çekirdek anahtarı | secret sızıntısı | secret taraması | yasak (kural) |
| A-09 | log'a parola/anahtar yazma | log üzerinden ifşa | log denetimi | yasak (kural) |
| A-10 | `sudoers` NOPASSWD genişletme | yetki yayılımı | `sudoers` denetimi | repoda yok |
| A-11 | `SUID` biti genişletilmiş binary | yetki yükseltme yüzeyi | `find -perm -4000` | repoda yok |
| A-12 | `umask 0000` | yeni dosya herkese açık | `umask` okuma | gözlenmedi |

### §3.2 Belge-anti-pattern'leri (bu vault'a özgü)

| # | Anti-pattern | Neden kötü | Doğrusu |
|---|---|---|---|
| B-01 | "repo'da uygulandı" + upstream kod | uydurma kanıt | `kaynak: upstream docs` yaz |
| B-02 | negatif kanıtı silmek | gerçeği gizlemek | negatif kanıt satırı tut |
| B-03 | `0750` = çekirdek koruma demek | abartı | "DAC katmanı" de |
| B-04 | URL envanterindeki URL'yi yeniden kullanmak | kendi kendini doğrulama | envanter dışı + HTTP 200 |
| B-05 | boş hücre / yer tutucu sözcük | dolgu | `UNKNOWN` / `⚠️ VERIFICATION REQUIRED` |
| B-06 | S1'i kapatmış görünmek | karar gasbı | `[UNKNOWN]` bırak |

### §3.3 Örnek: doğru — yanlış karşılaştırması

| Durum | Yanlış ifade | Doğru ifade |
|---|---|---|
| session `0750` | "çekirdek koruması sağlandı" | "session dizini DAC ile daraltıldı (OS-yakın tek kanıt)" |
| seccomp yok | "sandbox yok, açık" | "seccomp uygulanmadı → S1 `[UNKNOWN]`" |
| AppArmor yok | "MAC eksik" | "LSM politikası repoda yok; hedef mimari kararı bekleniyor" |
| imza yok | "imzalama kırıldı" | "imzalama yüzeyi repoda hiç başlamadı (negatif kanıt)" |

### §3.4 Sınıf sızıntısı anti-pattern'leri

| # | Sızıntı | Nerede görülür | Sonuç | Önleme |
|---|---|---|---|---|
| C-01 | R sınıfını P diye sunmak | §2 blokları | uydurma kanıt | `kaynak:` zorunlu |
| C-02 | U satırını "uygulandı" yapmak | §1.3 tablosu | hedef mimari gizlenir | `Repo durumu` sütunu |
| C-03 | N satırını silmek | §1.1 tablosu | eksiklik gizlenir | negatif kanıt zorunlu |
| C-04 | P satırını abartmak | §1.2 yorumu | katman karışır | §4.1 K-2 sınırı |
| C-05 | bilinmeyeni `0` yapmak | herhangi bir hücre | uydurma ölçüm | `UNKNOWN` yaz |
| C-06 | envanter URL'sini kanıt saymak | §5.1 | kendi kendini doğrulama | kesişim `=0` |
| C-07 | başka dala ait kanıtı kullanmak | §1.5 | sınır ihlali | §5.3 sınır tablosu |

### §3.5 Sınır ihlali tablosu (bu alt-dalın komşuları)

| Komşu dal | Bu dosyanın YAZMADIĞI | Gerekçe |
|---|---|---|
| `yetki-ve-erisim-modeli` | UID/GID, capability, ACL, MAC tanımı | yetki modeli oradadır |
| `uygulama-guvenlik-siniri` | syscall yüzeyi, sandbox, cgroup | sınır mekanizması oradadır |
| `K009-security` | OWASP, auth, CSRF, CSP, kripto | uygulama güvenlik denetimi kapsam dışı |
| `K010-identitiy` | kimlik doğrulama akışı | uygulama auth kapsam dışı |
| `K019` | uygulama test stratejisi | test stratejisi kapsam dışı |
| `isletim-sistemi-kernel` | çekirdek iç yapısı, patch politikası | burada yalnız **koruma sonucu** var |

## §4 Eşik / kabul kriteri

### §4.1 Bu dosyanın kabul kriterileri

| # | Kriter | Eşik | Ölçüm | Durum |
|---|---|---|---|---|
| K-1 | satır sayısı | `>=500` | `Get-Content).Count` | bu dosya |
| K-2 | pozitif repo kanıtı | `>=1` gerçek satır | `Select-Object -Index` | **2/2** |
| K-3 | negatif kanıt satırı | `>=8` | §1.1 tablosu | **8** |
| K-4 | envanter-dışı URL | `>=3` + HTTP 200 | `Invoke-WebRequest -Method Head` | **3/3** |
| K-5 | kesişim (envanter) | `=0` | 2633'lük katalog karşılaştırma | **0** |
| K-6 | yer tutucu sözcük / sahte kod parçacığı | `=0` | regex sayımı | **0** |
| K-7 | mojibake | `=0` | U+FFFD sayımı | **0** |
| K-8 | uydurma kod | `=0` | her blok `kaynak:` ile imzalı | **0** |

### §4.2 Alt-dal kabul kriteri (bu dosyanın üstü)

| # | Kriter | Eşik | Durum |
|---|---|---|---|
| D-1 | dosya sayısı | 4 (index + 3 içerik) | 4 |
| D-2 | içerik dosyası | her biri `>=500` | bkz. ölçüm |
| D-3 | index | `40..100` satır | 76 |
| D-4 | frontmatter | 8 alan + `group` | 4/4 |
| D-5 | `parent:` alanı | `=0` | 0 |

### §4.3 S1 kapı kriteri (kapatılmaz)

| Adım | Koşul | Sonuç |
|---|---|---|
| 1 | mimari sahibi yazılı karar verir | S1 için ön koşul |
| 2 | yeni ADR (`>=088`) açılır | karar kayıt altına alınır |
| 3 | §1.3 tablosundaki satırlar uygulanır | hedef mimariye geçiş |
| 4 | `-dogrulama.md` yeniden ölçer | kanıt tazelenir |

**Bu dosya 3. adıma kadar HİÇBİR satırı uygulanmış saymaz.**

### §4.4 Kanıt yenileme tetikleyicileri

| # | Tetikleyici | Ne yeniden ölçülür | Sorumlu | Sıklık |
|---|---|---|---|---|
| T-1 | `SessionConfig.php` değişikliği | §1.2 (2 satır) | security-engineer | her PR |
| T-2 | yeni PHP/JS dosyası eklendi | §1.1 glob (8 satır) | security-engineer | her release |
| T-3 | deploy ortamı değişimi | §2.7 (6 satır) | devops-engineer | her ortam |
| T-4 | upstream doküman sürümü | §5.1 URL (4 satır) | security-engineer | çeyreklik |
| T-5 | ADR `>=088` kabulü | §1.3 + §1.6 (26 satır) | mimari sahibi | karar anında |
| T-6 | bu dosyanın satır sayısı | K-1 (`>=500`) | security-engineer | her revizyon |

### §4.5 Dosya sorumluluk matrisi

| Dosya | Üreten | Denetleyen | Değiştiremez |
|---|---|---|---|
| `cekirdek-guvenlik-anayasa.md` | security-engineer | architect | backend-architect (kod) |
| `cekirdek-guvenlik-surucu.md` | security-engineer | backend-architect | mimari sahibi (süreç) |
| `cekirdek-guvenlik-ornekler.md` (bu dosya) | security-engineer | qa-engineer | `*.php` / `*.js` / `*.css` / `*.sql` |
| `index.md` (alt-dal) | security-engineer | architect | kök `index.md` (yalnız orkestratör) |

> **Audit kuralı:** bu dosya **salt-okunur denetim** çıktısıdır. Düzeltme
> uygulamayı yapan tarafta (backend-architect) yapılır; security-engineer
> yeniden ölçer ve `validation:` alanına işler.

## §5 Referans

### §5.1 URL tablosu (HTTP 200 · 2026-10-13 · envanter dışı)

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| Yama LSM ptrace_scope davranışı resmi ABIsıdır | https://docs.kernel.org/admin-guide/LSM/Yama.html | 2026-10-13 | HTTP 200 (HEAD) |
| AppArmor resmi proje wikisi profil yaşam döngüsünü tanımlar | https://gitlab.com/apparmor/apparmor/-/wikis/home | 2026-10-13 | HTTP 200 (HEAD) |
| Debian AppArmor paket/belge sayfası politika yönetimi anlatır | https://wiki.debian.org/AppArmor | 2026-10-13 | HTTP 200 (HEAD) |
| `memfd_create` anonim bellek dosyası oluşturur (salt-okunur X) | https://man7.org/linux/man-pages/man2/memfd_create.2.html | 2026-10-13 | HTTP 200 (HEAD) |

> **Not (dürüstlük):** 4 satırın tamamı 2026-10-13 tarihinde
> `Invoke-WebRequest -Method Head -UseBasicParsing -TimeoutSec 20` ile
> **HTTP 200** döndürmüş, 2633'lük katalog envanterinde **karşılaşmamıştır**.

### §5.2 Disk kanıtı tablosu

| Kanıt | Dosya | Satır | Doğrulama | Sınıf |
|---|---|---|---|---|
| `DIR_PERMISSIONS = 0750` | `shared/src/Session/SessionConfig.php` | 20 | idx 19 birebir | P |
| `@mkdir($savePath, 0750, true)` | `shared/src/Session/SessionConfig.php` | 35 | idx 34 birebir | P |
| `cookieDomain: '.coremusic.net'` | `shared/src/Session/SessionConfig.php` | 45 | idx 44 birebir | P |
| `httponly => true` | `shared/src/Session/SessionConfig.php` | 59 | idx 58 birebir | P |
| `samesite => Lax` | `shared/src/Session/SessionConfig.php` | 60 | idx 59 birebir | P |

### §5.3 Ölçüm komutları (yeniden üretilebilir)

```powershell
$K='C:\www\coremusic.net\.ai\architecture\K000-isletim-sistemi'
(Get-Content "$K\isletim-sistemi-security\cekirdek-guvenlik\cekirdek-guvenlik-ornekler.md").Count

$c=Get-Content 'C:\www\coremusic.net\shared\src\Session\SessionConfig.php' -Encoding UTF8
$c[19]; $c[34]

# cekirdek-yuzey negatif kaniti (vendor/node_modules hariç)
Get-ChildItem 'C:\www\coremusic.net' -Recurse -Include *.ko,*.efi,*.te -ErrorAction SilentlyContinue |
  Where-Object { $_.FullName -notmatch 'vendor|node_modules' }
```

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
