# Events

The package dispatches events when translations are created, updated, or deleted.

## Available Events

| Event                                     | Description                              |
| ----------------------------------------- | ---------------------------------------- |
| `PolyglotModel\Events\TranslationCreated` | When a new translation is saved          |
| `PolyglotModel\Events\TranslationUpdated` | When an existing translation is modified |
| `PolyglotModel\Events\TranslationDeleted` | When a translation is removed            |

## Listening to Events

```php
// app/Providers/EventServiceProvider.php

use PolyglotModel\Events\TranslationCreated;
use PolyglotModel\Events\TranslationUpdated;
use PolyglotModel\Events\TranslationDeleted;
use App\Listeners\NotifyTranslators;
use App\Listeners\ClearPageCache;

protected $listen = [
    TranslationCreated::class => [
        NotifyTranslators::class,
    ],
    TranslationUpdated::class => [
        ClearPageCache::class,
    ],
    TranslationDeleted::class => [
        ClearPageCache::class,
    ],
];
```

## Listener Example

```php
// app/Listeners/NotifyTranslators.php

namespace App\Listeners;

use PolyglotModel\Events\TranslationCreated;

class NotifyTranslators
{
    public function handle(TranslationCreated $event): void
    {
        $translation = $event->translation;

        Log::info("Translation created", [
            'model' => $translation->translatable_type,
            'id' => $translation->translatable_id,
            'field' => $translation->field,
            'locale' => $translation->locale,
        ]);

        // Notify translation team
        // Send webhook
        // Update translation status dashboard
    }
}
```

## Event Properties

Each event contains the `Translation` model instance:

```php
public function handle(TranslationCreated $event): void
{
    $event->translation->translatable_type; // App\Models\Post
    $event->translation->translatable_id;   // 123
    $event->translation->field;             // 'title'
    $event->translation->locale;            // 'tr'
    $event->translation->value;             // 'Türkçe Başlık'

    // Access parent model
    $event->translation->translatable; // Post instance
}
```

## Disabling Events

```php
// config/polyglot-model.php

'events' => [
    'enabled' => false,
],
```

Or via environment:

```env
# .env file - events are enabled by default
```
