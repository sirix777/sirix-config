<?php

declare(strict_types=1);

namespace Sirix\Config;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                'abstract_factories' => [
                    Factory\DottedAccessConfigAbstractFactory::class,
                ],
            ],
        ];
    }
}
