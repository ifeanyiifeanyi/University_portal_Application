@extends('admin.layouts.admin')

@section('title', 'Feed Fees Manager')

@section('admin')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-gradient-primary text-white py-3">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-money-bill-wave me-2"></i>Student Payments {{ $year }}
                        </h5>
                        <div class="d-flex flex-wrap gap-2 align-items-center">
                            <form method="GET" action="{{ route('admin.recurring_payments.get-recurring-payments') }}" class="d-flex">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-white border-end-0">
                                        <i class="fas fa-calendar-alt text-primary"></i>
                                    </span>
                                    <select name="year" class="form-select form-select-sm border-start-0 ps-0" onchange="this.form.submit()">
                                        @for($y = now()->year; $y >= now()->year - 5; $y--)
                                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </form>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.recurring-payments.export-csv', ['year' => $year]) }}"
                                   class="btn btn-sm btn-light">
                                   <i class="fas fa-file-csv me-1"></i>CSV
                                </a>
                                <a href="{{ route('admin.recurring-payments.export', ['year' => $year]) }}"
                                   class="btn btn-sm btn-light">
                                   <i class="fas fa-file-pdf me-1"></i>PDF
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Yearly Totals Cards -->
                <div class="card-body bg-light py-4 border-bottom">
                    <h6 class="text-uppercase text-muted mb-3 fs-6">
                        <i class="fas fa-chart-bar me-2"></i>Yearly Payment Summary
                    </h6>
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 g-4">
                        @foreach($yearlyTotals as $total)
                            <div class="col">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="card-title fw-bold mb-0">{{ $total['year'] }}</h6>
                                            <span class="badge bg-primary rounded-pill">
                                                {{ number_format($total['year'] == $year ? 100 :
                                                    ($yearlyTotals->where('year', $year)->first()['total'] > 0
                                                        ? ($total['total'] / $yearlyTotals->where('year', $year)->first()['total'] * 100)
                                                        : 0), 1) }}%
                                            </span>
                                        </div>
                                        <hr class="my-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted fs-7">Total Revenue</span>
                                            <h5 class="fw-bold text-secondary mb-0">₦{{ number_format($total['total'], 2) }}</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Table Section -->
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="payment-table" class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-3 py-2 text-uppercase text-secondary fw-semibold fs-7">SN</th>
                                    <th class="px-3 py-2 text-uppercase text-secondary fw-semibold fs-7">Student Name</th>
                                    <th class="px-3 py-2 text-uppercase text-secondary fw-semibold fs-7">Level</th>
                                    <th class="px-3 py-2 text-uppercase text-secondary fw-semibold fs-7">Amount Paid</th>
                                    <th class="px-3 py-2 text-uppercase text-secondary fw-semibold fs-7">Months</th>
                                    <th class="px-3 py-2 text-uppercase text-secondary fw-semibold fs-7">Payment Date</th>
                                    <th class="px-3 py-2 text-uppercase text-secondary fw-semibold fs-7">Covered Period</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0">
                                @forelse($students as $payment)
                                <tr>
                                    <td class="px-3 py-2 text-center">{{ $loop->iteration }}</td>
                                    <td class="px-3 py-2 fw-medium">
                                        {{ $payment['student_name'] }} <br>
                                        <small class="text-muted">{{ $payment['email'] }}, {{ $payment['phone_number'] }}</small>
                                    </td>
                                    <td class="px-3 py-2 fw-medium">{{ $payment['student_level'] }}</td>
                                    <td class="px-3 py-2 text-end">₦{{ number_format($payment['total_amount'], 2) }}</td>
                                    <td class="px-3 py-2 text-center">
                                        <span class="badge bg-secondary rounded-pill">
                                            {{ $payment['number_of_months'] }} month{{ $payment['number_of_months'] > 1 ? 's' : '' }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2">
                                        <div class="d-flex align-items-center">
                                            <i class="far fa-calendar-alt text-primary me-2"></i>
                                            {{ Carbon\Carbon::parse($payment['payment_date'])->format('d M Y') }}
                                        </div>
                                    </td>
                                    <td class="px-3 py-2">
                                        <div class="d-flex flex-wrap gap-1">
                                            @if($payment['number_of_months'] > 0)
                                                @foreach($payment['months_list'] as $month)
                                                    <span class="badge bg-primary text-white rounded-pill">
                                                        {{ $month['name'] }} {{ $month['year'] }}
                                                    </span>
                                                @endforeach
                                            @else
                                                <span class="badge bg-danger rounded-pill">No months paid</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="fas fa-file-invoice-dollar text-secondary mb-3" style="font-size: 3rem;"></i>
                                            <h5 class="text-secondary">No payments found for {{ $year }}</h5>
                                            <p class="text-muted">Try selecting a different year</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Custom Styles & Enhancements */
    .card {
        border-radius: 0.75rem;
        overflow: hidden;
    }

    .bg-gradient-primary {
        background: linear-gradient(145deg, #ebedf1 0%, #19191a 100%);
    }

    .fs-7 {
        font-size: 0.8rem;
    }

    /* Badge enhancements */
    .badge {
        font-weight: 500;
        padding: 0.35em 0.7em;
    }

    .badge.rounded-pill {
        border-radius: 50rem;
    }

    /* Table enhancements */
    .table {
        border-collapse: separate;
        border-spacing: 0;
    }

    .table th {
        font-size: 0.8rem;
        letter-spacing: 0.5px;
    }

    .table td {
        vertical-align: middle;
    }

    .table tbody tr {
        transition: all 0.2s ease;
    }

    .table tbody tr:hover {
        background-color: rgba(78, 115, 223, 0.05);
    }

    /* Enhanced responsive styles */
    @media (max-width: 992px) {
        .card-header {
            padding-left: 1.25rem;
            padding-right: 1.25rem;
        }
    }

    @media (max-width: 768px) {
        .badge {
            font-size: 0.7rem;
        }

        .card-header h5 {
            font-size: 1.1rem;
        }

        .table th, .table td {
            padding: 0.5rem 0.75rem;
        }
    }

    @media (max-width: 576px) {
        .table th, .table td {
            padding: 0.4rem 0.5rem;
            font-size: 0.8rem;
        }

        .badge {
            font-size: 0.65rem;
            padding: 0.25em 0.5em;
        }
    }
    /* Add this to your existing <style> section */

/* Search input styling */
#payment-search:focus {
    box-shadow: none;
    border-color: #d1d3e2;
}

.input-group-text {
    background-color: transparent;
}

/* Animation for search results */
tbody tr {
    transition: opacity 0.3s ease, transform 0.3s ease;
}

tr.no-results-row {
    animation: fadeIn 0.5s;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

/* Responsive styles for search */
@media (max-width: 768px) {
    .input-group-sm {
        width: 100%;
        margin-bottom: 0.5rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add search input to the header section
    const headerActions = document.querySelector('.d-flex.flex-wrap.gap-2.align-items-center');

    const searchContainer = document.createElement('div');
    searchContainer.className = 'input-group input-group-sm me-2';
    searchContainer.innerHTML = `
        <span class="input-group-text bg-white border-end-0">
            <i class="fas fa-search text-primary"></i>
        </span>
        <input type="text" id="payment-search" class="form-control form-control-sm border-start-0 ps-0"
               placeholder="Search payments...">
    `;

    headerActions.prepend(searchContainer);

    // Add search functionality
    const searchInput = document.getElementById('payment-search');
    const table = document.getElementById('payment-table');
    const rows = table.querySelectorAll('tbody tr');

    searchInput.addEventListener('keyup', function(e) {
        const searchText = e.target.value.toLowerCase();
        let matchCount = 0;

        rows.forEach(row => {
            const rowText = row.textContent.toLowerCase();
            if (rowText.includes(searchText)) {
                row.style.display = '';
                matchCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Show no results message if needed
        let noResultsRow = table.querySelector('.no-results-row');

        if (matchCount === 0 && searchText !== '') {
            if (!noResultsRow) {
                noResultsRow = document.createElement('tr');
                noResultsRow.className = 'no-results-row';
                noResultsRow.innerHTML = `
                    <td colspan="7" class="text-center py-4">
                        <div class="d-flex flex-column align-items-center">
                            <i class="fas fa-search text-secondary mb-3" style="font-size: 2rem;"></i>
                            <h6 class="text-secondary">No matching records found</h6>
                            <p class="text-muted small">Try adjusting your search criteria</p>
                        </div>
                    </td>
                `;
                table.querySelector('tbody').appendChild(noResultsRow);
            }
        } else if (noResultsRow) {
            noResultsRow.remove();
        }
    });

    searchInput.addEventListener('input', function(e) {
        if (e.target.value) {
            if (!e.target.parentNode.querySelector('.clear-search')) {
                const clearButton = document.createElement('span');
                clearButton.className = 'position-absolute end-0 top-0 bottom-0 d-flex align-items-center pe-2 clear-search';
                clearButton.innerHTML = '<i class="fas fa-times text-muted" style="cursor: pointer;"></i>';
                clearButton.style.zIndex = '5';

                clearButton.addEventListener('click', function() {
                    searchInput.value = '';
                    searchInput.dispatchEvent(new Event('keyup'));
                    this.remove();
                });

                e.target.parentNode.style.position = 'relative';
                e.target.parentNode.appendChild(clearButton);
            }
        } else {
            const clearButton = e.target.parentNode.querySelector('.clear-search');
            if (clearButton) clearButton.remove();
        }
    });
});
</script>
@endsection
