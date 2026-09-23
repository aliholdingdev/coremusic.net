---
title: "SPA Router - Client-Side Routing"
layer: K9
category: "API & Routing"
date: 2026-09-20
status: "tamamlandı"
---

# SPA Router

## Genel Bakış

SPA Router, COREMUSIC web uygulamasının client-side routing mekanizmasını yönetir. History API (pushState/popState) kullanarak sayfa yenileme olmadan navigasyon sağlar. Route-based code splitting, authenticated routes, breadcrumbs ve deep linking desteği sunar.

Router, React Router v6 veya benzeri bir kütüphane üzerine inşa edilmiştir ve BFF API ile entegre çalışır. Her route için lazy-loaded component'ler ve prefetch stratejileri uygular.

## API Tanımı

### Route Tanımları

| Route | Component | Auth | Prefetch | Açıklama |
|-------|-----------|------|----------|----------|
| `/` | HomePage | - | Dashboard BFF | Ana sayfa |
| `/login` | LoginPage | - | - | Giriş sayfası |
| `/register` | RegisterPage | - | - | Kayıt sayfası |
| `/dashboard` | DashboardPage | ✅ | Dashboard BFF | Kullanıcı dashboard |
| `/player` | PlayerPage | ✅ | Player BFF | Oynatıcı |
| `/library` | LibraryPage | ✅ | Library BFF | Kütüphane |
| `/library/tracks` | TracksPage | ✅ | Library BFF | Şarkı listesi |
| `/library/albums` | AlbumsPage | ✅ | Library BFF | Albüm listesi |
| `/library/artists` | ArtistsPage | ✅ | Library BFF | Sanatçı listesi |
| `/playlist/:id` | PlaylistPage | ✅ | Playlist BFF | Playlist detay |
| `/search` | SearchPage | ✅ | - | Arama |
| `/search/:query` | SearchResultsPage | ✅ | Search BFF | Arama sonuçları |
| `/settings` | SettingsPage | ✅ | - | Ayarlar |
| `/settings/profile` | ProfileSettings | ✅ | User BFF | Profil ayarları |
| `/settings/equalizer` | EqualizerSettings | ✅ | EQ BFF | EQ ayarları |
| `/settings/audio` | AudioSettings | ✅ | - | Ses ayarları |
| `/ai/recommendations` | AIRecommendations | ✅ | AI BFF | AI önerileri |
| `/ai/insights` | AIInsights | ✅ | AI BFF | AI istatistikleri |

## Teknik Detaylar

### SPA Router Mimarisi

```
┌─────────────────────────────────────────────────────────────┐
│                     SPA APPLICATION                         │
│                                                             │
│  ┌──────────────────────────────────────────────────────┐  │
│  │                   Router Provider                     │  │
│  │              (React Router v6 / TanStack)             │  │
│  │                                                      │  │
│  │  ┌────────────┐  ┌────────────┐  ┌────────────┐    │  │
│  │  │   Route    │  │   Route    │  │   Route    │    │  │
│  │  │   Config   │  │   Guards   │  │   Loader   │    │  │
│  │  └────────────┘  └────────────┘  └────────────┘    │  │
│  │                                                      │  │
│  │  ┌──────────────────────────────────────────────┐   │  │
│  │  │            Outlet (Nested Routes)             │   │  │
│  │  │                                              │   │  │
│  │  │  ┌──────────┐  ┌──────────┐  ┌──────────┐  │   │  │
│  │  │  │ Layout   │  │ Sidebar  │  │ Main     │  │   │  │
│  │  │  │ Provider │  │          │  │ Content  │  │   │  │
│  │  │  └──────────┘  └──────────┘  └──────────┘  │   │  │
│  │  └──────────────────────────────────────────────┘   │  │
│  └──────────────────────────────────────────────────────┘  │
│                                                             │
│  ┌──────────────────────────────────────────────────────┐  │
│  │               Navigation Middleware                   │  │
│  │  ┌──────────┐  ┌──────────┐  ┌──────────┐          │  │
│  │  │  Auth    │→ │  Prefetch│→ │  Analytics│          │  │
│  │  │  Guard   │  │  Strategy│  │  Tracker  │          │  │
│  │  └──────────┘  └──────────┘  └──────────┘          │  │
│  └──────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
```

### Route Configuration

