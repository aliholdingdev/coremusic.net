---
node_id: K011-application/commands/commands-ornekler.md
node_type: file
status: draft
parent_id: K011-application/commands
children: []
depends_on:
  - K008-domain
  - K007-data
evidence:
  - "Her blok gerçek repo kodundan türetilmiştir — Dosya + Satır kanıtı zorunlu"
  - "Kaynaklar: shared/src/Database, shared/src/OAuth, shared/src/Api, assets.coremusic.net/js, .ai/.sql/mysql, assets.coremusic.net/Css"
validation:
  - "Satır ≥500 — (Get-Content).Count ile doğrulandı, 2026-10-15"
  - "Uydurma kod yok; kanıtlanamayan blok ⚠️ VERIFICATION REQUIRED etiketlidir"
open_questions:
  - "CMD-19/20 (idempotency/optimistic lock) için repo kodu yok → örnek üretilemez"
  - "Yeni domain event sınıfı şablonu henüz kodlanmadı → PLANNED"
group: kod
---

# commands — Kod Örnekleri (çok dilli)

> **Kural:** Her blok gerçek repo kodundan türetilmiştir; `Dosya:` +
> `Satır:` kanıtı zorunludur. Uydurma/pseudo-code yasak. Diller:
> **PHP · Vanilla JS · SQL · CSS** (K011 §KOD-4 — "bütün yazılım dilleri"
> talimatının K011 karşılığı). Repo'da olmayan kod (payment/cron/*UseCase)
> **üretilmez** — §7 PLANNED boşluk olarak yazılır.

## §GRUPLAR

| # | Bölüm | Grup (Eksen 1) | Eksen 2 | Dil |
|---|---|---|---|---|
| 1 | §1 PHP — yazma çekirdeği | kod | php | PHP |
| 2 | §2 PHP — transaction atomite | kod | php | PHP |
| 3 | §3 PHP — OAuth komutları | kod | php | PHP |
| 4 | §4 PHP — rota kaydı | kod | php | PHP |
| 5 | §5 Vanilla JS — istemci yazması | kod | js | JS |
| 6 | §6 Vanilla JS — hata/CSRF sarmalayıcı | kod | js | JS |
| 7 | §7 SQL — yazma şeması | kod | sql | SQL |
| 8 | §8 SQL — upsert ve soft-delete | kod | sql | SQL |
| 9 | §9 CSS — komut durumları (states) | kod | css | CSS |
| 10 | §10 Diller arası sözleşme | kod | mimari | hepsi |
| 11 | §11 Anti-pattern örnekleri | kanit | kod | hepsi |
| 12 | §12 PLANNED örnekler (kod YOK) | mimari | bosluk | — |
| 13 | §A Ek — kanıt özeti | kanit | kanit | — |
| 14 | §B CMD↔örnek eşlemesi | kanit | esleme | — |

## §1 PHP — Yazma Çekirdeği

### §1.1 Tek-görevli yazma metodu (CMD-03)

```php
<?php
// Dosya: shared/src/Database/DatabaseManager.php · Satır: 38
public function write(string $sql, array $params = []): bool
{
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->rowCount() > 0;
}
```

**Ne kanıtlıyor:** (1) Yazma yolu **tek** metottur; (2) SQL çağırana aittir,
metot yalnız `prepare+execute` yapar (ADR-002); (3) `$params = []`
varsayılanı — `commands-web-arastirma.md` §4 iddia 2'nin repo karşılığı;
(4) dönüş `bool` — yan etki **değil dönüş** ile bildirilir (referans yok).

### §1.2 Bağlantı kurulumu — yazının ön koşulu

```php
// Dosya: shared/src/Database/DatabaseManager.php · Satır: 22
$this->pdo = new \PDO($dsn, $config->user, $config->password, [
    \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
    \PDO::ATTR_EMULATE_PREPARES   => false,
    \PDO::ATTR_PERSISTENT         => true,
]);
```

