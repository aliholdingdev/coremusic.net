---
node_id: "K000-isletim-sistemi/isletim-sistemi-runtime"
node_type: "subdomain"
status: "draft"
parent_id: "K000-isletim-sistemi"
children:
  - "K000-isletim-sistemi/isletim-sistemi-runtime/surec-ve-parcacik"
  - "K000-isletim-sistemi/isletim-sistemi-runtime/web-runtime-topolojisi"
  - "K000-isletim-sistemi/isletim-sistemi-runtime/baslangic-ve-bootstrap"
depends_on:
  - "isletim-sistemi-kernel"
  - "isletim-sistemi-core"
evidence:
  - "httpd.apache.org/docs/2.4/mpm.html yanıt kodu 2026-10-13 (HTTP 200)"
  - "learn.microsoft.com/en-us/windows/win32/procthread/creating-processes yanıt kodu 2026-10-13 (HTTP 200)"
  - "php.net/manual/en/opcache.installation.php yanıt kodu 2026-10-13 (HTTP 200)"
  - "home.coremusic.net/.htaccess (37 satır)"
  - "home.coremusic.net/web.config (43 satır)"
  - "shared/src/Bootstrap/RuntimeBootstrap.php (21 satır)"
validation: "web: MPM · CreateProcess · OPcache kurulum resmi dokümanları HEAD ile 2026-10-13 doğrulandı (HTTP 200). disk: .htaccess / web.config / RuntimeBootstrap satır içerikleri Select-Object -Index ile okundu."
open_questions:
  - "R1: Hedef runtime topolojisi kesin değil — Apache (mod_php/FPM) mı, IIS mi, ikisi de mi? Repo'da ikisinin de config'i var (çift hedef). [UNKNOWN — Q4 ile ilişkili]"
  - "R2: Çok-iş-parçacıklı çalışma (pthreads) hedef mimaride var mı? Uygulama katmanında yok. [UNKNOWN]"
  - "R3: PHP Apache üzerinde mod_php mi, FPM mi çalışıyor? Repo kesin kanıt vermiyor. [⚠️ VERIFICATION REQUIRED]"
  - "R7: Apache MPM kapasitesi (event/worker/prefork) hangisi? web.config yalnız IIS tarafını tanımlar. [UNKNOWN]"
group: mimari
---

# isletim-sistemi-runtime

> **Kapsam (tek cümle):** CoreMusic'in çalıştığı **çalışma zamanı ortamı** — web sunucusu
> yapılandırması (Apache `.htaccess` + IIS `web.config` çift hedefi), PHP çalışma zamanı
> kurulumu (`RuntimeBootstrap`) ve OS süreç/iş-parçacığı modeli (pthreads /
> processes-and-threads) katmanları.

Bu alt-dal üç Kademe-3 alt-dala bölünür; kök içerik dosyaları sınır ve kanıt katmanını taşır.

## Kök İçerik Dosyaları (Kademe-2)

| Dosya | Grup | Ne içerir |
|---|---|---|
| [`isletim-sistemi-runtime-kapsam.md`](isletim-sistemi-runtime-kapsam.md) | mimari | Sunucu + PHP + OS süreç katmanları, çift hedef R1, kapsam dışı listesi |
| [`isletim-sistemi-runtime-ornekler.md`](isletim-sistemi-runtime-ornekler.md) | kod | Gerçek repo kodu: `.htaccess` (37), `web.config` (43), `RuntimeBootstrap` (21) |
| [`isletim-sistemi-runtime-dogrulama.md`](isletim-sistemi-runtime-dogrulama.md) | kanit | Web (≥5) + disk kanıtları, 2097 URL envanteri kesişimi |

## Alt-Dallar (Kademe-3)

