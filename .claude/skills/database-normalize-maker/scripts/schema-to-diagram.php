<?php

declare(strict_types=1);

/**
 * CoreMusic — Schema to Mermaid ER Diagram
 *
 * SQL sema dosyasindaki CREATE TABLE bloklarini parse edip Mermaid.js ER
 * diyagrami uretir. PDO kullanmaz; saf metin analizi.
 *
 * Kullanim:
 *   php schema-to-diagram.php <schema.sql>
 *   php schema-to-diagram.php --help
 *
 * Cikis kodu: 0 = uretim basarili, 1 = hata.
 */

namespace CoreMusic\Database;

final class SchemaToDiagram
{
    private string $sql;

    public function __construct(private readonly string $schemaPath)
    {
        if (!is_file($schemaPath)) {
            throw new \InvalidArgumentException("Schema dosyasi bulunamadi: {$schemaPath}");
        }
        $raw = (string) file_get_contents($schemaPath);
        $raw = preg_replace('/\/\*.*?\*\//s', '', $raw) ?? $raw;
        $raw = preg_replace('/--[^\n]*/', '', $raw) ?? $raw;
        $this->sql = $raw;
    }

    public function render(): string
    {
        $out = "erDiagram\n\n";

        preg_match_all(
            '/CREATE\s+TABLE\s+(?:IF\s+NOT\s+EXISTS\s+)?`?([a-zA-Z0-9_]+)`?\s*\((.*?)\)\s*(ENGINE|;)/is',
            $this->sql,
            $matches,
            PREG_SET_ORDER
        );

        $relations = [];

        foreach ($matches as $m) {
            $table = $m[1];
            $body  = $m[2];
            $out .= "    {$table} {\n";

            // Parantez disindaki virgullere gore bol (DECIMAL(10,2) gibi
            // icerik virgulleri satir bolmez — bilinen hata kalibi)
            foreach ($this->splitTopLevel($body) as $line) {
                $line = trim($line);
                if ($line === '') {
                    continue;
                }
                $upper = strtoupper($line);

                // Foreign key iliskisini yakala
                if (preg_match(
                    '/FOREIGN\s+KEY\s*\(`?([a-zA-Z0-9_]+)`?\)\s*REFERENCES\s+`?([a-zA-Z0-9_]+)`?\s*\(`?([a-zA-Z0-9_]+)`?\)/i',
                    $line,
                    $fk
                )) {
                    $relations[] = [
                        'child' => $table,
                        'parent' => $fk[2],
                        'fk' => $fk[1],
                    ];
                    continue;
                }

                // Kisit/anahtar satirlarini atla
                if (
                    str_starts_with($upper, 'CONSTRAINT')
                    || str_starts_with($upper, 'PRIMARY KEY')
                    || str_starts_with($upper, 'FOREIGN KEY')
                    || str_starts_with($upper, 'UNIQUE')
                    || str_starts_with($upper, 'KEY ')
                    || str_starts_with($upper, 'INDEX ')
                    || str_starts_with($upper, 'CHECK')
                ) {
                    // PRIMARY KEY'i isaretle
                    if (str_starts_with($upper, 'PRIMARY KEY')) {
                        if (preg_match('/PRIMARY\s+KEY\s*\(`?([a-zA-Z0-9_]+)`?\)/i', $line, $pk)) {
                            $out .= "        {$pk[1]} PK\n";
                        }
                    }
                    continue;
                }

                // Kolon: "ad TIP ..."
                $parts = preg_split('/\s+/', $line, 3);
                if (count($parts) >= 2) {
                    $col = trim($parts[0], '`');
                    $type = $this->mermaidType($parts[1]);
                    $suffix = '';
                    if ($col === 'id') {
                        $suffix = ' PK';
                    } elseif (str_ends_with($col, '_id') && strcasecmp($col, 'id') !== 0) {
                        $suffix = ' FK';
                    }
                    $out .= "        {$type} {$col}{$suffix}\n";
                }
            }
            $out .= "    }\n\n";
        }

        foreach ($relations as $r) {
            $out .= "    {$r['parent']} ||--o{ {$r['child']} : \"{$r['fk']}\"\n";
        }

        return $out;
    }

    /**
     * Govdeyi parantez derinligini takip ederek virgule gore boler.
     * DECIMAL(10,2) / NUMERIC(8, 3) gibi icerik virgulleri bolme yapmaz.
     *
     * @return list<string>
     */
    private function splitTopLevel(string $body): array
    {
        $parts = [];
        $current = '';
        $depth = 0;
        $len = strlen($body);

        for ($i = 0; $i < $len; $i++) {
            $ch = $body[$i];
            if ($ch === '(') {
                $depth++;
            } elseif ($ch === ')') {
                if ($depth > 0) {
                    $depth--;
                }
            }

            if ($ch === ',' && $depth === 0) {
                $parts[] = $current;
                $current = '';
                continue;
            }
            $current .= $ch;
        }
        if (trim($current) !== '') {
            $parts[] = $current;
        }

        return $parts;
    }

    /** Mermaid tip adlarini temizler (bosluk/parantez yok). */
    private function mermaidType(string $type): string
    {
        $t = strtoupper(trim($type));
        if (str_starts_with($t, 'BIGINT')) {
            return 'bigint';
        }
        if (str_starts_with($t, 'INT')) {
            return 'int';
        }
        if (str_starts_with($t, 'SMALLINT')) {
            return 'smallint';
        }
        if (str_starts_with($t, 'TINYINT')) {
            return 'tinyint';
        }
        if (str_starts_with($t, 'DECIMAL') || str_starts_with($t, 'NUMERIC')) {
            return 'decimal';
        }
        if (str_starts_with($t, 'VARCHAR') || str_starts_with($t, 'CHAR')) {
            return 'varchar';
        }
        if (str_starts_with($t, 'TEXT') || str_starts_with($t, 'MEDIUMTEXT') || str_starts_with($t, 'LONGTEXT')) {
            return 'text';
        }
        if (str_starts_with($t, 'TIMESTAMP')) {
            return 'timestamp';
        }
        if (str_starts_with($t, 'DATETIME')) {
            return 'datetime';
        }
        if (str_starts_with($t, 'DATE')) {
            return 'date';
        }
        if (str_starts_with($t, 'JSON')) {
            return 'json';
        }
        return strtolower(preg_replace('/[^A-Z0-9_]/', '', $t) ?? $t);
    }
}

// ---- CLI ----
if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "Bu betik yalnizca CLI ile calisir.\n");
    exit(1);
}

$argv = $_SERVER['argv'] ?? [];
if (in_array('--help', $argv, true) || in_array('-h', $argv, true) || count($argv) < 2) {
    echo "Kullanim: php schema-to-diagram.php <schema.sql>\n";
    echo "Amaç: CREATE TABLE bloklarindan Mermaid.js ER diyagrami uretir.\n";
    exit(count($argv) < 2 && !in_array('--help', $argv, true) && !in_array('-h', $argv, true) ? 1 : 0);
}

try {
    $gen = new SchemaToDiagram($argv[1]);
    echo $gen->render();
    exit(0);
} catch (\Throwable $e) {
    fwrite(STDERR, "Hata: " . $e->getMessage() . "\n");
    exit(1);
}
