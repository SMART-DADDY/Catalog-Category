<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

it('loads package migrations through the service provider', function (): void {
    expect(Schema::hasTable('categories'))->toBeTrue();
});
