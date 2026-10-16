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
| Sınır | Bu blok paylaşım **politikasıdır**; kimlik doğrulama değildir → `K010-identitiy` |

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

> **K010 ayrimi (baglayici):** Kimlik dogrulama, oturum, sifre ve MFA -> `K010-identitiy`; bu kartta yalnizca Tizen dosya/surec/erisim kisitlari ele alinir.

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

---

## §7 Çapraz kart matrisi (Tizen uygulama sınırı kontrol haritası)

§1 sandbox profili, §2 kalıplar (paket izinleri / çalışma zamanı denetimi /
paylaşım güven dereceleri) ve §5 referans burada çaprazlanır; kanıtsız kontrol "var" sayılmaz.

| # | Sınır kontrolü | Yüzey (§ referansı) | Sağlayıcı katman | Doğrulama tipi | Öncelik |
|---|---|---|---|---|---|
| K1 | Paket dizin hiyerarşisi izinleri | §2.1 dizin izinleri | Dosya sistemi + kurulum | Statik | P0 |
| K2 | Çalışma zamanı izin denetimi | §2.2 runtime denetim | Runtime/privilege | Dinamik | P0 |
| K3 | Dosya paylaşımı güven dereceleri | §2.3 paylaşım | App data paylaşımı | Statik + dinamik | P1 |
| K4 | Paket imzası + mağaza doğrulaması | §1 paket katmanı | Paket yöneticisi | Statik | P0 |
| K5 | Uygulama sandbox (app/private alan) | §1 depolama | Sandbox | Statik + dinamik | P0 |
| K6 | Ağ erişim profili / izin | §1 ağ | Ağ politikası | Dinamik | P1 |
| K7 | Cihaz API'sine erişim (cihaz izni) | §1 cihaz katmanı | API privilege | Dinamik | P1 |
| K8 | Periyodik/zamanlanmış görev sınırı | §1 zamanlayıcı | Scheduler/privilege | Dinamik | P2 |

### §7.1 Matris okuma kuralı

1. **Statik kanıt** — manifest/izin dosyası, dizin ağacı listesi (Dosya: ile).
2. **Dinamik kanıt** — çalışma zamanı ret (izin yok / güvenlik ihlali olayı).
3. **Ölçüm kanatı** — yeniden üretilebilir komut çıktısı.

Kanıtsız satır `VERIFICATION REQUIRED` kalır.

### §7.2 Çapraz kart eşlemesi (sınır → kart)

| Kart kategorisi | İlgili kontroller | Bu dosyadaki karşılığı |
|---|---|---|
| Kimlik kartları | K4, K7 | §1 paket/cihaz, §10 S1/S4 |
| Depolama kartları | K1, K3, K5 | §2.1/§2.3, §10 S2 |
| Bağlantı kartları | K6 | §10 S3 |
| Zaman kartları | K8 | §7 K8, §13 T4 |
| Uygulama kartları | K2 | §2.2, §10 S5 |
| Paket kartları | K4 | §1, §13 N1 |

## §8 Ses/RT yüzeyi (Tizen için PLANNED senaryo)

> **DURUM: PLANNED** — Zincir gerçek cihazda bu turda doğrulanmadı.

### §8.1 Neden bu bölüm burada?

Ses alt sistemi (cihaz API'si üzerinden ses/sıra erişimi, çalma listesi
paylaşımı) uygulama sınırının ayrıcalıklı noktasıdır: cihaz erişim izni K7
ile, veri paylaşımı K3 ile, sandbox K5 ile kesişir. Ses algoritması/kalite bu
dosyanın dışındadır.

### §8.2 PLANNED senaryo zinciri (Ses ↔ Sınır)

| Adım | Senaryo | Beklenen yüzey | Durum |
|---|---|---|---|
| A1 | Uygulama ses cihazı API'sine erişir | Cihaz izni (K7) | PLANNED |
| A2 | Çalma verisi paylaşılır | Güven derecesi denetimi (K3) | PLANNED |
| A3 | Ses önbelleği sandbox içinde tutulur | K5 alanı | PLANNED |
| A4 | Uygulama kapanır | Zamanlanmış görev/kanal temizlenir (§9 M4) | PLANNED |

**Kapsam dışı:** ses algoritması/codec. **Harici referans:** `.ai/electronic/drivers/audio-drivers.md` — bu turda yalnız **okuma** (beyazliste dışı yazım yasağı).

## §9 Başarısızlık modları (Tizen uygulama sınırı)

| Mod | Tetikleyici | Gözlenen belirti | Sınır etkisi | Sürdürme/geri alma |
|---|---|---|---|---|
| M1 | Paket imza reddi | Kurulum/açılış ret | Paket düşer | İmzalı paket |
| M2 | Dizin izni ihlali | EACCES/izin ret | Erişim kesilir | §2.1 denetimi |
| M3 | Çalışma zamanı izin ret | Güvenlik ihlali olayı | API çağrısı düşer | §2.2 denetimi |
| M4 | Uygulama çökmesi | Süreç ölümü | Kaynak sızıntısı | Görev temizliği |
| M5 | Paylaşım güven derecesi ihlali | Paylaşım ret | Veri erişimi kesilir | §2.3 denetimi |
| M6 | Ağ ret | Bağlantı hatası | Bağlantı düşer | Ağ profili |
| M7 | Cihaz izni ret | API ret | Özellik kapalı | İzin ayarı |
| M8 | Zamanlanmış görev kötüye kullanımı | Beklenmeyen uyanma | Kaynak/uyku ihlali | §7 K8 denetimi |

