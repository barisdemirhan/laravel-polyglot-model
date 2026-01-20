<?php

declare(strict_types=1);

namespace PolyglotModel\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use PolyglotModel\Exceptions\InvalidLocaleException;
use PolyglotModel\Exceptions\TranslationException;

/**
 * Trait HasTranslations
 *
 * Provides translation capabilities to Eloquent models using polymorphic relationships.
 *
 * @property array $translatableFields Array of field names that can be translated
 * @property array $requiredTranslatableFields Array of field names required for complete translation
 * @property bool $translationExcluded Whether this model instance is excluded from translation
 */
trait HasTranslations
{
    /**
     * The preferred language for this model instance.
     */
    protected ?string $preferredLanguage = null;

    /**
     * Cached translations for this request.
     *
     * @var array<string, array<string, string>>
     */
    protected array $translationsCache = [];

    /**
     * Boot the HasTranslations trait.
     */
    protected static function bootHasTranslations(): void
    {
        static::saved(function ($model) {
            $model->clearTranslationCache();
        });

        static::deleted(function ($model) {
            $model->translations()->delete();
            $model->clearTranslationCache();
        });
    }

    /**
     * Get the translations relationship.
     */
    public function translations(): MorphMany
    {
        $translationModel = config('polyglot-model.model');

        return $this->morphMany($translationModel, 'translatable');
    }

    /**
     * Get translatable fields for this model.
     *
     * @return array<string>
     */
    public function getTranslatableFields(): array
    {
        return $this->translatableFields ?? [];
    }

    /**
     * Get required translatable fields for this model.
     *
     * @return array<string>
     */
    public function getRequiredTranslatableFields(): array
    {
        return $this->requiredTranslatableFields ?? $this->getTranslatableFields();
    }

    /**
     * Check if a field is translatable.
     */
    public function isFieldTranslatable(string $field): bool
    {
        return in_array($field, $this->getTranslatableFields(), true);
    }

    /**
     * Check if this model is excluded from translation.
     */
    public function isTranslationExcluded(): bool
    {
        return $this->translationExcluded ?? false;
    }

    /**
     * Set the preferred language for this model instance.
     *
     * @return $this
     */
    public function setPreferredLanguage(string $locale): static
    {
        $this->preferredLanguage = $locale;

        return $this;
    }

    /**
     * Get the preferred language for this model instance.
     */
    public function getPreferredLanguage(): ?string
    {
        return $this->preferredLanguage;
    }

    /**
     * Get the current locale to use for translations.
     */
    protected function getCurrentLocale(): string
    {
        return $this->preferredLanguage ?? App::getLocale();
    }

    /**
     * Get the source locale from config.
     */
    protected function getSourceLocale(): string
    {
        return config('polyglot-model.source_locale', 'en');
    }

    /**
     * Get the fallback locale from config.
     */
    protected function getFallbackLocale(): string
    {
        return config('polyglot-model.fallback_locale', 'en');
    }

    /**
     * Get supported locales from config.
     *
     * @return array<string>
     */
    protected function getSupportedLocales(): array
    {
        return config('polyglot-model.supported_locales', ['en']);
    }

    /**
     * Check if a locale is supported.
     */
    protected function isLocaleSupported(string $locale): bool
    {
        return in_array($locale, $this->getSupportedLocales(), true);
    }

    /**
     * Validate a locale.
     *
     * @throws InvalidLocaleException
     */
    protected function validateLocale(string $locale): void
    {
        if (empty($locale)) {
            throw InvalidLocaleException::emptyLocale();
        }

        if (! $this->isLocaleSupported($locale)) {
            if (config('polyglot-model.strict_locale', false)) {
                throw InvalidLocaleException::unsupportedLocale($locale, $this->getSupportedLocales());
            }

            Log::warning("Unsupported locale '{$locale}' used for translation.", [
                'model' => static::class,
                'model_id' => $this->getKey(),
                'supported_locales' => $this->getSupportedLocales(),
            ]);
        }
    }

