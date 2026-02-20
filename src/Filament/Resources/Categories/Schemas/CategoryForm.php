<?php

namespace SmartDaddy\CatalogCategory\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use SmartDaddy\CatalogCategory\Enums\CategoryStatus;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)
                    ->schema([
                        Section::make('Category Detail')
                            ->schema([
                                TextInput::make('name')
                                    ->required(),
                                Textarea::make('description')
                                    ->columnSpanFull(),
                            ])
                            ->columnSpan(2),

                        Section::make('Status')
                            ->schema([
                                Select::make('status')
                                    ->options(CategoryStatus::class)
                                    ->default(CategoryStatus::default())
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                            ])
                            ->columnSpan(1),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
