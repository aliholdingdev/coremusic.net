---
node_id: K000-isletim-sistemi/isletim-sistemi-runtime/web-runtime-topolojisi
node_type: subdomain
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-runtime
children:
  - K000-isletim-sistemi/isletim-sistemi-runtime/web-runtime-topolojisi/anayasa
  - K000-isletim-sistemi/isletim-sistemi-runtime/web-runtime-topolojisi/surucu
  - K000-isletim-sistemi/isletim-sistemi-runtime/web-runtime-topolojisi/ornekler
depends_on:
  - isletim-sistemi-kernel
  - isletim-sistemi-core
evidence:
  - httpd.apache.org/docs/2.4/mpm.html yanıt kodu 2026-10-13 (HTTP 200)
  - nginx.org/en/docs/http/request_processing.html yanıt kodu 2026-10-13 (HTTP 200)
  - learn.microsoft.com/en-us/iis/configuration/system.webServer/ yanıt kodu 2026-10-13 (HTTP 200)
  - home.coremusic.net/.htaccess (37 satır)
  - home.coremusic.net/web.config (43 satır)
  - shared/config/domain.php (19 satır)
  - https://learn.microsoft.com/en-us/iis/configuration/system.webServer/fastCgi/
validation: "web: MPM · nginx istek işleme · IIS system.webServer dokümanları HEAD ile 2026-10-13 doğrulandı (HTTP 200). disk: .htaccess / web.config / domain.php satır içerikleri Read ile birebir okundu."
open_questions:
  - "R1: Hedef runtime topolojisi kesin değil — Apache (mod_php/FPM) mı, IIS mi, ikisi de mi? Repo'da ikisinin de config'i var (çift hedef). [UNKNOWN — Q4 ile ilişkili]"
  - "R3: PHP Apache üzerinde mod_php mi, FPM mi çalışıyor? Repo'da FPM pool config'i yok. [.htaccess php_flag yalnız mod_php'de etkilidir — ⚠️ VERIFICATION REQUIRED]"
  - "R7: Sunucu MPM'i (event/worker/prefork) veya FastCGI havuzu hangi kapasitede? [⚠️ VERIFICATION REQUIRED]"
group: mimari
---

# web-runtime-topolojisi

> **Kapsam (tek cümle):** CoreMusic'in çalıştığı **web sunucusu çalışma zamanı topolojisi** — Apache MPM süreç modeli (prefork/worker/event), IIS app-pool/worker process, nginx + PHP-FPM FastCGI zinciri ve repo'daki `.htaccess`/`web.config` çift hedefinin bu modellerdeki yeri.
> Bu alt-dal Kademe-3'tür (kök `isletim-sistemi-runtime` altında); üç içerik dosyası sınır (anayasa), yürütme (surucu) ve kanıt (ornekler) katmanını taşır.

## §0 ELI15

CoreMusic tek bir kod tabanından **iki web sunucusu** (Apache + IIS) hedefler; `.htaccess` ve `web.config` çifti bu çift hedefin kanıtıdır. Bu alt-dal, isteğin sunucu process'ine girişinden PHP kodunun çalışmasına kadar olan topolojiyi (MPM / IIS app-pool / FastCGI zinciri) kavram düzeyinde tanımlar; uygulama içi bootstrap `baslangic-ve-bootstrap` alt-dalındadır.

## İçerik Dosyaları

| Dosya | Grup | Ne içerir |
|---|---|---|
| [`anayasa.md`](anayasa.md) | mimari | MPM/IIS/FPM süreç modelleri, çift hedef kuralı, vhost/subdomain topolojisi |
| [`surucu.md`](surucu.md) | mimari | `.htaccess`/`web.config` yürütme haritası, AllowOverride sınırı, hata modları |
| [`ornekler.md`](ornekler.md) | kod | Gerçek repo kodu: `.htaccess` (37), `web.config` (43), `domain.php` (19) |

## Katman Konumu

| Katman | Repo'da | Sunucu düzeyi |
|---|---|---|
| İstek kabulü | ⚠️ dolaylı (çift config) | MPM worker / app-pool worker process |
| Rewrite / front controller | ✅ `.htaccess` + `web.config` | mod_rewrite / IIS URL Rewrite |
| Alt alan eşlemesi | ✅ `domain.php` (7 subdomain) | name-based vhost / host header |
| FPM havuzu | ❌ yok (R3 ⚠️) | `www.conf` pool yapılandırması |

## Açık Sorular

R1 çift hedef (Apache/IIS) · R3 mod_php-vs-FPM · R7 MPM/FPM kapasitesi — üçü de [UNKNOWN] / [⚠️ VERIFICATION REQUIRED]; ayrıntı `anayasa.md` §8.

## Komşu Sınır Matrisi

