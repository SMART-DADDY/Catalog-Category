<?php

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use SmartDaddy\CatalogCategory\Enums\CategoryStatus;
use SmartDaddy\CatalogCategory\Filament\Resources\Categories\Schemas\CategoryForm;
use Tests\Fixtures\Support\FakeSchemasLivewire;

it('configures category form fields and defaults', function (): void {
    $schema = CategoryForm::configure(Schema::make(new FakeSchemasLivewire()));

    $name = $schema->getComponentByStatePath('name');
    $description = $schema->getComponentByStatePath('description');
    $status = $schema->getComponentByStatePath('status');

    expect($name)->toBeInstanceOf(TextInput::class)
        ->and($name->isRequired())->toBeTrue()
        ->and($description)->toBeInstanceOf(Textarea::class)
        ->and($description->getColumnSpan('default'))->toBe('full')
        ->and($status)->toBeInstanceOf(Select::class)
        ->and($status->isRequired())->toBeTrue()
        ->and($status->isSearchable())->toBeTrue()
        ->and($status->isPreloaded())->toBeTrue()
        ->and($status->getDefaultState())->toBe(CategoryStatus::Active)
        ->and($status->getOptions())->toHaveKeys([
            CategoryStatus::Draft->value,
            CategoryStatus::Active->value,
            CategoryStatus::Inactive->value,
            CategoryStatus::Discontinued->value,
        ]);
});
