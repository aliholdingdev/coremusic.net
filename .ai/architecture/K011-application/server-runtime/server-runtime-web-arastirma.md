---
title: "K011 · Server-Runtime — Web Araştırması"
type: report
kodeme: kademe-2
module: K011-application
parent: server-runtime
status: active
updated: 2026-10-15
group: kanit
---

# K011 · Server-Runtime — Web Araştırması (Kanıt)

> **Amaç:** `server-runtime` modülünün web araştırma havuzunu — **yalnızca
> daha önce doğrulanmış (HTTP 200)** URL kümeleriyle — sunmak; her URL'nin
> modülle ilişkisini repo kanıtıyla bağlamak; havuz izolasyonunu (kardeş
> `queries` modülüyle kesişim 0) kanıtlamak.

## §GRUPLAR

| # | Bölüm | Grup | Eksen 2 | Kullanım |
|---|---|---|---|---|
| 1 | §1 Havuz tablosu (6 URL) | kanit | havuz | Ana envanter |
| 2 | §2 URL → repo kanıtı eşlemesi | kanit | esleme | Neden bu URL |
| 3 | §3 Doğrulama kaydı (HTTP 200) | kanit | dogrulama | Erişim kanıtı |
| 4 | §4 Reddedilen adaylar (etiket) | kanit | red | Seçilmeyenler |
| 5 | §5 Kesişim-sıfır PowerShell bloğu | kanit | kesisim | İzolasyon |
| 6 | §6 URL envanter kontrolü | kanit | kontrol | Sayaç |

---

## §1 Havuz Tablosu (server-runtime — bu dosyada münhasır)

| # | URL | Kaynak | Konu | Erişim |
|---|---|---|---|---|
| 1 | `https://nginx.org/en/docs/http/ngx_http_upstream_module.html` | nginx.org | upstream/proxy_pass sunucu grubu, keepalive, balance | HTTP 200 ✅ |
| 2 | `https://nginx.org/en/docs/http/ngx_http_proxy_module.html` | nginx.org | proxy_buffering, proxy_set_header, timeout'lar, WebSocket upgrade | HTTP 200 ✅ |
| 3 | `https://nginx.org/en/docs/http/ngx_http_ssl_module.html` | nginx.org | TLS 1.2/1.3, cipher suite, HSTS, session cache | HTTP 200 ✅ |
| 4 | `https://nginx.org/en/docs/http/ngx_http_log_module.html` | nginx.org | access_log, `log_format` (JSON), `$request_time`, buffer | HTTP 200 ✅ |
| 5 | `https://nginx.org/en/docs/http/ngx_http_rewrite_module.html` | nginx.org | rewrite/return/try_files — Apache `.htaccess` karşılığı | HTTP 200 ✅ |
| 6 | `https://www.php.net/manual/en/installation.php` | php.net | PHP kurulum/çalışma zamanı (Unix/Windows), SAPI | HTTP 200 ✅ |

**Toplam: 6 URL.** Tümü `server-runtime-web-arastirma.md` içinde yaşar;
kardeş `queries` dosyasında **isabeti yoktur** (§5).

---

## §2 URL → Repo Kanıtı Eşlemesi

