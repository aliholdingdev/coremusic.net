---
node_id: K000-isletim-sistemi/isletim-sistemi-security/uygulama-guvenlik-siniri/uygulama-guvenlik-siniri-webos
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-security/uygulama-guvenlik-siniri
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-security/uygulama-guvenlik-siniri/uygulama-guvenlik-siniri-anayasa
  - K000-isletim-sistemi/isletim-sistemi-security/uygulama-guvenlik-siniri/uygulama-guvenlik-siniri-surucu
evidence:
  - https://webosose.org/docs/guides/development/configuration-files/security-guide/
  - https://webosose.org/docs/guides/core-topics/architecture/architecture-overview/
  - https://webosose.org/docs/guides/core-topics/application-management/web-app-lifecycle/
  - https://webosose.org/docs/guides/development/configuration-files/appinfo-json/
  - https://webosose.org/docs/guides/setup/bring-up-guide/bring-up-overview/
  - home.coremusic.net/include/Stream/MusicStreamHandler.php:189
  - home.coremusic.net/include/Stream/MusicStreamHandler.php:200
  - shared/src/AI/AIEngine.php:292
validation:
  - "2026-10-14: URL'ler erisim ile dogrulandi; kod ornekleri kaynak: upstream docs imzali; repo OS araci yokligu 2026-10-14 taramasinda 0 isabet."
open_questions:
  - "S1: OS guvenlik katmani hedef mimaride devreye alinacak mi? [UNKNOWN]"
  - "S2: Uygulama <-> OS guvenlik sorumluluk siniri? [UNKNOWN]"
group: kod
---

# Uygulama Güvenlik Sınırı — webOS OS Kartı

## §0 ELI15

webOS OSE'de bir uygulamanın sınırı üç yerde çizilir: **süreç** (Chromium
çok-süreçli model — her web app kendi bağımsız sürecinde koşar), **yetki** (Luna
Bus her istekte ACG + Trust Level eşleşmesine bakar, uyuşmazsa reddeder) ve
**paket beyanı** (`appinfo.json` varsayılan olarak ses/video yakalamayı kapalı
tutar). Bu kart bu üç sınırı resmi doküman satırlarıyla taşır ve CoreMusic'te
buna karşılık gelen **gerçek** sınır satırlarını ölçer: `realpath` + prefix
kontrolü ve `escapeshellarg`.

> **Oncelikli gercek:** Okuduğum sayfalarda **OS düzeyi sandbox** (namespace,
> seccomp, cgroup, Chromium sandbox yapılandırması) anlatılmıyor — bunlar bu
> kartta `⚠️ VERIFICATION REQUIRED` kalır. Ölçülen repo gerçeği: repoda Luna Bus,
> role/permission dosyası ve `luna://` kullanımı **0 isabet**; buna karşılık
> uygulama katmanında **3 pozitif sınır satırı** vardır (189, 200, 292 — §3).

## §1 webOS güvenlik profili

