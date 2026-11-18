<?php

namespace App\Services;

use App\Models\Category;
use App\Models\MenuItem;
use App\Repositories\Contracts\MenuItemRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MenuItemService
{
    public function __construct(private readonly MenuItemRepositoryInterface $repository)
    {
    }

    public function list(array $filters, int $perPage): LengthAwarePaginator
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function listByCategory(Category $category, array $filters, int $perPage): LengthAwarePaginator
    {
        return $this->repository->paginateByCategory($category, $filters, $perPage);
    }

    public function find(MenuItem $menuItem): MenuItem
    {
        return $menuItem->load('category');
    }

    public function create(array $data): MenuItem
    {
        return $this->repository->create($data);
    }

    public function update(MenuItem $menuItem, array $data): MenuItem
    {
        return $this->repository->update($menuItem, $data);
    }

    public function delete(MenuItem $menuItem): void
    {
        $this->repository->delete($menuItem);
    }
}
