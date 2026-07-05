<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Doctor Profile Management</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo site_url('admin'); ?>">Home</a></li>
                        <li class="breadcrumb-item active">Doctors</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div id="doctorAlert"></div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Doctor Profiles</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" id="addDoctorButton">
                            <i class="fas fa-plus mr-1"></i> Add Doctor Profile
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 70px;">ID</th>
                                    <th style="width: 180px;">Doctor Name</th>
                                    <th style="width: 180px;">Email</th>
                                    <th style="width: 150px;">Department</th>
                                    <th style="width: 150px;">Specialization</th>
                                    <th style="width: 100px;">Fee</th>
                                    <th style="width: 100px;">Status</th>
                                    <th style="width: 180px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="doctorsTableBody">
                                <tr>
                                    <td colspan="8" class="text-center">Loading doctors...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Add Doctor Modal -->
<div class="modal fade" id="addDoctorModal" tabindex="-1" role="dialog" aria-labelledby="addDoctorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="addDoctorForm" autocomplete="off">
                <div class="modal-header">
                    <h5 class="modal-title" id="addDoctorModalLabel">Add Doctor Profile</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <input
                        type="hidden"
                        name="<?php echo $this->security->get_csrf_token_name(); ?>"
                        value="<?php echo $this->security->get_csrf_hash(); ?>"
                    >

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="add_user_id">Doctor User</label>
                                <select class="form-control" id="add_user_id" name="user_id">
                                    <option value="">Select Doctor User</option>
                                </select>
                                <small class="text-danger validation-message" data-field="user_id"></small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="add_department_id">Department</label>
                                <select class="form-control" id="add_department_id" name="department_id">
                                    <option value="">Select Department</option>
                                </select>
                                <small class="text-danger validation-message" data-field="department_id"></small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="add_specialization">Specialization</label>
                                <input type="text" class="form-control" id="add_specialization" name="specialization" maxlength="150">
                                <small class="text-danger validation-message" data-field="specialization"></small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="add_qualification">Qualification</label>
                                <input type="text" class="form-control" id="add_qualification" name="qualification" maxlength="200">
                                <small class="text-danger validation-message" data-field="qualification"></small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="add_consultation_fee">Consultation Fee</label>
                                <input type="number" step="0.01" class="form-control" id="add_consultation_fee" name="consultation_fee" value="0.00">
                                <small class="text-danger validation-message" data-field="consultation_fee"></small>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="add_status">Status</label>
                                <select class="form-control" id="add_status" name="status">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                                <small class="text-danger validation-message" data-field="status"></small>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Available Days</label>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" name="available_days[]" value="Monday" id="add_mon">
                                            <label class="form-check-label" for="add_mon">Mon</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" name="available_days[]" value="Tuesday" id="add_tue">
                                            <label class="form-check-label" for="add_tue">Tue</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" name="available_days[]" value="Wednesday" id="add_wed">
                                            <label class="form-check-label" for="add_wed">Wed</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" name="available_days[]" value="Thursday" id="add_thu">
                                            <label class="form-check-label" for="add_thu">Thu</label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" name="available_days[]" value="Friday" id="add_fri">
                                            <label class="form-check-label" for="add_fri">Fri</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" name="available_days[]" value="Saturday" id="add_sat">
                                            <label class="form-check-label" for="add_sat">Sat</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" name="available_days[]" value="Sunday" id="add_sun">
                                            <label class="form-check-label" for="add_sun">Sun</label>
                                        </div>
                                    </div>
                                </div>
                                <small class="text-danger validation-message" data-field="available_days"></small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveDoctorButton">
                        <i class="fas fa-save mr-1"></i> Save Doctor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Doctor Modal -->
<div class="modal fade" id="editDoctorModal" tabindex="-1" role="dialog" aria-labelledby="editDoctorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="editDoctorForm" autocomplete="off">
                <div class="modal-header">
                    <h5 class="modal-title" id="editDoctorModalLabel">Edit Doctor Profile</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <input
                        type="hidden"
                        name="<?php echo $this->security->get_csrf_token_name(); ?>"
                        value="<?php echo $this->security->get_csrf_hash(); ?>"
                    >
                    <input type="hidden" id="edit_doctor_id" name="doctor_id">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_department_id">Department</label>
                                <select class="form-control" id="edit_department_id" name="department_id">
                                    <option value="">Select Department</option>
                                </select>
                                <small class="text-danger validation-message" data-field="department_id"></small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_specialization">Specialization</label>
                                <input type="text" class="form-control" id="edit_specialization" name="specialization" maxlength="150">
                                <small class="text-danger validation-message" data-field="specialization"></small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_qualification">Qualification</label>
                                <input type="text" class="form-control" id="edit_qualification" name="qualification" maxlength="200">
                                <small class="text-danger validation-message" data-field="qualification"></small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_consultation_fee">Consultation Fee</label>
                                <input type="number" step="0.01" class="form-control" id="edit_consultation_fee" name="consultation_fee" value="0.00">
                                <small class="text-danger validation-message" data-field="consultation_fee"></small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_status">Status</label>
                                <select class="form-control" id="edit_status" name="status">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                                <small class="text-danger validation-message" data-field="status"></small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Available Days</label>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" name="available_days[]" value="Monday" id="edit_mon">
                                            <label class="form-check-label" for="edit_mon">Mon</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" name="available_days[]" value="Tuesday" id="edit_tue">
                                            <label class="form-check-label" for="edit_tue">Tue</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" name="available_days[]" value="Wednesday" id="edit_wed">
                                            <label class="form-check-label" for="edit_wed">Wed</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" name="available_days[]" value="Thursday" id="edit_thu">
                                            <label class="form-check-label" for="edit_thu">Thu</label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" name="available_days[]" value="Friday" id="edit_fri">
                                            <label class="form-check-label" for="edit_fri">Fri</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" name="available_days[]" value="Saturday" id="edit_sat">
                                            <label class="form-check-label" for="edit_sat">Sat</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" name="available_days[]" value="Sunday" id="edit_sun">
                                            <label class="form-check-label" for="edit_sun">Sun</label>
                                        </div>
                                    </div>
                                </div>
                                <small class="text-danger validation-message" data-field="available_days"></small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="updateDoctorButton">
                        <i class="fas fa-edit mr-1"></i> Update Doctor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Include Doctor Module Script -->
<script src="<?php echo base_url('assets/js/modules/doctors.js'); ?>"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize the Doctors module
    Doctors.init({
        csrfName: '<?php echo $this->security->get_csrf_token_name(); ?>',
        csrfHash: '<?php echo $this->security->get_csrf_hash(); ?>',
        listUrl: '<?php echo site_url('admin/admin_doctors/get_doctors_ajax'); ?>',
        storeUrl: '<?php echo site_url('admin/admin_doctors/store'); ?>',
        editBaseUrl: '<?php echo site_url('admin/admin_doctors/edit'); ?>',
        updateBaseUrl: '<?php echo site_url('admin/admin_doctors/update'); ?>',
        changeStatusBaseUrl: '<?php echo site_url('admin/admin_doctors/change_status'); ?>',
        setupOptionsUrl: '<?php echo site_url('admin/admin_doctors/get_setup_options'); ?>'
    });
});
</script>