<?php

declare(strict_types=1);

namespace Tuzex\Ddd\SharedKernel\Domain\Quantity\Unit;

use Tuzex\Ddd\SharedKernel\Domain\Quantity\MeasureUnit;

final class Meter extends MeasureUnit
{
    public function __construct()
    {
        parent::__construct('m', 2);
    }
}
