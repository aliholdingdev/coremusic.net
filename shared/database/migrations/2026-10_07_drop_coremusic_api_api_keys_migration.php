<?php declare(strict_types=1);

/**
 * CoreMusic — DROP coremusic_api.api_keys (P2-15 / A-F05 single-writer kararı)
 *
 * KARAR (kullanıcı, 2026-10-07): "Auth sahip, api'dekini düşür."
 *   - Tek yazar = coremusic_auth.api_keys (ApiKeyRepository::DB_KEY = 'auth';
 *     shared/src/Repository/ApiKeyRepository.php:10 ADR-020 §2.2-1).
 *   - Kod taraması: coremusic_api kendi api_keys tablosuna HİÇ dokunmuyor
 *     (grep api_keys = 0 hits api.coremusic.net/include) → DROP kod kırıcı değil.
 *   - ADR-040 single-writer kuralı restored edilir.
 *
 * ⚠️ DESTRUCTIVE — bu dosya YALNIZCA onayla çalıştırılır:
 *   1) Önce yedek: mysqldump coremusic_api api_keys > backup-2026-10-07-api_keys.sql
 *   2) Çalıştırma onayı: kullanıcı (Vault Steward) açık onayı AYRİ istenir.
 *   3) Geri dönüş: yedekten CREATE TABLE + INSERT (dump dosyası).
 *
 * ÖN KOŞUL: coremusic_api.api_keys içinde veri YOKSA veya kopya ise —
 * çalıştırma ÖNCESİ satır sayısı salt-okunur kontrol edilir (aşağıdaki
 * precheck sorgusu), eşit/az ise güvenli.
 *
 * @see [[.decisions/accepted/ADR-040-database-authority]] (single-writer)
 * @see [[.decisions/accepted/ADR-020-api-public-security]]
 */

$precheck = [
    // Salt-okunur: DROP öncesi kopya tablo satır sayısı + auth'taki gerçek tablo
    // ile karşılaştırma operatör tarafından gözlemlenmeli.
    'SELECT COUNT(*) AS cnt FROM coremusic_api.api_keys',
    'SELECT COUNT(*) AS cnt FROM coremusic_auth.api_keys',
];

$queries = [

// ============================================================
// coremusic_api.api_keys — yetkisiz ikinci kopya (single-writer ihlali)
// Kod referansı YOK (tüm okuma/yazma coremusic_auth üzerinden).
// ============================================================
"DROP TABLE IF EXISTS coremusic_api.api_keys",

];

return [
    'name'        => '2026-10_07_drop_coremusic_api_api_keys',
    'database'    => 'coremusic_api',
    'destructive' => true,
    'approval'    => 'REQUIRED — kullanıcı onayı ayrıca alınacak',
    'precheck'    => $precheck,
    'queries'     => $queries,
    'rollback'    => 'mysqldump yedek dosyasından CREATE TABLE + INSERT ile geri yüklenir',
];
