# Hızlı Başlangıç

## 1. Modeli Trait ile Genişletin

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
     * Çevrilebilir alanlar.
     */
    protected array $translatableFields = [
        'title',
        'slug',
        'content',
        'excerpt',
    ];

    /**
     * "Tam" bir çeviri için gerekli alanlar.
     * hasAllRequiredFieldsForLocale() metodu tarafından kullanılır.
     */
    protected array $requiredTranslatableFields = [
        'title',
        'slug',
    ];
}
```

## 2. İçerik Oluşturun

```php
// İngilizce içerikli bir yazı oluştur (kaynak dil)
$post = Post::create([
    'title' => 'Getting Started with Laravel',
    'slug' => 'getting-started-laravel',
    'content' => 'Laravel is a wonderful PHP framework...',
]);
```

## 3. Çevirileri Ekleyin

```php
// Türkçe çeviri ekle
$post->setTranslate('title', 'tr', 'Laravel ile Başlarken');
$post->setTranslate('slug', 'tr', 'laravel-ile-baslarken');
$post->setTranslate('content', 'tr', 'Laravel harika bir PHP framework...');

// Almanca çeviri ekle
$post->setTranslate('title', 'de', 'Erste Schritte mit Laravel');
```

## 4. Çevirileri Alın

```php
// Yöntem 1: Açıkça belirtilen dil
$turkishTitle = $post->getTranslate('title', 'tr');
// "Laravel ile Başlarken"

// Yöntem 2: Sihirli getter (mevcut uygulama dilini kullanır)
App::setLocale('tr');
echo $post->title;
// "Laravel ile Başlarken"

// Yöntem 3: Tercih edilen dil
$germanTitle = $post->setPreferredLanguage('de')->title;
// "Erste Schritte mit Laravel"
```

## 5. Çeviri Durumunu Kontrol Edin

```php
// Çevirinin var olup olmadığını kontrol et
$post->hasTranslation('title', 'tr'); // true
$post->hasTranslation('title', 'fr'); // false

// Tüm gerekli alanların çevrilip çevrilmediğini kontrol et
$post->hasAllRequiredFieldsForLocale('tr'); // true
$post->hasAllRequiredFieldsForLocale('de'); // false (sadece başlık çevrilmiş)

// Tüm çevirileri al
$all = $post->getAllTranslations();
// [
//     'title' => ['en' => '...', 'tr' => '...', 'de' => '...'],
//     'slug' => ['en' => '...', 'tr' => '...'],
//     'content' => ['en' => '...', 'tr' => '...'],
// ]
```

## 6. Çevrilmiş İçeriği Sorgulayın

```php
// Çevirilerde arama yap
Post::searchTranslatable('title', 'Laravel', 'tr')->get();

// Tam Türkçe çevirisi olan gönderileri getir
Post::hasAllRequiredFieldsForLocaleScope('tr')->get();
```

## Sonraki Adımlar

- Gelişmiş arama için [Sorgu Kapsamlarını](/tr/query-scopes) öğrenin
- Daha iyi performans için [Önbellekleme](/tr/caching) yapılandırın
- Çeviri değişikliklerine tepki vermek için [Olayları](/tr/events) ayarlayın
