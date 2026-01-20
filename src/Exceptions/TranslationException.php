<?php

declare(strict_types=1);

namespace PolyglotModel\Exceptions;

use Exception;

class TranslationException extends Exception
{
    public static function fieldNotTranslatable(string $field, string $modelClass): self
    {
        return new self("Field '{$field}' is not translatable on model '{$modelClass}'.");
    }

    public static function modelNotPersisted(): self
    {
        return new self('Cannot set translation on a model that has not been persisted yet.');
    }

    public static function translationNotFound(string $field, string $locale): self
    {
        return new self("Translation not found for field '{$field}' in locale '{$locale}'.");
    }
}
