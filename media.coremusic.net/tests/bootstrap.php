<?php

declare(strict_types=1);

/**
 * PHPUnit bootstrap — media.coremusic.net test ortevarı.
 *
 * Güvenlik kuralları:
 *  - `MEDIA_ROOT` ASLA gerçek medya ağacına (38 GB, gitignored) işaretlenmez;
 *    her test kendi temp fixture kökünü kurar ve siler.
 *  - `shared/config/.env` yüklenmez; ConnectionFactory testlerde çağrılmaz
 *    (repositori sahtesi `tests/Fake/InMemoryMediaAccessRepository`).
 *  - Ortam değişkenleri önceden tanımlanmışsa temizlenir ki testlerin
 *    fail-closed beklentisi (kök yoksa 403) kirlenmesin.
 */

require dirname(__DIR__) . '/vendor/autoload.php';

foreach (['MEDIA_ROOT', 'MEDIA_PORT', 'DB_MEDIACATALOG_NAME', 'DB_MEDIA_NAME'] as $anahtar) {
    unset($_ENV[$anahtar], $_SERVER[$anahtar]);
    putenv($anahtar);
}