<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Разрешаем только авторизованным администраторам
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'order_id' => 'nullable|exists:orders,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,card,online,transfer',
            'status' => 'required|in:pending,completed,failed,refunded',
            'transaction_id' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'Необходимо выбрать пользователя.',
            'amount.required' => 'Введите сумму платежа.',
            'amount.numeric' => 'Сумма должна быть числом.',
            'payment_method.required' => 'Выберите способ оплаты.',
            'status.required' => 'Укажите статус платежа.',
        ];
    }
}
