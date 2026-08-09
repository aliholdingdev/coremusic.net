---
type: architecture
category: contracts
title: "API SDK Generation Strategy"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# API SDK Üretim Stratejisi

**Zorunlu Bağlantılar:** [[api-architecture-master]] · [[api-design-rules]] · [[api-public-contract]] · [[api-internal-contract]]

---

## 1. Amaç

CoreMusic API'sinden otomatik olarak üretilecek SDK'ların (Software Development Kit) stratejisini, standartlarını ve dağıtım yöntemlerini tanımlayan **Tek Doğruluk Kaynağıdır (SSOT)**. OpenAPI spesifikasyonundan tek bir kaynaktan çoklu dil için SDK üretilir.

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| OpenAPI tabanlı SDK üretimi | Manuel SDK yazımı |
| PHP, JavaScript, Python desteği | Diğer diller (planlanan) |
| SDK versiyonlama ve dağıtım | API backend kodu |
| Tip güvenli yanıt nesneleri | UI/frontend kodu |
| SDK test ve dokümantasyonu | Veritabanı şemaları |

---

## 3. SDK Üretim Akışı

```
┌─────────────────┐     ┌──────────────────┐     ┌─────────────────┐
│   OpenAPI 3.1   │────▶│  codegen Config  │────▶│  SDK Üreteci    │
│   Spec (YAML)   │     │  (per language)  │     │  (Automated)    │
└─────────────────┘     └──────────────────┘     └─────────────────┘
                                                          │
                        ┌──────────────────┐              │
                        │   Dil Bazlı      │◀─────────────┘
                        │   Paket Üretimi  │
                        └────────┬─────────┘
                                 │
              ┌──────────────────┼──────────────────┐
              ▼                  ▼                  ▼
        ┌──────────┐      ┌──────────┐      ┌──────────┐
        │  PHP     │      │   JS     │      │  Python  │
        │ Composer │      │   npm    │      │  PyPI    │
        └──────────┘      └──────────┘      └──────────┘
              │                  │                  │
              ▼                  ▼                  ▼
        ┌──────────┐      ┌──────────┐      ┌──────────┐
        │  Test    │      │  Test    │      │  Test    │
        └──────────┘      └──────────┘      └──────────┘
              │                  │                  │
              ▼                  ▼                  ▼
        ┌──────────┐      ┌──────────┐      ┌──────────┐
        │  Yayın   │      │  Yayın   │      │  Yayın   │
        └──────────┘      └──────────┘      └──────────┘
```

---

## 4. Desteklenen Diller

| Dil | codegen Motoru | Paket | Dağıtım | Durum |
|-----|---------------|-------|---------|-------|
| PHP | `openapi-generator-php` | `coremusic/sdk-php` | Composer | ✅ Aktif |
| JavaScript | `openapi-generator-js` | `@coremusic/sdk-js` | npm | ✅ Aktif |
| Python | `openapi-generator-python` | `coremusic-sdk` | PyPI | ✅ Aktif |
| Java | `openapi-generator-java` | `coremusic-sdk-java` | Maven | Planlandı |
| Go | `openapi-generator-go` | `coremusic-sdk-go` | Go Modules | Planlandı |
| Ruby | `openapi-generator-ruby` | `coremusic-sdk-ruby` | RubyGems | Planlandı |

---

## 5. SDK İsimlendirme Konvansiyonu

### 5.1 Paket İsimleri

| Dil | Paket Adı | Namespace/Module |
|-----|-----------|------------------|
| PHP | `coremusic/sdk-php` | `CoreMusic\SDK` |
| JavaScript | `@coremusic/sdk-js` | `@coremusic/sdk` |
| Python | `coremusic-sdk` | `coremusic_sdk` |

### 5.2 Sınıf İsimlendirme

| Örnek | Format |
|-------|--------|
| `MediaClient` | `{Resource}Client` |
| `MediaListResponse` | `{Resource}ListResponse` |
| `MediaGetRequest` | `{Resource}GetRequest` |
| `MediaNotFoundException` | `{Resource}NotFoundException` |
| `PaginationOptions` | `{Feature}Options` |

### 5.3 Method İsimlendirme

