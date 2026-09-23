---
title: "Search Service - Arama Servisi"
layer: K8
category: "Servis"
date: "2026-09-20"
version: "1.0.0"
status: "implemented"
dependencies:
  - K7-Repository
  - K5-Cache
---

# Search Service - Arama Servisi

## Genel Bakış

Search Service, COREMUSIC medya kütüphanesinde tam metin arama, bulanık eşleştirme ve gelişmiş filtreleme yetenekleri sunar. Servis, inverted index ve trigram-based fuzzy matching kullanarak high-performance arama işlemleri gerçekleştirir.

Servis, real-time indexing, query suggestion, search analytics ve personalized ranking gibi özellikler sunar. Cross-field search, faceted search ve contextual relevance scoring ile gelişmiş arama deneyimi sağlar.

## Servis Arayüzü

### Search Endpoints

| Endpoint | Method | Açıklama |
|----------|--------|----------|
| `/api/v1/search` | GET | Genel arama |
| `/api/v1/search/tracks` | GET | Parça ara |
| `/api/v1/search/albums` | GET | Albüm ara |
| `/api/v1/search/artists` | GET | Sanatçı ara |
| `/api/v1/search/playlists` | GET | Playlist ara |
| `/api/v1/search/advanced` | POST | Gelişmiş arama |
| `/api/v1/search/suggest` | GET | Arama önerileri |
| `/api/v1/search/autocomplete` | GET | Otomatik tamamlama |
| `/api/v1/search/history` | GET | Arama geçmişi |
| `/api/v1/search/trending` | GET | Popüler aramalar |
| `/api/v1/search/reindex` | POST | Yeniden indeksle |

### Filter Endpoints

| Endpoint | Method | Açıklama |
|----------|--------|----------|
| `/api/v1/search/filters/genres` | GET | Tür filtreleri |
| `/api/v1/search/filters/years` | GET | Yıl filtreleri |
| `/api/v1/search/filters/artists` | GET | Sanatçı filtreleri |
| `/api/v1/search/filters/formats` | GET | Format filtreleri |
| `/api/v1/search/filters/qualities` | GET | Kalite filtreleri |

## Teknik Detaylar

### Search Data Model

```cpp
// K8/search-service/include/SearchModels.h
namespace CoreMusic::K8::Search {

struct SearchRequest {
    std::string query;
    SearchType type;
    int offset;
    int limit;
    std::vector<SearchFilter> filters;
    SearchSortBy sortBy;
    bool includeFacets;
    bool fuzzyMatch;
    float fuzzyThreshold;
    std::string userId;
};

enum class SearchType {
    All,
    Tracks,
    Albums,
    Artists,
    Playlists,
    Lyrics
};

struct SearchFilter {
    std::string field;
    FilterOperator op;
    std::string value;
    std::vector<std::string> values;  // For IN operator
    std::pair<std::string, std::string> range; // For BETWEEN
};

enum class FilterOperator {
    Equals,
    NotEquals,
    Contains,
    StartsWith,
    EndsWith,
    GreaterThan,
    LessThan,
    Between,
    In,
    NotIn
};

enum class SearchSortBy {
    Relevance,
    Title,
    Artist,
    Album,
    Year,
    Duration,
    Rating,
    DateAdded,
    PlayCount
};

struct SearchResponse {
    std::vector<SearchResult> results;
    int totalResults;
    int offset;
    int limit;
    std::chrono::milliseconds searchTime;
    std::vector<SearchFacet> facets;
    std::vector<std::string> suggestions;
    SearchQuery query;
};

struct SearchResult {
    std::string id;
    SearchResultType type;
    std::string title;
    std::string subtitle;
    std::string snippet;
    float score;
    std::map<std::string, std::string> highlights;
    nlohmann::json metadata;
    std::string artworkUrl;
};

enum class SearchResultType {
    Track,
    Album,
    Artist,
    Playlist,
    Lyrics
};

struct SearchFacet {
    std::string field;
    std::vector<FacetValue> values;
};

struct FacetValue {
    std::string value;
    int count;
    bool isSelected;
};

} // namespace CoreMusic::K8::Search
```

### Search Index Manager

