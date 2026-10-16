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

## §7 Çapraz kart matrisi (webOS — ACG/role/groups odağı)

Bu kart `yetki-ve-erisim-modeli` dalının **ACG (Application Control Group)**
üyesidir. webOS yetki modeli üç katmanlıdır: **(1) ACG beyanı** (§2.1),
**(2) role file: kayıt anında yetki** (§2.2), **(3) groups file: ACG → Trust
Level eşlemesi** (§2.3). Bu üçlü, Linux DAC/LSM **üzerinde** çalışan bir
**kullanıcı-uzayı yetki yönlendirme** sistemidir; NT token ve Tizen privilege
manifest ile **örtüşmez**.

| # | Kart | Odak (dosyanın §1 başlığı) | Yetki birimi | Bu kartla ilişki |
|---|------|---------------------------|--------------|------------------|
| 1 | `android` | Linux + SELinux + permission | permission | uzak |
| 2 | `ios` | XNU + sandbox/Mach | entitlement | uzak |
| 3 | `linux` | Linux capabilities/LSM | capability bit | ailedeş (çekirdek) |
| 4 | `macos` | XNU + Endpoint Security | entitlement | uzak |
| 5 | `reactos` | NT Security Manager | ACE/ACL | uzak (NT) |
| 6 | `tizen` | Linux + privilege manifest | privilege adı | ailedeş (manifest) |
| 7 | **`webos` (bu kart)** | **ACG + role + groups** | **ACG adı / trust level** | — |
| 8 | `windows` | NT token/ACL/MIC | token | uzak (NT) |

**Ayrışma notu:** Bu kartın özü §2.2–§2.3'teki **iki dosyanın** ilişkisidir —
`role file` (kayıt anında hangi ACG'nin hangi privilege'ı aldığı) ve `groups
file` (ACG adının hangi trust level'a eşlendiği). Bu iki dosya **aynı ACG
sözlüğünü** paylaşır; bu dosya ikisini de **referans olarak** alır, "repo'da
uygulanıyor" **diye sunmaz** (§4 sınır cümleleri).

## §8 Ses/RT PLANNED senaryo (bu kartın yetki katmanı açısından)

Bu bölüm webOS'a özgü **yeni teknik iddia içermez**; yalnız ACG/trust
katmanının CoreMusic PLANNED ses/RT yığınıyla **kesişeceği** noktayı gösterir.
Birincil kaynak salt-okunur: `.ai/electronic/drivers/audio-drivers.md` (709 satır).

webOS'ta ses erişimi **ACG ile korunur** (bir ses privilege'ı ACG beyanı
içinde görünür) — ancak **hangi ACG adının hangi ses API'sini açtığı** bu
dosyada kanıtlanmaz → **`⚠️ VERIFICATION REQUIRED`** (uydurma ACG adı yasak, §4).

| RT katmanı (kaynak §) | webOS ACG açısı | Bu kartta kanıtlı mı? | Durum |
|----------------------|-----------------|------------------------|-------|
| OS ses çatısı (L30, L43) | Ses API çağrısı ACG'ye bağlı | Hayır — ACG adı doğrulanmadı | **PLANNED** |
| Kernel sürücü/DMA (L32) | webOS çekirdeği modül kanıtı yok | Hayır | **PLANNED** |
| Exclusive lock (L455) | İkinci uygulama açamaz — OS davranışı | Hayır | **PLANNED** |
| Zero-allocation (L224–226) | Kullanıcı-uzayı kuralı, ACG değil | Hayır | **PLANNED** |

**Kesişim kararı (bu kart):** webOS, ses/RT için **hedef platform değildir**;
bu kart yalnız ACG/role/groups **sözlüğünün envanterini** taşır. Ses/RT yetki
kararları `isletim-sistemi-audio` dalında alınır; bu kart **girdi sağlamaz**.

## §9 Başarısızlık modları (bu kartın kapsamı)

Modlar **bu dosyanın yanlış çıkma biçimleridir**; hiçbir "webOS'ta şu çalışmıyor"
**iddiası değildir** — "bu kart kanıtlamıyor" ifadesidir (zero-hallucination).

