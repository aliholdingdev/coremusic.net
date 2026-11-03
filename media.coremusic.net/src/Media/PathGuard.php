<?php

declare(strict_types=1);

namespace Media;

/**
 * PathGuard — göreli yol çözümü + kök kaçışı reddi (fail-closed).
 *
 * Desen kaynağı (OKU, kopya desen — taşıma değil): `bin/ingest.php:95-128`
 * (`yoluNormallestir` + `kokIcindeMi`). HTTP yüzeyi için ek katmanlar:
 *   - `..` segmenti HER KOŞULDA reddedilir (realpath false dönen dallarda da)
 *   - mutlak yol / Windows sürücü harfi reddi (kök dışı kalır)
 *   - NUL bayt reddi
 *   - symlink aday (`is_link`) reddi — realpath symlink'i zaten çözüyor,
 *     bu yüzden çözüm ÖNCESİ ham aday üzerinden kontrol edilir
 *   - realpath + kök-önek karşılaştırması (ayırıcı `/` ile — `"/kok-evil"`
 *     asla `"/kok"` içinde sayılmaz; ingest.php:106-112 ile aynı yanılıga kapalı)
 *   - dosya-varlık kontrolü (dizin/döngü çözülebilir ama dosya değildir)
 *
 * GÜVENLİK SÖZLEŞMESİ: `null` dönen her yol ASLA 200 üretemez.
 * HTTP yüzeyi bunu 400/403'e, CLI yüzeyi (bin/audit.php) HATA raporuna düşürür.
 */
final class PathGuard
{
    private function __construct()
    {
    }

    /**
     * Göreli yol sözdizimi + kök-dışılık denetimi — dosya sistemine DOKUNMAZ.
     *
     * `bin/audit.php` gibi, çözüm yerine yalnız denetim isteyen çağrılar içindir
     * (dosyanın diskte olup olmadığı ayrı bir sorudur; burada sorulmaz).
     */
    public static function isSafeRelative(string $relative): bool
    {
        $trimmed = trim($relative);
        if ($trimmed === '' || str_contains($relative, "\0")) {
            return false;
        }

        // Mutlak yol: POSIX ayracı, Windows ayırıcı veya sürücü harfi kök dışındadır.
        if (
            str_starts_with($trimmed, '/')
            || str_starts_with($trimmed, '\\')
            || preg_match('#^[A-Za-z]:#', $trimmed) === 1
        ) {
            return false;
        }

        $parcalar = preg_split('#[/\\\\]+#', $trimmed) ?: [];
        if (in_array('..', $parcalar, true)) {
            return false;
        }

        return $parcalar !== [];
    }

    /**
     * Göreli yolu kök içinde mutlak dosya yoluna çözer; çözülemezse `null`.
     *
     * `null` dönen her durum fail-closed'dur: kaçış, symlink, dosya yok,
     * kök yok/kurulamamış, dizin (dosya değil).
     *
     * @return string|null Gerçek dosya yolu — yalnız kök İÇİNDEYSE
     */
    public static function resolve(?string $root, string $relative): ?string
    {
        if (!self::isSafeRelative($relative)) {
            return null;
        }
        if ($root === null || trim($root) === '') {
            return null;
        }

        $realRoot = realpath($root);
        if ($realRoot === false) {
            return null;
        }
        $kokNorm = self::normalize($realRoot);

        $aday = $kokNorm . '/' . ltrim(str_replace('\\', '/', $relative), '/');

        // Symlink: realpath hedefi çözer → kaçış kontrolü ÖNCESİ ham aday reddedilir.
        if (is_link($aday)) {
            return null;
        }

        // Dizin üzerinden ön kontrol (dosya henüz var olmayabilir; ayrı denetlenir).
        $dizin = dirname($aday);
        $gercekDizin = realpath($dizin);
        $dizinNorm = self::normalize($gercekDizin === false ? $dizin : $gercekDizin);
        if (!($dizinNorm === $kokNorm || str_starts_with($dizinNorm, $kokNorm . '/'))) {
            return null;
        }

        $gercekDosya = realpath($aday);
        if ($gercekDosya === false) {
            return null;
        }
        $dosyaNorm = self::normalize($gercekDosya);
        if (!($dosyaNorm === $kokNorm || str_starts_with($dosyaNorm, $kokNorm . '/'))) {
            return null;
        }
        if (is_dir($gercekDosya) || !is_file($gercekDosya)) {
            return null;
        }

        return $gercekDosya;
    }

    /**
     * Yol normalizasyonu — `bin/ingest.php:95-100` ile birebir:
     * ayırıcı tekilleştirme + Windows'ta küçük harf (realpath `\`, aday `/` gelir).
     */
    private static function normalize(string $yol): string
    {
        $n = rtrim(str_replace('\\', '/', $yol), '/');

        return DIRECTORY_SEPARATOR === '\\' ? strtolower($n) : $n;
    }
}