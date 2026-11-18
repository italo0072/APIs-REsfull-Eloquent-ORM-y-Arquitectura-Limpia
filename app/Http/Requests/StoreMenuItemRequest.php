<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMenuItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => [
                'required', 'string', 'max:120',
                Rule::unique('menu_items', 'name')->where('category_id', $this->input('category_id')),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'price' => ['required', 'numeric', 'min:0', 'max:99999.99'],
            'is_available' => ['sometimes', 'boolean'],
            'image_url' => ['nullable', 'url', 'max:255'],
            'preparation_time' => ['nullable', 'integer', 'min:1', 'max:240'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.exists' => 'La categoría seleccionada no existe.',
            'name.unique' => 'Ya existe un platillo con ese nombre en la categoría.',
            'price.min' => 'El precio no puede ser negativo.',
        ];
    }
}