    /**
     * Get translation for a specific field and locale.
     */
    public function getTranslate(string $field, ?string $locale = null): ?string
    {
        if ($this->isTranslationExcluded()) {
            return $this->getOriginalAttributeValue($field);
        }

        $locale = $locale ?? $this->getCurrentLocale();

        // Return original value if source locale
        if ($locale === $this->getSourceLocale()) {
            return $this->getOriginalAttributeValue($field);
        }

        // Try to get from cache first
        $cached = $this->getFromCache($field, $locale);
        if ($cached !== null) {
            return $cached;
        }

        // Try to get from loaded relationship
        if ($this->relationLoaded('translations')) {
            $translation = $this->translations
                ->where('field', $field)
                ->where('locale', $locale)
                ->first();

            if ($translation) {
                $this->setToCache($field, $locale, $translation->value);

                return $translation->value;
            }
        } else {
            // Query the database
            $translation = $this->translations()
                ->where('field', $field)
                ->where('locale', $locale)
                ->first();

            if ($translation) {
                $this->setToCache($field, $locale, $translation->value);

                return $translation->value;
            }
        }

        // Try fallback locale
        if ($locale !== $this->getFallbackLocale()) {
            $fallback = $this->getTranslate($field, $this->getFallbackLocale());
            if ($fallback !== null) {
                return $fallback;
            }
        }

        // Return original value
        return $this->getOriginalAttributeValue($field);
    }

    /**
     * Set translation for a specific field and locale.
     *
     * @throws TranslationException
     * @throws InvalidLocaleException
     */
    public function setTranslate(string $field, string $locale, ?string $value): void
    {
        if (! $this->exists) {
            throw TranslationException::modelNotPersisted();
        }

        if (! $this->isFieldTranslatable($field)) {
            throw TranslationException::fieldNotTranslatable($field, static::class);
        }

        $this->validateLocale($locale);

        // Don't store translation for source locale
        if ($locale === $this->getSourceLocale()) {
            $this->setAttribute($field, $value);
            $this->save();

            return;
        }

        $translationModel = config('polyglot-model.model');

        if ($value === null) {
            // Delete the translation - fetch model first to trigger events
            $translation = $this->translations()
                ->where('field', $field)
                ->where('locale', $locale)
                ->first();

            if ($translation) {
                $translation->delete();
            }
        } else {
            // Create or update the translation
            $translationModel::updateOrCreate(
                [
                    'translatable_type' => $this->getMorphClass(),
                    'translatable_id' => $this->getKey(),
                    'field' => $field,
                    'locale' => $locale,
                ],
                ['value' => $value]
            );
        }

        // Clear cache
        $this->clearFieldCache($field, $locale);

        // Refresh the translations relationship if loaded
        if ($this->relationLoaded('translations')) {
            $this->load('translations');
        }
    }

    /**
     * Check if a translation exists.
     */
    public function hasTranslation(string $field, ?string $locale = null): bool
    {
        $locale = $locale ?? $this->getCurrentLocale();

        if ($locale === $this->getSourceLocale()) {
            return $this->getOriginalAttributeValue($field) !== null;
        }

        if ($this->relationLoaded('translations')) {
            return $this->translations
                ->where('field', $field)
                ->where('locale', $locale)
                ->isNotEmpty();
        }

        return $this->translations()
            ->where('field', $field)
            ->where('locale', $locale)
            ->exists();
    }

    /**
     * Check if model has any translations.
     */
    public function hasAnyTranslations(): bool
    {
        if ($this->relationLoaded('translations')) {
            return $this->translations->isNotEmpty();
        }

        return $this->translations()->exists();
    }

