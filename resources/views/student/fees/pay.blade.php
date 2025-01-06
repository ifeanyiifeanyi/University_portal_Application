@extends('student.layouts.student')

@section('title', 'Student Dashoard (pay fees)')
@section('student')
<div class="container-xxl mt-3">
    <div class="row">
        <div class="col-xl-12">
            
      
            <div class="card">
              <div class="card-header">
                <h2>Select Session of school fees</h2>
              </div>
              <div class="card-body pt-3">
                @include('messages')
                <form action="{{route('student.view.fees.process')}}" method="POST">
                  @csrf
                  <input type="hidden" name="department_id" id="department_id" value="{{$student->department_id}}">
                  <input type="hidden" name="student_id" value="{{$student->id}}">
                  <input type="hidden" name="user_id" value="{{$student->user_id}}">

                   <!-- Automatically pass the school fees payment type -->
    {{-- <input type="hidden" name="payment_type_id" value="{{ $paymentType->id }}"> --}}
    <div class="row mb-3">
      <label for="fullName" class="col-md-4 col-lg-3 col-form-label">Select Fees types</label>
      <div class="col-md-8 col-lg-9">
          <select name="payment_type_id" id="" class="form-control">
              <option value="" disabled selected>Select Fee for your department</option>
              @foreach ($paymentTypes as $paymentType)
              @if ($paymentType->paymentType) {{-- Check if paymentType exists --}}
              <option value="{{ $paymentType->paymentType->id }}">
                  {{ $paymentType->paymentType->name }}
              </option>
          @endif
          @endforeach
             
          </select>
          @if ($errors->has('payment_type_id'))
<span class="text-danger">{{$errors->first('payment_type_id')}}</span>
@endif
      </div>
    </div>

    {{-- <input type="hidden" name="amount" value="{{ $paymentType->amount }}"> --}}
                <div class="row mb-3">
                    <label for="fullName" class="col-md-4 col-lg-3 col-form-label">Academic session</label>
                    <div class="col-md-8 col-lg-9">
                        <select name="academic_session_id" id="" class="form-control">
                            <option value="" disabled selected>Select session</option>
                            @foreach ($academicsessions as $academicsession)
                            <option value="{{ $academicsession->id }}" {{ $academicsession->is_current ? 'selected' : '' }}>
                                {{ $academicsession->name }} {{ $academicsession->is_current ? '(Current Session)' : '' }}
                            </option>
                        @endforeach
                           
                        </select>
                        @if ($errors->has('academic_session_id'))
    <span class="text-danger">{{$errors->first('academic_session_id')}}</span>
    @endif
                    </div>
                  </div>
                  <div class="row mb-3">
                    <label for="fullName" class="col-md-4 col-lg-3 col-form-label">Select level</label>
                    <div class="col-md-8 col-lg-9">
                        <select id="level" name="level" class="form-control">
                            <option value="" disabled selected>Select level</option>
                            @foreach ($levels as $level)
                            <option value="{{ $currentDepartment->getLevelNumber($level) }}"
                                {{ old('level', $student->current_level) == $currentDepartment->getLevelNumber($level) ? 'selected' : '' }}>
                                {{ $level }}
                            </option>
                        @endforeach
                           
                        </select>
                        @if ($errors->has('level'))
    <span class="text-danger">{{$errors->first('level')}}</span>
    @endif
                    </div>
                  </div>
                  <div class="row mb-3">
                    <label for="fullName" class="col-md-4 col-lg-3 col-form-label">Select semester</label>
                    <div class="col-md-8 col-lg-9">
                        <select name="semester_id" id="" class="form-control">
                            <option value="" disabled selected>Select semester</option>
                            @foreach ($semesters as $semester)
                                        <option value="{{ $semester->id }}" {{ $semester->is_current ? 'selected' : '' }}>
                                            {{ $semester->name }} {{ $semester->is_current ? '(Current Semester)' : '' }}
                                        </option>
                                    @endforeach
                           
                        </select>
                        @if ($errors->has('semester_id'))
    <span class="text-danger">{{$errors->first('semester_id')}}</span>
    @endif
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="fullName" class="col-md-4 col-lg-3 col-form-label">Select Payment method</label>
                    <div class="col-md-8 col-lg-9">
                      <select name="payment_method_id" id="payment_method" class="form-control">
                        @foreach($paymentMethods as $method)
                            <option value="{{ $method->id }}">{{ $method->name }}</option>
                        @endforeach
                    </select>
                        @if ($errors->has('payment_method_id'))
    <span class="text-danger">{{$errors->first('payment_method_id')}}</span>
    @endif
                    </div>
                  </div>




                  <!-- Add this before the submit button -->
<div id="installment-options" class="row mb-3" style="display: none;">
  <label class="col-md-4 col-lg-3 col-form-label">Installment Option</label>
  <div class="col-md-8 col-lg-9">
      <div class="form-check">
          <input class="form-check-input" type="checkbox" name="is_installment" value="1" id="is_installment">
          <label class="form-check-label" for="is_installment">
              Pay in Installments
          </label>
      </div>
      <div id="installment-info" class="alert alert-info mt-2" style="display: none;">
          <h6 class="alert-heading mb-2">Installment Payment Details</h6>
          <div id="installment-breakdown"></div>
      </div>
  </div>
</div>

