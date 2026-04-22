<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель — Мини CRM</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- Umbra UI стили --}}
    <link rel="stylesheet" href="{{ asset('vendor/umbra-ui/css/umbra.css') }}"> {{-- если опубликовано, иначе Tailwind уже должен тянуть --}}
    
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-zinc-950 text-zinc-100 min-h-screen">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="w-72 bg-zinc-900 border-r border-zinc-800 p-6 flex flex-col">
            <div class="mb-10">
                <h1 class="text-2xl font-bold tracking-tight">Мини CRM</h1>
            </div>
            
            <nav class="flex-1 space-y-1">
                <a href="{{ route('admin.tickets.index') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-2xl hover:bg-zinc-800 transition-colors {{ request()->routeIs('admin.tickets.*') ? 'bg-zinc-800 text-white' : 'text-zinc-400' }}">
                    📋 Заявки
                </a>
            </nav>
            
            <div class="pt-6 border-t border-zinc-800">
                <div class="flex items-center justify-between text-sm">

                </div>
            </div>
        </div>

        <!-- Main content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="h-16 border-b border-zinc-800 bg-zinc-900 px-8 flex items-center justify-between">
                <h2 class="text-xl font-semibold">Управление заявками</h2>
            </header>
            
            <main class="flex-1 overflow-auto p-8">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- Toast container Umbra --}}
    <x-umbra-ui::toast-container />
</body>
</html>