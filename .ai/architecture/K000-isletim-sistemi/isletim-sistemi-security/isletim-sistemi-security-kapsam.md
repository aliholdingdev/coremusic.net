---
node_id: K000-isletim-sistemi/isletim-sistemi-security/isletim-sistemi-security-kapsam
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-security
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-kernel
  - K000-isletim-sistemi/isletim-sistemi-core
evidence:
  - https://www.opengroup.org/
  - https://www.nist.gov/cyberframework
  - https://man7.org/linux/man-pages/man7/attributes.7.html
  - https://man7.org/linux/man-pages/man2/umask.2.html
  - https://csrc.nist.gov/pubs/sp/800/122/final
  - home.coremusic.net/include/Stream/MusicStreamHandler.php:189
  - shared/src/Session/SessionConfig.php:35
  - shared/src/Middleware/PermissionMiddleware.php:31
validation:
  - "2026-10-13: 5 web URL HEAD ile HTTP 200 dogrulandi; 3 disk kaniti Select-Object -Index ile satir birebir okundu; OS araci negatif kaniti (seccomp|cap_set_proc|prctl|landlock|setuid|chroot) 0 isabet yeniden olculdu."
open_questions:
  - "S1: OS guvenlik katmani (capabilities/seccomp/LSM) devreye alinacak mi? [UNKNOWN]"
  - "S2: Uygulama <-> OS guvenlik sorumluluk siniri (kim hangini uygular)? [UNKNOWN]"
group: mimari
---

# isletim-sistemi-security — Kapsam

**Olcum tarihi:** 2026-10-13 · **Durum:** draft · **Kapsam iki katman:** OS duzeyi guvenlik <-> uygulama katmani guvenlik · **Alt-dal sayisi:** 3 · **Icerik dosyasi:** 4 kok + 12 alt-dal = 16.

## §1 — Kapsam

### §1.1 Kapsam tek cumlelik ozet

Bu subdomain, CoreMusic'in **iki guvenlik katmani arasindaki siniri** tanimlar: (1) isletim sistemi duzeyindeki guvenlik mekanizmalari — Linux capabilities, seccomp-BPF, cekirdek security alt sistemi (LSM), namespace/cgroup isolasyonu — ve (2) repo'da fiilen bulunan **uygulama katmani** guvenlik pratiigi — `realpath` yol dogrulama, `escapeshellarg` kacisi, `.htaccess` guvenlik basliklari, `mkdir 0750` dizin izni, `hash_equals` timing-safe karsilastirma, CSP nonce uretimi, BypassAuth fail-closed mantigi. Bu subdomain **hem iki katmani tanimlar hem de aralarindaki sorumluluk sinirinin ne oldugunu sorar (S2)** ve **OS katmaninin hedef mimaride devreye alinip alinmayacagini sorgular (S1)**.

### §1.2 Kapsam ici — bu subdomainin sahibi oldugu yuzeyler

| # | Yuzey | Ne kapsar | Alt-dal | Somut ornek (repo) |
|---|---|---|---|---|
| A1 | OS guvenlik mekanizmalari | Linux capabilities, seccomp-BPF, LSM (SELinux/AppArmor/Landlock), namespace/cgroup | `cekirdek-guvenlik` | Repo'da 0 kullanim (hedef referans) |
| A2 | Erisim denetim modeli | DAC (rwx/owner), MAC (etiket), ACL, RBAC, surec/dosya denetim akisi, audit | `yetki-ve-erisim-modeli` | `mkdir 0750` -> POSIX DAC izni |
| A3 | Uygulama <-> OS siniri | Kim hangi savunmayi uygular (S2), katmanli savunma, fail-closed esikleri | `uygulama-guvenlik-siniri` | `realpath` uygulama katmaninda, OS katmani yok |
| A4 | Repo pratiigi kanitlari | Yol dogrulama, shell kacis, guvenlik basligi, dizin izni, timing-safe karsilastirma, CSP nonce, fail-closed bypass | 3 alt-dalin tamami | §6 tablosu — 12 disk kaniti |
| A5 | Surec/dosya izin altyapisi | umask, chmod, ACL girisleri, dosya etiketleri | `yetki-ve-erisim-modeli` | `SessionConfig.php:20` sabiti `0750` |
| A6 | Guvenli onyukleme & mitigasyon cercevesi | Secure Boot, TPM, modul imzalama, SMEP/SMAP/KASLR | `cekirdek-guvenlik` | Repo'da 0 kullanilabilir kanit (hedef referans) |

### §1.3 Kapsam disi — dokunulmaz sinir (yanlis dalda is uretilmez)

| # | Konu | Hangi dalda | Gerekce | Ayrim cumlesi (bu dosyalarda yazilir) |
|---|---|---|---|---|
| X1 | Kimlik dogrulama / oturum (session) / sifre / MFA | `K0100-identitiy` | Uygulama kimlik is mantigi | "Oturum is mantigi K010'da; burada yalniz OS dosya sistemi kisiti (0750)" |
| X2 | Ag guvenligi (firewall, DNS, TLS sonlandirma) | `K013-integraion` | Ag katmani OS cekirdek guvenliginin parcasi degil | "Rate limit'in ag karsiligi K013'te; burada yalniz uygulama-OS siniri" |
| X3 | Sizma testi / pentest kampanyasi | `K18-testing` | Test faaliyeti != guvenlik mekanizmasi | "Pentest senaryolari K019'da; burada mekanizma ve olcum metodolojisi" |
| X4 | Kriptografik algoritma secimi (AES-256-GCM, Argon2id) | `K009-security` + ADR-022 | Kripto karari dondurulmus ADR alanidir | "Kripto ADR-022'de; bu dal yalniz sinir etkisini isaretler" |
| X5 | Uygulama OWASP denetiminin tamami | `K009-security` | Burada yalniz "OS vs uygulama" ayrimi ve sinirlar | "OWASP denetimi K009'da; bu dal ayrim matrisini tutar" |
| X6 | Donanim/firmware guvenligi (secure boot donanim, HSM) | `K001-donanaım-type` + elektronik vault | Fiziksel katman bu dalin disinda | "Donanim guvenligi K001'de; bu dal yalniz yazilim katmani" |
| X7 | Cekirdek ici yapisi / surucu guvenligi mekanizmasi | `isletim-sistemi-kernel` | Cekirdek mekanizma sahibi | "Cekirdek mekanizmasi -kernel'de; bu dal yalniz kullanim modeli" |
| X8 | Uygulama surucusu (driver) yazimi | `isletim-sistemi-kernel` | Kod uretimi bu daldan yapilmaz | "Surucu kodu -kernel'de; bu dal yalniz sinir denetimi" |

### §1.4 Iki katman tanimi (detayli sozluk)

| Katman | Tanim | Mekanizma ornekleri | Repo durumu (2026-10-13 olcumu) |
|---|---|---|---|
| **OS guvenlik** | Cekirdek ve kullanici-uzay yardimcilari ile surec/dosya erisimini kisitlama | capabilities (CAP_*), seccomp-BPF (SCMP_ACT_*), LSM (SELinux/AppArmor/Landlock), namespace, cgroup | YOK — 0 kullanim (negatif kanit §7) — **hedef mimari referansi** |
| **Uygulama guvenlik** | PHP/JS/Apache kodu ve yapilandirmasinda uygulanan savunmalar | `realpath`, `escapeshellarg`, `hash_equals`, `.htaccess` basliklari, `mkdir` izni, CSP nonce, fail-closed bypass | VAR — somut disk kaniti (§6 tablosu) |