| # | Havuz konusu | Repo kanıtı | İlişki |
|---|---|---|---|
| 1 | upstream/proxy_pass | `api.coremusic.net/index.php` L50-53 (`X-Forwarded-Proto` okuması — proxy arkasındaki TLS sonlandırma varsayımı) | Üretimde reverse-proxy varsa bu başlık nginx `proxy_set_header` ile gelir; PHP tarafı L52'de güvenle okur |
| 2 | proxy_buffering/set_header | `index.php` L184-187 (son header'lar) + `ResponseEmitter.php` L70 (`Vary`) | Bufferlama/başlık aktarımı zincirinin uç noktası |
| 3 | ssl_module (TLS/HSTS) | `.htaccess` L9-16 · `web.config` L4-13 (güvenlik başlıkları) | TLS kendisi vault'ta belgelenmemiş (⚠️ §4.3); bu URL TLS sonlandırma için referans |
| 4 | log_module (JSON access log) | `StructuredLogger.php` L61-72 (JSON `error_log` + `Redactor`) | Uygulama logu ↔ nginx access log formatı uyumu — traceId ile bağ |
| 5 | rewrite_module | `home.coremusic.net/.htaccess` L26-37 · `web.config` L14-41 | try_files/return ↔ RewriteRule paritesi (kapsam §6.5) |
| 6 | php.net installation (SAPI) | `RuntimeBootstrap.php` L7-20 · `.htaccess` L5-7 (`php_flag`) | SAPI/çalışma zamanı davranışı — mod_php vs FPM farkı `php_flag` geçerliliğini belirler |

### §2.1 nginx neden bu havuzda (PLANNED — disk kanıtı yok)

Repo'da nginx yapılandırma dosyası **yoktur** (0 glob isabet —
`nginx.conf`, `*.conf` sunucu kökü). nginx dokümanları **üretim hedefi**
için PLANNED referans taşır: home RPi5 ortamında Apache/IIS paritesinin
nginx'e taşınması senaryosu. Durum: `⚠️ VERIFICATION REQUIRED` — üretim
sunucusunun nginx olup olmadığı vault'a yazılmamıştır.

---

## §3 Doğrulama Kaydı

| # | URL | Yöntem | Sonuç |
|---|---|---|---|
| 1 | ngx_http_upstream_module.html | fetch (önceden) | HTTP 200 |
| 2 | ngx_http_proxy_module.html | fetch (önceden) | HTTP 200 |
| 3 | ngx_http_ssl_module.html | fetch (önceden) | HTTP 200 |
| 4 | ngx_http_log_module.html | fetch (önceden) | HTTP 200 |
| 5 | ngx_http_rewrite_module.html | fetch (önceden) | HTTP 200 |
| 6 | php.net/manual/en/installation.php | fetch (önceden) | HTTP 200 |

> **Not (kural):** `dev.mysql.com` erişilemez (403) — bu alan adı havuzda
> **hiçbir modülde** kullanılmaz (queries web-arastirma §4 ile ortak kural).
> Yeni URL ancak fetch ile 200 doğrulanıp havuza eklenir.

---

## §4 Reddedilen Adaylar (etiket — tam URL YOK)

| # | Aday (etiket) | Red gerekçesi |
|---|---|---|
| R-1 | nginx fastcgi modülü dokümanı | PHP-FPM davranışı vault'a yazılmadı; SAPI sorusu php.net installation ile karşılanıyor |
| R-2 | nginx map/geo modülü | Kapsam dışı — coğrafi/UA eşleme yok |
| R-3 | php.net Windows kurulum alt sayfası | `installation.php` yeterli; alt sayfa tekrar olurdu |
| R-4 | nginx daemon/processes sayfası | DevOps düzeyi — bu K2 modülünün konusu değil |
| R-5 | php.net configure komut satırı | Üretim SAPI ayarı bilinmiyor (⚠️), spekülasyon olurdu |

---

## §5 Kesişim-Sıfır PowerShell Bloğu (belge — çalıştırılmadı)

> **Çalıştırma durumu:** `shell` aracı bu oturumda `permission.rejected`
> döndürdü — aşağıdaki betik **belge olarak** yazılmıştır, çalıştırılmamıştır.
> Eşdeğer kontrol grep tool ile yapılmıştır (aşağıdaki §5.1).

