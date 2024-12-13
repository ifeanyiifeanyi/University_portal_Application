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
use Illuminate\Database\Eloquent\Collection;
use App\Notifications\TicketResponseNotification;
use App\Notifications\TicketStatusUpdateNotification;
use App\Notifications\TicketPriorityUpdateNotification;

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
        $request->validate([
            'responses' => 'required|array',
            'responses.*' => 'required|string',
        ]);

        // Initialize an empty Eloquent Collection instead of a basic Collection
        $responses = new Collection();

        foreach ($request->responses as $questionId => $responseText) {
            // Generate RFC 2822 compliant message ID
            $messageId = sprintf(
                '%s.%s@%s',
                time(),
                substr(md5(uniqid(rand(), true)), 0, 10),
                parse_url(config('app.url'), PHP_URL_HOST) ?: 'localhost.com'
            );

            $question = $ticket->questions()->findOrFail($questionId);

            $response = TicketResponse::create([
                'ticket_id' => $ticket->id,
                'question_id' => $questionId,
                'admin_id' => auth()->id(),
                'response' => $responseText,
                'is_ai_response' => $request->input("use_ai.$questionId", false),
                'email_message_id' => $messageId,
                'in_reply_to' => $question->email_message_id,
                'sent_at' => now(),
            ]);

            $responses->push($response);
        }

        // Send response notification to student
        $ticket->user->notify(new TicketResponseNotification($ticket, $responses));



        $oldStatus = $ticket->status;
        $ticket->update(['status' => 'in_progress']);

        // Notify student of status change if it changed
        if ($oldStatus !== 'in_progress') {
            $ticket->user->notify(new TicketStatusUpdateNotification(
                $ticket,
                $oldStatus,
                'in_progress'
            ));
        }

        return redirect()->back()->with('success', 'Responses sent successfully');
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed'
        ]);

        $oldStatus = $ticket->status;
        $ticket->update(['status' => $request->status]);

        // Notify student of status change
        $ticket->user->notify(new TicketStatusUpdateNotification(
            $ticket,
            $oldStatus,
            $request->status
        ));

        return redirect()->back()->with('success', 'Ticket status updated successfully');
    }


    public function updatePriority(Request $request, Ticket $ticket)
    {
        $request->validate([
            'priority' => 'required|in:low,medium,high'
        ]);

        $oldPriority = $ticket->priority;
        $ticket->update(['priority' => $request->priority]);


        $ticket->user->notify(new TicketPriorityUpdateNotification(
            $ticket,
            $oldPriority,
            $request->priority
        ));

        return redirect()->back()->with('success', 'Ticket priority updated successfully');
    }
}
