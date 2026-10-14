---
node_id: K000-isletim-sistemi/isletim-sistemi-security/yetki-ve-erisim-modeli/yetki-ve-erisim-modeli-webos
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-security/yetki-ve-erisim-modeli
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-security/yetki-ve-erisim-modeli/yetki-ve-erisim-modeli-anayasa
  - K000-isletim-sistemi/isletim-sistemi-security/yetki-ve-erisim-modeli/yetki-ve-erisim-modeli-surucu
evidence:
  - https://webosose.org/docs/guides/development/configuration-files/security-guide/
  - https://webosose.org/docs/guides/development/configuration-files/appinfo-json/
  - https://webosose.org/docs/guides/development/configuration-files/services-json/
  - https://webosose.org/docs/guides/getting-started/introduction-to-ls2-api/
  - shared/src/Middleware/CsrfMiddleware.php:42
  - shared/src/Session/SessionConfig.php:20
validation:
  - "2026-10-14: URL'ler erisim ile dogrulandi; kod ornekleri kaynak: upstream docs imzali; repo OS araci yokligu 2026-10-14 taramasinda 0 isabet."
open_questions:
  - "S1: OS guvenlik katmani hedef mimaride devreye alinacak mi? [UNKNOWN]"
  - "S2: Uygulama <-> OS guvenlik sorumluluk siniri? [UNKNOWN]"
group: kod
---

# Yetki ve Erişim Modeli — webOS OS Kartı

## §0 ELI15

webOS OSE'de yetki iki eksenli çalışır: **ACG** (Access Control Group) hangi API
yönteminin hangi gruba ait olduğunu söyler, **Trust Level** (`dev` / `part` / `oem`)
ise o grubun kimler tarafından kullanılabileceğini. Luna Bus (LS2) her istekte hem
üreticinin hem istemcinin değerlerine bakar ve eşleşmeyi reddeder. Bu kart, bu
modeli resmi Security Guide + appinfo.json + services.json sayfalarından
**birebir** alır ve CoreMusic repoda buna karşılık gelen yetki satırının olup
olmadığını ölçer.

> **Oncelikli gercek:** webOS yetki modeli **tamamen uygulama-paketi + Luna Bus
> yapılandırması** üzerinden kurulur: `appinfo.json`, role/api/groups/perm dosyaları.
> Repomuzda bunların **hiçbiri yok** (negatif kanıt, 2026-10-14) — CoreMusic'in
> yetkisi PHP middleware katmanında yaşar (`CsrfMiddleware.php:42`), OS düzeyinde
> değil. Bu kart "webOS modeli" ile "repo gerçeği"ni yan yana koyar, birini
> diğerinin yerine geçirmez.

## §1 webOS güvenlik profili

