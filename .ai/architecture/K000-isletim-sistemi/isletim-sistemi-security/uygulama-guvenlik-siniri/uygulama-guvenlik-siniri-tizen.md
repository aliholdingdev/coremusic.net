---
node_id: K000-isletim-sistemi/isletim-sistemi-security/uygulama-guvenlik-siniri/uygulama-guvenlik-siniri-tizen
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-security/uygulama-guvenlik-siniri
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-security/uygulama-guvenlik-siniri/uygulama-guvenlik-siniri-anayasa
  - K000-isletim-sistemi/isletim-sistemi-security/uygulama-guvenlik-siniri/uygulama-guvenlik-siniri-surucu
evidence:
  - https://docs.tizen.org/application/security/
  - https://samsungtizenos.com/docs/application/native/guides/concepts/file-system
  - https://samsungtizenos.com/docs/application/native/guides/security/security-tip
  - https://samsungtizenos.com/docs/application/native/guides/security/privacy-related-permissions
  - https://samsungtizenos.com/docs/application/native/reference/security-privileges
  - home.coremusic.net/include/Stream/MusicStreamHandler.php:189
validation:
  - "2026-10-14: URL'ler erisim ile dogrulandi; kod ornekleri kaynak: upstream docs imzali; repo OS araci yokligu 2026-10-14 taramasinda 0 isabet."
open_questions:
  - "S1: OS guvenlik katmani hedef mimaride devreye alinacak mi? [UNKNOWN]"
  - "S2: Uygulama <-> OS guvenlik sorumluluk siniri? [UNKNOWN]"
group: kod
---

# Uygulama Guvenlik Siniri — Tizen OS Kartı

## §0 ELI15

Bu kart, Tizen'de **bir uygulamanın diğerinden nasıl ayrıldığını** anlatır: her
uygulama **SMACK sandbox**'ı içinde çalışır, paket dizin ağacında **sahibi dışında
erişime kapalı** (`Others: Access denied`) klasörleri vardır ve hassas API'ye
erişmek için **manifest'te privilege beyanı** yapmak zorundadır. Paylaşım alanları
üç katmandadır: `data` (yalnız aynı paket) · `shared/trusted` (yalnız aynı sertifika
ile imzalanmış "aile") · `shared/data` (tüm cihaz okur — **zayıf**, upstream bunu
uyarıyla kullan der). CoreMusic'te bu katmanın karşılığı yoktur; kart hedef mimari +
negatif kanıttır.

> **Oncelikli gercek:** 2026-10-14 taramasında **418 PHP+JS dosyasında**
> (`vendor`/`node_modules` hariç) Tizen sandbox/privilege aracı **0 isabet**.
> Literal `Tizen` dizesi 3 dosyada geçer ve **üçü de TV user-agent algılamadır**
> (`breakpoints.js:31` · `breakpoints.spec.js:54` · `DeviceDetector.php:33`) —
> güvenlik katmanı değildir. Repodaki en yakın analog **uygulama katmanındaki**
> yol denetimidir: `home.coremusic.net/include/Stream/MusicStreamHandler.php:189`
> (`realpath`) + `:200` (`str_starts_with`) — bu bir **sandbox değildir**.

## §1 Tizen uygulama sandbox/sinir profili

**Ayrım (bağlayıcı):** Tizen, Linux çekirdeği üzerine kuruludur — bu kart ile
`isletim-sistemi-kernel` Linux kartı **birbirinin yerine geçmez**. Linux kartı
süreç izolasyonu/cgroup/seccomp'un çekirdek tanımını taşır; bu kart **Tizen'e özgü
sandbox katmanını** (SMACK etiketi, paket dizin izinleri, manifest privilege beyanı,
paylaşım güven dereceleri) öne çıkarır.

