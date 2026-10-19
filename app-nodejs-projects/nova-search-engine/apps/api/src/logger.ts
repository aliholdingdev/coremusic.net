// ===========================================
// Terminal Log — okunur, renkli, zaman damgalı
// (JSON log yerine "terminal logu" görünümü)
// ===========================================

const C = {
  reset: '\x1b[0m', dim: '\x1b[2m', green: '\x1b[32m', yellow: '\x1b[33m',
  red: '\x1b[31m', cyan: '\x1b[36m', magenta: '\x1b[35m',
};

function ts(): string {
  return new Date().toTimeString().slice(0, 8);
}

export function logLine(msg: string, color: keyof typeof C = 'dim'): void {
  console.log(`${C.dim}[${ts()}]${C.reset} ${C[color]}${msg}${C.reset}`);
}

/** HTTP istek satırı: [20:16:03] GET /api/search → 200 · 120ms */
export function requestLog(method: string, url: string, status: number, ms: number): void {
  const short = url.split('?')[0] ?? url;
  const statusColor = status >= 500 ? 'red' : status >= 400 ? 'yellow' : 'green';
  console.log(
    `${C.dim}[${ts()}]${C.reset} ${C.cyan}${method}${C.reset} ${short} → ` +
    `${C[statusColor]}${status}${C.reset} ${C.dim}· ${Math.round(ms)}ms${C.reset}`,
  );
}

/** Açılış afişi. */
export function banner(lines: string[]): void {
  console.log(`${C.magenta}┌──────────────────────────────────────────────┐${C.reset}`);
  for (const l of lines) {
    console.log(`${C.magenta}│${C.reset} ${l.padEnd(44)} ${C.magenta}│${C.reset}`);
  }
  console.log(`${C.magenta}└──────────────────────────────────────────────┘${C.reset}`);
}
