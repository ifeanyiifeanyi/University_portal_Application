
<style>
    .receipt-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
        font-family: 'Arial', sans-serif;
    }
    .receipt-header {
        text-align: center;
        margin-bottom: 30px;
    }
    .receipt-logo {
        max-width: 150px;
        margin-bottom: 15px;
    }
    .receipt-title {
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 5px;
    }
    .receipt-subtitle {
        color: #666;
        margin-bottom: 5px;
    }
    .receipt-number {
        font-weight: bold;
        margin-bottom: 20px;
    }
    .receipt-info {
        display: flex;
        justify-content: space-between;
        margin-bottom: 30px;
    }
    .receipt-info-column {
        flex: 1;
    }
    .info-group {
        margin-bottom: 15px;
    }
    .info-label {
        font-weight: bold;
        color: #555;
        margin-bottom: 5px;
    }
    .info-value {
        font-size: 15px;
    }
    .receipt-details-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 30px;
    }
    .receipt-details-table th,
    .receipt-details-table td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }
    .receipt-details-table th {
        background-color: #f5f5f5;
        font-weight: bold;
    }
    .receipt-total {
        text-align: right;
        margin-bottom: 30px;
    }
    .receipt-total-row {
        margin-bottom: 5px;
    }
    .receipt-total-label {
        display: inline-block;
        width: 150px;
        font-weight: bold;
        text-align: right;
        margin-right: 15px;
    }
    .receipt-total-value {
        display: inline-block;
        min-width: 100px;
        text-align: right;
        font-weight: normal;
    }
    .receipt-total-final {
        font-size: 18px;
        font-weight: bold;
        margin-top: 10px;
    }
    .receipt-footer {
        text-align: center;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #eee;
        color: #777;
    }
    .receipt-actions {
        display: flex;
        justify-content: center;
        margin-top: 30px;
    }
    .receipt-actions button {
        margin: 0 10px;
        padding: 10px 20px;
    }
    @media print {
        .no-print {
            display: none;
        }
        body {
            margin: 0;
            padding: 0;
            background: #fff;
        }
        .receipt-container {
            box-shadow: none;
            margin: 0;
            padding: 15px;
            width: 100%;
        }
    }
</style>
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="card receipt-container">
                <div class="receipt-header">
                    <img src="{{ asset('nursinglogo.webp') }}" alt="Logo" class="receipt-logo">
                    <h1 class="receipt-title">Payment Receipt For Feeding Fee subscription</h1>
                    <p class="receipt-subtitle">{{ config('app.name') }}</p>
                    <p class="receipt-number">Receipt #: REC-{{ $subscription->id }}-{{ date('Ymd') }}</p>
                </div>
                
                <div class="receipt-info">
                    <div class="receipt-info-column">
                        <div class="info-group">
                            <div class="info-label">Student Information</div>
                            <div class="info-value">{{ $subscription->student->user->first_name ?? '' }} {{ $subscription->student->user->last_name ?? '' }}</div>
                            <div class="info-value">{{ $subscription->student->matric_number ?? 'N/A' }}</div>
                            <div class="info-value">{{ $subscription->student->user->email ?? '' }}</div>
                        </div>
                    </div>
                    
                    <div class="receipt-info-column">
                        <div class="info-group">
                            <div class="info-label">Payment Date</div>
                            <div class="info-value">{{ $paymentData ? date('F j, Y g:i A', strtotime($paymentData['date'])) : now()->format('F j, Y g:i A') }}</div>
                        </div>
                        

                        {{-- <div class="info-group">
                            <div class="info-label">Months Between</div>
                            <div class="info-value">
                                <ul>
                                    @foreach($monthsinterval as $month)
                                        <li>{{ $month }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div> --}}
                        
                        <div class="info-group">
                            <div class="info-label">Payment Reference</div>
                            <div class="info-value">{{ $paymentData ? $paymentData['reference'] : 'N/A' }}</div>
                        </div>

                        <div class="info-group">
                            <div class="info-label">Selected Payment Months</div>
                            <div class="info-value">
                                <ul class="list-unstyled">
                                    @forelse($months as $month)
                                        <li>
                                            <span class="badge bg-primary">{{ $month }}</span>
                                        </li>
                                    @empty
                                        <li class="text-muted">No specific months selected</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <table class="receipt-details-table">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th>Plan</th>
                            <th>Duration</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Subscription Payment</td>
                            <td>Feeding Fee</td>
                            <td>{{ $subscription->number_of_payments ?? '1' }} month(s)</td>
                            <td>₦{{ number_format($subscription->amount_paid, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
                
                <div class="receipt-total">
                  
                    <div class="receipt-total-row receipt-total-final">
                        <span class="receipt-total-label">Total Paid:</span>
                        <span class="receipt-total-value">₦{{ number_format($subscription->amount_paid, 2) }}</span>
                    </div>
                </div>
                
                <div class="receipt-footer">
                    <p>Thank you for your payment. This receipt serves as confirmation of your subscription payment.</p>
                    <p>If you have any questions regarding this receipt, please contact our support team.</p>
                </div>
                
                <div class="receipt-actions no-print">
                    <button onclick="window.print()" class="btn btn-primary">
                        <i class="fa fa-print mr-2"></i> Print Receipt
                    </button>
                    <button onclick="downloadPDF()" class="btn btn-secondary">
                        <i class="fa fa-download mr-2"></i> Download PDF
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    function downloadPDF() {
        // Hide the download buttons for the PDF
        const actionButtons = document.querySelector('.receipt-actions');
        actionButtons.style.display = 'none';
        
        // Create the PDF
        const element = document.querySelector('.receipt-container');
        const opt = {
            margin: [10, 10, 10, 10],
            filename: 'receipt-{{ $subscription->id }}-{{ date('Ymd') }}.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2 },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };
        
        // Generate and download the PDF
        html2pdf().set(opt).from(element).save().then(() => {
            // Show the buttons again after the PDF is generated
            actionButtons.style.display = 'flex';
        });
    }
</script>