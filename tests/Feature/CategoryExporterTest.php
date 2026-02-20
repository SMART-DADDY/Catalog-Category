<?php

use App\Models\Store;
use Filament\Actions\Exports\Models\Export;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use SmartDaddy\CatalogCategory\Enums\CategoryStatus;
use SmartDaddy\CatalogCategory\Filament\Exports\CategoryExporter;
use SmartDaddy\CatalogCategory\Models\Category;

uses(RefreshDatabase::class);

it('builds a store-scoped export filename', function (): void {
    Carbon::setTestNow('2026-02-20 12:34:56');

    $store = Store::create(['name' => 'My Main Store']);

    Filament::swap(new class($store)
    {
        public function __construct(private readonly Store $store) {}

        public function getTenant(): Store
        {
            return $this->store;
        }
    });

    $exporter = new CategoryExporter(new Export(), [], []);

    expect($exporter->getFileName(new Export()))
        ->toBe('my-main-store-Categories-Export-2026-02-20-123456');

    Carbon::setTestNow();
});

it('falls back to generic store name when tenant is missing', function (): void {
    Carbon::setTestNow('2026-02-20 12:34:56');

    Filament::swap(new class
    {
        public function getTenant(): ?object
        {
            return null;
        }
    });

    $exporter = new CategoryExporter(new Export(), [], []);

    expect($exporter->getFileName(new Export()))
        ->toBe('store-Categories-Export-2026-02-20-123456');

    Carbon::setTestNow();
});

it('defines export columns and completion notifications', function (): void {
    $columns = CategoryExporter::getColumns();

    expect(array_map(fn ($column) => $column->getName(), $columns))
        ->toBe(['name', 'description', 'status']);

    $export = new Export([
        'successful_rows' => 3,
        'total_rows' => 5,
    ]);

    expect(CategoryExporter::getCompletedNotificationBody($export))
        ->toBe('Your category export has completed and 3 rows exported. 2 rows failed to export.');

    $allSuccessful = new Export([
        'successful_rows' => 1,
        'total_rows' => 1,
    ]);

    expect(CategoryExporter::getCompletedNotificationBody($allSuccessful))
        ->toBe('Your category export has completed and 1 row exported.');
});

it('exports formatted row values including enum status', function (): void {
    $store = Store::create(['name' => 'Main Store']);

    $category = Category::create([
        'store_id' => $store->id,
        'name' => 'Bakery',
        'description' => 'Fresh breads',
        'status' => CategoryStatus::Active,
    ]);

    $exporter = new CategoryExporter(
        new Export(),
        [
            'name' => 'Name',
            'description' => 'Description',
            'status' => 'Status',
        ],
        [],
    );

    expect($exporter($category))->toBe([
        'Bakery',
        'Fresh breads',
        'active',
    ]);
});
