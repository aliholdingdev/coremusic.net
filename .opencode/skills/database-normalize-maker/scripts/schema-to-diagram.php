<?php
declare(strict_types=1);

/**
 * AI Agentic Orchestrator - Visualizer Module
 * Schema to Mermaid.js ER Diagram Generator
 * 
 * SQL DDL scriptini alır ve ADR notlarını koruyarak standart 
 * bir Mermaid.js ER diagram dökümanına dönüştürür.
 */

namespace CoreMusic\AgenticOrchestrator\Database;

class SchemaToDiagram {
    private string $schemaContent;

    public function __construct(string $schemaPath) {
        if (!file_exists($schemaPath)) {
            throw new \InvalidArgumentException("Schema file not found: {$schemaPath}");
        }
        $this->schemaContent = file_get_contents($schemaPath);
    }

    public function generate(): void {
        echo "```mermaid\n";
        echo "erDiagram\n\n";

        preg_match_all('/CREATE TABLE\s+([a-zA-Z0-9_]+)\s*\((.*?)\)\s*;/is', $this->schemaContent, $matches);
        
        $foreignKeys = [];

        foreach ($matches[1] as $index => $tableName) {
            echo "    {$tableName} {\n";
            
            $columnsString = $matches[2][$index];
            $lines = explode(',', $columnsString);
            
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line) || str_starts_with($line, '--')) continue;
                
                // FK kontrolü
                if (preg_match('/FOREIGN KEY\s*\(([^\)]+)\)\s*REFERENCES\s*([a-zA-Z0-9_]+)\s*\(([^\)]+)\)/i', $line, $fkMatches)) {
                    $foreignKeys[] = [
                        'from' => $tableName,
                        'to' => $fkMatches[2],
                        'label' => trim($fkMatches[1])
                    ];
                    continue;
                }
                
                // Kolon tanımları
                if (!str_starts_with($line, 'CONSTRAINT') && !str_starts_with($line, 'PRIMARY KEY') && !str_starts_with($line, 'UNIQUE') && !str_starts_with($line, 'INDEX')) {
                    $parts = preg_split('/\s+/', $line, 3);
                    if (count($parts) >= 2) {
                        $colName = $parts[0];
                        $colType = $parts[1];
                        // Mermaid tipleri genelde boşluksuz yazılmalı
                        $colType = preg_replace('/[^a-zA-Z0-9_]/', '', $colType); 
                        echo "        {$colType} {$colName}\n";
                    }
                }
            }
            echo "    }\n\n";
        }

        // İlişkileri çiz
        foreach ($foreignKeys as $fk) {
            // Basit One-to-Many gösterimi (AI Orkestratörü kompleksliği burada ayarlar)
            echo "    {$fk['to']} ||--o{ {$fk['from']} : \"{$fk['label']}\"\n";
        }

        echo "```\n";
    }
}

if ($argc < 2) {
    die("Usage: php schema-to-diagram.php <path_to_schema.sql>\n");
}

try {
    $generator = new SchemaToDiagram($argv[1]);
    $generator->generate();
} catch (\Throwable $e) {
    die("Error: " . $e->getMessage() . "\n");
}
