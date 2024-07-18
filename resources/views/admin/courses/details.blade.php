<!-- resources/views/admin/courses/modal.blade.php -->
<div class="modal fade" id="courseView" tabindex="-1" role="dialog" aria-labelledby="courseModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="courseModalLabel">Course Details</h5>
                <button type="button" class="close btn btn-sm btn-dark" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <ul>
                        <li><strong>Code:</strong> <span id="modal_code"></span></li>
                        <li><strong>Title:</strong> <span id="modal_title"></span></li>
                        <li><strong>Credit Hours:</strong> <span id="modal_credit_hours"></span></li>
                        <hr>
                        <li><strong>Description:</strong> <span id="modal_description"></span></li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
