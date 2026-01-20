<?php

declare(strict_types=1);

namespace PolyglotModel\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphMany;

interface TranslatableContract
{
    /**
     * Get all translations for this model.
     */
    public function translations(): MorphMany;

    /**
     * Get translatable fields for this model.
     *
     * @return array<string>
     */
    public function getTranslatableFields(): array;

    /**
     * Get required translatable fields for this model.
     *
     * @return array<string>
     */
    public function getRequiredTranslatableFields(): array;

    /**
     * Get translation for a specific field and locale.
     */
    public function getTranslate(string $field, ?string $locale = null): ?string;

    /**
     * Set translation for a specific field and locale.
     */
    public function setTranslate(string $field, string $locale, ?string $value): void;

    /**
     * Check if model is excluded from translation.
     */
    public function isTranslationExcluded(): bool;

    /**
     * Check if model has a specific translation.
     */
    public function hasTranslation(string $field, ?string $locale = null): bool;

    /**
     * Check if model has any translations.
     */
    public function hasAnyTranslations(): bool;

    /**
     * Check if model has all required fields for a specific locale.
     */
    public function hasAllRequiredFieldsForLocale(?string $locale = null): bool;

    /**
     * Get all translations organized by field and locale.
     *
     * @return array<string, array<string, string>>
     */
    public function getAllTranslations(): array;

    /**
     * Get all translations for a specific field.
     *
     * @return array<string, string>
     */
    public function getFieldTranslations(string $field): array;

    /**
     * Set the preferred language for this model instance.
     *
     * @return $this
     */
    public function setPreferredLanguage(string $locale): static;

    /**
     * Clear the translation cache for this model.
     */
    public function clearTranslationCache(): void;
}
