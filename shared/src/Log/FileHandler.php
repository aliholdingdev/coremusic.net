<?php declare(strict_types=1);

namespace CoreMusic\Log;

/**
 * File Handler — coremusic_php_errors.log formatında dosya tabanlı logger.
 *
 * Debug mode: coremusic_php_errors.log + coremusic_php_warnings.log + coremusic_php_info.log
 * Production: sadece coremusic_php_errors.log
 */
final class FileHandler
{
    private string $logDir;
    private string $minLevel;
    private bool $debugMode;

    private const LEVELS = [
        'debug'   => 0,
        'info'    => 1,
        'warning' => 2,
        'error'   => 3,
    ];

    public function __construct(string $logDir, string $minLevel = 'info')
    {
        $this->logDir = rtrim($logDir, '/\\');
        $this->minLevel = $minLevel;
        $this->debugMode = defined('DEBUG_MODE') && DEBUG_MODE === true;
    }

    public function debug(string $message, array $context = []): void
    {
        if (!$this->debugMode || $this->shouldLog('debug')) {
            return;
        }
        $this->write('info', $message, $context, 'DEBUG');
    }

    public function info(string $message, array $context = []): void
    {
        if (!$this->shouldLog('info')) {
            return;
        }
        $this->write('info', $message, $context, 'INFO');
    }

    public function warning(string $message, array $context = []): void
    {
        if (!$this->shouldLog('warning')) {
            return;
        }
        $this->write('warning', $message, $context, 'WARNING');
    }

    public function error(string $message, array $context = []): void
    {
        if (!$this->shouldLog('error')) {
            return;
        }
        $this->write('error', $message, $context, 'ERROR');
    }

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

        $this->$level($message, $context);
    }

    private function shouldLog(string $level): bool
    {
        return (self::LEVELS[$level] ?? 0) >= (self::LEVELS[$this->minLevel] ?? 0);
    }

    private function write(string $fileLevel, string $message, array $context, string $logLevel): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $uri = $_SERVER['REQUEST_URI'] ?? '-';
        $method = $_SERVER['REQUEST_METHOD'] ?? '-';
        $ip = $_SERVER['REMOTE_ADDR'] ?? '-';

        $contextStr = '';
        if (!empty($context)) {
            $contextStr = ' ' . json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        $line = "[{$timestamp}] [{$logLevel}] [{$method} {$uri}] [{$ip}] {$message}{$contextStr}" . PHP_EOL;

        // Error her zaman coremusic_php_errors.log'a yazılır
        $this->appendToFile('coremusic_php_errors.log', $line);

        // Debug mode'da uyarı ve info da ayrı dosyalara
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
