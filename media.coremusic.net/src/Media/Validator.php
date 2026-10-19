<?php

declare(strict_types=1);

namespace Media;

/**
 * media.schema.json için **sınırlı** doğrulayıcı (bağımlılık YOK, paket kullanılmaz).
 *
 * Kapsam — spec'in 6 kuralı:
 *  1) `required` · 2) `pattern` (id/slug/sha256/tarih) · 3) `enum` + taxonomy senkronu
 *  4) `type` · 5) `minimum`/`maximum`/`exclusiveMinimum` (+ `minLength`/`maxLength`)
 *  6) `additionalProperties:false` ihlali (`ek` hariç — o tek serbest katman)
 *
 * Ayrıca şemada fiilen kullanılan `oneOf`/`allOf`/`const`/`items`/`if-then`/`not`
 * küçük ölçekli işlenir. Tam JSON-Schema motoru YAZILMAZ (spec).
 *
 * Çıktı: hata listesi `["yol => neden"]` — exception yerine liste (kalite kuralı).
 */
final class Validator
{
    private const REF_ONEK = '#/$defs/';

    /** @var array<string, mixed> */
    private array $sema = [];

    private ?string $yuklemeHatasi = null;

    private Taxonomy $taxonomy;

    /** validate() çağrısı başına sıfırlanan enum-drift raporu (tekrarı önler). */
    private array $drift = [];

    public function __construct(string $semaDosyasi, ?Taxonomy $taxonomy = null)
    {
        $this->taxonomy = $taxonomy ?? new Taxonomy('');
        $ham = @file_get_contents($semaDosyasi);
        if ($ham === false) {
            $this->yuklemeHatasi = "sema okunamadi: {$semaDosyasi}";

            return;
        }
        $cozulmus = json_decode($ham, true);
        if (!is_array($cozulmus)) {
            $this->yuklemeHatasi = 'sema JSON gecersiz: ' . json_last_error_msg();

            return;
        }
        $this->sema = $cozulmus;
    }

    /** Proje kökünden (config/media.schema.json + config/taxonomy.json) yükler. */
    public static function fromProject(?string $kok = null): self
    {
        $kok ??= dirname(__DIR__, 2);

        return new self($kok . '/config/media.schema.json', Taxonomy::fromProject($kok));
    }

    /**
     * Dosya adı → $defs anahtarı (media.schema.json $comment eşleme tablosu).
     * `meta.json` → meta (tip discriminator ile ses/video varyantları).
     */
    public static function tipAnahtari(string $dosyaTipi): ?string
    {
        $ad = mb_strtolower(str_replace('\\', '/', $dosyaTipi), 'UTF-8');
        $ad = basename($ad);

        return match ($ad) {
            'artist.json', 'artist' => 'artist',
            'album.json', 'album' => 'album',
            'meta.json', 'meta' => 'meta',
            'koleksiyon.json', 'koleksiyon' => 'koleksiyon',
            default => null,
        };
    }

    public function yuklemeHatasi(): ?string
    {
        return $this->yuklemeHatasi;
    }

    /**
     * @param array<string, mixed> $veri JSON'dan çözülmüş nesne
     *
     * @return list<string> "yol => neden" hata listesi (boş = geçerli)
     */
    public function validate(string $dosyaTipi, array $veri): array
    {
        $this->drift = [];
        if ($this->yuklemeHatasi !== null) {
            return [$this->yuklemeHatasi];
        }
        $anahtar = self::tipAnahtari($dosyaTipi);
        if ($anahtar === null) {
            return [$dosyaTipi . ' => bilinmeyen dosya tipi'];
        }

        $hatalar = [];
        $this->dogrula(['$ref' => self::REF_ONEK . $anahtar], $veri, '$', $hatalar);

        return $hatalar;
    }

    /* ------------------------------------------------------------------ *
     * Çekirdek yürüteç
     * ------------------------------------------------------------------ */

