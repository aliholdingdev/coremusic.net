---
title: "Backend Reference"
type: reference
category: backend-integration
updated: 2026-08-11
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Backend Reference

**Zorunlu Baglantilar:** [[ADR-051-platform-rewrite-from-scratch]] · [[ADR-053-enterprise-router-architecture]] · [[ADR-054-enterprise-composer-stack]]

---

## 1. Amaç

Frontend ile backend entegrasyonu için API yapısı, auth flow, CSRF koruması ve session yönetimi referansıdır.

---

## 2. API Endpoints Yapısı

### 2.1 Auth Endpoints

| Endpoint | Method | Body | Response | Açıklama |
|----------|--------|------|----------|----------|
| `/login` | POST | `{email, password, csrf_token}` | `{success, user, session}` | Giriş |
| `/logout` | POST | `{csrf_token}` | `{success}` | Çıkış |
| `/register` | POST | `{username, email, password, gender, csrf_token}` | `{success, user}` | Kayıt |
| `/auth/check` | GET | — | `{authenticated, user}` | Session kontrolü |
| `/auth/refresh` | POST | `{csrf_token}` | `{success, token}` | Token yenile |
| `/auth/permissions` | GET | — | `{permissions[]}` | Yetki listesi |

### 2.2 Music Endpoints

| Endpoint | Method | Body | Response | Açıklama |
|----------|--------|------|----------|----------|
| `/api/v1/songs` | GET | — | `{songs[], total, page}` | Şarkı listesi |
| `/api/v1/songs/{id}` | GET | — | `{song}` | Şarkı detay |
| `/api/v1/albums` | GET | — | `{albums[], total, page}` | Albüm listesi |
| `/api/v1/albums/{id}` | GET | — | `{album, songs[]}` | Albüm detay |
| `/api/v1/artists` | GET | — | `{artists[], total, page}` | Sanatçı listesi |
| `/api/v1/artists/{id}` | GET | — | `{artist, albums[]}` | Sanatçı detay |
| `/api/v1/search` | GET | `?q={query}` | `{songs[], albums[], artists[]}` | Arama |

### 2.3 Playlist Endpoints

| Endpoint | Method | Body | Response | Açıklama |
|----------|--------|------|----------|----------|
| `/api/v1/playlists` | GET | — | `{playlists[]}` | Çalma listesi |
| `/api/v1/playlists` | POST | `{name, songs[]}` | `{playlist}` | Oluştur |
| `/api/v1/playlists/{id}` | PUT | `{name, songs[]}` | `{playlist}` | Güncelle |
| `/api/v1/playlists/{id}` | DELETE | — | `{success}` | Sil |

### 2.4 User Endpoints

| Endpoint | Method | Body | Response | Açıklama |
|----------|--------|------|----------|----------|
| `/api/v1/user/profile` | GET | — | `{user}` | Profil |
| `/api/v1/user/profile` | PUT | `{name, email}` | `{user}` | Profil güncelle |
| `/api/v1/user/preferences` | GET | — | `{preferences}` | Tercihler |
| `/api/v1/user/preferences` | PUT | `{theme, language}` | `{preferences}` | Tercih güncelle |
| `/api/v1/user/favorites` | GET | — | `{favorites[]}` | Favoriler |
| `/api/v1/user/favorites` | POST | `{song_id}` | `{success}` | Favoriye ekle |
| `/api/v1/user/favorites/{id}` | DELETE | — | `{success}` | Favoriden çıkar |

---

## 3. Request/Response Formatı

### 3.1 Standart Başlıklar

```http
Content-Type: application/json
X-CSRF-Token: {csrf_token}
X-Requested-With: XMLHttpRequest
Accept: application/json
```

### 3.2 Başarılı Yanıt

```json
{
    "status": "success",
    "data": {
        "id": 1,
        "title": "Şarkı Adı"
    },
    "meta": {
        "timestamp": "2026-08-11T12:00:00Z",
        "version": "1.0"
    }
}
```

### 3.3 Liste Yanıtı

```json
{
    "status": "success",
    "data": [
        {"id": 1, "title": "Şarkı 1"},
        {"id": 2, "title": "Şarkı 2"}
    ],
    "meta": {
        "total": 100,
        "page": 1,
        "per_page": 20,
        "total_pages": 5
    }
}
```

### 3.4 Hata Yanıtı

```json
{
    "status": "error",
    "error": {
        "code": "VALIDATION_ERROR",
        "message": "Email formatı geçersiz",
        "details": {
            "field": "email",
            "rule": "email"
        }
    }
}
```

---

## 4. Authentication Flow

### 4.1 Login Flow

```
1. GET /login → Login formu göster (csrf_token input hidden)
2. POST /login → {email, password, csrf_token}
   ├── Başarılı → {success: true, user, session}
   │   ├── Redirect / (ana sayfa)
   │   └── Cookie: COREMUSIC_SESS={session_id}
   └── Başarısız → {success: false, error: "Kimlik doğrulama başarısız"}
```

### 4.2 Register Flow