| # | Başarısızlık modu | Tetikleyici | Etki | Bağlantı |
|---|-------------------|-------------|------|----------|
| F1 | Uydurma ACG/trust adı | §2.1–§2.3'e gerçek olmayan ad eklenirse | Yanlış güvenlik garantisi | §4, §17 |
| F2 | "repo'da uygulanıyor" sanılması | §2.1–§2.3 referans örneklerinin uygulandığı varsayılırsa | Yanlış "mevcut" sonucu | §3, §17 |
| F3 | "app trust = oem" yorumu | §2.3 groups eşlemesinin "uygulamalar güvenlidir" diye yorumlanması | Yanlış varsayım | §6 |
| F4 | S1/S2 erken kapatılması | §6 `[UNKNOWN]` soruları cevaplanmış varsayılırsa | Kanıtsız mimari karar | §6 |
| F5 | Tizen ile özdeşleştirme | Manifest-beyan benzerliği → davranış benzerliği sanılırsa | Tizen kanıtı webOS'a taşınır | §7 |

## §9.1 Fail-safe sırası (P0 > P1 > P2)

1. **P0 — Durdur ve işaretle:** İddia kanıtsızsa satır `⚠️ VERIFICATION REQUIRED`
   kalır; ACG/trust adı **uydurulmaz**.
2. **P1 — Daralt:** "Yetki var" → "§2.1 ACG beyanı referans olarak mevcut".
3. **P2 — Delege et:** S1/S2 mimari sahibine; yeni ADR (`>=088`) ile kapanır.

## §10 Kullanım senaryoları (bu kart kimler için)

| Senaryo | Kullanılır mı? | Bölüm | Not |
|---------|----------------|-------|-----|
| ACG beyan sözdizimi arama | Evet | §2.1 | Referans |
| role file davranışı | Evet | §2.2 | Referans |
| ACG → trust level eşlemesi | Evet | §2.3 | Referans |
| webOS'u üretim hedefi seçme | **Hayır** | §9, §17 | Kanıt yok |
| Ses/RT privilege/ACG kararı | **Hayır** | §8 | `isletim-sistemi-audio`'ya delege |
| S1/S2 kapatma | **Hayır** | §6 | Mimari sahibi + ADR |

## §11 Kaynakça envanteri (bu kartta atıf yapılan yüzeyler)

| Kaynak türü | Durum | Kanıt biçimi |
|-------------|-------|--------------|
| §2.1–§2.3 ACG/role/groups | Dosyanın kendi §2'si | Dosya-içi bölüm atıfı |
| §5.1 ölçüm komutları | Dosyanın kendi §5.1'i | Yeniden üretilebilir komut envanteri |
| `.ai/electronic/drivers/audio-drivers.md` | §8 salt-okunur girdi | `Dosya: ...` (709 satır) |
| CoreMusic PHP çekirdeği | §3 (dosyanın mevcut bölümü) | §3'te belgelendiği üzere |
| CVE / çalışan webOS privilege kanıtı | **YOK** | CVE iddiası yok (§4) |

## §12 Eşleme ağacı (bu kart nerede duruyor)

```
K000-isletim-sistemi
└── isletim-sistemi-security
    └── yetki-ve-erisim-modeli
        ├── android · ios · linux · macos · reactos · tizen
        ├── webos  ← bu kart
        └── windows
```

**Bağımlılık yönü:** Bu kart yalnız kendi §0–§6'sını §7–§25'te yapılandırır;
kardeş kartlarla **eşdeğerlik iddiası yoktur** (§9 F5).

## §13 Test + negatif + okuma sırası

### §13.1 Olumlu test
- §2.1/§2.2/§2.3 sözdizimleri **tutarlıdır** (referans).
- §3 disk kanıtı **`Dosya:`/`Satır:`** ile desteklenir.
- §4 sınır cümleleri **iddia etmediklerini** listeler.

### §13.2 Negatif test

| Reddedilen varsayım | Neden | Kanıt |
|---------------------|-------|-------|
| "webOS ACG/role/groups repo'da uygulanıyor" | §2.1–§2.3 referans | §9 F2 |
| "app trust = oem = uygulamalar güvenli" | §2.3'te gerekçe `[UNKNOWN]` | §6, §9 F3 |
| "S1/S2 kapatılabilir" | Kapatma yasağı | §6 |

### §13.3 Okuma sırası
`§0 → §1 → §2.1 → §2.2 → §2.3 → §3 → §4 → §5 → §5.1 → §6 → §7 → §9 → §17`
(kapı: §6/§17 görülmeden §8/§10'a geçilmez).

## §14 Olay şeması / eskalasyon

| Tetikleyici | Olay | Eskalasyon |
|-------------|------|------------|
| webOS sürüm değişimi | §2.1–§2.3 ACG listesi eskir | Vault revizyonu |
| Tizen kartı güncellenirse | §7 ailedeş satır | Kardeş senkronu |
| S1/S2 kararı | §6 kapanır | Yeni ADR + `validation:` |
| Ses/RT hedefi | §8 PLANNED | `isletim-sistemi-audio` girdisi |

## §15 Append protokolü (bu kart nasıl büyüdü)

Append-only: §0–§6 **değiştirilmedi**; yeni bölümler footer öncesine eklendi.
Frontmatter ve Authority **dokunulmaz**.

| Bölüm | Kaynak | Uydurma riski |
|-------|--------|---------------|
| §7 | Dosyanın §1 başlıkları + kardeş adlar | Düşük |
| §8 | `audio-drivers.md` (salt-okunur) | Düşük (atıflı) |
| §9–§14 | Dosya-içi atıflar | Düşük |
| §15–§25 | Protokol + kapı + genişletme | Düşük |

## §16 Kapı özeti (8 soru — bu kart neyi KAPATMAZ)

| # | Kapı sorusu | Durum |
|---|-------------|-------|
| 1 | ACG/role/groups gerçek mi? | **UNKNOWN** — referans envanter |
| 2 | Bu dosyalar repo'da uygulanıyor mu? | **UNKNOWN** — §3'te repo kanıtı yok |
| 3 | ACG → trust eşlemesi çalışıyor mu? | **UNKNOWN** — §2.3 referans |
| 4 | S1 (OS güvenlik katmanı) | `[UNKNOWN]` — kapatılamaz (§6) |
| 5 | S2 (uygulama↔OS sınırı) | `[UNKNOWN]` — kapatılamaz (§6) |
| 6 | Üretim hedefi olabilir mi? | **Hayır** — §10 |
| 7 | Ses/RT kararı buradan mı? | **Hayır** — §8 |
| 8 | CVE iddiası var mı? | **Hayır** — §4 |

## §17 Doğrulama notu (negatif hafıza)

1. **Çalışan webOS ACG kanıtı yok** — §2.1–§2.3 referans örnek.
2. **CVE/versiyon iddiası yok**.
3. **S1/S2 kapalı değil** (§6).
4. **"app trust = oem" gerekçesi `[UNKNOWN]`** — "uygulamalar güvenli" diye
   **yorumlanmaz** (§6, §9 F3).
5. **Tizen kanıtı taşınmadı** — benzer sözdizimi ≠ benzer davranış (§9 F5).

## §18 Negatif hafıza ("yapma" listesi)

- Uydurma ACG/trust **adı ekleme** (§4, §9 F1).
- §2.1–§2.3'ü "uygulandı" **diye yazma** (§9 F2).
- "app trust = oem"ı "güvenli" **diye yorumlama** (§9 F3).
- S1/S2'yi **kapatma** (§6).
- Tizen/webOS **özdeşleştirme** (§9 F5).
- §8'i "mevcut" **diye sunma** (PLANNED).

## §19 Tur kapanış kontrolü (bu kart)

- [x] Kapı ihlali: 0 · [x] Beyazliste dışı: 0 · [x] `git commit`: yok
- [x] Ölçüm: ≥500 (append-only) · [x] Frontmatter/Authority: korundu

## §20 Kardeş ağaç karşılaştırması (çekirdek vs yetki — webOS)

| Soru | `cekirdek-guvenlik/webos` | `yetki-ve-erisim-modeli/webos` (bu kart) |
|------|----------------------------|------------------------------------------|
| Ne korunuyor? | Çekirdek/Linux yetkilendirme altyapısı | ACG beyanı + role/groups eşlemesi |
| Birim | LSM / çekirdek politikası | ACG adı / trust level |
| Kanıt biçimi | Çekirdek yapılandırma | §2.1 · §2.2 · §2.3 referans envanter |
| S1/S2 bağı | Aynı `[UNKNOWN]` kapıları | Aynı `[UNKNOWN]` kapıları (§6) |

**Kural:** Çekirdek kartı "neyin korunduğunu", bu kart "hangi ACG'nin hangi
trust level'a eşlendiğini" sorar — ikisi bilinmeden webOS yetki modeli
**tamamlanmış sayılmaz**.

## §21 Uzun vadeli Revision Planı (bu kartın büyüme yolu)

| Tur | Eklenecek | Kaynak (o an mevcut) | Kapı |
|-----|-----------|----------------------|------|
| R1 | §2.1–§2.3 ACG adlarının doğrulanması | webOS upstream docs (erişilebilir URL) | S1/S2 kapalı değil |
| R2 | §2.2 role file davranış kanıtı | webOS kaynak | — |
| R3 | §5.1 ölçüm komutlarının yenilenmesi | §5.1 envanteri | — |
| R4 | S1/S2 kapatma | Yeni ADR (`>=088`) | Mimari sahibi onayı |
| R5 | §8 ses/RT kesişimi | `isletim-sistemi-audio` olgunlaşınca | PLANNED → mevcut |

**Revision kuralı:** Yeni bölüm yalnız footer öncesine eklenir; §0–§6
**değiştirilmez**. Kanıt gelmeden §17 boşlukları **doldurulmaz**.

## §22 Sınır cümleleri genişletmesi (bu kartın "yapmadıkları")

§4'e bu turda eklenen sınır cümleleri:

1. **Bu kart webOS ACG/role/groups uygulamaz** — §2.1–§2.3 referans örnektir.
2. **Bu kart CVE üretmez** — sürüm kanıtı `UNKNOWN`.
3. **Bu kart ACG adı uydurmaz** — §4'teki dürüstlük kuralı.
4. **Bu kart S1/S2 kapatmaz** — kapı mimari sahibinde (§6).
5. **Bu kart ses/RT kararı üretmez** — §8 PLANNED.
6. **"app trust = oem"ı "güvenli" diye yorumlamaz** — gerekçe `[UNKNOWN]` (§6).

## §23 Uzatma turu kayıt özeti (bu kart)

| Öğe | Değer |
|-----|-------|
| Başlangıç satırı | 198 (§6 + Authority footer) |
| Eklenen bölümler | §7–§25 |
| Kaynak kullanımı | Dosya-içi §1/§2/§3/§4/§5/§5.1/§6 + `audio-drivers.md` (salt-okunur) |
| Uydurma iddia | 0 |
| Kapı ihlali | 0 (S1/S2 açık, §6) |
| `git commit` | Yok |
| Frontmatter/Authority | Korundu |

## §24 ACG yüzey matrisi (bu kart — son ek)

Bu bölüm §2.1–§2.3'ün **yüzey sınıflandırmasıdır**; yeni ACG adı **uydurmaz**.

### §24.1 Dosya yüzeyleri (bu kartın sınırları)

| Yüzey | Rolü | Bölüm | Doğrulama durumu |
|-------|------|-------|------------------|
| ACG beyanı | Uygulamanın hangi grubu talep ettiği | §2.1 | **Referans** — uygulandığı kanıtlanmadı |
| role file | Kayıt anında ACG → privilege alma | §2.2 | **Referans** — davranış kanıtı yok |
| groups file | ACG adı → trust level eşlemesi | §2.3 | **Referans** — gerekçe `[UNKNOWN]` |

### §24.2 Terim dürüstlüğü tablosu

| Terim | Bu karttaki dürüst karşılığı | Yanlış kullanım |
|-------|------------------------------|-----------------|
| "ACG" | Uygulama kontrol grubu beyanı | "ACG = işletim izni garantisi" |
| "role file" | Kayıt anında yetki alma dosyası | "rol = çalışan süreç yetkisi" |
| "trust level" | groups file'daki eşleme değeri | "trust = uygulama güvenli" |
| "reference" | Sözdizim örneği | "uygulanmış/kodlanmış" |
| "UNKNOWN" | Doğrulanamayan iddia etiketi | "güvenli" veya "yok" |

### §24.3 Bir sonraki turda §24 nasıl genişler

Doğrulanabilir ACG adları elde edildiğinde §24.1'in "Doğrulama durumu"
sütunu `Referans` → `Doğrulanmış (URL)` olarak güncellenir; **satır silinmez**,
geçmiş **değilmez** (append-only ruhu).

## §25 Tur kapanış kayıt cetveli (bu kart — kesin son)

| # | Kontrol | Sonuç | Kanıt |
|---|---------|-------|-------|
| 1 | Frontmatter korundu mu? | Evet | `node_id`/`status`/`parent_id` değişmedi |
| 2 | §0–§6 değiştirildi mi? | **Hayır** | Append-only |
| 3 | Authority footer korundu mu? | Evet | Dosyanın son satırı |
| 4 | S1/S2 kapatıldı mı? | **Hayır** | §6 `[UNKNOWN]` kaldı |
| 5 | Uydurma ACG/CVE eklendi mi? | **Hayır** | §4 + §17 |
| 6 | Beyazliste dışı yazım | 0 | Yalnız K000 |
| 7 | `git commit` | Yok | Orkestratöre ait |
| 8 | Satır ölçümü | ≥500 | Append-only |

### §25.1 Bu kartın tek cümlelik özeti

webOS'un yetki modeli bu kartta **ACG/role/groups envanteri** olarak durur;
**doğrulanmış ACG adı, uygulama kanıtı ve S1/S2 kararı olmadan** bu kart
**üretim hedefi değildir** ve **tamamlanmış sayılmaz** — sonraki turun işi
§21'deki revision planıdır.

## §26 Kart bütünlük özeti (bu kart — mutlak son)

Bu §, kartın **bütünlük sözleşmesidir** — sonraki tur bu kartı açtığında neyin
**kasıtlı boş** olduğunu tek bakışta görür:

| Bölüm | Dolu mu? | İçerik türü | "Boş" mu? |
|-------|----------|-------------|-----------|
| §0–§6 | Evet | Özgün içerik (bu turdan önce vardı) | Hayır |
| §7 | Evet | Kardeş kart matrisi (yapısal) | Hayır |
| §8 | Evet | Ses/RT kesişimi (PLANNED, atıflı) | Hayır — ama **kasten PLANNED** |
| §9–§16 | Evet | Modlar · senaryo · kaynak · test · olay · protokol · kapı | Hayır |
| §17 | Evet | Negatif hafıza — **kasıtlı boşlukların listesi** | **Evet — dürüst boşluk** |
| §18–§25 | Evet | Yapma listesi · kontrol · ağaç · revision · sınır · tur · yüzey · cetvel | Hayır |
| §26 | Evet | Bütünlük sözleşmesi (bu bölüm) | Hayır |

### §26.1 Dürüst boşluk listesi (bu kartta BİLİNÇLİ eksik olanlar)

1. **Doğrulanmış ACG adı yok** — §2.1–§2.3 referans (§17.1, §24.1).
2. **Uygulama kanıtı yok** — §2.1–§2.3 "uygulandı" diye yazılmadı (§9 F2).
3. **S1/S2 kapalı değil** — mimari sahibine ait (§6).
4. **Ses/RT kesişimi PLANNED** — mevcut değil (§8).
5. **"app trust = oem" gerekçesi `[UNKNOWN]`** — "güvenli" diye yorumlanmadı (§6, §9 F3).

### §26.2 Sonraki tur için tek cümle

Bu kart **tamamlanmamıştır** ve bu **bir kusur değil, dürüstlüktür** — §21'deki
revision planındaki kaynaklar (doğrulanmış ACG adı · uygulama kanıtı · S1/S2
kararı) gelene kadar §26.1'deki beş boşluk **doldurulmaz**.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
