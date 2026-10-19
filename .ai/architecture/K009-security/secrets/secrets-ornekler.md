---
node_id: K009-security/secrets/ornekler
node_type: module
status: verify-required
parent_id: K009-security/secrets
children: []
depends_on:
  - K009-security/secrets
evidence:
  - "Redactor 28-105 · ConfigManager·53 · OAuthManager 33-39/77-78 · auth index 129-137 birebir alıntı"
validation:
  - "2026-10-12 — her alıntı dosya + satır aralığıyla doğrulandı"
open_questions:
  - "Redactor'ün çağrıldığı gerçek log yolları (FileHandler) okunmadı → dalga-3"
group: kod
---

# secrets-ornekler.md — Gerçek Kod Örnekleri

## §1 Maskeleme Tanımları — Redactor.php · 27-49

~~~php
/** Maskeleme değeri. */
public const MASK = '[REDACTED]';

/** Hassas anahtar listesi. */
private const SENSITIVE_KEYS = 'auth_key|password|passwd|csrf_token|token|secret|api_key|session_id|session|authorization|cookie';

private const KEY_VALUE_PATTERN = '/(\b(?:' . self::SENSITIVE_KEYS . ')\b["\']?\s*[=:]\s*)("[^"]*"|\'[^\']*\'|(?:Bearer|Basic|Digest)\s+[^\s,&;)\]}]+|[^\s,&;)\]}]+)/i';

private const LONG_HASH_PATTERN = '/(?<![A-Za-z0-9+\/])[A-Za-z0-9+\/]{64}(?![A-Za-z0-9+\/])/';
~~~

**Yorum:**
- satır 31: 11 anahtar | ile birleştirilmiş tek desen → tek noktada güncelleme.
- satır 40: üç değer biçimi (tırnaklı / auth-scheme / çıplak) → JSON ve
  key=value çıktılarını kapsar.
- satır 49: {64} sabiti — 64 altındaki hex token MASKLANMAZ (kontrol 14 FAIL).

## §2 Uygulama — Redactor.php · 65-93

~~~php
public static function redact(string $line): string
{
    if ($line === '') {
        return $line;
    }

    // 1) Anahtar-adı maskesi (key=value / key: value / "key":"value")
    $redacted = preg_replace_callback(
        self::KEY_VALUE_PATTERN,
        static fn (array $m): string => self::maskValue($m[1], $m[2]),
        $line
    );

    if ($redacted === null) {
        return $line;
    }

    // 2) 64-char hex/base64 hash maskesi — UUID'ler (8-4-4-4-12) korunur.
    $replaced = preg_replace(self::LONG_HASH_PATTERN, self::MASK, $redacted);

    // 3) E-posta maskesi — yerel kısım gizlenir, alan adı kalır (B-F-17).
    $withEmail = preg_replace_callback(
        self::EMAIL_PATTERN,
        static fn (array $m): string => '***@' . $m[1],
        $replaced ?? $redacted
    );

    return $withEmail ?? ($replaced ?? $redacted);
}
~~~

**Yorum:** üç desen SIRAYLA uygulanır; her adımda null dönüşü güvenli
geri düşüşle korunur (satır 78-80, 92) → maskeler hiçbir zaman sessizce
atlanmaz, satır aynen geçer (fail-safe değil, fail-visible: temiz satır
zaten değişmez).

## §3 Değer Maskesi — Redactor.php · 95-105

~~~php
private static function maskValue(string $prefix, string $value): string
{
    if ($value !== '' && ($value[0] === '"' || $value[0] === "'")) {
        return $prefix . $value[0] . self::MASK . $value[0];
    }

    return $prefix . self::MASK;
}
~~~

**Yorum:** tırnak korunur → JSON geçerliliği bozulmaz
("password":"x" → "password":"[REDACTED]").

## §4 Env Okuma — ConfigManager.php · 53

