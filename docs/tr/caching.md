# Önbellekleme (Caching)

Paket, performansı artırmak için yerleşik önbellekleme içerir.

## Yapılandırma

```php
// config/polyglot-model.php

'cache' => [
    'enabled' => true,
    'ttl' => 3600, // 1 saat (saniye cinsinden)
    'prefix' => 'polyglot_',
],
```

## Nasıl Çalışır

1. Bir çeviri getirildiğinde, yapılandırılan TTL ile önbelleğe alınır.
2. Sonraki istekler önbelleğe alınan değeri döndürür.
3. Çeviriler güncellendiğinde önbellek otomatik olarak temizlenir.

## Önbellek Anahtarları

Anahtarlar şu formatı izler:

```
{prefix}{model_type}_{model_id}_{field}_{locale}
```

Örnek:

```
polyglot_App\Models\Post_123_title_tr
```

## Önbelleği Temizleme

```php
// Bir model için tüm çevirileri temizle
$post->clearTranslationCache();

// Güncellemede otomatik temizleme
$post->setTranslate('title', 'tr', 'Yeni Başlık');
// Bu alan/dil için önbellek otomatik olarak temizlenir
```

## Bellek İçi (In-Memory) Önbellek

Paket ayrıca istek başına bir bellek içi önbellek kullanır:

```php
// İlk çağrı - veritabanını sorgular
$post->getTranslate('title', 'tr');

// Aynı istekteki ikinci çağrı - bellek içi önbelleği kullanır
$post->getTranslate('title', 'tr');
```

## Önbelleği Devre Dışı Bırakma

```php
// config/polyglot-model.php
'cache' => [
    'enabled' => false,
],
```

Veya ortam değişkeni ile:

```env
POLYGLOT_CACHE_ENABLED=false
POLYGLOT_CACHE_TTL=7200
```

## Üretim Ortamı (Production) Önerileri

- Önbelleklemeyi etkin tutun
- Daha iyi performans için Redis veya Memcached kullanın
- Güncelleme sıklığınıza göre uygun TTL ayarlayın
- Daha kolay geçersiz kılma (invalidation) için önbellek etiketlerini (cache tags) kullanmayı düşünün
