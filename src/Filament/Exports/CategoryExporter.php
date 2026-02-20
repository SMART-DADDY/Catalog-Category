<?php

namespace SmartDaddy\CatalogCategory\Filament\Exports;

use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;
use SmartDaddy\CatalogCategory\Enums\CategoryStatus;
use SmartDaddy\CatalogCategory\Models\Category;

class CategoryExporter extends BaseStoreExporter
{
    protected static ?string $model = Category::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name'),
            ExportColumn::make('description'),
            ExportColumn::make('status')
                ->formatStateUsing(fn (CategoryStatus $state): string => $state->value),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your category export has completed and '.Number::format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to export.';
        }

        return $body;
    }

    protected function getExportTypeName(): string
    {
        return 'Categories';
    }
}
