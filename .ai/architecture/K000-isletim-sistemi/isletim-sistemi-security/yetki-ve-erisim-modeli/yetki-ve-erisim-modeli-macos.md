---
node_id: K000-isletim-sistemi/isletim-sistemi-security/yetki-ve-erisim-modeli/yetki-ve-erisim-modeli-macos
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-security/yetki-ve-erisim-modeli
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-security/yetki-ve-erisim-modeli/yetki-ve-erisim-modeli-anayasa
  - K000-isletim-sistemi/isletim-sistemi-security/yetki-ve-erisim-modeli/yetki-ve-erisim-modeli-surucu
evidence:
  - https://pubs.opengroup.org/onlinepubs/9699919799/functions/chmod.html
  - https://developer.apple.com/documentation/security/authorization_services
  - https://developer.apple.com/documentation/bundleresources/entitlements
  - https://developer.apple.com/documentation/technotes/tn3127-inside-code-signing-requirements
  - https://developer.apple.com/documentation/xcode/resetting-access-to-protected-resources-in-macos
  - https://developer.apple.com/documentation/devicemanagement/privacypreferencespolicycontrol
  - https://support.apple.com/guide/security/system-integrity-protection-secb7ea06b49/1/web/1
  - shared/src/Session/SessionConfig.php:20
  - shared/src/Session/SessionConfig.php:35
validation:
  - "2026-10-14: 7 kaynak URL HEAD 200 ile dogrulandi; icerezler exa/webfetch ile okundu; repo DAC/ACL/AUTH araci taramasi 729 dosyada 0 isabet (negatif kanit)."
open_questions:
  - "S1: OS guvenlik katmani hedef mimaride devreye alinacak mi? [UNKNOWN]"
  - "S2: Uygulama <-> OS guvenlik sorumluluk siniri? [UNKNOWN]"
group: kod
---

# Yetki ve Erişim Modeli — macOS OS Kartı

## §0 ELI15

Bu kart, macOS'ta **"kim ne yapabilir"** sorusunun mekanizmalarını taşır:
POSIX DAC (`chmod` izin bitleri + setuid/setgid), Authorization Services
(`Security.Authorization` → Security Server politika veritabanı), entitlement
(imza içine gömülü yetki anahtarı), kod imzası **requirement**'ları, TCC korumalı
kaynak izinleri (kullanıcı onayı + `tccutil` sıfırlama) ve MDM üzerinden TCC
politikası yönetimi.

> **Öncelikli gerçek:** Repoda `chmod(`/`umask(`/`chown(`/`setfacl`/
> `AuthorizationCreate` çağrısına dair **0 isabet** vardır (729 dosya,
> `vendor`/`node_modules`/`.git` hariç, 2026-10-14 — negatif kanıt). Uygulamanın
> OS'a temas eden tek satır çifti `SessionConfig.php:20` ve `:35`tir: session
> dizini `0750` — işte bu bu kartın **tek pozitif repo kanıtıdır** ve doğrudan
> POSIX DAC'dır.

Repoda uygulama katmanı erişim kontrolleri vardır (`realpath` + `str_starts_with`,
`hash_equals`, `PermissionMiddleware` rol denetimi) ama bunlar **OS katmanı
değildir** — §3'te sınıfı açıkça ayrıştırılır. S1/S2 açık kalır (§6).

## §1 macOS erişim kontrol profili

