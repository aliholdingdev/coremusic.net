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

## §7 Çapraz kart matrisi (bu dal — 7 OS güvenlik kartı)

Bu kart `isletim-sistemi-turu` dalının **güvenlik** kartıdır ve dalın **tek
üretim-hedefi kartıdır** (§2.1: Raspberry Pi 5, Linux/ARM — bu nedenle §2'deki
repo izi yedi kart içinde **en güçlüsüdür**: §2.1 üretim hedefi + §2.2 CI Linux
runner tüm job'larda). Aşağıdaki matris yedi güvenlik kartını listeler.

| # | Kart | Odak (dosyanın §1 başlığı) | Repo izi (§2) | Bu kartla ilişki |
|---|------|---------------------------|----------------|------------------|
| 1 | `android-guvenlik` | Linux + SELinux + permission | UA + enum + CI | ailedeş (Linux çekirdek) |
| 2 | `ios-guvenlik` | XNU + sandbox/Mach | Boşluk matrisi | uzak aile |
| 3 | **`linux-guvenlik` (bu kart)** | **Linux capabilities/LSM** | **Üretim hedefi + CI (§2.1–§2.2)** | — |
| 4 | `macos-guvenlik` | XNU + Endpoint Security | Dürüst negatif | uzak aile |
| 5 | `reactos-guvenlik` | NT Security Manager | YOK | uzak (NT) |
| 6 | `tizen-guvenlik` | Linux + privilege manifest | UA, enum YOK | ailedeş (Linux) |
| 7 | `webos-guvenlik` | Linux + ACG/role/groups | UA, enum YOK | ailedeş (Linux) |
| 8 | `windows-guvenlik` | NT token/ACL/MIC + IIS/Apache | Yapılandırma | uzak (NT) |

**Ayrışma notu:** Bu kart **dalın referans kartıdır** — §2.1 üretim hedefi
doğrudan Linux olduğundan "repo izi" bu kartta **dürüst negatif değil, pozitif**
işaretlidir. Diğer altı kart (tizen/webos hariç) Linux'a **referansla** bakar.

## §8 Ses/RT PLANNED senaryo (bu kartın güvenlik açısı)

Bu bölüm Linux'a özgü **yeni teknik iddia içermez**; yalnız güvenlik
katmanının CoreMusic PLANNED ses/RT yığınıyla **kesişeceği** noktayı gösterir.
Birincil kaynak salt-okunur: `.ai/electronic/drivers/audio-drivers.md` (709 satır).

Linux'ta ses erişimi **capabilities + LSM (SELinux/AppArmor) + grup** ile
korunur (ör. `audio` grubu) — ancak **hangi kuralın hangi ses yolunu açtığı**
bu dosyada kanıtlanmaz → **`⚠️ VERIFICATION REQUIRED`**.

| RT katmanı (kaynak §) | Linux güvenlik açısı | Bu kartta kanıtlı mı? | Durum |
|----------------------|----------------------|------------------------|-------|
| ALSA çekirdek yolu (L365) | `/dev/snd/*` erişimi grup/capability | Hayır — audio policy kanıtı yok | **PLANNED** |
| Kernel sürücü/DMA (L32, L44) | çekirdek uzayı register erişimi | Hayır | **PLANNED** |
| SCHED_FIFO / IRQ affinity (L690–691) | RT önceliği için capability | Hayır | **PLANNED** |
| Exclusive lock (L455) | İkinci uygulama açamaz | Hayır | **PLANNED** |

**Kesişim kararı (bu kart):** Linux, ses/RT'nin **gerçek hedefidir** (RPi5
üretim hedefi — §2.1), **ama bu kart o kararı vermez**; güvenlik/RT kararları
`isletim-sistemi-audio` + `isletim-sistemi-security` dallarında alınır. Bu kart
yalnız **güvenlik katmanı girdisini** taşır.

## §9 Başarısızlık modları (bu kartın kapsamı)