```
1. GET /register → Register formu göster
2. POST /register → {username, email, password, gender, csrf_token}
   ├── Başarılı → {success: true, user}
   │   ├── Otomatik login
   │   └── Redirect /
   └── Başarısız → {success: false, errors: {...}}
```

### 4.3 Logout Flow

```
1. POST /logout → {csrf_token}
2. Session sonlandır
3. Cookie sil
4. Redirect /login
```

---

## 5. CSRF Koruması

### 5.1 Token Üretimi

```php
<?php
// Session başlatıldıktan sonra
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
```

### 5.2 Frontend Kullanımı

```html
<!-- Form'da -->
<input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
```

```javascript
// AJAX'ta
fetch('/api/v1/data', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': document.querySelector('input[name="csrf_token"]').value
    },
    body: JSON.stringify({ /* data */ })
});
```

### 5.3 Backend Doğrulaması

```php
<?php
$token = $request->getParsedBody()['csrf_token']
    ?? $request->getHeaderLine('X-CSRF-Token');

if (hash_equals($_SESSION['csrf_token'] ?? '', $token) === false) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'error' => ['code' => 'CSRF_FAILED']]);
    exit;
}
```

---

## 6. Session Yönetimi

### 6.1 Cookie Yapılandırması

| Özellik | Değer |
|---------|-------|
| Name | `COREMUSIC_SESS` |
| Lifetime | 3600s (1 saat) |
| Path | `/` |
| Domain | `.coremusic.net` |
| Secure | `true` |
| HttpOnly | `true` |
| SameSite | `Lax` |

### 6.2 Session Kontrolü

```javascript
// Periyodik session kontrolü
setInterval(async () => {
    const response = await fetch('/auth/check', {
        credentials: 'include'
    });
    const data = await response.json();
    if (!data.authenticated) {
        window.location.href = '/login';
    }
}, 300000); // 5 dakikada bir
```

---

## 7. Hata Yönetimi

### 7.1 HTTP Kodları

| Kod | Anlam | Frontend Davranışı |
|-----|-------|-------------------|
| 200 | Başarılı | Normal işleme |
| 201 | Oluşturuldu | Başarı mesajı |
| 400 | Bad Request | Form hata gösterimi |
| 401 | Unauthorized | Login sayfasına yönlendir |
| 403 | Forbidden | Yetki hatası göster |
| 404 | Not Found | 404 sayfası göster |
| 419 | CSRF Expired | Token yenile, formu tekrar gönder |
| 422 | Validation | Form hatalarını göster |
| 429 | Rate Limit | "Çok fazla istek" mesajı |
| 500 | Server Error | Genel hata sayfası |

### 7.2 Frontend Hata Yakalama

```javascript
async function apiRequest(url, options = {}) {
    try {
        const response = await fetch(url, {
            ...options,
            credentials: 'include',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': getCsrfToken(),
                ...options.headers
            }
        });

        if (response.status === 419) {
            // CSRF token süresi doldu — sayfayı yenile
            window.location.reload();
            return;
        }

        if (response.status === 401) {
            // Session süresi doldu — login sayfasına yönlendir
            window.location.href = '/login';
            return;
        }

        const data = await response.json();
        return data;
    } catch (error) {
        console.error('API Error:', error);
        throw error;
    }
}
```

---

## 8. Rate Limiting

| Endpoint Grubu | Limit | Süre |
|----------------|-------|------|
| Auth endpoints | 10 | 60s |
| API endpoints | 60 | 60s |
| Search endpoints | 30 | 60s |
| Upload endpoints | 5 | 60s |

### 8.1 Rate Limit Header'ları

```http
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 45
X-RateLimit-Reset: 1691750400
```

---

## 9. CORS Yapılandırması

| Özellik | Değer |
|---------|-------|
| Allowed Origins | `*.coremusic.net` |
| Allowed Methods | `GET, POST, PUT, DELETE, OPTIONS` |
| Allowed Headers | `Content-Type, X-CSRF-Token, Authorization` |
| Credentials | `true` |
| Max Age | 86400 |

---

## 10. Quick Reference

| Kullanım | Kaynak |
|----------|--------|
| CSRF token key | `csrf_token` (NOT `_csrf_token`) |
| Session name | `COREMUSIC_SESS` |
| API base URL | `/api/v1/` |
| Auth endpoints | `/login`, `/logout`, `/register` |
| Error format | `{status: "error", error: {code, message}}` |
| Success format | `{status: "success", data: {...}}` |

---

## 11. Cross References

| Kaynak | Hedef |
|--------|-------|
| Bu dosya | [[ADR-051-platform-rewrite-from-scratch]] |
| Bu dosya | [[ADR-053-enterprise-router-architecture]] |
| Bu dosya | [[ADR-010-csrf-protection-strategy]] |
| Bu dosya | [[ADR-011-session-management]] |
| Bu dosya | [[ADR-002-pdo-mandatory-no-orm]] |

---

## 12. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 1.0.0 |
| Sections | 12 |
| API Endpoints | 20+ |
| ADR Coverage | 051, 053, 010, 011, 002 |
| Status | Red Team · Human Mode · Truth Mode verified |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-11
**Mode:** Red Team · Human Mode · Truth Mode
