<?php

declare(strict_types=1);

namespace CoreMusic\Shared\Exception;

use RuntimeException;

class NotFoundException extends RuntimeException
{
    public static function entity(string $entity, string $identifier): self
    {
        return new self("{$entity} bulunamadi: {$identifier}", 404);
    }

    public static function user(string $id): self
    {
        return self::entity('Kullanici', $id);
    }

    public static function media(string $id): self
    {
        return self::entity('Medya', $id);
    }

    public static function playlist(string $id): self
    {
        return self::entity('Playlist', $id);
    }
}
