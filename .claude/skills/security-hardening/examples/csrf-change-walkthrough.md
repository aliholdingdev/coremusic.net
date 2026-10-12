# Örnek — CSRF Değişikliği / Yükseltme Walkthrough (girdi → çıktı tam döngü)

> Senaryo: "CSRF ekle / CSRF'i güçlendir" isteği. Kaynak: ADR-010 (üç katmanlı CSRF),
> `.ai/CLAUDE.md` §6 (#6) + Guardrail #6 · §21 · §22 (Multi-Tab CSRF → session-bound tek token).

## Girdi (talep)

```text
"Yeni mutating endpoint'im POST ile çalışıyor; CSRF koruması ekle ve çoklu sekmelerde
çalışmasını sağla. Sırayı bozmadan."
```

## Adım 1 — Threat check

| Soru | Cevap (ADR-010) |
|------|-----------------|
| Yüzey | Cookie tabanlı oturum → tarayıcı her isteğe cookie ekler → CSRF |
| Katmanlar | (1) synchronizer `csrf_token` · (2) `SameSite=Lax` · (3) Origin/Referer fail-closed · (4) yedek: signed double-submit (yalnız muaf uçlar) |
| Token adı | **`csrf_token`** — `_csrf_token` yasak (Guardrail #6) |

## Adım 2 — Order check

- Csrf = **pipeline #6**, konumu: `SessionManager (#5) → Csrf (#6) → BypassAuth (#7)`.
- Sıra **dokunulmaz**. Nonce zinciri (#4 → #5) ve oturum öncesi koşul (#5 → #6) korunur.

## Adım 3 — Uygulama (doğru implementasyon)

```php
// 1) Oturum başlangıcında token üret (SessionLifecycle — ADR-010 §1.1)
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // 64 hex, CSPRNG
}

// 2) Mid #6 — CsrfMiddleware mantığı: GET/HEAD/OPTIONS hariç her mutating istek
$method = $_SERVER['REQUEST_METHOD'];
if (!in_array($method, ['GET', 'HEAD', 'OPTIONS'], true)) {
    $sessionToken = $_SESSION['csrf_token'] ?? '';
    $sent = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($_POST['csrf_token'] ?? '');
    if ($sessionToken === '' || !hash_equals($sessionToken, $sent)) {
        http_response_code(403); // csrf_invalid — fail-closed
        exit;
    }
}
```

```html
<!-- 3) Token dağıtımı: gizli alan (HtmlShellRenderer — ADR-010 §2.2c) -->
<input type="hidden" name="csrf_token" id="csrf-global" value="<?= htmlspecialchars($csrfToken) ?>">
```

```js
// 4) SPA gönderimi: header (CORS allowlist'inde X-CSRF-Token)
const token = document.querySelector('[name=csrf_token]').value;
fetch(url, {
  method: 'POST',
  credentials: 'include',
  headers: { 'X-CSRF-Token': token, 'Content-Type': 'application/json' },
  body: JSON.stringify(payload),
});
```

**Multi-tab davranışı (§22 — Edge Case):** token **session-bound TEK token**tır — tüm
sekmeler aynı oturumu paylaşır ve aynı `csrf_token`'ı okur → sekmeler arası çakışma yok.
Privilege change (rol/şifre/email) sonrası token döner; açık eski sekme 403 alır → JS yeni
token'ı DOM'dan okuyup 1 kez yeniden dener (ADR-010 §2.2c).

## Adım 4 — Doğrulama adımları (test kapısı)

| # | Test | Beklenen |
|---|------|----------|
| 1 | Token'lı geçerli POST | 200 |
| 2 | Token'sız POST | **403 `csrf_invalid`** |
| 3 | Yanlış/sahte token POST | **403** |
| 4 | `_csrf_token` adıyla gönderim (eski ad) | 403 (ad yasak — Guardrail #6) |
| 5 | GET/HEAD/OPTIONS | Token istenmez (safe method) |
| 6 | Geçersiz Origin (cross-site POST) | **403 `origin_not_allowed`** |
| 7 | İki sekme: aynı token ikisi de geçer | 200/200 |
| 8 | Privilege change sonrası eski token | 403 → yeni token'la 200 |
| 9 | `MiddlewarePipelineTest` #6_Csrf | Yeşil (sıra korunmuş) |

## Adım 5 — Ters sıralama neyi kırardı (Response — "what would fail if reordered")

| Hata | Kırılan şey |
|------|-------------|
| Csrf (#6) → SessionManager (#5) öncesine | Oturum henüz başlamamış → `$_SESSION['csrf_token']` boş → **her legitimate POST 403** (hizmet kesintisi) veya token doğrulanamaz (güvenlik boşu) |
| SecurityHeaders (#4) ↔ SessionManager (#5) yer değiştirme | Nonce oturuma kaydedilemez → CSP `nonce` boş → **tüm inline script'ler bloklanır** + nonce oturumsuz kalır (XSS riski) — Guardrail #7 / §23-1 |
| Csrf, Auth (#8) sonrasına | Kimlik kontrolüne kadar token'sız erişim; bağımlılık zinciri bozulur |
| BypassAuth'ın prod'da açık bırakılması | Token doğrulaması atlanabilir → H001 / Soft Constraint #4 ihlali |

## Çıktı Özeti

```text
Değişiklik: POST endpoint'i #6 Csrf kapsamına alındı
Token:      csrf_token (session-bound tek token — multi-tab OK), hash_equals, 403 fail-closed
Sıra:       #1-#10 DEĞİŞMEDİ — MiddlewarePipelineTest yeşil
Doğrulama:  9 testlik kapı (Adım 4) geçildi; yasaklı kalıp taraması 0 ihlal
Kaynak:     ADR-010 §2.2 · CLAUDE §6/§7#6/§21/§22 — kanıtsız madde yok (Zero Hallucination)
```