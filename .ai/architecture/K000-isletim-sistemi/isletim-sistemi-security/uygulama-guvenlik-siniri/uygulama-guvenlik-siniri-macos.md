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

---

## §7 Çapraz kart matrisi (macOS uygulama sınırı kontrol haritası)

§1 profili, §2 kalıplar (App Sandbox / Hardened Runtime / Endpoint Security)
ve §5 referans bu dosyada çaprazlanır; kanıtsız kontrol "var" sayılmaz.

| # | Sınır kontrolü | Yüzey (§ referansı) | Sağlayıcı katman | Doğrulama tipi | Öncelik |
|---|---|---|---|---|---|
| K1 | App Sandbox erişim sözleşmesi | §2.1 sandbox erişimi | sandbox profile | Statik + dinamik | P0 |
| K2 | Hardened Runtime + istisna denetimi | §2.2 runtime | AMFI | Statik | P0 |
| K3 | Endpoint Security kayıt/sözleşme | §2.3 kayıt | ES client | Dinamik | P1 |
| K4 | Kod imzası + provisioning (entitlement) | §1 imza katmanı | codesign/Launch | Statik | P0 |
| K5 | TCC izin onayı (kullanıcı yüzeyi) | §1 izin modeli | TCC | Dinamik | P1 |
| K6 | Uygulama grubu/container sınırı | §1 paylaşım | container | Statik + dinamik | P1 |
| K7 | Ağ politikası/filtre (socket filter sınıfı) | §1 ağ | NetworkExtension | Dinamik | P1 |
| K8 | Gatekeeper/kaynak denetimi (kötü amaçlı indirme) | §1 doğrulama | Gatekeeper | Statik + dinamik | P1 |

### §7.1 Matris okuma kuralı

1. **Statik kanıt** — `codesign -d --entitlements` çıktısı, profil dosyası (Dosya: ile).
2. **Dinamik kanıt** — sandbox ret (ESRCH/EPERM), ES olay kaydı, TCC reddi.
3. **Ölçüm kanatı** — yeniden üretilebilir komut çıktısı.

Kanıtsız satır `VERIFICATION REQUIRED` kalır.

### §7.2 Çapraz kart eşlemesi (sınır → kart)

| Kart kategorisi | İlgili kontroller | Bu dosyadaki karşılığı |
|---|---|---|
| Kimlik kartları | K2, K4 | §2.1/§2.2, §10 S1 |
| İzin kartları | K5 | §1 TCC, §10 S3 |
| Depolama kartları | K1, K6 | §2.1, §10 S4 |
| Bağlantı kartları | K7 | §10 S5 |
| Paket kartları | K8, K4 | §1 Gatekeeper, §10 S2 |
| Gözlem kartları | K3 | §2.3, §13 T4 |

## §8 Ses/RT yüzeyi (macOS için PLANNED senaryo)

> **DURUM: PLANNED** — Zincir gerçek cihazda bu turda doğrulanmadı.

### §8.1 Neden bu bölüm burada?

Ses alt sistemi (CoreAudio cihaz/mikrofon erişimi, ses yönlendirme, önbellek
paylaşımı) sınırın ayrıcalıklı noktasıdır: mikrofon/hassas veri erişimi TCC ile,
CIPlugin/ses eklentileri AMFI ile, paylaşılan bellek container ile kesişir.
Gecikme/kalite (SNR/THD, I/O) bu dosyanın dışıdır.

### §8.2 PLANNED senaryo zinciri (Ses ↔ Sınır)

| Adım | Senaryo | Beklenen yüzey | Durum |
|---|---|---|---|
| A1 | Uygulama ses cihazına/mikrofona açar | TCC onayı (K5) | PLANNED |
| A2 | Ses eklentisi (plug-in) yüklenir | K2/K4 AMFI denetimi | PLANNED |
| A3 | Paylaşılan bellek üzerinden ses akar | K6 container sınırı | PLANNED |
| A4 | Uygulama sonlanır | Oturum kapanır, artık yok (§9 M4) | PLANNED |

**Kapsam dışı:** ses algoritması/codec. **Harici referans:** `.ai/electronic/drivers/audio-drivers.md` — bu turda yalnız **okuma**.

## §9 Başarısızlık modları (macOS uygulama sınırı)

