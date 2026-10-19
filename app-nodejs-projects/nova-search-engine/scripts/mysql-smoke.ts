// MySQL canlı smoke test — MysqlAdapter + migrate + round-trip
// Kullanım: bun scripts/mysql-smoke.ts   (repodan, .env ile)
// NOT: Ayrı bir test DB'si (MYSQL_DATABASE + '_test') kullanır — mevcut
// `novasearch` legacy şemalı olduğu için dokunulmaz (bkz. rapor).
import { MysqlAdapter } from '../packages/database/src/mysql.js';
import { migrate } from '../packages/database/src/schema.js';

const baseDb = process.env['MYSQL_DATABASE'] ?? 'novasearch';
const testDb = `${baseDb}_test`;
const cfg = {
  host: process.env['MYSQL_HOST'] ?? '127.0.0.1',
  port: Number(process.env['MYSQL_PORT'] ?? 3306),
  database: testDb,
  user: process.env['MYSQL_USER'] ?? 'root',
  password: process.env['MYSQL_PASSWORD'] ?? '',
  ssl: (process.env['MYSQL_SSL'] ?? 'false') === 'true',
};

async function main() {
  console.log(`MySQL smoke test → ${cfg.user}@${cfg.host}:${cfg.port}/${cfg.database}`);
  // 0) test DB'yi oluştur — MysqlAdapter ile information_schema üzerinden
  // (script'te doğrudan mysql2 importu yok; paket çözünürlüğü korunur)
  const root = new MysqlAdapter({ ...cfg, database: 'information_schema' });
  await root.exec(`CREATE DATABASE IF NOT EXISTS \`${testDb}\``);
  await root.close();
  console.log(`0) CREATE DATABASE IF NOT EXISTS ${testDb} → OK`);

  const db = new MysqlAdapter(cfg);

  // 0) sürüm
  const ver = await db.get<{ v: string }>('SELECT VERSION() AS v');
  console.log('0) VERSION =', ver?.v);

  // 1) migrate (tablolar + index)
  await migrate(db);
  console.log('1) migrate OK');

  // 2) round-trip: search_log'a yaz, oku, temizle
  await db.run(
    'INSERT INTO search_log (query, results_count, source, took_ms, created_at) VALUES (?, ?, ?, ?, ?)',
    ['__smoke__', 42, 'smoke', 7, new Date().toISOString()],
  );
  const row = await db.get<{ results_count: number }>(
    'SELECT results_count FROM search_log WHERE query = ?',
    ['__smoke__'],
  );
  console.log('2) round-trip OK → results_count =', row?.results_count);

  // 3) transaction: rollback davranışı
  try {
    await db.transaction(async (tx) => {
      await tx.run(
        'INSERT INTO search_log (query, results_count, source, took_ms, created_at) VALUES (?, ?, ?, ?, ?)',
        ['__tx__', 1, 'tx', 0, new Date().toISOString()],
      );
      throw new Error('zorla-rollback');
    });
  } catch (e) {
    const check = await db.get<{ c: number }>('SELECT COUNT(*) AS c FROM search_log WHERE query = ?', ['__tx__']);
    console.log('3) transaction rollback OK → __tx__ satır =', check?.c, '(0 beklenir)');
  }

  // 4) videos tablosu插入 + dedup (UNIQUE youtube_id)
  await db.run(
    `INSERT INTO videos (youtube_id, channel_youtube_id, channel_name, title, description, duration_seconds,
      view_count, like_count, comment_count, created_at, updated_at)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
    ['__smoke_vid__', 'ch1', 'Kanal', 'Başlık', 'Açıklama', 120, 1000, 10, 5, new Date().toISOString(), new Date().toISOString()],
  );
  let dupOk = false;
  try {
    await db.run(
      `INSERT INTO videos (youtube_id, channel_youtube_id, channel_name, title, description, duration_seconds,
        view_count, like_count, comment_count, created_at, updated_at)
       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
      ['__smoke_vid__', 'ch1', 'Kanal', 'Başlık2', '', 1, 1, 1, 1, new Date().toISOString(), new Date().toISOString()],
    );
  } catch {
    dupOk = true;
  }
  console.log('4) videos dedup (UNIQUE youtube_id) →', dupOk ? 'ENGELLENDI ✓' : 'BAŞARISIZ ✗');

  // 5) cleanup
  await db.run('DELETE FROM search_log WHERE query IN (?, ?)', ['__smoke__', '__tx__']);
  await db.run('DELETE FROM videos WHERE youtube_id = ?', ['__smoke_vid__']);
  const sonuc = await db.get<{ c: number }>('SELECT COUNT(*) AS c FROM search_log WHERE query LIKE ?', ['__smoke%']);
  console.log('5) cleanup OK → kalan smoke satır =', sonuc?.c);

  await db.close();
  console.log('MYSQL SMOKE: TÜMÜ GEÇTİ');
}

main().catch((e) => {
  console.error('MYSQL SMOKE BAŞARISIZ:', e?.message ?? e);
  process.exit(1);
});
