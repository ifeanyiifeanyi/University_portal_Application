@extends('admin.layouts.admin')

@section('title', 'Site Activity')

@section('admin')
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">{{ __('All Activities') }}</h5>
        </div>

        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            <div class="row">
                @foreach ($activities as $activity)
                    <div class="col-md-12 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-light">
                                <h6 class="card-title mb-0">
                                    <span class="text-muted">Action by:</span>
                                    <strong>{{ $activity->causer->full_name ?? 'System' }}</strong>
                                </h6>
                            </div>

                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="card-text">
                                            <span class="text-muted">Model:</span>
                                            <strong>{{ class_basename($activity->subject_type) }}</strong>
                                        </p>
                                        <p class="card-text">
                                            <span class="text-muted">Subject:</span>
                                            @if (is_object($activity->subject))
                                                <strong>{{ $activity->subject->name ?? $activity->subject->id }}</strong>
                                            @else
                                                <strong>N/A</strong>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="card-text">
                                            <span class="text-muted">Description:</span>
                                            <strong>{{ $activity->description }}</strong>
                                        </p>
                                        <p class="card-text">
                                            <span class="text-muted">Date:</span>
                                            <strong>{{ $activity->created_at->format('Y-m-d H:i:s') }}</strong>
                                        </p>
                                    </div>
                                </div>

                                <hr>

                                <div class="mt-3">
                                    <h6 class="text-muted">Details:</h6>
                                    <pre class="bg-light p-3 rounded"><code>{{ json_encode($activity->properties['original_data'] ?? $activity->properties, JSON_PRETTY_PRINT) }}</code></pre>
                                </div>
                            </div>

                            <div class="card-footer bg-light">
                                <form action="{{ route('activities.destroy', $activity->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this activity?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{-- {{ $activities->links() }} --}}
            </div>
        </div>
    </div>
@endsection
