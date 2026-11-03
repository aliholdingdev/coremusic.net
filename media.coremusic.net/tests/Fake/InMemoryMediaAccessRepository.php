<?php

declare(strict_types=1);

namespace Media\Test\Fake;

use Media\MediaAccessRepositoryInterface;

/**
 * InMemoryMediaAccessRepository — test sahtesi (DB'ye ASLA dokunmaz).
 *
 * Sahte amaç: `MediaAccessGuard`/`MediaStreamController` birim testleri gerçek
 * MySQL'e ve gerçek medya ağacına (38 GB, gitignored) değmesin.
 * Her metodun "hataya düşür" anahtarı vardır → fail-closed yolları test edilir.
 */
final class InMemoryMediaAccessRepository implements MediaAccessRepositoryInterface
{
    /** @var array<int, array{userId: string, fileKey: string, path: string, size: int, status: int}> */
    public array $auditKayitlari = [];

    public bool $izinVar = true;

    /** @var array{id: string, dosya_yolu: string, durum: string, dosya: string, format: string, boyut_bayt: int, sha256: string}|null */
    public ?array $asset = null;

    public ?\Throwable $grantHatasi = null;

    public ?\Throwable $assetHatasi = null;

    public ?\Throwable $auditHatasi = null;

    public function __construct()
    {
    }

    public function hasReadGrant(string $userIdHex, string $fileKey): bool
    {
        if ($this->grantHatasi !== null) {
            throw $this->grantHatasi;
        }

        return $this->izinVar;
    }

    public function findAsset(string $fileKey): ?array
    {
        if ($this->assetHatasi !== null) {
            throw $this->assetHatasi;
        }

        return $this->asset;
    }

    public function recordAudit(
        string $userIdHex,
        string $fileKey,
        string $relativePath,
        int $fileSize,
        int $status,
    ): void {
        if ($this->auditHatasi !== null) {
            throw $this->auditHatasi;
        }

        $this->auditKayitlari[] = [
            'userId' => $userIdHex,
            'fileKey' => $fileKey,
            'path' => $relativePath,
            'size' => $fileSize,
            'status' => $status,
        ];
    }
}