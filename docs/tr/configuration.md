# Konfigürasyon

Yapılandırma dosyası `config/polyglot-model.php` konumunda bulunur.

## Tam Konfigürasyon

```php
<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Kaynak Dil (Source Locale)
    |--------------------------------------------------------------------------
    |
    | Kaynak dil, orijinal içeriğinizin yazıldığı dildir. Bu, hangi alanın
    | kaynağı içerdiğini belirlemek için kullanılır.
    |
    */
    'source_locale' => env('POLYGLOT_SOURCE_LOCALE', 'en'),

    /*
    |--------------------------------------------------------------------------
    | Yedek Dil (Fallback Locale)
    |--------------------------------------------------------------------------
    |
    | Geçerli dil için bir çeviri mevcut olmadığında, paket orijinal alan
    | değerini kullanmadan önce bu dile geri dönecektir.
    |
    */
    'fallback_locale' => env('POLYGLOT_FALLBACK_LOCALE', 'en'),

    /*
    |--------------------------------------------------------------------------
    | Desteklenen Diller (Supported Locales)
    |--------------------------------------------------------------------------
    |
    | Uygulamanızın desteklediği tüm dilleri tanımlayın. Desteklenmeyen diller
    | için çeviriler 'strict_locale' ayarına göre işlenecektir.
    |
    */
    'supported_locales' => ['en', 'tr', 'de', 'es', 'fr', 'pt_BR'],

    /*
    |--------------------------------------------------------------------------
    | Katı Dil Modu (Strict Locale Mode)
    |--------------------------------------------------------------------------
    |
    | Etkinleştirildiğinde, desteklenmeyen bir dil için çeviri ayarlamaya çalışmak
    | InvalidLocaleException fırlatır. Devre dışı bırakıldığında, bir uyarı
    | günlüğe kaydedilerek sessizce kabul edilir.
    |
    */
    'strict_locale' => env('POLYGLOT_STRICT_LOCALE', false),

    /*
    |--------------------------------------------------------------------------
    | Önbellek Yapılandırması (Cache Configuration)
    |--------------------------------------------------------------------------
    */
    'cache' => [
        'enabled' => env('POLYGLOT_CACHE_ENABLED', true),
        'ttl' => env('POLYGLOT_CACHE_TTL', 3600),
        'prefix' => 'polyglot_',
    ],

    /*
    |--------------------------------------------------------------------------
    | Çeviri Tablosu (Translation Table)
    |--------------------------------------------------------------------------
    */
    'table_name' => 'translations',

    /*
    |--------------------------------------------------------------------------
    | Çeviri Modeli (Translation Model)
    |--------------------------------------------------------------------------
    |
    | Burada belirterek kendi Çeviri modelinizi kullanabilirsiniz.
    |
    */
    'model' => PolyglotModel\Models\Translation::class,

    /*
    |--------------------------------------------------------------------------
    | Olaylar (Events)
    |--------------------------------------------------------------------------
    */
    'events' => [
        'enabled' => true,
    ],
];
```

## Ortam Değişkenleri

| Değişken                   | Varsayılan | Açıklama                         |
| -------------------------- | ---------- | -------------------------------- |
| `POLYGLOT_SOURCE_LOCALE`   | `en`       | Kaynak içerik dili               |
| `POLYGLOT_FALLBACK_LOCALE` | `en`       | Çeviri eksik olduğunda yedek dil |
| `POLYGLOT_STRICT_LOCALE`   | `false`    | Geçersiz dilde hata fırlat       |
| `POLYGLOT_CACHE_ENABLED`   | `true`     | Önbelleklemeyi etkinleştir       |
| `POLYGLOT_CACHE_TTL`       | `3600`     | Saniye cinsinden önbellek süresi |

## Örnek .env

```env
POLYGLOT_SOURCE_LOCALE=en
POLYGLOT_FALLBACK_LOCALE=en
POLYGLOT_STRICT_LOCALE=false
POLYGLOT_CACHE_ENABLED=true
POLYGLOT_CACHE_TTL=3600
```

## Özel Çeviri Modeli

```php
// app/Models/CustomTranslation.php

namespace App\Models;

use PolyglotModel\Models\Translation;

class CustomTranslation extends Translation
{
    // Özel metotlar ekleyin veya mevcut olanları geçersiz kılın

    public function user()
    {
        return $this->belongsTo(User::class, 'translated_by');
    }
}
```

Sonra yapılandırmayı güncelleyin:

```php
// config/polyglot-model.php
'model' => App\Models\CustomTranslation::class,
```

## Özel Migrasyon Sütunları Ekleme

```php
// Migrasyonunuzu yayınladıktan sonra ekleyin
Schema::table('translations', function (Blueprint $table) {
    $table->foreignId('translated_by')->nullable();
    $table->timestamp('reviewed_at')->nullable();
});
```
