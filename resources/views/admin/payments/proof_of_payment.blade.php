@extends('admin.layouts.admin')

@section('title', 'Proof of Payment Details')

@section('css')
    <style>
        .payment-status-verified {
            color: #059669;
        }

        .payment-status-pending {
            color: #D97706;
        }

        .payment-status-rejected {
            color: #DC2626;
        }

        .status-badge {
            padding: 0.375rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .badge-verified {
            background: #D1FAE5;
        }

        .badge-pending {
            background: #FEF3C7;
        }

        .badge-rejected {
            background: #FEE2E2;
        }

        .proof-image {
            max-width: 400px;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .proof-image:hover {
            transform: scale(1.05);
        }
    </style>
@endsection

@section('admin')
    <div class="container-fluid">
        @include('admin.alert')



        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="card-title">Payment #{{ $payment->id }}</h4>
                            <span
                                class="status-badge {{ $payment->status === 'paid' ? 'badge-verified' : 'badge-pending' }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </div>

                        <!-- Invoice Details -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5 class="font-size-14 mb-3">Invoice Information</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="pl-0 text-muted">Invoice Number:</td>
                                        <td><strong>{{ $payment->invoice->invoice_number }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td class="pl-0 text-muted">Amount:</td>
                                        <td><strong>₦{{ number_format($payment->base_amount, 2) }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td class="pl-0 text-muted">Payment Type:</td>
                                        <td>{{ $payment->paymentType->name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="pl-0 text-muted">Payment Method:</td>
                                        <td>{{ $payment->paymentMethod->name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="pl-0 text-muted">Student:</td>
                                        <td>{{ $payment->student->user->full_name }}</td>
                                    </tr>
                                </table>
                            </div>

                            <div class="col-md-6">
                                <h5 class="font-size-14 mb-3">Payment Details</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="pl-0 text-muted">Transaction Reference:</td>
                                        <td><strong>{{ $payment->transaction_reference }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td class="pl-0 text-muted">Bank Name:</td>
                                        <td>{{ $payment->invoice->proveOfPayment->first()->bank_name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="pl-0 text-muted">Payment Date:</td>
                                        <td>{{ $payment->payment_date->format('M d, Y h:i A') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="pl-0 text-muted">Status:</td>
                                        <td>
                                            <span
                                                class="status-badge {{ $payment->status === 'paid' ? 'badge-verified' : 'badge-pending' }}">
                                                {{ ucfirst($payment->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- Payment Proof -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="font-size-14 mb-3">Payment Proof</h5>
                                @if ($payment->invoice->proveOfPayment->first()->proof_file)
                                    @php
                                        $extension = pathinfo(
                                            $payment->invoice->proveOfPayment->first()->proof_file,
                                            PATHINFO_EXTENSION,
                                        );
                                    @endphp

                                    @if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png']))
                                        <div class="text-center">
                                            <img src="{{ Storage::url($payment->invoice->proveOfPayment->first()->proof_file) }}"
                                                alt="Payment Proof" class="img-fluid rounded proof-image"
                                                data-toggle="modal" data-target="#imageModal" width="300" height="200">
                                        </div>
                                    @else
                                        <a href="{{ Storage::url($payment->invoice->proveOfPayment->first()->proof_file) }}"
                                            class="btn btn-primary" target="_blank">
                                            <i class="fas fa-file-download mr-1"></i> Download Proof Document
                                        </a>
                                    @endif
                                @else
                                    <p class="text-muted">No proof file uploaded</p>
                                @endif
                            </div>
                        </div>

                        <!-- Additional Notes -->
                        @if ($payment->additional_notes)
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="font-size-14 mb-3">Additional Notes</h5>
                                    <div class="p-3 bg-light rounded">
                                        {{ $payment->additional_notes }}
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Admin Actions -->
                        {{-- @if ($payment->status !== 'paid')
                            <div class="row">
                                <div class="col-12">
                                    <hr>
                                    <div class="text-right">
                                        <button type="button" class="btn btn-danger mr-2" data-toggle="modal"
                                            data-target="#rejectModal">
                                            <i class="fas fa-times-circle mr-1"></i> Reject
                                        </button>
                                        <button type="button" class="btn btn-success" data-toggle="modal"
                                            data-target="#verifyModal">
                                            <i class="fas fa-check-circle mr-1"></i> Verify
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif --}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Preview Modal -->

    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">Payment Proof</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="{{ Storage::url($payment->invoice->proveOfPayment->first()->proof_file) }}"
                        alt="Payment Proof" class="img-fluid" width="400" height="300">
                </div>
            </div>
        </div>
    </div>

    <!-- Verification Modal -->
    {{-- @if ($payment->status !== 'paid')
        <div class="modal fade" id="verifyModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('admin.payments.verify', $payment->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">Verify Payment</h5>
                            <button type="button" class="close" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Are you sure you want to verify this payment?</label>
                                <textarea class="form-control mt-2" name="admin_comment" rows="3"
                                    placeholder="Add any verification comments (optional)"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check-circle mr-1"></i> Verify Payment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Rejection Modal -->
        <div class="modal fade" id="rejectModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('admin.payments.reject', $payment->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">Reject Payment</h5>
                            <button type="button" class="close" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Rejection Reason <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="admin_comment" rows="3" required
                                    placeholder="Please provide a reason for rejection"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-times-circle mr-1"></i> Reject Payment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif --}}

    <!-- Verification Modal -->
    @if ($payment->status !== 'paid')
        <div class="modal fade" id="verifyModal" tabindex="-1" aria-labelledby="verifyModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('admin.payments.verify', $payment->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title" id="verifyModalLabel">Verify Payment</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Are you sure you want to verify this payment?</label>
                                <textarea class="form-control mt-2" name="admin_comment" rows="3"
                                    placeholder="Add any verification comments (optional)"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check-circle me-1"></i> Verify Payment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Rejection Modal -->
        <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('admin.payments.reject', $payment->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title" id="rejectModalLabel">Reject Payment</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Rejection Reason <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="admin_comment" rows="3" required
                                    placeholder="Please provide a reason for rejection"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-times-circle me-1"></i> Reject Payment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('javascript')
    <script>
        $(document).ready(function() {
            // Initialize tooltips
            $('[data-toggle="tooltip"]').tooltip();

            // Handle image preview click
            $('.proof-image').click(function() {
                $('#imageModal').modal('show');
            });
        });
    </script>
@endsection
