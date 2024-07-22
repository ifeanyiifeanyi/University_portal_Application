<!-- resources/views/admin/courses/modal.blade.php -->
<div class="modal fade" id="courseModal" tabindex="-1" role="dialog" aria-labelledby="courseModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="courseForm">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title" id="courseModalLabel">Add/Edit Course</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="code">Course Code</label>
                        <input type="text" class="form-control" id="code" name="code" required>
                        <span class="text-danger" id="codeError"></span>
                    </div>
                    <div class="form-group mb-3">
                        <label for="title">Course Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                        <span class="text-danger" id="titleError"></span>
                    </div>
                    <div class="form-group mb-3">
                        <label for="description">Course Description</label>
                        <textarea class="form-control" id="description" name="description" required></textarea>
                        <span class="text-danger" id="descriptionError"></span>
                    </div>
                    <div class="form-group mb-3">
                        <label for="credit_hours">Credit Hours</label>
                        <input type="number" class="form-control" id="credit_hours" name="credit_hours" required>
                        <span class="text-danger" id="creditHoursError"></span>
                    </div>
                    <input type="hidden" id="course_id" name="course_id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Course</button>
                </div>
            </form>
        </div>
    </div>
</div>
