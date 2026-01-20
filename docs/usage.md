# Basic Usage

## Defining Translatable Fields

Add the `$translatableFields` property to your model:

```php
use PolyglotModel\Contracts\TranslatableContract;
use PolyglotModel\Traits\HasTranslations;

class Post extends Model implements TranslatableContract
{
    use HasTranslations;

    protected array $translatableFields = [
        'title',
        'description',
        'content',
    ];
}
```

## Setting Translations

```php
// Set a single translation
$post->setTranslate('title', 'tr', 'Türkçe Başlık');

// Set to null to delete translation
$post->setTranslate('title', 'tr', null);
```

## Getting Translations

```php
// Get specific locale
$post->getTranslate('title', 'tr');

// Get current locale (magic getter)
$post->title;

// Get with preferred language
$post->setPreferredLanguage('de')->title;
```

## Fallback Behavior

When a translation is not found:

1. Check requested locale
2. Check fallback locale (from config)
3. Return original field value

```php
// No French translation exists
App::setLocale('fr');
$post->title; // Returns English (fallback) or original value
```

## Excluding from Translation

Mark a model instance as excluded:

```php
class Post extends Model implements TranslatableContract
{
    use HasTranslations;

    public function isTranslationExcluded(): bool
    {
        return $this->is_draft || $this->is_private;
    }
}
```

## Eager Loading

For better performance, eager load translations:

```php
// Single relationship
$posts = Post::with('translations')->get();

// With specific locale filter (if needed)
$posts = Post::with(['translations' => function ($query) {
    $query->where('locale', 'tr');
}])->get();
```

## Getting All Translations

```php
// All fields, all locales
$post->getAllTranslations();

// Single field, all locales
$post->getFieldTranslations('title');
// ['en' => 'English', 'tr' => 'Türkçe', 'de' => 'Deutsch']

// Find missing translations
$post->getMissingLocales('title');
// ['fr', 'es'] - locales without translation
```

## Blade Integration

```blade
{{-- In your Blade template --}}
<h1>{{ $post->title }}</h1>

{{-- Force specific locale --}}
<h1>{{ $post->getTranslate('title', 'tr') }}</h1>

{{-- Check if translated --}}
@if($post->hasTranslation('title', app()->getLocale()))
    <span class="badge">Translated</span>
@endif
```

## API Response

```php
// In your Controller or Resource
return [
    'title' => $post->title, // Auto-translated based on Accept-Language
    'translations' => $post->getAllTranslations(),
];
```
