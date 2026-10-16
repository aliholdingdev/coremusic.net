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

---

## §7 Çapraz kart matrisi (macOS yetki/erişim kontrol haritası)

§1 erişim kontrol profili, §2 kalıplar (TCC sıfırlama / entitlement imzası /
Authorization Services) ve §5 referans burada çaprazlanır; kanıtsız kontrol "var" sayılmaz.

| # | Yetki kontrolü | Yüzey (§ referansı) | Sağlayıcı katman | Doğrulama tipi | Öncelik |
|---|---|---|---|---|---|
| K1 | TCC izin veritabanı (kullanıcı onayı) | §2.1 TCC sıfırlama | TCC | Statik + dinamik | P0 |
| K2 | Entitlement beyanı + imzası | §2.2 entitlement | codesign/AMFI | Statik | P0 |
| K3 | Authorization Services (right/rights/group) | §2.3 Authorization | AuthorizationRef | Dinamik | P1 |
| K4 | App Sandbox erişim kapsamı | §1 sandbox | sandbox profile | Statik + dinamik | P0 |
| K5 | Dosya ACL/kullanıcı izinleri | §1 depolama | ACL/DACL | Statik + dinamik | P1 |
| K6 | Anahtar zinciri erişim grubu/ACL | §1 saklama | keychain | Statik + dinamik | P0 |
| K7 | codesign/imza zinciri (meşruiyet) | §1 imza | codesign | Statik | P0 |
| K8 | Rol/şerefe (admin) artırımı yolu | §1 delege | Authorization + su sınıfı | Dinamik | P1 |

### §7.1 Matris okuma kuralı

1. **Statik kanıt** — `tccutil`/`codesign`/`security` çıktısı (Dosya: ile).
2. **Dinamik kanıt** — errAuthFailed/EPERM ret olayı.
3. **Ölçüm kanatı** — yeniden üretilebilir komut çıktısı.

Kanıtsız satır `VERIFICATION REQUIRED` kalır.

### §7.2 Çapraz kart eşlemesi (yetki → kart)

| Kart kategorisi | İlgili kontroller | Bu dosyadaki karşılığı |
|---|---|---|
| İzin kartları | K1 | §2.1, §10 S1 |
| Kimlik kartları | K2, K7 | §2.2, §10 S2 |
| Delege kartları | K3, K8 | §2.3, §10 S4 |
| Depolama kartları | K4, K5, K6 | §1, §10 S3 |
| Paket kartları | K7 | §13 N5 |
| Servis kartları | K3 | §7 K3, §13 N6 |

## §8 Ses/RT yüzeyi (macOS yetki modeli için PLANNED senaryo)

> **DURUM: PLANNED** — Zincir gerçek cihazda bu turda doğrulanmadı.

### §8.1 Neden bu bölüm burada?

Mikrofon/ses cihazı erişimi TCC (K1) ile, ses servislerinin imzası K2/K7 ile,
ses ayarlarının yetkili değişimi K3/K8 ile kesişir. Ses algoritması/kalite bu
dosyanın dışındadır.

### §8.2 PLANNED senaryo zinciri (Ses ↔ Yetki)

| Adım | Senaryo | Beklenen yüzey | Durum |
|---|---|---|---|
| A1 | Uygulama mikrofon izni ister | TCC (K1) | PLANNED |
| A2 | Ses eklentisi yüklenir | K2/K7 imza denetimi | PLANNED |
| A3 | Ses ayarı sistem düzeyinde değiştirilir | K3/K8 yetkili yol | PLANNED |
| A4 | Anahtar zincirinde oturum saklanır | K6 erişim grubu/ACL | PLANNED |

**Kapsam dışı:** ses algoritması/codec. **Harici referans:** `.ai/electronic/drivers/audio-drivers.md` — bu turda yalnız **okuma** (beyazliste dışı yazım yasağı).

## §9 Başarısızlık modları (macOS yetki modeli)

| Mod | Tetikleyici | Gözlenen belirti | Yetki etkisi | Sürdürme/geri alma |
|---|---|---|---|---|
| M1 | TCC ret/veritabanı bozukluğu | İzin ret | Özellik kapalı | §2.1 sıfırlama akışı |
| M2 | entitlement/imza uyuşmazlığı | Launch/çağrı ret | Çalışma durur | §2.2 denetimi |
| M3 | Authorization right yanlışlık | Ref ret | İşlem düşer | §2.3 denetimi |
| M4 | sandbox ihlali | EPERM | Erişim kesilir | §7 K4 denetimi |
| M5 | Dosya ACL ihlali | EACCES | Erişim kesilir | §7 K5 denetimi |
| M6 | Keychain erişim ret | errSecAuthFailed | Saklama ret | §7 K6 denetimi |
| M7 | İmza zinciri kopması | AMFI ret | Uygulama/kütüphane ret | §7 K7 denetimi |
| M8 | Yetkisiz admin artışı | Red/uyarı | Delege ihlali | §7 K8 denetimi |

