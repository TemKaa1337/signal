<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\Attributes\RequiresOperatingSystemFamily;
use PHPUnit\Framework\TestCase;
use Temkaa\Signal\SignalManager;
use Temkaa\Signal\SignalSubscriberInterface;

final class WindowsSignalManagerTest extends TestCase
{
    #[RequiresOperatingSystemFamily('Windows')]
    public function testDoesNotHaveSubscriber(): void
    {
        $manager = new SignalManager();
        self::assertFalse($manager->hasSubscriber(PHP_WINDOWS_EVENT_CTRL_C));
    }

    #[RequiresOperatingSystemFamily('Windows')]
    public function testHasSubscriber(): void
    {
        $manager = new SignalManager();
        $subscriber = new class implements SignalSubscriberInterface {
            public function handle(): void
            {
            }
        };

        $manager->subscribe($subscriber, PHP_WINDOWS_EVENT_CTRL_C);
        self::assertTrue($manager->hasSubscriber(PHP_WINDOWS_EVENT_CTRL_C));
        self::assertSame($subscriber, $manager->getSubscriber(PHP_WINDOWS_EVENT_CTRL_C));
    }

    #[RequiresOperatingSystemFamily('Windows')]
    public function testSubscriberIsCalled(): void
    {
        $manager = new SignalManager();
        $subscriber = new class implements SignalSubscriberInterface {
            public bool $isCalled = false;

            public function handle(): void
            {
                $this->isCalled = true;
            }
        };

        $manager->subscribe($subscriber, PHP_WINDOWS_EVENT_CTRL_BREAK);
        self::assertFalse($subscriber->isCalled);

        sapi_windows_generate_ctrl_event(PHP_WINDOWS_EVENT_CTRL_BREAK);

        // needed to wait until $subscriber property is set
        usleep(500);
        self::assertTrue($subscriber->isCalled);
    }

    #[RequiresOperatingSystemFamily('Windows')]
    public function testUnsetSubscriber(): void
    {
        $manager = new SignalManager();
        $subscriber = new class implements SignalSubscriberInterface {
            public function handle(): void
            {
            }
        };

        $manager->subscribe($subscriber, PHP_WINDOWS_EVENT_CTRL_C);
        self::assertTrue($manager->hasSubscriber(PHP_WINDOWS_EVENT_CTRL_C));
        self::assertSame($subscriber, $manager->getSubscriber(PHP_WINDOWS_EVENT_CTRL_C));

        $manager->unsubscribe(PHP_WINDOWS_EVENT_CTRL_C);
        self::assertFalse($manager->hasSubscriber(PHP_WINDOWS_EVENT_CTRL_C));
    }
}
