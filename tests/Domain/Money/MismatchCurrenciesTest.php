<?php

declare(strict_types=1);

namespace Tuzex\Ddd\SharedKernel\Test\Domain\Money;

use PHPUnit\Framework\TestCase;
use Tuzex\Ddd\SharedKernel\Domain\Money;
use Tuzex\Ddd\SharedKernel\Domain\Money\Currency\Euro;
use Tuzex\Ddd\SharedKernel\Domain\Money\MismatchCurrencies;

final class MismatchCurrenciesTest extends TestCase
{
    public function testItReturnsSpecificMessage(): void
    {
        $money = new Money(12.34, new Euro());
        $exception = new MismatchCurrencies($money, $money);

        $this->assertSame('Mathematical operations are allowed for only the same currency (EUR => EUR).', $exception->getMessage());
    }
}
