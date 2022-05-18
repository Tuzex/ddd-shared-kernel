<?php

declare(strict_types=1);

namespace Tuzex\Ddd\SharedKernel\Domain;

use Tuzex\Ddd\SharedKernel\Domain\Quantity\MeasureUnit;
use Tuzex\Ddd\SharedKernel\Domain\Quantity\MismatchMeasureUnits;
use Tuzex\Ddd\SharedKernel\Domain\Quantity\TooLargeQuantityToSubtract;
use Webmozart\Assert\Assert;

final class Quantity
{
    public readonly int|float $amount;

    public function __construct(
        int|float $amount,
        public readonly MeasureUnit $measureUnit
    ) {
        Assert::greaterThanEq($amount, 0, 'Quantity must by greater or equal than zero, "%s" given');

        $this->amount = intval($amount * $this->measureUnit->fraction()) / $this->measureUnit->fraction();
    }

    public function equals(self $that): bool
    {
        return $this->amount === $that->amount && $this->measureUnit->equals($that->measureUnit);
    }

    public function equalToZero(): bool
    {
        return $this->amount === 0;
    }

    public function comparable(self $that): bool
    {
        return $this->measureUnit->equals($that->measureUnit);
    }

    public function greaterThan(self $that): bool
    {
        return $this->compare($that) > 0;
    }

    public function greaterThanOrEqualTo(self $that): bool
    {
        return $this->compare($that) >= 0;
    }

    public function lessThan(self $that): bool
    {
        return $this->compare($that) < 0;
    }

    public function lessThanOrEqualTo(self $that): bool
    {
        return $this->compare($that) <= 0;
    }

    public function increase(self $that): self
    {
        if (! $this->comparable($that)) {
            throw new MismatchMeasureUnits($this, $that);
        }

        return new self($this->amount + $that->amount, $this->measureUnit);
    }

    public function decrease(self $that): self
    {
        if ($this->lessThan($that)) {
            throw new TooLargeQuantityToSubtract($this, $that);
        }

        return new self($this->amount - $that->amount, $this->measureUnit);
    }

    private function compare(self $that): int
    {
        if (! $this->comparable($that)) {
            throw new MismatchMeasureUnits($this, $that);
        }

        return $this->amount <=> $that->amount;
    }
}
