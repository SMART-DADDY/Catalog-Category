<?php

namespace SmartDaddy\CatalogCategory\Enums;

use BackedEnum;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

enum CategoryStatus: string implements HasColor, HasDescription, HasIcon, HasLabel
{
    case Draft = 'draft';
    case Active = 'active';
    case Inactive = 'inactive';
    case Discontinued = 'discontinued';

    public function getLabel(): string|Htmlable|null
    {
        return $this->name;
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Draft => 'warning',
            self::Active => 'success',
            default => 'gray',
        };
    }

    public function getIcon(): string|BackedEnum|null
    {
        return match ($this) {
            self::Draft => Heroicon::Clock,
            self::Active => Heroicon::CheckBadge,
            default => Heroicon::NoSymbol,
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::Draft => 'Category is being created or edited, not visible to customers.',
            self::Active => 'Category is available for purchase.',
            self::Inactive => 'Category is temporarily unavailable but not deleted.',
            self::Discontinued => 'Category is permanently removed from sale.',
        };
    }

    public static function default(): self
    {
        return self::Active;
    }
}
