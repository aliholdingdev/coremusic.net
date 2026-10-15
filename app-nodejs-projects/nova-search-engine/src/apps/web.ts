/** Web UI — statik arayüz (public/index.html). Port: WEB_PORT (3110). API'ye fetch ile bağlanır. */
import { createServer } from 'node:http';
import { readFileSync, existsSync } from 'node:fs';
import { join } from 'node:path';
import { ROOT, config } from '../config.ts';

const PUBLIC_DIR = join(ROOT, 'public');

const MIME: Record<string, string> = {
  '.html': 'text/html; charset=utf-8',
  '.css': 'text/css; charset=utf-8',
  '.js': 'text/javascript; charset=utf-8',
  '.svg': 'image/svg+xml',
  '.png': 'image/png',
  '.ico': 'image/x-icon',
};

export function startWeb(): Promise<void> {
  const server = createServer((req, res) => {
    const path = (req.url ?? '/').split('?')[0];
    const rel = path === '/' ? 'index.html' : path.replace(/^\/+/, '');
    const file = join(PUBLIC_DIR, rel);
    // dizin dışına çıkışı engelle (path traversal)
    if (!file.startsWith(PUBLIC_DIR) || !existsSync(file)) {
      res.writeHead(404, { 'Content-Type': 'text/plain; charset=utf-8' });
      res.end('404 — bulunamadı');
      return;
    }
    try {
      const body = readFileSync(file);
      const ext = file.slice(file.lastIndexOf('.'));
      res.writeHead(200, {
        'Content-Type': MIME[ext] ?? 'application/octet-stream',
        'Cache-Control': 'no-cache',
      });
      res.end(body);
    } catch {
      res.writeHead(500, { 'Content-Type': 'text/plain; charset=utf-8' });
      res.end('500 — okunamadı');
    }
  });
  return new Promise((resolve, reject) => {
    server.once('error', reject); // EADDRINUSE vb. → serve yakalar ve dostça basar
    server.listen(config.webPort, config.api.host, () => {
      console.log(`NovaSearch Web: http://${config.api.host}:${config.webPort}/`);
      resolve();
    });
  });
}
