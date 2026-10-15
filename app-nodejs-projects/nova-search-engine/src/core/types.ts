/** Ortak veri tipleri — Domain katmanı (dış bağımlılık yok) */

export interface VideoRecord {
  youtubeId: string;
  title: string;
  description: string;
  channelId: string;
  channelName: string;
  /** Kanal handle bağlantısı (microformat.channel.url → https://www.youtube.com/@handle). */
  channelUrl?: string | null;
  durationSec: number;
  viewCount: number;
  likeCount: number | null;
  publishedAt: string | null; // ISO
  category: string | null;
  tags: string; // "a b c" (FTS için boşluklu)
  thumbnailUrl: string | null;
  firstSeenAt: string;
  lastSeenAt: string;
  /** YouTube Shorts shelf'inden geldi (süre verisi yok — platform tanımı short). */
  isShort?: boolean;
}

/** Video yorumu (youtubei.js CommentView → düz kayıt). */
export interface CommentRecord {
  commentId: string;
  authorName: string;
  authorChannelId: string | null;
  text: string;
  publishedText: string | null; // "6 years ago" (YouTube göreli metni)
  likeCount: number; // "43K" → 43000
  replyCount: number; // "593" → 593
  isPinned: boolean;
  isChannelOwner: boolean;
  url: string; // kanonik yorum bağlantısı: https://www.youtube.com/watch?v=<videoId>&lc=<commentId>
}

/** Benzer/önerilen video (watch_next_feed LockupView → düz kayıt). */
export interface RelatedVideo {
  youtubeId: string;
  title: string;
  channelName: string;
  viewCountText: string; // "336K" (ham metin)
  viewCount: number; // parseCompactCount → 336000
  publishedText: string | null; // "4mo ago"
  contentType: 'VIDEO' | 'SHORT';
  url: string;
  score?: number; // deterministik temel skor (varsa)
  aiScore?: number; // AI benzerlik kalite skoru (0-100)
  aiReason?: string; // AI gerekçesi
}

/** Video detay payload'ı — API / CLI / Web aynı yapıyı paylaşır. */
export interface VideoDetailPayload {
  video: (VideoRecord & { url: string }) | null;
  comments: CommentRecord[];
  related: RelatedVideo[];
  meta: {
    videoId: string;
    source: 'db' | 'youtube' | 'cache' | 'miss';
    commentsRequested: number;
    relatedRequested: number;
    commentsCount: number;
    relatedCount: number;
    commentsFromCache: boolean;
    relatedFromCache: boolean;
    aiEnabled: boolean; // AI benzerlik skoru istendi ve kullanılabilir oldu mu
    aiScored: number;
    offline: boolean;
    degraded: boolean; // YouTube erişilemedi → boş diziler (fail-safe)
    tookMs: number;
    dbVideos: number;
  };
}

export interface VideoDetailOptions {
  comments: number; // 0-50
  related: number; // 0-20
  ai: boolean; // benzer videolara AI skoru
  offline: boolean;
}

export interface SearchFilters {
  filter?: 'v' | 's' | 'sh'; // video / song / short
  sortBy?: 'relevance' | 'views' | 'date' | 'rating';
  minViews?: number;
  maxViews?: number;
  minScore?: number;
  quality?: number;
  duration?: string; // ">600" | "60-300"
  type?: string;
  uploadDate?: string; // "2015" | "<=1y"
  limit: number;
  page: number;
}

export interface SearchOptions extends SearchFilters {
  query: string;
  extraQuery?: string;
  /** AI'ya gidecek özel prompt — analiz/skor/genişletme sistem prompt'una eklenir. */
  aiPrompt?: string;
  ai: boolean;
  deep: boolean;
  explain: boolean;
  offline: boolean;
  scroll: number; // toplanacak sayfa hedefi · -1 = auto (tüm sayfalar, COLLECT_AUTO_MAX_PAGES)
  /** Her sonuç için getInfo çağırıp description/like/süre/tarih/kanal doldurur (N istek — yavaş). */
  detail: boolean;
}

export interface ScoredResult extends VideoRecord {
  score: number; // 0-100 deterministik
  aiScore?: number; // 0-100 (AI varsa)
  aiReason?: string;
  matchPct?: number;
  url?: string; // tam izleme bağlantısı: https://www.youtube.com/watch?v=<id>
}

export type SearchSource = 'ram' | 'disk' | 'db' | 'youtube';

export interface SearchResultPayload {
  results: ScoredResult[];
  meta: {
    query: string;
    source: SearchSource;
    total: number;
    tookMs: number;
    aiEnabled: boolean;
    offline: boolean;
    expandedQueries?: string[];
    collected?: number;
    limit?: number;
    page?: number;
    aiScored?: number; // bu sayfada AI skoru alan sonuç sayısı
    dbVideos?: number; // DB'deki toplam video
    scroll?: number; // uygulanan sayfa hedefi · -1 = auto (tüm sayfalar)
    detailEnriched?: number; // -detail ile getInfo ile zenginleştirilen sonuç sayısı
    aiPrompt?: string; // uygulanan özel AI prompt'u (varsa)
    aiPromptApplied?: boolean;
    filters?: Partial<SearchFilters>; // uygulanan filtreler
  };
}

export type JobType = 'search' | 'video' | 'channel' | 'playlist' | 'refresh';

export interface JobRow {
  id: number;
  type: JobType;
  payload: string; // JSON
  status: 'pending' | 'done' | 'failed';
  attempts: number;
  createdAt: string;
}