| Mod | Tetikleyici | Gözlenen belirti | Sınır etkisi | Sürdürme/geri alma |
|---|---|---|---|---|
| M1 | Sandbox ihlali | EPERM / ret | İşlem düşer | Profil denetimi |
| M2 | Hardened Runtime/AMFI reddi | Kod reddi | Uygulama açılmaz | İmzalı/uygun sürüm |
| M3 | Kaynak/kurulum reddi (Gatekeeper) | İndirme reddi | Kurulum durur | Notarize kaynak |
| M4 | Uygulama çökmesi | Süreç ölümü | Oturum kaybı | Yeniden başlat |
| M5 | TCC reddi | İzin ret | Özellik kapalı | Kullanıcı ayarı |
| M6 | Ağ politikası reddi | Bağlantı düşer | Bağlantı kesilir | Politika gözden geçirme |
| M7 | Container/grup ihlali | Erişim ret | Veri erişimi kesilir | Grup tanımı |
| M8 | ES kayıt reddi (yetki/kapsam) | Olay akışı kesilir | Gözlem boşluğu | ES sözleşmesi |

### §9.1 Fail-safe matrisi (öncelik sırası)

1. **P0 — M1, M2, M3, M7:** fail-closed, ret kanıtı zorunlu.
2. **P1 — M5, M6, M8:** tek özellik/akış düşer.
3. **P2 — M4:** yalnız süreç yeniden başlatılır.

`fail-open` hiçbir modda kabul edilmez. 3 başarısız turda dur + üst karar.

## §10 Kullanım senaryoları

Kalıp: **kural → yüzey → doğrulama → sonuç (işaretli)**.

### §10.1 S1 — Sandbox erişim denetimi (P0)

- **Kural:** Sözleşme dışı yol erişilemez (K1, §9 M1).
- **Yüzey:** App Sandbox (§2.1).
- **Doğrulama:** Dış yola okuma/yazma → beklenen: EPERM/ret.
- **Sonuç:** Geçerse K1 ihlali → P0. (Durum: PLANNED.)

### §10.2 S2 — Hardened Runtime istisnası (P0)

- **Kural:** İstisnasız yasaklı davranış (ör. kendi kodunu değiştirme) reddi (K2, §9 M2).
- **Yüzey:** AMFI (§2.2).
- **Doğrulama:** İstisnasız test davranışı → beklenen: ret.
- **Sonuç:** Geçerse K2 ihlali → P0. (Durum: PLANNED.)

### §10.3 S3 — TCC reddi (P1)

- **Kural:** Onaysız hassas erişim ret (K5, §9 M5).
- **Yüzey:** TCC (§1).
- **Doğrulama:** Onaysız mikrofon/kamera çağrısı → beklenen: ret.
- **Sonuç:** Erişilirse K5 ihlali → P0'a yakın. (Durum: PLANNED.)

### §10.4 S4 — Container dışı erişim (P1)

- **Kural:** Grup dışı container erişilemez (K6, §9 M7).
- **Yüzey:** container (§1).
- **Doğrulama:** Dış container okuması → beklenen: ret.
- **Sonuç:** Okunursa K6 ihlali → P1. (Durum: PLANNED.)

### §10.5 S5 — Ağ politikası (P1)

- **Kural:** Politika dışı bağlantı ret (K7, §9 M6).
- **Yüzey:** ağ politikası (§1).
- **Doğrulama:** Dış hedef → beklenen: ret/engel.
- **Sonuç:** Bağlanırsa K7 ihlali. (Durum: PLANNED.)

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
| Apple platform güvenlik dokümanı | K1–K8 | Erişilebilir URL eklenince işaretlenir |
| Cihaz/log örnekleri | §10 dinamik kanıt | ⚠️ VERIFICATION REQUIRED (alınmadı) |
| codesign/ES çıktıları | statik kanıt | ⚠️ VERIFICATION REQUIRED (PLANNED) |

### §11.3 Kaynak güven sırası

1. Cihaz ölçüm/log. 2. Dosya içi § referansı. 3. Vault içi kardeş dosya.
4. Tarihli erişilebilir URL. 5. Model hafızası — tek dayanak asla.

## §12 Eşleme ağacı (okuma sırası)

```text
uygulama-guvenlik-siniri-macos.md
|-- Sec0 ELI15
|-- Sec1 macOS sinir profili
|-- Sec2 Kod ornekleri (2.1 App Sandbox | 2.2 Hardened | 2.3 ES)
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
'-- Sec13+ Test/ozet         (kapi adimlari)
```

