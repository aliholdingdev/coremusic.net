<?php
declare(strict_types=1);

/**
 * AI Agentic Orchestrator - Security Engineer Module
 * OWASP Top 10 & CoreMusic Security Validator
 * 
 * Bu script, SQL dosyasını inceleyerek PII verilerinin (AES-256-GCM) 
 * şifrelenmesini, audit tablolarını ve SQLi açıklarına karşı standart
 * isimlendirme (snake_case) yapısını kontrol eder.
 */

namespace CoreMusic\AgenticOrchestrator\Database;

class SecurityAudit {
    private string $schemaContent;
    private array $sensitiveKeywords = ['ssn', 'credit_card', 'national_id', 'medical', 'token', 'secret'];

    public function __construct(string $schemaPath) {
        if (!file_exists($schemaPath)) {
            throw new \InvalidArgumentException("Schema file not found: {$schemaPath}");
        }
        $this->schemaContent = file_get_contents($schemaPath);
    }

    public function runAudit(): void {
        echo "========================================================\n";
        echo "[SECURITY ENGINEER AGENT]: OWASP & Data Security Audit Initiated...\n";
        echo "========================================================\n\n";

        $passed = true;

        if (!$this->checkEncryptionRequirements()) {
            $passed = false;
        }

        if (!$this->checkAuditTables()) {
            $passed = false;
        }
        
        if (!$this->checkSQLInjectionRisk()) {
            $passed = false;
        }

        if (!$passed) {
            echo "⚠️ [VERIFICATION REQUIRED]: Security Audit Failed. Do NOT deploy this schema.\n";
            exit(1);
        }

        echo "✅ [SUCCESS]: All security checks (Encryption, Audit, Naming) passed.\n";
    }

    private function checkEncryptionRequirements(): bool {
        $passed = true;
        $lines = explode("\n", $this->schemaContent);
        
        foreach ($lines as $lineNum => $line) {
            $lineLower = strtolower($line);
            foreach ($this->sensitiveKeywords as $keyword) {
                if (str_contains($lineLower, " {$keyword} ") || str_contains($lineLower, "{$keyword} ")) {
                    // PII kolonu var, text veya şifreleme ile ilgili ADR (yorum) var mı kontrol et.
                    if (!str_contains($lineLower, 'text') && !str_contains($lineLower, 'varchar(512)') && !str_contains($lineLower, 'encrypt')) {
                        echo "  [ERROR - PII ENCRYPTION]: Sensitive column '{$keyword}' found on line " . ($lineNum + 1) . " but does not seem to accommodate AES-256-GCM encryption (needs TEXT/VARCHAR(512) or ENCRYPT/ADR tag).\n";
                        $passed = false;
                    }
                }
            }
        }
        return $passed;
    }

    private function checkAuditTables(): bool {
        $passed = true;
        // Kritik tablolar (örneğin users, payments) için "_audit" tablosu aranır.
        if (preg_match('/CREATE TABLE\s+(users|payments|orders)\s*\(/is', $this->schemaContent, $matches)) {
            $criticalTable = $matches[1];
            if (!preg_match('/CREATE TABLE\s+' . $criticalTable . '_audit\s*\(/is', $this->schemaContent)) {
                echo "  [ERROR - AUDIT TRAIL]: Critical table '{$criticalTable}' does not have a corresponding '{$criticalTable}_audit' table for tracking changes.\n";
                $passed = false;
            }
        }
        return $passed;
    }

    private function checkSQLInjectionRisk(): bool {
        $passed = true;
        // Tablo ve kolon isimlerinde standart dışı karakter var mı (snake_case dışı).
        preg_match_all('/CREATE TABLE\s+([a-zA-Z0-9_\-]+)\s*/is', $this->schemaContent, $matches);
        foreach ($matches[1] as $tableName) {
            if (!preg_match('/^[a-z0-9_]+$/', $tableName)) {
                echo "  [ERROR - NAMING/SQLi]: Table name '{$tableName}' contains non-standard characters (must be lowercase snake_case). This poses risks for dynamic queries.\n";
                $passed = false;
            }
        }
        return $passed;
    }
}

if ($argc < 2) {
    die("Usage: php security-audit.php <path_to_schema.sql>\n");
}

try {
    $audit = new SecurityAudit($argv[1]);
    $audit->runAudit();
} catch (\Throwable $e) {
    die("Error: " . $e->getMessage() . "\n");
}
