<div>
    <div class="max-w-2xl mx-auto px-4 py-8">
        @if (session()->has('error'))
            <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 rounded-xl text-sm">
                {{ session('error') }}
            </div>
        @endif

        <x-booking.steps.step-payment
            :booking="$booking"
            :totalAmount="$totalAmount"
            wirePayBooking="payBooking"
            wireSkipPayment="cancelBooking"
            wireGoBack=""
        />

        <div class="mt-4 text-center">
            <a href="{{ route('dashboard') }}" class="text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white text-sm hover:underline transition">
                ← Вернуться в личный кабинет
            </a>
        </div>
    </div>
</div>