<?php

declare(strict_types=1);

/**
 * Dashboard — .adm-page--dashboard + .adm-panel__title "Dashboard".
 *
 * Görev D kararı: içerik YALNIZ veritabanından gelen GERÇEK veridir.
 * Sorgu başarısızsa (ör. DB kimlik bilgileri yapılandırılmamışsa —
 * DB_USER/DB_PASSWORD boş) sayfa "⚠️ VERIFICATION REQUIRED" boş-durumu
 * gösterir; uydurma sayım/sayı ÜRETİLMEZ (ZERO-HALLUCINATION).
 *
 * Sorgu: DatabaseManager (PDO prepare + bind; SELECT * YOK).
 * admin_users tablosu coremusic_auth DB'sinde yaşar; config'in gösterdiği
 * DB_NAME farklıysa bağlantı/sorgu hatası → aynı boş-durum dalı.
 */

$pageTitle = 'Dashboard';
$activeNav = 'admin-dashboard';

use CoreMusic\Admin\Platform\Database\DatabaseConfig;
use CoreMusic\Admin\Platform\Database\DatabaseManager;

/**
 * Dashboard için tek gerçek veri kaynağı: admin_users sayımı.
 *
 * @return array{ok:bool,count:int|null,message:string} geri dönüş; ok=false
 *         ise message sayfada gösterilir (sayım YOK, uydurma veri YOK).
 */
$loadAdminCount = static function (): array {
    if (DB_USER === '' || DB_PASSWORD === '') {
        return [
            'ok' => false,
            'count' => null,
            'message' => '⚠️ VERIFICATION REQUIRED — Dashboard verisi yalnız yapılandırılmış bir veritabanından okunabilir; veritabanı kimlik bilgileri tanımlı değil. Doğrulanmamış değer göstermek yerine bu sayfa boş bırakılmıştır.',
        ];
    }

    try {
        $manager = new DatabaseManager(new DatabaseConfig(
            DB_HOST,
            DB_ADMIN_NAME,
            DB_USER,
            DB_PASSWORD,
            DB_PORT,
            DB_CHARSET
        ));

        $rows = $manager->execute(
            'SELECT COUNT(*) AS admin_count FROM admin_users WHERE is_deleted = 0'
        );
        $count = isset($rows[0]['admin_count']) ? (int) $rows[0]['admin_count'] : 0;

        return ['ok' => true, 'count' => $count, 'message' => ''];
    } catch (Throwable $e) {
        error_log('[admin][dashboard] admin_users sayımı okunamadı: ' . $e->getMessage());

        return [
            'ok' => false,
            'count' => null,
            'message' => '⚠️ VERIFICATION REQUIRED — Dashboard veritabanı sorgusu şu anda tamamlanamadı; doğrulanmamış değer göstermek yerine bu sayfa boş bırakılmıştır.',
        ];
    }
};

$dashboard = $loadAdminCount();

ob_start();
?>
<section class="is-empty" aria-labelledby="dashboard-empty-title">
    <h2 id="dashboard-empty-title">Dashboard verisi</h2>
    <?php if ($dashboard['ok']): ?>
        <p class="adm-footer__meta">Kayıtlı yönetici sayısı: <?= $dashboard['count'] ?></p>
    <?php else: ?>
        <p><?= $dashboard['message'] ?></p>
    <?php endif; ?>
</section>
<?php
$content = ob_get_clean();

require __DIR__ . '/layout.php';
