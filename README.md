### A simple signal subscriber manager implementation.

### Installation
```
composer require temkaa/signal
```

This package automatically detects what OS are you using for both testing and production confidence. For Windows it uses
`sapi_windows_set_ctrl_handler`, for Linux - `pcntl_signal`.  
Package allows you to specify subscribers for specific PHP signals. To use this package you need:
1. If you use Windows, you need to have functions `` and `` enabled;
2. If you use any other OS you need to have `pcntl` extension and functions `pcntl_async_signals`, `pcntl_signal` enabled. 

Example:
```php
<?php

use Temkaa\Signal\SignalSubscriberInterface;
use Temkaa\Signal\SignalManager;
use const PHP_EOL;
use const SIGTERM;

final class SigtermListener implements SignalSubscriberInterface
{
    private bool $isCalled = false;
 
    public function handle(): void
    {
        $this->isCalled = true;

        echo 'Got sigterm signal, closing database connection...'.PHP_EOL;
    }
    
    public function isCalled(): bool
    {
        return $this->isCalled;    
    }
}

$sigtermListener = new SigtermListener();

$signalManager = new SignalManager();
$signalManager->subscribe($sigtermListener, SIGTERM);

posix_kill(posix_getpid(), SIGTERM);

// $sigtermListener->isCalled is true at this point
assert($sigtermListener->isCalled() === true);
```

You can also unregister specific listeners, check if any listener is subscribed to specific signal, etc:
```php
<?php

use Temkaa\Signal\SignalSubscriberInterface;
use Temkaa\Signal\SignalManager;
use const PHP_EOL;
use const SIGTERM;

final class SigtermListener implements SignalSubscriberInterface
{
    public function handle(): void
    {
        echo 'Got sigterm signal, closing database connection...'.PHP_EOL;
    }
}

$sigtermListener = new SigtermListener();

$signalManager = new SignalManager();
$signalManager->subscribe($sigtermListener, SIGTERM);
assert($signalManager->hasSubscriber(SIGTERM) === true);
assert($signalManager->getSubscriber(SIGTERM) === $sigtermListener);

$signalManager->unsubscribe(SIGTERM);
assert($signalManager->hasSubscriber(SIGTERM) === false);
```