### §1.5 Kapsam karar kayit defteri

| # | Karar | Durum | Kaynak | Not |
|---|---|---|---|---|
| K1 | Bu dal OS guvenlik araclarinin **secimini/kurulumunu** yapmaz; yalniz cercevesini ve olcumunu tanimlar | Baglayici | S1 [UNKNOWN] | Secim mimari sahibine ait |
| K2 | Oturum is mantigi (`session_start`, yeniden uretimi, CSRF akisi) burada **tanimlanmaz** -> `K0100-identitiy` | Baglayici | K010 ayrimi | Ayrim her alt-dalda acikca yazilir |
| K3 | Repo'da OS araci bulunmasi bir **eksiklik bulgusu degil, mevcut durum gercegidir**; telafisi S1 kararina baglidir | Baglayici | §7 negatif kanit | "0 kullanim = hedef referans" |
| K4 | Yeni guvenlik ADR taslagi bu daldan dogarsa numarasi `>=088` olmalidir (ADR 001-037 dondurulmus) | Baglayici | Vault ADR kurali | ADR-058/059 mevcut, yenisi >=088 |
| K5 | Bu dal salt-okunur vault'tur; `*.php`/`*.js`/`*.css`/`*.sql` kaynak koduna **yazilmaz** | Baglayici | Gores beyazlistesi | Kod yalniz okunur, kanit olarak alinir |
| K6 | Frontmatter 8 alan + `group` (9. alan) — her dosyada zorunlu | Baglayici | v2 §4.2 | `group` tirnaksiz |
| K7 | `parent:` alani YASAKTIR — yalniz `parent_id` kullanilir | Baglayici | v2 §4.2 | Tum 16 dosyada denetlendi |

### §1.6 Katman sorumluluk ilkesi (on-matris — S2'ye hazirlik)

| Savunma turu | Birincil katman (ilke) | Ikincil katman | CoreMusic'te uygulanan |
|---|---|---|---|
| Yol dogrulama (traversal) | OS (namespace/Landlock) | Uygulama (`realpath`) | Uygulama (tek katman) |
| Komut enjeksiyonu | OS (seccomp/capability) | Uygulama (`escapeshellarg`) | Uygulama (tek katman) |
| Yetki yukseltme | OS (capability/setuid) | Uygulama (bypass fail-closed) | Uygulama (tek katman) |
| Baslik/XSS sniffing | — (bu sinif OS disi) | Uygulama (`.htaccess`/middleware) | Uygulama |
| CSRF | — (bu sinif OS disi) | Uygulama (`hash_equals` + token) | Uygulama |
| Dizin izni | OS (POSIX/DAC) | Uygulama (`mkdir` izni) | Her iki katman (OS cekirdek + PHP cagrisi) |
| RBAC yetki denetimi | OS (UID/GID + ACL) | Uygulama (PermissionMiddleware) | Uygulama |
| Surec izolasyonu | OS (namespace/cgroup) | Uygulama (yok) | YOK (hedef referans) |

## §2 — 3 alt-dal envanteri

### §2.1 `cekirdek-guvenlik/`

| Dosya | Tur | Icerik ozeti | group | Acik soru |
|---|---|---|---|---|
| `index.md` | subdomain indeksi (Kademe-3) | Kunye, 3 cocuk, komsumatrisi, S1 isareti | mimari | S1 |
| `cekirdek-guvenlik-anayasa.md` | module | §0 ELI15 · §1 koruma halkalari · §2 guvenli onyukleme · §3 SMEP/SMAP/KASLR/PAN · §4 sanallastirma · §5 kernel exploit yuzeyi · §6 olcum · §7 repo disk kanitlari · §8 riskler · §9 kapsam disi · §10 kaynakca · §22 OS gruplari (8 platform) | mimari | S1 |
| `cekirdek-guvenlik-surucu.md` | module | §0 · §1 surucu imzalama · §2 ioctl/donanim yuzeyi · §3 kullanici-uzay <- cekirdek gecis noktalari · §4 surucu olcumu · §5 repo kaniti · §6 kapsam disi · §7 referans · §26 OS gruplari (8 platform) | mimari | S1 |
| `cekirdek-guvenlik-ornekler.md` | module | §0 · §1 surucu/on-kosul ornekleri · §2 mitigasyon konfigurasyon ornekleri · §3 repo disk ornekleri · §4 olcum ciktisi · §5 kapsam disi · §6 referans · §6 OS gruplari (8 platform) | kod | S1 |
| `cekirdek-guvenlik-{windows,linux,macos,ios,android,tizen,webos,reactos}.md` ×8 | module (OS karti) | §0 ELI15 · §1 OS guvenlik profili · §2 kod ornegi (`kaynak: upstream docs` imzali) · §3 CoreMusic repo durumu (hedef referans) · §4 sinir cumleleri (K010/K009/komsu dal) · §5 referans · §6 acik sorular | kod | S1 |

### §2.2 `yetki-ve-erisim-modeli/`

| Dosya | Tur | Icerik ozeti | group | Acik soru |
|---|---|---|---|---|
| `index.md` | subdomain indeksi (Kademe-3) | Kunye, 3 cocuk, komsumatrisi, S2 isareti | mimari | S2 |
| `yetki-ve-erisim-modeli-anayasa.md` | module | §0 ELI15 · §1 DAC/MAC/RBAC/BEL karsilastirmasi · §2 POSIX izinleri & ACL · §3 capability model · §4 MAC politikalari (SELinux/AppArmor) · §5 dosya sistemi uzerindeki etiket · §6 olcum · §7 referans · §24 OS gruplari (8 platform) | mimari | S2 |
| `yetki-ve-erisim-modeli-surucu.md` | module | §0 · §1 denetim akisini suren zincir (syscall -> VFS -> DAC -> ACL -> MAC) · §2 surec/TTY/sinyal yetkisi · §3 umask & dizin izni uretimi · §4 audit kaydi · §5 olcum · §6 kapsam disi · §7 referans · §20 OS gruplari (8 platform) | mimari | S2 |
| `yetki-ve-erisim-modeli-ornekler.md` | module | §0 · §1 repo RBAC/DAC ornekleri (Dosya: + Select-Object -Index) · §2 komut/tablo ornekleri · §3 yanlis-kullanim karsilastirmasi · §4 olcum · §5 kapsam disi · §6 referans · §13 OS gruplari (8 platform) | kod | S2 |
| `yetki-ve-erisim-modeli-{windows,linux,macos,ios,android,tizen,webos,reactos}.md` ×8 | module (OS karti) | §0 ELI15 · §1 OS erisim kontrol profili · §2 kod ornegi (`kaynak: upstream docs` imzali) · §3 repo durumu (hedef referans) · §4 sinir cumleleri · §5 referans · §6 acik sorular | kod | S2 |

### §2.3 `uygulama-guvenlik-siniri/`

