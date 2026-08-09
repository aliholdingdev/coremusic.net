---
type: architecture
category: contracts
title: "API Pagination Standard"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# API Pagination Standard

**Zorunlu Bağlantılar:** [[api-architecture-master]], [[api-design-rules]]

---

## 1. Amaç

CoreMusic API'sindeki tüm liste endpoint'leri için standart sayfalama (pagination) formatını tanımlar. Cursor-based ve offset-based olmak üzere iki strateji desteklenir.

---

## 2. Cursor-Based vs Offset-Based

| Özellik | Cursor-Based | Offset-Based |
|---------|-------------|--------------|
| Perf (Büyük DB) | Yüksek | Düşük |
| Kararlılık | Kararlı (stable) | Kararsız (unstable) |
| Real-time | Uygun | Uygun değil |
| Derin sayfa | Yüksek performans | Düşük performans |
| Kullanım | Asıl (default) | Legacy fallback |
| Complexity | Orta | Düşük |

---

## 3. Varsayılan Değerler

| Parametre | Varsayılan | Maksimum | Açıklama |
|-----------|------------|----------|----------|
| `page_size` | 20 | 100 | Sayfa başına kayıt |
| `cursor` | — | — | Sayfa belirteci |
| `page` | 1 | 10000 | Sayfa numarası (offset only) |
| `sort` | `created_at` | — | Sıralama alanı |
| `order` | `desc` | — | Sıralama yönü (asc/desc) |

---

## 4. Pagination Response Format

### 4.1 Cursor-Based Response

```json
{
    "data": [
        {"id": 1, "title": "Bohemian Rhapsody", "artist": "Queen"},
        {"id": 2, "title": "Stairway to Heaven", "artist": "Led Zeppelin"}
    ],
    "pagination": {
        "has_more": true,
        "next_cursor": "eyJpZCI6MTIzLCJjcmVhdGVkX2F0IjoiMjAyNi0wOC0wOSJ9",
        "prev_cursor": null,
        "total_count": 1547,
        "page_size": 20
    }
}
```

### 4.2 Offset-Based Response

```json
{
    "data": [
        {"id": 1, "title": "Bohemian Rhapsody", "artist": "Queen"},
        {"id": 2, "title": "Stairway to Heaven", "artist": "Led Zeppelin"}
    ],
    "pagination": {
        "current_page": 1,
        "per_page": 20,
        "total_count": 1547,
        "total_pages": 78,
        "has_next": true,
        "has_prev": false,
        "links": {
            "self": "/api/tracks?page=1&page_size=20",
            "next": "/api/tracks?page=2&page_size=20",
            "first": "/api/tracks?page=1&page_size=20",
            "last": "/api/tracks?page=78&page_size=20"
        }
    }
}
```

---

## 5. Cursor Format

| Özellik | Değer |
|---------|-------|
| Encoding | Base64 (URL-safe) |
| İçerik | `{id, created_at}` JSON |
| Şeffaflık | Client cursor'u decode etmemeli |
| Güvenlik | HMAC imzası (opsiyonel) |

```php
final class CursorEncoder
{
    public function encode(int $id, string $createdAt): string
    {
        $payload = json_encode([
            'id' => $id,
            'created_at' => $createdAt
        ]);

        return rtrim(base64_encode($payload), '=');
    }

    public function decode(string $cursor): array
    {
        $padded = str_pad($cursor, strlen($cursor) % 4, '=', STR_PAD_RIGHT);
        $payload = base64_decode($padded);

        $data = json_decode($payload, true);

        if (!isset($data['id'], $data['created_at'])) {
            throw new InvalidCursorException('Invalid cursor');
        }

        return $data;
    }
}
```

---

## 6. Request Parametreleri

### 6.1 Cursor-Based

```
GET /api/tracks?cursor=eyJpZCI6MTIzLCJjcmVhdGVkX2F0IjoiMjAyNi0wOC0wOSJ9&page_size=20
```

### 6.2 Offset-Based

```
GET /api/tracks?page=3&page_size=20
```

### 6.3 Sıralama

```
GET /api/tracks?sort=created_at&order=desc&page_size=20
```

---

## 7. Page Size Negotiation

| Durum | Aksiyon |
|-------|---------|
| `page_size` belirtilmemiş | Varsayılan 20 |
| `page_size` > 100 | Max 100'e kısıtla + header uyarısı |
| `page_size` < 1 | Min 1'e ayarla |
| `page_size` 0 | Varsayılan 20 |

```php
final class PageSizeNegotiator
{
    private const DEFAULT_SIZE = 20;
    private const MAX_SIZE = 100;
    private const MIN_SIZE = 1;

    public function negotiate(?int $requested): int
    {
        if ($requested === null || $requested <= 0) {
            return self::DEFAULT_SIZE;
        }

        return min(max($requested, self::MIN_SIZE), self::MAX_SIZE);
    }
}
```

---

## 8. Total Count Stratejisi

| Durum | Davranış |
|-------|----------|
| Small dataset (<10K) | Her zaman `total_count` döndür |
| Large dataset (>10K) | `total_count` opsiyonel |
| Search results | `total_count` her zaman döndür |
| Streaming | `has_more` yeterli |

