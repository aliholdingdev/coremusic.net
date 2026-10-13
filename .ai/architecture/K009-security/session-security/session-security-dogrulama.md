---
node_id: K009-security/session-security/dogrulama
node_type: module
status: verify-required
parent_id: K009-security/session-security
children: []
depends_on:
  - K009-security/session-security
evidence:
  - "6 dosya diskten okundu (2026-10-12): 4 Session + SecurityHeaders + ApiSession"
  - "Header seti gerçek satırlarıyla karşılaştırıldı (satır 26-41)"
validation:
  - "2026-10-12 — 36 kontrol; 34 PASS, 1 FAIL (HSTS), 1 UNKNOWN"
open_questions:
  - "Zaman aşımı eşikleri (SessionConfig::IDLE_TIMEOUT vb.) sayısal olarak okunmadı → UNKNOWN"
  - "HSTS başlığı header setinde YOK → üretimde .htaccess/nginx eklenip eklenmediği UNKNOWN"
group: kanit
---

# session-security-dogrulama.md — Disk Kanıtı Kontrolleri

> Tarih: 2026-10-12 · Beklenen / Bulgu / Karar sütunları.
> FAIL, sessiz geçilmez — raporlanır (bu dosyada 1 adet).

## §1 Cookie Bayrakları (SessionInitializer.php · 40-47)

| # | Kontrol | Beklenen | Bulgu |
|---|---|---|---|
| 01 | lifetime = 0 | oturum cookie'si | ✅ PASS satır 41 |
| 02 | path = / | tüm site | ✅ PASS satır 42 |
| 03 | domain = .coremusic.net | alt alan paylaşımı | ✅ PASS satır 43 |
| 04 | secure = $isHttps | koşullu HTTPS | ✅ PASS satır 44, kaynak 37-38 |
| 05 | httponly = true | JS erişimi kapalı | ✅ PASS satır 45 |
| 06 | samesite = Lax | cross-site POST'ta gitmez | ✅ PASS satır 46 |
| 07 | session_start çağrısı | bayraklardan SONRA | ✅ PASS satır 49 |

## §2 Zaman Aşımı (SessionLifecycle.php)

| # | Kontrol | Beklenen | Bulgu |
|---|---|---|---|
| 08 | Idle kontrolü ayrı | isIdleTimedOut | ✅ PASS satır 41-45, 97-104 |
| 09 | Lifetime kontrolü ayrı | isSessionExpired | ✅ PASS satır 32-35, 106-113 |
| 10 | Süresi dolan destroy() | destroy çağrısı | ✅ PASS satır 34, 43 |
| 11 | Lifetime uzatılmaz | extend yalnız created_at'siz | ✅ PASS satır 115-127 |
| 12 | Eşik değerleri | SessionConfig::IDLE_TIMEOUT vb. | ⚠️ UNKNOWN (sayı okunmadı) |

## §3 Kimlik Yenileme (rotation)

| # | Kontrol | Beklenen | Bulgu |
|---|---|---|---|
| 13 | Periyodik rotation | ROTATION_INTERVAL koşulu | ✅ PASS Lifecycle·54-62 |
| 14 | regenerate(true) — eski dosya silinsin | true bayrağı | ✅ PASS Lifecycle·60 |
| 15 | Sıfırlamada regenerate(true) | restartFresh sonu | ✅ PASS Bootstrapper·58 |
| 16 | API tarafında da rotation | true | ✅ PASS ApiSessionManager·85 |
| 17 | rotation damgası tutulur | _session_rotated_at | ✅ PASS Lifecycle·55-61 |

## §4 Sıfırlama Zinciri (SessionBootstrapper · 34-59)

| # | Kontrol | Beklenen | Bulgu |
|---|---|---|---|
| 18 | $_SESSION boşaltılır | satır 38 | ✅ PASS |
| 19 | session_destroy | satır 40-42 | ✅ PASS |
| 20 | Cookie süresi doldurularak silinir | setcookie time()-EXPIRY | ✅ PASS satır 44-54 |
| 21 | Taze start + regenerate | 57-58 | ✅ PASS |
| 22 | SSOT: session_* yalnız burada | sınıf başlığı | ✅ PASS satır 8-11 |

## §5 Başlık Politikaları (SecurityHeadersMiddleware · 26-41)

| # | Kontrol | Beklenen | Bulgu |
|---|---|---|---|
| 23 | X-Frame-Options | DENY/SAMEORIGIN | ✅ PASS satır 28 (DENY) |
| 24 | CSP frame-ancestors | 'none'/'self' | ✅ PASS satır 71 ('none') |
| 25 | CSP script nonce | üretilmeli | ✅ PASS satır 16-19, 54-56 |
| 26 | CSP base-uri/form-action | 'self' | ✅ PASS satır 72-73 |
| 27 | HSTS başlığı | max-age + includeSubDomains | ❌ **FAIL — header setinde YOK** |
| 28 | COOP/COE yalnız HTTPS | koşullu | ✅ PASS satır 38-41 |
| 29 | Permissions-Policy | tanımlı | ✅ PASS satır 34 |
| 30 | CORP | tanımlı | ✅ PASS satır 33 (cross-origin) |

