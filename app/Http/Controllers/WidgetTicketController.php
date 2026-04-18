<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class WidgetTicketController extends Controller
{
    public function create(): View
    {
        return view('widget.tickets.create');
    }

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
