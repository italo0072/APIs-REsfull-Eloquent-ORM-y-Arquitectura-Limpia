<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMenuItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $required = $this->isMethod('PUT') ? 'required' : 'sometimes';
        $menuItem = $this->route('menu_item');
        $categoryId = $this->input('category_id', $menuItem->category_id);

        return [
            'category_id' => [$required, 'integer', 'exists:categories,id'],
            'name' => [
                $required, 'string', 'max:120',
                Rule::unique('menu_items', 'name')
                    ->where('category_id', $categoryId)
                    ->ignore($menuItem->id),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'price' => [$required, 'numeric', 'min:0', 'max:99999.99'],
            'is_available' => ['sometimes', 'boolean'],
            'image_url' => ['nullable', 'url', 'max:255'],
            'preparation_time' => ['nullable', 'integer', 'min:1', 'max:240'],
        ];
    }

    public function messages(): array
    {
        return (new StoreMenuItemRequest())->messages();
    }
}
