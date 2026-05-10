# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).


## [3.0.0] - 2026-05-10

### Changed

- **BREAKING**: All functions are now declared in the global namespace instead of `Sirix\Config`. Remove any `use function Sirix\Config\...` imports from your code.
- All functions are wrapped in `function_exists()` checks to allow overriding in consuming projects.
- `env()` now accepts an optional third parameter `$type` for explicit type casting (`int`, `float`, `string`, `bool`, `array`).

#### Migration

- Remove `use function Sirix\Config\env;` and similar imports — functions are now globally available.
- If you had your own `env()` function, wrap it in `function_exists()` or adjust accordingly.
- Existing `env()` calls without the `$type` parameter work exactly as before.

## [2.0.0] - 2025-11-22

### Changed
- BREAKING: Overhauled Valinor mapping cache system:
  - Caching is now opt-in and disabled by default.
  - Default cache directory is `data/cache/valinor`.
  - New `development_mode` enables file‑watching cache to auto‑invalidate when source files change.
  - Cache configuration is now under `sirix_config.valinor` in your application config (see README for details).

#### Migration
- If you relied on implicit caching, enable it explicitly in your config.
- Ensure the process can write to the configured `cache_directory`.
- Clear any previous cache directory or migrate as needed.
- Consider enabling `development_mode` during development to reduce manual cache clears.
