<?php

declare(strict_types=1);

namespace Tuzex\Ddd\SharedKernel\Infrastructure\Persistence\Doctrine\Dbal\Type\Money;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use Tuzex\Ddd\SharedKernel\Domain\Money\Currency;
use Tuzex\Ddd\SharedKernel\Domain\Money\Currency\Euro;
use Tuzex\Ddd\SharedKernel\Domain\Money\Currency\UsDollar;

final class CurrencyType extends Type
{
    public const NAME = 'tuzex.currency';

    public const CURRENCY_MAP = [
        'EUR' => Euro::class,
        'USD' => UsDollar::class,
    ];

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        $column['length'] = 3;
        $column['fixed'] = true;

        return $platform->getVarcharTypeDeclarationSQL($column);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        if (! $value instanceof Currency
            || ! $this->supportsCurrency($value)
        ) {
            throw ConversionException::conversionFailedInvalidType($value, Currency::class, self::CURRENCY_MAP);
        }

        return $value->code();
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): Currency
    {
        if (! is_string($value)
            || ! $this->supportsCurrencyCode($value)
        ) {
            throw ConversionException::conversionFailedInvalidType($value, Currency::class, self::CURRENCY_MAP);
        }

        return new (self::CURRENCY_MAP[$value])();
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function getMappedDatabaseTypes(AbstractPlatform $platform): array
    {
        return [self::NAME];
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }

    private function supportsCurrency(Currency $currency): bool
    {
        return $this->supportsCurrencyCode($currency->code());
    }

    private function supportsCurrencyCode(string $code): bool
    {
        return array_key_exists($code, self::CURRENCY_MAP);
    }
}
