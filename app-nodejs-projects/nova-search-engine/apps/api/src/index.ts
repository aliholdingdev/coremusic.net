// ===========================================
// API giriş noktası
// ===========================================

import { start } from './server.js';
import { loadEnvFile } from '@novasearch/common';

loadEnvFile();

start().catch((err: unknown) => {
  const msg = err instanceof Error ? err.message : String(err);
  if (msg.includes('EADDRINUSE') || msg.includes('address already in use') || msg.includes('Failed to start server')) {
    const port = process.env['PORT'] ?? '3001';
    console.error(`Port ${port} zaten kullanımda. Başka bir NovaSearch API çalışıyor olabilir.`);
    console.error(`Çalışan süreci durdurup tekrar deneyin veya farklı port: PORT=3002 pnpm api`);
  } else {
    console.error(`API başlatılamadı: ${msg}`);
  }
  process.exit(1);
});
