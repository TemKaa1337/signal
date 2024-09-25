<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\Attributes\RequiresOperatingSystemFamily;
use PHPUnit\Framework\TestCase;
use Temkaa\Signal\SignalManager;
use Temkaa\Signal\SignalSubscriberInterface;

final class LinuxSignalManagerTest extends TestCase
{
    #[RequiresOperatingSystemFamily('Linux')]
    public function testDoesNotHaveSubscriber(): void
    {
        $manager = new SignalManager();
        self::assertFalse($manager->hasSubscriber(SIGTERM));
    }

    #[RequiresOperatingSystemFamily('Linux')]
    public function testHasSubscriber(): void
    {
        $manager = new SignalManager();
        $subscriber = new class implements SignalSubscriberInterface {
            public function handle(): void
            {
            }
        };

        $manager->subscribe($subscriber, SIGTERM);
        self::assertTrue($manager->hasSubscriber(SIGTERM));
        self::assertSame($subscriber, $manager->getSubscriber(SIGTERM));
    }

    #[RequiresOperatingSystemFamily('Linux')]
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

        $manager->subscribe($subscriber, SIGTERM);
        self::assertFalse($subscriber->isCalled);

        posix_kill(posix_getpid(), SIGTERM);

        // needed to wait until $subscriber property is set
        usleep(500);
        self::assertTrue($subscriber->isCalled);
    }

    #[RequiresOperatingSystemFamily('Linux')]
    public function testUnsetSubscriber(): void
    {
        $manager = new SignalManager();
        $subscriber = new class implements SignalSubscriberInterface {
            public function handle(): void
            {
            }
        };

        $manager->subscribe($subscriber, SIGTERM);
        self::assertTrue($manager->hasSubscriber(SIGTERM));
        self::assertSame($subscriber, $manager->getSubscriber(SIGTERM));

        $manager->unsubscribe(SIGTERM);
        self::assertFalse($manager->hasSubscriber(SIGTERM));
    }
}
