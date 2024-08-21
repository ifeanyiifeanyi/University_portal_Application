@extends('teacher.layouts.teacher')

@section('title', 'Enrolled students')
@section('css')

@endsection



@section('teacher')
<div class="container-xxl mt-3">
    <div class="row">
        <div class="col-md-6">
            <form action="{{ route('importassessment.csv') }}" method="POST" enctype="multipart/form-data">
                @csrf
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

        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Students enrolled</h5>
            <!-- Table with stripped rows -->
            <table class="table datatable">
              <thead>
                <tr>
                  
                  <th>Student name</th>
                 
                  <th>Student department</th>
                  
                  <th>Course name</th>
                  <th>Course code</th>
                  <th>Assessment score</th>
                  <th>Exam score</th>
                  <th>Total</th>
                  <th>Grade</th>
                 
                </tr>
              </thead>
              <tbody>
                @forelse ($students as $student)
                <tr>
                  <td>{{$student->student->user->first_name}} {{$student->student->user->last_name}} {{$student->student->user->other_name}}</td>
                  <td>{{$student->department->name}}</td>
                  <td>{{$student->course->title}}</td>
                  <td>{{$student->course->code}}</td>
                  <td>
                    <div class="form-group">
                      <input type="number" class="form-control" name="" id="assessment{{$student->id}}" oninput="calculateTotalAndGrade({{$student->id}})" placeholder="Assessment score">
                    </div>
                  </td>
                  <td>
                    <div class="form-group">
                      <input type="number" class="form-control" name="" id="exam{{$student->id}}" oninput="calculateTotalAndGrade({{$student->id}})" placeholder="Exam score">
                    </div>
                  </td>
                  <td>
                    <div class="form-group">
                        <input type="number" class="form-control" name="" id="total{{$student->id}}" placeholder="Total" readonly>
                    </div>
                  </td>
                  <td>
                    <div class="form-group">
                        <input type="text" class="form-control" name="" id="grade{{$student->id}}" placeholder="Grade" readonly>
                    </div>
                  </td>
                </tr>
                @empty
                  
                @endforelse
                
              
                
              </tbody>
            </table>
            <div class="mt-5">
                <button class="btn w-100 text-white" style="background: #AE152D;">Submit</button>
              </div>
            <!-- End Table with stripped rows -->

          </div>
        </div>

      </div>
    </div>
  </section>
</div>
@endsection