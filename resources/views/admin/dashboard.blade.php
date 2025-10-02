@extends('admin.layout.app')

@section('title', 'Админ-панель')

@section('content')
<h1 class="text-2xl font-bold mb-6">Общая статистика</h1>

{{-- Быстрые ссылки на основные разделы --}}
<div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-8">
    <a href="{{ route('admin.payments.index') }}" class="block bg-white p-4 rounded shadow hover:bg-gray-50">
        <div class="text-gray-500 text-sm">Платежей</div>
        <div class="text-2xl font-bold">{{ $total['payments'] }}</div>
    </a>

    <a href="{{ route('admin.orders.index') }}" class="block bg-white p-4 rounded shadow hover:bg-gray-50">
        <div class="text-gray-500 text-sm">Заказов</div>
        <div class="text-2xl font-bold">{{ $total['orders'] }}</div>
    </a>

    <a href="{{ route('admin.bookings.index') }}" class="block bg-white p-4 rounded shadow hover:bg-gray-50">
        <div class="text-gray-500 text-sm">Бронирований</div>
        <div class="text-2xl font-bold">{{ $total['bookings'] }}</div>
    </a>

    <a href="{{ route('admin.users.index') }}" class="block bg-white p-4 rounded shadow hover:bg-gray-50">
        <div class="text-gray-500 text-sm">Пользователей</div>
        <div class="text-2xl font-bold">{{ $total['users'] }}</div>
    </a>
    <a href="{{ route('admin.product-types.index') }}" class="block bg-white p-4 rounded shadow hover:bg-gray-50">
        <div class="text-gray-500 text-sm">ProductType</div>
        <div class="text-2xl font-bold">{{ $total['productTypes'] }}</div>
    </a>

    <a href="{{ route('admin.product-models.index') }}" class="block bg-white p-4 rounded shadow hover:bg-gray-50">

        <div class="text-gray-500 text-sm">ProductModel</div>
        <div class="text-2xl font-bold">{{ $total['productModels'] }}</div>
    </a>

    <a href="{{ route('admin.places.index') }}" class="block bg-white p-4 rounded shadow hover:bg-gray-50">
        <div class="text-gray-500 text-sm">Места (Place)</div>
        <div class="text-2xl font-bold">{{ $total['places'] }}</div>
    </a>

    <a href="{{ route('admin.zones.index') }}" class="block bg-white p-4 rounded shadow hover:bg-gray-50">
        <div class="text-gray-500 text-sm">Зоны (Zone)</div>
        <div class="text-2xl font-bold">{{ $total['zones'] }}</div>
    </a>

    <a href="{{ route('admin.price-rules.index') }}" class="block bg-white p-4 rounded shadow hover:bg-gray-50">
        <div class="text-gray-500 text-sm">PriceRule</div>
        <div class="text-2xl font-bold">{{ $total['priceRules'] }}</div>
    </a>

    <a href="{{ route('admin.resources.index') }}" class="block bg-white p-4 rounded shadow hover:bg-gray-50">
        <div class="text-gray-500 text-sm">Resource</div>
        <div class="text-2xl font-bold">{{ $total['resources'] }}</div>
    </a>
</div>

{{-- Платежи по месяцам --}}
<div class="bg-white shadow rounded p-4">
    <h2 class="text-xl font-semibold mb-4"> Платежи по месяцам</h2>
    <table class="w-full border-collapse">
        <thead>
            <tr class="bg-gray-100">
                <th class="px-4 py-2 text-left">Месяц</th>
                <th class="px-4 py-2">Кол-во</th>
                <th class="px-4 py-2">Сумма</th>
            </tr>
        </thead>
        <tbody>
        @forelse($monthly as $month => $data)
            <tr class="border-t">
                <td class="px-4 py-2">{{ $month }}</td>
                <td class="px-4 py-2 text-center">{{ $data->count }}</td>
                <td class="px-4 py-2 text-center">{{ number_format($data->amount,2,',',' ') }} ₽</td>
            </tr>
        @empty
            <tr><td colspan="3" class="px-4 py-2 text-center text-gray-500">Нет данных</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
