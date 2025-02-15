<?php

declare(strict_types=1);

namespace SirixTest\Config\Model;

final class FooModel
{
    public function __construct(
        public readonly string $foo,
        public readonly int $withCamelCase = 30,
        public readonly bool $bar = false,
        /** @var string[] */
        public readonly array $listOfStuff = [],
        /** @var float[] */
        public readonly array $listOfNumbers = [],
    ) {}
}
