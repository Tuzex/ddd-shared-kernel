<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Option;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ECSConfig $config): void {
    $parameters = $config->parameters();
    $parameters->set(Option::PATHS, [
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);

    $config->import(SetList::COMMON);
    $config->import(SetList::CLEAN_CODE);
    $config->import(SetList::PSR_12);

    $parameters->set(Option::SKIP, [
        PhpCsFixer\Fixer\Basic\BracesFixer::class => null,
    ]);
};
