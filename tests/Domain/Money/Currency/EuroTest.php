<?php

declare(strict_types=1);

namespace Tuzex\Ddd\SharedKernel\Test\Domain\Money\Currency;

use PHPUnit\Framework\TestCase;
use Tuzex\Ddd\SharedKernel\Domain\Money\Currency\Euro;

final class EuroTest extends TestCase
{
    public function testItContainsValidSubUnit(): void
    {
        $euro = new Euro();

        $this->assertSame(100, $euro->fraction());
        $this->assertSame(2, $euro->precision());
    }

    public function testItReturnsValidCode(): void
    {
        $euro = new Euro();

        $this->assertSame('EUR', $euro->code());
    }
}
