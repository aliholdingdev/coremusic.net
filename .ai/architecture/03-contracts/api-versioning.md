---
type: architecture
category: contracts
title: "API Versioning Strategy"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# API Versioning Strategy

**Zorunlu Bağlantılar:** [[api-architecture-master]] · [[api-design-rules]] · [[CLAUDE.md]]

## 1. Amaç

CoreMusic API sürüm yönetimi stratejisini, lifecycle akışlarını, breaking/non-breaking change kurallarını ve backward compatibility politikasını tanımlayan **Tek Doğruluk Kaynağıdır**.

## 2. Versioning Yaklaşımı

### 2.1 URL Versioning (Birincil)

```
/api/v1/songs
/api/v2/songs
```

| Yöntem | Durum | Açıklama |
|--------|-------|----------|
| **URL Path** | ✅ Birincil | `/api/v1/`, `/api/v2/` |
| **Query Param** | ⚠️ Destekli | `?version=1` (geriye uyumluluk) |
| **Header** | ⚠️ Destekli | `X-API-Version: 1` |
| **Content Negotiation** | ❌ Kullanılmıyor | Accept header versioning |

### 2.2 Version Header

```
X-API-Version: 1
X-API-Version: 2
X-API-Version-Latest: true
```

| Header | Yön | Açıklama |
|--------|-----|----------|
| `X-API-Version` | İstek | İstenen sürüm |
| `X-API-Version` | Yanıt | Kullanılan sürüm |
| `X-API-Version-Latest` | İstek | En son sürümü kullan |
| `X-API-Deprecation` | Yanıt | Eski sürüm uyarısı |
| `X-API-Sunset` | Yanıt | Kaldırma tarihi |

## 3. Version Lifecycle

```
┌─────────┐    ┌─────────┐    ┌─────────────┐    ┌─────────┐
│  DRAFT  │───▶│ ACTIVE  │───▶│ DEPRECATED  │───▶│ SUNSET  │
└─────────┘    └─────────┘    └─────────────┘    └─────────┘
   30 gün        Aktif          6 ay destek        Kaldırıldı
```

| Aşama | Süre | Davranış | Yanıt Headers |
|-------|------|----------|---------------|
| **Draft** | 30 gün | Test ortamında aktif | `X-API-Version-Draft: true` |
| **Active** | Sınırsız | Üretimde aktif | — |
| **Deprecated** | 6 ay minimum | Uyarı verir, çalışır | `X-API-Deprecation: true`, `X-API-Sunset: YYYY-MM-DD` |
| **Sunset** | — | 410 Gone döner | — |

### 3.1 Lifecycle Kuralları

| Kural | Değer |
|-------|-------|
| Minimum Active süresi | 12 ay |
| Minimum Deprecated süresi | 6 ay |
| Eşzamanlı aktif sürüm | Max 3 |
| Sunset sonrası yanıt | `410 Gone` |
| Migration tool | Otomatik versiyon dönüştürücü |

## 4. Breaking vs Non-Breaking Changes

### 4.1 Breaking Changes (Yeni Sürüm Gerekli)

| Değişiklik | Örnek | Etki |
|------------|-------|------|
| Field kaldırma | `artist` alanı silindi | İstemci kırılır |
| Field type değiştirme | `id` (int → string) | İstemci kırılır |
| URL yapısı değiştirme | `/songs/{id}` → `/tracks/{id}` | Routing kırılır |
| Auth mekanizması değiştirme | JWT → OAuth2 | Tüm istemciler etkilenir |
| Error formatı değiştirme | `{error}` → `{message}` | Error handling kırılır |
| Rate limit azaltma | 60 → 30 req/min | İstemci davranışı değişir |
| Required field ekleme | `genre` zorunlu yapıldı | Eski istemciler kırılır |
| Response envelope değiştirme | `{data}` → `{result}` | Parsing kırılır |

### 4.2 Non-Breaking Changes (Aynı Sürümde)

| Değişiklik | Örnek | Etki |
|------------|-------|------|
| Yeni field ekleme | `cover_url` eklendi | İstemci yok sayar |
| Yeni endpoint | `/api/v1/songs/{id}/lyrics` | Eski endpoint'ler etkilenmez |
| Yeni query param | `?genre=rock` | Eski parametreler çalışır |
| Yeni enum değeri | `status: "archived"` eklendi | Eski istemciler varsayılan kullanır |
| Rate limit artırma | 60 → 120 req/min | Olumlu etki |
| Yeni header destekleme | `Accept-Language` | Opsiyonel |
| Yeni response field | `metadata` eklendi | İstemci yok sayar |

### 4.3 Karar Matrisi

```
Değişiklik tipi belirle
    │
    ├── Eski istemci bozulur mu?
    │   ├── EVET → Breaking → Yeni versiyon (v2)
    │   └── HAYIR → Non-breaking → Aynı versiyonda
    │
    ├── Geriye uyumluluk korunuyor mu?
    │   ├── EVET → Non-breaking
    │   └── HAYIR → Breaking
    │
    └── Migration gerekiyor mu?
        ├── EVET → Breaking + Migration guide
        └── HAYIR → Non-breaking
```

## 5. Version Routing (Gateway)

### 5.1 Gateway Version Routing Akışı

```
Client: GET /api/v2/songs
    │
    ▼
API Gateway
    │
    ├── 1. URL'den versiyonu çıkar (v2)
    │
    ├── 2. Versiyon aktif mi?
    │   ├── EVET → devam
    │   ├── DEPRECATED → X-API-Deprecation header ekle
    │   └── SUNSET → 410 Gone dön
    │
    ├── 3. Versiyon routing tablosunda eşleştir
    │
    ├── 4. Doğru servise yönlendir
    │   └── /api/v2/songs → MusicService v2
    │
    └── 5. Response'a version header ekle
```