    private function dogrula(mixed $sema, mixed $deger, string $yol, array &$hatalar): void
    {
        if ($sema === true) {
            return;
        }
        if ($sema === false) {
            $hatalar[] = $yol . ' => yasakli alan (semada false)';

            return;
        }
        if (!is_array($sema)) {
            return;
        }

        // 1) $ref çözümü (enum drift denetimi eşlik eder)
        if (isset($sema['$ref']) && is_string($sema['$ref'])) {
            $ref = $sema['$ref'];
            $hedef = $this->coz($ref);
            if ($hedef === null) {
                $hatalar[] = $yol . ' => cozulemeyen $ref: ' . $ref;

                return;
            }
            $this->enumDrift($ref, $yol, $hatalar);
            $this->dogrula($hedef, $deger, $yol, $hatalar);

            return;
        }

        // 2) kompozit şemalar
        if (isset($sema['allOf']) && is_array($sema['allOf'])) {
            foreach ($sema['allOf'] as $alt) {
                $this->dogrula($alt, $deger, $yol, $hatalar);
            }
        }
        if (isset($sema['oneOf']) && is_array($sema['oneOf'])) {
            $this->varyant($sema['oneOf'], $deger, $yol, $hatalar, false);
        }
        if (isset($sema['anyOf']) && is_array($sema['anyOf'])) {
            $this->varyant($sema['anyOf'], $deger, $yol, $hatalar, true);
        }
        if (isset($sema['not'])) {
            $gecici = [];
            $this->dogrula($sema['not'], $deger, $yol, $gecici);
            if ($gecici === []) {
                $hatalar[] = $yol . ' => yasakli yapı (not ihlali)';
            }
        }
        // meta_base if/then: ust.album_id null ise ust.sanatci+album+yil zorunlu
        if (isset($sema['if'])) {
            $gecici = [];
            $this->dogrula($sema['if'], $deger, $yol, $gecici);
            if ($gecici === [] && isset($sema['then'])) {
                $this->dogrula($sema['then'], $deger, $yol, $hatalar);
            }
        }

        // 3) type
        if (isset($sema['type'])) {
            $tipler = array_values((array) $sema['type']);
            $uygun = false;
            foreach ($tipler as $tip) {
                if ($this->tipUygun($deger, (string) $tip)) {
                    $uygun = true;
                    break;
                }
            }
            if (!$uygun) {
                $hatalar[] = $yol . ' => tip uyumsuz: [' . implode('|', $tipler) . '] beklenen, '
                    . $this->tipAdi($deger) . ' geldi';

                return;
            }
        }

        // 4) const / enum
        if (array_key_exists('const', $sema) && $deger !== $sema['const']) {
            $hatalar[] = $yol . ' => sabit deger ihlali (const)';
        }
        if (isset($sema['enum']) && is_array($sema['enum']) && !in_array($deger, $sema['enum'], true)) {
            $hatalar[] = $yol . ' => enum disi deger: ' . $this->kisa($deger);
        }

        // 5) pattern
        if (isset($sema['pattern']) && is_string($deger)) {
            $desen = (string) $sema['pattern'];
            $sonuc = @preg_match('~' . $desen . '~u', $deger);
            if ($sonuc !== 1 && $sonuc !== false) {
                $sonuc = 0;
            }
            if ($sonuc === false) {
                $sonuc = @preg_match('~' . $desen . '~', $deger);
            }
            if ($sonuc !== 1) {
                $hatalar[] = $yol . ' => pattern ihlali: ' . $desen;
            }
        }

        // 6) uzunluk / sınır (metin)
        if (is_string($deger)) {
            $uz = mb_strlen($deger, 'UTF-8');
            if (isset($sema['minLength']) && $uz < (int) $sema['minLength']) {
                $hatalar[] = $yol . ' => minLength ihlali: ' . $uz . ' < ' . (int) $sema['minLength'];
            }
            if (isset($sema['maxLength']) && $uz > (int) $sema['maxLength']) {
                $hatalar[] = $yol . ' => maxLength ihlali: ' . $uz . ' > ' . (int) $sema['maxLength'];
            }
        }
        if (is_int($deger) || is_float($deger)) {
            if (isset($sema['minimum']) && $deger < $sema['minimum']) {
                $hatalar[] = $yol . ' => minimum ihlali: ' . $this->kisa($deger) . ' < ' . $this->kisa($sema['minimum']);
            }
            if (isset($sema['maximum']) && $deger > $sema['maximum']) {
                $hatalar[] = $yol . ' => maximum ihlali: ' . $this->kisa($deger) . ' > ' . $this->kisa($sema['maximum']);
            }
            if (isset($sema['exclusiveMinimum']) && $deger <= $sema['exclusiveMinimum']) {
                $hatalar[] = $yol . ' => exclusiveMinimum ihlali: ' . $this->kisa($deger)
                    . ' <= ' . $this->kisa($sema['exclusiveMinimum']);
            }
        }

        // 7) nesne: required / properties / additionalProperties:false
        $nesne = is_array($deger) && (array_is_list($deger) === false || $deger === []);
        if ($nesne) {
            if (isset($sema['required']) && is_array($sema['required'])) {
                foreach ($sema['required'] as $alan) {
                    if (is_string($alan) && !array_key_exists($alan, $deger)) {
                        $hatalar[] = $yol . '.' . $alan . ' => zorunlu alan eksik';
                    }
                }
            }
            if (isset($sema['properties']) && is_array($sema['properties'])) {
                foreach ($sema['properties'] as $alan => $altSema) {
                    if (!is_string($alan) || !array_key_exists($alan, $deger)) {
                        continue;
                    }
                    if ($altSema === false) {
                        $hatalar[] = $yol . '.' . $alan . ' => bu katmanda yasakli alan (semada false)';

                        continue;
                    }
                    $this->dogrula($altSema, $deger[$alan], $yol . '.' . $alan, $hatalar);
                }
            }
            if (($sema['additionalProperties'] ?? null) === false && isset($sema['properties'])) {
                $bilinen = array_keys((array) $sema['properties']);
                foreach ($deger as $k => $_) {
                    if (!in_array($k, $bilinen, true)) {
                        $hatalar[] = $yol . '.' . $k
                            . ' => additionalProperties=false (bilinmeyen anahtar, ek disi)';
                    }
                }
            }
        }

        // 8) dizi öğeleri
        if (isset($sema['items']) && is_array($deger) && array_is_list($deger)) {
            foreach ($deger as $i => $og) {
                $this->dogrula($sema['items'], $og, $yol . '[' . $i . ']', $hatalar);
            }
        }
    }