<div class="row mb-3">
  <label class="col-md-4 col-lg-3 col-form-label">Amount to Pay</label>
  <div class="col-md-8 col-lg-9">
      <input type="number" name="amount" id="amount" class="form-control" readonly>
      <div id="late-fee-alert" class="alert alert-warning mt-2" style="display: none;">
          <div class="d-flex align-items-center">
              <i class="fas fa-exclamation-triangle me-2"></i>
              <div>
                  <h6 class="alert-heading mb-1">Late Payment Fee Applied</h6>
                  <div id="late-fee-message"></div>
                  <div id="payment-breakdown" class="mt-2">
                      <small class="d-block">Base Amount: <span id="base-amount"></span></small>
                      <small class="d-block">Late Fee: <span id="late-fee"></span></small>
                      <small class="d-block">Total Amount: <span id="total-amount"></span></small>
                  </div>
                  <small class="text-muted d-block mt-1" id="due-date-message"></small>
              </div>
          </div>
      </div>
  </div>
</div>

                 
    
                  <div>
                    <button class="btn w-50 text-white btn-success" style="">Submit</button>
                  </div>
                </form>
              </div>
            </div>
    
          </div>
    </div>
    </div>

    @section('javascript')

<script>
    // document.addEventListener('DOMContentLoaded', function() {
    //     const departmentSelect = document.getElementById('department_id');
    //     const levelSelect = document.getElementById('level');
  
    //     function updateLevels() {
    //         const departmentId = departmentSelect.value;
    //         fetch(`/student/fees/departments/${departmentId}/levels`)
    //             .then(response => response.json())
    //             .then(levels => {
    //               console.log(levels);
    //                 levelSelect.innerHTML = '';
    //                 levels.forEach(level => {
    //                     const option = document.createElement('option');
    //                     option.value = level;
    //                     option.textContent = level;
    //                     levelSelect.appendChild(option);
    //                 });
    //             });
    //     }
  
    //     departmentSelect.addEventListener('change', updateLevels);
    //     updateLevels(); // Initial population
    // });



    $(document).ready(function() {
    // Handle payment type change
    $('select[name="payment_type_id"]').change(function() {
        var paymentTypeId = $(this).val();
        if (paymentTypeId) {
            $.ajax({
                url: '{{ route('student.fees.getpaymentdetails') }}',  // You'll need to create this route
                type: 'GET',
                data: {
                    payment_type_id: paymentTypeId,
                    department_id: $('#department_id').val()
                },
                success: function(data) {
                    // Handle late fee display
                    console.log(data);
                    if (data.late_fee > 0) {
                        const baseAmount = data.amount - data.late_fee;
                        const formattedDueDate = new Date(data.due_date).toLocaleDateString();
                        const formatter = new Intl.NumberFormat('en-NG', {
                            style: 'currency',
                            currency: 'NGN',
                            minimumFractionDigits: 2
                        });

                        $('#base-amount').text(formatter.format(baseAmount));
                        $('#late-fee').text(formatter.format(data.late_fee));
                        $('#total-amount').text(formatter.format(data.amount));
                        $('#late-fee-message').html(
                            'A late payment fee has been added to your payment due to payment after the due date.'
                        );
                        $('#due-date-message').text(`Original due date was ${formattedDueDate}`);
                        $('#late-fee-alert').slideDown();
                    } else {
                        $('#late-fee-alert').slideUp();
                    }

                    // Set the amount
                    $('#amount').val(data.amount);

                    // Handle installment options
                    if (data.supports_installments) {
                        $('#installment-options').slideDown();
                        
                        if (data.installment_config) {
                            const config = data.installment_config;
                            const totalAmount = data.amount;
                            const firstPaymentAmount = (totalAmount * config.minimum_first_payment_percentage) / 100;
                            const remainingAmount = totalAmount - firstPaymentAmount;
                            const regularInstallmentAmount = remainingAmount / (config.number_of_installments - 1);

                            let breakdownHtml = `
                                <p><strong>First Payment:</strong> ₦${firstPaymentAmount.toLocaleString('en-NG', {minimumFractionDigits: 2})}</p>
                                <p><strong>Remaining ${config.number_of_installments - 1} Installments:</strong> ₦${regularInstallmentAmount.toLocaleString('en-NG', {minimumFractionDigits: 2})} each</p>
                                <p><strong>Payment Interval:</strong> ${config.interval_days} days</p>
                            `;
                            
                            $('#installment-breakdown').html(breakdownHtml);
                            $('#installment-info').show();
                        }
                    } else {
                        $('#installment-options').slideUp();
                        $('#is_installment').prop('checked', false);
                        $('#installment-info').hide();
                    }

                    // Update amount display based on installment selection
                    updateAmountDisplay(data.amount, data.installment_config);
                }
            });
        }
    });

    // Update amount when installment checkbox changes
    $('#is_installment').change(function() {
        const paymentTypeId = $('select[name="payment_type_id"]').val();
        if (paymentTypeId) {
            $.ajax({
                url: '{{ route('student.fees.getpaymentdetails') }}',
                type: 'GET',
                data: {
                    payment_type_id: paymentTypeId,
                    department_id: $('#department_id').val()
                },
                success: function(data) {
                    updateAmountDisplay(data.amount, data.installment_config);
                }
            });
        }
    });

    // Function to update amount display
    function updateAmountDisplay(totalAmount, installmentConfig) {
        const isInstallment = $('#is_installment').is(':checked');
        if (isInstallment && installmentConfig) {
            const firstPaymentAmount = (totalAmount * installmentConfig.minimum_first_payment_percentage) / 100;
            $('#amount').val(firstPaymentAmount);
        } else {
            $('#amount').val(totalAmount);
        }
    }
});
  </script>
  @endsection
@endsection