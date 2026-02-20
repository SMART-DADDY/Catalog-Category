# smart-daddy/catalog-category

Category module package for Laravel + Filament applications.

This package provides:
- `Category` model
- `CategoryStatus` enum
- category migration
- Filament Category resource (pages, table, form, infolist)
- category importer and exporter

## Requirements

- PHP 8.2+
- Laravel 12+
- Filament 5+

## Installation

```bash
composer require smart-daddy/catalog-category
```

## Migration

```bash
php artisan migrate
```

The package registers its migration through `CatalogCategoryServiceProvider`.

## Filament integration

Register package resources in your panel provider:

```php
->discoverResources(
    in: base_path('vendor/smart-daddy/catalog-category/src/Filament/Resources'),
    for: 'SmartDaddy\\CatalogCategory\\Filament\\Resources'
)
```

If your project uses an in-repo path package, use your local package path instead of `vendor/...`.

## Usage

Use the model directly:

```php
use SmartDaddy\CatalogCategory\Models\Category;
use SmartDaddy\CatalogCategory\Enums\CategoryStatus;

$category = Category::query()->create([
    'store_id' => 1,
    'name' => 'Beverages',
    'description' => 'Drinks and juices',
    'status' => CategoryStatus::Active,
]);
```

## Optional User Activity integration

If `smart-daddy/user-activity` is installed, `Category` will automatically use `TracksUserActivity`.
If it is not installed, `Category` still works normally without activity tracking.

## Package identity

- Composer name: `smart-daddy/catalog-category`
- Root namespace: `SmartDaddy\\CatalogCategory\\`
- Service provider: `SmartDaddy\\CatalogCategory\\CatalogCategoryServiceProvider`

## Release checklist

1. Push latest changes to `main`.
2. Tag and push release:

```bash
git tag v0.1.0
git push origin v0.1.0
```

3. Submit repository to Packagist.
4. Enable Packagist auto-update webhook.

## License

MIT
