<?php

declare(strict_types=1);

/**
 * CoreMusic — Normalization & BCNF Checker
 *
 * Bir SQL şema dosyasini (CREATE TABLE bloklari) statik analiz ederek
 * 1NF ve BCNF (proxy) ihlallerini tespit eder. PDO kullanmaz; saf metin analizi.
 *
 * Kullanim:
 *   php normalize-checker.php <schema.sql>
 *   php normalize-checker.php --help
 *
 * Cikis kodu: 0 = tum kontroller gecti, 1 = ihlal bulundu.
 */

namespace CoreMusic\Database;

final class NormalizeChecker
{
    /** @var array<string, array<string, string>> tablo => (kolon => tip) */
    private array $tables = [];

    public function __construct(private readonly string $schemaPath)
    {
        if (!is_file($schemaPath)) {
            throw new \InvalidArgumentException("Schema dosyasi bulunamadi: {$schemaPath}");
        }
    }

    private function load(): void
    {
        $sql = (string) file_get_contents($this->schemaPath);
        // Yorumlari temizle (satir ici -- ve satir ici/**/ )
        $sql = preg_replace('/\/\*.*?\*\//s', '', $sql) ?? $sql;
        $sql = preg_replace('/--[^\n]*/', '', $sql) ?? $sql;

        // CREATE TABLE bloklarini ayikla
        preg_match_all(
            '/CREATE\s+TABLE\s+(?:IF\s+NOT\s+EXISTS\s+)?`?([a-zA-Z0-9_]+)`?\s*\((.*?)\)\s*(ENGINE|;)/is',
            $sql,
            $matches,
            PREG_SET_ORDER
        );

        foreach ($matches as $m) {
            $this->tables[$m[1]] = $this->parseColumns($m[2]);
        }
    }

    /**
     * Kolon tanimlarini (kolon => tip) soyutlar; kisit/anahtar satirlarini atlar.
     *
     * @return array<string, string>
     */
    private function parseColumns(string $body): array
    {
        $columns = [];
        // Virgul ile parcala (ic ice parantez icindeki virgulleri gozardi icin basit yaklasim:
        // MySQL kolon tanimlarinda ic ice () genelde tip/DEFAULT ifadelerinde sinirli).
        foreach (explode(',', $body) as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }
            $upper = strtoupper($line);
            if (
                str_starts_with($upper, 'CONSTRAINT')
                || str_starts_with($upper, 'PRIMARY KEY')
                || str_starts_with($upper, 'FOREIGN KEY')
                || str_starts_with($upper, 'UNIQUE')
                || str_starts_with($upper, 'KEY ')
                || str_starts_with($upper, 'INDEX ')
                || str_starts_with($upper, 'CHECK')
                || str_starts_with($upper, 'FULLTEXT')
                || str_starts_with($upper, 'SPATIAL')
            ) {
                continue;
            }
            $parts = preg_split('/\s+/', $line, 3);
            if (count($parts) >= 2) {
                $name = trim($parts[0], '`');
                $type = strtoupper($parts[1]);
                $columns[$name] = $type;
            }
        }
        return $columns;
    }

    public function run(): bool
    {
        $this->load();

        echo "============================================================\n";
        echo " Normalization & BCNF Checker (statik analiz)\n";
        echo " Hedef: MySQL 8.0 / InnoDB / utf8mb4\n";
        echo "============================================================\n\n";

        if ($this->tables === []) {
            echo "[UYARI] CREATE TABLE bulunamadi. Dosyayi kontrol edin.\n";
            return false;
        }

        $passed = true;
        foreach ($this->tables as $table => $columns) {
            echo "Tablo: {$table}\n";
            if (!$this->check1nf($table, $columns)) {
                $passed = false;
            }
            if (!$this->checkKeyPresence($table, $columns)) {
                $passed = false;
            }
            if (!$this->checkBcnfProxy($table, $columns)) {
                $passed = false;
            }
            echo "\n";
        }

        if (!$passed) {
            echo "[BLOCK] Normalizasyon denetimi basarisiz. Yukaridaki ihlalleri duzeltin.\n";
            return false;
        }
        echo "[PASS] Tum tablolar proxy normalizasyon kontrollerinden gecti.\n";
        return true;
    }

    /** 1NF proxy: atomik olmayan (liste/array) kolon izi. */
    private function check1nf(string $table, array $columns): bool
    {
        $passed = true;
        $suspects = ['tags', 'categories', 'hobbies', 'keywords'];
        foreach ($columns as $name => $type) {
            if (str_contains($type, 'ARRAY')) {
                echo "  [1NF-IHLALI] '{$table}.{$name}' tipinde ARRAY — ayri tablo (pivot) olusturun.\n";
                $passed = false;
                continue;
            }
            if (in_array(strtolower($name), $suspects, true) && str_contains($type, 'TEXT')) {
                echo "  [1NF-UYARI] '{$table}.{$name}' muhtemelen virgullu liste — 1NF icin ayri tablo onerilir.\n";
            }
        }
        return $passed;
    }

    /** Her varlik tablosunda standart PK izi (id/uuid) bulunmali (uyari seviyesi). */
    private function checkKeyPresence(string $table, array $columns): bool
    {
        $hasKey = isset($columns['id']) || isset($columns['uuid']);
        $isAudit = str_ends_with($table, '_audit');
        $isJunction = substr_count($table, '_') >= 1 && !isset($columns['id']);

        if (!$hasKey && !$isAudit && !$isJunction) {
            echo "  [PK-UYARI] '{$table}' standart 'id' PK izi tasimiyor (surrogate PK onerilir).\n";
        }
        return true; // uyari; basarisiz sayilmaz
    }

    /** BCNF proxy: transitive bagimlilik izi (orn. dept_name => dept tablosuna ait). */
    private function checkBcnfProxy(string $table, array $columns): bool
    {
        $passed = true;
        $allowedName = ['name', 'first_name', 'last_name', 'title'];
        foreach (array_keys($columns) as $name) {
            if (str_ends_with($name, '_name') && !in_array($name, $allowedName, true)) {
                echo "  [BCNF-IHLALI] '{$table}.{$name}' transitive bagimlilik izi tasir; '{$name}' genelde ayri bir tabloya aittir (orn. department_name -> departments).\n";
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
    echo "Kullanim: php normalize-checker.php <schema.sql>\n";
    echo "Amaç: CREATE TABLE bloklarini analiz ederek 1NF/BCNF proxy ihlallerini bulur.\n";
    exit(count($argv) < 2 && !in_array('--help', $argv, true) && !in_array('-h', $argv, true) ? 1 : 0);
}

try {
    $checker = new NormalizeChecker($argv[1]);
    exit($checker->run() ? 0 : 1);
} catch (\Throwable $e) {
    fwrite(STDERR, "Hata: " . $e->getMessage() . "\n");
    exit(1);
}