| HTTP Method | SDK Method | Örnek |
|-------------|-----------|-------|
| `GET /media` | `media.list()` | `sdk.media.list()` |
| `GET /media/{id}` | `media.get(id)` | `sdk.media.get(123)` |
| `POST /media` | `media.create(data)` | `sdk.media.create({...})` |
| `PUT /media/{id}` | `media.update(id, data)` | `sdk.media.update(123, {...})` |
| `DELETE /media/{id}` | `media.delete(id)` | `sdk.media.delete(123)` |
| `POST /media/search` | `media.search(query)` | `sdk.media.search('rock')` |

---

## 6. SDK Versiyonlama

### 6.1 Versiyon Eşleme

SDK versiyonu API versiyonunu yansıtır:

| API Versiyonu | SDK Versiyonu | Breaking Change |
|---------------|---------------|-----------------|
| v1.0.0 | 1.0.0 | — |
| v1.1.0 | 1.1.0 | Yeni endpoint'ler |
| v1.1.1 | 1.1.1 | Bug fix |
| v2.0.0 | 2.0.0 | Breaking changes |

### 6.2 Versiyon Bump Kuralları

| Değişiklik | Bump | Örnek |
|------------|------|-------|
| Yeni endpoint (backward-compat) | Minor | 1.0.0 → 1.1.0 |
| Parametre ekleme (opsiyonel) | Minor | 1.0.0 → 1.1.0 |
| Parametre kaldırma | Major | 1.0.0 → 2.0.0 |
| Yanıt formatı değişikliği | Major | 1.0.0 → 2.0.0 |
| Bug fix | Patch | 1.0.0 → 1.0.1 |
| Güvenlik yaması | Patch | 1.0.0 → 1.0.1 |

### 6.3 Deprecation Desteği

```php
// SDK içinde deprecated method uyarısı
/** @deprecated Use media.search() instead */
public function mediaFind(string $query): MediaListResponse
{
    trigger_error('mediaFind() is deprecated. Use media.search() instead.', E_USER_DEPRECATED);
    return $this->media->search($query);
}
```

---

## 7. SDK Dağıtımı

### 7.1 PHP — Composer

```json
{
  "name": "coremusic/sdk-php",
  "version": "1.0.0",
  "require": {
    "php": ">=8.1",
    "guzzlehttp/guzzle": "^7.0",
    "ext-json": "*"
  },
  "autoload": {
    "psr-4": {
      "CoreMusic\\SDK\\": "src/"
    }
  }
}
```

**Yayın:** `composer publish` → Packagist

### 7.2 JavaScript — npm

```json
{
  "name": "@coremusic/sdk-js",
  "version": "1.0.0",
  "main": "dist/index.js",
  "types": "dist/index.d.ts",
  "engines": {
    "node": ">=18.0.0"
  }
}
```

**Yayın:** `npm publish` → npm registry

### 7.3 Python — PyPI

```toml
[project]
name = "coremusic-sdk"
version = "1.0.0"
requires-python = ">=3.10"
dependencies = [
    "httpx>=0.24.0",
    "pydantic>=2.0.0"
]
```

**Yayın:** `twine upload dist/*` → PyPI

---

## 8. Tip Güvenli Yanıt Nesneleri

### 8.1 PHP

```php
final readonly class MediaResponse
{
    public function __construct(
        public int $id,
        public string $title,
        public string $artist,
        public string $genre,
        public int $duration,
        public string $coverUrl,
        public \DateTimeImmutable $createdAt,
    ) {}
}

final class MediaListResponse
{
    /** @var MediaResponse[] */
    public array $data;
    public int $total;
    public int $page;
    public int $perPage;
    public bool $hasNext;
}
```

### 8.2 JavaScript (TypeScript)

```typescript
interface MediaResponse {
  id: number;
  title: string;
  artist: string;
  genre: string;
  duration: number;
  coverUrl: string;
  createdAt: string;
}

interface MediaListResponse {
  data: MediaResponse[];
  total: number;
  page: number;
  perPage: number;
  hasNext: boolean;
}
```

### 8.3 Python

```python
from pydantic import BaseModel
from datetime import datetime

class MediaResponse(BaseModel):
    id: int
    title: str
    artist: str
    genre: str
    duration: int
    cover_url: str
    created_at: datetime

class MediaListResponse(BaseModel):
    data: list[MediaResponse]
    total: int
    page: int
    per_page: int
    has_next: bool
```

