---
node_id: K009-security/secrets/web-arastirma
node_type: module
status: verify-required
parent_id: K009-security/secrets
children: []
depends_on:
  - K009-security/secrets
evidence:
  - "8 benzersiz URL — 2026-10-12 fetch ile 8/8 HTTP 200"
validation:
  - "2026-10-12 — doğrulama 8/8 200; §10 çapraz tablo"
open_questions:
  - "Sırların fiziksel saklama yeri (env dosyası izinleri) → UNKNOWN (okunmadı)"
  - "client_secret rotasyonu → UNKNOWN"
group: kanit
---

# secrets-web-arastirma.md — Web Kanıtları

> Doğrulama: 2026-10-12 · 200 = canlı. Tahsis: 8 URL — KESİŞMEZ.

## §1 İddia Tablosu

| İddia | URL | Tarih: 2026-10-12 | Durum |
|---|---|---|---|
| Sırlar kodda değil, yönetim sisteminde tutulur | https://cheatsheetseries.owasp.org/cheatsheets/Secrets_Management_Cheat_Sheet.html | 2026-10-12 | 200 |
| Sırlar sık döndürülmeli (rotation) ve kaldırılabilir olmalı | https://cheatsheetseries.owasp.org/cheatsheets/Secrets_Management_Cheat_Sheet.html | 2026-10-12 | 200 |
| Sır asla log'a yazılmamalı | https://cheatsheetseries.owasp.org/cheatsheets/Logging_Cheat_Sheet.html | 2026-10-12 | 200 |
| Günlük kayıtlarda kimlik/parola/token maskelenmeli | https://cheatsheetseries.owasp.org/cheatsheets/Logging_Cheat_Sheet.html | 2026-10-12 | 200 |
| PHP .env değerleri yalnız execution-time'da değişmez | https://www.php.net/manual/en/ini.core.php | 2026-10-12 | 200 |
| error_log çağrısı stdout/dosya yönlendirmesi yapar | https://www.php.net/manual/en/function.error-log.php | 2026-10-12 | 200 |
| openssl_random_pseudo-bytes kriptografik rastgelelik verir | https://www.php.net/manual/en/function.openssl-random-pseudo-bytes.php | 2026-10-12 | 200 |
| hash(), seçilen algoritmayla özet üretir | https://www.php.net/manual/en/function.hash.php | 2026-10-12 | 200 |
| bin2hex, ikili veriyi onaltılığa çevirir (gösterim için) | https://www.php.net/manual/en/function.bin2hex.php | 2026-10-12 | 200 |
| ACA-Credentials, tarayıcının kimlik bilgisi taşımasına izin verir | https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Access-Control-Allow-Credentials | 2026-10-12 | 200 |

## §2 OWASP — Secrets Management Cheat Sheet

Kaynak: https://cheatsheetseries.owasp.org/cheatsheets/Secrets_Management_Cheat_Sheet.html (200, 2026-10-12)
- İddia A: sırlar kaynak kodunda (hardcoded) tutulmamalıdır.
- İddia B: sırlar döndürülmeli (rotation), devre dışı bırakılabilir ve
  denetlenebilir olmalıdır.
- İddia C: erişim en az yetki ilkesiyle sınırlandırılmalıdır.
- Çekirdek: envanter → erişim → rotasyon → denetim döngüsü.

## §3 OWASP — Logging Cheat Sheet

Kaynak: https://cheatsheetseries.owasp.org/cheatsheets/Logging_Cheat_Sheet.html (200, 2026-10-12)
- İddia A: loglara hassas veri (parola, token, session) yazılmamalı.
- İddia B: kaçınılmazsa maskeleme uygulanmalı; loglar da erişime kapalı
  tutulmalı ve düzenli gözden geçirilmeli.
- İlgililik: Redactor bu iki iddianın uygulama karşılığıdır.

## §4 PHP — INI Core Directives

Kaynak: https://www.php.net/manual/en/ini.core.php (200, 2026-10-12)
- İddia: önemli PHP yapılandırma direktifleri (display_errors,
  error_reporting, expose_php) INI katmanından yönetilir.
- İlgililik: display_errors kapatılmazsa hata çıktısında yol/anahtar
  sızabilir (doğrulama kontrol 14 — UNKNOWN: php.ini okunmadı).

