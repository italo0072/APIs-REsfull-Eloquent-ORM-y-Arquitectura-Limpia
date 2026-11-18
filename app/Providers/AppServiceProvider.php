<?php

namespace App\Providers;

use App\Repositories\Contracts\MenuItemRepositoryInterface;
use App\Repositories\EloquentMenuItemRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(MenuItemRepositoryInterface::class, EloquentMenuItemRepository::class);
    }

    public function boot(): void
    {
    }
}
