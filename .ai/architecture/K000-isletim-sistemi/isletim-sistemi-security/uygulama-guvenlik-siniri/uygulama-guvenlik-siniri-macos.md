---
node_id: K000-isletim-sistemi/isletim-sistemi-security/uygulama-guvenlik-siniri/uygulama-guvenlik-siniri-macos
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-security/uygulama-guvenlik-siniri
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-security/uygulama-guvenlik-siniri/uygulama-guvenlik-siniri-anayasa
  - K000-isletim-sistemi/isletim-sistemi-security/uygulama-guvenlik-siniri/uygulama-guvenlik-siniri-surucu
evidence:
  - https://developer.apple.com/documentation/security/app-sandbox
  - https://developer.apple.com/documentation/security/hardened-runtime
  - https://developer.apple.com/documentation/endpointsecurity
  - https://developer.apple.com/documentation/systemextensions
  - https://developer.apple.com/library/archive/documentation/Security/Conceptual/AppSandboxDesignGuide/Introduction/Introduction.html
  - https://developer.apple.com/documentation/security/authorization_services
  - https://support.apple.com/en-us/102445
  - home.coremusic.net/include/Stream/MusicStreamHandler.php:189
  - home.coremusic.net/include/Stream/MusicStreamHandler.php:200
  - shared/src/AI/AIEngine.php:292
  - shared/src/Middleware/CsrfMiddleware.php:42
validation:
  - "2026-10-14: 6 kaynak URL HEAD 200 ile dogrulandi; icerezler exa/webfetch (JSON primaryContent) ile okundu; repo sandbox araci taramasi 729 dosyada 0 isabet (negatif kanit)."
open_questions:
  - "S1: OS guvenlik katmani hedef mimaride devreye alinacak mi? [UNKNOWN]"
  - "S2: Uygulama <-> OS guvenlik sorumluluk siniri? [UNKNOWN]"
group: kod
---

# Uygulama Güvenlik Sınırı — macOS OS Kartı

## §0 ELI15

Bu kart, uygulamanın **macOS ile sınırına** koyacağı mekanizmaları taşır:
App Sandbox (entitlement ile istenen kaynağa erişim kısıtı), Hardened Runtime
(+ SIP ile kod enjeksiyonu/DLL hijacking bellek tahrifatı engeli), Endpoint
Security (sistem olaylarını **onaylayan/izleyen** kullanıcı-uzayı istemcisi),
system extension modeli ve Gatekeeper/notarization kapısı. Hepsi "uygulama ne
kadar yüzey görür, ne kadar olayı kendi adına onaylar" sorusunu yanıtlar.

> **Öncelikli gerçek:** Repoda `sandbox-exec`/`Seatbelt`/`seccomp`/`bwrap`/
> `no_new_privs`/`com.apple.security.*`/`.entitlements` çağırısına dair
> **0 isabet** vardır (729 dosya, `vendor`/`node_modules`/`.git` hariç,
> 2026-10-14 — negatif kanıt). Sınır şu an **yalnızca uygulama katmanında**
> duruyor: `realpath` + `str_starts_with` (path daraltma), `escapeshellarg`
> (shell argüman kaçışı), `hash_equals` (timing-safe token) — bunlar macOS
> sandbox'ı **değildir** (§3).

İki upstream sınır-uyarısı bu kartın omurgasıdır: (1) Authorization Services
**App Sandbox içinde desteklenmez** (privilege escalation) — §1; (2) Hardened
Runtime **notarization için zorunludur** ve yaygın olmayan yetenekleri (JIT
gibi) varsayılan olarak kapatır — §1. S1/S2 açık kalır (§6).

## §1 macOS sınır profili

