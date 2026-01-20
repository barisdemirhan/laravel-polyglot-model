# Artisan Komutları

## Çeviri İstatistikleri

Çevirileriniz hakkında istatistikleri görüntüleyin:

```bash
php artisan polyglot:stats
```

Çıktı:

```
📊 Translation Statistics

Total translations: 1,234

📦 Translations by Model:
+-------+------------------+-------+
| Model | Full Class       | Count |
+-------+------------------+-------+
| Post  | App\Models\Post  | 850   |
| Page  | App\Models\Page  | 384   |
+-------+------------------+-------+

🌍 Translations by Locale:
+--------+-------+
| Locale | Count |
+--------+-------+
| tr     | 456   |
| de     | 389   |
| es     | 389   |
+--------+-------+

📝 Translations by Field:
+----------+-------+
| Field    | Count |
+----------+-------+
| title    | 500   |
| content  | 450   |
| slug     | 284   |
+----------+-------+

🕐 Recent Translations (last 5):
+-------+----+-------+--------+---------------------+
| Model | ID | Field | Locale | Updated             |
+-------+----+-------+--------+---------------------+
| Post  | 42 | title | tr     | 2026-01-20 10:30:00 |
+-------+----+-------+--------+---------------------+
```

### Modele Göre Filtreleme

```bash
php artisan polyglot:stats --model="App\\Models\\Post"
```

### Dile Göre Filtreleme

```bash
php artisan polyglot:stats --locale=tr
```

## Yetim (Orphaned) Çevirileri Temizleme

Ebeveyn modeli artık mevcut olmayan çevirileri kaldırın:

```bash
php artisan polyglot:clean-orphaned
```

### Deneme Modu (Dry Run)

Gerçekten silmeden nelerin silineceğini önizleyin:

```bash
php artisan polyglot:clean-orphaned --dry-run
```

Çıktı:

```
Scanning for orphaned translations...
Found 15 orphaned translation(s).
Dry run mode - no records were deleted.

+----+------------------+----------+-------+--------+
| ID | Model Type       | Model ID | Field | Locale |
+----+------------------+----------+-------+--------+
| 45 | App\Models\Post  | 999      | title | tr     |
| 46 | App\Models\Post  | 999      | slug  | tr     |
+----+------------------+----------+-------+--------+
```

### Zamanlanmış Temizlik

`app/Console/Kernel.php` dosyanıza ekleyin:

```php
protected function schedule(Schedule $schedule): void
{
    // Yetim çevirileri haftalık olarak temizle
    $schedule->command('polyglot:clean-orphaned')
        ->weekly()
        ->sundays()
        ->at('03:00');
}
```
