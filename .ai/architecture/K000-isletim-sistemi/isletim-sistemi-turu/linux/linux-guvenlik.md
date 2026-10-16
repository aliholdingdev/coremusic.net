---
title: "Linux — İşletim Sistemi Güvenlik Profili"
type: architecture
node_id: "K000-isletim-sistemi/isletim-sistemi-turu/linux/linux-guvenlik"
node_type: security-profile
status: active
date: 2026-10-15
parent_id: "K000-isletim-sistemi/isletim-sistemi-turu/linux"
children: []
depends_on:
  - "K000-isletim-sistemi/isletim-sistemi-security"
  - "K009-security"
evidence:
  - ".github/workflows/ci.yml:14-15,27,69,112,144,163,180 (permissions + 6× ubuntu-latest — okundu)"
  - "home.coremusic.net/AGENTS.md:19 (Raspberry Pi 5 üretim hedefi — okundu)"
  - "shared/src/Component/TemplateEngine.php:73 (DIRECTORY_SEPARATOR — okundu)"
  - "shared/src/Device/DeviceDetector.php:29 (Raspberry Pi / aarch64 / arm keywords — okundu)"
  - "grep seccomp|landlock|no_new_privs (shared/) → 0 isabet (2026-10-15)"
validation: "2026-10-15 — 7 vendor URL erişildi (HTTP 200); repo iddiaları DOSYA:SATIR kanıtlı; negatif iddialar grep ile doğrulandı"
open_questions:
  - "Raspberry Pi 5 üretim hedefinde hangi distro/servis yöneticisi kullanılıyor? — UNKNOWN (systemd varsayımı kanıtlanmadı)"
  - "CI ubuntu-latest runner'ı hardening politikası (seccomp/LSM) uyguluyor mu? — ⚠️ VERIFICATION REQUIRED"
  - "Neva Engine Linux backend'i için Landlock/seccomp sandbox tasarımı var mı? — PLANNED (kod kanıtı yok)"
group: mimari
---

# Linux — İşletim Sistemi Güvenlik Profili

> **Kapsam:** Bu dosya, Linux'un işletim sistemi güvenlik modelini (LSM/SELinux/AppArmor,
> seccomp-BPF, no_new_privs, modül imzalama, Landlock, systemd güvenlik raporlaması)
> tanımlar ve CoreMusic deposundaki izlerini DOSYA:SATIR kanıtıyla bağlar. Uygulama
> seviyesi güvenlik kontrolleri (CSRF, auth, session) **K009**'a aittir — bkz §4.

---

## §0 — ELI15 (Explain Like I'm 15)

Linux, bilgisayarın donanımını yöneten çekirdeğin (kernel) adıdır; "Linux" denince çoğu
kullanıcının gördüğü şey, çekirdek + GNU araçları + dağıtım (distro) birleşimidir. Güvenlik
fikri basit bir soruya dayanır: **bir süreç, ihtiyacı olmayan hiçbir şeye dokunamamalıdır.**

Linux bunu beş katmanda yapar:

1. **Kurallar kodla değil, etiketle zorlanır (LSM).** Linux Security Module (LSM) çerçevesi,
   çekirdeğe güvenlik kancaları (hook) ekler. SELinux, AppArmor, Smack, TOMOYO gibi
   *mandatory access control* (MAC) uzantıları, "bu etiket şu etikete şunu yapabilir"
   kurallarını *default denial* (varsayılan ret) ile uygular. Kuralda yazmayan her şey yasaktır.
2. **Süreçlerin kullanabileceği syscall yüzeyi kırpılır (seccomp-BPF).** Birçok uygulama
   ömrü boyunca yüzlerce sistem çağrısının hiçbirisini kullanmaz. seccomp, gelen
   sistem çağrılarını *Berkeley Packet Filter* (BPF) programıyla filtreler; böylece çekirdeğe
   açılan saldırı yüzeyi küçülür. (Dikkat: seccomp tek başına bir *sandbox* değildir — §1.2.)
3. **Ayrıcalık yükseltmesi kilitlenir (no_new_privs).** `no_new_privs` bayrağı bir kez
   açıldığında `execve` setuid/setgid ve dosya yeteneklerini (file capabilities) artık
   ayrıcalık kazandırmaz; bayrak fork/clone/execve boyunca miras alınır ve geri alınamaz.
4. **Çekirdek modülleri imzayla tanınır (module signing).** `CONFIG_MODULE_SIG` ile modüller
   kurulumda kriptografik olarak imzalanır, yüklenmede imza doğrulanır; imzasız veya
   geçersiz anahtarla imzalı modül yüklemez (özellikle `CONFIG_MODULE_SIG_FORCE` ile).
