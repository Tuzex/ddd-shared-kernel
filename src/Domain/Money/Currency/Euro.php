<?php

declare(strict_types=1);

namespace Tuzex\Ddd\SharedKernel\Domain\Money\Currency;

use Tuzex\Ddd\SharedKernel\Domain\Money\Currency;
use Tuzex\Ddd\SharedKernel\Domain\Money\FractionalUnit;
use Tuzex\Ddd\SharedKernel\Domain\Money\MainUnit;

final class Euro extends Currency
{
    public function __construct()
    {
        parent::__construct(
            new MainUnit('EUR', '€'),
            new FractionalUnit('cent', 'c', 100),
        );
    }
}
