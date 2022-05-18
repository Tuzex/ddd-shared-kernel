<?php

declare(strict_types=1);

namespace Tuzex\Ddd\SharedKernel\Test\Infrastructure\Persistence\Doctrine\Dbal\Type\Quantity;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use stdClass;
use Tuzex\Ddd\SharedKernel\Domain\Quantity\MeasureUnit;
use Tuzex\Ddd\SharedKernel\Domain\Quantity\Unit\Gram;
use Tuzex\Ddd\SharedKernel\Domain\Quantity\Unit\Kilogram;
use Tuzex\Ddd\SharedKernel\Domain\Quantity\Unit\Liter;
use Tuzex\Ddd\SharedKernel\Domain\Quantity\Unit\Meter;
use Tuzex\Ddd\SharedKernel\Domain\Quantity\Unit\Mililiter;
use Tuzex\Ddd\SharedKernel\Domain\Quantity\Unit\Milimeter;
use Tuzex\Ddd\SharedKernel\Domain\Quantity\Unit\Piece;
use Tuzex\Ddd\SharedKernel\Infrastructure\Persistence\Doctrine\Dbal\Type\Quantity\MeasureUnitType;
use Tuzex\Ddd\SharedKernel\Test\Infrastructure\Persistence\Doctrine\Dbal\Type\TypeTest;

final class MeasureUnitTypeTest extends TypeTest
{
    /**
     * @dataProvider provideMeasureUnits
     */
    public function testItConvertsMeasureUnitToMeasureUnitSymbol(MeasureUnit $measureUnit): void
    {
        $measureUnitType = $this->getType();
        $measureUnitSymbol = $measureUnitType->convertToDatabaseValue($measureUnit, $this->mockPlatform());

        $this->assertSame($measureUnit->symbol, $measureUnitSymbol);
    }

    public function provideMeasureUnits(): array
    {
        return [
            Gram::class => [new Gram()],
            Kilogram::class => [new Kilogram()],
            Liter::class => [new Liter()],
            Meter::class => [new Meter()],
            Mililiter::class => [new Mililiter()],
            Milimeter::class => [new Milimeter()],
            Piece::class => [new Piece()],
        ];
    }

    /**
     * @dataProvider provideMeasureUnitSymbols
     */
    public function testItConvertsSupportedMeasureUnitToMeasureUnit(string $measureUnitSymbol): void
    {
        $measureUnitType = $this->getType();
        $measureUnit = $measureUnitType->convertToPHPValue($measureUnitSymbol, $this->mockPlatform());

        $this->assertSame($measureUnitSymbol, $measureUnit->symbol);
    }

    public function provideMeasureUnitSymbols(): array
    {
        return [
            Gram::class => ['g'],
            Kilogram::class => ['kg'],
            Liter::class => ['l'],
            Meter::class => ['m'],
            Mililiter::class => ['ml'],
            Milimeter::class => ['mm'],
            Piece::class => ['pc'],
        ];
    }

    public function testItThrowsExceptionIfMeasureUnitIsNotSupported(): void
    {
        $measureUnitType = $this->getType();
        $unsupportedMeasureUnit = 'xx';

        $this->expectException(ConversionException::class);

        $measureUnitType->convertToPHPValue($unsupportedMeasureUnit, $this->mockPlatform());
    }

    public function testItThrowsExceptionIfDatabaseValueIsNotString(): void
    {
        $measureUnitType = $this->getType();
        $unsupportedDatabaseValue = new stdClass();

        $this->expectException(ConversionException::class);

        $measureUnitType->convertToPHPValue($unsupportedDatabaseValue, $this->mockPlatform());
    }

    public function testItThrowsExceptionIfPhpValueIsNotMeasureUnitObject(): void
    {
        $measureUnitType = $this->getType();
        $measureUnitSymbol = 'l';

        $this->expectException(ConversionException::class);

        $measureUnitType->convertToDatabaseValue($measureUnitSymbol, $this->mockPlatform());
    }

    protected function getType(): MeasureUnitType
    {
        return new MeasureUnitType();
    }

    protected function getTypeName(): string
    {
        return 'tuzex.measure_unit';
    }

    protected function mockPlatform(): AbstractPlatform
    {
        return $this->createMock(AbstractPlatform::class);
    }
}