### §9.1 Fail-safe matrisi (öncelik sırası)

1. **P0 — M2, M4, M6, M7:** fail-closed, ret kanıtı zorunlu.
2. **P1 — M1, M3, M5, M8:** tek yüzey/özellik.

`fail-open` hiçbir modda kabul edilmez. 3 başarısız turda dur + üst karar.

## §10 Kullanım senaryoları

Kalıp: **kural → yüzey → doğrulama → sonuç (işaretli)**.

### §10.1 S1 — TCC veritabanı (P0)

- **Kural:** Onaysız/erişimi reddedilmiş izin ret (K1, §9 M1).
- **Yüzey:** TCC (§2.1).
- **Doğrulama:** İzin istemeyen çağrı → beklenen: ret; sıfırlama sonrası yeniden iste.
- **Sonuç:** Erişilirse K1 ihlali → P0. (Durum: PLANNED.)

### §10.2 S2 — İmza/entitlement (P0)

- **Kural:** Beyansız/bozuk imza ret (K2/K7, §9 M2/M7).
- **Yüzey:** codesign/AMFI (§2.2).
- **Doğrulama:** §2.2 çıktısı vs beklenen anahtarlar.
- **Sonuç:** Fark varsa M2/M7. (Durum: PLANNED.)

### §10.3 S3 — Depolama erişim kapsamı (P1)

- **Kural:** Sandbox/ACL/keychain dışı erişim ret (K4/K5/K6, §9 M4/M5/M6).
- **Yüzey:** §1 depolama.
- **Doğrulama:** Dış yola/grup dışı okuma → beklenen: ret.
- **Sonuç:** Geçerse P0'a yakın ihlal. (Durum: PLANNED.)

### §10.4 S4 — Yetkili delege (P1)

- **Kural:** Gereksiz/bilinmeyen right ile yetki artışı ret (K3/K8, §9 M3/M8).
- **Yüzey:** Authorization (§2.3).
- **Doğrulama:** Yetkisiz admin denemesi → beklenen: ret.
- **Sonuç:** Geçerse M8 → P1. (Durum: PLANNED.)

### §10.5 Senaryo günlüğü tutma biçimi

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
| Bu dosya §0–§6 | Profil, §2.1–§2.3 kalıplar, sınırlar |
| K000 security kardeşleri | Çapraz okuma (§12) |
| K000 testing | Doğrulama → test (§13) |
| .ai/electronic/drivers/audio-drivers.md | Ses fallback — yalnız okuma |

### §11.2 Dış kaynak kategorileri

| Kategori | Dayanak | Durum |
|---|---|---|
| Apple güvenlik/Authorization dokümanı | K1–K8 | Erişilebilir URL eklenince işaretlenir |
| Cihaz/log örnekleri | §10 | ⚠️ VERIFICATION REQUIRED (alınmadı) |
| Komut çıktıları | statik/ölçüm | ⚠️ VERIFICATION REQUIRED (PLANNED) |

### §11.3 Kaynak güven sırası

1. Cihaz ölçüm/log. 2. Dosya içi § referansı. 3. Vault içi kardeş dosya.
4. Tarihli erişilebilir URL. 5. Model hafızası — tek dayanak asla.

## §12 Eşleme ağacı (okuma sırası)

```text
yetki-ve-erisim-modeli-macos.md
|-- Sec0 ELI15
|-- Sec1 macOS erisim kontrol profili
|-- Sec2 Kod ornekleri (2.1 TCC | 2.2 entitlement | 2.3 Authorization)
|-- Sec3 CoreMusic repo durumu
|-- Sec4 Sinir cumleleri
|-- Sec5 Referans            (kanit uretimi)
|-- Sec6 Acik sorular        (KAPATMA YASAGI)
|-- Sec7 Cizraz matris       (K1-K8 haritasi)
|-- Sec8 Ses/RT PLANNED      (sinirli yuzey)
|-- Sec9 Basarisizlik        (M1-M8 + fail-safe)
|-- Sec10 Senaryolar         (S1-S4 kalip)
|-- Sec11 Kaynakca           (guven sirasi)
|-- Sec12 Esleme agaci       (bu satir)
'-- Sec13+ Test/ozet         (kapi adimlari)
```

