<?php

declare(strict_types=1);

namespace PolyglotModel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TranslationStatsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'polyglot:stats
                            {--model= : Filter by specific model class}
                            {--locale= : Filter by specific locale}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display translation statistics';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $tableName = config('polyglot-model.table_name', 'translations');
        $modelFilter = $this->option('model');
        $localeFilter = $this->option('locale');

        $this->info('📊 Translation Statistics');
        $this->line('');

        // Total translations
        $query = DB::table($tableName);

        if ($modelFilter) {
            $query->where('translatable_type', $modelFilter);
        }

        if ($localeFilter) {
            $query->where('locale', $localeFilter);
        }

        $totalTranslations = $query->count();
        $this->line("Total translations: <info>{$totalTranslations}</info>");
        $this->line('');

        // By model
        $this->info('📦 Translations by Model:');
        $byModel = DB::table($tableName)
            ->select('translatable_type', DB::raw('count(*) as count'))
            ->when($localeFilter, fn ($q) => $q->where('locale', $localeFilter))
            ->groupBy('translatable_type')
            ->orderByDesc('count')
            ->get();

        if ($byModel->isEmpty()) {
            $this->line('  No translations found.');
        } else {
            $modelData = $byModel->map(fn ($row) => [
                'model' => class_basename($row->translatable_type),
                'full_class' => $row->translatable_type,
                'count' => $row->count,
            ])->toArray();

            $this->table(['Model', 'Full Class', 'Count'], $modelData);
        }

        $this->line('');

        // By locale
        $this->info('🌍 Translations by Locale:');
        $byLocale = DB::table($tableName)
            ->select('locale', DB::raw('count(*) as count'))
            ->when($modelFilter, fn ($q) => $q->where('translatable_type', $modelFilter))
            ->groupBy('locale')
            ->orderByDesc('count')
            ->get();

        if ($byLocale->isEmpty()) {
            $this->line('  No translations found.');
        } else {
            $localeData = $byLocale->map(fn ($row) => [
                'locale' => $row->locale,
                'count' => $row->count,
            ])->toArray();

            $this->table(['Locale', 'Count'], $localeData);
        }

        $this->line('');

        // By field
        $this->info('📝 Translations by Field:');
        $byField = DB::table($tableName)
            ->select('field', DB::raw('count(*) as count'))
            ->when($modelFilter, fn ($q) => $q->where('translatable_type', $modelFilter))
            ->when($localeFilter, fn ($q) => $q->where('locale', $localeFilter))
            ->groupBy('field')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        if ($byField->isEmpty()) {
            $this->line('  No translations found.');
        } else {
            $fieldData = $byField->map(fn ($row) => [
                'field' => $row->field,
                'count' => $row->count,
            ])->toArray();

            $this->table(['Field', 'Count'], $fieldData);
        }

        // Recent translations
        $this->line('');
        $this->info('🕐 Recent Translations (last 5):');
        $recent = DB::table($tableName)
            ->select('translatable_type', 'translatable_id', 'field', 'locale', 'updated_at')
            ->when($modelFilter, fn ($q) => $q->where('translatable_type', $modelFilter))
            ->when($localeFilter, fn ($q) => $q->where('locale', $localeFilter))
            ->orderByDesc('updated_at')
            ->limit(5)
            ->get();

        if ($recent->isEmpty()) {
            $this->line('  No recent translations.');
        } else {
            $recentData = $recent->map(fn ($row) => [
                'model' => class_basename($row->translatable_type),
                'id' => $row->translatable_id,
                'field' => $row->field,
                'locale' => $row->locale,
                'updated' => $row->updated_at,
            ])->toArray();

            $this->table(['Model', 'ID', 'Field', 'Locale', 'Updated'], $recentData);
        }

        return self::SUCCESS;
    }
}