```powershell
# K011 Kesişim Kontrolü — server-runtime havuzu queries havuzuyla kesişiyor mu?
# Repo kökünden (C:\www\coremusic.net) çalıştırılır.

$rt = @(
  'ngx_http_upstream_module.html',
  'ngx_http_proxy_module.html',
  'ngx_http_ssl_module.html',
  'ngx_http_log_module.html',
  'ngx_http_rewrite_module.html',
  'php.net/manual/en/installation.php'
)
$queries = @(
  'pdo.prepare.php', 'pdo.query.php', 'pdo.exec.php',
  'pdostatement.execute.php', 'pdostatement.fetch.php'
)

$qDir = '.ai\architecture\K011-application\queries'
$rtDir = '.ai\architecture\K011-application\server-runtime'

# 1) server-runtime URL'leri queries klasöründe aranır → 0 beklenir
$hitsQ = Select-String -Path "$qDir\*.md" -Pattern ($rt -join '|') -SimpleMatch:$false
# 2) queries URL'leri server-runtime klasöründe aranır → 0 beklenir
$hitsR = Select-String -Path "$rtDir\*.md" -Pattern ($queries -join '|')

"queries içinde server-runtime URL isabet: $($hitsQ.Count)  (beklenen 0)"
"server-runtime içinde queries URL isabet:  $($hitsR.Count)  (beklenen 0)"
if ($hitsQ.Count -eq 0 -and $hitsR.Count -eq 0) { "KESISIM -> 0 · PASS" } else { "KESISIM -> FAIL" }
```

### §5.1 grep ile yapılan gerçek kontrol (çalıştırıldı)

| Kontrol | Desen | Aranan yer | Sonuç |
|---|---|---|---|
| A | `ngx_http` | `.ai/architecture` (tüm md) | Yalnız `server-runtime-web-arastirma.md` (bu dosya) — queries klasöründe 0 |
| B | `installation.php` | `.ai/architecture` (tüm md) | Yalnız bu dosya — queries klasöründe 0 |
| C | `pdo.prepare\|pdo.query\|pdo.exec\|pdostatement` | `server-runtime/*.md` | 0 isabet |

**KESİŞİM = 0** (iki yönlü).

---

## §6 Envanter Kontrolü

| # | Kontrol | Değer |
|---|---|---|
| 1 | Bu dosyadaki tam URL sayısı | 6 |
| 2 | Havuz dışı tam URL | 0 (reddedilenler yalnız etiket — §4) |
| 3 | 200 doğrulanmış | 6/6 |
| 4 | Kardeşte kesişim | 0 |
| 5 | dev.mysql.com | 0 kullanım |

---

## §7 Havuz Derinlemesine — URL Başına Notlar

### §7.1 ngx_http_upstream_module (URL #1)

| Alan | Not |
|---|---|
| Konu | `upstream` bloğu: sunucu grubu tanımı, yük dağıtım (round-robin/least_conn/ip_hash), `keepalive`, `fail_timeout`/`max_fails` |
| Neden havuzda | `index.php` L52 `X-Forwarded-Proto` okuması reverse-proxy zinciri varsayar; PHP-FPM + nginx mimarisinde PHP'nin arkasındaki sunucu grubu bu modülle tanımlanır |
| Repo bağlantısı | `index.php` L50-63 (proxy header okuma + host/port ayrıştırma) |
| Beklenen kullanım | PLANNED nginx conf'unda `upstream php_fpm { server unix:...; keepalive 32; }` şablonu |
| Durum | Üretim conf yok → ⚠️ (kapsam §10.4) |

### §7.2 ngx_http_proxy_module (URL #2)

| Alan | Not |
|---|---|
| Konu | `proxy_pass`, `proxy_set_header` (Host/X-Real-IP/X-Forwarded-For/X-Forwarded-Proto), `proxy_buffering`, `proxy_read_timeout`, WebSocket `Upgrade`/`Connection` |
| Neden havuzda | `X-Forwarded-Proto` başlığının PHP tarafındaki okuması (L52) bu modülün `proxy_set_header X-Forwarded-Proto $scheme`送出u ile üretilir |
| Repo bağlantısı | `index.php` L52 · `ResponseEmitter.php` L70 (`Vary` — buffer/çeviri katmanı için) |
| Beklenen kullanım | home/api için `proxy_pass http://127.0.0.1:9000` (FPM) şablonu; buffer off SSE/için |
| Durum | PLANNED |

### §7.3 ngx_http_ssl_module (URL #3)