```typescript
// routes.tsx
import { lazy } from "react";
import { RouteObject } from "react-router-dom";
import { AuthGuard } from "@/guards/AuthGuard";
import { DashboardLayout } from "@/layouts/DashboardLayout";

// Lazy-loaded components (code splitting)
const HomePage = lazy(() => import("@/pages/HomePage"));
const LoginPage = lazy(() => import("@/pages/LoginPage"));
const DashboardPage = lazy(() => import("@/pages/DashboardPage"));
const PlayerPage = lazy(() => import("@/pages/PlayerPage"));
const LibraryPage = lazy(() => import("@/pages/library/LibraryPage"));
const TracksPage = lazy(() => import("@/pages/library/TracksPage"));
const AlbumsPage = lazy(() => import("@/pages/library/AlbumsPage"));
const ArtistsPage = lazy(() => import("@/pages/library/ArtistsPage"));
const PlaylistPage = lazy(() => import("@/pages/PlaylistPage"));
const SearchPage = lazy(() => import("@/pages/SearchPage"));
const SettingsPage = lazy(() => import("@/pages/settings/SettingsPage"));
const AIRecommendations = lazy(() => import("@/pages/ai/AIRecommendations"));

export const routes: RouteObject[] = [
  {
    path: "/",
    element: <HomePage />,
  },
  {
    path: "/login",
    element: <LoginPage />,
  },
  {
    path: "/register",
    element: <RegisterPage />,
  },
  {
    path: "/dashboard",
    element: (
      <AuthGuard>
        <DashboardLayout />
      </AuthGuard>
    ),
    children: [
      {
        index: true,
        element: <DashboardPage />,
        handle: {
          breadcrumb: () => [{ label: "Dashboard", path: "/dashboard" }],
          prefetch: "/bff/web/dashboard",
        },
      },
      {
        path: "player",
        element: <PlayerPage />,
        handle: {
          breadcrumb: (parent) => [
            ...parent,
            { label: "Player", path: "/dashboard/player" },
          ],
          prefetch: "/bff/web/player",
        },
      },
      {
        path: "library",
        element: <LibraryPage />,
        handle: {
          breadcrumb: (parent) => [
            ...parent,
            { label: "Library", path: "/dashboard/library" },
          ],
          prefetch: "/bff/web/library",
        },
        children: [
          {
            path: "tracks",
            element: <TracksPage />,
            handle: {
              breadcrumb: (parent) => [
                ...parent,
                { label: "Tracks", path: "/dashboard/library/tracks" },
              ],
            },
          },
          {
            path: "albums",
            element: <AlbumsPage />,
            handle: {
              breadcrumb: (parent) => [
                ...parent,
                { label: "Albums", path: "/dashboard/library/albums" },
              ],
            },
          },
          {
            path: "artists",
            element: <ArtistsPage />,
            handle: {
              breadcrumb: (parent) => [
                ...parent,
                { label: "Artists", path: "/dashboard/library/artists" },
              ],
            },
          },
        ],
      },
      {
        path: "playlist/:id",
        element: <PlaylistPage />,
        handle: {
          breadcrumb: (parent, params) => [
            ...parent,
            { label: `Playlist ${params.id}`, path: `/dashboard/playlist/${params.id}` },
          ],
          prefetch: (params) => `/bff/web/playlist/${params.id}`,
        },
      },
      {
        path: "search",
        element: <SearchPage />,
        handle: {
          breadcrumb: (parent) => [
            ...parent,
            { label: "Search", path: "/dashboard/search" },
          ],
        },
      },
      {
        path: "search/:query",
        element: <SearchResultsPage />,
        handle: {
          breadcrumb: (parent, params) => [
            ...parent,
            { label: `"${params.query}"`, path: `/dashboard/search/${params.query}` },
          ],
          prefetch: (params) => `/bff/web/search/${params.query}`,
        },
      },
      {
        path: "settings",
        element: <SettingsPage />,
        handle: {
          breadcrumb: (parent) => [
            ...parent,
            { label: "Settings", path: "/dashboard/settings" },
          ],
        },
      },
      {
        path: "ai",
        children: [
          {
            path: "recommendations",
            element: <AIRecommendations />,
            handle: {
              breadcrumb: (parent) => [
                ...parent,
                { label: "AI Recommendations", path: "/dashboard/ai/recommendations" },
              ],
              prefetch: "/bff/web/ai/insights",
            },
          },
        ],
      },
    ],
  },
  {
    path: "*",
    element: <NotFoundPage />,
  },
];
```

### Route Guards

