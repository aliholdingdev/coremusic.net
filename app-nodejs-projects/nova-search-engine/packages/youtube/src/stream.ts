// ===========================================
// Stream Info — GoogleVideo akış çözücü
// youtubei.js üzerinden stream/format bilgisi
// (kalite, mimeType, URL). Metadata motorunun
// yan bilgisi; video akışı taşımaz.
// ===========================================

import { getYouTubeClient } from './client.js';
import { parseDuration } from './mapper.js';

/* eslint-disable @typescript-eslint/no-explicit-any */

export interface StreamFormat {
  itag: number;
  quality: string;
  mimeType: string;
  url: string;
  hasAudio: boolean;
  hasVideo: boolean;
  contentLength?: string;
}

export interface StreamInfo {
  youtubeId: string;
  title: string;
  durationSeconds: number;
  isLive: boolean;
  formats: StreamFormat[];
}

/** GoogleVideo (Innertube) üzerinden akış/format bilgisi getir. */
export async function getStreamInfo(videoId: string): Promise<StreamInfo | null> {
  const client = getYouTubeClient();
  return client.call(async (yt) => {
    // Not: IOS istemcisi doğrudan googlevideo URL'leri döndürür (2026-10 doğrulandı);
    // web istemcisinde URL'ler şifreli/boş gelebilir.
    let info: unknown;
    try {
      info = await yt.getBasicInfo(videoId, { client: 'IOS' });
    } catch {
      info = await yt.getInfo(videoId);
    }
    const basic = (info as any).basic_info ?? {};
    const streaming = (info as any).streaming_data ?? {};
    const raw: any[] = [...(streaming.formats ?? []), ...(streaming.adaptive_formats ?? [])];
    const player = (yt as any).session?.player;

    const formats: StreamFormat[] = [];
    for (const f of raw) {
      // URL'ler şifreliyse Player ile çözülür; IOS'ta doğrudan gelir.
      let url = String(f.url ?? '');
      if (!url && typeof f.decipher === 'function') {
        try {
          url = String(await f.decipher(player));
        } catch {
          url = '';
        }
      }
      formats.push({
        itag: Number(f.itag ?? 0),
        quality: String(f.quality_label ?? f.quality ?? ''),
        mimeType: String(f.mime_type ?? ''),
        url,
        hasAudio: Boolean(f.audio_quality ?? f.has_audio),
        hasVideo: Boolean(f.quality_label ?? f.width ?? f.has_video),
        contentLength: f.content_length ? String(f.content_length) : undefined,
      });
    }

    return {
      youtubeId: String(basic.id ?? videoId),
      title: String(basic.title ?? ''),
      durationSeconds: Number(basic.duration ?? 0) || parseDuration(String(basic.length_text ?? '')),
      isLive: Boolean(basic.is_live),
      formats: formats.filter((f) => f.url.length > 0),
    };
  });
}

/* eslint-enable @typescript-eslint/no-explicit-any */
