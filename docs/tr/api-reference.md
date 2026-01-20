# API Referansı

## HasTranslations Trait Metotları

### Çeviri Metotları

| Metot                  | Parametreler                                    | Dönüş     | Açıklama                                |
| ---------------------- | ----------------------------------------------- | --------- | --------------------------------------- |
| `getTranslate`         | `string $field, ?string $locale`                | `?string` | Alan için çeviriyi getir                |
| `setTranslate`         | `string $field, string $locale, ?string $value` | `void`    | Çeviri ayarla (silmek için null)        |
| `hasTranslation`       | `string $field, ?string $locale`                | `bool`    | Çevirinin varlığını kontrol et          |
| `hasAnyTranslations`   |                                                 | `bool`    | Herhangi bir çeviri var mı kontrol et   |
| `getAllTranslations`   |                                                 | `array`   | Tüm çevirileri iç içe dizi olarak getir |
| `getFieldTranslations` | `string $field`                                 | `array`   | Bir alan için tüm dilleri getir         |
| `getMissingLocales`    | `string $field`                                 | `array`   | Çevirisi olmayan dilleri getir          |

### Konfigürasyon Metotları

| Metot                                | Dönüş   | Açıklama                                       |
| ------------------------------------ | ------- | ---------------------------------------------- |
| `getTranslatableFields`              | `array` | Çevrilebilir alan adlarını getir               |
| `getRequiredTranslatableFields`      | `array` | Zorunlu alan adlarını getir                    |
| `isFieldTranslatable(string $field)` | `bool`  | Alanın çevrilebilirliğini kontrol et           |
| `isTranslationExcluded`              | `bool`  | Modelin hariç tutulup tutulmadığını kontrol et |

### Dil (Locale) Metotları

| Metot                           | Parametreler      | Dönüş     | Açıklama                             |
| ------------------------------- | ----------------- | --------- | ------------------------------------ |
| `setPreferredLanguage`          | `string $locale`  | `static`  | Örnek için tercih edilen dili ayarla |
| `getPreferredLanguage`          |                   | `?string` | Tercih edilen dili getir             |
| `hasAllRequiredFieldsForLocale` | `?string $locale` | `bool`    | Zorunlu alanları kontrol et          |

### Önbellek Metotları

| Metot                   | Açıklama                                            |
| ----------------------- | --------------------------------------------------- |
| `clearTranslationCache` | Model için tüm önbelleğe alınmış çevirileri temizle |

## Sorgu Kapsamları (Query Scopes)

| Kapsam                               | Parametreler                                                       | Açıklama                            |
| ------------------------------------ | ------------------------------------------------------------------ | ----------------------------------- |
| `searchTranslatable`                 | `string $field, string $search, ?string $locale`                   | Alanda arama yap                    |
| `searchMultipleTranslatable`         | `array $fields, string $search, ?string $locale`                   | Birden fazla alanda arama yap       |
| `searchTranslatableInLanguage`       | `string $field, string $search, string $locale`                    | Belirli bir dilde arama yap         |
| `hasAllRequiredFieldsForLocaleScope` | `?string $locale`                                                  | Tam çevirisi olanlara göre filtrele |
| `searchRelationTranslatable`         | `string $relation, string $field, string $search, ?string $locale` | İlişkide arama yap                  |

## Çeviri Modeli (Translation Model)

```php
use PolyglotModel\Models\Translation;

// Çevirileri doğrudan sorgula
$translations = Translation::forLocale('tr')->get();

// Ebeveyn modele eriş
$translation->translatable; // Post, Page vb. döndürür

// Yetim (sahipsiz) kayıtları temizle
Translation::cleanOrphaned();
```

### Model Özellikleri

| Özellik             | Tip      | Açıklama             |
| ------------------- | -------- | -------------------- |
| `translatable_type` | `string` | Ebeveyn model sınıfı |
| `translatable_id`   | `int`    | Ebeveyn model ID     |
| `field`             | `string` | Alan adı             |
| `locale`            | `string` | Dil kodu             |
| `value`             | `string` | Çevrilmiş değer      |

### Model Kapsamları

| Kapsam               | Açıklama                     |
| -------------------- | ---------------------------- |
| `forModel($model)`   | Ebeveyn modele göre filtrele |
| `forField($field)`   | Alan adına göre filtrele     |
| `forLocale($locale)` | Dile göre filtrele           |

## Olaylar (Events)

| Olay                 | Özellik                    | Açıklama                 |
| -------------------- | -------------------------- | ------------------------ |
| `TranslationCreated` | `Translation $translation` | Oluşturulunca tetiklenir |
| `TranslationUpdated` | `Translation $translation` | Güncellenince tetiklenir |
| `TranslationDeleted` | `Translation $translation` | Silinince tetiklenir     |

## İstisnalar (Exceptions)

| İstisna                  | Statik Metotlar                                                          |
| ------------------------ | ------------------------------------------------------------------------ |
| `TranslationException`   | `fieldNotTranslatable()`, `modelNotPersisted()`, `translationNotFound()` |
| `InvalidLocaleException` | `unsupportedLocale()`, `emptyLocale()`                                   |
