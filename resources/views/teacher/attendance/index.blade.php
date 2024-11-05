@extends('teacher.layouts.teacher')

@section('title', 'Teacher Attendance')
@section('css')

@endsection



@section('teacher')
<div class="row mt-5">
        <div class="col-md-6"></div>
    <div class="ms-auto col-md-6 mb-3">
        <a href="{{route('teacher.view.create.attendance')}}" class="btn w-100 btn-success" style="color: #ffffff">Create new attendance</a>
    </div>
    </div>
<section class="section">

    <div class="row">
      <div class="col-lg-12">

        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Attendance created</h5>
            <!-- Table with stripped rows -->
            <table class="table datatable">
              <thead>
                  <tr>
                      <th>Session</th>
                      <th>Semester</th>
                      <th>Department</th>
                      <th>Course name</th>
                      <th>Date & Time</th>
                      <th>Attendance Stats</th>
                      <th>Action</th>
                  </tr>
              </thead>
              <tbody>
                  @forelse ($attendances as $attendance)
                      <tr>
                          <td>{{ $attendance['session'] }}</td>
                          <td>{{ $attendance['semester'] }}</td>
                          <td>{{ $attendance['department'] }}</td>
                          <td>{{ $attendance['course'] }}</td>
                          <td>
                              <div>{{ \Carbon\Carbon::parse($attendance['date'])->format('d F, Y') }}</div>
                              <small class="text-muted">
                                  {{ \Carbon\Carbon::parse($attendance['start_time'])->format('h:i A') }} - 
                                  {{ \Carbon\Carbon::parse($attendance['end_time'])->format('h:i A') }}
                              </small>
                          </td>
                          <td>
                              <div>Present: {{ $attendance['present_students'] }}/{{ $attendance['total_students'] }}</div>
                              <div class="progress" style="height: 15px;">
                                  @php
                                      $percentage = $attendance['total_students'] > 0 
                                          ? ($attendance['present_students'] / $attendance['total_students']) * 100 
                                          : 0;
                                  @endphp
                                  <div class="progress-bar bg-success" role="progressbar" 
                                       style="width: {{ $percentage }}%;" 
                                       aria-valuenow="{{ $percentage }}" 
                                       aria-valuemin="0" 
                                       aria-valuemax="100">
                                      {{ round($percentage) }}%
                                  </div>
                              </div>
                          </td>
                          <td>
                              <a href="{{ route('teacher.view.attendees', [
                                  'attendanceid' => $attendance['attendanceid'],
                                  'departmentid' => $attendance['departmentid'],
                                  'courseid' => $attendance['courseid']
                              ]) }}" 
                              class="btn btn-success w-100" style="color: #ffffff">
                                  View attendees
                              </a>
                          </td>
                      </tr>
                  @empty
                      <tr>
                          <td colspan="7" class="text-center">No attendance records found.</td>
                      </tr>
                  @endforelse
              </tbody>
          </table>
            <!-- End Table with stripped rows -->

          </div>
        </div>

      </div>
    </div>
  </section>
@endsection