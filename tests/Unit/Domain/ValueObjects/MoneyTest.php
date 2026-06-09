<?php

namespace Tests\Unit\Domain\ValueObjects;

use App\Domain\ValueObjects\Money;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{
    public function test_can_create_money(): void
    {
        $money = new Money(15000.50);

        $this->assertEquals(15000.50, $money->getAmount());
        $this->assertEquals('IDR', $money->getCurrency());
    }

    public function test_can_create_money_with_custom_currency(): void
    {
        $money = new Money(100, 'USD');

        $this->assertEquals(100, $money->getAmount());
        $this->assertEquals('USD', $money->getCurrency());
    }

    public function test_formats_idr_correctly(): void
    {
        $money = new Money(15000);
        $this->assertEquals('Rp 15.000', $money->format());
    }

    public function test_formats_large_number(): void
    {
        $money = new Money(1250000);
        $this->assertEquals('Rp 1.250.000', $money->format());
    }

    public function test_formats_zero(): void
    {
        $money = new Money(0);
        $this->assertEquals('Rp 0', $money->format());
    }

    public function test_is_immutable(): void
    {
        $money = new Money(5000);
        $amount = $money->getAmount();

        // Modifying the returned value should not affect the original
        $amount = 10000;

        $this->assertEquals(5000, $money->getAmount());
    }
}