| Alan | Not |
|---|---|
| Konu | `ssl_protocols` (TLSv1.2/1.3), `ssl_ciphers`, `ssl_prefer_server_ciphers`, `ssl_session_cache`, HSTS (`add_header Strict-Transport-Security`) |
| Neden havuzda | TLS sonlandırma vault'ta belgelenmemiş (⚠️); güvenlik başlıkları katmanı (`.htaccess` L9-16, `web.config` L4-13) HSTS'i **içermez** → nginx'e taşınırsa HSTS bu modülden eklenir |
| Repo bağlantısı | Eksik başlık kanıtı: HSTS `.htaccess`/`web.config`'te 0 isabet (dosya tamamı okundu) |
| Durum | PLANNED + parite açığı kaydı |

### §7.4 ngx_http_log_module (URL #4)

| Alan | Not |
|---|---|
| Konu | `access_log`, `log_format` (özellikle JSON formatı), `$request_time`, `$upstream_response_time`, `buffer`, `if=$condition` |
| Neden havuzda | `StructuredLogger` uygulama logunu JSON üretir (L61-72); nginx access log'u JSON'a çevrilirse traceId/alan adlarıyla tek pipeline oluşur |
| Repo bağlantısı | `StructuredLogger.php` L61-72 · `index.php` L96-98 (istek logu) · `FetchWrapper.js` L50-51 (`X-Trace-Id`) |
| Beklenen kullanım | `log_format json '{...}'` şablonu; `$request_time` ↔ `api_usage.response_time_ms` (sql L87) hizası |
| Durum | PLANNED |

### §7.5 ngx_http_rewrite_module (URL #5)

| Alan | Not |
|---|---|
| Konu | `rewrite`, `return`, `if`, `try_files`, `$is_args/$args` |
| Neden havuzda | Apache `.htaccess` L26-37 ve IIS `web.config` L14-41 ile **birebir parite** taşıması gereken kuralların nginx karşılığı |
| Repo bağlantısı | `.htaccess` L30-36 ↔ `web.config` L16-39 (kapsam §6.5 parite tablosu) |
| Eşleme | `-f/-d` passthrough → `try_files $uri $uri/ /index.php?$query_string` · `[F]` → `return 403` · `[QSA]` → `?` args koruma |
| Durum | PLANNED |

### §7.6 php.net installation.php (URL #6)

| Alan | Not |
|---|---|
| Konu | PHP kurulum yolları (Unix/Windows), SAPI türleri (mod_php/CGI/FPM/CLI), `configure` seçenekleri, ilk yapılandırma |
| Neden havuzda | `.htaccess` L5-7 `php_flag`/`php_value` **yalnız mod_php** Apache'de geçerlidir; üretim SAPI bilinmediği için (⚠️) SAPI davranışı referans gerektirir |
| Repo bağlantısı | `.htaccess` L5-7 · `RuntimeBootstrap.php` L11-19 (ini_set çalışma zamanı) |
| Kritik fark | FPM'de `php_flag` yok sayılır → `display_errors` production'da `RuntimeBootstrap` L16 ile kapatılır (savunma derinliği) |
| Durum | Aktif referans (SAPI kararı verilmedikçe) |

---

## §8 Directive Karşılaştırma Matrisi (Apache ↔ IIS ↔ nginx*)

| Kural | Apache | IIS | nginx (PLANNED karşılık) |
|---|---|---|---|
| SPA catch-all | `RewriteRule ^(.*)$ index.php [QSA,L]` (L36) | `Rewrite index.php` + appendQueryString (L36-39) | `try_files $uri $uri/ /index.php?$query_string` |
| Statik passthrough | `RewriteRule \.(css\|js\|...) - [L]` (L34) | `Static files` → None (L16-19) | `location ~* \.(css\|js\|...)$ { expires 30d; }` |
| Dizin/dosya passthrough | `-f/-d` → `[L]` (L30-32) | `SPA Router` IsFile/IsDirectory (L28-35) | `try_files` ilk argümanları |
| 403 engel (dizin) | `[F,L]` (L35) | CustomResponse 403 (L20-23) | `return 403;` |
| Hassas uzantı | `Require all denied` (L18-20) | CustomResponse 403 (L24-27) | `location ~* \.(env\|log\|...) { deny all; }` |
| Başlık ekleme | `Header always set` (L9-16) | `<customHeaders>` (L4-13) | `add_header` |
| Başlık kaldırma | `Header always unset` (L14-15) | `<remove>` (L6) | `proxy_hide_header` |
| Dizin listesi | `Options -Indexes` (L2) | (varsayılan kapalı) | `autoindex off;` |

