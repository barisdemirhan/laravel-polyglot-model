<?php

declare(strict_types=1);

namespace PolyglotModel\Exceptions;

use Exception;

class InvalidLocaleException extends Exception
{
    public static function unsupportedLocale(string $locale, array $supportedLocales): self
    {
        $supported = implode(', ', $supportedLocales);

        return new self(
            "Locale '{$locale}' is not supported. Supported locales are: {$supported}."
        );
    }

    public static function emptyLocale(): self
    {
        return new self('Locale cannot be empty.');
    }
}
