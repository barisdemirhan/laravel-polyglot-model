# Configuration

The configuration file is located at `config/polyglot-model.php`.

## Full Configuration

```php
<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Source Locale
    |--------------------------------------------------------------------------
    |
    | The source locale is the language in which your original content is
    | written. This is used to determine which field contains the source.
    |
    */
    'source_locale' => env('POLYGLOT_SOURCE_LOCALE', 'en'),

    /*
    |--------------------------------------------------------------------------
    | Fallback Locale
    |--------------------------------------------------------------------------
    |
    | When a translation is not available for the current locale, the package
    | will fall back to this locale before using the original field value.
    |
    */
    'fallback_locale' => env('POLYGLOT_FALLBACK_LOCALE', 'en'),

    /*
    |--------------------------------------------------------------------------
    | Supported Locales
    |--------------------------------------------------------------------------
    |
    | Define all the locales your application supports. Translations for
    | unsupported locales will be handled based on 'strict_locale' setting.
    |
    */
    'supported_locales' => ['en', 'tr', 'de', 'es', 'fr', 'pt_BR'],

    /*
    |--------------------------------------------------------------------------
    | Strict Locale Mode
    |--------------------------------------------------------------------------
    |
    | When enabled, attempting to set a translation for an unsupported locale
    | will throw an InvalidLocaleException. When disabled, it will be silently
    | accepted with a warning logged.
    |
    */
    'strict_locale' => env('POLYGLOT_STRICT_LOCALE', false),

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    */
    'cache' => [
        'enabled' => env('POLYGLOT_CACHE_ENABLED', true),
        'ttl' => env('POLYGLOT_CACHE_TTL', 3600),
        'prefix' => 'polyglot_',
    ],

    /*
    |--------------------------------------------------------------------------
    | Translation Table
    |--------------------------------------------------------------------------
    */
    'table_name' => 'translations',

    /*
    |--------------------------------------------------------------------------
    | Translation Model
    |--------------------------------------------------------------------------
    |
    | You can use your own Translation model by specifying it here.
    |
    */
    'model' => PolyglotModel\Models\Translation::class,

    /*
    |--------------------------------------------------------------------------
    | Events
    |--------------------------------------------------------------------------
    */
    'events' => [
        'enabled' => true,
    ],
];
```

## Environment Variables

| Variable                   | Default | Description                       |
| -------------------------- | ------- | --------------------------------- |
| `POLYGLOT_SOURCE_LOCALE`   | `en`    | Source content locale             |
| `POLYGLOT_FALLBACK_LOCALE` | `en`    | Fallback when translation missing |
| `POLYGLOT_STRICT_LOCALE`   | `false` | Throw on invalid locale           |
| `POLYGLOT_CACHE_ENABLED`   | `true`  | Enable caching                    |
| `POLYGLOT_CACHE_TTL`       | `3600`  | Cache duration in seconds         |

## Example .env

```env
POLYGLOT_SOURCE_LOCALE=en
POLYGLOT_FALLBACK_LOCALE=en
POLYGLOT_STRICT_LOCALE=false
POLYGLOT_CACHE_ENABLED=true
POLYGLOT_CACHE_TTL=3600
```

## Custom Translation Model

```php
// app/Models/CustomTranslation.php

namespace App\Models;

use PolyglotModel\Models\Translation;

class CustomTranslation extends Translation
{
    // Add custom methods or override existing ones

    public function user()
    {
        return $this->belongsTo(User::class, 'translated_by');
    }
}
```

Then update config:

```php
// config/polyglot-model.php
'model' => App\Models\CustomTranslation::class,
```

## Adding Custom Migration Columns

```php
// Add to your migration after publishing
Schema::table('translations', function (Blueprint $table) {
    $table->foreignId('translated_by')->nullable();
    $table->timestamp('reviewed_at')->nullable();
});
```
