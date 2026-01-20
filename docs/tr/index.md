---
layout: home

hero:
  name: Laravel Polyglot Model
  text: Eloquent modelleri için zarif çoklu dil desteği
  tagline: Polimorfik depolama, eager loading ve güçlü sorgu kapsamları
  actions:
    - theme: brand
      text: Başlayın
      link: /tr/installation
    - theme: alt
      text: GitHub'da Görüntüle
      link: https://github.com/barisdemirhan/laravel-polyglot-model

features:
  - icon: 🚀
    title: Basit API
    details: Sezgisel trait tabanlı yaklaşım. Sadece trait'i ekleyin ve çevrilebilir alanları tanımlayın.
  - icon: 🔄
    title: Polimorfik Depolama
    details: Tüm modelleriniz için tek çeviri tablosu. Temiz ve verimli veritabanı tasarımı.
  - icon: ⚡
    title: Eager Loading
    details: Tam ilişki yükleme desteği ile performans için optimize edilmiştir.
  - icon: 🎯
    title: Sihirli Getter'lar
    details: Geçerli dile göre model özelliklerine normal bir özellik gibi erişin.
  - icon: 🔍
    title: Sorgu Kapsamları (Scopes)
    details: Güçlü yerleşik kapsamlarla çevrilmiş alanlarda arama yapın.
  - icon: 💾
    title: Önbellekleme
    details: Daha iyi performans için yapılandırılabilir TTL ile yerleşik önbellek desteği.
---

## Hızlı Örnek

```php
use PolyglotModel\Traits\HasTranslations;

class Post extends Model
{
    use HasTranslations;

    protected array $translatableFields = ['title', 'content'];
}

// Çevirileri ayarla
$post->setTranslate('title', 'tr', 'Merhaba Dünya');

// Çevirileri al (sihirli getter)
App::setLocale('tr');
echo $post->title; // "Merhaba Dünya"
```
