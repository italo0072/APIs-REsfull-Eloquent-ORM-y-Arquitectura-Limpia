<?php

use App\Http\Controllers\Api\MenuItemController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => response()->json([
    'message' => 'La Buena Mesa API',
    'endpoints' => [
        'menu_items' => '/api/menu-items',
        'menu_items_by_category' => '/api/menu-items/category/{category}',
    ],
]))->name('api.index');

Route::get('menu-items/category/{category}', [MenuItemController::class, 'byCategory'])
    ->name('menu-items.by-category');

Route::apiResource('menu-items', MenuItemController::class);
