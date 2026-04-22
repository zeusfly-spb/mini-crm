<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use OpenApi\Attributes as OA;

class TicketController extends Controller
{
    #[OA\Get(
        path: '/admin/tickets',
        operationId: 'adminTicketsIndex',
        summary: 'Список заявок',
        description: 'Возвращает HTML-страницу со списком заявок и фильтрами',
        tags: ['Admin Tickets'],
        parameters: [
            new OA\Parameter(name: 'status', in: 'query', required: false, schema: new OA\Schema(type: 'string', enum: ['new', 'pending', 'resolved'])),
            new OA\Parameter(name: 'date_from', in: 'query', required: false, schema: new OA\Schema(type: 'string', format: 'date')),
            new OA\Parameter(name: 'date_to', in: 'query', required: false, schema: new OA\Schema(type: 'string', format: 'date')),
            new OA\Parameter(name: 'email', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'phone', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'HTML страница списка заявок'),
        ]
    )]
    public function index(Request $request)
    {
        $tickets = Ticket::query()
            ->with('customer')
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->string('status'));
            })
            ->when($request->filled('date_from'), function ($query) use ($request) {
                $query->whereDate('created_at', '>=', $request->string('date_from'));
            })
            ->when($request->filled('date_to'), function ($query) use ($request) {
                $query->whereDate('created_at', '<=', $request->string('date_to'));
            })
            ->when($request->filled('email'), function ($query) use ($request) {
                $email = trim((string) $request->input('email'));

                $query->whereHas('customer', function ($customerQuery) use ($email) {
                    $customerQuery->where('email', 'like', '%' . $email . '%');
                });
            })
            ->when($request->filled('phone'), function ($query) use ($request) {
                $phone = trim((string) $request->input('phone'));

                $query->whereHas('customer', function ($customerQuery) use ($phone) {
                    $customerQuery->where('phone', 'like', '%' . $phone . '%');
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.tickets.index', compact('tickets'));
    }

    #[OA\Get(
        path: '/admin/tickets/{ticket}',
        operationId: 'adminTicketsShow',
        summary: 'Карточка заявки',
        description: 'Возвращает HTML-страницу просмотра конкретной заявки',
        tags: ['Admin Tickets'],
        parameters: [
            new OA\Parameter(name: 'ticket', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'HTML страница заявки'),
            new OA\Response(response: 404, description: 'Заявка не найдена'),
        ]
    )]
    public function show(Ticket $ticket)
    {
        $ticket->load('media');
        $statuses = Ticket::STATUSES;

        return view('admin.tickets.show', compact('ticket', 'statuses'));
    }

    #[OA\Put(
        path: '/admin/tickets/{ticket}',
        operationId: 'adminTicketsUpdate',
        summary: 'Обновить статус заявки',
        description: 'Обновляет статус заявки и делает redirect на страницу заявки',
        tags: ['Admin Tickets'],
        parameters: [
            new OA\Parameter(name: 'ticket', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['status'],
                properties: [
                    new OA\Property(property: 'status', type: 'string', enum: ['new', 'pending', 'resolved'], example: 'pending'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 302, description: 'Редирект после обновления'),
            new OA\Response(response: 422, description: 'Ошибка валидации'),
        ]
    )]
    public function update(Request $request, Ticket $ticket)
    {
        $data = $request->validate([
            'status' => ['required', 'string', 'in:' . implode(',', array_keys(Ticket::STATUSES))],
        ]);

        $ticket->update([
            'status' => $data['status'],
        ]);

        return redirect()
            ->route('admin.tickets.show', $ticket)
            ->with('status', 'Статус заявки обновлён.');
    }
}