### §9.1 Fail-safe matrisi (öncelik sırası)

1. **P0 — M1, M2, M3, M5:** fail-closed, ret kanıtı zorunlu.
2. **P1 — M6, M7:** tek özellik.
3. **P2 — M4, M8:** yalnız süreç/görev.

`fail-open` hiçbir modda kabul edilmez. 3 başarısız turda dur + üst karar.

## §10 Kullanım senaryoları

Kalıp: **kural → yüzey → doğrulama → sonuç (işaretli)**.

### §10.1 S1 — Paket imza doğrulaması (P0)

- **Kural:** İmzasız/paket reddi (K4, §9 M1).
- **Yüzey:** Paket yöneticisi (§1).
- **Doğrulama:** Test paketi → beklenen: kurulum ret.
- **Sonuç:** Kurulursa K4 ihlali → P0. (Durum: PLANNED.)

### §10.2 S2 — Dizin izni sınırı (P0)

- **Kural:** Manifest dışı dizin erişilemez (K1, §9 M2).
- **Yüzey:** Paket dizin hiyerarşisi (§2.1).
- **Doğrulama:** Dış dizine okuma/yazma → beklenen: ret.
- **Sonuç:** Geçerse K1 ihlali → P0. (Durum: PLANNED.)

### §10.3 S3 — Ağ izni (P1)

- **Kural:** Ağ izni yoksa bağlantı ret (K6, §9 M6).
- **Yüzey:** Ağ politikası (§1).
- **Doğrulama:** Bağlantı denemesi → beklenen: ret.
- **Sonuç:** Bağlanırsa K6 ihlali. (Durum: PLANNED.)

### §10.4 S4 — Cihaz izni (P1)

- **Kural:** İzinli olmayan cihaz API'si ret (K7, §9 M7).
- **Yüzey:** API privilege (§1).
- **Doğrulama:** İzinsiz API çağrısı → beklenen: ret.
- **Sonuç:** Geçerse K7 ihlali → P1. (Durum: PLANNED.)

### §10.5 S5 — Çalışma zamanı izin denetimi (P0)

- **Kural:** Runtime'da izin değişimi/ihlali engellenir (K2, §9 M3).
- **Yüzey:** Runtime denetim (§2.2).
- **Doğrulama:** Runtime ihlal denemesi → beklenen: ret + olay.
- **Sonuç:** Geçerse K2 ihlali → P0. (Durum: PLANNED.)

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
| Tizen uygulama/güvenlik dokümanı | K1–K8 | Erişilebilir URL eklenince işaretlenir |
| Cihaz/log örnekleri | §10 | ⚠️ VERIFICATION REQUIRED (alınmadı) |
| Manifest/izin çıktıları | statik kanıt | ⚠️ VERIFICATION REQUIRED (PLANNED) |

### §11.3 Kaynak güven sırası

1. Cihaz ölçüm/log. 2. Dosya içi § referansı. 3. Vault içi kardeş dosya.
4. Tarihli erişilebilir URL. 5. Model hafızası — tek dayanak asla.

## §12 Eşleme ağacı (okuma sırası)

