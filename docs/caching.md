# Caching

The package includes built-in caching to improve performance.

## Configuration

```php
// config/polyglot-model.php

'cache' => [
    'enabled' => true,
    'ttl' => 3600, // 1 hour in seconds
    'prefix' => 'polyglot_',
],
```

## How It Works

1. When a translation is fetched, it's cached with the configured TTL
2. Subsequent requests return the cached value
3. Cache is automatically cleared when translations are updated

## Cache Keys

Keys follow this format:

```
{prefix}{model_type}_{model_id}_{field}_{locale}
```

Example:

```
polyglot_App\Models\Post_123_title_tr
```

## Clearing Cache

```php
// Clear all translations for a model
$post->clearTranslationCache();

// Automatic clearing on update
$post->setTranslate('title', 'tr', 'New Title');
// Cache for this field/locale is automatically cleared
```

## In-Memory Cache

The package also uses an in-memory cache per request:

```php
// First call - queries database
$post->getTranslate('title', 'tr');

// Second call in same request - uses in-memory cache
$post->getTranslate('title', 'tr');
```

## Disabling Cache

```php
// config/polyglot-model.php
'cache' => [
    'enabled' => false,
],
```

Or via environment:

```env
POLYGLOT_CACHE_ENABLED=false
POLYGLOT_CACHE_TTL=7200
```

## Production Recommendations

- Keep caching enabled
- Use Redis or Memcached for better performance
- Set appropriate TTL based on your update frequency
- Consider using cache tags for easier invalidation
