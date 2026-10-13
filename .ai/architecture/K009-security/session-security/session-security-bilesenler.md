---
node_id: K009-security/session-security/bilesenler
node_type: module
status: verify-required
parent_id: K009-security/session-security
children: []
depends_on:
  - K009-security/session-security
evidence:
  - "shared/src/Session/ (4 dosya) + Api/Auth/ApiSessionManager + PageRouter/SessionInitializer"
validation:
  - "2026-10-12 — glob ile dosya varlıkları doğrulandı"
open_questions:
  - "SessionConfig eşik değerleri (sayı) → UNKNOWN"
  - "İki başlatıcının (SessionInitializer vs PageRouter) aktif kullanım senaryosu → dalga-3"
group: mimari
---

# session-security-bilesenler.md — Kademe-3 Planı

## §1 Kademe-3 Bileşen Listesi

| # | Bileşen | Dosya | Görev |
|---|---|---|---|
| B-01 | OturumYapılandırması | shared/src/Session/SessionConfig.php | eşik + cookie SSOT |
| B-02 | Başlatıcı | shared/src/Session/SessionInitializer.php | idempotent start |
| B-03 | Önyükleyici | shared/src/Session/SessionBootstrapper.php | tek yetkili session_* |
| B-04 | YaşamDöngüsü | shared/src/Session/SessionLifecycle.php | timeout, rotation, nonce |
| B-05 | API Oturumu | shared/src/Api/Auth/ApiSessionManager.php | API tarafı kimlik |
| B-06 | Sayfa Başlatıcı | shared/src/PageRouter/SessionInitializer.php | legacy başlatıcı |
| B-07 | Başlık Katmanı | shared/src/Middleware/SecurityHeadersMiddleware.php | CSP/XFO/HSTS |

## §2 Çağrı Grafiği

~~~text
İstek
  → SecurityHeadersMiddleware (nonce üretir, satır 16-19)
  → SessionManagerMiddleware / SessionInitializer::ensureStarted
      → SessionBootstrapper::ensureStarted (config + start, satır 19-28)
  → SessionLifecycle::startOrExtend (timeout + rotation, satır 21-69)
      → isSessionExpired / isIdleTimedOut
      → destroy() → Bootstrapper::restartFresh (34-59)
  → İşlem
  → SessionBootstrapper::writeClose (61-66)
~~~

## §3 Katman Sınırı

| Katman | Bileşen | Kural |
|---|---|---|
| Presentation | session cookie başlığı (Set-Cookie) | yalnız buradan çıkar |
| Application | SessionOrchestrator (login/logout akışı) | iş mantığı |
| Domain | oturum politikası kavramı (timeout kuralı) | framework bilmez |
| Infrastructure | B-01…B-06 | PHP session_* API'sinin tek sahibi |

SSOT kuralı: session_name / session_set_cookie_params / session_save_path /
session_start YALNIZCA SessionBootstrapper içindedir (sınıf başlığı satır 8-11).

## §4 Dosya Haritası (kademe-4)

~~~text
shared/src/Session/
  SessionConfig.php          (16 COOKIE_EXPIRY, 45 domain, 51-60 params)
  SessionInitializer.php     (20-51)
  SessionBootstrapper.php    (19-83)
  SessionLifecycle.php       (21-127)
shared/src/Api/Auth/
  ApiSessionManager.php      (85 rotation)
shared/src/PageRouter/
  SessionInitializer.php     (54, 71-76)
shared/src/Middleware/
  SecurityHeadersMiddleware.php (16-19, 26-41, 48-74)
shared/src/Log/Redactor.php  (31 session_id redaksiyonu)
tests/Security/              (adlar → UNKNOWN)
~~~

## §5 Bağımlılık Tablosu

| Kaynak | Hedef | Tür |
|---|---|---|
| SessionLifecycle | SessionBootstrapper | statik çağrı |
| SessionLifecycle | SessionConfig | sabit okuma |
| SessionInitializer | PHP session_* | platform API |
| SecurityHeaders | Session nonce değeri | request alanı (_csp_nonce) |
| ApiSessionManager | SessionConfig | paylaşılan cookie/save_path |
| PageRouter/SessionInitializer | SessionConfig | eşik paylaşımı |
| Tümü | Log/Redactor | redaksiyon (session_id) |

## §6 Test Yüzeyi Matrisi

| Bileşen | Test türü | Senaryo (doğrulama §9) |
|---|---|---|
| B-01 | unit | cookieParams bütünlüğü |
| B-02 | unit | tekrarlı çağrı idempotent |
| B-03 | unit | 40, 41, 42 (sıfırlama) |
| B-04 | unit | 37, 38, 39 (timeout/nonce) |
| B-05 | unit | 16 (API rotation) |
| B-07 | unit + header | 42, 43 (XFO/HSTS) |

## §7 Tek Başlatıcı Riski (kademe-3 kararı bekleyen)

İki başlatıcı vardır: shared/src/Session/SessionInitializer.php ve
shared/src/PageRouter/SessionInitializer.php (ikisi de regenerate çağırıyor).
Kademe-3 kararı: tek giriş noktası B-03 (Bootstrapper) olmalı, diğeri
ince adaptör kalmalıdır — ADR gerektirir (dalga-3, doğrulama kontrol 31).

## §8 Bilinen Eksik (bu kademe)

| Eksik | Etki | Konum |
|---|---|---|
| HSTS başlığı yok | SSL stripping | SecurityHeaders·26-41 (kontrol 27) |
| Eşik değerleri okunmadı | politika doğrulanamıyor | SessionConfig (kontrol 12) |
| Test dosyası adları | kapsam eksisi | tests/ → UNKNOWN |

## §9 Değişiklik Riski

| Dosya | Risk | Gerekçe |
|---|---|---|
| SessionBootstrapper | Yüksek | tüm oturumlar buradan geçer |
| SessionLifecycle | Yüksek | timeout + rotation |
| SessionInitializer | Orta | yalnız başlatma |
| SecurityHeaders | Yüksek | tüm yanıtlar |

Kural: bu dosyalarda değişiklik → security-audit workflow (shared AGENTS.md §3).