    /**
     * oneOf (tam 1 dal) / anyOf (en az 1 dal) yürütmesi.
     *
     * @param array<int, mixed> $dallar
     */
    private function varyant(array $dallar, mixed $deger, string $yol, array &$hatalar, bool $enAzBir): void
    {
        $gecen = 0;
        $enIyi = null;
        $enIyiSay = PHP_INT_MAX;
        foreach ($dallar as $dal) {
            $gecici = [];
            $this->dogrula($dal, $deger, $yol, $gecici);
            if ($gecici === []) {
                $gecen++;
            } elseif (count($gecici) < $enIyiSay) {
                $enIyiSay = count($gecici);
                $enIyi = $gecici;
            }
        }
        if ($enAzBir) {
            if ($gecen === 0) {
                if (is_array($enIyi)) {
                    foreach ($enIyi as $e) {
                        $hatalar[] = $e;
                    }
                } else {
                    $hatalar[] = $yol . ' => anyOf: hicbir dal eslesmedi';
                }
            }

            return;
        }
        if ($gecen === 1) {
            return;
        }
        if ($gecen > 1) {
            $hatalar[] = $yol . ' => oneOf: ' . $gecen . ' dal eslesti (belirsiz varyant)';

            return;
        }
        if (is_array($enIyi)) {
            foreach ($enIyi as $e) {
                $hatalar[] = $e;
            }
        } else {
            $hatalar[] = $yol . ' => oneOf: hicbir dal eslesmedi';
        }
    }