5. **Kendini kısıtlayan sandbox (Landlock).** Landlock, *stackable* bir LSM'dir; ayrıcalıksız
   (unprivileged) her süreç bile kendi dosya sistemi ve ağ erişimini kısıtlayabilir. Bir kez
   kısıtlandığında politika geri alınamaz, yalnızca sıkılaştırılabilir.

Buna üstte **systemd güvenlik raporlama kanalları** (GitHub Security Advisory /
systemd-security@redhat.com) ve dağıtım bazlı yama dağıtımı eklenir.

CoreMusic açısından anlamı: Linux, bu mimarinin **çalıştığı üretim ortamıdır** —
`home.coremusic.net` Raspberry Pi 5 (Linux/ARM sınıfı donanım) üzerinde çalışır (§2.1).
CoreMusic backend'i Linux üzerinde koşar; OS'nin sandbox/MAC katmanı işletim sistemine
devredilmiştir, uygulamanın kendi yükümlülükleri §4/K009 sınırındadır.

---

## §1 — OS Güvenlik Profili

### 1.1 Temel katmanlar

| Katman | Mekanizma | Ne yapar | Kaynak |
|---|---|---|---|
| MAC | **LSM çerçevesi (SELinux, AppArmor, Smack, TOMOYO, Yama, Landlock…)** | Çekirdeğe güvenlik hook'ları ekler; aktif modüller `/sys/kernel/security/lsm` listesinde, yetenek modülü hep en başta | §5 #1 · #2 |
| Syscall | **seccomp-BPF** | Gelen syscall'ları BPF programıyla filtreler; syscall numarası + argümanlarına göre `SECCOMP_RET_*` kararı döner | §5 #3 |
| Privilege | **no_new_privs** | `execve` ile setuid/setgid/fcap ayrıcalık kazandırmaz; fork/clone/execve ile miras, geri alınamaz | §5 #4 |
| Module | **Module signing (CONFIG_MODULE_SIG)** | Modüller X.509 + RSA/ECDSA/ML-DSA ile imzalanır; yüklenmede kernel doğrular; `CONFIG_MODULE_SIG_FORCE` restrictive mod | §5 #5 |
| Sandbox | **Landlock (stackable LSM)** | Ayrıcalıksız süreç bile dosya sistemi + ağ (TCP/UDP port) erişimini kısıtlayabilir; politika katmanlı, geri alınamaz yalnız sıkılaştırılabilir | §5 #6 |
| Ops | **systemd güvenlik raporlaması** | Güvenlik açığı GitHub Security Advisory (tercih) veya systemd-security@redhat.com ile gizli bildirilir; dağıtımlarla koordineli | §5 #7 |

### 1.2 Güvenlik modelinin varsayılanları

- **Default denial (MAC):** LSM policy'de açıkça izin verilmeyen her erişim reddedilir;
  root bile SELinux Type Enforcement'ta policy'de olmayanı yapamaz.
- **Capabilities yerine tam root yok:** Linux yetenekleri (capabilities) ince ayarlıdır;
  LSM'ler "minor" (ör. Yama) + en fazla bir "major" (ör. SELinux) modülünden oluşabilir.