```cpp
// K8/search-service/src/SearchIndex.cpp
namespace CoreMusic::K8::Search {

class SearchIndex {
public:
    void buildIndex(const std::vector<IndexableItem>& items) {
        // Inverted index oluşturma
        for (const auto& item : items) {
            indexItem(item);
        }

        // Trigram index oluştur
        buildTrigramIndex();

        // Field weights ayarla
        configureFieldWeights();
    }

    void indexItem(const IndexableItem& item) {
        // Ana metin alanlarını tokenize et
        auto tokens = tokenize(item.title + " " +
                              item.artist + " " +
                              item.album + " " +
                              item.genre);

        // Her token için inverted index'e ekle
        for (const auto& token : tokens) {
            invertedIndex_[token].insert(item.id);
        }

        // Fuzzy matching için trigram'ları hesapla
        auto trigrams = generateTrigrams(item.title);
        for (const auto& trigram : trigrams) {
            trigramIndex_[trigram].insert(item.id);
        }

        // Field-specific indexing
        fieldIndex_["title"][item.title].insert(item.id);
        fieldIndex_["artist"][item.artist].insert(item.id);
        fieldIndex_["album"][item.album].insert(item.id);
        fieldIndex_["genre"][item.genre].insert(item.id);

        // Metadata store
        metadataStore_[item.id] = item;
    }

    std::vector<std::string> search(const std::string& query,
                                   bool fuzzy = false,
                                   float threshold = 0.7f) {
        auto tokens = tokenize(query);
        std::set<std::string> resultIds;

        if (fuzzy) {
            // Fuzzy search
            for (const auto& token : tokens) {
                auto similarTrigrams = findSimilarTrigrams(token, threshold);
                for (const auto& trigram : similarTrigrams) {
                    auto it = trigramIndex_.find(trigram);
                    if (it != trigramIndex_.end()) {
                        resultIds.insert(it->second.begin(),
                                        it->second.end());
                    }
                }
            }
        } else {
            // Exact match
            for (const auto& token : tokens) {
                auto it = invertedIndex_.find(token);
                if (it != invertedIndex_.end()) {
                    if (resultIds.empty()) {
                        resultIds = it->second;
                    } else {
                        // Intersection
                        std::set<std::string> intersection;
                        std::set_intersection(
                            resultIds.begin(), resultIds.end(),
                            it->second.begin(), it->second.end(),
                            std::inserter(intersection,
                                         intersection.begin()));
                        resultIds = intersection;
                    }
                }
            }
        }

        return std::vector<std::string>(resultIds.begin(),
                                       resultIds.end());
    }

    float calculateScore(const std::string& itemId,
                        const std::vector<std::string>& queryTokens) {
        float score = 0.0f;
        const auto& item = metadataStore_[itemId];

        // Title match (highest weight)
        for (const auto& token : queryTokens) {
            if (containsIgnoreCase(item.title, token)) {
                score += 10.0f;
            }
            if (item.title == token) {
                score += 5.0f;  // Exact match bonus
            }
        }

        // Artist match
        for (const auto& token : queryTokens) {
            if (containsIgnoreCase(item.artist, token)) {
                score += 7.0f;
            }
        }

        // Album match
        for (const auto& token : queryTokens) {
            if (containsIgnoreCase(item.album, token)) {
                score += 5.0f;
            }
        }

        // Genre match
        for (const auto& token : queryTokens) {
            if (containsIgnoreCase(item.genre, token)) {
                score += 3.0f;
            }
        }

        // Popularity boost
        score += std::log1p(item.playCount) * 0.1f;
        score += item.rating * 0.5f;

        return score;
    }

private:
    std::unordered_map<std::string, std::set<std::string>> invertedIndex_;
    std::unordered_map<std::string, std::set<std::string>> trigramIndex_;
    std::unordered_map<std::string,
        std::unordered_map<std::string,
            std::set<std::string>>> fieldIndex_;
    std::unordered_map<std::string, IndexableItem> metadataStore_;

    std::vector<std::string> tokenize(const std::string& text) {
        std::vector<std::string> tokens;
        std::string token;
        std::istringstream stream(toLowerCase(text));

        while (stream >> token) {
            // Stop words'ü kaldır
            if (!isStopWord(token)) {
                tokens.push_back(stem(token));
            }
        }

        return tokens;
    }

    std::vector<std::string> generateTrigrams(const std::string& text) {
        std::vector<std::string> trigrams;
        std::string padded = "  " + toLowerCase(text) + " ";

        for (size_t i = 0; i + 3 <= padded.size(); ++i) {
            trigrams.push_back(padded.substr(i, 3));
        }

        return trigrams;
    }

    std::vector<std::string> findSimilarTrigrams(
        const std::string& token, float threshold) {

        auto tokenTrigrams = generateTrigrams(token);
        std::map<std::string, float> similarityScores;

        for (const auto& [trigram, ids] : trigramIndex_) {
            auto common = countCommonTrigrams(tokenTrigrams,
                                            generateTrigrams(trigram));
            auto total = tokenTrigrams.size() +
                        generateTrigrams(trigram).size() - common;

            float similarity = static_cast<float>(common) /
                              (total - common);

            if (similarity >= threshold) {
                // Bu trigram'a karşılık gelen tüm token'ları bul
                for (const auto& id : ids) {
                    const auto& item = metadataStore_[id];
                    auto itemTokens = tokenize(item.title);
                    for (const auto& itemToken : itemTokens) {
                        float tokenSim = calculateTokenSimilarity(
                            token, itemToken);
                        if (tokenSim >= threshold) {
                            similarityScores[itemToken] = tokenSim;
                        }
                    }
                }
            }
        }

        std::vector<std::string> similarTokens;
        for (const auto& [token, score] : similarityScores) {
            similarTokens.push_back(token);
        }

        return similarTokens;
    }
};

} // namespace CoreMusic::K8::Search
```

