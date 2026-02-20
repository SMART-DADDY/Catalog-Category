<?php

namespace SmartDaddy\CatalogCategory\Filament\Exports;

use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Filament\Facades\Filament;
use Illuminate\Support\Str;

abstract class BaseStoreExporter extends Exporter
{
    public function getFileName(Export $export): string
    {
        $store = Filament::getTenant();
        $storeName = $store ? Str::slug($store->name) : 'store';
        $exportType = $this->getExportTypeName();

        return "{$storeName}-{$exportType}-Export-".now()->format('Y-m-d-His');
    }

    abstract protected function getExportTypeName(): string;
}
