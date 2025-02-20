<div class="modal fade" id="editPlanModal{{ $plan->id }}" tabindex="-1"
    aria-labelledby="editPlanModalLabel{{ $plan->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.recurring-payments.update', $plan) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editPlanModalLabel{{ $plan->id }}">Edit Payment Plan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Error Display --}}
                    <div id="editFormErrors{{ $plan->id }}" class="alert alert-danger d-none"></div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name{{ $plan->id }}">Plan Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name{{ $plan->id }}" name="name" value="{{ old('name', $plan->name) }}"
                                    required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="amount{{ $plan->id }}">Amount (₦)</label>
                                <input type="number" step="0.01"
                                    class="form-control @error('amount') is-invalid @enderror"
                                    id="amount{{ $plan->id }}" name="amount"
                                    value="{{ old('amount', $plan->amount) }}" required>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group mt-3">
                        <label for="description{{ $plan->id }}">Description</label>
                        <textarea required class="form-control @error('description') is-invalid @enderror" id="description{{ $plan->id }}" name="description"
                            rows="3">{{ old('description', $plan->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-check mt-3">
                        <input type="checkbox" class="form-check-input" id="is_active{{ $plan->id }}"
                            name="is_active" value="1" {{ old('is_active', $plan->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active{{ $plan->id }}">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Plan</button>
                </div>
            </form>
        </div>
    </div>
</div>
