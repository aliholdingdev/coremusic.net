<?php declare(strict_types=1);

namespace CoreMusic\Auth\Test\Domain\ValueObject;

use CoreMusic\Auth\Domain\ValueObject\Email;
use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase
{
    public function testCreateWithValidEmail(): void
    {
        $email = Email::create('test@example.com');
        $this->assertSame('test@example.com', (string)$email);
    }

    public function testCreateNormalizesToLowerCase(): void
    {
        $email = Email::create('TEST@EXAMPLE.COM');
        $this->assertSame('test@example.com', (string)$email);
    }

    public function testCreateTrimsWhitespace(): void
    {
        $email = Email::create('  test@example.com  ');
        $this->assertSame('test@example.com', (string)$email);
    }

    public function testCreateWithInvalidEmailThrowsException(): void
    {
        $this->expectException(\CoreMusic\Exception\ValidationException::class);
        Email::create('not-an-email');
    }

    public function testCreateWithEmptyEmailThrowsException(): void
    {
        $this->expectException(\CoreMusic\Exception\ValidationException::class);
        Email::create('');
    }

    public function testUnsafeCreatesEmailWithoutValidation(): void
    {
        $email = Email::unsafe('test@example.com');
        $this->assertSame('test@example.com', (string)$email);
    }

    public function testEqualsReturnsTrueForSameEmail(): void
    {
        $email1 = Email::create('test@example.com');
        $email2 = Email::create('test@example.com');
        $this->assertTrue($email1->equals($email2));
    }

    public function testEqualsReturnsFalseForDifferentEmail(): void
    {
        $email1 = Email::create('test@example.com');
        $email2 = Email::create('other@example.com');
        $this->assertFalse($email1->equals($email2));
    }
}
