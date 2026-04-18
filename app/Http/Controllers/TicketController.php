<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;

class TicketController extends Controller
{
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

    public function show(Ticket $ticket)
    {
        $ticket->load('media');
        $statuses = Ticket::STATUSES;

        return view('admin.tickets.show', compact('ticket', 'statuses'));
    }

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