| Mekanizma | Ne yapar | Bu alt-dal açısı | Güven düzeyi |
|---|---|---|---|
| POSIX DAC (`chmod` izin bitleri) | `S_IRWXU/S_IRWXG/S_IROTH...` bitleri + `S_ISUID`/`S_ISGID`/`S_ISVTX`; süreç efektif UID'i dosya sahibiyle eşleşmeli **veya** uygun yetkiye sahip olmalı (POSIX `chmod` spesifikasyonu) | temel yetki katmanı | Yüksek — OpenGroup chmod 2026-10-14 okundu |
| Ownership (dosya sahibi/grup) | Aynı POSIX `chmod` sözleşmesi: sahiplik + izin bitleri birlikte hesaplanır; macOS bu modeli Unix dosya sisteminde aynen uygular | "kimin" hesabı | Yüksek — OpenGroup chmod 2026-10-14 okundu |
| Authorization Services (`Security.Authorization`) | Security Server ve **politika veritabanı**na program arayüzü; OS'un kısıtlı alanlarına erişimi yönetir; uygulama kimlik bilgilerine **hiç dokunmadan** doğrulama yapar (Security Agent arayüz kullanır); kimlik doğrulama yöntemi ileride değişse bile uygulama değişmez | yetki **karar zinciri** (uygulama → Security Server) | Yüksek — Authorization Services 2026-10-14 okundu |
| Authorization Services sınırı | Doküman bizzat uyarır: API **App Sandbox içinde desteklenmez**, çünkü privilege escalation'a izin verir | sandbox ↔ yetki **çelişkisi** (sinir işareti) | Yüksek — aynı doküman "Important" notu |
| Entitlement | Bir çalıştırıcıya verilen **hak/ayrıcalık**; key-value çifti olarak kod imzasının içine gömülür; Xcode'da `.entitlements` dosyasıyla tanımlanır, imzada birleştilir | imzalı yetki envanteri | Yüksek — Entitlements 2026-10-14 okundu |
| Kod imzası requirement | Verilen bir kod imzası üzerinde **Boolean döndüren fonksiyon** (klasik ifade sözdizimi); signing identifier (imzalayanın kodu tanımlayan dizesi) ≠ signing identity (sertifika + özel anahtar) ≠ code identity (aynı kod kavramı) | kod kimliği denetimi | Yüksek — TN3127 2026-10-14 okundu |
| TCC korumalı kaynak izinleri | İlk erişimde sistem kullanıcıya sorar; kullanıcı/grant kararı **hatırlanır**; `tccutil reset <servis> [bundle-id]` ile sıfırlanır, tüm hesaplar için `sudo` gerekir; her korumalı kaynağın bir servis adı vardır | kullanıcı onaylı erişim (macOS) | Yüksek — Xcode "Resetting access" 2026-10-14 okundu |
| TCC → MDM (PrivacyPreferencesPolicyControl) | `com.apple.TCC.configuration-profile-policy` payload tipi; uygulama bazında servis allow/deny; `CodeRequirement` ile kod imzası eşleşmesi; macOS cihaz kanalı, **user-approved MDM** şartı | merkezi politika dağıtımı | Yüksek — Device Management 2026-10-14 okundu |
| Mandatory access control (MAC) | Kullanıcı tarafından **aşılamayan** geliştirici politikaları; DAC'tan (kullanıcı değiştirebilir) farklıdır; sandboxing/extensions/SIP'i besler | sınır işareti — detay `cekirdek-guvenlik-macos` kartında | Yüksek (aktarım — SIP sayfası 2026-10-14 okundu) |
| Denetim sırası (syscall → DAC → imza → TCC → MAC) | Kavramsal zincir: çağrı → yol/izin bitleri → kod imzası requirement → kullanıcı onayı → politika | bu kartın sorusu (S2) | ⚠️ **VERIFICATION REQUIRED** — evreler tek tek kaynaklıdır (chmod/TN3127/TCC/SIP); evreler arası **birebir sıralama bu turda doğrudan okunmadı** → iddia üretilmedi |

> **Dürüstlük notu:** Son satır bilinçli olarak `⚠️ VERIFICATION REQUIRED`
> ile işaretlidir; sıralamayı **uydurmak** yerine boş bırakılmıştır. Ayrıca
> `man7.org` `attributes(7)` sayfası dosya izni **değil**, POSIX fonksiyon
> güvenliği (`MT-Safe`) kavramıdır — rwx kaynağı olarak **kullanılmamıştır**
> (önceki Linux kartında da aynı tuzağa dikkat çekilmiştir).

## §2 Kod örnekleri

> **UYARI (zorunlu):** 3 bloğun hiçbiri bu repodan alınmamıştır; tamamı
> `kaynak: upstream docs` ile imzalı referans kalıplarıdır. 1. blok repodaki tek
> pozitif kanıtla (`0750`) **aynı katmanı** (DAC) gösterir, aynı dosya değildir.

### §2.1 — TCC izin sıfırlama kalıbı (referans)