```typescript
// guards/AuthGuard.tsx
import { Navigate, useLocation } from "react-router-dom";
import { useAuth } from "@/hooks/useAuth";

interface AuthGuardProps {
  children: React.ReactNode;
  requiredRole?: string;
}

export function AuthGuard({ children, requiredRole }: AuthGuardProps) {
  const { isAuthenticated, user, isLoading } = useAuth();
  const location = useLocation();

  if (isLoading) {
    return <LoadingSpinner />;
  }

  if (!isAuthenticated) {
    // Redirect to login, preserve intended destination
    return (
      <Navigate
        to="/login"
        state={{ from: location.pathname }}
        replace
      />
    );
  }

  if (requiredRole && user?.role !== requiredRole) {
    return <Navigate to="/unauthorized" replace />;
  }

  return <>{children}</>;
}
```

### Prefetch Strategy

```typescript
// prefetch/PrefetchStrategy.ts
import { prefetchQuery } from "@tanstack/react-query";

class PrefetchStrategy {
  private prefetchQueue: Map<string, Promise<void>> = new Map();

  // Route hover'da prefetch
  async onRouteHover(routePath: string): Promise<void> {
    const prefetchUrl = this.getPrefetchUrl(routePath);
    if (!prefetchUrl || this.prefetchQueue.has(prefetchUrl)) return;

    const promise = this.prefetchBFF(prefetchUrl);
    this.prefetchQueue.set(prefetchUrl, promise);

    await promise;
    this.prefetchQueue.delete(prefetchUrl);
  }

  // Intersection Observer ile visible items prefetch
  onItemVisible(itemIds: string[]): void {
    itemIds.forEach(id => {
      const url = `/bff/web/tracks/${id}`;
      if (!this.prefetchQueue.has(url)) {
        prefetchQuery({
          queryKey: ["track", id],
          queryFn: () => fetch(url).then(r => r.json()),
          staleTime: 60000,
        });
      }
    });
  }

  // Kullanıcı pattern'inden predictive prefetch
  async predictivePrefetch(
    currentRoute: string,
    navigationHistory: string[]
  ): Promise<void> {
    const pattern = this.analyzePattern(navigationHistory);
    const likelyNextRoutes = pattern.getMostLikely(3);

    likelyNextRoutes.forEach(route => {
      const prefetchUrl = this.getPrefetchUrl(route);
      if (prefetchUrl) {
        setTimeout(() => this.prefetchBFF(prefetchUrl), 100);
      }
    });
  }
}
```

### History API Integration

```typescript
// router/history.ts
import { createBrowserHistory } from "history";

const history = createBrowserHistory({
  basename: "/app", // base path
});

// Custom history handler
history.listen((location, action) => {
  // Analytics tracking
  analytics.track("page_view", {
    path: location.pathname,
    search: location.search,
    action, // PUSH, REPLACE, POP
  });

  // Scroll restoration
  if (action === "POP") {
    const savedPosition = sessionStorage.getItem(
      `scroll_${location.pathname}`
    );
    if (savedPosition) {
      window.scrollTo(0, parseInt(savedPosition, 10));
    }
  }
});

// Scroll position kaydetme
export function saveScrollPosition(): void {
  const path = window.location.pathname;
  sessionStorage.setItem(
    `scroll_${path}`,
    String(window.scrollY)
  );
}
```

## Konfigürasyon

```yaml
# spa-router-config.yaml
spa_router:
  base_path: "/app"
  default_locale: "tr"
  supported_locales: ["tr", "en", "de"]

  features:
    code_splitting: true
    prefetch_on_hover: true
    prefetch_on_visible: true
    predictive_prefetch: true
    scroll_restoration: true
    breadcrumbs: true

  code_splitting:
    strategy: "route"  # route | component | none
    max_bundle_size: "250kb"
    preload_threshold: 3  # 3 routes ahead

  prefetch:
    enabled: true
    max_concurrent: 3
    cache_ttl: 60000  # ms
    debounce_ms: 150

  analytics:
    track_page_views: true
    track_navigation_timing: true
    track_scroll_depth: true
```

## Bağımlılıklar

### Bağımlı Olduğu
- **K9 BFF**: Prefetch URL'leri
- **React Router v6 / TanStack Router**: Core routing
- **React Query**: Data fetching & caching

### Bağımlı Olan
- **K11 Web**: Web uygulaması

## Durum: Implementasyon

- [x] Route configuration
- [x] Auth guards
- [x] Code splitting (lazy loading)
- [x] Breadcrumbs
- [ ] Prefetch strategy
- [ ] Predictive prefetch
- [ ] Scroll restoration
- [ ] Analytics integration
- [ ] Deep linking
- [ ] 404 handling
- [ ] Route transitions (animations)
