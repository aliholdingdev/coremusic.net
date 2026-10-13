---
node_id: K009-security/authorization/ornekler
node_type: module
status: verify-required
parent_id: K009-security/authorization
children: []
depends_on:
  - K009-security/authorization
evidence:
  - "4 dosyadan birebir alıntı (2026-10-12 okundu) — uydurma kod yok"
validation:
  - "2026-10-12 — her alıntının dosya + satır aralığı doğrulandı"
open_questions:
  - "RBAC test dosyaları (tests/) bu oturumda okunmadı → dalga-3"
group: kod
---

# authorization-ornekler.md — Gerçek Kod Örnekleri

> Kurallar: alıntılar diskten okunmuştur; düzenlenmemiştir.
> Yorum satırları analiz amaçlıdır ve özgün koda EKLENMEMİŞTİR.

## §1 Rol Kontrolü — PermissionMiddleware.php · 17-45

~~~php
public function handle(array $request, callable $next): array
{
    $routeMeta = $request['_route_meta'] ?? [];
    $auth      = $request['_auth'] ?? [];

    // Auth bilgisi yoksa → PermissionMiddleware ilgilenmez, sonraki middleware'a geç
    if (empty($auth['userId'])) {
        return $next($request);
    }

    $userRole = $auth['role'] ?? 'user';

    // Role kontrolü
    $requiredRole = $routeMeta['requiredRole'] ?? null;
    if ($requiredRole !== null && $userRole !== $requiredRole) {
        return $this->forbidden('role_insufficient', "Required role: {$requiredRole}");
    }

    // Permission kontrolü
    $requiredPermission = $routeMeta['requiredPermission'] ?? null;
    if ($requiredPermission !== null) {
        $userPermissions = $auth['permissions'] ?? [];
        if (!is_array($userPermissions) || !in_array($requiredPermission, $userPermissions, true)) {
            return $this->forbidden('permission_denied', "Required permission: {$requiredPermission}");
        }
    }

    return $next($request);
}
~~~