| Mekanizma | Ne yapar | Bu alt-dal açısı | Güven düzeyi |
|---|---|---|---|
| Luna Bus ACG + Trust Level denetimi | "If the client's ACG or Trust Level values don't match with the provider's values, Luna Bus rejects the request" | **servis erişim kapısı** (istek başına) | Yüksek · HTTP 200 |
| Role file `inbound`/`outbound` | İstemciden gelen ve gidene izin verilen servis listesi: `*` = tümü, `[]` = hiçbiri, string sonunda wildcard serbest | **çift yönlü** erişim kısıtı | Yüksek · HTTP 200 |
| Chromium çok-süreçli model | "each web app is run as a separate independent process" | **süreç izolasyonu** temeli | Yüksek · HTTP 200 |
| Web Application Manager (WAM) | Web app'leri başlatır/yönetir; "access privileges management, all based on the running status" | uygulama **ayrıcalık yöneticisi** | Yüksek · HTTP 200 |
| System and Application Manager (SAM) | Kurulum, başlatma, sonlandırma, kaldırma dahil yaşam döngüsü yönetimi | süreç **sahipliği** | Yüksek · HTTP 200 |
| App installer otomasyonu | Downloadable servis için ACG yapılandırma dosyalarını "automatically generates ... during installation" | politika **otomasyonu** | Yüksek · HTTP 200 |
| Dinamik servis sınırlaması | Talep anında açılır; bir süre işlem yapmazsa sistem otomatik kapatır | çalışan **yüzey daraltma** | Yüksek · HTTP 200 |
| Statik servis tamponu | Servis crash olursa Luna Bus istekleri saklar, systemd yeniden başlatınca teslim eder | dayanıklılık (erişim sınırı **değil**) | Yüksek · HTTP 200 |
| Web app yaşam döngüsü | `keepAlive=false` iken yeni uygulama açılınca çalışan app sonlanır; suspended instance öldürülüp yenisini açılır | **süreç ömrü** sınırı | Yüksek · HTTP 200 |
| Cihaz yakalama varsayılanı | `allowAudioCapture` / `allowVideoCapture` default **`false`** (API level 28 notu ses için) | uygulamanın **donanım hakkı** | Yüksek · HTTP 200 |
| İçerik politikaları | `thirdPartyCookiesPolicy` default `allow` · `useUnlimitedMediaPolicy` default `false` → strict media policy geçerli | web içeriği **sınırı** | Yüksek · HTTP 200 |
| Developer mode politika alanı | Yapılandırma dizinleri developer mode'da `/var/luna-service2-dev/` altına kayar | test/üretim **politika ayrımı** | Yüksek · HTTP 200 |
| OS sandbox (namespace · seccomp · cgroup) | Okunan 6 sayfanın hiçbirinde anlatılmıyor | süreç izolasyonunun **OS katmanı** | ⚠️ VERIFICATION REQUIRED |
| Chromium sandbox yapılandırması | Architecture sayfası yalnız "multi-process model" diyor; sandbox bayrakları yazılmıyor | tarayıcı içi **sandbox** | ⚠️ VERIFICATION REQUIRED |

**Okuma:** 14 satırın 11i doğrudan okunmuş olgu, 2si bilinçli olarak
`⚠️ VERIFICATION REQUIRED` işaretli eksik-yüzey, 1i (statik tampon) yalnız
dayanıklılık olduğunun sınır notudur. "Sınır" dediğimiz şey bu kartta **istek
reddi + süreç ayrımı + paket beyanı**dır; çekirdek düzeyi değildir.

## §2 Kod örnekleri

> **UYARI (zorunlu):** 3 blok da `kaynak: upstream docs` imzalıdır; bu repodan
> alınmamıştır. Repo karşılıkları §3 tablosunda ayrı satır olarak ölçülür.

### §2.1 — Role file ile çift yönlü kısıt (referans)

```sh
# kaynak: upstream docs - webosose.org/docs/guides/development/configuration-files/security-guide/
# ornek: script tabanli bilesen role dosyasi - outbound tek yonlu bagli
{
  "appId" : "com.webos.app.enactbrowser",
  "type" : "privileged",
  "trustLevel" : "oem",
  "allowedNames" : [ "com.webos.app.enactbrowser-*" ],
  "permissions" : [
    { "service" : "com.webos.app.enactbrowser-*",
      "outbound" : [ "*" ] }
  ]
}
```

| Sınıf | Repo karşılığı |
|---|---|
| R (upstream referans) | YOK — repoda role/izin dosyası yok (glob `*.role.json*` = 0 · 2026-10-14) |
| Kısıt anlamı | `inbound` yokluğu = gelen istek politikası bu dosyada tanımlı değil; `[]` kullanılırsa **hiçbir** istek geçer |

### §2.2 — İstemci izin dosyası (referans)

```sh
# kaynak: upstream docs - webosose.org/docs/guides/development/configuration-files/security-guide/
# com.example.service.perm.json - istemcinin istedigi ACG degeri
{
  "com.webos.service.applicationmanager" : [
    "applications.launch"
  ]
}
```

| Sınıf | Repo karşılığı |
|---|---|
| R (upstream referans) | YOK — repoda `*.perm.json` = 0 (2026-10-14) |
| Zorunluluk | Doküman: "All clients (services and applications that use other services) must have a client permission file" |
| Repo'daki karşılığı olan davranış | yalnızca uygulama-içi path sınırı §2.3 — farklı katman |

### §2.3 — Sınırın çalışma halinin gözlenmesi (referans)

```sh
# kaynak: upstream docs - webosose.org/docs/guides/development/configuration-files/security-guide/
# istemcinin ACG degerini goruntule - sinirin gorunur hali
ls-monitor -i com.webos.notification | grep "createToast"
# cikti: "createToast": { "provides": [ "all", "notification.operation" ]}
```

