@extends('student.layouts.student')

@section('title', 'Student Dashboard')
@section('student')
    <div class="container py-4">
        <div class="row">
            <div class="col-md-12 mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <h4>Ticket #{{ $ticket->ticket_number }}</h4>
                    <div>
                        <span
                            class="badge bg-{{ $ticket->status === 'open'
                                ? 'danger'
                                : ($ticket->status === 'in_progress'
                                    ? 'warning'
                                    : ($ticket->status === 'resolved'
                                        ? 'success'
                                        : 'secondary')) }}">
                            {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                        </span>
                        <span class="badge bg-info ms-2">{{ ucfirst($ticket->priority) }} Priority</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Ticket Details Card --}}
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Ticket Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <dl class="row">
                                    <dt class="col-sm-4">Subject:</dt>
                                    <dd class="col-sm-8">{{ $ticket->subject }}</dd>

                                    <dt class="col-sm-4">Department:</dt>
                                    <dd class="col-sm-8">{{ $ticket->department->name }}</dd>

                                    <dt class="col-sm-4">Created:</dt>
                                    <dd class="col-sm-8">{{ $ticket->created_at->format('M d, Y h:i A') }}</dd>
                                </dl>
                            </div>
                            <div class="col-md-6">
                                <dl class="row">
                                    <dt class="col-sm-4">Last Updated:</dt>
                                    <dd class="col-sm-8">{{ $ticket->updated_at->format('M d, Y h:i A') }}</dd>

                                    <dt class="col-sm-4">Status:</dt>
                                    <dd class="col-sm-8">{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</dd>

                                    <dt class="col-sm-4">Priority:</dt>
                                    <dd class="col-sm-8">{{ ucfirst($ticket->priority) }}</dd>
                                </dl>
                            </div>
                        </div>

                        <div class="mt-3">
                            <h6>Description:</h6>
                            <p class="mb-0">{{ $ticket->description }}</p>
                        </div>

                        @if ($ticket->attachments->count() > 0)
                            <div class="mt-4">
                                <h6>Attachments:</h6>
                                <div class="list-group">
                                    @foreach ($ticket->attachments as $attachment)
                                        <a href="{{ Storage::url($attachment->file_path) }}"
                                            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                                            target="_blank">
                                            <div>
                                                <i class="bi bi-paperclip me-2"></i>
                                                {{ $attachment->file_name }}
                                            </div>
                                            <span class="badge bg-primary rounded-pill">
                                                {{ \Illuminate\Support\Str::upper(pathinfo($attachment->file_name, PATHINFO_EXTENSION)) }}
                                            </span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Questions and Responses --}}
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Questions & Responses</h5>
                    </div>
                    <div class="card-body">
                        {{-- @dd($ticket) --}}
                        @forelse($ticket->questions as $index => $question)
                            <div class="question-response mb-4 {{ !$loop->last ? 'border-bottom pb-4' : '' }}">
                                <div class="question mb-3">
                                    <h6 class="text-primary">
                                        Question {{ $index + 1 }}:
                                    </h6>
                                    <p class="ms-3 mb-3">{{ Str::title($question->question) }}</p>

                                    @if ($question->responses->count() > 0)
                                        @foreach ($question->responses as $response)
                                            <div class="response bg-light p-3 rounded ms-3">
                                                <div class="d-flex justify-content-between">
                                                    <strong>Response from {{ $response->admin->user->full_name }}:</strong>
                                                    <small class="text-muted">
                                                        {{ $response->created_at->format('M d, Y h:i A') }}
                                                    </small>
                                                </div>
                                                <p class="mb-0 mt-2">{{ Str::title($response->response) }}</p>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="alert alert-info ms-3 mb-0">
                                            <i class="bi bi-info-circle me-2"></i>
                                            Awaiting response from support team
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="alert alert-warning mb-0">
                                <i class="bi bi-exclamation-circle me-2"></i>
                                No questions have been submitted for this ticket.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Reply Section (if ticket is not closed) --}}
            @if ($ticket->status !== 'closed')
                <div class="col-md-12 mt-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Add Reply</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('student.tickets.reply', $ticket) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <label for="editor" class="form-label">Your Reply</label>
                                    <textarea class="form-control" id="editor" name="reply" rows="3" required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="attachments" class="form-label">Attachments (optional)</label>
                                    <input type="file" class="form-control" id="attachments" name="attachments[]"
                                        multiple>
                                    <div class="form-text">You can upload multiple files. Maximum size per file: 10MB</div>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-send me-2"></i>Send Reply
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    <!-- Add this to your layout file -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        .question-response:last-child {
            margin-bottom: 0 !important;
            padding-bottom: 0 !important;
            border-bottom: none !important;
        }

        .response {
            border-left: 4px solid #0d6efd;
        }
    </style>
@endsection
