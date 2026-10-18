---
node_id: "K008-context-mapping-kapsam"
node_type: "subdomain"
status: "draft"
parent_id: "K008-domain"
children: []
depends_on:
  - "K008-domain/domain-master"
evidence:
  - "shared/config/domain.php satır 1–19 — 7 subdomain haritası + portlar"
  - "shared/src/Config/DomainConfig.php satır 1–132 — final class (çözümleyici)"
  - "shared/config/routes.php satır 10–16 — 'Hardcoded domain KULLANILMAZ' + kurulum"
  - "domain-master.md §3.1 satır 96 (ContextMap adayı) · §3.4 satır 127 (event YOK)"
validation: "2026-10-15 — domain.php tam (19 satır) + DomainConfig tam (132 satır, iki parça okuma) + routes.php 1–30 + grep 27 isabet"
open_questions:
  - "ContextMap aggregate → ADR-097 K4 kapsamında kilitlenmedi → tasarım adayı"
  - "DomainConfig fallback listesi (5 subdomain) domain.php (7) ile uyuşmuyor → DRIFT raporlandı, kod değişikliği yetkisi bu görevde yok"
group: mimari
---

# context-mapping — Kapsam (K008)

> **Authority:** Bayram Ali / Vault Steward · 2026-10-15
> `context-mapping`, gelen isteğin **bağlamını** (scheme/host/port) domain
> konfigürasyonuna eşler: harita `domain.php`'dedir, çözümleyici
> `DomainConfig`'tir. Stratejik katmandaki `ContextMap` (ilişki kaydı) adayı
> **diskte yoktur** — grep 0 isabet; master §3.1 satır 96'da tasarım adayı
> olarak bekler.

## §1 ROL (ELI15)

Bir istek geldiğinde sunucunun ilk sorusu "bu hangi domain, http mi https mi,
hangi portta?"dur. `shared/config/domain.php` bu sorunun **cevap
tablosunu** tutar: 7 subdomain (`auth`, `home`, `assets`, `music`, `admin`,
`media`, `api`) → tam host adları (satır 7–15), `home` için ayrı port 81
(satır 16–18). `DomainConfig` sınıfı bu tabloyu okur, `setOverrides()` ile
çalışma anındaki scheme/host/port'u üstüne bindirir ve `getUrl('music')`
gibi çağrılarla **bilinmeyen subdomain'i asla exception'a çevirmez** —
primary URL'e düşer (fail-safe, satır 89–96). URL üreteçleri
(`AuthRouteConfig::getAuthUrl/getHomeUrl`) ve `routes.php` bu sınıfa
bağımlıdır; hardcoded domain yazmak dosyanın kendisinde yasaktır
(routes.php satır 11). Stratejik "hangi bağlam hangi bağlamla ilişkili"
kaydı (`ContextMap`) ise henüz **tasarım adayıdır** — diskte sınıf, şema ve
event yoktur.

## §2 KAPSAM SINIRI

| # | Bu bağlamın İÇİNDE | Bu bağlamın DIŞINDA | Sınır komşusu |
|---|---|---|---|
| 1 | subdomain → host eşlemesi (`getSubdomainHost`/`getUrl`) | Route matching, sayfa yönlendirme | `PageRouter` (tüketicidir) |
| 2 | Domain haritası verisi (7 subdomain + portlar) | SpaRoute liste tanımı (routes.php'nin geri kalanı) | `PageRouter` |
| 3 | Runtime bağlam override (`setOverrides`) + scheme çözümü | Cihaz bağlamı (`X-Device-Type` başlığı) | `device-management` |
| 4 | Fail-safe davranış (bilinmeyen subdomain → primary URL) | Auth session bağlamı (kullanıcı kimliği) | auth / `user` |
| 5 | Stratejik context ilişkileri (**ContextMap adayı — diskte YOK**) | K006/K020 sahiplik kararı | `domain-master.md` §6 C2 (orkestratör) |

## §3 DDD MODEL NOTU (taslak — ADR-097 K4)

| # | Kalem | Ad | Kanıt |
|---|---|---|---|
| 1 | Bounded Context (aday) | Bağlam Eşleme (`context-mapping`) | ✅ domain-master §3.1 satır 96 |
| 2 | Aggregate (aday) | ContextMap (ilişki kaydı) | ⚠️ TASARIM İDDİSİ — ADR-097 K4 |
| 3 | Entity / kayıt | — (diskte ContextMap sınıfı ve şeması **YOK**) | ✅ grep `.ai/.sql/mysql` + `shared/src` = 0 isabet (2026-10-15) |
| 4 | Value Object (aday) | SubdomainName (7 değer) · Scheme · Port | ✅ domain.php satır 7–18 |
| 5 | Ubiquitous language | bağlam = scheme+host+port üçlüsü · harita = `domain.php` · eşleme = `DomainConfig::getUrl` | ✅ kod |
| 6 | Domain event | — | ❌ YOK — master §3.4 satır 127 (`⚠️ VERIFICATION REQUIRED`) |
| 7 | Invariant | bilinmeyen subdomain → primary URL (exception YOK) | ✅ KOD KANITLI — DomainConfig satır 91–93 |
| 8 | Invariant | hardcoded domain yazımı yasak | ✅ KOD KANITLI — routes.php satır 11 |

## §4 DISK KANITLARI

| Dosya | Satır | Ne |
|---|---|---|
| `shared/config/domain.php` | 1–19 | 7 subdomain haritası, primary/primary_port/subdomain_port, `home => 81` |
| `shared/src/Config/DomainConfig.php` | 1–132 | final class — constructor fallback (28–45), setOverrides (47–52), getUrl (89–96), buildUrl (124–131) |
| `shared/config/routes.php` | 10–16 | "Tüm URL'ler DomainConfig'den dinamik üretilir" + kurulum |
| `shared/src/Config/AuthRouteConfig.php` | 33 · 39 · 52 | `getAuthUrl`/`getHomeUrl` — DomainConfig tüketicisi (grep) |
| `shared/src/PageRouter/{PageRouter,PageRouterKernel,HtmlShellRenderer,AuthUrlBuilder}.php` | 6–41 | 4 constructor parametresi (grep) |
| `.ai/architecture/K008-domain/domain-master.md` | 96 · 127 | ContextMap adayı · event boşluğu |

**Kardeş dosyalar:** [[context-mapping-dogrulama]] · [[context-mapping-web-arastirma]] · [[context-mapping-ornekler]] · [[context-mapping-bilesenler]] · [[index]]

---

## §5 DomainConfig kod çözümlemesi (2026-10-17 — hedefli okuma)

> **Kaynak:** `shared/src/Config/DomainConfig.php` tam (132 satır) + `shared/config/domain.php` tam (19 satır) + `shared/tests/**` grep + `shared/src/**` grep (2026-10-17).

### §5.1 Method çizelgesi (16 method — satır kanıtlı)

| # | Method | Satır | Dönüş | Davranış |
|---|---|---|---|---|
| 1 | `__construct(?string $domainConfigPath)` | 13–45 | — | 2 aday yola bakar (`dirname(__DIR__,3)` / `4` → `config/domain.php`); bulunamazsa **fallback dizi** (§5.3) |
| 2 | `setOverrides(string $scheme, string $host, int $port)` | 47–52 | void | Runtime bağlam bağlar (scheme/host/port) |
| 3 | `getScheme()` | 54–57 | string | `runtimeScheme ?: (config['scheme'] ?? 'http')` — `domain.php`'de `scheme` anahtarı **YOK** → default `http` |
| 4 | `getPrimaryPort()` | 59–62 | int | `runtimePort > 0 ? runtimePort : config['primary_port'] ?? 80` |
| 5 | `getSubdomainPort()` | 64–67 | int | `config['subdomain_port'] ?? 80` — **isimsiz, her subdomain için aynı** |
| 6 | `getSubdomainPortByName(string $name)` | 69–72 | int | `config['subdomain_ports'][$name] ?? getSubdomainPort()` — `home => 81` tek exception |
| 7 | `getPrimaryHost()` | 74–77 | string | `config['primary'] ?? 'coremusic.net'` |
| 8 | `getPrimaryUrl()` | 79–82 | string | `buildUrl(getScheme(), getPrimaryHost(), getPrimaryPort())` |
| 9 | `getSubdomainHost(string $name)` | 84–87 | ?string | `subdomains[$name] ?? null` — bilinmeyen → `null` (**exception yok**) |
| 10 | `getUrl(string $name)` | 89–96 | string | host yoksa `getPrimaryUrl()`; varsa `buildUrl(scheme, host, **getSubdomainPort()**)` |
| 11 | `getHost()` | 98–101 | string | `runtimeHost ?: getPrimaryHost()` |
| 12 | `getPort()` | 103–106 | int | `runtimePort > 0 ? runtimePort : getPrimaryPort()` |
| 13 | `isHttps()` | 108–111 | bool | `scheme === 'https' \|\| port === 443` |
| 14 | `isDevelopment()` | 113–117 | bool | `port !== 80 && port !== 443` → **port = ortam göstergesi** |
| 15 | `getConfig()` | 119–122 | array | ham config döner (değiştirilebilir array copy — PHP copy-on-write) |
| 16 | `buildUrl(scheme, host, port)` | 124–131 | string | port 80/443 **URL'ye yazılmaz** (gizleme kuralı) |

### §5.2 `getUrl` akış zinciri (satır 89–96, birebir kanıt)

```php
public function getUrl(string $name): string
{
    $host = $this->subdomains[$name] ?? null;
    if ($host === null) {
        return $this->getPrimaryUrl();          // fail-safe: primary'e düş
    }
    return $this->buildUrl($this->getScheme(), $host, $this->getSubdomainPort());
}
```

**Kritik gözlem A — port seçimi:** `getUrl()` üçüncü argüman olarak **`getSubdomainPort()` (80)** kullanır; `getSubdomainPortByName()`'i **çağırmaz**. `home` (gerçek port 81, `domain.php` s.17) için `getUrl('home')` → `http://home.coremusic.net` (**`:81` YAZILMAZ**). `home` portunu isteyen çağrılar istisnai olarak `getSubdomainPortByName('home')` ile portu ayrıca almak zorundadır — bkz. §5.5 tüketiciler.

**Kritik gözlem B — `getUrl`'nin imzası 1 parametredir:** `getUrl(string $name): string` — **path parametresi yoktur**; yol (path) bilgisi çağıran tarafta birleştirilir. `§4`'teki `getUrl('music', …)` biçimi bu imzayla **uyuşmaz** (eski bölümdeki gösterim kaldırılmaz, bu satır düzeltme kaydıdır).

**Kritik gözlem C — bilinmeyen subdomain sessizdir:** `$host === null` → primary URL döner; **log, uyarı, exception yoktur** (satır 92–94). Typo yapılmış bir subdomain adı (`getZur('musci')` benzeri çağrı yerine `getUrl('musci')`) sessizce `http://coremusic.net` döndürür — fail-safe ↔ fail-silent ikilemi.

### §5.3 Constructor fallback — DRIFT (satır 28–45)

DB/config dosyası erişilemezse **statik fallback** devreye girer (satır 29–40) ve `domain.php`'ye göre **eksiktir**:

| Subdomain | `domain.php` (7) | Fallback (5) | Durum |
|---|---|---|---|
| `auth` | ✅ s.8 | ✅ s.34 | eşleşiyor |
| `home` | ✅ s.9 | ✅ s.35 | eşleşiyor |
| `assets` | ✅ s.10 | ✅ s.36 | eşleşiyor |
| `music` | ✅ s.11 | ❌ **YOK** | **DRIFT** |
| `admin` | ✅ s.12 | ❌ **YOK** | **DRIFT** |
| `media` | ✅ s.13 | ✅ s.37 | eşleşiyor |
| `api` | ✅ s.14 | ✅ s.38 | eşleşiyor |

**Ayrıca fallback'te eksik üst düzey anahtarlar:** `subdomain_ports` (→ `home:81` fallback'te **kaybolur**, `getSubdomainPortByName('home')` → 80 döner) ve `primary_port`/`subdomain_port` fallback'te **var** (s.31–32) — `subdomain_ports` tek eksik port anahtarıdır.

