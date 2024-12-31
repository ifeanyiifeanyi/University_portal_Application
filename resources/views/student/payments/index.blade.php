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
                                    
                                    {{-- <th scope="col">Payment name</th>
                                    <th scope="col">Amount</th>
                                    <th scope="col">Session</th>
                                    <th scope="col">Semester</th>
                                    <th scope="col">Level</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Reference number</th>
                                    <th scope="col">Payment method</th>
                                    
                                    <th scope="col"></th> --}}

                                    <th scope="col">Payment Type</th>
                    <th scope="col">Session/Semester</th>
                    <th scope="col">Amount</th>
                    <th scope="col">Paid Amount</th>
                    <th scope="col">Status</th>
                    <th scope="col">Date</th>
                    <th scope="col">Actions</th>
                                    
                                </tr>
                            </thead>
                            {{-- <tbody>
                           
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
                                <td><a href="{{route('student.fees.payments.showReceipt',['receipt'=>$payment->receipt->id])}}" class="btn w-100 text-white btn-success">View receipt</a></td>
                               </tr>
                            
                               @empty
                                   
                               @endforelse
                         
                               
                               
                            </tbody> --}}

                            <tbody class="divide-y divide-gray-200">
                                @foreach($payments as $payment)
                                <tr>
                                    <td class="px-4 py-3">
                                        {{ $payment->paymentType->name }}
                                        @if($payment->is_installment)
                                            <span class="text-xs text-gray-500">(Installment)</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ $payment->academicSession->name }} / {{ $payment->semester->name }}
                                    </td>
                                    <td class="px-4 py-3">₦{{ number_format($payment->amount, 2) }}</td>
                                    <td class="px-4 py-3">₦{{ number_format($payment->base_amount, 2) }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 rounded-full text-xs
                                            @if($payment->status === 'paid') bg-green-100 text-green-800
                                            @elseif($payment->status === 'partial') bg-yellow-100 text-yellow-800
                                            @else bg-red-100 text-red-800
                                            @endif">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">{{ $payment->payment_date->format('M d, Y') }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex space-x-2">
                                            
                                            
                                            @if($payment->is_installment && $payment->status === 'partial')
                                                <a href="{{ route('student.fees.payments.installment', $payment->id) }}" 
                                                   class="btn w-100 text-dark btn-warning">
                                                    Pay ₦{{ number_format($payment->next_transaction_amount, 2) }}
                                                </a>
                                            @endif
                
                                            @if($payment->status === 'paid' || $payment->status === 'partial')
                                                <a href="{{route('student.fees.payments.showReceipt',['receipt'=>$payment->receipt->id])}}" 
                                                   class="btn w-100 text-white btn-success mt-2">
                                                    Receipt
                                                </a>
                                            @endif
                                           
                                        </div>
                                    </td>
                                </tr>

                                
                                @endforeach
                            </tbody>
                        </table>




                        
                    </div>
                </div>
                <div class="flex justify-content-center px-6 py-4 border-t border-gray-200">
                    {{ $payments->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection