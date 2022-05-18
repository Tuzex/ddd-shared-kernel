<?php

declare(strict_types=1);

namespace Tuzex\Ddd\SharedKernel\Test\Domain;

use PHPUnit\Framework\TestCase;
use Tuzex\Ddd\SharedKernel\Domain\Money;
use Tuzex\Ddd\SharedKernel\Domain\Money\Currency\Euro;
use Tuzex\Ddd\SharedKernel\Domain\Money\Currency\UsDollar;
use Tuzex\Ddd\SharedKernel\Domain\Money\MismatchCurrencies;

final class MoneyTest extends TestCase
{
    /**
     * @dataProvider provideDataForComparison
     */
    public function testItIsComparable(Money $origin, Money $another, bool $result): void
    {
        $this->assertSame($result, $origin->comparable($another));
    }

    public function provideDataForComparison(): iterable
    {
        $value = 1.00;
        $circumstances = [
            'identical' => [$value, new Euro(), $value, new Euro(), true],
            'mismatched' => [$value, new Euro(), $value, new UsDollar(), false],
        ];

        return $this->generateDataForEquality($circumstances);
    }

    /**
     * @dataProvider provideDataForEquality
     */
    public function testItEquals(Money $origin, Money $another, bool $result): void
    {
        $this->assertSame($result, $origin->equals($another));
    }

    public function provideDataForEquality(): iterable
    {
        $circumstances = [
            'identical-monies' => [1.00, new Euro(), 1.00, new Euro(), true],
            'different-nominal-values' => [1.00, new Euro(), 2.00, new Euro(), false],
            'different-currencies' => [1.00, new Euro(), 1.00, new UsDollar(), false],
            'different-monies' => [1.00, new Euro(), 2.00, new UsDollar(), false],
        ];

        return $this->generateDataForEquality($circumstances);
    }

    /**
     * @dataProvider provideDataForComparisonLessThan
     */
    public function testItIsLessThan(Money $origin, Money $another, bool $result): void
    {
        $this->assertSame($result, $origin->lessThan($another));
    }

    public function provideDataForComparisonLessThan(): iterable
    {
        $results = [
            'less-than' => true,
            'equal' => false,
            'greater-than' => false,
        ];

        return $this->generateDataForComparison($results);
    }

    /**
     * @dataProvider provideDataForComparisonLessThanOrEqualTo
     */
    public function testItIsLessThanOrEqualTo(Money $origin, Money $another, bool $result): void
    {
        $this->assertSame($result, $origin->lessThanOrEqualTo($another));
    }

    public function provideDataForComparisonLessThanOrEqualTo(): iterable
    {
        $results = [
            'less-than' => true,
            'equal' => true,
            'greater-than' => false,
        ];

        return $this->generateDataForComparison($results);
    }

    /**
     * @dataProvider provideDataForComparisonGreaterThan
     */
    public function testItIsGreaterThan(Money $origin, Money $another, bool $result): void
    {
        $this->assertSame($result, $origin->greaterThan($another));
    }

    public function provideDataForComparisonGreaterThan(): iterable
    {
        $results = [
            'less-than' => false,
            'equal' => false,
            'greater-than' => true,
        ];

        return $this->generateDataForComparison($results);
    }

    /**
     * @dataProvider provideDataForComparisonGreaterThanOrEqualTo
     */
    public function testItIsGreaterThanOrEqualTo(Money $origin, Money $another, bool $result): void
    {
        $this->assertSame($result, $origin->greaterThanOrEqualTo($another));
    }

    public function provideDataForComparisonGreaterThanOrEqualTo(): iterable
    {
        $results = [
            'less-than' => false,
            'equal' => true,
            'greater-than' => true,
        ];

        return $this->generateDataForComparison($results);
    }

    /**
     * @dataProvider provideDataToCheck
     */
    public function testItChecks(Money $money, array $results): void
    {
        $this->assertSame($results['zero'], $money->equalToZero());
        $this->assertSame($results['positive'], $money->moreThanZero());
        $this->assertSame($results['negative'], $money->lessThanZero());
    }

    public function provideDataToCheck(): iterable
    {
        $circumstances = [
            'zero' => [0, true, false, false],
            'positive' => [1, false, true, false],
            'negative' => [-1, false, false, true],
        ];

        foreach ($circumstances as $type => $data) {
            yield $type => [
                'money' => new Money($data[0], new Euro()),
                'results' => [
                    'zero' => $data[1],
                    'positive' => $data[2],
                    'negative' => $data[3],
                ],
            ];
        }
    }

    /**
     * @dataProvider provideDataWithDifferentCurrencies
     */
    public function testItThrowsExceptionWhenCompareDifferentCurrencies(Money $minuend, Money $subtrahend): void
    {
        $this->expectException(MismatchCurrencies::class);
        $minuend->lessThan($subtrahend);
    }

    /**
     * @dataProvider provideDataWithDifferentCurrencies
     */
    public function testItThrowsExceptionWhenAddingDifferentCurrencies(Money $term, Money $addend): void
    {
        $this->expectException(MismatchCurrencies::class);
        $term->add($addend);
    }

    /**
     * @dataProvider provideDataWithDifferentCurrencies
     */
    public function testItThrowsExceptionWhenSubtractDifferentCurrencies(Money $minuend, Money $subtrahend): void
    {
        $this->expectException(MismatchCurrencies::class);
        $minuend->subtract($subtrahend);
    }