```sh
# kaynak: upstream docs - resetting-access-to-protected-resources-in-macos
# her korumalik kaynagin bir SERVIS adi vardir; varsayilan etki yalniz calisan kullanici
tccutil reset Calendar            # servis adi -> o kaynaga TUM uygulamalari tekrar sorar
tccutil reset All <bundle-id>     # tek uygulamanin TUM izinlerini sifirlar (docs: All + bundle ID)
sudo tccutil reset Calendar       # tum kullanici hesaplari icin (docs: sudo)
# not: cikti bicimleri bu turda okunmadi -> calistirilmaz, uydurulmaz
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans) | YOK — `tccutil` grep'i 0 isabet (2026-10-14) |

### §2.2 — Entitlement üretimi/imzası kalıbı (referans)

```text
# kaynak: upstream docs - bundleresources/entitlements
# 1) yetkiler .entitlements UZANTILI plist dosyasinda tanimlanir (Xcode Capabilities)
# 2) imzalama sirasinda Xcode: entitlement dosyasi + gelistirici hesabi + proje bilgisi
#    birlestirilip imzaya uygulanir
# 3) degeri false olan Boolean yetki IMZAYA EKLENMEZ (yardimci kural: yalniz true ekle)
# 4) yetkiler yalniz CALISTIRICIYA aittir; kutuphane/cerceve/plug-in host'un yetkilerini devralir
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel) | YOK — `.entitlements` glob / `com.apple.security.*` grep = 0 |

### §2.3 — Authorization Services çağrı sözleşmesi (referans)

```text
# kaynak: upstream docs - security/authorization_services
# cagri: AuthorizationCreate(...) -> AuthorizationRef (opak referans)
# ornek: AuthorizationFree(...)   -> referansi serbest birakir
# arkapis: Security Server (daemon) + politika veritabanı; Security Agent kimlik
#          dogrulama arayuzunu surer -> uygulama sifre/metin asla eline gecmez
# sinir (docs "Important"): API App Sandbox icinde DESTEKLENMEZ (privilege escalation)
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel — çalıştırılabilir değil) | YOK — `AuthorizationCreate` grep'i 0 isabet |

## §3 CoreMusic repo durumu

Tarama: repo kökü, `*.php,*.js,*.css,*.sql,*.sh,*.conf,*.yml,*.yaml`,
`vendor`/`node_modules`/`.git` hariç — **729 dosya**;
desen1 = `chmod\(|umask\(|chown\(|setfacl|getfacl|AuthorizationCreate|AuthorizationCopyRights|SecCodeCheckValidity` → **0**;
desen2 = `sandbox-exec|Seatbelt|seccomp|bwrap|no_new_privs|com\.apple\.security\.|.entitlements|codesign|spctl` → **0** (2026-10-14).

| Yüzey | Kanıt yöntemi | Sonuç (2026-10-14) |
|---|---|---|
| `chmod(`/`umask(`/`chown(` çağrısı | grep desen1, 729 dosya | **YOK** — 0 isabet (negatif kanıt) |
| ACL / capability aracı (`setfacl`, `setcap`) | grep desen1 | **YOK** (negatif kanıt) |
| Authorization Services çağrısı (`AuthorizationCreate`) | grep desen1 | **YOK** (negatif kanıt) |
| Entitlement dosyası / `com.apple.security.*` | grep desen2 | **YOK** (negatif kanıt) |
| Kod imzası aracı (`codesign`, `spctl`) | grep desen2 | **YOK** (negatif kanıt) |
| session dizin izni sabiti — `SessionConfig.php:20` | idx 19 birebir | `public const DIR_PERMISSIONS = 0750;` — **uygulama katmanının OS'a tek teması** (POSIX DAC: sahibi rwx, grup r-x, diğer `---`) |
| session dizin oluşturma — `SessionConfig.php:35` | idx 34 birebir | `@mkdir($savePath, 0750, true);` — uygulama katmanı (mkdir izni OS'ta `umask` ile birlikte uygulanır) |
| path çözümleme — `MusicStreamHandler.php:189` | idx 188 birebir | `$real = realpath($trimmed);` — **uygulama katmanı**, OS DAC'si değildir |
| sınır önleme — `MusicStreamHandler.php:200` | idx 199 birebir | `if (!str_starts_with($real, $prefix)) {` — uygulama katmanı |
| token karşılaştırma — `CsrfMiddleware.php:42` | idx 41 birebir | `!hash_equals($sessionToken, (string)$submitted)` — uygulama katmanı (timing-safe) |
| rol denetimi — `PermissionMiddleware.php:31` | idx 30 birebir | `if ($requiredRole !== null && $userRole !== $requiredRole) {` — **uygulama katmanı** RBAC; UID/GID/ACL karşılığı yok |
| yetki reddi — `PermissionMiddleware.php:40` | idx 39 birebir | `return $this->forbidden('permission_denied', ...)` — uygulama katmanı |

**Okuma:** 12 satırın 5'i negatif kanıt, 2'si tek çiftlik pozitif DAC kanıtı
(`0750`), 5'i uygulama katmanıdır. `0750` dışındaki hiçbir OS yetki mekanizması
(imza requirement, entitlement, TCC, Authorization Services) bu repoda
**başlamamıştır**.

## §4 Sınır cümleleri

> **K010 ayrimi (baglayici):** Kimlik dogrulama, oturum, sifre ve MFA -> `K010-identitiy`; bu kartta yalnizca macOS dosya/surec/erisim kisitlari (or. session dizini 0750) ele alinir.
> **K009 ayrimi (baglayici):** OWASP denetimi, auth/CSRF/CSP tasarimi, kripto secimi (ADR-022) -> `K009-security`; bu kart yalnız OS katmani + sinir isaretlerini tasinir.
> **Komşu dal:** Cekirdek ici yapisi ve surucu yazimi -> `isletim-sistemi-kernel`; macOS platform geneli -> `isletim-sistemi-turu/macos`.

Ek sınır: `hash_equals`/`realpath`/`str_starts_with`/`PermissionMiddleware` bu
kartın kanıtı **değildir** — onlar uygulama katmanı kontrolleridir; burada
yalnızca "OS'a temas etmiyor" diye sınıfı işaretlenir. SIP/KIP/SSV detayı
`cekirdek-guvenlik-macos`, sandbox/olay denetimi `uygulama-guvenlik-siniri-macos`
kartlarındadır. TCC kullanıcı onayı bir **kimlik doğrulama akışı değildir**
(K010) — burada yalnızca erişim izni mekanizması olarak taşınır.

## §5 Referans

| URL | İddia | 2026-10-14 durum |
|---|---|---|
| https://pubs.opengroup.org/onlinepubs/9699919799/functions/chmod.html | `chmod` izin bitleri + `S_ISUID`/`S_ISGID`; efektif UID sahiplik şartı (POSIX) | HTTP 200 (HEAD) · içerik okundu |
| https://developer.apple.com/documentation/security/authorization_services | `Security.Authorization` → Security Server + politika veritabanı; kimlik bilgisi uygulamaya geçmez; **App Sandbox içinde desteklenmez** (privilege escalation) | HTTP 200 · içerik okundu |
| https://developer.apple.com/documentation/bundleresources/entitlements | Entitlement = kod imzasına gömülü key-value hak; `.entitlements` dosyası; imzada birleşme; yalnız çalıştırıcıya ait | HTTP 200 · içerik okundu |
| https://developer.apple.com/documentation/technotes/tn3127-inside-code-signing-requirements | Requirement = kod imzası üzerinde Boolean fonksiyon; signing identifier / identity / code identity ayrımı; Xcode veya `codesign` ile imzalama | HTTP 200 · içerik okundu |
| https://developer.apple.com/documentation/xcode/resetting-access-to-protected-resources-in-macos | İlk erişimde onay + hatırlama; `tccutil reset <servis>`; `All` ile tümü; tek uygulama için bundle ID; tüm hesaplar için `sudo` | HTTP 200 · içerik okundu |
| https://developer.apple.com/documentation/devicemanagement/privacypreferencespolicycontrol | `com.apple.TCC.configuration-profile-policy` payload; servis bazlı allow; `CodeRequirement`; macOS cihaz kanalı; user-approved MDM | HTTP 200 · içerik okundu |
| https://support.apple.com/guide/security/system-integrity-protection-secb7ea06b49/1/web/1 | MAC ≠ DAC ayrımı; kullanıcı tarafından aşılamayan politikalar; SIP'i besleyen altyapı | HTTP 200 · içerik okundu |

> **Reddedilen adaylar (yazılmadı):** `.../bundleresources/protecting_the_user_s_privacy_for_macos`
> → **404** · `.../bundle-resources/protecting-the-user-s-privacy-for-macos`
> → **404** · `.../authorization_services/concepts/access-control-concepts`
> → **404** · `.../bundleresources/adding-an-entitlement-to-an-app` → **404**
> (hepsi 2026-10-14). TCC içerik ihtiyacı bu yüzden `resetting-access...`
> + `privacypreferencespolicycontrol` sayfalarından karşılandı; uydurma yol
> üretilmedi.

## §6 Açık sorular

| # | Soru | Durum |
|---|---|---|
| S1 | OS güvenlik katmanı (entitlement/imza requirement/TCC/Authorization Services) hedef mimaride devreye alınıp alınmayacağı | **[UNKNOWN]** — bu kartta KAPATMA YOK |
| S2 | Uygulama ↔ OS güvenlik sorumluluk sınırı (nereye kadar uygulama RBAC, nereden itibaren OS DAC/imza/TCC) — §1 son satırdaki sıralama da bu soruya bağlıdır | **[UNKNOWN]** — bu kartta KAPATMA YOK |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