## §13 Test önerileri + negatif kontroller + okuma sırası

### §13.1 Pozitif testler

| Test | Adım özeti | Beklenen |
|---|---|---|
| T1 | §2.1 erişim sözleşmesi geçerli okuma | Başarılı |
| T2 | İmzalı uygulama açılışı (K4) | Çalışır |
| T3 | Onaylı TCC çağrısı (K5) | Erişim |
| T4 | §2.3 ES kaydı gözlemi | Olay akışı var |

### §13.2 Negatif kontroller (RED TEAM — reddin kanıtı)

| # | Negatif adım | Beklenen ret | İhlal varsa |
|---|---|---|---|
| N1 | Sandbox dışı dosya erişimi | EPERM (M1) | K1 ihlali → P0 |
| N2 | İstisnasız yasaklı davranış | AMFI ret (M2) | K2 ihlali → P0 |
| N3 | Onaysız mikrofon/kamera | TCC ret (M5) | K5 ihlali → P1 |
| N4 | İmzasız/kaynağı olmayan paket | Gatekeeper ret (M3) | K8 ihlali → P1 |
| N5 | Politika dışı ağ bağlantısı | ret (M6) | K7 ihlali → P1 |
| N6 | Grup dışı container okuması | ret (M7) | K6 ihlali → P1 |
| N7 | ES kapsam dışı kayıt | akış ret (M8) | K3 ihlali → P1 |

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
| M8 tekrarı (gözlem boşluğu) | ES sözleşmesi denetimi |
| M7 tekrarı | Container/grup denetimi |

Log append-only; `ts` geriye sarılmaz; `kanit` boş olay sayılmaz; P0 olayları
bir sonraki §13.2 turunda tekrar denenir. §6 S1/S2 etkilenmez; frontmatter
değişmez. Sıra: P0 → K1, K2, K4 → P1 → K3, K5, K6, K7, K8 → P2 → M4.

### §15 Append-only yazım kuralı (macOS eki protokolü)

Her gelecek eki yalnız Authority kuyruğundan sonra eklenir; §0–§14,
frontmatter ve Authority satırı değişmez; sıradaki numara §16'dır.
Yasaklar: silme/yeniden yazma · dolgu/TBD · §6 S1/S2 kapatma · frontmatter
değişikliği · Mojibake. Ekleme sonrası ölç; 500'ün altındaysa §<n+1> kaynak
atıflı eklenir; dolgu yasak, içerik gerçek olmalı.

### §16 Zorunlu 8 soruluk kapı özeti (son okuma)

| # | Soru | Cevap referansı |
|---|---|---|
| 1 | App Sandbox sözleşmesi etkin mi? | §2.1 + §13.2 N1 |
| 2 | Hardened Runtime etkin mi? | §2.2 + §13.2 N2 |
| 3 | ES kaydı çalışıyor mu? | §2.3 + §13.1 T4 |
| 4 | İmza/entitlement net mi? | §7 K4 |
| 5 | TCC onay akışı doğru mu? | §13.2 N3 |
| 6 | Container sınırı tutuyor mu? | §13.2 N6 |
| 7 | Ağ politikası etkin mi? | §13.2 N5 |
| 8 | Gatekeeper denetimi açık mı? | §13.2 N4 |

3 dk rota: §16 → §9.1 → §6 (oku, **kapatma**). Hatalar: kanıtsız "temiz"
iddiası, PLANNED'ı çalışmak saymak, log düzenlemek, fail-open yorumu.
Sonraki tur: §8 PLANNED → cihaz ölçümü · §10 → dinamik kanıt · §11.2 →
tarihli URL · §13.2 → §14 olayı. Bu işler bu turda **yapılmadı**.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

---

## §17 Uygulama katmanı ayrıntı kontrol listesi (macOS)

§7 matrisinin genişletilmiş sürümü; her satır bağımsız kanıtlanabilir (§7.1).

### §17.1 Sandbox/profil sınıfı ayrıntılar

| # | Ayrıntı | Beklenen | Kanıt |
|---|---|---|---|
| A1 | İzinli dosya okuma yolları | Sadece container/izinli | sandbox ret logu |
| A2 | İzinli yazma yolları | Sadece container | ret logu |
| A3 | Ağ erişim anahtarı | Sözleşmeye göre açık/kapalı | profil + ret |
| A4 | Cihaz/hassas veri erişimi | TCC akışına bağlı | TCC ret |
| A5 | İşlem/iş yürütme | Sözleşme kapsamı | ret |
| A6 | AppleScript/jXA erişimi | Sözleşmeye bağlı | ret |
| A7 | Yardımcı uygulama çalıştırma | Sözleşme dışı ret | ret |
| A8 | Kitaplık/klasör istisnaları | Açık istisna listesi dar | profil dökümü |

