<?php

declare(strict_types=1);

namespace Sirix\Config\ConfigAggregator;

use function in_array;
use function Sirix\Config\loadConfigFromGlob;
use function Sirix\Config\putNotYetDefinedEnv;

readonly class EnvVarLoaderProvider
{
    public function __construct(private string $configPath, private ?array $allowedEnvVars = null)
    {
    }

    public function __invoke(): array
    {
        $config = loadConfigFromGlob($this->configPath);
        foreach ($config as $envVar => $value) {
            if ($this->allowedEnvVars !== null && ! in_array($envVar, $this->allowedEnvVars, true)) {
                continue;
            }

            putNotYetDefinedEnv($envVar, $value);
        }

        return [];
    }
}