---

## 9. Hata Yönetimi

### 9.1 SDK Hata Hiyerarşisi

```
CoreMusicSDKException (base)
├── AuthenticationException    (401)
├── AuthorizationException     (403)
├── NotFoundException           (404)
├── ValidationException        (422)
├── RateLimitException         (429)
├── ServerException            (500)
├── ConnectionException        (network)
└── TimeoutException           (timeout)
```

### 9.2 PHP Hata Örneği

```php
try {
    $media = $sdk->media->get(999);
} catch (NotFoundException $e) {
    echo "Media not found: " . $e->getMessage();
} catch (RateLimitException $e) {
    echo "Rate limited. Retry after: " . $e->getRetryAfter() . "s";
} catch (CoreMusicSDKException $e) {
    echo "SDK error: " . $e->getMessage();
}
```

### 9.3 JavaScript Hata Örneği

```javascript
try {
  const media = await sdk.media.get(999);
} catch (error) {
  if (error instanceof CoreMusic.NotFoundError) {
    console.log('Media not found');
  } else if (error instanceof CoreMusic.RateLimitError) {
    console.log(`Rate limited. Retry after: ${error.retryAfter}s`);
  }
}
```

---

## 10. Retry Mantığı (SDK İçinde)

### 10.1 Varsayılan Retry Politikası

| Parametre | Değer |
|-----------|-------|
| Max retry | 3 |
| Initial delay | 500ms |
| Backoff | Exponential (2x) |
| Max delay | 30s |
| Jitter | ±20% |
| Retryable hatalar | 429, 500, 502, 503, 504 |

### 10.2 PHP Retry Örneği

```php
$sdk = new CoreMusicSDK(
    apiKey: 'cm_pub_...',
    config: new SDKConfig(
        maxRetries: 3,
        initialDelay: 500,
        backoffMultiplier: 2.0,
        maxDelay: 30000,
        jitter: 0.2
    )
);
```

### 10.3 JavaScript Retry Örneği

```javascript
const sdk = new CoreMusicSDK({
  apiKey: 'cm_pub_...',
  maxRetries: 3,
  initialDelay: 500,
  backoffMultiplier: 2.0,
  maxDelay: 30000,
  jitter: 0.2
});
```

---

## 11. Kimlik Doğrulama Yardımcıları

### 11.1 PHP

```php
// API Key ile
$sdk = new CoreMusicSDK(apiKey: 'cm_pub_...');

// Header özelleştirme
$sdk = new CoreMusicSDK(
    apiKey: 'cm_pub_...',
    headers: [
        'X-Custom-Header' => 'value'
    ]
);
```

### 11.2 JavaScript

```javascript
// API Key ile
const sdk = new CoreMusicSDK({
  apiKey: 'cm_pub_...'
});

// Header özelleştirme
const sdk = new CoreMusicSDK({
  apiKey: 'cm_pub_...',
  headers: {
    'X-Custom-Header': 'value'
  }
});
```

### 11.3 Python

```python
# API Key ile
sdk = CoreMusicSDK(api_key="cm_pub_...")

# Header özelleştirme
sdk = CoreMusicSDK(
    api_key="cm_pub_...",
    headers={"X-Custom-Header": "value"}
)
```

---

## 12. Sayfalama Yardımcıları

### 12.1 PHP

```php
// Tek sayfa
$response = $sdk->media->list(page: 1, perPage: 20);

// Tümünü iterate et
foreach ($sdk->media->listAll() as $media) {
    echo $media->title;
}

// Async iterator
async foreach ($sdk->media->listAllAsync() as $media) {
    echo $media->title;
}
```

### 12.2 JavaScript

```javascript
// Tek sayfa
const response = await sdk.media.list({ page: 1, perPage: 20 });

// Async iterator
for await (const media of sdk.media.listAll()) {
  console.log(media.title);
}
```

### 12.3 Python

```python
# Tek sayfa
response = sdk.media.list(page=1, per_page=20)

# Generator iterator
for media in sdk.media.list_all():
    print(media.title)

# Async
async for media in sdk.media.list_all_async():
    print(media.title)
```

---

