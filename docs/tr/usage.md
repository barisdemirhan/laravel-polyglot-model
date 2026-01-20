# Temel Kullanım

## Çevrilebilir Alanları Tanımlama

Modelinize `$translatableFields` özelliğini ekleyin:

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

## Çevirileri Ayarlama

```php
// Tek bir çeviri ayarla
$post->setTranslate('title', 'tr', 'Türkçe Başlık');

// Çeviriyi silmek için null ayarla
$post->setTranslate('title', 'tr', null);
```

## Çevirileri Alma

```php
// Belirli bir dili al
$post->getTranslate('title', 'tr');

// Geçerli dili al (sihirli getter)
$post->title;

// Tercih edilen dil ile al
$post->setPreferredLanguage('de')->title;
```

## Yedek (Fallback) Davranışı

Bir çeviri bulunamadığında:

1. İstenen dili kontrol eder
2. Yedek dili kontrol eder (yapılandırmadan)
3. Orijinal alan değerini döndürür

```php
// Fransızca çeviri yok
App::setLocale('fr');
$post->title; // İngilizce (yedek) veya orijinal değeri döndürür
```

## Çeviriden Hariç Tutma

Bir model örneğini hariç tutulmuş olarak işaretleyin:

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

## Eager Loading (İlişkileri Önceden Yükleme)

Daha iyi performans için çevirileri önceden yükleyin:

```php
// Tekli ilişki
$posts = Post::with('translations')->get();

// Belirli bir dil filtresi ile (gerekirse)
$posts = Post::with(['translations' => function ($query) {
    $query->where('locale', 'tr');
}])->get();
```

## Tüm Çevirileri Alma

```php
// Tüm alanlar, tüm diller
$post->getAllTranslations();

// Tek alan, tüm diller
$post->getFieldTranslations('title');
// ['en' => 'English', 'tr' => 'Türkçe', 'de' => 'Deutsch']

// Eksik çevirileri bul
$post->getMissingLocales('title');
// ['fr', 'es'] - çevirisi olmayan diller
```

## Blade Entegrasyonu

```blade
{{-- Blade şablonunuzda --}}
<h1>{{ $post->title }}</h1>

{{-- Belirli bir dili zorla --}}
<h1>{{ $post->getTranslate('title', 'tr') }}</h1>

{{-- Çevrilip çevrilmediğini kontrol et --}}
@if($post->hasTranslation('title', app()->getLocale()))
    <span class="badge">Translated</span>
@endif
```

## API Yanıtı

```php
// Controller veya Resource içinde
return [
    'title' => $post->title, // Accept-Language başlığına göre otomatik çevrilir
    'translations' => $post->getAllTranslations(),
];
```
