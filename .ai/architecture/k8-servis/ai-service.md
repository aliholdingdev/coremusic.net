---
title: "AI Service - Yapay Zeka Servisi"
layer: K8
category: "Servis"
date: "2026-09-20"
version: "1.0.0"
status: "implemented"
dependencies:
  - K9-AI
  - K7-Repository
  - K5-Cache
---

# AI Service - Yapay Zeka Servisi

## Genel Bakış

AI Service, COREMUSIC'in yapay zeka yeteneklerini yöneten merkezi servistir. Müzik analizi, öneri üretme, sesli komut işleme ve personalized music experiences gibi AI tabanlı tüm işlevleri orkestra eder. Servis, K9 AI Engine ile entegre çalışarak real-time inference sağlar.

Servis, deep learning modellerini kullanarak music information retrieval (MIR), mood analysis, genre classification ve personalized recommendation gibi gelişmiş özellikler sunar. edge AI capabilities ile low-latency processing ve privacy-first design prensiplerine sahiptir.

## Servis Arayüzü

### Analysis Endpoints

| Endpoint | Method | Açıklama |
|----------|--------|----------|
| `/api/v1/ai/analyze/track/{id}` | GET | Parça analizi |
| `/api/v1/ai/analyze/track/{id}/detailed` | GET | Detaylı analiz |
| `/api/v1/ai/analyze/playlist/{id}` | GET | Playlist analizi |
| `/api/v1/ai/analyze/library` | GET | Kütüphane analizi |
| `/api/v1/ai/analyze/audio` | POST | Ham ses analizi |
| `/api/v1/ai/analyze/mood` | POST | Mood analizi |
| `/api/v1/ai/analyze/similarity` | POST | Benzerlik analizi |

### Recommendation Endpoints

| Endpoint | Method | Açıklama |
|----------|--------|----------|
| `/api/v1/ai/recommendations` | GET | Kişisel öneriler |
| `/api/v1/ai/recommendations/similar/{trackId}` | GET | Benzer parçalar |
| `/api/v1/ai/recommendations/artist/{artistId}` | GET | Sanatçı önerileri |
| `/api/v1/ai/recommendations/mood/{mood}` | GET | Mood bazlı öneriler |
| `/api/v1/ai/recommendations/discover` | GET | Keşif önerileri |
| `/api/v1/ai/recommendations/radio` | GET | Radyo istasyonu |
| `/api/v1/ai/recommendations/history` | GET | Öneri geçmişi |

### Voice Processing Endpoints

| Endpoint | Method | Açıklama |
|----------|--------|----------|
| `/api/v1/ai/voice/process` | POST | Sesli komut işleme |
| `/api/v1/ai/voice/transcribe` | POST | Ses→metin dönüştürme |
| `/api/v1/ai/voice/synthesize` | POST | Metin→ses dönüştürme |
| `/api/v1/ai/voice/identify` | POST | Müzik tanıma |
| `/api/v1/ai/voice/commands` | GET | Mevcut komutlar |

### Learning Endpoints

| Endpoint | Method | Açıklama |
|----------|--------|----------|
| `/api/v1/ai/learn/feedback` | POST | Geri bildirim gönder |
| `/api/v1/ai/learn/preferences` | GET | Öğrenilen tercihler |
| `/api/v1/ai/learn/profile` | GET | Kullanıcı profili |
| `/api/v1/ai/learn/train` | POST | Model eğitimi |

## Teknik Detaylar

### Music Analysis Models

