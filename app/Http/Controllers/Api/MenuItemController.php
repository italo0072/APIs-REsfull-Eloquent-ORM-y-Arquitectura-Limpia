<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMenuItemRequest;
use App\Http\Requests\UpdateMenuItemRequest;
use App\Http\Resources\MenuItemResource;
use App\Models\Category;
use App\Models\MenuItem;
use App\Services\MenuItemService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class MenuItemController extends Controller
{
    public function __construct(private readonly MenuItemService $service)
    {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        return MenuItemResource::collection(
            $this->service->list($this->filters($request), $this->perPage($request))
        );
    }

    public function byCategory(Request $request, Category $category): AnonymousResourceCollection
    {
        return MenuItemResource::collection(
            $this->service->listByCategory($category, $this->filters($request), $this->perPage($request))
        );
    }

    public function show(MenuItem $menuItem): MenuItemResource
    {
        return new MenuItemResource($this->service->find($menuItem));
    }

    public function store(StoreMenuItemRequest $request): JsonResponse
    {
        $item = $this->service->create($request->validated());

        return (new MenuItemResource($item))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(UpdateMenuItemRequest $request, MenuItem $menuItem): MenuItemResource
    {
        return new MenuItemResource($this->service->update($menuItem, $request->validated()));
    }

    public function destroy(MenuItem $menuItem): Response
    {
        $this->service->delete($menuItem);

        return response()->noContent();
    }

    private function filters(Request $request): array
    {
        $validated = $request->validate([
            'available' => ['sometimes', 'boolean'],
            'search' => ['sometimes', 'string', 'max:100'],
        ]);

        return $validated;
    }

    private function perPage(Request $request): int
    {
        return min(max($request->integer('per_page', 15), 1), 100);
    }
}