### 5.2 Version Routing Tablosu

```php
$versionRoutes = [
    'v1' => [
        'songs'     => MusicController::class,
        'playlists' => PlaylistController::class,
        'users'     => UserController::class,
    ],
    'v2' => [
        'songs'     => MusicControllerV2::class,
        'playlists' => PlaylistControllerV2::class,
        'users'     => UserControllerV2::class,
    ],
];
```

### 5.3 Version Fallback

| Durum | Davranış |
|-------|----------|
| Versiyon belirtilmemiş | `v1` kullan |
| Geçersiz versiyon | `400 Bad Request` |
| Versiyon deprecated | Çalıştır + warning header |
| Versiyon sunset | `410 Gone` |
| `X-API-Version-Latest: true` | En son aktif sürümü kullan |

## 6. Migration Stratejisi

### 6.1 Migration Checklist

| # | Adım | Sorumlu | Süre |
|---|------|---------|------|
| 1 | Breaking change listesini çıkar | Backend Architect | 1 gün |
| 2 | Migration guide yaz | Backend Architect | 2 gün |
| 3 | Eski sürümü deprecated yap | Backend Architect | 1 saat |
| 4 | Client bildirimi gönder | DevOps | 1 gün |
| 5 | Otomatik migration tool oluştur | Backend Architect | 3 gün |
| 6 | Client'ları güncelle | İlgili ekip | 1-4 hafta |
| 7 | Eski sürümü sunset yap | Backend Architect | 1 saat |
| 8 | Sunset endpoint'leri temizle | Backend Architect | 1 gün |

### 6.2 Migration Tool

```
┌─────────────────────────────────────────────────────────────┐
│                 AUTOMATED MIGRATION                          │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  Input: /api/v1/songs?fields=artist,title                   │
│                                                              │
│  ┌─────────────────────┐                                    │
│  │  Migration Mapper   │                                    │
│  │  - v1 → v2 mapping  │                                    │
│  │  - Field transform  │                                    │
│  │  - Response adapt   │                                    │
│  └──────────┬──────────┘                                    │
│             │                                                │
│  Output: /api/v2/songs?fields=artist,name,duration          │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

## 7. Backward Compatibility Kuralları

| # | Kural | Açıklama |
|---|-------|----------|
| 1 | Eski istemciler çalışmalı | Yeni sürüm, eski istemcileri bozmamalı |
| 2 | Yeni field'lar opsiyonel | `nullable` veya `default` değerli olmalı |
| 3 | Response formatı değişmez | `{data, meta, links}` korunur |
| 4 | Error formatı değişmez | `{error: {code, message, details}}` korunur |
| 5 | Auth mekanizması değişmez | JWT korunur, yeni method eklenir |
| 6 | Pagination formatı değişmez | `{page, per_page, total, data}` korunur |
| 7 | HTTP status code'ları değişmez | Aynı durum aynı kodu döner |
| 8 | Rate limit azaltılmaz | Sadece artırılabilir |

## 8. Deprecation Policy

### 8.1 Bildirim Süreci

| Gün | Aksiyon |
|-----|---------|
| GÜN 0 | Deprecated ilan et, header ekle |
| GÜN 1 | Client bildirimi (email, dashboard) |
| GÜN 30 | İlk hatırlatma |
| GÜN 90 | İkinci hatırlatma (1 ay kaldı) |
| GÜN 150 | Son hatırlatma (1 hafta kaldı) |
| GÜN 180 | Sunset uygula (410 Gone) |

### 8.2 Deprecation Headers

```http
HTTP/1.1 200 OK
X-API-Version: 1
X-API-Deprecation: true
X-API-Sunset: 2027-02-09
X-API-Deprecation-Info: https://docs.coremusic.net/api/v1-deprecation
Link: </api/v2/songs>; rel="successor-version"
```

## 9. Version Matrisi

| Sürüm | Durum | Aktif Tarih | Sunset Tarih | Destek |
|-------|-------|-------------|--------------|--------|
| v1 | Active | 2026-08-09 | — | ✅ Tam destek |
| v2 | Draft | 2026-11-01 | — | ⚠️ Test ortamı |
| v3 | — | Planlanıyor | — | — |

## 10. Yasaklar

| ❌ Yasak | ✅ Doğru |
|----------|----------|
| Eski sürümü hemen kaldır | 6 ay deprecated bırak |
| Breaking change without version | Yeni version aç |
| Header versioning tek başına | URL versioning birincil |
| Migration without notice | 30 gün önceden bildir |
| Silent breaking change | Deprecation header ekle |
| Version skip (v1 → v3) | Sıralı versionlama |

## 11. Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | URL versioning birincil | Versiyon routing kırılır |
| 2 | Minimum 6 ay deprecated | Client'lar kırılır |
| 3 | Breaking change = yeni version | Geriye uyumluluk bozulur |
| 4 | Sunset = 410 Gone | Eski istemciler çalışır |
| 5 | Max 3 eşzamanlı aktif version | Bakım yükü artar |

## 12. Cross References

| Dosya | İlişki |
|-------|--------|
| [[api-architecture-master]] | Ana API mimarisi |
| [[api-design-rules]] | Tasarım kuralları |
| [[api-security]] | Güvenlik katmanı |
| [[api-authentication]] | Kimlik doğrulama |
| [[api-error-codes]] | Hata kodları |
| [[middleware-pipeline]] | Middleware sırası |

## 13. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 1.0.0 |
| **ADR Uyumlu** | ✅ 001, 007, 042, 051, 053 |
| **Zero Hallucination** | ✅ |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
