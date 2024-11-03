@extends('student.layouts.student')

@section('title', 'Students Payments')
@section('student')
<div class="container-xxl mt-3">
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Fees Payments</h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">Components</a></li>
                <li class="breadcrumb-item active">Fees</li>
            </ol>
        </div>
    </div>

    <div class="row">
        @include('messages')
        <div class="col-12">
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
        </div>
    </div>
</div>
@endsection