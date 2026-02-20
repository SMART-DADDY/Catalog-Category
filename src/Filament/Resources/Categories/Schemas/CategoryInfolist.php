<?php

namespace SmartDaddy\CatalogCategory\Filament\Resources\Categories\Schemas;

use Filament\Facades\Filament;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class CategoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make()
                    ->schema([
                        Grid::make()->schema([
                            Section::make()
                                ->schema([
                                    TextEntry::make('name'),
                                    TextEntry::make('description')
                                        ->columnSpanFull(),
                                ])
                                ->columns()
                                ->columnSpanFull(),
                        ])
                            ->columnSpan(2),
                        Grid::make()->schema([
                            Section::make('Status')
                                ->schema([
                                    TextEntry::make('status')
                                        ->badge(),
                                ])
                                ->columnSpanFull(),
                            Section::make('Extras')
                                ->schema([
                                    TextEntry::make('deleted_at')
                                        ->dateTime()
                                        ->timezone(fn () => Filament::getTenant()?->timezone?->name ?? 'UTC'),
                                    TextEntry::make('created_at')
                                        ->dateTime()
                                        ->timezone(fn () => Filament::getTenant()?->timezone?->name ?? 'UTC'),
                                    TextEntry::make('updated_at')
                                        ->dateTime()
                                        ->timezone(fn () => Filament::getTenant()?->timezone?->name ?? 'UTC'),
                                ])
                                ->columnSpanFull(),

                            Section::make('User Activity')
                                ->schema([
                                    TextEntry::make('activity.created_by')
                                        ->label('Created By')
                                        ->getStateUsing(fn ($record) => $record->activity?->creator?->name ?? 'Unknown')
                                        ->placeholder('Unknown')
                                        ->icon(Heroicon::OutlinedUserPlus)
                                        ->visible(fn ($record) => $record->activity?->creator),
                                    TextEntry::make('activity.updated_by')
                                        ->label('Last Updated By')
                                        ->getStateUsing(fn ($record) => $record->activity?->updater?->name ?? 'Not updated yet')
                                        ->placeholder('Not updated yet')
                                        ->icon(Heroicon::OutlinedPencilSquare)
                                        ->visible(fn ($record) => $record->activity?->updater),
                                ])
                                ->columns(1)
                                ->columnSpanFull(),
                        ])
                            ->columnSpan(1),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),
            ]);
    }
}