> `*` nginx sütunu havuzdaki dokümanlardan türetilmiş **tasarım notudur**;
> repo'da nginx conf yok → PLANNED (kapsam §10.4). Hiçbir satır üretim
> yapılandırması olarak okunmamalıdır.

---

## §9 Havuz Üretim Kuralı (Süreç)

| # | Adım | Kural |
|---|---|---|
| 1 | Aday URL belirleme | Konu modülle ilgili olmalı (server-runtime: sunucu/runtime) |
| 2 | Erişim testi | fetch ile 200 doğrulanmadan havuza girmez |
| 3 | dev.mysql.com | Kalıcı yasak (403 — erişilemez) |
| 4 | Ekleme yeri | Yalnız bu modülün `-web-arastirma.md` dosyası |
| 5 | Kardeş kontrol | `queries` havuzuyla kesişim grep ile 0 teyit edilir |
| 6 | Reddedilen | Tam URL yazılmaz — yalnız etiket (§4) |

---

## §10 Sık Sorular

| # | Soru | Cevap |
|---|---|---|
| 1 | Neden 5 nginx URL? | Üç sunucu dosyası (rewrite/header/engel) + log/SSL zinciri nginx'e taşınma senaryosunu tamamlar |
| 2 | nginx conf yokken bu URL'ler neden? | PLANNED hedef mimari — ADR ile conf üretilmeden önce referans |
| 3 | php.net installation neden PDO sayfaları gibi değil? | PDO sayfaları `queries` havuzundadır; burada **çalışma zamanı/SAPI** konusu var — kesişimi önlemek için ayrıldı |
| 4 | URL'ler her oturumda yeniden mi test edilir? | §3 kaydı dondurulmuş; yeniden teyit §9 adım 2 ile yapılır |

---

## §11 Havuz Karar Kaydı (Neden 6, Neden Bu?)

| # | Aday konu | Karar | Gerekçe | Tarih |
|---|---|---|---|---|
| 1 | nginx upstream | ✅ ALINDI | proxy header zincirinin sunucu ucu | 2026-10-15 |
| 2 | nginx proxy | ✅ ALINDI | `X-Forwarded-Proto` üreten modül | 2026-10-15 |
| 3 | nginx ssl | ✅ ALINDI | HSTS parite açığı + TLS sonlandırma | 2026-10-15 |
| 4 | nginx log | ✅ ALINDI | JSON log hizası (`StructuredLogger`) | 2026-10-15 |
| 5 | nginx rewrite | ✅ ALINDI | `.htaccess`/`web.config` paritesi | 2026-10-15 |
| 6 | php.net installation | ✅ ALINDI | SAPI/`php_flag` geçerlilik sorusu | 2026-10-15 |
| 7 | nginx fastcgi | ❌ RED | SAPI kararı yok (§4 R-1) | 2026-10-15 |
| 8 | nginx map/geo | ❌ RED | kapsam dışı (§4 R-2) | 2026-10-15 |
| 9 | php.net Windows kurulum | ❌ RED | tekrar olurdu (§4 R-3) | 2026-10-15 |
| 10 | nginx daemon | ❌ RED | DevOps düzeyi (§4 R-4) | 2026-10-15 |
| 11 | php.net configure | ❌ RED | spekülasyon (§4 R-5) | 2026-10-15 |

---

## §12 PLANNED — nginx Konfigürasyon İskeleti (Tasarım Notu)

> **UYARI:** Aşağıdaki iskelet **tasarım notudur, üretim yapılandırması
> değildir.** Repo'da nginx conf dosyası yoktur; iskelet havuz dokümanlarından
> (§7.1-§7.5) türetilmiştir. Üretim conf'i ancak ADR + DevOps onayıyla yazılır.

