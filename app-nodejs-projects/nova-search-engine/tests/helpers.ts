// ===========================================
// Test yardımcıları — ortak fixture'lar
// ===========================================

import { analyzeQuery, rankVideos, type QueryAnalysis, type ScoredVideo } from '@novasearch/ai';
import type { SearchQuery, Video } from '@novasearch/common';

/** Heuristic analiz (AI'sız — deterministik davranış). */
export function analyzeQueryHeuristicForTest(query: string): Promise<QueryAnalysis> {
  return analyzeQuery(query, {});
}

function video(overrides: Partial<Video>): Video {
  return {
    youtubeId: 'id',
    url: 'https://www.youtube.com/watch?v=id',
    title: '',
    description: '',
    channelId: 'c',
    channelName: 'ch',
    durationSeconds: 300,
    viewCount: 1000,
    likeCount: 10,
    publishedAt: '2024-01-01',
    thumbnailUrl: '',
    tags: [],
    isShort: false,
    isLive: false,
    qualityScore: 0.5,
    source: 'youtube',
    ...overrides,
  };
}

export const rankVideosFixture = {
  videos(): ScoredVideo[] {
    return [
      { ...video({ youtubeId: 'v1', title: 'Adele Hello official', viewCount: 100_000 }), score: 0, matchedTerms: [], reasons: [] },
      { ...video({ youtubeId: 'v2', title: 'Random vlog', viewCount: 50, durationSeconds: 60 }), score: 0, matchedTerms: [], reasons: [] },
      { ...video({ youtubeId: 'v3', title: 'Adele live in London', viewCount: 5000, isLive: true }), score: 0, matchedTerms: [], reasons: [] },
    ];
  },
  query(overrides: Partial<SearchQuery> = {}): SearchQuery {
    return {
      query: 'adele',
      ai: false,
      result: 20,
      scroll: 50,
      filter: 'all',
      type: 'all',
      uploadDate: 'all',
      sortBy: 'relevance',
      minViews: 0,
      maxViews: 0,
      minScore: 0,
      duration: 'all',
      quality: 'all',
      page: 1,
      limit: 20,
      offline: true,
      auto: false,
      ...overrides,
    };
  },
  rank(analysis: QueryAnalysis): ScoredVideo[] {
    return rankVideos(
      this.videos().map(({ score, matchedTerms, reasons, ...v }) => {
        void score;
        void matchedTerms;
        void reasons;
        return v;
      }),
      analysis,
    );
  },
};
