<?php

namespace SmartDaddy\CatalogCategory\Filament\Resources\Categories\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\Model;
use SmartDaddy\CatalogCategory\Filament\Resources\Categories\CategoryResource;

class ViewCategory extends ViewRecord
{
    protected static string $resource = CategoryResource::class;

    protected function resolveRecord(int|string $key): Model
    {
        return parent::resolveRecord($key)->load(['activity.creator', 'activity.updater']);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->label('Edit')
                ->color('warning'),
            DeleteAction::make()
                ->label('Delete')
                ->color('danger'),
            RestoreAction::make()
                ->label('Restore')
                ->color('success'),
            ForceDeleteAction::make()
                ->label('Force delete')
                ->color('warning'),
        ];
    }
}