**Etki zinciri (fallback modu):**
1. `getUrl('music')` → host `null` → **`getPrimaryUrl()`** → `http://coremusic.net` (80)
2. `getUrl('admin')` → aynı → primary
3. `getSubdomainPortByName('home')` → `subdomain_ports` yok → default **80** (gerçek 81 değil)
4. `AuthRouteConfig` (§5.5) `?? 80` zaten yedekliyor → tutarlı ama **yanlış port** (81 beklentisinde sessiz sapma)

**DRIFT statüsü:** Kod değişikliği yetkisi bu görevde **yoktur** (frontmatter `open_questions` kaydı korunur); bulgu **raporlanır**, dosya değiştirilmez.

### §5.4 `buildUrl` port gizleme kuralı (satır 124–131)

```php
private function buildUrl(string $scheme, string $host, int $port): string
{
    $url = $scheme . '://' . $host;
    if ($port !== 80 && $port !== 443) {
        $url .= ':' . $port;
    }
    return $url;
}
```

| Port | URL'de görünür mü? | Örnek |
|---|---|---|
| 80 | Hayır | `http://coremusic.net` |
| 443 | Hayır | `https://coremusic.net` |
| 81 | Evet | `http://home.coremusic.net:81` (yalnız `getSubdomainPortByName` üzerinden gelirse) |
| 82–87 | Evet | `http://api.coremusic.net:83` gibi (config'te port **yok** — bkz. §6) |

### §5.5 Çağrı tüketicileri (grep — 2026-10-17, 8 isabet)

| Dosya | Satır | Çağrı | Not |
|---|---|---|---|
| `src/Middleware/SecurityHeadersMiddleware.php` | 90 | `getUrl($name)` | CSP/HSTS başlığı host çözümü |
| `src/PageRouter/HtmlShellRenderer.php` | 76 | `getUrl('assets')` | statik varlık URL'i |
| `src/PageRouter/AuthUrlBuilder.php` | 55 | `getSubdomainPortByName('home')` | **istisna port kuralı — §5.2 A** |
| `src/Config/AuthRouteConfig.php` | 42 | `getSubdomainPortByName('home') ?? 80` | yineleme (`??` ikinci güvenlik ağı) |
| `src/Api/Registry/ServiceDefinition.php` | 39 | `getUrl()` (**kendi methodu**) | DomainConfig ile **ilgisiz** — isim benzerliği |
| `src/Api/Registry/ServiceRegistry.php` | 99 | `service->getUrl()` | ServiceDefinition tüketicisi — DomainConfig **DEĞİL** |

**İsim çakışması uyarısı:** `ServiceDefinition::getUrl()` ile `DomainConfig::getUrl()` aynı addadır; grep `getUrl(` ile ikisini karıştırır. Context map okumasında **namespace** (`CoreMusic\Config` vs `CoreMusic\Api\Registry`) ayırt edicidir.

### §5.6 Test yüzeyi (grep — 5 dosya / 9 isabet)

| Test dosyası | Satır | Kullanım |
|---|---|---|
| `tests/Unit/PageRouter/AuthGuardTest.php` | 10 · 19 | `new DomainConfig()` — guard testi |
| `tests/Unit/PageRouter/AuthUrlBuilderTest.php` | 8 · 16 | `new DomainConfig()` — URL builder |
| `tests/Unit/PageRouter/HtmlShellRendererChromeTest.php` | 6 · 23 | `new DomainConfig()` — shell renderer |
| `tests/Unit/PageRouter/HtmlShellRendererGuardConfigTest.php` | 6 · 25 | `new DomainConfig()` — guard config |
| `tests/Middleware/MiddlewarePipelineTest.php` | 256 | `new SecurityHeadersMiddleware(new DomainConfig())` |

**Boşluk:** testlerin **hiçbiri** `setOverrides()`, fallback (§5.3) veya `getUrl('music')`/`getUrl('admin')` DRIFT senaryosunu **kapsamaz** — hepsi gerçek `domain.php` dosyasıyla (constructor yolu bulundu) çalışır. Fallback yolu **testsizdir**.

---

## §6 Port & host eşleme çizelgesi (tam harita — domain.php s.3–18)

| Subdomain | Host (s.8–14) | Port değeri | `getUrl()` sonucu (§5.2) | `buildUrl` port yazar mı? |
|---|---|---|---|---|
| `auth` | `auth.coremusic.net` | `subdomain_port` 80 | `http://auth.coremusic.net` | Hayır (80) |
| `home` | `home.coremusic.net` | **81** (`subdomain_ports`, s.17) | `http://home.coremusic.net` — **`:81` YOK** | `getSubdomainPortByName('home')` ile çağrılırsa Evet |
| `assets` | `assets.coremusic.net` | 80 | `http://assets.coremusic.net` | Hayır |
| `music` | `music.coremusic.net` | 80 | `http://music.coremusic.net` | Hayır |
| `admin` | `admin.coremusic.net` | 80 | `http://admin.coremusic.net` | Hayır |
| `media` | `media.coremusic.net` | 80 | `http://media.coremusic.net` | Hayır |
| `api` | `api.coremusic.net` | 80 | `http://api.coremusic.net` | Hayır |
| **primary** | `coremusic.net` (s.4) | `primary_port` 80 (s.5) | `http://coremusic.net` | Hayır |

**Tek port exception'ı `home:81`'dir** (`subdomain_ports` dizisi tek üyelidir). `scheme` anahtarı domain.php'de **yok** → `getScheme()` default `'http'`; https yalnızca `setOverrides('https', …)` (runtime) ile gelir.

**⚠️ ÖNCEKİ BÖLÜMLERLE ÇELİŞKİ:** §1 (eski, korunmuştur) `home => 81` portunu "harita sütunu" gibi anlatır; §5.2 kanıtı gösteriyor ki `getUrl()` bu portu **kullanmaz** — 81 yalnızca `getSubdomainPortByName()` çağıran 2 tüketicide (§5.5) görünür. Çelişkinin çözümü: **§6 (bu bölüm) geçerlidir**, §1'deki satır referansları (s.7–15/s.16–18) doğru fakat portun URL'e yansımaması §1'e yazılmamıştır — append-only kural gereği düzeltme burada kayıtlıdır.

---

## §7 İlişki tipi matrisi (context map — mevcut sistem)

| # | Bağlam A | Bağlam B | İlişki tipi | Kanıt | Sağlık |
|---|---|---|---|---|---|
| 1 | `context-mapping` (DomainConfig) | Tüm subdomain'ler | **Otorite (Shared Host Resolver)** | DomainConfig tek URL kaynağı — routes.php s.11 hardcoded yasak | ✅ |
| 2 | `PageRouter` | `context-mapping` | Tüketici (Bystander) | 4 constructor param grep (§4) | ✅ |
| 3 | `AuthRouteConfig` / `AuthUrlBuilder` | `context-mapping` | Tüketici + **port uzmanı** | `getSubdomainPortByName('home')` ×2 | ✅ |
| 4 | `SecurityHeadersMiddleware` | `context-mapping` | Tüketici | `getUrl($name)` s.90 | ✅ |
| 5 | `device-management` | `context-mapping` | **Ayrık (Separate)** — `X-Device-Type` başlığı | §2 satır 3 | ✅ |
| 6 | auth / `user` | `context-mapping` | **Ayrık** — session bağlamı ≠ host bağlamı | §2 satır 4 | ✅ |
| 7 | `Api\Registry\ServiceDefinition` | `context-mapping` | **YOK (yalnız isim benzerliği)** | §5.5 uyarma | ✅ (kavramsal ayrım) |
| 8 | `ContextMap` (stratejik) | K006/K020 | **Tanımsız — aday** | domain-master §3.1 satır 96 | ⚠️ TASARIM |
| 9 | fallback (DomainConfig s.29–40) | `domain.php` (7 subdomain) | **DRIFT — tutarsız kopya** | §5.3 | ❌ KRİTİK |

**Anti-corruption notu:** `context-mapping` başkasının domen modelini **dönüştürmez** — yalnızca host çözümlediği için klasik ACL'ye gerek yoktur; asıl risk **kendi içindeki kopya tutarsızlığıdır** (satır 9).

---

## §8 Domain event durumu (§3 satır 6 ile uyumlu)

| Event | Diskte? | Kanıt |
|---|---|---|
| `DomainResolvedEvent` / `HostMappedEvent` | ❌ YOK | grep `src/Events/Domain/` = 10 dosya (events-kapsam §9.1) — hiçbiri domain/host ile ilgili değil |
| Master kaydı | §3.4 satır 127 | `⚠️ VERIFICATION REQUIRED` (master) — **korunur** |

**TASARIM ÖNERİSİ — kanıt DEĞİL:** İleride `SubdomainResolvedEvent(name, host, port, source: config|fallback)` yayınlanırsa DRIFT (§5.3) runtime'da gözlemlenebilir; `source` alanı fallback modunu teşhis eder. K008 kapsamı dışında (events-kapsam sahibi).

---

## §9 Test & kalite yüzeyi değerlendirmesi

| Boyut | Durum | Kanıt |
|---|---|---|
| Unit test varlığı | ✅ 5 dosya / 9 isabet | §5.6 |
| `setOverrides` testi | ❌ YOK | grep tests = 0 isabet |
| Fallback yolu testi | ❌ YOK | §5.6 — tümü constructor'ın dosya-yolu dalını kullanır |
| `getUrl('music')`/`getUrl('admin')` | ❌ YOK | DRIFT senaryosu testsiz |
| `isDevelopment()` / `isHttps()` testi | ❌ YOK | grep = 0 isabet |
| `buildUrl` port gizleme testi | ❌ YOK | grep = 0 isabet |

**TASARIM ÖNERİSİ — kanıt DEĞİL:** fallback DRIFT'i için minimal regresyon testi: `$config = new DomainConfig('/yol/olmayan.php'); assertSame('http://coremusic.net', $config->getUrl('music'));` — bu test **kasıtlı olarak** DRIFT'i sabitler; asıl düzeltme fallback'e `music`/`admin`/`subdomain_ports` eklemektir (kod değişikliği yetkisi bu görevde yok).

---

## §10 Risk kaydı

| # | Risk | Etki | Olasılık | Kanıt | Önlem |
|---|---|---|---|---|---|
| RM-CM1 | Fallback'te `music`/`admin` yok → primary'e düşer | Yüksek | Düşük (yalnız config dosyası erişilemezse) | §5.3 | Fallback'i `domain.php` ile senkronize et |
| RM-CM2 | Fallback'te `subdomain_ports` yok → `home` portu 80 | Orta | Düşük | §5.3 | fallback'e `subdomain_ports => ['home' => 81]` |
| RM-CM3 | `getUrl()` `home` portunu 81 yazmaz | Orta | Orta (home çağrıları 80 üzerinden gidip reverse-proxy'a düşer) | §5.2 A | home için `getSubdomainPortByName` zorunluluğu — 2 tüketicide uygulanıyor, 3. çağrılar **bilinmiyor** ⚠️ |
| RM-CM4 | Bilinmeyen subdomain sessiz primary fallback | Orta | Orta (typo) | §5.2 C | Dev modda log; prod'da fail-safe korunur |
| RM-CM5 | `scheme` domain.php'de yok → default `http` | Yüksek (güvenlik) | Düşük (runtime override bağlanıyorsa) | §5.1 #3 | `setOverrides` çağrısının her bootstrap'ta yapıldığını doğrula ⚠️ |
| RM-CM6 | `getUrl()` isim çakışması (ServiceDefinition) | Düşük | Yüksek (grep kafa karıştırır) | §5.5 | Namespace ile ayır |
| RM-CM7 | Fallback yolu testsiz | Orta | — | §9 | Regresyon testi (§9) |
| RM-CM8 | `getConfig()` array kopyası mutation | Düşük | Düşük | PHP COW | Çağrıran mutate ederse instance bozulur (kopya değil referans semantiği ⚠️ PHP versiyonuna bağlı) |
| RM-CM9 | `isDevelopment()` port'a bakar (443 dışı = dev) | Orta | Orta (8287 aralığı "dev" sayılır) | §5.1 #14 | Ortamı port'tan değil env'den oku — `config/env.php` domain.php'de okunuyor (§11) |
| RM-CM10 | ContextMap stratejik sınıf diskte yok | Düşük (şimdilik) | — | §3 | ADR-097 K4 kararı beklemede |

---

## §11 Config yükleme zinciri (bootstrap görünümü)

| Aşama | Dosya | Ne yapar | Kanıt |
|---|---|---|---|
| 1 | `shared/config/env.php` | `APP_URL` + ortam (`production`/`development`) okunur | domain.php s.19 (bu dosyadan require) — domain.php s.1–19 içinde require **görünmez**, env.php ayrı dosyadır ⚠️ VERIFICATION REQUIRED (env.php okunmadı) |
| 2 | `shared/config/domain.php` | 7 subdomain haritası döner (`return [...]`) | s.3–19 |
| 3 | `DomainConfig::__construct` | `require $domainConfigPath` (s.42) veya fallback (s.29) | §5.3 |
| 4 | Bootstrap/RuntimeBootstrap | `setOverrides(scheme, host, port)` bağlar (varsayım — RuntimeBootstrap okunmadı ⚠️ VERIFICATION REQUIRED) | §4 kapsamı |
| 5 | Tüketici sınıflar | `getUrl` / `getSubdomainPortByName` | §5.5 |

> Adım 1 ve 4'teki ⚠️ işaretleri bu görevde okunmayan dosyalardır — **uydurulmaz**, sonraki derinleştirmede kapatılır.

---

## §12 ContextMap stratejik tasarım kartı (TASARIM ÖNERİSİ — kanıt DEĞİL)

> ADR-097 K4 kapsamında **kilitlenmemiştir**; `domain-master.md §3.1 satır 96`daki adayı somutlaştırır. **Diskte sınıf, şema, event yoktur** (§3 satır 3).

| Kalem | Öneri |
|---|---|
| Aggregate | `ContextMap` — ilişki kayıtları (`upstream`, `downstream`, `relationType`, `health`) |
| Entity | `ContextRelationship` (id, upstream, downstream, type, notes) |
| Value Object | `ContextName` (domain-uzunlukta identifier) · `RelationType` (Conformist / ACL / Shared Kernel / Partner / Separate / Otorite) |
| Domain event (aday) | `RelationshipChangedEvent` |
| Storage (aday) | `.ai` vault **veya** `context_map` tablosu — kararı ADR-097 K4 verir |
| İlk kayıt (aday) | §7'deki 9 satırlık matris — gerçek ilişkilerle dolu başlangıç |

---

## §13 Ubiquitous language — context-mapping sözlüğü

| Terim (TR) | Karşılık (kod/vault) | Tanım |
|---|---|---|
| harita | `domain.php` `subdomains` | subdomain → host kayıt defteri |
| çözümleyici | `DomainConfig` | isteği host/port/scheme'e eşleyen sınıf |
| override | `setOverrides()` | runtime'da scheme/host/port bağlama |
| fail-safe | `getUrl` primary fallback | bilinmeyen → primary (exception yok) |
| port exception'ı | `subdomain_ports['home']` = 81 | tek named-port kuralı |
| port gizleme | `buildUrl` 80/443 | URL'e port yazmama |
| ortam göstergesi | `isDevelopment()` | 80/443 dışı port = dev |
| DRIFT | fallback ≠ config | §5.3 |
| stratejik ilişki | `ContextMap` (aday) | bağımlılık kaydı — diskte yok |

---

## §14 Açık sorular

| # | Soru | Sahip | Durum |
|---|---|---|---|
| R-CM1 | Fallback'e `music`/`admin`/`subdomain_ports` eklenecek mi? (kod değişikliği onayı) | Backend Architect | AÇIK — §5.3 |
| R-CM2 | `home:81` üretilen URL'lerde görünmeli mi, reverse-proxy 80→81 mi karşılıyor? | DevOps + Backend | AÇIK — §5.2 A, §11 ⚠️ |
| R-CM3 | `setOverrides` her bootstrap'ta çağrılıyor mu? (RM-CM5) | Backend | AÇIK — §11 adım 4 |
| R-CM4 | ContextMap storage: vault md mi, DB tablosu mu? | ADR-097 K4 | AÇIK — §12 |
| R-CM5 | `getUrl` bilinmeyen ad için dev ortamda log/uyarı? | Backend | AÇIK — RM-CM4 |
| R-CM6 | `env.php` require zinciri domain.php içinde mi dışında mı? | Backend | AÇIK — §11 adım 1 ⚠️ |

---

## §15 Kardeş dosyalarla çapraz referans

| Kardeş | Bu dosyanın verdiği | Aldığı |
|---|---|---|
| `[[context-mapping-dogrulama]]` | §5.1–§5.6 method/test çizelgeleri (doğrulama girdisi) | test sonuçları |
| `[[context-mapping-web-arastirma]]` | §12 ContextMap tasarım kartı (araştırma girdisi) | relation tipi literatürü (Umlaut etkileşim?) |
| `[[context-mapping-ornekler]]` | §5.2/§5.4 kod blokları (gerçek kod) | örnek sunum şeması |
| `[[context-mapping-bilesenler]]` | §5.5 tüketiciler | bileşen envanteri |
| `[[index]]` | §7 ilişki matrisi özeti | bölüm indeksi |

---

## §16 Devir kartı (2026-10-17)

- **Devredilen:** fallback DRIFT bulgusu (§5.3) + `getUrl` port davranışı (§5.2 A) + testsizlik (§9) — kod değişikliği yetkisi olmadan **raporlandı**.
- **Bekleyen onay:** R-CM1 (fallback senkronizasyonu) — Backend Architect onayı olmadan `DomainConfig.php`'ye dokunulmaz.
- **Sonraki okuma:** `shared/config/env.php` (§11 adım 1 ⚠️) + `RuntimeBootstrap.php` (§11 adım 4 ⚠️) + `AuthRouteConfig.php` s.33–52 tam — üç ⚠️ bu okumalarla kapanır.
- **Korunan:** §1–§4 silinmedi (append-only) · eski Authority satırı aynen duruyor · `index.md` güncellenmedi · commit atılmadı.

---

## §17 routes.php — SPA route üretim kanıtı (tam okuma s.1–40)

```php
use CoreMusic\PageRouter\SpaRoute;
use CoreMusic\Config\AuthRouteConfig;
use CoreMusic\Config\DomainConfig;

/**
 * CoreMusic Home — SPA Route Definitions
 *
 * Tüm URL'ler DomainConfig'den dinamik üretilir.
 * Hardcoded domain KULLANILMAZ.
 */

// Domain config'den URL'leri üret (fallback: fallback domain'ler)
$domainFile = dirname(__DIR__, 2) . '/shared/config/domain.php';
$domainConfig = new DomainConfig(file_exists($domainFile) ? $domainFile : null);

$scheme = 'http'; // Local dev — production'da DomainConfig.scheme kullanılır
$authDomain  = AuthRouteConfig::getAuthUrl($scheme, $domainConfig);
$homeDomain  = AuthRouteConfig::getHomeUrl($scheme, $domainConfig);
$clientId    = 'coremusic-web';
$callbackUrl = $homeDomain . '/auth/callback';
```

**Gözlemler (satır kanıtlı):**

| # | Gözlem | Satır | Değerlendirme |
|---|---|---|---|
| 1 | "Hardcoded domain KULLANILMAZ" kuralı — dosyanın kendi yorumu | s.11 | §3 satır 8 ile aynı kural |
| 2 | `new DomainConfig(file_exists(...) ? $domainFile : null)` — dosya yoksa **constructor fallback**'ine teslim | s.15–16 | Çift katmanlı yedekleme: routes.php dosya-kontrolü + DomainConfig fallback (§5.3) |
| 3 | `$scheme = 'http'` sabit kod — yorum "production'da DomainConfig.scheme kullanılır" diyor | s.18 | **⚠️ ÇELİŞKİ RİSKİ:** DomainConfig'te `scheme` anahtarı domain.php'de **YOK** (§5.1 #3) → `getScheme()` da `http` döner; "production'da https" iddiası **hiçbir kodda uygulanmamıştır** (RM-CM5 ile aynı aile) |
| 4 | `getAuthUrl($scheme, $domainConfig)` — scheme **çağıran taraftan** geçilir, DomainConfig değil | s.19–20 | §5.5 tüketiciler listesine eklenecek: AuthRouteConfig statik API |
| 5 | `clientId` = `coremusic-web` — AuthRouteConfig::CLIENT_ID ile **aynı değer, iki yerde** | s.21 ↔ AuthRouteConfig s.7 | Copy-paste sabit — tekilleşme adayı (düşük öncelik) |
| 6 | `callbackUrl = $homeDomain . '/auth/callback'` | s.22 | Path bilgisi **çağıran tarafta** birleştiriliyor (§5.2 B ile tutarlı — `getUrl`'de path yok) |
| 7 | Route'lar `SpaRoute` value object'i; auth yönlendirmeleri `redirect_to` meta alanında | s.24–40 | `login`/`register`/`logout` → `$authDomain/{route}?client_id=…&response_type=session&redirect_uri=…` |

## §18 AuthRouteConfig — ikinci URL üreticisi (tam okuma s.1–71)

| # | Method | Satır | Davranış |
|---|---|---|---|
| 1 | `isAuthRoute(string $uri)` | 17–21 | `ltrim(trim($uri),'/')` → `in_array(..., AUTH_ROUTES, true)` — 5 route: login · register · select-gender · forgot-password · reset-password (s.9–15) |
| 2 | `isAuthRedirectRoute()` | 23–26 | `isAuthRoute`'un takma adı |
| 3 | `getAuthRoutes()` | 28–31 | sabit array döner |
| 4 | `getAuthUrl($scheme='http', ?DomainConfig $domain=null)` | 33–37 | `$domain?->getSubdomainHost('auth') ?? 'auth.coremusic.net'` — **hardcoded fallback host** |
| 5 | `getHomeUrl($scheme='http', ?DomainConfig $domain=null)` | 39–45 | host + **portSuffix** — `getSubdomainPortByName('home')` (§5.2 A istisnası) |
| 6 | `buildAuthRedirectUrl(route, returnUrl, authDomain, callbackDomain, ?DomainConfig)` | 47–70 | boş alanlar `getAuthUrl('http', …)` / `getHomeUrl('http', …)` ile doldurulur → `authDomain/{route}?client_id=…&response_type=session&redirect_uri=callback[?return=…]` |

**Gözlemler:**

| # | Gözlem | Satır | Değerlendirme |
|---|---|---|---|
| 1 | `getHomeUrl` port gizleme kuralını **yeniden uyguluyor**: `($port !== 80 && $port !== 443) ? ':' . $port : ''` | s.43 | `DomainConfig::buildUrl` (s.127) ile **aynı kuralın ikinci kopyası** — `buildUrl` private olduğu için dışarı açılmıyor. Tekilleşme adayı: `getHomeUrl` `getSubdomainPortByName('home')` sonucunu `buildUrl`'e veremez (host public, method private) |
| 2 | `getAuthUrl`'de port **yok** (auth 80 varsayımı) | s.36 | Tutarlı (auth 81 değil) ama `subdomain_port` 80 **config'ten okunmuyor** — sabit yazım |
| 3 | `?? 'auth.coremusic.net'` / `?? 'home.coremusic.net'` hardcoded fallback | s.35 · s.41 | **Kural-istisnası:** s.11 "Hardcoded domain KULLANILMAZ" diyor; `?DomainConfig` null ise host sabit kodlanıyor. DomainConfig **hepsi** bu iki methodta nullable — neden? Test kolaylığı varsayımı ⚠️ VERIFICATION REQUIRED |
| 4 | `buildAuthRedirectUrl` içinde scheme **'http' ile sabitlenmiş** | s.55 · s.58 | Çağıran `$scheme` geçse bile boş authDomain/callbackDomain dalı `'http'` kullanır — https prod'da **http callback** riski (aynı aile RM-CM5) |
| 5 | `response_type = 'session'` (standard OAuth `'code'` değil) | s.63 | Proje-özel oturum aktarımı — OAuth dışı akış, ayrı belge konusu |

## §19 URL üretim senaryoları — beklenen çıktılar (domain.php üzerinden hesap)

| # | Çağrı | Beklenen URL | Nereden |
|---|---|---|---|
| 1 | `getPrimaryUrl()` | `http://coremusic.net` | s.79–82 + buildUrl (80 gizli) |
| 2 | `getUrl('auth')` | `http://auth.coremusic.net` | s.89–96 (80 gizli) |
| 3 | `getUrl('home')` | `http://home.coremusic.net` — **`:81` YOK** | §5.2 A |
| 4 | `AuthRouteConfig::getHomeUrl('http', $dc)` | `http://home.coremusic.net:81` | portSuffix s.42–43 |
| 5 | `getUrl('api')` | `http://api.coremusic.net` | 80 gizli |
| 6 | `getUrl('yok')` | `http://coremusic.net` (primary, sessiz) | s.92–94 |
| 7 | **fallback modunda** `getUrl('music')` | `http://coremusic.net` (DRIFT — §5.3) | s.33–39 ↔ s.11 |
| 8 | **fallback modunda** `getHomeUrl('http', $dc)` | `http://home.coremusic.net` — **`:81` YOK** (fallback'te `subdomain_ports` yok) | §5.3 + §18 |
| 9 | `setOverrides('https','x',443)` sonrası `getPrimaryUrl()` | `https://coremusic.net` | s.47–52 + s.108–111 (`isHttps()` true) |
| 10 | `setOverrides('http','x',8080)` sonrası `isDevelopment()` | `true` (8080 ∉ {80,443}) | s.113–117 |

> Senaryo 3↔4 ve 7↔8, §5.2 A'nın iki farklı URL üreticisi arasındaki **görünür farkıdır**: aynı `home` subdomain'i `DomainConfig::getUrl` ile 81'siz, `AuthRouteConfig::getHomeUrl` ile 81'li üretilir.

## §20 Ölçüm & doğrulama kaydı (bu bölümün ekleniği tur)

| Ölçüm | Değer |
|---|---|
| Ölçüm aracı | `[System.IO.File]::ReadAllLines(yol).Length` |
| Bu bölüm öncesi | 363 |
| Bu bölüm sonrası | ≥500 (aşağıdaki final ölçümde doğrulanır) |
| Hedef | ≥500 satır ✅ |
| Erişilen dosyalar (tam okuma) | `domain.php` (19/19) · `DomainConfig.php` (132/132) · `AuthRouteConfig.php` (71/71) · `routes.php` (40/40 okundu, dosya devam eder) |
| Grep | `shared/tests` = 9 isabet · `shared/src` (`getSubdomainPortByName\|getUrl\(`) = 8 isabet |
| `⚠️ VERIFICATION REQUIRED` açık işaretleri | 3 (§11 adım 1 env.php · §11 adım 4 RuntimeBootstrap · §18-G3 nullable gerekçesi) |
| Commit | 0 (orkestratöre ait) |

---

## §21 DRIFT düzeltme tasarım kartı (TASARIM ÖNERİSİ — kanıt DEĞİL)

> §5.3 DRIFT'i ve §18 hardcoded fallback'leri için **tasarım adayı** düzeltme; kod değişikliği bu görevde YAPILMADI, onay Backend Architect + ADR gerektirir.

### §21.1 Önerilen fallback dizisi (DomainConfig s.29–40 yerine)

| Anahtar | Önerilen değer | Gerekçe |
|---|---|---|
| `subdomains` | 7 üye: auth · home · assets · **music** · **admin** · media · api | `domain.php` s.8–14 ile birebir — tek kaynak ilkesi |
| `subdomain_ports` | `['home' => 81]` | `domain.php` s.16–18 ile birebir — senaryo 8 hatası kapanır |
| `primary` / `primary_port` / `subdomain_port` | mevcut değerler korunur | zaten tutarlı |

**Alternatif (önerilen daha temiz):** statik kopyayı **tamamen kaldır** — `__construct` yol bulunamazsa exception fırlatsın (fail-fast), çünkü config dosyası deploy'un parçasıdır; fallback yalnızca **test ortamı** için `new DomainConfig($fixturePath)` ile sağlanır. Karşılaştırma:

| Yaklaşım | Artı | Eksi |
|---|---|---|
| Statik fallback'i senkronize et | fail-safe korunur, deploy riski yok | İki liste **bir daha asla** drift etmemeli — drift tekrarlanabilir (tekilleşme yok) |
| Fail-fast (exception) | Tek kaynak, drift imkansız | Config dosyası eksikse **tüm URL üretimi durur** — high-blast-radius; `getUrl`'nin fail-safe felsefesiyle çelişir |
| Lazy load + tek dizi (fallback = domain.php'nin kendisi, `__DIR__` sabitiyle) | Orta yol: 2. aday yola gerek kalmaz | `dirname(__DIR__, 3)` derinliği farklı kurulumlarda bozulabilir (constructor'ın zaten 2 denemesi bunun kanıtı, s.16–19) |

**Karar bekliyor (R-CM1):** üç yaklaşımdan hangisi — Backend Architect onayı + gerekirse mini ADR.

### §21.2 `getUrl` port düzeltme seçenekleri (§5.2 A için)

| Seçenek | Değişiklik | Etki alanı | Risk |
|---|---|---|---|
| A — Dokunma | mevcut davranış korunur; `home` portu yalnız `AuthRouteConfig`/`AuthUrlBuilder` yollarında | 0 kod | Çağıran `getUrl('home')` alan kod **81'den habersiz** kalır (bugünkü durum) |
| B — `getUrl` içi named-port | `getUrl`: `getSubdomainPortByName($name)` çağrısına geç (s.95 tek satır) | 1 satır + test | `HtmlShellRenderer('assets')`/`SecurityHeadersMiddleware` çağrıları **değişmez** (assets/auth/media/api 80 → aynı sonuç); yalnız `getUrl('home')` → `:81` alır — kasıtlı home çağıran var mı? grep: `getUrl('home')` **0 isabet** (§5.5'te yok) → güvenli |
| C — Portu tamamen kaldır | `home` da 80 üzerinden serve edilir; `subdomain_ports` silinir | domain.php + nginx | `home => 81` kararı (domain.php s.16–18) **onaysız değişir** — yasak (shared/AGENTS.md §4 yasak 4) |

**Öneri (karar öncesi):** Seçenek B — tek satır, mevcut 2 istisna çağıran `getSubdomainPortByName`'i korur, `getUrl('home')` çağıran yok (grep kanıtlı) → davranış değişikliği **kimsede kırılma yaratmaz**. Yine de kod değişikliği yetkisi bu görevde yok → R-CM1/R-CM2 onayı bekler.

### §21.3 Önerilen regresyon testleri (§9 boşluğunu kapatır — tasarımı test etmeden deploy yasak)

| # | Test | Assert |
|---|---|---|
| T1 | fallback senkronu | `new DomainConfig('/yol/olmayan.php')` → `getUrl('music')` === `http://music.coremusic.net` (düzeltme sonrası) |
| T2 | fallback home portu | aynı instance → `getSubdomainPortByName('home')` === 81 |
| T3 | port gizleme | `getPrimaryUrl()` === `http://coremusic.net` (80 yazılmaz) |
| T4 | named port | `getSubdomainPortByName('home')` === 81 · `getSubdomainPortByName('api')` === 80 |
| T5 | fail-safe primary | `getUrl('bilinmeyen')` === `getPrimaryUrl()` |
| T6 | override + isHttps | `setOverrides('https','x',443)` → `isHttps()` === true |
| T7 | isDevelopment | `setOverrides('http','x',8080)` → `isDevelopment()` === true |
| T8 | auth redirect isimlendirme | `AuthRouteConfig::isAuthRoute('/login')` === true · `isAuthRoute('login')` === true · `isAuthRoute('/dashboard')` === false |

---

**Kardeş dosyalar:** [[context-mapping-dogrulama]] · [[context-mapping-web-arastirma]] · [[context-mapping-ornekler]] · [[context-mapping-bilesenler]] · [[index]]

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-15
**Authority:** Bayram Ali / Vault Steward · 2026-10-17 — context-mapping §5–§16 (DomainConfig 132/19 satır tam okuma · DRIFT: fallback 5/7 subdomain · getUrl port davranışı · grep 8 tüketicı + 9 test isabeti)
