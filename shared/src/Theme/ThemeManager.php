<?php declare(strict_types=1);

namespace CoreMusic\Theme;

/**
 * CoreMusic -- Theme Manager (ADR-044)
 * Gender-based tema motoru: female/male/neutral
 *
 * Server-side tema tespiti, CSS token uretimi ve inline style injection.
 * JS ThemeManager.js ile birebir eslesme.
 *
 * Kullanim:
 *   $gender   = ThemeManager::detect($sessionData);
 *   $tokens   = ThemeManager::getTokens($gender);
 *   $styleTag = ThemeManager::injectInlineStyle($gender);
 *   $attr     = ThemeManager::injectDataAttribute($gender);
 */
final class ThemeManager
{
    /**
     * Tema token haritasi (JS ThemeManager.js THEMES ile eslesme)
     *
     * @var array<string, array<string, string>>
     */
    private const THEMES = [
        'female' => [
            '--accent'       => '#ff4fd8',
            '--accent-hover' => '#ff7ae3',
            '--accent-soft'  => 'rgba(255,79,216,0.15)',
            '--glass-bg'     => 'rgba(255,79,216,0.08)',
        ],
        'male' => [
            '--accent'       => '#4f8fff',
            '--accent-hover' => '#7ab0ff',
            '--accent-soft'  => 'rgba(79,143,255,0.15)',
            '--glass-bg'     => 'rgba(79,143,255,0.08)',
        ],
        'neutral' => [
            '--accent'       => '#a855f7',
            '--accent-hover' => '#c084fc',
            '--accent-soft'  => 'rgba(168,85,247,0.15)',
            '--glass-bg'     => 'rgba(168,85,247,0.08)',
        ],
    ];

    /** Gecerli gender degerleri */
    private const VALID_GENDERS = ['female', 'male', 'neutral'];

    /** Varsayilan gender */
    private const DEFAULT_GENDER = 'neutral';

    /**
     * Session verisinden gender tespit et
     *
     * Oncelik sirasi:
     *   1. cm_gender (session key)
     *   2. gender (session key)
     *   3. theme_gender (user_preferences)
     *   4. neutral (varsayilan)
     *
     * @param array<string, mixed> $session  Session data
     * @return string  female|male|neutral
     */
    public static function detect(array $session): string
    {
        $candidates = [
            $session['cm_gender'] ?? null,
            $session['gender'] ?? null,
            $session['theme_gender'] ?? null,
        ];

        foreach ($candidates as $candidate) {
            if ($candidate === null || !is_string($candidate)) {
                continue;
            }
            $sanitized = self::sanitize($candidate);
            if ($sanitized !== null) {
                return $sanitized;
            }
        }

        return self::DEFAULT_GENDER;
    }

    /**
     * CSS custom property token'larini dondur
     *
     * @param string $gender  female|male|neutral
     * @return array<string, string>  CSS property => value
     */
    public static function getTokens(string $gender): array
    {
        $key = self::sanitize($gender) ?? self::DEFAULT_GENDER;

        return self::THEMES[$key];
    }

    /**
     * Inline style tag uret (FOUC onleme icin)
     *
     * <style data-cm-theme>:root{--accent:#ff4fd8;...}</style>
     *
     * @param string $gender  female|male|neutral
     * @return string  HTML inline style tag
     */
    public static function injectInlineStyle(string $gender): string
    {
        $tokens = self::getTokens($gender);

        $props = [];
        foreach ($tokens as $property => $value) {
            $props[] = $property . ':' . $value;
        }

        return '<style data-cm-theme>:root{' . implode(';', $props) . '}</style>';
    }

    /**
     * HTML data-gender attribute uret
     *
     * Ornegin: data-gender="female"
     *
     * @param string $gender  female|male|neutral
     * @return string  HTML attribute string (bos stringegerlere)
     */
    public static function injectDataAttribute(string $gender): string
    {
        $safe = self::sanitize($gender) ?? self::DEFAULT_GENDER;

        return 'data-gender="' . $safe . '"';
    }

    /**
     * Gender degerini temizle ve gecerliligi kontrol et
     *
     * hash_equals timing-safe karsilastirma kullanir (ADR-010).
     *
     * @param string $input  Ham gender degeri
     * @return string|null  Gecerli gender veya null
     */
    private static function sanitize(string $input): ?string
    {
        $lower = strtolower(trim($input));

        if ($lower === '') {
            return null;
        }

        foreach (self::VALID_GENDERS as $valid) {
            if (hash_equals($valid, $lower)) {
                return $valid;
            }
        }

        return null;
    }

    /**
     * Tum gecerli gender degerlerini dondur
     *
     * @return array<int, string>
     */
    public static function validGenders(): array
    {
        return self::VALID_GENDERS;
    }

    /**
     * Belirli bir gender icin CSS path dondur (gelecek kullanim icin)
     *
     * @param string $gender  female|male|neutral
     * @return string  CSS dosya yolu
     */
    public static function getThemeCssPath(string $gender): string
    {
        $safe = self::sanitize($gender) ?? self::DEFAULT_GENDER;

        return '07_Themes/t-' . $safe . '.css';
    }
}
