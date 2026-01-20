# Changelog

All notable changes to `laravel-model-translations` will be documented in this file.

## [Unreleased]

## [1.0.0] - 2026-01-20

### Added

- Initial release
- `HasTranslations` trait for Eloquent models
- Polymorphic `Translation` model
- Cache support with configurable TTL
- Fallback locale support
- Magic getter/setter for translated attributes
- Query scopes for searching translations
- Artisan commands: `translations:stats`, `translations:clean-orphaned`
- Events: `TranslationCreated`, `TranslationUpdated`, `TranslationDeleted`
- Full test coverage
