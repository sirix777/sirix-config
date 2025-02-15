<?php

declare(strict_types=1);

namespace Sirix\Config\Exception;

use InvalidArgumentException as SplInvalidArgumentException;

class InvalidArgumentException extends SplInvalidArgumentException implements ExceptionInterface {}