| Mekanizma | Ne yapar | Bu alt-dal açısı | Güven düzeyi |
|---|---|---|---|
| SMACK sandbox | Uygulama iç depolamadaki dosyalar **varsayılan olarak** diğer uygulamalara erişilemez | D3-1: süreç/veri sınırı | upstream docs · repo 0 |
| Paket dizin izinleri | `bin`/`lib`/`data`/`res` → `Others: Access denied`; `shared` → `Others: Read` | D3-1: dosya sistemi sınırı | upstream docs · repo 0 |
| `shared/trusted` aile paylaşımı | Yalnız **aynı sertifika** ile imzalanmış uygulamalar okuma-yazma yapabilir | D3-2: aile içi güven | upstream docs · repo 0 |
| `shared/data` (3.0+) | Tüm uygulamalar okur; kullanmak için `http://tizen.org/privilege/appdir.shareddata` beyanı zorunlu | D3-2: en zayıf halka | upstream docs · repo 0 |
| Manifest privilege beyanı | Hassas API çağrısı öncesi manifestte beyan; beyan yoksa çağrı reddedilir | D3-3: geçiş kapısı | upstream docs · repo 0 |
| Privacy runtime izni (3.0+/4.0+) | Kullanıcı açıp kapatabilir; 4.0'tan beri istek zamanı uygulama yaşam döngüsünde seçilir | D3-3: kullanıcı denetimi | upstream docs · repo 0 |
| Privacy pop-up `group mode` (5.5+) | Pop-up yarıda kesilirse cevapsız istekler **Deny** sayılır (`..._REQUEST_RESULT_DENY_ONCE`) | D3-3: fail-safe davranış | upstream docs · repo 0 |
| Privacy Privilege Manager thread kuralı | Fonksiyonlar **thread-safe değildir**; ana event loop bağlamından çağrılır | D3-3: kullanım sınırı | upstream docs · repo 0 |
| Dosya paylaşımı güven tablosu | `app_get_data_path` → güçlü · `app_get_shared_data_path` → **zayıf, tüm cihaz okur** · `app_get_shared_trusted_path` → güçlü | D3-2: paylaşım matrisi | upstream docs · repo 0 |
| App control geçici okuma izni | `APP_CONTROL_DATA_PATH` extra anahtarıyla alıcıya **geçici** okuma izni verilir | D3-2: alternatif paylaşım | upstream docs · repo 0 |
| Trusted message port | `message_port_register_trusted_local_port()` / `message_port_send_trusted_message()` — platform **author sertifikasını** denetler | D3-4: süreçler arası sınır | upstream docs · repo 0 |
| Data control provider kontrolü | Provider, consumer'ın application ID'sini callback'te kontrol edip reddedebilir | D3-4: süreçler arası sınır | upstream docs · repo 0 |
| Dış depolama (SD/MMC) | Tüm uygulamalar erişebilir; kullanıcı cihaz dışında çıkarabilir → hassas veri **konmaz** | D3-1: sınırın dışında kalan alan | upstream docs · repo 0 |
| `internet` privilege çekirdek denetimi | Yetki yoksa bağlantı **çekirdek katmanında** engellenir; loopback'te sandboxing nedeniyle rastgele uygulamaya bağlanılamaz | D3-4: ağ sınırı | upstream docs · repo 0 |

**Okuma:** 14 satırın tamamı `upstream docs` sınıfıdır (hedef mimari), 0 satır bu
repoda uygulanmıştır. `Güven düzeyi` sütunu iddianın kaynağını gösterir — repo
uygulamasını değil; repo uygulaması §3'te ayrı ölçülür.

## §2 Kod örnekleri

> **UYARI:** Aşağıdaki 3 bloğun **hiçbiri bu repodan alınmamıştır**; her blok
> ilk satırında `kaynak: upstream docs` imzası taşır ve §5'teki URL'lere bağlıdır.
> Repo'da karşılıkları §3'te `YOK` (negatif kanıt) olarak ölçülür.

### §2.1 — Paket dizin hiyerarşisi izinleri (referans)

```text
# kaynak: upstream docs - application/native/guides/concepts/file-system
# uygulama paket dizin hiyerarsisi ve izinleri (upstream tablo)
| dizin           | izin                                             |
|-----------------|--------------------------------------------------|
| bin             | Owner: Read        | Others: Access denied      |
| lib             | Owner: Read        | Others: Access denied      |
| data            | Owner: Read and Write | Others: Access denied   |
| res             | Owner: Read        | Others: Access denied      |
| shared          | Owner: Read        | Others: Read               |
| shared/data     | Owner: Read and Write | Others: Read (privilege)  |
| shared/res      | Owner: Read        | Others: Read               |
| shared/trusted  | Owner+Family: Read and Write | Others: Access denied |
# yollari: app_get_data_path() / app_get_resource_path() /
#          app_get_shared_data_path() / app_get_shared_trusted_path()
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans) | YOK — repoda Tizen paket dizin ağacı / SMACK etiketi 0 (§3) |
| Doğrulama | URL 2026-10-14 · HTTP 200 (ham `.md` kaynağı da 200) |
| Sınır | Bu izinler **Tizen paket içindir**; CoreMusic `0750` session izni ile aynı ölçekte değildir |

### §2.2 — Çalışma zamanı izin denetimi (referans)

```c
/* kaynak: upstream docs - application/native/guides/security/privacy-related-permissions */
#include <privacy_privilege_manager.h>

