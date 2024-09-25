<?php

declare(strict_types=1);

namespace Temkaa\Signal;

/**
 * @psalm-api
 */
interface SignalSubscriberInterface
{
    public function handle(): void;
}