| Mekanizma | Ne yapar | Bu alt-dal açısı | Güven düzeyi |
|---|---|---|---|
| App Sandbox | Sistem kaynaklarına ve kullanıcı verisine koruma; erişim **yalnızca entitlement ile istenen** kaynaklara kısıtlanır; Mac App Store üzerinden dağıtım için zorunludur | uygulama ↔ kaynak sınırı | Yüksek — App Sandbox 2026-10-14 okundu |
| Hardened Runtime + SIP | Birlikte çalışma zamanı bütünlüğünü korur: kod enjeksiyonu, DLL hijacking ve **süreç bellek alanı tahrifatı** sınıfı istismarları engellenir; Xcode'da Signing & Capabilities'ten açılır | çalışma zamanı sınırı | Yüksek — Hardened Runtime 2026-10-14 okundu |
| Hardened Runtime kısıtları | Yaygın olmayan yetenekleri (ör. JIT) varsayılan kapatır; ancak gerekiyorsa **tek tek runtime-exception entitlement** ile açılır; yetkiler yalnız çalıştırıcıya eklenir (kütüphane/cerceve/plug-in host'un yetkisini devralır); macOS, Hardened Runtime istisna entitlement'ı kullanan system extension'ları **yüklemeyi reddeder** (non-DEXT hariç) | kısıt ↔ istisna dengesi | Yüksek — Hardened Runtime 2026-10-14 okundu |
| Notarization önkoşulu | macOS uygulamasını notarize etmek için **Hardened Runtime etkin olmak zorundadır** | dağıtım öncesi kapı | Yüksek — Hardened Runtime 2026-10-14 okundu |
| Endpoint Security | Sistem olaylarını izlemek için **C API**; istemci kayıt olur ve **bekleyen olayları onaylar** ya da gerçekleşen olay bildirimlerini alır: süreç çalıştırma, dosya sistemi mount, fork, sinyal yükseltme; system extension olarak paketlenip uygulamayla kurulur | olay **onay yüzeyi** (uygulama ↔ çekirdek olayları) | Yüksek — Endpoint Security 2026-10-14 okundu |
| System extensions | Düşük seviyeli kod **kullanıcı uzayında** çalışır; çekirdek uzantısı (KEXT) yerine yüksek ayrıcalık alır ama çekirdek güvenliğini paylaşmaz; kurulum/kaldırma uygulama paketi üzerinden runtime'da yapılır | çekirdek yüzeyini kullanıcı uzayına taşıma | Yüksek — SystemExtensions 2026-10-14 okundu |
| Authorization Services ↔ sandbox çelişkisi | Doküman "Important": API **App Sandbox içinde desteklenmez**, çünkü privilege escalation'a izin verir | **sınır-üstü çelişki** — sandbox kuran tasarım bu API'yı kullanamaz | Yüksek — Authorization Services 2026-10-14 okundu |
| Gatekeeper / notarization | macOS yalnızca güvenilir yazılımın çalışmasını hedefler; Developer ID imzası + notarization + ilk açılış onayı | çalıştırma öncesi son kapı | Yüksek — Safely open apps 2026-10-14 okundu |
| App Sandbox Design Guide (arşiv) | Sandbox tasarımı için upstream kılavuz (arşiv sayfası) — kapsam/akış referansı | tasarım referansı | **Orta** — arşiv içeriği bu turda **okunmadı**, yalnızca sayfa varlığı HTTP 200 ile doğrulandı → içerik iddiası yazılmadı |

**Okuma:** 9 satırın 7'i 2026-10-14'te dokümanın kendisinden okunmuştur; 1'i
(Design Guide) yalnız varlık düzeyinde doğrulandığı için içerik iddiası
taşımaz, 1'i (Authorization Services) başka kartın çakışma işareti olarak
buraya sınır olarak alınmıştır.

## §2 Kod örnekleri

> **UYARI (zorunlu):** 3 bloğun hiçbiri bu repodan alınmamıştır; tamamı
> `kaynak: upstream docs` ile imzalı **sözleşmesel** referans kalıplarıdır
> (çalıştırılabilir komut **değildir**); repoda karşılığı yoktur (§3).

### §2.1 — App Sandbox erişim sözleşmesi (referans)

```text
# kaynak: upstream docs - security/app-sandbox
# kural: kaynak erisimi yalnizca entitlement ile ISTENIR; istenmeyen kaynak erisilemez
# dagitim: Mac App Store uygulamalari icin App Sandbox capability ZORUNLUDUR
# sinir: sandbox, Authorization Services API ile BIRLIKTE CALISMAZ
#         (upstream "Important": API privilege escalation'a izin verir)
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel) | YOK — `com.apple.security.*` / `.entitlements` grep = 0 (2026-10-14) |

### §2.2 — Hardened Runtime etkinleştirme ve istisna kuralı (referans)

```text
# kaynak: upstream docs - security/hardened-runtime
# 1) Xcode: hedef -> Signing & Capabilities -> "+" -> Hardened Runtime
# 2) JIT gibi sinirlanan bir yetenek gerekiyorsa tek tek runtime-exception
#    entitlement ACILIR; "yalnizca mutlak gerekli olanlar" (docs uyarisi)
# 3) degeri false olan Boolean yetki imzaya EKLENMEZ
# 4) notarize etmek icin Hardened Runtime acik OLMAK ZORUNDADIR
# 5) koruma: kod enjeksiyonu / DLL hijacking / bellek alani tahrifati
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel) | YOK — notarization/Hardened Runtime adımı repoda 0 |

