<?php declare(strict_types=1);

namespace CoreMusic\Theme;

/**
 * CoreMusic -- Theme Manager (ADR-044 + Dark/Light Mode)
 * Gender-based tema motoru: female/male/neutral
 * Color mode motoru: dark/light/ışıl-peri
 *
 * Server-side tema tespiti, CSS token uretimi ve inline style injection.
 * JS ThemeManager.js ile birebir eslesme.
 *
 * Kullanim:
 *   $gender   = ThemeManager::detect($sessionData);
 *   $mode     = ThemeManager::detectMode($sessionData);
 *   $tokens   = ThemeManager::getTokens($gender);
 *   $styleTag = ThemeManager::injectInlineStyle($gender);
 *   $attr     = ThemeManager::injectDataAttribute($gender, $mode);
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

    /** Gecerli color mode degerleri */
    private const VALID_MODES = ['dark', 'light', 'ışıl-peri'];

    /** Varsayilan color mode (null = OS preferansini kullan) */
    private const DEFAULT_MODE = null;

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
     * Session verisinden color mode tespit et
     *
     * Oncelik sirasi:
     *   1. cm_color_mode (session key)
     *   2. color_mode (session key)
     *   3. cm_color_mode (cookie)
     *   4. null (varsayilan — CSS prefers-color-scheme kullanir)
     *
     * @param array<string, mixed> $session  Session data
     * @return string|null  dark|light|ışıl-peri|null
     */
    public static function detectMode(array $session): ?string
    {
        $candidates = [
            $session['cm_color_mode'] ?? null,
            $session['color_mode'] ?? null,
        ];

        foreach ($candidates as $candidate) {
            if ($candidate === null || !is_string($candidate)) {
                continue;
            }
            $sanitized = self::sanitizeMode($candidate);
            if ($sanitized !== null) {
                return $sanitized;
            }
        }

        // Cookie'den oku
        if (isset($_COOKIE['cm_color_mode'])) {
            $sanitized = self::sanitizeMode($_COOKIE['cm_color_mode']);
            if ($sanitized !== null) {
                return $sanitized;
            }
        }

        return self::DEFAULT_MODE;
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
     * NOT (karar 2026-10-04): color-mode token'lari (html[data-mode="ışıl-peri"])
     * BILINCLI OLARAK inline basilmaz:
     *   - HtmlShellRenderer ilk baytta data-mode attribute'u basar;
     *   - link rel=stylesheet render-blocking -> first paint'te mod token'i ve
     *     tuketen kurallar ayni dosyada atomik hazir (flash yok);
     *   - mod bloku token'lari stylesheet'te tanimli primitive'lere var() referansi
     *     -> inline kopya CSS gelene kadar calismaz;
     *   - CSS = SSOT (a-color-mode-tokens.css); deger kopyasi drift riski.
     * NOT 2: bu metod su an cagri noktasi icermiyor (ADR-044: "tanimli ama
     * cagrilmıyor" — PLANNED).
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
     * @return string  HTML attribute string
     */
    public static function injectDataAttribute(string $gender): string
    {
        $safe = self::sanitize($gender) ?? self::DEFAULT_GENDER;

        return 'data-gender="' . $safe . '"';
    }

    /**
     * HTML data-mode attribute uret
     *
     * Ornegin: data-mode="dark" veya bos string (null ise)
     *
     * @param string|null $mode  dark|light|ışıl-peri|null
     * @return string  HTML attribute string (bos stringegerlere)
     */
    public static function injectModeAttribute(?string $mode): string
    {
        if ($mode === null) {
            return '';
        }

        $safe = self::sanitizeMode($mode);

        if ($safe === null) {
            return '';
        }

        return 'data-mode="' . $safe . '"';
    }

    /**
     * Gender ve mode birlesik attribute uret
     *
     * Ornegin: data-gender="female" data-mode="dark"
     *
     * @param string      $gender  female|male|neutral
     * @param string|null $mode    dark|light|ışıl-peri|null
     * @return string  HTML attribute string
     */
    public static function injectAttributes(string $gender, ?string $mode = null): string
    {
        $attrs = [self::injectDataAttribute($gender)];

        $modeAttr = self::injectModeAttribute($mode);
        if ($modeAttr !== '') {
            $attrs[] = $modeAttr;
        }

        return implode(' ', $attrs);
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
     * Color mode degerini temizle ve gecerliligi kontrol et
     *
     * hash_equals timing-safe karsilastirma kullanir (ADR-010).
     *
     * @param string $input  Ham color mode degeri
     * @return string|null  Gecerli mode veya null
     */
    private static function sanitizeMode(string $input): ?string
    {
        $lower = strtolower(trim($input));

        if ($lower === '') {
            return null;
        }

        foreach (self::VALID_MODES as $valid) {
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
     * Tum gecerli color mode degerlerini dondur
     *
     * @return array<int, string>
     */
    public static function validModes(): array
    {
        return self::VALID_MODES;
    }

    /**
     * Belirli bir gender icin CSS path dondur (gelecek kullanim icin)
     *
     * ⚠️ VERIFICATION REQUIRED: dondurulen '07_Themes/t-*.css' DISKTE YOK —
     * assets.coremusic.net/Css/ altinda 07_ klasoru yalnizca 07_Vendors
     * (2026-10-04 glob: 07_ alt klasorleri = 33 Vendors dosyasi, Themes 0 isabet;
     * t-*.css = 0 isabet). Klasor/dosya uretilmedi (gorev yasagi); gercek tema
     * CSS'i 01_Abstracts + 08_Devices uzerinden gelir. Bu yol su an hicbir
     * cagri yerinde kullanilmiyor.
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