    /**
     * `#/$defs/…` çözümü (zincirli $ref destekli, derinlik sınırı 8).
     */
    private function coz(string $ref, int $derinlik = 0): ?array
    {
        if ($derinlik > 8 || !str_starts_with($ref, self::REF_ONEK)) {
            return null;
        }
        $ad = substr($ref, strlen(self::REF_ONEK));
        $hedef = $this->sema['$defs'][$ad] ?? null;
        if (!is_array($hedef)) {
            return null;
        }
        if (isset($hedef['$ref']) && is_string($hedef['$ref'])) {
            return $this->coz($hedef['$ref'], $derinlik + 1);
        }

        return $hedef;
    }

    /**
     * `*_enum` referansları için taxonomy.json senkronu (SYNC notlu 17 anahtar).
     * Şema enum'u ile taksonomi seti farklıysa hata üretir (drift = buradan döner).
     */
    private function enumDrift(string $ref, string $yol, array &$hatalar): void
    {
        if (!str_ends_with($ref, '_enum')) {
            return;
        }
        $tam = basename(str_replace('\\', '/', $ref));
        $anahtar = substr($tam, 0, -strlen('_enum'));
        if ($anahtar === '') {
            return;
        }
        if (!array_key_exists($anahtar, $this->drift)) {
            $this->drift[$anahtar] = $this->driftBul($anahtar, $ref);
        }
        if ($this->drift[$anahtar] !== '') {
            $hatalar[] = $yol . ' => ' . $this->drift[$anahtar];
        }
    }

    private function driftBul(string $anahtar, string $ref): string
    {
        if ($this->taxonomy->yuklemeHatasi() !== null) {
            return '';
        }
        $hedef = $this->coz($ref);
        $enum = is_array($hedef) ? ($hedef['enum'] ?? null) : null;
        if (!is_array($enum)) {
            return '';
        }
        if (!$this->taxonomy->has($anahtar)) {
            return "taxonomy.json'de anahtar yok: {$anahtar} (enum drift)";
        }
        $set = $this->taxonomy->values($anahtar);
        $semaFazla = array_diff($enum, $set);
        $taxFazla = array_diff($set, $enum);
        if ($semaFazla !== [] || $taxFazla !== []) {
            return "taxonomy drift: {$anahtar} (sema-taxonomi setleri farkli)";
        }

        return '';
    }

    private function tipUygun(mixed $deger, string $tip): bool
    {
        return match ($tip) {
            'null' => $deger === null,
            'string' => is_string($deger),
            'integer' => is_int($deger),
            'number' => is_int($deger) || is_float($deger),
            'boolean' => is_bool($deger),
            'object' => is_array($deger) && (array_is_list($deger) === false || $deger === []),
            'array' => is_array($deger) && (array_is_list($deger) || $deger === []),
            default => true,
        };
    }

    private function tipAdi(mixed $deger): string
    {
        return match (true) {
            $deger === null => 'null',
            is_bool($deger) => 'boolean',
            is_int($deger) => 'integer',
            is_float($deger) => 'number',
            is_string($deger) => 'string',
            is_array($deger) => array_is_list($deger) ? 'array' : 'object',
            default => get_debug_type($deger),
        };
    }

    private function kisa(mixed $deger): string
    {
        if (is_string($deger)) {
            $m = mb_strlen($deger, 'UTF-8') > 40;
            $d = $m ? mb_substr($deger, 0, 40, 'UTF-8') . '…' : $deger;

            return '"' . $d . '"';
        }
        if (is_bool($deger) || $deger === null) {
            return var_export($deger, true);
        }
        if (is_array($deger)) {
            return (array_is_list($deger) ? 'dizi' : 'nesne') . '(' . count($deger) . ')';
        }

        return (string) $deger;
    }
}
