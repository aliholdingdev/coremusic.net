/** Ortak veri tipleri — Domain katmanı (dış bağımlılık yok) */

export interface VideoRecord {
  youtubeId: string;
  title: string;
  description: string;
  channelId: string;
  channelName: string;
  durationSec: number;
  viewCount: number;
  likeCount: number | null;
  publishedAt: string | null; // ISO
  category: string | null;
  tags: string; // "a b c" (FTS için boşluklu)
  thumbnailUrl: string | null;
  firstSeenAt: string;
  lastSeenAt: string;
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
  scroll: number; // toplanacak sayfa hedefi
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
