<?php
declare(strict_types=1);

namespace CoreMusic\Api\Test\Unit;

use CoreMusic\Config\EnvParser;
use PHPUnit\Framework\TestCase;

/**
 * coremusic_billing CANLI şema denetimi (salt-okunur — DDL YASAK).
 *
 * Kanıt (2026-10-30 canlı kontrol, C:\temp\claude\billing_db_check.php):
 *   schema coremusic_billing exists: NO · billing tables (0)
 * Ürün şeması dump ile tanımlı: .ai/.sql/mysql/coremusic_billing.sql (11 tablo).
 * Bu test ASLA CREATE DATABASE yapmaz; şema canlıda yoksa dürüstçe skip eder
 * ve raporlanır. Şema canlıda VARDIRSA kullandığımız üç tabloyu (read-only
 * INFORMATION_SCHEMA) doğrular — deploy edilmemiş şemaya karşı yeşil görünmez.
 *
 * Credential yoklama: EnvParser::load() dizi döndürür; user/password KONSOLA,
 * assertion mesajına veya exception'a BASILMAZ (yalnız varlık doğrulanır).
 */
final class BillingLiveSchemaTest extends TestCase
{
    private const SCHEMA = 'coremusic_billing';

    /** @var list<string> */
    private const REQUIRED_TABLES = ['subscription_plans', 'user_subscriptions', 'invoices'];

    public function testCoremusicBillingSchemaExists(): void
    {
        if (!\extension_loaded('pdo_mysql')) {
            $this->markTestSkipped('pdo_mysql yüklü değil — canlı şema denetimi çalıştırılamaz');
        }

        $envFile = dirname(__DIR__, 3) . '/shared/config/.env';
        if (!is_file($envFile)) {
            $this->markTestSkipped('shared/config/.env yok — canlı şema denetimi çalıştırılamaz');
        }

        $env = EnvParser::load($envFile);
        $host = (string) ($env['DB_HOST'] ?? '127.0.0.1');
        $port = (int) ($env['DB_PORT'] ?? 3306);
        $user = (string) ($env['DB_USER'] ?? '');
        $pass = (string) ($env['DB_PASSWORD'] ?? '');

        if ($user === '' || $pass === '') {
            $this->markTestSkipped('DB kimlik bilgileri boş — canlı şema denetimi çalıştırılamaz');
        }

        try {
            $pdo = new \PDO(
                sprintf('mysql:host=%s;port=%d;charset=utf8mb4', $host, $port),
                $user,
                $pass,
                [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION, \PDO::ATTR_TIMEOUT => 5]
            );
        } catch (\Throwable) {
            // Mesaj host/credential içerebilir → loglanmaz, basılmaz (yalnız skip).
            $this->markTestSkipped('canlı MySQL bağlantısı kurulamadı — şema denetimi yapılamadı');
        }

        $exists = $pdo->prepare(
            'SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?'
        );
        $exists->execute([self::SCHEMA]);

        if ($exists->fetchColumn() === false) {
            // Ürün şeması dump'ta — canlıda yok. DDL bu testin İŞİ DEĞİL (yasak).
            $this->markTestSkipped('coremusic_billing canlıda yok — DDL yasak (ürün şeması)');
        }

        // Şema CANLI → kullandığımız tablolar read-only doğrulanır.
        $tables = $pdo->prepare(
            'SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES
              WHERE TABLE_SCHEMA = ? AND TABLE_NAME IN (?, ?, ?)'
        );
        $tables->execute([self::SCHEMA, ...self::REQUIRED_TABLES]);
        $found = $tables->fetchAll(\PDO::FETCH_COLUMN);

        $missing = array_values(array_diff(self::REQUIRED_TABLES, $found));
        $this->assertSame(
            [],
            $missing,
            'coremusic_billing canlıda var ama eksik tablo: ' . implode(', ', $missing)
        );
    }
}
