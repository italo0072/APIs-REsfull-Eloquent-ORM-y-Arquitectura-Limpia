<?php

namespace App\Repositories;

use App\Models\Category;
use App\Models\MenuItem;
use App\Repositories\Contracts\MenuItemRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class EloquentMenuItemRepository implements MenuItemRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->filtered(MenuItem::query(), $filters)->paginate($perPage);
    }

    public function paginateByCategory(Category $category, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = MenuItem::query()->where('category_id', $category->id);

        return $this->filtered($query, $filters)->paginate($perPage);
    }

    public function create(array $data): MenuItem
    {
        return MenuItem::create($data)->load('category');
    }

    public function update(MenuItem $menuItem, array $data): MenuItem
    {
        $menuItem->update($data);

        return $menuItem->refresh()->load('category');
    }

    public function delete(MenuItem $menuItem): bool
    {
        return (bool) $menuItem->delete();
    }

    private function filtered(Builder $query, array $filters): Builder
    {
        return $query
            ->with('category')
            ->when(isset($filters['available']), fn (Builder $q) => $q->available((bool) $filters['available']))
            ->when(! empty($filters['search']), fn (Builder $q) => $q->search($filters['search']))
            ->orderBy('name');
    }
}
