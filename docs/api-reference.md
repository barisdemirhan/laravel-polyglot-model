# API Reference

## HasTranslations Trait Methods

### Translation Methods

| Method                 | Parameters                                      | Returns   | Description                          |
| ---------------------- | ----------------------------------------------- | --------- | ------------------------------------ |
| `getTranslate`         | `string $field, ?string $locale`                | `?string` | Get translation for field            |
| `setTranslate`         | `string $field, string $locale, ?string $value` | `void`    | Set translation (null to delete)     |
| `hasTranslation`       | `string $field, ?string $locale`                | `bool`    | Check if translation exists          |
| `hasAnyTranslations`   |                                                 | `bool`    | Check for any translations           |
| `getAllTranslations`   |                                                 | `array`   | Get all translations as nested array |
| `getFieldTranslations` | `string $field`                                 | `array`   | Get all locales for a field          |
| `getMissingLocales`    | `string $field`                                 | `array`   | Get locales without translation      |

### Configuration Methods

| Method                               | Returns | Description                    |
| ------------------------------------ | ------- | ------------------------------ |
| `getTranslatableFields`              | `array` | Get translatable field names   |
| `getRequiredTranslatableFields`      | `array` | Get required field names       |
| `isFieldTranslatable(string $field)` | `bool`  | Check if field is translatable |
| `isTranslationExcluded`              | `bool`  | Check if model is excluded     |

### Locale Methods

| Method                          | Parameters        | Returns   | Description                       |
| ------------------------------- | ----------------- | --------- | --------------------------------- |
| `setPreferredLanguage`          | `string $locale`  | `static`  | Set preferred locale for instance |
| `getPreferredLanguage`          |                   | `?string` | Get preferred locale              |
| `hasAllRequiredFieldsForLocale` | `?string $locale` | `bool`    | Check required fields             |

### Cache Methods

| Method                  | Description                             |
| ----------------------- | --------------------------------------- |
| `clearTranslationCache` | Clear all cached translations for model |

## Query Scopes

| Scope                                | Parameters                                                         | Description                     |
| ------------------------------------ | ------------------------------------------------------------------ | ------------------------------- |
| `searchTranslatable`                 | `string $field, string $search, ?string $locale`                   | Search in field                 |
| `searchMultipleTranslatable`         | `array $fields, string $search, ?string $locale`                   | Search multiple fields          |
| `searchTranslatableInLanguage`       | `string $field, string $search, string $locale`                    | Search in specific locale       |
| `hasAllRequiredFieldsForLocaleScope` | `?string $locale`                                                  | Filter by complete translations |
| `searchRelationTranslatable`         | `string $relation, string $field, string $search, ?string $locale` | Search in relation              |

## Translation Model

```php
use PolyglotModel\Models\Translation;

// Query translations directly
$translations = Translation::forLocale('tr')->get();

// Access parent model
$translation->translatable; // Returns Post, Page, etc.

// Clean orphaned
Translation::cleanOrphaned();
```

### Model Properties

| Property            | Type     | Description        |
| ------------------- | -------- | ------------------ |
| `translatable_type` | `string` | Parent model class |
| `translatable_id`   | `int`    | Parent model ID    |
| `field`             | `string` | Field name         |
| `locale`            | `string` | Locale code        |
| `value`             | `string` | Translated value   |

### Model Scopes

| Scope                | Description            |
| -------------------- | ---------------------- |
| `forModel($model)`   | Filter by parent model |
| `forField($field)`   | Filter by field name   |
| `forLocale($locale)` | Filter by locale       |

## Events

| Event                | Property                   | Description          |
| -------------------- | -------------------------- | -------------------- |
| `TranslationCreated` | `Translation $translation` | Dispatched on create |
| `TranslationUpdated` | `Translation $translation` | Dispatched on update |
| `TranslationDeleted` | `Translation $translation` | Dispatched on delete |

## Exceptions

| Exception                | Static Methods                                                           |
| ------------------------ | ------------------------------------------------------------------------ |
| `TranslationException`   | `fieldNotTranslatable()`, `modelNotPersisted()`, `translationNotFound()` |
| `InvalidLocaleException` | `unsupportedLocale()`, `emptyLocale()`                                   |
