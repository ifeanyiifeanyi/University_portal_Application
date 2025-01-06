@extends('student.layouts.student')

@section('title', 'Student Dashboard')
@section('student')
<div class="container-xxl mt-3">
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">School fees</h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">Components</a></li>
                <li class="breadcrumb-item active">School fees</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6"></div>
    <div class="ms-auto mb-4 col-md-6">
        <a href="{{route('student.view.fees.pay')}}" class="btn w-100 text-white btn-success">Pay new fees</a>
    </div>
    </div>

    <div class="row">
        @include('messages')
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Fees invoice history</h5>
                </div><!-- end card header -->

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    
                                    <th scope="col">Invoice #</th>
                                    <th scope="col">Amount ₦</th>
                                    <th scope="col">Level</th>
                                    <th scope="col">Payment Type</th>
                                    <th scope="col">Session</th>
                                    <th scope="col">Session</th>
                                    <th scope="col"></th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($invoices as $invoice)
                                <tr>
                                    <td>{{$invoice->invoice_number}}</td>
                                    <td>{{$invoice->amount}}</td>
                                    <td>{{$invoice->level}}</td>
                                    @if ($invoice->paymentType) {{-- Check if paymentType exists --}}
                                    <td>{{$invoice->paymentType->name}}</td>
                                    @else 
                                    <td>Not available</td>
                                @endif
                                    
                                    <td>{{$invoice->academicSession->name}}</td>
                                    <td>{{$invoice->semester->name}}</td>
                                    <td><a href="{{route('student.view.fees.invoice',['id'=>$invoice->id])}}" class="btn w-100 text-white btn-success">View</a></td>
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

    <div class="row">
        @include('messages')
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Awaiting Fees</h5>
                </div><!-- end card header -->

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    
                                    <th scope="col">Payment Name</th>
                                    <th scope="col">Amount ₦</th>
                                    <th scope="col">Due Date</th>
                                    <th scope="col">Late Fee Amount</th>
                                 
                                    
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($paymentTypes as $paymentType)
                                <tr>
                                    <td>{{$paymentType->paymentType->name}}</td>
                                    <td>₦{{ number_format($paymentType->paymentType->amount, 2) }}</td>
                                    <td>{{$paymentType->paymentType->due_date->format('d.m.Y')}}</td>
                                    <td>{{$paymentType->paymentType->late_fee_amount}}</td>
                                    
                                   
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

<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">Payment Notification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="paymentMessage"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="{{route('student.view.payments')}}" class="btn btn-primary" id="paymentButton">Go to Payment</a>
            </div>
        </div>
    </div>
</div>
@section('javascript')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        checkPaymentStatus();
        function checkPaymentStatus() {
            fetch('{{route('student.fees.checkpaymentstatus')}}')
                .then(response => response.json())
                .then(data => {
                    if (data.shouldShowModal && data.message) {
                        // Set the message and show modal
                        document.getElementById('paymentMessage').textContent = data.message;
                        
                        // Set modal color based on status
                        const modalHeader = document.querySelector('.modal-header');
                        modalHeader.className = 'modal-header';
                        if (data.status === 'error') {
                            modalHeader.classList.add('bg-danger', 'text-white');
                        } else if (data.status === 'warning') {
                            modalHeader.classList.add('bg-warning');
                        }
                        
                        // Initialize and show the modal
                        const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));
                        paymentModal.show();
                        
                        // Store in localStorage
                        localStorage.setItem('paymentNotificationShown', 'true');
                    }
                })
                .catch(error => {
                    console.error('Error checking payment status:', error);
                });
        }
    });
    </script>
@endsection
@endsection