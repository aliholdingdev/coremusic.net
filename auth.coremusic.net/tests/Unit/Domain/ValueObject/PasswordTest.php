<?php

declare(strict_types=1);

namespace CoreMusic\Auth\Test\Unit\Domain\ValueObject;

use CoreMusic\Auth\Domain\ValueObject\Password;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class PasswordTest extends TestCase
{
    #[Test]
    public function create_with_valid_password(): void
    {
        $password = Password::create('mypassword123');
        $this->assertSame('mypassword123', $password->raw());
    }

    #[Test]
    public function create_with_too_short_password_throws_exception(): void
    {
        $this->expectException(\CoreMusic\Exception\ValidationException::class);
        Password::create('short');
    }

    #[Test]
    public function hash_with_pepper_returns_hash(): void
    {
        $password = Password::create('mypassword123');
        $pepper = 'test-pepper-key';
        $hash = $password->hashWithPepper($pepper);

        $this->assertNotEmpty($hash);
        $this->assertNotSame('mypassword123', $hash);
        $this->assertStringStartsWith('$argon2id$', $hash);
    }

    #[Test]
    public function verify_returns_true_for_correct_password(): void
    {
        $password = Password::create('mypassword123');
        $pepper = 'test-pepper-key';
        $hash = $password->hashWithPepper($pepper);

        $this->assertTrue($password->verify($hash, $pepper));
    }

    #[Test]
    public function verify_returns_false_for_wrong_password(): void
    {
        $password = Password::create('mypassword123');
        $wrongPassword = Password::create('wrongpassword');
        $pepper = 'test-pepper-key';
        $hash = $password->hashWithPepper($pepper);

        $this->assertFalse($wrongPassword->verify($hash, $pepper));
    }

    #[Test]
    public function verify_returns_false_for_empty_pepper(): void
    {
        $password = Password::create('mypassword123');
        $hash = '$argon2id$fakehash';

        $this->assertFalse($password->verify($hash, ''));
    }
}
