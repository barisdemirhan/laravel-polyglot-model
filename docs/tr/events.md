# Olaylar (Events)

Paket; çeviriler oluşturulduğunda, güncellendiğinde veya silindiğinde olaylar gönderir (dispatch eder).

## Mevcut Olaylar

| Olay                                      | Açıklama                            |
| ----------------------------------------- | ----------------------------------- |
| `PolyglotModel\Events\TranslationCreated` | Yeni bir çeviri kaydedildiğinde     |
| `PolyglotModel\Events\TranslationUpdated` | Mevcut bir çeviri değiştirildiğinde |
| `PolyglotModel\Events\TranslationDeleted` | Bir çeviri silindiğinde             |

## Olayları Dinleme

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

## Dinleyici (Listener) Örneği

```php
// app/Listeners/NotifyTranslators.php

namespace App\Listeners;

use PolyglotModel\Events\TranslationCreated;

class NotifyTranslators
{
    public function handle(TranslationCreated $event): void
    {
        $translation = $event->translation;

        Log::info("Çeviri oluşturuldu", [
            'model' => $translation->translatable_type,
            'id' => $translation->translatable_id,
            'field' => $translation->field,
            'locale' => $translation->locale,
        ]);

        // Çeviri ekibini bilgilendir
        // Webhook gönder
        // Çeviri durumu panosunu güncelle
    }
}
```

## Olay Özellikleri

Her olay `Translation` model örneğini içerir:

```php
public function handle(TranslationCreated $event): void
{
    $event->translation->translatable_type; // App\Models\Post
    $event->translation->translatable_id;   // 123
    $event->translation->field;             // 'title'
    $event->translation->locale;            // 'tr'
    $event->translation->value;             // 'Türkçe Başlık'

    // Ebeveyn modele erişim
    $event->translation->translatable; // Post instance
}
```

## Olayları Devre Dışı Bırakma

```php
// config/polyglot-model.php

'events' => [
    'enabled' => false,
],
```

Veya ortam değişkeni ile:

```env
# .env dosyası - olaylar varsayılan olarak etkindir
```
