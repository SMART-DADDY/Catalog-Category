<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Filament\Support\Icons\Heroicon;
use SmartDaddy\CatalogCategory\Filament\Resources\Categories\CategoryResource;
use SmartDaddy\CatalogCategory\Models\Category;
use App\Models\Store;

uses(RefreshDatabase::class);

it('exposes expected global search metadata', function (): void {
    $record = new Category([
        'name' => 'Frozen Foods',
        'description' => 'Frozen products',
    ]);

    expect(CategoryResource::getGloballySearchableAttributes())->toBe(['name', 'description'])
        ->and((string) CategoryResource::getGlobalSearchResultTitle($record))->toBe('Frozen Foods')
        ->and(CategoryResource::getGlobalSearchResultDetails($record))->toBe([
            'Description' => 'Frozen products',
        ]);
});

it('defines expected resource pages and removes soft delete scope for binding query', function (): void {
    $pages = CategoryResource::getPages();

    expect(array_keys($pages))->toBe(['index', 'create', 'view', 'edit']);

    $store = Store::create(['name' => 'Main Store']);

    $category = Category::create([
        'store_id' => $store->id,
        'name' => 'Soft Deleted',
        'description' => 'to test binding query',
    ]);

    $category->delete();

    $query = CategoryResource::getRecordRouteBindingEloquentQuery();

    expect($query->whereKey($category->id)->first())->not->toBeNull();
});

it('exposes expected navigation metadata', function (): void {
    expect(CategoryResource::getNavigationIcon())->toBe(Heroicon::OutlinedSquares2x2)
        ->and(CategoryResource::getActiveNavigationIcon())->toBe(Heroicon::Squares2x2)
        ->and(CategoryResource::getNavigationGroup())->toBe('Catalog')
        ->and(CategoryResource::getNavigationSort())->toBe(4);
});