| Sınıf | Repo karşılığı |
|---|---|
| R (upstream referans) | YOK — `ls-monitor`/`luna-send` repoda yok (`luna://` taraması 0 · 2026-10-14) |
| Bring-up doğrulaması | Bring-Up sayfası LSM/WAM/SAM'i `ls-monitor -l \| grep -E "surfacemanager\|webappmanager\|applicationManager\|sam"` ile doğrular |
| Çıkış yorumu | Çıktı salt **ACG beyanıdır**; yetkilendirme sonucu değildir → sonucu Luna Bus verir |

## §3 CoreMusic repo durumu

| Yüzey | Beklenen kanıt | Repo durumu | Kanıt (2026-10-14) | Sınıf |
|---|---|---|---|---|
| Inbound/outbound erişim politikası | `*.role.json*` / `inbound` | **YOK** | glob `*.role.json*` 0 | negatif kanıt |
| İstemci izin dosyası | `*.perm.json` | **YOK** | glob `*.perm.json` 0 | negatif kanıt |
| Luna Bus / servis erişim katmanı | `luna://` URI | **YOK (0 isabet)** | 418 PHP/JS · `Select-String 'luna://'` 0 | negatif kanıt |
| Uygulama paket beyanı | `appinfo.json` · `services.json` | **YOK** | glob 0 | negatif kanıt |
| OS sandbox aracı (seccomp/LSM/capability) | süreç izolasyonu yapılandırması | **YOK (0 isabet)** | 418 PHP/JS · `Select-String` 0 | negatif kanıt |
| Süreç/kimlik doğrulama yapılandırması | `*.bpf` · `*.sysctl` | **YOK** | glob `*.bpf` 0 · `*.sysctl` 0 | negatif kanıt |
| Dosya yolu sınırı (realpath) | path traversal kırılması | **VAR** | `home.coremusic.net/include/Stream/MusicStreamHandler.php:189` → `$real = realpath($trimmed);` | P |
| Dosya yolu sınırı (prefix) | kütüphane kökü dışına çıkış reddi | **VAR** | `home.coremusic.net/include/Stream/MusicStreamHandler.php:200` → `if (!str_starts_with($real, $prefix))` | P |
| Komut sınırı (shell escape) | shell parametresi kaçışı | **VAR** | `shared/src/AI/AIEngine.php:292` → `escapeshellarg($filePath)` | P |

**Okuma:** 9 satırın 6si negatif, 3ü pozitiftir. Üç pozitif de **uygulama
katmanıdır**: webOS'un "Luna Bus reddeder" dediği yerde bu repoda "yol uyuşmazsa
403" vardır — aynı **sınır fikri**, farklı **katman**. `shell_exec` çağrısının
kaynağı `AIEngine.php:295` olup komut üretim sınırı bu kartın kanıtıdır; komutun
kendisi ve politikası `K009-security` kapsamındadır. "Repoda sandbox vardır"
cümlesi **kurulamaz** → OS sandbox satırı YOK, `[UNKNOWN]` değil.

## §4 Sınır cümleleri

> **K010 ayrimi (baglayici):** Kimlik dogrulama, oturum, sifre ve MFA -> `K010-identitiy`; bu kartta yalnizca webOS dosya/surec/erisim kisitlari ele alinir.
> **K009 ayrimi (baglayici):** OWASP denetimi, auth/CSRF/CSP tasarimi, kripto secimi (ADR-022) -> `K009-security`; bu kart yalnız OS katmani + sinir isaretlerini tasinir.
> **Komşu dal:** Linux cekirdegi ici yapisi -> `isletim-sistemi-kernel`; webOS platform geneli -> `isletim-sistemi-turu/webos`.

| Sınır | Bu kart TAŞIR | Bu kart TAŞIMAZ |
|---|---|---|
| webOS | süreç ayrımı + ACG/red akışı + paket beyanı varsayılanları | yetki modelinin tanımı (asıl tablolar → `yetki-ve-erisim-modeli`) |
| Repo | 6 negatif + 3 pozitif sınır satırı (189 · 200 · 292) | CSRF/CSP/OWASP denetimi (`K009-security`) |
| Kimlik | yalnız erişim/süreç kısıtları | auth akışı, oturum, MFA (`K010-identitiy`) |
| Eksik yüzey | `⚠️ VERIFICATION REQUIRED` işaretleme | "sandbox yok/yoktur" iddiası — kanıt yoksa iddia yok |

