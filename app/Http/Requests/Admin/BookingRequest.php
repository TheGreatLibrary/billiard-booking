<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingRequest extends FormRequest
{
    public function rules()
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
