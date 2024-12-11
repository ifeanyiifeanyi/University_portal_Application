@extends('student.layouts.student')

@section('title', 'Student Dashboard')
@section('student')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">

                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mt-5 mb-3">Support Tickets</h4>
                    <a href="{{ route('student.support-tickets.create') }}" class="btn btn-success float-end btn-sm">Create Support Ticket</a>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('student.view.dashboard') }}">Student</a></li>
                            <li class="breadcrumb-item active">Support Tickets</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-centered table-nowrap mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 20px;">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="customCheck1">
                                                <label class="form-check-label" for="customCheck1">&nbsp;</label>
                                            </div>
                                        </th>
                                        <th style="width: 20px;">#</th>
                                        <th>Title</th>
                                        <th>Category</th>
                                        <th>Priority</th>
                                        <th>Created At</th>
                                        <th style="width: 120px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($supportTickets as $supportTicket)
                                        <tr>
                                            <td>
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input" id="customCheck2">
                                                    <label class="form-check-label" for="customCheck2">&nbsp;</label>
                                                </div>
                                            </td>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ Str::title($supportTicket->subject) }}</td>
                                            <td>{{ $supportTicket->category }}</td>
                                            <td>{{ $supportTicket->priority }}</td>
                                            <td>{{ $supportTicket->created_at }}</td>
                                            <td class="table-action btn-group">
                                                <a href="{{ route('student.support-tickets.show', $supportTicket) }}"
                                                    class="btn btn-sm btn-info">View</a>
                                                <a href="" class="btn btn-danger btn-sm">Delete</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- container-fluid -->
@endsection
