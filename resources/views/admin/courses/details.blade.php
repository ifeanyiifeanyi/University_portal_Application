<!-- resources/views/admin/courses/details.blade.php -->
<div class="modal fade" id="courseView" tabindex="-1" role="dialog" aria-labelledby="courseViewLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="courseViewLabel">Course Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-bordered">
                            <tr>
                                <th>Course Code:</th>
                                <td id="modal_code"></td>
                            </tr>
                            <tr>
                                <th>Course Title:</th>
                                <td id="modal_title"></td>
                            </tr>
                            <tr>
                                <th>Program:</th>
                                <td id="modal_program"></td>
                            </tr>
                            <tr>
                                <th>Course Type:</th>
                                <td>
                                    <span id="modal_course_type_badge" class="badge"></span>
                                </td>
                            </tr>
                            <tr>
                                <th>Credit Hours:</th>
                                <td id="modal_credit_hours"></td>
                            </tr>
                            <tr>
                                <th>Description:</th>
                                <td id="modal_description"></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
