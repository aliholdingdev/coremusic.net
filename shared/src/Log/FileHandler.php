<?php declare(strict_types=1);

namespace CoreMusic\Log;

use Psr\Log\LoggerInterface;
use Stringable;

/**
 * File Handler — coremusic_php_errors.log formatında dosya tabanlı logger.
 *
 * P5 (Enterprise Logger):
 *  - PSR-3 `LoggerInterface` UYGULAR (psr/log ^3.0): emergency/alert/critical/
 *    notice + log() dispatcher; seviye adları kanonik.
 *  - Request/Correlation: `setTraceId()` — satırlara `[trace=...]` eklenir
 *    (StructuredLogger traceId'i ile AYNI değer → istek stitching).
 *  - B-F-14: debug() guard'ı düzeltilmiş (önce tersine idi → debug modda hiç
 *    yazmıyordu).
 *
 * Log dosyaları (repo kökü — kullanici onayli kanonik hedef, 2026-10-07):
 *   - coremusic_php_errors.log     → TÜM seviyeler (her zaman)
 *   - coremusic_php_warnings.log   → warning-ailesi (yalnız debug mode)
 *   - coremusic_php_info.log       → info/debug (yalnız debug mode)
 *   - coremusic_php_kernel_debug.log → PageRouterKernel (ayrı yazıcı, bu sınıf değil)
 *   - coremusic_php_security.log   → [AUTH]/[SECURITY] olayları (YENİ stream,
 *     authEvent/securityEvent, prod dâhil her zaman — audit tail'i)
 *
 * Hassas veri: tüm satırlar Redactor'dan geçer (password/token/secret/… +
 * hash'ler + e-posta maskesi).
 */
final class FileHandler implements LoggerInterface
{
    private string $logDir;
    private string $minLevel;
    private bool $debugMode;

    /** Request correlation — PageRouterKernel her istekte setTraceId() çağırır. */
    private static string $traceId = '';

    /** PSR-3 kanonik seviyeler (artan önem). Mevcut adlar korunur. */
    private const LEVELS = [
        'debug'     => 0,
        'info'      => 1,
        'notice'    => 2,
        'warning'   => 3,
        'error'     => 4,
        'critical'  => 5,
        'alert'     => 6,
        'emergency' => 7,
    ];

    /** Uyarı-ailesi → warnings.log (debug modda ek split). */
    private const WARNING_FAMILY = ['warning', 'error', 'critical', 'alert', 'emergency'];

    public function __construct(string $logDir, string $minLevel = 'info', ?bool $debugMode = null)
    {
        $this->logDir = rtrim($logDir, '/\\');
        $this->minLevel = $minLevel;
        // $debugMode override test edilebilirlik içindir (B-F-14 regresyon testi);
        // üretimde null kalır → DEBUG_MODE sabitinden okunur.
        $this->debugMode = $debugMode ?? (defined('DEBUG_MODE') && DEBUG_MODE === true);
    }

    // ─── Trace / correlation ────────────────────────────────────────────

    public static function setTraceId(string $traceId): void
    {
        self::$traceId = $traceId;
    }

    public static function clearTraceId(): void
    {
        self::$traceId = '';
    }

    // ─── PSR-3 ─────────────────────────────────────────────────────────

    public function emergency(string|Stringable $message, array $context = []): void
    {
        $this->logAt('emergency', (string) $message, $context);
    }

    public function alert(string|Stringable $message, array $context = []): void
    {
        $this->logAt('alert', (string) $message, $context);
    }

    public function critical(string|Stringable $message, array $context = []): void
    {
        $this->logAt('critical', (string) $message, $context);
    }

    public function error(string|Stringable $message, array $context = []): void
    {
        $this->logAt('error', (string) $message, $context);
    }

    public function warning(string|Stringable $message, array $context = []): void
    {
        $this->logAt('warning', (string) $message, $context);
    }

    public function notice(string|Stringable $message, array $context = []): void
    {
        $this->logAt('notice', (string) $message, $context);
    }

    public function info(string|Stringable $message, array $context = []): void
    {
        $this->logAt('info', (string) $message, $context);
    }

    public function debug(string|Stringable $message, array $context = []): void
    {
        // B-F-14 düzeltmesi: debug YALNIZCA debugMode AÇIK ve seviye izinliyken
        // yazılır (önceki guard tersine idi → debug modda hiç yazmıyordu).
        $this->logAt('debug', (string) $message, $context);
    }

    /**
     * PSR-3 dispatcher. Bilinmeyen seviye 'error'a düşer (fail-safe: log
     * kaybedilmez); seviye tipi interface gereği untyped gelir.
     *
     * @param mixed $level
     */
    public function log($level, string|Stringable $message, array $context = []): void
    {
        $normalized = strtolower((string) $level);
        if (!isset(self::LEVELS[$normalized])) {
            $normalized = 'error';
        }
        $this->logAt($normalized, (string) $message, $context);
    }

