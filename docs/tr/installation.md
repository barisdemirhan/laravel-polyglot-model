# Kurulum

## Gereksinimler

- PHP 8.1 veya üzeri
- Laravel 10.x, 11.x veya 12.x

## Composer ile Kurulum

```bash
composer require barisdemirhan/laravel-polyglot-model
```

## Migrasyonları Yayınlama

```bash
php artisan vendor:publish --tag="polyglot-model-migrations"
php artisan migrate
```

## Konfigürasyonu Yayınlama (İsteğe Bağlı)

```bash
php artisan vendor:publish --tag="polyglot-model-config"
```

Bu işlem `config/polyglot-model.php` dosyasını oluşturacaktır. Bu dosyada şunları özelleştirebilirsiniz:

- Kaynak ve yedek (fallback) diller
- Desteklenen diller listesi
- Önbellek ayarları (TTL, önek)
- Çeviri tablosu adı
- Özel çeviri modeli
- Olaylar (events) açma/kapama

## Kurulumu Doğrulama

Kurulumdan sonra şunlara sahip olmalısınız:

1. Veritabanınızda bir `translations` tablosu
2. (İsteğe bağlı) Bir `config/polyglot-model.php` yapılandırma dosyası

Artık paketi kullanmaya hazırsınız! [Hızlı Başlangıç](/tr/quick-start) kılavuzuna gidin.
