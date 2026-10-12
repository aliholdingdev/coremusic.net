<?php declare(strict_types=1);

namespace CoreMusic\Security;

final class SessionKeys
{
    public const USER_ID       = 'MM_UserID';
    public const USERNAME      = 'MM_Username';
    public const EMAIL         = 'MM_Email';
    public const DISPLAY_NAME  = 'MM_DisplayName';
    public const ACCOUNT_TYPE  = 'MM_AccountType';
    public const IMAGE         = 'MM_Image';
    public const GENDER        = 'cm_gender';
    public const LAST_ACTIVE   = '_session_last_active';
    public const ROTATED_AT    = '_session_rotated_at';

    public const USER_KEYS = [
        self::USER_ID,
        self::USERNAME,
        self::EMAIL,
        self::DISPLAY_NAME,
        self::ACCOUNT_TYPE,
        self::IMAGE,
        self::GENDER,
    ];

    private function __construct() {}
}
