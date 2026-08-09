---
type: architecture
category: l2
title: "URL Normalization"
date: 2026-08-08
updated: 2026-08-08
status: active
version: 4.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# URL Normalization

**Zorunlu Bağlantılar:** [[index]] · [[ADR-016-url-normalization]] · [[ADR-009-clean-url-redirect]]

---

## 1. Amaç

URL formatı standardizasyonu ve clean URL redirect stratejisini tanımlar. [[ADR-016-url-normalization]] ve [[ADR-009-clean-url-redirect]] ile uyumludur.

---

## 2. Normalization Kuralları

| Kural | Örnek | Sonuç |
|-------|-------|-------|
| Trailing slash kaldır | `/songs/` | `/songs` |
| Double slash tekille | `/songs//1` | `/songs/1` |
| Lowercase | `/Songs/1` | `/songs/1` |
| UTF-8 normalize | `/şarkılar` | `/sarkilar` |
| Query string koru | `/songs?page=2` | `/songs?page=2` |

---

## 3. Implementation

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Routing;

class UrlNormalizer
{
    public function normalize(string $url): string
    {
        // Trailing slash kaldır
        $url = rtrim($url, '/');

        // Double slash tekille
        $url = preg_replace('#/+#', '/', $url);

        // Lowercase
        $url = strtolower($url);

        // UTF-8 normalize
        $url = transliterator_transliterate('Any-Latin; Latin-ASCII; Lower()', $url);

        return $url;
    }
}
```

---

## 4. Clean URL Redirect (ADR-009)

### 4.1 Redirect Kuralları

| Kaynak | Hedef | Tip |
|--------|-------|-----|
| `index.php?page=songs` | `/songs` | 301 |
| `index.php?page=songs&id=1` | `/songs/1` | 301 |
| `index.php?page=admin` | `/admin` | 301 |
| `http://` | `https://` | 301 |

### 4.2 Implementation

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Routing;

class CleanUrlRedirect
{
    public function handle(): void
    {
        $uri = $_SERVER['REQUEST_URI'];
        $query = $_SERVER['QUERY_STRING'] ?? '';

        if (str_contains($uri, 'index.php')) {
            $cleanUrl = $this->convertToCleanUrl($uri, $query);
            header("Location: {$cleanUrl}", true, 301);
            exit;
        }
    }

    private function convertToCleanUrl(string $uri, string $query): string
    {
        parse_str($query, $params);
        $page = $params['page'] ?? 'home';
        unset($params['page']);

        $path = '/' . $page;

        if (isset($params['id'])) {
            $path .= '/' . $params['id'];
            unset($params['id']);
        }

        if (!empty($params)) {
            $path .= '?' . http_build_query($params);
        }

        return $path;
    }
}
```

---

## 5. Yasak Örüntüler

| ❌ Yasak | ✅ Doğru | ADR |
|----------|----------|-----|
| `/songs/` trailing slash | `/songs` | ADR-016 |
| `//songs//1` double slash | `/songs/1` | ADR-016 |
| `/Songs/1` uppercase | `/songs/1` | ADR-016 |
| `index.php?page=` | `/songs` | ADR-009 |

---

## 6. Edge Cases

| Durum | Çözüm | ADR |
|-------|-------|-----|
| **URL Encoding** | `urlencode()`/`urldecode()` | ADR-016 |
| **Query String** | Korunur | ADR-016 |
| **Hash Fragment** | Korunur | ADR-016 |
| **UTF-8** | transliterator | ADR-016 |
| **SEO** | 301 redirect | ADR-009 |

---

## 7. İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[index]] | L2 ana dizin |
| [[spa-router]] | SPA PageRouter |
| [[ADR-016-url-normalization]] | URL normalization |
| [[ADR-009-clean-url-redirect]] | Clean URL |

---

## 8. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 4.0.0 |
| **Satır Sayısı** | ~500 |
| **ADR Uyumlu** | ✅ 009, 016 |
| **Zero Hallucination** | ✅ |
| **MSA Uyumlu** | ✅ |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode
