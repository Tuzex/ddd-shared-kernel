<?php

declare(strict_types=1);

namespace Tuzex\Ddd\SharedKernel\Test\Infrastructure\Persistence\Doctrine\Dbal\Type\Money;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use stdClass;
use Tuzex\Ddd\SharedKernel\Domain\Money\Currency;
use Tuzex\Ddd\SharedKernel\Domain\Money\Currency\Euro;
use Tuzex\Ddd\SharedKernel\Domain\Money\Currency\UsDollar;
use Tuzex\Ddd\SharedKernel\Infrastructure\Persistence\Doctrine\Dbal\Type\Money\CurrencyType;
use Tuzex\Ddd\SharedKernel\Test\Infrastructure\Persistence\Doctrine\Dbal\Type\TypeTest;

final class CurrencyTypeTest extends TypeTest
{
    /**
     * @dataProvider provideCurrencies
     */
    public function testItConvertsCurrencyToCurrencyCode(Currency $currency): void
    {
        $currencyType = $this->getType();
        $currencyCode = $currencyType->convertToDatabaseValue($currency, $this->mockPlatform());

        $this->assertSame($currency->code(), $currencyCode);
    }

    public function provideCurrencies(): array
    {
        return [
            Euro::class => [new Euro()],
            UsDollar::class => [new UsDollar()],
        ];
    }

    /**
     * @dataProvider provideCurrencyCodes
     */
    public function testItConvertsSupportedCurrencyCodeToCurrency(string $currencyCode): void
    {
        $currencyType = $this->getType();
        $currency = $currencyType->convertToPHPValue($currencyCode, $this->mockPlatform());

        $this->assertSame($currencyCode, $currency->code());
    }

    public function provideCurrencyCodes(): array
    {
        return [
            Euro::class => ['EUR'],
            UsDollar::class => ['USD'],
        ];
    }

    public function testItThrowsExceptionIfCurrencyCodeIsNotSupported(): void
    {
        $currencyType = $this->getType();
        $unsupportedCurrencyCode = 'XXX';

        $this->expectException(ConversionException::class);

        $currencyType->convertToPHPValue($unsupportedCurrencyCode, $this->mockPlatform());
    }

    public function testItThrowsExceptionIfDatabaseValueIsNotString(): void
    {
        $currencyType = $this->getType();
        $unsupportedDatabaseValue = new stdClass();

        $this->expectException(ConversionException::class);

        $currencyType->convertToPHPValue($unsupportedDatabaseValue, $this->mockPlatform());
    }

    public function testItThrowsExceptionIfPhpValueIsNotCurrencyObject(): void
    {
        $currencyType = $this->getType();
        $currencyCode = 'EUR';

        $this->expectException(ConversionException::class);

        $currencyType->convertToDatabaseValue($currencyCode, $this->mockPlatform());
    }

    protected function getType(): CurrencyType
    {
        return new CurrencyType();
    }

    protected function getTypeName(): string
    {
        return 'tuzex.currency';
    }

    protected function mockPlatform(): AbstractPlatform
    {
        return $this->createMock(AbstractPlatform::class);
    }
}
