{{-- resources/views/admin/layout/app-livewire.blade.php (для Livewire) --}}
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Панель управления' }} - Админка Бильярд</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-100">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        @include('admin.layout.sidebar')
        
        <!-- Main Content -->
        <div class="flex-1">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b">
                <div class="flex justify-between items-center px-6 py-4">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800">
                            {{ $title ?? 'Панель управления' }}
                        </h2>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-700">Администратор: {{ auth()->user()->name }}</span>
                        <form method="POST" action="/logout" class="inline">
                            @csrf
                            <button type="submit" class="text-red-500 hover:text-red-700 font-medium">Выйти</button>
                        </form>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <main class="p-6">
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                        {{ session('error') }}
                    </div>
                @endif

                {{ $slot }}  {{-- ← Для Livewire --}}
            </main>
        </div>
    </div>

    @livewireScripts
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Админ панель загружена');
        });
    </script>
</body>
</html>