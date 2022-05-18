<?php

declare(strict_types=1);

namespace Tuzex\Ddd\SharedKernel\Domain\Quantity;

use Webmozart\Assert\Assert;

abstract class MeasureUnit
{
    public function __construct(
        public readonly string $symbol,
        public readonly int $precision,
    ) {
        Assert::greaterThanEq($precision, 0, 'Measure unit precision must by greater or equal than zero, "%s" given');
    }

    public function equals(self $that): bool
    {
        return $this::class === $that::class && $that->symbol === $this->symbol;
    }

    public function fractional(): bool
    {
        return $this->precision > 0;
    }

    public function fraction(): int
    {
        return pow(10, $this->precision);
    }
}