Modlar **bu dosyanın yanlış çıkma biçimleridir**; hiçbir "Linux'ta şu çalışmıyor"
**iddiası değildir** — "bu kart kanıtlamıyor" ifadesidir (zero-hallucination).

| # | Başarısızlık modu | Tetikleyici | Etki | Bağlantı |
|---|-------------------|-------------|------|----------|
| F1 | Üretim hedefi izinin güvenlik doğrulaması sanılması | §2.1/§2.2 pozitif izlerin "güvenlik doğrulandı" sanılması | Yanlış güvenlik garantisi | §2.1, §17 |
| F2 | Dürüst negatifin tersine çevrilmesi | §2.4'teki "YOK" kanıtlarının VAR sanılması | Yanlış envanter | §2.4 |
| F3 | Bridge sorumluluk karıştırılması | §3 devir/devralmanın ters yorumu | Sınır ihlali | §3, §12 |
| F4 | S1/S2 erken kapatılması | §6 `[UNKNOWN]` soruları cevaplanmış varsayılırsa | Kanıtsız mimari karar | §6 |
| F5 | capabilities/LSM abartısı | §1.1–§1.3 katmanlarının "her şeyi koruduğu" sanılması | Yanlış varsayım | §1.1, §1.3 |

## §9.1 Fail-safe sırası (P0 > P1 > P2)

1. **P0 — Durdur ve işaretle:** İddia kanıtsızsa satır `⚠️ VERIFICATION REQUIRED`
   kalır; capability/LSM adı **uydurulmaz**.
2. **P1 — Daralt:** "Güvenlik doğrulandı" → "§2.1 üretim hedefi + §2.2 CI izi mevcut".
3. **P2 — Delege et:** Karar mimari sahibine; §6 açık soruları yeni ADR
   (`>=088`) ile kapatılır (bu dosya kapatmaz).

## §10 Kullanım senaryoları (bu kart kimler için)

| Senaryo | Kullanılır mı? | Bölüm | Not |
|---------|----------------|-------|-----|
| Linux güvenlik katmanı özeti | Evet | §1.1–§1.3 | Temel katman + varsayılan + risk |
| Üretim hedefi izi | Evet | §2.1, §2.2 | Pozitif iz (RPi5 + CI) |
| Hızlı referans özeti | Evet | §6.1 | Özet matrisi |
| K000→security sınırı | Evet | §3 | Bridge |
| K009 sınırı | Evet | §4 | Devredilmez |
| Ses/RT kararı | **Hayır** | §8 | `isletim-sistemi-audio`'ya delege |
| S1/S2 kapatma | **Hayır** | §6 | Mimari sahibi + ADR |

## §11 Kaynakça envanteri (bu kartta atıf yapılan yüzeyler)

| Kaynak türü | Durum | Kanıt biçimi |
|-------------|-------|--------------|
| §1 güvenlik profili | Dosyanın kendi §1'i | §5 vendor/general envanteri |
| §2 repo izi | §2.1–§2.4 | `Dosya:` + `Satır:` (dosyada belgelendi) |
| §6.1 özet matrisi | Dosyanın kendi §6.1'i | Hızlı referans tablosu |
| §5 Kaynaklar | 2026-10-15 HTTP 200 | §5'te tarihli envanter |
| `.ai/electronic/drivers/audio-drivers.md` | §8 salt-okunur girdi | `Dosya: ...` (709 satır) |
| CVE / çalışan Linux çekirdek kanıtı | **YOK** | CVE iddiası yok (§5/§4) |

## §12 Eşleme ağacı (bu kart nerede duruyor)

```
K000-isletim-sistemi
├── isletim-sistemi-turu
│   └── linux
│       └── linux-guvenlik  ← bu kart (dal referansı)
└── isletim-sistemi-security
    ├── cekirdek-guvenlik/linux       (kardeş dal)
    └── uygulama-guvenlik-siniri/linux (bridge hedefi, §3)
```

**Bağımlılık yönü:** Bu kart yalnız kendi §0–§6'sını §7–§19'da yapılandırır;
bridge kartlarına **veri üretmez**.

## §13 Test + negatif + okuma sırası

