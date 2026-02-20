<?php

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use SmartDaddy\CatalogCategory\Filament\Resources\Categories\CategoryResource;
use SmartDaddy\CatalogCategory\Filament\Resources\Categories\Pages\CreateCategory;
use SmartDaddy\CatalogCategory\Filament\Resources\Categories\Pages\EditCategory;
use SmartDaddy\CatalogCategory\Filament\Resources\Categories\Pages\ListCategories;
use SmartDaddy\CatalogCategory\Filament\Resources\Categories\Pages\ViewCategory;

class FakeCategoryResource
{
    public static function getUrl(string $name): string
    {
        return $name === 'index' ? '/categories' : '/';
    }
}

class TestListCategoriesPage extends ListCategories
{
    public function exposedHeaderActions(): array
    {
        return $this->getHeaderActions();
    }
}

class TestEditCategoryPage extends EditCategory
{
    protected static string $resource = FakeCategoryResource::class;

    public function exposedHeaderActions(): array
    {
        return $this->getHeaderActions();
    }

    public function exposedRedirectUrl(): string
    {
        return $this->getRedirectUrl();
    }
}

class TestCreateCategoryPage extends CreateCategory
{
    protected static string $resource = FakeCategoryResource::class;

    public function exposedRedirectUrl(): string
    {
        return $this->getRedirectUrl();
    }
}

class TestViewCategoryPage extends ViewCategory
{
    public function exposedHeaderActions(): array
    {
        return $this->getHeaderActions();
    }
}

it('binds each page to the category resource', function (): void {
    expect((new ReflectionClass(ListCategories::class))->getStaticPropertyValue('resource'))
        ->toBe(CategoryResource::class)
        ->and((new ReflectionClass(CreateCategory::class))->getStaticPropertyValue('resource'))
        ->toBe(CategoryResource::class)
        ->and((new ReflectionClass(EditCategory::class))->getStaticPropertyValue('resource'))
        ->toBe(CategoryResource::class)
        ->and((new ReflectionClass(ViewCategory::class))->getStaticPropertyValue('resource'))
        ->toBe(CategoryResource::class);
});

it('configures list page header actions', function (): void {
    $actions = (new TestListCategoriesPage())->exposedHeaderActions();

    expect($actions)->toHaveCount(1)
        ->and($actions[0])->toBeInstanceOf(CreateAction::class);
});

it('configures create page redirect url', function (): void {
    expect((new TestCreateCategoryPage())->exposedRedirectUrl())->toBe('/categories');
});

it('configures edit page header actions and redirect', function (): void {
    $page = new TestEditCategoryPage();
    $actions = $page->exposedHeaderActions();

    expect($actions)->toHaveCount(3)
        ->and($actions[0])->toBeInstanceOf(DeleteAction::class)
        ->and($actions[1])->toBeInstanceOf(ForceDeleteAction::class)
        ->and($actions[2])->toBeInstanceOf(RestoreAction::class)
        ->and($page->exposedRedirectUrl())->toBe('/categories');
});

it('configures view page header actions', function (): void {
    $actions = (new TestViewCategoryPage())->exposedHeaderActions();

    expect($actions)->toHaveCount(4)
        ->and($actions[0])->toBeInstanceOf(EditAction::class)
        ->and($actions[0]->getColor())->toBe('warning')
        ->and($actions[1])->toBeInstanceOf(DeleteAction::class)
        ->and($actions[1]->getColor())->toBe('danger')
        ->and($actions[2])->toBeInstanceOf(RestoreAction::class)
        ->and($actions[2]->getColor())->toBe('success')
        ->and($actions[3])->toBeInstanceOf(ForceDeleteAction::class)
        ->and($actions[3]->getColor())->toBe('warning');
});
