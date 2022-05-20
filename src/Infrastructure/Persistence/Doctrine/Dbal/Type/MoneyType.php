<?php

declare(strict_types=1);

namespace Tuzex\Ddd\SharedKernel\Infrastructure\Persistence\Doctrine\Dbal\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\JsonType;
use Tuzex\Ddd\SharedKernel\Domain\Money;
use Tuzex\Ddd\SharedKernel\Domain\Money\Currency\Euro;
use Tuzex\Ddd\SharedKernel\Domain\Money\Currency\UsDollar;
use Tuzex\Ddd\SharedKernel\Infrastructure\Persistence\Doctrine\Dbal\Type\Money\CurrencyType;

final class MoneyType extends JsonType
{
    public const NAME = 'tuzex.money';

    public const CURRENCY_MAP = [
        'EUR' => Euro::class,
        'USD' => UsDollar::class,
    ];

    public function getName(): string
    {
        return self::NAME;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        $currencyType = new CurrencyType();

        if (! $value instanceof Money
            || ! $currencyType->supportsCurrency($value->currency)
        ) {
            throw ConversionException::conversionFailedInvalidType($value, Money::class, self::CURRENCY_MAP);
        }

        return parent::convertToDatabaseValue([
            'main_amount' => $value->amountInMainUnit,
            'fractional_amount' => $value->amountInFractionalUnit,
            'currency_code' => $currencyType->convertToDatabaseValue(
                $value->currency,
                $platform
            ),
        ], $platform);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): Money
    {
        $currencyType = new CurrencyType();
        $value = parent::convertToPHPValue($value, $platform);

        if (! is_array($value)
            || ! $currencyType->supportsCurrencyCode($value['currency_code'])
        ) {
            throw ConversionException::conversionFailedInvalidType($value, Money::class, self::CURRENCY_MAP);
        }

        return new Money(
            floatval($value['main_unit']),
            $currencyType->convertToPHPValue(
                $value['currency_code'],
                $platform
            )
        );
    }

    public function getMappedDatabaseTypes(AbstractPlatform $platform): array
    {
        return [self::NAME];
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
