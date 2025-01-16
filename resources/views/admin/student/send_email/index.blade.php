@extends('admin.layouts.admin')

@section('title', 'Send Email to ' . $student->user->fullName())

@section('admin')
<div class="container-fluid">
    <div class="card shadow">
        <div class="card-body">
            <h5 class="card-title">Send Email to {{ $student->user->fullName() }}</h5>
            <form action="{{ route('admin.student.email.send-single', $student) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label">To</label>
                    <input type="text" class="form-control" value="{{ $student->user->email }}" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Subject</label>
                    <input type="text" name="subject" class="form-control @error('subject') is-invalid @enderror" value="{{ old('subject') }}">
                    @error('subject')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Message</label>
                    <textarea name="message" class="form-control @error('message') is-invalid @enderror" id="editor" rows="5"></textarea>
                    @error('message')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Attachments</label>
                    <input type="file" name="attachments[]" class="form-control @error('attachments') is-invalid @enderror" multiple>
                    @error('attachments')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary"> <i class="fas fa-paper-plane"></i> Send Email</button>
            </form>
        </div>
    </div>
</div>
@endsection