## §5 Referans

| URL | İddia | 2026-10-14 durum |
|---|---|---|
| https://webosose.org/docs/guides/development/configuration-files/security-guide/ | ACG/Trust uyuşmazlığında ret; `inbound`/`outbound` `*` ve `[]` semantiği; client permission file zorunluluğu; `ls-monitor` çıktısı | 2026-10-14 · HTTP 200 (GET) |
| https://webosose.org/docs/guides/core-topics/architecture/architecture-overview/ | Chromium multi-process → her web app ayrı süreç; WAM "access privileges management"; SAM yaşam döngüsü | 2026-10-14 · HTTP 200 (GET) |
| https://webosose.org/docs/guides/core-topics/application-management/web-app-lifecycle/ | `keepAlive` davranışı; yeni app açılınca çalışan app'ın sonlanması; suspended instance öldürülür | 2026-10-14 · HTTP 200 (GET) |
| https://webosose.org/docs/guides/development/configuration-files/appinfo-json/ | `allowAudioCapture`/`allowVideoCapture` default `false`; `thirdPartyCookiesPolicy`; `useUnlimitedMediaPolicy` | 2026-10-14 · HTTP 200 (GET) |
| https://webosose.org/docs/guides/getting-started/introduction-to-ls2-api/ | `luna://` URI biçimi, çağrı/abonelik sözlüğü | 2026-10-14 · HTTP 200 (GET) |
| https://webosose.org/docs/guides/setup/bring-up-guide/bring-up-overview/ | `ls-monitor -l` ile LSM/WAM/SAM doğrulaması; static servis kayıtları | 2026-10-14 · HTTP 200 (GET) |
| https://webosose.org/docs | Security Guide + Core Topics sayfalarının doküman ağacındaki yeri | 2026-10-14 · HTTP 200 (GET) |

> **Doğrulama notu:** 7 satır 2026-10-14'te HTTP GET ile okunmuş ve alıntılar
> birebirdir. Okunmayan hiçbir teknik özellik adı yazılmadı; OS sandbox
> (namespace/seccomp/cgroup) için kaynak **bulunamadı** → §1'de
> `⚠️ VERIFICATION REQUIRED`.

### §5.1 Ölçüm komutları (yeniden üretilebilir)

```powershell
# kaynak: disk olcumu - bu repo (2026-10-14, security-engineer olcumu)
$K='C:\www\coremusic.net\.ai\architecture\K000-isletim-sistemi\isletim-sistemi-security\uygulama-guvenlik-siniri'
(Get-Content "$K\uygulama-guvenlik-siniri-webos.md").Count
$h=Get-Content 'C:\www\coremusic.net\home.coremusic.net\include\Stream\MusicStreamHandler.php' -Encoding UTF8
$h[188]; $h[199]     # L189 realpath / L200 str_starts_with
$a=Get-Content 'C:\www\coremusic.net\shared\src\AI\AIEngine.php' -Encoding UTF8
$a[291]              # L292 escapeshellarg
```

## §6 Açık sorular

| # | Soru | Durum | Kapatma koşulu |
|---|---|---|---|
| S1 | OS guvenlik katmani hedef mimaride devreye alinacak mi? | **[UNKNOWN]** — kapatılmaz | mimari sahibi yazılı karar + yeni ADR (`>=088`) |
| S2 | Uygulama <-> OS guvenlik sorumluluk siniri? | **[UNKNOWN]** — kapatılmaz | bu dalın bulgusu + `K009-security` ile ortak protokol |

**Kapatma yasağı:** S2 bu kartın **ana sorusudur** ve eldeki kanıt (webOS: istek
reddi OS'ta · CoreMusic: yol/komut sınırı uygulamada) tek başına yeterli değildir
→ `[UNKNOWN]` olarak taşınır. OS sandbox yüzeyi (`⚠️ VERIFICATION REQUIRED`)
kaynak bulunana kadar kapatılmaz; "yok" diye yazılmaz. Kanıt yenileme tetikleyicisi:
`MusicStreamHandler.php` veya `AIEngine.php` değişimi → §3'ün 3 pozitif satırı
`Select-Object -Index` ile yeniden ölçülür.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
