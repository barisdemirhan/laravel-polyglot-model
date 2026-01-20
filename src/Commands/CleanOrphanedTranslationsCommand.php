<?php

declare(strict_types=1);

namespace PolyglotModel\Commands;

use Illuminate\Console\Command;

class CleanOrphanedTranslationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'polyglot:clean-orphaned
                            {--dry-run : Show what would be deleted without actually deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean orphaned translations where the parent model no longer exists';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Scanning for orphaned translations...');

        $translationModel = config('polyglot-model.model');
        $translations = $translationModel::all();
        $orphanedCount = 0;
        $orphanedIds = [];

        $this->output->progressStart($translations->count());

        foreach ($translations as $translation) {
            $modelClass = $translation->translatable_type;

            if (! class_exists($modelClass)) {
                $orphanedIds[] = $translation->id;
                $orphanedCount++;
                $this->output->progressAdvance();

                continue;
            }

            try {
                $model = new $modelClass;
                $exists = $modelClass::where($model->getKeyName(), $translation->translatable_id)->exists();

                if (! $exists) {
                    $orphanedIds[] = $translation->id;
                    $orphanedCount++;
                }
            } catch (\Exception $e) {
                $this->warn("Error checking {$modelClass}: {$e->getMessage()}");
            }

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();

        if ($orphanedCount === 0) {
            $this->info('No orphaned translations found.');

            return self::SUCCESS;
        }

        $this->line('');
        $this->info("Found {$orphanedCount} orphaned translation(s).");

        if ($this->option('dry-run')) {
            $this->warn('Dry run mode - no records were deleted.');
            $this->table(
                ['ID', 'Model Type', 'Model ID', 'Field', 'Locale'],
                $translationModel::whereIn('id', $orphanedIds)->get()->map(fn ($t) => [
                    $t->id,
                    $t->translatable_type,
                    $t->translatable_id,
                    $t->field,
                    $t->locale,
                ])->toArray()
            );

            return self::SUCCESS;
        }

        if ($this->confirm("Do you want to delete {$orphanedCount} orphaned translation(s)?", true)) {
            $translationModel::whereIn('id', $orphanedIds)->delete();
            $this->info("Successfully deleted {$orphanedCount} orphaned translation(s).");
        } else {
            $this->info('Operation cancelled.');
        }

        return self::SUCCESS;
    }
}
