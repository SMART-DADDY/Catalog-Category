<?php

namespace SmartDaddy\CatalogCategory\Models;

if (trait_exists(\SmartDaddy\UserActivity\Traits\TracksUserActivity::class)) {
    class Category extends BaseCategory
    {
        use \SmartDaddy\UserActivity\Traits\TracksUserActivity;
    }
} else {
    class Category extends BaseCategory
    {
    }
}