```cpp
// K8/ai-service/include/AnalysisModels.h
namespace CoreMusic::K8::AI {

struct TrackAnalysis {
    std::string trackId;
    AudioFeatures audioFeatures;
    MusicStructure structure;
    MoodAnalysis mood;
    GenreClassification genre;
    QualityMetrics quality;
    std::vector<TimestampedEvent> events;
    AnalysisConfidence confidence;
};

struct AudioFeatures {
    float tempo;                 // BPM
    float key;                   // Musical key (0-11)
    float mode;                  // Major (1) / Minor (0)
    float timeSignature;         // 3/4, 4/4, etc.
    float energy;                // 0.0 - 1.0
    float danceability;          // 0.0 - 1.0
    float acousticness;          // 0.0 - 1.0
    float instrumentalness;      // 0.0 - 1.0
    float liveness;              // 0.0 - 1.0
    float speechiness;           // 0.0 - 1.0
    float valence;               // 0.0 - 1.0
    float loudness;              // dB
    float dynamicRange;          // dB
    std::vector<float> mfcc;     // Mel-frequency cepstral coefficients
    std::vector<float> chroma;   // Chroma features
    std::vector<float> spectral; // Spectral features
};

struct MusicStructure {
    std::vector<Section> sections;
    std::vector<Beat> beats;
    std::vector<Bar> bars;
    std::vector<Segment> segments;
    std::vector<std::string> lyrics;
};

struct Section {
    float start;
    float duration;
    std::string label;           // "intro", "verse", "chorus", "bridge", "outro"
    float confidence;
};

struct MoodAnalysis {
    std::string primaryMood;
    std::vector<std::string> secondaryMoods;
    std::map<std::string, float> moodScores;
    float arousal;               // -1.0 to 1.0 (calm to energetic)
    float valence;               // -1.0 to 1.0 (sad to happy)
    float dominance;             // -1.0 to 1.0 (weak to strong)
};

struct GenreClassification {
    std::string primaryGenre;
    std::vector<std::pair<std::string, float>> genres;
    float confidence;
};

struct QualityMetrics {
    float dynamicRangeScore;
    float frequencyBalance;
    float stereoWidth;
    float clarityScore;
    float warmthScore;
    float brightnessScore;
    std::string qualityGrade;   // "A", "B", "C", "D", "F"
};

} // namespace CoreMusic::K8::AI
```

### AI Service Implementation