```php
final class TotalCountStrategy
{
    public function shouldIncludeTotal(int $estimatedCount): bool
    {
        // Small datasets: always include
        if ($estimatedCount < 10000) {
            return true;
        }

        // Large datasets: only if explicitly requested
        return false;
    }
}
```

---

## 9. Empty Results

```json
{
    "data": [],
    "pagination": {
        "has_more": false,
        "next_cursor": null,
        "prev_cursor": null,
        "total_count": 0,
        "page_size": 20
    }
}
```

---

## 10. Pagination Links (Offset-Based)

| Link | Açıklama | Şart |
|------|----------|------|
| `self` | Mevcut sayfa | Her zaman |
| `next` | Sonraki sayfa | `has_next = true` |
| `prev` | Önceki sayfa | `has_prev = true` |
| `first` | İlk sayfa | Her zaman |
| `last` | Son sayfa | Her zaman |

---

## 11. Infinite Scroll Desteği

| Özellik | Değer |
|---------|-------|
| Strateji | Cursor-based |
| Response | `has_more` field |
| Client | Intersection Observer |
| Threshold | Son 5 elemente gelince load |

```javascript
// Infinite scroll implementation
const observer = new IntersectionObserver((entries) => {
    if (entries[0].isIntersecting && hasMore && !loading) {
        loadMore();
    }
}, { threshold: 0.1 });

async function loadMore() {
    loading = true;
    const response = await fetch(`/api/tracks?cursor=${nextCursor}&page_size=20`);
    const { data, pagination } = await response.json();

    appendTracks(data);
    nextCursor = pagination.next_cursor;
    hasMore = pagination.has_more;
    loading = false;
}
```

---

## 12. Search Pagination

| Özellik | Değer |
|---------|-------|
| Strateji | Cursor-based zorunlu |
| Reason | Real-time index değişikliği |
| `total_count` | Her zaman döndür |
| Max page_size | 20 (search için) |

```
GET /api/search?q=queen&cursor=xxx&page_size=20
```

---

## 13. Hata Durumları

| Durum | HTTP | Mesaj |
|-------|------|-------|
| Geçersiz cursor | 400 | `Invalid cursor format` |
| `page_size` > 100 | 200 | Max 100'e kısıtla |
| `page` < 1 | 200 | İlk sayfaya yönlendir |
| `page` > total | 200 | Son sayfaya yönlendir |
| Sort alanı geçersiz | 400 | `Invalid sort field` |

---

## 14. Uygulama

### 14.1 Cursor-Based Repository

```php
final class TrackRepository
{
    public function paginate(
        int $pageSize,
        ?string $cursor = null,
        string $sort = 'created_at',
        string $order = 'desc'
    ): array {
        $builder = $this->pdo->createQueryBuilder();
        $builder->select('id', 'title', 'artist', 'created_at')
                ->from('tracks');

        if ($cursor !== null) {
            $decoded = $this->cursorEncoder->decode($cursor);
            $builder->where('created_at < :cursor_date')
                    ->setParameter('cursor_date', $decoded['created_at']);
        }

        $builder->orderBy($sort, $order === 'desc' ? 'DESC' : 'ASC')
                ->setMaxResults($pageSize + 1); // +1 for has_more

        $results = $builder->getQuery()->execute();
        $hasMore = count($results) > $pageSize;

        if ($hasMore) {
            array_pop($results);
        }

        return [
            'data' => $results,
            'pagination' => [
                'has_more' => $hasMore,
                'next_cursor' => $hasMore
                    ? $this->cursorEncoder->encode(
                        end($results)['id'],
                        end($results)['created_at']
                    )
                    : null,
                'prev_cursor' => $cursor,
                'page_size' => $pageSize
            ]
        ];
    }
}
```

---

## 15. Edge Cases

| Durum | Çözüm |
|-------|-------|
| Silent delete (soft delete) | Cursor-based kararlı |
| Yeni kayıt ekleme | Offset-based sayfa kayması |
| Cursor expired | İlk sayfaya yönlendirme |
| Concurrent modification | Optimistic locking |
| Large offset (deep page) | Cursor-based geçiş |

---

## 16. Warnings

| # | Uyarı | Sonuc |
|---|-------|-------|
| 1 | Offset-based large dataset'te | Performans düşüşü |
| 2 | `total_count` her zaman hesaplama | DB load artışı |
| 3 | Cursor'u client tarafında decode | Güvenlik açığı |
| 4 | `page_size` > 100 | Memory ve performans |
| 5 | Sıralama alanı indekslenmemiş | Full table scan |

---

## 17. Cross References

| Kaynak | Hedef | İlişki |
|--------|-------|--------|
| [[api-architecture-master]] | Ana mimari referans | Master |
| [[api-design-rules]] | API tasarım kuralları | Style guide |

---

## 18. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 1.0.0 |
| Strategies | 2 (Cursor + Offset) |
| Default Page Size | 20 |
| Max Page Size | 100 |
| Response Fields | 4-6 |
| Infinite Scroll | ✅ |
| Search Pagination | ✅ |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