**Yorum:**
- satır 23-25: kimlik yok → pas geçme (koruma AuthMiddleware'de).
- satır 31: rol eşitliği !== ile strict; tip gizlemesi yok.
- satır 39: in_array üçüncü parametresi true — zorunlu.
- satır 32/40: mesaj rol adını içerir; kullanıcıya DÖNMEZ, JSON error
  kodu üretilir (satır 56-62'de gövde sınırlandırılmıştır).

## §2 Reddi Gözleme — PermissionMiddleware.php · 47-63

~~~php
private function forbidden(string $code, string $message): array
{
    // P5-32: authz reddi gözlemlenir (öncekte sessizce 403 dönüyordu).
    \CoreMusic\Log\LoggerFactory::getInstance()->securityEvent('authz_denied', [
        'reason' => $code,
        'uri'    => $_SERVER['REQUEST_URI'] ?? '-',
        'ip'     => $_SERVER['REMOTE_ADDR'] ?? '-',
    ]);

    return [
        'httpStatus' => 403,
        'type'       => 'json',
        'body'       => ['error' => $code, 'message' => $message],
        'headers'    => [],
        'halt'       => true,
    ];
}
~~~

**Yorum:** reddin üç parçası vardır — karar (§1), kayıt (burada), durdurma
(halt => true). Üçünden biri eksikse reddi denetlemek imkânsızdır.

## §3 Origin Reddi — OriginCheckMiddleware.php · 34-61

~~~php
public function handle(array $request, callable $next): array
{
    $origin = $request['server']['HTTP_ORIGIN'] ?? '';

    // Same-origin veya origin yoksa → devam
    if ($origin === '') {
        return $next($request);
    }

    if ($this->isOriginAllowed($origin)) {
        return $next($request);
    }

    // İzinsiz origin → 403 (P5-32: olay artık loglanır)
    \CoreMusic\Log\LoggerFactory::getInstance()->securityEvent('origin_blocked', [
        'origin' => $origin,
        'uri'    => $_SERVER['REQUEST_URI'] ?? '-',
        'ip'     => $_SERVER['REMOTE_ADDR'] ?? '-',
    ]);

    return [
        'httpStatus' => 403,
        'type'       => 'json',
        'body'       => ['error' => 'origin_not_allowed', 'message' => 'CORS origin policy violation.'],
        'headers'    => [],
        'halt'       => true,
    ];
}
~~~

**Yorum:** üç karar dallanması — boş origin (geç), izinli (geç), izinsiz
(403 + kayıt). Bosluk kontrolü bilinçlidir: header yoksa tarayıcı isteği
cross-origin değildir.

## §4 Suffix Kuralı — OriginCheckMiddleware.php · 72-111

~~~text
allowed girdisi "host"           → yalnız birebir host eşleşmesi (satır 103)
allowed girdisi "*.host"         → alt alan kabul, apex hariç (satır 95-102)
allowed girdisi scheme'li        → host + scheme (+ port) bağlayıcı (satır 86-94)
hiçbiri uyuşmuyor + production   → false (fail-closed) (satır 108-111)
~~~

**Yorum:** apex'in kendisi *.example.com ile eşleşmez (satır 98, host !== apex)
— bu bilinçli daraltmadır: wildcard apex'i kapsasaydı credentialed origin
olarak apex dahi reflekt edilebilirdi.

## §5 Başlık Yayımı — CorsMiddleware.php · 63-77

~~~php
// Yalnız allowlist'e ait origin reflekt edilir (ADR-010/022 — wildcard yok,
// allowCredentials=true ile birlikte rastgele origin yansıtmak yasak).
if (!$this->isOriginAllowed($origin)) {
    return $response;
}

$response['headers'] = array_merge($response['headers'], [
    'Access-Control-Allow-Origin'      => $origin,
    'Access-Control-Allow-Credentials' => $this->allowCredentials ? 'true' : 'false',
    'Access-Control-Allow-Methods'     => implode(', ', $this->allowedMethods),
    'Access-Control-Allow-Headers'     => implode(', ', $this->allowedHeaders),
    'Access-Control-Max-Age'           => '86400',
]);
~~~

**Yorum:** başlıklar origin KONTROL EDİLDİKTEN sonra yazılır. Bu sıra, MDN'nin
"credentials + wildcard yasak" kuralıyla birebir örtüşür ve wildcard kullanımını
imkânsız kılar ($origin somut değerdir).

## §6 API 401/403 — Api/Middleware/AuthorizationMiddleware.php · 42-71

~~~php
// Check if user is authenticated
$authUser = $request['_auth_user'] ?? null;
if ($authUser === null) {
    return ApiResponse::error('UNAUTHORIZED', 'Authentication required', 401);
}

// Check required role
$requiredRole = $route['requiredRole'] ?? null;
if ($requiredRole !== null) {
    $userRoles = $authUser['roles'] ?? [];
    if (!in_array($requiredRole, $userRoles, true)) {
        return ApiResponse::error('FORBIDDEN', 'Insufficient permissions', 403);
    }
}
~~~

**Yorum:** 401 (satır 44-49) ile 403 (satır 65-69) AYNI metodta, ardışık
bloklardadır — ayrımın kaynağı kimlik varlığıdır, mesaj metni değil.

## §7 Public Rota — Api/Middleware/AuthorizationMiddleware.php · 94-110

~~~php
private function isPublicRoute(array $request): bool
{
    $route = $request['_route'] ?? null;
    if (is_array($route) && !empty($route['public'])) {
        return true;
    }

    $uri = $_SERVER['REQUEST_URI'] ?? '/';

    foreach (AuthenticationMiddleware::PUBLIC_ROUTE_PATTERNS as $pattern) {
        if (str_starts_with($uri, $pattern)) {
            return true;
        }
    }

    return false;
}
~~~

**Yorum:** ilk katman rota bayrağı (bilinçli), ikinci katman yalnız başlangıç
eşleşmesidir (str_starts_with). /admin public listedeyse /admin-panel de public
olurdu → pattern'ler sondaki / ile tanımlıdır; kontrol: dogrulama §2 no 14.

## §8 Negatif Örnek — GÜVENLİSİZ vs ÜRETİMDEKİ

~~~text
GÜVENLİSİZ (yapılmaması gereken)              ÜRETİMDEKİ KARŞILIĞI
──────────────────────────────────────────────────────────────────
role kontrolü == (tip gizlenir)        →     !== / in_array strict (31, 39)
origin'e sonra başlık yazmak           →     önce kontrol, sonra yazım (65-75)
prod'da dev origin fallback            →     isProduction=false döner (108-111)
reddi sessizce 401 yapmak              →     403 + authz_denied (P5-32)
public rotayı string contains ile      →     str_starts_with + rota bayrağı
~~~

## §9 Test Yüzeyine Bağ

| Örnek | İlgili test (dalga-3) |
|---|---|
| §1 rol/permission | PermissionMiddleware test dosyası → UNKNOWN |
| §3 origin reddi | OriginCheck test aranacak |
| §5 başlık yayımı | Cors test aranacak |
| §6 401/403 ayrımı | Api Authorization test aranacak |

Test dosyası okunmadığı için satır numarası UYDURULMAZ → UNKNOWN.

## §10 Alıntı Sınırları

1. Toplam 4 gerçek dosya, 6 kod bloğu.
2. Her blokta dosya + satır aralığı zorunlu.
3. Özgün koda yorum EKLENMEZ (yalnız bu dosyada analiz).
4. Tahmini/uydurma kod: YOK.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §11 PermissionMiddleware — TAM AKIŞ ÇÖZÜMÜ (kod okuması 2026-10-13)

> Kaynak: `shared/src/Middleware/PermissionMiddleware.php` (64 satır, diskten okundu). Sınıf başlığındaki künye: "Permission Middleware (L1 — Pipeline #9)" · "ADR-010/043 uyumlu. Frozen sıra: ...Auth → Permission → Validation → Controller".

| Adım | Satır | Gözlem | Güvenlik anlamı |
|---|---|---|---|
| 1 | 17 | `handle(array $request, callable $next)` PSR-15 benzeri imza | pipeline uyumu |
| 2 | 19 | `$routeMeta = $request['_route_meta'] ?? []` | yetki gereksinimi rotadan gelir |
| 3 | 20 | `$auth = $request['_auth'] ?? []` | kimlik bilgisi AuthMiddleware'den gelir |
| 4 | 23-25 | `empty($auth['userId'])` → doğrudan `$next($request)` | kimliksiz isteği İŞLEMEZ (Auth katmanının işi) |
| 5 | 27 | `$userRole = $auth['role'] ?? 'user'` | rol yoksa varsayılan `user` |
| 6 | 30 | `requiredRole` yoksa rol denetimi YAPILMAZ | rota meta'sı yoksa denetim kapalı |
| 7 | 31 | `$userRole !== $requiredRole` → 403 | `!==` KATI eşitlik — rol hiyerarşisi YOK |
| 8 | 32 | hata kodu `role_insufficient` | ayrıştırılabilir hata kodu |
| 9 | 36 | `requiredPermission` null ise denetim yok | izin gereksinimi opsiyonel |
| 10 | 38 | `$userPermissions = $auth['permissions'] ?? []` | izin listesi auth yükünde |
| 11 | 39 | `!is_array(...) \|\| !in_array(..., true)` | `strict: true` — tip güvenli üyelik |
| 12 | 40 | hata kodu `permission_denied` | ayrıştırılabilir hata kodu |
| 13 | 44 | tüm denetimler geçerse `$next($request)` | devam |
| 14 | 47-63 | `forbidden()` ortak redd üreticisi | tek noktadan red |

| §11 Çıkarımlar | Kanıt | Hükm |
|---|---|---|
| Yetki reddi gözlendi (önce sessiz 403) | satır 49 `// P5-32: authz reddi gözlemlenir` | iyileştirme kayıtlı |
| Reddi loglama zorunlu | satır 50 `LoggerFactory::getInstance()->securityEvent('authz_denied', ...)` | denetlenebilir red |
| Log alanları: reason · uri · ip | satır 51-53 | telemetri tanımlı |
| Red gövdesi JSON + `halt: true` | satır 56-62 | pipeline durur |
| HTTP durumu 403 | satır 57 | 401 ile karıştırılmaz |
| Rol varsayılanı `user` | satır 27 | rolü olmayan kullanıcı en düşük yetkide |
| Katı eşitlik | satır 31 `!==` | `admin` > `editor` gibi basamak YOK |
| İzin tip güvenliği | satır 39 `in_array(..., true)` | string/int karışması yetki açığı yapmaz |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §12 REDDİ GÖZLEM VE LOG SÖZLEŞMESİ (authz_denied)

| Alan | Kaynak satır | İçerik | Risk notu |
|---|---|---|---|
| `reason` | 51 | `role_insufficient` / `permission_denied` | ayrıştırılabilir |
| `uri` | 52 | `$_SERVER['REQUEST_URI'] ?? '-'` | sorgu dizesi loga girer → hassas parametre var mı? ⚠️ VERIFICATION REQUIRED |
| `ip` | 53 | `$_SERVER['REMOTE_ADDR'] ?? '-'` | proxy arkasında gerçek IP UNKNOWN |
| olay adı | 50 | `authz_denied` | securityEvent tipi |
| seviye | 50 | `LoggerFactory::getInstance()` | redaksiyon kuralı §8/kod-ornekleri |

| Log Kuralı | Uygulama |
|---|---|
| Red olayı sessiz kalamaz | kod zaten `securityEvent` çağırıyor (satır 50) |
| Parola/token loglanmaz | bu metot gövde almıyor → sızıntı yüzeyi yok |
| URI logu | `REQUEST_URI` ham yazılır → redaksiyon denetimi gerekir |
| IP | doğrudan `REMOTE_ADDR` → X-Forwarded-For işlenmiyor (proxy durumu UNKNOWN) |
| Tekrar eden red | kural/brute-force sinyali → rate-limit katmanına bağlanır |
| Denetim | `audit` dalga-3'te log akışı doğrulanacak |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §13 YETKİ HATA KODU MATRİSİ (403 AİLESİ)

| Hata kodu | Koşul (satır) | HTTP | Kullanıcıya gösterilen mesaj | Güvenlik |
|---|---|---|---|---|
| `role_insufficient` | rol katı eşit değil (31-32) | 403 | `Required role: {rol}` | rol adı sızar → ⓘ bilgi sızıntısı |
| `permission_denied` | izin listede yok (39-40) | 403 | `Required permission: {izin}` | izin adı sızar → ⓘ bilgi sızıntısı |
| 401 (Auth katmanı) | kimlik yok/yanlış | 401 | AuthMiddleware sözleşmesi | §6 diğer dosyada |
| 404 (router) | rota yok | 404 | — | 403 ile karıştırılmaz (master §23) |
| 403 (API katmanı) | `Api/Middleware/AuthorizationMiddleware` §6/§7 | 403 | API sözleşmesi | iki katman 403'ü ayrı kod |

| Kod Kuralı | Açıklama |
|---|---|
| Ayrıştırılabilirlik | gövde `error` alanı makinece okunur |
| İnsan mesajı | `message` alanı Türkçe/düz metin; stack yok |
| Rol/izin adı | eğitim saldırısı yüzeyi; üretimde genelleştirilebilir → açık soru |
| Sıralama | Auth (401) → Permission (403) — tersine dönmez |
| Gözlem | her 403 `authz_denied` logu üretir (§12) |
| Tutarlılık | Web ve API 403 gövdeleri aynı anahtarı kullanmalı ⚠️ VERIFICATION REQUIRED |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §14 BAŞARISIZLIK SENARYOLARI (NEGATİF KATALOG)

| # | Senaryo | Beklenen davranış | Kod kanıtı | Durum |
|---|---|---|---|---|
| 1 | Kimlik yok, rota `requiredRole: admin` | Auth katmanında 401; Permission çalışmaz | satır 23-25 (pas geçiş) | akış kurgusu |
| 2 | Kimlik var, rol `user`, rota `admin` | 403 `role_insufficient` + `authz_denied` logu | satır 31-32 · 50 | kanıtlı |
| 3 | Rol doğru, izin listede yok | 403 `permission_denied` + log | satır 39-40 · 50 | kanıtlı |
| 4 | `permissions` null | `?? []` → boş liste → izin reddi | satır 38 | kanıtlı |
| 5 | `permissions` string (tip bozukluğu) | `!is_array` koşulu → red (fail-closed) | satır 39 | kanıtlı |
| 6 | `requiredRole` tanımsız | rol denetimi atlanır, izin denetimi devam eder | satır 30 | kanıtlı (rota meta'sı yoksa denetimsiz) |
| 7 | `requiredPermission` tanımsız | izin denetimi atlanır | satır 36-37 | kanıtlı |
| 8 | `role` auth yükünde yok | varsayılan `user` | satır 27 | kanıtlı |
| 9 | `_auth` hiç yok | pas geçiş (Auth katmanı sorumlu) | satır 23-25 | kanıtlı |
| 10 | Rota meta'sı hiç yok | tüm denetimler pas geçer | satır 19 · 30 · 36 | **dikkat**: fail-open rota tanımı |

| §14 Kural | Açıklama |
|---|---|
| Fail-open yalnız rota tanımı yokken | middleware varsayılanı değil, rota meta'sı yokluğu |
| Yeni rota ekleyen | `requiredRole`/`requiredPermission` ZORUNLU (dokümante edilmeli) |
| Senaryo 10 | denetimli test ile teyit edilmeli → test-stratejisi |
| Senaryo 6-7 | varsayılanların rota kuralına göre seçilmesi mimari karar |
| Kanıtsız iddia | bu tabloda yok; her satır satır numarasına bağlı |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §15 CWE VE CORS EŞLEŞTİRİLMESİ (BU DOSYANIN KAYNAK HAVUZU)

| # | Resmi başlık (sayfadan) | URL | Eşleştiği § | Durum |
|---|---|---|---|---|
| 1 | CWE-862: Missing Authorization (4.20) | https://cwe.mitre.org/data/definitions/862.html | §14 senaryo 6-10 (denetimsiz rota) | doğrulandı (2026-10-13, HTTP 200) |
| 2 | CWE-863: Incorrect Authorization (4.20) | https://cwe.mitre.org/data/definitions/863.html | §11 satır 7 (katı rol eşitliği) · §13 | doğrulandı (2026-10-13, HTTP 200) |
| 3 | CORS - Glossary \| MDN | https://developer.mozilla.org/en-US/docs/Glossary/CORS | §5 başlık yayımı (CorsMiddleware) | doğrulandı (2026-10-13, HTTP 200) |

| Eşleme Kuralı | Açıklama |
|---|---|
| Eşleme ≠ bulgu | CWE satırı "açık var" demez; hangi senaryonun hangi sınıfa girdiğini gösterir |
| 862 vs 863 | yetkinin OLMAMASI (862) ile YANLIŞ VERİLMESİ (863) ayrılır |
| CORS | kök-domain politikası; `*` + credentials yasağı (§5 kanıtlı) |
| Tahsis | Bu 3 URL yalnız bu dosyada geçer |
| Başlık doğrulaması | resmi `<title>` metni 2026-10-13 GET ile alındı |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §16 DENETİM TESTİ TASLAĞI (BU DOSYADAKİ KOD İÇİN)

| # | Test senaryosu | Arrange | Assert | Kaynak § |
|---|---|---|---|---|
| T-1 | rol yetmiyorsa 403 | `_auth.role=user` · `requiredRole=admin` | `httpStatus=403` · `error=role_insufficient` | §11-§13 |
| T-2 | izin yoksa 403 | `permissions=[]` · `requiredPermission=x` | `error=permission_denied` | §11-§13 |
| T-3 | izin varsa geçiş | `permissions=[x]` | `$next` çağrılır | §11 |
| T-4 | permissions string ise red | `permissions="x"` | 403 (fail-closed) | §14-5 |
| T-5 | `_auth` yoksa pas geçiş | boş auth | `$next` çağrılır | §14-9 |
| T-6 | red logu üretiliyor mu | senaryo T-1 | `authz_denied` olayı | §12 |
| T-7 | logda token/parola yok | T-1 log çıktısı | redaksiyon | §12 |
| T-8 | rota meta'sız istek | rota tanımsız | davranış kaydı (senaryo 10) | §14-10 |
| T-9 | katı eşitlik (rol `Admin` vs `admin`) | farklı büyük/küçük harf | 403 | §11-7 |
| T-10 | API katmanı ile tutarlılık | aynı yetkisiz istek (API) | 403 gövde anahtarı eşleşmesi | §13 |

| Test Kuralı | Açıklama |
|---|---|
| Bu § TASLAKTIR | test dosyası yazılmadı; `shared/tests/Middleware/` içinde doğrulanacak |
| Satır numarası | üretim kodundan; test dosyası için uydurma yok |
| Kaynak | §11'deki 64 satırlık kod okuması |
| Sahip | qa-engineer (yazım) · security-engineer (onay) |
| Bağ | `security-test-stratejisi.md` §3 |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §17 BÖLÜM KAPANIŞI (2026-10-13)

| Kontrol | Beklenen | Durum |
|---|---|---|
| Eklenen § | §11..§17 | EVET |
| Mevcut §1-§10 | silinmedi | EVET |
| Yeni kod okuması | diskten | PermissionMiddleware.php 64 satır |
| Yeni URL (HEAD 200 + başlık) | 3 benzersiz | EVET |
| URL tekrarı | 0 | EVET |
| Uydurma kod/satır | 0 | EVET |
| Test dosyası değişikliği | YOK | EVET (yalnız taslak §16) |
| `git commit` | ATILMADI | EVET |

| Son Durum | |
|---|---|
| Dosya | authorization-ornekler.md · group: kod |
| Ek bölüm | §11..§17 |
| Kaynak | PermissionMiddleware.php (64 satır) · 3 URL |
| Commit | ATILMADI (yasak) |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**


## §18 ROTA META YETKİ SÖZLEŞMESİ (requiredRole / requiredPermission)

> Kaynak: §11'deki kod okuması (`PermissionMiddleware.php` satır 19 · 30 · 36). Bu §, rotayı tanıyan tarafın (route registry) uyması gereken sözleşmeyi yazar; yeni kod İÇERMEZ.

| Meta anahtarı | Tip | Zorunluluk | Yoksa davranış | Sahip |
|---|---|---|---|---|
| `requiredRole` | string | korumalı rota için ZORUNLU | rol denetimi pas geçer | route registry |
| `requiredPermission` | string | izin bazlı rota için ZORUNLU | izin denetimi pas geçer | route registry |
| ikisi birden | string | ZORUNLU DEĞİL | ikisi de uygulanır | route registry |
| hiçbiri | — | yalnız public rota | denetimsiz akış (§14-10) | route registry |

| Sözleşme Kuralı | Seviye | Açıklama |
|---|---|---|
| Public rota bilinçli ilan edilir | ZORUNLU | meta yokluğu "public" demek değildir; rota listesinde işaretlenir |
| Rol değeri tam yazılır | ZORUNLU | `admin` ≠ `Admin` (katı eşitlik, §11-7) |
| Rol hiyerarşisi yoktur | ZORUNLU | basamak gerekiyorsa izin (`requiredPermission`) kullanılır |
| İzin adı kaynaklıdır | ZORUNLU | izin listesi kimlik katmanından gelir (K010 sınırı) |
| Yeni korumalı rota | ZORUNLU | route registry + test birlikte eklenir |
| Meta silinmesi | YASAK | sessiz genişleme = CWE-862 sınıfı |
| Meta okunması | ZORUNLU | yalnız PermissionMiddleware okur (satır 19) |
| API rotaları | ZORUNLU | `Api/Middleware/AuthorizationMiddleware` ayrı katman (§6/§7) |

| Devreye Alma Sırası (yeni rota) | |
|---|---|
| 1 | rota route registry'ye yazılır |
| 2 | `requiredRole` / `requiredPermission` atanır |
| 3 | §16 test taslağındaki ilgili senaryo eklenir |
| 4 | public ise gerekçe kaydı yazılır |
| 5 | güvenlik gözden geçirmesi (security-engineer) |

| Açık Soru | Durum |
|---|---|
| Rotaların kaçta kaçı `requiredRole` taşıyor? | ⚠️ VERIFICATION REQUIRED (route registry okunmadı) |
| Public rota listesi nerede tutuluyor? | UNKNOWN |
| API rota meta'sı aynı anahtarları mı kullanıyor? | ⚠️ VERIFICATION REQUIRED |
| Rol/izin adları K010'da mı tanımlanıyor? | K010 sınırı — devir |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §19 BÖLÜM KAPANIŞI — ROL, TETİKLEYİCİ, ÖLÇÜM (2026-10-13)

| Kalem | Değer |
|---|---|
| Eklenen bölümler | §11..§19 |
| Silinen içerik | 0 (append-only) |
| Yeni kod okuması | `shared/src/Middleware/PermissionMiddleware.php` — 64 satır, diskten |
| Yeni doğrulanmış URL | 3 (CWE-862 · CWE-863 · MDN CORS Glossary) — HEAD 200 + resmi başlık, 2026-10-13 |
| Uydurma kod/satır | 0 |
| Test dosyası değişikliği | 0 (yalnız taslak §16) |
| `git commit` | ATILMADI (yasak) |

| Rol | Bu dosyada sorumluluk |
|---|---|
| security-engineer (yazım) | §11-§15 · §19 · veto denetimi |
| backend-architect | §18 rota meta sözleşmesi · route registry |
| qa-engineer | §16 test taslağını uygular |
| research-analyst | §15 URL doğrulama kaydı |
| Vault Steward | kapanış onayı (A) |

| Tetikleyici | Etkilenen § | Aksiyon |
|---|---|---|
| `PermissionMiddleware.php` değişimi | §11-§14 | satır numaraları yeniden ölçülür |
| Rota meta anahtarı değişimi | §18 | sözleşme yeniden yazılır |
| Rol/izin modeli değişimi (K010) | §13 · §18 | devir kaydı güncellenir |
| `Api/.../AuthorizationMiddleware` değişimi | §6 · §13 | iki katman 403 tutarlılığı yeniden ölçülür |
| Log/redaksiyon kuralı değişimi | §12 | URI logu yeniden denetlenir |
| Test ekleme (`shared/tests/Middleware/`) | §16 | taslak satırları gerçek testlere bağlanır |
| Yeni CWE kaynağı | §15 | yalnız HEAD 200 + başlık ile eklenir |

| Açık Soru Defteri (bu dosya) | Sahip | Durum |
|---|---|---|
| Route registry'de `requiredRole` kapsaması | backend-architect | ⚠️ VERIFICATION REQUIRED |
| Public rota listesi yeri | backend-architect | UNKNOWN |
| API ile web 403 gövde tutarlılığı | qa-engineer | ⚠️ VERIFICATION REQUIRED |
| `REQUEST_URI` logunda hassas parametre redaksiyonu | security-engineer | ⚠️ VERIFICATION REQUIRED |
| `REMOTE_ADDR` proxy arkasında gerçek IP | K014-network | UNKNOWN |
| Test dosyalarının mevcut kapsamı | qa-engineer | UNKNOWN (§9 kaydı korunur) |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §20 MIDDLEWARE BORU HATTI SIRASI VE PERMISSION'IN YERİ (2026-10-13)

> Kaynak: L1 Security kural seti (donmuş boru hattı) + diskten okunan `BypassAuthMiddleware.php` (66 satır). Bu §, §11'deki PermissionMiddleware'in boru hattı içindeki komşularını yazar; yeni kod İÇERMEZ.

| Sıra | Middleware | Permission üzerindeki etkisi |
|---|---|---|
| 1 | OriginCheck | yetkiye ulaşmadan reddeder (§3) |
| 2 | Cors | başlık yayımı (§5) |
| 3 | RateLimiter | A07 kötüye kullanımı sınırlar |
| 4 | SecurityHeaders | katkısız |
| 5 | SessionManager | kimlik oturumu hazırlar |
| 6 | Csrf | mutasyon isteklerini damgalar |
| 7 | BypassAuth | devre dışıysa Auth'a sahte kimlik geçirmez (aşağıda) |
| 8 | Auth | 401 üretir; Permission'a kimliği verir |
| 9 | **Permission** | §11 akışı — `requiredRole` / `requiredPermission` |
| 10 | Validation | girdi doğrulama — yetkiden SONRA |

| BypassAuth fail-closed kanıtı (kod okuması) | Satır | Davranış |
|---|---|---|
| bypass kontrolü | 23-34 | anahtar yoksa/üretilemiyorsa pas geçer (fail-closed) |
| oturum yazımı | 49-54 | yalnız gerçek oturum verisiyle |
| `_auth` birleştirme | 56-61 | sahte kimlik mevcut kimliği EZMEZ |

| Kural | Seviye | Gerekçe |
|---|---|---|
| BypassAuth production'da daima kapalı | ZORUNLU (L1 kural 13) | açık bypass → Permission atlanır |
| Permission, Auth'dan önce koşmaz | ZORUNLU | kimliksiz rol denetimi anlamsız (§11-1) |
| Validation yetkiden önce koşmaz | ZORUNLU | doğrulama hatası yetki sızıntısı üretmemeli |
| Boru hattı sırası değiştirilmez | YASAK | donmuş sıra (ADR) — değişiklik ADR ister |
| Yeni middleware eklenirse sıra kaydı güncellenir | ZORUNLU | §19 tetikleyici tablosu |

| Açık Soru | Durum |
|---|---|
| Boru hattı kaydı hangi dosyada tanımlı? | ⚠️ VERIFICATION REQUIRED (kayıt dosyası okunmadı) |
| BypassAuth production anahtarı hangi vault'ta? | UNKNOWN (`.env` kapsam dışı) |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**
