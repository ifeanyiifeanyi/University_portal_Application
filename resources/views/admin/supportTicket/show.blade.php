@extends('admin.layouts.admin')

@section('title', 'Support Ticket Details')


@section('admin')
    @include('admin.alert')
    <div class="container">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Ticket #{{ $ticket->ticket_number }}</h5>
                <span
                    class="badge bg-{{ $ticket->status === 'open' ? 'danger' : ($ticket->status === 'in_progress' ? 'warning' : 'success') }}">
                    {{ ucfirst($ticket->status) }}
                </span>
            </div>
            <div class="card-body">
                <h6><b>Subject:</b> {{ Str::title($ticket->subject) }}</h6>
                <p><b>Description:</b> {{ $ticket->description }}</p>
                <p><b>Department:</b> {{ $ticket->department->name }}</p>
                <p>
                    <b>Student:</b> {{ $ticket->user->full_name }}
                    ({{ $ticket->user->student->matric_number }})
                </p>
                <p><b>Created At:</b> {{ $ticket->created_at->format('F j, Y, g:i a') }}</p>

                @if ($ticket->attachments->count() > 0)
                    <div class="mb-3">
                        <h6>Attachments:</h6>
                        <ul class="list-unstyled">
                            @foreach ($ticket->attachments as $attachment)
                                <li>
                                    <a href="{{ Storage::url($attachment->file_path) }}" target="_blank">
                                        <i class="fas fa-file-archive"></i> {{ $attachment->file_name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>

        <form action="{{ route('admin.support_tickets.respond', $ticket) }}" method="POST">
            @csrf
            @foreach ($ticket->questions as $question)
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">{{ $loop->iteration }}. Question: {{ $question->question }}</h6>
                    </div>
                    <div class="card-body">
                        @if ($question->suggestedResponses->count() > 0)
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="use_ai[{{ $question->id }}]"
                                        id="useAi{{ $question->id }}" onchange="useAiResponse(this, {{ $question->id }})">
                                    <label class="form-check-label" for="useAi{{ $question->id }}">
                                        Use AI Suggested Response
                                    </label>
                                </div>
                                <div class="alert alert-info mt-2">
                                    <strong>AI Suggestion:</strong>
                                    <p id="aiResponse{{ $question->id }}">
                                        {{ $question->suggestedResponses->first()->suggested_response }}
                                    </p>
                                </div>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label">Your Response:</label>
                            <textarea name="responses[{{ $question->id }}]" class="form-control" rows="3" id="response{{ $question->id }}"
                                required></textarea>
                        </div>
                    </div>
                </div>
            @endforeach

            <button type="submit" class="btn btn-primary">Send Responses</button>
        </form>
    </div>
@endsection



@section('css')

@endsection
@section('javascript')
    <script>
        function useAiResponse(checkbox, questionId) {
            const responseField = document.getElementById('response' + questionId);
            const aiResponse = document.getElementById('aiResponse' + questionId).textContent.trim();

            if (checkbox.checked) {
                responseField.value = aiResponse;
            } else {
                responseField.value = '';
            }
        }
    </script>
@endsection
