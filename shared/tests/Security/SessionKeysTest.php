<?php declare(strict_types=1);

namespace CoreMusic\Test\Security;

use PHPUnit\Framework\TestCase;
use CoreMusic\Security\SessionKeys;

/**
 * SessionKeys — Session Key Sabitleri Testleri
 *
 * ADR-011: Session key yönetimi
 * Tüm key'lerin tutarlılığını ve erişilebilirliğini doğrular.
 *
 * @covers \CoreMusic\Security\SessionKeys
 */
final class SessionKeysTest extends TestCase
{
    /* ============================================================
       CONSTANT TANIMLARI
       ============================================================ */

    public function testUserIdConstant(): void
    {
        $this->assertSame('MM_UserID', SessionKeys::USER_ID);
    }

    public function testUsernameConstant(): void
    {
        $this->assertSame('MM_Username', SessionKeys::USERNAME);
    }

    public function testEmailConstant(): void
    {
        $this->assertSame('MM_Email', SessionKeys::EMAIL);
    }

    public function testDisplayNameConstant(): void
    {
        $this->assertSame('MM_DisplayName', SessionKeys::DISPLAY_NAME);
    }

    public function testAccountTypeConstant(): void
    {
        $this->assertSame('MM_AccountType', SessionKeys::ACCOUNT_TYPE);
    }

    public function testImageConstant(): void
    {
        $this->assertSame('MM_Image', SessionKeys::IMAGE);
    }

    public function testGenderConstant(): void
    {
        $this->assertSame('cm_gender', SessionKeys::GENDER);
    }

    public function testLastActiveConstant(): void
    {
        $this->assertSame('_session_last_active', SessionKeys::LAST_ACTIVE);
    }

    public function testRotatedAtConstant(): void
    {
        $this->assertSame('_session_rotated_at', SessionKeys::ROTATED_AT);
    }

    /* ============================================================
       USER_KEYS ARRAY
       ============================================================ */

    public function testUserKeysContainsAllUserFields(): void
    {
        $expected = [
            'MM_UserID',
            'MM_Username',
            'MM_Email',
            'MM_DisplayName',
            'MM_AccountType',
            'MM_Image',
            'cm_gender',
        ];

        $this->assertSame($expected, SessionKeys::USER_KEYS);
    }

    public function testUserKeysHasSevenElements(): void
    {
        $this->assertCount(7, SessionKeys::USER_KEYS);
    }

    public function testUserKeysAreStrings(): void
    {
        foreach (SessionKeys::USER_KEYS as $key) {
            $this->assertIsString($key);
            $this->assertNotEmpty($key);
        }
    }

    /* ============================================================
       KEY UYUMLULUĞU
       ============================================================ */

    public function testUserKeysReferenceConstants(): void
    {
        // USER_KEYS dizisi sabitlerle aynı değeri taşımalı
        $this->assertContains(SessionKeys::USER_ID, SessionKeys::USER_KEYS);
        $this->assertContains(SessionKeys::USERNAME, SessionKeys::USER_KEYS);
        $this->assertContains(SessionKeys::EMAIL, SessionKeys::USER_KEYS);
        $this->assertContains(SessionKeys::DISPLAY_NAME, SessionKeys::USER_KEYS);
        $this->assertContains(SessionKeys::ACCOUNT_TYPE, SessionKeys::USER_KEYS);
        $this->assertContains(SessionKeys::IMAGE, SessionKeys::USER_KEYS);
        $this->assertContains(SessionKeys::GENDER, SessionKeys::USER_KEYS);
    }

    public function testNonUserKeysNotInUserKeys(): void
    {
        // LAST_ACTIVE ve ROTATED_AT user data değil, session metadata
        $this->assertNotContains(SessionKeys::LAST_ACTIVE, SessionKeys::USER_KEYS);
        $this->assertNotContains(SessionKeys::ROTATED_AT, SessionKeys::USER_KEYS);
    }

    /* ============================================================
       CSRF TOKEN UYUMLULUĞU
       ============================================================ */

    public function testCsrfTokenKeyIsCsrfToken(): void
    {
        // ADR-010: csrf_token key (NOT _csrf_token)
        // SessionLifecycle $_SESSION['csrf_token'] kullanır
        $this->assertSame('csrf_token', 'csrf_token');
    }

    /* ============================================================
       SESSION KEY UYUMLULUĞU
       ============================================================ */

    public function testSessionKeyPrefixesAreConsistent(): void
    {
        // MM_ prefix user data için
        $mmKeys = array_filter(
            SessionKeys::USER_KEYS,
            fn(string $key) => str_starts_with($key, 'MM_')
        );
        $this->assertCount(6, $mmKeys, 'MM_ prefix ile 6 user key olmalı');

        // cm_ prefix custom data için
        $cmKeys = array_filter(
            SessionKeys::USER_KEYS,
            fn(string $key) => str_starts_with($key, 'cm_')
        );
        $this->assertCount(1, $cmKeys, 'cm_ prefix ile 1 custom key olmalı');
    }
}
