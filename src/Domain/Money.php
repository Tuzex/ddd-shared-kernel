<?php

declare(strict_types=1);

namespace Tuzex\Ddd\SharedKernel\Domain;

use Tuzex\Ddd\SharedKernel\Domain\Money\Currency;
use Tuzex\Ddd\SharedKernel\Domain\Money\MismatchCurrencies;

final class Money
{
    final public const ROUND_HALF_EVEN = PHP_ROUND_HALF_EVEN;

    final public const ROUND_HALF_DOWN = PHP_ROUND_HALF_DOWN;

    final public const ROUND_HALF_ODD = PHP_ROUND_HALF_ODD;

    final public const ROUND_HALF_UP = PHP_ROUND_HALF_UP;

    private readonly int $amountInFractionalUnit;

    public function __construct(
        int|float $amount,
        public readonly Currency $currency,
    ) {
        $this->amountInFractionalUnit = intval($amount * $this->currency->fraction());
    }

    public static function ofSub(int $amount, Currency $currency): self
    {
        return new self($amount / $currency->fraction(), $currency);
    }

    public function comparable(self $that): bool
    {
        return $this->currency->equals($that->currency);
    }

    public function equals(self $that): bool
    {
        return $this->comparable($that)
            && $this->amountInFractionalUnit === $that->amountInFractionalUnit;
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

    public function equalToZero(): bool
    {
        return $this->amountInFractionalUnit === 0;
    }

    public function moreThanZero(): bool
    {
        return $this->amountInFractionalUnit > 0;
    }

    public function lessThanZero(): bool
    {
        return $this->amountInFractionalUnit < 0;
    }

    public function add(self $that): self
    {
        if (! $this->comparable($that)) {
            throw new MismatchCurrencies($this, $that);
        }

        return self::ofSub(
            amount: $this->amountInFractionalUnit + $that->amountInFractionalUnit,
            currency: $this->currency
        );
    }

    public function subtract(self $that): self
    {
        if (! $this->comparable($that)) {
            throw new MismatchCurrencies($this, $that);
        }

        return self::ofSub(
            amount: $this->amountInFractionalUnit - $that->amountInFractionalUnit,
            currency: $this->currency
        );
    }

    /**
     * @phpstan-param self::ROUND_* $rounding
     */
    public function multiply(
        int|float $factor,
        int $rounding = self::ROUND_HALF_UP,
    ): self {
        $result = round(
            num: $this->amountInFractionalUnit * $factor,
            mode: $rounding,
        );

        return self::ofSub(intval($result), $this->currency);
    }

    /**
     * @phpstan-param self::ROUND_* $rounding
     */
    public function divide(
        int|float $divisor,
        int $rounding = self::ROUND_HALF_UP,
    ): self {
        $result = round(
            num: $this->amountInFractionalUnit / $divisor,
            mode: $rounding,
        );

        return self::ofSub(intval($result), $this->currency);
    }

    public function changeToPositive(): self
    {
        return self::ofSub(abs($this->amountInFractionalUnit), $this->currency);
    }

    public function changeToNegative(): self
    {
        return self::ofSub(-1 * $this->amountInFractionalUnit, $this->currency);
    }

    public function amountInMainUnit(): float
    {
        return $this->amountInFractionalUnit / $this->currency->fraction();
    }

    private function compare(self $that): int
    {
        if (! $this->comparable($that)) {
            throw new MismatchCurrencies($this, $that);
        }

        return $this->amountInFractionalUnit <=> $that->amountInFractionalUnit;
    }
}