```cpp
// K8/ai-service/src/AIService.cpp
namespace CoreMusic::K8::AI {

class AIService : public IService {
public:
    AIService(std::shared_ptr<IAIEngine> engine,
              std::shared_ptr<IMediaRepository> mediaRepo,
              std::shared_ptr<IUserRepository> userRepo)
        : engine_(std::move(engine))
        , mediaRepo_(std::move(mediaRepo))
        , userRepo_(std::move(userRepo)) {}

    void initialize() override {
        // ML modellerini yükle
        loadModels();
        status_ = ServiceStatus::Running;
    }

    // Track Analysis
    ServiceResult<TrackAnalysis> analyzeTrack(const std::string& trackId) {
        // Cache kontrolü
        auto cached = cache_->get<TrackAnalysis>("analysis:" + trackId);
        if (cached) {
            return ServiceResult<TrackAnalysis>::success(*cached);
        }

        auto track = mediaRepo_->findById(trackId);
        if (!track) {
            return ServiceResult<TrackAnalysis>::error({
                404, "Track not found", "", ServiceErrorCode::NotFound
            });
        }

        // Audio features çıkarma
        auto audioData = loadAudioData(track->filePath);
        auto features = extractAudioFeatures(audioData);

        // Mood analizi
        auto mood = analyzeMood(features);

        // Genre classification
        auto genre = classifyGenre(features);

        // Music structure analysis
        auto structure = analyzeStructure(audioData);

        // Quality assessment
        auto quality = assessQuality(audioData);

        TrackAnalysis analysis;
        analysis.trackId = trackId;
        analysis.audioFeatures = features;
        analysis.mood = mood;
        analysis.genre = genre;
        analysis.structure = structure;
        analysis.quality = quality;
        analysis.confidence = calculateConfidence(analysis);

        // Cache'e kaydet
        cache_->set("analysis:" + trackId, analysis, 86400s);

        return ServiceResult<TrackAnalysis>::success(analysis);
    }

    // Recommendations
    ServiceResult<std::vector<TrackRecommendation>> getRecommendations(
        const std::string& userId, int count = 20) {
        auto user = userRepo_->findById(userId);
        if (!user) {
            return ServiceResult<std::vector<TrackRecommendation>>::error({
                404, "User not found", "", ServiceErrorCode::NotFound
            });
        }

        // Kullanıcı profilini oluştur
        auto profile = buildUserProfile(userId);

        // Hybrid recommendation: content-based + collaborative filtering
        auto contentBased = getContentBasedRecommendations(profile, count * 2);
        auto collaborative = getCollaborativeRecommendations(userId, count * 2);

        // Sonuçları birleştir ve sırala
        auto merged = mergeRecommendations(contentBased, collaborative);

        // Diversity ensured ranking
        auto diversified = ensureDiversity(merged, count);

        return ServiceResult<std::vector<TrackRecommendation>>::success(
            diversified);
    }

    // Voice Processing
    ServiceResult<VoiceCommandResult> processVoiceCommand(
        const AudioBuffer& audio, const std::string& userId) {
        // Speech-to-text
        auto transcription = speechToText(audio);
        if (!transcription) {
            return ServiceResult<VoiceCommandResult>::error({
                400, "Could not transcribe audio",
                "", ServiceErrorCode::Validation
            });
        }

        // Command parsing
        auto command = parseCommand(*transcription);
        if (!command) {
            return ServiceResult<VoiceCommandResult>::error({
                400, "Could not understand command",
                transcription->text, ServiceErrorCode::Validation
            });
        }

        // Command execution
        auto result = executeCommand(*command, userId);

        // Text-to-speech response
        auto responseAudio = textToSpeech(result.responseText);

        VoiceCommandResult voiceResult;
        voiceResult.transcription = transcription->text;
        voiceResult.command = command->type;
        voiceResult.parameters = command->parameters;
        voiceResult.success = result.success;
        voiceResult.responseText = result.responseText;
        voiceResult.responseAudio = responseAudio;

        return ServiceResult<VoiceCommandResult>::success(voiceResult);
    }

private:
    std::shared_ptr<IAIEngine> engine_;
    std::shared_ptr<IMediaRepository> mediaRepo_;
    std::shared_ptr<IUserRepository> userRepo_;
    std::shared_ptr<ICacheManager> cache_;

    void loadModels() {
        // Model yükleme
        engine_->loadModel("mood-analyzer", "models/mood_v2.onnx");
        engine_->loadModel("genre-classifier", "models/genre_v1.onnx");
        engine_->loadModel("feature-extractor", "models/features_v3.onnx");
        engine_->loadModel("recommendation", "models/rec_v1.onnx");
        engine_->loadModel("voice-command", "models/voice_v1.onnx");
    }

    AudioFeatures extractAudioFeatures(const AudioData& audio) {
        // Feature extraction using pre-trained model
        auto input = prepareFeatureInput(audio);
        auto output = engine_->infer("feature-extractor", input);

        AudioFeatures features;
        features.tempo = output["tempo"];
        features.key = output["key"];
        features.mode = output["mode"];
        features.energy = output["energy"];
        features.danceability = output["danceability"];
        features.acousticness = output["acousticness"];
        features.instrumentalness = output["instrumentalness"];
        features.liveness = output["liveness"];
        features.speechiness = output["speechiness"];
        features.valence = output["valence"];
        features.loudness = output["loudness"];

        // MFCC ve diğer features
        features.mfcc = output["mfcc"];
        features.chroma = output["chroma"];
        features.spectral = output["spectral"];

        return features;
    }

    MoodAnalysis analyzeMood(const AudioFeatures& features) {
        auto input = prepareMoodInput(features);
        auto output = engine_->infer("mood-analyzer", input);

        MoodAnalysis mood;
        mood.primaryMood = output["primary_mood"];
        mood.moodScores = output["mood_scores"];
        mood.arousal = output["arousal"];
        mood.valence = output["valence"];
        mood.dominance = output["dominance"];

        return mood;
    }

    GenreClassification classifyGenre(const AudioFeatures& features) {
        auto input = prepareGenreInput(features);
        auto output = engine_->infer("genre-classifier", input);

        GenreClassification genre;
        genre.primaryGenre = output["primary_genre"];
        genre.genres = output["genres"];
        genre.confidence = output["confidence"];

        return genre;
    }

    UserProfile buildUserProfile(const std::string& userId) {
        UserProfile profile;

        // Listening history
        auto history = userRepo_->getListeningHistory(userId, 1000);
        profile.totalListeningTime = calculateTotalListeningTime(history);

        // Genre preferences
        profile.genrePreferences = calculateGenrePreferences(history);

        // Mood patterns
        profile.moodPatterns = analyzeMoodPatterns(history);

        // Time-based patterns
        profile.timePatterns = analyzeTimePatterns(history);

        // Skip patterns
        profile.skipPatterns = analyzeSkipPatterns(history);

        return profile;
    }
};

} // namespace CoreMusic::K8::AI
```

### Recommendation Engine