### §13.1 Olumlu test
- §2.1–§2.4 repo izleri **`Dosya:`/`Satır:`** ile destekli.
- §3 bridge §4 K009 sınırıyla **tutarlı**.
- §5 kaynakları **2026-10-15 HTTP 200**.
- §6.1 özet matrisi §1/§2 ile **tutarlı**.

### §13.2 Negatif test

| Reddedilen varsayım | Neden reddedilir | Kanıt |
|---------------------|------------------|-------|
| "Üretim hedefi izi = güvenlik doğrulaması" | §2.1/§2.2 yalnız hedef/CI izi | §9 F1 |
| "§2.4'teki YOKlar VAR" | Dürüst negatif kanıt | §9 F2 |
| "S1/S2 kapatılabilir" | Kapatma yasağı | §6 |

### §13.3 Okuma sırası
`§0 → §1 → §2 → §3 → §4 → §5 → §6 → §6.1 → §7 → §9 → §17`
(kapı: §6/§17 görülmeden §8/§10'a geçilmez).

## §14 Olay şeması / eskalasyon

| Tetikleyici | Olay | Eskalasyon |
|-------------|------|------------|
| Linux sürüm/LSM değişimi | §1.1–§1.3 eskir | Vault revizyonu |
| §5 kaynaklarından biri 404 | Kaynak envanteri eskir | URL yenileme + tarih |
| Kardeş dal güncellenirse | §7/§12 eşleşmesi | Kardeş senkronu |
| S1/S2 kararı | §6 kapanır | Yeni ADR + `validation:` |
| Ses/RT hedefi | §8 PLANNED | `isletim-sistemi-audio` girdisi |

## §15 Append protokolü (bu kart nasıl büyüdü)

Append-only: §0–§6 (§6.1 dahil) **değiştirilmedi**; yeni bölümler footer
öncesine eklendi. Frontmatter ve Authority **dokunulmaz**.

| Bölüm | Kaynak | Uydurma riski |
|-------|--------|---------------|
| §7 | Dosyanın §1/§2 başlıkları + kardeş adlar | Düşük |
| §8 | `audio-drivers.md` (salt-okunur) | Düşük (atıflı) |
| §9–§14 | Dosya-içi atıflar | Düşük |
| §15–§23 | Protokol + kapı + kontrol | Düşük |

## §16 Kapı özeti (8 soru — bu kart neyi KAPATMAZ)

| # | Kapı sorusu | Durum |
|---|-------------|-------|
| 1 | Linux güvenlik katmanı repo'da doğrulandı mı? | **Kısmen** — §2.1/§2.2 pozitif hedef/CI izi, güvenlik doğrulaması değil |
| 2 | capability/LSM politikaları uygulanıyor mu? | **UNKNOWN** — bu dosya kanıtlamaz |
| 3 | Bridge kartları hazır mı? | §3'e kadar — **tanım** var |
| 4 | S1 (OS güvenlik katmanı) | `[UNKNOWN]` — kapatılamaz (§6) |
| 5 | S2 (uygulama↔OS sınırı) | `[UNKNOWN]` — kapatılamaz (§6) |
| 6 | Linux üretim hedefi mi? | **Evet (hedef)** — §2.1; ama güvenlik kararı ayrı (§10) |
| 7 | Ses/RT kararı buradan mı? | **Hayır** — §8 |
| 8 | CVE iddiası var mı? | **Hayır** — §5 |

## §17 Doğrulama notu (negatif hafıza)

1. **Çalışan LSM/capability doğrulaması yok** — §2.1/§2.2 hedef/CI izidir.
2. **CVE/versiyon iddiası yok** — sürüm kanıtı `UNKNOWN`.
3. **S1/S2 kapalı değil** (§6).
4. **§2.4'teki "YOK"lar düzeltilemez** — dürüst negatif kanıt.
5. **Ses/RT kesişimi PLANNED** — mevcut değil (§8).

## §18 Negatif hafıza ("yapma" listesi)

- Bu karta CVE **ekleme** (§5/§4).
- §2.1 üretim hedefi izini "güvenlik doğrulaması" **diye yazma** (§9 F1).
- §2.4 YOK kanıtlarını **tersine çevirme** (§9 F2).
- S1/S2'yi **kapatma** (§6).
- §8'i "mevcut" **diye sunma** (PLANNED).

## §19 Tur kapanış kontrolü (bu kart)

- [x] Kapı ihlali: 0 · [x] Beyazliste dışı: 0 · [x] `git commit`: yok
- [x] Ölçüm: ≥500 (append-only) · [x] Frontmatter/Authority: korundu

## §20 Kardeş ağaç karşılaştırması (turu vs security dalı — Linux)

| Soru | `cekirdek-guvenlik/linux` | `uygulama-guvenlik-siniri/linux` | **bu kart** |
|------|----------------------------|------------------------------------|-------------|
| Ne soruyor? | Ne korunuyor (çekirdek)? | Uygulama↔OS sınırı ne? | Tür+OS profili ne? |
| Birim | LSM/sürücü | Sınır tanımı | Katman + hedef/CI izi |
| Kanıt | Çekirdek yapılandırma | Sınır kartı | §2.1–§2.4 (DOSYA:SATIR) |
| S1/S2 | Aynı `[UNKNOWN]` kapıları | Aynı | Aynı (§6) |

**Kural:** Üç kart kapıları paylaşır; biri diğerini **kapatmaz**.

## §21 Uzun vadeli Revision Planı (bu kartın büyüme yolu)

| Tur | Eklenecek | Kaynak | Kapı |
|-----|-----------|--------|------|
| R1 | §2'ye yeni repo izi | Repo taraması | S1/S2 kapalı değil |
| R2 | §5 kaynaklarının yenilenmesi | HTTP doğrulama | — |
| R3 | §1'de sürüm güncellemesi | Vendor docs | — |
| R4 | S1/S2 kapatma | Yeni ADR (`>=088`) | Mimari sahibi onayı |
| R5 | §8 ses/RT kesişimi | `isletim-sistemi-audio` + `security` | PLANNED → mevcut |

**Revision kuralı:** Yeni bölüm yalnız footer öncesine eklenir; §0–§6.1
**değiştirilmez**. Kanıt gelmeden §17 boşlukları **doldurulmaz**.

## §22 Tur kapanış kayıt cetveli (bu kart — kesin son)

| # | Kontrol | Sonuç | Kanıt |
|---|---------|-------|-------|
| 1 | Frontmatter korundu mu? | Evet | `node_id`/`status` değişmedi |
| 2 | §0–§6.1 değiştirildi mi? | **Hayır** | Append-only |
| 3 | Authority footer korundu mu? | Evet | Dosyanın son satırı |
| 4 | S1/S2 kapatıldı mı? | **Hayır** | §6 `[UNKNOWN]` kaldı |
| 5 | Uydurma CVE/capability eklendi mi? | **Hayır** | §5 + §17 |
| 6 | Beyazliste dışı yazım | 0 | Yalnız K000 |
| 7 | `git commit` | Yok | Orkestratöre ait |
| 8 | Satır ölçümü | ≥500 | Append-only |

### §22.1 Bu kartın tek cümlelik özeti

Linux'un güvenliği bu kartta **katman profili + üretim hedefi/CI pozitif izi +
özet matrisi (§6.1)** olarak durur; **S1/S2 kararı olmadan** bu kart
**tamamlanmış sayılmaz**.

## §23 Uzatma turu son satır özeti (bu kart — mutlak son)

| Öğe | Değer |
|-----|-------|
| Başlangıç satırı | 272 (§6.1 + Authority footer) |
| Eklenen bölümler | §7–§23 |
| Kaynak | Dosya-içi §1/§2/§2.1–§2.4/§3/§4/§5/§6/§6.1 + `audio-drivers.md` (salt-okunur) |
| Uydurma iddia | 0 |
| Kapı ihlali | 0 · `git commit` yok · Frontmatter/Authority korundu |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