void
app_check_and_request_permission(void)
{
    ppm_check_result_e result;
    const char *privilege = "http://tizen.org/privilege/camera";

    int ret = ppm_check_permission(privilege, &result);
    /* PRIVACY_PRIVILEGE_MANAGER_CHECK_RESULT_ALLOW -> izin var */
    /* fonksiyonlar thread-safe DEGILDIR; ana event loop baglamindan cagrilir */
}
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans) | YOK — `ppm_check_permission` çağrısı repoda 0 (§3) |
| Bağımlılık | Tizen Native SDK · `CAPI_PRIVACY_PRIVILEGE_MANAGER` |
| Sınır | Runtime denetimi manifest beyanının **yerine geçmez**, onu tamamlar |

### §2.3 — Dosya paylaşımı güven dereceleri (referans)

```text
# kaynak: upstream docs - application/native/guides/security/security-tip
# paylasim yontemi ve guven derecesi (upstream tablo)
| fonksiyon                    | kim okur/yazar                    | guven   |
|------------------------------|-----------------------------------|---------|
| app_get_data_path()          | ayni paketteki uygulamalar       | Strong  |
| app_get_shared_data_path()   | okuma: TUM uygulamalar           | Weak    |
|                              | yazma: ayni paketteki uygulamalar| (dikkat)|
| app_get_shared_trusted_path()| ayni sertifika ile imzalanmislar | Strong  |
# alternatif: app control uzerinden APP_CONTROL_DATA_PATH extra anahtari ->
#   platform ALICIYA gecici okuma izni verir (shared/data'tan guvenli)
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans) | YOK — paylaşım yolu API'leri repoda 0 (§3) |
| Uyarı | Upstream, `shared/data` kullanımını açıkça **zayıf** sınıflandırır ve deprecate edeceğini bildirir |
| Sınır | Bu blok paylaşım **politikasıdır**; kimlik doğrulama değildir → `K0100-identitiy` |

## §3 CoreMusic repo durumu

> **Ölçüm (2026-10-14):** kapsam `*.php,*.js` → **418 dosya** (`vendor`/`node_modules`/
> `.git` hariç). Tizen sandbox/privilege aracı deseni
> (`tizen|smack|<tizen:privilege|http://tizen.org/privilege|load_policy|/sys/kernel/security`)
> → **0 isabet** (3 isabetin tamamı literal `Tizen` UA dizesidir, güvenlik dışı).

| Yüzey | Beklenen artifact | Repo durumu | Kanıt (negatif/pozitif) |
|---|---|---|---|
| SMACK etiketi / sandbox politikası | `smack*` · `SECURITY_LABEL` · `load_policy` | **YOK** | desen 0 · negatif kanıt (2026-10-14) |
| Manifest privilege beyanı | `tizen-manifest.xml` · `<tizen:privilege` | **YOK** | desen 0 · negatif kanıt (2026-10-14) |
| Uygulama başına izin manifesti | izin beyan dosyası (Tizen tipi) | **YOK** | glob 0 · negatif kanıt (2026-10-14) |
| Runtime izin akışı | `ppm_*` benzeri çağrı | **YOK** | desen 0 · negatif kanıt (2026-10-14) |
| Tizen paket ağacı (`data`/`shared`/`trusted`) | paket dizin yapısı | **YOK** | glob 0 · negatif kanıt (2026-10-14) |
| UA cihaz algılama (Tizen dizesi) | TV user-agent listesi | **VAR (güvenlik değil)** | `assets.coremusic.net/js/core/breakpoints.js:31` · `breakpoints.spec.js:54` · `shared/src/Device/DeviceDetector.php:33` |
| Yol normalizasyonu (uygulama katmanı) | PHP dosya denetimi | **VAR** | `home.coremusic.net/include/Stream/MusicStreamHandler.php:189` → `realpath` · `:200` → `str_starts_with` · **P** |
| Session dizin izni (uygulama katmanı) | PHP `mkdir` modu | **VAR** | `shared/src/Session/SessionConfig.php:20` · `:35` → `0750` · **P** |
| Shell kaçış koruması (uygulama katmanı) | PHP yardımcı fonksiyonu | **VAR** | `shared/src/AI/AIEngine.php:292` → `escapeshellarg` · **P** |
| Zamanlama-güvenli karşılaştırma (uygulama katmanı) | PHP middleware | **VAR** | `shared/src/Middleware/CsrfMiddleware.php:42` → `hash_equals` · **P** |