```cpp
// K8/ai-service/src/RecommendationEngine.cpp
namespace CoreMusic::K8::AI {

class RecommendationEngine {
public:
    struct RecommendationRequest {
        std::string userId;
        int count;
        std::vector<std::string> seedTrackIds;
        std::vector<std::string> seedArtistIds;
        std::string mood;
        std::vector<std::string> excludeTrackIds;
        std::map<std::string, float> constraints;
    };

    std::vector<TrackRecommendation> recommend(
        const RecommendationRequest& request) {

        std::vector<TrackRecommendation> results;

        // 1. Content-based recommendations
        auto contentBased = getContentBased(request);
        results.insert(results.end(),
                      contentBased.begin(), contentBased.end());

        // 2. Collaborative filtering
        auto collaborative = getCollaborative(request);
        results.insert(results.end(),
                      collaborative.begin(), collaborative.end());

        // 3. Context-aware recommendations
        auto contextAware = getContextAware(request);
        results.insert(results.end(),
                      contextAware.begin(), contextAware.end());

        // 4. Popularity-based (fallback)
        if (results.size() < request.count) {
            auto popular = getPopular(request);
            results.insert(results.end(),
                          popular.begin(), popular.end());
        }

        // Deduplicate
        results = deduplicate(results);

        // Re-rank with MMR (Maximal Marginal Relevance)
        results = mmrRerank(results, request.count, 0.7);

        // Apply constraints
        results = applyConstraints(results, request.constraints);

        return std::vector<TrackRecommendation>(
            results.begin(),
            results.begin() + std::min(request.count,
                                       static_cast<int>(results.size())));
    }

private:
    std::vector<TrackRecommendation> getContentBased(
        const RecommendationRequest& request) {

        std::vector<TrackRecommendation> results;

        for (const auto& seedId : request.seedTrackIds) {
            auto analysis = analyzeTrack(seedId);
            auto similarTracks = findSimilarTracks(analysis, 50);

            for (const auto& track : similarTracks) {
                TrackRecommendation rec;
                rec.trackId = track.id;
                rec.score = track.similarityScore;
                rec.reason = "Similar to " + seedId;
                rec.source = "content-based";

                results.push_back(rec);
            }
        }

        return results;
    }

    std::vector<TrackRecommendation> getCollaborative(
        const RecommendationRequest& request) {

        std::vector<TrackRecommendation> results;

        // User-based collaborative filtering
        auto similarUsers = findSimilarUsers(request.userId, 100);

        for (const auto& user : similarUsers) {
            auto userTracks = getUserTopTracks(user.id, 20);

            for (const auto& track : userTracks) {
                TrackRecommendation rec;
                rec.trackId = track.id;
                rec.score = track.score * user.similarity;
                rec.reason = "Popular with similar users";
                rec.source = "collaborative";

                results.push_back(rec);
            }
        }

        // Item-based collaborative filtering
        for (const auto& seedId : request.seedTrackIds) {
            auto coOccurring = getCoOccurringTracks(seedId, 20);

            for (const auto& track : coOccurring) {
                TrackRecommendation rec;
                rec.trackId = track.id;
                rec.score = track.coOccurrenceScore;
                rec.reason = "Often played together";
                rec.source = "collaborative-item";

                results.push_back(rec);
            }
        }

        return results;
    }

    std::vector<TrackRecommendation> getContextAware(
        const RecommendationRequest& request) {

        std::vector<TrackRecommendation> results;

        // Time-of-day context
        auto timeOfDay = getCurrentTimeOfDay();
        auto timeTracks = getTimeBasedRecommendations(timeOfDay);

        for (const auto& track : timeTracks) {
            TrackRecommendation rec;
            rec.trackId = track.id;
            rec.score = track.timeRelevance;
            rec.reason = "Popular at this time";
            rec.source = "context-time";

            results.push_back(rec);
        }

        // Mood context
        if (!request.mood.empty()) {
            auto moodTracks = getMoodBasedRecommendations(request.mood);

            for (const auto& track : moodTracks) {
                TrackRecommendation rec;
                rec.trackId = track.id;
                rec.score = track.moodMatch;
                rec.reason = "Matches " + request.mood + " mood";
                rec.source = "context-mood";

                results.push_back(rec);
            }
        }

        return results;
    }

    std::vector<TrackRecommendation> mmrRerank(
        const std::vector<TrackRecommendation>& candidates,
        int count, float lambda) {

        std::vector<TrackRecommendation> selected;
        std::vector<bool> used(candidates.size(), false);

        for (int i = 0; i < count && i < candidates.size(); ++i) {
            float bestScore = -1.0f;
            int bestIdx = -1;

            for (int j = 0; j < candidates.size(); ++j) {
                if (used[j]) continue;

                float relevance = candidates[j].score;
                float maxSimilarity = 0.0f;

                for (int k = 0; k < selected.size(); ++k) {
                    float sim = calculateTrackSimilarity(
                        candidates[j].trackId, selected[k].trackId);
                    maxSimilarity = std::max(maxSimilarity, sim);
                }

                float mmrScore = lambda * relevance -
                               (1 - lambda) * maxSimilarity;

                if (mmrScore > bestScore) {
                    bestScore = mmrScore;
                    bestIdx = j;
                }
            }

            if (bestIdx >= 0) {
                selected.push_back(candidates[bestIdx]);
                used[bestIdx] = true;
            }
        }

        return selected;
    }
};

} // namespace CoreMusic::K8::AI
```

