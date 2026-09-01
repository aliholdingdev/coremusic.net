<?php declare(strict_types=1);

namespace CoreMusic\Auth\Test\Domain\ValueObject;

use CoreMusic\Auth\Domain\ValueObject\Password;
use PHPUnit\Framework\TestCase;

class PasswordTest extends TestCase
{
    public function testCreateWithValidPassword(): void
    {
        $password = Password::create('mypassword123');
        $this->assertSame('mypassword123', $password->raw());
    }

    public function testCreateWithTooShortPasswordThrowsException(): void
    {
        $this->expectException(\CoreMusic\Exception\ValidationException::class);
        Password::create('short');
    }

    public function testHashWithPepperReturnsHash(): void
    {
        $password = Password::create('mypassword123');
        $pepper = 'test-pepper-key';
        $hash = $password->hashWithPepper($pepper);

        $this->assertNotEmpty($hash);
        $this->assertNotSame('mypassword123', $hash);
        $this->assertStringStartsWith('$argon2id$', $hash);
    }

    public function testVerifyReturnsTrueForCorrectPassword(): void
    {
        $password = Password::create('mypassword123');
        $pepper = 'test-pepper-key';
        $hash = $password->hashWithPepper($pepper);

        $this->assertTrue($password->verify($hash, $pepper));
    }

    public function testVerifyReturnsFalseForWrongPassword(): void
    {
        $password = Password::create('mypassword123');
        $wrongPassword = Password::create('wrongpassword');
        $pepper = 'test-pepper-key';
        $hash = $password->hashWithPepper($pepper);

        $this->assertFalse($wrongPassword->verify($hash, $pepper));
    }

    public function testVerifyReturnsFalseForEmptyPepper(): void
    {
        $password = Password::create('mypassword123');
        $hash = '$argon2id$fakehash';

        $this->assertFalse($password->verify($hash, ''));
    }
}
