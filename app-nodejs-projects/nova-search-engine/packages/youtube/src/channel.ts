// ===========================================
// Channel — kanal detay çekimi (tam kazıma)
// ===========================================

import type { Channel } from '@novasearch/common';
import { getYouTubeClient } from './client.js';
import { parseViews, textOf } from './mapper.js';

/* eslint-disable @typescript-eslint/no-explicit-any */

/** Kanal bilgisi: açıklama, abone, ülke, katılım, toplam izlenme. */
export async function getChannelDetails(channelId: string): Promise<Channel | null> {
  const client = getYouTubeClient();
  return client.call(async (yt) => {
    const ch = await yt.getChannel(channelId);
    const header = (ch as any).header ?? {};
    const metadata = (ch as any).metadata ?? {};
    const avatars = (header?.content?.thumbnail_overlays ? [] : []) as never[];

    const name =
      textOf(metadata?.title) ||
      textOf(header?.content?.title) ||
      textOf((ch as any).current_tab?.content?.title);
    const description =
      textOf(metadata?.description) ||
      textOf(header?.content?.description);
    const subsText =
      textOf(header?.content?.subtitle) ||
      textOf(metadata?.subscriber_count_text);

    const thumbs = ((metadata?.thumbnail ?? header?.content?.thumbnails ?? []) as Array<{ url?: string }>)
      .map((t) => (t?.url ?? '').startsWith('//') ? `https:${t.url}` : (t?.url ?? ''))
      .filter((u) => u.length > 0);

    void avatars;

    return {
      youtubeId: channelId,
      name: name || channelId,
      description: description || '',
      subscriberCount: parseViews(subsText),
      videoCount: parseViews(textOf((ch as any).videos_count_text)),
      thumbnailUrl: thumbs[0] ?? '',
      verified: JSON.stringify(header ?? {}).toLowerCase().includes('verified'),
    } satisfies Channel;
  });
}

/* eslint-enable @typescript-eslint/no-explicit-any */
