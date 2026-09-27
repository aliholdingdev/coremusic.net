<?php declare(strict_types=1);

namespace CoreMusic\Log;

/**
 * Redactor — merkezi log maskeleme katmanı.
 *
 * Kural: .ai/AGENTS.md §17 Edge Case 3 — "Sensitive data log'da → [REDACTED] ile maskeleme".
 *
 * Tüm log yazma yolları (FileHandler, StructuredLogger, PageRouterKernel fatal)
 * satırı BURADAN geçirir; tek maskeleme noktası.
 *
 * Kurallar:
 *  1. Hassas anahtar-adı eşleşmesi → değer [REDACTED]
 *     (auth_key, password, passwd, csrf_token, token, secret, api_key,
 *      session_id, session, authorization, cookie) — case-insensitive.
 *     Biçimler: key=value, key: value, "key":"value" (JSON/dizi çıktısı),
 *     "authorization: Bearer <token>" (çok kelimeli auth değeri).
 *  2. 64-char hex / base64 hash → [REDACTED].
 *  3. UUID (8-4-4-4-12) KORUNUR — tireli kısa segmentleri hash kuralına girmez.
 *
 * Maskeleme yalnızca YAZILACAK satırı etkiler; geçmiş .log dosyalarına dokunulmaz.
 * Loglama seviyeleri değişmez.
 */
final class Redactor
{
    /** Maskeleme değeri. */
    public const MASK = '[REDACTED]';

    /** Hassas anahtar listesi. */
    private const SENSITIVE_KEYS = 'auth_key|password|passwd|csrf_token|token|secret|api_key|session_id|session|authorization|cookie';

    /**
     * Grup 1 = anahtar + (varsa kapanış tırnağı) + ayraç + boşluk,
     * Grup 2 = değer: tırnaklı | auth-scheme + token | tek kelimelik.
     *
     * Tırnaklı değerlerde tırnak yapısı korunur: "password":"x" → "password":"[REDACTED]".
     * Tırnaksız değer boşluk/&/,/;/)]} ile kesilir → URL sorgu string'i güvenli.
     */
    private const KEY_VALUE_PATTERN = '/(\b(?:' . self::SENSITIVE_KEYS . ')\b["\']?\s*[=:]\s*)("[^"]*"|\'[^\']*\'|(?:Bearer|Basic|Digest)\s+[^\s,&;)\]}]+|[^\s,&;)\]}]+)/i';

    /**
     * Tam sınırıyla 64-char hex veya base64 hash.
     * Sınır, harf/digit/+// dışında karakterdir → \b yerine lookaround kullanılır
     * (base64 sonu +/- ile bittiğinde \b yanlışlıkla eşleşmezdi).
     * `=` sınırda tutulmaz: `hash=<64hex>` gibi `key=` değerleri maskelenebilmeli.
     * UUID tire içerdiği için bu kurala girmez.
     */
    private const LONG_HASH_PATTERN = '/(?<![A-Za-z0-9+\/])[A-Za-z0-9+\/]{64}(?![A-Za-z0-9+\/])/';

    private function __construct()
    {
    }

    /**
     * Verilen satırı maskeler; temiz satırlar aynen döner (false-positive yok).
     */
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

        return $replaced ?? $redacted;
    }

    /**
     * Anahtarı ve ayracı koruyor, yalnızca değeri maskeliyor.
     */
    private static function maskValue(string $prefix, string $value): string
    {
        if ($value !== '' && ($value[0] === '"' || $value[0] === "'")) {
            return $prefix . $value[0] . self::MASK . $value[0];
        }

        return $prefix . self::MASK;
    }
}
