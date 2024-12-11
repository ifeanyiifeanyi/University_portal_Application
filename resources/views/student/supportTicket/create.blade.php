@extends('student.layouts.student')

@section('title', 'Student Dashboard')
@section('student')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 my-2">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Support Tickets</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Create New Ticket</div>

                    <div class="card-body">
                        <form action="{{ route('student.tickets.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Subject</label>
                                <input type="text" name="subject"
                                    class="form-control @error('subject') is-invalid @enderror" required
                                    value="{{ old('subject') }}">
                                @error('subject')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Category</label>
                                        <select name="category" class="form-control @error('category') is-invalid @enderror"
                                            required>
                                            <option value="" disabled selected>Select Category</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category }}">{{ $category }}</option>
                                            @endforeach
                                        </select>
                                        @error('category')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Priority</label>
                                    <select name="priority" class="form-control @error('priority') is-invalid @enderror"
                                        required>
                                        <option value="" disabled selected>Select Priority</option>
                                        @foreach ($priorities as $priority)
                                            <option value="{{ $priority }}">{{ $priority }}</option>
                                        @endforeach
                                    </select>
                                    @error('priority')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>


                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3" required>{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div id="questions-container">
                                <div class="mb-3">
                                    <label class="form-label">Questions</label>
                                    <div class="question-group">
                                        <input type="text" name="questions[]"
                                            class="form-control mb-2 @error('questions.*') is-invalid @enderror" required
                                            value="{{ old('questions.*') }}">
                                        @error('questions.*')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <button type="button" class="btn btn-secondary mb-3" onclick="addQuestion()">
                                Add Another Question
                            </button>

                            <div class="mb-3">
                                <label class="form-label">Attachments</label>
                                <input type="file" name="attachments[]"
                                    class="form-control @error('attachments.*') is-invalid @enderror" multiple
                                    value="{{ old('attachments.*') }}">
                                @error('attachments.*')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary">Submit Ticket</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- container-fluid -->

    <script>
        function addQuestion() {
            const container = document.getElementById('questions-container');
            const questionGroup = document.createElement('div');
            questionGroup.className = 'question-group mb-2';
            questionGroup.innerHTML = `
                <div class="input-group">
                    <input type="text" name="questions[]" class="form-control" required value="{{ old('questions.*') }}">
                    <button type="button" class="btn btn-danger" onclick="removeQuestion(this)">Remove</button>
                </div>
            `;
            container.appendChild(questionGroup);
        }

        function removeQuestion(button) {
            button.closest('.question-group').remove();
        }
    </script>
@endsection
