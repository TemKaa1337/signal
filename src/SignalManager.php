<?php

declare(strict_types=1);

namespace Temkaa\Signal;

use Closure;
use Temkaa\Signal\Exception\NotFoundException;
use Temkaa\Signal\Validator\ConfigurationValidator;

/**
 * @psalm-suppress UndefinedConstant
 *
 * @psalm-api
 */
final class SignalManager implements SignalManagerInterface
{
    /**
     * @var array<int, SignalSubscriberInterface>
     */
    private array $subscribers = [];

    private ?Closure $windowsHandler = null;

    public function __construct()
    {
        (new ConfigurationValidator())->validate();

        if (PHP_OS_FAMILY === 'Windows') {
            $this->windowsHandler = $this->handleSignal(...);

            /** @psalm-suppress UnusedFunctionCall */
            sapi_windows_set_ctrl_handler($this->windowsHandler);
        } else {
            pcntl_async_signals(true);
        }
    }

    public function __destruct()
    {
        if (PHP_OS_FAMILY === 'Windows') {
            /** @psalm-suppress UnusedFunctionCall */
            sapi_windows_set_ctrl_handler($this->windowsHandler, false);

            $this->windowsHandler = null;
        } else {
            foreach (array_keys($this->subscribers) as $signal) {
                /** @psalm-suppress MixedArgument */
                pcntl_signal($signal, SIG_DFL);
            }
        }

        $this->subscribers = [];
    }

    public function getSubscriber(int $signal): SignalSubscriberInterface
    {
        if (!$this->hasSubscriber($signal)) {
            throw new NotFoundException(sprintf('Could not find handler for "%s" signal.', $signal));
        }

        return $this->subscribers[$signal];
    }

    public function handleSignal(int $signal): void
    {
        // Only used for windows signal handling
        foreach ($this->subscribers as $supportedSignal => $subscriber) {
            if ($signal === $supportedSignal) {
                $subscriber->handle();
            }
        }
    }

    public function hasSubscriber(int $signal): bool
    {
        return isset($this->subscribers[$signal]);
    }

    /**
     * @param SignalSubscriberInterface $subscriber
     * @param int|list<int>             $signal
     */
    public function subscribe(SignalSubscriberInterface $subscriber, int|array $signal): void
    {
        $signals = is_int($signal) ? [$signal] : $signal;
        foreach ($signals as $signal) {
            $this->subscribers[$signal] = $subscriber;

            if (PHP_OS_FAMILY !== 'Windows') {
                pcntl_signal($signal, $subscriber->handle(...));
            }
        }
    }

    public function unsubscribe(int $signal): void
    {
        if (!$this->hasSubscriber($signal)) {
            return;
        }

        unset($this->subscribers[$signal]);

        if (PHP_OS_FAMILY !== 'Windows') {
            /** @psalm-suppress MixedArgument */
            pcntl_signal($signal, SIG_DFL);
        }
    }
}
