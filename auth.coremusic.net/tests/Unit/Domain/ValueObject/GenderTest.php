<?php

declare(strict_types=1);

namespace CoreMusic\Auth\Test\Unit\Domain\ValueObject;

use CoreMusic\Auth\Domain\ValueObject\Gender;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class GenderTest extends TestCase
{
    #[Test]
    public function create_male_returns_male(): void
    {
        $gender = Gender::create('male');

        $this->assertSame('male', $gender->value);
        $this->assertFalse($gender->isNeutral());
    }

    #[Test]
    public function create_female_returns_female(): void
    {
        $gender = Gender::create('female');

        $this->assertSame('female', $gender->value);
        $this->assertFalse($gender->isNeutral());
    }

    #[Test]
    public function create_neutral_returns_neutral(): void
    {
        $gender = Gender::create('neutral');

        $this->assertSame('neutral', $gender->value);
        $this->assertTrue($gender->isNeutral());
    }

    #[Test]
    public function create_invalid_returns_neutral(): void
    {
        $gender = Gender::create('invalid');

        $this->assertSame('neutral', $gender->value);
        $this->assertTrue($gender->isNeutral());
    }

    #[Test]
    public function create_is_case_insensitive(): void
    {
        $gender = Gender::create('MALE');

        $this->assertSame('male', $gender->value);
    }

    #[Test]
    public function equals_returns_true_for_same_value(): void
    {
        $gender1 = Gender::create('male');
        $gender2 = Gender::create('male');

        $this->assertTrue($gender1->equals($gender2));
    }

    #[Test]
    public function equals_returns_false_for_different_value(): void
    {
        $gender1 = Gender::create('male');
        $gender2 = Gender::create('female');

        $this->assertFalse($gender1->equals($gender2));
    }

    #[Test]
    public function to_string_returns_value(): void
    {
        $gender = Gender::create('female');

        $this->assertSame('female', (string) $gender);
    }
}
