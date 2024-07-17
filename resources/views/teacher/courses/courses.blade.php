@extends('teacher.layouts.teacher')

@section('title', 'Teacher Departments')
@section('css')

@endsection



@section('teacher')
{{-- <div class="pagetitle">
    <h1>Courses</h1>
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
            <h5 class="card-title">Courses assigned</h5>
            <!-- Table with stripped rows -->
            <table class="table datatable">
              <thead>
                <tr>
                  <th>
                    <b>N</b>ame
                  </th>
                  <th>Ext.</th>
                  <th>City</th>
                  <th data-type="date" data-format="YYYY/DD/MM">Start Date</th>
                  <th>Completion</th>
                </tr>
              </thead>
              <tbody>
             <tr>
                  <td>Cathleen Kramer</td>
                  <td>3380</td>
                  <td>Crowsnest Pass</td>
                  <td>2012/27/07</td>
                  <td>53%</td>
                </tr>
                <tr>
                  <td>Zelenia Roman</td>
                  <td>7516</td>
                  <td>Redwater</td>
                  <td>2012/03/03</td>
                  <td>31%</td>
                </tr>
              </tbody>
            </table>
            <!-- End Table with stripped rows -->

          </div>
        </div>

      </div>
    </div>
  </section>

@endsection