```nginx
# PLANNED — K011 server-runtime hedef iskelet (kaynak: havuz §7)
# Durum: ⚠️ VERIFICATION REQUIRED — üretim SAPI/sunucu bilinmiyor

upstream php_fpm {              # §7.1 — ngx_http_upstream_module
    server 127.0.0.1:9000;
    keepalive 32;
}

server {
    listen 443 ssl;             # §7.3 — ngx_http_ssl_module
    ssl_protocols TLSv1.2 TLSv1.3;
    add_header Strict-Transport-Security "max-age=31536000" always;

    log_format cm_json '{...}'; # §7.4 — ngx_http_log_module (JSON + traceId)
    access_log /var/log/nginx/cm.access.log cm_json;

    location ~* \.(env|log|sql|git|ini)$ { deny all; }   # .htaccess L18 parite
    location ~* \.(css|js|png|jpg|...)$   { expires 30d; } # .htaccess L34 parite
    location ~ ^/(config|include|pages)/  { return 403; }  # .htaccess L35 parite

    location / {                 # §7.5 — ngx_http_rewrite_module
        proxy_set_header X-Forwarded-Proto $scheme;  # index.php L52
        try_files $uri $uri/ /index.php?$query_string;
    }
}
```

### §12.1 İskelet ↔ Havuz İzi

| İskelet satırı | Havuz URL # |
|---|---|
| `upstream` bloğu | 1 |
| `proxy_set_header X-Forwarded-Proto` | 2 |
| `ssl_protocols` + HSTS | 3 |
| `log_format cm_json` | 4 |
| `try_files` / `return 403` | 5 |
| FPM port varsayımı | 6 (SAPI) — ⚠️ |

---

## §13 Risk Matrisi (Havuz Kullanımının Riskleri)

| # | Risk | Olasılık | Etki | Azaltma |
|---|---|---|---|---|
| 1 | PLANNED iskeletin üretim sanılması | Orta | Yüksek | UYARI kutusu (§12) + `PLANNED` etiketi |
| 2 | URL'de sürüm drift (nginx docs güncellenir) | Düşük | Orta | Yeniden 200 teyidi (§9 adım 2) |
| 3 | SAPI yanlış varsayımı (`php_flag` etkisiz) | Orta | Orta | `RuntimeBootstrap` L16 savunma derinliği + §7.6 |
| 4 | Kesişim ihlali (kardeş havuzdan URL kopyası) | Düşük | Düşük | §5 betiği + grep kontrolü C |
| 5 | dev.mysql.com'a geri dönme | Düşük | Yüksek (403) | §9 adım 3 kalıcı yasak |

---

## §14 Özet — Havuz Künyesi

| Alan | Değer |
|---|---|
| Havuz sahibi | `K011-application/server-runtime` |
| URL sayısı | 6 (5 nginx.org + 1 php.net) |
| Doğrulama | 6/6 HTTP 200 (önceden fetch) |
| Kardeş kesişim | 0 (iki yönlü grep) |
| Reddedilen (etiket) | 5 |
| PLANNED türev | nginx iskeleti (§12) |
| Yasak alan | dev.mysql.com |
| Güncelleme | Yeni URL ancak §9 sürecinden geçer |

---

## §15 URL → Etki Alanı Haritası (Hangi URL Kime Yazar)

| URL # | Etkilenen repo dosyası (okunan) | Etki alanı | Aksiyon (gelecek) |
|---|---|---|---|
| 1 | `api.coremusic.net/index.php` (L50-63) | Proxy header zinciri | conf'ta `proxy_set_header` teyidi |
| 2 | `api.coremusic.net/index.php` (L52) + `ResponseEmitter.php` (L70) | Başlık aktarımı/Vary | proxy buffer + header testi |
| 3 | `.htaccess` (L9-16) · `web.config` (L4-13) | TLS/HSTS parite açığı | HSTS kararı ADR'ye |
| 4 | `StructuredLogger.php` (L61-72) · sql L81-98 (`api_usage`) | Log hizası | JSON access log şablonu |
| 5 | `.htaccess` (L26-37) · `web.config` (L14-41) | Rewrite paritesi | nginx `try_files` eşlemesi |
| 6 | `.htaccess` (L5-7) · `RuntimeBootstrap.php` (L11-19) | SAPI/ini davranışı | SAPI kararı (FPM vs mod_php) |

