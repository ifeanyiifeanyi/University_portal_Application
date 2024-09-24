@extends('parent.layouts.parent')

@section('title', 'Childrens Profile')
@section('parent')
<style>
    .student-profile-card {
        border: none;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
    }
    
    .student-profile-card .card-header {
        border-radius: 15px 15px 0 0;
    }
    
    .profile-picture {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border: 5px solid #fff;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    
    .profile-info {
        padding: 20px;
    }
    
    .profile-info h5 {
        color: #007bff;
        border-bottom: 2px solid #007bff;
        padding-bottom: 10px;
        margin-top: 30px;
        margin-bottom: 20px;
    }
    
    .profile-info p {
        margin-bottom: 10px;
        display: flex;
        justify-content: space-between;
        border-bottom: 1px solid #f0f0f0;
        padding-bottom: 5px;
    }
    
    .profile-info strong {
        font-weight: 600;
        color: #333;
    }
    
    @media (max-width: 768px) {
        .profile-info p {
            flex-direction: column;
        }
    }
    </style>
<div class="container">
    <div class="row mt-4">
        <div class="col-xl-6">
            <div class="card student-profile-card">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">Student Profile</h3>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <img src="{{$getuser->profileImage()}}" alt="Student Picture" class="rounded-circle profile-picture">
                        <h4 class="mt-3">{{ $getuser->first_name }} {{ $getuser->last_name }} {{ $getuser->other_name }}</h4>
                        <p class="text-muted">Matric Number: {{ $student->matric_number }}</p>
                    </div>
                    
                    <div class="profile-info">
                        <h5>Personal Information</h5>
                        <p><strong>JAMB Reg. Number:</strong> {{ $student->jamb_registration_number }}</p>
                        <p><strong>Date of Birth:</strong> {{ $student->date_of_birth }}</p>
                        <p><strong>Gender:</strong> {{ $student->gender }}</p>
                        <p><strong>Phone:</strong> {{ $getuser->phone }}</p>
                        <p><strong>Email:</strong> {{ $getuser->email }}</p>
                        <p><strong>Marital Status:</strong> {{ $student->marital_status }}</p>
                        <p><strong>Religion:</strong> {{ $student->religion }}</p>
                        <p><strong>Blood Group:</strong> {{ $student->blood_group }}</p>
                        <p><strong>Genotype:</strong> {{ $student->genotype }}</p>

                        <h5>Address Information</h5>
                        <p><strong>Nationality:</strong> {{ $student->nationality }}</p>
                        <p><strong>State of Origin:</strong> {{ $student->state_of_origin }}</p>
                        <p><strong>LGA/Province:</strong> {{ $student->lga_of_origin }}</p>
                        <p><strong>Hometown:</strong> {{ $student->hometown }}</p>
                        <p><strong>Residential Address:</strong> {{ $student->residential_address }}</p>
                        <p><strong>Permanent Address:</strong> {{ $student->permanent_address }}</p>

                        <h5>Next of Kin Information</h5>
                        <p><strong>Name:</strong> {{ $student->next_of_kin_name }}</p>
                        <p><strong>Relationship:</strong> {{ $student->next_of_kin_relationship }}</p>
                        <p><strong>Phone:</strong> {{ $student->next_of_kin_phone }}</p>
                        <p><strong>Address:</strong> {{ $student->next_of_kin_address }}</p>

                        <h5>Academic Information</h5>
                        <p><strong>Year of Admission:</strong> {{ $student->year_of_admission }}</p>
                        <p><strong>Mode of Entry:</strong> {{ $student->mode_of_entry }}</p>
                        <p><strong>Current Level:</strong> {{ $student->current_level }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6">

            {{-- school fees --}}

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Payments History</h5>
                </div><!-- end card header -->

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    
                                    <th scope="col">Payment name</th>
                                    <th scope="col">Amount</th>
                                    <th scope="col">Session</th>
                                    <th scope="col">Semester</th>
                                    <th scope="col">Level</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Reference number</th>
                                    <th scope="col">Payment method</th>
                                    
                                    <th scope="col"></th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                               {{-- payment type --}}
                               {{-- session --}}
                               {{-- semester --}}
                               {{-- level --}}
                               {{-- status --}}
                               @forelse ($payments as $payment)
                               <tr>
                                <td>{{$payment->paymentType->name}}</td>
                                <td>{{$payment->amount}}</td>
                                <td>{{$payment->academicSession->name}}</td>
                                <td>{{$payment->semester->name}}</td>
                                <td>{{$payment->level}}</td>
                                <td>{{$payment->status}}</td>
                                <td>{{$payment->transaction_reference}}</td>
                                <td>{{$payment->paymentMethod->name}}</td>
                                <td><a href="{{route('student.fees.payments.showReceipt',['receipt'=>$payment->receipt->id])}}" class="btn w-100 text-white" style="background: #AE152D;">View receipt</a></td>
                               </tr>
                              
                               @empty
                                   
                               @endforelse
                         
                               
                               
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">All Results</h5>
                </div><!-- end card header -->

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    
                                    <th scope="col">Session</th>
                                    <th scope="col">Level</th>
                                    <th scope="col">Semester</th>
                                    <th scope="col">CGPA</th>
                                    <th scope="col"></th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($availableResults as $result)
                    <tr>
                        <td>{{ $result['session'] }}</td>
                        <td>100</td>
                        <td>{{ $result['semester'] }}</td>
                        <td>2355</td>
                        <td>
                            <a href="{{ route('parent.view.child.result',[
                                'session' => $result['sessionid'],
                                'semester' => $result['semesterid'],
                                'teacherid'=> $result['teacher'],
                                'studentid' => $result['studentid']
                            ]) }}" class="btn w-100 text-white" style="background: #AE152D;">View</a>
                        </td>
                    </tr>
                    @endforeach
                                
                         
                               
                               
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Courses enrolled and teachers assigned</h5>
                </div><!-- end card header -->

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    
                                    <th scope="col">Course name</th>
                                    <th scope="col">Course code</th>
                                    <th scope="col">Teacher assigned</th>
                                    <th scope="col">Teacher email</th>
                                    
                                    
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($teachersassigned as $assignment)
                                <tr>
                                    <!-- Check if course exists -->
                                    @if($assignment->course)
                                        <td>{{ $assignment->course->title }}</td>
                                        <td>{{ $assignment->course->code }}</td>
                                    @else
                                        <td colspan="2">Course details not available</td>
                                    @endif
                                    
                                    <!-- Check if teacherAssigned and teacher exist -->
                                    @if($assignment->teacherAssigned && $assignment->teacherAssigned->teacher)
                                        <td>{{ $assignment->teacherAssigned->teacher->user->first_name }}</td>
                                        <td>
                                            <a href="mailto:{{ $assignment->teacherAssigned->teacher->user->email }}">
                                                {{ $assignment->teacherAssigned->teacher->user->email }}
                                            </a>
                                        </td>
                                    @else
                                        <td colspan="2">Teacher details not available</td>
                                    @endif
                                </tr>
                            @endforeach
                            
                                
                         
                               
                               
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>
@endsection