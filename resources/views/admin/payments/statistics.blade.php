@extends('admin.layout.app')

@section('title', 'Статистика платежей')


@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">📊 Статистика платежей</h1>

    {{-- 🔙 Кнопка возврата --}}
    <a href="{{ route('admin.payments.index') }}"
       class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded">
        ⬅️ Назад к платежам
    </a>
</div>
{{-- Общие цифры --}}
<div class="grid grid-cols-3 gap-4 mb-8">
    <div class="bg-white shadow rounded p-4 text-center">
        <p class="text-gray-500">Всего платежей</p>
        <p class="text-2xl font-bold">{{ $total['count'] ?? 0 }}</p>
    </div>
    <div class="bg-white shadow rounded p-4 text-center">
        <p class="text-gray-500">Общая сумма</p>
        <p class="text-2xl font-bold">{{ number_format($total['amount'] ?? 0, 2) }} ₽</p>
    </div>
    <div class="bg-white shadow rounded p-4 text-center">
        <p class="text-gray-500">Средний чек</p>
        <p class="text-2xl font-bold">{{ number_format($total['average'] ?? 0, 2) }} ₽</p>
    </div>
</div>

{{-- Разбивка по статусам --}}
<div class="bg-white shadow rounded p-4 mb-8">
    <h2 class="text-xl font-semibold mb-4">По статусам</h2>
    <table class="w-full border-collapse">
        <thead>
            <tr class="bg-gray-100 text-left">
                <th class="px-4 py-2">Статус</th>
                <th class="px-4 py-2">Количество</th>
                <th class="px-4 py-2">Сумма</th>
            </tr>
        </thead>
        <tbody>
        @forelse($byStatus as $status => $data)
            <tr class="border-t">
                <td class="px-4 py-2">{{ $status }}</td>
                <td class="px-4 py-2">{{ $data['count'] }}</td>
                <td class="px-4 py-2">{{ number_format($data['amount'], 2) }} ₽</td>
            </tr>
        @empty
            <tr><td colspan="3" class="px-4 py-2 text-center text-gray-500">Нет данных</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

{{-- Разбивка по месяцам --}}
<div class="bg-white shadow rounded p-4">
    <h2 class="text-xl font-semibold mb-4">По месяцам</h2>
    <table class="w-full border-collapse">
        <thead>
            <tr class="bg-gray-100 text-left">
                <th class="px-4 py-2">Месяц</th>
                <th class="px-4 py-2">Количество</th>
                <th class="px-4 py-2">Сумма</th>
            </tr>
        </thead>
        <tbody>
        @forelse($monthly as $month => $data)
            <tr class="border-t">
                <td class="px-4 py-2">{{ $month }}</td>
                <td class="px-4 py-2">{{ $data['count'] }}</td>
                <td class="px-4 py-2">{{ number_format($data['amount'], 2) }} ₽</td>
            </tr>
        @empty
            <tr><td colspan="3" class="px-4 py-2 text-center text-gray-500">Нет данных</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