| Alt-dal | Odak | İçerik dosyaları |
|---|---|---|
| [`surec-ve-parcacik/`](surec-ve-parcacik/index.md) | OS süreç/iş-parçacığı — fork/exec, pthread, sinyal, bellek | anayasa · surucu · ornekler |
| [`web-runtime-topolojisi/`](web-runtime-topolojisi/index.md) | Web sunucusu MPM/topoloji — Apache MPM, IIS app-pool, nginx fastcgi | anayasa · surucu · ornekler |
| [`baslangic-ve-bootstrap/`](baslangic-ve-bootstrap/index.md) | PHP boot sırası — autoloader, OPcache, ini, hata raporlama | anayasa · surucu · ornekler |

## Katmanlar

| Katman | Repo'da | OS düzeyi |
|---|---|---|
| Web sunucusu | ✅ `.htaccess` + `web.config` | Process/thread modeli |
| PHP runtime | ✅ `RuntimeBootstrap` | `pthreads(7)` / `CreateProcess` |
| OS süreç modeli | ❌ doğrudan yok | Aday hedef kaynaklar (alt-dal) |

## Açık Sorular

R1 çift hedef (Apache/IIS) · R2 çok-iş-parçacığı · R3 mod_php-vs-FPM · R7 MPM kapasitesi — dördü de [UNKNOWN] / [⚠️ VERIFICATION REQUIRED]; ayrıntı `isletim-sistemi-runtime-kapsam.md` ve `web-runtime-topolojisi/` altında.

## Komşu Sınır Matrisi

| Komşu | Paylaşılan sınır | Bu alt-dalın sahibi | Komşunun sahibi |
|---|---|---|---|
| `isletim-sistemi-core` | Süreç/parçacık yaşam döngüsü çağrısı | Runtime boot tetikleyicisi | `core` süreç/tablosu |
| `isletim-sistemi-plan` | Zamanlayıcı / kuyruk sırası | Runtime kuyruk boşaltma (drain) | `plan` zamanlayıcı |
| `isletim-sistemi-api` | HTTP giriş kapısı | Sunucu yapılandırması (rewrite) | `api` route/dispatch |
| `isletim-sistemi-security` | Güvenlik başlıkları / filtre | `.htaccess` 403 filtresi | `security` auth/CSRF |
| `isletim-sistemi-observability` | Log/ölçüm çıkışı | Sunucu erişim logu | `observability` metrik |
| `isletim-sistemi-testing` | Ortam sağlama | Runtime boot ortamı | `testing` test harness |
| `K011-application` | Uygulama boot bağımlılığı | `RuntimeBootstrap` | `K011` uygulama mantığı |
| `K018-devops` | Dağıtım/ortam hedefi | Sunucu hedefi R1 | `K018` deploy pipeline |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında
> **9. alan `group`**. Bu tablo Kademe-3 alt-dalları listeler.

| Grup | Alt-dal | Amaç | Kademe |
|---|---|---|---|
| mimari | `surec-ve-parcacik/` | OS süreç/iş-parçacığı sınırı ve kuralı | Kademe-3 |
| mimari | `web-runtime-topolojisi/` | Web sunucusu topoloji ve MPM sınırı | Kademe-3 |
| mimari | `baslangic-ve-bootstrap/` | PHP boot sırası ve OPcache/ini sınırı | Kademe-3 |

## Web Research

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| Apache MPM modülleri (event/worker/prefork) resmi dokümanda sunucu süreç modelini tanımlar; `.htaccess`/`web.config` bu modelin üstünde çalışır (R7 iddiası). | `https://httpd.apache.org/docs/2.4/mpm.html` | 2026-10-13 | Doğrulandı (HTTP 200, resmi doküman) |
| Microsoft CreateProcessW, Windows süreç oluşturma API'sidir; IIS app-pool(worker process) bu süreç modelinde çalışır (R1 IIS hedefi iddiası). | `https://learn.microsoft.com/en-us/windows/win32/procthread/creating-processes` | 2026-10-13 | Doğrulandı (HTTP 200, resmi doküman) |
| PHP OPcache kurulum dokümanı `opcache.enable` / JIT bayraklarını tanımlar; `RuntimeBootstrap`'in `ini_set` çağrılarının hedefi budur (R5 iddiası). | `https://www.php.net/manual/en/opcache.installation.php` | 2026-10-13 | Doğrulandı (HTTP 200, resmi doküman) |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
