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

---

## §7 Çapraz kart matrisi (webOS uygulama sınırı kontrol haritası)

§1 profili, §2 kalıplar (role file / istemci izin dosyası / sınır gözlemi)
ve §5.1 ölçüm burada çaprazlanır; kanıtsız kontrol "var" sayılmaz.

| # | Sınır kontrolü | Yüzey (§ referansı) | Sağlayıcı katman | Doğrulama tipi | Öncelik |
|---|---|---|---|---|---|
| K1 | Role file ile çift yönlü kısıt (servis↔istemci) | §2.1 role file | Luna Service ACL | Statik | P0 |
| K2 | İstemci izin dosyası (metod düzeyi) | §2.2 izin dosyası | Servis izin katmanı | Statik | P0 |
| K3 | Sınırın çalışma halinin gözlenmesi (runtime) | §2.3 gözlem | Runtime log/ölçüm | Dinamik + ölçüm | P1 |
| K4 | Paket imzası + FOTA doğrulaması | §1 paket | Package/FOTA | Statik | P0 |
| K5 | Uygulama sandbox (app private alan) | §1 depolama | Sandbox | Statik + dinamik | P0 |
| K6 | Ağ politikası (uygulama/trust düzeyi) | §1 ağ | Network config | Dinamik | P1 |
| K7 | Servis/kimlik doğrulama (oturum) | §1 kimlik | Auth/Session | Dinamik | P1 |
| K8 | Kaynak kotası (bellek/CPU per-app) | §1 kaynak | OOM/kota | Ölçüm | P2 |

### §7.1 Matris okuma kuralı

1. **Statik kanıt** — role/izin dosyası, manifest (Dosya: ile).
2. **Dinamik kanıt** — servis ret (yetkisiz metod) olayı.
3. **Ölçüm kanatı** — §5.1 ölçüm komutunun yeniden üretilebilir çıktısı.

Kanıtsız satır `VERIFICATION REQUIRED` kalır.

### §7.2 Çapraz kart eşlemesi (sınır → kart)

| Kart kategorisi | İlgili kontroller | Bu dosyadaki karşılığı |
|---|---|---|
| Kimlik kartları | K7 | §1 oturum, §10 S4 |
| Servis kartları | K1, K2 | §2.1/§2.2, §10 S1 |
| Depolama kartları | K5 | §1 sandbox, §10 S3 |
| Bağlantı kartları | K6 | §10 S5 |
| Paket kartları | K4 | §1, §10 S2 |
| Kaynak kartları | K8 | §5.1 + §13 T3 |

## §8 Ses/RT yüzeyi (webOS için PLANNED senaryo)

> **DURUM: PLANNED** — Zincir gerçek cihazda bu turda doğrulanmadı.

### §8.1 Neden bu bölüm burada?

Ses alt sistemi (medya çerçevesi, ayrıcalıklı ses servisi, cihaz erişimi)
uygulama sınırının ayrıcalıklı noktasıdır: servis ACL'i (K1/K2), oturum
(K7) ve sandbox (K5) ile kesişir. Ses algoritması/kalite (SNR/THD) bu
dosyanın dışındadır.

### §8.2 PLANNED senaryo zinciri (Ses ↔ Sınır)

| Adım | Senaryo | Beklenen yüzey | Durum |
|---|---|---|---|
| A1 | Uygulama ses servisine metod çağırır | Role/izin denetimi (K1/K2) | PLANNED |
| A2 | Ayrıcalıklı ses servisi isteği doğrular | Oturum/kimlik (K7) | PLANNED |
| A3 | Ses veri yolu önbellek üzerinden akar | K5 sandbox alanı | PLANNED |
| A4 | Uygulama kapanır | Kanal/kota temizlenir (§9 M4) | PLANNED |

**Kapsam dışı:** ses algoritması/codec/DSP. **Harici referans:** `.ai/electronic/drivers/audio-drivers.md` — bu turda yalnız **okuma** (beyazliste dışı yazım yasağı).

## §9 Başarısızlık modları (webOS uygulama sınırı)

