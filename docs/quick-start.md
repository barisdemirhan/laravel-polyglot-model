# Quick Start

## 1. Add the Trait to Your Model

```php
<?php

namespace App\Models;

use PolyglotModel\Contracts\TranslatableContract;
use PolyglotModel\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class Post extends Model implements TranslatableContract
{
    use HasTranslations;

    /**
     * Fields that can be translated.
     */
    protected array $translatableFields = [
        'title',
        'slug',
        'content',
        'excerpt',
    ];

    /**
     * Required fields for a "complete" translation.
     * Used by hasAllRequiredFieldsForLocale() method.
     */
    protected array $requiredTranslatableFields = [
        'title',
        'slug',
    ];
}
```

## 2. Create Some Content

```php
// Create a post with English content (source locale)
$post = Post::create([
    'title' => 'Getting Started with Laravel',
    'slug' => 'getting-started-laravel',
    'content' => 'Laravel is a wonderful PHP framework...',
]);
```

## 3. Add Translations

```php
// Add Turkish translation
$post->setTranslate('title', 'tr', 'Laravel ile Başlarken');
$post->setTranslate('slug', 'tr', 'laravel-ile-baslarken');
$post->setTranslate('content', 'tr', 'Laravel harika bir PHP framework...');

// Add German translation
$post->setTranslate('title', 'de', 'Erste Schritte mit Laravel');
```

## 4. Retrieve Translations

```php
// Method 1: Explicit get
$turkishTitle = $post->getTranslate('title', 'tr');
// "Laravel ile Başlarken"

// Method 2: Magic getter (uses current app locale)
App::setLocale('tr');
echo $post->title;
// "Laravel ile Başlarken"

// Method 3: Preferred language
$germanTitle = $post->setPreferredLanguage('de')->title;
// "Erste Schritte mit Laravel"
```

## 5. Check Translation Status

```php
// Check if translation exists
$post->hasTranslation('title', 'tr'); // true
$post->hasTranslation('title', 'fr'); // false

// Check if all required fields are translated
$post->hasAllRequiredFieldsForLocale('tr'); // true
$post->hasAllRequiredFieldsForLocale('de'); // false (only title translated)

// Get all translations
$all = $post->getAllTranslations();
// [
//     'title' => ['en' => '...', 'tr' => '...', 'de' => '...'],
//     'slug' => ['en' => '...', 'tr' => '...'],
//     'content' => ['en' => '...', 'tr' => '...'],
// ]
```

## 6. Query Translated Content

```php
// Search in translations
Post::searchTranslatable('title', 'Laravel', 'tr')->get();

// Get posts with complete Turkish translations
Post::hasAllRequiredFieldsForLocaleScope('tr')->get();
```

## Next Steps

- Learn about [Query Scopes](/query-scopes) for advanced searching
- Configure [Caching](/caching) for better performance
- Set up [Events](/events) to react to translation changes
