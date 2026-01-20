<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Source Locale
    |--------------------------------------------------------------------------
    |
    | The source locale is the language in which your original content is written.
    | This is used to determine which field contains the source content.
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
    |
    | Translation cache settings for better performance. Caching is highly
    | recommended for production environments.
    |
    */
    'cache' => [
        'enabled' => env('POLYGLOT_CACHE_ENABLED', true),
        'ttl' => env('POLYGLOT_CACHE_TTL', 3600), // seconds
        'prefix' => 'polyglot_',
    ],

    /*
    |--------------------------------------------------------------------------
    | Translation Table
    |--------------------------------------------------------------------------
    |
    | The name of the database table used to store translations.
    |
    */
    'table_name' => 'translations',

    /*
    |--------------------------------------------------------------------------
    | Translation Model
    |--------------------------------------------------------------------------
    |
    | You can use your own Translation model by specifying it here.
    | Your model should extend the default Translation model.
    |
    */
    'model' => PolyglotModel\Models\Translation::class,

    /*
    |--------------------------------------------------------------------------
    | Events
    |--------------------------------------------------------------------------
    |
    | Enable or disable translation events. When enabled, the package will
    | dispatch TranslationCreated, TranslationUpdated, and TranslationDeleted
    | events that you can listen to.
    |
    */
    'events' => [
        'enabled' => true,
    ],
];