~~~php
return $_ENV[$key] ?? getenv($key) ?: $default;
~~~

**Yorum:** iki kaynak (super-global, sonra getenv) + varsayılan. Sırlar
için TEK okuma yolu budur; kod içine sabit değer yazmak bu yolun
DIŞINDADIR (kontrol 22'nin dayanağı).

## §5 Anahtar DI — OAuthManager.php · 33-39

~~~php
private string $encryptionKey;

public function __construct(\PDO $pdo, array $platformConfig, string $encryptionKey)
{
    ...
    $this->encryptionKey = $encryptionKey;
}
~~~

**Yorum (olumlu örnek):** anahtar constructor ile DIŞARIDAN gelir —
dosyada sabitlenmemiştir. Ancak çağıran taraf (container) OKUNMADI →
üretim yolu UNKNOWN (kontrol 19).

## §6 getenv ile OAuth Sırları — OAuthManager.php · 77-78

~~~php
$clientId = getenv($envKey['client_id']) ?: '';
$clientSecret = getenv($envKey['client_secret']) ?: '';
~~~

**Yorum:** env anahtar adları config'ten ($envKey), değerler ortamdan gelir
→ kodda sır yok. Boş string fallback, hatalı env'de isteği sessizce
düşürmez (sonraki adımda doğrulama beklenir).

## §7 Negatif Örnek — Kod İçi Yedek Sır (index.php · 129-137)

~~~php
if ($requestUri === '' || $requestUri === '/') {
    // Auth bypass aktifse -> direkt home'a redirect et
    if ($bypassActive) {
        $bypassKey = hash_hmac('sha256', 'bypass_' . date('Y-m-d'), defined('APP_PEPPER') ? APP_PEPPER : 'coremusic-bypass');
        $redirectUrl = (defined('MUSIC_URL') ? MUSIC_URL : 'http://home.coremusic.net:81')
            . '/auth/callback?auth_key=' . urlencode($bypassKey);
        header('Location: ' . $redirectUrl, true, 302);
        exit;
    }
~~~

**Yorum (NEGATİF, kontrol 22 FAIL):**
- Üç ihlal tek satırda: (a) sabit yedek anahtar, (b) deterministik günlük
  anahtar, (c) fallback sessizliği.
- Hafifletici: satır 60 (bypass yalnız non-prod + FORCE_AUTH_BYPASS).
- Güvenli sürüm: yedek YOK; env yoksa 500 + kayıt.

## §8 Kimlik Başlığı İznisiz — CorsMiddleware · 37-44

~~~php
$this->allowedHeaders   = $corsConfig['allowed_headers'] ?? [
    'Content-Type',
    'X-CSRF-Token',
    'X-Requested-With',
    'Authorization',
    'X-Api-Key',
];
$this->allowCredentials = $corsConfig['allow_credentials'] ?? true;
~~~

**Yorum:** runtime sırlar (Bearer, API key) yalnız preflight'te izinlenir;
cevap başlıklarında somut ACAO ile birlikte yayınlanır (·69-71).

## §9 GÜVENLİSİZ vs ÜRETİMDEKİ

~~~text
GÜVENLİSİZ                                  ÜRETİMDEKİ
──────────────────────────────────────────────────────────────
koda sabit sır                           →   env (ConfigManager·53)
her log satırını elle temizlemek         →   tek Redactor noktası (·10-11)
adı belirsiz anahtarla maskelemek        →   11 anahtar + 3 desen (·31-56)
sesli (echo) hata çıktısı                →   ⚠️ UNKNOWN (php.ini)
yedek sabit pepper                       →   ⚠️ HÂLÂ VAR (index·132)
~~~

## §10 Alıntı Sınırları

1. 5 gerçek dosyadan 8 kod bloğu.
2. Özgün koda yorum EKLENMEZ.
3. Uydurma kod: YOK · Sır tam değeri: YAZILMADI (Guardrail).