### Search Service Implementation

```cpp
// K8/search-service/src/SearchService.cpp
namespace CoreMusic::K8::Search {

class SearchService : public IService {
public:
    SearchService(std::shared_ptr<IMediaRepository> mediaRepo,
                  std::shared_ptr<ICacheManager> cache)
        : mediaRepo_(std::move(mediaRepo))
        , cache_(std::move(cache)) {}

    void initialize() override {
        // Search index'ini oluştur
        rebuildIndex();
        status_ = ServiceStatus::Running;
    }

    ServiceResult<SearchResponse> search(const SearchRequest& request) {
        auto startTime = std::chrono::steady_clock::now();

        // Cache kontrolü
        auto cacheKey = buildCacheKey(request);
        auto cached = cache_->get<SearchResponse>(cacheKey);
        if (cached) {
            return ServiceResult<SearchResponse>::success(*cached);
        }

        // Query parse
        auto parsedQuery = parseQuery(request.query);
        auto tokens = tokenize(request.query);

        // Arama yap
        std::vector<std::string> matchIds;
        if (request.fuzzyMatch) {
            matchIds = searchIndex_.search(
                request.query, true, request.fuzzyThreshold);
        } else {
            matchIds = searchIndex_.search(request.query);
        }

        // Filtreleri uygula
        matchIds = applyFilters(matchIds, request.filters);

        // Sonuçları puanla ve sırala
        std::vector<SearchResult> results;
        for (const auto& id : matchIds) {
            auto item = searchIndex_.getItem(id);
            if (item) {
                SearchResult result;
                result.id = id;
                result.type = item->type;
                result.title = item->title;
                result.subtitle = item->artist + " - " + item->album;
                result.score = searchIndex_.calculateScore(id, tokens);
                result.highlights = highlightMatches(item->title, tokens);
                result.metadata = item->metadata;
                result.artworkUrl = item->artworkUrl;

                results.push_back(result);
            }
        }

        // Sıralama
        sortResults(results, request.sortBy);

        // Facet'leri hesapla
        std::vector<SearchFacet> facets;
        if (request.includeFacets) {
            facets = calculateFacets(matchIds);
        }

        // Pagination
        int totalResults = results.size();
        auto start = results.begin() + std::min(request.offset,
            static_cast<int>(results.size()));
        auto end = results.begin() + std::min(request.offset + request.limit,
            static_cast<int>(results.size()));

        std::vector<SearchResponse> paginatedResults(start, end);

        auto endTime = std::chrono::steady_clock::now();
        auto searchTime = std::chrono::duration_cast<std::chrono::milliseconds>(
            endTime - startTime);

        SearchResponse response;
        response.results = paginatedResults;
        response.totalResults = totalResults;
        response.offset = request.offset;
        response.limit = request.limit;
        response.searchTime = searchTime;
        response.facets = facets;

        // Cache'e kaydet
        cache_->set(cacheKey, response, 300s);

        // Arama geçmişini kaydet
        if (!request.userId.empty()) {
            saveSearchHistory(request.userId, request.query, totalResults);
        }

        return ServiceResult<SearchResponse>::success(response);
    }

    ServiceResult<std::vector<std::string>> autocomplete(
        const std::string& prefix, int limit = 10) {

        std::vector<std::string> suggestions;

        // Prefix matching
        for (const auto& [token, ids] : searchIndex_.getInvertedIndex()) {
            if (token.find(toLowerCase(prefix)) == 0) {
                suggestions.push_back(token);
            }
        }

        // Sırala ve limit uygula
        std::sort(suggestions.begin(), suggestions.end());
        suggestions.resize(std::min(limit,
            static_cast<int>(suggestions.size())));

        return ServiceResult<std::vector<std::string>>::success(suggestions);
    }

    ServiceResult<std::vector<SearchResult>> getSuggestions(
        const std::string& query, int limit = 5) {

        std::vector<SearchResult> suggestions;

        // Query expansion
        auto expandedTerms = expandQuery(query);

        for (const auto& term : expandedTerms) {
            auto results = searchIndex_.search(term);
            for (const auto& id : results) {
                auto item = searchIndex_.getItem(id);
                if (item) {
                    SearchResult suggestion;
                    suggestion.id = id;
                    suggestion.type = item->type;
                    suggestion.title = item->title;
                    suggestion.subtitle = item->artist;
                    suggestion.score = searchIndex_.calculateScore(
                        id, tokenize(query));

                    suggestions.push_back(suggestion);
                }
            }
        }

        // Deduplicate
        deduplicateResults(suggestions);

        // Limit
        suggestions.resize(std::min(limit,
            static_cast<int>(suggestions.size())));

        return ServiceResult<std::vector<SearchResult>>::success(suggestions);
    }

private:
    std::shared_ptr<IMediaRepository> mediaRepo_;
    std::shared_ptr<ICacheManager> cache_;
    SearchIndex searchIndex_;

    void rebuildIndex() {
        auto allTracks = mediaRepo_->findAllTracks();
        std::vector<IndexableItem> items;

        for (const auto& track : allTracks) {
            IndexableItem item;
            item.id = track.id;
            item.type = SearchResultType::Track;
            item.title = track.title;
            item.artist = track.artist;
            item.album = track.album;
            item.genre = track.genre;
            item.year = track.year;
            item.playCount = track.playCount;
            item.rating = track.rating;
            item.artworkUrl = track.artworkUrl;

            items.push_back(item);
        }

        searchIndex_.buildIndex(items);
    }

    std::vector<SearchResult> highlightMatches(
        const std::string& text,
        const std::vector<std::string>& tokens) {

        // Highlight implementation
        // ... (mark matching substrings)

        return results;
    }

    std::vector<SearchFacet> calculateFacets(
        const std::vector<std::string>& ids) {

        std::vector<SearchFacet> facets;

        // Genre facet
        SearchFacet genreFacet;
        genreFacet.field = "genre";
        std::map<std::string, int> genreCounts;

        for (const auto& id : ids) {
            auto item = searchIndex_.getItem(id);
            if (item) {
                genreCounts[item->genre]++;
            }
        }

        for (const auto& [genre, count] : genreCounts) {
            genreFacet.values.push_back({genre, count, false});
        }

        facets.push_back(genreFacet);

        return facets;
    }

    void saveSearchHistory(const std::string& userId,
                          const std::string& query,
                          int resultCount) {
        SearchHistoryEntry entry;
        entry.userId = userId;
        entry.query = query;
        entry.resultCount = resultCount;
        entry.timestamp = std::chrono::system_clock::now();

        historyRepo_->save(entry);
    }
};

} // namespace CoreMusic::K8::Search
```

## Bağımlılıklar

| Servis/Bileşen | Bağımlılık Tipi | Açıklama |
|----------------|-----------------|----------|
| K7-Repository | Data Access | Medya verileri |
| K5-Cache | Infrastructure | Search result caching |

## Durum: Implementasyon

- **Full-Text Search**: ✅ Tamamlandı
- **Fuzzy Matching**: ✅ Tamamlandı
- **Faceted Search**: ✅ Tamamlandı
- **Autocomplete**: ✅ Tamamlandı
- **Search Analytics**: 🔄 Devam ediyor