    // ─── Olay kayıtları ────────────────────────────────────────────────

    /**
     * Auth event log — login attempt, success, failure.
     */
    public function authEvent(string $event, array $context = []): void
    {
        $message = "[AUTH] {$event}";
        if (!empty($context['email'])) {
            $message .= " email={$context['email']}";
        }
        if (!empty($context['ip'])) {
            $message .= " ip={$context['ip']}";
        }
        if (!empty($context['user_id'])) {
            $message .= " user_id={$context['user_id']}";
        }
        if (!empty($context['reason'])) {
            $message .= " reason={$context['reason']}";
        }

        $level = match ($event) {
            'login_success', 'register_success', 'logout' => 'info',
            'login_failed', 'register_failed', 'rate_limited' => 'warning',
            'login_error', 'register_error' => 'error',
            default => 'info',
        };

        $this->logAt($level, $message, $context, securityStream: true);
    }

    /**
     * Security / authz event — origin_blocked, csrf_invalid, authz_denied,
     * rate_limited, bypass_activated vb. (P5-32: 403/429 gövdesi artık gözlemlenir)
     */
    public function securityEvent(string $event, array $context = []): void
    {
        $message = "[SECURITY] {$event}";
        foreach (['ip', 'uri', 'origin', 'role', 'code', 'reason', 'path'] as $key) {
            if (!empty($context[$key])) {
                $message .= " {$key}={$context[$key]}";
            }
        }

        $level = match ($event) {
            'origin_blocked', 'csrf_invalid', 'authz_denied', 'rate_limited', 'bypass_activated' => 'warning',
            default => 'warning',
        };

        $this->logAt($level, $message, $context, securityStream: true);
    }

    // ─── Çekirdek ──────────────────────────────────────────────────────

    private function logAt(string $level, string $message, array $context, bool $securityStream = false): void
    {
        if ($level === 'debug') {
            if (!$this->debugMode || !$this->shouldLog('debug')) {
                return;
            }
        } elseif (!$this->shouldLog($level)) {
            return;
        }

        $fileLevel = in_array($level, self::WARNING_FAMILY, true) ? 'error' : 'info';
        $this->write($fileLevel, $message, $context, strtoupper($level), $securityStream);
    }

    private function shouldLog(string $level): bool
    {
        return (self::LEVELS[$level] ?? 0) >= (self::LEVELS[$this->minLevel] ?? 0);
    }

    private function write(string $fileLevel, string $message, array $context, string $logLevel, bool $securityStream = false): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $uri = $_SERVER['REQUEST_URI'] ?? '-';
        $method = $_SERVER['REQUEST_METHOD'] ?? '-';
        $ip = $_SERVER['REMOTE_ADDR'] ?? '-';
        $trace = self::$traceId !== '' ? ' [trace=' . self::$traceId . ']' : '';

        $contextStr = '';
        if (!empty($context)) {
            $contextStr = ' ' . json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        $line = "[{$timestamp}] [{$logLevel}] [{$method} {$uri}] [{$ip}]{$trace} {$message}{$contextStr}" . PHP_EOL;

        // Merkezi redaction (§17 Edge Case 3): hassas alanlar [REDACTED] olur;
        // e-postalar maskelenir (B-F-17: PII logda düz metin durmasın).
        $line = Redactor::redact($line);

        // Tüm seviyeler errors.log'a yazılır (mevcut davranış korunur —
        // kullanici onayli kanonik 4 dosya: errors/warnings/info/kernel_debug).
        $this->appendToFile('coremusic_php_errors.log', $line);

        // GÜVENLİK AKIŞI (yeni dosya — kullanıcı talebi 2026-10-07): auth + security
        // olayları AYRİ stream'e de yazılır; prod'da dâhil her zaman aktif
        // (audit/SIEM tail'i errors.log kalabalığından ayrışır).
        if ($securityStream) {
            $this->appendToFile('coremusic_php_security.log', $line);
        }

        // Debug modda uyarı ve info da ayrı dosyalara
        if ($this->debugMode) {
            if ($fileLevel === 'warning' || $fileLevel === 'error') {
                $this->appendToFile('coremusic_php_warnings.log', $line);
            }
            if ($fileLevel === 'info' || $fileLevel === 'debug') {
                $this->appendToFile('coremusic_php_info.log', $line);
            }
        }
    }

    private function appendToFile(string $filename, string $content): void
    {
        $filepath = $this->logDir . '/' . $filename;
        @file_put_contents($filepath, $content, FILE_APPEND | LOCK_EX);
    }
}
