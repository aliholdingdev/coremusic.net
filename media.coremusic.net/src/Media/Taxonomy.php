<?php

declare(strict_types=1);

namespace Media;

/**
 * Kapalı (closed) taksonomi okuyucusu — config/taxonomy.json (17 anahtar).
 *
 * DB/JSON drift'inin tek referansı burasıdır: `values()` / `isValid()` yalnız
 * bu dosyadaki seti döner. `etiket[]` açık (open) set olduğundan denetlenmez.
 */
final class Taxonomy
{
    /** @var array<string, list<string>> */
    private array $defs = [];

    private ?string $yuklemeHatasi = null;

    public function __construct(string $dosya)
    {
        if ($dosya === '') {
            $this->yuklemeHatasi = 'taxonomy dosyasi verilmedi';

            return;
        }
        $ham = @file_get_contents($dosya);
        if ($ham === false) {
            $this->yuklemeHatasi = "taxonomy okunamadi: {$dosya}";

            return;
        }
        $cozulmus = json_decode($ham, true);
        if (!is_array($cozulmus)) {
            $this->yuklemeHatasi = 'taxonomy JSON gecersiz: ' . json_last_error_msg();

            return;
        }
        $tanimlar = $cozulmus['definitions'] ?? null;
        if (!is_array($tanimlar)) {
            $this->yuklemeHatasi = "taxonomy 'definitions' yok: {$dosya}";

            return;
        }
        foreach ($tanimlar as $anahtar => $degerler) {
            if (!is_string($anahtar) || !is_array($degerler)) {
                continue;
            }
            $liste = [];
            foreach ($degerler as $d) {
                if (is_string($d)) {
                    $liste[] = $d;
                }
            }
            $this->defs[$anahtar] = $liste;
        }
    }

    /** Proje kökünden (config/taxonomy.json) yükler. */
    public static function fromProject(?string $kok = null): self
    {
        $kok ??= dirname(__DIR__, 2);

        return new self($kok . '/config/taxonomy.json');
    }

    /**
     * @return list<string> Alanın kapalı seti; bilinmeyen alan → []
     */
    public function values(string $alan): array
    {
        return $this->defs[$alan] ?? [];
    }

    /**
     * Değer kapalı sette mi? Bilinmeyen alan → false.
     */
    public function isValid(string $alan, mixed $deger): bool
    {
        if ($this->defs === [] || !array_key_exists($alan, $this->defs)) {
            return false;
        }

        return in_array($deger, $this->defs[$alan], true);
    }

    public function has(string $alan): bool
    {
        return array_key_exists($alan, $this->defs);
    }

    /**
     * @return list<string>
     */
    public function keys(): array
    {
        return array_keys($this->defs);
    }

    public function yuklemeHatasi(): ?string
    {
        return $this->yuklemeHatasi;
    }
}
