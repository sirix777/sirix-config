<?php

declare(strict_types=1);

use Laminas\Stdlib\ArrayUtils;
use Laminas\Stdlib\Glob;

if (! \function_exists('loadConfigFromGlob')) {
    function loadConfigFromGlob(string $globPattern): array
    {
        $config = [];
        $files  = Glob::glob($globPattern, Glob::GLOB_BRACE);

        foreach ($files as $file) {
            if (! str_ends_with($file, '.php') || ! file_exists($file)) {
                continue;
            }

            $config = ArrayUtils::merge($config, include $file);
        }

        return $config;
    }
}

if (! \function_exists('env')) {
    function env(string $key, mixed $default = null, ?string $type = null): mixed
    {
        $value = getenv($key);

        if ($value === false) {
            return $default;
        }

        if ($type !== null) {
            return castEnvValue($value, $type);
        }

        return parseEnvVar($value);
    }
}

if (! \function_exists('castEnvValue')) {
    function castEnvValue(string $value, string $type): mixed
    {
        $trimmedValue = trim($value);

        return match (strtolower($type)) {
            'int', 'integer' => (int) $trimmedValue,
            'float', 'double' => (float) $trimmedValue,
            'string' => $trimmedValue,
            'bool', 'boolean' => match (strtolower($trimmedValue)) {
                'true', '(true)', '1' => true,
                default => false,
            },
            'array' => $trimmedValue === '' ? [] : explode(',', $trimmedValue),
            default => parseEnvVar($value),
        };
    }
}

if (! \function_exists('parseEnvVar')) {
    function parseEnvVar(string $value): string|int|bool|null
    {
        $trimmedValue = trim($value);
        return match (strtolower($trimmedValue)) {
            'true', '(true)' => true,
            'false', '(false)' => false,
            'empty', '(empty)' => '',
            'null', '(null)' => null,
            default => is_numeric($trimmedValue) ? (int) $trimmedValue : $trimmedValue,
        };
    }
}

if (! \function_exists('formatEnvVarValueOrNull')) {
    /**
     * @param string|string[]|int|int[]|bool|null $value
     */
    function formatEnvVarValueOrNull(string|int|bool|array|null $value): string|null
    {
        $isArray = is_array($value);
        if (! $isArray && ! is_scalar($value)) {
            return null;
        }

        return $isArray ? implode(',', $value) : match ($value) {
            true => 'true',
            false => 'false',
            default => (string) $value,
        };
    }
}

if (! \function_exists('formatEnvVarValue')) {
    /**
     * @param string|string[]|int|int[]|bool|null $value
     */
    function formatEnvVarValue(string|int|bool|array|null $value): string
    {
        return formatEnvVarValueOrNull($value) ?? '';
    }
}

if (! \function_exists('loadEnvVarsFromConfig')) {
    /**
     * Loads config from $configPath, then puts all its values as env vars if they are not yet defined
     */
    function loadEnvVarsFromConfig(string $configPath, array|null $allowedEnvVars = null): void
    {
        $config = loadConfigFromGlob($configPath);
        foreach ($config as $envVar => $value) {
            if ($allowedEnvVars !== null && ! in_array($envVar, $allowedEnvVars, true)) {
                continue;
            }

            putNotYetDefinedEnv($envVar, $value);
        }
    }
}

if (! \function_exists('putNotYetDefinedEnv')) {
    function putNotYetDefinedEnv(string $key, mixed $value): void
    {
        if (env($key) !== null) {
            return;
        }

        $formattedValue = formatEnvVarValueOrNull($value);
        if ($formattedValue === null) {
            return;
        }

        putenv(sprintf('%s=%s', $key, $formattedValue));
    }
}
