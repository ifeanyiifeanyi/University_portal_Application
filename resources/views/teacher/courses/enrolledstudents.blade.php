@extends('teacher.layouts.teacher')

@section('title', 'Enrolled students')
@section('css')

@endsection



@section('teacher')
@if($students->isEmpty())
        {{-- <p>No students have registered for this course yet.</p> --}}
        <div class="alert alert-warning">
          No students have registered for this course yet
          <div class="mt-3">
            <a href="javascript:window.history.back()" style="background: #AE152D;" class="btn text-white w-50">Back</a>
          </div>
      </div>
    @else

<div class="container-xxl mt-3">
    <div class="row">
        <div class="col-md-6">
            <form action="{{ route('importassessment.csv') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="course_id" value="{{$courseId}}" id="">
                <div class="form-group">
                    <input type="file" class="form-control" name="assessment_import" id="" required>
                </div>
                <div class="form-group mt-2">
                    <button class="btn w-100 text-white" style="background: #AE152D;">Import excel</button>
                </div>
                
            </form>
            
        </div>
    <div class="ms-auto mb-4 col-md-6">
        <a href="{{route('exportassessment',['id'=>$courseId])}}" class="btn w-100 text-white" style="background: #AE152D;">Export excel</a>
    </div>
    </div>
<section class="section mt-4">
    <div class="row">
      <div class="col-lg-12">
        {{-- @include('messages') --}}
        @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show alert-fail-bg" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif

        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Students enrolled</h5>
            <!-- Table with stripped rows -->
            <form action="{{route('teacher.upload.result',['courseid'=>$courseId])}}" method="POST">
              @csrf
            <table class="table datatable">
              <thead>
                <tr>
                  
                  <th>Student Id</th>
                  <th>Student Name</th>
                  <th>Course Name</th>
                  <th>Course Code</th>
                  <th>Assessment score</th>
                  <th>Exam score</th>
                  <th>Total</th>
                  <th>Grade</th>
                  <th>Approval status</th>
                  <th>Failed Status</th>
                 
                </tr>
              </thead>
              <tbody>
               
                @forelse ($students as $student)
               
                <tr>
                  <td>
                    {{$student->student->matric_number}}
                  </td>
                  <td>{{$student->student->user->first_name}} {{$student->student->user->last_name}} {{$student->student->user->other_name}}</td>
                  <td>
                    {{$student->course->title}}
                  </td>
                  <td>
                    {{$student->course->code}}
                  </td>
              
                <td>
                  <input type="text" name="scores[{{ $student->id }}][assessment]" 
                         value="{{ $student->studentScore->assessment_score ?? '' }}" 
                         class="form-control assessment-score" min="0" max="40"
                         step="0.01"
                         required>
              </td>
                   
                  
                  <td>
                    <div class="form-group">
                      <input type="number"
                                                class="form-control exam-score" min="0" max="60" step="0.01"
                                                name="scores[{{ $student->id }}][exam]" 
                                                value="{{ $student->studentScore->exam_score ?? '' }}" 
                                                required>
                    </div>
                   
                  </td>
                  <td>
                    <div class="form-group">
                      <input type="number"
                      class="form-control total-score"
                      name="scores[{{ $student->id }}][total]" 
                             value="{{ $student->studentScore->total_score ?? '' }}" 
                       readonly>
                    </div>
                  </td>
                  <td>
                    
                    <div class="form-group">
                        <input type="text" class="form-control grade" name="scores[{{ $student->id }}][grade]" 
                        value="{{ $student->studentScore->grade ?? '' }}" placeholder="Grade" readonly>
                    </div>
                  </td>
                  <td class="approvalstatus">
                    {{ $student->studentScore->status ?? 'N/A' }}
                </td>
                <td class="statu">
                  {{ $student->studentScore ? ($student->studentScore->is_failed ? 'Failed' : 'Passed') : '' }}
              </td>
                </tr>
                @empty
                  
                @endforelse
                
                
                
              </tbody>
            </table>
            <div class="mt-5">
                <button class="btn w-100 text-white" style="background: #AE152D;">Submit</button>
              </div>
            </form>
            <!-- End Table with stripped rows -->

          </div>
        </div>

      </div>
    </div>
  </section>
</div>
@endif
@endsection
@section('javascript')
<script>
  document.addEventListener('DOMContentLoaded', function() {
      const rows = document.querySelectorAll('tbody tr');

      rows.forEach(row => {
          const assessmentInput = row.querySelector('.assessment-score');
          const examInput = row.querySelector('.exam-score');
          const totalInput = row.querySelector('.total-score');
          const gradeInput = row.querySelector('.grade');
          const statusCell = row.querySelector('.status');

          function validateInput(input, max) {
              input.addEventListener('input', function(e) {
                  let value = parseFloat(e.target.value);
                  if (isNaN(value) || value < 0) {
                      e.target.value = '';
                  } else if (value > max) {
                      e.target.value = '';
                  }
              });
          }

          validateInput(assessmentInput, 40);
          validateInput(examInput, 60);

          function calculateTotal() {
              const assessment = parseFloat(assessmentInput.value) || 0;
              const exam = parseFloat(examInput.value) || 0;
              const total = assessment + exam;

              totalInput.value = total.toFixed(2);

              fetch(`/teacher/courses/get-grade/${total}`)
                  .then(response => response.json())
                  .then(data => {
                      gradeInput.value = data.grade;
                      statusCell.textContent = data.status;
                      statusCell.classList.toggle('text-danger', data.status === 'Failed');
                  });
          }

          assessmentInput.addEventListener('change', calculateTotal);
          examInput.addEventListener('change', calculateTotal);
      });
  });
</script>
@endsection