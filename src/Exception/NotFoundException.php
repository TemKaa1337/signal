<?php

declare(strict_types=1);

namespace Temkaa\Signal\Exception;

use InvalidArgumentException;

final class NotFoundException extends InvalidArgumentException implements SignalExceptionInterface
{
}