| Dosya | Tur | Icerik ozeti | group | Acik soru |
|---|---|---|---|---|
| `index.md` | subdomain indeksi (Kademe-3) | Kunye, 3 cocuk, komsumatrisi, S1+S2 isareti | mimari | S1+S2 |
| `uygulama-guvenlik-siniri-anayasa.md` | module | §0 ELI15 · §1 uygulama<->OS siniri (syscall yuzeyi) · §2 sandbox turleri · §3 container/namespace/cgroup guvenligi · §4 kullanici alani <-> cekirdek denetimi · §5 olcum · §6 repo kaniti · §7 referans · §16 OS gruplari (8 platform) | mimari | S1+S2 |
| `uygulama-guvenlik-siniri-surucu.md` | module | §0 · §1 sandbox politikasinin surulmesi (bubblewrap/Job Object/sandbox-exec/seccomp) · §2 kisit -> uygulama -> dogrulama zinciri · §3 konteyner kisitlarinin devreye alinmasi · §4 basarisizlik modlari · §5 olcum · §6 referans · §15 OS gruplari (8 platform) | mimari | S1+S2 |
| `uygulama-guvenlik-siniri-ornekler.md` | module | §0 · §1 CoreMusic gercek auth/CSRF kodu (Dosya: + Select-Object -Index) · §2 sandbox konfigurasyon ornekleri · §3 yetkisiz erisim test ciktisi · §4 olcum · §5 kapsam disi · §6 referans · §7 OS gruplari (8 platform) | kod | S1+S2 |
| `uygulama-guvenlik-siniri-{windows,linux,macos,ios,android,tizen,webos,reactos}.md` ×8 | module (OS karti) | §0 ELI15 · §1 OS sandbox/sinir profili · §2 kod ornegi (`kaynak: upstream docs` imzali) · §3 repo durumu (hedef referans) · §4 sinir cumleleri · §5 referans · §6 acik sorular | kod | S1+S2 |

### §2.4 Envanter sayimlari (2026-10-14 guncel)

| Metrik | Deger |
|---|---|
| Alt-dal sayisi | 3 |
| Alt-dal basina icerik dosyasi (temel) | 3 |
| Alt-dal basina OS karti | 8 (windows · linux · macos · ios · android · tizen · webos · reactos) |
| Toplam alt-dal dosyasi (index + icerik + kart) | 36 (3 x 12) |
| Kok dosya (revize) | 4 |
| Toplam dosya | 40 |
| Icerik dosyasi minimum satir hedefi | >=500 |
| OS karti satir araligi | 150–260 |
| Alt-dal `index.md` satir araligi | 40–100 |
| Kok `index.md` satir araligi | 40–100 |
| `group` deger kumesi | `mimari` · `kod` · `kanit` |

### §2.5 Alt-dal oncelik sirasi (bagimlilik nedeniyle)

| Sira | Alt-dal | Neden bu sira |
|---|---|---|
| 1 | `cekirdek-guvenlik` | OS mekanizmalari tanimlanmadan sinir (S2) ve uygulama katmani anlasilmaz |
| 2 | `yetki-ve-erisim-modeli` | Erisim denetim akisi OS mekanizmalarindan sonra gelir; uygulama katmaninin neyi denetledigini belirler |
| 3 | `uygulama-guvenlik-siniri` | Her iki katman tanimlandiktan sonra sinir (S2) ve repo kanitlari baglam kazanir |

## §3 — OS guvenlik <-> uygulama guvenlik ayrim matrisi

### §3.1 Saldiri sinifi bazinda katman dagilimi

| # | Saldiri sinifi | OWASP 2025 | OS katmani savunmasi | Uygulama katmani savunmasi | Repo durumu (gozlem) |
|---|---|---|---|---|---|
| C1 | Dizin traversal (CWE-22) | A01:2025 | chroot/namespace koki, Landlock yol kisiti | `realpath` + kok dizin karsilastirmasi | Uygulama katmani: `MusicStreamHandler.php:189-200` |
| C2 | Komut enjeksiyonu (CWE-78) | A05:2025 | seccomp `execve`/`fork` filtreleme, `CAP_SYS_ADMIN` yoklugu | `escapeshellarg` kacisi | Uygulama katmani: `AIEngine.php:292` |
| C3 | Yetki yukseltme | A01:2025 | capability ayrimi, setuid denetimi, `no_new_privs` | BypassAuth fail-closed + production kilidi | OS katmani yok; uygulama fail-closed kanitli (`BypassAuthMiddleware.php:30`) |
| C4 | Clickjacking / MIME sniffing | A02:2025 | — (bu sinif OS disi) | `X-Frame-Options DENY`, `nosniff` | Apache `.htaccess:10-11` + PHP `SecurityHeadersMiddleware.php:27-28` |
| C5 | CSRF (CWE-352) | A01:2025 | — (bu sinif OS disi) | `hash_equals` timing-safe, `csrf_token` alani | `CsrfMiddleware.php:42` |
| C6 | Kaba kuvvet / rate limit | A07:2025 | netfilter/conntrack (ag dali) | APCu rate limit; auth yollari fail-closed | `RateLimiterMiddleware.php:39-40,86` |
| C7 | Surec hafizasi ifsasi | A04:2025 | `ptrace` yasagi, dumpable bit | Uygulama dump almaz | OS katmani yok (hedef referans) |
| C8 | Dizin izni gevsekligi (or. 777) | A02:2025 | POSIX/DAC cekirdek denetimi | `mkdir 0750` kisitli izin | `SessionConfig.php:20,35` |
| C9 | Bilgi sizintisi basliklari | A02:2025 | — | CSP nonce, Trusted Types, Referrer-Policy, `Server` unset | `SecurityHeadersMiddleware.php:18,32` + `.htaccess:14-15` |
| C10 | Kotu niyetli yerel kullanici | A01:2025 | MAC etiketleri, capability, namespace | — (uygulama bu sinifi savunmaz) | OS katmani yok (S1) |
| C11 | Yuksek yetkili rol yanlis kullanimi | A01:2025 | UID/GID + ACL | RBAC: `requiredRole` / `requiredPermission` | `PermissionMiddleware.php:31,40` |
| C12 | Gecersiz origin/CORS genislemesi | A01:2025 | — | Origin whitelist + tam host eslesmesi | `OriginCheckMiddleware.php:48,55` · `CorsMiddleware.php:45` |

### §3.2 Karar kurali (hangi katman birincil?)

| Kural | Aciklama | Repo uyumu |
|---|---|---|
| R1 | Bir savunma **mumkunse OS katmaninda** uygulanir; uygulama katmani ikinci savunma olarak kalir | CoreMusic'te OS katmani yok -> R2 devrede |
| R2 | OS katmani mevcut degilse (bugunku durum) uygulama katmani **tek savundur** — tek-nokta-basarisizlik riski tasiir ve raporlanir | Tum C1-C9'da uygulama katmani tek savunma |
| R3 | Kimlik/oturum/sifre/MFA savunmalari **her zaman** `K0100-identitiy` katmaninda; OS katmani yalniz dosya/surec kisiti verir | `SessionConfig.php:35` yalniz dosya izni, oturum mantigi degil |
| R4 | Ag sinifi savunmalari `K013-integraion` komusuna devredilir; bu dal yalniz uygulama-OS sinirini tutar | RateLimiter uygulama katmani, ag katmani degil |
| R5 | OWASP denetiminin tamami `K009-security` dali; bu dal yalniz "OS katmani var mi yok mu" sorusunu tutar | OWASP eslemesi §3.4 — sinir isareti only |

### §3.3 Katman boslugu tablosu (nerede tek savunma var?)

