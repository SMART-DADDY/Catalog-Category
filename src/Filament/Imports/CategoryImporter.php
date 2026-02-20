<?php

namespace SmartDaddy\CatalogCategory\Filament\Imports;

use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Filament\Facades\Filament;
use Illuminate\Support\Number;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\ValidationException;
use SmartDaddy\CatalogCategory\Enums\CategoryStatus;
use SmartDaddy\CatalogCategory\Models\Category;

class CategoryImporter extends Importer
{
    protected static ?string $model = Category::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->label('Name')
                ->exampleHeader('Name')
                ->example(['Beverages', 'Dairy', 'Bakery']),
            ImportColumn::make('description')
                ->label('Description')
                ->exampleHeader('Description')
                ->example(['Soft drinks, coffee, tea, and other non-alcoholic refreshments', 'Fresh bread, pastries, cakes, and other baked goods']),
            ImportColumn::make('status')
                ->label('Status')
                ->exampleHeader('Status')
                ->example('active')
                ->requiredMapping()
                ->rules(['required', new Enum(CategoryStatus::class)]),
        ];
    }

    public function resolveRecord(): Category
    {
        $storeId = $this->options['store_id'] ?? Filament::getTenant()?->getKey();

        if (blank($storeId)) {
            throw ValidationException::withMessages([
                'name' => 'Cannot resolve store for this import job. Please start the import from within a store context.',
            ]);
        }

        $record = Category::firstOrNew([
            'name' => $this->data['name'],
            'store_id' => $storeId,
        ]);

        $record->store_id = $storeId;

        return $record;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your category import has completed and '.Number::format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