    /**
     * Check if model has all required fields for a specific locale.
     */
    public function hasAllRequiredFieldsForLocale(?string $locale = null): bool
    {
        $locale = $locale ?? $this->getCurrentLocale();
        $requiredFields = $this->getRequiredTranslatableFields();

        foreach ($requiredFields as $field) {
            if (! $this->hasTranslation($field, $locale)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get all translations organized by field and locale.
     *
     * @return array<string, array<string, string>>
     */
    public function getAllTranslations(): array
    {
        $translations = [];

        // Eager load if not already loaded
        if (! $this->relationLoaded('translations')) {
            $this->load('translations');
        }

        foreach ($this->getTranslatableFields() as $field) {
            $translations[$field] = [];

            // Add source locale value
            $sourceValue = $this->getOriginalAttributeValue($field);
            if ($sourceValue !== null) {
                $translations[$field][$this->getSourceLocale()] = $sourceValue;
            }

            // Add translated values
            foreach ($this->translations as $translation) {
                if ($translation->field === $field) {
                    $translations[$field][$translation->locale] = $translation->value;
                }
            }
        }

        return $translations;
    }

    /**
     * Get all translations for a specific field.
     *
     * @return array<string, string>
     */
    public function getFieldTranslations(string $field): array
    {
        $translations = [];

        // Add source locale value
        $sourceValue = $this->getOriginalAttributeValue($field);
        if ($sourceValue !== null) {
            $translations[$this->getSourceLocale()] = $sourceValue;
        }

        // Get translations from relationship
        if ($this->relationLoaded('translations')) {
            foreach ($this->translations as $translation) {
                if ($translation->field === $field) {
                    $translations[$translation->locale] = $translation->value;
                }
            }
        } else {
            $translationRecords = $this->translations()
                ->where('field', $field)
                ->get();

            foreach ($translationRecords as $translation) {
                $translations[$translation->locale] = $translation->value;
            }
        }

        return $translations;
    }

    /**
     * Get missing locales for a specific field.
     *
     * @return array<string>
     */
    public function getMissingLocales(string $field): array
    {
        $existingLocales = array_keys($this->getFieldTranslations($field));

        return array_diff($this->getSupportedLocales(), $existingLocales);
    }

    /**
     * Get the original attribute value without translation.
     */
    protected function getOriginalAttributeValue(string $field): mixed
    {
        return $this->getAttributes()[$field] ?? null;
    }

    /**
     * Magic getter for translated attributes.
     */
    public function getAttribute($key)
    {
        // Check if it's a translatable field
        if ($this->isFieldTranslatable($key) && ! $this->isTranslationExcluded()) {
            $locale = $this->getCurrentLocale();

            // Only translate if not source locale
            if ($locale !== $this->getSourceLocale()) {
                $translation = $this->getTranslate($key, $locale);
                if ($translation !== null) {
                    return $translation;
                }
            }
        }

        return parent::getAttribute($key);
    }

    // ==========================================
    // Query Scopes
    // ==========================================

    /**
     * Scope to search in a translatable field.
     */
    public function scopeSearchTranslatable(Builder $query, string $field, string $search, ?string $locale = null): Builder
    {
        $locale = $locale ?? App::getLocale();

        return $query->where(function ($q) use ($field, $search, $locale) {
            // Search in original field for source locale
            $q->where($field, 'LIKE', "%{$search}%");

            // Search in translations
            $q->orWhereHas('translations', function ($tq) use ($field, $search, $locale) {
                $tq->where('field', $field)
                    ->where('locale', $locale)
                    ->where('value', 'LIKE', "%{$search}%");
            });
        });
    }

    /**
     * Scope to search in multiple translatable fields.
     */
    public function scopeSearchMultipleTranslatable(Builder $query, array $fields, string $search, ?string $locale = null): Builder
    {
        $locale = $locale ?? App::getLocale();

        return $query->where(function ($q) use ($fields, $search, $locale) {
            foreach ($fields as $field) {
                $q->orWhere($field, 'LIKE', "%{$search}%")
                    ->orWhereHas('translations', function ($tq) use ($field, $search, $locale) {
                        $tq->where('field', $field)
                            ->where('locale', $locale)
                            ->where('value', 'LIKE', "%{$search}%");
                    });
            }
        });
    }

    /**
     * Scope to search in a specific locale.
     */
    public function scopeSearchTranslatableInLanguage(Builder $query, string $field, string $search, string $locale): Builder
    {
        return $this->scopeSearchTranslatable($query, $field, $search, $locale);
    }

    /**
     * Scope to get models with all required translations for a locale.
     */
    public function scopeHasAllRequiredFieldsForLocaleScope(Builder $query, ?string $locale = null): Builder
    {
        $locale = $locale ?? App::getLocale();
        $requiredFields = $this->getRequiredTranslatableFields();

        if (empty($requiredFields) || $locale === $this->getSourceLocale()) {
            return $query;
        }

        foreach ($requiredFields as $field) {
            $query->whereHas('translations', function ($tq) use ($field, $locale) {
                $tq->where('field', $field)
                    ->where('locale', $locale)
                    ->whereNotNull('value')
                    ->where('value', '!=', '');
            });
        }

        return $query;
    }

    /**
     * Scope to search in a relationship's translatable field.
     */
    public function scopeSearchRelationTranslatable(Builder $query, string $relation, string $field, string $search, ?string $locale = null): Builder
    {
        $locale = $locale ?? App::getLocale();

        return $query->whereHas($relation, function ($q) use ($field, $search, $locale) {
            $q->where($field, 'LIKE', "%{$search}%")
                ->orWhereHas('translations', function ($tq) use ($field, $search, $locale) {
                    $tq->where('field', $field)
                        ->where('locale', $locale)
                        ->where('value', 'LIKE', "%{$search}%");
                });
        });
    }

    // ==========================================
    // Cache Methods
    // ==========================================

    /**
     * Check if caching is enabled.
     */
    protected function isCacheEnabled(): bool
    {
        return config('polyglot-model.cache.enabled', true);
    }

    /**
     * Get cache TTL in seconds.
     */
    protected function getCacheTtl(): int
    {
        return config('polyglot-model.cache.ttl', 3600);
    }

    /**
     * Get cache prefix.
     */
    protected function getCachePrefix(): string
    {
        return config('polyglot-model.cache.prefix', 'polyglot_');
    }

    /**
     * Get cache key for a field and locale.
     */
    protected function getCacheKey(string $field, string $locale): string
    {
        return $this->getCachePrefix() . $this->getMorphClass() . '_' . $this->getKey() . '_' . $field . '_' . $locale;
    }

    /**
     * Get translation from cache.
     */
    protected function getFromCache(string $field, string $locale): ?string
    {
        if (! $this->isCacheEnabled()) {
            return null;
        }

        // Check in-memory cache first
        $cacheKey = "{$field}_{$locale}";
        if (isset($this->translationsCache[$cacheKey])) {
            return $this->translationsCache[$cacheKey];
        }

        // Check persistent cache
        $persistentKey = $this->getCacheKey($field, $locale);

        return Cache::get($persistentKey);
    }

    /**
     * Set translation to cache.
     */
    protected function setToCache(string $field, string $locale, string $value): void
    {
        if (! $this->isCacheEnabled()) {
            return;
        }

        // Set in-memory cache
        $cacheKey = "{$field}_{$locale}";
        $this->translationsCache[$cacheKey] = $value;

        // Set persistent cache
        $persistentKey = $this->getCacheKey($field, $locale);
        Cache::put($persistentKey, $value, $this->getCacheTtl());
    }

    /**
     * Clear cache for a specific field and locale.
     */
    protected function clearFieldCache(string $field, string $locale): void
    {
        if (! $this->isCacheEnabled()) {
            return;
        }

        // Clear in-memory cache
        $cacheKey = "{$field}_{$locale}";
        unset($this->translationsCache[$cacheKey]);

        // Clear persistent cache
        $persistentKey = $this->getCacheKey($field, $locale);
        Cache::forget($persistentKey);
    }

    /**
     * Clear all translation cache for this model.
     */
    public function clearTranslationCache(): void
    {
        if (! $this->isCacheEnabled()) {
            return;
        }

        // Clear in-memory cache
        $this->translationsCache = [];

        // Clear persistent cache for all fields and locales
        foreach ($this->getTranslatableFields() as $field) {
            foreach ($this->getSupportedLocales() as $locale) {
                $persistentKey = $this->getCacheKey($field, $locale);
                Cache::forget($persistentKey);
            }
        }
    }
}
