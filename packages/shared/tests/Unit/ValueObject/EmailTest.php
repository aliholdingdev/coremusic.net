<?php

declare(strict_types=1);

namespace CoreMusic\Shared\Tests\Unit\ValueObject;

use CoreMusic\Shared\ValueObject\Email;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase
{
    public function testValidEmailCreation(): void
    {
        $email = new Email('user@example.com');
        $this->assertSame('user@example.com', $email->value());
    }

    public function testEmailIsLowercased(): void
    {
        $email = new Email('User@EXAMPLE.COM');
        $this->assertSame('user@example.com', $email->value());
    }

    public function testEmailIsTrimmed(): void
    {
        $email = new Email('  user@example.com  ');
        $this->assertSame('user@example.com', $email->value());
    }

    public function testEmailDomainExtraction(): void
    {
        $email = new Email('user@example.com');
        $this->assertSame('example.com', $email->domain());
    }

    public function testEmailEquality(): void
    {
        $email1 = new Email('user@example.com');
        $email2 = new Email('user@example.com');
        $email3 = new Email('other@example.com');

        $this->assertTrue($email1->equals($email2));
        $this->assertFalse($email1->equals($email3));
    }

    public function testEmailToString(): void
    {
        $email = new Email('user@example.com');
        $this->assertSame('user@example.com', (string) $email);
    }

    public function testInvalidEmailThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Email('not-an-email');
    }

    public function testEmptyEmailThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Email('');
    }

    public function testEmailWithoutDomainThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Email('user@');
    }

    public function testEmailWithSubdomain(): void
    {
        $email = new Email('user@sub.example.com');
        $this->assertSame('sub.example.com', $email->domain());
    }
}
