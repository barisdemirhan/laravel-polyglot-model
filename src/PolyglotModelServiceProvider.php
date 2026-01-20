<?php

declare(strict_types=1);

namespace PolyglotModel;

use PolyglotModel\Commands\CleanOrphanedTranslationsCommand;
use PolyglotModel\Commands\TranslationStatsCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class PolyglotModelServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('polyglot-model')
            ->hasConfigFile()
            ->hasMigration('create_translations_table')
            ->hasCommands([
                CleanOrphanedTranslationsCommand::class,
                TranslationStatsCommand::class,
            ]);
    }

    public function packageRegistered(): void
    {
        $this->app->singleton('polyglot-model', function () {
            return new PolyglotModelManager;
        });
    }
}
