<?php

declare(strict_types=1);

namespace Media;

/**
 * CatalogWriter — Katalog indeks yazıcı (facade).
 *
 * JSONL dosyası ve opsiyonel MySQL veritabanına yazımı koordine eder.
 * SSOT yan JSON dosyalarıdır (`config/media.schema.json` + `meta.json`lar);
 * bu indeks her zaman `bin/scan.php` ile baştan üretilebilir.
 *
 * MySQL (`media_catalog`) OPSİYONELDİR: `pdo_mysql` yoksa veya bağlanılamazsa
 * tek bir uyarı ile JSONL ile devam edilir — çökme YOK.
 */
final class CatalogWriter
{
    /** @var list<array<string, mixed>> */
    private array $kayitlar = [];

    private bool $dryRun;

    private string $yol;

    private JsonlWriter $jsonlWriter;

    public function __construct(string $yol, bool $dryRun = false)
    {
        $this->yol = $yol;
        $this->dryRun = $dryRun;
        $this->jsonlWriter = new JsonlWriter($yol, $dryRun);
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
        $this->jsonlWriter->ekle($kayit);
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
        return $this->jsonlWriter->yaz($rebuild);
    }

    /**
     * MySQL `media_catalog` türetilmiş indeksine yazar (opsiyonel).
     *
     * @param list<array<string, mixed>> $hucreler tip·veri·yol anahtarları üretim tarafında garanti edilir
     * @param bool                       $rebuild true → varlık tablolarını boşaltıp baştan doldurur
     *
     * @return array{baglandi: bool, yazildi: int, uyari: list<string>}
     */
    public function mysqlYaz(array $hucreler, bool $rebuild = false): array
    {
        return MySqlCatalogRepository::yaz($hucreler, $rebuild);
    }

    /**
     * Mevcut katalogdan `sha256 → yol` kümesi okur (ingest dedupe kaynağı).
     *
     * @return array<string, string> hash => ilk geçtiği katalog yolu
     */
    public static function hashSetiOku(string $dosya): array
    {
        return JsonlWriter::hashSetiOku($dosya);
    }
}