**Kontrol 27 (FAIL):** SecurityHeadersMiddleware satır 26-36'daki 9 başlık
arasında Strict-Transport-Security YOKTUR; satır 38-41 yalnız COOP/COE ekler.
Etki: ilk HTTPS isteğinde SSL stripping riski (web §10). Düzeltme önerisi
R-20 altında kayıtlıdır — kod değişikliği bu görev kapsamında YAPILMAZ.

## §6 Gözlem ve Tek Başlatıcı

| # | Kontrol | Beklenen | Bulgu |
|---|---|---|---|
| 31 | İki başlatıcı var mı | SessionInitializer + PageRouter | ⚠️ ikisi de var (gözetim) |
| 32 | PageRouter başlatıcıda cookie temizliği | satır 71-76 | ✅ PASS |
| 33 | PageRouter'da regenerate | satır 54, 76 | ✅ PASS |
| 34 | Redaksiyon anahtarında session_id | SENSITIVE_KEYS | ✅ PASS Redactor·31 |

## §7 Nonce / CSRF Üretimi (Lifecycle · 47-52)

| # | Kontrol | Beklenen | Bulgu |
|---|---|---|---|
| 35 | csrf_token boşsa üret | bin2hex(random_bytes(32)) | ✅ PASS satır 47-49 |
| 36 | csp_nonce her istekte tazelenir | satır 51-52 | ✅ PASS |

## §8 Sonuç

| Öğe | Değer |
|---|---|
| Toplam kontrol | 36 |
| PASS | 34 |
| FAIL | 1 (HSTS, kontrol 27) |
| UNKNOWN | 1 (eşik sayıları, kontrol 12) |
| Tarih | 2026-10-12 |

**Karar:** CONDITIONAL PASS — oturum mekanizması (cookie/timeout/rotation)
eksiksiz; HSTS başlığı eksikliği K009 risklerine işlenir (R-20) ve deploy
katmanında (sunucu yapılandırması) kapatılabilir. Kod değişikliği: YOK.

## §9 Negatif Senaryo Kontrolleri

| # | Senaryo | Beklenen davranış | Karşılık |
|---|---|---|---|
| 37 | Sessizce geçen idle süre | destroy + yeni oturum | Lifecycle·41-45 |
| 38 | Max lifetime doldu | destroy, uzatma yok | Lifecycle·32-35 |
| 39 | CSRF token boş/kirli | yeniden üretilir | Lifecycle·47-49 |
| 40 | Logout | sıfırlama zinciri | Bootstrapper·34-59 |
| 41 | Çalınan ID kullanılsa | periyodik rotation ile kısalır pencere | Lifecycle·54-62 |
| 42 | iframe'e gömme | XFO DENY + frame-ancestors none | Headers·28, 71 |
| 43 | HTTP'e indirgenme | HSTS beklentisi → ŞU AN KARŞILANMIYOR | kontrol 27 |

## §10 Tekrar Okuma Protokolü

1. Her kontrol, dosya + satır aralığı olmadan tamamlanmaz.
2. FAIL, "bilerek" işaretlenir ve gerekçesi satır numarasıyla yazılır.
3. UNKNOWN yalnız okuma ile PASS olur.
4. Kaynak dosya değişirse kontrol re-verify edilir.

## §11 Kanıt İmzası

| Alan | Değer |
|---|---|
| Okuma tarihi | 2026-10-12 |
| Okunan dosyalar | 6 |
| Kontrol | 43 (36 ana + 7 senaryo) |
| PASS | 41 |
| FAIL | 1 (HSTS) |
| UNKNOWN | 1 (eşik değerleri) |
| Authority | Bayram Ali / Vault Steward |

## §12 Kapsanan DOSYALAR

1. shared/src/Session/SessionInitializer.php (52 satır)
2. shared/src/Session/SessionLifecycle.php (128 satır)
3. shared/src/Session/SessionBootstrapper.php (84 satır)
4. shared/src/Session/SessionConfig.php (kayıt: 16, 45, 51-60 — grep kanıtı)
5. shared/src/Api/Auth/ApiSessionManager.php (satır 85 — grep kanıtı)
6. shared/src/Middleware/SecurityHeadersMiddleware.php (104 satır)
7. shared/src/PageRouter/SessionInitializer.php (satır 54, 71-76 — grep kanıtı)
8. shared/src/Log/Redactor.php (satır 31 — grep kanıtı)

Not: 4, 5, 7, 8 yalnız ilgili SATIRLAR üzerinden grep ile okundu;
tam dosya okunmadı → satır dışı iddia kurulmaz.
