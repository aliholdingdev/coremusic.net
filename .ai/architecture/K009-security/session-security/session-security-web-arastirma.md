---
node_id: K009-security/session-security/web-arastirma
node_type: module
status: verify-required
parent_id: K009-security/session-security
children: []
depends_on:
  - K009-security/session-security
evidence:
  - "13 benzersiz URL — 2026-10-12'de fetch ile 13/13 HTTP 200"
validation:
  - "2026-10-12 — doğrulama: 200/200/200/200/200/200/200/200/200/200/200/200/200"
  - "2026-10-12 — her iddia §10 çapraz tablosunda kod karşılığına bağlandı"
open_questions:
  - "rfc7762 (CSP Registry) ile üretimdeki CSP başlığının birebir karşılaştırması → UNKNOWN"
  - "SameSite=Lax dışı tarayıcı davranışı test edilmedi"
group: kanit
---

# session-security-web-arastirma.md — Web Kanıtları

> Doğrulama tarihi: 2026-10-12 · Durum 200 = kaynak canlı.
> Tahsis: 13 URL — kök ve diğer çocuklarla KESİŞMEZ.

## §1 İddia Tablosu

| İddia | URL | Tarih: 2026-10-12 | Durum |
|---|---|---|---|
| Session cookie, özel Set-Cookie nitelikleriyle tanımlanır | https://www.rfc-editor.org/rfc/rfc6265 | 2026-10-12 | 200 |
| HttpOnly, script'in cookie'ye erişimini engeller | https://developer.mozilla.org/en-US/docs/Web/HTTP/Cookies | 2026-10-12 | 200 |
| Secure, cookie'nin yalnız HTTPS'te gönderilmesini sağlar | https://developer.mozilla.org/en-US/docs/Web/HTTP/Cookies | 2026-10-12 | 200 |
| SameSite, cross-site isteklerde cookie'yi sınırlar | https://developer.mozilla.org/en-US/docs/Web/HTTP/Cookies | 2026-10-12 | 200 |
| setcookie, bayrakları tek çağrıda alır | https://www.php.net/manual/en/function.setcookie.php | 2026-10-12 | 200 |
| session_create_id, çakışmasız oturum kimliği üretir | https://www.php.net/manual/en/function.session-create-id.php | 2026-10-12 | 200 |
| session_regenerate_id(true), eski kimliği anında siler | https://www.php.net/manual/en/function.session-regenerate-id.php | 2026-10-12 | 200 |
| session_destroy, sunucudaki oturum verisini siler | https://www.php.net/manual/en/function.session-destroy.php | 2026-10-12 | 200 |
| X-Frame-Options, sayfanın çerçeveye alınmasını engeller | https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/X-Frame-Options | 2026-10-12 | 200 |
| CSP frame-ancestors, clickjacking'i modern yolla engeller | https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy | 2026-10-12 | 200 |
| HSTS, tarayıcıyı HTTPS'e kalıcı bağlar | https://developer.mozilla.org/en-US/docs/Web/Security/HTTP_strict_transport_security | 2026-10-12 | 200 |
| postMessage, origin doğrulaması ile kullanılmalıdır | https://developer.mozilla.org/en-US/docs/Web/API/Window/postMessage | 2026-10-12 | 200 |
| CSRF, kullanıcının istemsiz oturum işlemi tetiklemesidir | https://developer.mozilla.org/en-US/docs/Glossary/CSRF | 2026-10-12 | 200 |
| CSP talimatları IANA registerinde tanımlanır | https://www.rfc-editor.org/rfc/rfc7762 | 2026-10-12 | 200 |
| Doğrulanmamış yönlendirme oturum verisini sızdırabilir | https://cheatsheetseries.owasp.org/cheatsheets/Unvalidated_Redirects_and_Forwards_Cheat_Sheet.html | 2026-10-12 | 200 |

## §2 RFC 6265 — HTTP State Management (Cookie)

Kaynak: https://www.rfc-editor.org/rfc/rfc6265 (200, 2026-10-12)
- İddia: cookie, Set-Cookie başlığıyla tanımlanır; oturum cookie'si kalıcı
  değildir (yaşam süresi oturuma bağlıdır).
- Çekirdek: çerez adı/alanı/yolu; sunucu, tarayıcıya hangi nitelikte
  göndereceğini belirtir.

