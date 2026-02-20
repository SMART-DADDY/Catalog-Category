<?php

use App\Models\Store;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use SmartDaddy\CatalogCategory\Enums\CategoryStatus;
use SmartDaddy\CatalogCategory\Models\Category;

uses(RefreshDatabase::class);

it('creates expected schema and applies default status', function (): void {
    expect(Schema::hasTable('categories'))->toBeTrue()
        ->and(Schema::hasColumns('categories', [
            'id',
            'store_id',
            'name',
            'description',
            'status',
            'deleted_at',
            'created_at',
            'updated_at',
        ]))->toBeTrue();

    $store = Store::create(['name' => 'Main Store']);

    $category = Category::create([
        'store_id' => $store->id,
        'name' => 'Beverages',
        'description' => 'Cold and hot drinks',
    ]);
    $category->refresh();

    expect($category->status)->toBe(CategoryStatus::Active)
        ->and($category->getRawOriginal('status'))->toBe(CategoryStatus::Active->value);
});

it('defines fillable fields, casts and store relation', function (): void {
    $store = Store::create(['name' => 'Main Store']);

    $category = Category::create([
        'store_id' => $store->id,
        'name' => 'Bakery',
        'description' => 'Fresh breads',
        'status' => CategoryStatus::Draft,
    ]);

    expect($category->getFillable())->toBe(['store_id', 'name', 'description', 'status'])
        ->and($category->status)->toBe(CategoryStatus::Draft)
        ->and($category->store())->toBeInstanceOf(BelongsTo::class)
        ->and($category->store()->getRelated()::class)->toBe(Store::class)
        ->and(class_uses_recursive(Category::class))->toContain(SoftDeletes::class);
});

it('cascades category deletion when store is removed', function (): void {
    $store = Store::create(['name' => 'Main Store']);

    $category = Category::create([
        'store_id' => $store->id,
        'name' => 'Dairy',
        'description' => 'Milk and cheese',
        'status' => CategoryStatus::Inactive,
    ]);

    $store->delete();

    expect(Category::query()->whereKey($category->id)->exists())->toBeFalse();
});
