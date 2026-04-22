<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use OpenApi\Attributes as OA;

class WidgetTicketController extends Controller
{
    #[OA\Get(
        path: '/widget',
        operationId: 'widgetTicketsFormIndex',
        summary: 'Форма виджета',
        description: 'Возвращает HTML-страницу формы создания заявки',
        tags: ['Widget Tickets'],
        responses: [
            new OA\Response(response: 200, description: 'HTML страница формы'),
        ]
    )]
    #[OA\Get(
        path: '/widget/tickets/create',
        operationId: 'widgetTicketsFormCreate',
        summary: 'Форма виджета (альтернативный путь)',
        description: 'Возвращает HTML-страницу формы создания заявки',
        tags: ['Widget Tickets'],
        responses: [
            new OA\Response(response: 200, description: 'HTML страница формы'),
        ]
    )]
    #[OA\Get(
        path: '/feedback-widget',
        operationId: 'feedbackWidgetForm',
        summary: 'Форма обратной связи',
        description: 'Возвращает HTML-страницу формы обратной связи',
        tags: ['Widget Tickets'],
        responses: [
            new OA\Response(response: 200, description: 'HTML страница формы'),
        ]
    )]
    public function create(): View
    {
        return view('widget.tickets.create');
    }

    #[OA\Post(
        path: '/api/tickets',
        operationId: 'storeWidgetTicket',
        description: 'Создание заявки из виджета обратной связи',
        summary: 'Создать заявку',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'email', 'phone', 'subject', 'description'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', maxLength: 255, example: 'Иван Иванов'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', maxLength: 255, example: 'ivan@example.com'),
                    new OA\Property(property: 'phone', type: 'string', maxLength: 50, example: '+79991234567'),
                    new OA\Property(property: 'subject', type: 'string', maxLength: 255, example: 'Ошибка при сохранении'),
                    new OA\Property(property: 'description', type: 'string', example: 'Не удается отправить форму обратной связи'),
                ]
            )
        ),
        tags: ['Widget Tickets'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Заявка успешно создана',
                content: new OA\JsonContent(ref: '#/components/schemas/TicketCreateResponse')
            ),
            new OA\Response(response: 422, description: 'Ошибка валидации'),
            new OA\Response(response: 429, description: 'Лимит заявок на сутки превышен'),
        ]
    )]
    public function storeApi(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'attachment' => ['nullable', 'file', 'max:10240'],
        ]);

        $alreadySentToday = Ticket::query()
            ->whereDate('created_at', now()->toDateString())
            ->whereHas('customer', function ($query) use ($data) {
                $query->where('email', $data['email'])
                    ->orWhere('phone', $data['phone']);
            })
            ->exists();

        if ($alreadySentToday) {
            return response()->json([
                'message' => 'Вы уже отправляли заявку сегодня с этого email или номера телефона.',
            ], 429);
        }

        $customer = Customer::firstOrCreate(
            ['email' => $data['email']],
            ['name' => $data['name'], 'phone' => $data['phone']]
        );

        $ticket = Ticket::create([
            'customer_id' => $customer->id,
            'subject' => $data['subject'],
            'description' => $data['description'],
            'status' => 'new',
            'reply_date' => null,
        ]);

        if ($request->hasFile('attachment')) {
            $ticket->addMedia($request->file('attachment'))->toMediaCollection('attachments');
        }

        return response()->json([
            'message' => 'Заявка успешно отправлена.',
            'ticket_id' => $ticket->id,
        ], 201);
    }
}