> Her URL **en az bir** okunmuş repo dosyasına bağlanmıştır (§2); soyut
> "ilgili olabilir" bağlantısı yoktur.

---

## §16 Havuz-Sorgu İlişkisi (Mimari)

```text
[K011-application]
   ├── queries ────── havuz: 5 × php.net PDO (okuma katmanı)
   │                   kesişim ──✖──┐
   └── server-runtime  havuz: 5 × nginx + 1 × php.net installation
                        (sunucu/runtime katmanı)
```

| İlgi | Ayrım ilkesi |
|---|---|
| Ortak alan adı (php.net) | **Sayfa düzeyinde** ayrıştırma: PDO sayfaları queries'e, installation buraya → kesişim 0 |
| Ortak tema (runtime) | queries = DB runtime; burası = HTTP/sunucu runtime |
| Gelecek eklemeler | Her modül kendi havuzunu kendi dosyasında tutar; çapraz URL yasak |

---

## §17 Kapanış Beyanı

Bu dosya `server-runtime` web araştırma havuzunun tamamıdır: **6 URL** (hepsi
önceden fetch ile HTTP 200 doğrulanmış), her biri okunmuş repo kanıtına
bağlanmış; 5 reddedilen aday yalnız etiket taşır (tam URL yok); iki yönlü
grep ile kardeş `queries` havuzuyla kesişim **0** teyit edilmiştir; PLANNED
nginx iskeleti açıkça üretim dışı olarak işaretlidir; `dev.mysql.com`
kalıcı yasaktır. Havuz dışına URL çıkışı yoktur.

---

## §18 Teknik Notlar — İlgili Direktif Sözlüğü

