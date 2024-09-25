<?php

declare(strict_types=1);

namespace Temkaa\Signal\Validator;

use Temkaa\Signal\Exception\InvalidConfigurationException;

/**
 * @internal
 */
final readonly class ConfigurationValidator
{
    public function validate(): void
    {
        $disabledFunctions = explode(',', ini_get('disable_functions'));

        PHP_OS_FAMILY === 'Windows'
            ? $this->validateWindowsRequirements($disabledFunctions)
            : $this->validateUnixRequirements($disabledFunctions);
    }

    private function validateUnixRequirements(array $disabledFunctions): void
    {
        if (!extension_loaded('pcntl')) {
            throw new InvalidConfigurationException(
                'Cannot use SignalManager without "pcntl" extension.',
            );
        }

        foreach (['pcntl_async_signals', 'pcntl_signal'] as $requiredFunction) {
            if (in_array($requiredFunction, $disabledFunctions, strict: true)) {
                throw new InvalidConfigurationException(
                    'Cannot use SignalManager as "pcntl_async_signals" or "pcntl_signal" function is disabled.',
                );
            }
        }
    }

    private function validateWindowsRequirements(array $disabledFunctions): void
    {
        if (PHP_SAPI !== 'cli') {
            throw new InvalidConfigurationException('SignalManager can be used on windows only in CLI mode.');
        }

        if (!function_exists('sapi_windows_set_ctrl_handler')) {
            throw new InvalidConfigurationException(
                'Cannot use SignalManager without "sapi_windows_set_ctrl_handler" function.',
            );
        }

        if (in_array('sapi_windows_set_ctrl_handler', $disabledFunctions, strict: true)) {
            throw new InvalidConfigurationException(
                'Cannot use SignalManager as "sapi_windows_set_ctrl_handler" function is disabled.',
            );
        }
    }
}