| Mod | Tetikleyici | Gözlenen belirti | Sınır etkisi | Sürdürme/geri alma |
|---|---|---|---|---|
| M1 | Role/izin ihlali | Servis ret | Çağrı düşer | §2.1/§2.2 denetimi |
| M2 | Paket imza/FOTA reddi | Kurulum/önyükleme ret | Paket düşer | İmzalı paket |
| M3 | Sandbox ihlali | EACCES/ret | Erişim kesilir | Alan denetimi |
| M4 | Uygulama çökmesi | Süreç ölümü | Kaynak sızıntısı | Kota/oturum temizliği |
| M5 | Oturum/kimlik ret | Auth ret | Özellik kapalı | Oturum denetimi |
| M6 | Ağ politikası ret | Bağlantı ret | Bağlantı düşer | Ağ profili |
| M7 | Sınır gözlemi kaybı (log) | Gözlem boşluğu | Kanıt kaybı | §2.3 gözlem denetimi |
| M8 | Kota aşımı | OOM/ret | Uygulama durur | Ölçüm + limit |

### §9.1 Fail-safe matrisi (öncelik sırası)

1. **P0 — M1, M2, M3:** fail-closed, ret kanıtı zorunlu.
2. **P1 — M5, M6, M7:** tek yüzey/akış.
3. **P2 — M4, M8:** yalnız süreç/kota.

`fail-open` hiçbir modda kabul edilmez. 3 başarısız turda dur + üst karar.

## §10 Kullanım senaryoları

Kalıp: **kural → yüzey → doğrulama → sonuç (işaretli)**.

### §10.1 S1 — Role/izin denetimi (P0)

- **Kural:** Yetkisiz role/izin dosyası dışı metod ret (K1/K2, §9 M1).
- **Yüzey:** Luna Service ACL (§2.1/§2.2).
- **Doğrulama:** Yetkisiz rol ile metod → beklenen: ret + log.
- **Sonuç:** Geçerse K1/K2 ihlali → P0. (Durum: PLANNED.)

### §10.2 S2 — Paket/FOTA doğrulaması (P0)

- **Kural:** İmzasız paket/GÜNCELLEME ret (K4, §9 M2).
- **Yüzey:** Package/FOTA (§1).
- **Doğrulama:** Test paketi → beklenen: kurulum ret.
- **Sonuç:** Kurulursa K4 ihlali → P0. (Durum: PLANNED.)

### §10.3 S3 — Sandbox alanı (P0)

- **Kural:** Private alan dışı erişim ret (K5, §9 M3).
- **Yüzey:** sandbox (§1).
- **Doğrulama:** Dış alana okuma/yazma → beklenen: ret.
- **Sonuç:** Geçerse K5 ihlali → P0. (Durum: PLANNED.)

### §10.4 S4 — Oturum/kimlik (P1)

- **Kural:** Geçersiz oturumla servis erişimi ret (K7, §9 M5).
- **Yüzey:** Auth/Session (§1).
- **Doğrulama:** Oturumsuz çağrı → beklenen: ret.
- **Sonuç:** Geçerse K7 ihlali → P1. (Durum: PLANNED.)

### §10.5 S5 — Ağ politikası (P1)

- **Kural:** Güven düzeyi dışı bağlantı ret (K6, §9 M6).
- **Yüzey:** ağ politikası (§1).
- **Doğrulama:** Dış hedef → beklenen: ret.
- **Sonuç:** Bağlanırsa K6 ihlali. (Durum: PLANNED.)

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
| Bu dosya §0–§6 | Profil, §2.1–§2.3 kalıpları, §5.1 ölçüm, sınırlar |
| K000 security kardeşleri | Çapraz okuma (§12) |
| K000 testing | Doğrulama → test (§13) |
| .ai/electronic/drivers/audio-drivers.md | Ses fallback — yalnız okuma |

### §11.2 Dış kaynak kategorileri

| Kategori | Dayanak | Durum |
|---|---|---|
| webOS/Luna dokümantasyonu | K1–K8 | Erişilebilir URL eklenince işaretlenir |
| Cihaz/log örnekleri | §10 | ⚠️ VERIFICATION REQUIRED (alınmadı) |
| Role/izin dosyası çıktıları | statik kanıt | ⚠️ VERIFICATION REQUIRED (PLANNED) |

### §11.3 Kaynak güven sırası

1. Cihaz ölçüm/log. 2. Dosya içi § referansı. 3. Vault içi kardeş dosya.
4. Tarihli erişilebilir URL. 5. Model hafızası — tek dayanak asla.

## §12 Eşleme ağacı (okuma sırası)