| Direktif | Modül | Anlamı (kısa) |
|---|---|---|
| `keepalive` | upstream (#1) | Backend FPM/nginx arası bağlantı havuzu |
| `least_conn` | upstream (#1) | Yük dağıtım algoritması |
| `proxy_set_header` | proxy (#2) | Backend'e taşınan başlık |
| `proxy_buffering` | proxy (#2) | Yanıt buffer'ı (SSE/streaming'de off) |
| `proxy_read_timeout` | proxy (#2) | Backend yanıt zaman aşımı |
| `ssl_protocols` | ssl (#3) | Etkin TLS sürümleri |
| `ssl_session_cache` | ssl (#3) | TLS oturum yeniden kullanımı |
| `log_format` | log (#4) | Erişim logu şablonu (JSON destekli) |
| `$request_time` | log (#4) | İstek süresi ↔ `api_usage.response_time_ms` (sql L87) |
| `try_files` | rewrite (#5) | Dosya yoksa fallback |
| `return 403` | rewrite (#5) | Doğrudan 403 (Apache `[F]` karşılığı) |
| SAPI | php.net (#6) | PHP'nin sunucuyla arayüzü (mod_php/FPM/CGI/CLI) |

> Sözlük yalnız havuz konularını kapsar; repo kodu için `server-runtime-kapsam.md` §A.

---

## §19 Sonraki Turda Kapatılacaklar

| # | Açık | Kapanış koşulu |
|---|---|---|
| 1 | Üretim sunucusu/nginx durumu (⚠️) | Sunucu envanteri vault'a yazılınca |
| 2 | SAPI kararı (⚠️) | ADR yazılınca → §7.6 ve `.htaccess` L5-7 yeniden değerlendirilir |
| 3 | HSTS kararı | ADR → `.htaccess`/`web.config`/nginx ekleme |
| 4 | URL yeniden 200 teyidi | §9 adım 2 (tarih damgalı) |
| 5 | nginx conf varsa | Havuz konuları üretim conf'iyle eşleştirilir; PLANNED iskelet (§12) kaldırılır |

---

## §20 Denetim İzi

| Alan | Değer |
|---|---|
| Havuz sürümü | v1 (ilk üretim — 2026-10-15) |
| URL listesi | §1 (6 satır) |
| Doğrulama kaydı | §3 (6 satır) |
| Kesişim | §5 (0 — iki yönlü) |
| Karar kaydı | §11 (6 alındı / 5 red) |
| PLANNED türev | §12 (iskelet + uyarı) |
| Footer | Authority + Last Updated 2026-10-15 ✅ |

---

## §21 Soru-Cevap Genişletme

| # | Soru | Geniş cevap |
|---|---|---|
| Q-1 | nginx dokümanları üretim yokken neden bu kadar geniş? | Çünkü üç sunucu dosyası (`.htaccess` 37, `web.config` 43 satır) zaten **parite** taşır; nginx'e geçiş senaryosunda aynı üç kural tekrar yazılacaktır — havuz bu yazımı önceden besler. Ayrıca SSL/log konuları bu üç dosyada **yok** (§7.3 HSTS açığı, §7.4 log hizası); bu yüzden upstream/proxy/ssl/log da havuzdadır. |
| Q-2 | php.net installation neden PDO sayfalarından ayrı? | PDO sayfaları sorgu katmanının (queries) kanıtıdır; buradaki tek php.net URL'si **SAPI/çalışma zamanı** konusudur. Ayırma, iki modülün havuz kesişimini 0 tutar (§5). |
| Q-3 | Reddedilen adaylar neden tam URL değil? | Zero-hallucination: doğrulanmamış URL yazılmaz; etiket hem karar gerekçesini taşır hem URL doğrulama yükümlülüğü doğurmaz (§4). |
| Q-4 | Kesişim 0 nasıl garanti edilir? | Betik (§5) + iki yönlü grep (§5.1 A/B/C); yeni URL eklenince §9 adım 5 tekrarlanır. |
| Q-5 | HSTS neden açık? | `.htaccess` ve `web.config` tam okundu — `Strict-Transport-Security` 0 isabet; ssl_module (#3) bunu kaydeder. Karar ADR'ye aittir (§19.3). |

---

## §22 Havuz Tablosu — Kısa Sürüm (Tek Bakış)

| # | Alan adı | Konu (tek satır) | Repo bağı |
|---|---|---|---|
| 1 | nginx.org | upstream sunucu grubu + keepalive | `index.php` proxy header |
| 2 | nginx.org | proxy_set_header/buffering/timeout | `index.php` L52 |
| 3 | nginx.org | TLS sürümleri/cipher + HSTS | başlık parite açığı |
| 4 | nginx.org | JSON access log + süre metriği | `StructuredLogger` + `api_usage` |
| 5 | nginx.org | rewrite/return/try_files | `.htaccess`/`web.config` paritesi |
| 6 | php.net | SAPI + kurulum/çalışma zamanı | `php_flag` + `RuntimeBootstrap` |

**Toplam 6 · 200: 6 · Kesişim: 0 · dev.mysql.com: 0.**

---

## §23 Havuz Otomatik Kontrol Şablonu (Gelecek Eklemeler İçin)

| # | Şart | Uyulmazsa |
|---|---|---|
| 1 | URL fetch ile 200 | Havuza alınmaz |
| 2 | Konu `server-runtime` sınırlarında | Kardeş havuza yönlenir |
| 3 | Tam URL yalnız bu dosyada | Kesişim riski → §5 betiği FAIL |
| 4 | Reddedilen aday etiket olarak yazılır | Uydurma URL (Zero-Hallucination ihlali) |
| 5 | §3 kaydına satır eklenir | Doğrulama boşluğu |

> Bu şablon yeni bir K kademesi modülü havuzu açılırken de geçerlidir;
> `queries` modülü için aynı şablon kardeş dosyada uygulanmıştır.

### §23.1 Durum Özeti

| Alan | Değer |
|---|---|
| Bu dosyanın havuz satırı | 6 |
| Doğrulanmış | 6/6 |
| Kesişim | 0 |
| Footer | 2026-10-15 ✅ |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
