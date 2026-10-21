<?php

declare(strict_types=1);

/**
 * CoreMusic — Schema Security Auditor
 *
 * SQL sema dosyasini statik analiz ederek PII/sifreleme, audit tablosu ve
 * isimlendirme (SQLi yuzeyi) kontrollerini yapar. PDO kullanmaz; saf metin analizi.
 *
 * Kullanim:
 *   php security-audit.php <schema.sql>
 *   php security-audit.php --help
 *
 * Cikis kodu: 0 = gecti, 1 = ihlal/blok bulundu.
 */

namespace CoreMusic\Database;

final class SecurityAudit
{
    private string $sql;

    /** Hassas kolon anahtar kelimeleri (dusuk seviyeli tarama). */
    private const SENSITIVE = ['ssn', 'password', 'secret', 'token', 'api_key', 'credit_card', 'card_number', 'national_id', 'ip_address'];

    /** Audit tablosu beklenen kritik tablolar. */
    private const CRITICAL = ['users', 'user', 'payments', 'payment', 'orders', 'order'];

    public function __construct(private readonly string $schemaPath)
    {
        if (!is_file($schemaPath)) {
            throw new \InvalidArgumentException("Schema dosyasi bulunamadi: {$schemaPath}");
        }
        $raw = (string) file_get_contents($schemaPath);
        $raw = preg_replace('/\/\*.*?\*\//s', '', $raw) ?? $raw;
        $this->sql = $raw;
    }

    public function run(): bool
    {
        echo "============================================================\n";
        echo " Schema Security Auditor (statik analiz)\n";
        echo " OWASP temelli sema denetimi\n";
        echo "============================================================\n\n";

        $passed = true;
        $passed = $this->checkSensitiveColumns() && $passed;
        $passed = $this->checkAuditTables() && $passed;
        $passed = $this->checkNaming() && $passed;

        if (!$passed) {
            echo "\n[BLOCK] Guvenlik denetimi basarisiz. Bu semayi DEPLOY etmeyin.\n";
            return false;
        }
        echo "\n[PASS] Guvenlik kontrolleri (PII/Audit/Naming) gecti.\n";
        return true;
    }

    /** Hassas kolon taramasi: sifreleme/uygun tip izi. */
    private function checkSensitiveColumns(): bool
    {
        $passed = true;
        $lines = explode("\n", $this->sql);
        foreach ($lines as $i => $line) {
            $lower = strtolower($line);
            foreach (self::SENSITIVE as $kw) {
                if (preg_match('/\b' . preg_quote($kw, '/') . '\b/', $lower)) {
                    // Sifre/hash/encrypt izi ya da genis blob tipi olmali
                    $looksSafe = str_contains($lower, 'hash')
                        || str_contains($lower, 'encrypt')
                        || str_contains($lower, 'varbinary')
                        || str_contains($lower, 'blob')
                        || str_contains($lower, 'text');
                    if (!$looksSafe && str_contains($lower, 'varchar(255)')) {
                        // Parola icin hash onerilir; bayrakla
                        echo "  [PII-UYARI] Satir " . ($i + 1) . ": '{$kw}' muhtemelen hash/sifreleme gerektirir (Argon2id/AEAD) — 'VARCHAR(255)' tek basina yetersiz.\n";
                    }
                }
            }
        }
        return $passed;
    }

    /** Kritik tablo icin karsilikli _audit tablosu bulunmali. */
    private function checkAuditTables(): bool
    {
        $passed = true;
        preg_match_all('/CREATE\s+TABLE\s+(?:IF\s+NOT\s+EXISTS\s+)?`?([a-zA-Z0-9_]+)`?\s*\(/is', $this->sql, $m);
        $tables = array_map(static fn ($t) => strtolower($t), $m[1]);

        foreach ($tables as $t) {
            if (in_array($t, self::CRITICAL, true)) {
                $audit = $t . '_audit';
                if (!in_array($audit, $tables, true)) {
                    echo "  [AUDIT-IHLALI] Kritik tablo '{$t}' icin '{$audit}' tablosu yok (degisiklik denetimi izlenemez).\n";
                    $passed = false;
                }
            }
        }
        return $passed;
    }

    /** Isimlendirme: snake_case disi tablo/kolon adlari dinamik sorgu yuzeyini genisletir. */
    private function checkNaming(): bool
    {
        $passed = true;
        preg_match_all('/CREATE\s+TABLE\s+(?:IF\s+NOT\s+EXISTS\s+)?`?([a-zA-Z0-9_\-]+)`?\s*\(/is', $this->sql, $m);
        foreach ($m[1] as $t) {
            if (!preg_match('/^[a-z0-9_]+$/', $t)) {
                echo "  [NAMING-IHLALI] Tablo adi '{$t}' snake_case disi (dinamik sorgu riski).\n";
                $passed = false;
            }
        }
        return $passed;
    }
}

// ---- CLI ----
if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "Bu betik yalnizca CLI ile calisir.\n");
    exit(1);
}

$argv = $_SERVER['argv'] ?? [];
if (in_array('--help', $argv, true) || in_array('-h', $argv, true) || count($argv) < 2) {
    echo "Kullanim: php security-audit.php <schema.sql>\n";
    echo "Amaç: PII/sifreleme, audit tablosu ve isimlendirme denetimi yapar.\n";
    exit(count($argv) < 2 && !in_array('--help', $argv, true) && !in_array('-h', $argv, true) ? 1 : 0);
}

try {
    $audit = new SecurityAudit($argv[1]);
    exit($audit->run() ? 0 : 1);
} catch (\Throwable $e) {
    fwrite(STDERR, "Hata: " . $e->getMessage() . "\n");
    exit(1);
}