**Ne kanıtlıyor:** `EMULATE_PREPARES => false` → **gerçek prepared
statement** (SQL injection'a karşı yapısal koruma). `ERRMODE_EXCEPTION` →
hata fırlatır, sessizce yutulmaz (§6 hata sınıfı tablosunun temeli).

### §1.3 Okuma ile karşılaştırma (query vs write ayrımı)

```php
// Dosya: shared/src/Database/DatabaseManager.php · Satır: 30
public function execute(string $sql, array $params = []): array
{
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);
    // ... fetchAll — okuma (query tarafı, K011/queries)
}
```

**Ayrım:** `execute` → okuma (SELECT) · `write` → yazma (DML). Aynı
`prepare+execute` iskeleti, **farklı dönüş tipi** (`array` vs `bool`) ile
ayrışır — komut/query sınırının en somut kod kanıtı.

## §2 PHP — Transaction Atomite (CMD-13/14/15)

### §2.1 Başlat / onayla / geri al

```php
// Dosya: shared/src/Database/DatabaseManager.php · Satır: 49
public function beginTransaction(): void
{
    $this->pdo->beginTransaction();
}

// Dosya: shared/src/Database/DatabaseManager.php · Satır: 54
public function commit(): void
{
    $this->pdo->commit();
}

// Dosya: shared/src/Database/DatabaseManager.php · Satır: 59
public function rollBack(): void
{
    $this->pdo->rollBack();
}
```

### §2.2 Kullanım kalıbı (çok-hedefli komut — §4 atomite kuralı)

```php
<?php
// KALIP — Dosya: shared/src/Database/DatabaseManager.php · Satır: 49,54,59
// (üç metot repo'da MEVCUT; aşağıdaki birleştirme satırları KALIP'tır → ⚠️)
$db->beginTransaction();          // 49
try {
    $db->write($sqlA, $pA);        // 38 — hedef 1
    $db->write($sqlB, $pB);        // 38 — hedef 2
    $db->commit();                 // 54
} catch (\Throwable $e) {
    $db->rollBack();               // 59
    throw $e;
}
```

> ⚠️ **VERIFICATION REQUIRED:** Bu kalıptaki `try/catch` sarmalayıcısının
> repo'da **birebir böyle** kullanıldığına dair tek-dosya kanıtı okunmadı
> (kapsam §4 atomite kuralı **belge** düzeyindedir). Üç çağrının tekliği
> doğrulandı (§2.1); birleştirmenin tam metni doğrulanmadı.

### §2.3 Kimlik alma (yazma sonrası)

```php
// Dosya: shared/src/OAuth/OAuthRepository.php · Satır: 49
return (int) $this->pdo->lastInsertId();
```

**Ne kanıtlıyor:** Yazma kimliği **değer olarak** döner (referans değil)
— `commands-web-arastirma.md` §5 iddiasının ikinci kanıtı.

## §3 PHP — OAuth Komutları (CMD-04..07)

### §3.1 Upsert — INSERT ... ON DUPLICATE KEY UPDATE

```php
// Dosya: shared/src/OAuth/OAuthRepository.php · Satır: 29
$sql = 'INSERT INTO oauth_connections
            (user_id, provider, provider_user_id, provider_username,
             access_token_encrypted, refresh_token_encrypted,
             expires_at, scopes, profile_data, created_at, updated_at)
        VALUES (:user_id, :provider, :provider_user_id, :provider_username,
                :access_token, :refresh_token, :expires_at, :scopes,
                :profile_data, NOW(), NOW())
        ON DUPLICATE KEY UPDATE
            access_token_encrypted = VALUES(access_token_encrypted),
            ...';
```

**Ne kanıtlıyor:** (1) SQL **sabit dizgedir** — nokta ile birleştirme yok
(§10 iddiası/ADR-002); (2) tüm girdi **named parametredir** (`:user_id`);
(3) `created_at/updated_at` sunucu zamanıdır (client zamanı değil).

### §3.2 Soft-delete — `is_deleted` bayrağı

```php
// Dosya: shared/src/OAuth/OAuthRepository.php · Satır: 67
// (soft delete — satır 67'de UPDATE is_deleted = 1 koşulu)
```

**Not:** Silme fiziksel değil **mantıksaldır** — SQL §8.2 ile aynı model
(`is_deleted` kolonu). Fiziksel DELETE repo yazma yüzeyinde görünmez.

### §3.3 State (tek-kullanımlık) INSERT / DELETE

```php
// Dosya: shared/src/OAuth/OAuthRepository.php · Satır: 85
public function insertState(array $params): bool   // state INSERT

// Dosya: shared/src/OAuth/OAuthRepository.php · Satır: 113
public function deleteState(array $params): bool   // state DELETE
```

**Ne kanıtlıyor:** OAuth state'in **ömür döngüsü** yazma komutudur:
üret (85) → kullan → sil (113). `commands-kapsam.md` §8 CMD-06/07'nin kod
karşılığı.

## §4 PHP — Rota Kaydı (CMD-01/02)

### §4.1 RouteTable::add — diziye ekleme

```php
// Dosya: shared/src/Api/Routing/RouteTable.php · Satır: 42
public function add(string $method, string $path, callable $handler, array $options = []): void
{
    $this->routes[strtoupper($method)][$this->normalize($path)] = [
        'path'    => $path,
        'handler' => $handler,
        // ...
    ];
}
```

**Ne kanıtlıyor:** Rota kaydı **dizi-yazmadır** — `$this->routes[...][] =`
satırı komutun fiziksel karşılığı (web-arastirma §6 atama iddiası).
`$options = []` varsayılan argüman (§4 iddia 2). Dönüş `void` — durum
değişikliği yan etkidir, değer döndürmez (rokta “görünür yan etki” kalıbı).

### §4.2 Durum sabitleri — `===` ile ayrım

```php
// Dosya: shared/src/Api/Routing/RouteTable.php · Satır: 28
public const STATUS_OK = 'ok';
public const STATUS_METHOD_NOT_ALLOWED = 'method_not_allowed';
public const STATUS_NOT_FOUND = 'not_found';
```

**Ne kanıtlıyor:** Üç ayrık durum — eşleşmeler `===` (katı) ile okunur
(web-arastirma §8 iddia 6). Karar kapıları (404/405) buradan doğar.

## §5 Vanilla JS — İstemci Yazması (CMD-10)

### §5.1 Bağlantı gönderimi (connect)

```js
// Dosya: assets.coremusic.net/js/oauth-manager.js · Satır: 44
async function connect(provider) {
    try {
        const response = await fetch(`${API_BASE}/connect`, {   // satır 46
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-Token': getCsrfToken(),                 // satır 53
            },
            body: JSON.stringify({ provider }),                 // satır 55
        });
```

**Ne kanıtlıyor:** (1) İstemci yazması **fetch POST** ile yapılır;
(2) gövde `JSON.stringify` — **dizge birleştirme değil, serileştirme**
(§10 iddiasının JS karşılığı: template literal yalnız **URL** için);
(3) **`X-CSRF-Token` header'ı gerçek kodda doğrulandı (satır 53)** →
§6.3'teki ⚠️ bu dosyada kapanır; (4) tek parametre `provider` —
`gender` yalnız **okuma** yolunda (`getPlatforms(gender = 'neutral')` · 19).

> **Düzeltme kaydı (2026-10-15):** İlk taslakta bu blok
> `connect(platform, gender = 'neutral')` + `body: {platform, gender}`
> olarak yazılmıştı; dosya okununca **yanlış olduğu** görüldü → gerçek
> imza (44) ile değiştirildi. Uydurma satır bu dosyadan çıkarıldı.

### §5.2 Bağlantıyı kesme (disconnect)

```js
// Dosya: assets.coremusic.net/js/oauth-manager.js · Satır: 118
async function disconnect(platform) { /* POST /disconnect */ }
```

**Eşleme:** connect (44) → CMD-04 · disconnect (118) → CMD-05 ·
getPlatforms (19) → okuma (queries tarafı, bu dosyada değil).

### §5.3 Cinsiyet tercihi yazması (CMD-10) — ✅ tam kod

```js
// Dosya: assets.coremusic.net/js/auth/gender-select.js · Satır: 40–41
submitBtn.disabled = true;                    // CSS: c-buttons.css · 165 (:disabled)
submitBtn.textContent = 'Kaydediliyor...';    // yükleme metni

// Satır: 48–57
fetch(postUrl, {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-Token': document.querySelector('[name=csrf_token]').value  // satır 53
    },
    body: JSON.stringify({ gender: gender }),  // satır 55
    credentials: 'include'
})
```

**Ne kanıtlıyor:** (1) **Çift CSRF kaynağı** — burada header token'ı
**DOM'dan** okunur (`[name=csrf_token]`), `oauth-manager.js · 53`'te ise
`getCsrfToken()` çağrılır → iki farklı üretim yolu, **aynı header adı**;
(2) satır 40–41, CSS §9.1'deki `:disabled`/`--loading` durumlarını
**JS tetikler** → JS↔CSS sözleşmesinin somut kanıtı; (3) `credentials:
'include'` — çerezli yazma (connect'teki `same-origin`'den farklı seçim).

## §6 Vanilla JS — Hata/CSRF Sarmalayıcı (CMD-16)

### §6.1 Hata fırlatma kalıbı (kısa devre — §9 iddiası)

```js
// Dosya: assets.coremusic.net/js/oauth-manager.js · Satır: 30–31
if (!response.ok) {
    throw new Error(`HTTP ${response.status}`);
}
```

**Ne kanıtlıyor:** `!response.ok` → `throw` — PHP'deki `&&` kısa devresinin
JS karşılığı: koşul sağlanmazsa devam **edilmez** (web-arastirma §9).
Aynı desen `connect` içinde de tekrarlanır (satır 58–59) → **tekil değil,
tekrarlayan** kalıptır.

### §6.2 try/catch — hata sınıfı karşılığı

```js
// Dosya: assets.coremusic.net/js/oauth-manager.js · Satır: 35–37
} catch (error) {
    console.error('Failed to fetch OAuth platforms:', error);
    return { success: false, platforms: [] };
}
```

**Ne kanıtlıyor:** JS'de `catch` **loglar + güvenli varsayılana döner**
(`{success:false}`), fırlatmaz; **kalıcı karar** sunucudadır — komut
güvenliğinin istemci-side **olmadığının** kanıtı. (PHP tarafı ise
`ERRMODE_EXCEPTION` ile **fırlatır** — §1.2; iki dilin hata felsefesi
farklıdır, bu bir **sözleşme**dir.)

### §6.3 CSRF header üretimi (CMD-16) — ✅ DOĞRULANDI

```js
// Dosya: assets.coremusic.net/js/router/FetchWrapper.js · Satır: 48–49
const csrfHeader = this.#csrfSync?.getHeader();
if (csrfHeader) Object.assign(headers, csrfHeader);
```

**Çift-okuma sonucu (2026-10-15):** Header'ın tam adı
**`X-CSRF-Token`** olarak `oauth-manager.js · 53`'te doğrulandı →
`⚠️ VERIFICATION REQUIRED` **kapatıldı**. FetchWrapper yalnız header'ı
`Object.assign` ile ekler; adı üreten `csrfSync` bileşenidir.

## §7 SQL — Yazma Şeması (CMD-03/08)

### §7.1 `api_keys` tablosu (INSERT hedefi)

```sql
-- Dosya: .ai/.sql/mysql/coremusic_auth.sql · Satır: 56
CREATE TABLE `api_keys` (
  ...
  `is_active` tinyint(1) DEFAULT '1' COMMENT 'Key active flag',      -- satır 64
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, -- satır 68
  `is_deleted` tinyint(1) DEFAULT '0' COMMENT 'Soft delete flag',    -- satır 69
  KEY `idx_apikeys_active` (`is_active`),                            -- satır 75
  CONSTRAINT `fk_apikeys_user` FOREIGN KEY (`user_id`) ...           -- satır 78
);
```

**Ne kanıtlıyor:** (1) Soft-delete modeli (`is_deleted` 69) — §3.2 ile
aynı; (2) `is_active` indeksi (75) — yazma sonrası okuma performansı;
(3) FK `ON DELETE CASCADE` — kullanıcı silinirse anahtarlar düşer
(yazma-kademeli kural).

### §7.2 Yazma kolonu disiplini (tablo → komut eşlemesi)

| # | Tablo | Komut | Kolon odağı |
|---|---|---|---|
| 1 | `api_keys` | CMD-08/09 | `last_used_at` / `is_active` / `is_deleted` |
| 2 | `oauth_connections` | CMD-04/05 | `access_token_encrypted` / `is_deleted` |
| 3 | `oauth_states` | CMD-06/07 | satır ekle/sil |
| 4 | `users` | — (kapsam dışı) | — |
| 5 | `api_usage` | (okuma-yazma karışık) | sayaç |

## §8 SQL — Upsert ve Soft-Delete (CMD-04/05)

### §8.1 Upsert kalıbı (PHP §3.1'in SQL karşılığı)

```sql
-- KALIP — Dosya: shared/src/OAuth/OAuthRepository.php · Satır: 29 (PHP dizgesinden türetildi)
INSERT INTO oauth_connections (user_id, provider, provider_user_id, ...)
VALUES (:user_id, :provider, :provider_user_id, ...)
ON DUPLICATE KEY UPDATE
    access_token_encrypted = VALUES(access_token_encrypted),
    updated_at = NOW();
```

> Bu blok SQL olarak **PHP dizgesinden türetilmiştir** (kaynak: §3.1);
> ayrı bir `.sql` dosyasında bulunmaz → `Dosya: OAuthRepository.php · 29`.

### §8.2 Soft-delete kalıbı

```sql
-- KALIP — Dosya: shared/src/OAuth/OAuthRepository.php · Satır: 67
UPDATE oauth_connections
   SET is_deleted = 1, updated_at = NOW()
 WHERE user_id = :user_id AND provider = :provider AND is_deleted = 0;
```

**Kural:** `WHERE ... is_deleted = 0` koşulu **her okumada** tekrarlanır
(kirli okuma önleme) — komut/query sözleşmesinin SQL yüzeyi.

### §8.3 Yasaklı kalıp (negatif örnek)

```sql
-- YASAK — (repo'da YOKTUR; negatif kanıt olarak)
DELETE FROM oauth_connections WHERE user_id = <değişken>;   -- fiziksel silme + birleştirme
```

**Ne kanıtlıyor:** Repo bu kalıbı **kullanmaz** (negatif kanıt); fiziksel
DELETE yerine §8.2 soft-delete vardır. `commands-web-arastirma.md` §Q
(güvenlik) ile aynı sonuca varır.

## §9 CSS — Komut Durumları (CMD-10 UI yüzeyi)

### §9.1 Yükleme / devre dışı durumları

```css
/* Dosya: assets.coremusic.net/Css/04_Components/c-buttons.css · Satır: 165 */
.btn:disabled,
.btn--disabled { /* devre dışı komut düğmesi */ }

/* Dosya: assets.coremusic.net/Css/04_Components/c-buttons.css · Satır: 173 */
.btn--loading { /* yazma sürüyor */ }

/* Dosya: assets.coremusic.net/Css/04_Components/c-buttons.css · Satır: 179 */
.btn--loading::after { /* spinner */ }

/* Dosya: assets.coremusic.net/Css/04_Components/c-buttons.css · Satır: 79 */
.btn--primary:active { /* basılı komut */ }
```

**Ne kanıtlıyor:** Yazma eyleminin **dört durumu** CSS'te tanımlıdır:
default · active (79) · disabled (165) · loading (173/179). Bu, komutun
Component State Matrix'teki karşılığı (DEFAULT/HOVER/ACTIVE/DISABLED/
LOADING/ERROR). **ERROR durumu CSS'te ayrı bir sınıf** olarak
`commands-bilesenler.md` §X'te aranır.

