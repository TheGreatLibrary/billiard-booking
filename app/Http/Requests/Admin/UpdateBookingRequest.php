<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookingRequest extends FormRequest
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
            'resource_id' => 'required|exists:resources,id',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'status' => 'required|in:pending,confirmed,canceled,finished,no_show',
            'notes' => 'nullable|string|max:500',
        ];
    }
}
