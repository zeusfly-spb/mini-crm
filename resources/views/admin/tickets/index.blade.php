@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-semibold">Заявки</h1>
    </div>

    <form method="GET" class="bg-zinc-900 border border-zinc-800 rounded-3xl p-6 mb-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
        <div>
            <x-umbra-ui::label>Статус</x-umbra-ui::label>
            <x-umbra-ui::select name="status" class="admin-select">
                <option value="">Все статусы</option>
                <option value="new" {{ request('status') === 'new' ? 'selected' : '' }}>Новая</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>В ожидании</option>
                <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Решена</option>
            </x-umbra-ui::select>
        </div>

        <div>
            <x-umbra-ui::label>Дата от</x-umbra-ui::label>
            <x-umbra-ui::input type="date" name="date_from" value="{{ request('date_from') }}" />
        </div>

        <div>
            <x-umbra-ui::label>Дата до</x-umbra-ui::label>
            <x-umbra-ui::input type="date" name="date_to" value="{{ request('date_to') }}" />
        </div>

        <div>
            <x-umbra-ui::label>Email</x-umbra-ui::label>
            <x-umbra-ui::input type="text" name="email" value="{{ request('email') }}" placeholder="example@mail.ru" />
        </div>

        <div>
            <x-umbra-ui::label>Телефон</x-umbra-ui::label>
            <x-umbra-ui::input type="text" name="phone" value="{{ request('phone') }}" placeholder="+7" />
        </div>

        <div class="flex items-end gap-3 lg:col-span-5">
            <button
                type="submit"
                class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors px-4 py-2 bg-emerald-600 text-white hover:bg-emerald-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 focus-visible:ring-offset-zinc-900"
            >
                Применить фильтры
            </button>
            <a
                href="{{ route('admin.tickets.index') }}"
                class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors px-4 py-2 border border-zinc-700 text-zinc-200 hover:bg-zinc-800 hover:text-white"
            >
                Сбросить
            </a>
        </div>
    </form>

    <x-umbra-ui::card>
        <x-umbra-ui::table class="border-separate [border-spacing:0_4px]">
            <x-umbra-ui::table.head>
                <tr>
                    <th>ID</th>
                    <th>Клиент</th>
                    <th>Контакты</th>
                    <th>Тема / Текст</th>
                    <th>Статус</th>
                    <th>Дата создания</th>
                    <th></th>
                </tr>
            </x-umbra-ui::table.head>
            <x-umbra-ui::table.body>
                @foreach ($tickets as $ticket)
                <tr>
                    <td class="font-medium px-3 py-2">#{{ $ticket->id }}</td>
                    <td class="px-3 py-2">{{ $ticket->customer->name ?? '—' }}</td>
                    <td class="text-sm px-3 py-2">
                        {{ $ticket->customer->email ?? '—' }}<br>
                        <span class="text-zinc-500">{{ $ticket->customer->phone ?? '—' }}</span>
                    </td>
                    <td class="max-w-xs px-3 py-2">{{ Str::limit($ticket->text ?? $ticket->subject, 80) }}</td>
                    <td class="px-3 py-2 whitespace-nowrap">
                        <x-umbra-ui::badge 
                            class="whitespace-nowrap"
                            variant="{{ $ticket->status === 'resolved' ? 'success' : ($ticket->status === 'pending' ? 'warning' : ($ticket->status === 'new' ? 'info' : 'default')) }}">
                            {{ \App\Models\Ticket::STATUSES[$ticket->status] ?? $ticket->status }}
                        </x-umbra-ui::badge>
                    </td>
                    <td class="px-3 py-2">{{ $ticket->created_at->format('d.m.Y H:i') }}</td>
                    <td class="px-3 py-2 whitespace-nowrap">
                        <div class="flex flex-col gap-2 min-w-[132px]">
                            <a
                                href="{{ route('admin.tickets.show', $ticket) }}"
                                class="inline-flex items-center justify-center rounded-md text-xs font-medium transition-colors px-2.5 py-1.5 border border-input bg-transparent hover:bg-zinc-800 cursor-pointer whitespace-nowrap"
                            >
                                Подробнее
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </x-umbra-ui::table.body>
        </x-umbra-ui::table>
    </x-umbra-ui::card>

    <div class="mt-6">
        {{ $tickets->links() }}
    </div>
</div>
@endsection