- **seccomp ≠ sandbox:** Kernel dokümanı açıkça der ki seccomp "clearly defined mechanism
  for minimizing the exposed kernel surface"dir; politika ve bilgi akışı LSM + diğer
  sertleştirme teknikleriyle yönetilmelidir (§5 #3).
- **Best-effort sandbox (Landlock):** ABI sürümü çalışma zamanında kontrol edilir; desteklenmeyen
  haklar bilinçli olarak düşürülür (backward/forward compatibility).
- **Defense in depth:** LSM + seccomp + no_new_privs + modül imzalama + Landlock üst üste
  bindirilmiştir; tek katman ihlali sistemi düşürmez.

### 1.3 Bilinen yapısal riskler (genel mühendislik kabulü)

- Impersonation/TOCTOU: seccomp BPF programları pointer de-reference edemez; syscall
  argümanları tracee belleğinden okunurken dikkat edilmelidir (TOCTOU, §5 #3).
- no_new_privs, `execve` içermeyen ayrıcalık değişimlerini (setuid(2), SCM_RIGHTS) engellemez.
- Landlock, mount/pivot_root ile dosya sistemi topolojisi değişimini kısıtlar ama `chroot(2)`
  reddetmez; `/proc/<pid>/fd/*` ve nsfs gibi özel dosyalar açıkça kısıtlanamaz (§5 #6).

> ⚠️ **VERIFICATION REQUIRED:** Bu risk maddeleri bu oturumda ilgili kernel dokümanından
> birebir alıntıyla desteklenmiştir; ancak *CoreMusic'e özgü* risk değerlendirmesi bu dosyada
> yapılmamıştır (kod yüzeyinde Linux-specific sandbox yok, §2.4).

---

## §2 — Repo / CoreMusic İzi (DOSYA:SATIR kanıtlı)

> Bu bölümdeki her satır bu oturumda **okunmuş** gerçek dosya satırıdır. Okunmayan iddia yazılmaz.

### 2.1 Üretim hedefi — Raspberry Pi 5 (Linux/ARM)

| İddia | Kanıt | Durum |
|---|---|---|
| Home Media Center Raspberry Pi 5 üzerinde çalışır | `home.coremusic.net/AGENTS.md:19` → "Home Media Center — Raspberry Pi 5 üzerinde çalışan ana medya paneli." | ✅ OKUNDU |
| Cihaz tespiti Raspberry keywords'ü tanır | `shared/src/Device/DeviceDetector.php:29` → `'Raspberry Pi', 'RPi', 'aarch64', 'armv7', 'armv8', 'armv6', 'CrOS', 'kiosk', 'Raspbian', 'Embedded'` | ✅ OKUNDU |

**Dürüst sınırlama:** Raspberry Pi 5, Linux/ARM sınıfı bir donanımdır; bu satır *donanım*
hedefini kanıtlar. Kullanılan **distro/servis yöneticisi** (systemd?) repo'da yazılmamıştır
→ `UNKNOWN` (§6).

### 2.2 CI — Linux runner (tüm job'lar)

`.github/workflows/ci.yml:14-15` (okundu) → `permissions:` / `contents: read`
(least-privilege GITHUB_TOKEN). Job envanteri (okundu): `runs-on: ubuntu-latest` → L27 ·
L69 · L112 · L144 · L163 · L180 → **6 job, hepsi ubuntu-latest**.

- CoreMusic CI/CD ve test altyapısı **Linux üzerinde** koşar.
- `.github/workflows/` envanteri (glob): `ci.yml` + `secret-scan.yml` (2 dosya).

### 2.3 Taşınabilirlik izi — path separator

`shared/src/Component/TemplateEngine.php:73` (okundu) →
`$cachePath = rtrim(sys_get_temp_dir(), "\\/") . DIRECTORY_SEPARATOR . 'cmtpl_' . bin2hex(random_bytes(8)) . '.php';`

- `DIRECTORY_SEPARATOR` + `rtrim(..., "\\/")` — cross-platform (Windows `\` / Linux/macOS `/`)
  path birleştirmesi. Bu, kodun Linux hedefini *varsaymadığını* ama desteklediğini gösterir.

### 2.4 Dürüst negatif kanıtlar (2026-10-15 grep)

| Arama (`shared/`) | Sonuç |
|---|---|
| `seccomp` | **0 isabet** |
| `landlock` / `landlock_restrict` | **0 isabet** |
| `no_new_privs` | **0 isabet** |
| Linux'a özgü sandbox kodu (seccomp filter, Landlock ruleset, LSM policy) | **0 isabet** |

**Sonuç:** CoreMusic, Linux'u *production hedefi* olarak kullanır (Raspberry Pi 5 + CI
ubuntu-latest) ama Linux'a özgü **hiçbir** sandbox/MAC mekanizmasını (seccomp, Landlock,
modül imzalama) kendi kodunda uygulamaz. OS güvenliği tamamen platforma devredilmiştir;
CoreMusic'in sorumluluğu §4/K009 sınırındadır. Neva Engine Linux backend'i için Landlock/
seccomp tasarımı **PLANNED** (kod kanıtı yok).

---

## §3 — K000 Sınırı → `isletim-sistemi-security` BRIDGE

Bu dosya K000 (`isletim-sistemi-turu`) dalındadır; **OS kartı güvenlik detayları**
`isletim-sistemi-security` dalındadır. Aşağıdaki kartlar diskte mevcuttur (envanter
`isletim-sistemi-security/index.md` + glob üzerinden doğrulanmıştır, 2026-10-15):

| Alt dal | Linux kartı | Bağlantı |
|---|---|---|
| çekirdek-guvenlik | `cekirdek-guvenlik-linux.md` | [`../../isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-linux.md`](../../isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-linux.md) |
| yetki-ve-erisim-modeli | `yetki-ve-erisim-modeli-linux.md` | [`../../isletim-sistemi-security/yetki-ve-erisim-modeli/yetki-ve-erisim-modeli-linux.md`](../../isletim-sistemi-security/yetki-ve-erisim-modeli/yetki-ve-erisim-modeli-linux.md) |
| uygulama-guvenlik-siniri | `uygulama-guvenlik-siniri-linux.md` | [`../../isletim-sistemi-security/uygulama-guvenlik-siniri/uygulama-guvenlik-siniri-linux.md`](../../isletim-sistemi-security/uygulama-guvenlik-siniri/uygulama-guvenlik-siniri-linux.md) |

**Sınır kuralı:** Bu dosya yalnızca *Linux OS'nin* güvenlik modelini anlatır. CoreMusic
uygulamasının Linux'a karşı yükümlülükleri (token saklama, session hardening, HTTPS)
**bu dosyada çözülmez** → §4.

**Ne bu dosya devralır / ne devretmez:**

- **Devralır:** LSM/SELinux, seccomp-BPF, no_new_privs, modül imzalama, Landlock —
  bunlar *Linux'un* güvenlik kararlarıdır.
- **Devretmez:** CoreMusic backend'inin Linux sunucuda *kendi* uygulama güvenliği (auth,
  session, rate limit) K009 alanıdır; bu dosya yalnızca OS katmanını bağlar.

Ayrıca bu dalın kardeşleri: `[windows](../windows/index.md)` · `[macos](../macos/index.md)` ·
`[ios](../ios/index.md)` · `[android](../android/index.md)` · parent: `[index](../index.md)`.

---

## §4 — K009 Sınırı (Uygulama Güvenliği)

K009 = `K009-security` — CoreMusic uygulama güvenliği (auth, CSRF, CSP, session, rate limit,
Argon2id, AES-256-GCM). Sınır hedefi: [`../../../K009-security/index.md`](../../../K009-security/index.md).

- **Ne bu dosyada, ne K009'da:** OS kernel içi güvenlik politikaları (LSM policy, seccomp
  filter, modül imzalama, Landlock ruleset) → bunlar K000/isletim-sistemi-security alanı.
- **Ne K009'da, ne bu dosyada:** Uygulamanın Linux sunucuda *nasıl* güvendiği (TLS, token,
  cookie flags, PHP session hardening) → K009 alanı.

**Geçiş noktası (Linux-özel):**

| Soru | Sahip | Not |
|---|---|---|
| CI runner (ubuntu-latest) hardening politikası var mı? | DevOps + K009 | §2.2'de 6 job kanıtlandı; runner'ın LSM/seccomp yapılandırması repo'da yok → `⚠️ VERIFICATION REQUIRED` |
| Raspberry Pi 5 üretim ortamı hangi distro/servis yöneticisi? | DevOps | §2.1'de donanım kanıtlandı; distro `UNKNOWN` (§6) |
| Neva Engine Linux backend'i sandbox tasarımı | K000 (embedded) | Landlock/seccomp **PLANNED**; grep 0 isabet → kod kanıtı yok |

---

## §5 — Kaynaklar (bu oturumda erişilmiş · 2026-10-15)

> Tümü bu oturumda **erişilmiş** ve içerik döndürmüştür. Generic OWASP/NIST/CIS
> referansı **kullanılmamıştır** (görev kısıtı). 7 benzersiz URL.

| # | URL | Ne verdi | Durum |
|---|---|---|---|
| 1 | https://docs.kernel.org/security/index.html | Security Documentation indeksi — Credentials, IMA, Kernel Keys, LSM hooks, Kernel Self-Protection, TPM, Landlock, IPE | ✅ HTTP 200 |
| 2 | https://docs.kernel.org/admin-guide/LSM/index.html | LSM Usage — MAC extensions (SELinux/Smack/Tomoyo/AppArmor), capability + minor + tek major modül sırası, `/sys/kernel/security/lsm` | ✅ HTTP 200 |
| 3 | https://docs.kernel.org/userspace-api/seccomp_filter.html | Seccomp BPF — BPF filtreleme, `SECCOMP_RET_*` dönüş değerleri, TOCTOU, "seccomp isn't a sandbox", pitfall: arch kontrolü | ✅ HTTP 200 |
| 4 | https://docs.kernel.org/userspace-api/no_new_privs.html | No New Privileges — `prctl(PR_SET_NO_NEW_PRIVS)`, setuid/fcap engeli, fork/clone/execve mirası, seccomp mode 2 ön koşulu | ✅ HTTP 200 |
| 5 | https://docs.kernel.org/admin-guide/module-signing.html | Module signing — X.509, RSA/ECDSA/ML-DSA, `CONFIG_MODULE_SIG_FORCE` (permissive/restrictive), `.builtin_trusted_keys`, imzalı modül striplenemez | ✅ HTTP 200 |
| 6 | https://docs.kernel.org/userspace-api/landlock.html | Landlock — stackable LSM, filesystem + network (TCP/UDP) rules, ABI best-effort, `landlock_restrict_self`, ptrace/IPC scoping, 16 katman limiti | ✅ HTTP 200 |
| 7 | https://systemd.io/SECURITY/ | systemd güvenlik raporlama — GitHub Security Advisory (tercih) / systemd-security@redhat.com, geçerli rapor gereksinimleri | ✅ HTTP 200 |

**Elenen/askıya alınan iddialar (dürüst kayıt):**
- `docs.kernel.org/admin-guide/sysctl/kernel.html` → bu oturumda **erişilmedi**; §5'e
  alınmadı (yerine 7 doğrulanmış URL kullanıldı).
- Distro/servis yöneticisi (systemd) iddiası → systemd.io sayfası yalnızca *raporlama*
  kanallarını verir; CoreMusic'in systemd kullandığı **kanıtlanmadı** → `UNKNOWN`.

---

## §6 — Açık Sorular

1. **Raspberry Pi 5 distro/servis yöneticisi:** Üretim ortamı hangi dağıtımla (Raspbian/
   Ubuntu Server/diğer) ve systemd ile mi çalışıyor? Repo'da kanıt yok → `UNKNOWN`.
2. **CI runner hardening:** `ubuntu-latest` runner'larında LSM (AppArmor/SELinux) veya
   seccomp kısıtı uygulanıyor mu? Repo'da policy yok → `⚠️ VERIFICATION REQUIRED`.
3. **Neva Engine Linux sandbox:** Landlock/seccomp tabanlı sandbox tasarımı PLANNED;
   hangi syscall yüzeyi kısılacak, hangi dosya sistemi erişimine izin verilecek? → Embedded.
4. **Yama/kernel güncelleme kanalı:** Raspberry Pi 5'te kernel/security güncellemeleri nasıl
   dağıtılıyor (otomatik unattended-upgrades mi)? → `⚠️ VERIFICATION REQUIRED`.
5. **Module signing zorunluluğu:** Üretim kernel'i `CONFIG_MODULE_SIG_FORCE` ile derlenmiş
   mi (imzasız modül reddi)? Bilinmiyor → `UNKNOWN`.
6. **auditd/eBPF denetimi:** Üretim ortamında syscall audit (auditd) veya eBPF-tabanlı
   denetim (bpftrace/Tracee) kurulacak mı? Repo'da kanıt yok → `UNKNOWN`.
7. **Disk şifreleme (LUKS/dm-crypt):** Raspberry Pi 5 SD/SSD üzerinde at-rest şifreleme
   kullanılıyor mu? Kanıt yok → `UNKNOWN`.

### §6.1 — Linux güvenlik yüzeyi özet matrisi (bu dosyanın hızlı referansı)

| Kontrol | Ne korur | Repo izi | Durum |
|---|---|---|---|
| LSM (AppArmor/SELinux) | yetki daraltma (MAC) | policy dosyası yok | `UNKNOWN` |
| seccomp + no_new_privs | syscall yüzeyi daraltma | grep 0 (Neva Engine PLANNED) | PLANNED |
| Landlock | dosya sistemi + network sandbox | grep 0 | PLANNED |
| Module signing (`CONFIG_MODULE_SIG_FORCE`) | yetkisiz çekirdek modülü engeli | grep 0 | `UNKNOWN` |
| auditd / eBPF audit | syscall denetim izi | grep 0 | `UNKNOWN` |
| LUKS / dm-crypt | at-rest disk şifreleme | kanıt yok | `UNKNOWN` |
| unattended-upgrades | otomatik güvenlik yaması | kanıt yok | `UNKNOWN` |
| CI runner hardening | supply-chain runner güvenliği | `ci.yml` ubuntu-latest, policy yok | `⚠️ VERIFICATION REQUIRED` |

Matrisin tamamı `UNKNOWN` ya da `PLANNED` — Linux güvenlik yüzeyi bu repoda
**uygulamaya konulmamıştır**. §2'deki tek olumlu kanıtlar: `home.coremusic.net/AGENTS.md:19`
(Raspberry Pi 5 hedefi) ve `ci.yml` L27·L69·L112·L144·L163·L180'deki 6 ubuntu-latest
runner (build/test işi, güvenlik kontrolü değil). Gerçek Linux güvenlik kontrolü
(seccomp/Landlock sandbox) yazıldığında bu matris satırları grep kanıtıyla doldurulacaktır.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
