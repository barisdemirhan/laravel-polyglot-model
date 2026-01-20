<?php

declare(strict_types=1);

namespace PolyglotModel;

use Illuminate\Support\Facades\App;

class PolyglotModelManager
{
    /**
     * Get the current application locale.
     */
    public function getCurrentLocale(): string
    {
        return App::getLocale();
    }

    /**
     * Get the source locale from config.
     */
    public function getSourceLocale(): string
    {
        return config('polyglot-model.source_locale', 'en');
    }

    /**
     * Get the fallback locale from config.
     */
    public function getFallbackLocale(): string
    {
        return config('polyglot-model.fallback_locale', 'en');
    }

    /**
     * Get supported locales from config.
     */
    public function getSupportedLocales(): array
    {
        return config('polyglot-model.supported_locales', ['en']);
    }

    /**
     * Check if a locale is supported.
     */
    public function isLocaleSupported(string $locale): bool
    {
        return in_array($locale, $this->getSupportedLocales(), true);
    }

    /**
     * Check if strict locale mode is enabled.
     */
    public function isStrictLocaleMode(): bool
    {
        return config('polyglot-model.strict_locale', false);
    }

    /**
     * Check if caching is enabled.
     */
    public function isCacheEnabled(): bool
    {
        return config('polyglot-model.cache.enabled', true);
    }

    /**
     * Get cache TTL in seconds.
     */
    public function getCacheTtl(): int
    {
        return config('polyglot-model.cache.ttl', 3600);
    }

    /**
     * Get cache key prefix.
     */
    public function getCachePrefix(): string
    {
        return config('polyglot-model.cache.prefix', 'polyglot_');
    }

    /**
     * Check if events are enabled.
     */
    public function areEventsEnabled(): bool
    {
        return config('polyglot-model.events.enabled', true);
    }

    /**
     * Get the Translation model class.
     */
    public function getTranslationModel(): string
    {
        return config('polyglot-model.model', \PolyglotModel\Models\Translation::class);
    }

    /**
     * Get the translations table name.
     */
    public function getTableName(): string
    {
        return config('polyglot-model.table_name', 'translations');
    }
}
