/** Enrich — DB'deki eksik detaylı videoları getInfo ile doldurur + kanalları çeker. */
import { youtubeInfo, youtubeChannel } from '../providers/youtube.ts';
import { recentForEnrich, updateVideoDetail, upsertChannel, getDb } from '../database/db.ts';
import { progressBar } from '../cli/format.ts';

export async function runEnrich(limit: number): Promise<void> {
  const targets = recentForEnrich(limit);
  if (!targets.length) {
    console.log('Zenginleştirilecek video yok (hepsi dolu veya DB boş).');
    return;
  }
  console.log(`${targets.length} video zenginleştiriliyor...`);

  let ok = 0;
  let chOk = 0;
  const seenChannels = new Set<string>();

  for (let i = 0; i < targets.length; i++) {
    const t = targets[i];
    process.stdout.write(
      `\rZenginleştiriliyor: ${progressBar(i, targets.length, 20)} ${i}/${targets.length} ${t.youtubeId}      `,
    );

    const info = await youtubeInfo(t.youtubeId);
    if (info) {
      updateVideoDetail(info);
      ok++;
      if (info.channelId && !seenChannels.has(info.channelId)) {
        seenChannels.add(info.channelId);
        const ch = await youtubeChannel(info.channelId);
        if (ch) {
          upsertChannel(ch);
          chOk++;
        }
      }
    }
  }
  process.stdout.write(
    `\rZenginleştiriliyor: ${progressBar(targets.length, targets.length, 20)} ${targets.length}/${targets.length} tamam\n`,
  );

  const chTotal = (getDb().prepare(`SELECT COUNT(*) AS c FROM channels`).get() as { c: number }).c;
  console.log(`Tamam: ${ok}/${targets.length} video detay · ${chOk} kanal çekildi (toplam ${chTotal}).`);
}
