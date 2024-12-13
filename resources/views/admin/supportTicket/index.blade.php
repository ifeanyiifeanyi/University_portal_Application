@extends('admin.layouts.admin')

@section('title', 'Student Support Tickets Management')

@section('admin')
    @include('admin.alert')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0 float-start">Ticket Management</h5>

                        <div class="float-end">
                            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="collapse"
                                data-bs-target="#filterSection">
                                <i class="bi bi-funnel"></i> Filters
                            </button>
                        </div>
                    </div>

                    <div class="collapse" id="filterSection">
                        <div class="card-body border-bottom">
                            <form action="{{ route('admin.support_tickets.index') }}" method="GET" class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="">All Status</option>
                                        <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open
                                        </option>
                                        <option value="in_progress"
                                            {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>
                                            Resolved</option>
                                        <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed
                                        </option>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Priority</label>
                                    <select name="priority" class="form-select">
                                        <option value="">All Priorities</option>
                                        <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low
                                        </option>
                                        <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>
                                            Medium</option>
                                        <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High
                                        </option>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Department</label>
                                    <select name="department" class="form-select">
                                        <option value="">All Departments</option>
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->id }}"
                                                {{ request('department') == $department->id ? 'selected' : '' }}>
                                                {{ $department->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Date Range</label>
                                    <select name="date_range" class="form-select">
                                        <option value="">All Time</option>
                                        <option value="today" {{ request('date_range') === 'today' ? 'selected' : '' }}>
                                            Today</option>
                                        <option value="week" {{ request('date_range') === 'week' ? 'selected' : '' }}>
                                            This Week</option>
                                        <option value="month" {{ request('date_range') === 'month' ? 'selected' : '' }}>
                                            This Month</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Search</label>
                                    <input type="text" name="search" class="form-control"
                                        value="{{ request('search') }}"
                                        placeholder="Search by ticket number, subject, or description...">
                                </div>

                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                                    <a href="{{ route('admin.support_tickets.index') }}" class="btn btn-secondary">Reset</a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>
                                            <a href="{{ route('admin.support_tickets.index', array_merge(request()->query(), ['sort' => 'ticket_number'])) }}"
                                                class="text-decoration-none text-dark">
                                                Ticket
                                                @if (request('sort') === 'ticket_number')
                                                <i class="fas fa-arrows-alt-v"></i>                                               @endif
                                            </a>
                                        </th>
                                        <th>Subject</th>
                                        <th>Student</th>
                                        <th>
                                            <a href="{{ route('admin.support_tickets.index', array_merge(request()->query(), ['sort' => 'created_at'])) }}"
                                                class="text-decoration-none text-dark">
                                                Created
                                                @if (request('sort') === 'created_at')
                                                <i class="fas fa-arrows-alt-v"></i>
                                                @endif
                                            </a>
                                        </th>
                                        <th>Status</th>
                                        <th>Priority</th>
                                        <th>Last Update</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($tickets as $ticket)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $ticket->ticket_number }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if ($ticket->questions()->count() > 0)
                                                        <span class="badge bg-info rounded-pill me-2">
                                                            {{ $ticket->questions()->count() }}
                                                        </span>
                                                    @endif
                                                    {{ Str::title(Str::limit($ticket->subject, 50)) }}
                                                </div>
                                            </td>
                                            <td>{{ Str::title($ticket->user->full_name) }}</td>
                                            <td>{{ $ticket->created_at->format('M d, Y') }}</td>
                                            <td>
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
                                            </td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $ticket->priority === 'high' ? 'danger' : ($ticket->priority === 'medium' ? 'warning' : 'info') }}">
                                                    {{ ucfirst($ticket->priority) }}
                                                </span>
                                            </td>
                                            <td>{{ $ticket->updated_at->diffForHumans() }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('admin.support_tickets.show', $ticket) }}"
                                                        class="btn btn-sm btn-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-secondary dropdown-toggle"
                                                        data-bs-toggle="dropdown">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li>
                                                            <form
                                                                action="{{ route('admin.support_tickets.update_status', $ticket) }}"
                                                                method="POST">
                                                                @csrf
                                                                @method('PATCH')
                                                                <input type="hidden" name="status" value="in_progress">
                                                                <button type="submit" class="dropdown-item">
                                                                    Mark In Progress
                                                                </button>
                                                            </form>
                                                        </li>
                                                        <li>
                                                            <form
                                                                action="{{ route('admin.support_tickets.update_status', $ticket) }}"
                                                                method="POST">
                                                                @csrf
                                                                @method('PATCH')
                                                                <input type="hidden" name="status" value="resolved">
                                                                <button type="submit" class="dropdown-item">
                                                                    Mark Resolved
                                                                </button>
                                                            </form>
                                                        </li>
                                                        <li>
                                                            <form
                                                                action="{{ route('admin.support_tickets.update_priority', $ticket) }}"
                                                                method="POST">
                                                                @csrf
                                                                @method('PATCH')
                                                                <input type="hidden" name="priority" value="high">
                                                                <button type="submit" class="dropdown-item text-danger">
                                                                    Set High Priority
                                                                </button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-4">
                                                <div class="d-flex flex-column align-items-center">
                                                    <i class="bi bi-inbox h1 text-muted"></i>
                                                    <i class="fas fa-inbox h1 text-muted"></i>
                                                    <p class="mb-0">No tickets found</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div>
                                Showing {{ $tickets->firstItem() ?? 0 }} to {{ $tickets->lastItem() ?? 0 }}
                                of {{ $tickets->total() }} tickets
                            </div>
                            {{ $tickets->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection








@section('css')
<!-- Add this to your layout file -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
@endsection
@section('javascript')
<script>
    // Persist collapse state
    document.addEventListener('DOMContentLoaded', function() {
        const filterSection = document.getElementById('filterSection');
        if (new URLSearchParams(window.location.search).toString()) {
            filterSection.classList.add('show');
        }
    });
</script>
@endsection
