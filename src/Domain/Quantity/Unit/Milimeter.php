<?php

declare(strict_types=1);

namespace Tuzex\Ddd\SharedKernel\Domain\Quantity\Unit;

use Tuzex\Ddd\SharedKernel\Domain\Quantity\MeasureUnit;

final class Milimeter extends MeasureUnit
{
    public function __construct()
    {
        parent::__construct('mm', 0);
    }
}