### §2.3 — Endpoint Security kayıt sözleşmesi (referans)

```text
# kaynak: upstream docs - endpointsecurity
# 1) system extension icinde istemci KAYIT OLUR (C API; native cagri destekleyen dil)
# 2) mod secimi: bekleyen olayi ONAYLA  VEYA  gerceklesen olay bildirimini AL
# 3) olay siniflari (docs): surucu calistirma (process exec), dosya sistemi mount,
#    fork, sinyal yukseltme
# 4) paketleme: system extension + onu kur/kaldir eden uygulama bundle'i
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; sözleşmesel) | YOK — `EndpointSecurity`/`es_client` grep = 0 |

## §3 CoreMusic repo durumu

Tarama: repo kökü, `*.php,*.js,*.css,*.sql,*.sh,*.conf,*.yml,*.yaml`,
`vendor`/`node_modules`/`.git` hariç — **729 dosya**;
desen = `sandbox-exec|Seatbelt|sandbox_init|codesign|spctl|csrutil|tccutil|notariz|com.apple.security|.entitlements|seccomp|bwrap|bubblewrap|setfacl|getfacl|capset|capget|chroot|pivot_root|EndpointSecurity|systemextensions|kextload|SMAppService` (2026-10-14).

| Yüzey | Kanıt yöntemi | Sonuç (2026-10-14) |
|---|---|---|
| `sandbox-exec` / Seatbelt / `sandbox_init` profili | grep 22 desen | **YOK** — 729 dosya / 0 isabet (negatif kanıt) |
| seccomp / `no_new_privs` / `bwrap` (Linux eşdeğerleri dahil) | grep | **YOK** (negatif kanıt) |
| Endpoint Security istemcisi / system extension | grep | **YOK** (negatif kanıt) |
| Entitlement / notarization / kod imzası adımı | grep | **YOK** (negatif kanıt) |
| path daraltma — `MusicStreamHandler.php:189` | idx 188 birebir | `$real = realpath($trimmed);` — uygulama katmanı; **macOS sandbox değildir** |
| sınır önleme — `MusicStreamHandler.php:200` | idx 199 birebir | `if (!str_starts_with($real, $prefix)) {` — uygulama katmanı prefix denetimi |
| shell kaçışı — `AIEngine.php:292` | idx 291 birebir | `escapeshellarg($filePath)` — uygulama katmanı; shell yüzeyini daraltır, **syscall/olay yüzeyini değil** |
| token karşılaştırma — `CsrfMiddleware.php:42` | idx 41 birebir | `!hash_equals($sessionToken, (string)$submitted)` — uygulama katmanı (timing-safe) |
| session dizin izni — `SessionConfig.php:20,35` | idx 19/34 birebir | `0750` — **uygulama katmanının OS'a tek teması** (DAC; §4 sınırı) |
| uygulama güvenlik başlıkları — `.htaccess:10` | idx 9 birebir | `X-Frame-Options "DENY"` — tarayıcı katmanı (K009), OS sandbox **değildir** |

**Ölçüm (yeniden üretilebilir):**

```powershell
$src = Get-ChildItem 'C:\www\coremusic.net' -Recurse -File -Include *.php,*.js,*.css,*.sql,*.sh,*.conf,*.yml,*.yaml |
  Where-Object { $_.FullName -notmatch '\\vendor\\|\\node_modules\\|\\\.git\\' }   # beklenti: 729
($src | Select-String -Pattern 'sandbox-exec|Seatbelt|EndpointSecurity|com\.apple\.security|\.entitlements').Count  # beklenti: 0
```

**Okuma:** 10 satırın 4'ü negatif kanıt, 5'i uygulama katmanı kanıtı, 1'i
tarayıcı katmanıdır. Sınır katmanı şu an **OS'ta değil, PHP'dedir**;
App Sandbox/Hardened Runtime/Endpoint Security katmanı repoda **hiç
başlamamıştır** — bu, S1'in dayanağıdır.

## §4 Sınır cümleleri

> **K010 ayrimi (baglayici):** Kimlik dogrulama, oturum, sifre ve MFA -> `K010-identitiy`; bu kartta yalnizca macOS dosya/surec/erisim kisitlari (or. session dizini 0750) ele alinir.
> **K009 ayrimi (baglayici):** OWASP denetimi, auth/CSRF/CSP tasarimi, kripto secimi (ADR-022) -> `K009-security`; bu kart yalnız OS katmani + sinir isaretlerini tasinir.
> **Komşu dal:** Cekirdek ici yapisi ve surucu yazimi -> `isletim-sistemi-kernel`; macOS platform geneli -> `isletim-sistemi-turu/macos`.

Ek sınır: `realpath`/`str_starts_with`/`escapeshellarg`/`hash_equals` bu kartın
**OS kanıtı değildir** — uygulama katmanı savunmalarıdır; onları "sandbox" diye
adlandırmak abartıdır. Tersi de doğrudur: bu kart PHP auth/CSRF/CSP tasarımını
taşımaz (K009). Kext→system extension geçişi ve SIP/KIP halkası
`cekirdek-guvenlik-macos`, DAC/entitlement/TCC `yetki-ve-erisim-modeli-macos`
kartlarındadır; bu kart yalnız **uygulamanın macOS'a sınırını** taşır.

## §5 Referans

| URL | İddia | 2026-10-14 durum |
|---|---|---|
| https://developer.apple.com/documentation/security/app-sandbox | Sandbox, erişimi entitlement ile istenen kaynaklarla sınırlar; Mac App Store dağıtımı için zorunlu | HTTP 200 (HEAD) · içerik okundu |
| https://developer.apple.com/documentation/security/hardened-runtime | Hardened Runtime + SIP → kod enjeksiyonu/DLL hijacking/bellek tahrifatı engeli; JIT istisnası; yalnız çalıştırıcıya yetki; notarization önkoşulu; system extension reddi | HTTP 200 · içerik okundu |
| https://developer.apple.com/documentation/endpointsecurity | C API ile olay izleme; istemci kayıt olur → bekleyen olayı onaylar veya bildirim alır; process exec/mount/fork/sinyal olayları; system extension paketleme | HTTP 200 · içerik okundu |
| https://developer.apple.com/documentation/systemextensions | Kullanıcı uzayında uzantı; KEXT yerine yüksek ayrıcalık; bundle ID/Team ID kuralları; App Store veya notarization | HTTP 200 · içerik okundu |
| https://developer.apple.com/documentation/security/authorization_services | *"not supported within an App Sandbox"* — sandbox ↔ privilege escalation çelişkisi (sinir işareti) | HTTP 200 · içerik okundu |
| https://support.apple.com/en-us/102445 | Gatekeeper; Developer ID imza denetimi; Catalina+ varsayılan notarization; ilk açılış onayı | HTTP 200 · içerik okundu |
| https://developer.apple.com/library/archive/documentation/Security/Conceptual/AppSandboxDesignGuide/Introduction/Introduction.html | App Sandbox tasarım kılavuzu (arşiv) — **yalnız sayfa varlığı** doğrulandı, içerik iddiası yazılmadı | HTTP 200 (HEAD) |

> **Reddedilen adaylar (yazılmadı):** `.../security/implementing-network-security-for-servers`
> → **404** (2026-10-14) · `.../security/preventing-lock-screen-app-launches`
> → **404** · `.../technotes/tn3147-inside-code-signing` → **404**. Ayrıca
> `sandbox_init`/`sandbox-exec` man sayfası için doğrulanmış resmi URL bu turda
> bulunamadı → **hiçbir `sandbox-exec` komut kalıbı üretilmedi**
> (`⚠️ VERIFICATION REQUIRED`).

## §6 Açık sorular

| # | Soru | Durum |
|---|---|---|
| S1 | OS güvenlik katmanı (App Sandbox / Hardened Runtime / Endpoint Security / notarization) hedef mimaride devreye alınıp alınmayacağı | **[UNKNOWN]** — bu kartta KAPATMA YOK; mimari sahibi + yeni ADR (`>=088`) gerektirir |
| S2 | Uygulama ↔ OS güvenlik sorumluluk sınırı (uygulama kontrolleri nerede biter, macOS sandbox/olay onayımı nerede başlar) | **[UNKNOWN]** — bu kartta KAPATMA YOK; §3'teki "sınır PHP'de" bulgusu bu sorunun girdisidir, kararı değildir |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
