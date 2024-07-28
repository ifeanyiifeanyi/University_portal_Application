<!-- resources/views/admin/courses/modal.blade.php -->
<div class="modal fade" id="facultyModal" tabindex="-1" role="dialog" aria-labelledby="courseModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="facultyform">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title" id="facultyModalLabel">Add/Edit Facutly</h5>
                    <button type="button"  class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="code">Faculty Code</label>
                        <input type="text" class="form-control" id="code" name="code" >
                        <span class="text-danger" id="codeError"></span>
                    </div>
                    <div class="form-group mb-3">
                        <label for="name">Faculty Name</label>
                        <input type="text" class="form-control" id="name" name="name" >
                        <span class="text-danger" id="nameError"></span>
                    </div>
                    <div class="form-group mb-3">
                        <label for="description">Course Description</label>
                        <textarea class="form-control" id="description" name="description" ></textarea>
                        <span class="text-danger" id="descriptionError"></span>
                    </div>
                    <input type="hidden" id="faculty_id" name="faculty_id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Course</button>
                </div>
            </form>
        </div>
    </div>
</div>
