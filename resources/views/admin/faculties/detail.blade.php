<div class="modal fade" id="courseView" tabindex="-1" role="dialog" aria-labelledby="courseModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="facultyModalLabel"><i class="fas fa-tasks"></i> Faculty Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="card-body">
                    <ul>
                        <li><strong>Code:</strong> <span id="modal_code"></span></li>
                        <li><strong>Title:</strong> <span id="modal_name"></span></li>
                        <hr>
                        <li><strong>Description:</strong> <span id="modal_description"></span></li>
                        <hr>
                        <li>
                            <strong>Departments:</strong>
                            <ul id="modal_departments"></ul>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times"></i> Close</button>

            </div>
        </div>
    </div>
</div>
