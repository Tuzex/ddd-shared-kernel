<?php

declare(strict_types=1);

namespace Tuzex\Ddd\SharedKernel\Test\Infrastructure\Persistence\Doctrine\Dbal\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use PHPUnit\Framework\TestCase;

abstract class TypeTest extends TestCase
{
    public function testItReturnsName(): void
    {
        $type = $this->getType();

        $this->assertSame($this->getTypeName(), $type->getName());
    }

    public function testItReturnsArrayOfMappedTypes(): void
    {
        $type = $this->getType();

        $mappedDatabaseTypes = $type->getMappedDatabaseTypes($this->mockPlatform());

        $this->assertCount(1, $mappedDatabaseTypes);
        $this->assertContains($this->getTypeName(), $mappedDatabaseTypes);
    }

    public function testItRequiresSqlCommentHint(): void
    {
        $type = $this->getType();

        $this->assertTrue(
            $type->requiresSQLCommentHint($this->mockPlatform())
        );
    }

    abstract protected function getType(): Type;

    abstract protected function getTypeName(): string;

    abstract protected function mockPlatform(): AbstractPlatform;
}
