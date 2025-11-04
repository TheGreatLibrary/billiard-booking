<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('productType')?->id;

        return [
            'name' => 'required|string|max:255|unique:product_types,name,' . $id,
        ];
    }
}