```text
uygulama-guvenlik-siniri-tizen.md
|-- Sec0 ELI15
|-- Sec1 Tizen sandbox/sinir profili
|-- Sec2 Kod ornekleri (2.1 dizin | 2.2 runtime | 2.3 paylasim)
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
| T1 | İmzalı paket kur (K4) | Kurulum başarılı |
| T2 | İzinli dizin erişimi (K1) | Başarılı |
| T3 | Paylaşımlı veri okuması (K3) | Başarılı |
| T4 | Zamanlanmış görev ölçümü (K8) | Beklenen zamanlama |

### §13.2 Negatif kontroller (RED TEAM — reddin kanıtı)

| # | Negatif adım | Beklenen ret | İhlal varsa |
|---|---|---|---|
| N1 | İmzasız paket kurulumu | M1 ret | K4 ihlali → P0 |
| N2 | Manifest dışı dizin erişimi | M2 ret | K1 ihlali → P0 |
| N3 | Runtime izin ihlali | M3 ret + olay | K2 ihlali → P0 |
| N4 | Paylaşım dışı veri okuması | M5 ret | K3 ihlali → P1 |
| N5 | İzinsiz ağ bağlantısı | M6 ret | K6 ihlali → P1 |
| N6 | İzinsiz cihaz API'si | M7 ret | K7 ihlali → P1 |
| N7 | Beklenmeyen zamanlanmış görev | M8 olay | K8 ihlali → P2 |

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
| M5 tekrarı | Paylaşım güven derecesi denetimi |
| M8 tekrarı | Zamanlayıcı/uyku ihlali incelemesi |

Log append-only; `ts` geriye sarılmaz; `kanit` boş olay sayılmaz; P0 olayları
bir sonraki §13.2 turunda tekrar denenir. §6 S1/S2 etkilenmez; frontmatter
değişmez. Sıra: P0 → K1, K2, K4, K5 → P1 → K3, K6, K7 → P2 → K8/M4/M8.

### §15 Append-only + kapı özeti (Tizen eki protokolü)

Her gelecek eki yalnız Authority kuyruğundan sonra eklenir; §0–§14,
frontmatter ve Authority satırı değişmez; sıradaki numara §16'dır. Yasaklar:
silme/yeniden yazma · dolgu/TBD · §6 S1/S2 kapatma · frontmatter değişikliği ·
Mojibake. Ekleme sonrası ölç; 500'ün altındaysa §<n+1> kaynak atıflı eklenir.

### §16 Zorunlu 8 soruluk kapı özeti (son okuma)

| # | Soru | Cevap referansı |
|---|---|---|
| 1 | Paket imzası doğrulanıyor mu? | §13.2 N1 |
| 2 | Dizin izinleri manifest'e uygun mu? | §2.1 + §13.2 N2 |
| 3 | Runtime denetim etkin mi? | §2.2 + §13.2 N3 |
| 4 | Paylaşım güven dereceleri net mi? | §2.3 + §13.2 N4 |
| 5 | Sandbox alanı korunuyor mu? | §7 K5 |
| 6 | Ağ izni doğru mu? | §13.2 N5 |
| 7 | Cihaz izinleri dar mı? | §13.2 N6 |
| 8 | Zamanlanmış görev sınırı var mı? | §13.2 N7 |

3 dk rota: §16 → §9.1 → §6 (oku, **kapatma**). Hatalar: kanıtsız "çalışıyor"
iddiası, PLANNED'ı geçiş saymak, log düzenlemek, fail-open yorumu.
Sonraki tur: §8 → cihaz ölçümü · §10 → dinamik kanıt · §11.2 → tarihli URL ·
§13.2 → §14 olayı. Bu işler bu turda **yapılmadı**.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

---

## §17 Uygulama katmanı ayrıntı kontrol listesi (Tizen)

§7 matrisinin genişletilmiş sürümü; her satır bağımsız kanıtlanabilir (§7.1).

### §17.1 Paket/dizin sınıfı ayrıntılar

| # | Ayrıntı | Beklenen | Kanıt |
|---|---|---|---|
| A1 | Paket kök dizin sahibi | Paket/uygulama kimliği | dizin listesi |
| A2 | Veri dizini yazma kapsamı | Yalnız kendi verisi | EACCES kanıtı |
| A3 | Paylaşılan veri (public) kapsamı | Sadece beyan edilen | paylaşım ret |
| A4 | Ortam/config dosyası | Salt okunur / kapsamı dar | mount/perms |
| A5 | Geçici dizin | Sınır içi, temizlenir | envanter |
| A6 | İmza dosyaları/metadata | İmza doğrulama akışı | kurulum logu |
| A7 | Kütüphane yükleme yolu | Paket içi | yükleme ret |
| A8 | Çalıştırılabilir bitler | İmzalı/izinli | bit + imza |

### §17.2 Runtime/cihaz sınıfı ayrıntılar

| # | Ayrıntı | Beklenen | Kanıt |
|---|---|---|---|
| B1 | Runtime izin listesi (manifest) | En az yetki ilkesi | manifest dökümü |
| B2 | Çalışma zamanı izin istekleri | Runtime ret | olay |
| B3 | Cihaz API'si istekleri | İzinli çağrılar çalışır | API ret |
| B4 | Ağ istekleri | İzinli trafik | bağlantı ret |
| B5 | Zamanlanmış görev izni | Beyan + sınır | görev listesi |
| B6 | Arkaplan/ayrıntı davranışı | Kısıt | uyanma olayı |
| B7 | Oturum/uygulama durumları | Süreç yaşam döngüsü | süreç logu |
| B8 | İzin geri çekilmesi | Geri çekilişte erişim ret | TCC benzeri ret |

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
C: P0 (A1, A2, A6, A8, B1, B2) önce; B5–B8 son.

**S: Kanıt nereye?**
C: Bu dosyaya eki satır; mevcut satırlar değişmez.

**S: Ne zaman tekrar?**
C: Paket/manifest değişince + haftalık (§17.6).

### §17.6 Ölçüm tekrarlanabilirlik tablosu

| Ölçüm | Kaynak | Sıklık |
|---|---|---|
| Dizin izinleri | §17.1 | Paket kurulumunda |
| Runtime izinleri | §17.2 | Manifest değişiminde |
| Zamanlama davranışı | §17.2 B5 | Haftalık |
| Olay şeması bütünlüğü | §14 | Olay başına |

### §17.7 Süreklilik

§15 append protokolü + §16 kapı özeti birlikte geçerlidir. Bir sonraki eki
yazacak kişi: §16 → §9.1 → §6 okur, sonra yalnız Authority kuyruğundan sonra
§18 ekler. Ölç-ekle-devam döngüsü sürer; dolgu yasak.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
