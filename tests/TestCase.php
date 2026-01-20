<?php

declare(strict_types=1);

namespace PolyglotModel\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as Orchestra;
use PolyglotModel\PolyglotModelServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase();
    }

    protected function getPackageProviders($app): array
    {
        return [
            PolyglotModelServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $app['config']->set('polyglot-model.source_locale', 'en');
        $app['config']->set('polyglot-model.fallback_locale', 'en');
        $app['config']->set('polyglot-model.supported_locales', ['en', 'tr', 'de', 'es']);
        $app['config']->set('polyglot-model.cache.enabled', false);
        $app['config']->set('polyglot-model.events.enabled', true);
    }

    protected function setUpDatabase(): void
    {
        // Create translations table
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->morphs('translatable');
            $table->string('field');
            $table->string('locale', 10);
            $table->text('value');
            $table->timestamps();

            $table->unique(
                ['translatable_type', 'translatable_id', 'field', 'locale'],
                'translations_unique_idx'
            );
        });

        // Create test model table
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->nullable();
            $table->text('content')->nullable();
            $table->boolean('translation_excluded')->default(false);
            $table->timestamps();
        });
    }
}
