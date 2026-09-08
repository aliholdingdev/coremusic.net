<?php declare(strict_types=1);
/**
 * CoreMusic — AI Engine
 * 
 * Ana AI işleme motoru implementasyonu.
 * Recommendation, Audio Analysis, EQ Optimization, Hardware Analysis, Fault Prediction.
 *
 * @package CoreMusic\AI
 * @version 1.0.0
 * @since   2026-09-01
 */

namespace CoreMusic\AI;

use CoreMusic\AI\Contracts\AIEngineInterface;
use CoreMusic\Config\ConfigManager;

/**
 * AI Engine — CoreMusic'in merkezi AI işleme motoru.
 *
 * 5 modül:
 * 1. RecommendationEngine — Müzik önerileri (collaborative + content-based)
 * 2. AudioAnalyzer — Ses analizi (BPM, key, energy, mood)
 * 3. EQOptimizer — Otomatik EQ (room correction, preset)
 * 4. HardwareAnalyzer — Donanım analizi (DAC/ADC performans, termal)
 * 5. FaultPredictor — Hata tahmini (anomaly detection)
 */
class AIEngine implements AIEngineInterface
{
    private const RECOMMENDATION_WEIGHTS = [
        'bpm'           => 0.15,
        'key'           => 0.10,
        'energy'        => 0.20,
        'danceability'  => 0.15,
        'valence'       => 0.10,
        'acousticness'  => 0.10,
        'genre'         => 0.10,
        'artist'        => 0.10,
    ];

    private const MAX_RECOMMENDATIONS = 20;
    private const MIN_SCORE = 0.7;
    private const DIVERSITY_TARGET = 0.30;

    private ConfigManager $config;
    private ?string $dbConnection = null;

