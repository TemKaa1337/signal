<?php

declare(strict_types=1);

namespace Tests\Fixture;

use Psr\Log\LoggerInterface;
use Stringable;

final class Logger implements LoggerInterface
{
    private array $messages = [];

    public function alert(Stringable|string $message, array $context = []): void
    {
        $this->messages[__FUNCTION__][] = $message;
    }

    public function critical(Stringable|string $message, array $context = []): void
    {
        $this->messages[__FUNCTION__][] = $message;
    }

    public function debug(Stringable|string $message, array $context = []): void
    {
        $this->messages[__FUNCTION__][] = $message;
    }

    public function emergency(Stringable|string $message, array $context = []): void
    {
        $this->messages[__FUNCTION__][] = $message;
    }

    public function error(Stringable|string $message, array $context = []): void
    {
        $this->messages[__FUNCTION__][] = $message;
    }

    public function getMessages(): array
    {
        return $this->messages;
    }

    public function info(Stringable|string $message, array $context = []): void
    {
        $this->messages[__FUNCTION__][] = $message;
    }

    public function log($level, Stringable|string $message, array $context = []): void
    {
        $this->messages[__FUNCTION__][] = $message;
    }

    public function notice(Stringable|string $message, array $context = []): void
    {
        $this->messages[__FUNCTION__][] = $message;
    }

    public function warning(Stringable|string $message, array $context = []): void
    {
        $this->messages[__FUNCTION__][] = $message;
    }
}
