<?php declare(strict_types=1);

namespace CoreMusic\PageRouter;

final class StructuredLogger
{
    private const LEVELS = ['debug' => 0, 'info' => 1, 'warn' => 2, 'error' => 3];

    private string $traceId = '';

    public function __construct(
        private readonly string $minLevel = 'info'
    ) {}

    public function setTraceId(string $traceId): void
    {
        $this->traceId = $traceId;
    }

    public function getTraceId(): string
    {
        return $this->traceId;
    }

    public function debug(string $module, string $event, array $extra = []): void
    {
        $this->log('debug', $module, $event, $extra);
    }

    public function info(string $module, string $event, array $extra = []): void
    {
        $this->log('info', $module, $event, $extra);
    }

    public function warn(string $module, string $event, array $extra = []): void
    {
        $this->log('warn', $module, $event, $extra);
    }

    public function error(string $module, string $event, array $extra = []): void
    {
        $this->log('error', $module, $event, $extra);
    }

    public function timing(string $module, string $middleware, float $durationMs): void
    {
        $this->log('debug', $module, 'middleware_timing', [
            'middleware'  => $middleware,
            'durationMs' => round($durationMs, 3),
        ]);
    }

    public function log(string $level, string $module, string $event, array $extra = []): void
    {
        if (!$this->shouldLog($level)) {
            return;
        }

        $record = array_merge([
            'timestamp' => (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM),
            'level'     => $level,
            'module'    => $module,
            'event'     => $event,
            'traceId'   => $this->traceId,
        ], $extra);

        error_log('[CoreMusic] ' . json_encode($record, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    private function shouldLog(string $level): bool
    {
        $minVal   = self::LEVELS[$this->minLevel] ?? 1;
        $levelVal = self::LEVELS[$level] ?? 1;
        return $levelVal >= $minVal;
    }
}
