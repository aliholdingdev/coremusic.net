// ===========================================
// Ortam dosyası yükleme — Node gömülü API
// (.env yalnız yerel makinede; git'e girmez)
// ===========================================

import { existsSync } from 'node:fs';
import { resolve } from 'node:path';

/** .env dosyasını yükle; yoksa sessizce devam et. */
export function loadEnvFile(path = '.env'): void {
  const full = resolve(process.cwd(), path);
  if (!existsSync(full)) return;
  try {
    process.loadEnvFile(full);
  } catch {
    // bozuk .env sessizce yoksayılır (çalışma engellenmez)
  }
}
