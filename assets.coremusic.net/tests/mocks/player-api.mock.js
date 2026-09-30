/**
 * player-api.mock.js — PlayerInfoComponent API mock
 * 
 * Provides fetch responses for:
 * - GET /api/player/current — Current track info
 * - GET /api/player/progress — Real-time progress
 * - POST /api/player/seek — Seek request
 * - POST /api/player/toggle — Play/pause toggle
 * 
 * Usage:
 * import playerApiMock from './player-api.mock.js';
 * global.fetch = playerApiMock.create();
 */

export const mockResponses = {
  currentTrack: {
    song: 'Aşkın Yolunda',
    artist: 'Göksel',
    album: 'Sensiz Olmaz',
    imgCover: '/Image/res-pink/album-goksel.png',
    seekPct: 45,
    elapsed: '2:30',
    duration: '5:45',
    bitrate: '320 kbps',
    status: 'playing',
  },

  progress: {
    seekPct: 45,
    elapsed: '2:30',
    duration: '5:45',
    isPlaying: true,
  },

  seekSuccess: {
    success: true,
    seekPct: 75,
    message: 'Seek başarılı',
  },

  toggleSuccess: {
    success: true,
    isPlaying: false,
    message: 'Play/pause toggle başarılı',
  },

  errorNotFound: {
    error: 'Track not found',
    statusCode: 404,
  },

  errorUnauthorized: {
    error: 'Unauthorized',
    statusCode: 401,
  },

  errorServerError: {
    error: 'Internal server error',
    statusCode: 500,
  },
};

/**
 * Creates a mock fetch function.
 * 
 * @param {object} [options={}]
 * @param {string} [options.scenario='success'] — 'success', 'notfound', 'unauthorized', 'servererror', 'network'
 * @returns {Function} Mock fetch with proper Response interface
 */
export function createMockFetch(options = {}) {
  const { scenario = 'success' } = options;

  return async function mockFetch(url, config = {}) {
    // Network error scenario
    if (scenario === 'network') {
      return Promise.reject(new Error('Network error'));
    }

    // Parse URL to determine endpoint
    const isCurrentTrack = url.includes('/api/player/current');
    const isProgress = url.includes('/api/player/progress');
    const isSeek = url.includes('/api/player/seek');
    const isToggle = url.includes('/api/player/toggle');

    let status = 200;
    let body = null;

    // Scenario routing
    if (scenario === 'notfound') {
      status = 404;
      body = mockResponses.errorNotFound;
    } else if (scenario === 'unauthorized') {
      status = 401;
      body = mockResponses.errorUnauthorized;
    } else if (scenario === 'servererror') {
      status = 500;
      body = mockResponses.errorServerError;
    } else if (scenario === 'success') {
      // Endpoint-based response
      if (isCurrentTrack) {
        body = mockResponses.currentTrack;
      } else if (isProgress) {
        body = mockResponses.progress;
      } else if (isSeek) {
        body = mockResponses.seekSuccess;
      } else if (isToggle) {
        body = mockResponses.toggleSuccess;
      } else {
        status = 404;
        body = { error: 'Endpoint not found' };
      }
    }

    // Simulate delay
    await new Promise((resolve) => setTimeout(resolve, 10));

    // Return Response-like object
    return {
      ok: status >= 200 && status < 300,
      status,
      statusText: status === 200 ? 'OK' : 'Error',
      json: async () => body,
      text: async () => JSON.stringify(body),
      headers: new Map([['content-type', 'application/json']]),
    };
  };
}

/**
 * Creates a mock EventSource for SSE (Server-Sent Events).
 * Simulates real-time progress updates.
 * 
 * @param {object} [options={}]
 * @returns {EventSource-like object}
 */
export function createMockEventSource(options = {}) {
  const { initialProgress = 0, updateInterval = 100 } = options;

  let listeners = {};
  let currentProgress = initialProgress;
  let isOpen = true;

  // Simulate auto-increment
  const interval = setInterval(() => {
    if (isOpen && currentProgress < 100) {
      currentProgress += 5;
      if (listeners.message) {
        const event = new Event('message');
        event.data = JSON.stringify({
          seekPct: currentProgress,
          elapsed: `${Math.floor(currentProgress / 2)}:${String(Math.floor((currentProgress % 2) * 30)).padStart(2, '0')}`,
        });
        listeners.message(event);
      }
    }
  }, updateInterval);

  return {
    addEventListener: (eventName, handler) => {
      listeners[eventName] = handler;
      if (eventName === 'open') {
        // Simulate connection open
        setTimeout(() => handler(new Event('open')), 10);
      }
    },
    removeEventListener: (eventName) => {
      delete listeners[eventName];
    },
    close: () => {
      isOpen = false;
      clearInterval(interval);
    },
    readyState: 1, // OPEN
    CONNECTING: 0,
    OPEN: 1,
    CLOSED: 2,
  };
}

export default {
  mockResponses,
  createMockFetch,
  createMockEventSource,
};