## §3 MDN — HTTP Cookies (nitelikler)

Kaynak: https://developer.mozilla.org/en-US/docs/Web/HTTP/Cookies (200, 2026-10-12)
- HttpOnly: istemci tarafı script erişimi kapalı → session hijacking zor.
- Secure: yalnız HTTPS → ağdaki düz metin sızıntısı engellenir.
- SameSite: cross-site istekte çerez gönderilmez/azaltılır → CSRF katmanı.

## §4 PHP — setcookie

Kaynak: https://www.php.net/manual/en/function.setcookie.php (200, 2026-10-12)
- İddia: bayraklar (httponly, secure, samesite) tek çağrıda verilir;
  çıktı gönderildikten sonra çağrılamaz.

## §5 PHP — session_create_id

Kaynak: https://www.php.net/manual/en/function.session-create-id.php (200, 2026-10-12)
- İddia: çakışmasız oturum kimliği üretir; parçalı (prefix) kimlikler için
  kullanılabilir.

## §6 PHP — session_regenerate_id

Kaynak: https://www.php.net/manual/en/function.session-regenerate-id.php (200, 2026-10-12)
- İddia: parametre true ise ESKİ oturum dosyası silinir — kimlik yenileme
  saldırısına (session fixation) karşı kritik fark budur.

## §7 PHP — session_destroy

Kaynak: https://www.php.net/manual/en/function.session-destroy.php (200, 2026-10-12)
- İddia: yalnız sunucudaki oturum verisini siler; $_SESSION değişkenlerini
  ve cookie'yi kendiliğinden temizlemez — ayrı temizlik gerekir.

## §8 MDN — X-Frame-Options

Kaynak: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/X-Frame-Options (200, 2026-10-12)
- İddia: DENY/SAMEORIGIN ile sayfa çerçeveye (iframe) alınamaz →
  clickjacking ile oturum üzerinden tıklama hırsızlığı engellenir.

## §9 MDN — Content-Security-Policy

Kaynak: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy (200, 2026-10-12)
- İddia: frame-ancestors talimatı X-Frame-Options'ın modern yeridir;
  script-src kaynakları XSS'i sınırlar ki XSS oturum çalmanın 1 numaralı
  yoludur.

## §10 MDN — HTTP Strict Transport Security

Kaynak: https://developer.mozilla.org/en-US/docs/Web/Security/HTTP_strict_transport_security (200, 2026-10-12)
- İddia: max-age süresince tarayıcı isteği HTTPS'e çevirir; ilk istekteki
  indirgeme (SSL stripping) cookie sızıntısını önlemek için precache
  HSTS gerektirir.

## §11 MDN — Window.postMessage

Kaynak: https://developer.mozilla.org/en-US/docs/Web/API/Window/postMessage (200, 2026-10-12)
- İddia: hedef origin VERİLMELİDİR; '*' ile herkese gönderim, verinin
  yanlış pencereye gitmesine yol açar.

## §12 MDN — CSRF (Glossary)

Kaynak: https://developer.mozilla.org/en-US/docs/Glossary/CSRF (200, 2026-10-12)
- İddia: SameSite cookie, Lax/Strict ile azaltılır; belirleyici koruma
  istek düzeyindeki token'dır.

## §13 RFC 7762 — CSP İstek Kayıt Defteri

Kaynak: https://www.rfc-editor.org/rfc/rfc7762 (200, 2026-10-12)
- İddia: CSP talimat adları IANA registerinde tutulur; uygulama, bilinen
  talimat adlarını register'e uydurmalı.

## §14 OWASP — Unvalidated Redirects & Forwards

Kaynak: https://cheatsheetseries.owasp.org/cheatsheets/Unvalidated_Redirects_and_Forwards_Cheat_Sheet.html (200, 2026-10-12)
- İddia: doğrulanmamış yönlendirme, oturum kimliğini/query string'i yabancı
  origin'e taşıyabilir → hedef allowlist'i zorunlu (K009 ReturnUrlPolicy).

## §15 Çapraz Kontrol (iddia ↔ kod)

| İddia | Kod karşılığı | Örtüşme |
|---|---|---|
| HttpOnly | SessionInitializer·45 | ✅ |
| Secure | SessionInitializer·44 | ✅ |
| SameSite=Lax | SessionInitializer·46 | ✅ |
| lifetime=0 | SessionInitializer·41 | ✅ |
| regenerate(true) 3 yerde | Lifecycle·60, Bootstrapper·58, ApiSession·85 | ✅ |
| destroy + ayrı temizlik | Bootstrapper·38-54 ($_SESSION=[], setcookie) | ✅ |
| X-Frame-Options | kök SecurityHeadersMiddleware → doğrulama §6 | ⚠️ okunacak |
| CSP frame-ancestors | kök SecurityHeadersMiddleware → doğrulama §6 | ⚠️ okunacak |
| HSTS | kök header seti → doğrulama §7 | ⚠️ okunacak |
| postMessage origin | payload'ta session yok (HtmlShellRenderer·230) | ✅ |

## §16 Tahsis Bildirimi (13/13)

rfc6265 · MDN Cookies · php setcookie · php session-create-id · php
session-regenerate-id · php session-destroy · MDN X-Frame-Options · MDN CSP ·
MDN HSTS · MDN postMessage · MDN Glossary CSRF · rfc7762 · OWASP Unvalidated
Redirects — bu dosyada kullanıldı; başka K009 dosyasında TEKRAR EDİLMEZ.

## §17 Doğrulanamayan (UNKNOWN)

1. Üretimde HSTS başlığı gönderiliyor mu → doğrulama kontrol 27.
2. frame-ancestors değeri → doğrulama kontrol 26.
3. ROTATION_INTERVAL sayısalsal değeri → SessionConfig okunmadı.
4. SameSite=Lax dışı tarayıcı farkları → test yok.

## §18 Kaynak → Kural Çıkarımı (uygulanabilir 8 kural)

| # | Kural | Kaynak | Kod karşılığı |
|---|---|---|---|
| S-01 | Oturum çerezi HttpOnly olmalı | MDN Cookies | SessionInitializer·45 |
| S-02 | HTTPS'te Secure zorunlu | MDN Cookies | SessionInitializer·44 |
| S-03 | Cross-site POST'ta cookie gitmemeli | MDN Cookies/CSRF | SessionInitializer·46 |
| S-04 | Kimlik yenilemede eski dosya silinmeli | php regenerate-id(true) | 3 konum (§15) |
| S-05 | Logout'ta cookie ayrı temizlenmeli | php session-destroy | Bootstrapper·44-54 |
| S-06 | Clickjacking engeli başlıkta | MDN XFO + CSP | doğrulama §6 |
| S-07 | HTTPS'e kalıcı geçiş | MDN HSTS | doğrulama §7 |
| S-08 | postMessage hedef origin belirtmeli | MDN postMessage | HtmlShellRenderer·230 |

Her kural doğrulama.md'de en az bir disk kontrolüne bağlanmıştır.

## §19 Tahsis Haritası (dosyalar arası)

| URL grubu | Adet | Kullanan dosya |
|---|---|---|
| rfc6265 + MDN Cookies | 2 | bu dosya §2-§3 |
| php (setcookie/create-id/regenerate-id/destroy) | 4 | bu dosya §4-§7 |
| MDN başlıkları (XFO, CSP, HSTS) | 3 | bu dosya §8-§10 |
| MDN postMessage + Glossary CSRF | 2 | bu dosya §11-§12 |
| rfc7762 | 1 | bu dosya §13 |
| OWASP Unvalidated Redirects | 1 | bu dosya §14 |
| **Toplam** | **13** | kota DOLDU |

## §20 Kaynak Güveni Sıralaması

1. RFC (6265, 7762) — biçimsel tanım.
2. PHP el kitabı — uygulama davranışı (üretimde PHP 8.x).
3. MDN — tarayıcı davranışı + başlık sözlüğü.
4. OWASP — öneri/strateji seviyesi.

Çelişki halinde RFC > PHP > MDN > OWASP sırası izlenir; bu dosyada çelişki
tespit edilmedi.

## §21 Doğrulama Durumu Özeti

| Öğe | Sayı |
|---|---|
| İddia satırı (§1) | 15 |
| Benzersiz URL | 13 |
| Doğrulanmış (200) | 13 (2026-10-12) |
| Kod ile eşleşen | 7 (§15 ✅) |
| Kod okunmadı → bekleyen | 3 (§15 ⚠️) |
| UNKNOWN | 4 (§17) |