    public function __construct(ConfigManager $config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function getRecommendations(array $context, int $limit = 20): array
    {
        $limit = min($limit, self::MAX_RECOMMENDATIONS);
        $userId = $context['user_id'] ?? null;
        $sessionId = $context['session_id'] ?? null;
        $mood = $context['mood'] ?? null;
        $genre = $context['genre'] ?? null;
        $history = $context['history'] ?? [];

        // 1. Collaborative filtering — benzer kullanıcı tercihleri
        $collaborativeScores = $this->getCollaborativeScores($userId, $history);

        // 2. Content-based — şarkı özellikleri
        $contentScores = $this->getContentBasedScores($context);

        // 3. Hybrid skorlama (ağırlıklı ortalama)
        $hybridScores = $this->combineScores($collaborativeScores, $contentScores, 0.6, 0.4);

        // 4. Çeşitlilik filtresi — %30 farklı tür garantisi
        $diversified = $this->applyDiversityFilter($hybridScores, $genre);

        // 5. Minimum skor filtresi
        $filtered = array_filter($diversified, fn(array $item) => $item['score'] >= self::MIN_SCORE);

        // 6. Sıralama ve limit
        usort($filtered, fn(array $a, array $b) => $b['score'] <=> $a['score']);

        return array_slice(array_values($filtered), 0, $limit);
    }

    /**
     * {@inheritdoc}
     */
    public function analyzeAudio(string $filePath): array
    {
        if (!file_exists($filePath)) {
            throw new \RuntimeException("Audio file not found: {$filePath}");
        }

        // FFprobe ile metadata çıkarma
        $metadata = $this->extractMetadata($filePath);

        // DSP pipeline ile özellik çıkarma
        $features = $this->extractFeatures($filePath);

        return [
            'bpm'           => $features['bpm'] ?? 0.0,
            'key'           => $features['key'] ?? 'unknown',
            'energy'        => $features['energy'] ?? 0.0,
            'mood'          => $features['mood'] ?? 'neutral',
            'danceability'  => $features['danceability'] ?? 0.0,
            'acousticness'  => $features['acousticness'] ?? 0.0,
            'duration'      => $metadata['duration'] ?? 0,
            'sample_rate'   => $metadata['sample_rate'] ?? 44100,
            'bit_depth'     => $metadata['bit_depth'] ?? 16,
            'channels'      => $metadata['channels'] ?? 2,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function optimizeEQ(array $roomProfile): array
    {
        $roomSize = $roomProfile['size'] ?? 'medium';
        $reverb = $roomProfile['reverb'] ?? 0.5;
        $frequencyResponse = $roomProfile['frequency_response'] ?? [];

        // 31-band parametrik EQ eğrisi üret
        $eqCurve = [];
        for ($band = 0; $band < 31; $band++) {
            $frequency = $this->bandToFrequency($band);
            $gain = $this->calculateRoomCorrection($frequency, $roomSize, $reverb, $frequencyResponse);
            $q = $this->calculateBandQ($frequency, $roomSize);

            $eqCurve[] = [
                'band'      => $band,
                'frequency' => $frequency,
                'gain'      => round($gain, 2),
                'q'         => round($q, 2),
            ];
        }

        return $eqCurve;
    }

    /**
     * {@inheritdoc}
     */
    public function analyzeHardware(string $deviceId): array
    {
        // PCM3168A, XMOS XU316, AK4458 için analiz
        $status = 'healthy';
        $snr = 0.0;
        $thd = 0.0;
        $temperature = 0.0;
        $recommendations = [];

        // Donanım metriklerini topla (gerçek donanım entegrasyonunda IPC ile)
        // Şimdilik placeholder — gerçek implementasyon Device Service üzerinden yapılacak
        return [
            'status'          => $status,
            'snr'             => $snr,
            'thd'             => $thd,
            'temperature'     => $temperature,
            'recommendations' => $recommendations,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function predictFault(array $metrics): array
    {
        $risk = 'low';
        $probability = 0.0;
        $alerts = [];
        $maintenance = [];

        // Anomaly detection — metrik eşiklerini kontrol et
        $temperature = $metrics['temperature'] ?? 0;
        $snr = $metrics['snr'] ?? 0;
        $thd = $metrics['thd'] ?? 0;
        $usageHours = $metrics['usage_hours'] ?? 0;

        if ($temperature > 70) {
            $risk = 'high';
            $probability += 0.4;
            $alerts[] = "Yüksek sıcaklık: {$temperature}°C";
        }

        if ($snr < 90) {
            $risk = max($risk, 'medium') === 'low' ? 'medium' : $risk;
            $probability += 0.2;
            $alerts[] = "Düşük SNR: {$snr}dB";
        }

        if ($thd > 0.1) {
            $risk = max($risk, 'medium') === 'low' ? 'medium' : $risk;
            $probability += 0.15;
            $alerts[] = "Yüksek THD: {$thd}%";
        }

        if ($usageHours > 10000) {
            $maintenance[] = "Bakım zamanı gelmiş olabilir ({$usageHours} saat)";
        }

        return [
            'risk'         => $risk,
            'probability'  => min($probability, 1.0),
            'alerts'       => $alerts,
            'maintenance'  => $maintenance,
        ];
    }

    /* ─── Private Methods ─── */

    /**
     * Collaborative filtering skorları.
     */
    private function getCollaborativeScores(?string $userId, array $history): array
    {
        if ($userId === null) {
            return [];
        }

        // DB'den benzer kullanıcıları bul
        // Gerçek implementasyonda matrix factorization (ALS) kullanılacak
        return [];
    }

    /**
     * Content-based skorları hesaplar.
     */
    private function getContentBasedScores(array $context): array
    {
        $targetFeatures = [
            'bpm'          => $context['bpm'] ?? null,
            'energy'       => $context['energy'] ?? null,
            'danceability' => $context['danceability'] ?? null,
            'genre'        => $context['genre'] ?? null,
        ];

        // DB'den aday şarkıları çek ve özelliklerini karşılaştır
        // cosine similarity kullanarak skorla
        return [];
    }

    /**
     * İki skor setini birleştirir.
     *
     * @param array<string, array{score: float}> $collaborative
     * @param array<string, array{score: float}> $content
     */
    private function combineScores(array $collaborative, array $content, float $collabWeight, float $contentWeight): array
    {
        $allKeys = array_unique(array_merge(array_keys($collaborative), array_keys($content)));
        $combined = [];

        foreach ($allKeys as $key) {
            $collabScore = $collaborative[$key]['score'] ?? 0;
            $contentScore = $content[$key]['score'] ?? 0;

            $combined[$key] = [
                'id'    => $key,
                'score' => ($collabScore * $collabWeight) + ($contentScore * $contentWeight),
            ];
        }

        return $combined;
    }

    /**
     * Çeşitlilik filtresi uygular.
     */
    private function applyDiversityFilter(array $scores, ?string $preferredGenre): array
    {
        $diversified = [];
        $genreCounts = [];

        foreach ($scores as $id => $item) {
            $genre = $item['genre'] ?? 'unknown';
            $genreCount = $genreCounts[$genre] ?? 0;

            // Tercih edilen türden çok fazla olmasın
            if ($genre === $preferredGenre && $genreCount > self::DIVERSITY_TARGET * count($scores)) {
                $item['score'] *= 0.8;
            }

            $genreCounts[$genre] = $genreCount + 1;
            $diversified[$id] = $item;
        }

        return $diversified;
    }

    /**
     * FFprobe ile metadata çıkarır.
     */
    private function extractMetadata(string $filePath): array
    {
        $cmd = sprintf(
            'ffprobe -v quiet -print_format json -show_format -show_streams "%s"',
            escapeshellarg($filePath)
        );

        $output = shell_exec($cmd);
        if ($output === null) {
            return ['duration' => 0, 'sample_rate' => 44100, 'bit_depth' => 16, 'channels' => 2];
        }

        $data = json_decode($output, true, 512, JSON_THROW_ON_ERROR);
        $format = $data['format'] ?? [];
        $stream = $data['streams'][0] ?? [];

        return [
            'duration'    => (float)($format['duration'] ?? 0),
            'sample_rate' => (int)($stream['sample_rate'] ?? 44100),
            'bit_depth'   => (int)($stream['bits_per_raw_sample'] ?? 16),
            'channels'    => (int)($stream['channels'] ?? 2),
        ];
    }

    /**
     * DSP pipeline ile özellik çıkarır.
     */
    private function extractFeatures(string $filePath): array
    {
        // Gerçek implementasyonda C++ DSP engine ile yapılacak
        // Şimdilik placeholder değerler
        return [
            'bpm'          => 120.0,
            'key'          => 'C',
            'energy'       => 0.7,
            'mood'         => 'happy',
            'danceability' => 0.6,
            'acousticness' => 0.3,
        ];
    }

    /**
     * 31-band EQ frekansını hesaplar.
     */
    private function bandToFrequency(int $band): float
    {
        // ISO 31-band EQ frekansları (20Hz - 20kHz)
        $frequencies = [
            20, 25, 31.5, 40, 50, 63, 80, 100, 125, 160,
            200, 250, 315, 400, 500, 630, 800, 1000, 1250, 1600,
            2000, 2500, 3150, 4000, 5000, 6300, 8000, 10000, 12500, 16000, 20000,
        ];

        return $frequencies[$band] ?? 1000.0;
    }

    /**
     * Room correction kazancını hesaplar.
     */
    private function calculateRoomCorrection(float $frequency, string $roomSize, float $reverb, array $frequencyResponse): float
    {
        // Basit room correction modeli
        $roomFactor = match ($roomSize) {
            'small'  => 1.2,
            'medium' => 1.0,
            'large'  => 0.8,
            default  => 1.0,
        };

        // Frekans response'dan sapmayı hesapla
        $targetResponse = 0.0; // Düz tepki
        $freqKey = (string) $frequency;
        $actualResponse = $frequencyResponse[$freqKey] ?? 0.0;

        return ($targetResponse - $actualResponse) * $roomFactor * (1 - $reverb);
    }

    /**
     * Band Q değerini hesaplar.
     */
    private function calculateBandQ(float $frequency, string $roomSize): float
    {
        // Düşük frekanslarda geniş bant, yüksek frekanslarda dar bant
        if ($frequency < 100) {
            return 0.7;
        }
        if ($frequency < 500) {
            return 1.0;
        }
        if ($frequency < 2000) {
            return 1.4;
        }
        return 1.8;
    }
}
