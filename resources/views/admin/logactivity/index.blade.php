@extends('admin.layouts.admin')

@section('title', 'Site Activity')
@section('admin')
    <div class="card">
        <div class="card-header">{{ __('All Activities') }}</div>

        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            <div class="row">
                @foreach ($activities as $activity)
                    <div class="col-md-12 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title"><span class="text-muted">Action: </span>{{ $activity->causer->full_name }}</h5>
                                <h6 class="card-subtitle mb-2 text-muted"><span class="text-muted">Model: </span> {{ $activity->subject_type }}</h6>

                                <p class="card-text">
                                    <span class="text-muted">Subject: </span>
                                    @if (is_object($activity->subject))
                                        {{ $activity->subject->name }}
                                    @else
                                        {{ $activity->subject }}
                                    @endif
                                </p>
                                <p class="card-text"><span class="text-muted">Description: </span> {{ $activity->description }}</p>
                                <hr>
                                <form action="{{ route('activities.destroy', $activity->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this activity?')">
                                        <div class="fas fa-trash fa-2x"></div>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
