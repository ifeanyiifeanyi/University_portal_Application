@extends('student.layouts.student')

@section('title', 'Student Recurring Payments')
@section('student')
<div class="container-xxl mt-3">
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Recurring Payments</h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">Components</a></li>
                <li class="breadcrumb-item active">Recurring Payments</li>
            </ol>
        </div>
    </div>

    {{-- @php
    $paymentWarning = $subscription->getPaymentWarningStatus();
@endphp

@if($paymentWarning)
    <div class="alert alert-{{ $paymentWarning['type'] }} mt-3">
        <i class="fa fa-exclamation-triangle mr-2"></i>
        {{ $paymentWarning['message'] }}
    </div>
@endif --}}

    <div class="row">
        @include('messages')
        <div class="col-12">

            
            

            <table class="table mb-0">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Plan Name</th>
                        <th scope="col">Description</th>
                        <th scope="col">Amount</th>
                        <th scope="col">Status</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($paymentPlans) && count($paymentPlans) > 0)
                        @foreach($paymentPlans as $index => $plan)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $plan->name }}</td>
                            <td>{{ $plan->description }}</td>
                            <td>{{ number_format($plan->amount, 2) }}</td>
                            <td>
                                @if($plan->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{route('student.recurring.subscribe',['plan'=>$plan->id])}}" class="btn btn-primary btn-sm">
                                    Subscribe
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="text-center">No payment plans available</td>
                        </tr>
                    @endif
                </tbody>
            </table>


            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">All Recurring Payments</h5>
                </div><!-- end card header -->
                
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Plan Name</th>
                                    <th scope="col">Monthly Amount</th>
                                    <th scope="col">Total Amount</th>
                                    <th scope="col">Paid Amount</th>
                                    <th>Start Date</th>
                                    <th>Ending Date</th>
                                
                                  
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($subscriptions) && count($subscriptions) > 0)
                                    @foreach($subscriptions as $index => $subscription)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $subscription->plan->name ?? 'N/A' }}</td>
                                        <td>{{ number_format($subscription->amount_per_month, 2) }}</td>
                                        <td>{{ number_format($subscription->total_amount, 2) }}</td>
                                        <td>{{ number_format($subscription->amount_paid, 2) }}</td>
                                
                                        <td>
                                            @if ($subscription->start_date)
                                            {{ $subscription->start_date->format('d.m.Y') }}
                                            @endif
                                            
                                        </td>
                                        <td>
                                            @if ($subscription->end_date)
                                            {{ $subscription->end_date->format('d.m.Y') }}
                                            @endif
                                            
                                        </td>
                                        
                                        <td>
                                            @if ($subscription->is_active == 1)
    <a href="{{route('student.recurring.receipt',['id'=>$subscription->id])}}" class="btn btn-success">View Receipt</a>
@else
    <a href="{{route('student.recurring.continue-payment',['id'=>$subscription->id])}}" class="btn btn-warning">Continue payment</a>
@endif
                                            
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="8" class="text-center">No subscription plans found</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection