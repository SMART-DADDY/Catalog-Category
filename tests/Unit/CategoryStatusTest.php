<?php

use Filament\Support\Icons\Heroicon;
use SmartDaddy\CatalogCategory\Enums\CategoryStatus;

it('returns expected labels, colors, icons and descriptions', function (): void {
    expect(CategoryStatus::Draft->getLabel())->toBe('Draft')
        ->and(CategoryStatus::Draft->getColor())->toBe('warning')
        ->and(CategoryStatus::Draft->getIcon())->toBe(Heroicon::Clock)
        ->and(CategoryStatus::Draft->getDescription())->toBe('Category is being created or edited, not visible to customers.')
        ->and(CategoryStatus::Active->getLabel())->toBe('Active')
        ->and(CategoryStatus::Active->getColor())->toBe('success')
        ->and(CategoryStatus::Active->getIcon())->toBe(Heroicon::CheckBadge)
        ->and(CategoryStatus::Active->getDescription())->toBe('Category is available for purchase.')
        ->and(CategoryStatus::Inactive->getColor())->toBe('gray')
        ->and(CategoryStatus::Inactive->getIcon())->toBe(Heroicon::NoSymbol)
        ->and(CategoryStatus::Inactive->getDescription())->toBe('Category is temporarily unavailable but not deleted.')
        ->and(CategoryStatus::Discontinued->getColor())->toBe('gray')
        ->and(CategoryStatus::Discontinued->getIcon())->toBe(Heroicon::NoSymbol)
        ->and(CategoryStatus::Discontinued->getDescription())->toBe('Category is permanently removed from sale.');
});

it('exposes active as default status', function (): void {
    expect(CategoryStatus::default())->toBe(CategoryStatus::Active);
});
