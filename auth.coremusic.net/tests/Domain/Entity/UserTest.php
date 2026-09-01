<?php declare(strict_types=1);

namespace CoreMusic\Auth\Test\Domain\Entity;

use CoreMusic\Auth\Domain\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testFromRowCreatesUserCorrectly(): void
    {
        $row = [
            'id' => 'a1b2c3d4e5f6a7b8c9d0e1f2a3b4c5d6',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password_hash' => '$argon2id$v=19$m=65536,t=4,p=2$...',
            'display_name' => 'Test User',
            'gender' => 'male',
            'avatar_url' => 'https://example.com/avatar.jpg',
            'account_type' => 'premium',
            'is_active' => 1,
            'is_banned' => 0,
            'last_login_at' => '2026-09-01 10:00:00',
            'created_at' => '2026-01-01 00:00:00',
            'updated_at' => '2026-09-01 10:00:00',
        ];

        $user = User::fromRow($row);

        $this->assertSame('a1b2c3d4e5f6a7b8c9d0e1f2a3b4c5d6', $user->id);
        $this->assertSame('testuser', $user->username);
        $this->assertSame('test@example.com', $user->email);
        $this->assertSame('Test User', $user->displayName);
        $this->assertSame('male', $user->gender);
        $this->assertSame('premium', $user->accountType);
        $this->assertTrue($user->isActive());
        $this->assertFalse($user->isBanned());
    }

    public function testToArrayExcludesPasswordHash(): void
    {
        $row = [
            'id' => 'a1b2c3d4e5f6a7b8c9d0e1f2a3b4c5d6',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password_hash' => '$argon2id$...',
            'display_name' => 'Test User',
            'gender' => 'neutral',
            'avatar_url' => '',
            'account_type' => 'free',
            'is_active' => 1,
            'is_banned' => 0,
            'last_login_at' => null,
            'created_at' => '2026-01-01',
            'updated_at' => '2026-09-01',
        ];

        $user = User::fromRow($row);
        $array = $user->toArray();

        $this->assertArrayNotHasKey('password_hash', $array);
        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('username', $array);
        $this->assertArrayHasKey('email', $array);
    }

    public function testIsBannedReturnsTrueForBannedUser(): void
    {
        $row = [
            'id' => 'a1b2c3d4e5f6a7b8c9d0e1f2a3b4c5d6',
            'username' => 'banneduser',
            'email' => 'banned@example.com',
            'password_hash' => '$argon2id$...',
            'display_name' => 'Banned User',
            'gender' => 'neutral',
            'avatar_url' => '',
            'account_type' => 'free',
            'is_active' => 1,
            'is_banned' => 1,
            'last_login_at' => null,
            'created_at' => '2026-01-01',
            'updated_at' => '2026-09-01',
        ];

        $user = User::fromRow($row);

        $this->assertTrue($user->isBanned());
        $this->assertTrue($user->isActive());
    }
}
