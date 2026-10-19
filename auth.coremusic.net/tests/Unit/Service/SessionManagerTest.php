<?php declare(strict_types=1);

namespace CoreMusic\Auth\Test\Unit\Service;

use CoreMusic\Auth\Service\SessionManager;
use PHPUnit\Framework\TestCase;

/**
 * B-F-12 regresyonu — setAuthUser rol + izin yazmalıdır.
 *
 * Eksik MM_UserRole yazımı AuthMiddleware'de 'user' fallback'ine yol açıyor
 * ve RBAC katmanını etkisizleştiriyordu.
 */
final class SessionManagerTest extends TestCase
{
    private SessionManager $session;

    protected function setUp(): void
    {
        $_SESSION = [];
        $this->session = new SessionManager();
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
    }

    /** @param array<string, mixed> $overrides */
    private static function baseUser(array $overrides = []): array
    {
        return array_merge([
            'id'       => '018f00000000000000000000000000aa',
            'username' => 'bayram',
            'email'    => 'bayram@example.test',
        ], $overrides);
    }

    public function testSetAuthUser_writesRoleAndPermissions(): void
    {
        $this->session->setAuthUser(self::baseUser([
            'role'        => 'admin',
            'permissions' => ['library.manage'],
        ]));

        $this->assertSame('admin', $_SESSION['MM_UserRole']);
        $this->assertSame(['library.manage'], $_SESSION['MM_Permissions']);
    }

    public function testSetAuthUser_withoutRole_defaultsToUser(): void
    {
        $this->session->setAuthUser(self::baseUser());

        $this->assertSame('user', $_SESSION['MM_UserRole']);
        $this->assertSame([], $_SESSION['MM_Permissions']);
    }

    public function testSetAuthUser_emptyStringRole_becomesUser(): void
    {
        $this->session->setAuthUser(self::baseUser(['role' => '']));

        $this->assertSame('user', $_SESSION['MM_UserRole'], 'Boş rol string\'i geçerli rol sayılmaz');
    }

    public function testSetAuthUser_nonArrayPermissions_becomeEmptyArray(): void
    {
        $this->session->setAuthUser(self::baseUser([
            'role'        => 'admin',
            'permissions' => 'bogus',
        ]));

        $this->assertSame([], $_SESSION['MM_Permissions']);
    }

    public function testSetAuthUser_keepsCoreIdentityFields(): void
    {
        $this->session->setAuthUser(self::baseUser());

        $this->assertSame('018f00000000000000000000000000aa', $_SESSION['MM_UserID']);
        $this->assertSame('bayram', $_SESSION['MM_Username']);
        $this->assertSame('bayram@example.test', $_SESSION['MM_Email']);
    }
}
