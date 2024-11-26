<!-- resources/views/admin/courses/details.blade.php -->
<div class="modal fade" id="courseView" tabindex="-1" role="dialog" aria-labelledby="courseViewLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="courseModalLabel">View Course Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Course Code:</strong>
                        <p id="modal_code"></p>
                    </div>
                    <div class="col-md-6">
                        <strong>Course Title:</strong>
                        <p id="modal_title"></p>
                    </div>
                    <div class="col-md-6">
                        <strong>Program:</strong>
                        <p id="modal_program"></p>
                    </div>
                    <div class="col-md-6">
                        <strong>Credit Hours:</strong>
                        <p id="modal_credit_hours"></p>
                    </div>
                    <div class="col-md-12">
                        <strong>Description:</strong>
                        <p id="modal_description"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