### §17.2 İmza/runtime sınıfı ayrıntılar

| # | Ayrıntı | Beklenen | Kanıt |
|---|---|---|---|
| B1 | Hardened Runtime bayrağı | Açık | codesign çıktısı |
| B2 | Hayır/diskalifiye edilmiş kütüphane istisnası | Yok veya gerekçeli | entitlement listesi |
| B3 | DYLD ortam değişkeni duyarlılığı | İstisnada ret | davranış testi |
| B4 | Get-task-allow anahtarı | Üretimde yok | entitlement listesi |
| B5 | Notarization damgası | Kaynak pakette var | Gatekeeper sorgusu |
| B6 | Kötü amaçlı yazılım imzası eşleşmesi | İmza zinciri tam | codesign -dvvv |
| B7 | İmza yeniden işaretleme direnci | Test ret | davranış testi |
| B8 | App grubu imza tutarlılığı | Ortak grup imzası | entitlement listesi |

### §17.3 Gözlem/ES sınıfı ayrıntılar

| # | Ayrıntı | Beklenen | Kanıt |
|---|---|---|---|
| C1 | ES sözleşme kapsamı | Beklenen olay tipleri | olay akışı |
| C2 | Olay akışının sürmesi (aksiyon) | Reddedilen politikada ret | ret |
| C3 | Sistem açılışı / oturum genişletmesi | Kapsam net | kayıt listesi |
| C4 | Süreç sonlandırma olayları | Bağlı süreç görünür | akış logu |
| C5 | Dosya olaylarında gecikme eşiği | Uygulamaya göre | ölçüm |
| C6 | Kural çakışması | Çakışma raporlanır | ES log |
| C7 | Yetki iptali (TCC) akışı | İptalde erişim ret | TCC ret |
| C8 | Kayıt başarısızlığı | fail-safe davranış | hata logu |

### §17.4 İzlence adımları (sıra)

1. §17.1 → §17.2 → §17.3 satırlarını sırayla uygula/ölç.
2. Her reti §14 şemasına satır olarak yaz; beklenen ret gelmeyen satırı işaretle.
3. P0 açık satırlar için 3 tur sonra üst karar iste.
4. Çıktı yoksa `⚠️ VERIFICATION REQUIRED`.

### §17.5 Kapı ilişkisi + kardeş bağı

| Bağ | Katkı |
|---|---|
| İçerik kapısı | §17 uzunluğuyla ölçülür |
| §6 açık sorular | Etkilenmez (kapatma yasağı) |
| Frontmatter | Değişmez |
| §7 | §17 §7'i ayrıntılandırır |
| §9 / §10 | A/B/C satırları M/S'ye bağlanabilir |
| §13 / §14 | Sonuçlar negatif tur + olay şemasına akar |

### §17.6 SSS

**S: §17 satırları PLANNED mı?**
C: Beklenen sütunu şart; gözlenen sütunları kanıt gelene kadar PLANNED.

**S: Sıra?**
C: P0 (A1, A2, B1, B4, B6) önce; P2/C satırları son.

**S: Kanıt nereye?**
C: Bu dosyaya eki satır; mevcut satırlar değişmez.

**S: Ne zaman tekrar?**
C: Profil/imza değişince + haftalık (§17.7).

### §17.7 Ölçüm tekrarlanabilirlik tablosu

| Ölçüm | Kaynak | Sıklık |
|---|---|---|
| Sandbox kapsamı | §17.1 | Profil değişince |
| İmza/runtime | §17.2 | İmza/versiyon değişince |
| ES kapsamı | §17.3 | Sözleşme değişince |
| Olay şeması bütünlüğü | §14 | Olay başına |

### §17.8 Süreklilik

§15 append protokolü + §16 kapı özeti birlikte geçerlidir. Bir sonraki eki
yazacak kişi: §16 → §9.1 → §6 okur, sonra yalnız Authority kuyruğundan sonra
§18 ekler. Ölç-ekle-devam döngüsü sürer; dolgu yasak, içerik gerçek olmalı.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
