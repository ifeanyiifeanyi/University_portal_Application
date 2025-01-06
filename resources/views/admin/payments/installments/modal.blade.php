<!-- Details Modal -->
<div class="modal fade" id="detailsModal{{ $installment->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Installment Payment Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Student Information -->
                    <div class="col-md-6 mb-3">
                        <h6 class="border-bottom pb-2">Student Information</h6>
                        <p><strong>Name:</strong> {{ $installment->payment->student->user->full_name ?? 'N/A' }}</p>
                        <p><strong>Matric Number:</strong> {{ $installment->payment->student->matric_number ?? 'N/A' }}</p>
                        <p><strong>Department:</strong> {{ $installment->payment->student->department->name ?? 'N/A' }}</p>
                        <p><strong>Level:</strong> {{ $installment->payment->student->current_level ?? 'N/A' }}</p>
                    </div>

                    <!-- Payment Information -->
                    <div class="col-md-6 mb-3">
                        <h6 class="border-bottom pb-2">Payment Information</h6>
                        <p><strong>Total Amount:</strong> ₦{{ number_format($installment->payment->amount, 2) }}</p>
                        <p><strong>Remaining Amount:</strong> ₦{{ number_format($installment->payment->remaining_amount ?? 0, 2) }}</p>
                        <p><strong>Payment Method:</strong> {{ $installment->payment->paymentMethod->name ?? 'N/A' }}</p>
                        <p><strong>Payment Type:</strong> {{ $installment->payment->paymentType->name ?? 'N/A' }}</p>
                    </div>

                    <!-- Installment Details -->
                    <div class="col-md-6">
                        <h6 class="border-bottom pb-2">Installment Details</h6>
                        <p><strong>Status:</strong>
                            <span class="badge bg-{{ $statusClass }}">{{ ucfirst($installment->status) }}</span>
                        </p>
                        <p><strong>Due Date:</strong> {{ $installment->due_date->format('d M, Y') }}</p>
                        @if ($installment->paid_at)
                            <p><strong>Paid Date:</strong> {{ $installment->paid_at->format('d M, Y') }}</p>
                        @endif
                        <p><strong>Amount Due:</strong> ₦{{ number_format($installment->amount, 2) }}</p>
                        @if($installment->paid_amount)
                            <p><strong>Amount Paid:</strong> ₦{{ number_format($installment->paid_amount, 2) }}</p>
                        @endif
                    </div>

                    <!-- Additional Information -->
                    <div class="col-md-6">
                        <h6 class="border-bottom pb-2">Additional Information</h6>
                        <p><strong>Academic Session:</strong> {{ $installment->payment->academic_session->name ?? 'N/A' }}</p>
                        <p><strong>Semester:</strong> {{ $installment->payment->semester->name ?? 'N/A' }}</p>
                        <p><strong>Created At:</strong> {{ $installment->created_at->format('d M, Y H:i A') }}</p>
                        <p><strong>Last Updated:</strong> {{ $installment->updated_at->format('d M, Y H:i A') }}</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                @if($installment->payment->receipt)
                    <a href="{{ route('admin.payments.showReceipt', $installment->payment->receipt->id) }}"
                       class="btn btn-primary">
                        <i class="fas fa-file-alt me-1"></i> View Receipt
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
