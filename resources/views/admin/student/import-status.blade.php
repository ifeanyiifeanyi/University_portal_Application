@extends('admin.layouts.admin')

@section('admin')
    <div class="container my-5">
        <h1>Student Import Status</h1>

        @if (session('message'))
            <div class="alert alert-success">
                {{ session('message') }}
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Batch ID: {{ $batchId }}</h5>

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Successful Imports</th>
                                <th>Failed Imports</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    @if ($results['failed'] === 0)
                                        <span class="badge badge-success">Completed</span>
                                    @else
                                        <span class="badge badge-danger">Failed</span>
                                    @endif
                                </td>
                                <td>{{ $results['success'] }}</td>
                                <td>{{ $results['failed'] }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                @if ($results['failed'] > 0)
                    <h5 class="card-title mt-4">Failed Imports</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Row</th>
                                    <th>Error</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($results['errors'] as $error)
                                    <tr>
                                        <td>{{ $error['row'] }}</td>
                                        <td>{{ $error['error'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
