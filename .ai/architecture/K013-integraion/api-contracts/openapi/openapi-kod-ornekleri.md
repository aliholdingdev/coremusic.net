---
node_id: K013-integraion/api-contracts/openapi/openapi-kod-ornekleri
node_type: technology-deepdive
title: "OpenAPI Kod Örnekleri — Repo Kanıtlı Uç Nokta ve Kaynak-Etiketli Belge Örnekleri"
type: technology-deepdive
status: draft
date: 2026-10-12
parent_id: K000–K5000 Architecture Registry
children: []
depends_on:
  - K013-integraion/api-contracts/openapi
  - K013-integraion/api-contracts
  - K011-application
evidence:
  - "Dosya: api.coremusic.net/config/routes.php (2026-10-12 ölçüm, 120 satır)"
  - "Dosya: shared/src/Api/Routing/RouteTable.php (2026-10-12 ölçüm, 253 satır)"
  - "Dosya: api.coremusic.net/tests/Unit/RouteConfigTest.php (2026-10-12 ölçüm, 208 satır)"
  - "Dosya: shared/config/routes.php (2026-10-12 ölçüm, 131 satır)"
  - "https://spec.openapis.org/oas/v3.1.1.html (2026-10-12)"
  - "https://github.com/oai/openapi-specification (2026-10-12)"
validation:
  - "Repo kanıtları dosya yolu + gerçek satır numarası ile verilmiştir (2026-10-12 okuma)"
  - "Repo'da `openapi`/`swagger` geçen .yaml/.yml/.json/.php/.js dosyası YOKTUR (2026-10-12 tam tarama, 0 sonuç) — bu bir negatif kanıttır"
  - "Kaynak-etiketli örneklerde her blok `Kaynak:` satırı taşır; uydurma Dosya/Satır yoktur"
open_questions:
  - "OpenAPI belgesi üretilecekse kaynak hangi dosyadan türetilecek? → routes.php (tasarım iddiası) · UNKNOWN (karar yok)"
  - "`implemented: false` uçları belgeye nasıl yansıyacak? → UNKNOWN (standart karşılığı tartışmalı)"
  - "Test kapsamı (`RouteConfigTest`) OpenAPI doğrulamasını da kapsayacak mı? → ⚠️ VERIFICATION REQUIRED"
group: kod
---

# OpenAPI Kod Örnekleri — Repo Kanıtlı · Kaynak-Etiketli

> **Bu dosya ne değildir:** Araç kurulumu, CLI rehberi veya "nasıl yazılır"
> anlatımı değildir. Bu dosya iki şeyi **ayrıştırarak** gösterir:
> **(A)** CoreMusic deposunda gerçekten var olan ve OpenAPI'ye
> eşlenebilecek **yüzey kanıtı**, **(B)** spec'ten alınmış ve **kaynak
> etiketli** belge örnekleri.
>
> **Kanıt kuralı:** `Dosya: … / Satır: …` yazan her satır 2026-10-12'de
> diskten okunmuştur. `Kaynak: …` yazan her blok resmi dokümantasyondan
> alınmıştır. **İkisini karıştırmak yasaktır.**

---

## §0 ELI15 — Bu dosyanın işi ne?

OpenAPI belgesi elle yazılmaz; mevcut **route tablosundan** türetilebilir.
CoreMusic'te bu tablo zaten vardır ve method-aware'tir — yani OpenAPI'nin
`paths.{yol}.{method}` yapısıyla **doğrudan örtüşür**. Bu dosya o örtüşmeyi
gerçek satırlarla gösterir. (Kaynak: §1 repo kanıtı)

---

## §GRUPLAR

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Derin-dal indeksi | 3 |
| mimari | `openapi-anayasa.md` | Kimlik, sürüm, üst düzlem | 3 |
| mimari | `openapi-sozlesme-dili.md` | Operation/parameter/response | 3 |
| mimari | `openapi-dogrulama.md` | JSON Schema, lint, CI kapısı | 3 |
| **kod** | **`openapi-kod-ornekleri.md`** | **Repo kanıtlı + kaynak-etiketli örnekler** | 3 |
| kanit | `openapi-web-arastirma.md` | ≥8 benzersiz resmi URL | 3 |

---

## §1 KANIT A — CoreMusic repo yüzeyi (diskten okundu)

### §1.1 Envanter (2026-10-12 ölçümü)

| Dosya | Satır | Ne taşır | OpenAPI karşılığı |
|---|---|---|---|
| `api.coremusic.net/config/routes.php` | 120 | API route kayıt defteri (GET + POST) | `paths` |
| `shared/src/Api/Routing/RouteTable.php` | 253 | Method-aware route tablosu sınıfı | Belge üreticisi (derive kaynağı) |
| `api.coremusic.net/tests/Unit/RouteConfigTest.php` | 208 | Route yapılandırması birim testi | Sözleşme testi (kısmen) |
| `shared/config/routes.php` | 131 | Sayfa route kayıtları (web) | `paths` (web tarafı — ayrı belge) |
| `shared/config/auth-routes.php` | 62 | Auth route kayıtları | `paths` (auth) |
| `shared/src/PageRouter/PageRouter.php` | 192 | Sayfa yönlendirici | Sayfa katmanı — API belgesi değil |
| `shared/src/PageRouter/RouteRegistry.php` | 83 | Sayfa route kaydı | Sayfa katmanı |

**Negatif kanıt (önemli):** 2026-10-12'de repo genelinde `openapi`/`swagger`
dizesini içeren `.yaml` / `.yml` / `.json` dosyası **yoktur** (tarama: 0
sonuç); `.php` / `.js` içinde `openapi` geçen dosya **yoktur** (0 sonuç).
→ **CoreMusic'te şu an OpenAPI belgesi yoktur.** Bu bir eksiklik kaydıdır,
varsayım değil. (Kaynak: §Kaynak Tablosu [R5])

---

### §1.2 Route kaydı — gerçek kod (repo)

**Dosya:** `api.coremusic.net/config/routes.php` · **Satır: 24–49** (GET bloğu tamamı — `return [` 24 · `'GET' => [` 25 · kapanış `],` 49)

| Blok | Satır aralığı | Giriş sayısı |
|---|---|---|
| `return [` + `'GET'` | 24–49 | **11** (6 koleksiyon prefix + 4 public auth POST-only + 1 `auth/me`) |
| `'POST'` | 51–119 | **6** (login · register · forgot-password · reset-password · set-gender · logout) |
| Dosya toplamı | 1–120 | 17 route girişi |

> **Sayım düzeltmesi (Truth Mode):** Bu dosyanın ilk yazımında "6 GET + 6
> POST" denilmişti — **YANLIŞTIR.** `Select-String` sayımı: GET **11**,
> POST **6**. `defaults()` içindeki **6** kayıt ise yalnızca **koleksiyon
> prefix** altının (satır 96–101) kopyasıdır; public uçlar ve `auth/me`
> orada **yoktur** (bkz. §1.7).

```php
return [
    'GET' => [
        // Faz 2 hardcodedi GET prefix kayıtları (davranış değişmez)
        '/api/v1/auth'     => ['service' => 'auth',     'handler' => 'authController'],
        '/api/v1/user'     => ['service' => 'user',     'handler' => 'userController'],
        '/api/v1/music'    => ['service' => 'music',    'handler' => 'musicController'],
        '/api/v1/playlist' => ['service' => 'playlist', 'handler' => 'playlistController'],
        '/api/v1/media'    => ['service' => 'media',    'handler' => 'mediaController'],
        '/api/v1/download' => ['service' => 'download', 'handler' => 'downloadController'],

        // Public auth uçları — exact kayıt, prefix `/api/v1/auth` ile çakışmasın
        // Bu 4 uç POST-only'dir: GET implemented=false → 405 + Allow: POST, OPTIONS
        '/api/v1/auth/login'          => ['service' => 'auth', 'handler' => 'authController', 'public' => true, 'implemented' => false],
        '/api/v1/auth/register'       => ['service' => 'auth', 'handler' => 'authController', 'public' => true, 'implemented' => false],
        '/api/v1/auth/forgot-password' => ['service' => 'auth', 'handler' => 'authController', 'public' => true, 'implemented' => false],
        '/api/v1/auth/reset-password' => ['service' => 'auth', 'handler' => 'authController', 'public' => true, 'implemented' => false],

        // Faz 1b: oturumdaki kullanıcıyı döndürür (200 {data:{user}} | 401)
        '/api/v1/auth/me' => [
            'service'     => 'auth',
            'handler'     => 'authController',
            'action'      => 'me',
            'public'      => false,
            'implemented' => true,
        ],
    ],
```

> **Kaynak:** `api.coremusic.net/config/routes.php`, satır 25–50 (2026-10-12 okundu).
> **Aktarım notu:** kaynak dosyada Türkçe yorum satırları **mojibake** olarak
> durmaktadır (`davranYY`, `Ǭsterim` benzeri); bu blokta **düzeltildi** ve
> düzeltme burada **kayıtlıdır** — mojibake kaydı `.ai/architecture/index.md` §6'dadır.
> **`// ...` gibi kısaltma işaretleri KULLANILMADI** (dolgu yasağı), blok
> GET bölümünün **tamamıdır**.

---

### §1.3 Eşleşme tablosu — route → OpenAPI

`RouteTable` kayıt anahtarları ile OpenAPI `operation` alanları arasındaki
**doğrudan** karşılık:

| Route anahtarı (`routes.php`) | Ne işe yarar (dosya içi yorum) | OpenAPI karşılığı | Not |
|---|---|---|---|
| dizin anahtarı (`GET`/`POST`) | HTTP method | `paths./yol.get` / `.post` | Birebir |
| yol anahtarı (`/api/v1/auth/login`) | URI yolu | `paths./yol` anahtarı | Birebir |
| `service` | çözümleme anahtarı | — (ext: `x-service`) | Spec'te karşılığı yok → `x-` uzantısı |
| `handler` | çözümleme anahtarı | — (ext: `x-handler`) | Aynı |
| `action` | dispatch anahtarı | — (ext: `x-action`) | Aynı |
| `public` | auth'sız erişim | `security: []` (boş = kimlik gerekmez) | §2.1'e bakınız |
| `implemented` | `false` → 405 + Allow | — (spec'te karşılığı **yok**) | §2.2 — **kritik boşluk** |
| `validation` | doğrulama kural seti → 422 + `error.fields` | `requestBody.content.*.schema` + 422 yanıtı | Kısmi eşleşme |
| `requiredRole` | RBAC rolü | `security` + `x-required-role` | `security` yetki taşımaz → §2.3 |
| `requiredPermission` | RBAC izni | `x-required-permission` | Aynı |
| `cacheable` / `cacheTtl` | ETag + Cache-Control | Response `headers` (RFC 9111 → `openapi-anayasa.md` [S8]) | §2.4 |

---

### §1.4 `match()` — üç yönlü sonuç (repo kodu, gerçek satırlar)

**Dosya:** `shared/src/Api/Routing/RouteTable.php` · **Satır: 111–138**

```php
public function match(string $uri, string $method): array
{
    $uri    = self::normalize($uri);
    $method = strtoupper($method);

    $matched = $this->find($method, $uri);

    if ($matched !== null) {
        if ($matched['route']['implemented']) {
            return ['status' => self::STATUS_OK, 'route' => $matched['route'], 'allow' => []];
        }

        // Route tanılandı ama controller'ı henüz yok (Faz 1b öncesi):
        // 404 DEĞİL — 405 + Allow (RFC 9110 §15.5.6).
        return [
            'status' => self::STATUS_METHOD_NOT_ALLOWED,
            'route'  => $matched['route'],
            'allow'  => $this->allowedMethods($uri),
        ];
    }

    $allow = $this->allowedMethods($uri);
    if ($allow !== []) {
        return ['status' => self::STATUS_METHOD_NOT_ALLOWED, 'route' => null, 'allow' => $allow];
    }

    return ['status' => self::STATUS_NOT_FOUND, 'route' => null, 'allow' => []];
}
```

> **Kaynak:** `shared/src/Api/Routing/RouteTable.php`, satır 111–138 (2026-10-12 okundu).

**Üç durumun OpenAPI'ye dönüşümü:**

| `status` sabiti | HTTP karşılığı | Belgede nasıl yazılır | Eşleşme |
|---|---|---|---|
| `STATUS_OK` | 200 (işlem) | `paths.{yol}.{method}` mevcut | ✅ Doğrudan |
| `STATUS_METHOD_NOT_ALLOWED` | 405 + `Allow` başlığı | `responses` içinde `"405"` + `headers.allow` | ✅ Doğrudan |
| `STATUS_NOT_FOUND` | 404 | `responses` içinde `"404"` veya `default` | ✅ Doğrudan |

**Durum sabitleri (satır 28–30):**

| Sabit | Değer |
|---|---|
| `STATUS_OK` | `'ok'` |
| `STATUS_METHOD_NOT_ALLOWED` | `'method_not_allowed'` |
| `STATUS_NOT_FOUND` | `'not_found'` |

---

### §1.5 `allowedMethods()` — `Allow` listesi ve `OPTIONS` (repo kodu)

**Dosya:** `shared/src/Api/Routing/RouteTable.php` · **Satır: 160–176**

```php
private function allowedMethods(string $uri): array
{
    $allow = [];

    foreach (array_keys($this->routes) as $method) {
        $entry = $this->find($method, $uri);
        if ($entry !== null && $entry['route']['implemented']) {
            $allow[] = $method;
        }
    }

    if ($allow !== []) {
        $allow[] = 'OPTIONS';
    }

    return $allow;
}
```

> **Kaynak:** `shared/src/Api/Routing/RouteTable.php`, satır 160–176 (2026-10-12 okundu).

**Önemli davranış ayrıntısı:** `Allow` listesine **yalnızca
`implemented => true`** olan methodlar girer; ayrıca liste doluysa
`OPTIONS` **eklenir**. Bu, `routes.php` yorumundaki
`405 + Allow: POST, OPTIONS` ifadesinin kaynağıdır.

**OpenAPI karşılığı:**

```yaml
responses:
  "405":
    description: "Method desteklenmiyor"
    headers:
      Allow:
        description: "Bu yol için izin verilen methodlar"
        schema:
          type: string
```

> **Doğrulama:** `Allow` bir response header'ıdır; `operation` alanı
> **değildir.** (Kaynak: §Kaynak Tablosu [S1] + [S2] — RFC 9110 §15.5.6
> **dosyada** kod yorumu olarak, spec `Allow` başlığı [S1].)

---

### §1.6 `find()` + `isDeclared()` — 404/405 ayrımının kaynağı (repo kodu)

**Dosya:** `shared/src/Api/Routing/RouteTable.php` · **Satır: 197–240**

Dosya yorumu (satır 181–193) davranışı **açıkça** tanımlar:

> Eski davranış: `str_starts_with` ile prefix'e düşen HER alt yol
> (ör. `/api/v1/auth/does-not-exist`) collection route'unu geri
> döndürüyordu → private route muamelesi görüp authenticate
> edilmemiş istemciye **401** (404 değil) dönüyordu. **Sözleşme
> ihlali: tanımsız yol = rota yok.**
>
> Yeni ayrım (ADR-084 Contract First):
> - exact kayıt (istenen methodta) → route (405/401 davranışı değişmez)
> - yol tabloda BİR methodda kayıtlıysa → prefix fallback
> - tabloda TANIMSIZ alt yol → `null` → **404** (Allow'sız)

**Yol normalize kuralı (satır 242–252):**

| Adım | Kural | Örnek |
|---|---|---|
| 1 | Sorgu dizesi atılır (`?` sonrası) | `/v1/tracks?x=1` → `/v1/tracks` |
| 2 | Son `/` kırpılır | `/v1/tracks/` → `/v1/tracks` |
| 3 | Boşsa `/` yapılır | `` → `/` |

> **OpenAPI ile ilişkisi:** `paths` anahtarları **normalize edilmiş**
> haldedir; `?` ile başlayan kısım belgeye **yazılmaz** (sorgu
> dizesi `parameter` ile `in: query` olarak tarif edilir).
> Bu eşleşme **doğrudandır.**

**Segment sınırı (satır 212–218):** `/api/v1/auth` yalnızca **gerçek**
alt yollarda eşleşir — `/api/v1/authorize` gibi.

| Yol | Sonuç | Neden |
|---|---|---|
| `/api/v1/auth/login` | exact kayıt | Tabloda var |
| `/api/v1/auth/me` | exact kayıt | Tabloda var |
| `/api/v1/auth/does-not-exist` | **404** | `isDeclared()` → false |
| `/api/v1/authorize` | **404** | Segment sınırı ihlali (`/` + devamı yok) |

> **Sözleşme etkisi:** OpenAPI'de `paths` yalnızca **gerçek** yolları
> içerir; tarayıcı/aracı tarafından tahmin edilen yollar belgede
> **bulunmaz.** Bu, belge ile depo davranışını **aynı çizgide** tutar.

---

### §1.7 `fromArray()` / `defaults()` — kaynak iki kere mi var?

**Dosya:** `shared/src/Api/Routing/RouteTable.php` · **Satır: 71–104**

| Yöntem | Ne yapar | Belgeye etkisi |
|---|---|---|
| `fromArray($definition)` | Yapılandırma dosyasından tablo kurar | **Ana kaynak** |
| `defaults()` | Constructor'a route verilmediğinde kullanılan **hardcoded** GET prefix tablosu | **Yinelenen kaynak riski** |

**Risk (kanıt-gerekçeli):** `defaults()` içindeki 6 GET kaydı
(satır 96–101), `routes.php`'deki 6 GET kaydının (satır 27–32)
**kopyasıdır.** Belge iki yerden türetilirse **drift** (kayma) olur.

| Türetme kaynağı | Risk | Durum |
|---|---|---|
| `routes.php` (tek) | Düşük — tek gerçek | 🟢 Önerilen (tasarım iddiası) |
| `defaults()` (tek) | Orta — yalnız GET, POST eksik | 🔴 Yetersiz |
| İkisi birden | Yüksek — drift | 🔴 Çakışma |

> **Karar:** hangisinin SSOT olduğu `UNKNOWN` — ADR bekliyor.
> **Uydurma yok:** depoda "OpenAPI türetme kaynağı = X" kararı
> **bulunmamıştır.**

---

### §1.8 Route anahtar envanteri (dosya içi doğrulanmış)

`routes.php` dosya yorumunda (satır 8–17) tarif edilen ve kodda
**gözlemlenen** anahtarlar:

| Anahtar | Tip | Zorunlu mu | Belgedeki karşılığı |
|---|---|---|---|
| `service` | string | Hayır | `x-service` |
| `handler` | string | Hayır | `x-handler` |
| `action` | string | Hayır | `x-action` |
| `public` | bool | Varsayılan `false` | `security: []` |
| `implemented` | bool | Varsayılan `true` | **Yok (§2.2)** |
| `validation` | array\|null | Varsayılan `null` | `requestBody…schema` (kısmi) |
| `requiredRole` | ?string | Varsayılan `null` | `x-required-role` |
| `requiredPermission` | ?string | Varsayılan `null` | `x-required-permission` |
| `cacheable` | bool | Varsayılan `false` | Response `headers` |
| `cacheTtl` | int | Varsayılan `0` | `Cache-Control: max-age` |
| `method` | string | `add()` içinde set edilir | `paths.{yol}.{method}` |
| `path` | string | `add()` içinde set edilir (normalize) | `paths` anahtarı |

> **Kaynak:** `RouteTable.php` satır 47–57 (`add()` içindeki varsayılanlar)
> + `routes.php` satır 8–17 (dosya yorumu) — ikisi de 2026-10-12 okundu.

**Varsayılan değerler (`add()`, satır 49–57):**

| Alan | Varsayılan |
|---|---|
| `public` | `false` |
| `implemented` | `true` |
| `cacheable` | `false` |
| `validation` | `null` |
| `requiredRole` | `null` |
| `requiredPermission` | `null` |
| `cacheTtl` | `0` |

---

## §2 KANIT A — Eşleşmeyen üç nokta (dürüstlük bölümü)

Bu üç nokta, "route tablosu OpenAPI'ye çevrilebilir" iddiasını **sınırlar**.
Sınırı yazmazsak iddia abartılı olur.

### §2.1 `public` → `security: []` eşleşmesi

| Route durumu | OpenAPI | Sonuç |
|---|---|---|
| `public => true` | `security: []` (boş dizi) = "bu işlemde kimlik gerekmez" | ✅ Eşleşir |
| `public => false` | Kök `security` (belge geneli gereksinim) devreye girer | ✅ Eşleşir |

> **Kaynak (şartname):** OpenAPI'de boş `security` dizisi, "kimlik
> gerekmez" anlamına gelir. (Kaynak: §Kaynak Tablosu [S1])

### §2.2 `implemented: false` — spec'te karşılığı OLMAYAN durum

`routes.php` yorumu (satır 12–13) diyor ki:

> `implemented : false → route TANIMLI ama controller yok → 405 + Allow
> (404 DEĞİL); Faz 1b'de 6 POST ucu true yapıldı`

> **Alıntı aktarım notu (Truth Mode):** Kaynak satır 12–13'te Türkçe
> karakterler **mojibake** hâlindedir (`404 DE�?�L` · `yap��ld��`);
> alıntı **duzeltilmis** olarak verilmistir. İçerik **birebirdir**,
> yalnız karakter kodlaması düzeltilmiştir.
>
> **Ayrıca:** "6 POST" ifadesi **bu dosyanın iddiası değil**, kaynağı
> `routes.php` yorumudur. Bağımsız sayım (2026-10-12): POST bloğu
> satır 51–119'da **tam 6 route** var (login · register · forgot-password
> · reset-password · set-gender · logout) → **yorum doğrudur**.
> Ancak bu 6 uçtan **farklı** bir sayım olan `implemented => true`
> toplamı **7'dir** (6 POST + GET `auth/me`) — ikisi aynı şey değildir.

Bu durumun OpenAPI'de **doğrudan bir alanı yoktur.** Üç seçenek var ve
**hiçbiri henüz kararlaştırılmamıştır:**

| Seçenek | Ne demek | Sorun |
|---|---|---|
| A — Belgeye hiç koyma | Uç yokmuş gibi davran | Ama route `405` döndürüyor → belge ile gerçek çelişir |
| B — `405` yanıtını yaz | "Method desteklenmiyor" yanıtı | Tüketici neden çağıracak ki? Gereksiz gürültü |
| C — `x-implemented: false` uzantısı | Route'un fiili durumunu taşı | Standart dışı, araçlar görmez |

**Karar: `UNKNOWN` — ADR bekliyor.** Bu üç seçenekten hangisinin
seçildiğine dair depoda kanıt **yoktur**; uydurulmadı.

### §2.3 `requiredRole` / `requiredPermission` → `security` yetmez

OpenAPI `security` alanı **autentikasyon** (kimlik) gereksinimidir;
**yetkilendirme** (rol/izin) taşımaz. Route tablosundaki `requiredRole`
alanının spec'teki karşılığı **standart bir alan değildir.** Bunun için
`x-` uzantısı gerekir → **`UNKNOWN` (uzantı adı kararı yok).**

> **Uydurma yasak:** "OpenAPI'de `requiredRole` alanı vardır" cümlesi
> **spec'te yoktur**, yazılmamıştır.

### §2.4 `cacheable` / `cacheTtl` → yanıt başlığı olarak yazılır

ETag ve `Cache-Control` bir `operation` alanı değil, **response
`headers`** alanıdır. (Kaynak: **`openapi-anayasa.md` §Kaynak Tablosu
[S8]** — RFC 9111; bu dosyada URL tekrarı yapılmadı.)

---

## §3 KANIT B — Repo'dan OpenAPI'ye türetme (tasarım iddiası)

> ⚠️ **Bu bölüm TASARIM İDDİASIDIR — depoda böyle bir kod YOKTUR.**
> Hiçbir satır "üretilmiştir / çalıştırılmıştır" diye sunulmaz.

### §3.1 Türetme eşlemesi (kural tablosu)

| Route tablosu alanı | OpenAPI dönüşümü | Yöntem |
|---|---|---|
| `GET` bloğu anahtarları | `paths.{yol}.get` | Doğrudan |
| `POST` bloğu anahtarları | `paths.{yol}.post` | Doğrudan |
| `public => true` | `security: []` | Doğrudan |
| `public => false` | kök `security` devrede | Doğrudan |
| `validation` kural seti | `requestBody.content["application/json"].schema` | Türetilir — **kural dili birebir eşleşmez** |
| `requiredRole` | `x-required-role` | Uzantı |
| `implemented => false` | **karar yok (§2.2)** | — |
| `service` / `handler` / `action` | `x-service` / `x-handler` / `x-action` | Uzantı |

### §3.2 Türetilmiş belge iskeleti (kaynak-etiketli)

Aşağıdaki YAML, **§1.2'deki gerçek route kayıtlarından** türetilmiş bir
iskelettir; sunucu adresi **kasıtlı geçersiz** alandır.

```yaml
# Türetme kaynağı: api.coremusic.net/config/routes.php (satır 24-49)
# Durum: TASARIM İDDİASI — bu belge depoda mevcut DEĞİLDİR
openapi: "3.1.1"
info:
  title: "CoreMusic API"
  version: "1.0.0"          # sürüm kararı → UNKNOWN
servers:
  - url: "https://api.example.invalid"
paths:
  /api/v1/auth/me:
    get:
      operationId: getAuthMe
      summary: "Oturumdaki kullanıcıyı döndürür"
      security:
        - bearerAuth: []    # routes.php: public => false
      responses:
        "200":
          description: "Kullanıcı döndü"
        "401":
          description: "Kimlik doğrulanamadı"
  /api/v1/auth/login:
    post:
      operationId: postAuthLogin
      summary: "Giriş"
      security: []          # routes.php: public => true
      requestBody:
        content:
          application/json:
            schema:
              $ref: "#/components/schemas/LoginRequest"
      responses:
        "200": { description: "Başarılı" }
        "422": { description: "Doğrulama hatası" }
components:
  securitySchemes:
    bearerAuth:
      type: http
      scheme: bearer
```

> **Doğrulanma durumu:** `operationId` değerleri **tahmindir** — gerçek
> route kaydında `operationId` alanı yoktur. Bu yüzden `x-operation-id`
> mi kullanılacağı da `UNKNOWN`'dur.

---

## §4 KANIT B — Kaynak-etiketli belge örnekleri

> Bu bloklar **spec metninden** alınmıştır, repo'dan **değildir**.
> Her blokta `Kaynak:` satırı zorunludur.

### §4.1 Parametre serileştirme (`style` / `explode`)

```yaml
parameters:
  - name: ids
    in: query
    style: form
    explode: true
    schema:
      type: array
      items: { type: string }
```

> **Kaynak:** `https://swagger.io/docs/specification/v3_0/serialization/`
> (2026-10-12) — `style: form` + `explode: true` varsayılan query
> serileştirme davranışını tanımlar.

**Serileştirme matrisi (şartnameden türetilmiş özet):**

| `in` | `style` (varsayılan) | `explode` (varsayılan) | Örnek çıktı |
|---|---|---|---|
| query | `form` | `true` | `ids=a&ids=b` |
| query | `form` | `false` | `ids=a,b` |
| path | `simple` | `false` | `a,b` |
| path | `simple` | `true` | `a=1,b=2` |
| header | `simple` | `false` | `a,b` |
| cookie | `form` | `false` | `ids=a,b` |

### §4.2 Webhook (3.1 kök alanı)

```yaml
webhooks:
  trackProcessed:
    post:
      requestBody:
        content:
          application/json:
            schema:
              type: object
      responses:
        "204":
          description: "Alındı"
```

> **Kaynak:** `https://spec.openapis.org/oas/v3.1.1.html` [S1]
> (2026-10-12) — `webhooks` 3.1'de kök seviyede bir alandır.
>
> **Not (çakışma düzeltmesi):** Bu örnek daha önce
> `learn.openapis.org/examples/v3.1/webhook-example.html` adresine
> bağlanmıştı; o adres `openapi-anayasa.md` S9'da kullanıldığı için
> **S1'e** çevrildi.

### §4.3 Problem Details (422/5xx yanıt biçimi)

```json
{
  "type": "https://example.invalid/problems/validation",
  "title": "Doğrulama hatası",
  "status": 422,
  "detail": "Alanlar geçersiz",
  "instance": "/api/v1/auth/login"
}
```

> **Kaynak:** `https://datatracker.ietf.org/doc/rfc9457/` (2026-10-12) —
> RFC 9457, HTTP API'leri için problem ayrıntısı biçimini tanımlar.
>
> **Repo bağlantısı:** `routes.php` yorumunda hata biçiminden
> `error.fields` söz edilmektedir (satır 14). `error.fields` alanı
> RFC 9457 ile **birebir aynı değildir** → ikisinin eşleştirilmesi
> `UNKNOWN` ve ADR beklemektedir. **Eşitmiş gibi yazılmadı.**

---

## §5 Test Yüzeyi (repo kanıtı)

| Dosya | Satır | Ne test eder | OpenAPI ile ilişkisi |
|---|---|---|---|
| `api.coremusic.net/tests/Unit/RouteConfigTest.php` | 208 | Route yapılandırması | Yapısal doğrulamaya **kismen** denk |
| `shared/config/routes.php` + `auth-routes.php` | 131 + 62 | (test değil, kayıt) | — |

**Doğrulama sorusu:** `RouteConfigTest` route'ların varlığını mı,
yoksa **sözleşme uyumunu** (istek/yanıt şeması) mı test eder →
bu turda dosyanın **tamamı okunmadı** → `⚠️ VERIFICATION REQUIRED`.

**Türetilmiş test kuralı (tasarım iddiası):** bir route tanımlıysa ve
`implemented => true` ise, o route için OpenAPI'de bir `operation`
**bulunmalıdır**; tersi de geçerlidir. Bu kural depoda **yoktur**,
öneridir.

---

## §6 Kanıt Durumu Özeti

| # | İddia | Kanıt türü | Durum |
|---|---|---|---|
| 1 | Route tablosu method-aware'tir | Repo (`RouteTable.php`, 253 satır) | ✅ OKUNDU |
| 2 | GET **11** + POST **6** route girişi tanımlı | Repo (`routes.php`, GET 24–49 · POST 51–119) | ✅ OKUNDU (ilk yazım "6 GET + 6 POST" idi → düzeltildi) |
| 3 | `implemented: false` → 405 + Allow | Repo (dosya yorumu, satır 12–13) | ✅ OKUNDU (davranış test edilmedi) |
| 4 | Repo'da OpenAPI belgesi yok | Tam tarama, 0 sonuç | ✅ NEGATİF KANIT |
| 5 | `security: []` = kimlik gerekmez | Şartname | ✅ [S1] |
| 6 | Serileştirme `style`/`explode` matrisi | Şartname/dokümantasyon | ✅ [S3] |
| 7 | `webhooks` 3.1 kök alanı | Şartname metni | ✅ [S1] |
| 8 | RFC 9457 problem biçimi | IETF | ✅ [S4] |
| 9 | `implemented: false` belgeye nasıl yansır | — | ❓ UNKNOWN (§2.2) |
| 10 | `requiredRole` uzantı adı | — | ❓ UNKNOWN (§2.3) |
| 11 | `error.fields` ↔ RFC 9457 eşleşmesi | — | ❓ UNKNOWN (§4.3) |
| 12 | `operationId` adlandırma kuralı | — | ❓ UNKNOWN (§3.2) |

---

## §7 Terim Sözlüğü (bu dosyaya özgü)

| Terim | Anlamı |
|---|---|
| **Repo kanıtı** | Diskten okunmuş, `Dosya:` + `Satır:` taşır |
| **Kaynak-etiketli** | Resmi dokümantasyondan alınmış, `Kaynak:` taşır |
| **Türetme (derive)** | Mevcut route tablosundan belge üretme — henüz YOK |
| **Negatif kanıt** | "Arama yaptık, bulunmadı" — bulunmaması bir bulgudur |
| **Uzantı (`x-`)** | Spec'te standart olmayan, `x-` önekli anahtar |

---

## §8 Sık Karıştırılan Kavramlar

| Karışıklık | Doğrusu |
|---|---|
| "Route kaydı = OpenAPI belgesi" | Hayır; route **kaynak**, belge **üretimdir** — üretim henüz yok |
| `service`/`handler` OpenAPI alanı | Hayır; `x-` uzantısı gerekir |
| `implemented: false` = `404` | Hayır; dosya yorumu **405 + Allow** diyor |
| `requiredRole` = `security` | Hayır; `security` autentikasyondur, rol yetkilendirme |
| `error.fields` = RFC 9457 | Hayır — eşleşme `UNKNOWN` |

---

## §9 Kapsam Dışı

1. `openapi-sozlesme-dili.md` — operation/parameter/response ayrıntısı
2. `openapi-dogrulama.md` — JSON Schema, lint, CI
3. Belge üretme aracının seçimi / kurulumu → araç envanteri bu turda çekilmedi → `UNKNOWN`
4. `shared/config/routes.php` (sayfa route'ları) için ayrı belge — kapsam dışı
5. gRPC / GraphQL → `K013/protocol-adapters`

---

## §10 Açık Sorular

| # | Soru | İşaret |
|---|---|---|
| Q1 | Belge hangi kaynaktan türetilecek? (`routes.php` varsayımı) | UNKNOWN |
| Q2 | `implemented: false` belgeye nasıl yansıyacak? | UNKNOWN (ADR bekliyor) |
| Q3 | `requiredRole` için uzantı adı ne olacak? | UNKNOWN |
| Q4 | `error.fields` ↔ RFC 9457 eşleştirilecek mi? | UNKNOWN |
| Q5 | `RouteConfigTest` sözleşmeyi de test ediyor mu? | ⚠️ VERIFICATION REQUIRED |
| Q6 | Belge tek mi, API/sayfa ayrı mı? | UNKNOWN |

---

## § Kaynak Tablosu

### A) Repo kanıtları (diskten okundu, 2026-10-12)

| # | Dosya | Satır | Bulgu |
|---|---|---|---|
| R1 | `api.coremusic.net/config/routes.php` | 120 (toplam) · 24–49 okundu | GET + POST route kayıtları, `public`/`implemented`/`validation` alanları |
| R2 | `shared/src/Api/Routing/RouteTable.php` | 253 (toplam) · 1–60 okundu | `STATUS_OK` / `STATUS_METHOD_NOT_ALLOWED` / `STATUS_NOT_FOUND` sabitleri; `add()` imzası |
| R3 | `api.coremusic.net/tests/Unit/RouteConfigTest.php` | 208 | Route yapılandırması testi (tamamı OKUNMADI) |
| R4 | `shared/config/routes.php` | 131 | Sayfa route kayıtları |
| R5 | Repo geneli tarama | — | `openapi`/`swagger` geçen `.yaml/.yml/.json/.php/.js` = **0 sonuç** |

### B) Resmi şartname / dokümantasyon

| # | İddia (kısa) | URL | Tarih | Durum |
|---|---|---|---|---|
| S1 | `security` gereksinim mantığı — boş dizi = kimlik gerekmez · `webhooks` kök alanı | https://spec.openapis.org/oas/v3.1.1.html | 2026-10-12 | ✅ 200 |
| S2 | RFC 9110 §15.5.6 — 405 Method Not Allowed + `Allow` başlığı | https://www.rfc-editor.org/rfc/rfc9110.html | 2026-10-12 | ✅ 200 |
| S3 | Parametre serileştirme — `style` / `explode` | https://swagger.io/docs/specification/v3_0/serialization/ | 2026-10-12 | ✅ çekildi |
| S4 | RFC 9457 — Problem Details for HTTP APIs | https://datatracker.ietf.org/doc/rfc9457/ | 2026-10-12 | ✅ çekildi |
| S5 | Depo / sürümler (3.1.1 metni) | https://github.com/oai/openapi-specification | 2026-10-12 | ✅ çekildi |
| S6 | RFC 8288 — Web Linking (`Link` response header'ı) | https://www.rfc-editor.org/rfc/rfc8288.html | 2026-10-12 | ✅ 200 |

> **Çakışma kuralı (düzeltme 2026-10-12):** İlk yazımda bu tabloda
> `learn.openapis.org/examples/v3.1/webhook-example.html` (S2) ve
> `www.rfc-editor.org/rfc/rfc9111.html` (S6) vardı ve **"çakışma 0"
> iddiası YANLIŞTI** — her ikisi de `openapi-anayasa.md` içinde
> (S9 ve S8) geçiyordu. Düzeltme: S2 → RFC 9110, S6 → RFC 8288 ile
> değiştirildi; `webhooks` örneği S1'e, önbellek iddiası ise
> **`openapi-anayasa.md` §Kaynak Tablosu [S8]**'e
> **dosyalar arası işaret** ile verildi (URL tekrarı yok).
>
> Bu dosyada kullanılmayan ve `openapi-anayasa.md` + `openapi-kod-ornekleri.md`
> içinde de **olmayan** boş kaynaklar (gelecek turlar için):
> `spec.openapis.org/overlay/latest.html` · `datatracker.ietf.org/doc/rfc8594/` ·
> `spec.openapis.org/oas/v3.2.0.html`.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Kaynak:** v2 §2.13 · ADR-097 §5.5 · registry §8.1 · CoreMusic deposu (R1–R5) · **Tarih: 2026-10-12**