```text
uygulama-guvenlik-siniri-webos.md
|-- Sec0 ELI15
|-- Sec1 webOS guvenlik profili
|-- Sec2 Kod ornekleri (2.1 role file | 2.2 istemci izni | 2.3 gozlem)
|-- Sec3 CoreMusic repo durumu
|-- Sec4 Sinir cumleleri
|-- Sec5 Referans + 5.1 olcum  (kanit uretimi)
|-- Sec6 Acik sorular          (KAPATMA YASAGI)
|-- Sec7 Cizraz matris         (K1-K8 haritasi)
|-- Sec8 Ses/RT PLANNED        (sinirli yuzey)
|-- Sec9 Basarisizlik          (M1-M8 + fail-safe)
|-- Sec10 Senaryolar           (S1-S5 kalip)
|-- Sec11 Kaynakca             (guven sirasi)
|-- Sec12 Esleme agaci         (bu satir)
'-- Sec13+ Test/ozet           (kapi adimlari)
```

## §13 Test önerileri + negatif kontroller + okuma sırası

### §13.1 Pozitif testler

| Test | Adım özeti | Beklenen |
|---|---|---|
| T1 | İmzalı paket kur (K4) | Kurulum başarılı |
| T2 | Yetkili rol ile metod (K1/K2) | Çağrı başarılı |
| T3 | §5.1 kota ölçümü (K8) | Beklenen değer |
| T4 | §2.3 sınır gözlemi | Log akışı var |

### §13.2 Negatif kontroller (RED TEAM — reddin kanıtı)

| # | Negatif adım | Beklenen ret | İhlal varsa |
|---|---|---|---|
| N1 | Yetkisiz rol ile metod | M1 ret | K1/K2 ihlali → P0 |
| N2 | İmzasız paket/FOTA | M2 ret | K4 ihlali → P0 |
| N3 | Private alan dışı erişim | M3 ret | K5 ihlali → P0 |
| N4 | Oturumsuz servis çağrısı | M5 ret | K7 ihlali → P1 |
| N5 | Güven düzeyi dışı bağlantı | M6 ret | K6 ihlali → P1 |
| N6 | Gözlem/log kapalıyken sınır testi | M7 boşluk | K3 ihlali → P1 |
| N7 | Kota aşımı | M8 OOM/ret | K8 ihlali → P2 |

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
| M7 tekrarı (gözlem kaybı) | §2.3 gözlem zinciri denetimi |
| M8 tekrarı | Kota limiti revizyonu |

Log append-only; `ts` geriye sarılmaz; `kanit` boş olay sayılmaz; P0 olayları
bir sonraki §13.2 turunda tekrar denenir. §6 S1/S2 etkilenmez; frontmatter
değişmez. Sıra: P0 → K1, K2, K4, K5 → P1 → K3, K6, K7 → P2 → K8/M4/M8.

### §15 Append-only + kapı özeti (webOS eki protokolü)

Her gelecek eki yalnız Authority kuyruğundan sonra eklenir; §0–§14,
frontmatter ve Authority satırı değişmez; sıradaki numara §16'dır. Yasaklar:
silme/yeniden yazma · dolgu/TBD · §6 S1/S2 kapatma · frontmatter değişikliği ·
Mojibake. Ekleme sonrası ölç; 500'ün altındaysa §<n+1> kaynak atıflı eklenir.

### §16 Zorunlu 8 soruluk kapı özeti (son okuma)

| # | Soru | Cevap referansı |
|---|---|---|
| 1 | Role file kısıtı çift yönlü mü? | §2.1 + §13.2 N1 |
| 2 | İstemci izin dosyası eksiksiz mi? | §2.2 + §13.2 N1 |
| 3 | Sınır gözlemi (runtime) çalışıyor mu? | §2.3 + §13.1 T4 |
| 4 | Paket/FOTA doğrulaması etkin mi? | §13.2 N2 |
| 5 | Sandbox alanı korunuyor mu? | §13.2 N3 |
| 6 | Ağ politikası etkin mi? | §13.2 N5 |
| 7 | Oturum/kimlik reddi doğru mu? | §13.2 N4 |
| 8 | Kotası ölçülü mü? | §13.1 T3 |

