---
layout: home

hero:
  name: Laravel Polyglot Model
  text: Elegant multilingual support for Eloquent models
  tagline: Polymorphic storage, eager loading, and powerful query scopes
  actions:
    - theme: brand
      text: Get Started
      link: /installation
    - theme: alt
      text: View on GitHub
      link: https://github.com/barisdemirhan/laravel-polyglot-model

features:
  - icon: 🚀
    title: Simple API
    details: Intuitive trait-based approach. Just add the trait and define translatable fields.
  - icon: 🔄
    title: Polymorphic Storage
    details: Single translations table for all your models. Clean and efficient database design.
  - icon: ⚡
    title: Eager Loading
    details: Optimized for performance with full relationship loading support.
  - icon: 🎯
    title: Magic Getters
    details: Access translations like regular model attributes based on current locale.
  - icon: 🔍
    title: Query Scopes
    details: Search across translated fields with powerful built-in scopes.
  - icon: 💾
    title: Caching
    details: Built-in cache support with configurable TTL for better performance.
---

## Quick Example

```php
use PolyglotModel\Traits\HasTranslations;

class Post extends Model
{
    use HasTranslations;

    protected array $translatableFields = ['title', 'content'];
}

// Set translations
$post->setTranslate('title', 'tr', 'Merhaba Dünya');

// Get translations (magic getter)
App::setLocale('tr');
echo $post->title; // "Merhaba Dünya"
```
