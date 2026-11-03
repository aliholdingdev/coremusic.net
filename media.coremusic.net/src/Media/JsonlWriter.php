<?php

declare(strict_types=1);

namespace Media;

/**
 * JsonlWriter — `catalog.jsonl` dosyası yazıcı (tamponlu, atomik).
 *
 * SSOT yan JSON dosyalarıdır (`config/media.schema.json` + `meta.json`lar);
 * bu indeks her zaman `bin/scan.php` ile baştan üretilebilir.
 *
 * DRY-run → STDOUT'a basar, dosyaya dokunmaz.
 * `--rebuild` → mevcut dosyayı önce siler.
 */
final class JsonlWriter
{
    /** JSONL satır bayrakları: Türkçe UTF-8 korunur, `/` kaçışlanmaz. */
    private const JSON_BAYRAK = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE;

    /** @var list<array<string, mixed>> */
    private array $kayitlar = [];

    private bool $dryRun;

    private string $yol;

    public function __construct(string $yol, bool $dryRun = false)
    {
        $this->yol = $yol;
        $this->dryRun = $dryRun;
    }

    /** Proje kökünden `catalog/catalog.jsonl` hedefi. */
    public static function fromProject(?string $kok = null, bool $dryRun = false): self
    {
        $kok ??= dirname(__DIR__, 2);

        return new self($kok . '/catalog/catalog.jsonl', $dryRun);
    }

    public function yol(): string
    {
        return $this->yol;
    }

    public function dryRun(): bool
    {
        return $this->dryRun;
    }

    /** Bir katalog kaydını tampona ekler. */
    public function ekle(array $kayit): void
    {
        $this->kayitlar[] = $kayit;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function kayitlar(): array
    {
        return $this->kayitlar;
    }

    public function satirSayisi(): int
    {
        return count($this->kayitlar);
    }

    /**
     * Tampondaki kayıtları JSONL'e yazar.
     *
     * dry-run → STDOUT'a basar, dosyaya dokunmaz. `--rebuild` → mevcut dosyayı
     * önce siler (eski indeks kalmasın).
     *
     * @return array{yazildi: int, bayt: int, hata: string|null}
     */
    public function yaz(bool $rebuild = false): array
    {
        $satirlar = [];
        foreach ($this->kayitlar as $kayit) {
            $satir = json_encode($kayit, self::JSON_BAYRAK);
            if ($satir === false) {
                continue; // JSON'a çevrilemeyen kayıt atlanır (sayaç düşer, çökme yok)
            }
            $satirlar[] = $satir;
        }
        $metin = $satirlar === [] ? '' : implode("\n", $satirlar) . "\n";

        if ($this->dryRun) {
            if ($metin !== '') {
                echo $metin;
            }

            return ['yazildi' => count($satirlar), 'bayt' => strlen($metin), 'hata' => null];
        }

        $klasor = dirname($this->yol);
        if (!is_dir($klasor) && !@mkdir($klasor, 0775, true) && !is_dir($klasor)) {
            return ['yazildi' => 0, 'bayt' => 0, 'hata' => 'klasor olusturulamadi: ' . $klasor];
        }
        if ($rebuild && is_file($this->yol)) {
            @unlink($this->yol); // --rebuild: eskisini sil, baştan üret
        }
        $bayt = @file_put_contents($this->yol, $metin, LOCK_EX);
        if ($bayt === false) {
            return ['yazildi' => 0, 'bayt' => 0, 'hata' => 'yazilamadi: ' . $this->yol];
        }

        return ['yazildi' => count($satirlar), 'bayt' => (int) $bayt, 'hata' => null];
    }

    /**
     * Mevcut katalogdan `sha256 → yol` kümesi okur (ingest dedupe kaynağı).
     *
     * @return array<string, string> hash => ilk geçtiği katalog yolu
     */
    public static function hashSetiOku(string $dosya): array
    {
        $cikti = [];
        if ($dosya === '' || !is_file($dosya)) {
            return $cikti;
        }
        $satirlar = @file($dosya, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($satirlar === false) {
            return $cikti;
        }
        foreach ($satirlar as $satir) {
            $kayit = json_decode($satir, true);
            if (!is_array($kayit)) {
                continue;
            }
            $hash = $kayit['sha256'] ?? null;
            if (!is_string($hash) || preg_match('/^[a-f0-9]{64}$/', $hash) !== 1) {
                continue;
            }
            $yollar = is_array($kayit['yollar'] ?? null) ? $kayit['yollar'] : [];
            $cikti[$hash] = (string) ($yollar[0] ?? '');
        }

        return $cikti;
    }
}