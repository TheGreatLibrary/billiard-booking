<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id'      => 'required|exists:users,id',
            'total_amount' => 'required|numeric|min:0',
            'status'       => 'required|in:pending,processing,completed,canceled',
            'notes'        => 'nullable|string|max:500',
        ];
    }
}
