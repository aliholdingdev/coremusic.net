<?php

declare(strict_types=1);

namespace Media;

/**
 * MediaHttpException — HTTP durum + sözleşmeli enum kodu taşıyan istisna.
 *
 * `public/index.php` bunu yakalar ve `{error:{code,message}}` JSON zarfına
 * çevirir (HTML DEĞİL — Faz 2 tasarım maddesi 3). Exception mesajı yalnız
 * kendi verdiği kullanıcıya dönülebilir metindir; asla `getMessage()` ile
 * istemciye sızdırılan tutar (hata sızmaması, api CLAUDE.md §4 kural 2).
 *
 * Durum → kod eşlemesi (fail-closed):
 *   400 INVALID_FILE_KEY · 401 UNAUTHORIZED · 403 FORBIDDEN/PATH_DENIED
 *   404 NOT_FOUND · 500 INTERNAL_ERROR (yalnız yukarıdan; bu sınıf 500 üretmez)
 */
final class MediaHttpException extends \RuntimeException
{
    public function __construct(
        public readonly int $status,
        public readonly string $errorCode,
        string $message,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }
}