@extends('admin.layouts.admin')

@section('admin')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title">
                            Manual Payment Verification
                            @if ($payment && $payment->is_installment)
                                <span class="badge bg-info ms-2">Installment Payment</span>
                            @endif
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="mb-3 border-bottom pb-2">Invoice Details</h4>
                                <table class="table table-bordered">
                                    <tr>
                                        <th class="bg-light">Invoice Number</th>
                                        <td>{{ $invoice->invoice_number }}</td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Student</th>
                                        <td>{{ $invoice->student->user->full_name }}
                                            ({{ $invoice->student->matric_number }})</td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Department</th>
                                        <td>{{ $invoice->student->department->name }}</td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Payment Type</th>
                                        <td>{{ $invoice->paymentType->name }}</td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Total Amount</th>
                                        <td>{{ number_format($invoice->amount, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Session/Semester</th>
                                        <td>{{ $invoice->academicSession->name }} / {{ $invoice->semester->name }}</td>
                                    </tr>
                                </table>
                            </div>

                            <div class="col-md-6">
                                <h4 class="mb-3 border-bottom pb-2">Payment Status</h4>
                                @if ($payment && $payment->is_installment)
                                    <div class="alert alert-info">
                                        <p><strong>Installment Payment</strong></p>
                                        <p>Total Amount: ₦{{ number_format($payment->amount, 2) }}</p>
                                        <p>Amount Paid So Far: ₦{{ number_format($totalPaid, 2) }}</p>
                                        <p>Remaining Amount: ₦{{ number_format($remainingAmount, 2) }}</p>
                                        <p>Status: {{ ucfirst($currentInstallmentStatus) }}</p>
                                        @if ($nextInstallmentDue)
                                            <p>Next Installment Due: {{ $nextInstallmentDue->format('d M, Y') }}</p>
                                        @endif
                                    </div>

                                    <h5 class="mt-4">Installment Details</h5>
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Amount</th>
                                                    <th>Due Date</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            {{-- @dd($installments) --}}
                                            <tbody>
                                                @foreach ($installments as $installment)
                                                    <tr
                                                        class="{{ $installment->status == 'paid' ? 'table-success' : ($installment->status == 'overdue' ? 'table-danger' : 'table-warning') }}">
                                                        <td>{{ $installment->installment_number }}</td>
                                                        <td>₦{{ number_format($installment->amount, 2) }}</td>
                                                        <td>{{ $installment->due_date->format('d M, Y') }}</td>
                                                        <td>
                                                            <span
                                                                class="badge bg-{{ $installment->status == 'paid' ? 'success' : ($installment->status == 'overdue' ? 'danger' : 'warning') }}">
                                                                {{ ucfirst($installment->status) }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            @if ($installment->status == 'pending' || $installment->status == 'overdue')
                                                                <button type="button"
                                                                    class="btn btn-sm btn-primary verify-button"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#verifyInstallmentModal"
                                                                    data-installment-id="{{ $installment->id }}"
                                                                    data-installment-number="{{ $installment->installment_number }}"
                                                                    data-amount="{{ $installment->amount }}">
                                                                    Verify Payment
                                                                </button>
                                                            @elseif($installment->status == 'paid')
                                                                <span class="text-success">Paid on
                                                                    {{ $installment->paid_at->format('d M, Y') }}</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="alert alert-primary">
                                        <p><strong>Full Payment</strong></p>
                                        <p>Amount: ₦{{ number_format($invoice->amount, 2) }}</p>
                                        <p>Status: {{ ucfirst($invoice->status) }}</p>
                                    </div>

                                    @if (!$payment || $payment->status == 'pending')
                                        <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal"
                                            data-bs-target="#verifyFullPaymentModal">
                                            Verify Full Payment
                                        </button>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for installment payment verification (Bootstrap 5) -->
    <div class="modal fade" id="verifyInstallmentModal" tabindex="-1" aria-labelledby="verifyModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="verifyModalLabel">Verify Installment Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.payments.process-manual') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="payment_id" value="{{ $payment ? $payment->id : '' }}" >
                        <input type="hidden" name="installment_id" id="installment_id">

                        <div class="mb-3">
                            <label class="form-label">Installment Number</label>
                            <input type="text" class="form-control" id="installment_number" readonly>
                            <div class="form-text">
                                This is installment <span id="installment_number_display"></span>
                                out of {{ $installments->count() }}
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Amount (₦)</label>
                            <input type="number" class="form-control" name="amount" id="amount" step="0.01"
                                required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Transaction Reference</label>
                            <input type="text" class="form-control" name="transaction_reference" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Bank Name (Optional)</label>
                            <input type="text" class="form-control" name="bank_name">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Payment Proof</label>
                            <input type="file" class="form-control" name="proof_file" required>
                            <div class="form-text">Upload proof of payment (Image or PDF, max 2MB)</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Additional Notes</label>
                            <textarea class="form-control" name="additional_notes" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Verify Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal for full payment verification (Bootstrap 5) -->
    <div class="modal fade" id="verifyFullPaymentModal" tabindex="-1" aria-labelledby="verifyFullModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="verifyFullModalLabel">Verify Full Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.payments.processFullPayment-manual') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">

                        <div class="mb-3">
                            <label class="form-label">Amount (₦)</label>
                            <input type="number" class="form-control" name="amount" value="{{ $invoice->amount }}"
                                step="0.01" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Transaction Reference</label>
                            <input type="text" class="form-control" name="transaction_reference" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Bank Name (Optional)</label>
                            <input type="text" class="form-control" name="bank_name">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Payment Proof</label>
                            <input type="file" class="form-control" name="proof_file" required>
                            <div class="form-text">Upload proof of payment (Image or PDF, max 2MB)</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Additional Notes</label>
                            <textarea class="form-control" name="additional_notes" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Verify Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle installment verification
        const verifyButtons = document.querySelectorAll('.verify-button');
        verifyButtons.forEach(button => {
            button.addEventListener('click', function() {
                const installmentId = this.getAttribute('data-installment-id');
                const installmentNumber = this.getAttribute('data-installment-number');
                const amount = this.getAttribute('data-amount');

                document.getElementById('installment_id').value = installmentId;
                document.getElementById('installment_number').value = installmentNumber;
                document.getElementById('installment_number_display').textContent =
                    installmentNumber;
                document.getElementById('amount').value = amount;

                document.getElementById('verifyModalLabel').textContent =
                    'Verify Installment Payment #' + installmentNumber;
            });
        });
    });
</script>
