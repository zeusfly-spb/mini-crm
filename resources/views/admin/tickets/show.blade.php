@extends('layouts.admin')

@section('content')
<div class="max-w-5xl mx-auto">
    <a
        href="{{ route('admin.tickets.index') }}"
        class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 px-4 py-2 border border-input bg-background hover:bg-accent hover:text-accent-foreground cursor-pointer mb-6"
    >
        ← Назад к списку заявок
    </a>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <div class="lg:col-span-8">
            <x-umbra-ui::card class="mb-8">
                <div class="flex justify-between items-start mb-6">
                    <h1 class="text-3xl font-semibold">Заявка #{{ $ticket->id }}</h1>
                    <x-umbra-ui::badge 
                        variant="{{ $ticket->status === 'resolved' ? 'success' : ($ticket->status === 'pending' ? 'warning' : ($ticket->status === 'new' ? 'info' : 'default')) }}">
                        {{ $statuses[$ticket->status] ?? $ticket->status }}
                    </x-umbra-ui::badge>
                </div>

                <div class="space-y-8">
                    <div>
                        <x-umbra-ui::label>Клиент</x-umbra-ui::label>
                        <p class="text-2xl font-medium">{{ $ticket->customer->name }}</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-umbra-ui::label>Email</x-umbra-ui::label>
                            <p class="text-lg">{{ $ticket->customer->email ?? '—' }}</p>
                        </div>
                        <div>
                            <x-umbra-ui::label>Телефон</x-umbra-ui::label>
                            <p class="text-lg">{{ $ticket->customer->phone ?? '—' }}</p>
                        </div>
                    </div>

                    <div>
                        <x-umbra-ui::label>Текст заявки</x-umbra-ui::label>
                        <div class="bg-zinc-950 border border-zinc-800 rounded-3xl p-6 text-zinc-100">
                            <p class="whitespace-pre-line break-words leading-relaxed">
                                {{ trim((string) ($ticket->text ?? '')) ?: '—' }}
                            </p>
                        </div>
                    </div>

                    @if($ticket->media && $ticket->media->isNotEmpty())
                    <div>
                        <x-umbra-ui::label>Прикреплённые файлы ({{ $ticket->media->count() }})</x-umbra-ui::label>
                        <div class="space-y-3 mt-3">
                            @foreach($ticket->media as $media)
                            <div class="flex items-center justify-between bg-zinc-900 border border-zinc-800 rounded-2xl px-5 py-4">
                                <div>
                                    <p class="font-medium">{{ $media->file_name }}</p>
                                    <p class="text-sm text-zinc-500">{{ $media->human_readable_size }}</p>
                                </div>
                                <a href="{{ route('media.download', $media) }}" 
                                   class="text-emerald-400 hover:text-emerald-500 font-medium">
                                    Скачать
                                </a>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </x-umbra-ui::card>
        </div>

        <div class="lg:col-span-4">
            <x-umbra-ui::card id="status">
                <h3 class="font-semibold text-lg mb-5">Изменить статус</h3>
                
                <form method="POST" action="{{ route('admin.tickets.update', $ticket) }}">
                    @csrf
                    @method('PUT')

                    <select
                        name="status"
                        class="admin-select mb-6 flex h-10 w-full rounded-md border px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                    >
                        @foreach ($statuses as $value => $label)
                            <option value="{{ $value }}" @selected($ticket->status === $value)>{{ $label }}</option>
                        @endforeach
                    </select>

                    <x-umbra-ui::button
                        type="submit"
                        class="w-full cursor-pointer bg-emerald-500 text-white hover:bg-emerald-600 focus-visible:ring-emerald-500"
                    >
                        Обновить статус заявки
                    </x-umbra-ui::button>
                </form>
            </x-umbra-ui::card>
        </div>
    </div>
</div>
@endsection