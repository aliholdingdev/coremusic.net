// ===========================================
// @novasearch/common — Ortak Tipler
// ===========================================

/** Normalize edilmis video kaydi (tum provider'lar bu sozlesmeye yazar). */
export interface Video {
  youtubeId: string;
  title: string;
  description: string;
  channelId: string;
  channelName: string;
  durationSeconds: number;
  viewCount: number;
  likeCount: number;
  publishedAt: string | null;
  thumbnailUrl: string;
  tags: string[];
  isShort: boolean;
  isLive: boolean;
  qualityScore: number;
  source: 'youtube' | 'local';

  // --- Zengin metadata (YouTube'a göre tam kazıma) ---
  /** İzleme URL'i (canonical) */
  url: string;
  channelUrl?: string;
  /** Ham izlenme metni ("1.234.567 izlenme") */
  viewCountText?: string;
  /** Ham yayın metni ("2 years ago", "1 Haz 2016") */
  publishedText?: string;
  commentCount?: number;
  category?: string;
  /** Tüm çözünürlükler */
  thumbnails?: string[];
  /** Rozetler: "4K", "HD", "CC", "Yeni" vb. */
  badges?: string[];
  isFamilySafe?: boolean;
  isUnlisted?: boolean;
  isCrawlable?: boolean;
  channelSubscriberCount?: number;
  channelVerified?: boolean;
  /** Altyazı izleri (dil + URL) */
  captionTracks?: Array<{ language: string; name: string; url: string; auto: boolean }>;
  /** İlk yorumlar (özet) */
  topComments?: Array<{ author: string; text: string; likes: number }>;
  /** İlgili videolar (watch_next) */
  relatedIds?: string[];
  /** Tespit edilen içerik dili */
  language?: 'tr' | 'en' | 'other';
  /** Spam/tıklama tuzağı skoru (0-1; yalnız işarettir) */
  spamScore?: number;
}

export interface Channel {
  youtubeId: string;
  name: string;
  description: string;
  subscriberCount: number;
  videoCount: number;
  thumbnailUrl: string;
  verified: boolean;
}

/** Kullanici arama istegi (CLI/Web/API ayni sozlesmeyi kullanir). */
export interface SearchQuery {
  query: string;
  /** AI destekli genisletme/suzme aktif mi */
  ai: boolean;
  /** AI'a ek yonlendirme promptu */
  aiQuery?: string;
  extraQuery?: string;
  /** Gosterilecek sonuc */
  result: number;
  /** Kaynaktan cekilecek (overscan) */
  scroll: number;
  filter: ContentFilter;
  type: SearchType;
  uploadDate: UploadDate;
  sortBy: SortBy;
  minViews: number;
  maxViews: number;
  minScore: number;
  duration: DurationFilter;
  quality: QualityFilter;
  page: number;
  limit: number;
  offline: boolean;
  /** Arka planda otomatik derin toplama (auto-scroll/collect) */
  auto: boolean;
}

export type ContentFilter = 'all' | 'v' | 's' | 'sh';
export type SearchType = 'all' | 'video' | 'channel' | 'playlist';
export type UploadDate = 'all' | 'hour' | 'today' | 'week' | 'month' | 'year';
export type SortBy = 'relevance' | 'rating' | 'upload_date' | 'view_count';
export type DurationFilter = 'all' | 'short' | 'medium' | 'long';
export type QualityFilter = 'all' | 'high' | 'medium' | 'low';

/** Zenginleştirilmiş sonuç: skor + eşleşen terimler + neden önerildi. */
export interface EnrichedVideo extends Video {
  score: number;
  matchedTerms: string[];
  /** "Neden önerildi?" — kullanıcıya açıklanan gerekçeler */
  reasons: string[];
}

/** Arama motoru ilerleme olayı (CLI progressbar / web durumu). */
export interface SearchProgressEvent {
  step: 'analiz' | 'cache' | 'db' | 'toplama' | 'skorlama' | 'bitti';
  current?: number;
  total?: number;
  note?: string;
}

export interface SearchResponse {
  query: string;
  videos: EnrichedVideo[];
  total: number;
  source: 'cache' | 'local-db' | 'youtube' | 'mixed';
  offline: boolean;
  tookMs: number;
  aiUsed: boolean;
  /** AI/heuristic sorgu analizi (şeffaflık: genişletmeler + ipuçları) */
  analysis: {
    expandedQueries: string[];
    hints: Record<string, unknown>;
    /** Kullanıcının verdiği AI yönlendirmeleri (q / extraQuery) */
    directive?: string;
  };
  /** Benzer öneriler (Recommendation Engine) */
  recommendations?: EnrichedVideo[];
}