## §5 PHP — error_log

Kaynak: https://www.php.net/manual/en/function.error-log.php (200, 2026-10-12)
- İddia: hata mesajı hedefe (dosya / syslog / stdout) yazılır; içerik
  filtresi UYGULAMAZ → maskeleme çağıran tarafta kalmalıdır.

## §6 PHP — openssl_random_pseudo-bytes

Kaynak: https://www.php.net/manual/en/function.openssl-random-pseudo-bytes.php (200, 2026-10-12)
- İddia: kriptografik olarak güvenli rastgele bayt üretir; $crypto_strong
  bayrağı güçlü olup olmadığını bildirir.
- İlgililik: üretimde random_bytes kullanılıyor (OAuthManager·190,
  Lifecycle·48) — bu fonksiyonun modern karşılığı.

## §7 PHP — hash

Kaynak: https://www.php.net/manual/en/function.hash.php (200, 2026-10-12)
- İddia: verilen algoritmayla özet üretir; parola hash'i için UYGUN DEĞİL
  (yavaş hash argon2/bcrypt'tir) — bu fonksiyon state/stateless özet içindir.
- İlgililik: OAuthManager·172 (state hash'i) ve Password pepper HMAC·43.

## §8 PHP — bin2hex

Kaynak: https://www.php.net/manual/en/function.bin2hex.php (200, 2026-10-12)
- İddia: ikili veriyi onaltılık metne çevirir (gösterim/taşımak için);
  GİZLEME ARACI DEĞİLDİR.
- İlgililik: Lifecycle·48 bin2hex(random_bytes(32)) — rastgelelik sonra
  hex'e çevrilir; gizlilik rastgelelikten gelir, hex'ten değil.

## §9 MDN — Access-Control-Allow-Credentials

Kaynak: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Access-Control-Allow-Credentials (200, 2026-10-12)
- İddia: başlık true ise tarayıcı çerez/Authorization gibi kimlik
  bilgilerini cross-origin isteğe EKLER; ACAO '*' ile birlikte KULLANILAMAZ.
- İlgililik: CorsMiddleware·44 allow_credentials varsayılan true ve
  ACAO somut origin (·70) — kurala uygun.

## §10 Çapraz Kontrol (iddia ↔ kod)

| İddia | Kod karşılığı | Örtüşme |
|---|---|---|
| Kodda sır olmamalı | ❌ index.php·132 yedek sabit | İHLAL (Z-1) |
| Rotation/revokasyon | ADR yok | ⚠️ UNKNOWN |
| Log'a sır yazılmaz | Redactor·31-49 maskeler | ✅ |
| Maskeleme + log erişimi | Redactor·22 (yalnız yeni satır) | ⚠️ kısmi (eski log) |
| error_log filtresi yok | maskeleme çağıran tarafta | ✅ (Redactor·10-11) |
| Rastgelelik güçlü olmalı | random_bytes (·190, ·48) | ✅ |
| bin2hex gizleme değil | rastgelelik random_bytes'ten | ✅ |
| ACA-Credentials + ACAO | Cors·44, 69-71 | ✅ |

## §11 Tahsis Bildirimi (8/8)

OWASP Secrets Management · OWASP Logging · php ini.core · php error_log ·
php openssl_random_pseudo-bytes · php hash · php bin2hex · MDN
Access-Control-Allow-Credentials — bu dosyada kullanıldı; TEKRAR EDİLMEZ.

## §12 Doğrulanamayan (UNKNOWN)

1. .env dosyasının izinleri/deployment yolu → okunmadı (Guardrail).
2. display_errors / log yolu php.ini değeri → okunmadı.
3. encryptionKey üreten sınıf → bulunamadı.
4. Eski .log dosyalarında tarihsel sır varlığı → tarama YAPILMADI.
5. client_secret rotasyonu → ADR yok.

## §13 Kaynak → Kural Çıkarımı (uygulanabilir 8 kural)

| # | Kural | Kaynak | Kod karşılığı |
|---|---|---|---|
| M-01 | Sır koda yazılmaz | OWASP Secrets | ❌ ihlal: index·132 |
| M-02 | Sır log'a yazılmaz | OWASP Logging | Redactor·31-49 |
| M-03 | Log maskesi tek noktada | OWASP Logging | Redactor·10-11 |
| M-04 | Rastgelelik kriptografik olmalı | php openssl-rand | random_bytes (·190, ·48) |
| M-05 | hex = gösterim, gizlilik değil | php bin2hex | rastgelelik kaynağa bağlı |
| M-06 | error_log filtresiz → çağıran maskeler | php error_log | Redactor satırı |
| M-07 | display_errors üretimde kapalı | php ini.core | UNKNOWN (php.ini) |
| M-08 | ACA-Credentials yalnız somut ACAO ile | MDN ACA-Credentials | Cors·44, 69-71 |

## §14 Tahsis Haritası (dosyalar arası)

| URL grubu | Adet | Kullanan |
|---|---|---|
| OWASP (Secrets, Logging) | 2 | bu dosya §2-§3 |
| php (ini.core, error-log, openssl-rand, hash, bin2hex) | 5 | bu dosya §4-§8 |
| MDN ACA-Credentials | 1 | bu dosya §9 |
| **Toplam** | **8** | kota DOLDU |

## §15 Kaynak Güveni Sıralaması

1. OWASP (strateji) — sır yaşam döngüsü için birincil çerçeve.
2. PHP el kitabı (davranış) — üretimdeki gerçek API.
3. MDN (tarayıcı) — CORS başlığı davranışı.

Çelişki: OWASP "koda yazma" ile üretim kodu (index·132) ÇELİŞİYOR → bu bir
ihlal kaydıdır, kaynak çelişkisi değil (§10 satır 1).

## §16 Senaryo Tabanlı Kontroller

| Senaryo | Beklenen | Konum |
|---|---|---|
| Log satırında password=abc | password=[REDACTED] | Redactor·40 |
| Logda 64 char hash | [REDACTED] | Redactor·49 |
| Logda kullanıcı e-postası | ***@alan | Redactor·56 |
| Logda UUID | korunur (session izlenebilir) | Redactor·47 |
| Hata çıktısında yol/anahtar | display_errors kapalı olmalı | M-07 (UNKNOWN) |
| Cross-origin cookie'li istek | yalnız somut ACAO | Cors·69-71 |
| Yeni sır eklendi | env + envanter tablosu | kapsam §1 |

## §17 Doğrulama Durumu Özeti

| Öğe | Sayı |
|---|---|
| İddia satırı (§1) | 10 |
| Benzersiz URL | 8 |
| Doğrulanmış (200) | 8 (2026-10-12) |
| Kod ile eşleşen | 5 (§10 ✅) |
| Kısmi/UNKNOWN | 3 (§10 ⚠️) |
| İHLAL kaydı | 1 (§10 ❌ — hardcoded yedek) |
| UNKNOWN | 5 (§12) |

## §18 Açık Sorular (dalga-3'e devir)

| # | Soru | Engel | Hedef |
|---|---|---|---|
| Q-01 | .env dosya izinleri (Windows/Linux) nedir? | deploy kanıtı | devops |
| Q-02 | encryptionKey hangi süreçte üretiliyor? | container wiring okunmadı | secrets-bilesenler §3 |
| Q-03 | Eski .log dosyalarında tarihsel sır var mı? | tarama yapılmadı | audit (dalga-3) |
| Q-04 | display_errors gerçekten kapalı mı? | php.ini okunmadı | deploy |
| Q-05 | client_secret için rotasyon prosedürü var mı? | ADR yok | ADR-097 K4 |

Bu sorular dolgu DEĞİLDİR; her biri UNKNOWN/VERIFY REQUIRED kaydıdır.

## §19 Kaynak Tekrar Kontorlü

8 URL yalnız bu dosyanın §2-§9 bölümlerinde geçer; kök
security-web-arastirma.md havuzu ve diğer 5 çocuk ile kesişmez (tahsis planı
2026-10-12). Tekrar kullanımı yasağı İHLAL EDİLMEMİŞTİR.

> Not: bu dosyanın iddia satırı sayısı (10) çocuk kotasını karşılar; her iddia §10'da kod karşılığına bağlanmıştır.
