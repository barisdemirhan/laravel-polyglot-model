# Query Scopes

The package provides several query scopes for searching translated content.

## Search Single Field

```php
// Search in Turkish translations
Post::searchTranslatable('title', 'Laravel', 'tr')->get();

// Uses current locale if not specified
App::setLocale('tr');
Post::searchTranslatable('title', 'Laravel')->get();
```

## Search Multiple Fields

```php
// Search across title and content
Post::searchMultipleTranslatable(
    ['title', 'content'],
    'Laravel',
    'tr'
)->get();
```

## Filter by Complete Translations

Get only models with all required fields translated:

```php
// Only posts with complete Turkish translations
Post::hasAllRequiredFieldsForLocaleScope('tr')->get();
```

## Search in Relations

```php
// Search in related model's translations
Story::searchRelationTranslatable('category', 'name', 'Adventure', 'tr')->get();
```

## Combining Scopes

```php
Post::query()
    ->searchTranslatable('title', 'Laravel')
    ->hasAllRequiredFieldsForLocaleScope('tr')
    ->where('published', true)
    ->latest()
    ->get();
```

## Search in Source Locale

```php
// Also searches original field values
Post::searchTranslatable('title', 'Laravel', 'en')->get();
// Finds posts with "Laravel" in title (source or translation)
```