**Okuma:** 10 satırın 5'i `YOK` (negatif kanıt), 1'i "VAR ama güvenlik dışı" (UA
algılama), 4'ü uygulama katmanı pozitif kanıttır. **Hiçbir pozitif satır Tizen
sandbox sınırına ait değildir.** `realpath` + `str_starts_with` bir **yol
traversal denetimidir** — SMACK etiketi, privilege beyanı veya süreç izolasyonu
değildir; bunları "sandbox" diye sunmak abartı olurdu (bkz. §6 S2).

## §4 Sınır cümleleri

> **K010 ayrimi (baglayici):** Kimlik dogrulama, oturum, sifre ve MFA -> `K0100-identitiy`; bu kartta yalnizca Tizen dosya/surec/erisim kisitlari ele alinir.

> **K009 ayrimi (baglayici):** OWASP denetimi, auth/CSRF/CSP tasarimi, kripto secimi (ADR-022) -> `K009-security`; bu kart yalnız OS katmani + sinir isaretlerini tasinir.

> **Komşu dal:** Linux cekirdegi ici yapisi -> `isletim-sistemi-kernel`; Tizen platform geneli -> `isletim-sistemi-turu/tizen`.

## §5 Referans

| URL | İddia | 2026-10-14 durum |
|---|---|---|
| https://samsungtizenos.com/docs/application/native/guides/concepts/file-system | Paket dizin hiyerarşisi ve izinleri (`Others: Access denied` / `shared` okunur / `shared/trusted` aile); dış depolama herkese açık | 2026-10-14 · HTTP 200 (fetch edildi, ham `.md` de 200) |
| https://samsungtizenos.com/docs/application/native/guides/security/security-tip | SMACK sandbox; dosya paylaşım güven tablosu (Strong/Weak); `APP_CONTROL_DATA_PATH` geçici izin; trusted message port author sertifikası denetimi | 2026-10-14 · HTTP 200 (fetch edildi, ham `.md` de 200) |
| https://samsungtizenos.com/docs/application/native/guides/security/privacy-related-permissions | `ppm_check_permission()`; 4.0+ istek zamanı seçimi; 5.5+ group mode → cevapsız istek **Deny**; fonksiyonlar thread-safe değil | 2026-10-14 · HTTP 200 (fetch edildi, ham `.md` de 200) |
| https://samsungtizenos.com/docs/application/native/reference/security-privileges | `appdir.shareddata` 3.0+ zorunlu; `internet` privilege çekirdek katmanında engellenir; sandboxing nedeniyle loopback rastgele bağlantı yok | 2026-10-14 · HTTP 200 (fetch edildi, ham `.md` de 200) |
| https://docs.tizen.org/application/security/ | Görev havuzundaki URL — **içerik taşımıyor** | 2026-10-14 · HTTP 200 (302 → `samsungtizenos.com/docs/` açılış sayfası) |
| https://docs.tizen.org/application/native/tutorials/security/ | Görev havuzundaki URL — **içerik taşımıyor** | 2026-10-14 · HTTP 200 (302 → `samsungtizenos.com/docs/` açılış sayfası) |

> **Dürüstlük notu (zorunlu):** 6 satırın 4'ü bu oturumda **içerik olarak okunmuş**
> ve HTTP 200 döndürmüştür (ham `.md` kaynakları `raw.githubusercontent.com/Samsung/tizen-docs/master`
> üzerinden de 200 ile çekilmiştir). 5. ve 6. satır olan `docs.tizen.org` havuz
> URL'leri **200 döner ama tüm alt yolları ana sayfaya yönlendirir** — sayfa içeriği
> **yoktur**; bu iki satır kanıt olarak **kullanılmaz**, yalnızca taşıma kaydıdır.
> "Tizen sandbox profili" başlıklı ayrı bir upstream sayfası bu oturumda
> **okunamamıştır**; sandbox iddiaları yukarıdaki 4 sayfadan alınmıştır.
> Bu kartta uydurma CVE, uydurma API adı veya "repo'da uygulanıyor" iddiası **yoktur**.

## §6 Açık sorular

| # | Soru | Durum |
|---|---|---|
| S1 | OS guvenlik katmani hedef mimaride devreye alinacak mi? | `[UNKNOWN]` — kapatılmaz; mimari sahibi kararı + `>=088` ADR ister |
| S2 | Uygulama <-> OS guvenlik sorumluluk siniri? | `[UNKNOWN]` — §3'teki 4 pozitif satır PHP uygulama katmanında; Tizen sandbox/privilege sınırı repoda hiç başlamamış |

**KAPATMA YASAĞI:** Bu iki soru bu kartta **kapatılamaz**; kapatma yalnız mimari
sahibinin yazılı kararı ve yeni ADR (`>=088`) ile olur.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
