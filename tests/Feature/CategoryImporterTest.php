<?php

use App\Models\Store;
use Filament\Actions\Imports\Models\Import;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules\Enum;
use SmartDaddy\CatalogCategory\Enums\CategoryStatus;
use SmartDaddy\CatalogCategory\Filament\Imports\CategoryImporter;
use SmartDaddy\CatalogCategory\Models\Category;

uses(RefreshDatabase::class);

function makeImporter(array $options): CategoryImporter
{
    return new class(new Import(), [], $options) extends CategoryImporter {
        public function setTestData(array $data): static
        {
            $this->data = $data;

            return $this;
        }
    };
}

it('resolves record by name and store from options', function (): void {
    $store = Store::create(['name' => 'Main Store']);

    $existing = Category::create([
        'store_id' => $store->id,
        'name' => 'Beverages',
        'description' => 'Existing',
        'status' => CategoryStatus::Active,
    ]);

    $importer = makeImporter(['store_id' => $store->id])
        ->setTestData([
            'name' => 'Beverages',
            'description' => 'Updated',
            'status' => CategoryStatus::Draft->value,
        ]);

    $resolved = $importer->resolveRecord();

    expect($resolved->is($existing))->toBeTrue()
        ->and($resolved->store_id)->toBe($store->id);
});

it('resolves record with tenant store when option is missing', function (): void {
    $store = Store::create(['name' => 'Tenant Store']);

    Filament::swap(new class($store)
    {
        public function __construct(private readonly Store $store) {}

        public function getTenant(): Store
        {
            return $this->store;
        }
    });

    $importer = makeImporter([])
        ->setTestData([
            'name' => 'Dairy',
            'description' => 'Milk products',
            'status' => CategoryStatus::Active->value,
        ]);

    $resolved = $importer->resolveRecord();

    expect($resolved->exists)->toBeFalse()
        ->and($resolved->name)->toBe('Dairy')
        ->and($resolved->store_id)->toBe($store->id);
});

it('fails when store cannot be resolved for import', function (): void {
    Filament::swap(new class
    {
        public function getTenant(): ?object
        {
            return null;
        }
    });

    $importer = makeImporter([])
        ->setTestData([
            'name' => 'Bakery',
            'description' => 'Bread',
            'status' => CategoryStatus::Draft->value,
        ]);

    $importer->resolveRecord();
})->throws(ValidationException::class, 'Cannot resolve store for this import job');

it('builds import completion notifications with failed rows', function (): void {
    $import = new Import([
        'successful_rows' => 8,
        'total_rows' => 10,
    ]);

    expect(CategoryImporter::getCompletedNotificationBody($import))
        ->toBe('Your category import has completed and 8 rows imported. 2 rows failed to import.');

    $allSuccessful = new Import([
        'successful_rows' => 1,
        'total_rows' => 1,
    ]);

    expect(CategoryImporter::getCompletedNotificationBody($allSuccessful))
        ->toBe('Your category import has completed and 1 row imported.');
});

it('defines expected import columns and status validation', function (): void {
    $columns = CategoryImporter::getColumns();

    expect(array_map(fn ($column) => $column->getName(), $columns))
        ->toBe(['name', 'description', 'status'])
        ->and($columns[2]->isMappingRequired())->toBeTrue()
        ->and($columns[2]->getDataValidationRules())->toHaveCount(2)
        ->and($columns[2]->getDataValidationRules()[0])->toBe('required')
        ->and($columns[2]->getDataValidationRules()[1])->toBeInstanceOf(Enum::class);
});