    public function provideDataWithDifferentCurrencies(): array
    {
        $value = 1.25;

        return [
            'different-currencies' => [
                'first' => new Money($value, new Euro()),
                'second' => new Money($value, new UsDollar()),
            ],
        ];
    }

    /**
     * @dataProvider provideDataForAddition
     */
    public function testItAddsUpMonies(Money $term, Money $addend, float $sum): void
    {
        $this->assertSame($sum, $term->add($addend)->amountInMainUnit());
    }

    public function provideDataForAddition(): iterable
    {
        $calculations = [
            'positive-both' => [12.33, 5.18, 17.51],
            'negative-addend' => [12.33, -5.18, 7.15],
            'negative-term' => [-12.33, 5.18, -7.15],
            'negative-both' => [-5.18, -12.33, -17.51],
        ];

        return $this->generateDataForAdditionAndSubtraction($calculations);
    }

    /**
     * @dataProvider provideDataForSubtraction
     */
    public function testItSubtractsMonies(Money $minuend, Money $subtrahend, float $difference): void
    {
        $this->assertSame($difference, $minuend->subtract($subtrahend)->amountInMainUnit());
    }

    public function provideDataForSubtraction(): iterable
    {
        $calculations = [
            'positive-both' => [12.33, 5.18, 7.15],
            'negative-subtrahend' => [12.33, -5.18, 17.51],
            'negative-minuend' => [-12.33, 5.18, -17.51],
            'negative-both' => [-12.33, -5.18, -7.15],
        ];

        return $this->generateDataForAdditionAndSubtraction($calculations);
    }

    /**
     * @dataProvider provideDataForMultiplication
     */
    public function testItMultipliesMonies(Money $multiplier, float $multiplicand, float $product): void
    {
        $this->assertSame($product, $multiplier->multiply($multiplicand)->amountInMainUnit());
    }

    public function provideDataForMultiplication(): iterable
    {
        $calculations = [
            'positive-both' => [12.33, 1.2, 14.80],
            'negative-multiplicand' => [12.33, -1.2, -14.80],
            'negative-multiplier' => [-12.33, 1.7, -20.96],
            'negative-both' => [-12.33, -1.7, 20.96],
        ];

        return $this->generateDataForMultiplicationAndDivision($calculations);
    }

    /**
     * @dataProvider provideDataForDivision
     */
    public function testItDivisionsMonies(Money $dividend, float $divisor, float $quotient): void
    {
        $this->assertSame($quotient, $dividend->divide($divisor)->amountInMainUnit());
    }

    public function provideDataForDivision(): iterable
    {
        $calculations = [
            'positive-both' => [12.33, 1.2, 10.28],
            'negative-divisor' => [12.33, -3.98, -3.1],
            'negative-dividend' => [-12.33, 1.7, -7.25],
            'negative-both' => [-12.33, -2.29, 5.38],
        ];

        return $this->generateDataForMultiplicationAndDivision($calculations);
    }

    /**
     * @dataProvider provideDataForPositiveTransformation
     */
    public function testItChangesMoneyToPositive(Money $origin, float $result): void
    {
        $transformed = $origin->changeToPositive();

        $this->assertSame($result, $transformed->amountInMainUnit());
    }

    public function provideDataForPositiveTransformation(): iterable
    {
        $circumstances = [
            'positive-to-absolute' => [1, 1.00],
            'negative-to-absolute' => [-1, 1.00],
        ];

        foreach ($circumstances as $type => $data) {
            yield $type => [
                'origin' => new Money($data[0], new Euro()),
                'result' => $data[1],
            ];
        }
    }

    /**
     * @dataProvider provideDataForNegativeTransformation
     */
    public function testItChangesMoneyToNegative(Money $origin, float $result): void
    {
        $transformed = $origin->changeToNegative();

        $this->assertSame($result, $transformed->amountInMainUnit());
    }

    public function provideDataForNegativeTransformation(): iterable
    {
        $circumstances = [
            'positive-to-opposite' => [1, -1.00],
            'negative-to-opposite' => [-1, 1.00],
        ];

        foreach ($circumstances as $type => $data) {
            yield $type => [
                'origin' => new Money($data[0], new Euro()),
                'result' => $data[1],
            ];
        }
    }

    private function generateDataForEquality(array $circumstances): iterable
    {
        foreach ($circumstances as $type => $data) {
            yield $type => [
                'origin' => new Money($data[0], $data[1]),
                'another' => new Money($data[2], $data[3]),
                'result' => $data[4],
            ];
        }
    }

    private function generateDataForComparison(array $results): iterable
    {
        $currency = new Euro();
        $circumstances = [
            'less-than' => [1.00, 2.00],
            'equal' => [1.00, 1.00],
            'greater-than' => [2.00, 1.00],
        ];

        foreach ($circumstances as $type => $data) {
            yield $type => [
                'origin' => new Money($data[0], $currency),
                'another' => new Money($data[1], $currency),
                'result' => $results[$type],
            ];
        }
    }

    private function generateDataForAdditionAndSubtraction(array $calculations): iterable
    {
        $currency = new Euro();

        foreach ($calculations as $type => $data) {
            yield $type => [
                'first' => new Money($data[0], $currency),
                'second' => new Money($data[1], $currency),
                'result' => $data[2],
            ];
        }
    }

    private function generateDataForMultiplicationAndDivision(array $calculations): iterable
    {
        foreach ($calculations as $type => $data) {
            yield $type => [
                'first' => new Money($data[0], new Euro()),
                'second' => $data[1],
                'result' => $data[2],
            ];
        }
    }
}