### Voice Command Processor

```cpp
// K8/ai-service/src/VoiceCommandProcessor.cpp
namespace CoreMusic::K8::AI {

class VoiceCommandProcessor {
public:
    struct VoiceCommand {
        CommandType type;
        std::map<std::string, std::string> parameters;
        float confidence;
    };

    enum class CommandType {
        Play,
        Pause,
        Stop,
        Next,
        Previous,
        Volume,
        Seek,
        AddToQueue,
        CreatePlaylist,
        GetInfo,
        Recommendation,
        Search,
        Shuffle,
        Repeat,
        Like,
        Dislike,
        Help
    };

    VoiceCommand parseCommand(const TranscriptionResult& transcription) {
        // NLU (Natural Language Understanding)
        auto intent = classifyIntent(transcription.text);
        auto entities = extractEntities(transcription.text);

        VoiceCommand command;
        command.type = intentToCommandType(intent);
        command.parameters = entities;
        command.confidence = intent.confidence;

        // Context-based refinement
        command = refineWithContext(command, transcription.context);

        return command;
    }

    CommandResult executeCommand(const VoiceCommand& command,
                                const std::string& userId) {
        switch (command.type) {
            case CommandType::Play:
                return executePlay(command, userId);
            case CommandType::Volume:
                return executeVolume(command);
            case CommandType::Search:
                return executeSearch(command, userId);
            case CommandType::Recommendation:
                return executeRecommendation(command, userId);
            case CommandType::GetInfo:
                return executeGetInfo(command, userId);
            default:
                return CommandResult{
                    false, "Command not supported", {}};
        }
    }

private:
    Intent classifyIntent(const std::string& text) {
        // Transformer-based intent classification
        auto tokens = tokenize(text);
        auto embeddings = encodeTokens(tokens);
        auto logits = intentModel_->forward(embeddings);
        auto probs = softmax(logits);

        int maxIdx = std::max_element(probs.begin(), probs.end()) -
                    probs.begin();

        Intent intent;
        intent.label = intentLabels_[maxIdx];
        intent.confidence = probs[maxIdx];

        return intent;
    }

    std::map<std::string, std::string> extractEntities(
        const std::string& text) {

        std::map<std::string, std::string> entities;

        // Regex-based entity extraction
        std::regex playRegex(R"(play\s+(.*))");
        std::regex volumeRegex(R"((?:set\s+)?volume\s+(?:to\s+)?(\d+))");
        std::regex seekRegex(R"(seek\s+(?:to\s+)?(\d+:\d+))");

        std::smatch match;

        if (std::regex_search(text, match, playRegex)) {
            entities["query"] = match[1].str();
        }

        if (std::regex_search(text, match, volumeRegex)) {
            entities["level"] = match[1].str();
        }

        if (std::regex_search(text, match, seekRegex)) {
            entities["position"] = match[1].str();
        }

        return entities;
    }

    CommandResult executePlay(const VoiceCommand& command,
                             const std::string& userId) {
        if (command.parameters.count("query")) {
            auto query = command.parameters.at("query");
            auto searchResults = searchService_->search(query, 5);

            if (!searchResults.empty()) {
                audioService_->play({searchResults[0].trackId});
                return CommandResult{
                    true,
                    "Playing " + searchResults[0].title,
                    {{"trackId", searchResults[0].trackId}}
                };
            }

            return CommandResult{
                false, "Could not find " + query, {}};
        }

        audioService_->play({});
        return CommandResult{true, "Playing", {}};
    }
};

} // namespace CoreMusic::K8::AI
```

## Bağımlılıklar

| Servis/Bileşen | Bağımlılık Tipi | Açıklama |
|----------------|-----------------|----------|
| K9-AI Engine | Core | ML inference engine |
| K7-Repository | Data Access | Kullanıcı ve müzik verileri |
| K5-Cache | Infrastructure | Model cache, analysis cache |
| K8-AudioService | Service | Çalma kontrolü |
| ONNX Runtime | External | ML model inference |

## Durum: Implementasyon

- **Track Analysis**: ✅ Tamamlandı
- **Recommendations**: ✅ Tamamlandı
- **Voice Commands**: 🔄 Devam ediyor
- **Real-time Mood Detection**: ⏳ Beklemede
- **Personalized Learning**: 🔄 Devam ediyor