| Mekanizma | Ne yapar | Bu alt-dal açısı | Güven düzeyi |
|---|---|---|---|
| Trust Level (`dev`/`part`/`oem`) | `dev`: tüm seviyeler erişir · `part`: `part`+`oem` · `oem`: yalnız `oem` | yetki **hiyerarşisi** (matris dokümanda tablo) | Yüksek · HTTP 200 |
| ACG (Access Control Group) | Her LS2 metodunun bir ACG değeri vardır; istemcinin ACG'si üreticiyle uyuşmazsa Luna Bus reddeder | **erişim grubu** tanımı | Yüksek · HTTP 200 |
| `appinfo.json → requiredPermissions` | Uygulamanın kullandığı API'lerin ACG adlarını dizi olarak beyan eder | uygulama beyanı (**istek listesi**) | Yüksek · HTTP 200 |
| Role file (`/var/luna-service2/roles.d/`) | `exeName`/`appId`, `type` (`regular`/`privileged`/`devmode`), `trustLevel`, `allowedNames`, `permissions` (inbound/outbound) | **kayıt anında** yetki tanımı | Yüksek · HTTP 200 |
| API permission file (`api-permissions.d/`) | Metot adını ACG değerine eşler; "One method can belong to multiple ACGs" | **metot → grup** eşlemesi | Yüksek · HTTP 200 |
| Groups file (`groups.d/`) | ACG değerine Trust Level atar (ör. `acgvalue1: [dev]`) | **grup → seviye** eşlemesi | Yüksek · HTTP 200 |
| Client permission file (`client-permissions.d/`) | İstemcinin ihtiyaç duyduğu ACG listesi; "All clients ... must have a client permission file" | **istemci** yetki dosyası | Yüksek · HTTP 200 |
| Yapılandırma dizinleri | `roles.d` · `services.d` · `api-permissions.d` · `client-permissions.d` · `groups.d`; developer mode'da `/var/luna-service2-dev/` | politika **depolama alanı** | Yüksek · HTTP 200 |
| App trust level kuralı | Security Guide tablosu: App satırı "Trust level is set to OEM automatically" | uygulamanın **varsayılan seviyesi** | Orta · HTTP 200 (gerekçe okunmadı → yorum [UNKNOWN]) |
| App ID rezerve onekler | `com.palm` · `com.webos` · `com.lge` · `com.palmdts` yalnız platform geliştiricilerine açık | **isim alanı** yetkisi | Yüksek · HTTP 200 |
| `services.json` ad kuralı | Servis adı app adıyla başlamak zorunda (`com.example.myapplication` → `...myservice`) | servis **adlandırma** kısıtı | Yüksek · HTTP 200 |
| Cihaz/içerik izinleri | `allowAudioCapture` ve `allowVideoCapture` **default `false`**; `thirdPartyCookiesPolicy` (`allow` default); `useUnlimitedMediaPolicy` default `false` → strict media policy | uygulamaya verilen **hakların varsayılanı** | Yüksek · HTTP 200 |
| `ls-control scan-services` | Runtime'da hub'a yeniden tarama yaptırır; yalnız `opkg` ile kurulmuş bileşenlerde önerilir | politika **güncelleme kanalı** | Yüksek · HTTP 200 |

**Okuma:** 13 satırın 12si doğrudan okunmuş olgu, 1i (app trust level) dokümanın
kendi satırıdır ama gerekçesi yazılmamıştır → yorum yapılmaz. Bu modelin
özelliği: yetki **dosya tabanlı ve bildirimseldir** — kodda değil, paketin
yapılandırma dosyalarında yaşar.

## §2 Kod örnekleri

> **UYARI (zorunlu):** 3 bloğun tamamı `kaynak: upstream docs` imzalı **referans**
> kalıplarıdır; bu repodan alınmamıştır ve repo karşılıkları §3'te ölçülür.

### §2.1 — Uygulama ACG beyanı (referans)

```sh
# kaynak: upstream docs - webosose.org/docs/guides/development/configuration-files/security-guide/
# appinfo.json - uygulamanin istedigi ACG degerleri (acg3)
{
  "id" : "com.webos.exampleapp",
  ...
  "requiredPermissions" :[ "acg3" ]
}
```

| Sınıf | Repo karşılığı |
|---|---|
| R (upstream referans) | YOK — repoda `appinfo.json` yok |

### §2.2 — Role file: kayıt anında yetki (referans)

```sh
# kaynak: upstream docs - webosose.org/docs/guides/development/configuration-files/security-guide/
# com.example.service.native.role.json.in - ornek role dosyasi
{
  "exeName" : "/usr/bin/com.example.service.native",
  "type" : "regular",
  "trustLevel" : "dev",
  "allowedNames" : [ "com.example.service.native" ],
  "permissions" : [
    { "service" : "com.example.service.native",
      "inbound" : [ "*" ], "outbound" : [ "*" ] }
  ]
}
```

| Sınıf | Repo karşılığı |
|---|---|
| R (upstream referans) | YOK — repoda role dosyası / Luna Bus yok |
| N (negatif kanıt) | `*.role.json` glob 0 · 2026-10-14 |

### §2.3 — Groups file: ACG → Trust Level (referans)

```sh
# kaynak: upstream docs - webosose.org/docs/guides/development/configuration-files/security-guide/
# com.example.service.native.group.json - ornek grup dosyasi
{
  "allowedNames" : [ "com.example.service.native" ],
  "exampleservice.acgvalue1" : [ "dev" ],
  "exampleservice.acgvalue2" : [ "oem" ]
}
```

| Sınıf | Repo karşılığı |
|---|---|
| R (upstream referans) | YOK — repoda ACG/seviye eşlemesi yok |
| Karşılığı olan repo davranışı | yalnızca uygulama-içi kontrol: `shared/src/Middleware/CsrfMiddleware.php:42` (`hash_equals`) — **farklı katman** |

## §3 CoreMusic repo durumu

| Yüzey | webOS karşılığı | Repo durumu | Kanıt (2026-10-14) | Sınıf |
|---|---|---|---|---|
| ACG yapılandırması | `api-permissions.d/*.api.json` | **YOK** | glob `*.api.json` 0 | negatif kanıt |
| Trust Level / grup dosyası | `groups.d/*.groups.json` | **YOK** | glob `*.groups.json` 0 | negatif kanıt |
| Role file | `roles.d/*.role.json*` | **YOK** | glob `*.role.json*` 0 | negatif kanıt |
| Client permission file | `client-permissions.d/*.perm.json` | **YOK** | glob `*.perm.json` 0 | negatif kanıt |
| `appinfo.json` / `services.json` | uygulama paket beyanı | **YOK** | glob `appinfo.json`,`services.json` 0 | negatif kanıt |
| Luna Bus / LS2 istemcisi | `luna://` URI kullanımı | **YOK** | `*.php`,`*.js` taraması `luna://` 0 | negatif kanıt |
| OS güvenlik aracı deseni | seccomp/LSM/capability | **YOK (0 isabet)** | 418 PHP/JS dosyası · `Select-String` 0 | negatif kanıt |
| Zaman-güvenli token karşılaştırma | — (K009 alanı) | **VAR** | `shared/src/Middleware/CsrfMiddleware.php:42` → `hash_equals($sessionToken, (string)$submitted)` | P |
| Dosya izni ile yetki daraltma | en yakın DAC karşılığı | **VAR** | `shared/src/Session/SessionConfig.php:20` → `DIR_PERMISSIONS = 0750` | P |

**Okuma:** 9 satırın 7si negatif, 2si pozitiftir. webOS'un dosya-tabanlı yetki
modeli bu repoda **hiçbir katmanda yoktur**; CoreMusic yetkisi middleware
sırasında (`OriginCheck → ... → Auth → Permission`) yaşar ve buradaki tek somut
kanıt `hash_equals` ile **zaman-güvenli** karşılaştırmadır. "repo'da ACG benzeri
mekanizma var" cümlesi bu veriyle **kurulamaz** → `[UNKNOWN]` değil, **YOK**.

## §4 Sınır cümleleri

> **K010 ayrimi (baglayici):** Kimlik dogrulama, oturum, sifre ve MFA -> `K010-identitiy`; bu kartta yalnizca webOS dosya/surec/erisim kisitlari ele alinir.
> **K009 ayrimi (baglayici):** OWASP denetimi, auth/CSRF/CSP tasarimi, kripto secimi (ADR-022) -> `K009-security`; bu kart yalnız OS katmani + sinir isaretlerini tasinir.
> **Komşu dal:** Linux cekirdegi ici yapisi -> `isletim-sistemi-kernel`; webOS platform geneli -> `isletim-sistemi-turu/webos`.

| Sınır | Bu kart TAŞIR | Bu kart TAŞIMAZ |
|---|---|---|
| Yetki ekseni | ACG + Trust Level + 5 yapılandırma dosyası tipi | hangi role hangi kullanıcının gireceği (`K010-identitiy`) |
| Repo | 7 negatif + 2 pozitif (hash_equals, 0750) | CSRF/CSP tasarımı ve OWASP denetimi (`K009-security`) |
| İzolasyon | yetki **tanımını** | süreç sandbox mekanizması → `uygulama-guvenlik-siniri` |
| Değişiklik | bu kart salt-okunur denetim çıktısı | düzeltme backend-architect'te, yeniden ölçüm security-engineer'da |

## §5 Referans

| URL | İddia | 2026-10-14 durum |
|---|---|---|
| https://webosose.org/docs/guides/development/configuration-files/security-guide/ | Trust Level matrisi, ACG akışı, role/api/groups/client-perm dosyaları, dizin yolları, `ls-control`/`ls-monitor` | 2026-10-14 · HTTP 200 (GET) |
| https://webosose.org/docs/guides/development/configuration-files/appinfo-json/ | `requiredPermissions` tanımı; `allowAudioCapture`/`allowVideoCapture` default `false`; rezerve app ID oneklari | 2026-10-14 · HTTP 200 (GET) |
| https://webosose.org/docs/guides/development/configuration-files/services-json/ | Servis adı app adıyla başlamalı; `engine`/`executable` zorunlulukları | 2026-10-14 · HTTP 200 (GET) |
| https://webosose.org/docs/guides/getting-started/introduction-to-ls2-api/ | `luna://<service name>` URI biçimi, metod/parametre/call response sözlüğü | 2026-10-14 · HTTP 200 (GET) |
| https://webosose.org/docs | Security Guide sayfasının doküman ağacındaki yeri | 2026-10-14 · HTTP 200 (GET) |

> **Doğrulama notu:** 5 satır 2026-10-14'te HTTP GET ile okundu; tablolar
> sayfalardaki değerlerle birebir. `webostv.developer.lge.com` (LG webOS TV)
> sayfaları bu kartta **kullanılmadı** — webOS OSE docs'u kaynak alındı; TV
> sürümü izin modelinin birebir aynı olduğu iddia edilmez → [UNKNOWN].

### §5.1 Ölçüm komutları (yeniden üretilebilir)

```powershell
# kaynak: disk olcumu - bu repo (2026-10-14, security-engineer olcumu)
$K='C:\www\coremusic.net\.ai\architecture\K000-isletim-sistemi\isletim-sistemi-security\yetki-ve-erisim-modeli'
(Get-Content "$K\yetki-ve-erisim-modeli-webos.md").Count
Get-ChildItem 'C:\www\coremusic.net' -Recurse -Include appinfo.json,services.json,*.role.json,*.api.json,*.perm.json -File -ErrorAction SilentlyContinue |
  Where-Object { $_.FullName -notmatch '\\vendor\\|\\node_modules\\' }
```

## §6 Açık sorular

| # | Soru | Durum | Kapatma koşulu |
|---|---|---|---|
| S1 | OS guvenlik katmani hedef mimaride devreye alinacak mi? | **[UNKNOWN]** — kapatılmaz | mimari sahibi yazılı karar + yeni ADR (`>=088`) |
| S2 | Uygulama <-> OS guvenlik sorumluluk siniri? | **[UNKNOWN]** — kapatılmaz | `uygulama-guvenlik-siniri` dalı + K009 ile ortak protokol |

**Kapatma yasağı:** App trust level'ın neden "automatically OEM" olduğu dokümanda
açıklanmıyor → gerekçe `[UNKNOWN]` olarak kalır, model "uygulamalar oem seviyesinde
güvenlidir" diye **yorumlanmaz**. webOS TV sürümü ile OSE arasındaki izin modeli
eşdeğerliği de kanıtlanmadı → `[UNKNOWN]`.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
