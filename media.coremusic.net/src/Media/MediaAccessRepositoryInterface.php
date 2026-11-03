<?php

declare(strict_types=1);

namespace Media;

/**
 * MediaAccessRepositoryInterface — erişim denetimi + varlık okuma + denetim kaydı.
 *
 * Arayüz, birimi test edilebilirlik için VARDIR: testler sahte (in-memory)
 * uygulama kullanır → veritabanına ve gerçek medya ağacına asla dokunulmaz.
 * Üretim uygulaması `MySqlMediaAccessRepository` (PDO prepared, `SELECT *` YOK).
 *
 * Hata sözleşmesi (fail-closed):
 *   - DB hatası YUKARI taşınır (bağlantı yok, izin doğrulanamaz) → guard 403/500 üretir
 *   - kayıt bulunamadı → `null`/`false` (yok saymakla yetinme — sessiz geçiş YOK)
 */
interface MediaAccessRepositoryInterface
{
    /**
     * Kullanıcının `file_key` üzerinde okuma izni var mı?
     * (Adayı: `is_deleted = 0` + süre dolmamış + `read`/`admin`.)
     *
     * @param string $userIdHex UUIDv7 binary(16) — 32 hex, tire YOK
     */
    public function hasReadGrant(string $userIdHex, string $fileKey): bool;

    /**
     * `file_key` (asset ULID) için katalog varlık kaydı.
     *
     * @return array{
     *     id: string,
     *     dosya_yolu: string,
     *     durum: string,
     *     dosya: string,
     *     format: string,
     *     boyut_bayt: int,
     *     sha256: string
     * }|null Kayıt yoksa `null`
     */
    public function findAsset(string $fileKey): ?array;

    /**
     * `media_audit` insert — 200/206/304 gövdesi gönderilmeden ÖNCE çağrılır.
     * Başarısızlık yukarı taşınır (kayıtlanmayan erişim = çekilmeyen istek).
     *
     * @param string $userIdHex  32 hex (binary(16) → UNHEX)
     * @param int    $status     200 | 206 | 304
     */
    public function recordAudit(
        string $userIdHex,
        string $fileKey,
        string $relativePath,
        int $fileSize,
        int $status,
    ): void;
}