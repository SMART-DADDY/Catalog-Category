<?php

namespace SmartDaddy\CatalogCategory\Filament\Resources\Categories\Pages;

use Filament\Resources\Pages\CreateRecord;
use SmartDaddy\CatalogCategory\Filament\Resources\Categories\CategoryResource;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
