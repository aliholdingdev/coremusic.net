---
node_id: K009-security/session-security/kapsam
node_type: module
status: verify-required
parent_id: K009-security/session-security
children: []
depends_on:
  - K009-security/session-security
evidence:
  - "SessionInitializer.php 40-47 · SessionLifecycle.php 21-127 · SessionBootstrapper.php 34-59 · SessionConfig.php 16/45/51-60 · ApiSessionManager.php 85"
validation:
  - "2026-10-12 — her § iddiası satır aralığına bağlandı"
open_questions:
  - "Zaman aşımı sayısal değerleri (saniye) okunmadı → UNKNOWN"
group: mimari
---

# session-security-kapsam.md — Oturum Güvenliği Kapsamı

## §1 Cookie Koruması (SessionInitializer.php · 40-47)

~~~php
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'domain'   => '.coremusic.net',
    'secure'   => $isHttps,
    'httponly'  => true,
    'samesite' => 'Lax',
]);
~~~

| Bayrak | Değer | Koruma |
|---|---|---|
| lifetime | 0 | oturum cookie'si — tarayıcı kapanınca silinir |
| secure | $isHttps (satır 37-38) | yalnız HTTPS |
| httponly | true | JS erişimi yok → session hijacking zorlaşır |
| samesite | Lax | cross-site POST'ta cookie gitmez → CSRF katmanı |
| domain | .coremusic.net | alt alanlar arası oturum paylaşımı (kasıtlı) |

$isHttps hesabı iki kaynağa bakar: HTTPS sunucu değişkeni ve
X-Forwarded-Proto başlığı (satır 37-38) — proxy arkasındaki üretim için.

## §2 İki Zaman Aşımı (SessionLifecycle.php)

| Tür | Kaynak satır | Koşul | Sonuç |
|---|---|---|---|
| Idle timeout | 41-45, 97-104 | now - last_active >= IDLE_TIMEOUT | destroy() |
| Lifetime | 32-35, 106-113 | now - created_at >= MAX_LIFETIME | destroy() |

Kural: ikisi de AYRI kontrol edilir; idle uzatılabilir (her istekte
extendSession, satır 37), lifetime UZATILAMAZ (satır 115-127 —_CREATED AT
değişmez). Bu, "sonsuz oturum"u imkânsız kılar.

## §3 Oturum Kimliği Yenileme (rotation)

| Tetikleyici | Konum | Davranış |
|---|---|---|
| Periyodik | Lifecycle·54-62 | ROTATION_INTERVAL süresinde session_regenerate_id(true) |
| Sıfırlama | Bootstrapper·58 | restartFresh sonunda regenerate(true) |
| API tarafı | ApiSessionManager·85 | aynı (true = eski ID anında geçersiz) |

Neden true: eski kimlik dosyası hemen silinmezse çalınan ID bir süre daha
çalışır. Neden periyodik: sabit kimlik, XSS/endpoint kaydırlarında tüm
oturum boyunca geçerlidir.

## §4 Sıfırlama Zinciri (SessionBootstrapper · 34-59)

1. $_SESSION = [] (satır 38) — bellek temizliği.
2. session_destroy (satır 40-42) — dosya temizliği.
3. setcookie eski cookie'yi süresi geçmiş olarak yaz (satır 44-54).
4. configure + session_start (satır 56-57) — taze oturum.
5. session_regenerate_id(true) (satır 58) — yeni kimlik.

Bu zincirin TEK yeri burasıdır (SSOT, satır 8-11 yorumu); login/logout
bu metodu çağırır, session_* API'sine kendisi dokunmaz.

## §5 Başlık Politikaları (tarayıcı katmanı)

| Başlık | Rol | Bu düğümde |
|---|---|---|
| X-Frame-Options | clickjacking → oturum hırsızlığı | kapsam, doğrulama §6 |
| CSP (frame-ancestors) | modern clickjacking engeli | kapsam, doğrulama §6 |
| HSTS | HTTPS indirgenmesi → cookie sızıntısı | kapsam, web §9 |
| SameSite=Lax | cross-site istekte cookie gitmesin | §1 |
| postMessage origin | oturum verisinin yanlış pencereye çıkması | §7 |

## §6 Kapsam Dışı (bu düğüm DEĞİL)

1. CSRF token üretimi → K009 kök kod-ornekleri (token Lifecycle·47-49'da
   ÜRETİLİR, doğrulaması CSRF'in işidir — devir kaydı korunur).
2. JWT süresi/simasi → authentication/ (JwtService).
3. Cookie değerinin şifrelenmesi → encryption/.

## §7 postMessage Sınırı

Oturum verisi (csrf_token, nonce) HttpOnly cookie'nin İÇİNDE taşınır ve
Window.postMessage ile pencereler arası paylaşılmaz (HtmlShellRenderer·230
notu bu kuralı teyit eder). Kural: payload'ta session_id geçmez →
web-arastirma §10 (MDN postMessage) ile çaprazlanır.

## §8 Kabul Kriterleri

1. Cookie dört bayrağı da okunabilir olmalı (§1 satır aralıkları).
2. İki timeout ayrı ayrı doğranmalı (§2).
3. Rotation üç konumda da true ile (§3).
4. Sıfırlama zinciri tek yerde (§4).
5. Sayısal eşikler bilinmiyorsa UNKNOWN yazılır — UYDURULMAZ.

## §9 Bağlı Bileşen Haritası

| Dosya | Görev | Öne çıkan satır |
|---|---|---|
| shared/src/Session/SessionConfig.php | eşik + cookie SSOT | 16 (COOKIE_EXPIRY), 45 (domain), 51-60 (params) |
| shared/src/Session/SessionInitializer.php | basitleştirilmiş başlatıcı | 26-27 (session_name), 40-47 (cookie) |
| shared/src/Session/SessionBootstrapper.php | tek yetkili giriş noktası | 19-28, 34-59, 68-83 |
| shared/src/Session/SessionLifecycle.php | timeout + rotation + nonce | 21-69, 71-75, 97-127 |
| shared/src/Api/Auth/ApiSessionManager.php | API oturumu | 85 (regenerate) |
| shared/src/PageRouter/SessionInitializer.php | eski sayfa başlatıcı | 54, 71-76 (cookie temizliği) |

İki başlatıcının varlığı (SessionInitializer + PageRouter/SessionInitializer)
gözetim gerecidir → doğrulama.md kontrol 30.

## §10 İlişkili Düğümler ve Devir

| Konu | Giden düğüm | Gerekçe |
|---|---|---|
| Parola, JWT, pepper | authentication/ | kimlik doğrulama |
| Rol/permission kararı | authorization/ | yetki |
| HTTPS/TLS, şifreleme | encryption/ | taşıma güvenliği |
| Login deneme limiti | rate-limiting/ | sıklık |
| Oturum olaylarının kaydı | audit (dalga-3) | gözlem |

Devir kuralı: bu düğüm oturumun ÖMRÜNÜ yönetir; içeriğinin doğruluğu
(authentication) ve kullanıcının ne yapabildiği (authorization) buraya girmez.