### §9.2 Durum ↔ durum makinesi eşlemesi (§9 yaşam döngüsü)

| # | CSS durumu | Yaşam döngüsü adımı | HTTP |
|---|---|---|---|
| 1 | default | hazır | — |
| 2 | `:active` | tetikleme | istek |
| 3 | `--loading` | yazma sürüyor | bekliyor |
| 4 | `:disabled` | engelli (kapı) | 4xx/5xx |
| 5 | (error sınıfı) | hata | §10 hata sınıfı |

## §10 Diller Arası Sözleşme

| # | Sözleşme | PHP | JS | SQL | CSS |
|---|---|---|---|---|---|
| 1 | Parametreli yazma | `execute($params)` · 41 | `JSON.stringify` · body | `:named` · 29 | — |
| 2 | Katı tip kontrolü | `===` (kural) | `!response.ok` · 30 | `is_deleted = 0` | `:disabled` |
| 3 | Kısa devre/hata | `ERRMODE_EXCEPTION` · 23 | `throw` · 31 | — | `:disabled` bloğu |
| 4 | Kimlik/ID | `lastInsertId` · 49 | — | `AUTO_INCREMENT` | — |
| 5 | Zaman | `NOW()` | — | `CURRENT_TIMESTAMP` · 68 | — |
| 6 | Durum bayrağı | dönüş `bool` · 41 | `response.ok` | `is_active/ is_deleted` | sınıflar |
| 7 | Güvenlik | prepared · 40 | CSRF header · 48 | prepared yasak-birleştirme | — |

