@extends('teacher.layouts.teacher')

@section('title', 'Teacher Departments')
@section('css')

@endsection



@section('teacher')
{{-- <div class="pagetitle">
    <h1>Departments</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.html">Home</a></li>
        <li class="breadcrumb-item">Tables</li>
        <li class="breadcrumb-item active">Data</li>
      </ol>
    </nav>
  </div><!-- End Page Title --> --}}

  <section class="section">
    <div class="row">
      <div class="col-lg-12">

        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Departments</h5>
            <!-- Table with stripped rows -->
            <table class="table datatable">
              <thead>
                <tr>
                  <th>
                    <b>N</b>ame
                  </th>
                  <th>Department code</th>
                 
                </tr>
              </thead>
              <tbody>
             <tr>
              @forelse ($departmentassigned as $departmentassigned)
              <td>{{$departmentassigned->department->name}}</td>
              <td>{{$departmentassigned->department->code}}</td>
            </tr>
              @empty
                
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