## 13. SDK Test

### 13.1 Test Tipleri

| Test Tipi | Amaç | Kapsam |
|-----------|------|--------|
| Unit Test | Tek method testleri | Mock API yanıtları |
| Integration Test | Gerçek API çağrısı | Test API key |
| Snapshot Test | Yanıt formatı değişikliği | Schema uyumluluğu |
| E2E Test | Tam akış | Test ortamı |

### 13.2 Test Coverage Hedefi

| Dil | Minimum | Hedef |
|-----|---------|-------|
| PHP | %80 | %90 |
| JavaScript | %80 | %90 |
| Python | %80 | %90 |

### 13.3 Mock Servis

```php
// PHP Unit Test
$mockClient = new MockHttpClient([
    new MockResponse('{"data": [], "total": 0}')
]);
$sdk = new CoreMusicSDK(client: $mockClient);
$response = $sdk->media->list();
$this->assertEmpty($response->data);
```

---

## 14. SDK Dokümantasyon Üretimi

### 14.1 Otomatik Üretim

| Dil | Araç | Çıktı |
|-----|------|-------|
| PHP | phpDocumentor | HTML docs |
| JavaScript | TypeDoc | HTML docs |
| Python | Sphinx | HTML/PDF docs |

### 14.2 README Şablonu

```markdown
# CoreMusic {Language} SDK

## Kurulum
{install_command}

## Başlangıç
{quick_start_code}

## Örnekler
{examples}

## API Referansı
{api_reference_link}

## Hata Yönetimi
{error_handling}

## Lisans
MIT
```

---

## 15. SDK Üretim Pipeline

### 15.1 CI/CD Entegrasyonu

```
OpenAPI Spec Değişikliği
  → GitHub Actions tetiklenir
    → Spec doğrulanır (lint)
    → Her dil için SDK üretilir
      → Testler çalıştırılır (%80 coverage)
        → Version bump
          → Publish (Composer/npm/PyPI)
            → Dokümantasyon güncellenir
              → Changelog güncellenir
```

### 15.2 Publish Adımları

| Adım | Araç | Komut |
|------|------|-------|
| 1. Spec lint | spectral | `spectral lint openapi.yaml` |
| 2. SDK generate | openapi-generator | `openapi-generator generate` |
| 3. Test | phpunit/vitest/pytest | `npm test` |
| 4. Version bump | semantic-release | `semantic-release` |
| 5. Publish | composer/npm/twine | `npm publish` |
| 6. Docs | typedoc/sphinx | `typedoc --out docs` |

---

## 16. Hızlı Referans

| İhtiyaç | İlk Adım |
|---------|----------|
| Hangi diller destekleniyor | §4 Desteklenen Diller |
| Nasıl kurulur | §7 SDK Dağıtımı |
| Method isimleri | §5 İsimlendirme Konvansiyonu |
| Versiyon eşleme | §6 SDK Versiyonlama |
| Hata yönetimi | §9 Hata Yönetimi |
| Retry politikası | §10 Retry Mantığı |
| Sayfalama | §12 Sayfalama Yardımcıları |
| Test | §13 SDK Test |
| Nasıl yayınlanır | §15 SDK Üretim Pipeline |

---

## 17. Cross References

| Kaynak | Hedef | İlişki |
|--------|-------|--------|
| Bu dosya | [[api-architecture-master]] | Ana API mimarisi |
| Bu dosya | [[api-design-rules]] | Tasarım kuralları |
| Bu dosya | [[api-public-contract]] | Public API standartları |
| Bu dosya | [[api-internal-contract]] | Internal API |
| §3 Pipeline | [[ADR-042-vault-restructuring-2026-08-03]] | CI/CD |
| §9 Hata | [[api-public-contract]] §15 | Hata formatı |
| §13 Test | [[testing/strategy]] | Test stratejisi |
| §14 Doküman | [[ADR-024-ecosystem-modular-docs]] | Dokümantasyon |

---

## 18. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 1.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Sections | 18 |
| Supported Languages | 3 (PHP, JS, Python) |
| Planned Languages | 3 (Java, Go, Ruby) |
| Error Types | 8 |
| Retry Parameters | 6 |
| CI/CD Steps | 6 |
| Cross References | 8 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode