<?php

declare(strict_types=1);

namespace CoreMusic\Shared\Tests\Unit\Helper;

use CoreMusic\Shared\Helper\StringHelper;
use PHPUnit\Framework\TestCase;

class StringHelperTest extends TestCase
{
    public function testSlugifyBasic(): void
    {
        $this->assertSame('hello-world', StringHelper::slugify('Hello World'));
    }

    public function testSlugifyWithSpecialChars(): void
    {
        $this->assertSame('hello-world', StringHelper::slugify('Hello, World!'));
    }

    public function testSlugifyWithTurkishChars(): void
    {
        $this->assertSame('merhaba-dunya', StringHelper::slugify('Merhaba Dunya'));
    }

    public function testSlugifyWithMultipleDashes(): void
    {
        $this->assertSame('hello-world', StringHelper::slugify('hello---world'));
    }

    public function testTruncateShorterText(): void
    {
        $this->assertSame('hello', StringHelper::truncate('hello', 10));
    }

    public function testTruncateLongerText(): void
    {
        $this->assertSame('hello...', StringHelper::truncate('hello world', 5));
    }

    public function testTruncateCustomSuffix(): void
    {
        $this->assertSame('hello--', StringHelper::truncate('hello world', 5, '--'));
    }

    public function testCamelCase(): void
    {
        $this->assertSame('helloWorld', StringHelper::camelCase('hello_world'));
    }

    public function testStudlyCase(): void
    {
        $this->assertSame('HelloWorld', StringHelper::studlyCase('hello_world'));
    }

    public function testSnakeCase(): void
    {
        $this->assertSame('hello_world', StringHelper::snakeCase('helloWorld'));
    }

    public function testSnakeCaseWithAcronym(): void
    {
        $this->assertSame('hello_world_id', StringHelper::snakeCase('helloWorldID'));
    }

    public function testKebabCase(): void
    {
        $this->assertSame('hello-world', StringHelper::kebabCase('helloWorld'));
    }

    public function testContains(): void
    {
        $this->assertTrue(StringHelper::contains('hello world', 'world'));
        $this->assertFalse(StringHelper::contains('hello world', 'xyz'));
    }

    public function testStartWith(): void
    {
        $this->assertTrue(StringHelper::startWith('hello world', 'hello'));
        $this->assertFalse(StringHelper::startWith('hello world', 'world'));
    }

    public function testEndWith(): void
    {
        $this->assertTrue(StringHelper::endWith('hello world', 'world'));
        $this->assertFalse(StringHelper::endWith('hello world', 'hello'));
    }

    public function testRandom(): void
    {
        $random1 = StringHelper::random(16);
        $random2 = StringHelper::random(16);

        $this->assertSame(16, strlen($random1));
        $this->assertNotSame($random1, $random2);
    }

    public function testIsEmail(): void
    {
        $this->assertTrue(StringHelper::isEmail('user@example.com'));
        $this->assertFalse(StringHelper::isEmail('not-an-email'));
    }

    public function testIsUrl(): void
    {
        $this->assertTrue(StringHelper::isUrl('https://example.com'));
        $this->assertFalse(StringHelper::isUrl('not-a-url'));
    }
}
