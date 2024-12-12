<?php

namespace App\Http\Controllers\Student;

use App\Models\Ticket;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\AITicketService;
use App\Http\Controllers\Controller;

class StudentSupportTicketController extends Controller
{
    protected $aiService;

    public function __construct(AITicketService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function index()
    {
        $supportTickets = Ticket::where('user_id', auth()->id())
            ->with(['department', 'questions'])
            ->latest()
            ->paginate(10);
        return view('student.supportTicket.index', compact('supportTickets'));
    }

    public function create()
    {
        $categories = ['General', 'Billing', 'Payment', 'Account', 'Other', 'Support', 'Feedback', 'Complaint', 'Request'];
        $priorities = ['low', 'medium', 'high'];
        return view('student.supportTicket.create', compact('categories', 'priorities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'priority' => 'required|in:low,medium,high',
            'category' => 'required|string',
            'description' => 'required|string',
            'questions' => 'required|array|min:1',
            'questions.*' => 'required|string',
            'attachments.*' => 'nullable|file|max:10240',
        ], [
            'questions.required' => 'Please add at least one question.',
            'questions.array' => 'Questions must be an array.',
            'questions.min' => 'At least one question is required.',
            'attachments.*.file' => 'File must be a file.',
            'attachments.*.max' => 'File size must be less than 10MB.',
        ]);

        $ticket = Ticket::create([
            'ticket_number' => 'TKT' . Str::random(8),
            'user_id' => auth()->id(),
            'subject' => $request->subject,
            'description' => $request->description,
            'priority' => $request->priority,
            'category' => $request->category,
            'status' => 'open',
            'department_id' =>  auth()->user()->student->department_id,
        ]);


        foreach ($request->questions as $questionText) {
            // Generate a unique message ID for this response
            // Generate RFC 2822 compliant message ID
            $messageId = sprintf(
                '%s.%s@%s',
                time(),
                substr(md5(uniqid(rand(), true)), 0, 10),
                parse_url(config('app.url'), PHP_URL_HOST) ?: 'localhost.com'
            );

            $question = $ticket->questions()->create([
                'question' => $questionText,
                'email_message_id' => $messageId,
            ]);

            // TODO: Generate AI suggestion for each question (pay for tokens openai)
            // $aiSuggestion = $this->aiService->suggestResponse($questionText);
            // $ticket->suggestedResponses()->create([
            //     'question_id' => $question->id,
            //     'suggested_response' => $aiSuggestion,
            // ]);
        }

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('ticket-attachments', 'public');
                $ticket->attachments()->create([
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getClientMimeType(),
                ]);
            }
        }


        // return redirect()->route('student.supportTicket.index')->with('success', 'Ticket created successfully!');
        return redirect()->route('student.support-tickets.show', $ticket)->with('success', 'Ticket created successfully.');
    }

    public function show(Ticket $ticket)
    {
        // $this->authorize('view', $ticket);
        // dd($ticket);

        $ticket->load(['questions', 'attachments', 'questions']);

        return view('student.supportTicket.show', compact('ticket'));
    }


    public function reply(Request $request, Ticket $ticket)
    {
        $request->validate([
            'reply' => 'required|string',
            'attachments.*' => 'nullable|file|max:10240',
        ]);

        // Generate RFC 2822 compliant message ID
        $messageId = sprintf(
            '%s.%s@%s',
            time(),
            substr(md5(uniqid(rand(), true)), 0, 10),
            parse_url(config('app.url'), PHP_URL_HOST) ?: 'localhost.com'
        );

        // Create new question
        $question = $ticket->questions()->create([
            'question' => $request->reply,
            'email_message_id' => $messageId,
            // 'in_reply_to' => $ticket->questions()->latest()->value('email_message_id'),
        ]);

        // Handle attachments if any
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('ticket-attachments', 'public');
                $ticket->attachments()->create([
                    'question_id' => $question->id,
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getClientMimeType(),
                ]);
            }
        }

        // Update ticket status if it was closed
        if ($ticket->status === 'resolved') {
            $ticket->update(['status' => 'in_progress']);
        }

        return redirect()->back()->with('success', 'Reply sent successfully');
    }
}