3 dk rota: §16 → §9.1 → §6 (oku, **kapatma**). Hatalar: kanıtsız "çalışıyor"
iddiası, PLANNED'ı geçiş saymak, log düzenlemek, fail-open yorumu.
Sonraki tur: §8 → cihaz ölçümü · §10 → dinamik kanıt · §11.2 → tarihli URL ·
§13.2 → §14 olayı. Bu işler bu turda **yapılmadı**.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

---

## §17 Uygulama katmanı ayrıntı kontrol listesi (webOS)

§7 matrisinin genişletilmiş sürümü; her satır bağımsız kanıtlanabilir (§7.1).

### §17.1 Role/izin sınıfı ayrıntılar

| # | Ayrıntı | Beklenen | Kanıt |
|---|---|---|---|
| A1 | Servis→istemci izni (çift yönlü) | Role file eşleşmesi | dosya dökümü |
| A2 | İstemci→servis metod izni | Metod düzeyi izin | izin dosyası |
| A3 | Grup/rol eşlemesi | En az yetki | eşleme listesi |
| A4 | Yetki kalıntısı (eski rol) | Kaldırılmış yetki yok | denetim |
| A5 | Servis lansmanı (autostart) | Tanımlı servisler | servis listesi |
| A6 | Paket içi servis tanımı | Statik tanım tutarlı | manifest |
| A7 | FOTA/önyükleme revizyonu | İmza/revizyon eşleşmesi | ölçüm |
| A8 | Gözlem (§2.3) kapsamı | Beklenen olaylar logda | log |

### §17.2 Sandbox/kaynak sınıfı ayrıntılar

| # | Ayrıntı | Beklenen | Kanıt |
|---|---|---|---|
| B1 | Private alan yazma kapsamı | Sadece kendi alanı | ret kanıtı |
| B2 | Public/paylaşılan alan | Sadece beyan edilen | ret kanıtı |
| B3 | Geçici/önbellek | Sınır içi | envanter |
| B4 | Kütüphane/yükleme yolu | Paket içi | yükleme ret |
| B5 | Ağ bağlantı noktası/trust | Güven düzeyine uygun | bağlantı ret |
| B6 | Bellek/CPU kotası | Limit uygulanmış | §5.1 ölçüm |
| B7 | Süreç yaşam döngüsü | Kapanışta temizlik | süreç logu |
| B8 | İzin geri çekilmesi | Geri çekilişte ret | ret olayı |

### §17.3 İzleme adımları (sıra)

1. §17.1 → §17.2 satırlarını sırayla ölç.
2. Her reti §14 şemasına yaz; beklenen ret gelmeyeni işaretle.
3. P0 açık satır için 3 tur sonra üst karar iste.
4. Çıktı yoksa `⚠️ VERIFICATION REQUIRED`.

### §17.4 Kapı ilişkisi + kardeş bağı

| Bağ | Katkı |
|---|---|
| İçerik kapısı | §17 uzunluğuyla ölçülür |
| §6 açık sorular | Etkilenmez (kapatma yasağı) |
| Frontmatter | Değişmez |
| §7 | §17 §7'i ayrıntılandırır |
| §9 / §10 | A/B satırları M/S'ye bağlanabilir |
| §13 / §14 | Sonuçlar negatif tur + olay şemasına akar |

### §17.5 SSS

**S: §17 satırları PLANNED mı?**
C: Beklenen sütunu şart; gözlenen sütunları kanıt gelene kadar PLANNED.

**S: Sıra?**
C: P0 (A1, A2, A4, A6, A7, B1) önce; B5–B8 son.

**S: Kanıt nereye?**
C: Bu dosyaya eki satır; mevcut satırlar değişmez.

**S: Ne zaman tekrar?**
C: Role/izin/paket değişiminde + haftalık (§17.6).

### §17.6 Ölçüm tekrarlanabilirlik tablosu

| Ölçüm | Kaynak | Sıklık |
|---|---|---|
| Role/izin dosyaları | §17.1 | Rol değişiminde |
| Sandbox kapsamı | §17.2 | Paket değişiminde |
| Kotası | §17.2 B6 | Haftalık |
| Olay şeması bütünlüğü | §14 | Olay başına |

### §17.7 Süreklilik

§15 append protokolü + §16 kapı özeti birlikte geçerlidir. Bir sonraki eki
yazacak kişi: §16 → §9.1 → §6 okur, sonra yalnız Authority kuyruğundan sonra
§18 ekler. Ölç-ekle-devam döngüsü sürer; dolgu yasak.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
