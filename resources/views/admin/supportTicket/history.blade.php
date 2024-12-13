@extends('admin.layouts.admin')

@section('title', 'Support Ticket History')


@section('admin')
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Conversation History</h5>
        </div>
        <div class="card-body">
            @forelse($conversationHistory as $thread)
                <div class="conversation-thread mb-4 {{ !$loop->last ? 'border-bottom pb-4' : '' }}">
                    <div class="question-block bg-light p-3 rounded mb-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong class="d-block">{{ $loop->iteration }}. Student Question</strong>
                                <p class="mb-1">{{ $thread['question']->question }}</p>
                                <small class="text-muted">
                                    Asked {{ $thread['question']->created_at->format('M d, Y g:ia') }}
                                </small>
                            </div>
                            @if (!$thread['has_response'])
                                <span class="badge bg-warning">Awaiting Response</span>
                            @endif
                        </div>
                    </div>

                    @if ($thread['has_response'])
                        @foreach ($thread['responses'] as $response)
                            <div class="response-block ms-4 p-3 border-start border-success">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="d-flex align-items-center mb-2">
                                            <strong class="me-2">{{ $loop->iteration }}. Response from {{ $response->admin->user->full_name }}</strong>
                                            @if ($response->is_ai_response)
                                                <span class="badge bg-info">AI Assisted</span>
                                            @endif
                                        </div>
                                        <p class="mb-1">{{ $response->response }}</p>
                                        <small class="text-muted">
                                            Responded {{ $response->created_at->format('M d, Y g:ia') }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            @empty
                <div class="text-center py-4">
                    <i class="bi bi-chat-dots h1 text-muted"></i>
                    <p>No questions or responses yet.</p>
                </div>
            @endforelse
        </div>
    </div>



@endsection



@section('css')
    <style>
        .conversation-thread:last-child {
            margin-bottom: 0 !important;
            padding-bottom: 0 !important;
            border-bottom: none !important;
        }

        .response-block {
            position: relative;
            background-color: #f8f9fa;
        }

        .response-block::before {
            content: '';
            position: absolute;
            left: -1px;
            top: 0;
            bottom: 0;
            width: 3px;
            background-color: #198754;
        }
    </style>
@endsection
@section('javascript')

@endsection
