# Sorgu Kapsamları (Query Scopes)

Bu paket, çevrilmiş içerikte arama yapmak için birkaç sorgu kapsamı sağlar.

## Tek Alanda Arama

```php
// Türkçe çevirilerde ara
Post::searchTranslatable('title', 'Laravel', 'tr')->get();

// Belirtilmezse geçerli dili kullanır
App::setLocale('tr');
Post::searchTranslatable('title', 'Laravel')->get();
```

## Çoklu Alanda Arama

```php
// Başlık ve içerikte ara
Post::searchMultipleTranslatable(
    ['title', 'content'],
    'Laravel',
    'tr'
)->get();
```

## Tam Çevirileri Filtreleme

Sadece tüm zorunlu alanları çevrilmiş olan modelleri getir:

```php
// Sadece Türkçe çevirisi tam olan gönderiler
Post::hasAllRequiredFieldsForLocaleScope('tr')->get();
```

## İlişkilerde Arama

```php
// İlişkili modelin çevirilerinde ara
Story::searchRelationTranslatable('category', 'name', 'Macera', 'tr')->get();
```

## Kapsamları Birleştirme

```php
Post::query()
    ->searchTranslatable('title', 'Laravel')
    ->hasAllRequiredFieldsForLocaleScope('tr')
    ->where('published', true)
    ->latest()
    ->get();
```

## Kaynak Dilde Arama

```php
// Ayrıca orijinal alan değerlerinde de arar
Post::searchTranslatable('title', 'Laravel', 'en')->get();
// Başlığında "Laravel" geçenleri bulur (kaynak veya çeviri)
```
