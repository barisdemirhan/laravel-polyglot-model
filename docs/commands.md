# Artisan Commands

## Translation Statistics

View statistics about your translations:

```bash
php artisan polyglot:stats
```

Output:

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

### Filter by Model

```bash
php artisan polyglot:stats --model="App\\Models\\Post"
```

### Filter by Locale

```bash
php artisan polyglot:stats --locale=tr
```

## Clean Orphaned Translations

Remove translations where the parent model no longer exists:

```bash
php artisan polyglot:clean-orphaned
```

### Dry Run

Preview what would be deleted without actually deleting:

```bash
php artisan polyglot:clean-orphaned --dry-run
```

Output:

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

### Scheduled Cleanup

Add to your `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule): void
{
    // Clean orphaned translations weekly
    $schedule->command('polyglot:clean-orphaned')
        ->weekly()
        ->sundays()
        ->at('03:00');
}
```
