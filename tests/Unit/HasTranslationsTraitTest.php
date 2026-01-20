<?php

declare(strict_types=1);

namespace PolyglotModel\Tests\Unit;

use Illuminate\Support\Facades\App;
use PolyglotModel\Exceptions\InvalidLocaleException;
use PolyglotModel\Exceptions\TranslationException;
use PolyglotModel\Tests\Post;
use PolyglotModel\Tests\TestCase;

class HasTranslationsTraitTest extends TestCase
{
    protected Post $post;

    protected function setUp(): void
    {
        parent::setUp();

        $this->post = Post::create([
            'title' => 'English Title',
            'slug' => 'english-slug',
            'content' => 'English content',
        ]);
    }

    /** @test */
    public function it_returns_original_value_for_source_locale(): void
    {
        App::setLocale('en');

        $this->assertEquals('English Title', $this->post->title);
        $this->assertEquals('english-slug', $this->post->slug);
    }

    /** @test */
    public function it_can_set_and_get_translation(): void
    {
        $this->post->setTranslate('title', 'tr', 'Türkçe Başlık');

        $this->assertEquals('Türkçe Başlık', $this->post->getTranslate('title', 'tr'));
    }

    /** @test */
    public function it_returns_translation_via_magic_getter(): void
    {
        $this->post->setTranslate('title', 'tr', 'Türkçe Başlık');

        App::setLocale('tr');

        $this->assertEquals('Türkçe Başlık', $this->post->title);
    }

    /** @test */
    public function it_falls_back_to_fallback_locale(): void
    {
        // No German translation exists, should fall back to English
        App::setLocale('de');

        $this->assertEquals('English Title', $this->post->title);
    }

    /** @test */
    public function it_can_check_if_translation_exists(): void
    {
        $this->post->setTranslate('title', 'tr', 'Türkçe Başlık');

        $this->assertTrue($this->post->hasTranslation('title', 'tr'));
        $this->assertFalse($this->post->hasTranslation('title', 'de'));
        $this->assertTrue($this->post->hasTranslation('title', 'en')); // Source has value
    }

    /** @test */
    public function it_can_check_if_model_has_any_translations(): void
    {
        $this->assertFalse($this->post->hasAnyTranslations());

        $this->post->setTranslate('title', 'tr', 'Türkçe Başlık');

        $this->assertTrue($this->post->hasAnyTranslations());
    }

    /** @test */
    public function it_can_check_required_fields_for_locale(): void
    {
        // Required fields: title, slug
        $this->assertFalse($this->post->hasAllRequiredFieldsForLocale('tr'));

        $this->post->setTranslate('title', 'tr', 'Türkçe Başlık');
        $this->assertFalse($this->post->hasAllRequiredFieldsForLocale('tr'));

        $this->post->setTranslate('slug', 'tr', 'turkce-slug');
        $this->assertTrue($this->post->hasAllRequiredFieldsForLocale('tr'));
    }

    /** @test */
    public function it_can_get_all_translations(): void
    {
        $this->post->setTranslate('title', 'tr', 'Türkçe Başlık');
        $this->post->setTranslate('title', 'de', 'Deutscher Titel');

        $translations = $this->post->getAllTranslations();

        $this->assertArrayHasKey('title', $translations);
        $this->assertEquals('English Title', $translations['title']['en']);
        $this->assertEquals('Türkçe Başlık', $translations['title']['tr']);
        $this->assertEquals('Deutscher Titel', $translations['title']['de']);
    }

    /** @test */
    public function it_can_get_field_translations(): void
    {
        $this->post->setTranslate('title', 'tr', 'Türkçe Başlık');
        $this->post->setTranslate('title', 'de', 'Deutscher Titel');

        $translations = $this->post->getFieldTranslations('title');

        $this->assertEquals([
            'en' => 'English Title',
            'tr' => 'Türkçe Başlık',
            'de' => 'Deutscher Titel',
        ], $translations);
    }

    /** @test */
    public function it_can_get_missing_locales(): void
    {
        $this->post->setTranslate('title', 'tr', 'Türkçe Başlık');

        $missing = $this->post->getMissingLocales('title');

        $this->assertContains('de', $missing);
        $this->assertContains('es', $missing);
        $this->assertNotContains('en', $missing);
        $this->assertNotContains('tr', $missing);
    }

    /** @test */
    public function it_can_set_preferred_language(): void
    {
        $this->post->setTranslate('title', 'tr', 'Türkçe Başlık');

        App::setLocale('en');

        $title = $this->post->setPreferredLanguage('tr')->title;

        $this->assertEquals('Türkçe Başlık', $title);
    }

    /** @test */
    public function it_excludes_translation_when_model_is_excluded(): void
    {
        $excludedPost = Post::create([
            'title' => 'Excluded Title',
            'slug' => 'excluded-slug',
            'translation_excluded' => true,
        ]);

        $excludedPost->setTranslate('title', 'tr', 'Should Not Show');

        App::setLocale('tr');

        // Should return original, not translation
        $this->assertEquals('Excluded Title', $excludedPost->title);
    }

    /** @test */
    public function it_deletes_translations_when_model_is_deleted(): void
    {
        $this->post->setTranslate('title', 'tr', 'Türkçe Başlık');

        $this->assertTrue($this->post->hasAnyTranslations());

        $postId = $this->post->id;
        $this->post->delete();

        // Check that translations are deleted
        $translationModel = config('polyglot-model.model');
        $remaining = $translationModel::where('translatable_id', $postId)->count();

        $this->assertEquals(0, $remaining);
    }

    /** @test */
    public function it_throws_exception_for_non_translatable_field(): void
    {
        $this->expectException(TranslationException::class);

        $this->post->setTranslate('non_existent_field', 'tr', 'Value');
    }

    /** @test */
    public function it_throws_exception_when_setting_translation_on_unpersisted_model(): void
    {
        $this->expectException(TranslationException::class);

        $newPost = new Post(['title' => 'New Post']);
        $newPost->setTranslate('title', 'tr', 'Yeni Gönderi');
    }

    /** @test */
    public function it_validates_locale_in_strict_mode(): void
    {
        config(['polyglot-model.strict_locale' => true]);

        $this->expectException(InvalidLocaleException::class);

        $this->post->setTranslate('title', 'invalid_locale', 'Value');
    }

    /** @test */
    public function it_can_delete_translation_by_setting_null(): void
    {
        $this->post->setTranslate('title', 'tr', 'Türkçe Başlık');
        $this->assertTrue($this->post->hasTranslation('title', 'tr'));

        $this->post->setTranslate('title', 'tr', null);
        $this->assertFalse($this->post->hasTranslation('title', 'tr'));
    }

    /** @test */
    public function it_can_update_existing_translation(): void
    {
        $this->post->setTranslate('title', 'tr', 'İlk Başlık');
        $this->assertEquals('İlk Başlık', $this->post->getTranslate('title', 'tr'));

        $this->post->setTranslate('title', 'tr', 'Güncellenmiş Başlık');
        $this->assertEquals('Güncellenmiş Başlık', $this->post->getTranslate('title', 'tr'));
    }
}
