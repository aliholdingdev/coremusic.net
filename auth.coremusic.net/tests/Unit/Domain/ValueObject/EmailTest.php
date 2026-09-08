<?php

declare(strict_types=1);

namespace CoreMusic\Auth\Test\Unit\Domain\ValueObject;

use CoreMusic\Auth\Domain\ValueObject\Email;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class EmailTest extends TestCase
{
    #[Test]
    public function create_with_valid_email(): void
    {
        $email = Email::create('test@example.com');
        $this->assertSame('test@example.com', (string) $email);
    }

    #[Test]
    public function create_normalizes_to_lower_case(): void
    {
        $email = Email::create('TEST@EXAMPLE.COM');
        $this->assertSame('test@example.com', (string) $email);
    }

    #[Test]
    public function create_trims_whitespace(): void
    {
        $email = Email::create('  test@example.com  ');
        $this->assertSame('test@example.com', (string) $email);
    }

    #[Test]
    public function create_with_invalid_email_throws_exception(): void
    {
        $this->expectException(\CoreMusic\Exception\ValidationException::class);
        Email::create('not-an-email');
    }

    #[Test]
    public function create_with_empty_email_throws_exception(): void
    {
        $this->expectException(\CoreMusic\Exception\ValidationException::class);
        Email::create('');
    }

    #[Test]
    public function unsafe_creates_email_without_validation(): void
    {
        $email = Email::unsafe('test@example.com');
        $this->assertSame('test@example.com', (string) $email);
    }

    #[Test]
    public function equals_returns_true_for_same_email(): void
    {
        $email1 = Email::create('test@example.com');
        $email2 = Email::create('test@example.com');
        $this->assertTrue($email1->equals($email2));
    }

    #[Test]
    public function equals_returns_false_for_different_email(): void
    {
        $email1 = Email::create('test@example.com');
        $email2 = Email::create('other@example.com');
        $this->assertFalse($email1->equals($email2));
    }
}