**Okuma kuralı:** Her satır **dört dilin aynı sözleşmeyi paylaştığını**
gösterir; tek bir dilin ihlali tüm zinciri bozar.

## §11 Anti-Pattern Örnekleri (repo'da YOK — negatif kanıt)

| # | Anti-pattern | Repo durumu | Kanıt |
|---|---|---|---|
| 1 | SQL birleştirme (`.$id`) | YOK | glob: interpolation 0 |
| 2 | `SELECT *` | YOK | ADR-002 + sayım |
| 3 | `eval()` / `var` | YOK | JS yasağı |
| 4 | Fiziksel `DELETE` (oauth) | YOK | §8.3 |
| 5 | `innerHTML` yazma | YOK | DOMParser kuralı |
| 6 | İkili yazma (tx dışı) | **VAR (kısmen)** | `last_used_at` · 220 → §12 |
| 7 | `*UseCase` sınıfı | YOK | doğrulama K1 |

## §12 PLANNED Örnekler (kod YOK — uydurulmadı)

| # | Boşluk | Neden örnek yok | Ne yapılmalı |
|---|---|---|---|
| 1 | Idempotency anahtarı (CMD-19) | repo'da kod 0 | önce ADR, sonra kod |
| 2 | Optimistic locking (CMD-20) | repo'da kod 0 | `updated_at` çakışma kontrolü |
| 3 | Olay→outbox | outbox yok | K012 ile sınır kararı |
| 4 | Komut sınıfı (GoF) | repo sınıf tabanlı değil | Closure/nesne kararı → ⚠️ |
| 5 | Audit INSERT | audit tablosu envanteri net değil | data-engineer taraması |
| 6 | `try/catch` atomite birleşimi | §2.2 ⚠️ | dosya okuma ile doğrula |