| # | Saldiri sinifi | Kac katman savunuyor | Bosluk var mi? | Boslugun sonucu |
|---|---|---|---|---|
| B1 | Dizin traversal | 1 (uygulama) | Evet — OS katmani yok | `realpath` hatasi -> ihlal |
| B2 | Komut enjeksiyonu | 1 (uygulama) | Evet | `escapeshellarg` hatasi -> ihlal |
| B3 | Yetki yukseltme | 1 (uygulama fail-closed) | Evet | BypassAuth mantik hatasi -> ihlal |
| B4 | Clickjacking | 2 (Apache + PHP) | Hayir — iki bagimsiz katman | Bir katman devre disi kalirsa digeri ayakta |
| B5 | CSRF | 1 (uygulama) | Kisiti — `hash_equals` + token | Token uretimi hatasi -> ihlal |
| B6 | Surec hafizasi ifsasi | 0 | Evet — hicbir katman yok | OS katmani eklenmedikce acik |
| B7 | Kötu niyetli yerel kullanici | 0 | Evet | MAC/capability yok |
| B8 | RBAC yetki yanlis kullanimi | 1 (uygulama) | Evet | Rol eslesme hatasi -> ihlal |

### §3.4 OWASP Top 10:2025 eslemesi (bu dalin ilgilendikleri)

