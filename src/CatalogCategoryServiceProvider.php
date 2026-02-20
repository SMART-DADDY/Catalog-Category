<?php

namespace SmartDaddy\CatalogCategory;

use Illuminate\Support\ServiceProvider;

class CatalogCategoryServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
