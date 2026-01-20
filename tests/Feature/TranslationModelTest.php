<?php

declare(strict_types=1);

namespace PolyglotModel\Tests\Feature;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Event;
use PolyglotModel\Events\TranslationCreated;
use PolyglotModel\Events\TranslationDeleted;
use PolyglotModel\Events\TranslationUpdated;
use PolyglotModel\Tests\Post;
use PolyglotModel\Tests\TestCase;

class TranslationModelTest extends TestCase
{
    /** @test */
    public function it_dispatches_event_when_translation_is_created(): void
    {
        Event::fake([TranslationCreated::class]);

        $post = Post::create(['title' => 'Test', 'slug' => 'test']);
        $post->setTranslate('title', 'tr', 'Test Türkçe');

        Event::assertDispatched(TranslationCreated::class);
    }

    /** @test */
    public function it_dispatches_event_when_translation_is_updated(): void
    {
        Event::fake([TranslationUpdated::class]);

        $post = Post::create(['title' => 'Test', 'slug' => 'test']);
        $post->setTranslate('title', 'tr', 'İlk');
        $post->setTranslate('title', 'tr', 'Güncel');

        Event::assertDispatched(TranslationUpdated::class);
    }

    /** @test */
    public function it_dispatches_event_when_translation_is_deleted(): void
    {
        Event::fake([TranslationDeleted::class]);

        $post = Post::create(['title' => 'Test', 'slug' => 'test']);
        $post->setTranslate('title', 'tr', 'Test');
        $post->setTranslate('title', 'tr', null);

        Event::assertDispatched(TranslationDeleted::class);
    }

    /** @test */
    public function it_does_not_dispatch_events_when_disabled(): void
    {
        config(['polyglot-model.events.enabled' => false]);

        Event::fake([TranslationCreated::class]);

        $post = Post::create(['title' => 'Test', 'slug' => 'test']);
        $post->setTranslate('title', 'tr', 'Test Türkçe');

        Event::assertNotDispatched(TranslationCreated::class);
    }

    /** @test */
    public function it_can_search_translatable_field(): void
    {
        $post1 = Post::create(['title' => 'Laravel Tips', 'slug' => 'laravel-tips']);
        $post2 = Post::create(['title' => 'PHP Guide', 'slug' => 'php-guide']);

        $post1->setTranslate('title', 'tr', 'Laravel İpuçları');
        $post2->setTranslate('title', 'tr', 'PHP Rehberi');

        App::setLocale('tr');

        // Search Turkish translations
        $results = Post::searchTranslatable('title', 'İpuçları', 'tr')->get();

        $this->assertCount(1, $results);
        $this->assertEquals($post1->id, $results->first()->id);
    }

    /** @test */
    public function it_can_search_multiple_translatable_fields(): void
    {
        $post = Post::create([
            'title' => 'Test Title',
            'slug' => 'test-slug',
            'content' => 'Some content about Laravel',
        ]);

        $post->setTranslate('title', 'tr', 'Test Başlık');
        $post->setTranslate('content', 'tr', 'Laravel hakkında içerik');

        $results = Post::searchMultipleTranslatable(['title', 'content'], 'Laravel', 'tr')->get();

        $this->assertCount(1, $results);
    }

    /** @test */
    public function it_can_filter_models_with_all_required_translations(): void
    {
        $complete = Post::create(['title' => 'Complete', 'slug' => 'complete']);
        $complete->setTranslate('title', 'tr', 'Tamamlanmış');
        $complete->setTranslate('slug', 'tr', 'tamamlanmis');

        $incomplete = Post::create(['title' => 'Incomplete', 'slug' => 'incomplete']);
        $incomplete->setTranslate('title', 'tr', 'Eksik');
        // slug translation missing

        $results = Post::hasAllRequiredFieldsForLocaleScope('tr')->get();

        $this->assertCount(1, $results);
        $this->assertEquals($complete->id, $results->first()->id);
    }

    /** @test */
    public function it_works_with_eager_loading(): void
    {
        $post = Post::create(['title' => 'Test', 'slug' => 'test']);
        $post->setTranslate('title', 'tr', 'Test TR');
        $post->setTranslate('title', 'de', 'Test DE');
        $post->setTranslate('slug', 'tr', 'test-tr');

        // Fresh query with eager loading
        $loadedPost = Post::with('translations')->find($post->id);

        $this->assertTrue($loadedPost->relationLoaded('translations'));
        $this->assertCount(3, $loadedPost->translations);
        $this->assertEquals('Test TR', $loadedPost->getTranslate('title', 'tr'));
    }
}
