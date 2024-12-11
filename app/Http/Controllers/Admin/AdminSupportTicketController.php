<?php

namespace App\Http\Controllers\Admin;

use App\Models\Ticket;
use App\Models\Department;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\TicketResponse;
use App\Mail\TicketResponseMail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

class AdminSupportTicketController extends Controller
{

    public function index(Request $request)
    {
        $query = Ticket::with(['department', 'user', 'questions'])
            ->when(auth()->user()->department_id, function ($query) {
                return $query->where('department_id', auth()->user()->department_id);
            });

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('department')) {
            $query->where('department_id', $request->department);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('ticket_number', 'like', "%{$request->search}%")
                    ->orWhere('subject', 'like', "%{$request->search}%")
                    ->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        // Date range filter
        if ($request->filled('date_range')) {
            switch ($request->date_range) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('created_at', now()->month);
                    break;
            }
        }

        // Sorting
        if ($request->filled('sort')) {
            $query->orderBy($request->sort, $request->filled('direction') ? $request->direction : 'desc');
        } else {
            $query->latest();
        }

        $tickets = $query->paginate(15)->withQueryString();
        $departments = Department::all();

        return view('admin.supportTicket.index', compact('tickets', 'departments'));
    }

    public function show(Ticket $ticket)
    {
        $ticket->load(['questions.responses', 'questions.suggestedResponses', 'attachments']);
        return view('admin.supportTicket.show', compact('ticket'));
    }


    public function respond(Request $request, Ticket $ticket)
    {
        // dd($ticket->user->email);
        $request->validate([
            'responses' => 'required|array',
            'responses.*' => 'required|string',
        ]);

        foreach ($request->responses as $questionId => $responseText) {
            // Generate a unique message ID for this response
            $messageId = '<' . Str::uuid() . '@' . config('app.url') . '>';

            // Get the original question's message ID to link the response
            $question = $ticket->questions()->findOrFail($questionId);
            $inReplyTo = $question->email_message_id;

            $response = TicketResponse::create([
                'ticket_id' => $ticket->id,
                'question_id' => $questionId,
                'admin_id' => auth()->id(),
                'response' => $responseText,
                'is_ai_response' => $request->input("use_ai.$questionId", false),
                'email_message_id' => $messageId,
                'in_reply_to' => $inReplyTo,
                'sent_at' => now(),
            ]);

            // Send email to student
            Mail::to($ticket->user->email)->send(
                new TicketResponseMail($ticket, $response)
            );
        }

        $ticket->update(['status' => 'in_progress']);

        return redirect()->back()->with('success', 'Responses sent successfully');
    }
}
