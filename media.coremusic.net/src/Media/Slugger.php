<?php

declare(strict_types=1);

namespace Media;

/**
 * Kebab-case slug üretici — docs/adlandirma.md kural 1-3 (ADR-092 §6.1).
 *
 * Sıra: ASCII fold (12 eşleme) → lowercase → [a-z0-9-] filtresi → '-' tekilleştirme
 * → max 80 → boşsa 'isimsiz'. Çakışma `-2`, `-3` (case-insensitive, hedef klasörün
 * içinde). Windows-reserved adlara `-one` eklenir (uzantılıları dahil).
 */
final class Slugger
{
    /** Maks slug uzunluğu (ADR-092 §6.1 kural 1). */
    public const MAKS = 80;

    /** Fold sonrası boş çıkan slug için yedek değer — Karar: isimsiz (2026-09-29, ADR-092 §6.1 uyumlu — adlandirma.md §8.7'ye yazıldı). */
    public const YEDEK = 'isimsiz';

    /** docs/adlandirma.md §1 fold tablosu (12 eşleme). */
    private const FOLD = [
        'ş' => 's', 'Ş' => 'S',
        'ı' => 'i', 'İ' => 'i',
        'ğ' => 'g', 'Ğ' => 'G',
        'ü' => 'u', 'Ü' => 'U',
        'ö' => 'o', 'Ö' => 'O',
        'ç' => 'c', 'Ç' => 'C',
    ];

    /** Windows-reserved adlar (uzantılıları dahil — docs/adlandirma.md §3). */
    private const YASAKLI = [
        'con', 'aux', 'prn', 'nul',
        'com1', 'com2', 'com3', 'com4', 'com5', 'com6', 'com7', 'com8', 'com9',
        'lpt1', 'lpt2', 'lpt3', 'lpt4', 'lpt5', 'lpt6', 'lpt7', 'lpt8', 'lpt9',
    ];

    /** Sıra öneki için min/maks (docs/adlandirma.md §2: 01 … 99). */
    private const SIRA_MIN = 1;

    private const SIRA_MAX = 99;

    /**
     * Türkçe/ASCII fold tablosu (docs/adlandirma.md §1 — 12 eşleme).
     */
    public static function fold(string $metin): string
    {
        return strtr($metin, self::FOLD);
    }

    /**
     * Slug üretir.
     *
     * @param array<int, string> $dolu Aynı klasörde zaten kullanılan sluglar
     *                                 (case-insensitive çakışma sayımı için).
     */
    public static function slug(string $girdi, array $dolu = []): string
    {
        $temel = self::fold($girdi);
        $temel = mb_strtolower($temel, 'UTF-8');
        // /u yok: bayt düzeyinde [a-z0-9] dışı her şey güvenle '-' olur (UTF-8 parçalanmaz).
        $filtre = preg_replace('/[^a-z0-9]+/', '-', $temel);
        if (is_string($filtre)) {
            $temel = $filtre;
        }
        $temel = trim($temel, '-');
        if ($temel === '') {
            $temel = self::YEDEK;
        }
        if (self::yasakliMi($temel)) {
            $temel .= '-one';
        }
        $temel = self::kirp($temel);
        if ($dolu === []) {
            return $temel;
        }

        $kucukler = [];
        foreach ($dolu as $eski) {
            $kucukler[] = mb_strtolower((string) $eski, 'UTF-8');
        }
        $aday = $temel;
        $sayi = 2;
        while (in_array($aday, $kucukler, true)) {
            $ek = '-' . $sayi;
            $govde = rtrim(mb_substr($temel, 0, self::MAKS - mb_strlen($ek, 'UTF-8'), 'UTF-8'), '-');
            if ($govde === '') {
                $govde = self::YEDEK;
            }
            $aday = $govde . $ek;
            $sayi++;
        }

        return $aday;
    }

    /**
     * Windows-reserved ad denetimi (adlandirma.md §3): `con`, `aux`, `prn`, `nul`,
     * `com1-9`, `lpt1-9` ve uzantılıları (`con.mp3` → gövde `con`).
     */
    public static function yasakliMi(string $ad): bool
    {
        $ad = mb_strtolower(trim($ad), 'UTF-8');
        if ($ad === '') {
            return false;
        }
        $govde = $ad;
        $nokta = strrpos($ad, '.');
        if ($nokta !== false && $nokta > 0) {
            $govde = substr($ad, 0, $nokta);
        }

        return in_array($ad, self::YASAKLI, true) || in_array($govde, self::YASAKLI, true);
    }

    /**
     * İki haneli sıra öneki: 1 → `01-`, 7 → `07-` (adlandirma.md §2).
     */
    public static function siraPrefix(int $sira): string
    {
        if ($sira < self::SIRA_MIN) {
            $sira = self::SIRA_MIN;
        }
        if ($sira > self::SIRA_MAX) {
            $sira = self::SIRA_MAX;
        }

        return sprintf('%02d-', $sira);
    }

    /** 80 karakter kırpma + sondaki '-' temizliği (boşsa yedek değer). */
    private static function kirp(string $deger): string
    {
        $deger = mb_substr($deger, 0, self::MAKS, 'UTF-8');
        $deger = rtrim($deger, '-');

        return $deger === '' ? self::YEDEK : $deger;
    }
}
