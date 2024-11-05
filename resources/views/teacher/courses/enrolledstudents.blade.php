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
                         
                         required>
              </td>
                   
                  
                  <td>
                    <div class="form-group">
                      <input type="number"
                                                class="form-control exam-score" min="0" max="60"
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

            {{-- <form action="{{route('teacher.upload.result',['courseid'=>$courseId])}}" method="POST">
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
                    <td>{{$student->student->matric_number}}</td>
                    <td>{{$student->student->user->first_name}} {{$student->student->user->last_name}} {{$student->student->user->other_name}}</td>
                    <td>{{$student->course->title}}</td>
                    <td>{{$student->course->code}}</td>
                    <td>
                      <input type="number" 
                             name="scores[{{ $student->id }}][assessment]" 
                             value="{{ old('scores.'.$student->id.'.assessment', $student->studentScore->assessment_score ?? '') }}"
                             class="form-control assessment-score" 
                             min="0" 
                             max="40"
                             required>
                    </td>
                    <td>
                      <input type="number" 
                             class="form-control exam-score" 
                             min="0" 
                             max="60"
                             name="scores[{{ $student->id }}][exam]"
                             value="{{ old('scores.'.$student->id.'.exam', $student->studentScore->exam_score ?? '') }}"
                             required>
                    </td>
                    <td>
                      <input type="number"
                             class="form-control total-score"
                             name="scores[{{ $student->id }}][total]"
                             value="{{ old('scores.'.$student->id.'.total', $student->studentScore->total_score ?? '') }}"
                             readonly>
                    </td>
                    <td>
                      <input type="text" 
                             class="form-control grade" 
                             name="scores[{{ $student->id }}][grade]"
                             value="{{ old('scores.'.$student->id.'.grade', $student->studentScore->grade ?? '') }}" 
                             placeholder="Grade" 
                             readonly>
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
            </form> --}}
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
          const statusCell = row.querySelector('.statu'); // Changed to match your HTML class
          
          // Input validation function with debounce
          function validateInput(input, max) {
              let timeout;
              
              input.addEventListener('input', function(e) {
                  clearTimeout(timeout);
                  
                  timeout = setTimeout(() => {
                      let value = parseFloat(e.target.value);
                      
                      if (isNaN(value) || value < 0) {
                          e.target.value = '';
                      } else if (value > max) {
                          e.target.value = max;
                      } else {
                          // Round to 1 decimal place if needed
                          e.target.value = Math.round(value * 10) / 10;
                      }
                      
                      calculateTotal();
                  }, 300);
              });
          }
          
          // Apply validation to both inputs
          validateInput(assessmentInput, 40);
          validateInput(examInput, 60);
          
          // Calculate total and fetch grade
          async function calculateTotal() {
              try {
                  const assessment = parseFloat(assessmentInput.value) || 0;
                  const exam = parseFloat(examInput.value) || 0;
                  const total = Math.round((assessment + exam) * 10) / 10; // Round to 1 decimal
                  
                  // Update total input
                  totalInput.value = total;
                  
                  // Only fetch grade if we have valid scores
                  if (assessment > 0 || exam > 0) {
                      const response = await fetch(`/teacher/courses/get-grade/${total}`);
                      if (!response.ok) {
                          throw new Error('Failed to fetch grade');
                      }
                      
                      const data = await response.json();
                      
                      // Update grade and status
                      gradeInput.value = data.grade;
                      
                      // Update status based on grade
                      const isFailed = data.grade === 'F';
                      statusCell.textContent = isFailed ? 'Failed' : 'Passed';
                      statusCell.className = 'statu ' + (isFailed ? 'text-danger' : 'text-success');
                  }
              } catch (error) {
                  console.error('Error calculating grade:', error);
                  // Optionally show an error message to the user
              }
          }
          
          // Event listeners for score changes
          const handleScoreChange = (e) => {
              const input = e.target;
              const value = parseFloat(input.value);
              
              // Ensure value is within bounds
              if (!isNaN(value)) {
                  if (input === assessmentInput && value > 40) {
                      input.value = 40;
                  } else if (input === examInput && value > 60) {
                      input.value = 60;
                  }
              }
              
              calculateTotal();
          };
          
          assessmentInput.addEventListener('change', handleScoreChange);
          assessmentInput.addEventListener('blur', handleScoreChange);
          examInput.addEventListener('change', handleScoreChange);
          examInput.addEventListener('blur', handleScoreChange);
          
          // Initial calculation if values exist
          if (assessmentInput.value || examInput.value) {
              calculateTotal();
          }
      });
      
      // Add form submission handler to validate all inputs
      const form = document.querySelector('form');
      if (form) {
          form.addEventListener('submit', function(e) {
              const allInputs = form.querySelectorAll('.assessment-score, .exam-score');
              let isValid = true;
              
              allInputs.forEach(input => {
                  const value = parseFloat(input.value);
                  const max = input.classList.contains('assessment-score') ? 40 : 60;
                  
                  if (isNaN(value) || value < 0 || value > max) {
                      isValid = false;
                      input.classList.add('is-invalid');
                  } else {
                      input.classList.remove('is-invalid');
                  }
              });
              
              if (!isValid) {
                  e.preventDefault();
                  alert('Please correct the highlighted scores before submitting.');
              }
          });
      }
  });
  </script>
{{-- <script>
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
              // const assessment = parseFloat(assessmentInput.value) || 0;
              // const exam = parseFloat(examInput.value) || 0;
              // const total = assessment + exam;

              // totalInput.value = total.toFixed(2);

              const assessment = parseFloat(assessmentInput.value) || 0;
              const exam = parseFloat(examInput.value) || 0;
              const total = Math.round(assessment + exam);

              totalInput.value = total;

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
</script> --}}
@endsection