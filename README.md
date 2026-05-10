# Sirix config

Utils to load, parse and work with configuration on Mezzio projects.

This library is **based on** the [`shlinkio/shlink-config`](https://github.com/shlinkio/shlink-config) project, with some modifications and extensions for our specific requirements.

---

## Installation


```bash
composer install sirix/sirix-config
```

> This library is also a mezzio module which provides its own `ConfigProvider`. Add it to your configuration to get everything automatically set up.

## Included utils

### Global functions

All functions are available globally (no `use function` needed):

* `env(string $key, mixed $default = null, ?string $type = null): mixed` — Read an environment variable with optional type casting.
* `parseEnvVar(string $value): string|int|bool|null` — Parse a raw env value (handles `true`, `false`, `null`, `empty`, numeric).
* `castEnvValue(string $value, string $type): mixed` — Cast a raw env string to the specified type.
* `formatEnvVarValue(mixed $value): string` — Format a value for use as an env var string.
* `formatEnvVarValueOrNull(mixed $value): string|null` — Same as above, returns `null` for non-scalar/non-array values.
* `loadConfigFromGlob(string $globPattern): array` — Load and merge all PHP config files matching a glob pattern.
* `loadEnvVarsFromConfig(string $configPath, ?array $allowedEnvVars = null): void` — Load config and set env vars from it.
* `putNotYetDefinedEnv(string $key, mixed $value): void` — Set an env var only if it is not already defined.

#### `env()` function

```php
// Without type (auto-detect: bool, int, string, null)
env('DEBUG');              // true
env('PORT');               // 8080
env('NAME', 'default');    // 'default' if NAME is not set

// With explicit type casting
env('PORT', '8080', 'int');       // (int) 8080
env('RATE', '3.14', 'float');     // (float) 3.14
env('NAME', 'foo', 'string');     // 'foo'
env('DEBUG', 'true', 'bool');     // true
env('HOSTS', 'a,b,c', 'array');   // ['a', 'b', 'c']
```

Supported types: `int`/`integer`, `float`/`double`, `string`, `bool`/`boolean`, `array` (comma-separated).

### Config providers and factories

* `EnvVarLoaderProvider`: A config provider which loads the entries of the loaded config into env vars and always returns empty. Designed to be the first config provider in the pipeline.
* `DottedAccessConfigAbstractFactory`: An abstract factory that lets any config param to be fetched as a service by using the `config.foo.bar` notation.
* `ValinorConfigFactory`: A PSR-11 factory that lets you map arbitrary objects from arrays, using [cuyz/valinor](https://github.com/CuyZ/Valinor).

    In order to use it, you have to register the object to map as a service, and the ValinorConfigFactory with static access using the service that returns the raw array with the data as the static method name:

    ```php
    <?php

    declare(strict_types=1);

    return [
        MyCoolOptions::class => [ValinorConfigFactory::class, 'config.foo.options'],
    ];
    ```

    It is useful to combine this factory with the `DottedAccessConfigAbstractFactory`.

### Valinor cache configuration

`ValinorConfigFactory` supports an optional filesystem cache, configurable under the `sirix_config.valinor` key in your application config. Options:

- `cache` (bool): Enable/disable Valinor mapping cache. Default: `false`.
- `cache_directory` (string): Directory where cache files are stored. Default: `data/cache/valinor`.
- `development_mode` (bool): When `true`, enables file-watching cache to auto‑invalidate entries when source files change. Default: `false`.

Example config:

```php
<?php

declare(strict_types=1);

return [
    'sirix_config' => [
        'valinor' => [
            'cache' => true,
            'cache_directory' => 'data/cache/valinor',
            'development_mode' => true,
        ],
    ],
];
```
