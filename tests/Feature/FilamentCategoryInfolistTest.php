<?php

use Filament\Infolists\Components\TextEntry;
use Filament\Facades\Filament;
use Filament\Schemas\Schema;
use SmartDaddy\CatalogCategory\Filament\Resources\Categories\Schemas\CategoryInfolist;
use Tests\Fixtures\Support\FakeSchemasLivewire;

it('configures category infolist entries', function (): void {
    Filament::swap(new class
    {
        public function getTenant(): ?object
        {
            return null;
        }
    });

    $schema = CategoryInfolist::configure(Schema::make(new FakeSchemasLivewire()));

    $name = $schema->getComponentByStatePath('name', true);
    $description = $schema->getComponentByStatePath('description', true);
    $status = $schema->getComponentByStatePath('status', true);
    $deletedAt = $schema->getComponentByStatePath('deleted_at', true);
    $createdAt = $schema->getComponentByStatePath('created_at', true);
    $updatedAt = $schema->getComponentByStatePath('updated_at', true);
    $createdBy = $schema->getComponentByStatePath('activity.created_by', true);
    $updatedBy = $schema->getComponentByStatePath('activity.updated_by', true);

    expect($name)->toBeInstanceOf(TextEntry::class)
        ->and($description)->toBeInstanceOf(TextEntry::class)
        ->and($description->getColumnSpan('default'))->toBe('full')
        ->and($status)->toBeInstanceOf(TextEntry::class)
        ->and($status->isBadge())->toBeTrue()
        ->and($deletedAt)->toBeInstanceOf(TextEntry::class)
        ->and($deletedAt->getTimezone())->toBe('UTC')
        ->and($createdAt)->toBeInstanceOf(TextEntry::class)
        ->and($createdAt->getTimezone())->toBe('UTC')
        ->and($updatedAt)->toBeInstanceOf(TextEntry::class)
        ->and($updatedAt->getTimezone())->toBe('UTC')
        ->and($createdBy)->toBeInstanceOf(TextEntry::class)
        ->and($updatedBy)->toBeInstanceOf(TextEntry::class);
});