| OWASP 2025 | Ad | Bu dalin iliskisi | Ilgili alt-dal |
|---|---|---|---|
| A01:2025 | Broken Access Control | DAC/MAC/ACL/RBAC denetim akisi, yetki yukseltme, traversal | `yetki-ve-erisim-modeli` · `uygulama-guvenlik-siniri` |
| A02:2025 | Security Misconfiguration | `.htaccess` basliklari, `Server` unset, CSP, dizin izni | `uygulama-guvenlik-siniri` |
| A04:2025 | Cryptographic Failures | Sinir etkisi (kripto ADR-022'de, bu dal yalniz isaretler) | `uygulama-guvenlik-siniri` (sinir) |
| A05:2025 | Injection | Komut enjeksiyonu (`escapeshellarg`), OS enjeksiyon savunmasi (seccomp) | `cekirdek-guvenlik` · `uygulama-guvenlik-siniri` |
| A07:2025 | Authentication Failures | Sinir: oturum OS kisiti (K010'da is mantigi) | `yetki-ve-erisim-modeli` (sinir) · K010 |
| A10:2025 | Mishandling of Exceptional Conditions | Fail-closed davranis (`BypassAuth`, `RateLimiter`) | `uygulama-guvenlik-siniri` |

> Not: A03, A06, A08, A09 bu dalin yuzeyinin disindadir — tamami `K009-security` dali sorumludur.

## §4 — Bagimlilik grafigi

```text
isletim-sistemi-kernel  --(cekirdek mekanizmasi)-->  cekirdek-guvenlik
        |                                                |
        |                                                +--> cekirdek-guvenlik-surucu
        |                                                +--> cekirdek-guvenlik-ornekler
        v
isletim-sistemi-core    --(izin kavrami)---------->  yetki-ve-erisim-modeli
        |                                                |
        |                                                +--> yetki-ve-erisim-modeli-surucu
        |                                                +--> yetki-ve-erisim-modeli-ornekler
        v
repo pratiigi (mevcut)  --(sinir + kanit)-------->  uygulama-guvenlik-siniri
                                                         |
                                                         +--> uygulama-guvenlik-siniri-surucu
                                                         +--> uygulama-guvenlik-siniri-ornekler

Komsubu dallar (dokunulmaz):  K009-security (uygulama guvenligi)
                              ⚠️ (kimlik/oturum/sifre/MFA)
                              K013-integraion (ag)
                              K18-testing (sizma testi)
```

### §4.1 Bagimlilik tablosu

| # | Kaynak | Hedef | Bagimlilik turu | Not |
|---|---|---|---|---|
| D1 | `cekirdek-guvenlik` | `isletim-sistemi-kernel` | mekanizma (cekirdek) | Cekirdek alt sistemi bu dalin konu komusudur |
| D2 | `yetki-ve-erisim-modeli` | `isletim-sistemi-core` | kavram (izin) | Izin kavraminin sahibi core; burada yalniz denetim akisi |
| D3 | `uygulama-guvenlik-siniri` | `cekirdek-guvenlik` | savunma katmani | OS araclarinin devreye alma esigi S1'e bagli |
| D4 | `uygulama-guvenlik-siniri` | `yetki-ve-erisim-modeli` | denetim | Uygulama erisimi OS denetim akisinin ustunde calisir |
| D5 | `yetki-ve-erisim-modeli-ornekler` | `K0100-identitiy` | sinir | Oturum dosyasi izni burada, oturum is mantigi K010'da |
| D6 | 3 alt-dal | `isletim-sistemi-security-dogrulama.md` | kanit | Tum iddialar dogrulama dosyasinda HTTP/disk kanitli |
| D7 | `cekirdek-guvenlik-surucu` | `isletim-sistemi-kernel` | surucu siniri | Surucu yazimi -kernel'de; burada yalniz sinir denetimi |

### §4.2 Ters bagimlilik (kim bu dala bagimli?)

| Hedef | Bagimli | Ne bekler |
|---|---|---|
| `cekirdek-guvenlik` | `uygulama-guvenlik-siniri` | S1 karari -> OS katmani devreye alinacak mi |
| `yetki-ve-erisim-modeli` | `uygulama-guvenlik-siniri` | S2 karari -> sinir netlesicek mi |
| `isletim-sistemi-security` (tum) | `isletim-sistemi-security-dogrulama.md` | Kanit butunlugu (HTTP + disk) |

## §5 — Sinir komusulari

### §5.1 Komsumatrisi

| Komsu | Ne tasir bu dala | Bu dal ne devralir | Bu dal ne devretmez |
|---|---|---|---|
| `isletim-sistemi-kernel` | Cekirdek mekanizmalari: LSM altyapisi, seccomp yorumlayicisi, capability cekregi, namespace/cgroup cekregi | Mekanizmanin **kullanim modeli** (profil yazimi, politika secimi) | Cekirdek ici yapisi, surucu guvenlik mekanizmasi, kernel patch politikasi |
| `isletim-sistemi-core` | Izin kavrami, temel cekirdek yardimcilari, dosya modeli | Izin **denetim akisi** (DAC/MAC/ACL check -> allow/deny) | Izin kavraminin tanimi, dosya modeli kararlari |
| `isletim-sistemi-observability` | Guvenlik olay logu yuzeyi (audit, securityEvent) | Olay **kayit bicimi** (ne loglanir) | Log toplama/analiz altyapisi, log saklama suresi |
| `isletim-sistemi-testing` | Yetki testi yuzeyi (test altyapisi) | Olcum **yontemi** (test ne dogrular) | Test kampanyasi yurutumu, CI kapisina baglilik |
| `isletim-sistemi-runtime` | Runtime sandbox baglami | Sandbox **calisma zamani modeli** | Sandbox kurulumu, politika yazimi |
| `isletim-sistemi-turu` | Isletim sistemi turu farkliliklari | **Fark matrisi** (Windows/Linux/macOS ayni mi) | Tur bazli kurulum kararlari |
| `K0100-identitiy` | Kimlik dogrulama, oturum, sifre, MFA uygulama katmani | **Sinir**: uygulama oturumunun OS'taki karsiligi (oturum dosyasi izni `0750`, TTY sahipligi) | Oturum is mantigi, session fixation korumasi, MFA akisi — **buraya girilmez** |
| `K009-security` | OWASP, auth, CSRF, kripto ADR'leri (ADR-022) | Sinir etkisi isaretleme | OWASP denetimi, kripto algoritma secimi, CSRF tasarimi |
| `K013-integraion` | Ag entegrasyonu, servis iletisim yuzeyi | — (yalniz sinir isareti) | Rate limit'in ag katmani karsiligi, TLS sonlandirma |
| `K18-testing` | Sizma testi senaryolari | Test edilebilirlik yuzeyi (olcum metodolojisi) | Pentest kampanyasi yurutumu |

### §5.2 Komluluk kurali ihlali isaretleri

| Isaret | Sonuc | Yakalama yontemi |
|---|---|---|
| Bu daldan oturum akisi (login, MFA, session fixation) tanimlanmasi | Ihlal -> metin `K0100-identitiy` dosyasina tasinir | Icerik taramasi: `session_start`, `login`, `MFA` |
| Bu daldan ag/firewall kurali tanimlanmasi | Ihlal -> `K013-integraion` dosyasina tasinir | Icerik taramasi: `firewall`, `iptables`, `TLS sonlandirma` |
| Bu daldan OWASP denetimi yurutulmesi | Ihlal -> `K009-security` dali sorumlu | Icerik taramasi: OWASP kontrol listesi |
| Bu daldan `*.php`/`*.js`/`*.css`/`*.sql` kaynak kodu degisikligi | Ihlal -> vault bu dalda salt-okunur; kod yalniz okunur | `git status` + beyazliste denetimi |
| Bu daldan ADR 001-037 dondurulmus metni degistirilmesi | Ihlal -> vault bozulur | `git diff` ADR dosyalari |

### §5.3 K010 ile ayrim cumlesi (her alt-dalda zorunlu)

> **K010 ayrimi (baglayici):** Bu dosya **OS duzeyi guvenlik + uygulama-OS guvenlik sinirini** kapsar. **Kimlik dogrulama, oturum (session), sifre ve MFA** -> `K0100-identitiy` alt-dalinin konusudur; burada yalnizca bunlarin **OS dosya sistemi/surec kisitlari** (or. oturum dosyasi dizini `0750`, TTY sahipligi) ele alinir — oturum is mantigina girilmez.

### §5.4 K009 ile ayrim cumlesi (her alt-dalda zorunlu)

> **K009 ayrimi (baglayici):** Bu dosya **uygulama guvenliginin** degil, **OS duzeyi guvenliginin ve uygulama-OS sinirinin** tanimidir. OWASP Top 10 denetimi, auth/CSRF/CSP tasarimi, kripto algoritma secimi (AES-256-GCM, Argon2id) ve rate limit politikasi -> `K009-security` + ilgili ADR'ler (ADR-010/013/022).

## §6 — Disk kanitlari (gercek repo, 2026-10-13)

| # | Kanit | Dosya | Satir | Dogrulama yontemi |
|---|---|---|---|---|
| K-D1 | Dizin izni sabiti `0750` | `shared/src/Session/SessionConfig.php` | 20 | `Select-Object -Index 19` |
| K-D2 | `@mkdir($savePath, 0750, true);` | `shared/src/Session/SessionConfig.php` | 35 | `Select-Object -Index 34` |
| K-D3 | Cookie `httponly => true` | `shared/src/Session/SessionConfig.php` | 59 | `Select-Object -Index 58` |
| K-D4 | Cookie `samesite => 'Lax'` | `shared/src/Session/SessionConfig.php` | 60 | `Select-Object -Index 59` |
| K-D5 | `realpath` yol dogrulama | `home.coremusic.net/include/Stream/MusicStreamHandler.php` | 189 | `Select-Object -Index 188` |
| K-D6 | Kok yol karsilastirma oneki | `home.coremusic.net/include/Stream/MusicStreamHandler.php` | 199-200 | `Select-Object -Index 198,199` |
| K-D7 | `Options -Indexes` | `home.coremusic.net/.htaccess` | 2 | `Select-Object -Index 1` |
| K-D8 | `X-Frame-Options "DENY"` | `home.coremusic.net/.htaccess` | 10 | `Select-Object -Index 9` |
| K-D9 | Uzantilari reddet `Require all denied` | `home.coremusic.net/.htaccess` | 18-19 | `Select-Object -Index 17,18` |
| K-D10 | `escapeshellarg($filePath)` | `shared/src/AI/AIEngine.php` | 292 | `Select-Object -Index 291` |
| K-D11 | `hash_equals` timing-safe CSRF | `shared/src/Middleware/CsrfMiddleware.php` | 42 | `Select-Object -Index 41` |
| K-D12 | CSP nonce `bin2hex(random_bytes(32))` | `shared/src/Middleware/SecurityHeadersMiddleware.php` | 18 | `Select-Object -Index 17` |

### §6.1 Negatif kanit (OS araci yoklugu — 2026-10-13 yeniden olculdu)

| Arama kalibi | Kapsam | Isabet |
|---|---|---|
| `seccomp\|cap_set_proc\|capget\|prctl\|landlock\|apparmor\|selinux` | `shared/src`, `api.coremusic.net`, `home.coremusic.net` (`vendor` disi, `*.php`/`*.js`) | 0 |
| `posix_setuid\|posix_setgid\|setuid\|setgid\|chroot\|bubblewrap\|JobObject\|sandbox-exec\|unshare\|CLONE_NEW` | ayni kapsam | 0 |
| Toplam | 2 grup, tek tarama | **0 isabet** |

### §6.2 Web kanitlari (HTTP 200 — 2026-10-13, envanter disi)

| # | Iddia | URL | Durum |
|---|---|---|---|
| K-W1 | POSIX erisim kontrolu standardi Open Group tarafindan yayimlanir | https://www.opengroup.org/ | HTTP 200 |
| K-W2 | NIST Cybersecurity Framework erisilebilir | https://www.nist.gov/cyberframework | HTTP 200 |
| K-W3 | POSIX dosya nitelikleri (owner/group/mode) man sayfasi | https://man7.org/linux/man-pages/man7/attributes.7.html | HTTP 200 |
| K-W4 | `umask(2)` yeni dosya izin maskesini tanimlar | https://man7.org/linux/man-pages/man2/umask.2.html | HTTP 200 |
| K-W5 | NIST SP 800-122 PII kesfirimini isler | https://csrc.nist.gov/pubs/sp/800/122/final | HTTP 200 |

## §7 — Acik sorular (S1 · S2)

| # | Soru | Etkiledigi alt-dal | Etkiledigi bolum | Durum | Kapanis kriteri |
|---|---|---|---|---|---|
| S1 | OS guvenlik katmani (capabilities/seccomp/LSM) hedef mimaride devreye alinacak mi, yoksa guvenlik tamamen uygulama katmaninda mi? | `cekirdek-guvenlik` · `uygulama-guvenlik-siniri` | `cekirdek-guvenlik-anayasa` §8 · `uygulama-guvenlik-siniri-surucu` §6 | [UNKNOWN] — **KAPATILMAZ** | Mimari sahibinin yazili karari + ADR (yeni ise numara `>=088`) |
| S2 | Uygulama <-> OS guvenlik sorumluluk siniri (kim hangini uygular)? | `yetki-ve-erisim-modeli` · `uygulama-guvenlik-siniri` | `yetki-ve-erisim-modeli-anayasa` §7 · `uygulama-guvenlik-siniri-anayasa` §1 | [UNKNOWN] — **KAPATILMAZ** | Sinir matrisi (§3.1) taslak olarak sunulur, onay bekler |

### §7.1 S1/S2 icin hazirlanan karar secenekleri (secim YOK — kayit amaclidir)

| Secenek | Aciklama | Arti | Eksi | Bagli oldugu soru |
|---|---|---|---|---|
| Ops-1 | Guvenlik tamamen uygulama katmaninda (bugunku durumun teyidi) | Sifir OS bakim maliyeti | Tek-nokta-basarisizlik; uygulama hatasi dogrudan ihlal | S1 |
| Ops-2 | capabilities + seccomp ile PHP-FPM/Apache surec kisiti | Dusuk karisiklik; somut kazanim | Linux hedefi sart; Windows dev ortaminda karsiligi yok | S1 |
| Ops-3 | Tam LSM (SELinux/AppArmor) + konteyner isolasyonu | Derin katmanli savunma | Yuksek operasyon maliyeti; tek-host mimarisiyle uyumsuzluk riski | S1 |
| Ops-4 | Hibrit: uygulama katmani birincil + secici OS kisitlari (Ops-2) | En dusuk maliyet/okule oran | Sinir (S2) netlesmezse sorumluluk dagilir | S1 + S2 |

> Bu tablo bir **karar degildir**; S1/S2 kapanana kadar secenekler kayit altinda tutulur.

### §7.2 S1/S2 kapanmadan onceki gecici ilke (bu revizyon)

| Ilke | Uygulama |
|---|---|
| P1 | S1 kapanana kadar tum OS mekanizmalari **hedef referans** olarak etiketlenir; mevcut durum olarak sunulmaz |
| P2 | S2 kapanana kadar tum savunmalar **uygulama katmani** olarak etiketlenir; OS katmani "yok" olarak isaretlenir |
| P3 | Her alt-dal dosyasi §7.1'deki secenekleri bilgilendirme amaciyla tasiir; hicbirini secmez |
| P4 | K010 ayrimi her dosyada acikca yazilir; oturum is mantigina girilmez |
| P5 | K009 ayrimi her dosyada acikca yazilir; OWASP denetimi bu daldan yurutulmez |

### §7.3 Bu revizyonun durustluk beyani

| Beyan | Deger |
|---|---|
| Bu dosya S1/S2'yi kapatir mi? | HAYIR — her ikisi de [UNKNOWN] olarak korunur |
| Repo'da OS araci kullanimi var mi? | YOK (negatif kanit, 2026-10-13 yeniden olculdu: 0 isabet) |
| Uygulama katmani pratiigi kanitlanmis mi? | EVET — §6 tablosu 12 disk kaniti |
| Frontmatter 9 alan (8 + group) tam mi? | EVET — node_id, node_type, status, parent_id, children, depends_on, evidence, validation, open_questions + group |
| `group` tirnaksiz mi? | EVET — `group: mimari` |
| `parent:` alani var mi? | HAYIR — yalniz `parent_id` |
| Beyazliste disina yazilan dosya | 0 |

## §8 — 40 dosyalik sorumluluk matrisi (Kademe-2 + Kademe-3 + Kademe-4)

| # | Dosya | Kademe | group | Ana soru | S1/S2 bagi |
|---|---|---|---|---|---|
| F1 | `isletim-sistemi-security/index.md` | Kademe-2 | mimari | Klasor kunyesi, 3 cocuk, gruplar, OS karti deseni | S1 + S2 isareti |
| F2 | `isletim-sistemi-security-kapsam.md` | Kademe-2 | mimari | Kapsam siniri, ayrims matrisi (bu dosya) | S1 + S2 |
| F3 | `isletim-sistemi-security-dogrulama.md` | Kademe-2 | kanit | Iddia -> URL -> HTTP 200 + disk satir kaniti | S1 + S2 dogrulamasi |
| F4 | `isletim-sistemi-security-ornekler.md` | Kademe-2 | kod | Gercek repo kod ornekleri (4+ kanit) · §13 OS gruplari (8 platform matrisi) | S2 kaniti |
| F5 | `cekirdek-guvenlik/index.md` | Kademe-3 | mimari | Alt-dal kunyesi, 3 cocuk + 8 OS karti | S1 |
| F6 | `cekirdek-guvenlik-anayasa.md` | Kademe-4 | mimari | Koruma halkalari, on-kosul, mitigasyon · §22 OS gruplari | S1 |
| F7 | `cekirdek-guvenlik-surucu.md` | Kademe-4 | mimari | Surucu imzalama, ioctl/donanim yuzeyi · §26 OS gruplari | S1 |
| F8 | `cekirdek-guvenlik-ornekler.md` | Kademe-4 | kod | Surucu/mitigasyon ornekleri + repo kaniti · §6 OS gruplari | S1 kaniti |
| F9 | `yetki-ve-erisim-modeli/index.md` | Kademe-3 | mimari | Alt-dal kunyesi, 3 cocuk + 8 OS karti | S2 |
| F10 | `yetki-ve-erisim-modeli-anayasa.md` | Kademe-4 | mimari | DAC/MAC/RBAC/BEL karsilastirmasi, ACL, capability · §24 OS gruplari | S2 |
| F11 | `yetki-ve-erisim-modeli-surucu.md` | Kademe-4 | mimari | Denetim akisini suren zincir, audit · §20 OS gruplari | S2 |
| F12 | `yetki-ve-erisim-modeli-ornekler.md` | Kademe-4 | kod | RBAC/DAC repo ornekleri · §13 OS gruplari | S2 kaniti |
| F13 | `uygulama-guvenlik-siniri/index.md` | Kademe-3 | mimari | Alt-dal kunyesi, 3 cocuk + 8 OS karti | S1 + S2 |
| F14 | `uygulama-guvenlik-siniri-anayasa.md` | Kademe-4 | mimari | syscall siniri, sandbox turleri, konteyner · §16 OS gruplari | S1 + S2 |
| F15 | `uygulama-guvenlik-siniri-surucu.md` | Kademe-4 | mimari | Sandbox politikasinin surulmesi, esikler · §15 OS gruplari | S1 + S2 |
| F16 | `uygulama-guvenlik-siniri-ornekler.md` | Kademe-4 | kod | CoreMusic auth/CSRF gercek kodu · §7 OS gruplari | S1 + S2 kaniti |
| F17-F24 | `cekirdek-guvenlik-{windows,linux,macos,ios,android,tizen,webos,reactos}.md` ×8 | Kademe-4 | kod | OS bazli koruma halkasi/profili + upstream-docs imzali ornek | S1 (hedef referans) |
| F25-F32 | `yetki-ve-erisim-modeli-{windows,linux,macos,ios,android,tizen,webos,reactos}.md` ×8 | Kademe-4 | kod | OS bazli erisim kontrol modeli + ornek | S2 (hedef referans) |
| F33-F40 | `uygulama-guvenlik-siniri-{windows,linux,macos,ios,android,tizen,webos,reactos}.md` ×8 | Kademe-4 | kod | OS bazli sandbox/sinir + ornek | S1 + S2 (hedef referans) |

### §8.0 OS kartlari uzerine baglayici kurallar (2026-10-14)

| # | Kural |
|---|---|
| OC1 | Tum 24 OS karti `group: kod` · Kademe-4 · satir 150–260 · son satir Authority |
| OC2 | Her kartin §2'sindeki her kod ornegi `# kaynak: upstream docs` imzalidir; "repo'da uygulaniyor" yazmak YASAKTIR |
| OC3 | Her kartin §3'u repo durumunu (hedef referans — OS araci taramasi 0 isabet) tasir |
| OC4 | Her kartin §4'u K010/K009/komsu dal sinir cumlelerini birebir tasir |
| OC5 | S1/S2 hicbir kartta kapatilmaz; kartlar `[UNKNOWN]` durumu devralir |
| OC6 | Kartlar mevcut 16 dosyanin **yerine degil, yanina** konur; 16 dosyanin §OS gruplari bolumleri kart matrisine isaret eder |

### §8.1 Dosya turleri ve satir esikleri

| Dosya turu | Ornek | Alt sinir | Ust sinir | group |
|---|---|---|---|---|
| `index.md` (kok + alt-dal) | `index.md` | 40 satir | 100 satir | `mimari` |
| `-kapsam` | `isletim-sistemi-security-kapsam.md` | 500 satir | — | `mimari` |
| `-anayasa` | `cekirdek-guvenlik-anayasa.md` | 500 satir | — | `mimari` |
| `-surucu` | `yetki-ve-erisim-modeli-surucu.md` | 500 satir | — | `mimari` |
| `-ornekler` | `uygulama-guvenlik-siniri-ornekler.md` | 500 satir | — | `kod` |
| `-dogrulama` | `isletim-sistemi-security-dogrulama.md` | 500 satir | — | `kanit` |
| `-<os>` (OS karti) | `cekirdek-guvenlik-windows.md` | 150 satir | 260 satir | `kod` |
| `kod-ornekleri-<dil>` (dil dosyasi) | `kod-ornekleri/kod-ornekleri-js.md` | 150 satir | 260 satir | `kod` |
| `kod-ornekleri-<os>` (OS dosyasi) | `kod-ornekleri/kod-ornekleri-windows.md` | 150 satir | 260 satir | `kod` |

> 499 satir **basarisizdir**; icerik dosyalarinda esik 500 tam satirdir.

## §9 — OWASP satirlarinin dosyalara dagilimi

| OWASP 2025 | Sahip dosya (bu dalda) | Tasidigi yuzey | Sinirda kaldigi yer |
|---|---|---|---|
| A01:2025 | `yetki-ve-erisim-modeli-anayasa` §1 | DAC/MAC/RBAC karsilastirmasi | OWASP denetimi `K009-security` |
| A01:2025 | `uygulama-guvenlik-siniri-anayasa` §1 | syscall yuzeyi, traversal | OWASP denetimi `K009-security` |
| A02:2025 | `uygulama-guvenlik-siniri-ornekler` §1 | `.htaccess` + middleware basliklari | OWASP denetimi `K009-security` |
| A04:2025 | `uygulama-guvenlik-siniri-anayasa` §4 | kripto sinir isareti | Kripto secimi `K009-security` + ADR-022 |
| A05:2025 | `cekirdek-guvenlik-anayasa` §5 | seccomp/capability ile enjeksiyon sinirlama | OWASP denetimi `K009-security` |
| A07:2025 | `yetki-ve-erisim-modeli-ornekler` §1 | oturum dosyasi izni siniri | Oturum is mantigi `K0100-identitiy` |
| A10:2025 | `uygulama-guvenlik-siniri-surucu` §4 | fail-closed / fail-open esikleri | OWASP denetimi `K009-security` |

## §10 — Olcum metodolojisi (nasil sayildi?)

| Adim | Komut/yontem | Beklenen cikti | Tarih |
|---|---|---|---|
| M1 | Katalog taramasi: `.ai/architecture/**/*.md` icindeki benzersiz URL sayimi | envanter oncesi N = 2097 | 2026-10-13 |
| M2 | Aday URL `Invoke-WebRequest -Method Head -UseBasicParsing -TimeoutSec 20` | HTTP 200 | 2026-10-13 |
| M3 | Aday URL'in envanterde olup olmamasi (hashtable kontrolu) | envanterde YOK | 2026-10-13 |
| M4 | Disk kaniti: `Select-Object -Index (N-1)` | satir icerigi birebir | 2026-10-13 |
| M5 | Negatif kanit: kaynak kodda OS araci arama | 0 isabet | 2026-10-13 |
| M6 | Satir sayimi: `Get-Content ... .Count` | icerik >=500, index 40–100 | 2026-10-13 |
| M7 | Frontmatter denetimi: `parent:` arama, `group` tirnak kontrolu | parent:=0, tirnakli group:=0 | 2026-10-13 |
| M8 | Beyazliste denetimi: `git status` disina cikan dosya | 0 | 2026-10-13 |

### §10.1 Olcum kisitlari (ne olculmez?)

| Kisit | Neden |
|---|---|
| Uygulama OWASP denetimi | `K009-security` dali sorumludur |
| Performans/latency olcumu | `isletim-sistemi-testing` dali sorumludur |
| Log toplama/analiz | `isletim-sistemi-observability` dali sorumludur |
| Runtime sandbox kurulumu | `isletim-sistemi-runtime` dali sorumludur |

## §11 — Risk kaydi (OS katmani yoklugundan dogan)

| # | Risk | Etki | Olasilik | Mevcut telafi | Sahip |
|---|---|---|---|---|---|
| RK1 | Uygulama katmani hatasi dogrudan ihlale donuser (tek savunma) | Yuksek | Orta | Katmanli uygulama ici kontroller (`realpath` + `escapeshellarg` + fail-closed) | S1 karari |
| RK2 | Surec hafizasi ifsasi icin hicbir katman yok | Yuksek | Dusuk | Deploy ortaminda ayri kullanici/calisma zamani kisiti [UNKNOWN] | S1 karari |
| RK3 | Yerel kotu niyetli kullaniciya karsi savunma yok | Orta | Dusuk | Sunucu erisim modeli [UNKNOWN] | S1 karari |
| RK4 | Surucu/donanim yuzeyi denetlenmiyor | Yuksek | Dusuk | Web uygulamasinda surucu kodu yok (repo olcumu 0) | `isletim-sistemi-kernel` |
| RK5 | Sinir (S2) netlesmedigi icin sorumluluk dagilir | Orta | Yuksek | §3.1 taslak matris + onay bekleniyor | S2 karari |
| RK6 | `.htaccess` katmani Apache disi ortamda devre disi kalir | Orta | Orta | PHP middleware ikinci katman (`SecurityHeadersMiddleware`) | `uygulama-guvenlik-siniri` |
| RK7 | Rate limiter cache cozulemezse auth uclari kapanir (hizmet kesintisi) | Orta | Dusuk | ADR-013 §5.4 — fail-closed bilincli tercih | `K009-security` |

> Bu tablo bir **karar degildir**; telafi sutunundaki `[UNKNOWN]` degerleri S1/S2 kapanisina baglidir.

## §12 — Sinir kontrol listesi (bu revizyon icin)

| # | Kontrol | Durum |
|---|---|---|
| C1 | 16 dosyanin tamami beyazliste icinde mi? | EVET |
| C2 | `git commit` atildi mi? | HAYIR — subagent yasagi, orkestratöre ait |
| C3 | `K009-security/**` dosyalarina girildi mi? | HAYIR |
| C4 | `K0100-identitiy/**` dosyalarina girildi mi? | HAYIR |
| C5 | `isletim-sistemi-{core,cross,kernel,observability,plan,runtime,testing}/**` dosyalarina girildi mi? | HAYIR |
| C6 | `isletim-sistemi-{api,audio,turu}/**` ve `electroncis/**` dosyalarina girildi mi? | HAYIR |
| C7 | `*.php` / `*.js` / `*.css` / `*.sql` yazildi mi? | HAYIR — yalniz okundu (kanit) |
| C8 | `.ai/architecture/index.md` yazildi mi? | HAYIR — yalniz orkestratör |
| C9 | Icerik dosyalari >=500 satir mi? | EVET (§2.4 + olcum raporu) |
| C10 | `index.md` dosyalari 40–100 satir mi? | EVET |
| C11 | S1/S2 [UNKNOWN] olarak kapandi mi? | HAYIR — acik tutuldu |
| C12 | Uydurma CVE/URL/API kullanildi mi? | HAYIR — her URL HTTP 200 ile dogrulandi |

## §13 — Sozluk (bu dosyada gecen terimler)

| Terim | Kisa tanim | Sahip dosya |
|---|---|---|
| DAC | Discretionary Access Control — sahiplik + rwx | `yetki-ve-erisim-modeli-anayasa` |
| MAC | Mandatory Access Control — merkezi etiket politikasi | `yetki-ve-erisim-modeli-anayasa` |
| RBAC | Role-Based Access Control — rolanda yetki | `yetki-ve-erisim-modeli-anayasa` |
| BEL | Bu vault icinde tanimi bulunamadi -> UNKNOWN | `yetki-ve-erisim-modeli-anayasa` §1 |
| ACL | Access Control List — dosya basina izin listesi | `yetki-ve-erisim-modeli-anayasa` §2 |
| capability | Root'un tek yetkisine bolunmus izin parcalari | `cekirdek-guvenlik-anayasa` |
| seccomp-BPF | Sistem cagrisi bazli filtre | `uygulama-guvenlik-siniri-anayasa` §2 |
| LSM | Linux Security Modules — cekirdek denetim kancasi | `cekirdek-guvenlik-anayasa` |
| sandbox | Sureci kisitli ortamda calistirma | `uygulama-guvenlik-siniri-anayasa` §2 |
| fail-closed | Belirsizlikte erisimi reddetme | `uygulama-guvenlik-siniri-surucu` §4 |
| S1 | OS katmani devreye alinacak mi sorusu | bu dosya §7 |
| S2 | Uygulama <-> OS sorumluluk siniri sorusu | bu dosya §7 |

## §14 — Alt-dal kapsam sözleşmelereri (hangi alt-dal neyi sahiplenir)

### §14.1 `cekirdek-guvenlik/` kapsam sözleşmesi

| # | Kapsar | Kapsamaz |
|---|---|---|
| G1 | Koruma halkaları (donanım → firmware → bootloader → çekirdek → kullanıcı alanı) | Uygulama katmanı OWASP denetimi (`K009-security`) |
| G2 | Güvenli önyükleme (Secure Boot, TPM, modül imzalama) — **hedef referans** | Donanım/firmware tasarımı (`K001-donanaım-type` + elektronik vault) |
| G3 | CPU mitigasyonları: SMEP/SMAP/KASLR/PAN — **hedef referans** | CPU mikro mimarisi tasarımı (donanım vault'u) |
| G4 | Sanallaştırma (VM-exit, hypervisor güvenlik halkası) — **hedef referans** | Bulut/sanallaştırma altyapı kurulumu (devops dalı) |
| G5 | Kernel exploit yüzeyi sınıflandırması (syscall, ioctl, ağ, dosya) | Çekirdek iç yapısı ve sürücü yazımı (`isletim-sistemi-kernel`) |
| G6 | Repo disk kanıtları: bu yüzeyde OS aracı **yokluğu** (negatif kanıt) | Sürücü kodu üretimi (`isletim-sistemi-kernel`) |
| G7 | S1 sorusu: OS katmanı devreye alınacak mı? — `[UNKNOWN]` | OS katmanı **seçimi/kurulumu** (mimari sahibi + ADR `>=088`) |

### §14.2 `yetki-ve-erisim-modeli/` kapsam sözleşmesi

| # | Kapsar | Kapsamaz |
|---|---|---|
| E1 | DAC / MAC / RBAC / BEL karşılaştırma çerçevesi (BEL = `UNKNOWN`) | Kimlik doğrulama, oturum, sifre, MFA (`K0100-identitiy`) |
| E2 | POSIX izinleri, umask, ACL girişleri | Uygulama RBAC politika tasarımı (`K009-security`) |
| E3 | Capability modeli (root yetkisinin parçalanması) — **hedef referans** | Capability'nin çekirdek içinde uygulanması (`isletim-sistemi-kernel`) |
| E4 | MAC politika türleri (SELinux/AppArmor/Landlock) — **hedef referans** | Politika dağıtımı/operasyonu (S1 kararına bağlı) |
| E5 | Dosya sistemi üzerindeki etiketler ve denetim akışı (syscall → VFS → DAC → ACL → MAC) | Log toplama/analiz altyapısı (`isletim-sistemi-observability`) |
| E6 | Repo disk kanıtları: `mkdir 0750`, cookie bayrakları, `PermissionMiddleware` | Oturum iş mantığı (login, fixation, MFA akışı — `K0100-identitiy`) |
| E7 | S2 sorusu: uygulama ↔ OS sorumluluk sınırı — `[UNKNOWN]` | Sınırın **onaylanması** (mimari sahibi kararı) |

### §14.3 `uygulama-guvenlik-siniri/` kapsam sözleşmesi

| # | Kapsar | Kapsamaz |
|---|---|---|
| U1 | Uygulama ↔ OS sınırı: syscall yüzeyi ve denetim noktaları | OWASP denetiminin yürütülmesi (`K009-security`) |
| U2 | Sandbox türleri (seccomp-BPF, bubblewrap, Job Object, sandbox-exec) — **hedef referans** | Sandbox kurulumu/operasyonu (`isletim-sistemi-runtime`) |
| U3 | Container / namespace / cgroup güvenlik kısıtları — **hedef referans** | Container altyapısı kurulumu (devops dalı) |
| U4 | Kullanıcı alanı ↔ çekirdek geçiş denetimi | Çekirdek içi denetim mekanizması (`isletim-sistemi-kernel`) |
| U5 | Repo disk kanıtları: `.htaccess`, middleware zinciri, `realpath`, `escapeshellarg`, fail-closed | Kripto algoritma seçimi (ADR-022, `K009-security`) |
| U6 | Başarısızlık modları: fail-closed / fail-open eşikleri | Rate limit politika kararı (ADR-013, `K009-security`) |
| U7 | S1 + S2 her ikisi de bu alt-dalı etkiler — ikisi de `[UNKNOWN]` | Karar üretimi (mimari sahibi + ADR `>=088`) |

> **Sözleşme kuralı (üç alt-dal için bağlayıcı):** "Kapsamaz" sütunundaki her satır ilgili
> komşu dala aittir; bu daldan o konuda **tanım, süreç veya karar** üretilmez. İhlal
> tespit edilirse içerik ilgili dala taşınır (§5.2 ihlal işaretleri).

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
