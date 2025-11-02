<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'place_id' => 'required|exists:places,id',
            'zone_id' => 'required|exists:zones,id',
            'resource_ids' => 'required|array|min:1',
            'resource_ids.*' => 'exists:resources,id',
            'starts_at' => 'required|date|after:now',
            'ends_at' => 'required|date|after:starts_at',
            'status' => 'required|in:pending,confirmed,canceled,finished,no_show',
            'notes' => 'nullable|string|max:500',
            'equipment' => 'nullable|array',
            'equipment.*.model_id' => 'required|exists:product_models,id',
            'equipment.*.qty' => 'required|integer|min:1',
        ];
    }
}
