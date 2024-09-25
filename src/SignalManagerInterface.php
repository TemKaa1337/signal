<?php

declare(strict_types=1);

namespace Temkaa\Signal;

/**
 * @psalm-api
 */
interface SignalManagerInterface
{
    public function getSubscriber(int $signal): SignalSubscriberInterface;

    public function hasSubscriber(int $signal): bool;

    /**
     * @param SignalSubscriberInterface $subscriber
     * @param int|list<int>             $signal
     */
    public function subscribe(SignalSubscriberInterface $subscriber, int|array $signal): void;

    public function unsubscribe(int $signal): void;
}
