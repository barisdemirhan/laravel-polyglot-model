<?php

declare(strict_types=1);

namespace PolyglotModel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Cache;
use PolyglotModel\Events\TranslationCreated;
use PolyglotModel\Events\TranslationDeleted;
use PolyglotModel\Events\TranslationUpdated;

class Translation extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'translatable_type',
        'translatable_id',
        'field',
        'locale',
        'value',
    ];

    /**
     * Create a new Translation model instance.
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = config('polyglot-model.table_name', 'translations');
    }

    /**
     * Boot the model.
     */
    protected static function booted(): void
    {
        static::created(function (Translation $translation) {
            if (config('polyglot-model.events.enabled', true)) {
                event(new TranslationCreated($translation));
            }
        });

        static::updated(function (Translation $translation) {
            if (config('polyglot-model.events.enabled', true)) {
                event(new TranslationUpdated($translation));
            }
        });

        static::deleted(function (Translation $translation) {
            if (config('polyglot-model.events.enabled', true)) {
                event(new TranslationDeleted($translation));
            }
        });
    }

    /**
     * Get the parent translatable model.
     */
    public function translatable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope a query to a specific translatable model.
     */
    public function scopeForModel($query, Model $model)
    {
        return $query->where('translatable_type', $model->getMorphClass())
            ->where('translatable_id', $model->getKey());
    }

    /**
     * Scope a query to a specific field.
     */
    public function scopeForField($query, string $field)
    {
        return $query->where('field', $field);
    }

    /**
     * Scope a query to a specific locale.
     */
    public function scopeForLocale($query, string $locale)
    {
        return $query->where('locale', $locale);
    }

    /**
     * Get the cache key for this translation.
     */
    public function getCacheKey(): string
    {
        $prefix = config('polyglot-model.cache.prefix', 'polyglot_');

        return "{$prefix}{$this->translatable_type}_{$this->translatable_id}_{$this->field}_{$this->locale}";
    }

    /**
     * Clear the cache for this translation.
     */
    public function clearCache(): void
    {
        if (config('polyglot-model.cache.enabled', true)) {
            Cache::forget($this->getCacheKey());
        }
    }

    /**
     * Clean orphaned translations (where parent model no longer exists).
     */
    public static function cleanOrphaned(): int
    {
        $translations = static::all();
        $deletedCount = 0;

        foreach ($translations as $translation) {
            $modelClass = $translation->translatable_type;

            if (! class_exists($modelClass)) {
                $translation->delete();
                $deletedCount++;

                continue;
            }

            $exists = $modelClass::where(
                (new $modelClass)->getKeyName(),
                $translation->translatable_id
            )->exists();

            if (! $exists) {
                $translation->delete();
                $deletedCount++;
            }
        }

        return $deletedCount;
    }
}
