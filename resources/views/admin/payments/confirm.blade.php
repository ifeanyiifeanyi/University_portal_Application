<!-- resources/views/payments/confirm-ticket.blade.php -->
@extends('admin.layouts.admin')
@section('title', 'Confirm Payment Details')
@section('admin')
<div class="container">
    @include('admin.alert')
    <h2>Confirm Payment Details</h2>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Payment Details</h5>
            <p><strong>Student Name:</strong> {{ $student->user->full_name }}</p>
            <p><strong>Matric Number:</strong> {{ $student->matric_number }}</p>
            <p><strong>Department:</strong> {{ $student->department->name }}</p>
            <p><strong>Level:</strong> {{ $student->current_level }}</p>
            <p><strong>Payment Option:</strong> {{ $payment_method->config['gateway'] }}</p>
            <p><strong>Payment Type:</strong> {{ $payment_type->name }}</p>
            <p><strong>Amount:</strong> ₦{{ number_format($payment_type->amount, 2) }}</p>
            <p><strong>Academic Session:</strong> {{ $academic_session->name }}</p>
            <p><strong>Semester:</strong> {{ $semester->name }}</p>
        </div>
    </div>
    <div class="mt-3">
        <a href="{{ route('admin.payment.pay') }}" class="btn btn-secondary">Make Corrections</a>
        <form action="{{ route('admin.payments.processPayment') }}" method="POST" class="d-inline">
            @csrf
            <input type="hidden" name="payment_type_id" value="{{ $payment_type->id }}">
            <input type="hidden" name="student_id" value="{{ $student->id }}">
            <input type="hidden" name="academic_session_id" value="{{ $academic_session->id }}">
            <input type="hidden" name="semester_id" value="{{ $semester->id }}">
            <input type="hidden" name="amount" value="{{ $payment_type->amount }}">
            <input type="hidden" name="payment_method_id" value="{{ $payment_method->id }}">
            <button type="submit" class="btn btn-primary">Confirm and Pay</button>
        </form>
        <a href="{{ route('admin.payments.generateTicket', ['payment_type_id' => $payment_type->id, 'student_id' => $student->id, 'academic_session_id' => $academic_session->id, 'semester_id' => $semester->id]) }}" class="btn btn-info">Generate Printable Ticket</a>
    </div>
</div>
@endsection
