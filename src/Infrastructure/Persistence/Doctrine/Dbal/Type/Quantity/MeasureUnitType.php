<?php

declare(strict_types=1);

namespace Tuzex\Ddd\SharedKernel\Infrastructure\Persistence\Doctrine\Dbal\Type\Quantity;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use Tuzex\Ddd\SharedKernel\Domain\Money\Currency;
use Tuzex\Ddd\SharedKernel\Domain\Quantity\MeasureUnit;
use Tuzex\Ddd\SharedKernel\Domain\Quantity\Unit\Gram;
use Tuzex\Ddd\SharedKernel\Domain\Quantity\Unit\Kilogram;
use Tuzex\Ddd\SharedKernel\Domain\Quantity\Unit\Liter;
use Tuzex\Ddd\SharedKernel\Domain\Quantity\Unit\Meter;
use Tuzex\Ddd\SharedKernel\Domain\Quantity\Unit\Mililiter;
use Tuzex\Ddd\SharedKernel\Domain\Quantity\Unit\Milimeter;
use Tuzex\Ddd\SharedKernel\Domain\Quantity\Unit\Piece;

final class MeasureUnitType extends Type
{
    public const NAME = 'tuzex.measure_unit';

    public const UNIT_MAP = [
        'pc' => Piece::class,
        'g' => Gram::class,
        'kg' => Kilogram::class,
        'l' => Liter::class,
        'ml' => Mililiter::class,
        'm' => Meter::class,
        'mm' => Milimeter::class,
    ];

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        $column['length'] = 3;

        return $platform->getVarcharTypeDeclarationSQL($column);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (! $value instanceof MeasureUnit
            || ! $this->supportsMeasureUnit($value)
        ) {
            throw ConversionException::conversionFailedInvalidType($value, MeasureUnit::class, self::UNIT_MAP);
        }

        return $value->symbol;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): MeasureUnit
    {
        if (! is_string($value)
            || ! $this->supportsMeasureUnitSymbol($value)
        ) {
            throw ConversionException::conversionFailedInvalidType($value, Currency::class, self::UNIT_MAP);
        }

        return new (self::UNIT_MAP[$value])();
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

    private function supportsMeasureUnit(MeasureUnit $measureUnit): bool
    {
        return $this->supportsMeasureUnitSymbol($measureUnit->symbol);
    }

    private function supportsMeasureUnitSymbol(string $symbol): bool
    {
        return array_key_exists($symbol, self::UNIT_MAP);
    }
}
