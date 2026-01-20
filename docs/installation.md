# Installation

## Requirements

- PHP 8.1 or higher
- Laravel 10.x, 11.x, or 12.x

## Install via Composer

```bash
composer require barisdemirhan/laravel-polyglot-model
```

## Publish Migrations

```bash
php artisan vendor:publish --tag="polyglot-model-migrations"
php artisan migrate
```

## Publish Configuration (Optional)

```bash
php artisan vendor:publish --tag="polyglot-model-config"
```

This will create a `config/polyglot-model.php` file where you can customize:

- Source and fallback locales
- Supported locales list
- Cache settings (TTL, prefix)
- Translation table name
- Custom translation model
- Events toggle

## Verify Installation

After installation, you should have:

1. A `translations` table in your database
2. (Optional) A `config/polyglot-model.php` configuration file

You're now ready to use the package! Head to the [Quick Start](/quick-start) guide.