| Komşu | Paylaşılan sınır | Bu alt-dalın sahibi | Komşunun sahibi |
|---|---|---|---|
| `isletim-sistemi-core` | Süreç/parçacık yaşam döngüsü | Worker process döngüsü | `core` süreç tablosu |
| `surec-ve-parcacik` | Process modeli (fork/CreateProcess) | MPM/app-pool süreç şekli | `surec-ve-parcacik` semantiği |
| `baslangic-ve-bootstrap` | PHP istek boot sırası | Sunucu → PHP devri (SAPI) | `baslangic-ve-bootstrap` boot |
| `isletim-sistemi-api` | HTTP giriş kapısı | Vhost + rewrite eşlemesi | `api` route/dispatch |
| `isletim-sistemi-security` | Güvenlik başlıkları / 403 filtresi | `.htaccess` FilesMatch + headers | `security` auth/CSRF |
| `isletim-sistemi-observability` | Erişim logu | Sunucu erişim logu | `observability` metrik |
| `K011-application` | Subdomain haritası | `domain.php` 7 subdomain | `K011` uygulama mantığı |
| `K17-devops` | Sunucu dağıtım hedefi | MPM/havuz kapasite tanımı | `K018` deploy pipeline |

## Web Research

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| Apache MPM modülleri (event/worker/prefork) resmi dokümanda sunucu süreç modelini tanımlar; `.htaccess`/`web.config` bu modelin üstünde çalışır (R7 iddiası). | `https://httpd.apache.org/docs/2.4/mpm.html` | 2026-10-13 | Doğrulandı (HTTP 200, resmi doküman) |
| nginx istek işleme sırası (vhost eşlemesi → rewrite → PHP) resmi dokümanda anlatılır; repo `web.config`/`.htaccess` eşdeğerlerinin nginx karşılığı bu sıradadır (R1 çapraz hedef iddiası). | `https://nginx.org/en/docs/http/request_processing.html` | 2026-10-13 | Doğrulandı (HTTP 200, resmi doküman) |
| Microsoft IIS `system.webServer` yapılandırma bölümü resmi dokümantasyonundadır; repo `web.config` (43 satır) bu bölümün altındaki `httpProtocol` + `rewrite` bloklarını kullanır (R1 IIS hedefi iddiası). | `https://learn.microsoft.com/en-us/iis/configuration/system.webServer/` | 2026-10-13 | Doğrulandı (HTTP 200, resmi doküman) |

---

## § OS GRUPLAR — sekiz işletim sistemi grubu

| # | OS | Web sunucu/runtime topolojisi | Bu dosyadaki karşılığı | Kaynak |
|---|---|---|---|---|
| 1 | Windows | IIS: HTTP.sys → app-pool → worker process; PHP FastCGI handler | § Katman Konumu — IIS satırı (`web.config` 43 satır) | repo `home.coremusic.net/web.config:1-43` · IIS fastCgi dokümanı (bu tur eklendi) |
| 2 | macOS | gömülü Apache httpd — MPM süreç modeli + `.htaccess` | §0 ELI15 — Apache yüzeyi | `httpd.apache.org/docs/2.4/mpm.html` (mevcut kanıt) |
| 3 | iOS | ayrı web sunucusu topolojisi bu projede tanımlı değil | karşılığı yok (kapsam dışı) | repo taraması: bu projede kullanılmıyor |
| 4 | Android | ayrı web sunucusu topolojisi bu projede tanımlı değil | karşılığı yok (kapsam dışı) | repo taraması: bu projede kullanılmıyor |
| 5 | Linux | nginx veya Apache + PHP-FPM FastCGI zinciri | § Katman Konumu — FPM havuzu satırı (repo'da yok, R3) | `nginx.org/en/docs/http/request_processing.html` (mevcut kanıt) |
| 6 | FreeBSD/BSD | ports/paket Apache veya nginx — POSIX süreç modeli | Komşu Sınır Matrisi — `surec-ve-parcacik` | repo taraması: bu projede kullanılmıyor |
| 7 | Tizen | ayrı sunucu topolojisi yok (yerleşik web runtime) | karşılığı yok (kapsam dışı) | repo taraması: bu projede kullanılmıyor |
| 8 | webOS | ayrı sunucu topolojisi yok (yerleşik web runtime) | karşılığı yok (kapsam dışı) | repo taraması: bu projede kullanılmıyor |

> OS.1 uzun açıklamalar ve OS.2 fastCgi örneği içerik dosyalarındadır — repo'da dört `web.config` tarandı, `fastCgi` düğümü hiçbiri geçmez (disk kanıtı). Kaynak: `https://learn.microsoft.com/en-us/iis/configuration/system.webServer/fastCgi/` (2026-10-14 HTTP 200).

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
