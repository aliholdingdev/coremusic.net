<?php declare(strict_types=1);
/**
 * CoreMusic — AI Engine Interface
 * 
 * Ana AI işleme motoru arayüzü.
 * Recommendation, Audio Analysis, EQ Optimization, Hardware Analysis, Fault Prediction.
 *
 * @package CoreMusic\AI\Contracts
 * @version 1.0.0
 * @since   2026-09-01
 */

namespace CoreMusic\AI\Contracts;

/**
 * AI Engine ana arayüzü.
 * Tüm AI işleme modülleri bu arayüzü implemente eder.
 */
interface AIEngineInterface
{
    /**
     * Müzik önerisi üretir.
     *
     * @param array<string, mixed> $context Kullanıcı bağlamı (tercihler, geçmiş, mood)
     * @param int                  $limit   Maksimum öneri sayısı
     * @return array<int, array{id: string, title: string, artist: string, score: float, reason: string}>
     */
    public function getRecommendations(array $context, int $limit = 20): array;

    /**
     * Ses dosyasını analiz eder (BPM, key, energy, mood).
     *
     * @param string $filePath Ses dosyası yolu
     * @return array{bpm: float, key: string, energy: float, mood: string, danceability: float, acousticness: float}
     */
    public function analyzeAudio(string $filePath): array;

    /**
     * Otomatik EQ ayarları üretir.
     *
     * @param array<string, mixed> $roomProfile Ortam profili (frekans, reverb, boyut)
     * @return array<int, array{band: int, frequency: float, gain: float, q: float}>
     */
    public function optimizeEQ(array $roomProfile): array;

    /**
     * Donanım performansını analiz eder.
     *
     * @param string $deviceId Cihaz ID'si
     * @return array{status: string, snr: float, thd: float, temperature: float, recommendations: array<int, string>}
     */
    public function analyzeHardware(string $deviceId): array;

    /**
     * Hata tahmini yapar.
     *
     * @param array<string, mixed> $metrics Cihaz metrikleri
     * @return array{risk: string, probability: float, alerts: array<int, string>, maintenance: array<int, string>}
     */
    public function predictFault(array $metrics): array;
}
