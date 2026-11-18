<?php

namespace App\Repositories\Contracts;

use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface MenuItemRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function paginateByCategory(Category $category, array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function create(array $data): MenuItem;

    public function update(MenuItem $menuItem, array $data): MenuItem;

    public function delete(MenuItem $menuItem): bool;
}
