<?php

declare(strict_types=1);

namespace PolyglotModel\Tests;

use Illuminate\Database\Eloquent\Model;
use PolyglotModel\Contracts\TranslatableContract;
use PolyglotModel\Traits\HasTranslations;

/**
 * Test model for translation tests.
 */
class Post extends Model implements TranslatableContract
{
    use HasTranslations;

    protected $fillable = ['title', 'slug', 'content', 'translation_excluded'];

    protected $casts = [
        'translation_excluded' => 'boolean',
    ];

    protected array $translatableFields = [
        'title',
        'slug',
        'content',
    ];

    protected array $requiredTranslatableFields = [
        'title',
        'slug',
    ];

    public function isTranslationExcluded(): bool
    {
        return $this->translation_excluded ?? false;
    }
}
