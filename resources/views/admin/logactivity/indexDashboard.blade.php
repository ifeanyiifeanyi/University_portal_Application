@extends('admin.layouts.admin')

@section('title', 'Log Activity Archive')


@section('admin')
@include('admin.alert')
<div class="container">
    <nav aria-label="breadcrumb" class="mt-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.view.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Activity Log Archives</li>
        </ol>
    </nav>
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Activity Log Archives</h5>
                </div>
                <div class="card-body">
                    @if($archives->isEmpty())
                        <div class="alert alert-info" role="alert">
                            No archives available.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Month</th>
                                        <th>Size</th>
                                        <th>Created At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($archives as $archive)
                                        <tr>
                                            <td>
                                                @php
                                                    $monthNum = (int)$archive['month'];
                                                    $monthName = date('F', mktime(0, 0, 0, $monthNum, 1));
                                                    $monthInfo = match($monthNum) {
                                                        1 => 'January (New Year Archives)',
                                                        3 => 'March (Quarter End)',
                                                        6 => 'June (Mid-Year)',
                                                        9 => 'September (Q3 End)',
                                                        12 => 'December (Year End)',
                                                        default => $monthName
                                                    };
                                                @endphp
                                                {{ $monthInfo }}
                                            </td>
                                            <td>{{ number_format($archive['size'] / 1024, 2) }} KB</td>
                                            <td>{{ date('jS M Y', $archive['created_at']) }}</td>
                                            <td>
                                                <a href="{{ route('activity-archives.download', basename($archive['name'])) }}"
                                                   class="btn btn-primary btn-sm">
                                                    <i class="fas fa-download me-1"></i> Download
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection



@section('css')

@endsection
@section('javascript')

@endsection