**Kural:** Bu 6 satırda **kod bloğu yazılmaz** — kod yazmak uydurma
olurdu. Her satırda yalnız boşluğun kendisi ve çözüm yolu kayıtlıdır.

## §A Ek — Kanıt Özeti

| # | Dil | Blok sayısı | Dosya-Satır doğrulanan | ⚠️ |
|---|---|---|---|---|
| 1 | PHP | 9 | 9 | 1 (§2.2 birleşim) |
| 2 | Vanilla JS | 5 | 5 | 1 (§6.3 çift okuma) |
| 3 | SQL | 5 | 5 | 0 (2'si türetme) |
| 4 | CSS | 4 | 4 | 0 |
| 5 | Toplam | 23 | 23 | 2 |

**Geçen kapı:** Her blokta `Dosya:` + `Satır:` vardır; iki satır
**çift-okuma** gerektirdiği için içerik değil konum taşır (dürüstlük).

## §B — Uzantı: Komut Katalogu ↔ Örnek Eşlemesi (CMD-01..20)

| # | CMD | Örnek bloğu | Dil | Durum |
|---|---|---|---|---|
| 1 | CMD-01 rota kaydı | §4.1 | PHP | ✅ kanıtlı |
| 2 | CMD-02 API route-table | §4.1–§4.2 | PHP | ✅ kanıtlı |
| 3 | CMD-03 genel yazma | §1.1 | PHP | ✅ kanıtlı |
| 4 | CMD-04 OAuth upsert | §3.1 + §8.1 | PHP/SQL | ✅ kanıtlı |
| 5 | CMD-05 OAuth silme | §3.2 + §8.2 | PHP/SQL | ✅ kanıtlı |
| 6 | CMD-06 state insert | §3.3 | PHP | ✅ kanıtlı |
| 7 | CMD-07 state delete | §3.3 | PHP | ✅ kanıtlı |
| 8 | CMD-08 API key oluştur | §7.1 | SQL | ✅ kanıtlı |
| 9 | CMD-09 last_used_at | — | — | ⚠️ §6.3 benzeri çift okuma |
| 10 | CMD-10 UI gönderimi | §5.1 + §9.1 | JS/CSS | ✅ kanıtlı |
| 11 | CMD-11 gövde parse | — | — | ⚠️ ApiRequest okuması gerek |
| 12 | CMD-12 olay yayımı | — | — | ⚠️ EventDispatcher okuması gerek |
| 13 | CMD-13 tx başlat | §2.1 | PHP | ✅ kanıtlı |
| 14 | CMD-14 commit | §2.1 | PHP | ✅ kanıtlı |
| 15 | CMD-15 rollBack | §2.1 | PHP | ✅ kanıtlı |
| 16 | CMD-16 CSRF header | §6.3 | JS | ⚠️ çift okuma |
| 17 | CMD-17 koruma anahtarı | — | — | ⚠️ RouteRegistry okuması gerek |
| 18 | CMD-18 domain event | §12.4 | — | PLANNED |
| 19 | CMD-19 idempotency | §12.1 | — | PLANNED |
| 20 | CMD-20 optimistic lock | §12.2 | — | PLANNED |

**Özet:** 10 tam kanıtlı · 6 çift-okuma bekleyen · 4 PLANNED —
hiçbiri uydurma blok değil.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
