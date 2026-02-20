<?php

use App\Models\Store;
use Filament\Actions\ActionGroup;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\ImportAction;
use Filament\Facades\Filament;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Foundation\Testing\RefreshDatabase;
use SmartDaddy\CatalogCategory\Filament\Exports\CategoryExporter;
use SmartDaddy\CatalogCategory\Filament\Imports\CategoryImporter;
use SmartDaddy\CatalogCategory\Filament\Resources\Categories\Tables\CategoriesTable;
use SmartDaddy\CatalogCategory\Models\Category;

uses(RefreshDatabase::class);

function makeTableLivewireDouble(): HasTable
{
    $livewire = \Mockery::mock(HasTable::class);
    $livewire->shouldIgnoreMissing();
    $livewire->shouldReceive('isTableColumnToggledHidden')->andReturnFalse();
    $livewire->shouldReceive('getTableSortColumn')->andReturnNull();
    $livewire->shouldReceive('getTableSortDirection')->andReturnNull();

    return $livewire;
}

it('configures table columns, sorting and filters', function (): void {
    Filament::swap(new class
    {
        public function getTenant(): ?object
        {
            return null;
        }
    });

    $table = CategoriesTable::configure(Table::make(makeTableLivewireDouble()));

    $columns = $table->getColumns();

    expect(array_keys($columns))->toBe(['name', 'status', 'deleted_at', 'created_at', 'updated_at'])
        ->and($columns['name'])->toBeInstanceOf(TextColumn::class)
        ->and($columns['name']->isSearchable())->toBeTrue()
        ->and($columns['status'])->toBeInstanceOf(TextColumn::class)
        ->and($columns['status']->isBadge())->toBeTrue()
        ->and($columns['deleted_at']->isSortable())->toBeTrue()
        ->and($columns['deleted_at']->isToggleable())->toBeTrue()
        ->and($columns['deleted_at']->isToggledHiddenByDefault())->toBeTrue()
        ->and($columns['created_at']->isSortable())->toBeTrue()
        ->and($columns['created_at']->isToggleable())->toBeTrue()
        ->and($columns['updated_at']->isSortable())->toBeTrue()
        ->and($columns['updated_at']->isToggleable())->toBeTrue()
        ->and($columns['updated_at']->isToggledHiddenByDefault())->toBeTrue()
        ->and($table->getDefaultSort(Category::query(), 'desc'))->toBe('id')
        ->and($table->getDefaultSortDirection())->toBe('desc')
        ->and($table->getFilters())->toHaveCount(1)
        ->and(array_values($table->getFilters())[0])->toBeInstanceOf(TrashedFilter::class);
});

it('configures import header action with store option', function (): void {
    $store = Store::create(['name' => 'Main Store']);

    Filament::swap(new class($store)
    {
        public function __construct(private readonly Store $store) {}

        public function getTenant(): Store
        {
            return $this->store;
        }
    });

    $table = CategoriesTable::configure(Table::make(makeTableLivewireDouble()));
    $headerActions = array_values($table->getHeaderActions());

    expect($headerActions)->toHaveCount(1)
        ->and($headerActions[0])->toBeInstanceOf(ImportAction::class)
        ->and($headerActions[0]->getImporter())->toBe(CategoryImporter::class)
        ->and($headerActions[0]->getOptions())->toBe(['store_id' => $store->id]);
});

it('configures record and toolbar bulk actions including exporter', function (): void {
    Filament::swap(new class
    {
        public function getTenant(): ?object
        {
            return null;
        }
    });

    $table = CategoriesTable::configure(Table::make(makeTableLivewireDouble()));

    $recordActions = $table->getRecordActions();
    $toolbarActions = $table->getToolbarActions();

    expect($recordActions)->toHaveCount(1)
        ->and($recordActions[0])->toBeInstanceOf(ActionGroup::class)
        ->and(array_keys($recordActions[0]->getFlatActions()))->toBe([
            'view',
            'edit',
            'delete',
            'restore',
            'forceDelete',
        ]);

    expect($toolbarActions)->toHaveCount(1)
        ->and($toolbarActions[0])->toBeInstanceOf(ActionGroup::class);

    $flatToolbarActions = $toolbarActions[0]->getFlatActions();

    expect($flatToolbarActions)->toHaveKeys([
        'delete',
        'forceDelete',
        'restore',
        'export',
    ])
        ->and($flatToolbarActions['export'])->toBeInstanceOf(ExportBulkAction::class)
        ->and($flatToolbarActions['export']->getExporter())->toBe(CategoryExporter::class);
});
