<?php

declare(strict_types=1);

namespace Temkaa\Signal\Exception;

use InvalidArgumentException;

final class InvalidConfigurationException extends InvalidArgumentException implements SignalExceptionInterface
{
}
