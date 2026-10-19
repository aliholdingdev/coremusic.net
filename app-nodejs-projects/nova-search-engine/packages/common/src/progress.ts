// ===========================================
// Progress Bar — Unicode ilerleme göstergesi
// stderr'e yazar; --json çıktısını bozmaz.
// ===========================================

import type { SearchProgressEvent } from '@novasearch/common';

const ESC = '\x1b[2K'; // satırı temizle

export class ProgressBar {
  private lastLine = '';

  constructor(private width = 28) {}

  /** Motor olayını ekrana çevir. */
  handle(event: SearchProgressEvent): void {
    if (event.current !== undefined && event.total !== undefined) {
      this.draw(event.current / event.total, `${event.step}: ${event.note ?? ''}`);
    } else {
      this.draw(-1, `${event.step}: ${event.note ?? ''}`);
    }
    if (event.step === 'bitti') this.finish(event.note ?? 'tamam');
  }

  /** Belirli yüzdelerle çubuk çizer (determinate). */
  draw(ratio: number, label: string): void {
    const bar =
      ratio < 0
        ? '⣾⣽⣻⢿⡿⣟⣯⣷'[Math.floor(Date.now() / 120) % 8] ?? '…'
        : (() => {
            const filled = Math.round(ratio * this.width);
            return '█'.repeat(filled) + '░'.repeat(this.width - filled);
          })();
    const pct = ratio < 0 ? '   …' : String(Math.round(ratio * 100)).padStart(3, ' ') + '%';
    const line = `  [${bar}] ${pct}  ${label}`;
    this.lastLine = line;
    process.stderr.write(`${ESC}\r${line}`);
  }

  finish(note: string): void {
    const line = `  [${'█'.repeat(this.width)}] 100%  ${note}`;
    process.stderr.write(`${ESC}\r${line}\n`);
    this.lastLine = '';
  }

  clear(): void {
    if (this.lastLine) process.stderr.write(`${ESC}\r`);
    this.lastLine = '';
  }
}
