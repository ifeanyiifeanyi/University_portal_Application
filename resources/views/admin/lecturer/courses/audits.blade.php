@extends('admin.layouts.admin')

@section('title', 'Audits')
@section('css')

@endsection



@section('admin')
    <div class="container">
        <div class="card p-3">
            <h5>Score Audits <br> <code>{{ $teacher->teacher_title }} {{ $teacher->user->fullName() }}</code></h4>
        </div>
        @foreach ($groupedAudits as $session => $semesters)
            <div class="card p-3"
                style="display: flex !important; flex-direction: row; justify-content:space-between;flex-wrap:wrap">
                <h5>Academic Session: <code class="lead">{{ $session }}</code></h5>
                @foreach ($semesters as $semester => $courses)
                    <h5>Semester: <code class="lead">{{ $semester }}</code></h5>
            </div>
            @foreach ($courses as $course => $audits)
                <div class="card p-3">
                    <div class="table-responsive">
                        <table id="example" class="table table-bordered table-striped border-dark">
                            <h4 class="mb-3">{{ $course }}</h4>

                            <thead class="border-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Student</th>
                                    <th>Action</th>
                                    <th>Changed Fields</th>
                                    <th>User</th>
                                    <th>IP Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($audits as $audit)
                                    <tr>
                                        <th>{{ $loop->iteration }}</th>
                                        <td>{{ $audit->created_at->format('D, M j, Y g:i A') }}</td>
                                        <td>{{ $audit->studentScore->student->user->fullName() }}</td>
                                        <td>{{ $audit->action }}</td>
                                        <td>{{ Str::limit($audit->comment, 25) }}</td>
                                        <td>{{ $audit->user->fullName() }}</td>
                                        <td>{{ $audit->ip_address }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        @endforeach
        @endforeach

    </div>

@endsection


@section('javascript')

@endsection
