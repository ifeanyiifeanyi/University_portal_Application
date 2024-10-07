@extends('admin.layouts.admin')

@section('title', 'Invoice Manager')
@section('css')

@endsection



@section('admin')
    @include('admin.alert')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-center gap-5">
                            <div>
                                <p>
                                    <a href="" class="btn btn-primary float-left" style="text-align: right">Generate New
                                        Invoice
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped table-bordered" id="example">
                            <thead>
                                <tr>
                                    <th>sn</th>
                                    <th>Invoice ID</th>
                                    <th>Student Name</th>
                                    <th>Department</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($invoices as $invoice)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $invoice->invoice_number }}</td>
                                        <td>{{ $invoice->student->user->full_name }}</td>
                                        <td>{{ $invoice->department->name }}</td>
                                        <td>₦{{ number_format($invoice->amount, 0, 2) }}</td>
                                        <td>{{ $invoice->status }}</td>
                                        <td>
                                            <a href="{{ route('admin.invoice.show', $invoice->id) }}" class="btn btn-sm"
                                                style="background: blueviolet; color:white">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            @if ($invoice->status == 'paid')
                                                <a href="" class="btn btn-sm"
                                                    style="background: rgb(128, 0, 62);color:white"><i
                                                        class="fas fa-edit"></i></a>
                                            @endif



                                            @if ($invoice->status == 'pending')
                                                <a href="" class="btn btn-sm" style="background: rgb(95, 236, 163)">
                                                    <i class="fas fa-credit-card"></i>
                                                </a>
                                            @endif


                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @section('javascript')

    @endsection