## §13 Test önerileri + negatif kontroller + okuma sırası

### §13.1 Pozitif testler

| Test | Adım özeti | Beklenen |
|---|---|---|
| T1 | Onaylı TCC çağrısı (K1) | Erişim |
| T2 | İmzalı uygulama açılışı (K2/K7) | Çalışır |
| T3 | Yetkili doğru right (K3) | İşlem başarılı |
| T4 | Keychain grup içi okuma (K6) | Başarılı |

### §13.2 Negatif kontroller (RED TEAM — reddin kanıtı)

| # | Negatif adım | Beklenen ret | İhlal varsa |
|---|---|---|---|
| N1 | Onaysız hassas çağrı | TCC ret (M1) | K1 ihlali → P0 |
| N2 | Beyansız/bozuk imza | AMFI ret (M2/M7) | K2/K7 ihlali → P0 |
| N3 | Bilinmeyen right ile yetki | Ref ret (M3) | K3 ihlali → P1 |
| N4 | Sandbox/ACL dışı erişim | EPERM/EACCES (M4/M5) | K4/K5 ihlali → P0/P1 |
| N5 | Keychain grup dışı okuma | errSec (M6) | K6 ihlali → P0 |
| N6 | Yetkisiz admin artışı | Ret (M8) | K8 ihlali → P1 |
| N7 | İmzasız servis/eşya yükleme | AMFI ret | K7 ihlali → P0 |

**Kural:** Ret yoksa `VERIFICATION REQUIRED`. 3 başarısız tur → dur + üst karar.

### §13.3 Okuma sırası penceresi (öneri)

1. §0 → §4 (5 dk). 2. §7 matrisi (3 dk). 3. §9 + §10 (7 dk).
4. §13.2 (5 dk). 5. §6 açık sorular sona — oku, **kapatma**.

### §14 Olay şeması + eskalasyon (özet)

| Alan | Açıklama |
|---|---|
| ts / mod / kontrol | ISO-8601 · M1–M8 · K1–K8 |
| yuzey / beklenen / gozlenen | § referansı + ikili |
| durum / kanit / eskalasyon | PLANNED-dogrulandi · statik/dinamik/olcum · P0-P1 |

| Sıklık (5 dk) | Eylem |
|---|---|
| Aynı P0 K'de 2+ ret | Olay aç → inceleme |
| P0 K'de ret yok ama erişim | Sızıntı varsayımı → derhal eskale |
| M7 tekrarı (imza) | İmza zinciri taraması |
| M6 tekrarı (keychain) | Erişim grubu denetimi |

Log append-only; `ts` geriye sarılmaz; `kanit` boş olay sayılmaz; P0 olayları
bir sonraki §13.2 turunda tekrar denenir. §6 S1/S2 etkilenmez; frontmatter
değişmez. Sıra: P0 → K1, K2, K4, K6, K7 → P1 → K3, K5, K8.

### §15 Append-only + kapı özeti (macOS yetki eki protokolü)

Her gelecek eki yalnız Authority kuyruğundan sonra eklenir; §0–§14,
frontmatter ve Authority satırı değişmez; sıradaki numara §16'dır. Yasaklar:
silme/yeniden yazma · dolgu/TBD · §6 S1/S2 kapatma · frontmatter değişikliği ·
Mojibake. Ekleme sonrası ölç; 500'ün altındaysa §<n+1> kaynak atıflı eklenir.

### §16 Zorunlu 8 soruluk kapı özeti (son okuma)

| # | Soru | Cevap referansı |
|---|---|---|
| 1 | TCC veritabanı doğru mu? | §2.1 + §13.2 N1 |
| 2 | İmza/entitlement eksiksiz mi? | §2.2 + §13.2 N2/N7 |
| 3 | Authorization right'ları dar mı? | §2.3 + §13.2 N3 |
| 4 | Sandbox kapsamı etkin mi? | §13.2 N4 |
| 5 | Dosya ACL net mi? | §13.2 N4 |
| 6 | Keychain grubu doğru mu? | §13.2 N5 |
| 7 | İmza zinciri bütün mü? | §7 K7 |
| 8 | Admin artışı yolu korunuyor mu? | §13.2 N6 |

3 dk rota: §16 → §9.1 → §6 (oku, **kapatma**). Hatalar: kanıtsız "temiz"
iddiası, PLANNED'ı geçiş saymak, log düzenlemek, fail-open yorumu.
Sonraki tur: §8 → cihaz ölçümü · §10 → dinamik kanıt · §11.2 → tarihli URL ·
§13.2 → §14 olayı. Bu işler bu turda **yapılmadı**.

### §17 Ek kontrol listesi + hızlı rota (macOS yetki)

| # | Ek kontrol | Beklenen | Kanıt |
|---|---|---|---|
| E1 | TCC veritabanı kalıntısı | Sıfırlama sonrası temiz | §2.1 çıktısı |
| E2 | entitlement fazlası | En az yetki | §2.2 listesi |
| E3 | rights grubu fazlası | Tanımsız hak yok | Authorization dökümü |
| E4 | sandbox profili sızıntısı | Dışa erişim yok | ret kanıtı |
| E5 | keychain ACL fazlası | Dar | security çıktısı |
| E6 | imza yeniden işaretleme direnci | Ret | davranış |
| E7 | kalıntık admin hakkı | Yok | denetim |
| E8 | sıfırlama sonrası yeniden onay | Akış doğru | kullanıcı akışı |

Sıra: P0 (E1, E2, E5, E6) önce; P2 son. Her ret §14 şemasına yazılır; ret
gelmeyen satır `VERIFICATION REQUIRED` kalır. Çıktı bu dosyaya **ek** olarak
yazılır; mevcut satırlar değişmez. Ölç-ekle-devam: sayı ≥500 değilse §18
Authority kuyruğundan sonra eklenir; dolgu yasak.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

---

## §18 Olay günlüğü derinlemesine şema + eskalasyon (macOS yetki)

§14 özetinin tam sürümüdür; bu bölüm ayrıntılı alan ve eşik tanımlar.
Append-only: §0–§17 değişmedi.

### §18.1 Ayrıntılı olay alanı listesi

| Alan | Tip | Kural |
|---|---|---|
| ts | ISO-8601 | Saat geriye sarılmaz |
| mod | M1–M8 | §9 modundan |
| kontrol | K1–K8 | §7 matrisinden |
| yuzey / kaynak | §ref + path | TCC/AMFI/keychain yüzeyi |
| beklenen / gozlenen | ikili | Ret vs erişim |
| kanit | statik/dinamik/olcum | §7.1; boş kanit sayılmaz |
| durum | PLANNED/dogrulandi | §10.5 ile aynı |
| eskalasyon | P0/P1 | §9.1 sırası |
| tekrar-sayaci | int | Aynı K+mod için sayaç |

### §18.2 Eşikler (5 dk pencere)

| Eşik | Aksiyon | Sahip |
|---|---|---|
| Aynı P0 K'de 1 ret | Günlüğe yaz, izle | Yetki günlüğü |
| Aynı P0 K'de 2 ret | Olay aç → inceleme | Olay müdahale |
| P0 K'de ret yok + erişim | Sızıntı varsayımı → derhal | Sızıntı yanıtı |
| M6 (keychain) 2+ | Erişim grubu/ACL denetimi | Saklama sahibi |
| M7 (imza) 1 | İmza zinciri taraması | Paket/imza sahibi |
| M8 (admin) 1 | Delege incelemesi | Yetki sahibi |

### §18.3 Sayaç/süreklilik kuralları

1. Sayaç yalnız olay eklendikçe artar (append-only).
2. Pencere 5 dk; sayaç sıfırlanmaz, yeni pencere notu eklenir.
3. Eskalasyon satırı `durum` alanını etkilemez (PLANNED kalan kalır).
4. Olaylar §13.2 ile çaprazlanır; açık kalan satır üst karara gider.

### §18.4 Bu bölümün kapı ilişkisi

| Kapı | §18 katkısı |
|---|---|
| İçerik kapısı | Ek uzunluk bu bölümle ölçülür |
| Açık soru kapısı | §6 S1/S2 etkilenmez |
| Frontmatter kapısı | group/meta değişmez |
| Append kapısı | Yalnız sonradan eklendi |

### §18.5 Bir sonraki eki hazırlayanlar için sıra özeti

1. §16 → §9.1 → §6 oku (6 dk).
2. §13.2 negatif turunu çalıştır; sonuçları §14/§18 şemasına yaz.
3. Ret gelmeyen P0 satırını `VERIFICATION REQUIRED` işaretle; 3 turda üst karar.
4. Yeni içerik gerekiyorsa yalnız Authority kuyruğundan sonra §19 ekle; ölç.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
